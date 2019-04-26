<?php

session_start();
if(!isset($_SESSION["tJHSQRuoNnWUwLRe"])){ // if "user" not set,
	session_destroy();
	header('Location: login.php');     // go to login page
	exit();
}

// include the class that handles database connections
require "database.php";

// include the class containing functions/methods for "customer" table
// Note: this application uses "customer" table, not "cusotmers" table
require "customers.class.php";
$cust = new Customer();
 
// set active record field values, if any 
// (field values not set for display_list and display_create_form)

/*ob_flush();
ob_start();
var_dump($POST);
file_put_contents("dump.txt", ob_get_flush());*/

if(isset($_GET["id"]))          $id = $_GET["id"]; 
if(isset($_POST["name"]))       $cust->name = htmlspecialchars($_POST["name"]);
if(isset($_POST["email"]))      $cust->email = htmlspecialchars($_POST["email"]);
if(isset($_POST["mobile"]))     $cust->mobile = htmlspecialchars($_POST["mobile"]);
// we don't need htmlspecialchars() because password is never displayed in browswer!
if(isset($_POST["password"]))     $cust->password = $_POST["password"];
$cust->passwordHashed = md5($cust->password);


if(isset($_GET["clubName"])) $cust ->ClubName = $_GET["clubName"];
if(isset($_POST["Distance_in_feet"])) $cust->distance = htmlspecialchars($_POST["Distance_in_feet"]);
if(isset($_POST["clubName"])) $cust ->ClubName = $_POST["clubName"];
if(isset($_POST["Club_Name"])) $cust ->ClubName = $_POST["Club_Name"];
// "fun" is short for "function" to be invoked 
if(isset($_GET["fun"])) $fun = $_GET["fun"];
else $fun = "display_list"; 

switch ($fun) {
    case "display_list":        $cust->list_records();
        break;
    case "display_create_form": $cust->create_club(); 
        break;
    case "display_read_form":   $cust->read_record($id); 
        break;
    case "display_update_form": $cust->update_record($id);
        break;
    case "display_delete_form": $cust->delete_record($cust->ClubName); 
        break;
    case "insert_db_record":    $cust->insert_db_record(); 
        break;
    case "update_db_record":    $cust->update_db_record($id);
        break;
    case "delete_db_record":    $cust->delete_db_record($_GET["ClubName"]);
        break;
    case "add_db_Shot":         $cust ->create_user_shot();
        break;
    case "upload_file":         $cust ->upload_file_form();
        break;
    case "insert_db_file":      $cust->insert_db_file();   
        break;
    default: 
        echo "Error: Invalid function call (customer.php)";
        exit();
        break;
}

