<?php

//Include
include('api_helper.php');

//----------------Dispatcher----------------

//Set connection parameters
$hostname = "localhost";
$username = "superadmin";
$password = "superpassword";
$database = "SmallProj23";

//Attempt to connect to the database
try {
    $con = mysqli_connect($hostname, $username, $password, $database);

} catch(mysqli_sql_exception $e) {

    die("Unable to Connect to the Database!" . $e->getMessage(). "<br>");
}

//Determine which function should be called based off the request method
if ($_SERVER['REQUEST_METHOD'] === 'REGIS') {
    regisUser($con);

} else if ($_SERVER['REQUEST_METHOD'] === 'LOGIN') {
    loginUser($con);

} else if ($_SERVER['REQUEST_METHOD'] === 'LOAD') {
    if(isset($_GET['pageNum']) && isset($_GET['substring'])){
	    $currentPage = $_GET['pageNum'];
	    $substringIndex = $_GET['substring'];
	    $filter = $_GET['filter'];
    	loadContacts($con,$currentPage,$substringIndex . "%",$filter);

    } else if(isset($_GET['pageNum'])) {
	    $currentPage = $_GET['pageNumber'];
	    loadContacts($con,$pageNumber,"%","FirstName");

    } else if(isset($_GET['substring'])) {
	    $substringIndex = $_GET['substring'];
        loadContacts($con,1,$substringIndex . "%","FirstName");

    } else loadContacts($con,1,"%","FirstName");


} else if ($_SERVER['REQUEST_METHOD'] === 'UPDT') {
    updateContact($con);

} else if ($_SERVER['REQUEST_METHOD'] === 'DELET') {
    deleteContact($con);

} else if ($_SERVER['REQUEST_METHOD'] === 'ADD') {
    addContact($con);
}



//User would like to register an account
function regisUser($con) {
    //Double checking request method
    if ($_SERVER['REQUEST_METHOD'] === 'REGIS') {
        // Decode the incoming request (php://input is an input stream with raw JSON from the HTTP request body)
        $requestData = json_decode(file_get_contents("php://input"), true);

        //Pulling data from the json
        $firstname = $requestData['firstname'];
        $lastname = $requestData['lastname'];
        $email = $requestData['email'];
        $username = $requestData['username'];
        $password = $requestData['password'];
        $phone = $requestData['phone'];

        //Hash the password
        $hash = password_hash($password, PASSWORD_DEFAULT);

        /*If the stmts aren't in the format below, we are prone to SQL injections! Please have values '?' in the con->prepare and only specify them in bind_params!*/

        //Safely preform an INSERT query using a prepared statement
        $stmt = $con->prepare("INSERT INTO userLogins (FirstName, LastName, Email, Username, PasswordHashed, Phone) VALUES (?, ?, ?, ?, ?, ?)");
        //Bind the anonymous parameters
        $stmt->bind_param("ssssss", $firstname, $lastname, $email, $username, $hash, $phone);

        //Attempt to execute our query
        try {
            $stmt->execute();
            $response = ['success' => true];
            
            //Terminating query
            $stmt->close();
            
            //Grab the current user's ID
            $clientID = fetchID($con, $username, $hash);
            if($clientID > 0) {

                //Begin the session and save some information about the user
                session_start();
                $_SESSION['id'] = $clientID;
                $_SESSION['username'] = $username;
                $_SESSION['firstname'] = $firstname;
                $_SESSION['lastname'] = $lastname;
                $_SESSION['email'] = $email;
                $_SESSION['phone'] = $phone;

            } else $response = ['success' => false]; //Returned value was not a valid ID


        //Either the user entered a duplicate username, or there was an issue contacting the database
        } catch(mysqli_sql_exception) {$response = ['success' => false];}

        // Return JSON response
        header("Content-Type: application/json");
        echo json_encode($response);
    }
}

//User is trying to log in
function loginUser($con) {
    //Double checking request method
    if ($_SERVER['REQUEST_METHOD'] === 'LOGIN') {
        // Decode the incoming request (php://input is an input stream with raw JSON from the HTTP request body)
        $requestData = json_decode(file_get_contents("php://input"), true);

        //Pulling data from the json
        $username = $requestData['username'];
        $password = $requestData['password'];

        /*If the stmts aren't in the format below, we are prone to SQL injections! Please have values '?' in the con->prepare and only specify them in bind_params!*/

        //Safely preform a SELECT query using a prepared statement
        $stmt = $con->prepare("SELECT PasswordHashed FROM userLogins WHERE Username=?");
        //Bind the anonymous parameters
        $stmt->bind_param("s", $username);

        //Attempt to execute our query
        try {
            $stmt->execute();
            $result = $stmt->get_result();

            //Check if anything was actually retrieved from the database
            if($result->num_rows > 0) {
                $hashedP = $result->fetch_assoc()["PasswordHashed"]; //Grab the saved password

                //If the provided password matched the password we had on record, user is who they claim to be. Otherwise do not permit them entry to the specified account
                if(password_verify($password, $hashedP)) {
                    $response = ['success' => true];

                    //Terminating query
                    $stmt->close();

                    //Grab what information about the user that is already stored 
                    $userInfo = fetchUserInfo($con, $username, $hashedP);
                    if(!empty($userInfo)) {

                        //Begin the session and save some information about the user
                        session_start();
                        $_SESSION['username'] = $username;
                        $_SESSION['id'] = $userInfo['id'];
                        $_SESSION['firstname'] = $userInfo['firstname'];
                        $_SESSION['lastname'] = $userInfo['lastname'];
                        $_SESSION['email'] = $userInfo['email'];
                        $_SESSION['phone'] = $userInfo['phone'];

                    } else $response = ['success' => false]; //Returned array was empty

                } else $response = ['success' => false]; //Bad password

            //If nothing was retrieved from the data table, then the user specified did not exist
            } else $response = ['success' => false];


        //Error communicating with the database
        } catch(mysqli_sql_exception) {$response = ['success' => false];}

        // Return JSON response
        header("Content-Type: application/json");
        echo json_encode($response);
    }
}

//Front-End is requesting a list of the current user's contacts
function loadContacts($con,$pageNumber,$substring,$searchParam) {
    //Double checking request method
    if ($_SERVER['REQUEST_METHOD'] === 'LOAD') {
        // Decode the incoming request (php://input is an input stream with raw JSON from the HTTP request body)
        $requestData = json_decode(file_get_contents("php://input"), true);

        //Pulling data from the json
	    $clientID = $requestData['id'];
	    $calcOffset = $pageNumber * 15;
	

        /*If the stmts aren't in the format below, we are prone to SQL injections! Please have values '?' in the con->prepare and only specify them in bind_params!*/

        //Safely preform a SELECT query using a prepared statement
        // $stmt = $con->prepare("SELECT * , 'test' AS testCol FROM userContacts WHERE (ownerID=?)");
	    //$stmt = $con->prepare("SELECT *, (SELECT COUNT(*) FROM userContacts WHERE ownerID = ? AND FirstName LIKE ? ) AS contactCount FROM userContacts WHERE (ownerID=?) AND FirstName LIKE ? LIMIT 6 OFFSET ?");
        $querySearch = "SELECT *, (SELECT COUNT(*) FROM userContacts WHERE ownerID = ? AND " .  $searchParam . " LIKE ? ) AS contactCount FROM userContacts WHERE (ownerID=?) AND " . $searchParam . " LIKE ? LIMIT 15 OFFSET ?";
	    $stmt = $con->prepare($querySearch);	
	    //Bind the anonymous parameters
        $stmt->bind_param("isisi",$clientID,$substring,$clientID,$substring,$calcOffset);

        //Attempt to execute our query
        try {
            $stmt->execute();

            //Bind the query results to the specified variables
            $stmt->bind_result($contactID, $ownerID, $firstName, $lastName, $phone, $email,$contactCount);

	    $contacts = [];

            //Populate an array of arrays with contact information
            while($stmt->fetch()) {
                $contacts[] = [
                    'firstname' => $firstName,
                    'lastname' => $lastName,
                    'email' => $email,
                    'phone' => $phone,
		    'contactID' => $contactID,
		];
            }
		
	    if($contactCount == null) $contactCount = 0;
	
	
        $response = ['success' => true, 'contacts' => $contacts, 'debug' => $contactCount, 'search' => $substring, 'column' => $searchParam]; //Send the contacts in the JSON file

        //Error communicating with the database
        } catch(mysqli_sql_exception) {$response = ['success' => false];}

        // Return JSON response
        header("Content-Type: application/json");
        echo json_encode($response);
    }
}


//User would like to update a contact's information
function updateContact($con) {
    //Double checking request method
    if ($_SERVER['REQUEST_METHOD'] === 'UPDT') {
        // Decode the incoming request (php://input is an input stream with raw JSON from the HTTP request body)
        $requestData = json_decode(file_get_contents("php://input"), true);

        //Pulling data from the json
        $ownerID = $requestData['id'];
        $firstname = $requestData['firstname'];
        $email = $requestData['email'];
        $phone = $requestData['phone'];
        $contactId = $requestData['contactId']; 
        $lastname = $requestData['lastname'];
    
        /*If the stmts aren't in the format below, we are prone to SQL injections! Please have values '?' in the con->prepare and only specify them in bind_params!*/

        //Safely preform an UPDATE query using a prepared statement
        $stmt = $con->prepare("UPDATE userContacts SET FirstName=?, LastName=?, Email=?, Phone=? WHERE (ownerID = ? AND contactID = ?)");
        //Bind the anonymous parameters
        $stmt->bind_param("ssssii", $firstname, $lastname, $email, $phone,$ownerID, $contactId);

        //Attempt to execute our query
        try {
            $stmt->execute();
            $response = ['success' => true];

        //Either the contact to be updated doesnt exist, or there was an issue contacting the database
        } catch(mysqli_sql_exception) {$response = ['success' => false];}

        
        //Terminating query
        $stmt->close();

        // Return JSON response
        header("Content-Type: application/json");
        echo json_encode($response);
    }
}


//User would like to delete a member from their contacts list
function deleteContact($con) {
    //Double checking request method
    if ($_SERVER['REQUEST_METHOD'] === 'DELET') {
        // Decode the incoming request (php://input is an input stream with raw JSON from the HTTP request body)
        $requestData = json_decode(file_get_contents("php://input"), true);

        //Pulling data from the json
        $ownerID = $requestData['id'];
        $contactId = $requestData['contactId']; 
    
        /*If the stmts aren't in the format below, we are prone to SQL injections! Please have values '?' in the con->prepare and only specify them in bind_params!*/

        //Safely preform a DELETE query using a prepared statement
        $stmt = $con->prepare("DELETE FROM userContacts WHERE (ownerID=? AND contactID=?)");
        //Bind the anonymous parameters
        $stmt->bind_param("ii", $ownerID, $contactId);

        //Attempt to execute our query
        try {
            $stmt->execute();
            $response = ['success' => true];

        //Either the contact didnt exist, or there was an issue contacting the database
        } catch(mysqli_sql_exception) {$response = ['success' => false];}

        
        //Terminating query
        $stmt->close();

        // Return JSON response
        header("Content-Type: application/json");
        echo json_encode($response);
    }
}

//User would like to add a member to their contacts list
function addContact($con) {
    //Double checking request method
    if ($_SERVER['REQUEST_METHOD'] === 'ADD') {
        // Decode the incoming request (php://input is an input stream with raw JSON from the HTTP request body)
        $requestData = json_decode(file_get_contents("php://input"), true);

        //Pulling data from the json
        $ownerID = $requestData['id'];
        $firstname = $requestData['firstname'];
        $lastname = $requestData['lastname'];
        $email = $requestData['email'];
        $phone = $requestData['phone'];

        /*If the stmts aren't in the format below, we are prone to SQL injections! Please have values '?' in the con->prepare and only specify them in bind_params!*/

        //Safely preform an INSERT query using a prepared statement
        $stmt = $con->prepare("INSERT INTO userContacts (ownerID, FirstName, LastName, Email, Phone) VALUES (?, ?, ?, ?, ?)");
        //Bind the anonymous parameters
        $stmt->bind_param("isssi", $ownerID, $firstname, $lastname, $email, $phone);

        //Attempt to execute our query
        try {
            $stmt->execute();
            $response = ['success' => true];
            
            //Terminating query
            $stmt->close();

        //There was an issue contacting the database
        } catch(mysqli_sql_exception) {$response = ['success' => false];}

        // Return JSON response
        header("Content-Type: application/json");
        echo json_encode($response);
    }
}
?>
