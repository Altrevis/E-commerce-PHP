<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user'])) {
    header('Location: /pages/login.php');
    exit;
}

$user_id = $_SESSION['user']['id'];

// Si l'utilisateur est admin, il peut voir tous les produits
if ($_SESSION['user']['role'] === 'admin') {
    $stmt = $pdo->prepare("SELECT * FROM articles");
} else {
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE user_id = ?");
    $stmt->execute([$user_id]);
}

$articles = $stmt->fetchAll();
?>

<h1>Edit Your Products</h1>

<?php if (empty($articles)): ?>
    <p>You have no products yet. <a href="product_create.php">Create one here</a></p>
<?php else: ?>
    <?php foreach ($articles as $article): ?>
        <form method="POST">
            <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
            <input type="text" name="name" value="<?= htmlspecialchars($article['name']) ?>" required>
            <textarea name="description"><?= htmlspecialchars($article['description']) ?></textarea>
            <input type="text" name="image_url" value="<?= htmlspecialchars($article['image_url']) ?>">
            <button type="submit">Update</button>
        </form>
        <hr>
    <?php endforeach; ?>
<?php endif; ?>

