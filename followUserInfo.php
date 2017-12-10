<?php
    // check whether the user has logged in
    session_start();
    if (!isset($_SESSION['Username'])) {
	header("Location: logout.php");
    } else if (!isset($_GET['name']) || $_GET['name'] == '') {
	header("location: userInfo.php");
    }
    include('ini_db.php');
?>

<!doctype html>
<html lang="en">
<head>
    <title>Info</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <link rel="stylesheet" href="./CSS/followUserInfo.css">
</head>

<body>
<?php include("./includes/navigation_bar.html"); ?>

    <div id="title">
	<?php echo "<h1>" . $_GET['name'] . "'s Page</h1>"; ?>
    </div>
<?php
    // check whether following or not
    $check_follow = "SELECT *
		     FROM Follow
		     WHERE Username1 = \"" . $_SESSION["Username"] . 
		     "\" AND Username2 = \"" . $_GET["name"] . "\"";
    $check_result = $conn->query($check_follow);
    if (($check_result->num_rows) > 0) {
	$status = "Unfollow";
    } else {
	$status = "Follow";
    }
?>
    <div id="followbutton"> 
	<form action="follow.php" method="post">
	    <?php
		 echo "<input type=\"hidden\" name=\"followee\" value=" . $_GET["name"] . ">"; 
		 echo "<input type=\"hidden\" name=\"action\" value=" . $status . ">"; 
		 echo "<input type=\"submit\" value=" . $status . ">"; 
	    ?>
	</form>
    </div>

<?php
    // get artist info
    $userName = $_GET['name'];

    //show user info 
    $r1 = $conn->prepare("SELECT * FROM User WHERE Username = ?");
    $r1->bind_param('s', $userName);
    $r1->execute();
   
    $info_result = $r1->get_result();
    echo "<div id=\"info\">";
    while ($row = $info_result->fetch_assoc()) {
	echo "<p>Name: " . $row['Name'] . "</p>";
	echo "<p>Email: " . $row['Email'] . "</p>";
	echo "<p>City: " . $row['City'] . "</p>";
    }
    echo "</div>";
    $r1->close();
    

    //show likes
    $likes = $conn->prepare("SELECT * 
			    FROM Likes NATURAL JOIN Artist
			    WHERE Username = ?");
    $likes->bind_param('s', $userName);
    $likes->execute();
    $likes_result = $likes->get_result();
    echo "<div id=\"artist\">";
    echo "The artists " . $userName . " likes: ";
    echo "<table id=\"artisttable\">";

    while ($row = $likes_result->fetch_assoc()) {
	echo "<tr>";
	echo "<td><a href=\"artist.php?artist=" . $row['ArtistId'] . "\">" .$row['ArtistTitle'] . "</a></td>";
	echo "<td>" . $row['ArtistDescription'] . "</td>"; 
	echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    $likes->close();


    //show follow user
    $follow= $conn->prepare("SELECT Username2  
			     FROM Follow
			     WHERE Username1 = ?");
    $follow->bind_param('s', $userName);
    $follow->execute();
    $follow_result = $follow->get_result();
    echo "<div id=\"follow\">";
    echo "The users " . $userName . " follows:";
    echo "<table id=\"followtable\">";
    while ($row = $follow_result->fetch_assoc()) {
	echo "<tr>";
	echo "<td><a href=\"FollowUserInfo.php?name=" . $row['Username2'] . "\">" . $row['Username2'] . "</a></td>";  //here need to change
	//echo "<td><a href=\"album.php?album=" . $row['AlbumId'] . "\">" .$row['AlbumName'] . "</a></td>";
	echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    $follow->close();
    $conn->close();

?>


<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
</body>
</html>