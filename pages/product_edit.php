<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

// Vérification de la session utilisateur
if (empty($_SESSION['user'])) {
    header('Location: /pages/login.php');
    exit;
}

// Vérification et validation sécurisée de l'ID
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET['id'];

// Récupération de l'article
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch();

// Vérification des permissions (seul l'auteur ou un admin peut éditer)
if (!$article || ($article['user_id'] != $_SESSION['user']['id'] && $_SESSION['user']['role'] !== 'admin')) {
    die("You are not authorized to edit this product.");
}

// Traitement de la mise à jour du produit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $image_url = trim($_POST['image_url']);
    $price = (float) $_POST['price']; // Conversion sécurisée du prix

    // Vérification de la validité des entrées
    if (!empty($name) && !empty($description) && $price > 0) {
        $stmt = $pdo->prepare("UPDATE articles SET name = ?, description = ?, image_url = ?, price = ? WHERE id = ?");
        $stmt->execute([$name, $description, $image_url, $price, $id]);

        header("Location: product_detail.php?id=$id");
        exit;
    } else {
        $error = "Please fill in all fields correctly.";
    }
}

// Traitement de la suppression du produit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_article']) && $_POST['delete_article'] === '1') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    try {
        // Suppression des enregistrements liés au stock
        $stmt = $pdo->prepare("DELETE FROM stock WHERE article_id = ?");
        $stmt->execute([$id]);

        // Suppression de l'article
        $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
        $stmt->execute([$id]);

        // Redirection après suppression
        header("Location: index.php?message=Article deleted successfully!");
        exit;
    } catch (PDOException $e) {
        $error = "Error deleting article: " . $e->getMessage();
    }
}

// Génération du token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<h1>Edit Product</h1>

<?php if (isset($error)): ?>
    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST">
    <label>Name:</label>
    <input type="text" name="name" value="<?= htmlspecialchars($article['name']) ?>" required>

    <label>Description:</label>
    <textarea name="description" required><?= htmlspecialchars($article['description']) ?></textarea>

    <label>Image URL:</label>
    <input type="text" name="image_url" value="<?= htmlspecialchars($article['image_url']) ?>">

    <label>Price ($):</label>
    <input type="number" name="price" value="<?= htmlspecialchars($article['price']) ?>" step="0.01" min="0.01" required>

    <button type="submit" name="update">Update</button>
</form>

<!-- Bouton Delete -->
<form method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <input type="hidden" name="delete_article" value="1">
    <button type="submit">Delete</button>
</form>
