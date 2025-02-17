<?php
require 'config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $slug = $_POST['slug'];
    $description = $_POST['description'];
    $image = $_POST['image'];
    $quantity = $_POST['quantity'];
    $datePublication = date('Y-m-d H:i:s');
    $dateModification = date('Y-m-d H:i:s');
    
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("INSERT INTO ARTICLE (Nom, Slug, Description, DatePublication, DateModification, Image-Link) VALUES (:nom, :slug, :description, :datePublication, :dateModification, :image)");
        $stmt->execute([
            'nom' => $nom,
            'slug' => $slug,
            'description' => $description,
            'datePublication' => $datePublication,
            'dateModification' => $dateModification,
            'image' => $image
        ]);
        
        $articleID = $pdo->lastInsertId();
        
        $stmt = $pdo->prepare("INSERT INTO STOCK (ArticleID, Quantity) VALUES (:articleID, :quantity)");
        $stmt->execute([
            'articleID' => $articleID,
            'quantity' => $quantity
        ]);
        
        $pdo->commit();
        header('Location: index.html');
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "<p style='color:red;'>Erreur lors de la création de l'article.</p>";
    }
}
?>