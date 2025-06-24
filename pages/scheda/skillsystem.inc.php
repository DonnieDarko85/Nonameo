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

    // carico elenco abilita filtrato per razza
    $result = gdrcd_query("SELECT nome, car, id_abilita FROM abilita WHERE id_razza=-1 OR id_razza= ".$personaggio['id_razza']." ORDER BY nome", 'result');

    // organizzo per caratteristica
    $abilita_per_car = array();
    while($row = gdrcd_query($result, 'fetch')) {
        $abilita_per_car[$row['car']][] = $row;
    }
    ?>

    <div class="form_info">
        <?= $MESSAGE['interface']['sheet']['avalaible_xp']; ?>:
        <span id="xp-disponibili"><?= ($px_totali_pg - $px_spesi); ?></span>
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
                    echo '<td><span class="skill-grade" data-id="'.$abilita['id_abilita'].'">'.$grado.'</span></td>';
                    echo '<td>';
                    if((($grado + 1) * $PARAMETERS['settings']['px_x_rank'] <= ($px_totali_pg - $px_spesi)) && ($_REQUEST['pg'] == $_SESSION['login']) && ($grado < $PARAMETERS['settings']['skills_cap'])) {
                        echo '<span class="skill-container" data-id="'.$abilita['id_abilita'].'" data-type="add">[<a href="#" class="skill-action" data-pg="'.gdrcd_filter('out', $_REQUEST['pg']).'" data-id="'.$abilita['id_abilita'].'" data-action="add">+</a>]</span>';
                    }
                    if ($_SESSION['permessi'] >= MODERATOR) {
                        $display = ($grado > 0) ? 'inline' : 'none';
                        echo ' <span class="skill-container" data-id="'.$abilita['id_abilita'].'" data-type="sub" style="display:'.$display.';">[<a href="#" class="skill-action" data-pg="'.gdrcd_filter('out', $_REQUEST['pg']).'" data-id="'.$abilita['id_abilita'].'" data-action="sub">-</a>]</span>';
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
                    echo '<td><span class="skill-grade" data-id="'.$abilita['id_abilita'].'">'.$grado.'</span></td>';
                    echo '<td>';
                    if((($grado + 1) * $PARAMETERS['settings']['px_x_rank'] <= ($px_totali_pg - $px_spesi)) && ($_REQUEST['pg'] == $_SESSION['login']) && ($grado < $PARAMETERS['settings']['skills_cap'])) {
                        echo '<span class="skill-container" data-id="'.$abilita['id_abilita'].'" data-type="add">[<a href="#" class="skill-action" data-pg="'.gdrcd_filter('out', $_REQUEST['pg']).'" data-id="'.$abilita['id_abilita'].'" data-action="add">+</a>]</span>';
                    }
                    if ($_SESSION['permessi'] >= MODERATOR) {
                        $display = ($grado > 0) ? 'inline' : 'none';
                        echo ' <span class="skill-container" data-id="'.$abilita['id_abilita'].'" data-type="sub" style="display:'.$display.';">[<a href="#" class="skill-action" data-pg="'.gdrcd_filter('out', $_REQUEST['pg']).'" data-id="'.$abilita['id_abilita'].'" data-action="sub">-</a>]</span>';
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




<script>
$(document).ready(function () {
    const pxPerRank = <?= $PARAMETERS['settings']['px_x_rank']; ?>;
    const skillsCap = <?= $PARAMETERS['settings']['skills_cap']; ?>;
    let pxDisponibili = <?= ($px_totali_pg - $px_spesi); ?>;

    $('.skill-action').on('click', function (e) {
        e.preventDefault();
        const pg = $(this).data('pg');
        const skillId = $(this).data('id');
        const action = $(this).data('action');

        fetch('pages/scheda/handle_skill.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ pg, what: skillId, action })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const gradeSpan = $('.skill-grade[data-id="' + skillId + '"]');
                let current = parseInt(gradeSpan.text());

                const costo = pxPerRank * (action === 'add' ? (current + 1) : current);

                if (action === 'add') {
                    current++;
                    pxDisponibili -= costo;
                } else {
                    pxDisponibili += costo;
                    current--;
                }

                gradeSpan.text(current);
                $('#xp-disponibili').text(pxDisponibili);

                // Mostra o nasconde i pulsanti con parentesi
                const addWrapper = $('.skill-container[data-id="' + skillId + '"][data-type="add"]');
                const subWrapper = $('.skill-container[data-id="' + skillId + '"][data-type="sub"]');

                if ((current + 1) * pxPerRank > pxDisponibili || current >= skillsCap) {
                    addWrapper.hide();
                } else {
                    addWrapper.show();
                }

                if (current === 0) {
                    subWrapper.hide();
                } else {
                    subWrapper.show();
                }

            } else {
                alert('Errore: ' + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Errore nella richiesta AJAX.');
        });
    });
});
</script>