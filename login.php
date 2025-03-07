<?php include "header.php"?>
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
            font-size: 12px;
        }
        .btn_div{
            display: flex;
            padding-top: 30px;
            gap: 20px;
        }
        .btn_div buttton{
            border: 1px solid rgba(33, 32, 32, 0.4);
            padding: 5px 10px;
            cursor: pointer;
            background-color: rgb(170, 236, 71);
            border-radius: 5px;
        }
        .btn_div buttton:hover{
            background-color: transparent;
        }
 
        @media (max-width:600px) {
            .card{
                width:100%;
                padding: 5px 0;
            }
            .form{
                padding: 15px;
                margin: 0 5px;
            }
            .btn_div{
                padding-top: 10px;
                gap: 50px;
            }
            .form div{
             padding: 5px 0px;
            }
        }

    </style>
    <?php
        if (isset($_SESSION["email"])){
            header("location: /index.php");
            exit;
        }

        $email = ""; 

        $fields = [
            "email" => ["validateEmail", "validate"],
            "password" => ["validatePassword", "validate"]
        ];

        $inputs = [];

        require_once "db.php";
        $conn = getDatabaseConnection();

        $validate = new FormValidator($conn);
           
        class FormValidator{
            private $errors = [];
            private $dbconnection;

            function __construct($dbconnection){
                $this->dbconnection = $dbconnection;
            }

            function validate($field, $value){
                if(empty(trim($value))){
                    return $this->errors[$field] = ucfirst($field) . " is required";
                }
            }

            function validateEmail($field, $value) {
                if(!filter_var($value, FILTER_VALIDATE_EMAIL)){
                    $this->errors[$field] = "Enter a valid email format";
                }
            }

            function validatePassword($field, $value) {
                if(!preg_match("/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/" , $value)){
                    $this->errors[$field] = "Email or password is invalid";
                }
           }
    

            function getErrors(){
                return $this->errors;
            }

            function isValid(){
                return empty($this->errors); //return true if empty
            }
            
        }

        
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            foreach ($fields as $field => $values) {
                $inputs[$field] = $_POST[$field] ?? '';
                foreach ($values as $value) {
                    $validate->$value($field, $inputs[$field]);
                }
            }
            if($validate->isValid()){
                $stmt = $conn->prepare("SELECT id, first_name, last_name, phone, password, created_at FROM users WHERE email = ?");
                $email = $inputs["email"];
                $password= $inputs["password"];
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->bind_result($id, $first_name, $last_name, $phone, $stored_password, $created_at);

                if($stmt->fetch()){
                    // var_dump($stored_password, password_verify($password, $stored_password));
                    // var_dump($password, $stored_password);
                    if(password_verify($password, $stored_password)){
                        session_regenerate_id(true);
                        $_SESSION["email"] = $email;
                        $_SESSION["id"] = $id;
                        $_SESSION["first_name"] = $first_name;
                        $_SESSION["last_name"] = $last_name;
                        $_SESSION["phone"] = $phone;
                        $_SESSION["created_at"] = $created_at;

                        // var_dump($_SESSION); 
                        // exit();

                        header("location: /index.php");
                        exit();
                    }else{
                        echo "Invalid";
                    }
                }

                // $name = $first_name . " " . $last_name; // Combine first & last name
               
            }else{
                $error = $validate->getErrors();
            }
        }
    ?>
    <form action="" method= "post">
        <div style="display:flex; flex-direction:column; justify-content:center; align-items:center; ">
            <div class="card">
                <h2>Login</h2>
                <div class="form">
                    <?php foreach ($fields as $key => $value): ?>
                    <div>
                        <label for="<?=$key?>"> <?= ucfirst($key)?></label>
                        <input type="<?= $key === "email" ? "email" : "password" ?>"
                               name="<?=$key?>"
                               value="<?=htmlspecialchars($inputs[$key] ?? "") ?>"
                               id = "<?= $key?>" >
                        <span><?= $error[$key] ?? ""?></span>
                    </div>
                    <?php endforeach;?>
                </div>
                <div class='btn_div'>
                    <button type="submit">Login</button>
                    <button><a href="/index.php">Cancel</a></button>
                </div>
            </div>
        </div>
    </form>
</section>


