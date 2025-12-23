<?php
include "config.php";
include "navbar.php";

// Handle Add Product
if(isset($_POST['add_product'])){
    $type_id = intval($_POST['type_id']); // must be a type
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $conn->query("INSERT INTO products (type_id, price, quantity) VALUES ($type_id, $price, $quantity)");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Handle Delete Product
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM products WHERE id=$id");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Handle Edit Product
if(isset($_POST['edit_product'])){
    $id = intval($_POST['product_id']);
    $type_id = intval($_POST['type_id']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $conn->query("UPDATE products SET type_id=$type_id, price=$price, quantity=$quantity WHERE id=$id");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Fetch types for dropdown
$types = $conn->query("
SELECT c.id, cat.name AS category, sub.name AS subcategory, c.name AS type_name 
FROM categories c
LEFT JOIN categories sub ON c.parent_id = sub.id AND c.type='type'
LEFT JOIN categories cat ON sub.parent_id = cat.id
WHERE c.type='type'
");

// Fetch all products
$products = $conn->query("
SELECT p.*, t.name AS type_name, sub.name AS subcategory_name, cat.name AS category_name
FROM products p
LEFT JOIN categories t ON p.type_id = t.id
LEFT JOIN categories sub ON t.parent_id = sub.id
LEFT JOIN categories cat ON sub.parent_id = cat.id
ORDER BY p.id
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
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
        .edit-btn, .delete-btn { background:#222; color:#ff4444; padding:4px 8px; border-radius:4px; cursor:pointer; text-decoration:none; margin-right:5px;}
        .edit-btn:hover, .delete-btn:hover { background:#ff0000; color:#fff; }
    </style>
</head>
<body>
<div class="container">

<h2>Add Product</h2>
<form method="POST">
    <select name="type_id" required>
        <option value="">--Select Type--</option>
        <?php while($t = $types->fetch_assoc()){
            echo "<option value='".$t['id']."'>".$t['category']." > ".$t['subcategory']." > ".$t['type_name']."</option>";
        } ?>
    </select>
    <input type="number" step="0.01" name="price" placeholder="Price" required>
    <input type="number" name="quantity" placeholder="Quantity" required>
    <button type="submit" name="add_product">Add Product</button>
</form>

<h2>All Products</h2>
<table>
    <tr>
        <th>Category</th>
        <th>Subcategory</th>
        <th>Type</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Action</th>
    </tr>
    <?php while($p = $products->fetch_assoc()): ?>
        <tr>
            <td><?= $p['category_name'] ?? '' ?></td>
            <td><?= $p['subcategory_name'] ?? '' ?></td>
            <td><?= $p['type_name'] ?? '' ?></td>
            <td><?= $p['price'] ?></td>
            <td><?= $p['quantity'] ?></td>
            <td>
                <a href="edit_product.php?id=<?= $p['id'] ?>" class="edit-btn">Edit</a>
                <a href="?delete=<?= $p['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

</div>
</body>
</html>
