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

    public static function retrieveUnitsFromProject($projectID) {
        $units = R::find('unit', 'projectid = ' . $projectID);

        if (empty($units)) {
            throw new Exception("Project $projectID has no units assigned.");
        }

        return $units;
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

    public static function addUser($usr, $password, $email, $fname, $lname, $prefcolor) {
        try {
            $user = R::dispense('user');
            $user->username = trim($usr);
            $user->password = trim($password);
            $user->email = trim($email);
            $user->fname = trim($fname);
            $user->lname = trim($lname);
            $user->prefcolor = trim($prefcolor);

            $id = R::store($user);
        }
        catch (Exception $e) {
            throw new Exception ("Unable to create user. " . $e->getMessage());
        }
    }
    public static function addUnit($projectid, $unitname, $qty, $pts, $status, $notes) {

        try {
            $dateAdded = R::isoDateTime();

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

            $projectid = R::store('project');

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
                $unit->unitname = $unitname;
            }

            if (!is_null($qty)) {
                $unit->qty = $$qty;
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







}