
//User tried to log in by hitting the submit button, which calls this function
function submitLogin(event) { 
    event.preventDefault(); //Keeps from actually submitting so we can submit

    //Take the elements and map them to variables
    const username = document.getElementById("usernameLogin").value;
    const password = document.getElementById("passwordLogin").value;

    //Initialize the data we want to json stringify
    const formData = {
        username: username,
        password: password
    };

    //Send the json data out to our API
    fetch('api.php', {
        method: 'LOGIN', //Specify request type
        headers: { // Specify JSON formatting
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData) // The data we're sending out to the API
    })
    .then(response => response.json()) //Retrieve the response json and parse it
    .then(data => {
        //If the response json echoed a success
        if (data.success) {
            // Redirects to the contacts page
            window.location.href = "contact.php";
        } else {
            //Print error message in the message div
            document.getElementById("message").innerHTML = '<p>Login failed. Please check your credentials.</p>';
        }
    })
    // This should not happen. Some issue while talking to the database, or in JSON formatting
    .catch(error => {
        document.getElementById("message").innerHTML = '<p>Login failed. Please check your credentials.</p>';
        console.error('Error:', error);
    });
}
