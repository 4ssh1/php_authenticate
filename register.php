<section>
    <style>
        a{
            text-decoration:none;
        }
        .card{
            width: 40%;
            border: 1px solid rgba(27, 3, 3, 0.5);
            border-radius: 10px;
            padding: 20px 0;
            background-color: rgba(255, 255, 255, 0.96);
            display:flex;
            flex-direction:column; 
            justify-content:center; 
            align-items:center;
            box-shadow: 0px 3px 8px rgb(0,0,0)
        }
        .card h2{
            border-bottom: 1px solid;
        }
        .form{
            border: 1px solid rgba(27, 3, 3, 0.5);
            padding: 10px;
            border-radius: 10px;
        }
        .form div{
             padding: 10px 0px;
        }
        .form label{
            width: 200px; display:inline-block;
        }
        .form input{
            padding: 5px 10px;
            outline:none;
            border-radius: 10px;
            border: 1px solid rgba(27, 3, 3, 0.5);
        }
        .form input:focus{
            background-color:rgb(206, 206, 206);
        }
        .form span{
            color: red;
            display: block;
            font-size: 9px;
            max-width: 350px;
        }
        .btn_div{
            display: flex;
            padding-top: 30px;
            gap: 20px;
        }
        .btn_div button{
            border: 1px solid rgba(33, 32, 32, 0.4);
            padding: 5px 10px;
            cursor: pointer;
            background-color: rgb(170, 236, 71);
            border-radius: 5px;
        }
        .btn_div button:hover{
            background-color: transparent;
        }

        @media (max-width:600px) {
            .card{
                width:100%;
                padding: 5px 0;
            }
            .form{
                padding: 10px;
                margin: 0 5px;
            }
            .btn_div{
                padding-top: 15px;
                gap: 50px;
            }
            .form div{
                padding: 5px 0px;
            }
        }
        
        </style>
    <?php 
    include "header.php";
    
    if (isset($_SESSION["email"])){
        header("location: /index.php");
        exit;
    }

    require_once "db.php";
    
    $conn = getDatabaseConnection();
    $validationS = new FormValidate($conn);
    
    $fields = [
        "name" => ["validateName", "validate"],
        "email" => ["validateEmail", "validate"],
        "password" => ["validatePassword", "validate"],
        "address" => ["validate", ],
        "phone" => ["validatePhone", "validate"]
    ];
    
    $inputs = [
        "name" => "",
        "email" => "",
        "phone" => "",
        "password" => "",
        "address" => ""
     ];

    
     class FormValidate{
        private $errors = []; //errors is an array and cannot be accessed anywhere else
        private $dbconnection;

        public function __construct($dbconnection) {
            $this->dbconnection = $dbconnection;
        }

        function validate($field, $value){
            if(empty(trim($value))){
                $this->errors[$field] = ucfirst($field) ." is required";
            }
            
                /*$this refers to the current object inside the FormValidate class, ucfirst methods make the first letter to be uppercase, 
                errors[fields], this makes errors an associateve array with $fields as the key, (think of -> as a . for objects in javasript).
                . is used for contenation in php
                public, private or protected function modifiers are only used in class. Protected functions are available within
                classes and subclasses only*/
        }

       function validateEmail($field, $value) {
            if(!filter_var($value, FILTER_VALIDATE_EMAIL)){ 
                $this->errors[$field] = "Invalid email format";
            }elseif($this->emailExists($value)){
                $this->errors[$field] = "Email is already in use";
            }

            // include "db.php";
            // $dbconnection = getDatabaseConection();

        }
        private function emailExists($email){
            $statement = $this->dbconnection->prepare("SELECT id FROM users WHERE email = ?");

            //bind variables to the prepared statement as parameters
            $statement->bind_param("s", $email);

            //execute statement
            $statement->execute();
            $statement->store_result();
            $exists = $statement->num_rows > 0;
            $statement->close();
            return $exists;
        }
       function validatePassword($field, $value) {
            if(!preg_match("/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/" , $value)){
                $this->errors[$field] = "password must be at least 8 characters, with uppercase, lowercase, number and special characters";
            }
       }

       function validatePhone($field, $value) {
            if(!preg_match("/^\d{11,13}$/", $value)){
                $this->errors[$field] = "Invalid phone format";
            }
       }

       function validateName($field, $value) {
            if(!preg_match("/^[a-zA-Z\s]+$/", $value)){
                $this->errors[$field] = "Invalid name format";
            }
       }

       function getErrors(){
            return $this->errors; //get the errors and the function can only be called here class since the errors is private and 
                                  //it's only accessible in the class 
       }

       function isValid(){
            return empty($this->errors); //returns a boolean, if errors is empty, it's true, otherwise false
       }
     }

     

    
     
     
     if($_SERVER["REQUEST_METHOD"] == "POST"){
        foreach ($fields as $field => $errorCheckers){
           $inputs[$field] = $_POST[$field] ?? ""; //sets the value to be the name of the field
               foreach ($errorCheckers as $errorChecker){
                   $validationS->$errorChecker($field, $inputs[$field]); //each $errorChecker becomes validate and validate..., 
                                                                         //so it becomes $validationS->validate, $validationS->validate...
               }
            } 
            if($validationS->isValid()){

                $fullName = $inputs["name"];  //trim($_POST["name"])
                $nameParts = preg_split('/\s+/', trim($fullName), 2);
                $first_name = $nameParts[0] ?? "";
                $last_name = $nameParts[1] ?? "";
                $email = $inputs["email"];
                $phone = $inputs["phone"];
                $address = $inputs["address"];
                $password = password_hash($inputs["password"], PASSWORD_DEFAULT) ;
                $created_at = date("Y-m-d H:i:s");

             //    $nameParts = explode(" ", $fullName, 2); // Split into two parts
             //    $firstName = $nameParts[0] ?? ""; // First part
             //    $lastName = $nameParts[1] ?? ""; // Second part (if exists)
             

                //lets use prepared statements to avoid "sql injection attacks"

                 $statement = $conn->prepare(
                     "INSERT INTO users (first_name, last_name, email, phone, address, password, created_at) " .
                     "VALUES (?, ?, ?, ?, ?, ?, ?)"
                 );

                 //bind variables to the prepared statement as parameters
                 $statement->bind_param("sssssss", $first_name, $last_name, $email, $phone, $address, $password, $created_at);

                 //execute statement
                 $statement->execute();

                 $insert_id = $statement->insert_id;

                 $statement->close();

                 //a new account is created 
                 //save session data

                 $_SESSION["id"] = $insert_id;
                 $_SESSION["first_name"] = $first_name;
                 $_SESSION["last_name"] = $last_name;
                 $_SESSION["email"] = $email;
                 $_SESSION["phone"] = $phone;
                 $_SESSION["address"] = $address;
                 $_SESSION["created_at"] = $created_at;

                 header("location: /index.php");
                 exit;
             }else{
                 $errors = $validationS->getErrors(); 
             }
         }
    ?>
    <form action="" method= "post">
        <div style="display:flex; flex-direction:column; justify-content:center; align-items:center; ">
            <div class="card">
                <h2>Register</h2>
                <div class="form"> 
                    <?php foreach ($fields as $field => $errorCheckers): ?>
                        <div>
                            <label for="<?= $field ?>"><?= ucfirst($field) . ($field === "address" ? "" : "*")?> </label>
                            <input type="<?= $field === 'phone' ? 'tel' : ($field === 'password' ? 'password' : 'text') ?>"
                            name="<?= $field ?>" id="<?= $field ?>"
                            placeholder= "<?= $field === "name" ? "First name  Last name" : ""?>"
                            value="<?= $field === 'password' ? '' : htmlspecialchars($inputs[$field] ?? '')
                             ?>">
                            <span><?= $errors[$field] ?? '' ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class='btn_div'>
                    <button type="submit">Submit</button>
                    <button><a href="/index.php">Cancel</a></button>
                </div>
            </div>
        </div>
    </form>
</section>