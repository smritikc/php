<?php
include "config.php";
include "navbar.php";

// Add product to cart
if(isset($_POST['add_to_cart'])){
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    // Get product price
    $res = $conn->query("SELECT price FROM products WHERE id=$product_id");
    $product = $res->fetch_assoc();
    $price = $product['price'];

    // Check if product already in cart
    $check = $conn->query("SELECT id, quantity FROM cart WHERE product_id=$product_id");
    if($check->num_rows > 0){
        $row = $check->fetch_assoc();
        $new_qty = $row['quantity'] + $quantity;
        $conn->query("UPDATE cart SET quantity=$new_qty WHERE id=".$row['id']);
    } else {
        $conn->query("INSERT INTO cart (product_id, quantity, price) VALUES ($product_id, $quantity, $price)");
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Update cart quantities
if(isset($_POST['update_cart'])){
    foreach($_POST['quantities'] as $cart_id => $qty){
        $cart_id = intval($cart_id);
        $qty = intval($qty);
        if($qty <= 0){
            $conn->query("DELETE FROM cart WHERE id=$cart_id");
        } else {
            $conn->query("UPDATE cart SET quantity=$qty WHERE id=$cart_id");
        }
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Remove single item
if(isset($_GET['remove'])){
    $cart_id = intval($_GET['remove']);
    $conn->query("DELETE FROM cart WHERE id=$cart_id");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Fetch products for "Add to Cart" dropdown
$products = $conn->query("
SELECT p.id, t.name AS type_name, sub.name AS subcategory_name, cat.name AS category_name, p.price, p.quantity AS stock
FROM products p
LEFT JOIN categories t ON p.type_id = t.id
LEFT JOIN categories sub ON t.parent_id = sub.id
LEFT JOIN categories cat ON sub.parent_id = cat.id
ORDER BY p.id
");

// Fetch current cart items
$cart_items = $conn->query("
SELECT c.id AS cart_id, c.quantity, c.price, p.id AS product_id, t.name AS type_name, sub.name AS subcategory_name, cat.name AS category_name
FROM cart c
LEFT JOIN products p ON c.product_id = p.id
LEFT JOIN categories t ON p.type_id = t.id
LEFT JOIN categories sub ON t.parent_id = sub.id
LEFT JOIN categories cat ON sub.parent_id = cat.id
ORDER BY c.id
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shopping Cart</title>
    <style>
        body { font-family: Arial; background:#111; color:#fff; margin:0; padding:0; }
        .container { width:90%; margin:20px auto; background:#222; padding:20px; border-radius:8px; }
        h2 { color:#ff4444; }
        input, select, button { padding:6px; margin-bottom:10px; border-radius:4px; border:none; }
        button { background:#b00000; color:white; cursor:pointer; }
        button:hover { background:#ff4444; }
        table { width:100%; border-collapse: collapse; margin-top:20px; }
        table, th, td { border:1px solid #555; }
        th, td { padding:10px; text-align:left; }
        th { background:#b00000; }
        .delete-btn { color:#ff4444; text-decoration:none; }
        .delete-btn:hover { color:#fff; }
        input.qty { width:50px; }
    </style>
</head>
<body>
<div class="container">

<h2>Add Product to Cart</h2>
<form method="POST">
    <select name="product_id" required>
        <option value="">--Select Product--</option>
        <?php while($p = $products->fetch_assoc()){
            echo "<option value='".$p['id']."'>".$p['category_name']." > ".$p['subcategory_name']." > ".$p['type_name']." | Price: ".$p['price']."</option>";
        } ?>
    </select>
    <input type="number" name="quantity" value="1" min="1" required>
    <button type="submit" name="add_to_cart">Add</button>
</form>

<h2>Shopping Cart</h2>
<?php if($cart_items->num_rows > 0): ?>
<form method="POST">
<table>
    <tr>
        <th>Category</th>
        <th>Subcategory</th>
        <th>Type</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Subtotal</th>
        <th>Action</th>
    </tr>
    <?php $total=0; while($c = $cart_items->fetch_assoc()):
        $subtotal = $c['price'] * $c['quantity'];
        $total += $subtotal;
    ?>
    <tr>
        <td><?= $c['category_name'] ?></td>
        <td><?= $c['subcategory_name'] ?></td>
        <td><?= $c['type_name'] ?></td>
        <td><?= $c['price'] ?></td>
        <td><input type="number" class="qty" name="quantities[<?= $c['cart_id'] ?>]" value="<?= $c['quantity'] ?>" min="0"></td>
        <td><?= $subtotal ?></td>
        <td><a href="?remove=<?= $c['cart_id'] ?>" class="delete-btn">Remove</a></td>
    </tr>
    <?php endwhile; ?>
    <tr>
        <td colspan="5" style="text-align:right;"><strong>Total:</strong></td>
        <td colspan="2"><?= $total ?></td>
    </tr>
</table>
<button type="submit" name="update_cart">Update Cart</button>
</form>
<?php else: ?>
<p>Your cart is empty.</p>
<?php endif; ?>

</div>
</body>
</html>
