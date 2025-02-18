<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Commerce</title>
    <link rel="stylesheet" href="/php_exam//assets/style.css"> <!-- Ensure this path is correct -->
</head>
<body>
    <header>
        <nav>
            <a href="/php_exam">Home</a>
            <?php if (isset($_SESSION['user'])): ?>
                <div class="dropdown">
                    <a href="/php_exam/pages/cart.php">Cart</a>
                    <div class="dropdown-content">
                        <a href="/php_exam/pages/cart.php">View Cart</a>
                        <a href="/php_exam/pages/cart_validate.php">Checkout</a>
                    </div>
                </div>
                <div class="dropdown">
                    <a href="#">Product</a>
                    <div class="dropdown-content">
                        <a href="/php_exam/pages/product_create.php">Create Product</a>
                        <a href="/php_exam/pages/product_edit.php?id=<?= $article_id ?>">Edit Product</a>
                        <a href="/php_exam/pages/product_detail.php?id=1">View Product Details</a> <!-- Example link -->
                    </div>
                </div>
                <a href="/php_exam/pages/account.php">Account</a>
                <a href="/php_exam/logout.php">Logout</a>
            <?php else: ?>
                <a href="/php_exam/pages/login.php">Login</a>
                <a href="/hp_exam/pages/register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>
