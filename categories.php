<?php
include "config.php";
include "navbar.php";

// Handle Category Add
if(isset($_POST['add_category'])){
    $name = $conn->real_escape_string($_POST['cat_name']);
    $conn->query("INSERT INTO categories (name, parent_id, type) VALUES ('$name', NULL, 'category')");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Handle Subcategory Add
if(isset($_POST['add_subcategory'])){
    $name = $conn->real_escape_string($_POST['sub_name']);
    $parent_id = intval($_POST['category_id']);
    $conn->query("INSERT INTO categories (name, parent_id, type) VALUES ('$name', $parent_id, 'subcategory')");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Handle Type/Item Add
if(isset($_POST['add_type'])){
    $name = $conn->real_escape_string($_POST['type_name']);
    $parent_id = intval($_POST['subcategory_id']);
    $conn->query("INSERT INTO categories (name, parent_id, type) VALUES ('$name', $parent_id, 'type')");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Handle Delete
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM categories WHERE id=$id");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Fetch categories for dropdowns
$categories = $conn->query("SELECT * FROM categories WHERE type='category' ORDER BY name");
$subcategories = $conn->query("SELECT * FROM categories WHERE type='subcategory' ORDER BY name");

// Fetch all items with parent relationships
$sql = "
SELECT 
    cat.name AS category_name, 
    sub.name AS subcategory_name, 
    typ.name AS type_name,
    typ.id AS type_id,
    sub.id AS sub_id,
    cat.id AS cat_id
FROM categories cat
LEFT JOIN categories sub ON sub.parent_id = cat.id AND sub.type='subcategory'
LEFT JOIN categories typ ON typ.parent_id = sub.id AND typ.type='type'
WHERE cat.type='category'
ORDER BY cat.id, sub.id, typ.id
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Categories</title>
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
        .delete-btn { background:#222; color:#ff4444; padding:4px 8px; border-radius:4px; cursor:pointer; text-decoration:none; }
        .delete-btn:hover { background:#ff0000; color:#fff; }
    </style>
</head>
<body>
<div class="container">

<h2>Add Category</h2>
<form method="POST">
    <input type="text" name="cat_name" placeholder="Category Name" required>
    <button type="submit" name="add_category">Add Category</button>
</form>

<h2>Add Subcategory</h2>
<form method="POST">
    <select name="category_id" required>
        <option value="">--Select Category--</option>
        <?php while($cat = $categories->fetch_assoc()){
            echo "<option value='".$cat['id']."'>".$cat['name']."</option>";
        } ?>
    </select>
    <input type="text" name="sub_name" placeholder="Subcategory Name" required>
    <button type="submit" name="add_subcategory">Add Subcategory</button>
</form>

<h2>Add Type/Item</h2>
<form method="POST">
    <select name="subcategory_id" required>
        <option value="">--Select Subcategory--</option>
        <?php
        while($sub = $subcategories->fetch_assoc()){
            echo "<option value='".$sub['id']."'>".$sub['name']."</option>";
        }
        ?>
    </select>
    <input type="text" name="type_name" placeholder="Type/Item Name" required>
    <button type="submit" name="add_type">Add Type</button>
</form>

<h2>All Categories, Subcategories & Types</h2>
<table>
    <tr>
        <th>Category</th>
        <th>Subcategory</th>
        <th>Item/Type</th>
        <th>Action</th>
    </tr>
    <?php
    while($row = $result->fetch_assoc()){
        echo "<tr>";
        echo "<td>".($row['category_name'] ?? '')."</td>";
        echo "<td>".($row['subcategory_name'] ?? '')."</td>";
        echo "<td>".($row['type_name'] ?? '')."</td>";
        echo "<td>";
        if($row['type_id']) echo "<a href='?delete=".$row['type_id']."' class='delete-btn'>Delete Type</a> ";
        if($row['sub_id']) echo "<a href='?delete=".$row['sub_id']."' class='delete-btn'>Delete Subcategory</a> ";
        if($row['cat_id']) echo "<a href='?delete=".$row['cat_id']."' class='delete-btn'>Delete Category</a>";
        echo "</td>";
        echo "</tr>";
    }
    ?>
</table>

</div>
</body>
</html>
