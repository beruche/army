<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 6/21/2015
 * Time: 8:19 PM
 */

ob_start();
session_start();

require 'ArmyForm.php';

unset ($_SESSION["username"]);
$_SESSION["username"] = array();
session_destroy();
ArmyForm::redirectIndex('success', "You have logged out!");