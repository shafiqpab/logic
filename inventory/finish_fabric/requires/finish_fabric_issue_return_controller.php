<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=='roll_maintained')
{
	$cbo_company_name=$data;
	$variable_setting_production=return_field_value("fabric_roll_level","variable_settings_production","company_name='$cbo_company_name' and item_category_id=2 and variable_list=3 and status_active=1","fabric_roll_level");
	if($variable_setting_production==1)
	{
		echo "$('#roll_maintained').val($variable_setting_production);\n";
	}
	else
	{
		echo "$('#roll_maintained').val(0);\n";
	}

	$variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name='$data' and item_category_id=2 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	echo "document.getElementById('store_update_upto').value 	= '".$variable_inventory."';\n";
	exit();
}
if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/finish_fabric_issue_return_controller",$data);
}
if ($action=="load_drop_down_store")
{
	echo create_drop_down("cbo_store_name",170, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data' and b.category_type=2 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select store --", 0,"", 1);
	exit();
}

if($action=="load_drop_down_sewing_com")
{
	$data = explode("_",$data);
	$company_id=$data[1];

	if($data[0]==1)
	{
		echo create_drop_down( "cbo_sewing_company", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "--Select Sewing Company--", "$company_id", "","" );
	}
	else if($data[0]==3)
	{
		echo create_drop_down( "cbo_sewing_company", 150, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=21 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select Sewing Company--", 1, "" );
	}
	else
	{
		echo create_drop_down( "cbo_sewing_company", 150, $blank_array,"",1, "--Select Sewing Company--", 1, "" );
	}
	exit();
}



if($action=="mrr_popup")
{
	//echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(mrr)
		{
 		$("#hidden_recv_number").val(mrr); // mrr number
 		parent.emailwindow.hide();
 	}
 </script>

</head>

<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="750" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
				<thead>
					<tr>
						<th>Search By</th>
						<th align="center" id="search_by_td_up">Enter Issue Number</th>
						<th>Date Range</th>
						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
					</tr>
				</thead>
				<tbody>
					<tr class="general">
						<td>
							<?
							$search_by = array(1=>'Issue No',2=>'Challan No',3=>'Batch No',4=>'Job No',5=>'Style No',6=>'Internal Ref.');
							$dd="change_search_event(this.value, '0*0*0*0*0*0', '0*0*0*0*0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 120, $search_by,"",0, "--Select--", "",$dd,0 );
							?>
						</td>
						<td width="" align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td align="center">
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" />
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" />
						</td>
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_company_id; ?>, 'create_mrr_search_list_view', 'search_div', 'finish_fabric_issue_return_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td align="center" height="40" valign="middle" colspan="5">
							<? echo load_month_buttons(1);  ?>
							<!-- Hidden field here-->
							<input type="hidden" id="hidden_recv_number" value="" />
							<!-- -END-->
						</td>
					</tr>
				</tbody>
			</tr>
		</table>
		<div align="center" style="margin-top:10px" valign="top" id="search_div"> </div>
	</form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_mrr_search_list_view")
{
	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$fromDate = $ex_data[2];
	$toDate = $ex_data[3];
	$company = $ex_data[4];

	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==1) // for mrr
		{
			$sql_cond .= " and a.issue_number LIKE '%$txt_search_common'";
		}
		else if(trim($txt_search_by)==2) // for chllan no
		{
			$sql_cond .= " and a.challan_no LIKE '%$txt_search_common%'";
		}
 		else if(trim($txt_search_by)==3) // for Batch no
 		{
 			$sql_cond .= " and d.batch_no LIKE '%$txt_search_common%'";
 		}

 	}

 	if( $fromDate!="" && $toDate!="" )
 	{
 		if($db_type==0)
 		{
 			$sql_cond .= " and a.issue_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
 		}
 		else
 		{
 			$sql_cond .= " and a.issue_date  between '".change_date_format($fromDate,'','',1)."' and '".change_date_format($toDate,'','',1)."'";
 		}
 	}
 	if(trim($company)!="") $sql_cond .= " and a.company_id='$company'";

 	if($db_type==0) $year_field="YEAR(a.insert_date) as year";
 	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
 	else $year_field="";

 	if((trim($txt_search_by)==4 || trim($txt_search_by)==5 || trim($txt_search_by)==6) &&  trim($txt_search_common)!="")
 	{
 		if(trim($txt_search_by)==4 )
 		{
 			$job_cond = " and a.job_no LIKE '%$txt_search_common%'";
 		}
 		else if(trim($txt_search_by)==5)
 		{
 			$style_cond = " and a.style_ref_no LIKE '%$txt_search_common%'";
 		}
 		else if(trim($txt_search_by)==6)
 		{
 			$internal_ref_cond = " and b.grouping LIKE '%$txt_search_common%'";
 		}
 		$job_sql = sql_select("select a.job_no, a.style_ref_no,a.buyer_name,b.id as po_id from wo_po_details_master a, wo_po_break_down b where a.job_no = b.job_no_mst $job_cond $style_cond $internal_ref_cond");
 		foreach ($job_sql as $val)
 		{
 			$po_id_arr[$val[csf("po_id")]] = $val[csf("po_id")];
 		}

 		$po_id_arr = array_filter($po_id_arr);
 		$all_po_ids = implode(",", $po_id_arr);
 		$poCond=""; $all_po_id_cond="";
 		if(count($po_id_arr)>0)
 		{
 			if($db_type==2 && count($po_id_arr)>999)
 			{
 				$po_id_arr_chunk=array_chunk($po_id_arr,999) ;
 				foreach($po_id_arr_chunk as $chunk_arr)
 				{
 					$chunk_arr_value=implode(",",$chunk_arr);
 					$poCond.="  c.po_breakdown_id in($chunk_arr_value) or ";
 				}

 				$all_po_id_cond.=" and (".chop($poCond,'or ').")";
 			}
 			else
 			{
 				$all_po_id_cond=" and c.po_breakdown_id in($all_po_ids)";
 			}
 		}
 		else{
 			echo "Data Not Found";
 			die;
 		}

 	}

 	if($db_type==0)
 	{
 		$order_list=" , group_concat(po_breakdown_id) as po_id";
 	}
 	else
 	{
 		$order_list=" , LISTAGG(cast(po_breakdown_id as VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY po_breakdown_id) as po_id";
 	}
 	if($db_type==0) $year_field="YEAR(a.insert_date) as year";
 	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
 	else $year_field="";

 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

 	if (trim($txt_search_by)==6 &&  trim($txt_search_common)!="") 
 	{
		$sql = "select 1 as type, a.id,a.issue_number_prefix_num,a.issue_number, a.issue_basis,a.issue_purpose, b.cons_uom,d.batch_no, null as buyer_id,
		$year_field, a.challan_no,a.issue_date,sum(b.cons_quantity) as issue_qnty $order_list
		from inv_transaction b,inv_issue_master a,order_wise_pro_details c,pro_batch_create_mst d
		where a.id=b.mst_id and a.entry_form in(18) and a.status_active=1 and b.id=c.trans_id and a.issue_purpose!=8 and b.transaction_type=2
		and b.status_active=1 and b.pi_wo_batch_no=d.id $sql_cond $all_po_id_cond
		group by a.id,a.issue_number_prefix_num,a.issue_number,a.issue_basis,a.issue_purpose,b.cons_uom, d.batch_no, a.challan_no,a.issue_date, a.insert_date";
 	}
 	else
 	{
	 	$sql = "select 1 as type, a.id,a.issue_number_prefix_num,a.issue_number, a.issue_basis,a.issue_purpose, b.cons_uom,d.batch_no, null as buyer_id,
	 	$year_field, a.challan_no,a.issue_date,sum(b.cons_quantity) as issue_qnty $order_list
	 	from inv_transaction b,inv_issue_master a,order_wise_pro_details c,pro_batch_create_mst d
	 	where a.id=b.mst_id and a.entry_form in(18) and a.status_active=1 and b.id=c.trans_id and a.issue_purpose!=8 and b.transaction_type=2
	 	and b.status_active=1 and b.pi_wo_batch_no=d.id $sql_cond $all_po_id_cond
	 	group by a.id,a.issue_number_prefix_num,a.issue_number,a.issue_basis,a.issue_purpose,b.cons_uom, d.batch_no, a.challan_no,a.issue_date, a.insert_date
	 	union all
	 	select 2 as type, a.id,a.issue_number_prefix_num,a.issue_number, a.issue_basis,a.issue_purpose, b.cons_uom,d.batch_no, e.buyer_id,
	 	$year_field, a.challan_no,a.issue_date,sum(b.cons_quantity) as issue_qnty , null as po_id
	 	from inv_transaction b,inv_issue_master a,pro_batch_create_mst d,wo_non_ord_samp_booking_mst e
	 	where a.id=b.mst_id and a.entry_form in(18) and a.status_active=1 and a.issue_purpose=8 and b.transaction_type=2 and b.status_active=1 and b.pi_wo_batch_no = d.id and d.booking_no=e.booking_no $sql_cond
	 	group by a.id,a.issue_number_prefix_num,a.issue_number,a.issue_basis,a.issue_purpose,b.cons_uom, d.batch_no, a.challan_no,a.issue_date, a.insert_date, e.buyer_id
	 	order by id";
	 	//and d.batch_against=3
	 }

 	//echo $sql;

 	$sql_result=sql_select($sql);

 	if(empty($sql_result))
 	{
 		echo "Data Not Found";
 		die;
 	}

 	foreach($sql_result as $selectResult)
 	{
 		$order_arr=array_filter(array_unique(explode(",",$selectResult[csf('po_id')])));
 		foreach($order_arr as $value)
 		{
 			$batch_po_arr[$value] = $value;
 		}
 	}

 	$batch_po_arr = array_filter($batch_po_arr);
 	$all_batch_po_ids = implode(",", $batch_po_arr);
 	$BpoCond=""; $all_batch_po_id_cond="";
 	if(count($batch_po_arr)>0)
 	{
 		if($db_type==2 && count($batch_po_arr)>999)
 		{
 			$batch_po_arr_chunk=array_chunk($batch_po_arr,999) ;
 			foreach($batch_po_arr_chunk as $chunk_arr)
 			{
 				$chunk_arr_value=implode(",",$chunk_arr);
 				$BpoCond.="  a.id in($chunk_arr_value) or ";
 			}

 			$all_batch_po_id_cond.=" and (".chop($BpoCond,'or ').")";
 		}
 		else
 		{
 			$all_batch_po_id_cond=" and a.id in($all_batch_po_ids)";
 		}

 		$po_arr=array();$batch_arr=array();
 		$po_data=sql_select("select a.id, a.job_no_mst,c.style_ref_no, c.buyer_name,a.grouping 
 			from wo_po_details_master c,wo_po_break_down a
 			where c.job_no=a.job_no_mst and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $file_cond $ref_cond $style_ref_cond $job_no_cond  $all_batch_po_id_cond");

 		$all_po_id='';
 		foreach($po_data as $row)
 		{
 			$po_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
 			$po_arr[$row[csf('id')]]['job_no']=$row[csf('job_no_mst')];
 			$po_arr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
 			$po_arr[$row[csf('id')]]['internal_ref']=$row[csf('grouping')];
 		}
 	}
 	?>
 	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="1270">
 		<thead>
 			<tr>
 				<th width="50">SL</th>
 				<th width="100">MRR No</th>
 				<th width="100">Year</th>
 				<th width="100">Batch No</th>
 				<th width="100">Buyer No</th>
 				<th width="100">Job No</th>
 				<th width="100">Style No</th>
 				<th width="100">Internal Ref.</th>
 				<th width="50">UOM</th>
 				<th width="100">Challan No</th>
 				<th width="100">Issue Date</th>
 				<th width="150">Issue Purpose</th>
 				<th>Issue Qnty</th>
 			</tr>
 		</thead>
 	</table>

 	<div style="width:1270px; overflow-y:scroll; max-height:280px;font-size:12px; overflow-x:hidden;" id="scroll_body">
 		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="1250" id="list_view">
 			<tbody>
 				<?
 				$i=1;

 				foreach($sql_result as $row)
 				{
 					if ($k%2==0)
 						$bgcolor="#E9F3FF";
 					else
 						$bgcolor="#FFFFFF";

 					$style_ref='';$job_no='';$buyer_ids = '';$internalRef = '';

 					$order_id=array_unique(explode(",",$row[csf('po_id')]));
 					foreach($order_id as $value)
 					{
 						if($buyer_ids=='') $buyer_ids=$po_arr[$value]['buyer_name']; else $buyer_ids.=",".$po_arr[$value]['buyer_name'];
 						if($style_ref=='') $style_ref=$po_arr[$value]['style']; else $style_ref.="**".$po_arr[$value]['style'];
 						if($job_no=='') $job_no=$po_arr[$value]['job_no']; else $job_no.="**".$po_arr[$value]['job_no'];
 						if($internalRef=='') $internalRef=$po_arr[$value]['internal_ref']; else $internalRef.="**".$po_arr[$value]['internal_ref'];
 					}
 					$style_refs=implode(",",array_unique(explode("**", $style_ref)));
 					$job_no=implode(",",array_unique(explode("**", $job_no)));
 					$internalRef=implode(",",array_unique(explode("**", $internalRef)));

 					if($row[csf('type')]==2)
 					{
 						$buyer_name = $buyer_arr[$row[csf('buyer_id')]];
 					}
 					else
 					{
 						$bayer_id_arr = array_filter(array_unique(explode(",", $buyer_ids)));
 						foreach ($bayer_id_arr as $val)
 						{
 							$buyer_name = $buyer_arr[$val];
 						}
 					}

 					?>
 					<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer;" onClick="js_set_value('<? echo $row[csf("id")]; ?>_<? echo $row[csf("issue_number")]; ?>_<? echo $row[csf("issue_purpose")]; ?>_<? echo $row[csf("challan_no")]; ?>')">
 						<td width="50" align="center"><p><? echo $i; ?></p></td>
 						<td width="100" align="center"><p><? echo $row[csf("issue_number_prefix_num")]; ?></p></td>
 						<td width="100" align="center"><p><? echo $row[csf("year")]; ?></p></td>
 						<td width="100" align="center"><p><? echo $row[csf("batch_no")]; ?></p></td>
 						<td width="100" align="center">
 							<p>
 								<?
 								echo $buyer_name;
 								?>
 							</p>
 						</td>
 						<td width="100" align="center"><p><? echo $job_no; ?></p></td>
 						<td width="100" align="center"><p><? echo $style_refs; ?></p></td>
 						<td width="100" align="center"><p><? echo $internalRef; ?></p></td>
 						<td width="50" align="center"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
 						<td width="100" ><p><? echo $row[csf("challan_no")]; ?></p></td>
 						<td width="100" align="center"><p><? if($row[csf("issue_date")]!="" && $row[csf("issue_date")]!="0000-00-00") echo change_date_format($row[csf("issue_date")]); ?></p></td>
 						<td width="150"><p><? echo $yarn_issue_purpose[$row[csf("issue_purpose")]]; ?></p></td>
 						<td align="right"><p><? echo number_format($row[csf("issue_qnty")],2,".",""); ?></p></td>
 					</tr>
 					<?
 					$i++;
 				}
 				?>
 			</tbody>
 		</table>
 	</div>
 	<?
 	exit();

 }

 if($action=="show_fabric_desc_listview")
 {
 	$mrr_no = $data;
 	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');
 	$batch_no_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');

 	$issue_rtn_array=array();
 	$issData=sql_select("select a.issue_id, a.prod_id, a.batch_id_from_fissuertn,b.fabric_shade, a.body_part_id, a.floor_id,a.room, a.rack, a.self,a.bin_box, sum(a.cons_quantity) as qnty, b.issue_dtls_id from inv_transaction a,pro_finish_fabric_rcv_dtls b where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and a.transaction_type=4 and a.issue_id=$mrr_no	and b.status_active=1 and b.is_deleted=0 group by a.issue_id, a.prod_id, a.batch_id_from_fissuertn,b.fabric_shade,a.body_part_id,a.floor_id,a.room,a.rack,a.self,a.bin_box, b.issue_dtls_id");
 	foreach($issData as $row)
 	{
 		$issue_rtn_array[$row[csf('issue_id')]][$row[csf('prod_id')]][$row[csf('batch_id_from_fissuertn')]][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]][$row[csf('issue_dtls_id')]]=$row[csf('qnty')];
 	}

 	$lib_room_rack_shelf_sql = "select b.company_id,b.location_id,b.store_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name floor_name,c.floor_room_rack_name room_name,d.floor_room_rack_name rack_name,e.floor_room_rack_name shelf_name,f.floor_room_rack_name bin_name from lib_floor_room_rack_dtls b
 	left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
 	left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0
 	left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0
 	left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
 	left join lib_floor_room_rack_mst f on b.bin_id=f.floor_room_rack_id and f.status_active=1 and f.is_deleted=0
 	where b.status_active=1 and b.is_deleted=0";
 	$lib_floor_arr=sql_select($lib_room_rack_shelf_sql);
 	foreach ($lib_floor_arr as $room_rack_shelf_row) {
 		$company  = $room_rack_shelf_row[csf("company_id")];
 		$floor_id = $room_rack_shelf_row[csf("floor_id")];
 		$room_id  = $room_rack_shelf_row[csf("room_id")];
 		$rack_id  = $room_rack_shelf_row[csf("rack_id")];
 		$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
 		$bin_id   = $room_rack_shelf_row[csf("bin_id")];

 		if($floor_id!="" && $room_id=="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
 			$lib_floor_arr[$company][$floor_id] = $room_rack_shelf_row[csf("floor_name")];
 		}

 		if($floor_id!="" && $room_id!="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
 			$lib_room_arr[$company][$floor_id][$room_id] = $room_rack_shelf_row[csf("room_name")];
 		}

 		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id=="" && $bin_id==""){
 			$lib_rack_arr[$company][$floor_id][$room_id][$rack_id] = $room_rack_shelf_row[csf("rack_name")];
 		}

 		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id==""){
 			$lib_shelf_arr[$company][$floor_id][$room_id][$rack_id][$shelf_id] = $room_rack_shelf_row[csf("shelf_name")];
 		}
 		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id!=""){
 			$lib_bin_arr[$company][$floor_id][$room_id][$rack_id][$shelf_id][$bin_id] = $room_rack_shelf_row[csf("bin_name")];
 		}
 	}

 	//$data_array=sql_select("select  a.id as issue_id,a.company_id, b.id dtls_id,b.batch_id as batch_id,a.company_id,b.fabric_shade,d.floor_id,d.room, d.rack, d.self,d.bin_box, b.order_id,b.body_part_id,b.gmt_item_id,sum(b.issue_qnty) as qnty, c.id as prod_id, c.product_name_details, c.current_stock, c.color, c.unit_of_measure,d.cons_rate from inv_issue_master a,inv_transaction d, inv_finish_fabric_issue_dtls b, product_details_master c where a.id=d.mst_id and d.id=b.trans_id and b.prod_id=c.id and a.id='$mrr_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.transaction_type=2 and d.item_category=2 group by a.id,a.company_id,b.id,b.batch_id, b.fabric_shade,d.rack,d.self,d.bin_box, b.order_id,b.body_part_id,b.gmt_item_id,c.id, c.product_name_details, c.current_stock, c.color, c.unit_of_measure, a.company_id,d.floor_id,d.room,d.cons_rate");

	$data_array=sql_select("SELECT a.id as issue_id,a.company_id, b.id as dtls_id,b.batch_id as batch_id,b.fabric_shade,d.floor_id,d.room, d.rack, d.self,d.bin_box, b.order_id,b.body_part_id, b.gmt_item_id,(b.issue_qnty) as qnty, (e.quantity) as prop_quantity, c.id as prod_id, c.product_name_details, c.current_stock, c.color, c.unit_of_measure,d.cons_rate, f.grouping	from inv_issue_master a, inv_transaction d left join order_wise_pro_details e on d.id=e.trans_id and e.entry_form=18 and e.status_active=1 left join wo_po_break_down f on e.po_breakdown_id=f.id, inv_finish_fabric_issue_dtls b, product_details_master c where a.id=d.mst_id and d.id=b.trans_id and b.prod_id=c.id and a.id='$mrr_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.transaction_type=2 and d.item_category=2");

	foreach($data_array as $row)
 	{
		$issue_string= $row[csf("issue_id")].'='.$row[csf("dtls_id")];
		$issue_data_array[$issue_string]['issue_id']=$row[csf("issue_id")];
		$issue_data_array[$issue_string]['company_id']=$row[csf("company_id")];
		$issue_data_array[$issue_string]['dtls_id']=$row[csf("dtls_id")];
		$issue_data_array[$issue_string]['batch_id']=$row[csf("batch_id")];
		$issue_data_array[$issue_string]['fabric_shade']=$row[csf("fabric_shade")];
		$issue_data_array[$issue_string]['floor_id']=$row[csf("floor_id")];
		$issue_data_array[$issue_string]['room']=$row[csf("room")];
		$issue_data_array[$issue_string]['rack']=$row[csf("rack")];
		$issue_data_array[$issue_string]['self']=$row[csf("self")];
		$issue_data_array[$issue_string]['bin_box']=$row[csf("bin_box")];
		$issue_data_array[$issue_string]['order_id']=$row[csf("order_id")];
		$issue_data_array[$issue_string]['body_part_id']=$row[csf("body_part_id")];
		$issue_data_array[$issue_string]['gmt_item_id']=$row[csf("gmt_item_id")];

		if($row[csf("prop_quantity")]!="")
		{
			$issue_data_array[$issue_string]['qnty']+=$row[csf("prop_quantity")];
		}
		else{
			$issue_data_array[$issue_string]['qnty']+=$row[csf("qnty")];
		}
		
		$issue_data_array[$issue_string]['prod_id']=$row[csf("prod_id")];
		$issue_data_array[$issue_string]['product_name_details']=$row[csf("product_name_details")];
		$issue_data_array[$issue_string]['current_stock']=$row[csf("current_stock")];
		$issue_data_array[$issue_string]['color']=$row[csf("color")];
		$issue_data_array[$issue_string]['unit_of_measure']=$row[csf("unit_of_measure")];
		$issue_data_array[$issue_string]['cons_rate']=$row[csf("cons_rate")];
		$issue_data_array[$issue_string]['grouping'].=$row[csf("grouping")].',';
	}
	
	/* echo "select  a.id as issue_id,a.company_id, b.id dtls_id,b.batch_id as batch_id,a.company_id,b.fabric_shade,d.floor_id,d.room, d.rack, d.self,d.bin_box, b.order_id,b.body_part_id,b.gmt_item_id,sum(b.issue_qnty) as qnty, c.id as prod_id, c.product_name_details, c.current_stock, c.color, c.unit_of_measure,d.cons_rate from inv_issue_master a,inv_transaction d, inv_finish_fabric_issue_dtls b, product_details_master c where a.id=d.mst_id and d.id=b.trans_id and b.prod_id=c.id and a.id='$mrr_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.transaction_type=2 and d.item_category=2 
	group by a.id,a.company_id,b.id,b.batch_id, b.fabric_shade,d.rack,d.self,d.bin_box, b.order_id,b.body_part_id,b.gmt_item_id,c.id, c.product_name_details, c.current_stock, c.color, c.unit_of_measure, a.company_id,d.floor_id,d.room,d.cons_rate"; */
 	?>
 	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="790">
 		<thead>
 			<th width="30">SL</th>
 			<th width="50">Prod. ID</th>
 			<th width="60">Batch No</th>
 			<th width="60">Body Part</th>
 			<th width="120">Fabric Description</th>
			<th width="50">UOM</th>
			<th width="60">Item Color</th>
			<th width="60">Int. Ref.</th>
 			<th width="50">Shade</th>
 			<th width="50">Floor</th>
 			<th width="50">Room</th>
 			<th width="50">Rack</th>
 			<th width="50">Shelf</th>
 			<th width="50">Bin</th>
 			<th width="60">Issue Qty</th>
 			<th width="60">Issue Return Qty</th>
 			<th>Balance</th>
 		</thead>
 		<tbody>
 			<?
 			$i=1;
 			foreach($issue_data_array as $key=>$row)
 			{
 				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
 				if($totalReturned=="") $totalReturned=0;
 				$iss_rtn_qnty=$issue_rtn_array[$row['issue_id']][$row['prod_id']][$row['batch_id']][$row['fabric_shade']][$row['body_part_id']][$row['floor_id']][$row['room']][$row['rack']][$row['self']][$row['bin_box']][$row['dtls_id']];

 				$balance=$row[csf('qnty')]-$iss_rtn_qnty;

 				$floor 		= $lib_floor_arr[$row["company_id"]][$row["floor_id"]];
 				$room 		= $lib_room_arr[$row["company_id"]][$row["floor_id"]][$row["room"]];
 				$rack_no	= $lib_rack_arr[$row["company_id"]][$row["floor_id"]][$row["room"]][$row["rack"]];
 				$shelf_no 	= $lib_shelf_arr[$row["company_id"]][$row["floor_id"]][$row["room"]][$row["rack"]][$row["self"]];
 				$bin_no 	= $lib_bin_arr[$row["company_id"]][$row["floor_id"]][$row["room"]][$row["rack"]][$row["self"]][$row["bin_box"]];

				$grouping = implode(",",array_unique(explode(",",chop($row["grouping"],','))));

 				?>
 				<tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $row['batch_id']."**".$row['issue_id']."**".$row['prod_id']."**".$row['product_name_details']."**".$row['rack']."**".$row['self']."**".number_format($row['qnty'],2,".","")."**".number_format($iss_rtn_qnty,2,".","")."**".number_format($row['current_stock'],2,".","")."**".$color_arr[$row['color']]."**".$row['order_id']."**".$row['unit_of_measure']."**".$row['company_id']."**".$row['floor_id']."**".$row['room']."**".$row['color']."**".$row['fabric_shade']."**".$row['dtls_id']."**".$row['body_part_id']."**".$row['gmt_item_id']."**".$row['cons_rate']."**".$row['bin_box'];?>")' style="cursor:pointer" >
 					<td align="center"><? echo $i; ?></td>
 					<td align="center"><p><? echo $row['prod_id']; ?></p></td>
 					<td><p><? echo $batch_no_arr[$row['batch_id']]; ?></p></td>
 					<td><p><? echo $body_part[$row['body_part_id']]; ?></p></td>
 					<td><p><? echo $row['product_name_details']; ?></p></td>
					<td align="center"><p><? echo $unit_of_measurement[$row['unit_of_measure']]; ?></p></td>
					<td><p><? echo $color_arr[$row['color']]; ?></p></td>
					<td><p><? echo $grouping ; ?></p></td>
 					<td align="center"><p><? echo $fabric_shade[$row['fabric_shade']]; ?></p></td>
 					<td align="center"><p><? echo $floor; ?></p></td>
 					<td align="center"><p><? echo $room; ?></p></td>
 					<td align="center"><p><? echo $rack_no; ?></p></td>
 					<td align="center"><p><? echo $shelf_no; ?></p></td>
 					<td align="center"><p><? echo $bin_no; ?></p></td>
 					<td align="right"><? echo number_format($row['qnty'],2,'.',''); ?></td>
 					<td align="right"><? echo number_format($iss_rtn_qnty,2,'.',''); ?></td>
 					<td align="right"><? echo number_format($balance,2,'.',''); ?></td>
 				</tr>
 				<?
 				$i++;
 			}
 			?>
 		</tbody>
 	</table>
 	<?
 	exit();
 }

 if($action=="floor_room_rack_shelf")
 {
 	die;
 	$data=explode("**",$data);
 	$lib_floor_arr=return_library_array( "select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.floor_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[2] and b.floor_id=$data[3] order by b.floor_id", "floor_id","floor_room_rack_name" );
 	$lib_room_arr=return_library_array( "select b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[2] and b.room_id=$data[4]  order by b.floor_id", "room_id","floor_room_rack_name" );
 	$lib_rack_arr=return_library_array( "select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[2] and b.rack_id=$data[5] order by b.floor_id", "rack_id","floor_room_rack_name" );
 	
 	$lib_shelf_arr=return_library_array( "select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[2] and b.shelf_id=$data[6] order by b.floor_id", "shelf_id","floor_room_rack_name" );
 	
 	$lib_bin_arr=return_library_array( "select b.bin_id,a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[2] and b.bin_id=$data[7] order by b.floor_id", "bin_id","floor_room_rack_name" );

 	$sql=sql_select("select batch_id,store_id,floor,room,rack_no,shelf_no from inv_finish_fabric_issue_dtls where status_active=1 and is_deleted=0 and mst_id=$data[1] and batch_id=$data[0]");

 	foreach($sql as $row)
 	{
 		/*echo "$('#txt_floor_name').val('".$lib_floor_arr[$row[csf('floor')]]."');\n";
 		echo "$('#txt_room_name').val('".$lib_room_arr[$row[csf('room')]]."');\n";
 		echo "$('#txt_rack_name').val('".$lib_rack_arr[$row[csf('rack_no')]]."');\n";
 		echo "$('#txt_shelf_name').val('".$lib_shelf_arr[$row[csf('shelf_no')]]."');\n";*/

 	}
 	exit();
 }

 if($action=="populate_details_from_data")
 {
 	$data=explode("**",$data);
 	$dtls_data=sql_select("select a.company_id, b.batch_id, b.store_id,b.floor,b.room,b.rack_no,b.shelf_no,b.bin_box from  inv_issue_master a,inv_finish_fabric_issue_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.mst_id=$data[1] and b.id=$data[2] and b.batch_id=$data[0]");

	echo "load_room_rack_self_bin('requires/finish_fabric_issue_return_controller*2', 'store','store_td', '".$dtls_data[0][csf("company_id")]."','',this.value);\n";
 	echo "$('#cbo_store_name').val(".$dtls_data[0][csf("store_id")].");\n";
 	//echo "$('#cbo_store_name').attr('disabled','disabled');\n";

	echo "load_room_rack_self_bin('requires/finish_fabric_issue_return_controller', 'floor','floor_td', '".$dtls_data[0][csf("company_id")]."','','".$dtls_data[0][csf("store_id")]."',this.value);\n";
		echo "document.getElementById('cbo_floor').value 					= '".$dtls_data[0][csf("floor")]."';\n";

	echo "load_room_rack_self_bin('requires/finish_fabric_issue_return_controller', 'room','room_td', '".$dtls_data[0][csf("company_id")]."','','".$dtls_data[0][csf("store_id")]."','".$dtls_data[0][csf("floor")]."',this.value);\n";
	echo "document.getElementById('cbo_room').value 					= '".$dtls_data[0][csf("room")]."';\n";

	echo "load_room_rack_self_bin('requires/finish_fabric_issue_return_controller', 'rack','rack_td', '".$dtls_data[0][csf("company_id")]."','','".$dtls_data[0][csf("store_id")]."','".$dtls_data[0][csf("floor")]."','".$dtls_data[0][csf("room")]."',this.value);\n";
	echo "document.getElementById('txt_rack').value 					= '".$dtls_data[0][csf("rack_no")]."';\n";

	echo "load_room_rack_self_bin('requires/finish_fabric_issue_return_controller', 'shelf','shelf_td', '".$dtls_data[0][csf("company_id")]."','','".$dtls_data[0][csf("store_id")]."','".$dtls_data[0][csf("floor")]."','".$dtls_data[0][csf("room")]."','".$dtls_data[0][csf("rack_no")]."',this.value);\n";
	echo "document.getElementById('txt_shelf').value 					= '".$dtls_data[0][csf("shelf_no")]."';\n";

	echo "load_room_rack_self_bin('requires/finish_fabric_issue_return_controller', 'bin','bin_td', '".$dtls_data[0][csf("company_id")]."','','".$dtls_data[0][csf("store_id")]."','".$dtls_data[0][csf("floor")]."','".$dtls_data[0][csf("room")]."','".$dtls_data[0][csf("rack_no")]."','".$dtls_data[0][csf("shelf_no")]."',this.value);\n";
	echo "document.getElementById('cbo_bin').value 					= '".$dtls_data[0][csf("bin_box")]."';\n";


 	echo "$('#hidden_batch_id').val(".$dtls_data[0][csf("batch_id")].");\n";
 	echo "$('#cbo_store_name').attr('disabled','disabled');\n";
 	echo "$('#cbo_floor').attr('disabled','disabled');\n";
 	echo "$('#cbo_room').attr('disabled','disabled');\n";
 	echo "$('#txt_rack').attr('disabled','disabled');\n";
 	echo "$('#txt_shelf').attr('disabled','disabled');\n";
 	echo "$('#cbo_bin').attr('disabled','disabled');\n";

 	$batch_no=return_field_value("batch_no","pro_batch_create_mst","id='".$dtls_data[0][csf("batch_id")]."'");
 	echo "$('#txt_batch_no').val('".$batch_no."');\n";
 	exit();
 }

 if($action=="return_po_popup")
 {
 	echo load_html_head_contents("Issue Return Info", "../../../", 1, 1,'','','');
 	extract($_REQUEST);

 	if($roll_maintained==1)
 	{
 		$table_width=700;
 	}
 	else
 	{
 		$table_width=600;
 	}
 	?>
 	<script>
 		function distribute_qnty(str)
		{
			if(str==1)
			{
				var total_balance_quantity=$('#total_balance_quantity').val()*1;
				var txt_prop_grey_qnty=$('#txt_prop_grey_qnty').val()*1;

				if(txt_prop_grey_qnty>total_balance_quantity)
				{
					alert("Return Qnty not available");
					$('#txt_prop_grey_qnty').val("");
					return;
				}
				var len=totalFinish=0;
				$("#pop_table tbody").find('tr').each(function()
				{
					len=len+1;
					var row_balance = $("#issueqnty_"+len).attr("placeholder")*1;
					var perc=(row_balance/total_balance_quantity)*100;
					var return_qnty=(perc*txt_prop_grey_qnty)/100;
					return_qnty = return_qnty.toFixed(2);
					$("#issueqnty_"+len).val(return_qnty);
				});
			}  
			else
			{
				$('#txt_prop_grey_qnty').val('');
				$("#pop_table tbody").find('tr').each(function()
				{ 
					$(this).find('input[name="issueqnty[]"]').val('');
				});
			}
		}

		//===Reject Qty====
		function distribute_qnty2(str)
		{
			if(str==1)
			{
				var tot_po_qnty=$('#tot_po_qnty').val()*1;
				var txt_reject_grey_qnty=$('#txt_prop_reject_qnty').val()*1;
				var tblRow = $("#tbl_list_search tr").length;
				var len=totalReject=0;
				$("#tbl_list_search").find('tr').each(function()
				{
					len=len+1;
					var txtOrginal=$(this).find('input[name="txtOrginal[]"]').val()*1;
					var txtIsSales=$(this).find('input[name="txtIsSales[]"]').val()*1;

					if(txtOrginal==0)
					{
						$(this).remove();
					}
					else
					{
						var po_qnty=$(this).find('input[name="txtPoQnty[]"]').val()*1;
						var perc=(po_qnty/tot_po_qnty)*100;
						var reject_qnty=(perc*txt_reject_grey_qnty)/100;

						totalReject = totalReject*1+reject_qnty*1;
						totalReject = totalReject.toFixed(2);
						if(tblRow==len)
						{
							var balance = txt_reject_grey_qnty-totalReject;
							if(balance!=0) reject_qnty=reject_qnty+(balance);
						}
						if(txtIsSales == 1)reject_qnty=txt_reject_grey_qnty;
						$(this).find('input[name="rejqetqnty[]"]').val(reject_qnty.toFixed(2));
					}

				});
			}
			else
			{
				$('#txt_prop_reject_qnty').val('');
				$("#tbl_list_search").find('tr').each(function()
				{
					$(this).find('input[name="rejqetqnty[]"]').val('');
				});
			}
		}

 		function js_set_value()
 		{
 			var table_legth=$('#pop_table tbody tr').length;
 			var break_qnty=break_roll=break_id="";
 			var tot_qnty=tot_reject_qnty=tot_roll=0;
 			for(var i=1; i<=table_legth; i++)
 			{
 				tot_qnty +=($("#issueqnty_"+i).val()*1);
 				tot_reject_qnty +=($("#rejqetqnty_"+i).val()*1);

 				if(break_qnty!="") break_qnty +="_";
 				break_qnty+=($("#poId_"+i).val()*1)+'**'+($("#issueqnty_"+i).val()*1)+'**'+($("#rejqetqnty_"+i).val()*1);

 				if(break_roll!="") break_roll +="_";
 				break_roll+=($("#poId_"+i).val()*1)+'**'+($("#roll_"+i).val()*1)+'**'+($("#issueqnty_"+i).val()*1)+'**'+($("#rejqetqnty_"+i).val()*1);

 				if(break_id!="") break_id +=",";
 				break_id+=($("#poId_"+i).val()*1);
 				if($("#roll_"+i).val()>0) tot_roll +=($("#roll_"+i).val()*1);

 			}

 			$("#tot_qnty").val(tot_qnty.toFixed(2));
 			$("#tot_reject_qnty").val(tot_reject_qnty);
 			$("#tot_roll").val(tot_roll);
 			$("#break_qnty").val(break_qnty);
 			$("#break_roll").val(break_roll);
 			$("#break_order_id").val(break_id);
 			$('#distribution_method').val( $('#cbo_distribiution_method').val());
 			parent.emailwindow.hide();
 		}

 		function fn_calculate(id)
 		{
 			var recv_qnty=($("#recevqnty_"+id).val()*1);
 			var cumu_qnty=($("#cumulativeIssue_"+id).val()*1);
 			var issue_qnty=($("#issueqnty_"+id).val()*1);
 			var hiddenissue_qnty=($("#hiddenissueqnty_"+id).val()*1);
 			if(((cumu_qnty*1)+(hiddenissue_qnty+issue_qnty-hiddenissue_qnty))>(recv_qnty*1))
 			{
 				alert("Return Quantity can not be greater than Issue Quantity.");
 				$("#issueqnty_"+id).val(0);
 			}
 		}
 	</script>
 </head>
 <body>
 	<div align="center" style="width:100%;" >
 		<div style="width:<? echo $width; ?>px; margin-top:10px" align="center">
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300" align="center">
				<thead>
					<th>Total Return Qnty</th>
					<th>Distribution Method</th>
				</thead>
				<tr class="general">
					<td><input type="text" name="txt_prop_grey_qnty" id="txt_prop_grey_qnty" class="text_boxes_numeric" value="<? if($prev_distribution_method==1) echo $txt_return_qnty; ?>" style="width:120px"  onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)" <? echo $disabled; ?>/></td>

					<td>
						<?
						$distribiution_method=array(1=>"Proportionately",2=>"Manually");
						echo create_drop_down( "cbo_distribiution_method", 160, $distribiution_method,"",0,"--Select--",$prev_distribution_method,"distribute_qnty(this.value);",$disable_drop_down );

						?>
					</td>
				</tr>
			</table>
		</div>
		<br>
 		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
 			<table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" id="pop_table">
 				<thead>
 					<tr>
 						<th width="140">Order No</th>
 						<th width="120">Issue Quantity</th>
 						<th width="120">Cumulative Return</th>
 						<?
 						if($roll_maintained==1)
 						{
 							?>
 							<th>Roll</th>
 							<?
 						}
 						?>
 						<th width="120">Return Quantity</th>
 						<th width="100">Reject Quantity</th>
 					</tr>
 				</thead>
 				<tbody>
 					<?
 					$cbo_company_name 	= str_replace("'","",$cbo_company_name);
 					$txt_issue_id 		= str_replace("'","",$txt_issue_id);
 					$txt_prod_id 		= str_replace("'","",$txt_prod_id);

 					$cbo_floor 		= str_replace("'","",$cbo_floor);
 					$cbo_room 		= str_replace("'","",$cbo_room);
 					$txt_rack 		= str_replace("'","",$txt_rack);
 					$txt_shelf 		= str_replace("'","",$txt_shelf);
 					$cbo_bin 		= str_replace("'","",$cbo_bin);
 					$cbo_store_name = str_replace("'","",$cbo_store_name);


 					$update_id 			= str_replace("'","",$update_id);
 					$txt_return_qnty 	= str_replace("'","",$txt_return_qnty);
 					$roll_maintained 	= str_replace("'","",$roll_maintained);
 					$break_qnty 		= explode("_",str_replace("'","",$break_qnty));
 					foreach ($break_qnty as $po_qnty_breakdown_row) 
 					{
 						$po_qnty_breakdown_arr = explode("**",$po_qnty_breakdown_row);
 						$po_qnty[$po_qnty_breakdown_arr[0]]=$po_qnty_breakdown_arr[1];
 						$reject_po_qnty[$po_qnty_breakdown_arr[0]]=$po_qnty_breakdown_arr[2];
 					}

 					// old data may not have issue_dtls_id in issue return id so condition is ommited rather use store, prod_id, batch_id room, rack, shelf, bin 
 					if($cbo_bin){ $bin_cond = " and b.bin_box=$cbo_bin"; }
 					$sql=sql_select("SELECT a.prod_id, a.po_breakdown_id, sum(a.quantity) as issue_qnty, b.mst_id 
 					from  order_wise_pro_details a, inv_transaction b 
 					where a.trans_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_name' and b.mst_id='$txt_issue_id' and b.prod_id='$txt_prod_id' and b.body_part_id='$cbo_body_part' and b.pi_wo_batch_no='$batch_id' and b.fabric_shade='$cbo_fabric_type' and b.store_id=$cbo_store_name and b.floor_id=$cbo_floor and b.room=$cbo_room and b.rack=$txt_rack and b.self=$txt_shelf $bin_cond and a.entry_form in(18) and b.transaction_type in(2) group by b.mst_id, a.prod_id, a.po_breakdown_id");

 					foreach($sql as $row)
 					{
 						$po_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
 					}

 					if(!empty($po_arr))
 					{
 						$po_no_arr=return_library_array("select id,po_number from wo_po_break_down where id in (".implode(",",$po_arr).")","id","po_number");

 						$update_id_cond = ($update_id!="")?" and a.id!=$update_id":"";
 						if($cbo_bin){ $bin_cond = " and a.bin_box=$cbo_bin"; }

 						//and a.body_part_id='$cbo_body_part'
 						$cumilitive_return_sql=sql_select("SELECT c.po_breakdown_id,sum(c.quantity) as cumu_qnty from inv_transaction a,order_wise_pro_details c where a.id=c.trans_id and c.status_active=1 and a.issue_id='$txt_issue_id' and a.prod_id=$txt_prod_id and a.pi_wo_batch_no ='$batch_id' and a.transaction_type=4 and a.store_id=$cbo_store_name and a.floor_id=$cbo_floor and a.room=$cbo_room and a.rack=$txt_rack and a.self=$txt_shelf $bin_cond and a.fabric_shade='$cbo_fabric_type' and a.body_part_id='$cbo_body_part' and c.po_breakdown_id in (".implode(",",$po_arr).") $update_id_cond group by c.po_breakdown_id");
 						
 						foreach ($cumilitive_return_sql as $cu_return_row) 
 						{
 							$cumilitive_return[$cu_return_row[csf("po_breakdown_id")]]=$cu_return_row[csf("cumu_qnty")];
 						}
 					}
 					$i=1;
 					foreach($sql as $row)
 					{
 						$return_balance=$row[csf("issue_qnty")]-$cumilitive_return[$row[csf('po_breakdown_id')]];
 						?>
 						<tr>
 							<td align="center"><input type="text" id="poNo_<? echo $i; ?>" name="poNo_<? echo $i; ?>" class="text_boxes" style="width:140px" value="<? echo $po_no_arr[$row[csf("po_breakdown_id")]];  ?>"  readonly disabled >
 								<input type="hidden" id="poId_<? echo $i; ?>" name="poId_<? echo $i; ?>" class="text_boxes" style="width:140px" value="<? echo $row[csf("po_breakdown_id")]; ?>" readonly disabled />
 							</td>
 							<td align="center"><input type="text" id="recevqnty_<? echo $i; ?>" name="recevqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:110px" value="<? echo number_format($row[csf("issue_qnty")],2); ?>" readonly disabled ></td>

 							<td align="center"><input type="text" id="cumulativeIssue_<? echo $i; ?>" name="cumulativeIssue_<? echo $i; ?>" value="<? echo number_format($cumilitive_return[$row[csf('po_breakdown_id')]],2); ?>" class="text_boxes_numeric" style="width:110px" readonly disabled ></td>
 							<?
 							if($roll_maintained==1)
 							{
 								?>
 								<td align="center"><input type="text" id="roll_<? echo $i; ?>" name="roll_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" ></td>
 								<?
 							}
 							else
 							{
 								?>
 								<td align="center" style="display:none;"><input type="text" id="roll_<? echo $i; ?>" name="roll_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" ></td>
 								<?
 							}
 							?>
 							<td align="center">
 								<input type="text" id="issueqnty_<? echo $i; ?>" name="issueqnty_<? echo $i; ?>" onKeyUp="fn_calculate(<? echo $i; ?>);" class="text_boxes_numeric" value="<? echo $po_qnty[$row[csf("po_breakdown_id")]]; ?>" style="width:110px" placeholder="<?=$return_balance;?>">
 								<input type="hidden" id="hiddenissueqnty_<? echo $i; ?>" name="hiddenissueqnty_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $po_qnty[$row[csf("po_breakdown_id")]]; ?>">
 							</td>
 							<td align="center">
 								<input type="text" id="rejqetqnty_<? echo $i; ?>" name="rejqetqnty_<? echo $i; ?>" onKeyUp="fn_calculate(<? echo $i; ?>);" class="text_boxes_numeric" value="<? echo $reject_po_qnty[$row[csf("po_breakdown_id")]]; ?>" style="width:110px" >
 								<input type="hidden" id="hiddenrejectqnty_<? echo $i; ?>" name="hiddenrejectqnty_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $reject_po_qnty[$row[csf("po_breakdown_id")]]; ?>">
 							</td>
 						</tr>
 						<?
 						$i++;
 						$balance_quantity += $return_balance;
 					}
 					?>
 				</tbody>
 			</table>
 			<table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0" border="0" rules="all" align="center">
 				<tr>
 					<td align="center">
 						<input type="button" id="btn_close" value="Close" onClick="js_set_value();" style="width:150px;" class="formbutton" />
 						<input type="hidden" id="tot_qnty" name="tot_qnty" />
 						<input type="hidden" id="tot_reject_qnty" name="tot_reject_qnty" />
 						<input type="hidden" id="tot_roll" name="tot_roll" />
 						<input type="hidden" id="break_qnty" name="break_qnty" />
 						<input type="hidden" id="break_roll" name="break_roll" />
 						<input type="hidden" id="break_order_id" name="break_order_id" />
 						<input type="hidden" id="total_balance_quantity" name="total_balance_quantity" value="<? echo $balance_quantity;?>">
 						<input type="hidden" name="distribution_method" id="distribution_method" class="text_boxes" value="">
 					</td>
 				</tr>
 			</table>
 		</form>
 	</div>
 </body>
 <?
}

if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$roll_maintained=str_replace("'","",$roll_maintained);
	$cbo_return_purpose=str_replace("'","",$cbo_return_purpose);

	$cbo_floor = (str_replace("'", "", $cbo_floor) =="")? 0 :str_replace("'", "", $cbo_floor);
	$cbo_room = (str_replace("'", "", $cbo_room)=="")? 0 :str_replace("'", "", $cbo_room);
	$txt_rack = (str_replace("'", "", $txt_rack)=="")? 0 :str_replace("'", "", $txt_rack);
	$txt_shelf = (str_replace("'", "", $txt_shelf)=="")? 0 :str_replace("'", "", $txt_shelf);
	$cbo_bin = (str_replace("'", "", $cbo_bin)=="")? 0 :str_replace("'", "", $cbo_bin);

	if($cbo_return_purpose==8) $book_without_order=1; else $book_without_order=0;

	$is_update_cond =( $operation==1 ) ? " and id <> $update_id" : "";
	$max_transaction_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$txt_prod_id and store_id= $cbo_store_name and status_active = 1 $is_update_cond", "max_date");
	if($max_transaction_date != "")
	{
		$max_transaction_date = date("Y-m-d", strtotime($max_transaction_date));
		$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_issue_date)));
		if ($receive_date < $max_transaction_date)
		{
			echo "20**Issue Return Date Can not Be Less Than Last Transaction Date Of This Lot";
			die;
		}
	}

	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		if(str_replace("'","",$issue_mst_id)!="")
		{
			$new_return_number[0] = str_replace("'","",$txt_system_id);
			$id=str_replace("'","",$issue_mst_id);
			//issue master table UPDATE here START----------------------//
			$field_array_mst="receive_purpose*booking_without_order*receive_date*issue_id*challan_no*updated_by*update_date";
			$data_array_mst=$cbo_return_purpose."*".$book_without_order."*".$txt_issue_date."*".$txt_issue_id."*".$txt_challan_no."*'".$user_id."'*'".$pc_date_time."'";
		}
		else
		{
			//issue master table entry here START---------------------------------------//
			if($db_type==0) $year_cond="YEAR(insert_date)";
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";

			$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
			$new_return_number = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,str_replace("'","",$cbo_company_id),'KFIR',52,date("Y",time())));

			$field_array_mst="id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, item_category, company_id, receive_date, issue_id, challan_no, receive_purpose, inserted_by, insert_date";
			$data_array_mst="(".$id.",'".$new_return_number[1]."','".$new_return_number[2]."','".$new_return_number[0]."',52,2,".$cbo_company_id.",".$txt_issue_date.",".$txt_issue_id.",".$txt_challan_no.",".$cbo_return_purpose.",'".$user_id."','".$pc_date_time."')";
		}

		//transaction table insert here START--------------------------------//cbouom
		$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$field_array_trans = "id,mst_id,company_id,prod_id,item_category,transaction_type,transaction_date,order_uom,order_qnty,cons_uom,cons_quantity,cons_rate,cons_amount,balance_qnty,balance_amount,remarks,issue_id,issue_challan_no,floor_id,room,rack,self,bin_box,batch_id_from_fissuertn,pi_wo_batch_no,store_id,fabric_shade,body_part_id,cons_reject_qnty,inserted_by,insert_date";
		$data_array_trans = "(".$transactionID.",".$id.",".$cbo_company_id.",".$txt_prod_id.",2,4,".$txt_issue_date.",".$cbouom.",".$txt_return_qnty.",".$cbouom.",".$txt_return_qnty.",".$txt_rate.",".$txt_amount.",".$txt_return_qnty.",".$txt_amount.",".$txt_remarks.",".$txt_issue_id.",".$txt_challan_no.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$hidden_batch_id.",".$hidden_batch_id.",".$cbo_store_name.",".$cbo_fabric_type.",".$cbo_body_part.",".$txt_reject_return_qnty.",'".$user_id."','".$pc_date_time."')";

		//adjust product master table START-------------------------------------//
		$sql = sql_select("select product_name_details,last_purchased_qnty,current_stock, stock_value, color,gsm,dia_width,detarmination_id from product_details_master where id=$txt_prod_id");
		$presentStock=$available_qnty=0; $color_id=0;
		$product_name_details="";
		foreach($sql as $result)
		{
			$presentStock			= $result[csf("current_stock")];
			$presentStockValue		= $result[csf("stock_value")];
			$product_name_details 	= $result[csf("product_name_details")];
			$color_id 				= $result[csf("color")];
			$gsm 					= $result[csf("gsm")];
			$dia_width 				= $result[csf("dia_width")];
			$detarmination_id 		= $result[csf("detarmination_id")];
		}
		$nowStock 					= $presentStock+str_replace("'","",$txt_return_qnty);
		$nowStockValue 				= $presentStockValue+str_replace("'","",$txt_amount);
		if($nowStock >0)
		{
			$nowStockRate = $nowStockValue/$nowStock;
		}else{
			$nowStockRate =0;
			$nowStockValue=0;
		}

		$nowStock = number_format($nowStock,2,".","");
		$field_array_prod = "last_purchased_qnty*current_stock*stock_value*avg_rate_per_unit*updated_by*update_date";
		$data_array_prod=$txt_return_qnty."*".$nowStock."*'".$nowStockValue."'*'".$nowStockRate."'*'".$user_id."'*'".$pc_date_time."'";

		$dtls_id = return_next_id_by_sequence("PRO_FIN_FAB_RCV_DTLS_PK_SEQ", "pro_finish_fabric_rcv_dtls", $con);
		$field_details_array = "id,mst_id,trans_id,prod_id,batch_id,body_part_id,fabric_description_id,gsm,width,color_id,order_id,uom, fabric_shade,receive_qnty,is_sales,floor,room,rack_no,shelf_no,bin,issue_dtls_id,remarks,inserted_by,insert_date,reject_qty";
		$data_array_dtls = "(" . $dtls_id . ",". $id .",". $transactionID .",". $txt_prod_id .",". $hidden_batch_id.",".$cbo_body_part .",". $detarmination_id .",'". $gsm ."','". $dia_width ."',". $hdn_color_id .",". $txt_order_id_all .",". $cbouom ."," . $cbo_fabric_type . "," . $txt_return_qnty . ",1," .$cbo_floor.",". $cbo_room . "," . $txt_rack . "," . $txt_shelf . "," . $cbo_bin . ",".$hdn_issue_dtls_id.",".$txt_remarks."," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',".$txt_reject_return_qnty.")";

		//order_wise_pro_detail table insert here
		$txt_break_qnty=str_replace("'","",$txt_break_qnty);
		$txt_break_roll=str_replace("'","",$txt_break_roll);
		$txt_order_id_all=str_replace("'","",$txt_order_id_all);
		$ordr_wise_rtn_qnty_arr=explode("_",$txt_break_qnty);
		$ordr_wise_rtn_roll_arr=explode("_",$txt_break_roll);
		$ordr_id_arr=explode(",",$txt_order_id_all);

		$field_array_proportion="id,trans_id,trans_type,entry_form,po_breakdown_id,prod_id,quantity,color_id,inserted_by,insert_date,reject_qty";

		$data_array_proportion=$data_array_roll="";
		if(!empty($txt_break_qnty))
		{
			foreach($ordr_wise_rtn_qnty_arr as $val)
			{
				$order_qnty_arr=explode("**",$val);
				if($order_qnty_arr[1]>0)
				{
					$proportion_id = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					if($data_array_proportion!="") $data_array_proportion.=", ";
					$data_array_proportion.="(".$proportion_id.",".$transactionID.",4,52,".$order_qnty_arr[0].",".$txt_prod_id.",".$order_qnty_arr[1].",".$color_id.",'".$user_id."','".$pc_date_time."',".$order_qnty_arr[2].")";
				}
			}

			
		}

		$rID=$transID=$prodUpdate=$propoId=true;
		if(str_replace("'","",$txt_system_id)!="")
		{
			$rID=sql_update("inv_receive_master",$field_array_mst,$data_array_mst,"id",$id,1);
		}
		else
		{
			$rID=sql_insert("inv_receive_master",$field_array_mst,$data_array_mst,1);
		}
		$transID = sql_insert("inv_transaction",$field_array_trans,$data_array_trans,1);

		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		$dtlsData=sql_insert("pro_finish_fabric_rcv_dtls",$field_details_array,$data_array_dtls,0);
		$prodUpdate = sql_update("product_details_master",$field_array_prod,$data_array_prod,"id",$txt_prod_id,1);
		if($data_array_proportion!="")
		{
			$propoId=sql_insert("order_wise_pro_details",$field_array_proportion,$data_array_proportion,1);
			
		}

		//echo "10**".$rID ."&&". $transID ."&&". $dtlsData ."&&". $prodUpdate ."&&". $propoId ;die;

		if($db_type==0)
		{
			if( $rID && $transID && $dtlsData && $prodUpdate && $propoId )
			{
				mysql_query("COMMIT");
				echo "0**".$new_return_number[0]."**".$id."**".str_replace("'","",$roll_maintained);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$new_return_number[0];
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if( $rID && $transID && $dtlsData && $prodUpdate && $propoId )
			{
				oci_commit($con);
				echo "0**".$new_return_number[0]."**".$id."**".str_replace("'","",$roll_maintained);
			}
			else
			{
				oci_rollback($con);
				echo "10**".$new_return_number[0];
			}
		}
		disconnect($con);
		die;

	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here
		$issue_mst_id= str_replace("'","",$issue_mst_id);
		$roll_maintained= str_replace("'","",$roll_maintained);
		$txt_system_id = str_replace("'","",$txt_system_id);
		//check update id
		if( str_replace("'","",$update_id) == "" )
		{
			echo "10";disconnect($con);die;
		}


		$txt_return_qnty=str_replace("'","",$txt_return_qnty);

		//****************************************** BEFORE ENTRY ADJUST START *****************************************//
		//product master table information
		//before stock update
		$sql = sql_select( "select a.id,a.current_stock, a.stock_value, b.cons_amount, b.cons_quantity from product_details_master a, inv_transaction b where a.id=b.prod_id and a.id=$before_prod_id and b.id=$update_id and a.item_category_id=2 and b.item_category=2 and b.transaction_type=4" );
		$before_prod_id=$before_issue_qnty=$before_stock_qnty=0;
		foreach($sql as $result)
		{
			$before_prod_id 	= $result[csf("id")];
			$before_stock_qnty 	= $result[csf("current_stock")];
			$before_stock_value = $result[csf("stock_value")];
			//before quantity and stock value
			$before_issue_qnty	= $result[csf("cons_quantity")];
			$before_issue_amnt  = $result[csf("cons_amount")];
		}

		//current product ID
		$txt_prod_id = str_replace("'","",$txt_prod_id);
		$txt_return_qnty = str_replace("'","",$txt_return_qnty);
		$before_prod_id= str_replace("'","",$before_prod_id);
		//$curr_stock_qnty=return_field_value("current_stock","product_details_master","id=$txt_prod_id and item_category_id=2");

		$present_prod_sql = sql_select("select current_stock, stock_value, gsm, dia_width, detarmination_id from product_details_master where id=$txt_prod_id and item_category_id=2");
		$curr_stock_qnty= $present_prod_sql[0][csf("current_stock")];
		$curr_stock_value= $present_prod_sql[0][csf("stock_value")];

		$gsm= $present_prod_sql[0][csf("gsm")];
		$dia_width= $present_prod_sql[0][csf("dia_width")];
		$detarmination_id= $present_prod_sql[0][csf("detarmination_id")];


		//weighted and average rate START here------------------------//
		//product master table data UPDATE START----------------------//
		$update_array_prod= "last_purchased_qnty*current_stock*stock_value*avg_rate_per_unit*updated_by*update_date";
		if($before_prod_id==$txt_prod_id)
		{
			$adj_stock_qnty = (($curr_stock_qnty-$before_issue_qnty)+$txt_return_qnty); // CurrentStock + Before Issue Qnty - Current Issue Qnty
			$adj_stock_value =  (($curr_stock_value-$before_issue_amnt)+str_replace("'","",$txt_amount));

			if($adj_stock_qnty>0){
				$adj_stock_rate = $adj_stock_value/$adj_stock_qnty;
			}else{
				$adj_stock_rate =0;
				$adj_stock_value=0;
			}

			$adj_stock_qnty = number_format($adj_stock_qnty,2,".","");
			$data_array_prod= $txt_return_qnty."*".$adj_stock_qnty."*'".$adj_stock_value."'*'".$adj_stock_rate."'*'".$user_id."'*'".$pc_date_time."'";
		}
		else
		{
			$updateIdprod_array = $update_dataProd = array();
			//before product adjust
			$adj_before_stock_qnty 	= $before_stock_qnty-$before_issue_qnty; // CurrentStock + Before Issue Qnty
			$adj_before_stock_value = $before_stock_value-$before_issue_amnt;
			
			if($adj_before_stock_qnty>0)
			{
				$adj_before_stock_rate = $adj_before_stock_value/$adj_before_stock_qnty;
			}
			else
			{
				$adj_before_stock_rate =0;
				$adj_before_stock_value=0;
			}
			$adj_before_stock_qnty = number_format($adj_before_stock_qnty,2,".","");
			$updateIdprod_array[]=$before_prod_id;
			$update_dataProd[$before_prod_id]=explode("*",("".$before_issue_qnty."*".$adj_before_stock_qnty."*'".$adj_before_stock_value."'*'".$adj_before_stock_rate."'*'".$user_id."'*'".$pc_date_time."'"));

			//current product adjust
			$adj_curr_stock_qnty = 	$curr_stock_qnty+$txt_return_qnty; // CurrentStock + Before Issue Qnty
			$adj_curr_stock_value = $curr_stock_value+str_replace("'","",$txt_amount); 
			if($adj_curr_stock_qnty>0){
				$adj_curr_stock_rate = $adj_curr_stock_value/$adj_curr_stock_qnty;
			}else{
				$adj_curr_stock_rate =0;
				$adj_curr_stock_value=0;
			}

			$adj_curr_stock_qnty = number_format($adj_curr_stock_qnty,2,".","");
			$updateIdprod_array[]=$txt_prod_id;
			$update_dataProd[$txt_prod_id]=explode("*",("".$txt_return_qnty."*".$adj_curr_stock_qnty."*'".$adj_curr_stock_value."'*'".$adj_curr_stock_rate."'*'".$user_id."'*'".$pc_date_time."'"));
		}

		$id=str_replace("'","",$issue_mst_id);
		//yarn master table UPDATE here START----------------------//cbouom
		$field_array_mst="receive_date*issue_id*challan_no*receive_purpose*updated_by*update_date";
		$data_array_mst=$txt_issue_date."*".$txt_issue_id."*".$txt_challan_no."*".$cbo_return_purpose."*'".$user_id."'*'".$pc_date_time."'";

		$field_array_trans="company_id*prod_id*item_category*transaction_type*transaction_date*order_uom*order_qnty*cons_uom*cons_quantity*cons_rate*cons_amount*balance_qnty*balance_amount*remarks*issue_id*issue_challan_no*floor_id*room*rack*self*bin_box*batch_id_from_fissuertn*pi_wo_batch_no*store_id*fabric_shade*body_part_id*cons_reject_qnty*updated_by*update_date";
		$data_array_trans= "".$cbo_company_id."*".$txt_prod_id."*2*4*".$txt_issue_date."*".$cbouom."*".$txt_return_qnty."*".$cbouom."*".$txt_return_qnty."*".$txt_rate."*".$txt_amount."*".$txt_return_qnty."*".$txt_amount."*".$txt_remarks."*".$txt_issue_id."*".$txt_challan_no."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$hidden_batch_id."*".$hidden_batch_id."*".$cbo_store_name."*".$cbo_fabric_type."*".$cbo_body_part."*".$txt_reject_return_qnty."*'".$user_id."'*'".$pc_date_time."'";

		//$field_array_dtls_update="receive_qnty*reject_qty*no_of_roll*remarks*body_part_id*updated_by*update_date";
		//$data_array_dtls_update=$txt_return_qnty."*".$txt_reject_return_qnty."*".$txt_remarks."*".$cbo_body_part."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";


		$field_array_dtls_update="prod_id*batch_id*body_part_id*fabric_description_id*gsm*width*color_id*order_id*uom* fabric_shade*receive_qnty*is_sales*floor*room*rack_no*shelf_no*bin*issue_dtls_id*remarks*reject_qty*updated_by*update_date";
		$data_array_dtls_update=$txt_prod_id ."*". $hidden_batch_id."*".$cbo_body_part ."*". $detarmination_id ."*'". $gsm ."'*'". $dia_width ."'*". $hdn_color_id ."*". $txt_order_id_all ."*". $cbouom ."*" . $cbo_fabric_type . "*" . $txt_return_qnty . "*0*" .$cbo_floor."*". $cbo_room . "*" . $txt_rack . "*" . $txt_shelf . "*" . $cbo_bin . "*".$hdn_issue_dtls_id."*".$txt_remarks ."*". $txt_reject_return_qnty ."*". $_SESSION['logic_erp']['user_id'] ."*'". $pc_date_time ."'";


		$update_id = str_replace("'","",$update_id);

		//order_wise_pro_detail table insert here
		$txt_break_qnty=str_replace("'","",$txt_break_qnty);
		$txt_break_roll=str_replace("'","",$txt_break_roll);
		$txt_order_id_all=str_replace("'","",$txt_order_id_all);
		$ordr_wise_rtn_qnty_arr=explode("_",$txt_break_qnty);
		$ordr_wise_rtn_roll_arr=explode("_",$txt_break_roll);
		$ordr_id_arr=explode(",",$txt_order_id_all);

		$field_array_proportion="id,trans_id,trans_type,entry_form,po_breakdown_id,prod_id,quantity,color_id,inserted_by,insert_date,reject_qty";
		$color_id=return_field_value("color","product_details_master","id=$txt_prod_id and item_category_id=2","color");
		//echo "select color from product_details_master where id=$txt_prod_id and item_category_id=2"; die;

		$data_array_proportion=$data_array_roll="";
		if(!empty($txt_break_qnty))
		{
			foreach($ordr_wise_rtn_qnty_arr as $val)
			{
				$order_qnty_arr=explode("**",$val);
				if($order_qnty_arr[1]>0)
				{
					$proportion_id = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					if($data_array_proportion!="") $data_array_proportion.=", ";
					$data_array_proportion.="(".$proportion_id.",".$update_id.",4,52,".$order_qnty_arr[0].",".$txt_prod_id.",".$order_qnty_arr[1].",".$color_id.",'".$user_id."','".$pc_date_time."',".$order_qnty_arr[2].")";
				}
			}

			
		}

		$query1=$query4=$query5=$rID=$transID=$propoId=$rollId=true;

		if($before_prod_id==$txt_prod_id)
		{
			$query1= sql_update("product_details_master",$update_array_prod,$data_array_prod,"id",$before_prod_id,1);
		}
		else
		{
			$query1=execute_query(bulk_update_sql_statement("product_details_master","id",$update_array_prod,$update_dataProd,$updateIdprod_array));
		}
		$rID=sql_update("inv_receive_master",$field_array_mst,$data_array_mst,"id",$id,1);
		$transID = sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_id,1);
		$dtlsID=sql_update("pro_finish_fabric_rcv_dtls",$field_array_dtls_update,$data_array_dtls_update,"id",$hdn_recv_dtls_id,1);

		if($data_array_proportion!="")
		{
			$query4 = execute_query("DELETE FROM order_wise_pro_details WHERE trans_id=$update_id and entry_form=52");
			$propoId=sql_insert("order_wise_pro_details",$field_array_proportion,$data_array_proportion,1);
			
		}


		//echo "10**$query1 && $query4 && $rID && $transID && $dtlsID && $propoId";
		//echo $field_array_dtls_update."<br>".$data_array_dtls_update;

		//oci_rollback($con);
		//die;


		if($db_type==0)
		{
			if($query1 && $query4 && $rID && $transID && $dtlsID && $propoId)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$txt_system_id)."**".$issue_mst_id."**".str_replace("'","",$roll_maintained);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_system_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($query1 && $query4 && $rID && $transID && $dtlsID && $propoId)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_system_id)."**".$issue_mst_id."**".str_replace("'","",$roll_maintained);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_system_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		$con = connect(); 
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$issue_mst_id= str_replace("'","",$issue_mst_id);
		$update_trans_id= str_replace("'","",$update_id); // trans_id
		$roll_maintained= str_replace("'","",$roll_maintained);
		$txt_system_id = str_replace("'","",$txt_system_id);
		$txt_break_qnty=str_replace("'","",$txt_break_qnty);

		// master table delete here---------------------------------------
		if( $update_trans_id == "" || str_replace("'","",$txt_prod_id) == "" || str_replace("'","",$before_prod_id) == "" )
		{
			echo "20**Delete not allowed."; die;
			disconnect($con); 
			exit(); 
		}
		else 
		{
			$sql = sql_select( "SELECT a.id,a.current_stock, a.stock_value, b.cons_quantity, b.cons_amount, b.store_id from product_details_master a, inv_transaction b where a.id=b.prod_id and b.id=$update_trans_id and a.item_category_id=2 and b.item_category=2 and b.transaction_type=4" );
			//  and a.id=$txt_prod_id
		
			$before_issue_qnty=$before_stock_qnty=0;
			foreach($sql as $result)
			{
				$before_prod_id 	= $result[csf("id")];
				$before_stock_qnty 	= $result[csf("current_stock")];
				$before_stock_value = $result[csf("stock_value")];
				$before_issue_qnty	= $result[csf("cons_quantity")];
				$before_issue_amnt	= $result[csf("cons_amount")];
				$before_store_id	= $result[csf("store_id")];
			}

			$max_trans_query = sql_select("SELECT max(id) as max_id from inv_transaction where prod_id=$before_prod_id and store_id=$before_store_id and item_category=2 and status_active=1");
			$max_trans_id = $max_trans_query[0][csf('max_id')];

			if($max_trans_id > str_replace("'", "", $update_trans_id))
			{
				echo "20**Next transaction found of this store and product. delete not allowed.";
				die;
			}

			$adj_beforeStock		= $before_stock_qnty-$before_issue_qnty;
			$adj_beforeStockValue	= $before_stock_value-$before_issue_amnt;

			if($adj_beforeStock>0){
				$adj_beforeStockRate= $adj_beforeStockValue/$adj_beforeStock;
			}else{
				$adj_beforeStockRate=0;
				$adj_beforeStockValue=0;
			}

			$adj_beforeStock = number_format($adj_beforeStock,2,".","");
			$field_array_prod = "last_issued_qnty*current_stock*stock_value*avg_rate_per_unit*updated_by*update_date";
			$data_array_prod=$before_issue_qnty."*".$adj_beforeStock."*'".$adj_beforeStockValue."'*'".$adj_beforeStockRate."'*'".$user_id."'*'".$pc_date_time."'";

			$checkTransaction = sql_select("SELECT id from pro_finish_fabric_rcv_dtls where status_active=1 and is_deleted=0 and mst_id = ".$issue_mst_id." and id !=".$hdn_recv_dtls_id."");
			if(count($checkTransaction) == 0)
			{
				$field_array_mst = "updated_by*update_date*status_active*is_deleted";
				$data_array_mst = "'" . $user_id . "'*'" . $pc_date_time . "'*0*1";
				$is_mst_del = sql_update("inv_receive_master", $field_array_mst, $data_array_mst, "id", $issue_mst_id, 1);
				if($is_mst_del) $flag=1; else $flag=0;
			}				
			
			$field_array_trans="updated_by*update_date*status_active*is_deleted";
			$data_array_trans="".$user_id."*'".$pc_date_time."'*0*1";				
			$rID=sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_trans_id,1);
			if($rID) $flag=1; else $flag=0;

			$field_array_dtls="updated_by*update_date*status_active*is_deleted";
			$data_array_dtls="".$user_id."*'".$pc_date_time."'*0*1";				
			$rID2=sql_update("pro_finish_fabric_rcv_dtls",$field_array_dtls,$data_array_dtls,"id",$hdn_recv_dtls_id,1);
			if($rID2) $flag=1; else $flag=0;

			$rID3=sql_update("product_details_master",$field_array_prod,$data_array_prod,"id",$before_prod_id,1);
			if($rID3) $flag=1; else $flag=0;

			if(!empty($txt_break_qnty))
			{
				$field_array_prop="updated_by*update_date*status_active*is_deleted";
				$data_array_prop="'".$user_id."'*'".$pc_date_time."'*0*1";
				$rID4=sql_update("order_wise_pro_details",$field_array_prop,$data_array_prop,"trans_id*entry_form","$update_trans_id*52",1);
				if($rID4) $flag=1; else $flag=0;
				
			}
		}
		// echo "10**$rID##$rID2##$rID3##$rID4##$is_mst_del**$flag";
		// oci_rollback($con);disconnect($con);die;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_system_id)."**".$issue_mst_id."**".str_replace("'","",$roll_maintained)."**".$is_mst_del;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_system_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_system_id)."**".$issue_mst_id."**".str_replace("'","",$roll_maintained)."**".$is_mst_del;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_system_id);
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="show_dtls_list_view")
{

	$ex_data = explode("**",$data);

	$sql = "select a.recv_number,a.company_id,a.supplier_id,a.receive_date,a.item_category,a.recv_number,b.id, b.cons_quantity, b.cons_uom, b.cons_rate, b.cons_amount,b.store_id,b.floor_id,b.room,b.rack,b.self, c.product_name_details, c.id as prod_id
	from  inv_receive_master a, inv_transaction b left join product_details_master c on b.prod_id=c.id
	where a.id=b.mst_id and b.item_category=2 and b.transaction_type=4 and a.id=$ex_data[0] and a.status_active=1 and b.status_active=1";
	//echo $sql;
	$result = sql_select($sql);
	$i=1;
	$rettotalQnty=0;
	$rcvtotalQnty=0;
	$rejtotalQnty=0;
	$totalAmount=0;
	?>
	<table class="rpt_table" border="1" cellpadding="2" cellspacing="0" style="width:600px" rules="all">
		<thead>
			<tr>
				<th>SL</th>
				<th>Return No</th>
				<th>Product ID</th>
				<th>Item Description</th>
				<th>Return Qty</th>
			</tr>
		</thead>
		<tbody>
			<?
			foreach($result as $row){
				if($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";

				$rettotalQnty +=$row[csf("cons_quantity")];
				$totalAmount +=$row[csf("cons_amount")];

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("id")]."**".$ex_data[1]."**".$row[csf("company_id")]."**".$row[csf("floor_id")]."**".$row[csf("room")]."**".$row[csf("rack")]."**".$row[csf("self")]."**".$row[csf("store_id")];?>","child_form_input_data","requires/finish_fabric_issue_return_controller")' style="cursor:pointer" >
					<td width="50"><? echo $i; ?></td>
					<td width="120"><p><? echo $row[csf("recv_number")]; ?></p></td>
					<td width="100" align="center"><p><? echo $row[csf("prod_id")]; ?></p></td>
					<td width="250"><p><? echo $row[csf("product_name_details")]; ?></p></td>
					<td align="right" style="padding-right:3px;"><p><? echo $row[csf("cons_quantity")]; ?></p></td>
				</tr>
				<? $i++; } ?>
				<tfoot>
					<th colspan="4">Total</th>
					<th><? echo number_format($rettotalQnty,2); ?></th>
				</tfoot>
			</tbody>
		</table>
		<?
		exit();
	}

	if($action=="child_form_input_data")
	{
		$ex_data = explode("**",$data);
		$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');
		$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst","id","batch_no");

		/*$lib_room_rack_shelf_sql = "select b.company_id,b.location_id,b.store_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name floor_name,c.floor_room_rack_name room_name,d.floor_room_rack_name rack_name,e.floor_room_rack_name shelf_name from lib_floor_room_rack_dtls b
		left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active =1 and a.is_deleted=0
		left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active =1 and c.is_deleted=0
		left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active =1 and d.is_deleted=0
		left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active =1 and e.is_deleted=0
		where b.status_active =1 and b.is_deleted=0 and b.company_id in(".$ex_data[2].") and b.store_id in(".$ex_data[7].")";
		$lib_floor_arr=sql_select($lib_room_rack_shelf_sql);
		foreach ($lib_floor_arr as $room_rack_shelf_row) {
			$company  = $room_rack_shelf_row[csf("company_id")];
			$location = $room_rack_shelf_row[csf("location_id")];
			$floor_id = $room_rack_shelf_row[csf("floor_id")];
			$room_id  = $room_rack_shelf_row[csf("room_id")];
			$rack_id  = $room_rack_shelf_row[csf("rack_id")];
			$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
			$bin_id   = $room_rack_shelf_row[csf("bin_id")];

			if($floor_id!="" && $room_id=="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
				$lib_floor_arr[$company][$floor_id] = $room_rack_shelf_row[csf("floor_name")];
			}

			if($floor_id!="" && $room_id!="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
				$lib_room_arr[$company][$floor_id][$room_id] = $room_rack_shelf_row[csf("room_name")];
			}

			if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id=="" && $bin_id==""){
				$lib_rack_arr[$company][$floor_id][$room_id][$rack_id] = $room_rack_shelf_row[csf("rack_name")];
			}

			if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id==""){
				$lib_shelf_arr[$company][$floor_id][$room_id][$rack_id][$shelf_id] = $room_rack_shelf_row[csf("shelf_name")];
			}
		}*/

		$roll_maintained=str_replace("'","",$ex_data[1]);
		$sql = "select b.company_id,b.id as prod_id, b.product_name_details, b.color, b.current_stock, a.id as tr_id, a.store_id, a.issue_id, a.cons_quantity,a.cons_rate,a.cons_amount, a.issue_challan_no,a.gmt_item_id, a.remarks,a.floor_id,a.room, a.rack, a.self,a.bin_box,a.batch_id_from_fissuertn,a.cons_uom,c.id dtls_id,c.body_part_id,c.fabric_shade,c.reject_qty, c.issue_dtls_id from inv_transaction a,pro_finish_fabric_rcv_dtls c, product_details_master b where a.id=$ex_data[0] and a.status_active=1 and a.item_category=2 and a.transaction_type=4 and a.id=c.trans_id and a.prod_id=b.id and b.status_active=1 and c.status_active=1";

		$result = sql_select($sql);
		foreach($result as $row)
		{
			$issue_purpose=return_field_value("issue_purpose","inv_issue_master","id='".$row[csf("issue_id")]."'");

			//$floor 		= $lib_floor_arr[$row[csf("company_id")]][$row[csf("floor_id")]];
			//$room 		= $lib_room_arr[$row[csf("company_id")]][$row[csf("floor_id")]][$row[csf("room")]];
			//$rack_no	= $lib_rack_arr[$row[csf("company_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]];
			//$shelf_no 	= $lib_shelf_arr[$row[csf("company_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]];

			echo "return_qnty_basis(".$issue_purpose.");\n";
			echo "$('#cbo_store_name').val('".$row[csf("store_id")]."');\n";

			echo "load_room_rack_self_bin('requires/finish_fabric_issue_return_controller*2', 'store','store_td', '".$row[csf('company_id')]."','',this.value);\n";

			echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("store_id")]."';\n";
			echo "load_room_rack_self_bin('requires/finish_fabric_issue_return_controller', 'floor','floor_td', '".$row[csf('company_id')]."','','".$row[csf('store_id')]."',this.value);\n";
			echo "document.getElementById('cbo_floor').value 					= '".$row[csf("floor_id")]."';\n";
			echo "load_room_rack_self_bin('requires/finish_fabric_issue_return_controller', 'room','room_td', '".$row[csf('company_id')]."','','".$row[csf('store_id')]."','".$row[csf('floor_id')]."',this.value);\n";
			echo "document.getElementById('cbo_room').value 					= '".$row[csf("room")]."';\n";
			echo "load_room_rack_self_bin('requires/finish_fabric_issue_return_controller', 'rack','rack_td', '".$row[csf('company_id')]."','','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
			echo "document.getElementById('txt_rack').value 					= '".$row[csf("rack")]."';\n";
			echo "load_room_rack_self_bin('requires/finish_fabric_issue_return_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";
			echo "document.getElementById('txt_shelf').value 					= '".$row[csf("self")]."';\n";

			echo "load_room_rack_self_bin('requires/finish_fabric_issue_return_controller', 'bin','bin_td', '".$row[csf('company_id')]."','','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."','".$row[csf('self')]."',this.value);\n";
			echo "document.getElementById('cbo_bin').value 					= '".$row[csf("bin_box")]."';\n";

			echo "$('#txt_batch_no').val('".$batch_arr[$row[csf("batch_id_from_fissuertn")]]."');\n";
			echo "$('#hidden_batch_id').val('".$row[csf("batch_id_from_fissuertn")]."');\n";
			echo "$('#txt_fabric_desc').val('".$row[csf("product_name_details")]."');\n";
			echo "$('#txt_prod_id').val('".$row[csf("prod_id")]."');\n";
			echo "$('#before_prod_id').val('".$row[csf("prod_id")]."');\n";
			echo "$('#txt_return_qnty').val('".$row[csf("cons_quantity")]."');\n";
			echo "$('#txt_reject_return_qnty').val('".$row[csf("reject_qty")]."');\n";
			echo "$('#cbo_body_part').val('".$row[csf("body_part_id")]."').attr('disabled','disabled');\n";
			echo "$('#cbo_item_name').val('".$row[csf("gmt_item_id")]."').attr('disabled','disabled');\n";
			echo "$('#txt_rate').val('".$row[csf("cons_rate")]."').attr('disabled','disabled');\n";
			echo "$('#txt_amount').val('".number_format($row[csf("cons_amount")],2,".","")."').attr('disabled','disabled');\n";

			/*echo "$('#txt_floor_name').val('".$floor."');\n";
			echo "$('#txt_room_name').val('".$room."');\n";
			echo "$('#txt_rack_name').val('".$rack_no."');\n";
			echo "$('#txt_shelf_name').val('".$shelf_no."');\n";

			echo "$('#txt_floor').val('".$row[csf("floor_id")]."');\n";
			echo "$('#txt_room').val('".$row[csf("room")]."');\n";
			echo "$('#txt_rack').val('".$row[csf("rack")]."');\n";
			echo "$('#txt_shelf').val('".$row[csf("self")]."');\n";*/
			echo "$('#cbo_store_name').attr('disabled','disabled');\n";
		 	echo "$('#cbo_floor').attr('disabled','disabled');\n";
		 	echo "$('#cbo_room').attr('disabled','disabled');\n";
		 	echo "$('#txt_rack').attr('disabled','disabled');\n";
		 	echo "$('#txt_shelf').attr('disabled','disabled');\n";
		 	echo "$('#cbo_bin').attr('disabled','disabled');\n";

			echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
			echo "$('#txt_issue_id').val('".$row[csf("issue_id")]."');\n";
			echo "$('#txt_color').val('".$color_arr[$row[csf("color")]]."');\n";
			echo "$('#hdn_color_id').val('".$row[csf("color")]."');\n";
			echo "$('#cbouom').val('".$row[csf("cons_uom")]."');\n";
			echo "$('#cbo_fabric_type').val('".$row[csf("fabric_shade")]."');\n";

			$propotion_sql=sql_select("select po_breakdown_id, quantity,reject_qty from order_wise_pro_details where trans_id='".$row[csf("tr_id")]."'");
			$po_wise_qnty="";$po_id_all="";
			if(count($propotion_sql)>0)
			{
				foreach($propotion_sql as $row_order)
				{
					if($po_wise_qnty!="") $po_wise_qnty .="_";
					$po_wise_qnty .=$row_order[csf("po_breakdown_id")]."**".$row_order[csf("quantity")]."**".$row_order[csf("reject_qty")];
					if($po_id_all!="") $po_id_all .=",";
					$po_id_all .=$row_order[csf("po_breakdown_id")];
				}
				if($roll_maintained==1)
				{
					$roll_sql=sql_select("select po_breakdown_id, roll_no, qnty from  pro_roll_details where mst_id='$issue_id' and dtls_id='".$row[csf("tr_id")]."'");
					$roll_ref="";
					foreach($roll_sql as $row_roll)
					{
						if($roll_ref!="") $roll_ref .="_";
						$roll_ref .=$row_roll[csf("po_breakdown_id")]."**".$row_roll[csf("roll_no")]."**".$row_roll[csf("qnty")];
					}
				}
			}
			else
			{
				echo "$('#txt_return_qnty').removeAttr('placeholder').removeAttr('readonly').removeAttr('onDblClick').attr('placeholder','write');\n";
			}

			echo "$('#txt_break_qnty').val('$po_wise_qnty');\n";
			echo "$('#txt_break_roll').val('$roll_ref');\n";
			echo "$('#txt_order_id_all').val('$po_id_all');\n";


			$totalIssued = return_field_value("sum(b.cons_quantity)","inv_issue_master a, inv_transaction b"," a.id=b.mst_id and a.id='".$row[csf("issue_id")]."' and b.prod_id='".$row[csf("prod_id")]."' and b.item_category=2 and b.transaction_type=2");

			if($totalIssued=="") $totalIssued=0;
			echo "$('#txt_tot_issue').val('".$totalIssued."');\n";


			$totalReturn = return_field_value("sum(cons_quantity)","inv_transaction","issue_id='".$row[csf("issue_id")]."' and prod_id='".$row[csf("prod_id")]."' and item_category=2 and transaction_type=4");
			echo "$('#txt_total_return_display').val('".$totalReturn."');\n";
			$netUsed = $totalIssued-$totalReturn;
			echo "$('#txt_net_used').val('".$netUsed."');\n";
			echo "$('#hide_net_used').val('".$row[csf("cons_quantity")]."');\n";
			echo "$('#txt_global_stock').val('".$row[csf("current_stock")]."');\n";
			echo "$('#update_id').val(".$row[csf("tr_id")].");\n";
			echo "$('#hdn_recv_dtls_id').val(".$row[csf("dtls_id")].");\n";
			echo "$('#hdn_issue_dtls_id').val(".$row[csf("issue_dtls_id")].");\n";

		}
		echo "set_button_status(1, permission, 'fnc_fabric_issue_rtn',1,1);\n";
		exit();
	}


	if($action=="return_number_popup")
	{
		echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
		extract($_REQUEST);
		?>

		<script>
			function js_set_value(mrr)
			{
				$("#hidden_return_number").val(mrr);
				parent.emailwindow.hide();
			}

			function change_caption()
			{
				var caption=$("#cbo_search_by :selected").text();
				$("#search_by_td_up").text("Enter "+caption);
			}
		</script>

	</head>

	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<tr>
							<th width="170">Search By</th>
							<th width="270" align="center" id="search_by_td_up">Enter Return Number</th>
							<th width="220">Date Range</th>
							<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
						</tr>
					</thead>
					<tbody>
						<tr class="general">
							<td>
								<?
								$search_by = array(1=>'Return Number',2=>'Batch No',3=>'Booking No');
								echo create_drop_down( "cbo_search_by", 140, $search_by,"",0, "--Select--", "",'change_caption()','' );
								?>
							</td>
							<td width="" align="center" id="search_by_td">
								<input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" />
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" />
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_return_search_list_view', 'search_div', 'finish_fabric_issue_return_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td align="center" height="40" valign="middle" colspan="5">
								<? echo load_month_buttons(1);  ?>
								<input type="hidden" id="hidden_return_number" value="" />
							</td>
						</tr>
					</tbody>
				</tr>
			</table>
			<div style="margin-top:5px" align="center" valign="top" id="search_div"> </div>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_return_search_list_view")
{
	$ex_data = explode("_",$data);
	$search_by = $ex_data[0];
	$search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];

	$sql_cond="";
	if($search_common!="")
	{
		if($search_by==1)
		{
			$sql_cond .= " and a.recv_number like '%$search_common'";
		}else if($search_by==2)
		{
			$sql_cond .= " and d.batch_no like '$search_common%'";
		}
		else
		{
			$sql_cond .= " and d.booking_no like '%$search_common%'";
		}
	}

	if( $txt_date_from!="" && $txt_date_to!="" )
	{
		if($db_type==0)
		{
			$sql_cond .= " and a.receive_date  between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else
		{
			$sql_cond .= " and a.receive_date  between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
		}
	}

	if($company!="") $sql_cond .= " and a.company_id='$company'";

	if($db_type==0) $year_field="YEAR(a.insert_date) as year,";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	else $year_field="";

	$sql = "SELECT a.id as mst_id,a.recv_number_prefix_num, a.recv_number, a.receive_date, a.item_category, a.issue_id, $year_field b.id, b.cons_quantity, c.product_name_details, c.id as prod_id, d.batch_no,d.booking_no
	from inv_receive_master a, inv_transaction b, product_details_master c, pro_batch_create_mst d
	where a.id=b.mst_id and b.prod_id=c.id and b.batch_id_from_fissuertn=d.id and b.item_category=2 and b.transaction_type=4 and a.entry_form=52 $sql_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id";
	$arr=array();
	echo create_list_view("list_view", "Return No, Year, Batch No,Booking No, Item Description, Return Date, Return Qnty","70,60,140,140,280,80","940","260",0, $sql , "js_set_value", "mst_id,issue_id", "", 1, "0,0,0,0,0,0,0", $arr, "recv_number_prefix_num,year,batch_no,booking_no,product_name_details,receive_date,cons_quantity","","",'0,0,0,0,0,3,2') ;
	exit();
}

if($action=="populate_master_from_data")
{

	$sql = "select id,recv_number,company_id,receive_purpose,receive_date,challan_no,issue_id from inv_receive_master  where id='$data'";
	//echo $sql;
	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "set_button_status(0, permission, 'fnc_fabric_issue_rtn',1,1);";
		echo "$('#txt_system_id').val('".$row[csf("recv_number")]."');\n";
		echo "$('#issue_mst_id').val('".$row[csf("id")]."');\n";
		echo "$('#cbo_return_purpose').val('".$row[csf("receive_purpose")]."');\n";
		echo "$('#txt_issue_id').val('".$row[csf("issue_id")]."');\n";
		$issue_num = return_field_value("issue_number"," inv_issue_master","id='".$row[csf("issue_id")]."'");
		echo "$('#txt_issue_no').val('$issue_num');\n";
		echo "return_qnty_basis('".$row[csf("receive_basis")]."');\n";
		echo "$('#txt_issue_date').val('".change_date_format($row[csf("receive_date")])."');\n";

		echo "$('#txt_challan_no').val('".$row[csf("challan_no")]."');\n";
		echo "disable_enable_fields( 'cbo_company_name', 1, '', '' );\n"; // disable true

	}
	exit();
}

if ($action=="issue_return_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);

	$sql=" select id, recv_number, issue_id, challan_no, receive_date from  inv_receive_master where id='$data[3]' and entry_form=52 and item_category=2";

	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1", "id","color_name");
	$batch_arr=array();
	$sql_batch=sql_select("select id, batch_no, color_id from pro_batch_create_mst where status_active=1 and is_deleted=0");
	foreach($sql_batch as $val)
	{
		$batch_arr[$val[csf('id')]]['batch']=$val[csf('batch_no')];
		$batch_arr[$val[csf('id')]]['color']=$val[csf('color_id')];
	}

	//$issueNo_arr=return_library_array( "select id, issue_number from inv_issue_master where status_active=1 and is_deleted=0 and item_category=2 and entry_form=18", "id", "issue_number");

	$issueNo_result = sql_select("select id, issue_number,knit_dye_company,knit_dye_source from inv_issue_master where id=".$dataArray[0][csf('issue_id')]." and status_active=1 and is_deleted=0 and item_category=2 and entry_form=18");
	foreach ($issueNo_result as $value)
	{
		$issueNo_arr[$value[csf("id")]]["issue_number"] = $value[csf("issue_number")];
		$issueNo_arr[$value[csf("id")]]["knit_dye_source"] = $value[csf("knit_dye_source")];
		$issueNo_arr[$value[csf("id")]]["knit_dye_company"] = $value[csf("knit_dye_company")];
	}

	$floor_room_rack_name_arr = return_library_array("select a.floor_room_rack_id, a.floor_room_rack_name from lib_floor_room_rack_mst a,  lib_floor_room_rack_dtls b where a.floor_room_rack_id = b.floor_room_rack_dtls_id and a.company_id =".$data[0]." and a.status_active =1 and b.status_active =1 group by a.floor_room_rack_id, a.floor_room_rack_name","floor_room_rack_id","floor_room_rack_name");

	?>
	<div style="width:930px;">
		<table width="900" cellspacing="0" align="right">
			<tr>
				<td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">
					<?
					$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
						?>
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')];?>
						City No: <? echo $result[csf('city')];?>
						Zip Code: <? echo $result[csf('zip_code')]; ?>
						Province No: <? echo $result[csf('province')];?>
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email Address: <? echo $result[csf('email')];?>
						Website No: <? echo $result[csf('website')];
					}
					?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:16px"><strong><u><? echo $data[2]; ?> Challan</u></strong></td>
			</tr>
			<tr>
				<td width="120"><strong>Return ID:</strong></td><td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
				<td width="130"><strong>Return Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
				<td width="125"><strong>Issue No:</strong></td> <td width="175px"><? echo $issueNo_arr[$dataArray[0][csf('issue_id')]]["issue_number"]; ?></td>
			</tr>
			<tr>
				<td><strong>Challan:</strong></td> <td><? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>Service Company</strong></td>
				<td>
					<?
					if($issueNo_arr[$dataArray[0][csf('issue_id')]]["knit_dye_source"] == 1)
					{
						echo $company_library[$issueNo_arr[$dataArray[0][csf('issue_id')]]["knit_dye_company"]];
					}else{
						echo $supplier_library[$issueNo_arr[$dataArray[0][csf('issue_id')]]["knit_dye_company"]];
					}

					?>
				</td>
				<td><strong></strong></td><td><? //echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="right" cellspacing="0" cellpadding="0" width="900" border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="110">Store</th>
					<th width="110">Buyer:<br>Style No:<br>Job No:<br>Internal Ref:</th>
					<th width="70">Batch No</th>
					<th width="200">Item Description</th>
					<th width="80">Color</th>
					<th width="60">Rack</th>
					<th width="60">Self</th>
					<th width="60">Roll</th>
					<th width="100">Returned Qty.</th>
					<th>Remarks</th>
				</thead>
				<tbody>
					<?
					$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');

					$i=1;
					$mst_id=$dataArray[0][csf('id')];

					$sql_dtls="SELECT a.id as pd_id, a.product_name_details, a.lot, b.id, b.cons_uom, b.batch_id_from_fissuertn, b.rack, b.self, b.cons_quantity, b.store_id, b.no_of_roll, b.remarks, c.order_id from product_details_master a, inv_transaction b, pro_finish_fabric_rcv_dtls c where a.id=b.prod_id and b.mst_id=c.mst_id and b.id=c.trans_id and b.transaction_type=4 and b.item_category=2 and b.mst_id='$data[3]' and b.status_active=1 and b.is_deleted=0";
            		// echo $sql_dtls;
					$sql_result = sql_select($sql_dtls);
					$order_ids='';
					foreach($sql_result as $row)
					{
						$order_ids.=$row[csf("order_id")].',';
					}
					$order_ids=chop($order_ids,',');
					if($order_ids!="")
					{
						$sql_job="SELECT a.id, a.po_number,	a.job_no_mst,a.file_no,a.grouping, b.style_ref_no,b.buyer_name from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.id in($order_ids)";
						$result_sql_job=sql_select($sql_job);
						$style_arr=array();$po_array=array();
						foreach($result_sql_job as $row)
						{
							if($style_ref_no=='') $style_ref_no=$row[csf("style_ref_no")]; else $style_ref_no.=','.$row[csf("style_ref_no")];
							$po_array[$row[csf("id")]]['po']=$row[csf("po_number")];
							$po_array[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
							$style_arr[$row[csf("id")]]=$row[csf("style_ref_no")];
							$jobs_arr[$row[csf("id")]]=$row[csf("job_no_mst")];
							$int_ref_arr[$row[csf("id")]]=$row[csf("grouping")];
						}
					}

					foreach($sql_result as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$po_no= array_filter(array_unique(explode(",",$row[csf("order_id")])));
						$order_nos="";$style_ref_nos="";$req_dia_arr="";$po_file='';$po_ref='';$po_buyer=''; $job_ref_nos=""; $int_ref_nos="";
						$check_buyer_arr = array();
						foreach($po_no as $val)
						{
							if ($style_ref_nos=="") $style_ref_nos=$style_arr[$val]; else $style_ref_nos.=",".$style_arr[$val];
							if ($job_ref_nos=="") $job_ref_nos=$jobs_arr[$val]; else $job_ref_nos.=",".$jobs_arr[$val];
							if ($int_ref_nos=="") $int_ref_nos=$int_ref_arr[$val]; else $int_ref_nos.=",".$int_ref_arr[$val];

							if($check_buyer_arr[$po_array[$val]['buyer']] != $po_array[$val]['buyer'])
							{
								if($po_buyer=="") $po_buyer = $buyer_library[$po_array[$val]['buyer']]; else $po_buyer .=", ".$buyer_library[$po_array[$val]['buyer']];
								$check_buyer_arr[$po_array[$val]['buyer']] = $po_array[$val]['buyer'];
							}
						}
						$po_buyer= array_filter(array_unique(explode(",",$po_buyer)));
						$style_no= array_filter(array_unique(explode(",",$style_ref_nos)));
						$job_no= array_filter(array_unique(explode(",",$job_ref_nos)));
						$int_ref_no= array_filter(array_unique(explode(",",$int_ref_nos)));
						$po_buyer = implode(",", $po_buyer);
						$style_no = implode(",", $style_no);
						$job_no = implode(",", $job_no);
						$int_ref_no = implode(",", $int_ref_no);
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $i; ?></td>
							<td><p><? echo $store_arr[$row[csf("store_id")]]; ?></p></td>
							<td><p><? echo 'Buyer: '.$po_buyer.'<br>Style No: '.$style_no.'<br> Job No: '.$job_no.'<br> Internal Ref: '.$int_ref_no; ?></p></td>
							<td><p><? echo $batch_arr[$row[csf('batch_id_from_fissuertn')]]['batch']; ?></p></td>
							<td><p><? echo $row[csf("product_name_details")]; ?></p></td>
							<td><p><? echo $color_arr[$batch_arr[$row[csf('batch_id_from_fissuertn')]]['color']]; ?></p></td>
							<td><? echo $floor_room_rack_name_arr[$row[csf("rack")]]; ?></td>
							<td><? echo $floor_room_rack_name_arr[$row[csf("self")]]; ?></td>
							<td align="right"><? echo $row[csf("no_of_roll")]; ?></td>
							<td align="right"><? echo number_format($row[csf("cons_quantity")],2,'.',''); ?></td>
							<td><p><? echo $row[csf("remarks")]; ?></p></td>
						</tr>
						<?
						$cons_quantity_sum+=$row[csf('cons_quantity')];
						$i++;
					} ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="9" align="right">Total :</td>
						<td align="right"><? echo number_format($cons_quantity_sum,2,'.',''); ?></td>
						<td></td>
					</tr>
				</tfoot>
			</table>
			<br>
			<?
			echo signature_table(88, $data[0], "900px");
			?>
		</div>
	</div>
	<?
	exit();
}
?>
