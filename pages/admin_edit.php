<?php
// Démarre une session PHP
session_start();

// Inclusion du fichier de connexion à la base de données
require_once '../includes/db.php';

// Vérifie si l'utilisateur est connecté et a le rôle "admin"
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    // Redirige vers la page de connexion si l'utilisateur n'est pas admin
    header('Location: /pages/login.php');
    exit;
}

// Vérifie si un article a été spécifié dans l'URL via GET
if (!isset($_GET['article_id'])) {
    die("No article specified."); // Affiche un message d'erreur et stoppe l'exécution
}

// Récupère l'ID de l'article à modifier
$article_id = $_GET['article_id'];

// Prépare une requête SQL pour récupérer l'article en fonction de son ID
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$article_id]);
$article = $stmt->fetch();

// Vérifie si l'article existe dans la base de données
if (!$article) {
    die("Article not found."); // Stoppe l'exécution si l'article n'existe pas
}

// Vérifie si un token CSRF existe en session, sinon en génère un
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Génère un token CSRF sécurisé
}

// Vérifie si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifie la validité du token CSRF pour éviter les attaques CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token."); // Stoppe l'exécution si le token est invalide
    }

    // Récupère et sécurise les données du formulaire
    $name = $_POST['name'];
    $content = $_POST['content'];

    // Prépare et exécute la requête SQL pour mettre à jour l'article
    $stmt = $pdo->prepare("UPDATE articles SET name = ?, content = ? WHERE id = ?");
    $stmt->execute([$name, $content, $article_id]);

    // Redirige vers la page d'administration avec un message de succès
    header("Location: /pages/admin.php?success=article_updated");
    exit;
}
?>

<!-- Formulaire d'édition de l'article -->
<h1>Edit Article</h1>
<form method="POST">
    <!-- Champ caché pour le token CSRF -->
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <!-- Champ pour le titre de l'article -->
    <label>Title:</label>
    <input type="text" name="name" value="<?= htmlspecialchars($article['name']) ?>" required>

    <!-- Champ pour le contenu de l'article -->
    <label>Content:</label>
    <textarea name="content" required><?= htmlspecialchars($article['content']) ?></textarea>

    <!-- Bouton pour soumettre les modifications -->
    <button type="submit">Save Changes</button>
</form>