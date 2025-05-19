<div class="pagina_scheda">
    <?php
    /* HELP: E' possibile modificare la scheda agendo su scheda.css nel tema scelto,
     * oppure sostituendo il codice che segue la voce "Scheda del personaggio"
     */
    /********* CARICAMENTO PERSONAGGIO ***********/
    //Se non e' stato specificato il nome del pg
    if(isset($_REQUEST['pg']) === false) {
        echo '<div class="error">'.gdrcd_filter('out', $MESSAGE['error']['unknown_character_sheet']).'</div>';
        exit();
    }
    $query = "SELECT personaggio.*, razza.sing_m, razza.sing_f, razza.id_razza, razza.bonus_car0, razza.bonus_car1, razza.bonus_car2, razza.bonus_car3, razza.bonus_car4, razza.bonus_car5
        FROM personaggio LEFT JOIN razza ON personaggio.id_razza=razza.id_razza
        WHERE personaggio.nome = '".gdrcd_filter('in', $_REQUEST['pg'])."'";
    $personaggi = gdrcd_query($query, 'result');
    //Se il personaggio non esiste
    if(gdrcd_query($personaggi, 'num_rows') == 0) {
        echo '<div class="error">'.gdrcd_filter('out', $MESSAGE['error']['unknown_character_sheet']).'</div>';
        exit();
    }
    $personaggio = gdrcd_query($personaggi, 'fetch');
    $bonus_oggetti = gdrcd_query("SELECT SUM(oggetto.bonus_car0) AS BO0, SUM(oggetto.bonus_car1) AS BO1, SUM(oggetto.bonus_car2) AS BO2, SUM(oggetto.bonus_car3) AS BO3, SUM(oggetto.bonus_car4) AS BO4, SUM(oggetto.bonus_car5) AS BO5
            FROM oggetto JOIN clgpersonaggiooggetto ON oggetto.id_oggetto = clgpersonaggiooggetto.id_oggetto
            WHERE clgpersonaggiooggetto.nome = '".gdrcd_filter('in', $_REQUEST['pg'])."' AND clgpersonaggiooggetto.posizione > ".ZAINO."");

    //Controllo esilio, se esiliato non visualizzo la scheda
    if($personaggio['esilio'] > strftime('%Y-%m-%d')) {
        echo '<div class="warning">'.gdrcd_filter('out', $personaggio['nome']).' '.gdrcd_filter('out', $personaggio['cognome']).' '.gdrcd_filter('out', $MESSAGE['warning']['character_exiled']).' '.gdrcd_format_date($personaggio['esilio']).' ('.$personaggio['motivo_esilio'].' - '.$personaggio['autore_esilio'].')</div>';
        if($_SESSION['permessi'] >= GAMEMASTER) { ?>
            <div class="panels_box">
                <div class="form_gioco">
                    <form action="main.php?page=scheda_modifica&pg=<?php echo gdrcd_filter('url', $_REQUEST['pg']) ?>" method="post">
                        <input type="hidden" value="<?php echo strftime('%Y'); ?>" name="year" />
                        <input type="hidden" value="<?php echo strftime('%m'); ?>" name="month" />
                        <input type="hidden" value="<?php echo strftime('%d'); ?>" name="day" />
                        <input type="hidden" value="<?php gdrcd_filter('out', $MESSAGE['interface']['sheet']['modify_form']['unexile']); ?>" name="causale" />
                        <input type="hidden" value="exile" name="op" />
                        <div class="form_label">
                            <?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['modify_form']['unexile']); ?>
                        </div>
                        <div class="form_submit">
                            <input type="submit" value="<?php echo gdrcd_filter('out', $MESSAGE['interface']['forms']['submit']); ?>" />
                        </div>
                    </form>
                </div>
            </div>
        <?php
        }
        exit();
    }

    ?>
    <div class="page_title">
        <h2><?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['page_name']); ?></h2>
    </div>
    <div class="page_body">
        <?php
        /** * Controllo e avviso che è ora di cambiare password
         * @author Blancks
         */
        if($PARAMETERS['mode']['alert_password_change'] == 'ON') {
            $six_months = 15552000;
            $ts_signup = strtotime($record['data_iscrizione']);
            $ts_lastpass = (int) strtotime($record['ultimo_cambiopass']);
            if($ts_lastpass + $six_months < time() && $record['nome'] == $_SESSION['login']) {
                $message = ($ts_signup + $six_months < time()) ? $MESSAGE['warning']['changepass'] : $MESSAGE['warning']['changepass_signup'];
                echo '<div class="warning">'.$message.'</div>';
            }
        }
        ?>
        <div class="menu_scheda"><!-- Menu scheda -->
            <?php include ('scheda/menu.inc.php'); ?>
        </div>
        <div class="page_body">
            <div class="ritratto"><!-- nome, ritratto, ultimo ingresso -->
                <div class="titolo_box">
                    <?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['box_title']['portrait']); ?>
                </div>
                <div class="ritratto_nome">
                    <span class="ritratto_nome_nome">
                        <?php echo gdrcd_filter('out', $personaggio['nome']); ?>
                    </span>
                    <span class="ritratto_nome_cognome">
                        <?php echo gdrcd_filter('out', $personaggio['cognome']); ?>
                    </span>
                </div>
                <div class="ritratto_avatar">
                    
                    <img src="<?php echo !empty(gdrcd_filter('fullurl', $personaggio['url_img']))? $personaggio['url_img'] : '../imgs/avatar_empty.png' ?>" class="ritratto_avatar_immagine" />
          
                </div>
                <div class="iscritto_da">
                    <?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['first_login']).' '.gdrcd_format_date($personaggio['data_iscrizione']); ?>
                </div>
                <?php if(gdrcd_format_date($record['ora_entrata']) != '00/00/0000') { ?>
                    <div class="ultimo_ingresso">
                        <?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['last_login']).' '.gdrcd_format_date($personaggio['ora_entrata']); ?>
                    </div>
                <?php } ?>
                <div class="ritratto_invia_messaggio"><!-- Link invia messaggio -->
                    <a href="main.php?page=messages_center&op=create&destinatario=<?=gdrcd_filter('url', $personaggio['nome']); ?>"
                       class="link_invia_messaggio">
                        <?php if(empty($PARAMETERS['names']['private_message']['image_file']) === false) { ?>
                            <img src="<?php echo $PARAMETERS['names']['private_message']['image_file']; ?>"
                                 alt="<?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['send_message_to']['send']).' '.gdrcd_filter('out', $PARAMETERS['names']['private_message']['sing']).' '.gdrcd_filter('out', $MESSAGE['interface']['sheet']['send_message_to']['to']).' '.gdrcd_filter('out', $personaggio['nome']); ?>"
                                 title="<?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['send_message_to']['send']).' '.gdrcd_filter('out', $PARAMETERS['names']['private_message']['sing']).' '.gdrcd_filter('out', $MESSAGE['interface']['sheet']['send_message_to']['to']).' '.gdrcd_filter('out', $personaggio['nome']); ?>"
                                 class="link_messaggio_forum">
                        <?php } else {
                            echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['send_message_to']['send']).' '.gdrcd_filter('out', strtolower($PARAMETERS['names']['private_message']['sing'])).' '.gdrcd_filter('out', $MESSAGE['interface']['sheet']['send_message_to']['to']).' '.gdrcd_filter('out', $personaggio['nome']);
                        } ?>
                    </a>
                </div>
            </div>
            <!-- nome, ritratto, ultimo ingresso, abiti portati -->
            <div id="contenitoreScheda">
                    <!-- Caricamento in JS -->
            </div>
        </div>
    </div><!-- Elenco abilità -->
    <?php
    /********* CHIUSURA SCHEDA **********/
    //Impedisci XSS nella musica
    $record['url_media'] = gdrcd_filter('fullurl', $record['url_media']);
    if($PARAMETERS['mode']['allow_audio'] == 'ON' && ! $_SESSION['blocca_media'] && ! empty($record['url_media'])) { ?>
        <audio autoplay>
            <source src="<?php echo $record['url_media']; ?>" type="audio/mpeg">
        </audio>
        <!--[if IE9]>
        <embed src="<?php echo $record['url_media']; ?>" autostart="true" hidden="true"/>
        <![endif]-->
    <?php } ?>
</div><!-- Pagina -->

<script>

    $(document).ready(function() {
        $('#contenitoreScheda').empty();
        $('#contenitoreScheda').load("pages/scheda_profilo.inc.php?pg=<?=urlencode($pg);?>");
    });

</script>