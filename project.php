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

if(isset($_REQUEST['id'])) {
    if (!isset($user)) {
        $user = null;
    }
    $projectID = $_REQUEST['id'];

    echo "<div class='row'>";
    echo "<div class='col-xs-12 col-md-9' id='main'>";
    ArmyForm::displayUnits($projectID, $user);
    echo "</div>";
    echo "<div class='col-xs-12 col-md-3' id='news'>";
    ArmyForm::displayNews();
    echo "</div>";
    echo "</div>";


}
else {
    echo "<p>You didn't select a project to review.</p>";
}

require 'footer.html';



