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
//R::debug(TRUE);

try {
    $news = ArmyDB::retrieveRecentNews();

    foreach ($news as $newsitem) {
        var_dump($newsitem);
    }


}
catch (Exception $e) {
    echo $e->getMessage();
}


