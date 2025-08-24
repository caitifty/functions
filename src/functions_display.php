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


function fn_ordinal($number) {

  /*

  Provides ordinal text versions of numbers from integer input

  e.g., 2 -> 2nd; 3 -> 3rd etc

  $number = input integer

  */


  $suffix = 'th';
  if (!in_array(($number % 100), [11, 12, 13])) {
      switch ($number % 10) {
          case 1:  $suffix = 'st'; break;
          case 2:  $suffix = 'nd'; break;
          case 3:  $suffix = 'rd'; break;
      }
  }

  return $number . $suffix;
  
}


function fn_footer($test, $next, $type = "plain") {

  /*

  Provides a neatened footer

  $test = 0 for production, 1 or above for testing
  $next = next location ("" for just footer display)
  $type = full | plain (full = with menu, plain = just footer [default if empty])

  */

#  global $static_hostName;

  if($test > 0) {

    if($next != "") echo "<br><p>Next location: $next</p>";
    echo '<br></body></html>';
 
  } else {

    if($next != "") {

      header("Location: $next");

    } else {

      switch ($type) {

        case "full":
          echo '<br><p align="right">';
          if($_ENV['static_hostName'] == "localhost") echo '<span style="color:Tomato">localhost</span> &nbsp;';
          if(dirname($_SERVER['SCRIPT_FILENAME']) == "/var/www/".$_ENV['static_sitename']) {
            echo '<a href="menu.php">Menu</a> 
                  <a href="users/users_preferences.php">User preferences</a> 
                  <a href="logout.php">Logout</a> &nbsp; </p>';
          } else {
            echo '<a href="../menu.php">Menu</a> 
                  <a href="../users/users_preferences.php">User preferences</a> 
                  <a href="../logout.php">Logout</a> &nbsp; </p>';
          }
          echo '<br></body></html>';
          break;

        default:
          echo '<br></body></html>';

      }

    }

  }

}


function fn_question_radio_ynd_full($txt, $var, $on, $onloc, $id) {

  /*
  Returns yes/no/dk question if id=0 or previous question had on=id
  $txt = question text
  $var = variable name
  $on = javascript onclick depth 0 = no javascript, > 0 gives onclick depth
  $onloc = which answer triggers on vs off (options y|k|d|"" where "" used for on=0
  $id = previous triggering question onclick depth (0 = trigger every time)
  */

  include '../strings_'.$_SESSION['language'].'.php';
  /*
  $string201 = "Yes";
  $string202 = "No";
  $string35 = "Don't know";
  */

  if($id > 0) {
    $idtext = fn_javadisplay_id($id);
  } else {
    $idtext = array ("","","");
  }

  echo '<tr'.$idtext[0].'><td style="font-weight:bold">'.$txt.'</td></tr>';
  echo '<tr'.$idtext[1].'><td>';
  if($on > 0) {
    switch ($onloc) {
      case "y":
        $ontext_y = ' onClick = "on'.$on.'();"';
        $ontext_n = ' onClick = "off'.$on.'();"';
        $ontext_d = ' onClick = "off'.$on.'();"';
        break;
      case "n":
        $ontext_y = ' onClick = "off'.$on.'();"';
        $ontext_n = ' onClick = "on'.$on.'();"';
        $ontext_d = ' onClick = "off'.$on.'();"';
        break;
      case "d":
        $ontext_y = ' onClick = "off'.$on.'();"';
        $ontext_n = ' onClick = "off'.$on.'();"';
        $ontext_d = ' onClick = "on'.$on.'();"';
        break;
      default:
        $ontext_y = "";
        $ontext_n = "";
        $ontext_d = "";
        break;
    }
  } else {
        $ontext_y = "";
        $ontext_n = "";
        $ontext_d = "";
  }
  echo '<label class = "container">'.$string201.'<input type = "radio" name = "'.$var.'" value = "Yes"'.$ontext_y.'><span class = "checkmark"></span></label>';
  echo '<label class = "container">'.$string202.'<input type = "radio"  name = "'.$var.'" value = "No"'.$ontext_n.'><span class = "checkmark"></span></label>';
  echo '<label class = "container">'.$string35.'<input type = "radio"  name = "'.$var.'" value = "Dk"'.$ontext_d.'><span class = "checkmark"></span></label>';
  echo '</td></tr>';
  echo '<tr'.$idtext[2].'><td>&nbsp;</td></tr>';
}


function fn_question_radio_yn_full($txt, $var, $on, $onloc, $id) {

  /*
  Returns yes/no question if id=0 or previous question had on=id   ## Some questions 'Don't know' not appropriate option
  $txt = question text
  $var = variable name
  $on = javascript onclick depth 0 = no javascript, > 0 gives onclick depth
  $onloc = which answer triggers on vs off (options y|k|d|"" where "" used for on=0
  $id = previous triggering question onclick depth (0 = trigger every time)
  */

  include '../strings_'.$_SESSION['language'].'.php';
  /*
  $string201 = "Yes";
  $string202 = "No";
  */

  if($id > 0) {
    $idtext = fn_javadisplay_id($id);
  } else {
    $idtext = array ("","","");
  }

  
  echo '<tr'.$idtext[0].'><td style="font-weight:bold">'.$txt.'</td></tr>';
  echo '<tr'.$idtext[1].'><td>';
  if($on > 0) {
    switch ($onloc) {
      case "y":
        $ontext_y = ' onClick = "on'.$on.'();"';
        $ontext_n = ' onClick = "off'.$on.'();"';
        break;
      case "n":
        $ontext_y = ' onClick = "off'.$on.'();"';
        $ontext_n = ' onClick = "on'.$on.'();"';
        break;
      case "d":
        $ontext_y = ' onClick = "off'.$on.'();"';
        $ontext_n = ' onClick = "off'.$on.'();"';
        break;
      default:
        $ontext_y = "";
        $ontext_n = "";
        break;
    }
  } else {
        $ontext_y = "";
        $ontext_n = "";
  }
  echo '<label class = "container">'.$string201.'<input type = "radio" name = "'.$var.'" value = "Yes"'.$ontext_y.'><span class = "checkmark"></span></label>';
  echo '<label class = "container">'.$string202.'<input type = "radio"  name = "'.$var.'" value = "No"'.$ontext_n.'><span class = "checkmark"></span></label>';
  echo '</td></tr>';
  echo '<tr'.$idtext[2].'><td>&nbsp;</td></tr>';
}


function fn_question_radio_full($txt, $var, $res, $on, $id) {

  /*
  Displays a question and a set of radiobutton responses if id=0 or previous question had on=id
  $txt = question text
  $var = variable name
  $res = array of responses (displayed text, stored value if selected, 0|1 to indicate if this response is onclick off (0) or on (1))
  $on = javascript onclick depth 0 = no javascript, > 0 gives onclick depth
  $id = previous triggering question onclick depth (0 = trigger every time)
  */  

  include '../strings_'.$_SESSION['language'].'.php';

  if($id > 0) {
    $idtext = fn_javadisplay_id($id);
  } else {
    $idtext = array ("","","");
  }


  echo '<tr'.$idtext[0].'><td style="font-weight:bold">'.$txt.'</td></tr>';
  echo '<tr'.$idtext[1].'>';
      echo '<td>';
      foreach($res as $label) {
        if($on > 0) {
          if($label[2] == 1) {
            $click = ' onClick = "on'.$on.'();"';
          } else {
            $click = ' onClick = "off'.$on.'();"';
          }
          echo '<label class="container">'.$label[0].'<input type="radio" name="'.$var.'" value="'.$label[1].'"'.$click.'><span class="checkmark"></span></label>';
        } else {
          echo '<label class="container">'.$label[0].'<input type="radio" name="'.$var.'" value="'.$label[1].'"><span class="checkmark"></span></label>';
        }
      }
      echo '</td>';
  echo '</tr>';
  echo '<tr'.$idtext[2].'><td>&nbsp;</td></tr>';
}


//----fn_question_radio_yn----//

####### DEPRECATED - USE fn_question_radio_yn_full or fn_question_radio_ynd_full

/*
To convert fn_question_radio_yn to fn_question_radio_yn_full|fn_question_radio_ynd_full
fn_question_radio_yn ($displaytext, $variable, 1);
fn_question_radio_yn_full ($displaytext, $variable, 1, "y", 0);
ie add "y", 0 to end
*/

// returns yes/no question, with question text = $text, variable name = $var.  $on  = 0 means no javascript, $on>0 gives javascript onclick of depth = $on

function fn_question_radio_yn($txt, $var, $on) {
  include '../strings_'.$_SESSION['language'].'.php';
  /*
  $string201 = "Yes";
  $string202 = "No";
  */

  echo '<tr><td align = "center" style="font-weight:bold">'.$txt.'</td></tr>';
  echo '<tr><td>';
  if($on > 0) {
    $ontext_on = ' onClick = "on'.$on.'();"';
    $ontext_off = ' onClick = "off'.$on.'();"';
  } else {
    $ontext_on = "";
    $ontext_off = "";
  }
  echo '<label class = "container">'.$string201.'<input type = "radio" name = "'.$var.'" value = "Yes"'.$ontext_on.'><span class = "checkmark"></span></label>';
  echo '<label class = "container">'.$string202.'<input type = "radio"  name = "'.$var.'" value = "No"'.$ontext_off.'><span class = "checkmark"></span></label>';
  echo '</td></tr>';
  echo '<tr><td>&nbsp;</td></tr>';
}



function fn_introtext ($txt) {

  /* returns display of text */

  echo '<tr><td align="center" style="font-weight:bold">'.$txt.'</td></tr>';
  echo '<tr><td>&nbsp;</td></tr>';

}

function fn_question_text_javadisplay($txt, $var, $id) {

  /*
  Returns text input field if id = 0 or if id > 0 and previous question had on = id

  $txt = question text
  $var = variable
  $id = previous triggering question onclick depth (0 = trigger every time)
  */

  if($id > 0) {
    $idtext = fn_javadisplay_id($id);
  } else {
    $idtext = array ("","","","");
  }

  echo '<tr'.$idtext[0].'><td style="font-weight:bold">'.$txt.'</td></tr>';
  echo '<tr'.$idtext[1].'><td>';
  echo '<tr'.$idtext[2].'><td><input size="16" type="text" name="'.$var.'"></td></tr>';
  echo '<tr'.$idtext[3].'><td>&nbsp;</td></tr>';
}


function fn_javadisplay_id($id) {
  switch($id){
    case 0:
      $snippet = "";
      break;
    case 1:
      $snippet = "hidethis";
      break;
    case 2:
      $snippet = "hidethat";
      break;
    case 3:
      $snippet = "hidetheother";
      break;
    case 4:
      $snippet = "hideanother";
      break;
    case 5:
      $snippet = "hideonemore";
      break;
    case 6:
      $snippet = "hidejustonemore";
      break;
  }

  $on0 = '';
  $on1 = ' id="'.$snippet.'1" style="display:none;"';
  $on2 = ' id="'.$snippet.'2" style="display:none;"';
  $on3 = ' id="'.$snippet.'3" style="display:none;"';
  $on4 = ' id="'.$snippet.'4" style="display:none;"';
  $on5 = ' id="'.$snippet.'5" style="display:none;"';
  $on6 = ' id="'.$snippet.'6" style="display:none;"';
  $on7 = ' id="'.$snippet.'7" style="display:none;"';
  $on8 = ' id="'.$snippet.'8" style="display:none;"';
  $on9 = ' id="'.$snippet.'9" style="display:none;"';

  $output = array($on1, $on2, $on3, $on4, $on5, $on6, $on7, $on8, $on9);
  return $output;
}


function fn_question_checkbox($txt, $var, $id) {

  /*
  Displays list of checkboxes if id=0 or previous question had on=id
  $txt = question text
  $var = array of possible responses
  $id = previous triggering question onclick depth (0 = trigger every time)
  */

  if($id > 0) {
    $idtext = fn_javadisplay_id($id);
  } else {
    $idtext = array ("", "", "");
  }
  echo '<tr'.$idtext[0].'><td style="font-weight:bold">'.$txt.'</td></tr>';
  echo '<tr'.$idtext[1].'>';
      echo '<td>';
      foreach($var as $label) {
          echo '<label class = "containerbox">'.$label[0].'<input type = "checkbox" name = "'.$label[1].'" value = "Yes"><span class = "checkmarkbox"></span></label>';
      }
      echo '</td>';
  echo '</tr>';
  echo '<tr'.$idtext[2].'><td>&nbsp;</td></tr>';
}


function fn_question_checkbox_cother($txt, $var, $othervar, $othervarvalue, $id) {

  /*
  Displays list of checkboxes with an 'other' option if id=0 or previous question had on=id
  $txt = question text
  $var = array of possible responses (variable text string, variable name)
  $othervar = array with label and variable name of 'other' option' (variable text string, variable name)
  $othervarvalue = array with label and variable name for other option value (variable text string, variable name)
  $id = previous triggering question onclick depth (0 = trigger every time)
  */

  $random1 = uniqid();
  $random2 = uniqid();

  if($id > 0) {
    $idtext = fn_javadisplay_id($id);
  } else {
    $idtext = array ("", "", "");
  }

  echo '<script type="text/javascript">
  function myFunction'.$random1.'() {
    // Get the checkbox
    var checkBox = document.getElementById("'.$random1.'");
    // Get the output text
    var text = document.getElementById("'.$random2.'");

    // If the checkbox is checked, display the output text
    if (checkBox.checked == true){
      text.style.display = "block";
    } else {
      text.style.display = "none";
    }
  } 
  </script>';

  echo '<tr'.$idtext[0].'><td style="font-weight:bold">'.$txt.'</td></tr>';
  
  echo '<tr'.$idtext[1].'>';

      echo '<td>';
        foreach($var as $label) {
            echo '<label class="containerbox">'.$label[0].'<input type="checkbox" name="'.$label[1].'" value="Yes"><span class="checkmarkbox"></span></label>';
        }
        echo '<label class="containerbox">'.$othervar[0].'<input type="checkbox" name="'.$othervar[1].'" id="'.$random1.'" value="Yes" onClick="myFunction'.$random1.'();"><span class="checkmarkbox"></span></label>';
        echo '</td>';
 
  echo '</tr>';


  echo '<tr id="'.$random2.'" style="display:none;">';

    echo '<td style="font-weight:bold">'.$othervarvalue[0].'<br>';
    echo '<input type="text" name="'.$othervarvalue[1].'"><br><br></td>';

  echo '</tr>';

  echo '<tr'.$idtext[2].'><td>&nbsp;</td></tr>';
}



function fn_question_radio($txt, $var, $res) {

  ####### DEPRECATED - USE fn_question_radio_full
  /* 
  Displays a question and a set of radiobutton responses
    txt = question text
    var = variable name
    res = array of responses (displayed text, stored value if selected)
  */
  echo '<tr><td style="font-weight:bold">'.$txt.'</td></tr>';
  echo '<tr>';
      echo '<td>';
      foreach($res as $label) {
          echo '<label class="container">'.$label[0].'<input type="radio" name="'.$var.'" value="'.$label[1].'"><span class="checkmark"></span></label>';
      }
      echo '</td>';
  echo '</tr>';
  echo '<tr><td>&nbsp;</td></tr>';
}


?>