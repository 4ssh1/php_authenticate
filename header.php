<?php
    session_start();
    $authenticated = false;

    if(isset($_SESSION["email"])){
        $authenticated = true;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
    <script type="text/javascript">
        function toggleMenu() {
            const dropMenu = document.getElementById("dropMenu");
            dropMenu.classList.toggle("show")
        }
        document.addEventListener("click",(e)=>{
            const dropMenu = document.getElementById("dropMenu");
            const dropMenuParentButton = document.querySelector(".dropbtn");
            
            if(!dropMenu.contains(e.target) && e.target !== dropMenuParentButton){
                dropMenu.classList.remove("show")
            }

        })
    </script>
</head>
<body>
        <section>
            <nav>
                <div class="flex">
                    <div>
                        <h2>AFK <span style="font-size: 15px; padding-left:40px;"><a href="/authentication">HOME</a></span></h2>
                    </div>
                    <?php
                        if($authenticated){
                    ?>
                    <div class= "dropdown">
                        <button onclick="toggleMenu()" class="dropbtn">Admin </button>
                        <div id="dropMenu" class="dropMenu">
                            <a href="profile">Profile</a>
                            <a href="logout">Logout</a>
                        </div>
                    </div>
                    <?php
                    }else{
                    ?>
                    <div class="flex">
                        <Button class="register"><a href="register">Register</a></Button>
                        <Button class="login"><a href="login">Login</a></Button>
                    </div>
                    <?php
                    }
                    ?>
                </div>
            </nav>
        </section>