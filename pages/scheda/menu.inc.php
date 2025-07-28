<?php

$pg = gdrcd_filter('out', $_REQUEST['pg']);
$me = gdrcd_filter('out',$_SESSION['login']);
$permessi  = gdrcd_filter('out',$_SESSION['permessi']);

# Modifica
if (($pg == $me) || ($permessi >= GUILDMODERATOR)) { ?>
        <a href="main.php?page=scheda_modifica&pg=<?=$pg;?>">
            <?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['menu']['update']); ?>
        </a>
<?php } ?>
   
    <a href="#" class="menu-link" data-url="pages/scheda_profilo.inc.php?pg=<?=urlencode($pg);?>">
        <?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['menu']['main']); ?>
    </a>
    <a href="#" class="menu-link" data-url="pages/scheda_skill.inc.php?pg=<?=urlencode($pg);?>">
        <?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['menu']['skill']); ?>
    </a>
    <a href="#" class="menu-link" data-url="pages/scheda_storia.inc.php?pg=<?=urlencode($pg);?>">
        <?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['menu']['background']); ?>
    </a>



    <!-- ROLES -->
    <!-- Se maggiore di moderatore -->
<?php if ($permessi >= MODERATOR) { ?>

    <!-- AMMINISTRA -->
    <a href="main.php?page=scheda_gst&pg=<?=$pg;?>">
        <?php echo gdrcd_filter('out', $MESSAGE['interface']['sheet']['menu']['gst']); ?>
    </a>
<?php } ?>

<script>

    $('.menu-link').on('click', function(e) {
        e.preventDefault();
        const url = $(this).data('url');
        console.log('Caricamento URL:', url);
        caricaContenuto(url);
    });

    function caricaContenuto(url) {
        $('#contenitoreScheda').empty();
        $('#contenitoreScheda').load(url); 
    }

</script>

