<?php
include "config.php";
include "navbar.php";

// Handle form submission
if(isset($_POST['register'])){
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // secure hash
    $phone = $conn->real_escape_string($_POST['phone']);
    $address = $conn->real_escape_string($_POST['address']);

    // Check if email already exists
    $check = $conn->query("SELECT id FROM users WHERE email='$email'");
    if($check->num_rows == 0){
        $conn->query("INSERT INTO users (name,email,password,phone,address) VALUES ('$name','$email','$password','$phone','$address')");
        $msg = "Registration successful!";
    } else {
        $msg = "Email already registered!";
    }
}

// Fetch all users for table display
$users = $conn->query("SELECT name, email, phone, address FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration</title>
    <style>
        body { font-family: Arial; background:#111; color:#fff; margin:0; padding:0; }
        .container { width:90%; margin:20px auto; background:#222; padding:20px; border-radius:8px; }
        h2 { color:#ff4444; }
        input, button { padding:6px; margin-bottom:10px; border-radius:4px; border:none; }
        button { background:#b00000; color:white; cursor:pointer; }
        button:hover { background:#ff4444; }
        table { width:100%; border-collapse: collapse; margin-top:20px; }
        table, th, td { border:1px solid #555; }
        th, td { padding:10px; text-align:left; }
        th { background:#b00000; }
        .msg { color: #0f0; margin-bottom: 10px; }
    </style>
</head>
<body>
<div class="container">

<h2>User Registration</h2>
<?php if(isset($msg)) echo "<p class='msg'>$msg</p>"; ?>
<form method="POST">
    <input type="text" name="name" placeholder="Full Name" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <input type="text" name="phone" placeholder="Phone Number" required><br>
    <input type="text" name="address" placeholder="Address" required><br>
    <button type="submit" name="register">Register</button>
</form>

<h2>Pre-Registered Users</h2>
<?php if($users->num_rows > 0): ?>
<table>
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Address</th>
    </tr>
    <?php while($u = $users->fetch_assoc()): ?>
    <tr>
        <td><?= $u['name'] ?></td>
        <td><?= $u['email'] ?></td>
        <td><?= $u['phone'] ?></td>
        <td><?= $u['address'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>
<?php else: ?>
<p>No registered users yet.</p>
<?php endif; ?>

</div>
</body>
</html>
