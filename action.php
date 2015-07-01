<?php
/**
 * Created by PhpStorm.
 * User: beruc_000
 * Date: 2015-06-30
 * Time: 5:12 PM
 */

session_start();

require_once 'rb.php';
require_once 'ArmyForm.php';
require_once 'ArmyDB.php';


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
    /**
     * Unit Selections
     */
    case "createUnit":
        $unitname = $_REQUEST['unitname'];
        $qty = $_REQUEST['qty'];
        $pts = $_REQUEST['pts'];
        $projectid = $_REQUEST['projectid'];

        try {
            $newunitid = ArmyDB::addUnit($projectid, $unitname, $qty, $pts, 0, '');
            ArmyDB::addNewsItem($user, "addUnit", $projectid, $newunitid);
            ArmyForm::redirect('success', "$unitname has been added.", 'project', $projectid);
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
        break;
    case "editUnit":

        //var_dump($_REQUEST);

        $unitid = $_REQUEST['unitid'];
        $unitname = $_REQUEST['unitname'];
        $unitqty = $_REQUEST['qty'];
        $unitpts = $_REQUEST['pts'];
        $paintStatus = $_REQUEST['paintStatus'];
        $assembleStatus = $_REQUEST['assembleStatus'];
        $baseStatus = $_REQUEST['baseStatus'];
        $unitnotes = $_REQUEST['unitnotes'];

        $status = $assembleStatus . $baseStatus . $paintStatus;

        try {
            ArmyDB::updateUnit($unitid, $unitname, $unitqty, $unitpts, $status, $unitnotes);
            ArmyDB::addNewsItem($user, "editUnit", null, $unitid);
            ArmyForm::redirect('success', "Unit $unitid has been updated.", 'unit', $unitid);

        }
        catch (Exception $e) {
            ArmyForm::redirect('error', "Unable to add unit. " . $e->getMessage(), 'unit', $unitid);
        }

        break;


    /**
     * Project Selections
     */
    case "createProject":

        //var_dump($_REQUEST);

        $submitter = $_REQUEST['submitter'];
        $projectname = $_REQUEST['projname'];
        $battlegroup = $_REQUEST['btlgrp'];
        $gamegroup = "";
        $description = "";


        if ($submitter != $user) {
            ArmyForm::redirect('error', "Submitter doesn't match the user page! Security problem!", 'user');
        }

        try {
            $newprojectid = ArmyDB::addProject($user, $projectname, $battlegroup, $gamegroup, $description);
            ArmyDB::addNewsItem($user, "addProject", $newprojectid);
            ArmyForm::redirect('success', "Project $newprojectid has been added!", 'project', $newprojectid);
        }
        catch (Exception $e) {
            //echo $user . "//" . $projectname . "//" . $battlegroup . "//" . $gamegroup . "//" . $description;
            ArmyForm::redirect('error', "Unable to add project. " . $e->getMessage(), 'user');
        }

        break;

    case "deleteProject":
        $projectid = $_REQUEST['id'];

        try {
            ArmyDB::deleteProject($projectid);
            ArmyDB::addNewsItem($user, "deleteProject", $projectid);
            ArmyForm::redirect('success', "Project ID $projectid has been deleted!", 'user');
        }
        catch (Exception $e) {
            ArmyForm::redirect('error', "Unable to delete project. " . $e->getMessage(), 'user');
        }

        break;



    /**
     * User Selections
     */
    case "createUser":
        echo "yay!";

        $user = $_REQUEST['tmpUsr'];
        $pwd = $_REQUEST['tmpPwd'];
        $email = $_REQUEST['tmpEmail'];
        $fname = $_REQUEST['fname'];
        $lname = $_REQUEST['lname'];

        try {
            ArmyDB::addUser($user, $pwd, $email, $fname, $lname);
            ArmyDB::addNewsItem($user, "addUser", null);
            $_SESSION['username'] = $user;
            ArmyForm::redirect('success', 'User ' . $tmpUsr . 'created! Welcome to Army Builder!', 'user');
        }
        catch (Exception $e) {
            ArmyForm::redirect('error', "Unable to create user. " . $e->getMessage(), 'index');
        }
        break;

    default:
        echo "What are you trying to do? $action";
        break;

}