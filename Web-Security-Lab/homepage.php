<?php
session_start();
$loginFailed = false;

// Use an include file which does the database connection (it assigns the database connection to the $connection variable)
include("incs/db-connect.inc.php");

// Use an include file to check the user is authenticated to use this page (if they aren't it'll send them to the index page to log in)
include("incs/login-check.inc.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Web Security Testing Lab</title>    
        <meta http-equiv="x-ua-compatible" content="IE=100">
        <meta charset="UTF-8">
        <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
        <link href="default.css" media="all" rel="stylesheet" type="text/css" >
        <script src="jquery.min.js"></script>
        <script src="common.js"></script>
        <link href="general.css" media="all" rel="stylesheet" type="text/css">
        <link href="forms.css" media="all" rel="stylesheet" type="text/css">
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        
    </head>    
    <body class="no-sidebars" id="top">         
        <header id="mainH">
            <div class="inner-cont">
                <h1>Message Board</h1>
                <a href="#" title="Home" id="homeLink">                    
                    <span class="hgroup">
                        <h2>Created by</h2>
                        <h3>@Leo07866</h3>
                    </span>
                </a>       
                <a href="logout.php" title="Logout" style="color: #fff;">Logout</a>
            </div>
        </header>
        <div id="container">
            <main role="main">
                <h1>Homepage</h1>
                <?php
                if (mysqli_connect_error()) { // If connection error
                    echo ("Failed to connect to the database");
                } else { // Database connected correctly - Only show page contents if we have a database connection to use  
                    echo "<p>"
                    . "Welcome " . $_SESSION["userDetails"]["username"]
                    . " (" . $_SESSION["userDetails"]["first_name"] . " "
                    . $_SESSION["userDetails"]["surname"] . ")"
                    . "</p>";
                    ?>
                    <h1>Your personal message stream</h1>
                    <p>Add your thoughts to the message board below.</p>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                        <fieldset>
                            <legend>Messages</legend>                   
                            <div>
                                <label for="message">Your Message:</label>
                                <input type="text" name="message" placeholder="Your message goes here">
                            </div>
                            <span id="submit-btn-cont">
                                <input id="submit-btn" class="submit-btn" type="submit" value="Send" name="submitBtn">
                            </span>
                        </fieldset>
                    </form> 
                    <?php
                    /* As this is just a demonstration site and hacking occurs on it messages are stored in 
                     * session variables rather than a database. This is so that messages the user adds are 
                     * only shown to them as we don't want other people who are using the site (i.e other 
                     * students) to be inconvenienced or attacked by code entered by users (ethical hackers 
                     * in this case). It also makes the messages added temporary (they are removed when you 
                     * log out) which is useful for testing purposes. */
                    
                    if (empty($_SESSION["messages"])) { // If we have no messages already
                        // Initalise the messages session array with some initial messages
                        $_SESSION["messages"] = array("First message", "Second message", "Third message");
                        /* Note when you log out the session is destroyed and thus messages which are stored 
                         * in the messages session variable are deleted. Therefore we need a way of adding 
                         * initial messages. This destroying of session variables also means any messages you 
                         * add (and in the worksheet the messages will contain hack code) will be lost when 
                         * you log out, perfect for removing test messages and starting again. */
                    }
                                        
                    if (isset($_POST["submitBtn"])) { // If the form has been submitted
                        if (!empty($_POST["message"])) { // If a message was entered in the form
							//$_POST["message"] = filter_var($_POST["message"], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH); //this sanitaze user input and post massage
                            // Add entered message into the messages session variable (which is an array in this case)
                            $_SESSION["messages"][] = $_POST["message"];
                        } else { // If no message entered in the form display an error message
                            echo "<div class='errorBox'>Add a message - No message was submitted with the form.</div>";
                        }
                    }

                    if (!empty($_SESSION["messages"])) { // If we have messages in our array of messages (held in a session variable)
                        foreach ($_SESSION["messages"] as $message) { // Loop through messages and output them
                            echo "<div class='messageBoardMessage'>" . $message . "</div>";
                        }
                    }
                 
                }
                ?>
            </main>
        </div>
        <footer id="mainF">
            <div role="contentinfo">
                <p> Leo07866 <?php echo Date("Y"); ?> &copy;</p>
              
            </div>
        </footer> 
    </body>
</html>
