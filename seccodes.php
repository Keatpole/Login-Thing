<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <?php
        $_GET["webname"] = basename(__FILE__, '.php');
        include_once 'header.php';

        if (!isset($_SESSION["id"])) {
            header("location: .?error=nologin");
            exit();
        }
        
    ?>
    <h1>Manage Security Codes</h1>

    <h3>Add Security Code</h3>
    <h5>Example - Question: What's your first pet's name? Answer: Blob</h5>
    <form action="includes/account/addseccode" method="post">
        <textarea name="q" placeholder='Question...'></textarea>
        <textarea name="a" placeholder='Answer...'></textarea></br></br>
        <input type="password" name="pwd" placeholder="Your Password...">
        </br><button type="submit" name="submit" class="button">Add</button>
    </form>

</body>
</html>