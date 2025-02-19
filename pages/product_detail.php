<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid product ID.");
}
$id = (int) $_GET['id'];

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

<?php if (isset($_SESSION['user']) && ($article['user_id'] == $_SESSION['user']['id'] || $_SESSION['user']['role'] === 'admin')): ?>
    <a href="product_edit.php?id=<?= $article['id'] ?>" class="btn btn-primary">Edit Product</a>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
