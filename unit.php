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

    echo "<div class='row'>";
    echo "<div class='col-xs-12 col-md-9' id='main'><!--unit information-->";

    if (!ArmyDB::doesUnitExist($unitID)) {
        ArmyForm::redirect("error", "Unit $unitID does not exist.", 'unit');
    }

    ArmyForm::displayUnitInformation($unitID, $user);

    echo "</div><!--end.unit information-->";
    echo "<div class='col-xs-12 col-md-3' id='news'><!--news information-->";
    ArmyForm::displayNews();
    echo "</div><!--end.news information-->";
    echo "</div><!-- displayUnitPage -->";


} else {
    echo "<p>You didn't select a unit to review.</p>";
}


require 'footer.html';
