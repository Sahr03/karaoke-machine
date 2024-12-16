<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Website</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    margin: 0;
}

header {
    background-color: #333;
    color: #fff;
    padding: 10px;
}

nav ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
}

nav ul li {
    display: inline-block;
    margin-right: 20px;
}

nav ul li a {
    color: #fff;
    text-decoration: none;
}

main {
    max-width: 800px;
    margin: 20px auto;
    padding: 0 20px;
}

section {
    margin-bottom: 40px;
}

h1 {
    font-size: 3em;
    margin-bottom: 20px;
}

h2 {
    font-size: 2em;
    margin-bottom: 20px;
}

form {
    display: flex;
    flex-direction: column;
    max-width: 500px;
}

label {
    font-size: 1.2em;
    margin-bottom: 10px;
}

input[type="text"],
input[type="email"],
textarea {
    padding: 10px;
    margin-bottom: 20px;
    border: none;
    border-radius: 5px;
}

input[type="submit"] {
    background-color: #333;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

footer {
    background-color: #333;
    color: #fff;
    padding: 10px;
    text-align: center;
}
    </style>
    <link rel="stylesheet" href="style.css">
</head>
<body>


<?php
    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    include("secret.php");

    try {
        $dsn = "mysql:host=courses;dbname=z1930087";
        $pdo = new PDO($dsn, $username, $password);
    } catch (PDOException $e) {
        echo "Connection to database failed: " . $e->getMessage();
    }

    if(isset($_POST['username'])) {
        $username = $_POST['username'];
        // Store the username in the session
        session_start();
        $_SESSION['username'] = $username;
        // Redirect to the songs page
        header('Location: songs.php');
        exit();
    }

    if(isset($_POST['queue_type']) && isset($_POST['file_id'])) {
        $queue_type = $_POST['queue_type'];
        $file_id = $_POST['file_id'];
        $user_id = $_SESSION['user_id'];

        // Insert the queue entry into the database
        $stmt = $pdo->prepare("INSERT INTO Queue (Queue_Type, Time, File_ID, User_ID) VALUES (:queue_type, NOW(), :file_id, :user_id)");
        $stmt->bindParam(':queue_type', $queue_type);
        $stmt->bindParam(':file_id', $file_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
    }
?>

<header>
    <nav>
        <ul>
            <li><a href="#">Home</a></li>
            <li><a href='DJ.php'>DJ Interface</a></li>
        </ul>
    </nav>
</header>

<main>
    <section>
        <h1>Karaoke</h1>
    </section>

    <form method="post">
        <label for="username">Enter your name below</label>
        <input type="text" id="username" name="username">
        <input type="submit" value="Submit">
    </form>

    <?php if(isset($_SESSION['username'])): ?>
        <section>
            <h2>Choose a song to sing</h2>
            <form method="post">
                <label for="file_id">Song</label>
                <select id="file_id" name="file_id">
                    <option value="1">Song 1</option>
                    <option value="2">Song 2</option>
                    <option value="3">Song 3</option>
                </select>
                <p>Queue Type:</p>
                <input type="radio" id="free" name="queue_type" value="Free">
                <label for="free">Free</label><br>
                <input type="radio" id="accelerated" name="queue_type" value="Accelerated">
                <label for="accelerated">Accelerated (pay to move up in queue)</label><br>
                <input type="submit" value="Sign Up">
            </form>
        </section>
    <?php endif; ?>
</main>

<footer>
    <p>© Karaoke 2023</p>
</footer>
