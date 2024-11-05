<?php 
//Built in Functions //brand_id
if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
	//buyer.................................
	if ($_SESSION['logic_erp']["buyer_id"] == 0) {
		$buyer_cond = "";
	} else if ($_SESSION['logic_erp']["buyer_id"] == "") {
//Check For mysql;
		$buyer_cond = "";
	} else { $buyer_cond = " and buy.id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";}

	//company...........................
	if ($_SESSION['logic_erp']["company_id"] == 0) {
		$company_cond = "";
	} else if ($_SESSION['logic_erp']["company_id"] == "") {
//Check For mysql;
		$company_cond = "";
	} else { $company_cond = " and comp.id in (" . $_SESSION['logic_erp']["company_id"] . ")";}
	
	//Brand.................................
	if ($_SESSION['logic_erp']["brand_id"] == 0) {
		$brand_cond = "";
	} else if ($_SESSION['logic_erp']["brand_id"] == "") {
//Check For mysql;
		$buyer_cond = "";
	} else { $brand_cond = " and brand.id in (" . $_SESSION['logic_erp']["brand_id"] . ")";}

	
} else {
	$buyer_cond = "";
	$company_cond = "";
	$brand_cond = "";
}

// user level buyer/brand/supplier/unit/floor
function set_user_lavel_filtering($tbl_alias, $index_key) {
	if ($_SESSION['logic_erp'][$index_key] && $_SESSION['logic_erp']["data_level_secured"] == 1) {
		return $tbl_alias . " in(" . $_SESSION['logic_erp'][$index_key] . ")";
	} else {
		return "";
	}
}

function custom_file_name($booking, $style, $job, $xtension = 'pdf') {
	$file_name = '';
	if ($style) {
		$file_name = $style;
	}

	if ($file_name) {$under_squre = '_';} else { $under_squre = '';}
	if ($job) {
		$file_name .= $under_squre . $job;
	}

	if ($file_name) {$under_squre = '_';} else { $under_squre = '';}
	if ($booking) {
		$file_name .= $under_squre . $booking;
	}

	if ($booking) {
		$file_name .= '.' . $xtension;
	}

	return str_replace("'", "", $file_name);
}

// Common Used Functions
function csf($data) // checked 3
{
	global $db_type;
	if ($db_type == 0 || $db_type == 1) {
		return strtolower($data);
	} else {
		return strtoupper($data);
	}

}

function return_field_value($field_name, $table_name, $query_cond, $return_fld_name="", $new_conn="") // checked 3
{
	// This function will Return Single or Multiple field value
	// concated with seperator having only one row result
	//Return value:  query result as filed value
	// Uses  single field:: return_field_value("buyer_name", "lib_buyer", "id=1");
	// Uses  multi field:: return_field_value("concate(buyer_name,'_',contact_person)", "lib_buyer", "id=1"); do not use concat
	if ($return_fld_name == "") {
		$return_fld_name = $field_name;
	}

	$queryText = "select " . $field_name . " from " . $table_name . " where " . $query_cond . " ";
	//return $queryText; die;
	$nameArray = sql_select($queryText, '', $new_conn);
	foreach ($nameArray as $result) {
		if ($result[csf($return_fld_name)] != "") {
			return $result[csf($return_fld_name)];
		} else {
			return false;
		}
	}

	//die;
}

// library conversion rate
function set_conversion_rate($cid, $cdate,$company=0) {
	global $db_type;

	if($company=='' || $company==0) $companyCond="";else $companyCond="and company_id=$company";
	if ($cdate == '') {
		if ($db_type == 0) {
			$cdate = date("Y-m-d", time());
		} else {
			$cdate = date("d-M-y", time());
		}

	}
	if ($cid == 1) {
		return "1";
	} else {
		if ($db_type == 0) {
			$cdate = change_date_format($cdate, "yyyy-mm-dd", "-", 1);
		} else {
			$cdate = change_date_format($cdate, "d-M-y", "-", 1);
		}
		//echo $cdate;die;
		$queryText = "select conversion_rate from currency_conversion_rate where con_date<='" . $cdate . "' and currency=$cid and status_active=1 and is_deleted=0 $companyCond order by con_date desc";
		//echo $queryText; die;
		$nameArray = sql_select($queryText, '', $new_conn);
		if (count($nameArray) > 0) {
			foreach ($nameArray as $result) {
				if ($result[csf('conversion_rate')] != "") {
					return $result[csf("conversion_rate")];
				} else {
					return "0";
				}
			}

		} else {
			return "0";
		}

	}
}

function return_next_id($field_name, $table_name, $max_row = 1, $new_conn="") // Checked   3
{
	// This function will Return Last number of Row of table
	// To generate next Id
	// Return value:  number
	// Uses  single field:: return_next_id("id", "lib_buyer", "1");
	$increment = 1;
	$queryText = "select max(" . $field_name . ") as " . $field_name . "  from " . $table_name . " ";
	$nameArray = sql_select($queryText, '', $new_conn);
	foreach ($nameArray as $result) {
		return ($result[csf($field_name)] + $increment);
	}

	//die;
	/*
	$queryText="select mst_id from master_table_id where form_name='$form_name'"  ;
	$nameArray=sql_select( $queryText );
	foreach ($nameArray as $result)
		$new_id=$result[csf($field_name)]+$increment;
		$upd_id=$result[csf($field_name)]+$max_row;
		sql_execute("update master_table_id set mst_id='$upd_id' where form_name='$form_name'");
		return ($result[csf($field_name)]+$increment);
		die;*/

}

/**
 * return_next_id_by_sequence This function is responsible for returning System ID/Sequence number
 * @param  string  $seq_name   			Defining the name of the oracle sequence
 * @param  string  $table_name 			Defining the table name
 * @param  object  $new_conn   			Defining DB connection
 * @param  integer $is_mrr           	Defining whether request is for System ID or Sequence. If 1 then System ID will be returned else sequence no
 * @param  integer $company_id       	Defining Company ID
 * @param  string  $mrr_prefix       	Defining System Prefix
 * @param  integer $entry_form       	Defining Entry form Id
 * @param  integer $year_id          	Defining Four digit year
 * @param  integer $item_category_id 	Defining Item category id. Item category is ignored and set default value to "zero" as entry form is ensured
 * @param  integer $booking_type     	Defining Booking type id
 * @return string                    	System ID/Sequence Number
 */
function return_next_id_by_sequence($seq_name, $table_name, $new_conn, $is_mrr = 0, $company_id = 0, $mrr_prefix = "", $entry_form = 0, $year_id = 0, $item_category_id = 0, $booking_type = 0, $production_type = 0, $embelishment_type = 0, $transfer_criteria = 0) {
	global $db_type;
	$item_category_id = 0; // see function defination
	if ($db_type == 2) {
		if ($is_mrr == 1) {
			$mrr_cond = ($mrr_cond != "") ? " and $mrr_cond" : "";
			//echo "10**";
			$seq_sql = "select f_NextSeq('" . strtoupper($table_name) . "',$company_id,$entry_form,$year_id,$item_category_id,$booking_type,$production_type,$embelishment_type,$transfer_criteria) next_id from dual";
			$seqArray = sql_select($seq_sql, '', '', '', $new_conn);
			//print_r($seqArray); die;
			// Prepare System ID
			$comp_prefix = return_field_value("company_short_name", "lib_company", "id=$company_id");
			$recv_number_prefix = $comp_prefix . "-" . $mrr_prefix . "-" . substr(date("Y", time()), 2, 2) . "-";

			$recv_number = $recv_number_prefix . "" . str_pad($seqArray[0]['NEXT_ID'], 5, '0', STR_PAD_LEFT) . "*" . $recv_number_prefix . "*" . str_pad($seqArray[0]['NEXT_ID'], 5, '0', STR_PAD_LEFT);
			// die;

			return $recv_number;
		} else {
			$seq_sql = "select " . $seq_name . ".nextval as ID from dual";
			$seqArray = sql_select($seq_sql, '', '', '', $new_conn);
			return $seqArray[0]['ID'];
		}
	} else {
		if ($is_mrr == 1) {
			$seq_sql = "select NextVal($is_mrr,'$table_name',$company_id,$entry_form,$year_id,$item_category_id,$booking_type,$production_type,$embelishment_type,$transfer_criteria) as next_val";
			$seqArray = sql_select($seq_sql, '');
			// Prepare System ID
			$comp_prefix = return_field_value("company_short_name", "lib_company", "id=$company_id");
			$recv_number_prefix = $comp_prefix . "-" . $mrr_prefix . "-" . substr(date("Y", time()), 2, 2) . "-";

			$recv_number = $recv_number_prefix . "" . str_pad($seqArray[0]['next_val'], 5, '0', STR_PAD_LEFT) . "*" . $recv_number_prefix . "*" . str_pad($seqArray[0]['next_val'], 5, '0', STR_PAD_LEFT);

			return $recv_number;
		} else {
			$seq_sql = "select NextVal(0,'$table_name',$company_id,$entry_form,$year_id,$item_category_id,$booking_type,$production_type,$embelishment_type,$transfer_criteria) as next_val";
			$seqArray = sql_select($seq_sql, '');
			foreach ($seqArray as $result) {
				return $result[0];
			}

		}
	}
}

// used in some area of marchendising/subcontact
function check_table_status($form_name, $set_lock) // Checked   3
{
	/*return 1;
	die;*/
	global $db_type;
	//global $con ;
	$conn = connect();
	if ($set_lock == 1) {
		//$nameArray=sql_select( "select is_locked, TO_CHAR(set_time,'YYYY-MM-DD HH24:MI:SS') as set_time from table_status_on_transaction where form_name='$form_name'" );
		if ($db_type == 2) {
			$nameArray = sql_select("select is_locked, TO_CHAR(set_time,'YYYY-MM-DD HH24:MI:SS') as set_time from table_status_on_transaction where form_name='$form_name'");
		} else {
			$nameArray = sql_select("select is_locked, set_time as set_time from table_status_on_transaction where form_name='$form_name'");
		}

		if (count($nameArray) > 0) {
			foreach ($nameArray as $row) {
				if ($row[csf("is_locked")] == 0) {
					//execute_query("update table_status_on_transaction set is_locked=1 where form_name='$form_name'");
					$toDate = "to_date('" . date("d-M-Y h:i:s") . "','DD MONTH YYYY HH24:MI:SS')";
					$response = sql_update("table_status_on_transaction", 'is_locked*set_time', "1*$toDate", "form_name", $form_name, 1);
					if ($db_type == 2) {
						if ($response) {
							oci_commit($conn);
						} else {
							oci_rollback($conn);
						}
					}
					return 1;
				} else {
					$currTime = date("Y-m-d h:i:s A");
					$row_time = $row[csf("set_time")] . ' ' . date("A");
					$diff = datediff("n", $row_time, $currTime);

					if ($diff > 3) {
						return 1;
						$response = sql_update("table_status_on_transaction", 'is_locked*set_time', "0*''", "form_name", $form_name, 0);
						if ($db_type == 2) {
							if ($response) {
								oci_commit($conn);
							} else {
								oci_rollback($conn);
							}
						}
						return 1;
					} else {
						return 0;
					}
				}
			}
		} else {
			//$response=execute_query("insert into table_status_on_transaction (form_name, is_locked) values ('$form_name',1)");
			$id = return_next_id("id", "table_status_on_transaction", 1);
			$field_array = "id,form_name,is_locked,set_time";
			$toDate = "to_date('" . date("d-M-Y h:i:s") . "','DD MONTH YYYY HH24:MI:SS')";
			$data_array = "(" . $id . "," . $form_name . ",1," . $toDate . ")";
			//$response=sql_insert("table_status_on_transaction",$field_array,$data_array,0);
			$response = execute_query("insert into table_status_on_transaction ($field_array) values $data_array");
			if ($db_type == 2) {
				if ($response) {
					oci_commit($conn);
				} else {
					oci_rollback($conn);
				}
			}
			return 1;
		}
	} else {
		//$response=execute_query("update table_status_on_transaction set is_locked=0 where form_name='$form_name'");
		$response = sql_update("table_status_on_transaction", 'is_locked*set_time', "0*''", "form_name", $form_name, 0);
		if ($db_type == 2) {
			if ($response) {
				oci_commit($conn);
			} else {
				oci_rollback($conn);
			}
		}
	}
}

function is_duplicate_field($field_name, $table_name, $query_cond) // checkd 3
{
	// This function will Return Last number of Row of table
	// To generate next Id
	// Return value:  true false
	// Uses  single field:: is_duplicate_field("buyer", "lib_buyer", "buyer_name like 'eta'");
	$queryText = "select " . $field_name . " from " . $table_name . " where " . $query_cond . "";
//echo $queryText;
	$nameArray = sql_select($queryText);
	if (count($nameArray) > 0) {
		return 1;
	} else {
		return 0;
	}

	///die;
}

// need to check
function split_string($main_string, $fld_width, $position) {
	//This function will return space seperated string of a
	//joint long string to fit a html table row and column
	// uses  --> echo split_string($main_string, 25)

	if ($position == "") {
		if ($fld_width > 0 and $fld_width < 20) {
			$position = 5;
		} else if ($fld_width > 19 and $fld_width < 40) {
			$position = 15;
		} else if ($fld_width > 39 and $fld_width < 60) {
			$position = 30;
		}

		/*else if ( $fld_width>0 and $fld_width<20) $position=5;
			else if ( $fld_width>0 and $fld_width<20) $position=5;
			else if ( $fld_width>0 and $fld_width<20) $position=5;
		*/
	}
	$len = strlen($main_string);
	if ($len > $position) {
		$whole = chunk_split($main_string, $position);
	} else {
		$whole = $main_string;
	}

	return $whole;
}

function get_button_level_permission($permission) {

/*	Added By		:	Sumon
Date Added		:	14-06-2012

This Function will Return a String with Permission Definition
Uses			: echo get_button_level_permission($permission);
 */

	$permission = explode('_', $permission);
	$perm_str = "";
	if ($permission[0] == 1) {
		$perm_str = "New Entry permission. ";
	} else {
		$insert = "";
	}

	if ($permission[1] == 1) {
		$perm_str .= "Edit permission. ";
	} else {
		$perm_str .= "";
	}

	if ($permission[2] == 1) {
		$perm_str .= "Delete permission. ";
	} else {
		$perm_str .= "";
	}

	if ($permission[3] == 1) {
		$perm_str .= "Approval permission. ";
	} else {
		$perm_str .= "";
	}

	if ($permission[4] == 1) {
		$perm_str .= "Print permission. ";
	} else {
		$perm_str .= "";
	}

	return $perm_str; //die;
}

function change_date_format($date, $new_format="", $new_sep="", $on_save="") {
	//This function will return newly formatted date String
	// uses  --> echo change_date_format($date,"dd-mm-yyyy","/")
	global $db_type;

	if ($new_sep == "") {
		$new_sep = "-";
	}

	if ($new_format == "") {
		$new_format = "dd-mm-yyyy";
	}

	//if ($date=="" || $date=="0000-00-00") $date="0000-00-00";
	if ($date == "0000-00-00" || $date == "" || $date == 0) {
		return "";
	}

	if ($db_type == 2) {
		if ($date == "0000-00-00" || $date == "" || $date == 0) {
			return "";
		}

		if ($on_save == 0) {
			return date("d-m-Y", strtotime($date));
		} else {
			return date("d-M-Y", strtotime($date));
		}

	}
	$year = date("Y", strtotime($date));
	$mon = date("m", strtotime($date));
	$day = date("d", strtotime($date));

	if ($new_format == "yyyy-mm-dd") // yyyy-mm-dd
	{
		$dd = $year . $new_sep . $mon . $new_sep . $day;
	} else if ($new_format == "dd-mm-yyyy") // dd-mm-yyyy
	{
		$dd = $day . $new_sep . $mon . $new_sep . $year;
	}

	if ($db_type == 0 || $db_type == 1) {
		if ($dd == "1970-01-01" || $dd == "01-01-1970" || $dd == "30-11--0001") {
			return "";
		} else {
			return $dd;
		}
	} else
	if ($dd == "1970-01-01" || $dd == "01-01-1970" || $dd == "30-11--0001") {
		return "";
	} else {
		return date("Y-M-d", strtotime($dd));
	}

	//die;
}

function add_time($event_time, $event_length) {
	//This function will return new time after adding a given value with a given time
	// Here $event_time= Time ,$event_length= integer Minutes
	// uses  --> add_time($event_time,50)

	$timestamp = strtotime("$event_time");
	$etime = strtotime("+$event_length minutes", $timestamp);
	$etime = date('H:i:s', $etime);
	return $etime;
}

function add_date($orgDate, $days) {
	$cd = strtotime($orgDate);
	$retDAY = date('Y-m-d', mktime(0, 0, 0, date('m', $cd), date('d', $cd) + $days, date('Y', $cd)));
	return $retDAY;
}

function encrypt($string) {
	// Retrun String after Ecryption
	// Here $string= Given Text to be encrypted,
	$key = "logic_erp_2011_2012_platform";
	$result = '';
	for ($i = 0; $i < strlen($string); $i++) {
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key)) - 1, 1);
		$char = chr(ord($char) + ord($keychar));
		$result .= $char;
	}
	return base64_encode($result);
}


function GetDays($sStartDate, $sEndDate) {
	// Retrun array of days

	$sStartDate = gmdate("Y-m-d", strtotime($sStartDate));
	$sEndDate = gmdate("Y-m-d", strtotime($sEndDate));

	$aDays[] = $sStartDate;

	$sCurrentDate = $sStartDate;
	while ($sCurrentDate < $sEndDate) {
		$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
		$aDays[] = $sCurrentDate;
	}
	return $aDays;
}

// interval: day or month or year or ........
function datediff($interval, $datefrom, $dateto, $using_timestamps = false) {
	if ($datefrom != "" and $dateto != "") {
		if (!$using_timestamps) {
			$datefrom = strtotime($datefrom, 0);
			$dateto = strtotime($dateto, 0);
		}
		$difference = $dateto - $datefrom; // Difference in seconds
		switch ($interval) {
		case 'yyyy': // Number of full years
			$years_difference = floor($difference / 31536000);
			if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom) + $years_difference) > $dateto) {
				$years_difference--;
			}
			if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto) - ($years_difference + 1)) > $datefrom) {
				$years_difference++;
			}
			$datediff = $years_difference;
			break;
		case "q": // Number of full quarters
			$quarters_difference = floor($difference / 8035200);
			while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($quarters_difference * 3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
				$quarters_difference++;
			}
			$quarters_difference--;
			$datediff = $quarters_difference;
			break;
		case "m": // Number of full months
			$months_difference = floor($difference / 2678400);
			while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
				$months_difference++;
			}
			//$months_difference--;
			$datediff = $months_difference;
			break;
		case 'y': // Difference between day numbers
			$datediff = date("z", $dateto) - date("z", $datefrom);
			break;
		case "d": // Number of full days
			$datediff = (floor($difference / 86400) + 1);
			break;
		case "w": // Number of full weekdays
			$days_difference = floor($difference / 86400);
			$weeks_difference = floor($days_difference / 7); // Complete weeks
			$first_day = date("w", $datefrom);
			$days_remainder = floor($days_difference % 7);
			$odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
			if ($odd_days > 7) {
				$days_remainder--;
			}
			// Sunday
			if ($odd_days > 6) {
				$days_remainder--;
			}
			// Saturday
			$datediff = ($weeks_difference * 5) + $days_remainder;
			break;
		case "ww": // Number of full weeks
			$datediff = floor($difference / 604800);
			break;
		case "h": // Number of full hours
			$datediff = floor($difference / 3600);
			break;
		case "n": // Number of full minutes
			$datediff = floor($difference / 60);
			break;
		default: // Number of full seconds (default)
			$datediff = $difference;
			break;
		}
		return $datediff;
	}
}

function number_to_words($number='', $full_unit='', $half_unit="") {
	// This function returns amount in word
	// uses :: echo number_to_words("55555555250", "USD", "CENTS");
	$number = str_replace(",", "", $number);
	if (($number < 0) || ($number > 99999999999)) {
		throw new Exception("Number is out of range");
	}
	$number = explode('.', $number);
	if ($number[1] == "" || $number == 0) {
		$result1 = " " . $full_unit;
		$number = $number[0];
	} else {
		$number[1] = str_pad($number[1], 2, "0", STR_PAD_RIGHT);
		$result1 = " " . $full_unit . " and " . number_to_words($number[1]) . " " . $half_unit;
		$number = $number[0];
	}

	$Cn = floor($number / 10000000); /* Crore (giga) */
	$number -= $Cn * 10000000;

	// $Gn = floor($number / 1000000);  /* Millions (giga) */
	//$number -= $Gn * 1000000;

	$Ln = floor($number / 100000); /* Lacs (giga) */
	$number -= $Ln * 100000;

	$kn = floor($number / 1000); /* Thousands (kilo) */
	$number -= $kn * 1000;
	$Hn = floor($number / 100); /* Hundreds (hecto) */
	$number -= $Hn * 100;
	$Dn = floor($number / 10); /* Tens (deca) */
	$n = $number % 10; /* Ones */

	$result = "";
	if ($Cn) {$result .= number_to_words($Cn) . " Crore ";}

	/* if ($Gn)
		    {  $result .= number_to_words($Gn) . " Million ";  }
	*/
	if ($Ln) {$result .= number_to_words($Ln) . " Lac ";}

	if ($kn) {$result .= (empty($result) ? "" : " ") . number_to_words($kn) . " Thousand ";}

	if ($Hn) {$result .= (empty($result) ? "" : " ") . number_to_words($Hn) . " Hundred ";}

	$ones = array("", "One", "Two", "Three", "Four", "Five", "Six",
		"Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen",
		"Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen",
		"Nineteen");
	$tens = array("", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty",
		"Seventy", "Eighty", "Ninety");

	if ($Dn || $n) {
		/*if (!empty($result)) {
			$result .= " and ";
		}*/

		if ($Dn < 2) {
			$result .= $ones[$Dn * 10 + $n];
		} else {
			$result .= $tens[$Dn];
			if ($n) {
				$result .= "-" . $ones[$n];
			}
		}
	}

	if (empty($result)) {$result = "Zero";}

	return "$result " . " $result1";
}

function return_mrr_number($company, $location, $category, $year, $num_length, $main_query, $str_fld_name, $num_fld_name="", $old_mrr_no="") {
	// This function will Return Last number of Row of table
	// To generate next Id
	//Return value:  number
	// Uses  single field:: return_next_id("id", "lib_buyer", "1");
	//"select string_fld, num_fld from tbl where company and location and param order by num_fld desc limit 1";

	if ($old_mrr_no == "") // Create New MRRR
	{
		$nameArray = sql_select($main_query, 1);
		foreach ($nameArray as $result) {
			$last_mrr[0] = $result[csf($str_fld_name)];
		}

		$last_mrr[1] = $result[csf($num_fld_name)];

		if (count($nameArray) < 1) // New MRR Gen
		{
			if ($company != "") {
				$comp_prefix = return_field_value("company_short_name", "lib_company", "id='$company'");
			}

			if ($location != "") {
				$loc_prefix = return_field_value("location_short_name", " lib_location", "id='$location'");
			}

			if (strlen($year) == 4) {
				$year = substr($year, 2, 2);
			}

			//$company, $location, $category, $year, $num_length,
			if ($comp_prefix != "") {
				$new_mrr .= $comp_prefix . "-";
			}

			if ($loc_prefix != "") {
				$new_mrr .= $loc_prefix . "-";
			}
			if ($category != "") {
				$new_mrr .= $category . "-";
			}

			if ($year != "") {
				$new_mrr .= $year . "-";
			}

			$new_mrr_fin = $new_mrr . "" . str_pad(1, $num_length, 0, STR_PAD_LEFT);
			return $new_mrr_fin . "*" . $new_mrr . "*" . "1"; //die;
		} else {
			if ($last_mrr[0] == "") {
				if ($company != "") {
					$comp_prefix = return_field_value("company_short_name", "lib_company", "id='$company'");
				}

				if ($location != "") {
					$loc_prefix = return_field_value("location_short_name", " lib_location", "id='$location'");
				}

				if (strlen($year) == 4) {
					$year = substr($year, 2, 2);
				}

				//$company, $location, $category, $year, $num_length,
				if ($comp_prefix != "") {
					$new_mrr .= $comp_prefix . "-";
				}

				if ($loc_prefix != "") {
					$new_mrr .= $loc_prefix . "-";
				}
				if ($category != "") {
					$new_mrr .= $category . "-";
				}

				if ($year != "") {
					$new_mrr .= $year . "-";
				}

				$new_mrr_fin = $new_mrr . "" . str_pad(1, $num_length, 0, STR_PAD_LEFT);
				return $new_mrr_fin . "*" . $new_mrr . "*" . "1"; //die;
			} else {
				$num = $last_mrr[1] + 1;
				$new_mrr_fin = $last_mrr[0] . "" . str_pad($num, $num_length, 0, STR_PAD_LEFT);

				return $new_mrr_fin . "*" . $last_mrr[0] . "*" . $num; //die;
			}
		}
	} else /// Check Old Mrr and Get New MRRR for Updates
	{
		$old_mrr_no = explode("-", $old_mrr_no);
		if ($company != "") {
			$comp_prefix = return_field_value("company_short_name", "lib_company", "id='$company'");
		}
		if ($location != "") {
			$loc_prefix = return_field_value("location_short_name", " lib_location", "id='$location'");
		}

		if (strlen($year) == 4) {
			$year = substr($year, 2, 2);
		}

	}

}

function return_library_array___backup($query, $id_fld_name, $data_fld_name, $new_conn) {
	/*$query=explode("where", $query);
	$nameArray=sql_select( $query[0] );*/
	$nameArray = sql_select($query, '', $new_conn);
	foreach ($nameArray as $result) {
		$new_array[$result[csf($id_fld_name)]] = $result[csf($data_fld_name)];
	}
	return $new_array;
}


function return_library_array($query, $id_fld_name, $data_fld_name, $new_conn="") {
	/*$query=explode("where", $query);
	$nameArray=sql_select( $query[0] );*/
	$nameArray = sql_select($query, '', $new_conn);
	foreach ($nameArray as $result) {
		if($result[csf($data_fld_name)]=="MnS") $result[csf($data_fld_name)]="M&S";
		else if($result[csf($data_fld_name)]=="HnM") $result[csf($data_fld_name)]="H&M";
		else if($result[csf($data_fld_name)]=="CnA") $result[csf($data_fld_name)]="C&A";
		$new_array[$result[csf($id_fld_name)]] = $result[csf($data_fld_name)];
	}
	return $new_array;
}




function return_library_autocomplete($query, $data_fld_name, $new_conn="") {

	//$query=explode("where", $query);
	$nameArray = sql_select($query, '', $new_conn);
	foreach ($nameArray as $result) {
		//$str_auto_commplete_data.= '"'.($result[csf($data_fld_name)]).'",';
		$str_auto_commplete_data .= '"' . (str_replace(array('"', "'"), '', $result[csf($data_fld_name)])) . '",';
	}
	return $str_auto_commplete_data;
}

function return_library_autocomplete_fromArr($data_arr, $new_conn="") {

	foreach ($data_arr as $result) {
		$str_auto_commplete_data .= '"' . (str_replace("'", '', $result)) . '",';
	}
	return $str_auto_commplete_data;
}

function load_submit_buttons($permission, $sub_func, $is_update, $is_show_print='', $refresh_function='', $btn_id="", $is_show_approve="") {
	$permission = explode('_', $permission);
	$perm_str = "";
	if ($btn_id == "") {
		$btn_id = 1;
	}

	if ($permission[0] == 1) // Insert
	{
		if ($is_update == 0) //Entry Mode
		{
			$perm_str = '<input type="button" value="Save" name="save" onclick="' . $sub_func . '(0)" style="width:80px" id="save' . $btn_id . '"   class="formbutton"/>&nbsp;&nbsp;';
		} else {
			$perm_str = '<input type="button" value="Save" name="save" onclick="show_button_disable_msg(0)" style="width:80px" id="save' . $btn_id . '"   class="formbutton_disabled"/>&nbsp;&nbsp;';
		}

	} else {
		$perm_str = '<input type="button" value="Save" name="save" onclick="show_no_permission_msg(0)" style="width:80px" id="save' . $btn_id . '"   class="formbutton_disabled"/>&nbsp;&nbsp;';
	}

	if ($permission[1] == 1) // Update
	{
		if ($is_update == 1) //Entry Mode
		{
			$perm_str .= '<input type="button" value="Update" name="update" onclick="' . $sub_func . '(1)" style="width:80px" id="update' . $btn_id . '"   class="formbutton"/>&nbsp;&nbsp;';
		} else {
			$perm_str .= '<input type="button" value="Update" name="update" onclick="show_button_disable_msg(1)" style="width:80px" id="update' . $btn_id . '"   class="formbutton_disabled"/>&nbsp;&nbsp;';
		}

	} else {
		$perm_str .= '<input type="button" value="Update" name="update" onclick="show_no_permission_msg(1)" style="width:80px" id="update' . $btn_id . '"   class="formbutton_disabled"/>&nbsp;&nbsp;';
	}

	if ($permission[2] == 1) // Delete
	{
		if ($is_update == 1) //Entry Mode
		{
			$perm_str .= '<input type="button" value="Delete" name="delete" onclick="' . $sub_func . '(2)" style="width:80px" id="Delete' . $btn_id . '"   class="formbutton"/>&nbsp;&nbsp;';
		} else {
			$perm_str .= '<input type="button" value="Delete" name="delete" onclick="show_button_disable_msg(2)" style="width:80px" id="Delete' . $btn_id . '"   class="formbutton_disabled"/>&nbsp;&nbsp;';
		}

	} else {
		$perm_str .= '<input type="button" value="Delete" name="delete" onclick="show_no_permission_msg(2)" style="width:80px" id="Delete' . $btn_id . '"   class="formbutton_disabled"/>&nbsp;&nbsp;';
	}

	$perm_str .= '<input type="button" value="Refresh" name="refresh" onclick="' . $refresh_function . '" style="width:80px" id="Refresh' . $btn_id . '"   class="formbutton"/>';
	$perm_str .= "<br><p style='margin-bottom: 5px;'></p>";

	if ($is_show_approve == 1) {
		if ($permission[3] == 1) {
			if ($is_update == 1) //Entry Mode
			{
				$perm_str .= '<input type="button" value="Approve" name="approve" onclick="' . $sub_func . '(3)" style="width:80px" id="approve' . $btn_id . '"   class="formbutton"/>&nbsp;&nbsp;';
			} else {
				$perm_str .= '<input type="button" value="Approve" name="approve" onclick="show_button_disable_msg(3)" style="width:80px" id="approve' . $btn_id . '"   class="formbutton_disabled"/>&nbsp;&nbsp;';
			}

		} else {
			$perm_str .= '<input type="button" value="Approve" name="approve" onclick="show_no_permission_msg(3)" style="width:80px; visibility:hidden" id="approve' . $btn_id . '"   class="formbutton_disabled"/>&nbsp;&nbsp;';
		}

	}

	if ($is_show_print == 1) {
		if ($permission[4] == 1) {
			if ($is_update == 0) //Entry Mode
			{
				$perm_str .= '<input type="button" value="Print" name="print" onclick="' . $sub_func . '(4)" style="width:80px" id="Print' . $btn_id . '"   class="formbutton"/>&nbsp;&nbsp;';
			} else {
				$perm_str .= '<input type="button" value="Print" name="print" onclick="show_button_disable_msg(4)" style="width:80px" id="Print' . $btn_id . '"   class="formbutton_disabled"/>&nbsp;&nbsp;';
			}

		} else {
			$perm_str .= '<input type="button" value="Print" name="print" onclick="show_no_permission_msg(4)" style="width:80px" id="Print' . $btn_id . '"   class="formbutton_disabled"/>&nbsp;&nbsp;';
		}

	} else {
		$perm_str .= '<input type="button" value="Print" name="print" onclick="show_button_disable_msg(4)" style="width:80px; visibility:hidden" id="Print' . $btn_id . '"   class="formbutton_disabled"/>&nbsp;&nbsp;';
	}

	return $perm_str;die;
}

// Usage--> echo create_drop_down( "cbo_f_name", 200, "select id,name from tbale where condition","id,name", 1, $selected )
function create_drop_down($field_id, $field_width, $query, $field_list, $show_select, $select_text_msg="", $selected_index="", $onchange_func="", $is_disabled="", $array_index="", $fixed_options="", $fixed_values="", $not_show_array_index="", $tab_index="", $new_conn="", $field_name="", $additionalClass="", $additionalAttributes="") {

	//$drop_down_loader_data=$field_id."*".$field_width."*".$query."*".$field_list."*".$show_select."*".$select_text_msg."*".$selected_index."*".$onchange_func."*". $onchange_func_param_db."*".$onchange_func_param_sttc."*".$add_new_page_lnk."*".$div_id;
	if ($is_disabled == 1) {
		$is_disabled = "disabled";
	} else {
		$is_disabled = "";
	}

	if ($tab_index != "") {
		$tab_index = 'tabindex="' . $tab_index . '"';
	} else {
		$tab_index = "";
	}

	if ($selected_index == "") {
		$selected_index = '0';
	}

	$field_list = explode(",", $field_list);

	$drop_down = '<select ' . $tab_index . ' name="' . ($field_name == "" ? $field_id : $field_name) . '" id="' . $field_id . '" class="combo_boxes ' . $additionalClass . '" ' . $is_disabled . '  style="width:' . $field_width . 'px" onchange="' . $onchange_func . '" ' . $multi_select . '>\n';

	if ($show_select == 1) {
		$drop_down .= '<option data-attr="" value="0">' . $select_text_msg . '</option>\n';
	}
	if ($fixed_options != "") {
		$fixed_options = explode("*", $fixed_options);
		$fixed_values = explode("*", $fixed_values);
		for ($kk = 0; $kk < count($fixed_options); $kk++) {
			$drop_down .= '<option data-attr="" value="' . $fixed_values[$kk] . ' ">' . $fixed_options[$kk] . '</option>\n';
		}
	}
	$addattr = explode(",", $additionalAttributes);

	if (!is_array($query)) {
		$nameArray = sql_select($query, '', $new_conn);

		foreach ($nameArray as $result) {
			//if(count($nameArray)==1) $selected_index=$result[csf($field_list[0])];
			$attdata = '';
			$m = 0;
			foreach ($addattr as $ak) {
				if ($m == 0) {
					$attdata = $result[csf($field_list[$ak])];
				} else {
					$attdata .= "**" . $result[csf($field_list[$ak])];
				}

				$m++;
			}

			$drop_down .= '<option data-attr="' . $attdata . '" value="' . $result[csf($field_list[0])] . '" ';
			if ($selected_index == $result[csf($field_list[0])]) {$drop_down .= 'selected';}
			$drop_down .= '>' . $result[csf($field_list[1])] . '</option>\n';
		}
	} else // List from An Array
	{
		if ($array_index == "") {
			$array_index = "";
		} else {
			$array_index = explode(",", $array_index);
		}

		if ($not_show_array_index == "") {
			$not_show_array_index = "";
		} else {
			$not_show_array_index = explode(",", $not_show_array_index);
		}

		foreach ($query as $key => $value):
			if ($array_index == "") {
				if ($not_show_array_index == "") {
					$drop_down .= '<option value="' . $key . '" ';
					if ($selected_index == $key) {$drop_down .= 'selected';}
					$drop_down .= '>' . $value . '</option>\n';
				} else {
					if (!in_array($key, $not_show_array_index)) {
						$drop_down .= '<option value="' . $key . '" ';
						if ($selected_index == $key) {$drop_down .= 'selected';}
						$drop_down .= '>' . $value . '</option>\n';
					}
				}
			} else {
				if (in_array($key, $array_index)) {
					if ($not_show_array_index == "") {
						$drop_down .= '<option value="' . $key . '" ';
						if ($selected_index == $key) {$drop_down .= 'selected';}
						$drop_down .= '>' . $value . '</option>\n';
					} else {
						if (!in_array($key, $not_show_array_index)) {
							$drop_down .= '<option value="' . $key . '" ';
							if ($selected_index == $key) {$drop_down .= 'selected';}
							$drop_down .= '>' . $value . '</option>\n';
						}
					}
				}
			}
		endforeach;
	}
	/*
		if($add_new_page_lnk!="")
		{
			$drop_down .='<option value="N">-- Add New --</option>\n';
	*/
	$drop_down .= '</select>';
	/*
		if( $_SESSION['logic_erp']["user_level"]==2 )
		{
			if($add_new_page_lnk!="")
			{
				$add_new_page_fnc="add_new_library('".$drop_down_loader_data."','".$add_new_page_lnk."','".$div_id."')";
				 $drop_down .='&nbsp;&nbsp; <img src="../../images/add_new.gif" width="27" height="17" onclick="'.$add_new_page_fnc.'"/> ';
			}
	*/
	return $drop_down;
	die;
}

// Usage--> echo create_list_view( "ID, Buyer Name, Contact No", "40,200,", "select id, buyer_name,contact_no from lib_buyer", "getDataBuyerMst", "0","1", 600, 350, $tbl_border, $is_multi_select, 1 );

function create_list_view($table_id, $tbl_header_arr, $td_width_arr, $tbl_width, $tbl_height, $tbl_border, $query, $onclick_fnc_name, $onclick_fnc_param_db_arr, $onclick_fnc_param_sttc_arr, $show_sl, $field_printed_from_array_arr, $data_array_name_arr, $qry_field_list_array, $controller_file_path='', $filter_grid_fnc="", $fld_type_arr="", $summary_flds="", $check_box_all="", $new_conn="") {
	$tbl_width = $tbl_width + 10;
	//$fld_type_arr Definition 0=string,1=integer,2=float,3=date, 4=3 digit,5=4 digit,5=5 digit
	$table_id = explode(",", $table_id);
	$tbl_header = explode(',', $tbl_header_arr);
	$td_width = explode(',', $td_width_arr);
	
	$onclick_fnc_param_db = explode(',', $onclick_fnc_param_db_arr);

	$field_printed_from_array = explode(',', $field_printed_from_array_arr);
	$data_array_name = count($data_array_name_arr);
	$qry_field_list = explode(',', $qry_field_list_array);

	$fld_type_arr = explode(',', $fld_type_arr);
	if ($summary_flds != "") {
		$summary_flds = explode(',', $summary_flds);
		$summary_total = array();} else {
		$summary_flds = "";
	}

	$table = '<div><table width="' . $tbl_width . '" cellpadding="0" cellspacing="0" border="' . $tbl_border . '" class="rpt_table" id="rpt_table' . $table_id[0] . '" rules="all"><thead><tr>';

	if ($show_sl == 1) {
		$table .= '<th width="50">SL No</th>';
	}

	for ($i = 0; $i < count($tbl_header); $i++) {
		if ($i < count($tbl_header) - 1) {
			$table .= '<th width="' . $td_width[$i] . '">' . $tbl_header[$i] . '</th>';
		} else {
			$table .= '<th>' . $tbl_header[$i] . '</th>';
		}

	}
	$table .= '</tr></thead>
	';
	

	$tbl_width1 = $tbl_width - 2;
	$tbl_width = $tbl_width - 22;
	$table .= '</table> <div style="max-height:' . $tbl_height . 'px; width:' . $tbl_width1 . 'px; overflow-y:scroll" id="' . $table_id[1] . '"><table align="left" width="' . $tbl_width . '" height="" cellpadding="0" cellspacing="0" border="' . $tbl_border . '" class="rpt_table" id="' . $table_id[0] . '" rules="all"><tbody>';
	$j = 0;

	if ($controller_file_path == "") {
		$controller_file_path = "";
	} else {
		$controller_file_path = ",'" . $controller_file_path . "'";
	}

	if ($onclick_fnc_param_sttc_arr == "") {
		$onclick_fnc_param_sttc_arr = "";
	} else {
		$onclick_fnc_param_sttc_arr = "," . $onclick_fnc_param_sttc_arr . "";
	}

	$nameArray = sql_select($query, '', $new_conn);
	foreach ($nameArray as $result) {
		$j++;
		if ($j % 2 == 0) {
			$bgcolor = "#E9F3FF";
		} else {
			$bgcolor = "#FFFFFF";
		}

		$db_param = "";

		if ($onclick_fnc_param_db_arr != "") {
			if ($check_box_all != "") {
				$aid = $j . "_";
			} else {
				$aid = "";
			}

			for ($w = 0; $w < count($onclick_fnc_param_db); $w++) {
				if (count($onclick_fnc_param_db) < 2) {
					$db_param .= "'" . $aid . $result[csf($onclick_fnc_param_db[$w])] . "'";
				} else {
					if ($db_param == "") {
						$db_param .= "'" . $aid . $result[csf($onclick_fnc_param_db[$w])] . "";
					} else {
						$db_param .= "_" . $result[csf($onclick_fnc_param_db[$w])] . "";
					}

					if ($w == count($onclick_fnc_param_db) - 1) {
						$db_param .= "'";
					}

				}
			}
		} else {
			$db_param = "";
			$onclick_fnc_param_sttc_arr = str_replace(",", "", $onclick_fnc_param_sttc_arr);
		}

		//$file_path="'".$file_path."'";
		$aid = "";

		if ($onclick_fnc_name != "") {
			$table .= '<tr height="20" onclick="' . $onclick_fnc_name . '(' . $db_param . '' . $onclick_fnc_param_sttc_arr . '' . $controller_file_path . ')" bgcolor="' . $bgcolor . '" style="cursor:pointer" id="tr_' . $j . '">';
		} else {
			$table .= '<tr height="20" bgcolor="' . $bgcolor . '" id="tr_' . $j . '">';
		}

		if ($show_sl == 1) {
			$table .= '<td width="50" >' . $j . '</td>';
		}

		for ($i = 0; $i < count($qry_field_list); $i++) {
			$show_data = "";

			if (in_array($qry_field_list[$i], $summary_flds)) {
				$summary_total[$qry_field_list[$i]] = $summary_total[$qry_field_list[$i]] + $result[csf($qry_field_list[$i])];
			}
		

			if ($fld_type_arr[$i] == 0) {
				$align = "align='left'";
				$show_data = ($result[csf($qry_field_list[$i])]);} else if ($fld_type_arr[$i] == 1) {
				$align = "align='right'";
				$show_data = number_format($result[csf($qry_field_list[$i])], '0');
			} else if ($fld_type_arr[$i] == 2) {
				$align = "align='right'";
				$show_data = number_format($result[csf($qry_field_list[$i])], '2');
			} else if ($fld_type_arr[$i] == 3) {
				$align = "align='left'";
				$show_data = change_date_format($result[csf($qry_field_list[$i])]);
			} else if ($fld_type_arr[$i] == 4) {
				$align = "align='right'";
				$show_data = number_format($result[csf($qry_field_list[$i])], '3');
			} else if ($fld_type_arr[$i] == 5) {
				$align = "align='right'";
				$show_data = number_format($result[csf($qry_field_list[$i])], '4');
			} else if ($fld_type_arr[$i] == 6) {
				$align = "align='right'";
				$show_data = number_format($result[csf($qry_field_list[$i])], '5');
			}
			else if ($fld_type_arr[$i] == 7) {
				$align = "align='center'";
				$show_data = $result[csf($qry_field_list[$i])];
			}
			if ($i < count($qry_field_list) - 1) {
				$split = get_split_length($data_array_name_arr[$i][$show_data], $td_width[$i]);
				if ($field_printed_from_array[$i] == $qry_field_list[$i]) {
					$table .= '<td ' . $align . ' width="' . $td_width[$i] . '"><p>' . $data_array_name_arr[$i][$show_data] . '</p></td>';
				} else {
					$split = get_split_length($show_data, $td_width[$i]);
					$table .= '<td  ' . $align . ' width="' . $td_width[$i] . '"><p>' . $show_data . '</p></td>';
				}
			} else {
				$split = get_split_length($data_array_name_arr[$i][$show_data], $td_width[$i]);
				if ($field_printed_from_array[$i] == $qry_field_list[$i]) {
					$table .= '<td ><p>' . $data_array_name_arr[$i][$show_data] . '</p></td>';
				} else {
					$split = get_split_length($show_data, $td_width[$i]);
					$table .= '<td ' . $align . '><p>' . $show_data . '</p></td>';
				}
			}
		}
		$table .= '</tr>';
	}
	$span = 0;
	if (is_array($summary_flds)) {

		for ($i = 0; $i < count($summary_flds); $i++) {
			if ($i == 0) {$table .= '<tfoot><tr><th colspan="' . $summary_flds[$i] . '">Total</th>';} else {
				if ($summary_total[$summary_flds[$i]] != "" || $summary_total[$summary_flds[$i]] != 0) {
					$tot = number_format($summary_total[$summary_flds[$i]], 2);
				} else {
					$tot = "";
				}

				$table .= '<th colspan="' . $span . '" align="right">' . $tot . '</th>'; // $summary_total

			}

		}
		$table .= '</tr></tfoot>';
	}

	if (trim($filter_grid_fnc) != "") {
		$js = '<script>';
		$js .= ' ' . $filter_grid_fnc . ' ';
		$js .= '</script>';
	} else {
		$js = '';
	}

	if ($check_box_all != "") {
		$check = '<div class="check_all_container"><div style="width:100%">
		<div style="width:50%; float:left" align="left">
		<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
		</div>
		<div style="width:50%; float:left" align="left">
		<input type="button" name="close" id="close"  onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
		</div>
		</div></div>';
	}

	$table .= '</tbody></table></div>' . $check . '</div>' . $js;
	return $table;
	die;
}

function get_split_length($str, $width) {
	if ($width == "" || $width == 0) {
		$width = 300;
	}

	$str_w = strlen($str) * 9;

	if ($str_w < $width) {
		return $width;
	} else // if ($str_w<$width)
	{
		return round($width / 10);
	}
}
/*

	function split_string($main_string, $fld_width, $position)
	{
		//This function will return space seperated string of a
		//joint long string to fit a html table row and column
		// uses  --> echo split_string($main_string, 25)

		if ($position=="")
		{
			if ( $fld_width>0 and $fld_width<20) $position=5;
			else if ( $fld_width>19 and $fld_width<40) $position=15;
			else if ( $fld_width>39 and $fld_width<60) $position=30;

		}
	    $len=strlen($main_string);
	    if ($len>$position)
	    {
			$whole= chunk_split($main_string, $position);
	    }
	    else
	   	 	$whole= $main_string;
	   	return $whole;
	}
*/
function create_year_array() {
	$start_year = 10;
	$no_of_year = 16;
	$y = date("Y", time()) - $start_year;
	$cy = date("Y", time());
	for ($k = 0; $k < $no_of_year; $k++) {
		$yer[$y + $k] = $y + $k;
	}
	return $yer;
}

function load_month_buttons($show_year=0) {
	/*	$y=date("Y",time())-10;
	$cy=date("Y",time());*/
	if ($show_year == 1) {
		echo create_drop_down("cbo_year_selection", 65, create_year_array(), "", 0, "-- --", date("Y", time()), "", 0, "");
		//$displ="display:none";
	}

	/*$month_year="<select name='cbo_year_selection' id='cbo_year_selection' class='combo_boxes' style='width:65px; $displ'>";
		for ($k=0; $k<16;$k++)
		{
			$yer=$y+$k;
			if ($cy==$yer)
				$month_year .="<option value='$yer' selected='selected'>$yer</option>";
			else
				$month_year .="<option value='$yer'>$yer</option>";

		}
		$month_year .="</select>";
	 */

	global $months;
	for ($i = 1; $i < 13; $i++) {
		if ($i < 10) {
			$j = "0" . $i;
		} else {
			$j = $i;
		}

		$month_btn .= '<input type="button" name="btn_' . $i . '" style="width:50px;" onclick="set_date_range(\'' . $j . '\')" id="btn_' . $i . '" value="' . substr($months[$i], 0, 3) . '" class="month_button" />&nbsp;&nbsp;';
	}
	return $month_year . "&nbsp;&nbsp;" . $month_btn;die;
}

function load_freeze_divs($img_path='', $permission="", $page_title_off="", $on_qcf = false,$margin=10) {
	if ($page_title_off == 1) {
		$title = "";
	} else {
		$title = $_SESSION['page_title'];
	}

	$html = '<div id="boxes">
		<div id="dialog" class="window" >
		<div id="msg" class="msg_header">0
		</div>
		<div style="width:400;padding:20px; height:150px; vertical-align:middle">
		<img src="' . $img_path . 'images/Loading2.gif" width="30" height="30" clear="all" style="vertical-align:middle;" /> <span id="msg_text" style="font-size:14px; color:#F00"> </span>
		</div>
		</div>
		<div id="mask"></div>
		</div>
		<div style="margin-top:'.$margin.'px; margin-bottom:'.$margin.'px;"> <input type="hidden" id="active_menu_id" value="' . $_SESSION['menu_id'] . '"><input type="hidden" id="active_module_id" value="' . $_SESSION['module_id'] . '"><input type="hidden" id="garments_nature" value="' . $_SESSION['fabric_nature'] . '"><input type="hidden" id="session_user_id" value="' . $_SESSION['logic_erp']['user_id'] . '">';
	if ($on_qcf == true) {
		$title = "Page On Qc";
		$style = 'style="color:red"';
	}

	if ($permission != "") {
		$html .= '<div ' . $style . ' class="form_caption" title="' . get_button_level_permission($permission) . '">' . $title . '</div>';
	} else {
		$html .= '<div ' . $style . ' class="form_caption"  >' . $title . '</div>';
	}

	$html .= '</div>';
	return $html;
}

/**
 * @param $jqlatest defining JQuery library initializing. If $jqlatest = 1, JQuery v3.1.1 will be loaded. Else v1.6.2
 */
function load_html_head_contents($title, $path, $filter="", $popup="", $unicode="", $multi_select="", $am_chart="", $jqlatest="", $combo_box_search="") {
	$html = '
 	<!DOCTYPE HTML>
 	<html>
 	<head>
 	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
 	<title>' . $title . '</title>
 	<link href="' . $path . 'css/style_common.css" rel="stylesheet" type="text/css" media="screen" />
 	<script src="' . $path . 'includes/functions.js" type="text/javascript"></script>';

	if ($jqlatest == 1) {
		$html .= ' <script type="text/javascript" src="' . $path . 'js/jquery_latest.js"></script>';
	} else {
		$html .= ' <script type="text/javascript" src="' . $path . 'js/jquery.js"></script>';
	}

	//if ($combo_box_search == 1) {
		$html .= ' <link href="' . $path . 'css/select2.min.css" rel="stylesheet" type="text/css" media="screen" />';
		$html .= ' <script src="' . $path . 'js/select2.min.js" type="text/javascript"></script>';
	//}

	if ($filter != "") {
		$html .= '
 	<link href="' . $path . 'css/filtergrid.css" rel="stylesheet" type="text/css" media="screen" />
 	<script src="' . $path . 'js/tablefilter.js" type="text/javascript"></script>';
	}

	if ($popup != "") {
		$html .= '
 	<link href="' . $path . 'css/modal_window.css" rel="stylesheet" type="text/css" />
 	<script type="text/javascript" src="' . $path . 'js/modal_window.js"></script>';
	}

	if ($unicode != "") {
		$html .= '
 	<script type="text/javascript" src="' . $path . 'js/driver.phonetic.js" ></script>
 	<script type="text/javascript" src="' . $path . 'js/driver.probhat.js" ></script>
 	<script type="text/javascript" src="' . $path . 'js/engine.js" ></script>';
	}

	if ($multi_select != "") {
		$html .= '
 	<script src="' . $path . 'js/multi_select.js" type="text/javascript"></script>';
	}

	if ($am_chart != "") {
		$html .= '
 	<script type="text/javascript" src="' . $path . 'ext_resource/amcharts/flash/swfobject.js" ></script>
 	<script type="text/javascript" src="' . $path . 'ext_resource/amcharts/javascript/amcharts.js" ></script>
 	<script type="text/javascript" src="' . $path . 'ext_resource/amcharts/javascript/amfallback.js" ></script>
 	<script type="text/javascript" src="' . $path . 'ext_resource/amcharts/javascript/raphael.js" ></script>
 	<script type="text/javascript" src="' . $path . 'js/chart/logic_chart.js" ></script>';
	}

	return $html;die;
}

//----*********************************************************************************************************** Common Used Functions ends

// HRM Functions
function get_buyer_in_time($in_time, $r_in_time, $allow_time=0) {
	if ($allow_time == 0 or $allow_time == "") {return $in_time;die;}
	$timeDiffin = datediff(n, $in_time, $r_in_time);
	//return $timeDiffin;die;
	if ($in_time == "00:00:00") {return $in_time;die;}
	if ($allow_time > $timeDiffin) {return $in_time;die;} else {
		if (strlen($timeDiffin) < 2) {
			return add_time($r_in_time, -$timeDiffin);
		} else {
			$adds = substr($timeDiffin, 0, 1) + substr($timeDiffin, 1, 1);
			if ($adds <= $allow_time) {return add_time($r_in_time, -$adds);die;}
		}
	}
}

function get_buyer_out_time($out_time, $r_out_time, $allow_time, $ot_limit) {
	if ($allow_time == 0 or $allow_time == "") {return $out_time;die;}
	if ($out_time == "00:00:00") {return $out_time;die;}
	$timeDiffin = datediff(n, $r_out_time, $out_time);

	if ($timeDiffin <= $ot_limit) {return $out_time;die;} else {
		$b_out_time = add_time($r_out_time, $ot_limit);
		$min = substr($out_time, 3, 1) + substr($out_time, 4, 1);
		if (strlen($min) == 1) {
			$min = "0" . $min;
		} else {
			$min = $min;
		}

		$b_act_out = substr($b_out_time, 0, 2) . ":" . $min . ":" . substr($out_time, 6, 2);
		return $b_act_out;die;
	}
}

function get_buyer_ot_hr($act_ot_min, $one_hour_ot_unit, $allow_ot_fraction, $half_hr_ot_unit, $first_ot_limit) {
	$full_hr = 0;
	if ($act_ot_min == 0) {return 0;die;}

	if ($first_ot_limit == 0 or $first_ot_limit == "") {
		$timeDiffin = $act_ot_min;
		if ($timeDiffin < 0) {return 0;die;}
		$full_hr = floor($timeDiffin / 60);

		$rest = $timeDiffin % 60;
		if ($rest >= $one_hour_ot_unit) {
			$full_hr = $full_hr + 1;
		} else if ($allow_ot_fraction == 1) {
			if ($rest >= $half_hr_ot_unit && $rest < $one_hour_ot_unit) {
				$full_hr = $full_hr + .5;
			}
		}
		return $full_hr;die;
	} else {
		$timeDiffin = $act_ot_min; //datediff(n,$r_out_time,$out_time);

		if ($timeDiffin < 0) {return 0;die;}

		if ($timeDiffin >= $first_ot_limit) {return ($first_ot_limit / 60);die;} else {
			$full_hr = floor($timeDiffin / 60);
			//if ($first_ot_limit==1200) {return $full_hr; die;}

			$rest = $timeDiffin % 60;
			if ($rest >= $one_hour_ot_unit) {
				$full_hr = $full_hr + 1;
			} else if ($allow_ot_fraction == 1) {
				if ($rest >= $half_hr_ot_unit && $rest < $one_hour_ot_unit) {
					$full_hr = $full_hr + .5;
				}
			}
		}
		return $full_hr;die;
	}

}
function get_buyer_tot_emp_ot_hr($emp_code, $str_date, $one_hour_ot_unit, $allow_ot_fraction, $half_hr_ot_unit, $first_ot_limit) {
	$emp_ot_hr = 0;
	$sql = "select * from hrm_attendance where emp_code=$emp_code and $str_date";
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result)) {
		$ot_hr = get_buyer_ot_hr($row[total_over_time_min], $one_hour_ot_unit, $allow_ot_fraction, $half_hr_ot_unit, $first_ot_limit);
		$emp_ot_hr = $emp_ot_hr + $ot_hr;
	}
	return $emp_ot_hr;die;
}

//***************************************************************************************************************** HRM Functions

// Merchandising Functions
// this method is not used anymore
function fnc_gray_fabric_consumption($job_no, $order_qnty, $consumption_type, $item_group) {
	// total_grey_fab_consumption_top,consumption_per_number_top,consumption_per_unit_top
	$sql = "select * from wo_po_measurement_top_details where job_no_mst_top='$job_no' and status_active=1";
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);
	// total_grey_fab_consumption_bottom,consumption_per_number_bottom,consumption_per_unit_bottom
	$sql = "select * from wo_po_measurement_bottom_details where job_no_mst_bottom='$job_no' and status_active=1";
	$result_bt = mysql_query($sql);
	$row_bt = mysql_fetch_array($result_bt);

	if ($consumption_type == 1) // Gray Consumption
	{
		$consumption = $row[total_grey_fab_consumption_top] + $row_bt[total_grey_fab_consumption_bottom];
	} else if ($consumption_type == 2) // FInish
	{
		$consumption = $row[total_finish_fab_consumption_top] + $row_bt[total_finish_fab_consumption_bottom];
	} else if ($consumption_type == 3) // Yarn
	{
		$consumption = $row[txt_total_yarn_needed_top] + $row_bt[txt_total_yarn_needed_bottom];
	} else if ($consumption_type == 4) // trims
	{
		$consumption = 1;
	}
//$row[txt_total_yarn_needed_top]+ $row_bt[txt_total_yarn_needed_bottom];
	//return $consumption; die;

	if ($row[consumption_per_unit_top] == 0) {
		$total_qnty = $order_qnty / (12 * $row[consumption_per_number_top]);
	} else {
		$total_qnty = $order_qnty / (1 * $row[consumption_per_number_top]);
	}

	$total_req = $consumption * $total_qnty;
	if ($consumption_type == 4) // trims
	{
		$consumption_per_unit_trim = return_field_value("consumption_per_unit", "wo_po_cost_trims_dtls", "job_no_cost_trim_mst='$job_no' and item_code=$item_group");
		$total_req = $total_req * $consumption_per_unit_trim;
	}
	return number_format($total_req, 2, '.', '');die;

}

//************************************************************************************************************* Merchandising Functions

/*
	function return_global_query($strQuery)
	{
		//$strQuery=strtoupper($strQuery);
		$data_raw=explode("FROM",$strQuery);
		$data_ist=explode("SELECT",$data_raw[0]);
		$data=explode(",",$data_ist[1]);
		$new_qry.="SELECT ";
		for ($i=0;$i<count($data);$i++)
		{
			if ($i!=count($data)-1) $new_qry.=" ".strtolower($data[$i])." as ".strtoupper($data[$i]).",";
			else $new_qry.=" ".strtolower($data[$i])." as ".strtoupper($data[$i]);
		}
		$new_qry.=" FROM  ".$data_raw[1]." ";
		return $new_qry; die;
*/
function get_file_ext($key) {
	$key = strtolower(substr(strrchr($key, "."), 1));
	$key = str_replace("jpeg", "jpg", $key);
	return $key;
}

function def_number_format($number, $dec_type, $comma='') {
	$dec_place = array(1 => 2, 2 => 2, 3 => 8, 4 => 2, 5 => 4, 6 => 0, 7 => 2, 8 => 6);
	if ($comma != 0) {
		$comma = ",";
	}

	if ($dec_type == "" && $comma == "") {return $number;die;} // number_format($number, 0, '', '');
	else if ($dec_type != "") {
		return number_format($number, $dec_place[$dec_type], '.', $comma);
	}

}

/**
 * [def_number_format description]
 * @param  [integer] $number [defining value that need to be formatted]
 * @param  [array]   $dec_type [defining array for different decimal point]
 * @param  [string]  $comma [defining the seperator like comma in the number]
 * @return [integer]            [return number]
 */
function decimal_format($number, $dec_type="", $comma="") {
	// THIS ARRAY VARIABLE IS INITIATED IN ARRAY_FUNCTION.PHP FILE
	global $dec_place;

	$comma = ($comma!="") ? "," : "";

	if ($dec_type == "" && $comma == "") { 
		return $number;
	} 
	else if ($dec_type != "" && $comma == "") {
		return number_format($number, $dec_place[$dec_type], '.', "");
	}
	else if ($dec_type != "" && $comma != "") {
		return number_format($number, $dec_place[$dec_type], '.', $comma);
	}

}

function return_id____off($field_text, $library_array, $table_name, $table_field="", $entry_form="") {

	global $db_type;
	$field_text=str_replace('"','',trim($field_text));
	$field_text = str_replace("'", "", trim(strtoupper($field_text)));
	//$field_text = str_replace('"', '', trim(strtoupper($field_text)));
	if ($field_text == "") {return 0;die;}
	if ($db_type == 2) {
		$field_text = str_replace("(", "[", trim(strtoupper($field_text)));
		$field_text = str_replace(")", "]", trim(strtoupper($field_text)));
	}
	$library_array_new = array_combine($library_array, $library_array);
	//if (strpos($field_text,"INSERT")>0 ) { return 0; }

	//if ( in_array((string)$field_text, $library_array) )
	if ($library_array_new[$field_text] != '') {

		$data_id = array_search($field_text, $library_array, true); //$color_library[str_replace("'","",$$txtcolor)];

	} else {

		$data_id = return_next_id("id", "$table_name", 1);
		if ($entry_form != '') {
			$table_field = $table_field . ",entry_form";
			$data_fld = "(" . $data_id . ",'" . trim(strtoupper($field_text)) . "','" . $entry_form . "')";
		} else {
			//  $table_field=$table_field.",entry_form";
			$data_fld = "(" . $data_id . ",'" . trim(strtoupper($field_text)) . "')";
		}
		$rID = sql_insert($table_name, $table_field, $data_fld, 1);
	}
	return $data_id;
	die;
}
//****=========New Method for Lib Color,size name ***================
function return_id_lib_common____off($field_text, $library_array, $table_name, $table_field="", $entry_form="") {

	global $db_type;
	global $pc_date_time;
	$field_text=str_replace('"','',trim($field_text));
	$field_text = str_replace("'", "", trim(strtoupper($field_text)));
	//$field_text = str_replace('"', '', trim(strtoupper($field_text)));
	if ($field_text == "") {return 0;die;}
	if ($db_type == 2) {
		$field_text = str_replace("(", "[", trim(strtoupper($field_text)));
		$field_text = str_replace(")", "]", trim(strtoupper($field_text)));
	}
	$library_array_new = array_combine($library_array, $library_array);
	//if (strpos($field_text,"INSERT")>0 ) { return 0; }

	if(in_array((string)$field_text, $library_array,true) )
	{
	//if ($library_array_new[$field_text] != '') {

		$data_id = array_search($field_text, $library_array, true); //$color_library[str_replace("'","",$$txtcolor)];

	} else {
	//pc_date_time//$_SESSION['logic_erp']['user_id']
		$data_id = return_next_id("id", "$table_name", 1);
		if ($entry_form != '') {
			$table_field = $table_field . ",entry_form,insert_date,inserted_by";
			$data_fld = "(" . $data_id . ",'" . trim(strtoupper($field_text)) . "','" . $entry_form . "','" . $pc_date_time . "','" . $_SESSION['logic_erp']['user_id'] . "')";
		} else {
			//  $table_field=$table_field.",entry_form";
			$data_fld = "(" . $data_id . ",'" . trim(strtoupper($field_text)) . "')";
		}
		$rID = sql_insert($table_name, $table_field, $data_fld, 1);
	}
	return $data_id;
	die;
}



function _sleep($file_name=''){
	$file_path = base_path("tmp_".$file_name."_sleep.txt");
	$users = file($file_path);
	$user = $users[0]+1;

	$openFile = fopen($file_path , "w");
	fputs($openFile , $user);
	fclose($openFile);
	sleep($user);
}

function _wake($file_name=''){
	$file_path = base_path("tmp_".$file_name."_sleep.txt");
	$users = file($file_path);
	$user = ($users[0]>0)?($users[0]-1):0;

	$openFile = fopen($file_path , "w");
	fputs($openFile , $user);
	fclose($openFile);
}
//=========New Method for Lib Color,size name ================
//Developed by REZA
function return_id_lib_common($field_text, $library_array, $table_name, $table_field="", $entry_form=0) {

	global $con ;
	global $pc_date_time;
	$file = base_path($table_name.".txt");

    $field_text = trim(str_replace(array("'","`",'"',"(",")"),array("","","","[","]"), strtoupper(strip_tags($field_text))));
    $entry_form = str_replace("'","",$entry_form);
    $table_field = str_replace("'","",$table_field);

	$return_id = 0;

    if ($field_text=="") {return $return_id;}
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
function return_id($field_text, $library_array, $table_name, $table_field="", $entry_form=0) {

	global $con ;
	global $pc_date_time;
	$file = base_path($table_name.".txt");

    $field_text = trim(str_replace(array("'","`",'"',"(",")"),array("","","","[","]"), strtoupper(strip_tags($field_text))));
    $entry_form = str_replace("'","",$entry_form);
    $table_field = str_replace("'","",$table_field);
	

	$return_id = 0;
	
    if ($field_text=="") {return $return_id;}
    else{

        $tmp_library_array = array_flip($library_array);
		//print_r( $tmp_library_array);
		if (isset($_COOKIE[$table_name])) {
			$cookieData_arr = json_decode($_COOKIE[$table_name],true);
		}
		
		if($tmp_library_array[$field_text] > 0){$return_id = $tmp_library_array[$field_text];}
		else if($cookieData_arr[$field_text] > 0){$return_id = $cookieData_arr[$field_text];}
		else{
		
			//Text file check crearte..................................
			if (!file_exists($file)) {
				$myfile = fopen($file, "a") or die("Unable to create!");
				chmod($file, 0777);
				fclose($myfile);
			}
			//..............................................end;


			// One second delay if multi user insert in same time...........
			$sleepFile=base_path($table_name."_sleep.txt");
			if(date(time() - filemtime($sleepFile)) < 2){_sleep($table_name);}
			$mySleepFile = fopen($sleepFile, "w") or die("Unable to create!");
			chmod($sleepFile, 0777);
			fclose($mySleepFile);
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




/**
 * [return_lib_id_by_value description]
 * @param  [string] $field_text [defining value of the table field]
 * @param  [string] $table_name [defining name of the table]
 * @param  [string] $table_field [defining name of the table field]
 * @param  [string] $entry_form [defining name of the table field]
 * @return [integer]            [return id of table]
 */
function return_lib_id_by_value($field_text, $table_name, $table_field, $entry_form="", $con="") {

	global $db_type;
	$field_text = trim(str_replace("'", "", strtoupper($field_text)));
	//echo "select id from  $table_name where status_active=1 and is_deleted=0 and trim($table_field)='$field_text'";die;
	$data_id = return_field_value('id', $table_name, "status_active=1 and is_deleted=0 and trim($table_field)='$field_text'");
	if($data_id){
		return $data_id;die;
	}
	else
	{
		if(!$con)
		{
			return 0;die;
		}
		$data_id = return_next_id("id", "$table_name", 1);
		if($entry_form!="")
		{
			$insert_sql="insert into $table_name (id,".$table_field.",entry_form) values ($data_id,'".$field_text."',$entry_form)";
		}
		else
		{
			$insert_sql="insert into $table_name (id,".$table_field.") values ($data_id,'".$field_text."')";
		}
		$result =  oci_parse($con, $insert_sql);
		$exestd=oci_execute($result,OCI_NO_AUTO_COMMIT);
		oci_commit($con);
		return $data_id;die;
	}
}

//need to check
function bulk_update_sql_statement($table, $id_column, $update_column, $data_values, $id_count) {
	$field_array = explode("*", $update_column);
	//$id_count=explode("*",$id_count);
	//$data_values=explode("*",$data_values);
	//print_r($data_values);die;
	$sql_up .= "UPDATE $table SET ";

	//echo $id_count;

	for ($len = 0; $len < count($field_array); $len++) {
		$sql_up .= " " . $field_array[$len] . " = CASE $id_column ";
		for ($id = 0; $id < count($id_count); $id++) {
			if (trim($data_values[$id_count[$id]][$len]) == "") {
				$sql_up .= " when " . $id_count[$id] . " then  '" . $data_values[$id_count[$id]][$len] . "'";
			} else {
				$sql_up .= " when " . $id_count[$id] . " then  " . $data_values[$id_count[$id]][$len] . "";
			}

		}
		if ($len != (count($field_array) - 1)) {
			$sql_up .= " END, ";
		} else {
			$sql_up .= " END ";
		}

	}
	$sql_up .= " where $id_column in (" . implode(",", $id_count) . ")";
	return $sql_up;
}

///============================monzu=====================
// this function is for updating job mst table.
//call from: woven_order_entry_controller.php, size_color_breakdown_controller.php
function update_job_mast($update_id) {
	//$data_array_se=sql_select("select sum(a.po_quantity) as po_tot,sum(a.po_total_price) as po_tot_price,b.currency_id from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and job_no_mst=$update_id and a.is_deleted=0 and a.status_active=1");
	$data_array_se = sql_select("select sum(a.po_quantity) as po_tot,sum(a.po_total_price) as po_tot_price from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and job_no_mst=$update_id and a.is_deleted=0 and a.status_active=1 group by job_no_mst");
	list($po_data) = $data_array_se;
	/*if($po_data[csf('currency_id')]==1)
		{
			$poavgprice=number_format($po_data[csf('po_tot_price')]/$po_data[csf('po_tot')],2);
		}
		else
		{
			$poavgprice=number_format($po_data[csf('po_tot_price')]/$po_data[csf('po_tot')],4);
		}*/
	$poavgprice = number_format($po_data[csf('po_tot_price')] / $po_data[csf('po_tot')], 4);

	$field_array = "job_quantity*avg_unit_price*total_price";
	$data_array = "" . $po_data[csf('po_tot')] . "*" . $poavgprice . "*" . $po_data[csf('po_tot_price')] . "";
	$rID = sql_update("wo_po_details_master", $field_array, $data_array, "job_no", "" . $update_id . "", 1);
	$value = array(0 => $rID, 1 => $po_data[csf('po_tot')], 2 => $poavgprice, 3 => $po_data[csf('po_tot_price')]);
	return $value;
}

//======================================================
function update_cost_sheet($job_no) //kausar
{
	$data_array = sql_select("select a.company_name, a.buyer_name, a.brand_id, a.avg_unit_price, a.currency_id, a.job_quantity, b.costing_per, c.fabric_cost, c.trims_cost, c.embel_cost, c.wash_cost, c.comm_cost, c.lab_test, c.inspection, c.cm_cost, c.freight, c.currier_pre_cost, c.currier_percent, c.deffdlc_cost, c.deffdlc_percent, c.certificate_pre_cost, c.common_oh, c.commission, c.depr_amor_pre_cost, c.total_cost from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_dtls c where a.job_no=b.job_no and b.job_no =c.job_no and a.job_no=$job_no and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
	if (count($data_array) > 0)
	{
		$company_name = ""; $buyer_name = ""; $style_qty=0; $avg_unit_price = 0; $currency_id = ''; $costing_per = 0; $fabric_cost_o = 0; $trims_cost_o = 0; $embel_cost_o = 0; $wash_cost_o = 0; $commarcial_o = 0; $lab_test_o = 0; $inspection_o = 0; $cm_cost_o = 0; $freight_o = 0; $currier_pre_cost_o = 0; $currier_percent = 0; $deffdlc_cost = 0; $deffdlc_percent = 0; $certificate_pre_cost_o = 0; $common_oh_o = 0; $commision_o = 0; $depr_amor_pre_cost_o = 0; $total_cost = 0; $price_dzn = 0; 

		foreach ($data_array as $row)
		{
			if ($row[csf('currier_percent')] == "")  $row[csf('currier_percent')] = 0;
			if ($row[csf('deffdlc_percent')] == "")  $row[csf('deffdlc_percent')] = 0;

			$company_name = $row[csf('company_name')];
			$buyer_name = $row[csf('buyer_name')];
			$brand_id = $row[csf('brand_id')];
			$style_qty=$row[csf('job_quantity')];
			$avg_unit_price = $row[csf('avg_unit_price')];
			$currency_id = $row[csf('currency_id')];
			$costing_per = $row[csf('costing_per')];

			$fabric_cost_o = $row[csf('fabric_cost')];
			$trims_cost_o = $row[csf('trims_cost')];
			$embel_cost_o = $row[csf('embel_cost')];
			$wash_cost_o = $row[csf('wash_cost')];
			$commarcial_o = $row[csf('comm_cost')];
			$lab_test_o = $row[csf('lab_test')];
			$inspection_o = $row[csf('inspection')];
			$cm_cost_o = $row[csf('cm_cost')];
			$freight_o = $row[csf('freight')];
			$currier_pre_cost_o = $row[csf('currier_pre_cost')];
			$certificate_pre_cost_o = $row[csf('certificate_pre_cost')];
			//echo $currier_pre_cost_o.'=DD';

			$currier_percent = $row[csf('currier_percent')];
			$deffdlc_cost = $row[csf('deffdlc_cost')];
			$deffdlc_percent = $row[csf('deffdlc_percent')];
			$common_oh_o = $row[csf('common_oh')];
			$commision_o = $row[csf('commission')];
			$depr_amor_pre_cost_o = $row[csf('depr_amor_pre_cost')];
			$total_cost_o = $row[csf('total_cost')];

			$costing_per_pcs = 0;
			if ($costing_per == 1) $costing_per_pcs = 12;
			else if ($costing_per == 2) $costing_per_pcs = 1;
			else if ($costing_per == 3) $costing_per_pcs = 12 * 2;
			else if ($costing_per == 4) $costing_per_pcs = 12 * 3;
			else if ($costing_per == 5) $costing_per_pcs = 12 * 4;

			$price_dzn = $row[csf('avg_unit_price')] * $costing_per_pcs;
			$price_pcs_set = $row[csf('avg_unit_price')];
		}

		$sql_f = sql_select("select monthly_cm_expense, no_factory_machine, working_hour, cost_per_minute, depreciation_amorti, operating_expn from lib_standard_cm_entry where company_id=$company_name and status_active=1 and is_deleted=0");

		$depreciation_amorti_per = 0;
		$operating_expn_per = 0;
		foreach ($sql_f as $sql_f_row) {
			$depreciation_amorti_per = $sql_f_row[csf('depreciation_amorti')];
			$operating_expn_per = $sql_f_row[csf('operating_expn')];
		}

		//==================================Deffd. LC %==============
		$data_deffdlc_per = sql_select("select deffd_lc_cost_percent from  lib_buyer where id='$buyer_name'");
		if (count($data_deffdlc_per) > 0) {
			foreach ($data_deffdlc_per as $rowdefflc) {
				if ($rowdefflc[csf('deffd_lc_cost_percent')] == "") $rowdefflc[csf('deffd_lc_cost_percent')] = 0;
				if ($deffdlc_percent != 0) $rowdefflc[csf('deffd_lc_cost_percent')] = $deffdlc_percent;

				if ($rowdefflc[csf('deffd_lc_cost_percent')] != 0) {
					$deffdlc_cost = ($price_dzn * $rowdefflc[csf('deffd_lc_cost_percent')]) / 100;
					$deffdlc_percent = $rowdefflc[csf('deffd_lc_cost_percent')];
				} else {
					$deffdlc_cost = $deffdlc_cost;
					$deffdlc_percent = $deffdlc_percent;
				}
			}
		} else {
			$deffdlc_cost = $deffdlc_cost;
			$deffdlc_percent = $deffdlc_percent;
		}

		//==================================Currier Cost %==============

		$fob_value = $price_dzn - $commision_o;
		
		$data_currier_per = sql_select("select commercial_cost_method, commercial_cost_percent, copy_quotation as based_on from variable_order_tracking where company_name=" . $company_name . "  and variable_list=57 and tna_integrated='$buyer_name' and profit_calculative='$brand_id' and status_active=1 and is_deleted=0");
		if(count($currier_result)<1)
		{
			$currier_result=sql_select("select commercial_cost_method, commercial_cost_percent, copy_quotation as based_on from variable_order_tracking where company_name=".$company_name."  and variable_list=57 and tna_integrated='$buyer_name' and profit_calculative='0' and status_active=1 and is_deleted=0");
		}
		if (count($data_currier_per)<1) {
			$data_currier_per = sql_select("select commercial_cost_method, commercial_cost_percent, copy_quotation as based_on from variable_order_tracking where company_name=" . $company_name . "  and variable_list=57 and status_active=1 and is_deleted=0");
		}
		if (count($data_currier_per) > 0) {
			$currier_cost_method = 0; $based_on=1; $fixamount=0;
			foreach ($data_currier_per as $row)
			{
				if($row[csf("based_on")]==2) $fixamount=$row[csf("commercial_cost_percent")]; 
				else
				{
					$currier_cost_method = $row[csf("commercial_cost_method")];
					if ($currier_cost_method == "" || $currier_cost_method == 0) $currier_cost_percent = 1; else $currier_cost_percent = $currier_cost_method;
	
					$currier_cost_percent = $row[csf("commercial_cost_percent")];
					if ($currier_cost_percent == "") $currier_cost_percent == 0;
					if ($currier_percent != 0) $currier_cost_percent = $currier_percent;
					if ($currier_cost_percent == 0) $currier_cost_percent = $currier_percent; else $currier_cost_percent = $currier_cost_percent;
				}
				
				$based_on=$row[csf("based_on")];
			}
			//echo "10**=".$currier_cost_percent.'=S='.$currier_cost_method.'AA';die;

			if ($currier_cost_method == 1) {
				$data_array = sql_select("select (fab_amount+yarn_amount+trim_amount) as amount from wo_pre_cost_sum_dtls where job_no=$job_no' and status_active=1 and is_deleted=0");
				foreach ($data_array as $row) {
					$amount = def_number_format($row[csf("amount")], 8, "");
				}
			}
			else if ($currier_cost_method == 2) {
				$amount = def_number_format($price_dzn, 8, "");
			}
			else if ($currier_cost_method == 3) //On Net Selling
			{
				$amount = def_number_format($fob_value, 8, "");
			}
			else
			{
				$currier_pre_cost_o = $currier_pre_cost_o;
				$currier_percent = $currier_cost_percent;
			}
			if($amount>0)
			{
				$currier_pre_cost_o = def_number_format(($amount * ($currier_cost_percent / 100)), 8, "");
				$currier_percent = $currier_cost_percent;
			}
			if($based_on==2 && $fixamount>0)
			{
				if($costing_per==1) $costing_per_amount=12;
				else if($costing_per==2) $costing_per_amount=1;
				else if($costing_per==3) $costing_per_amount=12*2;
				else if($costing_per==4) $costing_per_amount=12*3;
				else if($costing_per==5) $costing_per_amount=12*4;
				else $costing_per_amount=0;
				
				$currier_pre_cost_o=def_number_format((($fixamount/$style_qty)*$costing_per_amount), 8, "");
				$currier_percent = number_format((($currier_pre_cost_o / $price_dzn) * 100), 2,'.','');
			}
		} else {
			$currier_pre_cost_o = $currier_pre_cost_o;
			$currier_percent = $currier_percent;
		}
		//echo "10**=".$currier_pre_cost_o.'='.$currier_percent.'AA';die;

		//==================================Commision==============
		$data_array_commision = sql_select("select id, commision_rate, commission_amount, commission_base_id from  wo_pre_cost_commiss_cost_dtls where job_no=$job_no and commision_rate !=0 and status_active=1 and is_deleted=0");
		if (count($data_array_commision) > 0) {
			$commision_amount_tot = 0;
			$field_array_up_comision = "commission_amount";
			foreach ($data_array_commision as $row_commision)
			{
				if ($row_commision[csf('commission_base_id')] == 1) $commision_amount = ($row_commision[csf('commision_rate')] * $price_dzn) / 100;
				else if ($row_commision[csf('commission_base_id')] == 2) $commision_amount = $row_commision[csf('commision_rate')] * $costing_per_pcs;
				else if ($row_commision[csf('commission_base_id')] == 3)
				{
					if ($costing_per == 1) $commision_amount = $row_commision[csf('commision_rate')] * 1 * 1;
					else if ($costing_per == 2) $commision_amount = $row_commision[csf('commision_rate')] / 12;
					else if ($costing_per == 3) $commision_amount = $row_commision[csf('commision_rate')] * 1 * 2;
					else if ($costing_per == 4) $commision_amount = $row_commision[csf('commision_rate')] * 1 * 3;
					else if ($costing_per == 5) $commision_amount = $row_commision[csf('commision_rate')] * 1 * 4;
					else $commision_amount = 0;
				}

				if ($currency_id == 1) {
					$commision_amount = number_format($commision_amount, 2,'.','');
					$commision_amount_tot = number_format(($commision_amount_tot + $commision_amount), 2,'.','');
				} else {
					$commision_amount = number_format($commision_amount, 6,'.','');
					$commision_amount_tot = number_format(($commision_amount_tot + $commision_amount), 6,'.','');
				}

				$id_arr[] = $row_commision[csf('id')];
				$data_array_up_comision[$row_commision[csf('id')]] = explode(",", ("" . $commision_amount . ""));
			}
			$rID = execute_query(bulk_update_sql_statement("wo_pre_cost_commiss_cost_dtls", "id", $field_array_up_comision, $data_array_up_comision, $id_arr));
		}
		//============================
		$total_cost = ($total_cost_o - $commision_o) + $commision_amount_tot;
		$fob_value = $price_dzn - $commision_amount_tot;
		//==================================Comarcial==============
		$commercial_cost_method = return_field_value("commercial_cost_method", "variable_order_tracking", "company_name=".$company_name." and tna_integrated='$buyer_name' and profit_calculative='$brand_id' and variable_list=27 and status_active=1 and is_deleted=0");
		if ($commercial_cost_method == "" || $commercial_cost_method == 0) {
			$commercial_cost_method = return_field_value("commercial_cost_method", "variable_order_tracking", "company_name=".$company_name." and tna_integrated='$buyer_name' and profit_calculative='0' and variable_list=27 and status_active=1 and is_deleted=0");
		}
		if ($commercial_cost_method == "" || $commercial_cost_method == 0) {
			$commercial_cost_method = return_field_value("commercial_cost_method", "variable_order_tracking", "company_name=".$company_name." and variable_list=27 and status_active=1 and is_deleted=0");
		}
		
		if ($commercial_cost_method == "" || $commercial_cost_method == 0) {
			$commercial_cost_method = 1;
		}

		$amount = 0;
		$operating_expn_value = number_format(($fob_value * $operating_expn_per / 100), 4);
		if ($commercial_cost_method == 1)
		{
			$data_array = sql_select("select (fab_amount+yarn_amount+trim_amount) as amount from wo_pre_cost_sum_dtls where job_no=$job_no and status_active=1 and is_deleted=0");
			foreach ($data_array as $row) {
				$amount = def_number_format($row[csf("amount")], 8, "");
			}
		}
		else if ($commercial_cost_method == 2) $amount = def_number_format($price_dzn, 8, "");
		else if ($commercial_cost_method == 3) $amount = def_number_format($fob_value, 8, "");
		else if ($commercial_cost_method == 5 || $commercial_cost_method == 6 || $commercial_cost_method == 7)
		{
			$amount=0;
			$sql_fab="select amount as amount from wo_pre_cost_fabric_cost_dtls where job_no=$job_no and fabric_source in (1,2) and status_active=1 and is_deleted=0";
			$data_fab=sql_select($sql_fab);
			$fab_amount=0;
			foreach($data_fab as $row )
			{
				$fab_amount+=$row[csf("amount")];
			}
			unset($data_fab);
			$sql_yarn="select amount as amount from wo_pre_cost_fab_yarn_cost_dtls where job_no=$job_no and status_active=1 and is_deleted=0";
			$data_yarn=sql_select($sql_yarn);
			$yarn_amount=0;
			foreach($data_yarn as $row )
			{
				$yarn_amount+=$row[csf("amount")];
			}
			unset($data_yarn);
			$data_array=sql_select("select fabric_cost, trims_cost, embel_cost, wash_cost, lab_test, inspection, cm_cost, freight, currier_pre_cost, certificate_pre_cost, design_cost, studio_cost, common_oh from wo_pre_cost_dtls where job_no=$job_no and status_active=1 and is_deleted=0");
			$ft_amount=0;
			foreach( $data_array as $row )
			{
				if ($commercial_cost_method == 5)
				{
					$ft_amount=$row[csf("trims_cost")]+$row[csf("embel_cost")]+$row[csf("wash_cost")]+$row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("cm_cost")]+$row[csf("freight")]+$currier_pre_cost_o+$row[csf("certificate_pre_cost")]+$row[csf("design_cost")]+$row[csf("studio_cost")]+$operating_expn_value;
				}
				else if ($commercial_cost_method == 6)
				{
					$ft_amount=$row[csf("trims_cost")]+$row[csf("embel_cost")]+$row[csf("wash_cost")]+$row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("freight")]+$currier_pre_cost_o+$row[csf("certificate_pre_cost")]+$row[csf("design_cost")]+$row[csf("studio_cost")]+$operating_expn_value;
				}
				else if ($commercial_cost_method == 7)
				{
					$ft_amount=$row[csf("fabric_cost")]+$row[csf("trims_cost")]+$row[csf("embel_cost")]+$row[csf("wash_cost")]+$row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("cm_cost")]+$row[csf("freight")]+$currier_pre_cost_o+$row[csf("certificate_pre_cost")]+$row[csf("design_cost")]+$row[csf("studio_cost")]+$operating_expn_value;
				}
			}
			unset($data_array);
			if ($commercial_cost_method== 7) $fob_value=$ft_amount;
			else $fob_value=$ft_amount+$yarn_amount+$fab_amount;
			$amount = def_number_format($fob_value, 8, "");
		}

		$tot_com_amount = 0;

		$data_array1 = sql_select("select id, rate from wo_pre_cost_comarci_cost_dtls where job_no=$job_no and status_active=1 and is_deleted=0");
		foreach ($data_array1 as $row1) {
			$com_amount = def_number_format(($amount * ($row1[csf("rate")] / 100)), 8, "");
			$tot_com_amount += $com_amount;
			$rID_de = execute_query("update wo_pre_cost_comarci_cost_dtls set amount=$com_amount where id='" . $row1[csf("id")] . "'", 1);
		}
		//execute_query( "update wo_pre_cost_dtls set comm_cost=$tot_com_amount where job_no =$job_no",1 );
		execute_query("update wo_pre_cost_sum_dtls set comar_amount=$tot_com_amount where job_no =$job_no", 1);
		//============================

		$total_cost = ($total_cost - $commarcial_o) + $tot_com_amount;
		$depreciation_amorti_value = number_format(($fob_value * $depreciation_amorti_per / 100), 6,'.','');
		$total_cost = ($total_cost - $depr_amor_pre_cost_o) + $depreciation_amorti_value;

		$total_cost = ($total_cost - $common_oh_o) + $operating_expn_value;

		$margin_dzn = $price_dzn - $total_cost;

		$margin_pcs = $margin_dzn / $costing_per_pcs;

		if ($currency_id == 1) {
			$price_dzn = number_format($price_dzn, 2,'.','');
			$margin_pcs = number_format($margin_pcs, 2,'.','');
		} else {
			$price_dzn = number_format($price_dzn, 6,'.','');
			$margin_pcs = number_format($margin_pcs, 6,'.','');
		}

		$txt_fabric_po_price_percent = number_format((($fabric_cost_o / $price_dzn) * 100), 2,'.','');
		$txt_trim_po_price_percent = number_format((($trims_cost_o / $price_dzn) * 100), 2,'.','');
		$txt_embel_po_price_percent = number_format((($embel_cost_o / $price_dzn) * 100), 2,'.','');
		$txt_wash_po_price_percent = number_format((($wash_cost_o / $price_dzn) * 100), 2,'.','');
		$txt_comml_po_price_percent = number_format((($tot_com_amount / $price_dzn) * 100), 2,'.','');
		$txt_lab_test_po_price_percent = number_format((($lab_test_o / $price_dzn) * 100), 2,'.','');
		$txt_inspection_po_price_percent = number_format((($inspection_o / $price_dzn) * 100), 2,'.','');
		$txt_cm_po_price_percent = number_format((($cm_cost_o / $price_dzn) * 100), 2,'.','');
		$txt_freight_po_price_percent = number_format((($freight_o / $price_dzn) * 100), 2,'.','');

		$txt_currier_po_price_percent = number_format($currier_percent, 2,'.','');
		$txt_currier_po_cost = number_format($currier_pre_cost_o, 6,'.','');

		$txt_deffdlc_po_price_percent = number_format($deffdlc_percent, 2,'.','');
		$txt_deffdlc_po_cost = number_format($deffdlc_cost, 6,'.','');

		$txt_certificate_po_price_precent = number_format((($certificate_pre_cost_o / $price_dzn) * 100), 2,'.','');
		$txt_common_oh_po_price_percent = number_format((($operating_expn_value / $price_dzn) * 100), 2,'.','');
		$txt_commission_po_price_percent = number_format((($commision_amount_tot / $price_dzn) * 100), 2,'.','');
		$txt_depr_amor_po_price_percent = number_format((($depreciation_amorti_value / $price_dzn) * 100), 2,'.','');
		$txt_total_po_price_percent = number_format((($total_cost / $price_dzn) * 100), 2,'.','');

		$txt_final_price_dzn_po_price_percent = number_format((($price_dzn / $price_dzn) * 100), 2,'.','');
		$txt_margin_dzn_po_price_percent = number_format((($margin_dzn / $price_dzn) * 100), 2,'.','');
		$txt_final_price_pcs_po_price_percent = number_format((($avg_unit_price / $price_pcs_set) * 100), 2,'.','');
		//$txt_margin_pcs_po_price_percent = number_format((($margin_pcs / $price_dzn) * 100), 2);
		$txt_margin_pcs_po_price_percent = number_format((($margin_pcs / $price_pcs_set) * 100), 2,'.',''); // issue no 10695
		$cm_for_shipment_sche = number_format(($margin_dzn + $cm_cost_o), 6,'.','');

		//echo "10**".$txt_currier_po_price_percent."*".$txt_currier_po_cost."*".$txt_deffdlc_po_price_percent."*".$txt_deffdlc_po_cost; die;
		$field_array = "price_pcs_or_set*price_dzn*margin_dzn*margin_pcs_set*commission*comm_cost*depr_amor_pre_cost*total_cost*fabric_cost_percent*trims_cost_percent*embel_cost_percent*wash_cost_percent*comm_cost_percent*commission_percent *lab_test_percent*inspection_percent*cm_cost_percent*freight_percent*currier_percent*currier_pre_cost*deffdlc_percent*deffdlc_cost*certificate_percent*common_oh*common_oh_percent*depr_amor_po_price*total_cost_percent*price_dzn_percent*margin_dzn_percent*price_pcs_or_set_percent*margin_pcs_set_percent*cm_for_sipment_sche";
		$data_array = "'" . $avg_unit_price . "'*'" . $price_dzn . "'*'" . $margin_dzn . "'*'" . $margin_pcs . "'*'" . $commision_amount_tot . "'*'" . $tot_com_amount . "'*'" . $depreciation_amorti_value . "'*'" . $total_cost . "'*'" . $txt_fabric_po_price_percent . "'*'" . $txt_trim_po_price_percent . "'*'" . $txt_embel_po_price_percent . "'*'" . $txt_wash_po_price_percent . "'*'" . $txt_comml_po_price_percent . "'*'" . $txt_commission_po_price_percent . "'*'" . $txt_lab_test_po_price_percent . "'*'" . $txt_inspection_po_price_percent . "'*'" . $txt_cm_po_price_percent . "'*'" . $txt_freight_po_price_percent . "'*'" . $txt_currier_po_price_percent . "'*'" . $txt_currier_po_cost . "'*'" . $txt_deffdlc_po_price_percent . "'*'" . $txt_deffdlc_po_cost . "'*'" . $txt_certificate_po_price_precent . "'*'" . $operating_expn_value . "'*'" . $txt_common_oh_po_price_percent . "'*'" . $txt_depr_amor_po_price_percent . "'*'" . $txt_total_po_price_percent . "'*'" . $txt_final_price_dzn_po_price_percent . "'*'" . $txt_margin_dzn_po_price_percent . "'*'" . $txt_final_price_pcs_po_price_percent . "'*'" . $txt_margin_pcs_po_price_percent . "'*'" . $cm_for_shipment_sche . "'";
		$rID = sql_update("wo_pre_cost_dtls", $field_array, $data_array, "job_no", "" . $job_no . "", 1);
	}
	//update_comarcial_cost($job_no, $company_name);
}

function update_comarcial_cost($job_no, $company_name) {
	
	$sql_job=sql_select("select BUYER_NAME, BRAND_ID from BH_WO_PO_DETAILS_MASTER where job_no=$job_no");
	
	$buyerid=$sql_job[0]["BUYER_NAME"];
	$brandid=$sql_job[0]["BRAND_ID"];
	
	$commercial_cost_method = return_field_value("commercial_cost_method", "variable_order_tracking", "company_name=".$company_name." and variable_list=27 and tna_integrated='$buyerid' and profit_calculative='$brandid' and status_active=1 and is_deleted=0");

	if ($commercial_cost_method == "" || $commercial_cost_method == 0)
	{
		$commercial_cost_method = return_field_value("commercial_cost_method", "variable_order_tracking", "company_name=".$company_name." and variable_list=27 and tna_integrated='$buyerid' and profit_calculative='0' and status_active=1 and is_deleted=0");
	}
		
	if ($commercial_cost_method == "" || $commercial_cost_method == 0) {
		$commercial_cost_method = return_field_value("commercial_cost_method", "variable_order_tracking", "company_name=".$company_name." and variable_list=27 and status_active=1 and is_deleted=0");
	}
	
	if ($commercial_cost_method == "" || $commercial_cost_method == 0) {
		$commercial_cost_method = 1;
	}
	// echo  "10**=".$commercial_cost_method.'='.$job_no.'=A';die;
	$price_dzn = 0; $commission = 0;
	$data_array_pricedzn_comm = sql_select("select price_dzn, commission from wo_pre_cost_dtls where job_no=$job_no and status_active=1 and is_deleted=0");
	foreach ($data_array_pricedzn_comm as $data_array_pricedzn_comm_row) {
		$price_dzn = $data_array_pricedzn_comm_row[csf('price_dzn')];
		$commission = $data_array_pricedzn_comm_row[csf('commission')];
		//$amount=def_number_format($row[csf("amount")],5,"");
	}
	//echo "10**=".$commercial_cost_method.'DD';
	//check_table_status( $_SESSION['menu_id'],0);die;

	$amount = 0;
	if ($commercial_cost_method == 1) {
		$data_array = sql_select("select (fab_amount+yarn_amount+trim_amount) as amount from wo_pre_cost_sum_dtls where job_no=$job_no and status_active=1 and is_deleted=0");
		foreach ($data_array as $row) {
			$amount = def_number_format($row[csf("amount")], 5, "");
		}
	}
	else if ($commercial_cost_method == 2) {
		$amount = def_number_format($price_dzn, 5, "");
	}
	else if ($commercial_cost_method == 3) {
		$amount = def_number_format($price_dzn - $commission, 5, "");
	}
	else if($commercial_cost_method==4)
	{

	}
	else if($commercial_cost_method==5 || $commercial_cost_method==6 || $commercial_cost_method==7 || $commercial_cost_method==9)
	{
		$fab_amount=0;
		$sql_fab=sql_select("SELECT sum(amount) as amount from wo_pre_cost_fabric_cost_dtls WHERE job_no=$job_no and fabric_source=2 and status_active=1 and is_deleted=0");
		if(count($sql_fab) > 0)
		{
			$fab_amount = $sql_fab[0][csf('amount')];
		}
		
		$sql_yarn="select amount as amount from wo_pre_cost_fab_yarn_cost_dtls where job_no=$job_no and status_active=1 and is_deleted=0";
		$data_yarn=sql_select($sql_yarn);
		$yarn_amount=0;
		foreach($data_yarn as $row )
		{
			$yarn_amount+=$row[csf("amount")];
		}
		unset($data_yarn);
		
		$pre_cost_dtls = sql_select("SELECT fabric_cost, trims_cost, embel_cost, wash_cost, lab_test, inspection, cm_cost, freight, currier_pre_cost, certificate_pre_cost, design_cost, studio_cost, common_oh,incentives_pre_cost from wo_pre_cost_dtls WHERE job_no=$job_no ");
		foreach ($pre_cost_dtls as $row) {
			if($commercial_cost_method==5)
			{
				$other_amount = $row[csf('trims_cost')] + $row[csf('embel_cost')] + $row[csf('wash_cost')] + $row[csf('lab_test')] +$row[csf('inspection')] + $row[csf('cm_cost')] + $row[csf('freight')] + $row[csf('currier_pre_cost')] + $row[csf('certificate_pre_cost')] + $row[csf('design_cost')] + $row[csf('studio_cost')] + $row[csf('common_oh')];
			}
			if($commercial_cost_method==6)
			{
				$other_amount = $row[csf('trims_cost')] + $row[csf('embel_cost')] + $row[csf('wash_cost')] + $row[csf('lab_test')] +$row[csf('inspection')] + $row[csf('freight')] + $row[csf('currier_pre_cost')] + $row[csf('certificate_pre_cost')] + $row[csf('design_cost')] + $row[csf('studio_cost')] + $row[csf('common_oh')];
			}
			if($commercial_cost_method==7)
			{
				$other_amount = $row[csf('fabric_cost')] +$row[csf('trims_cost')] + $row[csf('embel_cost')] + $row[csf('wash_cost')] + $row[csf('lab_test')] +$row[csf('inspection')]+ $row[csf('cm_cost')] + $row[csf('freight')] + $row[csf('currier_pre_cost')] + $row[csf('certificate_pre_cost')] + $row[csf('design_cost')] + $row[csf('studio_cost')] + $row[csf('common_oh')]+ $row[csf('incentives_pre_cost')];
			}
			if($commercial_cost_method==9) //Previous issue id=15304 
			{
				$other_amount = $row[csf('fabric_cost')]+$row[csf('trims_cost')]+$row[csf('embel_cost')]+$row[csf('wash_cost')];
			}
		}
		
		if($commercial_cost_method==7 || $commercial_cost_method == 9) $total_amount = $other_amount;
		else $total_amount = $fab_amount +$yarn_amount + $other_amount;
		
		$amount=def_number_format($total_amount,8,"");
		//$ss=$price_dzn - $dd;
	}
	  //Method End-===============
	
	$data_array1=sql_select("select id,rate from wo_pre_cost_comarci_cost_dtls where job_no=$job_no and status_active=1 and is_deleted=0");
		
	$tot_com_amount=0;
	foreach( $data_array1 as $row1 )
	{
		$com_amount=def_number_format(($amount*($row1[csf("rate")]/100)),8,"");
		$tot_com_amount+=$com_amount;
		$rID_de=execute_query( "update  wo_pre_cost_comarci_cost_dtls set amount=$com_amount where id='".$row1[csf("id")]."'",1 );
	}
	//echo "10**=".$tot_com_amount;
	//check_table_status( $_SESSION['menu_id'],0);
	//die;
	execute_query("update wo_pre_cost_dtls set comm_cost=$tot_com_amount where job_no =$job_no", 1);
	execute_query("update wo_pre_cost_sum_dtls set comar_amount=$tot_com_amount where job_no =$job_no", 1);
	return $tot_com_amount;
}

function update_color_size_sequence($txt_job_no, $btn_mood) {
	global $db_type;
	$colororder_by = ""; $sizeorder_by = "";
	if ($btn_mood == 1) {
		$colororder_by = "order by id ASC"; $sizeorder_by = "order by id ASC";
		//$colororder_by="order by color_order, id ASC"; $sizeorder_by="order by size_order, id ASC";
	} else if ($btn_mood == 2) {
		$colororder_by = "order by color_order ASC"; $sizeorder_by = "order by size_order ASC";
	}
	
	if($db_type==0)
	{
		$sqlc ="select color_number_id, color_order as color_order from wo_po_color_size_breakdown where job_no_mst=$txt_job_no and status_active!=0 and is_deleted=0 group by color_number_id, color_order $colororder_by";
	}
	else
	{
		$sqlc ="select min(id) as id, color_number_id, min(color_order) as color_order from wo_po_color_size_breakdown where job_no_mst=$txt_job_no and status_active!=0 and is_deleted=0 group by color_number_id $colororder_by";
	}
	//$sql_data = sql_select("select min(id) as id, color_number_id, min(color_order) as color_order from wo_po_color_size_breakdown where job_no_mst=$txt_job_no and status_active!=0 and is_deleted=0 group by color_number_id $colororder_by");
	//echo "10**".$sqlc; die;
	$sql_data = sql_select($sqlc);
	$color_order = 1;
	foreach ($sql_data as $row) {
		$rID = execute_query("update wo_po_color_size_breakdown set color_order=" . $color_order . " where color_number_id=" . $row[csf('color_number_id')] . " and job_no_mst=$txt_job_no", 0);
		$color_order++;
	}
	unset($sql_data);

	//$sql_size = sql_select("select min(id) as id, size_number_id, min(size_order) as size_order from wo_po_color_size_breakdown where job_no_mst=$txt_job_no and status_active!=0 and is_deleted=0 group by size_number_id $sizeorder_by");
	if($db_type==0)
	{
		$sqls = "select size_number_id, size_order as size_order from wo_po_color_size_breakdown where job_no_mst=$txt_job_no and status_active!=0 and is_deleted=0 group by size_number_id, size_order $sizeorder_by";
	}
	else
	{
		$sqls ="select min(id) as id, size_number_id, min(size_order) as size_order from wo_po_color_size_breakdown where job_no_mst=$txt_job_no and status_active!=0 and is_deleted=0 group by size_number_id $sizeorder_by";
	}
	$sql_size = sql_select($sqls);
	$size_order = 1;
	foreach ($sql_size as $rows) {
		$rID = execute_query("update wo_po_color_size_breakdown set size_order=" . $size_order . " where size_number_id=" . $rows[csf('size_number_id')] . " and job_no_mst=$txt_job_no", 0);
		$size_order++;
	}
}

// Usage--> echo create_list_view( "ID, Buyer Name, Contact No", "40,200,", "select id, buyer_name,contact_no from lib_buyer", "getDataBuyerMst", "0","1", 600, 350, $tbl_border, $is_multi_select, 1 );

function generate_dynamic_report($table_id, $tbl_header_arr, $td_width_arr, $tbl_width, $tbl_height, $tbl_border, $query, $onclick_fnc_name, $onclick_fnc_param_db_arr, $onclick_fnc_param_sttc_arr, $show_sl, $field_printed_from_array_arr, $data_array_name_arr, $qry_field_list_array, $controller_file_path="", $filter_grid_fnc="", $fld_type_arr="", $summary_flds="", $check_box_all="") {
	//$fld_type_arr Definition 0=string,1=integer,2=float,3=date
	$tbl_header = explode(',', $tbl_header_arr);
	$td_width = explode(',', $td_width_arr);
	$onclick_fnc_param_db = explode(',', $onclick_fnc_param_db_arr);

	$field_printed_from_array = explode(',', $field_printed_from_array_arr);
	$data_array_name = count($data_array_name_arr);
	$qry_field_list = explode(',', $qry_field_list_array);

	$fld_type_arr = explode(',', $fld_type_arr);
	if ($summary_flds != "") {
		$summary_flds = explode(',', $summary_flds);
		$summary_total = array();} else {
		$summary_flds = "";
	}

	$table = '<div>
	<table width="' . $tbl_width . '" cellpadding="0" cellspacing="0" border="' . $tbl_border . '" class="rpt_table" rules="all">
	<thead><tr>';

	if ($show_sl == 1) {
		$table .= '<th width="50">SL No</th>';
	}

	for ($i = 0; $i < count($tbl_header); $i++) {
		if ($i < count($tbl_header) - 1) {
			$table .= '<th width="' . $td_width[$i] . '">' . $tbl_header[$i] . '</th>';
		} else {
			$table .= '<th>' . $tbl_header[$i] . '</th>';
		}

	}
	$table .= '</tr></thead>
	';
	$tbl_width1 = $tbl_width - 2;
	$tbl_width = $tbl_width - 20;
	$table .= '</table> <div style="max-height:' . $tbl_height . 'px; width:' . $tbl_width1 . 'px; overflow-y:scroll"><table width="' . $tbl_width . '" height="" cellpadding="0" cellspacing="0" border="' . $tbl_border . '" class="rpt_table" id="' . $table_id . '" rules="all"><tbody>';
	$j = 0;

	if ($controller_file_path == "") {
		$controller_file_path = "";
	} else {
		$controller_file_path = ",'" . $controller_file_path . "'";
	}

	if ($onclick_fnc_param_sttc_arr == "") {
		$onclick_fnc_param_sttc_arr = "";
	} else {
		$onclick_fnc_param_sttc_arr = "," . $onclick_fnc_param_sttc_arr . "";
	}

	$nameArray = sql_select($query);
	foreach ($nameArray as $result) {
		$j++;
		if ($j % 2 == 0) {
			$bgcolor = "#E9F3FF";
		} else {
			$bgcolor = "#FFFFFF";
		}

		$db_param = "";

		if ($onclick_fnc_param_db_arr != "") {
			if ($check_box_all != "") {
				$aid = $j . "_";
			} else {
				$aid = "";
			}

			for ($w = 0; $w < count($onclick_fnc_param_db); $w++) {
				if (count($onclick_fnc_param_db) < 2) {
					$db_param .= "'" . $aid . $result[csf($onclick_fnc_param_db[$w])] . "'";
				} else {
					if ($db_param == "") {
						$db_param .= "'" . $aid . $result[csf($onclick_fnc_param_db[$w])] . "";
					} else {
						$db_param .= "_" . $result[csf($onclick_fnc_param_db[$w])] . "";
					}

					if ($w == count($onclick_fnc_param_db) - 1) {
						$db_param .= "'";
					}

				}
			}
		} else {
			$db_param = "";
			$onclick_fnc_param_sttc_arr = str_replace(",", "", $onclick_fnc_param_sttc_arr);
		}

		//$file_path="'".$file_path."'";
		$aid = "";

		if ($onclick_fnc_name != "") {
			$table .= '<tr height="20" onclick="' . $onclick_fnc_name . '(' . $db_param . '' . $onclick_fnc_param_sttc_arr . '' . $controller_file_path . ')" bgcolor="' . $bgcolor . '" style="cursor:pointer" id="tr_' . $j . '">';
		} else {
			$table .= '<tr height="20" bgcolor="' . $bgcolor . '" id="tr_' . $j . '">';
		}

		if ($show_sl == 1) {
			$table .= '<td width="50" >' . $j . '</td>';
		}

		for ($i = 0; $i < count($qry_field_list); $i++) {
			$show_data = "";

			if (in_array($qry_field_list[$i], $summary_flds)) {
				$summary_total[$qry_field_list[$i]] = $summary_total[$qry_field_list[$i]] + $result[csf($qry_field_list[$i])];
			}

			if ($fld_type_arr[$i] == 0) {
				$align = "align='left'";
				$show_data = ($result[csf($qry_field_list[$i])]);} else if ($fld_type_arr[$i] == 1) {
				$align = "align='right'";
				$show_data = number_format($result[csf($qry_field_list[$i])], '0');
			} else if ($fld_type_arr[$i] == 2) {
				$align = "align='right'";
				$show_data = number_format($result[csf($qry_field_list[$i])], '2');
			} else if ($fld_type_arr[$i] == 3) {
				$align = "align='left'";
				$show_data = change_date_format($result[csf($qry_field_list[$i])]);
			}

			if ($i < count($qry_field_list) - 1) {
				$split = get_split_length($data_array_name_arr[$i][$show_data], $td_width[$i]);
				if ($field_printed_from_array[$i] == $qry_field_list[$i]) {
					$table .= '<td ' . $align . ' width="' . $td_width[$i] . '"><p>' . $data_array_name_arr[$i][$show_data] . '</p></td>';
				} else {
					$split = get_split_length($show_data, $td_width[$i]);
					$table .= '<td  ' . $align . ' width="' . $td_width[$i] . '"><p>' . $show_data . '</p></td>';
				}
			} else {
				$split = get_split_length($data_array_name_arr[$i][$show_data], $td_width[$i]);
				if ($field_printed_from_array[$i] == $qry_field_list[$i]) {
					$table .= '<td ><p>' . $data_array_name_arr[$i][$show_data] . '</p></td>';
				} else {
					$split = get_split_length($show_data, $td_width[$i]);
					$table .= '<td ' . $align . '><p>' . $show_data . '</p></td>';
				}
			}
		}
		$table .= '</tr>';
	}
	$span = 0;
	if (is_array($summary_flds)) {

		for ($i = 0; $i < count($summary_flds); $i++) {
			if ($i == 0) {$table .= '<tfoot><tr><th colspan="' . $summary_flds[$i] . '">Total</th>';} else {
				if ($summary_total[$summary_flds[$i]] != "" || $summary_total[$summary_flds[$i]] != 0) {
					$tot = number_format($summary_total[$summary_flds[$i]], 2);
				} else {
					$tot = "";
				}

				$table .= '<th colspan="' . $span . '" align="right">' . $tot . '</th>'; // $summary_total

			}

		}
		$table .= '</tr></tfoot>';
	}

	if (trim($filter_grid_fnc) != "") {
		$js = '<script>';
		$js .= ' ' . $filter_grid_fnc . ' ';
		$js .= '</script>';
	} else {
		$js = '';
	}

	if ($check_box_all != "") {
		$check = '<div class="check_all_container"><div style="width:100%">
		<div style="width:50%; float:left" align="left">
		<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
		</div>
		<div style="width:50%; float:left" align="left">
		<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
		</div>
		</div></div>';
	}

	$table .= '</tbody></table></div>' . $check . '</div>' . $js;
	return $table;
	die;
}

function create_delete_report_file($contents, $type, $delete_old, $path) {
	//echo $_SESSION['logic_erp']["user_name"]	; die;
	if ($delete_old == 1) {
		foreach (glob($path . "ext_resource/tmp_report/" . $_SESSION['logic_erp']["user_name"] . "*") as $filename) {
			//return  $filename; die;//if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
		}
	}
	if ($contents != "") {
		$name = time();
		if ($type == 1) {
			$filename = $_SESSION['logic_erp']['user_name'] . "_" . $name . ".xls";
		} else if ($type == 2) {
			$filename = $_SESSION['logic_erp']['user_name'] . "_" . $name . ".doc";
		} else if ($type == 3) {
			$filename = $_SESSION['logic_erp']['user_name'] . "_" . $name . ".txt";
		} else if ($type == 4) {
			$filename = $_SESSION['logic_erp']['user_name'] . "_" . $name . ".pdf";
		}
		$filename = $path . 'ext_resource/tmp_report/' . $filename;

		$create_new_doc = fopen($filename, 'w');
		if (fwrite($create_new_doc, $contents)) {
			return $filename;
		} else {
			return $filename;
		}
		die;

	}
}

function check_magic_quote_gpc($data) {
	//	delete_priv 	save_priv 	edit_priv 	approve_priv
	//	print_r( $data[0]['operation']);
	//return $data[0]['operation'];die;
	//	die;
	if (trim($data[0]['operation']) == '') {
		$op = 99;
	} else if ($data[0]['operation'] == 0) {
		$op = "save_priv";
	} else if ($data[0]['operation'] == 1) {
		$op = "edit_priv";
	} else if ($data[0]['operation'] == 2) {
		$op = "delete_priv";
	} else if ($data[0]['operation'] == 3) {
		$op = "approve_priv";
	} else if ($data[0]['operation'] == 5) {
		$op = 99;
	}

	if ($op != 99) {
		if (return_field_value($op, "user_priv_mst", "user_id='" . $_SESSION['logic_erp']['user_id'] . "' and main_menu_id='" . $_SESSION['menu_id'] . "'", $op, $new_conn) != 1) {
			return '';
		}
	}

	if (get_magic_quotes_gpc()) {

		$data_array = array();
		$process = array(&$_POST);
		while (list($key, $val) = each($process)) {
			foreach ($val as $k => $v) {

				$data_array[$k] = stripslashes($v);
				unset($process[$key][$k]);
			}
		}
		return $data_array;
	} else {
		$data_array = array();
		$process = array(&$_POST);
		while (list($key, $val) = each($process)) {
			foreach ($val as $k => $v) {

				$data_array[$k] = ($v);
				unset($process[$key][$k]);
			}
		}
		return $data_array;
	}
} 

function signature_table($report_id, $company, $width, $template_id="", $padding_top = 70,$prepared_by='',$userSignatureArr=array(),$break_tr=7, $custom_style='') {
	
	if ($template_id != '') {
		$template_id = " and template_id=$template_id ";
	}
	 
	$sql = sql_select("select USER_ID,DESIGNATION,NAME,ACTIVITIES,PREPARED_BY,GROUP_BY from variable_settings_signature where report_id=$report_id and company_id=$company   and status_active=1 $template_id order by sequence_no ");
	$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
	
	

	if($sql[0]["PREPARED_BY"]==1){
		list($prepared_by,$activities)=explode('**',$prepared_by);
		$CUSTOM_DESIGNATION = return_field_value("b.CUSTOM_DESIGNATION", "user_passwd a,LIB_DESIGNATION b", "b.id=a.DESIGNATION and a.id=$prepared_by", "CUSTOM_DESIGNATION");
		$sql_2[100] = array ( 'USER_ID'=>$prepared_by,'DESIGNATION' => 'Prepared By' ,'NAME' => ((($user_lib_name[$prepared_by])?$user_lib_name[$prepared_by]:$prepared_by)."<br>".$CUSTOM_DESIGNATION), 'ACTIVITIES' =>$activities, 'PREPARED_BY' => 0 );
		$sql=$sql_2+$sql;
	}
	
	$count = count($sql);
	$td_width = floor($width / $count);
	$standard_width = $count * 150;
	if ($standard_width > $width) {
		$td_width = 150;
	}
	$no_coloumn_per_tr = floor($width / $td_width);
	$i = 1;
	if ($count == 0) {$message = "<b>Note: This is Software Generated Copy , Signature is not Required.</b>";}
	echo '<table cellspacing="5" id="signatureTblId" width="' . $width . '" style="padding-top:' . $padding_top . 'px; '.$custom_style.'"><tr><td width="100%" height="' . $padding_top . '" colspan="' . $count . '">' . $message . '</td></tr>';
	$flag=0;
	foreach ($sql as $row) {
		$flag++;
		$sigHtml='';
		if($userSignatureArr[$row['USER_ID']]){$sigHtml='<img src="'.$userSignatureArr[$row['USER_ID']].'" height="40">';}
		else{$sigHtml='<div height="40"></div>';}
		if($flag==1){echo "<tr>";}
		
		echo '<td width="' . $td_width . '" align="center" valign="top">
		<p style="min-height:40px;">'.$sigHtml.'</p>
		<strong>' . $row[csf("activities")] . '</strong><br>
		<strong style="text-decoration:overline">' . $row[csf("designation")] . "</strong><br>" . $row[csf("name")] . '</td>';
		if($flag==$break_tr || $count==$i){echo "</tr>";$flag=0;}
		$i++;
	}
	echo '</table>';
}

function get_app_signature($report_id, $company, $width, $template_id="", $padding_top = 70,$prepared_by='',$userSignatureArr=array(),$break_tr=7) {

	
	if ($template_id != '') {
		$template_id = " and template_id=$template_id ";
	}
	$sql = sql_select("select USER_ID,DESIGNATION,NAME,ACTIVITIES,PREPARED_BY,GROUP_BY from variable_settings_signature where report_id=$report_id and company_id=$company   and status_active=1 $template_id order by sequence_no ");
	$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");


	//$app_data_arr = "select  b.APPROVED_BY,a.GROUP_NO from ELECTRONIC_APPROVAL_SETUP a,APPROVAL_MST b where USER_ID=b.APPROVED_BY and a.company_id=$company and a.ENTRY_FORM=59 and b.mst_id = 3326 group by b.APPROVED_BY,a.GROUP_NO";

	

	if($sql[0]["PREPARED_BY"]==1){
		list($prepared_by, $activities) = explode('**', $prepared_by);
		$sql_2[100] = array ( 'USER_ID'=>$prepared_by,'DESIGNATION' => 'Prepared By' ,'NAME' => ($user_lib_name[$prepared_by])?$user_lib_name[$prepared_by]:$prepared_by, 'ACTIVITIES' =>$activities, 'PREPARED_BY' => 0 );
		$sql=$sql_2+$sql;
	}

	//print_r($sql_2);die;
	
	$count = count($sql);
	$td_width = floor($width / $count);
	$standard_width = $count * 150;
	if ($standard_width > $width) {
		$td_width = 150;
	}
 
	$i = 1;
	if ($count == 0) {$message = "<b>Note: This is Software Generated Copy , Signature is not Required.</b>";}
	echo '<table cellspacing="5" id="signatureTblId" width="' . $width . '" style="padding-top:' . $padding_top . 'px;"><tr><td width="100%" height="' . $padding_top . '" colspan="' . $count . '">' . $message . '</td></tr>';
	$flag=0;
	foreach ($sql as $row) {
		$flag++;
		$sigHtml='';
		if($userSignatureArr[$row['USER_ID']]){$sigHtml=$userSignatureArr[$row['USER_ID']];}
		else{$sigHtml='<div height="40"></div>';}
		if($flag==1){echo "<tr>";}
		
		echo '<td width="' . $td_width . '" align="center" valign="bottom">
		<div style="min-height:40px;">'.$sigHtml.'</div>
		<strong style="text-decoration:overline">' . $row[csf("designation")] . "</strong></td>";

		if($flag==$break_tr || $count==$i){echo "</tr>";$flag=0;}
		$i++;
	}
	echo '</table>';
}

function get_group_app_signature($dataArr = array()) {

	//$dataArr = array('report_id' => 1,'company_id' => 1,'template_id' => 0,'width' => 0,'padding_top' => 70,'break_tr' => 7,'app_entry_form' => 27,'sys_id' => 1204,'prepared_by' => 1);
 
	$width = $dataArr['width'];
	$padding_top = ($dataArr['padding_top'])?$dataArr['padding_top']:70;
	$break_tr = ($dataArr['break_tr'])?$dataArr['break_tr']:7;
	$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
	
	// $userSql = "select b.ID, b.USER_FULL_NAME,c.CUSTOM_DESIGNATION from USER_PASSWD b,LIB_DESIGNATION c where b.DESIGNATION=c.id and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0";
	// $userSqlRes = sql_select($userSql);
	// foreach($userSqlRes as $urow){
	// 	$user_lib_name[$urow['ID']]	= $urow['USER_FULL_NAME'];
	// 	$user_desig_name[$urow['ID']] = $urow['CUSTOM_DESIGNATION'];
	// }
	
	if ($dataArr['template_id']){
		$where_con = " and template_id= {$dataArr['template_id']}";
	}
	
	$signature_setting_sql = "select USER_ID,DESIGNATION,NAME,ACTIVITIES,PREPARED_BY,SEQUENCE_NO,GROUP_BY as GROUP_NO from variable_settings_signature where report_id={$dataArr['report_id']} and company_id={$dataArr['company_id']} and status_active=1 $where_con order by SEQUENCE_NO ";
	  //echo $signature_setting_sql;die;
	$signature_setting_data_arr = sql_select($signature_setting_sql);

	$signature_data_arr = array();$setting_signature_group_data_arr = array();$signature_group_seq_arr = array();
	if($signature_setting_data_arr[0]['PREPARED_BY']==1){
		$signature_data_arr[0] = $dataArr['prepared_by'];
		$setting_signature_group_data_arr[0] = array ( 'SEQUENCE_NO'=>0, 'GROUP_NO'=>0,'USER_ID'=>$dataArr['prepared_by'],'DESIGNATION' => 'Prepared By' ,'NAME' => $user_lib_name[$dataArr['prepared_by']], 'ACTIVITIES' =>0, 'PREPARED_BY' => 0,'APPROVED_DATE' =>$dataArr['prepared_date'] );
	}

	foreach($signature_setting_data_arr as $sigRow){
		$setting_signature_group_data_arr[$sigRow['SEQUENCE_NO']] = $sigRow;
		$signature_group_seq_arr[$sigRow['GROUP_NO']] = $sigRow['SEQUENCE_NO'];
		$signature_data_arr[$sigRow['SEQUENCE_NO']] = $sigRow['USER_ID'];
	}

 
	$app_data_sql = "select  b.APPROVED_BY as USER_ID,b.APPROVED,b.APPROVED_DATE,a.GROUP_NO from ELECTRONIC_APPROVAL_SETUP a,APPROVAL_MST b where a.USER_ID=b.APPROVED_BY and a.company_id={$dataArr['company_id']} and a.ENTRY_FORM={$dataArr['app_entry_form']} and b.mst_id = {$dataArr['sys_id']} group by b.APPROVED_BY, b.APPROVED, a.GROUP_NO,b.APPROVED_DATE ORDER BY a.GROUP_NO";
	//echo $app_data_sql;die;
	$app_data_arr = sql_select($app_data_sql);
	foreach($app_data_arr as $appRow){
		if($signature_group_seq_arr[$appRow['GROUP_NO']] > 0){ 
			$appRow['DESIGNATION']=$setting_signature_group_data_arr[$signature_group_seq_arr[$appRow['GROUP_NO']]]['DESIGNATION'];
			$signature_data_arr[$signature_group_seq_arr[$appRow['GROUP_NO']]] = $appRow['USER_ID'];
			$setting_signature_group_data_arr[$signature_group_seq_arr[$appRow['GROUP_NO']]] = $appRow;
		}
	}



	$count = count($signature_data_arr);
	$td_width = floor($width / $count);
	$standard_width = $count * 150;
	if ($standard_width > $width) {
		$td_width = 150;
	}
 
	$i = 1;
	if ($count == 0) {$message = "<b>Note: This is Software Generated Copy , Signature is not Required.</b>";}
	echo '<table cellspacing="5" id="signatureTblId" width="' . $width . '" style="padding-top:' . $padding_top . 'px;"><tr><td width="100%" height="' . $padding_top . '" colspan="' . $count . '">' . $message . '</td></tr>';
	$flag=0;
	foreach ($signature_data_arr as $seq_id => $user_id) {
		$flag++;
		//$sigHtml='';
		//if($userSignatureArr[$row['USER_ID']]){$sigHtml=$userSignatureArr[$row['USER_ID']];}
		//else{$sigHtml='<div height="40"></div>';}
		if($flag==1){echo "<tr>";}
		
		echo '<td width="' . $td_width . '" align="center" valign="bottom">
		<div><b>'.$user_lib_name[$user_id].'</b></div>
		<div style="font-size:11px;">'.$setting_signature_group_data_arr[$seq_id]['APPROVED_DATE'].'</div>
		<strong style="text-decoration:overline">'.$setting_signature_group_data_arr[$seq_id]['DESIGNATION'].'</strong></td>';

		if($flag==$break_tr || $count==$i){echo "</tr>";$flag=0;}
		$i++;
	}
	echo '</table>';
}



function integration_params_inter_com($type,$company_id) {
	$nameArray = sql_select("select id,project_name,database_name,server_name,ip_address,login_name,login_password,admin_mail,server_id,port_no from lib_integration_variables where project_name=$type and company_id=$company_id");
	//$integration_array=array();
	foreach ($nameArray as $result) {
		$new_conn = $result[csf("server_name")] . "*" . $result[csf("login_name")] . "*" . $result[csf("login_password")] . "*" . $result[csf("database_name")];
		return $new_conn;die;
	}
}


function integration_params($type) {
	$nameArray = sql_select("select id,project_name,database_name,server_name,ip_address,login_name,login_password,admin_mail,server_id,port_no from lib_integration_variables where project_name=$type");
	//$integration_array=array();
	foreach ($nameArray as $result) {
		$new_conn = $result[csf("server_name")] . "*" . $result[csf("login_name")] . "*" . $result[csf("login_password")] . "*" . $result[csf("database_name")];
		return $new_conn;die;
		/*$integration_array['database_name']=$result[csf("database_name")];
			$integration_array['server_name']=$result[csf("server_name")];
			$integration_array['login_name']=$result[csf("login_name")];
			$integration_array['login_password']=$result[csf("login_password")];
			$integration_array['ip_address']=$result[csf("ip_address")];
			$integration_array['admin_mail']=$result[csf("admin_mail")];
			$integration_array['server_id']=$result[csf("server_id")];
			$integration_array['port_no']=$result[csf("port_no")];
		*/
	}
}

function month_add($orgDate, $mon) {
	$cd = strtotime($orgDate);
	//$retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,date('d',$cd),date('Y',$cd)));
	$retDAY = date('Y-m-d', mktime(0, 0, 0, date('m', $cd) + $mon, 1, date('Y', $cd)));
	return $retDAY;
} 

function show_company($company_id, $show_cap="", $fldlist="") {
	$fldarray = array("plot_no" => "plot_no", "level_no" => "level_no", "road_no" => "road_no", "block_no" => "block_no", "city" => "city", "zip_code" => "zip_code", "province" => "province", "country_id" => "country_id", "email" => "email", "website" => "website", "vat_number" => "vat_number");

	if (!is_array($fldlist)) {
		$fldlist = $fldarray;
	}

	$nameArray = sql_select("select a.plot_no, a.level_no, a.road_no, a.block_no, b.country_name as country_id, a.province, a.city, a.zip_code, a.email, a.website, a.vat_number from lib_company a left join  lib_country b on a.country_id =b.id where a.id=$company_id and a.status_active=1 and a.is_deleted=0");
	foreach ($nameArray as $result) {
		foreach ($fldarray as $fld) {
			if (in_array($fld, $fldlist)) {
				if (trim($result[csf($fld)]) != "") {
					if ($show_cap == 1) {
						$address .= ucwords(str_replace("_", " ", $fld)) . "-";
						$address .= " " . trim($result[csf($fld)]);
					} else {
						$address .= " " . trim($result[csf($fld)]);
					}

					if ($address != '') {
						$address .= ",";
					}
				}
			}
		}
	}
	return $address;
}

function convertToInt($fiel, $repValue = array(), $aliase="") {
	if ($db_type == 0) {
		$repValue = implode('|', $repValue);
		return " TO_NUMBER( REGEXP_REPLACE($fiel,'$repValue',NULL),9999999999999)  as  $aliase ";
	} else {
		$replace = '';
		foreach ($repValue as $val) {
			if ($replace == '') {$replace = "REPLACE($fiel,'$val','')";} else { $replace .= "REPLACE($replace,'$val','')";}
		}
		return $replace;
	}
}

//for bundle production.................
function production_validation($mst_id, $next_op) {
	$next_op = explode("_", $next_op);
	if ($next_op[1] == '') {
		$bundle = sql_select("SELECT BUNDLE_NO FROM PRO_GARMENTS_PRODUCTION_DTLS
			WHERE BUNDLE_NO IN ( SELECT BUNDLE_NO FROM PRO_GARMENTS_PRODUCTION_DTLS WHERE DELIVERY_MST_ID=$mst_id )
			and PRODUCTION_TYPE=$next_op[0] and status_active=1 and is_deleted=0 ");
	} else {
		$bundle = sql_select("SELECT BUNDLE_NO FROM PRO_GARMENTS_PRODUCTION_DTLS A, PRO_GARMENTS_PRODUCTION_MST B
			WHERE A.BUNDLE_NO IN ( SELECT BUNDLE_NO FROM PRO_GARMENTS_PRODUCTION_DTLS WHERE DELIVERY_MST_ID=$mst_id )
			and A.PRODUCTION_TYPE=$next_op[0] and B.embel_name=$next_op[1] and A.mst_id=B.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	}

	foreach ($bundle as $row) {
		$ids[$row[csf("BUNDLE_NO")]] = $row[csf("BUNDLE_NO")];
	}
	return $ids;
}
//for bundle production.................
function production_data($mst_id, $next_op) {

	$next_op = explode("_", $next_op);

	if ($next_op[1] != '') {
		$str = " and a.embel_name=" . $next_op[1];
	}

	$bundle = sql_select("SELECT a.po_break_down_id,a.country_id,a.item_number_id,b.cut_no,b.production_qnty, b.barcode_no,b.is_rescan ,b.bundle_no,b.color_size_break_down_id from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.delivery_mst_id=$mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.production_type=$next_op[0] $str");
	foreach ($bundle as $row) {
		$dataArr[$row[csf("BUNDLE_NO")]] = $row;
	}
	return $dataArr;
}

function check_operation_status($col_id, $order_id, $job_no, $cutting_no, $hidden_barcode, $sequence_array) {
	$precost_job = return_field_value("a.job_no", "wo_pre_cost_mst a", "a.job_no='" . $job_no . "' and a.status_active=1 and a.is_deleted=0 ", "job_no");

	if (!empty($precost_job)) {
		$sql_extrawork = sql_select("select b.id,b.emb_name,a.item_number_id from wo_pre_cos_emb_co_avg_con_dtls a,wo_pre_cost_embe_cost_dtls b  where a.job_no=b.job_no and b.emb_name in (1,2,4) and b.job_no='" . $job_no . "' and a.pre_cost_emb_cost_dtls_id=b.id   and color_size_table_id in (" . $col_id . ") and a.requirment>0  and b.status_active=1 and b.is_deleted=0 group by b.emb_name,b.id,a.item_number_id order by b.id ASC");

		$first_op_arr = array();
		if (count($sql_extrawork) > 0) {
			$i = 1;

			foreach ($sql_extrawork as $row) {
				$current_op = $row[csf("emb_name")];
				if ($row[csf("emb_name")] == 1) //print
				{
					$preceding = "3_1";
					$succeding = "2_1";
				}
				if ($row[csf("emb_name")] == 2) {
					$preceding = "3_2";
					$succeding = "2_2"; // $first_op_arr[$row[csf("item_number_id")]]=$row[csf("emb_name")];
				}
				if ($row[csf("emb_name")] == 4) {
					$preceding = "3_4";
					$succeding = "2_4"; // $first_op_arr[$row[csf("item_number_id")]]=$row[csf("emb_name")];
				}

				if ($i == 1) {
					$sequence_array[$col_id]["9" . $current_op]['preceding'] = 1;
				} else {
					$sequence_array[$col_id]["9" . $current_op]['preceding'] = $last_preceding;
				}

				if ($last_op != '') {
					$sequence_array[$col_id]["9" . $last_op]['succeding'] = $succeding;
				}

				if ($i == count($sql_extrawork)) {
					$sequence_array[$col_id]["9" . $current_op]['succeding'] = 4;
				} else {
					$sequence_array[$col_id]["9" . $current_op]['succeding'] = $succeding;
				}

				$last_succeding = $succeding;
				$last_preceding = $preceding;
				$last_op = $current_op;
				$i++;

				$sequence_array[$col_id]["9" . $current_op]['job_no'] = $job_no;
				$sequence_array[$col_id]["9" . $current_op]['po_no'] = $order_id;
				$sequence_array[$col_id]["9" . $current_op]['cut_no'] = $cutting_no;
			}
			$sequence_array[$col_id][4]['succeding'] = 5;
			$sequence_array[$col_id][4]['preceding'] = $preceding;
			$sequence_array[$col_id][4]['job_no'] = $job_no;
			$sequence_array[$col_id][4]['po_no'] = $order_id;
			$sequence_array[$col_id][4]['cut_no'] = $cutting_no;

			$sequence_array[$col_id][5]['succeding'] = 6;
			$sequence_array[$col_id][5]['preceding'] = 4;
			$sequence_array[$col_id][5]['job_no'] = $job_no;
			$sequence_array[$col_id][5]['po_no'] = $order_id;
			$sequence_array[$col_id][5]['cut_no'] = $cutting_no;

		} else {
			$sequence_array[$col_id][4]['succeding'] = 5;
			$sequence_array[$col_id][4]['preceding'] = 1;
			$sequence_array[$col_id][4]['job_no'] = $job_no;
			$sequence_array[$col_id][4]['po_no'] = $order_id;
			$sequence_array[$col_id][4]['cut_no'] = $cutting_no;

			$sequence_array[$col_id][5]['succeding'] = 6;
			$sequence_array[$col_id][5]['preceding'] = 4;
			$sequence_array[$col_id][5]['job_no'] = $job_no;
			$sequence_array[$col_id][5]['po_no'] = $order_id;
			$sequence_array[$col_id][5]['cut_no'] = $cutting_no;
		}

	} else // Job Table
	{
		$sql_extrawork = sql_select(" select gmts_item_id ,job_no,embelishment,printseq, embro,embroseq, spworks, spworksseq from wo_po_details_mas_set_details where  job_no='" . $job_no . "'");
		if (count($sql_extrawork) > 0) {
			$last_operation = array();
			foreach ($sql_extrawork as $val) {
				$print_sequence = $val[csf("printseq")];
				$emblishment_sequence = $val[csf("embroseq")] * 1;
				$spwork_sequence = $val[csf("spworksseq")] * 1;
				$tmparr[$print_sequence] = 1;
				$tmparr[$emblishment_sequence] = 2;
				$tmparr[$spwork_sequence] = 4;
				ksort($tmparr);

				if ($spwork_sequence == 0 && $emblishment_sequence == 0 && $print_sequence == 0) {
					$tmparr = array();
				}

				if (count($tmparr) > 0) {
					foreach ($tmparr as $embel_name) {
						$current_op = $embel_name;
						if ($embel_name == 1) //print
						{
							$preceding = "3_1";
							$succeding = "2_1";
						}
						if ($embel_name == 2) {
							$preceding = "3_2";
							$succeding = "2_2"; // $first_op_arr[$row[csf("item_number_id")]]=$row[csf("emb_name")];
						}
						if ($embel_name == 4) {
							$preceding = "3_4";
							$succeding = "2_4"; // $first_op_arr[$row[csf("item_number_id")]]=$row[csf("emb_name")];
						}

						if ($i == 1) {
							$sequence_array[$col_id]["9" . $current_op]['preceding'] = 1;
						} else {
							$sequence_array[$col_id]["9" . $current_op]['preceding'] = $last_preceding;
						}

						if ($last_op != '') {
							$sequence_array[$col_id]["9" . $last_op]['succeding'] = $succeding;
						}

						if ($i == count($sql_extrawork)) {
							$sequence_array[$col_id]["9" . $current_op]['succeding'] = 4;
						} else {
							$sequence_array[$col_id]["9" . $current_op]['succeding'] = $succeding;
						}

						$last_succeding = $succeding;
						$last_preceding = $preceding;
						$last_op = $current_op;
						$i++;

						$sequence_array[$col_id]["9" . $current_op]['job_no'] = $job_no;
						$sequence_array[$col_id]["9" . $current_op]['po_no'] = $order_id;
						$sequence_array[$col_id]["9" . $current_op]['cut_no'] = $cutting_no;
					}
					$sequence_array[$col_id][4]['succeding'] = 5;
					$sequence_array[$col_id][4]['preceding'] = $preceding;
					$sequence_array[$col_id][4]['job_no'] = $job_no;
					$sequence_array[$col_id][4]['po_no'] = $order_id;
					$sequence_array[$col_id][4]['cut_no'] = $cutting_no;

					$sequence_array[$col_id][5]['succeding'] = 6;
					$sequence_array[$col_id][5]['preceding'] = 4;
					$sequence_array[$col_id][5]['job_no'] = $job_no;
					$sequence_array[$col_id][5]['po_no'] = $order_id;
					$sequence_array[$col_id][5]['cut_no'] = $cutting_no;
				} else {
					$sequence_array[$col_id][4]['succeding'] = 5;
					$sequence_array[$col_id][4]['preceding'] = 1;
					$sequence_array[$col_id][4]['job_no'] = $job_no;
					$sequence_array[$col_id][4]['po_no'] = $order_id;
					$sequence_array[$col_id][4]['cut_no'] = $cutting_no;

					$sequence_array[$col_id][5]['succeding'] = 6;
					$sequence_array[$col_id][5]['preceding'] = 4;
					$sequence_array[$col_id][5]['job_no'] = $job_no;
					$sequence_array[$col_id][5]['po_no'] = $order_id;
					$sequence_array[$col_id][5]['cut_no'] = $cutting_no;
				}
			}
		} else {
			$sequence_array[$col_id][4]['succeding'] = 5;
			$sequence_array[$col_id][4]['preceding'] = 1;
			$sequence_array[$col_id][4]['job_no'] = $job_no;
			$sequence_array[$col_id][4]['po_no'] = $order_id;
			$sequence_array[$col_id][4]['cut_no'] = $cutting_no;

			$sequence_array[$col_id][5]['succeding'] = 6;
			$sequence_array[$col_id][5]['preceding'] = 4;
			$sequence_array[$col_id][5]['job_no'] = $job_no;
			$sequence_array[$col_id][5]['po_no'] = $order_id;
			$sequence_array[$col_id][5]['cut_no'] = $cutting_no;
		}
	}
	return $sequence_array;
}

function gmt_production_validate_qnty($last_operation, $bundleNo, $qty) {
	$bundl = '';
	foreach ($last_operation as $item_id => $operation_cond) {
		if ($operation_cond != 0) {
			$operation_conds = " and color_size_break_down_id in (" . ltrim($operation_cond, ",") . ") ";
		}

		$sql_check = sql_select("select bundle_no,sum(production_qnty) as production_qnty  from pro_garments_production_dtls c, pro_garments_production_mst a  where  bundle_no='" . $bundleNo . "'   $operation_conds and a.id=c.mst_id   $item_id  and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by c.bundle_no");
		foreach ($sql_check as $chkrow) {
			if (($chkrow[csf("production_qnty")] - $qty) > 0) {
				$bundl = $chkrow[csf("bundle_no")];
			}
		}
	}
	return $bundl;
}

// this data will come from variable settings to control production sequence
$production_squence = 2;
function gmt_production_validation_script($opcode, $is_preceding, $colorSizeid, $cutting_no, $production_squence) {
	$last_operation = array();
	global $production_squence;
	if ($colorSizeid != '') {
		$colorS = " and col_size_id='" . $colorSizeid . "'";
	}

	if ($cutting_no != '') {
		$cutting = " and cutting_no='" . $cutting_no . "'";
	} else {
		return $last_operation;
	}

	if ($production_squence == 1) // precoting sequence
	{
		if ($cutting_no != '') {
			$cutting = " and cutting_no='" . $cutting_no . "'";
		} else {
			return $last_operation;
		}

		$sql_check = sql_select("select preceding_op,succeding_op,embel_name,col_size_id from pro_production_sequence where current_operation=$opcode $colorS $cutting");
	} else {
		if ($is_preceding == 1) {
			$str = " and c.production_type=1 ";
		}elseif ($is_preceding == 9) {
			$str = " and c.production_type=9 ";
		} else {
			$str = " and c.production_type=4 ";
		}

		$last_operation[$str] = 0;
		return $last_operation;
	}

	foreach ($sql_check as $chkrow) {
		$str = '';
		if ($is_preceding == 1) {
			if (($chkrow[csf("embel_name")] * 1) == 0) {
				$str = " and c.production_type=" . $chkrow[csf("preceding_op")];
			} else {
				$str = " and c.production_type=" . $chkrow[csf("preceding_op")] . " and a.embel_name=" . $chkrow[csf("embel_name")];
			}

			$last_operation[$str] .= "," . $chkrow[csf("col_size_id")];
		} else {
			$embl = explode("_", $chkrow[csf("succeding_op")]);

			if (($embl[1] * 1) == 0) {
				$str = " and c.production_type=" . $embl[0];
			} else {
				$str = " and c.production_type=" . $embl[0] . " and a.embel_name=" . $embl[1];
			}

			$last_operation[$str] .= "," . $chkrow[csf("col_size_id")];
		}
	}
	return $last_operation;
}

function get_spacial_instruction($mst_id, $width = "100%", $entry_form = '') {
	$html = '
	<table  width=' . $width . ' class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
	<thead>
		<tr style="border:1px solid black;">
			<th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Special Instruction</th>
		</tr>
	</thead>
	<tbody>';

	if ($entry_form != '') {$entry_form_con = " and entry_form=$entry_form";}

	$data_array = sql_select("select id, terms,terms_prefix from  wo_booking_terms_condition where booking_no='" . str_replace("'", "", $mst_id) . "' $entry_form_con   order by id");
	if (count($data_array) > 0) {
		$i = 0;
		foreach ($data_array as $row) {
			$i++;
			$html .= '
			<tr id="settr_1" align="" style="border:1px solid black;">
				<td style="border:1px solid black;">' . $i . '</td>
				<td style="border:1px solid black;">' . $row[csf('terms')] . '</td>
			</tr>';
		}
	}

	$html .= '
	</tbody>
	</table>';
	if ($mst_id) {return $html;} else {return "";}
}


function getTermsConditions($mst_id, $width = "100%", $entry_form = '') {
	$html = '
	<table  width=' . $width . ' border="0" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<td colspan="3" height="30" valign="middle"><u><b>Terms & Conditions:</b></u></td>
			</tr>
		</thead>
	<tbody>';

	if ($entry_form != '') {$entry_form_con = " and entry_form=$entry_form";}

	$data_array = sql_select("select id, terms,terms_prefix from  wo_booking_terms_condition where booking_no='" . str_replace("'", "", $mst_id) . "' $entry_form_con   order by id");
	if (count($data_array) > 0) {
		$i = 0;
		foreach ($data_array as $row) {
			$i++;
			$html .= '
			<tr id="settr_1">
				<td width="3%">' . $i . ')</td>
				<td>' . $row[csf('terms_prefix')] . '</td>
				<td>' . $row[csf('terms')] . '</td>
			</tr>';
		}
	}

	$html .= '
	</tbody>
	</table>';
	if ($mst_id) {return $html;} else {return "";}
}


function removenumeric($arr) {
	foreach ($arr as $key => $value) {
		if (is_int($key)) {
			unset($arr[$key]);
		}
	}
	return $arr;
}

function return_itemWise_usdRate($prod_id) {
	// This function will Return Item Avg Rate (usd)
	if ($prod_id) {
		$currency_conversion = sql_select("select id, conversion_rate from currency_conversion_rate where currency=2 and status_active=1 and rownum <2 order by id desc");
		$lib_conversion_rate = $currency_conversion[0][csf("conversion_rate")];
		$prod_data = sql_select("select avg_rate_per_unit from product_details_master where item_category_id=1 and id=$prod_id");
		$avg_rate_per_unit = $prod_data[0][csf("avg_rate_per_unit")];
		$avg_rate_usd = 0;
		if ($lib_conversion_rate > 0 && $avg_rate_per_unit > 0) {
			$avg_rate_usd = $avg_rate_per_unit / $lib_conversion_rate;
		}
		return $avg_rate_usd;
	}
}

/**
 * load_room_rack_self_bin - This function is responsible for returning floor/room/rack/shelf/bin dropdown component
 * @param  string  	$controller 	underscore seperated data according to search
 * @param  string  	$data   		defining the action path
 * @return string           		dropdown component of floor/roo,/rack/self/bin according to search
 */
/*function load_room_rack_self_bin($controller, $data, $selector = "") {
	$data = explode("*", $data);
	//echo $controller.test.$data[9];die;
	//echo $data[9];
	$action_type = $data[0];
	$element_id = ($data[9] == "undefined") ? 0 : 1;
	$display_orientation = ($data[14] != '') ? "$data[14]" : "";
	$item_category = implode(",", array_unique(explode("_", $data[10])));
	$element_sl 	 = ($data[12] != '') ? "$data[12]" : "";
	$element_seq 	 = ($data[12] != '') ? "_$data[12]" : "";
	$company_id_element = ($element_id != 0) ? (($data[0] == "undefined") ? "cbo_company_id" : ($display_orientation=="H")?"cbo_company_id":"cbo_company_id_to") : "cbo_company_id";
	$location_id_element = ($element_id != 0) ? (($data[0] == "undefined") ? "cbo_location" : ($display_orientation=="H")?"cbo_location":"cbo_location_to") : "cbo_location";
	$store_id_element = ($element_id != 0) ? (($display_orientation=="H")?"cbo_store_name":"cbo_store_name_to") : "cbo_store_name";
	$floor_id_element = ($element_id != 0) ? (($display_orientation=="H")?"cbo_floor_to".$element_seq:"cbo_floor_to") : "cbo_floor";
	$room_id_element = ($element_id != 0) ? (($display_orientation=="H")?"cbo_room_to".$element_seq:"cbo_room_to") : "cbo_room";
	$rack_id_element = ($element_id != 0) ? (($display_orientation=="H")?"txt_rack_to".$element_seq:"txt_rack_to") : "txt_rack";
	$custom_function = ($data[11] != '') ? "$data[11]" : "";
	$element_width = ($data[13] != '') ? "$data[13]" : "";
	$el_width = ($element_width=="") ? "152" : $element_width;

	if ($action_type == "store") {
		//========== user credential start ========
		$user_id = $_SESSION['logic_erp']['user_id'];
		$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id='$user_id'");
		$store_location_id = $userCredential[0][csf('store_location_id')];

		if ($store_location_id != '') {$store_location_credential_cond = "and a.id in($store_location_id)";} else { $store_location_credential_cond = "";}

		$selector = ($element_id != 0) ? "floor_td_to" : "floor_td";
		$reset_element = ($element_id != 0) ? "store*to" : "store";
		$controller = ($element_id != 0) ? ($controller . "*$item_category*cbo_floor_to*$element_sl") : $controller;
		$element_name = ($element_id != 0) ? "cbo_store_name_to[]" : "cbo_store_name[]";
		$element_id = ($element_id != 0) ? "cbo_store_name_to" : "cbo_store_name";

		if ($data[2] != "" && $data[2] > 0) {$location_cond = "and a.location_id='$data[2]'";} else { $location_cond = "";}
		echo create_drop_down($element_id, $el_width, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[1]' $location_cond and a.status_active=1 and a.is_deleted=0 and b.category_type in($item_category) $store_location_credential_cond group by a.id, a.store_name order by a.store_name", "id,store_name", 1, "--Select--", 0, "reset_room_rack_self_bin('$reset_element');load_room_rack_self_bin('$controller','floor','$selector', $('#$company_id_element').val(), $('#$location_id_element').val(), this.value,'','','','','','',".$el_width.",'".$display_orientation."');$custom_function",0,'','','','','','',$element_name);
	}

	if ($action_type == "floor") {
		if ($data[2] != "" && $data[2] > 0) {$location_cond = "and b.location_id='$data[2]'";} else { $location_cond = "";}
		$cboWidth = '';
		$selector = ($element_id != 0) ? "room_td_to" : "room_td";
		$reset_element = ($element_id != 0) ? "floor*to" : "floor";
		$controller = ($element_id != 0) ? ($controller . "*$item_category*cbo_room_to*$element_sl") : $controller;
		$element_name = ($element_id != 0) ? "cbo_floor_to[]" : "cbo_floor[]";
		$element_id = ($element_id != 0) ? $data[9] : "cbo_floor";
		echo create_drop_down($element_id, $el_width, "select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and b.store_id='$data[3]' and a.company_id='$data[1]' $location_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name", "floor_id,floor_room_rack_name", 1, "--Select--", 0, "reset_room_rack_self_bin('$reset_element'); load_room_rack_self_bin('$controller','room','$selector', $('#$company_id_element').val(),$('#$location_id_element').val(),$('#$store_id_element').val(),$(".$element_id.").val(),'','','','','',".$el_width.",'".$display_orientation."');$custom_function",0,'','','','','','',$element_name);
	}

	if ($action_type == "room") {
		if ($data[2] != "" && $data[2] > 0) {$location_cond = "and b.location_id='$data[2]'";} else { $location_cond = "";}
		$selector = ($element_id != 0) ? "rack_td_to" : "rack_td";
		$reset_element = ($element_id != 0) ? "room*to" : "room";
		$controller = ($element_id != 0) ? ($controller . "*$item_category*txt_rack_to*$element_sl") : $controller;
		$element_name = ($element_id != 0) ? "cbo_room_to[]" : "cbo_room[]";
		$element_id = ($element_id != 0) ? $data[9] : "cbo_room";

		echo create_drop_down($element_id, $el_width, "select b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.room_id and b.store_id='$data[3]' and a.company_id='$data[1]' $location_cond and b.floor_id='$data[4]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name order by a.floor_room_rack_name", "room_id,floor_room_rack_name", 1, "--Select--", 0, "reset_room_rack_self_bin('$reset_element'); load_room_rack_self_bin('$controller','rack','$selector',$('#$company_id_element').val(),$('#$location_id_element').val(),$('#$store_id_element').val(),$('#$floor_id_element').val(),$(".$element_id.").val(),'','','','',".$el_width.",'".$display_orientation."');$custom_function",0,'','','','','','',$element_name);
	}

	if ($action_type == "rack") {
		if ($data[2] != "" && $data[2] > 0) {$location_cond = "and b.location_id='$data[2]'";} else { $location_cond = "";}
		$selector = ($element_id != 0) ? "shelf_td_to" : "shelf_td";
		$reset_element = ($element_id != 0) ? "rack*to" : "rack";
		$controller = ($element_id != 0) ? ($controller . "*$item_category*txt_shelf_to*$element_sl") : $controller;
		$element_name = ($element_id != 0) ? "txt_rack_to[]" : "txt_rack[]";
		$element_id = ($element_id != 0) ? $data[9] : "txt_rack";

		echo create_drop_down($element_id, $el_width, "select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.rack_id and b.store_id='$data[3]' and a.company_id='$data[1]' $location_cond and b.floor_id='$data[4]' and b.room_id='$data[5]' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name", "rack_id,floor_room_rack_name", 1, "--Select--", 0, "reset_room_rack_self_bin('$reset_element'); load_room_rack_self_bin('$controller','shelf','$selector',$('#$company_id_element').val(),$('#$location_id_element').val(),$('#$store_id_element').val(),$('#$floor_id_element').val(),$('#$room_id_element ').val(),$(".$element_id.").val(),'','','',".$el_width.",'".$display_orientation."');$custom_function",0,'','','','','','',$element_name);
	}

	if ($action_type == "shelf") {
		if ($data[2] != "" && $data[2] > 0) {$location_cond = "and b.location_id='$data[2]'";} else { $location_cond = "";}
		$selector = ($element_id != 0) ? "bin_td_to" : "bin_td";
		$reset_element = ($element_id != 0) ? "shelf*to" : "shelf";
		$controller = ($element_id != 0) ? ($controller . "*$item_category*cbo_bin_to*$element_sl") : $controller;
		$element_name = ($element_id != 0) ? "txt_shelf_to[]" : "txt_shelf[]";
		$element_id = ($element_id != 0) ? $data[9] : "txt_shelf";


		echo create_drop_down($element_id, $el_width, "select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.shelf_id and b.store_id='$data[3]' and a.company_id='$data[1]' $location_cond and b.floor_id='$data[4]' and b.room_id='$data[5]' and b.rack_id='$data[6]' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name", "shelf_id,floor_room_rack_name", 1, "--Select--", 0, "reset_room_rack_self_bin('$reset_element'); load_room_rack_self_bin('$controller','bin','$selector',$('#$company_id_element').val(),$('#$location_id_element').val(),$('#$store_id_element').val(),$('#$floor_id_element').val(),$('#$room_id_element').val(),$('#$rack_id_element').val(),$(".$element_id.").val(),'','',".$el_width.",'".$display_orientation."');$custom_function",0,'','','','','','',$element_name);
	}

	if ($action_type == "bin") {
		//$element_id = ( $element_id !="" )? $element_id : "cbo_bin";
		$element_name = ($element_id != 0) ? "cbo_bin_to[]" : "cbo_bin[]";
		$element_id = ($element_id != 0) ? $data[9] : "cbo_bin";

		if ($data[2] != "" && $data[2] > 0) {$location_cond = "and b.location_id='$data[2]'";} else { $location_cond = "";}
		echo create_drop_down($element_id, $el_width, "select b.bin_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.bin_id and b.store_id='$data[3]' and a.company_id='$data[1]' $location_cond and b.floor_id='$data[4]' and b.room_id='$data[5]' and b.rack_id='$data[6]' and b.shelf_id='$data[7]' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.bin_id,a.floor_room_rack_name order by a.floor_room_rack_name", "bin_id,floor_room_rack_name", 1, "--Select--", 0, "",0,'','','','','','',$element_name);
	}
	exit();
}*/

function load_room_rack_self_bin($controller, $data, $selector = "") {
	$data = explode("*", $data);
	//echo $controller.test.$data[9];die;
	//echo $data[9];
	$action_type = $data[0];
	$element_id = ($data[9] == "undefined") ? 0 : 1;
	$display_orientation = ($data[14] != '') ? "$data[14]" : "";
	$item_category = implode(",", array_unique(explode("_", $data[10])));
	$element_sl 	 = ($data[12] != '') ? "$data[12]" : "";
	//$element_seq 	 = ($data[12] != '') ? "_$data[12]" : "";
	$element_seq 	 = ($data[12] == '' || $data[12] == 'undefined') ? "" : "_$data[12]";
	
	//$company_id_element = ($element_id != 0) ? (($data[0] == "undefined") ? "cbo_company_id" : ($display_orientation=="H")?"cbo_company_id":"cbo_company_id_to") : "cbo_company_id";
	//$location_id_element = ($element_id != 0) ? (($data[0] == "undefined") ? "cbo_location" : ($display_orientation=="H")?"cbo_location":"cbo_location_to") : "cbo_location";

	$company_id_element = ($element_id != 0) ? (($data[0] == "undefined") ? "cbo_company_id" : (($display_orientation=="H")?"cbo_company_id":"cbo_company_id_to")) : "cbo_company_id";
   	$location_id_element = ($element_id != 0) ? (($data[0] == "undefined") ? "cbo_location" : (($display_orientation=="H")?"cbo_location":"cbo_location_to")): "cbo_location";


	$store_id_element = ($element_id != 0) ? (($display_orientation=="H")?"cbo_store_name":"cbo_store_name_to") : "cbo_store_name";
	$floor_id_element = ($element_id != 0) ? (($display_orientation=="H")?"cbo_floor_to".$element_seq:"cbo_floor_to") : "cbo_floor";
	$room_id_element = ($element_id != 0) ? (($display_orientation=="H")?"cbo_room_to".$element_seq:"cbo_room_to") : "cbo_room";
	$rack_id_element = ($element_id != 0) ? (($display_orientation=="H")?"txt_rack_to".$element_seq:"txt_rack_to") : "txt_rack";
	$custom_function = ($data[11] != '') ? "$data[11]" : "";
	$element_width = ($data[13] != '') ? "$data[13]" : "";
	$el_width = ($element_width=="") ? "152" : $element_width;

	if ($action_type == "store") {
		//========== user credential start ========
		$user_id = $_SESSION['logic_erp']['user_id'];
		$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id='$user_id'");
		$store_location_id = $userCredential[0][csf('store_location_id')];

		if ($store_location_id != '') {$store_location_credential_cond = "and a.id in($store_location_id)";} else { $store_location_credential_cond = "";}

		$selector = ($element_id != 0) ? "floor_td_to" : "floor_td";
		$reset_element = ($element_id != 0) ? "store*to" : "store";
		$controller = ($element_id != 0) ? ($controller . "*$item_category*cbo_floor_to$element_seq*$element_sl") : $controller;
		$element_name = ($element_id != 0) ? "cbo_store_name_to[]" : "cbo_store_name[]";
		$element_id = ($element_id != 0) ? "cbo_store_name_to" : "cbo_store_name";

		if ($data[2] != "" && $data[2] > 0) {$location_cond = "and a.location_id='$data[2]'";} else { $location_cond = "";}

		if ($data[9]=='cbo_store_name_to') { $store_location_credential_cond="";}
		
		echo create_drop_down($element_id, $el_width, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[1]' $location_cond and a.status_active=1 and a.is_deleted=0 and b.category_type in($item_category) $store_location_credential_cond group by a.id, a.store_name order by a.store_name", "id,store_name", 1, "--Select--", 0, "reset_room_rack_self_bin('$reset_element');load_room_rack_self_bin('$controller','floor','$selector', $('#$company_id_element').val(), $('#$location_id_element').val(), this.value,'','','','','','',".$el_width.",'".$display_orientation."');$custom_function",0,'','','','','','',$element_name);
	}

	if ($action_type == "floor") {
		if ($data[2] != "" && $data[2] > 0) {$location_cond = "and b.location_id='$data[2]'";} else { $location_cond = "";}
		$cboWidth = '';
		$selector = ($element_id != 0) ? "room_td_to" : "room_td";
		$reset_element = ($element_id != 0) ? "floor*to$element_seq" : "floor";
		$controller = ($element_id != 0) ? ($controller . "*$item_category*cbo_room_to$element_seq*$element_sl") : $controller;
		$element_name = ($element_id != 0) ? "cbo_floor_to[]" : "cbo_floor[]";
		$element_id = ($element_id != 0) ? $data[9] : "cbo_floor";
		echo create_drop_down($element_id, $el_width, "select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and b.store_id='$data[3]' and a.company_id='$data[1]' $location_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name", "floor_id,floor_room_rack_name", 1, "--Select--", 0, "reset_room_rack_self_bin('$reset_element'); load_room_rack_self_bin('$controller','room','$selector', $('#$company_id_element').val(),$('#$location_id_element').val(),$('#$store_id_element').val(),$(".$element_id.").val(),'','','','','',".$el_width.",'".$display_orientation."');$custom_function",0,'','','','','','',$element_name);
	}

	if ($action_type == "room") {
		if ($data[2] != "" && $data[2] > 0) {$location_cond = "and b.location_id='$data[2]'";} else { $location_cond = "";}
		$selector = ($element_id != 0) ? "rack_td_to" : "rack_td";
		$reset_element = ($element_id != 0) ? "room*to$element_seq" : "room";
		$controller = ($element_id != 0) ? ($controller . "*$item_category*txt_rack_to$element_seq*$element_sl") : $controller;
		$element_name = ($element_id != 0) ? "cbo_room_to[]" : "cbo_room[]";
		$element_id = ($element_id != 0) ? $data[9] : "cbo_room";

		echo create_drop_down($element_id, $el_width, "select b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.room_id and b.store_id='$data[3]' and a.company_id='$data[1]' $location_cond and b.floor_id='$data[4]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name order by a.floor_room_rack_name", "room_id,floor_room_rack_name", 1, "--Select--", 0, "reset_room_rack_self_bin('$reset_element'); load_room_rack_self_bin('$controller','rack','$selector',$('#$company_id_element').val(),$('#$location_id_element').val(),$('#$store_id_element').val(),$('#$floor_id_element').val(),$(".$element_id.").val(),'','','','',".$el_width.",'".$display_orientation."');$custom_function",0,'','','','','','',$element_name);
	}

	if ($action_type == "rack") {
		if ($data[2] != "" && $data[2] > 0) {$location_cond = "and b.location_id='$data[2]'";} else { $location_cond = "";}
		$selector = ($element_id != 0) ? "shelf_td_to" : "shelf_td";
		$reset_element = ($element_id != 0) ? "rack*to$element_seq" : "rack";
		$controller = ($element_id != 0) ? ($controller . "*$item_category*txt_shelf_to$element_seq*$element_sl") : $controller;
		$element_name = ($element_id != 0) ? "txt_rack_to[]" : "txt_rack[]";
		$element_id = ($element_id != 0) ? $data[9] : "txt_rack";

		//echo create_drop_down($element_id, $el_width, "select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.rack_id and b.store_id='$data[3]' and a.company_id='$data[1]' $location_cond and b.floor_id='$data[4]' and b.room_id='$data[5]' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name", "rack_id,floor_room_rack_name", 1, "--Select--", 0, "reset_room_rack_self_bin('$reset_element'); load_room_rack_self_bin('$controller','shelf','$selector',$('#$company_id_element').val(),$('#$location_id_element').val(),$('#$store_id_element').val(),$('#$floor_id_element').val(),$('#$room_id_element ').val(),$(".$element_id.").val(),'','','',".$el_width.",'".$display_orientation."');$custom_function",0,'','','','','','',$element_name);
		
		//for rack sequence wise		
		echo create_drop_down($element_id, $el_width, "select b.rack_id,a.floor_room_rack_name, b.serial_no from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.rack_id and b.store_id='$data[3]' and a.company_id='$data[1]' $location_cond and b.floor_id='$data[4]' and b.room_id='$data[5]' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and b.shelf_id is null group by b.rack_id,a.floor_room_rack_name, b.serial_no order by b.serial_no, a.floor_room_rack_name", "rack_id,floor_room_rack_name", 1, "--Select--", 0, "reset_room_rack_self_bin('$reset_element'); load_room_rack_self_bin('$controller','shelf','$selector',$('#$company_id_element').val(),$('#$location_id_element').val(),$('#$store_id_element').val(),$('#$floor_id_element').val(),$('#$room_id_element ').val(),$(".$element_id.").val(),'','','',".$el_width.",'".$display_orientation."');$custom_function",0,'','','','','','',$element_name);
	}

	if ($action_type == "shelf") {
		if ($data[2] != "" && $data[2] > 0) {$location_cond = "and b.location_id='$data[2]'";} else { $location_cond = "";}
		$selector = ($element_id != 0) ? "bin_td_to" : "bin_td";
		$reset_element = ($element_id != 0) ? "shelf*to$element_seq" : "shelf";
		$controller = ($element_id != 0) ? ($controller . "*$item_category*cbo_bin_to$element_seq*$element_sl") : $controller;
		$element_name = ($element_id != 0) ? "txt_shelf_to[]" : "txt_shelf[]";
		$element_id = ($element_id != 0) ? $data[9] : "txt_shelf";
		

		echo create_drop_down($element_id, $el_width, "select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.shelf_id and b.store_id='$data[3]' and a.company_id='$data[1]' $location_cond and b.floor_id='$data[4]' and b.room_id='$data[5]' and b.rack_id='$data[6]' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name", "shelf_id,floor_room_rack_name", 1, "--Select--", 0, "reset_room_rack_self_bin('$reset_element'); load_room_rack_self_bin('$controller','bin','$selector',$('#$company_id_element').val(),$('#$location_id_element').val(),$('#$store_id_element').val(),$('#$floor_id_element').val(),$('#$room_id_element').val(),$('#$rack_id_element').val(),$(".$element_id.").val(),'','',".$el_width.",'".$display_orientation."');$custom_function",0,'','','','','','',$element_name);
	}

	if ($action_type == "bin") {
		//$element_id = ( $element_id !="" )? $element_id : "cbo_bin";
		$element_name = ($element_id != 0) ? "cbo_bin_to[]" : "cbo_bin[]";
		$element_id = ($element_id != 0) ? $data[9] : "cbo_bin";

		if ($data[2] != "" && $data[2] > 0) {$location_cond = "and b.location_id='$data[2]'";} else { $location_cond = "";}
		echo create_drop_down($element_id, $el_width, "select b.bin_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.bin_id and b.store_id='$data[3]' and a.company_id='$data[1]' $location_cond and b.floor_id='$data[4]' and b.room_id='$data[5]' and b.rack_id='$data[6]' and b.shelf_id='$data[7]' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.bin_id,a.floor_room_rack_name order by a.floor_room_rack_name", "bin_id,floor_room_rack_name", 1, "--Select--", 0, "$custom_function",0,'','','','','','',$element_name);
	}
	exit();
}

function fn_number_format($val,$after_point=0,$dot='',$null='', $default_value=''){
	if(is_nan($val) || is_infinite($val)){return $default_value;}
	else if($dot==''){return number_format($val,$after_point);}
	else{return number_format($val,$after_point,$dot,$null);}
}


function get_help_button($path,$action,$type=1){
		return '<a href="javascript:help_popup(\''.$path.'\','.$type.',\''.$action.'\');">Help</a>';
	}

/**
	// For all type of print button.
 * fnc_company_location_address - This function is responsible for returning Company full name, Location address, Company logo.
 * @param  integer  $com_id 	defining Company ID
 * @param  integer  $loc_id   	defining Location ID
 * @return integer  $short      defining Location Address returning type
 */
function fnc_company_location_address($com_id, $loc_id=0, $short=0)
{
	$comp_logo = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$com_id'", "image_location");
	$country_full_name = return_library_array("SELECT id,country_name from lib_country", "id", "country_name");
	$company_info_dtls=array();
	if($loc_id!='' && $loc_id!=0){
		$location_adress= return_field_value("address","lib_location","company_id=$com_id and id=$loc_id ","address");
		$company_name= return_field_value("company_name","lib_company","id=$com_id","company_name");
		$company_info_dtls[0]	= $company_name;
		$company_info_dtls[1] 	= $location_adress;
		$company_info_dtls[2] 	= $comp_logo;
	}
	else
	{
		$sql = "SELECT * FROM lib_company WHERE id = $com_id  AND is_deleted=0 AND status_active=1 ORDER BY company_name ASC";
		$result = sql_select( $sql );
		foreach( $result as $row  )
		{
			if($short==1){
				if($row[csf("level_no")])		$level_no	= $row[csf("level_no")].', ';
				if($row[csf("plot_no")])		$plot_no 	= $row[csf("plot_no")].', ';
				if($row[csf("road_no")]) 		$road_no 	= $row[csf("road_no")].', ';
				if($row[csf("block_no")]!='')	$block_no 	= $row[csf("block_no")].', ';
				if($row[csf("zip_code")]!='')	$zip_code 	= ' -'.$row[csf("zip_code")].', ';
				if($row[csf("city")]!='') $city 			= ($zip_code!="")? $row[csf("city")]:$row[csf("city")]." ,";
				if($row[csf("country_id")]!='')	$country 	= $country_full_name[$row[csf("country_id")]].'.';
				if($row[csf("contact_no")]!='')	$contact_no = $row[csf("contact_no")].', ';
				if($row[csf("email")]!='')		$email 		= $row[csf("email")].', ';
				if($row[csf("website")]!='')	$website 	= $row[csf("website")];
			}
			else if($short==2){
				if($row[csf("level_no")])$level_no 		= "Level # ".$row[csf("level_no")].', ';
				if($row[csf("plot_no")])$plot_no 		= "House # ".$row[csf("plot_no")].', ';
				if($row[csf("road_no")]) $road_no 		= "Road # ".$row[csf("road_no")].', ';
				if($row[csf("block_no")]!='')$block_no 	= "Block # ".$row[csf("block_no")].', ';
				if($row[csf("zip_code")]!='')$zip_code 	= ' -'.$row[csf("zip_code")].' , ';
				if($row[csf("city")]!='') $city 		= ($zip_code!="")?"City # ".$row[csf("city")]:"City # ".$row[csf("city")]." ,";
				if($row[csf("country_id")]!='')$country = $country_full_name[$row[csf("country_id")]].'.';
				if($row[csf("contact_no")]!='')	$contact_no = "Contact Number: ".$row[csf("contact_no")].', ';
				if($row[csf("email")]!='')	$email 		= "Email: ".$row[csf("email")].', ';
				if($row[csf("website")]!='')$website 	= "Website: ".$row[csf("website")];
			}
			else if($short==3){
				if($row[csf("level_no")])$level_no 		= "".$row[csf("level_no")].', ';
				if($row[csf("plot_no")])$plot_no 		= "".$row[csf("plot_no")].', ';
				if($row[csf("road_no")]) $road_no 		= "".$row[csf("road_no")].', ';
				if($row[csf("block_no")]!='')$block_no 	= "".$row[csf("block_no")].', ';
				if($row[csf("zip_code")]!='')$zip_code 	= ' -'.$row[csf("zip_code")].' , ';
				if($row[csf("city")]!='') $city 		= ($zip_code!="")?"".$row[csf("city")]:"".$row[csf("city")]." ,";
				if($row[csf("country_id")]!='')$country = $country_full_name[$row[csf("country_id")]].'.';
				if($row[csf("contact_no")]!='')	$contact_no = "".$row[csf("contact_no")].', ';
				if($row[csf("email")]!='')	$email 		= "".$row[csf("email")].', ';
				if($row[csf("website")]!='')$website 	= "".$row[csf("website")];
			}
			else{
				$company_info_dtls[0]	= 0;
				$company_info_dtls[1] 	= "Error Found.";
			}
			$company_info_dtls[0]	= $row[csf("company_name")];
			$company_info_dtls[1] 	= rtrim($level_no.$plot_no.$road_no.$block_no.$city.$zip_code.$country.'</br>'.$contact_no.$email.$website,", ");
			$company_info_dtls[2] 	= $comp_logo;
		}
	}
	return $company_info_dtls;
}

/*
|--------------------------------------------------------------------------
| get_company_array
| written by : Md. Nuruzzaman
|--------------------------------------------------------------------------
| This function will return company array
|
*/
function get_company_array()
{
	$sql = sql_select("SELECT id, company_name, company_short_name FROM lib_company WHERE status_active=1 AND is_deleted=0");
	$data=array();
	foreach($sql as $row)
	{
		$data[$row[csf('id')]]['name']=$row[csf('company_name')];
		$data[$row[csf('id')]]['shortname']=$row[csf('company_short_name')];
	}
	unset($sql);
	return $data;
}
/*
|--------------------------------------------------------------------------
| get_color_array
| written by : Md. Nuruzzaman
|--------------------------------------------------------------------------
| This function will return color array
|
*/
function get_color_array()
{
	//$data = return_library_array( "SELECT id, color_name FROM lib_color WHERE status_active =1 AND is_deleted=0", 'id', 'color_name');
	$data = return_library_array( 'SELECT id, color_name FROM lib_color', 'id', 'color_name');
	return $data;
}

/*
|--------------------------------------------------------------------------
| get_supplier_array
| written by : Md. Nuruzzaman
|--------------------------------------------------------------------------
| This function will return supplier array
|
*/
function get_supplier_array()
{
	$data = return_library_array("SELECT id, supplier_name FROM lib_supplier", "id", "supplier_name");
	return $data;
}

/*
|--------------------------------------------------------------------------
| get_batch_array
| written by : Md. Nuruzzaman
|--------------------------------------------------------------------------
| This function will return batch array
|
*/
function get_batch_array()
{
	$data = return_library_array("SELECT id, batch_no FROM pro_batch_create_mst", "id", "batch_no");
	return $data;
}

/*
|--------------------------------------------------------------------------
| get_country_array
| written by : Md. Nuruzzaman
|--------------------------------------------------------------------------
| This function will return country array
|
*/
function get_country_array()
{
	$data = return_library_array("SELECT id, country_name FROM lib_country", "id", "country_name");
	return $data;
}

/*
|--------------------------------------------------------------------------
| get_brand_array
| written by : Md. Nuruzzaman
|--------------------------------------------------------------------------
| This function will return brand array
|
*/
function get_brand_array()
{
	$data = return_library_array("SELECT id, brand_name FROM lib_brand", "id", "brand_name");
	return $data;
}

/*
|--------------------------------------------------------------------------
| get_yarn_count_array
| written by : Md. Nuruzzaman
|--------------------------------------------------------------------------
| This function will return yarn count array
|
*/
function get_yarn_count_array()
{
	$data = return_library_array("SELECT id, yarn_count FROM lib_yarn_count", "id", "yarn_count");
	return $data;
}

/*
|--------------------------------------------------------------------------
| get_machine_array
| written by : Md. Nuruzzaman
|--------------------------------------------------------------------------
| This function will return machine array
| $data['id'] = ['machine_no']
|
*/
function get_machine_array()
{
	$data = return_library_array("SELECT id, machine_no FROM lib_machine_name", 'id','machine_no');
	return $data;
}

/*
|--------------------------------------------------------------------------
| get_buyer_array
| written by : Md. Nuruzzaman
|--------------------------------------------------------------------------
| This function will return buyer array
|
*/
function get_buyer_array()
{
	$data = return_library_array("SELECT id, buyer_name FROM lib_buyer", "id", "buyer_name");
	return $data;
}

/*
|--------------------------------------------------------------------------
| get_empty_data_msg
| written by : Md. Nuruzzaman
|--------------------------------------------------------------------------
|
*/

function get_empty_data_msg()
{
	return "<h3 style=\"color:red;\">No Data Found.</h3>";
}



function get_ip_mac($trace)
{
 	 ob_start();
	 system($trace.' -h 2'." yahoo.com");
	 $trace=ob_get_contents();
	 ob_clean();
	 $lines=explode("\n", $trace);
	 $lines=explode(" ", $lines[5]);
	 
	foreach($lines as $line)
	{
		if (strlen(trim($line))>5)
		{
			$proxy_address=$line;
		}
	}
	
	$ipAddress=$_SERVER['REMOTE_ADDR'];
	$macAddr="";
	
	#run the external command, break output into lines
	ob_start();
	 system('arp -a '.$ipAddress);
	 $arp=ob_get_contents();
	 ob_clean();
	$lines=explode("\n", $arp);
	
	#look for the output line describing our IP address
	foreach($lines as $line)
	{
	   $cols=preg_split('/\s+/', trim($line));
	   if ($cols[0]==$ipAddress)
	   {
		   $macAddr=$cols[1];
	   }
	}
	//return trim($ipAddress)."__".strtoupper(trim($macAddr))."__".trim($proxy_address); 
	return trim($proxy_address); 
  //echo strtoupper($macAddr)."--".$ipAddress."--".$proxy_address; 
}




//.....................................REZA
//dataType=0 for int 1 for string; 
function where_con_using_array($arrayData,$dataType=0,$table_coloum){
	$chunk_list_arr=array_chunk($arrayData,999);
	if(count($chunk_list_arr)<1){return " and ".$table_coloum." in(0)";}
	$p=1;
	foreach($chunk_list_arr as $process_arr)
	{
		if($dataType==0){
			if($p==1){$sql .=" and (".$table_coloum." in(".implode(',',$process_arr).")"; }
			else {$sql .=" or ".$table_coloum." in(".implode(',',$process_arr).")";}
		}
		else{
			if($p==1){$sql .=" and (".$table_coloum." in('".implode("','",$process_arr)."')"; }
			else {$sql .=" or ".$table_coloum." in('".implode("','",$process_arr)."')";}
		}
		$p++;
	}
	
	$sql.=") ";
	return $sql;
}




function executeTime(){
	return (microtime(true)-$_SERVER[REQUEST_TIME]);
}

/*
|--------------------------------------------------------------------------
| get_vs_allocated_qty
| written by : Md. Nuruzzaman
|--------------------------------------------------------------------------
| This function will return allocated quantity variable settings array
|
*/
function get_vs_allocated_qty($arr)
{
	$sql = "SELECT allocation AS ALLOCATION, sales_allocation AS SALES_ALLOCATION, smn_allocation AS SMN_ALLOCATION FROM variable_settings_inventory WHERE variable_list = 18 AND company_name = ".$arr['company_id']." AND item_category_id = ".$arr['category_id'];
	$sql_rslt = sql_select($sql);
	$data_arr = array();
	if(!empty($sql_rslt))
	{
		foreach($sql_rslt as $row)
		{
			$data_arr['is_allocated'] = $row['ALLOCATION'];
			$data_arr['is_sales_order'] = $row['SALES_ALLOCATION'];
			$data_arr['is_smn_allocation'] = $row['SMN_ALLOCATION'];
		}
	}
	else
	{
		$data_arr['is_allocated'] = 2;
		$data_arr['is_sales_order'] = 2;
		$data_arr['is_smn_allocation'] = 2;
	}
	
	//for vs auto allocation
	if($arr['vs_auto_allocation'] == 1)
	{
		$data_arr1 = get_vs_auto_allocation_from_requisition($arr);
		$data_arr['is_auto_allocation'] = $data_arr1['is_auto_allocation'];
	}
	
	return $data_arr;
}

/*
|--------------------------------------------------------------------------
| get_vs_auto_allocation_from_requisition
| written by : Md. Nuruzzaman
|--------------------------------------------------------------------------
| This function will return auto allocation from requisition variable settings array
|
*/
function get_vs_auto_allocation_from_requisition($arr)
{
	$sql = "SELECT auto_allocate_yarn_from_requis AS AUTO_ALLOCATION FROM variable_settings_production WHERE variable_list = 6 AND company_name = ".$arr['company_id']." AND status_active = 1 AND is_deleted=0";
	$sql_rslt = sql_select($sql);
	$data_arr = array();
	if(!empty($sql_rslt))
	{
		foreach($sql_rslt as $row)
		{
			$data_arr['is_auto_allocation'] = $row['AUTO_ALLOCATION'];
		}
	}
	else
	{
		$data_arr['is_auto_allocation'] = 2;
	}
	return $data_arr;
}

/*
|--------------------------------------------------------------------------
| get_allocation_balance
| written by : Md. Nuruzzaman
| Update by : Md. Didarul alam
|--------------------------------------------------------------------------
| This function will return available qty based on variable settings array
|
*/
function get_allocation_balance($arr)
{
	$data_arr = array();
	$prod_id = $arr['product_id'];
	$variable_set_allocation = $arr['is_auto_allocation'];
	$job_no = $arr['job_no'];
	$selected_booking_no = $arr['booking_no'];
	$poId = $arr['po_id'];

	if($variable_set_allocation == 1)
	{
		$prodStockAvailavle = sql_select("SELECT current_stock AS CURRENT_STOCK,(current_stock-allocated_qnty) AS AVAILABLE_QNTY FROM product_details_master WHERE status_active=1 AND is_deleted=0 AND id=".$prod_id."");
		foreach($prodStockAvailavle as $row)
		{
			$data_arr['available_qnty'] = $row['AVAILABLE_QNTY'];
			$data_arr['current_stock'] = $row['CURRENT_STOCK'];
		}
	}
	else
	{
		/*
		|--------------------------------------------------------------------------
		| for allocation information
		|--------------------------------------------------------------------------
		|
		*/
		$sql_allo = "SELECT b.booking_no AS BOOKING_NO, b.job_no AS JOB_NO, b.item_id AS PROD_ID, b.po_break_down_id AS PO_BREAK_DOWN_ID, c.yarn_count_id AS YARN_COUNT_ID, c.yarn_comp_type1st AS YARN_COMP_TYPE1ST, c.yarn_comp_percent1st AS YARN_COMP_PERCENT1ST, c.yarn_type AS YARN_TYPE, c.dyed_type AS DYED_TYPE, b.qnty AS ALLOCATED_QNTY FROM inv_material_allocation_mst a, inv_material_allocation_dtls b, product_details_master c WHERE a.id=b.mst_id AND b.item_id=c.id AND b.booking_no = '".$selected_booking_no."' AND b.job_no = '".$job_no."' AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND b.item_category=1 AND c.status_active=1 AND c.is_deleted=0";
		$data_array = sql_select($sql_allo);
		$product_id_arr = array();
		$allocationQty = 0;
		foreach ($data_array as $row_allo)
		{
			$booking_no = $row_allo['BOOKING_NO'];
			$job_no = $row_allo['JOB_NO'];
			$yarn_count_id = $row_allo['YARN_COUNT_ID'];
			$yarn_comp_type1st = $row_allo['YARN_COMP_TYPE1ST'];
			$yarn_comp_percent1st = $row_allo['YARN_COMP_PERCENT1ST'];
			$yarn_type_id = $row_allo['YARN_TYPE'];
			$product_type_arr[$row_allo['PROD_ID']] = $row_allo['DYED_TYPE'];
			
			$product_id_arr[$row_allo['PROD_ID']] = $row_allo['PROD_ID'];
			$job_total_allocation_arr[$job_no][$row_allo['PROD_ID']] += $row_allo['ALLOCATED_QNTY'];
			if($row_allo['BOOKING_NO']!="")
			{
				$booking_alocation_arr[$row_allo['BOOKING_NO']][$row_allo['PROD_ID']] += $row_allo['ALLOCATED_QNTY'];
			}
		}
		unset($data_array);
		
		/*
		|--------------------------------------------------------------------------
		| for yarn dyeing and service information
		|--------------------------------------------------------------------------
		|
		*/
		$prod_id_cond = '';
		if(!empty($product_id_arr))
		{
			$prod_id_cond = "AND b.product_id IN(".implode(",", $product_id_arr).")";
		}
		
		if($yarn_count_id != '')
		{
			$count_id_cond = "AND b.count = ".$yarn_count_id."";
		}
		
		if($yarn_comp_type1st != '')
		{
			$yarn_comp_type1st_cond = "AND b.yarn_comp_type1st = ".$yarn_comp_type1st."";
		}
		
		if($yarn_comp_percent1st!="")
		{
			$yarn_comp_percent1st_cond = "AND b.yarn_comp_percent1st = ".$yarn_comp_percent1st."";
		}
	
		$ydsw_sql="SELECT x.job_no AS JOB_NO, x.product_id AS PRODUCT_ID, sum(x.yarn_wo_qty) AS YARN_WO_QTY, x.yarn_dyeing_prefix_num AS YARN_DYEING_PREFIX_NUM FROM(SELECT b.job_no, b.product_id, SUM(b.yarn_wo_qty) yarn_wo_qty, a.yarn_dyeing_prefix_num FROM wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b WHERE a.id=b.mst_id AND b.status_active=1 AND b.is_deleted=0 AND a.entry_form IN(41,42,94,114,135) AND b.entry_form IN(41,42,94,114,135) AND b.job_no='".$job_no."' ".$prod_id_cond." GROUP BY b.job_no, b.product_id, a.yarn_dyeing_prefix_num
		UNION ALL
		SELECT b.job_no, b.product_id, SUM(b.yarn_wo_qty) yarn_wo_qty, a.yarn_dyeing_prefix_num from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b WHERE a.id=b.mst_id AND b.status_active=1 AND b.is_deleted=0 AND a.entry_form IN(125,340) AND b.entry_form IN(125,340) AND b.job_no='".$job_no."' ".$count_id_cond." ".$yarn_comp_type1st_cond." ".$yarn_comp_percent1st_cond." GROUP BY b.job_no, b.product_id, a.yarn_dyeing_prefix_num )x GROUP BY x.job_no, x.product_id, x.yarn_dyeing_prefix_num";
		//echo $ydsw_sql;
		$check_ydsw = sql_select($ydsw_sql);
		$prod_wise_ydsw=array();
		foreach ($check_ydsw as $row)
		{
			$prod_wise_ydsw[$row['JOB_NO']][$row['PRODUCT_ID']]['qty'] += $row['YARN_WO_QTY'];
			$prod_wise_ydsw[$row['JOB_NO']][$row['PRODUCT_ID']]['yarn_dyeing_prefix_num'][$row['YARN_DYEING_PREFIX_NUM']] = $row['YARN_DYEING_PREFIX_NUM'];
		}
		unset($check_ydsw);
		
		/*
		|--------------------------------------------------------------------------
		| for sales_booking_no
		|--------------------------------------------------------------------------
		|
		*/
		
		$sql_sales = "SELECT a.sales_booking_no AS BOOKING_NO FROM fabric_sales_order_mst a WHERE a.status_active = 1 AND a.is_deleted = 0 AND a.job_no = '".$job_no."'";
		$sql_sales_rslt = sql_select($sql_sales);
		$booking_no_arr = array();
		foreach ($sql_sales_rslt as $row)
		{
			$booking_no_arr[$row['BOOKING_NO']] = $row['BOOKING_NO'];
		}
		unset($sql_sales_rslt);
		$booking_nos = "'".implode("','",$booking_no_arr)."'";
		
	
		/*
		|--------------------------------------------------------------------------
		| for program information
		|--------------------------------------------------------------------------
		|
		*/
		
		$sql_program = "SELECT b.id AS ID FROM ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b WHERE a.id=b.mst_id and a.booking_no in(".$booking_nos.") and a.status_active=1 and a.is_deleted=0";  //and b.status_active=1 and b.is_deleted=0: ommit cause program can be deleted even after issue
		$sql_program_rslt = sql_select($sql_program);
		$program_no_arr = array();
		foreach ($sql_program_rslt as $row)
		{
			$program_no_arr[$row['ID']] = $row['ID'];
		}
		unset($sql_program_rslt);
		$all_knit_id = implode(",", $program_no_arr);
		
		
		/*
		|--------------------------------------------------------------------------
		| for requisition information
		|--------------------------------------------------------------------------
		|
		*/
		if (!empty($product_id_arr) != "")
		{
			$req_sql = "SELECT a.booking_no AS BOOKING_NO, c.id AS ID, c.knit_id AS KNIT_ID, c.prod_id AS PROD_ID, c.requisition_no AS REQUISITION_NO, c.yarn_qnty AS YARN_QNTY FROM ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c WHERE a.id=b.mst_id AND b.id=c.knit_id AND b.id in (".$all_knit_id.") AND c.prod_id in(".implode(",", $product_id_arr).") AND a.status_active=1 AND a.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0"; // AND b.status_active=1 AND b.is_deleted=0 : ommit cause program can be deleted even after issue

			//echo $req_sql;
			$req_result = sql_select($req_sql);
			$duplicate_check = array();
			foreach($req_result as $row)
			{
				if($duplicate_check[$row['ID']] != $row['ID'])
				{
					$duplicate_check[$row['ID']] = $row['ID'];
					$booking_requsition_arr[$row['BOOKING_NO']][$row['PROD_ID']]['qty'] += $row['YARN_QNTY'];
					$booking_requsition_arr[$row['BOOKING_NO']][$row['PROD_ID']]['requisition_no'][$row['REQUISITION_NO']] = $row['REQUISITION_NO'];
				}
			}
			unset($req_result);
		}

		$data_arr['job_allocation'] = $job_total_allocation_arr;
		$data_arr['booking_allocation'] = $booking_alocation_arr;
		$data_arr['booking_requisition'] = $booking_requsition_arr;
		$data_arr['yarn_dyeing_service'] = $prod_wise_ydsw;
	}
	return $data_arr;
}

/*
|--------------------------------------------------------------------------
| get_vs_yarn_test_mandatory
| written by : Md. Nuruzzaman
|--------------------------------------------------------------------------
| This function will return yarn test mandatory variable settings array
|
*/
function get_vs_yarn_test_mandatory($arr)
{
	$sql = "SELECT YES_NO FROM VARIABLE_SETTINGS_INVENTORY WHERE VARIABLE_LIST = ".$arr['variable_list']." AND COMPANY_NAME = ".$arr['company_id']." AND ITEM_CATEGORY_ID = 1";
	$sql_rslt = sql_select($sql);
	$data_arr = array();
	if(!empty($sql_rslt))
	{
		foreach($sql_rslt as $row)
		{
			$data_arr['is_mandatory'] = $row['YES_NO'];
		}
	}
	else
	{
		$data_arr['is_mandatory'] = 2;
	}
	return $data_arr;
}

function manage_allocation_transaction_log($log_data)
{
	$id = return_next_id_by_sequence("INV_ALLOCAT_TRANS_LOG_SEQ", "INV_MATERIAL_ALLOCAT_TRANS_LOG", $con);
	$field_array = "ID, ENTRY_FORM, REF_ID, REF_NUMBER, PRODUCT_ID, CURRENT_STOCK, ALLOCATED_QTY,AVAILABLE_QTY, DYED_TYPE,INSERT_DATE";
	$data_array = "(" . $id . "," . $log_data['entry_form'] . "," . $log_data['ref_id'] . ",'" . $log_data['ref_number'] . "'," . $log_data['product_id'] . "," . $log_data['current_stock'] . "," . $log_data['allocated_qty'] . "," .$log_data['available_qty'] . "," . $log_data['dyed_type'] . ",'" . $log_data['insert_date'] . "')";
	//return "INSERT INTO INV_MATERIAL_ALLOCAT_TRANS_LOG(".$field_array.") VALUES".$data_array;
	return sql_insert("INV_MATERIAL_ALLOCAT_TRANS_LOG", $field_array, $data_array);
}



function fnc_report_button($company_id,$module_id,$page_id,$first_button) //Aziz
{ //$first_button==1 for first button and $first_button==0 for all button
	
	$print_report_format=0;
	if($first_button==0) //for all button
	{
		 $sql_button= "select id,format_id from lib_report_template where template_name in(".$company_id.") and module_id=$module_id and report_id=$page_id and is_deleted=0 and status_active=1 order by id DESC";    
		$sql_button_result=sql_select($sql_button);
		$format_idArr=array();
		foreach($sql_button_result as $row)
		{
			$format_idArr[$row[csf('format_id')]]=$row[csf('format_id')];
		}
		if(count($sql_button_result)>0)
		{
		 $format_idAll=implode(",",$format_idArr);
		 $print_idArrAll=array_unique(explode(",",$format_idAll));
		 $print_report_format=implode(",",$print_idArrAll);
		}
		else  
		{
			 $print_report_format=0;
		}
	}
	
	 return $print_report_format;
}



function get_permitted_print_button($data_array=array()){
    $sql=sql_select("select FORMAT_ID,USER_ID from lib_report_template where TEMPLATE_NAME={$data_array['COMAPNY_NAME']} and MODULE_ID={$data_array['MODULE_ID']} and REPORT_ID={$data_array['REPORT_ID']}");

    $users_button_arr=array();
    foreach($sql as $rows){
        foreach(explode(',',$rows['USER_ID']) as $user_id){
            if($user_id==$data_array['USER_ID']){
                $users_button_arr[$user_id]=$rows['FORMAT_ID'];
                break;
            }
        }
    }
    return $users_button_arr[$data_array['USER_ID']];
  
}


/*
|--------------------------------------------------------------------------
| get_buyer_array
| written by : Jahid
|--------------------------------------------------------------------------
| This function will return buyer array
|
*/
function fnc_item_creation_arr()
{
	$sql=sql_select("select ID, ORDER_UOM_DECIMAL_POINT_QTY, ORDER_UOM_DECIMAL_POINT, ORDER_UOM_DECIMAL_POINT_AMT, CONS_UOM_DECIMAL_POINT_QTY, CONS_UOM_DECIMAL_POINT, CONS_UOM_DECIMAL_POINT_AMT from PRODUCT_DETAILS_MASTER where status_active=1 and item_category_id not in(1,2,3,13,14) and entry_form<>24 ");
	$item_data_arr=array();
	foreach($sql as $val)
	{
		$item_data_arr[$val["ID"]]["OUDPQTY"]=$val["ORDER_UOM_DECIMAL_POINT_QTY"];
		$item_data_arr[$val["ID"]]["OUDPRATE"]=$val["ORDER_UOM_DECIMAL_POINT"];
		$item_data_arr[$val["ID"]]["OUDPAMT"]=$val["ORDER_UOM_DECIMAL_POINT_AMT"];
		$item_data_arr[$val["ID"]]["CUDPQTY"]=$val["CONS_UOM_DECIMAL_POINT_QTY"];
		$item_data_arr[$val["ID"]]["CUDPRATE"]=$val["CONS_UOM_DECIMAL_POINT"];
		$item_data_arr[$val["ID"]]["CUDPAMT"]=$val["CONS_UOM_DECIMAL_POINT_AMT"];
	}
	return $item_data_arr;
}

function fnc_item_group_arr()
{
	$sql=sql_select("select ID, ORDER_UOM_DECIMAL_POINT_QTY, ORDER_UOM_DECIMAL_POINT, ORDER_UOM_DECIMAL_POINT_AMT, CONS_UOM_DECIMAL_POINT_QTY, CONS_UOM_DECIMAL_POINT, CONS_UOM_DECIMAL_POINT_AMT from LIB_ITEM_GROUP where status_active=1 and item_category=4");
	$item_data_arr=array();
	foreach($sql as $val)
	{
		$item_data_arr[$val["ID"]]["OUDPQTY"]=$val["ORDER_UOM_DECIMAL_POINT_QTY"];
		$item_data_arr[$val["ID"]]["OUDPRATE"]=$val["ORDER_UOM_DECIMAL_POINT"];
		$item_data_arr[$val["ID"]]["OUDPAMT"]=$val["ORDER_UOM_DECIMAL_POINT_AMT"];
		$item_data_arr[$val["ID"]]["CUDPQTY"]=$val["CONS_UOM_DECIMAL_POINT_QTY"];
		$item_data_arr[$val["ID"]]["CUDPRATE"]=$val["CONS_UOM_DECIMAL_POINT"];
		$item_data_arr[$val["ID"]]["CUDPAMT"]=$val["CONS_UOM_DECIMAL_POINT_AMT"];
	}
	return $item_data_arr;
}

function fnc_tempengine($table_name="", $user_id="", $entry_form="", $ref_from="", $ref_id_arr="",  $ref_str_arr="")
{
	global $con ;
	
	$numeless=count($ref_id_arr);
	//echo $con.'='.$user_id.'='.$entry_form.'='.$ref_from.'='.$ref_id_arr;
	//print_r($ref_id_arr);
	$psql = "BEGIN PRC_TEMPENGINE(:in_user_id,:in_ref_from,:in_entry_form,:in_ref_id_arr, :in_ref_table); END;";//:in_ref_str_arr, 
	$stmt = oci_parse($con,$psql);
	oci_bind_by_name($stmt,":in_user_id",$user_id);
	oci_bind_by_name($stmt,":in_entry_form",$entry_form);
	oci_bind_by_name($stmt,":in_ref_from",$ref_from);
	
	oci_bind_array_by_name($stmt, ":in_ref_id_arr", $ref_id_arr, $numeless, -1, SQLT_INT);
	//oci_bind_array_by_name($stmt, ":in_ref_str_arr", $ref_str_arr, $numeless, -1, SQLT_CHR);
	
	oci_bind_by_name($stmt,":in_ref_table",$table_name);
	oci_execute($stmt); 
	 //echo "jahid";
	oci_commit($con);
	disconnect($con);
}

function fnc_isdyeingplan($table_name="", $ref_value_arr="")
{
	global $con ;
	$numeless=count($ref_value_arr);
	//echo $table_name.'='.$ref_value_arr; PRC_IS_DYEING_PLAN PRC_IS_KNIT_DYE_PLAN
	$psql = "BEGIN PRC_IS_DYEING_PLAN(:in_jobid_arr, :in_ref_table); END;";
	$stmt = oci_parse($con,$psql);
	oci_bind_by_name($stmt,":in_ref_table",$table_name);
	oci_bind_array_by_name($stmt, ":in_jobid_arr", $ref_value_arr, $numeless, -1, SQLT_INT);
	//oci_bind_by_name($stmt,":in_ref_table",$table_name);
	oci_execute($stmt); 
	oci_commit($con);
	disconnect($con);
}


function set_print_button($dataArr=array()){
	//Ref page: order\woven_order\requires\pre_cost_entry_controller_v2.php

	$btnSql = "select BUTTON_ID from LIB_REPORT_TEMPLATE a,LIB_REPORT_TEMPLATE_DETAILS b where a.id=b.mst_id and (b.USER_ID = {$dataArr['USER_ID']} or b.USER_ID = 0) and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 AND a.template_name ={$dataArr['COMPANY_ID']} and a.module_id={$dataArr['MODULE_ID']} and a.report_id={$dataArr['REPORT_ID']}";
	$btnSqlRes = sql_select($btnSql);
	$btnDataArr=array();
	foreach($btnSqlRes as $btnRow){
		$btnDataArr[$btnRow['BUTTON_ID']]=$btnRow['BUTTON_ID'];
	}
	return $btnDataStr = implode(',',$btnDataArr);

}

/*
|--------------------------------------------------------------------------
| get_page_permission
| written by : Md. Helal Uddin
|--------------------------------------------------------------------------
| This function will return page permission string
| Calling procedure : $permission = get_page_permission($_SERVER['REQUEST_URI'],$_SESSION['menu_id']);
| Note: need to call bellow extract($_REQUEST); in page 
|
*/
function get_page_permission($uri = '',$mid='',$user_id='')
{
    if(!isset($uri))
    {
        $uri = $_SERVER['REQUEST_URI'];
    }
    $uri = trim($uri, '/');
    $uri_arr = explode("/", explode("?",$uri)[0]);
    $uri_sub = array_slice($uri_arr,1,count($uri_arr));
    $uri = "page_container.php?m=".implode("/",$uri_sub);
    if(empty($user_id))
    {
        $user_id = $_SESSION['logic_erp']['user_id'];
    }

    $menu_cond = "";
    if(!empty($mid))
    {
        $menu_cond = " AND a.m_menu_id = $mid";
    }

    $sql = " SELECT B.SAVE_PRIV,B.EDIT_PRIV,B.DELETE_PRIV,B.APPROVE_PRIV
                FROM main_menu a, user_priv_mst b
                WHERE     a.status = 1 AND a.is_mobile_menu NOT IN (1) AND a.m_menu_id = b.main_menu_id AND b.valid = 1 AND b.user_id = $user_id and trim(a.f_location) = '".trim($uri)."' and a.IS_DELETED = 0 and a.STATUS_ACTIVE = 1 $menu_cond";
				
    $result = sql_select($sql);
    $permission = "";
    foreach( $result as $r_sql)
    {
        $permission=$r_sql[csf('SAVE_PRIV')] . "_" .  $r_sql[csf('EDIT_PRIV')] . "_" . $r_sql[csf('DELETE_PRIV')] . "_" .  $r_sql[csf('APPROVE_PRIV')];
    }
    return $permission;
}

function get_garments_item_array($pnature)//ISD-23-20335 confirmation by jahid hasan, saeed, nasir
{
	$data = return_library_array("SELECT a.ID, a.ITEM_NAME FROM LIB_GARMENT_ITEM a, LIB_GARMENT_ITEM_TAG_NATURE b WHERE a.ID=b.GARMENT_ITEM_ID and b.PRODUCT_NATURE='$pnature' and STATUS_ACTIVE=1 AND IS_DELETED=0 ORDER BY ITEM_NAME", "ID", "ITEM_NAME");
	if(count($data)==0)
	{
		global $garments_item;
		$data=$garments_item;
	}
	return $data;
}

function filter_string($inputString){
	return preg_replace('/[^A-Za-z0-9\-@#_ =]/', '', $inputString);
}

function get_report_button_array($company_id, $module_id, $report_id,$user_id,$button_id_arr)
{
	//echo "SELECT b.button_id FROM lib_report_template a, lib_report_template_details b WHERE a.id=b.mst_id and b.user_id in ($user_id, 0) and a.template_name in ($company_id) and a.module_id=$module_id and a.report_id=$report_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.button_id";die;

	$data_arr = return_library_array("SELECT b.button_id FROM lib_report_template a, lib_report_template_details b WHERE a.id=b.mst_id and b.user_id in ($user_id, 0) and a.template_name in ($company_id) and a.module_id=$module_id and a.report_id=$report_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.button_id", "button_id", "button_id");
  
	//print_r($button_id_arr);die;

	foreach($button_id_arr as $idtxt){
		list($id, $btn_id) = explode('#', $idtxt);
		echo (in_array($id,$data_arr))?"$('#{$btn_id}').show();\n":"$('#{$btn_id}').hide();\n";
	}
}

/* 
Note : This function is use for showing notification msg
*/
if(!function_exists("fn_show_msg"))
{
	function fn_show_msg($type="",$msg="")
	{
		?>
		<div class='notifi_msg_content'>
		<?
		if($type==1)
		{
			?>
			<div class="alert alert-danger alert-white rounded">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true" onclick="parentNode.remove();">×</button> 
				<div class="icon"><i class="fa fa-times"></i></div>
				<strong>Error!</strong> <?=$msg;?>
			</div>
			<?
		}
		else if($type==2)
		{
			?>
			<div class="alert alert-warning alert-white rounded">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true" onclick="parentNode.remove();">×</button>
				<div class="icon"><i class="fa fa-warning"></i></div>
				<strong>Alert!</strong> <?=$msg;?>
			</div>
			<?
		}
		else if($type==3)
		{
			?>
			<div class="alert alert-info alert-white rounded">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true" onclick="parentNode.remove();">×</button>
				<div class="icon"><i class="fa fa-info-circle"></i></div>
				<strong>Info!</strong> <?=$msg;?>
			</div>
			<?
		}
		else
		{
			?>
			<div class="alert alert-success alert-white rounded">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true" onclick="parentNode.remove();">×</button>
				<div class="icon"><i class="fa fa-check"></i></div>
				<strong>Success!</strong> <?=$msg;?>
			</div>
			<?
		}
		?>
		</div>
		<?
		die;
	}
}
?>