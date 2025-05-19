<?php

/** Homepage
 * Markup e procedure della homepage
 * @author Blancks
 */

/*
 * Includo i Crediti
 */
require 'includes/credits.inc.php';

/*
 * Conteggio utenti online
 */
$users = gdrcd_query("SELECT COUNT(nome) AS online FROM personaggio WHERE ora_entrata > ora_uscita AND DATE_ADD(ultimo_refresh, INTERVAL 4 MINUTE) > NOW()");


?>
<div id="main">
    <div id="site_width">
<div class="contenitore">
        <div id="content">
          <div class="titolo">A Venezia, la carne si vende e l'ombra si compra.</div>
            <div class="sidecontent">
                <ul>
                    <li>
                        <a href="javascript:modalWindow('scheda_iscr', 'Iscrizione', 'popup-est.php?page=iscrizione')"><?php echo $MESSAGE['homepage']['registration']; ?></a>
                    </li>
                    <li>
                        <a href="javascript:modalWindow('scheda_reg', 'Regolamento', 'popup-est.php?page=user_regolamento')"><?php echo $MESSAGE['homepage']['rules']; ?></a>
                    </li>
                    <li>
                        <a href="javascript:modalWindow('scheda_amb', 'Ambientazione', 'popup-est.php?page=user_ambientazione')"><?php echo $MESSAGE['homepage']['storyline']; ?></a>
                    </li>
                    <li>
                        <a href="javascript:modalWindow('scheda_raz', 'Razze', 'popup-est.php?page=user_razze')"><?php echo $MESSAGE['homepage']['races']; ?></a>
                    </li>
                </ul>
        <div class="side_modules">
                    <?php
                        // Include il modulo di reset della password
                        include (__DIR__ . '/reset_password.inc.php');
                    ?>
                </div>

            </div>

      
            <br class="blank"/>
        
                    <div class="logincontent">
                <div class="login_form">
                    <form action="login.php" id="do_login" method="post"
                        <?php if ($PARAMETERS['mode']['popup_choise'] == 'ON') { echo ' onsubmit="check_login(); return false;"';} ?>
                    >
                        <div>
                            <span class="form_label"><label for="username"><?php echo $MESSAGE['homepage']['forms']['username']; ?></label></span>
                            <input type="text" id="username" name="login1"/>
                        </div>
                        <div>
                            <span class="form_label"><label for="password"><?php echo $MESSAGE['homepage']['forms']['password']; ?></label></span>
                            <input type="password" id="password" name="pass1"/>
                        </div>
                        <?php if (!empty($PARAMETERS['themes']['available']) and count($PARAMETERS['themes']['available']) > 1): ?>
                            <div>
                                <span class="form_label"><label for="theme"><?= gdrcd_filter('out', $MESSAGE['homepage']['forms']['theme_choice']) ?></label></span>
                                <select name="theme" id="theme">
                                    <?php
                                    foreach ($PARAMETERS['themes']['available'] as $k => $name) {
                                        echo '<option value="' . gdrcd_filter('out', $k) . '"';
                                        if ($k == $PARAMETERS['themes']['current_theme']) {
                                            echo ' selected="selected"';
                                        }
                                        echo '>' . gdrcd_filter('out', $name) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        <?php endif; ?>
                        <?php if ($PARAMETERS['mode']['popup_choise'] == 'ON') { ?>
                             <!--<div>
                               <span class="form_label"><label for="allow_popup"><?php echo $MESSAGE['homepage']['forms']['open_in_popup']; ?></label></span>
                                <input type="checkbox" id="allow_popup"/>
                                <input type="hidden" value="0" name="popup" id="popup">
                            </div>-->
                        <?php } ?>
                        <input type="submit" value="<?php echo $MESSAGE['homepage']['forms']['login']; ?>"/>
                    </form>
                </div>
            </div>
        </div>
        <div class="content_body">
    <?php
        gdrcd_load_modules('homepage__'.$MODULE['content']);
    ?>
</div>
        
        </div>
 </div>
    </div>

