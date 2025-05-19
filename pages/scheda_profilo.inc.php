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

?>

<div class="profilo"><!-- Punteggi, salute, status, classe, razza. -->    
    <div class="titolo_box">
        <?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['box_title']['profile']); ?>
    </div>
    <?php if($record['permessi'] > 0) { ?>
        <div class="profilo_voce">
            <div class="profilo_voce_label">
                <?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['profile']['role']); ?>:
            </div>
            <div class="profilo_voce_valore">
                <?php
                switch($personaggio['permessi']) {
                    case USER:
                        $permessi_utente = '';
                        break;
                    case GUILDMODERATOR:
                        $permessi_utente = $PARAMETERS['names']['guild_name']['lead'];
                        break;
                    case GAMEMASTER:
                        $permessi_utente = $PARAMETERS['names']['master']['sing'];
                        break;
                    case MODERATOR:
                        $permessi_utente = $PARAMETERS['names']['moderators']['sing'];
                        break;
                    case SUPERUSER:
                        $permessi_utente = $PARAMETERS['names']['administrator']['sing'];
                        break;
                }
                echo gdrcd_filter('out', $permessi_utente).' <img src="imgs/icons/permessi'.(int) $personaggio['permessi'].'.gif" class="profilo_img_gilda" />'; ?>
            </div>
        </div>
    <?php } ?>
    <div class="profilo_voce">
        <div class="profilo_voce_label">
            <?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['profile']['occupation']); ?>:
        </div>
        <div class="profilo_voce_valore">
            <?php //carico le gilde
            $guilds = gdrcd_query("SELECT ruolo.nome_ruolo, ruolo.gilda, ruolo.immagine, gilda.visibile, gilda.nome AS nome_gilda FROM clgpersonaggioruolo LEFT JOIN ruolo ON ruolo.id_ruolo = clgpersonaggioruolo.id_ruolo LEFT JOIN gilda ON ruolo.gilda = gilda.id_gilda WHERE clgpersonaggioruolo.personaggio = '".gdrcd_filter('in', $personaggio['nome'])."'", 'result');
            if(gdrcd_query($guilds, 'num_rows') == 0) {
                echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['profile']['uneployed']);
            } else {
                while($row_guilds = gdrcd_query($guilds, 'fetch')) {
                    if($row_guilds['gilda'] == -1) {
                        echo '<img class="profilo_img_gilda"  src="themes/'.$PARAMETERS['themes']['current_theme'].'/imgs/guilds/'.gdrcd_filter('out', $row_guilds['immagine']).'" alt="'.gdrcd_filter('out', $row_guilds['nome_ruolo']).'" title="'.gdrcd_filter('out', $row_guilds['nome_ruolo']).'" />';
                    } else {
                        if(($row_guilds['visibile'] == 1) || ($_SESSION['permessi'] >= USER)) {
                            echo '<a href="main.php?page=servizi_gilde&id_gilda='.$row_guilds['gilda'].'"><img class="profilo_img_gilda"  src="themes/'.$PARAMETERS['themes']['current_theme'].'/imgs/guilds/'.gdrcd_filter('out', $row_guilds['immagine']).'" alt="'.gdrcd_filter('out', $row_guilds['nome_ruolo'].' - '.$row_guilds['nome_gilda']).'" title="'.gdrcd_filter('out', $row_guilds['nome_ruolo'].' - '.$row_guilds['nome_gilda']).'" /></a>';
                        }
                    }
                }

            } ?>
        </div>
    </div>
    <div class="profilo_voce">
        <div class="profilo_voce_label">
            <?php echo gdrcd_filter('out', $PARAMETERS['names']['race']['sing']); ?>:
        </div>
        <div class="profilo_voce_valore">
            <?php if((empty($personaggio['sing_f']) == false) || (empty($personaggio['sing_m']) == false)) {
                echo ($personaggio['sesso'] == 'f') ? gdrcd_filter('out', $personaggio['sing_f']) : gdrcd_filter('out', $personaggio['sing_m']);
            } else {
                echo gdrcd_filter('out', $PARAMETERS['names']['race']['sing'].' '.$MESSAGE['interface']['sheet']['profile']['no_race']);
            } ?>
        </div>
    </div>
    <div class="profilo_voce">
        <div class="profilo_voce_label">
            <?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['profile']['experience']); ?>:
        </div>
        <div class="profilo_voce_valore">
            <?php echo gdrcd_filter('out', floor($personaggio['esperienza'])); ?>
        </div>
    </div>
    <!-- caratteristiche -->
    <div class="profilo_voce">
        <div class="profilo_voce_label">
            <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car0']); ?>:
        </div>
        <div class="profilo_voce_valore">
            <?php echo gdrcd_filter('out', $personaggio['car0'] + $personaggio['bonus_car0'] + $bonus_oggetti['BO0']); ?>
        </div>
    </div>
    <div class="profilo_voce">
        <div class="profilo_voce_label">
            <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car1']); ?>:
        </div>
        <div class="profilo_voce_valore">
            <?php echo gdrcd_filter('out', $personaggio['car1'] + $personaggio['bonus_car1'] + $bonus_oggetti['BO1']); ?>
        </div>
    </div>
    <div class="profilo_voce">
        <div class="profilo_voce_label">
            <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car2']); ?>:
        </div>
        <div class="profilo_voce_valore">
            <?php echo gdrcd_filter('out', $personaggio['car2'] + $personaggio['bonus_car2'] + $bonus_oggetti['BO2']); ?>
        </div>
    </div>
    <div class="profilo_voce">
        <div class="profilo_voce_label">
            <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car3']); ?>:
        </div>
        <div class="profilo_voce_valore">
            <?php echo gdrcd_filter('out', $personaggio['car3'] + $personaggio['bonus_car3'] + $bonus_oggetti['BO3']); ?>
        </div>
    </div>
    <div class="profilo_voce">
        <div class="profilo_voce_label">
            <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car4']); ?>:
        </div>
        <div class="profilo_voce_valore">
            <?php echo gdrcd_filter('out', $personaggio['car4'] + $personaggio['bonus_car4'] + $bonus_oggetti['BO4']); ?>
        </div>
    </div>
    <div class="profilo_voce">
        <div class="profilo_voce_label">
            <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['car5']); ?>:
        </div>
        <div class="profilo_voce_valore">
            <?php echo gdrcd_filter('out', $personaggio['car5'] + $personaggio['bonus_car5'] + $bonus_oggetti['BO5']); ?>
        </div>
    </div>
    <div class="profilo_voce">
        <div class="profilo_voce_label">
            <?php echo gdrcd_filter('out', $PARAMETERS['names']['stats']['hitpoints']); ?>:
        </div>
        <div class="profilo_voce_valore">
            <?php echo gdrcd_filter('out', $personaggio['salute']).'/'.gdrcd_filter('out', $personaggio['salute_max']); ?>
        </div>
    </div>
    <div class="profilo_voce">
        <div class="profilo_voce_label">
            <?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['profile']['status']); ?>:
        </div>
        <div class="profilo_voce_valore">
            <?php echo nl2br(gdrcd_filter('out', $personaggio['stato'])); ?>
        </div>
    </div>
</div>