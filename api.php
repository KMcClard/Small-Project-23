<!DOCTYPE html>
<html>
<body>
<?php

	$hostname = "localhost";
	$username = "superadmin";
	$password = "superpassword";
	$database = "SmallProj23";

	$conn = new mysqli($hostname, $username, $password, $database);

	// Check the connection
	if ($conn->connect_error) {
     		die("Connection failed: " . $conn->connect_error);
	}

	$function = $_GET['func'] ?? '';
	$column_name = $_GET['column_name'] ?? '';

	if($function == "1")
	{	
		if(!empty($column_name))
		{
		
			$query = "SELECT * FROM userLogins";
			$result = $conn->query($query);

			if($result->num_rows > 0)
			{
				$data = [];

				while($row = $result->fetch_assoc())
				{
					$data[] = [
						"testdata" => $row[$column_name],	
					];
				}
				$response = ["data" => $data];
			}
			else
			{
				$response = ["message" => "No data found in the database."];
			}
		}
		else
		{
			$response = ["error" => "Invalid or missing 'column_name' parameter in the url."];
		}	
	}
	$conn->close();
	echo json_encode($response);
?>


</body>
</html>
