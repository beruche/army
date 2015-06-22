<?php
/**
 * Created by PhpStorm.
 * User: beruc_000
 * Date: 2015-06-17
 * Time: 4:34 PM
 */

require 'rb.php';

R::setup('sqlite:army.db');

$user = "enterstatsman";


$project = R::dispense('project');
$project->username = $user;
$project->projectname = 'The Wrath';
$project->battlegroup = 'Daemonkin: Khorne';
$project->gamegroup = 'Warhammer 40K';
$project->description = 'RAAAAAR';
$project->date_added = R::isoDateTime();

$projectid = R::store($project);