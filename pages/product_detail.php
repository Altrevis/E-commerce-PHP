<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php"); // Redirection en cas d'ID invalide
    exit;
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch();

if (!$article) {
    die("Article not found.");
}
?>

<h1><?= htmlspecialchars($article['name']) ?></h1>
<p><?= htmlspecialchars($article['description']) ?></p>
<img src="<?= htmlspecialchars($article['image_url']) ?>" alt="<?= htmlspecialchars($article['name']) ?>" width="200">

<!-- Affichage du prix -->
<p><strong>Price: $<?= number_format($article['price'], 2) ?></strong></p>

<form method="POST" action="../pages/cart.php">
    <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
    <input type="number" name="quantity" value="1" min="1">
    <button type="submit">Add to Cart</button>
</form>

<!-- Bouton Edit Product pour les admins -->
<?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
<a href="product_edit.php?id=<?= $article['id'] ?>" class="edit-button">Edit Product</a>
<?php endif; ?>


<!-- Ajoute ceci dans ton fichier CSS pour que le bouton s'affiche correctement -->
<style>
.edit-button {
    display: inline-block;
    padding: 10px 15px;
    background-color: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    transition: background-color 0.3s;
    margin-top: 10px;
}

.edit-button:hover {
    background-color: #0056b3;
}
</style>