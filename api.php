<?php

    //Create Operation
    function createUser($con) {
    
        // Decode the incoming request (php://input is an input stream with raw JSON from the HTTP request body)
        $data = json_decode(file_get_contents("php://input"));
    
        //Pulling data from the json
        $firstname = $data->firstName;
        $lastname = $data->lastName;
        $username = $data->username;
        $email = $data->email;
        $password = $data->password;
    
        // Safely insert the data with a prepared statement
        $stmt = $con->prepare("INSERT INTO userLogins (FirstName, LastName, Username, Email, PasswordHashed) VALUES (?, ?, ?, ?, ?)");
    
        // Bind parameters
        $stmt->bind_param("sssss", $firstname, $lastname, $username, $email, $password);
    
        try {
            //Attempting to create user
            $stmt->execute();
            echo json_encode(["message" => "> Registration Complete!<br>"]);
            
            /* Consider moving this based on if we make them login after registration */ 
            $_SESSION["username"] = $username; // Corrected property name
            $_SESSION["password"] = $password; // Corrected property name

        } catch (mysqli_sql_exception $e) {
            //Only exception possible is if username is taken
            echo json_encode(["message" => "> That username is taken!<br>"]);
            
        }
    
        //Ending process
        $stmt->close();
    
    }

    //READ Operation
    function read($con) {

        /* Need to obtain the id for the logged in user */
        $id = $_SESSION['clientID']

        //Safely obtain the data with a prepared statement
        $stmt = $con->prepare("SELECT * FROM UserContacts WHERE clientID = '$id'");
            
        try {
            //Query the database
            $stmt->execute();
            $result = $stmt->get_result();

            //Endoce the retrieved data
            if($result->num_rows > 0) {
                    
                echo json_encode($result);

            } else {
                echo json_encode(["message" => "> This database was empty!<br>"]);
            }

        //Massive database error
        } catch(mysqli_sql_exception $e) {
            echo json_encode(["error" => "> Database Error!<br>" . $e->getMessage() . "<br>"]);
        }

        //Closing Query
        $stmt->close();
    }


    

    

    //----------------Dispatcher----------------

    //Connecting to the database
	$hostname = "localhost";
	$username = "superadmin";
	$password = "superpassword";
	$database = "SmallProj23";

    try {
        $con = mysqli_connect($hostname, $username, $password,$database);
    } catch(mysqli_sql_exception $e) {

        die("Unable to Connect to the Database!<br>" . $e->getMessage(). "<br>");
    }

    $method = $_SERVER['REQUEST_METHOD'];

    switch($method) {
        case 'POST':
            createUser($con);
            $con.close();
            break;
	//Make Method Specific to Login
        case 'GET':
            read($con)
            $con.close();
            break;
	//Make Method Specific to User Data

	    
        case 'PUT':
            break;
        case 'DELETE':
            break;
        default:
            echo json_encode(["error" => "> Request Error!<br>"]);
    }
?>
