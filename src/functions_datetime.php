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


function fn_dateiso ($datein) {

  /*

  Converts incoming date to YYYY-MM-DD (ISO 8601 basic format) regardless of whether date was entered with a datepicker or text (for Safari and IE)

  $datein = incoming date in any format

  */

  return date('Y-m-d',strtotime($datein));

}

 

function fn_mysqldate_mdy($val) {

  // converts iso date (YYYY-MM-DD) to m/d/yyyy string

  if(!empty($val)) {
    $arr=trim($val);
    $arr=explode("-",$val);
    $arr[0]=substr($arr[0],2);
    $arr[1]=(int)$arr[1];
    $arr[2]=(int)$arr[2];
    return ($arr[1].'/'.$arr[2].'/'.$arr[0]);
  } else {
    return ("");
  }
}




function fn_nextweekday($dateObject) {
  /*  
      takes a date in iso format and returns the next mon-fri weekday date in iso format.  
      Eg if date is 2023-10-13 (a friday), it returns 2023-10-13 (keeps date the same, just converts to iso).
      If date is 2023-10-14 (a saturday), it returns 2023-10-16 (the following monday).
  */

  $dn = date('N', strtotime($dateObject));
  if($dn  == 6) $dateObject = date('Y-m-d', strtotime($dateObject . " +2 day"));
  if($dn  == 7) $dateObject = date('Y-m-d', strtotime($dateObject . " +1 day"));
  return $dateObject;

}


function fn_dateinterval($months, DateTime $dateObject) 
    {
        $next = new DateTime($dateObject->format('Y-m-d'));
        $next->modify('last day of +'.$months.' month');

        if($dateObject->format('d') > $next->format('d')) {
            return $dateObject->diff($next);
        } else {
            return new DateInterval('P'.$months.'M');
        }
    }

function fn_addmonths($d1, $months)
    {
        $date = new DateTime($d1);

        // call second function to add the months
        $newDate = $date->add(fn_dateinterval($months, $date));

        // goes back 1 day from date, remove if you want same day of month
#        $newDate->sub(new DateInterval('P1D')); 

        //formats final date to Y-m-d form
        $dateReturned = $newDate->format('Y-m-d'); 

        return $dateReturned;
    }


?>