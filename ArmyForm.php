<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 6/21/2015
 * Time: 6:58 PM
 */

require 'ArmyDB.php';


class ArmyForm {


    static function messageBox($user = null)
    {
        if (!isset($_REQUEST['action'])) {
            $action = null;
        }
        else {
            $action = $_REQUEST['action'];
        }

        if ((!isset($_REQUEST['msg']))) {
            if ($user == null) {
                echo "<div class='alert alert-info' role='alert'>Welcome! Please log in or create a new account to continue.</div>";
            }
        } else {
            switch ($action) {
                case "error":
                    echo '<div class="alert alert-danger" role="alert">' . $_REQUEST['msg'] . '</div>';
                    break;
                case "success":
                    echo '<div class="alert alert-success" role="alert">' . $_REQUEST['msg'] . '</div>';
                    break;
                default:
                    break;
            }
        }
    }

    static function header($isLoggedIn, $user = null)
    {
        require('header.html');
        echo '<nav class="navbar navbar-inverse">';
        echo '<div class="container-fluid">';
        echo '<div class="navbar-header">';
        echo '<a class="navbar-brand" href="index.php">MUSTER STATION</a>';
        echo '</div><div>';

        if ($isLoggedIn) {
            echo "<div class='collapse navbar-collapse' id='bs-example-navbar-collapse-1'>";
            echo "<ul class='nav navbar-nav'>";
            echo '<li><a href="user.php?username=' . $user . '"><span class="glyphicon glyphicon-user"></span> Signed in as ' . $user . '</a></li>';

            // Projects dropdown
            echo "<li class='dropdown'>";
            echo "<a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>Projects <span class='caret'></span></a>";
            echo "<ul class='dropdown-menu'>";

            try {
                $projects = ArmyDB::retrieveProjectsFromUser($user);
                foreach ($projects as $project) {
                    $title = $project->projectname;
                    $projectid = $project->id;
                    echo "<li><a href='project.php?id=" . $projectid . "'>$title</a></li>";
                }
            }
            catch (Exception $e) {
                echo "<li><a href='#'>No projects</a></li>";
            }

            echo "</ul>";
            echo "</li>";

            // Units dropdown
            echo "<li class='dropdown'>";
            echo "<a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>Units<span class='caret'></span></a>";
            echo "<ul class='dropdown-menu'>";

            try {
                $projects = ArmyDB::retrieveProjectsFromUser($user);
                $unitcount = 0;
                foreach ($projects as $project) {
                    $projectid = $project->id;

                    try {
                        $units = ArmyDB::retrieveUnitsFromProject($projectid);

                        foreach ($units as $unit) {
                            $unitcount++;
                            $unitid = $unit->id;
                            $unitname = $unit->name;

                            echo "<li><a href='unit.php?id=" . $unitid . "'>$unitname</a></li>";
                        }
                    }
                    catch (Exception $e) {

                    }
                }
                if ($unitcount == 0) {
                    echo "<li><a href='#'>No units</a></li>";
                }
            }
            catch (Exception $e) {
                echo "<li><a href='#'>No units</a></li>";
            }

            echo "</ul>";
            echo "</li>";

            // Final header
            echo "</ul>";
            echo '<ul class="nav navbar-nav navbar-right">';
            echo '<li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>';
            echo "</ul>";
        }
        else {
            echo "<form class='navbar-form navbar-right' role='form' action='login.php'>";
            echo "<div class='form-group'>";
            echo "<input type='text' name='usr' class='form-control' placeholder='Username...' style='margin-right:10px;' required>";
            echo "<input type='password' name='pwd' class='form-control' placeholder='Password...' style='margin-right:10px;' required>";
            echo "<input type='hidden' name='action' value='login'><input type='submit' class='btn btn-warning' value='Login' style='margin-right:10px;' >";
            echo "<button type='button' class='btn btn-danger' data-toggle='modal' data-target='#myModal'>Create Account</button>";
            echo "</div></form>";
            echo "</ul>";
        }
        echo "</div></div></div></nav>";
        echo "<div class='container-fluid'>";

    }

    /**
     * Displays the log in form.
     */
    static function displayLogInUser()
    {
        echo "<div class='col-xs-6'><h1>Log Into Account</h1>";
        echo "<form role='form' method='post' action='login.php'>";
        echo "<label for='loginUser'>Username: </label>";
        echo "<input type='text' class='form-control' id='loginUser' name='usr' required>";
        echo "<label for='loginPass'>Password: </label>";
        echo "<input type='password' class='form-control' id='loginPass' name='pwd' required>";
        echo "<input type='hidden' name='action' value='login'><input type='submit' class='btn btn-default'>";
        echo "</form></div>";
    }

    /**
     * Displays the create user form.
     */
    static function displayCreateUser()
    {
        echo "<div class='col-xs-6'><h1>Create Account</h1>";
        echo "<form role='form' method='post' action='login.php'>";

        echo "<label for='createUser'>Username: </label>";
        echo "<input type='text' class='form-control' id='createUser' name='usr' required>";
        echo "<label for='createPass'>Password: </label>";
        echo "<input type='password' class='form-control' id='createPass' name='pwd' required>";
        echo "<label for='createPass'>Email Address: </label>";
        echo "<input type='email' class='form-control' id='createEmail' name='email' required>";

        echo "<input type='hidden' name='action' value='create'><input type='submit' class='btn btn-default'>";
        echo "</form></div>";
    }

    static function redirect($action, $msg, $location, $id = null)
    {
        $page = null;

        switch ($location) {
            case "index": $page = "index.php"; break;
            case "login": $page = "login.php"; break;
            case "user":  $page = "user.php"; break;
            case "project": $page = "project.php"; break;
            case "unit": $page = "unit.php"; break;
            default: $page = "index.php"; break;

        }
        if (!is_null($id)) {
            header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/' . $page . "?action=" . $action . "&msg=" . $msg . "&id=" . $id);
        }
        else {
            header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/' . $page . "?action=" . $action . "&msg=" . $msg);
        }

    }

    static function checkForLogIn()
    {
        if (isset($_SESSION['username'])) {
            return true;
        }

        return false;
    }

    static function checkForUserName($user)
    {
        $users = R::getAll('SELECT * FROM user');

        foreach ($users as $u) {
            if ($u['name'] == $user) {
                return true;
            }
        }

        return false;
    }

    static function verifyUser($user, $password)
    {
        $users = R::getAll('SELECT * FROM user');

        foreach ($users as $u) {
            // echo "ID: " . $u['id'] . " User: " . $u['name'] . " Password: " . $u['password'];
            // echo "<br>";

            if ($u['username'] == $user) {
                if ($u['password'] == $password) {
                    return true;
                }
            }

        }

        throw new IncorrectLoginException("Invalid log in. Please check your username and password.");

    }

    static function displayUserPage($user) {
        echo "<div class='row'>";
        echo "<div class='col-xs-12 col-md-8' id='main'>";
        self::displayProjects($user);
        echo "</div>";
        echo "<div class='col-xs-6 col-md-4' id='news'>";
        self::displayNews();
        echo "</div>";
        echo "</div>";
    }

    static function displayProjectPage($projectid) {
        echo "<div class='row'>";
        echo "<div class='col-xs-12 col-md-9' id='main'>";
        self::displayUnits($projectid);
        echo "</div>";
        echo "<div class='col-xs-6 col-md-3' id='news'>";
        self::displayNews();
        echo "</div>";
        echo "</div>";
    }

    static function displayUnitPage($unitID, $loggedinuser) {
        echo "<div class='row'>";
        echo "<div class='col-xs-12 col-md-9' id='main'><!--unit information-->";

        try {
            self::editUnitInformation($unitID, $loggedinuser);
        }
        catch (Exception $e) {
            self::displayUnitInformation($unitID);
        }

        echo "</div><!--end.unit information-->";
        echo "<div class='col-xs-6 col-md-3' id='news'><!--news information-->";
        self::displayNews();
        echo "</div><!--end.news information-->";
        echo "</div><!-- displayUnitPage -->";
    }

    static function displayProjects($user) {
        echo "<div id='displayProjects' class='container-fluid'>";
        echo "<h1>My projects</h1>";
        echo "<table class='table table-hover'>";

        try {
            $projects = ArmyDB::retrieveProjectsFromUser($user);
            $count = 0;

            echo "<tr><th>ID</th><th>Project Name</th><th>Battle Group</th><th>Pts</th><th colspan='2'>&nbsp;</th></tr>";

            foreach ($projects as $project) {
                $count++;
                $projectid = $project->id;
                $projectname = $project->projectname;
                $battlegroup = $project->battlegroup;
                $points = 0;

                try {
                    $units = ArmyDB::retrieveUnitsFromProject($projectid);

                    foreach ($units as $unit) {
                        $points += $unit->pts;
                    }

                }
                catch (Exception $e) {
                    $points = 0;
                }

                echo "<tr>";
                echo "<td>$count</td>";
                echo "<td><a href='project.php?id=" . $projectid . "'>$projectname</a></td>";
                echo "<td>$battlegroup</td>";
                echo "<td>$points</td>";
                echo "<td><a href='action.php?action=deleteProject&id=" . $projectid . "'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></a></td>";
                echo "</tr>";
            }
        }
        catch (Exception $e) {
            echo "<tr><td colspan='6'>No projects created yet.</td></tr>";
        }

        // add project
        echo "<form role='form' method='post' action='action.php'><tr>";
        echo "<td><span class='glyphicon glyphicon-plus' aria-hidden='true'></span></td>";
        echo "<td><input type='text' class='form-control' name='projname' placeholder='Project Name' required></td>";
        echo "<td><input type='text' class='form-control' name='btlgrp' placeholder='Battlegroup' required></td>";
        echo "<td colspan='3' class='text-center'><input type='submit' class='btn btn-default' value='Create!'></td>";
        echo "<input type='hidden' name='submitter' value='$user'>";
        echo "<input type='hidden' name='action' value='createProject'></form>";
        echo "</tr>";

        echo "</table>";
        echo "</div><!-- /div.displayProjects-->";
    }

    static function displayUnits($projectid) {
        try {
            $project = ArmyDB::retrieveProjectInfo($projectid);
            //var_dump($project);
            $projectname = $project['projectname'];
            $projectgroup = $project['battlegroup'];
            $projectgame = $project['gamegroup'];
            $description = $project['description'];

            echo "<div id='displayProjects' class='container-fluid'>";

            echo "<div class='page-header'>";
            echo "<h1>$projectname</h1>";
            echo "<h3>$projectgame</h3>";
            echo "</div>";
            echo "<div class='well'>$description</div>";
            echo "<table class='table table-hover'>";
        }
        catch (Exception $e) {
            echo $e->getMessage();
        }

        try {
            $units = ArmyDB::retrieveUnitsFromProject($projectid);
            $count = 0;

            echo "<tr><th>ID</th><th>Qty</th><th>Unit Name</th><th>Points</th><th>Status</th><th colspan='2'>&nbsp;</th></tr>";

            foreach ($units as $unit) {
                $count++;
                $unitID = $unit->id;
                $unitname = $unit->name;
                $qty = $unit->qty;
                $pts = $unit->pts;
                $status = $unit->status;

                echo "<tr>";
                echo "<td>$count</td>";
                echo "<td>$qty</td>";
                echo "<td><a href='unit.php?id=" . $unitID . "'>$unitname</a></td>";
                echo "<td>$pts</td>";
                echo "<td>$status</td>";
                echo "<td><a href='action.php?action=deleteUnit&projectid=" . $projectid . "&id=" . $unitID . "'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></a></td>";
                echo "</tr>";
            }
        }
        catch (Exception $e) {
            echo "<tr><td colspan='7'>No units created yet.</td></tr>";
        }

        // add unit
        echo "<form role='form' method='post' action='action.php'><tr>";
        echo "<td><span class='glyphicon glyphicon-plus' aria-hidden='true'></span></td>";
        echo "<td><input type='text' class='form-control' name='qty' placeholder='#' required></td>";
        echo "<td><input type='text' class='form-control' name='unitname' placeholder='Unit Name' required></td>";
        echo "<td><input type='text' class='form-control' name='pts' placeholder='Points' required></td>";
        echo "<td colspan='3' class='text-center'><input type='submit' class='btn btn-default' value='Create!'></td>";
        echo "<input type='hidden' name='action' value='createUnit'>";
        echo "<input type='hidden' name='projectid' value='" . $projectid . "'>";
        echo "</form></tr>";

        echo "</table>";
        echo "</div><!-- /div.displayProjects-->";
    }


    static function displayUnitInformation($unitid) {

        $unit = ArmyDB::retrieveUnit($unitid);

        //var_dump($unit);

        $unitname = $unit['name'];

        $unitqty = $unit['qty'];

        $unitpts = $unit['pts'];
        if ($unitpts == 1) {
            $pts = "pt";
        }
        else {
            $pts = "pts";
        }

        $unitstatus = $unit['status'];
        if ($unitstatus == 0) {
            $assembleStatus = 0;
            $baseStatus = 0;
            $paintStatus = 0;
        }
        else {
            $statusArray = str_split($unitstatus);
            $assembleStatus = $statusArray[0];
            $baseStatus = $statusArray[1];
            $paintStatus = $statusArray[2];
        }

        $unitprojectid = $unit['projectid'];

        $unitdateadded = $unit['date_added'];

        $editable = false;

        $unitnotes = $unit['notes'];
        if (empty($unitnotes)) {
            $unitnotes = "No notes entered yet.";
        }

        $project = ArmyDB::retrieveProjectInfo($unitprojectid);
        $projectname = $project['projectname'];
        $battlegroup = $project['battlegroup'];

        echo "<div class='row'>";
        echo "<div class='col-xs-6' id='unitinfo'>";
        echo "<h1>$unitqty $unitname ($unitpts" . $pts . ")</h1>";
        echo "<h4><em>$projectname - $battlegroup</em></h4>";
        echo "</div>";
        echo "<div class='col-xs-6' id='status'>";
        echo "<form id='paintintable'>";
        echo "<fieldset disabled>";

        echo "<label for='assembleStatus'>Assembly Status</label>";
        echo "<select id='assembleStatus' name='assembleStatus' class='form-control'>";
        switch ($assembleStatus) {
            case 0:
                echo "<option value='0' selected>Unassembled</option>";
                echo "<option value='1'>Partially Assembled</option>";
                echo "<option value='2'>Assembled</option>";
                break;
            case 1:
                echo "<option value='0'>Unassembled</option>";
                echo "<option value='1' selected>Partially Assembled</option>";
                echo "<option value='2'>Assembled</option>";
                break;
            case 2:
                echo "<option value='0'>Unassembled</option>";
                echo "<option value='1'>Partially Assembled</option>";
                echo "<option value='2' selected>Assembled</option>";
                break;
            default:
                echo "<option value='0'>Unassembled</option>";
                echo "<option value='1'>Partially Assembled</option>";
                echo "<option value='2'>Assembled</option>";
                break;
        }
        echo "</select>";
        echo "<label for='paintStatus'>Painting Status</label>";
        echo "<select id='paintStatus' name='paintStatus' class='form-control'>";
        switch ($paintStatus) {
            case 0:
                echo "<option value='0' selected>Bare</option>";
                echo "<option value='1'>Primed</option>";
                echo "<option value='2'>Basecoat</option>";
                echo "<option value='3'>Shade / Washd</option>";
                echo "<option value='4'>Basic Highlight</option>";
                echo "<option value='5'>Detail Highlight</option>";
                break;
            case 1:
                echo "<option value='0'>Bare</option>";
                echo "<option value='1' selected>Primed</option>";
                echo "<option value='2'>Basecoat</option>";
                echo "<option value='3'>Shade / Washd</option>";
                echo "<option value='4'>Basic Highlight</option>";
                echo "<option value='5'>Detail Highlight</option>";
                break;
            case 2:
                echo "<option value='0'>Bare</option>";
                echo "<option value='1'>Primed</option>";
                echo "<option value='2' selected>Basecoat</option>";
                echo "<option value='3'>Shade / Washd</option>";
                echo "<option value='4'>Basic Highlight</option>";
                echo "<option value='5'>Detail Highlight</option>";
                break;
            case 3:
                echo "<option value='0'>Bare</option>";
                echo "<option value='1'>Primed</option>";
                echo "<option value='2'>Basecoat</option>";
                echo "<option value='3' selected>Shade / Washd</option>";
                echo "<option value='4'>Basic Highlight</option>";
                echo "<option value='5'>Detail Highlight</option>";
                break;
            case 4:
                echo "<option value='0'>Bare</option>";
                echo "<option value='1'>Primed</option>";
                echo "<option value='2'>Basecoat</option>";
                echo "<option value='3'>Shade / Washd</option>";
                echo "<option value='4' selected>Basic Highlight</option>";
                echo "<option value='5'>Detail Highlight</option>";
                break;
            case 5:
                echo "<option value='0'>Bare</option>";
                echo "<option value='1'>Primed</option>";
                echo "<option value='2'>Basecoat</option>";
                echo "<option value='3'>Shade / Washd</option>";
                echo "<option value='4'>Basic Highlight</option>";
                echo "<option value='5' selected>Detail Highlight</option>";
                break;
            default:
                echo "<option value='0'>Bare</option>";
                echo "<option value='1'>Primed</option>";
                echo "<option value='2'>Basecoat</option>";
                echo "<option value='3'>Shade / Washd</option>";
                echo "<option value='4'>Basic Highlight</option>";
                echo "<option value='5'>Detail Highlight</option>";
                break;
        }
        echo "</select>";
        echo "<label for='assembleStatus'>Basing Status</label>";
        echo "<select id='assembleStatus' class='form-control'>";
        switch ($baseStatus) {
            case 0:
                echo "<option value='0' selected>Not based</option>";
                echo "<option value='1'>Bare basing</option>";
                echo "<option value='2'>Painting basing</option>";
                echo "<option value='3'>Highlighted basing</option>";
                break;
            case 1:
                echo "<option value='0'>Not based</option>";
                echo "<option value='1' selected>Bare basing</option>";
                echo "<option value='2'>Painting basing</option>";
                echo "<option value='3'>Highlighted basing</option>";
                break;
            case 2:
                echo "<option value='0' selected>Not based</option>";
                echo "<option value='1'>Bare basing</option>";
                echo "<option value='2' selected>Painting basing</option>";
                echo "<option value='3'>Highlighted basing</option>";
                break;
            case 3:
                echo "<option value='0' selected>Not based</option>";
                echo "<option value='1'>Bare basing</option>";
                echo "<option value='2'>Painting basing</option>";
                echo "<option value='3' selected>Highlighted basing</option>";
                break;
            default:
                echo "<option value='0' selected>Not based</option>";
                echo "<option value='1'>Bare basing</option>";
                echo "<option value='2'>Painting basing</option>";
                echo "<option value='3'>Highlighted basing</option>";
                break;
        }
        echo "</select>";
        echo "</fieldset></form>";

        echo "</div>";
        echo "</div><!-- displayUnitPage -->";
        echo "<div class='row'><p>&nbsp;</p><div class='well'>$unitnotes</div></div>";
    }

    static function editUnitInformation($unitid, $loggedinuser) {
        $unit = ArmyDB::retrieveUnit($unitid);

        //var_dump($unit);

        $unitname = $unit['name'];

        $unitqty = $unit['qty'];

        $unitpts = $unit['pts'];
        if ($unitpts == 1) {
            $pts = "pt";
        }
        else {
            $pts = "pts";
        }

        $unitstatus = $unit['status'];
        if ($unitstatus == 0) {
            $assembleStatus = 0;
            $baseStatus = 0;
            $paintStatus = 0;
        }
        else {
            $statusArray = str_split($unitstatus);
            $assembleStatus = $statusArray[0];
            $baseStatus = $statusArray[1];
            $paintStatus = $statusArray[2];
        }

        $unitprojectid = $unit['projectid'];
        $project = ArmyDB::retrieveProjectInfo($unitprojectid);


        $projectname = $project['projectname'];
        $battlegroup = $project['battlegroup'];

        $unitdateadded = $unit['date_added'];

        $editable = false;

        $unitowner = $project['username'];
        //var_dump($unitowner);
        if ($unitowner != $loggedinuser) {
            throw new Exception("Unit is owned by $unitowner, but $loggedinuser wants to edit. Can't do that.");
        }

        $unitnotes = $unit['notes'];
        if (empty($unitnotes)) {
            $unitnotes = "No notes entered yet.";
        }

        $unitadded = $unit['date_added'];
        $unitedited = $unit['date_edited'];



        echo "<form role='form' method='post' action='action.php'>";
        echo "<div class='row'>";


        echo "<h1>Edit a unit!</h1>";
        echo "<div class='col-xs-6' id='unitinfo'>";

        echo "<div class='form-group'>";
        echo "<label for='qty'>Quantity:</label>";
        echo "<input type='text' class='form-control' id='qty' name='qty' value='" . $unitqty . "'>";
        echo "</div>";

        echo "<div class='form-group'>";
        echo "<label for='unitname'>Unit Name:</label>";
        echo "<input type='text' class='form-control' id='unitname' name='unitname' value='" . $unitname . "'>";
        echo "</div>";

        echo "<div class='form-group'>";
        echo "<label for='qty'>Points</label>";
        echo "<input type='text' class='form-control' id='pts' name='pts' value='" . $unitpts . "'>";
        echo "</div>";

        echo "<label for='assembleStatus'>Assembly Status</label>";
        echo "<select id='assembleStatus' name='assembleStatus' class='form-control'>";
        switch ($assembleStatus) {
            case 0:
                echo "<option value='0' selected>Unassembled</option>";
                echo "<option value='1'>Partially Assembled</option>";
                echo "<option value='2'>Assembled</option>";
                break;
            case 1:
                echo "<option value='0'>Unassembled</option>";
                echo "<option value='1' selected>Partially Assembled</option>";
                echo "<option value='2'>Assembled</option>";
                break;
            case 2:
                echo "<option value='0'>Unassembled</option>";
                echo "<option value='1'>Partially Assembled</option>";
                echo "<option value='2' selected>Assembled</option>";
                break;
            default:
                echo "<option value='0'>Unassembled</option>";
                echo "<option value='1'>Partially Assembled</option>";
                echo "<option value='2'>Assembled</option>";
                break;
        }
        echo "</select>";
        echo "<label for='paintStatus'>Painting Status</label>";
        echo "<select id='paintStatus' name='paintStatus' class='form-control'>";
        switch ($paintStatus) {
            case 0:
                echo "<option value='0' selected>Bare</option>";
                echo "<option value='1'>Primed</option>";
                echo "<option value='2'>Basecoat</option>";
                echo "<option value='3'>Shade / Washd</option>";
                echo "<option value='4'>Basic Highlight</option>";
                echo "<option value='5'>Detail Highlight</option>";
                break;
            case 1:
                echo "<option value='0'>Bare</option>";
                echo "<option value='1' selected>Primed</option>";
                echo "<option value='2'>Basecoat</option>";
                echo "<option value='3'>Shade / Washd</option>";
                echo "<option value='4'>Basic Highlight</option>";
                echo "<option value='5'>Detail Highlight</option>";
                break;
            case 2:
                echo "<option value='0'>Bare</option>";
                echo "<option value='1'>Primed</option>";
                echo "<option value='2' selected>Basecoat</option>";
                echo "<option value='3'>Shade / Washd</option>";
                echo "<option value='4'>Basic Highlight</option>";
                echo "<option value='5'>Detail Highlight</option>";
                break;
            case 3:
                echo "<option value='0'>Bare</option>";
                echo "<option value='1'>Primed</option>";
                echo "<option value='2'>Basecoat</option>";
                echo "<option value='3' selected>Shade / Washd</option>";
                echo "<option value='4'>Basic Highlight</option>";
                echo "<option value='5'>Detail Highlight</option>";
                break;
            case 4:
                echo "<option value='0'>Bare</option>";
                echo "<option value='1'>Primed</option>";
                echo "<option value='2'>Basecoat</option>";
                echo "<option value='3'>Shade / Washd</option>";
                echo "<option value='4' selected>Basic Highlight</option>";
                echo "<option value='5'>Detail Highlight</option>";
                break;
            case 5:
                echo "<option value='0'>Bare</option>";
                echo "<option value='1'>Primed</option>";
                echo "<option value='2'>Basecoat</option>";
                echo "<option value='3'>Shade / Washd</option>";
                echo "<option value='4'>Basic Highlight</option>";
                echo "<option value='5' selected>Detail Highlight</option>";
                break;
            default:
                echo "<option value='0'>Bare</option>";
                echo "<option value='1'>Primed</option>";
                echo "<option value='2'>Basecoat</option>";
                echo "<option value='3'>Shade / Washd</option>";
                echo "<option value='4'>Basic Highlight</option>";
                echo "<option value='5'>Detail Highlight</option>";
                break;
        }
        echo "</select>";
        echo "<label for='baseStatus'>Basing Status</label>";
        echo "<select id='baseStatus' name='baseStatus' class='form-control'>";
        switch ($baseStatus) {
            case 0:
                echo "<option value='0' selected>Not based</option>";
                echo "<option value='1'>Bare basing</option>";
                echo "<option value='2'>Painting basing</option>";
                echo "<option value='3'>Highlighted basing</option>";
                break;
            case 1:
                echo "<option value='0'>Not based</option>";
                echo "<option value='1' selected>Bare basing</option>";
                echo "<option value='2'>Painting basing</option>";
                echo "<option value='3'>Highlighted basing</option>";
                break;
            case 2:
                echo "<option value='0' selected>Not based</option>";
                echo "<option value='1'>Bare basing</option>";
                echo "<option value='2' selected>Painting basing</option>";
                echo "<option value='3'>Highlighted basing</option>";
                break;
            case 3:
                echo "<option value='0' selected>Not based</option>";
                echo "<option value='1'>Bare basing</option>";
                echo "<option value='2'>Painting basing</option>";
                echo "<option value='3' selected>Highlighted basing</option>";
                break;
            default:
                echo "<option value='0' selected>Not based</option>";
                echo "<option value='1'>Bare basing</option>";
                echo "<option value='2'>Painting basing</option>";
                echo "<option value='3'>Highlighted basing</option>";
                break;
        }
        echo "</select>";
        echo "</div>";
        echo "<div class='col-xs-6' id='unitnotes'>";
        echo "<div class='form-group'>";
        echo "<label for='unitnotes'>Unit Notes</label>";
        echo "<textarea class='form-control' rows='10' id='unitnotes' name='unitnotes'>$unitnotes</textarea>";
        echo "</div>";
        echo "<div class='form-group'>";
        echo "<input type='submit' class='btn btn-warning' value='Submit Edits!'></td>";
        echo "</div>";
        echo "<input type='hidden' name='action' value='editUnit'>";
        echo "<input type='hidden' name='unitid' value='" . $unitid . "'>";
        echo "</form>";
        echo "<ul>";
        echo "<li>Date added: $unitadded</li>";
        if (!empty($unitedited)) {
            echo "<li>Date updated: $unitedited</li>";
        }
        echo "</ul>";
        echo "</div></div>";

    }


    static function displayPaintingTable($assembleStatus, $baseStatus, $paintStatus) {
        echo "<form id='paintintable'>";
        echo "<fieldset disabled>";

        echo "<label for='assembleStatus'>Assembly Status</label>";
        echo "<select id='assembleStatus' name='assembleStatus' class='form-control'>";
        switch ($assembleStatus) {
            case 0:
                echo "<option value='0' selected>Unassembled</option>";
                echo "<option value='1'>Partially Assembled</option>";
                echo "<option value='2'>Assembled</option>";
                break;
            case 1:
                echo "<option value='0'>Unassembled</option>";
                echo "<option value='1' selected>Partially Assembled</option>";
                echo "<option value='2'>Assembled</option>";
                break;
            case 2:
                echo "<option value='0'>Unassembled</option>";
                echo "<option value='1'>Partially Assembled</option>";
                echo "<option value='2' selected>Assembled</option>";
                break;
            default:
                echo "<option value='0'>Unassembled</option>";
                echo "<option value='1'>Partially Assembled</option>";
                echo "<option value='2'>Assembled</option>";
                break;
        }
        echo "</select>";
        echo "<label for='paintStatus'>Painting Status</label>";
        echo "<select id='paintStatus' name='paintStatus' class='form-control'>";
        switch ($paintStatus) {
            case 0:
                echo "<option value='0' selected>Bare</option>";
                echo "<option value='1'>Primed</option>";
                echo "<option value='2'>Basecoat</option>";
                echo "<option value='3'>Shade / Washd</option>";
                echo "<option value='4'>Basic Highlight</option>";
                echo "<option value='5'>Detail Highlight</option>";
                break;
            case 1:
                echo "<option value='0'>Bare</option>";
                echo "<option value='1' selected>Primed</option>";
                echo "<option value='2'>Basecoat</option>";
                echo "<option value='3'>Shade / Washd</option>";
                echo "<option value='4'>Basic Highlight</option>";
                echo "<option value='5'>Detail Highlight</option>";
                break;
            case 2:
                echo "<option value='0'>Bare</option>";
                echo "<option value='1'>Primed</option>";
                echo "<option value='2' selected>Basecoat</option>";
                echo "<option value='3'>Shade / Washd</option>";
                echo "<option value='4'>Basic Highlight</option>";
                echo "<option value='5'>Detail Highlight</option>";
                break;
            case 3:
                echo "<option value='0'>Bare</option>";
                echo "<option value='1'>Primed</option>";
                echo "<option value='2'>Basecoat</option>";
                echo "<option value='3' selected>Shade / Washd</option>";
                echo "<option value='4'>Basic Highlight</option>";
                echo "<option value='5'>Detail Highlight</option>";
                break;
            case 4:
                echo "<option value='0'>Bare</option>";
                echo "<option value='1'>Primed</option>";
                echo "<option value='2'>Basecoat</option>";
                echo "<option value='3'>Shade / Washd</option>";
                echo "<option value='4' selected>Basic Highlight</option>";
                echo "<option value='5'>Detail Highlight</option>";
                break;
            case 5:
                echo "<option value='0'>Bare</option>";
                echo "<option value='1'>Primed</option>";
                echo "<option value='2'>Basecoat</option>";
                echo "<option value='3'>Shade / Washd</option>";
                echo "<option value='4'>Basic Highlight</option>";
                echo "<option value='5' selected>Detail Highlight</option>";
                break;
            default:
                echo "<option value='0'>Bare</option>";
                echo "<option value='1'>Primed</option>";
                echo "<option value='2'>Basecoat</option>";
                echo "<option value='3'>Shade / Washd</option>";
                echo "<option value='4'>Basic Highlight</option>";
                echo "<option value='5'>Detail Highlight</option>";
                break;
        }
        echo "</select>";
        echo "<label for='assembleStatus'>Basing Status</label>";
        echo "<select id='assembleStatus' class='form-control'>";
        switch ($baseStatus) {
            case 0:
                echo "<option value='0' selected>Not based</option>";
                echo "<option value='1'>Bare basing</option>";
                echo "<option value='2'>Painted basing</option>";
                echo "<option value='3'>Highlighted basing</option>";
                break;
            case 1:
                echo "<option value='0'>Not based</option>";
                echo "<option value='1' selected>Bare basing</option>";
                echo "<option value='2'>Painted basing</option>";
                echo "<option value='3'>Highlighted basing</option>";
                break;
            case 2:
                echo "<option value='0' selected>Not based</option>";
                echo "<option value='1'>Bare basing</option>";
                echo "<option value='2' selected>Painted basing</option>";
                echo "<option value='3'>Highlighted basing</option>";
                break;
            case 3:
                echo "<option value='0' selected>Not based</option>";
                echo "<option value='1'>Bare basing</option>";
                echo "<option value='2'>Painted basing</option>";
                echo "<option value='3' selected>Highlighted basing</option>";
                break;
            default:
                echo "<option value='0' selected>Not based</option>";
                echo "<option value='1'>Bare basing</option>";
                echo "<option value='2'>Painted basing</option>";
                echo "<option value='3'>Highlighted basing</option>";
                break;
        }
        echo "</select>";
        echo "</fieldset></form>";
        echo "<!--paintingtable -->";


    }







    static function displayNews() {
        echo "<div id='displayNews' class='container-fluid'>";
        echo "<h1>Recent Updates</h1>";

        try {
            $news = ArmyDB::retrieveRecentNews();

            echo "<div class='list-group'>";
            //var_dump($news);

            foreach ($news as $newsitem) {
                $newsuser = $newsitem['username'];
                $newsaction = $newsitem['action'];
                $newsprojectid = $newsitem['projectid'];
                $newsunitid = $newsitem['unitid'];
                $newsdate = $newsitem['date_added'];

                //echo $newsuser . "//" . $newsaction . "//" . $newsprojectid. "//" . $newsunitid . "//" . $newsdate . "<p>";

                switch ($newsaction) {
                    case "addUnit":
                        echo "<a href='#' class='list-group-item'>";
                        echo "<h4 class='list-group-item-heading'>Unit added!</h4>";
                        echo "<p class='list-group-item-text'>" . $newsuser . " added unit " . $newsunitid . " to project " . $newsprojectid . "!</p>";
                        echo "<p class='list-group-item-text text-right small'>$newsdate</p>";
                        echo "</a>";
                        break;
                    case "addUser":
                        echo "<a href='#' class='list-group-item'>";
                        echo "<h4 class='list-group-item-heading'>New user!</h4>";
                        echo "<p class='list-group-item-text'>" . $newsuser . " has joined the site!</p>";
                        echo "<p class='list-group-item-text text-right small'>$newsdate</p>";
                        echo "</a>";
                        break;
                    case "deleteUnit":
                        echo "<a href='#' class='list-group-item'>";
                        echo "<h4 class='list-group-item-heading'>Unit deleted!</h4>";
                        echo "<p class='list-group-item-text'>" . $newsuser . " deleted unit " . $newsunitid . " from project " . $newsprojectid . "!</p>";
                        echo "<p class='list-group-item-text text-right small'>$newsdate</p>";
                        echo "</a>";
                        break;
                    case "addProject":
                        echo "<a href='#' class='list-group-item'>";
                        echo "<h4 class='list-group-item-heading'>Project added!</h4>";
                        echo "<p class='list-group-item-text'>" . $newsuser . " added project " . $newsprojectid . "!</p>";
                        echo "<p class='list-group-item-text text-right small'>$newsdate</p>";
                        echo "</a>";
                        break;
                    case "deleteProject":
                        echo "<a href='#' class='list-group-item'>";
                        echo "<h4 class='list-group-item-heading'>Project deleted!</h4>";
                        echo "<p class='list-group-item-text'>" . $newsuser . " deleted project " . $newsprojectid . "!</p>";
                        echo "<p class='list-group-item-text text-right small'>$newsdate</p>";
                        echo "</a>";
                        break;
                    case "editUnit":
                        echo "<a href='#' class='list-group-item'>";
                        echo "<h4 class='list-group-item-heading'>Unit updated!</h4>";
                        echo "<p class='list-group-item-text'>" . $newsuser . " updated unit " . $newsunitid . "!</p>";
                        echo "<p class='list-group-item-text text-right small'>$newsdate</p>";
                        echo "</a>";
                        break;
                    default:
                        echo "<a href='#' class='list-group-item'>";
                        echo "<h4 class='list-group-item-heading'>$newsaction</h4>";
                        echo "<p class='list-group-item-text'>Filler text!</p>";
                        echo "<p class='list-group-item-text text-right small'>$newsdate</p>";
                        echo "</a>";
                        break;
                }




            }
            echo "</div>";

        }
        catch (Exception $e) {
            echo $e->getMessage();
        }


        /*
        echo "<div class='list-group'>";
            echo "<a href='#' class='list-group-item'>";
                echo "<h4 class='list-group-item-heading'>First List Group Item Heading</h4>";
                echo "<p class='list-group-item-text'>List Group Item Text</p>";
            echo "</a>";
            echo "<a href='#' class='list-group-item'>";
                echo "<h4 class='list-group-item-heading'>Second List Group Item Heading</h4>";
                echo "<p class='list-group-item-text'>List Group Item Text</p>";
            echo "</a>";
            echo "<a href='#' class='list-group-item'>";
                echo "<h4 class='list-group-item-heading'>Third List Group Item Heading</h4>";
                echo "<p class='list-group-item-text'>List Group Item Text</p>";
            echo "</a>";
        echo "</div>";
        */

        echo "</div>";
    }



}



























/**
 * Exceptions. Ignore these down here.
 */

class UserAlreadyExistsException extends Exception
{
}

class IncorrectLoginException extends Exception
{
}

class NoteNotCreatedException extends Exception
{
}