<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 6/21/2015
 * Time: 6:58 PM
 */

require 'ArmyDB.php';


class ArmyForm
{


    static function messageBox($user = null)
    {
        if (!isset($_REQUEST['action'])) {
            $action = null;
        } else {
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
            } catch (Exception $e) {
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
                    $projecttitle = $project->projectname;

                    try {
                        $units = ArmyDB::retrieveUnitsFromProject($projectid);

                        foreach ($units as $unit) {
                            $unitcount++;
                            $unitid = $unit->id;
                            $unitname = $unit->name;

                            echo "<li><a href='unit.php?id=" . $unitid . "'>$projecttitle: $unitname</a></li>";
                        }
                    } catch (Exception $e) {

                    }
                }
                if ($unitcount == 0) {
                    echo "<li><a href='#'>No units</a></li>";
                }
            } catch (Exception $e) {
                echo "<li><a href='#'>No units</a></li>";
            }

            echo "</ul>";
            echo "</li>";

            // Final header
            echo "</ul>";
            echo '<ul class="nav navbar-nav navbar-right">';
            echo '<li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>';
            echo "</ul>";
        } else {
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
            case "index":
                $page = "index.php";
                break;
            case "login":
                $page = "login.php";
                break;
            case "user":
                $page = "user.php";
                break;
            case "project":
                $page = "project.php";
                break;
            case "unit":
                $page = "unit.php";
                break;
            default:
                $page = "index.php";
                break;

        }
        if (!is_null($id)) {
            header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/' . $page . "?action=" . $action . "&msg=" . $msg . "&id=" . $id);
        } else {
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

    static function displayUserPage($user)
    {
        echo "<div class='row'>";
        echo "<div class='col-xs-12 col-md-8' id='main'>";
        self::displayProjects($user);
        echo "</div>";
        echo "<div class='col-xs-6 col-md-4' id='news'>";
        self::displayNews();
        echo "</div>";
        echo "</div>";
    }

    static function displayProjects($user)
    {
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

                } catch (Exception $e) {
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
        } catch (Exception $e) {
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

    static function displayUnits($projectid, $loggedinuser)
    {
        try {
            $project = ArmyDB::retrieveProjectInfo($projectid);
            //var_dump($project);
            $projectname = $project['projectname'];
            $projectgroup = $project['battlegroup'];
            if ($projectgroup == "") {
                $projectgroup = "N/A";
            }
            $projectgame = $project['gamegroup'];
            if ($projectgame == "") {
                $projectgame = "N/A";
            }
            $description = $project['description'];
            if (empty($description)) {
                $description = "Description is not currently set.";
            }
            $description = preg_replace('/\n(\s*\n)+/', '</p><p>', $description);
            $description = preg_replace('/\n/', '<br>', $description);
            $description = '<p>' . $description . '</p>';


            $projectdateadded = $project['date_added'];
            $projectdateedited = $project['date_edited'];

            $projectowner = $project['username'];

            $editable = false;
            if ($projectowner == $loggedinuser) {
                $editable = true;
            }

            $totalUnits = ArmyDB::countUnitsInProject($projectid);
            if (empty($totalUnits)) {
                $totalUnits = 0;
            }
            $totalPoints = ArmyDB::countPointsInProject($projectid);
            if (empty($totalPoints)) {
                $totalPoints = 0;
            }

            echo "<div class='row' id='topRow'>";
            echo "<div id='displayProjectInformation' class='col-xs-12 col-md-9'>";
            echo "<h1>$projectname ($totalPoints pts)</h1>";
            echo "<h4>$projectgroup - $projectgame</h3>";
            echo "<p>$description</p>";
            echo "</div><!-- displayProjectInformation -->";
            echo "<div id='image' class='col-xs-12 col-md-3'>";
            echo "<img src='http://placehold.it/480x320' class='img-responsive pull-right' alt='placeholder'>";
            echo "</div><!-- image -->";
            echo "</div><!-- topRow -->";

            echo "<div class='row' id='middleRow'>";
            echo "<div id='stats' class='col-xs-12 col-md-4'>";
            echo "<h3>Statistics</h3>";
            echo "<dl class='dl-horizontal'>";
            echo "<dt># of units:</dt>";
            echo "<dd>$totalUnits</dd>";
            echo "<dt># of points:</dt>";
            echo "<dd>$totalPoints</dd>";
            echo "<dt>Date Added:</dt>";
            echo "<dd>$projectdateadded</dd>";
            if (isset($projectdateedited)) {
                echo "<dt>Date Last Edited:</dt><dd>$projectdateedited</dd>";
            }
            echo "</dl>";
            echo "</div><!-- stats -->";
            echo "<div id='status' class='col-xs-12 col-md-4'>";
            echo "<h3>Completion</h3>";
            echo "<dl class='dl-horizontal'>";
            echo "<dt>Completion (by units):</dt>";
            echo "<dd>" . ArmyDB::calculateProjectStatusByUnits($projectid) . "%</dd>";
            echo "<dt>Completion (by points):</dt>";
            echo "<dd>" . ArmyDB::calculateProjectStatusByPts($projectid) . "%</dd>";
            echo "</dl>";
            echo "</div><!-- status -->";

            if ($editable) {
                echo "<div id='maintainProject' class='col-xs-12 col-md-4'>";
                echo "<h3>Edit Project</h3>";
                echo "<div class='btn-group-vertical' role='group' aria-label='edit projects'>";
                echo "<button type='button' class='btn btn-warning btn-lg' data-toggle='modal' data-target='#editProject'>Edit Project</button>";
                echo "<button type='button' class='btn btn-danger btn-lg' data-toggle='modal' data-target='#deleteProject'>Delete Project</button>";
                echo "</div>";

                // edit project
                echo "<div id='editProject' class='modal fade' role='dialog'>";
                echo "<div class='modal-dialog'>";
                echo "<form role='form' id='editProject' method='post' action='action.php'>";
                echo "<div class='modal-content'>";
                echo "<div class='modal-header'>";
                echo "<button type='button' class='close' data-dismiss='modal'>&times;</button>";
                echo "<h4 class='modal-title'>Edit Project</h4>";
                echo "</div>";
                echo "<div class='modal-body'>";

                echo "<div class='form-group'>";
                echo "<label for='projectname'>Project Title:</label>";
                echo "<input type='text' class='form-control' id='projectname' name='projectname' value='" . $projectname . "'>";
                echo "</div>";

                echo "<div class='form-group'>";
                echo "<label for='btlgrp'>Battlegroup:</label>";
                echo "<input type='text' class='form-control' id='btlgrp' name='btlgrp' value='" . $projectgroup . "'>";
                echo "</div>";

                echo "<div class='form-group'>";
                echo "<label for='gamegrp'>Gamegroup:</label>";
                echo "<input type='text' class='form-control' id='gamegrp' name='gamegrp' value='" . $projectgame . "'>";
                echo "</div>";

                echo "<div class='form-group'>";
                echo "<label for='desc'>Project Notes:</label>";
                echo "<textarea class='form-control' rows='10' id='desc' name='desc'>$description</textarea>";
                echo "</div>";

                echo "</div>";
                echo "<div class='modal-footer'>";
                echo "<input type='submit' class='btn btn-warning' value='Submit Edits!'></td>";
                echo "<input type='hidden' name='action' value='editProject'>";
                echo "<input type='hidden' name='id' value='" . $projectid . "'>";
                echo "</form>";
                echo "</div><!-- modal-footer -->";
                echo "</div><!-- modal-content -->";
                echo "</div><!-- modal-dialog -->";
                echo "</div><!-- editProject -->";


                // delete project
                echo "<div id='deleteProject' class='modal fade' role='dialog'>";
                echo "<div class='modal-dialog'>";
                echo "<div class='modal-content'>";
                echo "<div class='modal-header'>";
                echo "<button type='button' class='close' data-dismiss='modal'>&times;</button>";
                echo "<h4 class='modal-title'>Delete Project</h4>";
                echo "</div><!-- modal-header -->";
                echo "<div class='modal-body'>";
                echo "<p>Are you sure?</p>";
                echo "</div><!-- modal-body -->";
                echo "<div class='modal-footer'>";
                echo "<form role='form' id='deleteUnit' method='post' action='action.php'>";
                echo "<div class='btn-group' role='group' aria-label='...'>";
                echo "<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>";
                echo "<input type='submit' class='btn btn-danger' value='Delete Project'></td>";
                echo "<input type='hidden' name='action' value='deleteProject'>";
                echo "<input type='hidden' name='id' value='" . $projectid . "'>";
                echo "</div><!-- btn-group-->";
                echo "</form>";
                echo "</div><!-- modal-footer -->";
                echo "</div><!-- modal-content -->";
                echo "</div><!-- modal-dialog -->";
                echo "</div><!-- deleteProject -->";
                echo "</div>";

            } else {
                echo "<div id='userInfo' class='col-xs-12 col-md-4'>";
                echo "<h3>User Information</h3>";
                echo "<ul>";
                echo "<li>Username: XXX</li>";
                echo "<li>Full Name: XXX</li>";
                echo "<li>Location: XXX</li>";
                echo "</ul>";
                echo "</div><!-- userInfo -->";
            }

            echo "</div><!-- middleRow -->";

            echo "<div class='row' id='tableRow'>";
            echo "<div id='table' class='col-xs-12'>";
            echo "<h3>Units</h3>";
            echo "<table class='table table-hover'>";


            echo "<tr><th class='text-center col-xs-1'>ID</th><th class='text-center col-xs-1'>Qty</th><th class='col-xs-6'>Unit Name</th><th class='text-center col-xs-2'>Points</th><th class='text-center col-xs-2'>Status</th><th class='col-xs-1'>&nbsp;</th></tr>";

            $count = 0;

            try {
                $units = ArmyDB::retrieveUnitsFromProject($projectid);

                foreach ($units as $unit) {
                    $count++;
                    $unitID = $unit->id;
                    $unitname = $unit->name;
                    $qty = $unit->qty;
                    $pts = $unit->pts;
                    $status = 10 * ArmyDB::convertStatusToDecimal($unit->status) . "%";

                    echo "<tr>";
                    echo "<td class='text-center'>$count</td>";
                    echo "<td class='text-center'>$qty</td>";
                    echo "<td><a href='unit.php?id=" . $unitID . "'>$unitname</a></td>";
                    echo "<td class='text-center'>$pts</td>";
                    echo "<td class='text-center'>$status</td>";
                    if ($editable) {
                        echo "<td><a href='action.php?action=deleteUnit&projectid=" . $projectid . "&id=" . $unitID . "'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></a></td>";
                    } else {
                        echo "<td>&nbsp;</td>";
                    }
                    echo "</tr>";
                }
            } catch (Exception $e) {
                echo "<tr><td colspan='6'>This project has no units.</td></tr>";
            }


            // add unit
            if ($editable) {
                echo "<tr></tr><form role='form' method='post' action='action.php'>";
                echo "<td class='text-center'><span class='glyphicon glyphicon-plus' aria-hidden='true'></span></td>";
                echo "<td><input type='text' class='form-control' name='qty' placeholder='#' required></td>";
                echo "<td><input type='text' class='form-control' name='unitname' placeholder='Unit Name' required></td>";
                echo "<td><input type='text' class='form-control' name='pts' placeholder='Points' required></td>";
                echo "<td class='text-center'><input type='submit' class='btn btn-success' value='Create!'></td><td>&nbsp;</td></td>";
                echo "<input type='hidden' name='action' value='createUnit'>";
                echo "<input type='hidden' name='projectid' value='" . $projectid . "'>";
                echo "</form></tr>";
            }

            echo "</table>";
            echo "</div>";
            echo "</div><!-- /tableRow -->";
            ArmyForm::displayNotes($projectid);

        } catch (Exception $e) {

        }
    }


    public static function displayUnitInformation($unitid, $user)
    {

        $unit = ArmyDB::retrieveUnit($unitid);

        //var_dump($unit);

        $unitname = $unit['name'];

        $unitqty = $unit['qty'];

        $unitpts = $unit['pts'];
        if ($unitpts == 1) {
            $pts = "pt";
        } else {
            $pts = "pts";
        }

        $unitstatus = $unit['status'];
        if ($unitstatus == 0) {
            $assembleStatus = 0;
            $baseStatus = 0;
            $paintStatus = 0;
        } else {
            $statusArray = str_split($unitstatus);
            $assembleStatus = $statusArray[0];
            $baseStatus = $statusArray[1];
            $paintStatus = $statusArray[2];
        }

        $unitprojectid = $unit['projectid'];

        $unitdateadded = $unit['date_added'];
        if (isset($unit['date_edited'])) {
            $unitdateedited = $unit['date_edited'];
        } else {
            $unitdateedited = null;
        }

        $unitnotes = $unit['notes'];
        if (empty($unitnotes)) {
            $unitnotes = "No notes entered yet.";
        }
        $unitnotes = preg_replace('/\n(\s*\n)+/', '</p><p>', $unitnotes);
        $unitnotes = preg_replace('/\n/', '<br>', $unitnotes);
        $unitnotes = '<p>' . $unitnotes . '</p>';

        $project = ArmyDB::retrieveProjectInfo($unitprojectid);
        $projectname = $project['projectname'];
        $battlegroup = $project['battlegroup'];
        $projectowner = $project['username'];

        echo "<div class='row'>";
        echo "<div class='col-xs-12 col-md-6' id='unitinfo'>";
        echo "<h1>$unitqty $unitname ($unitpts" . $pts . ")</h1>";
        echo "<h4><em><a href='project.php?id=" . $unitprojectid . "'>$projectname</a> - $battlegroup</em></h4>";

        echo "<p>$unitnotes</p>";

        echo "<h2>Status</h2>";
        echo "<ul>";
        echo "<li>Assembly: " . ArmyDB::convertStatusToText($assembleStatus, "assemble") . "</li>";
        echo "<li>Painting: " . ArmyDB::convertStatusToText($paintStatus, "paint") . "</li>";
        echo "<li>Basing  : " . ArmyDB::convertStatusToText($baseStatus, "base") . "</li>";
        echo "<li>Date added: $unitdateadded</li>";
        if (!is_null($unitdateedited)) {
            echo "<li>Date added: $unitdateedited</li>";
        }
        echo "</ul>";

        echo "<div class='btn-group' role='group'>";
        if ($projectowner == $user) {
            echo "<button type='button' class='btn btn-warning btn-lg' data-toggle='modal' data-target='#editUnit'>Edit Unit</button>";
            echo "<button type='button' class='btn btn-danger btn-lg' data-toggle='modal' data-target='#deleteUnit'>Delete Unit</button>";

            // edit Unit
            echo "<div id='editUnit' class='modal fade' role='dialog'>";
            echo "<div class='modal-dialog'>";

            echo "<div class='modal-content'>";
            echo "<div class='modal-header'>";
            echo "<button type='button' class='close' data-dismiss='modal'>&times;</button>";
            echo "<h4 class='modal-title'>Edit Unit</h4>";
            echo "</div>";
            echo "<form role='form' method='post' action='action.php'>";
            echo "<div class='modal-body'>";

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
            echo "<div class='form-group'>";
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
                    echo "<option value='3'>Shade / Washed</option>";
                    echo "<option value='4'>Basic Highlight</option>";
                    echo "<option value='5'>Detail Highlight</option>";
                    break;
                case 1:
                    echo "<option value='0'>Bare</option>";
                    echo "<option value='1' selected>Primed</option>";
                    echo "<option value='2'>Basecoat</option>";
                    echo "<option value='3'>Shade / Washed</option>";
                    echo "<option value='4'>Basic Highlight</option>";
                    echo "<option value='5'>Detail Highlight</option>";
                    break;
                case 2:
                    echo "<option value='0'>Bare</option>";
                    echo "<option value='1'>Primed</option>";
                    echo "<option value='2' selected>Basecoat</option>";
                    echo "<option value='3'>Shade / Washed</option>";
                    echo "<option value='4'>Basic Highlight</option>";
                    echo "<option value='5'>Detail Highlight</option>";
                    break;
                case 3:
                    echo "<option value='0'>Bare</option>";
                    echo "<option value='1'>Primed</option>";
                    echo "<option value='2'>Basecoat</option>";
                    echo "<option value='3' selected>Shade / Washed</option>";
                    echo "<option value='4'>Basic Highlight</option>";
                    echo "<option value='5'>Detail Highlight</option>";
                    break;
                case 4:
                    echo "<option value='0'>Bare</option>";
                    echo "<option value='1'>Primed</option>";
                    echo "<option value='2'>Basecoat</option>";
                    echo "<option value='3'>Shade / Washed</option>";
                    echo "<option value='4' selected>Basic Highlight</option>";
                    echo "<option value='5'>Detail Highlight</option>";
                    break;
                case 5:
                    echo "<option value='0'>Bare</option>";
                    echo "<option value='1'>Primed</option>";
                    echo "<option value='2'>Basecoat</option>";
                    echo "<option value='3'>Shade / Washed</option>";
                    echo "<option value='4'>Basic Highlight</option>";
                    echo "<option value='5' selected>Detail Highlight</option>";
                    break;
                default:
                    echo "<option value='0'>Bare</option>";
                    echo "<option value='1'>Primed</option>";
                    echo "<option value='2'>Basecoat</option>";
                    echo "<option value='3'>Shade / Washed</option>";
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
            echo "<div class='form-group'>";
            echo "<label for='unitnotes'>Unit Notes</label>";
            echo "<textarea class='form-control' rows='10' id='unitnotes' name='unitnotes'>$unitnotes</textarea>";
            echo "</div>";
            echo "<input type='hidden' name='action' value='editUnit'>";
            echo "<input type='hidden' name='unitid' value='" . $unitid . "'>";


            echo "</div>";
            echo "<div class='modal-footer'>";
            echo "<input type='submit' class='btn btn-warning' value='Submit Edits!'></td>";
            echo "</div>";
            echo "</form>";
            echo "</div>";

            echo "</div>";
            echo "</div>";

            // modal form for deleting units! //

            echo "<div id='deleteUnit' class='modal fade' role='dialog'>";
            echo "<div class='modal-dialog'>";

            echo "<div class='modal-content'>";
            echo "<div class='modal-header'>";
            echo "<button type='button' class='close' data-dismiss='modal'>&times;</button>";
            echo "<h4 class='modal-title'>Delete Unit</h4>";
            echo "</div>";
            echo "<form role='form' method='post' action='action.php'>";
            echo "<div class='modal-body'>";
            echo "<p>Are you sure?</p>";
            echo "</div>";
            echo "<div class='modal-footer'>";
            echo "<form role='form' id='deleteUnit' method='post' action='action.php'>";
            echo "<div class='btn-group' role='group' aria-label='...'>";
            echo "<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>";
            echo "<input type='submit' class='btn btn-danger' value='Delete Unit!'></td>";
            echo "<input type='hidden' name='action' value='deleteUnit'>";
            echo "<input type='hidden' name='id' value='" . $unitid . "'><input type='hidden' name='projectid' value='" . $unitprojectid . "'>";
            echo "</div>";
            echo "</form>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        } else {
            echo "<button type='button' class='btn btn-default btn-lg' data-toggle='modal' data-target='#editUnit' disabled>Edit Unit</button>";
            echo "<button type='button' class='btn btn-default btn-lg' data-toggle='modal' data-target='#deleteUnit' disabled>Delete Unit</button>";
        }

        echo "</div><!-- buttongroup -->";
        echo "</div><!-- unitinfo -->";

        echo "<div class='col-xs-12 col-md-6' id='image'>";
        echo "<img src='http://placehold.it/480x320' class='img-responsive pull-right' alt='placeholder'>";

        echo "</div><!-- image -->";

        echo "</div><!-- row -->";
        ArmyForm::displayNotes($unitprojectid, $unitid);


    }



    static function displayNews()
    {
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
                if (!is_null($newsunitid)) {
                    $newsunittitle = ArmyDB::retrieveUnitTitle($newsunitid);
                    if (empty($newsunittitle)) {
                        $newsunittitle = "a now deleted unit";
                    }
                }
                if (!is_null($newsprojectid)) {
                    $newsprojecttitle = ArmyDB::retrieveProjectTitle($newsprojectid);
                    if (empty($newsprojecttitle)) {
                        $newsprojecttitle = "a now deleted project";
                    }
                }

                //echo $newsuser . "//" . $newsaction . "//" . $newsprojectid. "//" . $newsunitid . "//" . $newsdate . "<p>";

                switch ($newsaction) {
                    case "addUnit":
                        echo "<a href='unit.php?id=" . $newsunitid . "' class='list-group-item bg-success'>";
                        echo "<h4 class='list-group-item-heading'>Unit added!</h4>";
                        echo "<p class='list-group-item-text'> <b>$newsuser</b> added <b>$newsunittitle</b> to <b>$newsprojecttitle</b>!</p>";
                        echo "<p class='list-group-item-text text-right small'>$newsdate</p>";
                        echo "</a>";
                        break;
                    case "addUser":
                        echo "<a href='user.php?username" . $newsuser . "' class='list-group-item'>";
                        echo "<h4 class='list-group-item-heading'>New user!</h4>";
                        echo "<p class='list-group-item-text'><b>$newsuser</b> has joined the site!</p>";
                        echo "<p class='list-group-item-text text-right small'>$newsdate</p>";
                        echo "</a>";
                        break;
                    case "deleteUnit":
                        echo "<a href='project.php?id=" . $newsprojectid . "' class='list-group-item'>";
                        echo "<h4 class='list-group-item-heading'>Unit deleted!</h4>";
                        echo "<p class='list-group-item-text'><b>$newsuser</b> deleted a unit from <b>$newsprojecttitle</b>!</p>";
                        echo "<p class='list-group-item-text text-right small'>$newsdate</p>";
                        echo "</a>";
                        break;
                    case "addProject":
                        echo "<a href='project.php?id=" . $newsprojectid . "' class='list-group-item'>";
                        echo "<h4 class='list-group-item-heading'>Project added!</h4>";
                        echo "<p class='list-group-item-text'><b>$newsuser</b> added a new project: <b>$newsprojecttitle!</b></p>";
                        echo "<p class='list-group-item-text text-right small'>$newsdate</p>";
                        echo "</a>";
                        break;
                    case "deleteProject":
                        echo "<a href='user.php?username" . $newsuser . "' class='list-group-item'>";
                        echo "<h4 class='list-group-item-heading'>Project deleted!</h4>";
                        echo "<p class='list-group-item-text'><b>$newsuser</b> deleted a project!</p>";
                        echo "<p class='list-group-item-text text-right small'>$newsdate</p>";
                        echo "</a>";
                        break;
                    case "editUnit":
                        echo "<a href='unit.php?id=" . $newsunitid . "' class='list-group-item'>";
                        echo "<h4 class='list-group-item-heading'>Unit updated!</h4>";
                        echo "<p class='list-group-item-text'><b>$newsuser</b> updated the unit <b>$newsunittitle</b>!</p>";
                        echo "<p class='list-group-item-text text-right small'>$newsdate</p>";
                        echo "</a>";
                        break;
                    case "updateProject":
                        echo "<a href='project.php?id=" . $newsprojectid . "' class='list-group-item'>";
                        echo "<h4 class='list-group-item-heading'>Project updated!</h4>";
                        echo "<p class='list-group-item-text'><b>$newsuser</b> updated the project <b>$newsprojecttitle</b>!</p>";
                        echo "<p class='list-group-item-text text-right small'>$newsdate</p>";
                        echo "</a>";
                        break;
                    case "addNote":
                        if (!is_null($newsunitid)) {
                            echo "<a href='unit.php?id=" . $newsunitid . "' class='list-group-item'>";
                            echo "<h4 class='list-group-item-heading'>Note posted!</h4>";
                            echo "<p class='list-group-item-text'><b>$newsuser</b> posted on unit <b>$newsunittitle</b>!</p>";
                        }
                        else {
                            echo "<a href='project.php?id=" . $newsprojectid . "' class='list-group-item'>";
                            echo "<h4 class='list-group-item-heading'>Note posted!</h4>";
                            echo "<p class='list-group-item-text'><b>$newsuser</b> posted on unit <b>$newsprojecttitle</b>!</p>";
                        }
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

        } catch (Exception $e) {
            echo $e->getMessage();
        }

        echo "</div>";
    }

    public static function displayNotes($projectID, $unitID = null) {

        if (!is_null($unitID)) {
            $notes = ArmyDB::retrieveNotesByUnitID($unitID);
        } else {
            $notes = ArmyDB::retrieveNotesByProjectID($projectID);
        }

        echo "<div class='row'>";
        echo "<h1>Comments</h1>";
        foreach ($notes as $note) {
            //var_dump($note);
            $poster = $note['poster'];
            $notetext = $note['text'];
            $dateadded = $note['date_added'];

            echo "<div class='list-group col-xs-6 col-md-3'>";
            echo "<a href='#' class='list-group-item'>";
            echo "<li class='list-group-item list-group-item'>$notetext</li>";
            echo "<li class='list-group-item list-group-item-danger'>Posted by $poster<br>$dateadded</li>";
            echo "</ul>";
            echo "</div>";
            
            
        }

        if (ArmyForm::checkForLogIn()) {
            $poster = $_SESSION['username'];
            echo "<div class='col-xs-6 col-md-3'>";
            echo "<div class='list-group'>";
            echo "<a href='#' class='list-group-item'>";
            echo "<h3>Post a comment!</h3>";
            echo "<form role='form' method='post' action='action.php'>";
            echo "<textarea class='form-control' rows='10' id='desc' name='notetext' placeholder='Post your note here!'></textarea>";
            if (isset($unitID)) {
                echo "<input type='hidden' name='unitid' value='$unitID'>";
            }
            echo "<input type='hidden' name='action' value='addNote'>";
            echo "<input type='hidden' name='projectid' value='$projectID'>";
            echo "<input type='hidden' name='poster' value='$poster'><input type='submit' class='btn btn-default'>";
            echo "</form>";
            echo "<p class='list-group-item-text'></p>";
            echo "</a>";
            echo "</div></div>";
        }

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