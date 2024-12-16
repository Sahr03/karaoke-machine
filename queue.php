<?php
include("secret.php");

try {
    $dsn = "mysql:host=courses;dbname=z1930087";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
} catch (PDOException $e) {
    echo "Connection to database failed: " . $e->getMessage();
}

if(isset($_POST['add_song'])) {
    $file_id = $_POST['file_id'];
    $queue_type = $_POST['queue_type'];

    if($queue_type === 'Accelerated') {
        $queue_type = 'Accelerated';
    } else {
        $queue_type = 'Free';
    }

    $queuedSongsStmt = $pdo->prepare("INSERT INTO Queue(Queue_Type, TIME, File_ID, User_ID) VALUES (:queue_type, NOW(), :file_id, 1)");
    $queuedSongsStmt->bindParam(':queue_type', $queue_type);
    $queuedSongsStmt->bindParam(':file_id', $file_id);
    $queuedSongsStmt->execute();
}

$songStmt = $pdo->prepare("SELECT songs.song_id, songs.title, contributors.name AS artist, versions.file_id FROM songs 
                           JOIN song_contributors ON song_contributors.song_id = songs.song_id
                           JOIN contributors ON contributors.contributor_id = song_contributors.contributor_id
                           JOIN versions ON versions.song_id = songs.song_id");
$songStmt->execute();
$songs = $songStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Song to Queue</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
<div class="container text-center">
    <h1>Add Song to Queue</h1>
    <form method="post" action="queue.php">
        <div class="form-group">
            <label for="song">Song:</label>
            <select class="form-control" id="song" name="file_id">
                <?php foreach($songs as $song) { ?>
                    <option value="<?php echo $song['file_id']; ?>"><?php echo $song['title'] . ' - ' . $song['artist']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label for="queueType">Queue Type:</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="queue_type" value="Free" checked>
                <label class="form-check-label" for="queueType">
                    Free
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="queue_type" value="Accelerated">
                <label class="form-check-label" for="queueType">
                    Accelerated (adds $5 to your bill)
                </label>
            </div>
        </div>
        <button type="submit" name="add_song" class="btn btn-primary">Add to Queue</button>
    </form>
</div>
</body>
</html>
