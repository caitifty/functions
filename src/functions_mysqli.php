<?php

 /*   
    Copyright (C) 2021-2025 Peter J. Davidson

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published
    by the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program, in the file LICENSE.txt.  If not, see <https://www.gnu.org/licenses/>.
*/


// fn_mysqli_runstatements

function fn_mysqli_runstatements ($test, $tables, $stmnt) {

	/*
	Lock affected tables, run statements, unlock tables
	** Also creates a logfile with all successful sql statements

	$test: $testing (0 or 1)
	$tables = array of tables which will be written to
	$stmnt = array of statements

	*/

	$backfiles = debug_backtrace();
	$backfile = basename($backfiles[0]['file']);

  global $mysqli_link;
  global $static_mainerrormessage;
  global $static_mysqli_logfilepath;


  if($test == 0) {

    $backfiles = debug_backtrace();
    $backfile = basename($backfiles[0]['file']);

    $logfile = $static_mysqli_logfilepath."/".date('Y-m-d')."_mysqli_log";
    $log = fopen($logfile, "a");

    $tablestring = "LOCK TABLES ";
    foreach ($tables as $val) $tablestring = $tablestring . $val . " WRITE, ";
    $tablestring = trim($tablestring, ", ");
    $tablestring = $tablestring . ";";
    mysqli_query($mysqli_link, $tablestring);

    foreach($stmnt as $value) {

      if(!mysqli_query($mysqli_link, $value)) {
        mysqli_query($mysqli_link,"UNLOCK TABLES;");
        mysqli_close($mysqli_link);
        include(__DIR__.'/header.html');
        echo '<br><br><p align="center">'.$static_mainerrormessage.'</p><br><br>';
        echo '<p>A mysql statement died in '.$backfile.'.</p><br>';
        echo "<p>$value</p><br>";
        echo '<br></body></html>';
        exit();

      } else {
      	
      	// write query to logfile

	      if(isset($_SESSION['uname'])) {
	              fwrite($log, time()."|".$_SESSION['uname']."|".$backfile."|".$value."\n");
	      } else {
		      fwrite($log, time()."||".$backfile."|".$value."\n");
	      }
      } 

    }
    mysqli_query($mysqli_link,"UNLOCK TABLES;");
#    mysqli_close($mysqli_link);
    fclose($log);

  } else {

    if(isset($stmnt)) {
      foreach($stmnt as $value) {
        echo "<p>$value</p><br>";
      } 
    }

    echo "<p>fn_mysqli_runstatements function called from: $backfile</p><br>";

  }

}



//----fn_mysqlinullorquote----//

// returns string as NULL if empty and single quoted otherwise (eg for strings)

function fn_mysqlinullorquote($var){
  if($var == "") { 
    $var = "NULL"; 
  } else { 
    $var = "'$var'"; 
  }
  return $var;
}

function fn_mysqlinullquoteescape ($var) {

  // returns string as NULL if empty, else escapes string and returns in single quotes
  global $mysqli_link;
  if($var == "") { 
    $var = "NULL"; 
  } else { 
    $var = "'".mysqli_real_escape_string($mysqli_link, $var)."'";
  }
  return $var;
}

// returns string as 'MISSING' if empty and single quoted otherwise (eg for strings)

function fn_mysqlimissingorquote($var){
  if($var == "") { 
    $var = "'MISSING'"; 
  } else { 
    $var = "'$var'"; 
  }
  return $var;
}


// returns string as 'No' if empty and single quoted otherwise (eg for yes/no input where value = yes)

function fn_mysqlinoorquote($var){
  if($var == "") { 
    $var = "'No'"; 
  } else { 
    $var = "'$var'"; 
  }
  return $var;
}


//----fn_mysqlinull----//

// returns string as NULL if empty and unaltered otherwise (eg for numeric)

function fn_mysqlinull($var){
  if($var == "") $var = "NULL"; 
  return $var;
}


//----fn_mysqliresult----//

// gets a single row result back from a query

function fn_mysqliresult($res,$row = 0,$col = 0){
    if (mysqli_num_rows($res) && $row <= (mysqli_num_rows($res)-1) && $row >= 0){
        mysqli_data_seek($res,$row);
        $resrow = mysqli_fetch_row($res);
        if (isset($resrow[$col])){
            return $resrow[$col];
        }
    }
    return false;
}


?>
