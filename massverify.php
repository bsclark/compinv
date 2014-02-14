<?php
/*
  This file will display the contents of the CSV file
  so the data can be looked at before uploading into 
  the database.
*/

/* Artifacts
	$bar = ucwords(strtolower($data[$c])); // Hello World!
*/

include "./Parts/Main.php";

session_start();

function _CSV_to_Array ($target_path, $p_or_r) {
	//Dumps a CSV file into an array
    if (($handle = fopen($target_path, "r")) !== FALSE) {
		//Gets header names from CSV file
		$fields = fgetcsv($handle, 1000, ",");
		
        while (($line = fgetcsv($handle, 1000, ",")) !== FALSE) {
			$items[] = $line;
		}  
		
		$x = 0;
		$y = 0;
		
		foreach ($items as $i) {
			foreach ($fields as $z) {
				$csvarray[$x][$z] = $i[$y];
				$y++;
			}
			$y = 0;
			$x++;
		}
    }
	
	if (isset($p_or_r)) {
	    // Print the contents of the multidimensional array.
		echo "<pre>";	
		print_r($csvarray);
		echo "</pre>";
		return $csvarray;
	} else {
		return $csvarray;
	}

	// CSV file cleanup
	fclose($handle);
	unlink($target_path);
}

function _Create_SQL($_tosql, $p_or_r) {
// Create the SQL to upload into the database
	$sql_biuld = "";
	$sql_array_no = 0;
	foreach	($_tosql as $key => $value) {
		$sqla = "INSERT INTO inv_main (";
		$sqlb = "";
		$z = 0;
		foreach ($value as $iKey => $iValue) {			
			$sqla = $sqla . $iKey . ", ";
			if ($z == 0 ) {
				$sqlb = $sqlb . "'" . $iValue . "'"; 
				$z++;
			} else {
				$sqlb = $sqlb . ", '" . $iValue . "'";
			}
		}
				
		//$sqla = rtrim($sqla, ", ");
		$sqla = $sqla .  "inventoryupdate) VALUES (";
		$sqlb = $sqlb . ", '" . date("Y-m-d") . "')";
		
		if (isset($p_or_r)) {
			echo "<pre>" . $sqla . $sqlb . "</pre>\n";
		} else {
			$sql_biuld[$sql_array_no] = $sqla . $sqlb;
			$sql_array_no++;
		}
	}
	return $sql_biuld;
}

function _Create_Table($_totable) {
// Populate array into table to ensure data is going to be imported correctly
	echo "<table class=\"serverlisting\">\n";

	foreach	($_totable as $key => $h_value) {
		$theader = "<tr>";
		
		foreach (array_keys($h_value) as $h_nam => $h_val) {
				$theader = $theader . "<th>" . $h_val . "</th>";
		}
		$theader = $theader . "</tr>";
		break;
	}
	
	$tdata = "";
	$rowcolor = 0;
	foreach	($_totable as $key => $r_value) {
		if ($rowcolor == "1") {
			$tdata = $tdata . "<tr class=\"alt\">";
			$rowcolor = 0;
		} else {
			$tdata = $tdata . "<tr>";
			$rowcolor = 1;
		}		
		
		foreach ($r_value as $r_nam => $r_val) {
			$tdata = $tdata . "<td>" . $r_val . "</td>";
		}
		$tdata = $tdata . "</tr>\n";
	}
	
	echo $theader . "\n";
	echo $tdata;
	echo "</table>\n";
}

_page_top("Base", "");

echo "<h2>MASS Upload Verify</h2>\n";

if(is_file($_SESSION['$target_file'])) {
	echo "Please check the following to make sure all is correct. If so, click Next to add to database.";
	echo "<hr />\n";
	_Create_Table(_CSV_to_Array(($_SESSION['$target_file']), NULL));
	$_SESSION['createdsql'] = _Create_SQL(_CSV_to_Array(($_SESSION['$target_file']), NULL), NULL);
	
	echo "<br />\n";
	echo "<form action=\"./upload.php\">\n";	
	echo "<input type=\"submit\" name=\"next\" value=\"Next\">\n";
	echo "</form>\n";	
}

_page_bot();
?>
