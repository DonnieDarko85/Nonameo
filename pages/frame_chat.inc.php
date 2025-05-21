<?php /* HELP: Frame della chat */
/* Tipi messaggio: (A azione, P parlato, N PNG, M Master, I Immagine, S sussurro, D dado, C skill check, O uso oggetto) */

/*Seleziono le info sulla chat corrente*/
$info = gdrcd_query("SELECT nome, stanza_apparente, invitati, privata, proprietario, scadenza FROM mappa WHERE id=".$_SESSION['luogo']." LIMIT 1");
?>
<div class="pagina_frame_chat">
    <div class="page_title"><h2><?php echo $info['nome']; ?></h2></div>
    <div class="page_body">
        <?php
        echo AudioController::build('chat');

        if($info['privata'] == 1) {
            $allowance = false;
            if((($info['proprietario'] == gdrcd_capital_letter($_SESSION['login'])) || (strpos($_SESSION['gilda'], $info['proprietario']) != false) || (strpos($info['invitati'], gdrcd_capital_letter($_SESSION['login'])) != false) || (($PARAMETERS['mode']['spyprivaterooms'] == 'ON') && ($_SESSION['permessi'] > MODERATOR))) && ($info['scadenza'] > strftime('%Y-%m-%d %H:%M:%S'))) {
                $allowance = true;
            }
        } else {
            $allowance = true;
        }

        if($allowance === false) {
            echo '<div class="warning">'.$MESSAGE['chat']['whisper']['privat'].'</div>';
        } else {
        ?>
        <?php $_SESSION['last_message'] = 0; ?>
        <div style="height: 1px; width: 1px;">
            <iframe src="pages/chat.inc.php?ref=30&chat=yes" class="iframe_chat" id="chat_frame" name="chat_frame" frameborder="0" allowtransparency="true"></iframe>
        </div>
        <div id='pagina_chat' class="chat_box"></div>
        <div class="panels_box">
            <div class="form_chat">
                <!-- Form messaggi -->
                <div class="form_row">
                    <form action="pages/chat.inc.php?ref=10&chat=yes" method="post" target="chat_frame" id="chat_form_messages">
                        <div class="casella_chat">
                                <select name="type" id="type">
                                    <option value="0"><?php echo gdrcd_filter('out', $MESSAGE['chat']['type'][0]);//parlato?></option>
                                    <option value="1"><?php echo gdrcd_filter('out', $MESSAGE['chat']['type'][1]);//azione?></option>
                                    <option value="4"><?php echo gdrcd_filter('out', $MESSAGE['chat']['type'][4]);//sussurro?></option>
                                    <option value="8"><?php echo gdrcd_filter('out', $MESSAGE['chat']['type'][8]);//sussurro globale?></option>
                                    <?php if ($_SESSION['permessi'] == GAMEMASTER || $_SESSION['permessi'] == SUPERUSER || $_SESSION['permessi'] == GAMEMASTERJUNIOR) { ?>
                                        <option value="2"><?php echo gdrcd_filter('out', $MESSAGE['chat']['type'][2]);//master?></option>
                                        <option value="3"><?php echo gdrcd_filter('out', $MESSAGE['chat']['type'][3]);//png?></option>
                                    <?php } ?>
                                    <?php if (($info['privata'] == 1) && (($info['proprietario'] == $_SESSION['login']) || ((is_numeric($info['proprietario']) === true) && (strpos($_SESSION['gilda'], '' . $info['proprietario']))))) { ?>
                                        <option value="5"><?php echo gdrcd_filter('out', $MESSAGE['chat']['type'][5]);//invita?></option>
                                        <option value="6"><?php echo gdrcd_filter('out', $MESSAGE['chat']['type'][6]);//caccia?></option>
                                        <option value="7"><?php echo gdrcd_filter('out', $MESSAGE['chat']['type'][7]);//elenco?></option>
                                    <?php }//if
                                    ?>
                                </select>
                                <br/>
                            </div>
                        <div class="casella_chat">
                            <input name="tag" id="tag" value="" />
                            <br /><span class="casella_info">
                                <?php echo gdrcd_filter('out', $MESSAGE['chat']['tag']['info']['tag'].$MESSAGE['chat']['tag']['info']['dst']);
                                if($_SESSION['permessi'] >= GAMEMASTER) {
                                    echo gdrcd_filter('out', $MESSAGE['chat']['tag']['info']['png']);
                                } ?>
                            </span>
                        </div>
                        <div class="casella_chat">
                            <textarea name="message" id="message"></textarea>
                            <br />
                            <?php if($PARAMETERS['mode']['chatsave'] == 'ON') { ?>
                                <span class="casella_info">
                                    <a href="javascript:void(0);" onClick="window.open('chat_save.proc.php','Log','width=1,height=1,toolbar=no');">⇩ Salva Chat</a>
                                </span>
                            <?php }/*
                            if (REG_ROLE) { ?>
                                 <span>&nbsp;&nbsp;||&nbsp;&nbsp;</span><a href="javascript:parent.modalWindow('rolesreg', '', 'popup.php?page=chat_pannelli_index&pannello=segnalazione_role');">
                                   Registra giocata
                                </a><span>&nbsp;&nbsp;||&nbsp;&nbsp;</span>
                            <?php } */?>
                            <span>&nbsp;&nbsp;||&nbsp;&nbsp;</span>

                            <a href="javascript:void(0);" onclick="document.getElementById('modale_custom').style.display='block'">✎ Blocco Note</a>
                            
                            <?php if(($PARAMETERS['mode']['skillsystem'] == 'ON') || ($PARAMETERS['mode']['dices'] == 'ON')) { ?>
                                <span>&nbsp;&nbsp;||&nbsp;&nbsp;</span>
                                <a href="javascript:void(0);" onClick="document.getElementById('dadoModal').style.display='block'">
                                    ⚃ Dadi e Abilità
                                </a>
                            <?php } ?>
                        </div>
                        <div class="casella_chat">
                            <input type="submit" value="<?php echo gdrcd_filter('out', $MESSAGE['interface']['forms']['submit']); ?>" />
                            <input type="hidden" name="op" value="new_chat_message" />
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODALE PERSONALIZZATA -->
        <div id="modale_custom" style="display:none; position:fixed; top:10%; left:50%; transform:translateX(-50%); background:#222; color:#ccc; padding:20px; border:1px solid #444; z-index:1000; box-shadow:0 0 10px #000; width: 500px;">
        <div id="blocconoteHeader" style="cursor: move; background-color: #111; color: #a19c7b; padding: 10px; display: flex; justify-content: flex-end; margin-bottom: 10px;font-family: 'ElliotSix'; letter-spacing: 2px;"><span>Blocco Note</span> <span onclick="minimizzaBlocco()" style="cursor:pointer; margin-left: 10px;" title="Minimizza">_</span> <span onclick="document.getElementById('modale_custom').style.display='none'" style="cursor:pointer; font-weight:bold;">✕</span></div>    
            <textarea id="modale_textarea" style="width:100%; height:200px;"></textarea>
<div style="display: flex; justify-content: space-between; margin-top: 10px;">
    <button onclick="cancellaNote()">Cancella</button>
    <button onclick="inviaDaModale()">Invia</button>
</div></div>

        <!-- MODALE DEL DADO -->
<div id="dadoModal" style="display:none; position:fixed; top:10%; left:50%; transform:translateX(-50%); background:#a19c7b; padding:20px; border:1px solid #444; z-index:1000; box-shadow:0 0 10px #000; width: 390px;">

    <!-- Header con X e Minimizza -->
     <div id="dadoHeader" style="cursor: move; background-color: #111; color: #a19c7b; padding: 10px; display: flex; justify-content: flex-end; margin-bottom: 10px;font-family: 'ElliotSix'; letter-spacing: 2px;">
        <span>Lancio Dado</span>                        
        <span onclick="minimizzaDado()" style="cursor:pointer; margin-left: 10px;" title="Minimizza">_</span>
        <span onclick="document.getElementById('dadoModal').style.display='none'" style="cursor:pointer; margin-left: 10px; font-weight: bold;">✕</span>
    </div>

    <form action="pages/chat.inc.php?ref=30&chat=yes" method="post" target="chat_frame" id="chat_form_actions">
        <!-- ...tutto il resto del form rimane invariato -->
                <?php if($PARAMETERS['mode']['skillsystem'] == 'ON') { ?>
    <div class="casella_chat">
        <?php $result = gdrcd_query("SELECT id_abilita, nome FROM abilita WHERE id_razza=-1 OR id_razza IN (SELECT id_razza FROM personaggio WHERE nome = '".$_SESSION['login']."') ORDER BY nome", 'result'); ?>
        <select name="id_ab" id="id_ab">
            <option value="no_skill"></option>
            <?php while($row = gdrcd_query($result, 'fetch')) { ?>
                <option value="<?php echo $row['id_abilita']; ?>"><?php echo gdrcd_filter('out', $row['nome']); ?></option>
            <?php } gdrcd_query($result, 'free'); ?>
        </select>
        <br /><span class="casella_info"><?php echo gdrcd_filter('out', $MESSAGE['chat']['commands']['skills']); ?></span>
    </div>
    <div class="casella_chat">
        <select name="id_stats" id="id_stats">
            <option value="no_stats"></option>
            <?php foreach($PARAMETERS['names']['stats'] as $id_stats => $name_stats) {
                if(is_numeric(substr($id_stats, 3))) { ?>
                    <option value="stats_<?php echo substr($id_stats, 3); ?>"><?php echo $name_stats; ?></option>
                <?php } } ?>
        </select>
        <br /><span class="casella_info"><?php echo gdrcd_filter('out', $MESSAGE['chat']['commands']['stats']); ?></span>
    </div>
<?php } else {
    echo '<input type="hidden" name="id_ab" value="no_skill">';
} ?>

<?php if($PARAMETERS['mode']['dices'] == 'ON') { ?>
    <div class="casella_chat">
        <select name="dice" id="dice">
            <option value="no_dice"></option>
            <?php foreach($PARAMETERS['settings']['skills_dices'] as $dice_name => $dice_value) { ?>
        <option value="<?php echo $dice_value; ?>" <?php if($dice_value == 20) echo 'selected'; ?>>
            <?php echo $dice_name; ?>
        </option>
    <?php } ?>
        </select>
        <br /><span class="casella_info"><?php echo gdrcd_filter('out', $MESSAGE['chat']['commands']['dice']); ?></span>
    </div>
<?php } else {
    echo '<input type="hidden" name="dice" value="no_dice">';
} ?>
<div class="casella_chat">
    <input type="submit" value="<?php echo gdrcd_filter('out', $MESSAGE['interface']['forms']['submit']); ?>" />
    <input type="hidden" name="op" value="take_action">
</div>
            </form>
        </div>

        <?php } ?>
    </div>
</div>

<script>
        function minimizzaDado() {
        const modal = document.getElementById('dadoModal');
        modal.style.display = 'none';

        // Puoi anche mostrare un'icona o bottone per riaprirla (esempio base qui sotto)
        let icon = document.getElementById('dadoIcon');
        if (!icon) {
            icon = document.createElement('div');
            icon.id = 'dadoIcon';
            icon.innerText = 'Dadi';
            icon.title = 'Lancio Dado';
            icon.style.position = 'fixed';
            icon.style.bottom = '10px';
            icon.style.right = '10px';
            icon.style.cursor = 'pointer';
            icon.style.fontSize = '15px';
            icon.style.zIndex = '1001';
            document.body.appendChild(icon);
            icon.onclick = function() {
                modal.style.display = 'block';
                icon.remove();
            };
        }
    }

        function minimizzaBlocco() {
        const modalBlocco = document.getElementById('modale_custom');
        modalBlocco.style.display = 'none';

        // Puoi anche mostrare un'icona o bottone per riaprirla (esempio base qui sotto)
        let blocco_icon = document.getElementById('bloccoIcon');
        if (!blocco_icon) {
            blocco_icon = document.createElement('div');
            blocco_icon.id = 'bloccoIcon';
            blocco_icon.innerText = 'Note';
            blocco_icon.title = 'Blocco Note';
            blocco_icon.style.position = 'fixed';
            blocco_icon.style.bottom = '30px';
            blocco_icon.style.right = '10px';
            blocco_icon.style.cursor = 'pointer';
            blocco_icon.style.fontSize = '15px';
            blocco_icon.style.fontFamily = 'Bitter';
            blocco_icon.style.zIndex = '1001';
            document.body.appendChild(blocco_icon);
            blocco_icon.onclick = function() {
                modalBlocco.style.display = 'block';
                blocco_icon.remove();
            };
        }
    }

    makeDraggable(document.getElementById("dadoModal"), document.getElementById("dadoHeader"));
    makeDraggable(document.getElementById("modale_custom"), document.getElementById("blocconoteHeader"));


    function makeDraggable(modal, handle) {
        let pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;

        handle.onmousedown = dragMouseDown;

        function dragMouseDown(e) {
            e = e || window.event;
            e.preventDefault();
            pos3 = e.clientX;
            pos4 = e.clientY;
            document.onmouseup = closeDragElement;
            document.onmousemove = elementDrag;
        }

        function elementDrag(e) {
            e = e || window.event;
            e.preventDefault();
            pos1 = pos3 - e.clientX;
            pos2 = pos4 - e.clientY;
            pos3 = e.clientX;
            pos4 = e.clientY;
            modal.style.top = (modal.offsetTop - pos2) + "px";
            modal.style.left = (modal.offsetLeft - pos1) + "px";
        }

        function closeDragElement() {
            document.onmouseup = null;
            document.onmousemove = null;
        }
    }
    // Sincronizza modale con textarea principale
    const modaleTextarea = document.getElementById('modale_textarea');
    const mainTextarea = document.getElementById('message');

    function syncTextareas() {
        modaleTextarea.value = mainTextarea.value;
    }

    document.querySelector("a[href='javascript:void(0);'][onclick*='modale_custom']").addEventListener("click", syncTextareas);

    modaleTextarea.addEventListener('input', function () {
        mainTextarea.value = modaleTextarea.value;
    });
    
    function inviaDaModale() {
        mainTextarea.value = modaleTextarea.value;
        document.getElementById('chat_form_messages').submit();
    }

    function cancellaNote() {
    document.getElementById('modale_textarea').value = '';  // svuota textarea
    document.getElementById('message').value = '';         // svuota anche la textarea principale sincronizzata
}
</script>
