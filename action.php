<?php
/**
 * Most database changes are processed through action.php, passed along as part of a form.
 * User: Ryan Allan
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
        /**
         * Creates a unit in the database along with posting a news article. If an exception comes up,
         * cancel the process and go back to the project page stating that the action could not be completed.
         */
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
        /**
         * Deletes a unit based on the unit id. If an exception occurs, redirect to the project
         * page the site came from stating the action cannot be completed.
         */
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
        /**
         * Retrieves a unit from the database based on unit id, and updates the respective fields.
         * If an exception occurs, return to unit page stating why.
         */
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
        /**
         * Creates a project in the database with the information provided in the form.
         * If an exception occurs, return to user page with error message.
         */
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
        /**
         * Deletes a project based on project id. If an error occurs, return to user page with error message.
         */
        $projectid = $_REQUEST['id'];

        try {
            ArmyDB::deleteAllUnits($projectid);
            ArmyDB::deleteProject($projectid);
            ArmyDB::addNewsItem($user, "deleteProject", $projectid);
            ArmyForm::redirect('success', "Project ID $projectid has been deleted!", 'user');
        }
        catch (Exception $e) {
            ArmyForm::redirect('error', "Unable to delete project. " . $e->getMessage(), 'user');
        }

        break;

    case "editProject":
        /**
         * Retrieves a project based on project id, and updates it with information provided by form, and posts a news article.
         * If exception occurs, return to user page with error message.
         */
        $projectid = $_REQUEST['id'];
        $projectname = $_REQUEST['projectname'];
        $battlegroup = $_REQUEST['btlgrp'];
        $gamegroup = $_REQUEST['gamegrp'];
        $desc = $_REQUEST['desc'];

        try {
            ArmyDB::updateProject($projectid, $projectname, $battlegroup, $gamegroup, $desc);
            ArmyDB::addNewsItem($user, "updateProject", $projectid);
            ArmyForm::redirect('success', "Project ID $projectid has been updated!", 'user');
        }
        catch (Exception $e) {
            ArmyForm::redirect('error', "Unable to delete project. " . $e->getMessage(), 'user');
        }

        break;



    /**
     * User Selections
     */
    case "createUser":
        /**
         * Creates a user in the database and posts a news article.
         * If an error occurs, return to index with error message.
         */
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

    case "addNote":
        /**
         * Adds a note to a specific user ID or project ID, and posts a news article.
         * If an error occurs, return to either the unit or project page with an error message.
         */
        $poster = $_REQUEST['poster'];
        $notetext = $_REQUEST['notetext'];
        //var_dump($_REQUEST);
        $projectid = $_REQUEST['projectid'];
        if(isset($_REQUEST['unitid'])) {
            $unitid = $_REQUEST['unitid'];
        }
        else {
            $unitid = null;
        }

        try {
            ArmyDB::addNote($poster, $unitid, $projectid, $notetext);
            ArmyDB::addNewsItem($poster, "addNote", $projectid, $unitid);
            if (!is_null($unitid)) {
                ArmyForm::redirect('success', "Note posted!", 'unit', $unitid);
            }
            else {
                ArmyForm::redirect('success', "Note posted!", 'project', $projectid);
            }
        }
        catch (Exception $e) {
            ArmyForm::redirect('error', "Unable to add note. " . $e->getMessage(), 'user');
        }

        break;

    default:
        echo "What are you trying to do? $action";
        break;

}