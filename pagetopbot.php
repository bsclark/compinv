<?php
function ptop() {
  global $pagename;

  echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"DTD/xhtml1-transitional.dtd\">\n";
  echo "<html>\n";
  echo "<head>\n";
  echo "<title>Server List - " . $pagename . " Admin</title>\n";
  echo "<link href=\"MainStyle.css\" rel=\"stylesheet\" type=\"text/css\" />\n";
  echo "</head>\n";
  echo "\n<body>\n";
}

function pbottom() {
  echo "<br>\n";
  echo "<hr>\n";
  echo "<p class=\"copy\" align=\"center\">\n";
  echo "GPLv2\n";
  echo "</p>\n";
  echo "\n</body>\n";
  echo "</html>\n";
}
?>
