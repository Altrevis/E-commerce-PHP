<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

// Fetch user information from the database
$user_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Fetch articles created by the user
$stmt = $pdo->prepare("SELECT * FROM articles WHERE user_id = ?");
$stmt->execute([$user_id]);
$articles = $stmt->fetchAll();
?>

<h1>Profile of <?= htmlspecialchars($user['username']) ?></h1>

<h3>Email: <?= htmlspecialchars($user['email']) ?></h3>

<h2>Articles created by <?= htmlspecialchars($user['username']) ?></h2>

<?php
$stmt = $pdo->prepare("SELECT * FROM articles WHERE user_id = ?");
$stmt->execute([$user_id]);
$articles = $stmt->fetchAll();
?>

<?php if (empty($articles)): ?>
    <p>No products available.</p>
<?php else: ?>
    <ul class="product-list">
        <?php foreach ($articles as $article): ?>
            <li class="product-item">
                <a href="product_detail.php?id=<?= htmlspecialchars($article['id']) ?>">
                    <img src="<?= htmlspecialchars($article['image_url']) ?>" alt="<?= htmlspecialchars($article['name']) ?>" width="150">
                    <h2><?= htmlspecialchars($article['name']) ?></h2>
                    <p><?= htmlspecialchars($article['description']) ?></p>
                    <p><strong>Price: $<?= number_format($article['price'], 2) ?></strong></p> <!-- Affichage du prix -->
                    <small>Published on: <?= htmlspecialchars($article['published_at']) ?></small>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>