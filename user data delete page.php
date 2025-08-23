<?php
require 'database.php';

$id = 0;

if (!empty($_GET['id'])) {
    $id = $_GET['id'];
}

if (!empty($_POST)) {
    // keep track post values
    $id = $_POST['id'];

    try {
        // delete data
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Quote "id" for PostgreSQL
        $sql = 'DELETE FROM table_the_iot_projects WHERE "id" = ?';
        $q = $pdo->prepare($sql);
        $q->execute(array($id));

        Database::disconnect();

        // Redirect to user_data.php (no spaces)
        header("Location: user_data.php");
        exit;
    } catch (PDOException $e) {
        die("Error deleting data: " . $e->getMessage());
    }
}
?>

 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link   href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/bootstrap.min.js"></script>
	<title>Delete : NodeMCU V3 ESP8266 / ESP12E with MYSQL Database</title>
</head>
 
<body>
	<h2 align="center">NodeMCU V3 ESP8266 / ESP12E with MYSQL Database</h2>

    <div class="container">
     
		<div class="span10 offset1">
			<div class="row">
				<h3 align="center">Delete User</h3>
			</div>

			<form class="form-horizontal" action="user data delete page.php" method="post">
				<input type="hidden" name="id" value="<?php echo $id;?>"/>
				<p class="alert alert-error">Are you sure to delete ?</p>
				<div class="form-actions">
					<button type="submit" class="btn btn-danger">Yes</button>
					<a class="btn" href="user data.php">No</a>
				</div>
			</form>
		</div>
                 
    </div> <!-- /container -->
  </body>
</html>