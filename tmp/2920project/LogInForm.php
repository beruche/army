<?php

/**
 * Displays all functionality for logging into the system.
 * Created by PhpStorm.
 * User: Ryan Allan
 * Date: 2015-06-08
 * Time: 6:06 PM
 */
class LogInForm
{
    /**
     * Displays the log in form.
     */
    static function displayLogInUser()
    {
        echo "<div class='col-xs-6'><h1>Log Into Account</h1>";
        echo "<form role='form' method='POST' action='index.php' \>";
        echo "<label for='loginUser'>Username: </label>";
        echo "<input type='username' class='form-control' id='loginUser' name='usr' required>";
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
        echo "<form role='form' method='POST' action='index.php'>";

        echo "<label for='createUser'>Username: </label>";
        echo "<input type='username' class='form-control' id='createUser' name='usr' required>";
        echo "<label for='createPass'>Password: </label>";
        echo "<input type='password' class='form-control' id='createPass' name='pwd' required>";
        echo "<label for='createPass'>Email Address: </label>";
        echo "<input type='email' class='form-control' id='createEmail' name='email' required>";

        echo "<input type='hidden' name='action' value='create'><input type='submit' class='btn btn-default'>";
        echo "</form></div>";
    }

    /**
     * Verifies whether the username and password are correct.
     * @param $user
     * @param $password
     * @return bool
     * @throws IncorrectLoginException
     */
    static function verifyUser($user, $password)
    {
        $users = R::getAll('SELECT * FROM user');

        foreach ($users as $u) {
            // echo "ID: " . $u['id'] . " User: " . $u['name'] . " Password: " . $u['password'];
            // echo "<br>";

            if ($u['name'] == $user) {
                if ($u['password'] == $password) {
                    return true;
                }
            }

        }

        throw new IncorrectLoginException("Invalid log in. Please check your username and password.");
    }

    /**
     * Creates the user in the database if they are not duplicate.
     * @param $user
     * @param $password
     * @param $email
     * @throws UserAlreadyExistsException
     * @throws \RedBeanPHP\RedException
     */
    static function createUser($user, $password, $email)
    {
        // check for duplicate names
        if (!LogInForm::checkForUserName($user)) {
            $newUser = R::dispense('user');
            $newUser->name = strip_tags(trim($user));
            $newUser->password = strip_tags(trim($password));
            $newUser->email = strip_tags(trim($email));

            $id = R::store($newUser);
        } else {
            throw new UserAlreadyExistsException("Username $user already exists, please select another or log in.");
        }
    }

    /**
     * Checks to see if the user name already exists, as part of createUser.
     * @param $user
     * @return bool
     */
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

    /**
     * Testing module -- dumps all users to the page.
     */
    static function dumpUsers()
    {
        $users = R::getAll('SELECT * FROM user');

        foreach ($users as $u) {
            var_dump($u);
            echo "<br>";
        }
    }

    /**
     * Testing module -- dumps all notes to the page.
     */
    static function dumpNotes()
    {
        $users = R::getAll('SELECT * FROM note');

        foreach ($users as $u) {
            var_dump($u);
            echo "<br>";
        }
    }

    /**
     * Testing module -- dumps all user/note connections to the page.
     */
    static function dumpUserNotes()
    {
        $users = R::getAll('SELECT * FROM usernotes');

        foreach ($users as $u) {
            var_dump($u);
            echo "<br>";
        }
    }

    /**
     * Redirects back to the index page with a specific message.
     * @param $action
     * @param $msg
     */
    static function redirectIndex($action, $msg)
    {
        header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/' . "index.php?action=" . $action . "&msg=" . $msg);
    }

    /**
     * Checks to see if the user is logged in or not.
     * @return bool
     */
    static function checkForLogIn()
    {
        if (isset($_SESSION['username'])) {
            return true;
        }

        return false;
    }
}