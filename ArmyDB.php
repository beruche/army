<?php
/**
 * Created by PhpStorm.
 * User: beruc_000
 * Date: 2015-06-17
 * Time: 4:52 PM
 */

class ArmyDB {

    /*
     * Retrieval functions
     */

    public static function retrieveAllUnits() {
        return R::getAll('SELECT * FROM unit');
    }

    public static function retrieveAllProjects() {
        return R::getAll('SELECT * FROM project');
    }

    public static function retrieveAllUsers() {
        return R::getAll('SELECT * FROM user');
    }

    public static function retrieveAllNotes() {
        return R::getAll('SELECT * FROM note');
    }

    public static function retrieveAllNews() {
        return R::getAll('SELECT * FROM news');
    }

    public static function retrieveProjectInfo($projectID) {
        return R::load('project', $projectID);
    }

    public static function retrieveUnitsFromProject($projectID) {
        $units = R::find('unit', 'projectid = ' . $projectID);

        if (empty($units)) {
            throw new Exception("Project $projectID has no units assigned.");
        }

        return $units;
    }

    public static function retrieveUnit($unitID) {
        $unit = R::load('unit', $unitID);

        if (empty($unit)) {
            throw new Exception("Unit $unitID does not exist.");
        }

        return $unit;
    }

    public static function retrieveUnitTitle($unitID) {
        try {
            $unit = ArmyDB::retrieveUnit($unitID);
            //var_dump($unit);
            return $unit['name'];
        }
        catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public static function retrieveProjectTitle($projectID) {
        try {
            $project = ArmyDB::retrieveProjectInfo($projectID);
            //var_dump($unit);
            return $project['projectname'];
        }
        catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public static function retrieveProjectsFromUser($usr) {
        $projects = R::find('project', 'username = "' . $usr . '"');

        if (empty($projects)) {
            throw new Exception("User '$usr' has no units assigned.");
        }

        return $projects;
    }

    public static function retrieveRecentNews() {
        return R::findAll( 'news' , ' ORDER BY date_added DESC LIMIT 10 ' );
    }


    public static function retrieveUserNameFromProject($projectid) {
        $project = R::getRow('SELECT username from project WHERE id ='. $projectid);

        if(empty($project)) {
            throw new Exception("There is no username associated with $projectid.");
        }

        return $project;
    }

    /*
     * Addition functions
     */

    public static function addUser($usr, $password, $email, $fname, $lname) {
        try {
            $user = R::dispense('user');
            $user->username = trim($usr);
            $user->password = trim($password);
            $user->email = trim($email);
            $user->fname = trim($fname);
            $user->lname = trim($lname);

            $id = R::store($user);
        }
        catch (Exception $e) {
            throw new Exception ("Unable to create user. " . $e->getMessage());
        }
    }
    public static function addUnit($projectid, $unitname, $qty, $pts, $status, $notes) {

        try {
            $dateAdded = R::isoDateTime();

            if (!is_numeric($projectid) || empty($projectid)) {
                throw new Exception("Project ID of $projectid invalid.");
            }

            if (empty($unitname)) {
                throw new Exception("Unitname is invalid: $unitname");
            }

            // add unit to list
            $unit = R::dispense('unit');
            $unit->name = $unitname;
            $unit->qty = $qty;
            $unit->pts = $pts;
            $unit->status = $status;
            $unit->projectid = $projectid;
            $unit->notes = $notes;
            $unit->date_added = $dateAdded;

            $unitid = R::store($unit);

            return $unitid;

        }
        catch (Exception $e) {
            throw new Exception ("Unable to create unit. " . $e->getMessage());
        }

    }

    public static function addProject($user, $projectname, $battlegroup, $gamegroup, $description) {

        try {
            $dateAdded = R::isoDateTime();

            $project = R::dispense('project');
            $project->username = $user;
            $project->projectname = $projectname;
            $project->battlegroup = $battlegroup;
            $project->gamegroup = $gamegroup;
            $project->description = $description;
            $project->date_added = $dateAdded;

            $projectid = R::store($project);

            return $projectid;

        }
        catch (Exception $e) {
            throw new Exception ("Unable to create project. " . $e->getMessage());
        }
    }

    public static function addNewsItem($user, $action, $projectid, $unitid = null) {

        try {
            $dateAdded = R::isoDateTime();

            $news = R::dispense('news');
            $news->username = $user;
            $news->action = $action;
            $news->projectid = $projectid;
            $news->date_added = $dateAdded;

            if(!is_null($unitid)) {
                $news->unitid = $unitid;
            }

            $newsid = R::store($news);
            return $newsid;
        }
        catch (Exception $e) {
            throw new Exception ("Unable to add news item" . $e->getMessage());
        }
    }

    public static function addNote($poster, $unitid, $projectid, $notetext) {
        $dateAdded = R::isoDateTime();

        try {
            $note = R::dispense('note');

            $note->poster = $poster;
            $note->text = $notetext;
            $note->dateAdded = $dateAdded;

            if(!is_null($unitid)) {
                $note->unitid = $unitid;
            }

            if(!is_null($projectid)) {
                $note->projectid = $projectid;
            }

            $noteid = R::store($note);

            return $noteid;
        }
        catch (Exception $e) {
            throw new Exception ("Unable to add note! " . $e->getMessage());
        }
    }

    /*
     * Updating functions
     */
    public static function updateUnit($unitid, $unitname, $qty, $pts, $status, $notes)
    {
        $dateEdited = R::isoDateTime();

        try {
            $unit = R::load('unit', $unitid);

            if (empty($unit)) {
                throw new Exception("Unit $unitid does not exist.");
            }

            if (!is_null($unitname)) {
                $unit->name = $unitname;
            }

            if (!is_null($qty)) {
                $unit->qty = $qty;
            }

            if (!is_null($pts)) {
                $unit->pts = $pts;
            }

            if (!is_null($status)) {
                $unit->status = $status;
            }

            if (!is_null($notes)) {
                $unit->notes = $notes;
            }

            $unit->date_edited = $dateEdited;

            $unitid = R::store($unit);

            return $unitid;
        }
        catch (Exception $e) {
            throw new Exception ("Unable to edit unit $unitid! " . $e->getMessage());
        }
    }

    public static function updateProject($projectid, $projectname, $battlegroup, $gamegroup, $description) {

        $dateEdited = R::isoDateTime();

        try {
            $project = R::load('project', $projectid);

            if (empty($project)) {
                throw new Exception("Project $projectid does not exist.");
            }

            if (!is_null($projectname)) {
                $project->projectname = $projectname;
            }

            if (!is_null($battlegroup)) {
                $project->battlegroup = $battlegroup;
            }

            if (!is_null($gamegroup)) {
                $project->gamegroup = $gamegroup;
            }

            if (!is_null($description)) {
                $project->description = $description;
            }

            $project->date_edited = $dateEdited;

            $projectid = R::store($project);

            return $projectid;
        }
        catch (Exception $e) {
            throw new Exception ("Unable to edit project $projectid! " . $e->getMessage());
        }
    }


    public static function updateUser($username, $fname, $lname, $email, $prefcolor) {
        try {
            $user = R::load('user', $username);

            if (empty($user)) {
                throw new Exception("Username $user does not exist!");
            }

            if (!is_null($fname)) {
                $user->fname = $fname;
            }

            if (!is_null($lname)) {
                $user->lname = $lname;
            }

            if (!is_null($email)) {
                $user->email = $email;
            }

            if (!is_null($prefcolor)) {
                $user->prefcolor = $prefcolor;
            }

            $userid = R::store($user);

            return $userid;
        }
        catch (Exception $e) {
            throw new Exception ("Unable to change password for $username!");
        }
    }

    public static function updatePassword($username, $password) {
        try {
            $user = R::load('user', $username);

            if (empty($user)) {
                throw new Exception("Username $user does not exist!");
            }

            if (strlen($password) > 0) {
                $user->password = $password;
            }

            $userid = R::store($user);

            return $userid;
        }
        catch (Exception $e) {
            throw new Exception ("Unable to change password for $username!");
        }
    }

    /*
     * Deleting functions
     */
    public static function deleteUnit($unitid) {
        $unit = R::load('unit', $unitid);

        if(empty($unit)) {
            throw new Exception ("Unit ID $unitid does not exist!");
        }
        else {
            R::trash($unit);
        }
    }

    public static function deleteAllUnits($projectid) {
        try {
            $units = R::find('unit', 'projectid = ' . $projectid);

            if (empty($units)) {
                throw new Exception ("Project $projectid is empty!");
            } else {
                foreach ($units as $unit) {
                    R::trash($unit);
                }
            }
        }
        catch (Exception $e) {
            throw new Exception ("Removal interrupted! " . $e->getMessage());
        }
    }

    public static function deleteProject($projectid) {
        $unit = R::load('project', $projectid);

        if(empty($unit)) {
            throw new Exception ("Project ID $projectid does not exist!");
        }
        else {
            R::trash($unit);
        }
    }

    public static function retrieveUserIDfromUnitID($unitID) {
        try {
            $unit = self::retrieveUnit($unitID);
            return self::retrieveUserNameFromProject($unit['projectid']);
        }
        catch (Exception $e) {
            throw new Exception("Unit ID $unitID doesn't exist, or otherwise unable to get user name.");
        }
    }

    public static function doesUnitExist($unitID) {
        try {
            $unit = self::retrieveUnit($unitID);
            if (!$unit['id'] == 0) {
                return true;
            }
            else {
                return false;
            }
        }
        catch (Exception $e) {
            throw new Exception("Unit $unitID does not exist.");
        }
    }

    public static function doesProjectExist($projectID) {
        try {
            $project = self::retrieveProjectInfo($projectID);
            if (!empty($project)) {
                return true;
            }
            else {
                return false;
            }
        }
        catch (Exception $e) {
            throw new Exception("Unit $projectID does not exist.");
        }
    }

    public static function convertStatusToDecimal($status) {
        $statusarray = self::convertStatusToArray($status);
        return $statusarray[0] + $statusarray[1] + $statusarray[2];
    }
    public static function convertStatusToArray($status) {
        if ($status == 0) {
            $statusArray = [0, 0, 0];
        }
        else {
            $statusArray = str_split($status);
        }

        return $statusArray;
    }

    public static function convertStatusToText($status, $text) {
        switch ($text) {
            case "assemble":
                switch ($status) {
                    case 0: return "Unassembled";
                    case 1: return "Partially assembled";
                    case 2: return "Assembled";
                    default: return "Assemble: Not sure what you're trying to do.";
                }
                break;
            case "paint":
                switch ($status) {
                    case 0: return "Bare";
                    case 1: return "Primed";
                    case 2: return "Basecoat";
                    case 3: return "Shade / washed";
                    case 4: return "Basic highlight";
                    case 5: return "Detail highlight";

                    default: return "Paint: Not sure what you're trying to do.";
                }
                break;
            case "base":
                switch ($status) {
                    case 0: return "Not based";
                    case 1: return "Bare basing";
                    case 2: return "Painting basin";
                    case 3: return "Highlighted basing";
                    default:
                }
                break;
            default:
                return "Check your status information.";

        }
    }

    public static function countUnitsInProject($projectID)
    {
        try {
            $project = ArmyDB::retrieveUnitsFromProject($projectID);

            //var_dump($project);

            $count = 0;
            foreach ($project as $unit) {
                //var_dump($unit);
                $count++;
            }

            return $count;
        }
        catch (Exception $e) {
            return 0;
        }
    }

    public static function countPointsInProject($projectID)
    {
        try {
            $project = ArmyDB::retrieveUnitsFromProject($projectID);
            $pts = 0;
            foreach ($project as $unit) {
                $pts += $unit['pts'];
            }

            return $pts;
        }
        catch (Exception $e) {
            return 0;
        }
    }

    public static function calculateProjectStatusByPts($projectID) {
        $countPts = ArmyDB::countPointsInProject($projectID);

        $units = ArmyDB::retrieveUnitsFromProject($projectID);
        $statusPts = 0;

        foreach ($units as $unit) {
            $statusPts += $unit['pts'] * (ArmyDB::convertStatusToDecimal($unit['status']) / 10);
        }

        if ($units == 0) {
            return "0.0%";
        }
        else {
            return round($statusPts/$countPts * 100,1);
        }
    }

    public static function calculateProjectStatusByUnits($projectID) {
        $countUnits = ArmyDB::countUnitsInProject($projectID);

        if ($countUnits == 0) {
            return "0.0";
        }

        $units = ArmyDB::retrieveUnitsFromProject($projectID);
        $statusPts = 0;

        foreach ($units as $unit) {
            $statusPts += ArmyDB::convertStatusToDecimal($unit['status']);
        }

        if ($statusPts == 0) {
            return "0.0";
        }
        return round($statusPts / ($statusPts * $countUnits) * 100, 1);

    }

    public static function retrieveNotesByUnitID($unitID) {
        return R::find('note', 'unitid = ' . $unitID);
    }

    public static function retrieveNotesByProjectID($projectID) {
        return R::find('note', 'projectid = ' . $projectID);
    }

    public static function retrieveUserInformation($user) {
        return R::find('user', 'username = ? ', [  $user ]);
    }
}