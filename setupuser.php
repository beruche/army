<?php
/**
 * Created by PhpStorm.
 * User: beruc_000
 * Date: 2015-06-08
 * Time: 6:16 PM
 */

require 'rb.php';

R::setup('sqlite:army.db');

$user = R::dispense('user');
$user->username = 'enterstatsman';
$user->password = 'password';
$user->email = 'enterstatsman@gmail.com';
$user->fname = 'Ryan';
$user->lname = 'Allan';
$user->prefcolor = '#CC0000';

$id = R::store($user);