<?php
session_start(); // Démarrer la session pour accéder aux variables de session
require_once '../includes/db.php'; // Inclusion du fichier de connexion à la base de données
require_once '../includes/header.php'; // Inclusion de l'en-tête de la page

// Vérification si l'utilisateur est connecté, sinon redirection vers la page de connexion
if (!isset($_SESSION['user'])) {
    header('Location: /pages/login.php');
    exit;
}

$user_id = $_SESSION['user']['id']; // Récupération de l'ID de l'utilisateur connecté

// Récupération des informations de l'utilisateur depuis la base de données
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification si l'utilisateur ajoute de l'argent à son compte
    if (isset($_POST['add_money'])) {
        $amount = $_POST['amount'];

        // Vérification que le montant est valide
        if ($amount > 0) {
            // Mise à jour du solde de l'utilisateur dans la base de données
            $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
            $stmt->execute([$amount, $user_id]);

            // Mise à jour de la session pour refléter la nouvelle balance
            $_SESSION['user']['balance'] += $amount;

            // Redirection pour éviter la soumission multiple du formulaire
            header("Location: account.php");
            exit;
        } else {
            echo "Invalid amount."; // Message d'erreur si le montant est invalide
        }
    }

    // Vérification si l'utilisateur met à jour son profil (username et mot de passe)
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    // Hachage du mot de passe uniquement si un nouveau mot de passe est fourni
    $password = $_POST['password'] ? password_hash($_POST['password'], PASSWORD_BCRYPT) : $user['password'];

    // Mise à jour des informations de l'utilisateur dans la base de données
    $stmt = $pdo->prepare("UPDATE users SET username = ?, password = ? WHERE id = ?");
    $stmt->execute([$username, $password, $user_id]);

    // Mise à jour du username dans la session
    $_SESSION['user']['username'] = $username;

    // Redirection vers la même page pour afficher les informations mises à jour
    header("Location: account.php");
    exit;
}
}
?>

<!-- Affichage des informations du compte -->
<div class="account-container">
    <div class="account-info">
        <h1>Account</h1>
        <h3>Username: <?= htmlspecialchars($user['username']) ?></h3>
        <h3>Email: <?= htmlspecialchars($user['email']) ?></h3>
        <h3>Balance: $<?= htmlspecialchars(number_format($user['balance'], 2)) ?></h3>
    </div>

    <!-- Formulaire de modification du profil -->
    <div class="edit-profile">
        <h2>Edit Profile</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="New Username"
                value="<?= htmlspecialchars($user['username']) ?>" required>
            <input type="password" name="password" placeholder="New Password">
            <button type="submit">Update</button>
        </form>

        <!-- Formulaire pour ajouter de l'argent -->
        <div class="add-money">
            <h2>Add Money</h2>
            <form method="POST">
                <input type="number" name="amount" placeholder="Amount to add" min="1" required>
                <button type="submit" name="add_money">Add Money</button>
            </form>
        </div>
    </div>
</div>

<!-- Section des produits de l'utilisateur -->
<h2>My Products</h2>

<?php
// Récupération des articles appartenant à l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM articles WHERE user_id = ?");
$stmt->execute([$user_id]);
$articles = $stmt->fetchAll();
?>

<!-- Affichage des articles de l'utilisateur -->
<?php if (empty($articles)): ?>
<p>No products available.</p> <!-- Message si aucun produit n'est disponible -->
<?php else: ?>
<ul class="product-list">
    <?php foreach ($articles as $article): ?>
    <li class="product-item">
        <a href="product_detail.php?id=<?= htmlspecialchars($article['id']) ?>">
            <img src="<?= htmlspecialchars($article['image_url']) ?>" alt="<?= htmlspecialchars($article['name']) ?>"
                width="150">
            <h2><?= htmlspecialchars($article['name']) ?></h2>
            <p><?= htmlspecialchars($article['description']) ?></p>
            <p><strong>Price: $<?= number_format($article['price'], 2) ?></strong></p>
            <p>Published on: <?= htmlspecialchars($article['published_at']) ?></p>

            <!-- Vérification du rôle pour afficher le bouton d'édition -->
            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'user'): ?>
            <a href="product_edit.php?id=<?= $article['id'] ?>" class="edit-button">Edit Product</a>
            <?php endif; ?>
        </a>
    </li>
    <?php endforeach; ?>
</ul>

<?php endif; ?>