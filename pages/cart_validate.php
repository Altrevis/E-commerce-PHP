<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

// Vérifie si l'utilisateur est connecté, sinon redirige vers la page de connexion
if (!isset($_SESSION['user'])) {
    header('Location: /pages/login.php');
    exit;
}

$user_id = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Suppression d'un article du panier
    if (isset($_POST['cart_id'])) {
        $cart_id = $_POST['cart_id'];
        
        // Supprime l'article du panier uniquement si l'utilisateur en est le propriétaire
        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->execute([$cart_id, $user_id]);

        // Redirige l'utilisateur vers la page du panier après suppression
        header('Location: /pages/cart.php');
        exit;
    }

    // Récupération du total du panier
    $stmt = $pdo->prepare("
        SELECT SUM(a.price * c.quantity) AS total 
        FROM cart c 
        JOIN articles a ON c.article_id = a.id 
        WHERE c.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $total = $stmt->fetchColumn();

    // Vérifie si l'utilisateur a un solde suffisant pour payer
    if ($_SESSION['user']['balance'] >= $total) {
        // Début de la transaction pour assurer l'intégrité des données
        $pdo->beginTransaction();
        try {
            // Mise à jour du solde de l'utilisateur en soustrayant le montant total
            $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?")
                ->execute([$total, $user_id]);

            // Création de la commande avec un JSON vide comme placeholder pour les articles
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, items) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $total, json_encode([])]);

            // Suppression de tous les articles du panier après la commande
            $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$user_id]);

            // Valide la transaction
            $pdo->commit();
            echo "Order placed successfully!";
        } catch (Exception $e) {
            // Annule la transaction en cas d'erreur
            $pdo->rollBack();
            echo "Error placing order: " . $e->getMessage();
        }
    } else {
        // Message d'erreur si le solde est insuffisant
        echo "Insufficient balance.";
    }
}
?>

<h1>Proceed to Checkout</h1>

<?php
// Récupération du montant total actuel du panier
$stmt = $pdo->prepare("
    SELECT SUM(a.price * c.quantity) AS total 
    FROM cart c 
    JOIN articles a ON c.article_id = a.id 
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$total = $stmt->fetchColumn();
?>

<!-- Affichage du total du panier -->
<p>Total amount: $<?= htmlspecialchars(number_format($total, 2)) ?></p>

<!-- Formulaire pour confirmer et payer la commande -->
<form method="POST">
    <button type="submit">Confirm and Pay</button>
</form>