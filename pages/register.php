<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Insérer l'utilisateur avec balance initiale à 0
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, balance) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $email, $password, 0]);

    // Récupérer l'ID de l'utilisateur inséré
    $user_id = $pdo->lastInsertId();

    // Stocker toutes les infos nécessaires dans la session
    $_SESSION['user'] = [
        'id' => $user_id,
        'username' => $username,
        'email' => $email,
        'balance' => 0 // initialisation de la balance à 0
    ];

    header('Location: ./index.php');
    exit;
}
?>

<form method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Register</button>
</form>
<?php require_once '../includes/footer.php'; ?>
