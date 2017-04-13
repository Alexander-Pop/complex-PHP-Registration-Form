<?php
//Define custom variable to access protected files
define('LOCK', 0);

// Import classes
require $_SERVER['DOCUMENT_ROOT'] . '/class/class.database.php';

// Import anti-spam security
require $_SERVER['DOCUMENT_ROOT'] . '/security/index.php';

// Set up classes
$db = new Database();
$key = new Key();

// Initialize Sessions
session_start();

// Handle form data
$imgb64 = $key->genImage();

if(isset($_POST["submit"])) {
    // Insert user inputs into a single array to make things easier on the coder
    $formdata = array(
        htmlspecialchars($_POST["username"]),
        htmlspecialchars($_POST["password"]),
        htmlspecialchars($_POST["confirmation"]),
        htmlspecialchars($_POST["securitycode"])
    );
    
    // Check user inputs to ensure they are legitimate, secure, and can fit in the database
    if((strlen($formdata[0]) < 3) || (strlen($formdata[0]) > 40)) {
        echo 'Username not between 3 and 40 characters!';
    } elseif(strlen($formdata[1]) < 8) {
        echo 'Password needs to be above 8 characters in length!';
    } elseif($formdata[1] !== $formdata[2]) {
        echo 'The verification password does not match the previously given password!';
    } elseif(!filter_input(INPUT_POST,"email",FILTER_VALIDATE_EMAIL)) {
        echo 'The email address is invalid!';
    } elseif(sha1($formdata[3]) !== $_POST["key"]) {
        echo 'The security key is incorrect!';
    } else {
        // Time to generate a random session value to authenticate the user
        $sessiondata = hash('sha256', openssl_random_pseudo_bytes(128));
        
        // Hash the password using bcrypt which includes salting functionality
        $passhash = password_hash($formdata[1], PASSWORD_BCRYPT, ['cost' => 13]);
        
        // Generate an SQL query to send data to the database with prepared statements
        $query = "INSERT INTO accounts (username,passhash,email,session) VALUES (?,?,?,?)";
        
        // Send the query with appropriate data
        $db->sendquery($db->connect(), $query, array($formdata[0],$passhash,htmlspecialchars($_POST["email"]),$sessiondata));
        
        // Start a server-side session equal to the sessiondata that was added to the database
        $_SESSION['current'] = $sessiondata;
    }
}
?>
<!-- A simple HTML form -->
<form method="post">
    <input type="hidden" name="key" value="<?= sha1($imgb64[1]) ?>" />
    <table border="0" cellspacing="5" cellpadding="0" style="margin:0 auto;">
        <tr><td>Username:</td><td><input type="text" name="username" /></td></tr>
        <tr><td>Password:</td><td><input type="password" name="password" /></td></tr>
        <tr><td>Verify Password:</td><td><input type="password" name="confirmation" /></td></tr>
        <tr><td>Email Address:</td><td><input type="text" name="email" /></td></tr>
        <tr><td>Security Key: <?= '<iframe src="'.$imgb64[0].'" style="width:100px;height:20px;border:0;margin-bottom:-5px;"></iframe>'; ?></td><td><input type="text" name="securitycode" /></td></tr>
        <tr><td colspan="2"><input type="submit" name="submit" style="width:100%;" value="Register" /></td></tr>
    </table>
</form>