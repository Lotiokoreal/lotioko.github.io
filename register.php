<?php
include 'config.php'; // Fichier contenant les informations de connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Vérification des entrées
    if (empty($username) || empty($password) || empty($email)) {
        die("Tous les champs doivent être remplis.");
    }

    // Hachage sécurisé du mot de passe
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Connexion à la base de données
    $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

    // Vérification de la connexion
    if ($conn->connect_error) {
        die("Connexion échouée : " . $conn->connect_error);
    }

    // Préparation de la requête SQL avec une insertion sécurisée
    $stmt = $conn->prepare("INSERT INTO utilisateurs (username, password, email) VALUES (?, ?, ?)");
    if ($stmt === false) {
        die("Erreur de préparation de la requête : " . $conn->error);
    }
    $stmt->bind_param("sss", $username, $hashed_password, $email);

    // Exécution de la requête et vérification
    if ($stmt->execute()) {
        header('Location: accueil.php');
    } else {
        echo "Erreur lors de l'inscription : " . $stmt->error;
    }

    // Fermeture de la connexion
    $stmt->close();
    $conn->close();
}
?>
