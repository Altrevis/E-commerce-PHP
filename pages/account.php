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
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'] ? password_hash($_POST['password'], PASSWORD_BCRYPT) : $user['password'];

        $stmt = $pdo->prepare("UPDATE users SET email = ?, password = ? WHERE id = ?");
        $stmt->execute([$email, $password, $user_id]);

        $_SESSION['user']['email'] = $email;
        echo "Profile updated successfully!";
    }

    // Ajouter de l'argent
    if (isset($_POST['add_money'])) {
        $amount = $_POST['amount'];

        // Assurer que le montant est valide
        if ($amount > 0) {
            // Mise à jour de la balance
            $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
            $stmt->execute([$amount, $user_id]);

            // Mettre à jour la session pour refléter la nouvelle balance
            $_SESSION['user']['balance'] += $amount;

            echo "Money added successfully!";
        } else {
            echo "Invalid amount.";
        }
    }
}
?>

<h1>Account</h1>
<p>Username: <?= htmlspecialchars($user['username']) ?></p>
<p>Email: <?= htmlspecialchars($user['email']) ?></p>
<p>Balance: $<?= htmlspecialchars(number_format($user['balance'], 2)) ?></p> <!-- Affichage de la balance -->

<h2>Edit Profile</h2>
<form method="POST">
    <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($user['email']) ?>" required>
    <input type="password" name="password" placeholder="New Password (leave blank to keep current)">
    <button type="submit">Update</button>
</form>

<h2>Add Money</h2>
<form method="POST">
    <input type="number" name="amount" placeholder="Amount to add" min="1" required>
    <button type="submit" name="add_money">Add Money</button>
</form>

<?php require_once '../includes/footer.php'; ?>
