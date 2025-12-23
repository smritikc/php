<style>
body {
    font-family: Arial, sans-serif;
    background-color: #111;
    color: #fff;
    margin: 0;
    padding: 0;
}
nav {
    background-color: #b00000;
    padding: 15px;
    text-align: center;
}
nav a {
    color: white;
    margin: 15px;
    text-decoration: none;
    font-weight: bold;
}
nav a:hover {
    color: #ff4444;
}
.container {
    width: 80%;
    margin: 30px auto;
    background: #222;
    padding: 20px;
    border-radius: 8px;
}
button {
    background: #b00000;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}
button:hover {
    background: #ff4444;
}
</style>

<nav>
    <a href="index.php">Home</a>
    <a href="categories.php">Categories</a>
    <a href="products.php">Products</a>
    <a href="wishlist.php">Wishlist</a>
    <a href="cart.php">Cart</a>
    <?php if(isset($_SESSION['user'])): ?>
        <span style="color:white; margin:10px;">Welcome, <?= $_SESSION['user'] ?></span>
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    <?php endif; ?>
</nav>
