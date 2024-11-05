<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

 include ("../../../../ext_resource/excel/excel/vendor/autoload.php");
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color; 


if($action=="load_drop_down_buyer")
{
	$party="1,3,21,90";
	echo create_drop_down("cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in ($data) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) group by buy.id, buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
	exit();
}

if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 120, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id  and a.status_active=1 and a.is_deleted=0 and a.company_id in ($data) and  b.category_type=2 group by a.id,a.store_name order by a.store_name","id,store_name", 1, "--Select Store--", 1, "",0 );
	exit();
}

if ($action=="load_drop_down_supplier")
{
	$dataArr = explode("_",$data);
	if($dataArr[0]==5 || $dataArr[0]==3)
	{
		echo create_drop_down( "cbo_supplier_id", 100, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- All Supplier --", "", "",0,"" );
	}
	else
	{
		echo create_drop_down( "cbo_supplier_id", 100, "select DISTINCT(c.id),c.supplier_name from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$dataArr[1]' and b.party_type in(1,9) order by id,supplier_name","id,supplier_name", 1, "-- All Supplier --", $selected, "",0 );
	}
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value(id)
		{
			var str=id.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			var strdt=str[2];
			str=str[1];

			if( jQuery.inArray(  str , selected_id ) == -1 ) {
				selected_id.push( str );
				selected_name.push( strdt );
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i,1 );
			}
			var id = '';
			var ddd='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( ddd );
		}

	</script>
</head>
<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:580px;">
				<table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Buyer</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Job No</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
						<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
								?>
							</td>
							<td align="center">
								<?
								$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Internal Ref");
								$dd="change_search_event(this.value, '0*0*0', '0*0', '../../') ";
								echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>', 'create_job_no_search_list_view', 'search_div', 'batch_and_store_wise_finish_fabric_stock_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$data[1]";
	}

	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if ($search_by==1) 
	{
		$search_field="a.job_no";
	}
	else if ($search_by==2) 
	{
		$search_field="a.style_ref_no";
	}
	else
	{
		$search_field="b.grouping";
	}
	//$year="year(insert_date)";
	if($db_type==0) $year_field_by="year(a.insert_date) as year ";
	else if($db_type==2) $year_field_by="to_char(a.insert_date,'YYYY') as year ";
	if($db_type==0) $month_field_by="and month(a.insert_date)";
	else if($db_type==2) $month_field_by="and to_char(a.insert_date,'MM')";
	if($db_type==0) $year_field="and year(a.insert_date)=$year_id";
	else if($db_type==2) $year_field="and to_char(a.insert_date,'YYYY')";

	if($db_type==0)
	{
		if($year_id==0)$year_cond=""; else $year_cond="and year(a.insert_date)='$year_id'";
	}
	else if($db_type==2)
	{
		if($year_id==0)$year_cond=""; else $year_cond="and to_char(a.insert_date,'YYYY')='$year_id'";
	}
	else $year_cond="";

	$arr=array (0=>$company_arr,1=>$buyer_arr);

	$sql= "SELECT a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $year_field_by from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond group by a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, a.insert_date order by a.job_no";

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","620","270",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;
	exit();
}

if ($action=="booking_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	$company_id=$data[0];
	$buyer_id=$data[1];
	$year_id=$data[2];

	?>
	<script>
		function js_set_value(booking_no)
		{
			document.getElementById('selected_booking').value=booking_no;
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="750" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
				<tr>
					<td align="center" width="100%">
						<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
							<thead>
								<th width="150">Buyer Name</th>
								<th width="150">Booking No</th>
								<th width="200">Date Range</th>
								<th></th>
							</thead>
							<tr>
								<input type="hidden" id="selected_booking">
								<td>
									<?
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_id,"",0 );
									?>
								</td>
								<td>
									<input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:150px">
								</td>
								<td>
									<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
									<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
								</td>
								<td align="center">
									<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $company_id; ?>'+'_'+document.getElementById('txt_booking_no').value,'create_booking_search_list_view', 'search_div', 'batch_and_store_wise_finish_fabric_stock_report_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td  align="center" height="40" valign="middle">
							<?
							echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );
							?>
							<? echo load_month_buttons();  ?>
						</td>
					</tr>
					<tr>
						<td align="center" valign="top" id="search_div">
						</td>
					</tr>
				</table>
			</form>
		</div>
	</body>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	$company=$data[3];
	$booking_no=$data[4];

	if($db_type==0)
	{
		if ($data[1]!="" &&  $data[2]!="") $booking_date  = "and booking_date  between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
		if ($data[1]!="" &&  $data[2]!="") $booking_date  = "and booking_date  between '".change_date_format($data[1],'','',1)."' and '".change_date_format($data[2],'','',1)."'"; else $booking_date ="";
	}
	$po_array=array();
	$sql_po= sql_select("select booking_no,po_break_down_id from wo_booking_mst  where company_id='$company' $buyer $booking_date and booking_type=1 and is_short=2 and   status_active=1  and is_deleted=0 order by booking_no");
	foreach($sql_po as $row)
	{
		$po_id=explode(",",$row[csf("po_break_down_id")]);
		$po_number_string="";
		foreach($po_id as $key=> $value )
		{
			$po_number_string.=$order_arr[$value].",";
		}
		$po_array[$row[csf("po_break_down_id")]]=rtrim($po_number_string,",");
	}
	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$po_num=return_library_array( "select job_no, job_no_prefix_num from wo_po_details_master",'job_no','job_no_prefix_num');
	$arr=array (2=>$comp,3=>$buyer_arr,4=>$po_num,5=>$po_array,6=>$item_category,7=>$fabric_source,8=>$suplier,9=>$approved,10=>$is_ready);
	$booking_cond = ($booking_no!="")?" and booking_no_prefix_num=$booking_no":"";
	$sql= "select booking_no_prefix_num, booking_no,booking_date,company_id,buyer_id,job_no,po_break_down_id,item_category,fabric_source,supplier_id,is_approved,ready_to_approved from wo_booking_mst  where company_id=$company $buyer $booking_date $booking_cond and booking_type=1 and is_short in(1,2) and  status_active=1  and 	is_deleted=0 order by booking_no";
	echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No.,PO number,Fabric Nature,Fabric Source,Supplier,Approved,Is-Ready", "80,80,70,100,90,200,80,80,50,50","1020","320",0, $sql , "js_set_value", "booking_no_prefix_num", "", 1, "0,0,company_id,buyer_id,job_no,po_break_down_id,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", $arr , "booking_no_prefix_num,booking_date,company_id,buyer_id,job_no,po_break_down_id,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", '','','0,0,0,0,0,0,0,0,0,0,0','','');

	exit();
}



if($action=="pinumber_popup")
{
	echo load_html_head_contents("PI Number Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(str)
		{
			var splitData = str.split("_");
			$("#pi_id").val(splitData[0]);
			$("#pi_no").val(splitData[1]);
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center" style="width:100%; margin-top:5px" >
		<form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
			<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>
						<th>Supplier</th>
						<th>Search By</th>
						<th id="search_by_td_up">Enter PI Number</th>
						<th>Date Range</th>
						<th>
							<input type="reset" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('searchlcfrm_1','search_div','','','','');" />
							<input type="hidden" id="pi_id" value="" />
							<input type="hidden" id="pi_no" value="" />
						</th>
					</tr>
				</thead>
				<tbody>
					<tr align="center">
						<td>
							<?
							$sql_supplier = "select DISTINCT(c.id),c.supplier_name from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyID' and b.party_type in(1,9) order by id,supplier_name";
							echo create_drop_down( "cbo_supplier_id", 130,"$sql_supplier",'id,supplier_name', 1, '-- All Supplier --',0,'',0);
							?>
						</td>

						<td align="center">
							<?
							$search_by_arr=array(1=>"PI No",2=>"LC No");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:100px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
						</td>

						<td align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;" placeholder="From Date" readonly />
							To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;" placeholder="To Date" readonly />
						</td>
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_search_by').value, 'create_pi_search_list_view', 'search_div', 'batch_and_store_wise_finish_fabric_stock_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="5" align="center"><? echo load_month_buttons(1); ?></td>
					</tr>
				</tbody>
			</table>
			<div align="center" style="margin-top:10px" id="search_div"> </div>
		</form>
	</div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?

}


if($action=="create_pi_search_list_view")
{
	$ex_data = explode("_",$data);

	if($ex_data[0]==0) $cbo_supplier = "%%"; else $cbo_supplier = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$company = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	$search_type = $ex_data[5];
	$pi_date_cond="";
	if( $from_date!="" && $to_date!="")
	{
		if($db_type==0)
		{
			$pi_date_cond= " and a.pi_date between '".change_date_format($from_date,"yyyy-mm-dd")."' and '".change_date_format($to_date,"yyyy-mm-dd")."'";
		}
		else
		{
			$pi_date_cond= " and a.pi_date between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";
		}
	}

	if($search_type == 1)
	{
		$sql= "select id, pi_number, supplier_id, importer_id, pi_date, last_shipment_date, total_amount from com_pi_master_details where importer_id=$company and entry_form=166 and supplier_id like '$cbo_supplier' and pi_number like '%".$txt_search_common."%' and is_deleted=0 and status_active=1 $pi_date_cond";
	}else{
		$sql= "select  a.id, a.pi_number, a.supplier_id, a.importer_id, a.pi_date, a.last_shipment_date, a.total_amount from  com_pi_master_details a, com_btb_lc_master_details b, com_btb_lc_pi c where b.id = c.com_btb_lc_master_details_id and c.pi_id = a.id and a.importer_id =$company and a.entry_form = 166 and a.supplier_id like '$cbo_supplier' and b.lc_number like '%".$txt_search_common."%' $pi_date_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 group by  a.id, a.pi_number, a.supplier_id, a.importer_id, a.pi_date, a.last_shipment_date, a.total_amount";
	}

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr 	= return_library_array("select id,short_name from lib_supplier where status_active=1","id","short_name");

	$arr=array(1=>$company_arr,2=>$supplier_arr);
	echo create_list_view("list_view", "PI No, Importer, Supplier Name, PI Date, Last Shipment Date, PI Value","130,110,130,90,130","780","260",0, $sql , "js_set_value", "id,pi_number", "", 1, "0,importer_id,supplier_id,0,0,0,0", $arr, "pi_number,importer_id,supplier_id,pi_date,last_shipment_date,total_amount", "",'','0,0,0,3,3,2') ;
	exit();
}


if($action=="report_generate")
{
	$started = microtime(true);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$report_type 		= str_replace("'","",$cbo_report_type);
	$buyer_id 			= str_replace("'","",$cbo_buyer_id);
	$book_no 			= trim(str_replace("'","",$txt_book_no));
	$book_id 			= str_replace("'","",$txt_book_id);
	$job_no 			= trim(str_replace("'","",$txt_job_no));
	$txt_pi_no 			= trim(str_replace("'","",$txt_pi_no));
	$hdn_pi_id 			= trim(str_replace("'","",$hdn_pi_id));
	$txt_batch_no 		= trim(str_replace("'","",$txt_batch_no));

	$txt_file_no 		= str_replace("'","",$txt_file_no);
	$txt_ref_no 		= str_replace("'","",$txt_ref_no);
	$job_year 			= str_replace("'","",$cbo_year);
	$cbo_company_id 	= str_replace("'","",$cbo_company_id);
	$cbo_pay_mode 		= str_replace("'","",$cbo_pay_mode);
	$cbo_supplier_id 	= str_replace("'","",$cbo_supplier_id);
	$cbo_store_name 	= str_replace("'","",$cbo_store_name);
	$date_from 		 	= str_replace("'","",$txt_date_from);
	$date_to 		 	= str_replace("'","",$txt_date_to);
	$cbo_value_with 	= str_replace("'","",$cbo_value_with);

	$get_upto 			= str_replace("'","",$cbo_get_upto);
	$txt_days 			= str_replace("'","",$txt_days);
	$get_upto_qnty 		= str_replace("'","",$cbo_get_upto_qnty);
	$txt_qnty 			= str_replace("'","",$txt_qnty);

	if($cbo_store_name > 0){
		$store_cond = " and b.store_id in ($cbo_store_name)";
		$store_cond_2 = " and c.store_id in ($cbo_store_name)";
	}

	if($txt_batch_no)
	{
		$batch_cond = " and e.batch_no like '%$txt_batch_no%'";
	}

	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and d.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and d.buyer_id=$buyer_id";
	}

	if($db_type==0)
	{
		if($job_year==0) $year_cond=""; else $year_cond=" and YEAR(f.insert_date)=$job_year";
	}
	else if($db_type==2)
	{
		if($job_year==0) $year_cond=""; else $year_cond=" and to_char(f.insert_date,'YYYY')=$job_year";
	}

	$date_cond="";
	if($date_from!="" && $date_to!="")
	{
		if($db_type==0)$start_date=change_date_format($date_from,"yyyy-mm-dd","");
		else if($db_type==2) $start_date=change_date_format($date_from,"","",1);

		if($db_type==0)$end_date=change_date_format($date_to,"yyyy-mm-dd","");
		else if($db_type==2) $end_date=change_date_format($date_to,"","",1);

		$date_cond   = " and b.transaction_date <= '$end_date'";
		$date_cond_2 = " and c.transaction_date <= '$end_date'";
		$date_cond_3 = " and a.transaction_date <= '$end_date'";
	}

	$company_arr 	= return_library_array("select id, company_name from lib_company where status_active=1","id","company_name");
	$supplier_arr 	= return_library_array("select id,short_name from lib_supplier where status_active=1","id","short_name");
	$buyer_arr 		= return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$season_arr 	= return_library_array( "select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0",'id','season_name');
	$store_arr 		= return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$color_arr 		= return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$booking_type_arr = array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
	$conversion_rate=return_field_value("conversion_rate","currency_conversion_rate","is_deleted=0 and status_active=1 and id=(select max(id) from currency_conversion_rate where currency=2 and is_deleted=0 and status_active=1 and company_id in ($cbo_company_id) )","",$con);

	$pi_no_cond="";
	if ($hdn_pi_id=="")
	{
		$pi_no_cond="";
	}
	else
	{
		$pi_no_cond=" and a.booking_id = '$hdn_pi_id' and a.receive_basis=1 ";
		$pi_no_trans_cond = " and a.id = 0";
	}

	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and f.job_no_prefix_num in ($job_no) ";
	if ($book_no=="") $booking_no_cond=""; else $booking_no_cond=" and d.booking_no_prefix_num='$book_no'";
	if($cbo_supplier_id ==0) $supplier_cond = ""; else $supplier_cond = " and d.supplier_id = ".$cbo_supplier_id;
	if($cbo_pay_mode ==0) $pay_mode_cond = ""; else $pay_mode_cond = " and d.pay_mode = ".$cbo_pay_mode;

	if($job_no != "" || $book_no!="" || $cbo_supplier_id !=0 || $buyer_id!=0 || $cbo_pay_mode !=0)
	{
		$serch_ref_sql_1 = "select c.booking_no from wo_booking_dtls c, wo_booking_mst d, wo_po_break_down e, wo_po_details_master f where c.status_active=1 and e.status_active=1 and f.job_no=e.job_no_mst and c.booking_type in (1,4,3) and c.booking_no=d.booking_no and c.po_break_down_id=e.id and f.company_name in ($cbo_company_id) $buyer_id_cond $job_no_cond $booking_no_cond $year_cond $pay_mode_cond $supplier_cond ";

		$concate="";
		if($job_no == "")
		{
			$concate = " union all ";
			$serch_ref_sql_2 = " select d.booking_no from wo_non_ord_samp_booking_mst d where d.booking_type = 4 and d.company_id in ($cbo_company_id) $booking_no_cond $pay_mode_cond $supplier_cond $buyer_id_cond ";
		}
		$serch_ref_sql = $serch_ref_sql_1.$concate.$serch_ref_sql_2;

		$serch_ref_result = sql_select($serch_ref_sql);

		foreach ($serch_ref_result as $val)
		{
			$search_book_arr[$val[csf("booking_no")]] = $val[csf("booking_no")];
		}

		if(empty($search_book_arr))
		{
			echo "<p style='font-weight:bold;text-align:center;font-size:20px;'>Booking No not found</p>";
			die;
		}
	}

	if(!empty($search_book_arr))
	{
		$search_book_nos="'".implode("','",$search_book_arr)."'";
		$search_book_arr = explode(",", $search_book_nos);

		$all_book_nos_cond=""; $bookCond="";
		if($db_type==2 && count($search_book_arr)>999)
		{
			$all_search_book_arr_chunk=array_chunk($search_book_arr,999) ;
			foreach($all_search_book_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$bookCond.="  e.booking_no in($chunk_arr_value) or ";
			}

			$all_book_nos_cond.=" and (".chop($bookCond,'or ').")";
		}
		else
		{
			$all_book_nos_cond=" and e.booking_no in($search_book_nos)";
		}
	}

	$rcv_sql = "SELECT b.id as ID, e.booking_no as BOOKING_NO, e.booking_no_id as BOOKING_NO_ID, e.booking_without_order as BOOKING_WITHOUT_ORDER, a.company_id as COMPANY_ID,a.receive_basis as  RECEIVE_BASIS, a.knitting_source as KNITTING_SOURCE, a.knitting_company as KNITTING_COMPANY,a.booking_id as WO_PI_PROD_ID,a.booking_no as WO_PI_PROD_NO, b.transaction_date as TRANSACTION_DATE, b.prod_id as PROD_ID, b.store_id as STORE_ID, c.body_part_id as BODY_PART_ID, c.fabric_description_id as FABRIC_DESCRIPTION_ID, c.gsm as GSM, c.width as WIDTH, f.color as COLOR_ID, b.cons_uom as CONS_UOM,listagg(c.dia_width_type,',') within group (order by c.dia_width_type) as DIA_WIDTH_TYPE, listagg(d.po_breakdown_id,',') within group (order by d.po_breakdown_id) as PO_BREAKDOWN_ID, b.cons_quantity as QUANTITY, b.order_rate as ORDER_RATE, b.order_amount as ORDER_AMOUNT, b.pi_wo_batch_no as PI_WO_BATCH_NO, a.lc_sc_no as LC_SC_NO, e.batch_no as BATCH_NO, a.ENTRY_FORM
	FROM inv_receive_master a, inv_transaction b, pro_finish_fabric_rcv_dtls c left join order_wise_pro_details d on c.trans_id = d.trans_id and c.id = d.dtls_id and entry_form in(7,37) and d.po_breakdown_id <>0 and d.trans_id <>0, pro_batch_create_mst e, product_details_master f
	WHERE a.company_id in ($cbo_company_id) and a.id = b.mst_id and b.id=c.trans_id and b.transaction_type=1 and a.entry_form in(7,37) and c.trans_id <>0 and a.status_active =1 and b.status_active =1 and c.is_sales=0 and c.status_active =1 and b.pi_wo_batch_no=e.id and b.prod_id = f.id $store_cond $date_cond  $all_book_nos_cond $pi_no_cond $batch_cond
	group by b.id,e.booking_no,e.booking_no_id, e.booking_without_order, a.company_id,a.receive_basis, a.knitting_source,a.knitting_company, a.booking_id, a.booking_no, b.transaction_date, b.prod_id, b.store_id, c.body_part_id, c.fabric_description_id, c.gsm, c.width, f.color ,b.cons_uom,c.dia_width_type,b.cons_quantity, b.order_rate, b.order_amount, b.pi_wo_batch_no, a.lc_sc_no, e.batch_no, a.ENTRY_FORM order by a.company_id, b.pi_wo_batch_no"; 
	//echo $rcv_sql;
	$rcv_data = sql_select($rcv_sql);
	foreach ($rcv_data as  $val)
	{
		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val['TRANSACTION_DATE']));
		$ref_str="";
		$dia_width_type_ref = implode(",",array_unique(explode(",", $val["DIA_WIDTH_TYPE"])));

		$ref_str = $val["PROD_ID"]."*".$val["STORE_ID"]."*".$val["BODY_PART_ID"]."*".$val["FABRIC_DESCRIPTION_ID"]."*".$val["GSM"]."*".$val["WIDTH"]."*".$val["COLOR_ID"]."*".$val["CONS_UOM"]."*".$val["PI_WO_BATCH_NO"]."*".$val["BATCH_NO"];
		
		$rate_usd=$order_amount=0;
		if ($val["ENTRY_FORM"]==7) 
		{
			$rate_usd=$val["ORDER_RATE"]/$conversion_rate; // rate for usd
			$order_amount=$val["ORDER_AMOUNT"]/$conversion_rate; // for usd
		}
		else
		{
			$rate_usd=$val["ORDER_RATE"];
			$order_amount=$val["ORDER_AMOUNT"];
		}
		if($transaction_date >= $date_frm)
		{
			$data_array[$val["CONS_UOM"]][$val["BOOKING_NO"]][$ref_str] .= $val["QUANTITY"]."*".$rate_usd."*".$val["RECEIVE_BASIS"]."*".$val["WO_PI_PROD_NO"]."*".$dia_width_type_ref."*".$val["LC_SC_NO"]."*"."1*1__";
		}
		else
		{
			$data_array[$val["CONS_UOM"]][$val["BOOKING_NO"]][$ref_str] .= $val["QUANTITY"]."*".$rate_usd."*".$val["RECEIVE_BASIS"]."*".$val["WO_PI_PROD_NO"]."*".$dia_width_type_ref."*".$val["LC_SC_NO"]."*"."1*2__";
		}
		$all_prod_id[$val["PROD_ID"]] = $val["PROD_ID"];

		if($val["BOOKING_WITHOUT_ORDER"] == 0)
		{
			$all_po_id_arr[$val["PO_BREAKDOWN_ID"]] = $val["PO_BREAKDOWN_ID"];
			$po_array[$val["BOOKING_NO"]][$ref_str]["po_no"] .= $val["PO_BREAKDOWN_ID"].",";
		}

		$book_str = explode("-", $val["BOOKING_NO"]);
		if($val["BOOKING_WITHOUT_ORDER"] == 1 || $book_str[1] == "SMN")
		{
			$all_samp_book_arr[$val["BOOKING_NO"]] = "'".$val["BOOKING_NO"]."'";
		}
		$booking_no_arr[$val["BOOKING_NO"]] = "'".$val["BOOKING_NO"]."'";
		$batch_id_arr[$val["PI_WO_BATCH_NO"]] = $val["PI_WO_BATCH_NO"];

		$rate_arr_booking_and_product_wise[$val["BOOKING_NO"]][$val["PROD_ID"]][$val["STORE_ID"]]["quantity"] += $val["QUANTITY"];
		$rate_arr_booking_and_product_wise[$val["BOOKING_NO"]][$val["PROD_ID"]][$val["STORE_ID"]]["amount"] += $order_amount;
		
	}
	unset($rcv_data);
	/* echo "<pre>";
	print_r($data_array);die; */

	if ($hdn_pi_id=="")
	{
		$trans_in_sql = "SELECT c.transaction_date as TRANSACTION_DATE, c.pi_wo_batch_no as PI_WO_BATCH_NO, e.batch_no as BATCH_NO, e.booking_no as BOOKING_NO, e.booking_no_id as BOOKING_NO_ID, e.booking_without_order as BOOKING_WITHOUT_ORDER, c.body_part_id as BODY_PART_ID, c.prod_id as PROD_ID, c.store_id as STORE_ID, d.detarmination_id as DETARMINATION_ID, d.gsm as GSM, d.dia_width as WIDTH, d.color as COLOR_ID, c.cons_uom as  CONS_UOM, sum(c.cons_quantity) as QUANTITY, c.order_rate as ORDER_RATE, c.order_amount as ORDER_AMOUNT, listagg(f.po_breakdown_id,',') within group (order by f.po_breakdown_id) as PO_BREAKDOWN_ID, b.batch_id as BATCH_ID, b.from_store as FROM_STORE, b.from_prod_id as FROM_PROD_ID
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c left join order_wise_pro_details f on c.id = f.trans_id and f.trans_type=5 and f.status_active=1 and f.po_breakdown_id<>0, product_details_master d, pro_batch_create_mst e
		where a.id=b.mst_id and b.to_trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($cbo_company_id) and c.item_category=2 and c.transaction_type=5 and a.status_active=1 and b.status_active=1 and c.status_active=1  and a.entry_form in (14,15,306) $store_cond_2 $date_cond_2 $all_book_nos_cond $batch_cond
		group by c.transaction_date, c.pi_wo_batch_no, e.batch_no, e.booking_no, e.booking_no_id, e.booking_without_order, c.company_id, c.body_part_id, c.prod_id,c.store_id, d.detarmination_id, d.gsm, d.dia_width, d.color, c.cons_uom, c.order_rate, c.order_amount, b.batch_id, b.from_store, b.from_prod_id order by c.company_id, c.pi_wo_batch_no";
		//echo $trans_in_sql;
		$trans_in_data = sql_select($trans_in_sql);
		foreach ($trans_in_data as  $val)
		{
			$date_frm=date('Y-m-d',strtotime($start_date));
			$transaction_date=date('Y-m-d',strtotime($val['TRANSACTION_DATE']));
			$ref_str="";

			$ref_str = $val["PROD_ID"]."*".$val["STORE_ID"]."*".$val["BODY_PART_ID"]."*".$val["DETARMINATION_ID"]."*".$val["GSM"]."*".$val["WIDTH"]."*".$val["COLOR_ID"]."*".$val["CONS_UOM"]."*".$val["PI_WO_BATCH_NO"]."*".$val["BATCH_NO"];

			if($transaction_date >= $date_frm)
			{
				$data_array[$val["CONS_UOM"]][$val["BOOKING_NO"]][$ref_str] .= $val["QUANTITY"]."*".$val["ORDER_RATE"]."*"."*".""."*".""."*"."*5*1__";
			}
			else
			{
				$data_array[$val["CONS_UOM"]][$val["BOOKING_NO"]][$ref_str] .= $val["QUANTITY"]."*".$val["ORDER_RATE"]."*"."*".""."*".""."*"."*5*2__";
			}

			$all_prod_id[$val["PROD_ID"]] = $val["PROD_ID"];

			if($val["BOOKING_WITHOUT_ORDER"] == 0)
			{
				$all_po_id_arr[$val["PO_BREAKDOWN_ID"]] = $val["PO_BREAKDOWN_ID"];
				$po_array[$val["BOOKING_NO"]][$ref_str]["po_no"] .= $val["PO_BREAKDOWN_ID"].",";
			}

			$book_str = explode("-", $val["BOOKING_NO"]);
			if($val["BOOKING_WITHOUT_ORDER"] == 1 || $book_str[1] == "SMN")
			{
				$all_samp_book_arr[$val["BOOKING_NO"]] = "'".$val["BOOKING_NO"]."'";
			}
			$booking_no_arr[$val["BOOKING_NO"]] = "'".$val["BOOKING_NO"]."'";
			$batch_id_arr[$val["PI_WO_BATCH_NO"]] = $val["PI_WO_BATCH_NO"];

			$rate_arr_booking_and_product_wise[$val["BOOKING_NO"]][$val["PROD_ID"]][$val["STORE_ID"]]["quantity"] += $val["QUANTITY"];
			$rate_arr_booking_and_product_wise[$val["BOOKING_NO"]][$val["PROD_ID"]][$val["STORE_ID"]]["amount"] += $val["ORDER_AMOUNT"];

			if($rate_arr_booking_and_product_wise[$val["BOOKING_NO"]][$val["PROD_ID"]][$val["STORE_ID"]]["amount"]*1 ==0)
			{
				$all_trans_in_batch[$val["BATCH_ID"]] = $val["BATCH_ID"];
				$trans_in_batch_prod_store[$val["BOOKING_NO"].'*'.$val["PROD_ID"].'*'.$val["STORE_ID"]] .= $val["BATCH_ID"].'*'.$val["FROM_PROD_ID"].'*'.$val["FROM_STORE"].",";
			}
		}
		unset($trans_in_data);
	}
	/* echo "<pre>";
	print_r($data_array);die; */
	if(!empty($data_array))	
	{
		$con = connect();
		$r_id3=execute_query("delete from tmp_booking_no where userid=$user_id");
		$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (770,771,772,773)");
		if($r_id3 && $r_id6)
		{
			oci_commit($con);
		}
	}

	$all_trans_in_batch = array_filter($all_trans_in_batch);
	if(!empty($all_trans_in_batch))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 770, 1,$all_trans_in_batch, $empty_arr);//PO ID

		$rcv_rate_for_transin_sql = sql_select("SELECT b.prod_id as PROD_ID, b.store_id as STORE_ID, b.pi_wo_batch_no as PI_WO_BATCH_NO, sum(b.cons_quantity) as QUANTITY,  sum(b.order_amount) as ORDER_AMOUNT from inv_receive_master a, inv_transaction b, GBL_TEMP_ENGINE g where a.id=b.mst_id and b.transaction_type=1 and a.entry_form=37 and a.status_active =1 and b.status_active =1 and a.is_deleted=0 and b.is_deleted=0 and b.pi_wo_batch_no=g.ref_val and g.user_id=$user_id and g.entry_form=770 group by b.prod_id, b.store_id, b.pi_wo_batch_no"); 
		//$all_trans_in_batch_nos_cond
		foreach ($rcv_rate_for_transin_sql as $val) 
		{
			$rcv_rate_for_transin_arr[$val["PI_WO_BATCH_NO"].'*'.$val["PROD_ID"].'*'.$val["STORE_ID"]]['QUANTITY'] += $val["QUANTITY"];
			$rcv_rate_for_transin_arr[$val["PI_WO_BATCH_NO"].'*'.$val["PROD_ID"].'*'.$val["STORE_ID"]]['ORDER_AMOUNT'] += $val["ORDER_AMOUNT"];
		}
		unset($rcv_rate_for_transin_sql);

		/*echo "<pre>";
		print_r($rcv_rate_for_transin_arr);
		die;*/

		foreach ($trans_in_batch_prod_store as $transInStr => $RcvStr) 
		{
			$transInArr = explode("*", $transInStr);
			$RcvStrArr = array_unique(explode(",",chop($RcvStr,",")));
			foreach ($RcvStrArr as $val) 
			{
				$RcvStrVal = explode("*", $val);

				$rate_arr_booking_and_product_wise[$transInArr[0]][$transInArr[1]][$transInArr[2]]["quantity"] += $rcv_rate_for_transin_arr[$RcvStrVal[0].'*'.$RcvStrVal[1].'*'.$RcvStrVal[2]]['QUANTITY'];
				$rate_arr_booking_and_product_wise[$transInArr[0]][$transInArr[1]][$transInArr[2]]["amount"] += $rcv_rate_for_transin_arr[$RcvStrVal[0].'*'.$RcvStrVal[1].'*'.$RcvStrVal[2]]["ORDER_AMOUNT"];
			}
		}
	}

	$all_po_id_arr = array_filter($all_po_id_arr);
	$all_po_id_arr = array_unique(explode(",",implode(",", $all_po_id_arr)));
	if(!empty($all_po_id_arr))
	{
		/* $all_po_ids=implode(",",$all_po_id_arr);
		$all_po_id_cond=""; $poCond="";
		$all_po_id_cond_2=""; $poCond_2="";
		if($db_type==2 && count($all_po_id_arr)>999)
		{
			$all_po_id_arr_chunk=array_chunk($all_po_id_arr,999) ;
			foreach($all_po_id_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$poCond.="  e.id in($chunk_arr_value) or ";
				$poCond_2.="  b.po_break_down_id in($chunk_arr_value) or ";
			}

			$all_po_id_cond.=" and (".chop($poCond,'or ').")";
			$all_po_id_cond_2.=" and (".chop($poCond_2,'or ').")";
		}
		else
		{
			$all_po_id_cond=" and e.id in($all_po_ids)";
			$all_po_id_cond_2=" and b.po_break_down_id in($all_po_ids)";
		} */

		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 773, 1,$all_po_id_arr, $empty_arr);//PO ID

		$booking_sql = sql_select("SELECT a.body_part_id as BODY_PART_ID,c.booking_no as BOOKING_NO,a.lib_yarn_count_deter_id as LIB_YARN_COUNT_DETER_ID, c.fabric_color_id as FABRIC_COLOR_ID, c.gmts_color_id as GMTS_COLOR_ID, c.color_type as COLOR_TYPE, d.booking_date as BOOKING_DATE, d.pay_mode as PAY_MODE, d.booking_type as BOOKING_TYPE, d.entry_form as ENTRY_FORM, d.is_short as IS_SHORT, f.company_name as COMPANY_NAME, f.job_no as JOB_NO, f.style_ref_no as STYLE_REF_NO, f.buyer_name as BUYER_NAME, f.client_id as CLIENT_ID, f.season_buyer_wise as SEASON_BUYER_WISE, f.total_set_qnty as TOTAL_SET_QNTY, f.job_quantity as JOB_QUANTITY, c.fin_fab_qnty as FIN_FAB_QNTY, a.uom as UOM, c.rate as RATE, d.supplier_id as SUPPLIER_ID, e.GROUPING
		from wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls c, wo_booking_mst d, wo_po_break_down e, wo_po_details_master f, GBL_TEMP_ENGINE g
		where a.job_no=c.job_no and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and e.status_active=1 and a.id = c.pre_cost_fabric_cost_dtls_id and  f.job_no = e.job_no_mst and c.booking_type in(1) and c.booking_no = d.booking_no and c.po_break_down_id = e.id and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=773 
		union all
		select b.body_part_id as BODY_PART_ID, c.booking_no as BOOKING_NO, b.lib_yarn_count_deter_id as LIB_YARN_COUNT_DETER_ID, c.fabric_color_id as FABRIC_COLOR_ID , c.gmts_color_id as GMTS_COLOR_ID,c.color_type as COLOR_TYPE, d.booking_date as BOOKING_DATE, d.pay_mode as PAY_MODE, d.booking_type as BOOKING_TYPE, d.entry_form as ENTRY_FORM, d.is_short as IS_SHORT,f.company_name as COMPANY_NAME, f.job_no as JOB_NO, f.style_ref_no as STYLE_REF_NO, f.buyer_name as BUYER_NAME, f.client_id as CLIENT_ID, f.season_buyer_wise as SEASON_BUYER_WISE,f.total_set_qnty as TOTAL_SET_QNTY, f.job_quantity as JOB_QUANTITY, c.fin_fab_qnty as FIN_FAB_QNTY, b.uom as UOM, c.rate as RATE, d.supplier_id as SUPPLIER_ID, e.GROUPING
		from wo_pre_cost_fab_conv_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_booking_dtls c ,  wo_booking_mst d , wo_po_break_down e, wo_po_details_master f, GBL_TEMP_ENGINE g
		where b.job_no=c.job_no and a.status_active=1 and b.status_active=1 and c.status_active=1 and e.status_active=1 and a.id = c.pre_cost_fabric_cost_dtls_id and  f.job_no = e.job_no_mst and a.fabric_description = b.id and c.booking_type in(3,4) and c.booking_no = d.booking_no  and c.po_break_down_id = e.id and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=773 ");

		//$all_po_id_cond

		foreach ($booking_sql as  $val)
		{
			$book_po_ref[$val["BOOKING_NO"]]["company_name"] 	= $val["COMPANY_NAME"];
			$book_po_ref[$val["BOOKING_NO"]]["buyer_name"] 	= $val["BUYER_NAME"];
			$book_po_ref[$val["BOOKING_NO"]]["job_no"] 		.= $val["JOB_NO"].",";
			$book_po_ref[$val["BOOKING_NO"]]["int_ref"] 		.= $val["GROUPING"].",";
			$book_po_ref[$val["BOOKING_NO"]]["client_id"] 		= $val["CLIENT_ID"];
			$book_po_ref[$val["BOOKING_NO"]]["season"] 		.= $val["SEASON_BUYER_WISE"].",";
			$book_po_ref[$val["BOOKING_NO"]]["style_ref_no"] 	.= $val["STYLE_REF_NO"].",";
			$book_po_ref[$val["BOOKING_NO"]]["booking_no"] 	= $val["BOOKING_NO"];
			$book_po_ref[$val["BOOKING_NO"]]["booking_date"] 	= $val["BOOKING_DATE"];
			$book_po_ref[$val["BOOKING_NO"]]["pay_mode"] 		= $pay_mode[$val["PAY_MODE"]];
			if($val["PAY_MODE"] == 3 || $val["PAY_MODE"] == 5)
			{
				$book_po_ref[$val["BOOKING_NO"]]["supplier"] = $company_arr[$val["SUPPLIER_ID"]];
			}else{
				$book_po_ref[$val["BOOKING_NO"]]["supplier"] = $supplier_arr[$val["SUPPLIER_ID"]];
			}

			$job_qnty_arr[$val["JOB_NO"]]["qnty"] = $val["JOB_QUANTITY"]*$val["TOTAL_SET_QNTY"];
			$book_po_ref[$val["BOOKING_NO"]][$val["BODY_PART_ID"]][$val["LIB_YARN_COUNT_DETER_ID"]][$val["FABRIC_COLOR_ID"]]["qnty"] += $val["FIN_FAB_QNTY"];
			$book_po_ref[$val["BOOKING_NO"]][$val["BODY_PART_ID"]][$val["LIB_YARN_COUNT_DETER_ID"]][$val["FABRIC_COLOR_ID"]]["color_type"] .= $color_type[$val["COLOR_TYPE"]].",";

			$book_po_ref[$val["BOOKING_NO"]][$val["BODY_PART_ID"]][$val["LIB_YARN_COUNT_DETER_ID"]][$val["FABRIC_COLOR_ID"]]["amount"] += $val["FIN_FAB_QNTY"]*$val["RATE"];

			$bookingType="";
			if($val['BOOKING_TYPE'] == 4)
			{
				$bookingType = "Sample With Order";
			}
			else if($val['BOOKING_TYPE'] == 3)
			{
				$bookingType = "Service Booking";
			}
			else
			{
				$bookingType = $booking_type_arr[$val['ENTRY_FORM']];
			}
			$book_po_ref[$val["BOOKING_NO"]]["booking_type"] = $bookingType;
		}
		unset($booking_sql);
	}
	/*echo "<pre>";
	print_r($book_po_ref);*/

	if(!empty($all_samp_book_arr))
	{
		/* $all_samp_book_nos_cond=""; $sampBookCond="";
		if($db_type==2 && count($all_samp_book_arr)>999)
		{
			$all_samp_book_arr_chunk=array_chunk($all_samp_book_arr,999) ;
			foreach($all_samp_book_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$sampBookCond.="  a.booking_no in($chunk_arr_value) or ";
			}

			$all_samp_book_nos_cond.=" and (".chop($sampBookCond,'or ').")";
		}
		else
		{
			$all_samp_book_nos_cond=" and a.booking_no in(".implode(",",$all_samp_book_arr).")";
		} */

		foreach ($all_samp_book_arr as $s_book) {
			$rID2=execute_query("insert into tmp_booking_no (userid, booking_no) values ($user_id,".$s_book.")");
		}
		if($rID2)
		{
			oci_commit($con);
		}

		//$all_samp_book_ids = implode(",", $all_samp_book_arr);
		$non_samp_sql = sql_select("select a.booking_date, a.booking_no, a.pay_mode, a.company_id, a.supplier_id, b.lib_yarn_count_deter_id, b.gmts_color,b.uom, b.color_type_id, b.body_part, a.buyer_id, b.style_des from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, tmp_booking_no c where a.booking_no=b.booking_no and b.status_active =1 and a.booking_type =4 and a.booking_no=c.booking_no and c.userid=$user_id"); //and a.id in ($all_samp_book_ids) // $all_samp_book_nos_cond 

		foreach ($non_samp_sql as  $val)
		{
			$book_po_ref[$val[csf("booking_no")]]["booking_no"]   	= $val[csf("booking_no")];
			$book_po_ref[$val[csf("booking_no")]]["booking_date"]  	= $val[csf("booking_date")];
			$book_po_ref[$val[csf("booking_no")]]["company_name"] 	= $val[csf("company_id")];
			$book_po_ref[$val[csf("booking_no")]]["buyer_name"] 	= $val[csf("buyer_id")];
			$book_po_ref[$val[csf("booking_no")]]["style_ref_no"] 	= $val[csf("style_des")];
			$book_po_ref[$val[csf("booking_no")]]["booking_type"] 	= "Sample WithOut Order";
			if($val[csf("pay_mode")] == 3 || $val[csf("pay_mode")] 	== 5)
			{
				$book_po_ref[$val[csf("booking_no")]]["supplier"] = $company_arr[$val[csf("supplier_id")]];
			}else{
				$book_po_ref[$val[csf("booking_no")]]["supplier"] = $supplier_arr[$val[csf("supplier_id")]];
			}
		}
		unset($non_samp_sql);
	}

	$batch_id_arr = array_filter($batch_id_arr);
	if(!empty($batch_id_arr))
	{
		/* $batch_ids= implode(",",$batch_id_arr);

		$all_batch_ids_cond=""; $batchCond="";
		if($db_type==2 && count($batch_id_arr)>999)
		{
			$batch_id_arr_chunk=array_chunk($batch_id_arr,999) ;
			foreach($batch_id_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$batchCond.="  e.id in($chunk_arr_value) or ";
			}
			$all_batch_ids_cond.=" and (".chop($batchCond,'or ').")";
		}
		else
		{
			$all_batch_ids_cond=" and e.id in($batch_ids)";
		} */

		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 771, 1,$batch_id_arr, $empty_arr);//All Batch ID
	}
	/* echo "<pre>";
	print_r($data_array);die; */
	$issRtnSql = "select c.transaction_date, d.knit_dye_source, b.body_part_id, b.prod_id,c.store_id, b.fabric_description_id, b.gsm, b.width, f.color as color_id,c.cons_uom, c.cons_quantity as quantity, c.order_rate, b.batch_id, e.batch_no, e.booking_no, e.booking_without_order from inv_receive_master a, pro_finish_fabric_rcv_dtls b,inv_transaction c, inv_issue_master d, pro_batch_create_mst e, product_details_master f, GBL_TEMP_ENGINE g  where a.id = b.mst_id and b.trans_id=c.id and c.issue_id=d.id and a.entry_form=52 and a.item_category=2 and c.pi_wo_batch_no = e.id and c.prod_id=f.id and a.status_active =1 and b.status_active=1 and c.status_active =1 and c.company_id in  ($cbo_company_id) $store_cond_2 $date_cond_2 and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=771 ";
	//$all_batch_ids_cond
	$issRtnData = sql_select($issRtnSql);
	foreach ($issRtnData as $val)
	{

		$issRtnRef_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("fabric_description_id")]."*".$val[csf("gsm")]."*".$val[csf("width")]."*".$val[csf("color_id")]."*".$val[csf("cons_uom")]."*".$val[csf("batch_id")]."*".$val[csf("batch_no")];


		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
		$ref_str="";
		if($transaction_date >= $date_frm)
		{
			if($val[csf("knit_dye_source")] == 1)
			{
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["inside_return"] += $val[csf("quantity")];
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["inside_return_amount"] += $val[csf("quantity")]*$val[csf("order_rate")];
			}
			else
			{
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["outside_return"] += $val[csf("quantity")];
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["outside_return_amount"] += $val[csf("quantity")]*$val[csf("order_rate")];
			}
		}
		else
		{
			$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["opening"] += $val[csf("quantity")];
			$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["opening_amount"] +=$val[csf("quantity")]*$val[csf("order_rate")];
		}
	}
	unset($issRtnData);

	
	$issue_sql = sql_select("select a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, c.cons_uom, c.store_id, c.cons_quantity, c.id as trans_id,c.transaction_date, d.detarmination_id, d.gsm, d.dia_width, d.color, c.pi_wo_batch_no, e.batch_no, e.booking_no, e.booking_without_order, round(c.order_rate,2) as order_rate from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE g where a.id=b.mst_id and b.trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no= e.id and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2 and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=771 and a.entry_form=18 and c.status_active=1 and b.status_active=1 and a.status_active=1 and c.item_category=2 and c.transaction_type =2 group by a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, c.cons_uom, c.store_id, c.cons_quantity, c.id, c.transaction_date, d.detarmination_id, d.gsm, d.dia_width, d.color, c.pi_wo_batch_no, e.batch_no, e.booking_no, e.booking_without_order, round(c.order_rate,2)");
	//$all_batch_ids_cond
	
	foreach ($issue_sql as $val)
	{
		$issRef_str="";
		$issRef_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")]."*".$val[csf("pi_wo_batch_no")]."*".$val[csf("batch_no")];


		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
		
		if($transaction_date >= $date_frm)
		{
			if($val[csf("issue_purpose")] == 9)
			{
				if($val[csf("knit_dye_source")] == 1)
				{
					$issue_data[$val[csf("booking_no")]][$issRef_str]["cutting_inside"] += $val[csf("cons_quantity")];
				}
				else
				{
					$issue_data[$val[csf("booking_no")]][$issRef_str]["cutting_outside"] += $val[csf("cons_quantity")];
				}
			}
			else
			{
				$issue_data[$val[csf("booking_no")]][$issRef_str]["other_issue"] += $val[csf("cons_quantity")];
			}
			$issue_data[$val[csf("booking_no")]][$issRef_str]["issue_amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
		}
		else
		{
			$issue_data[$val[csf("booking_no")]][$issRef_str]["opening_issue"] += $val[csf("cons_quantity")];
			$issue_data[$val[csf("booking_no")]][$issRef_str]["opening_issue_amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
		}
	}
	unset($issue_sql);
	/*echo "<pre>";
	print_r($issue_data);
	die;*/
	
	$rcvRtnSql = sql_select("select c.transaction_date, c.company_id, c.prod_id, c.store_id, c.cons_quantity, c.cons_uom, d.detarmination_id, d.gsm, d.dia_width, d.color, e.booking_no, e.booking_without_order, b.body_part_id, c.pi_wo_batch_no, e.batch_no from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE g where a.id = b.mst_id and b.trans_id=c.id and a.entry_form =46 and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2 and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=771 and c.prod_id=d.id and c.pi_wo_batch_no=e.id and a.status_active =1 and b.status_active =1 and c.status_active =1");
	//$all_batch_ids_cond

	foreach ($rcvRtnSql as $val)
	{

		$rcvRtn_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")]."*".$val[csf("pi_wo_batch_no")]."*".$val[csf("batch_no")];

		

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
		$ref_str="";
		if($transaction_date >= $date_frm)
		{
			$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["qnty"] += $val[csf("cons_quantity")];
			$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
		}
		else
		{
			$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["opening_qnty"] += $val[csf("cons_quantity")];
			$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["opening_amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
		}
	}
	unset($rcvRtnSql);
	
	$transOutSql = sql_select("SELECT c.transaction_date,c.pi_wo_batch_no, e.batch_no,e.booking_no, e.booking_no_id, e.booking_without_order, c.body_part_id, c.prod_id, c.store_id, d.detarmination_id, d.gsm, d.dia_width, d.color, c.cons_uom, c.cons_quantity,c.order_rate from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c,product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE g where a.id=b.mst_id and b.trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2 and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=771 and c.item_category=2 and c.transaction_type=6 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.entry_form in (14,15,306) and b.trans_id=c.id and b.active_dtls_id_in_transfer=1"); //$all_batch_ids_cond

	foreach ($transOutSql as $val)
	{
		$transOut_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")]."*".$val[csf("pi_wo_batch_no")]."*".$val[csf("batch_no")];

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
		$ref_str="";
		if($transaction_date >= $date_frm)
		{
			$trans_out_data[$val[csf("booking_no")]][$transOut_str]["qnty"] += $val[csf("cons_quantity")];
			$trans_out_data[$val[csf("booking_no")]][$transOut_str]["amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
		}
		else
		{
			$trans_out_data[$val[csf("booking_no")]][$transOut_str]["opening_qnty"] += $val[csf("cons_quantity")];
			$trans_out_data[$val[csf("booking_no")]][$transOut_str]["opening_amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
		}
	}
	unset($transOutSql);
	
    /*echo "<pre>";
    print_r($trans_out_data);
    die;*/

    $composition_arr=array();
    $sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id ";
    $data_deter=sql_select($sql_deter);

    if(count($data_deter)>0)
    {
    	foreach( $data_deter as $row )
    	{
    		if(array_key_exists($row[csf('id')],$composition_arr))
    		{
    			$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
    			$constructionArr[$row[csf('id')]]=$constructionArr[$row[csf('id')]];
    			list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
    			$copmpositionArr[$row[csf('id')]]=$cps;
    		}
    		else
    		{
    			$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
    			$constructionArr[$row[csf('id')]]=$row[csf('construction')];
    			list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
    			$copmpositionArr[$row[csf('id')]]=$cps;
    		}
    	}
    }
    unset($data_deter);

    if(!empty($all_prod_id))
    {
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 772, 1,$all_prod_id, $empty_arr);

    	$transaction_date_array=array();
		//$sql_date="SELECT c.booking_no, a.prod_id, min(a.transaction_date) as min_date, max(a.transaction_date) as max_date from inv_transaction a,pro_batch_create_mst c, GBL_TEMP_ENGINE g where a.pi_wo_batch_no=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=2 and a.prod_id=g.ref_val and g.entry_form=772 and g.user_id=$user_id group by c.booking_no,a.prod_id"; //$all_prod_id_cond 

		$sql_date="SELECT a.pi_wo_batch_no, a.prod_id, a.store_id, a.transaction_type,  a.transaction_date  
		from inv_transaction a, GBL_TEMP_ENGINE g 
		where a.status_active=1 and a.is_deleted=0 and a.item_category=2 and a.prod_id=g.ref_val and g.entry_form=772 and g.user_id=$user_id $date_cond_3";

		$sql_date_result=sql_select($sql_date);
		foreach( $sql_date_result as $row )
		{
			if($row[csf('transaction_type')]==1 || $row[csf('transaction_type')]==4 || $row[csf('transaction_type')]==5)
			{
				if($transaction_date_array[$row[csf('pi_wo_batch_no')]][$row[csf('prod_id')]][$row[csf('store_id')]]['max_rcv']=="")
				{
					$transaction_date_array[$row[csf('pi_wo_batch_no')]][$row[csf('prod_id')]][$row[csf('store_id')]]['max_rcv']=$row[csf('transaction_date')];
				}
				else if($transaction_date_array[$row[csf('pi_wo_batch_no')]][$row[csf('prod_id')]][$row[csf('store_id')]]['max_rcv'] < $row[csf('transaction_date')])
				{
					$transaction_date_array[$row[csf('pi_wo_batch_no')]][$row[csf('prod_id')]][$row[csf('store_id')]]['max_rcv']=$row[csf('transaction_date')];
				}
			}

			if($transaction_date_array[$row[csf('pi_wo_batch_no')]][$row[csf('prod_id')]][$row[csf('store_id')]]['max_tr_date']=="")
			{
				$transaction_date_array[$row[csf('pi_wo_batch_no')]][$row[csf('prod_id')]][$row[csf('store_id')]]['max_tr_date']=$row[csf('transaction_date')];
			}
			else if($transaction_date_array[$row[csf('pi_wo_batch_no')]][$row[csf('prod_id')]][$row[csf('store_id')]]['max_tr_date'] < $row[csf('transaction_date')])
			{
				$transaction_date_array[$row[csf('pi_wo_batch_no')]][$row[csf('prod_id')]][$row[csf('store_id')]]['max_tr_date']=$row[csf('transaction_date')];
			}


			if($row[csf('transaction_type')]==1 )
			{
				if($last_rcv_date_array[$row[csf('pi_wo_batch_no')]][$row[csf('prod_id')]][$row[csf('store_id')]]['max_rcv']=="")
				{
					$last_rcv_date_array[$row[csf('pi_wo_batch_no')]][$row[csf('prod_id')]][$row[csf('store_id')]]['max_rcv']=$row[csf('transaction_date')];
				}
				else if($last_rcv_date_array[$row[csf('pi_wo_batch_no')]][$row[csf('prod_id')]][$row[csf('store_id')]]['max_rcv'] < $row[csf('transaction_date')])
				{
					$last_rcv_date_array[$row[csf('pi_wo_batch_no')]][$row[csf('prod_id')]][$row[csf('store_id')]]['max_rcv']=$row[csf('transaction_date')];
				}
			}

			if($row[csf('transaction_type')]==2 )
			{
				if($last_rcv_date_array[$row[csf('pi_wo_batch_no')]][$row[csf('prod_id')]][$row[csf('store_id')]]['max_issue']=="")
				{
					$last_rcv_date_array[$row[csf('pi_wo_batch_no')]][$row[csf('prod_id')]][$row[csf('store_id')]]['max_issue']=$row[csf('transaction_date')];
				}
				else if($last_rcv_date_array[$row[csf('pi_wo_batch_no')]][$row[csf('prod_id')]][$row[csf('store_id')]]['max_issue'] < $row[csf('transaction_date')])
				{
					$last_rcv_date_array[$row[csf('pi_wo_batch_no')]][$row[csf('prod_id')]][$row[csf('store_id')]]['max_issue']=$row[csf('transaction_date')];
				}
			}


		}
		unset($sql_date_result);
    }

    $r_id3=execute_query("delete from tmp_booking_no where userid=$user_id");
	$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (770,771,772,773)");
	if($r_id3 && $r_id6)
	{
		oci_commit($con);
	}
    
	/* echo "<pre>";
	print_r($last_rcv_date_array);
	die; */

	$table_width = "4050";
	$col_span = "28";

	ob_start();
	?>
	<style type="text/css">
		.word_break_wrap {
			word-break: break-all;
			word-wrap: break-word;
		}
		.grad1 {
			  background-image: linear-gradient(#e6e6e6, #b1b1cd, #e0e0eb);
			}
	</style>
	<fieldset style="width:<? echo $table_width+20;?>px;">
		<table cellpadding="0" cellspacing="0" width="2080">
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="23" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="23" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="23" style="font-size:14px"><strong> <? if($date_from!="") echo "From : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
			</tr>
		</table>

		<table width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="90">LC Company</th>
				<th width="60">Buyer</th>
				<th width="80">Internal Ref.</th>
				<th width="80">SBU</th>
				<th width="50">Job</th>
				<th width="50">Style</th>
				<th width="70">Season</th>
				<th width="70">Booking No</th>
				<th width="50">Booking Date</th>
				<th width="80">Booking Type</th>
				<th width="50">Paymode</th>
				<th width="100">PI</th>
				<th width="100">LC/SC</th>
				<th width="70">Supplier</th>
				<th width="100">PO Number</th>
				<th width="100">Store Name</th>
				<th width="100">Batch No</th>
				<th width="40">Product ID</th>
				<th width="100">Body Part</th>
				<th width="120">F.Construction</th>
				<th width="120">F.Composition</th>
				<th width="40"><p>Fab.Dia</p></th>
				<th width="40">GSM</th>
				<th width="100">F. Color</th>
				<th width="40">UOM</th>
				<th width="60">Opening Stock</th>
				<th width="60">Receive Qty</th>
				<th width="60"><p>Inside Issue Return</p></th>
				<th width="60"><p>Outside Issue Return</p></th>
				<th width="60">Trans In Qty</th>
				<th width="60">Total Rcv</th>
				<th width="40">Rate ($)</th>
				<th width="60">Receive Amount</th>
				<th width="60"><p>Cutting Issue Inside</p></th>
				<th width="60"><p>Cutting Issue Outside</p></th>
				<th width="60">Other Issue Qty</th>
				<th width="60">Receive Rtn. Qnty</th>
				<th width="60">Trans Out Qty</th>
				<th width="60">Total Issue</th>
				<th width="40">Rate ($)</th>
				<th width="60">Issue Amount</th>
				<th width="60">Stock Qty</th>
				<th width="40">Rate ($)</th>
				<th width="80">Stock Amount</th>
				<th width="50">Age (days)</th>
				<th width="50">DOH</th>
				<th width="50">Last Rcv. Date</th>
				<th width="50">Last trans. Date</th>
			</thead>
		</table>
		<div style="width:<? echo $table_width+20;?>px; max-height:350px; overflow-y:scroll;" id="scroll_body">
			<table width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
				<?
				$i=1;
				foreach ($data_array as $uom => $uom_data)
				{
					$uom_total_booking_qty=$uom_total_opening_qnty=$uom_total_recv_qnty=$uom_total_inside_return=$uom_total_outside_return=$uom_total_trans_in_qty=$uom_total_tot_receive=$uom_total_total_issue=$uom_total_total_issue_amount=$uom_total_stock_qnty=$uom_total_stock_amount=$uom_total_cutting_inside_issue=$uom_total_cutting_outside_issue=$uom_total_other_issue=$uom_total_rcv_return_qnty=$uom_total_trans_out_qnty=0;
					foreach ($uom_data as $booking_no => $book_data)
					{
						foreach ($book_data as $prodStr => $row)
						{
							//echo $prodStr."<br>";
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							$ref_qnty_arr = explode("__", $row);
							$recv_qnty=$trans_out_qty=$trans_in_qty=$opening_recv=$opening_trans=0;
							$recv_amount=$opening_recv_amount=$trans_in_amount=$opening_trans_amount=0;
							$dia_width_types="";$pi_no=""; $lc_sc_no="";
							foreach ($ref_qnty_arr as $ref_qnty_str)
							{
								$ref_qnty = explode("*", $ref_qnty_str);
								if($ref_qnty[6] == 1)
								{
									if($ref_qnty[7]==1){
										$recv_qnty += $ref_qnty[0];
										$recv_amount += $ref_qnty[0]*$ref_qnty[1];
									}else{
										$opening_recv +=$ref_qnty[0];
										$opening_recv_amount +=$ref_qnty[0]*$ref_qnty[1];
									}
								}
								if($ref_qnty[6] == 5)
								{
									if($ref_qnty[7]==1){
										$trans_in_qty += $ref_qnty[0];
										$trans_in_amount += $ref_qnty[0]*$ref_qnty[1];
									}else{
										$opening_trans +=$ref_qnty[0];
										$opening_trans_amount +=$ref_qnty[0]*$ref_qnty[1];
									}
								}
								$dia_width_types .=$ref_qnty[4].",";

								if($ref_qnty[2]==1)
								{
									$pi_no .= $ref_qnty[3].",";
								}

								$lc_sc_no .= $ref_qnty[5].",";
								//echo $recv_qnty."=";
							}

							$po_number 	= implode(",",array_unique(explode(",",chop($po_array[$booking_no][$prodStr]["po_no"],","))));
							$pi_no 	= implode(",",array_unique(explode(",",chop($pi_no,","))));
							$lc_sc_no 	= implode(",",array_unique(explode(",",chop($lc_sc_no,","))));
							$prodStr 	= explode("*", $prodStr);

							$company_name 	= $book_po_ref[$booking_no]["company_name"];
							$buyer_name 	= $book_po_ref[$booking_no]["buyer_name"];
							$supplier 		= $book_po_ref[$booking_no]["supplier"];
							$int_ref_arr 		= array_unique(explode(",",chop($book_po_ref[$booking_no]["int_ref"],",")));
							$int_ref = implode(",", $int_ref_arr);
							$job_arr 		= array_unique(explode(",",chop($book_po_ref[$booking_no]["job_no"],",")));
							$job_nos = implode(",", $job_arr);

							$client_arr = array_unique(explode(",",chop($book_po_ref[$booking_no]["client_id"],",")));
							$client_nos="";
							foreach ($client_arr as $client_id)
							{
								$client_nos .= $buyer_arr[$client_id].",";
							}
							$client_nos = chop($client_nos,",");

							$season = array_unique(explode(",",chop($book_po_ref[$booking_no]["season"],",")));
							$season_nos="";
							foreach ($season as $s_id)
							{
								$season_nos .= $season_arr[$s_id].",";
							}

							$style_ref_no = implode(",",array_unique(explode(",",chop($book_po_ref[$booking_no]["style_ref_no"],","))));;
							$pay_mode_nos = implode(",",array_unique(explode(",",chop($book_po_ref[$booking_no]["pay_mode"],","))));

							$booking_date = $book_po_ref[$booking_no]["booking_date"];
							$booking_type = $book_po_ref[$booking_no]["booking_type"];

							//$dia_width_type_arr = array_filter(array_unique(explode(",",chop($dia_width_types,","))));
							$dia_width_type_arr = array_unique(explode(",",chop($dia_width_types,",")));

							$dia_width_type="";
							foreach ($dia_width_type_arr as $width_type)
							{
								$dia_width_type .= $fabric_typee[$width_type].",";
							}
							$dia_width_type = chop($dia_width_type,",");

							$booking_qnty 	= $book_po_ref[$booking_no][$prodStr[2]][$prodStr[3]][$prodStr[6]]["qnty"];
							$booking_amount = $book_po_ref[$booking_no][$prodStr[2]][$prodStr[3]][$prodStr[6]]["amount"];
							if($booking_qnty >0){
								$booking_rate 	= $booking_amount/$booking_qnty;
							}else{
								$booking_rate=0;
							}

							$color_type_nos = implode(",",array_unique(explode(",",chop($book_po_ref[$booking_no][$prodStr[2]][$prodStr[3]][$prodStr[6]]["color_type"],","))));

							//$transOut_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")];

							
							$issRtnRef_str = $prodStr[0]."*".$prodStr[1]."*".$prodStr[2]."*".$prodStr[3]."*".$prodStr[4]."*".$prodStr[5]."*".$prodStr[6]."*".$prodStr[7]."*".$prodStr[8]."*".$prodStr[9];
							
							$inside_return 			= $issue_return_data[$booking_no][$issRtnRef_str]["inside_return"];
							$inside_return_amount 	= $issue_return_data[$booking_no][$issRtnRef_str]["inside_return_amount"];
							$outside_return 		= $issue_return_data[$booking_no][$issRtnRef_str]["outside_return"];
							$outside_return_amount  = $issue_return_data[$booking_no][$issRtnRef_str]["outside_return_amount"];
							$opening_iss_return 	= $issue_return_data[$booking_no][$issRtnRef_str]["opening"];
							$opening_iss_return_amount = $issue_return_data[$booking_no][$issRtnRef_str]["opening_amount"];

							$tot_receive 			= $recv_qnty + $trans_in_qty + $inside_return + $outside_return;
							$tot_receive_amount 	= $recv_amount + $trans_in_amount + $inside_return_amount + $outside_return_amount;

							$tot_receive_rate=0;
							if($tot_receive>0)
							{
								$tot_receive_rate 	= $tot_receive_amount/$tot_receive;
							}
							$booking_balance_qnty 	= $booking_qnty- $tot_receive;
							$booking_balance_amount = $booking_balance_qnty*$booking_rate;

							$cutting_inside 		= $issue_data[$booking_no][$issRtnRef_str]["cutting_inside"];
							$cutting_outside 		= $issue_data[$booking_no][$issRtnRef_str]["cutting_outside"];
							$other_issue 			= $issue_data[$booking_no][$issRtnRef_str]["other_issue"];
							$issue_amount 			= $issue_data[$booking_no][$issRtnRef_str]["issue_amount"];
							$opening_issue 			= $issue_data[$booking_no][$issRtnRef_str]["opening_issue"];
							$opening_issue_amount 	= $issue_data[$booking_no][$issRtnRef_str]["opening_issue_amount"];

							$rcv_return_opening_qnty = $rcv_return_data[$booking_no][$issRtnRef_str]["opening_qnty"];
							$rcv_return_opening_amount = $rcv_return_data[$booking_no][$issRtnRef_str]["opening_amount"];
							$rcv_return_qnty  		= $rcv_return_data[$booking_no][$issRtnRef_str]["qnty"];
							$rcv_return_amount  	= $rcv_return_data[$booking_no][$issRtnRef_str]["amount"];

							$trans_out_amount  		= $trans_out_data[$booking_no][$issRtnRef_str]["amount"];
							$trans_out_qnty  		= $trans_out_data[$booking_no][$issRtnRef_str]["qnty"];
							$trans_out_opening_qnty = $trans_out_data[$booking_no][$issRtnRef_str]["opening_qnty"];
							$trans_out_opening_amount = $trans_out_data[$booking_no][$issRtnRef_str]["opening_amount"];

							$total_issue  			= $cutting_inside + $cutting_outside + $other_issue + $rcv_return_qnty + $trans_out_qnty;
							/*$total_issue_amount 	= $issue_amount + $rcv_return_amount + $trans_out_amount;
							//echo $issue_amount.' + '.$rcv_return_amount.' + '.$trans_out_amount;
							$tot_issue_rate=0;
							if($total_issue>0)
							{
								$tot_issue_rate 	= $total_issue_amount/$total_issue;
							}*/

							$opening_title 	= "Receive:".$opening_recv ." + Transfer In:". $opening_trans ." + Issue Return:" . $opening_iss_return . "\n";
							$opening_title 	.= "Issue:".$opening_issue ." + Transfer Out:". $trans_out_opening_qnty ." + Receive Return:" . $rcv_return_opening_qnty;
							$opening_qnty 	= ($opening_recv + $opening_trans + $opening_iss_return) - ($opening_issue + $rcv_return_opening_qnty +$trans_out_opening_qnty);

							$stock_qnty 	= $opening_qnty + ($tot_receive - $total_issue);
							$stock_title 	= "Opening:".$opening_qnty ." + (Receive:". $tot_receive ."- Issue:". $total_issue.")";

							$booking_and_product_wise_quantity = $rate_arr_booking_and_product_wise[$booking_no][$prodStr[0]][$prodStr[1]]["quantity"];
							$booking_and_product_wise_amount = $rate_arr_booking_and_product_wise[$booking_no][$prodStr[0]][$prodStr[1]]["amount"];
							$booking_and_product_wise_rate = $booking_and_product_wise_amount/$booking_and_product_wise_quantity;

							$tot_receive_rate =$booking_and_product_wise_rate;
							// echo $tot_receive_rate.'<br>';

							$opening_amount = ($opening_recv_amount+$opening_trans_amount) -($opening_issue_amount + $rcv_return_opening_amount);

							if($opening_qnty>0)
							{
								//$opening_rate = $opening_amount/$opening_qnty;

								//$opening_rate = ($opening_recv_amount+$opening_trans_amount) / ($opening_recv + $opening_trans + $opening_iss_return);
							}

							if($tot_receive_rate ==0)
							{
								$tot_receive_rate =$opening_rate;
							}

							$tot_issue_rate = $tot_receive_rate;
							$total_issue_amount = $total_issue * $tot_issue_rate;

							if(number_format($stock_qnty,2,".","") == "-0.00")
							{
								$stock_qnty=0;
							}

							$stock_rate = $tot_receive_rate;
							$stock_amount = $stock_qnty * $stock_rate;

							/* $daysOnHand = datediff("d",change_date_format($transaction_date_array[$prodStr[8]][$prodStr[0]][$prodStr[1]]['max_tr_date'],'','',1),date("Y-m-d"));
							$ageOfDays 	= datediff("d",change_date_format($transaction_date_array[$prodStr[8]][$prodStr[0]][$prodStr[1]]['max_rcv'],'','',1),date("Y-m-d")); */

							if($start_date!="" && $end_date !="")
							{
								$daysOnHand = datediff("d",change_date_format($transaction_date_array[$prodStr[8]][$prodStr[0]][$prodStr[1]]['max_tr_date'],'','',1),date("Y-m-d",strtotime($end_date)));
								$ageOfDays 	= datediff("d",change_date_format($transaction_date_array[$prodStr[8]][$prodStr[0]][$prodStr[1]]['max_rcv'],'','',1),date("Y-m-d",strtotime($end_date)));
							}
							else
							{
								$daysOnHand = datediff("d",change_date_format($transaction_date_array[$prodStr[8]][$prodStr[0]][$prodStr[1]]['max_tr_date'],'','',1),date("Y-m-d"));
								$ageOfDays 	= datediff("d",change_date_format($transaction_date_array[$prodStr[8]][$prodStr[0]][$prodStr[1]]['max_rcv'],'','',1),date("Y-m-d"));
							}

							$last_rcv_date = $last_rcv_date_array[$prodStr[8]][$prodStr[0]][$prodStr[1]]['max_rcv'];
							$last_issue = $last_rcv_date_array[$prodStr[8]][$prodStr[0]][$prodStr[1]]['max_issue'];


							//echo $recv_qnty."<br>";
							if(($consump_per_dzn/12) > 0)
							{
								$possible_cut_piece = $stock_qnty/($consump_per_dzn/12);
							}

							if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stock_qnty > $txt_qnty) || ($get_upto_qnty == 2 && $stock_qnty < $txt_qnty) || ($get_upto_qnty == 3 && $stock_qnty >= $txt_qnty) || ($get_upto_qnty == 4 && $stock_qnty <= $txt_qnty) || ($get_upto_qnty == 5 && $stock_qnty == $txt_qnty) || $get_upto_qnty == 0))
							{
								if($stock_qnty!=0 && $cbo_value_with==2)
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<td width="30"><? echo $i;?></td>
										<td width="90"><? echo $company_arr[$company_name]?></td>
										<td width="60" title="<? echo $buyer_arr[$buyer_name]; ?>"><? echo (strlen($buyer_arr[$buyer_name]) > 10) ? substr($buyer_arr[$buyer_name],0,10).'...' :$buyer_arr[$buyer_name]; ?></td>
										<td width="80"><? echo $int_ref;?></td>
										<td width="80" class="word_break_wrap"><? echo $client_nos;?></td>
										<td width="50"><p class="word_break_wrap"><? echo $job_nos;?></p></td>
										<td width="50" title="<? echo $style_ref_no; ?>"><p class="word_break_wrap"><? echo (strlen($style_ref_no) > 10) ? substr($style_ref_no,0,10).'...' :$style_ref_no; ?></p></td>
										<td width="70"><? echo chop($season_nos,",");?></td>
										<td width="70" class="word_break_wrap"><? echo $booking_no;?></td>
										<td width="50"><? echo $booking_date;?></td>
										<td width="80"><? echo $booking_type;?></td>
										<td width="50"><? echo $pay_mode_nos;?></td>
										<td width="100"><? echo $pi_no;?></td>
										<td width="100" title="<? echo $lc_sc_no; ?>"><p class="word_break_wrap"><? echo (strlen($lc_sc_no) > 16) ? substr($lc_sc_no,0,16).'...' :$lc_sc_no; ?></p></td>
										<td width="70" title="<? echo $supplier; ?>"><p class="word_break_wrap"><? echo (strlen($supplier) > 16) ? substr($supplier,0,16).'...' :$supplier; ?></p></td>
										<td width="100" title="<? //echo $po_breakdown_id;?>"><a href="##" onClick="open_po_number('<? echo $po_number;?>','<? echo $prodStr;?>');">view</a></td>
										<td width="100" title="store"><? echo $store_arr[$prodStr[1]];?></td>
										<td width="100" title="batch<? echo $prodStr[8];?>" class="word_break_wrap"><? echo $prodStr[9];?></td>
										
										<td width="40"><? echo $prodStr[0];?></td>
										<td width="100" title="<? echo $body_part[$prodStr[2]];?>"><p class="word_break_wrap"><? echo (strlen($body_part[$prodStr[2]]) > 15) ? substr($body_part[$prodStr[2]],0,15).'...' :$body_part[$prodStr[2]]; ?></p></td>
										<td width="120"><p class="word_break_wrap"><? echo $constructionArr[$prodStr[3]];?></p></td>
										<td width="120" title="<? echo $composition_arr[$prodStr[3]]; ?>"><p class="word_break_wrap"><? 
										echo (strlen($composition_arr[$prodStr[3]]) > 25) ? substr($composition_arr[$prodStr[3]],0,25).'...' :$composition_arr[$prodStr[3]];?></p></td>
										<td width="40"><p class="word_break_wrap"><? echo $prodStr[5]; ?></p></td>
										<td width="40"><? echo $prodStr[4]; ?></td>
										<td width="100" title="<? echo $color_arr[$prodStr[6]]; ?>"><p class="word_break_wrap"><? echo (strlen($color_arr[$prodStr[6]]) > 15) ? substr($color_arr[$prodStr[6]],0,15).'...' :$color_arr[$prodStr[6]]; ?></p></td>
										<td width="40"><? echo $unit_of_measurement[$prodStr[7]]; ?></td>
										
										<td width="60" align="right" title="<? echo $opening_title;?>" class="word_break_wrap"><? echo number_format($opening_qnty,2,".","");?></td>
										<td width="60" align="right" class="word_break_wrap">
											<a href="##" onClick="openmypage_qnty('<? echo $booking_no;?>','<? echo implode("*", $prodStr);?>','openmypage_receive','<? echo $start_date;?>','<? echo $end_date;?>');"><? echo number_format($recv_qnty,2,".","");?>
											</a>
										</td> 
										<td width="60" align="right" class="word_break_wrap"><? echo number_format($inside_return,2,".","");?></td>
										<td width="60" align="right" class="word_break_wrap"><? echo number_format($outside_return,2,".","");?></td>
										<td width="60" align="right" class="word_break_wrap">
											<a href="##" onClick="openmypage_qnty('<? echo $booking_no;?>','<? echo implode("*", $prodStr);?>','openmypage_trans_in','<? echo $start_date;?>','<? echo $end_date;?>');"><? echo number_format($trans_in_qty,2,".","");?>
											</a>
										</td>
										<td width="60" align="right" class="word_break_wrap"><? echo number_format($tot_receive,2,".","");?></td>
										<td width="40" align="right"><p class="word_break_wrap"><? echo number_format($tot_receive_rate,2,".","");?></p></td>
										<td width="60" align="right"><? echo number_format($tot_receive_amount,2,".","");?></td>
										<td width="60" align="right"><a href="##" onClick="openmypage_qnty('<? echo $booking_no;?>','<? echo implode("*", $prodStr);?>','openmypage_cutting_inside','<? echo $start_date;?>','<? echo $end_date;?>');"><? echo number_format($cutting_inside,2,".","");?></a></td>
										<td width="60" align="right"><a href="##" onClick="openmypage_qnty('<? echo $booking_no;?>','<? echo implode("*", $prodStr);?>','openmypage_cutting_outside','<? echo $start_date;?>','<? echo $end_date;?>');"><? echo number_format($cutting_outside,2,".",""); ?></a></td>
										<td width="60" align="right"><? echo number_format($other_issue,2,".","") ?></td>
										<td width="60" align="right"><? echo number_format($rcv_return_qnty,2,".","");?></td>
										<td width="60" align="right"><a href="##" onClick="openmypage_qnty('<? echo $booking_no;?>','<? echo implode("*", $prodStr);?>','openmypage_trans_out','<? echo $start_date;?>','<? echo $end_date;?>');"><? echo number_format($trans_out_qnty,2,".","");?></a></td>
										<td width="60" align="right"><? echo number_format($total_issue,2,".","");?></td>
										<td width="40" align="right"><p class="word_break_wrap"><? echo number_format($tot_issue_rate,2,".","");?></p></td>
										<td width="60" align="right"><? echo number_format($total_issue_amount,2,".","");?></td>
										<td width="60" align="right" title="<? //echo $stock_title;?>"><? echo number_format($stock_qnty,2,".","");?></td>
										<td width="40" align="right"><p class="word_break_wrap"><? echo number_format($stock_rate,2,".","");?></p></td>
										<td width="80" align="right"><? echo number_format($stock_amount,2,".","");?></td>
										<td width="50" align="center"><? echo $ageOfDays;?></td>
										<td width="50" align="center"><? echo $daysOnHand ?></td>
										<td width="50" align="center"><? echo $last_rcv_date ?></td>
										<td width="50" align="center"><? echo $last_issue ?></td>
									</tr>
									<?
									$i++;
									$uom_total_booking_qty+=$booking_qnty;
									$uom_total_opening_qnty+=$opening_qnty;
									$uom_total_recv_qnty+=$recv_qnty;
									$uom_total_inside_return+=$inside_return;
									$uom_total_outside_return+=$outside_return;
									$uom_total_trans_in_qty+=$trans_in_qty;
									$uom_total_tot_receive+=$tot_receive;
									$uom_total_cutting_inside_issue+=$cutting_inside;
									$uom_total_cutting_outside_issue+=$cutting_outside;
									$uom_total_other_issue+=$other_issue;
									$uom_total_rcv_return_qnty+=$rcv_return_qnty;
									$uom_total_trans_out_qnty+=$trans_out_qnty;
									$uom_total_total_issue+=$total_issue;
									$uom_total_total_issue_amount+=$total_issue_amount;
									$uom_total_stock_qnty+=$stock_qnty;
									$uom_total_stock_amount+=$stock_amount;
								}
								//else if($stock_qnty>=0 && $cbo_value_with==1)
								else if( $cbo_value_with==1)
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<td width="30"><? echo $i;?></td>
										<td width="90"><? echo $company_arr[$company_name]?></td>
										<td width="60" title="<? echo $buyer_arr[$buyer_name]; ?>"><? echo (strlen($buyer_arr[$buyer_name]) > 10) ? substr($buyer_arr[$buyer_name],0,10).'...' :$buyer_arr[$buyer_name]; ?></td>
										<td width="80" class="word_break_wrap"><? echo $int_ref;?></td>
										<td width="80" class="word_break_wrap"><? echo $client_nos;?></td>
										<td width="50"><p class="word_break_wrap"><? echo $job_nos;?></p></td>
										<td width="50" title="<? echo $style_ref_no; ?>"><p class="word_break_wrap"><? echo (strlen($style_ref_no) > 10) ? substr($style_ref_no,0,10).'...' :$style_ref_no; ?></p></td>
										<td width="70"><p><? echo chop($season_nos,",");?></p></td>
										<td width="70"><? echo $booking_no;?></td>
										<td width="50"><? echo $booking_date;?></td>
										<td width="80"><p class="word_break_wrap"><? echo $booking_type;?></p></td>
										<td width="50"><? echo $pay_mode_nos;?></td>
										<td width="100" ><p class="word_break_wrap"><? echo $pi_no;?></p></td>
										<td width="100" title="<? echo $lc_sc_no; ?>"><p class="word_break_wrap"><? echo (strlen($lc_sc_no) > 16) ? substr($lc_sc_no,0,16).'...' :$lc_sc_no; ?></p></td>
										<td width="70" title="<? echo $supplier; ?>"><p class="word_break_wrap"><? echo (strlen($supplier) > 16) ? substr($supplier,0,16).'...' :$supplier; ?></p></td>
										<td width="100" title="<? //echo $po_breakdown_id;?>"><a href="##" onClick="open_po_number('<? echo $po_number;?>','<? echo $prodStr;?>');">view</a></td>
										<td width="100" title="store"><? echo $store_arr[$prodStr[1]];?></td>
										<td width="100" title="batch"><? echo $prodStr[9];?></td>
										
										<td width="40"><? echo $prodStr[0];?></td>
										<td width="100" title="<? echo $body_part[$prodStr[2]];?>"><p class="word_break_wrap"><? echo (strlen($body_part[$prodStr[2]]) > 15) ? substr($body_part[$prodStr[2]],0,15).'...' :$body_part[$prodStr[2]]; ?></p></td>
										<td width="120"><p class="word_break_wrap"><? echo $constructionArr[$prodStr[3]];?></p></td>
										<td width="120" title="<? echo $composition_arr[$prodStr[3]]; ?>"><p class="word_break_wrap"><? 
										echo (strlen($composition_arr[$prodStr[3]]) > 25) ? substr($composition_arr[$prodStr[3]],0,25).'...' :$composition_arr[$prodStr[3]];?></p></td>
										<td width="40"><p class="word_break_wrap"><? echo $prodStr[5]; ?></p></td>
										<td width="40"><? echo $prodStr[4]; ?></td>
										<td width="100" title="<? echo $color_arr[$prodStr[6]]; ?>"><p class="word_break_wrap">
										<? echo (strlen($color_arr[$prodStr[6]]) > 15) ? substr($color_arr[$prodStr[6]],0,15).'...' :$color_arr[$prodStr[6]]; ?></p></td>
										<td width="40"><? echo $unit_of_measurement[$prodStr[7]]; ?></td>

										<td width="60" align="right"><? echo number_format($opening_qnty,2,".","");?></td>
										<td width="60" align="right">
											<a href="##" onClick="openmypage_qnty('<? echo $booking_no;?>','<? echo implode("*", $prodStr);?>','openmypage_receive','<? echo $start_date;?>','<? echo $end_date;?>');"><? echo number_format($recv_qnty,2,".","");?>
											</a>
										</td>
										<td width="60" align="right"><? echo number_format($inside_return,2,".","")?></td>
										<td width="60" align="right"><? echo number_format($outside_return,2,".","")?></td>
										<td width="60" align="right">
											<a href="##" onClick="openmypage_qnty('<? echo $booking_no;?>','<? echo implode("*", $prodStr);?>','openmypage_trans_in','<? echo $start_date;?>','<? echo $end_date;?>');"><? echo number_format($trans_in_qty,2,".","");?>
											</a>
										</td>
										<td width="60" align="right"><? echo number_format($tot_receive,2,".","")?></td>
										<td width="40" align="right"><p class="word_break_wrap"><? echo number_format($tot_receive_rate,2,".","");?></p></td>
										<td width="60" align="right"><? echo number_format($tot_receive_amount,2,".","");?></td>
										<td width="60" align="right"><a href="##" onClick="openmypage_qnty('<? echo $booking_no;?>','<? echo implode("*", $prodStr);?>','openmypage_cutting_inside','<? echo $start_date;?>','<? echo $end_date;?>');"><? echo number_format($cutting_inside,2,".","");?></a></td>
										<td width="60" align="right"><a href="##" onClick="openmypage_qnty('<? echo $booking_no;?>','<? echo implode("*", $prodStr);?>','openmypage_cutting_outside','<? echo $start_date;?>','<? echo $end_date;?>');"><? echo number_format($cutting_outside,2,".",""); ?></a></td>
										<td width="60" align="right"><? echo number_format($other_issue,2,".",""); ?></td>
										<td width="60" align="right"><? echo number_format($rcv_return_qnty,2,".","");?></td>
										<td width="60" align="right"><a href="##" onClick="openmypage_qnty('<? echo $booking_no;?>','<? echo implode("*", $prodStr);?>','openmypage_trans_out','<? echo $start_date;?>','<? echo $end_date;?>');"><? echo number_format($trans_out_qnty,2,".","");?></a></td>
										<td width="60" align="right"><? echo number_format($total_issue,2,".","");?></td>
										<td width="40" align="right"><p class="word_break_wrap"><? echo number_format($tot_issue_rate,2,".","");?></p></td>
										<td width="60" align="right"><? echo number_format($total_issue_amount,2,".","");?></td>
										<td width="60" align="right" title="<? //echo $stock_title;?>"><? echo number_format($stock_qnty,2,".","");?></td>
										<td width="40" align="right"><p class="word_break_wrap"><? echo number_format($stock_rate,2,".","");?></p></td>
										<td width="80" align="right"><? echo number_format($stock_amount,2,".","");?></td>
										<td width="50" align="center"><? echo $ageOfDays;?></td>
										<td width="50" align="center"><? echo $daysOnHand ?></td>
										<td width="50" align="center"><? echo $last_rcv_date ?></td>
										<td width="50" align="center"><? echo $last_issue ?></td>
									</tr>
									<?
									$i++;
									$uom_total_booking_qty+=$booking_qnty;
									$uom_total_opening_qnty+=$opening_qnty;
									$uom_total_recv_qnty+=$recv_qnty;
									$uom_total_inside_return+=$inside_return;
									$uom_total_outside_return+=$outside_return;
									$uom_total_trans_in_qty+=$trans_in_qty;
									$uom_total_tot_receive+=$tot_receive;
									$uom_total_cutting_inside_issue+=$cutting_inside;
									$uom_total_cutting_outside_issue+=$cutting_outside;
									$uom_total_other_issue+=$other_issue;
									$uom_total_rcv_return_qnty+=$rcv_return_qnty;
									$uom_total_trans_out_qnty+=$trans_out_qnty;
									$uom_total_total_issue+=$total_issue;
									$uom_total_total_issue_amount+=$total_issue_amount;
									$uom_total_stock_qnty+=$stock_qnty;
									$uom_total_stock_amount+=$stock_amount;
								}
							}
						}
					}
				}
				?>
			</table>
		</div>
		<table width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
			<tfoot>
				<th width="30">&nbsp;</th>
				<th width="90">&nbsp;</th>
				<th width="60">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="50">&nbsp;</th>
				<th width="50">&nbsp;</th>
				<th width="70">&nbsp;</th>
				<th width="70">&nbsp;</th>
				<th width="50">&nbsp;</th>
				<th width="80">&nbsp;</th>
				<th width="50">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="70">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="40">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="120">&nbsp;</th>
				<th width="120">&nbsp;</th>
				<th width="40">&nbsp;</th>
				<th width="40">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="40">Total</th>
				<th width="60" id="value_opening_stock" class="word_break_wrap">&nbsp;</th>
				<th width="60" id="value_rcv_qnty" class="word_break_wrap">&nbsp;</th>
				<th width="60" id="value_inside_iss_return" class="word_break_wrap">&nbsp;</th>
				<th width="60" id="value_out_iss_return" class="word_break_wrap">&nbsp;</th>
				<th width="60" id="value_trans_in" class="word_break_wrap">&nbsp;</th>
				<th width="60" id="value_total_rcv" class="word_break_wrap">&nbsp;</th>
				<th width="40">&nbsp;</th>
				<th width="60" id="value_total_receive_amount" class="word_break_wrap">&nbsp;</th>
				<th width="60" id="value_total_cutting_inside" class="word_break_wrap">&nbsp;</th>
				<th width="60" id="value_total_cutting_outside" class="word_break_wrap">&nbsp;</th>
				<th width="60" id="value_total_other_issue" class="word_break_wrap">&nbsp;</th>
				<th width="60" id="value_total_rcv_return" class="word_break_wrap">&nbsp;</th>
				<th width="60" id="value_total_transfer_out" class="word_break_wrap">&nbsp;</th>
				<th width="60" id="value_total_issue" class="word_break_wrap">&nbsp;</th>
				<th width="40">&nbsp;</th>
				<th width="60" id="value_issue_amount" class="word_break_wrap">&nbsp;</th>
				<th width="60" id="value_stock_qnty" class="word_break_wrap">&nbsp;</th>
				<th width="40">&nbsp;</th>
				<th width="80" id="value_stock_amount" class="word_break_wrap">&nbsp;</th>
				<th width="50">&nbsp;</th>
				<th width="50" class="word_break_wrap">&nbsp;</th>
				<th width="50" class="word_break_wrap">&nbsp;</th>
				<th width="50" class="word_break_wrap">&nbsp;</th>
			</tfoot>
		</table>
	</fieldset>
	<?
	//echo "Execution Time: " . (microtime(true) - $started) . "S";
	foreach (glob("$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename####$report_type";

	exit();
}

if($action=="report_generate_excel_only")
{
	$started = microtime(true);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$report_type 		= str_replace("'","",$cbo_report_type);
	$buyer_id 			= str_replace("'","",$cbo_buyer_id);
	$book_no 			= trim(str_replace("'","",$txt_book_no));
	$book_id 			= str_replace("'","",$txt_book_id);
	$job_no 			= trim(str_replace("'","",$txt_job_no));
	$txt_pi_no 			= trim(str_replace("'","",$txt_pi_no));
	$hdn_pi_id 			= trim(str_replace("'","",$hdn_pi_id));
	$txt_batch_no 		= trim(str_replace("'","",$txt_batch_no));

	$txt_file_no 		= str_replace("'","",$txt_file_no);
	$txt_ref_no 		= str_replace("'","",$txt_ref_no);
	$job_year 			= str_replace("'","",$cbo_year);
	$cbo_company_id 	= str_replace("'","",$cbo_company_id);
	$cbo_pay_mode 		= str_replace("'","",$cbo_pay_mode);
	$cbo_supplier_id 	= str_replace("'","",$cbo_supplier_id);
	$cbo_store_name 	= str_replace("'","",$cbo_store_name);
	$date_from 		 	= str_replace("'","",$txt_date_from);
	$date_to 		 	= str_replace("'","",$txt_date_to);
	$cbo_value_with 	= str_replace("'","",$cbo_value_with);

	$get_upto 			= str_replace("'","",$cbo_get_upto);
	$txt_days 			= str_replace("'","",$txt_days);
	$get_upto_qnty 		= str_replace("'","",$cbo_get_upto_qnty);
	$txt_qnty 			= str_replace("'","",$txt_qnty);

	if($cbo_store_name > 0){
		$store_cond = " and b.store_id in ($cbo_store_name)";
		$store_cond_2 = " and c.store_id in ($cbo_store_name)";
	}
	
	if($txt_batch_no)
	{
		$batch_cond = " and e.batch_no like '%$txt_batch_no%'";
	}

	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and d.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and d.buyer_id=$buyer_id";
	}

	if($db_type==0)
	{
		if($job_year==0) $year_cond=""; else $year_cond=" and YEAR(f.insert_date)=$job_year";
	}
	else if($db_type==2)
	{
		if($job_year==0) $year_cond=""; else $year_cond=" and to_char(f.insert_date,'YYYY')=$job_year";
	}

	$date_cond="";
	if($date_from!="" && $date_to!="")
	{
		if($db_type==0)$start_date=change_date_format($date_from,"yyyy-mm-dd","");
		else if($db_type==2) $start_date=change_date_format($date_from,"","",1);

		if($db_type==0)$end_date=change_date_format($date_to,"yyyy-mm-dd","");
		else if($db_type==2) $end_date=change_date_format($date_to,"","",1);

		$date_cond   = " and b.transaction_date <= '$end_date'";
		$date_cond_2 = " and c.transaction_date <= '$end_date'";
	}

	$company_arr 	= return_library_array("select id, company_name from lib_company where status_active=1","id","company_name");
	$supplier_arr 	= return_library_array("select id,short_name from lib_supplier where status_active=1","id","short_name");
	$buyer_arr 		= return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$season_arr 	= return_library_array( "select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0",'id','season_name');
	$store_arr 		= return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$color_arr 		= return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$booking_type_arr = array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
	$conversion_rate=return_field_value("conversion_rate","currency_conversion_rate","is_deleted=0 and status_active=1 and id=(select max(id) from currency_conversion_rate where currency=2 and is_deleted=0 and status_active=1 and company_id in ($cbo_company_id) )","",$con);

	$pi_no_cond="";
	if ($hdn_pi_id=="")
	{
		$pi_no_cond="";
	}
	else
	{
		$pi_no_cond=" and a.booking_id = '$hdn_pi_id' and a.receive_basis=1 ";
		$pi_no_trans_cond = " and a.id = 0";
	}

	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and f.job_no_prefix_num in ($job_no) ";
	if ($book_no=="") $booking_no_cond=""; else $booking_no_cond=" and d.booking_no_prefix_num='$book_no'";
	if($cbo_supplier_id ==0) $supplier_cond = ""; else $supplier_cond = " and d.supplier_id = ".$cbo_supplier_id;
	if($cbo_pay_mode ==0) $pay_mode_cond = ""; else $pay_mode_cond = " and d.pay_mode = ".$cbo_pay_mode;

	if($job_no != "" || $book_no!="" || $cbo_supplier_id !=0 || $buyer_id!=0 || $cbo_pay_mode !=0)
	{
		$serch_ref_sql_1 = "select c.booking_no from wo_booking_dtls c, wo_booking_mst d, wo_po_break_down e, wo_po_details_master f where c.status_active=1 and e.status_active=1 and f.job_no=e.job_no_mst and c.booking_type in (1,4,3) and c.booking_no=d.booking_no and c.po_break_down_id=e.id and f.company_name in ($cbo_company_id) $buyer_id_cond $job_no_cond $booking_no_cond $year_cond $pay_mode_cond $supplier_cond ";

		$concate="";
		if($job_no == "")
		{
			$concate = " union all ";
			$serch_ref_sql_2 = " select d.booking_no from wo_non_ord_samp_booking_mst d where d.booking_type = 4 and d.company_id in ($cbo_company_id) $booking_no_cond $pay_mode_cond $supplier_cond $buyer_id_cond ";
		}
		$serch_ref_sql = $serch_ref_sql_1.$concate.$serch_ref_sql_2;

		$serch_ref_result = sql_select($serch_ref_sql);

		foreach ($serch_ref_result as $val)
		{
			$search_book_arr[$val[csf("booking_no")]] = $val[csf("booking_no")];
		}

		if(empty($search_book_arr))
		{
			echo "<p style='font-weight:bold;text-align:center;font-size:20px;'>Booking No not found</p>";
			die;
		}
	}

	if(!empty($search_book_arr))
	{
		$search_book_nos="'".implode("','",$search_book_arr)."'";
		$search_book_arr = explode(",", $search_book_nos);

		$all_book_nos_cond=""; $bookCond="";
		if($db_type==2 && count($search_book_arr)>999)
		{
			$all_search_book_arr_chunk=array_chunk($search_book_arr,999) ;
			foreach($all_search_book_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$bookCond.="  e.booking_no in($chunk_arr_value) or ";
			}

			$all_book_nos_cond.=" and (".chop($bookCond,'or ').")";
		}
		else
		{
			$all_book_nos_cond=" and e.booking_no in($search_book_nos)";
		}
	}

	$rcv_sql = "SELECT b.id as ID, e.booking_no as BOOKING_NO, e.booking_no_id as BOOKING_NO_ID, e.booking_without_order as BOOKING_WITHOUT_ORDER, a.company_id as COMPANY_ID,a.receive_basis as  RECEIVE_BASIS, a.knitting_source as KNITTING_SOURCE, a.knitting_company as KNITTING_COMPANY,a.booking_id as WO_PI_PROD_ID,a.booking_no as WO_PI_PROD_NO, b.transaction_date as TRANSACTION_DATE, b.prod_id as PROD_ID, b.store_id as STORE_ID, c.body_part_id as BODY_PART_ID, c.fabric_description_id as FABRIC_DESCRIPTION_ID, c.gsm as GSM, c.width as WIDTH, f.color as COLOR_ID, b.cons_uom as CONS_UOM,listagg(c.dia_width_type,',') within group (order by c.dia_width_type) as DIA_WIDTH_TYPE, listagg(d.po_breakdown_id,',') within group (order by d.po_breakdown_id) as PO_BREAKDOWN_ID, b.cons_quantity as QUANTITY, b.order_rate as ORDER_RATE, b.order_amount as ORDER_AMOUNT, b.pi_wo_batch_no as PI_WO_BATCH_NO, a.lc_sc_no as LC_SC_NO, e.batch_no as BATCH_NO, a.ENTRY_FORM
	FROM inv_receive_master a, inv_transaction b, pro_finish_fabric_rcv_dtls c left join order_wise_pro_details d on c.trans_id = d.trans_id and c.id = d.dtls_id and entry_form in(7,37) and d.po_breakdown_id <>0 and d.trans_id <>0, pro_batch_create_mst e, product_details_master f
	WHERE a.company_id in ($cbo_company_id) and a.id = b.mst_id and b.id=c.trans_id and b.transaction_type=1 and a.entry_form in(7,37) and c.trans_id <>0 and a.status_active =1 and b.status_active =1 and c.is_sales=0 and c.status_active =1 and b.pi_wo_batch_no=e.id and b.prod_id = f.id $store_cond $date_cond  $all_book_nos_cond $pi_no_cond $batch_cond
	group by b.id,e.booking_no,e.booking_no_id, e.booking_without_order, a.company_id,a.receive_basis, a.knitting_source,a.knitting_company, a.booking_id, a.booking_no, b.transaction_date, b.prod_id, b.store_id, c.body_part_id, c.fabric_description_id, c.gsm, c.width, f.color ,b.cons_uom,c.dia_width_type,b.cons_quantity, b.order_rate, b.order_amount, b.pi_wo_batch_no, a.lc_sc_no, e.batch_no, a.ENTRY_FORM order by a.company_id, b.pi_wo_batch_no"; 
	//echo $rcv_sql;
	$rcv_data = sql_select($rcv_sql);
	foreach ($rcv_data as  $val)
	{
		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val['TRANSACTION_DATE']));
		$ref_str="";
		$dia_width_type_ref = implode(",",array_unique(explode(",", $val["DIA_WIDTH_TYPE"])));

		$ref_str = $val["PROD_ID"]."*".$val["STORE_ID"]."*".$val["BODY_PART_ID"]."*".$val["FABRIC_DESCRIPTION_ID"]."*".$val["GSM"]."*".$val["WIDTH"]."*".$val["COLOR_ID"]."*".$val["CONS_UOM"]."*".$val["PI_WO_BATCH_NO"]."*".$val["BATCH_NO"];
		
		$rate_usd=$order_amount=0;
		if ($val["ENTRY_FORM"]==7) 
		{
			$rate_usd=$val["ORDER_RATE"]/$conversion_rate; // rate for usd
			$order_amount=$val["ORDER_AMOUNT"]/$conversion_rate; // for usd
		}
		else
		{
			$rate_usd=$val["ORDER_RATE"];
			$order_amount=$val["ORDER_AMOUNT"];
		}
		if($transaction_date >= $date_frm)
		{
			$data_array[$val["CONS_UOM"]][$val["BOOKING_NO"]][$ref_str] .= $val["QUANTITY"]."*".$rate_usd."*".$val["RECEIVE_BASIS"]."*".$val["WO_PI_PROD_NO"]."*".$dia_width_type_ref."*".$val["LC_SC_NO"]."*"."1*1__";
		}
		else
		{
			$data_array[$val["CONS_UOM"]][$val["BOOKING_NO"]][$ref_str] .= $val["QUANTITY"]."*".$rate_usd."*".$val["RECEIVE_BASIS"]."*".$val["WO_PI_PROD_NO"]."*".$dia_width_type_ref."*".$val["LC_SC_NO"]."*"."1*2__";
		}
		$all_prod_id[$val["PROD_ID"]] = $val["PROD_ID"];

		if($val["BOOKING_WITHOUT_ORDER"] == 0)
		{
			$all_po_id_arr[$val["PO_BREAKDOWN_ID"]] = $val["PO_BREAKDOWN_ID"];
			$po_array[$val["BOOKING_NO"]][$ref_str]["po_no"] .= $val["PO_BREAKDOWN_ID"].",";
		}

		$book_str = explode("-", $val["BOOKING_NO"]);
		if($val["BOOKING_WITHOUT_ORDER"] == 1 || $book_str[1] == "SMN")
		{
			$all_samp_book_arr[$val["BOOKING_NO"]] = "'".$val["BOOKING_NO"]."'";
		}
		$booking_no_arr[$val["BOOKING_NO"]] = "'".$val["BOOKING_NO"]."'";
		$batch_id_arr[$val["PI_WO_BATCH_NO"]] = $val["PI_WO_BATCH_NO"];

		$rate_arr_booking_and_product_wise[$val["BOOKING_NO"]][$val["PROD_ID"]][$val["STORE_ID"]]["quantity"] += $val["QUANTITY"];
		$rate_arr_booking_and_product_wise[$val["BOOKING_NO"]][$val["PROD_ID"]][$val["STORE_ID"]]["amount"] += $order_amount;
		
	}
	unset($rcv_data);
	/* echo "<pre>";
	print_r($data_array);die; */

	if ($hdn_pi_id=="")
	{
		$trans_in_sql = "SELECT c.transaction_date as TRANSACTION_DATE, c.pi_wo_batch_no as PI_WO_BATCH_NO, e.batch_no as BATCH_NO, e.booking_no as BOOKING_NO, e.booking_no_id as BOOKING_NO_ID, e.booking_without_order as BOOKING_WITHOUT_ORDER, c.body_part_id as BODY_PART_ID, c.prod_id as PROD_ID, c.store_id as STORE_ID, d.detarmination_id as DETARMINATION_ID, d.gsm as GSM, d.dia_width as WIDTH, d.color as COLOR_ID, c.cons_uom as  CONS_UOM, sum(c.cons_quantity) as QUANTITY, c.order_rate as ORDER_RATE, c.order_amount as ORDER_AMOUNT, listagg(f.po_breakdown_id,',') within group (order by f.po_breakdown_id) as PO_BREAKDOWN_ID, b.batch_id as BATCH_ID, b.from_store as FROM_STORE, b.from_prod_id as FROM_PROD_ID
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c left join order_wise_pro_details f on c.id = f.trans_id and f.trans_type=5 and f.status_active=1 and f.po_breakdown_id<>0, product_details_master d, pro_batch_create_mst e
		where a.id=b.mst_id and b.to_trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($cbo_company_id) and c.item_category=2 and c.transaction_type=5 and a.status_active=1 and b.status_active=1 and c.status_active=1  and a.entry_form in (14,15,306) $store_cond_2 $date_cond_2 $all_book_nos_cond $batch_cond
		group by c.transaction_date, c.pi_wo_batch_no, e.batch_no, e.booking_no, e.booking_no_id, e.booking_without_order, c.company_id, c.body_part_id, c.prod_id,c.store_id, d.detarmination_id, d.gsm, d.dia_width, d.color, c.cons_uom, c.order_rate, c.order_amount, b.batch_id, b.from_store, b.from_prod_id order by c.company_id, c.pi_wo_batch_no";
		//echo $trans_in_sql;
		$trans_in_data = sql_select($trans_in_sql);
		foreach ($trans_in_data as  $val)
		{
			$date_frm=date('Y-m-d',strtotime($start_date));
			$transaction_date=date('Y-m-d',strtotime($val['TRANSACTION_DATE']));
			$ref_str="";

			$ref_str = $val["PROD_ID"]."*".$val["STORE_ID"]."*".$val["BODY_PART_ID"]."*".$val["DETARMINATION_ID"]."*".$val["GSM"]."*".$val["WIDTH"]."*".$val["COLOR_ID"]."*".$val["CONS_UOM"]."*".$val["PI_WO_BATCH_NO"]."*".$val["BATCH_NO"];

			if($transaction_date >= $date_frm)
			{
				$data_array[$val["CONS_UOM"]][$val["BOOKING_NO"]][$ref_str] .= $val["QUANTITY"]."*".$val["ORDER_RATE"]."*"."*".""."*".""."*"."*5*1__";
			}
			else
			{
				$data_array[$val["CONS_UOM"]][$val["BOOKING_NO"]][$ref_str] .= $val["QUANTITY"]."*".$val["ORDER_RATE"]."*"."*".""."*".""."*"."*5*2__";
			}

			$all_prod_id[$val["PROD_ID"]] = $val["PROD_ID"];

			if($val["BOOKING_WITHOUT_ORDER"] == 0)
			{
				$all_po_id_arr[$val["PO_BREAKDOWN_ID"]] = $val["PO_BREAKDOWN_ID"];
				$po_array[$val["BOOKING_NO"]][$ref_str]["po_no"] .= $val["PO_BREAKDOWN_ID"].",";
			}

			$book_str = explode("-", $val["BOOKING_NO"]);
			if($val["BOOKING_WITHOUT_ORDER"] == 1 || $book_str[1] == "SMN")
			{
				$all_samp_book_arr[$val["BOOKING_NO"]] = "'".$val["BOOKING_NO"]."'";
			}
			$booking_no_arr[$val["BOOKING_NO"]] = "'".$val["BOOKING_NO"]."'";
			$batch_id_arr[$val["PI_WO_BATCH_NO"]] = $val["PI_WO_BATCH_NO"];

			$rate_arr_booking_and_product_wise[$val["BOOKING_NO"]][$val["PROD_ID"]][$val["STORE_ID"]]["quantity"] += $val["QUANTITY"];
			$rate_arr_booking_and_product_wise[$val["BOOKING_NO"]][$val["PROD_ID"]][$val["STORE_ID"]]["amount"] += $val["ORDER_AMOUNT"];

			if($rate_arr_booking_and_product_wise[$val["BOOKING_NO"]][$val["PROD_ID"]][$val["STORE_ID"]]["amount"]*1 ==0)
			{
				$all_trans_in_batch[$val["BATCH_ID"]] = $val["BATCH_ID"];
				$trans_in_batch_prod_store[$val["BOOKING_NO"].'*'.$val["PROD_ID"].'*'.$val["STORE_ID"]] .= $val["BATCH_ID"].'*'.$val["FROM_PROD_ID"].'*'.$val["FROM_STORE"].",";
			}
		}
		unset($trans_in_data);
	}
	/* echo "<pre>";
	print_r($data_array);die; */
	if(!empty($data_array))	
	{
		$con = connect();
		$r_id3=execute_query("delete from tmp_booking_no where userid=$user_id");
		$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (770,771,772,773)");
		if($r_id3 && $r_id6)
		{
			oci_commit($con);
		}
	}

	$all_trans_in_batch = array_filter($all_trans_in_batch);
	if(!empty($all_trans_in_batch))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 770, 1,$all_trans_in_batch, $empty_arr);//PO ID

		$rcv_rate_for_transin_sql = sql_select("SELECT b.prod_id as PROD_ID, b.store_id as STORE_ID, b.pi_wo_batch_no as PI_WO_BATCH_NO, sum(b.cons_quantity) as QUANTITY,  sum(b.order_amount) as ORDER_AMOUNT from inv_receive_master a, inv_transaction b, GBL_TEMP_ENGINE g where a.id=b.mst_id and b.transaction_type=1 and a.entry_form=37 and a.status_active =1 and b.status_active =1 and a.is_deleted=0 and b.is_deleted=0 and b.pi_wo_batch_no=g.ref_val and g.user_id=$user_id and g.entry_form=770 group by b.prod_id, b.store_id, b.pi_wo_batch_no"); 
		//$all_trans_in_batch_nos_cond
		foreach ($rcv_rate_for_transin_sql as $val) 
		{
			$rcv_rate_for_transin_arr[$val["PI_WO_BATCH_NO"].'*'.$val["PROD_ID"].'*'.$val["STORE_ID"]]['QUANTITY'] += $val["QUANTITY"];
			$rcv_rate_for_transin_arr[$val["PI_WO_BATCH_NO"].'*'.$val["PROD_ID"].'*'.$val["STORE_ID"]]['ORDER_AMOUNT'] += $val["ORDER_AMOUNT"];
		}
		unset($rcv_rate_for_transin_sql);

		/*echo "<pre>";
		print_r($rcv_rate_for_transin_arr);
		die;*/

		foreach ($trans_in_batch_prod_store as $transInStr => $RcvStr) 
		{
			$transInArr = explode("*", $transInStr);
			$RcvStrArr = array_unique(explode(",",chop($RcvStr,",")));
			foreach ($RcvStrArr as $val) 
			{
				$RcvStrVal = explode("*", $val);

				$rate_arr_booking_and_product_wise[$transInArr[0]][$transInArr[1]][$transInArr[2]]["quantity"] += $rcv_rate_for_transin_arr[$RcvStrVal[0].'*'.$RcvStrVal[1].'*'.$RcvStrVal[2]]['QUANTITY'];
				$rate_arr_booking_and_product_wise[$transInArr[0]][$transInArr[1]][$transInArr[2]]["amount"] += $rcv_rate_for_transin_arr[$RcvStrVal[0].'*'.$RcvStrVal[1].'*'.$RcvStrVal[2]]["ORDER_AMOUNT"];
			}
		}
	}

	$all_po_id_arr = array_filter($all_po_id_arr);
	$all_po_id_arr = array_unique(explode(",",implode(",", $all_po_id_arr)));
	if(!empty($all_po_id_arr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 773, 1,$all_po_id_arr, $empty_arr);//PO ID

		$booking_sql = sql_select("SELECT a.body_part_id as BODY_PART_ID,c.booking_no as BOOKING_NO,a.lib_yarn_count_deter_id as LIB_YARN_COUNT_DETER_ID, c.fabric_color_id as FABRIC_COLOR_ID, c.gmts_color_id as GMTS_COLOR_ID, c.color_type as COLOR_TYPE, d.booking_date as BOOKING_DATE, d.pay_mode as PAY_MODE, d.booking_type as BOOKING_TYPE, d.entry_form as ENTRY_FORM, d.is_short as IS_SHORT, f.company_name as COMPANY_NAME, f.job_no as JOB_NO, f.style_ref_no as STYLE_REF_NO, f.buyer_name as BUYER_NAME, f.client_id as CLIENT_ID, f.season_buyer_wise as SEASON_BUYER_WISE, f.total_set_qnty as TOTAL_SET_QNTY, f.job_quantity as JOB_QUANTITY, c.fin_fab_qnty as FIN_FAB_QNTY, a.uom as UOM, c.rate as RATE, d.supplier_id as SUPPLIER_ID, e.GROUPING
		from wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls c, wo_booking_mst d, wo_po_break_down e, wo_po_details_master f, GBL_TEMP_ENGINE g
		where a.job_no=c.job_no and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and e.status_active=1 and a.id = c.pre_cost_fabric_cost_dtls_id and  f.job_no = e.job_no_mst and c.booking_type in(1) and c.booking_no = d.booking_no and c.po_break_down_id = e.id and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=773 
		union all
		select b.body_part_id as BODY_PART_ID, c.booking_no as BOOKING_NO, b.lib_yarn_count_deter_id as LIB_YARN_COUNT_DETER_ID, c.fabric_color_id as FABRIC_COLOR_ID , c.gmts_color_id as GMTS_COLOR_ID,c.color_type as COLOR_TYPE, d.booking_date as BOOKING_DATE, d.pay_mode as PAY_MODE, d.booking_type as BOOKING_TYPE, d.entry_form as ENTRY_FORM, d.is_short as IS_SHORT,f.company_name as COMPANY_NAME, f.job_no as JOB_NO, f.style_ref_no as STYLE_REF_NO, f.buyer_name as BUYER_NAME, f.client_id as CLIENT_ID, f.season_buyer_wise as SEASON_BUYER_WISE,f.total_set_qnty as TOTAL_SET_QNTY, f.job_quantity as JOB_QUANTITY, c.fin_fab_qnty as FIN_FAB_QNTY, b.uom as UOM, c.rate as RATE, d.supplier_id as SUPPLIER_ID, e.GROUPING
		from wo_pre_cost_fab_conv_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_booking_dtls c ,  wo_booking_mst d , wo_po_break_down e, wo_po_details_master f, GBL_TEMP_ENGINE g
		where b.job_no=c.job_no and a.status_active=1 and b.status_active=1 and c.status_active=1 and e.status_active=1 and a.id = c.pre_cost_fabric_cost_dtls_id and  f.job_no = e.job_no_mst and a.fabric_description = b.id and c.booking_type in(3,4) and c.booking_no = d.booking_no  and c.po_break_down_id = e.id and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=773 ");

		//$all_po_id_cond

		foreach ($booking_sql as  $val)
		{
			$book_po_ref[$val["BOOKING_NO"]]["company_name"] 	= $val["COMPANY_NAME"];
			$book_po_ref[$val["BOOKING_NO"]]["buyer_name"] 	= $val["BUYER_NAME"];
			$book_po_ref[$val["BOOKING_NO"]]["job_no"] 		.= $val["JOB_NO"].",";
			$book_po_ref[$val["BOOKING_NO"]]["int_ref"] 		.= $val["GROUPING"].",";
			$book_po_ref[$val["BOOKING_NO"]]["client_id"] 		= $val["CLIENT_ID"];
			$book_po_ref[$val["BOOKING_NO"]]["season"] 		.= $val["SEASON_BUYER_WISE"].",";
			$book_po_ref[$val["BOOKING_NO"]]["style_ref_no"] 	.= $val["STYLE_REF_NO"].",";
			$book_po_ref[$val["BOOKING_NO"]]["booking_no"] 	= $val["BOOKING_NO"];
			$book_po_ref[$val["BOOKING_NO"]]["booking_date"] 	= $val["BOOKING_DATE"];
			$book_po_ref[$val["BOOKING_NO"]]["pay_mode"] 		= $pay_mode[$val["PAY_MODE"]];
			if($val["PAY_MODE"] == 3 || $val["PAY_MODE"] == 5)
			{
				$book_po_ref[$val["BOOKING_NO"]]["supplier"] = $company_arr[$val["SUPPLIER_ID"]];
			}else{
				$book_po_ref[$val["BOOKING_NO"]]["supplier"] = $supplier_arr[$val["SUPPLIER_ID"]];
			}

			$job_qnty_arr[$val["JOB_NO"]]["qnty"] = $val["JOB_QUANTITY"]*$val["TOTAL_SET_QNTY"];
			$book_po_ref[$val["BOOKING_NO"]][$val["BODY_PART_ID"]][$val["LIB_YARN_COUNT_DETER_ID"]][$val["FABRIC_COLOR_ID"]]["qnty"] += $val["FIN_FAB_QNTY"];
			$book_po_ref[$val["BOOKING_NO"]][$val["BODY_PART_ID"]][$val["LIB_YARN_COUNT_DETER_ID"]][$val["FABRIC_COLOR_ID"]]["color_type"] .= $color_type[$val["COLOR_TYPE"]].",";

			$book_po_ref[$val["BOOKING_NO"]][$val["BODY_PART_ID"]][$val["LIB_YARN_COUNT_DETER_ID"]][$val["FABRIC_COLOR_ID"]]["amount"] += $val["FIN_FAB_QNTY"]*$val["RATE"];

			$bookingType="";
			if($val['BOOKING_TYPE'] == 4)
			{
				$bookingType = "Sample With Order";
			}
			else if($val['BOOKING_TYPE'] == 3)
			{
				$bookingType = "Service Booking";
			}
			else
			{
				$bookingType = $booking_type_arr[$val['ENTRY_FORM']];
			}
			$book_po_ref[$val["BOOKING_NO"]]["booking_type"] = $bookingType;
		}
		unset($booking_sql);
	}
	/*echo "<pre>";
	print_r($book_po_ref);*/

	if(!empty($all_samp_book_arr))
	{
		foreach ($all_samp_book_arr as $s_book) {
			$rID2=execute_query("insert into tmp_booking_no (userid, booking_no) values ($user_id,".$s_book.")");
		}
		if($rID2)
		{
			oci_commit($con);
		}

		//$all_samp_book_ids = implode(",", $all_samp_book_arr);
		$non_samp_sql = sql_select("select a.booking_date, a.booking_no, a.pay_mode, a.company_id, a.supplier_id, b.lib_yarn_count_deter_id, b.gmts_color,b.uom, b.color_type_id, b.body_part, a.buyer_id, b.style_des from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, tmp_booking_no c where a.booking_no=b.booking_no and b.status_active =1 and a.booking_type =4 and a.booking_no=c.booking_no and c.userid=$user_id"); //and a.id in ($all_samp_book_ids) // $all_samp_book_nos_cond 

		foreach ($non_samp_sql as  $val)
		{
			$book_po_ref[$val[csf("booking_no")]]["booking_no"]   	= $val[csf("booking_no")];
			$book_po_ref[$val[csf("booking_no")]]["booking_date"]  	= $val[csf("booking_date")];
			$book_po_ref[$val[csf("booking_no")]]["company_name"] 	= $val[csf("company_id")];
			$book_po_ref[$val[csf("booking_no")]]["buyer_name"] 	= $val[csf("buyer_id")];
			$book_po_ref[$val[csf("booking_no")]]["style_ref_no"] 	= $val[csf("style_des")];
			$book_po_ref[$val[csf("booking_no")]]["booking_type"] 	= "Sample WithOut Order";
			if($val[csf("pay_mode")] == 3 || $val[csf("pay_mode")] 	== 5)
			{
				$book_po_ref[$val[csf("booking_no")]]["supplier"] = $company_arr[$val[csf("supplier_id")]];
			}else{
				$book_po_ref[$val[csf("booking_no")]]["supplier"] = $supplier_arr[$val[csf("supplier_id")]];
			}
		}
		unset($non_samp_sql);
	}

	$batch_id_arr = array_filter($batch_id_arr);
	if(!empty($batch_id_arr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 771, 1,$batch_id_arr, $empty_arr);//All Batch ID
	}
	/* echo "<pre>";
	print_r($data_array);die; */
	$issRtnSql = "select c.transaction_date, d.knit_dye_source, b.body_part_id, b.prod_id,c.store_id, b.fabric_description_id, b.gsm, b.width, f.color as color_id,c.cons_uom, c.cons_quantity as quantity, c.order_rate, b.batch_id, e.batch_no, e.booking_no, e.booking_without_order from inv_receive_master a, pro_finish_fabric_rcv_dtls b,inv_transaction c, inv_issue_master d, pro_batch_create_mst e, product_details_master f, GBL_TEMP_ENGINE g  where a.id = b.mst_id and b.trans_id=c.id and c.issue_id=d.id and a.entry_form=52 and a.item_category=2 and c.pi_wo_batch_no = e.id and c.prod_id=f.id and a.status_active =1 and b.status_active=1 and c.status_active =1 and c.company_id in  ($cbo_company_id) $store_cond_2 $date_cond_2 and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=771 ";
	//$all_batch_ids_cond
	$issRtnData = sql_select($issRtnSql);
	foreach ($issRtnData as $val)
	{

		$issRtnRef_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("fabric_description_id")]."*".$val[csf("gsm")]."*".$val[csf("width")]."*".$val[csf("color_id")]."*".$val[csf("cons_uom")]."*".$val[csf("batch_id")]."*".$val[csf("batch_no")];


		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
		$ref_str="";
		if($transaction_date >= $date_frm)
		{
			if($val[csf("knit_dye_source")] == 1)
			{
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["inside_return"] += $val[csf("quantity")];
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["inside_return_amount"] += $val[csf("quantity")]*$val[csf("order_rate")];
			}
			else
			{
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["outside_return"] += $val[csf("quantity")];
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["outside_return_amount"] += $val[csf("quantity")]*$val[csf("order_rate")];
			}
		}
		else
		{
			$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["opening"] += $val[csf("quantity")];
			$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["opening_amount"] +=$val[csf("quantity")]*$val[csf("order_rate")];
		}
	}
	unset($issRtnData);

	
	$issue_sql = sql_select("select a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, c.cons_uom, c.store_id, c.cons_quantity, c.id as trans_id,c.transaction_date, d.detarmination_id, d.gsm, d.dia_width, d.color, c.pi_wo_batch_no, e.batch_no, e.booking_no, e.booking_without_order, round(c.order_rate,2) as order_rate from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE g where a.id=b.mst_id and b.trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no= e.id and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2 and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=771 and a.entry_form=18 and c.status_active=1 and b.status_active=1 and a.status_active=1 and c.item_category=2 and c.transaction_type =2 group by a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, c.cons_uom, c.store_id, c.cons_quantity, c.id, c.transaction_date, d.detarmination_id, d.gsm, d.dia_width, d.color, c.pi_wo_batch_no, e.batch_no, e.booking_no, e.booking_without_order, round(c.order_rate,2)");
	//$all_batch_ids_cond
	
	foreach ($issue_sql as $val)
	{
		$issRef_str="";
		$issRef_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")]."*".$val[csf("pi_wo_batch_no")]."*".$val[csf("batch_no")];


		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
		
		if($transaction_date >= $date_frm)
		{
			if($val[csf("issue_purpose")] == 9)
			{
				if($val[csf("knit_dye_source")] == 1)
				{
					$issue_data[$val[csf("booking_no")]][$issRef_str]["cutting_inside"] += $val[csf("cons_quantity")];
				}
				else
				{
					$issue_data[$val[csf("booking_no")]][$issRef_str]["cutting_outside"] += $val[csf("cons_quantity")];
				}
			}
			else
			{
				$issue_data[$val[csf("booking_no")]][$issRef_str]["other_issue"] += $val[csf("cons_quantity")];
			}
			$issue_data[$val[csf("booking_no")]][$issRef_str]["issue_amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
		}
		else
		{
			$issue_data[$val[csf("booking_no")]][$issRef_str]["opening_issue"] += $val[csf("cons_quantity")];
			$issue_data[$val[csf("booking_no")]][$issRef_str]["opening_issue_amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
		}
	}
	unset($issue_sql);
	/*echo "<pre>";
	print_r($issue_data);
	die;*/
	
	$rcvRtnSql = sql_select("select c.transaction_date, c.company_id, c.prod_id, c.store_id, c.cons_quantity, c.cons_uom, d.detarmination_id, d.gsm, d.dia_width, d.color, e.booking_no, e.booking_without_order, b.body_part_id, c.pi_wo_batch_no, e.batch_no from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE g where a.id = b.mst_id and b.trans_id=c.id and a.entry_form =46 and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2 and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=771 and c.prod_id=d.id and c.pi_wo_batch_no=e.id and a.status_active =1 and b.status_active =1 and c.status_active =1");
	//$all_batch_ids_cond

	foreach ($rcvRtnSql as $val)
	{
		$rcvRtn_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")]."*".$val[csf("pi_wo_batch_no")]."*".$val[csf("batch_no")];

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
		$ref_str="";
		if($transaction_date >= $date_frm)
		{
			$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["qnty"] += $val[csf("cons_quantity")];
			$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
		}
		else
		{
			$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["opening_qnty"] += $val[csf("cons_quantity")];
			$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["opening_amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
		}
	}
	unset($rcvRtnSql);
	
	$transOutSql = sql_select("SELECT c.transaction_date,c.pi_wo_batch_no, e.batch_no,e.booking_no, e.booking_no_id, e.booking_without_order, c.body_part_id, c.prod_id, c.store_id, d.detarmination_id, d.gsm, d.dia_width, d.color, c.cons_uom, c.cons_quantity,c.order_rate from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c,product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE g where a.id=b.mst_id and b.trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2 and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=771 and c.item_category=2 and c.transaction_type=6 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.entry_form in (14,15,306) and b.trans_id=c.id and b.active_dtls_id_in_transfer=1"); //$all_batch_ids_cond

	foreach ($transOutSql as $val)
	{
		$transOut_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")]."*".$val[csf("pi_wo_batch_no")]."*".$val[csf("batch_no")];

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
		$ref_str="";
		if($transaction_date >= $date_frm)
		{
			$trans_out_data[$val[csf("booking_no")]][$transOut_str]["qnty"] += $val[csf("cons_quantity")];
			$trans_out_data[$val[csf("booking_no")]][$transOut_str]["amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
		}
		else
		{
			$trans_out_data[$val[csf("booking_no")]][$transOut_str]["opening_qnty"] += $val[csf("cons_quantity")];
			$trans_out_data[$val[csf("booking_no")]][$transOut_str]["opening_amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
		}
	}
	unset($transOutSql);
	
    /*echo "<pre>";
    print_r($trans_out_data);
    die;*/

    $composition_arr=array();
    $sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id ";
    $data_deter=sql_select($sql_deter);

    if(count($data_deter)>0)
    {
    	foreach( $data_deter as $row )
    	{
    		if(array_key_exists($row[csf('id')],$composition_arr))
    		{
    			$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
    			$constructionArr[$row[csf('id')]]=$constructionArr[$row[csf('id')]];
    			list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
    			$copmpositionArr[$row[csf('id')]]=$cps;
    		}
    		else
    		{
    			$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
    			$constructionArr[$row[csf('id')]]=$row[csf('construction')];
    			list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
    			$copmpositionArr[$row[csf('id')]]=$cps;
    		}
    	}
    }
    unset($data_deter);

    if(!empty($all_prod_id))
    {
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 772, 1,$all_prod_id, $empty_arr);

    	$transaction_date_array=array();
    	if($all_prod_id_cond!=""){
    		$sql_date="SELECT c.booking_no, a.prod_id, min(a.transaction_date) as min_date, max(a.transaction_date) as max_date from inv_transaction a,pro_batch_create_mst c, GBL_TEMP_ENGINE g where a.pi_wo_batch_no=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=2 and a.prod_id=g.ref_val and g.entry_form=772 and g.user_id=$user_id group by c.booking_no,a.prod_id"; //$all_prod_id_cond 

    		$sql_date_result=sql_select($sql_date);
    		foreach( $sql_date_result as $row )
    		{
    			$transaction_date_array[$row[csf('booking_no')]][$row[csf('prod_id')]]['min_date']=$row[csf('min_date')];
    			$transaction_date_array[$row[csf('booking_no')]][$row[csf('prod_id')]]['max_date']=$row[csf('max_date')];
    		}
    		unset($sql_date_result);
    	}
    }

    $r_id3=execute_query("delete from tmp_booking_no where userid=$user_id");
	$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (770,771,772,773)");
	if($r_id3 && $r_id6)
	{
		oci_commit($con);
	}
    
	/* echo "<pre>";
	print_r($data_array);
	die; */

	$table_width = "3870";
	$col_span = "26";

	$html = "";
	$html .= '<table cellpadding="0" cellspacing="0" width="2080">
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="23" style="font-size:18px"><strong>'. $report_title .'</strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="23" style="font-size:16px"><strong>'. $company_arr[str_replace("'","",$cbo_company_id)] .'</strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="23" style="font-size:14px"><strong>'; 
				if($date_from!="") {
					$html .= "From : ".change_date_format(str_replace("'","",$txt_date_from));
				}
			$html .='</strong></td>
			</tr>
		</table>
		<table width="'. $table_width .'" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="90">LC Company</th>
				<th width="60">Buyer</th>
				<th width="80">Internal Ref.</th>
				<th width="50">Job</th>
				<th width="50">Style</th>
				<th width="70">Season</th>
				<th width="70">Booking No</th>
				<th width="50">Booking Date</th>
				<th width="80">Booking Type</th>
				<th width="50">Paymode</th>
				<th width="100">PI</th>
				<th width="100">LC/SC</th>
				<th width="70">Supplier</th>
				<th width="100">Store Name</th>
				<th width="100">Batch No</th>
				<th width="40">Product ID</th>
				<th width="100">Body Part</th>
				<th width="120">F.Construction</th>
				<th width="120">F.Composition</th>
				<th width="40"><p>Fab.Dia</p></th>
				<th width="40">GSM</th>
				<th width="100">F. Color</th>
				<th width="40">UOM</th>
				<th width="60">Opening Stock</th>
				<th width="60">Receive Qty</th>
				<th width="60"><p>Inside Issue Return</p></th>
				<th width="60"><p>Outside Issue Return</p></th>
				<th width="60">Trans In Qty</th>
				<th width="60">Total Rcv</th>
				<th width="40">Rate ($)</th>
				<th width="60">Receive Amount</th>
				<th width="60"><p>Cutting Issue Inside</p></th>
				<th width="60"><p>Cutting Issue Outside</p></th>
				<th width="60">Other Issue Qty</th>
				<th width="60">Receive Rtn. Qnty</th>
				<th width="60">Trans Out Qty</th>
				<th width="60">Total Issue</th>
				<th width="40">Rate ($)</th>
				<th width="60">Issue Amount</th>
				<th width="60">Stock Qty</th>
				<th width="40">Rate ($)</th>
				<th width="80">Stock Amount</th>
				<th width="50">Age (days)</th>
				<th width="50">DOH</th>
			</thead>
		</table>
		<table cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >';
				$i=1;
				foreach ($data_array as $uom => $uom_data)
				{
					$uom_total_booking_qty=$uom_total_opening_qnty=$uom_total_recv_qnty=$uom_total_inside_return=$uom_total_outside_return=$uom_total_trans_in_qty=$uom_total_tot_receive=$uom_total_total_issue=$uom_total_total_issue_amount=$uom_total_stock_qnty=$uom_total_stock_amount=$uom_total_cutting_inside_issue=$uom_total_cutting_outside_issue=$uom_total_other_issue=$uom_total_rcv_return_qnty=$uom_total_trans_out_qnty=0;
					foreach ($uom_data as $booking_no => $book_data)
					{
						foreach ($book_data as $prodStr => $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							$ref_qnty_arr = explode("__", $row);
							$recv_qnty=$trans_out_qty=$trans_in_qty=$opening_recv=$opening_trans=0;
							$recv_amount=$opening_recv_amount=$trans_in_amount=$opening_trans_amount=0;
							$dia_width_types="";$pi_no=""; $lc_sc_no="";
							foreach ($ref_qnty_arr as $ref_qnty_str)
							{
								$ref_qnty = explode("*", $ref_qnty_str);
								if($ref_qnty[6] == 1)
								{
									if($ref_qnty[7]==1){
										$recv_qnty += $ref_qnty[0];
										$recv_amount += $ref_qnty[0]*$ref_qnty[1];
									}else{
										$opening_recv +=$ref_qnty[0];
										$opening_recv_amount +=$ref_qnty[0]*$ref_qnty[1];
									}
								}
								if($ref_qnty[6] == 5)
								{
									if($ref_qnty[7]==1){
										$trans_in_qty += $ref_qnty[0];
										$trans_in_amount += $ref_qnty[0]*$ref_qnty[1];
									}else{
										$opening_trans +=$ref_qnty[0];
										$opening_trans_amount +=$ref_qnty[0]*$ref_qnty[1];
									}
								}
								$dia_width_types .=$ref_qnty[4].",";

								if($ref_qnty[2]==1)
								{
									$pi_no .= $ref_qnty[3].",";
								}

								$lc_sc_no .= $ref_qnty[5].",";
								//echo $recv_qnty."=";
							}

							

							$po_number 	= implode(",",array_unique(explode(",",chop($po_array[$booking_no][$prodStr]["po_no"],","))));
							$pi_no 	= implode(",",array_unique(explode(",",chop($pi_no,","))));
							$lc_sc_no 	= implode(",",array_unique(explode(",",chop($lc_sc_no,","))));
							$prodStr 	= explode("*", $prodStr);

							$company_name 	= $book_po_ref[$booking_no]["company_name"];
							$buyer_name 	= $book_po_ref[$booking_no]["buyer_name"];
							$supplier 		= $book_po_ref[$booking_no]["supplier"];
							$int_ref_arr 		= array_unique(explode(",",chop($book_po_ref[$booking_no]["int_ref"],",")));
							$int_ref = implode(",", $int_ref_arr);
							$job_arr 		= array_unique(explode(",",chop($book_po_ref[$booking_no]["job_no"],",")));
							$job_nos = implode(",", $job_arr);

							$client_arr = array_unique(explode(",",chop($book_po_ref[$booking_no]["client_id"],",")));
							$client_nos="";
							foreach ($client_arr as $client_id)
							{
								$client_nos .= $buyer_arr[$client_id].",";
							}

							$season = array_unique(explode(",",chop($book_po_ref[$booking_no]["season"],",")));
							$season_nos="";
							foreach ($season as $s_id)
							{
								$season_nos .= $season_arr[$s_id].",";
							}

							$style_ref_no = implode(",",array_unique(explode(",",chop($book_po_ref[$booking_no]["style_ref_no"],","))));;
							$pay_mode_nos = implode(",",array_unique(explode(",",chop($book_po_ref[$booking_no]["pay_mode"],","))));

							$booking_date = $book_po_ref[$booking_no]["booking_date"];
							$booking_type = $book_po_ref[$booking_no]["booking_type"];

							
							$dia_width_type_arr = array_unique(explode(",",chop($dia_width_types,",")));

							$dia_width_type="";
							foreach ($dia_width_type_arr as $width_type)
							{
								$dia_width_type .= $fabric_typee[$width_type].",";
							}
							$dia_width_type = chop($dia_width_type,",");

							$booking_qnty 	= $book_po_ref[$booking_no][$prodStr[2]][$prodStr[3]][$prodStr[6]]["qnty"];
							$booking_amount = $book_po_ref[$booking_no][$prodStr[2]][$prodStr[3]][$prodStr[6]]["amount"];
							if($booking_qnty >0){
								$booking_rate 	= $booking_amount/$booking_qnty;
							}else{
								$booking_rate=0;
							}

							$color_type_nos = implode(",",array_unique(explode(",",chop($book_po_ref[$booking_no][$prodStr[2]][$prodStr[3]][$prodStr[6]]["color_type"],","))));
							
							$issRtnRef_str = $prodStr[0]."*".$prodStr[1]."*".$prodStr[2]."*".$prodStr[3]."*".$prodStr[4]."*".$prodStr[5]."*".$prodStr[6]."*".$prodStr[7]."*".$prodStr[8]."*".$prodStr[9];
							
							$inside_return 			= $issue_return_data[$booking_no][$issRtnRef_str]["inside_return"];
							$inside_return_amount 	= $issue_return_data[$booking_no][$issRtnRef_str]["inside_return_amount"];
							$outside_return 		= $issue_return_data[$booking_no][$issRtnRef_str]["outside_return"];
							$outside_return_amount  = $issue_return_data[$booking_no][$issRtnRef_str]["outside_return_amount"];
							$opening_iss_return 	= $issue_return_data[$booking_no][$issRtnRef_str]["opening"];
							$opening_iss_return_amount = $issue_return_data[$booking_no][$issRtnRef_str]["opening_amount"];

							$tot_receive 			= $recv_qnty + $trans_in_qty + $inside_return + $outside_return;
							$tot_receive_amount 	= $recv_amount + $trans_in_amount + $inside_return_amount + $outside_return_amount;

							$tot_receive_rate=0;
							if($tot_receive>0)
							{
								$tot_receive_rate 	= $tot_receive_amount/$tot_receive;
							}
							$booking_balance_qnty 	= $booking_qnty- $tot_receive;
							$booking_balance_amount = $booking_balance_qnty*$booking_rate;

							$cutting_inside 		= $issue_data[$booking_no][$issRtnRef_str]["cutting_inside"];
							$cutting_outside 		= $issue_data[$booking_no][$issRtnRef_str]["cutting_outside"];
							$other_issue 			= $issue_data[$booking_no][$issRtnRef_str]["other_issue"];
							$issue_amount 			= $issue_data[$booking_no][$issRtnRef_str]["issue_amount"];
							$opening_issue 			= $issue_data[$booking_no][$issRtnRef_str]["opening_issue"];
							$opening_issue_amount 	= $issue_data[$booking_no][$issRtnRef_str]["opening_issue_amount"];

							$rcv_return_opening_qnty = $rcv_return_data[$booking_no][$issRtnRef_str]["opening_qnty"];
							$rcv_return_opening_amount = $rcv_return_data[$booking_no][$issRtnRef_str]["opening_amount"];
							$rcv_return_qnty  		= $rcv_return_data[$booking_no][$issRtnRef_str]["qnty"];
							$rcv_return_amount  	= $rcv_return_data[$booking_no][$issRtnRef_str]["amount"];

							$trans_out_amount  		= $trans_out_data[$booking_no][$issRtnRef_str]["amount"];
							$trans_out_qnty  		= $trans_out_data[$booking_no][$issRtnRef_str]["qnty"];
							$trans_out_opening_qnty = $trans_out_data[$booking_no][$issRtnRef_str]["opening_qnty"];
							$trans_out_opening_amount = $trans_out_data[$booking_no][$issRtnRef_str]["opening_amount"];

							$total_issue  			= $cutting_inside + $cutting_outside + $other_issue + $rcv_return_qnty + $trans_out_qnty;

							$opening_title 	= "Receive:".$opening_recv ." + Transfer In:". $opening_trans ." + Issue Return:" . $opening_iss_return . "\n";
							$opening_title 	.= "Issue:".$opening_issue ." + Transfer Out:". $trans_out_opening_qnty ." + Receive Return:" . $rcv_return_opening_qnty;
							$opening_qnty 	= ($opening_recv + $opening_trans + $opening_iss_return) - ($opening_issue + $rcv_return_opening_qnty +$trans_out_opening_qnty);

							$stock_qnty 	= $opening_qnty + ($tot_receive - $total_issue);
							$stock_title 	= "Opening:".$opening_qnty ." + (Receive:". $tot_receive ."- Issue:". $total_issue.")";

							$booking_and_product_wise_quantity = $rate_arr_booking_and_product_wise[$booking_no][$prodStr[0]][$prodStr[1]]["quantity"];
							$booking_and_product_wise_amount = $rate_arr_booking_and_product_wise[$booking_no][$prodStr[0]][$prodStr[1]]["amount"];
							$booking_and_product_wise_rate = $booking_and_product_wise_amount/$booking_and_product_wise_quantity;

							$tot_receive_rate =$booking_and_product_wise_rate;
							// echo $tot_receive_rate.'<br>';

							$opening_amount = ($opening_recv_amount+$opening_trans_amount) -($opening_issue_amount + $rcv_return_opening_amount);

							if($opening_qnty>0)
							{
								//$opening_rate = $opening_amount/$opening_qnty;

								//$opening_rate = ($opening_recv_amount+$opening_trans_amount) / ($opening_recv + $opening_trans + $opening_iss_return);
							}

							if($tot_receive_rate ==0)
							{
								$tot_receive_rate =$opening_rate;
							}

							$tot_issue_rate = $tot_receive_rate;
							$total_issue_amount = $total_issue * $tot_issue_rate;

							if(number_format($stock_qnty,2,".","") == "-0.00")
							{
								$stock_qnty=0;
							}

							$stock_rate = $tot_receive_rate;
							$stock_amount = $stock_qnty * $stock_rate;

							$daysOnHand = datediff("d",change_date_format($transaction_date_array[$booking_no][$prodStr[0]]['max_date'],'','',1),date("Y-m-d"));
							$ageOfDays 	= datediff("d",change_date_format($transaction_date_array[$booking_no][$prodStr[0]]['min_date'],'','',1),date("Y-m-d"));

							//echo $recv_qnty."<br>";
							if(($consump_per_dzn/12) > 0)
							{
								$possible_cut_piece = $stock_qnty/($consump_per_dzn/12);
							}

							if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stock_qnty > $txt_qnty) || ($get_upto_qnty == 2 && $stock_qnty < $txt_qnty) || ($get_upto_qnty == 3 && $stock_qnty >= $txt_qnty) || ($get_upto_qnty == 4 && $stock_qnty <= $txt_qnty) || ($get_upto_qnty == 5 && $stock_qnty == $txt_qnty) || $get_upto_qnty == 0))
							{
								if($stock_qnty!=0 && $cbo_value_with==2)
								{
									$html .='<tr id="tr'. $i.'">
										<td>'. $i .'</td>
										<td>' . $company_arr[$company_name] .'</td>
										<td>'.$buyer_arr[$buyer_name].'</td>
										<td>'. $int_ref .'</td>
										<td>'. $job_nos .'</td>
										<td>'. $style_ref_no.'</td>
										<td>'. chop($season_nos,",") . '</td>
										<td>'. $booking_no .'</td>
										<td>'. $booking_date.'</td>
										<td>'. $booking_type.'</td>
										<td>'. $pay_mode_nos.'</td>
										<td>'. $pi_no.'</td>
										<td>'. $lc_sc_no.'</td> 
										<td>'.$supplier.'</td>
										<td>'. $store_arr[$prodStr[1]] .'</td>
										<td>'. $prodStr[9] .'</td>
										<td>'. $prodStr[0] .'</td>
										<td>'. $body_part[$prodStr[2]].'</td>
										<td>'. $constructionArr[$prodStr[3]] .'</td>
										<td>';

										if(strlen($composition_arr[$prodStr[3]]) > 16)
										{
											$html .= substr($composition_arr[$prodStr[3]],0,16).'...';
										}else{
											$html .= $composition_arr[$prodStr[3]];
										}
										$html .= '</td>
										<td>'. $prodStr[5].'</td>
										<td>'. $prodStr[4] .'</td>
										<td>';
										if(strlen($color_arr[$prodStr[6]]) > 16)
										{
											$html .= substr($color_arr[$prodStr[6]],0,16).'...';
										}else{
											$html .= $color_arr[$prodStr[6]];
										}
										$html .='</td>
										<td>'. $unit_of_measurement[$prodStr[7]] .'</td>
										<td>' . number_format($opening_qnty,2,".","") .'</td>
										<td>'. number_format($recv_qnty,2,".","") .'</td> 
										<td>'. number_format($inside_return,2,".","") .'</td>
										<td>'. number_format($outside_return,2,".","") .'</td>
										<td>'. number_format($trans_in_qty,2,".","") .'</td>
										<td>'. number_format($tot_receive,2,".","") .'</td>
										<td>'. number_format($tot_receive_rate,2,".","") .'</td>
										<td>'. number_format($tot_receive_amount,2,".","") .'</td>
										<td>'. number_format($cutting_inside,2,".","") .'</td>
										<td>' .number_format($cutting_outside,2,".","") .'</td>
										<td>'. number_format($other_issue,2,".","") .'</td>
										<td>'. number_format($rcv_return_qnty,2,".","") .'</td>
										<td>'. number_format($trans_out_qnty,2,".","") .'</td>
										<td>'. number_format($total_issue,2,".","") .'</td>
										<td>'. number_format($tot_issue_rate,2,".","") .'</td>
										<td>'. number_format($total_issue_amount,2,".","") .'</td>
										<td>'. number_format($stock_qnty,2,".","") .'</td>
										<td>'. number_format($stock_rate,2,".","") .'</td>
										<td>'. number_format($stock_amount,2,".","") .'</td>
										<td>'. $ageOfDays.'</td>
										<td>'. $daysOnHand .'</td>
										</tr>';
										
									$i++;
									$uom_total_booking_qty+=$booking_qnty;
									$uom_total_opening_qnty+=$opening_qnty;
									$uom_total_recv_qnty+=$recv_qnty;
									$uom_total_inside_return+=$inside_return;
									$uom_total_outside_return+=$outside_return;
									$uom_total_trans_in_qty+=$trans_in_qty;
									$uom_total_tot_receive+=$tot_receive;
									$uom_total_cutting_inside_issue+=$cutting_inside;
									$uom_total_cutting_outside_issue+=$cutting_outside;
									$uom_total_other_issue+=$other_issue;
									$uom_total_rcv_return_qnty+=$rcv_return_qnty;
									$uom_total_trans_out_qnty+=$trans_out_qnty;
									$uom_total_total_issue+=$total_issue;
									$uom_total_total_issue_amount+=$total_issue_amount;
									$uom_total_stock_qnty+=$stock_qnty;
									$uom_total_stock_amount+=$stock_amount;
								}
								//else if($stock_qnty>=0 && $cbo_value_with==1)
								else if( $cbo_value_with==1)
								{
									$html .='<tr id="tr'. $i.'">
										<td>'. $i .'</td>
										<td>'. $company_arr[$company_name] .'</td>
										<td>';
										$html .=  (strlen($buyer_arr[$buyer_name]) > 10) ? substr($buyer_arr[$buyer_name],0,10).'...' :$buyer_arr[$buyer_name];
										$html .= '</td>
										<td>'. $int_ref .'</td>
										<td>'. $job_nos .'</td>
										<td>'; 
										$html .= (strlen($style_ref_no) > 10) ? substr($style_ref_no,0,10).'...' :$style_ref_no; 
										$html .= '</td>
										<td>'. chop($season_nos,",") .'</td>
										<td>'. $booking_no .'</td>
										<td>'. $booking_date .'</td>
										<td>'. $booking_type .'</td>
										<td>'. $pay_mode_nos .'</td>
										<td>'. $pi_no .'</td>
										<td>';
										$html .= (strlen($lc_sc_no) > 16) ? substr($lc_sc_no,0,16).'...' :$lc_sc_no; 
										$html .= '</td>
										<td>';
										$html .= (strlen($supplier) > 16) ? substr($supplier,0,16).'...' :$supplier; 
										$html .= '</td>
										<td>'. $store_arr[$prodStr[1]] .'</td>
										<td>'. $prodStr[9].'</td>
										<td>'.  $prodStr[0].'</td>
										<td>'.$body_part[$prodStr[2]].'</td>
										<td>'. $constructionArr[$prodStr[3]] .'</td>
										<td>';
										if(strlen($composition_arr[$prodStr[3]]) > 16)
										{
											$html .= substr($composition_arr[$prodStr[3]],0,16).'...';
										}else{
											$html .= $composition_arr[$prodStr[3]];
										}
										$html .= '</td>
										<td>'. $prodStr[5] .'</td>
										<td>'.  $prodStr[4] .'</td>
										<td>';
										if(strlen($color_arr[$prodStr[6]]) > 16)
										{
											$html .= substr($color_arr[$prodStr[6]],0,16).'...';
										}else{
											$html .= $color_arr[$prodStr[6]];
										}
										$html .='</td>
										<td>'. $unit_of_measurement[$prodStr[7]] .'</td>
										<td>'. number_format($opening_qnty,2,".","") .'</td>
										<td>'. number_format($recv_qnty,2,".","").'</td>
										<td>'. number_format($inside_return,2,".","") .'</td>
										<td>'. number_format($outside_return,2,".","") .'</td>
										<td>'. number_format($trans_in_qty,2,".","").'</td>
										<td>'. number_format($tot_receive,2,".","") .'</td>
										<td>'. number_format($tot_receive_rate,2,".","") .'</td>
										<td>'. number_format($tot_receive_amount,2,".","") .'</td>
										<td>'. number_format($cutting_inside,2,".","") .'</td>
										<td>'. number_format($cutting_outside,2,".","") .'</td>
										<td>'. number_format($other_issue,2,".","") .'</td>
										<td>'. number_format($rcv_return_qnty,2,".","") .'</td>
										<td>'. number_format($trans_out_qnty,2,".","") .'</td>
										<td>'. number_format($total_issue,2,".","") .'</td>
										<td>'. number_format($tot_issue_rate,2,".","") .'</td>
										<td>'. number_format($total_issue_amount,2,".","") .'</td>
										<td>'. number_format($stock_qnty,2,".","") .'</td>
										<td>'. number_format($stock_rate,2,".","") .'</td>
										<td>'. number_format($stock_amount,2,".","") .'</td>
										<td>'. $ageOfDays .'</td>
										<td>'. $daysOnHand  .'</td>
									</tr>';
									
									$i++;
									$uom_total_booking_qty+=$booking_qnty;
									$uom_total_opening_qnty+=$opening_qnty;
									$uom_total_recv_qnty+=$recv_qnty;
									$uom_total_inside_return+=$inside_return;
									$uom_total_outside_return+=$outside_return;
									$uom_total_trans_in_qty+=$trans_in_qty;
									$uom_total_tot_receive+=$tot_receive;
									$uom_total_cutting_inside_issue+=$cutting_inside;
									$uom_total_cutting_outside_issue+=$cutting_outside;
									$uom_total_other_issue+=$other_issue;
									$uom_total_rcv_return_qnty+=$rcv_return_qnty;
									$uom_total_trans_out_qnty+=$trans_out_qnty;
									$uom_total_total_issue+=$total_issue;
									$uom_total_total_issue_amount+=$total_issue_amount;
									$uom_total_stock_qnty+=$stock_qnty;
									$uom_total_stock_amount+=$stock_amount;
								}
							}
						}
					}
				}
			
			$html .='</table>
		<table>
			<tfoot>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>Total</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tfoot>
		</table>';
	//echo "Execution Time: " . (microtime(true) - $started) . "S";
	/* foreach (glob("$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	$filename=$user_id."_".$name.".xls";
	echo "$filename####$filename";

	exit(); */



	foreach (glob("batch_store_finish_$user_id*.xlsx") as $filename) {
		@unlink($filename);
	}
	$name=time();
	$filename='batch_store_finish_'.$user_id."_".$name.".xlsx";

	$reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
	$spreadsheet = $reader->loadFromString($html);

	// Save Excel file
	$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
	$writer->save($filename);
	echo "$filename####$filename";  
	exit(); 



}

if($action=="open_po_number")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);

	?>

	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="6">PO Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="100">Job No</th>
						<th width="150">Style Ref no.</th>
						<th width="150">PO Number</th>
					</tr>
				</thead>
				<tbody>
					<?
					$dtlsArray = sql_select("select a.po_number, a.job_no_mst, b.style_ref_no from wo_po_break_down a, wo_po_details_master b where a.job_no_mst = b.job_no and a.id in ($po_id)");
					$i=1;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="100"><p><? echo $row[csf('job_no_mst')]; ?></p></td>
							<td width="150" ><p><? echo $row[csf('style_ref_no')]; ?></p></td>
							<td width="150"><p><? echo $row[csf('po_number')]; ?></p></td>
						</tr>
						<?
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="4" align="right">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}

if($action=="openmypage_receive")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="9">Receive Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="75">Receive Date</th>
						<th width="100">Receive ID</th>
						<th width="100">Batch No</th>
						<th width="100">Ext No</th>
						<th width="80">Booking No</th>
						<th width="80">Batch Date</th>
						<th width="80">Receive Qty.</th>
						<th width="80">Roll Qty.</th>
					</tr>
				</thead>
				<tbody>
					<?
					//$ref_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("fabric_description_id")]."*".$val[csf("gsm")]."*".$val[csf("width")]."*".$val[csf("color_id")]."*".$val[csf("cons_uom")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")];

					$prod_ref = explode("*", $prod_ref);
					$prod_id = $prod_ref[0];
					$store_id = $prod_ref[1];
					$body_part_id = $prod_ref[2];
					$fabric_description_id = $prod_ref[3];
					$gsm = $prod_ref[4];
					$width = $prod_ref[5];
					$color_id = $prod_ref[6];
					$cons_uom = $prod_ref[7];
					$batch_id = $prod_ref[8];
					$batch_no = $prod_ref[9];
					$floor_id = $prod_ref[10];
					$room = $prod_ref[11];
					$rack = $prod_ref[12];
					$self = $prod_ref[13];
					//$from_date

					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$i=1;
					if($body_part_id!='') $body_part_cond=" and b.body_part_id='$body_part_id'"; else $body_part_cond="";
					if($width!='') $width_cond=" and c.width='$width'"; else $width_cond="";
					if($prod_ref[10])
					{
						$room_rack_cond = " and b.floor_id='$floor_id' and b.room='$room' and b.rack='$rack' and b.self = '$self'";
					}

					if($db_type==0) $start_date=change_date_format($from_date,"yyyy-mm-dd","");
					else if($db_type==2) $start_date=change_date_format($from_date,"","",1);

					if($db_type==0) $end_date=change_date_format($to_date,"yyyy-mm-dd","");
					else if($db_type==2) $end_date=change_date_format($to_date,"","",1);

					$date_cond="";
					if($from_date != "" && $to_date !="")
					{
						$date_cond   = " and b.transaction_date between '$start_date' and  '$end_date'";
					}

					$rcv_sql = sql_select("SELECT a.recv_number, e.batch_no,e.batch_date, e.extention_no, e.booking_no,  b.transaction_date as receive_date, b.prod_id, sum(c.no_of_roll) as no_of_roll, sum(b.cons_quantity) as quantity from inv_receive_master a, inv_transaction b, pro_finish_fabric_rcv_dtls c, pro_batch_create_mst e  WHERE a.company_id in ($companyID) and a.id = b.mst_id and b.id = c.trans_id  and b.transaction_type =1 and a.entry_form in (37,7) and a.status_active =1 and b.status_active =1 and c.status_active =1  and b.pi_wo_batch_no = e.id and e.booking_no = '$booking_no' and b.prod_id='$prod_id' and b.store_id= '$store_id' and c.body_part_id= '$body_part_id' and c.gsm = '$gsm' $width_cond and b.cons_uom = '$cons_uom' $room_rack_cond and e.id = $batch_id $date_cond group by a.recv_number, e.batch_no,e.batch_date, e.extention_no, e.booking_no,  b.transaction_date, b.prod_id"); //and c.width='$width'
					//echo $mrr_sql;

					foreach($rcv_sql as $row)
					{
						//$date_frm=date('Y-m-d',strtotime($from_date));
						//$transaction_date=date('Y-m-d',strtotime($row[csf('receive_date')]));
						//if( $from_date == "" || ( $from_date != "" && ($transaction_date >= $date_frm)))
						//{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$tot_reject=$row[csf('returnable_qnty')];
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><p><? echo $i; ?></p></td>
								<td width="75"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
								<td width="100"><p><? echo $row[csf('recv_number')]; ?></p></td>
								<td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
								<td width="50"><p><? echo $row[csf('extention_no')]; ?></p></td>
								<td width="80"><p><? echo $row[csf('booking_no')]; ?></p></td>
								<td width="80"><p><? echo change_date_format($row[csf('batch_date')]); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($row[csf('no_of_roll')],2); ?></p></td>
							</tr>
							<?
							$tot_qty+=$row[csf('quantity')];
							$tot_no_of_roll+=$row[csf('no_of_roll')];
							$i++;
						//}
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="7" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_qty,2); ?>&nbsp;</td>
						<td align="right">&nbsp;<? echo number_format($tot_no_of_roll,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>

		</div>
	</fieldset>
	<?
	exit();
}

if($action=="openmypage_trans_in")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="9">Transfer In Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="80">Transfer Date</th>
						<th width="100">Transfer ID</th>
						<th width="100">Booking No</th>
						<th width="80">Trans out Qty.</th>
						<th width="100">Color</th>
						<th width="100">Batch No</th>
					</tr>
				</thead>
				<tbody>
					<?
					$prod_ref = explode("*", $prod_ref);
					$prod_id = $prod_ref[0];
					$store_id = $prod_ref[1];
					$body_part_id = $prod_ref[2];
					$fabric_description_id = $prod_ref[3];
					$gsm = $prod_ref[4];
					$width = $prod_ref[5];
					$color_id = $prod_ref[6];
					$cons_uom = $prod_ref[7];
					$batch_id = $prod_ref[8];
					$batch_no = $prod_ref[9];
					$floor_id = $prod_ref[10];
					$room = $prod_ref[11];
					$rack = $prod_ref[12];
					$self = $prod_ref[13];

					$color_arr=return_library_array( "select id,color_name from lib_color where id = '$color_id'", "id", "color_name");
					$i=1;
					if($width!="") $width_cond = " and d.dia_width='$width'"; else $width_cond = "";
					if($prod_ref[10])
					{
						$room_rack_cond = " and c.floor_id='$floor_id' and c.room='$room' and c.rack='$rack' and c.self = '$self'";
					}

					if($db_type==0) $start_date=change_date_format($from_date,"yyyy-mm-dd","");
					else if($db_type==2) $start_date=change_date_format($from_date,"","",1);

					if($db_type==0) $end_date=change_date_format($to_date,"yyyy-mm-dd","");
					else if($db_type==2) $end_date=change_date_format($to_date,"","",1);

					$date_cond="";
					if($from_date != "" && $to_date !="")
					{
						$date_cond   = " and c.transaction_date between '$start_date' and  '$end_date'";
					}

					$trans_in_sql = sql_select("select c.transaction_date, a.transfer_system_id, e.batch_no,e.booking_no,  c.body_part_id, c.prod_id, d.color, c.store_id, c.cons_uom, sum(c.cons_quantity) as  quantity from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c,product_details_master d, pro_batch_create_mst e where a.id = b.mst_id and b.to_trans_id = c.id  and c.prod_id = d.id and c.pi_wo_batch_no = e.id and c.company_id in ($companyID) and c.item_category=2  and e.booking_no = '$booking_no' and c.prod_id='$prod_id' and c.store_id= '$store_id' and c.body_part_id = '$body_part_id' and d.gsm='$gsm' $width_cond $room_rack_cond and c.cons_uom = '$cons_uom' and c.transaction_type = 5 and a.status_active =1 and b.status_active =1 and c.status_active =1  and a.entry_form in (14,15,306) and e.id = $batch_id $date_cond group by c.transaction_date, a.transfer_system_id, e.batch_no,e.booking_no, c.body_part_id, c.prod_id, d.color, c.store_id, c.cons_uom"); //and d.dia_width = '$width'

					foreach($trans_in_sql as $row)
					{
						//$date_frm=date('Y-m-d',strtotime($from_date));
						//$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));
						//if( $from_date == "" || ( $from_date != "" && ($transaction_date >= $date_frm)))
						//{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$tot_reject=$row[csf('returnable_qnty')];
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><p><? echo $i; ?></p></td>
								<td width="80"><p><? echo change_date_format($row[csf('transaction_date')]); ?></p></td>
								<td width="100"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
								<td width="100"><p><? echo $row[csf('booking_no')]; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
								<td width="100"><p><? echo $color_arr[$row[csf('color')]]; ?></p></td>
								<td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
							</tr>
							<?
							$tot_qty+=$row[csf('quantity')];
							$tot_no_of_roll+=$row[csf('no_of_roll')];
							$i++;
						//}
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="4" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_qty,2); ?>&nbsp;</td>
						<td align="right" colspan="2">&nbsp;</td>
					</tr>
				</tfoot>
			</table>

		</div>
	</fieldset>
	<?
	exit();
}

if($action=="openmypage_trans_out")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="9">Transfer Out Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="80">Transfer Date</th>
						<th width="100">Transfer ID</th>
						<th width="100">Booking No</th>
						<th width="80">Trans out Qty.</th>
						<th width="100">Color</th>
						<th width="100">Batch No</th>
					</tr>
				</thead>
				<tbody>
					<?
					$prod_ref = explode("*", $prod_ref);
					$prod_id = $prod_ref[0];
					$store_id = $prod_ref[1];
					$body_part_id = $prod_ref[2];
					$fabric_description_id = $prod_ref[3];
					$gsm = $prod_ref[4];
					$width = $prod_ref[5];
					$color_id = $prod_ref[6];
					$cons_uom = $prod_ref[7];
					$batch_id = $prod_ref[8];
					$batch_no = $prod_ref[9];
					$floor_id = $prod_ref[10];
					$room = $prod_ref[11];
					$rack = $prod_ref[12];
					$self = $prod_ref[13];

					$color_arr=return_library_array( "select id,color_name from lib_color where id = '$color_id'", "id", "color_name");
					$i=1;
					if($width!="") $width_cond = " and d.dia_width='$width'"; else $width_cond = "";
					if($prod_ref[10])
					{
						$room_rack_cond = " and c.floor_id='$floor_id' and c.room='$room' and c.rack='$rack' and c.self = '$self'";
					}

					if($db_type==0) $start_date=change_date_format($from_date,"yyyy-mm-dd","");
					else if($db_type==2) $start_date=change_date_format($from_date,"","",1);

					if($db_type==0) $end_date=change_date_format($to_date,"yyyy-mm-dd","");
					else if($db_type==2) $end_date=change_date_format($to_date,"","",1);

					$date_cond="";
					if($from_date != "" && $to_date !="")
					{
						$date_cond   = " and c.transaction_date between '$start_date' and  '$end_date'";
					}

					$trans_out_sql = sql_select("select c.transaction_date, a.transfer_system_id, e.batch_no,e.booking_no,  c.body_part_id, c.prod_id, d.color, c.store_id, c.cons_uom, sum(c.cons_quantity) as  quantity from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c,product_details_master d, pro_batch_create_mst e where a.id = b.mst_id and b.trans_id = c.id  and c.prod_id = d.id and c.pi_wo_batch_no = e.id and c.company_id in ($companyID) and c.item_category=2  and e.booking_no = '$booking_no' and c.prod_id='$prod_id' and c.store_id= '$store_id' and c.body_part_id = '$body_part_id' and d.gsm='$gsm' $width_cond $room_rack_cond and c.cons_uom = '$cons_uom' and c.transaction_type = 6 and a.status_active =1 and b.status_active =1 and c.status_active =1 and a.entry_form in (14,15,306) and b.active_dtls_id_in_transfer=1 and e.id = $batch_id $date_cond group by c.transaction_date, a.transfer_system_id, e.batch_no,e.booking_no, c.body_part_id, c.prod_id, d.color, c.store_id, c.cons_uom"); //and d.dia_width = '$width'

					foreach($trans_out_sql as $row)
					{
						//$date_frm=date('Y-m-d',strtotime($from_date));
						//$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));
						//if( $from_date == "" || ( $from_date != "" && ($transaction_date >= $date_frm)))
						//{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$tot_reject=$row[csf('returnable_qnty')];
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><p><? echo $i; ?></p></td>
								<td width="80"><p><? echo change_date_format($row[csf('transaction_date')]); ?></p></td>
								<td width="100"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
								<td width="100"><p><? echo $row[csf('booking_no')]; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
								<td width="100"><p><? echo $color_arr[$row[csf('color')]]; ?></p></td>
								<td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
							</tr>
							<?
							$tot_qty+=$row[csf('quantity')];
							$tot_no_of_roll+=$row[csf('no_of_roll')];
							$i++;
						//}
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="4" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_qty,2); ?>&nbsp;</td>
						<td align="right" colspan="2">&nbsp;</td>
					</tr>
				</tfoot>
			</table>

		</div>
	</fieldset>
	<?
	exit();
}

if($action=="openmypage_cutting_inside")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="11">Cutting Inside Issue Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="80">Issue Date</th>
						<th width="100">Req No</th>
						<th width="100">Issue Id</th>
						<th width="100">Batch No</th>
						<th width="100">Ext No</th>
						<th width="100">Booking No</th>
						<th width="100">Batch Date</th>
						<th width="100">Issue Purpose</th>
						<th width="80">Issue Qty.</th>
						<th width="100">Remarks</th>

					</tr>
				</thead>
				<tbody>
					<?
					$prod_ref = explode("*", $prod_ref);
					$prod_id = $prod_ref[0];
					$store_id = $prod_ref[1];
					$body_part_id = $prod_ref[2];
					$fabric_description_id = $prod_ref[3];
					$gsm = $prod_ref[4];
					$width = $prod_ref[5];
					$color_id = $prod_ref[6];
					$cons_uom = $prod_ref[7];
					$batch_id = $prod_ref[8];
					$batch_no = $prod_ref[9];
					$floor_id = $prod_ref[10];
					$room = $prod_ref[11];
					$rack = $prod_ref[12];
					$self = $prod_ref[13];

					$color_arr=return_library_array( "select id,color_name from lib_color where id = '$color_id'", "id", "color_name");
					$i=1;
					if($width!='') $width_cond = " and d.dia_width='$width'"; else $width_cond = "";

					if($prod_ref[10])
					{
						$room_rack_cond = " and c.floor_id='$floor_id' and c.room='$room' and c.rack='$rack' and c.self = '$self'";
					}

					if($db_type==0) $start_date=change_date_format($from_date,"yyyy-mm-dd","");
					else if($db_type==2) $start_date=change_date_format($from_date,"","",1);

					if($db_type==0) $end_date=change_date_format($to_date,"yyyy-mm-dd","");
					else if($db_type==2) $end_date=change_date_format($to_date,"","",1);

					$date_cond="";
					if($from_date != "" && $to_date !="")
					{
						$date_cond   = " and c.transaction_date between '$start_date' and  '$end_date'";
					}

					$issue_sql = sql_select("select a.issue_number, a.issue_purpose, c.transaction_date, a.cutt_req_no,  e.booking_no, e.batch_no,e.extention_no, e.batch_date,b.remarks, sum(c.cons_quantity) as quantity from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c , product_details_master d, pro_batch_create_mst e  where a.id = b.mst_id and b.trans_id = c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($companyID) and a.knit_dye_source =1 and c.prod_id= '$prod_id' and c.store_id= $store_id and b.body_part_id =$body_part_id and c.cons_uom = '$cons_uom' and e.booking_no= '$booking_no' and d.gsm='$gsm' $width_cond $room_rack_cond and a.entry_form = 18 and c.status_active =1 and b.status_active=1 and a.status_active =1 and c.item_category=2 and c.transaction_type=2 and e.id=$batch_id $date_cond group by a.issue_number, a.issue_purpose, c.transaction_date, a.cutt_req_no,  e.booking_no, e.batch_no,e.extention_no, e.batch_date,b.remarks"); //and d.dia_width = '$width'

					foreach($issue_sql as $row)
					{
						//$date_frm=date('Y-m-d',strtotime($from_date));
						//$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));
						//if( $from_date == "" || ( $from_date != "" && ($transaction_date >= $date_frm)))
						//{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$tot_reject=$row[csf('returnable_qnty')];
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><p><? echo $i; ?></p></td>
								<td width="80"><p><? echo change_date_format($row[csf('transaction_date')]); ?></p></td>
								<td width="100"><p><? echo $row[csf('cutt_req_no')]; ?></p></td>
								<td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
								<td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
								<td width="50"><p><? echo $row[csf('extention_no')]; ?></p></td>
								<td width="100"><p><? echo $row[csf('booking_no')]; ?></p></td>
								<td width="80"><p><? echo change_date_format($row[csf('batch_date')]); ?></p></td>
								<td width="100"><p><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
								<td width="100"><p><? echo $row[csf('remarks')]; ?></p></td>
							</tr>
							<?
							$tot_qty+=$row[csf('quantity')];
							$i++;
						//}
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_qty,2); ?>&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
				</tfoot>
			</table>

		</div>
	</fieldset>
	<?
	exit();
}

if($action=="openmypage_cutting_outside")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="11">Cutting Outside Issue Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="80">Issue Date</th>
						<th width="100">Req No</th>
						<th width="100">Issue Id</th>
						<th width="100">Batch No</th>
						<th width="100">Ext No</th>
						<th width="100">Booking No</th>
						<th width="100">Batch Date</th>
						<th width="100">Issue Purpose</th>
						<th width="80">Issue Qty.</th>
						<th width="100">Remarks</th>

					</tr>
				</thead>
				<tbody>
					<?
					$prod_ref = explode("*", $prod_ref);
					$prod_id = $prod_ref[0];
					$store_id = $prod_ref[1];
					$body_part_id = $prod_ref[2];
					$fabric_description_id = $prod_ref[3];
					$gsm = $prod_ref[4];
					$width = $prod_ref[5];
					$color_id = $prod_ref[6];
					$cons_uom = $prod_ref[7];
					$batch_id = $prod_ref[8];
					$batch_no = $prod_ref[9];
					$floor_id = $prod_ref[10];
					$room = $prod_ref[11];
					$rack = $prod_ref[12];
					$self = $prod_ref[13];

					$color_arr=return_library_array( "select id,color_name from lib_color where id = '$color_id'", "id", "color_name");
					$i=1;
					if($width!='') $width_cond = " and d.dia_width='$width'"; else $width_cond = "";
					if($prod_ref[10])
					{
						$room_rack_cond = " and c.floor_id='$floor_id' and c.room='$room' and c.rack='$rack' and c.self = '$self'";
					}

					if($db_type==0) $start_date=change_date_format($from_date,"yyyy-mm-dd","");
					else if($db_type==2) $start_date=change_date_format($from_date,"","",1);

					if($db_type==0) $end_date=change_date_format($to_date,"yyyy-mm-dd","");
					else if($db_type==2) $end_date=change_date_format($to_date,"","",1);

					$date_cond="";
					if($from_date != "" && $to_date !="")
					{
						$date_cond   = " and c.transaction_date between '$start_date' and  '$end_date'";
					}

					$issue_sql = sql_select("select a.issue_number, a.issue_purpose, c.transaction_date, a.cutt_req_no,  e.booking_no, e.batch_no,e.extention_no, e.batch_date,b.remarks, sum(c.cons_quantity) as quantity from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e where a.id = b.mst_id and b.trans_id = c.id and c.prod_id = d.id and c.pi_wo_batch_no= e.id and c.company_id in ($companyID) and a.knit_dye_source =3 and c.prod_id = '$prod_id' and c.store_id = $store_id and b.body_part_id =$body_part_id and c.cons_uom = '$cons_uom' and e.booking_no = '$booking_no' and d.gsm='$gsm' $width_cond $room_rack_cond and a.entry_form = 18 and c.status_active =1 and b.status_active=1 and a.status_active =1 and c.item_category =2 and c.transaction_type =2 and e.id=$batch_id $date_cond group by a.issue_number, a.issue_purpose, c.transaction_date, a.cutt_req_no,  e.booking_no, e.batch_no,e.extention_no, e.batch_date,b.remarks"); //and d.dia_width = '$width'

					foreach($issue_sql as $row)
					{
						//$date_frm=date('Y-m-d',strtotime($from_date));
						//$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));
						//if( $from_date == "" || ( $from_date != "" && ($transaction_date >= $date_frm)))
						//{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$tot_reject=$row[csf('returnable_qnty')];
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><p><? echo $i; ?></p></td>
								<td width="80"><p><? echo change_date_format($row[csf('transaction_date')]); ?></p></td>
								<td width="100"><p><? echo $row[csf('cutt_req_no')]; ?></p></td>
								<td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
								<td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
								<td width="50"><p><? echo $row[csf('extention_no')]; ?></p></td>
								<td width="100"><p><? echo $row[csf('booking_no')]; ?></p></td>
								<td width="80"><p><? echo change_date_format($row[csf('batch_date')]); ?></p></td>
								<td width="100"><p><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
								<td width="100"><p><? echo $row[csf('remarks')]; ?></p></td>
							</tr>
							<?
							$tot_qty+=$row[csf('quantity')];
							$i++;
						//}
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_qty,2); ?>&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
				</tfoot>
			</table>

		</div>
	</fieldset>
	<?
	exit();
}


?>