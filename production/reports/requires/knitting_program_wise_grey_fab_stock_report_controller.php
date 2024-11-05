<?

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
	require_once('../../../includes/common.php');

	$_SESSION['page_permission']=$permission;
	if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

	$user_name = $_SESSION['logic_erp']['user_id'];
//--------------------------------------------------------------------------------------------------------------------
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$yarn_count_library=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count"  );
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );
	exit();
}
if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 120, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.company_id='$data' and a.status_active=1 and a.is_deleted=0 and b.category_type in(13) group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "" );
	exit();
}
if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(str)
		{
			var splitData = str.split("_");
	//alert (splitData[1]);
	$("#hide_job_id").val(splitData[0]);
	$("#hide_job_no").val(splitData[1]);
	parent.emailwindow.hide();
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
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'knitting_program_wise_grey_fab_stock_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}
if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];
	//echo $month_id;
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
	if($db_type==0) $year_field="YEAR(insert_date) as year";
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(insert_date)=$year_id"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";
	}
	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by job_no";
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','') ;
	exit();
} // Job Search end
if($action=="order_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array; var selected_name = new Array;
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click');
			}
		}
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str ) {
			if (str!="") str=str.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#hide_order_id').val( id );
			$('#hide_order_no').val( name );
		}
	</script>
</head>
<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:780px;">
				<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Buyer</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Order No</th>
						<th>Shipment Date</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th>
						<input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
						<input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
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
								$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "",$dd,0 );
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
							</td>
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_order_no_search_list_view', 'search_div', 'buyer_order_wise_knitting_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}
if($action=="create_order_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	echo $data[1];
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
	if($search_by==1)
		$search_field="b.po_number";
	else if($search_by==2)
		$search_field="a.style_ref_no";
	else
		$search_field="a.job_no";
	$start_date =trim($data[4]);
	$end_date =trim($data[5]);
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	if($db_type==0) $year_field="YEAR(a.insert_date) as year,";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	$arr=array(0=>$company_arr,1=>$buyer_arr);
	$sql= "select b.id, $year_field a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond order by b.id, b.pub_shipment_date";
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,130,50,60,130,130","760","220",0, $sql , "js_set_value", "id,po_number", "", 1, "company_name,buyer_name,0,0,0,0,0", $arr , "company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date", "",'','0,0,0,0,0,0,3','',1) ;
	exit();
}

//This Action will generate (Roll + Gross) data
if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_name= str_replace("'","",$cbo_company_name);
	$cbo_store_name= str_replace("'","",$cbo_store_name);
	$program_no= str_replace("'","",$txt_program_no);
	$type = str_replace("'","",$cbo_type);
	$cbo_year_selection = str_replace("'","",$cbo_year_selection);
	$cbo_year_selection = substr($cbo_year_selection, -2);
	$cbo_value_with = str_replace("'","",$cbo_value_with);
	$get_upto_qnty = str_replace("'","",$cbo_get_upto_qnty);
	$txt_qnty = str_replace("'","",$txt_qnty);
	$internal_ref = trim(str_replace("'","",$txt_internal_ref));

	if ($cbo_store_name>0) 
	{
		$store_id_a_cond = " and a.store_id = $cbo_store_name";
		$store_name_a_cond = " and a.store_name = $cbo_store_name";
		$store_name_b_cond = " and b.store_name = $cbo_store_name";
		$store_id_c_cond = " and c.store_id = $cbo_store_name";
		$store_id_b_cond = " and b.store_id = $cbo_store_name";
		$from_store_b_cond = " and b.from_store = $cbo_store_name";
		$to_store_b_cond = " and b.to_store = $cbo_store_name";

		$from_n_to_store_b_cond = " and (b.from_store = $cbo_store_name or b.to_store = $cbo_store_name)";
	}

	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond_trans=" and d.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond_trans="";
		}
		else
		{
			$buyer_id_cond="";
			$buyer_id_cond_trans="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name in (".str_replace("'","",$cbo_buyer_name).")";
		$buyer_id_cond_trans=" and d.buyer_name in (".str_replace("'","",$cbo_buyer_name).")";
	}

	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");


		}
		else if($db_type==2)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);


		}
		$date_cond=" and d.program_date between '$start_date' and '$end_date' ";
		$date_cond_roll=" and h.program_date between '$start_date' and '$end_date' ";
	}

	// Special Date Condition Only for Libas

	if($db_type==0)
	{
		$libas_date_cond = " and d.program_date > '2017-Dec-31'";
		$libas_date_cond_2 = " and b.program_date > '2017-Dec-31' ";
	}
	else if($db_type==2)
	{
		$libas_date_cond = " and d.program_date > '31-Dec-2017'";
		$libas_date_cond_2 = " and b.program_date > '31-Dec-2017' ";
	}

	$date_cond .= $libas_date_cond;

	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") {
		$job_no_cond="";
		$tran_job_no_cond="";
		$tran_row_job_cond="";
	}
	else {
		$job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
		$tran_job_no_cond=" and d.job_no_prefix_num in ($job_no) ";
		$tran_row_job_cond=" and b.job_no_prefix_num in ($job_no) ";
	}

	if ($program_no=="") $program_no_cond=""; else $program_no_cond=" and d.id in ($program_no) ";
	if ($program_no=="") $trans_program_no_cond=""; else $trans_program_no_cond=" and b.to_program in ($program_no) ";
	if ($program_no=="") $proram_no_cond_roll=""; else $proram_no_cond_roll=" and h.id in ($program_no) ";
	if ($internal_ref=="")
	{
		$internal_ref_cond="";
		$internal_ref_cond_2="";
	}
	else
	{
		$internal_ref_cond=" and b.grouping like '%".$internal_ref."%'";
		$internal_ref_cond_2=" and e.grouping like '%".$internal_ref."%'";
	}

	$booking_no=str_replace("'","",$txt_booking_no);
	if($booking_no == "") {
		$booking_no_cond ="";
		$booking_no_cond_program="";
		$booking_no_cond_transRow="";
	}
	else {
		$booking_no_cond=" and e.booking_no like '%".trim($booking_no) ."' and e.booking_no like '%-$cbo_year_selection-%'";
		$booking_no_cond_program=" and a.booking_no like '%".trim($booking_no) ."' and a.booking_no like '%-$cbo_year_selection-%'";
		$booking_no_cond_transRow=" and c.booking_no like '%".trim($booking_no) ."' and c.booking_no like '%-$cbo_year_selection-%'";
	}
	if ($program_no=="") $program_cond_trans=""; else $program_cond_trans=" and b.to_program in ($program_no) ";
	$all_prog_for_issue=array();

	if(str_replace("'","",trim($txt_order_no))=="")
	{
		$po_cond="";
		$trans_po_cond="";
	}
	else
	{
		if(str_replace("'","",$hide_order_id)!="")
		{
			$po_id=str_replace("'","",$hide_order_id);
			$po_cond="and b.id in(".$po_id.")";
			$po_cond2="and e.id in(".$po_id.")";
			$trans_po_cond=" and a.to_order_id in(".$po_id.")";
			$trans_po_cond_roll=" and b.to_order_id in(".$po_id.")";
		}
		else
		{
			$po_number="%".trim(str_replace("'","",$txt_order_no))."%";
			$po_cond="and b.po_number like '$po_number'";
			$po_cond2="and e.po_number like '$po_number'";
			$trans_po_cond=" and e.po_number like '$po_number'";
			$trans_po_cond_roll=" and e.po_number like '$po_number'";
		}
	}

	$con = connect();
	$r_id2=execute_query("delete from tmp_barcode_no where userid=$user_name and entry_form=126");
	$r_id=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_name and entry_form in (126)");
	oci_commit($con);

	$sql="SELECT a.job_no_prefix_num, d.id as prog_no, d.program_date, a.job_no, a.company_name, a.buyer_name, b.id as po_id,b.grouping, d.machine_dia,d.fabric_dia, b.po_number, c.gsm_weight, sum(c.program_qnty) as program_qnty, d.stitch_length, d.color_id, e.booking_no, e.fabric_desc,d.knitting_source, d.batch_no, a.client_id
	from ppl_planning_entry_plan_dtls c, ppl_planning_info_entry_dtls d, ppl_planning_info_entry_mst e,wo_po_details_master a, wo_po_break_down b, lib_buyer f
	where a.id=b.job_id and e.id=d.mst_id and c.dtls_id=d.id and c.po_id=b.id and a.company_name=$company_name $buyer_id_cond $po_cond $job_no_cond $program_no_cond $date_cond $booking_no_cond $internal_ref_cond and c.is_sales !=1 and d.is_sales !=1  and a.buyer_name= f.id and f.status_active=1 and f.is_deleted=0 
	group by d.id, b.id, b.grouping, d.machine_dia, d.fabric_dia, d.program_date, e.booking_no, e.fabric_desc, d.color_id, c.gsm_weight, d.stitch_length, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, b.po_number, a.insert_date,d.knitting_source, d.batch_no, a.client_id
	order by a.buyer_name, a.job_no, b.id";//a.job_no=b.job_no_mst
	//echo $sql."<br><br>";//die;

	$nameArray=sql_select( $sql );
	foreach ($nameArray as $val)
	{
		if($program_arr[$val[csf("prog_no")]]=="")
		{
			$program_arr[$val[csf("prog_no")]]=$val[csf("prog_no")];
			$ProgNO = $val[csf("prog_no")];
			//$rID1=execute_query("insert into tmp_prog_no (userid, prog_no) values ($user_name,$ProgNO)");
		}

		$trans_po_arr[$val[csf("po_id")]] = $val[csf("po_id")];
		//$program_arr[$val[csf("prog_no")]] = $val[csf("prog_no")];
		$all_prog_for_issue[$val[csf("prog_no")]] = $val[csf("prog_no")];
		$for_issue_po_arr[$val[csf('po_id')]] = $val[csf('po_id')];
	}

	$program_arr = array_filter($program_arr);

	if(count($program_arr)>0)
	{
		$all_prog_nos = implode(",", $program_arr);
		$progCond = ""; $prog_cond_for_rcv = "";
		$progCondRoll = ""; $prog_cond_for_rcv_roll = "";
		if($db_type==2 && count($program_arr)>999)
		{
			$program_arr_chunk=array_chunk($program_arr,999) ;
			foreach($program_arr_chunk as $chunk_arr)
			{
				$progCond.=" a.booking_id in(".implode(",",$chunk_arr).") or ";
				$progCondRoll.=" a.booking_no in('".implode("','",$chunk_arr)."') or ";
			}

			$prog_cond_for_rcv.=" and (".chop($progCond,'or ').")";
			$prog_cond_for_rcv_roll.=" and (".chop($progCondRoll,'or ').")";

		}
		else
		{
			$prog_cond_for_rcv=" and a.booking_id in($all_prog_nos)";
			$prog_cond_for_rcv_roll=" and a.booking_no in('".implode("','", $program_arr)."')";
		}

		fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 126, 1,$program_arr, $empty_arr); // program temp entry

		$knitting_recv_qnty_array=array(); $prod_id_arr=array();

		$prod_sql="SELECT a.id, b.id as bid, c.id as cid, d.id as did, a.booking_id,c.po_breakdown_id as po_id, c.quantity as knitting_qnty, d.qnty, b.trans_id as trans_id, a.receive_date as receive_date, d.barcode_no, a.store_id 
		from  GBL_TEMP_ENGINE e, inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c left join pro_roll_details d on c.po_breakdown_id =d.po_breakdown_id and c.dtls_id=d.dtls_id and d.entry_form=2 and d.status_active=1 
		where e.ref_val=a.booking_id and e.ref_from=1 and e.user_id=$user_name and e.entry_form=126 and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.id=b.mst_id and c.entry_form=2 and b.id=c.dtls_id and  b.status_active=1 and b.is_deleted=0  
		group by a.id, b.id, c.id, d.id, a.booking_id,c.po_breakdown_id, c.quantity, d.qnty, b.trans_id, a.receive_date, d.barcode_no, a.store_id";

		//echo $prod_sql;die; //$prog_cond_for_rcv
		$sql_prod=sql_select($prod_sql); //echo $prod_sql;die;
		foreach($sql_prod as $row)
		{
			if($row[csf('trans_id')]>0)
			{
				if(($cbo_store_name > 0 && $cbo_store_name==$row[csf('store_id')])  ||  $cbo_store_name==0)
				{
					if($row[csf('did')] == "") // 
					{
						if($check_did[$row[csf('cid')]]=="") //check proposi does not repeat
						{
							$check_did[$row[csf('cid')]]=$row[csf('cid')];
							$knitting_recv_qnty_array[$row[csf('booking_id')]][$row[csf('po_id')]]+=$row[csf('knitting_qnty')];

							$knitting_recv_date_array[$row[csf('booking_id')]][$row[csf('po_id')]]=$row[csf('receive_date')];
							if($knitting_recv_date_array[$row[csf('booking_id')]][$row[csf('po_id')]] > $row[csf("receive_date")])
							{
								$minimum_date = $row[csf("receive_date")];
							}else{
								$minimum_date = $knitting_recv_date_array[$row[csf('booking_id')]][$row[csf('po_id')]];
							}

							$knitting_recv_date_array[$row[csf('booking_id')]][$row[csf('po_id')]] = $minimum_date;
						}
					}
					else // roll data
					{
						//echo "string";
						if($check_did[$row[csf('barcode_no')]]=="") //check roll does not repeat
						{
							$check_did[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
							$knitting_recv_qnty_array[$row[csf('booking_id')]][$row[csf('po_id')]]+=$row[csf('qnty')];

							$knitting_recv_date_array[$row[csf('booking_id')]][$row[csf('po_id')]]=$row[csf('receive_date')];
							if($knitting_recv_date_array[$row[csf('booking_id')]][$row[csf('po_id')]] > $row[csf("receive_date")])
							{
								$minimum_date = $row[csf("receive_date")];
							}else{
								$minimum_date = $knitting_recv_date_array[$row[csf('booking_id')]][$row[csf('po_id')]];
							}

							$knitting_recv_date_array[$row[csf('booking_id')]][$row[csf('po_id')]] = $minimum_date;
						}
					}
				}
			}
			else
			{
				$prod_id_arr[$row[csf('booking_id')]].=$row[csf('id')].",";
				$program_ref_arr[$row[csf('id')]] = $row[csf('booking_id')];
				$production_id_arr[$row[csf('id')]] = $row[csf('id')];
			}
			$production_barcode_no_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
			$barcode_no_ref[$row[csf('barcode_no')]]["book"] 	=   $row[csf('booking_id')];//program_no
			//transfer purpose barcode

			$together_production_transfer_barcode[$row[csf('barcode_no')]]=$row[csf('barcode_no')];

		}
		/*echo "<pre>";
		print_r($knitting_recv_qnty_array);die;*/

		if(empty($production_barcode_no_arr) ) // if search by order,job   301220
		{
			$production_barcode_cond_for_rcv="";
		}
		else
		{
			$production_barcode_no_arr = array_filter($production_barcode_no_arr);
			if(count($production_barcode_no_arr) > 0)
			{
				//echo count($production_barcode_no_arr);die;
				$all_production_barcode_no_arr = implode(",", $production_barcode_no_arr);
				$productionBarcode = ""; $production_barcode_cond_for_rcv = "";
				if($db_type==2 && count($production_barcode_no_arr)>999)
				{
					$all_production_barcode_no_arr_chunk=array_chunk($production_barcode_no_arr,999) ;
					foreach($all_production_barcode_no_arr_chunk as $chunk_arr)
					{
						$productionBarcode.=" barcode_no in(".implode(",",$chunk_arr).") or ";
					}

					$production_barcode_cond_for_rcv.=" and (".chop($productionBarcode,'or ').")";
				}
				else
				{
					$production_barcode_cond_for_rcv=" and barcode_no in($all_production_barcode_no_arr)";
				}
			}
		}
	//print_r($production_id_arr);die;
		$production_id_arr = array_filter($production_id_arr);
		if(count($production_id_arr) > 0)
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 126, 2,$production_id_arr, $empty_arr); // production Id temp entry

			$recv_array=array();

			$sql_recv=sql_select("select a.booking_id,c.po_breakdown_id as po_id,sum(c.quantity) as knitting_qnty, min(receive_date) as receive_date from GBL_TEMP_ENGINE d, inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c where d.ref_val=a.booking_id and d.user_id=$user_name and d.entry_form=126 and d.ref_from=2 and a.id=b.mst_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=22 and c.entry_form=22 and a.receive_basis=9 and b.status_active=1 and b.is_deleted=0 $store_id_a_cond group by a.booking_id,c.po_breakdown_id");

			$minimum_date = "";
			foreach($sql_recv as $row)
			{
				$recv_array[$row[csf('booking_id')]][$row[csf('po_id')]]=$row[csf('knitting_qnty')];
				$knitting_recv_qnty_array[$program_ref_arr[$row[csf('booking_id')]]][$row[csf('po_id')]]+=$row[csf('knitting_qnty')];
				$knitting_recv_date_array[$program_ref_arr[$row[csf('booking_id')]]][$row[csf('po_id')]] =$row[csf('receive_date')];
				if($knitting_recv_date_array[$program_ref_arr[$row[csf('booking_id')]]][$row[csf('po_id')]] > $row[csf("receive_date")])
				{
					$minimum_date = $row[csf("receive_date")];
				}else{
					$minimum_date = $knitting_recv_date_array[$program_ref_arr[$row[csf('booking_id')]]][$row[csf('po_id')]];
				}

				$knitting_recv_date_array[$program_ref_arr[$row[csf('booking_id')]]][$row[csf('po_id')]] = $minimum_date;

				$for_issue_po_arr[$row[csf('po_id')]] = $row[csf('po_id')];

			}
		}

		$delivery_qty_res = sql_select("select sum(qnty) as qnty,a.booking_no,po_breakdown_id, min(receive_date) as receive_date from pro_roll_details a, inv_receive_master b, GBL_TEMP_ENGINE c  where a.mst_id = b.id and b.entry_form = 58 and a.entry_form in (58) and a.status_active=1 and a.is_deleted=0 and a.booking_no=cast(c.ref_val as varchar(4000)) and c.user_id=$user_name and c.ref_from=1 and c.entry_form=126 store_id_b_cond group by a.booking_no,po_breakdown_id"); //$prog_cond_for_rcv_roll

		$minimum_date = "";
		foreach ($delivery_qty_res as  $val)
		{
			$knitting_recv_qnty_array[$val[csf("booking_no")]][$val[csf("po_breakdown_id")]] += $val[csf("qnty")];
			$knitting_recv_date_array[$val[csf("booking_no")]][$val[csf("po_breakdown_id")]] =$val[csf("receive_date")];

			if($knitting_recv_date_array[$val[csf("booking_no")]][$val[csf("po_breakdown_id")]] > $val[csf("receive_date")])
			{
				$minimum_date = $val[csf("receive_date")];
			}else{
				$minimum_date = $knitting_recv_date_array[$val[csf("booking_no")]][$val[csf("po_breakdown_id")]];
			}

			$knitting_recv_date_array[$val[csf("booking_no")]][$val[csf("po_breakdown_id")]] = $minimum_date;

			$for_issue_po_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
		}
	}


	$booking_arr=array();
	$sql_book=sql_select("select  a.booking_no,sum(a.grey_fab_qnty) as grey_fab_qnty from wo_booking_dtls a,wo_booking_mst b where a.booking_no=b.booking_no and b.booking_type=1 and  a.status_active=1 and a.is_deleted=0 $booking_no_cond_program group by a.booking_no");
	foreach($sql_book as $row_b)
	{

		$booking_arr[$row_b[csf('booking_no')]]['grey_fab_qnty']=$row_b[csf('grey_fab_qnty')];
	}

	if($db_type==0){
		$from_order_idCond="group_concat( a.from_order_id) as from_order_id";		
		$from_order_idCond="group_concat( b.from_order_id) as from_order_id";		
	}
	else
	{
		$from_order_idCond="listagg(cast( a.from_order_id as varchar2(4000)), ',') within group (order by a.id) as from_order_id";
		$from_order_idCond="listagg(cast( b.from_order_id as varchar2(4000)), ',') within group (order by a.id) as from_order_id";
	}

	$trans_sql="SELECT b.to_program, $from_order_idCond, min(a.transfer_date) as transfer_date,
	sum(case when c.trans_type in(5) then c.quantity else 0 end) as item_transfer_in,a.to_order_id , e.po_number, d.job_no_prefix_num, d.buyer_name, e.grouping, d.client_id
	from inv_item_transfer_dtls b,inv_item_transfer_mst a, order_wise_pro_details c, wo_po_details_master d, wo_po_break_down e, ppl_planning_info_entry_dtls h, lib_buyer f
	where a.id=b.mst_id and c.dtls_id=b.id and d.id = e.job_id and a.to_order_id = e.id and b.to_program=h.id and c.trans_type in(5) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=13 and c.entry_form in (13) and a.entry_form in (13) and b.from_program>0 and b.to_program>0 and c.is_sales <> 1 and a.transfer_criteria in (1,2,4) and d.company_name=$company_name $trns_to_po_cond $tran_job_no_cond $trans_po_cond $program_cond_trans $buyer_id_cond_trans $internal_ref_cond_2 $date_cond_roll and d.buyer_name=f.id and f.status_active=1 and f.is_deleted=0 $to_store_b_cond
	group by b.to_program,a.to_order_id, e.po_number, d.job_no_prefix_num, d.buyer_name, e.grouping, d.client_id";
	//and d.job_no = e.job_no_mst

	//echo $trans_sql;	
	//die; 

	$data_trans= sql_select($trans_sql); //$transfer_all_po_cond
	$trns_row_data=array();$trns_row_data_from=array();
	foreach($data_trans as $row_b)
	{
		//$trns_row_data[$row_b[csf('to_program')]][$row_b[csf('to_order_id')]."!!!!".$row_b[csf('from_order_id')].'!!!!'.$row_b[csf('job_no_prefix_num')].'!!!!'.$row_b[csf('po_number')].'!!!!'.$row_b[csf('buyer_name')].'!!!!'.$row_b[csf('grouping')].'!!!!'.$row_b[csf('transfer_date')]]['trans_qty_in'] += $row_b[csf('item_transfer_in')];

		$trns_row_data[$row_b[csf('to_program')]][$row_b[csf('to_order_id')]."!!!!"."from_order".'!!!!'.$row_b[csf('job_no_prefix_num')].'!!!!'.$row_b[csf('po_number')].'!!!!'.$row_b[csf('buyer_name')].'!!!!'.$row_b[csf('grouping')]]['trans_qty_in'] += $row_b[csf('item_transfer_in')];
		
		$trns_row_data[$row_b[csf('to_program')]][$row_b[csf('to_order_id')]."!!!!"."from_order".'!!!!'.$row_b[csf('job_no_prefix_num')].'!!!!'.$row_b[csf('po_number')].'!!!!'.$row_b[csf('buyer_name')].'!!!!'.$row_b[csf('grouping')]]['transfer_date'] = $row_b[csf('transfer_date')];

		$roll_no=0;
		$from_order_id_exp=explode(",", $row_b[csf('from_order_id')]);
		foreach ($from_order_id_exp as $rows_from_orderID) 
		{
			$roll_data=explode(",",$yarn_lot_arr[$rows_from_orderID]['roll']);
			foreach($roll_data as $val)
			{
				$val=explode("**",$val);
				if(!in_array($val[0],$roll_arr))
				{
					$roll_no+=$val[1];
					$roll_arr[]=$val[0];
				}
			}
		}

		$trns_row_data[$row_b[csf('to_program')]][$row_b[csf('to_order_id')]."!!!!"."from_order".'!!!!'.$row_b[csf('job_no_prefix_num')].'!!!!'.$row_b[csf('po_number')].'!!!!'.$row_b[csf('buyer_name')].'!!!!'.$row_b[csf('grouping')]]['count'] += $roll_no;

		$trns_row_prog_ref_data[$row_b[csf('to_program')]] = $row_b[csf('to_program')];
		//added in 03-01-2020
		$all_prog_for_issue[$row_b[csf('to_program')]] = $row_b[csf('to_program')];
		$for_issue_po_arr[$row_b[csf('to_order_id')]] = $row_b[csf('to_order_id')];
		$to_order_client_arr[$row_b[csf('to_order_id')]] = $row_b[csf('client_id')];

	}
	unset($data_trans);
		
	$roll_trans_in_sql="SELECT b.from_order_id, min(a.transfer_date) as transfer_date, sum(f.qnty) as item_transfer_in, b.to_order_id, e.po_number, d.job_no_prefix_num, d.buyer_name, e.grouping , h.id as program_no, d.client_id from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c , wo_po_details_master d,wo_po_break_down e, pro_roll_details f, pro_roll_details g, ppl_planning_info_entry_dtls h, lib_buyer i
	where a.entry_form in (82,83) 
	and a.id=b.mst_id and c.dtls_id=b.id and c.po_breakdown_id=e.id
	and d.id= e.job_id
	and a.id=f.mst_id and b.id=f.dtls_id and c.trans_type in(5) 
	and g.entry_form=2 and f.barcode_no = g.barcode_no and g.receive_basis=2 and cast(g.booking_no as varchar2(30)) = cast(h.id as varchar2(30)) and d.company_name=$company_name
	$date_cond_roll $proram_no_cond_roll $tran_job_no_cond $trans_po_cond_roll $buyer_id_cond_trans $internal_ref_cond_2
	and b.to_order_id = e.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	and c.status_active=1 and c.is_deleted=0 and a.item_category=13 and c.entry_form in (82,83)  and c.is_sales <> 1 and d.buyer_name= i.id and i.status_active=1 and i.is_deleted=0 $to_store_b_cond 
	group by b.to_program, b.from_order_id, b.to_order_id, e.po_number, d.job_no_prefix_num, d.buyer_name, e.grouping, h.id, d.client_id";

	//from_order_id
	//$transfer_all_po_cond2   $production_barcode_cond_for_rcv
	//echo $roll_trans_in_sql;die;
	
	$roll_data_trans= sql_select($roll_trans_in_sql);
	foreach ($roll_data_trans as $key => $row) 
	{
		$in_barcode_no_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
		$together_production_transfer_barcode[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	}

	//$trns_row_data=array();
	foreach($roll_data_trans as $row)
	{
		$trns_row_data[$row[csf('program_no')]][$row[csf('to_order_id')].'!!!!'.'from_order'.'!!!!'.$row[csf('job_no_prefix_num')].'!!!!'.$row[csf('po_number')].'!!!!'.$row[csf('buyer_name')].'!!!!'.$row[csf('grouping')]]['trans_qty_in']+=$row[csf('item_transfer_in')];
		$trns_row_data[$row[csf('program_no')]][$row[csf('to_order_id')].'!!!!'.'from_order'.'!!!!'.$row[csf('job_no_prefix_num')].'!!!!'.$row[csf('po_number')].'!!!!'.$row[csf('buyer_name')].'!!!!'.$row[csf('grouping')]]['count']++;

		$trns_row_data[$row[csf('program_no')]][$row[csf('to_order_id')].'!!!!'.'from_order'.'!!!!'.$row[csf('job_no_prefix_num')].'!!!!'.$row[csf('po_number')].'!!!!'.$row[csf('buyer_name')].'!!!!'.$row[csf('grouping')]]['transfer_date']=$row[csf('transfer_date')];
		

		$trns_row_prog_ref_data[$row[csf('program_no')]] = $row[csf('program_no')];

		//added in 03-01-2020
		$all_prog_for_issue[$row[csf('program_no')]] = $row[csf('program_no')];

		$for_issue_po_arr[$row[csf('to_order_id')]] = $row[csf('to_order_id')];
		$to_order_client_arr[$row[csf('to_order_id')]] = $row[csf('client_id')];

	}
	unset($roll_data_trans);


	/*echo "<pre>";
	print_r($trns_row_data);
	die;*/

	$for_issue_po_arr=array_filter($for_issue_po_arr);
	if(!empty($for_issue_po_arr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 126, 3,$for_issue_po_arr, $empty_arr); // PO Id temp entry

		$roll_issue="SELECT b.remarks, d.qnty knitting_issue_qnty, d.po_breakdown_id, b.id, d.barcode_no, g.booking_no as program_no from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details c, GBL_TEMP_ENGINE e, pro_roll_details d left join pro_roll_details g on  d.barcode_no = g.barcode_no and g.entry_form=2 and g.receive_basis=2 where a.id=b.mst_id and b.id = c.dtls_id and a.id=d.mst_id and b.id=d.dtls_id and c.trans_type = 2 and a.item_category=13 and a.entry_form in (61) and c.entry_form in (61) and d.entry_form in (61) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active = 1 and c.is_deleted = 0 and e.ref_val=c.po_breakdown_id and e.user_id= $user_name and e.entry_form=126 and e.ref_from=3 $store_name_b_cond";  
		// $for_issue_po_id_cond

		$sql_roll_data=sql_select($roll_issue);
		$knit_issue_arr = array();
		foreach($sql_roll_data as $row)
		{
			if($row[csf('program_no')] !="")
			{
				$knit_issue_arr[$row[csf('program_no')]][$row[csf('po_breakdown_id')]]['qnty'] +=$row[csf('knitting_issue_qnty')];
				$knit_issue_arr[$row[csf('program_no')]]['knit_id']=$row[csf('knit_id')];
				$knit_issue_arr[$row[csf('program_no')]][$row[csf('po_breakdown_id')]]['remarks']=$row[csf('remarks')];
			}
			else
			{
				$issue_barcode_arr[$row[csf('barcode_no')]] =$row[csf('barcode_no')];
			}
		}
		unset($sql_roll_data);


		if($issue_barcode_arr)
		{
			//Splited barcode no 
			foreach($issue_barcode_arr as $barCode)
			{
				$rID3=execute_query("insert into tmp_barcode_no (userid, barcode_no, entry_form) values ($user_name,$barCode, 126)");
			}

			if($rID3)
			{
				oci_commit($con);
			}

			$split_chk_sql = sql_select("select b.barcode_no, c.qnty, d.booking_no as program_no, c.po_breakdown_id from pro_roll_split b, pro_roll_details c, pro_roll_details d, tmp_barcode_no e where b.entry_form = 75 and b.split_from_id=c.roll_split_from and b.barcode_no=d.barcode_no and d.entry_form=2 and d.receive_basis=2 and b.status_active = 1 and c.status_active = 1 and e.barcode_no=c.barcode_no and e.userid= $user_name and e.entry_form=126");  //$barcode_cond

			if(!empty($split_chk_sql))
			{
				foreach ($split_chk_sql as $val)
				{
					$knit_issue_arr[$val[csf('program_no')]][$val[csf('po_breakdown_id')]]['qnty'] +=$val[csf('qnty')];
				}
			}
		}

		$sql_data=sql_select("SELECT b.program_no,b.remarks,c.quantity knitting_issue_qnty,c.po_breakdown_id,b.id
		from  inv_issue_master a,inv_grey_fabric_issue_dtls b , order_wise_pro_details c, GBL_TEMP_ENGINE e
		where a.id=b.mst_id and b.id = c.dtls_id
		and c.trans_type = 2 and a.item_category=13 and a.entry_form in (16) and c.entry_form in (16)
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active = 1 and c.is_deleted = 0 and a.company_id=$company_name
		and b.program_no <> 0 and b.program_no is not null  and e.ref_val=c.po_breakdown_id and e.user_id= $user_name and e.entry_form=126 and e.ref_from=3 $store_name_b_cond");

		$idChkArr = array();
		foreach($sql_data as $row)
		{
			$knit_issue_arr[$row[csf('program_no')]][$row[csf('po_breakdown_id')]]['qnty'] +=$row[csf('knitting_issue_qnty')];
			$knit_issue_arr[$row[csf('program_no')]]['knit_id']=$row[csf('knit_id')];
			$knit_issue_arr[$row[csf('program_no')]][$row[csf('po_breakdown_id')]]['remarks']=$row[csf('remarks')];
		}
		unset($sql_data);

		$sql_iss_return=sql_select("select c.po_breakdown_id, a.booking_no, c.quantity from inv_receive_master a, inv_transaction b, order_wise_pro_details c, GBL_TEMP_ENGINE e where a.id = b.mst_id and b.transaction_type =4 and b.id=c.trans_id and c.entry_form=51 and a.receive_basis=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.ref_val=c.po_breakdown_id and e.user_id= $user_name and e.entry_form=126 and e.ref_from=3 $store_id_b_cond"); //$for_issue_po_id_cond 

		foreach($sql_iss_return as $row)
		{
			$issue_return_arr[$row[csf('booking_no')]][$row[csf('po_breakdown_id')]]['qnty'] +=$row[csf('quantity')];
		}
		unset($sql_iss_return);

		$roll_iss_rtn_sql="SELECT c.po_breakdown_id, b.pi_wo_batch_no, c.quantity
		from inv_receive_master a, inv_transaction b, order_wise_pro_details c, GBL_TEMP_ENGINE e
		where a.id = b.mst_id and b.transaction_type=4 and b.id=c.trans_id and c.entry_form=84 and a.receive_basis=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.ref_val=c.po_breakdown_id and e.user_id= $user_name and e.ref_from=3 and e.entry_form=126 $store_id_b_cond"; //$for_issue_po_id_cond

		//echo $roll_iss_rtn_sql;
		$roll_iss_rtn_data=sql_select($roll_iss_rtn_sql);
		foreach($roll_iss_rtn_data as $row)
		{
			$issue_return_arr[$row[csf('pi_wo_batch_no')]][$row[csf('po_breakdown_id')]]['qnty'] +=$row[csf('quantity')];
		}
		unset($roll_iss_rtn_data);

		$transfer_sql="SELECT b.from_program,b.to_program, a.entry_form, b.from_store, b.to_store,
		(case when c.trans_type in(6)  then c.quantity else 0 end) as item_transfer_out,
		(case when c.trans_type in(5) then c.quantity else 0 end) as item_transfer_in,
		a.from_order_id,a.to_order_id
		from inv_item_transfer_dtls b,inv_item_transfer_mst a,order_wise_pro_details c, GBL_TEMP_ENGINE e
		where a.id=b.mst_id  and c.dtls_id=b.id and c.trans_type in(5,6) and a.status_active=1 and a.is_deleted=0 and a.item_category=13
		and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		and c.entry_form in (83,13,80) and a.transfer_criteria in (1,2,4,6) and  b.from_program>0 and b.to_program>0 and (c.is_sales is null or c.is_sales = 0 or c.is_sales = 2) and e.ref_val=c.po_breakdown_id and e.user_id= $user_name and e.ref_from=3 and e.entry_form=126 $from_n_to_store_b_cond ";  //$for_issue_po_id_cond

		//echo $transfer_sql;
		$data_array=sql_select($transfer_sql); //and c.is_sales <> 1

		$transfer_qty_arr=array();
		foreach($data_array as $row_b)
		{
			if($row_b[csf("entry_form")] == "83" || $row_b[csf("entry_form")] == "13")
			{
				if(($cbo_store_name>0 && $row_b[csf("to_store")]==$cbo_store_name) || $cbo_store_name==0)
				{
					$transfer_qty_arr[$row_b[csf('to_program')]][$row_b[csf('to_order_id')]]['transfer_in']+=$row_b[csf('item_transfer_in')];
				}
			}
			if(($cbo_store_name>0 && $row_b[csf("from_store")]==$cbo_store_name) || $cbo_store_name==0)
			{
				$transfer_qty_arr[$row_b[csf('from_program')]][$row_b[csf('from_order_id')]]['transfer_out']+=$row_b[csf('item_transfer_out')];
			}
		}

		$get_barcode_sql="SELECT (case when c.trans_type in(6) then d.qnty else 0 end) as item_transfer_out, (case when c.trans_type in(5) then d.qnty else 0 end) as item_transfer_in, c.po_breakdown_id, c.trans_type, d.barcode_no,e.booking_no as program_no, b.from_store, b.to_store
	    from inv_item_transfer_dtls b,inv_item_transfer_mst a,order_wise_pro_details c, pro_roll_details d, pro_roll_details e, GBL_TEMP_ENGINE f 
	    where a.id=b.mst_id and c.dtls_id=b.id and a.id=d.mst_id and b.id=d.dtls_id and d.barcode_no= e.barcode_no and e.entry_form=2 and e.receive_basis=2
	    and c.trans_type in(5,6) and a.status_active=1 and a.is_deleted=0 and a.item_category=13 
	    and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form in (82,110) and a.transfer_criteria in (1,4,6,2) 
	    and (c.is_sales is null or c.is_sales = 0 or c.is_sales = 2)  and f.ref_val=c.po_breakdown_id and f.user_id= $user_name and f.ref_from=3 and f.entry_form=126 $from_n_to_store_b_cond";  //$for_issue_po_id_cond
		//echo $get_barcode_sql;die;
		$barcode_data_array=sql_select($get_barcode_sql);
		$trans_barcode_arr=array();
		foreach($barcode_data_array as $row)
		{
			//$trans_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
			if(($cbo_store_name>0 && $row[csf("to_store")]==$cbo_store_name) || $cbo_store_name==0){
				$transfer_qty_arr[$row[csf('program_no')]][$row[csf('po_breakdown_id')]]['transfer_in']+=$row[csf('item_transfer_in')];
			}
			if(($cbo_store_name>0 && $row[csf("from_store")]==$cbo_store_name) || $cbo_store_name==0){
				$transfer_qty_arr[$row[csf('program_no')]][$row[csf('po_breakdown_id')]]['transfer_out']+=$row[csf('item_transfer_out')];
			}
		}


		if(!empty($all_prog_for_issue))
		{
			//N.B (receive + transfer program no) temp entry
			fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 126, 4,$all_prog_for_issue, $empty_arr); 
			
			$yarn_production_wise_lot_count=sql_select("SELECT b.po_breakdown_id, a.yarn_lot, a.yarn_count as yarn_count, c.booking_id, a.id, a.yarn_prod_id
			from pro_grey_prod_entry_dtls a, order_wise_pro_details b,inv_receive_master c, GBL_TEMP_ENGINE d
			where a.id=b.dtls_id and a.mst_id = c.id and c.receive_basis = 2 and b.entry_form =2 and c.entry_form = 2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.booking_id=d.ref_val and d.ref_from=4 and d.entry_form=126 and d.user_id=$user_name");
			//$all_yarn_program_no_cond
			//$yarn_order_cond

			$chkData = array();
			foreach ($yarn_production_wise_lot_count as $val)
			{
				if($chkData[$val[csf("id")]] == "")
				{
					$chkData[$val[csf("id")]] = $val[csf("id")];
					$yarn_production_wise_lot_count_data[$val[csf("po_breakdown_id")]][$val[csf("booking_id")]]["lot"] .= $val[csf("yarn_lot")].",";
					$yarn_production_wise_lot_count_data[$val[csf("po_breakdown_id")]][$val[csf("booking_id")]]["count"] .= $val[csf("yarn_count")].",";
					$yarn_production_wise_lot_count_data[$val[csf("po_breakdown_id")]][$val[csf("booking_id")]]["yarn_prod_id"] .= $val[csf("yarn_prod_id")].",";

					$yarn_lot_arr[$val[csf('booking_id')]]['lot'] .=$val[csf("yarn_lot")].",";
					$yarn_lot_arr[$val[csf('booking_id')]]['ycount'] .=$val[csf("yarn_count")].",";
					$yarn_lot_arr[$val[csf('booking_id')]]['roll'] .=$val[csf("id")]."**".$val[csf("no_of_roll")].",";
					$yarn_lot_arr[$val[csf('booking_id')]]['yarn_prod_id'] .=$val[csf("yarn_prod_id")].",";
					$all_yarn_prod_id_arr[$val[csf("yarn_prod_id")]] = $val[csf("yarn_prod_id")];
				}
			}

			$all_yarn_prod_id_arr = array_filter($all_yarn_prod_id_arr);
			if(count($all_yarn_prod_id_arr) > 0)
			{
				fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 126, 5,$all_yarn_prod_id_arr, $empty_arr); // prod Id temp entry
				
				$supplier_yarn = return_library_array("select a.id, b.short_name from product_details_master a, lib_supplier b, GBL_TEMP_ENGINE c where  a.supplier_id = b.id and a.id=c.ref_val and c.user_id=$user_name and c.entry_form=126 and c.ref_from=5 and b.status_active = 1 and a.status_active=1","id","short_name"); //$all_yarn_prod_id_cond
			}

			$sql_arr = sql_select("SELECT d.id as prog_no, d.program_date, d.machine_dia, d.fabric_dia, c.gsm_weight, c.program_qnty, d.stitch_length, d.color_id, e.booking_no, e.fabric_desc from ppl_planning_entry_plan_dtls c, ppl_planning_info_entry_dtls d, ppl_planning_info_entry_mst e, GBL_TEMP_ENGINE f where e.id=d.mst_id and c.dtls_id=d.id and c.is_sales <> 1 and e.is_sales <> 1 and c.status_active =1 and c.is_deleted= 0 and d.status_active =1 and d.is_deleted= 0 and e.status_active = 1 and e.is_deleted= 0 and d.id=f.ref_val and f.ref_from=4 and f.user_id=$user_name and f.entry_form=126 group by d.id, d.program_date, d.machine_dia, d.fabric_dia, c.gsm_weight, c.program_qnty , d.stitch_length, d.color_id, e.booking_no, e.fabric_desc");
			
			//$all_trans_prog_cond 

			$trns_data_arr=array();
			foreach($sql_arr as $row)
			{
				$trns_data_arr[$row[csf('prog_no')]]['booking_no']=$row[csf('booking_no')];
				$trns_data_arr[$row[csf('prog_no')]]['program_date']=$row[csf('program_date')];
				$trns_data_arr[$row[csf('prog_no')]]['program_qnty']=$row[csf('program_qnty')];
				$trns_data_arr[$row[csf('prog_no')]]['mc_dia_gg']=$row[csf('machine_dia')].' / '.$row[csf('fabric_dia')];
				$trns_data_arr[$row[csf('prog_no')]]['fabric_desc']=$row[csf('fabric_desc')];
				$trns_data_arr[$row[csf('prog_no')]]['color_id']=$row[csf('color_id')];
				$trns_data_arr[$row[csf('prog_no')]]['gsm_weight']=$row[csf('gsm_weight')];
				$trns_data_arr[$row[csf('prog_no')]]['stitch_length']=$row[csf('stitch_length')];
				$trns_data_arr[$row[csf('prog_no')]]['job_no']=$row[csf('job_no_prefix_num')];
				$trns_data_arr[$row[csf('prog_no')]]['po_number']=$row[csf('po_number')];
				$trns_data_arr[$row[csf('prog_no')]]['buyer_name']=$row[csf('buyer_name')];
			}
		}
	}

	$r_id5=execute_query("delete from tmp_barcode_no where userid=$user_name and entry_form in (126)");
	$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_name and entry_form in (126)");
	if( $r_id5 && $r_id6)
	{
		oci_commit($con);
	}

	ob_start();
	?>
	<fieldset style="width:2250px;">
		<table width="2400" cellspacing="0" cellpadding="0" border="0" rules="all" >
			<tr class="form_caption">
				<td colspan="20" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
			</tr>
			<tr class="form_caption">
				<td colspan="20" align="center"><? echo $company_library[$company_name]; ?></td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2580" class="rpt_table" >
			<thead>
				<th width="40">SL</th>
				<th width="80">Prog. Date</th>
				<th width="100">Buyer</th>
				<th width="70">Job No</th>
				<th width="70">Internal Ref.</th>
				<th width="110">Order No</th>
				<th width="100">SBU</th>
				<th width="80">MC/F.Dia</th>
				<th width="70">YCount</th>
				<th width="70">Lot</th>
				<th width="100">Yarn Brand</th>
				<th width="150">Fab. Description</th>
				<th width="100">Fab. Color</th>
				<th width="60">FGSM</th>
				<th width="70">SL</th>
				<th width="100">Fab.Booking No</th>
				<th width="80">Book Qty/KG</th>
				<th width="70">Prog. No</th>
				<th width="80">Prog Qty/kg</th>

				<th width="100">Batch No</th>

				<th width="60">Roll</th>
				<th width="80">Receive Qty(kg)</th>
				<th width="80">Trans In Qty(kg)</th>
				<th width="80">Issue Ret. Qty(kg)</th>
				<th width="80">Total Qty(kg)</th>
				<th width="80">Delivery Qty(kg)</th>
				<th width="80">Trans Out Qty(kg)</th>
				<th width="80">Total Qty(kg)</th>
				<th width="80">Stock Qty(kg)</th>
				<th width="80">Age</th>
				<th width="100">Remarks</th>
			</thead>
		</table>
		<div style="width:2600px; overflow-y:scroll; max-height:450px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2580" class="rpt_table" id="tbl_list_search">
				<?
				
				$i=1;  $roll_arr=array(); $transfer_part_check_arr=array();//print_r($nameArray);
				foreach($nameArray as $row)
				{
					$po_no=$row[csf('po_id')];
					$knit_issue_qty=$knit_issue_arr[$row[csf('prog_no')]][$po_no]['qnty'];
					$remark=$knit_issue_arr[$row[csf('prog_no')]][$po_no]['remarks'];
					$knitting_recv_qnty=$knitting_recv_qnty_array[$row[csf('prog_no')]][$row[csf('po_id')]];
					$min_recv_date=$knitting_recv_date_array[$row[csf('prog_no')]][$row[csf('po_id')]];

					$issue_return_qnty = $issue_return_arr[$row[csf('prog_no')]][$po_no]['qnty'];

					/*$now = time(); // or your date as well
					$recv_date = strtotime($min_recv_date);
					$datediff = $now - $recv_date;
					$age = ($datediff / (60 * 60 * 24));*/
					$date1=date_create($min_recv_date);
					$date2=date_create();
					$diff=date_diff($date1,$date2);
					$age = $diff->format("%a");

					$trans_qty_out=$transfer_qty_arr[$row[csf('prog_no')]][$po_no]['transfer_out'];
					$trans_qty_in=$transfer_qty_arr[$row[csf('prog_no')]][$po_no]['transfer_in'];
					$totalRecv=$knitting_recv_qnty+$trans_qty_in+$issue_return_qnty;
					$totalIssue=$knit_issue_qty+$trans_qty_out;

					$tot_balance=$totalRecv-$totalIssue; //echo "$knitting_recv_qnty+$trans_qty_in+$issue_return_qnty-$totalIssue";die;

					if(number_format($tot_balance,2,".","") == "-0.00")
					{
						$tot_balance = "0.00";
					}

					if($row[csf('prog_no')]==25473)
					{
						//echo $row[csf('prog_no')]."="."$knitting_recv_qnty+$trans_qty_in+$issue_return_qnty-$totalIssue";
						//die;
					}

					if ((($get_upto_qnty == 1 && $tot_balance > $txt_qnty) || ($get_upto_qnty == 2 && $tot_balance < $txt_qnty) || ($get_upto_qnty == 3 && $tot_balance >= $txt_qnty) || ($get_upto_qnty == 4 && $tot_balance <= $txt_qnty) || ($get_upto_qnty == 5 && $tot_balance == $txt_qnty) || $get_upto_qnty == 0) && ($cbo_value_with==0 || ($cbo_value_with ==1 && $tot_balance>0 )))
					{

					/* if($row[csf('prog_no')]==25473)
					{
						echo $row[csf('prog_no')]."===<$tot_balance>  "."$knitting_recv_qnty+$trans_qty_in+$issue_return_qnty-$totalIssue";
						die;
					} */


						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$booking_qty=$booking_arr[$row[csf('booking_no')]]['grey_fab_qnty'];

						$roll_no='';
						$roll_data=explode(",",$yarn_lot_arr[$po_no]['roll']);
						foreach($roll_data as $val)
						{
							$val=explode("**",$val);
							if(!in_array($val[0],$roll_arr))
							{
								$roll_no+=$val[1];
								$roll_arr[]=$val[0];
							}
						}

						$color_id=$row[csf('color_id')];
						$color=array_filter(explode(',',$color_id));

						$y_count_id = array();$yarn_count_value="";
						$ylot = chop($yarn_production_wise_lot_count_data[$po_no][$row[csf('prog_no')]]["lot"],",");
						$ylot=implode(",", array_unique(explode(",", $ylot)));
						$y_count = chop($yarn_production_wise_lot_count_data[$po_no][$row[csf('prog_no')]]["count"],",");
						$y_count_id=array_unique(explode(',',$y_count));

						foreach($y_count_id as $val)
						{
							if($val>0)
							{
								if($yarn_count_value=='') $yarn_count_value=$yarn_count_library[$val]; else $yarn_count_value.=", ".$yarn_count_library[$val];
							}
						}

						$color_id_value='';
						foreach($color as $cval)
						{
							if($cval>0)
							{
								if($color_id_value=='') $color_id_value=$color_library[$cval]; else $color_id_value.=", ".$color_library[$cval];
							}
						}

						$yarn_prod_id = chop($yarn_production_wise_lot_count_data[$po_no][$row[csf('prog_no')]]["yarn_prod_id"],",");
						$yarn_prod_id_arr=array_filter(array_unique(explode(',',$yarn_prod_id)));
						$brand_supplier_arr=array();
						foreach ($yarn_prod_id_arr as $val)
						{
							$brand_supplier_arr[$supplier_yarn[$val]] = $supplier_yarn[$val];
						}
						$brand_supplier = implode(",", $brand_supplier_arr);
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="80"><p><? echo change_date_format($row[csf('program_date')]); ?></p></td>
							<td width="100"><p><? echo $buyer_library[$row[csf('buyer_name')]]; ?></p></td>
							<td width="70"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
							<td width="70"><p><? echo $row[csf('grouping')]; ?></p></td>
							<td width="110"><p title="po=<? echo $po_no;?>"><? echo $row[csf('po_number')]; ?></p></td>
							<td width="100"><p><? echo $buyer_library[$row[csf('client_id')]]; ?></p></td>
							<td width="80"><p><? echo $row[csf('machine_dia')]." / ".$row[csf("fabric_dia")]; ?></p></td>
							<td width="70"><p><? echo $yarn_count_value; ?></p></td>
							<td width="70" align="center" title="yarn prod=<? echo $yarn_prod_id;?>"><p><? echo $ylot; ?></p></td>
							<td width="100" align="center" ><p><? echo $brand_supplier; ?></p></td>
							<td width="150" align="center"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
							<td width="100"><p><? echo $color_id_value; ?></p></td>
							<td width="60" align="center"><p><? echo $row[csf('gsm_weight')]; ?></p></td>
							<td width="70" align="center"><p><? echo $row[csf('stitch_length')];  ?></p></td>
							<td width="100"><p><? echo $row[csf('booking_no')]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($booking_qty,2); ?></p></td>
							<td width="70" align="center"><p><? echo $row[csf('prog_no')]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[csf('program_qnty')],2); ?></p></td>

							<td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>

							<td align="center" width="60"><p><? echo $roll_no; ?></p></td>
							<td align="right" width="80"><p><a href='#report_details' onClick="openmypage_receive('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('prog_no')]; ?>','<? echo $row[csf('booking_no')];?>','<? echo $row[csf("knitting_source")]?>','<? echo $row[csf('machine_dia')];?>','receive_grey_popup');"><? echo number_format($knitting_recv_qnty,2,'.',''); ?> </a></p></td>
							

							<td width="80" align="right" title="<? echo 'Program:'.$row[csf('prog_no')].'PO:'.$po_no; ?>"><p><a  href="##"  onClick="openmypage_transfer_in('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('prog_no')]; ?>','<? echo $row[csf('booking_no')];?>','<? echo $row[csf("knitting_source")]?>','transfer_in_popup');" ><? echo number_format($trans_qty_in,2); ?></a></p></td>

							<td width="80" align="right"><p><a  href="##"  onClick="openmypage_issue_return('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('prog_no')]; ?>','<? echo $row[csf('booking_no')];?>','issue_return_popup');" ><? echo number_format($issue_return_qnty,2); ?></a></p></td>


							<td width="80" align="right" title="<? echo 'Recv='.$knitting_recv_qnty.'+ Trans_in='.$trans_qty_in.'+ Issue Rtn='.$issue_return_qnty ?>"><p><?  echo number_format($knitting_recv_qnty+$trans_qty_in+$issue_return_qnty,2); ?></p></td>
							<td align="right" width="80" title="<? echo $row[csf('po_id')].'='.$row[csf('prog_no')]; ?>">
								<a href='#report_details' onClick="openmypage_issue('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('prog_no')]; ?>','<? echo  $row[csf('booking_no')];?>','issue_grey_popup');"><? echo number_format($knit_issue_qty,2,'.',''); ?>
								</a>
							</td>
							<td width="80" align="right" title="<? echo 'Program:'.$row[csf('prog_no')].'PO:'.$po_no; ?>"><p><a   href="##"  onClick="openmypage_transfer_out('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('prog_no')]; ?>','<? echo $row[csf('booking_no')];?>','<? echo $row[csf("knitting_source")]?>','transfer_out_popup');" ><? echo number_format($trans_qty_out,2); ?></a></p></td>
							<td width="80" align="right"><p><?  echo number_format($knit_issue_qty+$trans_qty_out,2); ?></p></td>
							<td align="right" width="80">
								<a href='#report_details' onClick="openmypage_issue('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('prog_no')]; ?>','<? echo  $row[csf('booking_no')];?>','stock_grey_popup');">
									<?
									$tot_balance=$totalRecv-$totalIssue;
									echo number_format($tot_balance,2,'.','');
									?>
								</a>
							</td>
							<td width="80" align="right"><p><?  echo $age; ?></p></td>
							<td width="100"><? echo $remark; ?></td>
						</tr>
						<?
						$transfer_part_check_arr[$row[csf('prog_no')]][$row[csf('buyer_name')]][$row[csf('job_no_prefix_num')]][$row[csf('grouping')]][$row[csf('po_number')]][$row[csf('machine_dia')]." / ".$row[csf("fabric_dia")]][$row[csf('fabric_desc')]][$color_id_value]=1;
						$total_booking_qty+=$booking_qty;
						$total_program_qnty+=$row[csf('program_qnty')];
						$total_grey_qty+=$totalRecv;
						$total_stockbalance+=$tot_balance;
						$$total_age+=$age;
						$total_knitting_recv_qnty+=$knitting_recv_qnty;
						$total_trans_qty_in+=$trans_qty_in;
						$total_iss_return_qty +=$issue_return_qnty;
						$total_Recv_qty+=$knitting_recv_qnty+$trans_qty_in;
						$total_knit_issue_qty+=$knit_issue_qty;
						$total_trans_qty_out+=$trans_qty_out;
						$total_Issue_qty+=$knit_issue_qty+$trans_qty_out;

						$i++;
					}
				}
				unset($nameArray);
				$roll_arr=array();

				/*echo "<pre>";
				print_r($trns_row_data);
				echo "</pre>";*/

				foreach($trns_row_data as $prog_no=>$trns_data)
				{
					foreach($trns_data as $trns_data_key=>$row2)
					{
						$ex_trn_data=explode("!!!!",$trns_data_key);
						/*echo "<pre>";
						print_r($trns_data_key);*/
						$to_order_id=$ex_trn_data[0];
						// $trans_qty_in=$ex_trn_data[1];
						$trans_qty_in=$row2['trans_qty_in'];
						$booking_no="";$program_date="";$ylot="";$y_count="";$y_count_id="";
						$from_order_id = $ex_trn_data[1];
						$to_order_job_no = $ex_trn_data[2];
						$to_order_po_no = $ex_trn_data[3];
						$to_order_buyer_id = $ex_trn_data[4];
						$trans_internel_ref = $ex_trn_data[5];
						//$min_transfer_date = $ex_trn_data[6];
						$min_transfer_date = $row2['transfer_date'];

						$date2=date_create($min_transfer_date);
						$date3=date_create();
						$diff=date_diff($date2,$date3);
						$age = $diff->format("%a");

						$booking_no= $trns_data_arr[$prog_no]['booking_no'];
						$program_date = $trns_data_arr[$prog_no]['program_date'];

						$ylot=$yarn_lot_arr[$prog_no]['lot'];
						$ylot = implode(",",array_filter(array_unique(explode(",",chop($ylot)))));
						$y_count=$yarn_lot_arr[$prog_no]['ycount'];
						$y_count_id=array_unique(explode(',',$y_count));
						$yarn_count_value='';
						foreach($y_count_id as $val)
						{
							if($val>0)
							{
								if($yarn_count_value=='') $yarn_count_value=$yarn_count_library[$val]; else $yarn_count_value.=", ".$yarn_count_library[$val];
							}
						}

						$roll_no='';
						if ($from_order_id=='from_order') 
						{
							//echo "string";
							$roll_no=$row2['count'];
						}
						else
						{
							
							$from_order_id_exp=explode(",", $from_order_id);
							foreach ($from_order_id_exp as $rows_from_orderID) 
							{
								$roll_data=explode(",",$yarn_lot_arr[$rows_from_orderID]['roll']);
								foreach($roll_data as $val)
								{
									$val=explode("**",$val);
									if(!in_array($val[0],$roll_arr))
									{
										$roll_no+=$val[1];
										$roll_arr[]=$val[0];
									}
								}
							}
						}


						$color_id=$trns_data_arr[$prog_no]['color_id'];
						$color=array_filter(explode(',',$color_id));
						$color_id_value='';
						foreach($color as $cval)
						{
							if($cval>0)
							{
								if($color_id_value=='') $color_id_value=$color_library[$cval]; else $color_id_value.=", ".$color_library[$cval];
							}
						}

						$yarn_prod_id = chop($yarn_lot_arr[$prog_no]['yarn_prod_id'],",");
						$yarn_prod_id_arr=array_unique(explode(',',$yarn_prod_id));

						$brand_supplier_arr=array();
						foreach ($yarn_prod_id_arr as $val)
						{
							$brand_supplier_arr[$supplier_yarn[$val]] = $supplier_yarn[$val];
						}
						$brand_supplier = implode(",", $brand_supplier_arr);

						$booking_qty=$booking_arr[$trns_data_arr[$prog_no]['booking_no']]['grey_fab_qnty'];
						$transfer_out_qnty = $transfer_qty_arr[$prog_no][$to_order_id]['transfer_out'];
						$remark =  $knit_issue_arr[$prog_no][$to_order_id]['remarks'];
						$knit_issue_qty = $knit_issue_arr[$prog_no][$to_order_id]['qnty'];

						$issue_return_qnty = $issue_return_arr[$prog_no][$to_order_id]['qnty'];

						$totalTransIssueReturn = $trans_qty_in + $issue_return_qnty;

						$trans_balance = ($totalTransIssueReturn) - ($knit_issue_qty+$transfer_out_qnty);

						if(number_format($trans_balance,2,".","") == "-0.00")
						{
							$trans_balance = "0.00";
						}
						// echo $totalTransIssueReturn.'-'.$knit_issue_qty.'+'.$transfer_out_qnty.'==<br>';
						//1451.100000000000363797880709171295166015625-1451.09999999999990905052982270717620849609375+==
						//echo $trans_balance.'==<br>';//4.5474735088646411895751953125E-13==
						if ((($get_upto_qnty == 1 && $trans_balance > $txt_qnty) || ($get_upto_qnty == 2 && $trans_balance < $txt_qnty) || ($get_upto_qnty == 3 && $trans_balance >= $txt_qnty) || ($get_upto_qnty == 4 && $trans_balance <= $txt_qnty) || ($get_upto_qnty == 5 && $trans_balance == $txt_qnty) || $get_upto_qnty == 0) && ($cbo_value_with==0 || ($cbo_value_with ==1 && $trans_balance>0 )))
						{

							//if($trns_data_arr[$prog_no]['program_qnty'] > 0)
							//{
								if($transfer_part_check_arr[$prog_no][$to_order_buyer_id][$to_order_job_no][$trans_internel_ref][$to_order_po_no][$trns_data_arr[$prog_no]['mc_dia_gg']][$trns_data_arr[$prog_no]['fabric_desc']][$color_id_value]!=1)
								{

									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="40"><?echo $i?></td>
										<td width="80"><p><? echo change_date_format($program_date); ?></p></td>
										<td width="100"><p><? echo $buyer_library[$to_order_buyer_id];//$buyer_library[$trns_data_arr[$prog_no]['buyer_name']]; ?></p></td>
										<td width="70"><p><? echo $to_order_job_no;//$trns_data_arr[$prog_no]['job_no']//$po_lib_arr[$to_order_id]['job_no']; ?></p></td>
										<td width="70"><p><? echo $trans_internel_ref; ?></p></td>
										<td width="110"><p><? echo $to_order_po_no;//$trns_data_arr[$prog_no]['po_number'];//$po_lib_arr[$to_order_id]['po_number']; ?></p></td>
										<td width="100"><p><? echo $buyer_library[$to_order_client_arr[$to_order_id]]; ?></p></td>
										<td width="80"><p><? echo $trns_data_arr[$prog_no]['mc_dia_gg']; ?></p></td>
										<td width="70"><p><? echo $yarn_count_value; ?></p></td>
										<td width="70" align="center" title="<? echo $from_order_id;?>"><p><? echo $ylot; ?></p></td>
										<td width="100" align="center" ><p><? echo $brand_supplier; ?></p></td>
										<td width="150" align="center"><p><? echo $trns_data_arr[$prog_no]['fabric_desc']; ?></p></td>
										<td width="100"><p><? echo $color_id_value; ?></p></td>
										<td width="60" align="center"><p><? echo $trns_data_arr[$prog_no]['gsm_weight']; ?></p></td>
										<td width="70" align="center"><p><? echo $trns_data_arr[$prog_no]['stitch_length'];  ?></p></td>
										<td width="100"><p><? echo $booking_no ?></p></td>
										<td width="80" align="right"><p><? //echo number_format($booking_qty,2); ?></p></td>
										<td width="70" align="center"><p><? echo $prog_no ."- (T)" ?></p></td>
										<td width="80" align="right"><p><? //echo number_format($trns_data_arr[$prog_no]['program_qnty'],2); ?></p></td>

										<td width="100" align="right"><p></p></td>

										<td align="center" width="60"><p><? echo $roll_no; ?></p></td>
										<td align="right" width="80"></td>
										<td width="80" align="right"><p><a href="##" onClick="openmypage_transfer_in('<? echo $to_order_id; ?>','<? echo $prog_no; ?>','<? echo  $booking_no;?>','','transfer_in_popup');" ><? echo number_format($trans_qty_in,2); ?></a></p></td>

										<td width="80" align="right"><p><a href="##" onClick="openmypage_issue_return('<? echo $to_order_id; ?>','<? echo $prog_no; ?>','<? echo  $booking_no;?>','issue_return_popup');" ><? echo number_format($issue_return_qnty,2); ?></a></p></td>

										<td width="80" align="right"><p><?  echo number_format($totalTransIssueReturn,2); ?></p></td>
										<td align="right" width="80" title="<? echo $prog_no.'='.$to_order_id; ?>"><a href="##" onClick="openmypage_issue('<? echo $to_order_id; ?>','<? echo $prog_no; ?>','<? echo  $booking_no;?>','trans_issue_grey_popup');" ><? echo number_format($knit_issue_qty,2); ?></a></td>
										<td width="80" align="right"><p><a href="##" onClick="openmypage_transfer_out('<? echo $to_order_id; ?>','<? echo $prog_no; ?>','<? echo  $booking_no;?>','','transfer_out_popup');" ><? echo number_format($transfer_out_qnty,2); ?></a></p></td>
										<td width="80" align="right"><p><?  echo number_format($knit_issue_qty+$transfer_out_qnty,2); ?></p></td>
										<td align="right" width="80">
											<a href='#report_details' onClick="openmypage_issue('<? echo $to_order_id; ?>','<? echo $prog_no; ?>','<? echo  $booking_no;?>','trans_stock_grey_popup');">
												<?  echo number_format($trans_balance,2); ?>
											</a>
										</td>
										<td width="80" align="right"><? echo $age;?></td>
										<td width="100"><? echo $remark; ?></td>
									</tr>
									<?
									$total_trans_qty_in+=$trans_qty_in;
									$total_Recv_qty+=$totalTransIssueReturn;
									$total_iss_return_qty +=$issue_return_qnty;
									$total_knit_issue_qty+=$knit_issue_qty;
									$total_trans_qty_out+=$transfer_out_qnty;
									$total_Issue_qty+=$knit_issue_qty+$transfer_out_qnty;
									$total_stockbalance+=$trans_balance;
									$total_age+=$age;
									$i++;
								}
							//}
						
						}
					}
				}
				?>
			</table>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2580" class="rpt_table">
				<tfoot>
					<th width="40"></th>
					<th width="80"></th>
					<th width="100"></th>
					<th width="70"></th>
					<th width="70"></th>
					<th width="110"></th>
					<th width="100"></th>
					<th width="80"></th>
					<th width="70"></th>
					<th width="70"></th>
					<th width="100"></th>
					<th width="150"></th>
					<th width="100"></th>
					<th width="60"></th>
					<th width="70"></th>
					<th width="100"></th>
					<th width="80" id="value_total_booking_qty"><? echo number_format($total_booking_qty,2,'.',''); ?></th>
					<th width="70"></th>
					<th width="80" id="value_total_program_qnty"><? echo number_format($total_program_qnty,2,'.',''); ?></th>

					<th width="100" title="Batch No"></th>

					<th width="60"></th>
					<th width="80" id="value_total_grey_qty"><? echo number_format($total_knitting_recv_qnty,2,'.','');?></th>
					<th width="80" id="value_total_trans_qty_in"><? echo number_format($total_trans_qty_in,2,'.',''); ?></th>
					<th width="80" id="value_total_iss_return_qty"><? echo number_format($total_iss_return_qty,2,'.',''); ?></th>
					<th width="80" id="value_total_Recv_qty"><? echo number_format($total_Recv_qty,2,'.',''); ?></th>
					<th width="80" id="value_total_knit_issue_qty"><? echo number_format($total_knit_issue_qty,2,'.',''); ?></th>
					<th width="80" id="value_total_trans_qty_out"><? echo number_format($total_trans_qty_out,2,'.',''); ?></th>
					<th width="80" id="value_total_Issue_qty"><? echo number_format($total_Issue_qty,2,'.',''); ?></th>
					<th width="80" id="value_total_stockbalance"><? echo number_format($total_stockbalance,2,'.',''); ?></th>
					<th width="80" id=""><? //echo number_format($total_age,2,'.',''); ?></th>
					<th width="100"></th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}

//This Action will generate Only Gross data
if($action=="report_generate_gross")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_name= str_replace("'","",$cbo_company_name);
	$cbo_store_name= str_replace("'","",$cbo_store_name);
	$program_no= str_replace("'","",$txt_program_no);
	$type = str_replace("'","",$cbo_type);
	$cbo_year_selection = str_replace("'","",$cbo_year_selection);
	$cbo_year_selection = substr($cbo_year_selection, -2);
	$cbo_value_with = str_replace("'","",$cbo_value_with);
	$get_upto_qnty = str_replace("'","",$cbo_get_upto_qnty);
	$txt_qnty = str_replace("'","",$txt_qnty);
	$internal_ref = trim(str_replace("'","",$txt_internal_ref));

	if ($cbo_store_name>0) 
	{
		$store_id_a_cond = " and a.store_id = $cbo_store_name";
		$store_name_a_cond = " and a.store_name = $cbo_store_name";
		$store_name_b_cond = " and b.store_name = $cbo_store_name";
		$store_id_c_cond = " and c.store_id = $cbo_store_name";
		$store_id_b_cond = " and b.store_id = $cbo_store_name";
		$from_store_b_cond = " and b.from_store = $cbo_store_name";
		$to_store_b_cond = " and b.to_store = $cbo_store_name";

		$from_n_to_store_b_cond = " and (b.from_store = $cbo_store_name or b.to_store = $cbo_store_name)";
	}

	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond_trans=" and d.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond_trans="";
		}
		else
		{
			$buyer_id_cond="";
			$buyer_id_cond_trans="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name in (".str_replace("'","",$cbo_buyer_name).")";
		$buyer_id_cond_trans=" and d.buyer_name in (".str_replace("'","",$cbo_buyer_name).")";
	}

	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");


		}
		else if($db_type==2)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);


		}
		$date_cond=" and d.program_date between '$start_date' and '$end_date' ";
		$date_cond_roll=" and h.program_date between '$start_date' and '$end_date' ";
	}

	// Special Date Condition Only for Libas

	if($db_type==0)
	{
		$libas_date_cond = " and d.program_date > '2017-Dec-31'";
		$libas_date_cond_2 = " and b.program_date > '2017-Dec-31' ";
	}
	else if($db_type==2)
	{
		$libas_date_cond = " and d.program_date > '31-Dec-2017'";
		$libas_date_cond_2 = " and b.program_date > '31-Dec-2017' ";
	}

	$date_cond .= $libas_date_cond;

	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") {
		$job_no_cond="";
		$tran_job_no_cond="";
		$tran_row_job_cond="";
	}
	else {
		$job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
		$tran_job_no_cond=" and d.job_no_prefix_num in ($job_no) ";
		$tran_row_job_cond=" and b.job_no_prefix_num in ($job_no) ";
	}

	if ($program_no=="") $program_no_cond=""; else $program_no_cond=" and d.id in ($program_no) ";
	if ($program_no=="") $trans_program_no_cond=""; else $trans_program_no_cond=" and b.to_program in ($program_no) ";
	if ($program_no=="") $proram_no_cond_roll=""; else $proram_no_cond_roll=" and h.id in ($program_no) ";
	if ($internal_ref=="")
	{
		$internal_ref_cond="";
		$internal_ref_cond_2="";
	}
	else
	{
		$internal_ref_cond=" and b.grouping like '%".$internal_ref."%'";
		$internal_ref_cond_2=" and e.grouping like '%".$internal_ref."%'";
	}

	$booking_no=str_replace("'","",$txt_booking_no);
	if($booking_no == "") {
		$booking_no_cond ="";
		$booking_no_cond_program="";
		$booking_no_cond_transRow="";
	}
	else {
		$booking_no_cond=" and e.booking_no like '%".trim($booking_no) ."' and e.booking_no like '%-$cbo_year_selection-%'";
		$booking_no_cond_program=" and a.booking_no like '%".trim($booking_no) ."' and a.booking_no like '%-$cbo_year_selection-%'";
		$booking_no_cond_transRow=" and c.booking_no like '%".trim($booking_no) ."' and c.booking_no like '%-$cbo_year_selection-%'";
	}
	if ($program_no=="") $program_cond_trans=""; else $program_cond_trans=" and b.to_program in ($program_no) ";
	$all_prog_for_issue=array();

	if(str_replace("'","",trim($txt_order_no))=="")
	{
		$po_cond="";
		$trans_po_cond="";
	}
	else
	{
		if(str_replace("'","",$hide_order_id)!="")
		{
			$po_id=str_replace("'","",$hide_order_id);
			$po_cond="and b.id in(".$po_id.")";
			$po_cond2="and e.id in(".$po_id.")";
			$trans_po_cond=" and a.to_order_id in(".$po_id.")";
			$trans_po_cond_roll=" and b.to_order_id in(".$po_id.")";
		}
		else
		{
			$po_number="%".trim(str_replace("'","",$txt_order_no))."%";
			$po_cond="and b.po_number like '$po_number'";
			$po_cond2="and e.po_number like '$po_number'";
			$trans_po_cond=" and e.po_number like '$po_number'";
			$trans_po_cond_roll=" and e.po_number like '$po_number'";
		}
	}

	$con = connect();
	$r_id=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_name and entry_form in (126)");
	oci_commit($con);

	$sql="SELECT a.job_no_prefix_num, d.id as prog_no, d.program_date, a.job_no, a.company_name, a.buyer_name, b.id as po_id,b.grouping, d.machine_dia,d.fabric_dia, b.po_number, c.gsm_weight, sum(c.program_qnty) as program_qnty, d.stitch_length, d.color_id, e.booking_no, e.fabric_desc,d.knitting_source, d.batch_no, a.client_id
	from ppl_planning_entry_plan_dtls c, ppl_planning_info_entry_dtls d, ppl_planning_info_entry_mst e,wo_po_details_master a, wo_po_break_down b, lib_buyer f
	where a.id=b.job_id and e.id=d.mst_id and c.dtls_id=d.id and c.po_id=b.id and a.company_name=$company_name $buyer_id_cond $po_cond $job_no_cond $program_no_cond $date_cond $booking_no_cond $internal_ref_cond and c.is_sales !=1 and d.is_sales !=1  and a.buyer_name= f.id and f.status_active=1 and f.is_deleted=0 
	group by d.id, b.id, b.grouping, d.machine_dia, d.fabric_dia, d.program_date, e.booking_no, e.fabric_desc, d.color_id, c.gsm_weight, d.stitch_length, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, b.po_number, a.insert_date,d.knitting_source, d.batch_no, a.client_id
	order by a.buyer_name, a.job_no, b.id";//a.job_no=b.job_no_mst
	//echo $sql."<br><br>";//die;

	$nameArray=sql_select( $sql );
	foreach ($nameArray as $val)
	{
		if($program_arr[$val[csf("prog_no")]]=="")
		{
			$program_arr[$val[csf("prog_no")]]=$val[csf("prog_no")];
			$ProgNO = $val[csf("prog_no")];
		}

		$trans_po_arr[$val[csf("po_id")]] = $val[csf("po_id")];
		$all_prog_for_issue[$val[csf("prog_no")]] = $val[csf("prog_no")];
		$for_issue_po_arr[$val[csf('po_id')]] = $val[csf('po_id')];
	}

	$program_arr = array_filter($program_arr);

	if(!empty($program_arr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 126, 1,$program_arr, $empty_arr); // program temp entry

		$knitting_recv_qnty_array=array(); $prod_id_arr=array();

		$prod_sql="SELECT a.id, b.id as bid, c.id as cid, a.booking_id, c.po_breakdown_id as po_id, c.quantity as knitting_qnty, b.trans_id as trans_id, a.receive_date as receive_date, a.store_id from  GBL_TEMP_ENGINE e, inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where e.ref_val=a.booking_id and e.ref_from=1 and e.user_id=$user_name and e.entry_form=126 and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.id=b.mst_id and c.entry_form=2 and b.id=c.dtls_id and  b.status_active=1 and b.is_deleted=0 group by a.id, b.id, c.id, a.booking_id,c.po_breakdown_id, c.quantity, b.trans_id, a.receive_date, a.store_id";

		$sql_prod=sql_select($prod_sql);
		foreach($sql_prod as $row)
		{
			if($row[csf('trans_id')]>0)
			{
				if(($cbo_store_name > 0 && $cbo_store_name==$row[csf('store_id')])  ||  $cbo_store_name==0)
				{
					if($check_did[$row[csf('cid')]]=="") //check proposi does not repeat
					{
						$check_did[$row[csf('cid')]]=$row[csf('cid')];
						$knitting_recv_qnty_array[$row[csf('booking_id')]][$row[csf('po_id')]]+=$row[csf('knitting_qnty')];

						$knitting_recv_date_array[$row[csf('booking_id')]][$row[csf('po_id')]]=$row[csf('receive_date')];
						if($knitting_recv_date_array[$row[csf('booking_id')]][$row[csf('po_id')]] > $row[csf("receive_date")])
						{
							$minimum_date = $row[csf("receive_date")];
						}else{
							$minimum_date = $knitting_recv_date_array[$row[csf('booking_id')]][$row[csf('po_id')]];
						}

						$knitting_recv_date_array[$row[csf('booking_id')]][$row[csf('po_id')]] = $minimum_date;
					}
				}
			}
			else
			{
				$prod_id_arr[$row[csf('booking_id')]].=$row[csf('id')].",";
				$program_ref_arr[$row[csf('id')]] = $row[csf('booking_id')];
				$production_id_arr[$row[csf('id')]] = $row[csf('id')];
			}
		}
		/*echo "<pre>";
		print_r($knitting_recv_qnty_array);die;*/

		$production_id_arr = array_filter($production_id_arr);
		if(count($production_id_arr) > 0)
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 126, 2,$production_id_arr, $empty_arr); // production Id temp entry

			$recv_array=array();

			$sql_recv=sql_select("select a.booking_id,c.po_breakdown_id as po_id,sum(c.quantity) as knitting_qnty, min(receive_date) as receive_date from GBL_TEMP_ENGINE d, inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c where d.ref_val=a.booking_id and d.user_id=$user_name and d.entry_form=126 and d.ref_from=2 and a.id=b.mst_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=22 and c.entry_form=22 and a.receive_basis=9 and b.status_active=1 and b.is_deleted=0 $store_id_a_cond group by a.booking_id,c.po_breakdown_id");

			$minimum_date = "";
			foreach($sql_recv as $row)
			{
				$recv_array[$row[csf('booking_id')]][$row[csf('po_id')]]=$row[csf('knitting_qnty')];
				$knitting_recv_qnty_array[$program_ref_arr[$row[csf('booking_id')]]][$row[csf('po_id')]]+=$row[csf('knitting_qnty')];
				$knitting_recv_date_array[$program_ref_arr[$row[csf('booking_id')]]][$row[csf('po_id')]] =$row[csf('receive_date')];
				if($knitting_recv_date_array[$program_ref_arr[$row[csf('booking_id')]]][$row[csf('po_id')]] > $row[csf("receive_date")])
				{
					$minimum_date = $row[csf("receive_date")];
				}else{
					$minimum_date = $knitting_recv_date_array[$program_ref_arr[$row[csf('booking_id')]]][$row[csf('po_id')]];
				}

				$knitting_recv_date_array[$program_ref_arr[$row[csf('booking_id')]]][$row[csf('po_id')]] = $minimum_date;

				$for_issue_po_arr[$row[csf('po_id')]] = $row[csf('po_id')];

			}
		}
	}


	$booking_arr=array();
	$sql_book=sql_select("select  a.booking_no,sum(a.grey_fab_qnty) as grey_fab_qnty from wo_booking_dtls a,wo_booking_mst b where a.booking_no=b.booking_no and b.booking_type=1 and  a.status_active=1 and a.is_deleted=0 $booking_no_cond_program group by a.booking_no");
	foreach($sql_book as $row_b)
	{

		$booking_arr[$row_b[csf('booking_no')]]['grey_fab_qnty']=$row_b[csf('grey_fab_qnty')];
	}

	if($db_type==0){
		$from_order_idCond="group_concat( a.from_order_id) as from_order_id";		
		$from_order_idCond="group_concat( b.from_order_id) as from_order_id";		
	}
	else
	{
		$from_order_idCond="listagg(cast( a.from_order_id as varchar2(4000)), ',') within group (order by a.id) as from_order_id";
		$from_order_idCond="listagg(cast( b.from_order_id as varchar2(4000)), ',') within group (order by a.id) as from_order_id";
	}

	$trans_sql="SELECT b.to_program, $from_order_idCond, min(a.transfer_date) as transfer_date,
	sum(case when c.trans_type in(5) then c.quantity else 0 end) as item_transfer_in,a.to_order_id , e.po_number, d.job_no_prefix_num, d.buyer_name, e.grouping, d.client_id
	from inv_item_transfer_dtls b,inv_item_transfer_mst a, order_wise_pro_details c, wo_po_details_master d, wo_po_break_down e, ppl_planning_info_entry_dtls h, lib_buyer f
	where a.id=b.mst_id and c.dtls_id=b.id and d.id = e.job_id and a.to_order_id = e.id and b.to_program=h.id and c.trans_type in(5) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=13 and c.entry_form in (13) and a.entry_form in (13) and b.from_program>0 and b.to_program>0 and c.is_sales <> 1 and a.transfer_criteria in (1,2,4) and d.company_name=$company_name $trns_to_po_cond $tran_job_no_cond $trans_po_cond $program_cond_trans $buyer_id_cond_trans $internal_ref_cond_2 $date_cond_roll and d.buyer_name=f.id and f.status_active=1 and f.is_deleted=0 $to_store_b_cond
	group by b.to_program,a.to_order_id, e.po_number, d.job_no_prefix_num, d.buyer_name, e.grouping, d.client_id";

	$data_trans= sql_select($trans_sql); //$transfer_all_po_cond
	$trns_row_data=array();$trns_row_data_from=array();
	foreach($data_trans as $row_b)
	{
		$trns_row_data[$row_b[csf('to_program')]][$row_b[csf('to_order_id')]."!!!!"."from_order".'!!!!'.$row_b[csf('job_no_prefix_num')].'!!!!'.$row_b[csf('po_number')].'!!!!'.$row_b[csf('buyer_name')].'!!!!'.$row_b[csf('grouping')]]['trans_qty_in'] += $row_b[csf('item_transfer_in')];
		
		$trns_row_data[$row_b[csf('to_program')]][$row_b[csf('to_order_id')]."!!!!"."from_order".'!!!!'.$row_b[csf('job_no_prefix_num')].'!!!!'.$row_b[csf('po_number')].'!!!!'.$row_b[csf('buyer_name')].'!!!!'.$row_b[csf('grouping')]]['transfer_date'] = $row_b[csf('transfer_date')];

		$roll_no=0;
		$from_order_id_exp=explode(",", $row_b[csf('from_order_id')]);
		foreach ($from_order_id_exp as $rows_from_orderID) 
		{
			$roll_data=explode(",",$yarn_lot_arr[$rows_from_orderID]['roll']);
			foreach($roll_data as $val)
			{
				$val=explode("**",$val);
				if(!in_array($val[0],$roll_arr))
				{
					$roll_no+=$val[1];
					$roll_arr[]=$val[0];
				}
			}
		}

		$trns_row_data[$row_b[csf('to_program')]][$row_b[csf('to_order_id')]."!!!!"."from_order".'!!!!'.$row_b[csf('job_no_prefix_num')].'!!!!'.$row_b[csf('po_number')].'!!!!'.$row_b[csf('buyer_name')].'!!!!'.$row_b[csf('grouping')]]['count'] += $roll_no;

		$trns_row_prog_ref_data[$row_b[csf('to_program')]] = $row_b[csf('to_program')];
		//added in 03-01-2020
		$all_prog_for_issue[$row_b[csf('to_program')]] = $row_b[csf('to_program')];
		$for_issue_po_arr[$row_b[csf('to_order_id')]] = $row_b[csf('to_order_id')];
		$to_order_client_arr[$row_b[csf('to_order_id')]] = $row_b[csf('client_id')];

	}
	unset($data_trans);

	$for_issue_po_arr=array_filter($for_issue_po_arr);
	if(!empty($for_issue_po_arr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 126, 3,$for_issue_po_arr, $empty_arr); // PO Id temp entry

		$sql_data=sql_select("SELECT b.program_no,b.remarks,c.quantity knitting_issue_qnty,c.po_breakdown_id,b.id
		from  inv_issue_master a,inv_grey_fabric_issue_dtls b , order_wise_pro_details c, GBL_TEMP_ENGINE e
		where a.id=b.mst_id and b.id = c.dtls_id
		and c.trans_type = 2 and a.item_category=13 and a.entry_form in (16) and c.entry_form in (16)
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active = 1 and c.is_deleted = 0
		and b.program_no <> 0 and b.program_no is not null  and e.ref_val=c.po_breakdown_id and e.user_id= $user_name and e.entry_form=126 and e.ref_from=3 $store_name_b_cond");

		$idChkArr = array();
		foreach($sql_data as $row)
		{
			$knit_issue_arr[$row[csf('program_no')]][$row[csf('po_breakdown_id')]]['qnty'] +=$row[csf('knitting_issue_qnty')];
			$knit_issue_arr[$row[csf('program_no')]]['knit_id']=$row[csf('knit_id')];
			$knit_issue_arr[$row[csf('program_no')]][$row[csf('po_breakdown_id')]]['remarks']=$row[csf('remarks')];
		}
		unset($sql_data);

		$sql_iss_return=sql_select("select c.po_breakdown_id, a.booking_no, c.quantity from inv_receive_master a, inv_transaction b, order_wise_pro_details c, GBL_TEMP_ENGINE e where a.id = b.mst_id and b.transaction_type =4 and b.id=c.trans_id and c.entry_form=51 and a.receive_basis=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.ref_val=c.po_breakdown_id and e.user_id= $user_name and e.entry_form=126 and e.ref_from=3 $store_id_b_cond"); //$for_issue_po_id_cond 

		foreach($sql_iss_return as $row)
		{
			$issue_return_arr[$row[csf('booking_no')]][$row[csf('po_breakdown_id')]]['qnty'] +=$row[csf('quantity')];
		}
		unset($sql_iss_return);

		$transfer_sql="SELECT b.from_program,b.to_program, a.entry_form, b.from_store, b.to_store,
		(case when c.trans_type in(6)  then c.quantity else 0 end) as item_transfer_out,
		(case when c.trans_type in(5) then c.quantity else 0 end) as item_transfer_in,
		a.from_order_id,a.to_order_id
		from inv_item_transfer_dtls b,inv_item_transfer_mst a,order_wise_pro_details c, GBL_TEMP_ENGINE e
		where a.id=b.mst_id  and c.dtls_id=b.id and c.trans_type in(5,6) and a.status_active=1 and a.is_deleted=0 and a.item_category=13
		and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		and c.entry_form in (83,13,80) and a.transfer_criteria in (1,2,4,6) and  b.from_program>0 and b.to_program>0 and (c.is_sales is null or c.is_sales = 0 or c.is_sales = 2) and e.ref_val=c.po_breakdown_id and e.user_id= $user_name and e.ref_from=3 and e.entry_form=126 $from_n_to_store_b_cond ";  //$for_issue_po_id_cond

		//echo $transfer_sql;
		$data_array=sql_select($transfer_sql); //and c.is_sales <> 1

		$transfer_qty_arr=array();
		foreach($data_array as $row_b)
		{
			if($row_b[csf("entry_form")] == "83" || $row_b[csf("entry_form")] == "13")
			{
				if(($cbo_store_name>0 && $row_b[csf("to_store")]==$cbo_store_name) || $cbo_store_name==0)
				{
					$transfer_qty_arr[$row_b[csf('to_program')]][$row_b[csf('to_order_id')]]['transfer_in']+=$row_b[csf('item_transfer_in')];
				}
			}
			if(($cbo_store_name>0 && $row_b[csf("from_store")]==$cbo_store_name) || $cbo_store_name==0)
			{
				$transfer_qty_arr[$row_b[csf('from_program')]][$row_b[csf('from_order_id')]]['transfer_out']+=$row_b[csf('item_transfer_out')];
			}
		}

		if(!empty($all_prog_for_issue))
		{
			//N.B (receive + transfer program no) temp entry
			fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 126, 4,$all_prog_for_issue, $empty_arr); 
			
			$yarn_production_wise_lot_count=sql_select("SELECT b.po_breakdown_id, a.yarn_lot, a.yarn_count as yarn_count, c.booking_id, a.id, a.yarn_prod_id
			from pro_grey_prod_entry_dtls a, order_wise_pro_details b,inv_receive_master c, GBL_TEMP_ENGINE d
			where a.id=b.dtls_id and a.mst_id = c.id and c.receive_basis = 2 and b.entry_form =2 and c.entry_form = 2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.booking_id=d.ref_val and d.ref_from=4 and d.entry_form=126 and d.user_id=$user_name");

			$chkData = array();
			foreach ($yarn_production_wise_lot_count as $val)
			{
				if($chkData[$val[csf("id")]] == "")
				{
					$chkData[$val[csf("id")]] = $val[csf("id")];
					$yarn_production_wise_lot_count_data[$val[csf("po_breakdown_id")]][$val[csf("booking_id")]]["lot"] .= $val[csf("yarn_lot")].",";
					$yarn_production_wise_lot_count_data[$val[csf("po_breakdown_id")]][$val[csf("booking_id")]]["count"] .= $val[csf("yarn_count")].",";
					$yarn_production_wise_lot_count_data[$val[csf("po_breakdown_id")]][$val[csf("booking_id")]]["yarn_prod_id"] .= $val[csf("yarn_prod_id")].",";

					$yarn_lot_arr[$val[csf('booking_id')]]['lot'] .=$val[csf("yarn_lot")].",";
					$yarn_lot_arr[$val[csf('booking_id')]]['ycount'] .=$val[csf("yarn_count")].",";
					$yarn_lot_arr[$val[csf('booking_id')]]['roll'] .=$val[csf("id")]."**".$val[csf("no_of_roll")].",";
					$yarn_lot_arr[$val[csf('booking_id')]]['yarn_prod_id'] .=$val[csf("yarn_prod_id")].",";
					$all_yarn_prod_id_arr[$val[csf("yarn_prod_id")]] = $val[csf("yarn_prod_id")];
				}
			}

			$all_yarn_prod_id_arr = array_filter($all_yarn_prod_id_arr);
			if(count($all_yarn_prod_id_arr) > 0)
			{
				fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 126, 5,$all_yarn_prod_id_arr, $empty_arr); // prod Id temp entry
				
				$supplier_yarn = return_library_array("select a.id, b.short_name from product_details_master a, lib_supplier b, GBL_TEMP_ENGINE c where  a.supplier_id = b.id and a.id=c.ref_val and c.user_id=$user_name and c.entry_form=126 and c.ref_from=5 and b.status_active = 1 and a.status_active=1","id","short_name"); //$all_yarn_prod_id_cond
			}

			$sql_arr = sql_select("SELECT d.id as prog_no, d.program_date, d.machine_dia, d.fabric_dia, c.gsm_weight, c.program_qnty, d.stitch_length, d.color_id, e.booking_no, e.fabric_desc from ppl_planning_entry_plan_dtls c, ppl_planning_info_entry_dtls d, ppl_planning_info_entry_mst e, GBL_TEMP_ENGINE f where e.id=d.mst_id and c.dtls_id=d.id and c.is_sales <> 1 and e.is_sales <> 1 and c.status_active =1 and c.is_deleted= 0 and d.status_active =1 and d.is_deleted= 0 and e.status_active = 1 and e.is_deleted= 0 and d.id=f.ref_val and f.ref_from=4 and f.user_id=$user_name and f.entry_form=126 group by d.id, d.program_date, d.machine_dia, d.fabric_dia, c.gsm_weight, c.program_qnty , d.stitch_length, d.color_id, e.booking_no, e.fabric_desc");
			
			//$all_trans_prog_cond 

			$trns_data_arr=array();
			foreach($sql_arr as $row)
			{
				$trns_data_arr[$row[csf('prog_no')]]['booking_no']=$row[csf('booking_no')];
				$trns_data_arr[$row[csf('prog_no')]]['program_date']=$row[csf('program_date')];
				$trns_data_arr[$row[csf('prog_no')]]['program_qnty']=$row[csf('program_qnty')];
				$trns_data_arr[$row[csf('prog_no')]]['mc_dia_gg']=$row[csf('machine_dia')].' / '.$row[csf('fabric_dia')];
				$trns_data_arr[$row[csf('prog_no')]]['fabric_desc']=$row[csf('fabric_desc')];
				$trns_data_arr[$row[csf('prog_no')]]['color_id']=$row[csf('color_id')];
				$trns_data_arr[$row[csf('prog_no')]]['gsm_weight']=$row[csf('gsm_weight')];
				$trns_data_arr[$row[csf('prog_no')]]['stitch_length']=$row[csf('stitch_length')];
				$trns_data_arr[$row[csf('prog_no')]]['job_no']=$row[csf('job_no_prefix_num')];
				$trns_data_arr[$row[csf('prog_no')]]['po_number']=$row[csf('po_number')];
				$trns_data_arr[$row[csf('prog_no')]]['buyer_name']=$row[csf('buyer_name')];
			}
		}
	}

	$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_name and entry_form in (126)");
	oci_commit($con);

	ob_start();
	?>
	<fieldset style="width:2250px;">
		<table width="2400" cellspacing="0" cellpadding="0" border="0" rules="all" >
			<tr class="form_caption">
				<td colspan="20" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
			</tr>
			<tr class="form_caption">
				<td colspan="20" align="center"><? echo $company_library[$company_name]; ?></td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2580" class="rpt_table" >
			<thead>
				<th width="40">SL</th>
				<th width="80">Prog. Date</th>
				<th width="100">Buyer</th>
				<th width="70">Job No</th>
				<th width="70">Internal Ref.</th>
				<th width="110">Order No</th>
				<th width="100">SBU</th>
				<th width="80">MC/F.Dia</th>
				<th width="70">YCount</th>
				<th width="70">Lot</th>
				<th width="100">Yarn Brand</th>
				<th width="150">Fab. Description</th>
				<th width="100">Fab. Color</th>
				<th width="60">FGSM</th>
				<th width="70">SL</th>
				<th width="100">Fab.Booking No</th>
				<th width="80">Book Qty/KG</th>
				<th width="70">Prog. No</th>
				<th width="80">Prog Qty/kg</th>

				<th width="100">Batch No</th>

				<th width="60">Roll</th>
				<th width="80">Receive Qty(kg)</th>
				<th width="80">Trans In Qty(kg)</th>
				<th width="80">Issue Ret. Qty(kg)</th>
				<th width="80">Total Qty(kg)</th>
				<th width="80">Delivery Qty(kg)</th>
				<th width="80">Trans Out Qty(kg)</th>
				<th width="80">Total Qty(kg)</th>
				<th width="80">Stock Qty(kg)</th>
				<th width="80">Age</th>
				<th width="100">Remarks</th>
			</thead>
		</table>
		<div style="width:2600px; overflow-y:scroll; max-height:450px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2580" class="rpt_table" id="tbl_list_search">
				<?
				
				$i=1;  $roll_arr=array(); $transfer_part_check_arr=array();//print_r($nameArray);
				foreach($nameArray as $row)
				{
					$po_no=$row[csf('po_id')];
					$knit_issue_qty=$knit_issue_arr[$row[csf('prog_no')]][$po_no]['qnty'];
					$remark=$knit_issue_arr[$row[csf('prog_no')]][$po_no]['remarks'];
					$knitting_recv_qnty=$knitting_recv_qnty_array[$row[csf('prog_no')]][$row[csf('po_id')]];
					$min_recv_date=$knitting_recv_date_array[$row[csf('prog_no')]][$row[csf('po_id')]];

					$issue_return_qnty = $issue_return_arr[$row[csf('prog_no')]][$po_no]['qnty'];

					/*$now = time(); // or your date as well
					$recv_date = strtotime($min_recv_date);
					$datediff = $now - $recv_date;
					$age = ($datediff / (60 * 60 * 24));*/
					$date1=date_create($min_recv_date);
					$date2=date_create();
					$diff=date_diff($date1,$date2);
					$age = $diff->format("%a");

					$trans_qty_out=$transfer_qty_arr[$row[csf('prog_no')]][$po_no]['transfer_out'];
					$trans_qty_in=$transfer_qty_arr[$row[csf('prog_no')]][$po_no]['transfer_in'];
					$totalRecv=$knitting_recv_qnty+$trans_qty_in+$issue_return_qnty;
					$totalIssue=$knit_issue_qty+$trans_qty_out;

					$tot_balance=$totalRecv-$totalIssue; //echo "$knitting_recv_qnty+$trans_qty_in+$issue_return_qnty-$totalIssue";die;

					if(number_format($tot_balance,2,".","") == "-0.00")
					{
						$tot_balance = "0.00";
					}

					if($row[csf('prog_no')]==25473)
					{
						//echo $row[csf('prog_no')]."="."$knitting_recv_qnty+$trans_qty_in+$issue_return_qnty-$totalIssue";
						//die;
					}

					if ((($get_upto_qnty == 1 && $tot_balance > $txt_qnty) || ($get_upto_qnty == 2 && $tot_balance < $txt_qnty) || ($get_upto_qnty == 3 && $tot_balance >= $txt_qnty) || ($get_upto_qnty == 4 && $tot_balance <= $txt_qnty) || ($get_upto_qnty == 5 && $tot_balance == $txt_qnty) || $get_upto_qnty == 0) && ($cbo_value_with==0 || ($cbo_value_with ==1 && $tot_balance>0 )))
					{

					/* if($row[csf('prog_no')]==25473)
					{
						echo $row[csf('prog_no')]."===<$tot_balance>  "."$knitting_recv_qnty+$trans_qty_in+$issue_return_qnty-$totalIssue";
						die;
					} */


						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$booking_qty=$booking_arr[$row[csf('booking_no')]]['grey_fab_qnty'];

						$roll_no='';
						$roll_data=explode(",",$yarn_lot_arr[$po_no]['roll']);
						foreach($roll_data as $val)
						{
							$val=explode("**",$val);
							if(!in_array($val[0],$roll_arr))
							{
								$roll_no+=$val[1];
								$roll_arr[]=$val[0];
							}
						}

						$color_id=$row[csf('color_id')];
						$color=array_filter(explode(',',$color_id));

						$y_count_id = array();$yarn_count_value="";
						$ylot = chop($yarn_production_wise_lot_count_data[$po_no][$row[csf('prog_no')]]["lot"],",");
						$ylot=implode(",", array_unique(explode(",", $ylot)));
						$y_count = chop($yarn_production_wise_lot_count_data[$po_no][$row[csf('prog_no')]]["count"],",");
						$y_count_id=array_unique(explode(',',$y_count));

						foreach($y_count_id as $val)
						{
							if($val>0)
							{
								if($yarn_count_value=='') $yarn_count_value=$yarn_count_library[$val]; else $yarn_count_value.=", ".$yarn_count_library[$val];
							}
						}

						$color_id_value='';
						foreach($color as $cval)
						{
							if($cval>0)
							{
								if($color_id_value=='') $color_id_value=$color_library[$cval]; else $color_id_value.=", ".$color_library[$cval];
							}
						}

						$yarn_prod_id = chop($yarn_production_wise_lot_count_data[$po_no][$row[csf('prog_no')]]["yarn_prod_id"],",");
						$yarn_prod_id_arr=array_filter(array_unique(explode(',',$yarn_prod_id)));
						$brand_supplier_arr=array();
						foreach ($yarn_prod_id_arr as $val)
						{
							$brand_supplier_arr[$supplier_yarn[$val]] = $supplier_yarn[$val];
						}
						$brand_supplier = implode(",", $brand_supplier_arr);
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="80"><p><? echo change_date_format($row[csf('program_date')]); ?></p></td>
							<td width="100"><p><? echo $buyer_library[$row[csf('buyer_name')]]; ?></p></td>
							<td width="70"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
							<td width="70"><p><? echo $row[csf('grouping')]; ?></p></td>
							<td width="110"><p title="po=<? echo $po_no;?>"><? echo $row[csf('po_number')]; ?></p></td>
							<td width="100"><p><? echo $buyer_library[$row[csf('client_id')]]; ?></p></td>
							<td width="80"><p><? echo $row[csf('machine_dia')]." / ".$row[csf("fabric_dia")]; ?></p></td>
							<td width="70"><p><? echo $yarn_count_value; ?></p></td>
							<td width="70" align="center" title="yarn prod=<? echo $yarn_prod_id;?>"><p><? echo $ylot; ?></p></td>
							<td width="100" align="center" ><p><? echo $brand_supplier; ?></p></td>
							<td width="150" align="center"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
							<td width="100"><p><? echo $color_id_value; ?></p></td>
							<td width="60" align="center"><p><? echo $row[csf('gsm_weight')]; ?></p></td>
							<td width="70" align="center"><p><? echo $row[csf('stitch_length')];  ?></p></td>
							<td width="100"><p><? echo $row[csf('booking_no')]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($booking_qty,2); ?></p></td>
							<td width="70" align="center"><p><? echo $row[csf('prog_no')]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[csf('program_qnty')],2); ?></p></td>

							<td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>

							<td align="center" width="60"><p><? echo $roll_no; ?></p></td>
							<td align="right" width="80"><p><a href='#report_details' onClick="openmypage_receive('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('prog_no')]; ?>','<? echo $row[csf('booking_no')];?>','<? echo $row[csf("knitting_source")]?>','<? echo $row[csf('machine_dia')];?>','receive_grey_popup');"><? echo number_format($knitting_recv_qnty,2,'.',''); ?> </a></p></td>
							

							<td width="80" align="right" title="<? echo 'Program:'.$row[csf('prog_no')].'PO:'.$po_no; ?>"><p><a  href="##"  onClick="openmypage_transfer_in('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('prog_no')]; ?>','<? echo $row[csf('booking_no')];?>','<? echo $row[csf("knitting_source")]?>','transfer_in_popup');" ><? echo number_format($trans_qty_in,2); ?></a></p></td>

							<td width="80" align="right"><p><a  href="##"  onClick="openmypage_issue_return('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('prog_no')]; ?>','<? echo $row[csf('booking_no')];?>','issue_return_popup');" ><? echo number_format($issue_return_qnty,2); ?></a></p></td>


							<td width="80" align="right" title="<? echo 'Recv='.$knitting_recv_qnty.'+ Trans_in='.$trans_qty_in.'+ Issue Rtn='.$issue_return_qnty ?>"><p><?  echo number_format($knitting_recv_qnty+$trans_qty_in+$issue_return_qnty,2); ?></p></td>
							<td align="right" width="80" title="<? echo $row[csf('po_id')].'='.$row[csf('prog_no')]; ?>">
								<a href='#report_details' onClick="openmypage_issue('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('prog_no')]; ?>','<? echo  $row[csf('booking_no')];?>','issue_grey_popup');"><? echo number_format($knit_issue_qty,2,'.',''); ?>
								</a>
							</td>
							<td width="80" align="right" title="<? echo 'Program:'.$row[csf('prog_no')].'PO:'.$po_no; ?>"><p><a   href="##"  onClick="openmypage_transfer_out('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('prog_no')]; ?>','<? echo $row[csf('booking_no')];?>','<? echo $row[csf("knitting_source")]?>','transfer_out_popup');" ><? echo number_format($trans_qty_out,2); ?></a></p></td>
							<td width="80" align="right"><p><?  echo number_format($knit_issue_qty+$trans_qty_out,2); ?></p></td>
							<td align="right" width="80">
								<a href='#report_details' onClick="openmypage_issue('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('prog_no')]; ?>','<? echo  $row[csf('booking_no')];?>','stock_grey_popup');">
									<?
									$tot_balance=$totalRecv-$totalIssue;
									echo number_format($tot_balance,2,'.','');
									?>
								</a>
							</td>
							<td width="80" align="right"><p><?  echo $age; ?></p></td>
							<td width="100"><? echo $remark; ?></td>
						</tr>
						<?
						$transfer_part_check_arr[$row[csf('prog_no')]][$row[csf('buyer_name')]][$row[csf('job_no_prefix_num')]][$row[csf('grouping')]][$row[csf('po_number')]][$row[csf('machine_dia')]." / ".$row[csf("fabric_dia")]][$row[csf('fabric_desc')]][$color_id_value]=1;
						$total_booking_qty+=$booking_qty;
						$total_program_qnty+=$row[csf('program_qnty')];
						$total_grey_qty+=$totalRecv;
						$total_stockbalance+=$tot_balance;
						$$total_age+=$age;
						$total_knitting_recv_qnty+=$knitting_recv_qnty;
						$total_trans_qty_in+=$trans_qty_in;
						$total_iss_return_qty +=$issue_return_qnty;
						$total_Recv_qty+=$knitting_recv_qnty+$trans_qty_in;
						$total_knit_issue_qty+=$knit_issue_qty;
						$total_trans_qty_out+=$trans_qty_out;
						$total_Issue_qty+=$knit_issue_qty+$trans_qty_out;

						$i++;
					}
				}
				unset($nameArray);
				$roll_arr=array();

				/*echo "<pre>";
				print_r($trns_row_data);
				echo "</pre>";*/

				foreach($trns_row_data as $prog_no=>$trns_data)
				{
					foreach($trns_data as $trns_data_key=>$row2)
					{
						$ex_trn_data=explode("!!!!",$trns_data_key);
						/*echo "<pre>";
						print_r($trns_data_key);*/
						$to_order_id=$ex_trn_data[0];
						// $trans_qty_in=$ex_trn_data[1];
						$trans_qty_in=$row2['trans_qty_in'];
						$booking_no="";$program_date="";$ylot="";$y_count="";$y_count_id="";
						$from_order_id = $ex_trn_data[1];
						$to_order_job_no = $ex_trn_data[2];
						$to_order_po_no = $ex_trn_data[3];
						$to_order_buyer_id = $ex_trn_data[4];
						$trans_internel_ref = $ex_trn_data[5];
						//$min_transfer_date = $ex_trn_data[6];
						$min_transfer_date = $row2['transfer_date'];

						$date2=date_create($min_transfer_date);
						$date3=date_create();
						$diff=date_diff($date2,$date3);
						$age = $diff->format("%a");

						$booking_no= $trns_data_arr[$prog_no]['booking_no'];
						$program_date = $trns_data_arr[$prog_no]['program_date'];

						$ylot=$yarn_lot_arr[$prog_no]['lot'];
						$ylot = implode(",",array_filter(array_unique(explode(",",chop($ylot)))));
						$y_count=$yarn_lot_arr[$prog_no]['ycount'];
						$y_count_id=array_unique(explode(',',$y_count));
						$yarn_count_value='';
						foreach($y_count_id as $val)
						{
							if($val>0)
							{
								if($yarn_count_value=='') $yarn_count_value=$yarn_count_library[$val]; else $yarn_count_value.=", ".$yarn_count_library[$val];
							}
						}

						$roll_no='';
						if ($from_order_id=='from_order') 
						{
							//echo "string";
							$roll_no=$row2['count'];
						}
						else
						{
							
							$from_order_id_exp=explode(",", $from_order_id);
							foreach ($from_order_id_exp as $rows_from_orderID) 
							{
								$roll_data=explode(",",$yarn_lot_arr[$rows_from_orderID]['roll']);
								foreach($roll_data as $val)
								{
									$val=explode("**",$val);
									if(!in_array($val[0],$roll_arr))
									{
										$roll_no+=$val[1];
										$roll_arr[]=$val[0];
									}
								}
							}
						}


						$color_id=$trns_data_arr[$prog_no]['color_id'];
						$color=array_filter(explode(',',$color_id));
						$color_id_value='';
						foreach($color as $cval)
						{
							if($cval>0)
							{
								if($color_id_value=='') $color_id_value=$color_library[$cval]; else $color_id_value.=", ".$color_library[$cval];
							}
						}

						$yarn_prod_id = chop($yarn_lot_arr[$prog_no]['yarn_prod_id'],",");
						$yarn_prod_id_arr=array_unique(explode(',',$yarn_prod_id));

						$brand_supplier_arr=array();
						foreach ($yarn_prod_id_arr as $val)
						{
							$brand_supplier_arr[$supplier_yarn[$val]] = $supplier_yarn[$val];
						}
						$brand_supplier = implode(",", $brand_supplier_arr);

						$booking_qty=$booking_arr[$trns_data_arr[$prog_no]['booking_no']]['grey_fab_qnty'];
						$transfer_out_qnty = $transfer_qty_arr[$prog_no][$to_order_id]['transfer_out'];
						$remark =  $knit_issue_arr[$prog_no][$to_order_id]['remarks'];
						$knit_issue_qty = $knit_issue_arr[$prog_no][$to_order_id]['qnty'];

						$issue_return_qnty = $issue_return_arr[$prog_no][$to_order_id]['qnty'];

						$totalTransIssueReturn = $trans_qty_in + $issue_return_qnty;

						$trans_balance = ($totalTransIssueReturn) - ($knit_issue_qty+$transfer_out_qnty);

						if(number_format($trans_balance,2,".","") == "-0.00")
						{
							$trans_balance = "0.00";
						}
						// echo $totalTransIssueReturn.'-'.$knit_issue_qty.'+'.$transfer_out_qnty.'==<br>';
						//1451.100000000000363797880709171295166015625-1451.09999999999990905052982270717620849609375+==
						//echo $trans_balance.'==<br>';//4.5474735088646411895751953125E-13==
						if ((($get_upto_qnty == 1 && $trans_balance > $txt_qnty) || ($get_upto_qnty == 2 && $trans_balance < $txt_qnty) || ($get_upto_qnty == 3 && $trans_balance >= $txt_qnty) || ($get_upto_qnty == 4 && $trans_balance <= $txt_qnty) || ($get_upto_qnty == 5 && $trans_balance == $txt_qnty) || $get_upto_qnty == 0) && ($cbo_value_with==0 || ($cbo_value_with ==1 && $trans_balance>0 )))
						{

							//if($trns_data_arr[$prog_no]['program_qnty'] > 0)
							//{
								if($transfer_part_check_arr[$prog_no][$to_order_buyer_id][$to_order_job_no][$trans_internel_ref][$to_order_po_no][$trns_data_arr[$prog_no]['mc_dia_gg']][$trns_data_arr[$prog_no]['fabric_desc']][$color_id_value]!=1)
								{

									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="40"><?echo $i?></td>
										<td width="80"><p><? echo change_date_format($program_date); ?></p></td>
										<td width="100"><p><? echo $buyer_library[$to_order_buyer_id];//$buyer_library[$trns_data_arr[$prog_no]['buyer_name']]; ?></p></td>
										<td width="70"><p><? echo $to_order_job_no;//$trns_data_arr[$prog_no]['job_no']//$po_lib_arr[$to_order_id]['job_no']; ?></p></td>
										<td width="70"><p><? echo $trans_internel_ref; ?></p></td>
										<td width="110"><p><? echo $to_order_po_no;//$trns_data_arr[$prog_no]['po_number'];//$po_lib_arr[$to_order_id]['po_number']; ?></p></td>
										<td width="100"><p><? echo $buyer_library[$to_order_client_arr[$to_order_id]]; ?></p></td>
										<td width="80"><p><? echo $trns_data_arr[$prog_no]['mc_dia_gg']; ?></p></td>
										<td width="70"><p><? echo $yarn_count_value; ?></p></td>
										<td width="70" align="center" title="<? echo $from_order_id;?>"><p><? echo $ylot; ?></p></td>
										<td width="100" align="center" ><p><? echo $brand_supplier; ?></p></td>
										<td width="150" align="center"><p><? echo $trns_data_arr[$prog_no]['fabric_desc']; ?></p></td>
										<td width="100"><p><? echo $color_id_value; ?></p></td>
										<td width="60" align="center"><p><? echo $trns_data_arr[$prog_no]['gsm_weight']; ?></p></td>
										<td width="70" align="center"><p><? echo $trns_data_arr[$prog_no]['stitch_length'];  ?></p></td>
										<td width="100"><p><? echo $booking_no ?></p></td>
										<td width="80" align="right"><p><? //echo number_format($booking_qty,2); ?></p></td>
										<td width="70" align="center"><p><? echo $prog_no ."- (T)" ?></p></td>
										<td width="80" align="right"><p><? //echo number_format($trns_data_arr[$prog_no]['program_qnty'],2); ?></p></td>

										<td width="100" align="right"><p></p></td>

										<td align="center" width="60"><p><? echo $roll_no; ?></p></td>
										<td align="right" width="80"></td>
										<td width="80" align="right"><p><a href="##" onClick="openmypage_transfer_in('<? echo $to_order_id; ?>','<? echo $prog_no; ?>','<? echo  $booking_no;?>','','transfer_in_popup');" ><? echo number_format($trans_qty_in,2); ?></a></p></td>

										<td width="80" align="right"><p><a href="##" onClick="openmypage_issue_return('<? echo $to_order_id; ?>','<? echo $prog_no; ?>','<? echo  $booking_no;?>','issue_return_popup');" ><? echo number_format($issue_return_qnty,2); ?></a></p></td>

										<td width="80" align="right"><p><?  echo number_format($totalTransIssueReturn,2); ?></p></td>
										<td align="right" width="80" title="<? echo $prog_no.'='.$to_order_id; ?>"><a href="##" onClick="openmypage_issue('<? echo $to_order_id; ?>','<? echo $prog_no; ?>','<? echo  $booking_no;?>','trans_issue_grey_popup');" ><? echo number_format($knit_issue_qty,2); ?></a></td>
										<td width="80" align="right"><p><a href="##" onClick="openmypage_transfer_out('<? echo $to_order_id; ?>','<? echo $prog_no; ?>','<? echo  $booking_no;?>','','transfer_out_popup');" ><? echo number_format($transfer_out_qnty,2); ?></a></p></td>
										<td width="80" align="right"><p><?  echo number_format($knit_issue_qty+$transfer_out_qnty,2); ?></p></td>
										<td align="right" width="80">
											<a href='#report_details' onClick="openmypage_issue('<? echo $to_order_id; ?>','<? echo $prog_no; ?>','<? echo  $booking_no;?>','trans_stock_grey_popup');">
												<?  echo number_format($trans_balance,2); ?>
											</a>
										</td>
										<td width="80" align="right"><? echo $age;?></td>
										<td width="100"><? echo $remark; ?></td>
									</tr>
									<?
									$total_trans_qty_in+=$trans_qty_in;
									$total_Recv_qty+=$totalTransIssueReturn;
									$total_iss_return_qty +=$issue_return_qnty;
									$total_knit_issue_qty+=$knit_issue_qty;
									$total_trans_qty_out+=$transfer_out_qnty;
									$total_Issue_qty+=$knit_issue_qty+$transfer_out_qnty;
									$total_stockbalance+=$trans_balance;
									$total_age+=$age;
									$i++;
								}
							//}
						
						}
					}
				}
				?>
			</table>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2580" class="rpt_table">
				<tfoot>
					<th width="40"></th>
					<th width="80"></th>
					<th width="100"></th>
					<th width="70"></th>
					<th width="70"></th>
					<th width="110"></th>
					<th width="100"></th>
					<th width="80"></th>
					<th width="70"></th>
					<th width="70"></th>
					<th width="100"></th>
					<th width="150"></th>
					<th width="100"></th>
					<th width="60"></th>
					<th width="70"></th>
					<th width="100"></th>
					<th width="80" id="value_total_booking_qty"><? echo number_format($total_booking_qty,2,'.',''); ?></th>
					<th width="70"></th>
					<th width="80" id="value_total_program_qnty"><? echo number_format($total_program_qnty,2,'.',''); ?></th>

					<th width="100" title="Batch No"></th>

					<th width="60"></th>
					<th width="80" id="value_total_grey_qty"><? echo number_format($total_knitting_recv_qnty,2,'.','');?></th>
					<th width="80" id="value_total_trans_qty_in"><? echo number_format($total_trans_qty_in,2,'.',''); ?></th>
					<th width="80" id="value_total_iss_return_qty"><? echo number_format($total_iss_return_qty,2,'.',''); ?></th>
					<th width="80" id="value_total_Recv_qty"><? echo number_format($total_Recv_qty,2,'.',''); ?></th>
					<th width="80" id="value_total_knit_issue_qty"><? echo number_format($total_knit_issue_qty,2,'.',''); ?></th>
					<th width="80" id="value_total_trans_qty_out"><? echo number_format($total_trans_qty_out,2,'.',''); ?></th>
					<th width="80" id="value_total_Issue_qty"><? echo number_format($total_Issue_qty,2,'.',''); ?></th>
					<th width="80" id="value_total_stockbalance"><? echo number_format($total_stockbalance,2,'.',''); ?></th>
					<th width="80" id=""><? //echo number_format($total_age,2,'.',''); ?></th>
					<th width="100"></th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}

if($action=="receive_grey_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $po_id;
	if ($cbo_store_name>0) 
	{
		$store_cond = " and a.store_id = $cbo_store_name";
		$store_cond2 = " and b.store_id = $cbo_store_name";
	}
	$buyer_library=return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(9,20) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id","supplier_name");
	?>
	<script>
		function print_window()
		{
			$(".flt").css("display","none");
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			$(".flt").css("display","block");
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="380px";
		}
		var tableFilters =
		{
			col_14: "none",
			col_operation: {
				id: ["value_total_balance"],
				col: [6],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}
	</script>
	<fieldset style="width:1200px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div id="report_container" style="width:100%">
			<table border="1" class="rpt_table" rules="all" width="1180" cellpadding="0" cellspacing="0" id="table_header">
				<caption>
					<b>Knit Grey Fabrics Received Info</b>
				</caption>
				<thead>
					<th width="30">Sl</th>
					<th width="80">Knitting Company</th>
					<th width="80">Receive Date</th>
					<th width="70">Prog. No</th>
					<th width="120">Receive ID</th>
					<th width="120">Receive Mc/F.Dia</th>
					<th width="80">Receive Ch. No</th>
					<th width="80">Receive Qty</th>
					<th width="80" width="">Roll</th>
					<th width="80" width="">Floor</th>
					<th width="80" width="">Room</th>
					<th width="80" width="">Rack</th>
					<th width="80" width="">Shelf</th>
					<th>Remarks</th>
				</thead>
			</table>
			<div style="width:1200px; overflow-y:scroll; max-height:250px;font-size:12px;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="1180" cellpadding="0" cellspacing="0" id="table_body">

					<?
					if($knit_source == 1)
					{
						$sql_22 = "select a.recv_number as booking_no,a.id
						from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c
						where a.id=b.mst_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=2 and c.entry_form=2 and a.receive_basis=2
						and b.status_active=1 and b.is_deleted=0 and a.booking_id = '$prog_no' and b.trans_id = 0 and a.company_id = $companyID
						and c.po_breakdown_id in ($po_id) $store_cond";
					}
					else
					{
						$sql_22 = "
						SELECT a.recv_number as booking_no,a.id
						from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c
						where a.id=b.mst_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=2 and c.entry_form=2 and a.receive_basis=2
						and b.status_active=1 and b.is_deleted=0 and a.booking_id = '$prog_no' and b.trans_id = 0 and a.company_id = $companyID
						and c.po_breakdown_id in ($po_id) $store_cond
						union all
						select b.booking_no, b.id
						from wo_booking_dtls a, wo_booking_mst b
						where a.booking_no = b.booking_no and a.program_no = '$prog_no' and a.booking_type = 3
						and a.process = 1 and b.item_category = 12
						and a.status_active = 1 and a.is_deleted = 0
						and b.status_active = 1 and b.is_deleted = 0";
					}
					$booking_id = "";
					$result_22 = sql_select($sql_22);
					foreach($result_22 as $row_22)
					{
						$booking_id .= $row_22[csf('id')].",";
					}
                   //for entry form 22
                   //echo $sql_22."**";die;
					$booking_id =  chop($booking_id,',');
					if($booking_id != "")
					{
						$sql_extend = " union all
						select a.knitting_source,a.knitting_company,a.recv_number,a.receive_date,sum(c.quantity) as qnty , a.challan_no, b.no_of_roll as roll , b.machine_dia, b.width, a.remarks, b.floor_id, b.room, b.rack, b.self
						from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c
						where a.id=b.mst_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=22 and c.entry_form=22 and a.receive_basis in (9,11)
						and b.status_active=1 and b.is_deleted=0 and a.company_id = $companyID
						and a.booking_id in ($booking_id) and c.po_breakdown_id in ($po_id) $store_cond
						group by a.recv_number,a.receive_date ,a.challan_no , b.no_of_roll,a.knitting_source,a.knitting_company , b.machine_dia, b.width, a.remarks, b.floor_id, b.room, b.rack, b.self ";
					}

					$sql="SELECT b.knitting_source, b.knitting_company, b.recv_number, b.receive_date, sum(a.qnty) qnty , b.challan_no, sum(a.roll_no) as roll, $mc_dia as machine_dia, c.width, b.remarks, c.floor_id, c.room, c.rack, c.self
					from pro_roll_details a,inv_receive_master b, pro_grey_prod_entry_dtls c
					where a.entry_form = 58 and a.mst_id = b.id and b.id = c.mst_id and a.dtls_id = c.id
					and a.booking_no = '$prog_no' and a.po_breakdown_id in ($po_id)
					and a.status_active = 1 and a.is_deleted = 0 and b.company_id = $companyID $store_cond2
					group by b.recv_number,b.receive_date , b.challan_no,b.knitting_source,b.knitting_company , c.machine_dia, c.width, b.remarks, c.floor_id, c.room, c.rack, c.self
					union all
					select a.knitting_source,a.knitting_company,a.recv_number, a.receive_date, sum(c.quantity) as qnty , a.challan_no, b.no_of_roll as roll , b.machine_dia, b.width, a.remarks, b.floor_id, b.room, b.rack, b.self
					from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c
					where a.id=b.mst_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=2 and c.entry_form=2 and a.receive_basis=2
					and b.status_active=1 and b.is_deleted=0 and a.booking_id = '$prog_no' and c.po_breakdown_id in ($po_id) and b.trans_id <> 0  and a.company_id = $companyID $store_cond
					group by a.recv_number,a.receive_date , a.challan_no, b.no_of_roll,a.knitting_source,a.knitting_company , b.machine_dia, b.width, a.remarks, b.floor_id, b.room, b.rack, b.self
					$sql_extend ";

                    //echo $sql;
					$result = sql_select($sql);
					$floorIdArr = array();
					$roomIdArr = array();
					$rackIdArr = array();
					$shelfIdArr = array();
					foreach($result as $row)
					{
						$floorIdArr[$row[csf('floor_id')]] = $row[csf('floor_id')];
						$roomIdArr[$row[csf('room')]] = $row[csf('room')];
						$rackIdArr[$row[csf('rack')]] = $row[csf('rack')];
						$shelfIdArr[$row[csf('self')]] = $row[csf('self')];
					}
					
					$companyId = $companyID;
					//floorSql
					$floorSql = "
						SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
						FROM lib_floor_room_rack_mst a
						INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.floor_id
						WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyId.") AND a.floor_room_rack_id IN(".implode(',', $floorIdArr).")
					";
					$floorDetails = return_library_array( $floorSql, 'floor_room_rack_id', 'floor_room_rack_name');
					
					//roomSql
					$roomSql = "
						SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
						FROM lib_floor_room_rack_mst a
						INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.room_id
						WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyId.") AND a.floor_room_rack_id IN(".implode(',', $roomIdArr).")
					";
					$roomDetails = return_library_array( $roomSql, 'floor_room_rack_id', 'floor_room_rack_name');
					
					//rackSql
					$rackSql = "
						SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id, b.serial_no
						FROM lib_floor_room_rack_mst a
						INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.rack_id
						WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyId.") AND a.floor_room_rack_id IN(".implode(',', $rackIdArr).")
					";
					$rackDetails = return_library_array( $rackSql, 'floor_room_rack_id', 'floor_room_rack_name');

					//selfSql
					$shelfSql = "
						SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
						FROM lib_floor_room_rack_mst a
						INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.shelf_id
						WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyId.") AND a.floor_room_rack_id IN(".implode(',', $shelfIdArr).")
					";
					$shelfDetails = return_library_array( $shelfSql, 'floor_room_rack_id', 'floor_room_rack_name');
					?>
					<tbody>
						<?
						$i = 1;
						foreach($result as $row)
						{

							?>
							<tr>
								<td width="30"><? echo $i;?></td>
								<? if($row[csf("knitting_source")] == 1)
								{
									$knitting_company = $company_library[$row[csf("knitting_company")]];
								}
								else
								{
									$knitting_company = $buyer_library[$row[csf("knitting_company")]];
								}?>
								<td width="80" title="<? echo $row[csf("knitting_company")];?>"><? echo $knitting_company;?></td>
								<td width="80"><? echo $row[csf("receive_date")];?></td>
								<td width="70"><? echo $prog_no;?></td>
								<td width="120"><? echo $row[csf("recv_number")];?></td>
								<td width="120"><? echo $row[csf('machine_dia')].' / '.$row[csf('width')];?></td>
								<td width="80"><? echo $row[csf("challan_no")];?></td>
								<td width="80" align="right"><? echo number_format($row[csf("qnty")],2);?></td>
								<td width="80" align="center"><? echo $row[csf("roll")];?></td>
								<td width="80" align="center"><? echo $floorDetails[$row[csf("floor_id")]];?></td>
								<td width="80" align="center"><? echo $roomDetails[$row[csf("room")]];?></td>
								<td width="80" align="center"><? echo $rackDetails[$row[csf("rack")]];?></td>
								<td width="80" align="center"><? echo $shelfDetails[$row[csf("self")]];?></td>
								<td width="" align="right"><? echo $row[csf("remarks")];?></td>
							</tr>
							<?
							$i++;
							$total_balance += $row[csf("qnty")];
						}
						?>
					</tbody>
					<tfoot>
						<th width="530" colspan="7">Total:</th>
						<th width="80" align="right"><? echo number_format($total_balance,2); ?></th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
					</tfoot>

				</table>
			</div>

		</div>
	</fieldset>
	<script>setFilterGrid('table_body',-1,tableFilters);</script>
	<?
	exit();
}

if($action=="receive_grey_popup_26092020")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $po_id;
	$buyer_library=return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(9,20) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id","supplier_name");
	?>
	<script>
		function print_window()
		{
			$(".flt").css("display","none");
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			$(".flt").css("display","block");
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="380px";
		}
		var tableFilters =
		{
			col_10: "none",
			col_operation: {
				id: ["value_total_balance"],
				col: [6],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}
	</script>
	<fieldset style="width:880px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div id="report_container" style="width:100%">
			<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0" id="table_header">
				<caption>
					<b>Knit Grey Fabrics Received Info</b>
				</caption>
				<thead>
					<th width="30">Sl</th>
					<th width="80">Knitting Company</th>
					<th width="80">Receive Date</th>
					<th width="70">Prog. No</th>
					<th width="120">Receive ID</th>
					<th width="120">Receive Mc/F.Dia</th>
					<th width="80">Receive Ch. No</th>
					<th width="80">Receive Qty</th>
					<th width="80" width="">Roll</th>
					<th>Remarks</th>
				</thead>
			</table>
			<div style="width:880px; overflow-y:scroll; max-height:250px;font-size:12px;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0" id="table_body">

					<?
					if($knit_source == 1)
					{
						$sql_22 = "select a.recv_number as booking_no,a.id
						from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c
						where a.id=b.mst_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=2 and c.entry_form=2 and a.receive_basis=2
						and b.status_active=1 and b.is_deleted=0 and a.booking_id = '$prog_no' and b.trans_id = 0 and a.company_id = $companyID
						and c.po_breakdown_id in ($po_id) ";
					}
					else
					{
						$sql_22 = "
						SELECT a.recv_number as booking_no,a.id
						from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c
						where a.id=b.mst_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=2 and c.entry_form=2 and a.receive_basis=2
						and b.status_active=1 and b.is_deleted=0 and a.booking_id = '$prog_no' and b.trans_id = 0 and a.company_id = $companyID
						and c.po_breakdown_id in ($po_id)
						union all
						select b.booking_no, b.id
						from wo_booking_dtls a, wo_booking_mst b
						where a.booking_no = b.booking_no and a.program_no = '$prog_no' and a.booking_type = 3
						and a.process = 1 and b.item_category = 12
						and a.status_active = 1 and a.is_deleted = 0
						and b.status_active = 1 and b.is_deleted = 0";
					}
					$booking_id = "";
					$result_22 = sql_select($sql_22);
					foreach($result_22 as $row_22)
					{
						$booking_id .= $row_22[csf('id')].",";
					}
                   //for entry form 22
                   //echo $sql_22."**";die;
					$booking_id =  chop($booking_id,',');
					if($booking_id != "")
					{
						$sql_extend = " union all
						select a.knitting_source,a.knitting_company,a.recv_number,a.receive_date,sum(c.quantity) as qnty , a.challan_no, b.no_of_roll as roll , b.machine_dia, b.width, a.remarks
						from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c
						where a.id=b.mst_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=22 and c.entry_form=22 and a.receive_basis in (9,11)
						and b.status_active=1 and b.is_deleted=0 and a.company_id = $companyID
						and a.booking_id in ($booking_id) and c.po_breakdown_id in ($po_id)
						group by a.recv_number,a.receive_date ,a.challan_no , b.no_of_roll,a.knitting_source,a.knitting_company , b.machine_dia, b.width, a.remarks ";
					}

					$sql="SELECT b.knitting_source,b.knitting_company,b.recv_number,b.receive_date, sum(a.qnty) qnty , b.challan_no,sum(a.roll_no) as roll , $mc_dia as machine_dia, c.width, b.remarks
					from pro_roll_details a,inv_receive_master b, pro_grey_prod_entry_dtls c
					where a.entry_form = 58 and a.mst_id = b.id and b.id = c.mst_id and a.dtls_id = c.id
					and a.booking_no = '$prog_no' and a.po_breakdown_id in ($po_id)
					and a.status_active = 1 and a.is_deleted = 0 and b.company_id = $companyID
					group by b.recv_number,b.receive_date , b.challan_no,b.knitting_source,b.knitting_company , c.machine_dia, c.width, b.remarks
					union all
					select a.knitting_source,a.knitting_company,a.recv_number, a.receive_date, sum(c.quantity) as qnty , a.challan_no, b.no_of_roll as roll , b.machine_dia, b.width, a.remarks
					from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c
					where a.id=b.mst_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=2 and c.entry_form=2 and a.receive_basis=2
					and b.status_active=1 and b.is_deleted=0 and a.booking_id = '$prog_no' and c.po_breakdown_id in ($po_id) and b.trans_id <> 0  and a.company_id = $companyID
					group by a.recv_number,a.receive_date , a.challan_no, b.no_of_roll,a.knitting_source,a.knitting_company , b.machine_dia, b.width, a.remarks
					$sql_extend ";


                    // echo $sql;
					$result = sql_select($sql);
					?>

					<tbody>
						<?
						$i = 1;
						foreach($result as $row)
						{

							?>
							<tr>
								<td width="30"><? echo $i;?></td>
								<? if($row[csf("knitting_source")] == 1){
									$knitting_company = $company_library[$row[csf("knitting_company")]];
								}else{
									$knitting_company = $buyer_library[$row[csf("knitting_company")]];
								}?>
								<td width="80" title="<? echo $row[csf("knitting_company")];?>"><? echo $knitting_company;?></td>
								<td width="80"><? echo $row[csf("receive_date")];?></td>
								<td width="70"><? echo $prog_no;?></td>
								<td width="120"><? echo $row[csf("recv_number")];?></td>
								<td width="120"><? echo $row[csf('machine_dia')].' / '.$row[csf('width')];?></td>
								<td width="80"><? echo $row[csf("challan_no")];?></td>
								<td width="80" align="right"><? echo number_format($row[csf("qnty")],2);?></td>
								<td width="80" align="center"><? echo $row[csf("roll")];?></td>
								<td width="" align="right"><? echo $row[csf("remarks")];?></td>
							</tr>
							<?
							$i++;
							$total_balance += $row[csf("qnty")];
						}
						?>
					</tbody>
					<tfoot>
						<th width="530" colspan="7">Total:</th>
						<th width="80" align="right"><? echo number_format($total_balance,2); ?></th>
						<th >&nbsp;</th>
						<th >&nbsp;</th>
					</tfoot>

				</table>
			</div>

		</div>
	</fieldset>
	<script>setFilterGrid('table_body',-1,tableFilters);</script>
	<?
	exit();
}
if($action=="issue_grey_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $po_id;
	if ($cbo_store_name>0) 
	{
		$store_cond = " and b.store_name = $cbo_store_name";
	}
	$buyer_library=return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and b.supplier_id=c.supplier_id and a.status_active=1  group by a.id,a.supplier_name order by a.supplier_name","id","supplier_name");

	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body></html>');
			d.close();
		}
		var tableFilters =
		{
			col_9: "none",
			col_operation: {
				id: ["value_tot_qty"],
				col: [6],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}

	</script>
	<fieldset style="width:638px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div style="width:620px" id="report_container">
			<table border="1" class="rpt_table" rules="all" width="620" cellpadding="0" cellspacing="0" id="table_header">
				<caption>
					<b>Knit Grey Fabrics Issued Info</b>
				</caption>
				<thead>
					<th width="30">Sl</th>
					<th width="80">Issue Date</th>
					<th width="70">Dyeing Company</th>
					<th width="70">Prog. No</th>
					<th width="100">Issue ID</th>
					<th width="50">Issue Ch. No</th>
					<th width="80">Delivery Qty</th>
					<!--                    <th width="80">Trans Out</th>-->
					<th width="50">Roll</th>
					<th >Rack No</th>
				</thead>
			</table>
			<div style="width:638px; overflow-y:scroll; max-height:250px;font-size:12px;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="620" cellpadding="0" cellspacing="0" id="table_body">
					<tbody>
						<?
						//rackSql
						$rackSql = "
							SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id, b.serial_no
							FROM lib_floor_room_rack_mst a
							INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.rack_id
							WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyID.")";
						$rackDetails = return_library_array( $rackSql, 'floor_room_rack_id', 'floor_room_rack_name');
						$i=1;
						$sql_data=("select b.from_program,b.to_program, b.transfer_qnty as transfer_qnty,a.to_order_id from inv_item_transfer_dtls b,inv_item_transfer_mst a,order_wise_pro_details c where a.id=b.mst_id  and c.dtls_id=b.id and c.trans_type=6 and a.status_active=1 and a.is_deleted=0 and a.item_category=13 and a.transfer_criteria=4 and  b.from_program='$prog_no' and b.to_program='$prog_no' and a.to_order_id in($po_id) and b.from_program>0 and b.to_program>0 ");
						$data_array=sql_select($sql_data);

						$transfer_qty_arr=array();
						foreach($data_array as $row_b)
						{
						//$transfer_qty_arr[$row_b[csf('from_program')]][$row_b[csf('po_breakdown_id')]]['from_qnty']=$row_b[csf('transfer_qnty')];
							$transfer_qty_arr[$row_b[csf('to_program')]]['to_order_id']=$row_b[csf('transfer_qnty')];
						}

						$production_data = sql_select("select a.id, a.recv_number , b.barcode_no, a.booking_no, a.roll_maintained
							from inv_receive_master a, pro_roll_details b
							where a.id = b.mst_id and b.po_breakdown_id in($po_id) and b.entry_form = 2 and a.receive_basis = 2 and b.status_active = 1 and b.is_deleted = 0 and a.booking_no = '$prog_no' and b.is_sales <>1");
						foreach ($production_data as $val)
						{
							$productionBarcode[$val[csf('barcode_no')]] = $val[csf('barcode_no')];
							$roll_maintained = $val[csf('roll_maintained')];

						}


						$all_productionBarcode = implode(",", array_filter(array_unique($productionBarcode)));
						if($all_productionBarcode=="") $all_productionBarcode=0;
						$barcodeCond = $all_Barcode_cond = "";
						$all_productionBarcode_arr=explode(",",$all_productionBarcode);
						if($db_type==2 && count($all_productionBarcode_arr)>999)
						{
							$all_productionBarcode_chunk=array_chunk($all_productionBarcode_arr,999) ;
							foreach($all_productionBarcode_chunk as $chunk_arr)
							{
								$chunk_arr_value=implode(",",$chunk_arr);
								$barcodeCond.=" d.barcode_no in($chunk_arr_value) or ";
							}

							$all_Barcode_cond.=" and (".chop($barcodeCond,'or ').")";

						}
						else
						{
							$all_Barcode_cond=" and d.barcode_no in($all_productionBarcode)";
						}

						$mrr_sql="SELECT a.knit_dye_source,a.knit_dye_company,a.issue_number,a.order_id,a.issue_date,a.challan_no,b.no_of_roll,b.rack,b.program_no, sum(c.quantity) as knitting_issue_qnty from inv_issue_master a,inv_grey_fabric_issue_dtls b ,order_wise_pro_details c where a.id=b.mst_id and b.id =  c.dtls_id and a.item_category=13 and b.program_no='$prog_no' and c.po_breakdown_id in($po_id) and a.entry_form in (16) and c.entry_form in (16) and  a.issue_basis=3 and b.status_active=1 and b.is_deleted=0  $store_cond group by  b.program_no,b.issue_qnty,a.issue_number,a.issue_date,a.challan_no,b.no_of_roll,b.rack,a.order_id,a.knit_dye_source,a.knit_dye_company

						union all

						SELECT a.knit_dye_source,a.knit_dye_company,a.issue_number,a.order_id,a.issue_date,a.challan_no,b.no_of_roll,b.rack,b.program_no, sum(d.qnty) as knitting_issue_qnty from inv_issue_master a,inv_grey_fabric_issue_dtls b , pro_roll_details d where a.id=b.mst_id  and a.id = d.mst_id and b.id = d.dtls_id and a.entry_form =61 and d.entry_form = 61 and b.status_active=1 and b.is_deleted=0  and d.booking_no='$prog_no' and d.po_breakdown_id in($po_id)  $store_cond group by b.program_no, b.issue_qnty,a.issue_number,a.issue_date, a.challan_no, b.no_of_roll,b.rack,a.order_id, a.knit_dye_source, a.knit_dye_company";
						 //echo $mrr_sql; //$all_Barcode_cond
						$dtlsArray=sql_select($mrr_sql);



						foreach($dtlsArray as $row)
						{
							if ($i%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							$trans_out=$transfer_qty_arr[$row[csf('program_no')]][$row[csf('order_id')]]['to_qnty'];
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><p><? echo $i; ?>&nbsp;</p></td>
								<td width="80"><p><? echo change_date_format($row[csf('issue_date')]); ?>&nbsp;</p></td>
								<?  if($row[csf('knit_dye_source')] == 1)
								{
									$dye_company = $company_library[$row[csf('knit_dye_company')]];
								}else{
									$dye_company = $buyer_library[$row[csf('knit_dye_company')]];
								}
								?>
								<td width="70"><p><? echo $dye_company; ?>&nbsp;</p></td>

								<td width="70"><p><? echo $prog_no; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $row[csf('issue_number')]; ?>&nbsp;</p></td>
								<td width="50"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
								<td width="80" align="right"><p><? echo number_format($row[csf('knitting_issue_qnty')],2); ?></p></td>
								<!--                            <td width="80" align="right"><p><? //echo number_format($trans_out,2); ?></p></td>-->
								<td width="50"><p><? echo $row[csf('no_of_roll')]; ?>&nbsp;</p></td>
								<td ><p><? echo $rackDetails[$row[csf('rack')]]; ?>&nbsp;</p></td>
							</tr>
							<?
							$tot_qty+=$row[csf('knitting_issue_qnty')];
							$tot_trans_qty+=$trans_out;
							$i++;
						}
						?>
					</tbody>
				</table>
			</div>
			<table border="1" class="rpt_table" rules="all" width="620" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
					<th width="30">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="70">&nbsp;</th>
					<th width="70">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="50">Total:</th>
					<th width="80" id="value_tot_qty"><? echo number_format($tot_qty,2); ?></th>
					<!--                     <th width="80" id="value_tot_trans_qty"><? //echo number_format($tot_trans_qty,2); ?></th>-->
					<th width="50">&nbsp;</th>
					<th >&nbsp;</th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<script>setFilterGrid('table_body',-1,tableFilters);</script>
	<?
	exit();
}

if($action=="stock_grey_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $po_id;
	if ($cbo_store_name>0) 
	{
		$store_cond = " and b.from_store = $cbo_store_name";
		$store_iss_cond = " and b.store_name = $cbo_store_name";
		$store_iss_cond2 = " and c.store_name = $cbo_store_name";
		$store_rcv_cond = " and b.store_id = $cbo_store_name";
		$store_rcv_cond2 = " and a.store_id = $cbo_store_name";
		$to_store_cond = " and b.to_store = $cbo_store_name";
	}
	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$('#table_body tbody tr:first').hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			$('#table_body tbody tr:first').show();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="250px";
		}
		var tableFilters =
		{
			col_10: "none",
			col_operation: {
				id: ["value_total_recv","value_total_iss","value_total_stock"],
				col: [3,4,5],
				operation: ["sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML"]
			}
		}
	</script>
	<fieldset style="width:560px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div id="report_container" style="width:100%">
			<table border="1" class="rpt_table" rules="all" width="540" cellpadding="0" cellspacing="0" id="table_header">
				<caption>
					<b>Knit Grey Fabrics Stock Info</b>
				</caption>
				<thead>
					<th width="40">Sl</th>
					<th width="100">Rack No</th>
					<th width="100">Shelf No</th>
					<th width="100">Receive Qty</th>
					<th width="100">Issue Qty</th>
					<th>Stock Qty</th>
				</thead>
			</table>
			<div style="width:560px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidde;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="540" cellpadding="0" cellspacing="0" id="table_body">
					<tbody>
						<?
						$sql_data=("SELECT b.from_program,b.to_program,b.rack, b.shelf, b.transfer_qnty as transfer_qnty,a.from_order_id from inv_item_transfer_dtls b,inv_item_transfer_mst a,order_wise_pro_details c where a.id=b.mst_id  and c.dtls_id=b.id and c.trans_type=6 and a.status_active=1 and a.is_deleted=0 and a.item_category=13 and a.transfer_criteria in (4,6) and c.entry_form in (83,13,80) and  b.from_program='$prog_no'  and a.from_order_id in($po_id) and b.from_program>0 and b.to_program>0 $store_cond");
						$data_array=sql_select($sql_data);
						$transfer_qty_arr=array();
						foreach($data_array as $row_b)
						{
							$transfer_qty_arr[$row_b[csf('rack')]][$row_b[csf('shelf')]]['from_qnty']+=$row_b[csf('transfer_qnty')];
							$rack_shelf_arr[$row_b[csf('rack')]][$row_b[csf('shelf')]]=$row_b[csf('transfer_qnty')];
						}
						 //var_dump($transfer_qty_arr);
						$sql_result=("SELECT b.from_program,b.to_program,b.rack, b.shelf, b.transfer_qnty as transfer_qnty,a.to_order_id from inv_item_transfer_dtls b,inv_item_transfer_mst a,order_wise_pro_details c where a.id=b.mst_id  and c.dtls_id=b.id and c.trans_type=6 and a.status_active=1 and a.is_deleted=0 and a.item_category=13 and a.transfer_criteria=4 and b.to_program='$prog_no' and a.to_order_id in($po_id) and b.from_program>0 and b.to_program>0 $store_cond");
						$data_array_issue=sql_select($sql_result);

						$transfer_qty_issue_arr=array();
						foreach($data_array_issue as $row_b)
						{
							$transfer_qty_issue_arr[$row_b[csf('rack')]][$row_b[csf('shelf')]]['from_qnty']+=$row_b[csf('transfer_qnty')];
						//$transfer_qty_arr[$row_b[csf('rack')]]['shelf']+=$row_b[csf('transfer_qnty')];
						}

						$i=1;
						$iss_arr=array(); $recv_arr=array(); $rack_shelf_arr=array(); $recv_arr_trans=array();

						$iss_data=sql_select("SELECT b.rack, b.self, sum(c.quantity) as issue_qnty from inv_issue_master a, inv_grey_fabric_issue_dtls b ,order_wise_pro_details c where a.id=b.mst_id and b.id = c.dtls_id and a.company_id='$companyID' and a.item_category=13 and b.program_no='$prog_no' and c.po_breakdown_id in ($po_id) and a.entry_form in (16) and c.entry_form in (16) and a.issue_basis=3 and b.status_active=1 and b.is_deleted=0 $store_iss_cond group by b.rack,b.self");

						foreach($iss_data as $row)
						{
							$iss_arr[$row[csf('rack')]][$row[csf('self')]]=$row[csf('issue_qnty')];
							$rack_shelf_arr[$row[csf('rack')]][$row[csf('self')]]=$row[csf('issue_qnty')];
						}

						$recv_id='';

						$sql_prod="SELECT a.id, a.booking_id,b.order_id, b.rack, b.self, sum(b.grey_receive_qnty) as recv_qnty, max(b.trans_id) as trans_id,a.roll_maintained from inv_receive_master a, pro_grey_prod_entry_dtls b ,order_wise_pro_details c where a.id=b.mst_id and b.id = c.dtls_id and a.item_category=13 and a.entry_form=2 and a.company_id='$companyID' and a.booking_id='$prog_no' and a.receive_basis=2 and b.status_active=1 and b.is_deleted=0  and c.po_breakdown_id in ($po_id) $store_rcv_cond2 group by a.id,a.booking_id,b.order_id,b.rack,b.self,a.roll_maintained";

						$data_prod=sql_select($sql_prod);
						foreach($data_prod as $row)
						{
							if($row[csf('trans_id')]>0)
							{

								$recv_arr[$row[csf('rack')]][$row[csf('self')]]+=$row[csf('recv_qnty')];
								$rack_shelf_arr[$row[csf('rack')]][$row[csf('self')]]+=$row[csf('recv_qnty')];

							}
							else
							{
								if($recv_id=='') $recv_id= $row[csf('id')]; else $recv_id.=','.$row[csf('id')];
							}

						}
						//echo $recv_id;
						if($recv_id!="")
						{
							$sql_recv="SELECT b.rack, b.self, sum(c.quantity) as recv_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b  ,order_wise_pro_details c  where a.id=b.mst_id and b.id = c.dtls_id and a.item_category=13 and a.company_id='$companyID' and a.booking_id in($recv_id) and a.entry_form=22 and a.receive_basis=9 and c.po_breakdown_id in ($po_id) and b.status_active=1 and b.is_deleted=0 $store_rcv_cond2 group by b.rack, b.self";
							$data_recv=sql_select($sql_recv);
							foreach($data_recv as $row)
							{
								$recv_arr[$row[csf('rack')]][$row[csf('self')]]+=$row[csf('recv_qnty')];
								$rack_shelf_arr[$row[csf('rack')]][$row[csf('self')]]+=$row[csf('recv_qnty')];//just print for rack shelf
							}
						}

					// for roll receive start ----------------------------------------------

					$barcode_res = sql_select("select  a.barcode_no from pro_roll_details a, inv_receive_master b where a.entry_form = 2 and a.mst_id = b.id and a.booking_no = '$prog_no' and a.po_breakdown_id in ($po_id) and a.entry_form = 2 and a.status_active = 1 and a.is_deleted = 0 and b.company_id = $companyID");

					foreach ($barcode_res as $val)
					{
						$productionBarcode[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
					}

					$all_productionBarcode = implode(",", array_filter(array_unique($productionBarcode)));
					if($all_productionBarcode) 
					{
						$barcodeCond = $all_Barcode_cond = "";
						$all_productionBarcode_arr=explode(",",$all_productionBarcode);
						if($db_type==2 && count($all_productionBarcode_arr)>999)
						{
							$all_productionBarcode_chunk=array_chunk($all_productionBarcode_arr,999) ;
							foreach($all_productionBarcode_chunk as $chunk_arr)
							{
								$chunk_arr_value=implode(",",$chunk_arr);
								$barcodeCond.=" barcode_no in($chunk_arr_value) or ";
							}

							$all_Barcode_cond.=" and (".chop($barcodeCond,'or ').")";

						}
						else
						{
							$all_Barcode_cond=" and barcode_no in($all_productionBarcode)";
						}

						$rollWiseRcv =  sql_select("SELECT  a.qnty as recv_qnty, c.rack, c.self, a.id
							from pro_roll_details a, inv_receive_master b , pro_grey_prod_entry_dtls c
							where a.entry_form = 58 and a.mst_id = b.id and b.id = c.mst_id and c.id = a.dtls_id
							and a.entry_form = 58 and a.status_active = 1 and a.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and b.company_id = $companyID $all_Barcode_cond $store_rcv_cond");

						foreach ($rollWiseRcv as $val)
						{
							$rack_shelf_arr[$val[csf('rack')]][$val[csf('self')]]+=$val[csf('recv_qnty')];
							$recv_arr[$val[csf('rack')]][$val[csf('self')]]+=$val[csf('recv_qnty')];
						}

						// End roll receive  ----------------------------------------------

						// Roll Wise Issue Start -----
						$rollWiseIssue = sql_select("SELECT a.qnty as issue_qnty, c.rack, c.self 
						from  pro_roll_details a, inv_issue_master b,inv_grey_fabric_issue_dtls c  
						where a.mst_id = b.id and b.id = c.mst_id and a.dtls_id =c.id and a.entry_form=61 and b.entry_form = 61 and a.status_active=1 and a.is_deleted = 0  and a.booking_no='$prog_no' and a.po_breakdown_id in($po_id) $store_iss_cond2");// $all_Barcode_cond
						foreach ($rollWiseIssue as $val)
						{
							$iss_arr[$val[csf('rack')]][$val[csf('self')]]+=$val[csf('issue_qnty')];
							$rack_shelf_arr[$val[csf('rack')]][$val[csf('self')]]=$val[csf('issue_qnty')];
						}
						// End roll Issue  ----------------------------------------------

						// Roll wise Transfer Out Start--------------------------------------
						$rollWiseTransOut=("SELECT b.rack, b.shelf, 
						sum(case when c.trans_type in(6) then c.quantity else 0 end) as transfer_qnty
						from inv_item_transfer_dtls b,inv_item_transfer_mst a,order_wise_pro_details c, pro_roll_details d 
						where a.id=b.mst_id and c.dtls_id=b.id and a.id=d.mst_id and b.id=d.dtls_id and c.trans_type in(6) and a.status_active=1 and a.is_deleted=0 and a.item_category=13 and b.status_active=1 and b.is_deleted=0 
						and c.status_active=1 and c.is_deleted=0 and c.entry_form in(82,110) and a.entry_form in(82,110) and a.transfer_criteria in (1,2,4,6) and (c.is_sales is null or c.is_sales = 0 or c.is_sales = 2) $all_Barcode_cond $store_cond group by b.rack, b.shelf, c.entry_form");
						//echo $rollWiseTransOut;
						$data_array_issue=sql_select($rollWiseTransOut);
						//echo "<pre>";print_r($transfer_qty_arr);
						//$transfer_qty_arr=array();
						foreach($data_array_issue as $row_b)
						{
							$transfer_qty_arr[$row_b[csf('rack')]][$row_b[csf('shelf')]]['from_qnty']+=$row_b[csf('transfer_qnty')];
						}
						// Roll wise Transfer Out End--------------------------------------

						// Roll wise Issue Return Start--------------------------------------
						$roll_iss_rtn_sql="SELECT b.rack, b.self, d.qnty as quantity
						from inv_receive_master a, inv_transaction b, order_wise_pro_details c, pro_roll_details d
						where a.id=b.mst_id and b.id=c.trans_id and a.id=d.mst_id and b.transaction_type=4 and c.entry_form=84 and d.entry_form=84 and a.receive_basis=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.booking_no='$prog_no' and d.po_breakdown_id in($po_id) $store_rcv_cond";
						// echo $roll_iss_rtn_sql;
						$roll_iss_rtn_data=sql_select($roll_iss_rtn_sql);
						foreach($roll_iss_rtn_data as $row)
						{
							$iss_return_arr[$row[csf('rack')]][$row[csf('self')]]+=$row[csf('quantity')];
							$rack_shelf_arr[$row[csf('rack')]][$row[csf('self')]]=$row[csf('quantity')];
						}
						// Roll wise Issue Return End--------------------------------------

					}


					$issue_return_sql = sql_select("SELECT c.quantity, b.rack, b.self from inv_receive_master a, inv_transaction b, order_wise_pro_details c where a.id = b.mst_id and b.transaction_type = 4 and b.id = c.trans_id and c.entry_form=51 and a.receive_basis=3  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.company_id='$companyID' and a.booking_id = '$prog_no' and c.po_breakdown_id in ($po_id) $store_rcv_cond");

					foreach ($issue_return_sql as $val)
					{
						$iss_return_arr[$val[csf('rack')]][$val[csf('self')]]+=$val[csf('quantity')];
						$rack_shelf_arr[$val[csf('rack')]][$val[csf('self')]]=$val[csf('quantity')];
					}

					//rackSql
					$rackSql = "
						SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id, b.serial_no
						FROM lib_floor_room_rack_mst a
						INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.rack_id
						WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyID.")
					";
					$rackDetails = return_library_array( $rackSql, 'floor_room_rack_id', 'floor_room_rack_name');
					
					//selfSql
					$selfSql = "
						SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
						FROM lib_floor_room_rack_mst a
						INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.shelf_id
						WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyID.")
					";
					$selfDetails = return_library_array( $selfSql, 'floor_room_rack_id', 'floor_room_rack_name');	

					$i=1;
					foreach($rack_shelf_arr as $rack=>$data)
					{
						foreach($data as $shelf=>$qty)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							$recv_qty=$recv_arr[$rack][$shelf];
							$iss_qty=$iss_arr[$rack][$shelf];
							$iss_return = $iss_return_arr[$rack][$shelf];

							$trans_from_recv=$transfer_qty_arr[$rack][$shelf]['from_qnty'];
							$tran_to_issue=$transfer_qty_issue_arr[$rack][$shelf]['from_qnty'];
							$tot_recv=$recv_qty+$tran_to_issue + $iss_return;
							$tot_issue=$iss_qty+$trans_from_recv;
							$stock_qty=$tot_recv-$tot_issue;
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="40"><p><? echo $i; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $rackDetails[$rack]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $selfDetails[$shelf]; ?>&nbsp;</p></td>
								<td width="100" align="right" title="<? echo 'Recv='.$recv_qty.'+ Trans In='.$tran_to_issue.'+ Issue Rtn='.$iss_return; ?>"><? echo number_format($tot_recv,2); ?></td>
								<td width="100" align="right" title="<? echo $iss_qty.'__'.$trans_from_recv;?>"><? echo number_format($tot_issue,2); ?></td>
								<td align="right"><? echo number_format($stock_qty,2); ?></td>
							</tr>
							<?
							$i++;
							$total_recv_qty=$tot_recv;
							$total_iss_qty=$tot_issue;
							$total_stock_qty=$stock_qty;
						}
					}

					?>
				</tbody>
			</table>
		</div>
		<table border="1" class="rpt_table" rules="all" width="540" cellpadding="0" cellspacing="0" id="report_table_footer">
			<tfoot>
				<th width="40">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th align="right" width="100">Total:</th>
				<th align="right" width="100" id="value_total_recv"><? echo number_format($total_recv_qty,2); ?></th>
				<th align="right" width="100" id="value_total_iss"><? echo number_format($total_iss_qty,2); ?></th>
				<th align="right" id="value_total_stock"><? echo number_format($total_stock_qty,2); ?></th>
			</tfoot>
		</table>
	</div>
</fieldset>
<script>setFilterGrid('table_body',-1,tableFilters);</script>
<?
exit();
}

if($action=="trans_issue_grey_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $po_id;
	if ($cbo_store_name>0) 
	{
		$store_cond = " and b.store_name = $cbo_store_name";
	}
	$buyer_library=return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and b.supplier_id=c.supplier_id and a.status_active=1  group by a.id,a.supplier_name order by a.supplier_name","id","supplier_name");

	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body></html>');
			d.close();
		}
		var tableFilters =
		{
			col_9: "none",
			col_operation: {
				id: ["value_tot_qty"],
				col: [6],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}

	</script>
	<fieldset style="width:638px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div style="width:620px" id="report_container">
			<table border="1" class="rpt_table" rules="all" width="620" cellpadding="0" cellspacing="0" id="table_header">
				<caption>
					<b>Knit Grey Fabrics Issued Info</b>
				</caption>
				<thead>
					<th width="30">Sl</th>
					<th width="80">Issue Date</th>
					<th width="70">Dyeing Company</th>
					<th width="70">Prog. No</th>
					<th width="100">Issue ID</th>
					<th width="50">Issue Ch. No</th>
					<th width="80">Delivery Qty</th>
					<th width="50">Roll</th>
					<th >Rack No</th>
				</thead>
			</table>
			<div style="width:638px; overflow-y:scroll; max-height:250px;font-size:12px;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="620" cellpadding="0" cellspacing="0" id="table_body">
					<tbody>
						<?
						$i=1;
						$sql_data=("SELECT b.from_program,b.to_program, b.transfer_qnty as transfer_qnty,a.to_order_id from inv_item_transfer_dtls b,inv_item_transfer_mst a,order_wise_pro_details c where a.id=b.mst_id  and c.dtls_id=b.id and c.trans_type=6 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=13 and a.transfer_criteria=4 and  b.from_program='$prog_no' and b.to_program='$prog_no' and a.to_order_id in($po_id) and b.from_program>0 and b.to_program>0 ");
						$data_array=sql_select($sql_data);

						$transfer_qty_arr=array();
						foreach($data_array as $row_b)
						{
							$transfer_qty_arr[$row_b[csf('to_program')]]['to_order_id']=$row_b[csf('transfer_qnty')];
						}

						$mrr_sql="SELECT a.knit_dye_source,a.knit_dye_company,a.issue_number,a.order_id,a.issue_date,a.challan_no,b.no_of_roll,b.rack,b.program_no, sum(c.quantity) as knitting_issue_qnty from inv_issue_master a,inv_grey_fabric_issue_dtls b ,order_wise_pro_details c where a.id=b.mst_id and b.id =  c.dtls_id and a.item_category=13 and b.program_no='$prog_no' and c.po_breakdown_id in($po_id) and a.entry_form in (16) and c.entry_form in (16) and b.status_active=1 and b.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $store_cond group by  b.program_no, b.issue_qnty,a.issue_number,a.issue_date,a.challan_no,b.no_of_roll, b.rack,a.order_id, a.knit_dye_source,a.knit_dye_company
						union all
						SELECT a.knit_dye_source,a.knit_dye_company,a.issue_number,a.order_id,a.issue_date,a.challan_no,b.no_of_roll,b.rack,b.program_no, sum(c.qnty) as knitting_issue_qnty 
						from inv_issue_master a,inv_grey_fabric_issue_dtls b ,pro_roll_details c, pro_roll_details d
						where a.id=b.mst_id and b.id = c.dtls_id and a.id=c.mst_id and C.BARCODE_NO=D.BARCODE_NO and D.ENTRY_FORM in(2,22) 
						and a.item_category=13 
						and d.booking_no='$prog_no' and c.po_breakdown_id in($po_id) and a.entry_form in (61) and c.entry_form in (61) 
						and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $store_cond
						group by b.program_no, b.issue_qnty,a.issue_number,a.issue_date,a.challan_no,b.no_of_roll, b.rack,a.order_id, a.knit_dye_source,a.knit_dye_company";
						
						//echo $mrr_sql;

						$dtlsArray=sql_select($mrr_sql);

						/*$barcode_res = sql_select("SELECT  a.barcode_no from pro_roll_details a, inv_receive_master b where a.entry_form = 2 and a.mst_id = b.id and a.booking_no = '$prog_no' and a.po_breakdown_id in ($po_id) and a.entry_form = 2 and a.status_active = 1 and a.is_deleted = 0 and b.company_id = $companyID");

						foreach ($barcode_res as $val)
						{
							$productionBarcode[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
						}
						$all_productionBarcode = implode(",", array_filter(array_unique($productionBarcode)));
						if($all_productionBarcode) 
						{
							$barcodeCond = $all_Barcode_cond = "";
							$all_productionBarcode_arr=explode(",",$all_productionBarcode);
							if($db_type==2 && count($all_productionBarcode_arr)>999)
							{
								$all_productionBarcode_chunk=array_chunk($all_productionBarcode_arr,999) ;
								foreach($all_productionBarcode_chunk as $chunk_arr)
								{
									$chunk_arr_value=implode(",",$chunk_arr);
									$barcodeCond.=" barcode_no in($chunk_arr_value) or ";
								}

								$all_Barcode_cond.=" and (".chop($barcodeCond,'or ').")";

							}
							else
							{
								$all_Barcode_cond=" and barcode_no in($all_productionBarcode)";
							}

							// Roll Wise Issue Start -----
							$rollWiseIssue = sql_select("SELECT a.qnty as issue_qnty, c.rack, c.self from  pro_roll_details a, inv_issue_master b,inv_grey_fabric_issue_dtls c  where a.mst_id = b.id and b.id = c.mst_id and a.dtls_id =c.id and a.entry_form=61 and b.entry_form = 61 and a.status_active=1 and a.is_deleted = 0 $all_Barcode_cond");
							foreach ($rollWiseIssue as $val)
							{
								$iss_arr[$val[csf('rack')]][$val[csf('self')]]+=$val[csf('issue_qnty')];
								$rack_shelf_arr[$val[csf('rack')]][$val[csf('self')]]=$val[csf('issue_qnty')];
							}
							// End roll Issue  ----------------------------------------------
						}*/

						foreach($dtlsArray as $row)
						{
							if ($i%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							$trans_out=$transfer_qty_arr[$row[csf('program_no')]][$row[csf('order_id')]]['to_qnty'];
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><p><? echo $i; ?>&nbsp;</p></td>
								<td width="80"><p><? echo change_date_format($row[csf('issue_date')]); ?>&nbsp;</p></td>
								<?  if($row[csf('knit_dye_source')] == 1)
								{
									$dye_company = $company_library[$row[csf('knit_dye_company')]];
								}else{
									$dye_company = $buyer_library[$row[csf('knit_dye_company')]];
								}
								?>
								<td width="70"><p><? echo $dye_company; ?>&nbsp;</p></td>

								<td width="70"><p><? echo $prog_no; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $row[csf('issue_number')]; ?>&nbsp;</p></td>
								<td width="50"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
								<td width="80" align="right"><p><? echo number_format($row[csf('knitting_issue_qnty')],2); ?></p></td>
								<td width="50"><p><? echo $row[csf('no_of_roll')]; ?>&nbsp;</p></td>
								<td ><p><? echo $row[csf('rack')]; ?>&nbsp;</p></td>
							</tr>
							<?
							$tot_qty+=$row[csf('knitting_issue_qnty')];
							$tot_trans_qty+=$trans_out;
							$i++;
						}
						?>
					</tbody>
				</table>
			</div>
			<table border="1" class="rpt_table" rules="all" width="620" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
					<th width="30">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="70">&nbsp;</th>
					<th width="70">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="50">Total:</th>
					<th width="80" id="value_tot_qty"><? echo number_format($tot_qty,2); ?></th>

					<th width="50">&nbsp;</th>
					<th >&nbsp;</th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<script>setFilterGrid('table_body',-1,tableFilters);</script>
	<?
	exit();
}


if($action=="trans_stock_grey_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $po_id;
	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$('#table_body tbody tr:first').hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			$('#table_body tbody tr:first').show();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="250px";
		}
		var tableFilters =
		{
			col_10: "none",
			col_operation: {
				id: ["value_total_recv","value_total_iss","value_total_stock"],
				col: [3,4,5],
				operation: ["sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML"]
			}
		}
	</script>
	<fieldset style="width:560px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div id="report_container" style="width:100%">
			<table border="1" class="rpt_table" rules="all" width="540" cellpadding="0" cellspacing="0" id="table_header">
				<caption>
					<b>Knit Grey Fabrics Stock Info</b>
				</caption>
				<thead>
					<th width="40">Sl</th>
					<th width="100">Rack No</th>
					<th width="100">Shelf No</th>
					<th width="100">Transfered Qty</th>
					<th width="100">Issue Qty</th>
					<th>Stock Qty</th>
				</thead>
			</table>
			<div style="width:560px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="540" cellpadding="0" cellspacing="0" id="table_body">
					<tbody>
						<?

						/*$barcodeData = sql_select("SELECT b.mst_id, b.barcode_no, b.po_breakdown_id, a.booking_id from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form=2 and b.entry_form=2 and a.item_category=13 and a.receive_basis=2 and b.booking_no in('$prog_no')");
						foreach ($barcodeData as $row) 
						{
							$production_barcode_no_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
							$barcode_no_ref[$row[csf('barcode_no')]]["po"] = $row[csf('po_breakdown_id')];
						}
						//print_r($production_barcode_no_arr);
						$production_barcode_no_arr = array_filter($production_barcode_no_arr);
						if(count($production_barcode_no_arr) > 0)
						{
							//echo count($production_barcode_no_arr);die;
							$all_production_barcode_no_arr = implode(",", $production_barcode_no_arr);
							$productionBarcode = ""; $production_barcode_cond_for_rcv = "";
							if($db_type==2 && count($production_barcode_no_arr)>999)
							{
								$all_production_barcode_no_arr_chunk=array_chunk($production_barcode_no_arr,999) ;
								foreach($all_production_barcode_no_arr_chunk as $chunk_arr)
								{
									$productionBarcode.=" barcode_no in(".implode(",",$chunk_arr).") or ";
								}

								$production_barcode_cond_for_rcv.=" and (".chop($productionBarcode,'or ').")";
							}
							else
							{
								$production_barcode_cond_for_rcv=" and barcode_no in($all_production_barcode_no_arr)";
							}
						}*/
						/*echo "<pre>";
						print_r($production_barcode_cond_for_rcv);die;*/

						$recv_arr=array(); $rack_shelf_arr=array(); $recv_arr_trans=array();
						$sql_data=("SELECT b.from_program,b.to_program,b.rack, b.shelf, b.transfer_qnty as transfer_qnty from inv_item_transfer_dtls b,inv_item_transfer_mst a,order_wise_pro_details c where a.id=b.mst_id  and c.dtls_id=b.id and c.trans_type=6 and a.status_active=1 and a.is_deleted=0 and a.item_category=13 and a.transfer_criteria=4 and  b.from_program='$prog_no'  and a.from_order_id in($po_id) and b.from_program>0 and b.to_program>0
							union all
							SELECT  0 as from_program, 0 as to_program,  b.rack, b.shelf,sum(case when c.trans_type in(6) then d.qnty else 0 end) as transfer_qnty  from inv_item_transfer_dtls b,inv_item_transfer_mst a, order_wise_pro_details c, pro_roll_details d, pro_roll_details e where a.id=b.mst_id  and c.dtls_id=b.id and a.id=d.mst_id and b.id=d.dtls_id and c.trans_type in(6) and a.status_active=1 and a.is_deleted=0 and a.item_category=13 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.barcode_no = e.barcode_no and e.entry_form=2 and e.receive_basis=2  and e.booking_no='$prog_no' and c.entry_form in (83,82,110) and a.entry_form in (83,82,110) and a.transfer_criteria in (1,2,4,6) and (c.is_sales is null or c.is_sales = 0 or c.is_sales = 2) and c.po_breakdown_id in($po_id) group by b.rack, b.shelf ");
						$data_array=sql_select($sql_data);
						$transfer_qty_arr=array();

						$rack_id=$self_id=0;
						foreach($data_array as $row_b)
						{
							if($row_b[csf('rack')] == "")
							{
								$rack_id=0;
							}else{
								$rack_id=$row_b[csf('rack')];
							}
							if($row_b[csf('shelf')] == "")
							{
								$self_id=0;
							}else{
								$self_id=$row_b[csf('shelf')];
							}

							$transfer_qty_arr[$rack_id][$self_id]['from_qnty']+=$row_b[csf('transfer_qnty')];
						}

						$sql_result=("SELECT b.from_program,b.to_program,b.rack, b.shelf, b.transfer_qnty as transfer_qnty,a.to_order_id from inv_item_transfer_dtls b,inv_item_transfer_mst a,order_wise_pro_details c where a.id=b.mst_id  and c.dtls_id=b.id and c.trans_type=6 and a.status_active=1 and a.is_deleted=0 and a.item_category=13 and a.transfer_criteria=4 and b.to_program='$prog_no' and a.to_order_id in($po_id) and b.from_program>0 and b.to_program>0 
							union all 
							SELECT 0 as from_program, 0 as to_program, b.rack, b.shelf, 
							sum(case when c.trans_type in(5) then d.qnty else 0 end) as transfer_qnty,
							case when c.entry_form=82 then b.from_order_id else 0 end as from_order_id
							from inv_item_transfer_dtls b,inv_item_transfer_mst a,order_wise_pro_details c, pro_roll_details d, pro_roll_details e
							where a.id=b.mst_id and c.dtls_id=b.id and a.id=d.mst_id and b.id=d.dtls_id and c.trans_type in(5) 
							and d.barcode_no = e.barcode_no and e.entry_form=2 and e.receive_basis=2 and e.booking_no='$prog_no'
							and a.status_active=1 and a.is_deleted=0 and a.item_category=13 and b.status_active=1 and b.is_deleted=0 
							and c.status_active=1 and c.is_deleted=0 and c.entry_form in(82) and a.entry_form in(82) and a.transfer_criteria in (1,2,4,6) and (c.is_sales is null or c.is_sales = 0 or c.is_sales = 2) 
							and c.po_breakdown_id in($po_id) $production_barcode_cond_for_rcv 
							group by b.rack, b.shelf, c.entry_form, b.from_order_id");
						$data_array_issue=sql_select($sql_result);

						$transfer_qty_issue_arr=array();
						$rack_id=$self_id=0;
						foreach($data_array_issue as $row_b)
						{
							if($row_b[csf('rack')] == "")
							{
								$rack_id=0;
							}else{
								$rack_id=$row_b[csf('rack')];
							}
							if($row_b[csf('shelf')] == "")
							{
								$self_id=0;
							}else{
								$self_id=$row_b[csf('shelf')];
							}

							$transfer_qty_issue_arr[$rack_id][$self_id]['from_qnty']+=$row_b[csf('transfer_qnty')];
							$rack_shelf_arr[$rack_id][$self_id]=$row_b[csf('transfer_qnty')];

						}
						//print_r($transfer_qty_issue_arr);die;
						$i=1;

						$iss_data=sql_select("SELECT b.rack, b.self, sum(c.quantity) as issue_qnty from inv_issue_master a, inv_grey_fabric_issue_dtls b ,order_wise_pro_details c where a.id=b.mst_id and b.id = c.dtls_id and a.company_id='$companyID' and a.item_category=13 and b.program_no='$prog_no' and c.po_breakdown_id in ($po_id) and a.entry_form in (16,61) and c.entry_form in (16,61) and b.status_active=1 and b.is_deleted=0 group by b.rack,b.self
							union all
							SELECT b.rack, b.self, sum(c.qnty) as issue_qnty 
							from inv_issue_master a,inv_grey_fabric_issue_dtls b ,pro_roll_details c, pro_roll_details d
							where a.id=b.mst_id and b.id = c.dtls_id and a.id=c.mst_id and C.BARCODE_NO=D.BARCODE_NO and D.ENTRY_FORM in(2,22) 
							and a.item_category=13 
							and d.booking_no='$prog_no' and c.po_breakdown_id in($po_id) and a.entry_form in (61) and c.entry_form in (61) 
							and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.rack,b.self");

						$iss_arr=array();
						$rack_id=$self_id=0;
						foreach($iss_data as $row)
						{
							if($row[csf('rack')] == "")
							{
								$rack_id=0;
							}else{
								$rack_id=$row[csf('rack')];
							}
							if($row[csf('self')] == "")
							{
								$self_id=0;
							}else{
								$self_id=$row[csf('self')];
							}

							$iss_arr[$rack_id][$self_id]=$row[csf('issue_qnty')];
							$rack_shelf_arr[$rack_id][$self_id]=$row[csf('issue_qnty')];
						}

						//print_r($rack_shelf_arr);die;

						$rackSql = "
						SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id, b.serial_no FROM lib_floor_room_rack_mst a
						INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.rack_id
						WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyID.")";
						$rackDetails = return_library_array( $rackSql, 'floor_room_rack_id', 'floor_room_rack_name');
						
						//selfSql
						$selfSql = "
							SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
							FROM lib_floor_room_rack_mst a
							INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.shelf_id
							WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyID.")
						";
						$selfDetails = return_library_array( $selfSql, 'floor_room_rack_id', 'floor_room_rack_name');
						$i=1;
						foreach($rack_shelf_arr as $rack=>$data)
						{
							foreach($data as $shelf=>$qty)
							{
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";


								$iss_qty=$iss_arr[$rack][$shelf];
								$trans_from_recv=$transfer_qty_arr[$rack][$shelf]['from_qnty'];
								$tran_to_issue=$transfer_qty_issue_arr[$rack][$shelf]['from_qnty'];
								$tot_recv=$tran_to_issue;
								$tot_issue=$iss_qty+$trans_from_recv;
								$stock_qty=$tot_recv-$tot_issue;
								?>
								<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
									<td width="40"><p><? echo $i; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $rackDetails[$rack]; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $selfDetails[$shelf]; ?>&nbsp;</p></td>
									<td width="100" align="right"><? echo number_format($tot_recv,2); ?></td>
									<td width="100" align="right"><? echo number_format($tot_issue,2); ?></td>
									<td align="right"><? echo number_format($stock_qty,2); ?></td>
								</tr>
								<?
								$i++;
								$total_recv_qty=$tot_recv;
								$total_iss_qty=$tot_issue;
								$total_stock_qty=$stock_qty;
							}
						}

						?>
					</tbody>
				</table>
			</div>
			<table border="1" class="rpt_table" rules="all" width="540" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
					<th width="40">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th align="right" width="100">Total:</th>
					<th align="right" width="100" id="value_total_recv"><? echo number_format($total_recv_qty,2); ?></th>
					<th align="right" width="100" id="value_total_iss"><? echo number_format($total_iss_qty,2); ?></th>
					<th align="right" id="value_total_stock"><? echo number_format($total_stock_qty,2); ?></th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<script>setFilterGrid('table_body',-1,tableFilters);</script>
	<?
	exit();
}


if ($action == "booking_no_popup")
{
	echo load_html_head_contents("Booking Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_booking_id").val(splitData[0]);
			$("#hide_booking_no").val(splitData[1]);
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:740px;">
				<table width="740" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Buyer</th>
						<th width="170">Please Enter Booking No</th>
						<th>Booking Date</th>

						<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
						<input type="hidden" name="hide_booking_no" id="hide_booking_no" value="" />
						<input type="hidden" name="hide_booking_id" id="hide_booking_id" value="" />
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
								?>
							</td>
							<td align="center">
								<input type="text" style="width:150px" class="text_boxes" name="txt_booking_no" id="txt_booking_no" />
							</td>
							<td align="center">
								<input type="text" style="width:70px" class="datepicker" name="txt_date_from" id="txt_date_from" readonly/> To
								<input type="text" style="width:70px" class="datepicker" name="txt_date_to" id="txt_date_to" readonly/>
							</td>

							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_booking_no').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_booking_no_search_list_view', 'search_div', 'knitting_program_wise_grey_fab_stock_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td colspan="4" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}
if($action == "create_booking_no_search_list_view")
{
	$data=explode('**',$data);

	if ($data[0]!=0) $company="  company_id='$data[0]'";
	if ($data[1]!=0) $buyer=" and buyer_id='$data[1]'"; else $buyer="";
	if ($data[2]!=0) $booking_no=" and booking_no_prefix_num='$data[2]'"; else $booking_no='';
	if($db_type==0)
	{
		if ($data[3]!="" &&  $data[4]!="") $booking_date  = "and booking_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
		if ($data[3]!="" &&  $data[4]!="") $booking_date  = "and booking_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}

	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,5=>$item_category,6=>$fabric_source,7=>$suplier,8=>$approved,9=>$is_ready);

	$sql= "select id,booking_no_prefix_num, booking_no, booking_date, company_id, buyer_id, job_no, po_break_down_id, item_category, fabric_source, supplier_id, is_approved, ready_to_approved from wo_booking_mst where $company $buyer $booking_no $booking_date and booking_type=1 and is_short=2 and status_active=1 and is_deleted=0 order by id Desc";
	echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No.,Fabric Nature,Fabric Source,Supplier,Approved,Is-Ready", "80,80,50,80,90,100,80,80,50,50","820","320",0, $sql , "js_set_value", "id,booking_no", "", 1, "0,0,company_id,buyer_id,0,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", $arr , "booking_no,booking_date,company_id,buyer_id,job_no,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", '','','0,3,0,0,0,0,0,0,0,0','','');
	exit();
}


if($action=="transfer_in_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	if ($cbo_store_name>0) 
	{
		$store_cond = " and b.to_store = $cbo_store_name";
	}
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
		}
		var tableFilters =
		{
			col_10: "none",
			col_operation: {
				id: ["value_total_balance"],
				col: [6],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}
	</script>
	<fieldset style="width:500px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div id="report_container" style="width:100%">
			<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" id="table_header">
				<caption>
					<b>Transfer In</b>
				</caption>
				<thead>
					<th width="30">Sl</th>
					<th width="150">Transfer ID</th>
					<th width="100">From Job</th>
					<th width="100">From Order</th>
					<th width="100">Transfer Date</th>
					<th width="120">Transfer Qty</th>

				</thead>
			</table>
			<div style="width:500px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" id="table_body">
					<?

					$data_array=" SELECT a.transfer_system_id as sys_num, a.transfer_date, 0 as bfrom_order_id, a.from_order_id, c.entry_form,
					sum(case when c.trans_type in(5) then c.quantity else 0 end) as qnty  from inv_item_transfer_dtls b,inv_item_transfer_mst a,order_wise_pro_details c
					where a.id=b.mst_id  and c.dtls_id=b.id and c.trans_type in(5) and a.status_active=1 and a.is_deleted=0 and a.item_category=13
					and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
					and c.entry_form in (13) and a.entry_form in (13) and a.transfer_criteria in (1,2,4) and  b.from_program>0 and b.to_program>0 and c.is_sales <> 1 and b.to_program in($prog_no) and a.to_order_id in($po_id) $store_cond
					group by a.transfer_system_id, a.transfer_date, a.from_order_id, c.entry_form
					union all 
					SELECT a.transfer_system_id as sys_num, a.transfer_date, 
					case when c.entry_form=82 then b.from_order_id else 0 end as bfrom_order_id,
					case when c.entry_form=83 then a.from_order_id else 0 end as from_order_id, c.entry_form,
					sum(d.qnty) as qnty 
					from inv_item_transfer_dtls b,inv_item_transfer_mst a,order_wise_pro_details c, pro_roll_details d, pro_roll_details g, ppl_planning_info_entry_dtls h
					where a.id=b.mst_id  and c.dtls_id=b.id and a.id=d.mst_id and b.id=d.dtls_id and c.trans_type in(5) and a.status_active=1 and a.is_deleted=0 and a.item_category=13 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
					and c.entry_form in (83,82) and a.entry_form in (83,82) and a.transfer_criteria in (1,2,4,6) and (c.is_sales is null or c.is_sales = 0 or c.is_sales = 2) and c.po_breakdown_id in($po_id) $production_barcode_cond_for_rcv $store_cond and d.barcode_no=g.barcode_no and g.entry_form=2 and g.receive_basis=2 and cast(g.booking_no as varchar2(30)) = cast(h.id as varchar2(30)) and h.id=$prog_no
					group by a.transfer_system_id, a.transfer_date , c.entry_form,b.from_order_id,a.from_order_id";
					//echo $data_array;
					$result = sql_select($data_array);
					foreach($result as $row)
					{
						$po_arr[$row[csf("bfrom_order_id")]]=$row[csf("bfrom_order_id")];
						$po_arr[$row[csf("from_order_id")]]=$row[csf("from_order_id")];
					}
					$job_array=array();
					
					if(!empty($po_arr))
					{
						$sqls="SELECT b.id,a.job_no,b.po_number from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in (". implode(',', $po_arr).")  group by b.id,a.job_no,b.po_number ";
						foreach(sql_select($sqls) as $vals)
						{
							$job_array[$vals[csf("id")]]["job"]=$vals[csf("job_no")];
							$job_array[$vals[csf("id")]]["po_number"]=$vals[csf("po_number")];
						}
					}
					?>
					<tbody>
						<?
						$i = 1;
						foreach($result as $row)
						{
							if ($row[csf("entry_form")]==82) 
							{
								$job_no=$job_array[$row[csf("bfrom_order_id")]]["job"];
								$po_no=$job_array[$row[csf("bfrom_order_id")]]["po_number"];
							}
							else
							{
								$job_no=$job_array[$row[csf("from_order_id")]]["job"];
								$po_no=$job_array[$row[csf("from_order_id")]]["po_number"];
							}
							?>
							<tr>
								<td width="30"><? echo $i;?></td>
								<td width="150"><? echo $row[csf("sys_num")];?></td>
								<td width="100"><? echo $job_no;?></td>
								<td width="100"><? echo $po_no;?></td>
								<td width="100"><? echo change_date_format($row[csf("transfer_date")]);?></td>
								<td width="120" align="center"><? echo $row[csf("qnty")];?></td>
							</tr>
							<?
							$i++;
							$total_balance += $row[csf("qnty")];
						}
						?>
						<tr bgcolor="#E4E4E4">
							<td colspan="5" align="right"><strong>Total </strong></td>
							<td align="center"><strong><? echo number_format($total_balance,2); ?></strong></td>
						</tr>
					</tbody>


				</table>
			</div>

		</div>
	</fieldset>
	<script>setFilterGrid('table_body',-1,tableFilters);</script>
	<?
	exit();
}


if($action=="issue_return_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	if ($cbo_store_name>0) 
	{
		$store_cond = " and b.store_id = $cbo_store_name";
	}
	$buyer_library=return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(9,20) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id","supplier_name");
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
		}
		var tableFilters =
		{
			col_10: "none",
			col_operation: {
				id: ["value_total_balance"],
				col: [5],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}
	</script>
	<fieldset style="width:700px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div id="report_container" style="width:100%">
			<table border="1" class="rpt_table" rules="all" width="680" cellpadding="0" cellspacing="0" id="table_header">
				<caption>
					<b>Knit Grey Fabrics issue Return Info</b>
				</caption>
				<thead>
					<th width="30">Sl</th>
					<th width="100">Knitting Company</th>
					<th width="100">Return Date</th>
					<th width="150">Return ID</th>
					<th width="100">Return Ch. No</th>
					<th width="100">Return Qty</th>
					<th width="100">Roll</th>
				</thead>
			</table>
			<div style="width:698px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="680" cellpadding="0" cellspacing="0" id="table_body">
					<?
					$data_array = "SELECT a.knitting_source, a.knitting_company, a.recv_number, a.receive_date, a.challan_no, c.po_breakdown_id, a.booking_no, c.quantity from inv_receive_master a, inv_transaction b, order_wise_pro_details c where a.id = b.mst_id and b.transaction_type =4 and b.id=c.trans_id and c.entry_form=51 and a.receive_basis=3 and a.booking_id = $prog_no and c.po_breakdown_id=$po_id $store_cond
					union all 
					SELECT a.knitting_source, a.knitting_company, a.recv_number, a.receive_date, a.challan_no, c.po_breakdown_id, a.booking_no, c.quantity 
					from inv_receive_master a, inv_transaction b, order_wise_pro_details c 
					where a.id = b.mst_id and b.transaction_type =4 and b.id=c.trans_id and c.entry_form=84 and a.receive_basis=0 and b.pi_wo_batch_no = $prog_no and c.po_breakdown_id=$po_id $store_cond";

					// echo $data_array;
					$result = sql_select($data_array);
					?>
					<tbody>
						<?
						$i = 1;
						foreach($result as $row)
						{
							?>
							<tr>
								<td width="30"><? echo $i;?></td>
								<td width="100"><? echo ($row[csf("knitting_source")] ==1) ? $company_library[$row[csf("knitting_company")]] : $buyer_library[$row[csf("knitting_company")]];?></td>
								<td width="100"><? echo change_date_format($row[csf("receive_date")]);?></td>
								<td width="150"><? echo $row[csf("recv_number")];?></td>
								<td width="100"><? echo $row[csf("challan_no")];?></td>
								<td width="100" align="right"><? echo $row[csf("quantity")];?></td>
								<td width="100"><? ?></td>
							</tr>
							<?
							$i++;
							$total_balance += $row[csf("quantity")];
						}
						?>
						<tr bgcolor="#E4E4E4">
							<td colspan="5" align="right"><strong>Total </strong></td>
							<td align="right"><strong><? echo number_format($total_balance,2); ?></strong></td>
							<td></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</fieldset>
	<script>setFilterGrid('table_body',-1,tableFilters);</script>
	<?
	exit();
}

if($action=="transfer_out_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	if ($cbo_store_name>0) 
	{
		$store_cond = " and b.from_store = $cbo_store_name";
	}
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
		}
		var tableFilters =
		{
			col_10: "none",
			col_operation: {
				id: ["value_total_balance"],
				col: [6],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}
	</script>
	<div>
		<fieldset style="width:500px; ">
			<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
			<div id="report_container" style="width:100%">
				<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" id="table_header">
					<caption>
						<b>Transfer Out</b>
					</caption>
					<thead>
						<th width="30">Sl</th>
						<th width="150">Transfer ID</th>
						<th width="100">To Job</th>
						<th width="100">To Order</th>
						<th width="100">Transfer Date</th>
						<th width="120">Transfer Qty</th>

					</thead>
				</table>
				<div style="width:500px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
					<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" id="table_body">

						<?
						$barcodeData = sql_select("SELECT b.mst_id, b.barcode_no, b.po_breakdown_id, a.booking_id from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form=2 and b.entry_form=2 and a.item_category=13 and a.receive_basis=2 and b.booking_no in('$prog_no')");
						foreach ($barcodeData as $row) 
						{
							$production_barcode_no_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
							$barcode_no_ref[$row[csf('barcode_no')]]["po"] = $row[csf('po_breakdown_id')];
						}
						//print_r($production_barcode_no_arr);
						$production_barcode_no_arr = array_filter($production_barcode_no_arr);
						if(count($production_barcode_no_arr) > 0)
						{
							//echo count($production_barcode_no_arr);die;
							$all_production_barcode_no_arr = implode(",", $production_barcode_no_arr);
							$productionBarcode = ""; $production_barcode_cond_for_rcv = "";
							if($db_type==2 && count($production_barcode_no_arr)>999)
							{
								$all_production_barcode_no_arr_chunk=array_chunk($production_barcode_no_arr,999) ;
								foreach($all_production_barcode_no_arr_chunk as $chunk_arr)
								{
									$productionBarcode.=" barcode_no in(".implode(",",$chunk_arr).") or ";
								}

								$production_barcode_cond_for_rcv.=" and (".chop($productionBarcode,'or ').")";
							}
							else
							{
								$production_barcode_cond_for_rcv=" and barcode_no in($all_production_barcode_no_arr)";
							}
						}
						/*echo "<pre>";
						print_r($production_barcode_cond_for_rcv);die;*/

						$data_array="SELECT a.transfer_system_id as sys_num, a.transfer_date, a.to_order_id, d.booking_no, c.entry_form,
						sum(case when c.trans_type in(6) then c.quantity else 0 end) as qnty  from inv_item_transfer_dtls b, inv_item_transfer_mst a left join wo_non_ord_samp_booking_mst d on a.to_order_id =d.id and a.entry_form=80, order_wise_pro_details c
						where a.id=b.mst_id  and c.dtls_id=b.id and c.trans_type in(6) and a.status_active=1 and a.is_deleted=0 and a.item_category=13
						and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
						and c.entry_form in (13,80) and a.entry_form in (13,80) and a.transfer_criteria in (1,2,4,6) and  b.from_program>0 and b.to_program>0 and (c.is_sales is null or c.is_sales = 0 or c.is_sales = 2) and b.from_program in($prog_no) and a.from_order_id in($po_id) $store_cond
						group by a.transfer_system_id, a.transfer_date, a.to_order_id, d.booking_no, c.entry_form ";

						if($production_barcode_cond_for_rcv !="")
						{
							$data_array .=" union all 
							SELECT a.transfer_system_id as sys_num, a.transfer_date, 
							case when c.entry_form=82 then b.to_order_id when c.entry_form=83 then a.to_order_id else 0 end as to_order_id, e.booking_no, c.entry_form, sum(case when c.trans_type in(6) then d.qnty else 0 end) as qnty  from inv_item_transfer_dtls b, inv_item_transfer_mst a left join wo_non_ord_samp_booking_mst e on a.to_order_id =e.id and a.entry_form=110, order_wise_pro_details c, pro_roll_details d
							where a.id=b.mst_id  and c.dtls_id=b.id and a.id=d.mst_id and b.id=d.dtls_id and c.trans_type in(6) and a.status_active=1 and a.is_deleted=0 and a.item_category=13
							and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
							and c.entry_form in (83,82,110) and a.entry_form in (83,82,110) and a.transfer_criteria in (1,2,4,6) and (c.is_sales is null or c.is_sales = 0 or c.is_sales = 2) and c.po_breakdown_id in($po_id) $production_barcode_cond_for_rcv $store_cond
							group by a.transfer_system_id, a.transfer_date, e.booking_no, c.entry_form,b.to_order_id,a.to_order_id";
						}
						
						//echo $data_array;
						$result = sql_select($data_array);
						// print_r($result);
						foreach($result as $row)
						{
							if($row[csf("entry_form")]==82 || $row[csf("entry_form")]==83 || $row[csf("entry_form")]==13)
							{
								$all_po_arr[$row[csf("bto_order_id")]]=$row[csf("bto_order_id")];
								$all_po_arr[$row[csf("to_order_id")]]=$row[csf("to_order_id")];
							} 
						}
						$all_po_ids = implode(",",array_filter($all_po_arr));
						$job_array=array();
						$sqls="SELECT b.id,a.job_no,b.po_number from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in ($all_po_ids)  group by b.id,a.job_no,b.po_number ";
						foreach(sql_select($sqls) as $vals)
						{
							$job_array[$vals[csf("id")]]["job"]=$vals[csf("job_no")];
							$job_array[$vals[csf("id")]]["po_number"]=$vals[csf("po_number")];
						}
						?>

						<tbody>
							<?
							$i = 1;
							foreach($result as $row)
							{
								if ($row[csf("entry_form")]==80 || $row[csf("entry_form")]==110) 
								{
									$job_no=$row[csf("booking_no")];
									$po_no="";
								}
								else
								{
									$job_no=$job_array[$row[csf("to_order_id")]]["job"];
									$po_no=$job_array[$row[csf("to_order_id")]]["po_number"];
								}
								?>
								<tr>
									<td width="30"><? echo $i;?></td>
									<td width="150"><? echo $row[csf("sys_num")];?></td>
									<td width="100"><? echo $job_no;?></td>
									<td width="100"><? echo $po_no;?></td>
									<td width="100"><? echo change_date_format($row[csf("transfer_date")]);?></td>
									<td width="120" align="center"><? echo $row[csf("qnty")];?></td>
								</tr>
								<?
								$i++;
								$total_balance += $row[csf("qnty")];
							}
							?>
							<tr bgcolor="#E4E4E4">
								<td colspan="5" align="right"><strong>Total </strong></td>
								<td align="center"><strong><? echo number_format($total_balance,2); ?></strong></td>
							</tr>
						</tbody>


					</table>
				</div>

			</div>
		</fieldset>
	</div>
	<script>setFilterGrid('table_body',-1,tableFilters);</script>
	<?
	exit();
}


?>