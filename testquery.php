<?php
/**
 * Created by PhpStorm.
 * User: beruc_000
 * Date: 2015-06-17
 * Time: 4:41 PM
 */

require 'rb.php';
require 'ArmyForm.php';

R::setup('sqlite:army.db');
//R::debug(TRUE);


ArmyForm::displayNotes(4);
