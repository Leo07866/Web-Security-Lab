<?php
session_start();

$loginFailed = false;

// Use an include file which does the database connection (it assigns the database connection to the $connection variable)
require("incs/db-connect.inc.php");

if (isset($_COOKIE["rememberMe"])) { // If we have a value in the rememberMe cookie
    $cookieData = json_decode($_COOKIE["rememberMe"], true); // Convert the JSON string in the cookie into an array
    // Check if the cookie has the correct authentication token value
    if ((!empty($cookieData["authenticationToken"])) && ($cookieData["authenticationToken"] == "64jkf0wjdtqbausjsks")) { // If cookie value is correct
        /* Log the user in using the username from the cookie. 
         * We assume as the cookie has our expected authenticationToken value they are the correct user 
         * thus we can log the user in with the username contained in the cookie. This is a flawed approach 
         * as the authenticationToken is the same for every user so if you change the username in the cookie 
         * it will log you into that user account instead. */
        $query = "SELECT id, username, first_name, surname, email_address "
                . "FROM foobar_users "
                . "WHERE username='" . $cookieData["username"] . "' LIMIT 0, 1";
        $result = mysqli_query($connection, $query);
        if ($result) {
            $found = mysqli_num_rows($result);
            if ($found > 0) { // If we have at least 1 row returned we know the user exists
                $_SESSION['login'] = true;

                /* Get result containing the user's account details and put them into a session variable. 
                 * We use this array of user details to detect if a user is logged in. 
                 * The query only returns 1 row (and we only need 1 row as usernames are unique) so we just 
                 * need to use the fetch function once to retreive the results of that 1 row. */
                $_SESSION["userDetails"] = mysqli_fetch_assoc($result);            

                /* Work out where to send the user. If the user tried to access a protected page (i.e. one they 
                * needed to be logged in to access) but weren't logged in they'll be sent here to log in. 
                * Therefore we see if we have a HTTP_REFERER value in the PHP server variables which tells us 
                * the previous page the user went to before being sent to this page so that we can redirect them 
                * back there now they are logged in. If there isn't a HTTP_REFERER value, e.g. they went here 
                * direct, then send them to the homepage. */            
               $referrer = (
                               (!empty($_SERVER["HTTP_REFERER"])) 
                               && (
                                   (substr($_SERVER["HTTP_REFERER"], -9) != "index.php") && 
                                   (substr($_SERVER["HTTP_REFERER"], -10) != "logout.php") && 
                                   ($_SERVER["HTTP_REFERER"] != "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"])
                                   )
                           ) ? $_SERVER["HTTP_REFERER"] : "homepage.php";            
               header("Location: " . $referrer);
            }else {
                $loginFailed = true;
                $loginError = "<p>Logging in via the remember me option failed. It looks like the user specified doesn't exist.</p>";
            }
        } else {
            $loginFailed = true;
            $loginError = "<p>Logging in via the remember me option failed. It looks like the user specified doesn't exist.</p>";
        }
    } else {
        $loginFailed = true;
        $loginError = "<p>Logging in via the remember me option failed. Authentication error/failure.</p>";
    }
}

if (isset($_POST["submitBtn"])) { // If form submitted
    if ((!empty($_POST["username"])) && (!empty($_POST["password"]))) { // Check we have a username and password
        // Store the username and password into variables to make using them easier
        $username = $_POST["username"];
        $password = $_POST["password"];

        // Check if the username and password provided in the form match a user's account (a row in the users 
        // table) and if so we can log them in.
        $query = "SELECT id, username, first_name, surname, email_address "
                . "FROM foobar_users "
               . "WHERE username='" . $username . "' AND password='" . $password . "'";
        $result = mysqli_query($connection, $query);
        if ($result) {
            $found = mysqli_num_rows($result);
            if ($found > 0) { // If we have at least 1 row returned we know the user exists
                $_SESSION['login'] = true;

                /* Get result containing the user's account details and put them into a session variable. 
                 * We use this array of user details to detect if a user is logged in. 
                 * The query only returns 1 row (and we only need 1 row as usernames are unique) so we just 
                 * need to use the fetch function once to retreive the results of that 1 row. */
                $_SESSION["userDetails"] = mysqli_fetch_assoc($result);

                // If they set/checked the checkbox to keep themselves logged in (they wish to use the auto login/remember me function)
                if ((isset($_POST["rememberMe"])) && ($_POST["rememberMe"] == "yes")) { 
                    /* Set up the rememberMe cookie for auto login which includes their username and the 
                     * generic authentication token. The 2 values (username and authenticationToken) are held 
                     * in an array and encoded into a JSON encoded string so they become one string/value and 
                     * thus can be stored as the cookies value (cookies can only store 1 value). */
                    $expiry = (time() + (((60 * 60) * 24) * 30));
                    $cookieData = array("username" => $_SESSION["userDetails"]["username"], "authenticationToken" => "64jkf0wjdtqbausjsks");
                    setcookie("rememberMe", json_encode($cookieData), $expiry);                    
                }

                /* Work out where to send the user. See the usage of this code above for an explanation of this code. */
                $referrer = (
                            (!empty($_SERVER["HTTP_REFERER"])) 
                            && (
                                (substr($_SERVER["HTTP_REFERER"], -9) != "index.php") && 
                                (substr($_SERVER["HTTP_REFERER"], -10) != "logout.php") && 
                                ($_SERVER["HTTP_REFERER"] != "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"])
                                )
                        ) ? $_SERVER["HTTP_REFERER"] : "homepage.php"; 
                header("Location: " . $referrer);
            } else {
                $loginFailed = true;
                $loginError = "<p>Wrong user name and password combination!</p>";
            }
        } else {
            $loginFailed = true;
            $loginError = "<p>Running the query to check the username and password are correct failed.</p>";
        }
    } else {
        $loginFailed = true;
        $loginError = "<p>Username and/or password has not been added to the form.</p>";
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Web Security Testing Lab</title>    
        <meta http-equiv="x-ua-compatible" content="IE=100">
        <meta charset="UTF-8">
        <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
        <link href="default.css" media="all" rel="stylesheet" type="text/css" >
        <link href="general.css" media="all" rel="stylesheet" type="text/css">
        <link href="forms.css" media="all" rel="stylesheet" type="text/css">
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
       
    </head>    
    <body class="no-sidebars" id="top">         
        <header id="mainH">
            <div class="inner-cont">
                <h1>Vulnerable Web Lab</h1>
                <a href="#" title="Home" id="homeLink">                    
                    <span class="hgroup">
                        <h2>Created by</h2>
                        <h3>@Leo07866</h3>
                    </span>
                </a>                      
            </div>
        </header>
        <div id="container">
            <main role="main">
                <h1>Welcome, Login Below</h1>
<?php
if (mysqli_connect_error()) { // If connection error
    echo ("Failed to connect to the database");
} else { // Database connected correctly - Only show page contents if we have a database connection to use   
 
    // If the user was redirected here as they need to login (i.e. they went to a protected page without 
    // being logged in) tell them they need to login to access the page they requested.
    if ((!empty($_SESSION["loginRequired"])) && ($_SESSION["loginRequired"] == true)) {
        echo "<div class='alertBox'>"
        . "<h1>Protected page requested without being logged on.</h1>"
        . "You must log in before you can access the protected parts of the site."
        . "</div>";
        unset($_SESSION["loginRequired"]); // Remove this variable as we are finished with it
    }
    
    // If we have a login error display it
    if (($loginFailed == true) && (!empty($loginError))) {
        echo "<div class='errorBox'>" . $loginError . "</div>";
    }

    if ((!empty($_SESSION['login'])) && ($_SESSION['login'] == true)) { // If user is already logged in
        echo "<p>You are logged in, <a href='homepage.php' title='Homepage'>continue to the homepage</a>.</p>";
    } else { // If user is not logged in
        ?>
        
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <fieldset>
                        <legend>Login</legend>
                        <div>
                            <label for="username">Username:</label>
                            <input type="text" name="username" placeholder="Enter your username">
                        </div>
                        <div>
                            <label for="password">Password:</label>
                            <input type="password" name="password" placeholder="Enter your password">
                        </div>
                        <div>
                            <label for="rememberMe">Remember me:</label>
                            <input type="checkbox" name="rememberMe" value="yes" checked>
                        </div>
                        <span id="submit-btn-cont">
                            <input id="submit-btn" class="submit-btn" type="submit" value="Send" name="submitBtn">
                        </span>
                    </fieldset>
  
                </form> 
        <?php
    }
}
?>
            </main>
        </div>
        <footer id="mainF">
            <div role="contentinfo">
                <p>Copyright Leo07866 2018 - <?php echo Date("Y"); ?> &copy;</p>
             
            </div>
        </footer> 
    </body>
</html>
