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
switch ($action) {
    case "createUnit":
        $unitname = $_REQUEST['unitname'];
        $qty = $_REQUEST['qty'];
        $pts = $_REQUEST['pts'];
        $projectid = $_REQUEST['projectid'];

        try {
            $newunitid = ArmyDB::addUnit($projectid, $unitname, $qty, $pts, 0, '');
            ArmyDB::addNewsItem($user, "addUnit", $projectid, $newunitid);
            ArmyForm::redirect('success', "$unitname has been added.", 'unit', $newunitid);
        }
        catch (Exception $e) {
            ArmyForm::redirect('error', "Unable to add unit. " . $e->getMessage(), 'project', $projectid);
        }

        break;
    case "deleteUnit":
        $unitID = $_REQUEST['id'];
        $projectID = $_REQUEST['projectid'];

        try {
            ArmyDB::deleteUnit($unitID);
            ArmyDB::addNewsItem($user, "deleteUnit", $projectID, $unitID);
            ArmyForm::redirect('success', "Unit $unitID has been deleted.", 'project', $projectID);
        }
        catch (Exception $e) {
            ArmyForm::redirect('error', "Unable to add unit. " . $e->getMessage(), 'project', $projectID);
        }


    default:
        if (isset($user)) {
            ArmyForm::header(true, $user);
            ArmyForm::messageBox($user);
        } else {
            ArmyForm::header(false);
            ArmyForm::messageBox();
        }

        if (isset($_REQUEST['id'])) {
            $unitID = $_REQUEST['id'];
            //ArmyForm::displayProjectPage($username);
        } else {
            echo "<p>You didn't select a unit to review.</p>";
        }

        require 'footer.html';
        break;
}
