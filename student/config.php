<?php
function connect(){
  $dbserver="localhost";
  $username="root";
  $password="";
  $dbname="tassia_school";

 $conn=mysqli_connect($dbserver,$username,$password,$dbname) or die("Could not connect");
 // Check connection
 if (mysqli_connect_errno()) {
     echo "Connection failed: " .mysqli_connect_error();
 }
 echo "Connected successfully";

 return $conn;
}
function insertData($sql)
  {
    $conn = connect();
    if (mysqli_query($conn, $sql)) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}
  }


 ?>