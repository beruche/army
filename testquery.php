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
    ArmyDB::addUnit(2, "Eldar Guardians", 10, 120, 0, null);


}
catch (Exception $e) {
    echo $e->getMessage();
}


