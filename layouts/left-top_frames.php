<!--LAYOUT LEFT-TOP-->

<?php
/** * Pagina di layout.
 * E' selezionabile come layout principale per il proprio gdr semplicemente da config.inc.php
 * Contiene il css che viene richiamato separatamente come file esterno e il markup
 *
 * Il layout è a piena compatibilità con i browser.
 * La scelta di inserire qui il css ad esso destinato è per limitarne la modifica da parte dell'utente
 * consentendogli di personalizzare tutto il resto senza rovinare la compatibilità cross browser
 *
 * @author Blancks
 */
if (isset($_GET['css']))
{
    header('Content-Type:text/css; charset=utf-8');


    ?>@charset "utf-8";

    body{
    margin: 0;
    padding: 0;
    border: 0;
    overflow: hidden;
    height: 100%;
    max-height: 100%;
    }

    #framecontentLeft, #framecontentTop{
    position: absolute;
    top: 0;
    left: 0;
    width: 15vw; /*Width of left frame div*/
    height: 10vh;
    overflow: hidden; /*Disable scrollbars. Set to "scroll" to enable*/
    }

    #framecontentTop{
    width: 700px !important;
    left: auto;
    right: 0;
    top: 6px;
    width: auto;
    height: 70px;
    overflow: hidden;
    text-align: right;
    background: url(https://nonamegdr.altervista.org/themes/advanced/imgs/menu/sfondo-TOP.png);
    background-size: 100%;
    line-height: 4;
    }

    #maincontent{
    position: fixed;
    left: 210px; /*Set left value to WidthOfLeftFrameDiv*/
    top: 70px !important; /*Set top value to HeightOfTopFrameDiv*/
    right: 0;
    bottom: 0;
    overflow: auto;
    }

    .innertube{
    margin: 15px; /*Margins for inner DIV inside each DIV (to provide padding)*/
    }

    * html body{ /*IE6 hack*/
    padding: 100px 0 0 210px; /*Set value to (HeightOfTopFrameDiv 0 0 WidthOfLeftFrameDiv)*/
    }

    * html #maincontent{ /*IE6 hack*/
    height: 100%;
    width: 100%;
    }

    * html #framecontentTop{ /*IE6 hack*/
    width: 100%;
    }
	
    .mini_avatar {
    width: 85px;
    height: 85px;
    position: relative;
    top: -193px;
    left: 103px;
    filter: grayscale(100%);
    overflow: hidden;
    }
    
    .mini_avatar:hover {
    filter: grayscale(0%);
    }

	.mini_avatar img {
    width: 100%;
    height: 100%;
    }

    <?php

} else
{


    if ($PARAMETERS['left_column']['activate'] == 'ON')
    {

        ?>
        <!-- Colonna sinistra -->
        <div id="framecontentLeft">
            <div class="innertube">

                <div class="colonne_sx">
                    <?php
                    foreach ($PARAMETERS['left_column']['box'] as $box)
                    {
                        echo '<div class="' . $box['class'] . '">';

                        gdrcd_load_modules($box['page'], $box);

                        echo '</div>';
                    }

                    ?>
                </div>
				  <?php
$record = gdrcd_query("SELECT url_img_chat FROM personaggio WHERE nome LIKE '". gdrcd_filter('in', $_SESSION['login']) ."' LIMIT 1");
?>

<div class="mini_avatar">
    <!-- Modifica il link in modo che apra la modale -->
    <a href="javascript:void(0);" onclick="modalWindow('scheda-personaggio', 'Scheda Personaggio', 'https://nonamegdr.altervista.org/popup.php?page=scheda&pg=<?php echo gdrcd_filter('url', $_SESSION['login']); ?>', 800, 600)">
        <img src="<?php echo !empty(gdrcd_filter('fullurl', $record['url_img_chat']))? $record['url_img_chat'] : '../imgs/avatars/mini_empty.png' ?>"  alt="Avatar Chat" />
        
        <!--<img src="<?php echo gdrcd_filter('fullurl', $record['url_img_chat']); ?>" alt="Avatar Chat" />-->
    </a>
</div>
            </div>
        </div>
        <?php

    }


    if ($PARAMETERS['top_column']['activate'] == 'ON')
    {
        ?>

        <!-- Riga superiore  -->
        <div id="framecontentTop">
            <div class="innertube">

                <div class="colonne_top">
                    <?php

                    foreach ($PARAMETERS['top_column']['box'] as $box)
                    {
                        echo '<div class="' . $box['class'] . '">';

                        gdrcd_load_modules($box['page'], $box);

                        echo '</div>';

                    }

                    ?>
                </div>

            </div>
        </div>

        <?php

    }
    ?>

    <div id="maincontent">
        <div class="output">
            <?php gdrcd_load_modules($strInnerPage); ?>
        </div>
    </div>

    <?php

}

?>