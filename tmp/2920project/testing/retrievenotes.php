<?php
/**
 * Created by PhpStorm.
 * User: beruc_000
 * Date: 2015-06-08
 * Time: 6:19 PM
 */

require 'rb.php';
require 'LogInForm.php';

R::setup('sqlite:2920project.db');

LogInForm::dumpNotes();