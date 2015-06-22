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

if (ArmyForm::checkForLogIn()) {
    ArmyForm::redirectIndex("error", "You have already logged in!");
}

if ((isset($_REQUEST['action'])) && (!empty($_REQUEST['action']))) {
    $action = $_REQUEST['action'];
} else {
    $action = null;
}

switch ($action) {
    case "login":
        $tmpPwd = strip_tags(trim($_REQUEST['pwd']));
        $tmpUsr = strip_tags(trim($_REQUEST['usr']));

        try {
            ArmyForm::verifyUser($tmpUsr, $tmpPwd);
            $_SESSION['username'] = $tmpUsr;
            ArmyForm::redirectIndex('success', 'You have logged in, ' . $tmpUsr . '!');
        } catch (IncorrectLoginException $e) {
            ArmyForm::redirectLogin('error', $e->getMessage());
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

        try {
            ArmyForm::checkForUserName($tmpUsr);
            setcookie("tmpUsr", $tmpUsr, time() + (86400 * 30), "/");
            setcookie("tmpPwd", $tmpPwd, time() + (86400 * 30), "/");
            setcookie("tmpEmail", $tmpEmail, time() + (86400 * 30), "/");
            ArmyForm::redirectCreate();
        } catch (UserAlreadyExistsException $e) {
            LogInForm::redirectLogin('error', $e->getMessage());
        }
        break;
    default:
        ArmyForm::header(false);
        ArmyForm::messageBox();
        ArmyForm::displayLogInUser();
        ArmyForm::displayCreateUser();
        break;
}



require 'footer.html';