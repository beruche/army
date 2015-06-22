<?php
/**
 * Created by PhpStorm.
 * User: beruc_000
 * Date: 2015-06-08
 * Time: 6:16 PM
 */

require 'rb.php';

R::setup('sqlite:army.db');

$user = R::load('user', 4);
R::trash($user);