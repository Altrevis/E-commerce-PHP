<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $image_url = trim($_POST['image_url']);
    $quantity = intval($_POST['quantity']);
    $price = floatval($_POST['price']); // Récupération du prix

    // Génération du slug
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

    // Vérification de l'utilisateur en session
    if (!isset($_SESSION['username'])) {
        echo "Username is not set in session";
        exit;
    }

    // Récupération de l'ID utilisateur
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$_SESSION['username']]);
    $user_id = $stmt->fetchColumn();

    if (!$user_id) {
        echo "User not found";
        exit;
    }

    try {
        // Insertion de l'article avec le prix
        $stmt = $pdo->prepare("
            INSERT INTO articles (name, slug, description, image_url, price, user_id, published_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$name, $slug, $description, $image_url, $price, $user_id]);

        $article_id = $pdo->lastInsertId();

        // Insertion dans le stock
        $stmt = $pdo->prepare("INSERT INTO stock (article_id, quantity) VALUES (?, ?)");
        $stmt->execute([$article_id, $quantity]);

        // Redirection après succès
        header('Location: ./index.php');
        exit;
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
        exit;
    }
}
?>

<h1>Create Product</h1>

<form method="POST">
    <input type="text" name="name" placeholder="Name" required>
    <textarea name="description" placeholder="Description"></textarea>
    <input type="text" name="image_url" placeholder="Image URL">
    <input type="number" name="quantity" placeholder="Quantity" required min="1">
    <input type="number" name="price" placeholder="Price" step="0.01" required min="0"> <!-- Champ prix avec valeur minimale -->
    <button type="submit">Create</button>
</form>
