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

// if not logged in, send to login screen.
if (!ArmyForm::checkForLogIn()) {
    ArmyForm::redirectLogin("error", "You are not logged in.");
}

if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
} else {
    $action = null;
}

$user = $_SESSION['username'];

switch ($action) {


    default:
        ArmyForm::header(true, $user);
        ArmyForm::messageBox($user);
        ArmyForm::displayHomePage($user);
        break;

}








require 'footer.html';