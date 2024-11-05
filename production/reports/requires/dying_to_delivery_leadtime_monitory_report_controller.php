<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php'); 

$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
//$body_part_arr=return_library_array( "select id,body_part_full_name from lib_body_part", "id", "body_part_full_name"  );
$buyer_list=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$floor_arr=return_library_array( "select id,floor_name from  lib_prod_floor",'id','floor_name');
$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );

//--------------------------------------------------------------------------------------------------------------------

//popup for booking number
if($action=="bookingnumbershow")
{
	echo load_html_head_contents("Booking Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var company_id='<? echo $company_name;?>';
		function js_set_value(id)
		{
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:900px;">
				<table width="896" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Company</th>
						<th>Buyer</th>
						<th>Year</th>
						<th>Within Group</th>
						<th>FSO No</th>
						<th>Booking No</th>
						<th>Style Ref.</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
					</thead>
					<tbody>
						<tr>
							<td>
								<?
								echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down('requires/dying_to_delivery_leadtime_monitory_report_controller', this.value, 'load_drop_down_buyer_fso', 'buyer_td_fso' );" );
								?>

							</td>
							<td id="buyer_td_fso">
								<?
								echo create_drop_down( "cbo_buyer_name", 110, "select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 order by short_name","id,buyer_name", 1, "-- Select Buyer --", $cbo_buyer_name, "" );
								?>
							</td>
							<td>
								<?
								echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
								?>
							</td>
							<td>
								<?
								echo create_drop_down( "cbo_within_group", 65, $yes_no,"", 1,"-- All --", "", "",0,"" );
								?>
							</td>
							<td align="center">
								<input type="text" style="width:130px" class="text_boxes" name="txt_fso_no" id="txt_fso_no" />
							</td>

							<td align="center">
								<input type="text" style="width:130px" class="text_boxes" name="txt_booking_no" id="txt_booking_no" />
							</td>
							<td align="center">
								<input type="text" style="width:130px" class="text_boxes" name="txt_style_no" id="txt_style_no" />
							</td>
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('txt_fso_no').value+'**'+document.getElementById('txt_booking_no').value+'**'+document.getElementById('txt_style_no').value+'**'+document.getElementById('cbo_within_group').value+'**'+document.getElementById('cbo_year').value+'**'+document.getElementById('cbo_buyer_name').value, 'bookingnumbershow_search_list_view', 'search_div', 'dying_to_delivery_leadtime_monitory_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
			<div style="margin-top:15px" id="search_div"></div>
		</form>


	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
	$("#cbo_company_name").val(company_id);
</script>
</html>
<?
exit();
}


if($action=="bookingnumbershow_search_list_view")
{
	extract($_REQUEST);
	list($company_name,$txt_fso_no,$txt_booking_no,$txt_style_no,$cbo_within_group,$cbo_year,$cbo_buyer_name)=explode('**',$data);

	if($txt_fso_no)    $search_con=" and a.job_no_prefix_num =$txt_fso_no";
	if($txt_booking_no)       $search_con .=" and a.sales_booking_no like('%$txt_booking_no%')";
	if($txt_style_no)       $search_con .=" and a.style_ref_no like('%$txt_style_no%')";
	if($cbo_within_group)       $search_con .=" and a.within_group =$cbo_within_group";
	if($cbo_buyer_name)       $search_con .=" and a.buyer_id=$cbo_buyer_name";
	if($cbo_year)       $search_con .=" and to_char(a.insert_date,'YYYY')= $cbo_year";
	?>
	<input type="hidden" id="selected_id" name="selected_id" />
	<?
	$sql="SELECT a.id, a.job_no_prefix_num,a.sales_booking_no,a.style_ref_no,a.within_group,(case when a.within_group=1 then b.buyer_id else  a.buyer_id end) as buyer_id from FABRIC_SALES_ORDER_MST a left join wo_booking_mst b on a.sales_booking_no=b.booking_no  where a.company_id=$company_name and a.is_deleted = 0 $search_con group by a.id, a.job_no_prefix_num,a.sales_booking_no,a.style_ref_no,a.within_group,(case when a.within_group=1 then b.buyer_id else  a.buyer_id end)";
	$arr=array(3=>$yes_no,4=>$buyer_arr);
	echo  create_list_view("list_view", "Fso no,Booking no,Style,Within Group,Buyer", "100,100,100,100,170","620","290",0, $sql, "js_set_value", "job_no_prefix_num,job_no_prefix_num", "", 1, "0,0,0,within_group,buyer_id", $arr , "job_no_prefix_num,sales_booking_no,style_ref_no,within_group,buyer_id", "",'','0') ;
	exit();
}


if($action=="batchnumbershow")
{
	echo load_html_head_contents("Batch Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(id)
		{
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:750px;">
				<table width="746" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Batch No</th>
						<th>Batch Date Range</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<input type="text" style="width:150px" class="text_boxes" name="txt_batch_no" id="txt_batch_no" />
							</td>
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:80px" placeholder="From Date"/>
								&nbsp;To&nbsp;
								<input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:80px" placeholder="To Date"/>
							</td>
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_name; ?>'+'**'+document.getElementById('txt_batch_no').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'batchnumbershow_search_list_view', 'search_div', 'dying_to_delivery_leadtime_monitory_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<tr>
								<td headers="5"></td>
							</tr>
							<td colspan="8">
								<? echo load_month_buttons(1); ?>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
			<div style="margin-top:15px" id="search_div"></div>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="batchnumbershow_search_list_view")
{
	extract($_REQUEST);
	list($company_name,$txt_batch_no,$txt_date_from,$txt_date_to)=explode('**',$data);
	$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
	$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');

	if($db_type==2)
	{
		$txt_date_from=change_date_format($txt_date_from,'','',1);
		$txt_date_to=change_date_format($txt_date_to,'','',1);
	}

	if($txt_batch_no!=''){
		$search_con=" and batch_no like('%$txt_batch_no')";
	}

	if($txt_date_from!='' && $txt_date_to!='')
	{
		$search_con .=" and batch_date between '$txt_date_from' and '$txt_date_to'";
	}


	?>
	<input type="hidden" id="selected_id" name="selected_id" />
	<? if($db_type==0) $field_grpby=" GROUP BY batch_no";
	else if($db_type==2) $field_grpby="GROUP BY batch_no,extention_no,id,batch_no,batch_for,booking_no,color_id,batch_weight";
	$sql="SELECT id,batch_no,extention_no,batch_for,booking_no,color_id,batch_weight from pro_batch_create_mst where company_id=$company_name and is_deleted = 0 $search_con $field_grpby ";
	$arr=array(2=>$color_library,4=>$batch_for);
	echo  create_list_view("list_view", "Batch no,Ext No,Color,Booking no, Batch for,Batch weight ", "100,50,100,100,100,170","620","290",0, $sql, "js_set_value", "id,batch_no,extention_no", "", 1, "0,0,color_id,0,batch_for,0", $arr , "batch_no,extention_no,color_id,booking_no,batch_for,batch_weight", "",'','0') ;
	exit();
}


if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 110, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id in($data)    order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/dying_to_delivery_leadtime_monitory_report_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();
}
if ($action=="load_drop_down_floor")
{
	$ex_data = explode("_", $data);
	echo create_drop_down( "cbo_floor_id", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process in(3,4) and company_id in($ex_data[0]) and location_id in($ex_data[1]) order by floor_name","id,floor_name",1, "-- Select Floor --", $selected, "load_drop_down( 'requires/dying_to_delivery_leadtime_monitory_report_controller',this.value, 'load_drop_down_machine', 'machine_td' );",0 );
	exit();
}

if ($action=="load_drop_down_machine")
{


	echo create_drop_down( "cbo_machine_id", 110, "SELECT id,machine_no || '-' || brand as machine_name from lib_machine_name where   floor_id=$data and status_active=1 and is_deleted=0 and is_locked=0 ","id,machine_name", 1, "-- Select Machine --", $selected, "",0 );
	exit();
}

if($action=="load_drop_down_buyer")
{

	echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
	exit();
}
if($action=="load_drop_down_buyer_fso")
{

	echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$all_condition="";

	
	$cbo_company_name 	= str_replace("'","",$cbo_company_name);
	
	$cbo_buyer_name 	= str_replace("'","",$cbo_buyer_name);
	$cbo_year 			= str_replace("'","",$cbo_year);
	 $fso_id 			= str_replace("'","",$booking_number);
	$cbo_base_on_date 		= str_replace("'","",$cbo_base_on_date);
	$txt_days 			= str_replace("'","",$txt_days);
	$txt_date_from 		= str_replace("'","",$txt_date_from);
	$txt_date_to 		= str_replace("'","",$txt_date_to);
	$batch_number 		= str_replace("'","",$batch_number);
	$batch_number_show 	= str_replace("'","",$batch_number_show);
	$batch_extension 	= str_replace("'","",$batch_extension);
	$rpt_type  			= str_replace("'","",$type);
	$fso_no  			= str_replace("'","",$booking_number_show);
	//echo $type_id .'DDD';

	//if($cbo_company_name) $comp_cond=" and c.working_company_id in($cbo_company_name)";else $comp_cond="";
	//if($cbo_company_name) $comp_cond2=" and b.working_company_id in($cbo_company_name)";else $comp_cond="";
	if($cbo_company_name) $prod_comp_cond=" and a.company_id in($cbo_company_name)";else $prod_comp_cond="";
	
	if($cbo_year) $batch_year_cond = " and to_char(c.insert_date,'YYYY')=$cbo_year";
	if($batch_number_show) $batch_cond=" and b.batch_no like '%$batch_number_show'";
	if($batch_extension) $batch_ext_cond=" and b.extention_no='$batch_extension'";
	if($batch_number_show) $batch_cond2=" and c.batch_no like '%$batch_number_show'";
	if($batch_number_show) $batch_cond3=" and a.batch_no like '%$batch_number_show'";
	if($batch_extension) $batch_ext_cond2=" and c.extention_no='$batch_extension'";
	if($fso_no) $sales_cond=" and c.sales_order_no like '%$fso_no'";
	$buyer_cond1=""; $buyer_cond2="";
	if($cbo_buyer_name>0) $buyer_cond1=" and d.po_buyer=$cbo_buyer_name";
	if($cbo_buyer_name>0) $buyer_cond2=" and d.buyer_id=$cbo_buyer_name";

	$batch_id_arr = array();//$batch_prod_date_cond="";
	if($txt_date_from && $txt_date_to)
	{
		if($cbo_base_on_date==1)
		{
			if($db_type==0)
			{
				$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
				$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
			//	$batch_date_cond2="and  c.batch_date >= '$date_from' AND c.batch_date<='$date_to'";
				$prod_date_cond="and  a.production_date between '$date_from' AND '$date_to'";
				$batch_prod_date_cond="and  c.batch_date between '$date_from' AND '$date_to'";
			}
			if($db_type==2)
			{
				$date_from=change_date_format($txt_date_from,'','',1);
				$date_to=change_date_format($txt_date_to,'','',1);
				//$batch_date_cond=" and  b.batch_date >= '$date_from' AND b.batch_date<='$date_to'";
				//$batch_date_cond2=" and  c.batch_date >= '$date_from' AND c.batch_date<='$date_to'";
				$prod_date_cond=" and  a.production_date between '$date_from' AND '$date_to'";
				$batch_prod_date_cond="and  c.batch_date between '$date_from' AND '$date_to'";
			}
		}
		else
		{
			if($db_type==0)
			{
				$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
				$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
			//	$batch_date_cond2="and  c.batch_date >= '$date_from' AND c.batch_date<='$date_to'";
				$prod_date_cond="and  c.batch_date between '$date_from' AND '$date_to'";
			}
			if($db_type==2)
			{
				$date_from=change_date_format($txt_date_from,'','',1);
				$date_to=change_date_format($txt_date_to,'','',1);
				//$batch_date_cond=" and  b.batch_date >= '$date_from' AND b.batch_date<='$date_to'";
				//$batch_date_cond2=" and  c.batch_date >= '$date_from' AND c.batch_date<='$date_to'";
				$prod_date_cond=" and  c.batch_date between '$date_from' AND '$date_to'";
			}
		}
	} 



	

	/*$all_booking_nos="'".implode("','",$all_booking)."'";
	$all_booking_nos_non="'".implode("','",$all_booking_non)."'";
	$all_batch_id="'".implode("','",$all_batch)."'";
*/
//echo $rpt_type.'DDDSS';
if($rpt_type==1)//show 
{ 
	
   $dyeing_sql_unload="SELECT a.id as mst_id,a.service_company,a.floor_id,a.service_source,a.insert_date,a.remarks,a.batch_id, a.result,a.batch_no,a.process_id,a.entry_form,a.start_hours,a.start_minutes,a.production_date as end_date,a.load_unload_id, a.batch_ext_no,a.process_start_date,a.process_end_date,a.end_minutes,a.end_hours,b.batch_qty,b.production_qty,b.prod_id,b.no_of_roll,c.extention_no,c.sales_order_no,c.sales_order_id,c.booking_no,c.batch_date,c.batch_weight,c.booking_without_order,c.insert_date as batch_date_time,c.update_date,c.dur_req_hr,c.dur_req_min,
d.buyer_id as fso_buyer,d.style_ref_no,d.within_group,d.po_buyer from pro_batch_create_mst c,pro_fab_subprocess a,pro_fab_subprocess_dtls b,fabric_sales_order_mst d where  a.id=b.mst_id and c.id=a.batch_id and d.id=c.sales_order_id and c.is_sales=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and a.load_unload_id=2 $sales_cond $batch_year_cond $batch_cond3 $prod_comp_cond $prod_date_cond $buyer_cond1 order by a.id asc";


 //echo $dyeing_sql_unload; die;
	$dyeing_unload_data = sql_select($dyeing_sql_unload);
	
	$all_batch_id="";
	foreach( $dyeing_unload_data as $row )
	{
		if($row[csf("update_date")]!='') $batch_dateTime=$row[csf("update_date")];
		else $batch_dateTime=$row[csf("batch_date_time")];
		
		$batch_hr_min=$row[csf("dur_req_hr")].":".$row[csf("dur_req_min")].':'.'00';
		$batch_insert_time=explode(" ",$batch_dateTime);
		$batch_time_convert=$batch_insert_time[1].' '.$batch_insert_time[2];
		if(($row[csf("dur_req_hr")]=="" || $row[csf("dur_req_hr")]==0) && ($row[csf("dur_req_min")]=="" || $row[csf("dur_req_min")]==0))
		{
		$Batchtimecal=strtotime("$batch_time_convert");
		$Batchtimecal= date('H:i',$Batchtimecal);
		$batch_date_time=$Batchtimecal;	
	//	echo $batch_date_time.'Z'.$row[csf("update_date")].', ';
		}
		else
		{
			$batch_date_time=$batch_hr_min;	
			//echo $batch_date_time.'X';	
		}
		
			
			
		$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_no"]=$row[csf("batch_no")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["end_date"]=$row[csf("end_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["extention_no"]=$row[csf("extention_no")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_hr_min"]=$batch_date_time;//$row[csf("dur_req_hr")].":".$row[csf("dur_req_min")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_qty"]=$row[csf("batch_weight")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["sales_order_no"]=$row[csf("sales_order_no")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["booking_no"]=$row[csf("booking_no")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["entry_form"]=$row[csf("entry_form")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_date"]=$row[csf("batch_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["insert_date"]=$row[csf("insert_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["booking_without_order"]=$row[csf("booking_without_order")];
		
		
		$is_dyeing_done[$row[csf("batch_id")]] = $row[csf("batch_id")];
		$load_unload_time_arr[$row[csf("batch_id")]]["end_date"]=$row[csf("end_date")];
		$load_unload_time_arr[$row[csf("batch_id")]]["process_time"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		
		$all_batch_arr[$row[csf("batch_id")]]=$row[csf("batch_id")];
		if($all_batch_id=="") $all_batch_id=$row[csf("batch_id")];else $all_batch_id.=",".$row[csf("batch_id")];
		
		if($row[csf("within_group")]==2)
		{
			$booking_data_arr2[$row[csf("sales_order_no")]]["buyer_id"]=$buyer_list[$row[csf("fso_buyer")]];
		}
		else
		{
			$booking_data_arr[$row[csf("booking_no")]]["buyer_id"]=$buyer_list[$row[csf("po_buyer")]];
		}
		$booking_data_arr2[$row[csf("sales_order_no")]]["style_ref_no"]=$row[csf("style_ref_no")];
		$booking_data_arr[$row[csf("booking_no")]]["job_no"]=$row[csf("job_no")];
		$booking_data_arr2[$row[csf("sales_order_no")]]["within_group"]=$row[csf("within_group")];
		
		$batch_wise_dying_arr[$row[csf("batch_id")]]["end_date"]=$row[csf("end_date")];
		$all_to_batch_arr[$row[csf("batch_id")]]=$row[csf("batch_id")];
		
	}
	unset($dyeing_unload_data);
	$batch_ids=count($all_batch_arr);
	if($db_type==2 && $batch_ids>1000)
	{
		$batch_cond_for=" and (";
		$batIdsArr=array_chunk($all_batch_arr,999);
		foreach($batIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$batch_cond_for.=" a.batch_id in($ids) or";
		}
		$batch_cond_for=chop($batch_cond_for,'or ');
		$batch_cond_for.=")";
	}
	else
	{
		$batch_cond_for=" and a.batch_id in(".implode(",",$all_batch_arr).")";
	}
	
	
	
	//$all_batch_ids=explode(",",$all_batch_id);
	
$load_unload_sql="SELECT a.id as mst_id,a.result,a.service_company,a.floor_id,a.service_source,a.insert_date,a.batch_id,a.batch_no,a.process_id,a.entry_form,a.start_hours,a.start_minutes,a.production_date as end_date,a.load_unload_id, a.batch_ext_no,a.process_start_date,a.process_end_date,a.end_minutes,a.end_hours,b.production_qty,b.batch_qty,b.prod_id,b.no_of_roll,c.sales_order_no,c.sales_order_id,c.booking_no,c.extention_no,c.batch_date,c.insert_date,c.booking_without_order,c.dur_req_hr,c.dur_req_min from pro_batch_create_mst c,pro_fab_subprocess a,pro_fab_subprocess_dtls b where  a.id=b.mst_id and c.id=a.batch_id and a.load_unload_id=1 and a.entry_form=35 and c.is_sales=1 and a.status_active=1 and b.status_active=1  and c.status_active=1  $sales_cond $batch_year_cond $batch_cond3 $prod_comp_cond $batch_cond_for order by a.id,a.batch_id desc";
	$dying_result_data = sql_select($load_unload_sql);
	foreach($dying_result_data as $row )
	{
		$load_time_arr[$row[csf("batch_id")]]["end_date"]=$row[csf("process_end_date")];
		//$load_time_arr[$row[csf("batch_id")]]["start_date"]=$row[csf("end_date")];
		$load_time_arr[$row[csf("batch_id")]]["end_time"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$load_time_arr[$row[csf("batch_id")]]["start_time"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
	}
	unset($dying_result_data);
	
	
	
	 $prod_sql="SELECT a.id as mst_id,a.result,c.insert_date as batch_date_time,c.update_date,a.service_company,a.floor_id,a.service_source,a.insert_date,a.remarks,a.batch_id,a.previous_process, a.result,a.batch_no,a.process_id,a.remarks,a.entry_form,a.start_hours,a.start_minutes,a.production_date as end_date,a.load_unload_id, a.batch_ext_no,a.process_start_date,a.process_end_date,a.end_minutes,a.end_hours,b.production_qty,b.batch_qty,b.prod_id,b.no_of_roll,c.sales_order_no,c.sales_order_id,c.booking_no,c.extention_no,c.batch_date,c.insert_date,c.booking_without_order,c.dur_req_hr,c.dur_req_min from pro_batch_create_mst c,pro_fab_subprocess a,pro_fab_subprocess_dtls b where  a.id=b.mst_id and c.id=a.batch_id and a.entry_form!=35 and c.is_sales=1 and a.status_active=1 and b.status_active=1  and c.status_active=1  $sales_cond $batch_year_cond $batch_cond3 $prod_comp_cond $batch_cond_for order by a.id,a.batch_id desc";

	//echo $prod_sql; die;
	$result_data = sql_select($prod_sql);
	$process_brush_arr_check=array(68);
	$process_peach_arr_check=array(67);
	foreach($result_data as $row )
	{
		//$is_dyeing_done[$row[csf("batch_id")]] = $row[csf("batch_id")];
		//$load_unload_time_arr[$row[csf("batch_id")]]["end_date"]=$row[csf("end_date")];
		
		
		$all_sales_id_arr[$row[csf("sales_order_id")]]=$row[csf("sales_order_id")];
		if($all_batch_id=="") $all_batch_id=$row[csf("batch_id")];else $all_batch_id.=",".$row[csf("batch_id")];
		//$process_ids=explode(",",$row[csf("process_id")]);
		if($row[csf("entry_form")]==32)//HeatSet
		{
		$batch_wise_dying_arr[$row[csf("batch_id")]]["heatset_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["heatset_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["heatset_start_date"]=$row[csf("process_start_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["heatset_end_date"]=$row[csf("end_date")];
		}
		else if($row[csf("entry_form")]==30 && $row[csf("previous_process")]==0)//Sliting //
		{
		$batch_wise_dying_arr[$row[csf("batch_id")]]["sliting_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["sliting_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["sliting_start_date"]=$row[csf("process_start_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["sliting_end_date"]=$row[csf("end_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["sliting_result"]=$row[csf("result")];
		}
		else if($row[csf("entry_form")]==48 && $row[csf("previous_process")]==0)//Stentering
		{
		$batch_wise_dying_arr[$row[csf("batch_id")]]["stenter_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["stenter_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["stenter_start_date"]=$row[csf("process_start_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["stenter_end_date"]=$row[csf("end_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["stenter_result"]=$row[csf("result")];
		}
		else if($row[csf("entry_form")]==31 && $row[csf("previous_process")]==0)//Drying
		{
			//echo  $row[csf("previous_process")].'f';
		$batch_wise_dying_arr[$row[csf("batch_id")]]["dry_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["dry_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["dry_start_date"]=$row[csf("process_start_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["dry_end_date"]=$row[csf("end_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["dry_result"]=$row[csf("result")];
		}
		else if($row[csf("entry_form")]==33 && $row[csf("previous_process")]==0)//Compacting
		{
		$batch_wise_dying_arr[$row[csf("batch_id")]]["comp_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["comp_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["comp_start_date"]=$row[csf("process_start_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["comp_end_date"]=$row[csf("end_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["comp_result"]=$row[csf("result")];
		}
		else if($row[csf("entry_form")]==48 && $row[csf("result")]>0 && $row[csf("previous_process")]>0)//Prod Type Stenter 
		{
		$batch_wise_dying_arr[$row[csf("batch_id")]]["type_stenter_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["type_stenter_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["type_stenter_start_date"]=$row[csf("process_start_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["type_stenter_end_date"]=$row[csf("end_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["type_stenter_result"]=$row[csf("result")];
		//echo $row[csf("result")].'dXXX';
		}
		else if($row[csf("entry_form")]==33 && $row[csf("result")]>0 && $row[csf("previous_process")]>0)//Prod Type Compacting 
		{
		$batch_wise_dying_arr[$row[csf("batch_id")]]["type_comp_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["type_comp_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["type_comp_start_date"]=$row[csf("process_start_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["type_comp_end_date"]=$row[csf("end_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["type_comp_result"]=$row[csf("result")];
		}
		else if($row[csf("entry_form")]==31 && $row[csf("result")]>0 && $row[csf("previous_process")]>0)//Prod Type Drying 
		{
		$batch_wise_dying_arr[$row[csf("batch_id")]]["type_dry_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["type_dry_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["type_dry_start_date"]=$row[csf("process_start_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["type_dry_end_date"]=$row[csf("end_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["type_dry_result"]=$row[csf("result")];
		}
		
		else if($row[csf("entry_form")]==34 && $row[csf("result")]>0)//SpecilaFinish
		{
			
			if(in_array($row[csf("process_id")],$process_brush_arr_check))//Prod Type Brush 
			{
				
				//echo $row[csf("process_id")]."G";
			$batch_wise_dying_arr[$row[csf("batch_id")]]["type_brush_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
			$batch_wise_dying_arr[$row[csf("batch_id")]]["type_brush_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
			$batch_wise_dying_arr[$row[csf("batch_id")]]["type_brush_start_date"]=$row[csf("process_start_date")];
			$batch_wise_dying_arr[$row[csf("batch_id")]]["type_brush_end_date"]=$row[csf("end_date")];
			$batch_wise_dying_arr[$row[csf("batch_id")]]["type_brush_result"]=$row[csf("result")];
			}
			else if(in_array($row[csf("process_id")],$process_peach_arr_check))//Prod Type Peach 
			{
				
				//echo $row[csf("process_id")]."G";
			$batch_wise_dying_arr[$row[csf("batch_id")]]["type_peach_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
			$batch_wise_dying_arr[$row[csf("batch_id")]]["type_peach_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
			$batch_wise_dying_arr[$row[csf("batch_id")]]["type_peach_start_date"]=$row[csf("process_start_date")];
			$batch_wise_dying_arr[$row[csf("batch_id")]]["type_peach_end_date"]=$row[csf("end_date")];
			$batch_wise_dying_arr[$row[csf("batch_id")]]["type_peach_result"]=$row[csf("result")];
			}
		}
		
			if($row[csf("update_date")]!='') $batch_dateTime=$row[csf("update_date")];
			else $batch_dateTime=$row[csf("batch_date_time")];
			$batch_date_time=$batch_dateTime;
			$batch_hr_min=$row[csf("dur_req_hr")].":".$row[csf("dur_req_min")].':'.'00';
			$batch_insert_time=explode(" ",$batch_date_time);
			$batch_time_convert=$batch_insert_time[1].' '.$batch_insert_time[2];
			if(($row[csf("dur_req_hr")]=="" || $row[csf("dur_req_hr")]==0) && ($row[csf("dur_req_min")]=="" || $row[csf("dur_req_min")]==0))
			{
			$Batchtimecal=strtotime("$batch_time_convert");
			$Batchtimecal= date('H:i',$Batchtimecal);
			$batch_date_time=$Batchtimecal;	
				//echo $batch_date_time.'Y'.$row[csf("update_date")].', ';
			}
			else
			{
				$batch_date_time=$batch_hr_min;		
			}
		//echo $Batchtimecal.'d';					
		
		
		$batch_wise_dying_arr[$row[csf("batch_id")]]["result"]=$row[csf("result")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_hr_min"]=$batch_date_time;//$row[csf("dur_req_hr")].":".$row[csf("dur_req_min")];
		//$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_min"]=$row[csf("dur_req_min")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_no"]=$row[csf("batch_no")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["end_date"]=$row[csf("end_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["extention_no"]=$row[csf("extention_no")];
		//$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_qty"]=$row[csf("batch_qty")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["sales_order_no"]=$row[csf("sales_order_no")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["booking_no"]=$row[csf("booking_no")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["entry_form"]=$row[csf("entry_form")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_date"]=$row[csf("batch_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["insert_date"]=$row[csf("insert_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["booking_without_order"]=$row[csf("booking_without_order")];
		
		$all_to_batch_arr[$row[csf("batch_id")]]=$row[csf("batch_id")];
		
		
	}
	unset($result_data);
	$batchids=count($all_to_batch_arr);
	if($db_type==2 && $batchids>1000)
	{
		$batch_cond_for2=" and (";
		$batIdsArr=array_chunk($all_to_batch_arr,999);
		foreach($batIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$batch_cond_for2.=" b.batch_id in($ids) or";
		}
		$batch_cond_for2=chop($batch_cond_for2,'or ');
		$batch_cond_for2.=")";
	}
	else
	{
		$batch_cond_for2=" and b.batch_id in(".implode(",",$all_to_batch_arr).")";
	}
	
	
//echo "DDDDDD";die;
	/*echo "<pre>";
	echo $batch_cond_for;
	echo "</pre>";*/
	

	/*$booking_data_arr=array();
	$non_booking_sql="SELECT buyer_id,booking_no,booking_type from wo_non_ord_samp_booking_mst where booking_no in($all_booking_nos_non) and  status_active=1 and booking_type = 4 " ;
	foreach(sql_select($non_booking_sql) as $row )
	{
		if($row[csf("booking_type")]==4)
		{
			$booking_data_arr[$row[csf("booking_no")]]["type"]="Sample Without Order";
			$booking_data_arr[$row[csf("booking_no")]]["buyer_id"]=$buyer_list[$row[csf("buyer_id")]];
		}
	}*/
	$sales_ids=count($all_sales_id_arr);
	if($db_type==2 && $sales_ids>1000)
	{
		$sales_cond_for=" and (";
		$salesIdsArr=array_chunk($all_batch_arr,999);
		foreach($salesIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$sales_cond_for_in.=" c.id in($ids) or";
		}
		$sales_cond_for_in=chop($sales_cond_for_in,'or ');
		$sales_cond_for_in.=")";
	}
	else
	{
		$sales_cond_for_in=" and c.id in(".implode(",",$all_sales_id_arr).")";
	}
	$fin_fab_sql= sql_select("select c.insert_date,c.update_date,c.receive_date, b.batch_id,b.uom,b.receive_qnty from  pro_finish_fabric_rcv_dtls b,inv_receive_master c where  b.mst_id=c.id and c.entry_form in(7,37) and c.status_active=1 and c.booking_without_order=0 and c.entry_form in(7,37)  and b.status_active=1 and b.is_deleted=0 $batch_cond_for2 order by b.batch_id");
	//echo "select c.insert_date,c.update_date,c.receive_date, b.batch_id,b.uom,b.receive_qnty from  pro_finish_fabric_rcv_dtls b,inv_receive_master c where  b.mst_id=c.id and c.entry_form in(7,37) and c.status_active=1 and c.booking_without_order=0 and c.entry_form in(7,37)  and b.status_active=1 and b.is_deleted=0 $batch_cond_for2 order by b.batch_id";
	
	foreach($fin_fab_sql as $row)
	{
		if($row[csf("update_date")]!='') $fin_dateTime=$row[csf("update_date")];
		else $fin_dateTime=$row[csf("insert_date")];
		$fin_fab_arr[$row[csf("batch_id")]]["uom"]=$row[csf("uom")];
		$fin_fab_arr[$row[csf("batch_id")]]["receive_date"]=$row[csf("receive_date")];
		$fin_fab_arr[$row[csf("batch_id")]]["insert_date"]=$fin_dateTime;
		//echo $fin_dateTime.', ';
		
		$fin_fab_qty_arr[$row[csf("batch_id")]][$row[csf("uom")]]["receive_qnty"]+=$row[csf("receive_qnty")];
	}
	unset($fin_fab_sql);
	$fin_delivery_sql= sql_select("select c.insert_date,c.update_date,c.delevery_date, b.batch_id,b.uom,b.current_delivery from  pro_grey_prod_delivery_dtls b,pro_grey_prod_delivery_mst c where  b.mst_id=c.id  and c.status_active=1 and c.entry_form in(54)  and b.status_active=1 and b.is_deleted=0 $batch_cond_for2 order by b.batch_id");
	//echo "select c.insert_date,c.delevery_date, b.batch_id,b.uom,b.current_delivery from  pro_grey_prod_delivery_dtls b,pro_grey_prod_delivery_mst c where  b.mst_id=c.id and c.status_active=1 and c.entry_form in(54)  and b.status_active=1 and b.is_deleted=0 $batch_cond_for2 order by b.batch_id";
	
	foreach($fin_delivery_sql as $row)
	{
		if($row[csf("update_date")]!='') $del_fin_dateTime=$row[csf("update_date")];
		else $del_fin_dateTime=$row[csf("insert_date")];
		$fin_fab_deli_arr[$row[csf("batch_id")]]["uom"]=$row[csf("uom")];
		$fin_fab_deli_arr[$row[csf("batch_id")]]["delevery_date"]=$row[csf("delevery_date")];
		$fin_fab_deli_arr[$row[csf("batch_id")]]["insert_date"]=$del_fin_dateTime;
		$fin_fab_deli_qty_arr[$row[csf("batch_id")]][$row[csf("uom")]]["current_delivery"]+=$row[csf("current_delivery")];
	}
	unset($fin_delivery_sql);
	
	//-----------------------===========For SubCon============Area ----------------------------
 $subcon_dyeing_sql_unload="SELECT a.id as mst_id,a.service_company,a.floor_id,a.service_source,a.insert_date,a.remarks,a.batch_id, a.result,a.batch_no,a.process_id,a.entry_form,a.start_hours,a.start_minutes,a.production_date as end_date,a.load_unload_id, a.batch_ext_no,a.process_start_date,a.process_end_date,a.end_minutes,a.end_hours,c.booking_no,c.batch_weight,c.update_date,c.insert_date as batch_date_time,c.batch_date,c.booking_without_order,c.extention_no,c.dur_req_hr,c.dur_req_min,d.cust_style_ref,d.order_no,e.party_id as buyer_id from pro_batch_create_mst c,pro_batch_create_dtls b,pro_fab_subprocess a,subcon_ord_dtls d,subcon_ord_mst e where  c.id=b.mst_id and c.id=a.batch_id and d.id=b.po_id and d.job_no_mst=e.subcon_job and c.entry_form=36 and a.status_active=1 and c.status_active=1  and a.load_unload_id=2  $batch_year_cond $batch_cond3 $prod_comp_cond $prod_date_cond $buyer_cond1 order by a.id asc";
	$subcon_dyeing_unload_data = sql_select($subcon_dyeing_sql_unload);//For SubCon
	$sub_all_batch_id="";
	foreach( $subcon_dyeing_unload_data as $row )
	{
		//$is_dyeing_done[$row[csf("batch_id")]] = $row[csf("batch_id")];
		if($row[csf("update_date")]!='') $batch_dateTime=$row[csf("update_date")];
			else $batch_dateTime=$row[csf("batch_date_time")];
			$batch_date_time=$batch_dateTime;
			$batch_hr_min=$row[csf("dur_req_hr")].":".$row[csf("dur_req_min")].':'.'00';
			$batch_insert_time=explode(" ",$batch_date_time);
			$batch_time_convert=$batch_insert_time[1].' '.$batch_insert_time[2];
			if(($row[csf("dur_req_hr")]=="" || $row[csf("dur_req_hr")]==0) && ($row[csf("dur_req_min")]=="" || $row[csf("dur_req_min")]==0))
			{
			$Batchtimecal=strtotime("$batch_time_convert");
			$Batchtimecal= date('H:i',$Batchtimecal);
			$batch_date_time=$Batchtimecal;	
			}
			else
			{
				$batch_date_time=$batch_hr_min;		
			}
			
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["batch_hr_min"]=$batch_date_time;//$row[csf("dur_req_hr")].":".$row[csf("dur_req_min")];
		//$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_min"]=$row[csf("dur_req_min")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["batch_no"]=$row[csf("batch_no")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["end_date"]=$row[csf("end_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["extention_no"]=$row[csf("extention_no")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["batch_qty"]=$row[csf("batch_weight")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["sales_order_no"]=$row[csf("sales_order_no")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["booking_no"]=$row[csf("booking_no")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["entry_form"]=$row[csf("entry_form")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["batch_date"]=$row[csf("batch_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["insert_date"]=$row[csf("insert_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["booking_without_order"]=$row[csf("booking_without_order")];
		
		$sub_load_unload_time_arr[$row[csf("batch_id")]]["end_date"]=$row[csf("end_date")];
		$sub_load_unload_time_arr[$row[csf("batch_id")]]["process_time"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$sub_all_batch_arr[$row[csf("batch_id")]]=$row[csf("batch_id")];
		if($sub_all_batch_id=="") $sub_all_batch_id=$row[csf("batch_id")];else $sub_all_batch_id.=",".$row[csf("batch_id")];
		
		$booking_data_arr[$row[csf("batch_id")]]["buyer_id"]=$buyer_list[$row[csf("buyer_id")]];
		$booking_data_arr2[$row[csf("batch_id")]]["style_ref_no"]=$row[csf("cust_style_ref")];
		$booking_data_arr2[$row[csf("batch_id")]]["order_no"]=$row[csf("order_no")];
		//w$booking_data_arr[$row[csf("booking_no")]]["job_no"]=$row[csf("job_no")];
		//$booking_data_arr2[$row[csf("sales_order_no")]]["within_group"]=$row[csf("within_group")];
	}
	unset($subcon_dyeing_unload_data);
	$sub_batch_ids=count($sub_all_batch_arr);
	if($db_type==2 && $sub_batch_ids>1000)
	{
		$sub_batch_cond_for=" and (";
		$batIdsArr=array_chunk($sub_all_batch_arr,999);
		foreach($batIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$sub_batch_cond_for.=" a.batch_id in($ids) or";
		}
		$sub_batch_cond_for=chop($sub_batch_cond_for,'or ');
		$sub_batch_cond_for.=")";
	}
	else
	{
		$sub_batch_cond_for=" and a.batch_id in(".implode(",",$sub_all_batch_arr).")";
	}
	 $sub_load_unload_sql="SELECT a.id as mst_id,a.result,a.service_company,a.floor_id,a.service_source,a.insert_date,a.batch_id,a.batch_no,a.process_id,a.entry_form,a.start_hours,a.start_minutes,a.production_date as end_date,a.load_unload_id, a.batch_ext_no,a.process_start_date,a.process_end_date,a.end_minutes,a.end_hours,c.sales_order_no,c.sales_order_id,c.booking_no,c.extention_no,c.batch_date,c.insert_date,c.booking_without_order,c.dur_req_hr,c.dur_req_min from pro_batch_create_mst c,pro_fab_subprocess a where  c.id=a.batch_id and a.load_unload_id=1 and a.entry_form=38 and a.status_active=1 and c.status_active=1  $batch_year_cond $batch_cond3 $prod_comp_cond $sub_batch_cond_for order by a.id,a.batch_id desc";
	$sub_dying_result_data = sql_select($sub_load_unload_sql);//For SubCon
	foreach($sub_dying_result_data as $row )
	{
		$sub_load_time_arr[$row[csf("batch_id")]]["end_date"]=$row[csf("process_end_date")];
		//$load_time_arr[$row[csf("batch_id")]]["start_date"]=$row[csf("end_date")];
		$sub_load_time_arr[$row[csf("batch_id")]]["end_time"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$sub_load_time_arr[$row[csf("batch_id")]]["start_time"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
	}
	unset($sub_dying_result_data);
	
	 $sub_prod_sql="SELECT a.id as mst_id,a.result,a.previous_process,a.service_company,a.floor_id,a.service_source,a.insert_date,a.remarks,a.batch_id, a.result,a.batch_no,a.process_id,a.remarks,a.entry_form,a.start_hours,a.start_minutes,a.production_date as end_date,a.load_unload_id, a.batch_ext_no,a.process_start_date,a.process_end_date,a.end_minutes,a.end_hours,b.production_qty,b.batch_qty,b.prod_id,b.no_of_roll,c.sales_order_no,c.sales_order_id,c.booking_no,c.extention_no,c.batch_date,c.insert_date as batch_date_time,c.update_date,c.booking_without_order,c.dur_req_hr,c.dur_req_min from pro_batch_create_mst c,pro_fab_subprocess a,pro_fab_subprocess_dtls b where  a.id=b.mst_id and c.id=a.batch_id and a.entry_form not in(35,38)  and a.status_active=1 and b.status_active=1  and c.status_active=1  $batch_year_cond $batch_cond3 $prod_comp_cond $sub_batch_cond_for order by a.id,a.batch_id desc";
	$sub_result_data = sql_select($sub_prod_sql);//For SubCon
	$process_brush_arr_check=array(68); 
	$process_peach_arr_check=array(67);
	foreach($sub_result_data as $row )
	{
		//$is_dyeing_done[$row[csf("batch_id")]] = $row[csf("batch_id")];
	//	$sub_load_unload_time_arr[$row[csf("batch_id")]]["end_date"]=$row[csf("end_date")];
		if($sub_all_batch_id=="") $sub_all_batch_id=$row[csf("batch_id")];else $sub_all_batch_id.=",".$row[csf("batch_id")];
		if($row[csf("entry_form")]==32)//HeatSet
		{
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["heatset_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["heatset_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["heatset_start_date"]=$row[csf("process_start_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["heatset_end_date"]=$row[csf("end_date")];
		}
		elseif($row[csf("entry_form")]==30 && $row[csf("previous_process")]==0)//Sliting
		{
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["sliting_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["sliting_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["sliting_start_date"]=$row[csf("process_start_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["sliting_end_date"]=$row[csf("end_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["sliting_result"]=$row[csf("result")];
		}
		elseif($row[csf("entry_form")]==48 && $row[csf("previous_process")]==0)//Stentering
		{
			//echo $row[csf("end_hours")].'='.$row[csf("end_minutes")].', ';
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["stenter_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["stenter_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["stenter_start_date"]=$row[csf("process_start_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["stenter_end_date"]=$row[csf("end_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["stenter_result"]=$row[csf("result")];
		}
		elseif($row[csf("entry_form")]==31 && $row[csf("previous_process")]==0)//Drying
		{
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["dry_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["dry_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["dry_start_date"]=$row[csf("process_start_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["dry_end_date"]=$row[csf("end_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["dry_result"]=$row[csf("result")];
		}
		elseif($row[csf("entry_form")]==33 && $row[csf("previous_process")]==0)//Compacting
		{
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["comp_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["comp_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["comp_start_date"]=$row[csf("process_start_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["comp_end_date"]=$row[csf("end_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["comp_result"]=$row[csf("result")];
		}
		elseif($row[csf("entry_form")]==48 && $row[csf("result")]>0 && $row[csf("previous_process")]>0)//Prod Type Stenter 
		{
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["type_stenter_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["type_stenter_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["type_stenter_start_date"]=$row[csf("process_start_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["type_stenter_end_date"]=$row[csf("end_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["type_stenter_result"]=$row[csf("result")];
		//echo $row[csf("result")].'dXXX';
		}
		else if($row[csf("entry_form")]==33 && $row[csf("result")]>0 && $row[csf("previous_process")]>0)//Prod Type Compacting 
		{
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["type_comp_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["type_comp_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["type_comp_start_date"]=$row[csf("process_start_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["type_comp_end_date"]=$row[csf("end_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["type_comp_result"]=$row[csf("result")];
		}
		else if($row[csf("entry_form")]==31 && $row[csf("result")]>0 && $row[csf("previous_process")]>0)//Prod Type Drying 
		{
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["type_dry_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["type_dry_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["type_dry_start_date"]=$row[csf("process_start_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["type_dry_end_date"]=$row[csf("end_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["type_dry_result"]=$row[csf("result")];
		}
		elseif($row[csf("entry_form")]==34 && $row[csf("result")]>0)//SpecialFinish
		{
		//$process_ids=explode(",",$row[csf("process_id")]);
			if(in_array($row[csf("process_id")],$process_brush_arr_check))//Prod Type Brush  
			{
				//echo $row[csf("process_id")]."T";
			$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["type_brush_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
			$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["type_brush_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
			$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["type_brush_start_date"]=$row[csf("process_start_date")];
			$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["type_brush_end_date"]=$row[csf("end_date")];
			$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["type_brush_result"]=$row[csf("result")];
			}
			if(in_array($row[csf("process_id")],$process_peach_arr_check))//Peach
			{
				//echo $row[csf("process_id")]."T";
			$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["type_peach_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
			$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["type_peach_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
			$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["type_peach_start_date"]=$row[csf("process_start_date")];
			$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["type_peach_end_date"]=$row[csf("end_date")];
			$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["type_peach_result"]=$row[csf("result")];
			}
		
		}
		
		if($row[csf("update_date")]!='') $sub_batch_date_time=$row[csf("update_date")];
		else $sub_batch_date_time=$row[csf("batch_date_time")];
			
		//$sub_batch_date_time=$row[csf("batch_date_time")];
		$batch_hr_min=$row[csf("dur_req_hr")].":".$row[csf("dur_req_min")].':'.'00';
			
		$batch_insert_time=explode(" ",$sub_batch_date_time);
		$batch_time_convert=$batch_insert_time[1].' '.$batch_insert_time[2];
		if(($row[csf("dur_req_hr")]=="" || $row[csf("dur_req_hr")]==0) && ($row[csf("dur_req_min")]=="" || $row[csf("dur_req_min")]==0))
		{
		$Batchtimecal=strtotime("$batch_time_convert");
		$Batchtimecal= date('H:i',$Batchtimecal);
		$batch_date_time=$Batchtimecal;	
		}
		else
		{
			$batch_date_time=$batch_hr_min;		
		}
			
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["result"]=$row[csf("result")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["batch_hr_min"]=$batch_date_time;//$row[csf("dur_req_hr")].":".$row[csf("dur_req_min")];
		//$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_min"]=$row[csf("dur_req_min")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["batch_no"]=$row[csf("batch_no")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["end_date"]=$row[csf("end_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["extention_no"]=$row[csf("extention_no")];
		//$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["batch_qty"]=$row[csf("batch_qty")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["sales_order_no"]=$row[csf("sales_order_no")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["booking_no"]=$row[csf("booking_no")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["entry_form"]=$row[csf("entry_form")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["batch_date"]=$row[csf("batch_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["insert_date"]=$row[csf("insert_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["booking_without_order"]=$row[csf("booking_without_order")];
		
		$sub_all_to_batch_arr[$row[csf("batch_id")]]=$row[csf("batch_id")];
		
		
	}
	unset($sub_result_data);
	$sub_batchids=count($sub_all_to_batch_arr);
	if($db_type==2 && $sub_batchids>1000)
	{
		$sub_batch_cond_for2=" and (";
		$batIdsArr=array_chunk($sub_all_to_batch_arr,999);
		foreach($batIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$sub_batch_cond_for2.=" b.batch_id in($ids) or";
		}
		$sub_batch_cond_for2=chop($sub_batch_cond_for2,'or ');
		$sub_batch_cond_for2.=")";
	}
	else
	{
		$sub_batch_cond_for2=" and b.batch_id in(".implode(",",$sub_all_to_batch_arr).")";
	}
	
	$sub_fin_fab_sql= sql_select("select c.insert_date,c.product_date, b.batch_id,b.product_qnty,d.order_uom from  subcon_production_mst c,subcon_production_dtls b,subcon_ord_dtls d where c.id=b.mst_id and b.order_id=d.id and c.status_active=1  and c.entry_form in(292)  and b.status_active=1 and b.is_deleted=0  order by b.batch_id");
	//echo "select c.insert_date,c.product_date, b.batch_id,b.product_qnty,d.order_uom from  subcon_production_mst c,subcon_production_dtls b,subcon_ord_dtls d where c.id=b.mst_id and b.order_id=d.id and c.status_active=1  and c.entry_form in(292)  and b.status_active=1 and b.is_deleted=0  order by b.batch_id";
//$sub_batch_cond_for2 order_id
	//echo "select c.insert_date,c.product_date, b.batch_id,b.product_qnty from  subcon_production_mst c,subcon_production_dtls b where c.id=b.mst_id  and c.status_active=1  and c.entry_form in(292)  and b.status_active=1 and b.is_deleted=0 $sub_batch_cond_for2 order by b.batch_id";
	foreach($sub_fin_fab_sql as $row)
	{
		//$sub_fin_fab_arr[$row[csf("batch_id")]]["uom"]=$row[csf("uom")];
		$sub_fin_fab_arr[$row[csf("batch_id")]]["receive_date"]=$row[csf("product_date")];
		$sub_fin_fab_arr[$row[csf("batch_id")]]["insert_date"]=$row[csf("insert_date")];
		$sub_fin_fab_qty_arr[$row[csf("batch_id")]][$row[csf("order_uom")]]["receive_qnty"]+=$row[csf("product_qnty")];
	}
	//print_r($sub_fin_fab_qty_arr); 
	unset($sub_fin_fab_sql);
	$sub_fin_delivery_sql= sql_select("select c.insert_date,c.delivery_date, b.batch_id,b.delivery_qty,d.order_uom from  subcon_delivery_dtls b,subcon_delivery_mst c,subcon_ord_dtls d  where  b.mst_id=c.id  and b.order_id=d.id and d.status_active=1 and c.status_active=1 and c.process_id in(4)  and b.status_active=1 and b.is_deleted=0  order by b.batch_id"); //$sub_batch_cond_for2
//echo "select c.insert_date,c.delivery_date, b.batch_id,b.delivery_qty,d.order_uom from  subcon_delivery_dtls b,subcon_delivery_mst c,subcon_ord_dtls d  where  b.mst_id=c.id  and b.order_id=d.id and d.status_active=1 and c.status_active=1 and c.process_id in(4)  and b.status_active=1 and b.is_deleted=0 $sub_batch_cond_for2 order by b.batch_id";
	
	foreach($sub_fin_delivery_sql as $row)
	{
		$sub_fin_fab_deli_arr[$row[csf("batch_id")]]["uom"]=$row[csf("uom")];
		$sub_fin_fab_deli_arr[$row[csf("batch_id")]]["delevery_date"]=$row[csf("delivery_date")];
		$sub_fin_fab_deli_arr[$row[csf("batch_id")]]["insert_date"]=$row[csf("insert_date")];
		$sub_fin_fab_deli_qty_arr[$row[csf("batch_id")]][$row[csf("order_uom")]]["current_delivery"]+=$row[csf("delivery_qty")];
	}
	unset($sub_fin_delivery_sql);
	//print_r($fin_fab_deli_qty_arr);
	//echo $rpt_type.'DDDSS';
		ob_start();
    if($rpt_type==1) $width=8250;else $width=500;
			?>
			<div style="width:<? echo $width;?>px;">
				<style type="text/css">
					.alignment_css
					{
						word-break: break-all;
						word-wrap: break-word;
					}
				</style>
			<!--	<fieldset style="width:<?// echo $width;?>px;">-->
					
						<table class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
							<caption><strong><? echo  $company_library[$cbo_company_name]; ?><br><? echo $report_title;?>	<br>
								<? echo  change_date_format($txt_date_from).' To '.change_date_format($txt_date_to); ?></strong>
							</caption>
							<thead>
								<tr>
									<th colspan="9">Batch Information</th>
									<th colspan="2">Batch</th>
									<th colspan="6">Heat Setting</th>
									<th colspan="6">Dyeing</th>
									<th colspan="7">Slitting</th>
									<th colspan="7">Stentering </th>
									<th colspan="7" style="word-break:break-all">Production type wise Stentering </th>
									<th colspan="7">Drying</th>
									<th colspan="7" style="word-break:break-all">Production type wise  Drying </th>
									<th colspan="7">Compacting</th>
									<th colspan="7" style="word-break:break-all">Production type wise Compacting</th>
									<th colspan="7">Brush</th>
									<th colspan="7">Peach</th>
									<th colspan="6">Finish Fabric Production</th>
									<th colspan="6">Finish Fabric Delivery</th>
									<th colspan="4">Result</th>
								</tr>
								
								<tr>
									<th width="20" class="">SL</th>
									<th width="80" class="">Date</th>
									<th width="80" class="">Buyer</th>
									<th width="80" class="">Style Ref.</th>
									<th width="100" class="">FSO No</th>
									<th width="100" style="word-break:break-all" class="">Fabric Booking No.</th>
									<th width="80" class="">Batch numbers</th>
									<th width="80" class="">Ext. No</th>
									
									<th width="80" class="">Batch Qty</th>
									<th width="80" style="word-break:break-all" class="">Batch Creation Date</th>
									<th width="80" style="word-break:break-all"  class="">Batch Creation Time</th>
									<th width="80" style="word-break:break-all" class="">Heat Setting Start Date</th>
									<th width="80" style="word-break:break-all" class="">Heat Setting Start Time</th>
									<th width="80" style="word-break:break-all" class="">Heat Setting End Date</th>
									<th width="80" style="word-break:break-all" class="">Heat Setting End Time</th>
									<th width="80" style="word-break:break-all" class="">Batch to Heat Setting Execution Days</th>
									<th width="80" style="word-break:break-all" class="">Batch to Heat Setting Execution Time</th>
									<th width="80" style="word-break:break-all" class="">Dyeing loading Date</th>
									<th width="80" style="word-break:break-all" class="">Dyeing Loading Time</th>
									<th width="80" style="word-break:break-all" class="">Dyeing Unloading Date</th>
									<th width="80" style="word-break:break-all" class="">Dyeing Unloading Time</th>
									<th width="80" style="word-break:break-all" class="">Batch to Dyeing Execution Days</th>
									<th width="80" style="word-break:break-all" class="">Batch to Dyeing Execution Time</th>
									<th width="80" class="">Slitting Start Date</th>
									<th width="80" class="">Slitting Start Time</th>
									<th width="80" class="">Slitting End Date</th>
									<th width="80" class="">Slitting End Time</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Slitting Execution Days</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Slitting Execution Time</th>
									<th width="80" class="">Result</th>
									
									<th width="80" style="word-break:break-all" class="">Stentering Start Date</th>
									<th width="80" style="word-break:break-all" class="">Stentering  Start Time</th>
									<th width="80" style="word-break:break-all" class="">Stentering  End Date</th>
									<th width="80" style="word-break:break-all" class="">Stentering  End Time</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Stentering  Execution Days</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Stentering Execution Time</th>
									<th width="80" class="">Result</th>
									
									<th width="80" class="">Start Date</th>
									<th width="80" class="">Start Time</th>
									<th width="80" class="">End Date</th>
									<th width="80" class="">End Time</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to  Execution Days</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Execution Time</th>
									<th width="80" class="">Result</th>
									
									<th width="80" style="word-break:break-all" class="">Drying Start Date</th>
									<th width="80" style="word-break:break-all" class="">Drying Start Time</th>
									<th width="80" class=""> Drying End Date</th>
									<th width="80" class="">Drying  End Time</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Drying  Execution Days</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Drying Execution Time</th>
									<th width="80" class="">Result</th>
									
									<th width="80" class="">Start Date</th>
									<th width="80" class="">Start Time</th>
									<th width="80" class="">End Date</th>
									<th width="80" class="">End Time</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to  Execution Days</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Execution Time</th>
									<th width="80" class="">Result</th>
									
									<th width="80" style="word-break:break-all" class="">Compacting Start Date</th>
									<th width="80" style="word-break:break-all" class="">Compacting Start Time</th>
									<th width="80" style="word-break:break-all" class=""> Compacting End Date</th>
									<th width="80" style="word-break:break-all" class="">Compacting  End Time</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Compacting  Execution Days</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Compacting Execution Time</th>
									<th width="80" class="">Result</th>
									
									 <th width="80" class="">Start Date</th>
									<th width="80" class="">Start Time</th>
									<th width="80" class="">End Date</th>
									<th width="80" class="">End Time</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to  Execution Days</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Execution Time</th>
									<th width="80" class="">Result</th>
									
									 <th width="80" class="">Brush Start Date</th>
									<th width="80" class="">Brush Start Time</th>
									<th width="80" class=""> Brush End Date</th>
									<th width="80" class="">Brush  End Time</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Brush Execution Days</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to BrushExecution Time</th>
									<th width="80" class="">Result</th>
									
									<th width="80" class="">Start Date</th>
									<th width="80" class="">Start Time</th>
									<th width="80" class="">End Date</th>
									<th width="80" class="">End Time</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to  Execution Days</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Execution Time</th>
									<th width="80" class="">Result</th>
									
									<th width="80" class="">QC Date</th>
									<th width="80" class="">QC Time</th>
									<th width="80" style="word-break:break-all" class=""> Dyeing to QC Execution Days</th>
									<th width="80" style="word-break:break-all"class="">Dyeing to QC Execution Time</th>
									<th width="80" class="">Production Qty. Kg</th>
									<th width="80" class="">Production Qty. Yds</th>
									
									<th width="80" style="word-break:break-all" class="">Finish Fabric Delivery Date</th>
									<th width="80" style="word-break:break-all" class="">Finish Fabric Delivery Time</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Delivery Execution Days</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Delivery Execution Time</th>
									<th width="80" class="">Delivery Qty. Kg</th>
									<th width="80" class="">Delivery Qty. Yds</th>
									
									 <th width="80" style="word-break:break-all" class="">Batch Creation to Delivery Execution Days</th>
									<th width="80" style="word-break:break-all" class="">Batch Creation to Delivery Execution Time</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Fabric Delivery Execution Days</th>
									<th width="" style="word-break:break-all" class="">Dyeing to Fabric Delivery Execution Time</th>
								   
									   
									
									
								</tr>
							</thead>
						</table>
						<div style=" max-height:380px; width:<? echo $width+20;?>px; overflow-y:scroll;" id="scroll_body">
						<table align="left" class="rpt_table" id="table_body" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tbody>
								<tr>
								<td colspan="102"><b>Inhouse </b></td>
								</tr>
									<?
									$tot_batch_qty=$tot_fin_prod_qc_qnty_kg=$tot_fin_prod_qc_qnty_yds=$tot_fin_deli_qnty_kg=$tot_fin_deli_qnty_yds=0;
									$ii=1;
									foreach($batch_wise_dying_arr as $batch_id=>$row)
									{
										
													if ($ii%2==0)
														$bgcolor="#E9F3FF";
													else
														$bgcolor="#FFFFFF";
													$within_group=$booking_data_arr2[$row[("sales_order_no")]]["within_group"];
												//	echo $within_group.'DDDDD';
													$heatset_start_date=$row[("heatset_start_date")];
													$heatset_start_time=$row[("heatset_starttime")];
													$heatset_end_date=$row[("heatset_end_date")];
													$heatset_endtime=$row[("heatset_endtime")];
													$batch_hr_min=$row[("batch_hr_min")].':00';
												//	echo $batch_hr_min.', ';
													$batch_date_time=($row["batch_date"].' '.$batch_hr_min);
													$heat_date_time=($heatset_end_date.' '.$heatset_endtime.':'.'00');
													$batchtoheat_time_diff=datediff(n,$batch_date_time ,$heat_date_time);
												//	echo $batch_date_time.'='.$heat_date_time;
													//$diff_time_days=$total_time_diff;
													$unload_date=$load_unload_time_arr[$batch_id]["end_date"];
													//echo $unload_date.'D'.$batch_id;
													$unload_time=$load_unload_time_arr[$batch_id]["process_time"];
													$unload_date_time=($unload_date.' '.$unload_time.':'.'00');
													$unload_date_time2=(change_date_format($unload_date).' '.$unload_time.':'.'00');
													
													$load_date=$load_time_arr[$batch_id]["end_date"];
													$load_time=$load_time_arr[$batch_id]["end_time"];
													//echo $batch_date_time.'='.$unload_date_time2;
													$batchtodying_time_diff=datediff(n,$batch_date_time ,$unload_date_time);
													
													$sliting_start_date=$row[("sliting_start_date")];
													$sliting_start_time=$row[("sliting_starttime")];
													$sliting_end_date=$row[("sliting_end_date")];
													$sliting_endtime=$row[("sliting_endtime")]; 
													//$batch_hr_min=$row[("batch_hr_min")];
													//$batch_date_time=($row["batch_date"].' '.$batch_hr_min.':'.'00');
													$sliting_date_time=(change_date_format($sliting_end_date).' '.$sliting_endtime.':'.'00');
													$dyingtosliting_time_diff=datediff(n,$unload_date_time2 ,$sliting_date_time); 
													//echo $sliting_date_time.'='.$unload_date_time2;
													
													$stenter_start_date=$row[("stenter_start_date")];
													$stenter_start_time=$row[("stenter_starttime")];
													$stenter_end_date=$row[("stenter_end_date")];
													$stenter_endtime=$row[("stenter_endtime")];
													$stenter_date_time=($stenter_end_date.' '.$stenter_endtime.':'.'00');
													$dyingtostener_time_diff=datediff(n,$unload_date_time ,$stenter_date_time);
													
													$type_stenter_start_date=$row[("type_stenter_start_date")];
													$type_stenter_start_time=$row[("type_stenter_starttime")];
													$type_stenter_end_date=$row[("type_stenter_end_date")];
													$type_stenter_endtime=$row[("type_stenter_endtime")];
													$type_stenter_date_time=($type_stenter_end_date.' '.$type_stenter_endtime.':'.'00');
													$type_dyingtotype_stener_time_diff=datediff(n,$unload_date_time ,$type_stenter_date_time);
													
													$typewise_comp_start_date=$row[("type_comp_start_date")];
													$typewise_comp_start_time=$row[("type_comp_starttime")];
													$typewise_comp_end_date=$row[("type_comp_end_date")];
													$typewise_comp_endtime=$row[("type_comp_endtime")];
													$typewise_comp_date_time=($typewise_comp_end_date.' '.$typewise_comp_endtime.':'.'00');
													$dyingtotypecomp_time_diff=datediff(n,$unload_date_time ,$typewise_comp_date_time);
													
													$typewise_dry_start_date=$row[("type_dry_start_date")];
													$typewise_dry_start_time=$row[("type_dry_starttime")];
													$typewise_dry_end_date=$row[("type_dry_end_date")];
													$typewise_dry_endtime=$row[("type_dry_endtime")];
													$typewise_dry_date_time=($typewise_dry_end_date.' '.$typewise_dry_endtime.':'.'00');
													$dyingtotypedry_time_diff=datediff(n,$unload_date_time ,$typewise_dry_date_time);
													
													$comp_start_date=$row[("comp_start_date")];
													$comp_start_time=$row[("comp_starttime")];
													$comp_end_date=$row[("comp_end_date")];
													$comp_endtime=$row[("comp_endtime")];
													$comp_date_time=($comp_end_date.' '.$comp_endtime.':'.'00');
													$dyingtocomp_time_diff=datediff(n,$unload_date_time ,$comp_date_time);
													
													$dry_start_date=$row[("dry_start_date")];//
													$dry_starttime=$row[("dry_starttime")];
													$dry_end_date=$row[("dry_end_date")];
													$dry_endtime=$row[("dry_endtime")];
													$dry_date_time=($dry_end_date.' '.$dry_endtime.':'.'00');
													$dyingtodry_time_diff=datediff(n,$unload_date_time ,$dry_date_time);
													
													
													$typewise_brush_start_date=$row[("type_brush_start_date")];
													$typewise_brush_start_time=$row[("type_brush_starttime")];
													$typewise_brush_end_date=$row[("type_brush_end_date")];
													$typewise_brush_endtime=$row[("type_brush_endtime")];
													$typewise_brush_date_time=($typewise_brush_end_date.' '.$typewise_brush_endtime.':'.'00');
													$dyingtotypebrush_time_diff=datediff(n,$unload_date_time ,$typewise_brush_date_time);
													
													$typewise_peach_start_date=$row[("type_peach_start_date")];
													$typewise_peach_start_time=$row[("type_peach_starttime")];
													$typewise_peach_end_date=$row[("type_peach_end_date")];
													$typewise_peach_endtime=$row[("type_peach_endtime")];
													$typewise_peach_date_time=($typewise_peach_end_date.' '.$typewise_peach_endtime.':'.'00');
													$dyingtotypepeach_time_diff=datediff(n,$unload_date_time ,$typewise_peach_date_time);
													
													//$fin_uom=$fin_fab_arr[$batch_id]["uom"];
													$fin_prod_date=$fin_fab_arr[$batch_id]["receive_date"];
													$fin_insert_time=explode(" ",$fin_fab_arr[$batch_id]["insert_date"]);
													$fin_time_convert=$fin_insert_time[1].' '.$fin_insert_time[2];
													if($fin_prod_date!="")
													{
													$fintimecal=strtotime("$fin_time_convert");
													$fintime_cal= date('H:i',$fintimecal);
													$fintime_cal=$fintime_cal.':00';
													} else $fintime_cal="";
													$fin_prod_date_time=($fin_prod_date.' '.$fintime_cal);
													$dyingtotypefinprod_time_diff=datediff(n,$unload_date_time ,$fin_prod_date_time);
													$fin_prod_qc_qnty_kg=$fin_fab_qty_arr[$batch_id][12]["receive_qnty"];
													$fin_prod_qc_qnty_yds=$fin_fab_qty_arr[$batch_id][27]["receive_qnty"];
													
													//$fin_uom=$fin_fab_deli_arr[$batch_id]["uom"];
													$fin_deli_date=$fin_fab_deli_arr[$batch_id]["delevery_date"];
													$fin_deli_insert_time=explode(" ",$fin_fab_deli_arr[$batch_id]["insert_date"]);
													$fin_deli_time_convert=$fin_deli_insert_time[1].' '.$fin_deli_insert_time[2];
													//echo $fin_deli_time_convert.'gg';
													if($fin_deli_date!="")
													{
													$delifintime_cal=strtotime("$fin_deli_time_convert");
													$deli_fintime_cal= date('H:i',$delifintime_cal);
													$deli_fintime_cal=$deli_fintime_cal.':00';
													} else $deli_fintime_cal="";
													//echo  $deli_fintime_cal.'NN';
													$fin_deli_prod_date_time=($fin_deli_date.' '.$deli_fintime_cal);
													$dyingtotypeDelifinprod_time_diff=datediff(n,$unload_date_time ,$fin_deli_prod_date_time);
													$fin_deli_qnty_kg=$fin_fab_deli_qty_arr[$batch_id][12]["current_delivery"];
													$fin_deli_qnty_yds=$fin_fab_deli_qty_arr[$batch_id][27]["current_delivery"];
													
													
													$batchtoDelifinprod_time_diff=datediff(n,$batch_date_time ,$fin_deli_prod_date_time);
													
													$dyeingtoDelifinprod_time_diff=datediff(n,$unload_date_time ,$fin_deli_prod_date_time);
													//echo $fin_deli_prod_date_time.'='.$unload_date_time.', ';
													
													//sliting
													
													//$batch_hr_min=batch_hr_min;
													?>
													<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $ii; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $ii; ?>">
														<td width="20" align="center" class=""><? echo $ii++ ;?></td>
														<td width="80" align="center" title="Prod Date"><? echo "&nbsp;".change_date_format($row["end_date"]) ;?> </td>
														<td width="80" class="" align="center"><?  if($within_group==2) echo $booking_data_arr2[$row[("sales_order_no")]]["buyer_id"];else echo $booking_data_arr[$row[("booking_no")]]["buyer_id"];//echo $company_library[$row["w_company_id"]];?> </td>
														<td width="80" align="center" style="word-break:break-all" class=""><? echo $booking_data_arr2[$row[("sales_order_no")]]["style_ref_no"];?></td>
														<td width="100" align="center" class=""><? echo $row[("sales_order_no")];?></td>
														<td width="100" align="center" class=""><? echo $row[("booking_no")];//$row["batch_no"];//$booking_data_arr2[$row[("sales_order_no")]]["style_ref_no"];?></td>
														<? ?>
														<td width="80" align="center" class=""><? echo $row[("batch_no")];//if($within_group==1) echo $booking_data_arr[$row[("booking_no")]]["job_no"];?></td>
														<td width="80" align="center" class=""><? echo $row["extention_no"];//$booking_data_arr2[$row[("sales_order_no")]]["season"];?> </td>
														<td width="80" align="right"><? echo number_format($row["batch_qty"],0);?></td>
														<td width="80" align="center" class=""><? echo "&nbsp;".change_date_format($row["batch_date"]);?></td>
														<td width="80" align="center" class=""><? echo $row[("batch_hr_min")];//$batch_hr_min;?> </td>
														<td width="80" align="center" class=""><? echo "&nbsp;".change_date_format($heatset_start_date);?></td>
														<td width="80" align="center" class=""><? echo  $heatset_start_time;?></td>
														<td width="80" align="center" class="" title="<? echo $batch_id;?>"><? echo "&nbsp;".change_date_format($heatset_end_date);?></td>
														<td width="80" align="center" class=""><? echo $heatset_endtime;?></td>
														<td width="80" align="center" class="">
															<?
															if($heatset_end_date!="")  echo $batchtoheat_days_remian=datediff('d',$row["batch_date"],$heatset_end_date)-1;
															else echo "";
															
															?>
														</td>
														<td width="80" align="center" class=""><?  if($heatset_end_date!='') echo floor($batchtoheat_time_diff/60).":".$batchtoheat_time_diff%60;
														else echo " "; ?> </td>
														<td width="80" class="" align="center" ><?  echo "&nbsp;".change_date_format($load_date);?> </td>
														<td width="80" class="" align="center"><?  echo $load_time;?> </td>
														<td width="80" class="" align="center">
															<? echo "&nbsp;".change_date_format($unload_date);?>
														</td>
														<td width="80" class="" align="center"><? echo $unload_time;?> </td>
														<td width="80" class="" align="center"><?  if($unload_date!="")  echo $batchtodying_days_remian=datediff('d',$row["batch_date"],$unload_date)-1;
															else echo "";;?> </td>
														<td width="80" class="" align="center"><? if($batch_hr_min!=0) echo floor($batchtodying_time_diff/60).":".$batchtodying_time_diff%60;;?></td>
														<td width="80" class="" align="center" title="<? echo $sliting_start_date;?>" >
															<? echo "&nbsp;".change_date_format($sliting_start_date);?>
														</td>
														<td width="80" class="" align="center">
															<?
															echo $sliting_start_time;?>
														</td>
														<td width="80" class="" align="center">
															<? echo "&nbsp;".change_date_format($sliting_end_date);;
															?>
														</td>
														<td width="80" align="center" class="" title="H:M">
															<?
															echo $sliting_endtime;
															?>
														</td>
														<td width="80" align="center" title="Unload Date Time" class="">
															<?
															
															 if($sliting_end_date!="")  echo $dyingtosliting_days_remian=datediff('d',$unload_date,$sliting_end_date)-1;//
															?>
														</td>
														<td width="80" align="center" class="" title="Unload Date Time=<? echo $unload_date_time;?>">
															<?
															 if($sliting_end_date!=0) echo floor($dyingtosliting_time_diff/60).":".$dyingtosliting_time_diff%60;
															 else echo " "; 
															?> 
														</td>
														<td width="80" class="" align="center"><? echo $dyeing_result[$row["sliting_result"]];?></td>
														
														<td width="80" class="" align="center"><? echo "&nbsp;".change_date_format($stenter_start_date);?></td>
														<td width="80" class="" align="center"><? echo $stenter_start_time;?></td>
														<td width="80" class="" align="center"><? echo "&nbsp;".change_date_format($stenter_end_date);?></td>
														<td width="80" class="" align="center"><? echo $stenter_endtime ;?></td>
														<td width="80" class="" align="center"><?   if($stenter_end_date!="")  echo $dyingtostener_days_diff=datediff('d',$unload_date,$stenter_end_date)-1; ;?></td>
														<td width="80" align="center" title="Unload Date Time=<? echo $unload_date_time;?>"><?  if($stenter_end_date!=0) echo floor($dyingtostener_time_diff/60).":".$dyingtostener_time_diff%60;
															 else echo " "; ?></td>
														
														<td width="80" align="center" class=""><? echo $dyeing_result[$row["stenter_result"]]  ;?></td>
														<td width="80" align="center" class=""><? echo "&nbsp;".change_date_format($type_stenter_start_date);?></td>
														<td width="80" align="center" class=""><? echo $type_stenter_start_time;?></td>
														<td width="80" align="center" class=""><? echo "&nbsp;".change_date_format($type_stenter_end_date) ;?></td>
														<td width="80" align="center" class=""><? echo $type_stenter_endtime;?></td>
														<td width="80" align="center" class=""><? if($type_stenter_end_date!="")  echo $dyingtotype_stener_time_diff=datediff('d',$unload_date,$type_stenter_end_date)-1;?></td>
														<td width="80" align="center" class=""><?   if($type_stenter_end_date!=0) echo floor($type_dyingtotype_stener_time_diff/60).":".$type_dyingtotype_stener_time_diff%60;
															 else echo " ";?></td>
														
														<td width="80" align="center" class=""><? echo  $dyeing_result[$row["type_stenter_result"]];?></td>
														
														<td width="80" align="center" class=""><? echo "&nbsp;".change_date_format($dry_start_date);?></td>
														<td width="80" align="center" class=""><? echo $dry_starttime;?></td>
														<td width="80" align="center" class=""><? echo "&nbsp;".change_date_format($dry_end_date);?></td>
														<td width="80" align="center"class=""><? echo  $dry_endtime;?></td>
														<td width="80" align="center" class=""><? if($dry_end_date!="")  echo $dyingtodry_days_remian=datediff('d',$unload_date,$dry_end_date)-1;
															else echo "";?></td>
														<td width="80" align="center" title="Unload Date Time=<? echo $unload_date_time;?>"><? if($dry_end_date!=0) echo floor($dyingtodry_time_diff/60).":".$dyingtodry_time_diff%60;
															 else echo " ";?></td>
													   
														<td width="80" align="center" class=""><? echo $dyeing_result[$row["dry_result"]];?></td>
														
														<td width="80"align="center"  class=""><? echo "&nbsp;".change_date_format($typewise_dry_start_date);?></td>
														<td width="80" align="center" class=""><? echo $typewise_dry_start_time;?></td>
														<td width="80" align="center" class=""><? echo "&nbsp;".change_date_format($typewise_dry_end_date);?></td>
														<td width="80" align="center" class=""><? echo $typewise_dry_endtime;?></td>
														<td width="80" align="center" class=""><? if($typewise_dry_end_date!="")  echo $dyingtotype_dry_days_remian=datediff('d',$unload_date,$typewise_dry_end_date)-1;
															else echo ""; ;?></td>
														<td width="80" align="center" class=""><? if($typewise_dry_end_date!=0) 
														echo floor($dyingtotypedry_time_diff/60).":".$dyingtotypedry_time_diff%60; 
														else echo " "; ?></td>
														<td width="80" align="center" class=""><? echo  $dyeing_result[$row["type_dry_result"]];?></td>
														
														<td width="80" align="center" class=""><? echo "&nbsp;".change_date_format($comp_start_date);?></td>
														<td width="80" align="center" class=""><? echo $comp_start_time  ;?></td>
														<td width="80" align="center" class=""><? echo "&nbsp;".change_date_format($comp_end_date);?></td>
														<td width="80" align="center" class=""><? echo $comp_endtime;?></td>
														<td width="80" align="center" class=""><? if($comp_end_date!="")  echo $dyingtocomp_days_remian=datediff('d',$unload_date,$comp_end_date)-1;
															else echo ""; ;?></td>
														<td width="80" align="center" class=""><? if($comp_end_date!=0) echo floor($dyingtocomp_time_diff/60).":".$dyingtocomp_time_diff%60;
															 else echo " "; ?></td>
														
														<td width="80" class="" align="center"><? echo  $dyeing_result[$row["comp_result"]];?></td>
														
														
														<td width="80" class="" align="center"><? echo "&nbsp;".change_date_format($typewise_comp_start_date);?></td>
														<td width="80" class="" align="center"><? echo $typewise_comp_start_time;?></td>
														<td width="80" class="" align="center"><? echo "&nbsp;".change_date_format($typewise_comp_end_date); ;?></td>
														<td width="80" class="" align="center"><? echo $typewise_comp_endtime;?></td>
														<td width="80" class="" align="center"><?  if($typewise_comp_end_date!="")  echo $dyingtotype_comp_days_remian=datediff('d',$unload_date,$typewise_comp_end_date)-1;
															else echo ""; ;?></td>
														<td width="80" class="" align="center"><?  if($typewise_comp_end_date!=0) 
														echo floor($dyingtotypecomp_time_diff/60).":".$dyingtotypecomp_time_diff%60; 
														else echo " "; ;?></td>
														
														<td width="80" class="" align="center"><? echo $dyeing_result[$row["type_comp_result"]];?></td>
														
														<td width="80" class="" align="center"><? echo "&nbsp;".change_date_format($typewise_brush_start_date) ;?></td>
														<td width="80" class="" align="center"><? echo $typewise_brush_start_time ;?></td>
														<td width="80" class="" align="center"><? echo "&nbsp;".change_date_format($typewise_brush_end_date) ;?></td>
														<td width="80" class="" align="center"><? echo $typewise_brush_endtime;?></td>
														<td width="80" class="" align="center"><? if($typewise_brush_end_date!="")  echo $dyingtotype_brush_days_remian=datediff('d',$unload_date,$typewise_brush_end_date)-1;
								;?></td>
														<td width="80" class="" align="center"><? if($typewise_brush_end_date!=0) 
														echo floor($dyingtotypebrush_time_diff/60).":".$dyingtotypebrush_time_diff%60; 
														else echo " "; ?></td>
														 <td width="80" class="" align="center"><? echo $dyeing_result[$row["type_brush_result"]];?></td>
														
													  
													  <td width="80" class="" align="center"><? echo "&nbsp;".change_date_format($typewise_peach_start_date) ;?></td>
														<td width="80" class="" align="center"><? echo $typewise_peach_start_time ;?></td>
														<td width="80" class="" align="center"><? echo "&nbsp;".change_date_format($typewise_peach_end_date) ;?></td>
														<td width="80" class="" align="center"><? echo $typewise_peach_endtime;?></td>
														<td width="80" class="" align="center"><? if($typewise_peach_end_date!="")  echo $dyingtotype_peach_days_remian=datediff('d',$unload_date,$typewise_peach_end_date)-1;?></td>
														<td width="80" class="" align="center"><? if($typewise_peach_end_date!=0) 
														echo floor($dyingtotypepeach_time_diff/60).":".$dyingtotypepeach_time_diff%60; 
														else echo " "; ?></td>
													  
														<td width="80" class="" align="center"><? echo $dyeing_result[$row["type_peach_result"]];?></td>
														<td width="80" class="" align="center" ><? echo "&nbsp;".change_date_format($fin_prod_date)  ;?></td>
														<td width="80" class="" align="center"><? echo $fintime_cal ;?></td>
														<td width="80" class="" align="center"><?  if($fin_prod_date!="")  echo $dyingtofinprod_days_remian=datediff('d',$unload_date,$fin_prod_date)-1;?></td>
														
														<td width="80" class="" align="center"><?  if($fin_prod_date!=0) 
														echo floor($dyingtotypefinprod_time_diff/60).":".$dyingtotypefinprod_time_diff%60; 
														else echo " ";?></td>
														
														<td width="80" align="right"><?  echo number_format($fin_prod_qc_qnty_kg,2);?></td>
														<td width="80" align="right"><? echo number_format($fin_prod_qc_qnty_yds,2)  ;?></td>
														<td width="80" class="" align="center"><? echo "&nbsp;".change_date_format($fin_deli_date);?></td>
														  <td width="80" class="" align="center"><? echo $deli_fintime_cal ;?></td>
														<td width="80" class="" align="center"><?  if($fin_deli_date!="")  echo $dyingtofindeli_days_remian=datediff('d',$unload_date,$fin_deli_date)-1;?></td>
														<td width="80" class="" align="center"><?  if($fin_deli_date!=0) 
														echo floor($dyingtotypeDelifinprod_time_diff/60).":".$dyingtotypeDelifinprod_time_diff%60; 
														else echo " ";?></td>
														  <td width="80" align="right"><? echo number_format($fin_deli_qnty_kg,2) ;?></td>
														<td width="80" align="right"><? echo number_format($fin_deli_qnty_yds,2)  ;?></td>
														<td width="80" align="center" class=""><?   if($fin_deli_date!="")  echo $dyingtosliting_days_remian=datediff('d',$row["batch_date"],$fin_deli_date)-1;?></td>
														<td width="80" align="center" title="Batch Date Time=<? echo $batch_date_time;?>" class=""><?  if($fin_deli_date!=0) 
														echo floor($batchtoDelifinprod_time_diff/60).":".$batchtoDelifinprod_time_diff%60; 
														else echo " " ;?></td>
														<td width="80" class="" align="center"><? if($fin_deli_date!="")  echo $dyingtodeli_days_remian=datediff('d',$unload_date,$fin_deli_date)-1 ;//echo "M".$unload_date_time.'='.$fin_deli_prod_date_time;?></td>
													  
														<td width="" align="center" title="Unload Date Time=<? echo $unload_date;?>"><?  if($fin_deli_date!=0) 
														echo floor($dyeingtoDelifinprod_time_diff/60).":".$dyeingtoDelifinprod_time_diff%60; 
														else echo " " ;;?></td>
														
													</tr>
													<?
													$p++;
													$tot_batch_qty+= $row["batch_qty"];
													$tot_fin_prod_qc_qnty_kg+= $fin_prod_qc_qnty_kg;
													$tot_fin_prod_qc_qnty_yds+= $fin_prod_qc_qnty_yds;
													$tot_fin_deli_qnty_kg+= $fin_deli_qnty_kg;
													$tot_fin_deli_qnty_yds+= $fin_deli_qnty_yds;
											
										}
							
									?>
						</tbody>
			   
					</table>
					</div>
			   <table width="<? echo $width; ?>" border="1" cellpadding="2" cellspacing="0" class="tbl_bottom" rules="all">
				
				<tr>
				<td colspan="8" width="620" align="left">Total</td>
				<td align="right" width="80"><? echo number_format($tot_batch_qty,0);?> </td>
				<td colspan="80"  width="6400"></td>
				 <td align="right" width="80"><? echo number_format($tot_fin_prod_qc_qnty_kg,2);?> </td>
				<td align="right" width="80"><? echo number_format($tot_fin_prod_qc_qnty_yds,2);?> </td>
				<td align="right" width="80"><? //echo number_format($tot_fin_prod_qc_qnty_yds,0);?> </td>
				<td colspan="3" width="240"></td>
				 <td align="right" width="80"><? echo number_format($tot_fin_deli_qnty_kg,2);?> </td>
				<td align="right" width="80"><? echo number_format($tot_fin_deli_qnty_yds,2);?> </td>
				<td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
				<td colspan="4" width="240"></td>
				<tr/>
				</table>
			   
				<table class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
					<caption><strong><? echo  $company_library[$cbo_company_name]; ?><br><? echo $report_title;?>	<br>
						<? echo  change_date_format($txt_date_from).' To '.change_date_format($txt_date_to); ?></strong>
					</caption>
							<thead>
								<tr>
									<th colspan="9">Batch Information</th>
									<th colspan="2">Batch</th>
									<th colspan="6">Heat Setting</th>
									<th colspan="6">Dyeing</th>
									<th colspan="7">Slitting</th>
									<th colspan="7">Stentering </th>
									<th colspan="7" style="word-break:break-all">Production type wise Stentering </th>
									<th colspan="7">Drying</th>
									<th colspan="7" style="word-break:break-all">Production type wise  Drying </th>
									<th colspan="7">Compacting</th>
									<th colspan="7" style="word-break:break-all">Production type wise Compacting</th>
									<th colspan="7">Brush</th>
									<th colspan="7">Peach</th>
									<th colspan="6">Finish Fabric Production</th>
									<th colspan="6">Finish Fabric Delivery</th>
									<th colspan="4">Result</th>
								</tr>
								
								<tr>
									<th width="20" class="">SL</th>
									<th width="80" class="">Date</th>
									<th width="80" class="">Buyer</th>
									<th width="80" class="">Style Ref.</th>
									<th width="100" class="">FSO No</th>
									<th width="100" style="word-break:break-all" class="">Order No.</th>
									<th width="80" class="">Batch numbers</th>
									<th width="80" class="">Ext. No</th>
									
									<th width="80" class="">Batch Qty</th>
									<th width="80" style="word-break:break-all" class="">Batch Creation Date</th>
									<th width="80" style="word-break:break-all"  class="">Batch Creation Time</th>
									<th width="80" style="word-break:break-all" class="">Heat Setting Start Date</th>
									<th width="80" style="word-break:break-all" class="">Heat Setting Start Time</th>
									<th width="80" style="word-break:break-all" class="">Heat Setting End Date</th>
									<th width="80" style="word-break:break-all" class="">Heat Setting End Time</th>
									<th width="80" style="word-break:break-all" class="">Batch to Heat Setting Execution Days</th>
									<th width="80" style="word-break:break-all" class="">Batch to Heat Setting Execution Time</th>
									<th width="80" style="word-break:break-all" class="">Dyeing loading Date</th>
									<th width="80" style="word-break:break-all" class="">Dyeing Loading Time</th>
									<th width="80" style="word-break:break-all" class="">Dyeing Unloading Date</th>
									<th width="80" style="word-break:break-all" class="">Dyeing Unloading Time</th>
									<th width="80" style="word-break:break-all" class="">Batch to Dyeing Execution Days</th>
									<th width="80" style="word-break:break-all" class="">Batch to Dyeing Execution Time</th>
									<th width="80" class="">Slitting Start Date</th>
									<th width="80" class="">Slitting Start Time</th>
									<th width="80" class="">Slitting End Date</th>
									<th width="80" class="">Slitting End Time</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Slitting Execution Days</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Slitting Execution Time</th>
									<th width="80" class="">Result</th>
									
									<th width="80" style="word-break:break-all" class="">Stentering Start Date</th>
									<th width="80" style="word-break:break-all" class="">Stentering  Start Time</th>
									<th width="80" style="word-break:break-all" class="">Stentering  End Date</th>
									<th width="80" style="word-break:break-all" class="">Stentering  End Time</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Stentering  Execution Days</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Stentering Execution Time</th>
									<th width="80" class="">Result</th>
									
									<th width="80" class="">Start Date</th>
									<th width="80" class="">Start Time</th>
									<th width="80" class="">End Date</th>
									<th width="80" class="">End Time</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to  Execution Days</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Execution Time</th>
									<th width="80" class="">Result</th>
									
									<th width="80" style="word-break:break-all" class="">Drying Start Date</th>
									<th width="80" style="word-break:break-all" class="">Drying Start Time</th>
									<th width="80" class=""> Drying End Date</th>
									<th width="80" class="">Drying  End Time</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Drying  Execution Days</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Drying Execution Time</th>
									<th width="80" class="">Result</th>
									
									<th width="80" class="">Start Date</th>
									<th width="80" class="">Start Time</th>
									<th width="80" class="">End Date</th>
									<th width="80" class="">End Time</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to  Execution Days</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Execution Time</th>
									<th width="80" class="">Result</th>
									
									<th width="80" style="word-break:break-all" class="">Compacting Start Date</th>
									<th width="80" style="word-break:break-all" class="">Compacting Start Time</th>
									<th width="80" style="word-break:break-all" class=""> Compacting End Date</th>
									<th width="80" style="word-break:break-all" class="">Compacting  End Time</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Compacting  Execution Days</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Compacting Execution Time</th>
									<th width="80" class="">Result</th>
									
									 <th width="80" class="">Start Date</th>
									<th width="80" class="">Start Time</th>
									<th width="80" class="">End Date</th>
									<th width="80" class="">End Time</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to  Execution Days</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Execution Time</th>
									<th width="80" class="">Result</th>
									
									 <th width="80" class="">Brush Start Date</th>
									<th width="80" class="">Brush Start Time</th>
									<th width="80" class=""> Brush End Date</th>
									<th width="80" class="">Brush  End Time</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Brush Execution Days</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to BrushExecution Time</th>
									<th width="80" class="">Result</th>
									
									<th width="80" class="">Start Date</th>
									<th width="80" class="">Start Time</th>
									<th width="80" class="">End Date</th>
									<th width="80" class="">End Time</th>
	
									<th width="80" style="word-break:break-all" class="">Dyeing to  Execution Days</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Execution Time</th>
									<th width="80" class="">Result</th>
									
									<th width="80" class="">QC Date</th>
									<th width="80" class="">QC Time</th>
									<th width="80" style="word-break:break-all" class=""> Dyeing to QC Execution Days</th>
									<th width="80" style="word-break:break-all"class="">Dyeing to QC Execution Time</th>
									<th width="80" class="">Production Qty. Kg</th>
									<th width="80" class="">Production Qty. Yds</th>
									
									<th width="80" style="word-break:break-all" class="">Finish Fabric Delivery Date</th>
									<th width="80" style="word-break:break-all" class="">Finish Fabric Delivery Time</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Delivery Execution Days</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Delivery Execution Time</th>
									<th width="80" class="">Delivery Qty. Kg</th>
									<th width="80" class="">Delivery Qty. Yds</th>
									
									 <th width="80" style="word-break:break-all" class="">Batch Creation to Delivery Execution Days</th>
									<th width="80" style="word-break:break-all" class="">Batch Creation to Delivery Execution Time</th>
									<th width="80" style="word-break:break-all" class="">Dyeing to Fabric Delivery Execution Days</th>
									<th width="" style="word-break:break-all" class="">Dyeing to Fabric Delivery Execution Time</th>
								</tr>
							</thead>
						</table>
						<div style=" max-height:380px; width:<? echo $width+20;?>px; overflow-y:scroll;" id="scroll_body">
						<table align="left" class="rpt_table" id="table_body2" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tbody>
								<tr>
								<td colspan="102"><b>Inbound Subcontract</b></td>
								</tr>
									<?
									$tot_batch_qty=$tot_fin_prod_qc_qnty_kg=$tot_fin_prod_qc_qnty_yds=$tot_fin_deli_qnty_kg=$tot_fin_deli_qnty_yds=0;
									$ii=1;
									foreach($sub_batch_wise_dying_arr as $batch_id=>$row)
									{
										
													if ($ii%2==0)
														$bgcolor="#E9F3FF";
													else
														$bgcolor="#FFFFFF";
													//$within_group=$booking_data_arr2[$row[("sales_order_no")]]["within_group"];
													$heatset_start_date=$row[("heatset_start_date")];
													$heatset_start_time=$row[("heatset_starttime")];
													$heatset_end_date=$row[("heatset_end_date")];
													$heatset_endtime=$row[("heatset_endtime")];
													$batch_hr_min=$row[("batch_hr_min")].':00';
													$batch_date_time=($row["batch_date"].' '.$batch_hr_min);
													$heat_date_time=($heatset_end_date.' '.$heatset_endtime.':'.'00');
													$batchtoheat_time_diff=datediff(n,$batch_date_time ,$heat_date_time);
													//$diff_time_days=$total_time_diff;
													$unload_date=$sub_load_unload_time_arr[$batch_id]["end_date"];
													$unload_time=$sub_load_unload_time_arr[$batch_id]["process_time"];
													$unload_date_time=($unload_date.' '.$unload_time.':'.'00');
													$unload_date_time2=(change_date_format($unload_date).' '.$unload_time.':'.'00');
													
													$load_date=$sub_load_time_arr[$batch_id]["end_date"];
													$load_time=$sub_load_time_arr[$batch_id]["end_time"];
													$batchtodying_time_diff=datediff(n,$batch_date_time ,$unload_date_time);
													
													$sliting_start_date=$row[("sliting_start_date")];
													$sliting_start_time=$row[("sliting_starttime")];
													$sliting_end_date=$row[("sliting_end_date")];
													$sliting_endtime=$row[("sliting_endtime")];
													//$batch_hr_min=$row[("batch_hr_min")];
													//$batch_date_time=($row["batch_date"].' '.$batch_hr_min.':'.'00');
													$sliting_date_time=($sliting_end_date.' '.$sliting_endtime.':'.'00');
													$dyingtosliting_time_diff=datediff(n,$unload_date_time ,$sliting_date_time);
												
													$stenter_start_date=$row[("stenter_start_date")];
													$stenter_start_time=$row[("stenter_starttime")];
													$stenter_end_date=change_date_format($row[("stenter_end_date")]);
													$stenter_endtime=$row[("stenter_endtime")];
													$stenter_date_time=(change_date_format($stenter_end_date).' '.$stenter_endtime.':'.'00');
													$dyingtostener_time_diff=datediff(n,$unload_date_time2 ,$stenter_date_time);
														//echo $unload_date_time2.'='.$stenter_date_time;
													
													$type_stenter_start_date=$row[("type_stenter_start_date")];
													$type_stenter_start_time=$row[("type_stenter_starttime")];
													$type_stenter_end_date=$row[("type_stenter_end_date")];
													$type_stenter_endtime=$row[("type_stenter_endtime")];
													$type_stenter_date_time=($type_stenter_end_date.' '.$type_stenter_endtime.':'.'00');
													$type_dyingtotype_stener_time_diff=datediff(n,$unload_date_time ,$type_stenter_date_time);
													
													$typewise_comp_start_date=$row[("type_comp_start_date")];
													$typewise_comp_start_time=$row[("type_comp_starttime")];
													$typewise_comp_end_date=$row[("type_comp_end_date")];
													$typewise_comp_endtime=$row[("type_comp_endtime")];
													$typewise_comp_date_time=($typewise_comp_end_date.' '.$typewise_comp_endtime.':'.'00');
													$dyingtotypecomp_time_diff=datediff(n,$unload_date_time ,$typewise_comp_date_time);
													
													$typewise_dry_start_date=$row[("type_dry_start_date")];
													$typewise_dry_start_time=$row[("type_dry_starttime")];
													$typewise_dry_end_date=$row[("type_dry_end_date")];
													$typewise_dry_endtime=$row[("type_dry_endtime")];
													$typewise_dry_date_time=($typewise_dry_end_date.' '.$typewise_dry_endtime.':'.'00');
													$dyingtotypedry_time_diff=datediff(n,$unload_date_time ,$typewise_dry_date_time);
													
													$comp_start_date=$row[("comp_start_date")];
													$comp_start_time=$row[("comp_starttime")];
													$comp_end_date=$row[("comp_end_date")];
													$comp_endtime=$row[("comp_endtime")];
													$comp_date_time=($comp_end_date.' '.$comp_endtime.':'.'00');
													$dyingtocomp_time_diff=datediff(n,$unload_date_time ,$comp_date_time);
													
													$dry_start_date=$row[("dry_start_date")];//
													$dry_starttime=$row[("dry_starttime")];
													$dry_end_date=$row[("dry_end_date")];
													$dry_endtime=$row[("dry_endtime")];
													$dry_date_time=($dry_end_date.' '.$dry_endtime.':'.'00');
													$dyingtodry_time_diff=datediff(n,$unload_date_time ,$dry_date_time);
													
													
													$typewise_brush_start_date=$row[("type_brush_start_date")];
													$typewise_brush_start_time=$row[("type_brush_starttime")];
													$typewise_brush_end_date=$row[("type_brush_end_date")];
													$typewise_brush_endtime=$row[("type_brush_endtime")];
													$typewise_brush_date_time=($typewise_brush_end_date.' '.$typewise_brush_endtime.':'.'00');
													$dyingtotypebrush_time_diff=datediff(n,$unload_date_time ,$typewise_brush_date_time);
													
													$typewise_peach_start_date=$row[("type_peach_start_date")];
													$typewise_peach_start_time=$row[("type_peach_starttime")];
													$typewise_peach_end_date=$row[("type_peach_end_date")];
													$typewise_peach_endtime=$row[("type_peach_endtime")];
													$typewise_peach_date_time=($typewise_peach_end_date.' '.$typewise_peach_endtime.':'.'00');
													$dyingtotypepeach_time_diff=datediff(n,$unload_date_time ,$typewise_peach_date_time);
													
													//$fin_uom=$fin_fab_arr[$batch_id]["uom"];
													$fin_prod_date=$sub_fin_fab_arr[$batch_id]["receive_date"];
													$fin_insert_time=explode(" ",$sub_fin_fab_arr[$batch_id]["insert_date"]);
													$fin_time_convert=$fin_insert_time[1].' '.$fin_insert_time[2];
													if($fin_prod_date!="")
													{
													$fintimecal=strtotime("$fin_time_convert");
													$fintime_cal= date('H:i',$fintimecal);
													$fintime_cal=$fintime_cal.':00';
													} else $fintime_cal="";
													$fin_prod_date_time=($fin_prod_date.' '.$fintime_cal);
													$dyingtotypefinprod_time_diff=datediff(n,$unload_date_time ,$fin_prod_date_time);
													$fin_prod_qc_qnty_kg=$sub_fin_fab_qty_arr[$batch_id][12]["receive_qnty"];
													$fin_prod_qc_qnty_yds=$sub_fin_fab_qty_arr[$batch_id][27]["receive_qnty"];
													
													//$fin_uom=$fin_fab_deli_arr[$batch_id]["uom"];
													$fin_deli_date=$sub_fin_fab_deli_arr[$batch_id]["delevery_date"];
													$fin_deli_insert_time=explode(" ",$sub_fin_fab_deli_arr[$batch_id]["insert_date"]);
													$fin_deli_time_convert=$fin_deli_insert_time[1].' '.$fin_deli_insert_time[2];
													//echo $fin_deli_time_convert.'gg';
													if($fin_deli_date!="")
													{
													$delifintime_cal=strtotime("$fin_deli_time_convert");
													$deli_fintime_cal= date('H:i',$delifintime_cal);
													$deli_fintime_cal=$deli_fintime_cal.':00';
													} else $deli_fintime_cal="";
													//echo  $deli_fintime_cal.'NN';
													$fin_deli_prod_date_time=($fin_deli_date.' '.$deli_fintime_cal);
													$dyingtotypeDelifinprod_time_diff=datediff(n,$unload_date_time ,$fin_deli_prod_date_time);
													$fin_deli_qnty_kg=$sub_fin_fab_deli_qty_arr[$batch_id][12]["current_delivery"];
													$fin_deli_qnty_yds=$sub_fin_fab_deli_qty_arr[$batch_id][27]["current_delivery"];
													//$fin_fab_deli_qty_arr[$row[csf("batch_id")]][12]["current_delivery"];
													
													
													$batchtoDelifinprod_time_diff=datediff(n,$batch_date_time ,$fin_deli_prod_date_time);
													
													$dyeingtoDelifinprod_time_diff=datediff(n,$unload_date_time ,$fin_deli_prod_date_time);
													//echo $fin_deli_prod_date_time.'='.$unload_date_time.', ';
													
													//sliting
													
													//$batch_hr_min=batch_hr_min;
													?>
													<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trsub_<? echo $ii; ?>','<? echo $bgcolor;?>')" id="trsub_<? echo $ii; ?>">
														<td width="20" class="" align="center"><? echo $ii++ ;?></td>
														<td width="80" title="Prod Date" align="center"><? echo "&nbsp;".change_date_format($row["end_date"]) ;?> </td>
														<td width="80" class="" align="center"><?  echo $booking_data_arr[$batch_id]["buyer_id"];//echo $company_library[$row["w_company_id"]];?> </td>
														<td width="80" style="word-break:break-all <strong></strong>" class=""><? echo $booking_data_arr2[$batch_id]["style_ref_no"];?></td>
														<td width="100" class="" align="center"><? //echo $row[("sales_order_no")];?></td>
														<td width="100" class="" align="center"><? echo $booking_data_arr2[$batch_id]["order_no"];;//$row["batch_no"];//$booking_data_arr2[$row[("sales_order_no")]]["style_ref_no"];?></td>
														<? ?>
														<td width="80" class="" align="center" title="BatchId=<? echo $batch_id;?>"> <? echo $row[("batch_no")];//if($within_group==1) echo $booking_data_arr[$row[("booking_no")]]["job_no"];?></td>
														<td width="80" class="" align="center"><? echo $row["extention_no"];//$booking_data_arr2[$row[("sales_order_no")]]["season"];?> </td>
														<td width="80" align="right"><? echo number_format($row["batch_qty"],0);?></td>
														<td width="80" class="" align="center"><? echo "&nbsp;".change_date_format($row["batch_date"]);?></td>
														<td width="80" class="" align="center"><? echo $row[("batch_hr_min")];//$batch_hr_min;?> </td>
														<td width="80" class="" align="center"><? echo "&nbsp;".change_date_format($heatset_start_date);?></td>
														<td width="80" class="" align="center"><? echo  $heatset_start_time;?></td>
														<td width="80" class="" align="center" title="<? echo $batch_id;?>"><? echo "&nbsp;".change_date_format($heatset_end_date);?></td>
														<td width="80" class="" align="center"><? echo $heatset_endtime;?></td>
														<td width="80" class="" align="center">
															<?
															if($heatset_end_date!="")  echo $batchtoheat_days_remian=datediff('d',$row["batch_date"],$heatset_end_date)-1;
															else echo "";
															?>
														</td>
														<td width="80" class="" align="center"><?  if($heatset_end_date!='') echo floor($batchtoheat_time_diff/60).":".$batchtoheat_time_diff%60;
														else echo " "; ?> </td>
														<td width="80" class="" align="center"><?  echo "&nbsp;".change_date_format($load_date);?> </td>
														<td width="80" class="" align="center"><?  echo $load_time;?> </td>
														<td width="80" class="" align="center">
															<? echo "&nbsp;".change_date_format($unload_date);?>
														</td>
														<td width="80" class="" align="center"><? echo $unload_time;?> </td>
														<td width="80" class="" align="center"><?  if($unload_date!="")  echo $batchtodying_days_remian=datediff('d',$row["batch_date"],$unload_date)-1;
															else echo "";;?> </td>
														<td width="80" class="" align="center"><? if($batch_hr_min!=0) echo floor($batchtodying_time_diff/60).":".$batchtodying_time_diff%60;;?></td>
														<td width="80" class="" align="center" title="<? echo $sliting_start_date;?>" >
															<? echo "&nbsp;".change_date_format($sliting_start_date);?>
														</td>
														<td width="80" class="" align="center">
															<?
															echo $sliting_start_time;?>
														</td>
														<td width="80" class="" align="center">
															<? echo "&nbsp;".change_date_format($sliting_end_date);;
															?>
														</td>
														<td width="80" class="" title="H:M" align="center">
															<?
															echo $sliting_endtime;
															?>
														</td>
														<td width="80" title="Unload Date Time" class="" align="center">
															<?
															
															 if($sliting_end_date!="")  echo $dyingtosliting_days_remian=datediff('d',$unload_date,$sliting_end_date)-1;//sliting_end_date
															?>
														</td>
														<td width="80" class="" align="center">
															<?
															 if($sliting_end_date!=0) echo floor($dyingtosliting_time_diff/60).":".$dyingtosliting_time_diff%60;
															 else echo " "; 
															?> 
														</td>
														<td width="80" class="" align="center"><? echo $dyeing_result[$row["sliting_result"]];?></td>
														
														<td width="80" align="center" class=""><? echo "&nbsp;".change_date_format($stenter_start_date);?></td>
														<td width="80" align="center" class=""><? echo $stenter_start_time;?></td>
														<td width="80" align="center" class=""><? echo "&nbsp;".change_date_format($stenter_end_date);?></td>
														<td width="80" align="center" class=""><? echo $stenter_endtime ;?></td>
														<td width="80" align="center" class=""><?   if($stenter_end_date!="")  echo $dyingtostener_days=datediff('d',$unload_date,$stenter_end_date)-1; ;?></td>
														<td width="80" align="center" class=""><?  if($stenter_end_date!=0) echo floor($dyingtostener_time_diff/60).":".$dyingtostener_time_diff%60;
															 else echo " "; ?></td>
														
														<td width="80" align="center" class=""><? echo $dyeing_result[$row["stenter_result"]]  ;?></td>
														<td width="80" align="center" class=""><? echo "&nbsp;".change_date_format($type_stenter_start_date);?></td>
														<td width="80" align="center" class=""><? echo $type_stenter_start_time;?></td>
														<td width="80" align="center" class=""><? echo "&nbsp;".change_date_format($type_stenter_end_date) ;?></td>
														<td width="80" align="center" class=""><? echo $type_stenter_endtime;?></td>
														<td width="80" align="center" class=""><? if($type_stenter_end_date!="")  echo $dyingtotype_stener_time_diff=datediff('d',$unload_date,$type_stenter_end_date)-1;?></td>
														<td width="80" align="center" class=""><?   if($type_stenter_end_date!=0) echo floor($type_dyingtotype_stener_time_diff/60).":".$type_dyingtotype_stener_time_diff%60;
															 else echo " ";?></td>
														
														<td width="80" align="center" class=""><? echo  $dyeing_result[$row["type_stenter_result"]];?></td>
														<td width="80" align="center" class=""><? echo "&nbsp;".change_date_format($dry_start_date);?></td>
														<td width="80" align="center" class=""><? echo $dry_start_time;?></td>
														<td width="80" align="center" class=""><? echo "&nbsp;".change_date_format($dry_end_date);?></td>
														<td width="80" align="center" class=""><? echo  $dry_endtime;?></td>
														<td width="80" align="center" class=""><? if($dry_end_date!="")  echo $dyingtodry_days_remian=datediff('d',$unload_date,$dry_end_date)-1;//
															else echo "";?></td>
														<td width="80" align="center" class=""><? if($dry_end_date!=0) echo floor($dyingtodry_time_diff/60).":".$dyingtodry_time_diff%60;
															 else echo " ";?></td>
													   
														<td width="80" class="" align="center"><? echo $dyeing_result[$row["dry_result"]];?></td>
														
														<td width="80" class="" align="center"><? echo "&nbsp;".change_date_format($typewise_dry_start_date);?></td>
														<td width="80" class="" align="center"><? echo $typewise_dry_start_time;?></td>
														<td width="80" class="" align="center"><? echo "&nbsp;".change_date_format($typewise_dry_end_date);?></td>
														<td width="80" class="" align="center"><? echo $typewise_dry_endtime;?></td>
														<td width="80" class="" align="center"><? if($typewise_dry_end_date!="")  echo $dyingtotype_dry_days_remian=datediff('d',$unload_date,$typewise_dry_end_date)-1;//
															else echo ""; ;?></td>
														<td width="80" align="center" class=""><? if($typewise_dry_end_date!=0) 
														echo floor($dyingtotypedry_time_diff/60).":".$dyingtotypedry_time_diff%60; 
														else echo " "; ?></td>
														<td width="80" class="" align="center"><? echo  $dyeing_result[$row["type_dry_result"]];?></td>
														
														<td width="80" class="" align="center"><? echo "&nbsp;".change_date_format($comp_start_date);?></td>
														<td width="80" class="" align="center"><? echo $comp_start_time  ;?></td>
														<td width="80" class="" align="center"><? echo "&nbsp;".change_date_format($comp_end_date);?></td>
														<td width="80" class="" align="center"><? echo $comp_endtime;?></td>
														<td width="80" class="" align="center"><? if($comp_end_date!="")  echo $dyingtocomp_days_remian=datediff('d',$unload_date,$comp_end_date)-1;
															else echo ""; ;?></td>
														<td width="80" class="" align="center"><? if($comp_end_date!=0) echo floor($dyingtocomp_time_diff/60).":".$dyingtocomp_time_diff%60;
															 else echo " "; ?></td>
														
														<td width="80" class="" align="center"><? echo  $dyeing_result[$row["comp_result"]];?></td>
														
														
														<td width="80" class="" align="center"><? echo "&nbsp;".change_date_format($typewise_comp_start_date);?></td>
														<td width="80" class="" align="center"><? echo $typewise_comp_start_time;?></td>
														<td width="80" class="" align="center"><? echo "&nbsp;".change_date_format($typewise_comp_end_date); ;?></td>
														<td width="80" class="" align="center"><? echo $typewise_comp_endtime;?></td>
														<td width="80" class="" align="center"><?  if($typewise_comp_end_date!="")  echo $dyingtotype_comp_days_remian=datediff('d',$unload_date,$typewise_comp_end_date)-1;
															else echo ""; ;?></td>
														<td width="80" class="" align="center"><?  if($typewise_comp_end_date!=0) 
														echo floor($dyingtotypecomp_time_diff/60).":".$dyingtotypecomp_time_diff%60; 
														else echo " "; ;?></td>
														
														<td width="80" class="" align="center"><? echo $dyeing_result[$row["type_comp_result"]];?></td>
														
														<td width="80" class="" align="center"><? echo "&nbsp;".change_date_format($typewise_brush_start_date) ;?></td>
														<td width="80" class="" align="center"><? echo $typewise_brush_start_time ;?></td>
														<td width="80" class="" align="center"> <? echo "&nbsp;".change_date_format($typewise_brush_end_date) ;?></td>
														<td width="80" class="" align="center"><? echo $typewise_brush_endtime;?></td>
														<td width="80" class="" align="center"><? if($typewise_brush_end_date!="")  echo $dyingtotype_brush_days_remian=datediff('d',$unload_date,$typewise_brush_end_date)-1;
								;?></td>
														<td width="80" class="" align="center"><? if($typewise_brush_end_date!=0) 
														echo floor($dyingtotypebrush_time_diff/60).":".$dyingtotypebrush_time_diff%60; 
														else echo " "; ?></td>
														 <td width="80" class="" align="center"><? echo $dyeing_result[$row["type_brush_result"]];?></td>
														
													  
													  <td width="80" class="" align="center"><? echo "&nbsp;".change_date_format($typewise_peach_start_date) ;?></td>
														<td width="80" class="" align="center"><? echo $typewise_peach_start_time ;?></td>
														<td width="80" class="" align="center"><? echo "&nbsp;".change_date_format($typewise_peach_end_date) ;?></td>
														<td width="80" class="" align="center"><? echo $typewise_peach_endtime;?></td>
														<td width="80" class="" align="center"><? if($typewise_peach_end_date!="")  echo $dyingtotype_peach_days_remian=datediff('d',$unload_date,$typewise_peach_end_date)-1;?></td>
														<td width="80" class="" align="center"><? if($typewise_peach_end_date!=0) 
														echo floor($dyingtotypepeach_time_diff/60).":".$dyingtotypepeach_time_diff%60; 
														else echo " "; ?></td>
													  
														<td width="80" class="" align="center"><? echo $dyeing_result[$row["type_peach_result"]];?></td>
														<td width="80" class="" align="center"> <? echo "&nbsp;".change_date_format($fin_prod_date)  ;?></td>
														<td width="80" class="" align="center"><? echo $fintime_cal ;?></td>
														<td width="80" class="" align="center"><?  if($fin_prod_date!="")  echo $dyingtofinprod_days_remian=datediff('d',$unload_date,$fin_prod_date)-1;?></td>
														
														<td width="80" class="" align="center"><?  if($fin_prod_date!=0) 
														echo floor($dyingtotypefinprod_time_diff/60).":".$dyingtotypefinprod_time_diff%60; 
														else echo " ";?></td>
														
														<td width="80" align="right"><?  echo number_format($fin_prod_qc_qnty_kg,2);?></td>
														<td width="80" align="right"><? echo number_format($fin_prod_qc_qnty_yds,2)  ;?></td>
														<td width="80" class="" align="center"><? echo "&nbsp;".change_date_format($fin_deli_date);?></td>
														  <td width="80" class="" align="center"><? echo $deli_fintime_cal ;?></td>
														<td width="80" class="" align="center"><?  if($fin_deli_date!="")  echo $dyingtofindeli_days_remian=datediff('d',$unload_date,$fin_deli_date)-1;?></td>
														<td width="80" class="" align="center"><?  if($fin_deli_date!=0) 
														echo floor($dyingtotypeDelifinprod_time_diff/60).":".$dyingtotypeDelifinprod_time_diff%60; 
														else echo " ";?></td>
														  <td width="80" align="right"><? echo number_format($fin_deli_qnty_kg,2) ;?></td>
														<td width="80" align="right"><? echo number_format($fin_deli_qnty_yds,2)  ;?></td>
														<td width="80" class="" align="center"><?   if($fin_deli_date!="")  echo $dyingtodeli_days_remian=datediff('d',$row["batch_date"],$fin_deli_date)-1;?></td>
														<td width="80" class="" align="center"><?  if($fin_deli_date!=0) 
														echo floor($batchtoDelifinprod_time_diff/60).":".$batchtoDelifinprod_time_diff%60; 
														else echo " " ;?></td>
														<td width="80" class="" align="center"><? if($fin_deli_date!="")  echo $dyingtodeli_days_remian=datediff('d',$unload_date,$fin_deli_date)-1 ;?></td>
													  
														<td width="" align="center" title="Unload Date Time=<? echo $unload_date;?>" class=""><?  if($fin_deli_date!=0) 
														echo floor($dyeingtoDelifinprod_time_diff/60).":".$dyeingtoDelifinprod_time_diff%60; 
														else echo " " ;;?></td>
														
													</tr>
													<?
													$p++;
													$tot_batch_qty+= $row["batch_qty"];
													$tot_fin_prod_qc_qnty_kg+= $fin_prod_qc_qnty_kg;
													$tot_fin_prod_qc_qnty_yds+= $fin_prod_qc_qnty_yds;
													$tot_fin_deli_qnty_kg+= $fin_deli_qnty_kg;
													$tot_fin_deli_qnty_yds+= $fin_deli_qnty_yds;
											
										}
							
									?>
						</tbody>
			   
					</table>
					</div>
			   <table width="<? echo $width; ?>" border="1" cellpadding="2" cellspacing="0" class="tbl_bottom" rules="all">
				
				<tr>
				<td colspan="8" width="620" align="left">Total</td>
				<td align="right" width="80"><? echo number_format($tot_batch_qty,0);?> </td>
				<td colspan="80"  width="6400"></td>
				<td align="right" width="80"><? echo number_format($tot_fin_prod_qc_qnty_kg,2);?> </td>
				<td align="right" width="80"><? echo number_format($tot_fin_prod_qc_qnty_yds,2);?> </td>
				<td align="right" width="80"><? //echo number_format($tot_fin_prod_qc_qnty_yds,0);?> </td>
				<td colspan="3" width="240"></td>
				<td align="right" width="80"><? echo number_format($tot_fin_deli_qnty_kg,2);?> </td>
				<td align="right" width="80"><? echo number_format($tot_fin_deli_qnty_yds,2);?> </td>
				<td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
				<td colspan="4" width="240"></td>
				<tr/>
				</table>
				
				
				</div>
				<?
			 
		 
		  
}
else //Report 2
{ 
    
   
        $dyeing_sql_batch="SELECT c.id as batch_id,c.batch_no, c.extention_no as  batch_ext_no,b.item_description,b.batch_qnty,b.prod_id,b.roll_no as no_of_roll,c.extention_no,c.sales_order_no,c.sales_order_id,c.booking_no,c.color_id,c.color_range_id,c.batch_date,c.batch_weight,c.booking_without_order,c.insert_date as batch_date_time,c.update_date,c.dur_req_hr,c.dur_req_min,
d.buyer_id as fso_buyer,d.style_ref_no,d.within_group,d.booking_type,d.booking_id,d.po_buyer from pro_batch_create_mst c,pro_batch_create_dtls b,fabric_sales_order_mst d where  c.id=b.mst_id and d.id=c.sales_order_id and c.is_sales=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $sales_cond $batch_year_cond $batch_cond2   $batch_prod_date_cond $buyer_cond1 order by c.id asc";//die;
	$dyeing_batch_data = sql_select($dyeing_sql_batch);
	
	//$all_batch_id="";
	foreach( $dyeing_batch_data as $row )
	{
		if($row[csf("update_date")]!='') $batch_dateTime=$row[csf("update_date")];
		else $batch_dateTime=$row[csf("batch_date_time")];
		$all_to_batch_arr[$row[csf("batch_id")]]=$row[csf("batch_id")];
		$batch_hr_min=$row[csf("dur_req_hr")].":".$row[csf("dur_req_min")].':'.'00';
		$batch_insert_time=explode(" ",$batch_dateTime);
		$batch_time_convert=$batch_insert_time[1].' '.$batch_insert_time[2];
		
			
		$item_description=explode(",",$row[csf("item_description")]);
		$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_no"]=$row[csf("batch_no")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["color_id"]=$row[csf("color_id")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["booking_type"]=$row[csf("booking_type")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["booking_id"]=$row[csf("booking_id")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["end_date"]=$row[csf("end_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["extention_no"]=$row[csf("extention_no")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_hr_min"]=$batch_date_time;//$row[csf("dur_req_hr")].":".$row[csf("dur_req_min")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_qty"]+=$row[csf("batch_qnty")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_wgt"]=$row[csf("batch_weight")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["sales_order_no"]=$row[csf("sales_order_no")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["color_range_id"]=$row[csf("color_range_id")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["const_composition"].=$item_description[0].',';
		$batch_wise_dying_arr[$row[csf("batch_id")]]["booking_no"]=$row[csf("booking_no")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["entry_form"]=$row[csf("entry_form")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_date"]=$row[csf("batch_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["insert_date"]=$row[csf("insert_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["booking_without_order"]=$row[csf("booking_without_order")];
		
		if($row[csf("booking_id")]!="")
		{
		$booking_id_arr[$row[csf("booking_id")]] = $row[csf("booking_id")];
		}
		$is_dyeing_done[$row[csf("batch_id")]] = $row[csf("batch_id")];
		$load_unload_time_arr[$row[csf("batch_id")]]["unload_machine_id"]=$row[csf("machine_id")];
		$load_unload_time_arr[$row[csf("batch_id")]]["unload_result"].=$row[csf("result")].',';
		//echo $row[csf("ltb_btb_id")].'ff';
		$load_unload_time_arr[$row[csf("batch_id")]]["unload_ltb_btb_id"]=$ltb_btb[$row[csf("ltb_btb_id")]];
		$load_unload_time_arr[$row[csf("batch_id")]]["const_composition"]=$row[csf("item_description")];
		$load_unload_time_arr[$row[csf("batch_id")]]["result"]=$dyeing_result[$row[csf("result")]];
		$load_unload_time_arr[$row[csf("batch_id")]]["unload_shift_name"]=$shift_name[$row[csf("shift_name")]].',';
		$load_unload_time_arr[$row[csf("batch_id")]]["end_date"].=$row[csf("end_date")].',';
		$load_unload_time_arr[$row[csf("batch_id")]]["process_time"].=$row[csf("end_hours")].":".$row[csf("end_minutes")].',';
		
		$load_unload_time_arr2[$row[csf("batch_id")]][$row[csf("end_date")]]["hr_min"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$load_unload_time_arr2[$row[csf("batch_id")]][$row[csf("end_date")]]["shift_name"]=$shift_name[$row[csf("shift_name")]];
		
		$all_batch_arr[$row[csf("batch_id")]]=$row[csf("batch_id")];
	//	if($all_batch_id=="") $all_batch_id=$row[csf("batch_id")];else $all_batch_id.=",".$row[csf("batch_id")];
		
		if($row[csf("within_group")]==2)
		{
			$booking_data_arr2[$row[csf("sales_order_no")]]["buyer_id"]=$buyer_list[$row[csf("fso_buyer")]];
		}
		else
		{
			$booking_data_arr[$row[csf("booking_no")]]["buyer_id"]=$buyer_list[$row[csf("po_buyer")]];
		}
		$booking_data_arr2[$row[csf("sales_order_no")]]["style_ref_no"]=$row[csf("style_ref_no")];
		$booking_data_arr[$row[csf("booking_no")]]["job_no"]=$row[csf("job_no")];
		$booking_data_arr2[$row[csf("sales_order_no")]]["within_group"]=$row[csf("within_group")];
		
		$batch_wise_dying_arr[$row[csf("batch_id")]]["end_date"]=$row[csf("end_date")];
		
	}
	unset($dyeing_batch_data);
	
	 $dyeing_sql_unload="SELECT a.id as mst_id,a.machine_id,a.ltb_btb_id,a.shift_name,a.fabric_type,a.result,a.service_company,a.floor_id,a.service_source,a.insert_date,a.remarks,a.batch_id, a.result,a.batch_no,a.process_id,a.entry_form,a.start_hours,a.start_minutes,a.production_date as end_date,a.load_unload_id, a.batch_ext_no,a.process_start_date,a.process_end_date,a.end_minutes,a.end_hours,b.batch_qty,b.production_qty,b.prod_id,b.no_of_roll,c.extention_no,c.sales_order_no,c.sales_order_id,c.booking_no,c.batch_date,c.batch_weight,c.booking_without_order,c.insert_date as batch_date_time,c.update_date,c.dur_req_hr,c.dur_req_min,
d.buyer_id as fso_buyer,d.style_ref_no,d.within_group,d.booking_type,d.booking_id,d.po_buyer from pro_batch_create_mst c,pro_fab_subprocess a,pro_fab_subprocess_dtls b,fabric_sales_order_mst d where  a.id=b.mst_id and c.id=a.batch_id and d.id=c.sales_order_id and c.is_sales=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and a.load_unload_id=2 $sales_cond $batch_year_cond $batch_cond3 $prod_comp_cond $prod_date_cond $buyer_cond1 order by a.id asc";
	$dyeing_unload_data = sql_select($dyeing_sql_unload);
	
	$all_batch_id="";
	foreach( $dyeing_unload_data as $row )
	{
		if($row[csf("update_date")]!='') $batch_dateTime=$row[csf("update_date")];
		else $batch_dateTime=$row[csf("batch_date_time")];
		
		$batch_hr_min=$row[csf("dur_req_hr")].":".$row[csf("dur_req_min")].':'.'00';
		$batch_insert_time=explode(" ",$batch_dateTime);
		$batch_time_convert=$batch_insert_time[1].' '.$batch_insert_time[2];
		
			
			
		$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_no"]=$row[csf("batch_no")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["booking_type"]=$row[csf("booking_type")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["booking_id"]=$row[csf("booking_id")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["end_date"]=$row[csf("end_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["extention_no"]=$row[csf("extention_no")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_hr_min"]=$batch_date_time;//$row[csf("dur_req_hr")].":".$row[csf("dur_req_min")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_qty"]=$row[csf("batch_weight")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_wgt"]=$row[csf("batch_weight")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["sales_order_no"]=$row[csf("sales_order_no")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["booking_no"]=$row[csf("booking_no")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["entry_form"]=$row[csf("entry_form")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_date"]=$row[csf("batch_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["insert_date"]=$row[csf("insert_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["booking_without_order"]=$row[csf("booking_without_order")];
		
		if($row[csf("booking_id")]!="")
		{
		$booking_id_arr[$row[csf("booking_id")]] = $row[csf("booking_id")];
		}
		$is_dyeing_done[$row[csf("batch_id")]] = $row[csf("batch_id")];
		$load_unload_time_arr[$row[csf("batch_id")]]["unload_machine_id"]=$row[csf("machine_id")];
		$load_unload_time_arr[$row[csf("batch_id")]]["unload_result"].=$row[csf("result")].',';
		//echo $row[csf("ltb_btb_id")].'ff';
		$load_unload_time_arr[$row[csf("batch_id")]]["unload_ltb_btb_id"]=$ltb_btb[$row[csf("ltb_btb_id")]];
		$load_unload_time_arr[$row[csf("batch_id")]]["fabric_type"]=$fabric_type_for_dyeing[$row[csf("fabric_type")]];
		$load_unload_time_arr[$row[csf("batch_id")]]["result"]=$dyeing_result[$row[csf("result")]];
		$load_unload_time_arr[$row[csf("batch_id")]]["unload_shift_name"]=$shift_name[$row[csf("shift_name")]].',';
		$load_unload_time_arr[$row[csf("batch_id")]]["end_date"].=$row[csf("end_date")].',';
		$load_unload_time_arr[$row[csf("batch_id")]]["process_time"].=$row[csf("end_hours")].":".$row[csf("end_minutes")].',';
		
		$load_unload_time_arr2[$row[csf("batch_id")]][$row[csf("end_date")]]["hr_min"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$load_unload_time_arr2[$row[csf("batch_id")]][$row[csf("end_date")]]["shift_name"]=$shift_name[$row[csf("shift_name")]];
		
		$all_batch_arr[$row[csf("batch_id")]]=$row[csf("batch_id")];
		if($all_batch_id=="") $all_batch_id=$row[csf("batch_id")];else $all_batch_id.=",".$row[csf("batch_id")];
		
		if($row[csf("within_group")]==2)
		{
			$booking_data_arr2[$row[csf("sales_order_no")]]["buyer_id"]=$buyer_list[$row[csf("fso_buyer")]];
		}
		else
		{
			$booking_data_arr[$row[csf("booking_no")]]["buyer_id"]=$buyer_list[$row[csf("po_buyer")]];
		}
		$booking_data_arr2[$row[csf("sales_order_no")]]["style_ref_no"]=$row[csf("style_ref_no")];
		$booking_data_arr[$row[csf("booking_no")]]["job_no"]=$row[csf("job_no")];
		$booking_data_arr2[$row[csf("sales_order_no")]]["within_group"]=$row[csf("within_group")];
		
		$batch_wise_dying_arr[$row[csf("batch_id")]]["end_date"]=$row[csf("end_date")];
		
	}
	unset($dyeing_unload_data);
	
	
   $dyeing_sql_unload="SELECT a.id as mst_id,a.machine_id,a.ltb_btb_id,a.shift_name,a.fabric_type,a.result,a.service_company,a.floor_id,a.service_source,a.insert_date,a.remarks,a.batch_id, a.result,a.batch_no,a.process_id,a.entry_form,a.start_hours,a.start_minutes,a.production_date as end_date,a.load_unload_id, a.batch_ext_no,a.process_start_date,a.process_end_date,a.end_minutes,a.end_hours,b.batch_qty,b.production_qty,b.prod_id,b.no_of_roll,c.extention_no,c.sales_order_no,c.sales_order_id,c.booking_no,c.batch_date,c.batch_weight,c.booking_without_order,c.insert_date as batch_date_time,c.update_date,c.dur_req_hr,c.dur_req_min,
d.buyer_id as fso_buyer,d.style_ref_no,d.within_group,d.booking_type,d.booking_id,d.po_buyer from pro_batch_create_mst c,pro_fab_subprocess a,pro_fab_subprocess_dtls b,fabric_sales_order_mst d where  a.id=b.mst_id and c.id=a.batch_id and d.id=c.sales_order_id and c.is_sales=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and a.load_unload_id=2 $sales_cond $batch_year_cond $batch_cond3 $prod_comp_cond $prod_date_cond $buyer_cond1 order by a.id asc";
	$dyeing_unload_data = sql_select($dyeing_sql_unload);
	
	$all_batch_id="";
	foreach( $dyeing_unload_data as $row )
	{
		if($row[csf("update_date")]!='') $batch_dateTime=$row[csf("update_date")];
		else $batch_dateTime=$row[csf("batch_date_time")];
		
		$batch_hr_min=$row[csf("dur_req_hr")].":".$row[csf("dur_req_min")].':'.'00';
		$batch_insert_time=explode(" ",$batch_dateTime);
		$batch_time_convert=$batch_insert_time[1].' '.$batch_insert_time[2];
		
			
			
		$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_no"]=$row[csf("batch_no")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["booking_type"]=$row[csf("booking_type")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["booking_id"]=$row[csf("booking_id")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["end_date"]=$row[csf("end_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["extention_no"]=$row[csf("extention_no")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_hr_min"]=$batch_date_time;//$row[csf("dur_req_hr")].":".$row[csf("dur_req_min")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_qty"]=$row[csf("batch_weight")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_wgt"]=$row[csf("batch_weight")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["sales_order_no"]=$row[csf("sales_order_no")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["booking_no"]=$row[csf("booking_no")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["entry_form"]=$row[csf("entry_form")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_date"]=$row[csf("batch_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["insert_date"]=$row[csf("insert_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["booking_without_order"]=$row[csf("booking_without_order")];
		
		if($row[csf("booking_id")]!="")
		{
		$booking_id_arr[$row[csf("booking_id")]] = $row[csf("booking_id")];
		}
		$is_dyeing_done[$row[csf("batch_id")]] = $row[csf("batch_id")];
		$load_unload_time_arr[$row[csf("batch_id")]]["unload_machine_id"]=$row[csf("machine_id")];
		$load_unload_time_arr[$row[csf("batch_id")]]["unload_result"].=$row[csf("result")].',';
		//echo $row[csf("ltb_btb_id")].'ff';
		$load_unload_time_arr[$row[csf("batch_id")]]["unload_ltb_btb_id"]=$ltb_btb[$row[csf("ltb_btb_id")]];
		$load_unload_time_arr[$row[csf("batch_id")]]["fabric_type"]=$fabric_type_for_dyeing[$row[csf("fabric_type")]];
		$load_unload_time_arr[$row[csf("batch_id")]]["result"]=$dyeing_result[$row[csf("result")]];
		$load_unload_time_arr[$row[csf("batch_id")]]["unload_shift_name"]=$shift_name[$row[csf("shift_name")]].',';
		$load_unload_time_arr[$row[csf("batch_id")]]["end_date"].=$row[csf("end_date")].',';
		$load_unload_time_arr[$row[csf("batch_id")]]["process_time"].=$row[csf("end_hours")].":".$row[csf("end_minutes")].',';
		
		$load_unload_time_arr2[$row[csf("batch_id")]][$row[csf("end_date")]]["hr_min"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$load_unload_time_arr2[$row[csf("batch_id")]][$row[csf("end_date")]]["shift_name"]=$shift_name[$row[csf("shift_name")]];
		
		$all_batch_arr[$row[csf("batch_id")]]=$row[csf("batch_id")];
		if($all_batch_id=="") $all_batch_id=$row[csf("batch_id")];else $all_batch_id.=",".$row[csf("batch_id")];
		
		if($row[csf("within_group")]==2)
		{
			$booking_data_arr2[$row[csf("sales_order_no")]]["buyer_id"]=$buyer_list[$row[csf("fso_buyer")]];
		}
		else
		{
			$booking_data_arr[$row[csf("booking_no")]]["buyer_id"]=$buyer_list[$row[csf("po_buyer")]];
		}
		$booking_data_arr2[$row[csf("sales_order_no")]]["style_ref_no"]=$row[csf("style_ref_no")];
		$booking_data_arr[$row[csf("booking_no")]]["job_no"]=$row[csf("job_no")];
		$booking_data_arr2[$row[csf("sales_order_no")]]["within_group"]=$row[csf("within_group")];
		
		$batch_wise_dying_arr[$row[csf("batch_id")]]["end_date"]=$row[csf("end_date")];
		
	}
	unset($dyeing_unload_data);
	$batch_ids=count($all_batch_arr);
	if($db_type==2 && $batch_ids>1000)
	{
		$batch_cond_for=" and (";
		$batIdsArr=array_chunk($all_batch_arr,999);
		foreach($batIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$batch_cond_for.=" a.batch_id in($ids) or";
		}
		$batch_cond_for=chop($batch_cond_for,'or ');
		$batch_cond_for.=")";
	}
	else
	{
		$batch_cond_for=" and a.batch_id in(".implode(",",$all_batch_arr).")";
	}
	
	
	
	//$all_batch_ids=explode(",",$all_batch_id);
	
$load_unload_sql="SELECT a.id as mst_id,a.result,a.service_company,a.shift_name,a.floor_id,a.fabric_type,a.result,a.service_source,a.insert_date,a.batch_id,a.batch_no,a.process_id,a.entry_form,a.start_hours,a.start_minutes,a.production_date as end_date,a.load_unload_id, a.batch_ext_no,a.process_start_date,a.process_end_date,a.end_minutes,a.end_hours,b.production_qty,b.batch_qty,b.prod_id,b.no_of_roll,c.sales_order_no,c.sales_order_id,c.booking_no,c.extention_no,c.batch_date,c.insert_date,c.booking_without_order,c.dur_req_hr,c.dur_req_min from pro_batch_create_mst c,pro_fab_subprocess a,pro_fab_subprocess_dtls b where  a.id=b.mst_id and c.id=a.batch_id and a.load_unload_id=1 and a.entry_form=35 and c.is_sales=1 and a.status_active=1 and b.status_active=1  and c.status_active=1  $sales_cond $batch_year_cond $batch_cond3 $prod_comp_cond $batch_cond_for order by a.id,a.batch_id desc";
	$dying_result_data = sql_select($load_unload_sql);
	foreach($dying_result_data as $row )
	{
		$load_time_arr[$row[csf("batch_id")]]["prod_qty"]+=$row[csf("production_qty")];
		$load_time_arr[$row[csf("batch_id")]]["end_date"].=$row[csf("process_end_date")].',';
		$load_time_arr[$row[csf("batch_id")]]["load_shift_name"].=$shift_name[$row[csf("shift_name")]].',';
		//$load_time_arr[$row[csf("batch_id")]]["start_date"]=$row[csf("end_date")];
		$load_time_arr2[$row[csf("batch_id")]][$row[csf("process_end_date")]]["end_time"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		//$load_time_arr2[$row[csf("batch_id")]]["end_date"]=$row[csf("end_date")];
		$load_time_arr2[$row[csf("batch_id")]][$row[csf("process_end_date")]]["load_shift_name"]=$row[csf("load_shift_name")];
	}
	unset($dying_result_data);
	
	
	
	 $prod_sql="SELECT a.id as mst_id,a.machine_id,a.shift_name,a.result,c.insert_date as batch_date_time,c.update_date,a.service_company,a.floor_id,a.service_source,a.insert_date,a.remarks,a.batch_id,a.previous_process, a.result,a.batch_no,a.process_id,a.remarks,a.entry_form,a.start_hours,a.start_minutes,a.production_date as end_date,a.load_unload_id, a.batch_ext_no,a.process_start_date,a.process_end_date,a.end_minutes,a.end_hours,b.production_qty,b.batch_qty,b.prod_id,b.const_composition,b.no_of_roll,c.sales_order_no,c.sales_order_id,c.booking_no,c.extention_no,c.color_id,c.color_range_id,c.batch_date,c.insert_date,c.booking_without_order,c.dur_req_hr,c.dur_req_min,c.batch_weight from pro_batch_create_mst c,pro_fab_subprocess a,pro_fab_subprocess_dtls b where  a.id=b.mst_id and c.id=a.batch_id and a.entry_form!=35 and c.is_sales=1 and a.status_active=1 and b.status_active=1  and c.status_active=1  $sales_cond $batch_year_cond $batch_cond3 $prod_comp_cond $batch_cond_for order by a.id,a.batch_id desc";
	
	
	 $result_data = sql_select($prod_sql);
	$process_brush_arr_check=array(68);
	$process_rotation_arr_check=array(206);
	$process_wash_arr_check=array(64);
	$process_scouring_arr_check=array(60);
	foreach($result_data as $row )
	{
		//$is_dyeing_done[$row[csf("batch_id")]] = $row[csf("batch_id")];
		//$load_unload_time_arr[$row[csf("batch_id")]]["end_date"]=$row[csf("end_date")];
		
		
		$all_sales_id_arr[$row[csf("sales_order_id")]]=$row[csf("sales_order_id")];
		if($all_batch_id=="") $all_batch_id=$row[csf("batch_id")];else $all_batch_id.=",".$row[csf("batch_id")];
		//$process_ids=explode(",",$row[csf("process_id")]);
		if($row[csf("entry_form")]==32)//HeatSet
		{
		$batch_wise_heat_arr[$row[csf("batch_id")]][$row[csf("end_date")]]["heatset_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$batch_wise_heat_arr[$row[csf("batch_id")]][$row[csf("process_start_date")]]["heatset_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
		$batch_wise_heat_arr[$row[csf("batch_id")]][$row[csf("process_start_date")]]["rotation_shift_name"]=$shift_name[$row[csf("rotation_shift_name")]];
		$batch_wise_heat_arr[$row[csf("batch_id")]][$row[csf("end_date")]]["end_heat_shift_name"]=$shift_name[$row[csf("shift_name")]];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["heatset_start_date"].=$row[csf("process_start_date")].',';
		$batch_wise_dying_arr[$row[csf("batch_id")]]["heatset_end_date"].=$row[csf("end_date")].',';
		$batch_wise_dying_arr[$row[csf("batch_id")]]["heatset_prod_qty"]+=$row[csf("production_qty")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["heat_mc_id"]=$row[csf("machine_id")];
		}
		else if($row[csf("entry_form")]==30)//Sliting //
		{
		$batch_wise_dying_arr[$row[csf("batch_id")]]["sliting_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["sliting_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["sliting_start_date"]=$row[csf("process_start_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["sliting_end_date"]=$row[csf("end_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["sliting_mc_id"]=$row[csf("machine_id")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["sliting_shift_name"]=$shift_name[$row[csf("shift_name")]];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["sliting_prod_qty"]+=$row[csf("production_qty")];
		}
		else if($row[csf("entry_form")]==48)//Stentering
		{
	//	echo $row[csf("end_date")].'='.$row[csf("end_hours")].":".$row[csf("end_minutes")].'='.$row[csf("process_start_date")].'='.$row[csf("start_hours")].":".$row[csf("start_minutes")].'<br>';
		$batch_wise_dying_arr[$row[csf("batch_id")]]["stenter_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["stenter_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["stenter_start_date"]=$row[csf("process_start_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["stenter_end_date"]=$row[csf("end_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["stenter_mc_id"]=$row[csf("machine_id")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["stenter_shift_name"]=$shift_name[$row[csf("shift_name")]];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["stenter_prod_qty"]+=$row[csf("production_qty")];
		}
		else if($row[csf("entry_form")]==31)//Drying
		{
			//echo  $row[csf("previous_process")].'f';
		$batch_wise_dry_arr[$row[csf("batch_id")]][$row[csf("end_date")]]["dry_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$batch_wise_dry_arr[$row[csf("batch_id")]][$row[csf("process_start_date")]]["dry_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
		$batch_wise_dry_arr[$row[csf("batch_id")]][$row[csf("process_start_date")]]["start_shift_name"]=$shift_name[$row[csf("shift_name")]];
		$batch_wise_dry_arr[$row[csf("batch_id")]][$row[csf("end_date")]]["end_shift_name"]=$shift_name[$row[csf("shift_name")]];
		
		$batch_wise_dying_arr[$row[csf("batch_id")]]["dry_start_date"].=$row[csf("process_start_date")].',';
		$batch_wise_dying_arr[$row[csf("batch_id")]]["dry_end_date"].=$row[csf("end_date")].',';
		$batch_wise_dying_arr[$row[csf("batch_id")]]["dry_result"]=$row[csf("result")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["dry_shift_name"]=$shift_name[$row[csf("shift_name")]];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["dry_mc_id"]=$row[csf("machine_id")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["dry_prod_qty"]+=$row[csf("production_qty")];
		}
		else if($row[csf("entry_form")]==33)//Compacting
		{
		$batch_wise_comp_arr[$row[csf("batch_id")]][$row[csf("end_date")]]["comp_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$batch_wise_comp_arr[$row[csf("batch_id")]][$row[csf("process_start_date")]]["comp_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
		$batch_wise_comp_arr[$row[csf("batch_id")]][$row[csf("process_start_date")]]["start_shift_name"]=$shift_name[$row[csf("shift_name")]];
		$batch_wise_comp_arr[$row[csf("batch_id")]][$row[csf("end_date")]]["end_shift_name"]=$shift_name[$row[csf("shift_name")]];
		
		$batch_wise_dying_arr[$row[csf("batch_id")]]["comp_start_date"].=$row[csf("process_start_date")].',';
		$batch_wise_dying_arr[$row[csf("batch_id")]]["comp_end_date"].=$row[csf("end_date")].',';
		$batch_wise_dying_arr[$row[csf("batch_id")]]["comp_result"]=$row[csf("result")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["comp_shift_name"]=$shift_name[$row[csf("shift_name")]];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["comp_mc_id"]=$row[csf("machine_id")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["comp_prod_qty"]+=$row[csf("production_qty")];
		}
		else if($row[csf("entry_form")]==47)//Signeing
		{
		$batch_wise_singeing_arr[$row[csf("batch_id")]][$row[csf("end_date")]]["singeing_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$batch_wise_singeing_arr[$row[csf("batch_id")]][$row[csf("process_start_date")]]["singeing_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
		$batch_wise_singeing_arr[$row[csf("batch_id")]][$row[csf("process_start_date")]]["start_shift_name"]=$shift_name[$row[csf("shift_name")]];
		$batch_wise_singeing_arr[$row[csf("batch_id")]][$row[csf("end_date")]]["end_shift_name"]=$shift_name[$row[csf("shift_name")]];
		
		$batch_wise_dying_arr[$row[csf("batch_id")]]["singeing_start_date"].=$row[csf("process_start_date")].',';
		$batch_wise_dying_arr[$row[csf("batch_id")]]["singeing_end_date"].=$row[csf("end_date")].',';
		$batch_wise_dying_arr[$row[csf("batch_id")]]["singeing_result"]=$row[csf("result")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["singeing_shift_name"]=$shift_name[$row[csf("shift_name")]];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["singeing_mc_id"]=$row[csf("machine_id")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["singeing_prod_qty"]+=$row[csf("production_qty")];
		}
		else if($row[csf("entry_form")]==34 && $row[csf("process_id")]==68)//Brush
		{
			//echo $row[csf("production_qty")].", ";
		$batch_wise_brush_arr[$row[csf("batch_id")]][$row[csf("end_date")]]["brush_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$batch_wise_brush_arr[$row[csf("batch_id")]][$row[csf("process_start_date")]]["brush_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
		$batch_wise_brush_arr[$row[csf("batch_id")]][$row[csf("process_start_date")]]["start_shift_name"]=$shift_name[$row[csf("shift_name")]];
		$batch_wise_brush_arr[$row[csf("batch_id")]][$row[csf("end_date")]]["end_shift_name"]=$shift_name[$row[csf("shift_name")]];
		
		$batch_wise_dying_arr[$row[csf("batch_id")]]["brush_start_date"].=$row[csf("process_start_date")].',';
		$batch_wise_dying_arr[$row[csf("batch_id")]]["brush_end_date"].=$row[csf("end_date")].',';
		$batch_wise_dying_arr[$row[csf("batch_id")]]["brush_result"]=$row[csf("result")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["brush_shift_name"]=$shift_name[$row[csf("shift_name")]];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["brush_mc_id"]=$row[csf("machine_id")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["brush_prod_qty"]+=$row[csf("production_qty")];
		}
		
		
		
		
		else if($row[csf("entry_form")]==34 && $row[csf("result")]>0)//SpecilaFinish
		{
			
			if(in_array($row[csf("process_id")],$process_scouring_arr_check))//Prod Type scouring 
			{
				
				//echo $row[csf("process_id")]."G";
			$batch_wise_dying_arr[$row[csf("batch_id")]]["type_sourcing_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
			$batch_wise_dying_arr[$row[csf("batch_id")]]["type_sourcing_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
			$batch_wise_dying_arr[$row[csf("batch_id")]]["type_sourcing_start_date"]=$row[csf("process_start_date")];
			$batch_wise_dying_arr[$row[csf("batch_id")]]["type_sourcing_end_date"]=$row[csf("end_date")];
			$batch_wise_dying_arr[$row[csf("batch_id")]]["sourcing_mc_id"]=$row[csf("machine_id")];
			$batch_wise_dying_arr[$row[csf("batch_id")]]["sourcing_shift_name"]=$shift_name[$row[csf("shift_name")]];
			$batch_wise_dying_arr[$row[csf("batch_id")]]["sourcing_prod_qty"]+=$row[csf("production_qty")];
			}
			else if(in_array($row[csf("process_id")],$process_rotation_arr_check))//Prod Type Rotaion $process_wash_arr_check
			{
				
				//echo $row[csf("process_id")]."G";
			$batch_wise_rotaion_arr[$row[csf("batch_id")]][$row[csf("end_date")]]["rotaion_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
			$batch_wise_rotaion_arr[$row[csf("batch_id")]][$row[csf("process_start_date")]]["rotaion_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
			$batch_wise_rotaion_arr[$row[csf("batch_id")]][$row[csf("process_start_date")]]["rotation_shift_name"]=$shift_name[$row[csf("rotation_shift_name")]];

			$batch_wise_dying_arr[$row[csf("batch_id")]]["rotaion_start_date"].=$row[csf("process_start_date")].',';
			$batch_wise_dying_arr[$row[csf("batch_id")]]["rotaion_end_date"].=$row[csf("end_date")].',';
			$batch_wise_dying_arr[$row[csf("batch_id")]]["rotaion_result"]=$row[csf("result")];
			$batch_wise_dying_arr[$row[csf("batch_id")]]["rotaion_mc_id"]=$row[csf("machine_id")];
			$batch_wise_dying_arr[$row[csf("batch_id")]]["rotation_shift_name"]=$shift_name[$row[csf("shift_name")]];
			$batch_wise_dying_arr[$row[csf("batch_id")]]["rotaion_prod_qty"]+=$row[csf("production_qty")];
			}else if(in_array($row[csf("process_id")],$process_wash_arr_check))//Prod Type wash 
			{
				
				//echo $row[csf("process_id")]."G";
			$batch_wise_wash_arr[$row[csf("batch_id")]][$row[csf("end_date")]]["wash_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
			$batch_wise_wash_arr[$row[csf("batch_id")]][$row[csf("process_start_date")]]["wash_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
			$batch_wise_wash_arr[$row[csf("batch_id")]][$row[csf("process_start_date")]]["wash_shift_name"]=$shift_name[$row[csf("shift_name")]];

			$batch_wise_dying_arr[$row[csf("batch_id")]]["wash_start_date"].=$row[csf("process_start_date")].',';
			$batch_wise_dying_arr[$row[csf("batch_id")]]["wash_end_date"].=$row[csf("end_date")].',';
			$batch_wise_dying_arr[$row[csf("batch_id")]]["wash_result"]=$row[csf("result")];
			$batch_wise_dying_arr[$row[csf("batch_id")]]["wash_mc_id"]=$row[csf("machine_id")];
			$batch_wise_dying_arr[$row[csf("batch_id")]]["wash_shift_name"]=$shift_name[$row[csf("shift_name")]];
			$batch_wise_dying_arr[$row[csf("batch_id")]]["wash_prod_qty"]+=$row[csf("production_qty")];
			}



		}
		
			
	

		
		
		$batch_wise_dying_arr[$row[csf("batch_id")]]["result"]=$row[csf("result")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["color_id"]=$row[csf("color_id")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["color_range_id"]=$row[csf("color_range_id")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["const_composition"].=$row[csf("const_composition")].',';
		$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_hr_min"]=$batch_date_time;//$row[csf("dur_req_hr")].":".$row[csf("dur_req_min")];
		//$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_min"]=$row[csf("dur_req_min")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_no"]=$row[csf("batch_no")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["end_date"]=$row[csf("end_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["extention_no"]=$row[csf("extention_no")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_qty"]=$row[csf("batch_qty")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_wgt"]=$row[csf("batch_weight")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["sales_order_no"]=$row[csf("sales_order_no")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["booking_no"]=$row[csf("booking_no")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["entry_form"]=$row[csf("entry_form")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_date"]=$row[csf("batch_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["insert_date"]=$row[csf("insert_date")];
		$batch_wise_dying_arr[$row[csf("batch_id")]]["booking_without_order"]=$row[csf("booking_without_order")];
		
		$all_to_batch_arr[$row[csf("batch_id")]]=$row[csf("batch_id")];
		
		
	}
	unset($result_data);//booking_id_arr
	$batchids=count($all_to_batch_arr);
	if($db_type==2 && $batchids>1000)
	{
		$batch_cond_for2=" and (";
		$batIdsArr=array_chunk($all_to_batch_arr,999);
		foreach($batIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$batch_cond_for2.=" b.batch_id in($ids) or";
		}
		$batch_cond_for2=chop($batch_cond_for2,'or ');
		$batch_cond_for2.=")";
	}
	else
	{
		$batch_cond_for2=" and b.batch_id in(".implode(",",$all_to_batch_arr).")";
	}
	//print_r($booking_id_arr);
	$batch_cond_for3=str_replace('b.batch_id','a.id',$batch_cond_for2);
	$batch_cond_for4=str_replace('b.batch_id','b.id',$batch_cond_for2);
	//echo $batch_cond_for3.'dDD';
	$bookids=count($booking_id_arr);
	if($db_type==2 && $bookids>1000)
	{
		$book_cond_for=" and (";
		$batIdsArr=array_chunk($booking_id_arr,999);
		foreach($batIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$book_cond_for.=" a.id in($ids) or";
		}
		$book_cond_for=chop($book_cond_for,'or ');
		$book_cond_for.=")";
	}
	else
	{
		$book_cond_for=" and a.id in(".implode(",",$booking_id_arr).")";
	}
	$sql_batch="SELECT a.id,b.prod_id,b.width_dia_type,b.body_part_id from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and  a.status_active=1 and  b.status_active=1  $batch_cond_for3" ;
	foreach(sql_select($sql_batch) as $row )
	{
			$batch_data_dtls_arr[$row[csf("id")]]["body_part_id"].=$body_part[$row[csf("body_part_id")]].',';
			$batch_data_dtls_arr[$row[csf("id")]]["width_dia_type"].=$fabric_typee[$row[csf("width_dia_type")]].',';
	}
	$yarncount = return_library_array("select id, yarn_count from  lib_yarn_count", 'id', 'yarn_count');
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$sql_mc=sql_select("select machine_no,id,prod_capacity from  lib_machine_name where is_deleted=0");
	foreach($sql_mc as $row)
	{
		$machine_name_arr[$row[csf('id')]]=$row[csf('machine_no')];
		if($row[csf('prod_capacity')]>0)
		{
		$machine_capacity_arr[$row[csf('id')]]=$row[csf('prod_capacity')];
		}
	}
	
	//$machine_name_arr = return_library_array("select machine_no,id from  lib_machine_name where is_deleted=0", "id", "machine_no");

	$cons_comp_sql = sql_select("select a.id, a.construction,c.composition_name, b.percent
		from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b, lib_composition_array c
		where a.id = b.mst_id and b.copmposition_id = c.id  and a.status_active=1 and b.status_active=1 and c.status_active=1");
	foreach ($cons_comp_sql as  $val)
	{
		$cons_comp_arr[$val[csf("id")]]["const"] = $val[csf("construction")];
		$cons_comp_arr[$val[csf("id")]]["compo"] =$val[csf("composition_name")] .",". $val[csf("percent")] . "% ";

	}
		$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');

	$sql_yarn_lot = "SELECT a.id,b.prod_id, b.po_id,d.brand_id, d.yarn_lot AS yarn_lot,d.yarn_count,d.febric_description_id as deter_id FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d WHERE a.id = b.mst_id AND b.roll_id = c.id AND c.dtls_id = d.id   AND b.status_active = 1 AND b.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0 $batch_cond_for3 GROUP BY a.id,b.prod_id,d.febric_description_id, d.brand_id,b.po_id,d.yarn_lot,d.yarn_count"; 
			$sql_yarn_lot_res = sql_select($sql_yarn_lot);
			$yarn_lot_arr=array();
			
	foreach($sql_yarn_lot_res as $rows)
	{
		//$yarn_lot_arr[$rows[csf('id')]]['lot'].=$rows[csf('yarn_lot')].',';
		$yarn_lot_arr[$rows[csf('id')]]['lot'].=$rows[csf('yarn_lot')].',';
		$yarn_lot_arr[$rows[csf('id')]]['brand_id'].=$brand_arr[$rows[csf('brand_id')]].',';
		$yarn_lot_arr[$rows[csf('id')]]['yarn_count'].=$rows[csf('yarn_count')].',';
		$yarn_lot_arr[$rows[csf('id')]]['compo'].=$cons_comp_arr[$rows[csf("deter_id")]]["compo"] .',';
	}
	unset($sql_yarn_lot_res);
	 $sql_sales_order= "SELECT  b.id as batch_id,b.batch_weight,b.batch_no,b.booking_no,b.sales_order_no,b.color_id,b.color_range_id,c.id as recipe_id,a.job_no,e.mst_id as req_id ,f.requ_no from  fabric_sales_order_mst a, pro_batch_create_mst b,pro_recipe_entry_mst c,dyes_chem_requ_recipe_att e,dyes_chem_issue_requ_dtls f where a.id=b.sales_order_id and c.batch_id=b.id and e.recipe_id=c.id and f.mst_id=e.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  ".where_con_using_array($all_batch_arr,0,'b.id')."   group by  b.id ,b.batch_no,b.booking_no,b.color_id,b.color_range_id,f.requ_no,c.id,b.sales_order_no,b.batch_weight,a.job_no,e.mst_id";
	$result_recipe_data=sql_select($sql_sales_order);
	foreach ($result_recipe_data as $row)
	{
		$recipe_batch_id_arr[$row[csf("batch_id")]]['recipe_id'].=$row[csf("recipe_id")].',';
		$recipe_batch_id_arr[$row[csf("batch_id")]]['requ_no'].=$row[csf("requ_no")].',';
		$recipe_batch_id_arr[$row[csf("batch_id")]]['requ_id'].=$row[csf("req_id")].',';
	//	if($all_req_ids=='') $all_req_ids=$value[csf("req_id")];else $all_req_ids.=",".$value[csf("req_id")];
		//if($all_recipe_ids=='') $all_recipe_ids=$value[csf("recipe_id")];else $all_recipe_ids.=",".$value[csf("recipe_id")];
	}
	unset($result_recipe_data);
	

//	print_r($batch_data_arr);
	
	
//echo "DDDDDD";die;
	/*echo "<pre>";
	echo $batch_cond_for;
	echo "</pre>";*/
	

	$booking_data_arr=array();
	$non_booking_sql="SELECT a.buyer_id,a.is_short,a.booking_no,a.booking_type from wo_non_ord_samp_booking_mst a where  a.status_active=1 and a.booking_type = 4 $book_cond_for" ;
	foreach(sql_select($non_booking_sql) as $row )
	{
		if($row[csf("booking_type")]==4)
		{
			//$booking_data_arr[$row[csf("booking_no")]]["type"]="Sample Without Order";
			$booking_data_arr[$row[csf("booking_no")]]["buyer_id"]=$buyer_list[$row[csf("buyer_id")]];
			if($row[csf("is_short")]==1)
			{
				$booking_data_arr[$row[csf("booking_no")]]["type"]="Main";
			}
			else 
			{
				$booking_data_arr[$row[csf("booking_no")]]["type"]="Short";
			}
		}
	}
	
	$main_booking_sql="SELECT a.is_short,a.buyer_id,a.booking_no,a.booking_type from wo_booking_mst a where  a.status_active=1 and a.booking_type = 1 $book_cond_for" ;
	foreach(sql_select($main_booking_sql) as $row )
	{
		
			//$booking_data_arr[$row[csf("booking_no")]]["type"]="Sample Without Order";
			$booking_data_arr[$row[csf("booking_no")]]["buyer_id"]=$buyer_list[$row[csf("buyer_id")]];
			if($row[csf("is_short")]==1)
			{
				$booking_data_arr[$row[csf("booking_no")]]["type"]="Main";
			}
			else 
			{
				$booking_data_arr[$row[csf("booking_no")]]["type"]="Short";
			}
		
	}
	//print_r($booking_data_arr);
	
	$sales_ids=count($all_sales_id_arr);
	if($db_type==2 && $sales_ids>1000)
	{
		$sales_cond_for=" and (";
		$salesIdsArr=array_chunk($all_batch_arr,999);
		foreach($salesIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$sales_cond_for_in.=" c.id in($ids) or";
		}
		$sales_cond_for_in=chop($sales_cond_for_in,'or ');
		$sales_cond_for_in.=")";
	}
	else
	{
		$sales_cond_for_in=" and c.id in(".implode(",",$all_sales_id_arr).")";
	}
	$fin_fab_sql= sql_select("select c.insert_date,c.update_date,c.receive_date, b.batch_id,b.shift_name,b.uom,b.receive_qnty from  pro_finish_fabric_rcv_dtls b,inv_receive_master c where  b.mst_id=c.id and c.entry_form in(7,37) and c.status_active=1 and c.booking_without_order=0 and c.entry_form in(7,37)  and b.status_active=1 and b.is_deleted=0 $batch_cond_for2 order by b.batch_id");
	//echo "select c.insert_date,c.update_date,c.receive_date, b.batch_id,b.uom,b.receive_qnty from  pro_finish_fabric_rcv_dtls b,inv_receive_master c where  b.mst_id=c.id and c.entry_form in(7,37) and c.status_active=1 and c.booking_without_order=0 and c.entry_form in(7,37)  and b.status_active=1 and b.is_deleted=0 $batch_cond_for2 order by b.batch_id";
	
	foreach($fin_fab_sql as $row)
	{
		if($row[csf("update_date")]!='') $fin_dateTime=$row[csf("update_date")];
		else $fin_dateTime=$row[csf("insert_date")];
		$fin_fab_arr[$row[csf("batch_id")]]["uom"]=$row[csf("uom")];
		$fin_fab_arr[$row[csf("batch_id")]]["receive_date"]=$row[csf("receive_date")];
		$fin_fab_arr[$row[csf("batch_id")]]["shift_name"]=$shift_name[$row[csf("shift_name")]];
		$fin_fab_arr[$row[csf("batch_id")]]["insert_date"]=$fin_dateTime;
		//echo $fin_dateTime.', ';
		
		$fin_fab_qty_arr[$row[csf("batch_id")]][$row[csf("uom")]]["receive_qnty"]+=$row[csf("receive_qnty")];
	}
	unset($fin_fab_sql);
	$fin_delivery_sql= sql_select("select c.insert_date,c.update_date,c.delevery_date, b.batch_id,b.uom,b.current_delivery from  pro_grey_prod_delivery_dtls b,pro_grey_prod_delivery_mst c where  b.mst_id=c.id  and c.status_active=1 and c.entry_form in(54)  and b.status_active=1 and b.is_deleted=0 $batch_cond_for2 order by b.batch_id");
	//echo "select c.insert_date,c.delevery_date, b.batch_id,b.uom,b.current_delivery from  pro_grey_prod_delivery_dtls b,pro_grey_prod_delivery_mst c where  b.mst_id=c.id and c.status_active=1 and c.entry_form in(54)  and b.status_active=1 and b.is_deleted=0 $batch_cond_for2 order by b.batch_id";
	
	foreach($fin_delivery_sql as $row)
	{
		if($row[csf("update_date")]!='') $del_fin_dateTime=$row[csf("update_date")];
		else $del_fin_dateTime=$row[csf("insert_date")];
		$fin_fab_deli_arr[$row[csf("batch_id")]]["uom"]=$row[csf("uom")];
		$fin_fab_deli_arr[$row[csf("batch_id")]]["delevery_date"]=$row[csf("delevery_date")];
		$fin_fab_deli_arr[$row[csf("batch_id")]]["insert_date"]=$del_fin_dateTime;
		$fin_fab_deli_qty_arr[$row[csf("batch_id")]][$row[csf("uom")]]["current_delivery"]+=$row[csf("current_delivery")];
	}
	unset($fin_delivery_sql);
	
	//-----------------------===========For SubCon============Area ----------------------------
	

		ob_start();
		
			
			$width=6840; 
			?>
			<div style="width:<? echo $width;?>px;">
				<style type="text/css">
					.alignment_css
					{
						word-break: break-all;
						word-wrap: break-word;
					}
				</style>
			<!--	<fieldset style="width:<?// echo $width;?>px;">-->
            		
						<div>
						<table class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
							<caption><strong><? echo  $company_library[$cbo_company_name]; ?><br><? echo $report_title;?>	<br>
								<? echo  change_date_format($txt_date_from).' To '.change_date_format($txt_date_to); ?></strong>
							</caption>
							<thead>
								<tr>
									<th width="20" class="" rowspan="2">SL</th>
									<th width="100" class="" rowspan="2">Buyer</th>
									<th width="100" class="" rowspan="2">Style Ref.</th>
									<th width="100" rowspan="2" class="">FSO No</th>
									<th width="100" rowspan="2" style="word-break:break-all" class="">Fab.Booking No.</th>
                                    <th width="100" rowspan="2" style="word-break:break-all" class="">Booking Type</th>
                                    <th width="100" rowspan="2" style="word-break:break-all" class="">Body Part</th>
                                    <th width="100" rowspan="2" style="word-break:break-all" class="">Fabrics Desc.</th>
                                    <th width="100" rowspan="2" style="word-break:break-all" class="">Dia / Width Type</th>
                                    <th width="80" rowspan="2"  style="word-break:break-all" class="">Yarn Lot No</th>
                                    <th width="80" rowspan="2" style="word-break:break-all" class="">Yarn Info.</th>
                                    <th width="80" rowspan="2" style="word-break:break-all" class="">Color Name</th>
                                    <th width="80" rowspan="2" style="word-break:break-all" class="">Color Range</th>
                                    <th width="80" rowspan="2" style="word-break:break-all" class="">Color wise <br>required<br> Fabirc Qty</th>
                                    
                                    
                                    <th colspan="4">Batch Information</th>
									<th colspan="2">Chemical & Dyes Cons.</th>
									<th colspan="5">Slitting</th>
									<th colspan="5">Bleaching / Scouring </th>
									<th colspan="5" style="word-break:break-all">Stentering </th>
									<th colspan="9">Dyeing Production</th>
									<th colspan="4" style="word-break:break-all">Rotation </th>
									<th colspan="5">Wash</th>
									<th colspan="5" style="word-break:break-all">Heat set</th>
                                    
									<th colspan="5">Drying</th>
									<th colspan="5">Compacting</th>
                                    <th colspan="5">Singeing</th>
                                    <th colspan="5">Brush</th>
                                    
									<th colspan="3">Finish Fabric<br>Prduction	</th>
									<th colspan="2">Delivery To <br>Finish<br>Fabirc Store	</th>
								</tr>
								
								<tr>
									<th width="80" class="">Batch Date</th>
									<th width="80" class="">Batch No</th>
									<th width="80" class="">Extn. No</th>
									<th width="80" class="">Batch Wgt.</th>
									<th width="80" style="word-break:break-all" class="">Recipe Info</th>
									<th width="80" style="word-break:break-all" class="">Requisition<br>Issue Info</th>
                                    <th width="80" class="">Machine No</th>
									<th width="80" class="">Quantity</th>
									<th width="80" class="">Start Date & Time</th>
									<th width="80" class="">End Time <br>Date & Shift</th>
									<th width="80" style="word-break:break-all" class="">Time Used</th>
                                    <th width="80" class="">Machine No</th>
									<th width="80" class="">Quantity</th>
									<th width="80" class="">Start Date & Time</th>
									<th width="80" class="">End Time<br> Date & Shift</th>
									<th width="80" style="word-break:break-all" class="">Time Used</th>
                                    
                                    <th width="80" title="Stenter" class="">Machine No</th>
									<th width="80" class="">Quantity</th>
									<th width="80" class="">Start Date & Time</th>
									<th width="80" class="">End Time<br> Date & Shift</th>
									<th width="80" style="word-break:break-all" class="">Time Used</th>
                                    
									<th width="80" title="Dyeing" class="">Machine No</th>
                                    <th width="80" title="Dyeing" class="">Machine Capacity</th>
									<th width="80" class="">Load Qty</th>
									<th width="100" class="">Load Date<br> Time & Shift</th>
									<th width="100" class="">UnLoad Date<br> Time & Shift</th>
									<th width="80" style="word-break:break-all" class="">Time Used</th>
                                    <th width="80" class="">BTB/LTB</th>
                                    <th width="80" class="">Result</th>
                                    <th width="80" class="">Dyeing Process</th>
                                    
                                    <th width="80" title="Rotation" class="">Quantity</th>
									<th width="80" class="">Start Date & Time</th>
									<th width="80" class="">End Date & Time</th>
									<th width="80" style="word-break:break-all" class="">Time Used</th>
                                    
                                     <th width="80" title="Wash" class="">Machine No</th>
									<th width="80" class="">Quantity</th>
									<th width="80" class="">Start Date & Time</th>
									<th width="80" class="">End Time<br> Date & Shift</th>
									<th width="80" style="word-break:break-all" class="">Time Used</th>
                                    
                                    <th width="80" title="Heatset" class="">Machine No</th>
									<th width="80" class="">Quantity</th>
									<th width="80" class="">Start Date & Time</th>
									<th width="80" class="">End Time<br> Date & Shift</th>
									<th width="80" style="word-break:break-all" class="">Time Used</th>
                                    
                                     <th width="80" title="Drying" class="">Machine No</th>
									<th width="80" class="">Quantity</th>
									<th width="80" class="">Start Date & Time</th>
									<th width="80" class="">End Time<br> Date & Shift</th>
									<th width="80" style="word-break:break-all" class="">Time Used</th>
                                    
                                    <th width="80" title="Compacting" class="">Machine No</th>
									<th width="80" class="">Quantity</th>
									<th width="80" class="">Start Date & Time</th>
									<th width="80" class="">End Time<br> Date & Shift</th>
									<th width="80" style="word-break:break-all" class="">Time Used</th>
                                    
                                    <th width="80" title="Singeing" class="">Machine No</th>
									<th width="80" class="" title="SL=70">Quantity</th>
									<th width="80" class="">Start Date & Time</th>
									<th width="80" class="">End Time<br> Date & Shift</th>
									<th width="80" style="word-break:break-all" class="">Time Used</th>
                                    
                                    <th width="80" title="Brush" class="">Machine No</th>
									<th width="80" class="" title="SL=70">Quantity</th>
									<th width="80" class="">Start Date & Time</th>
									<th width="80" class="">End Time<br> Date & Shift</th>
									<th width="80" style="word-break:break-all" class="">Time Used</th>
                                    
                                    
									<th width="80" title="Fin Fab Prod" class="">Quantity</th>
									<th width="80" class="">Date</th>
									<th width="80" style="word-break:break-all" class=""> Shift</th>
									
									<th width="80" style="word-break:break-all" class="">Quantity</th>
									<th width="" style="word-break:break-all" class="">Date</th>
								</tr>
                                
							</thead>
						</table>
						<div style=" max-height:380px; width:<? echo $width+20;?>px; overflow-y:scroll;" id="scroll_body">
						<table align="left" class="rpt_table" id="table_body" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tbody>
								<tr>
								<td colspan="78"><b>Inhouse </b></td>
								</tr>
									<?
									$tot_batch_qty=$tot_fin_prod_qc_qnty_kg=$tot_sliting_prod_qty=$tot_fin_deli_qnty_kg=$tot_stenter_prod_qty=$tot_sourcing_prod_qty=$tot_load_prod_qty=$tot_rotaion_prod_qty=$tot_heatset_prod_qty=$tot_drying_prod_qty=$tot_comp_prod_qty=$tot_singe_prod_qty=$tot_brush_prod_qty=0;
									$ii=1;
									foreach($batch_wise_dying_arr as $batch_id=>$row)
									{
										
										if ($ii%2==0)
											$bgcolor="#E9F3FF";
										else
											$bgcolor="#FFFFFF";
										$within_group=$booking_data_arr2[$row[("sales_order_no")]]["within_group"];
										$booking_type=$row[("booking_type")];
									//	echo $within_group.'DDDDD';
										$heatset_start_date=$row[("heatset_start_date")];
										
										$heatset_start_time=$row[("heatset_starttime")];
										$heatset_end_date=$row[("heatset_end_date")];
										$heatset_endtime=$row[("heatset_endtime")];
										$batch_hr_min=$row[("batch_hr_min")].':00';
									//	echo $batch_hr_min.', ';
										$batch_date_time=($row["batch_date"].' '.$batch_hr_min);
										$heat_date_time=($heatset_end_date.' '.$heatset_endtime.':'.'00');
										$batchtoheat_time_diff=datediff(n,$batch_date_time ,$heat_date_time);
									//	echo $batch_date_time.'='.$heat_date_time;
										//$diff_time_days=$total_time_diff;
										$unload_dateArr=rtrim($load_unload_time_arr[$batch_id]["end_date"],',');
										$unload_dateData=array_unique(explode(",",$unload_dateArr));
										$unload_prod_qty=$load_unload_time_arr[$batch_id]["prod_qty"];
										$unload_machine_id=$load_unload_time_arr[$batch_id]["unload_machine_id"];
										$unload_ltb_btb=$load_unload_time_arr[$batch_id]["unload_ltb_btb_id"];
										$fabric_type=$load_unload_time_arr[$batch_id]["fabric_type"];
										$resultName=$load_unload_time_arr[$batch_id]["result"];
									
										//$unload_shift_name=$load_unload_time_arr[$batch_id]["unload_shift_name"];
										//echo $unload_date.'D'.$batch_id;
										//$unload_time=$load_unload_time_arr[$batch_id]["process_time"];
										//$unload_date_time=($unload_date.' '.$unload_time.':'.'00');
										//$unload_date_time2=(change_date_format($unload_date).' '.$unload_time.':'.'00');
										
										$load_shift_name=$load_time_arr[$batch_id]["load_shift_name"];
										$load_prod_qty=$load_time_arr[$batch_id]["prod_qty"];
										//$load_date=$load_time_arr[$batch_id]["end_date"];
										$load_dateArr=rtrim($load_time_arr[$batch_id]["end_date"],',');
										//echo $load_dateArr.'ddd';
										$load_dateData=array_unique(explode(",",$load_dateArr));
										
										//$load_time=$load_time_arr[$batch_id]["end_time"];
										//$load_date_time=(change_date_format($load_date).' '.$load_time.':'.'00');
										//echo $batch_date_time.'='.$unload_date_time2;
										//$dying_time_used_diff=datediff(n,$load_date_time ,$unload_date_time);
										
										$sliting_start_date=$row[("sliting_start_date")];
										$sliting_start_time=$row[("sliting_starttime")];
										$sliting_end_date=$row[("sliting_end_date")];
										$sliting_endtime=$row[("sliting_endtime")];
										$sliting_mc_id=$row[("sliting_mc_id")];
										$sliting_shift_name=$row[("sliting_shift_name")];
										$sliting_prod_qty=$row[("sliting_prod_qty")]; 
										//$batch_hr_min=$row[("batch_hr_min")];
										//$batch_date_time=($row["batch_date"].' '.$batch_hr_min.':'.'00');
										$sliting_end_date_time=(change_date_format($sliting_end_date).' '.$sliting_endtime.':'.'00');
										$sliting_start_date_time=(change_date_format($sliting_start_date).' '.$sliting_start_time.':'.'00');
										$sliting_time_used_diff=datediff(n,$sliting_start_date_time ,$sliting_end_date_time); 
										//echo $sliting_date_time.'='.$unload_date_time2;
										$sourcing_mc_id=$row[("sourcing_mc_id")];
										$sourcing_shift_name=$row[("sourcing_shift_name")];
										$sourcing_prod_qty=$row[("sourcing_prod_qty")]; 
										$typewise_sourcing_start_date=$row[("type_sourcing_start_date")];
										$typewise_sourcing_start_time=$row[("type_sourcing_starttime")];
										$typewise_sourcing_end_date=$row[("type_sourcing_end_date")];
										$typewise_sourcing_endtime=$row[("type_sourcing_endtime")];
										//$typewise_sourcing_date_time=($typewise_brush_end_date.' '.$typewise_brush_endtime.':'.'00');
										$sourcing_end_date_time=(change_date_format($typewise_sourcing_end_date).' '.$typewise_sourcing_endtime.':'.'00');
										$sourcing_start_date_time=(change_date_format($typewise_sourcing_start_date).' '.$typewise_sourcing_start_time.':'.'00');
										$sourcing_time_used_diff=datediff(n,$sourcing_start_date_time ,$sourcing_end_date_time);
										
										$rotaion_prod_qty=$row[("rotaion_prod_qty")];
										$rotation_shift_name=$row[("rotation_shift_name")];
										
										$typewise_peach_end_date=$row[("type_peach_end_date")];
										$typewise_peach_endtime=$row[("type_peach_endtime")];
										$typewise_peach_date_time=($typewise_peach_end_date.' '.$typewise_peach_endtime.':'.'00');
										$dyingtotypepeach_time_diff=datediff(n,$unload_date_time ,$typewise_peach_date_time);
										
										$stenter_mc_id=$row[("stenter_mc_id")];
										$stenter_shift_name=$row[("stenter_shift_name")];
										$stenter_prod_qty=$row[("stenter_prod_qty")];
										$stenter_start_date=$row[("stenter_start_date")];
										$stenter_start_time=$row[("stenter_starttime")];
										$stenter_end_date=$row[("stenter_end_date")];
										$stenter_endtime=$row[("stenter_endtime")];
										$stenter_date_time=($stenter_end_date.' '.$stenter_endtime.':'.'00');
										$stenter_end_date_time=(change_date_format($stenter_end_date).' '.$stenter_endtime.':'.'00');
										$stenter_start_date_time=(change_date_format($stenter_start_date).' '.$stenter_start_time.':'.'00');
										$stenter_time_used_diff=datediff(n,$stenter_start_date_time ,$stenter_end_date_time);
										//$dyingtostener_time_diff=datediff(n,$unload_date_time ,$stenter_date_time);
										//echo $stenter_start_time.'='.$stenter_endtime.','; //stenter_endtime  stenter_starttime
										$type_stenter_start_date=$row[("type_stenter_start_date")];
										$type_stenter_start_time=$row[("type_stenter_starttime")];
										$type_stenter_end_date=$row[("type_stenter_end_date")];
										$type_stenter_endtime=$row[("type_stenter_endtime")];
										$type_stenter_date_time=($type_stenter_end_date.' '.$type_stenter_endtime.':'.'00');
										$type_dyingtotype_stener_time_diff=datediff(n,$unload_date_time ,$type_stenter_date_time);
										
										$comp_start_date=$row[("comp_start_date")];
										$comp_start_time=$row[("comp_starttime")];
										$comp_end_date=$row[("comp_end_date")];
										$comp_endtime=$row[("comp_endtime")];
										$comp_date_time=($comp_end_date.' '.$comp_endtime.':'.'00');
										$dyingtocomp_time_diff=datediff(n,$unload_date_time ,$comp_date_time);
										
										$dry_start_date=$row[("dry_start_date")];//
										$dry_starttime=$row[("dry_starttime")];
										$dry_end_date=$row[("dry_end_date")];
										$dry_endtime=$row[("dry_endtime")];
										$dry_date_time=($dry_end_date.' '.$dry_endtime.':'.'00');
										//echo $unload_date_time.'='.$dry_date_time.'<br>';
										$dyingtodry_time_diff=datediff(n,$unload_date_time ,$dry_date_time);
										
										
										
										
										//$fin_uom=$fin_fab_arr[$batch_id]["uom"];
										$fin_prod_date=$fin_fab_arr[$batch_id]["receive_date"];
										$fin_insert_time=explode(" ",$fin_fab_arr[$batch_id]["insert_date"]);
										$fin_time_convert=$fin_insert_time[1].' '.$fin_insert_time[2];
										if($fin_prod_date!="")
										{
										$fintimecal=strtotime("$fin_time_convert");
										$fintime_cal= date('H:i',$fintimecal);
										$fintime_cal=$fintime_cal.':00';
										} else $fintime_cal="";
										$fin_prod_date_time=($fin_prod_date.' '.$fintime_cal);
										$dyingtotypefinprod_time_diff=datediff(n,$unload_date_time ,$fin_prod_date_time);
										$fin_prod_qc_qnty_kg=$fin_fab_qty_arr[$batch_id][12]["receive_qnty"];
										$fin_prod_qc_qnty_yds=$fin_fab_qty_arr[$batch_id][27]["receive_qnty"];
										
										//$fin_uom=$fin_fab_deli_arr[$batch_id]["uom"];
										$fin_deli_date=$fin_fab_deli_arr[$batch_id]["delevery_date"];
										//$fin_deli_insert_time=explode(" ",$fin_fab_deli_arr[$batch_id]["insert_date"]);
										//$fin_deli_time_convert=$fin_deli_insert_time[1].' '.$fin_deli_insert_time[2];
										//echo $fin_deli_time_convert.'gg';
										
										$fin_deli_qnty_kg=$fin_fab_deli_qty_arr[$batch_id][12]["current_delivery"];
										$fin_deli_qnty_yds=$fin_fab_deli_qty_arr[$batch_id][27]["current_delivery"];
										//$batch_hr_min=batch_hr_min;
										$color_id=$row[("color_id")];
										$color_range_id=$row[("color_range_id")];
										//echo $row[("const_composition")].'D';
										$const_composition=rtrim($row[("const_composition")],',');
										$const_compositions=implode(",",array_unique(explode(",",$const_composition)));
										$body_part_id=rtrim($batch_data_dtls_arr[$batch_id]["body_part_id"],',');
										$body_part_ids=implode(",",array_unique(explode(",",$body_part_id)));
										$width_dia_type=rtrim($batch_data_dtls_arr[$batch_id]["width_dia_type"],',');
										$width_dia_types=implode(",",array_unique(explode(",",$width_dia_type)));
										$yarn_lot=rtrim($yarn_lot_arr[$batch_id]['lot'],',');
										$yarn_lots=implode(",",array_unique(explode(",",$yarn_lot)));
										$brand_id=rtrim($yarn_lot_arr[$batch_id]['brand_id'],',');
										$brand_name=implode(",",array_unique(explode(",",$brand_id)));
										$compo=rtrim($yarn_lot_arr[$batch_id]['compo'],',');
										$compos=implode(",",array_unique(explode(",",$compo)));
										$recipe_id=rtrim($recipe_batch_id_arr[$batch_id]['recipe_id'],',');
										$recipe_ids=implode(",",array_unique(explode(",",$recipe_id)));
										$requ_no=rtrim($recipe_batch_id_arr[$batch_id]['requ_no'],',');
										$requ_nos=implode(",",array_unique(explode(",",$requ_no)));
										$requ_id=rtrim($recipe_batch_id_arr[$batch_id]['requ_id'],',');
										$requ_ids=implode(",",array_unique(explode(",",$requ_id)));
										$yarn_count=rtrim($yarn_lot_arr[$batch_id]['yarn_count'],',');
										$yarn_counts=array_unique(explode(",",$yarn_count));
										$yarn_count_value = "";
										foreach ($yarn_counts as $val) {
											if ($val > 0) {
												if ($yarn_count_value == '') $yarn_count_value = $yarncount[$val]; else $yarn_count_value .= ", " . $yarncount[$val];
											}
										}
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $ii; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $ii; ?>">
											<td width="20" class="" align="center"><? echo $ii++ ;?></td>
											<td width="100" title="Prod Date" align="center"><? if($within_group==2) echo $booking_data_arr2[$row[("sales_order_no")]]["buyer_id"];else echo $booking_data_arr[$row[("booking_no")]]["buyer_id"];//echo change_date_format($row["end_date"]) ;?> </td>
											<td width="100" class="" align="center" style="word-break:break-all" ><?  echo $booking_data_arr2[$row[("sales_order_no")]]["style_ref_no"];;?> </td>
											<td width="100" style="word-break:break-all" align="center" class=""><? echo $row[("sales_order_no")];?></td>
											<td width="100" class="" style="word-break:break-all" align="center"><? echo $row[("booking_no")];?></td>
											<td width="100" class="" style="word-break:break-all" align="center"><?  echo $booking_data_arr[$row[("booking_no")]]["type"];?></td>
											<? ?>
											<td width="100" class="" style="word-break:break-all" align="center"><? echo $body_part_ids;?></td>
											<td width="100" class="" style="word-break:break-all" align="center"><? echo $const_compositions;//$booking_data_arr2[$row[("sales_order_no")]]["season"];?> </td>
											<td width="100" align="center" style="word-break:break-all"><? echo $width_dia_types;?></td>
											
											<td width="80" class=""  align="center" style="word-break:break-all"><? echo $yarn_lots;?></td>
											
											<td width="80" class=""  style="word-break:break-all" align="center"><? echo $yarn_count_value.','.$compos;//$batch_hr_min;?> </td>
											<td width="80" class="" style="word-break:break-all" align="center"><? echo $color_library[$color_id];?></td>
											<td width="80" class="" style="word-break:break-all" align="center"><? echo $color_range[$color_range_id];//echo  number_format($row[("batch_qty")],0);?></td>
											<td width="80"  align="right" title="<? echo $batch_id;?>"><? echo number_format($row[("batch_qty")],0);?></td>
											<td width="80" class="" align="center"><? echo change_date_format($row[("batch_date")]);?></td>
											<td width="80" class="" align="center">
												<?
												echo $row[("batch_no")];
												
												?>
											</td>
											<td width="80" align="center" class=""><? echo $row[("extention_no")];// if($heatset_end_date!='') echo floor($batchtoheat_time_diff/60).":".$batchtoheat_time_diff%60;
											//else echo " "; 
											$recipe_idArr=array_unique(explode(",",$recipe_id));
											?> </td>
											<td width="80" align="right" ><?  echo $row[("batch_wgt")];?> </td>
											<td width="80" class="" align="center">
                                            
                                            <? 
										$recipe_button='';
										foreach($recipe_idArr as $rId)
										{
										$recipe_button.="<a href='#' onClick=\"fn_recipe_calc('".$rId."','".$batch_id."','".$yarn_lots."','".$brand_name."',1)\"> ".$rId." <a/>".',';
											
										 }
											 echo rtrim($recipe_button,',');
											 ?>
                                              </td>
											<td width="80" class="" align="center">
												 <a href="##" onClick="fn_recipe_calc('<? echo $requ_ids;?>','<? echo $batch_id;?>','<? echo $yarn_lots;?>','<? echo $brand_name;?>',2)"><?  $requ_nosArr=explode("-",$requ_nos);echo ltrim($requ_nosArr[3],'0');?> </a> <? //echo $requ_nos;?>
											</td>
											<td width="80" class="" align="center"><? echo $machine_name_arr[$sliting_mc_id];?> </td>
											<td width="80" class="" align="right"><?  echo $sliting_prod_qty;?> </td>
											<td width="80" class="" align="center"title="<? echo $sliting_start_date;?>">
												<? echo change_date_format($sliting_start_date).'<br>'.$sliting_start_time;?>
											</td>
											<td width="80" class="" align="center">
												<?
												echo change_date_format($sliting_end_date).'<br>'.$sliting_endtime.'<br>'.$sliting_shift_name;
												?>
											</td>
											<td width="80" class="" align="center"><? //if($batch_hr_min!=0) echo floor($batchtodying_time_diff/60).":".$batchtodying_time_diff%60;;
											if($sliting_end_date!=0) 
											{
											echo 'Hr '.floor($sliting_time_used_diff/60).':Min '.$sliting_time_used_diff%60;
											}
											 else echo " "; 
											?></td>
											
											<td width="80" class="26">
												<? echo $machine_name_arr[$sourcing_mc_id];
												?>
											   
											</td>
											<td width="80" class="" title="" align="right">
												<?
												echo number_format($sourcing_prod_qty,0);
												?>
											</td>
											<td width="80" title="" align="center"class="">
												<?
												echo change_date_format($typewise_sourcing_start_date).'<br>'. $typewise_sourcing_start_time;
												
												// if($sliting_end_date!="")  echo $dyingtosliting_days_remian=datediff('d',$unload_date,$sliting_end_date)-1;//
												?>
											</td>
											<td width="80" class="" align="center" title="">
												<?
												 echo change_date_format($typewise_sourcing_end_date).'<br>'.$typewise_sourcing_endtime.'<br>'.$sourcing_shift_name;
												?> 
											</td>
											<td width="80" class="" align="center"><?  
											if($typewise_sourcing_end_date!=0) 
											{
											echo 'Hr '.floor($sourcing_time_used_diff/60).':Min '.$sourcing_time_used_diff%60;
											}
											 else echo " ";?></td>
											<td width="80" class="" align="center"><? echo $machine_name_arr[$stenter_mc_id];?></td>
											<td width="80" class="" align="right"><? 
														echo  number_format($stenter_prod_qty,0);
											 ?></td>
											<td width="80" class="" align="center"><? echo change_date_format($stenter_start_date).'<br>'. $stenter_start_time;;?></td>
											<td width="80" class="" align="center"><?  echo change_date_format($stenter_end_date).'<br>'. $stenter_endtime.'<br>'.$stenter_shift_name;?></td>
											<td width="80" class="" align="center"><?    
											if($stenter_end_date!=0) 
											{
											echo 'Hr '.floor($stenter_time_used_diff/60).':Min '.$stenter_time_used_diff%60;
											}
											 else echo " ";;?></td>
											<td width="80"  title="Unload MC" align="center"><? echo $machine_name_arr[$unload_machine_id]; ?></td>
											<td width="80" class="" align="right"><? echo $machine_capacity_arr[$unload_machine_id];?></td>
											<td width="80" class="" align="right"><? echo number_format($load_prod_qty,0);?></td>
											<td width="100" class="" align="center"><? //echo change_date_format($load_date).'<br>'.$load_time.'<br>'.$load_shift_name;?>
												<table width="100%" border="0"  class="rpt_table" rules="all">
											<? 
											// load_dateData
											foreach($load_dateData as $load_date)
											{
											?>	<tr>
											<?
												$load_time=$load_time_arr2[$batch_id][$load_date]["end_time"];
												$load_shift_name=$load_time_arr2[$batch_id][$load_date]["load_shift_name"];
												?>
												<td>
												<?
												$load_datetime=change_date_format($load_date).','.$load_time;
												 echo $load_datetime;
												 ?>
												 </td>
												 </tr>
												 <tr>
												<td align="center">
												<?
												 echo $load_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
											}
											?>
										   </table>
											</td>
											<td width="100" class="40" align="center">
											<table width="100%" align="center" border="0"  class="rpt_table" rules="all">
											<? 
											foreach($unload_dateData as $unload_date)
											{
											?>	<tr>
											<?
												$unload_time=$load_unload_time_arr2[$batch_id][$unload_date]["hr_min"];
												$un_shift_name=$load_unload_time_arr2[$batch_id][$unload_date]["shift_name"];
												?>
												<td>
                                                <p style="word-break:break-all">
												<?
												$unload_datetime=change_date_format($unload_date).', '.$unload_time;
												 echo $unload_datetime;
												 ?>
                                                 </p>
												 </td>
												 </tr>
												 <tr>
												<td>
												<?
												 echo $un_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
											}
											?>
										   </table>
											</td>
											<td width="80" align="center" class=""><? 
											foreach($unload_dateData as $unload_date)
											{
												$unload_time=$load_unload_time_arr2[$batch_id][$unload_date]["hr_min"];
												$unload_date_time=(change_date_format($unload_date).' '.$unload_time.':'.'00');
												//echo $batch_date_time.'='.$unload_date_time2;
												$dying_time_used_hr=0;$dying_time_used_hr=0;
												foreach($load_dateData as $load_date)
												{
													$load_time=$load_time_arr2[$batch_id][$load_date]["end_time"];
													$load_date_time=(change_date_format($load_date).' '.$load_time.':'.'00');
													$dying_time_used_diff=datediff(n,$load_date_time ,$unload_date_time);
													$dying_time_used_hr+=floor($dying_time_used_diff/60);
													$dying_time_used_min+=$dying_time_used_diff%60;
													
												}
											}
											if(count($unload_dateData)>0) 
											{
												$dyingused_time_diff=datediff(n,$load_datetime ,$unload_datetime);
												echo floor($dyingused_time_diff/60).":".$dyingused_time_diff%60;
											//echo 'Hr '.$dying_time_used_hr.':Min '.$dying_time_used_min;
											}
											 else echo " ";?></td>
											<td width="80" class="" align="center"><?  echo $unload_ltb_btb;?></td>
											<td width="80" class="" align="center" ><p style="word-break:break-all"><?    echo	$resultName;?></p></td>
											<td width="80" class="" align="center"><? echo  $fabric_type?></td>
											<td width="80" class="" align="right"><? echo number_format($rotaion_prod_qty,0);?></td>
											<td width="80" class="" align="center"><?  
											$rotaion_start_date=rtrim($row[("rotaion_start_date")],',');
											$rotaion_start_dateArr=array_unique(explode(",",$rotaion_start_date));
											?>
											<table width="100%" border="0" align="center" class="rpt_table" rules="all">
											<? 
											foreach($rotaion_start_dateArr as $rotaion_startdate)
											{
											?>	<tr>
											<?
												$rot_start_time=$batch_wise_rotaion_arr[$batch_id][$rotaion_startdate]["rotaion_starttime"];
												$start_rotation_shift_name=$batch_wise_rotaion_arr[$batch_id][$rotaion_startdate]["rotation_shift_name"];
												?>
												<td>
                                                <p style="word-break:break-all">
												<?
												$rot_start_datetime=change_date_format($rotaion_startdate).', '.$rot_start_time;
												 echo $rot_start_datetime;
												 ?>
                                                 </p>
                                                 
												 </td>
												 </tr>
												 <tr>
												<td>
												<?
												 echo $start_rotation_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
											}
											
											?>
										   </table>
											</td>
											<td width="80" class="" align="center"><?  
											$rotaion_end_date=rtrim($row[("rotaion_end_date")],',');
											$rotaion_end_dateArr=array_unique(explode(",",$rotaion_end_date));
											?>
											<table width="100%" border="0" align="center" class="rpt_table" rules="all">
											<? 
											// load_dateData
											foreach($rotaion_end_dateArr as $rotaion_enddate)
											{
											?>	<tr>
											<?
												$rot_end_time=$batch_wise_rotaion_arr[$batch_id][$rotaion_enddate]["rotaion_endtime"];
												$end_rotation_shift_name=$batch_wise_rotaion_arr[$batch_id][$rotaion_enddate]["rotation_shift_name"];
												?>
												<td>
												<?
												$rot_end_datetime=change_date_format($rotaion_enddate).', '.$rot_end_time;
												 echo $rot_end_datetime;
												 ?>
												 </td>
												 </tr>
												 <tr>
												<td>
												<?
												 echo $end_rotation_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
												 $rotaion_endtime=$batch_wise_rotaion_arr[$batch_id][$rotaion_enddate]["rotaion_endtime"];
												$rotaion_end_datetime=(change_date_format($rotaion_enddate).' '.$rotaion_endtime.':'.'00');
												$rotation_time_used_hr=$rotation_time_used_min=0;
												 foreach($rotaion_start_dateArr as $rotaion_startdate)
													{
														$rotaion_starttime=$batch_wise_rotaion_arr[$batch_id][$rotaion_startdate]["rotaion_starttime"];
														$rotaion_start_datetime=(change_date_format($rotaion_startdate).' '.$rotaion_starttime.':'.'00');
														
														$rotation_time_used_diff=datediff(n,$rotaion_start_datetime ,$rotaion_end_datetime);
														$rotation_time_used_hr+=floor($rotation_time_used_diff/60);
														$rotation_time_used_min+=$rotation_time_used_diff%60;
													}
											}
											
											?>
										   </table>
                                           </td>
											<td width="80" class="" align="center"><?   
											if(count($rotaion_end_dateArr)>0) 
											{ echo 'Hr '.$rotation_time_used_hr.':Min '.$rotation_time_used_min;}
											 else echo " ";?></td>
											<td width="80" class="" align="center"><? echo $machine_name_arr[$row['wash_mc_id']];?> </td>
											
											<td width="80" class="" align="right"><? echo number_format($row['wash_prod_qty'],0);?></td>						

											<td width="80" class="" align="center">
											
											
											
											<?  
											$wash_start_date=rtrim($row[("wash_start_date")],',');
											$wash_start_dateArr=array_unique(explode(",",$wash_start_date));
											?>
											<table width="100%" border="0" align="center" class="rpt_table" rules="all">
											<? 
											foreach($wash_start_dateArr as $wash_startdate)
											{
											?>	<tr>
											<?
												$rot_start_time=$batch_wise_wash_arr[$batch_id][$wash_startdate]["wash_starttime"];
												$start_wash_shift_name=$batch_wise_wash_arr[$batch_id][$wash_startdate]["wash_shift_name"];
												?>
												<td>
                                                <p style="word-break:break-all">
												<?
												$rot_start_datetime=change_date_format($wash_startdate).', '.$rot_start_time;
												 echo $rot_start_datetime;
												 ?>
                                                 </p>
                                                 
												 </td>
												 </tr>
												 <tr>
												<td align="center">
												<?
												 echo $start_wash_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
											}
											
											?>
										   </table>
											
											</td>
											<td width="80" class="" align="center"><?  
											$wash_end_date=rtrim($row[("wash_end_date")],',');
											$wash_end_dateArr=array_unique(explode(",",$wash_end_date));
											?>
											<table width="100%" border="0" align="center" class="rpt_table" rules="all">
											<? 
											// load_dateData
											foreach($wash_end_dateArr as $wash_enddate)
											{
											?>	<tr>
											<?
												$rot_end_time=$batch_wise_wash_arr[$batch_id][$wash_enddate]["wash_endtime"];
												$end_wash_shift_name=$batch_wise_wash_arr[$batch_id][$wash_enddate]["shift_name"];
												?>
												<td>
                                                <p style="word-break:break-all">
												<?
												$rot_end_datetime=change_date_format($wash_enddate).', '.$rot_end_time;
												 echo $rot_end_datetime;
												 ?>
                                                 </p>
												 </td>
												 </tr>
												 <tr>
												<td>
												<?
												 echo $end_wash_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
												 $wash_endtime=$batch_wise_wash_arr[$batch_id][$wash_enddate]["wash_endtime"];
												$wash_end_datetime=(change_date_format($wash_enddate).' '.$wash_endtime.':'.'00');
												$wash_time_used_hr=$wash_time_used_min=0;
												 foreach($wash_start_dateArr as $wash_startdate)
													{
														$wash_starttime=$batch_wise_wash_arr[$batch_id][$wash_startdate]["wash_starttime"];
														$wash_start_datetime=(change_date_format($wash_startdate).' '.$wash_starttime.':'.'00');
														
														$wash_time_used_diff=datediff(n,$wash_start_datetime ,$wash_end_datetime);
														$wash_time_used_hr+=floor($wash_time_used_diff/60);
														$wash_time_used_min+=$wash_time_used_diff%60;
													}
											}
											
											?>
										   </table>
                                           </td>
											<td width="80" class="" align="center"><p style="word-break:break-all"><?   
											if(count($wash_end_dateArr)>0) 
											{ echo 'Hr '.$wash_time_used_hr.':Min '.$wash_time_used_min;}
											 else echo " ";?>
                                             </p>
                                             </td>



                                            
											<td width="80" class="" align="center"><? echo  $machine_name_arr[$row[("heat_mc_id")]];?></td>
											<td width="80" class="" align="right"><? echo number_format($row[("heatset_prod_qty")],0);;?></td>
											<td width="80" class="" align="center"><?  
											$heatset_start_date=rtrim($row[("heatset_start_date")],',');
											$heatset_start_dateArr=array_unique(explode(",",$heatset_start_date));
											?>
											<table width="100%" border="0" align="center" class="rpt_table" rules="all">
											<? 
											// load_dateData
											foreach($heatset_start_dateArr as $heat_startdate)
											{
											?>	<tr>
											<?
												$heat_start_time=$batch_wise_heat_arr[$batch_id][$heat_startdate]["heatset_starttime"];
												$heat_shift_name=$batch_wise_heat_arr[$batch_id][$heat_startdate]["heat_shift_name"];
												?>
												<td>
                                                <p style="word-break:break-all">
												<?
												$start_datetime=change_date_format($heat_startdate).', '.$heat_start_time;
												 echo $start_datetime;
												 ?>
                                                 </p>
												 </td>
												 </tr>
												 <tr>
												<td align="center">
												<?
												 echo $heat_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
												
											}
											
											?>
										   </table>
                                           </td>
											<td width="80" align="center" class=""> <?  
											$heatset_end_date=rtrim($row[("heatset_end_date")],',');
											$heatset_end_dateArr=array_unique(explode(",",$heatset_end_date));
											?>
											<table width="100%" border="0" align="center" class="rpt_table" rules="all">
											<? 
											// load_dateData
											foreach($heatset_end_dateArr as $heat_enddate)
											{
											?>	<tr>
											<?
												$heat_end_time=$batch_wise_heat_arr[$batch_id][$heat_enddate]["heatset_endtime"];
												$end_heat_shift_name=$batch_wise_heat_arr[$batch_id][$heat_enddate]["end_heat_shift_name"];
												?>
												<td>
                                                <p style="word-break:break-all">
												<?
												$end_datetime=change_date_format($heat_enddate).', '.$heat_end_time;
												 echo $end_datetime;
												 ?>
                                                 </p>
												 </td>
												 </tr>
												 <tr>
												<td>
												<?
												 echo $end_heat_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
												  $heat_endtime=$batch_wise_heat_arr[$batch_id][$heat_enddate]["heatset_endtime"];
												$heat_end_datetime=(change_date_format($heat_enddate).' '.$heat_endtime.':'.'00');
												$heat_time_used_hr=$heat_time_used_min=0;
												 foreach($heatset_start_dateArr as $heat_startdate)
													{
														$heatset_starttime=$batch_wise_heat_arr[$batch_id][$heat_startdate]["heatset_starttime"];
														$heat_start_datetime=(change_date_format($heat_startdate).' '.$heatset_starttime.':'.'00');
														//echo $heat_start_datetime.'='.$heat_end_datetime.'<br>';
														$heat_time_used_diff=datediff(n,$heat_start_datetime ,$heat_end_datetime);
														$heat_time_used_hr+=floor($heat_time_used_diff/60);
														$heat_time_used_min+=$heat_time_used_diff%60;
													}
												
											}
											
											?>
										   </table>
                                           </td>
											<td width="80" class="" align="center">
											<p style="word-break:break-all">
											<?   
											if(count($heatset_start_dateArr)>0) 
											{ echo 'Hr '.$heat_time_used_hr.':Min '.$heat_time_used_min;}
											 else echo " ";?>
                                             </p>
                                             </td>
											
											<td width="80" class="" align="center"><? echo $machine_name_arr[$row['dry_mc_id']];?></td>
											<td width="80" class="60" align="right"> <? echo number_format($row['dry_prod_qty'],0)  ;?></td>
											<td width="80" class="">
											<?  
											$dry_start_date=rtrim($row[("dry_start_date")],',');
											$dry_start_dateArr=array_unique(explode(",",$dry_start_date));
											?>
											<table width="100%" border="0"   align="center" class="rpt_table" rules="all">
											<? 
											// load_dateData
											foreach($dry_start_dateArr as $dry_startdate)
											{
											?>	<tr>
											<?
												$dry_start_time=$batch_wise_dry_arr[$batch_id][$dry_startdate]["dry_starttime"];
												$start_dry_shift_name=$batch_wise_dry_arr[$batch_id][$dry_startdate]["start_shift_name"];
												?>
												<td>
                                                <p style="word-break:break-all">
												<?
												$dry_start_datetime=change_date_format($dry_startdate).', '.$dry_start_time;
												 echo $dry_start_datetime;
												 ?>
                                                 </p>
												 </td>
												 </tr>
												 <tr>
												<td  align="center">
												<?
												 echo $start_dry_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
												
											}
											
											?>
										   </table>
                                            </td>
											<td width="80" class=""  align="center">
											<?  
											$dry_end_date=rtrim($row[("dry_end_date")],',');
											$dry_end_dateArr=array_unique(explode(",",$dry_end_date));
											?>
											<table width="100%"  align="center" border="0"  class="rpt_table" rules="all">
											<? 
											// load_dateData
											foreach($dry_end_dateArr as $dry_enddate)
											{
											?>	<tr>
											<?
												$dry_end_time=$batch_wise_dry_arr[$batch_id][$dry_enddate]["dry_endtime"];
												$dry_end_shift_name=$batch_wise_dry_arr[$batch_id][$dry_enddate]["end_shift_name"];
												?>
												<td>
                                                <p style="word-break:break-all">
												<?
												$dry_end_datetime=change_date_format($dry_enddate).', '.$dry_end_time;
												 echo $dry_end_datetime;
												 ?>
                                                 </p>
												 </td>
												 </tr>
												 <tr>
												<td>
												<?
												 echo $dry_end_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
												 //$dry_endtime=$batch_wise_dry_arr[$batch_id][$dry_enddate]["dry_starttime"];
												$dry_end_datetime=(change_date_format($dry_enddate).' '.$dry_end_time.':'.'00');
												$dry_time_used_hr=$dry_time_used_min=0;
												 foreach($dry_start_dateArr as $dry_startdate)
													{
														$dry_starttime=$batch_wise_dry_arr[$batch_id][$dry_startdate]["dry_starttime"];
														$dry_start_datetime=(change_date_format($dry_startdate).' '.$dry_starttime.':'.'00');
														//echo $dry_start_datetime.'='.$dry_end_datetime.'<br>';
														$dry_time_used_diff=datediff(n,$dry_start_datetime ,$dry_end_datetime);
														$dry_time_used_hr+=floor($dry_time_used_diff/60);
														$dry_time_used_min+=$dry_time_used_diff%60;
													}
											}
											
											?>
										   </table>
                                            </td>
											<td width="80" class=""  align="center"><?   
											if(count($dry_start_dateArr)>0) 
											{ echo 'Hr '.$dry_time_used_hr.':Min '.$dry_time_used_min;}
											 else echo " "; ?></td>
											<td width="80" class=""  align="center"><? echo $machine_name_arr[$row['comp_mc_id']]; //comp_start_date ?></td>
											
											<td width="80" class="" align="right"><? echo  number_format($row["comp_prod_qty"],0);?></td>
											
											
											<td width="80" class=""  align="center"><?  
											$comp_start_date=rtrim($row[("comp_start_date")],',');
											$comp_start_dateArr=array_unique(explode(",",$comp_start_date));
											?>
											<table width="100%" border="0"  align="center" class="rpt_table" rules="all">
											<? 
											// load_dateData
											foreach($comp_start_dateArr as $comp_startdate)
											{
											?>	<tr>
											<?
												$comp_start_time=$batch_wise_comp_arr[$batch_id][$comp_startdate]["comp_starttime"];
												$start_comp_shift_name=$batch_wise_comp_arr[$batch_id][$comp_startdate]["start_shift_name"];
												?>
												<td>
												<?
												$comp_start_datetime=change_date_format($comp_startdate).', '.$comp_start_time;
												 echo $comp_start_datetime;
												 ?>
												 </td>
												 </tr>
												 <tr>
												<td  align="center">
												<?
												 echo $start_comp_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
												
											}
											
											?>
										   </table>
                                           </td>
											<td width="80" class=""  align="center">
                                            	<?  
											$comp_end_date=rtrim($row[("comp_end_date")],',');
											$comp_end_dateArr=array_unique(explode(",",$comp_end_date));
											?>
											<table width="100%"  align="center" border="0"  class="rpt_table" rules="all">
											<? 
											// load_dateData
											foreach($comp_end_dateArr as $comp_enddate)
											{
											?>	<tr>
											<?
												$comp_endtime=$batch_wise_comp_arr[$batch_id][$comp_enddate]["comp_endtime"];
												$comp_end_shift_name=$batch_wise_comp_arr[$batch_id][$comp_enddate]["end_shift_name"];
												?>
												<td>
                                                <p style="word-break:break-all">
												<?
												$comp_end_datetime=change_date_format($comp_enddate).', '.$comp_endtime;
												 echo $comp_end_datetime;
												 ?>
                                                 </p>
												 </td>
												 </tr>
												 <tr>
												<td>
												<?
												 echo $comp_end_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
												$comp_end_datetime_cal=(change_date_format($comp_enddate).' '.$comp_endtime.':'.'00');
												$comp_time_used_hr=$comp_time_used_min=0;
												 foreach($comp_start_dateArr as $com_startdate)
													{
														$comp_starttime=$batch_wise_comp_arr[$batch_id][$com_startdate]["comp_starttime"];
														$comp_start_datetime_cal=(change_date_format($com_startdate).' '.$comp_starttime.':'.'00');
														//echo $dry_start_datetime.'='.$dry_end_datetime.'<br>';
														$comp_time_used_diff=datediff(n,$comp_start_datetime_cal ,$comp_end_datetime_cal);
														$comp_time_used_hr+=floor($comp_time_used_diff/60);
														$comp_time_used_min+=$comp_time_used_diff%60;
													}
											}
											
											?>
										   </table>
                                            </td>
											<td width="80" class=""  align="center"><p style="word-break:break-all"> <?   
											if(count($comp_start_dateArr)>0) 
											{ echo 'Hr '.$comp_time_used_hr.':Min '.$comp_time_used_min;}
											 else echo " "; ?>
                                             </p>
                                             </td>
											<td width="80" class=""  align="center"><? echo $machine_name_arr[$row['singeing_mc_id']];  ;?></td>
											<td width="80" class="70" align="right"><? echo number_format($row["singeing_prod_qty"],0);?></td>
											<td width="80" class=""  align="center"><?  
											$singeing_start_date=rtrim($row[("singeing_start_date")],',');
											$singeing_start_dateArr=array_unique(explode(",",$singeing_start_date));
											?>
											<table width="100%" border="0"  align="center"  class="rpt_table" rules="all">
											<? 
											// load_dateData
											foreach($singeing_start_dateArr as $singeing_startdate)
											{
											?>	<tr>
											<?
												$singe_start_time=$batch_wise_singeing_arr[$batch_id][$singeing_startdate]["singeing_starttime"];
												
												$start_singe_shift_name=$batch_wise_singeing_arr[$batch_id][$singeing_startdate]["start_shift_name"];
												?>
												<td>
                                                <p style="word-break:break-all">
												<?
												$singe_start_datetime=change_date_format($singeing_startdate).', '.$singe_start_time;
												 echo $singe_start_datetime;
												 ?>
                                                 </p>
												 </td>
												 </tr>
												 <tr>
												<td  align="center">
												<?
												 echo $start_singe_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
												
											}
											
											?>
										   </table>
                                           </td>
                                           
											<td width="80" class=""  align="center"> <?  
											$singeing_end_date=rtrim($row[("singeing_end_date")],',');
											$singeing_end_dateArr=array_unique(explode(",",$singeing_end_date));
											?>
											<table width="100%" border="0"   align="center" class="rpt_table" rules="all">
											<? 
											// load_dateData
											foreach($singeing_end_dateArr as $singe_enddate)
											{
											?>	<tr>
											<?
												$singeing_endtime=$batch_wise_singeing_arr[$batch_id][$singe_enddate]["singeing_endtime"];
												$singe_end_shift_name=$batch_wise_singeing_arr[$batch_id][$singe_enddate]["end_shift_name"];
												?>
												<td>
                                                <p style="word-break:break-all">
												<?
												$singe_end_datetime=change_date_format($singe_enddate).', '.$singeing_endtime;
												 echo $singe_end_datetime;
												 ?>
                                                 </p>
												 </td>
												 </tr>
												 <tr>
												<td>
												<?
												 echo $singe_end_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
												$singeing_end_datetime_cal=(change_date_format($singe_enddate).' '.$singeing_endtime.':'.'00');
												$singe_time_used_hr=$singe_time_used_min=0;
												 foreach($singeing_start_dateArr as $singe_startdate)
													{
														$singe_starttime=$batch_wise_singeing_arr[$batch_id][$singe_startdate]["singeing_starttime"];
														$singe_start_datetime_cal=(change_date_format($singe_startdate).' '.$singe_starttime.':'.'00');
														//echo $dry_start_datetime.'='.$dry_end_datetime.'<br>';
														$singe_time_used_diff=datediff(n,$singe_start_datetime_cal ,$singeing_end_datetime_cal);
														$singe_time_used_hr+=floor($singe_time_used_diff/60);
														$singe_time_used_min+=$singe_time_used_diff%60;
													}
											}
											
											?>
										   </table></td>
											<td width="80" class=""  align="center"> <p style="word-break:break-all"><?   
											if(count($singeing_start_dateArr)>0) 
											{ echo 'Hr '.$singe_time_used_hr.':Min '.$singe_time_used_min;}
											 else echo " "; ?>
                                             </p>
                                             </td>
                                             
                                             
                                             
                                             <td width="80" class=""  align="center"><? echo $machine_name_arr[$row['brush_mc_id']];  ;?></td>
											<td width="80" class="70" align="right"><? echo number_format($row["brush_prod_qty"],0);?></td>
											<td width="80" class=""  align="center"><?  
											$brush_start_date=rtrim($row[("brush_start_date")],',');
											$brush_start_dateArr=array_unique(explode(",",$brush_start_date));
											?>
											<table width="100%" border="0"  align="center" class="rpt_table" rules="all">
											<? 
											// load_dateData
											foreach($brush_start_dateArr as $brush_startdate)
											{
											?>	<tr>
											<?
												$brush_start_time=$batch_wise_brush_arr[$batch_id][$brush_startdate]["brush_starttime"];
												
												$start_brush_shift_name=$batch_wise_brush_arr[$batch_id][$brush_startdate]["start_shift_name"];
												?>
												<td>
												<?
												$brush_start_datetime=change_date_format($brush_startdate).','.$brush_start_time;
												 echo $brush_start_datetime;
												 ?>
												 </td>
												 </tr>
												 <tr>
												<td  align="center">
												<?
												 echo $start_brush_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
												
											}
											
											?>
										   </table>
                                           </td>
											<td width="80" class=""  align="center"> <?  
											$brush_end_date=rtrim($row[("brush_end_date")],',');
											$brush_end_dateArr=array_unique(explode(",",$brush_end_date));
											?>
											<table width="100%"  align="center" border="0"  class="rpt_table" rules="all">
											<? 
											// load_dateData
											foreach($brush_end_dateArr as $brush_enddate)
											{
											?>	<tr>
											<?
												$brush_endtime=$batch_wise_brush_arr[$batch_id][$brush_enddate]["brush_endtime"];
												$brush_end_shift_name=$batch_wise_brush_arr[$batch_id][$brush_enddate]["end_shift_name"];
												?>
												<td>
                                                <p style="word-break:break-all">
												<?
												$brush_end_datetime=change_date_format($brush_enddate).', '.$brush_endtime;
												 echo $brush_end_datetime;
												 ?>
                                                 </p>
												 </td>
												 </tr>
												 <tr>
												<td  align="center">
												<?
												 echo $brush_end_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
												$brush_end_datetime_cal=(change_date_format($brush_enddate).' '.$brush_endtime.':'.'00');
												$brush_time_used_hr=$brush_time_used_min=0;
												 foreach($brush_start_dateArr as $brush_startdate)
													{
														$brush_starttime=$batch_wise_brush_arr[$batch_id][$brush_startdate]["brush_starttime"];
														$brush_start_datetime_cal=(change_date_format($brush_startdate).' '.$brush_starttime.':'.'00');
														//echo $brush_start_datetime_cal.'='.$brush_end_datetime_cal.'<br>';
														$brush_time_used_diff=datediff(n,$brush_start_datetime_cal ,$brush_end_datetime_cal);
														$brush_time_used_hr+=floor($brush_time_used_diff/60);
														$brush_time_used_min+=$brush_time_used_diff%60;
													}
											}
											
											?>
										   </table></td>
											<td width="80" class=""  align="center"><p style="word-break:break-all"> <?   
											if(count($brush_start_dateArr)>0) 
											{ echo 'Hr '.$brush_time_used_hr.':Min '.$brush_time_used_min;}
											 else echo " "; ?>
                                             </p>
                                             </td>
                                             
											
											<td width="80" class="" align="right"><?  $fin_fab_qty=$fin_prod_qc_qnty_kg+$fin_prod_qc_qnty_yds;
											echo  number_format($fin_fab_qty,0);?></td>
											
											<td width="80" class=""  align="center"><? echo change_date_format($fin_fab_arr[$batch_id]["receive_date"]);?></td>
											<td width="80" class=""  align="center"><? echo $fin_fab_arr[$batch_id]["shift_name"];?></td>
											<td width="80" class="" align="right"><? $delivery_qty=$fin_deli_qnty_kg+$fin_deli_qnty_yds;echo number_format($delivery_qty,0);?></td>
											<td width="" class="78"  align="center"><? echo change_date_format($fin_deli_date);?></td>
											
											
											
										</tr>
										<?
										$p++;
										$tot_batch_qty+= $row["batch_qty"];
										$tot_fin_prod_qc_qnty_kg+= $fin_fab_qty;
										$tot_sliting_prod_qty+= $sliting_prod_qty;
										$tot_fin_deli_qnty_kg+= $delivery_qty;
										$tot_stenter_prod_qty+= $stenter_prod_qty;
										$tot_sourcing_prod_qty+= $sourcing_prod_qty;
										$tot_load_prod_qty+= $load_prod_qty;
										$tot_rotaion_prod_qty+= $rotaion_prod_qty;
										$tot_heatset_prod_qty+= $row[("heatset_prod_qty")];
										$tot_drying_prod_qty+= $row[("dry_prod_qty")];
										$tot_comp_prod_qty+= $row[("comp_prod_qty")];
										$tot_singe_prod_qty+= $row[("singeing_prod_qty")];
										$tot_brush_prod_qty+= $row[("brush_prod_qty")];
								
							}
				
						?>
						</tbody>
			   
					</table>
                  <table width="<? echo $width; ?>" border="1" cellpadding="2" cellspacing="0" class="tbl_bottom" rules="all">
                    <tr>
                    <td align="right" width="20"><? //echo number_format($tot_fin_deli_qnty_yds,2);?> </td>
                    <td align="right" width="100"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="100">&nbsp; </td>
                    <td align="right" width="100"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="100">&nbsp; </td>
                    <td align="right" width="100"><? //echo number_format($tot_fin_deli_qnty_yds,2);?> </td>
                    <td align="right" width="100"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="100">&nbsp; </td>
                    <td align="right" width="100"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="80">&nbsp; </td>
                    <td width="80">&nbsp; </td>
                    <td width="80">&nbsp; </td>
                    <td  width="80" align="left">Total</td>
                    <td width="80"><? echo number_format($tot_batch_qty,0);?></td>
                  
                    <td align="right" width="80"> </td>
                    <td  width="80"></td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_prod_qc_qnty_kg,2);?> </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_prod_qc_qnty_yds,2);?> </td>
                    <td  width="80"></td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_prod_qc_qnty_yds,0);?> </td>
                   
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_kg,2);?> </td>
                    <td align="right" width="80"  title="22"><? echo number_format($tot_sliting_prod_qty,0);?> </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? echo number_format($tot_sourcing_prod_qty,0);?>  </td>
                    <td align="right" width="80"></td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? echo number_format($tot_stenter_prod_qty,0);?> </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="80" align="right">  </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,2);?> </td>
                    <td align="right" width="80"><? echo number_format($tot_load_prod_qty,0);?> </td>
                    <td width="100">&nbsp; </td>
                    <td align="right" width="100"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,2);?> </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? echo number_format($tot_rotaion_prod_qty,0);?> </td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,2);?> </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,2);?> </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="80" align="right"><? //echo number_format($tot_heatset_prod_qty,0);?></td>
                    <td align="right" width="80"><? echo number_format($tot_heatset_prod_qty,0);?> </td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,2);?> </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? echo number_format($tot_drying_prod_qty,0);?> </td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,2);?> </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? echo number_format($tot_comp_prod_qty,0);?> </td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,2);?> </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? echo number_format($tot_singe_prod_qty,0);?> </td>
                    <td width="80" align="right"><? //echo number_format($tot_singe_prod_qty,0);?></td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,2);?> </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    
                    
                     <td align="right" title="Brush" width="80"><? //echo number_format($tot_singe_prod_qty,0);?> </td>
                    <td width="80" align="right"><? echo number_format($tot_brush_prod_qty,0);?></td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,2);?> </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="80"><? //echo number_format($tot_fin_prod_qc_qnty_kg,0);?> </td>
                    
                    <td width="80"><? echo number_format($tot_fin_prod_qc_qnty_kg,0);?> </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? echo number_format($tot_fin_deli_qnty_kg,2);?> </td>
                    <td align="right" width=""><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    
                   
                    <tr/>
				</table>
					</div>
                      </div>
	<br>
			   <?
             //  die; 5420
			
			 $width_td=5980;
			   ?>
			  <div style="width:<? echo $width_td;?>px; float:left">
				<p style="width:1000px; float: left;word-break:break-all">
                <?
				 $sub_dyeing_sql_batch="SELECT c.id as batch_id,c.batch_date,c.color_id,c.batch_no,b.item_description as fab_desc,b.batch_qnty as production_qty,b.prod_id,b.roll_no as no_of_roll,c.extention_no,c.sales_order_no,c.sales_order_id,c.booking_no,c.batch_date,c.batch_weight,c.booking_without_order,c.insert_date as batch_date_time,c.update_date,c.dur_req_hr,c.dur_req_min,
 d.cust_style_ref,d.order_no,e.party_id as buyer_id from pro_batch_create_mst c,pro_batch_create_dtls b , subcon_ord_dtls d,subcon_ord_mst e where   c.id=b.mst_id and d.id=b.po_id  and  d.job_no_mst=e.subcon_job and c.entry_form=36 and c.status_active=1 and b.status_active=1 and d.status_active=1 $sales_cond $batch_year_cond $batch_cond2   $batch_prod_date_cond $buyer_cond1 order by c.id asc";  
	$sub_dyeing_batch_data = sql_select($sub_dyeing_sql_batch);
	
	//$sub_all_batch_id="";
	foreach( $sub_dyeing_batch_data as $row )
	{
		
		$fab_desc=explode(",",$row[csf("fab_desc")]);
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["batch_no"]=$row[csf("batch_no")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["booking_type"]=$row[csf("booking_type")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["booking_id"]=$row[csf("booking_id")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["end_date"]=$row[csf("end_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["color_id"]=$row[csf("color_id")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["extention_no"]=$row[csf("extention_no")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["batch_qty"]+=$row[csf("production_qty")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["batch_wgt"]=$row[csf("batch_weight")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["sales_order_no"]=$row[csf("sales_order_no")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["booking_no"]=$row[csf("booking_no")];	
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["const_composition"].=$fab_desc[0].',';
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["entry_form"]=$row[csf("entry_form")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["batch_date"]=$row[csf("batch_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["insert_date"]=$row[csf("insert_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["booking_without_order"]=$row[csf("booking_without_order")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["end_date"]=$row[csf("end_date")];
		$is_dyeing_done[$row[csf("batch_id")]] = $row[csf("batch_id")];
		$load_unload_time_arr[$row[csf("batch_id")]]["unload_machine_id"]=$row[csf("machine_id")];
		$load_unload_time_arr[$row[csf("batch_id")]]["unload_result"].=$row[csf("result")].',';
		//echo $row[csf("ltb_btb_id")].'ff';
		$load_unload_time_arr[$row[csf("batch_id")]]["unload_ltb_btb_id"]=$ltb_btb[$row[csf("ltb_btb_id")]];
		//$load_unload_time_arr[$row[csf("batch_id")]]["const_composition"]=$row[csf("fab_desc")];
		$load_unload_time_arr[$row[csf("batch_id")]]["result"]=$dyeing_result[$row[csf("result")]];
		$load_unload_time_arr[$row[csf("batch_id")]]["unload_shift_name"]=$shift_name[$row[csf("shift_name")]].',';
		$load_unload_time_arr[$row[csf("batch_id")]]["end_date"].=$row[csf("end_date")].',';
		$load_unload_time_arr[$row[csf("batch_id")]]["process_time"].=$row[csf("end_hours")].":".$row[csf("end_minutes")].',';
		
		$load_unload_time_arr2[$row[csf("batch_id")]][$row[csf("end_date")]]["hr_min"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$load_unload_time_arr2[$row[csf("batch_id")]][$row[csf("end_date")]]["shift_name"]=$shift_name[$row[csf("shift_name")]];
		
		$sub_all_batch_arr[$row[csf("batch_id")]]=$row[csf("batch_id")];
		//if($sub_all_batch_id=="") $sub_all_batch_id=$row[csf("batch_id")];else $sub_all_batch_id.=",".$row[csf("batch_id")];
		
		if($row[csf("within_group")]==2)
		{
			$booking_data_arr2[$row[csf("sales_order_no")]]["buyer_id"]=$buyer_list[$row[csf("fso_buyer")]];
		}
		else
		{
			$booking_data_arr[$row[csf("booking_no")]]["buyer_id"]=$buyer_list[$row[csf("po_buyer")]];
		}
		$po_data_arr[$row[csf("batch_id")]]["style_ref_no"]=$row[csf("cust_style_ref")];
		$po_data_arr[$row[csf("batch_id")]]["buyer_id"]=$buyer_list[$row[csf("buyer_id")]];
		$po_data_arr[$row[csf("batch_id")]]["order_no"]=$row[csf("order_no")];
	}
	unset($sub_dyeing_batch_data);
	
	
         $sub_dyeing_sql_unload="SELECT a.id as mst_id,a.machine_id,a.ltb_btb_id,a.shift_name,a.fabric_type,a.result,a.service_company,a.floor_id,a.service_source,a.insert_date,a.remarks,a.batch_id, a.result,a.batch_no,a.process_id,a.entry_form,a.start_hours,a.start_minutes,a.production_date as end_date,a.load_unload_id, a.batch_ext_no,a.process_start_date,a.process_end_date,a.end_minutes,a.end_hours,b.batch_qnty as production_qty,b.prod_id,b.roll_no as no_of_roll,c.extention_no,c.sales_order_no,c.sales_order_id,c.booking_no,c.batch_date,c.batch_weight,c.booking_without_order,c.insert_date as batch_date_time,c.update_date,c.dur_req_hr,c.dur_req_min,
 d.cust_style_ref,d.order_no,e.party_id as buyer_id from pro_batch_create_mst c,pro_fab_subprocess a,pro_batch_create_dtls b , subcon_ord_dtls d,subcon_ord_mst e where   c.id=b.mst_id and c.id=a.batch_id and d.id=b.po_id and b.mst_id=a.batch_id and  d.job_no_mst=e.subcon_job and c.entry_form=36 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and a.load_unload_id=2 $sales_cond $batch_year_cond $batch_cond3 $prod_comp_cond $prod_date_cond $buyer_cond1 order by a.id asc"; 
	$sub_dyeing_unload_data = sql_select($sub_dyeing_sql_unload);
	
	$sub_all_batch_id="";
	foreach( $sub_dyeing_unload_data as $row )
	{
		
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["batch_no"]=$row[csf("batch_no")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["booking_type"]=$row[csf("booking_type")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["booking_id"]=$row[csf("booking_id")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["end_date"]=$row[csf("end_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["extention_no"]=$row[csf("extention_no")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["batch_qty"]=$row[csf("batch_weight")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["batch_wgt"]=$row[csf("batch_weight")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["sales_order_no"]=$row[csf("sales_order_no")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["booking_no"]=$row[csf("booking_no")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["entry_form"]=$row[csf("entry_form")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["batch_date"]=$row[csf("batch_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["insert_date"]=$row[csf("insert_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["booking_without_order"]=$row[csf("booking_without_order")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["end_date"]=$row[csf("end_date")];
		$is_dyeing_done[$row[csf("batch_id")]] = $row[csf("batch_id")];
		$load_unload_time_arr[$row[csf("batch_id")]]["unload_machine_id"]=$row[csf("machine_id")];
		$load_unload_time_arr[$row[csf("batch_id")]]["unload_result"].=$row[csf("result")].',';
		//echo $row[csf("ltb_btb_id")].'ff';
		$load_unload_time_arr[$row[csf("batch_id")]]["unload_ltb_btb_id"]=$ltb_btb[$row[csf("ltb_btb_id")]];
		$load_unload_time_arr[$row[csf("batch_id")]]["fabric_type"]=$fabric_type_for_dyeing[$row[csf("fabric_type")]];
		$load_unload_time_arr[$row[csf("batch_id")]]["result"]=$dyeing_result[$row[csf("result")]];
		$load_unload_time_arr[$row[csf("batch_id")]]["unload_shift_name"]=$shift_name[$row[csf("shift_name")]].',';
		$load_unload_time_arr[$row[csf("batch_id")]]["end_date"].=$row[csf("end_date")].',';
		$load_unload_time_arr[$row[csf("batch_id")]]["process_time"].=$row[csf("end_hours")].":".$row[csf("end_minutes")].',';
		
		$load_unload_time_arr2[$row[csf("batch_id")]][$row[csf("end_date")]]["hr_min"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$load_unload_time_arr2[$row[csf("batch_id")]][$row[csf("end_date")]]["shift_name"]=$shift_name[$row[csf("shift_name")]];
		
		$sub_all_batch_arr[$row[csf("batch_id")]]=$row[csf("batch_id")];
		if($sub_all_batch_id=="") $sub_all_batch_id=$row[csf("batch_id")];else $sub_all_batch_id.=",".$row[csf("batch_id")];
		
		if($row[csf("within_group")]==2)
		{
			$booking_data_arr2[$row[csf("sales_order_no")]]["buyer_id"]=$buyer_list[$row[csf("fso_buyer")]];
		}
		else
		{
			$booking_data_arr[$row[csf("booking_no")]]["buyer_id"]=$buyer_list[$row[csf("po_buyer")]];
		}
		$po_data_arr[$row[csf("batch_id")]]["style_ref_no"]=$row[csf("cust_style_ref")];
		$po_data_arr[$row[csf("batch_id")]]["buyer_id"]=$buyer_list[$row[csf("buyer_id")]];
		$po_data_arr[$row[csf("batch_id")]]["order_no"]=$row[csf("order_no")];
	}
	unset($sub_dyeing_unload_data);
	
	$batch_ids=count($sub_all_batch_arr);
	if($db_type==2 && $batch_ids>1000)
	{
		$sub_batch_cond_for=" and (";
		$batIdsArr=array_chunk($sub_all_batch_arr,999);
		foreach($batIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$sub_batch_cond_for.=" a.batch_id in($ids) or";
		}
		$sub_batch_cond_for=chop($sub_batch_cond_for,'or ');
		$sub_batch_cond_for.=")";
	}
	else
	{
		$sub_batch_cond_for=" and a.batch_id in(".implode(",",$sub_all_batch_arr).")";
	}
	
	
	
	//$all_batch_ids=explode(",",$all_batch_id);
	
/*echo $sub_load_unload_sql="SELECT a.id as mst_id,a.result,a.service_company,a.shift_name,a.floor_id,a.fabric_type,a.result,a.service_source,a.insert_date,a.batch_id,a.batch_no,a.process_id,a.entry_form,a.start_hours,a.start_minutes,a.production_date as end_date,a.load_unload_id, a.batch_ext_no,a.process_start_date,a.process_end_date,a.end_minutes,a.end_hours,b.production_qty,b.batch_qty,b.prod_id,b.no_of_roll,c.sales_order_no,c.sales_order_id,c.booking_no,c.extention_no,c.batch_date,c.insert_date,c.booking_without_order,c.dur_req_hr,c.dur_req_min from pro_batch_create_mst c,pro_fab_subprocess a,pro_batch_create_dtls b where  a.id=b.mst_id and c.id=a.batch_id and a.load_unload_id=1 and a.entry_form=38  and a.status_active=1 and b.status_active=1  and c.status_active=1  $sales_cond $batch_year_cond $batch_cond3 $prod_comp_cond $sub_batch_cond_for order by a.id,a.batch_id desc";*/
/* echo $sub_load_unload_sql="SELECT a.id as mst_id,a.result,a.service_company,a.floor_id,a.service_source,a.insert_date,a.batch_id,a.batch_no,a.process_id,a.entry_form,a.start_hours,a.start_minutes,a.production_date as end_date,a.load_unload_id, a.batch_ext_no,a.process_start_date,a.process_end_date,a.end_minutes,a.end_hours,c.sales_order_no,c.sales_order_id,c.booking_no,c.extention_no,c.batch_date,c.insert_date,c.booking_without_order,c.dur_req_hr,c.dur_req_min from pro_batch_create_mst c,pro_fab_subprocess a where  c.id=a.batch_id and a.load_unload_id=1 and a.entry_form=38 and a.status_active=1 and c.status_active=1  $batch_year_cond $batch_cond3 $prod_comp_cond $sub_batch_cond_for order by a.id,a.batch_id desc";die;
	$sub_dying_result_data = sql_select($sub_load_unload_sql);
	foreach($sub_dying_result_data as $row )
	{
		//$load_time_arr[$row[csf("batch_id")]]["prod_qty"]+=$row[csf("production_qty")];
		$load_time_arr[$row[csf("batch_id")]]["end_date"].=$row[csf("process_end_date")].',';
		$load_time_arr[$row[csf("batch_id")]]["load_shift_name"].=$shift_name[$row[csf("shift_name")]].',';
		//$load_time_arr[$row[csf("batch_id")]]["start_date"]=$row[csf("end_date")];
		$load_time_arr2[$row[csf("batch_id")]][$row[csf("process_end_date")]]["end_time"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		//$load_time_arr2[$row[csf("batch_id")]]["end_date"]=$row[csf("end_date")];
		$load_time_arr2[$row[csf("batch_id")]][$row[csf("process_end_date")]]["load_shift_name"]=$row[csf("load_shift_name")];
	}
	unset($sub_dying_result_data);*/
	
	$sub_load_unload_sql="SELECT a.id as mst_id,a.result,a.service_company,a.floor_id,a.service_source,a.insert_date,a.batch_id,a.batch_no,a.process_id,a.entry_form,a.start_hours,a.start_minutes,a.production_date as end_date,a.load_unload_id, a.batch_ext_no,a.process_start_date,a.process_end_date,a.end_minutes,a.end_hours,c.sales_order_no,c.sales_order_id,c.booking_no,c.extention_no,c.batch_date,c.insert_date,c.booking_without_order,c.dur_req_hr,c.dur_req_min from pro_batch_create_mst c,pro_fab_subprocess a where  c.id=a.batch_id and a.load_unload_id=1 and a.entry_form=38 and a.status_active=1 and c.status_active=1  $batch_year_cond $batch_cond3 $prod_comp_cond $sub_batch_cond_for order by a.id,a.batch_id desc";
	$sub_dying_result_data = sql_select($sub_load_unload_sql);//For SubCon
	foreach($sub_dying_result_data as $row )
	{
		$load_time_arr[$row[csf("batch_id")]]["end_date"].=$row[csf("process_end_date")].',';
		$load_time_arr[$row[csf("batch_id")]]["load_shift_name"].=$shift_name[$row[csf("shift_name")]].',';
		//$load_time_arr[$row[csf("batch_id")]]["start_date"]=$row[csf("end_date")];
		$load_time_arr2[$row[csf("batch_id")]][$row[csf("process_end_date")]]["end_time"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		//$load_time_arr2[$row[csf("batch_id")]]["end_date"]=$row[csf("end_date")];
		$load_time_arr2[$row[csf("batch_id")]][$row[csf("process_end_date")]]["load_shift_name"]=$row[csf("load_shift_name")];
	}
	unset($sub_dying_result_data);
	
	
	
	  $sub_prod_sql="SELECT a.id as mst_id,a.machine_id,a.shift_name,a.result,c.insert_date as batch_date_time,c.update_date,a.service_company,a.floor_id,a.service_source,a.insert_date,a.remarks,a.batch_id,a.previous_process, a.result,a.batch_no,a.process_id,a.remarks,a.entry_form,a.start_hours,a.start_minutes,a.production_date as end_date,a.load_unload_id, a.batch_ext_no,a.process_start_date,a.process_end_date,a.end_minutes,a.end_hours,b.production_qty,b.batch_qty,b.prod_id,b.const_composition,b.no_of_roll,c.sales_order_no,c.sales_order_id,c.booking_no,c.extention_no,c.color_id,c.color_range_id,c.batch_date,c.insert_date,c.booking_without_order,c.dur_req_hr,c.dur_req_min,c.batch_weight from pro_batch_create_mst c,pro_fab_subprocess a,pro_fab_subprocess_dtls b where  a.id=b.mst_id and c.id=a.batch_id  and a.entry_form not in(35,38) and a.status_active=1 and b.status_active=1  and c.status_active=1  $sales_cond $batch_year_cond $batch_cond3 $prod_comp_cond $sub_batch_cond_for order by a.id,a.batch_id desc";
	$sub_result_data = sql_select($sub_prod_sql);
	$process_brush_arr_check=array(68);
	$process_rotation_arr_check=array(206);
	$process_wash_arr_check=array(64);
	$process_scouring_arr_check=array(60);

	
	$sub_all_batch_id="";
	foreach($sub_result_data as $row )
	{
		//$is_dyeing_done[$row[csf("batch_id")]] = $row[csf("batch_id")];
		//$load_unload_time_arr[$row[csf("batch_id")]]["end_date"]=$row[csf("end_date")];
		
		
		//$all_sales_id_arr[$row[csf("sales_order_id")]]=$row[csf("sales_order_id")];
			if($sub_all_batch_id=="") $sub_all_batch_id=$row[csf("batch_id")];else $sub_all_batch_id.=",".$row[csf("batch_id")];
		//$process_ids=explode(",",$row[csf("process_id")]);
		if($row[csf("entry_form")]==32)//HeatSet
		{
		$sub_batch_wise_heat_arr[$row[csf("batch_id")]][$row[csf("end_date")]]["heatset_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$sub_batch_wise_heat_arr[$row[csf("batch_id")]][$row[csf("process_start_date")]]["heatset_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
		$sub_batch_wise_heat_arr[$row[csf("batch_id")]][$row[csf("process_start_date")]]["rotation_shift_name"]=$shift_name[$row[csf("rotation_shift_name")]];
		$sub_batch_wise_heat_arr[$row[csf("batch_id")]][$row[csf("end_date")]]["end_heat_shift_name"]=$shift_name[$row[csf("shift_name")]];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["heatset_start_date"].=$row[csf("process_start_date")].',';
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["heatset_end_date"].=$row[csf("end_date")].',';
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["heatset_prod_qty"]+=$row[csf("production_qty")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["heat_mc_id"]=$row[csf("machine_id")];
		}
		else if($row[csf("entry_form")]==30)//Sliting //
		{
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["sliting_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["sliting_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["sliting_start_date"]=$row[csf("process_start_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["sliting_end_date"]=$row[csf("end_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["sliting_mc_id"]=$row[csf("machine_id")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["sliting_shift_name"]=$shift_name[$row[csf("shift_name")]];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["sliting_prod_qty"]+=$row[csf("production_qty")];
		}
		else if($row[csf("entry_form")]==48)//Stentering
		{
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["stenter_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["stenter_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["stenter_start_date"]=$row[csf("process_start_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["stenter_end_date"]=$row[csf("end_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["stenter_mc_id"]=$row[csf("machine_id")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["stenter_shift_name"]=$shift_name[$row[csf("shift_name")]];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["stenter_prod_qty"]+=$row[csf("production_qty")];
		}
		else if($row[csf("entry_form")]==31)//Drying
		{
			//echo  $row[csf("previous_process")].'f';
		$sub_batch_wise_dry_arr[$row[csf("batch_id")]][$row[csf("end_date")]]["dry_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$sub_batch_wise_dry_arr[$row[csf("batch_id")]][$row[csf("process_start_date")]]["dry_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
		$sub_batch_wise_dry_arr[$row[csf("batch_id")]][$row[csf("process_start_date")]]["start_shift_name"]=$shift_name[$row[csf("shift_name")]];
		$sub_batch_wise_dry_arr[$row[csf("batch_id")]][$row[csf("end_date")]]["end_shift_name"]=$shift_name[$row[csf("shift_name")]];
		
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["dry_start_date"].=$row[csf("process_start_date")].',';
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["dry_end_date"].=$row[csf("end_date")].',';
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["dry_result"]=$row[csf("result")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["dry_shift_name"]=$shift_name[$row[csf("shift_name")]];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["dry_mc_id"]=$row[csf("machine_id")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["dry_prod_qty"]+=$row[csf("production_qty")];
		}
		else if($row[csf("entry_form")]==33)//Compacting
		{
		$sub_batch_wise_comp_arr[$row[csf("batch_id")]][$row[csf("end_date")]]["comp_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$sub_batch_wise_comp_arr[$row[csf("batch_id")]][$row[csf("process_start_date")]]["comp_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
		$sub_batch_wise_comp_arr[$row[csf("batch_id")]][$row[csf("process_start_date")]]["start_shift_name"]=$shift_name[$row[csf("shift_name")]];
		$sub_batch_wise_comp_arr[$row[csf("batch_id")]][$row[csf("end_date")]]["end_shift_name"]=$shift_name[$row[csf("shift_name")]];
		
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["comp_start_date"].=$row[csf("process_start_date")].',';
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["comp_end_date"].=$row[csf("end_date")].',';
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["comp_result"]=$row[csf("result")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["comp_shift_name"]=$shift_name[$row[csf("shift_name")]];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["comp_mc_id"]=$row[csf("machine_id")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["comp_prod_qty"]+=$row[csf("production_qty")];
		}
		else if($row[csf("entry_form")]==47)//Signeing
		{
		$sub_batch_wise_singeing_arr[$row[csf("batch_id")]][$row[csf("end_date")]]["singeing_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$sub_batch_wise_singeing_arr[$row[csf("batch_id")]][$row[csf("process_start_date")]]["singeing_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
		$sub_batch_wise_singeing_arr[$row[csf("batch_id")]][$row[csf("process_start_date")]]["start_shift_name"]=$shift_name[$row[csf("shift_name")]];
		$sub_batch_wise_singeing_arr[$row[csf("batch_id")]][$row[csf("end_date")]]["end_shift_name"]=$shift_name[$row[csf("shift_name")]];
		
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["singeing_start_date"].=$row[csf("process_start_date")].',';
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["singeing_end_date"].=$row[csf("end_date")].',';
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["singeing_result"]=$row[csf("result")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["singeing_shift_name"]=$shift_name[$row[csf("shift_name")]];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["singeing_mc_id"]=$row[csf("machine_id")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["singeing_prod_qty"]+=$row[csf("production_qty")];
		}
		else if($row[csf("entry_form")]==34 && $row[csf("process_id")]==68)//Brush
		{
		$sub_batch_wise_brush_arr[$row[csf("batch_id")]][$row[csf("end_date")]]["brush_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		$sub_batch_wise_brush_arr[$row[csf("batch_id")]][$row[csf("process_start_date")]]["brush_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
		$sub_batch_wise_brush_arr[$row[csf("batch_id")]][$row[csf("process_start_date")]]["start_shift_name"]=$shift_name[$row[csf("shift_name")]];
		$sub_batch_wise_brush_arr[$row[csf("batch_id")]][$row[csf("end_date")]]["end_shift_name"]=$shift_name[$row[csf("shift_name")]];
		
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["brush_start_date"].=$row[csf("process_start_date")].',';
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["brush_end_date"].=$row[csf("end_date")].',';
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["brush_result"]=$row[csf("result")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["brush_shift_name"]=$shift_name[$row[csf("shift_name")]];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["brush_mc_id"]=$row[csf("machine_id")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["brush_prod_qty"]+=$row[csf("production_qty")];
		}
		
		
		else if($row[csf("entry_form")]==34 && $row[csf("result")]>0)//SpecilaFinish
		{
			
			if(in_array($row[csf("process_id")],$process_scouring_arr_check))//Prod Type scouring 
			{
				
				//echo $row[csf("process_id")]."G";
			$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["type_sourcing_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
			$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["type_sourcing_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
			$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["type_sourcing_start_date"]=$row[csf("process_start_date")];
			$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["type_sourcing_end_date"]=$row[csf("end_date")];
			$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["sourcing_mc_id"]=$row[csf("machine_id")];
			$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["sourcing_shift_name"]=$shift_name[$row[csf("shift_name")]];
			$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["sourcing_prod_qty"]+=$row[csf("production_qty")];
			}
			else if(in_array($row[csf("process_id")],$process_rotation_arr_check))//Prod Type Rotaion 
			{
				
				//echo $row[csf("process_id")]."G";
			$sub_batch_wise_rotaion_arr[$row[csf("batch_id")]][$row[csf("end_date")]]["rotaion_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
			$sub_batch_wise_rotaion_arr[$row[csf("batch_id")]][$row[csf("process_start_date")]]["rotaion_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
			$sub_batch_wise_rotaion_arr[$row[csf("batch_id")]][$row[csf("process_start_date")]]["rotation_shift_name"]=$shift_name[$row[csf("rotation_shift_name")]];
			$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["rotaion_start_date"].=$row[csf("process_start_date")].',';
			$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["rotaion_end_date"].=$row[csf("end_date")].',';
			$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["rotaion_result"]=$row[csf("result")];
			$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["rotaion_mc_id"]=$row[csf("machine_id")];
			$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["rotation_shift_name"]=$shift_name[$row[csf("shift_name")]];
			$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["rotaion_prod_qty"]+=$row[csf("production_qty")];
			}else if(in_array($row[csf("process_id")],$process_wash_arr_check))//Prod Type wash 
			{
				
				//echo $row[csf("process_id")]."G";
			$batch_wise_wash_arr[$row[csf("batch_id")]][$row[csf("end_date")]]["wash_endtime"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
			$batch_wise_wash_arr[$row[csf("batch_id")]][$row[csf("process_start_date")]]["wash_starttime"]=$row[csf("start_hours")].":".$row[csf("start_minutes")];
			$batch_wise_wash_arr[$row[csf("batch_id")]][$row[csf("process_start_date")]]["wash_shift_name"]=$shift_name[$row[csf("shift_name")]];

			$batch_wise_dying_arr[$row[csf("batch_id")]]["wash_start_date"].=$row[csf("process_start_date")].',';
			$batch_wise_dying_arr[$row[csf("batch_id")]]["wash_end_date"].=$row[csf("end_date")].',';
			$batch_wise_dying_arr[$row[csf("batch_id")]]["wash_result"]=$row[csf("result")];
			$batch_wise_dying_arr[$row[csf("batch_id")]]["wash_mc_id"]=$row[csf("machine_id")];
			$batch_wise_dying_arr[$row[csf("batch_id")]]["wash_shift_name"]=$shift_name[$row[csf("shift_name")]];
			$batch_wise_dying_arr[$row[csf("batch_id")]]["wash_prod_qty"]+=$row[csf("production_qty")];
			}





		}
		
			
		//echo $Batchtimecal.'d';					
		
		
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["result"]=$row[csf("result")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["color_id"]=$row[csf("color_id")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["color_range_id"]=$row[csf("color_range_id")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["const_composition"].=$row[csf("const_composition")].',';
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["batch_hr_min"]=$batch_date_time;//$row[csf("dur_req_hr")].":".$row[csf("dur_req_min")];
		//$batch_wise_dying_arr[$row[csf("batch_id")]]["batch_min"]=$row[csf("dur_req_min")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["batch_no"]=$row[csf("batch_no")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["end_date"]=$row[csf("end_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["extention_no"]=$row[csf("extention_no")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["batch_qty"]=$row[csf("batch_qty")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["batch_wgt"]=$row[csf("batch_weight")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["sales_order_no"]=$row[csf("sales_order_no")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["booking_no"]=$row[csf("booking_no")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["entry_form"]=$row[csf("entry_form")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["batch_date"]=$row[csf("batch_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["insert_date"]=$row[csf("insert_date")];
		$sub_batch_wise_dying_arr[$row[csf("batch_id")]]["booking_without_order"]=$row[csf("booking_without_order")];
		
		$sub_all_to_batch_arr[$row[csf("batch_id")]]=$row[csf("batch_id")];
		
		
	}
	unset($sub_result_data);//booking_id_arr
	$batchids=count($sub_all_to_batch_arr);
	if($db_type==2 && $batchids>1000)
	{
		$sub_batch_cond_for2=" and (";
		$batIdsArr=array_chunk($sub_all_to_batch_arr,999);
		foreach($batIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$sub_batch_cond_for2.=" b.batch_id in($ids) or";
		}
		$sub_batch_cond_for2=chop($sub_batch_cond_for2,'or ');
		$sub_batch_cond_for2.=")";
	}
	else
	{
		$sub_batch_cond_for2=" and b.batch_id in(".implode(",",$sub_all_to_batch_arr).")";
	}
	//print_r($booking_id_arr);
	$sub_batch_cond_for3=str_replace('b.batch_id','a.id',$sub_batch_cond_for2);
	$sub_batch_cond_for4=str_replace('b.batch_id','b.id',$sub_batch_cond_for2);
	
	//$sql_rec= "SELECT  b.id as batch_id,b.batch_weight,b.batch_no,b.booking_no,b.sales_order_no,b.color_id,b.color_range_id,c.id as recipe_id from   pro_batch_create_mst b,pro_recipe_entry_mst c where   c.batch_id=b.id  and b.status_active=1 and b.is_deleted=0  ".where_con_using_array($sub_all_batch_arr,0,'b.id')."   group by  b.id ,b.batch_no,b.booking_no,b.color_id,b.color_range_id,c.id,b.sales_order_no,b.batch_weight";
	
	
	$sql_sales_order= "SELECT  b.id as batch_id,b.batch_weight,b.batch_no,b.booking_no,b.sales_order_no,b.color_id,b.color_range_id,c.id as recipe_id,e.mst_id as req_id ,f.requ_no from   pro_batch_create_mst b,pro_recipe_entry_mst c,dyes_chem_requ_recipe_att e,dyes_chem_issue_requ_dtls f where   c.batch_id=b.id and e.recipe_id=c.id and f.mst_id=e.mst_id  and b.status_active=1 and b.is_deleted=0  ".where_con_using_array($sub_all_batch_arr,0,'b.id')."   group by  b.id ,b.batch_no,b.booking_no,b.color_id,b.color_range_id,f.requ_no,c.id,b.sales_order_no,b.batch_weight,e.mst_id";
	$result_recipe_data=sql_select($sql_sales_order); //sub_all_batch_arr
	foreach ($result_recipe_data as $row)
	{
		$recipe_batch_id_arr[$row[csf("batch_id")]]['recipe_id'].=$row[csf("recipe_id")].',';
		$recipe_batch_id_arr[$row[csf("batch_id")]]['requ_no'].=$row[csf("requ_no")].',';
		$recipe_batch_id_arr[$row[csf("batch_id")]]['requ_id'].=$row[csf("req_id")].',';
	//	if($all_req_ids=='') $all_req_ids=$value[csf("req_id")];else $all_req_ids.=",".$value[csf("req_id")];
		//if($all_recipe_ids=='') $all_recipe_ids=$value[csf("recipe_id")];else $all_recipe_ids.=",".$value[csf("recipe_id")];
	}
	unset($result_recipe_data);
	
	//echo $batch_cond_for3.'dDD';
	
	$sql_batch="SELECT a.id,b.prod_id,b.width_dia_type,b.body_part_id from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and  a.status_active=1 and  b.status_active=1  $sub_batch_cond_for3" ;
	foreach(sql_select($sql_batch) as $row )
	{
			$batch_data_dtls_arr[$row[csf("id")]]["body_part_id"].=$body_part[$row[csf("body_part_id")]].',';
			$batch_data_dtls_arr[$row[csf("id")]]["width_dia_type"].=$fabric_typee[$row[csf("width_dia_type")]].',';
	}
	$yarncount = return_library_array("select id, yarn_count from  lib_yarn_count", 'id', 'yarn_count');
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$sql_mc=sql_select("select machine_no,id,prod_capacity from  lib_machine_name where is_deleted=0");
	foreach($sql_mc as $row)
	{
		$machine_name_arr[$row[csf('id')]]=$row[csf('machine_no')];
		if($row[csf('prod_capacity')]>0)
		{
		$machine_capacity_arr[$row[csf('id')]]=$row[csf('prod_capacity')];
		}
	}
	
	//$machine_name_arr = return_library_array("select machine_no,id from  lib_machine_name where is_deleted=0", "id", "machine_no");

	$cons_comp_sql = sql_select("select a.id, a.construction,c.composition_name, b.percent
		from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b, lib_composition_array c
		where a.id = b.mst_id and b.copmposition_id = c.id  and a.status_active=1 and b.status_active=1 and c.status_active=1");
	foreach ($cons_comp_sql as  $val)
	{
		$cons_comp_arr[$val[csf("id")]]["const"] = $val[csf("construction")];
		$cons_comp_arr[$val[csf("id")]]["compo"] =$val[csf("composition_name")] .",". $val[csf("percent")] . "% ";

	}
	
	$sub_fin_fab_sql= sql_select("select c.insert_date,c.product_date, b.batch_id,b.product_qnty,d.order_uom from  subcon_production_mst c,subcon_production_dtls b,subcon_ord_dtls d where c.id=b.mst_id and b.order_id=d.id and c.status_active=1  and c.entry_form in(292)  and b.status_active=1 and b.is_deleted=0  $sub_batch_cond_for2 order by b.batch_id");
	//echo "select c.insert_date,c.product_date, b.batch_id,b.product_qnty,d.order_uom from  subcon_production_mst c,subcon_production_dtls b,subcon_ord_dtls d where c.id=b.mst_id and b.order_id=d.id and c.status_active=1  and c.entry_form in(292)  and b.status_active=1 and b.is_deleted=0  $sub_batch_cond_for2 order by b.batch_id";
	foreach($sub_fin_fab_sql as $row)
	{
		//$sub_fin_fab_arr[$row[csf("batch_id")]]["uom"]=$row[csf("uom")];
		$sub_fin_fab_arr[$row[csf("batch_id")]]["receive_date"]=$row[csf("product_date")];
		$sub_fin_fab_arr[$row[csf("batch_id")]]["insert_date"]=$row[csf("insert_date")];
		$sub_fin_fab_qty_arr[$row[csf("batch_id")]][$row[csf("order_uom")]]["receive_qnty"]+=$row[csf("product_qnty")];
	}
	//print_r($sub_fin_fab_qty_arr); 
	unset($sub_fin_fab_sql);
	$sub_fin_delivery_sql= sql_select("select c.insert_date,c.delivery_date, b.batch_id,b.delivery_qty,d.order_uom from  subcon_delivery_dtls b,subcon_delivery_mst c,subcon_ord_dtls d  where  b.mst_id=c.id  and b.order_id=d.id and d.status_active=1 and c.status_active=1 and c.process_id in(4)  and b.status_active=1 and b.is_deleted=0 $sub_batch_cond_for2 order by b.batch_id"); //$sub_batch_cond_for2
//echo "select c.insert_date,c.delivery_date, b.batch_id,b.delivery_qty,d.order_uom from  subcon_delivery_dtls b,subcon_delivery_mst c,subcon_ord_dtls d  where  b.mst_id=c.id  and b.order_id=d.id and d.status_active=1 and c.status_active=1 and c.process_id in(4)  and b.status_active=1 and b.is_deleted=0 $sub_batch_cond_for2 order by b.batch_id";
	
	foreach($sub_fin_delivery_sql as $row)
	{
		$sub_fin_fab_deli_arr[$row[csf("batch_id")]]["uom"]=$row[csf("uom")];
		$sub_fin_fab_deli_arr[$row[csf("batch_id")]]["delevery_date"]=$row[csf("delivery_date")];
		$sub_fin_fab_deli_arr[$row[csf("batch_id")]]["insert_date"]=$row[csf("insert_date")];
		$sub_fin_fab_deli_qty_arr[$row[csf("batch_id")]][$row[csf("order_uom")]]["current_delivery"]+=$row[csf("delivery_qty")];
	}
	unset($sub_fin_delivery_sql);
	//print_r($fin_fab_deli_qty_arr);
							
				?>
                </p>
                
						<table class="rpt_table" width="<? echo $width_td;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
							<?
                              ?>
                            <thead>
								<tr>
									<th width="20" class="67" rowspan="2">SL</th>
									<th width="100" class="" rowspan="2">Buyer</th>
									<th width="100" class="" rowspan="2">Order No </th>
									<th width="100" rowspan="2" class="">Item Desc.</th>
									<th width="100" rowspan="2" style="word-break:break-all" class="">Dia/Width Type</th>
                                 
                                    <th width="80" rowspan="2" style="word-break:break-all" class="">Color Name</th>
                                    <th width="80" rowspan="2" style="word-break:break-all" class="">Color wise <br>required<br> Fabirc Qty</th>
                                    
                                    
                                    <th colspan="3">Batch Information</th>
									<th colspan="2">Chemical & Dyes Cons.</th>
                                    
									<th colspan="5">Slitting</th>
									<th colspan="5">Bleaching / Scouring </th>
									<th colspan="5" style="word-break:break-all">Stentering </th>
									<th colspan="8">Dyeing Production</th>
									<th colspan="4" style="word-break:break-all">Rotation </th>
									<th colspan="5">Wash</th>
									<th colspan="5" style="word-break:break-all">Heat set</th>
                                    
									<th colspan="5">Drying</th>
									<th colspan="5">Compacting</th>
                                    <th colspan="5">Singeing</th>
                                    <th colspan="5">Brush</th>
                                    
									<th colspan="2">Finish Fabric<br>Prduction	</th>
									<th colspan="2">Delivery To <br>Finish<br>Fabirc Store	</th>
								</tr>
								
								<tr>
									<th width="80" class="">Batch Date</th>
									<th width="80" class="">Batch No</th>
									<th width="80" class="">Extn. No</th>
                                    
                                    <th width="80" class="">Recipe Info</th>
									<th width="80" class="">Requisition<br>Issue Info</th>
								
                                    <th width="80" class="">Machine No</th>
									<th width="80" class="">Quantity</th>
									<th width="80" class="">Start Date & Time</th>
									<th width="80" class="">End Time <br>Date & Shift</th>
									<th width="80" style="word-break:break-all" class="">Time Used</th>
                                    <th width="80" class="">Machine No</th>
									<th width="80" class="">Quantity</th>
									<th width="80" class="">Start Date & Time</th>
									<th width="80" class="">End Time<br> Date & Shift</th>
									<th width="80" style="word-break:break-all" class="">Time Used</th>
                                    
                                    <th width="80" title="Stenter" class="">Machine No</th>
									<th width="80" class="">Quantity</th>
									<th width="80" class="">Start Date & Time</th>
									<th width="80" class="">End Time<br> Date & Shift</th>
									<th width="80" style="word-break:break-all" class="">Time Used</th>
                                    
									<th width="80" title="Dyeing" class="">Machine No</th>
                                    <th width="80" title="Dyeing" class="">Machine Capacity</th>
									<th width="80" class="">Load Qty</th>
									<th width="100" class="">Load Date<br> Time & Shift</th>
									<th width="100" class="">UnLoad Date<br> Time & Shift</th>
									<th width="80" style="word-break:break-all" class="">Time Used</th>
                                    <th width="80" class="">BTB/LTB</th>
                                    <th width="80" class="">Result</th>
                                  
                                    
                                    <th width="80" title="Rotation" class="">Quantity</th>
									<th width="80" class="">Start Date & Time</th>
									<th width="80" class="">End Date & Time</th>
									<th width="80" style="word-break:break-all" class="">Time Used</th>
                                    
                                     <th width="80" title="Wash" class="">Machine No</th>
									<th width="80" class="">Quantity</th>
									<th width="80" class="">Start Date & Time</th>
									<th width="80" class="">End Time<br> Date & Shift</th>
									<th width="80" style="word-break:break-all" class="">Time Used</th>
                                    
                                    <th width="80" title="Heatset" class="">Machine No</th>
									<th width="80" class="">Quantity</th>
									<th width="80" class="">Start Date & Time</th>
									<th width="80" class="">End Time<br> Date & Shift</th>
									<th width="80" style="word-break:break-all" class="">Time Used</th>
                                    
                                     <th width="80" title="Drying" class="">Machine No</th>
									<th width="80" class="">Quantity</th>
									<th width="80" class="">Start Date & Time</th>
									<th width="80" class="">End Time<br> Date & Shift</th>
									<th width="80" style="word-break:break-all" class="">Time Used</th>
                                    
                                    <th width="80" title="15 Compacting" class="">Machine No</th>
									<th width="80" class="">Quantity</th>
									<th width="80" class="">Start Date & Time</th>
									<th width="80" class="">End Time<br> Date & Shift</th>
									<th width="80" style="word-break:break-all" class="">Time Used</th>
                                    
                                    <th width="80" title="Singeing" class="">Machine No</th>
									<th width="80" class="" title="SL=70">Quantity</th>
									<th width="80" class="">Start Date & Time</th>
									<th width="80" class="">End Time<br> Date & Shift</th>
									<th width="80" style="word-break:break-all" class="">Time Used</th>
                                    
                                    <th width="80" title="Brush" class="">Machine No</th>
									<th width="80" class="" title="SL=70">Quantity</th>
									<th width="80" class="">Start Date & Time</th>
									<th width="80" class="">End Time<br> Date & Shift</th>
									<th width="80" style="word-break:break-all" class="">Time Used</th>
                                    
									<th width="80" title="Fin Fab Prod" class="">Quantity</th>
									<th width="80" class="">Date</th>
									<th width="80" style="word-break:break-all" class="">Quantity</th>
									<th width="" style="word-break:break-all" class="">Date</th>
								</tr>
                                
							</thead>
						</table>
						<div style=" max-height:380px; width:<? echo $width_td+20;?>px; overflow-y:scroll;" id="scroll_body">
						<table align="left" class="rpt_table" id="table_body" width="<? echo $width_td;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tbody>
								<tr>
								<td colspan="67"><b>Inbound Subcontract </b></td>
								</tr>
									<?
									$tot_batch_qty=$tot_fin_prod_qc_qnty_kg=$tot_sliting_prod_qty=$tot_fin_deli_qnty_kg=$tot_stenter_prod_qty=$tot_sourcing_prod_qty=$tot_load_prod_qty=$tot_rotaion_prod_qty=$tot_heatset_prod_qty=$tot_drying_prod_qty=$tot_comp_prod_qty=$tot_singe_prod_qty=$tot_brush_prod_qty=0;
									$ii=1;
									foreach($sub_batch_wise_dying_arr as $batch_id=>$row)
									{
										
										if ($ii%2==0)
											$bgcolor="#E9F3FF";
										else
											$bgcolor="#FFFFFF";
									
										$heatset_start_date=$row[("heatset_start_date")];
										$heatset_start_time=$row[("heatset_starttime")];
										$heatset_end_date=$row[("heatset_end_date")];
										$heatset_endtime=$row[("heatset_endtime")];
										$batch_hr_min=$row[("batch_hr_min")].':00';
									//	echo $batch_hr_min.', ';
										$batch_date_time=($row["batch_date"].' '.$batch_hr_min);
										$heat_date_time=($heatset_end_date.' '.$heatset_endtime.':'.'00');
										$batchtoheat_time_diff=datediff(n,$batch_date_time ,$heat_date_time);
									//	echo $batch_date_time.'='.$heat_date_time;
										//$diff_time_days=$total_time_diff;
										$unload_dateArr=rtrim($load_unload_time_arr[$batch_id]["end_date"],',');
										$unload_dateData=array_unique(explode(",",$unload_dateArr));
										$unload_prod_qty=$load_unload_time_arr[$batch_id]["prod_qty"];
										$unload_machine_id=$load_unload_time_arr[$batch_id]["unload_machine_id"];
										$unload_ltb_btb=$load_unload_time_arr[$batch_id]["unload_ltb_btb_id"];
										$fabric_type=$load_unload_time_arr[$batch_id]["fabric_type"];
										$resultName=$load_unload_time_arr[$batch_id]["result"];
										
										$load_shift_name=$load_time_arr[$batch_id]["load_shift_name"];
										$load_prod_qty=$load_time_arr[$batch_id]["prod_qty"];
										//$load_date=$load_time_arr[$batch_id]["end_date"];
										$load_dateArr=rtrim($load_time_arr[$batch_id]["end_date"],',');
										//echo $load_dateArr.'ddd';
										$load_dateData=array_unique(explode(",",$load_dateArr));
										
										//$load_time=$load_time_arr[$batch_id]["end_time"];
										//$load_date_time=(change_date_format($load_date).' '.$load_time.':'.'00');
										//echo $batch_date_time.'='.$unload_date_time2;
										//$dying_time_used_diff=datediff(n,$load_date_time ,$unload_date_time);
										
										$sliting_start_date=$row[("sliting_start_date")];
										$sliting_start_time=$row[("sliting_starttime")];
										$sliting_end_date=$row[("sliting_end_date")];
										$sliting_endtime=$row[("sliting_endtime")];
										$sliting_mc_id=$row[("sliting_mc_id")];
										$sliting_shift_name=$row[("sliting_shift_name")];
										$sliting_prod_qty=$row[("sliting_prod_qty")]; 
										//$batch_hr_min=$row[("batch_hr_min")];
										//$batch_date_time=($row["batch_date"].' '.$batch_hr_min.':'.'00');
										$sliting_end_date_time=(change_date_format($sliting_end_date).' '.$sliting_endtime.':'.'00');
										$sliting_start_date_time=(change_date_format($sliting_start_date).' '.$sliting_start_time.':'.'00');
										$sliting_time_used_diff=datediff(n,$sliting_start_date_time ,$sliting_end_date_time); 
										//echo $sliting_date_time.'='.$unload_date_time2;
										$sourcing_mc_id=$row[("sourcing_mc_id")];
										$sourcing_shift_name=$row[("sourcing_shift_name")];
										$sourcing_prod_qty=$row[("sourcing_prod_qty")]; 
										$typewise_sourcing_start_date=$row[("type_sourcing_start_date")];
										$typewise_sourcing_start_time=$row[("type_sourcing_starttime")];
										$typewise_sourcing_end_date=$row[("type_sourcing_end_date")];
										$typewise_sourcing_endtime=$row[("type_sourcing_endtime")];
										//$typewise_sourcing_date_time=($typewise_brush_end_date.' '.$typewise_brush_endtime.':'.'00');
										$sourcing_end_date_time=(change_date_format($typewise_sourcing_end_date).' '.$typewise_sourcing_endtime.':'.'00');
										$sourcing_start_date_time=(change_date_format($typewise_sourcing_start_date).' '.$typewise_sourcing_start_time.':'.'00');
										$sourcing_time_used_diff=datediff(n,$sourcing_start_date_time ,$sourcing_end_date_time);
										
										$rotaion_prod_qty=$row[("rotaion_prod_qty")];
										$rotation_shift_name=$row[("rotation_shift_name")];
										
										$typewise_peach_end_date=$row[("type_peach_end_date")];
										$typewise_peach_endtime=$row[("type_peach_endtime")];
										$typewise_peach_date_time=($typewise_peach_end_date.' '.$typewise_peach_endtime.':'.'00');
										$dyingtotypepeach_time_diff=datediff(n,$unload_date_time ,$typewise_peach_date_time);
										
										$stenter_mc_id=$row[("stenter_mc_id")];
										$stenter_shift_name=$row[("stenter_shift_name")];
										$stenter_prod_qty=$row[("stenter_prod_qty")];
										$stenter_start_date=$row[("stenter_start_date")];
										$stenter_start_time=$row[("stenter_starttime")];
										$stenter_end_date=$row[("stenter_end_date")];
										$stenter_endtime=$row[("stenter_endtime")];
										$stenter_date_time=($stenter_end_date.' '.$stenter_endtime.':'.'00');
										$stenter_end_date_time=(change_date_format($stenter_end_date).' '.$stenter_endtime.':'.'00');
										$stenter_start_date_time=(change_date_format($stenter_start_date).' '.$stenter_start_time.':'.'00');
										$stenter_time_used_diff=datediff(n,$stenter_start_date_time ,$stenter_end_date_time);
										//$dyingtostener_time_diff=datediff(n,$unload_date_time ,$stenter_date_time);
										
										$type_stenter_start_date=$row[("type_stenter_start_date")];
										$type_stenter_start_time=$row[("type_stenter_starttime")];
										$type_stenter_end_date=$row[("type_stenter_end_date")];
										$type_stenter_endtime=$row[("type_stenter_endtime")];
										$type_stenter_date_time=($type_stenter_end_date.' '.$type_stenter_endtime.':'.'00');
										$type_dyingtotype_stener_time_diff=datediff(n,$unload_date_time ,$type_stenter_date_time);
										
										$comp_start_date=$row[("comp_start_date")];
										$comp_start_time=$row[("comp_starttime")];
										$comp_end_date=$row[("comp_end_date")];
										$comp_endtime=$row[("comp_endtime")];
										$comp_date_time=($comp_end_date.' '.$comp_endtime.':'.'00');
										$dyingtocomp_time_diff=datediff(n,$unload_date_time ,$comp_date_time);
										
										$dry_start_date=$row[("dry_start_date")];//
										$dry_starttime=$row[("dry_starttime")];
										$dry_end_date=$row[("dry_end_date")];
										$dry_endtime=$row[("dry_endtime")];
										$dry_date_time=($dry_end_date.' '.$dry_endtime.':'.'00');
										$dyingtodry_time_diff=datediff(n,$unload_date_time ,$dry_date_time);
										
										
										
										
										//$fin_uom=$fin_fab_arr[$batch_id]["uom"];
										$fin_prod_date=$fin_fab_arr[$batch_id]["receive_date"];
										$fin_insert_time=explode(" ",$fin_fab_arr[$batch_id]["insert_date"]);
										$fin_time_convert=$fin_insert_time[1].' '.$fin_insert_time[2];
										if($fin_prod_date!="")
										{
										$fintimecal=strtotime("$fin_time_convert");
										$fintime_cal= date('H:i',$fintimecal);
										$fintime_cal=$fintime_cal.':00';
										} else $fintime_cal="";
										$fin_prod_date_time=($fin_prod_date.' '.$fintime_cal);
										
										$dyingtotypefinprod_time_diff=datediff(n,$unload_date_time ,$fin_prod_date_time);
										$fin_prod_qc_qnty_kg=$sub_fin_fab_qty_arr[$batch_id][12]["receive_qnty"];
										$fin_prod_qc_qnty_yds=$sub_fin_fab_qty_arr[$batch_id][27]["receive_qnty"];
										
										//$fin_uom=$fin_fab_deli_arr[$batch_id]["uom"];
										$fin_deli_date=$sub_fin_fab_deli_arr[$batch_id]["delevery_date"];
										//$fin_deli_insert_time=explode(" ",$fin_fab_deli_arr[$batch_id]["insert_date"]);
										//$fin_deli_time_convert=$fin_deli_insert_time[1].' '.$fin_deli_insert_time[2];
										//echo $fin_deli_time_convert.'gg';
										
										$fin_deli_qnty_kg=$sub_fin_fab_deli_qty_arr[$batch_id][12]["current_delivery"];
										$fin_deli_qnty_yds=$sub_fin_fab_deli_qty_arr[$batch_id][27]["current_delivery"];
										//$batch_hr_min=batch_hr_min;
										$color_id=$row[("color_id")];
										$color_range_id=$row[("color_range_id")];
										$const_composition=rtrim($row[("const_composition")],',');
										$const_compositions=implode(",",array_unique(explode(",",$const_composition)));
										$body_part_id=rtrim($batch_data_dtls_arr[$batch_id]["body_part_id"],',');
										$body_part_ids=implode(",",array_unique(explode(",",$body_part_id)));
										$width_dia_type=rtrim($batch_data_dtls_arr[$batch_id]["width_dia_type"],',');
										$width_dia_types=implode(",",array_unique(explode(",",$width_dia_type)));
										$yarn_lot=rtrim($yarn_lot_arr[$batch_id]['lot'],',');
										$yarn_lots=implode(",",array_unique(explode(",",$yarn_lot)));
										$brand_id=rtrim($yarn_lot_arr[$batch_id]['brand_id'],',');
										$brand_name=implode(",",array_unique(explode(",",$brand_id)));
										$compo=rtrim($yarn_lot_arr[$batch_id]['compo'],',');
										$compos=implode(",",array_unique(explode(",",$compo)));
										$recipe_id=rtrim($recipe_batch_id_arr[$batch_id]['recipe_id'],',');
										//echo $recipe_id.'DDDDDD';
										$recipe_ids=implode(",",array_unique(explode(",",$recipe_id)));
										$requ_no=rtrim($recipe_batch_id_arr[$batch_id]['requ_no'],',');
										$requ_nos=implode(",",array_unique(explode(",",$requ_no)));
										$requ_id=rtrim($recipe_batch_id_arr[$batch_id]['requ_id'],',');
										$requ_ids=implode(",",array_unique(explode(",",$requ_id)));
										$yarn_count=rtrim($yarn_lot_arr[$batch_id]['yarn_count'],',');
										$yarn_counts=array_unique(explode(",",$yarn_count));
										$yarn_count_value = "";
										foreach ($yarn_counts as $val) {
											if ($val > 0) {
												if ($yarn_count_value == '') $yarn_count_value = $yarncount[$val]; else $yarn_count_value .= ", " . $yarncount[$val];
											}
										}
									//	$po_data_arr[$row[csf("batch_id")]]["style_ref_no"]=$row[csf("cust_style_ref")];
										//$po_data_arr[$row[csf("batch_id")]]["buyer_id"]=$buyer_list[$row[csf("buyer_id")]];
										//$po_data_arr[$row[csf("batch_id")]]["order_no"]
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trsub_<? echo $ii; ?>','<? echo $bgcolor;?>')" id="trsub_<? echo $ii; ?>">
											<td width="20" align="center" class=""><? echo $ii++ ;?></td>
											<td width="100" align="center" title="Prod Date"><?  echo $po_data_arr[$batch_id]["buyer_id"];?> </td>
											<td width="100" align="center" class="" style="word-break:break-all" ><?  echo $po_data_arr[$batch_id]["order_no"];;?> </td>
											<td width="100" align="center" style="word-break:break-all" class=""><? echo $const_compositions;?></td>
											<td width="100" align="center" class="" style="word-break:break-all" ><? echo $width_dia_types;?></td>
											
											
											<td width="80" align="center" class=""><? echo $color_library[$color_id];?></td>
											
											<td width="80"  align="right" title="<? echo $batch_id;?>"><? echo number_format($row[("batch_qty")],0);?></td>
											<td width="80" align="center" class=""><? echo change_date_format($row[("batch_date")]);?></td>
											<td width="80" align="center" class="">
												<?
												echo $row[("batch_no")];
												
												?>
											</td>
											<td width="80" align="center" class="10"><? echo $row[("extention_no")];// if($heatset_end_date!='') echo floor($batchtoheat_time_diff/60).":".$batchtoheat_time_diff%60;
											//else echo " "; ?> </td>
                                            <td width="80" class="" align="center">
                                            
                                            <? 
											$recipe_idArr=array_unique(explode(",",$recipe_id));
											
											$recipe_button='';
											foreach($recipe_idArr as $rId)
											{
											$recipe_button.="<a href='#' onClick=\"fn_recipe_calc('".$rId."','".$batch_id."','".$yarn_lots."','".$brand_name."',1)\"> ".$rId." <a/>".',';
												
											 }
											 echo rtrim($recipe_button,',');
											 ?>
                                              </td>
											<td width="80" class="" align="center">
												 <a href="##" onClick="fn_recipe_calc('<? echo $requ_ids;?>','<? echo $batch_id;?>','<? echo $yarn_lots;?>','<? echo $brand_name;?>',2)"><?  $requ_nosArr=explode("-",$requ_nos);echo ltrim($requ_nosArr[3],'0');?> </a> <? //echo $requ_nos;?>
											</td>
                                            
											 
                                        
											<td width="80" align="center"  class=""><? echo $machine_name_arr[$sliting_mc_id];?> </td>
											<td width="80" class="" align="right"><?  echo $sliting_prod_qty;?> </td>
											<td width="80" align="center" class="" title="<? echo $sliting_start_date;?>" >
												<? echo change_date_format($sliting_start_date).'<br>'.$sliting_start_time;?>
											</td>
											<td width="80" class="" align="center">
												<?
												echo change_date_format($sliting_end_date).'<br>'.$sliting_endtime.'<br>'.$sliting_shift_name;
												?>
											</td>
											<td width="80" class="" align="center"><? //if($batch_hr_min!=0) echo floor($batchtodying_time_diff/60).":".$batchtodying_time_diff%60;;
											if($sliting_end_date!=0) 
											{
											echo 'Hr '.floor($sliting_time_used_diff/60).':Min '.$sliting_time_used_diff%60;
											}
											 else echo " "; 
											?></td>
											
											<td width="80" align="center" class="16">
												<? echo $machine_name_arr[$sourcing_mc_id];
												?>
											   
											</td>
											<td width="80" class="" title="" align="right">
												<?
												echo number_format($sourcing_prod_qty,0);
												?>
											</td>
											<td width="80" title="" align="center" class="">
												<?
												echo change_date_format($typewise_sourcing_start_date).'<br>'. $typewise_sourcing_start_time;
												
												// if($sliting_end_date!="")  echo $dyingtosliting_days_remian=datediff('d',$unload_date,$sliting_end_date)-1;//
												?>
											</td>
											<td width="80" class="" align="center" title="">
												<?
												 echo change_date_format($typewise_sourcing_end_date).'<br>'.$typewise_sourcing_endtime.'<br>'.$sourcing_shift_name;
												?> 
											</td>
											<td width="80" align="center" class="20"><?  
											if($typewise_sourcing_end_date!=0) 
											{
											echo 'Hr '.floor($sourcing_time_used_diff/60).':Min '.$sourcing_time_used_diff%60;
											}
											 else echo " ";?></td>
											
											<td width="80" class=""><? echo $machine_name_arr[$stenter_mc_id];?></td>
											<td width="80" class="" align="right"><? 
														echo  number_format($stenter_prod_qty,0);
											 ?></td>
											<td width="80" align="center" class=""><? echo change_date_format($stenter_start_date).'<br>'. $stenter_start_time;;?></td>
											<td width="80" align="center" class=""><?  echo change_date_format($stenter_end_date).'<br>'. $stenter_endtime.'<br>'.$stenter_shift_name;?></td>
											<td width="80" align="center" class=""><?    
											if($stenter_end_date!=0) 
											{
											echo 'Hr '.floor($stenter_time_used_diff/60).':Min '.$stenter_time_used_diff%60;
											}
											 else echo " ";;?></td>
											<td width="80"  align="center" title="Unload MC"><? echo $machine_name_arr[$unload_machine_id]; ?></td>
											
											<td width="80"  align="right" class=""><? echo $machine_capacity_arr[$unload_machine_id];?></td>
											<td width="80" class="" align="right"><? echo number_format($load_prod_qty,0);?></td>
											<td width="100" align="center" class=""><? //echo change_date_format($load_date).'<br>'.$load_time.'<br>'.$load_shift_name;?>
												<table width="100%" border="0"  align="center" class="rpt_table" rules="all">
											<? 
											// load_dateData
											foreach($load_dateData as $load_date)
											{
											?>	<tr>
											<?
												$load_time=$load_time_arr2[$batch_id][$load_date]["end_time"];
												$load_shift_name=$load_time_arr2[$batch_id][$load_date]["load_shift_name"];
												?>
												<td>
												<?
												$load_datetime=change_date_format($load_date).','.$load_time;
												 echo $load_datetime;
												 ?>
												 </td>
												 </tr>
												 <tr>
												<td>
												<?
												 echo $load_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
											}
											?>
										   </table>
											</td>
											<td width="100" class="30" align="center">
											<table width="100%" border="0"  class="rpt_table" rules="all">
											<? 
											foreach($unload_dateData as $unload_date)
											{
											?>	<tr>
											<?
												$unload_time=$load_unload_time_arr2[$batch_id][$unload_date]["hr_min"];
												$un_shift_name=$load_unload_time_arr2[$batch_id][$unload_date]["shift_name"];
												?>
												<td>
												<?
												$unload_datetime=change_date_format($unload_date).','.$unload_time;
												 echo $unload_datetime;
												 ?>
												 </td>
												 </tr>
												 <tr>
												<td>
												<?
												 echo $un_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
											}
											
											?>
										   </table>
											</td>
											<td width="80" class="" align="center"><? 
											foreach($unload_dateData as $unload_date)
											{
												$unload_time=$load_unload_time_arr2[$batch_id][$unload_date]["hr_min"];
												$unload_date_time=(change_date_format($unload_date).' '.$unload_time.':'.'00');
												//echo $batch_date_time.'='.$unload_date_time2;
												$dying_time_used_hr=0;$dying_time_used_hr=0;
												foreach($load_dateData as $load_date)
												{
													$load_time=$load_time_arr2[$batch_id][$load_date]["end_time"];
													$load_date_time=(change_date_format($load_date).' '.$load_time.':'.'00');
													$dying_time_used_diff=datediff(n,$load_date_time ,$unload_date_time);
													$dying_time_used_hr+=floor($dying_time_used_diff/60);
													$dying_time_used_min+=$dying_time_used_diff%60;
													
												}
											}
											if(count($unload_dateData)>0) 
											{
												$dyingused_time_diff=datediff(n,$load_datetime ,$unload_datetime);
												echo floor($dyingused_time_diff/60).":".$dyingused_time_diff%60;
										//echo 'Hr '.$dying_time_used_hr.':Min '.$dying_time_used_min;
											}
											 else echo " ";?></td>
											<td width="80" class="" align="center"><?  echo $unload_ltb_btb;?></td>
											<td width="80" class="" align="center"><?    echo	$resultName;?></td>
											
											<td width="80" class="" align="right"><? echo number_format($rotaion_prod_qty,0);?></td>
											<td width="80" class="" align="center"><?  
											$rotaion_start_date=rtrim($row[("rotaion_start_date")],',');
											$rotaion_start_dateArr=array_unique(explode(",",$rotaion_start_date));
											?>
											<table width="100%" border="0" align="center" class="rpt_table" rules="all">
											<? 
											// load_dateData
											foreach($rotaion_start_dateArr as $rotaion_startdate)
											{
											?>	<tr>
											<?
												$rot_start_time=$sub_batch_wise_rotaion_arr[$batch_id][$rotaion_startdate]["rotaion_starttime"];
												$start_rotation_shift_name=$sub_batch_wise_rotaion_arr[$batch_id][$rotaion_startdate]["rotation_shift_name"];
												//$unload_time=$batch_wise_rotaion_arr[$batch_id][$row[csf("end_date")]]["rotaion_endtime"];
												
												?>
												<td>
												<?
												$rot_start_datetime=change_date_format($rotaion_startdate).','.$rot_start_time;
												 echo $rot_start_datetime;
												 ?>
												 </td>
												 </tr>
												 <tr>
												<td>
												<?
												//$unload_datetime=change_date_format($unload_date).','.$unload_time.','.$un_shift_name;
												 echo $start_rotation_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
											}
											
											?>
										   </table>
											</td>
											<td width="80" align="center" class=""><?  
											$rotaion_end_date=rtrim($row[("rotaion_end_date")],',');
											$rotaion_end_dateArr=array_unique(explode(",",$rotaion_end_date));
											?>
											<table width="100%" border="0" align="center"  class="rpt_table" rules="all">
											<? 
											// load_dateData
											foreach($rotaion_end_dateArr as $rotaion_enddate)
											{
											?>	<tr>
											<?
												$rot_end_time=$sub_batch_wise_rotaion_arr[$batch_id][$rotaion_enddate]["rotaion_endtime"];
												$end_rotation_shift_name=$sub_batch_wise_rotaion_arr[$batch_id][$rotaion_enddate]["rotation_shift_name"];
												?>
												<td align="center">
												<?
												$rot_end_datetime=change_date_format($rotaion_enddate).','.$rot_end_time;
												 echo $rot_end_datetime;
												 ?>
												 </td>
												 </tr>
												 <tr>
												<td align="center">
												<?
												 echo $end_rotation_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
												 $rotaion_endtime=$sub_batch_wise_rotaion_arr[$batch_id][$rotaion_enddate]["rotaion_endtime"];
												$rotaion_end_datetime=(change_date_format($rotaion_enddate).' '.$rotaion_endtime.':'.'00');
												$rotation_time_used_hr=$rotation_time_used_min=0;
												 foreach($rotaion_start_dateArr as $rotaion_startdate)
													{
														$rotaion_starttime=$sub_batch_wise_rotaion_arr[$batch_id][$rotaion_startdate]["rotaion_starttime"];
														$rotaion_start_datetime=(change_date_format($rotaion_startdate).' '.$rotaion_starttime.':'.'00');
														
														$rotation_time_used_diff=datediff(n,$rotaion_start_datetime ,$rotaion_end_datetime);
														$rotation_time_used_hr+=floor($rotation_time_used_diff/60);
														$rotation_time_used_min+=$rotation_time_used_diff%60;
													}
											}
											
											?>
										   </table>
                                           </td>
											<td width="80" align="center" class=""><?   
											if(count($rotaion_end_dateArr)>0) 
											{ echo 'Hr '.$rotation_time_used_hr.':Min '.$rotation_time_used_min;}
											 else echo " ";?></td>
										<td width="80" class="" align="center"><?   
											if(count($rotaion_end_dateArr)>0) 
											{ echo 'Hr '.$rotation_time_used_hr.':Min '.$rotation_time_used_min;}
											 else echo " ";?></td>
											<td width="80" class=""><? echo $machine_name_arr[$row['wash_mc_id']];?> </td>
											
											<td width="80" class="" align="right"><? echo number_format($row['wash_prod_qty'],0);?></td>						

											<td width="80" class="" align="center">
											
											
											
											<?  
											$wash_start_date=rtrim($row[("wash_start_date")],',');
											$wash_start_dateArr=array_unique(explode(",",$wash_start_date));
											?>
											<table width="100%" border="0" align="center" class="rpt_table" rules="all">
											<? 
											foreach($wash_start_dateArr as $wash_startdate)
											{
											?>	<tr>
											<?
												$rot_start_time=$batch_wise_wash_arr[$batch_id][$wash_startdate]["wash_starttime"];
												$start_wash_shift_name=$batch_wise_wash_arr[$batch_id][$wash_startdate]["wash_shift_name"];
												?>
												<td>
												<?
												$rot_start_datetime=change_date_format($wash_startdate).','.$rot_start_time;
												 echo $rot_start_datetime;
												 ?>
												 </td>
												 </tr>
												 <tr>
												<td align="center">
												<?
												 echo $start_wash_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
											}
											
											?>
										   </table>
											
											</td>
											<td width="80" class="" align="center"><?  
											$wash_end_date=rtrim($row[("wash_end_date")],',');
											$wash_end_dateArr=array_unique(explode(",",$wash_end_date));
											?>
											<table width="100%" border="0" align="center"  class="rpt_table" rules="all">
											<? 
											// load_dateData
											foreach($wash_end_dateArr as $wash_enddate)
											{
											?>	<tr>
											<?
												$rot_end_time=$batch_wise_wash_arr[$batch_id][$wash_enddate]["wash_endtime"];
												$end_wash_shift_name=$batch_wise_wash_arr[$batch_id][$wash_enddate]["shift_name"];
												?>
												<td>
												<?
												$rot_end_datetime=change_date_format($wash_enddate).','.$rot_end_time;
												 echo $rot_end_datetime;
												 ?>
												 </td>
												 </tr>
												 <tr>
												<td align="center">
												<?
												 echo $end_wash_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
												 $wash_endtime=$batch_wise_wash_arr[$batch_id][$wash_enddate]["wash_endtime"];
												$wash_end_datetime=(change_date_format($wash_enddate).' '.$wash_endtime.':'.'00');
												$wash_time_used_hr=$wash_time_used_min=0;
												 foreach($wash_start_dateArr as $wash_startdate)
													{
														$wash_starttime=$batch_wise_wash_arr[$batch_id][$wash_startdate]["wash_starttime"];
														$wash_start_datetime=(change_date_format($wash_startdate).' '.$wash_starttime.':'.'00');
														
														$wash_time_used_diff=datediff(n,$wash_start_datetime ,$wash_end_datetime);
														$wash_time_used_hr+=floor($wash_time_used_diff/60);
														$wash_time_used_min+=$wash_time_used_diff%60;
													}
											}
											
											?>
										   </table>
                                           </td>
											<td width="80" class="" align="center"><?   
											if(count($wash_end_dateArr)>0) 
											{ echo 'Hr '.$wash_time_used_hr.':Min '.$wash_time_used_min;}
											 else echo " ";?></td>
											<td width="80" class="" align="center"> <? echo  $machine_name_arr[$row[("heat_mc_id")]];?></td>
											<td width="80" class="" align="right"><? echo number_format($row[("heatset_prod_qty")],0);;?></td>
											<td width="80" class="" align="center"><?  
											$heatset_start_date=rtrim($row[("heatset_start_date")],',');
											$heatset_start_dateArr=array_unique(explode(",",$heatset_start_date));
											?>
											<table width="100%" border="0" align="center" class="rpt_table" rules="all">
											<? 
											// load_dateData
											foreach($heatset_start_dateArr as $heat_startdate)
											{
											?>	<tr>
											<?
												$heat_start_time=$sub_batch_wise_heat_arr[$batch_id][$heat_startdate]["heatset_starttime"];
												$heat_shift_name=$sub_batch_wise_heat_arr[$batch_id][$heat_startdate]["heat_shift_name"];
												?>
												<td>
												<?
												$start_datetime=change_date_format($heat_startdate).','.$heat_start_time;
												 echo $start_datetime;
												 ?>
												 </td>
												 </tr>
												 <tr>
												<td align="center">
												<?
												 echo $heat_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
												
											}
											
											?>
										   </table>
                                           </td>
											<td width="80" class="" align="center"> <?  
											$heatset_end_date=rtrim($row[("heatset_end_date")],',');
											$heatset_end_dateArr=array_unique(explode(",",$heatset_end_date));
											?>
											<table width="100%" align="center" border="0"  class="rpt_table" rules="all">
											<? 
											// load_dateData
											foreach($heatset_end_dateArr as $heat_enddate)
											{
											?>	<tr>
											<?
												$heat_end_time=$sub_batch_wise_heat_arr[$batch_id][$heat_enddate]["heatset_endtime"];
												$end_heat_shift_name=$sub_batch_wise_heat_arr[$batch_id][$heat_enddate]["end_heat_shift_name"];
												?>
												<td>
												<?
												$end_datetime=change_date_format($heat_enddate).','.$heat_end_time;
												 echo $end_datetime;
												 ?>
												 </td>
												 </tr>
												 <tr>
												<td>
												<?
												 echo $end_heat_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
												  $heat_endtime=$sub_batch_wise_heat_arr[$batch_id][$heat_enddate]["heatset_endtime"];
												$heat_end_datetime=(change_date_format($heat_enddate).' '.$heat_endtime.':'.'00');
												$heat_time_used_hr=$heat_time_used_min=0;
												 foreach($heatset_start_dateArr as $heat_startdate)
													{
														$heatset_starttime=$sub_batch_wise_heat_arr[$batch_id][$heat_startdate]["heatset_starttime"];
														$heat_start_datetime=(change_date_format($heat_startdate).' '.$heatset_starttime.':'.'00');
														//echo $heat_start_datetime.'='.$heat_end_datetime.'<br>';
														$heat_time_used_diff=datediff(n,$heat_start_datetime ,$heat_end_datetime);
														$heat_time_used_hr+=floor($heat_time_used_diff/60);
														$heat_time_used_min+=$heat_time_used_diff%60;
													}
												
											}
											
											?>
										   </table>
                                           </td>
											<td width="80" class="" align="center"><?   
											if(count($heatset_start_dateArr)>0) 
											{ echo 'Hr '.$heat_time_used_hr.':Min '.$heat_time_used_min;}
											 else echo " ";?></td>
											
											<td width="80" class="" align="center"><? echo $machine_name_arr[$row['dry_mc_id']];?></td>
											<td width="80" class="" align="right"> <? echo number_format($row['dry_prod_qty'],0)  ;?></td>
											<td width="80" class="50" align="center">
											<?  
											$dry_start_date=rtrim($row[("dry_start_date")],',');
											$dry_start_dateArr=array_unique(explode(",",$dry_start_date));
											?>
											<table width="100%" border="0"  align="center" class="rpt_table" rules="all">
											<? 
											// load_dateData
											foreach($dry_start_dateArr as $dry_startdate)
											{
											?>	<tr>
											<?
												$dry_start_time=$sub_batch_wise_dry_arr[$batch_id][$dry_startdate]["dry_starttime"];
												$start_dry_shift_name=$sub_batch_wise_dry_arr[$batch_id][$dry_startdate]["start_shift_name"];
												?>
												<td align="center">
												<?
												$dry_start_datetime=change_date_format($dry_startdate).','.$dry_start_time;
												 echo $dry_start_datetime;
												 ?>
												 </td>
												 </tr>
												 <tr>
												<td align="center">
												<?
												 echo $start_dry_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
												
											}
											
											?>
										   </table>
                                            </td>
											<td width="80" class="" align="center">
											<?  
											$dry_end_date=rtrim($row[("dry_end_date")],',');
											$dry_end_dateArr=array_unique(explode(",",$dry_end_date));
											?>
											<table width="100%" border="0" align="center"  class="rpt_table" rules="all">
											<? 
											// load_dateData
											foreach($dry_end_dateArr as $dry_enddate)
											{
											?>	<tr>
											<?
												$dry_end_time=$sub_batch_wise_dry_arr[$batch_id][$dry_enddate]["dry_endtime"];
												$dry_end_shift_name=$sub_batch_wise_dry_arr[$batch_id][$dry_enddate]["end_shift_name"];
												?>
												<td align="center">
												<?
												$dry_end_datetime=change_date_format($dry_enddate).','.$dry_end_time;
												 echo $dry_end_datetime;
												 ?>
												 </td>
												 </tr>
												 <tr>
												<td align="center">
												<?
												 echo $dry_end_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
												 //$dry_endtime=$batch_wise_dry_arr[$batch_id][$dry_enddate]["dry_starttime"];
												$dry_end_datetime=(change_date_format($dry_enddate).' '.$dry_end_time.':'.'00');
												$dry_time_used_hr=$dry_time_used_min=0;
												 foreach($dry_start_dateArr as $dry_startdate)
													{
														$dry_starttime=$sub_batch_wise_dry_arr[$batch_id][$dry_startdate]["dry_starttime"];
														$dry_start_datetime=(change_date_format($dry_startdate).' '.$dry_starttime.':'.'00');
														//echo $dry_start_datetime.'='.$dry_end_datetime.'<br>';
														$dry_time_used_diff=datediff(n,$dry_start_datetime ,$dry_end_datetime);
														$dry_time_used_hr+=floor($dry_time_used_diff/60);
														$dry_time_used_min+=$dry_time_used_diff%60;
													}
											}
											
											?>
										   </table>
                                            </td>
											<td width="80" class="" align="center"><?   
											if(count($dry_start_dateArr)>0) 
											{ echo 'Hr '.$dry_time_used_hr.':Min '.$dry_time_used_min;}
											 else echo " "; ?></td>
											<td width="80" class="" align="center"><? echo $machine_name_arr[$row['comp_mc_id']]; //comp_start_date ?></td>
											
											<td width="80" class="" align="right"><? echo  number_format($row["comp_prod_qty"],0);?></td>
											
											
											<td width="80" class="" align="center"><?  
											$comp_start_date=rtrim($row[("comp_start_date")],',');
											$comp_start_dateArr=array_unique(explode(",",$comp_start_date));
											?>
											<table width="100%" border="0"  align="center" class="rpt_table" rules="all">
											<? 
											// load_dateData
											foreach($comp_start_dateArr as $comp_startdate)
											{
											?>	<tr>
											<?
												$comp_start_time=$sub_batch_wise_comp_arr[$batch_id][$comp_startdate]["comp_starttime"];
												$start_comp_shift_name=$sub_batch_wise_comp_arr[$batch_id][$comp_startdate]["start_shift_name"];
												?>
												<td>
												<?
												$comp_start_datetime=change_date_format($comp_startdate).','.$comp_start_time;
												 echo $comp_start_datetime;
												 ?>
												 </td>
												 </tr>
												 <tr>
												<td>
												<?
												 echo $start_comp_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
												
											}
											
											?>
										   </table>
                                           </td>
											<td width="80" class="" align="center">
                                            	<?  
											$comp_end_date=rtrim($row[("comp_end_date")],',');
											$comp_end_dateArr=array_unique(explode(",",$comp_end_date));
											?>
											<table width="100%" border="0"  align="center" class="rpt_table" rules="all">
											<? 
											// load_dateData
											foreach($comp_end_dateArr as $comp_enddate)
											{
											?>	<tr>
											<?
												$comp_endtime=$sub_batch_wise_comp_arr[$batch_id][$comp_enddate]["comp_endtime"];
												$comp_end_shift_name=$sub_batch_wise_comp_arr[$batch_id][$comp_enddate]["end_shift_name"];
												?>
												<td>
												<?
												$comp_end_datetime=change_date_format($comp_enddate).','.$comp_endtime;
												 echo $comp_end_datetime;
												 ?>
												 </td>
												 </tr>
												 <tr>
												<td>
												<?
												 echo $comp_end_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
												$comp_end_datetime_cal=(change_date_format($comp_enddate).' '.$comp_endtime.':'.'00');
												$comp_time_used_hr=$comp_time_used_min=0;
												 foreach($comp_start_dateArr as $com_startdate)
													{
														$comp_starttime=$sub_batch_wise_comp_arr[$batch_id][$com_startdate]["comp_starttime"];
														$comp_start_datetime_cal=(change_date_format($com_startdate).' '.$comp_starttime.':'.'00');
														//echo $dry_start_datetime.'='.$dry_end_datetime.'<br>';
														$comp_time_used_diff=datediff(n,$comp_start_datetime_cal ,$comp_end_datetime_cal);
														$comp_time_used_hr+=floor($comp_time_used_diff/60);
														$comp_time_used_min+=$comp_time_used_diff%60;
													}
											}
											
											?>
										   </table>
                                            </td>
											<td width="80" class="" align="center"> <?   
											if(count($comp_start_dateArr)>0) 
											{ echo 'Hr '.$comp_time_used_hr.':Min '.$comp_time_used_min;}
											 else echo " "; ?></td>
											<td width="80" class="" align="center"><? echo $machine_name_arr[$row['singeing_mc_id']];  ;?></td>
											<td width="80" class="" align="right"><? echo number_format($row["singeing_prod_qty"],0);?></td>
											<td width="80" class="60"><?  
											$singeing_start_date=rtrim($row[("singeing_start_date")],',');
											$singeing_start_dateArr=array_unique(explode(",",$singeing_start_date));
											?>
											<table width="100%" border="0" align="center"  class="rpt_table" rules="all">
											<? 
											// load_dateData
											foreach($singeing_start_dateArr as $singeing_startdate)
											{
											?>	<tr>
											<?
												$singe_start_time=$sub_batch_wise_singeing_arr[$batch_id][$singeing_startdate]["singeing_starttime"];
												
												$start_singe_shift_name=$sub_batch_wise_singeing_arr[$batch_id][$singeing_startdate]["start_shift_name"];
												?>
												<td align="center">
												<?
												$singe_start_datetime=change_date_format($singeing_startdate).','.$singe_start_time;
												 echo $singe_start_datetime;
												 ?>
												 </td>
												 </tr>
												 <tr>
												<td>
												<?
												 echo $start_singe_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
												
											}
											
											?>
										   </table>
                                           </td>
											<td width="80" class="" align="center"> <?  
											$singeing_end_date=rtrim($row[("singeing_end_date")],',');
											$singeing_end_dateArr=array_unique(explode(",",$singeing_end_date));
											?>
											<table width="100%" border="0"  align="center" class="rpt_table" rules="all">
											<? 
											// load_dateData
											foreach($singeing_end_dateArr as $singe_enddate)
											{
											?>	<tr>
											<?
												$singeing_endtime=$sub_batch_wise_singeing_arr[$batch_id][$singe_enddate]["singeing_endtime"];
												$singe_end_shift_name=$sub_batch_wise_singeing_arr[$batch_id][$singe_enddate]["end_shift_name"];
												?>
												<td>
												<?
												$singe_end_datetime=change_date_format($singe_enddate).','.$singeing_endtime;
												 echo $singe_end_datetime;
												 ?>
												 </td>
												 </tr>
												 <tr>
												<td>
												<?
												 echo $singe_end_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
												$singeing_end_datetime_cal=(change_date_format($singe_enddate).' '.$singeing_endtime.':'.'00');
												$singe_time_used_hr=$singe_time_used_min=0;
												 foreach($singeing_start_dateArr as $singe_startdate)
													{
														$singe_starttime=$sub_batch_wise_singeing_arr[$batch_id][$singe_startdate]["singeing_starttime"];
														$singe_start_datetime_cal=(change_date_format($singe_startdate).' '.$singe_starttime.':'.'00');
														//echo $dry_start_datetime.'='.$dry_end_datetime.'<br>';
														$singe_time_used_diff=datediff(n,$singe_start_datetime_cal ,$singeing_end_datetime_cal);
														$singe_time_used_hr+=floor($singe_time_used_diff/60);
														$singe_time_used_min+=$singe_time_used_diff%60;
													}
											}
											
											?>
										   </table></td>
											<td width="80" class="" align="center"> <?   
											if(count($singeing_start_dateArr)>0) 
											{ echo 'Hr '.$singe_time_used_hr.':Min '.$singe_time_used_min;}
											 else echo " "; ?></td>
                                             
                                             
                                             <td width="80" class="" align="center"><? echo $machine_name_arr[$row['brush_mc_id']];  ;?></td>
											<td width="80" class="" align="right"><? echo number_format($row["brush_prod_qty"],0);?></td>
											<td width="80" class="60" align="center"><?  
											$brush_start_date=rtrim($row[("brush_start_date")],',');
											$brush_start_dateArr=array_unique(explode(",",$brush_start_date));
											?>
											<table width="100%" border="0" align="center"  class="rpt_table" rules="all">
											<? 
											// load_dateData
											foreach($brush_start_dateArr as $brush_startdate)
											{
											?>	<tr>
											<?
												$brush_start_time=$sub_batch_wise_brush_arr[$batch_id][$brush_startdate]["brush_starttime"];
												
												$start_brush_shift_name=$sub_batch_wise_brush_arr[$batch_id][$brush_startdate]["start_shift_name"];
												?>
												<td>
												<?
												$brush_start_datetime=change_date_format($brush_startdate).','.$brush_start_time;
												 echo $brush_start_datetime;
												 ?>
												 </td>
												 </tr>
												 <tr>
												<td>
												<?
												 echo $start_brush_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
												
											}
											
											?>
										   </table>
                                           </td>
											<td width="80" class="" align="center"> <?  
											$brush_end_date=rtrim($row[("brush_end_date")],',');
											$brush_end_dateArr=array_unique(explode(",",$singeing_end_date));
											?>
											<table width="100%" border="0" align="center" class="rpt_table" rules="all">
											<? 
											// load_dateData
											foreach($brush_end_dateArr as $brush_enddate)
											{
											?>	<tr>
											<?
												$brush_endtime=$sub_batch_wise_brush_arr[$batch_id][$brush_enddate]["brush_endtime"];
												$brush_end_shift_name=$sub_batch_wise_brush_arr[$batch_id][$brush_enddate]["end_shift_name"];
												?>
												<td>
												<?
												$brush_end_datetime=change_date_format($brush_enddate).','.$brush_endtime;
												 echo $brush_end_datetime;
												 ?>
												 </td>
												 </tr>
												 <tr>
												<td>
												<?
												 echo $brush_end_shift_name;
												 ?>
												 </td>
												 </tr>
												 <?
												$brush_end_datetime_cal=(change_date_format($brush_enddate).' '.$brush_endtime.':'.'00');
												$brush_time_used_hr=$brush_time_used_min=0;
												 foreach($brush_start_dateArr as $brush_startdate)
													{
														$brush_starttime=$sub_batch_wise_singeing_arr[$batch_id][$brush_startdate]["singeing_starttime"];
														$brush_start_datetime_cal=(change_date_format($brush_startdate).' '.$brush_starttime.':'.'00');
														//echo $dry_start_datetime.'='.$dry_end_datetime.'<br>';
														$brush_time_used_diff=datediff(n,$brush_start_datetime_cal ,$brush_end_datetime_cal);
														$brush_time_used_hr+=floor($brush_time_used_diff/60);
														$brush_time_used_min+=$brush_time_used_diff%60;
													}
											}
											
											?>
										   </table></td>
											<td width="80" class="" align="center"> <?   
											if(count($brush_start_dateArr)>0) 
											{ echo 'Hr '.$brush_time_used_hr.':Min '.$brush_time_used_min;}
											 else echo " "; ?>
                                             </td>
                                             
											
											<td width="80" class="" align="right"><?  $fin_fab_qty=$fin_prod_qc_qnty_kg+$fin_prod_qc_qnty_yds;
											echo  number_format($fin_fab_qty,0);?></td>
											
											<td width="80" class="" align="center"><? echo change_date_format($sub_fin_fab_arr[$batch_id]["receive_date"]);?></td>
											
											<td width="80" class="" align="right"><? $delivery_qty=$sub_fin_deli_qnty_kg+$fin_deli_qnty_yds;echo number_format($delivery_qty,0);?></td>
                                            <td width="" class="67" align="center"><? echo change_date_format($fin_deli_date);?></td>
											
											
											
										</tr>
										<?
										$p++;
										$tot_batch_qty+= $row["batch_qty"];
										$tot_fin_prod_qc_qnty_kg+= $fin_fab_qty;
										$tot_sliting_prod_qty+= $sliting_prod_qty;
										$tot_fin_deli_qnty_kg+= $delivery_qty;
										$tot_stenter_prod_qty+= $stenter_prod_qty;
										$tot_sourcing_prod_qty+= $sourcing_prod_qty;
										$tot_load_prod_qty+= $load_prod_qty;
										$tot_rotaion_prod_qty+= $rotaion_prod_qty;
										$tot_heatset_prod_qty+= $row[("heatset_prod_qty")];
										$tot_drying_prod_qty+= $row[("dry_prod_qty")];
										$tot_comp_prod_qty+= $row[("comp_prod_qty")]; 
										$tot_singe_prod_qty+= $row[("singeing_prod_qty")];
										$tot_brush_prod_qty+= $row[("brush_prod_qty")];
								
							}
				
						?>
						</tbody>
			   
					</table>
                  <table width="<? echo $width_td; ?>" border="1" cellpadding="2" cellspacing="0" class="tbl_bottom" rules="all">
                    <tr>
                    <td align="right" width="20"><? //echo number_format($tot_fin_deli_qnty_yds,2);?> </td>
                    <td align="right" width="100"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="100">&nbsp; </td>
                    <td align="right" width="100"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="100">&nbsp; </td>
                    <td  width="80" align="left">Total</td>
                    <td width="80"><? echo number_format($tot_batch_qty,0);?></td>
                  
                    <td align="right" width="80"> </td>
                    <td  width="80">&nbsp; </td>
                    <td align="right" width="80" title="10"><? //echo number_format($tot_fin_prod_qc_qnty_kg,2);?> </td>
                    <td  width="80">&nbsp; </td>
                    <td  width="80">&nbsp; </td>
                   
                   
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_kg,2);?> </td>
                    <td align="right" width="80"  title="22"><? echo number_format($tot_sliting_prod_qty,0);?> </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? echo number_format($tot_sourcing_prod_qty,0);?>  </td>
                    <td align="right" width="80"></td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? echo number_format($tot_stenter_prod_qty,0);?> </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="80" align="right">  </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,2);?> </td>
                    <td align="right" width="80"><? echo number_format($tot_load_prod_qty,0);?> </td>
                    <td width="100">&nbsp; </td>
                    <td align="right" width="100"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,2);?> </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    
                    <td align="right" width="80"><? echo number_format($tot_rotaion_prod_qty,0);?> </td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,2);?> </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,2);?> </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="80" align="right"><? //echo number_format($tot_heatset_prod_qty,0);?></td>
                    <td align="right" width="80"><? echo number_format($tot_heatset_prod_qty,0);?> </td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,2);?> </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? echo number_format($tot_drying_prod_qty,0);?> </td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,2);?> </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? echo number_format($tot_comp_prod_qty,0);?> </td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,2);?> </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    <td width="80" align="right"><? echo number_format($tot_singe_prod_qty,0);?></td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,2);?> </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    
                     <td width="80">&nbsp; </td>
                    <td align="right" width="80"><? echo number_format($tot_brush_prod_qty,0);?> </td>
                    <td width="80" align="right"><? //echo number_format($tot_singe_prod_qty,0);?></td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,2);?> </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    
                    
                    
                    <td width="80"><? echo number_format($tot_fin_prod_qc_qnty_kg,0);?> </td>
                    <td align="right" width="80"><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                   
                    <td align="right" width="80"><? echo number_format($tot_fin_deli_qnty_kg,2);?> </td>
                   
                    <td align="right" width=""><? //echo number_format($tot_fin_deli_qnty_yds,0);?> </td>
                    
                   
                    <tr/>
				</table>
					</div>
			</div>
            </div>
				<?
			 
		 
		  
		  
}
		foreach (glob("*.xls") as $filename)
		{
			if( @filemtime($filename) < (time()-$seconds_old) )
				@unlink($filename);
		}
	//---------end------------//
		$filename=time().".xls";
		$create_new_doc = fopen($filename, 'w');
		$fdata=ob_get_contents();
		fwrite($create_new_doc,$fdata);
		ob_end_clean();
		echo "$fdata****$filename****$type";
		exit();
	}
	
	
if($action=="report_generate2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$all_condition="";

	$rpt_type 			= str_replace("'","",$type);
	$cbo_type 			= str_replace("'","",$cbo_type);
	$cbo_company_name 	= str_replace("'","",$cbo_company_name);
	$cbo_location_id 	= str_replace("'","",$cbo_location_id);
	$cbo_floor_id 		= str_replace("'","",$cbo_floor_id);
	$cbo_machine_id 	= str_replace("'","",$cbo_machine_id);
	$cbo_buyer_name 	= str_replace("'","",$cbo_buyer_name);
	$cbo_year 			= str_replace("'","",$cbo_year);
	$fso_id 			= str_replace("'","",$booking_number);
	$cbo_gate_upto 		= str_replace("'","",$cbo_gate_upto);
	$txt_days 			= str_replace("'","",$txt_days);
	$txt_date_from 		= str_replace("'","",$txt_date_from);
	$txt_date_to 		= str_replace("'","",$txt_date_to);
	$batch_number 		= str_replace("'","",$batch_number);
	$batch_number_show 	= str_replace("'","",$batch_number_show);
	$batch_extension 	= str_replace("'","",$batch_extension);
	$fso_no  			= str_replace("'","",$booking_number_show);

	if($cbo_company_name) $comp_cond=" and c.working_company_id in($cbo_company_name)";else $comp_cond="";
	if($cbo_company_name) $comp_cond2=" and b.company_id in($cbo_company_name)";else $comp_cond="";
	//if($cbo_company_name) $comp_cond3=" and b.company_id in($cbo_company_name)";else $comp_cond3="";
	if($cbo_company_name) $prod_comp_cond=" and a.service_company in($cbo_company_name)";else $prod_comp_cond="";
	if($cbo_floor_id) $floor_cond=" and c.floor_id=$cbo_floor_id";else  $floor_cond="";
	if($cbo_floor_id) $floor_cond2=" and a.floor_id=$cbo_floor_id";else $floor_cond2="";
	if($cbo_floor_id) $floor_cond3=" and b.floor_id=$cbo_floor_id";else $floor_cond2="";
	if($cbo_year) $batch_year_cond = " and to_char(c.insert_date,'YYYY')=$cbo_year";
	if($batch_number_show) $batch_cond=" and b.batch_no like '%$batch_number_show'";
	if($batch_extension) $batch_ext_cond=" and b.extention_no='$batch_extension'";
	if($batch_number_show) $batch_cond2=" and c.batch_no like '%$batch_number_show'";
	if($batch_number_show) $batch_cond3=" and a.batch_no like '%$batch_number_show'";
	if($batch_extension) $batch_ext_cond2=" and c.extention_no='$batch_extension'";
	//if($fso_no) $sales_cond=" and d.job_no like '%$fso_no'";

	//if($cbo_buyer_name>0) $buyer_cond=" and d.po_buyer=$cbo_buyer_name";
	if($cbo_buyer_name>0) $buyer_cond2=" and d.buyer_id=$cbo_buyer_name";

	$batch_id_arr = array();
	if($txt_date_from && $txt_date_to)
	{
		if($db_type==0)
		{
			$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
			$batch_date_cond="and  b.batch_date >= '$date_from' AND b.batch_date<='$date_to'";
			$batch_date_cond2="and  c.batch_date >= '$date_from' AND c.batch_date<='$date_to'";
			$prod_date_cond="and  a.production_date >= '$date_from' AND a.production_date<='$date_to'";
		}
		if($db_type==2)
		{
			$date_from=change_date_format($txt_date_from,'','',1);
			$date_to=change_date_format($txt_date_to,'','',1);
			$batch_date_cond=" and  b.batch_date >= '$date_from' AND b.batch_date<='$date_to'";
			$batch_date_cond2=" and  c.batch_date >= '$date_from' AND c.batch_date<='$date_to'";
			$prod_date_cond=" and  a.production_date >= '$date_from' AND a.production_date<='$date_to'";
		}

		$get_batch_ids_by_batch_date=sql_select("select entry_form, b.id,b.process_id from pro_batch_create_mst b where b.status_active=1 and b.entry_form=36 $batch_cond $batch_date_cond $floor_cond3");
		//echo "select entry_form, b.id,b.process_id from pro_batch_create_mst b where b.status_active=1 and b.entry_form=36 $batch_cond $batch_date_cond $floor_cond3";
		//echo "select b.id,b.process_id from pro_batch_create_mst b where b.status_active=1 $batch_cond $batch_date_cond $floor_cond3";

		foreach($get_batch_ids_by_batch_date as $row)
		{
			if($row[csf("entry_form")]!=36)
			{
				$batch_process = explode(",",$row[csf("process_id")]);
				if(in_array(33, $batch_process)){
					$production_batch_arr[$row[csf("id")]] = $row[csf("id")];
				}
			}
			else
			{
				$sub_batch_process = explode(",",$row[csf("process_id")]);
				if(in_array(33, $sub_batch_process)){
					$sub_production_batch_arr[$row[csf("id")]] = $row[csf("id")];
				}
			}
		}
		//print_r($sub_production_batch_arr);

		$get_batch_ids_from_subprocess = sql_select("select a.batch_id from pro_fab_subprocess a where a.status_active=1 and a.entry_form=38 and a.load_unload_id=2 $prod_date_cond $floor_cond2 $batch_cond3 group by a.batch_id");
		//echo "select a.batch_id from pro_fab_subprocess a where a.status_active=1 and a.entry_form=38 and a.load_unload_id=2 $prod_date_cond $floor_cond2 $batch_cond3 group by a.batch_id";

		foreach($get_batch_ids_from_subprocess as $row)
		{
			$production_batch_arr[$row[csf("batch_id")]] = $row[csf("batch_id")];
		}


		if(empty($production_batch_arr))
		{
			$prod_date_cond="";
		}
		//print_r($production_batch_arr);

		if($db_type==2 && count($production_batch_arr)>1000)
		{
			$production_batch_id_cond=" and (";
			$batIdsArr=array_chunk($production_batch_arr,999);
			foreach($batIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$production_batch_id_cond.=" b.id in($ids) or";
			}
			$production_batch_id_cond=chop($production_batch_id_cond,'or ');
			$production_batch_id_cond.=")";
		}
		else
		{
			$production_batch_id_cond=" and b.id in(".implode(",",$production_batch_arr).")";
		}
		if(empty($production_batch_arr))
		{
			$production_batch_id_cond="";
			$production_batch_id_cond="and b.id=0";
			//echo "<b style='color:red'>No Data Found.</b>";die;
		}

		 $get_batch_ids_from_subprocess_sql = "select a.id,b.id batch_id,b.extention_no
		from pro_batch_create_dtls c,pro_batch_create_mst b
		left join pro_fab_subprocess a on b.id=a.batch_id and a.status_active=1 $prod_comp_cond
		where c.mst_id=b.id and c.status_active=1  $batch_cond $comp_cond2 $batch_ext_cond $production_batch_id_cond order by a.id desc";//group by b.id,b.extention_no,d.within_group,d.id,d.buyer_id,d.po_buyer

		$get_batch_ids_from_subprocess = sql_select($get_batch_ids_from_subprocess_sql);
		$i=0;
		foreach($get_batch_ids_from_subprocess as $row)
		{
			$batch_id_arr[$row[csf("batch_id")]] = $row[csf("batch_id")];

			if($row[csf("within_group")]==1){
				//if(($row[csf("po_buyer")] == $cbo_buyer_name) || ($cbo_buyer_name==0))
					//$fso_id_arr[$row[csf("fso_id")]] = $row[csf("fso_id")];
			}else{
				//if(($row[csf("buyer_id")] == $cbo_buyer_name) || ($cbo_buyer_name==0))
				//	$fso_id_arr[$row[csf("fso_id")]] = $row[csf("fso_id")];
			}

			$batch_sub_id_arr[$row[csf("batch_id")]][] = $row[csf("id")];
			if($row[csf("id")]!="")
				$sub_process_id_arr[$row[csf("id")]]=$row[csf("id")];

			if($i==0){
				$batch_max_process[$row[csf("batch_id")]] = ($row[csf("id")]=="")?0:$row[csf("id")];
			}
			$batch_sub_process_arr[$row[csf("batch_id")]]['entry_form'] = $row[csf("entry_form")];
			$batch_sub_process_arr[$row[csf("batch_id")]]['result'] = $row[csf("result")];
			$batch_id_ext_arr[$row[csf("batch_id")]] = $row[csf("batch_ext_no")];
			$i++;
		}
	} else {
		/*$get_batch_ids_from_subprocess_sql = "select a.id,b.id batch_id,b.extention_no,b.process_id,d.within_group,d.id fso_id,d.buyer_id,d.po_buyer,c.prod_id,c.batch_qnty
		from fabric_sales_order_mst d,pro_batch_create_dtls c,pro_batch_create_mst b
		left join pro_fab_subprocess a on b.id=a.batch_id and a.status_active=1
		where d.id=c.po_id and c.mst_id=b.id and c.status_active=1 and d.status_active=1 $sales_cond $batch_cond $comp_cond2 $batch_ext_cond
		order by a.id desc";*///group by b.id,b.extention_no,b.process_id,d.within_group,d.id,d.buyer_id,d.po_buyer
	$get_batch_ids_from_subprocess_sql = "select a.entry_form,a.result,a.load_unload_id,a.id,b.id batch_id,b.extention_no
		from pro_batch_create_dtls c,pro_batch_create_mst b
		left join pro_fab_subprocess a on b.id=a.batch_id  and a.status_active=1 $prod_comp_cond
		where c.mst_id=b.id  and b.entry_form=36  and c.status_active=1 and b.status_active=1  $batch_cond $batch_ext_cond $sub_buyer_cond2 $production_batch_id_cond order by a.id desc";
		
		$get_batch_ids_from_subprocess = sql_select($get_batch_ids_from_subprocess_sql);
		$i=0;
		foreach($get_batch_ids_from_subprocess as $row)
		{
			$batch_process = explode(",",$row[csf("process_id")]);
			if($row[csf("id")]=="")
			{
				if(in_array(33, $batch_process))
				{
					$batch_id_arr[$row[csf("batch_id")]] = $row[csf("batch_id")];
				}
			}
			else
			{
				$batch_id_arr[$row[csf("batch_id")]] = $row[csf("batch_id")];
				$sub_process_id_arr[$row[csf("id")]]=$row[csf("id")];
			}

			$batch_id_ext_arr[$row[csf("batch_id")]] = $row[csf("batch_ext_no")];

			$batch_wise_qnty[$row[csf("batch_id")]][$row[csf("prod_id")]] += $row[csf("batch_qnty")];

			if($i==0){
				$batch_max_process[$row[csf("batch_id")]] = ($row[csf("id")]=="")?0:$row[csf("id")];
			}
			if($row[csf("id")]!="")
				$sub_process_id_arr[$row[csf("id")]]=$row[csf("id")];

			if($row[csf("within_group")]==1){
				//if(($row[csf("po_buyer")] == $cbo_buyer_name) || ($cbo_buyer_name==0))
					//$fso_id_arr[$row[csf("fso_id")]] = $row[csf("fso_id")];
			}else{
				//if(($row[csf("buyer_id")] == $cbo_buyer_name) || ($cbo_buyer_name==0))
					//$fso_id_arr[$row[csf("fso_id")]] = $row[csf("fso_id")];
			}
			$i++;
		}
	}

	if($db_type==2 && count($batch_id_arr)>1000)
	{
		$batch_id_cond=" and (";
		$batIdsArr=array_chunk($batch_id_arr,999);
		foreach($batIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$batch_id_cond.=" c.id in($ids) or";
		}
		$batch_id_cond=chop($batch_id_cond,'or ');
		$batch_id_cond.=")";
	}
	else
	{
		if(!empty($batch_id_arr))
			$batch_id_cond=" and c.id in(".implode(",",$batch_id_arr).")";
	}
	$batch_date_cond2 = (empty($batch_max_process))?$batch_date_cond2:"";
	$sql_query="select c.id ,c.process_id,c.batch_no,c.batch_date as batch_date,c.color_id,c.color_range_id,c.booking_no,c.company_id as working_company_id,c.sales_order_no,c.sales_order_id,c.id as batch_id,c.extention_no as batch_ext_no,c.remarks,c.floor_id,d.prod_id,d.width_dia_type,d.item_description as fabric_desc,sum(d.batch_qnty) as batch_qty,d.po_id,count(d.id) no_of_roll,d.fin_dia,d.gsm
	from pro_batch_create_dtls d,pro_batch_create_mst c
	where d.mst_id=c.id and c.entry_form=36  and c.status_active=1 and d.status_active=1 $batch_id_cond $batch_cond2 $batch_date_cond2 $batch_year_cond $comp_cond3 $batch_ext_cond2  $floor_cond
	group by c.id,c.batch_no,c.process_id,c.color_id,c.color_range_id,c.batch_date,c.booking_no,c.company_id,c.sales_order_no, c.sales_order_id,c.id,c.extention_no,c.remarks,c.floor_id,d.prod_id,d.fin_dia,d.gsm,d.width_dia_type,d.item_description,d.po_id";

	$result_batch=sql_select($sql_query);
	$all_booking=array();
	$all_batch=array();  $dtls_qty_chk=array();  $sten_dtls_qty_chk=array();
	foreach($result_batch as $row)
	{
		$batch_id_arr[$row[csf("id")]]=$row[csf("id")];
		$sales_id_arr[$row[csf("po_id")]]=$row[csf("po_id")];
		/*$sales_booking_no=explode("-",$row[csf("booking_no")]);
		$non_booking_no=$sales_booking_no[1];
		if($non_booking_no=='SMN')
		{
			$all_booking_non[$row[csf("booking_no")]]=$row[csf("booking_no")];
		}
		else
		{
			$all_booking[$row[csf("booking_no")]]=$row[csf("booking_no")];
		}*/
		$all_batch[$row[csf("id")]]=$row[csf("id")];
	}
	$batch_ids_arr=implode(",",$all_batch);
	$batIds2=chop($batch_ids_arr,','); $batch_cond_for_in2="";$batch_cond_for_in3="";
	$bat_ids2=count(array_unique(explode(",",$batch_ids_arr)));
	if($db_type==2 && $bat_ids2>1000)
	{
		$batch_cond_for_in2=" and (";
		$batch_cond_for_in3=" and (";
		$batIdsArr2=array_chunk(explode(",",$batIds2),999);
		foreach($batIdsArr2 as $ids)
		{
			$ids=implode(",",$ids);
			$batch_cond_for_in2.=" a.batch_id in($ids) or";
			$batch_cond_for_in3.=" b.id in($ids) or";
		}
		$batch_cond_for_in2=chop($batch_cond_for_in2,'or ');
		$batch_cond_for_in2.=")";
		$batch_cond_for_in3=chop($batch_cond_for_in3,'or ');
		$batch_cond_for_in3.=")";
	}
	else
	{
		$batch_cond_for_in2=" and a.batch_id in($batIds2)";
		$batch_cond_for_in3=" and b.id in($batIds2)";
	}

	$batch_id_arr1 = ltrim(implode(",",$sub_process_id_arr),", ");
	$batIds= explode(",",$batch_id_arr1);
	$batch_cond_for_in="";

	$bat_ids=count($batIds);
	if($db_type==2 && $bat_ids>1000)
	{
		$batch_cond_for_in=" and (";
		$batIdsArr=array_chunk($batIds,999);
		foreach($batIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$batch_cond_for_in.=" a.id in($ids) or";
		}
		$batch_cond_for_in=chop($batch_cond_for_in,'or ');
		$batch_cond_for_in.=")";
	}
	else
	{
		$batch_cond_for_in=" and a.id in(".implode(",",$batIds).")";
	}

	$batch_ids=count($batch_id_arr);
	if($db_type==2 && $batch_ids>1000)
	{
		$batch_cond_for_ext=" and (";
		$batIdsArr=array_chunk($batch_id_arr,999);
		foreach($batIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$batch_cond_for_ext.=" a.id in($ids) or";
		}
		$batch_cond_for_ext=chop($batch_cond_for_ext,'or ');
		$batch_cond_for_ext.=")";
	}
	else
	{
		$batch_cond_for_ext=" and a.id in(".implode(",",$batch_id_arr).")";
	}

	$check_batch_extension = sql_select("SELECT a.id,a.extention_no,a.re_dyeing_from from pro_batch_create_mst a where re_dyeing_from>0 $batch_cond_for_ext");
	foreach( $check_batch_extension as $row )
	{
		$batch_ext[$row[csf("id")]] = $row[csf("extention_no")];
		$batch_ext_from[$row[csf("re_dyeing_from")]] = $row[csf("re_dyeing_from")];
	}

	$all_booking_nos="'".implode("','",$all_booking)."'";
	$all_booking_nos_non="'".implode("','",$all_booking_non)."'";
	$all_batch_id="'".implode("','",$all_batch)."'";

	$dyeing_sql_unload="SELECT a.id as mst_id,a.service_company,a.floor_id,a.service_source,a.insert_date,a.remarks,a.batch_id, a.result,a.batch_no,a.process_id,a.remarks,a.entry_form,a.start_hours,a.start_minutes,a.production_date as end_date,a.load_unload_id, a.batch_ext_no,a.process_start_date,a.process_end_date,a.end_minutes,a.end_hours from pro_fab_subprocess a where  a.status_active=1  and a.load_unload_id=2 $batch_cond_for_in2 order by a.id asc";
	$dyeing_unload_data = sql_select($dyeing_sql_unload);
	foreach( $dyeing_unload_data as $row )
	{
		$sub_dyeing_arr[$row[csf("batch_id")]]['result'] = $row[csf("result")];
		$sub_dyeing_arr[$row[csf("batch_id")]]['load_unload_id'] = $row[csf("load_unload_id")];
		
		$is_dyeing_done[$row[csf("batch_id")]] = $row[csf("batch_id")];
		$load_unload_time_arr[$row[csf("batch_id")]]["end_date"]=$row[csf("end_date")];
		$load_unload_time_arr[$row[csf("batch_id")]]["process_time"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		if($row[csf("result")]==1 || $row[csf("result")]==11){
			$load_unload_shade_matched[$row[csf("batch_id")]]=1;
		}
	}

	if($batch_cond_for_in!="")
	{
	  $dyeing_sql="SELECT a.id as mst_id,a.service_company,a.floor_id,a.service_source,a.insert_date,a.remarks,a.batch_id, a.result,a.batch_no,a.process_id,a.entry_form,a.start_hours,a.start_minutes,a.production_date as end_date,a.load_unload_id, a.batch_ext_no,a.process_start_date,a.process_end_date,a.end_minutes,a.end_hours,b.production_qty,b.prod_id,b.no_of_roll,b.width_dia_type from pro_fab_subprocess a left join pro_fab_subprocess_dtls b on a.id=b.mst_id  and b.production_qty>0 where   a.status_active=1  $batch_cond_for_in  order by a.id asc";
		$dyeing_data = sql_select($dyeing_sql);
		$cum_prod_qnty = 0;
		foreach($dyeing_data as $row )
		{
			$load_unload_id=$sub_dyeing_arr[$row[csf("batch_id")]]['load_unload_id'];
			$result_id=$sub_dyeing_arr[$row[csf("batch_id")]]['result'];
			$prod_process_id=explode(",",$row[csf("process_id")]);
			if($row[csf("load_unload_id")]==2 && $row[csf("result")]==1)
			{
				//If found Sliting
				$process_result=30;
			}
			else if($row[csf("mst_id")]=="")
			{
				if(in_array(33,$prod_process_id))
				{
					//If found Heat Setting
					$process_result=33;
				}
			}
			else $process_result=$row[csf("result")];

			//$cum_prod_qnty += $row[csf("production_qty")];
			//$batch_qnty = $batch_wise_qnty[$row[csf("batch_id")]][$row[csf("prod_id")]];
			//if($row[csf("prod_id")]==102875)
				//echo $process_result."==".$row[csf("batch_id")]."==".$row[csf("prod_id")]."==".$row[csf("production_qty")]."<br />";
				if($row[csf("production_qty")]>0)
				{
					$production_qty=$row[csf("production_qty")];
			//echo $production_qty.'='.$row[csf("batch_id")].'='.$row[csf("prod_id")].'='.$row[csf("width_dia_type")].',<br>';
				$subpro_prod_qty_arr[$process_result][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("width_dia_type")]]+=$production_qty;
			   }
			$subpro_prod_arr[$process_result][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("width_dia_type")]]['floor_id']=$row[csf("floor_id")];
			$subpro_prod_arr[$process_result][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("width_dia_type")]]['service_source']=$row[csf("service_source")];
			$subpro_prod_arr[$process_result][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("width_dia_type")]]['process_end_date']=$row[csf("end_date")];
			$subpro_prod_arr[$process_result][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("width_dia_type")]]['remarks']=$row[csf("remarks")];
			$subpro_prod_arr[$process_result][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("width_dia_type")]]['no_of_roll']=$row[csf("no_of_roll")];
			$subpro_prod_arr[$process_result][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("width_dia_type")]]['hr_min']=$row[csf("end_hours")].':'.$row[csf("end_minutes")];
			
			$result_id_arr[$row[csf("mst_id")]] = $row[csf("result")];
			$result_id_entry_form_arr[$row[csf("mst_id")]] = $row[csf("entry_form")];
			$load_unload_arr[$row[csf("mst_id")]] = $row[csf("load_unload_id")];
		}
	}


	/*echo "<pre>";
	print_r($subpro_prod_qty_arr);
	echo "</pre>";*/
	$chk_batch_qty=array();$prod_qty_arr=array();
	$subpro_prod_qty=0;
	foreach($result_batch as $row)
	{
		$process_ids=explode(",",$row[csf("process_id")]);
		$batch_max_process_id = $batch_max_process[$row[csf("id")]];
		$results = $result_id_arr[$batch_max_process_id];
		//echo $results."=".$load_unload."=".$batch_max_process_id."=".$cbo_type;
		$load_unload = $load_unload_arr[$batch_max_process_id];
		$is_shade_matched = $load_unload_shade_matched[$row[csf("id")]];

		$entry_form = $result_id_entry_form_arr[$batch_max_process_id];
		$has_extension = $batch_ext[$row[csf("id")]];
		$sub_entry_form=$batch_sub_process_arr[$row[csf("id")]]['entry_form'];
		$sub_result_id=$batch_sub_process_arr[$row[csf("id")]]['result'];
		

		if(($cbo_type==0 || $cbo_type==30) && ($load_unload==2 && ($results==1 || $results==11 || $results==0)))
		{
			$process_id= 30;
			//echo "X";
		}

		if(($cbo_type==0 || $cbo_type==33) && $results==0 && $entry_form=="")
		{
	 		//If found Peach..
			if(in_array(33, $process_ids) && $has_extension==""){
				$process_id= 33;
				$entry_form=33;
			}
		}

		if($results==13)
		{
			//If found Drying...
			$process_id= ($cbo_type>0 && $cbo_type!=$results)?"":13;
			//echo $process_id.'X';
		}
		if($results==12)
		{
	 		//If found Stentering...
			$process_id= ($cbo_type>0 && $cbo_type!=$results)?"":12;
		}
		if($results==14)
		{
	 		//If found Compacting...
			$process_id= ($cbo_type>0 && $cbo_type!=$results)?"":14;
		}
		if($results==15)
		{
			//If found Brush...
			$process_id= ($cbo_type>0 && $cbo_type!=$results)?"":15;
		}
		if($results==16)
		{
	 		//If found Peach..
			$process_id= ($cbo_type>0 && $cbo_type!=$results)?"":16;
		}

		if($process_id!=""){
			if($process_id==33 )
			{
				$subpro_prod_qty=$row[csf("batch_qty")];
				//echo $subpro_prod_qty.'A';
			}
			else
			{
				$subpro_prod_qty=$subpro_prod_qty_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]][$row[csf("width_dia_type")]];
				//echo $row[csf("id")].'='.$row[csf("prod_id")].'='.$row[csf("width_dia_type")];
			}
			

			$hr_min = $subpro_prod_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]][$row[csf("width_dia_type")]]['hr_min'];


			if($process_id==33)
			{
				$no_of_roll 		= $row[csf("no_of_roll")];
				$process_end_date 	= $row[csf("batch_date")];
				$prod_remarks 		= $row[csf("remarks")];
				$floor_id 			= $row[csf("floor_id")];
			}
			else
			{
				$prod_remarks 		= $subpro_prod_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]][$row[csf("width_dia_type")]]['remarks'];
				$process_end_date 	= $subpro_prod_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]][$row[csf("width_dia_type")]]['process_end_date'];
				$no_of_roll 		= $subpro_prod_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]][$row[csf("width_dia_type")]]['no_of_roll'];
				$floor_id 			= $subpro_prod_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]][$row[csf("width_dia_type")]]['floor_id'];
			}


			/*$sales_booking_no=explode("-",$row[csf("sales_booking_no")]);
			$non_booking_no=$sales_booking_no[1];
			if($non_booking_no=='SMN')
			{
				$all_booking_non[$row[csf("sales_booking_no")]]=$row[csf("sales_booking_no")];
			}
			else
			{
				$all_booking[$row[csf("sales_booking_no")]]=$row[csf("sales_booking_no")];
			}
*/
			$service_source=$subpro_prod_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]][$row[csf("width_dia_type")]]['service_source'];
			$batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]["batch_qty"]=$row[csf("batch_qty")];
			$batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]["prod_qty"]+=$subpro_prod_qty;
			//$summary_wip_arr[$process_id]["prod_qty"]+=$prod_qty_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]];

			$batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]["shadematched"]="Drying Qty";
			$batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]["sales_order_no"]=$row[csf("sales_order_no")];
			$batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]["process_end_date"]=$process_end_date;
			$batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]["batch_date"]=$row[csf("batch_date")];
			if($hr_min)
			{
				$batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]["process_end_time"]=$hr_min;
			}
			$batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]["service_source"]=$knitting_source[$service_source];
			$batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]["booking_no"]=$row[csf("booking_no")];
			$batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]["w_company_id"]=$row[csf("working_company_id")];
			$batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]["floor_id"]=$floor_id;
			$batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]["color_id"]=$row[csf("color_id")];
			$batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]["batch_no"]=$row[csf("batch_no")];
			$batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]["batch_id"]=$row[csf("id")];
			$batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]["batch_ext_no"]=$row[csf("batch_ext_no")];
			$batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]["fabric_desc"]=$row[csf("fabric_desc")];
			$batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]["roll_no"]=$row[csf("roll_no")];
			$batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]["process_id"]=$row[csf("process_id")];
			$batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]["color_range_id"]=$row[csf("color_range_id")];
			$batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]["width_dia_type"]=$row[csf("width_dia_type")];
			$batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]["remarks"]=$prod_remarks;
			$batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]["no_of_roll"]=$no_of_roll;
			$batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]["fin_dia"]=$row[csf("fin_dia")];
			$batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]["gsm"]=$row[csf("gsm")];
			$batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]["barcode_no"].=$row[csf("barcode_no")].',';

			$heat_set_arr 	= array(0, 33);
			$other_production_arr = array(0, $results);
			$dyeing_result_arr = array(1,11,0);

			if($load_unload==1 && $entry_form==35 && in_array(33, $process_ids))
			{
				unset($batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]);
			}
			else if(($cbo_type==0 || $cbo_type==33) && $results==0 && $entry_form=="")
			{
				if(in_array(33, $process_ids) && $has_extension==""){
					unset($batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]);
				}
			}
			else if(($cbo_type==0 || $cbo_type==33) && $sub_result_id==11 && $sub_entry_form=32)//HeatSet Complete Found
			{
				
				if(in_array(33, $process_ids)){
					unset($batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]);
				}
			}
			else if($results==11 && $entry_form!=35 ){
				unset($batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]);
			}

			else if($batch_ext_from[$row[csf("id")]]!="")
			{
				unset($batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]);
			}
			else if($load_unload==2 && $entry_form==35 && !in_array($results, $dyeing_result_arr))
			{
				unset($batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]);
			}else if($load_unload==2 && $entry_form==35 && in_array($results, $dyeing_result_arr))
			{
				if(!in_array($cbo_type,array(0,30))){
					unset($batch_wip_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]]);
				}
			}else{
				//$summary_wip_arr[$process_id]["batch_qty"]+=$row[csf("batch_qty")];
				//$summary_wip_arr[$process_id]["prod_qty"]+=$subpro_prod_qty;
				$summary_wip_arr[$process_id]["no_of_roll"]+=$no_of_roll;
				$summary_wip_arr[$process_id]["batch_no"].=$row[csf("id")].',';
			}

		}

	}
	//print_r($batch_wip_arr);

	$sub_po_sql = "select a.id,b.id batch_id,b.extention_no,b.process_id,e.within_group,d.cust_style_ref,d.order_no,e.subcon_job,d.id po_id,e.party_id as buyer_id,c.prod_id,c.batch_qnty
		from subcon_ord_mst e,subcon_ord_dtls d,pro_batch_create_dtls c,pro_batch_create_mst b
		left join pro_fab_subprocess a on b.id=a.batch_id  and a.status_active=1
		where  d.job_no_mst=e.subcon_job and d.id=c.po_id and c.mst_id=b.id   and b.entry_form=36 and c.status_active=1 and d.status_active=1  $batch_cond $batch_ext_cond $sub_buyer_cond2 $batch_cond_for_in3
		order by a.id desc";
		foreach(sql_select($sub_po_sql) as $v)
		{
			$subcon_job_arr[$v[csf("batch_id")]]["style_ref_no"]=$v[csf("cust_style_ref")];
			$subcon_job_arr[$v[csf("batch_id")]]["order_no"]=$v[csf("order_no")];
			$subcon_job_arr[$v[csf("batch_id")]]["subcon_job"]=$v[csf("subcon_job")];
			$subcon_job_arr[$v[csf("batch_id")]]["buyer_id"]=$v[csf("buyer_id")];
		}

	ob_start();
	if($rpt_type==2)
	{
		if($rpt_type==0) $width=2750;else $width=500;
		?>
		<div>
			<style type="text/css">
				.
				{
					word-break: break-all;
					word-wrap: break-word;
				}
			</style>
			<fieldset style="width:<? echo $width;?>px;">
				<?
				if($rpt_type==2)
				{
						foreach($batch_wip_arr as $page_from_id=>$batch_data)
							{
								if(!empty($batch_data)){
									
									foreach($batch_data as $batch_id=>$prod_data)
									{
										foreach($prod_data as $prod_id=>$row)
										{
											//echo number_format($row["prod_qty"],2); 
											$summary_wip_arr[$page_from_id]["prod_qty"]+=$row["prod_qty"];
											$summary_wip_arr[$page_from_id]["batch_qty"]+=$row["batch_qty"];
										}
									}
									
								}
							}

						//-----------------------------------
					?>
					<div style="width:510px; float:left">
						<table class="rpt_table" width="490" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
							<caption> <strong><? echo  $company_library[$cbo_company_name]; ?><br>Finish fabric WIP Summary Report<br>
								<? echo  change_date_format($txt_date_from).' To '.change_date_format($txt_date_to); ?></strong>
							</caption>
							<thead>
								<tr>
									<th width="20" class="">SL</th>
									<th width="120" class="">Process Name</th>
									<th width="80" class="">Batch Qty.</th>
									<th width="80" class="">WIP Qty.</th>
									<th width="80" class="">No of Batch.</th>
									<th width="" class="">No of Roll</th>
								</tr>
							</thead>
						</table>
						<div style="max-height:380px; text-align:left; width:510px; overflow-y:scroll;" id="scroll_body">
							<table align="left" class="rpt_table" id="table_body_sammary" width="490" cellpadding="0" cellspacing="0" border="1" rules="all">
								<tbody>
									<?
									$s=1; $tot_summ_batch_qty=$tot_summ_prod_qty=0;
									foreach($summary_wip_arr as $process_id=>$row)
									{
										if ($s%2==0)
											$bgcolor="#E9F3FF";
										else
											$bgcolor="#FFFFFF"; $summ_no_of_roll=$row["no_of_roll"];
										$barcode_nos_arr=array_unique(explode(",",rtrim($row["barcode_no"],',')));
										$no_of_roll=count($barcode_nos_arr);
										$batch_nos_arr=array_unique(explode(",",rtrim($row["batch_no"],',')));
										$no_of_batch=count($batch_nos_arr);
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trsum_<? echo $s; ?>','<? echo $bgcolor;?>')" id="trsum_<? echo $s; ?>">
											<td width="20" class="" align="center"><? echo $s;?></td>
											<td width="120" class=""><? echo   $process_format[$process_id];?> </td>
											<td width="80" align="right" class=""><? echo number_format($row["batch_qty"],2);?> </td>
											<td width="80" align="right" class=""><? echo number_format($row["prod_qty"],2);?></td>
											<td width="80" align="center" class=""><? echo $no_of_batch;?></td>
											<td width="" align="center" class=""><? echo $summ_no_of_roll;?></td>
										</tr>
										<?
										$tot_summ_batch_qty+=$row["batch_qty"];
										$tot_summ_prod_qty+=$row["prod_qty"];
										$s++;
									}
									?>
								</tbody>
								<tfoot>
									<tr>
										<th width="140" colspan="2"> Total </h>
											<th width="80" align="right"><? echo number_format($tot_summ_batch_qty,2);?> </th>
											<th width="80" align="right"><? echo number_format($tot_summ_prod_qty,2);?>  </th>
											<th width="80"> </th>
											<th width=""> </th>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>

						<?
						/*foreach (glob("*.xls") as $filename)
						{
							if( @filemtime($filename) < (time()-$seconds_old) )
								@unlink($filename);
						}
						//---------end------------//
						$filename=time().".xls";
						$create_new_doc = fopen($filename, 'w');
						$fdata=ob_get_contents();
						fwrite($create_new_doc,$fdata);
						ob_end_clean();
						echo "$fdata****$filename****$type";
						exit();*/
					}
					?>
					<table class="rpt_table" width="2760" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
						<caption><strong><? echo  $company_library[$cbo_company_name]; ?><br>Finish fabric WIP Report	<br>
							<? echo  change_date_format($txt_date_from).' To '.change_date_format($txt_date_to); ?></strong>
						</caption>
						<thead>
							<tr>
								<th width="80" class="alignment_css">SL</th>
								<th width="80" class="alignment_css">Source</th>
								<th width="80" class="alignment_css">Working <br>Company</th>
								<th width="80" class="alignment_css">Floor</th>
								<th width="80" class="alignment_css">Buyer</th>
								<th width="80" class="alignment_css">Style Ref.</th>
								<th width="80" class="alignment_css">Job</th>
								<th width="80" class="alignment_css">Season</th>
								<th width="90" class="alignment_css">Booking No</th>
								<th width="80" class="alignment_css">Booking Type</th>
								<th width="80" class="alignment_css">Short<br>Booking Type</th>
								<th width="80" class="alignment_css">Division</th>
								<th width="110" class="alignment_css">FSO No</th>
								<th width="80" class="alignment_css">Batch No</th>
								<th width="80" class="alignment_css">Extn. No</th>
								<th width="80" class="alignment_css">Fabric Type</th>
								<th width="80" class="alignment_css">Fabric <br>Composition</th>
								<th width="80" class="alignment_css">GSM</th>
								<th width="80" class="alignment_css">DIA</th>
								<th width="80" class="alignment_css">Dia/Width<br> Type</th>
								<th width="80" class="alignment_css">Color Name</th>
								<th width="80" class="alignment_css">Color Range</th>
								<th width="80" class="alignment_css">Batch Qty.</th>
								<th width="80" class="alignment_css">WIP Qty.</th>
								<th width="80" class="alignment_css">No of Roll</th>
								<th width="80" class="alignment_css">Last Process <br>Fin. Date</th>
								<th width="80" class="alignment_css">Last process <br>Fin. Time</th>
								<th width="80" class="alignment_css">Execution Days</th>
								<th width="80" class="alignment_css">Execution Time[H]</th>
								<th width="80" class="alignment_css">Dyeing Unload Date</th>
								<th width="80" class="alignment_css">Dyeing  <br>Unload Time</th>
								<th width="" class="alignment_css">Remarks</th>
							</tr>
						</thead>
					</table>
					<div style=" max-height:380px; width:2780px; overflow-y:scroll;" id="scroll_body">
						<table align="left" class="rpt_table" id="table_body" width="2760" cellpadding="0" cellspacing="0" border="1" rules="all">
							<tbody>
								<?
								$m=1;
								foreach($batch_wip_arr as $page_from_id=>$batch_data)
								{
									//print_r($batch_data);
									$tot_batch_qty=$tot_wip_qty=0;
									?>
									<tr>
										<th colspan="32" align="left">
											<strong>
												<?
												if($m==1) echo "In-Bound Subcontract<br>";else echo "";
												if($page_from_id==33)
												{
													echo "Heat Setting";
												}
												else
												{
													echo $process_format[$page_from_id];
												}
												?>
											</strong>
										</th>
									</tr>
									<?
									$m++;
									if(!empty($batch_data)){
										$ii=1;
										foreach($batch_data as $batch_id=>$prod_data)
										{
											foreach($prod_data as $prod_id=>$row)
											{
												if ($ii%2==0)
													$bgcolor="#E9F3FF";
												else
													$bgcolor="#FFFFFF";
			
												//$within_group=$booking_data_arr2[$row[("sales_order_no")]]["within_group"];
												$order_no=$subcon_job_arr[$batch_id]["order_no"];
												$subcon_job=$subcon_job_arr[$batch_id]["subcon_job"];
												$buyer_id=$subcon_job_arr[$batch_id]["buyer_id"];
												$style_ref_no=$subcon_job_arr[$batch_id]["style_ref_no"];
												?>
												<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $ii; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $ii; ?>">
													<td width="80" class="alignment_css"><? echo $ii++ ;?></td>
													<td width="80" class="alignment_css"><? echo $row["service_source"] ;?> </td>
													<td width="80" class="alignment_css"><? echo $company_library[$row["w_company_id"]];?> </td>
													<td width="80" class="alignment_css"><? echo $floor_arr[$row["floor_id"]];?></td>
													<td width="80" class="alignment_css"><? echo $buyer_list[$buyer_id];?></td>
													<td width="80" class="alignment_css"><? echo $style_ref_no;?></td>
													<? ?>
													<td width="80" class="alignment_css"><? echo $subcon_job;?></td>
													<td width="80" class="alignment_css"><? //echo $booking_data_arr2[$row[("sales_order_no")]]["season"];?> </td>
													<td width="90" title="SubCon OrderNo" class="alignment_css"><? echo $order_no;?></td>
													<td width="80" class="alignment_css"><? //echo $booking_data_arr[$row["booking_no"]]["type"];?></td>
													<td width="80" class="alignment_css"><? //echo $booking_data_arr[$row["booking_no"]]["short_booking_type"];?> </td>
													<td width="80" class="alignment_css"><? //echo $booking_data_arr[$row["booking_no"]]["division"];?></td>
													<td width="110" class="alignment_css"><? //echo  $row["sales_order_no"];?></td>
													<td width="80" class="alignment_css" title="<? echo $batch_id;?>"><? echo $row["batch_no"];?></td>
													<td width="80" class="alignment_css"><? echo $row["batch_ext_no"];?></td>
													<td width="80" class="alignment_css">
														<?
														$com_data=array_unique( explode(",",$row["fabric_type"])) ;
														$com_data_desc=array_unique( explode(",",$row["fabric_desc"])) ;
														echo $com_data_desc[0];
														$width_dia_type=$fabric_typee[$row["width_dia_type"]];
														?>
													</td>
													<td width="80" class="alignment_css"><? echo $com_data_desc[1]; ?> </td>
													<td width="80" class="alignment_css"><?  echo $row["gsm"];?> </td>
													<td width="80" class="alignment_css"><?  echo $row["fin_dia"];?> </td>
													<td width="80" class="alignment_css">
														<? echo implode(",",array_unique( explode(",", $width_dia_type)));?>
													</td>
													<td width="80" class="alignment_css"><? echo $color_library[$row["color_id"]];?> </td>
													<td width="80" class="alignment_css"><? echo $color_range[$row["color_range_id"]];?> </td>
													<td width="80" class="alignment_css" align="right"><? echo number_format($row["batch_qty"],2);?></td>
													<td width="80" class="alignment_css" title="<? echo $row["shadematched"];?>" align="right">
														<? echo number_format($row["prod_qty"],2);?>
													</td>
													<td width="80" class="alignment_css" align="center">
														<?
														$barcode_nos_arr=array_unique(explode(",",rtrim($row["barcode_no"],',')));
														$no_of_roll=count($barcode_nos_arr);
														if($page_from_id==32) echo $no_of_roll;else  echo $row["no_of_roll"];?>
													</td>
													<td width="80" class="alignment_css">
														<? if($page_from_id==32) $last_process_date=change_date_format($row["batch_date"]); else $last_process_date=change_date_format($row["process_end_date"]); echo $last_process_date;
														?>
													</td>
													<td width="80" class="alignment_css" title="H:M">
														<?
														$dyeing_date=$load_unload_time_arr[$batch_id]["end_date"];
														$dyeing_time=$load_unload_time_arr[$batch_id]["process_time"];
														$unload_date_time=($dyeing_date.' '.$dyeing_time.':'.'00');
														if($page_from_id==33) echo "";else echo $row[('process_end_time')];
														$new_date_time_start=($dyeing_start_date.' '.$dyeing_start_time.':'.'00');
														$new_date_time_end=($row["process_end_date"].' '.$row["process_end_time"].':'.'00');
														$new_date_time_end2=$dyeing_date.' '.$dyeing_time.':'.'00';
														?>
													</td>
													<td width="80" class="alignment_css">
														<?
														$total_time_diff=datediff(n,$unload_date_time ,$new_date_time_end);
														$diff_time_days=$total_time_diff;
														$days_remian=datediff('d',$last_process_date,change_date_format($dyeing_date))-1;
														if($page_from_id==33 || $page_from_id==30) echo ""; else echo  abs($days_remian);?>
													</td>
													<td width="80" class="alignment_css">
														<?
														if($page_from_id==33 || $page_from_id==30) echo "";  else echo  abs ( floor( abs($diff_time_days/60)) )." H :".abs($diff_time_days%60)." M ";
														?>
													</td>
													<td width="80" class="alignment_css"><? echo change_date_format($dyeing_date)  ;?></td>
													<td width="80" class="alignment_css"><? echo $dyeing_time ;?></td>
													<td width="" class="alignment_css"><? echo  $row[('remarks')];?></td>
												</tr>
												<?
												$p++;
												$tot_batch_qty+= $row["batch_qty"];
												$tot_wip_qty+= $row["prod_qty"];
											}
										}
										if($tot_batch_qty>0){
											?>
											<tr style="background-color:whitesmoke; " onClick="change_color('tr3_<? echo $ii; ?>','<? echo $bgcolor; ?>')" id="tr3_<? echo $ii; ?>" style="font-size:12px; cursor:pointer;" >
												<td width="" colspan="22" class="alignment_css" align="right"><strong><? echo  $process_format[$page_from_id];?> Total=</strong></td>
												<td width="80" class="alignment_css" align="right"><? echo number_format($tot_batch_qty,2);?></td>
												<td width="80" class="alignment_css" align="right"><? echo number_format($tot_wip_qty,2);?></td>
												<td width="80" class="alignment_css"></td>
												<td width="80" class="alignment_css"></td>
												<td width="80" class="alignment_css"></td>
												<td width="80" class="alignment_css"></td>
												<td width="80" class="alignment_css"></td>
												<td width="80" class="alignment_css"></td>
												<td width="80" class="alignment_css"></td>
												<td width="" class="alignment_css"></td>
											</tr>
											<?
										}
									}
								}
								?>
							</tbody>
						</table>
					</div>
				</fieldset>
			</div>
			<?
			foreach (glob("*.xls") as $filename)
			{
				if( @filemtime($filename) < (time()-$seconds_old) )
					@unlink($filename);
			}
		//---------end------------//
			$filename=time().".xls";
			$create_new_doc = fopen($filename, 'w');
			$fdata=ob_get_contents();
			fwrite($create_new_doc,$fdata);
			ob_end_clean();
			echo "$fdata****$filename****$type";
			exit();
		}
	}
	
	?>