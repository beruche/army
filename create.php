<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 6/21/2015
 * Time: 8:10 PM
 */

require 'rb.php';
require 'ArmyForm.php';

R::setup('sqlite:army.db');

if (isset($_REQUEST['action']) && ($_REQUEST['action'] == "create"))
{
    require 'ArmyDB.php';

    $user = $_REQUEST['tmpUsr'];
    $pwd = $_REQUEST['tmpPwd'];
    $email = $_REQUEST['tmpEmail'];
    $fname = $_REQUEST['fname'];
    $lname = $_REQUEST['lname'];
    $cpref = $_REQUEST['cpref'];

    try {
        ArmyDB::addUser($user, $pwd, $email, $fname, $lname, $cpref);
        ArmyDB::addNewsItem($user, "addUser", null);
        $_SESSION['username'] = $tmpUsr;
        ArmyForm::redirect('success', 'User ' . $tmpUsr . 'created! Welcome to Army Builder!', 'user');
    }
    catch (Exception $e) {
        ArmyForm::redirect('error', "Unable to create user. " . $e->getMessage(), 'create');
    }

}
else {
    if (!isset($_COOKIE["tmpUsr"])) {
        ArmyForm::redirectLogin("error", "To create an account, please use this form.");
    }
    else {
        $tmpUsr = $_COOKIE["tmpUsr"];
        $tmpPwd = $_COOKIE["tmpPwd"];
        $tmpEmail = $_COOKIE["tmpEmail"];
        setcookie("tmpUsr", "", time() - 3600);
        setcookie("tmpPwd", "", time() - 3600);
        setcookie("tmpEmail", "", time() - 3600);

        ArmyForm::header(false);

        echo "<div class='alert alert-info' role='alert'>";
        echo "To complete registration, please fill out these additional fields, $tmpUsr!";
        echo "</div>";

        echo "<div class='col-xs-6'>";
        echo "<form role='form' method='post' action='create.php'>";

        echo "<label for='fname'>First Name: </label>";
        echo "<input type='text' class='form-control' id='fname' name='fname' required>";
        echo "<label for='lname'>Last Name: </label>";
        echo "<input type='text' class='form-control' id='lname' name='lname' required>";
        echo "<label for='cpref'>Color Preference: </label>";
        echo "<input type='text' class='form-control' id='cpref' name='cpref' required>";
        echo "<input type='hidden' name='tmpUsr' value='" . $tmpUsr . "'>";
        echo "<input type='hidden' name='tmpPwd' value='" . $tmpPwd . "'>";
        echo "<input type='hidden' name='tmpEmail' value='" . $tmpEmail . "'>";
        echo "<input type='hidden' name='action' value='create'><input type='submit' class='btn btn-default'>";
        echo "</form></div>";

        require 'footer.html';
    }
}



