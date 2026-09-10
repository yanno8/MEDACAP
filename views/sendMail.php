<?php 

function sendMailRegisterUser($to, $subject, $message) {
    //$cc = 'lgonin@cfao.com, aybokally@cfao.com, ysonwa-ext@cfao.com'; // Adresses des destinataires en copie
    
    // Ajoutez un nom devant l'adresse e-mail
    $fromName = 'CFAO Mobility Academy'; // Remplacez par le nom que vous souhaitez afficher
    $fromEmail = 'helpdeskcm@cfao.com'; // Adresse e-mail de l'expéditeur

    $headers =  'From: ' . $fromName . ' <' . $fromEmail . '>' . "\r\n" .
                'Reply-To: ysonwa-ext@cfao.com'. "\r\n" .
                //'Cc: ' . $cc . "\r\n" .
                'X-Mailer: PHP/' . phpversion() . "\r\n" .
                'MIME-Version: 1.0' . "\r\n" .
                'Content-Type: text/html; charset=UTF-8' . "\r\n";
    
    mail($to, $subject, $message, $headers);
}

function sendMailRegisterTraining($to, $subject, $message, $trainer) {
    $cc = 'lgonin@cfao.com, aybokally@cfao.com, ysonwa-ext@cfao.com, ceyinga-ext@cfao.com, '.$trainer; // Adresses des destinataires en copie
    
    // Ajoutez un nom devant l'adresse e-mail
    $fromName = 'CFAO Mobility Academy'; // Remplacez par le nom que vous souhaitez afficher
    $fromEmail = 'helpdeskcm@cfao.com'; // Adresse e-mail de l'expéditeur

    $headers =  'From: ' . $fromName . ' <' . $fromEmail . '>' . "\r\n" .
                'Reply-To: ceyinga-ext@cfao.com'. "\r\n" .
                'Cc: ' . $cc . "\r\n" .
                'X-Mailer: PHP/' . phpversion() . "\r\n" .
                'MIME-Version: 1.0' . "\r\n" .
                'Content-Type: text/html; charset=UTF-8' . "\r\n";
    
    mail($to, $subject, $message, $headers);
}

function sendMailSelectDone($subject, $message) {
    $to = 'ysonwa-ext@cfao.com'; // Adresse e-mail du destin
    //$cc = 'lgonin@cfao.com, aybokally@cfao.com'; // Adresses des destinataires en copie
    
    // Ajoutez un nom devant l'adresse e-mail
    $fromName = 'CFAO Mobility Academy'; // Remplacez par le nom que vous souhaitez afficher
    $fromEmail = 'helpdeskcm@cfao.com'; // Adresse e-mail de l'expéditeur

    $headers =  'From: ' . $fromName . ' <' . $fromEmail . '>' . "\r\n" .
                'Reply-To: ysonwa-ext@cfao.com'. "\r\n" .
                //'Cc: ' . $cc . "\r\n" .
                'X-Mailer: PHP/' . phpversion() . "\r\n" .
                'MIME-Version: 1.0' . "\r\n" .
                'Content-Type: text/html; charset=UTF-8' . "\r\n";
    
    mail($to, $subject, $message, $headers);
}

function sendMailQCMDone($to, $subject, $message) {
    $cc = 'lgonin@cfao.com, aybokally@cfao.com, ysonwa-ext@cfao.com'; // Adresses des destinataires en copie
    
    // Ajoutez un nom devant l'adresse e-mail
    $fromName = 'CFAO Mobility Academy'; // Remplacez par le nom que vous souhaitez afficher
    $fromEmail = 'helpdeskcm@cfao.com'; // Adresse e-mail de l'expéditeur

    $headers =  'From: ' . $fromName . ' <' . $fromEmail . '>' . "\r\n" .
                'Reply-To: ysonwa-ext@cfao.com'. "\r\n" .
                'Cc: ' . $cc . "\r\n" .
                'X-Mailer: PHP/' . phpversion() . "\r\n" .
                'MIME-Version: 1.0' . "\r\n" .
                'Content-Type: text/html; charset=UTF-8' . "\r\n";
    
    mail($to, $subject, $message, $headers);
}