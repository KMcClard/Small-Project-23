<!--PHP code to connect this page to the website session. Feel free to take this out and reformat to HTML if it messes with Bootstrap; I can re-add it any time-->
<?php
// Include the API script
include("api.php");

// Start or resume the session
session_start();

//Set the clientID variable to send to JS (set to the empty string if it doesnt exist)
$clientID = (isset($_SESSION['id']))?$_SESSION['id']:'';
?>

<!DOCTYPE html>

<html>

<head>
	<title>CRUD</title>	
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
	<link rel="icon" href="favicon.ico">
	
	<style>
		body {
			background-color: #373A36;
			font-family: "arial" !important;
		}

		nav{
			background-color: #3B4252;
		}
		.grid {
			display: grid;
			grid-template-columns: auto auto auto auto auto;
			row-gap: 0px;
			column-gap: 0px;
			background-color: linen;
			padding: 10px;
		}
		
		.gridHeader {
			background-color: RoyalBlue;
			border: 1px solid rgba(0, 0, 0, 0.8);
			padding: 10px;
			font-size: 30px;
			text-align: center;
		}
		
		.gridItem {
			background-color: LightCyan;
			border: 1px solid rgba(0, 0, 0, 0.8);
			padding: 10px;
			font-size: 30px;
			text-align: center;
		}
		
		.navbar-custom {
			background-color: #D48166;
			box-shadow: 0px 0px 5px 1px black;
			
		}
		.table-custom {
			background-color: #D48166 !important;	
		}
		td,th {
			color: #E6E2DD;
			background-color: #D48166 !important;
			/* border-color: #D48166 !important;*/
			/*overflow: hidden;*/
		}
		table {
			z-index : 0;
			overflow:hidden;
		}
		button {
			z-index:10;
		}
		.table-darker {
			background-color: #BA5059 !important;
		}
		.table-container{
			box-shadow: 0px 0px 5px 1px black;
		}
		.contactNav {
			margin-bottom: 3em;	
		}
	</style>
</head>


<body>
	<!--Banner-->
	<div class="contactNav">
	  <nav class="navbar navbar-custom">
	  <div class="container-fluid">
		<div class="navbar-brand">
			<img src="favicon.ico" alt="Bootstrap" width="40" height="36" class="d-inline-block align-text-center">
			<b style="color: #E6E2DD;">CRUDDY CONTACTS</b>
		</div>
		                        <div class="d-flex align-text-center">
                                <button type="button" class="btn mb-2 mb-md-0 btn-secondary btn-sm btn-round mr-3" id="newContact" data-bs-toggle="modal" data-bs-target="#addModal">Add Contact</button>
                                <!-- Modal -->
                                <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModal" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                        <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel">Contact Form</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                        <form id="addForm" action="api.php" method="">
                                                                <div class="row mb-3">
                                                                        <label for="firstName" class="col-sm-2 col-form-label">First Name</label>
                                                                        <div class="col-sm-10">
                                                                                <input type="text" name="firstName" class="form-control" id="firstName" required>
                                                                        </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                        <label for="lastName" class="col-sm-2 col-form-label">Last Name</label>
                                                                        <div class="col-sm-10">
                                                                                <input type="text" name="lastName" class="form-control" id="lastName" required>
                                                                        </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                        <label for="phone" class="col-sm-2 col-form-label">Phone Number</label>
                                                                        <div class="col-sm-10">
                                                                                <input type="text" name="phone" class="form-control" id="phone" required>
                                                                        </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                        <label for="email" class="col-sm-2 col-form-label">Email</label>
                                                                        <div class="col-sm-10">
                                                                                <input type="text" name="email" class="form-control" id="email" required>
                                                                        </div>
                                                                </div>
                                                                <input type="hidden" id="formClientId" name="clientID" value="">

                                                        </form>
                                                        </div>
                                                        <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                <button type="button" class="btn btn-primary" id="createNewContactBut">Create New Contact</button>
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </div>

		<div class="d-flex justify-content-between">
			<li class="d-flex justify-content-left align-self-center  nav-item dropdown" style="margin-right:20px" >
          			<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" id="currentFilter" aria-expanded="false">
            				Filter Choice
          			</a>
				<ul class="dropdown-menu">
            				<li><a class="dropdown-item" onclick="setFilterParameter(this)" data-filter="FirstName">First Name</a></li>
            				<li><a class="dropdown-item" onclick="setFilterParameter(this)" data-filter="LastName">Last Name</a></li>
					<li><a class="dropdown-item" onclick="setFilterParameter(this)" data-filter="Phone">Phone</a></li>
					<li><a class="dropdown-item" onclick="setFilterParameter(this)" data-filter="Email">Email</a></li>
          			</ul>
        		</li>
			<form id="searchForm" style="background-color: white;" class="d-flex-inline justify-content-between" method="post" oninput="manualSearch(event)">
				<input class="form-control justify-content-center me-2" style="background-color: white" type="text" placeholder="search" id="searchBox" name="searchBox">
			</form>
		</div>	
		<button class="navbar-brand align-text-center" id="logout" style="font-size: 24px;" >
		LOGOUT
		</button>
	  </div>
	</nav>
	</div>
	<!--Table-->
	<div class="container">

		<!--Search-->
		<div class="row">
			<div class="col">
				<!--
				<form id="searchForm" method="post" oninput="manualSearch(event)">
					<label>SEARCH</label>
					<input type="text" id="searchBox" name="searchBox"> 
				</form>-->
			</div>
		</div>
		
		<!--Contacts-->
		<!--<div class="row mt-2 table-custome rounded-4">-->
			
			<table class="table table-striped rounded-4 table-borderless table-custom table-container">
  				<thead>
    					<tr style="table-darker rounded table-container">
      						<th style="table-custom" scope="col">First</th>
      						<th scope="col">Last</th>
							<th scope="col">Phone</th>
							<th scope="col">Email</th>
							<th scope="col">Options</th>
    					</tr>
  				</thead>
  				<tbody class="table-custom">
    					<tr style="table-lighter" data-id="">
      							<td id="gridFirst1"></td>
      							<td id="gridLast1"></td>
							<td id="gridPhone1"></td>
							<td id="gridEmail1"></td>
							<td>
								<div class="dropdown">
									<button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenu1" data-bs-toggle="dropdown" aria-expanded="false" ></button>
									<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
										<li><button class="dropdown-item" type="button" id="gridEdit1" data-bs-toggle="modal" data-bs-target="#editModal" onclick="editPress(this,1)">Edit</button></li>
										<li><button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="deletePress(this,1)">Delete</button></li>
									</ul>
								</div>
							</td>
					</tr>

    					<tr style="table-darker" data-id="">
      							<td id="gridFirst2"></td>
      							<td id="gridLast2"></td>
      							<td id="gridPhone2"></td>
							<td id="gridEmail2"></td>
							<td>	
								<div class="dropdown">
									<button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-bs-toggle="dropdown" aria-expanded="false"></button>
									<ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
										<li><button class="dropdown-item" type="button" id="gridEdit2" data-bs-toggle="modal" data-bs-target="#editModal" onclick="editPress(this,2)">Edit</button></li>		
										<li><button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#editModal" onclick="deletePress(this,2)">Delete</button></li>
									</ul>
								</div>
				
					
					</tr>		

    					<tr style="table-lighter" data-id="">
      							<td id="gridFirst3"></td>
     							<td id="gridLast3"></td>
							<td id="gridPhone3"></td>
							<td id="gridEmail3"></td>
							<td>	
								<div class="dropdown">
								<button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenu3" data-bs-toggle="dropdown" aria-expanded="false"></button>
									<ul class="dropdown-menu" aria-labelledby="dropdownMenu3">	
										<li><button class="dropdown-item" type="button" id="gridEdit3" data-bs-toggle="modal" data-bs-target="#editModal" onclick="editPress(this,3)">Edit</button></li>
										<li><button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-toggle="#deleteModal" onclick="deletePress(this,3)">Delete</button></li>
									</ul>
								</div>
							</td>		
					</tr>
		
					<tr style="table-darker" data-id="">
							<td id="gridFirst4"></td>
							<td id="gridLast4"></td>
							<td id="gridPhone4"></td>
							<td id="gridEmail4"></td>
							<td>
								<div class="dropdown">
        	                                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenu4" data-bs-toggle="dropdown" aria-expanded="false"></button>
									 <ul class="dropdown-menu" aria-labelledby="dropdownMenu4">
										<li><button class="dropdown-item" type="button" id="gridEdit4" data-bs-toggle="modal" data-bs-target="#editModal" onclick="editPress(this,4)">Edit</button></li>
										<li><button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="deletePress(this,4)">Delete</button></li>
									</ul>
								</div>

							</td>
					</tr>

					<tr style="table-lighter" data-id="">
							<td id="gridFirst5"></td>
							<td id="gridLast5"></td>
							<td id="gridPhone5"></td>
							<td id="gridEmail5"></td>
							<td>
								
                                                                <div class="dropdown">
                                                                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenu5" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                                                              <ul class="dropdown-menu" aria-labelledby="dropdownMenu5">
											<li><button class="dropdown-item" type="button" id="gridEdit5" data-bs-toggle="modal" data-bs-target="#editModal" onclick="editPress(this,5)">Edit</button></li>
											<li><button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="deletePress(this,5)">Delete</button></li>
								 
										</ul>
								</div>
							</td>

					</tr>

					<tr style="table-darker" data-id="">
							<td id="gridFirst6"></td>
							<td id="gridLast6"></td>
							<td id="gridPhone6"></td>
							<td id="gridEmail6"></td>
							<td>
								 <div class="dropdown">
                                                                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenu6" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                                                              <ul class="dropdown-menu" aria-labelledby="dropdownMenu6">
								  			<li><button class="dropdown-item" type="button" id="gridEdit6" data-bs-toggle="modal" data-bs-target="#editModal" onclick="editPress(this,6)">Edit</button></li>                                                                          
									     		<li><button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="deletePress(this,6)">Delete</button></li>
										
									     </ul>
								</div>
							</td>
					</tr>
					<tr style="table-lighter" data-id="">
                                                        <td id="gridFirst7"></td>
                                                        <td id="gridLast7"></td>
                                                        <td id="gridPhone7"></td>
                                                        <td id="gridEmail7"></td>
							<td>
		                                                <div class="dropdown">
                                                                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenu7" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                                                              <ul class="dropdown-menu" aria-labelledby="dropdownMenu7">
											<li><button class="dropdown-item" type="button" id="gridEdit7" data-bs-toggle="modal" data-bs-target="#editModal" onclick="editPress(this,7)">Edit</button></li>
											<li><button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="deletePress(this,7)">Delete</button></li>
 									  </ul>
								</div>
							</td>	
							
					</tr>

                                        <tr style="table-darker" data-id="">
                                                        <td id="gridFirst8"></td>
                                                        <td id="gridLast8"></td>
                                                        <td id="gridPhone8"></td>
                                                        <td id="gridEmail8"></td>
							<td>
                                                                <div class="dropdown">
                                                                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenu8" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                                                              <ul class="dropdown-menu" aria-labelledby="dropdownMenu8">
											<li><button class="dropdown-item" type="button" id="gridEdit8" data-bs-toggle="modal" data-bs-target="#editModal" onclick="editPress(this,8)">Edit</button></li>                                                                          
											<li><button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="deletePress(this,8)">Delete</button></li>
	
									      </ul>

								</div>
							</td>
					</tr>

                                        <tr style="table-lighter" data-id="">
                                                        <td id="gridFirst9"></td>
                                                        <td id="gridLast9"></td>
                                                        <td id="gridPhone9"></td>
                                                        <td id="gridEmail9"></td>
							<td>
								<div class="dropdown">
                                                                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenu9" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                                                              <ul class="dropdown-menu" aria-labelledby="dropdownMenu9">
											<li><button class="dropdown-item" type="button" id="gridEdit9" data-bs-toggle="modal" data-bs-target="#editModal" onclick="editPress(this,9)">Edit</button></li>                                                                       
									      		<li><button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="deletePress(this,9)">Delete</button></li>

									      </ul>

								</div>
							</td>
					</tr>

                                        <tr style="table-darker" data-id="">
                                                        <td id="gridFirst10"></td>
                                                        <td id="gridLast10"></td>
                                                        <td id="gridPhone10"></td>
                                                        <td id="gridEmail10"></td>
							<td>
                                                                <div class="dropdown">
                                                                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenu10" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                                                              <ul class="dropdown-menu" aria-labelledby="dropdownMenu10">
											<li><button class="dropdown-item" type="button" id="gridEdit10" data-bs-toggle="modal" data-bs-target="#editModal" onclick="editPress(this,10)">Edit</button></li>                                                                          
									      		<li><button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="deletPress(this,10)">Delete</button></li>
									      </ul>
								</div>
							</td>
								
					</tr>

                                        <tr style="table-lighter" data-id="">
                                                        <td id="gridFirst11"></td>
                                                        <td id="gridLast11"></td>
                                                        <td id="gridPhone11"></td>
                                                        <td id="gridEmail11"></td>
							<td>
								<div class="dropdown">
                                                                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenu11" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                                                              <ul class="dropdown-menu" aria-labelledby="dropdownMenu11">
											<li><button class="dropdown-item" type="button" id="gridEdit11" data-bs-toggle="modal" data-bs-target="#editModal" onclick="editPress(this,11)">Edit</button></li>                                                                          
											<li><button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="deletPress(this,11)">Delete</button></li>	
									      </ul>
								</div>
							</td>
					</tr>
					<tr style="table-darker" data-id="">
                                                        <td id="gridFirst12"></td>
                                                        <td id="gridLast12"></td>
                                                        <td id="gridPhone12"></td>
                                                        <td id="gridEmail12"></td>
							<td>
                                                                <div class="dropdown">
                                                                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenu12" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                                                              <ul class="dropdown-menu" aria-labelledby="dropdownMenu12">
											<li><button class="dropdown-item" type="button" id="gridEdit12" data-bs-toggle="modal" data-bs-target="#editModal" onclick="editPress(this,12)">Edit</button></li>
											 <li><button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="deletPress(this,12)">Delete</button></li>									  
											

										</ul>
								</div>
							</td>								
					</tr>

                                        <tr style="table-lighter" data-id="">
                                                        <td id="gridFirst13"></td>
                                                        <td id="gridLast13"></td>
                                                        <td id="gridPhone13"></td>
                                                        <td id="gridEmail13"></td>
							<td>
	                                                          <div class="dropdown">
                                                                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenu13" data-bs-toggle="dropdown" aria-expanded="false"></button>
								        	<ul class="dropdown-menu" aria-labelledby="dropdownMenu13">
											<li><button class="dropdown-item" type="button" id="gridEdit13" data-bs-toggle="modal" data-bs-target="#editModal" onclick="editPress(this,13)">Edit</button></li>
											 <li><button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="deletPress(this,13)">Delete</button></li>
										</ul>
								</div>
							</td>
					</tr>

                                        <tr style="table-darker" data-id="">
                                                        <td id="gridFirst14"></td>
                                                        <td id="gridLast14"></td>
                                                        <td id="gridPhone14"></td>
                                                        <td id="gridEmail14"></td>
							<td>
                                                                <div class="dropdown">
                                                                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenu14" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                                                              <ul class="dropdown-menu" aria-labelledby="dropdownMenu14">
											<li><button class="dropdown-item" type="button" id="gridEdit14" data-bs-toggle="modal" data-bs-target="#editModal" onclick="editPress(this,14)">Edit</button></li>    
											<li><button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="deletPress(this,14)">Delete</button></li>
			
									      </ul>
								</div>
							</td>
                                        </tr>

                                        <tr style="table-lighter" data-id="">
                                                        <td id="gridFirst15"></td>
                                                        <td id="gridLast15"></td>
                                                        <td id="gridPhone15"></td>
                                                        <td id="gridEmail15"></td>
							<td>
                                                                <div class="dropdown">
                                                                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenu15" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                                                              <ul class="dropdown-menu" aria-labelledby="dropdownMenu15">
											<li><button class="dropdown-item" type="button" id="gridEdit15" data-bs-toggle="modal" data-bs-target="#editModal" onclick="editPress(this,15)">Edit</button></li>  
											<li><button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="deletPress(this,15)">Delete</button></li>

									</ul>
								</div>
							</td>
					</tr>
					<tr>
						<td colspan="3">
							<div class="col text-start">
                                				<button class="btn btn-primary" id="prevPage">Previous Page</button>
				                        </div>
						</td>
						<td colspan="2">                       
							 <div class="col text-end">
                                				<button class="btn btn-primary" id="nextPage">Next Page</button>
                        				</div>
						</td>
					<td>
 				 </tbody>
			</table>		
		<!--</div>-->
		
		<div class="row mt-2 justify-content-evenly">			
			<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModal" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content">
                                                        <div class="modal-header">
                                                                <h5 class="modal-title" id="editModalLabel">Contact Form</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                        <form id="editForm" action="api.php" method="">
                                                                <div class="row mb-3">
                                                                        <label for="firstName" class="col-sm-2 col-form-label">First Name</label>
                                                                        <div class="col-sm-10">
                                                                                <input type="text" name="firstName" class="form-control" id="firstNameEdit" required>
                                                                        </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                        <label for="lastName" class="col-sm-2 col-form-label">Last Name</label>
                                                                        <div class="col-sm-10">
                                                                                <input type="text" name="lastName" class="form-control" id="lastNameEdit" required>
                                                                        </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                        <label for="phone" class="col-sm-2 col-form-label">Phone Number</label>
                                                                        <div class="col-sm-10">
                                                                                <input type="text" name="phone" class="form-control" id="phoneEdit" required>
                                                                        </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                        <label for="email" class="col-sm-2 col-form-label">Email</label>
                                                                        <div class="col-sm-10">
                                                                                <input type="text" name="email" class="form-control" id="emailEdit" required>
                                                                        </div>
                                                                </div>
                                                                <input type="hidden" id="formClientId" name="clientID" value="">

                                                        </form>
                                                        </div>
                                                        <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                <button type="button" class="btn btn-primary" id="editContactBut">Save Changes</button>
                                                        </div>
                                                </div>
                                        </div>
                             	 </div>
		</div>
		
		 <div class="row mt-2 justify-content-evenly">
                        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModal" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                        <div class="modal-header">
                                                                <h5 class="modal-title" id="deleteModalLabel">Delete Contact</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                      		<p>Saving the changes will delete the selected contact there will be no way to undo the changes except for adding them in the future.</p> 
                                                        </div>
                                                        <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                <button type="button" class="btn btn-primary" id="deleteContactBut">Confirm Changes</button>
                                                        </div>
                                                </div>
                                        </div>
                                 </div>
                </div>
	</div>


	

	<!--Send the ID variable over to JS-->
	<script type="text/javascript">
		let clientID = '<?php echo $clientID?>'; 
		localStorage.setItem("clientID",clientID.toString());

		
	</script>
	<script src="contact.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</body>
</html>
