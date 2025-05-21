<?php
// Carico le sole abilita del PG
$abilita = gdrcd_query("SELECT id_abilita, grado FROM clgpersonaggioabilita WHERE nome='".gdrcd_filter('in', $_REQUEST['pg'])."'", 'result');

$px_spesi = 0;
while($row = gdrcd_query($abilita, 'fetch')) {
    $px_abi = $PARAMETERS['settings']['px_x_rank'] * (($row['grado'] * ($row['grado'] + 1)) / 2);
    $px_spesi += $px_abi;
    $ranks[$row['id_abilita']] = $row['grado'];
}

$personaggio = gdrcd_query("SELECT id_razza, esperienza FROM personaggio WHERE nome='".gdrcd_filter('in', $_REQUEST['pg'])."'", 'query');
$px_totali_pg = gdrcd_filter('int', $personaggio['esperienza']);
?>
<div class="elenco_abilita pagina_scheda_modale">
    <div class="titolo_box">
        <?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['box_title']['skills']); ?>
    </div>

    <?php
    // Incremento skill
    if((gdrcd_filter('get', $_REQUEST['op']) == 'addskill') && (($_SESSION['login'] == gdrcd_filter('out', $_REQUEST['pg'])) || ($_SESSION['permessi'] >= MODERATOR))) {
        $px_necessari = $PARAMETERS['settings']['px_x_rank'] * ($ranks[$_REQUEST['what']] + 1);
        if(($px_totali_pg - $px_spesi) >= $px_necessari) {
            $px_spesi += $px_necessari;
            if($px_necessari == $PARAMETERS['settings']['px_x_rank']) {
                $query = "INSERT INTO clgpersonaggioabilita (id_abilita, nome, grado) VALUES (".gdrcd_filter('num', $_REQUEST['what']).", '".gdrcd_filter('in', $_REQUEST['pg'])."', 1)";
                $ranks[$_REQUEST['what']] = 1;
            } else {
                $ranks[$_REQUEST['what']]++;
                $query = "UPDATE clgpersonaggioabilita SET grado = ".$ranks[$_REQUEST['what']]." WHERE id_abilita = ".gdrcd_filter('num', $_REQUEST['what'])." AND nome = '".gdrcd_filter('in', $_REQUEST['pg'])."'";
            }
            gdrcd_query($query);
            echo '<div class="warning">'.gdrcd_filter('out', $MESSAGE['warning']['modified']).'</div>';
        }
    }

    // Decremento skill
    if((gdrcd_filter('get', $_REQUEST['op']) == 'subskill') && ($_SESSION['permessi'] >= MODERATOR)) {
        if($ranks[$_REQUEST['what']] == 1) {
            $query = "DELETE FROM clgpersonaggioabilita WHERE id_abilita = ".$_REQUEST['what']." AND nome = '".gdrcd_filter('in', $_REQUEST['pg'])."' LIMIT 1";
            $ranks[$_REQUEST['what']] = 0;
        } else {
            $ranks[$_REQUEST['what']]--;
            $query = "UPDATE clgpersonaggioabilita SET grado = ".$ranks[$_REQUEST['what']]." WHERE id_abilita = ".$_REQUEST['what']." AND nome = '".gdrcd_filter('in', $_REQUEST['pg'])."'";
        }
        gdrcd_query($query);
        echo '<div class="warning">'.gdrcd_filter('out', $MESSAGE['warning']['modified']).'</div>';
    }

    // carico elenco abilita filtrato per razza
    $result = gdrcd_query("SELECT nome, car, id_abilita FROM abilita WHERE id_razza=-1 OR id_razza= ".$personaggio['id_razza']." ORDER BY nome", 'result');

    // organizzo per caratteristica
    $abilita_per_car = array();
    while($row = gdrcd_query($result, 'fetch')) {
        $abilita_per_car[$row['car']][] = $row;
    }
    ?>

    <div class="form_info">
        <?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['avalaible_xp']).': '.($px_totali_pg - $px_spesi); ?>
    </div>

    <div class="div_colonne_abilita_scheda">
        <?php
        // Mostro in due colonne: car0+car1, car2+car3, car4+car5
        for ($i = 0; $i <= 4; $i += 2) {
            echo '<div class="due_colonne_abilita">';

            // Colonna sinistra
            echo '<div class="colonna_abilita">';
            if (isset($abilita_per_car[$i])) {
                echo '<h3>'.gdrcd_filter('out', strtoupper($PARAMETERS['names']['stats']['car'.$i])).'</h3>';
                echo '<table>';
                foreach ($abilita_per_car[$i] as $abilita) {
                    $grado = 0 + gdrcd_filter('int', $ranks[$abilita['id_abilita']]);
                    echo '<tr>';
                    echo '<td>'.gdrcd_filter('out', $abilita['nome']).'</td>';
                    echo '<td>'.$grado.'</td>';
                    echo '<td>';
                    if((($grado + 1) * $PARAMETERS['settings']['px_x_rank'] <= ($px_totali_pg - $px_spesi)) && ($_REQUEST['pg'] == $_SESSION['login']) && ($grado < $PARAMETERS['settings']['skills_cap'])) {
                        echo '[<a href="main.php?page=scheda&pg='.gdrcd_filter('url', $_REQUEST['pg']).'&op=addskill&what='.$abilita['id_abilita'].'">+</a>]';
                    }
                    if(($_SESSION['permessi'] >= MODERATOR) && ($grado > 0)) {
                        echo ' [<a href="main.php?page=scheda&pg='.gdrcd_filter('url', $_REQUEST['pg']).'&op=subskill&what='.$abilita['id_abilita'].'">-</a>]';
                    }
                    echo '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            }
            echo '</div>';

            // Colonna destra
            echo '<div class="colonna_abilita">';
            if (isset($abilita_per_car[$i+1])) {
                echo '<h3>'.gdrcd_filter('out', strtoupper($PARAMETERS['names']['stats']['car'.($i+1)])).'</h3>';
                echo '<table>';
                foreach ($abilita_per_car[$i+1] as $abilita) {
                    $grado = 0 + gdrcd_filter('int', $ranks[$abilita['id_abilita']]);
                    echo '<tr>';
                    echo '<td>'.gdrcd_filter('out', $abilita['nome']).'</td>';
                    echo '<td>'.$grado.'</td>';
                    echo '<td>';
                    if((($grado + 1) * $PARAMETERS['settings']['px_x_rank'] <= ($px_totali_pg - $px_spesi)) && ($_REQUEST['pg'] == $_SESSION['login']) && ($grado < $PARAMETERS['settings']['skills_cap'])) {
                        echo '[<a href="main.php?page=scheda&pg='.gdrcd_filter('url', $_REQUEST['pg']).'&op=addskill&what='.$abilita['id_abilita'].'">+</a>]';
                    }
                    if(($_SESSION['permessi'] >= MODERATOR) && ($grado > 0)) {
                        echo ' [<a href="main.php?page=scheda&pg='.gdrcd_filter('url', $_REQUEST['pg']).'&op=subskill&what='.$abilita['id_abilita'].'">-</a>]';
                    }
                    echo '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            }
            echo '</div>';

            echo '</div>'; // fine due_colonne_abilita
        }
        ?>
    </div> <!-- fine div_colonne_abilita_scheda -->

    <div class="form_info">
        <?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['info_skill_cost']); ?>
    </div>
</div>
