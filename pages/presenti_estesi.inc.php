

<!-- Box presenti -->
<div class="pagina_presenti_estesa">
    <div class="page_title">
        <h2><?php echo gdrcd_filter('out', $MESSAGE['interface']['logged_users']['page_title']); ?></h2>
    </div>
    <div class="presenti_estesi">
        <?php
        if ($PARAMETERS['mode']['user_online_state'] == 'ON') {
            echo '<div id="descriptionLoc"></div>';
        }

        $query = "SELECT personaggio.nome, personaggio.cognome, personaggio.permessi, personaggio.sesso, personaggio.id_razza, razza.sing_m, razza.sing_f, razza.icon, personaggio.disponibile, personaggio.online_status, personaggio.is_invisible, personaggio.ultima_mappa, personaggio.ultimo_luogo, personaggio.posizione, personaggio.ora_entrata, personaggio.ora_uscita, personaggio.ultimo_refresh, mappa.stanza_apparente, mappa.nome as luogo, mappa_click.nome as mappa, personaggio.url_img_chat FROM personaggio LEFT JOIN mappa ON personaggio.ultimo_luogo = mappa.id LEFT JOIN mappa_click ON personaggio.ultima_mappa = mappa_click.id_click LEFT JOIN razza ON personaggio.id_razza = razza.id_razza WHERE personaggio.ora_entrata > personaggio.ora_uscita AND DATE_ADD(personaggio.ultimo_refresh, INTERVAL 4 MINUTE) > NOW() ORDER BY personaggio.is_invisible, personaggio.ultima_mappa, personaggio.ultimo_luogo, personaggio.nome";
        $result = gdrcd_query($query, 'result');

        echo '<ul class="elenco_presenti">';
        $ultimo_luogo_corrente = '';
        $mappa_corrente = '';

        while ($record = gdrcd_query($result, 'fetch')) {

            if ($record['is_invisible'] == 1) {
                $luogo_corrente = $MESSAGE['status_pg']['invisible'][1];
            } else {
                if ($record['mappa'] != $mappa_corrente) {
                    $mappa_corrente = $record['mappa'];
                    echo '<li class="luogo">'.gdrcd_filter('out', $mappa_corrente).'</li>';
                }

                $luogo_corrente = empty($record['stanza_apparente']) ? $record['luogo'] : $record['stanza_apparente'];
            }

            if ($ultimo_luogo_corrente != $luogo_corrente) {
                $ultimo_luogo_corrente = $luogo_corrente;
                if ($record['is_invisible'] == 0 && ($PARAMETERS['mode']['mapwise_links'] == 'OFF')) {
                    echo '<li class="luogo"><a href="main.php?dir='.$record['ultimo_luogo'].'&map_id='.$record['ultima_mappa'].'">'.gdrcd_filter('out', $luogo_corrente).'</a></li>';
                } else {
                    echo '<li class="luogo">'.gdrcd_filter('out', $luogo_corrente).'</li>';
                }
            }

            echo '<li class="presente">';
            echo '<div class="presente_container">';

            // Miniavatar
            $miniavatar_url = !empty($record['url_img_chat']) ? $record['url_img_chat'] : 'imgs/icons/standard_avatar.png';
            echo '<img class="miniavatar" src="' . $miniavatar_url . '" alt="Miniavatar di ' . gdrcd_filter('out', $record['nome']) . '" />';

            echo '<div class="presente_info">';

            $activity = gdrcd_check_time($record['ora_entrata']);
            $icon = ($activity <= 2) ? 'enter.gif' : 'blank.png';
            echo '<img class="presenti_ico" src="imgs/icons/'.$icon.'" alt="" />';

            $permessi_icon = 'permessi'.$record['permessi'].'.gif';
            switch ($record['permessi']) {
                case USER: $alt_permessi = ''; break;
                case GUILDMODERATOR: $alt_permessi = $PARAMETERS['names']['guild_name']['lead']; break;
                case GAMEMASTER: $alt_permessi = $PARAMETERS['names']['master']['sing']; break;
                case MODERATOR: $alt_permessi = $PARAMETERS['names']['moderators']['sing']; break;
                case SUPERUSER: $alt_permessi = $PARAMETERS['names']['administrator']['sing']; break;
            }
            echo '<img class="presenti_ico" src="imgs/icons/'.$permessi_icon.'" alt="'.gdrcd_filter('out', $alt_permessi).'" title="'.gdrcd_filter('out', $alt_permessi).'" />';

            echo '<img class="presenti_ico" src="imgs/icons/disponibile'.$record['disponibile'].'.png" alt="" title="'.gdrcd_filter('out', $MESSAGE['status_pg']['availability'][$record['disponibile']]).'" />';

            $record['icon'] = ($record['icon'] == '') ? 'standard_razza.png' : $record['icon'];
            echo '<img class="presenti_ico" src="themes/'.$PARAMETERS['themes']['current_theme'].'/imgs/races/'.$record['icon'].'" alt="" />';

            echo '<img class="presenti_ico" src="imgs/icons/testamini'.$record['sesso'].'.png" alt="" />';

            echo '<a href="main.php?page=messages_center&op=create&destinatario='.gdrcd_filter('url', $record['nome']).'" class="link_sheet">MP</a> ';

            echo '<a href="main.php?page=scheda&pg='.$record['nome'].'" class="link_sheet gender_'.$record['sesso'].'">'.gdrcd_filter('out', $record['nome']);
            if (!empty($record['cognome'])) {
                echo ' '.gdrcd_filter('out', $record['cognome']);
            }
            echo '</a>';

            // Aggiunta descrizione sotto le icone e nome
            if (!empty(trim($record['online_status']))) {
                $descrizione = nl2br(gdrcd_filter('out', $record['online_status']));
                echo '<div class="presente_descrizione">'.$descrizione.'</div>';
            }

            echo '</div>'; // .presente_info
            echo '</div>'; // .presente_container
            echo '</li>';
        }

        echo '</ul>';
        ?>
    </div>
</div>
