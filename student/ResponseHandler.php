<?php
class ResponseHandler
{
	//success messages
	private $success;
	//error messages
	private $errors;

	public function __construct()
	{
		$this->success = array();
		$this->errors = array();
	}


	/**
	 * Prints JSON encoded 'success' response.
	 * This method is to be called when an event or action is successful, and
	 * some information must be returned to the end user.
	 *
	 * @param mixed $response - The data to be returned to the front end. This
	 *				  may be a simple message, or an array of data
	 *				  to be displayed on the webpage.
	 */
	public function success($response)
	{
		if(!is_array($response)) {
			$response = array($response);
		}

		print json_encode(array('state' => true,'response' => $response));
		exit();
	}


	/**
	 * Prints JSON encoded 'error' response.
	 * This method is to be called when an event or action fails, and
	 * a single error must be returned to the end user.
	 *
	 * @param String $response - the error message
	 */
	public function error($response)
	{
		if(!is_array($response)) {
			$response = array($response);
		}

		print json_encode(array('status' => false, 'response' => $response));
		exit();
	}


	/**
	 * Print all errors out in JSON.
	 *
	 * This method is meant to be called when one part of
	 * the form validation fails and at least one error has
	 * been added. In the occasion that this method
	 * is called without any errors being defined, it will add
	 * a generic error message and prompt the user to refresh.
	 *
	 * This method also assumes that there are no more actions
	 * to preform and will exit the program. Use the getErrors()
	 * method to retrieve the errors without exiting the program.
	 */
	public function throwErrors()
	{
		if(count($this->errors) == 0)
			$this->addError('An error occurred. Please refresh and try again.');
		print json_encode(array('state' => false, 'response' => $this->errors));
		exit();
	}


	/**
	 * Add a message to the list of errors
	 * @param String message - the error message to be added
	 */
	public function addError($message)
	{
		$this->errors[] = $message;
	}


	/**
	 * Return the list of errors
	 * @return Array
	 */
	public function getErrors()
	{
		return $this->errors;
	}


	/**
	 * Return true if one or more errors exists.
	 * @return boolean
	 */
	public function hasErrors()
	{
		if(count($this->errors) > 0) {
			return true;
		}
		return false;
	}
}