<?php
// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'C:/xampp/PHPMailer/src/Exception.php';
require 'C:/xampp/PHPMailer/src/PHPMailer.php';
require 'C:/xampp/PHPMailer/src/SMTP.php';

// Vérification des données envoyées depuis le formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Instantiation de PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Paramètres SMTP
        $mail->SMTPDebug = 2;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Remplacez par votre serveur SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'nonlolmoi@gmail.com'; // Remplacez par votre email
        $mail->Password = 'abqv glia yhhn nytp'; // Remplacez par votre mot de passe SMTP
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Destinataire de l'email
        $mail->setFrom($email, $name);
        $mail->addAddress('lotiokocroize@gmail.com'); // Remplacez par l'adresse de destination

        // Contenu de l'email
        $mail->isHTML(false); // Si votre email est en texte brut, utilisez false ; true si c’est en HTML
        $mail->Subject = "Nouveau message de contact de $name";
        $mail->Body = "Vous avez reçu un nouveau message de contact:\n\n";
        $mail->Body .= "Nom: $name\n";
        $mail->Body .= "Email: $email\n";
        $mail->Body .= "Message:\n$message";

        // Envoyer l'email
        $mail->send();
        header('Location: accueil.php');
    } catch (Exception $e) {
        echo "<p>Erreur lors de l'envoi du message: {$mail->ErrorInfo}</p>";
    }
}
?>
