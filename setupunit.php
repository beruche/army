<?php
/**
 * Created by PhpStorm.
 * User: beruc_000
 * Date: 2015-06-17
 * Time: 4:45 PM
 */

require 'rb.php';

R::setup('sqlite:army.db');

$projectid = 3;

$unit = R::dispense('unit');
$unit->name = "Necron Warriors";
$unit->qty = 10;
$unit->pts = 150;
$unit->status = 1;
$unit->projectid = $projectid;
$unit->notes = 'bang bang bang';
$unit->date_added = R::isoDateTime();

$unitid = R::store($unit);