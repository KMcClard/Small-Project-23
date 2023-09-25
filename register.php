<?php
// index.php

// Start or resume the session
session_start();

// Include the API script
include("api.php");
?>

<!DOCTYPE html>
<html>
<head>
    <title>CRUD Sign-up</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
	
	<style>
		body {
			background-color: linen;
		}
	</style>
</head>
<body>
    <label form="signupForm"><b>Create a New Account</b></label>
	<br><br>

    <form id="signupForm" method="post" onsubmit="submitRegis(event)">
        <label for="email">EMAIL</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="username">USERNAME</label><br>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">PASSWORD</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <label for="firstName">FIRST NAME</label><br>
        <input type="text" id="firstName" name="firstName" required><br><br>

        <label for="lastName">LAST NAME</label><br>
        <input type="text" id="lastName" name="lastName" required><br><br>

        <label for="phone">PHONE</label><br>
        <input type="number" id="phone" name="phone" required><br><br>

        <input type="submit" id="signupButton" value="      SIGN UP      "><
    </form>

    <!--Hyperlink to the login page-->
    <br><a id="loginLink" href="index.html"> Return to login</a>

    <!--Dynamic message space. Some errors are printed to here from the js file-->
    <br><div id="message"></div>

    <script src="register.js"></script>
</body>
</html>
