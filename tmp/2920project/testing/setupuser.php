<?php
/**
 * Created by PhpStorm.
 * User: beruc_000
 * Date: 2015-06-08
 * Time: 6:16 PM
 */

require 'rb.php';

R::setup('sqlite:2920project.db');

$user = R::dispense('user');
$user->name = 'someone';
$user->password = 'whee';
$user->email = 'someone@woohoo.com';

$id = R::store($user);