<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['username'] != 'Lotioko') {
    header("Location: accueil.php");
    exit();
}

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "rootaccess1568";
$dbname = "sitekenan";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("La connexion a échoué. Veuillez réessayer plus tard.");
}

// Récupérer les statistiques selon la section sélectionnée
$section = isset($_GET['section']) ? $_GET['section'] : 'connexion';

if ($section == 'connexion') {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM connexions WHERE login_time >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $stmt->execute();
    $stmt->bind_result($connexions_7j);
    $stmt->fetch();
    $stmt->close();
} elseif ($section == 'clicks') {
    $stmt = $conn->prepare("SELECT page, COUNT(*) as clicks FROM page_clicks WHERE click_time >= DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY page");
    $stmt->execute();
    $result = $stmt->get_result();
    $page_clicks = [];
    while ($row = $result->fetch_assoc()) {
        $page_clicks[] = $row;
    }
    $stmt->close();
} elseif ($section == 'accounts') {
    $stmt = $conn->prepare("SELECT id, username, email FROM utilisateurs");
    $stmt->execute();
    $users = $stmt->get_result();
    $stmt->close();
}

// Supprimer un utilisateur
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];

        $stmt = $conn->prepare("DELETE FROM utilisateurs WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
        header("Location: dashboard.php?section=accounts");
        exit();
    } elseif (isset($_POST['edit_user'])) {
        $user_id = $_POST['user_id'];
        $new_username = $_POST['new_username'];
        $new_email = $_POST['new_email'];
        $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE utilisateurs SET username = ?, email = ?, password = ? WHERE id = ?");
        $stmt->bind_param("sssi", $new_username, $new_email, $new_password, $user_id);
        $stmt->execute();
        $stmt->close();
        header("Location: dashboard.php?section=accounts");
        exit();
    } elseif (isset($_POST['add_user'])) {
        $new_username = $_POST['new_username'];
        $new_email = $_POST['new_email'];
        $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO utilisateurs (username, password, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $new_username, $new_password, $new_email);
        $stmt->execute();
        $stmt->close();
        header("Location: dashboard.php?section=accounts");
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link href="img/logoJeanMonnet.png" rel="icon" type="image/png" />
</head>
<body>
<header>
    <div class="container header-left">
        <h1>Dashboard</h1>
    </div>
    <div class="container header-right">
        <?php if(isset($_SESSION['username'])): ?>
            <p>Bonjour, <?php echo htmlspecialchars($_SESSION['username']); ?> !</p>
        <?php else: ?>
            <p></p>
        <?php endif; ?>
    </div>
</header>
<nav>
    <a href="accueil.php" class="navBack">Accueil</a>
    <a href="apropos.php" class="navBack">À propos</a>
    <a href="contact.php" class="navBack">Contact</a>
    <a href="logout.php" class="navBack">Déconnexion</a>
</nav>
<main>
    <div class="container3">
        <h2>Bienvenue sur le dashboard, Lotioko!</h2>
        <p>Voila le Dashboard ou "Tableau de Bord" en Français.</p>

        <nav class="main-nav">
            <a href="dashboard.php?section=connexion">Statistiques de Connexion</a>
            <a href="dashboard.php?section=clicks">Statistiques des Clics</a>
            <a href="dashboard.php?section=accounts">Gestion des Comptes</a>
        </nav>

        <?php if ($section == 'connexion'): ?>
            <div class="admin-content">
                <h3>Statistiques des connexions</h3>
                <p>Nombre de connexions des 7 derniers jours: <?php echo $connexions_7j; ?></p>
            </div>
        <?php elseif ($section == 'clicks'): ?>
            <div class="admin-content">
                <h3>Statistiques des clics</h3>
                <ul>
                    <?php foreach ($page_clicks as $click): ?>
                        <li><?php echo htmlspecialchars($click['page']) . ": " . $click['clicks'] . " clics"; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php elseif ($section == 'accounts'): ?>
            <div class="admin-content">
                <h3>Gestion des comptes utilisateurs</h3>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Nom d'utilisateur</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                    <?php while($user = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" name="delete_user">Supprimer</button>
                                </form>
                                <button onclick="toggleEditForm(<?php echo $user['id']; ?>)">Modifier</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
                <div id="edit-form-container" style="display:none;">
                    <h4>Modifier les informations d'un utilisateur</h4>
                    <form id="edit-form" method="post">
                        <input type="hidden" id="edit_user_id" name="user_id" value="">
                        <label for="new_username">Nom d'utilisateur :</label>
                        <input type="text" id="new_username" name="new_username" required>
                        <label for="new_email">Email :</label>
                        <input type="email" id="new_email" name="new_email" required>
                        <label for="new_password">Nouveau mot de passe :</label>
                        <input type="password" id="new_password" name="new_password" required>
                        <button type="submit" name="edit_user">Enregistrer</button>
                    </form>
                </div>
                <h4>Ajouter un utilisateur</h4>
                <form method="post">
                    <label for="new_username">Nom d'utilisateur :</label>
                    <input type="text" id="new_username" name="new_username" required>
                    <label for="new_email">Email :</label>
                    <input type="email" id="new_email" name="new_email" required>
                    <label for="new_password">Mot de passe :</label>
                    <input type="password" id="new_password" name="new_password" required>
                    <button type="submit" name="add_user">Ajouter</button>
                </form>
            </div>
            <script src="javascript/dashboard.js"></script>
        <?php endif; ?>
    </div>
</main>
<footer>
    <div class="container2">
        <p>© 2024 Tous droits réservés</p>
    </div>
</footer>
</body>
</html>
