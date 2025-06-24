<?php
session_start();
require_once('../../includes/required.php');

header('Content-Type: application/json');

if (!isset($_POST['pg'], $_POST['what'], $_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'Parametri mancanti.']);
    exit;
}

$pg = gdrcd_filter('in', $_POST['pg']);
$what = gdrcd_filter('num', $_POST['what']);
$action = $_POST['action'];

$is_owner = ($_SESSION['login'] == $pg);
$is_mod = ($_SESSION['permessi'] >= MODERATOR);

if (!$is_owner || !$is_mod) {
    echo json_encode(['success' => false, 'message' => 'Permessi insufficienti.']);
    exit;
}

// Carico grado attuale
$row = gdrcd_query("SELECT grado FROM clgpersonaggioabilita WHERE nome='$pg' AND id_abilita=$what", 'query');
$grado_attuale = isset($row['grado']) ? (int)$row['grado'] : 0;

// Calcolo PX spesi
$abilita_pg = gdrcd_query("SELECT grado FROM clgpersonaggioabilita WHERE nome='$pg'", 'result');
$px_spesi = 0;
while ($row = gdrcd_query($abilita_pg, 'fetch')) {
    $g = (int)$row['grado'];
    $px_spesi += $PARAMETERS['settings']['px_x_rank'] * ($g * ($g + 1) / 2);
}

// PX totali
$pg_data = gdrcd_query("SELECT esperienza FROM personaggio WHERE nome='$pg'", 'query');
$px_totali = (int)$pg_data['esperienza'];
$px_disponibili = $px_totali - $px_spesi;

// ADD
if ($action === 'add') {
    if (!$is_owner) {
        echo json_encode(['success' => false, 'message' => 'Solo il proprietario può aumentare.']);
        exit;
    }
    if ($grado_attuale >= $PARAMETERS['settings']['skills_cap']) {
        echo json_encode(['success' => false, 'message' => 'Grado massimo raggiunto.']);
        exit;
    }

    $costo = $PARAMETERS['settings']['px_x_rank'] * ($grado_attuale + 1);
    if ($costo > $px_disponibili) {
        echo json_encode(['success' => false, 'message' => 'PX insufficienti.']);
        exit;
    }

    if ($grado_attuale == 0) {
        gdrcd_query("INSERT INTO clgpersonaggioabilita (id_abilita, nome, grado) VALUES ($what, '$pg', 1)");
    } else {
        $nuovo_grado = $grado_attuale + 1;
        gdrcd_query("UPDATE clgpersonaggioabilita SET grado = $nuovo_grado WHERE id_abilita = $what AND nome = '$pg'");
    }

    echo json_encode(['success' => true]);
    exit;
}

// SUB
if ($action === 'sub') {
    if (!$is_mod) {
        echo json_encode(['success' => false, 'message' => 'Solo i moderatori possono ridurre.']);
        exit;
    }

    if ($grado_attuale == 0) {
        echo json_encode(['success' => false, 'message' => 'Grado già a zero.']);
        exit;
    }

    if ($grado_attuale == 1) {
        gdrcd_query("DELETE FROM clgpersonaggioabilita WHERE id_abilita = $what AND nome = '$pg' LIMIT 1");
    } else {
        $nuovo_grado = $grado_attuale - 1;
        gdrcd_query("UPDATE clgpersonaggioabilita SET grado = $nuovo_grado WHERE id_abilita = $what AND nome = '$pg'");
    }

    echo json_encode(['success' => true]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Azione non riconosciuta.']);
