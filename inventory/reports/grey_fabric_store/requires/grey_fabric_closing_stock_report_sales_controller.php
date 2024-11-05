<?

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90) group by buyer_id)  $buyer_cond group by buy.id,buy.buyer_name  order by buyer_name","id,buyer_name", 0, "-All Buyer-", $selected, "" );
	exit();
}

if ($action=="load_drop_down_po_company")
{
	$data=explode("_", $data);
	if($data[0] == 1){
		echo create_drop_down( "cbo_pocompany_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 0, "-Po Company-", $selected, "load_drop_down( 'requires/grey_fabric_closing_stock_report_sales_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/grey_fabric_closing_stock_report_sales_controller' );" );
	}
	else
	{
		echo create_drop_down( "cbo_pocompany_id", 130, $blank_array,"", 1, "-Po Company-", $selected, "",0,"" );
	}
	exit();
}

if ($action == "load_drop_down_store") 
{
	$data = explode("**", $data);

	if ($data[1] == 2)
	{
		$disable = 1;

	}
	else
	{
		$disable = 0;
	}

	echo create_drop_down("cbo_store_name", 100, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and  a.status_active=1 and a.is_deleted=0 and a.company_id=$data[0] and b.category_type in(13)  order by a.store_name", "id,store_name", 1, "-- All Store--", 0, "", $disable);
	exit();
}

if ($action == "eval_multi_select") 
{
	echo "set_multiselect('cbo_buyer_id','0','0','','0');\n";
	echo "set_multiselect('cbo_pocompany_id','0','0','','0');\n";
	echo "setTimeout[($('#po_company_td a').attr('onclick',\"disappear_list(cbo_pocompany_id,'0');getCompanyId();\") ,3000)];\n";


	exit();
}

if($action=="order_no_search_popup")
{
	echo load_html_head_contents("Sales Order Info","../../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(booking_data)
		{
			document.getElementById('hidden_booking_data').value=booking_data;
			parent.emailwindow.hide();
		}

	</script>
	</head>
	<body>
		<div align="center">
			<fieldset style="width:830px;margin-left:4px;">
				<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
					<table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
						<thead>
							<th>Within Group</th>
							<th>Search By</th>
							<th>Search</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
								<input type="hidden" name="hidden_booking_data" id="hidden_booking_data" value="">
							</th>
						</thead>
						<tr class="general">
							<td align="center"><? echo create_drop_down( "cbo_within_group", 150, $yes_no,"",1, "--Select--", "",$dd,0 ); ?></td>
							<td align="center">
								<?
								$search_by_arr=array(1=>"Sales Order No",2=>"Sales / Booking No",3=>"Style Ref.");
								echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
								?>
							</td>
							<td align="center">
								<input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('cbo_within_group').value+'_'+<? echo $cbo_year; ?>, 'create_order_no_search_list_view', 'search_div', 'grey_fabric_closing_stock_report_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
							</td>
						</tr>
					</table>
					<div id="search_div" style="margin-top:10px"></div>
				</form>
			</fieldset>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_order_no_search_list_view")
{
	$data=explode('_',$data);

	$search_string=trim($data[0]);
	$search_by =$data[1];
	$company_id =$data[2];
	$within_group=$data[3];
	$cbo_year = $data[4];

	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');

	$search_field_cond='';
	if($search_string!="")
	{
		if($search_by==1) $search_field_cond=" and a.job_no like '%".$search_string."'";
		else if($search_by==2) $search_field_cond=" and a.sales_booking_no like '%".$search_string."'";
		else $search_field_cond=" and a.style_ref_no like '".$search_string."%'";
	}

	if ($db_type == 0)
	{
		if($cbo_year>0)
		{
			$sales_order_year_condition=" and YEAR(a.insert_date)=$cbo_year";
		}
	}
	else if ($db_type == 2)
	{
		if($cbo_year>0)
		{
			$sales_order_year_condition=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		}
	}

	if($within_group==0) $within_group_cond=""; else $within_group_cond=" and a.within_group=$within_group";

	if($db_type==0) $year_field="YEAR(a.insert_date) as year";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later

	$sql = "select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $search_field_cond $sales_order_year_condition order by a.id DESC";
	//echo $sql;//die;
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="90">Sales Order No</th>
			<th width="60">Year</th>
			<th width="80">Within Group</th>
			<th width="70">Buyer</th>
			<th width="120">Sales/ Booking No</th>
			<th width="80">Booking date</th>
			<th width="110">Style Ref.</th>
			<th>Location</th>
		</thead>
	</table>
	<div style="width:800px; max-height:300px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="tbl_list_search">
			<?
			$i=1;
			foreach ($result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if($row[csf('within_group')]==1)
					$buyer=$company_arr[$row[csf('buyer_id')]];
				else
					$buyer=$buyer_arr[$row[csf('buyer_id')]];

				$booking_data =$row[csf('id')]."**".$row[csf('job_no_prefix_num')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $booking_data; ?>');">
					<td width="40"><? echo $i; ?></td>
					<td width="90"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
					<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="80"><p><? echo $yes_no[$row[csf('within_group')]]; ?></p></td>
					<td width="70"><p><? echo $buyer; ?></p></td>
					<td width="120"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
					<td><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<?
	exit();
}

if($action=="report_generate") // Show
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$started = microtime(true);
	$company_name  	= str_replace("'","",$cbo_company_id);
	$within_group  	= str_replace("'","",$cbo_within_group);
	$pocompany_id 	= str_replace("'","",$cbo_pocompany_id);
	$po_buyer_id 	= str_replace("'","",$cbo_buyer_id);
	$cbo_year 		= str_replace("'","",$cbo_year);
	$order_no 		= trim(str_replace("'","",$txt_order_no));
	$order_id 		= str_replace("'","",$hide_order_id);
	$cbo_value_with = str_replace("'","",$cbo_value_with);

	$date_from 		= str_replace("'","",$txt_date_from);
	$date_to 		= str_replace("'","",$txt_date_to);

	$cbo_store_wise = str_replace("'","",$cbo_store_wise);
	$cbo_store_name = str_replace("'","",$cbo_store_name);

	$get_upto 		= str_replace("'","",$cbo_get_upto);
	$txt_days 		= str_replace("'","",$txt_days);
	$get_upto_qnty 	= str_replace("'","",$cbo_get_upto_qnty);
	$txt_qnty 		= str_replace("'","",$txt_qnty);

	//$store_arr 	 	= return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$company_arr 	= return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	//$buyer_arr   	= return_library_array( "select id, short_name from lib_buyer where status_active=1 and is_deleted=0", 'id','short_name');
	//$season_arr  	= return_library_array( "select id, season_name from LIB_BUYER_SEASON",'id','season_name');
	$yarn_count_arr = return_library_array("select id,yarn_count from lib_yarn_count", 'id', 'yarn_count');
	//$color_arr 		= return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

	if($pocompany_id!=0 || $pocompany_id!="")
	{
		if($within_group==1)
		{
			$pocompany_cond="and lc_company_id in($pocompany_id)";
		}
		else 
		{
			$pocompany_cond="and lc_company_id in($pocompany_id)";
		}
	} 
	else 
	{
		$pocompany_cond="";
	}

	if($cbo_store_wise==1)
	{
		$store_cond = " and store_id=$cbo_store_name";
	}

	if($po_buyer_id!=0 || $po_buyer_id!="")
	{
		if($within_group==1)
		{
			$buyer_id_cond=" and po_buyer in (".str_replace("'","",$po_buyer_id).")";
		}
		else 
		{

			$buyer_id_cond=" and buyer_id in (".str_replace("'","",$po_buyer_id).")";
		}
	}

	$date_cond="";
	if($date_from!="" && $date_to!="")
	{
		if($db_type==0)$start_date=change_date_format($date_from,"yyyy-mm-dd","");
		else if($db_type==2) $start_date=change_date_format($date_from,"","",1);

		if($db_type==0)$end_date=change_date_format($date_to,"yyyy-mm-dd","");
		else if($db_type==2) $end_date=change_date_format($date_to,"","",1);

		$date_cond=" and transaction_date <= '$end_date'";
	}

	if ($order_id=='') $order_no_cond=""; else $order_no_cond="and fso_id='$order_id'";

	if ($order_no=='') $sales_order_no_cond=""; else $sales_order_no_cond=" and job_no like '%$order_no%'";

	if($cbo_year>0)
	{
		$sales_order_year_condition=" and fso_year in($cbo_year)";
	}

	if($within_group !=0)
	{
		$within_group_cond = " and within_group='$within_group' ";
	}
	
	$data_array = sql_select("SELECT id, lc_company_id, lc_company_name as LC_COMPANY_NAME, buyer_id, buyer_name as BUYER_NAME, job_no as JOB_NO, style_no as STYLE_NO, booking_no as BOOKING_NO, booking_without_order, fso_no as FSO_NO, fso_id as FSO_ID, season, within_group, product_id as PRODUCT_ID, determination_id as DETERMINATION_ID, construction as CONSTRUCTION, composition as COMPOSITION, gsm as GSM, dia_width as DIA_WIDTH, color_id, color_no as COLOR_NO, color_range as COLOR_RANGE, color_range_id as COLOR_RANGE_ID, stitch_length as STITCH_LENGTH, yarn_lot as YARN_LOT, yarn_count AS YARN_COUNT, transaction_date as TRANSACTION_DATE, transaction_type as TRANSACTION_TYPE, cons_quantity as CONS_QUANTITY 
	from TMP_INV_GREY_STOCK_REF 
	where  status_active=1 $pocompany_cond $sales_order_no_cond $order_no_cond $buyer_id_cond $store_cond $date_cond $sales_order_year_condition");

	foreach ($data_array as  $row) 
	{
		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($row['TRANSACTION_DATE']));

		if($row["TRANSACTION_TYPE"] ==1 || $row["TRANSACTION_TYPE"] ==5)
		{
			if($row["COLOR_RANGE_ID"]!="")
			{
				$prodWiseSalesDataStatus[$row["FSO_ID"]][$row["PRODUCT_ID"]][$row["COLOR_RANGE"]][$row["STITCH_LENGTH"]]['COMPANY_NAME']=$row['COMPANY_NAME'];
				$prodWiseSalesDataStatus[$row["FSO_ID"]][$row["PRODUCT_ID"]][$row["COLOR_RANGE"]][$row["STITCH_LENGTH"]]['LC_COMPANY_NAME']=$row['LC_COMPANY_NAME'];
				$prodWiseSalesDataStatus[$row["FSO_ID"]][$row["PRODUCT_ID"]][$row["COLOR_RANGE"]][$row["STITCH_LENGTH"]]['BUYER_NAME']=$row['BUYER_NAME'];
				$prodWiseSalesDataStatus[$row["FSO_ID"]][$row["PRODUCT_ID"]][$row["COLOR_RANGE"]][$row["STITCH_LENGTH"]]['JOB_NO']=$row['JOB_NO'];
				$prodWiseSalesDataStatus[$row["FSO_ID"]][$row["PRODUCT_ID"]][$row["COLOR_RANGE"]][$row["STITCH_LENGTH"]]['STYLE_NO']=$row['STYLE_NO'];
				$prodWiseSalesDataStatus[$row["FSO_ID"]][$row["PRODUCT_ID"]][$row["COLOR_RANGE"]][$row["STITCH_LENGTH"]]['BOOKING_NO']=$row['BOOKING_NO'];
				$prodWiseSalesDataStatus[$row["FSO_ID"]][$row["PRODUCT_ID"]][$row["COLOR_RANGE"]][$row["STITCH_LENGTH"]]['BOOKING_TYPE']=$row['BOOKING_TYPE'];
				$prodWiseSalesDataStatus[$row["FSO_ID"]][$row["PRODUCT_ID"]][$row["COLOR_RANGE"]][$row["STITCH_LENGTH"]]['FSO_NO']=$row['FSO_NO'];
				$prodWiseSalesDataStatus[$row["FSO_ID"]][$row["PRODUCT_ID"]][$row["COLOR_RANGE"]][$row["STITCH_LENGTH"]]['PRODUCT_ID']=$row['PRODUCT_ID'];
				$prodWiseSalesDataStatus[$row["FSO_ID"]][$row["PRODUCT_ID"]][$row["COLOR_RANGE"]][$row["STITCH_LENGTH"]]['GSM']=$row['GSM'];
				$prodWiseSalesDataStatus[$row["FSO_ID"]][$row["PRODUCT_ID"]][$row["COLOR_RANGE"]][$row["STITCH_LENGTH"]]['DIA_WIDTH']=$row['DIA_WIDTH'];
				$prodWiseSalesDataStatus[$row["FSO_ID"]][$row["PRODUCT_ID"]][$row["COLOR_RANGE"]][$row["STITCH_LENGTH"]]['CONSTRUCTION']=$row['CONSTRUCTION'];
				$prodWiseSalesDataStatus[$row["FSO_ID"]][$row["PRODUCT_ID"]][$row["COLOR_RANGE"]][$row["STITCH_LENGTH"]]['COMPOSITION']=$row['COMPOSITION'];
				$prodWiseSalesDataStatus[$row["FSO_ID"]][$row["PRODUCT_ID"]][$row["COLOR_RANGE"]][$row["STITCH_LENGTH"]]['GSM']=$row['GSM'];
				$prodWiseSalesDataStatus[$row["FSO_ID"]][$row["PRODUCT_ID"]][$row["COLOR_RANGE"]][$row["STITCH_LENGTH"]]['COLOR_NO'] .=$row['COLOR_NO'].",";
				$prodWiseSalesDataStatus[$row["FSO_ID"]][$row["PRODUCT_ID"]][$row["COLOR_RANGE"]][$row["STITCH_LENGTH"]]['STORE_NAME'] .=$row['STORE_NAME'].",";
				$prodWiseSalesDataStatus[$row["FSO_ID"]][$row["PRODUCT_ID"]][$row["COLOR_RANGE"]][$row["STITCH_LENGTH"]]['YARN_LOT'] .=$row['YARN_LOT'].",";
				$prodWiseSalesDataStatus[$row["FSO_ID"]][$row["PRODUCT_ID"]][$row["COLOR_RANGE"]][$row["STITCH_LENGTH"]]['YARN_COUNT'] .=$row['YARN_COUNT'].",";
				$prodWiseSalesDataStatus[$row["FSO_ID"]][$row["PRODUCT_ID"]][$row["COLOR_RANGE"]][$row["STITCH_LENGTH"]]['COLOR_RANGE'] =$row['COLOR_RANGE'];
				$prodWiseSalesDataStatus[$row["FSO_ID"]][$row["PRODUCT_ID"]][$row["COLOR_RANGE"]][$row["STITCH_LENGTH"]]['STITCH_LENGTH'] =$row['STITCH_LENGTH'];
				$prodWiseSalesDataStatus[$row["FSO_ID"]][$row["PRODUCT_ID"]][$row["COLOR_RANGE"]][$row["STITCH_LENGTH"]]['STORE_NAME'] =$row['STORE_NAME'];

				$date_frm=date('Y-m-d',strtotime($start_date));
				$transaction_date=date('Y-m-d',strtotime($row['TRANSACTION_DATE']));

				if($transaction_date >= $date_frm)
				{
					if($row["TRANSACTION_TYPE"]==1)
					{
						$prodWiseSalesDataStatus[$row["FSO_ID"]][$row["PRODUCT_ID"]][$row["COLOR_RANGE"]][$row["STITCH_LENGTH"]]['RECV_QNTY'] +=$row["CONS_QUANTITY"];
					}
					else
					{
						$prodWiseSalesDataStatus[$row["FSO_ID"]][$row["PRODUCT_ID"]][$row["COLOR_RANGE"]][$row["STITCH_LENGTH"]]['TRANS_IN_QNTY'] +=$row["CONS_QUANTITY"];
					}
				}
				else
				{
					if($transaction_date < $date_frm)
					{
						if($row["TRANSACTION_TYPE"]==1)
						{
							$prodWiseSalesDataStatus[$row["FSO_ID"]][$row["PRODUCT_ID"]][$row["COLOR_RANGE"]][$row["STITCH_LENGTH"]]['OPENING_RCV'] +=$row["CONS_QUANTITY"];
						}
						else
						{
							$prodWiseSalesDataStatus[$row["FSO_ID"]][$row["PRODUCT_ID"]][$row["COLOR_RANGE"]][$row["STITCH_LENGTH"]]['OPENING_TRANSIN'] +=$row["CONS_QUANTITY"];
						}
						
					}
				}
			}
		}
		else if($row["TRANSACTION_TYPE"] ==6)
		{
			
			if($transaction_date >= $date_frm){
				$transOutQnty[$row['FSO_ID']][$row['PRODUCT_ID']][$row["COLOR_RANGE"]][$row["STITCH_LENGTH"]] += $row["CONS_QUANTITY"];
				
			}else{
				if($transaction_date < $date_frm){
					$openingTransOutQnty[$row['FSO_ID']][$row['PRODUCT_ID']][$row["COLOR_RANGE"]][$row["STITCH_LENGTH"]] += $row["CONS_QUANTITY"];
				}
			}
		}
		else if($row["TRANSACTION_TYPE"] ==2)
		{
			if($transaction_date >= $date_frm){
				$knit_issue_arr[$row['FSO_ID']][$row['PRODUCT_ID']][$row["COLOR_RANGE"]][$row["STITCH_LENGTH"]]['issue_qty'] += $row['CONS_QUANTITY'];
			}
			else
			{
				if($transaction_date < $date_frm){
					$opening_issue[$row['FSO_ID']][$row['PRODUCT_ID']][$row["COLOR_RANGE"]][$row["STITCH_LENGTH"]]['issue_qty'] += $row['CONS_QUANTITY'];
				}
			}
		}
	}

	if(empty($prodWiseSalesDataStatus))
	{
		echo "data not found";
		die;
	}

	$transaction_date_array=array();
	$sql_date="SELECT c.po_breakdown_id as PO_BREAKDOWN_ID, a.prod_id as PROD_ID, min(a.transaction_date) as MIN_DATE, max(a.transaction_date) as MAX_DATE
	from inv_transaction a,order_wise_pro_details c
	where a.id=c.trans_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=13 $store_cond2 group by c.po_breakdown_id,a.prod_id";

	$sql_date_result=sql_select($sql_date);
	foreach( $sql_date_result as $row )
	{
		$transaction_date_array[$row['PO_BREAKDOWN_ID']][$row['PROD_ID']]['min_date']=$row['MIN_DATE'];
		$transaction_date_array[$row['PO_BREAKDOWN_ID']][$row['PROD_ID']]['max_date']=$row['MAX_DATE'];
	}
	unset($sql_date_result);

	/*echo "here"; echo "<pre>"; print_r($prodWiseSalesDataStatus); die;*/
	ob_start();
	$table_width = ($cbo_store_wise==1)?"2590":"2500";
	
	?>
	<style type="text/css">
		.word_wrap_break{
			word-wrap: break-word;
			word-break: break-all;
		}
	</style>

	<fieldset style="width:2480">
		<table cellpadding="0" cellspacing="0" width="1300">
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="24" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="24" style="font-size:16px">
					<strong>
						<?
						echo $company_arr[str_replace("'","",$cbo_company_id)];
						?>
					</strong>
				</td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="24" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>" class="rpt_table" align="left" >
			<thead>
				<tr>
					<th width="40"  rowspan="2">SL</th>
					<th width="80" rowspan="2">LC Company</th>
					<th width="100" rowspan="2">Buyer</th>
					<th width="100" rowspan="2">Style No</th>
					<th width="50" rowspan="2">Season</th>
					<th width="130" rowspan="2">Booking No</th>
					<th width="50" rowspan="2">FSO Year</th>
					<th width="130" rowspan="2">FSO</th>

					<th colspan="10">Fabric Details</th>
					<th colspan="4">Receive Details</th>
					<th colspan="3">Issue Details</th>
					<th colspan="3">Stock Details</th>
				</tr>
				<tr>
					<th width="70">Product ID</th>
					<th width="90">Construction</th>
					<th width="200">Composition</th>
					<th width="70">GSM</th>
					<th width="140">Color</th>
					<th width="70">Color Range</th>
					<th width="70">Stitch Length</th>
					<th width="80">F/Dia</th>

					<th width="90">Yarn Lot</th>
					<th width="90">Yarn Count</th>

					<th width="90">Opening</th>
					<th width="90">Recv. Qty.</th>
					<th width="90">Transf. In Qty.</th>
					<th width="90">Total Recv.</th>

					<th width="90">Issue Qty.</th>
					<th width="90">Transf. Out Qty.</th>
					<th width="90">Total Issue</th>

					<th width="90">Stock Qty.</th>
					<th width="50">Age(days)</th>
					<th>DOH</th>
				</tr>
			</thead>
		</table>
		<div style="width:<? echo $table_width+20; ?>px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>" class="rpt_table" id="tbl_list_search" align="left">
				<?
				$i=1;
				$tot_recv_qty=0;
				foreach($prodWiseSalesDataStatus as $poId=>$prodArr)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					foreach ($prodArr as $prodId=>$colorRange)
					{
						foreach ($colorRange as $crange=>$stitchLength)
						{
							$opening=$iss_qty=$trans_out_qty=0;
							foreach ($stitchLength as $slength=>$row)
							{
								$opening = ($row['OPENING_RCV']+$row['OPENING_TRANSIN'])-($opening_issue[$poId][$prodId][$crange][$slength]['issue_qty']+$openingTransOutQnty[$poId][$prodId][$crange][$slength]);

								$issue_return_qnty  = "0";
								$iss_qty 			= $knit_issue_arr[$poId][$prodId][$crange][$slength]['issue_qty'];
								$recv_tot_qty  = ($row['RECV_QNTY']+$issue_return_qnty+$row['TRANS_IN_QNTY']);
								$trans_out_qty = $transOutQnty[$poId][$prodId][$crange][$slength];
								
								$iss_tot_qty   = ($iss_qty+$trans_out_qty);

								$stock_qty 	   = $opening+($recv_tot_qty-$iss_tot_qty);
								//$stock_qty     = number_format($stock_qty,2,".","");
								if($stock_qty < .001)
								{
									$stock_qty = 0;
								}

								$daysOnHand = datediff("d",change_date_format($transaction_date_array[$poId][$prodId]['max_date'],'','',1),date("Y-m-d"));
								$ageOfDays 	= datediff("d",change_date_format($transaction_date_array[$poId][$prodId]['min_date'],'','',1),date("Y-m-d"));

								$counts="";
								$yarn_counts = array_unique(explode(",",chop($row['YARN_COUNT'],",")));
								foreach ($yarn_counts as $yarn_count) {
									$counts .= $yarn_count_arr[$yarn_count].",";
								}
								

								$yarn_lot = implode(",",array_filter(array_unique(explode(",", chop($row['YARN_LOT'],",")))));
								$COLOR_NO = implode(",",array_filter(array_unique(explode(",", chop($row["COLOR_NO"],",")))));

								if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stock_qty > $txt_qnty) || ($get_upto_qnty == 2 && $stock_qty < $txt_qnty) || ($get_upto_qnty == 3 && $stock_qty >= $txt_qnty) || ($get_upto_qnty == 4 && $stock_qty <= $txt_qnty) || ($get_upto_qnty == 5 && $stock_qty == $txt_qnty) || $get_upto_qnty == 0))
								{
									if($stock_qty > 0 && $cbo_value_with==2)
									{
										$tot_opening  		+= $opening;
										$tot_recv_qty 		+= $row['RECV_QNTY'];
										$tot_iss_ret_qty 	+= $issue_return_qnty;
										$tot_trans_in_qty 	+= $row['TRANS_IN_QNTY'];
										$grand_tot_recv_qty += $recv_tot_qty;

										$tot_iss_qty 		+= $iss_qty;
										$tot_rec_ret_qty 	+= $recv_ret_qty;
										$tot_trans_out_qty 	+= $trans_out_qty;
										$grand_tot_iss_qty 	+= ($iss_qty+$trans_out_qty);
										$grand_stock_qty 	+= $stock_qty;

										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">

											<td width="40" align="center"><?  echo $i; ?></td>
											<td width="80" class="word_wrap_break" class="word_wrap_break"><? echo $row["LC_COMPANY_NAME"]; ?></td>
											<td width="100" class="word_wrap_break"><? echo $row["BUYER_NAME"]; ?></td>
											<td width="100" class="word_wrap_break"><? echo $row["STYLE_NO"]; ?></td>
											<td width="50" class="word_wrap_break"><? echo $season; ?></td>
											<td width="130" class="word_wrap_break"><? echo $row["BOOKING_NO"]; ?></td>
											<td width="50" class="word_wrap_break"><? echo $row["FSO_YEAR"]; ?></td>
											<td width="130"title="<? echo $poId;?>" class="word_wrap_break"><? echo $row["FSO_NO"]; ?></td>

											<td width="70"><? echo $row["PRODUCT_ID"];?></td>
											<td width="90" class="word_wrap_break"><? echo $row["CONSTRUCTION"]; ?></td>
											<td width="200" class="word_wrap_break"><? echo $row["COMPOSITION"]; ?></td>
											<td width="70" align="center"><? echo $row["GSM"]; ?></td>
											<td width="140" align="center" class="word_wrap_break"><? echo $COLOR_NO; ?></td>
											<td width="70" class="word_wrap_break" title="<? echo $crange;?>"><? echo $row["COLOR_RANGE"];?></td>
											<td width="70" class="word_wrap_break"><? echo $row["STITCH_LENGTH"];?></td>
											<td width="80" align="center"><? echo $row["DIA_WIDTH"]; ?></td>

											<td width="90"><? echo $yarn_lot; ?></td>
											<td width="90"><? echo trim($counts,", "); ?></td>

											<td width="90" align="right" title="<? echo $opening_title;?>"><? echo ($opening==-0)?0:number_format($opening,2); ?></td>
											<td width="90" align="right"><? echo number_format($row['RECV_QNTY'],2); ?></td>
											
											<td width="90" align="right"><a href='#report_details' onClick="openmy_popup_page('<? echo $poId."_".$prodId."_".$detarmination_id."_".$data[1]."_".$crange."_".$slength; ?>','trans_in_popup');"><? echo number_format($row['TRANS_IN_QNTY'],2);?></a></td>
											<td width="90" align="right"><? echo number_format($recv_tot_qty,2); ?></td>

											<td width="90" align="right"><? echo number_format($iss_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($trans_out_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($iss_tot_qty,2); ?></td>

											<td width="90" align="right"><? echo number_format($stock_qty,2); ?></td>
											<td align="center" width="50"><? if($stock_qty>0) echo $ageOfDays; ?></td>
											<td align="center"><? if($stock_qty>0) echo $daysOnHand; ?></td>
										</tr>
										<?
										$i++;
									}
									else if($stock_qty>=0 && $cbo_value_with==1)
									{
										$tot_opening  		+= $opening;
										$tot_recv_qty 		+= $row['RECV_QNTY'];
										$tot_iss_ret_qty 	+= $issue_return_qnty;
										$tot_trans_in_qty 	+= $row['TRANS_IN_QNTY'];
										$grand_tot_recv_qty += $recv_tot_qty;

										$tot_iss_qty 		+= $iss_qty;
										$tot_rec_ret_qty 	+= $recv_ret_qty;
										$tot_trans_out_qty 	+= $trans_out_qty;
										$grand_tot_iss_qty 	+= ($iss_qty+$trans_out_qty);
										$grand_stock_qty 	+= $stock_qty;
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="40" align="center"><?  echo $i; ?></td>
											<td width="80" class="word_wrap_break"><? echo $row["LC_COMPANY_NAME"]; ?></td>
											<td width="100" class="word_wrap_break"><? echo $row["BUYER_NAME"]; ?></td>
											<td width="100" class="word_wrap_break"><? echo $row["STYLE_NO"]; ?></td>
											<td width="50" class="word_wrap_break"><? echo $season; ?></td>
											<td width="130" class="word_wrap_break"><? echo $row["BOOKING_NO"]; ?></td>
											<td width="50" class="word_wrap_break"><? echo $row["FSO_YEAR"]; ?></td>
											<td width="130" title="<? echo $poId;?>" class="word_wrap_break"><? echo $row["FSO_NO"]; ?></td>

											<td width="70"><? echo $row["PRODUCT_ID"];?></td>
											<td width="90" class="word_wrap_break"><? echo $row["CONSTRUCTION"]; ?></td>
											<td width="200" class="word_wrap_break"><? echo $row["COMPOSITION"]; ?></td>
											<td width="70" align="center"><? echo $row["GSM"]; ?></td>
											<td width="140" align="center" class="word_wrap_break"><? echo $COLOR_NO; ?></td>
											<td width="70" class="word_wrap_break" title="<? echo $crange;?>"><? echo $row["COLOR_RANGE"];?></td>
											<td width="70" class="word_wrap_break"><? echo $row["STITCH_LENGTH"];?></td>
											<td width="80" align="center"><? echo $row["DIA_WIDTH"]; ?></td>

											<td width="90"><? echo $yarn_lot; ?></td>
											<td width="90"><? echo trim($counts,", "); ?></td>

											<td width="90" align="right" title="<? echo $opening_title;?>"><? echo ($opening==-0)?0:number_format($opening,2); ?></td>
											<td width="90" align="right"><? echo number_format($row['RECV_QNTY'],2); ?></td>
											
											<td width="90" align="right"><a href='#report_details' onClick="openmy_popup_page('<? echo $poId."_".$prodId."_".$detarmination_id."_".$data[1]."_".$crange."_".$slength; ?>','trans_in_popup');"><? echo number_format($row['TRANS_IN_QNTY'],2);?></a></td>
											<td width="90" align="right"><? echo number_format($recv_tot_qty,2); ?></td>

											<td width="90" align="right"><? echo number_format($iss_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($trans_out_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($iss_tot_qty,2); ?></td>

											<td width="90" align="right"><? echo number_format($stock_qty,2); ?></td>
											<td align="center" width="50"><? if($stock_qty>0) echo $ageOfDays; ?></td>
											<td align="center"><? if($stock_qty>0) echo $daysOnHand; ?></td>
										</tr>
										<?
										$i++;
									}
								}
							}
						}
					}
				}
				?>
			</table>
		</div>
		<table width="<? echo $table_width; ?>"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"  align="left">
			<tfoot>
				<tr>
					<th width="40"></th>
					<th width="80"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="50"></th>
					<th width="130"></th>
					<th width="50"></th>
					<th width="130"></th>

					<th width="70"></th>
					<th width="90"></th>
					<th width="200"></th>
					<th width="70"></th>
					<th width="140"></th>
					<th width="70"></th>
					<th width="70"></th>
					<th width="80"></th>

					<th width="90"></th>
					<th width="90">Grand Total = </th>

					<th width="90" align="right" id="value_html_opening_qnty"><? echo number_format($tot_opening,2); ?></th>
					<th width="90" align="right" id="value_html_recv_qnty"><? echo number_format($tot_recv_qty,2); ?></th>
					<th width="90" align="right" id="value_html_trans_qty_in"><? echo number_format($tot_trans_in_qty,2); ?></th>
					<th width="90" align="right" id="value_html_total_recv"><? echo number_format($grand_tot_recv_qty,2); ?></th>

					<th width="90" align="right" id="value_html_issue_qty"><? echo number_format($tot_iss_qty,2); ?></th>
					<th width="90" align="right" id="value_html_trans_qty_out"><? echo number_format($tot_trans_out_qty,2); ?></th>
					<th width="90" align="right" id="value_html_toal_issue"><? echo number_format($grand_tot_iss_qty,2); ?></th>
					<th width="90" align="right" id="value_html_total_stock"><? echo number_format($grand_stock_qty,2); ?></th>
					<th width="50"></th>
					<th></th>
				</tr>
			</tfoot>
		</table>
	</fieldset>
	<?
	echo "Execution Time: " . (microtime(true) - $started) . "S"; // in seconds
	$html = ob_get_contents();
	ob_clean();

	foreach (glob("*.xls") as $filename) {
		@unlink($filename);
	}
    //---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html####$filename####$rpt_type";
	exit();
}

if($action == "trans_in_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$dataArr = explode("_", $data);
	$poId = $dataArr[0];
	$prodId = $dataArr[1];
	$detarmination_id = $dataArr[2];
	$gms = $dataArr[3];
	$color_range = $dataArr[4];
	$stitch_length = $dataArr[5];
	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$(".flt").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="380px";
			$(".flt").show();
		}

	</script>
	<fieldset style="width:90%; margin:auto;">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div style="width:95%; margin:auto;" id="report_container">
			<table border="1" class="rpt_table" rules="all" width="630" cellpadding="0" cellspacing="0" id="table_header">
				<caption>
					<b>Knit Grey Fabrics Transfer Info</b>
				</caption>
				<thead>
					<th width="30">SL</th>
					<th width="110">Transfer ID</th>
					<th width="80">Transfer Date</th>
					<th width="80">Transfer In Qty</th>
					<th width="50">Roll No</th>
					<th width="50">Program No</th>
					<th width="80">Barcode No</th>
					<th width="50">Rack No</th>
					<th width="">Shelf</th>
				</thead>

			</table>
			<div style="width:650px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="640" cellpadding="0" cellspacing="0" id="table_body">
					<tbody>
						<?
						$i=1;

						$mrr_sql="select a.transfer_system_id,a.transfer_date, b.to_rack as rack, b.to_shelf as self, c.barcode_no, c.roll_no, c.qnty,c.booking_no
						from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
						left join pro_roll_details d on c.barcode_no=d.barcode_no and d.entry_form=58 and d.status_active=1
						left join pro_grey_prod_entry_dtls e on d.dtls_id=e.id
						where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(133) and c.entry_form in(133) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.to_order_id='$poId' and b.from_prod_id=$prodId and e.stitch_length='$stitch_length' and e.color_range_id=$color_range order by a.id,b.id";// and e.color_range_id=$color_range

						$dtlsArray=sql_select($mrr_sql);
						foreach($dtlsArray as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="110"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
								<td width="80"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
								<td width="80" align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
								<td width="50" align="center"><p><? echo $row[csf('roll_no')]; ?></p></td>
								<td width="50" align="center"><p><? echo $row[csf('booking_no')]; ?></p></td>
								<td width="80"><p><? echo $row[csf('barcode_no')]; ?></p></td>
								<td width="50" align="center"><p><? echo $row[csf('rack')]; ?></p></td>
								<td align="center"><p><? echo $row[csf('self')]; ?></p></td>
							</tr>
							<?
							$tot_trans_qty+=$row[csf('qnty')];
							$i++;
						}
						?>
					</tbody>
				</table>
			</div>
			<table border="1" class="rpt_table" rules="all" width="630" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
					<th width="30"></th>
					<th width="110"></th>
					<th width="80">Total:</th>
					<th width="80"><? echo number_format($tot_trans_qty,2); ?></th>
					<th colspan="4" width="365"></th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<script>setFilterGrid('table_body',-1);</script>
	<?
	exit();
}

if($action == "trans_out_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$dataArr = explode("_", $data);
	$poId = $dataArr[0];
	$prodId = $dataArr[1];
	$detarmination_id = $dataArr[2];
	$gms = $dataArr[3];
	$color_range = $dataArr[4];
	$stitch_length = $dataArr[5];
	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$(".flt").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="380px";
			$(".flt").show();
		}
	</script>
	<fieldset style="width:620px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div style="width:100%" id="report_container">
			<table border="1" class="rpt_table" rules="all" width="630" cellpadding="0" cellspacing="0" id="table_header">
				<caption>
					<b>Knit Grey Fabrics Transfer Info</b>
				</caption>
				<thead>
					<th width="30">SL</th>
					<th width="110">Transfer ID</th>
					<th width="80">Transfer Date</th>
					<th width="80">Transfer Out Qty</th>
					<th width="50">Roll No</th>
					<th width="50">Program No</th>
					<th width="80">Barcode No</th>
					<th width="50">Rack No</th>
					<th width="">Shelf</th>
				</thead>

			</table>
			<div style="width:650px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="630" cellpadding="0" cellspacing="0" id="table_body">
					<tbody>
						<?
						$i=1;

						/*$mrr_sql= "select a.transfer_system_id,a.transfer_date, a.from_order_id, a.to_order_id, b.from_program,b.rack,b.shelf,b.roll, c.quantity, d.barcode_no,d.booking_no
						from inv_item_transfer_dtls b, inv_item_transfer_mst a, order_wise_pro_details c ,pro_roll_details d where a.id=b.mst_id and c.dtls_id=b.id and d.dtls_id = b.id and c.trans_type in(6) and a.item_category=13 and c.entry_form=133 and d.entry_form=133 and a.transfer_criteria=4 and a.from_order_id='$poId' and b.from_prod_id=$prodId and d.po_breakdown_id = a.to_order_id and d.roll_split_from=0
						and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
						order by a.id , b.id ";*/

						$mrr_sql= "select a.transfer_system_id,a.transfer_date, a.from_order_id, a.to_order_id, b.from_program,b.rack,b.shelf,b.roll, c.quantity, d.barcode_no,d.booking_no
						from inv_item_transfer_dtls b, inv_item_transfer_mst a, order_wise_pro_details c ,pro_roll_details d 
						left join pro_roll_details e on d.barcode_no=e.barcode_no and e.entry_form=58 and d.status_active=1 
						left join pro_grey_prod_entry_dtls f on e.dtls_id=f.id 
						where a.id=b.mst_id and c.dtls_id=b.id and d.dtls_id = b.id and c.trans_type in(6) and a.item_category=13 and c.entry_form=133 and d.entry_form=133 and a.transfer_criteria=4 and a.from_order_id='$poId' and b.from_prod_id=$prodId and d.po_breakdown_id = a.to_order_id and d.roll_split_from=0
						and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and f.stitch_length='$stitch_length' and f.color_range_id=$color_range
						order by a.id, b.id ";

						$dtlsArray=sql_select($mrr_sql);
						foreach($dtlsArray as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="110"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
								<td width="80"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
								<td width="80" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
								<td width="50" align="center"><p><? echo $row[csf('roll')]; ?></p></td>
								<td width="50" align="center"><p><? echo $row[csf('booking_no')]; ?></p></td>
								<td width="80"><p><? echo $row[csf('barcode_no')]; ?></p></td>
								<td width="50" align="center"><p><? echo $row[csf('rack')]; ?></p></td>
								<td align="center"><p><? echo $row[csf('self')]; ?></p></td>
							</tr>
							<?
							$tot_trans_qty+=$row[csf('quantity')];
							$i++;
						}
						?>
					</tbody>
				</table>
			</div>
			<table border="1" class="rpt_table" rules="all" width="630" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
					<th width="30"></th>
					<th width="110"></th>
					<th width="80">Total:</th>
					<th width="80"><? echo number_format($tot_trans_qty,2); ?></th>
					<th colspan="4" width="365"></th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<script>setFilterGrid('table_body',-1);</script>
	<?
	exit();
}

if($action == "transfer_in_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$companyID= $companyID;
	$fsoNo=$fsoNo;
	$deterId=$deterId;
	$gsm=$gsm;
	$popup_width=$popup_width;
	$type=$type;
	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$(".flt").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="380px";
			$(".flt").show();
		}

	</script>
	<fieldset style="width:90%; margin:auto;">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div style="width:95%; margin:auto;" id="report_container">
			<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" id="table_header">
				<thead>
					<th width="30">SL</th>
					<th width="110">From Sales Order </th>
					<th width="80">Booking No</th>
					<th width="80">Program No</th>
					<th width="80">Barcode No</th>
					<th width="">Transfer In Qty</th>
				</thead>
			</table>
			<div style="width:520px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" id="table_body">
					<tbody>
						<?
						$i=1;
						$trans_in_sql = "SELECT A.FROM_ORDER_ID, A.TO_ORDER_ID,B.TO_PROD_ID, D.QNTY AS TRANSFER_IN_QNTY,F.JOB_NO, D.BARCODE_NO, B.TO_PROGRAM
						FROM INV_ITEM_TRANSFER_MST A,INV_TRANSACTION E,INV_ITEM_TRANSFER_DTLS B,PRODUCT_DETAILS_MASTER C, PRO_ROLL_DETAILS D,FABRIC_SALES_ORDER_MST F
						WHERE  A.ID=E.MST_ID AND A.ID=D.MST_ID AND E.ID=B.TO_TRANS_ID AND B.ID=D.DTLS_ID AND B.FROM_PROD_ID=C.ID AND A.TO_ORDER_ID=F.ID AND F.JOB_NO in('$fsoNo') AND C.DETARMINATION_ID=$deterId AND C.GSM=$gsm AND A.ENTRY_FORM=133 AND A.TRANSFER_CRITERIA=4 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0 AND E.STATUS_ACTIVE=1 AND E.IS_DELETED=0 AND E.TRANSACTION_TYPE=5  AND D.ENTRY_FORM=133";
						// echo $trans_in_sql;
						$trans_in_sql_result = sql_select($trans_in_sql);
						$trnsInQtyArr = array();$transfer_in_barcode_qty_arr=array();
						foreach($trans_in_sql_result as $row )
						{
							$transfer_in_barcode_qty_arr[$row['BARCODE_NO']] += $row['TRANSFER_IN_QNTY'];
							$from_order_id_arr[$row['FROM_ORDER_ID']] = $row['FROM_ORDER_ID'];
						}
						$from_order_ids=implode(",", $from_order_id_arr);
						$fso_sql="SELECT ID, JOB_NO, SALES_BOOKING_NO FROM FABRIC_SALES_ORDER_MST WHERE id in($from_order_ids) and status_active=1 and is_deleted=0";
						// echo $fso_sql;
						$fso_sql_result = sql_select($fso_sql);
						foreach ($fso_sql_result as $key => $value) 
						{
							$job_arr[$value['ID']]['FROM_JOB']=$value['JOB_NO'];
							$job_arr[$value['ID']]['FROM_BOOKING']=$value['SALES_BOOKING_NO'];
						}

						//unset($trans_in_sql_result);
						foreach($trans_in_sql_result as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="110"><p><? echo $job_arr[$row['FROM_ORDER_ID']]['FROM_JOB']; ?></p></td>
								<td width="80"><p><? echo $job_arr[$row['FROM_ORDER_ID']]['FROM_BOOKING']; ?></p></td>
								<td width="80"><p><? echo $row['TO_PROGRAM']; ?></p></td>
								<td width="80"><p><? echo $row['BARCODE_NO']; ?></p></td>
								<td width="" align="right"><? echo number_format($row['TRANSFER_IN_QNTY'],2); ?></td>
							</tr>
							<?
							$tot_trans_qty+=$row['TRANSFER_IN_QNTY'];
							$i++;
						}
						?>
					</tbody>
				</table>
			</div>
			<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
					<th width="30"></th>
					<th width="110"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="80">Total:</th>
					<th width=""><? echo number_format($tot_trans_qty,2); ?></th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<script>setFilterGrid('table_body',-1);</script>
	<?
	exit();
}

if($action == "transfer_out_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$companyID= $companyID;
	$fsoNo=$fsoNo;
	$deterId=$deterId;
	$gsm=$gsm;
	$popup_width=$popup_width;
	$type=$type;
	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$(".flt").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="380px";
			$(".flt").show();
		}

	</script>
	<fieldset style="width:90%; margin:auto;">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div style="width:95%; margin:auto;" id="report_container">
			<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" id="table_header">
				<thead>
					<th width="30">SL</th>
					<th width="110">From Sales Order </th>
					<th width="80">Booking No</th>
					<th width="80">Program No</th>
					<th width="80">Barcode No</th>
					<th width="">Transfer Out Qty</th>
				</thead>
			</table>
			<div style="width:520px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" id="table_body">
					<tbody>
						<?
						$i=1;						
						$trans_out_sql = "SELECT A.FROM_ORDER_ID,B.FROM_PROD_ID, B.FROM_PROGRAM, D.QNTY AS TRANSFER_OUT_QNTY,F.JOB_NO, F.SALES_BOOKING_NO, D.BARCODE_NO
						FROM INV_ITEM_TRANSFER_MST A, INV_TRANSACTION E, INV_ITEM_TRANSFER_DTLS B, PRODUCT_DETAILS_MASTER C,PRO_ROLL_DETAILS D, FABRIC_SALES_ORDER_MST F
						WHERE A.ID=E.MST_ID AND E.ID=B.TRANS_ID AND B.ID=D.DTLS_ID AND B.FROM_PROD_ID=C.ID AND A.FROM_ORDER_ID=F.ID  AND A.ID=D.MST_ID AND B.ID=D.DTLS_ID AND F.JOB_NO in('$fsoNo') AND C.DETARMINATION_ID=$deterId AND C.GSM=$gsm AND A.STATUS_ACTIVE=1 AND A.ENTRY_FORM=133 AND A.TRANSFER_CRITERIA=4 AND E.TRANSACTION_TYPE=6 AND B.STATUS_ACTIVE=1 AND E.STATUS_ACTIVE=1 AND E.IS_DELETED=0 AND D.ENTRY_FORM=133 AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0";
						// echo $trans_out_sql;
						$trans_out_sql_result = sql_select($trans_out_sql);
						foreach($trans_out_sql_result as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="110"><p><? echo $row['JOB_NO']; ?></p></td>
								<td width="80"><p><? echo $row['SALES_BOOKING_NO']; ?></p></td>
								<td width="80"><p><? echo $row['FROM_PROGRAM']; ?></p></td>
								<td width="80"><p><? echo $row['BARCODE_NO']; ?></p></td>
								<td width="" align="right"><? echo number_format($row['TRANSFER_OUT_QNTY'],2); ?></td>
							</tr>
							<?
							$tot_trans_qty+=$row['TRANSFER_OUT_QNTY'];
							$i++;
						}
						?>
					</tbody>
				</table>
			</div>
			<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
					<th width="30"></th>
					<th width="110"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="80">Total:</th>
					<th width=""><? echo number_format($tot_trans_qty,2); ?></th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<script>setFilterGrid('table_body',-1);</script>
	<?
	exit();
}
?>