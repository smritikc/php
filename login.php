<?php
session_start();
include "config.php";

$msg = '';

if(isset($_POST['login'])){
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $res = $conn->query("SELECT * FROM users WHERE email='$email'");
    if($res->num_rows > 0){
        $user = $res->fetch_assoc();
        if(password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: index.php"); // Redirect to home page
            exit;
        } else {
            $msg = "Incorrect password!";
        }
    } else {
        $msg = "Email not registered!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body { font-family: Arial; background:#111; color:#fff; margin:0; padding:0; }
        .container { width:400px; margin:50px auto; background:#222; padding:20px; border-radius:8px; }
        input, button { width:100%; padding:6px; margin-bottom:10px; border-radius:4px; border:none; }
        button { background:#b00000; color:white; cursor:pointer; }
        button:hover { background:#ff4444; }
        .msg { color:#f00; margin-bottom: 10px; }
    </style>
</head>
<body>
<div class="container">
<h2>User Login</h2>
<?php if($msg) echo "<p class='msg'>$msg</p>"; ?>
<form method="POST">
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit" name="login">Login</button>
</form>
</div>
</body>
</html>
