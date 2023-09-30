<?php

//An API function would like the ID of the current user
function fetchID($con, $username, $password) {

    //Safely preform a SELECT query using a prepared statement
    $stmt = $con->prepare("SELECT (clientID) FROM userLogins WHERE (Username=? AND PasswordHashed=?)");
    //Bind the anonymous parameters
    $stmt->bind_param("ss", $username, $password);

    //Attempt to execute our query
    try {
        $stmt->execute();
        $result = $stmt->get_result();

        //Check if anything was actually retrieved from the database
        if($result->num_rows > 0) {
            $clientId = $result->fetch_assoc()["clientID"]; //Grab the client's ID

            //Terminating query
            $stmt->close();
            
            //Return the ID to the parent API function
            return $clientId;
        }


    //Error communicating with the database
    } catch(mysqli_sql_exception) {
        //Terminate query and return an erroneous value
        $stmt->close();
        return 0;
    }

    //End of execution; should never be reached. Terminate query and return an erroneous value
    $stmt->close();
    return 0;
}


//An API function would like to populate the session of the current user
function fetchUserInfo($con, $username, $password) {

    //Safely preform a SELECT query using a prepared statement
    $stmt = $con->prepare("SELECT * FROM userLogins WHERE (Username=? AND PasswordHashed=?)");
    //Bind the anonymous parameters
    $stmt->bind_param("ss", $username, $password);

    //Attempt to execute our query
    try {
        $stmt->execute();

        //Bind the query results to the specified variables
        $stmt->bind_result($clientId, $firstName, $lastName, $username, $email, $passwordHashed, $phone);

        //Populate the array with the retrieved values
        while($stmt->fetch()) {
            $userInfo = [
                'id' => $clientId,
                'firstname' => $firstName,
                'lastname' => $lastName,
                'email' => $email,
                'phone' => $phone,
            ];
        }

        return $userInfo;

    //Error communicating with the database
    } catch(mysqli_sql_exception) {
        $stmt->close();
        return array();
    }

    //End of execution; should never be reached. Terminate query and return an empty array
    $stmt->close();
    return array();
}

?>
