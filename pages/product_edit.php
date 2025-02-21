<?php
session_start();  // Démarre la session PHP

require_once '../includes/db.php';  // Inclut la connexion à la base de données
require_once '../includes/header.php';  // Inclut le fichier d'en-tête (généralement pour le menu et les liens)

if (!isset($_SESSION['user'])) {  // Vérifie si l'utilisateur est connecté
    header('Location: /pages/login.php');  // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {  // Vérifie si l'ID est présent dans l'URL et est valide
    header("Location: index.php");  // Redirige vers la page d'accueil en cas d'ID invalide
    exit;
}

$id = (int) $_GET['id'];  // Récupère l'ID de l'article et le convertit en entier

// Prépare la requête pour récupérer l'article à partir de l'ID
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch();  // Récupère les informations de l'article

// Vérifie si l'article existe et si l'utilisateur est autorisé à le modifier
if (!$article || ($article['user_id'] != $_SESSION['user']['id'] && $_SESSION['user']['role'] !== 'admin')) {
    die("You are not authorized to edit this product.");  // Si l'utilisateur n'est pas le propriétaire de l'article ou un administrateur, il n'est pas autorisé à modifier l'article
}

// Mise à jour du produit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {  // Si le formulaire de mise à jour est soumis
    $name = $_POST['name'];
    $description = $_POST['description'];
    $image_url = $_POST['image_url'];
    $price = $_POST['price'];  // Récupère le prix

    // Prépare et exécute la requête de mise à jour
    $stmt = $pdo->prepare("UPDATE articles SET name = ?, description = ?, image_url = ?, price = ? WHERE id = ?");
    $stmt->execute([$name, $description, $image_url, $price, $id]);

    header("Location: product_detail.php?id=$id");  // Redirige vers la page de détail du produit
    exit;
}

// Suppression du produit
if (isset($_POST['delete_article']) && $_POST['delete_article'] === '1') {  // Si l'utilisateur a soumis la demande de suppression
    $article_id = $_POST['article_id'];  // Récupère l'ID de l'article à supprimer
    try {
        // Supprime l'article de la table stock
        $stmt = $pdo->prepare("DELETE FROM stock WHERE article_id = ?");
        $stmt->execute([$article_id]);
        
        // Supprime l'article de la table articles
        $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
        $stmt->execute([$article_id]);
        
        // Affiche un message de succès après suppression
        $articles = $pdo->query("SELECT * FROM articles")->fetchAll();
        echo "Article deleted successfully!";
    } catch (PDOException $e) {
        // Si une erreur survient, l'affiche
        echo "Error deleting article: " . $e->getMessage();
    }
}
?>

<h1>Edit Product</h1>

<!-- Formulaire pour éditer l'article -->
<form method="POST">
    <input type="text" name="name" value="<?= htmlspecialchars($article['name']) ?>" required>
    <!-- Champ pour le nom de l'article -->
    <textarea name="description"><?= htmlspecialchars($article['description']) ?></textarea>
    <!-- Champ pour la description -->
    <input type="text" name="image_url" value="<?= htmlspecialchars($article['image_url']) ?>">
    <!-- Champ pour l'URL de l'image -->
    <input type="number" name="price" value="<?= htmlspecialchars($article['price']) ?>" step="0.01" required>
    <!-- Champ pour le prix -->

    <button type="submit" name="update">Update</button> <!-- Bouton pour soumettre la mise à jour -->
</form>

<!-- Formulaire pour supprimer l'article -->
<form method="POST" action="" style="display:inline;"
    onsubmit="return confirm('Are you sure you want to delete this product?');">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>"> <!-- Token CSRF pour la sécurité -->
    <input type="hidden" name="article_id" value="<?= $article['id'] ?>"> <!-- ID de l'article à supprimer -->
    <input type="hidden" name="delete_article" value="1"> <!-- Indique qu'il s'agit d'une demande de suppression -->
    <button type="submit">Delete</button> <!-- Bouton pour soumettre la suppression -->
</form>