

<?php
ini_set('display_errors', 1);


session_start();
include('../../includes/common.php');

 
 
$con = connect();

 
$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
 
$colour_data_arr = explode('__',"BLACK__WHITE__PINK__ORANGE 17-2255__AVG__RED__Dark Cayan__DARK__9637__CORAL RED__BLUE__GREY__D.BLUE__OFF WHITE 11-105__8005__1176 YELLOW__3108 RED__5521 BLUE__5839 NAVY__6624 TURQUOISE__9999 BLACK__OPTIC WHITE__B25__H101CY__POWERFUL BLUE__SUNSET GLOW__G.MELL__3400 RED__7040 GREEN__BLACK IRIS/BRIGHT WHITE STRIPE__PINK OTHER 50-302 NEON__NAVY__0100 WHITE__2102 ORANGE__3300 RED__4564 PINK__6617 TURQUOISE__9890 DK.GREY__HOT PINK__YELLOW__4933 PURPLE__5884 NAVY__OFF WHITE [11-1-5]__6617 TERQUISE__89-111__07-108__4514 PINK__6242 TURQ__6624 DK. TURQ__9898 DK. GREY__2061__5450__6106__7137__AVERAGE__3123 [RED]__0100, 9999__AVAILABLE__DAY FUCHIA-1023__DAY FUCHIA-1023, VIOLET-4221, PETROL-5249__VIOLET-4221__PETROL-5249__N FUCHIA-1020__NAVY-5171__OFFWHITE-8004__WHITE-9000__BLACK-9300__BLACK/WHITE STRIPE__2000__197 BLACK/WHITE__DEEP PEACOCK BLUE__LT. GREY MELANGE/B.WHITE [Y/D]__BLACK/B.WHITE [Y/D]__LT.GREY MELANGE/B.WHITE__167 CINDER__258 HIBISCUS__675 DENIM BLUE__547 DEEP PEACOCK BLUE__LT. GREY MELANGE/B.WHITE__6519__3474__6116__TBC__GREEN__FUSCHIA__52-310__43-213__25-202__HB863__H105CY__76-225__63-115__56-207__76-121__89-217__REZA1__REZA2");//

foreach($colour_data_arr as $colourText){
	$color_id = return_id_lib_common_new( $colourText, $color_library, "lib_color", "id,color_name","158");
	echo $color_id."<br>";
}


function _sleep($file_name=''){
	$file_path = "tmp_".$file_name."_sleep.txt";
	$users = file($file_path);
	$user = $users[0]+1;

	$openFile = fopen($file_path , "w");
	fputs($openFile , $user);
	fclose($openFile);
	sleep($user);
}

function _wake($file_name=''){
	$file_path = "tmp_".$file_name."_sleep.txt";
	$users = file($file_path);
	$user = ($users[0]>0)?($users[0]-1):0;

	$openFile = fopen($file_path , "w");
	fputs($openFile , $user);
	fclose($openFile);
}

function return_id_lib_common_new($field_text, $library_array, $table_name, $table_field="", $entry_form=0) {

	global $con ;
	global $pc_date_time;
	$file = $table_name.".txt";

    $field_text = trim(str_replace(array("'",'"',"(",")"),array("","","[","]"), strtoupper(strip_tags($field_text))));
    $entry_form = str_replace("'","",$entry_form);
    $table_field = str_replace("'","",$table_field);

	$return_id = 0;

    if (empty($field_text)) {return $return_id;}
    else{

        $tmp_library_array = array_flip($library_array);
		if (isset($_COOKIE[$table_name])) {
			$cookieData_arr = json_decode($_COOKIE[$table_name],true);
		}

		if($tmp_library_array[$field_text] > 0){$return_id = $tmp_library_array[$field_text];}
		else if($cookieData_arr[$field_text] > 0){$return_id = $cookieData_arr[$field_text];}
		else{
		
			//Text file check crearte..................................
			if (!file_exists($file)) {
				$myfile = fopen($file, "a") or die("Unable to create!");
				fclose($myfile);
			}
			//..............................................end;


			// One second delay if multi user insert in same time...........
			if(date(time() - filemtime($table_name.'_sleep.txt')) < 2){_sleep($table_name);}
			$sleepFile = fopen($table_name.'_sleep.txt', "w") or die("Unable to create!");
			fclose($sleepFile);
			//....................................................end;
			

			//Text file read..................................
			$textFileDataArr = array();
			$myfile = fopen($file, "r") or die("Unable to open file!");
			while(!feof($myfile)) {
				$dataIdText = fgets($myfile);
				list($data_id,$data_text) = explode('[_________]',$dataIdText);
				if($data_id){$textFileDataArr[trim($data_text)] = $data_id;}
			}
			fclose($myfile);
			//...................................................end;

			if($textFileDataArr[$field_text] > 0){$return_id = $textFileDataArr[$field_text];}
			else{
				$table_field = $table_field . ",entry_form,insert_date,inserted_by";
				$mst_id = return_next_id("id", "$table_name", 1);
				$data_fld = "(" . $mst_id . ",'" . $field_text . "','" . $entry_form . "','" . $pc_date_time . "','" . $_SESSION['logic_erp']['user_id'] . "')";

				$rID = sql_insert($table_name, $table_field, $data_fld, 0);
				//echo $rID; oci_rollback($con); die;

				if($rID == 1){
					oci_commit($con);
					$return_id = $mst_id;
					$library_array[$mst_id] = $field_text;

					//Write text file.................................
					$myfile = fopen($file, "a") or die("Unable to open file!");
					fwrite($myfile, $mst_id.'[_________]'.$field_text."\n");
					fclose($myfile);
					//.....................end;
					
					//set cookie...............................
					$cookieData_arr[$field_text] = $mst_id;
					if(count($cookieData_arr)){
						setcookie($table_name,json_encode($cookieData_arr), time() + (86400 * 30), "/");
					}
					//..........................end;

				}
				else { oci_rollback($con); }
				_wake($table_name);
			}

		}
		
		return $return_id;

    }
}


 
?>