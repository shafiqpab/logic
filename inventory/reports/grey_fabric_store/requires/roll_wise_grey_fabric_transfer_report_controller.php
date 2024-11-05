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
	//if($data[1]==1) $party="1,3,21,90"; else $party="80";
	$party="1,3,21,90";
	echo create_drop_down( "cbo_buyer_id", 110, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}

if ($action=="load_drop_down_from_store")
{
	echo create_drop_down( "cbo_from_store_id", 110, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id='$data' and  b.category_type in(13)  group by a.id,a.store_name","id,store_name", 1, "--Select Store--", 0, "",$disable );
	exit();
}

if ($action=="load_drop_down_to_store")
{
	echo create_drop_down( "cbo_to_store_id", 110, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id='$data' and  b.category_type in(13)  group by a.id,a.store_name","id,store_name", 1, "--Select Store--", 0, "",$disable );
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));	

	$cbo_transfer_criteria=str_replace("'","",$cbo_transfer_criteria);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_to_company_id=str_replace("'","",$cbo_to_company_id);
	$cbo_item_cat=str_replace("'","",$cbo_item_cat);
	$cbo_from_store_id=str_replace("'","",$cbo_from_store_id);
	$cbo_to_store_id=str_replace("'","",$cbo_to_store_id);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);

	$search_cond="";
	if($cbo_transfer_criteria) $search_cond .=" and a.transfer_criteria = $cbo_transfer_criteria";
	if($cbo_company_id) $search_cond .=" and a.company_id = $cbo_company_id";
	if($cbo_buyer_id > 0) $search_cond .=" and e.buyer_name=$cbo_buyer_id";
	if($cbo_from_store_id) $search_cond .=" and b.from_store=$cbo_from_store_id";
	if($cbo_to_store_id) $search_cond .=" and b.to_store=$cbo_to_store_id";

	if($date_from !="" && $date_to != "")
	{
		$search_cond .= " and a.transfer_date between '$date_from' and '$date_to' ";
	}

	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$delivery_challan_arr=return_library_array( "select id, sys_number from pro_grey_prod_delivery_mst", "id", "sys_number"  );
	$brand_library=return_library_array("select id, brand_name from lib_brand","id","brand_name");
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

	$sql = "SELECT a.id as mst_id, a.transfer_date , b.from_store, b.to_store, b.from_order_id, b.y_count, b.stitch_length, b.feb_description_id, b.machine_no_id, b.yarn_lot, b.brand_id, b.gsm, b.dia_width, b.knit_program_id as challan_id , sum(c.qnty) as transfer_qnty, 
	count(c.roll_id) as tot_roll, c.roll_id, c.roll_no, c.booking_without_order, c.po_breakdown_id, e.buyer_name 
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, wo_po_break_down d, wo_po_details_master e
	where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no
	and a.to_company=$cbo_to_company_id $search_cond and c.entry_form=82 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by a.id, a.transfer_date , b.from_store, b.to_store, b.from_order_id, b.y_count, b.stitch_length, b.feb_description_id, b.machine_no_id, b.yarn_lot, b.brand_id, b.gsm, b.dia_width, b.knit_program_id , c.booking_without_order, c.roll_id, c.roll_no, c.po_breakdown_id, e.buyer_name order by e.buyer_name";
	// echo $sql;
	$nameArray=sql_select($sql);
	$all_roll_id='';
	$tot_no_of_roll=$tot_transfer_qnty=0;
	foreach ($nameArray as $row)
	{
		$tot_no_of_roll += $row[csf("roll_no")];
		$tot_transfer_qnty += $row[csf("transfer_qnty")];

		$all_roll_id.=$row[csf("roll_id")].',';
	}
	$all_roll_id=chop($all_roll_id,",");
	$roll_id_arr=array_unique(explode(",",$all_roll_id));
	// echo "<pre>";print_r($roll_id_arr);echo "<pre>";die;

	$all_roll_id_cond=""; $roll_idCond="";
	if($db_type==2 && count($roll_id_arr)>999)
	{
		$all_delv_batch_id_arr_chunk=array_chunk($roll_id_arr,999) ;
		foreach($all_delv_batch_id_arr_chunk as $chunk_arr)
		{
			$chunk_batch_arr_value=implode(",",$chunk_arr);
			$roll_idCond.="  d.id in($chunk_batch_arr_value) or ";
		}

		$all_roll_id_cond.=" and (".chop($roll_idCond,'or ').")";
	}
	else
	{
		$all_roll_id_cond=" and d.id in($all_roll_id)";
	}
	if($all_roll_id!="")
	{
		$production_sql=sql_select("SELECT b.receive_basis, b.booking_id, b.booking_no, c.id as prod_detls_id, c.color_id, c.color_range_id, c.machine_dia, c.machine_gg, c.machine_no_id, c.yarn_lot, d.id as roll_id, d.roll_no, d.barcode_no
		from inv_receive_master b, pro_grey_prod_entry_dtls c , pro_roll_details d
		where b.id=c.mst_id and c.id=d.dtls_id $all_roll_id_cond"); //and d.roll_id=0
		$production_delivery_data=array();	
		foreach($production_sql as $row)
		{
			$production_delivery_data[$row[csf("roll_id")]]["receive_basis"]=$row[csf("receive_basis")];
			$production_delivery_data[$row[csf("roll_id")]]["booking_id"]=$row[csf("booking_id")];
			$production_delivery_data[$row[csf("roll_id")]]["booking_no"]=$row[csf("booking_no")];
			$production_delivery_data[$row[csf("roll_id")]]["color_id"]=$row[csf("color_id")];
			$production_delivery_data[$row[csf("roll_id")]]["color_range_id"]=$row[csf("color_range_id")];
			$production_delivery_data[$row[csf("roll_id")]]["machine_dia"]=$row[csf("machine_dia")];
			$production_delivery_data[$row[csf("roll_id")]]["machine_gg"]=$row[csf("machine_gg")];
			$production_delivery_data[$row[csf("roll_id")]]["machine_no_id"]=$row[csf("machine_no_id")];
			$production_delivery_data[$row[csf("roll_id")]]["yarn_lot"]=$row[csf("yarn_lot")];

			if($row[csf("receive_basis")] == 2)
			{
				$program_id_arr[$row[csf("booking_id")]] = $row[csf("booking_id")];
			}
		}
	}

	$all_program_id_cond=""; $program_idCond="";
	if($db_type==2 && count($program_id_arr)>999)
	{
		$all_program_id_arr_chunk=array_chunk($program_id_arr,999) ;
		foreach($all_program_id_arr_chunk as $chunk_arr)
		{
			$chunk_prog_arr_value=implode(",",$chunk_arr);
			$program_idCond.="  b.id in($chunk_prog_arr_value) or ";
		}

		$all_program_id_cond.=" and (".chop($program_idCond,'or ').")";
	}
	else
	{
		$all_program_id_cond=" and b.id in (".implode(",", $program_id_arr).")";
	}
	if(!empty($program_id_arr))
	{
		$program_sql=sql_select("SELECT b.id, a.booking_no
		from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id  = b.mst_id and b.status_active=1 and b.is_deleted=0 $all_program_id_cond");
		$booking_from_program=array();	
		foreach($program_sql as $row)
		{
			$booking_from_program[$row[csf("id")]]=$row[csf("booking_no")];
		}
	}

	$all_booking="";
	$transfer_data_arr=array();
	foreach($nameArray as $row)
	{
		$rol_id=$row[csf("roll_id")];
		if($production_delivery_data[$rol_id]["receive_basis"] == 2)
		{
			$all_booking = $booking_from_program[$production_delivery_data[$rol_id]["booking_id"]];
		}
		else
		{
			$all_booking=$production_delivery_data[$rol_id]["booking_no"];
		}
		$color_id=$production_delivery_data[$rol_id]["color_id"];
		//echo $all_booking.'<br>';

		//$transfer_data_arr[$row[csf("buyer_name")]][$all_booking][$row[csf("challan_id")]][$color_id][$row[csf("feb_description_id")]][$row[csf("dia_width")]][$row[csf("y_count")]][$row[csf("brand_id")]][$row[csf("yarn_lot")]][$row[csf("gsm")]][$row[csf("stitch_length")]]['transfer_qnty']+=$row[csf("transfer_qnty")];

		$pre_key=$all_booking."*".$row[csf('challan_id')]."*".$color_id."*".$row[csf('feb_description_id')]."*".$row[csf('dia_width')]."*".$row[csf('y_count')]."*".$row[csf('brand_id')]."*".$row[csf('yarn_lot')]."*".$row[csf('gsm')]."*".$row[csf('stitch_length')];

		$transfer_data_arr[$buyer_arr[$row[csf('buyer_name')]]][$pre_key]['transfer_date']=$row[csf('transfer_date')];
		$transfer_data_arr[$buyer_arr[$row[csf('buyer_name')]]][$pre_key]['transfer_qnty']+=$row[csf('transfer_qnty')];
		$transfer_data_arr[$buyer_arr[$row[csf('buyer_name')]]][$pre_key]['roll_no']=$row[csf('roll_no')];
		$transfer_data_arr[$buyer_arr[$row[csf('buyer_name')]]][$pre_key]['tot_roll']+=$row[csf('tot_roll')];
	}
	// echo "<pre>";print_r($transfer_data_arr);


	$i=1; $total_roll_no=0;$total_transfer_qnty=0;
	foreach ($transfer_data_arr as $buyer_key => $buyerVal)
	{
		foreach ($buyerVal as $dtls_key => $row)
		{
			$total_roll_no += $row["tot_roll"];
			$total_transfer_qnty += $row["transfer_qnty"];
			$i++;
		}
	}

	ob_start();
	?>
	<style type="text/css">
		.word_wrap_break {
			word-break: break-all;
			word-wrap: break-word;
		}
	</style>
	<fieldset style="width:1310px;">
		<table cellpadding="0" cellspacing="0" width="1270">
			<tr  class="form_caption" style="border:none;">
				<td align="center"  colspan="13" style="font-size:18px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
				<td align="center"  colspan="13" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center"  colspan="13" style="font-size:14px;color: black;"><strong> <? if($date_from!="") echo "From Store: ".$store_arr[$cbo_from_store_id].", To Store: ".$store_arr[$cbo_to_store_id];?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
				<td align="center"  colspan="13" style="font-size:14px"><strong> <? if($date_from!="") echo "From Date : ".change_date_format(str_replace("'","",$txt_date_from)).",  To Date : ".change_date_format(str_replace("'","",$txt_date_to));?></strong></td>
			</tr>
		</table>

		<table width="1310" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
			<tfoot>
				<tr>
					<th width="30">&nbsp;</th>
					<th width="70">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="90" title="Challan No">&nbsp;</th>
					<th width="90" title="color">&nbsp;</th>
					<th width="80">&nbsp;</th> 
					<th width="60">&nbsp;</th>
					<th width="90">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="120">&nbsp;</th>
					<th width="50">&nbsp;</th>
					<th width="100">Total:</th>
					<th width="50" align="right" id="value_trans_roll"><? echo number_format($total_roll_no,2);?></th>
					<th width="80" align="right" id="value_trans_qnty"><? echo number_format($total_transfer_qnty,2);?></th>
				</tr>
			</tfoot>
		</table>

		<table width="1310" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="70">Transfer Date</th>
					<th width="80">Buyer</th>
					<th width="100">Booking No.</th>
					<th width="90">Challan No</th>
					<th width="90">Fin. Color</th>
					<th width="80">Fabric Type</th> 
					<th width="60">Finish Dia</th>
					<th width="90">Count</th>
					<th width="80">Brand</th>
					<th width="120">Yarn Lot</th>
					<th width="50">GSM</th>
					<th width="100">Stich Length</th>
					<th width="50">No of Roll</th>
					<th width="80">Transfer Qty (Kg)</th>
				</tr>
			</thead>
		</table>
		<div style="width:1330px; max-height:350px; overflow-y:scroll;" id="scroll_body">
			<table width="1310" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" > 
				<?
				$i=1; $total_roll_no=0; $total_transfer_qnty=0;
				ksort($transfer_data_arr);
				foreach ($transfer_data_arr as $buyer_key => $buyerVal)
				{
					foreach ($buyerVal as $dtls_key => $row)
					{
						$data = explode("*", $dtls_key);
						$booking=$data[0];
						$deli_challan=$data[1];
						$color=$data[2];
						$fabric_desc_id=$data[3];
						$dia=$data[4];
						$yarn_count=$data[5];
						$brand=$data[6];
						$yarn_lot=$data[7];
						$gsm=$data[8];
						$stich_length=$data[9];

						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
							<td width="30"><? echo $i;?></td>
							<td width="70" class="word_wrap_break"><p><? echo change_date_format($row["transfer_date"]); ?></p></td>
							<td width="80" class="word_wrap_break"><p><? echo $buyer_key; ?></p></td>
							<td width="100" class="word_wrap_break"><p><? echo $booking; ?></p></td>
							<td width="90" class="word_wrap_break"><p><? echo $delivery_challan_arr[$deli_challan]; ?></p></td>
							<td width="90" align="center" class="word_wrap_break"><p><? echo $color_arr[$color]; ?></p></th>
							<td width="80" class="word_wrap_break"><p><? echo $constructtion_arr[$fabric_desc_id]; ?></p></td> 
							<td width="60" align="center" class="word_wrap_break"><p><? echo $dia; ?></p></td>
							<td width="90" class="word_wrap_break" align="center"><p><? echo $yarn_count_arr[$yarn_count]; ?></p></td>
							<td width="80" class="word_wrap_break" align="center"><p><? echo $brand_library[$brand]; ?></p></td>
							<td width="120" class="word_wrap_break" align="center"><? echo $yarn_lot; ?></td>
							<td width="50" align="center" class="word_wrap_break"><p><? echo $gsm; ?></p></td>
							<td width="100" class="word_wrap_break" align="center"><p><? echo $stich_length; ?></p></td>
							<td width="50" align="right" class="word_wrap_break"><? echo $row["tot_roll"]; ?></td>
							<td width="80" align="right" class="word_wrap_break"><? echo $row["transfer_qnty"]; ?></td>
						</tr>
						<?
						$total_roll_no += $row["tot_roll"];
						$total_transfer_qnty += $row["transfer_qnty"];
						$i++;
					}
				}
				?>								
			</table>
			</div> 
		</fieldset>         
		<?
	
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    	@unlink($filename);
    }
    
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$summary_html"; 
    exit();
}
?>