<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user'])) {
    header('Location: /pages/login.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch();

if (!$article || ($article['user_id'] != $_SESSION['user']['id'] && $_SESSION['user']['role'] !== 'admin')) {
    die("You are not authorized to edit this product.");
}

// Mise à jour du produit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $image_url = $_POST['image_url'];
    $price = $_POST['price']; // Ajout du prix

    $stmt = $pdo->prepare("UPDATE articles SET name = ?, description = ?, image_url = ?, price = ? WHERE id = ?");
    $stmt->execute([$name, $description, $image_url, $price, $id]);

    header("Location: product_detail.php?id=$id");
    exit;
}

// Suppression du produit
if (isset($_POST['delete_article']) && $_POST['delete_article'] === '1') {
    $article_id = $_POST['article_id'];
    try {
        // Delete records from stock table
        $stmt = $pdo->prepare("DELETE FROM stock WHERE article_id = ?");
        $stmt->execute([$article_id]);
        
        // Delete the article from the database
        $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
        $stmt->execute([$article_id]);
        
        // Refresh the articles list
        $articles = $pdo->query("SELECT * FROM articles")->fetchAll();
        echo "Article deleted successfully!";
    } catch (PDOException $e) {
        echo "Error deleting article: " . $e->getMessage();
    }
}
?>

<h1>Edit Product</h1>

<form method="POST">
    <input type="text" name="name" value="<?= htmlspecialchars($article['name']) ?>" required>
    <textarea name="description"><?= htmlspecialchars($article['description']) ?></textarea>
    <input type="text" name="image_url" value="<?= htmlspecialchars($article['image_url']) ?>">
    <input type="number" name="price" value="<?= htmlspecialchars($article['price']) ?>" step="0.01" required>
    <!-- Champ prix ajouté -->

    <button type="submit" name="update">Update</button>
</form>

<!-- Bouton Delete -->
<form method="POST" action="" style="display:inline;"
    onsubmit="return confirm('Are you sure you want to delete this product?');">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
    <input type="hidden" name="delete_article" value="1">
    <button type="submit">Delete</button>
</form>