<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

// Vérifie si l'utilisateur est connecté, sinon redirige vers la page de connexion
if (!isset($_SESSION['user'])) {
    header('Location: /pages/login.php');
    exit;
}

// Ajout d'un article au panier
if (isset($_POST['article_id']) && isset($_POST['quantity'])) {
    $article_id = (int) $_POST['article_id']; // Conversion en entier pour éviter les injections
    $quantity = (int) $_POST['quantity'];
    $user_id = $_SESSION['user']['id'];

    // Vérifie le stock disponible pour cet article
    $stmt = $pdo->prepare("SELECT quantity FROM stock WHERE article_id = ?");
    $stmt->execute([$article_id]);
    $stock_available = $stmt->fetchColumn();

    // Vérifie la quantité déjà présente dans le panier
    $stmt = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND article_id = ?");
    $stmt->execute([$user_id, $article_id]);
    $existing_quantity = $stmt->fetchColumn() ?: 0; // Si NULL, retourne 0

    if ($stock_available === false) {
        // Si l'article n'existe pas dans le stock
        $error = "Article non trouvé.";
    } elseif (($existing_quantity + $quantity) > $stock_available) {
        // Si la quantité demandée dépasse le stock disponible
        $error = "Stock insuffisant. Il reste seulement $stock_available en stock.";
    } else {
        // Si l'article est déjà dans le panier, on met à jour la quantité
        if ($existing_quantity) {
            $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND article_id = ?");
            $stmt->execute([$quantity, $user_id, $article_id]);
        } else {
            // Sinon, on ajoute l'article dans le panier
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, article_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $article_id, $quantity]);
        }
    }
}

// Suppression d'un article du panier ou réduction de la quantité
if (isset($_POST['cart_id'])) {
    $cart_id = (int) $_POST['cart_id']; // Sécurisation avec un cast en entier

    // Vérifie si l'article est bien présent dans le panier
    $stmt = $pdo->prepare("SELECT * FROM cart WHERE id = ?");
    $stmt->execute([$cart_id]);
    $cart_item = $stmt->fetch();

    if ($cart_item) {
        if ($cart_item['quantity'] > 1) {
            // Si la quantité est supérieure à 1, on réduit simplement la quantité
            $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity - 1 WHERE id = ?");
            $stmt->execute([$cart_id]);
        } else {
            // Sinon, on supprime complètement l'article du panier
            $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ?");
            $stmt->execute([$cart_id]);
        }
    }
}

// Récupération des articles du panier de l'utilisateur
$user_id = $_SESSION['user']['id'];
$stmt = $pdo->prepare("
    SELECT c.id, a.name, a.image_url, c.quantity, a.price 
    FROM cart c 
    JOIN articles a ON c.article_id = a.id 
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();
?>

<h1>Cart</h1>

<?php if (empty($cart_items)): ?>
<!-- Affichage si le panier est vide -->
<p>Your cart is empty.</p>
<?php else: ?>
<ul>
    <?php foreach ($cart_items as $item): ?>
    <li>
        <!-- Affichage des détails de l'article -->
        <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" width="50">
        <?= htmlspecialchars($item['name']) ?> - Quantity: <?= $item['quantity'] ?> - Price:
        $<?= number_format($item['price'], 2) ?>

        <!-- Formulaire pour retirer un article du panier -->
        <form method="POST" action="<?= $_SERVER['REQUEST_URI'] ?>">
            <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
            <button type="submit">Remove</button>
        </form>

        <!-- Affichage des erreurs éventuelles -->
        <?php if (!empty($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
    </li>
    <?php endforeach; ?>
</ul>
<!-- Lien pour procéder au paiement -->
<a href="../pages/cart_validate.php">Proceed to Checkout</a>
<?php endif; ?>