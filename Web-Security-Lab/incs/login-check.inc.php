<?php
// If we don't already have an open session open one
if(empty($_SESSION)){ 
    session_start();     
} 

// If a user isn't logged in
if ((empty($_SESSION["login"])) || ($_SESSION["login"] != true)){
    /* User isn't logged in and needs to be therefore we send them to the index page (where the login form is) 
     * to log on. A session variable is used to record this has happened then the index page can detect this 
     * and display an error message informing the user they must log in (and thus they know why they were 
     * redirected to the index page). */
    $_SESSION["loginRequired"] = true;
    header("Location: index.php");
}
?>