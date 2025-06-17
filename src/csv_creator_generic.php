<?php

 /*   
    Copyright (C) 2022-2023 Peter J. Davidson

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

require '../sessioncheck.inc';
require '../conn.php';


// set $testing to 1 to test, 0 for production

$testing=0;

if($testing==1) include 'header.html';


$statement=$_POST['csvstatement'];
$outputfile=$_POST['csvoutputfile'];

if($testing==1) {
    echo "<p>input: $statement</p>";
    echo "<p>output: $outputfile</p>";
    include 'footer_plain.html';
    exit();
}

dumptocsv($statement,$outputfile);

mysqli_close($mysqli_link);



########  now it's all just functions


function dumptocsv($query,$outputfilename) {

    global $mysqli_link;

    $result = mysqli_query($mysqli_link,$query);

    if(!mysqli_query($mysqli_link,$query)) {
      mysqli_close($mysqli_link);
      include('header.html');
      echo '<br><br><p align="center">'.$static_mainerrormessage.'</p><br><br>';
      echo '<p>A mysql statement died in '.basename(__FILE__).'.</p><br>';
      echo "<p> $query</p>";
      include('footer_plain.html');
      exit();
    } 

    if (!$result) die('Couldn\'t fetch records');
    $num_fields = mysqli_num_fields($result);
    $headers = array();
    for ($i = 0; $i < $num_fields; $i++) {
        $headers[] = mysqli_field_name($result , $i);
    }
    $fp = fopen('php://output', 'w');
    if ($fp && $result) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="'.$outputfilename.'"');
        header('Pragma: no-cache');
        header('Expires: 0');
        fputcsv($fp, $headers);
        while ($row = $result->fetch_array(MYSQLI_NUM)) {
            fputcsv($fp, array_values($row));
        }
        die;
    }

}

function mysqli_field_name($result, $field_offset) {
    $properties = mysqli_fetch_field_direct($result, $field_offset);
    return is_object($properties) ? $properties->name : null;
}



?>
