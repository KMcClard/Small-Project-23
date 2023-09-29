<?php

//Include
include('api_helper.php');

//----------------Dispatcher----------------

//Set connection parameters
$hostname = "localhost";
$username = "root";
$password = "";
$database = "api_data";

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
    loadContacts($con);
} else if ($_SERVER['REQUEST_METHOD'] === 'UPDT') {
    updateContact($con);
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
        $stmt = $con->prepare("INSERT INTO smlpj (firstName, lastName, email, username, passwordHashed, phone) VALUES (?, ?, ?, ?, ?, ?)");
        //Bind the anonymous parameters
        $stmt->bind_param("sssssi", $firstname, $lastname, $email, $username, $hash, $phone);

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
        $stmt = $con->prepare("SELECT (passwordHashed) FROM smlpj WHERE (username=?)");
        //Bind the anonymous parameters
        $stmt->bind_param("s", $username);

        //Attempt to execute our query
        try {
            $stmt->execute();
            $result = $stmt->get_result();

            //Check if anything was actually retrieved from the database
            if($result->num_rows > 0) {
                $hashedP = $result->fetch_assoc()["passwordHashed"]; //Grab the saved password

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
function loadContacts($con) {
    //Double checking request method
    if ($_SERVER['REQUEST_METHOD'] === 'LOAD') {
        // Decode the incoming request (php://input is an input stream with raw JSON from the HTTP request body)
        $requestData = json_decode(file_get_contents("php://input"), true);

        //Pulling data from the json
        $clientID = $requestData['id'];

        /*If the stmts aren't in the format below, we are prone to SQL injections! Please have values '?' in the con->prepare and only specify them in bind_params!*/

        //Safely preform a SELECT query using a prepared statement
        $stmt = $con->prepare("SELECT * FROM smlcon WHERE (ownerID=?)");
        //Bind the anonymous parameters
        $stmt->bind_param("i", $clientID);

        //Attempt to execute our query
        try {
            $stmt->execute();

            //Bind the query results to the specified variables
            $stmt->bind_result($contactID, $ownerID, $firstName, $lastName, $email, $phone);

            //Populate an array of arrays with contact information
            while($stmt->fetch()) {
                $contacts[] = [
                    'firstname' => $firstName,
                    'lastname' => $lastName,
                    'email' => $email,
                    'phone' => $phone,
                ];
            }

            $response = ['success' => true, 'contacts' => $contacts];

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
        $newFirst = $requestData['newfirst'];
        $oldFirst = $requestData['oldfirst'];
        $newLast = $requestData['newlast'];
        $oldLast = $requestData['oldlast']; 
        $newEmail = $requestData['newemail'];
        $oldEmail = $requestData['oldemail'];
        $newPhone = $requestData['newphone'];
        $oldPhone = $requestData['oldphone'];
    
        /*If the stmts aren't in the format below, we are prone to SQL injections! Please have values '?' in the con->prepare and only specify them in bind_params!*/

        //Safely preform an UPDATE query using a prepared statement
        $stmt = $con->prepare("UPDATE smlcon SET firstName=?, lastName=?, email=?, phone=? WHERE (ownerID=? AND firstName=? AND lastName=? AND email=? AND phone=?)");
        //Bind the anonymous parameters
        $stmt->bind_param("sssiisssi", $newFirst, $newLast, $newEmail, $newPhone, $ownerID, $oldFirst, $oldLast, $oldEmail, $oldPhone);

        //Attempt to execute our query
        try {
            $stmt->execute();
            $response = ['success' => true];

        //Either the user entered a duplicate username, or there was an issue contacting the database
        } catch(mysqli_sql_exception) {$response = ['success' => false];}

        
        //Terminating query
        $stmt->close();

        // Return JSON response
        header("Content-Type: application/json");
        echo json_encode($response);
    }
}
?>
