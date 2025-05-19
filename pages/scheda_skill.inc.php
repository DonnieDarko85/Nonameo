<?php

require_once('../includes/required.php');

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

if($PARAMETERS['mode']['skillsystem'] == 'ON') {
    include ('scheda/skillsystem.inc.php');
} ?>