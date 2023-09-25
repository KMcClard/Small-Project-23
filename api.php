<?php

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
    regis($con);
} else if ($_SERVER['REQUEST_METHOD'] === 'LOGIN') {
    login($con);
} //Others, when implemented, will go here as else ifs. Switch statement caused some trouble for whatever reason


//User would like to register an account
function regis($con) {
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

        /*If the stmts aren't in the format below, we are prone to SQL injections! Please have values '?' in the con->prepare and only specify them in blind_params!*/

        //Safely preform an INSERT query using a prepared statement
        $stmt = $con->prepare("INSERT INTO smlpj (firstName, lastName, email, username, passwordHashed, phone) VALUES (?, ?, ?, ?, ?, ?)");
        //Blind parameters
        $stmt->bind_param("sssssi", $firstname, $lastname, $email, $username, $hash, $phone);

        //Attempt to execute our query
        try {
            $stmt->execute();
            $response = ['success' => true];

            //Begin the session and save some information about the user
            session_start();
            $_SESSION['username'] = $username;
            $_SESSION['firstname'] = $firstname;
            $_SESSION['lastname'] = $lastname;
            $_SESSION['email'] = $email;
            $_SESSION['phone'] = $phone;

        //Only exception possible is if username already existed in the database
        } catch(mysqli_sql_exception) {
            $response = ['success' => false];

        }

        // Return JSON response
        header("Content-Type: application/json");
        echo json_encode($response);

        //Terminating query
        $stmt->close();
    }
}

//User is trying to log in
function login($con) {
    //Double checking request method
    if ($_SERVER['REQUEST_METHOD'] === 'LOGIN') {
        // Decode the incoming request (php://input is an input stream with raw JSON from the HTTP request body)
        $requestData = json_decode(file_get_contents("php://input"), true);

        //Pulling data from the json
        $username = $requestData['username'];
        $password = $requestData['password'];

        /*If the stmts aren't in the format below, we are prone to SQL injections! Please have values '?' in the con->prepare and only specify them in blind_params!*/

        //Safely preform a SELECT query using a prepared statement
        $stmt = $con->prepare("SELECT (passwordHashed) FROM smlpj WHERE (username=?)");
        //Blind parameters
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
                    //SET SESSION VARIABLE HERE LATER. FETCH FROM DATABASE

                } else {
                    $response = ['success' => false];
                }

            //If nothing was retrieved from the datable, then the user specified did not exist
            } else {
                $response = ['success' => false];
            }


        //Error communicating with the database
        } catch(mysqli_sql_exception) {
            $response = ['success' => false];
        }

        // Return JSON response
        header("Content-Type: application/json");
        echo json_encode($response);

        //Terminating query
        $stmt->close();
    }
}
?>
