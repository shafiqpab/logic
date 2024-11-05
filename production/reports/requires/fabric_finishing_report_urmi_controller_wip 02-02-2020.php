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

$process_format=array(0=>"ALL", 33 => 'Heat Setting WIP',30 => 'Slitting/Squeezing WIP', 13 => 'Drying WIP' , 12 => "Stentering WIP", 14 => 'Compacting WIP',  15 => 'Brush WIP', 16 => 'Peach WIP');

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
								echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down('requires/fabric_finishing_report_urmi_controller_wip', this.value, 'load_drop_down_buyer_fso', 'buyer_td_fso' );" );
								?>

							</td>
							<td id="buyer_td_fso">
								<?
								echo create_drop_down( "cbo_buyer_name", 110, "select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 order by short_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
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
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('txt_fso_no').value+'**'+document.getElementById('txt_booking_no').value+'**'+document.getElementById('txt_style_no').value+'**'+document.getElementById('cbo_within_group').value+'**'+document.getElementById('cbo_year').value+'**'+document.getElementById('cbo_buyer_name').value, 'bookingnumbershow_search_list_view', 'search_div', 'fabric_finishing_report_urmi_controller_wip', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
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
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_name; ?>'+'**'+document.getElementById('txt_batch_no').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'batchnumbershow_search_list_view', 'search_div', 'fabric_finishing_report_urmi_controller_wip', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
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
	echo create_drop_down( "cbo_location_id", 110, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id in($data)    order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/fabric_finishing_report_urmi_controller_wip', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();
}
if ($action=="load_drop_down_floor")
{
	$ex_data = explode("_", $data);
	echo create_drop_down( "cbo_floor_id", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process in(3,4) and company_id in($ex_data[0]) and location_id in($ex_data[1]) order by floor_name","id,floor_name",1, "-- Select Floor --", $selected, "load_drop_down( 'requires/fabric_finishing_report_urmi_controller_wip',this.value, 'load_drop_down_machine', 'machine_td' );",0 );
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
	if($cbo_company_name) $comp_cond2=" and b.working_company_id in($cbo_company_name)";else $comp_cond="";
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
	if($fso_no) $sales_cond=" and d.job_no like '%$fso_no'";

	if($cbo_buyer_name>0) $buyer_cond=" and d.po_buyer=$cbo_buyer_name";
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

		$get_batch_ids_by_batch_date=sql_select("select entry_form, b.id,b.process_id from pro_batch_create_mst b where b.status_active=1 $batch_cond $batch_date_cond $floor_cond3");
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

		$get_batch_ids_from_subprocess = sql_select("select a.batch_id from pro_fab_subprocess a where a.status_active=1 and a.load_unload_id=2 $prod_date_cond $floor_cond2 $batch_cond3 group by a.batch_id");

		foreach($get_batch_ids_from_subprocess as $row)
		{
			$production_batch_arr[$row[csf("batch_id")]] = $row[csf("batch_id")];
		}


		if(empty($production_batch_arr))
		{
			$prod_date_cond="";
		}

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

		 $get_batch_ids_from_subprocess_sql = "select a.id,b.id batch_id,b.extention_no,d.within_group,d.id fso_id,d.buyer_id,d.po_buyer
		from fabric_sales_order_mst d,pro_batch_create_dtls c,pro_batch_create_mst b
		left join pro_fab_subprocess a on b.id=a.batch_id and a.status_active=1 $prod_comp_cond
		where d.id=c.po_id and c.mst_id=b.id and c.status_active=1 and d.status_active=1 $sales_cond $batch_cond $comp_cond2 $batch_ext_cond $production_batch_id_cond order by a.id desc";//group by b.id,b.extention_no,d.within_group,d.id,d.buyer_id,d.po_buyer

		$get_batch_ids_from_subprocess = sql_select($get_batch_ids_from_subprocess_sql);
		$i=0;
		foreach($get_batch_ids_from_subprocess as $row)
		{
			$batch_id_arr[$row[csf("batch_id")]] = $row[csf("batch_id")];

			if($row[csf("within_group")]==1){
				if(($row[csf("po_buyer")] == $cbo_buyer_name) || ($cbo_buyer_name==0))
					$fso_id_arr[$row[csf("fso_id")]] = $row[csf("fso_id")];
			}else{
				if(($row[csf("buyer_id")] == $cbo_buyer_name) || ($cbo_buyer_name==0))
					$fso_id_arr[$row[csf("fso_id")]] = $row[csf("fso_id")];
			}

			$batch_sub_id_arr[$row[csf("batch_id")]][] = $row[csf("id")];
			if($row[csf("id")]!="")
				$sub_process_id_arr[$row[csf("id")]]=$row[csf("id")];

			if($i==0){
				$batch_max_process[$row[csf("batch_id")]] = ($row[csf("id")]=="")?0:$row[csf("id")];
			}
			//$batch_max_process[$row[csf("batch_id")]] = $row[csf("id")];
			$batch_id_ext_arr[$row[csf("batch_id")]] = $row[csf("batch_ext_no")];
			$i++;
		}
	} else {
		$get_batch_ids_from_subprocess_sql = "select a.id,b.id batch_id,b.extention_no,b.process_id,d.within_group,d.id fso_id,d.buyer_id,d.po_buyer,c.prod_id,c.batch_qnty
		from fabric_sales_order_mst d,pro_batch_create_dtls c,pro_batch_create_mst b
		left join pro_fab_subprocess a on b.id=a.batch_id and a.status_active=1
		where d.id=c.po_id and c.mst_id=b.id and c.status_active=1 and d.status_active=1 $sales_cond $batch_cond $comp_cond2 $batch_ext_cond
		order by a.id desc";//group by b.id,b.extention_no,b.process_id,d.within_group,d.id,d.buyer_id,d.po_buyer
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
				if(($row[csf("po_buyer")] == $cbo_buyer_name) || ($cbo_buyer_name==0))
					$fso_id_arr[$row[csf("fso_id")]] = $row[csf("fso_id")];
			}else{
				if(($row[csf("buyer_id")] == $cbo_buyer_name) || ($cbo_buyer_name==0))
					$fso_id_arr[$row[csf("fso_id")]] = $row[csf("fso_id")];
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

	if($db_type==2 && count($fso_id_arr)>1000)
	{
		$sales_cond=" and (";
		$fsoArr=array_chunk($fso_id_arr,999);
		foreach($fsoArr as $ids)
		{
			$ids=implode(",",$ids);
			$sales_cond.=" d.po_id in($ids) or";
		}
		$sales_cond=chop($sales_cond,'or ');
		$sales_cond.=")";
	}
	else
	{
		if(!empty($fso_id_arr))
			$sales_cond=" and d.po_id in(".implode(",",$fso_id_arr).")";
	}

	$batch_date_cond2 = (empty($batch_max_process))?$batch_date_cond2:"";
	$sql_query="select c.id ,c.process_id,c.batch_no,c.batch_date as batch_date,c.color_id,c.color_range_id,c.booking_no,c.working_company_id,c.sales_order_no,c.sales_order_id,c.id as batch_id,c.extention_no as batch_ext_no,c.remarks,c.floor_id,d.prod_id,d.width_dia_type,d.item_description as fabric_desc,sum(d.batch_qnty) as batch_qty,d.po_id,count(d.id) no_of_roll
	from pro_batch_create_dtls d,pro_batch_create_mst c
	where d.mst_id=c.id and c.is_sales=1 and c.status_active=1 and d.status_active=1 $batch_id_cond $batch_cond2 $batch_date_cond2 $batch_year_cond $comp_cond $batch_ext_cond2 $sales_cond $floor_cond
	group by c.id,c.batch_no,c.process_id,c.color_id,c.color_range_id,c.batch_date,c.booking_no,c.working_company_id,c.sales_order_no, c.sales_order_id,c.id,c.extention_no,c.remarks,c.floor_id,d.prod_id,d.width_dia_type,d.item_description,d.po_id";

	$result_batch=sql_select($sql_query);
	$all_booking=array();
	$all_batch=array();  $dtls_qty_chk=array();  $sten_dtls_qty_chk=array();
	foreach($result_batch as $row)
	{
		$batch_id_arr[$row[csf("id")]]=$row[csf("id")];
		$sales_id_arr[$row[csf("po_id")]]=$row[csf("po_id")];
		$sales_booking_no=explode("-",$row[csf("booking_no")]);
		$non_booking_no=$sales_booking_no[1];
		if($non_booking_no=='SMN')
		{
			$all_booking_non[$row[csf("booking_no")]]=$row[csf("booking_no")];
		}
		else
		{
			$all_booking[$row[csf("booking_no")]]=$row[csf("booking_no")];
		}
		$all_batch[$row[csf("id")]]=$row[csf("id")];
	}

	$sales_cond_ids=implode(",",$sales_id_arr);
	$poIds=chop($sales_cond_ids,','); $sales_cond_for_in="";
	$po_ids=count(array_unique(explode(",",$sales_cond_for_in)));
	if($db_type==2 && $po_ids>1000)
	{
		$sales_cond_for_in=" and (";
		$poIdsArr=array_chunk(explode(",",$poIds),999);
		foreach($poIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$sales_cond_for_in.=" c.id in($ids) or";
		}
		$sales_cond_for_in=chop($sales_cond_for_in,'or ');
		$sales_cond_for_in.=")";
	}
	else
	{
		$sales_cond_for_in=" and c.id in($poIds)";
	}

	$batch_ids_arr=implode(",",$all_batch);
	$batIds2=chop($batch_ids_arr,','); $batch_cond_for_in2="";
	$bat_ids2=count(array_unique(explode(",",$batch_ids_arr)));
	if($db_type==2 && $bat_ids2>1000)
	{
		$batch_cond_for_in2=" and (";
		$batIdsArr2=array_chunk(explode(",",$batIds2),999);
		foreach($batIdsArr2 as $ids)
		{
			$ids=implode(",",$ids);
			$batch_cond_for_in2.=" a.batch_id in($ids) or";
		}
		$batch_cond_for_in2=chop($batch_cond_for_in2,'or ');
		$batch_cond_for_in2.=")";
	}
	else
	{
		$batch_cond_for_in2=" and a.batch_id in($batIds2)";
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

	$dyeing_sql_unload="SELECT a.id as mst_id,a.service_company,a.floor_id,a.service_source,a.insert_date,a.remarks,a.batch_id, a.result,a.batch_no,a.process_id,a.remarks,a.entry_form,a.start_hours,a.start_minutes,a.production_date as end_date,a.load_unload_id, a.batch_ext_no,a.process_start_date,a.process_end_date,a.end_minutes,a.end_hours,b.production_qty,b.prod_id,b.no_of_roll from pro_fab_subprocess a,pro_fab_subprocess_dtls b where  a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.load_unload_id=2 $batch_cond_for_in2 order by a.id asc";
	$dyeing_unload_data = sql_select($dyeing_sql_unload);
	foreach( $dyeing_unload_data as $row )
	{
		$is_dyeing_done[$row[csf("batch_id")]] = $row[csf("batch_id")];
		$load_unload_time_arr[$row[csf("batch_id")]]["end_date"]=$row[csf("end_date")];
		$load_unload_time_arr[$row[csf("batch_id")]]["process_time"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
		if($row[csf("result")]==1 || $row[csf("result")]==11){
			$load_unload_shade_matched[$row[csf("batch_id")]]=1;
		}
	}

	if($batch_cond_for_in!="")
	{
	$dyeing_sql="SELECT a.id as mst_id,a.service_company,a.floor_id,a.service_source,a.insert_date,a.remarks,a.batch_id, a.result,a.batch_no,a.process_id,a.entry_form,a.start_hours,a.start_minutes,a.production_date as end_date,a.load_unload_id, a.batch_ext_no,a.process_start_date,a.process_end_date,a.end_minutes,a.end_hours,b.production_qty,b.prod_id,b.no_of_roll,b.width_dia_type from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 $batch_cond_for_in  order by a.id asc";
		$dyeing_data = sql_select($dyeing_sql);
		$cum_prod_qnty = 0;
		foreach($dyeing_data as $row )
		{
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

			$subpro_prod_qty_arr[$process_result][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("width_dia_type")]]+=$row[csf("production_qty")];
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

		$load_unload = $load_unload_arr[$batch_max_process_id];
		$is_shade_matched = $load_unload_shade_matched[$row[csf("id")]];

		$entry_form = $result_id_entry_form_arr[$batch_max_process_id];
		$has_extension = $batch_ext[$row[csf("id")]];

		if(($cbo_type==0 || $cbo_type==30) && ($load_unload==2 && ($results==1 || $results==11 || $results==0)))
		{
			$process_id= 30;
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
			}
			else
			{
				$subpro_prod_qty=$subpro_prod_qty_arr[$process_id][$row[csf("id")]][$row[csf("prod_id")]][$row[csf("width_dia_type")]];
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


			$sales_booking_no=explode("-",$row[csf("sales_booking_no")]);
			$non_booking_no=$sales_booking_no[1];
			if($non_booking_no=='SMN')
			{
				$all_booking_non[$row[csf("sales_booking_no")]]=$row[csf("sales_booking_no")];
			}
			else
			{
				$all_booking[$row[csf("sales_booking_no")]]=$row[csf("sales_booking_no")];
			}

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

	$booking_data_arr=array();
	$non_booking_sql="SELECT buyer_id,booking_no,booking_type from wo_non_ord_samp_booking_mst where booking_no in($all_booking_nos_non) and  status_active=1 and booking_type = 4 " ;
	foreach(sql_select($non_booking_sql) as $row )
	{
		if($row[csf("booking_type")]==4)
		{
			$booking_data_arr[$row[csf("booking_no")]]["type"]="Sample Without Order";
			$booking_data_arr[$row[csf("booking_no")]]["buyer_id"]=$buyer_list[$row[csf("buyer_id")]];
		}
	}

	$booking_data="SELECT a.buyer_id,c.buyer_id as fso_buyer, a.booking_no ,a.short_booking_type ,a.booking_type,a.is_short,b.job_no,b.division_id,c.job_no as fso_no,c.po_buyer,c.style_ref_no,c.season,c.within_group	from  fabric_sales_order_mst c left join wo_booking_mst a on a.booking_no=c.SALES_BOOKING_NO left join wo_booking_dtls b on a.booking_no=b.booking_no and a.status_active=1 and b.status_active=1 where c.status_active=1 $sales_cond_for_in group by a.buyer_id, a.booking_no ,a.short_booking_type ,a.booking_type,a.is_short,b.division_id,c.buyer_id,c.season,c.within_group,c.job_no,c.style_ref_no,b.job_no,c.po_buyer  ";
	foreach(sql_select($booking_data) as $v)
	{
		if($booking_data_arr[$v[csf("booking_no")]]["division"])
			$booking_data_arr[$v[csf("booking_no")]]["division"].=','.$short_division_array[$v[csf("division_id")]];
		else
			$booking_data_arr[$v[csf("booking_no")]]["division"].=$short_division_array[$v[csf("division_id")]];

		$booking_data_arr[$v[csf("booking_no")]]["short_booking_type"] =$short_booking_type[$v[csf("short_booking_type")]];
		if($v[csf("booking_type")]==1 && $v[csf("is_short")]==1)
		{
			$booking_data_arr[$v[csf("booking_no")]]["type"]="Short";
		}
		else if($v[csf("booking_type")]==1 && $v[csf("is_short")]==2)
		{
			$booking_data_arr[$v[csf("booking_no")]]["type"]="Main";
		}
		else if($v[csf("booking_type")]==2)
		{
			$booking_data_arr[$v[csf("booking_no")]]["type"]="Trims";
		}
		else if($v[csf("booking_type")]==3)
		{
			$booking_data_arr[$v[csf("booking_no")]]["type"]="Service";
		}
		else if($v[csf("booking_type")]==4)
		{
			$booking_data_arr[$v[csf("booking_no")]]["type"]="Sample";
		}
		else if($v[csf("booking_type")]==5)
		{
			$booking_data_arr[$v[csf("booking_no")]]["type"]="Trims Sample";
		}
		else if($v[csf("booking_type")]==6)
		{
			$booking_data_arr[$v[csf("booking_no")]]["type"]="Embellishment sample";
		}
		else if($v[csf("booking_type")]==7)
		{
			$booking_data_arr[$v[csf("booking_no")]]["type"]="Dia";
		}
		if($v[csf("within_group")]==2)
		{
			$booking_data_arr2[$v[csf("fso_no")]]["buyer_id"]=$buyer_list[$v[csf("fso_buyer")]];
		}
		else
		{
			$booking_data_arr[$v[csf("booking_no")]]["buyer_id"]=$buyer_list[$v[csf("po_buyer")]];
		}
		$booking_data_arr2[$v[csf("fso_no")]]["style_ref_no"]=$v[csf("style_ref_no")];
		$booking_data_arr[$v[csf("booking_no")]]["job_no"]=$v[csf("job_no")];
		$booking_data_arr2[$v[csf("fso_no")]]["within_group"]=$v[csf("within_group")];
		$booking_data_arr2[$v[csf("fso_no")]]["season"]=$v[csf("season")];
	}

	ob_start();
	if($rpt_type==0 || $rpt_type==1 )
	{
		if($rpt_type==0) $width=2750;else $width=500;
		?>
		<div>
			<style type="text/css">
				.alignment_css
				{
					word-break: break-all;
					word-wrap: break-word;
				}
			</style>
			<fieldset style="width:<? echo $width;?>px;">
				<?
				if($rpt_type!=0)
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
					<div>
						<table class="rpt_table" width="490" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
							<caption> <strong><? echo  $company_library[$cbo_company_name]; ?><br>	Finish fabric WIP Summary Report	<br>
								<? echo  change_date_format($txt_date_from).' To '.change_date_format($txt_date_to); ?></strong>
							</caption>
							<thead>
								<tr>
									<th width="20" class="alignment_css">SL</th>
									<th width="120" class="alignment_css">Process Name</th>
									<th width="80" class="alignment_css">Batch Qty.</th>
									<th width="80" class="alignment_css">WIP Qty.</th>
									<th width="80" class="alignment_css">No of Batch.</th>
									<th width="" class="alignment_css">No of Roll</th>
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
											<td width="20" class="alignment_css" align="center"><? echo $s;?></td>
											<td width="120" class="alignment_css"><? echo   $process_format[$process_id];?> </td>
											<td width="80" align="right" class="alignment_css"><? echo number_format($row["batch_qty"],2);?> </td>
											<td width="80" align="right" class="alignment_css"><? echo number_format($row["prod_qty"],2);?></td>
											<td width="80" align="center" class="alignment_css"><? echo $no_of_batch;?></td>
											<td width="" align="center" class="alignment_css"><? echo $summ_no_of_roll;?></td>
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

								foreach($batch_wip_arr as $page_from_id=>$batch_data)
								{
									$tot_batch_qty=$tot_wip_qty=0;
									?>
									<tr>
										<th colspan="32" align="left">
											<strong>
												<?
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
												$within_group=$booking_data_arr2[$row[("sales_order_no")]]["within_group"]
												?>
												<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $ii; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $ii; ?>">
													<td width="80" class="alignment_css"><? echo $ii++ ;?></td>
													<td width="80" class="alignment_css"><? echo $row["service_source"] ;?> </td>
													<td width="80" class="alignment_css"><? echo $company_library[$row["w_company_id"]];?> </td>
													<td width="80" class="alignment_css"><? echo $floor_arr[$row["floor_id"]];?></td>
													<td width="80" class="alignment_css"><? if($within_group==2) echo $booking_data_arr2[$row[("sales_order_no")]]["buyer_id"];else echo $booking_data_arr[$row[("booking_no")]]["buyer_id"];?></td>
													<td width="80" class="alignment_css"><? echo $booking_data_arr2[$row[("sales_order_no")]]["style_ref_no"];?></td>
													<? ?>
													<td width="80" class="alignment_css"><? if($within_group==1) echo $booking_data_arr[$row[("booking_no")]]["job_no"];?></td>
													<td width="80" class="alignment_css"><? echo $booking_data_arr2[$row[("sales_order_no")]]["season"];?> </td>
													<td width="90" class="alignment_css"><? echo $row["booking_no"];?></td>
													<td width="80" class="alignment_css"><? echo $booking_data_arr[$row["booking_no"]]["type"];?></td>
													<td width="80" class="alignment_css"><? echo $booking_data_arr[$row["booking_no"]]["short_booking_type"];?> </td>
													<td width="80" class="alignment_css"><? echo $booking_data_arr[$row["booking_no"]]["division"];?></td>
													<td width="110" class="alignment_css"><? echo  $row["sales_order_no"];?></td>
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
													<td width="80" class="alignment_css"><?  echo $com_data_desc[2];?> </td>
													<td width="80" class="alignment_css"><?  echo $com_data_desc[3];?> </td>
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