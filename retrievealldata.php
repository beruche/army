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
R::debug(TRUE);

$projects = ArmyDB::retrieveAllProjects();
echo "<h1>Projects</h1>";
var_dump($projects);

$units = ArmyDB::retrieveAllUnits();
echo "<h1>Units</h1>";
var_dump($units);

$users = ArmyDB::retrieveAllUsers();
echo "<h1>Users</h1>";
var_dump($users);

$news = ArmyDB::retrieveAllNews();
echo "<h1>News</h1>";
var_dump($news);

$notes = ArmyDB::retrieveAllNotes();
echo "<h1>Notes</h1>";
var_dump($notes);