<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

$id = $_GET['id'];
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

<form method="POST" action="/pages/cart.php">
    <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
    <input type="number" name="quantity" value="1" min="1">
    <button type="submit">Add to Cart</button>
</form>

<?php require_once '../includes/footer.php'; ?>
