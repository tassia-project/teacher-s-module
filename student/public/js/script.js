
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