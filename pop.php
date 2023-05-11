<!DOCTYPE html>
<html>
<head>
	<title>My Website</title>
	<style>
		table {
			border-collapse: collapse;
			width: 100%;
			margin-bottom: 20px;
		}
		table, th, td {
			border: 1px solid black;
			padding: 5px;
		}
		th {
			background-color: #f2f2f2;
		}
		form {
			margin-bottom: 20px;
		}
		label {
			display: block;
			margin-bottom: 5px;
		}
		input[type="text"] {
			padding: 5px;
			border-radius: 3px;
			border: 1px solid #ccc;
			width: 100%;
			box-sizing: border-box;
		}
		input[type="submit"] {
			padding: 5px 10px;
			border-radius: 3px;
			background-color: #4CAF50;
			color: white;
			border: none;
			cursor: pointer;
		}
		input[type="submit"]:hover {
			background-color: #3e8e41;
		}
		.error {
			color: red;
			margin-bottom: 10px;
		}
		.success {
			color: green;
			margin-bottom: 10px;
		}

		<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Operating System</th>
        <th>IPv4</th>
        <th>Action</th> <!-- new column for delete button -->
    </tr>

</table>
	</style>
</head>
<body>
	<div class="container">
		<h1>Welcome to my website!</h1>
		<p>This website is a device management</p>
    <!-- Form for adding data -->
	<form method="post">
		<label for="id">ID:</label>
		<input type="text" name="id" id="id" required>
		<label for="name">Name:</label>
		<input type="text" name="name" id="name" required>
		<label for="os">Operating System:</label>
		<input type="text" name="os" id="os" required>
		<label for="ipv4">IPv4:</label>
		<input type="text" name="ipv4" id="ipv4" required>
		<input type="submit" name="submit" value="Add Data">
	</form>
	
	<table>
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Operating System</th>
			<th>IPv4</th>
		</tr>

		<?php

		// schakel warinings uit op de webpage
		error_reporting(E_ERROR);
		ini_set('display_errors', 1);

		//Er worden variabelen gedefinieerd voor de servernaam, gebruikersnaam, wachtwoord en database-naam.
		include('config.php');

		// verbinden met database
		$conn = mysqli_connect($config['servername'], $config['username'], $config['password'], $config['dbname']);

   		 // Check connection
   		 if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    	}

  	  //Als er een POST-request is ontvangen en de parameter 'delete_id' is ingesteld, dan wordt het apparaat met de overeenkomende id verwijderd uit de database.
		if(isset($_POST['delete_id'])) {
			$delete_id = mysqli_real_escape_string($conn, $_POST['delete_id']);
			$sql = "DELETE FROM device WHERE id='$delete_id'";
			if(mysqli_query($conn, $sql)) {
				echo "<div class='success'>Data deleted successfully.</div>";
			} else	 {
				$error_message = "Error deleting data: " . mysqli_error($conn);
				echo "<div class='error'>" . $error_message . "</div>";
				file_put_contents('error.txt', $error_message, FILE_APPEND);
			}
		}
  
    //Als er een POST-request is ontvangen, worden de invoervelden gevalideerd en wordt de data in de database ingevoegd als de invoer correct is.
	if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate input data
    $id = mysqli_real_escape_string($conn, $_POST["id"]);
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $os = mysqli_real_escape_string($conn, $_POST["os"]);
    $ipv4 = mysqli_real_escape_string($conn, $_POST["ipv4"]);

    // check data
    if (empty($id) || empty($name) || empty($os) || empty($ipv4)) {
		echo "<div class='error'>Please fill in all fields.</div>";
	} elseif (!filter_var($ipv4, FILTER_VALIDATE_IP)) {
		echo "<div class='error'>Invalid IPv4 address.</div>";
	} else {
		// Use prepared statements to insert data into database
		$stmt = mysqli_prepare($conn, "INSERT INTO device (id, name, os, ipv4) VALUES (?, ?, ?, ?)");
		mysqli_stmt_bind_param($stmt, "ssss", $id, $name, $os, $ipv4);

		if (mysqli_stmt_execute($stmt)) {
			echo "<div class='success'>Data inserted successfully.</div>";
		} else {
			$error_message = "Error inserting data: " . mysqli_error($conn);
			echo "<div class='error'>" . $error_message . "</div>";
			error_log($error_message, 3, "error.log");
		}

		mysqli_stmt_close($stmt);
	}
}
// Test error message
$error_message = "This is a test error message.";
echo "<div class='error'>" . $error_message . "</div>";
error_log($error_message, 3, "error.log");
file_put_contents('error.log', $error_message_test, FILE_APPEND);


 
    // De data van alle apparaten wordt opgehaald uit de database en in een tabel weergegeven.
    $sql = "SELECT * FROM device";
    $result = @mysqli_query($conn, $sql);

    // Elke rij in de tabel bevat een knop waarmee het bijbehorende apparaat kan worden verwijderd uit de database.
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $row["name"] . "</td>";
            echo "<td>" . $row["os"] . "</td>";
            echo "<td>" . $row["ipv4"] . "</td>";
            echo "<td>";
            echo "<form method='POST'>";
            echo "<input type='hidden' name='delete_id' value='" . $row["id"] . "'/>";
            echo "<button type='submit'>Delete</button>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='5'>0 results</td></tr>";
    }

    // De connectie met de database wordt gesloten.
    mysqli_close($conn);
?>

	</table>
</body>
</html>
