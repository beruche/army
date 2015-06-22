<?php
/**
 * Created by PhpStorm.
 * User: beruc_000
 * Date: 2015-06-17
 * Time: 12:11 PM
 */

require '.\rb.php';

R::setup();
R::debug( TRUE );

/*
$user = R::dispense('user');

$user->id = 1;
$user->userid = "someotheruser";
$user->fname = "Test";
$user->lname = "Testerson";
$user->password = "test";
$user->email = "test@testing.com";

var_dump($user);

R::begin();
try {
    R::store($user);
    R::commit();
}
catch ( Exception $e) {
    echo "update failed " . $e->getMessage();
    R::rollback();
}

*/

$users = R::getAll (' SELECT * from user');
var_dump($users);