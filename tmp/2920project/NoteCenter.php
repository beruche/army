<?php

/**
 * Contains all functionality for modifying and viewing notes.
 * Created by PhpStorm.
 * User: Ryan Allan
 * Date: 2015-06-10
 * Time: 5:17 PM
 */
class NoteCenter
{

    /**
     * Displays the messages at the top of the screen, categorizing messages as errors or successes and displays appropriately.
     * @param $user The user that is currently logged in.
     */
    static function welcomeUser($user)
    {

        if ((!isset($_REQUEST['msg']))) {
            if ($user == null) {
                echo "<div class='alert alert-info' role='alert'>Welcome! Please log in or create a new account to continue.</div>";
            }
            else {
                echo "<div class='alert alert-warning' role='alert'>Welcome, $user!</div>";
            }
        } else {
            switch ($_REQUEST['action']) {
                case "error":
                    echo '<div class="alert alert-danger" role="alert">' . $_REQUEST['msg'] . '</div>';
                    break;
                case "success":
                    echo '<div class="alert alert-success" role="alert">' . $_REQUEST['msg'] . '</div>';
                    break;
                default:
                    echo "<div class='alert alert-info' role='alert'>Welcome, $user!</div>";
                    break;
            }
        }
    }

    /**
     * Displays the header of the page.
     * @param $isLoggedIn Is the user logged in?
     * @param null $user The logged in user, if there is not. Defaults to null.
     */
    static function header($isLoggedIn, $user = null)
    {
        require('header.html');
        echo '<nav class="navbar navbar-inverse">';
        echo '<div class="container-fluid">';
        echo '<div class="navbar-header">';
        echo '<a class="navbar-brand" href="index.php">SUPER NOTE GENERATOR</a>';
        echo '</div><div>';

        if ($isLoggedIn) {
            echo '<ul class="nav navbar-nav navbar-right">';
            echo '<li><a href="#"><span class="glyphicon glyphicon-user"></span> Signed in as ' . $user . '</a></li>';
            echo '<li><a href="index.php?action=logout"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>';
            echo "</ul>";
        }

        echo "</div></div></div></nav>";
        echo "<div class='container-fluid'>";

    }

    /**
     * Displays the create note functionality.
     * @param $user The logged in user.
     */
    static function displayCreateNote($user)
    {
        echo "<div id='createNote' class='col-xs-6'>";
        echo "<h2>Add a note!</h2>";
        echo "<form method='POST' action='index.php'>";
        echo "<input type='hidden' name='action' value='addNote'>";
        echo "<input type='hidden' name='usernote' value=" . $user . ">";
        echo "<textarea class='form-control' rows='3' name='note' placeholder='Enter note here!'></textarea><p>";
        echo "<input type='submit'></div>";

    }


    /**
     * Adds a note to the database if possible.
     * @param $user The logged in user.
     * @param $note The note text.
     * @return int|string Returns the Note ID if successful.
     * @throws NoteNotCreatedException
     * @throws \RedBeanPHP\RedException
     */
    static function addNote($user, $note)
    {
        if (strlen(trim($note)) == 0) {
            throw new NoteNotCreatedException("Note is blank! Please try again.");
        }

        $newNote = R::dispense('note');
        $newNote->text = $note;
        $noteID = R::store($newNote);

        $newUserNotes = R::dispense('usernotes');
        $newUserNotes->userid = $user;
        $newUserNotes->noteid = $noteID;
        $newUserNotes->date_created = R::isoDateTime();
        R::store($newUserNotes);

        return $noteID;
    }

    /**
     * Displays the stored notes for this user on the screen.
     * @param $user
     */
    static function viewNotes($user)
    {
        $notes = R::getAll(' SELECT noteid, date_created FROM usernotes WHERE userid = "' . $user . '"');

        echo '<div class="list-group col-xs-6">';
        echo '<h2>Your notes!</h2>';

        //var_dump($notes);
        $count = 0;

        foreach ($notes as $n) {
            $dateCreated = $n['date_created'];
            $specificNote = "note-" . $user . "-" . $n['noteid'];
            $note = R::getRow('SELECT text FROM note WHERE id = "' . $n['noteid'] . '"');

            //var_dump($note);
            echo '<a href="#" class="list-group-item">';
            echo '<h4 class="list-group-item-heading">' . $note['text'] . '</h4>';
            echo '<p class="list-group-item-text">' . $dateCreated . '</p>';
            echo '<a href="index.php?action=edit&noteToEdit=' . $specificNote . '"><span class="glyphicon glyphicon-edit"></span> Edit</span></a></p></a>';
        }

        echo '</div>';
    }

    /**
     * Shows the form to edit a specific note.
     * @param $user
     * @param $noteid
     * @throws NoteNotCreatedException
     */
    static function editNote($user, $noteid)
    {
        $noteinfo = preg_split("/-/", $noteid);
        //var_dump($noteinfo);
        $userToEdit = $noteinfo[1];
        $noteToEdit = $noteinfo[2];

        if (!($user == $userToEdit)) {
            throw new NoteNotCreatedException("$user does not match $userToEdit! Aborting.");
        }

        $note = R::getRow('SELECT text FROM note WHERE id = "' . $noteToEdit . '"');
        //var_dump($note);

        echo "<div id='editNote' class='col-xs-6'>";
        echo "<h2>Edit a note!</h2>";
        echo "<form method='POST' action='index.php'>";
        echo "<input type='hidden' name='action' value='updateNote'>";
        echo "<input type='hidden' name='noteToEdit' value=" . $noteToEdit . ">";
        echo "<textarea class='form-control' rows='3' name='note' placeholder='Enter note here!'>" . $note['text'] . "</textarea><p>";
        echo "<input type='submit'></div>";
    }

    /**
     * Updates a note in the database.
     * @param $noteid
     * @param $notetext
     * @throws NoteNotCreatedException
     */
    static function updateNote($noteid, $notetext)
    {
        if (strlen(trim($notetext)) == 0) {
            throw new NoteNotCreatedException("Note is blank! Please try again.");
        }

        $noteToEdit = R::load('note', $noteid);
        $noteToEdit->text = $notetext;
        R::store($noteToEdit);
    }
}