<?php
session_start();

require('config.php');
require('PDOAdapter.php');
require('ResponseHandler.php');

$responseHandler = new ResponseHandler();

//Is the request ajax or direct?
if(isAjax()) {
	//make sure our csrf token exists and is valid
   	validateCSRFToken($responseHandler);

	//Attempt to connect to database
	$db = new PDOAdapter($config['database']);
	
	//Throw error if database connection fails
	if(!$db->isConnected()) {
		$responseHandler->throwErrors();
	}

	if(isset($_POST['action'])) {
		switch ($_POST['action']) {
			case 'complete_registration':
				if(!isset($_POST['student_info']) || !isset($_POST['courses'])) {
					$responseHandler->throwErrors();
				}

				//generate an ID to be used as the key for this student's records in the database
				$id = $db->generateUuid();

				//insert the student's information into the database
				if(!registerStudent($id, $_POST['student_info'], $db, $responseHandler) ||
				   !registerCourses($id, $_POST['courses'], $db, $responseHandler)) {
				   	$responseHandler->throwErrors();
				}

				//send email notifications
				if(!sendNotifications($config['admin_email'], $_POST['student_info'], $_POST['courses'], $db)) {
					$responseHandler->success("You have successfully registered, but we were unable to send you a confirmation
									email. If you would like to confirm your registration, please email <b>" . $config['admin_email'] . "</b>");
				}

				//registration was a success, let the user know
				$responseHandler->success("You have successfully registered!");
				break;
			case 'fetch_courses':
				//retrieve the courses from the database
				$courses = fetchCourses($db);
				if(is_array($courses) && count($courses) > 0) {
					//change the format of the timestamps to HH:MM am/pm
					for($x = 0; $x < count($courses); $x++) {
						$courses[$x]['start_time'] = date("g:i a", strtotime($courses[$x]['start_time']));
						$courses[$x]['end_time'] = date("g:i a", strtotime($courses[$x]['end_time']));
					}

					$responseHandler->success($courses);
				}
				else {
					$responseHandler->addError("There are no courses available at this time.");
					$responseHandler->throwErrors();	
				}
				break;
			default:
				//An invalid action was submitted.
				$responseHandler->throwErrors();
				break;
		}
	} else {
		$responseHandler->throwErrors();
	}

} else {
	//generate a CSRF token
	$_SESSION['_csrf_token'] = md5(rand());
	
	//show the form
	include_once("./form_view.php");
}


/**
 * Validate and insert the student's information into the database.
 *
 * @param String $id - The ID to be used as the key for this student's records in the database
 * @param Array $student_info - The form fields submitted. Each index in the array
 *				       corresponds to each field in the 'students' table.
 * @param PDOAdapter $db - The database connection.
 * @param ResponseHandler $responseHandler - An instance of ResponseHandler. Allows us to collect
 *						        and display error/success messages.
 *
 * @return boolean  | This method will only return true. If any part of the data validation or
 * 			 database insertion fails, it will exit the program and throw an error (in JSON format).
 */
function registerStudent($id, $student_info, $db, $responseHandler)
{
	$expected_fields = array(
		'student_id',
		'first_name',
		'middle_name',
		'last_name',
		'email',
		'street_address',
		'city',
		'state',
		'zip',
		'home_phone',
		'cell_phone'
	);

	$required_fields = array(
		'student_id',
		'first_name',
		'last_name',
		'email',
		'street_address',
		'city',
		'state',
		'zip'
	);

	//make sure all fields were submitted
	foreach($student_info as $record => $value) {
		if(!in_array($record, $expected_fields)) {
			$responseHandler->throwErrors();
		}
	}


	//make sure all required fields have a value
	foreach($student_info as $record => $value) {
		if(empty($value) && in_array($record, $required_fields)) {
			$responseHandler->error("All fields with an asterisk must be filled in.");
		}
	}

	//validate student ID
	if(!is_numeric($student_info['student_id']) || strlen((string)$student_info['student_id']) != 9) {
		$responseHandler->addError("Student ID must contain a 9 digit number.");
	}


	//validate first name
	$student_info['first_name'] = str_replace("/\s+/", " ", $student_info['first_name']);
	if(!preg_match('/^[a-zA-Z-]+$/', $student_info['first_name'])) {
		$responseHandler->addError("First Name may only contain alphabetic characters and dashes.");
	}

	//validate middle name
	$student_info['middle_name'] = str_replace("/\s+/", " ", $student_info['middle_name']);
	if(!empty($student_info['middle_name']) && !preg_match('/^[a-zA-Z-]+$/', $student_info['middle_name'])) {
		$responseHandler->addError("Middle Name may only contain alphabetic characters and dashes.");
	}

	//validate last name
	$student_info['last_name'] = str_replace("/\s+/", " ", $student_info['last_name']);
	if(!preg_match('/^[a-zA-Z-]+$/', $student_info['last_name'])) {
		$responseHandler->addError("Last Name may only contain alphabetic characters and dashes.");
	}

	//validate email
	if(!filter_var($student_info['email'],FILTER_VALIDATE_EMAIL)) {
		$responseHandler->addError("Please enter a valid email.");
	}

	//validate home phone
	if(!empty($student_info['home_phone'])) {
		if(!preg_match('/^((\()[0-9]{3}(\)))?[0-9]{3}(-)?[0-9]{4}+$/', $student_info['home_phone'])) {
			$responseHandler->addError("Please enter a valid home phone number. (xxx)xxx-xxxx");
		} else {
			$student_info['home_phone'] = preg_replace("/[^0-9]/", "", $student_info['home_phone']);
		}
	}

	//validate cell phone
	if(!empty($student_info['cell_phone'])) {
		if(!preg_match('/^((\()[0-9]{3}(\)))?[0-9]{3}(-)?[0-9]{4}+$/', $student_info['cell_phone'])) {
			$responseHandler->addError("Please enter a valid cell phone number. (xxx)xxx-xxxx");
		} else {
			$student_info['cell_phone'] = preg_replace("/[^0-9]/", "", $student_info['cell_phone']);
		}
	}

	//validate street address
	if(strlen($student_info['street_address']) < 5 || !preg_match("/^[a-zA-Z0-9-. ]+$/", $student_info['street_address'])) {
		$responseHandler->addError("Please enter a valid street address.");
	}

	//validate city
	if(!preg_match("/^[a-zA-Z]+$/", $student_info['city'])) {
		$responseHandler->addError("City may only contain alphabetic characters.");
	}

	//validate state
	if(!preg_match("/^[a-zA-Z]{2}+$/", $student_info['state'])) {
		$responseHandler->addError("Please enter a valid state abbreviation.");
	}

	//validate zip code
	if(!is_numeric($student_info['zip']) || strlen((string)$student_info['zip']) != 5) {
		$responseHandler->addError("Please enter a valid zip code.");
	}


	//where there any errors?
	if($responseHandler->hasErrors()) {
		$responseHandler->throwErrors();
	}

	//sanitize values
	$student_info['student_id'] = filter_var($student_info['student_id'], FILTER_SANITIZE_NUMBER_INT);
	$student_info['first_name'] = filter_var($student_info['first_name'], FILTER_SANITIZE_STRING);
	$student_info['middle_name'] = filter_var($student_info['middle_name'], FILTER_SANITIZE_STRING);
	$student_info['last_name'] = filter_var($student_info['last_name'], FILTER_SANITIZE_STRING);
	$student_info['email'] = filter_var($student_info['email'], FILTER_SANITIZE_EMAIL);
	$student_info['home_phone'] = filter_var($student_info['home_phone'], FILTER_SANITIZE_NUMBER_INT);
	$student_info['cell_phone'] = filter_var($student_info['cell_phone'], FILTER_SANITIZE_NUMBER_INT);
	$student_info['street_address'] = filter_var($student_info['street_address'], FILTER_SANITIZE_STRING);
	$student_info['city'] = filter_var($student_info['city'], FILTER_SANITIZE_STRING);
	$student_info['state'] = filter_var($student_info['state'], FILTER_SANITIZE_STRING);
	$student_info['zip'] = filter_var($student_info['zip'], FILTER_SANITIZE_NUMBER_INT);

	//prepare the id and student information for database querying
	$db_fields= array_merge(array('id' => $id), $student_info);

	try {
		//Insert the student's information into the database
		$insertQuery = $db->query("INSERT INTO students 
			VALUES (:id, 
				:student_id, 
				:first_name, 
				:middle_name, 
				:last_name, 
				:email, 
				:street_address, 
				:city, 
				:state, 
				:zip, 
				:home_phone, 
				:cell_phone)", $db_fields);
		$db->execute();
	} catch(PDOException $e) {
		//let the user know if their information has already been submitted
		if(intVal($e->getCode()) === 23000) {
			$responseHandler->error('You have already registed.');
		}

		$responseHandler->error("Registration failed. Please refresh and try again.");
	}

	return true;
}


/**
 * Register the student for each course.
 *
 * @param String $id - The ID to be used as the key for this student's records in the database.
 * @param Array $courses - The ID's for each course the student is attempting to register for.
 * @param PDOAdapter $db - The database connection.
 * @param ResponseHandler $responseHandler - An instance of ResponseHandler. Allows us to collect
 *						        and display error/success messages.
 *
 * @return boolean
 */
function registerCourses($id, $courses, $db, $responseHandler)
{
	//a student may only enroll in up to 3 courses
	if(count($courses) == 0 || count($courses) > 3) {
		$responseHandler->error("Please select up to 3 course to enroll.");
	}

	//validate the course ID's and check if the course has spots available
	foreach($courses as $course_id) {
		$course_id = filter_var($course_id, FILTER_SANITIZE_STRING);

		//validate the course ID
		if(empty($course_id) || 
		   !preg_match('/^[a-z0-9]{8}(-)[a-z0-9]{4}(-)[a-z0-9]{4}(-)[a-z0-9]{4}(-)[a-z0-9]{12}+$/', $course_id) ||
		   !courseExists($course_id, $db)) {
			$responseHandler->error('Failed to register courses. Please Refresh and try again.');
		}

		//get the course from the database
		$course = fetchCourse($course_id, $db);

		//are there spots available?
		if(!$course || $course['spots_available'] < 1) {
			$responseHandler->addError($course['name'] . ' is full. Please select another course.');
		}
	}

	//throw errors if any were found
	if($responseHandler->hasErrors()) {
		$responseHandler->throwErrors();
	}

	//register student for courses
	foreach($courses as $course_id) {
		$registrationDetails = array('id' => $db->generateUuid(), 'student_id' => $id, 'course_id' => $course_id);
		$db->query('INSERT INTO course_registrations VALUES(:id,:student_id,:course_id)', $registrationDetails);
		$db->execute();

		if($db->countAffectedRows() != 1) {
			//Registration failed, undo all registration steps so the student
			//may attempt to register again.
			removeStudent($id, $db);
			$responseHandler->error("Course registration failed. Please try again.");
		}
	}

	return true;
}


/**
 * Checks if the a course with the given course id exists.
 * @param String $course_id - the primary key for the course
 * @param PDOAdapter $db - the database connection
 */
function courseExists($course_id, $db)
{
	try {
		$db->query('SELECT COUNT(*) AS course_exists FROM courses WHERE id = :id', array('id' => $course_id));
		$results = $db->single();

		if(isset($results['course_exists']) && $results['course_exists'] != 1) {
			return false;
		}
	} catch(PDOException $e) {
		return false;
	}

	return true;
}


/**
 * Retrieve all courses from the database.
 * @param PDOAdapter $db - database connection
 *
 * @return Array
 */
function fetchCourses($db)
{
	$db->query("SELECT
			courses.id,
			courses.name,
			courses.description,
			courses.credits,
			courses.teacher,
			courses.class_days,
			courses.start_time,
			courses.end_time,
			courses.max_size,
			courses.max_size - (SELECT COUNT(id) 
					        FROM course_registrations 
					        WHERE course_id = courses.id) 
					        AS spots_available
			FROM courses");
	return $db->results();
}


/**
 * Retrieve a single course from the database.
 * @param String $course_id - the primary key for the course
 * @param PDOAdapter $db - database connction
 *
 * @return Array | boolean
 */
function fetchCourse($course_id, $db)
{
	try {
		$db->query("SELECT
				courses.id,
				courses.name,
				courses.description,
				courses.credits,
				courses.teacher,
				courses.class_days,
				courses.start_time,
				courses.end_time,
				courses.max_size,
				courses.max_size - (SELECT COUNT(id) 
						        FROM course_registrations 
						        WHERE course_id = courses.id) 
						        AS spots_available
				FROM courses
				WHERE id = :id", array('id' => $course_id));

		return $db->single();
	} catch(PDOException $e) {
		return false;
	}

	return false;
}


/**
 * Remove all of a student's records from all tables
 * @param String $id - the primary key for the student's records
 * @param PDOAdapter $db - the database connection
 *
 * @return boolean | true if the removal was successful
 */
function removeStudent($id, $db) {
	try {
		$db->query('DELETE * FROM course_registrations WHERE student_id = :student_id', array('student_id' => $id));
		$db->execute();
		$db->query('DELETE * FROM students WHERE id = :student_id', array('student_id' => $id));
		$db->execute();
	} catch(PDOException $e) {
		return false;
	}

	return true;
}


/**
 * Send two emails: one to notify the administrator of the student's registration,
 * and the second to notify the student.
 * @param String $admin_email - the aministrator's email (defined in config.php)
 * @param String $student_email - the student's email (provided in form submittion)
 * @param Array $courses_id - a list of the courses the student enrolled
 * @param PDOAdapter $db - database connection
 *
 * @return boolean
 */
function sendNotifications($admin_email, $student_info, $courses_id, $db)
{
	//sanitize user input
	$student_info['email'] = filter_var($student_info['email'], FILTER_SANITIZE_EMAIL);
	$student_info['first_name'] = filter_var($student_info['first_name'], FILTER_SANITIZE_STRING);
	$student_info['middle_name'] = filter_var($student_info['middle_name'], FILTER_SANITIZE_STRING);
	$student_info['last_name'] = filter_var($student_info['last_name'], FILTER_SANITIZE_STRING);

	//retrieve course information
	$course_information = "";
	foreach($courses_id as $id) {
		$id = filter_var($id, FILTER_SANITIZE_STRING);
		$course = fetchCourse($id, $db);

		$course_information .= "- " . $course['name'] . "\n";
	}

	//admin email
	$admin_email_body = "A new student has registred for courses.\n
				  Name: " . $student_info['first_name'] . " " . $student_info['middle_name'] . " " . $student_info['last_name'] . "\n
				  Email: " . $student_info['email'] . "\n
				  Courses Enrolled: \n"
				  . $course_information . "\n\n";

	//student email
	$student_email_body = "Your registration was successful!.\n
				  Courses Enrolled: \n"
				  . $course_information . "\n\n
				  If you have any questions, contact an administrator at " . $admin_email;		  

	//send emails
	try {
		if(!mail($admin_email, "Student Registration Notification", $admin_email_body) ||
		   !mail($student_info['email'], "Student Registration Notification", $student_email_body)) {
			return false;
		}
	} catch(Exception $e) {
		return false;
	}

	return true;
}


/**
 * Check if the request was made by Ajax.
 * @return boolean
 */
function isAjax()
{
	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
   	   strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		return true;
	}

	return false;
}


/**
 * Verify that a CSRF token exists and is valid. This method will
 * automatically throw an error and exit further processing if
 * a valid token is not found.
 *
 * @param ResponseHandler $responseHandler - An instance of ResponseHandler. Allows us to collect
 *						        and display error/success messages.
 */
function validateCSRFToken($responseHandler)
{
	//Make sure the CSRF token exists
	if(isset($_POST['_csrf_token']) && isset($_SESSION['_csrf_token'])) {
		//Disallow duplicate submissions
		if($_POST['_csrf_token'] != $_SESSION['_csrf_token']) {
			$responseHandler->addError('You have already submitted this form. Please refresh the page to fill it out again.');
			$responseHandler->throwErrors();
		}
	} else {
		//The CSRF token wasn't found
		$responseHandler->addError('An error occurred. Please refresh the page and try again.');
		$responseHandler->throwErrors();
	}
}