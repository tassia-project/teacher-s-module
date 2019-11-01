<!DOCTYPE html>
<html lang="en">

<style>
  body {
	margin: auto;
	font-family: Arial;
	background: #fff;
}

hr {
	height: 1px;
	background:  #cdcdcd;
	border: none;
	outline: none;
}

#banner {
	width: 100%;
	height: 100px;
	background: #3887BE;
	overflow: auto;
}

#banner h1 {
	position: relative;
	color: #fff;
	font-size: 50px;
	text-transform: uppercase;
	text-align: center;
	margin-top: 175px;
}

@media all and (max-width:768px){
	#banner h1 {
		margin-top: 125px;
	}
}

@media all and (max-width:480px){
	#banner h1 {
		font-size: 36px;
	}
}

#wrapper {
	width: 100%;
	max-width: 1140px;
	padding: 25px;
	margin: auto;
	border-radius: 10px;
	-webkit-border-radius: 10px;
	-moz-border-radius: 10px;
}

@media all and (max-width:768px){
	#wrapper {
		padding: 25px 0;
	}
}

.row:before,
.row:after {
	content: " ";
	display: table;
	clear: both;
}
/*.row {
	margin: auto -20px;
}*/

.column-1,
.column-2,
.column-3,
.column-4 {
	min-height: 1px;
	float: left;
	padding: 20px;
	box-sizing: border-box;
}

.column-1 {
	width: 25%;
}

.column-2 {
	width: 50%;
}

.column-3 {
	width: 75%;
}

.column-4 {
	width: 100%;
}

.column-shift-half-col {
	margin-left: 12.5%;
}

.column-shift-1 {
	margin-left: 25%;
}

.column-shift-2 {
	margin-left: 50%;
}

.column-shift-3 {
	margin-left: 75%;
}

@media all and (max-width:768px){
    .column-1,
    .column-2,
    .column-3,
    .column-4 {
    	width: 100%;
    }

    .column-shift-half-col,
    .column-shift-1,
    .column-shift-2,
    .column-shift-3,
    .column-shift-4 {
    	margin-left: 0px;
    }
}

form label,
form input[type="text"],
form select {
	width: 100%;
	display: block;
}

form label {
	margin-top: 10px;
	margin-bottom: 5px;
}

form label > span {
	color: #B71C1C;
	font-weight: bold;
}

form input[type="text"],
form select {
	width: 100%;
	padding: 10px;
	border: 1px solid #cdcdcd;
	border-radius: 5px;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
}

form input[type="submit"] {
	width: 100%;
	padding: 10px;
	color: #fff;
	font-size: 16px;
	font-weight: bold;
	cursor: pointer;
	background: #3887BE;
	border: none;
	outline: none;
	border-radius: 5px;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
}

form input[type="submit"]:hover {
	background: #2D6C98;
}


form input[type="submit"]:disabled {
	cursor: auto;
	background: #C3DBEC;
}


form h2 {
	font-size: 30px;
	color: #3C4E5A;
}

@media all and (max-width:768px){
	form input[type="submit"] {

	}
}

.notice {
	font-size: 16px;
	padding: 5px 15px;
	background-color: #f4f4f4;
	border-radius: 10px;
	-webkit-border-radius: 10px;
	-moz-border-radius: 10px;
}

.notice.error {
	color: #fff;
	background: #B71C1C;
}

.notice.success {
	color: #fff;
	background: #78AE62;
}


.text-center {
	text-align: center;
}

.course-details h3,
.course-details h4 {
	margin: 0px;
}

.course-details h3 {
	color: #2D6C98;
}

.course-details button {
	color: #fff;
	padding: 5px 10px;
	background-color: #2D6C98;
	border: none;
	outline: none;
	cursor: pointer;
	border-radius: 10px;
	-webkit-border-radius: 10px;
	-moz-border-radius: 10px;
}

.course-details button:hover {
	background: #26597E;
}

.course-details button.added {
	background-color: #1eb87a;
}

.course-details button:disabled {
	background: #C3DBEC;
}

</style>


<script>
$(document).ready(function(){
	//input fields
	form_container = $('#form_container');
	student_id_input = $('input[name="student_id"]');
	first_name_input = $('input[name="first_name"]');
	middle_name_input = $('input[name="middle_name"]');
	last_name_input = $('input[name="last_name"]');
	email_input = $('input[name="email"]');
	home_phone_input = $('input[name="home_phone"]');
	cell_phone_input = $('input[name="cell_phone"]');
	street_address_input = $('input[name="street_address"]');
	city_input = $('input[name="city"]');
	state_input = $('input[name="state"]');
	zip_input = $('input[name="zip"]');
	csrf_token_input = $('input[name="_csrf_token"]');
	registration_submit_button = $('input[type="submit"][name="submit_registration"]');
	add_course_button = $('input[name="add_course"]');
	var courses = [];

	//the div that will contain all of our courses once they are loaded
	courses_container = $("#courses");

	//disable submit  button until courses have been loaded
	registration_submit_button.attr('disabled','disabled');

	//fetch courses
	fetchCourses();

	registration_submit_button.click(function(e){
		e.preventDefault();
		var student_info = {
			'student_id':student_id_input.val(),
			'first_name':first_name_input.val(),
			'middle_name':middle_name_input.val(),
			'last_name':last_name_input.val(),
			'email':email_input.val(),
			'home_phone':home_phone_input.val(),
			'cell_phone':cell_phone_input.val(),
			'street_address':street_address_input.val(),
			'city':city_input.val(),
			'state':state_input.val(),
			'zip':zip_input.val(),
		};

		if(courses.length == 0) {
			alert('You must enroll in at least one course.');
			return;
		}

		if(registerStudent(student_info, courses)) {
			//registration was successful. Reset the form

		}
	});

	//add course ID to 'courses' if the user clicks the 'add' button
	courses_container.on('click', 'button[name="add_course"]', function(e){
		e.preventDefault();
		//deselect course
		if($(this).hasClass('added')) {
			$(this).removeClass('added');
			$(this).html("Enroll");
			courses.splice($.inArray($(this).attr('value')), 1);
		//select course
		} else {
			if(courses.length == 3) {
				alert("You may not enroll in more than 3 courses.");
				return;
			}

			$(this).addClass('added');
			$(this).html("Enrolled");
			courses.push($(this).attr('value'));
		}
	});
});



/**
 * Send registration request
 * @param Array student_info - the data submitted from the registration form
 * @param Array courses - the courses the student would like to enroll
 */
function registerStudent(student_info, courses) {
	$.ajax({
		url: "index.php",
		type: "POST",
		dataType: 'json',
		data: {"action":"complete_registration","_csrf_token": csrf_token_input.val(), "student_info": student_info,"courses":courses},
		beforeSend: function(request) {
			$('form input').attr('disabled','disabled');
			$('.notice.error').remove();
			$('.notice.success').remove();
		},
		success: function(request) {
			if(request.state === true) {
				console.log(request);
				$.each(request.response, function(key,value) {
					form_container.before('<div class="notice success"><p>' + value + '</p></div>');
				});

				//hide and reset the form
				form_container.slideUp();
				$('form').trigger("reset");
				//remove all courses from queue
				courses = [];
				//update courses
				fetchCourses();
			} else {
				errors = "";
				//show errors
				$.each(request.response, function(key, value){
					errors += '<div class="notice error"><p>' + value + '</p></div>';
				});

				form_container.prepend(errors);
				return false;
			}
		},
		error: function(request) {
			courses_container.html('<div class="notice error"><p>Registration Failed. Please try again.</p></div>');
			$('form input').removeAttr('disabled');
		},
		complete: function(request) {
			registration_submit_button.removeAttr('disabled');
			$('form input').removeAttr('disabled');
		}
	});
}


/**
 * Send request to retrieve information for each course and display them on the page.
 */
function fetchCourses() {
	$.ajax({
		url: "index.php",
		type: "POST",
		dataType: 'json',
		data: {"action":"fetch_courses","_csrf_token": csrf_token_input.val()},
		beforeSend: function(request) {

		},
		success: function(request){
			if(request.state == true) {
				courses = "";
				$.each(request.response, function(key,value){
					courses += '<div class="course-details">' + 
							'<div class="row">' + 
								'<div class="column-4">' + 
									'<h3>' + value.name + '</h3>' + 
									'<p><b>Teacher:</b> ' + value.teacher + '  |  <b>Credits:</b> ' + value.credits + '  |  <b>Meets on</b> ' + value.class_days + '  |  <b>Time:</b> ' + value.start_time + '-' + value.end_time + '</p>' +
									'<h4>Description</h4>' +
									'<p>' + value.description + '</p>';

									if(value.spots_available < 1) {
										courses += '<button type="button" disabled="disabled">Course Full</button>';
									} else {
										courses += '<button type="button" name="add_course" value="' + value.id + '">Enroll</button>';
									}

								courses += '<span>   <b>Spots Available:</b> ' + value.spots_available + ' out of ' + value.max_size + '</div>' +
							'</div>' +
							'</div><hr />';
				});

				courses_container.html(courses);
				registration_submit_button.removeAttr('disabled');
			} else {
				errors = "";

				$.each(request.response, function(key, value){
					errors += '<div class="notice error"><p>' + value + '</p></div>';
				});

				courses_container.html(errors);
			}
		},
		error: function(request){
			courses_container.html('<div class="notice error"><p>Failed to load courses. Please refresh the page.</p></div>');
		}
	});
}
</script>
	<head>
		<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
   		 <meta name="viewport" content="width=device-width, initial-scale=1">

		<title>Student Registration Form</title>
		<link rel="stylesheet" href="public/css/style.css">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
		<script type="text/javascript" src="public/js/script.js"></script>
	</head>
	<body>
		<div id="banner">
			<h1>student Registration Form</h1>
		</div>
		<div id="wrapper">
			<div class="notice">
				<p><i>The fields marked with ( * ) are <span>required</span>.</i></p>
			</div>
			<div class="row">
				<div class="column-shift-half-col column-3">
					<div id="form_container">
						<form action="" method="post">
							<h2>Personal Information</h2>
							<div class="row">
								<div class="column-2">
									<label for="student_id"><span>*</span> Student ID</label>
									<input type="text" name="student_id" />
								</div>
							</div>

							<div class="row">
								<div class="column-1">
									<label for="first_name"><span>*</span> First Name</label>
									<input type="text" name="first_name" />
								</div>
								<div class="column-1">
									<label for="middle_name">Middle Name</label>
									<input type="text" name="middle_name" />
								</div>
								<div class="column-1">
									<label for="last_name"><span>*</span> Last Name</label>
									<input type="text" name="last_name" />
								</div>
							</div>
							
							<div class="column-1">
									<label for="dob"><span>*</span> Date of Birth</label>
									<input type="text" name="dd/mm/yyyy" />
								</div>
								
								<div class="column-1">
									<label for="age"><span>*</span> Age</label>
									<input type="text" name="age" />
								</div>


							
							<div class="row">
							<h2>Contact Information</h2>
								<div class="column-1">
									<label for="email"><span>*</span> Email</label>
									<input type="text" name="email" />
								</div>
								<div class="column-1">
									<label for="home_phone">Home Phone</label>
									<input type="text" name="home_phone" />
								</div>
								<div class="column-1">
									<label for="cell_phone">Cell Phone</label>
									<input type="text" name="cell_phone" />
								</div>
								
								</div>
							
							<div class="column-2">
									<label for="emergency"><span>*</span>Emergency Contact (If Parent/Guardian cannot be reached)</label>
									<input type="text" name="emergency_contact" />
								</div>
								
								
								<div class="column-2">
									<label for="relationship"><span>*</span>Relationship to Student</label>
									<input type="text" name="relationship" />
								</div>

							<div class="row">
								<div class="column-2">
									<label for="street_address"><span>*</span> Street Address</label>
									<input type="text" name="street_address" />
								</div>
							</div>

							<div class="row">
								<div class="column-1">
									<label for="city"><span>*</span> City</label>
									<input type="text" name="city" />
								</div>
								<div class="column-1">
									<label for="state"><span>*</span> State (Abbr Only)</label>
									<input type="text" name="state" />
								</div>
								<div class="column-1">
									<label for="zip"><span>*</span> Zip</label>
									<input type="text" name="zip" />
								</div>
							</div>



							<div class="row">
								<div class="column-shift-1 column-1">
									<input type="hidden" name="_csrf_token" value="<?php print $_SESSION['_csrf_token']; ?>" />
									<input type="submit" value="Register" name="submit_registration" />
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>