<?php
/**
 * Created by PhpStorm.
 * User: beruc_000
 * Date: 2015-06-17
 * Time: 4:41 PM
 */

require 'rb.php';
require 'ArmyDB.php';

R::setup('sqlite:army.db');
//R::debug(TRUE);

try {
    $projectid = 3;

    ArmyDB::clearProjectUnits($projectid);

    var_dump(ArmyDB::retrieveUnitsFromProject($projectid));


}
catch (Exception $e) {
    echo $e->getMessage();
}


