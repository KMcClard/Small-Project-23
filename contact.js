
//-----------------------Fill out the contacts table-----------------------
//    This code will exectue as soon as the HTML loads the JS link

//Grab page info from loacal storage
let pageNumber = parseInt(localStorage.getItem('pageNumber')) || 0;
let urlWithQuery = `api.php?pageNum=${pageNumber}`;
let contactCount = parseInt(localStorage.getItem('contactNumbers'));
let searchParameter = localStorage.getItem('searchParameter') || "";
let filterParameter = localStorage.getItem('filterParameter') || "FirstName";
let currentContact = localStorage.getItem('currentContact') || -1;

clientID = parseInt(localStorage.getItem("clientID")) || -1;


//This is used to handle all the form data
const form = document.getElementById('addForm');
const addContactButton = document.getElementById('createNewContactBut');
const editForm = document.getElementById('editForm');
const editContactButton = document.getElementById('editContactBut');
const deleteContactButton = document.getElementById('deleteContactBut');
const logoutButton = document.getElementById('logout');

const formData = {
    id: clientID,
};

//Set up buttons
window.addEventListener("load",loadContacts);
window.addEventListener("load",setClientID);
document.getElementById("nextPage").addEventListener("click",nextContacts);
document.getElementById("prevPage").addEventListener("click",lastContacts);
logoutButton.addEventListener("click",logoutUser);
//Check if backarrow was used


//Load the contacts
function loadContacts(){

	urlWithQuery = `api.php?pageNum=${pageNumber}&substring=${searchParameter}&filter=${filterParameter}`;
	console.log("This is our url: " + urlWithQuery);
	if(clientID == -1)
	{
		window.location.href = '/index.html';	
	}
	if(performance.navigation.type == 2){
   		console.log("We used back:" + clientID);
		location.reload(true);
	}
	//Send the json data out to our API
	fetch(urlWithQuery, {
	    method: 'LOAD', // Specify request type
	    headers: {      // Specify JSON formatting
        	'Content-Type': 'application/json'
    	},
   	 body: JSON.stringify(formData) // The data we're sending out to the API
	})
	.then(response => response.json()) //Retrieve the response json and parse it
	.then(data => {
    	//If the response json echoed a success
    	if (data.success) {

            contactCount = data.debug;
            localStorage.setItem("contactCount",contactCount.toString());	
	    
	  
        	// Fill the table with contacts
		    if(data.contacts == null) {
			 
		    	for(let i = 1; i <= 15; i++) {
		    		document.getElementById("gridFirst"+i.toString()).innerHTML = "";
                    		document.getElementById("gridLast"+i.toString()).innerHTML = "";
                    		document.getElementById("gridPhone"+i.toString()).innerHTML = "";
                    		document.getElementById("gridEmail"+i.toString()).innerHTML = "";
                    		document.getElementById("dropdownMenu"+i.toString()).innerHTML = "";
                    		document.getElementById("dropdownMenu"+i.toString()).style.display  = "none";
			}
                
            } else {	
                for(let i = 1; i <= 15; i++) {

                    if(data.contacts[i-1] != null) {
                        
                        let contact = data.contacts[i-1];
                        let currentRow = document.getElementById("gridFirst"+i.toString());
			let currentRowParent = currentRow.parentElement;
			currentRowParent.setAttribute("data-id",contact['contactID']);
			
			document.getElementById("gridFirst"+i.toString()).innerHTML = contact['firstname'];
                        document.getElementById("gridLast"+i.toString()).innerHTML = contact['lastname'];
                        document.getElementById("gridPhone"+i.toString()).innerHTML = contact['phone'];
                        document.getElementById("gridEmail"+i.toString()).innerHTML = contact['email'];
                        document.getElementById("dropdownMenu"+i.toString()).style.display = "block";
                        document.getElementById("dropdownMenu"+i.toString()).innerHTML = "Optionsx";
                    } else {
                        
                        document.getElementById("gridFirst"+i.toString()).innerHTML = "";
                        document.getElementById("gridLast"+i.toString()).innerHTML = "";
                        document.getElementById("gridPhone"+i.toString()).innerHTML = "";
                        document.getElementById("gridEmail"+i.toString()).innerHTML = "";
                        document.getElementById("dropdownMenu"+i.toString()).innerHTML = "";
                        document.getElementById("dropdownMenu"+i.toString()).style.display  = "none";

                    }
                }
            }

            //If a URL parameter was manually inserted when requesting this page, address the request
            // let urlParams = new URLSearchParams(window.location.search);
            // if(urlParams.has('search')) urlSearch();

        } else  {
            //Print error message to the console
            console.error("DATA RETURNED FALSE!");
        }

	})
		// This should not happen. Some issue while talking to the database, or in JSON formatting
		.catch(error => {
    		console.error('Error:', error);
	});
}

function logoutUser (){
	formData['id'] = -1;
	clientID = -1;
	localStorage.setItem("clientID",clientID);
	loadContacts();
}

//-----------------------Submit Form to Add New Contacts------------------------
addContactButton.addEventListener('click', function (e) {
    e.preventDefault();

    if (isFormValid(form)) {
        // Form is valid, submit it
        form.submit();
    } else {
        alert("Please fill out all required fields before submitting.");
    }

	setClientID();
	
	let formData = {
		firstname: form.querySelector('[name="firstName"]').value,
		lastname: form.querySelector('[name="lastName"]').value,
		email: form.querySelector('[name="email"]').value,
		phone: form.querySelector('[name="phone"]').value,
		id: clientID
	}
	
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
            console.log("Added");
            window.location.href = "/contact.php";
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
	
});

editContactButton.addEventListener('click', function (e) {
    e.preventDefault();

    if (isFormValid(editForm)) {
        // Form is valid, submit it
       	
    } else {
        alert("Please fill out all required fields before submitting.");
    	return;
    }

        setClientID();

        let formData = {
                firstname: editForm.querySelector('[name="firstName"]').value,
                lastname: editForm.querySelector('[name="lastName"]').value,
                email: editForm.querySelector('[name="email"]').value,
                phone: editForm.querySelector('[name="phone"]').value,
                contactId : currentContact,
		id: clientID,
        }
	console.log(JSON.stringify(formData));

        fetch('api.php', {
        method: 'UPDT',   // Specify request type
        headers: {       // Specify JSON formatting
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)// The data we're sending out to the API
    })
    .then(response => response.json()) //Retrieve the response json and parse it
    .then(data => {
        //If the response json echoed a success
        if (data.success) {
            // Redirects to the contacts page
	    window.location.href  = "/contact.php";
            console.log("Edited");
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
    
    loadContacts();
    currentContact = -1;
    localStorage.setItem("currentContact",currentContact);
    	
});

deleteContactButton.addEventListener('click', function (e) {
        setClientID();

        let formData = {
                contactId : currentContact,
                id: clientID,
        }
        console.log(JSON.stringify(formData));

        fetch('api.php', {
        method: 'DELET',   // Specify request type
        headers: {       // Specify JSON formatting
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)// The data we're sending out to the API
    })
    .then(response => response.json()) //Retrieve the response json and parse it
    .then(data => {
        //If the response json echoed a success
        if (data.success) {
            // Redirects to the contacts page
            window.location.href  = "/contact.php";
            console.log("Edited");
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

    loadContacts();
    currentContact = -1;
    localStorage.setItem("currentContact",currentContact);

});

function isFormValid(form) {
    const formElements = form.elements;
    for (let i = 0; i < formElements.length; i++) {
        const element = formElements[i];
        if (element.tagName === "INPUT" && element.required) {
            if (element.value.trim() === "") {
                return false; // Required field is empty or contains only spaces
            }
        }
    }
    return true; // All required fields are filled out
}

function setClientID(){
	const hiddenInput = document.getElementById("formClientId");
	hiddenInput.value = clientID;
	if(hiddenInput == -1)
	{
		alert("Session has expired login again!");
		window.location.href  = "/index.html";
	}
}

//-----------------------Move Through Contacts------------------
function nextContacts(){
	if(contactCount > ( pageNumber + 1 ) * 11 )
	{
		pageNumber++;
		loadContacts();
		localStorage.setItem("pageNumber", pageNumber.toString());
		console.log(pageNumber);
	}
}


function lastContacts(){
	if(pageNumber > 0)
	{	
		pageNumber--;
		loadContacts();
		localStorage.setItem("pageNumber", pageNumber.toString());
		console.log(pageNumber);
	}
}

//-----------------------Search Functions-----------------------

//Searched from the search bar

function setFilterParameter(element){
	filterParameter = element.getAttribute('data-filter');
	localStorage.setItem("filterParameter",filterParameter);
	document.getElementById("currentFilter").innerHTML = element.innerHTML;
	loadContacts();
}

function manualSearch(event){

    //Set the parameter
    let inputValue = document.getElementById('searchBox').value;	
    console.log("This is what were reading:" + inputValue);
    
    if(pageNumber > 0)
    {
	pageNumber = 0;
	localStorage.setItem("pageNumber",pageNumber);
    }
    
    searchParameter = inputValue;

    //Store the parameter
    localStorage.setItem("searchParameter",searchParameter);
    console.log("This is the substring: " + searchParameter);
    
    
    loadContacts();

}

//Searched via url parameters
function urlSearch(event){

    //Grab the search parameter from the URL
    let urlParams = new URLSearchParams(window.location.search);

    if(urlParams.has('search')) {
        let inputValue = urlParams.get('search');
   	
	
	if(pageNumber > 0)
	{
		pageNumber = 0;
		localStorage.setItem("pageNumber",pageNumber);
	}
	
        console.log("This is what were reading:" + inputValue);
        searchParameter = inputValue;

        //Store the parameter
        localStorage.setItem("searchParameter",searchParameter);
        console.log("This is the substring: " + searchParameter);

         
	loadContacts();
    }

}

//-----------------------Button Functions-----------------------
// An Edit button was pressed
function editPress(row,count) {
    //let first = document.getElementById("gridFirst"+row.toString()).innerHTML;
    //let last = document.getElementById("gridLast"+row.toString()).innerHTML;
    //let email = document.getElementById("gridEmail"+row.toString()).innerHTML;
    //let phone = document.getElementById("gridPhone"+row.toString()).innerHTML;
    //let text = document.getElementById("gridEdit"+row.toString()).innerHTML;

    //if(text.localeCompare("Edit") == 0) {
        
        //Send the user to the update contact page with information about the specified contact in the URL
        //window.location.href = "update.php?first="+first+"&last="+last+"&email="+email+"&phone="+phone;

    //} else { //Button was pressed in an invalid state
    //    console.error("INVALID BUTTON CONTENTS!!");
    //}
    let trElem = row.parentElement.parentElement.parentElement.parentElement;
    console.log("Does this work: " +  trElem.parentElement.getAttribute("data-id"));
    currentContact = trElem.parentElement.getAttribute("data-id");
    localStorage.setItem("currentContact", currentContact);
    let firstName = document.getElementById("firstNameEdit");
    let lastName = document.getElementById("lastNameEdit");
    let phone = document.getElementById("phoneEdit");
    let email = document.getElementById("emailEdit");
    firstName.value = document.getElementById("gridFirst"+count.toString()).innerHTML;
    lastName.value = document.getElementById("gridLast"+count.toString()).innerHTML;
    phone.value = document.getElementById("gridPhone"+count.toString()).innerHTML;
    email.value = document.getElementById("gridEmail"+count.toString()).innerHTML;
}

function deletePress(row,count) {
    //let first = document.getElementById("gridFirst"+row.toString()).innerHTML;
    //let last = document.getElementById("gridLast"+row.toString()).innerHTML;
    //let email = document.getElementById("gridEmail"+row.toString()).innerHTML;
    //let phone = document.getElementById("gridPhone"+row.toString()).innerHTML;
    //let text = document.getElementById("gridEdit"+row.toString()).innerHTML;

    //if(text.localeCompare("Edit") == 0) {

        //Send the user to the update contact page with information about the specified contact in the URL
        //window.location.href = "update.php?first="+first+"&last="+last+"&email="+email+"&phone="+phone;

    //} else { //Button was pressed in an invalid state
    //    console.error("INVALID BUTTON CONTENTS!!");
    //}
    let trElem = row.parentElement.parentElement.parentElement.parentElement;
    console.log("Does this work: " +  trElem.parentElement.getAttribute("data-id"));
    currentContact = trElem.parentElement.getAttribute("data-id");
    localStorage.setItem("currentContact", currentContact);
}

