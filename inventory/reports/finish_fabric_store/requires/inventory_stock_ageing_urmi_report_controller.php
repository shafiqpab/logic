<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


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
if($action=="load_drop_down_buyer")
{
	$party="1,3,21,90";
	echo create_drop_down("cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in ($data) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) group by buy.id, buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
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
									<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $company_id; ?>'+'_'+document.getElementById('txt_booking_no').value,'create_booking_search_list_view', 'search_div', 'reference_wise_finish_stock_report_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
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
if($action=="report_generate")
{ 	
	?>
		<div style="text-align:center;"><h3>Inventory Stock Ageing Report</h3></div>

	<?
	$started = microtime(true);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));	
	
	
	$report_type 		= str_replace("'","",$cbo_report_type);
	$buyer_id 			= str_replace("'","",$cbo_buyer_id);
	$book_no 			= trim(str_replace("'","",$txt_book_no));
	$book_id 			= str_replace("'","",$txt_book_id);

	$booking_no 		= str_replace("'","",$txt_book_no);
	$booking_id 		= str_replace("'","",$txt_book_id);


	$job_no 			= trim(str_replace("'","",$txt_job_no));
	$txt_pi_no 			= trim(str_replace("'","",$txt_pi_no));
	$hdn_pi_id 			= trim(str_replace("'","",$hdn_pi_id));

	$txt_file_no 		= str_replace("'","",$txt_file_no);
	$txt_ref_no 		= str_replace("'","",$txt_ref_no);
	$job_year 			= str_replace("'","",$cbo_year);
	$cbo_company_id 	= str_replace("'","",$cbo_company_id);
	$company_name 		= str_replace("'","",$cbo_company_id);
	$cbo_pay_mode 		= str_replace("'","",$cbo_pay_mode);
	$cbo_supplier_id 	= str_replace("'","",$cbo_supplier_id);
	$cbo_store_name 	= str_replace("'","",$cbo_store_name);
	$date_from 		 	= str_replace("'","",$txt_date_from);
	$date_to 		 	= str_replace("'","",$txt_date_to);

	$txt_date_from 		= str_replace("'","",$txt_date_from);
	$txt_date_to 		= str_replace("'","",$txt_date_to);


	$cbo_value_with 	= str_replace("'","",$cbo_value_with);

	$get_upto 			= str_replace("'","",$cbo_get_upto);
	$txt_days 			= str_replace("'","",$txt_days);
	$get_upto_qnty 		= str_replace("'","",$cbo_get_upto_qnty);
	$txt_qnty 			= str_replace("'","",$txt_qnty);

	$company_arr 	= return_library_array("select id, company_name from lib_company where status_active=1","id","company_name");
	$supplier_arr 	= return_library_array("select id,short_name from lib_supplier where status_active=1","id","short_name");
	$booking_type_arr = array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");

	//this part for textile


	if($within_group==1)
	{
		if($buyer_id>0)
		{
			$buyer_id_cond=" and d.po_buyer=$buyer_id" ;
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		if($buyer_id>0)
		{
			$buyer_id_cond=" and d.buyer_id=$buyer_id" ;
		}
		else
		{
			$buyer_id_cond="";
		}
	}

	if($pocompany_id==0) $pocompany_cond=""; else $pocompany_cond="and a.company_id='$pocompany_id'";
	$date_cond="";
	if($date_from!="")
	{
		if($db_type==0)$start_date=change_date_format($date_from,"yyyy-mm-dd","");
		else if($db_type==2) $start_date=change_date_format($date_from,"","",1);

		$date_cond=" and d.program_date='$date_from'";
	}

	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	if ($program_no=="") $program_no_cond=""; else $program_no_cond=" and d.id in ($program_no) ";
	if(str_replace("'","",trim($txt_order_no))=="")
	{
		$po_cond="";
	}
	else
	{
		if(str_replace("'","",$hide_order_id)!="")
		{
			$po_id=str_replace("'","",$hide_order_id);
			$po_cond="and b.id in(".$po_id.")";
		}
		else
		{
			$po_number=trim(str_replace("'","",$txt_order_no))."%";
			$po_cond="and b.po_number like '$po_number'";
		}
	}

	if($db_type==0)
	{
		if($year_id!=0) $year_search_cond=" and year(a.insert_date)=$year_id"; else $year_search_cond="";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_search_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id"; else $year_search_cond="";
	}


	if ($order_no=='') $order_no_cond=""; else $order_no_cond="and d.job_no_prefix_num='$order_no'";
	$bookingdata_arr=array();
	$bookingIds="";
	$totRows=0;
	$bookingIds_cond="";
	$booking_cond="";

	if ($booking_no!="")
	{
		$booking_no_cond=" and d.sales_booking_no like '%$booking_no%'";
		if($within_group==1 || $year_id!=0 )
		{
			$booking_no_cond.=" and d.sales_booking_no like '%-".substr($year_id, -2)."-%'";
		}

	} else {
		$booking_no_cond="";
	}

	if($cbo_store_wise ==1)
	{
		$selectRcvStore = " a.store_id,";
		$selectTransStore = " b.to_store as store_id,";
		$selectTransOutStore = " b.from_store as store_id,";
		$groupByRcvStore = " a.store_id,";
		$groupByTransStore = " b.to_store,";
		$groupByTransOutStore = " b.from_store,";

		if($cbo_store_name)
		{
			$rcvStoreCond = " and e.store_id = $cbo_store_name";
			$TransStoreCond = " and b.to_store = $cbo_store_name";
		}
	}

	if($within_group>0)
	{
		$withinGroupCond = "and d.within_group=$within_group";
	}

	if($txt_date_from != "" && $txt_date_to != "")
	{
		$to_trans_date_cond = " and e.transaction_date <= '".$txt_date_to."'";
		$to_trans_date_cond2 = " and a.transaction_date <= '".$txt_date_to."'";
		$to_trans_date_cond3 = " and c.transaction_date <= '".$txt_date_to."'";
		$to_trans_date_cond4 = " and f.transaction_date <= '".$txt_date_to."'";
	}

	$con = connect();
	$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id ");
    oci_commit($con);

	$sql = "SELECT b.id,d.within_group, f.id as batch_id, f.batch_no,f.extention_no,1 as type, sum(g.batch_qnty) as batch_qty, min(a.receive_date) as mrr_date, a.company_id, c.po_breakdown_id,  b.body_part_id, b.fabric_description_id, $selectRcvStore b.uom, h.color as color_id,b.dia_width_type, b.width, b.gsm, (c.quantity) as quantity , sum(e.cons_amount) as amount,0 as is_transfered,0 as from_order_id, a.receive_basis, (e.order_amount) as order_amount, e.transaction_date
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, fabric_sales_order_mst d, inv_transaction e, pro_batch_create_mst f, pro_batch_create_dtls g, product_details_master h
	where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and c.trans_id=e.id and e.prod_id=h.id and b.batch_id=f.id and f.id=g.mst_id and CAST(g.po_id AS VARCHAR2(4000)) =b.order_id and g.body_part_id = b.body_part_id and d.sales_booking_no=f.booking_no and f.status_active=1 and g.status_active=1 and a.entry_form=225 and c.entry_form=225 and b.is_sales=1 and c.is_sales=1 and a.company_id = $company_name $withinGroupCond $order_no_cond $booking_no_cond $buyer_id_cond $rcvStoreCond $year_search_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_basis in (10,14) $to_trans_date_cond
	group by  b.id,d.within_group,f.id, f.batch_no,f.extention_no,a.company_id,c.po_breakdown_id, b.body_part_id,b.fabric_description_id, $groupByRcvStore b.uom, e.order_amount, h.color, b.dia_width_type, b.width, a.item_category, b.gsm,c.quantity, a.receive_basis, e.transaction_date
	union all
	select  b.id,d.within_group,f.id as batch_id, f.batch_no,f.extention_no,2 as type, sum(g.batch_qnty) as batch_qty, min(a.transfer_date) as mrr_date, a.company_id,a.to_order_id as po_breakdown_id, g.body_part_id,b.feb_description_id as fabric_description_id, $selectTransStore b.uom, c.color as color_id,b.dia_width_type, b.dia_width as width, b.gsm, (b.transfer_qnty) as quantity , sum(e.cons_amount) as amount,1 as is_transfered,a.from_order_id , 0 as receive_basis,  (e.order_amount) as order_amount, e.transaction_date
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, fabric_sales_order_mst d , inv_transaction e, pro_batch_create_mst f, pro_batch_create_dtls g, product_details_master c 
	where a.id=b.mst_id and a.to_order_id=d.id and b.to_trans_id=e.id and e.prod_id=c.id and b.to_batch_id=f.id and f.id=g.mst_id AND g.body_part_id = b.body_part_id and f.status_active=1 and g.status_active=1  and a.company_id=$company_name $withinGroupCond $order_no_cond $booking_no_cond $buyer_id_cond $TransStoreCond $year_search_cond and a.entry_form in(230) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $to_trans_date_cond
	group by  b.id,d.within_group,f.id,f.batch_no,f.extention_no,a.company_id,a.to_order_id, e.order_amount,b.from_prod_id, g.body_part_id, b.feb_description_id, $groupByTransStore b.uom, c.color, b.dia_width_type, b.dia_width, b.gsm,b.transfer_qnty,a.from_order_id, e.transaction_date
	order by uom,po_breakdown_id";
	//and d.id=f.sales_order_id 
	//echo $sql;//die;
	$nameArray=sql_select($sql);
	$ref_key="";$open=0;
	foreach($nameArray as $row)
	{
		$all_batch_id[$row[csf("batch_id")]]=$row[csf("batch_id")];
		$row[csf("prod_id")]=$row[csf("batch_id")]; //this is intensional so don't change it
		if($row[csf("quantity")] > 0)
		{
			$fso_id_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
			$prod_id_arr[$row[csf("prod_id")]] = $row[csf("prod_id")];
			if($cbo_store_wise ==1)
			{
				$sub_total_col_span = 22;
				$ref_key =$row[csf("company_id")]."**".$row[csf("batch_id")]."**".$row[csf("fabric_description_id")]."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$row[csf("body_part_id")]."**".$row[csf("dia_width_type")]."**".$row[csf("color_id")]."**".$row[csf("store_id")]."**".$row[csf("batch_no")]."**".$row[csf("extention_no")]."**".$row[csf("batch_qty")]."**".$row[csf("within_group")];
			}else{
				$sub_total_col_span = 21;
				$ref_key = $row[csf("company_id")]."**".$row[csf("batch_id")]."**".$row[csf("fabric_description_id")]."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$row[csf("body_part_id")]."**".$row[csf("dia_width_type")]."**".$row[csf("color_id")]."**".$row[csf("batch_no")]."**".$row[csf("extention_no")]."**".$row[csf("batch_qty")]."**".$row[csf("within_group")];
			}

			if( ($txt_date_from != "" && $txt_date_to != "") )
			{

				if(strtotime($row[csf("transaction_date")]) >= strtotime($txt_date_from) && strtotime($row[csf("transaction_date")]) <= strtotime($txt_date_to))
				{
					if($row[csf("type")] == 1)
					{
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["rcv_qnty"] += $row[csf("quantity")];
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["rcv_amount"] += $row[csf("order_amount")];
					}
					else
					{
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["trans_in_qnty"] += $row[csf("quantity")];
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["trans_in_amount"] += $row[csf("order_amount")];
					}

					if($source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] == "")
					{
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] = $row[csf("mrr_date")];
					}
					else
					{
						if($source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] > $row[csf("mrr_date")])
						{
							$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] = $row[csf("mrr_date")];
						}
					}
				}
				else
				{
					if(strtotime($row[csf("transaction_date")]) < strtotime($txt_date_from))
					{
						if($cbo_store_wise ==1)
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_rcv_qnty"] += $row[csf("quantity")];

							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_balance_amt"] += $row[csf("order_amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["rcv_in"] += $row[csf("order_amount")];
						}else{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_rcv_qnty"] += $row[csf("quantity")];

							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_balance_amt"] += $row[csf("order_amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["rcv_in"] += $row[csf("order_amount")];
						}

						if($row[csf("type")] == 1)
						{
							$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["opening"] += $row[csf("quantity")];
							$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["rcv_amount"] += 0;//$row[csf("order_amount")];
						}else{
							$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["trans_in_qnty"] = 0;
							$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["trans_in_amount"] = 0;
						}
					}

					if($source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] == "")
					{
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] = $row[csf("mrr_date")];
					}
					else
					{
						if($source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] > $row[csf("mrr_date")])
						{
							$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] = $row[csf("mrr_date")];
						}
					}
				}
			}
			else
			{
				if($row[csf("type")] == 1)
				{
					$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["rcv_qnty"] += $row[csf("quantity")];
					$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["rcv_amount"] += $row[csf("order_amount")];
				}else{
					$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["trans_in_qnty"] += $row[csf("quantity")];
					$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["trans_in_amount"] += $row[csf("order_amount")];
				}

				if($source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] == "")
				{
					$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] = $row[csf("mrr_date")];
				}
				else
				{
					if($source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] > $row[csf("mrr_date")])
					{
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] = $row[csf("mrr_date")];
					}
				}
			}
		}
	}

	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 888, 1,$all_batch_id, $empty_arr);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 889, 1,$fso_id_arr, $empty_arr);
	oci_commit($con);

	$all_batch_ids= implode(",", $all_batch_id);
	$batch_conds=" and mst_id in($all_batch_ids)";
	if($db_type==2 && count($all_batch_id)>999)
	{
		$batch_conds="";
		$chnk=array_chunk($all_batch_id, 999);
		foreach($chnk as $val)
		{
			$ids=implode(",", $val);
			if(!$batch_conds)$batch_conds.=" and ( mst_id in($ids) ";
			else $batch_conds.=" or  mst_id in($ids) ";
		}
		$batch_conds.=")";

	}
	$batch_sql="SELECT a.mst_id,  a.item_description,  a.batch_qnty, a.body_part_id FROM pro_batch_create_dtls a, GBL_TEMP_ENGINE b Where status_active=1 and a.mst_id=b.ref_val and b.user_id=$user_id and b.entry_form=888 "; //$batch_conds 
	foreach(sql_select($batch_sql) as $vals)
	{
		$items=explode(",",$vals[csf("item_description")]);
		$items_st=trim($items[0]);
		$batch_qnty_arr_new[$vals[csf("mst_id")]][$vals[csf("body_part_id")]][$items_st]+=$vals[csf("batch_qnty")];
	}
	/*echo "<pre>";
	print_r($batch_qnty_arr_new);die;*/


	$fso_id_arr = array_filter($fso_id_arr);
	if(!empty($fso_id_arr))
	{
		$fso_ids = implode(",", array_filter($fso_id_arr));
		$fsoCond = $all_fso_cond = "";
		$fsoCond2 = $all_fso_cond2 = "";
		$fsoCond3 = $all_fso_cond3 = "";
		if($db_type==2 && count($fso_id_arr)>999)
		{
			$fso_id_arr_chunk=array_chunk($fso_id_arr,999) ;
			foreach($fso_id_arr_chunk as $chunk_arr)
			{
				$fsoCond.=" a.id in(".implode(",",$chunk_arr).") or ";
				$fsoCond2.=" c.po_breakdown_id in(".implode(",",$chunk_arr).") or ";
				$fsoCond3.=" a.from_order_id in(".implode(",",$chunk_arr).") or ";
			}

			$all_fso_cond.=" and (".chop($fsoCond,'or ').")";
			$all_fso_cond2.=" and (".chop($fsoCond2,'or ').")";
			$all_fso_cond3.=" and (".chop($fsoCond3,'or ').")";
		}
		else
		{
			$all_fso_cond=" and a.id in($fso_ids)";
			$all_fso_cond2=" and c.po_breakdown_id in($fso_ids)";
			$all_fso_cond3=" and a.from_order_id in($fso_ids)";
		}

		$fso_ref_sql = sql_select("SELECT a.company_id,a.po_buyer,a.po_company_id,a.within_group, a.id as sales_id, a.job_no,a.season,a.sales_booking_no,a.style_ref_no,a.buyer_id,a.season,a.sales_booking_no,a.booking_type,a.booking_without_order,a.booking_entry_form, b.determination_id, b.gsm_weight, b.width_dia_type, b.dia, b.cons_uom, b.color_id, b.color_type_id, b.finish_qty, b.grey_qty from fabric_sales_order_mst a, fabric_sales_order_dtls b, GBL_TEMP_ENGINE c where a.id = b.mst_id and a.id=c.ref_val and c.user_id=$user_id and c.entry_form=889 and a.status_active =1 and b.status_active =1"); //$all_fso_cond

		$fso_ref_data_arr=array();$fso_ref_data=array();
		$booking_type_arr=array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
		foreach($fso_ref_sql as $row)
		{
			$fso_ref_qnty_type_arr[$row[csf('sales_id')]][$row[csf('determination_id')]][$row[csf('width_dia_type')]][$row[csf('color_id')]][$row[csf('cons_uom')]]['book_qnty'] +=$row[csf('finish_qty')];
			$fso_ref_qnty_type_arr[$row[csf('sales_id')]][$row[csf('determination_id')]][$row[csf('width_dia_type')]][$row[csf('color_id')]][$row[csf('cons_uom')]]['fso_qnty'] +=$row[csf('grey_qty')];
			$fso_ref_qnty_type_arr[$row[csf('sales_id')]][$row[csf('determination_id')]][$row[csf('width_dia_type')]][$row[csf('color_id')]][$row[csf('cons_uom')]]['color_type'] .=$row[csf('color_type_id')].",";

			$fso_ref_data[$row[csf('sales_id')]]["within_group"] = $row[csf('within_group')];
			$fso_ref_data[$row[csf('sales_id')]]["po_company_id"] = $row[csf('po_company_id')];

			if($row[csf('within_group')]==1)
			{
				$fso_ref_data[$row[csf('sales_id')]]["po_buyer"] = $row[csf('po_buyer')];
			}else {
				$fso_ref_data[$row[csf('sales_id')]]["po_buyer"] = $row[csf('buyer_id')];
			}

			$fso_ref_data[$row[csf('sales_id')]]["style_ref_no"] = $row[csf('style_ref_no')];
			$fso_ref_data[$row[csf('sales_id')]]["season"] = $row[csf('season')];
			$fso_ref_data[$row[csf('sales_id')]]["job_no"] = $row[csf('job_no')];
			$fso_ref_data[$row[csf('sales_id')]]["sales_booking_no"] = $row[csf('sales_booking_no')];

			if($row[csf('booking_type')] == 4)
			{
				if($row[csf('booking_without_order')] == 1)
				{
					$bookingType = "Sample Without Order";
				}
				else
				{
					$bookingType =  "Sample With Order";
				}
			}
			else
			{
				$bookingType =  $booking_type_arr[$row[csf('booking_entry_form')]];
			}

			$salesTypeData[$row[csf("sales_id")]]['booking_type'] = $bookingType;
		}

		unset($fso_ref_sql);

		$delivery_qnty_sql = sql_select("SELECT b.body_part_id bodypart_id,b.uom,b.width_type,sum(c.quantity) delivery_qnty, sum(a.order_amount) as amount, c.is_sales, c.po_breakdown_id, b.batch_id,d.detarmination_id determination_id,d.gsm,d.dia_width dia, $selectRcvStore d.color color_id, a.transaction_date
			from inv_finish_fabric_issue_dtls b,order_wise_pro_details c,product_details_master d, inv_transaction a, GBL_TEMP_ENGINE e
			where a.company_id=$company_name and b.id=c.dtls_id and c.prod_id=d.id and c.trans_id = a.id  $rcvStoreCond and b.status_active=1 and c.entry_form=224 and c.status_active=1 and a.status_active=1 $to_trans_date_cond2 and c.po_breakdown_id=e.ref_val and e.user_id=$user_id and e.entry_form=889 group by b.body_part_id,b.uom, b.width_type, c.is_sales, c.po_breakdown_id, b.batch_id,d.detarmination_id,d.gsm, $groupByRcvStore d.dia_width,d.color,a.transaction_date"); //$all_fso_cond2 

		foreach ($delivery_qnty_sql as $row)  
		{
			if( ($txt_date_from != "" && $txt_date_to != "") )
			{
				if(strtotime($row[csf("transaction_date")]) >= strtotime($txt_date_from) && strtotime($row[csf("transaction_date")]) <= strtotime($txt_date_to))
				{
					if($cbo_store_wise ==1)
					{
						$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["delivery_qnty"] += $row[csf("delivery_qnty")];
						$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["delivery_amount"] += $row[csf("amount")];
					}
					else
					{
						$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["delivery_qnty"] += $row[csf("delivery_qnty")];
						$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["delivery_amount"] += $row[csf("amount")];
					}
				}
				else
				{
					if(strtotime($row[csf("transaction_date")]) < strtotime($txt_date_from))
					{
						if($cbo_store_wise ==1)
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_iss_qnty"] += $row[csf("delivery_qnty")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_balance_amt"] -= $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["iss"] += $row[csf("amount")];
						}
						else
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_iss_qnty"] += $row[csf("delivery_qnty")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_balance_amt"] -= $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["iss"] += $row[csf("amount")];
						}
					}
				}
			}
			else
			{
				if($cbo_store_wise==1)
				{
					$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["delivery_qnty"] += $row[csf("delivery_qnty")];
					$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["delivery_amount"] += $row[csf("amount")];
				}
				else
				{
					$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["delivery_qnty"] += $row[csf("delivery_qnty")];
					$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["delivery_amount"] += $row[csf("amount")];
				}
			}
		}
		unset($delivery_qnty_sql);

		$issue_return_sql = sql_select("SELECT a.company_id, c.po_breakdown_id, b.prod_id, b.batch_id, b.body_part_id, b.fabric_description_id,  b.uom, f.color as color_id,b.dia_width_type, b.width, b.gsm, $selectRcvStore sum(c.quantity) as quantity , sum(e.order_amount) as amount, e.transaction_date from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, inv_transaction e, product_details_master f, GBL_TEMP_ENGINE d where a.id=b.mst_id and b.id=c.dtls_id and c.trans_id=e.id and e.prod_id=f.id and a.entry_form=233 and c.entry_form=233 and b.is_sales=1 and c.is_sales=1 and a.company_id=$company_name  $rcvStoreCond $to_trans_date_cond and c.po_breakdown_id=d.ref_val and d.user_id=$user_id and d.entry_form=889 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.company_id,c.po_breakdown_id, b.prod_id, b.batch_id, b.body_part_id, b.fabric_description_id,b.uom, f.color, $groupByRcvStore b.dia_width_type, b.width, a.item_category, b.gsm, e.transaction_date"); //$all_fso_cond2
		foreach ($issue_return_sql as $row)
		{
			if( ($txt_date_from != "" && $txt_date_to != "") )
			{
				if(strtotime($row[csf("transaction_date")]) >= strtotime($txt_date_from) && strtotime($row[csf("transaction_date")]) <= strtotime($txt_date_to))
				{
					if($cbo_store_wise ==1)
					{
						$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["quantity"] += $row[csf("quantity")];
						$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["amount"] += $row[csf("amount")];
					}
					else
					{
						$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["quantity"] += $row[csf("quantity")];
						$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["amount"] += $row[csf("amount")];
					}
				}
				else
				{
					if(strtotime($row[csf("transaction_date")]) < strtotime($txt_date_from))
					{
						if($cbo_store_wise ==1)
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_iss_ret_qnty"] += $row[csf("quantity")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_balance_amt"] += $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["iss_ret"] += $row[csf("amount")];
						}
						else
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_iss_ret_qnty"] += $row[csf("quantity")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_balance_amt"] += $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["iss_ret"] += $row[csf("amount")];
						}
					}
				}
			}
			else
			{
				if($cbo_store_wise ==1)
				{
					$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["quantity"] += $row[csf("quantity")];
					$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["amount"] += $row[csf("amount")];
				}
				else
				{
					$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["quantity"] += $row[csf("quantity")];
					$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["amount"] += $row[csf("amount")];
				}
			}
		}
		unset($issue_return_sql);

		$transfered_fabric_sql = sql_select("SELECT a.from_order_id as po_breakdown_id,b.batch_id, b.from_prod_id as prod_id, b.body_part_id, b.feb_description_id as fabric_description_id, $selectTransOutStore b.uom, d.color as color_id,b.fabric_shade,b.dia_width_type, b.dia_width as width, b.gsm, sum(b.transfer_qnty) as quantity, sum(c.order_amount) as amount, c.transaction_date
			from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c, product_details_master d, GBL_TEMP_ENGINE e
			where a.id=b.mst_id and b.trans_id = c.id and c.prod_id=d.id and c.transaction_type=6 and a.entry_form in(230) and a.company_id = $company_name and a.from_order_id=e.ref_val and e.user_id=$user_id and e.entry_form=889 $to_trans_date_cond3 and a.status_active =1 and a.is_deleted =0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0
			group by a.from_order_id, b.batch_id, b.from_prod_id, b.body_part_id, b.feb_description_id, $groupByTransOutStore b.uom, d.color, b.fabric_shade,b.dia_width_type, b.dia_width, b.gsm, c.transaction_date"); //$all_fso_cond3

		foreach ($transfered_fabric_sql as $row)
		{
			if( ($txt_date_from != "" && $txt_date_to != "") )
			{
				if(strtotime($row[csf("transaction_date")]) >= strtotime($txt_date_from) && strtotime($row[csf("transaction_date")]) <= strtotime($txt_date_to))
				{
					if($cbo_store_wise ==1)
					{
						$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["quantity"] += $row[csf("quantity")];

						$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["amount"] += $row[csf("amount")];
					}
					else
					{
						$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["quantity"] += $row[csf("quantity")];
						$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["amount"] += $row[csf("amount")];
					}

				}
				else
				{
					if(strtotime($row[csf("transaction_date")]) < strtotime($txt_date_from))
					{
						if($cbo_store_wise==1)
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_trans_out"] += $row[csf("quantity")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_balance_amt"] -= $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["out"] += $row[csf("amount")];
						}
						else
						{

							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_trans_out"] += $row[csf("quantity")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_balance_amt"] -= $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["out"] += $row[csf("amount")];
						}
					}
				}
			}
			else
			{
				if($cbo_store_wise ==1)
				{
					$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["quantity"] += $row[csf("quantity")];

					$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["amount"] += $row[csf("amount")];
				}
				else
				{
					$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["quantity"] += $row[csf("quantity")];
					$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["amount"] += $row[csf("amount")];
				}
			}
		}
		unset($transfered_fabric_sql);

		$rcv_return_sql = sql_select("SELECT c.po_breakdown_id, c.entry_form , c.quantity, c.is_sales, d.store_id, d.batch_id, e.detarmination_id, e.gsm, e.dia_width, d.body_part_id, d.width_type, e.color,d.uom, f.order_amount as amount, f.transaction_date
			from order_wise_pro_details c, inv_finish_fabric_issue_dtls d, product_details_master e, inv_transaction f, GBL_TEMP_ENGINE b
			where c.dtls_id = d.id and d.prod_id = e.id and c.trans_id=f.id and c.entry_form = 287 and c.po_breakdown_id=b.ref_val and b.entry_form=889 and b.user_id=$user_id $to_trans_date_cond4 and c.is_sales=1 and e.item_category_id =2 and c.status_active =1 and c.is_deleted = 0 and d.status_active =1 and d.is_deleted = 0 and f.status_active =1 and f.is_deleted = 0"); //$all_fso_cond2



		foreach ($rcv_return_sql as $row)
		{

			if( ($txt_date_from != "" && $txt_date_to != "") )
			{
				if(strtotime($row[csf("transaction_date")]) >= strtotime($txt_date_from) && strtotime($row[csf("transaction_date")]) <= strtotime($txt_date_to))
				{
					if($cbo_store_wise ==1)
					{
						$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["quantity"] += $row[csf("quantity")];
						$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["amount"] += $row[csf("amount")];
					}
					else
					{
						$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["quantity"] += $row[csf("quantity")];
						$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["amount"] += $row[csf("amount")];
					}
				}
				else
				{
					if(strtotime($row[csf("transaction_date")]) < strtotime($txt_date_from))
					{
						if($cbo_store_wise ==1)
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["open_rec_ret_qnty"] += $row[csf("quantity")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["open_balance_amt"] -= $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["rcv_ret"] += $row[csf("amount")];
						}
						else
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["open_rec_ret_qnty"] += $row[csf("quantity")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["open_balance_amt"] -= $row[csf("amount")];
							$opening_arr_amount_by_transaction[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["rcv_ret"] += $row[csf("amount")];
						}
					}
				}
			}
			else
			{
				if($cbo_store_wise ==1)
				{
					$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["quantity"] += $row[csf("quantity")];
					$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["amount"] += $row[csf("amount")];
				}
				else
				{
					$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["quantity"] += $row[csf("quantity")];
					$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["amount"] += $row[csf("amount")];
				}
			}
		}

		$prod_id_arr = array_filter($prod_id_arr);
		if(count($prod_id_arr)>0)
		{
			$prod_ids = implode(",", $prod_id_arr);
			$prodCond = $all_prod_id_cond = "";
			if($db_type==2 && count($prod_id_arr)>999)
			{
				$prod_id_arr_chunk=array_chunk($prod_id_arr,999) ;
				foreach($prod_id_arr_chunk as $chunk_arr)
				{
					$prodCond.=" b.pi_wo_batch_no in(".implode(",",$chunk_arr).") or ";
				}

				$all_prod_id_cond.=" and (".chop($prodCond,'or ').")";
			}
			else
			{
				$all_prod_id_cond=" and b.pi_wo_batch_no in($prod_ids)";
			}
		}

		$date_array=array();
		$dateRes_date="SELECT c.po_breakdown_id,b.pi_wo_batch_no, a.avg_rate_per_unit, min(b.transaction_date) as min_date, max(b.transaction_date) as max_date 
		from product_details_master a, inv_transaction b,order_wise_pro_details c, GBL_TEMP_ENGINE d
		where a.id=b.prod_id and b.id=c.trans_id and b.is_deleted=0 and b.status_active=1 and b.item_category=2 and b.transaction_type in (2,6) and c.trans_type in (2,6)
		and b.pi_wo_batch_no=d.ref_val and d.user_id=$user_id and d.entry_form=888
		group by c.po_breakdown_id,b.pi_wo_batch_no, a.avg_rate_per_unit "; //$all_prod_id_cond
		$result_dateRes_date = sql_select($dateRes_date);
		foreach($result_dateRes_date as $row)
		{
			if(!$row[csf("pi_wo_batch_no")])$row[csf("pi_wo_batch_no")]=0;
			$date_array[$row[csf("po_breakdown_id")]][$row[csf("pi_wo_batch_no")]]['min_date']=$row[csf("min_date")];
			$date_array[$row[csf("po_breakdown_id")]][$row[csf("pi_wo_batch_no")]]['max_date']=$row[csf("max_date")];
			$avg_rate_arr[$row[csf("pi_wo_batch_no")]] = $row[csf("avg_rate_per_unit")];
		}
	}

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array_deter=sql_select($sql_deter);
	foreach( $data_array_deter as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($data_array_deter);


	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id ");
	oci_commit($con);
	//echo "Here";die;

	ob_start();

					$i=1;
					$pp=1;
					foreach ($source_arr as $uom_id => $uom_data)
					{
						$uom_arr=array();
						$sub_rcv=$sub_trans_in=$sub_iss_ret=$sub_rcv_tot=$sub_rcv_amount=$sub_issue=$sub_issue_return=$sub_rcv_ret=$sub_tran_out=$sub_issue_tot=$sub_issue_amount=$sub_stock_qty=$sub_stock_amount=$sub_opening_qnty=0;
						foreach ($uom_data as $po_breakdown_id => $po_breakdown_data)
						{
							$y=1; $show_row_sub_total = false;
							$opening_balance_qnty=0;
							foreach ($po_breakdown_data as $prod_ref => $row)
							{
								$sales_prod_key_arr=explode("**", $prod_ref);
								$company_id = $sales_prod_key_arr[0];
								$prod_id = $sales_prod_key_arr[1];//here prod id is equal to batch id

								$fabric_description_id = $sales_prod_key_arr[2];
								$gsm = $sales_prod_key_arr[3];
								$width = $sales_prod_key_arr[4];
								$body_part_id = $sales_prod_key_arr[5];
								$dia_width_type = $sales_prod_key_arr[6];
								$color_id  = $sales_prod_key_arr[7];
								if($cbo_store_wise ==1)
								{
									$batch_no  =$sales_prod_key_arr[9];
									$ext_no  = $sales_prod_key_arr[10];
									//$batch_qty  = $sales_prod_key_arr[11];
									$within_group  = $yes_no[$sales_prod_key_arr[12]];
								}
								else
								{
									$batch_no  =$sales_prod_key_arr[8];
									$ext_no  = $sales_prod_key_arr[9];
									//$batch_qty  = $sales_prod_key_arr[10];
									$within_group  = $yes_no[$sales_prod_key_arr[11]];
								}
								
								
								$booking_qnty = $fso_ref_qnty_type_arr[$po_breakdown_id][$fabric_description_id][$dia_width_type][$color_id][$uom_id]['book_qnty'];
								$fso_qnty = $fso_ref_qnty_type_arr[$po_breakdown_id][$fabric_description_id][$dia_width_type][$color_id][$uom_id]['fso_qnty'];
								$color_type_id = $fso_ref_qnty_type_arr[$po_breakdown_id][$fabric_description_id][$dia_width_type][$color_id][$uom_id]['color_type'];

								$daysOnHand = datediff("d",$date_array[$po_breakdown_id][$prod_id]['max_date'],date("Y-m-d"));
								if(!$daysOnHand)$daysOnHand = datediff("d",$date_array[$po_breakdown_id][0]['max_date'],date("Y-m-d"));
								if($cbo_store_wise ==1)
								{
									$store_id  = $sales_prod_key_arr[8];
									$is_transfered  = $sales_prod_key_arr[9];
									$delivery_qnty = $delivery_qnty_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["delivery_qnty"];
									$delivery_amount = $delivery_qnty_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["delivery_amount"];

									$transferOutQnty =  $transfered_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["quantity"];
									$transferOutAmount =  $transfered_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["amount"];

									$rcv_ret_qnty = $rcv_ret_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["quantity"];
									$rcv_ret_amount = $rcv_ret_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["amount"];

									$issue_return_qnty = $issue_return_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["quantity"];
									$issue_return_amount = $issue_return_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["amount"];

									$opening_balance_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["open_rcv_qnty"];

									$opening_issue_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["open_iss_qnty"];

									$opening_trans_out_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["open_trans_out"];

									$opening_recv_rtn_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["open_rec_ret_qnty"];

									$open_iss_ret_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["open_iss_ret_qnty"];

									$opening_balance_amount = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["open_balance_amt"];


									//===amount for title===
									$opening_rcv_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["rcv_in"];
									$opening_issue_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["iss"];
									$opening_trans_out_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["out"];
									$opening_recv_rtn_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["rcv_ret"];
									$open_iss_ret_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["iss_ret"];
									//======


								}else{
									$is_transfered  = $sales_prod_key_arr[8];
									$delivery_qnty = $delivery_qnty_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["delivery_qnty"];
									$delivery_amount = $delivery_qnty_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["delivery_amount"];
									$transferOutQnty =  $transfered_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["quantity"];
									$transferOutAmount =  $transfered_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["amount"];

									$rcv_ret_qnty = $rcv_ret_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["quantity"];
									$rcv_ret_amount = $rcv_ret_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["amount"];

									$issue_return_qnty = $issue_return_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["quantity"];
									$issue_return_amount = $issue_return_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["amount"];

									$opening_balance_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["open_rcv_qnty"];
									$opening_issue_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["open_iss_qnty"];
									$opening_trans_out_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["open_trans_out"];
									$opening_recv_rtn_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["open_rec_ret_qnty"];
									$open_iss_ret_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["open_iss_ret_qnty"];

									$opening_balance_amount = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["open_balance_amt"];


									//===amount for title===
									$opening_rcv_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["rcv_in"];
									$opening_issue_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["iss"];
									$opening_trans_out_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["out"];
									$opening_recv_rtn_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["rcv_ret"];
									$open_iss_ret_amount = $opening_arr_amount_by_transaction[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id]["iss_ret"];
									//======
								}

								$total_rcv_qnty = $row['rcv_qnty']+$issue_return_qnty+$row['trans_in_qnty'];

								$rcv_amount = $row['rcv_amount'] + $issue_return_amount + $row['trans_in_amount'];
								if($total_rcv_qnty > 0)
								{
									$rcv_avg_rate = $rcv_amount/$total_rcv_qnty;
								}else{
									$rcv_avg_rate = 0;
								}


								$total_issue_qnty = $delivery_qnty+$rcv_ret_qnty+ $transferOutQnty;
								$issue_amount = $delivery_amount+$rcv_ret_amount+ $transferOutAmount;
								if($total_issue_qnty>0)
								{
									$issue_avg_rate = $issue_amount/$total_issue_qnty;
								}else{
									$issue_avg_rate = 0;
								}

								$opening_bal = ($opening_balance_qnty+$open_iss_ret_qnty)-($opening_issue_qnty+$opening_trans_out_qnty+$opening_recv_rtn_qnty);
								$opening_title = "Receive=$opening_balance_qnty,Issue Return=$open_iss_ret_qnty\n Issue=$opening_issue_qnty,Trans. Out=$opening_trans_out_qnty";
								//$opening_title .= "<br><br>Receive amount=$opening_rcv_amount,Iss Ret amount=$open_iss_ret_amount\n Iss amount=$opening_issue_amount,Trans. Out amount=$opening_trans_out_amount";

								$total_stock_qty =  $opening_bal + ($total_rcv_qnty-$total_issue_qnty);
								if($user_id != 276){
									$total_stock_qty = ($total_stock_qty>0)?$total_stock_qty:0.00;
								}

								$total_stock_amount = ($opening_balance_amount + $rcv_amount) - $issue_amount;
								$total_stock_amount = ($total_stock_amount>0)?$total_stock_amount:0.00;
								if($total_stock_qty>0)
								{
									$total_stock_avg_rate = $total_stock_amount/$total_stock_qty;
								}

								$color_type_ids="";
								$color_type_arr =  array_filter(array_unique(explode(",",chop($color_type_id,","))));
								foreach ($color_type_arr as $val)
								{
									if($color_type_ids == "") $color_type_ids = $color_type[$val]; else $color_type_ids .= ", ". $color_type[$val];
								}

								if ((($cbo_get_upto_qnty == 1 && $total_stock_qty > $txt_qnty) || ($cbo_get_upto_qnty == 2 && $total_stock_qty < $txt_qnty) || ($cbo_get_upto_qnty == 3 && $total_stock_qty >= $txt_qnty) || ($cbo_get_upto_qnty == 4 && $total_stock_qty <= $txt_qnty) || ($cbo_get_upto_qnty == 5 && $total_stock_qty == $txt_qnty) || $cbo_get_upto_qnty == 0) && (($cbo_get_upto == 1 && $daysOnHand > $txt_days) || ($cbo_get_upto == 2 && $daysOnHand < $txt_days) || ($cbo_get_upto == 3 && $daysOnHand >= $txt_days) || ($cbo_get_upto == 4 && $daysOnHand <= $txt_days) || ($cbo_get_upto == 5 && $daysOnHand == $txt_days) || $cbo_get_upto == 0))
								{

									if($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$fabric_span = $details_row_span_arr[$uom_id."##".$po_breakdown_id];

									$pop_ref = $po_breakdown_id."__".$prod_id."__".$fabric_description_id."__".$gsm."__".$width."__".$body_part_id."__".$dia_width_type."__".$color_id."__".$uom_id;
									$transfered = ($is_transfered==1)?"<strong style='color:red'>[T]</strong>":"";

									$mrr_date = "";
									$mrr_date =$row['mrr_date'];
									$ageOfDays = datediff("d", $mrr_date, date("Y-m-d"));

									$dataArrTex[$company_id][$uom_id][$ageOfDays]+=$total_stock_qty;							
								}
							}							
						}
					}
	

		/*echo "<pre>";
		print_r($dataArrTex);
		echo "</pre>";*/
			
		$ageRangeTex=array(1=> "1-30", 2=> "31-60", 3=> "61-90", 4=> "91-120", 5=> "121-150", 6=> "150-180", 7=> "Above 180");

		foreach ($dataArrTex as $companyIDtex => $companyDataTex) 
		{
			foreach ($companyDataTex as $uomIDtex => $uomDataTex) 
			{
				foreach ($uomDataTex as $ageKeyTex => $valueTex) 
				{

					$comUomArrTex[$companyIDtex][$uomIDtex]=$uomIDtex;
					if($ageKeyTex<=30)
					{
						$mainArrTex[$companyIDtex][1][$uomIDtex]+=$valueTex;
						$mainArrTex2[$companyIDtex][1]+=$valueTex;
					}
					else if($ageKeyTex>30 && $ageKeyTex<=60)
					{
						$mainArrTex[$companyIDtex][2][$uomIDtex]+=$valueTex;
						$mainArrTex2[$companyIDtex][2]+=$valueTex;
					}
					else if($ageKeyTex>60 && $ageKeyTex<=90)
					{
						$mainArrTex[$companyIDtex][3][$uomIDtex]+=$valueTex;
						$mainArrTex2[$companyIDtex][3]+=$valueTex;
					}
					else if($ageKeyTex>90 && $ageKeyTex<=120)
					{
						$mainArrTex[$companyIDtex][4][$uomIDtex]+=$valueTex;
						$mainArrTex2[$companyIDtex][4]+=$valueTex;
					}
					else if($ageKeyTex>120 && $ageKeyTex<=150)
					{
						$mainArrTex[$companyIDtex][5][$uomIDtex]+=$valueTex;
						$mainArrTex2[$companyIDtex][5]+=$valueTex;
					}

					else if($ageKeyTex>150 && $ageKeyTex<=180)
					{
						$mainArrTex[$companyIDtex][6][$uomIDtex]+=$valueTex;
						$mainArrTex2[$companyIDtex][6]+=$valueTex;
					}
					else if($ageKeyTex>180)
					{
						$mainArrTex[$companyIDtex][7][$uomIDtex]+=$valueTex;
						$mainArrTex2[$companyIDtex][7]+=$valueTex;
					}
				}
			}
		}
		//sort($comUomArrTex);
		foreach ($mainArrTex2 as $companyIDsTex => $companyDatasTex) 
		{

			 ?>		<div style="height: 250px;width: auto;float: left;">
		            <table cellpadding="0" cellspacing="0" width="500" class="rpt_table" style="float: left; margin-right: 10px;margin-bottom: 10px;">
		               	<caption style="background-color:#f9f9f9; font-size: 16px; font-weight: bold;">Fabric Stock(<? if($companyIDsTex==0 || $companyIDsTex==""){ echo "Unknown Company";}else{echo $company_arr[$companyIDsTex];} ?>-Textile)</caption>
		               <thead>
		                  <th width="30">SL</th>
		                  <th width="100">Age Days</th>
		                  <?
		                 foreach ($comUomArrTex[$companyIDsTex] as $uomKeyTex => $uomNameTex) 
		     			 {
			                     ?>
			                     <th width="100">Fabric Stock(<? echo $unit_of_measurement[$uomNameTex]; ?>)</th>
			                     <?
		                  }
		                  ?>
		               </thead>
		              <tbody>
		              <?
		    $i=1; 
		    ksort($companyDatasTex);

			foreach ($companyDatasTex as $ageKeyRangeTex => $ageDataTex) 
			{ 
				?>
				<tr>
                     <td><? echo $i; ?></td>
                     <td><? echo $ageRangeTex[$ageKeyRangeTex]; ?></td>


                     <?
					foreach ($comUomArrTex[$companyIDsTex] as $uomKeyTex => $uomNameTex) 
     			 	{
     			 		$totalArr[$companyIDsTex][$uomKeyTex]+=$mainArrTex[$companyIDsTex][$ageKeyRangeTex][$uomKeyTex];
						?>
					 		<td align="right"><? echo $mainArrTex[$companyIDsTex][$ageKeyRangeTex][$uomKeyTex]; ?></td>
						<?	
					}
				$i++;

				?>
				 </tr>  
				 <?
			}
			?>
		 </tbody>
		 	<tfoot>
		 		<tr>
		 			<th></th>
		 			<th></th>
		 			<?
		 			 
						foreach ($comUomArrTex[$companyIDsTex] as $uomKeyTex => $uomNameTex) 
     			 		{
     			 			?>

		 					<th align="right"><? echo $totalArr[$companyIDsTex][$uomKeyTex]; ?></th>

		 					<?
		 				}
		 			
		 		?>
		 		</tr>
		 	</tfoot>
		   </table>
		</div>
			<?
		}


	//End this part for textile
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

	if($report_type==2)
	{
		$rcv_select = " b.floor_id, b.room, b.rack, b.self,";
		$rcv_group = " b.floor_id, b.room, b.rack, b.self,";
	}

	$rcv_sql = "SELECT b.id,e.booking_no, e.booking_no_id, e.booking_without_order, a.company_id,a.receive_basis, a.knitting_source,a.knitting_company,a.booking_id as wo_pi_prod_id,a.booking_no as wo_pi_prod_no, b.transaction_date, b.prod_id, b.store_id, $rcv_select c.body_part_id,c.fabric_description_id, c.gsm, c.width, f.color as color_id, b.cons_uom,listagg(c.dia_width_type,',') within group (order by c.dia_width_type) as dia_width_type, listagg(d.po_breakdown_id,',') within group (order by d.po_breakdown_id) as po_breakdown_id, b.cons_quantity as quantity,b.order_rate, b.order_amount, b.pi_wo_batch_no, a.lc_sc_no
	FROM inv_receive_master a, inv_transaction b, pro_finish_fabric_rcv_dtls c left join order_wise_pro_details d on c.trans_id = d.trans_id and d.entry_form=37 and d.po_breakdown_id <>0, pro_batch_create_mst e, product_details_master f
	WHERE a.company_id in ($cbo_company_id) and a.id = b.mst_id and b.id=c.trans_id and b.transaction_type=1 and a.entry_form=37 and a.status_active =1 and b.status_active =1 and c.status_active =1 and e.status_active=1 and b.pi_wo_batch_no=e.id and b.prod_id=f.id $store_cond $date_cond  $all_book_nos_cond $pi_no_cond
	group by b.id,e.booking_no,e.booking_no_id, e.booking_without_order, a.company_id,a.receive_basis, a.knitting_source,a.knitting_company, a.booking_id, a.booking_no, b.transaction_date, b.prod_id, b.store_id, $rcv_group c.body_part_id, c.fabric_description_id, c.gsm, c.width, f.color ,b.cons_uom,c.dia_width_type,b.cons_quantity, b.order_rate, b.order_amount, b.pi_wo_batch_no, a.lc_sc_no order by a.company_id"; //and e.booking_no in('UHM-Fb-21-00038','UHM-Fb-21-00032')
	//echo $rcv_sql;die;
	$rcv_data = sql_select($rcv_sql);
	foreach ($rcv_data as  $val)
	{
		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
		$ref_str="";
		$dia_width_type_ref = implode(",",array_unique(explode(",", $val[csf("dia_width_type")])));

		if($report_type==2)
		{
			$ref_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("fabric_description_id")]."*".$val[csf("gsm")]."*".$val[csf("width")]."*".$val[csf("color_id")]."*".$val[csf("cons_uom")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")];
		}
		else
		{
			$ref_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("fabric_description_id")]."*".$val[csf("gsm")]."*".$val[csf("width")]."*".$val[csf("color_id")]."*".$val[csf("cons_uom")];
		}

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
	print_r($data_array);die;*/

	if($report_type == 2)
	{
		$trans_in_select = " c.floor_id, c.room, c.rack, c.self,";
		$trans_in_group = " c.floor_id, c.room, c.rack, c.self,";
	}

	if ($hdn_pi_id=="")
	{
		$trans_in_sql = "SELECT c.transaction_date, c.pi_wo_batch_no, e.batch_no,e.booking_no, e.booking_no_id, e.booking_without_order, c.body_part_id, c.prod_id,c.store_id, $trans_in_select d.detarmination_id, d.gsm, d.dia_width as width, d.color as color_id, c.cons_uom, sum(c.cons_quantity) as quantity,c.order_rate, c.order_amount, listagg(f.po_breakdown_id,',') within group (order by f.po_breakdown_id) as po_breakdown_id
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c left join order_wise_pro_details f on c.id = f.trans_id and f.trans_type = 5 and f.status_active=1 and f.po_breakdown_id<>0, product_details_master d, pro_batch_create_mst e
		where a.id=b.mst_id and b.to_trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($cbo_company_id) and c.item_category=2 and c.transaction_type=5 and a.status_active=1 and b.status_active=1 and c.status_active=1 and e.status_active=1  and a.entry_form in (14,15,306) $store_cond_2 $date_cond_2 $all_book_nos_cond
		group by c.transaction_date, c.pi_wo_batch_no, e.batch_no,e.booking_no, e.booking_no_id, e.booking_without_order, c.company_id, c.body_part_id, c.prod_id,c.store_id, $trans_in_group d.detarmination_id, d.gsm, d.dia_width, d.color, c.cons_uom, c.order_rate, c.order_amount order by c.company_id";
		 //echo $trans_in_sql;die;
		$trans_in_data = sql_select($trans_in_sql);
		foreach ($trans_in_data as  $val)
		{

			$date_frm=date('Y-m-d',strtotime($start_date));
			$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
			$ref_str="";

			if($report_type == 2)
			{
				$ref_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("width")]."*".$val[csf("color_id")]."*".$val[csf("cons_uom")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")];
			}
			else
			{
				$ref_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("width")]."*".$val[csf("color_id")]."*".$val[csf("cons_uom")];
			}

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

	if(!empty($data_array))	
	{
		$con = connect();
		$r_id3=execute_query("delete from tmp_booking_no where userid=$user_id");
		$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id ");
		if($r_id3 && $r_id6)
		{
			oci_commit($con);
		}
	}

	$all_po_id_arr = array_filter($all_po_id_arr);
	$all_po_id_arr = array_unique(explode(",",implode(",", $all_po_id_arr)));
	if(!empty($all_po_id_arr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 990, 1,$all_po_id_arr, $empty_arr);//PO ID

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

			$com_arr[$val[csf("booking_no")]]["company_name"] 		= $val[csf("company_name")];
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
			$com_arr[$val[csf("booking_no")]]["company_name"] 		= $val[csf("company_id")];
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
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 991, 1,$batch_id_arr, $empty_arr);//PO ID
	}

	if($report_type == 2)
	{
		$issue_return_select = " c.floor_id, c.room, c.rack, c.self,";
	}

	$issRtnSql = "select c.transaction_date, d.knit_dye_source, b.body_part_id, b.prod_id,c.store_id, $issue_return_select b.fabric_description_id, b.gsm, b.width, f.color as color_id,c.cons_uom, c.cons_quantity as quantity, c.order_rate, b.batch_id, e.batch_no, e.booking_no, e.booking_without_order from inv_receive_master a, pro_finish_fabric_rcv_dtls b,inv_transaction c, inv_issue_master d, pro_batch_create_mst e, product_details_master f, GBL_TEMP_ENGINE g where a.id = b.mst_id and b.trans_id=c.id and c.issue_id=d.id and a.entry_form=52 and a.item_category=2 and c.pi_wo_batch_no = e.id and c.prod_id=f.id and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=991 and a.status_active =1 and b.status_active=1 and c.status_active =1 and c.company_id in  ($cbo_company_id) $store_cond_2 $date_cond_2 ";  //$all_batch_ids_cond
	$issRtnData = sql_select($issRtnSql);
	foreach ($issRtnData as $val)
	{
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

	if($report_type == 2)
	{
		$issue_select = " c.floor_id, c.room, c.rack, c.self,";
		$issue_group = " c.floor_id, c.room, c.rack, c.self,";
	}

	/*$issue_sql = sql_select("select a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, c.cons_uom, c.store_id, $issue_select c.cons_quantity, c.id as trans_id,c.transaction_date, d.detarmination_id, d.gsm, d.dia_width, d.color, e.booking_no, e.booking_without_order, round(g.order_rate,2) as order_rate from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c  left join inv_mrr_wise_issue_details f on c.id = f.issue_trans_id and f.entry_form=18 and f.status_active =1 left join inv_transaction g on f.recv_trans_id = g.id , product_details_master d, pro_batch_create_mst e  where a.id = b.mst_id and b.trans_id = c.id and c.prod_id = d.id and c.pi_wo_batch_no= e.id and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2 $all_batch_ids_cond and a.entry_form=18 and c.status_active=1 and b.status_active=1 and a.status_active=1 and c.item_category =2 and c.transaction_type =2 group by a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, c.cons_uom, c.store_id, $issue_group c.cons_quantity, c.id, c.transaction_date, d.detarmination_id, d.gsm, d.dia_width, d.color, e.booking_no, e.booking_without_order, round(g.order_rate,2)");*/

	$issue_sql = sql_select("select a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, c.cons_uom, c.store_id, $issue_select c.cons_quantity, c.id as trans_id,c.transaction_date, d.detarmination_id, d.gsm, d.dia_width, d.color, e.booking_no, e.booking_without_order, round(c.order_rate,2) as order_rate from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE g  where a.id = b.mst_id and b.trans_id = c.id and c.prod_id = d.id and c.pi_wo_batch_no= e.id and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2 and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=991 and a.entry_form=18 and c.status_active=1 and b.status_active=1 and a.status_active=1 and c.item_category =2 and c.transaction_type =2 group by a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, c.cons_uom, c.store_id, $issue_group c.cons_quantity, c.id, c.transaction_date, d.detarmination_id, d.gsm, d.dia_width, d.color, e.booking_no, e.booking_without_order, round(c.order_rate,2)");

	foreach ($issue_sql as $val)
	{
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
	print_r($issue_data);
	die;*/
	if($report_type == 2){
		$rcv_return_select = " c.floor_id, c.room, c.rack, c.self,";
	}

	$rcvRtnSql = sql_select("select c.transaction_date, c.company_id, c.prod_id, c.store_id, $rcv_return_select c.cons_quantity, c.cons_uom, d.detarmination_id, d.gsm, d.dia_width, d.color, e.booking_no, e.booking_without_order, b.body_part_id from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE g where a.id = b.mst_id and b.trans_id=c.id and a.entry_form =46 and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2 and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=991 and c.prod_id=d.id and c.pi_wo_batch_no=e.id and a.status_active =1 and b.status_active =1 and c.status_active =1");

	foreach ($rcvRtnSql as $val)
	{
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

	if($report_type == 2)
	{
		$trans_out_select = " c.floor_id, c.room, c.rack, c.self,";
	}

	$transOutSql = sql_select("select c.transaction_date,c.pi_wo_batch_no, e.batch_no,e.booking_no, e.booking_no_id, e.booking_without_order, c.body_part_id, c.prod_id, c.store_id, $trans_out_select d.detarmination_id, d.gsm, d.dia_width, d.color, c.cons_uom, c.cons_quantity,c.order_rate from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c,product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE g where a.id=b.mst_id and b.trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2  and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=991 and c.item_category=2 and c.transaction_type=6 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.entry_form in (14,15,306)");

	foreach ($transOutSql as $val)
	{
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

	//if($all_po_id_cond_2!="")
	if(!empty($all_po_id_arr))
	{
		//$consumption_sql = sql_select("select c.job_no,c.body_part_id,c.lib_yarn_count_deter_id, c.construction, c.composition, b.color_number_id, a.costing_per, sum(b.requirment) as requirment, count(b.gmts_sizes) as gmts_sizes from wo_pre_cost_mst a, wo_pre_cost_fabric_cost_dtls c, wo_pre_cos_fab_co_avg_con_dtls b where a.job_no = c.job_no and b.job_no=c.job_no and c.id = b.pre_cost_fabric_cost_dtls_id and c.fab_nature_id=2 and c.status_active =1 and b.status_active=1 $all_po_id_cond_2 group by c.job_no,c.body_part_id, c.lib_yarn_count_deter_id, c.construction, c.composition,b.color_number_id,a.costing_per");

		/*$consumption_sql = sql_select("SELECT c.job_no,c.body_part_id,c.lib_yarn_count_deter_id, c.construction, c.composition, b.color_number_id, a.costing_per,  sum(b.requirment) as requirment, count(b.gmts_sizes) as gmts_sizes 
		from wo_pre_cost_mst a, wo_pre_cost_fabric_cost_dtls c, wo_pre_cos_fab_co_avg_con_dtls b, GBL_TEMP_ENGINE g 
		where a.job_no = c.job_no and b.job_no=c.job_no and c.id = b.pre_cost_fabric_cost_dtls_id and c.fab_nature_id=2 and c.status_active =1 and b.status_active=1 and c.color_size_sensitive !=3 and b.po_break_down_id=g.ref_val and g.user_id=$user_id and g.entry_form=990 
		group by c.job_no,c.body_part_id, c.lib_yarn_count_deter_id, c.construction, c.composition,b.color_number_id, a.costing_per 
		union all 
		SELECT c.job_no,c.body_part_id,c.lib_yarn_count_deter_id, c.construction, c.composition, d.contrast_color_id as color_number_id, a.costing_per, sum(b.requirment) as requirment, count(b.gmts_sizes) as gmts_sizes 
		from wo_pre_cost_mst a, wo_pre_cost_fabric_cost_dtls c, wo_pre_cos_fab_co_avg_con_dtls b ,wo_pre_cos_fab_co_color_dtls d, GBL_TEMP_ENGINE g 
		where a.job_no = c.job_no and b.job_no=c.job_no and c.id = b.pre_cost_fabric_cost_dtls_id and c.id = d.pre_cost_fabric_cost_dtls_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and b.color_number_id= d.gmts_color_id and d.status_active=1 and c.color_size_sensitive=3 and c.fab_nature_id=2 and c.status_active =1 and b.status_active=1 and b.po_break_down_id=g.ref_val and g.user_id=$user_id and g.entry_form=990 
		group by c.job_no,c.body_part_id, c.lib_yarn_count_deter_id, c.construction, c.composition, d.contrast_color_id, a.costing_per");*/  //$all_po_id_cond_2

		foreach ($consumption_sql as $val)
		{
			if($val[csf("costing_per")] == 1){
				$multipy_with = 1;
			}elseif ($val[csf("costing_per")] == 2) {
				$multipy_with = 12;
			}elseif ($val[csf("costing_per")] == 3) {
				$multipy_with = .5;
			}elseif ($val[csf("costing_per")] == 4) {
				$multipy_with = .3333;
			}elseif ($val[csf("costing_per")] == 5) {
				$multipy_with = .25;
			}

			$consumption_arr[$val[csf("job_no")]][$val[csf("body_part_id")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("color_number_id")]] += $multipy_with*($val[csf("requirment")]/$val[csf("gmts_sizes")]);
		}
		unset($consumption_sql);
	}

    /*echo "<pre>";
    print_r($consumption_arr);
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
	$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id ");
	if($r_id3 && $r_id6)
	{
		oci_commit($con);
	}
	
	/*echo "<pre>";
	print_r($data_array);
	die;*/
	
	ob_start();

		foreach ($data_array as $uom => $uom_data)
		{
			$uom_total_booking_qty=$uom_total_opening_qnty=$uom_total_recv_qnty=$uom_total_inside_return=$uom_total_outside_return=$uom_total_trans_in_qty=$uom_total_tot_receive=$uom_total_total_issue=$uom_total_total_issue_amount=$uom_total_stock_qnty=$uom_total_stock_amount=0;
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
					foreach ($ref_qnty_arr as $ref_qnty)
					{
						$ref_qnty = explode("*", $ref_qnty);
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
					}

					$po_number 	= implode(",",array_unique(explode(",",chop($po_array[$booking_no][$prodStr]["po_no"],","))));
					$pi_no 	= implode(",",array_unique(explode(",",chop($pi_no,","))));
					$lc_sc_no 	= implode(",",array_unique(explode(",",chop($lc_sc_no,","))));
					$prodStrArr 	= explode("*", $prodStr);

					//echo $booking_no.'<br>';
					$company_name 	= $book_po_ref[$booking_no]["company_name"];
					//$company_name 	= $com_arr[$booking_no]["company_name"];
					// echo $company_name.'<br>';
					$buyer_name 	= $book_po_ref[$booking_no]["buyer_name"];
					$supplier 		= $book_po_ref[$booking_no]["supplier"];
					$first_date 	= $book_po_ref[$booking_no]["fs_date"];
					$last_date 		= $book_po_ref[$booking_no]["ls_date"];
					$job_arr 		= array_filter(array_unique(explode(",",chop($book_po_ref[$booking_no]["job_no"],","))));
					$job_quantity 	= ""; $consump_per_dzn="";
					foreach ($job_arr as $job)
					{
						$job_quantity += $job_qnty_arr[$job]["qnty"];
						$consump_per_dzn += $consumption_arr[$job][$prodStrArr[2]][$prodStrArr[3]][$prodStrArr[6]];
					}
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

					$dia_width_type_arr = array_filter(array_unique(explode(",",chop($dia_width_types,","))));

					$dia_width_type="";
					foreach ($dia_width_type_arr as $width_type)
					{
						$dia_width_type .= $fabric_typee[$width_type].",";
					}
					$dia_width_type = chop($dia_width_type,",");

					$booking_qnty 	= $book_po_ref[$booking_no][$prodStrArr[2]][$prodStrArr[3]][$prodStrArr[6]]["qnty"];
					$booking_amount = $book_po_ref[$booking_no][$prodStrArr[2]][$prodStrArr[3]][$prodStrArr[6]]["amount"];
					if($booking_qnty >0){
						$booking_rate 	= $booking_amount/$booking_qnty;
					}else{
						$booking_rate=0;
					}

					$color_type_nos = implode(",",array_unique(explode(",",chop($book_po_ref[$booking_no][$prodStrArr[2]][$prodStrArr[3]][$prodStrArr[6]]["color_type"],","))));

					//echo $booking_no."=".$prodStrArr[2]."=".$prodStrArr[3]."=".$prodStrArr[6]."<br>";
					//$transOut_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")];

					if($report_type ==2)
					{
						$issRtnRef_str = $prodStrArr[0]."*".$prodStrArr[1]."*".$prodStrArr[2]."*".$prodStrArr[3]."*".$prodStrArr[4]."*".$prodStrArr[5]."*".$prodStrArr[6]."*".$prodStrArr[7]."*".$prodStrArr[8]."*".$prodStrArr[9]."*".$prodStrArr[10]."*".$prodStrArr[11];
					}
					else
					{
						$issRtnRef_str = $prodStrArr[0]."*".$prodStrArr[1]."*".$prodStrArr[2]."*".$prodStrArr[3]."*".$prodStrArr[4]."*".$prodStrArr[5]."*".$prodStrArr[6]."*".$prodStrArr[7];
					}
					
					//echo $booking_no."==".$issRtnRef_str."<br>";


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

					$booking_and_product_wise_quantity = $rate_arr_booking_and_product_wise[$booking_no][$prodStrArr[0]][$prodStrArr[1]]["quantity"];
					$booking_and_product_wise_amount = $rate_arr_booking_and_product_wise[$booking_no][$prodStrArr[0]][$prodStrArr[1]]["amount"];
					if($booking_and_product_wise_amount>0 && $booking_and_product_wise_quantity>0)
					{
						$booking_and_product_wise_rate = $booking_and_product_wise_amount/$booking_and_product_wise_quantity;
					}
					else
					{
						$booking_and_product_wise_rate = 0;
					}
					$tot_receive_rate =$booking_and_product_wise_rate;

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

					/*$stock_amount 	= $opening_amount + ($tot_receive_amount - $total_issue_amount);

					if($stock_qnty>0)
					{
						$stock_rate = $stock_amount/$stock_qnty;
					}*/

					if(number_format($stock_qnty,2,".","") == "-0.00")
					{
						$stock_qnty=0;
					}

					$stock_rate = $tot_receive_rate;
					$stock_amount = $stock_qnty * $stock_rate;

					$daysOnHand = datediff("d",change_date_format($transaction_date_array[$booking_no][$prodStrArr[0]]['max_date'],'','',1),date("Y-m-d"));
					$ageOfDays 	= datediff("d",change_date_format($transaction_date_array[$booking_no][$prodStrArr[0]]['min_date'],'','',1),date("Y-m-d"));

					//$possible_cut_piece = ($consump_per_dzn/12) * ($recv_qnty + $trans_in_qty);
					if(($consump_per_dzn/12) > 0)
					{
						$possible_cut_piece = $stock_qnty/($consump_per_dzn/12);
					}

					if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stock_qnty > $txt_qnty) || ($get_upto_qnty == 2 && $stock_qnty < $txt_qnty) || ($get_upto_qnty == 3 && $stock_qnty >= $txt_qnty) || ($get_upto_qnty == 4 && $stock_qnty <= $txt_qnty) || ($get_upto_qnty == 5 && $stock_qnty == $txt_qnty) || $get_upto_qnty == 0))
					{
						$dataArr[$company_name][$prodStrArr[7]][$ageOfDays]+=$stock_qnty;
					}
				}
			}
			
		}
		
	/*echo "<pre>";
	print_r($dataArr);
	echo "</pre>";*/
			
	$ageRange=array(1=> "1-30", 2=> "31-60", 3=> "61-90", 4=> "91-120", 5=> "121-150", 6=> "150-180", 7=> "Above 180");

	foreach ($dataArr as $companyID => $companyData) 
	{
		foreach ($companyData as $uomID => $uomData) 
		{
			foreach ($uomData as $ageKey => $value) 
			{

				$comUomArr[$companyID][$uomID]=$uomID;
				if($ageKey<=30)
				{
					$mainArr[$companyID][1][$uomID]+=$value;
					$mainArr2[$companyID][1]+=$value;
				}
				else if($ageKey>30 && $ageKey<=60)
				{
					$mainArr[$companyID][2][$uomID]+=$value;
					$mainArr2[$companyID][2]+=$value;
				}
				else if($ageKey>60 && $ageKey<=90)
				{
					$mainArr[$companyID][3][$uomID]+=$value;
					$mainArr2[$companyID][3]+=$value;
				}
				else if($ageKey>90 && $ageKey<=120)
				{
					$mainArr[$companyID][4][$uomID]+=$value;
					$mainArr2[$companyID][4]+=$value;
				}
				else if($ageKey>120 && $ageKey<=150)
				{
					$mainArr[$companyID][5][$uomID]+=$value;
					$mainArr2[$companyID][5]+=$value;
				}

				else if($ageKey>150 && $ageKey<=180)
				{
					$mainArr[$companyID][6][$uomID]+=$value;
					$mainArr2[$companyID][6]+=$value;
				}
				else if($ageKey>180)
				{
					$mainArr[$companyID][7][$uomID]+=$value;
					$mainArr2[$companyID][7]+=$value;
				}
			}
		}
	}

	/*echo "<pre>";
	print_r($mainArr2);
	echo "</pre>";*/

	foreach ($mainArr2 as $companyIDs => $companyDatas) 
	{

		 ?>		<div style="height: 250px;width: auto; float: left;">
	            <table cellpadding="0" cellspacing="0" width="500" class="rpt_table" style="float: left;margin-right: 10px;margin-bottom: 10px;">
	               <caption style="background-color:#f9f9f9; font-size: 16px; font-weight: bold;">Fabric Stock(<? if($companyIDs==0 || $companyIDs==""){ echo "Unknown Company";}else{echo $company_arr[$companyIDs];} ?>-Garments)</caption>
	               <thead>
	                  <th width="30">SL</th>
	                  <th width="100">Age Days</th>
	                  <?
	                 foreach ($comUomArr[$companyIDs] as $uomKey => $uomName) 
	     			 {
		                     ?>
		                     <th width="100">Stock(<? echo $unit_of_measurement[$uomName]; ?>)</th>
		                     <?
	                  }
	                  ?>
	               </thead>
	               <tbody>
	              <?
	    $i=1; 
	    ksort($companyDatas);
		foreach ($companyDatas as $ageKeyRange => $ageData) 
		{ 
			?>
			<tr>
                 <td><? echo $i; ?></td>
                 <td><? echo $ageRange[$ageKeyRange]; ?></td>


                 <?
				foreach ($comUomArr[$companyIDs] as $uomKey => $uomName) 
 			 	{
 			 		$totalArr[$companyIDs][$uomKey]+=$mainArr[$companyIDs][$ageKeyRange][$uomKey];
					?>
				 		<td align="right"><? echo $mainArr[$companyIDs][$ageKeyRange][$uomKey]; ?></td>
					<?
				}
		
			$i++;
			?>
			 </tr>  
			 <?
		}

		?>

	 </tbody>
	 		<tfoot>
		 		<tr>
		 			<th></th>
		 			<th></th>
		 			<?
						foreach ($comUomArr[$companyIDs] as $uomKey => $uomName) 
     			 		{
     			 			?>

		 					<th align="right"><? echo $totalArr[$companyIDs][$uomKey]; ?></th>

		 					<?
		 				}
		 			
		 		?>
		 		</tr>
		 	</tfoot>
	   </table>
	</div>

		<?
	}
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