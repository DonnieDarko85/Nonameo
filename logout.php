<?php
require('includes/required.php');
session_start(); // <- solo se non è già presente in required.php
$handleDBConnection = gdrcd_connect();

gdrcd_query("UPDATE personaggio SET ora_uscita = NOW() WHERE nome='" . gdrcd_filter('in', $_SESSION['login']) . "'");

$nome_pg = gdrcd_filter('out', $_SESSION['login']);

// Frasi grottesche-divertenti con traduzione
$logout_frasi = [
    [
        'stramba' => "$nome_pg s’è perso tra le ombre de ‘na calle storta. <br>Se te ga voja de ripescalo…",
        'tradotta' => "$nome_pg s'è perso tra le ombre di una viuzza storta. <br>Se lo vuoi ripescare…"
    ],
    [
        'stramba' => "$nome_pg se ga fato ciapar da un canaleto sospeto. <br>Se ga voja de tornar…",
        'tradotta' => "$nome_pg s'è fatto inghiottire da un canaletto sospetto. <br>Se ha voglia di tornare…"
    ],
    [
        'stramba' => "$nome_pg se ga disfà come un pupeto bagnà. <br>Ma te lo poi rimetar insieme…",
        'tradotta' => "$nome_pg si è disfatto come un fantoccio bagnato. <br>Ma lo puoi ricucire…"
    ],
    [
        'stramba' => "$nome_pg ga dito “torno subito” e dopo no l'è più rivà. <br>Vuo provar a riportarlo?",
        'tradotta' => "$nome_pg ha detto “torno subito” e non l’ha più visto nessuno. <br>Vuoi provare a riportarlo?"
    ],
    [
        'stramba' => "$nome_pg l’è sparìo, ma l’odore el xe restà. <br>Se te manca…",
        'tradotta' => "$nome_pg è sparito, ma l’odore c'è ancora. <br>Se ti manca…"
    ],
    [
        'stramba' => "$nome_pg ga sentìo un richiamo strano… e l’ha seguìo. <br>Se te lo vol far torna’…",
        'tradotta' => "$nome_pg ha sentito un richiamo strano… e l’ha seguito. <br>Se lo vuoi riacciuffare,"
    ],
    [
        'stramba' => "$nome_pg se n’è ndà a dar da manjar ai pìssani mutanti. <br>Se torna, sarà pezo.",
        'tradotta' => "$nome_pg è andato a dare da mangiare ai piccioni mutanti. <br>Se torna, sarà peggio."
    ],
    [
        'stramba' => "$nome_pg ga fato un patto co’ ‘na ombra e adesso l’è sparìo. <br>Se te vol romper el patto…",
        'tradotta' => "$nome_pg ha fatto un patto con un'ombra e ora è svanito. <br>Se vuoi rompere il patto..."
    ],
    [
        'stramba' => "$nome_pg ga perso la mappa… e la testa. <br>Se te vol rimetterlo in strada…",
        'tradotta' => "$nome_pg ha perso la mappa… e la testa. <br>Se lo vuoi rimettere in strada…"
    ],
        [
        'stramba' => "$nome_pg l’è finìo in un bàcaro stregà. <br>No se sa se torna sòbrio. <br>Se te vol provàr a sveiarlo...",
        'tradotta' => "$nome_pg è finito in un’osteria stregata. <br>Non si sa se tornerà sobrio. <br>Se vuoi provare a svegliarlo..."
    ],
    [
        'stramba' => "$nome_pg l’ha seguìo un gàto coi oci che lu ipnotizava. <br>Adesso el dorme in un sottoportego. <br>Se lo vol tirar fora...",
        'tradotta' => "$nome_pg ha seguito un gatto dagli occhi ipnotici. Ora dorme sotto un portico. <br>Se lo vuoi tirare fuori..."
    ],
    [
        'stramba' => "$nome_pg l’è stà ciapà da ‘na maschera parlante. <br>Se el torna, no sarà più lu. <br>Se te ghe vol rompèr el incanto...",
        'tradotta' => "$nome_pg è stato catturato da una maschera parlante. <br>Se torna, non sarà più lui. <br>Se vuoi rompere l’incanto..."
    ],
    [
        'stramba' => "$nome_pg el se xe incantà a guardar l'acqua... <br>e l’acqua lo ga ciapà. <br>Se te ghe vol tiràr un remo...",
        'tradotta' => "$nome_pg si è incantato a guardare l’acqua… <br>e l’acqua l’ha preso. <br>Se vuoi tirargli un remo..."
    ],
    [
        'stramba' => "$nome_pg l’è andà drìo a ‘na barcheta senza remi. <br>Forse el torna, forse no. <br>Se te ghe vol mandàr un remo...",
        'tradotta' => "$nome_pg ha seguito una barchetta senza remi. <br>Forse torna, forse no. <br>Se vuoi mandargli un remo..."
    ],
    [
        'stramba' => "$nome_pg ga zughè a carte co’ ‘na vecia che no moriva mai. <br>Mo’ no se le vede più. <br>Se te vol rompèr el tavolo...",
        'tradotta' => "$nome_pg ha giocato a carte con una vecchia che non moriva mai. <br>Ora non si vedono più. <br>Se vuoi rovesciare il tavolo..."
    ],
    [
        'stramba' => "$nome_pg el xe entrà in un porton che prima no ghe gera. <br>El porton dopo el xe sparìo. <br>Se te vol trovàr la ciaveta...",
        'tradotta' => "$nome_pg è entrato in un portone che prima non c’era. <br>Il portone dopo è sparito. <br>Se vuoi trovare la chiave..."
    ],
    [
        'stramba' => "$nome_pg l’ha trovà ‘na lettera che parlava. <br>Adesso i parla in do, ma solo tra lori. <br>Se te vol leggèr anca ti...",
        'tradotta' => "$nome_pg ha trovato una lettera che parlava. <br>Ora parlano in due, ma solo tra loro. <br>Se vuoi leggere anche tu..."
    ],
    [
        'stramba' => "$nome_pg l’è cascà drento a un pozzo co’ l’eco. <br>Ma l’eco rispondeva. <br>Se te ghe vol urlàr forte...",
        'tradotta' => "$nome_pg è caduto in un pozzo con l’eco. <br>Ma l’eco rispondeva. <br>Se vuoi urlargli forte..."
    ]
];


// Scelgo una frase casuale
$frase_scelta = $logout_frasi[array_rand($logout_frasi)];

// Inserisco il nome del personaggio nella frase
$frase_stramba = str_replace('$nome_pg', $nome_pg, $frase_scelta['stramba']);
$frase_tradotta = str_replace('$nome_pg', $nome_pg, $frase_scelta['tradotta']);
?>

<html>
<head>
    <meta http-equiv="Content-Type" content='text/html; charset=utf-8'>
    <link rel="stylesheet" href="themes/<?php echo $PARAMETERS['themes']['current_theme']; ?>/main.css" type='text/css'>
    <link rel="shortcut icon" href="imgs/favicon.ico"/>
    <title>LOGOUT - Carnem et Umbrae</title>
</head>

<body class="logout_body">
<div class="logout_background">
    <div class="logout_box">
        <span class="logout_text">
            <span class="frase-container">
                <span class="frase-stramba"><?php echo $frase_stramba; ?></span>
                <span class="frase-italiano"><?php echo $frase_tradotta; ?></span>
                <span class="logout_text">
            <a href="index.php" class="logout_link">ciàca qua.</a>
        </span>
            </span>
        </span>
    </div>
</div>
</body>
</html>

<?php
// Chiudo la connessione
gdrcd_close_connection($handleDBConnection);
session_unset();
session_destroy();
?>
