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

    ArmyDB::updateUnit(19,"Chaplain",1,120,131,"Scary skull captain that likes to hit things!");
}
catch (Exception $e) {
    echo "Unable to create project. " . $e->getMessage();
}

