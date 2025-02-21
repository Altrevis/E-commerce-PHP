<?php
session_start();  // Démarre la session PHP

require_once '../includes/db.php';  // Inclut la connexion à la base de données
require_once '../includes/header.php';  // Inclut le fichier d'en-tête (généralement pour le menu et les liens)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {  // Vérifie si la méthode de la requête est POST (soumission du formulaire)
    $name = $_POST['name'];  // Récupère le nom du produit
    $description = $_POST['description'];  // Récupère la description du produit
    $image_url = $_POST['image_url'];  // Récupère l'URL de l'image du produit
    $quantity = $_POST['quantity'];  // Récupère la quantité disponible en stock
    $price = $_POST['price'];  // Récupère le prix du produit

    // Crée un slug à partir du nom du produit (version simplifiée pour l'URL)
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

    // Vérifie si le nom d'utilisateur est défini dans la session
    if (!isset($_SESSION['username'])) {
        echo "Username is not set in session";  // Affiche un message d'erreur si le nom d'utilisateur n'est pas défini
        exit;
    }

    // Récupère l'ID de l'utilisateur connecté à partir de la session
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$_SESSION['username']]);
    $user_id = $stmt->fetchColumn();  // Récupère l'ID de l'utilisateur connecté

    try {
        // Insère un nouvel article dans la base de données avec le nom, description, image, prix, et l'ID de l'utilisateur
        $stmt = $pdo->prepare("INSERT INTO articles (name, slug, description, image_url, price, user_id, published_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$name, $slug, $description, $image_url, $price, $user_id]);

        // Récupère l'ID de l'article créé
        $article_id = $pdo->lastInsertId();

        // Insère la quantité de stock dans la base de données pour l'article créé
        $stmt = $pdo->prepare("INSERT INTO stock (article_id, quantity) VALUES (?, ?)");
        $stmt->execute([$article_id, $quantity]);

        echo "Product created successfully!";  // Affiche un message de succès si tout se passe bien
        header('Location: ./index.php');  // Redirige vers la page d'accueil après la création du produit
        exit;
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();  // Affiche un message d'erreur si une exception est lancée
        exit;
    }
}
?>

<div class="narrow-block">
    <!-- Conteneur pour le formulaire de création de produit -->
    <h1>Create Product</h1> <!-- Titre de la page -->
    <form method="POST">
        <!-- Formulaire qui envoie une requête POST -->
        <input type="text" name="name" placeholder="Name" required> <!-- Champ pour le nom du produit -->
        <textarea name="description" placeholder="Description"></textarea> <!-- Champ pour la description du produit -->
        <input type="text" name="image_url" placeholder="Image URL"> <!-- Champ pour l'URL de l'image -->
        <input type="number" name="quantity" placeholder="Quantity" required> <!-- Champ pour la quantité -->
        <input type="number" name="price" placeholder="Price" step="0.01" required> <!-- Champ pour le prix -->
        <button type="submit">Create</button> <!-- Bouton pour soumettre le formulaire -->
    </form>
</div>