<?php
//Page specific suff
include "./pagetopbot.php";
$pagename = "Server List - Definitions";

//Page header
ptop();

if ($HTTP_GET_VARS["varname"] == "montype"):
  echo "How the system is being monitored. Via what means the system is being monitored.<br>\n";
elseif ($HTTP_GET_VARS["varname"] == "notes"):
  echo "Notes, just notes.<br>\n";
else:
  echo "Unknown Error!\n";
endif;

//Bottom of page
pbottom();
?>
