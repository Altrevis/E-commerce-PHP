<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $image_url = $_POST['image_url'];
    $quantity = $_POST['quantity'];

    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

    try {
        $stmt = $pdo->prepare("INSERT INTO articles (name, slug, description, image_url, published_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$name, $slug, $description, $image_url]);

        $article_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO stock (article_id, quantity) VALUES (?, ?)");
        $stmt->execute([$article_id, $quantity]);

        echo "Product created successfully!";
        header('Location: /php_exam');
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
    <input type="number" name="quantity" placeholder="Quantity" required>
    <button type="submit">Create</button>
</form>
<?php require_once '../includes/footer.php'; ?>
