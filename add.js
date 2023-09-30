
//User tried to add a contact by submitting the add form
function addContact(event) { 
    event.preventDefault(); //Keeps from actually submitting so we can submit

    //Take the elements and map them to variables
    const firstName = document.getElementById("firstAdd").value;
    const lastName = document.getElementById("lastAdd").value;
    const email = document.getElementById("emailAdd").value;
    const phone = document.getElementById("phoneAdd").value;

    //Initialize the data we want to json stringify
    const formData = {
        id: clientID,
        firstname: firstName,
        lastname: lastName,
        email: email,
        phone: phone
    };

    //Send the json data out to our API
    fetch('api.php', {
        method: 'ADD',   // Specify request type
        headers: {       // Specify JSON formatting
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
            document.getElementById("message").innerHTML = '<p>Unable to add the contact as requested. Please check your credentials.</p>';
        }
    })
    // This should not happen. Some issue while talking to the database, or in JSON formatting
    .catch(error => {
        document.getElementById("message").innerHTML = '<p>Unable to add the contact as requested. Please check your credentials.</p>';
        console.error('Error:', error);
    });
}


//The user cancelled the contact addition
function leavePage() {
    window.location.href = "contact.php";
}