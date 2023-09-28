<?php
// contact.php

// Include the API script
include("api.php");

// Start or resume the session
session_start();

//Set the clientID variable to send to JS (set to the empty string if it doesnt)
$clientID = (isset($_SESSION['id']))?$_SESSION['id']:'';
?>

<!DOCTYPE html>
<html>
<head>
	<title>CRUD</title>	
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

	<style>
		body {
			background-color: linen;
		}
		
		.grid {
			display: grid;
			grid-template-columns: auto auto auto auto auto;
			row-gap: 0px;
			column-gap: 0px;
			background-color: linen;
			padding: 10px;
		}
		
		.gridHeader {
			background-color: RoyalBlue;
			border: 1px solid rgba(0, 0, 0, 0.8);
			padding: 10px;
			font-size: 30px;
			text-align: center;
		}
		
		.gridItem {
			background-color: LightCyan;
			border: 1px solid rgba(0, 0, 0, 0.8);
			padding: 10px;
			font-size: 30px;
			text-align: center;
		}
		
	</style>
</head>
<body>
	<div class="container-fluid mb-5">
		<div class="row" style="font-size: 24px; background-color: LightCyan;">
				<b>CRUD</b>
		</div>
	</div>
	
	<div class="container">
		<div class="row">
			<div class="col">
				<form id="searchForm" method="login" onsubmit="searchContacts(event)">
					<label>SEARCH</label>
					<input type="text" id="searchBox" name="searchBox">
					<input type="submit" id="searchButton" value="      GO      ">
				</form>
			</div>
		</div>
		
		<div class="row mt-2">
			<div class="grid">
				<!-- Row 0, grid headers-->
				<div class="gridHeader">
					<label><b>FIRST</b></label>
				</div>
				<div class="gridHeader">
					<label><b>LAST</b></label>
				</div>
				<div class="gridHeader">
					<label><b>PHONE</b></label>
				</div>
				<div class="gridHeader">
					<label><b>EMAIL</b></label>
				</div>
				<div class="gridHeader">
					<!-- empty space -->
				</div>
				
				<!-- Grid spots (row 1) to be filled by contact info-->
				<div class="gridItem">
					<label id="gridFirst1">Apple</label>
				</div>
				<div class="gridItem">
					<label id="gridLast1">Sauce</label>
				</div>
				<div class="gridItem">
					<label id="gridPhone1">8005093345</label>
				</div>
				<div class="gridItem">
					<label id="gridEmail1">asauce@hotmail.com</label>
				</div>
				<div class="gridItem">
					<button id="gridEdit1">+</button>
				</div>
				
				<!-- Grid spots (row 2) to be filled by contact info-->
				<div class="gridItem">
					<label id="gridFirst2"></label>
				</div>
				<div class="gridItem">
					<label id="gridLast2"></label>
				</div>
				<div class="gridItem">
					<label id="gridPhone2"></label>
				</div>
				<div class="gridItem">
					<label id="gridEmail2"></label>
				</div>
				<div class="gridItem">
					<button id="gridEdit2">+</button>
				</div>
				
				<!-- Grid spots (row 3) to be filled by contact info-->
				<div class="gridItem">
					<label id="gridFirst3"></label>
				</div>
				<div class="gridItem">
					<label id="gridLast3"></label>
				</div>
				<div class="gridItem">
					<label id="gridPhone3"></label>
				</div>
				<div class="gridItem">
					<label id="gridEmail3"></label>
				</div>
				<div class="gridItem">
					<button id="gridEdit3">+</button>
				</div>
				
				<!-- Grid spots (row 4) to be filled by contact info-->
				<div class="gridItem">
					<label id="gridFirst4"></label>
				</div>
				<div class="gridItem">
					<label id="gridLast4"></label>
				</div>
				<div class="gridItem">
					<label id="gridPhone4"></label>
				</div>
				<div class="gridItem">
					<label id="gridEmail4"></label>
				</div>
				<div class="gridItem">
					<button id="gridEdit4">+</button>
				</div>
				
				<!-- Grid spots (row 5) to be filled by contact info-->
				<div class="gridItem">
					<label id="gridFirst5"></label>
				</div>
				<div class="gridItem">
					<label id="gridLast5"></label>
				</div>
				<div class="gridItem">
					<label id="gridPhone5"></label>
				</div>
				<div class="gridItem">
					<label id="gridEmail5"></label>
				</div>
				<div class="gridItem">
					<button id="gridEdit5">+</button>
				</div>
				
				<!-- Grid spots (row 6) to be filled by contact info-->
				<div class="gridItem">
					<label id="gridFirst6"></label>
				</div>
				<div class="gridItem">
					<label id="gridLast6"></label>
				</div>
				<div class="gridItem">
					<label id="gridPhone6"></label>
				</div>
				<div class="gridItem">
					<label id="gridEmail6"></label>
				</div>
				<div class="gridItem">
					<button id="gridEdit6">+</button>
				</div>
			</div>
		</div>
		
		<div class="row mt-2 justify-content-evenly">
			<div class="col text-start">
				<button id="prevPage">Previous Page</button>
			</div>
			
			<div class="col text-end">
				<button id="nextPage">Next Page</button>
			</div>
		</div>
	</div>
	
	<!--Dynamic message space. Some errors are printed to here from the js file-->
	<div id="message"></div>

	<!--Send the ID variable over to JS-->
	<script type="text/javascript">
		const clientID = '<?php echo $clientID?>'; 
	</script>
	<script src="contact.js"></script>
</body>
</html>