<?php
session_start();  // Démarre la session PHP

require_once '../includes/db.php';  // Inclut la connexion à la base de données
require_once '../includes/header.php';  // Inclut le fichier d'en-tête (généralement pour le menu et les liens)

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {  // Vérifie si l'ID est présent et valide dans l'URL
    header("Location: index.php");  // Redirige vers la page d'accueil en cas d'ID invalide
    exit;
}

$id = (int) $_GET['id'];  // Récupère l'ID de l'article et le convertit en entier

// Prépare la requête pour récupérer l'article à partir de l'ID
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch();  // Récupère les informations de l'article

if (!$article) {  // Si l'article n'existe pas
    die("Article not found.");  // Affiche un message d'erreur
}
?>

<!-- Affiche le nom de l'article -->
<h1><?= htmlspecialchars($article['name']) ?></h1>
<!-- Affiche la description de l'article -->
<p><?= htmlspecialchars($article['description']) ?></p>
<!-- Affiche l'image de l'article -->
<img src="<?= htmlspecialchars($article['image_url']) ?>" alt="<?= htmlspecialchars($article['name']) ?>" width="200">

<!-- Affiche le prix de l'article formaté -->
<p><strong>Price: $<?= number_format($article['price'], 2) ?></strong></p>

<!-- Formulaire pour ajouter l'article au panier -->
<form method="POST" action="../pages/cart.php">
    <input type="hidden" name="article_id" value="<?= $article['id'] ?>"> <!-- Envoie l'ID de l'article -->
    <input type="number" name="quantity" value="1" min="1"> <!-- Permet de sélectionner la quantité -->
    <button type="submit">Add to Cart</button> <!-- Bouton pour ajouter l'article au panier -->
</form>

<!-- Affiche un bouton d'édition si l'utilisateur est un admin -->
<?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
<a href="product_edit.php?id=<?= $article['id'] ?>" class="edit-button">Edit Product</a>
<!-- Lien vers la page d'édition de l'article -->
<?php endif; ?>