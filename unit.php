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
else {
    $user = null;
}

if (isset($user)) {
    ArmyForm::header(true, $user);
    ArmyForm::messageBox($user);
} else {
    ArmyForm::header(false);
    ArmyForm::messageBox();
}

if (isset($_REQUEST['id'])) {
    $unitID = $_REQUEST['id'];
    ArmyForm::displayUnitPage($unitID, $user);
} else {
    echo "<p>You didn't select a unit to review.</p>";
}

require 'footer.html';
