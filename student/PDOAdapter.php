<?php

class PDOAdapter
{
	//mysql host
	private $host;
	//mysql username
	private $username;
	//mysql password
	private $password;
	//mysql database name
	private $database_name;
	//database connection
	private $pdo_connection;
	//the query to execute
	private $query;
	//PDO configuration
	private $pdo_settings;

	public function __construct(Array $config)
	{
		//attempt to connect to database
		try {
			$this->host = $config['host'];
			$this->username = $config['username'];
			$this->password = $config['password'];
			$this->database_name = $config['database_name'];

			$this->pdo_settings = array(
				PDO::ATTR_PERSISTENT => true,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
			);

			$this->pdo_connection = new PDO("mysql:host=$this->host;dbname=$this->database_name", 
				$this->username, $this->password, $this->pdo_settings);
		} catch(PDOException $e) {
			//Failed to connect to database. The isConnected() method will detect this.
		}
	}


	/**
	 * Check if database connection is active
	 *
	 * @return boolean
	 */
	public function isConnected()
	{
		if(is_null($this->pdo_connection)) {
			return false;
		}

		return true;
	}


	/**
	 * Prepare a given database query
	 * @param String $queryString - the query to execute
	 * @param Array $parameters - the values to bind to the query
	 */
	public function query($queryString, $parameters = array())
	{
		$this->query = $this->pdo_connection->prepare($queryString);

		foreach($parameters as $placeholder => $value) {
			$this->bindParameters(':' . $placeholder, $value);
		}
	}


	/**
	 *  Bind values to query placeholders
	 *  @param String placeHolder - the temporary placeholder from the query string,
	 *				    that the value will be binded to.
	 * @param mixed $value - the data that will be binded to it's corresponding placeholder
	 */
	public function bindParameters($placeHolder, $value)
	{
		if(is_int($value)) {
			$type = PDO::PARAM_INT;
		} elseif(is_bool($value)) {
			$type = PDO::PARAM_BOOL;
		} elseif(is_null($value)) {
			$type = PDO::PARAM_NULL;
		} else {
			$type = PDO::PARAM_STR;
		}

		$this->query->bindValue($placeHolder, $value, $type);
	}


	/**
	 * Execute the database query. (Assumes that $this->query has already been defined)
	 */
	public function execute()
	{
		$this->query->execute();
	}


	/**
	 * Get an associative array of results from the database query. This
	 * method should be called when a database query is expected to
	 * return multiple rows of data.
	 *
	 * @return Array
	 */
	public function results()
	{
		$this->execute();
		$results = $this->query->fetchAll(PDO::FETCH_ASSOC);

		if(!$results || count($results) == 0) {
			return false;
		}

		return $results;
	}


	/**
	 * Get an associative array of results from the database query. This
	 * method should be called when a database query is expected to
	 * return a single row of data.
	 *
	 * @return Array
	 */
	public function single()
	{
		$this->execute();
		$results = $this->query->fetch(PDO::FETCH_ASSOC);

		if(!$results || count($results) == 0) {
			return false;
		}

		return $results;
	}


	/**
	 * Count the number of table rows that were affected by the query.
	 * This method assumes that the query has already been executed.
	 *
	 * @return int
	 */
	public function countAffectedRows()
	{
		return $this->query->rowCount();
	}


	/**
	 * Each item in the database is indexed by a Universally Unique ID (or UUID).
	 * This method generates that UUID.
	 *
	 * @return String
	 */
	public function generateUuid()
	{
		$this->query("SELECT UUID() AS uuid");
		$result = $this->single();
		return $result['uuid'];
	}
}