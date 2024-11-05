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
	$booking_number = trim(str_replace("'","",$booking_number));
	$cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
	$batch_number = trim(str_replace("'","",$batch_number));
	$batch_id = str_replace("'","",$batch_id);
	$cbo_floor_id = str_replace("'","",$cbo_floor_id);
	$cbo_machine_name = str_replace("'","",$cbo_machine_name);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);

	if($cbo_process_type==156){$whereCon.=" and a.entry_form=33";} //Compacting
	else if($cbo_process_type==63){$whereCon.=" and a.entry_form=30";} //Slitting/Squeezing
	else if($cbo_process_type==179){$whereCon.=" and a.entry_form=310";} //De-oiling
	else if($cbo_process_type==200){$whereCon.=" and a.entry_form=323";} //Dry Slitting
	else if($cbo_process_type==170){$whereCon.=" and a.entry_form=34";} //Special Finish
	else if($cbo_process_type==65){$whereCon.=" and a.entry_form=48";} //Stentering
	else if($cbo_process_type==171){$whereCon.=" and a.entry_form=31";} //Drying
	else if($cbo_process_type==94){$whereCon.=" and a.entry_form=47";} //Singeing
	else if($cbo_process_type==33){$whereCon.=" and a.entry_form=32";} //Heat Setting
	
	
	
	if($cbo_company_name) $whereCon.=" and a.COMPANY_ID=$cbo_company_name";
	if($batch_number) $whereCon.=" and a.batch_no like '%$batch_number'";
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
	
	
	
	$sql="select a.ID,a.MACHINE_ID,a.BATCH_ID,a.BATCH_NO,a.SHIFT_NAME,a.FLOOR_ID,a.REMARKS,a.BATCH_EXT_NO,b.GSM,b.DIA_WIDTH,b.CONST_COMPOSITION,b.BATCH_QTY,b.PRODUCTION_QTY,b.WIDTH_DIA_TYPE,c.BOOKING_NO,c.COLOR_ID,c.ENTRY_FORM,c.TOTAL_TRIMS_WEIGHT

from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst c 
where a.id=b.mst_id and c.id=a.batch_id and c.entry_form in(0,36) $whereCon 
ORDER BY b.id
	";// and a.PROCESS_ID='$cbo_process_type' and a.entry_form=30
	
	   //echo $sql;
	
	$sql_result = sql_select($sql);
	foreach( $sql_result as $row )
	{
		$key=$row[MACHINE_ID].'**'.$row[BOOKING_NO].'**'.$row[BATCH_NO].'**'.$row[BATCH_ID].'**'.$row[COLOR_ID];
		$key2=$row[BATCH_ID].'**'.$row[CONST_COMPOSITION].'**'.$row[GSM].'**'.$row[DIA_WIDTH].'**'.$row[WIDTH_DIA_TYPE];
		
		$qty_arr[BATCH_QTY][$key2]+=$row[BATCH_QTY];
		$qty_arr[PRODUCTION_QTY][$key2]+=$row[PRODUCTION_QTY];
		
		$qty_arr[TOTAL_TRIMS_WEIGHT][$key]=$row[TOTAL_TRIMS_WEIGHT];
		
		
		
		if($qty_arr[FIRST_REPORCESS_QTY][$key2]==''){
			$qty_arr[FIRST_REPORCESS_QTY][$key2]=$row[PRODUCTION_QTY];
		}
		
		if($row[REMARKS]){$remarks_arr[$key2][$row[REMARKS]]=$row[REMARKS];}
		$batch_id_arr[$row[ENTRY_FORM]][$row[BATCH_ID]]=$row[BATCH_ID];
		
		$dataArr[$key][$key2]=array(
			MACHINE_ID=>$row[MACHINE_ID],
			BOOKING_NO=>$row[BOOKING_NO],
			BATCH_NO=>$row[BATCH_NO],
			BATCH_ID=>$row[BATCH_ID],
			SHIFT_NAME=>$row[SHIFT_NAME],
			FLOOR_ID=>$row[FLOOR_ID],
			COLOR_ID=>$row[COLOR_ID],
			BATCH_EXT_NO=>$row[BATCH_EXT_NO],
			REMARKS=>implode(',',$remarks_arr[$key2]),
			CONST_COMPOSITION=>$row[CONST_COMPOSITION],
			GSM=>$row[GSM],
			DIA_WIDTH=>$row[DIA_WIDTH],
			BATCH_QTY=>$qty_arr[BATCH_QTY][$key2],
			PRODUCTION_QTY=>$qty_arr[PRODUCTION_QTY][$key2],
			REPORCESS_QTY=>$qty_arr[FIRST_REPORCESS_QTY][$key2],
		);

	}

	
	
	//SELF ORDER.......................................................
	if(count($batch_id_arr[0])>0){
		$batch_sql="SELECT b.mst_id as BATCH_ID,b.PO_ID from pro_batch_create_dtls b where b.mst_id in(".implode(',',$batch_id_arr[0]).") and b.status_active=1 group by b.mst_id,b.PO_ID";	
		$batch_sql_result = sql_select($batch_sql);
		foreach( $batch_sql_result as $row )
		{
			$batchDataArr['self_po'][$row[PO_ID]]=$row[PO_ID];
			$batchPoArr[$row[BATCH_ID]][$row[PO_ID]]=$row[PO_ID];
		}
		
		
		if($txt_job_no){$where_con_self=" and a.JOB_NO_MST like('%$txt_job_no')";$is_job_con=1;}
		if($cbo_buyer_name!=0){$where_con_self.=" and b.BUYER_NAME=$cbo_buyer_name";$is_buyer_con=1;}
		
		$result_job=sql_select("select a.id as PO_ID,a.PO_NUMBER,a.JOB_NO_MST, b.BUYER_NAME,B.STYLE_REF_NO from wo_po_break_down a, 
		wo_po_details_master b where a.job_no_mst=b.job_no and a.id in(".implode(',',$batchDataArr['self_po']).") and b.status_active=1 and b.is_deleted=0 and a.status_active=1 $where_con_self 
		and a.is_deleted=0");
		foreach( $result_job as $row )
		{
			$jobDataArr[JOB][$row[PO_ID]]=$row[JOB_NO_MST];
			$jobDataArr[BUYER][$row[PO_ID]]=$row[BUYER_NAME];
			$jobDataArr[PO][$row[PO_ID]]=$row[PO_NUMBER];
			$jobDataArr[STYLE][$row[PO_ID]]=$row[STYLE_REF_NO];
		}
	}
	

	//SUBCONTACT ORDER.............................................................
	if(count($batch_id_arr[36])>0){
		$batch_sql="SELECT b.mst_id as BATCH_ID,b.PO_ID from pro_batch_create_dtls b where b.mst_id in(".implode(',',$batch_id_arr[36]).") and b.status_active=1 group by b.mst_id,b.PO_ID";	
		$batch_sql_result = sql_select($batch_sql);
		foreach( $batch_sql_result as $row )
		{
			$batchDataArr['sub_po'][$row[PO_ID]]=$row[PO_ID];
			$batchPoArr[$row[BATCH_ID]][$row[PO_ID]]=$row[PO_ID];
		}
		
		if($txt_job_no){$where_con_sub=" and b.subcon_job like('%$txt_job_no')";$is_job_con=1;}
		if($cbo_buyer_name!=0){$where_con_sub.=" and b.party_id=$cbo_buyer_name";$is_buyer_con=1;}
		
		$result_job=sql_select("select a.id as PO_ID,a.ORDER_NO,b.subcon_job as JOB_NO_MST, b.party_id as BUYER_NAME from  subcon_ord_dtls a, 
		 subcon_ord_mst b where a.job_no_mst=b.subcon_job and a.id in(".implode(',',$batchDataArr['sub_po']).") and b.status_active=1 and b.is_deleted=0 and a.status_active=1  $where_con_sub
		and a.is_deleted=0 group by b.subcon_job, b.party_id");
		foreach( $result_job as $row )
		{
			$jobDataArr[JOB][$row[PO_ID]]=$row[JOB_NO_MST];
			$jobDataArr[BUYER][$row[PO_ID]]=$row[BUYER_NAME];
			$jobDataArr[PO][$row[PO_ID]]=$row[ORDER_NO];
			//$jobDataArr[STYLE][$row[PO_ID]]=$row[PO_ID];
		}
		
	}
		
	
		
		$width=1700;
		ob_start();
		if($batch_against==1 || $batch_against==3 )
		{
		?>
			<div>
				<fieldset style="width:<? echo $width;?>px;">
                    <table class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                        <tr>
                            <th colspan="20">
                            <caption>
                                <strong>
                                    <? echo $company_library[$cbo_company_name]; ?><br>Finish fabric WIP Report<br>
                                    <? echo change_date_format($txt_date_from).' To '.change_date_format($txt_date_to); ?>
                                </strong>
                            </caption>
                            </th>
                        </tr>
                        <thead>
                            <th width="35">SL</th>
                            <th width="80">M/C No</th>
                            <th width="80">Buyer</th>
                            <th width="80">Booking No</th>
                            <th width="80">Job No</th>
                            <th width="80">Order No</th>
                            <th width="80">Style</th>
                            <th width="80">Batch No</th>
                            <th width="80">Extn. No</th>
                            <th width="80">Color Name</th>
                            <th>Fabric Type</th>
                            <th width="40">GSM</th>
                            <th width="40">F/Dia</th>
                            <th width="80">Batch Qty.</th>
                            <th width="80">Prod. Qty.</th>
                            <th width="80" title="Reprocess Qty (Without 1 st Time)">Reprocess Qty</th>
                            <th width="80">Trims Qty</th>
                            <th width="40">Shift</th>
                            <th width="80">Floor</th>
                            <th width="200">Remark</th>
                        </thead>
					</table>
					<div style="max-height:380px; width:<? echo $width+18;?>px; overflow-y:scroll;" id="scroll_body">
						<table align="left" class="rpt_table" id="table_body" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
							<tbody>
								<?
								$i=1;$ii=1;
								foreach($dataArr as $key=>$rowArr)
								{
									$rowspan=count($rowArr);
									$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
									
									list($row[MACHINE_ID],$row[BOOKING_NO],$row[BATCH_NO],$row[BATCH_ID],$row[COLOR_ID])=explode('**',$key);
									
										$buyerArr=array();$jobArr=array();$poArr=array();$styleArr=array();
										foreach($batchPoArr[$row[BATCH_ID]] as $po_id){
											if($jobDataArr[BUYER][$po_id]){
												$buyerArr[$jobDataArr[BUYER][$po_id]]= $buyer_arr[$jobDataArr[BUYER][$po_id]];
											}
											if($jobDataArr[JOB][$po_id]){
												$jobArr[$jobDataArr[JOB][$po_id]]= $jobDataArr[JOB][$po_id];
											}
											$poArr[$jobDataArr[PO][$po_id]]= $jobDataArr[PO][$po_id];
											$styleArr[$jobDataArr[STYLE][$po_id]]= $jobDataArr[STYLE][$po_id];
										}
								
								
									if($is_job_con==1 && count($jobArr)<1){
										continue;
									}
									else if($is_buyer_con==1 && count($buyerArr)<1){
										continue;
									}
								
								
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $ii; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $ii; ?>">
                                    <td rowspan="<? echo $rowspan;?>" width="35" align="center"><? echo $i;?></td>
									<td rowspan="<? echo $rowspan;?>" width="80"><p><? echo $machine_arr[$row['MACHINE_ID']];?></p></td>
									<td rowspan="<? echo $rowspan;?>" width="80"><p><? echo implode(',',$buyerArr);?></p></td>
									<td rowspan="<? echo $rowspan;?>" width="80"><p><? echo $row['BOOKING_NO'];?></p></td>
									<td rowspan="<? echo $rowspan;?>" width="80"><p><? echo implode(',',$jobArr);?></p></td>
									<td rowspan="<? echo $rowspan;?>" width="80"><p><? echo implode(',',$poArr);?></p></td>
									<td rowspan="<? echo $rowspan;?>" width="80"><p><? echo implode(',',$styleArr);?></p></td>
									<td rowspan="<? echo $rowspan;?>" width="80"><p><? echo $row['BATCH_NO'];?></p></td>
									<td rowspan="<? echo $rowspan;?>" width="80"><? echo $row['BATCH_EXT_NO'];?></td>
									<td rowspan="<? echo $rowspan;?>" width="80"><p><? echo $color_library[$row[COLOR_ID]];?></p></td>
									
									<?
									$flag=0;
									foreach($rowArr as $key2=>$row)
									{
										$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
										if($flag==1){?>
                                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $ii; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $ii; ?>">
										<?
                                          }
									?>
                                        <td><p><? echo $row['CONST_COMPOSITION'];?></p></td>
                                        <td width="40" align="center"><? echo $row['GSM'];?></td>
                                        <td width="40" align="center"><? echo $row['DIA_WIDTH'];?></td>
                                        <td width="80" align="right"><? echo $row['BATCH_QTY'];?></td>
                                        <td width="80" align="right"><? echo $row['PRODUCTION_QTY'];?></td>
                                        <td width="80" align="right"><? echo ($row['PRODUCTION_QTY']-$qty_arr['FIRST_REPORCESS_QTY'][$key2]);?></td>
                                        <? if($flag==0){
											$grand_trims_qty+=$qty_arr[TOTAL_TRIMS_WEIGHT][$key];	
										?>
                                        <td rowspan="<? echo $rowspan;?>" width="80" align="right"><? echo $qty_arr[TOTAL_TRIMS_WEIGHT][$key];?></td>
                                        <td rowspan="<? echo $rowspan;?>" width="40" align="center"><? echo $shift_name[$row['SHIFT_NAME']];?></td>
                                        <td rowspan="<? echo $rowspan;?>" width="80"><p><? echo $floor_arr[$row['FLOOR_ID']];?></p></td>
										<? } ?>
                                        <td width="200"><p><? echo $row['REMARKS'];?></p></td>
                                        
                                    </tr>									
									<?
									//-----------------------Sum-------------------------------
									$grand_batch_qty+=$row['BATCH_QTY'];
									$grand_production_qty+=$row['PRODUCTION_QTY'];
									$grand_re_process_qty+=($row['PRODUCTION_QTY']-$qty_arr['FIRST_REPORCESS_QTY'][$key2]);
									
									
									$flag=1;
									$ii++;	
								}
									$i++;
								}
								?>
							</tbody>
						</table>
					</div>
					<table class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                        <tfoot>
<!--                            <tr>
                                <th colspan="13" align="right">Batch Total : </th>
                                <th width="80">Batch Qty.</th>
                                <th width="80">Prod. Qty.</th>
                                <th width="80">Rep Qty</th>
                                <th width="80">Trims Qty</th>
                                <th width="40"></th>
                                <th width="80"></th>
                                <th width="200"></th>
                            </tr>
                            <tr>
                                <th colspan="13" align="right">Machine Total : </th>
                                <th width="80">Batch Qty.</th>
                                <th width="80">Prod. Qty.</th>
                                <th width="80">Repr Qty</th>
                                <th width="80">Trims Qty</th>
                                <th width="40"></th>
                                <th width="80"></th>
                                <th width="200"></th>
                            </tr>
                            <tr>
                                <th colspan="13" align="right">Booking Total : </th>
                                <th width="80">Batch Qty.</th>
                                <th width="80">Prod. Qty.</th>
                                <th width="80">Rep Qty</th>
                                <th width="80">Trims Qty</th>
                                <th width="40"></th>
                                <th width="80"></th>
                                <th width="200"></th>
                            </tr>
-->                            <tr>
                                <th colspan="13" align="right">Grand Total : </th>
                                <th width="80" align="right"><? echo $grand_batch_qty;?></th>
                                <th width="80" align="right"><? echo $grand_production_qty;?></th>
                                <th width="80" align="right"><? echo $grand_re_process_qty;?></th>
                                <th width="80" align="right"><? echo $grand_trims_qty;?></th>
                                <th width="40"></th>
                                <th width="80"></th>
                                <th width="200"></th>
                            </tr>
                        </tfoot>
					</table>
				</fieldset>
			</div>
			<?
			foreach (glob("$user_id*.xls") as $filename)
			{
				@unlink($filename);
			}
		//---------end------------//
			$filename=$user_id.'_'.time().".xls";
			$create_new_doc = fopen($filename, 'w');
			$fdata=ob_get_contents();
			fwrite($create_new_doc,$fdata);
			ob_end_clean();
			echo "$fdata****$filename****$type";
			exit();
		}
	}
	?>