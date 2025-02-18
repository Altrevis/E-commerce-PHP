<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';
if (!isset($_SESSION['user'])) {
    header('Location: /pages/login.php');
    exit;
}

$article_id = $_GET['id'];
$user_id = $_SESSION['user']['id'];

// Check if the user owns the article or is an admin
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$article_id]);
$article = $stmt->fetch();

if (!$article || ($article['user_id'] != $user_id && $_SESSION['user']['role'] !== 'admin')) {
    die("You do not have permission to edit this article.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $image_url = $_POST['image_url'];

    $stmt = $pdo->prepare("UPDATE articles SET name = ?, description = ?, image_url = ? WHERE id = ?");
    $stmt->execute([$name, $description, $image_url, $article_id]);

    header('Location: /');
    exit;
}
?>

<h1>Edit Product</h1>
<form method="POST">
    <input type="text" name="name" placeholder="Name" value="<?= htmlspecialchars($article['name']) ?>" required>
    <textarea name="description" placeholder="Description"><?= htmlspecialchars($article['description']) ?></textarea>
    <input type="text" name="image_url" placeholder="Image URL" value="<?= htmlspecialchars($article['image_url']) ?>">
    <button type="submit">Save Changes</button>
</form>
<?php require_once '../includes/footer.php'; ?>
