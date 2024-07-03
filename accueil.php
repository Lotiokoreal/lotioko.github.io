<?php
//PHP c'est douleureux
session_start();

$servername = "localhost";
$username = "root";
$password = "rootaccess1568";
$dbname = "sitekenan";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("La connexion a échoué. Veuillez réessayer plus tard.");
}

$stmt = $conn->prepare("SELECT content FROM site_content WHERE section = ?");
$section = 'main_text';
$stmt->bind_param("s", $section);
$stmt->execute();
$stmt->bind_result($main_text);
$stmt->fetch();
$stmt->close();$stmt = $conn->prepare("SELECT COUNT(*) FROM connexions WHERE login_time >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$stmt->execute();
$stmt->bind_result($connexions_7j);
$stmt->fetch();
$stmt->close();

$page = basename($_SERVER['PHP_SELF']);
$stmt = $conn->prepare("INSERT INTO page_clicks (page) VALUES (?)");
$stmt->bind_param("s", $page);
$stmt->execute();
$stmt->close();



$stmt = $conn->prepare("SELECT id, username, email FROM utilisateurs");
$stmt->execute();
$users = $stmt->get_result();
$stmt->close();



if (empty($main_text)) {
    $main_text = "Texte non trouvé.";
}

if (isset($_POST['update_text']) && isset($_SESSION['username']) && $_SESSION['username'] == 'Lotioko') {
    $new_text = $_POST['main_text'];

    $stmt = $conn->prepare("UPDATE site_content SET content = ? WHERE section = ?");
    $stmt->bind_param("ss", $new_text, $section);

    if ($stmt->execute() === TRUE) {
        $main_text = $new_text;
    } else {
        echo "Erreur de mise à jour. Veuillez réessayer plus tard.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Kenan</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="img/logoJeanMonnet.png" rel="icon" type="image/png" />
</head>
<body>
<header>
    <div class="container header-left">
        <h1>Bienvenue sur mon site</h1>
    </div>
    <div class="container header-right">
        <?php if(isset($_SESSION['username'])): ?>
            <p>Bonjour, <?php echo htmlspecialchars($_SESSION['username']); ?> !</p>
        <?php else: ?>
            <p></p>
        <?php endif; ?>
    </div>
    <div class="logo-container">
        <div class="logo">
            <img src="img/logoJeanMonnet.png" alt="Logo du lycée Jean Monnet à Foulayronnes" href="https://lp-jean-monnet.fr/nos-formations/">
        </div>
</header>
<nav>
    <a href="accueil.php" class="navBack">Accueil</a>
    <a href="apropos.php" class="navBack">À propos</a>
    <a href="contact.php" class="navBack">Contact</a>
    <?php if(!isset($_SESSION['username'])): ?>
        <a href="html/inscription.html" class="navBack">S'inscrire</a>
        <a href="html/login.html" class="navBack">Connexion</a>
    <?php endif; ?>
    <?php if(isset($_SESSION['username'])): ?>
        <a href="logout.php" class="navBack">Déconnexion</a>
        <?php if($_SESSION['username'] == 'Lotioko'): ?>
            <a href="dashboard.php" class="navBack">Dashboard</a>
        <?php endif; ?>
    <?php endif; ?>
</nav>
<main>
    <div class="container3">
        <h2>Activité : Création de Site WEB</h2>
        <p>
            <?php echo nl2br(htmlspecialchars($main_text)); ?>
        </p>
        <?php if(isset($_SESSION['username']) && $_SESSION['username'] == 'Lotioko'): ?>
            <div class="admin-content">
                <h3>Modifier le contenu</h3>
                <form method="post">
                    <textarea name="main_text" rows="10" cols="50"><?php echo htmlspecialchars($main_text); ?></textarea>
                    <br>
                    <button type="submit" name="update_text">Mettre à jour</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
    <aside class="right-article">
        <h3>Réalisé à l'ESIEA par</h3>
        <h3>CROIZE Kenan</h3>
        <h4>Éleve du Lycée Jean-Monnet</h4>
        <a href="https://www.esiea.fr/" target="_blank">
            <img src="https://www.esiea.fr/wp-content/themes/esiea/img/logo-esiea.svg" alt="logo ESIEA" loading="lazy" width="auto" height="80">
        </a>
        <a href="https://lp-jean-monnet.fr/" target="_blank">
            <img src="img/logoJeanMonnet2.png" alt="logo Lycée Jean-Monnet" loading="lazy" width="auto" height="112">
        </a>
    </aside>
    <button id="chatWithAIButton" class="chat-button">Chat</button>
    <div id="chatForm" class="chat-form">
        <div class="chat-header">
            <span>Chat avec AI</span>
            <button id="closeChatForm" class="close-button">&times;</button>
        </div>
        <div class="chat-body">
            <div id="chatOutput"></div>
            <textarea id="userInput" rows="3" placeholder="Posez votre question ici..."></textarea>
            <button id="sendButton" class="send-button">Envoyer</button>
        </div>
    </div>
</main>
<footer>
    <div class="container2">
        <p>© 2024 Tous droits réservés</p>
    </div>
</footer>
<script src="javascript/sourisnav.js"></script>
<script src="javascript/main.js"></script>
<script src="javascript/dropdown.js"></script>
<script src="javascript/chatbot.js"></script>
</body>
</html>
