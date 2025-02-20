<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Commerce</title>
    <link rel="stylesheet" href="../assets/style.css"> <!-- Ensure this path is correct -->
</head>
<body>
<header>
    <nav>
        <a href="../pages">Home</a>
        <?php if (isset($_SESSION['user'])): ?>
            <div class="dropdown">
                <a href="../pages/cart.php">Cart</a>
                <div class="dropdown-content">
                    <a href="../pages/cart.php">View Cart</a>
                    <a href="../pages/cart_validate.php">Checkout</a>
                </div>
            </div>
            <div class="dropdown">
                <a href="#">Product</a>
                <div class="dropdown-content">
                    <a href="../pages/product_create.php">Create Product</a>
                </div>
            </div>
            <a href="../pages/account.php">Account</a>
            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                <a href="../pages/admin.php">Admin Panel</a>
            <?php endif; ?>
            <a href="../pages/logout.php">Logout</a>
        <?php else: ?>
            <a href="../pages/login.php">Login</a>
            <a href="../pages/register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>
