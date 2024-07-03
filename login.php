<?php
session_start();
include 'config.php'; // Assurez-vous que ce fichier contient les informations de connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Connexion à la base de données
    $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

    // Vérification de la connexion
    if ($conn->connect_error) {
        die("Connexion échouée : " . $conn->connect_error);
    }

    // Préparation de la requête SQL
    $stmt = $conn->prepare("SELECT id, username, password FROM utilisateurs WHERE username = ?");
    if ($stmt === false) {
        die("Erreur de préparation de la requête : " . $conn->error);
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // Vérification si l'utilisateur existe
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $db_username, $hashed_password);
        $stmt->fetch();

        // Vérification du mot de passe
        if (password_verify($password, $hashed_password)) {
            // Mot de passe correct
            $_SESSION['user_id'] = $user_id; // Stocker l'ID de l'utilisateur en session est souvent plus sûr que le nom d'utilisateur
            $_SESSION['username'] = $db_username;
            $stmt->close();
            $conn->close();
            header('Location: accueil.php');
            exit();
        } else {
            // Mot de passe incorrect
            echo "Identifiants incorrects.";
        }
    } else {
        // Utilisateur non trouvé
        echo "Identifiants incorrects.";
    }

    // Fermeture de la connexion
    $stmt->close();
    $conn->close();
}
?>
