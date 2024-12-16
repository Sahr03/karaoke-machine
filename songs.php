<!DOCTYPE html>
<html>
<head>
    <title>Song List</title>
    <!-- Bootstrap CDN for styling -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <!-- Custom CSS for link color -->
    <style>
        a {
            color: black !important;
        }
    </style>
</head>
<body>
    <div class="container text-center">
        <h1>Song List</h1>
        <form method="post" action="songs.php">
            <div class="row">
                <div class="col-md-8 mx-auto mb-3">
                    <input type="text" class="form-control" name="search_song" placeholder="Search for a song...">
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 mx-auto mb-3">
                    <input type="text" class="form-control" name="search_artist" placeholder="Search for an artist...">
                </div>
            </div>
            <div class="row">
                <?php
                include("secret.php");

                try {
                    $dsn = "mysql:host=courses;dbname=z1930087";
                    $pdo = new PDO($dsn, $username, $password);
                } catch (PDOException $e) {
                    echo "Connection to database failed: " . $e->getMessage();
                }

                $sql = "SELECT Songs.Song_ID, Songs.Title, Contributors.Name as Artist
                        FROM Songs
                        INNER JOIN Song_Contributors ON Songs.Song_ID = Song_Contributors.Song_ID
                        INNER JOIN Contributors ON Song_Contributors.Contributor_ID = Contributors.Contributor_ID
                        WHERE Contributors.Role = 'Artist'";

                if (isset($_POST['search_song'])) {
                    $search_song = $_POST['search_song'];
                    $sql .= " AND Songs.Title LIKE '%$search_song%'";
                }

                if (isset($_POST['search_artist'])) {
                    $search_artist = $_POST['search_artist'];
                    $sql .= " AND Contributors.Name LIKE '%$search_artist%'";
                }

                $result = $pdo->query($sql);

                if ($result->rowCount() > 0) {
                        // output data of each row
                        echo '<table class="mx-auto">
                                <tr>
                                    <th> Song ID </th>
                                    <th> Title </th>
                                    <th> Artist </th>
                                    <th> Pick your song </th>
                                    <th> Accelerated $5.00 Fee </th>
                                </tr>';
                        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                            echo '<tr>
                                    <td>' . $row["Song_ID"] . '</td>
                                    <td>' . $row["Title"] . '</td>
                                    <td>' . $row["Artist"] . '</td>
									<td><input type="radio" name="song" value="' . $row["Song_ID"] . '" form = "box" ></td>
 
									<td><input type="radio" name="accelerated" value="5" form = "box" ></td>
                                </tr>';
                        }
                        echo '</table>';
                } else {
                    echo "0 results";
                }

                $pdo = null;
                ?>
            </div>
            <div class="row mt-4">
                <div class="col">
				<form method="post" action="queue.php" id ="box">
                    <button type="submit" class="btn btn-primary">Add Selected Songs to Queue</button>
               

                </div>
                <div class="col">
                    <a href="website.php" class="btn btn-secondary">Back</a>
                </div>
            </div>
			</form>
	</div>
</body>
</html>
