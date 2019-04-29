<?php

class Customer {

    public $id;
    public $name;
    public $email;
    public $mobile;
    public $password = ""; // text from HTML form
    public $passwordHashed; // hashed password
    private $noerrors = true;
    private $nameError = null;
    private $emailError = null;
    private $mobileError = null;
    private $passwordError = null;
    private $clubNameError = null;
    Private $distanceError = null;
    public $ClubName = null;
    private $ClubNameError = null;
    private $DistaceError = null;
    public $distance = null;
    private $title = "Shot"; //tab name
    private $tableName = "customers";
    // initialize $_FILES variables
    private $fileName = '';
    private $tmpName = '';
    private $fileSize = '';
    private $fileType = '';
    private $content = '';
    public $file = false;

    function create_user_shot() {//this is going to allow the user to add a shot. 
        $this->generate_html_top(5);
        $this->generate_form_group("Club Name", $this->ClubNameError, $this->ClubName, "disabled");
        $this->generate_form_group("Distance in feet", $this->DistaceError, $this->distance);
        $this->generate_html_bottom(1);
    }

    function create_club() {//this is going to allow a user to create a club to add to their records. 
        $this->generate_html_top(6);
        $this->generate_form_group("Club Name", $this->ClubNameError, $this->ClubName);
        $this->generate_form_group("Distance in feet", $this->DistaceError, $this->distance);
        $this->generate_html_bottom(1);
    }

    function create_user_record() { // display "create" form
        $this->generate_html_top(1);
        $this->generate_form_group("name", $this->nameError, $this->name, "autofocus");
        $this->generate_form_group("email", $this->emailError, $this->email);
        $this->generate_form_group("mobile", $this->mobileError, $this->mobile);
       $this->generate_form_group("password", $this->passwordError, $this->password, "", "password");
	
        $this->generate_html_bottom(1);
    }
    
    function upload_file_form(){//this allows a user to upload a picture. 
        $this->generate_upload_top();
                echo "
			<div class='control-group '>
				<label class='control-label'>Picture</label>
				<div class='controls'>
					<input type='hidden' name='MAX_FILE_SIZE' value='16000000'>
					<input name='userfile' type='file' id='userfile'>
				</div>
			</div>
			";
        $this->generate_html_bottom(3);
    }
    
    function insert_db_file(){//upload the file
            $this->fileName = $_FILES['userfile']['name'];
        
            $this->tmpName = $_FILES['userfile']['tmp_name'];
            $this->fileSize = $_FILES['userfile']['size'];
            $this->fileType = $_FILES['userfile']['type'];
            $this->content = file_get_contents($this->tmpName);
            //var_dump($this);

            $pdo = Database::connect();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "UPDATE clubmenowusers set filename = ?, filesize = ?, filetype = ?, Content = ? "; 
            $sql .= "WHERE id = ?";

            $q = $pdo->prepare($sql);

            $q->execute(array($this->fileName, $this->fileSize, $this->fileType, $this->content,
                    $_SESSION['tJHSQRuoNnWUwLRe']));
 
            
          header("Location: customers.php ");
   
}

// end function create_user_record()

    function read_record($id) { // display "read" form
        $this->select_db_record($id);
        $this->generate_html_top(2);
        echo "
				<div class='control-group col-md-6'>
					<div class='controls '>
			";
        if ($this->fileSize > 0) {
            echo "<img height='100%' width='100%' src='data:image/jpeg;base64, "
            . base64_encode($this->content) . "' />";
        } else {
            echo 'No photo on file.';
        }
        echo "
					</div>
				</div>
			";
        $this->generate_form_group("name", $this->nameError, $this->name, "disabled");
        $this->generate_form_group("email", $this->emailError, $this->email, "disabled");
        $this->generate_form_group("mobile", $this->mobileError, $this->mobile, "disabled");
        $this->generate_html_bottom(2);
    }

// end function read_record()

    function update_record($id) { // display "update" form
        if ($this->noerrors) {
            $this->select_db_record($id);
        }
        $this->generate_html_top(3, $id);

        $this->generate_form_group("name", $this->nameError, $this->name, "autofocus onfocus='this.select()'");
        $this->generate_form_group("email", $this->emailError, $this->email);
        $this->generate_form_group("mobile", $this->mobileError, $this->mobile);
        $this->generate_form_group("password", $this->$passwordError, $this->password, "", "password");

        $this->generate_html_bottom(3);
    }

// end function update_record()

    function delete_record($ClubName) { // display "read" form
        $this->generate_html_top(4, $id);
        $this->generate_form_group("Club Name", $this->clubNameError, $this->ClubName, "disabled");
        $this->generate_html_bottom(4);
    }

// end function delete_record()

    /*
     * This method inserts one record into the table, 
     * and redirects user to List, IF user input is valid, 
     * OTHERWISE it redirects user back to Create form, with errors
     * - Input: user data from Create form
     * - Processing: INSERT (SQL)
     * - Output: None (This method does not generate HTML code,
     *   it only changes the content of the database)
     * - Precondition: Public variables set (name, email, mobile)
     *   and database connection variables are set in datase.php.
     *   Note that $id will NOT be set because the record 
     *   will be a new record so the SQL database will "auto-number"
     * - Postcondition: New record is added to the database table, 
     *   and user is redirected to the List screen (if no errors), 
     *   or Create form (if errors)
     */

    function insert_db_record() {
     

        if (!isset($_GET['ClubName']) && !isset($_POST['Club_Name'])) {
            if ($this->fieldsAllValid()) { // validate user input
                // if valid data, insert record into table
                $pdo = Database::connect();
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $this->passwordHashed = MD5($this->password);
                // safe code
                $sql = "INSERT INTO clubmenowusers (name,email,mobile, passwordHashed) values(?, ?, ?, ?)";

                // dangerous code
                //$sql = "INSERT INTO $this->tableName (name,email,mobile) values('$this->name', '$this->email', '$this->mobile')";
                $q = $pdo->prepare($sql);
                // safe code
                $q->execute(array($this->name, $this->email, $this->mobile, $this->passwordHashed));
                Database::disconnect();
                header("Location: customers.php ");
            } else {
                // if not valid data, go back to "create" form, with errors
                // Note: error fields are set in fieldsAllValid ()method
                $this->create_user_record();
            }
        } else {
            if (isset($_GET["ClubName"]))
                $this->ClubName = $_GET["ClubName"];
            if ($this->fieldsAllValid("true")) {
                //echo "<script>alert('fleids are good')</script>";
                $pdo = Database::connect();
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $sql = "INSERT INTO clubmenowshots (ClubName,DistanceInFeet, UserID) values(?, ?, ?)";
                $q = $pdo->prepare($sql);

                if (isset($_GET["ClubName"])) {
                    $q->execute(array($_GET['ClubName'], $this->distance, $_SESSION['tJHSQRuoNnWUwLRe']));
                } else {
                    $q->execute(array($this->ClubName, $this->distance, $_SESSION['tJHSQRuoNnWUwLRe']));
                }

                Database::disconnect();
                header("Location: customers.php ");
                //header("Location: $this->tableName.php"); // go back to "list"
            } else {
                // if not valid data, go back to "create" form, with errors
                // Note: error fields are set in fieldsAllValid ()method
                $this->create_user_shot();
            }
        }
    }

// end function insert_db_record

    private function select_db_record($id) {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT * FROM clubmenowusers where id = ?";
        $q = $pdo->prepare($sql);
        $q->execute(array($id));
        $data = $q->fetch(PDO::FETCH_ASSOC);
        Database::disconnect();
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->mobile = $data['mobile'];
        $this->fileName = $data['filename'];
        $this->fileSize = $data['filesize'];
        $this->fileType = $data['filetype'];
        $this->content = $data['Content'];
    }

// function select_db_record()

    function update_db_record($id) {

        $this->id = $id;
        if ($this->fieldsAllValid()) {
            if (($this->password == '')) {
                $this->noerrors = true;
                $pdo = Database::connect();
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $sql = "UPDATE clubmenowusers  set name = ?, email = ?, mobile = ? "; 
                $sql .= "WHERE id = ?";
                $q = $pdo->prepare($sql);



                $q->execute(array($this->name, $this->email, $this->mobile,
                    $this->id));
            } else {
                $this->noerrors = true;
                $pdo = Database::connect();
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $sql = "UPDATE clubmenowusers  set name = ?, email = ?, mobile = ?, ";
                $sql .= "passwordHashed = ? ";
                $sql .= "WHERE id = ?";
                $q = $pdo->prepare($sql);
                $q->execute(array($this->name, $this->email, $this->mobile,
                    $this->passwordHashed, $this->id));
            }


            Database::disconnect();
            header("Location: $this->tableName.php");
        } else {
          //  echo "<script>alert('FAILED')</script>";
            $this->noerrors = false;
            $this->update_record($id);  // go back to "update" form
        }
    }

// end function update_db_record 

    function delete_db_record($ClubName) {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "DELETE FROM clubmenowshots WHERE ClubName = ? AND UserID = ?";
        $q = $pdo->prepare($sql);
        $q->execute(array($ClubName, $_SESSION['tJHSQRuoNnWUwLRe']));
        Database::disconnect();
        header("Location: $this->tableName.php");
    }

// end function delete_db_record()

    private function generate_html_top($fun, $id = null) {
        switch ($fun) {
            case 1: // create
                $funWord = "Create";
                $funNext = "insert_db_record";
                break;
            case 2: // read
                $funWord = "Read";
                $funNext = "none";
                break;
            case 3: // update
                $funWord = "Update";
                $funNext = "update_db_record&id=" . $id;
                break;
            case 4: // delete
                $funWord = "Delete";
                $funNext = "delete_db_record&ClubName=" . $this->ClubName;
                break;
            case 5://add a shot
                $funWord = "Add a Shot";
                $funNext = "insert_db_record&id=" . $_SESSION['tJHSQRuoNnWUwLRe'] . "&ClubName=" . $this->ClubName;
                break;
            case 6://add a club
                $funWord = "Add a club";
                $funNext = "insert_db_record&id=" . $_SESSION['tJHSQRuoNnWUwLRe'];
                break;

            default:
                echo "Error: Invalid function: generate_html_top()";
                exit();
                break;
        }
        echo "<!DOCTYPE html>
        <html>
            <head>
                <title>$funWord a $this->title</title>
                    ";
        echo "
                <meta charset='UTF-8'>
                <link href='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css' rel='stylesheet'>
                <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js'></script>
                <style>label {width: 5em;}</style>
                    ";
        echo "
            </head>";
        echo "
            <body>
                <div class='container'>
                    <div class='span10 offset1'>
                        <p class='row'>
                            <h3>$funWord a $this->title</h3>
                        </p>
                        <form class='form-horizontal' action='$this->tableName.php?fun=$funNext' method='post' enctype='multipart/form-data'>                        
                    ";
    }

// end function generate_html_top()
    private function generate_upload_top(){
                        $funWord = "Update";
         $funNext = "insert_db_file&id=" .$_SESSION['tJHSQRuoNnWUwLRe'];
          echo "<!DOCTYPE html>
        <html>
            <head>
                <title>$funWord a $this->title</title>
                    ";
        echo "
                <meta charset='UTF-8'>
                <link href='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css' rel='stylesheet'>
                <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js'></script>
                <style>label {width: 5em;}</style>
                    ";
        echo "
            </head>";
        echo "
            <body>
                <div class='container'>
                    <div class='span10 offset1'>
                        <p class='row'>
                            <h3>$funWord a $this->title</h3>
                        </p>
                        <form class='form-horizontal' action='$this->tableName.php?fun=$funNext' method='post' enctype='multipart/form-data'>                        
                    ";
    }
    

    private function generate_html_bottom($fun) {
        switch ($fun) {
            case 1: // create
                $funButton = "<button type='submit' class='btn btn-success'>Create</button>";
                $updateBut = "";
                break;
            case 2: // read
                $funButton = "";
                break;
            case 3: // update
                $funButton = "<button type='submit' class='btn btn-warning'>Update</button>";
                $updateBut = "";
                break;
            case 4: // delete
                $funButton = "<button type='submit' class='btn btn-danger'>Delete</button>";
                $updateBut = "";
                break;
            default:
                echo "Error: Invalid function: generate_html_bottom()";
                exit();
                break;
        }
        echo " 
                            <div class='form-actions'>
                            $updateBut 
                             <br>
                             $funButton
                                                               
                                <a class='btn btn-secondary' href='$this->tableName.php'>Back</a>
                            </div>
                        </form>
                    </div>

                </div> <!-- /container -->
            </body>
        </html>
                    ";
    }

// end function generate_html_bottom()

    private function generate_form_group($label, $labelError, $val, $modifier = "", $fieldType = "text") {
        echo "<div class='form-group";
        echo!empty($labelError) ? ' alert alert-danger ' : '';
        echo "'>";
        echo "<label class='control-label'>$label &nbsp;</label>";
        //echo "<div class='controls'>";
        echo "<input "
        . "name='$label' "
        . "type='$fieldType' "
        . "$modifier "
        . "placeholder='$label' "
        . "value='";
        echo!empty($val) ? $val : '';
        echo "'>";
        if (!empty($labelError)) {
            echo "<span class='help-inline'>";
            echo "&nbsp;&nbsp;" . $labelError;
            echo "</span>";
        }
        //echo "</div>"; // end div: class='controls'
        echo "</div>"; // end div: class='form-group'
    }

// end function generate_form_group()

    private function fieldsAllValid($clubs = false) {
        $valid = true;
        if (!$clubs) {
            if (empty($this->name)) {
                $this->nameError = 'Please enter Name';
                $valid = false;
            }
            if (empty($this->email)) {
                $this->emailError = 'Please enter Email Address';
                $valid = false;
            } else if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                $this->emailError = 'Please enter a valid email address: me@mydomain.com';
                $valid = false;
            }
            if (empty($this->mobile)) {
                $this->mobileError = 'Please enter Mobile phone number';
                $valid = false;
            }
        } else {
            //   echo "<script>alert('checking')</script>";
            if (empty($this->ClubName)) {
                //  echo "<script>alert('bad Name')</script>";                
                $this->ClubNameError;
                $valid = false;
            }

            if (empty($this->distance)) {
                // echo "<script>alert('empty distance')</script>";
                $this->DistanceError = "Plese Enter Distance";
                $valid = false;
            } else if (!is_numeric($this->distance)) {
                // echo "<script>alert('not number distance')</script>";
                $this->DistanceError = "Please enter only numbers for distance";
                $valid = false;
            }
        }

        return $valid;
    }

// end function fieldsAllValid() 

    function list_records() {
        echo "<!DOCTYPE html>
        <html>
            <head>
                <title>$this->title" . "s" . "</title>
                    ";
        echo "
                <meta charset='UTF-8'>
                <link href='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css' rel='stylesheet'>
                <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js'></script>
                    ";
        echo "
            </head>
            <body>
                <a href='https://github.com/Grimmjow91/ClubMeNow' target='_blank'>Github</a><br />               
				
                <div class='container'>
                    <p class='row'>
                        <h3>$this->title" . "s" . "</h3>
                    </p>
                    <p>
                        <a href='$this->tableName.php?fun=display_create_form' class='btn btn-success'>Create</a>
						<a href='logout.php' class='btn btn-warning'>Logout</a> 
                        <a href='$this->tableName.php?fun=display_read_form&id=" . $_SESSION['tJHSQRuoNnWUwLRe'] . "' class='btn btn-success'>See User </a>   
                        <a href='$this->tableName.php?fun=display_update_form&id=" . $_SESSION['tJHSQRuoNnWUwLRe'] . "' class='btn btn-success'>Update User </a>   
                        <a href='$this->tableName.php?fun=upload_file&id=". $_SESSION['tJHSQRuoNnWUwLRe'] . "' class='btn btn-success'>Upload avatar</a>   
                                                    
					</p>
                    <div class='row'>
                        <table class='table table-striped table-bordered' height=100px>
                            <thead>
                                <tr>
                                    <th>Club Name</th>
                                    <th>Average</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                    ";
        $pdo = Database::connect();
        $sql = "SELECT ClubName, avg(DistanceInFeet) FROM clubmenowshots WHERE UserID = " . $_SESSION['tJHSQRuoNnWUwLRe'] . " group BY ClubName ORDER BY ClubName DESC;";
        foreach ($pdo->query($sql) as $row) {
            echo "<tr>";
            //echo "<td><img height='100%' width='100%' src='data:image/jpeg;base64, " . base64_encode($row["Content"]) . "' /></td>";
            echo "<td>" . $row["ClubName"] . "</td>";
            echo "<td>" . $row["avg(DistanceInFeet)"] . "</td>";
            echo "<td>";
            echo "&nbsp;";
            //echo "<a class='btn btn-warning' href='$this->tableName.php?fun=display_update_form&id=" . $_SESSION['tJHSQRuoNnWUwLRe'] . "'>Add a Shot</a>";
            echo "<a class='btn btn-warning' href='$this->tableName.php?fun=add_db_Shot&id=" . $_SESSION['tJHSQRuoNnWUwLRe'] . '&clubName=' . $row["ClubName"] . "'>Add a Shot</a>";
            echo "&nbsp;";
            echo "<a class='btn btn-danger' href='$this->tableName.php?fun=display_delete_form&clubName=" . $row["ClubName"] . "'>Delete</a>";
            echo "</td>";
            echo "</tr>";
        }
        Database::disconnect();
        echo "
                            </tbody>
                        </table>
                    </div>
                </div>

            </body>

        </html>
                    ";
    }

// end function list_records()
}

// end class Customer