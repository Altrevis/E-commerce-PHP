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
            <a href="../pages/index.php">Home</a>
            <?php if (isset($_SESSION['user'])): ?>
                <div class="dropdown">
                    <a href="../pages/cart.php">Cart</a>
                    <div class="dropdown-content">
                        <a href="../pages/cart.php">View Cart</a>
                        <a href="../pages/cart_validate.php">Checkout</a>
                    </div>
                </div>
                <a href="../pages/product_create.php">Create Product</a>
                <a href="../pages/account.php">Account</a>
                <a href="../logout.php">Logout</a>
            <?php else: ?>
                <a href="../pages/login.php">Login</a>
                <a href="../pages/register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>
