
//---------------------------Populate the Form---------------------------
//    This code will exectue as soon as the HTML loads the JS link

//Set up old contact information variables
var oldFirst = '';
var oldLast = '';
var oldEmail = '';
var oldPhone = 0;

//Grab parameters from the URL
const urlParams = new URLSearchParams(window.location.search);
if(urlParams.has('first')) oldFirst = urlParams.get('first'); 
if(urlParams.has('last')) oldLast = urlParams.get('last'); 
if(urlParams.has('email')) oldEmail = urlParams.get('email'); 
if(urlParams.has('phone')) oldPhone = urlParams.get('phone');

//Fill in the fields with the preexisting data
document.getElementById("firstUpdate").value = oldFirst;
document.getElementById("lastUpdate").value = oldLast;
document.getElementById("emailUpdate").value = oldEmail;
document.getElementById("phoneUpdate").value = oldPhone;

//---------------------------Button JS---------------------------
//User wanted to update the contact
function updateContact(event) {
    event.preventDefault();

    //Take the elements and map them to variables
    const newFirst = document.getElementById("firstUpdate").value;
    const newLast = document.getElementById("lastUpdate").value;
    const newEmail = document.getElementById("emailUpdate").value;
    const newPhone = document.getElementById("phoneUpdate").value;

    //Initialize the data we want to json stringify
    const formData = {
        id: clientID,
        newfirst: newFirst,
        oldfirst: oldFirst,
        newlast: newLast,
        oldlast: oldLast, 
        newemail: newEmail,
        oldemail: oldEmail,
        newphone: newPhone,
        oldphone: oldPhone
    };

    //Send the json data out to our API
    fetch('api.php', {
        method: 'UPDT', // Specify request type
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
            document.getElementById("message").innerHTML = '<p>Update failed. Please check the information you provided.</p>';
        }
    })
    // This should not happen. Some issue while talking to the database, or in JSON formatting
    .catch(error => {
        document.getElementById("message").innerHTML = '<p>Update failed. Please check the information you provided.</p>';
        console.error('Error:', error);
    });

}


//User wanted to delete a contact
function deleteContact() {

    //Take the elements and map them to variables
    const firstName = document.getElementById("firstUpdate").value;
    const lastName = document.getElementById("lastUpdate").value;
    const email = document.getElementById("emailUpdate").value;
    const phone = document.getElementById("phoneUpdate").value;

    //Initialize the data we want to json stringify
    const formData = {
        id: clientID,
        firstName: firstName,
        lastName: lastName,
        email: email,
        phone: phone
    };

    //Send the json data out to our API
    fetch('api.php', {
        method: 'DELET', // Specify request type
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
            document.getElementById("message").innerHTML = '<p>Deletion failed. Please check the information you provided.</p>';
        }
    })
    // This should not happen. Some issue while talking to the database, or in JSON formatting
    .catch(error => {
        document.getElementById("message").innerHTML = '<p>Deletion failed. Please check the information you provided.</p>';
        console.error('Error:', error);
    });

}

//The user cancelled the contact edit
function leavePage() {
    window.location.href = "contact.php";
}