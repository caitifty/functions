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

require '../vendor/autoload.php';  ##### NOTE: requires php-fpdf to be installed via composer


$table=filter_var($_GET['table'], FILTER_SANITIZE_ADD_SLASHES);
$today=date('Y-m-d');
$outputfile=$today."_".$table."_data_dictionary.pdf";


$statement="SELECT `column_name`,`column_type`, `column_comment` FROM `information_schema`.`COLUMNS` WHERE `table_name` = '$table';";
$result=mysqli_query($mysqli_link, $statement);


$pdf=new FPDF();
$pdf->AddPage('L','Letter');
$pdf->SetFont('Arial','B',10);

$headerstring=$table." table data dictionary.  Downloaded ".$today;
$pdf->Cell(0,12,$headerstring,0);
$pdf->Ln();

$pdf->Cell(40,12,"Variable name",1);
$pdf->Cell(40,12,"MySQL variable type",1);
$pdf->Cell(0,12,"Variable purpose",1);

$pdf->SetFont('Arial','',10);

foreach($result as $row) {
    $pdf->Ln();
    $pdf->Cell(40,12,$row['column_name'],1);
    $pdf->Cell(40,12,$row['column_type'],1);
    $pdf->Cell(0,12,$row['column_comment'],1);
}

mysqli_free_result($result);

$pdf->Output('D',$outputfile);


mysqli_close($mysqli_link);



?>