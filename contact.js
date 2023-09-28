//Executes when the page loads

//-----------------------Fill out the contacts table-----------------------
//Initialize the data we want to json stringify
const formData = {
    id: clientID
};

//Send the json data out to our API
fetch('api.php', {
    method: 'LOAD', //Specify request type
    headers: { // Specify JSON formatting
        'Content-Type': 'application/json'
    },
    body: JSON.stringify(formData) // The data we're sending out to the API
})
.then(response => response.json()) //Retrieve the response json and parse it
.then(data => {
    //If the response json echoed a success
    if (data.success) {

        // Fill the table with contacts
        for(let i = 1; i <= 6; i++) {

            if(data.contacts[i-1]) {
                let contact = data.contacts[i-1];
                document.getElementById("gridFirst"+i.toString()).innerHTML = contact['firstname'];
                document.getElementById("gridLast"+i.toString()).innerHTML = contact['lastname'];
                document.getElementById("gridPhone"+i.toString()).innerHTML = contact['phone'];
                document.getElementById("gridEmail"+i.toString()).innerHTML = contact['email'];
                document.getElementById("gridEdit"+i.toString()).innerHTML = "Edit";
            }
        }

    } else {
        //Print error message in the message div
        console.error("DATA RETURNED FALSE!");
    }
})
// This should not happen. Some issue while talking to the database, or in JSON formatting
.catch(error => {
    console.error('Error:', error);
});


//-----------------------Search Function-----------------------
//User searched the contact table for a string
function searchContacts(event) { 
    event.preventDefault(); //Keeps from actually submitting so we can submit

    for(let i = 1; i <= 6; i++) {
        if(document.getElementById("gridFirst"+i.toString()).innerHTML != '') {
                document.getElementById("gridFirst"+i.toString()).style.display = 'block';
                document.getElementById("gridLast"+i.toString()).style.display = 'block';
                document.getElementById("gridPhone"+i.toString()).style.display = 'block';
                document.getElementById("gridEmail"+i.toString()).style.display = 'block';
                document.getElementById("gridEdit"+i).innerHTML = "Edit";
        }
    }

    const key = document.getElementById("searchBox").value.toLowerCase();
    var emptyRows = [];

    // Search the table for any partial match with our key
    for(let i = 1; i <= 6; i++) {
        //If there was a partial match
        if(document.getElementById("gridFirst"+i.toString()).innerHTML.toLowerCase().includes(key)
            ||document.getElementById("gridLast"+i.toString()).innerHTML.toLowerCase().includes(key)
            ||document.getElementById("gridPhone"+i.toString()).innerHTML.toLowerCase().includes(key)
            ||document.getElementById("gridEmail"+i.toString()).innerHTML.toLowerCase().includes(key)) {

            //Move the row to the first avaliable row
            if(emptyRows.length > 0) {
                swap(i.toString(), emptyRows.shift());
                emptyRows.push(i); //Mark this row as an empty row
            }

        //If the contact does not contain the substring, hide it
        } else {
            document.getElementById("gridFirst"+i.toString()).style.display = 'none';
            document.getElementById("gridLast"+i.toString()).style.display = 'none';
            document.getElementById("gridPhone"+i.toString()).style.display = 'none';
            document.getElementById("gridEmail"+i.toString()).style.display = 'none';
            document.getElementById("gridEdit"+i).innerHTML = "+";
            
            emptyRows.push(i); //Mark this row as an empty row
        }
    }
}

function swap(i, j) {
    //Swap the first names
    temp = document.getElementById("gridFirst"+i).innerHTML;
    document.getElementById("gridFirst"+i).innerHTML = document.getElementById("gridFirst"+j).innerHTML;
    document.getElementById("gridFirst"+j).innerHTML = temp;
    document.getElementById("gridFirst"+i).style.display = 'none';
    document.getElementById("gridFirst"+j).style.display = 'block';
    
    //Swap the last names
    temp = document.getElementById("gridLast"+i).innerHTML;
    document.getElementById("gridLast"+i).innerHTML = document.getElementById("gridLast"+j).innerHTML;
    document.getElementById("gridLast"+j).innerHTML = temp;
    document.getElementById("gridLast"+i).style.display = 'none';
    document.getElementById("gridLast"+j).style.display = 'block';
    
    //Swap the phone numbers
    temp = document.getElementById("gridPhone"+i).innerHTML;
    document.getElementById("gridPhone"+i).innerHTML = document.getElementById("gridPhone"+j).innerHTML;
    document.getElementById("gridPhone"+j).innerHTML = temp;
    document.getElementById("gridPhone"+i).style.display = 'none';
    document.getElementById("gridPhone"+j).style.display = 'block';
    
    //Swap the emails
    temp = document.getElementById("gridEmail"+i).innerHTML;
    document.getElementById("gridEmail"+i).innerHTML = document.getElementById("gridEmail"+j).innerHTML;
    document.getElementById("gridEmail"+j).innerHTML = temp;
    document.getElementById("gridEmail"+i).style.display = 'none';
    document.getElementById("gridEmail"+j).style.display = 'block';

    //Swap buttons
    document.getElementById("gridEdit"+i).innerHTML = "+";
    document.getElementById("gridEdit"+j).innerHTML = "Edit";
}
