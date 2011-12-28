<?php

   require_once('client.php');

   $errorMessage = "";

   $username = $_POST['username'];
   $password = $_POST['password'];

   if ($username != '')
   {
        $result = ws_checkUsername($username, $password);
        if (get_class($result) == 'SOAP_Fault')
        {
            $errorMessage = $result->getFault()->faultstring;
        }
        else
        {
            session_start();
                session_unset();
            session_register("username");
            session_register("password");
            $_SESSION["username"]=$username;
            $_SESSION["password"]=$password;
            header("location: listReports.php");
                exit();
        }
   }


?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>JSP Page</title>
    </head>
    <body>

    <h1>Welcome to the JasperServer sample (PHP version)</h1>

   <h2><font color="red"><?php echo $errorMessage; ?></font></h2>

   <form action="index.php" method=POST>

       Type in a JasperServer username and password (i.e. jasperadmin/jasperadmin)<br><br>

       Username <input type="text" name="username"><br>
       Password <input type="password" name="password"><br>

       <br>
       <input type="submit" value="Enter">

   </form>



    </body>
</html>
