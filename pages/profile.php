<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

// Vérifier si un ID utilisateur est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid user ID";
    exit;
}

$user_id = intval($_GET['id']);

// Récupération des informations de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "User not found";
    exit;
}

// Récupération des articles créés par l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM articles WHERE user_id = ?");
$stmt->execute([$user_id]);
$articles = $stmt->fetchAll();
?>

<h1>Profile of <?= htmlspecialchars($user['username']) ?></h1>
<h3>Email: <?= htmlspecialchars($user['email']) ?></h3>

<h2>Articles created by <?= htmlspecialchars($user['username']) ?></h2>

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
