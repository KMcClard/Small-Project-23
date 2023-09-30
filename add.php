<?php
// contact.php

// Include the API script
include("api.php");

// Start or resume the session
session_start();

//Set the clientID variable to send to JS (set to the empty string if it doesnt exist)
$clientID = (isset($_SESSION['id']))?$_SESSION['id']:'';
?>

<!DOCTYPE html>
<html>
<head>
<title>CRUD Contact Creator</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
	
	<style>
		body {
			background-color: linen;
		}

		h1 {
			color: maroon;
			margin-left: 4px;
		}
		
		.topBar {
			margin: auto;
			width: 100%;
			padding-left: 8px;
			text-align: left;
			font-size: 24px;
			background-color: LightCyan;
		}
		
		.centerWindow {
			margin-top: auto;
			width: 50%;
			border-radius: 32px;
			border: 3px solid RoyalBlue;
			padding: 10px;
			text-align: center;
			background-color: LightCyan;
		}
		
		.centerWindow2 {
			border-radius: 32px;
			border: 3px solid RoyalBlue;
			background-color: LightCyan;
		}
	</style>
</head>


<body>
	<!--Banner-->
    <div class="container-fluid mb-5">
		<div class="row" style="font-size: 24px; background-color: LightCyan;">
				<b>CRUD</b>
		</div>
	</div>

	<!--Login Form-->
    <div class="container text-center">
		<div class="row align-items-center">
			<div class="col p-2" style="border-radius: 32px; border: 3px solid RoyalBlue; background-color: LightCyan;">
				<h1>Add Contact</h1>
				<hr>
				<br>
				
                <form id="addForm" method="add" onsubmit="addContact(event)">
                    <label for="firstAdd">First</label><br>
                    <input type="text" id="firstAdd" name="firstAdd" required><br><br>

                    <label for="lastAdd">Last</label><br>
                    <input type="text" id="lastAdd" name="lastAdd" required><br><br>
					
					<label for="emailAdd">Email</label><br>
                    <input type="email" id="emailAdd" name="emailAdd" required><br><br>
					
					<label for="phoneAdd">Phone</label><br>
                    <input type="number" id="phoneAdd" name="phoneAdd" required><br><br>

                    <input type="submit" id="addButton" value="      ADD      "><br>
                </form>

				<input type="button" id="cancelButton" value="      CANCEL      " onclick="leavePage()">
				
			</div>
		</div>
	</div>

    <!--Dynamic message space. Some errors are printed to here from the js file-->
    <div id="message"></div>

	<!--Send the ID variable over to JS-->
	<script type="text/javascript">
		const clientID = '<?php echo $clientID?>'; 
	</script>
    <script src="add.js"></script>
</body>
</html>
