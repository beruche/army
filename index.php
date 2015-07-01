<?php
/**
 * Created by PhpStorm.
 * User: Ryan Allan
 * Date: 2015-06-08
 * Time: 4:44 PM
 */

ob_start();
session_start();

require 'rb.php';
require 'ArmyForm.php';

R::setup('sqlite:army.db');

if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
} else {
    $action = null;
}

if (isset($_SESSION['username'])) {
    $user = $_SESSION['username'];
}

if (isset($user)) {
    ArmyForm::header(true, $user);
    ArmyForm::messageBox($user);
}
else {
    ArmyForm::header(false);
    ArmyForm::messageBox();
}

echo "<div class='jumbotron'>";
echo "<h1>Muster Station</h1>";
echo "<p>Need help getting your miniatures army ready for the table?</p>";
echo "</div>";
echo "<p>This is some text.</p>";
echo "<p>This is another text.</p>";


require 'footer.html';