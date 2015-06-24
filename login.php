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
    ArmyForm::redirect("error", "You have already logged in!", "index");
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
            ArmyForm::redirect('success', 'You have logged in, ' . $tmpUsr . '!', "user");
        } catch (IncorrectLoginException $e) {
            ArmyForm::redirect('error', $e->getMessage(), "login");
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
            ArmyForm::redirect('','','create');
        } catch (UserAlreadyExistsException $e) {
            LogInForm::redirect('error', $e->getMessage(), 'create');
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