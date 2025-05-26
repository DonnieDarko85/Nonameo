<?php
function render_modal($id, $title, $content, $class = '') {
?>
<div id="<?= $id ?>" class="modal_custom <?= $class ?>" style="display:none;">
    <div id="<?= $id ?>_header" class="modal_header">
        <span><?= $title ?></span>
        <span onclick="minimizzaModal('<?= $id ?>')" class="modal_min">_</span>
        <span onclick="document.getElementById('<?= $id ?>').style.display='none'" class="modal_close">✕</span>
    </div>
    <div class="modal_body">
        <?= $content ?>
    </div>
</div>
<?php } ?>
