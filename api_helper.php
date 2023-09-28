<?php

//An API function would like the ID of the current user
function fetchID($con, $username, $password) {
    /*If the stmts aren't in the format below, we are prone to SQL injections! Please have values '?' in the con->prepare and only specify them in blind_params!*/

    //Safely preform a SELECT query using a prepared statement
    $stmt = $con->prepare("SELECT (clientID) FROM smlpj WHERE (username=? AND passwordHashed=?)");
    //Blind parameters
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
            
            return $clientId;
        }


    //Error communicating with the database
    } catch(mysqli_sql_exception) {
        //Terminating query
        $stmt->close();
        
        return 0;
    }

    //Terminating query
    $stmt->close();

    return 0;
}

//An API function would like to populate the session of the current user
function fetchUserInfo($con, $username, $password) {

    /*If the stmts aren't in the format below, we are prone to SQL injections! Please have values '?' in the con->prepare and only specify them in blind_params!*/

    //Safely preform a SELECT query using a prepared statement
    $stmt = $con->prepare("SELECT * FROM smlpj WHERE (username=? AND passwordHashed=?)");
    //Blind parameters
    $stmt->bind_param("ss", $username, $password);

    //Attempt to execute our query
    try {
        $stmt->execute();
        $stmt->bind_result($clientId, $firstName, $lastName, $username, $email, $phone, $passwordHashed, $dateCreated);

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

    //Terminating query
    $stmt->close();
    return array();
}

?>
