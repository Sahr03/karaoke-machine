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
// Get the queued songs by user
$queuedSongsStmt = $pdo->prepare("SELECT User.User_ID, Songs.Title, Contributors.Name AS Artist, Karaoke_Files.File_ID
                                  FROM User 
                                  JOIN Queue ON Queue.User_ID = User.User_ID 
                                  JOIN Karaoke_Files ON Queue.File_ID = Karaoke_Files.File_ID
                                  JOIN Songs ON Karaoke_Files.Song_ID = Songs.Song_ID
                                  JOIN Song_Contributors ON Songs.Song_ID = Song_Contributors.Song_ID
                                  JOIN Contributors ON Song_Contributors.Contributor_ID = Contributors.Contributor_ID");
                                  
$queuedSongsStmt->execute();
$queuedSongs = $queuedSongsStmt->fetchAll(PDO::FETCH_NUM);

// Get the singing song by user
$singingSongStmt = $pdo->prepare("SELECT User.User_ID, Karaoke_Files.File_ID, Songs.Title, Contributors.Name AS Artist
                                  FROM Queue 
                                  JOIN Karaoke_Files ON Queue.File_ID = Karaoke_Files.File_ID
                                  JOIN User ON Queue.User_ID = User.User_ID 
                                  JOIN Versions ON Queue.Version_ID = Versions.Version_ID
                                  JOIN Songs ON Versions.Song_ID = Songs.Song_ID
                                  JOIN Song_Contributors ON Songs.Song_ID = Song_Contributors.Song_ID
                                  JOIN Contributors ON Song_Contributors.Contributor_ID = Contributors.Contributor_ID
                                  WHERE Queue.Queue_Type = 'singing' 
                                  ORDER BY Queue.Queue_ID ASC 
                                  LIMIT 1");
$singingSongStmt->execute();
$singingSong = $singingSongStmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DJ Interface</title>
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

        footer {
            background-color: #333;
            color: #fff;
            padding: 10px;
            text-align: center;
        }

        table {
            border-collapse: collapse;
            margin: 20px 0;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #333;
            color: #fff;
        }

        .queued-songs-header {
            font-weight: bold;
            font-size: 1.2em;
            margin-bottom: 10px;
        }

        .singing-song-header {
            font-weight: bold;
            font-size: 1.2em;
            margin-bottom: 10px;
        }

    </style>
</head>
<body>
<header>
    <nav>
<ul>
    <li><a href='website.php'>Home</a></li>
    
</ul>
</header>
<main>
    <section>
        <h1>DJ Interface</h1>
        <h2>Queued Songs</h2>
        <div class="queued-songs-header">Currently queued songs:</div>
        <table>
            <thead>
            <tr>
                <th>Username</th>
                <th>Title</th>
                <th>Artist</th>
                <th>File</th>
            </tr>
            </thead>
            <tbody>
            
                
            <?php foreach ($queuedSongs as $song): ?>
                <tr>
                    <td><?= $song['0'] ?></td>
                    <td><?= $song['1'] ?></td>
                    <td><?= $song['2'] ?></td>
                    <td><a href="<?= $song['3'] ?>">Download</a></td>
                </tr>
                
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>

<section>
    <h2>Singing Song</h2>
    <?php if ($singingSong): ?>
        <div class="singing-song-header">Currently singing:</div>
        <table>
            <thead>
            <tr>
                <th>Username</th>
                <th>Title</th>
                <th>Artist</th>
                <th>File</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?= $singingSong['username'] ?></td>
                <td><?= $singingSong['title'] ?></td>
                <td><?= $singingSong['Artist'] ?></td>
                <td><a href="<?= $singingSong['Song_ID'] ?>">Download</a></td>
            </tr>
            </tbody>
        </table>
    <?php else: ?>
        <p>No one is currently singing.</p>
    <?php endif; ?>
</section>
</main>
<footer>
    &copy; 2023 DJ Interface
</footer>
</body>
</html>
