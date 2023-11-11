<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
//database login info
$dbuser = "root";
$dbpw = "";
$db = "blockchain";
//Specific to you the store owner
$storeName = "My Ecommerce";
$rootURL = "http://yourrooturl.com/directory"; //example https://mysite.org  or http://yourhomepage.com/store
$yourEmail = "test@email.com";  //email notifications will be sent to this email when a new order is placed


//pw to access the admin pages
$adminPW = "123456"; 


//connect to the database
$conn = mysqli_connect("localhost", $dbuser, $dbpw, $db);
if(!$conn){
  die('Connection error check server log');
}

?>
