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


function fn_datazip ($stmnt, $fname) {

    /*
    Create zipfile of folder with data dump from statements and a pdf data dictionary

    $stmnt = array of statements
    $fname = name of folder in zipfile and basename of zipfile

    */

    require '../vendor/autoload.php'; ##### NOTE: requires php-fpdf to be installed via composer

    include (__DIR__.'/../../checknevada.org.db.inc');
    include (__DIR__.'/../statics.inc');

    $dblink = mysqli_connect($static_hostName,$static_username,$static_password,$static_databaseName);


    // set folder name and zipfile name

    $foldername = date('Y-m-d')."_".$fname;
    $zip_name = $foldername.".zip";


    // create folder in /tmp, deleting if it already exists [note it's actually in /tmp/systemd-private-[number]-apache2.service-[numbers]/tmp]

    $fullfoldername = $static_data_filepath.'/tempdata/'.$foldername;
  
    if (is_dir($fullfoldername)) { # fullfolder exists already, remove it first

        array_map('unlink', glob("$fullfoldername/*.*")); # remove any files in directory first
        rmdir($fullfoldername);

    }

    mkdir($fullfoldername, 0777, true);



    // start a README

    $readmeoutputfile = $fullfoldername."/README.txt";

    $readme = fopen($readmeoutputfile, "a");

    fwrite($readme, "Contents of this folder:\n\n");



    // loop through statements & generate data dictionary & data files

    foreach($stmnt as $query) {

        // extract table name

        $temparr = preg_split("/from /i", $query);
        $temptail = $temparr[1];
        $temparr2 = explode(' ',$temptail);
        $tablename = $temparr2[0];


        // Add file descriptors to README

        $readmepdfline = date('Y-m-d')."_".$tablename."_data_dictionary.pdf: Data dictionary for ".$tablename." table\n";
        $tempout = fwrite($readme, $readmepdfline);

        $readmecsvline = date('Y-m-d')."_".$tablename."_data.csv: Data from ".$tablename." table in CSV format as of ".date('Y-m-d')."\n\n";
        $tempout = fwrite($readme, $readmecsvline);



        // create datadictionary pdf from table name and put in tmp folder

        $outputfile = $fullfoldername."/".date('Y-m-d')."_".$tablename."_data_dictionary.pdf";

        $pdfquery = "SELECT `column_name`,`column_type`, `column_comment` FROM `information_schema`.`COLUMNS` WHERE `table_name` = '$tablename';";
        $pdfresult=mysqli_query($dblink, $pdfquery);

        $pdf=new FPDF();
        $pdf->AddPage('L','Letter');
        $pdf->SetFont('Arial','B',10);

        $headerstring=$tablename." table data dictionary.  Generated ".date('Y-m-d');
        $pdf->Cell(0,12,$headerstring,0);
        $pdf->Ln();

        $pdf->Cell(40,12,"Variable name",1);
        $pdf->Cell(40,12,"SQL variable type",1);
        $pdf->Cell(0,12,"Variable purpose",1);

        $pdf->SetFont('Arial','',10);

        foreach($pdfresult as $row) {
            $pdf->Ln();
            $pdf->Cell(40,12,$row['column_name'],1);
            $pdf->Cell(40,12,$row['column_type'],1);
            $pdf->Cell(0,12,$row['column_comment'],1);
        }

        mysqli_free_result($pdfresult);

        $pdf->Output('F',$outputfile);



        // create csv from statement and put in tmp folder

        $outputfile = $fullfoldername."/".date('Y-m-d')."_".$tablename."_data.csv";

        $result = mysqli_query($dblink, $query);

        $num_fields = mysqli_num_fields($result);

        $headers = array();
        for ($i = 0; $i < $num_fields; $i++) {
            $headers[] = fn_mysqli_field_name($result , $i);
        }

        $fp = fopen($outputfile, 'w');

        if ($fp && $result) {

            fputcsv($fp, $headers);
            while ($row = $result->fetch_array(MYSQLI_NUM)) {
                fputcsv($fp, array_values($row));
            }

        }

        fclose($fp);

        mysqli_free_result($result);

    }

    mysqli_close($dblink);


    // finish README

    fclose($readme);


    // zip tmp folder 

    chdir( $static_data_filepath.'/tempdata' );

    $execstring = "zip -r $zip_name $foldername/";

    exec( $execstring );


    // push zipfile to user

    $size = filesize($zip_name);

    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename=' . $zip_name);
    header('Content-Length: ' . $size);
    ob_end_clean();
    flush();
    readfile($zip_name);


    // delete zipfile and all files 

    unlink($zip_name);

    array_map('unlink', glob("$foldername/*.*"));
    rmdir($foldername);
    
}






########  function needed for csv header generation

function fn_mysqli_field_name($result, $field_offset) {
    $properties = mysqli_fetch_field_direct($result, $field_offset);
    return is_object($properties) ? $properties->name : null;
}



?>