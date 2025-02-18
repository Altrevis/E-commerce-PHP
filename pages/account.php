<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user'])) {
    header('Location: /pages/login.php');
    exit;
}

$user_id = $_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'] ? password_hash($_POST['password'], PASSWORD_BCRYPT) : $user['password'];

    $stmt = $pdo->prepare("UPDATE users SET email = ?, password = ? WHERE id = ?");
    $stmt->execute([$email, $password, $user_id]);

    $_SESSION['user']['email'] = $email;
    echo "Profile updated successfully!";
}
?>

<h1>Account</h1>
<p>Username: <?= htmlspecialchars($user['username']) ?></p>
<p>Email: <?= htmlspecialchars($user['email']) ?></p>

<h2>Edit Profile</h2>
<form method="POST">
    <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($user['email']) ?>" required>
    <input type="password" name="password" placeholder="New Password (leave blank to keep current)">
    <button type="submit">Update</button>
</form>

<?php require_once '../includes/footer.php'; ?>
