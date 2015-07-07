<?php
/**
 * Contains all of the methods that interface with the database directly, and is called by all of the other pages.
 *
 * Created by phpStorm
 * User: Ryan Allan
 * Date: 2015-06-17
 * Time: 4:52 PM
 */

class ArmyDB {

    /*
     * Retrieval functions
     */

    /**
     * Retrieves all units as an array.
     * @return array
     */
    public static function retrieveAllUnits() {
        return R::getAll('SELECT * FROM unit');
    }

    /**
     * Retrieves all projects as an array.
     * @return array
     */
    public static function retrieveAllProjects() {
        return R::getAll('SELECT * FROM project');
    }

    /**
     * Retrieves all users as an array.
     * @return array
     */
    public static function retrieveAllUsers() {
        return R::getAll('SELECT * FROM user');
    }

    /**
     * Retrieves all notes as an array.
     * @return array
     */
    public static function retrieveAllNotes() {
        return R::getAll('SELECT * FROM note');
    }

    /**
     * Retrieves all news as an array.
     * @return array
     */
    public static function retrieveAllNews() {
        return R::getAll('SELECT * FROM news');
    }

    /**
     * Retrieves the project bean based on a project ID.
     * @param $projectID The project ID to look up.
     * @return \RedBeanPHP\OODBBean
     */
    public static function retrieveProjectInfo($projectID) {
        return R::load('project', $projectID);
    }

    /**
     * Retrieves all units for a specific project as an array of beans. Throws an exception if there are no units.
     * @param $projectID The project ID to look up.
     * @return array
     * @throws Exception
     */
    public static function retrieveUnitsFromProject($projectID) {
        $units = R::find('unit', 'projectid = ' . $projectID);

        if (empty($units)) {
            throw new Exception("Project $projectID has no units assigned.");
        }

        return $units;
    }

    /**
     * Retrieves the unit bean based on a unit ID. Returns an exception if the unit id does not exist.
     * @param $unitID
     * @return \RedBeanPHP\OODBBean
     * @throws Exception
     */
    public static function retrieveUnit($unitID) {
        $unit = R::load('unit', $unitID);

        if (empty($unit)) {
            throw new Exception("Unit $unitID does not exist.");
        }

        return $unit;
    }

    /**
     * Retrieves the title for a specific unit given the UNit ID; throws an exception if the unit ID does not exist.
     * @param $unitID
     * @return mixed
     * @throws Exception
     */
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

    /**
     * Retrieves the title for a specific project given the Project ID; throws an exception if the project ID doesn't exist.
     * @param $projectID
     * @return mixed
     * @throws Exception
     */
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

    /**
     * Retrieves all units based on a user ID as an array. An exception is thrown if no units have been created by that user.
     * @param $usr
     * @return array
     * @throws Exception
     */
    public static function retrieveProjectsFromUser($usr) {
        $projects = R::find('project', 'username = "' . $usr . '"');

        if (empty($projects)) {
            throw new Exception("User '$usr' has no units assigned.");
        }

        return $projects;
    }

    /**
     * Retrieves the last 10 news items, sorted by latest date.
     * @return array
     */
    public static function retrieveRecentNews() {
        return R::findAll( 'news' , ' ORDER BY date_added DESC LIMIT 10 ' );
    }

    /**
     * Retrieves the unit name associated with a specific project.
     * @param $projectid
     * @return array
     * @throws Exception
     */
    public static function retrieveUserNameFromProject($projectid) {
        $project = R::getRow('SELECT username from project WHERE id ='. $projectid);

        if(empty($project)) {
            throw new Exception("There is no username associated with $projectid.");
        }

        return $project;
    }

    /**
     * Adds a user to the SQLite DB. Throws an exception if there is an error creating the user.
     * @param $usr Username of the new user.
     * @param $password Password of the new user.
     * @param $email Email of the new user.
     * @param $fname First name of the new user.
     * @param $lname Last name of the new user.
     * @throws Exception
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

    /**
     * Adds a unit to a specific project in the DB. Throws an exception if the project ID doesn't exist, if the unit name is empty or if there's an error creating the unit.
     * @param $projectid Project ID to add to.
     * @param $unitname Name of the unit
     * @param $qty The number of models in the unit.
     * @param $pts Total points cost of the unit.
     * @param $status What the painting status is as three seperate numbers.
     * @param $notes Notes associated with the unit.
     * @return int|string
     * @throws Exception
     */
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

    /**
     * Adds a project to the Redbean DB. Throws an exception if there is an error creating a project.
     * @param $user Username of the new project.
     * @param $projectname The name of the project.
     * @param $battlegroup The battle group of the project, depending on game type.
     * @param $gamegroup The game type.
     * @param $description Description of the new project.
     * @return int|string
     * @throws Exception
     */
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

    /**
     * Adds a new news item to the list.
     * @param $user The user performing the action.
     * @param $action The action being taken. See action.php.
     * @param $projectid The project ID associated with the action.
     * @param null $unitid The unit ID associated with the action, if necessary.
     * @return int|string
     * @throws Exception
     */
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

    /**
     * Adds a note to a specific unit or project.
     * @param $poster The creator of the note.
     * @param $unitid The unit ID to post to.
     * @param $projectid The project ID to post to.
     * @param $notetext The text of the note.
     * @return int|string
     * @throws Exception
     */
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

    /**
     * Retrieves a unit and updates the unit information.
     * @param $unitid
     * @param $unitname
     * @param $qty
     * @param $pts
     * @param $status
     * @param $notes
     * @return int|string
     * @throws Exception
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

    /**
     * Retrieves a project and updates the project information based on information provided by a form.
     * @param $projectid
     * @param $projectname
     * @param $battlegroup
     * @param $gamegroup
     * @param $description
     * @return int|string
     * @throws Exception
     */
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

    /**
     * Retrieves a user and updates the user information.
     * @param $username
     * @param $fname
     * @param $lname
     * @param $email
     * @param $prefcolor **unused at this point**
     * @return int|string
     * @throws Exception
     */
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

    /**
     * Updates the password of a user. Important enough to put in a separate function.
     * @param $username Username to change
     * @param $password The new password.
     * @return int|string
     * @throws Exception
     */
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

    /**
     * Deletes a unit from the database based on unit ID.
     * @param $unitid
     * @throws Exception
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

    /**
     * Deletes all units from the database.
     * @param $projectid
     * @throws Exception
     */
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

    /**
     * Deletes a project from the database based on project ID.
     * @param $projectid
     * @throws Exception
     */
    public static function deleteProject($projectid) {
        $unit = R::load('project', $projectid);

        if(empty($unit)) {
            throw new Exception ("Project ID $projectid does not exist!");
        }
        else {
            R::trash($unit);
        }
    }

    /**
     * Retrieves the user ID associated with a specific unit ID.
     * @param $unitID
     * @return array
     * @throws Exception
     */
    public static function retrieveUserIDfromUnitID($unitID) {
        try {
            $unit = self::retrieveUnit($unitID);
            return self::retrieveUserNameFromProject($unit['projectid']);
        }
        catch (Exception $e) {
            throw new Exception("Unit ID $unitID doesn't exist, or otherwise unable to get user name.");
        }
    }

    /**
     * Checks to see if a unit exists and returns true or false.
     * @param $unitID
     * @return bool
     * @throws Exception
     */
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

    /**
     * Checks to see if a project exists and returns true or false.
     * @param $projectID
     * @return bool
     * @throws Exception
     */
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

    /**
     * The status is stored as three numbers, such as 212. This function separates them and adds them together to be from 0 to 10.
     * @param $status
     * @return mixed
     */
    public static function convertStatusToDecimal($status) {
        $statusarray = self::convertStatusToArray($status);
        return $statusarray[0] + $statusarray[1] + $statusarray[2];
    }

    /**
     * This function separates the status and returns them in an array.
     * @param $status
     * @return array
     */
    public static function convertStatusToArray($status) {
        if ($status == 0) {
            $statusArray = [0, 0, 0];
        }
        else {
            $statusArray = str_split($status);
        }

        return $statusArray;
    }

    /**
     * This function takes the number and the status type and returns the corresponding description in text.
     * @param $status
     * @param $text
     * @return string
     */
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
                    default: return "Base: Not sure what you're trying to do.";
                }
                break;
            default:
                return "Check your status information.";

        }
    }

    /**
     * Counts the number of units in a project and returns the amount.
     * @param $projectID
     * @return int
     */
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

    /**
     * Returns the sum of the points of all units in a project.
     * @param $projectID
     * @return int
     */
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

    /**
     * Calculates the project status as a percentage based on the number of points in the army.
     * Throws an exception if it is unable to access the number of points or units.
     * If there are no units, return 0%.
     * @param $projectID
     * @return float|string
     * @throws Exception
     */
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

    /**
     * Calculates the project status as a percentage based on the number of units in the army.
     * If units or points = 0, return 0.0%.
     * Throws exceptions if unable to retrieve the number of units.
     * @param $projectID
     * @return float|string
     * @throws Exception
     */
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

    /**
     * Retrieves the notes for a specific unit id.
     * @param $unitID
     * @return array
     */
    public static function retrieveNotesByUnitID($unitID) {
        return R::find('note', 'unitid = ' . $unitID);
    }

    /**
     * Retrieves the notes for a specific project id.
     * @param $projectID
     * @return array
     */
    public static function retrieveNotesByProjectID($projectID) {
        return R::find('note', 'projectid = ' . $projectID);
    }

    /**
     * Retrieves the user information for a specific user based on username.
     * @param $user
     * @return array
     */
    public static function retrieveUserInformation($user) {
        return R::find('user', 'username = ? ', [  $user ]);
    }
}