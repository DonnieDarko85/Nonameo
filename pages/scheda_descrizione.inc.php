<?php

require_once('../includes/required.php');
require_once('../plugins/bbdecoder/bbdecoder.php');

?>

<div class="pagina_scheda_storia pagina_scheda_modale">
    <?php /*HELP: */
    //Se non e' stato specificato il nome del pg
    if(isset($_REQUEST['pg']) === false) {
        echo gdrcd_filter('out', $MESSAGE['error']['unknonw_character_sheet']);
        exit();
    }
    /*Visualizzo la pagina*/
    ?>
    <div class="page_title">
        <h2><?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['page_name']); ?></h2>
    </div>


    <div class="page_title">
        <h2><?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['menu']['description']); ?></h2>
    </div>
    <div class="page_body">
        <div class="panels_box">
            <?php /*Oggetti nello zaino*/
            $personaggio = gdrcd_query("SELECT descrizione, affetti FROM personaggio WHERE nome = '".gdrcd_filter('in', $_REQUEST['pg'])."'");

            ?>
            <div class="body_box">
                <?php
                /** * Html, bbcode o entrambi ?
                 * @author Blancks
                 */
                if($PARAMETERS['mode']['user_bbcode'] == 'ON') {
                    if($PARAMETERS['settings']['user_bbcode']['type'] == 'bbd' && $PARAMETERS['settings']['bbd']['free_html'] == 'ON') {
                        echo bbdecoder(gdrcd_html_filter($personaggio['descrizione']), true);
                    } elseif($PARAMETERS['settings']['user_bbcode']['type'] == 'bbd') {
                        echo bbdecoder(gdrcd_filter('out', $personaggio['descrizione']), true);
                    } else {
                        echo gdrcd_bbcoder(gdrcd_filter('out', $personaggio['descrizione']));
                    }
                } else {
                    echo gdrcd_html_filter($personaggio['descrizione']);
                } ?>
            </div>
        </div>
        <!-- Link a piè di pagina -->

    </div>
    <div class="page_title">
        <h2><?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['menu']['friend']); ?></h2>
    </div>
    <div class="page_body">
        <div class="panels_box">
            <div class="body_box">
                <?php
                /** * Html, bbcode o entrambi ?
                 * @author Blancks
                 */
                if($PARAMETERS['mode']['user_bbcode'] == 'ON') {
                    if($PARAMETERS['settings']['user_bbcode']['type'] == 'bbd' && $PARAMETERS['settings']['bbd']['free_html'] == 'ON') {
                        echo bbdecoder(gdrcd_html_filter($personaggio['affetti']), true);
                    } elseif($PARAMETERS['settings']['user_bbcode']['type'] == 'bbd') {
                        echo bbdecoder(gdrcd_filter('out', $personaggio['affetti']), true);
                    } else {
                        echo gdrcd_bbcoder(gdrcd_filter('out', $personaggio['affetti']));
                    }
                } else {
                    echo gdrcd_html_filter($personaggio['affetti']);
                } ?>
            </div>
        </div>
    </div>

</div><!-- Pagina -->