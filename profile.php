<?php
include  "header.php";

if(!isset($_SESSION["email"])){
    header("location: /login.php");
    exit;
}
?>

<style>
    .mainCard{
       margin: 2px auto;
       width: 50%;
       border: 1px solid white;
       box-shadow: 0 2px 6px #356;
       padding: 7px;
       border-radius: 10px;
       background: white;
    }
    .mainCard div{
        padding: 2.5px 0;
        display: flex;
        justify-content: space-between;
    }
</style>

<div style="padding: 2px, 10px;">
    <div>
        <div>
            <h2>Profile</h2>
            <hr>
            <div class= "mainCard" >
                <div>
                    <div>First Name: </div>
                    <div> <?= $_SESSION["first_name"]?></div>
                </div>
                <div>
                    <div>Last Name: </div>
                    <div> <?= $_SESSION["last_name"]?></div>
                </div>
                <div>
                    <div>Email:</div>
                    <div> <?= $_SESSION["email"]?></div>
                </div>
                <div>
                    <div>Phone:</div>
                    <div> <?= $_SESSION["phone"]?></div>
                </div>
                <div>
                    <div>Address:</div>
                    <div> <?= $_SESSION["address"]?></div>
                </div>
                <div>
                    <div>Created at:</div>
                    <div> <?= $_SESSION["created_at"]?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include "footer.php"
?>