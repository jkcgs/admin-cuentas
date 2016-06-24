<?php
define("INCLUDED", 1);

require 'vendor/autoload.php';
include "include/config.php";
include "include/auth.php";
include "include/db.php";
include "include/functions.php";

include "views/main.php";
