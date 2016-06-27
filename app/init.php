<?php defined("INCLUDED") or die("nel");

include "config/config.php";
include "includes/auth.php";
include "includes/db.php";
include "includes/functions.php";

if(isset($_GET['action'])) {
	require('controller/actions.php');
	exit;
}

if(isset($_GET['ext'])) {
	require('controller/external.php');
	exit;
}

require('controller/main.php');