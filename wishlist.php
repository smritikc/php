<?php
include "config.php";
include "navbar.php";

// Add product to wishlist
if(isset($_POST['add_to_wishlist'])){
    $product_id = intval($_POST['product_id']);
    $check = $conn->query("SELECT id FROM wishlist WHERE product_id=$product_id");
    if($check->num_rows == 0){
        $conn->query("INSERT INTO wishlist (product_id) VALUES ($product_id)");
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Remove product from wishlist
if(isset($_GET['remove'])){
    $id = intval($_GET['remove']);
    $conn->query("DELETE FROM wishlist WHERE id=$id");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Add product to cart from wishlist AND redirect to cart page
if(isset($_GET['add_to_cart'])){
    $product_id = intval($_GET['add_to_cart']);

    // Get product price
    $res = $conn->query("SELECT price FROM products WHERE id=$product_id");
    $product = $res->fetch_assoc();
    $price = $product['price'];

    // Add to cart (update quantity if already exists)
    $check = $conn->query("SELECT id, quantity FROM cart WHERE product_id=$product_id");
    if($check->num_rows > 0){
        $row = $check->fetch_assoc();
        $new_qty = $row['quantity'] + 1;
        $conn->query("UPDATE cart SET quantity=$new_qty WHERE id=".$row['id']);
    } else {
        $conn->query("INSERT INTO cart (product_id, quantity, price) VALUES ($product_id, 1, $price)");
    }

    // Remove from wishlist
    $conn->query("DELETE FROM wishlist WHERE product_id=$product_id");

    // Redirect to cart page
    header("Location: cart.php");
    exit;
}

// Fetch all products for add dropdown
$products = $conn->query("
SELECT p.id, t.name AS type_name, sub.name AS subcategory_name, cat.name AS category_name, p.price
FROM products p
LEFT JOIN categories t ON p.type_id = t.id
LEFT JOIN categories sub ON t.parent_id = sub.id
LEFT JOIN categories cat ON sub.parent_id = cat.id
ORDER BY p.id
");

// Fetch wishlist items
$wishlist_items = $conn->query("
SELECT w.id AS wish_id, p.id AS product_id, t.name AS type_name, sub.name AS subcategory_name, cat.name AS category_name, p.price
FROM wishlist w
LEFT JOIN products p ON w.product_id = p.id
LEFT JOIN categories t ON p.type_id = t.id
LEFT JOIN categories sub ON t.parent_id = sub.id
LEFT JOIN categories cat ON sub.parent_id = cat.id
ORDER BY w.added_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Wishlist</title>
    <style>
        body { font-family: Arial; background:#111; color:#fff; margin:0; padding:0; }
        .container { width:90%; margin:20px auto; background:#222; padding:20px; border-radius:8px; }
        h2 { color:#ff4444; }
        table { width:100%; border-collapse: collapse; margin-top:20px; }
        table, th, td { border:1px solid #555; }
        th, td { padding:10px; text-align:left; }
        th { background:#b00000; }
        button, select { padding:6px; margin-bottom:10px; border-radius:4px; border:none; cursor:pointer; }
        button { background:#b00000; color:white; }
        button:hover { background:#ff4444; }
        .delete-btn, .cart-btn { color:#ff4444; text-decoration:none; margin-right:5px; }
        .delete-btn:hover, .cart-btn:hover { color:#fff; }
    </style>
</head>
<body>
<div class="container">

<h2>Add Product to Wishlist</h2>
<form method="POST">
    <select name="product_id" required>
        <option value="">--Select Product--</option>
        <?php while($p = $products->fetch_assoc()){
            echo "<option value='".$p['id']."'>".$p['category_name']." > ".$p['subcategory_name']." > ".$p['type_name']." | Price: ".$p['price']."</option>";
        } ?>
    </select>
    <button type="submit" name="add_to_wishlist">Add</button>
</form>

<h2>Your Wishlist</h2>
<?php if($wishlist_items->num_rows > 0): ?>
<table>
    <tr>
        <th>Category</th>
        <th>Subcategory</th>
        <th>Type</th>
        <th>Price</th>
        <th>Action</th>
    </tr>
    <?php while($w = $wishlist_items->fetch_assoc()): ?>
    <tr>
        <td><?= $w['category_name'] ?></td>
        <td><?= $w['subcategory_name'] ?></td>
        <td><?= $w['type_name'] ?></td>
        <td><?= $w['price'] ?></td>
        <td>
            <a href="?add_to_cart=<?= $w['product_id'] ?>" class="cart-btn">Add to Cart</a>
            <a href="?remove=<?= $w['wish_id'] ?>" class="delete-btn">Remove</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<?php else: ?>
<p>Your wishlist is empty.</p>
<?php endif; ?>

</div>
</body>
</html>
