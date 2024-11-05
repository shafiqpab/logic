<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


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
								$search_by_arr=array(1=>"Job No",2=>"Style Ref");
								$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
								echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>', 'create_job_no_search_list_view', 'search_div', 'tna_finish_stock_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_name=$data[1]";
	}

	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field_by="year(insert_date) as year ";
	else if($db_type==2) $year_field_by="to_char(insert_date,'YYYY') as year ";
	if($db_type==0) $month_field_by="and month(insert_date)";
	else if($db_type==2) $month_field_by="and to_char(insert_date,'MM')";
	if($db_type==0) $year_field="and year(insert_date)=$year_id";
	else if($db_type==2) $year_field="and to_char(insert_date,'YYYY')";

	if($db_type==0)
	{
		if($year_id==0)$year_cond=""; else $year_cond="and year(insert_date)='$year_id'";
	}
	else if($db_type==2)
	{
		if($year_id==0)$year_cond=""; else $year_cond="and to_char(insert_date,'YYYY')='$year_id'";
	}
	else $year_cond="";

	$arr=array (0=>$company_arr,1=>$buyer_arr);

	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field_by from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by job_no";

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
									<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $company_id; ?>'+'_'+document.getElementById('txt_booking_no').value,'create_booking_search_list_view', 'search_div', 'tna_finish_stock_report_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
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
	$sql= "select booking_no_prefix_num, booking_no,booking_date,company_id,buyer_id,job_no,po_break_down_id,item_category,fabric_source,supplier_id,is_approved,ready_to_approved from wo_booking_mst  where company_id in ($company) $buyer $booking_date $booking_cond and booking_type=1 and is_short in(1,2) and  status_active=1 and is_deleted=0 order by booking_no";
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
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_search_by').value, 'create_pi_search_list_view', 'search_div', 'tna_finish_stock_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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
	//echo "hi";die;
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

	/*if($txt_pi_no != "")
	{
		$pi_search_sql = sql_select("select a.id, a.pi_number, b.work_order_no, b.booking_without_order from com_pi_master_details a, com_pi_item_details b where a.id = b.pi_id and a.pi_basis_id = 1 and b.item_category_id = 2 and a.importer_id=$cbo_company_id and a.pi_number='$txt_pi_no' and a.status_active=1 and b.status_active=1");
		foreach ($pi_search_sql as $val)
		{
			$search_book_arr[$val[csf("work_order_no")]] = $val[csf("work_order_no")];
		}
	}*/

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
		$serch_ref_sql_1 = "select c.booking_no from wo_booking_dtls c, wo_booking_mst d, wo_po_break_down e, wo_po_details_master f where c.status_active=1 and e.status_active=1 and f.job_no=e.job_no_mst and c.booking_type in (1,4) and c.booking_no=d.booking_no and c.po_break_down_id=e.id and f.company_name in ($cbo_company_id) $buyer_id_cond $job_no_cond $booking_no_cond $year_cond $pay_mode_cond $supplier_cond ";

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

	

	/*$rcv_sql = "SELECT b.id,e.booking_no, e.booking_no_id, e.booking_without_order, a.company_id,a.receive_basis, a.knitting_source,a.knitting_company,a.booking_id as wo_pi_prod_id,a.booking_no as wo_pi_prod_no, b.transaction_date, b.prod_id, b.store_id, $rcv_select c.body_part_id,c.fabric_description_id, c.gsm, c.width, f.color as color_id, b.cons_uom,listagg(c.dia_width_type,',') within group (order by c.dia_width_type) as dia_width_type, listagg(d.po_breakdown_id,',') within group (order by d.po_breakdown_id) as po_breakdown_id, b.cons_quantity as quantity,b.order_rate, b.order_amount, b.pi_wo_batch_no, a.lc_sc_no
	FROM inv_receive_master a, inv_transaction b, pro_finish_fabric_rcv_dtls c left join order_wise_pro_details d on c.trans_id = d.trans_id and d.entry_form=37 and d.po_breakdown_id <>0, pro_batch_create_mst e, product_details_master f
	WHERE a.company_id in ($cbo_company_id) and a.id = b.mst_id and b.id=c.trans_id and b.transaction_type=1 and a.entry_form=37 and a.status_active =1 and b.status_active =1 and c.status_active =1 and e.status_active=1 and b.pi_wo_batch_no=e.id and b.prod_id=f.id $store_cond $date_cond  $all_book_nos_cond $pi_no_cond
	group by b.id,e.booking_no,e.booking_no_id, e.booking_without_order, a.company_id,a.receive_basis, a.knitting_source,a.knitting_company, a.booking_id, a.booking_no, b.transaction_date, b.prod_id, b.store_id, $rcv_group c.body_part_id, c.fabric_description_id, c.gsm, c.width, f.color ,b.cons_uom,c.dia_width_type,b.cons_quantity, b.order_rate, b.order_amount, b.pi_wo_batch_no, a.lc_sc_no order by a.company_id";*/ //and e.booking_no in('UHM-Fb-21-00038','UHM-Fb-21-00032')

	$rcv_sql = "SELECT a.company_id, b.transaction_date, b.prod_id, b.pi_wo_batch_no,e.booking_no, e.booking_no_id, b.cons_uom, b.cons_quantity as quantity, listagg(d.po_breakdown_id,',') within group (order by d.po_breakdown_id) as po_breakdown_id,e.booking_without_order 
	FROM inv_receive_master a, inv_transaction b, pro_finish_fabric_rcv_dtls c left join order_wise_pro_details d on c.trans_id = d.trans_id and d.entry_form=37 and d.po_breakdown_id <>0, pro_batch_create_mst e, product_details_master f
	WHERE a.company_id in ($cbo_company_id) and a.id = b.mst_id and b.id=c.trans_id and b.transaction_type=1 and a.entry_form=37 and a.status_active =1 and b.status_active =1 and c.status_active =1 and e.status_active=1 and b.pi_wo_batch_no=e.id and b.prod_id=f.id $date_cond 
	group by a.company_id, b.transaction_date, b.prod_id, b.pi_wo_batch_no,e.booking_no, e.booking_no_id, b.cons_uom, b.cons_quantity,e.booking_without_order order by a.company_id"; 

	/*$rcv_sql = "SELECT  a.company_id, b.transaction_date, b.cons_uom, b.cons_quantity as quantity
	FROM inv_receive_master a, inv_transaction b, pro_finish_fabric_rcv_dtls c left join order_wise_pro_details d on 
	c.trans_id = d.trans_id and d.entry_form=37 and d.po_breakdown_id <>0
	WHERE a.company_id in ($cbo_company_id) and a.id = b.mst_id and b.id=c.trans_id and b.transaction_type=1 and a.entry_form=37 and a.status_active =1 and b.status_active =1 and c.status_active =1    $date_cond
	group by  a.company_id, b.transaction_date, b.cons_uom, b.cons_quantity
	order by a.company_id";*/


	//echo $rcv_sql;die;
	$rcv_data = sql_select($rcv_sql);
	foreach ($rcv_data as  $val)
	{

		$dataArr[$val[csf('company_id')]][$val[csf('cons_uom')]]['recv_qnty']+=$val[csf('quantity')];

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
		$ref_str="";
		$dia_width_type_ref = implode(",",array_unique(explode(",", $val[csf("dia_width_type")])));

		
		$ref_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("fabric_description_id")]."*".$val[csf("gsm")]."*".$val[csf("width")]."*".$val[csf("color_id")]."*".$val[csf("cons_uom")];
		
		if($transaction_date >= $date_frm)
		{
			$data_array[$val[csf("cons_uom")]][$val[csf("booking_no")]][$ref_str] .= $val[csf("quantity")]."*".$val[csf("order_rate")]."*".$val[csf("receive_basis")]."*".$val[csf("wo_pi_prod_no")]."*".$dia_width_type_ref."*".$val[csf("lc_sc_no")]."*"."1*1__";
		}
		else
		{
			$data_array[$val[csf("cons_uom")]][$val[csf("booking_no")]][$ref_str] .= $val[csf("quantity")]."*".$val[csf("order_rate")]."*".$val[csf("receive_basis")]."*".$val[csf("wo_pi_prod_no")]."*".$dia_width_type_ref."*".$val[csf("lc_sc_no")]."*"."1*2__";
		}
		$all_prod_id[$val[csf("prod_id")]] = $val[csf("prod_id")];

		if($val[csf("booking_without_order")] == 0)
		{
			$all_po_id_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
			$po_array[$val[csf("booking_no")]][$ref_str]["po_no"] .= $val[csf("po_breakdown_id")].",";
		}

		$book_str = explode("-", $val[csf("booking_no")]);

		if($val[csf("booking_without_order")] == 1 || $book_str[1] =="SMN")
		{
			$all_samp_book_arr[$val[csf("booking_no")]] = "'".$val[csf("booking_no")]."'";
		}
		$booking_no_arr[$val[csf("booking_no")]] = "'".$val[csf("booking_no")]."'";
		$batch_id_arr[$val[csf("pi_wo_batch_no")]] = $val[csf("pi_wo_batch_no")];

		$rate_arr_booking_and_product_wise[$val[csf("booking_no")]][$val[csf("prod_id")]][$val[csf("store_id")]]["quantity"] += $val[csf("quantity")];
		$rate_arr_booking_and_product_wise[$val[csf("booking_no")]][$val[csf("prod_id")]][$val[csf("store_id")]]["amount"] += $val[csf("order_amount")];
	}
	/*echo "<pre>";
	print_r($dataArr);die;*/

	

	if ($hdn_pi_id=="")
	{
		/*$trans_in_sql = "SELECT c.transaction_date, c.pi_wo_batch_no, e.batch_no,e.booking_no, e.booking_no_id, e.booking_without_order, c.body_part_id, c.prod_id,c.store_id, $trans_in_select d.detarmination_id, d.gsm, d.dia_width as width, d.color as color_id, c.cons_uom, sum(c.cons_quantity) as quantity,c.order_rate, c.order_amount, listagg(f.po_breakdown_id,',') within group (order by f.po_breakdown_id) as po_breakdown_id
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c left join order_wise_pro_details f on c.id = f.trans_id and f.trans_type = 5 and f.status_active=1 and f.po_breakdown_id<>0, product_details_master d, pro_batch_create_mst e
		where a.id=b.mst_id and b.to_trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($cbo_company_id) and c.item_category=2 and c.transaction_type=5 and a.status_active=1 and b.status_active=1 and c.status_active=1 and e.status_active=1  and a.entry_form in (14,15,306) $store_cond_2 $date_cond_2 $all_book_nos_cond
		group by c.transaction_date, c.pi_wo_batch_no, e.batch_no,e.booking_no, e.booking_no_id, e.booking_without_order, c.company_id, c.body_part_id, c.prod_id,c.store_id, $trans_in_group d.detarmination_id, d.gsm, d.dia_width, d.color, c.cons_uom, c.order_rate, c.order_amount order by c.company_id";*/

		$trans_in_sql = "SELECT c.company_id,c.transaction_date, c.pi_wo_batch_no,e.booking_no, e.booking_no_id, c.prod_id,e.booking_without_order, c.cons_uom, sum(c.cons_quantity) as quantity, listagg(f.po_breakdown_id,',') within group (order by f.po_breakdown_id) as po_breakdown_id 
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c left join order_wise_pro_details f on c.id = f.trans_id and f.trans_type = 5 and f.status_active=1 and f.po_breakdown_id<>0, product_details_master d, pro_batch_create_mst e
		where a.id=b.mst_id and b.to_trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($cbo_company_id) and c.item_category=2 and c.transaction_type=5 and a.status_active=1 and b.status_active=1 and c.status_active=1 and e.status_active=1  and a.entry_form in (14,15,306) $date_cond_2
		group by c.company_id,c.transaction_date, c.pi_wo_batch_no,e.booking_no, e.booking_no_id, c.prod_id,e.booking_without_order, c.cons_uom order by c.company_id"; 

		/*$trans_in_sql = "SELECT c.company_id,c.transaction_date, c.cons_uom, sum(c.cons_quantity) as quantity 
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c left join order_wise_pro_details f on c.id = f.trans_id and f.trans_type = 5 and f.status_active=1 
		and f.po_breakdown_id<>0
		where a.id=b.mst_id and b.to_trans_id=c.id and  c.company_id in ($cbo_company_id) and c.item_category=2 and c.transaction_type=5 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.entry_form in (14,15,306) $date_cond_2 
		group by  c.company_id,c.transaction_date, c.cons_uom
		order by c.company_id";*/
		 //echo $trans_in_sql;die;
		$trans_in_data = sql_select($trans_in_sql);
		foreach ($trans_in_data as  $val)
		{


			$dataArr[$val[csf('company_id')]][$val[csf('cons_uom')]]['trans_in_qnty']+=$val[csf('quantity')];

			$date_frm=date('Y-m-d',strtotime($start_date));
			$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
			$ref_str="";

			
			$ref_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("width")]."*".$val[csf("color_id")]."*".$val[csf("cons_uom")];
			

			if($transaction_date >= $date_frm)
			{
				$data_array[$val[csf("cons_uom")]][$val[csf("booking_no")]][$ref_str] .= $val[csf("quantity")]."*".$val[csf("order_rate")]."*"."*".""."*".""."*"."*5*1__";
			}
			else
			{
				$data_array[$val[csf("cons_uom")]][$val[csf("booking_no")]][$ref_str] .= $val[csf("quantity")]."*".$val[csf("order_rate")]."*"."*".""."*".""."*"."*5*2__";
			}

			$all_prod_id[$val[csf("prod_id")]] = $val[csf("prod_id")];

			if($val[csf("booking_without_order")] == 0)
			{
				$all_po_id_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
				$po_array[$val[csf("booking_no")]][$ref_str]["po_no"] .= $val[csf("po_breakdown_id")].",";
			}

			$book_str = explode("-", $val[csf("booking_no")]);
			if($val[csf("booking_without_order")] == 1 || $book_str[1] == "SMN")
			{
				$all_samp_book_arr[$val[csf("booking_no")]] = "'".$val[csf("booking_no")]."'";
			}
			$booking_no_arr[$val[csf("booking_no")]] = "'".$val[csf("booking_no")]."'";
			$batch_id_arr[$val[csf("pi_wo_batch_no")]] = $val[csf("pi_wo_batch_no")];

			$rate_arr_booking_and_product_wise[$val[csf("booking_no")]][$val[csf("prod_id")]][$val[csf("store_id")]]["quantity"] += $val[csf("quantity")];
			$rate_arr_booking_and_product_wise[$val[csf("booking_no")]][$val[csf("prod_id")]][$val[csf("store_id")]]["amount"] += $val[csf("order_amount")];
		}
	}
	/*echo "<pre>";
	print_r($dataArr);die;*/

	if(!empty($data_array))	
	{
		$con = connect();
		$r_id3=execute_query("delete from tmp_booking_no where userid=$user_id");
		//$r_id4=execute_query("delete from tmp_poid where userid=$user_id");
		//$r_id5=execute_query("delete from tmp_batch_id where userid=$user_id");
		//$r_id6=execute_query("delete from tmp_prod_id where userid=$user_id");
		$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (990,991,992)");
		if($r_id3 && $r_id6)
		{
			oci_commit($con);
		}
	}

	$all_po_id_arr = array_filter($all_po_id_arr);
	$all_po_id_arr = array_unique(explode(",",implode(",", $all_po_id_arr)));
	if(!empty($all_po_id_arr))
	{
		/*$all_po_ids=implode(",",$all_po_id_arr);
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
		}*/
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 990, 1,$all_po_id_arr, $empty_arr);//PO ID

		/* foreach ($all_po_id_arr as  $poval) {
			$rID2=execute_query("insert into tmp_poid (userid, poid) values ($user_id,$poval)");
			
		}
		if($rID2)
		{
		    oci_commit($con);
		} */

		/*$sql_min= "select e.id, MIN(e.pub_shipment_date) pub_shipment_date from  wo_po_break_down e where e.status_active!=0 $all_po_id_cond group by e.id";
		//echo $sql_min;
		$data_array_min=sql_select($sql_min);
		foreach ($data_array_min as $sql_min)
		{
			$min_date_arr[$sql_min[csf("id")]]["min_date"] =change_date_format($sql_min[csf('pub_shipment_date')],'dd-mm-yyyy','-');
		}

		
		$sql_max= "select e.id, MAX(e.pub_shipment_date) pub_shipment_date from  wo_po_break_down e where status_active!=0 $all_po_id_cond  group by e.id";
		$data_array_max=sql_select($sql_max);
		foreach ($data_array_max as $row_max)
		{
			$max_date_arr[$row_max[csf("id")]]["min_date"] =change_date_format($row_max[csf('pub_shipment_date')],'dd-mm-yyyy','-');
		}*/

		$ship_date_array = sql_select("SELECT g.booking_no, MIN(e.pub_shipment_date) min_shipment_date, MAX(e.pub_shipment_date) max_shipment_date from  wo_po_break_down e, wo_booking_dtls g, GBL_TEMP_ENGINE f where e.status_active!=0 and e.id=g.po_break_down_id and g.status_active=1 and g.booking_type in (1,4) and e.id=f.ref_val and f.user_id=$user_id and f.entry_form=990 group by g.booking_no");

		foreach ($ship_date_array as $sql_min) {
			$min_date_arr[$sql_min[csf("booking_no")]]["min_date"]=change_date_format($sql_min[csf('min_shipment_date')],'dd-mm-yyyy','-');
			$max_date_arr[$sql_min[csf("booking_no")]]["min_date"]=change_date_format($sql_min[csf('max_shipment_date')],'dd-mm-yyyy','-');
		}

		$booking_sql = sql_select("SELECT a.body_part_id,c.booking_no,a.lib_yarn_count_deter_id, c.fabric_color_id, c.gmts_color_id, c.color_type, d.booking_date, d.pay_mode, d.booking_type, d.entry_form, d.is_short,f.company_name, f.job_no, f.style_ref_no, f.buyer_name, f.client_id, f.season_buyer_wise, f.total_set_qnty, f.job_quantity, c.fin_fab_qnty, a.uom, c.rate, d.supplier_id, c.po_break_down_id
		from wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls c, wo_booking_mst d, wo_po_break_down e, wo_po_details_master f, GBL_TEMP_ENGINE g
		where a.job_no=c.job_no and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and e.status_active=1 and a.id = c.pre_cost_fabric_cost_dtls_id and e.job_id=f.id and c.booking_type =1 and c.booking_mst_id = d.id and c.po_break_down_id = e.id and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=990
		union all
		select b.body_part_id,c.booking_no,b.lib_yarn_count_deter_id, c.fabric_color_id, c.gmts_color_id,c.color_type, d.booking_date, d.pay_mode, d.booking_type, d.entry_form, d.is_short,f.company_name, f.job_no, f.style_ref_no, f.buyer_name, f.client_id, f.season_buyer_wise,f.total_set_qnty, f.job_quantity, c.fin_fab_qnty, b.uom, c.rate, d.supplier_id,c.po_break_down_id
		from wo_pre_cost_fab_conv_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_booking_dtls c, wo_booking_mst d, wo_po_break_down e, wo_po_details_master f, GBL_TEMP_ENGINE g
		where b.job_no=c.job_no and a.status_active=1 and b.status_active=1 and c.status_active=1 and e.status_active=1 and a.id = c.pre_cost_fabric_cost_dtls_id and e.job_id=f.id and a.fabric_description = b.id and c.booking_type =4 and c.booking_mst_id=d.id  and c.po_break_down_id = e.id and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=990"); // $all_po_id_cond

		foreach ($booking_sql as  $val)
		{
			$book_po_ref[$val[csf("booking_no")]]["company_name"] 	= $val[csf("company_name")];
			$book_po_ref[$val[csf("booking_no")]]["buyer_name"] 	= $val[csf("buyer_name")];
			$book_po_ref[$val[csf("booking_no")]]["job_no"] 		.= $val[csf("job_no")].",";
			$book_po_ref[$val[csf("booking_no")]]["client_id"] 		= $val[csf("client_id")];
			$book_po_ref[$val[csf("booking_no")]]["season"] 		.= $val[csf("season_buyer_wise")].",";
			$book_po_ref[$val[csf("booking_no")]]["style_ref_no"] 	.= $val[csf("style_ref_no")].",";
			$book_po_ref[$val[csf("booking_no")]]["booking_no"] 	= $val[csf("booking_no")];
			$book_po_ref[$val[csf("booking_no")]]["booking_date"] 	= $val[csf("booking_date")];
			$book_po_ref[$val[csf("booking_no")]]["pay_mode"] 		= $pay_mode[$val[csf("pay_mode")]];
			$book_po_ref[$val[csf("booking_no")]]["fs_date"] 		= $min_date_arr[$val[csf("booking_no")]]["min_date"];
			$book_po_ref[$val[csf("booking_no")]]["ls_date"] 		= $max_date_arr[$val[csf("booking_no")]]["min_date"];
			if($val[csf("pay_mode")] == 3 || $val[csf("pay_mode")] == 5)
			{
				$book_po_ref[$val[csf("booking_no")]]["supplier"] = $company_arr[$val[csf("supplier_id")]];
			}else{
				$book_po_ref[$val[csf("booking_no")]]["supplier"] = $supplier_arr[$val[csf("supplier_id")]];
			}

			$job_qnty_arr[$val[csf("job_no")]]["qnty"] = $val[csf("job_quantity")]*$val[csf("total_set_qnty")];
			$book_po_ref[$val[csf("booking_no")]][$val[csf("body_part_id")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("fabric_color_id")]]["qnty"] += $val[csf("fin_fab_qnty")];
			$book_po_ref[$val[csf("booking_no")]][$val[csf("body_part_id")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("fabric_color_id")]]["color_type"] .= $color_type[$val[csf("color_type")]].",";

			$book_po_ref[$val[csf("booking_no")]][$val[csf("body_part_id")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("fabric_color_id")]]["amount"] += $val[csf("fin_fab_qnty")]*$val[csf("rate")];

			$bookingType="";
			if($val[csf('booking_type')] == 4)
			{
				$bookingType = "Sample With Order";
			}
			else
			{
				$bookingType = $booking_type_arr[$val[csf('entry_form')]];
			}
			$book_po_ref[$val[csf("booking_no")]]["booking_type"] = $bookingType;
		}
	}
	//echo "<pre>";
	//print_r($book_po_ref);

	if(!empty($all_samp_book_arr))
	{
		/*$all_samp_book_nos_cond=""; $sampBookCond="";
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
		}
		*/

		foreach ($all_samp_book_arr as $s_book) {
			$rID2=execute_query("insert into tmp_booking_no (userid, booking_no) values ($user_id,".$s_book.")");
		}
		if($rID2)
		{
			oci_commit($con);
		}

		$non_samp_sql = sql_select("select a.booking_date, a.booking_no, a.pay_mode, a.company_id, a.supplier_id, b.lib_yarn_count_deter_id, b.fabric_color, b.uom, b.color_type_id, b.body_part, a.buyer_id, b.style_des, b.finish_fabric, b.rate from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, tmp_booking_no c where a.booking_no=b.booking_no and b.status_active =1 and a.booking_type =4 and a.booking_no=c.booking_no and c.userid=$user_id"); //and a.id in ($all_samp_book_ids)  $all_samp_book_nos_cond

		
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

			$book_po_ref[$val[csf("booking_no")]][$val[csf("body_part")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("fabric_color")]]["qnty"] += $val[csf("finish_fabric")];
			$book_po_ref[$val[csf("booking_no")]][$val[csf("body_part")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("fabric_color")]]["color_type"] .= $color_type[$val[csf("color_type_id")]].",";

			$book_po_ref[$val[csf("booking_no")]][$val[csf("body_part")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("fabric_color")]]["amount"] += $val[csf("finish_fabric")]*$val[csf("rate")];
		}
		unset($non_samp_sql);
	}

	$batch_id_arr = array_filter($batch_id_arr);
	if(!empty($batch_id_arr))
	{
		/*
		$batch_ids= implode(",",$batch_id_arr);
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
		}
		*/

		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 991, 1,$batch_id_arr, $empty_arr);//PO ID

		/* foreach ($batch_id_arr as $batchID) {
			$rID3=execute_query("insert into tmp_batch_id (userid, batch_id) values ($user_id,".$batchID.")");
		}
		if($rID3)
		{
			oci_commit($con);
		} */

	}

	

	//$issRtnSql = "select c.transaction_date, d.knit_dye_source, b.body_part_id, b.prod_id,c.store_id, $issue_return_select b.fabric_description_id, b.gsm, b.width, f.color as color_id,c.cons_uom, c.cons_quantity as quantity, c.order_rate, b.batch_id, e.batch_no, e.booking_no, e.booking_without_order from inv_receive_master a, pro_finish_fabric_rcv_dtls b,inv_transaction c, inv_issue_master d, pro_batch_create_mst e, product_details_master f, GBL_TEMP_ENGINE g where a.id = b.mst_id and b.trans_id=c.id and c.issue_id=d.id and a.entry_form=52 and a.item_category=2 and c.pi_wo_batch_no = e.id and c.prod_id=f.id and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=991 and a.status_active =1 and b.status_active=1 and c.status_active =1 and c.company_id in  ($cbo_company_id) $store_cond_2 $date_cond_2 ";  //$all_batch_ids_cond

	$issRtnSql = "select c.transaction_date,c.cons_uom, c.cons_quantity as quantity,c.company_id, e.booking_no, e.booking_without_order  from inv_receive_master a, pro_finish_fabric_rcv_dtls b,inv_transaction c, inv_issue_master d, pro_batch_create_mst e, product_details_master f, GBL_TEMP_ENGINE g where a.id = b.mst_id and b.trans_id=c.id and c.issue_id=d.id and a.entry_form=52 and a.item_category=2 and c.pi_wo_batch_no = e.id and c.prod_id=f.id and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=991 and a.status_active =1 and b.status_active=1 and c.status_active =1 and c.company_id in  ($cbo_company_id) $date_cond_2";

	
	/*$issRtnSql = "select  c.company_id,c.transaction_date,c.cons_uom,sum(c.cons_quantity) as quantity from inv_receive_master a, pro_finish_fabric_rcv_dtls b,inv_transaction c, inv_issue_master d
	where a.id = b.mst_id and b.trans_id=c.id and c.issue_id=d.id and a.entry_form=52 and a.item_category=2  and a.status_active =1 and b.status_active=1 and c.status_active =1 and c.company_id in ($cbo_company_id)  $date_cond_2 
	group by c.company_id,c.transaction_date,c.cons_uom";*/

	//echo $issRtnSql; die;

	$issRtnData = sql_select($issRtnSql);
	
	foreach ($issRtnData as $val)
	{

		$dataArr[$val[csf('company_id')]][$val[csf('cons_uom')]]['issRtnQnty']+=$val[csf('quantity')];

		if($report_type == 2)
		{
			$issRtnRef_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("fabric_description_id")]."*".$val[csf("gsm")]."*".$val[csf("width")]."*".$val[csf("color_id")]."*".$val[csf("cons_uom")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")];
		}
		else
		{
			$issRtnRef_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("fabric_description_id")]."*".$val[csf("gsm")]."*".$val[csf("width")]."*".$val[csf("color_id")]."*".$val[csf("cons_uom")];
		}

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

	/*echo "<pre>";
	print_r($dataArr);die;*/

	//$issue_sql = sql_select("select a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, c.cons_uom, c.store_id, $issue_select c.cons_quantity, c.id as trans_id,c.transaction_date, d.detarmination_id, d.gsm, d.dia_width, d.color, e.booking_no, e.booking_without_order, round(c.order_rate,2) as order_rate from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE g  where a.id = b.mst_id and b.trans_id = c.id and c.prod_id = d.id and c.pi_wo_batch_no= e.id and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2 and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=991 and a.entry_form=18 and c.status_active=1 and b.status_active=1 and a.status_active=1 and c.item_category =2 and c.transaction_type =2 group by a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, c.cons_uom, c.store_id, $issue_group c.cons_quantity, c.id, c.transaction_date, d.detarmination_id, d.gsm, d.dia_width, d.color, e.booking_no, e.booking_without_order, round(c.order_rate,2)");

	$issue_sql = sql_select("select  c.prod_id, c.cons_uom, c.cons_quantity as quantity,c.company_id,c.transaction_date, e.booking_no, e.booking_without_order,c.cons_quantity from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE g  where a.id = b.mst_id and b.trans_id = c.id and c.prod_id = d.id and c.pi_wo_batch_no= e.id and c.company_id in ($cbo_company_id) $date_cond_2 and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=991 and a.entry_form=18 and c.status_active=1 and b.status_active=1 and a.status_active=1 and c.item_category =2 and c.transaction_type =2 group by c.prod_id, c.cons_uom, c.cons_quantity,c.company_id,c.transaction_date, e.booking_no, e.booking_without_order,c.cons_quantity");

	/*$issue_sql = sql_select("select  c.company_id,c.cons_uom, c.cons_quantity as quantity,c.transaction_date
	from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c
	where a.id = b.mst_id and b.trans_id = c.id and c.company_id in ($cbo_company_id) $store_cond_2  and a.entry_form=18 and c.status_active=1 
	and b.status_active=1 and a.status_active=1 and c.item_category =2 and c.transaction_type =2 
	group by  c.company_id,c.cons_uom, c.cons_quantity,c.transaction_date");*/
	foreach ($issue_sql as $val)
	{

		$dataArr[$val[csf('company_id')]][$val[csf('cons_uom')]]['issue_qnty']+=$val[csf('quantity')];

		$issRef_str="";
		if($report_type == 2)
		{
			$issRef_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")];
		}
		else
		{
			$issRef_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")];
		}

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

	/*echo "<pre>";
	print_r($dataArr);die;*/
	

	//$rcvRtnSql = sql_select("select c.transaction_date, c.company_id, c.prod_id, c.store_id, $rcv_return_select c.cons_quantity, c.cons_uom, d.detarmination_id, d.gsm, d.dia_width, d.color, e.booking_no, e.booking_without_order, b.body_part_id from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE g where a.id = b.mst_id and b.trans_id=c.id and a.entry_form =46 and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2 and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=991 and c.prod_id=d.id and c.pi_wo_batch_no=e.id and a.status_active =1 and b.status_active =1 and c.status_active =1");


	$rcvRtnSql = sql_select("select c.transaction_date, c.company_id, c.cons_quantity as quantity, c.cons_uom, e.booking_no, e.booking_without_order from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE g where a.id = b.mst_id and b.trans_id=c.id and a.entry_form =46 and c.company_id in ($cbo_company_id) $date_cond_2 and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=991 and c.prod_id=d.id and c.pi_wo_batch_no=e.id and a.status_active =1 and b.status_active =1 and c.status_active =1");

	/*$rcvRtnSql = sql_select("select c.transaction_date, c.company_id, sum (c.cons_quantity) as quantity, c.cons_uom 
	from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c
	where a.id = b.mst_id and b.trans_id=c.id and a.entry_form =46 and c.company_id in ($cbo_company_id) $date_cond_2 
	and a.status_active =1 and b.status_active =1 and c.status_active =1 
	group by  c.transaction_date, c.company_id, c.cons_uom ");*/
	foreach ($rcvRtnSql as $val)
	{

		$dataArr[$val[csf('company_id')]][$val[csf('cons_uom')]]['rcvRtnQnty']+=$val[csf('quantity')];

		if($report_type == 2)
		{
			$rcvRtn_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")];
		}
		else
		{
			$rcvRtn_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")];
		}
		

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

	/*echo "<pre>";
	print_r($dataArr);die;*/
	
	//$transOutSql = sql_select("select c.transaction_date,c.pi_wo_batch_no, e.batch_no,e.booking_no, e.booking_no_id, e.booking_without_order, c.body_part_id, c.prod_id, c.store_id, $trans_out_select d.detarmination_id, d.gsm, d.dia_width, d.color, c.cons_uom, c.cons_quantity,c.order_rate from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c,product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE g where a.id=b.mst_id and b.trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2  and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=991 and c.item_category=2 and c.transaction_type=6 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.entry_form in (14,15,306)");

	$transOutSql = sql_select("select c.transaction_date,e.booking_no, e.booking_no_id, e.booking_without_order, c.cons_uom, c.cons_quantity as quantity,c.company_id from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c,product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE g where a.id=b.mst_id and b.trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2  and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=991 and c.item_category=2 and c.transaction_type=6 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.entry_form in (14,15,306)");

	/*$transOutSql = sql_select("select c.company_id,c.transaction_date, c.cons_uom, sum(c.cons_quantity) as quantity
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c
	where a.id=b.mst_id and b.trans_id=c.id and c.company_id in ($cbo_company_id) $date_cond_2 and c.item_category=2 and c.transaction_type=6
	and a.status_active=1 and b.status_active=1 and c.status_active=1 
	and a.entry_form in (14,15,306) 
	group by  c.company_id,c.transaction_date, c.cons_uom");*/

	foreach ($transOutSql as $val)
	{
		$dataArr[$val[csf('company_id')]][$val[csf('cons_uom')]]['transOutQnty']+=$val[csf('quantity')];

		if($report_type == 2)
		{
			$transOut_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")];
		}
		else
		{
			$transOut_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")];
		}

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

	echo "<pre>";
	print_r($dataArr);die;



    /*echo "<pre>";
    print_r($consumption_arr);
    die;*/



    if(!empty($all_prod_id))
    {
    	/*$all_prod_ids=implode(",",$all_prod_id);
    	$all_prod_id_cond=""; $prodCond="";
    	if($db_type==2 && count($all_prod_id)>999)
    	{
    		$all_prod_id_chunk=array_chunk($all_prod_id,999) ;
    		foreach($all_prod_id_chunk as $chunk_arr)
    		{
    			$chunk_arr_value=implode(",",$chunk_arr);
    			$prodCond.="  a.prod_id in($chunk_arr_value) or ";
    		}

    		$all_prod_id_cond.=" and (".chop($prodCond,'or ').")";
    	}
    	else
    	{
    		$all_prod_id_cond=" and a.prod_id in($all_prod_ids)";
    	}
		*/

		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 992, 1,$all_prod_id, $empty_arr);
		/* foreach ($all_prod_id as $prodVal) 
		{
			$rID4=execute_query("insert into tmp_prod_id (userid, prod_id) values ($user_id,$prodVal)");
		}

		if($rID4)
		{
			oci_commit($con);
		} */

    	$transaction_date_array=array();
    	//if($all_prod_id_cond!=""){
		if(!empty($all_prod_id)){
    		$sql_date="SELECT c.booking_no, a.prod_id, min(a.transaction_date) as min_date, max(a.transaction_date) as max_date from inv_transaction a,pro_batch_create_mst c, GBL_TEMP_ENGINE g where a.pi_wo_batch_no=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=2 and a.prod_id=g.ref_val and g.user_id=$user_id and g.entry_form=992 group by c.booking_no,a.prod_id"; //$all_prod_id_cond

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
	//$r_id4=execute_query("delete from tmp_poid where userid=$user_id");
	//$r_id5=execute_query("delete from tmp_batch_id where userid=$user_id");
	//$r_id6=execute_query("delete from tmp_prod_id where userid=$user_id");
	$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (990,991,992)");
	if($r_id3 && $r_id6)
	{
		oci_commit($con);
	}
	

    $floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where status_active =1 and is_deleted=0","floor_room_rack_id","floor_room_rack_name");
	/*echo "<pre>";
	print_r($data_array);
	die;*/
	
	$table_width = "5670";
	$col_span = "29";
	
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



		<?	
		foreach ($dataArr as $companyID => $companyData) 
		{
				
			?>
				<table cellpadding="0" cellspacing="0" width="500" class="rpt_table">
					<caption style="background-color:#f9f9f9;"><? echo $company_arr[$companyID]; ?></caption>
					<thead>
						<th width="30">SL</th>
						<th width="100">Age Days</th>
						<?
						foreach ($companyData as $uomID => $row) 
						{
							?>
							<th width="100"><? echo $unit_of_measurement[$uomID]; ?></th>

							<?
						}
						?>
						
					</thead>
					<tbody>
						<tr>
							<td>1</td>
							<td>100 day</td>
							<?
							foreach ($companyData as $uomID => $row) 
							{
								?>
								<td><? echo $row['recv_qnty']; ?> </td>
								<?
							}
							?>
							
						</tr>
					</tbody>
				</table>
				<br/>
			<?
		}
		?>


	
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



?>