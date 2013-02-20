<?php
// Set search path for modules
$searchPaths = array('../lib', '../lib/modules');
ini_set("include_path", join(":", $searchPaths) . ":" . ini_get("include_path"));

require_once("uf/router.php");

// Setup a router for our main route file
$r = new router("../config/route.json");
?>
