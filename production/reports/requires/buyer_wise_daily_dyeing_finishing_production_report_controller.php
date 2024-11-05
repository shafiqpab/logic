<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


//$process_format=array(0=>"ALL", 33 => 'Heat Setting WIP',30 => 'Slitting/Squeezing WIP', 13 => 'Drying WIP' , 12 => "Stentering WIP", 14 => 'Compacting WIP',  15 => 'Brush WIP', 16 => 'Peach WIP');

//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 110, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id in($data)    order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/machine_wise_finishing_report_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();
}

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
	exit();
}

if ($action=="load_drop_down_floor")
{
	$ex_data = explode("_", $data);
	echo create_drop_down( "cbo_floor_id", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process in(3,4) and company_id in($ex_data[0]) and location_id in($ex_data[1]) order by floor_name","id,floor_name",1, "-- Select Floor --", $selected, "load_drop_down( 'requires/machine_wise_finishing_report_controller',this.value, 'load_drop_down_machine', 'machine_td' );",0 );
	exit();
}

if ($action=="load_drop_down_machine")
{
	echo create_drop_down( "cbo_machine_name", 135, "SELECT id,machine_no || '-' || brand as machine_name from lib_machine_name where   floor_id=$data and status_active=1 and is_deleted=0 and is_locked=0 ","id,machine_name", 1, "-- Select Machine --", $selected, "",0 );
	exit();
}


if($action=="load_drop_down_buyer_fso")
{

	echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
	exit();
}


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
								echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down('requires/machine_wise_finishing_report_controller', this.value, 'load_drop_down_buyer_fso', 'buyer_td_fso' );" );
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
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('txt_fso_no').value+'**'+document.getElementById('txt_booking_no').value+'**'+document.getElementById('txt_style_no').value+'**'+document.getElementById('cbo_within_group').value+'**'+document.getElementById('cbo_year').value+'**'+document.getElementById('cbo_buyer_name').value, 'bookingnumbershow_search_list_view', 'search_div', 'machine_wise_finishing_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
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
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_name; ?>'+'**'+document.getElementById('txt_batch_no').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'batchnumbershow_search_list_view', 'search_div', 'machine_wise_finishing_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
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
		$search_con=" and batch_no like('%".trim($txt_batch_no)."')";
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



if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$all_condition="";

	$cbo_type = str_replace("'","",$cbo_type);
	$cbo_process_type = str_replace("'","",$cbo_process_type);
	$cbo_company_name = str_replace("'","",$cbo_company_name);
	$cbo_location_id = str_replace("'","",$cbo_location_id);
	$txt_job_no = trim(str_replace("'","",$txt_job_no));
	$txt_booking_number = trim(str_replace("'","",$txt_booking_number));
	$cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
	$txt_batch_number = trim(str_replace("'","",$txt_batch_number));
	$batch_id = str_replace("'","",$batch_id);
	$cbo_floor_id = str_replace("'","",$cbo_floor_id);
	$batch_color = str_replace("'","",$batch_color);
	$cbo_machine_name = str_replace("'","",$cbo_machine_name);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	/*
		if($cbo_process_type==156){$whereCon.=" and a.entry_form=33";} //Compacting
		else if($cbo_process_type==63){$whereCon.=" and a.entry_form=30";} //Slitting/Squeezing
		else if($cbo_process_type==179){$whereCon.=" and a.entry_form=310";} //De-oiling
		else if($cbo_process_type==200){$whereCon.=" and a.entry_form=323";} //Dry Slitting
		else if($cbo_process_type==170){$whereCon.=" and a.entry_form=34";} //Special Finish
		else if($cbo_process_type==65){$whereCon.=" and a.entry_form=48";} //Stentering
		else if($cbo_process_type==171){$whereCon.=" and a.entry_form=31";} //Drying
		else if($cbo_process_type==94){$whereCon.=" and a.entry_form=47";} //Singeing
		else if($cbo_process_type==33){$whereCon.=" and a.entry_form=32";} //Heat Setting
	*/	
	
	//echo $whereCon;die;
	
	if($cbo_process_type !=''){$whereCon.=" and a.PROCESS_ID in($cbo_process_type)";}
	
	if($cbo_company_name) $whereCon.=" and a.COMPANY_ID=$cbo_company_name";
	if($txt_batch_number) $whereCon.=" and a.batch_no like '%$txt_batch_number'";
	if($batch_id) $whereCon.=" and a.batch_id=$batch_id";
	if($cbo_floor_id) $whereCon.=" and a.floor_id=$cbo_floor_id";
	if($cbo_machine_name) $whereCon.=" and a.MACHINE_ID=$cbo_machine_name";
	
	list($batch_against,$sample_type)=explode(',',$cbo_type);
	if($batch_against){
		$whereCon.=" and c.BATCH_AGAINST=$batch_against";
	}

	if($batch_against==3){
		$whereCon.=" and c.BOOKING_WITHOUT_ORDER=$sample_type";
	}

	
	
	
	if($txt_date_from!="" && $txt_date_to!="")
	{
		if($db_type==0)
		{
			$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
		}
		else if($db_type==2)
		{
			$txt_date_from=change_date_format($txt_date_from,'','',-1);
			$txt_date_to=change_date_format($txt_date_to,'','',-1);
		}
		
		$whereCon.=" and a.PRODUCTION_DATE between '$txt_date_from' and '$txt_date_to'";
		
	}


	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$floor_arr=return_library_array( "select id,floor_name from  lib_prod_floor",'id','floor_name');
	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$machine_arr=return_library_array( "SELECT id,machine_no || '-' || brand as machine_name from lib_machine_name where   status_active=1 and is_deleted=0 and is_locked=0", "id", "machine_name"  );
	
	
	$supplier_library=return_library_array( "select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 and a.is_deleted=0 group by a.id, a.supplier_name order by a.supplier_name", "id", "supplier_name"  );
	
	
	
//SELF ORDER.......................................................
	
	if($cbo_type==0 || $cbo_type==1){ 
		
		if($db_type==0){
			$PO_ID="group_concat( distinct e.id) AS PO_ID";
		}
		if($db_type==2){
			$PO_ID="listagg(e.id ,',') within group (order by e.id) AS PO_ID";
		}
		
		
		$inhouse_sql="select a.ID,a.RESULT,a.PROCESS_ID,a.PRODUCTION_DATE,a.MACHINE_ID,a.BATCH_ID,a.BATCH_NO,a.SHIFT_NAME,a.FLOOR_ID,a.REMARKS,a.BATCH_EXT_NO,a.FABRIC_TYPE,b.GSM,b.DIA_WIDTH,b.CONST_COMPOSITION,b.PRODUCTION_QTY,b.WIDTH_DIA_TYPE,c.BOOKING_NO,c.COLOR_ID,c.ENTRY_FORM,c.TOTAL_TRIMS_WEIGHT, f.JOB_NO, f.BUYER_NAME,f.STYLE_REF_NO,g.BOOKING_TYPE,g.IS_SHORT,h.LOT,$PO_ID ,c.BATCH_WEIGHT as BATCH_QTY
	
	from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst c ,pro_batch_create_dtls d,wo_po_break_down e, wo_po_details_master f,WO_BOOKING_MST g,PRODUCT_DETAILS_MASTER h
	where  a.id = b.mst_id
         AND c.BOOKING_NO = g.BOOKING_NO
         AND b.PROD_ID = h.id
         AND b.PROD_ID = d.PROD_ID
         AND d.PROD_ID = h.id
         AND d.po_id = e.id
         AND c.id = a.batch_id
         AND c.id = d.mst_id
         AND d.po_id = e.id
         AND e.job_no_mst = f.job_no  and c.entry_form in(0,36) and a.PROCESS_ID in(33,94,63,171,65,156,179,200,170,209,231) $whereCon 
	group by a.ID,
         a.RESULT,
         a.PROCESS_ID,
         a.PRODUCTION_DATE,
         a.MACHINE_ID,
         a.BATCH_ID,
         a.BATCH_NO,
         a.SHIFT_NAME,
         a.FLOOR_ID,
         a.REMARKS,
         a.BATCH_EXT_NO,
         a.FABRIC_TYPE,
         b.GSM,
         b.DIA_WIDTH,
         b.CONST_COMPOSITION,
         b.PRODUCTION_QTY,
         b.WIDTH_DIA_TYPE,
         c.BOOKING_NO,
         c.COLOR_ID,
         c.ENTRY_FORM,
         c.TOTAL_TRIMS_WEIGHT,
         f.JOB_NO,
         f.BUYER_NAME,
         f.STYLE_REF_NO,
         g.BOOKING_TYPE,
         g.IS_SHORT,
         h.LOT,
         c.BATCH_WEIGHT, b.ID
	";// and a.PROCESS_ID='$cbo_process_type' and a.entry_form=30
	
	    //echo $inhouse_sql; 
		$po_id_arr=array();
		$inhouse_sql_result = sql_select($inhouse_sql);
		foreach( $inhouse_sql_result as $row )
		{
			$key=$row[PROCESS_ID].'**'.$row[PRODUCTION_DATE].'**'.$row[MACHINE_ID].'**'.$row[JOB_NO].'**'.$row[STYLE_REF_NO].'**'.$row[BATCH_NO].'**'.$row[BATCH_ID].'**'.$row[COLOR_ID].'**'.$row[BOOKING_TYPE].'**'.$row[IS_SHORT];
			
			
			foreach(array_unique(explode(',',$row[PO_ID])) as $poid){
				$po_id_arr[$poid]=$poid;
			}
			
			$qty_arr[BATCH_QTY][$key]=$row[BATCH_QTY];
			$qty_arr[PRODUCTION_QTY][$key]+=$row[PRODUCTION_QTY];
	
			$buyerWiseProductionQtyArr[$row[BUYER_NAME]]+=$row[PRODUCTION_QTY];
			$processWiseProductionQtyArr[$row[PROCESS_ID]]+=$row[PRODUCTION_QTY];
			if($row[MACHINE_ID]<1){
				$processBatchWiseProductionQtyArr[$row[PROCESS_ID].'**'.$row[BATCH_NO]]+=$row[PRODUCTION_QTY];
			}
			$proTypeWiseProductionQtyArr[$row[BOOKING_TYPE].'**'.$row[IS_SHORT]]+=$row[PRODUCTION_QTY];
			

			$dataArr[$row[BUYER_NAME]][$key]=array(
				PO_ID=>$row[PO_ID],
				LOT=>$row[LOT],
				BOOKING_TYPE=>$row[BOOKING_TYPE],
				IS_SHORT=>$row[IS_SHORT],
				PROCESS_ID=>$row[PROCESS_ID],
				BUYER_NAME=>$row[BUYER_NAME],
				PRODUCTION_DATE=>$row[PRODUCTION_DATE],
				JOB_NO=>$row[JOB_NO],
				STYLE_REF_NO=>$row[STYLE_REF_NO],
				MACHINE_ID=>$row[MACHINE_ID],
				BOOKING_NO=>$row[BOOKING_NO],
				BATCH_NO=>$row[BATCH_NO],
				BATCH_ID=>$row[BATCH_ID],
				SHIFT_NAME=>$row[SHIFT_NAME],
				FLOOR_ID=>$row[FLOOR_ID],
				COLOR_ID=>$row[COLOR_ID],
				RESULT=>$row[RESULT],
				FABRIC_TYPE=>$row[FABRIC_TYPE],
				BATCH_EXT_NO=>$row[BATCH_EXT_NO],
				REMARKS=>implode(',',$remarks_arr[$key2]),
				CONST_COMPOSITION=>$row[CONST_COMPOSITION],
				GSM=>$row[GSM],
				DIA_WIDTH=>$row[DIA_WIDTH],
				WIDTH_DIA_TYPE=>$row[WIDTH_DIA_TYPE],
				REMARKS=>$row[REMARKS],
				BATCH_QTY=>$qty_arr[BATCH_QTY][$key],
				PRODUCTION_QTY=>$qty_arr[PRODUCTION_QTY][$key],
				REPORCESS_QTY=>$qty_arr[FIRST_REPORCESS_QTY][$key],
			);
	
		}
	
	}//......self

	
	
	//SUBCONTACT ORDER.......................................................
	
	if($cbo_type==0 || $cbo_type==2){ 
		$sub_sql="select a.ID,a.RESULT,a.PROCESS_ID,a.PRODUCTION_DATE,a.MACHINE_ID,a.BATCH_ID,a.BATCH_NO,a.SHIFT_NAME,a.FLOOR_ID,a.REMARKS,a.BATCH_EXT_NO,a.FABRIC_TYPE,b.GSM,b.DIA_WIDTH,b.CONST_COMPOSITION,c.BATCH_WEIGHT as BATCH_QTY,
		
		b.PRODUCTION_QTY,b.WIDTH_DIA_TYPE,c.BOOKING_NO,c.COLOR_ID,c.ENTRY_FORM,c.TOTAL_TRIMS_WEIGHT,e.ORDER_NO, f.subcon_job as JOB_NO, f.party_id as BUYER_NAME,b.CONST_COMPOSITION,e.MAIN_PROCESS_ID
	
	from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst c ,pro_batch_create_dtls d,subcon_ord_dtls e, subcon_ord_mst f
	where a.id=b.mst_id and c.id=a.batch_id and c.id=d.mst_id and d.po_id=e.id and e.job_no_mst=f.subcon_job and c.entry_form in(0,36)   $whereCon 
	ORDER BY b.id
	";// and a.PROCESS_ID='$cbo_process_type' and a.entry_form=30
	//and a.PROCESS_ID in(33,94,63,171,65,156,179,200,170,209,231)
	   //echo $sub_sql;die;
	
		$sub_sql_result = sql_select($sub_sql);
		$subDataArr=array();
		$qty_arr=array();
		foreach( $sub_sql_result as $row )
		{
			$key=$row[PROCESS_ID].'**'.$row[PRODUCTION_DATE].'**'.$row[MACHINE_ID].'**'.$row[JOB_NO].'**'.$row[STYLE_REF_NO].'**'.$row[BATCH_NO].'**'.$row[BATCH_ID].'**'.$row[COLOR_ID];
			
			$qty_arr[BATCH_QTY][$key]=$row[BATCH_QTY];
			$qty_arr[PRODUCTION_QTY][$key]=$row[PRODUCTION_QTY];
	
			//$buyerWiseProductionQtyArr[$row[BUYER_NAME]]+=$row[PRODUCTION_QTY];
			
			
			$buyerWiseProductionQtyArr[$row[BUYER_NAME]]+=$row[PRODUCTION_QTY];
			$processWiseProductionQtyArr[$row[PROCESS_ID]]+=$row[PRODUCTION_QTY];
			if($row[MACHINE_ID]<1){
				$processBatchWiseProductionQtyArr[$row[PROCESS_ID].'**'.$row[BATCH_NO]]+=$row[PRODUCTION_QTY];
			}
			$proTypeWiseProductionQtyArr[$row[BOOKING_TYPE].'**'.$row[IS_SHORT]]+=$row[PRODUCTION_QTY];
			
			
			$subDataArr[$row[BUYER_NAME]][$key]=array(
				MAIN_PROCESS_ID=>$row[MAIN_PROCESS_ID],
				ORDER_NO=>$row[ORDER_NO],
				CONST_COMPOSITION=>$row[CONST_COMPOSITION],
				PROCESS_ID=>$row[PROCESS_ID],
				BUYER_NAME=>$row[BUYER_NAME],
				PRODUCTION_DATE=>$row[PRODUCTION_DATE],
				JOB_NO=>$row[JOB_NO],
				STYLE_REF_NO=>$row[STYLE_REF_NO],
				MACHINE_ID=>$row[MACHINE_ID],
				BOOKING_NO=>$row[BOOKING_NO],
				BATCH_NO=>$row[BATCH_NO],
				BATCH_ID=>$row[BATCH_ID],
				SHIFT_NAME=>$row[SHIFT_NAME],
				FLOOR_ID=>$row[FLOOR_ID],
				COLOR_ID=>$row[COLOR_ID],
				RESULT=>$row[RESULT],
				FABRIC_TYPE=>$row[FABRIC_TYPE],
				BATCH_EXT_NO=>$row[BATCH_EXT_NO],
				REMARKS=>implode(',',$remarks_arr[$key2]),
				CONST_COMPOSITION=>$row[CONST_COMPOSITION],
				GSM=>$row[GSM],
				DIA_WIDTH=>$row[DIA_WIDTH],
				WIDTH_DIA_TYPE=>$row[WIDTH_DIA_TYPE],
				REMARKS=>$row[REMARKS],
				BATCH_QTY=>$qty_arr[BATCH_QTY][$key],
				PRODUCTION_QTY=>$qty_arr[PRODUCTION_QTY][$key],
				REPORCESS_QTY=>$qty_arr[FIRST_REPORCESS_QTY][$key],
			);
	
		}
	
	}//......SUBCONTACT

	
	//color type.................	
 	$color_sql = "SELECT B.PO_BREAK_DOWN_ID,B.FABRIC_COLOR_ID,C.COLOR_TYPE_ID, C.GSM_WEIGHT,B.FIN_FAB_QNTY,B.GREY_FAB_QNTY,c.PROCESS_LOSS_METHOD FROM WO_BOOKING_DTLS B,WO_PRE_COST_FABRIC_COST_DTLS C WHERE B.PRE_COST_FABRIC_COST_DTLS_ID=C.ID AND B.BOOKING_TYPE=1 AND B.JOB_NO=B.JOB_NO AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.PO_BREAK_DOWN_ID IN(".implode(',',$po_id_arr).") GROUP BY B.PO_BREAK_DOWN_ID,B.FABRIC_COLOR_ID,C.COLOR_TYPE_ID, C.GSM_WEIGHT,B.FIN_FAB_QNTY,B.GREY_FAB_QNTY,c.PROCESS_LOSS_METHOD";
    //echo  $color_sql;
	$color_sql_result = sql_select($color_sql);
	foreach ($color_sql_result as $row) {
		$color_type_array_precost[$row[PO_BREAK_DOWN_ID]][$row[GSM_WEIGHT]][$row[FABRIC_COLOR_ID]] = $color_type[$row[COLOR_TYPE_ID]];
		$finish_qty[$row[PO_BREAK_DOWN_ID]][$row[GSM_WEIGHT]][$row[FABRIC_COLOR_ID]] = $row[FIN_FAB_QNTY];
		$gray_qty[$row[PO_BREAK_DOWN_ID]][$row[GSM_WEIGHT]][$row[FABRIC_COLOR_ID]] = $row[GREY_FAB_QNTY];
		$process_method[$row[PO_BREAK_DOWN_ID]][$row[GSM_WEIGHT]][$row[FABRIC_COLOR_ID]] = $row[PROCESS_LOSS_METHOD];
	}



	
	
		
		$width=2100;
		$width2=1500;
		ob_start();
		?>
        <div>
            <fieldset style="width:<? echo $width+30;?>px;">
                <table width="<? echo $width;?>">
                    <tr>
                        <th colspan="24">
                        <caption>
                            <strong>
                                <? echo $company_library[$cbo_company_name]; ?><br>Buyer Wise Daily Dyeing Finishing Production Report<br>
                                <? echo change_date_format($txt_date_from).' To '.change_date_format($txt_date_to); ?>
                            </strong>
                        </caption>
                        </th>
                    </tr>
                </table>
                
     <table align="left">
        <tr>
            <td valign="top"><!-----------------Buyer Summary--------------------->
               <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="left"> 
                  <thead>
                      <th width="35">SL</th>
                      <th width="150">Buyer</th>
                      <th width="80">Prod qty</th>
                  </thead>
                  <tbody>
                      <? 
                      $i=1;
					  $proTotal=0;
                      foreach($buyerWiseProductionQtyArr as $buyer_id=>$qty){ 
					  $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					  ?>
                      <tr bgcolor="<? echo $bgcolor; ?>">
                          <td align="center"><?= $i;?></td>
                          <td><?= $buyer_arr[$buyer_id];?></td>
                          <td align="right"><?= number_format($qty,4);?></td>
                      </tr>
                      <? 
                      $i++;
					  $proTotal+=$qty;
                      } ?>
                  </tbody>
                  <tfoot>
                      <th colspan="2" align="center">Total</th>
                      <th align="right"><?= number_format($proTotal,4);?></th
                  ></tfoot>
               </table>
          </td>
          <td valign="top"><!-----------------Production Summary--------------------->
          
               <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="left"> 
                  <thead>
                      <tr>
                          <th colspan="3">Production Summery</th>
                      </tr>
                      <tr>
                          <th width="35">SL</th>
                          <th width="150">Prod Type</th>
                          <th width="80">Prod qty</th>
                      </tr>
                  </thead>
                  <tbody>
                      <? 
                      $i=1;$proTotal=0;
                      foreach($proTypeWiseProductionQtyArr as $fb_type=>$qty){ 
					  list($fb_type,$is_short)=explode('**',$fb_type);
					  
					  if($fb_type==1 && $is_short==1){
						  $bookingType="Short";
					  }
					  else if($fb_type==1 && $is_short==2){
						  $bookingType="Main";
					  }
					  else{
						  $bookingType=$booking_type[$fb_type]; 
					  }
					  
					  
					  $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					  ?>
                      <tr bgcolor="<? echo $bgcolor; ?>">
                          <td align="center"><?= $i;?></td>
                          <td><?= $bookingType;?></td>
                          <td align="right"><?= number_format($qty,4);?></td>
                      </tr>
                      <? 
                      $i++;
					  $proTotal+=$qty;
                      } ?>
                  </tbody>
                  <tfoot>
                      <th colspan="2" align="center">Total</th>
                      <th align="right"><?= number_format($proTotal,4);?></th>
                  </tfoot>
               </table>
          
          </td>
          <td valign="top"><!----------------- No Machine Production Summary--------------------->
          
              
               <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="left"> 
                  <thead>
                      <tr>
                          <th colspan="4"> No Machine Production </th>
                      </tr>
                      <tr>
                          <th width="35">SL</th>
                          <th width="150">Process</th>
                          <th width="100">Batch</th>
                          <th width="80">Prod qty</th>
                      </tr>
                  </thead>
                  <tbody>
                      <? 
                      $i=1;$proTotal=0;
                      foreach($processBatchWiseProductionQtyArr as $processBatch=>$qty){ 
					  $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					  list($process_id,$batch)=explode('**',$processBatch);
					  ?>
                      <tr bgcolor="<? echo $bgcolor; ?>">
                          <td width="35" align="center"><?= $i;?></td>
                          <td width="150"><?= $conversion_cost_head_array[$process_id];?></td>
                          <td width="100" align="right"><?= $batch;?></td>
                          <td width="80" align="right"><?= number_format($qty,4);?></td>
                      </tr>
                      <? 
                      $i++;
					  $proTotal+=$qty;
                      } ?>
                  </tbody>
                  <tfoot>
                      <th colspan="3" align="center">Total</th>
                      <th align="right"><?= number_format($proTotal,4);?></th>
                  </tfoot>
               </table>
          
          </td>
          <td valign="top"><!----------------- Process Summary--------------------->
          
              
               <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="left"> 
                  <thead>
                      <tr>
                          <th width="35">SL</th>
                          <th width="150">Process</th>
                          <th width="80">Prod qty</th>
                      </tr>
                  </thead>
                  <tbody>
                      <? 
                      $i=1;$proTotal=0;
                      foreach($processWiseProductionQtyArr as $process_id=>$qty){ 
					  $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					 ?>
                      <tr bgcolor="<? echo $bgcolor; ?>">
                          <td width="35" align="center"><?= $i;?></td>
                          <td width="150"><?= $conversion_cost_head_array[$process_id];?></td>
                          <td width="80" align="right"><?= number_format($qty,4);?></td>
                      </tr>
                      <? 
                      $i++;
					  $proTotal+=$qty;
                      } ?>
                  </tbody>
                  <tfoot>
                      <th colspan="2" align="center">Total</th>
                      <th align="right"><?= number_format($proTotal,4);?></th>
                  </tfoot>
               </table>
          
          </td>
      </tr>
  </table>

               
               
       <div style="clear:both; overflow:hidden;">        
	   <? if($cbo_type==0 || $cbo_type==1){ ?>  
        <table class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
            <thead>
            <tr>
                <td colspan="24" bgcolor="#CCCCCC"><strong>Inhouse</strong></td>
            </tr>
            <tr>
                <th width="35">SL</th>
                <th width="80">Buyer</th>
                <th width="80">Prod date</th>
                <th width="80">M/C no</th>
                <th width="80">Style Ref.</th>
                <th width="80">Job No</th>
                <th width="80">Booking</th>
                <th width="80">Booking Type</th>
                <th width="80">Color</th>
                <th width="80">Fabric type</th>
                <th width="80">Color Type</th>
                <th width="80">Batch</th>
                <th width="80">Process</th>
                <th width="80">Lot</th>
                <th width="80">GSM</th>
                <th width="80">Dia Type</th>
                <th width="80">Batch qty</th>
                <th width="80">Fin. Prod qty</th>
                <th width="80">Balance Qty</th>
                <th width="80">Fin Result</th>
                <th width="80">Budget Process Loss</th>
                <th width="80">Actual Process Loss</th>
                <th width="80">P Loss Status</th>
                <th>Remarks</th>
            </tr>
            </thead>
        </table>
        <div style="max-height:380px; width:<? echo $width+18;?>px; overflow-y:scroll; float:left;" id="scroll_body">
            <table align="left" class="rpt_table" id="table_body" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tbody>
                    <?
                    $i=1;
					$grand_batch_qty=0;
					$grand_production_qty=0;
					foreach($dataArr as $buyer_id=>$rowArr)
                    {
						foreach($rowArr as $key=>$row)
						{
                        
                        $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						
						  if($row[BOOKING_TYPE]==1 && $row[IS_SHORT]==1){
							  $bookingType="Short";
						  }
						  else if($row[BOOKING_TYPE]==1 && $row[IS_SHORT]==2){
							  $bookingType="Main";
						  }
						  else{
							  $bookingType=$booking_type[$row[BOOKING_TYPE]]; 
						  }
						  
							$total_fin_fab_qnty=0;
							$total_grey_fab_qnty=0;
							$process_loss_method=0;
							foreach(array_unique(explode(',',$row[PO_ID])) as $poid){
								if($finish_qty[$poid][$row[GSM]][$row[COLOR_ID]]){
									$total_fin_fab_qnty+=$finish_qty[$poid][$row[GSM]][$row[COLOR_ID]];
									$total_grey_fab_qnty+=$gray_qty[$poid][$row[GSM]][$row[COLOR_ID]];
									$process_loss_method=$process_method[$poid][$row[GSM]][$row[COLOR_ID]];
									$color_type=$color_type_array_precost[$poid][$row[GSM]][$row[COLOR_ID]];
								}
							}
							//echo $process_loss_method;die;
							
							
							if($process_loss_method==1)
							{
								$process_percent=(($total_grey_fab_qnty-$total_fin_fab_qnty)/$total_fin_fab_qnty)*100;
							}
				
							if($process_loss_method==2)
							{
								$process_percent=(($total_grey_fab_qnty-$total_fin_fab_qnty)/$total_grey_fab_qnty)*100;
							}

						//echo $process_loss_method;die;
						 
						
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                            <td width="35" align="center"><? echo $i;?></td>
                            <td width="80"><p><? echo $buyer_arr[$row['BUYER_NAME']];?></p></td>
                            <td width="80"><p><? echo change_date_format($row['PRODUCTION_DATE']);?></p></td>
                            <td width="80"><p><? echo $machine_arr[$row['MACHINE_ID']];?></p></td>
                            <td width="80"><p><? echo $row['STYLE_REF_NO'];?></p></td>
                            <td width="80"><p><? echo $row['JOB_NO']; ?></p></td>
                            <td width="80"><p><? echo $row['BOOKING_NO'];?></p></td>
                            <td width="80" align="center"><?= $bookingType;?></td>
                            <td width="80"><p><? echo $color_library[$row[COLOR_ID]];?></p></td>
                            <td width="80"><p><? echo $row['CONST_COMPOSITION'];?></td>
                            <td width="80"><p><?= $color_type;?></p></td>
                            <td width="80"><? echo $row['BATCH_NO'];?></td>
                            <td width="80" align="center"><? echo $conversion_cost_head_array[$row['PROCESS_ID']];?></td>
                            <td width="80"><p><?= $row['LOT'];?></p></td>
                            <td width="80" align="center"><? echo $row['GSM'];?></td>
                            <td width="80" align="right"><? echo $fabric_typee[$row['WIDTH_DIA_TYPE']];?></td>
                            <td width="80" align="right"><? echo number_format($row['BATCH_QTY'],2);?></td>
                            <td width="80" align="right"><? echo number_format($row['PRODUCTION_QTY'],2);?></td>
                            <td width="80" align="right"><? echo number_format($balnce=$row['BATCH_QTY']-$row['PRODUCTION_QTY'],2);?></td>
                            <td width="80" align="center"><? echo $dyeing_result[$row['RESULT']];?></td>
                            <td width="80" align="right"><?= number_format($process_percent,2);?></td>
                            <td width="80" align="right"><?
							if($row['RESULT']==11){ 
								echo $actual_process_loss=$balnce*100/$row['BATCH_QTY'];
							}
							?>
							</td>
                            <td width="80" align="right">
                            <?
								if(($actual_process_loss>0) &&  ($actual_process_loss>$process_percent)){
									echo "Increase";
								}
								else{
									echo "Decrease";
								}
							?>
                            </td>
                            <td><? echo $row['REMARKS'];?></td>
                          </tr>									
                        <?
                        //-----------------------Sum-------------------------------
                        $buyer_batch_qty+=$row['BATCH_QTY'];
                        $buyer_production_qty+=$row['PRODUCTION_QTY'];
                        
                        $grand_batch_qty+=$row['BATCH_QTY'];
                        $grand_production_qty+=$row['PRODUCTION_QTY'];
                        
                        $i++;
                    	}
						?>
                        <tr bgcolor="#CCCCCC">
                        	<td colspan="14" align="right">Buyer Total:</td>
                        	<td></td>
                        	<td></td>
                        	<td align="right"><? echo number_format($buyer_batch_qty,2);?></td>
                        	<td align="right"><? echo number_format($buyer_production_qty,2);?></td>
                    		<td align="right"><? echo number_format($buyer_bal=$buyer_batch_qty-$buyer_production_qty,2);?></td>
                        	<td></td>
                        	<td align="right"><?= number_format(($buyer_bal*100/$buyer_batch_qty),2);?></td>
                        	<td></td>
                        	<td></td>
                        	<td></td>
                        </tr>
                        
                        <?
                        $buyer_batch_qty=0;
                        $buyer_production_qty=0;
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <table class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
            <tfoot>
               <tr>
                    <th colspan="13" align="right">Grand Total : </th>
                    <th width="80" align="right"><?  ?></th>
                    <th width="80" align="right"><?  ?></th>
                    <th width="80" align="right"><? echo number_format($grand_batch_qty,2);?></th>
                    <th width="80" align="right"><? echo number_format($grand_production_qty,2);?></th>
                    <th width="80" align="right"><? echo number_format($grand_bal=$grand_batch_qty-$grand_production_qty,2);?></th>
                    <th width="80"></th>
                    <th width="80"><?= number_format(($grand_bal*100/$grand_batch_qty),2);?></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="280"></th>
                </tr>
            </tfoot>
        </table>
       <? } ?>
        </div>
        <!--Subcontract....................................................-->
        <div style="margin-top:15px;">     
	   <? if($cbo_type==0 || $cbo_type==2){ ?>  
        <table class="rpt_table" width="<? echo $width2;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
            <thead>
            <tr>
                <td colspan="24" bgcolor="#CCCCCC"><strong>Subcontract</strong></td>
            </tr>
            <tr>
                <th width="35">SL</th>
                <th width="80">Party Name</th>
                <th width="80">Prod date</th>
                <th width="80">M/C no</th>
                <th width="80">Job No</th>
                <th width="80">Order No</th>
                <th width="80">Order Process</th>
                <th width="80">Color</th>
                <th width="80">Fabric Description</th>
                <th width="80">Batch</th>
                <th width="80">Process</th>
                <th width="80">Batch qty</th>
                <th width="80">Fin. Prod qty</th>
                <th width="80">Balance Qty</th>
                <th width="80">Fin Result</th>
                <th width="80">Actual Process Loss</th>
                <th>Remarks</th>
            </tr>
            </thead>
        </table>
        <div style="max-height:380px; width:<? echo $width2+18;?>px; overflow-y:scroll; float:left;" id="scroll_body">
            <table align="left" class="rpt_table" id="table_body" width="<? echo $width2;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tbody>
                    <?
                    $i=1;
                    
					$grand_batch_qty=0;
					$grand_production_qty=0;
					foreach($subDataArr as $buyer_id=>$rowArr)
                    {
						foreach($rowArr as $key=>$row)
						{
                        
                        $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $ii; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $ii; ?>">
                            <td width="35" align="center"><? echo $i;?></td>
                            <td width="80"><p><? echo $supplier_library[$row['BUYER_NAME']];?></p></td>
                            <td width="80"><p><? echo change_date_format($row['PRODUCTION_DATE']);?></p></td>
                            <td width="80"><p><? echo $machine_arr[$row['MACHINE_ID']];?></p></td>
                            <td width="80"><p><? echo $row['JOB_NO']; ?></p></td>
                            <td width="80"><p><? echo $row['ORDER_NO'];?></p></td>
                            <td width="80"><p><? echo $production_process[$row['MAIN_PROCESS_ID']];?></p></td>
                            <td width="80"><p><? echo $color_library[$row['COLOR_ID']];?></p></td>
                            <td width="80"><p><? echo $row['CONST_COMPOSITION'];?></p></td>
                            <td width="80"><? echo $row['BATCH_NO'];?></td>
                            <td width="80" align="center"><?= $conversion_cost_head_array[$process_id];?></td>
                            <td width="80" align="right"><? echo number_format($row['BATCH_QTY'],2);?></td>
                            <td width="80" align="right"><? echo number_format($row['PRODUCTION_QTY'],2);?></td>
                            <td width="80" align="right"><? echo number_format($blance=$row['BATCH_QTY']-$row['PRODUCTION_QTY'],2);?></td>
                            <td width="80" align="center"><? echo $dyeing_result[$row['RESULT']];?></td>
                            <td width="80" align="right">
                            <?
							
							
							if($row['RESULT']==11){
								echo $blance*100/$row['BATCH_QTY'];
							}	 
							?>
                            
                            </td>
                            <td><? echo $row['REMARKS'];?></td>
                          </tr>									
                        <?
                        //-----------------------Sum-------------------------------
                        $buyer_batch_qty+=$row['BATCH_QTY'];
                        $buyer_production_qty+=$row['PRODUCTION_QTY'];
                        $grand_batch_qty+=$row['BATCH_QTY'];
                        $grand_production_qty+=$row['PRODUCTION_QTY'];
                        
                        $i++;
                    	}
						?>
                        <tr bgcolor="#CCCCCC">
                        	<td colspan="11" align="right">Buyer Total:</td>
                        	<td align="right"><?= number_format($buyer_batch_qty,2);?></td>
                        	<td align="right"><?= number_format($buyer_production_qty,2);?></td>
                        	<td align="right"><?= number_format($buyer_bal=$buyer_batch_qty-$buyer_production_qty,2);?></td>
                        	<td></td>
                        	<td align="right"><?= number_format(($buyer_bal*100/$buyer_batch_qty),2);?></td>
                        	<td></td>
                        </tr>
                        
                        <?
                        $buyer_batch_qty=0;
                        $buyer_production_qty=0;
						
	   				}

                    ?>
                </tbody>
            </table>
        </div>
        <table class="rpt_table" width="<? echo $width2;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
            <tfoot>
               <tr>
                    <th colspan="11" align="right">Grand Total : </th>
                    <th width="80" align="right"><? echo number_format($grand_batch_qty,2);?></th>
                    <th width="80" align="right"><? echo number_format($grand_production_qty,2);?></th>
                    <th width="80" align="right"><? echo number_format($grand_blance=$grand_batch_qty-$grand_production_qty,2);?></th>
                    <th width="80" align="right"></th>
                    <th width="80"><? echo number_format(($grand_blance*100/ $grand_batch_qty),2);?></th>
                    <th width="247"></th>
                </tr>
            </tfoot>
        </table>
        <? } ?>
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
	?>