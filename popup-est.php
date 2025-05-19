<?php
require 'header.inc.php';

echo '<div class="popup">';

if (!empty($_GET['page'])) {
    $file = __DIR__
        . DIRECTORY_SEPARATOR
        . 'pages'
        . DIRECTORY_SEPARATOR
        . 'homepage'
        . DIRECTORY_SEPARATOR
        . $_GET['page']
        . '.inc.php';

    if (file_exists($file)) {
        gdrcd_load_modules(gdrcd_filter('include', $file));
    } else {
        echo "<div class='warning'>Pagina non trovata: <code>$file</code></div>";
    }
} else {
    echo $MESSAGE['interface']['layout_not_found'];
}

echo '</div>';

require 'footer.inc.php';
