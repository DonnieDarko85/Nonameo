<?php

require ('../../includes/required.php');
$handleDBConnection = gdrcd_connect();

$feedback = '';

if (!empty($_POST['email'])) {
    $result = gdrcd_query("SELECT nome, email FROM personaggio", 'result');

    while ($row = gdrcd_query($result, 'assoc')) {
        if (gdrcd_password_check($_POST['email'], $row['email'])) {
            $pass = gdrcd_genera_pass();
            $hasReset = gdrcd_query("UPDATE personaggio SET pass = '" . gdrcd_encript($pass) . "' WHERE nome = '" . gdrcd_filter('in', $row['nome']) . "' LIMIT 1");

            if ($hasReset) {
                $subject = gdrcd_filter('out', $MESSAGE['register']['forms']['mail']['sub'] . ' ' . $PARAMETERS['info']['site_name']);
                $text = gdrcd_filter('out', $MESSAGE['register']['forms']['mail']['text'] . ': ' . $pass);

                $hasEmailSent = mail($_POST['email'], $subject, $text, 'From: ' . $PARAMETERS['info']['webmaster_email']);

                if ($hasEmailSent) {
                    $feedback = gdrcd_filter('out', $MESSAGE['homepage']['resetOK']);
                } else {
                    $feedback = gdrcd_filter('out', $MESSAGE['warning']['cant_do']);
                }
            } else {
                $feedback = gdrcd_filter('out', $MESSAGE['warning']['cant_do']);
            }

            break; 
        } else {
            $feedback = gdrcd_filter('out', $MESSAGE['warning']['not_found']);
        }
    }
}

echo $feedback;