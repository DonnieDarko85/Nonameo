<?php /*HELP: */
$disoccupato = 0;/*Il pg e' affiliato ad una gilda*/
$lavoro = -1;
$jobsn = 0;
$ultimolavoro = strftime("%Y-%m-%d");
$query = "SELECT clgpersonaggioruolo.id_ruolo, clgpersonaggioruolo.scadenza, ruolo.gilda FROM clgpersonaggioruolo LEFT JOIN ruolo ON clgpersonaggioruolo.id_ruolo = ruolo.id_ruolo WHERE clgpersonaggioruolo.personaggio = '".$_SESSION['login']."' ORDER BY ruolo.gilda";
$result = gdrcd_query($query, 'result');

while($jobs = gdrcd_query($result, 'fetch')) {
    $jobsn++;

    if($jobs['gilda'] == -1) {/*Il pg ha un lavoro indipendente*/
        $disoccupato = -1;
        $lavoro = $jobs['id_ruolo'];
        $ultimolavoro = $jobs['scadenza'];
    }
}
gdrcd_query($result, 'free');
?>
<div class="pagina_servizi_lavoro">
    <!-- Titolo della pagina -->
    <div class="page_title">
        <h2><?php echo gdrcd_filter('out', $MESSAGE['interface']['job']['page_name']); ?></h2>
    </div>

    <!-- Box principale -->
    <div class="page_body">
        <?php
        if(isset($_POST['op']) === false) {
            $query = "SELECT nome_ruolo, immagine, stipendio, id_ruolo FROM ruolo WHERE gilda=-1 ORDER BY nome_ruolo";
            $result = gdrcd_query($query, 'result'); ?>
            
            <style>
                .job-grid {
    				display: flex;
    				flex-wrap: wrap;
    				gap: 1rem;
    				justify-content: flex-start;
    				width: 800px;
    				margin: auto;
    				height: 500px;
    				overflow: auto;
    				background: #a19c7b;
    				padding: 10px;
    				border: 5px dashed black;
                			}
                .job-card {
                        flex: 1 1 calc(25% - 1rem);
   					 /* border: 1px solid #ccc; */
    					padding: 1rem 0;
    					box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
    					/* border-radius: 5px; */
   						text-align: center;
    					background: #000;
                			}
                .job-card img {
                    max-width: 100%;
                    height: auto;
                    margin-bottom: 0.5rem;
                }
                .job-card .job-name {
                    font-weight: 400;
    				margin-bottom: 0.5rem;
   	 				color: #a19c7b;
    				letter-spacing: 1px;
                    font-family: 'Special Elite';
                }
                .job-card .job-pay {
                   margin-bottom: 0.5rem;
    				font-size: 10px;
    				color: #999;
    				font-weight: 400;
    				letter-spacing: 2px;
    				position: relative;
    				top: -8px;
                    font-family: 'Special Elite';
                }
                .job-card form {
                    margin: 0;
                }
                .job-card input[type='submit'] {
                width: 130px;
                background: black;
                color: #a19c7b;
                }
            </style>

            <div class="elenco_record_gioco">
                <div class="job-grid">
                    <?php while($row = gdrcd_query($result, 'fetch')) { ?>
                        <div class="job-card">
                            <img src="themes/<?php echo $PARAMETERS['themes']['current_theme']; ?>/imgs/guilds/<?php echo $row['immagine']; ?>" alt="<?php echo $row['nome_ruolo']; ?>" />
                            <div class="job-name"><?php echo $row['nome_ruolo']; ?></div>
                            <div class="job-pay"><?php echo $row['stipendio'].' '.$PARAMETERS['names']['currency']['plur']; ?></div>
                            <form method="post" action="main.php?page=servizi_lavoro">
                                <?php
                                if($ultimolavoro <= strftime("%Y-%m-%d")) {
                                    if($lavoro == $row['id_ruolo']) { ?>
                                        <input type="submit" value="<?php echo gdrcd_filter('out', $MESSAGE['interface']['job']['submit']['quit']); ?>" />
                                        <input type="hidden" name="op" value="resign" />
                                    <?php } else {
                                        if($jobsn < $PARAMETERS['settings']['guilds_limit']) { ?>
                                            <input type="submit" value="<?php echo gdrcd_filter('out', $MESSAGE['interface']['job']['submit']['pick']); ?>" />
                                            <input type="hidden" name="op" value="pick" />
                                        <?php } else {
                                            echo '&nbsp;';
                                        }
                                    } ?>
                                    <input type="hidden" name="nome_lavoro" value="<?php echo gdrcd_filter('out', $row['nome_ruolo']); ?>" />
                                    <input type="hidden" name="id_record" value="<?php echo $row['id_ruolo']; ?>" />
                                <?php
                                } else {
                                    if($lavoro == $row['id_ruolo']) {
                                        $ultimolavoroexp = explode("-", $ultimolavoro);
                                        echo gdrcd_filter('out', $MESSAGE['interface']['job']['extent']
                                            )." ".$ultimolavoroexp[2]."-".$ultimolavoroexp[1]."-".$ultimolavoroexp[0];
                                    } else {
                                        echo '&nbsp;';
                                    }
                                }
                                ?>
                            </form>
                        </div>
                    <?php }
                    gdrcd_query($result, 'free'); ?>
                </div>
                <div style="margin-top: 1rem;">
                    <?php echo gdrcd_filter('out', $MESSAGE['interface']['job']['disclaimer'])." ".$PARAMETERS['settings']['minimum_employment']; ?>
                </div>
            </div>
        <?php
        }

        if($_POST['op'] == 'pick') {
            if($disoccupato == -1) {
                gdrcd_query("UPDATE clgpersonaggioruolo SET id_ruolo = ".gdrcd_filter('num', $_POST['id_record']).", scadenza = DATE_ADD(NOW(), INTERVAL ".gdrcd_filter('num', $PARAMETERS['settings']['minimum_employment'])." DAY) WHERE personaggio='".$_SESSION['login']."' AND id_ruolo = ".gdrcd_filter('num', $lavoro)." LIMIT 1");
            } else {
                gdrcd_query("INSERT INTO clgpersonaggioruolo (id_ruolo, personaggio, scadenza) VALUES (".gdrcd_filter('num', $_POST['id_record']).", '".$_SESSION['login']."', DATE_ADD(NOW(), INTERVAL ".gdrcd_filter('num', $PARAMETERS['settings']['minimum_employment'])." DAY))");
            }

            echo '<div class="warning">'.gdrcd_filter('out', $MESSAGE['interface']['job']['ok_job']).'</div>';

            gdrcd_query("INSERT INTO log (nome_interessato, autore, data_evento, codice_evento ,descrizione_evento) VALUES ('".$_SESSION['login']."', '".$_SESSION['login']."', NOW(), ".NUOVOLAVORO.", '".gdrcd_filter_in($_POST['nome_lavoro'])."')");
            ?>
            <div class="link_back">
                <a href="main.php?page=servizi_lavoro"><?php echo gdrcd_filter('out', $MESSAGE['interface']['job']['back']); ?></a>
            </div>
        <?php
        }

        if($_POST['op'] == 'resign') {
            gdrcd_query("DELETE FROM clgpersonaggioruolo WHERE personaggio='".$_SESSION['login']."' AND id_ruolo = ".gdrcd_filter('num', $_POST['id_record'])." LIMIT 1");

            echo '<div class="warning">'.gdrcd_filter('out', $MESSAGE['interface']['job']['ok_quit']).'</div>';
            gdrcd_query("INSERT INTO log (nome_interessato, autore, data_evento, codice_evento ,descrizione_evento) VALUES ('".$_SESSION['login']."', '".$_SESSION['login']."', NOW(), ".DIMISSIONE.", '".gdrcd_filter('in', $_POST['nome_lavoro'])."')");
            ?>
            <div class="panels_link">
                <a href="main.php?page=servizi_lavoro"><?php echo gdrcd_filter('out', $MESSAGE['interface']['job']['back']); ?></a>
            </div>
        <?php } ?>
    </div>
</div><!-- Box principale -->
