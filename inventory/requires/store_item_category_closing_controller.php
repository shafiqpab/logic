<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];


if($action=="load_drop_down_year")
{
	//$sql=sql_select("select id, period_name as year_start from lib_ac_period_mst where status_active=1 and is_deleted=0 and is_closed=0 and company_id=$data order by year_start");
	//$selected_year_arr=array($sql[0][csf("id")]=>$sql[0][csf("year_start")]);
	$sql="select id,period_name as year_start from lib_ac_period_mst where status_active=1 and is_deleted=0 and is_closed=0 and company_id=$data order by year_start";
	echo create_drop_down( "cbo_year", 150, $sql,"id,year_start",1, "-- Select --", 0, "load_drop_down( 'requires/store_item_category_closing_controller',this.value, 'load_drop_down_month', 'month_td' );load_drop_down( 'requires/store_item_category_closing_controller',this.value+'**'+$('#cbo_company_id').val(), 'load_drop_down_category', 'category_td' );" );
	exit(); 
}


if($action=="load_drop_down_month")
{
	$sql="select month_id, financial_period as financial_period from lib_ac_period_dtls where status_active=1 and is_deleted=0 and period_locked=0 and mst_id=$data and financial_period not in('Opening','Closing','Post Closing')";
	//echo $sql;die;
	echo create_drop_down( "cbo_month", 150, $sql,"month_id,financial_period",1, "-- Select --", 0, "load_drop_down( 'requires/store_item_category_closing_controller',$('#cbo_year').val()+'**'+$('#cbo_company_id').val()+'**'+this.value, 'load_drop_down_category', 'category_td' );" );
	exit(); 
}

if($action=="load_drop_down_category")
{
	//echo $data;die;
	$data_ref=explode("**",$data);
	$year_id=trim($data_ref[0]);
	$com_id=trim($data_ref[1]);
	$month_id=trim($data_ref[2]);
	$month_cond="";
	if($month_id) $month_cond=" and b.month_id=$month_id";
	$sql_period="select b.id as DTLS_ID, b.period_starting_date as PERIOD_STARTING_DATE, b.period_ending_date as PERIOD_ENDING_DATE from lib_ac_period_mst a, lib_ac_period_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_closed=0 and a.is_locked=0 and b.period_locked=0 and b.is_locked=0 and financial_period not in('Opening','Closing','Post Closing') and a.company_id=$data_ref[1] and a.id=$data_ref[0] $month_cond order by DTLS_ID";
	//echo $sql_period;die;
	$sql_period_result=sql_select($sql_period);
	$priod_data=array();$cat_period_ending_date="";
	foreach($sql_period_result as $row)
	{
		/*$priod_data[$row["DTLS_ID"]]["id"]=$row["DTLS_ID"];
		$priod_data[$row["DTLS_ID"]]["PERIOD_STARTING_DATE"]=$row["PERIOD_STARTING_DATE"];
		$priod_data[$row["DTLS_ID"]]["PERIOD_ENDING_DATE"]=$row["PERIOD_ENDING_DATE"];*/
		$cat_period_ending_date=$row["PERIOD_ENDING_DATE"];
	}
	unset($sql_period_result);
	//echo $sql_period;die;
	$sql="select id as ID, category_id as CATEGORY_ID, actual_category_name as ACTUAL_CATEGORY_NAME, ac_period_dtls_id as AC_PERIOD_DTLS_ID, period_ending_date as PERIOD_ENDING_DATE from lib_item_category_comp_wise where status_active=1 and is_deleted=0 and is_inventory=1 and company_id=$com_id order by actual_category_name";
	$sql_result=sql_select($sql);
	$category_data=array();
	foreach($sql_result as $row)
	{
		if($row["PERIOD_ENDING_DATE"]!="" && $row["PERIOD_ENDING_DATE"]!="0000-00-00")
		{
			//echo strtotime($cat_period_ending_date)."=".strtotime($row["PERIOD_ENDING_DATE"])."*";
			if( strtotime($cat_period_ending_date) > strtotime($row["PERIOD_ENDING_DATE"]) )
			{
				$category_data[$row["CATEGORY_ID"]]=$row["ACTUAL_CATEGORY_NAME"];
				$test_category_data[$row["CATEGORY_ID"]]=$row["ACTUAL_CATEGORY_NAME"];
			}
		}
		else
		{
			$category_data[$row["CATEGORY_ID"]]=$row["ACTUAL_CATEGORY_NAME"];
		}
	}
	unset($sql_result);
	//echo "<pre>";print_r($test_category_data);die;
	echo create_drop_down( "cbo_item_category_id", 170, $category_data,"",1, "-- Select --", 0, "" );
	exit(); 
}



if ($action=="save_update_delete_process")
{
	extract($_REQUEST);
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	//ini_set('display_errors',1);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_year=str_replace("'","",$cbo_year);
	$cbo_month=str_replace("'","",$cbo_month);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	//echo $cbo_company_id."=".$cbo_year."=".$cbo_item_category_id;die; 
	
	if($cbo_company_id==0 || $cbo_year==0 || $cbo_item_category_id==0)
	{
		echo "50__Year Closing Process Not Allow"; die;
	}
	
	
	
	//###############   Closing year Check  #####################///
	$close_year_id=return_field_value("id","lib_ac_period_mst","status_active=1 and is_deleted=0 and is_closed=1 and is_locked=1 and id=$cbo_year","id");
	if($close_year_id!="")
	{
		echo "50__Year Closing Process Already Completed"; die;
	}
	
	$month_cond="";
	if($cbo_month>0)
	{
		//###############   Closing Month Check  #####################///
		$process_month_id=return_field_value("b.id as id","lib_ac_period_mst a, lib_ac_period_dtls b","a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.period_locked=1 and b.is_locked=1 and a.company_id=$cbo_company_id and a.id=$cbo_year and b.month_id=$cbo_month","id");
		if($process_month_id !="")
		{
			echo "50__Month Closing Process Already Completed"; die;
		}
		$month_cond=" and month_id <= $cbo_month";
	}
	
	//###############  Last period of closing   #####################///
	$sql_ac_month="select max(b.period_starting_date) as PERIOD_STARTING_DATE, max(b.period_ending_date) as PERIOD_ENDING_DATE from lib_ac_period_mst a, lib_ac_period_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.period_locked=1 and b.is_locked=1 and a.company_id=$cbo_company_id";
	//echo $process_month_id;die;
	$sql_ac_month_result=sql_select($sql_ac_month);
	$period_starting_date=$period_ending_date="";
	foreach($sql_ac_month_result as $row)
	{
		if($row["PERIOD_STARTING_DATE"]!="") $period_starting_date=$row["PERIOD_STARTING_DATE"];
		if($row["PERIOD_ENDING_DATE"]!="") $period_ending_date=$row["PERIOD_ENDING_DATE"];
	}
	unset($sql_ac_month_result);
	//echo $period_starting_date."=".$period_ending_date;die;
	
	//###############   Company wise year id so no need company check    #####################///
	
	$category_wise_ending_period=return_field_value("period_ending_date","lib_item_category_comp_wise","status_active=1 and is_deleted=0 and company_id=$cbo_company_id and category_id=$cbo_item_category_id and period_ending_date is not null","period_ending_date");
	//echo $category_wise_ending_period;die;
	$period_locked_cond="";
	if($category_wise_ending_period)
	{
		$period_locked_cond=" and period_ending_date > '$category_wise_ending_period' ";
	}
	else
	{
		$period_locked_cond=" and period_locked=0 ";
	}
	
	$sql_month="select month_id as MONTH_ID, financial_period as FINANCIAL_PERIOD, period_ending_date as PERIOD_ENDING_DATE from lib_ac_period_dtls where status_active=1 and is_deleted=0 and mst_id=$cbo_year and financial_period not in('Opening','Closing','Post Closing') $period_locked_cond $month_cond order by month_id";
	//echo $sql_month;die;
	$sql_month_result=sql_select($sql_month);
	$lib_month_arr=$lib_month_colum_arr=array();
	$i=1;$first_month="";
	foreach($sql_month_result as $row)
	{
		if(count($sql_month_result)==$i)
		{
			$year_month=date("Y-m",strtotime($row["PERIOD_ENDING_DATE"]));
			$priod_year=date("Y",strtotime($row["PERIOD_ENDING_DATE"]));
			$priod_month=date("m",strtotime($row["PERIOD_ENDING_DATE"]));
		}
		//$lib_month_arr[$row[csf("month_id")]]=date("m",strtotime($row[csf("financial_period")]));
		//$lib_period_month_arr[date("Y-m",strtotime($row[csf("period_ending_date")]))]=$row[csf("month_id")];
		$lib_year_month_arr[date("Y-m",strtotime($row["PERIOD_ENDING_DATE"]))]=date("Y-m",strtotime($row["PERIOD_ENDING_DATE"]));
		$lib_month_colum_arr["period_".$row["MONTH_ID"]]=date("Y-m",strtotime($row["PERIOD_ENDING_DATE"]));
		if($first_month=="") $first_month=date("Y-m",strtotime($row["PERIOD_ENDING_DATE"]));
		$i++;
	}
	unset($sql_month_result);
	//echo $first_month;die;
	//echo "<pre>";print_r($lib_month_colum_arr);die;
	//echo $cbo_company_id."=".$cbo_year."=".$cbo_month;die;
	
	
	
	//###############   Product wise previous closing data  #####################///
	$prev_close_item="select a.id as ID, a.prod_id as PROD_ID, a.closing as CLOSING, a.last_rate as LAST_RATE, a.closing_receive as CLOSING_RECEIVE, a.closing_issue as CLOSING_ISSUE, a.closing_receive_rtn as CLOSING_RECEIVE_RTN, a.closing_issue_rtn as CLOSING_ISSUE_RTN, a.closing_transfer_in as CLOSING_TRANSFER_IN, a.closing_transfer_out as CLOSING_TRANSFER_OUT 
	from year_close_item a, product_details_master b where a.prod_id=b.id and b.item_category_id=$cbo_item_category_id and a.company_id=$cbo_company_id and a.year_id=$cbo_year";
	$prev_close_item_result=sql_select($prev_close_item);
	$prev_close_item_data=array();
	foreach($prev_close_item_result as $row)
	{
		$prev_close_item_data[$row["PROD_ID"]]["ID"]=$row["ID"];
		$prev_close_item_data[$row["PROD_ID"]]["CLOSING"]=$row["CLOSING"];
		$prev_close_item_data[$row["PROD_ID"]]["CLOSING_AMT"]=$row["CLOSING"]*$row["LAST_RATE"];
		$prev_close_item_data[$row["PROD_ID"]]["CLOSING_RECEIVE"]=$row["CLOSING_RECEIVE"];
		$prev_close_item_data[$row["PROD_ID"]]["CLOSING_ISSUE"]=$row["CLOSING_ISSUE"];
		$prev_close_item_data[$row["PROD_ID"]]["CLOSING_RECEIVE_RTN"]=$row["CLOSING_RECEIVE_RTN"];
		$prev_close_item_data[$row["PROD_ID"]]["CLOSING_ISSUE_RTN"]=$row["CLOSING_ISSUE_RTN"];
		$prev_close_item_data[$row["PROD_ID"]]["CLOSING_TRANSFER_IN"]=$row["CLOSING_TRANSFER_IN"];
		$prev_close_item_data[$row["PROD_ID"]]["CLOSING_TRANSFER_OUT"]=$row["CLOSING_TRANSFER_OUT"];
	}
	unset($prev_close_item_result);
	//print_r($prev_close_item_data);die;
	
	//###############   Product and ref wise previous closing data  #####################///
	$prev_close_item_ref="select a.id as ID, a.prod_id as PROD_ID, a.ref_type as REF_TYPE, a.ref_id as REF_ID, a.closing as CLOSING, a.last_rate as LAST_RATE, a.closing_receive as CLOSING_RECEIVE, a.closing_issue as CLOSING_ISSUE, a.closing_receive_rtn as CLOSING_RECEIVE_RTN, a.closing_issue_rtn as CLOSING_ISSUE_RTN, a.closing_transfer_in as CLOSING_TRANSFER_IN, a.closing_transfer_out as CLOSING_TRANSFER_OUT 
	from year_close_item_ref a, product_details_master b where a.prod_id=b.id and b.item_category_id=$cbo_item_category_id and a.company_id=$cbo_company_id and a.year_id=$cbo_year";
	$prev_close_item_ref_result=sql_select($prev_close_item_ref);
	$prev_close_item_ref_data=array();
	foreach($prev_close_item_ref_result as $row)
	{
		$prev_ref_data[$row["PROD_ID"]][$row["REF_TYPE"]]=$row["REF_ID"];
		$prev_close_item_ref_data[$row["PROD_ID"]][$row["REF_TYPE"]][$row["REF_ID"]]["ID"]=$row["ID"];
		$prev_close_item_ref_data[$row["PROD_ID"]][$row["REF_TYPE"]][$row["REF_ID"]]["CLOSING"]=$row["CLOSING"];
		$prev_close_item_ref_data[$row["PROD_ID"]][$row["REF_TYPE"]][$row["REF_ID"]]["CLOSING_AMT"]=$row["CLOSING"]*$row["LAST_RATE"];
		$prev_close_item_ref_data[$row["PROD_ID"]][$row["REF_TYPE"]][$row["REF_ID"]]["CLOSING_RECEIVE"]=$row["CLOSING_RECEIVE"];
		$prev_close_item_ref_data[$row["PROD_ID"]][$row["REF_TYPE"]][$row["REF_ID"]]["CLOSING_ISSUE"]=$row["CLOSING_ISSUE"];
		$prev_close_item_ref_data[$row["PROD_ID"]][$row["REF_TYPE"]][$row["REF_ID"]]["CLOSING_RECEIVE_RTN"]=$row["CLOSING_RECEIVE_RTN"];
		$prev_close_item_ref_data[$row["PROD_ID"]][$row["REF_TYPE"]][$row["REF_ID"]]["CLOSING_ISSUE_RTN"]=$row["CLOSING_ISSUE_RTN"];
		$prev_close_item_ref_data[$row["PROD_ID"]][$row["REF_TYPE"]][$row["REF_ID"]]["CLOSING_TRANSFER_IN"]=$row["CLOSING_TRANSFER_IN"];
		$prev_close_item_ref_data[$row["PROD_ID"]][$row["REF_TYPE"]][$row["REF_ID"]]["CLOSING_TRANSFER_OUT"]=$row["CLOSING_TRANSFER_OUT"];
	}
	unset($prev_close_item_ref_result);
	
	//###############   Company Wise All Item  #####################///
	$all_item_sql="select id as ID from product_details_master where status_active=1 and is_deleted=0 and company_id=$cbo_company_id and item_category_id=$cbo_item_category_id";
	$all_item_result=sql_select($all_item_sql);
	
	//###############   Year Month Condition for transaction  #####################///
	if($db_type==0)
	{
		$year_cond=" and year(insert_date) <= '$priod_year'  and MONTH(insert_date) <= '$priod_month'";
		if($period_ending_date!="") $year_cond .=" and year(insert_date) >= '".date("Y",strtotime($period_ending_date))."'  and MONTH(insert_date) > '".date("m",strtotime($period_ending_date))."'";
		//if($cbo_month>0) $year_cond.=" and year(insert_date) <= '$priod_year' and MONTH(insert_date) <= '".$lib_month_arr[$cbo_month]."'";
	}
	else
	{
		$year_cond=" and to_char(insert_date,'YYYY-MM') <= '$year_month'";
		if($period_ending_date!="") $year_cond .=" and to_char(insert_date,'YYYY-MM') > '".date("Y-m",strtotime($period_ending_date))."'";
		//if($cbo_month>0) $year_cond.=" and to_char(insert_date,'YYYY-MM') <= '".$priod_year."-".$lib_month_arr[$cbo_month]."'";
	}
	
	//###############   Product Store Floor Room Rack Shelf Bin/Box Data set from Transaction  #####################///
	$sql_transac="select id as ID, company_id as COMPANY_ID, prod_id as PROD_ID, item_category as ITEM_CATEGORY, store_id as STORE_ID, floor_id as FLOOR_ID, room as ROOM, rack as RACK, self as SELF, bin_box as BIN_BOX, transaction_type as TRANSACTION_TYPE, cons_quantity as CONS_QUANTITY, cons_amount as CONS_AMOUNT, insert_date as INSERT_DATE, transaction_date as TRANSACTION_DATE
	from inv_transaction where status_active=1 and is_deleted=0 and item_category=$cbo_item_category_id and company_id=$cbo_company_id $year_cond 
	order by PROD_ID";
	//echo $sql_transac;die;
	$sql_trans_result=sql_select($sql_transac);
	//echo $sql_transac;die;
	//echo "<pre>";print_r($lib_year_month_arr);die;
	$data_pord=$data_pord_store=$data_pord_store_floor=$data_pord_store_floor_room=$data_pord_store_floor_room_rac=$data_pord_store_floor_room_rac_self=$data_pord_store_floor_room_rac_self_bin=array();
	foreach($sql_trans_result as $row)
	{
		$transaction_date=date("Y-m",strtotime($row["TRANSACTION_DATE"]));
		//echo $transaction_date;die;
		$transaction_test_arr[$row["PROD_ID"]]=$row["PROD_ID"];
		if($row["TRANSACTION_TYPE"] == 1 || $row["TRANSACTION_TYPE"] == 4 || $row["TRANSACTION_TYPE"] == 5)
		{
			if($lib_year_month_arr[$transaction_date]!="")
			{
				$data_pord[$row["PROD_ID"]][$lib_year_month_arr[$transaction_date]]["QNT"] = bcadd($data_pord[$row["PROD_ID"]][$lib_year_month_arr[$transaction_date]]["QNT"],$row["CONS_QUANTITY"],15);
				$data_pord[$row["PROD_ID"]][$lib_year_month_arr[$transaction_date]]["AMT"] =bcadd($data_pord[$row["PROD_ID"]][$lib_year_month_arr[$transaction_date]]["AMT"],$row["CONS_AMOUNT"],15);
				if($row["TRANSACTION_TYPE"] == 1) $data_pord[$row["PROD_ID"]][$lib_year_month_arr[$transaction_date]]["RECEIVE"] =bcadd($data_pord[$row["PROD_ID"]][$lib_year_month_arr[$transaction_date]]["RECEIVE"],$row["CONS_QUANTITY"],15);
				if($row["TRANSACTION_TYPE"] == 4) $data_pord[$row["PROD_ID"]][$lib_year_month_arr[$transaction_date]]["ISSUE_RTN"] =bcadd($data_pord[$row["PROD_ID"]][$lib_year_month_arr[$transaction_date]]["ISSUE_RTN"],$row["CONS_QUANTITY"],15);
				if($row["TRANSACTION_TYPE"] == 5) $data_pord[$row["PROD_ID"]][$lib_year_month_arr[$transaction_date]]["TRANSFER_IN"] =bcadd($data_pord[$row["PROD_ID"]][$lib_year_month_arr[$transaction_date]]["TRANSFER_IN"],$row["CONS_QUANTITY"],15);
				
				$data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$lib_year_month_arr[$transaction_date]]["QNT"] =bcadd($data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$lib_year_month_arr[$transaction_date]]["QNT"],$row["CONS_QUANTITY"],15);
				$data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$lib_year_month_arr[$transaction_date]]["AMT"] =bcadd($data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$lib_year_month_arr[$transaction_date]]["AMT"],$row["CONS_AMOUNT"],15);
				if($row["TRANSACTION_TYPE"] == 1) $data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$lib_year_month_arr[$transaction_date]]["RECEIVE"] =bcadd($data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$lib_year_month_arr[$transaction_date]]["RECEIVE"],$row["CONS_QUANTITY"],15);
				if($row["TRANSACTION_TYPE"] == 4) $data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$lib_year_month_arr[$transaction_date]]["ISSUE_RTN"] =bcadd($data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$lib_year_month_arr[$transaction_date]]["ISSUE_RTN"],$row["CONS_QUANTITY"],15);
				if($row["TRANSACTION_TYPE"] == 5) $data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$lib_year_month_arr[$transaction_date]]["TRANSFER_IN"] =bcadd($data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$lib_year_month_arr[$transaction_date]]["TRANSFER_IN"],$row["CONS_QUANTITY"],15);
				
				if($row["FLOOR_ID"])
				{
					$data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$lib_year_month_arr[$transaction_date]]["QNT"] =bcadd($data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$lib_year_month_arr[$transaction_date]]["QNT"],$row["CONS_QUANTITY"],15);
					$data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$lib_year_month_arr[$transaction_date]]["AMT"] =bcadd($data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$lib_year_month_arr[$transaction_date]]["AMT"],$row["CONS_AMOUNT"],15);
					if($row["TRANSACTION_TYPE"] == 1) $data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$lib_year_month_arr[$transaction_date]]["RECEIVE"] =bcadd($data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$lib_year_month_arr[$transaction_date]]["RECEIVE"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 4) $data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$lib_year_month_arr[$transaction_date]]["ISSUE_RTN"] =bcadd($data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$lib_year_month_arr[$transaction_date]]["ISSUE_RTN"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 5) $data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$lib_year_month_arr[$transaction_date]]["TRANSFER_IN"] =bcadd($data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$lib_year_month_arr[$transaction_date]]["TRANSFER_IN"],$row["CONS_QUANTITY"],15);
				}
				
				if($row["ROOM"])
				{
					$data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$lib_year_month_arr[$transaction_date]]["QNT"] =bcadd($data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$lib_year_month_arr[$transaction_date]]["QNT"],$row["CONS_QUANTITY"],15);
					$data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$lib_year_month_arr[$transaction_date]]["AMT"] =bcadd($data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$lib_year_month_arr[$transaction_date]]["AMT"],$row["CONS_AMOUNT"],15);
					if($row["TRANSACTION_TYPE"] == 1) $data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$lib_year_month_arr[$transaction_date]]["RECEIVE"] =bcadd($data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$lib_year_month_arr[$transaction_date]]["RECEIVE"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 4) $data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$lib_year_month_arr[$transaction_date]]["ISSUE_RTN"] =bcadd($data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$lib_year_month_arr[$transaction_date]]["ISSUE_RTN"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 5) $data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$lib_year_month_arr[$transaction_date]]["TRANSFER_IN"] =bcadd($data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$lib_year_month_arr[$transaction_date]]["TRANSFER_IN"],$row["CONS_QUANTITY"],15);
				}
				
				if($row["RACK"])
				{
					$data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$lib_year_month_arr[$transaction_date]]["QNT"] =bcadd($data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$lib_year_month_arr[$transaction_date]]["QNT"],$row["CONS_QUANTITY"],15);
					$data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$lib_year_month_arr[$transaction_date]]["AMT"] =bcadd($data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$lib_year_month_arr[$transaction_date]]["AMT"],$row["CONS_AMOUNT"],15);
					if($row["TRANSACTION_TYPE"] == 1) $data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$lib_year_month_arr[$transaction_date]]["RECEIVE"] =bcadd($data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$lib_year_month_arr[$transaction_date]]["RECEIVE"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 4) $data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$lib_year_month_arr[$transaction_date]]["ISSUE_RTN"] =bcadd($data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$lib_year_month_arr[$transaction_date]]["ISSUE_RTN"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 5) $data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$lib_year_month_arr[$transaction_date]]["TRANSFER_IN"] =bcadd($data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$lib_year_month_arr[$transaction_date]]["TRANSFER_IN"],$row["CONS_QUANTITY"],15);
				}
				
				if($row["SELF"])
				{
					$data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$lib_year_month_arr[$transaction_date]]["QNT"] =bcadd($data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$lib_year_month_arr[$transaction_date]]["QNT"],$row["CONS_QUANTITY"],15);
					$data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$lib_year_month_arr[$transaction_date]]["AMT"] =bcadd($data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$lib_year_month_arr[$transaction_date]]["AMT"],$row["CONS_AMOUNT"],15);
					if($row["TRANSACTION_TYPE"] == 1) $data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$lib_year_month_arr[$transaction_date]]["RECEIVE"] =bcadd($data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$lib_year_month_arr[$transaction_date]]["RECEIVE"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 4) $data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$lib_year_month_arr[$transaction_date]]["ISSUE_RTN"] =bcadd($data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$lib_year_month_arr[$transaction_date]]["ISSUE_RTN"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 5) $data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$lib_year_month_arr[$transaction_date]]["TRANSFER_IN"] =bcadd($data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$lib_year_month_arr[$transaction_date]]["TRANSFER_IN"],$row["CONS_QUANTITY"],15);
				}
				
				if($row["BIN_BOX"])
				{
					$data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$lib_year_month_arr[$transaction_date]]["QNT"] =bcadd($data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$lib_year_month_arr[$transaction_date]]["QNT"],$row["CONS_QUANTITY"],15);
					$data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$lib_year_month_arr[$transaction_date]]["AMT"] =bcadd($data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$lib_year_month_arr[$transaction_date]]["AMT"],$row["CONS_AMOUNT"],15);
					if($row["TRANSACTION_TYPE"] == 1) $data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$lib_year_month_arr[$transaction_date]]["RECEIVE"] =bcadd($data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$lib_year_month_arr[$transaction_date]]["RECEIVE"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 4) $data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$lib_year_month_arr[$transaction_date]]["ISSUE_RTN"] =bcadd($data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$lib_year_month_arr[$transaction_date]]["ISSUE_RTN"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 5) $data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$lib_year_month_arr[$transaction_date]]["TRANSFER_IN"] =bcadd($data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$lib_year_month_arr[$transaction_date]]["TRANSFER_IN"],$row["CONS_QUANTITY"],15);
				}
			}
			else
			{
				$data_pord[$row["PROD_ID"]][$first_month]["QNT"] =bcadd($data_pord[$row["PROD_ID"]][$first_month]["QNT"],$row["CONS_QUANTITY"],15);
				$data_pord[$row["PROD_ID"]][$first_month]["AMT"] =bcadd($data_pord[$row["PROD_ID"]][$first_month]["AMT"],$row["CONS_AMOUNT"],15);
				if($row["TRANSACTION_TYPE"] == 1) $data_pord[$row["PROD_ID"]][$first_month]["RECEIVE"] =bcadd($data_pord[$row["PROD_ID"]][$first_month]["RECEIVE"],$row["CONS_QUANTITY"],15);
				if($row["TRANSACTION_TYPE"] == 4) $data_pord[$row["PROD_ID"]][$first_month]["ISSUE_RTN"] =bcadd($data_pord[$row["PROD_ID"]][$first_month]["ISSUE_RTN"],$row["CONS_QUANTITY"],15);
				if($row["TRANSACTION_TYPE"] == 5) $data_pord[$row["PROD_ID"]][$first_month]["TRANSFER_IN"] =bcadd($data_pord[$row["PROD_ID"]][$first_month]["TRANSFER_IN"],$row["CONS_QUANTITY"],15);
				
				$data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$first_month]["QNT"] =bcadd($data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$first_month]["QNT"],$row["CONS_QUANTITY"],15);
				$data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$first_month]["AMT"] =bcadd($data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$first_month]["AMT"],$row["CONS_AMOUNT"],15);
				if($row["TRANSACTION_TYPE"] == 1) $data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$first_month]["RECEIVE"] =bcadd($data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$first_month]["RECEIVE"],$row["CONS_QUANTITY"],15);
				if($row["TRANSACTION_TYPE"] == 4) $data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$first_month]["ISSUE_RTN"] =bcadd($data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$first_month]["ISSUE_RTN"],$row["CONS_QUANTITY"],15);
				if($row["TRANSACTION_TYPE"] == 5) $data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$first_month]["TRANSFER_IN"] =bcadd($data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$first_month]["TRANSFER_IN"],$row["CONS_QUANTITY"],15);
				
				if($row["FLOOR_ID"])
				{
					$data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$first_month]["QNT"] =bcadd($data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$first_month]["QNT"],$row["CONS_QUANTITY"],15);
					$data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$first_month]["AMT"] =bcadd($data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$first_month]["AMT"],$row["CONS_AMOUNT"],15);
					if($row["TRANSACTION_TYPE"] == 1) $data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$first_month]["RECEIVE"] =bcadd( $data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$first_month]["RECEIVE"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 4) $data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$first_month]["ISSUE_RTN"] =bcadd($data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$first_month]["ISSUE_RTN"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 5) $data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$first_month]["TRANSFER_IN"] =bcadd($data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$first_month]["TRANSFER_IN"],$row["CONS_QUANTITY"],15);
				}
				
				if($row["ROOM"])
				{
					$data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$first_month]["QNT"] =bcadd($data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$first_month]["QNT"],$row["CONS_QUANTITY"],15);
					$data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$first_month]["AMT"] =bcadd($data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$first_month]["AMT"],$row["CONS_AMOUNT"],15);
					if($row["TRANSACTION_TYPE"] == 1) $data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$first_month]["RECEIVE"] =bcadd($data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$first_month]["RECEIVE"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 4) $data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$first_month]["ISSUE_RTN"] =bcadd($data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$first_month]["ISSUE_RTN"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 5) $data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$first_month]["TRANSFER_IN"] =bcadd($data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$first_month]["TRANSFER_IN"],$row["CONS_QUANTITY"],15);
				}
				
				if($row["RACK"])
				{
					$data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$first_month]["QNT"] =bcadd($data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$first_month]["QNT"],$row["CONS_QUANTITY"],15);
					$data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$first_month]["AMT"] =bcadd($data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$first_month]["AMT"],$row["CONS_AMOUNT"],15);
					if($row["TRANSACTION_TYPE"] == 1) $data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$first_month]["RECEIVE"] =bcadd($data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$first_month]["RECEIVE"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 4) $data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$first_month]["ISSUE_RTN"] =bcadd($data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$first_month]["ISSUE_RTN"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 5) $data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$first_month]["TRANSFER_IN"] =bcadd($data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$first_month]["TRANSFER_IN"],$row["CONS_QUANTITY"],15);
				}
				
				if($row["SELF"])
				{
					$data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$first_month]["QNT"] =bcadd($data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$first_month]["QNT"],$row["CONS_QUANTITY"],15);
					$data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$first_month]["AMT"] =bcadd($data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$first_month]["AMT"],$row["CONS_AMOUNT"],15);
					if($row["TRANSACTION_TYPE"] == 1) $data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$first_month]["RECEIVE"] =bcadd($data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$first_month]["RECEIVE"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 4) $data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$first_month]["ISSUE_RTN"] =bcadd($data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$first_month]["ISSUE_RTN"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 5) $data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$first_month]["TRANSFER_IN"] =bcadd($data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$first_month]["TRANSFER_IN"],$row["CONS_QUANTITY"],15);
				}
				
				if($row["BIN_BOX"])
				{
					$data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$first_month]["QNT"] =bcadd($data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$first_month]["QNT"],$row["CONS_QUANTITY"],15);
					$data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$first_month]["AMT"] =bcadd($data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$first_month]["AMT"],$row["CONS_AMOUNT"],15);
					if($row["TRANSACTION_TYPE"] == 1) $data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$first_month]["RECEIVE"] =bcadd($data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$first_month]["RECEIVE"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 4) $data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$first_month]["ISSUE_RTN"] =bcadd($data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$first_month]["ISSUE_RTN"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 5) $data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$first_month]["TRANSFER_IN"] =bcadd($data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$first_month]["TRANSFER_IN"],$row["CONS_QUANTITY"],15);
				}
			}
		}
		else
		{
			if($lib_year_month_arr[$transaction_date]!="")
			{
				$data_pord[$row["PROD_ID"]][$lib_year_month_arr[$transaction_date]]["QNT"] =bcsub($data_pord[$row["PROD_ID"]][$lib_year_month_arr[$transaction_date]]["QNT"],$row["CONS_QUANTITY"],15);
				$data_pord[$row["PROD_ID"]][$lib_year_month_arr[$transaction_date]]["AMT"] =bcsub($data_pord[$row["PROD_ID"]][$lib_year_month_arr[$transaction_date]]["AMT"],$row["CONS_AMOUNT"],15);				
				if($row["TRANSACTION_TYPE"] == 2) $data_pord[$row["PROD_ID"]][$lib_year_month_arr[$transaction_date]]["ISSUE"] =bcadd($data_pord[$row["PROD_ID"]][$lib_year_month_arr[$transaction_date]]["ISSUE"],$row["CONS_QUANTITY"],15);
				if($row["TRANSACTION_TYPE"] == 3) $data_pord[$row["PROD_ID"]][$lib_year_month_arr[$transaction_date]]["RECEIVE_RTN"] =bcadd($data_pord[$row["PROD_ID"]][$lib_year_month_arr[$transaction_date]]["RECEIVE_RTN"],$row["CONS_QUANTITY"],15);
				if($row["TRANSACTION_TYPE"] == 6) $data_pord[$row["PROD_ID"]][$lib_year_month_arr[$transaction_date]]["TRANSFER_OUT"] =bcadd($data_pord[$row["PROD_ID"]][$lib_year_month_arr[$transaction_date]]["TRANSFER_OUT"],$row["CONS_QUANTITY"],15);
				
				$data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$lib_year_month_arr[$transaction_date]]["QNT"] =bcsub($data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$lib_year_month_arr[$transaction_date]]["QNT"],$row["CONS_QUANTITY"],15);
				$data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$lib_year_month_arr[$transaction_date]]["AMT"] =bcsub($data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$lib_year_month_arr[$transaction_date]]["AMT"],$row["CONS_AMOUNT"],15);				
				if($row["TRANSACTION_TYPE"] == 2) $data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$lib_year_month_arr[$transaction_date]]["ISSUE"] =bcadd($data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$lib_year_month_arr[$transaction_date]]["ISSUE"],$row["CONS_QUANTITY"],15);
				if($row["TRANSACTION_TYPE"] == 3) $data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$lib_year_month_arr[$transaction_date]]["RECEIVE_RTN"] =bcadd($data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$lib_year_month_arr[$transaction_date]]["RECEIVE_RTN"],$row["CONS_QUANTITY"],15);
				if($row["TRANSACTION_TYPE"] == 6) $data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$lib_year_month_arr[$transaction_date]]["TRANSFER_OUT"] =bcadd($data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$lib_year_month_arr[$transaction_date]]["TRANSFER_OUT"],$row["CONS_QUANTITY"],15);
				
				if($row["FLOOR_ID"])
				{
					$data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$lib_year_month_arr[$transaction_date]]["QNT"] =bcsub($data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$lib_year_month_arr[$transaction_date]]["QNT"],$row["CONS_QUANTITY"],15);
					$data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$lib_year_month_arr[$transaction_date]]["AMT"] =bcsub($data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$lib_year_month_arr[$transaction_date]]["AMT"],$row["CONS_AMOUNT"],15);
					if($row["TRANSACTION_TYPE"] == 2) $data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$lib_year_month_arr[$transaction_date]]["ISSUE"] =bcadd($data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$lib_year_month_arr[$transaction_date]]["ISSUE"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 3) $data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$lib_year_month_arr[$transaction_date]]["RECEIVE_RTN"] =bcadd($data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$lib_year_month_arr[$transaction_date]]["RECEIVE_RTN"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 6) $data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$lib_year_month_arr[$transaction_date]]["TRANSFER_OUT"] =bcadd($data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$lib_year_month_arr[$transaction_date]]["TRANSFER_OUT"],$row["CONS_QUANTITY"],15);
				}
				
				if($row["ROOM"])
				{
					$data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$lib_year_month_arr[$transaction_date]]["QNT"] =bcsub($data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$lib_year_month_arr[$transaction_date]]["QNT"],$row["CONS_QUANTITY"],15);
					$data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$lib_year_month_arr[$transaction_date]]["AMT"] =bcsub($data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$lib_year_month_arr[$transaction_date]]["AMT"],$row["CONS_AMOUNT"],15);
					if($row["TRANSACTION_TYPE"] == 2) $data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$lib_year_month_arr[$transaction_date]]["ISSUE"] =bcadd($data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$lib_year_month_arr[$transaction_date]]["ISSUE"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 3) $data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$lib_year_month_arr[$transaction_date]]["RECEIVE_RTN"] =bcadd($data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$lib_year_month_arr[$transaction_date]]["RECEIVE_RTN"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 6) $data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$lib_year_month_arr[$transaction_date]]["TRANSFER_OUT"] =bcadd($data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$lib_year_month_arr[$transaction_date]]["TRANSFER_OUT"],$row["CONS_QUANTITY"],15);
				}
				
				if($row["RACK"])
				{
					$data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$lib_year_month_arr[$transaction_date]]["QNT"] =bcsub($data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$lib_year_month_arr[$transaction_date]]["QNT"],$row["CONS_QUANTITY"],15);
					$data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$lib_year_month_arr[$transaction_date]]["AMT"] =bcsub($data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$lib_year_month_arr[$transaction_date]]["AMT"],$row["CONS_AMOUNT"],15);
					if($row["TRANSACTION_TYPE"] == 2) $data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$lib_year_month_arr[$transaction_date]]["ISSUE"] =bcadd($data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$lib_year_month_arr[$transaction_date]]["ISSUE"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 3) $data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$lib_year_month_arr[$transaction_date]]["RECEIVE_RTN"] =bcadd($data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$lib_year_month_arr[$transaction_date]]["RECEIVE_RTN"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 6) $data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$lib_year_month_arr[$transaction_date]]["TRANSFER_OUT"] =bcadd($data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$lib_year_month_arr[$transaction_date]]["TRANSFER_OUT"],$row["CONS_QUANTITY"],15);
				}
				
				if($row["SELF"])
				{
					$data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$lib_year_month_arr[$transaction_date]]["QNT"] =bcsub($data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$lib_year_month_arr[$transaction_date]]["QNT"],$row["CONS_QUANTITY"],15);
					$data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$lib_year_month_arr[$transaction_date]]["AMT"] =bcsub($data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$lib_year_month_arr[$transaction_date]]["AMT"],$row["CONS_AMOUNT"],15);
					if($row["TRANSACTION_TYPE"] == 2) $data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$lib_year_month_arr[$transaction_date]]["ISSUE"] =bcadd($data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$lib_year_month_arr[$transaction_date]]["ISSUE"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 3) $data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$lib_year_month_arr[$transaction_date]]["RECEIVE_RTN"] =bcadd($data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$lib_year_month_arr[$transaction_date]]["RECEIVE_RTN"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 6) $data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$lib_year_month_arr[$transaction_date]]["TRANSFER_OUT"] =bcadd($data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$lib_year_month_arr[$transaction_date]]["TRANSFER_OUT"],$row["CONS_QUANTITY"],15);
				}
				
				if($row["BIN_BOX"])
				{
					$data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$lib_year_month_arr[$transaction_date]]["QNT"] =bcsub($data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$lib_year_month_arr[$transaction_date]]["QNT"],$row["CONS_QUANTITY"],15);
					$data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$lib_year_month_arr[$transaction_date]]["AMT"] =bcsub($data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$lib_year_month_arr[$transaction_date]]["AMT"],$row["CONS_AMOUNT"],15);
					if($row["TRANSACTION_TYPE"] == 2) $data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$lib_year_month_arr[$transaction_date]]["ISSUE"] =bcadd($data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$lib_year_month_arr[$transaction_date]]["ISSUE"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 3) $data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$lib_year_month_arr[$transaction_date]]["RECEIVE_RTN"] =bcadd($data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$lib_year_month_arr[$transaction_date]]["RECEIVE_RTN"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 6) $data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$lib_year_month_arr[$transaction_date]]["TRANSFER_OUT"] =bcadd($data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$lib_year_month_arr[$transaction_date]]["TRANSFER_OUT"],$row["CONS_QUANTITY"],15);
				}
				
				
			}
			else
			{
				$data_pord[$row["PROD_ID"]][$first_month]["QNT"] =bcsub($data_pord[$row["PROD_ID"]][$first_month]["QNT"],$row["CONS_QUANTITY"],15);
				$data_pord[$row["PROD_ID"]][$first_month]["AMT"] =bcsub($data_pord[$row["PROD_ID"]][$first_month]["AMT"],$row["CONS_AMOUNT"],15);
				if($row["TRANSACTION_TYPE"] == 2) $data_pord[$row["PROD_ID"]][$first_month]["ISSUE"] =bcadd($data_pord[$row["PROD_ID"]][$first_month]["ISSUE"],$row["CONS_QUANTITY"],15);
				if($row["TRANSACTION_TYPE"] == 3) $data_pord[$row["PROD_ID"]][$first_month]["RECEIVE_RTN"] =bcadd($data_pord[$row["PROD_ID"]][$first_month]["RECEIVE_RTN"],$row["CONS_QUANTITY"],15);
				if($row["TRANSACTION_TYPE"] == 6) $data_pord[$row["PROD_ID"]][$first_month]["TRANSFER_OUT"] =bcadd($data_pord[$row["PROD_ID"]][$first_month]["TRANSFER_OUT"],$row["CONS_QUANTITY"],15);
				
				$data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$first_month]["QNT"] =bcsub($data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$first_month]["QNT"],$row["CONS_QUANTITY"],15);
				$data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$first_month]["AMT"] =bcsub($data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$first_month]["AMT"],$row["CONS_AMOUNT"],15);
				if($row["TRANSACTION_TYPE"] == 2) $data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$first_month]["ISSUE"] =bcadd($data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$first_month]["ISSUE"],$row["CONS_QUANTITY"],15);
				if($row["TRANSACTION_TYPE"] == 3) $data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$first_month]["RECEIVE_RTN"] =bcadd($data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$first_month]["RECEIVE_RTN"],$row["CONS_QUANTITY"],15);
				if($row["TRANSACTION_TYPE"] == 6) $data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$first_month]["TRANSFER_OUT"] =bcadd($data_pord_store[$row["PROD_ID"]][$row["STORE_ID"]][$first_month]["TRANSFER_OUT"],$row["CONS_QUANTITY"],15);
				
				if($row["FLOOR_ID"])
				{
					$data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$first_month]["QNT"] =bcsub($data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$first_month]["QNT"],$row["CONS_QUANTITY"],15);
					$data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$first_month]["AMT"] =bcsub($data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$first_month]["AMT"],$row["CONS_AMOUNT"],15);
					if($row["TRANSACTION_TYPE"] == 2) $data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$first_month]["ISSUE"] =bcadd($data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$first_month]["ISSUE"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 3) $data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$first_month]["RECEIVE_RTN"] =bcadd($data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$first_month]["RECEIVE_RTN"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 6) $data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$first_month]["TRANSFER_OUT"] =bcadd($data_pord_store_floor[$row["PROD_ID"]][$row["FLOOR_ID"]][$first_month]["TRANSFER_OUT"],$row["CONS_QUANTITY"],15);
				}
				
				if($row["ROOM"])
				{
					$data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$first_month]["QNT"] =bcsub($data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$first_month]["QNT"],$row["CONS_QUANTITY"],15);
					$data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$first_month]["AMT"] =bcsub($data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$first_month]["AMT"],$row["CONS_AMOUNT"],15);
					if($row["TRANSACTION_TYPE"] == 2) $data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$first_month]["ISSUE"] =bcadd($data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$first_month]["ISSUE"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 3) $data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$first_month]["RECEIVE_RTN"] =bcadd($data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$first_month]["RECEIVE_RTN"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 6) $data_pord_store_floor_room[$row["PROD_ID"]][$row["ROOM"]][$first_month]["TRANSFER_OUT"] =bcadd($row["CONS_QUANTITY"],15);
				}
				
				if($row["RACK"])
				{
					$data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$first_month]["QNT"] =bcsub($data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$first_month]["QNT"],$row["CONS_QUANTITY"],15);
					$data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$first_month]["AMT"] =bcsub($data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$first_month]["AMT"],$row["CONS_AMOUNT"],15);
					if($row["TRANSACTION_TYPE"] == 2) $data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$first_month]["ISSUE"] =bcadd($data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$first_month]["ISSUE"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 3) $data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$first_month]["RECEIVE_RTN"] =bcadd($data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$first_month]["RECEIVE_RTN"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 6) $data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$first_month]["TRANSFER_OUT"] =bcadd($data_pord_store_floor_room_rac[$row["PROD_ID"]][$row["RACK"]][$first_month]["TRANSFER_OUT"],$row["CONS_QUANTITY"],15);
				}
				
				if($row["SELF"])
				{
					$data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$first_month]["QNT"] =bcsub($data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$first_month]["QNT"],$row["CONS_QUANTITY"],15);
					$data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$first_month]["AMT"] =bcsub($data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$first_month]["AMT"],$row["CONS_AMOUNT"],15);
					if($row["TRANSACTION_TYPE"] == 2) $data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$first_month]["ISSUE"] =bcadd($data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$first_month]["ISSUE"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 3) $data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$first_month]["RECEIVE_RTN"] =bcadd($data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$first_month]["RECEIVE_RTN"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 6) $data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$first_month]["TRANSFER_OUT"] =bcadd($data_pord_store_floor_room_rac_self[$row["PROD_ID"]][$row["SELF"]][$first_month]["TRANSFER_OUT"],$row["CONS_QUANTITY"],15);
				}
				
				if($row["BIN_BOX"])
				{
					$data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$first_month]["QNT"] =bcsub($data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$first_month]["QNT"],$row["CONS_QUANTITY"],15);
					$data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$first_month]["AMT"] =bcsub($data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$first_month]["AMT"],$row["CONS_AMOUNT"],15);
					if($row["TRANSACTION_TYPE"] == 2) $data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$first_month]["ISSUE"] =bcadd($data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$first_month]["ISSUE"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 3) $data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$first_month]["RECEIVE_RTN"] =bcadd($data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$first_month]["RECEIVE_RTN"],$row["CONS_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 6) $data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$first_month]["TRANSFER_OUT"] =bcadd($data_pord_store_floor_room_rac_self_bin[$row["PROD_ID"]][$row["BIN_BOX"]][$first_month]["TRANSFER_OUT"],$row["CONS_QUANTITY"],15);
				}
			}
		}
	}
	unset($sql_trans_result);
	//echo "<pre>";print_r($data_pord_store);die;
	
	//###############   Year Month Condition for Propotionate  #####################///
	if($db_type==0)
	{
		$year_cond_order=" and year(a.insert_date) <= '$priod_year'  and MONTH(insert_date) <= '$priod_month'";
		if($period_ending_date!="") $year_cond_order.=" and year(a.insert_date) >= '".date("Y",strtotime($period_ending_date))."'  and MONTH(a.insert_date) > '".date("m",strtotime($period_ending_date))."'";
		//if($cbo_month>0) $year_cond_order.=" and MONTH(a.insert_date) <= '".$lib_month_arr[$cbo_month]."'";
	}
	else
	{
		$year_cond_order=" and to_char(a.insert_date,'YYYY-MM') <= '$year_month'";
		if($period_ending_date!="") $year_cond_order .=" and to_char(a.insert_date,'YYYY-MM') > '".date("Y-m",strtotime($period_ending_date))."'";
		//if($cbo_month>0) $year_cond_order.=" and to_char(a.insert_date,'MM') <= '".$lib_month_arr[$cbo_month]."'";
	}
	
	//###############   Product Order Data set from Order Wise Propotionate  #####################///
	$sql_inv_order="select a.id as ID, b.company_id as COMPANY_ID, a.prod_id as PROD_ID, b.item_category_id as ITEM_CATEGORY, a.po_breakdown_id as PO_BREAKDOWN_ID, a.trans_type as TRANSACTION_TYPE, a.quantity as ORD_QUANTITY, a.order_amount as ORD_AMOUNT, a.insert_date as INSERT_DATE, a.is_sales as IS_SALES, c.transaction_date as TRANSACTION_DATE 
	from inv_transaction c, order_wise_pro_details a, product_details_master b
	where c.id=a.trans_id and a.prod_id=b.id and c.prod_id=b.id and a.TRANS_ID>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id=$cbo_item_category_id and b.company_id=$cbo_company_id $year_cond_order order by PROD_ID";
	//echo $sql_inv_order;die;
	
	$sql_inv_order_result=sql_select($sql_inv_order);
	$data_pord_order=array();
	foreach($sql_inv_order_result as $row)
	{
		$ord_transac_test_arr[$row["PROD_ID"]]=$row["PROD_ID"];
		$order_date=date("Y-m",strtotime($row["TRANSACTION_DATE"]));
		if($row["PO_BREAKDOWN_ID"])
		{
			$order_id_ref=$row["PO_BREAKDOWN_ID"]."*".$row["IS_SALES"];
			if($row["TRANSACTION_TYPE"] == 1 || $row["TRANSACTION_TYPE"] == 4 || $row["TRANSACTION_TYPE"] == 5)
			{
				if($lib_year_month_arr[$order_date]!="")
				{
					$data_pord_order[$row["PROD_ID"]][$order_id_ref][$lib_year_month_arr[$order_date]]["QNT"]=bcadd($data_pord_order[$row["PROD_ID"]][$order_id_ref][$lib_year_month_arr[$order_date]]["QNT"],$row["ORD_QUANTITY"],15);
					$data_pord_order[$row["PROD_ID"]][$order_id_ref][$lib_year_month_arr[$order_date]]["AMT"]=bcadd($data_pord_order[$row["PROD_ID"]][$order_id_ref][$lib_year_month_arr[$order_date]]["AMT"],$row["ORD_AMOUNT"],15);
					if($row["TRANSACTION_TYPE"] == 1) $data_pord_order[$row["PROD_ID"]][$order_id_ref][$lib_year_month_arr[$order_date]]["RECEIVE"]=bcadd($data_pord_order[$row["PROD_ID"]][$order_id_ref][$lib_year_month_arr[$order_date]]["RECEIVE"],$row["ORD_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 4) $data_pord_order[$row["PROD_ID"]][$order_id_ref][$lib_year_month_arr[$order_date]]["ISSUE_RTN"]=bcadd($data_pord_order[$row["PROD_ID"]][$order_id_ref][$lib_year_month_arr[$order_date]]["ISSUE_RTN"],$row["ORD_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 5) $data_pord_order[$row["PROD_ID"]][$order_id_ref][$lib_year_month_arr[$order_date]]["TRANSFER_IN"]=bcadd($data_pord_order[$row["PROD_ID"]][$order_id_ref][$lib_year_month_arr[$order_date]]["TRANSFER_IN"],$row["ORD_QUANTITY"],15);
				}
				else
				{
					$data_pord_order[$row["PROD_ID"]][$order_id_ref][$first_month]["QNT"]=bcadd($data_pord_order[$row["PROD_ID"]][$order_id_ref][$first_month]["QNT"],$row["ORD_QUANTITY"],15);
					$data_pord_order[$row["PROD_ID"]][$order_id_ref][$first_month]["AMT"]=bcadd($data_pord_order[$row["PROD_ID"]][$order_id_ref][$first_month]["AMT"],$row["ORD_AMOUNT"],15);
					if($row["TRANSACTION_TYPE"] == 1) $data_pord_order[$row["PROD_ID"]][$order_id_ref][$first_month]["RECEIVE"]=bcadd($data_pord_order[$row["PROD_ID"]][$order_id_ref][$first_month]["RECEIVE"],$row["ORD_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 4) $data_pord_order[$row["PROD_ID"]][$order_id_ref][$first_month]["ISSUE_RTN"]=bcadd($data_pord_order[$row["PROD_ID"]][$order_id_ref][$first_month]["ISSUE_RTN"],$row["ORD_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 5) $data_pord_order[$row["PROD_ID"]][$order_id_ref][$first_month]["TRANSFER_IN"]=bcadd($data_pord_order[$row["PROD_ID"]][$order_id_ref][$first_month]["TRANSFER_IN"],$row["ORD_QUANTITY"],15);
				}
				
			}
			else
			{
				if($lib_year_month_arr[$order_date]!="")
				{
					$data_pord_order[$row["PROD_ID"]][$order_id_ref][$lib_year_month_arr[$order_date]]["QNT"] =bcsub($data_pord_order[$row["PROD_ID"]][$order_id_ref][$lib_year_month_arr[$order_date]]["QNT"],$row["ORD_QUANTITY"],15);
					$data_pord_order[$row["PROD_ID"]][$order_id_ref][$lib_year_month_arr[$order_date]]["AMT"] =bcsub($data_pord_order[$row["PROD_ID"]][$order_id_ref][$lib_year_month_arr[$order_date]]["AMT"],$row["ORD_AMOUNT"],15);
					if($row["TRANSACTION_TYPE"] == 2) $data_pord_order[$row["PROD_ID"]][$order_id_ref][$lib_year_month_arr[$order_date]]["ISSUE"]=bcadd($data_pord_order[$row["PROD_ID"]][$order_id_ref][$lib_year_month_arr[$order_date]]["ISSUE"],$row["ORD_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 3) $data_pord_order[$row["PROD_ID"]][$order_id_ref][$lib_year_month_arr[$order_date]]["RECEIVE_RTN"]=bcadd($data_pord_order[$row["PROD_ID"]][$order_id_ref][$lib_year_month_arr[$order_date]]["RECEIVE_RTN"],$row["ORD_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 6) $data_pord_order[$row["PROD_ID"]][$order_id_ref][$lib_year_month_arr[$order_date]]["TRANSFER_OUT"]=bcadd($data_pord_order[$row["PROD_ID"]][$order_id_ref][$lib_year_month_arr[$order_date]]["TRANSFER_OUT"],$row["ORD_QUANTITY"],15);
				}
				else
				{
					$data_pord_order[$row["PROD_ID"]][$order_id_ref][$first_month]["QNT"] =bcsub($data_pord_order[$row["PROD_ID"]][$order_id_ref][$first_month]["QNT"],$row["ORD_QUANTITY"],15);
					$data_pord_order[$row["PROD_ID"]][$order_id_ref][$first_month]["AMT"] =bcsub($data_pord_order[$row["PROD_ID"]][$order_id_ref][$first_month]["AMT"],$row["ORD_AMOUNT"],15);
					if($row["TRANSACTION_TYPE"] == 2) $data_pord_order[$row["PROD_ID"]][$order_id_ref][$first_month]["ISSUE"]=bcadd($data_pord_order[$row["PROD_ID"]][$order_id_ref][$first_month]["ISSUE"],$row["ORD_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 3) $data_pord_order[$row["PROD_ID"]][$order_id_ref][$first_month]["RECEIVE_RTN"]=bcadd($data_pord_order[$row["PROD_ID"]][$order_id_ref][$first_month]["RECEIVE_RTN"],$row["ORD_QUANTITY"],15);
					if($row["TRANSACTION_TYPE"] == 6) $data_pord_order[$row["PROD_ID"]][$order_id_ref][$first_month]["TRANSFER_OUT"]=bcadd($data_pord_order[$row["PROD_ID"]][$order_id_ref][$first_month]["TRANSFER_OUT"],$row["ORD_QUANTITY"],15);
				}
			}
		}
	}
	unset($sql_inv_order_result);
	//echo "<pre>";print_r($data_pord_order);die;
	//echo "<pre>";print_r($data_pord_store);echo "<pre>";print_r($data_pord_store_floor);
	//$field_period_mst="is_closed*is_locked*updated_by*update_date";
	//$data_period_mst="1*1*'".$user_id."'*'".$pc_date_time."'";
	$update_period_mst=$update_period_dtls="";
	$update_period_mst_rid=$update_period_dtls_rid=$update_year_item_rid=$update_year_item_ref_rid=true;
	$fields_item=$fields_item_ref="";
	$fields_item="id, prod_id, company_id, year_id";
	$fields_item_ref="id, prod_id, ref_id, ref_type, company_id, year_id";
	foreach($lib_month_colum_arr as $key=>$value)
	{
		$fields_item.=",".$key;
		$fields_item.=",".$key."_rate";
		$fields_item.=",".$key."_receive";
		$fields_item.=",".$key."_issue";
		$fields_item.=",".$key."_receive_rtn";
		$fields_item.=",".$key."_issue_rtn";
		$fields_item.=",".$key."_transfer_in";
		$fields_item.=",".$key."_transfer_out";
		$fields_item_ref.=",".$key;
		$fields_item_ref.=",".$key."_rate";
		$fields_item_ref.=",".$key."_receive";
		$fields_item_ref.=",".$key."_issue";
		$fields_item_ref.=",".$key."_receive_rtn";
		$fields_item_ref.=",".$key."_issue_rtn";
		$fields_item_ref.=",".$key."_transfer_in";
		$fields_item_ref.=",".$key."_transfer_out";
	}
	$fields_item.=", closing, last_rate, closing_receive, closing_issue, closing_receive_rtn, closing_issue_rtn, closing_transfer_in, closing_transfer_out";
	$fields_item_ref.=", closing, last_rate, closing_receive, closing_issue, closing_receive_rtn, closing_issue_rtn, closing_transfer_in, closing_transfer_out";
	
	//echo $fields_item."<br>".$fields_item_ref;die;
	//$data_pord=$data_pord_store=$data_pord_store_floor=$data_pord_store_floor_room=$data_pord_store_floor_room_rac=$data_pord_store_floor_room_rac_self=$data_pord_store_floor_room_rac_self_bin=array();
	$year_item_id=return_next_id("id", "year_close_item", 1);
	$year_item_ref_id = return_next_id("id", "year_close_item_ref", 1);
	//foreach($data_pord as $prod_id=>$prod_data)
	foreach($all_item_result as $val)
	{
		$prod_id=$val["ID"];
		$values_item="";
		$insert_year_close_item="";
		//###############   If next month quantity rate not found then carry previous month value #####################///
		if($transaction_test_arr[$prod_id])
		{
			$prod_qnty=$prod_amt=$prod_rate=$receive_qnty=$issue_qnty=$receive_rtn_qnty=$issue_rtn_qnty=$transfer_in_qnty=$transfer_out_qnty=0;
			if($prev_close_item_data[$prod_id]["ID"]=="")
			{
				//$year_item_id = return_next_id_by_sequence("YEAR_CLOSE_ITEM_PK_SEQ", "year_close_item", $con);
				$values_item="(".$year_item_id.",".$prod_id.",".$cbo_company_id.",".$cbo_year;
				foreach($lib_month_colum_arr as $key=>$value)
				{
					//###############   This Check carry previous month value #####################///
					if($data_pord[$prod_id][$value]["QNT"]) 
					{
						$prod_qnty =bcadd($prod_qnty,$data_pord[$prod_id][$value]["QNT"],15);
					}
					if($data_pord[$prod_id][$value]["AMT"]) 
					{
						$prod_amt=bcadd($prod_amt,$data_pord[$prod_id][$value]["AMT"],15);
					}
					if($data_pord[$prod_id][$value]["RECEIVE"]) 
					{
						$receive_qnty=bcadd($receive_qnty,$data_pord[$prod_id][$value]["RECEIVE"],15);
					}
					if($data_pord[$prod_id][$value]["ISSUE"]) 
					{
						$issue_qnty=bcadd($issue_qnty,$data_pord[$prod_id][$value]["ISSUE"],15);
					}
					if($data_pord[$prod_id][$value]["RECEIVE_RTN"]) 
					{
						$receive_rtn_qnty=bcadd($receive_rtn_qnty,$data_pord[$prod_id][$value]["RECEIVE_RTN"],15);
					}
					if($data_pord[$prod_id][$value]["ISSUE_RTN"]) 
					{
						$issue_rtn_qnty=bcadd($issue_rtn_qnty,$data_pord[$prod_id][$value]["ISSUE_RTN"],15);
					}
					if($data_pord[$prod_id][$value]["TRANSFER_IN"]) 
					{
						$transfer_in_qnty=bcadd($transfer_in_qnty,$data_pord[$prod_id][$value]["TRANSFER_IN"],15);
					}
					if($data_pord[$prod_id][$value]["TRANSFER_OUT"]) 
					{
						$transfer_out_qnty=bcadd($transfer_out_qnty,$data_pord[$prod_id][$value]["TRANSFER_OUT"],15);
					}
					
					if($prod_amt!=0 && $prod_qnty!=0) $prod_rate=number_format(($prod_amt/$prod_qnty),15,".",""); else $prod_rate=0;
					$values_item.=",'".$prod_qnty."','".$prod_rate."','".$receive_qnty."','".$issue_qnty."','".$receive_rtn_qnty."','".$issue_rtn_qnty."','".$transfer_in_qnty."','".$transfer_out_qnty."'";
				}
				if($prod_amt!=0 && $prod_qnty!=0) $prod_rate=number_format(($prod_amt/$prod_qnty),15,".",""); else $prod_rate=0;
				$values_item.=",".$prod_qnty.",".$prod_rate.",'".$receive_qnty."','".$issue_qnty."','".$receive_rtn_qnty."','".$issue_rtn_qnty."','".$transfer_in_qnty."','".$transfer_out_qnty."')";
				//echo $values_item;oci_rollback($con);die;
				$insert_year_close_item="insert into year_close_item ($fields_item) values $values_item";
				$year_item_id++;
			}
			else
			{
				$prod_qnty=$prev_close_item_data[$prod_id]["CLOSING"];
				$prod_amt=$prev_close_item_data[$prod_id]["CLOSING_AMT"];
				$receive_qnty=$prev_close_item_data[$prod_id]["CLOSING_RECEIVE"];
				$issue_qnty=$prev_close_item_data[$prod_id]["CLOSING_ISSUE"];
				$receive_rtn_qnty=$prev_close_item_data[$prod_id]["CLOSING_RECEIVE_RTN"];
				$issue_rtn_qnty=$prev_close_item_data[$prod_id]["CLOSING_ISSUE_RTN"];
				$transfer_in_qnty=$prev_close_item_data[$prod_id]["CLOSING_TRANSFER_IN"];
				$transfer_out_qnty=$prev_close_item_data[$prod_id]["CLOSING_TRANSFER_OUT"];
				$insert_year_close_item="update year_close_item set ";
				foreach($lib_month_colum_arr as $key=>$value)
				{
					if($data_pord[$prod_id][$value]["QNT"]) 
					{
						$prod_qnty =bcadd($prod_qnty,$data_pord[$prod_id][$value]["QNT"],15);
					}
					if($data_pord[$prod_id][$value]["AMT"]) 
					{
						$prod_amt =bcadd($prod_amt,$data_pord[$prod_id][$value]["AMT"],15);
					}
					if($data_pord[$prod_id][$value]["RECEIVE"]) 
					{
						$receive_qnty=bcadd($receive_qnty,$data_pord[$prod_id][$value]["RECEIVE"],15);
					}
					if($data_pord[$prod_id][$value]["ISSUE"]) 
					{
						$issue_qnty=bcadd($issue_qnty,$data_pord[$prod_id][$value]["ISSUE"],15);
					}
					if($data_pord[$prod_id][$value]["RECEIVE_RTN"]) 
					{
						$receive_rtn_qnty=bcadd($receive_rtn_qnty,$data_pord[$prod_id][$value]["RECEIVE_RTN"],15);
					}
					if($data_pord[$prod_id][$value]["ISSUE_RTN"]) 
					{
						$issue_rtn_qnty=bcadd($issue_rtn_qnty,$data_pord[$prod_id][$value]["ISSUE_RTN"],15);
					}
					if($data_pord[$prod_id][$value]["TRANSFER_IN"]) 
					{
						$transfer_in_qnty=bcadd($transfer_in_qnty,$data_pord[$prod_id][$value]["TRANSFER_IN"],15);
					}
					if($data_pord[$prod_id][$value]["TRANSFER_OUT"]) 
					{
						$transfer_out_qnty=bcadd($transfer_out_qnty,$data_pord[$prod_id][$value]["TRANSFER_OUT"],15);
					}
					//echo $prod_qnty."=".$data_pord[$prod_id][$value]["QNT"]."=".$prev_close_item_data[$prod_id]["CLOSING"]."=".$value;die;
					
					if($prod_amt!=0 && $prod_qnty!=0) $prod_rate=number_format(($prod_amt/$prod_qnty),15,".",""); else $prod_rate=0;
					$insert_year_close_item .="$key='".$prod_qnty."', $key"."_rate='".$prod_rate."', $key"."_receive='".$receive_qnty."', $key"."_issue='".$issue_qnty."', $key"."_receive_rtn='".$receive_rtn_qnty."', $key"."_issue_rtn='".$issue_rtn_qnty."', $key"."_transfer_in='".$transfer_in_qnty."', $key"."_transfer_out='".$transfer_out_qnty."',";
				}
				if($prod_amt!=0 && $prod_qnty!=0) $prod_rate=number_format(($prod_amt/$prod_qnty),15,".",""); else $prod_rate=0;
				$insert_year_close_item .="closing='".$prod_qnty."', last_rate='".$prod_rate."', closing_receive='".$receive_qnty."', closing_issue='".$issue_qnty."', closing_receive_rtn='".$receive_rtn_qnty."', closing_issue_rtn='".$issue_rtn_qnty."', closing_transfer_in='".$transfer_in_qnty."', closing_transfer_out='".$transfer_out_qnty."' where id='".$prev_close_item_data[$prod_id]["ID"]."'";
			}
			
			if($insert_year_close_item!="")
			{
				$update_year_item_rid=execute_query($insert_year_close_item);
				if($update_year_item_rid) 
				{
					$update_year_item_rid=1;
				}
				else
				{
					$update_year_item_rid=0;
					echo $insert_year_close_item;oci_rollback($con);die;
				}
			}
			
			foreach($data_pord_store[$prod_id] as $store_id=>$store_data)
			{
				if($store_id)
				{
					$values_item_ref="";$insert_year_close_item_ref="";
					$store_qnty=$store_amt=$store_rate=$receive_qnty=$issue_qnty=$receive_rtn_qnty=$issue_rtn_qnty=$transfer_in_qnty=$transfer_out_qnty=0;
					if($prev_close_item_ref_data[$prod_id][1][$store_id]["ID"]=="")
					{
						//$year_item_ref_id = return_next_id_by_sequence("YEAR_CLOSE_ITEM_REF_PK_SEQ", "year_close_item_ref", $con);
						$values_item_ref="(".$year_item_ref_id.",".$prod_id.",".$store_id.",1,".$cbo_company_id.",".$cbo_year;
						foreach($lib_month_colum_arr as $key=>$value)
						{
							//###############   This Check carry previous month value #####################///
							if($store_data[$value]["QNT"]) 
							{
								$store_qnty =bcadd($store_qnty,$store_data[$value]["QNT"],15);
							}
							if($store_data[$value]["AMT"]) 
							{
								$store_amt =bcadd($store_amt,$store_data[$value]["AMT"],15);
							}
							if($store_data[$value]["RECEIVE"]) 
							{
								$receive_qnty=bcadd($receive_qnty,$store_data[$value]["RECEIVE"],15);
							}
							if($store_data[$value]["ISSUE"]) 
							{
								$issue_qnty=bcadd($issue_qnty,$store_data[$value]["ISSUE"],15);
							}
							if($store_data[$value]["RECEIVE_RTN"]) 
							{
								$receive_rtn_qnty=bcadd($receive_rtn_qnty,$store_data[$value]["RECEIVE_RTN"],15);
							}
							if($store_data[$value]["ISSUE_RTN"]) 
							{
								$issue_rtn_qnty=bcadd($issue_rtn_qnty,$store_data[$value]["ISSUE_RTN"],15);
							}
							if($store_data[$value]["TRANSFER_IN"]) 
							{
								$transfer_in_qnty=bcadd($transfer_in_qnty,$store_data[$value]["TRANSFER_IN"],15);
							}
							if($store_data[$value]["TRANSFER_OUT"]) 
							{
								$transfer_out_qnty=bcadd($transfer_out_qnty,$store_data[$value]["TRANSFER_OUT"],15);
							}
							
							if($store_amt!=0 && $store_qnty!=0) $store_rate=number_format(($store_amt/$store_qnty),15,".",""); else $store_rate=0;
							$values_item_ref.=",'".$store_qnty."','".$store_rate."','".$receive_qnty."','".$issue_qnty."','".$receive_rtn_qnty."','".$issue_rtn_qnty."','".$transfer_in_qnty."','".$transfer_out_qnty."'";
						}
						if($store_amt!=0 && $store_qnty!=0) $store_rate=number_format(($store_amt/$store_qnty),15,".",""); else $store_rate=0;
						$values_item_ref.=",".$store_qnty.",".$store_rate.",'".$receive_qnty."','".$issue_qnty."','".$receive_rtn_qnty."','".$issue_rtn_qnty."','".$transfer_in_qnty."','".$transfer_out_qnty."')";
						//echo $values_item;oci_rollback($con);die;
						$insert_year_close_item_ref="insert into year_close_item_ref ($fields_item_ref) values $values_item_ref";
						$year_item_ref_id++;
						$update_year_item_ref_rid=execute_query($insert_year_close_item_ref);
						if($update_year_item_ref_rid) $update_year_item_ref_rid=1;
						else
						{
							$update_year_item_ref_rid=0;
							echo $insert_year_close_item_ref;oci_rollback($con);die;
						}
					}
					else
					{
						$store_qnty=$prev_close_item_ref_data[$prod_id][1][$store_id]["CLOSING"];
						$store_amt=$prev_close_item_ref_data[$prod_id][1][$store_id]["CLOSING_AMT"];
						$receive_qnty=$prev_close_item_ref_data[$prod_id][1][$store_id]["CLOSING_RECEIVE"];
						$issue_qnty=$prev_close_item_ref_data[$prod_id][1][$store_id]["CLOSING_ISSUE"];
						$receive_rtn_qnty=$prev_close_item_ref_data[$prod_id][1][$store_id]["CLOSING_RECEIVE_RTN"];
						$issue_rtn_qnty=$prev_close_item_ref_data[$prod_id][1][$store_id]["CLOSING_ISSUE_RTN"];
						$transfer_in_qnty=$prev_close_item_ref_data[$prod_id][1][$store_id]["CLOSING_TRANSFER_IN"];
						$transfer_out_qnty=$prev_close_item_ref_data[$prod_id][1][$store_id]["CLOSING_TRANSFER_OUT"];
						$insert_year_close_item_ref="update year_close_item_ref set ";
						foreach($lib_month_colum_arr as $key=>$value)
						{
							//###############   This Check carry previous month value #####################///
							if($store_data[$value]["QNT"]) 
							{
								$store_qnty=bcadd($store_qnty,$store_data[$value]["QNT"],15);
							}
							if($store_data[$value]["AMT"]) 
							{
								$store_amt=bcadd($store_amt,$store_data[$value]["AMT"],15);
							}
							if($store_data[$value]["RECEIVE"]) 
							{
								$receive_qnty=bcadd($receive_qnty,$store_data[$value]["RECEIVE"],15);
							}
							if($store_data[$value]["ISSUE"]) 
							{
								$issue_qnty=bcadd($issue_qnty,$store_data[$value]["ISSUE"],15);
							}
							if($store_data[$value]["RECEIVE_RTN"]) 
							{
								$receive_rtn_qnty=bcadd($receive_rtn_qnty,$store_data[$value]["RECEIVE_RTN"],15);
							}
							if($store_data[$value]["ISSUE_RTN"]) 
							{
								$issue_rtn_qnty=bcadd($issue_rtn_qnty,$store_data[$value]["ISSUE_RTN"],15);
							}
							if($store_data[$value]["TRANSFER_IN"]) 
							{
								$transfer_in_qnty=bcadd($transfer_in_qnty,$store_data[$value]["TRANSFER_IN"],15);
							}
							if($store_data[$value]["TRANSFER_OUT"]) 
							{
								$transfer_out_qnty=bcadd($transfer_out_qnty,$store_data[$value]["TRANSFER_OUT"],15);
							}
							if($store_amt!=0 && $store_qnty!=0) $store_rate=number_format(($store_amt/$store_qnty),15,".",""); else $store_rate=0;
							$insert_year_close_item_ref .="$key='".$store_qnty."', $key"."_rate='".$store_rate."', $key"."_receive='".$receive_qnty."', $key"."_issue='".$issue_qnty."', $key"."_receive_rtn='".$receive_rtn_qnty."', $key"."_issue_rtn='".$issue_rtn_qnty."', $key"."_transfer_in='".$transfer_in_qnty."', $key"."_transfer_out='".$transfer_out_qnty."',";
						}
						if($store_amt!=0 && $store_qnty!=0) $store_rate=number_format(($store_amt/$store_qnty),15,".",""); else $store_rate=0;
						$insert_year_close_item_ref .="closing='".$store_qnty."', last_rate='".$store_rate."', closing_receive='".$receive_qnty."', closing_issue='".$issue_qnty."', closing_receive_rtn='".$receive_rtn_qnty."', closing_issue_rtn='".$issue_rtn_qnty."', closing_transfer_in='".$transfer_in_qnty."', closing_transfer_out='".$transfer_out_qnty."'  where id='".$prev_close_item_ref_data[$prod_id][1][$store_id]["ID"]."'";
						
						$update_year_item_ref_rid=execute_query($insert_year_close_item_ref);
						if($update_year_item_ref_rid) $update_year_item_ref_rid=1;
						else
						{
							$update_year_item_ref_rid=0;
							echo $insert_year_close_item_ref;oci_rollback($con);die;
						}
					}
				}
				//echo $insert_year_close_item_ref;oci_rollback($con);die;
			}
			
			if(count($data_pord_store_floor[$prod_id])>0)
			{
				foreach($data_pord_store_floor[$prod_id] as $floor_id=>$floor_data)
				{
					if($floor_id && trim(str_replace("'","",$floor_id)) !="")
					{
						$values_item_ref="";$insert_year_close_item_ref="";
						$floor_qnty=$floor_amt=$floor_rate=$receive_qnty=$issue_qnty=$receive_rtn_qnty=$issue_rtn_qnty=$transfer_in_qnty=$transfer_out_qnty=0;
						if($prev_close_item_ref_data[$prod_id][2][$floor_id]["ID"]=="")
						{
							//$year_item_ref_id = return_next_id_by_sequence("YEAR_CLOSE_ITEM_REF_PK_SEQ", "year_close_item_ref", $con);
							$values_item_ref="(".$year_item_ref_id.",".$prod_id.",".$floor_id.",2,".$cbo_company_id.",".$cbo_year;
							foreach($lib_month_colum_arr as $key=>$value)
							{
								if($floor_data[$value]["QNT"]) 
								{
									$floor_qnty =bcadd($floor_qnty,$floor_data[$value]["QNT"],15);
								}
								if($floor_data[$value]["AMT"]) 
								{
									$floor_amt =bcadd($floor_amt,$floor_data[$value]["AMT"],15);
								}
								if($floor_data[$value]["RECEIVE"]) 
								{
									$receive_qnty=bcadd($receive_qnty,$floor_data[$value]["RECEIVE"],15);
								}
								if($floor_data[$value]["ISSUE"]) 
								{
									$issue_qnty=bcadd($issue_qnty,$floor_data[$value]["ISSUE"],15);
								}
								if($floor_data[$value]["RECEIVE_RTN"]) 
								{
									$receive_rtn_qnty=bcadd($receive_rtn_qnty,$floor_data[$value]["RECEIVE_RTN"],15);
								}
								if($floor_data[$value]["ISSUE_RTN"]) 
								{
									$issue_rtn_qnty=bcadd($issue_rtn_qnty,$floor_data[$value]["ISSUE_RTN"],15);
								}
								if($floor_data[$value]["TRANSFER_IN"]) 
								{
									$transfer_in_qnty=bcadd($transfer_in_qnty,$floor_data[$value]["TRANSFER_IN"],15);
								}
								if($floor_data[$value]["TRANSFER_OUT"]) 
								{
									$transfer_out_qnty=bcadd($transfer_out_qnty,$floor_data[$value]["TRANSFER_OUT"],15);
								}
								
								if($floor_amt!=0 && $floor_qnty!=0) $floor_rate=number_format(($floor_amt/$floor_qnty),15,".",""); else $floor_rate=0;
								$values_item_ref.=",'".$floor_qnty."','".$floor_rate."','".$receive_qnty."','".$issue_qnty."','".$receive_rtn_qnty."','".$issue_rtn_qnty."','".$transfer_in_qnty."','".$transfer_out_qnty."'";
							}
							if($floor_amt!=0 && $floor_qnty!=0) $floor_rate=number_format(($floor_amt/$floor_qnty),15,".",""); else $floor_rate=0;
							$values_item_ref.=",'".$floor_qnty."','".$floor_rate."','".$receive_qnty."','".$issue_qnty."','".$receive_rtn_qnty."','".$issue_rtn_qnty."','".$transfer_in_qnty."','".$transfer_out_qnty."')";
							//echo $values_item;oci_rollback($con);die;
							
							$insert_year_close_item_ref="insert into year_close_item_ref ($fields_item_ref) values $values_item_ref";
							$year_item_ref_id++;
							
							$update_year_item_ref_rid=execute_query($insert_year_close_item_ref);
							if($update_year_item_ref_rid) $update_year_item_ref_rid=1;
							else
							{
								$update_year_item_ref_rid=0;
								echo $insert_year_close_item_ref;oci_rollback($con);die;
							}
						}
						else
						{
							$floor_qnty=$prev_close_item_ref_data[$prod_id][2][$floor_id]["CLOSING"];
							$floor_amt=$prev_close_item_ref_data[$prod_id][2][$floor_id]["CLOSING_AMT"];
							$receive_qnty=$prev_close_item_ref_data[$prod_id][2][$floor_id]["CLOSING_RECEIVE"];
							$issue_qnty=$prev_close_item_ref_data[$prod_id][2][$floor_id]["CLOSING_ISSUE"];
							$receive_rtn_qnty=$prev_close_item_ref_data[$prod_id][2][$floor_id]["CLOSING_RECEIVE_RTN"];
							$issue_rtn_qnty=$prev_close_item_ref_data[$prod_id][2][$floor_id]["CLOSING_ISSUE_RTN"];
							$transfer_in_qnty=$prev_close_item_ref_data[$prod_id][2][$floor_id]["CLOSING_TRANSFER_IN"];
							$transfer_out_qnty=$prev_close_item_ref_data[$prod_id][2][$floor_id]["CLOSING_TRANSFER_OUT"];
							$insert_year_close_item_ref="update year_close_item_ref set ";
							foreach($lib_month_colum_arr as $key=>$value)
							{
								//###############   This Check carry previous month value #####################///
								if($floor_data[$value]["QNT"]) 
								{
									$floor_qnty=bcadd($floor_qnty,$floor_data[$value]["QNT"],15);
								}
								if($floor_data[$value]["AMT"]) 
								{
									$floor_amt=bcadd($floor_amt,$floor_data[$value]["AMT"],15);
								}
								if($floor_data[$value]["RECEIVE"]) 
								{
									$receive_qnty=bcadd($receive_qnty,$floor_data[$value]["RECEIVE"],15);
								}
								if($floor_data[$value]["ISSUE"]) 
								{
									$issue_qnty=bcadd($issue_qnty,$floor_data[$value]["ISSUE"],15);
								}
								if($floor_data[$value]["RECEIVE_RTN"]) 
								{
									$receive_rtn_qnty=bcadd($receive_rtn_qnty,$floor_data[$value]["RECEIVE_RTN"],15);
								}
								if($floor_data[$value]["ISSUE_RTN"]) 
								{
									$issue_rtn_qnty=bcadd($issue_rtn_qnty,$floor_data[$value]["ISSUE_RTN"],15);
								}
								if($floor_data[$value]["TRANSFER_IN"]) 
								{
									$transfer_in_qnty=bcadd($transfer_in_qnty,$floor_data[$value]["TRANSFER_IN"],15);
								}
								if($floor_data[$value]["TRANSFER_OUT"]) 
								{
									$transfer_out_qnty=bcadd($transfer_out_qnty,$floor_data[$value]["TRANSFER_OUT"],15);
								}
								
								if($floor_amt!=0 && $floor_qnty!=0) $floor_rate=number_format(($floor_amt/$floor_qnty),15,".",""); else $floor_rate=0;
								$insert_year_close_item_ref .="$key='".$floor_qnty."', $key"."_rate='".$floor_rate."', $key"."_receive='".$receive_qnty."', $key"."_issue='".$issue_qnty."', $key"."_receive_rtn='".$receive_rtn_qnty."', $key"."_issue_rtn='".$issue_rtn_qnty."', $key"."_transfer_in='".$transfer_in_qnty."', $key"."_transfer_out='".$transfer_out_qnty."',";
							}
							if($floor_amt!=0 && $floor_qnty!=0) $floor_rate=number_format(($floor_amt/$floor_qnty),15,".",""); else $floor_rate=0;
							$insert_year_close_item_ref .="closing='".$floor_qnty."', last_rate='".$floor_rate."', closing_receive='".$receive_qnty."', closing_issue='".$issue_qnty."', closing_receive_rtn='".$receive_rtn_qnty."', closing_issue_rtn='".$issue_rtn_qnty."', closing_transfer_in='".$transfer_in_qnty."', closing_transfer_out='".$transfer_out_qnty."' where id='".$prev_close_item_ref_data[$prod_id][2][$floor_id]["ID"]."'";
							
							$update_year_item_ref_rid=execute_query($insert_year_close_item_ref);
							if($update_year_item_ref_rid) $update_year_item_ref_rid=1;
							else
							{
								$update_year_item_ref_rid=0;
								echo $insert_year_close_item_ref;oci_rollback($con);die;
							}
						}
					}
					
					//echo $insert_year_close_item_ref;oci_rollback($con);die;
				}
			}
			
			if(count($data_pord_store_floor_room[$prod_id])>0)
			{
				foreach($data_pord_store_floor_room[$prod_id] as $room_id=>$room_data)
				{
					if($room_id && trim(str_replace("'","",$room_id)) !="")
					{
						$values_item_ref="";$insert_year_close_item_ref="";
						$room_qnty=$room_amt=$room_rate=$receive_qnty=$issue_qnty=$receive_rtn_qnty=$issue_rtn_qnty=$transfer_in_qnty=$transfer_out_qnty=0;
						if($prev_close_item_ref_data[$prod_id][3][$room_id]["ID"]=="")
						{
							//$year_item_ref_id = return_next_id_by_sequence("YEAR_CLOSE_ITEM_REF_PK_SEQ", "year_close_item_ref", $con);
							$values_item_ref="(".$year_item_ref_id.",".$prod_id.",".$room_id.",3,".$cbo_company_id.",".$cbo_year;
							foreach($lib_month_colum_arr as $key=>$value)
							{
								if($room_data[$value]["QNT"]) 
								{
									$room_qnty =bcadd($room_qnty,$room_data[$value]["QNT"],15);
								}
								if($room_data[$value]["AMT"]) 
								{
									$room_amt =bcadd($room_amt,$room_data[$value]["AMT"],15);
								}
								if($room_data[$value]["RECEIVE"]) 
								{
									$receive_qnty=bcadd($receive_qnty,$room_data[$value]["RECEIVE"],15);
								}
								if($room_data[$value]["ISSUE"]) 
								{
									$issue_qnty=bcadd($issue_qnty,$room_data[$value]["ISSUE"],15);
								}
								if($room_data[$value]["RECEIVE_RTN"]) 
								{
									$receive_rtn_qnty=bcadd($receive_rtn_qnty,$room_data[$value]["RECEIVE_RTN"],15);
								}
								if($room_data[$value]["ISSUE_RTN"]) 
								{
									$issue_rtn_qnty=bcadd($issue_rtn_qnty,$room_data[$value]["ISSUE_RTN"],15);
								}
								if($room_data[$value]["TRANSFER_IN"]) 
								{
									$transfer_in_qnty=bcadd($transfer_in_qnty,$room_data[$value]["TRANSFER_IN"],15);
								}
								if($room_data[$value]["TRANSFER_OUT"]) 
								{
									$transfer_out_qnty=bcadd($transfer_out_qnty,$room_data[$value]["TRANSFER_OUT"],15);
								}
								
								if($room_amt!=0 && $room_qnty!=0) $room_rate=number_format(($room_amt/$room_qnty),15,".",""); else $room_rate=0;
								$values_item_ref.=",'".$room_qnty."','".$room_rate."','".$receive_qnty."','".$issue_qnty."','".$receive_rtn_qnty."','".$issue_rtn_qnty."','".$transfer_in_qnty."','".$transfer_out_qnty."'";
							}
							if($room_amt!=0 && $room_qnty!=0) $room_rate=number_format(($room_amt/$room_qnty),15,".",""); else $room_rate=0;
							$values_item_ref.=",'".$room_qnty."','".$room_rate."','".$receive_qnty."','".$issue_qnty."','".$receive_rtn_qnty."','".$issue_rtn_qnty."','".$transfer_in_qnty."','".$transfer_out_qnty."')";
							//echo $values_item;oci_rollback($con);die;
							
							$insert_year_close_item_ref="insert into year_close_item_ref ($fields_item_ref) values $values_item_ref";
							$year_item_ref_id++;
							
							$update_year_item_ref_rid=execute_query($insert_year_close_item_ref);
							if($update_year_item_ref_rid) $update_year_item_ref_rid=1;
							else
							{
								$update_year_item_ref_rid=0;
								echo $insert_year_close_item_ref;oci_rollback($con);die;
							}
						}
						else
						{
							$room_qnty=$prev_close_item_ref_data[$prod_id][3][$room_id]["CLOSING"];
							$room_amt=$prev_close_item_ref_data[$prod_id][3][$room_id]["CLOSING_AMT"];
							$receive_qnty=$prev_close_item_ref_data[$prod_id][3][$room_id]["CLOSING_RECEIVE"];
							$issue_qnty=$prev_close_item_ref_data[$prod_id][3][$room_id]["CLOSING_ISSUE"];
							$receive_rtn_qnty=$prev_close_item_ref_data[$prod_id][3][$room_id]["CLOSING_RECEIVE_RTN"];
							$issue_rtn_qnty=$prev_close_item_ref_data[$prod_id][3][$room_id]["CLOSING_ISSUE_RTN"];
							$transfer_in_qnty=$prev_close_item_ref_data[$prod_id][3][$room_id]["CLOSING_TRANSFER_IN"];
							$transfer_out_qnty=$prev_close_item_ref_data[$prod_id][3][$room_id]["CLOSING_TRANSFER_OUT"];
							$insert_year_close_item_ref="update year_close_item_ref set ";
							foreach($lib_month_colum_arr as $key=>$value)
							{
								//###############   This Check carry previous month value #####################///
								if($room_data[$value]["QNT"]) 
								{
									$room_qnty =bcadd($room_qnty,$room_data[$value]["QNT"],15);
								}
								if($room_data[$value]["AMT"]) 
								{
									$room_amt =bcadd($room_amt,$room_data[$value]["AMT"],15);
								}
								if($room_data[$value]["RECEIVE"]) 
								{
									$receive_qnty=bcadd($receive_qnty,$room_data[$value]["RECEIVE"],15);
								}
								if($room_data[$value]["ISSUE"]) 
								{
									$issue_qnty=bcadd($issue_qnty,$room_data[$value]["ISSUE"],15);
								}
								if($room_data[$value]["RECEIVE_RTN"]) 
								{
									$receive_rtn_qnty=bcadd($receive_rtn_qnty,$room_data[$value]["RECEIVE_RTN"],15);
								}
								if($room_data[$value]["ISSUE_RTN"]) 
								{
									$issue_rtn_qnty=bcadd($issue_rtn_qnty,$room_data[$value]["ISSUE_RTN"],15);
								}
								if($room_data[$value]["TRANSFER_IN"]) 
								{
									$transfer_in_qnty=bcadd($transfer_in_qnty,$room_data[$value]["TRANSFER_IN"],15);
								}
								if($room_data[$value]["TRANSFER_OUT"]) 
								{
									$transfer_out_qnty=bcadd($transfer_out_qnty,$room_data[$value]["TRANSFER_OUT"],15);
								}
								
								if($room_amt!=0 && $room_qnty!=0) $room_rate=number_format(($room_amt/$room_qnty),15,".",""); else $room_rate=0;
								$insert_year_close_item_ref .="$key='".$room_qnty."', $key"."_rate='".$room_rate."', $key"."_receive='".$receive_qnty."', $key"."_issue='".$issue_qnty."', $key"."_receive_rtn='".$receive_rtn_qnty."', $key"."_issue_rtn='".$issue_rtn_qnty."', $key"."_transfer_in='".$transfer_in_qnty."', $key"."_transfer_out='".$transfer_out_qnty."',";
							}
							if($room_amt!=0 && $room_qnty!=0) $room_rate=number_format(($room_amt/$room_qnty),15,".",""); else $room_rate=0;
							$insert_year_close_item_ref .="closing='".$room_qnty."', last_rate='".$room_rate."', closing_receive='".$receive_qnty."', closing_issue='".$issue_qnty."', closing_receive_rtn='".$receive_rtn_qnty."', closing_issue_rtn='".$issue_rtn_qnty."', closing_transfer_in='".$transfer_in_qnty."', closing_transfer_out='".$transfer_out_qnty."' where id='".$prev_close_item_ref_data[$prod_id][3][$room_id]["ID"]."'";
							
							$update_year_item_ref_rid=execute_query($insert_year_close_item_ref);
							if($update_year_item_ref_rid) $update_year_item_ref_rid=1;
							else
							{
								$update_year_item_ref_rid=0;
								echo $insert_year_close_item_ref;oci_rollback($con);die;
							}
						}
					}
					
					//echo $insert_year_close_item_ref;oci_rollback($con);die;
				}
			}
			
			if(count($data_pord_store_floor_room_rac[$prod_id])>0)
			{
				foreach($data_pord_store_floor_room_rac[$prod_id] as $rack_id=>$rack_data)
				{
					if($rack_id>0 && trim(str_replace("'","",$rack_id)) !="")
					{
						$values_item_ref="";$insert_year_close_item_ref="";
						$rack_qnty=$rack_amt=$rack_rate=$receive_qnty=$issue_qnty=$receive_rtn_qnty=$issue_rtn_qnty=$transfer_in_qnty=$transfer_out_qnty=0;
						if($prev_close_item_ref_data[$prod_id][4][$rack_id]["ID"]=="")
						{
							//$year_item_ref_id = return_next_id_by_sequence("YEAR_CLOSE_ITEM_REF_PK_SEQ", "year_close_item_ref", $con);
							$values_item_ref="(".$year_item_ref_id.",".$prod_id.",".$rack_id.",4,".$cbo_company_id.",".$cbo_year;
							foreach($lib_month_colum_arr as $key=>$value)
							{
								if($rack_data[$value]["QNT"]) 
								{
									$rack_qnty =bcadd($rack_qnty,$rack_data[$value]["QNT"],15);
								}
								if($rack_data[$value]["AMT"]) 
								{
									$rack_amt =bcadd($rack_amt,$rack_data[$value]["AMT"],15);
								}
								if($rack_data[$value]["RECEIVE"]) 
								{
									$receive_qnty=bcadd($receive_qnty,$rack_data[$value]["RECEIVE"],15);
								}
								if($rack_data[$value]["ISSUE"]) 
								{
									$issue_qnty=bcadd($issue_qnty,$rack_data[$value]["ISSUE"],15);
								}
								if($rack_data[$value]["RECEIVE_RTN"]) 
								{
									$receive_rtn_qnty=bcadd($receive_rtn_qnty,$rack_data[$value]["RECEIVE_RTN"],15);
								}
								if($rack_data[$value]["ISSUE_RTN"]) 
								{
									$issue_rtn_qnty=bcadd($issue_rtn_qnty,$rack_data[$value]["ISSUE_RTN"],15);
								}
								if($rack_data[$value]["TRANSFER_IN"]) 
								{
									$transfer_in_qnty=bcadd($transfer_in_qnty,$rack_data[$value]["TRANSFER_IN"],15);
								}
								if($rack_data[$value]["TRANSFER_OUT"]) 
								{
									$transfer_out_qnty=bcadd($transfer_out_qnty,$rack_data[$value]["TRANSFER_OUT"],15);
								}
								
								if($rack_amt!=0 && $rack_qnty!=0) $rack_rate=number_format(($rack_amt/$rack_qnty),15,".",""); else $rack_rate=0;
								$values_item_ref.=",'".$rack_qnty."','".$rack_rate."','".$receive_qnty."','".$issue_qnty."','".$receive_rtn_qnty."','".$issue_rtn_qnty."','".$transfer_in_qnty."','".$transfer_out_qnty."'";
							}
							if($rack_amt !=0 && $rack_qnty!=0) $rack_rate=number_format(($rack_amt/$rack_qnty),15,".",""); else $rack_rate=0;
							$values_item_ref.=",'".$rack_qnty."','".$rack_rate."','".$receive_qnty."','".$issue_qnty."','".$receive_rtn_qnty."','".$issue_rtn_qnty."','".$transfer_in_qnty."','".$transfer_out_qnty."')";
							$insert_year_close_item_ref="insert into year_close_item_ref ($fields_item_ref) values $values_item_ref";
							$year_item_ref_id++;
							
							$update_year_item_ref_rid=execute_query($insert_year_close_item_ref);
							if($update_year_item_ref_rid) $update_year_item_ref_rid=1;
							else
							{
								$update_year_item_ref_rid=0;
								echo $insert_year_close_item_ref;oci_rollback($con);die;
							}
						}
						else
						{
							$rack_qnty=$prev_close_item_ref_data[$prod_id][4][$rack_id]["CLOSING"];
							$rack_amt=$prev_close_item_ref_data[$prod_id][4][$rack_id]["CLOSING_AMT"];
							$receive_qnty=$prev_close_item_ref_data[$prod_id][4][$rack_id]["CLOSING_RECEIVE"];
							$issue_qnty=$prev_close_item_ref_data[$prod_id][4][$rack_id]["CLOSING_ISSUE"];
							$receive_rtn_qnty=$prev_close_item_ref_data[$prod_id][4][$rack_id]["CLOSING_RECEIVE_RTN"];
							$issue_rtn_qnty=$prev_close_item_ref_data[$prod_id][4][$rack_id]["CLOSING_ISSUE_RTN"];
							$transfer_in_qnty=$prev_close_item_ref_data[$prod_id][4][$rack_id]["CLOSING_TRANSFER_IN"];
							$transfer_out_qnty=$prev_close_item_ref_data[$prod_id][4][$rack_id]["CLOSING_TRANSFER_OUT"];
							$insert_year_close_item_ref="update year_close_item_ref set ";
							foreach($lib_month_colum_arr as $key=>$value)
							{
								//###############   This Check carry previous month value #####################///
								if($rack_data[$value]["QNT"]) 
								{
									$rack_qnty =bcadd($rack_qnty,$rack_data[$value]["QNT"],15);
								}
								if($rack_data[$value]["AMT"]) 
								{
									$rack_amt =bcadd($rack_amt,$rack_data[$value]["AMT"],15);
								}
								if($rack_data[$value]["RECEIVE"]) 
								{
									$receive_qnty=bcadd($receive_qnty,$rack_data[$value]["RECEIVE"],15);
								}
								if($rack_data[$value]["ISSUE"]) 
								{
									$issue_qnty=bcadd($issue_qnty,$rack_data[$value]["ISSUE"],15);
								}
								if($rack_data[$value]["RECEIVE_RTN"]) 
								{
									$receive_rtn_qnty=bcadd($receive_rtn_qnty,$rack_data[$value]["RECEIVE_RTN"],15);
								}
								if($rack_data[$value]["ISSUE_RTN"]) 
								{
									$issue_rtn_qnty=bcadd($issue_rtn_qnty,$rack_data[$value]["ISSUE_RTN"],15);
								}
								if($rack_data[$value]["TRANSFER_IN"]) 
								{
									$transfer_in_qnty=bcadd($transfer_in_qnty,$rack_data[$value]["TRANSFER_IN"],15);
								}
								if($rack_data[$value]["TRANSFER_OUT"]) 
								{
									$transfer_out_qnty=bcadd($transfer_out_qnty,$rack_data[$value]["TRANSFER_OUT"],15);
								}
								
								if($rack_amt!=0 && $rack_qnty!=0) $rack_rate=number_format(($rack_amt/$rack_qnty),15,".",""); else $rack_rate=0;
								$insert_year_close_item_ref .="$key='".$rack_qnty."', $key"."_rate='".$rack_rate."', $key"."_receive='".$receive_qnty."', $key"."_issue='".$issue_qnty."', $key"."_receive_rtn='".$receive_rtn_qnty."', $key"."_issue_rtn='".$issue_rtn_qnty."', $key"."_transfer_in='".$transfer_in_qnty."', $key"."_transfer_out='".$transfer_out_qnty."',";
							}
							if($rack_amt!=0 && $rack_qnty!=0) $rack_rate=number_format(($rack_amt/$rack_qnty),15,".",""); else $rack_rate=0;
							$insert_year_close_item_ref .="closing='".$rack_qnty."', last_rate='".$rack_rate."', closing_receive='".$receive_qnty."', closing_issue='".$issue_qnty."', closing_receive_rtn='".$receive_rtn_qnty."', closing_issue_rtn='".$issue_rtn_qnty."', closing_transfer_in='".$transfer_in_qnty."', closing_transfer_out='".$transfer_out_qnty."' where id='".$prev_close_item_ref_data[$prod_id][4][$rack_id]["ID"]."'";
							
							$update_year_item_ref_rid=execute_query($insert_year_close_item_ref);
							if($update_year_item_ref_rid) $update_year_item_ref_rid=1;
							else
							{
								$update_year_item_ref_rid=0;
								echo $insert_year_close_item_ref;oci_rollback($con);die;
							}
						}
					}
					
					//echo $insert_year_close_item_ref;oci_rollback($con);die;
				}
			}
			
			if(count($data_pord_store_floor_room_rac_self[$prod_id])>0)
			{
				foreach($data_pord_store_floor_room_rac_self[$prod_id] as $shelf_id=>$shelf_data)
				{
					if($shelf_id && trim(str_replace("'","",$shelf_id)) !="")
					{
						$values_item_ref="";$insert_year_close_item_ref="";
						$shelf_qnty=$shelf_amt=$shelf_rate=$receive_qnty=$issue_qnty=$receive_rtn_qnty=$issue_rtn_qnty=$transfer_in_qnty=$transfer_out_qnty=0;
						if($prev_close_item_ref_data[$prod_id][5][$shelf_id]["ID"]=="")
						{
							//$year_item_ref_id = return_next_id_by_sequence("YEAR_CLOSE_ITEM_REF_PK_SEQ", "year_close_item_ref", $con);
							$values_item_ref="(".$year_item_ref_id.",".$prod_id.",".$shelf_id.",5,".$cbo_company_id.",".$cbo_year;
							foreach($lib_month_colum_arr as $key=>$value)
							{
								if($shelf_data[$value]["QNT"]) 
								{
									$shelf_qnty =bcadd($shelf_qnty,$shelf_data[$value]["QNT"],15);
								}
								if($shelf_data[$value]["AMT"]) 
								{
									$shelf_amt =bcadd($shelf_amt,$shelf_data[$value]["AMT"],15);
								}
								if($shelf_data[$value]["RECEIVE"]) 
								{
									$receive_qnty=bcadd($receive_qnty,$shelf_data[$value]["RECEIVE"],15);
								}
								if($shelf_data[$value]["ISSUE"]) 
								{
									$issue_qnty=bcadd($issue_qnty,$shelf_data[$value]["ISSUE"],15);
								}
								if($shelf_data[$value]["RECEIVE_RTN"]) 
								{
									$receive_rtn_qnty=bcadd($receive_rtn_qnty,$shelf_data[$value]["RECEIVE_RTN"],15);
								}
								if($shelf_data[$value]["ISSUE_RTN"]) 
								{
									$issue_rtn_qnty=bcadd($issue_rtn_qnty,$shelf_data[$value]["ISSUE_RTN"],15);
								}
								if($shelf_data[$value]["TRANSFER_IN"]) 
								{
									$transfer_in_qnty=bcadd($transfer_in_qnty,$shelf_data[$value]["TRANSFER_IN"],15);
								}
								if($shelf_data[$value]["TRANSFER_OUT"]) 
								{
									$transfer_out_qnty=bcadd($transfer_out_qnty,$shelf_data[$value]["TRANSFER_OUT"],15);
								}
								
								if($shelf_amt!=0 && $shelf_qnty!=0) $shelf_rate=number_format(($shelf_amt/$shelf_qnty),15,".",""); else $shelf_rate=0;
								$values_item_ref.=",'".$shelf_qnty."','".$shelf_rate."','".$receive_qnty."','".$issue_qnty."','".$receive_rtn_qnty."','".$issue_rtn_qnty."','".$transfer_in_qnty."','".$transfer_out_qnty."'";
							}
							if($shelf_amt!=0 && $shelf_qnty!=0) $shelf_rate=number_format(($shelf_amt/$shelf_qnty),15,".",""); else $shelf_rate=0;
							$values_item_ref.=",'".$shelf_qnty."','".$shelf_rate."','".$receive_qnty."','".$issue_qnty."','".$receive_rtn_qnty."','".$issue_rtn_qnty."','".$transfer_in_qnty."','".$transfer_out_qnty."')";
							$insert_year_close_item_ref="insert into year_close_item_ref ($fields_item_ref) values $values_item_ref";
							$year_item_ref_id++;
							
							$update_year_item_ref_rid=execute_query($insert_year_close_item_ref);
							if($update_year_item_ref_rid) $update_year_item_ref_rid=1;
							else
							{
								$update_year_item_ref_rid=0;
								echo $insert_year_close_item_ref;oci_rollback($con);die;
							}
						}
						else
						{
							$shelf_qnty=$prev_close_item_ref_data[$prod_id][5][$shelf_id]["CLOSING"];
							$shelf_amt=$prev_close_item_ref_data[$prod_id][5][$shelf_id]["CLOSING_AMT"];
							$receive_qnty=$prev_close_item_ref_data[$prod_id][5][$shelf_id]["CLOSING_RECEIVE"];
							$issue_qnty=$prev_close_item_ref_data[$prod_id][5][$shelf_id]["CLOSING_ISSUE"];
							$receive_rtn_qnty=$prev_close_item_ref_data[$prod_id][5][$shelf_id]["CLOSING_RECEIVE_RTN"];
							$issue_rtn_qnty=$prev_close_item_ref_data[$prod_id][5][$shelf_id]["CLOSING_ISSUE_RTN"];
							$transfer_in_qnty=$prev_close_item_ref_data[$prod_id][5][$shelf_id]["CLOSING_TRANSFER_IN"];
							$transfer_out_qnty=$prev_close_item_ref_data[$prod_id][5][$shelf_id]["CLOSING_TRANSFER_OUT"];
							$insert_year_close_item_ref="update year_close_item_ref set ";
							foreach($lib_month_colum_arr as $key=>$value)
							{
								//###############   This Check carry previous month value #####################///
								if($shelf_data[$value]["QNT"]) 
								{
									$shelf_qnty =bcadd($shelf_qnty,$shelf_data[$value]["QNT"],15);
								}
								if($shelf_data[$value]["AMT"]) 
								{
									$shelf_amt =bcadd($shelf_amt,$shelf_data[$value]["AMT"],15);
								}
								if($shelf_data[$value]["RECEIVE"]) 
								{
									$receive_qnty=bcadd($receive_qnty,$shelf_data[$value]["RECEIVE"],15);
								}
								if($shelf_data[$value]["ISSUE"]) 
								{
									$issue_qnty=bcadd($issue_qnty,$shelf_data[$value]["ISSUE"],15);
								}
								if($shelf_data[$value]["RECEIVE_RTN"]) 
								{
									$receive_rtn_qnty=bcadd($receive_rtn_qnty,$shelf_data[$value]["RECEIVE_RTN"],15);
								}
								if($shelf_data[$value]["ISSUE_RTN"]) 
								{
									$issue_rtn_qnty=bcadd($issue_rtn_qnty,$shelf_data[$value]["ISSUE_RTN"],15);
								}
								if($shelf_data[$value]["TRANSFER_IN"]) 
								{
									$transfer_in_qnty=bcadd($transfer_in_qnty,$shelf_data[$value]["TRANSFER_IN"],15);
								}
								if($shelf_data[$value]["TRANSFER_OUT"]) 
								{
									$transfer_out_qnty=bcadd($transfer_out_qnty,$shelf_data[$value]["TRANSFER_OUT"],15);
								}
								
								if($shelf_amt!=0 && $shelf_qnty!=0) $shelf_rate=number_format(($shelf_amt/$shelf_qnty),15,".",""); else $shelf_rate=0;
								$insert_year_close_item_ref .="$key='".$shelf_qnty."', $key"."_rate='".$shelf_rate."', $key"."_receive='".$receive_qnty."', $key"."_issue='".$issue_qnty."', $key"."_receive_rtn='".$receive_rtn_qnty."', $key"."_issue_rtn='".$issue_rtn_qnty."', $key"."_transfer_in='".$transfer_in_qnty."', $key"."_transfer_out='".$transfer_out_qnty."',";
							}
							if($shelf_amt!=0 && $shelf_qnty!=0) $shelf_rate=number_format(($shelf_amt/$shelf_qnty),15,".",""); else $shelf_rate=0;
							$insert_year_close_item_ref .="closing='".$shelf_qnty."', last_rate='".$shelf_rate."', closing_receive='".$receive_qnty."', closing_issue='".$issue_qnty."', closing_receive_rtn='".$receive_rtn_qnty."', closing_issue_rtn='".$issue_rtn_qnty."', closing_transfer_in='".$transfer_in_qnty."', closing_transfer_out='".$transfer_out_qnty."' where id='".$prev_close_item_ref_data[$prod_id][5][$shelf_id]["ID"]."'";
							
							$update_year_item_ref_rid=execute_query($insert_year_close_item_ref);
							if($update_year_item_ref_rid) $update_year_item_ref_rid=1;
							else
							{
								$update_year_item_ref_rid=0;
								echo $insert_year_close_item_ref;oci_rollback($con);die;
							}
						}
					}
					
					//echo $insert_year_close_item_ref;oci_rollback($con);die;
				}
			}
			
			if(count($data_pord_store_floor_room_rac_self_bin[$prod_id])>0)
			{
				foreach($data_pord_store_floor_room_rac_self_bin[$prod_id] as $bin_id=>$bin_data)
				{
					if($bin_id && trim(str_replace("'","",$bin_id)) !="")
					{
						$values_item_ref="";$insert_year_close_item_ref="";
						$bin_qnty=$bin_amt=$bin_rate=$receive_qnty=$issue_qnty=$receive_rtn_qnty=$issue_rtn_qnty=$transfer_in_qnty=$transfer_out_qnty=0;
						if($prev_close_item_ref_data[$prod_id][6][$bin_id]["ID"]=="")
						{
							//$year_item_ref_id = return_next_id_by_sequence("YEAR_CLOSE_ITEM_REF_PK_SEQ", "year_close_item_ref", $con);
							$values_item_ref="(".$year_item_ref_id.",".$prod_id.",".$bin_id.",6,".$cbo_company_id.",".$cbo_year;
							foreach($lib_month_colum_arr as $key=>$value)
							{
								if($bin_data[$value]["QNT"]) 
								{
									$bin_qnty =bcadd($bin_qnty,$bin_data[$value]["QNT"],15);
								}
								if($bin_data[$value]["AMT"]) 
								{
									$bin_amt =bcadd($bin_amt,$bin_data[$value]["AMT"],15);
								}
								if($bin_data[$value]["RECEIVE"]) 
								{
									$receive_qnty=bcadd($receive_qnty,$bin_data[$value]["RECEIVE"],15);
								}
								if($bin_data[$value]["ISSUE"]) 
								{
									$issue_qnty=bcadd($issue_qnty,$bin_data[$value]["ISSUE"],15);
								}
								if($bin_data[$value]["RECEIVE_RTN"]) 
								{
									$receive_rtn_qnty=bcadd($receive_rtn_qnty,$bin_data[$value]["RECEIVE_RTN"],15);
								}
								if($bin_data[$value]["ISSUE_RTN"]) 
								{
									$issue_rtn_qnty=bcadd($issue_rtn_qnty,$bin_data[$value]["ISSUE_RTN"],15);
								}
								if($bin_data[$value]["TRANSFER_IN"]) 
								{
									$transfer_in_qnty=bcadd($transfer_in_qnty,$bin_data[$value]["TRANSFER_IN"],15);
								}
								if($bin_data[$value]["TRANSFER_OUT"]) 
								{
									$transfer_out_qnty=bcadd($transfer_out_qnty,$bin_data[$value]["TRANSFER_OUT"],15);
								}
								
								if($bin_amt!=0 && $bin_qnty!=0) $bin_rate=number_format(($bin_amt / $bin_qnty),15,".",""); else $bin_rate=0;
								$values_item_ref.=",'".$bin_qnty."','".$bin_rate."','".$receive_qnty."','".$issue_qnty."','".$receive_rtn_qnty."','".$issue_rtn_qnty."','".$transfer_in_qnty."','".$transfer_out_qnty."'";
							}
							if($bin_amt!=0 && $bin_qnty!=0) $bin_rate=number_format(($bin_amt / $bin_qnty),15,".",""); else $bin_rate=0;
							$values_item_ref.=",'".$bin_qnty."','".$bin_rate."','".$receive_qnty."','".$issue_qnty."','".$receive_rtn_qnty."','".$issue_rtn_qnty."','".$transfer_in_qnty."','".$transfer_out_qnty."')";
							$insert_year_close_item_ref="insert into year_close_item_ref ($fields_item_ref) values $values_item_ref";
							$year_item_ref_id++;
							
							$update_year_item_ref_rid=execute_query($insert_year_close_item_ref);
							if($update_year_item_ref_rid) $update_year_item_ref_rid=1;
							else
							{
								$update_year_item_ref_rid=0;
								echo $insert_year_close_item_ref;oci_rollback($con);die;
							}
						}
						else
						{
							$bin_qnty=$prev_close_item_ref_data[$prod_id][6][$bin_id]["CLOSING"];
							$bin_amt=$prev_close_item_ref_data[$prod_id][6][$bin_id]["CLOSING_AMT"];
							$receive_qnty=$prev_close_item_ref_data[$prod_id][6][$bin_id]["CLOSING_RECEIVE"];
							$issue_qnty=$prev_close_item_ref_data[$prod_id][6][$bin_id]["CLOSING_ISSUE"];
							$receive_rtn_qnty=$prev_close_item_ref_data[$prod_id][6][$bin_id]["CLOSING_RECEIVE_RTN"];
							$issue_rtn_qnty=$prev_close_item_ref_data[$prod_id][6][$bin_id]["CLOSING_ISSUE_RTN"];
							$transfer_in_qnty=$prev_close_item_ref_data[$prod_id][6][$bin_id]["CLOSING_TRANSFER_IN"];
							$transfer_out_qnty=$prev_close_item_ref_data[$prod_id][6][$bin_id]["CLOSING_TRANSFER_OUT"];
							$insert_year_close_item_ref="update year_close_item_ref set ";
							foreach($lib_month_colum_arr as $key=>$value)
							{
								//###############   This Check carry previous month value #####################///
								if($bin_data[$value]["QNT"]) 
								{
									$bin_qnty =bcadd($bin_qnty,$bin_data[$value]["QNT"],15);
								}
								if($bin_data[$value]["AMT"]) 
								{
									$bin_amt =bcadd($bin_amt,$bin_data[$value]["AMT"],15);
								}
								if($bin_data[$value]["RECEIVE"]) 
								{
									$receive_qnty=bcadd($receive_qnty,$bin_data[$value]["RECEIVE"],15);
								}
								if($bin_data[$value]["ISSUE"]) 
								{
									$issue_qnty=bcadd($issue_qnty,$bin_data[$value]["ISSUE"],15);
								}
								if($bin_data[$value]["RECEIVE_RTN"]) 
								{
									$receive_rtn_qnty=bcadd($receive_rtn_qnty,$bin_data[$value]["RECEIVE_RTN"],15);
								}
								if($bin_data[$value]["ISSUE_RTN"]) 
								{
									$issue_rtn_qnty=bcadd($issue_rtn_qnty,$bin_data[$value]["ISSUE_RTN"],15);
								}
								if($bin_data[$value]["TRANSFER_IN"]) 
								{
									$transfer_in_qnty=bcadd($transfer_in_qnty,$bin_data[$value]["TRANSFER_IN"],15);
								}
								if($bin_data[$value]["TRANSFER_OUT"]) 
								{
									$transfer_out_qnty=bcadd($transfer_out_qnty,$bin_data[$value]["TRANSFER_OUT"],15);
								}
								
								if($bin_amt!=0 && $bin_qnty!=0) $bin_rate=number_format(($bin_amt / $bin_qnty),15,".",""); else $bin_rate=0;
								$insert_year_close_item_ref .="$key='".$bin_qnty."', $key"."_rate='".$bin_rate."', $key"."_receive='".$receive_qnty."', $key"."_issue='".$issue_qnty."', $key"."_receive_rtn='".$receive_rtn_qnty."', $key"."_issue_rtn='".$issue_rtn_qnty."', $key"."_transfer_in='".$transfer_in_qnty."', $key"."_transfer_out='".$transfer_out_qnty."',";
							}
							if($bin_amt!=0 && $bin_qnty!=0) $bin_rate=number_format(($bin_amt / $bin_qnty),15,".",""); else $bin_rate=0;
							$insert_year_close_item_ref .="closing='".$bin_qnty."', last_rate='".$bin_rate."', closing_receive='".$receive_qnty."', closing_issue='".$issue_qnty."', closing_receive_rtn='".$receive_rtn_qnty."', closing_issue_rtn='".$issue_rtn_qnty."', closing_transfer_in='".$transfer_in_qnty."', closing_transfer_out='".$transfer_out_qnty."' where id='".$prev_close_item_ref_data[$prod_id][6][$bin_id]["ID"]."'";
							
							$update_year_item_ref_rid=execute_query($insert_year_close_item_ref);
							if($update_year_item_ref_rid) $update_year_item_ref_rid=1;
							else
							{
								$update_year_item_ref_rid=0;
								echo $insert_year_close_item_ref;oci_rollback($con);die;
							}
						}
					}
					
					//echo $insert_year_close_item_ref;oci_rollback($con);die;
				}
			}
			
			if(count($data_pord_order[$prod_id])>0)
			{
				foreach($data_pord_order[$prod_id] as $order_id_ref=>$order_data)
				{
					$order_ref=explode("*",$order_id_ref);
					$order_id=$order_ref[0];
					$is_seles=$order_ref[1];
					if($order_id && trim(str_replace("'","",$order_id)) !="")
					{
						if($is_seles==1) $ref_type_seq=8; else $ref_type_seq=7;
						$values_item_ref="";$insert_year_close_item_ref="";
						$order_qnty=$order_amt=$order_rate=$receive_qnty=$issue_qnty=$receive_rtn_qnty=$issue_rtn_qnty=$transfer_in_qnty=$transfer_out_qnty=0;
						if($prev_close_item_ref_data[$prod_id][$ref_type_seq][$order_id]["ID"]=="")
						{
							//$year_item_ref_id = return_next_id_by_sequence("YEAR_CLOSE_ITEM_REF_PK_SEQ", "year_close_item_ref", $con);
							$values_item_ref="(".$year_item_ref_id.",".$prod_id.",".$order_id.",".$ref_type_seq.",".$cbo_company_id.",".$cbo_year;
							foreach($lib_month_colum_arr as $key=>$value)
							{
								if($order_data[$value]["QNT"]) 
								{
									$order_qnty =bcadd($order_qnty,$order_data[$value]["QNT"],15);
								}
								if($order_data[$value]["AMT"]) 
								{
									$order_amt =bcadd($order_amt,$order_data[$value]["AMT"],15);
								}
								if($order_data[$value]["RECEIVE"]) 
								{
									$receive_qnty=bcadd($receive_qnty,$order_data[$value]["RECEIVE"],15);
								}
								if($order_data[$value]["ISSUE"]) 
								{
									$issue_qnty=bcadd($issue_qnty,$order_data[$value]["ISSUE"],15);
								}
								if($order_data[$value]["RECEIVE_RTN"]) 
								{
									$receive_rtn_qnty=bcadd($receive_rtn_qnty,$order_data[$value]["RECEIVE_RTN"],15);
								}
								if($order_data[$value]["ISSUE_RTN"]) 
								{
									$issue_rtn_qnty=bcadd($issue_rtn_qnty,$order_data[$value]["ISSUE_RTN"],15);
								}
								if($order_data[$value]["TRANSFER_IN"]) 
								{
									$transfer_in_qnty=bcadd($transfer_in_qnty,$order_data[$value]["TRANSFER_IN"],15);
								}
								if($order_data[$value]["TRANSFER_OUT"]) 
								{
									$transfer_out_qnty=bcadd($transfer_out_qnty,$order_data[$value]["TRANSFER_OUT"],15);
								}
								
								if($order_amt!=0 && $order_qnty!=0) $order_rate=number_format(($order_amt/$order_qnty),15,".",""); else $order_rate=0;
								$values_item_ref.=",'".$order_qnty."','".$order_rate."','".$receive_qnty."','".$issue_qnty."','".$receive_rtn_qnty."','".$issue_rtn_qnty."','".$transfer_in_qnty."','".$transfer_out_qnty."'";
							}
							if($order_amt!=0 && $order_qnty!=0) $order_rate=number_format(($order_amt/$order_qnty),15,".",""); else $order_rate=0;
							$values_item_ref.=",'".$order_qnty."','".$order_rate."','".$receive_qnty."','".$issue_qnty."','".$receive_rtn_qnty."','".$issue_rtn_qnty."','".$transfer_in_qnty."','".$transfer_out_qnty."')";
							$insert_year_close_item_ref="insert into year_close_item_ref ($fields_item_ref) values $values_item_ref";
							$year_item_ref_id++;
							
							$update_year_item_ref_rid=execute_query($insert_year_close_item_ref);
							if($update_year_item_ref_rid) $update_year_item_ref_rid=1;
							else
							{
								$update_year_item_ref_rid=0;
								echo $insert_year_close_item_ref;oci_rollback($con);die;
							}
						}
						else
						{
							$order_qnty=$prev_close_item_ref_data[$prod_id][$ref_type_seq][$order_id]["CLOSING"];
							$order_amt=$prev_close_item_ref_data[$prod_id][$ref_type_seq][$order_id]["CLOSING_AMT"];
							$receive_qnty=$prev_close_item_ref_data[$prod_id][$ref_type_seq][$order_id]["CLOSING_RECEIVE"];
							$issue_qnty=$prev_close_item_ref_data[$prod_id][$ref_type_seq][$order_id]["CLOSING_ISSUE"];
							$receive_rtn_qnty=$prev_close_item_ref_data[$prod_id][$ref_type_seq][$order_id]["CLOSING_RECEIVE_RTN"];
							$issue_rtn_qnty=$prev_close_item_ref_data[$prod_id][$ref_type_seq][$order_id]["CLOSING_ISSUE_RTN"];
							$transfer_in_qnty=$prev_close_item_ref_data[$prod_id][$ref_type_seq][$order_id]["CLOSING_TRANSFER_IN"];
							$transfer_out_qnty=$prev_close_item_ref_data[$prod_id][$ref_type_seq][$order_id]["CLOSING_TRANSFER_OUT"];
							$insert_year_close_item_ref="update year_close_item_ref set ";
							foreach($lib_month_colum_arr as $key=>$value)
							{
								//###############   This Check carry previous month value #####################///
								if($order_data[$value]["QNT"]) 
								{
									$order_qnty =bcadd($order_qnty,$order_data[$value]["QNT"],15);
								}
								if($order_data[$value]["AMT"]) 
								{
									$order_amt =bcadd($order_amt,$order_data[$value]["AMT"],15);
								}
								if($order_data[$value]["RECEIVE"]) 
								{
									$receive_qnty=bcadd($receive_qnty,$order_data[$value]["RECEIVE"],15);
								}
								if($order_data[$value]["ISSUE"]) 
								{
									$issue_qnty=bcadd($issue_qnty,$order_data[$value]["ISSUE"],15);
								}
								if($order_data[$value]["RECEIVE_RTN"]) 
								{
									$receive_rtn_qnty=bcadd($receive_rtn_qnty,$order_data[$value]["RECEIVE_RTN"],15);
								}
								if($order_data[$value]["ISSUE_RTN"]) 
								{
									$issue_rtn_qnty=bcadd($issue_rtn_qnty,$order_data[$value]["ISSUE_RTN"],15);
								}
								if($order_data[$value]["TRANSFER_IN"]) 
								{
									$transfer_in_qnty=bcadd($transfer_in_qnty,$order_data[$value]["TRANSFER_IN"],15);
								}
								if($order_data[$value]["TRANSFER_OUT"]) 
								{
									$transfer_out_qnty=bcadd($transfer_out_qnty,$order_data[$value]["TRANSFER_OUT"],15);
								}
								
								if($order_amt!=0 && $order_qnty!=0) $order_rate=number_format(($order_amt/$order_qnty),15,".",""); else $order_rate=0;
								$insert_year_close_item_ref .="$key='".$order_qnty."', $key"."_rate='".$order_rate."', $key"."_receive='".$receive_qnty."', $key"."_issue='".$issue_qnty."', $key"."_receive_rtn='".$receive_rtn_qnty."', $key"."_issue_rtn='".$issue_rtn_qnty."', $key"."_transfer_in='".$transfer_in_qnty."', $key"."_transfer_out='".$transfer_out_qnty."',";
							}
							if($order_amt!=0 && $order_qnty!=0) $order_rate=number_format(($order_amt/$order_qnty),15,".",""); else $order_rate=0;
							$insert_year_close_item_ref .="closing='".$order_qnty."', last_rate='".$order_rate."', closing_receive='".$receive_qnty."', closing_issue='".$issue_qnty."', closing_receive_rtn='".$receive_rtn_qnty."', closing_issue_rtn='".$issue_rtn_qnty."', closing_transfer_in='".$transfer_in_qnty."', closing_transfer_out='".$transfer_out_qnty."' where id='".$prev_close_item_ref_data[$prod_id][$ref_type_seq][$order_id]["ID"]."'";
							
							$update_year_item_ref_rid=execute_query($insert_year_close_item_ref);
							if($update_year_item_ref_rid) $update_year_item_ref_rid=1;
							else
							{
								$update_year_item_ref_rid=0;
								echo $insert_year_close_item_ref;oci_rollback($con);die;
							}
						}
					}
					
					//echo $insert_year_close_item_ref;oci_rollback($con);die;
				}
			}
		
		}
		else
		{
			$insert_year_close_item ="";
			if($prev_close_item_data[$prod_id]["ID"])
			{
				$prod_qnty=$prev_close_item_data[$prod_id]["CLOSING"];
				$prod_amt=$prev_close_item_data[$prod_id]["CLOSING_AMT"];
				$receive_qnty=$prev_close_item_data[$prod_id]["CLOSING_RECEIVE"];
				$issue_qnty=$prev_close_item_data[$prod_id]["CLOSING_ISSUE"];
				$receive_rtn_qnty=$prev_close_item_data[$prod_id]["CLOSING_RECEIVE_RTN"];
				$issue_rtn_qnty=$prev_close_item_data[$prod_id]["CLOSING_ISSUE_RTN"];
				$transfer_in_qnty=$prev_close_item_data[$prod_id]["CLOSING_TRANSFER_IN"];
				$transfer_out_qnty=$prev_close_item_data[$prod_id]["CLOSING_TRANSFER_OUT"];
				$insert_year_close_item="update year_close_item set ";
				foreach($lib_month_colum_arr as $key=>$value)
				{
					if($data_pord[$prod_id][$value]["QNT"]) 
					{
						$prod_qnty =bcadd($prod_qnty,$data_pord[$prod_id][$value]["QNT"],15);
					}
					if($data_pord[$prod_id][$value]["AMT"]) 
					{
						$prod_amt =bcadd($prod_amt,$data_pord[$prod_id][$value]["AMT"],15);
					}
					if($data_pord[$prod_id][$value]["RECEIVE"]) 
					{
						$receive_qnty=bcadd($receive_qnty,$data_pord[$prod_id][$value]["RECEIVE"],15);
					}
					if($data_pord[$prod_id][$value]["ISSUE"]) 
					{
						$issue_qnty=bcadd($issue_qnty,$data_pord[$prod_id][$value]["ISSUE"],15);
					}
					if($data_pord[$prod_id][$value]["RECEIVE_RTN"]) 
					{
						$receive_rtn_qnty=bcadd($receive_rtn_qnty,$data_pord[$prod_id][$value]["RECEIVE_RTN"],15);
					}
					if($data_pord[$prod_id][$value]["ISSUE_RTN"]) 
					{
						$issue_rtn_qnty=bcadd($issue_rtn_qnty,$data_pord[$prod_id][$value]["ISSUE_RTN"],15);
					}
					if($data_pord[$prod_id][$value]["TRANSFER_IN"]) 
					{
						$transfer_in_qnty=bcadd($transfer_in_qnty,$data_pord[$prod_id][$value]["TRANSFER_IN"],15);
					}
					if($data_pord[$prod_id][$value]["TRANSFER_OUT"]) 
					{
						$transfer_out_qnty=bcadd($transfer_out_qnty,$data_pord[$prod_id][$value]["TRANSFER_OUT"],15);
					}
					//echo $prod_qnty."=".$data_pord[$prod_id][$value]["QNT"]."=".$prev_close_item_data[$prod_id]["CLOSING"]."=".$value;die;
					
					if($prod_amt!=0 && $prod_qnty!=0) $prod_rate=number_format(($prod_amt/$prod_qnty),15,".",""); else $prod_rate=0;
					$insert_year_close_item .="$key='".$prod_qnty."', $key"."_rate='".$prod_rate."', $key"."_receive='".$receive_qnty."', $key"."_issue='".$issue_qnty."', $key"."_receive_rtn='".$receive_rtn_qnty."', $key"."_issue_rtn='".$issue_rtn_qnty."', $key"."_transfer_in='".$transfer_in_qnty."', $key"."_transfer_out='".$transfer_out_qnty."',";
				}
				if($prod_amt!=0 && $prod_qnty!=0) $prod_rate=number_format(($prod_amt/$prod_qnty),15,".",""); else $prod_rate=0;
				$insert_year_close_item .="closing='".$prod_qnty."', last_rate='".$prod_rate."', closing_receive='".$receive_qnty."', closing_issue='".$issue_qnty."', closing_receive_rtn='".$receive_rtn_qnty."', closing_issue_rtn='".$issue_rtn_qnty."', closing_transfer_in='".$transfer_in_qnty."', closing_transfer_out='".$transfer_out_qnty."' where id='".$prev_close_item_data[$prod_id]["ID"]."'";
			}
			if($insert_year_close_item!="")
			{
				$update_year_item_rid=execute_query($insert_year_close_item);
				if($update_year_item_rid) 
				{
					$update_year_item_rid=1;
				}
				else
				{
					$update_year_item_rid=0;
					echo $insert_year_close_item;oci_rollback($con);die;
				}
			}
			
			if(count($prev_ref_data[$prod_id][1])>0)
			{
				foreach($prev_ref_data[$prod_id][1] as $store_id)
				{
					$insert_year_close_item_ref="";
					$store_qnty=$prev_close_item_ref_data[$prod_id][1][$store_id]["CLOSING"];
					$store_amt=$prev_close_item_ref_data[$prod_id][1][$store_id]["CLOSING_AMT"];
					$receive_qnty=$prev_close_item_ref_data[$prod_id][1][$store_id]["CLOSING_RECEIVE"];
					$issue_qnty=$prev_close_item_ref_data[$prod_id][1][$store_id]["CLOSING_ISSUE"];
					$receive_rtn_qnty=$prev_close_item_ref_data[$prod_id][1][$store_id]["CLOSING_RECEIVE_RTN"];
					$issue_rtn_qnty=$prev_close_item_ref_data[$prod_id][1][$store_id]["CLOSING_ISSUE_RTN"];
					$transfer_in_qnty=$prev_close_item_ref_data[$prod_id][1][$store_id]["CLOSING_TRANSFER_IN"];
					$transfer_out_qnty=$prev_close_item_ref_data[$prod_id][1][$store_id]["CLOSING_TRANSFER_OUT"];
					$insert_year_close_item_ref="update year_close_item_ref set ";
					foreach($lib_month_colum_arr as $key=>$value)
					{
						//###############   This Check carry previous month value #####################///
						
						if($data_pord_store[$prod_id][$store_id][$value]["QNT"]) 
						{
							$store_qnty=bcadd($store_qnty,$data_pord_store[$prod_id][$store_id][$value]["QNT"],15);
						}
						if($data_pord_store[$prod_id][$store_id][$value]["AMT"]) 
						{
							$store_amt=bcadd($store_amt,$data_pord_store[$prod_id][$store_id][$value]["AMT"],15);
						}
						if($data_pord_store[$prod_id][$store_id][$value]["RECEIVE"]) 
						{
							$receive_qnty=bcadd($receive_qnty,$data_pord_store[$prod_id][$store_id][$value]["RECEIVE"],15);
						}
						if($data_pord_store[$prod_id][$store_id][$value]["ISSUE"]) 
						{
							$issue_qnty=bcadd($issue_qnty,$data_pord_store[$prod_id][$store_id][$value]["ISSUE"],15);
						}
						if($data_pord_store[$prod_id][$store_id][$value]["RECEIVE_RTN"]) 
						{
							$receive_rtn_qnty=bcadd($receive_rtn_qnty,$data_pord_store[$prod_id][$store_id][$value]["RECEIVE_RTN"],15);
						}
						if($data_pord_store[$prod_id][$store_id][$value]["ISSUE_RTN"]) 
						{
							$issue_rtn_qnty=bcadd($issue_rtn_qnty,$data_pord_store[$prod_id][$store_id][$value]["ISSUE_RTN"],15);
						}
						if($data_pord_store[$prod_id][$store_id][$value]["TRANSFER_IN"]) 
						{
							$transfer_in_qnty=bcadd($transfer_in_qnty,$data_pord_store[$prod_id][$store_id][$value]["TRANSFER_IN"],15);
						}
						if($data_pord_store[$prod_id][$store_id][$value]["TRANSFER_OUT"]) 
						{
							$transfer_out_qnty=bcadd($transfer_out_qnty,$data_pord_store[$prod_id][$store_id][$value]["TRANSFER_OUT"],15);
						}
						if($store_amt!=0 && $store_qnty!=0) $store_rate=number_format(($store_amt/$store_qnty),15,".",""); else $store_rate=0;
						$insert_year_close_item_ref .="$key='".$store_qnty."', $key"."_rate='".$store_rate."', $key"."_receive='".$receive_qnty."', $key"."_issue='".$issue_qnty."', $key"."_receive_rtn='".$receive_rtn_qnty."', $key"."_issue_rtn='".$issue_rtn_qnty."', $key"."_transfer_in='".$transfer_in_qnty."', $key"."_transfer_out='".$transfer_out_qnty."',";
					}
					if($store_amt!=0 && $store_qnty!=0) $store_rate=number_format(($store_amt/$store_qnty),15,".",""); else $store_rate=0;
					$insert_year_close_item_ref .="closing='".$store_qnty."', last_rate='".$store_rate."', closing_receive='".$receive_qnty."', closing_issue='".$issue_qnty."', closing_receive_rtn='".$receive_rtn_qnty."', closing_issue_rtn='".$issue_rtn_qnty."', closing_transfer_in='".$transfer_in_qnty."', closing_transfer_out='".$transfer_out_qnty."'  where id='".$prev_close_item_ref_data[$prod_id][1][$store_id]["ID"]."'";
					
					$update_year_item_ref_rid=execute_query($insert_year_close_item_ref);
					if($update_year_item_ref_rid) $update_year_item_ref_rid=1;
					else
					{
						$update_year_item_ref_rid=0;
						echo $insert_year_close_item_ref;oci_rollback($con);die;
					}
				}
			}
			
			if(count($prev_ref_data[$prod_id][2])>0)
			{
				foreach($prev_ref_data[$prod_id][2] as $floor_id)
				{
					if($floor_id && trim(str_replace("'","",$floor_id)) !="")
					{
						$insert_year_close_item_ref="";
					
						$floor_qnty=$prev_close_item_ref_data[$prod_id][2][$floor_id]["CLOSING"];
						$floor_amt=$prev_close_item_ref_data[$prod_id][2][$floor_id]["CLOSING_AMT"];
						$receive_qnty=$prev_close_item_ref_data[$prod_id][2][$floor_id]["CLOSING_RECEIVE"];
						$issue_qnty=$prev_close_item_ref_data[$prod_id][2][$floor_id]["CLOSING_ISSUE"];
						$receive_rtn_qnty=$prev_close_item_ref_data[$prod_id][2][$floor_id]["CLOSING_RECEIVE_RTN"];
						$issue_rtn_qnty=$prev_close_item_ref_data[$prod_id][2][$floor_id]["CLOSING_ISSUE_RTN"];
						$transfer_in_qnty=$prev_close_item_ref_data[$prod_id][2][$floor_id]["CLOSING_TRANSFER_IN"];
						$transfer_out_qnty=$prev_close_item_ref_data[$prod_id][2][$floor_id]["CLOSING_TRANSFER_OUT"];
						$insert_year_close_item_ref="update year_close_item_ref set ";
						foreach($lib_month_colum_arr as $key=>$value)
						{
							//###############   This Check carry previous month value #####################///
							
							if($data_pord_store_floor[$prod_id][$floor_id][$value]["QNT"]) 
							{
								$floor_qnty=bcadd($floor_qnty,$data_pord_store_floor[$prod_id][$floor_id][$value]["QNT"],15);
							}
							if($data_pord_store_floor[$prod_id][$floor_id][$value]["AMT"]) 
							{
								$floor_amt=bcadd($floor_amt,$data_pord_store_floor[$prod_id][$floor_id][$value]["AMT"],15);
							}
							if($data_pord_store_floor[$prod_id][$floor_id][$value]["RECEIVE"]) 
							{
								$receive_qnty=bcadd($receive_qnty,$data_pord_store_floor[$prod_id][$floor_id][$value]["RECEIVE"],15);
							}
							if($data_pord_store_floor[$prod_id][$floor_id][$value]["ISSUE"]) 
							{
								$issue_qnty=bcadd($issue_qnty,$data_pord_store_floor[$prod_id][$floor_id][$value]["ISSUE"],15);
							}
							if($data_pord_store_floor[$prod_id][$floor_id][$value]["RECEIVE_RTN"]) 
							{
								$receive_rtn_qnty=bcadd($receive_rtn_qnty,$data_pord_store_floor[$prod_id][$floor_id][$value]["RECEIVE_RTN"],15);
							}
							if($data_pord_store_floor[$prod_id][$floor_id][$value]["ISSUE_RTN"]) 
							{
								$issue_rtn_qnty=bcadd($issue_rtn_qnty,$data_pord_store_floor[$prod_id][$floor_id][$value]["ISSUE_RTN"],15);
							}
							if($data_pord_store_floor[$prod_id][$floor_id][$value]["TRANSFER_IN"]) 
							{
								$transfer_in_qnty=bcadd($transfer_in_qnty,$data_pord_store_floor[$prod_id][$floor_id][$value]["TRANSFER_IN"],15);
							}
							if($data_pord_store_floor[$prod_id][$floor_id][$value]["TRANSFER_OUT"]) 
							{
								$transfer_out_qnty=bcadd($transfer_out_qnty,$data_pord_store_floor[$prod_id][$floor_id][$value]["TRANSFER_OUT"],15);
							}
							
							if($floor_amt!=0 && $floor_qnty!=0) $floor_rate=number_format(($floor_amt/$floor_qnty),15,".",""); else $floor_rate=0;
							$insert_year_close_item_ref .="$key='".$floor_qnty."', $key"."_rate='".$floor_rate."', $key"."_receive='".$receive_qnty."', $key"."_issue='".$issue_qnty."', $key"."_receive_rtn='".$receive_rtn_qnty."', $key"."_issue_rtn='".$issue_rtn_qnty."', $key"."_transfer_in='".$transfer_in_qnty."', $key"."_transfer_out='".$transfer_out_qnty."',";
						}
						if($floor_amt!=0 && $floor_qnty!=0) $floor_rate=number_format(($floor_amt/$floor_qnty),15,".",""); else $floor_rate=0;
						$insert_year_close_item_ref .="closing='".$floor_qnty."', last_rate='".$floor_rate."', closing_receive='".$receive_qnty."', closing_issue='".$issue_qnty."', closing_receive_rtn='".$receive_rtn_qnty."', closing_issue_rtn='".$issue_rtn_qnty."', closing_transfer_in='".$transfer_in_qnty."', closing_transfer_out='".$transfer_out_qnty."' where id='".$prev_close_item_ref_data[$prod_id][2][$floor_id]["ID"]."'";
						
						$update_year_item_ref_rid=execute_query($insert_year_close_item_ref);
						if($update_year_item_ref_rid) $update_year_item_ref_rid=1;
						else
						{
							$update_year_item_ref_rid=0;
							echo $insert_year_close_item_ref;oci_rollback($con);die;
						}
					}
				}
			}
			if(count($prev_ref_data[$prod_id][3])>0)
			{
				foreach($prev_ref_data[$prod_id][3] as $room_id)
				{
					if($room_id && trim(str_replace("'","",$room_id)) !="")
					{
						$insert_year_close_item_ref="";
						$room_qnty=$prev_close_item_ref_data[$prod_id][3][$room_id]["CLOSING"];
						$room_amt=$prev_close_item_ref_data[$prod_id][3][$room_id]["CLOSING_AMT"];
						$receive_qnty=$prev_close_item_ref_data[$prod_id][3][$room_id]["CLOSING_RECEIVE"];
						$issue_qnty=$prev_close_item_ref_data[$prod_id][3][$room_id]["CLOSING_ISSUE"];
						$receive_rtn_qnty=$prev_close_item_ref_data[$prod_id][3][$room_id]["CLOSING_RECEIVE_RTN"];
						$issue_rtn_qnty=$prev_close_item_ref_data[$prod_id][3][$room_id]["CLOSING_ISSUE_RTN"];
						$transfer_in_qnty=$prev_close_item_ref_data[$prod_id][3][$room_id]["CLOSING_TRANSFER_IN"];
						$transfer_out_qnty=$prev_close_item_ref_data[$prod_id][3][$room_id]["CLOSING_TRANSFER_OUT"];
						$insert_year_close_item_ref="update year_close_item_ref set ";
						foreach($lib_month_colum_arr as $key=>$value)
						{
							//###############   This Check carry previous month value #####################///
							
							if($data_pord_store_floor_room[$prod_id][$room_id][$value]["QNT"]) 
							{
								$room_qnty =bcadd($room_qnty,$data_pord_store_floor_room[$prod_id][$room_id][$value]["QNT"],15);
							}
							if($data_pord_store_floor_room[$prod_id][$room_id][$value]["AMT"]) 
							{
								$room_amt =bcadd($room_amt,$data_pord_store_floor_room[$prod_id][$room_id][$value]["AMT"],15);
							}
							if($data_pord_store_floor_room[$prod_id][$room_id][$value]["RECEIVE"]) 
							{
								$receive_qnty=bcadd($receive_qnty,$data_pord_store_floor_room[$prod_id][$room_id][$value]["RECEIVE"],15);
							}
							if($data_pord_store_floor_room[$prod_id][$room_id][$value]["ISSUE"]) 
							{
								$issue_qnty=bcadd($issue_qnty,$data_pord_store_floor_room[$prod_id][$room_id][$value]["ISSUE"],15);
							}
							if($data_pord_store_floor_room[$prod_id][$room_id][$value]["RECEIVE_RTN"]) 
							{
								$receive_rtn_qnty=bcadd($receive_rtn_qnty,$data_pord_store_floor_room[$prod_id][$room_id][$value]["RECEIVE_RTN"],15);
							}
							if($data_pord_store_floor_room[$prod_id][$room_id][$value]["ISSUE_RTN"]) 
							{
								$issue_rtn_qnty=bcadd($issue_rtn_qnty,$data_pord_store_floor_room[$prod_id][$room_id][$value]["ISSUE_RTN"],15);
							}
							if($data_pord_store_floor_room[$prod_id][$room_id][$value]["TRANSFER_IN"]) 
							{
								$transfer_in_qnty=bcadd($transfer_in_qnty,$data_pord_store_floor_room[$prod_id][$room_id][$value]["TRANSFER_IN"],15);
							}
							if($data_pord_store_floor_room[$prod_id][$room_id][$value]["TRANSFER_OUT"]) 
							{
								$transfer_out_qnty=bcadd($transfer_out_qnty,$data_pord_store_floor_room[$prod_id][$room_id][$value]["TRANSFER_OUT"],15);
							}
							
							if($room_amt!=0 && $room_qnty!=0) $room_rate=number_format(($room_amt/$room_qnty),15,".",""); else $room_rate=0;
							$insert_year_close_item_ref .="$key='".$room_qnty."', $key"."_rate='".$room_rate."', $key"."_receive='".$receive_qnty."', $key"."_issue='".$issue_qnty."', $key"."_receive_rtn='".$receive_rtn_qnty."', $key"."_issue_rtn='".$issue_rtn_qnty."', $key"."_transfer_in='".$transfer_in_qnty."', $key"."_transfer_out='".$transfer_out_qnty."',";
						}
						if($room_amt!=0 && $room_qnty!=0) $room_rate=number_format(($room_amt/$room_qnty),15,".",""); else $room_rate=0;
						$insert_year_close_item_ref .="closing='".$room_qnty."', last_rate='".$room_rate."', closing_receive='".$receive_qnty."', closing_issue='".$issue_qnty."', closing_receive_rtn='".$receive_rtn_qnty."', closing_issue_rtn='".$issue_rtn_qnty."', closing_transfer_in='".$transfer_in_qnty."', closing_transfer_out='".$transfer_out_qnty."' where id='".$prev_close_item_ref_data[$prod_id][3][$room_id]["ID"]."'";
						$update_year_item_ref_rid=execute_query($insert_year_close_item_ref);
						if($update_year_item_ref_rid) $update_year_item_ref_rid=1;
						else
						{
							$update_year_item_ref_rid=0;
							echo $insert_year_close_item_ref;oci_rollback($con);die;
						}
					}
				}
			}
			
			if(count($prev_ref_data[$prod_id][4])>0)
			{
				foreach($prev_ref_data[$prod_id][4] as $rack_id)
				{
					if($rack_id && trim(str_replace("'","",$rack_id)) !="")
					{
						$insert_year_close_item_ref="";
						$rack_qnty=$prev_close_item_ref_data[$prod_id][4][$rack_id]["CLOSING"];
						$rack_amt=$prev_close_item_ref_data[$prod_id][4][$rack_id]["CLOSING_AMT"];
						$receive_qnty=$prev_close_item_ref_data[$prod_id][4][$rack_id]["CLOSING_RECEIVE"];
						$issue_qnty=$prev_close_item_ref_data[$prod_id][4][$rack_id]["CLOSING_ISSUE"];
						$receive_rtn_qnty=$prev_close_item_ref_data[$prod_id][4][$rack_id]["CLOSING_RECEIVE_RTN"];
						$issue_rtn_qnty=$prev_close_item_ref_data[$prod_id][4][$rack_id]["CLOSING_ISSUE_RTN"];
						$transfer_in_qnty=$prev_close_item_ref_data[$prod_id][4][$rack_id]["CLOSING_TRANSFER_IN"];
						$transfer_out_qnty=$prev_close_item_ref_data[$prod_id][4][$rack_id]["CLOSING_TRANSFER_OUT"];
						$insert_year_close_item_ref="update year_close_item_ref set ";
						foreach($lib_month_colum_arr as $key=>$value)
						{
							//###############   This Check carry previous month value #####################///
							
							if($data_pord_store_floor_room_rac[$prod_id][$rack_id][$value]["QNT"]) 
							{
								$rack_qnty =bcadd($rack_qnty,$data_pord_store_floor_room_rac[$prod_id][$rack_id][$value]["QNT"],15);
							}
							if($data_pord_store_floor_room_rac[$prod_id][$rack_id][$value]["AMT"]) 
							{
								$rack_amt =bcadd($rack_amt,$data_pord_store_floor_room_rac[$prod_id][$rack_id][$value]["AMT"],15);
							}
							if($data_pord_store_floor_room_rac[$prod_id][$rack_id][$value]["RECEIVE"]) 
							{
								$receive_qnty=bcadd($receive_qnty,$data_pord_store_floor_room_rac[$prod_id][$rack_id][$value]["RECEIVE"],15);
							}
							if($data_pord_store_floor_room_rac[$prod_id][$rack_id][$value]["ISSUE"]) 
							{
								$issue_qnty=bcadd($issue_qnty,$data_pord_store_floor_room_rac[$prod_id][$rack_id][$value]["ISSUE"],15);
							}
							if($data_pord_store_floor_room_rac[$prod_id][$rack_id][$value]["RECEIVE_RTN"]) 
							{
								$receive_rtn_qnty=bcadd($receive_rtn_qnty,$data_pord_store_floor_room_rac[$prod_id][$rack_id][$value]["RECEIVE_RTN"],15);
							}
							if($data_pord_store_floor_room_rac[$prod_id][$rack_id][$value]["ISSUE_RTN"]) 
							{
								$issue_rtn_qnty=bcadd($issue_rtn_qnty,$data_pord_store_floor_room_rac[$prod_id][$rack_id][$value]["ISSUE_RTN"],15);
							}
							if($data_pord_store_floor_room_rac[$prod_id][$rack_id][$value]["TRANSFER_IN"]) 
							{
								$transfer_in_qnty=bcadd($transfer_in_qnty,$data_pord_store_floor_room_rac[$prod_id][$rack_id][$value]["TRANSFER_IN"],15);
							}
							if($data_pord_store_floor_room_rac[$prod_id][$rack_id][$value]["TRANSFER_OUT"]) 
							{
								$transfer_out_qnty=bcadd($transfer_out_qnty,$data_pord_store_floor_room_rac[$prod_id][$rack_id][$value]["TRANSFER_OUT"],15);
							}
							
							if($rack_amt!=0 && $rack_qnty!=0) $rack_rate=number_format(($rack_amt/$rack_qnty),15,".",""); else $rack_rate=0;
							$insert_year_close_item_ref .="$key='".$rack_qnty."', $key"."_rate='".$rack_rate."', $key"."_receive='".$receive_qnty."', $key"."_issue='".$issue_qnty."', $key"."_receive_rtn='".$receive_rtn_qnty."', $key"."_issue_rtn='".$issue_rtn_qnty."', $key"."_transfer_in='".$transfer_in_qnty."', $key"."_transfer_out='".$transfer_out_qnty."',";
						}
						if($rack_amt!=0 && $rack_qnty!=0) $rack_rate=number_format(($rack_amt/$rack_qnty),15,".",""); else $rack_rate=0;
						$insert_year_close_item_ref .="closing='".$rack_qnty."', last_rate='".$rack_rate."', closing_receive='".$receive_qnty."', closing_issue='".$issue_qnty."', closing_receive_rtn='".$receive_rtn_qnty."', closing_issue_rtn='".$issue_rtn_qnty."', closing_transfer_in='".$transfer_in_qnty."', closing_transfer_out='".$transfer_out_qnty."' where id='".$prev_close_item_ref_data[$prod_id][4][$rack_id]["ID"]."'";
						
						$update_year_item_ref_rid=execute_query($insert_year_close_item_ref);
						if($update_year_item_ref_rid) $update_year_item_ref_rid=1;
						else
						{
							$update_year_item_ref_rid=0;
							echo $insert_year_close_item_ref;oci_rollback($con);die;
						}
					}
				}
			}
			
			if(count($prev_ref_data[$prod_id][5])>0)
			{
				foreach($prev_ref_data[$prod_id][5] as $shelf_id)
				{
					if($shelf_id && trim(str_replace("'","",$shelf_id)) !="")
					{
						$insert_year_close_item_ref="";
						$shelf_qnty=$prev_close_item_ref_data[$prod_id][5][$shelf_id]["CLOSING"];
						$shelf_amt=$prev_close_item_ref_data[$prod_id][5][$shelf_id]["CLOSING_AMT"];
						$receive_qnty=$prev_close_item_ref_data[$prod_id][5][$shelf_id]["CLOSING_RECEIVE"];
						$issue_qnty=$prev_close_item_ref_data[$prod_id][5][$shelf_id]["CLOSING_ISSUE"];
						$receive_rtn_qnty=$prev_close_item_ref_data[$prod_id][5][$shelf_id]["CLOSING_RECEIVE_RTN"];
						$issue_rtn_qnty=$prev_close_item_ref_data[$prod_id][5][$shelf_id]["CLOSING_ISSUE_RTN"];
						$transfer_in_qnty=$prev_close_item_ref_data[$prod_id][5][$shelf_id]["CLOSING_TRANSFER_IN"];
						$transfer_out_qnty=$prev_close_item_ref_data[$prod_id][5][$shelf_id]["CLOSING_TRANSFER_OUT"];
						$insert_year_close_item_ref="update year_close_item_ref set ";
						foreach($lib_month_colum_arr as $key=>$value)
						{
							//###############   This Check carry previous month value #####################///
							
							if($data_pord_store_floor_room_rac_self[$prod_id][$shelf_id][$value]["QNT"]) 
							{
								$shelf_qnty =bcadd($shelf_qnty,$data_pord_store_floor_room_rac_self[$prod_id][$shelf_id][$value]["QNT"],15);
							}
							if($data_pord_store_floor_room_rac_self[$prod_id][$shelf_id][$value]["AMT"]) 
							{
								$shelf_amt =bcadd($shelf_amt,$data_pord_store_floor_room_rac_self[$prod_id][$shelf_id][$value]["AMT"],15);
							}
							if($data_pord_store_floor_room_rac_self[$prod_id][$shelf_id][$value]["RECEIVE"]) 
							{
								$receive_qnty=bcadd($receive_qnty,$data_pord_store_floor_room_rac_self[$prod_id][$shelf_id][$value]["RECEIVE"],15);
							}
							if($data_pord_store_floor_room_rac_self[$prod_id][$shelf_id][$value]["ISSUE"]) 
							{
								$issue_qnty=bcadd($issue_qnty,$data_pord_store_floor_room_rac_self[$prod_id][$shelf_id][$value]["ISSUE"],15);
							}
							if($data_pord_store_floor_room_rac_self[$prod_id][$shelf_id][$value]["RECEIVE_RTN"]) 
							{
								$receive_rtn_qnty=bcadd($receive_rtn_qnty,$data_pord_store_floor_room_rac_self[$prod_id][$shelf_id][$value]["RECEIVE_RTN"],15);
							}
							if($data_pord_store_floor_room_rac_self[$prod_id][$shelf_id][$value]["ISSUE_RTN"]) 
							{
								$issue_rtn_qnty=bcadd($issue_rtn_qnty,$data_pord_store_floor_room_rac_self[$prod_id][$shelf_id][$value]["ISSUE_RTN"],15);
							}
							if($data_pord_store_floor_room_rac_self[$prod_id][$shelf_id][$value]["TRANSFER_IN"]) 
							{
								$transfer_in_qnty=bcadd($transfer_in_qnty,$data_pord_store_floor_room_rac_self[$prod_id][$shelf_id][$value]["TRANSFER_IN"],15);
							}
							if($data_pord_store_floor_room_rac_self[$prod_id][$shelf_id][$value]["TRANSFER_OUT"]) 
							{
								$transfer_out_qnty=bcadd($transfer_out_qnty,$data_pord_store_floor_room_rac_self[$prod_id][$shelf_id][$value]["TRANSFER_OUT"],15);
							}
							
							if($shelf_amt!=0 && $shelf_qnty!=0) $shelf_rate=number_format(($shelf_amt/$shelf_qnty),15,".",""); else $shelf_rate=0;
							$insert_year_close_item_ref .="$key='".$shelf_qnty."', $key"."_rate='".$shelf_rate."', $key"."_receive='".$receive_qnty."', $key"."_issue='".$issue_qnty."', $key"."_receive_rtn='".$receive_rtn_qnty."', $key"."_issue_rtn='".$issue_rtn_qnty."', $key"."_transfer_in='".$transfer_in_qnty."', $key"."_transfer_out='".$transfer_out_qnty."',";
						}
						if($shelf_amt!=0 && $shelf_qnty!=0) $shelf_rate=number_format(($shelf_amt/$shelf_qnty),15,".",""); else $shelf_rate=0;
						$insert_year_close_item_ref .="closing='".$shelf_qnty."', last_rate='".$shelf_rate."', closing_receive='".$receive_qnty."', closing_issue='".$issue_qnty."', closing_receive_rtn='".$receive_rtn_qnty."', closing_issue_rtn='".$issue_rtn_qnty."', closing_transfer_in='".$transfer_in_qnty."', closing_transfer_out='".$transfer_out_qnty."' where id='".$prev_close_item_ref_data[$prod_id][5][$shelf_id]["ID"]."'";
						
						$update_year_item_ref_rid=execute_query($insert_year_close_item_ref);
						if($update_year_item_ref_rid) $update_year_item_ref_rid=1;
						else
						{
							$update_year_item_ref_rid=0;
							echo $insert_year_close_item_ref;oci_rollback($con);die;
						}
					}
				}
			}
			
			if(count($prev_ref_data[$prod_id][6])>0)
			{
				foreach($prev_ref_data[$prod_id][6] as $bin_id)
				{
					if($bin_id && trim(str_replace("'","",$bin_id)) !="")
					{
						$insert_year_close_item_ref="";
						$bin_qnty=$prev_close_item_ref_data[$prod_id][6][$bin_id]["CLOSING"];
						$bin_amt=$prev_close_item_ref_data[$prod_id][6][$bin_id]["CLOSING_AMT"];
						$receive_qnty=$prev_close_item_ref_data[$prod_id][6][$bin_id]["CLOSING_RECEIVE"];
						$issue_qnty=$prev_close_item_ref_data[$prod_id][6][$bin_id]["CLOSING_ISSUE"];
						$receive_rtn_qnty=$prev_close_item_ref_data[$prod_id][6][$bin_id]["CLOSING_RECEIVE_RTN"];
						$issue_rtn_qnty=$prev_close_item_ref_data[$prod_id][6][$bin_id]["CLOSING_ISSUE_RTN"];
						$transfer_in_qnty=$prev_close_item_ref_data[$prod_id][6][$bin_id]["CLOSING_TRANSFER_IN"];
						$transfer_out_qnty=$prev_close_item_ref_data[$prod_id][6][$bin_id]["CLOSING_TRANSFER_OUT"];
						$insert_year_close_item_ref="update year_close_item_ref set ";
						foreach($lib_month_colum_arr as $key=>$value)
						{
							//###############   This Check carry previous month value #####################///
							
							if($data_pord_store_floor_room_rac_self_bin[$prod_id][$bin_id][$value]["QNT"]) 
							{
								$bin_qnty =bcadd($bin_qnty,$data_pord_store_floor_room_rac_self_bin[$prod_id][$bin_id][$value]["QNT"],15);
							}
							if($data_pord_store_floor_room_rac_self_bin[$prod_id][$bin_id][$value]["AMT"]) 
							{
								$bin_amt =bcadd($bin_amt,$data_pord_store_floor_room_rac_self_bin[$prod_id][$bin_id][$value]["AMT"],15);
							}
							if($data_pord_store_floor_room_rac_self_bin[$prod_id][$bin_id][$value]["RECEIVE"]) 
							{
								$receive_qnty=bcadd($receive_qnty,$data_pord_store_floor_room_rac_self_bin[$prod_id][$bin_id][$value]["RECEIVE"],15);
							}
							if($data_pord_store_floor_room_rac_self_bin[$prod_id][$bin_id][$value]["ISSUE"]) 
							{
								$issue_qnty=bcadd($issue_qnty,$data_pord_store_floor_room_rac_self_bin[$prod_id][$bin_id][$value]["ISSUE"],15);
							}
							if($data_pord_store_floor_room_rac_self_bin[$prod_id][$bin_id][$value]["RECEIVE_RTN"]) 
							{
								$receive_rtn_qnty=bcadd($receive_rtn_qnty,$data_pord_store_floor_room_rac_self_bin[$prod_id][$bin_id][$value]["RECEIVE_RTN"],15);
							}
							if($data_pord_store_floor_room_rac_self_bin[$prod_id][$bin_id][$value]["ISSUE_RTN"]) 
							{
								$issue_rtn_qnty=bcadd($issue_rtn_qnty,$data_pord_store_floor_room_rac_self_bin[$prod_id][$bin_id][$value]["ISSUE_RTN"],15);
							}
							if($data_pord_store_floor_room_rac_self_bin[$prod_id][$bin_id][$value]["TRANSFER_IN"]) 
							{
								$transfer_in_qnty=bcadd($transfer_in_qnty,$data_pord_store_floor_room_rac_self_bin[$prod_id][$bin_id][$value]["TRANSFER_IN"],15);
							}
							if($data_pord_store_floor_room_rac_self_bin[$prod_id][$bin_id][$value]["TRANSFER_OUT"]) 
							{
								$transfer_out_qnty=bcadd($transfer_out_qnty,$data_pord_store_floor_room_rac_self_bin[$prod_id][$bin_id][$value]["TRANSFER_OUT"],15);
							}
							
							if($bin_amt!=0 && $bin_qnty!=0) $bin_rate=number_format(($bin_amt / $bin_qnty),15,".",""); else $bin_rate=0;
							$insert_year_close_item_ref .="$key='".$bin_qnty."', $key"."_rate='".$bin_rate."', $key"."_receive='".$receive_qnty."', $key"."_issue='".$issue_qnty."', $key"."_receive_rtn='".$receive_rtn_qnty."', $key"."_issue_rtn='".$issue_rtn_qnty."', $key"."_transfer_in='".$transfer_in_qnty."', $key"."_transfer_out='".$transfer_out_qnty."',";
						}
						if($bin_amt!=0 && $bin_qnty!=0) $bin_rate=number_format(($bin_amt / $bin_qnty),15,".",""); else $bin_rate=0;
						$insert_year_close_item_ref .="closing='".$bin_qnty."', last_rate='".$bin_rate."', closing_receive='".$receive_qnty."', closing_issue='".$issue_qnty."', closing_receive_rtn='".$receive_rtn_qnty."', closing_issue_rtn='".$issue_rtn_qnty."', closing_transfer_in='".$transfer_in_qnty."', closing_transfer_out='".$transfer_out_qnty."' where id='".$prev_close_item_ref_data[$prod_id][6][$bin_id]["ID"]."'";
						
						$update_year_item_ref_rid=execute_query($insert_year_close_item_ref);
						if($update_year_item_ref_rid) $update_year_item_ref_rid=1;
						else
						{
							$update_year_item_ref_rid=0;
							echo $insert_year_close_item_ref;oci_rollback($con);die;
						}
					}
				}
			}
			
			if(count($prev_ref_data[$prod_id][7])>0)
			{
				foreach($prev_ref_data[$prod_id][7] as $order_id)
				{
					if($order_id && trim(str_replace("'","",$order_id)) !="")
					{
						$insert_year_close_item_ref="";
						$ref_type_seq=7;
						$order_id_ref=$order_id."*".$ref_type_seq;
						$order_qnty=$prev_close_item_ref_data[$prod_id][$ref_type_seq][$order_id]["CLOSING"];
						$order_amt=$prev_close_item_ref_data[$prod_id][$ref_type_seq][$order_id]["CLOSING_AMT"];
						$receive_qnty=$prev_close_item_ref_data[$prod_id][$ref_type_seq][$order_id]["CLOSING_RECEIVE"];
						$issue_qnty=$prev_close_item_ref_data[$prod_id][$ref_type_seq][$order_id]["CLOSING_ISSUE"];
						$receive_rtn_qnty=$prev_close_item_ref_data[$prod_id][$ref_type_seq][$order_id]["CLOSING_RECEIVE_RTN"];
						$issue_rtn_qnty=$prev_close_item_ref_data[$prod_id][$ref_type_seq][$order_id]["CLOSING_ISSUE_RTN"];
						$transfer_in_qnty=$prev_close_item_ref_data[$prod_id][$ref_type_seq][$order_id]["CLOSING_TRANSFER_IN"];
						$transfer_out_qnty=$prev_close_item_ref_data[$prod_id][$ref_type_seq][$order_id]["CLOSING_TRANSFER_OUT"];
						$insert_year_close_item_ref="update year_close_item_ref set ";
						foreach($lib_month_colum_arr as $key=>$value)
						{
							//###############   This Check carry previous month value #####################///
							if($data_pord_order[$prod_id][$order_id_ref][$value]["QNT"]) 
							{
								$order_qnty =bcadd($order_qnty,$data_pord_order[$prod_id][$order_id_ref][$value]["QNT"],15);
							}
							if($data_pord_order[$prod_id][$order_id_ref][$value]["AMT"]) 
							{
								$order_amt =bcadd($order_amt,$data_pord_order[$prod_id][$order_id_ref][$value]["AMT"],15);
							}
							if($data_pord_order[$prod_id][$order_id_ref][$value]["RECEIVE"]) 
							{
								$receive_qnty=bcadd($receive_qnty,$data_pord_order[$prod_id][$order_id_ref][$value]["RECEIVE"],15);
							}
							if($data_pord_order[$prod_id][$order_id_ref][$value]["ISSUE"]) 
							{
								$issue_qnty=bcadd($issue_qnty,$data_pord_order[$prod_id][$order_id_ref][$value]["ISSUE"],15);
							}
							if($data_pord_order[$prod_id][$order_id_ref][$value]["RECEIVE_RTN"]) 
							{
								$receive_rtn_qnty=bcadd($receive_rtn_qnty,$data_pord_order[$prod_id][$order_id_ref][$value]["RECEIVE_RTN"],15);
							}
							if($data_pord_order[$prod_id][$order_id_ref][$value]["ISSUE_RTN"]) 
							{
								$issue_rtn_qnty=bcadd($issue_rtn_qnty,$data_pord_order[$prod_id][$order_id_ref][$value]["ISSUE_RTN"],15);
							}
							if($data_pord_order[$prod_id][$order_id_ref][$value]["TRANSFER_IN"]) 
							{
								$transfer_in_qnty=bcadd($transfer_in_qnty,$data_pord_order[$prod_id][$order_id_ref][$value]["TRANSFER_IN"],15);
							}
							if($data_pord_order[$prod_id][$order_id_ref][$value]["TRANSFER_OUT"]) 
							{
								$transfer_out_qnty=bcadd($transfer_out_qnty,$data_pord_order[$prod_id][$order_id_ref][$value]["TRANSFER_OUT"],15);
							}
							
							if($order_amt!=0 && $order_qnty!=0) $order_rate=number_format(($order_amt/$order_qnty),15,".",""); else $order_rate=0;
							$insert_year_close_item_ref .="$key='".$order_qnty."', $key"."_rate='".$order_rate."', $key"."_receive='".$receive_qnty."', $key"."_issue='".$issue_qnty."', $key"."_receive_rtn='".$receive_rtn_qnty."', $key"."_issue_rtn='".$issue_rtn_qnty."', $key"."_transfer_in='".$transfer_in_qnty."', $key"."_transfer_out='".$transfer_out_qnty."',";
						}
						if($order_amt!=0 && $order_qnty!=0) $order_rate=number_format(($order_amt/$order_qnty),15,".",""); else $order_rate=0;
						$insert_year_close_item_ref .="closing='".$order_qnty."', last_rate='".$order_rate."', closing_receive='".$receive_qnty."', closing_issue='".$issue_qnty."', closing_receive_rtn='".$receive_rtn_qnty."', closing_issue_rtn='".$issue_rtn_qnty."', closing_transfer_in='".$transfer_in_qnty."', closing_transfer_out='".$transfer_out_qnty."' where id='".$prev_close_item_ref_data[$prod_id][$ref_type_seq][$order_id]["ID"]."'";
						
						$update_year_item_ref_rid=execute_query($insert_year_close_item_ref);
						if($update_year_item_ref_rid) $update_year_item_ref_rid=1;
						else
						{
							$update_year_item_ref_rid=0;
							echo $insert_year_close_item_ref;oci_rollback($con);die;
						}
					}
				}
			}
			
			if(count($prev_ref_data[$prod_id][8])>0)
			{
				foreach($prev_ref_data[$prod_id][8] as $order_id)
				{
					if($order_id && trim(str_replace("'","",$order_id)) !="")
					{
						$insert_year_close_item_ref="";
						$ref_type_seq=8;
						$order_id_ref=$order_id."*".$ref_type_seq;
						$order_qnty=$prev_close_item_ref_data[$prod_id][$ref_type_seq][$order_id]["CLOSING"];
						$order_amt=$prev_close_item_ref_data[$prod_id][$ref_type_seq][$order_id]["CLOSING_AMT"];
						$receive_qnty=$prev_close_item_ref_data[$prod_id][$ref_type_seq][$order_id]["CLOSING_RECEIVE"];
						$issue_qnty=$prev_close_item_ref_data[$prod_id][$ref_type_seq][$order_id]["CLOSING_ISSUE"];
						$receive_rtn_qnty=$prev_close_item_ref_data[$prod_id][$ref_type_seq][$order_id]["CLOSING_RECEIVE_RTN"];
						$issue_rtn_qnty=$prev_close_item_ref_data[$prod_id][$ref_type_seq][$order_id]["CLOSING_ISSUE_RTN"];
						$transfer_in_qnty=$prev_close_item_ref_data[$prod_id][$ref_type_seq][$order_id]["CLOSING_TRANSFER_IN"];
						$transfer_out_qnty=$prev_close_item_ref_data[$prod_id][$ref_type_seq][$order_id]["CLOSING_TRANSFER_OUT"];
						$insert_year_close_item_ref="update year_close_item_ref set ";
						foreach($lib_month_colum_arr as $key=>$value)
						{
							//###############   This Check carry previous month value #####################///
							if($data_pord_order[$prod_id][$order_id_ref][$value]["QNT"]) 
							{
								$order_qnty =bcadd($order_qnty,$data_pord_order[$prod_id][$order_id_ref][$value]["QNT"],15);
							}
							if($data_pord_order[$prod_id][$order_id_ref][$value]["AMT"]) 
							{
								$order_amt =bcadd($order_amt,$data_pord_order[$prod_id][$order_id_ref][$value]["AMT"],15);
							}
							if($data_pord_order[$prod_id][$order_id_ref][$value]["RECEIVE"]) 
							{
								$receive_qnty=bcadd($receive_qnty,$data_pord_order[$prod_id][$order_id_ref][$value]["RECEIVE"],15);
							}
							if($data_pord_order[$prod_id][$order_id_ref][$value]["ISSUE"]) 
							{
								$issue_qnty=bcadd($issue_qnty,$data_pord_order[$prod_id][$order_id_ref][$value]["ISSUE"],15);
							}
							if($data_pord_order[$prod_id][$order_id_ref][$value]["RECEIVE_RTN"]) 
							{
								$receive_rtn_qnty=bcadd($receive_rtn_qnty,$data_pord_order[$prod_id][$order_id_ref][$value]["RECEIVE_RTN"],15);
							}
							if($data_pord_order[$prod_id][$order_id_ref][$value]["ISSUE_RTN"]) 
							{
								$issue_rtn_qnty=bcadd($issue_rtn_qnty,$data_pord_order[$prod_id][$order_id_ref][$value]["ISSUE_RTN"],15);
							}
							if($data_pord_order[$prod_id][$order_id_ref][$value]["TRANSFER_IN"]) 
							{
								$transfer_in_qnty=bcadd($transfer_in_qnty,$data_pord_order[$prod_id][$order_id_ref][$value]["TRANSFER_IN"],15);
							}
							if($data_pord_order[$prod_id][$order_id_ref][$value]["TRANSFER_OUT"]) 
							{
								$transfer_out_qnty=bcadd($transfer_out_qnty,$data_pord_order[$prod_id][$order_id_ref][$value]["TRANSFER_OUT"],15);
							}
							
							if($order_amt!=0 && $order_qnty!=0) $order_rate=number_format(($order_amt/$order_qnty),15,".",""); else $order_rate=0;
							$insert_year_close_item_ref .="$key='".$order_qnty."', $key"."_rate='".$order_rate."', $key"."_receive='".$receive_qnty."', $key"."_issue='".$issue_qnty."', $key"."_receive_rtn='".$receive_rtn_qnty."', $key"."_issue_rtn='".$issue_rtn_qnty."', $key"."_transfer_in='".$transfer_in_qnty."', $key"."_transfer_out='".$transfer_out_qnty."',";
						}
						if($order_amt!=0 && $order_qnty!=0) $order_rate=number_format(($order_amt/$order_qnty),15,".",""); else $order_rate=0;
						$insert_year_close_item_ref .="closing='".$order_qnty."', last_rate='".$order_rate."', closing_receive='".$receive_qnty."', closing_issue='".$issue_qnty."', closing_receive_rtn='".$receive_rtn_qnty."', closing_issue_rtn='".$issue_rtn_qnty."', closing_transfer_in='".$transfer_in_qnty."', closing_transfer_out='".$transfer_out_qnty."' where id='".$prev_close_item_ref_data[$prod_id][$ref_type_seq][$order_id]["ID"]."'";
						
						$update_year_item_ref_rid=execute_query($insert_year_close_item_ref);
						if($update_year_item_ref_rid) $update_year_item_ref_rid=1;
						else
						{
							$update_year_item_ref_rid=0;
							echo $insert_year_close_item_ref;oci_rollback($con);die;
						}
					}
				}
			}
		}
		//echo $insert_year_close_item;die;
	}
	
	//$cbo_item_category_id
	$category_datas=array();$update_category_rid=true;
	if($cbo_month>0)
	{
		//print_r($data_pord);oci_rollback($con);die;
		$sql_ac_period_dtls=sql_select("select id as ID, period_ending_date as PERIOD_ENDING_DATE from lib_ac_period_dtls where mst_id=$cbo_year and month_id=$cbo_month and period_locked=0 and is_locked=0");
		$ac_period_dtls_id=$sql_ac_period_dtls[0]["ID"];
		if($db_type==0)
		{
			$dtls_period_ending_date=change_date_format($sql_ac_period_dtls[0]["PERIOD_ENDING_DATE"],'yyyy-mm-dd');
		}
		else
		{
			$dtls_period_ending_date=change_date_format($sql_ac_period_dtls[0]["PERIOD_ENDING_DATE"],'','',1);
		}
		$update_category=execute_query("update lib_item_category_comp_wise set ac_period_dtls_id=$ac_period_dtls_id, period_ending_date='$dtls_period_ending_date', updated_by=$user_id, update_date='".$pc_date_time."' where category_id=$cbo_item_category_id and company_id=$cbo_company_id");
		if($update_category) {$update_category_rid=1;} else { $update_category_rid=0; echo "update lib_item_category_comp_wise set ac_period_dtls_id=$ac_period_dtls_id, period_ending_date='$dtls_period_ending_date', updated_by=$user_id, update_date='".$pc_date_time."' where category_id=$cbo_item_category_id and company_id=$cbo_company_id"; oci_rollback($con);die;}
		
		if($ac_period_dtls_id)
		{
			$sql_cat=sql_select("select id as ID, category_id as CATEGORY_ID, actual_category_name as ACTUAL_CATEGORY_NAME, period_ending_date as PERIOD_ENDING_DATE from lib_item_category_comp_wise where status_active=1 and is_deleted=0 and company_id=$cbo_company_id and is_inventory=1 and category_id <> $cbo_item_category_id");
			if(count($sql_cat)>0)
			{
				foreach($sql_cat as $val)
				{
					if($val["PERIOD_ENDING_DATE"]!="" && $val["PERIOD_ENDING_DATE"]!="0000-00-00")
					{
						if( strtotime($dtls_period_ending_date) != strtotime($val["PERIOD_ENDING_DATE"]))
						{
							$category_datas[$val["CATEGORY_ID"]]["CATEGORY_ID"]=$val["CATEGORY_ID"];
							$category_datas[$val["CATEGORY_ID"]]["ACTUAL_CATEGORY_NAME"]=$val["ACTUAL_CATEGORY_NAME"];
							$category_datas[$val["CATEGORY_ID"]]["PERIOD_ENDING_DATE"]=$val["PERIOD_ENDING_DATE"];
						}
					}
					else
					{
						$category_datas[$val["CATEGORY_ID"]]["CATEGORY_ID"]=$val["CATEGORY_ID"];
						$category_datas[$val["CATEGORY_ID"]]["ACTUAL_CATEGORY_NAME"]=$val["ACTUAL_CATEGORY_NAME"];
						$category_datas[$val["CATEGORY_ID"]]["PERIOD_ENDING_DATE"]=$val["PERIOD_ENDING_DATE"];
					}
				}
			}
		}
		
		if(count($category_datas)==0)
		{
			if($cbo_month==12)
			{
				$update_period_mst=execute_query("update lib_ac_period_mst set is_closed=1, is_locked=1, updated_by=$user_id, update_date='".$pc_date_time."' where id=$cbo_year");
				if($update_period_mst) {$update_period_mst_rid=1;} else { $update_period_mst_rid=0; echo "update lib_ac_period_mst set is_closed=1, is_locked=1, updated_by=$user_id, update_date='".$pc_date_time."' where id=$cbo_year"; oci_rollback($con);die;}
				$update_period_dtls=execute_query("update lib_ac_period_dtls set period_locked=1, is_locked=1, updated_by=$user_id, update_date='".$pc_date_time."' where mst_id=$cbo_year");
				if($update_period_dtls) {$update_period_dtls_rid=1;} else { $update_period_dtls_rid=0; echo "update lib_ac_period_dtls set period_locked=1, is_locked=1, updated_by=$user_id, update_date='".$pc_date_time."' where mst_id=$cbo_year"; oci_rollback($con);die;}
			}
			else
			{
				$update_period_dtls=execute_query("update lib_ac_period_dtls set period_locked=1, is_locked=1, updated_by=$user_id, update_date='".$pc_date_time."' where mst_id=$cbo_year and month_id<=$cbo_month");
				if($update_period_dtls) {$update_period_dtls_rid=1;} else { $update_period_dtls_rid=0; echo "update lib_ac_period_dtls set period_locked=1, is_locked=1, updated_by=$user_id, update_date='".$pc_date_time."' where mst_id=$cbo_year and month_id<=$cbo_month"; oci_rollback($con);die;}
			}
		}
		
	}
	else
	{
		$sql_ac_period_dtls=sql_select("select id as ID, period_ending_date as PERIOD_ENDING_DATE from lib_ac_period_dtls where mst_id=$cbo_year and month_id=12 and period_locked=0 and is_locked=0");
		$ac_period_dtls_id=$sql_ac_period_dtls[0]["ID"];
		if($db_type==0)
		{
			$dtls_period_ending_date=change_date_format($sql_ac_period_dtls[0]["PERIOD_ENDING_DATE"],'yyyy-mm-dd');
		}
		else
		{
			$dtls_period_ending_date=change_date_format($sql_ac_period_dtls[0]["PERIOD_ENDING_DATE"],'','',1);
		}
		
		$update_category=execute_query("update lib_item_category_comp_wise set ac_period_dtls_id=$ac_period_dtls_id, period_ending_date='$dtls_period_ending_date', updated_by=$user_id, update_date='".$pc_date_time."' where category_id=$cbo_item_category_id and company_id=$cbo_company_id");
		if($update_category) {$update_category_rid=1;} else { $update_category_rid=0; echo "update lib_item_category_comp_wise set ac_period_dtls_id=$ac_period_dtls_id, period_ending_date='$dtls_period_ending_date', updated_by=$user_id, update_date='".$pc_date_time."' where category_id=$cbo_item_category_id and company_id=$cbo_company_id"; oci_rollback($con);die;}
		
		
		if($ac_period_dtls_id)
		{
			$sql_cat=sql_select("select id as ID, category_id as CATEGORY_ID, actual_category_name as ACTUAL_CATEGORY_NAME, period_ending_date as PERIOD_ENDING_DATE from lib_item_category_comp_wise where status_active=1 and is_deleted=0 and is_inventory=1 and company_id=$cbo_company_id and category_id <> $cbo_item_category_id");
			if(count($sql_cat)>0)
			{
				foreach($sql_cat as $val)
				{
					if($val["PERIOD_ENDING_DATE"]!="" && $val["PERIOD_ENDING_DATE"]!="0000-00-00")
					{
						if( strtotime($dtls_period_ending_date) != strtotime($val["PERIOD_ENDING_DATE"]))
						{
							$category_datas[$val["CATEGORY_ID"]]["CATEGORY_ID"]=$val["CATEGORY_ID"];
							$category_datas[$val["CATEGORY_ID"]]["ACTUAL_CATEGORY_NAME"]=$val["ACTUAL_CATEGORY_NAME"];
							$category_datas[$val["CATEGORY_ID"]]["PERIOD_ENDING_DATE"]=$val["PERIOD_ENDING_DATE"];
						}
					}
					else
					{
						$category_datas[$val["CATEGORY_ID"]]["CATEGORY_ID"]=$val["CATEGORY_ID"];
						$category_datas[$val["CATEGORY_ID"]]["ACTUAL_CATEGORY_NAME"]=$val["ACTUAL_CATEGORY_NAME"];
						$category_datas[$val["CATEGORY_ID"]]["PERIOD_ENDING_DATE"]=$val["PERIOD_ENDING_DATE"];
					}
				}
			}
		}
		//period_1
		if(count($category_datas)==0)
		{
			$update_period_mst=execute_query("update lib_ac_period_mst set is_closed=1, is_locked=1, updated_by=$user_id, update_date='".$pc_date_time."' where id=$cbo_year");
			if($update_period_mst) {$update_period_mst_rid=1;} else { $update_period_mst_rid=0; echo "update lib_ac_period_mst set is_closed=1, is_locked=1, updated_by=$user_id, update_date='".$pc_date_time."' where id=$cbo_year"; oci_rollback($con);die;}
			$update_period_dtls=execute_query("update lib_ac_period_dtls set period_locked=1, is_locked=1, updated_by=$user_id, update_date='".$pc_date_time."' where mst_id=$cbo_year");
			if($update_period_dtls) {$update_period_dtls_rid=1;} else { $update_period_dtls_rid=0; echo "update lib_ac_period_dtls set period_locked=1, is_locked=1, updated_by=$user_id, update_date='".$pc_date_time."' where mst_id=$cbo_year"; oci_rollback($con);die;}
		}
	}
	
	//echo $update_period_mst_rid=$update_period_dtls_rid=$update_year_item_rid=$update_year_item_ref_rid;oci_rollback($con);die;
	if($db_type==0)
	{
		if($update_period_mst_rid && $update_period_dtls_rid && $update_year_item_rid && $update_year_item_ref_rid && $update_category_rid)
		{
			mysql_query("COMMIT");  
			echo "Closing Process Completed successfully";
		}
		else
		{
			mysql_query("ROLLBACK"); 
			echo "Closing Process Failed";
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($update_period_mst_rid && $update_period_dtls_rid && $update_year_item_rid && $update_year_item_ref_rid && $update_category_rid)
		{
			oci_commit($con);  
			echo "Closing Process Completed successfully";
		}
		else
		{
			oci_rollback($con);
			echo "Closing Process Failed";
		}
	}
	disconnect($con);
	die;

}





function custom_sub($Num1,$Num2,$Scale=null) 
{
	//echo $Num1;return;
	// check if they're valid positive numbers, extract the whole numbers and decimals
	if(!preg_match("/^\+?(\d+)(\.\d+)?$/",$Num1,$Tmp1)||
		!preg_match("/^\+?(\d+)(\.\d+)?$/",$Num2,$Tmp2)) return('0');
	
	// this is where the result is stored
	$Output=array();
	
	// remove ending zeroes from decimals and remove point
	$Dec1=isset($Tmp1[2])?rtrim(substr($Tmp1[2],1),'0'):'';
	$Dec2=isset($Tmp2[2])?rtrim(substr($Tmp2[2],1),'0'):'';
	
	// calculate the longest length of decimals
	$DLen=max(strlen($Dec1),strlen($Dec2));
	
	// if $Scale is null, automatically set it to the amount of decimal places for accuracy
	if($Scale==null) $Scale=$DLen;
	
	// remove leading zeroes and reverse the whole numbers, then append padded decimals on the end
	$Num1=strrev(ltrim($Tmp1[1],'0').str_pad($Dec1,$DLen,'0'));
	$Num2=strrev(ltrim($Tmp2[1],'0').str_pad($Dec2,$DLen,'0'));
	
	// calculate the longest length we need to process
	$MLen=max(strlen($Num1),strlen($Num2));
	
	// pad the two numbers so they are of equal length (both equal to $MLen)
	$Num1=str_pad($Num1,$MLen,'0');
	$Num2=str_pad($Num2,$MLen,'0');
	
	// process each digit, keep the ones, carry the tens (remainders)
	for($i=0;$i<$MLen;$i++) {
		$Sum=((int)$Num1{$i}-(int)$Num2{$i});
		if(isset($Output[$i])) $Sum+=$Output[$i];
		$Output[$i]=$Sum%10;
		if($Sum>9) $Output[$i+1]=1;
	}
	
	// convert the array to string and reverse it
	$Output=strrev(implode($Output));
	
	// substring the decimal digits from the result, pad if necessary (if $Scale > amount of actual decimals)
	// next, since actual zero values can cause a problem with the substring values, if so, just simply give '0'
	// next, append the decimal value, if $Scale is defined, and return result
	$Decimal=str_pad(substr($Output,-$DLen,$Scale),$Scale,'0');
	$Output=(($MLen-$DLen<1)?'0':substr($Output,0,-$DLen));
	$Output.=(($Scale>0)?".{$Decimal}":'');
	return($Output);
}

function custom_add($Num1,$Num2,$Scale=null) 
{
	// check if they're valid positive numbers, extract the whole numbers and decimals
	if(!preg_match("/^\+?(\d+)(\.\d+)?$/",$Num1,$Tmp1)||
		!preg_match("/^\+?(\d+)(\.\d+)?$/",$Num2,$Tmp2)) return('0');
	
	// this is where the result is stored
	$Output=array();
	
	// remove ending zeroes from decimals and remove point
	$Dec1=isset($Tmp1[2])?rtrim(substr($Tmp1[2],1),'0'):'';
	$Dec2=isset($Tmp2[2])?rtrim(substr($Tmp2[2],1),'0'):'';
	
	// calculate the longest length of decimals
	$DLen=max(strlen($Dec1),strlen($Dec2));
	
	// if $Scale is null, automatically set it to the amount of decimal places for accuracy
	if($Scale==null) $Scale=$DLen;
	
	// remove leading zeroes and reverse the whole numbers, then append padded decimals on the end
	$Num1=strrev(ltrim($Tmp1[1],'0').str_pad($Dec1,$DLen,'0'));
	$Num2=strrev(ltrim($Tmp2[1],'0').str_pad($Dec2,$DLen,'0'));
	
	// calculate the longest length we need to process
	$MLen=max(strlen($Num1),strlen($Num2));
	
	// pad the two numbers so they are of equal length (both equal to $MLen)
	$Num1=str_pad($Num1,$MLen,'0');
	$Num2=str_pad($Num2,$MLen,'0');
	
	// process each digit, keep the ones, carry the tens (remainders)
	for($i=0;$i<$MLen;$i++) {
		$Sum=((int)$Num1{$i}+(int)$Num2{$i});
		if(isset($Output[$i])) $Sum+=$Output[$i];
		$Output[$i]=$Sum%10;
		if($Sum>9) $Output[$i+1]=1;
	}
	
	// convert the array to string and reverse it
	$Output=strrev(implode($Output));
	
	// substring the decimal digits from the result, pad if necessary (if $Scale > amount of actual decimals)
	// next, since actual zero values can cause a problem with the substring values, if so, just simply give '0'
	// next, append the decimal value, if $Scale is defined, and return result
	$Decimal=str_pad(substr($Output,-$DLen,$Scale),$Scale,'0');
	$Output=(($MLen-$DLen<1)?'0':substr($Output,0,-$DLen));
	$Output.=(($Scale>0)?".{$Decimal}":'');
	return($Output);
}

$A="2222222222222.1111111111";
$B="3333333333333.4444444444";

/*echo custom_sub($B,$A);
echo "<br>";
echo custom_add($B,$A);die;*/


?>
