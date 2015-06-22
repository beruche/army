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
require 'LogInForm.php';
require 'Exceptions.php';
require 'NoteCenter.php';

R::setup('sqlite:2920project.db');

if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
} else {
    $action = null;
}

if (!LogInForm::checkForLogIn()) {
    switch ($action) {
        case "login":
            $tmpPwd = strip_tags(trim($_REQUEST['pwd']));
            $tmpUsr = strip_tags(trim($_REQUEST['usr']));

            // echo "log in using these parameters:<br>";
            // echo "Username: " . $tmpUsr . " | Password: " . $tmpPwd . "<br><br>";

            try {
                LogInForm::verifyUser($tmpUsr, $tmpPwd);
                $_SESSION['username'] = $tmpUsr;
                LogInForm::redirectIndex('success', 'You have logged in, ' . $tmpUsr . '!');
            } catch (IncorrectLoginException $e) {
                LogInForm::redirectIndex('error', $e->getMessage());
            }
            break;

        case "create":
            $tmpPwd = strip_tags(trim($_REQUEST['pwd']));
            $tmpUsr = strip_tags(trim($_REQUEST['usr']));

            if (isset($_REQUEST['email'])) {
                $tmpEmail = strip_tags(trim($_REQUEST['email']));
            } else {
                $tmpEmail = null;
            }

            //echo "create an account using these parameters:<br>";
            //echo "Username: " . $tmpUsr . " | Password: " . $tmpPwd . "Email :" . $tmpEmail . "<br><br>";

            try {
                LogInForm::createUser($tmpUsr, $tmpPwd, $tmpEmail);
                LogInForm::redirectIndex('success', "Created the following user: " . $tmpUsr . " / " . $tmpPwd);
            } catch (UserAlreadyExistsException $e) {
                LogInForm::redirectIndex('error', $e->getMessage());
            }

            break;

        default:
            NoteCenter::header(false);
            NoteCenter::welcomeUser(null);
            LogInForm::displayLogInUser();
            LogInForm::displayCreateUser();
            break;
    }
} else {
    switch ($action) {
        case "addNote":
            $userNote = $_REQUEST['usernote'];
            $note = $_REQUEST['note'];

            try {
                $noteID = NoteCenter::addNote($userNote, $note);
                LogInForm::redirectIndex('success', 'Note ' . $noteID . ' has been added!');
            } catch (NoteNotCreatedException $e) {
                LogInForm::redirectIndex('error', $e->getMessage());
            }
            break;
        case "logout":
            unset ($_SESSION["username"]);
            $_SESSION["username"] = array();
            session_destroy();
            LogInForm::redirectIndex('success', "You have logged out!");
            break;
        case "edit":
            if (!isset($_REQUEST['noteToEdit'])) {
                LogInForm::redirectIndex('error', 'No note to edit!');
            } else {
                try {
                    $noteToEdit = $_REQUEST['noteToEdit'];
                    $user = $_SESSION["username"];

                    NoteCenter::header(true, $user);
                    NoteCenter::editNote($user, $noteToEdit);
                } catch (NoteNotCreatedException $e) {
                    LogInForm::redirectIndex('error', $e->getMessage());
                }
            }
            break;
        case "updateNote":
            try {
                $noteid = $_REQUEST['noteToEdit'];
                $notetext = $_REQUEST['note'];
                NoteCenter::updateNote($noteid, $notetext);
                LogInForm::redirectIndex('success', 'Note ' . $noteid . ' has been edited!');
            } catch (NoteNotCreatedException $e) {
                LogInForm::redirectIndex('error', $e->getMessage());
            }
            break;
        default:
            $user = $_SESSION['username'];

            NoteCenter::header(true, $user);

            NoteCenter::welcomeUser($user);
            NoteCenter::displayCreateNote($user);
            NoteCenter::viewNotes($user);

            break;
    }
}

require 'footer.html';
