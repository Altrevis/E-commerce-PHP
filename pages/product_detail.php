<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

// Vérification et nettoyage de l'ID
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET['id'];

// Récupération de l'article
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch();

if (!$article) {
    die("Article not found.");
}
?>

<h1><?= htmlspecialchars($article['name']) ?></h1>
<p><?= nl2br(htmlspecialchars($article['description'])) ?></p>
<img src="<?= htmlspecialchars($article['image_url']) ?>" alt="<?= htmlspecialchars($article['name']) ?>" width="200">

<!-- Affichage du prix -->
<p><strong>Price: $<?= number_format((float) $article['price'], 2) ?></strong></p>

<!-- Formulaire d'ajout au panier -->
<form method="POST" action="../pages/cart.php">
    <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
    <input type="number" name="quantity" value="1" min="1" required>
    <button type="submit">Add to Cart</button>
</form>

<!-- Bouton Edit Product pour les admins -->
<?php if (!empty($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
    <a href="product_edit.php?id=<?= $article['id'] ?>" class="edit-button">Edit Product</a>
<?php endif; ?>

<!-- Styles CSS -->
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
