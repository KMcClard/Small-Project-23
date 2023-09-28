
//User tried to register an account by hitting the submit button, which calls this function
function submitRegis(event) { 
    event.preventDefault(); //Keeps from actually submitting so we can submit

    //Take the elements and map them to variables
    const firstname = document.getElementById("firstName").value;
    const lastname = document.getElementById("lastName").value;
    const email = document.getElementById("email").value;
    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;
    const phone = document.getElementById("phone").value;

    //Initialize the data we want to json stringify
    const formData = {
        firstname: firstname,
        lastname: lastname,
        email: email,
        username: username,
        password: password,
        phone: phone
    };

    //Send the json data out to our API
    fetch('api.php', {
        method: 'REGIS', //Specify request type
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
            document.getElementById("message").innerHTML = '<p>Registration failed. Please check the information you provided.</p>';
        }
    })
    // This should not happen. Some issue while talking to the database, or in JSON formatting
    .catch(error => {
        document.getElementById("message").innerHTML = '<p>Registration failed. Please check the information you provided.</p>';
        console.error('Error:', error);
    });
}
