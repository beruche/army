<?php
/**
 * The landing page for the project. Displays the log in information, and a listing of all users, projects and units.
 * Created by PhpStorm.
 * User: Ryan Allan
 * Date: 2015-06-08
 * Time: 4:44 PM
 */

ob_start();
session_start();

require 'rb.php';
require 'ArmyForm.php';

R::setup('sqlite:army.db');

if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
} else {
    $action = null;
}

if (isset($_SESSION['username'])) {
    $user = $_SESSION['username'];
}

if (isset($user)) {
    ArmyForm::header(true, $user);
    ArmyForm::messageBox($user);
}
else {
    ArmyForm::header(false);
    ArmyForm::messageBox();
}

echo "<div class='jumbotron'>";
echo "<h1>Muster Station</h1>";
echo "<p>Need help getting your miniatures army ready for the table?</p>";
echo "</div>";
echo "<div class='row'>";
echo "<div class='col-xs-12' id='display'>";
echo "<div class='col-xs-12 col-md-4' id='users'>";
echo "<h1>Users</h1>";
try {
    $users = ArmyDB::retrieveallUsers();
    echo "<ul>";
    foreach ($users as $user) {
        echo "<li><a href='user.php?username=" . $user['username'] . "'>" . $user['username'] .  "</a></li>";
    }
    echo "</ul>";
}
catch (Exception $e) {
    echo "<p>No users available</p>";
}

echo "</div><!-- Users -->";
echo "<div class='col-xs-12 col-md-4' id='projects'>";
echo "<h1>Projects</h1>";
try {
    $projects = ArmyDB::retrieveAllProjects();
    echo "<ul>";
    foreach ($projects as $project) {
        echo "<li><a href='project.php?id=" . $project['id'] . "'>" . $project['projectname'] .  "</a></li>";
    }
    echo "</ul>";
}
catch (Exception $e) {
    echo "<p>No projects available</p>";
}
echo "</div><!-- Projects -->";
echo "<div class='col-xs-12 col-md-4' id='units'>";
echo "<h1>Units</h1>";
try {
    $units = ArmyDB::retrieveAllUnits();
    echo "<ul>";
    foreach ($units as $unit) {
        echo "<li><a href='unit.php?id=" . $unit['id'] . "'>" . $unit['name'] .  "</a></li>";
    }
    echo "</ul>";
}
catch (Exception $e) {
    echo "<p>No units available</p>";
}
echo "</div><!-- Row -->";
echo "</div><!-- Units -->";

echo "</div><!-- display -->";


require 'footer.html';