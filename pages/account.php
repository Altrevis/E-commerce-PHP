<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user'])) {
    header('Location: /pages/login.php');
    exit;
}

$user_id = $_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_money'])) {
        $amount = $_POST['amount'];

        // Assurer que le montant est valide
        if ($amount > 0) {
            // Mise à jour de la balance
            $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
            $stmt->execute([$amount, $user_id]);

            // Mettre à jour la session pour refléter la nouvelle balance
            $_SESSION['user']['balance'] += $amount;

            // Rediriger pour éviter la soumission multiple et rafraîchir les données
            header("Location: account.php");
            exit;
        } else {
            echo "Invalid amount.";
        }
    }
}
?>

<div class="account-container">
    <div class="account-info">
        <h1>Account</h1>
        <h3>Username: <?= htmlspecialchars($user['username']) ?></h3>
        <h3>Email: <?= htmlspecialchars($user['email']) ?></h3>
        <h3>Balance: $<?= htmlspecialchars(number_format($user['balance'], 2)) ?></h3>
    </div>
    
    <div class="edit-profile">
        <h2>Edit Profile</h2>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($user['email']) ?>" required>
            <input type="password" name="password" placeholder="New Password">
            <button type="submit">Update</button>
        </form>
    </div>
    
    <div class="add-money">
        <h2>Add Money</h2>
        <form method="POST">
            <input type="number" name="amount" placeholder="Amount to add" min="1" required>
            <button type="submit" name="add_money">Add Money</button>
        </form>
    </div>
</div>

<div class="account-container">
    <h2>My Products</h2>
    
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
                        <p><strong>Price: $<?= number_format($article['price'], 2) ?></strong></p>
                        <p>Published on: <?= htmlspecialchars($article['published_at']) ?></p>
                        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'user'): ?>
                            <a href="product_edit.php?id=<?= $article['id'] ?>" class="edit-button">Edit Product</a>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>