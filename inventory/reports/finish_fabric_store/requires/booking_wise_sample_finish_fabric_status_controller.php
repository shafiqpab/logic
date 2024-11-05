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
	//$data=explode("**",$data);
	//if($data[1]==2) $disable=1; else $disable=0;

	//$store_cond = ($userCredential[0][csf("store_location_id")]) ? " and a.id in (".$userCredential[0][csf("store_location_id")].")" : "" ;
	echo create_drop_down( "cbo_store_id", 120, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id='$data' and  b.category_type in(2,3)  group by a.id,a.store_name","id,store_name", 1, "--Select Store--", 0, "",$disable );
	exit();
}


if ($action=="booking_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'1','');
	extract($_REQUEST);
	$data=explode('_',$data);
	$company_id=$data[0];
	$buyer_id=$data[1];
	$year_id=$data[2];

	?>
	<script>

		var selected_id = new Array(); var selected_name = new Array();

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str )
		{

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}

			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#hdn_booking_id').val(id);
			$('#hdn_booking_no').val(name);
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
								<th width="100">Job No</th>
								<th width="150">Booking No</th>
								<th width="200">Date Range</th>
								<th>
									<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
									<input type="hidden" name="hdn_booking_no" id="hdn_booking_no" value="">
									<input type="hidden" name="hdn_booking_id" id="hdn_booking_id" value="">
								</th>
							</thead>
							<tr>
								<td>
									<?
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_id,"",0 );
									?>
								</td>
								<td>
									<input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px">
								</td>
								<td>
									<input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:150px">
								</td>
								<td>
									<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
									<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
								</td>
								<td align="center">
									<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $company_id; ?>'+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('txt_job_no').value,'create_booking_search_list_view', 'search_div', 'booking_wise_sample_finish_fabric_status_controller','setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" /></td>
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
	$job_no=$data[5];

	if($db_type==0)
	{
		if ($data[1]!="" &&  $data[2]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
		if ($data[1]!="" &&  $data[2]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[1],'','',1)."' and '".change_date_format($data[2],'','',1)."'"; else $booking_date ="";
	}

	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$booking_cond = ($booking_no!="")?" and a.booking_no_prefix_num=$booking_no":"";
	$job_no_cond = ($job_no!="")?" and b.job_no like '%$job_no%'":"";

	$sql= "select a.company_id, a.booking_no_prefix_num, a.id, a.booking_no, a.booking_date, a.entry_form, a.booking_type, a.is_short, a.buyer_id, b.job_no, b.style_ref_no from wo_booking_mst a,wo_booking_dtls d, wo_po_details_master b where company_id=$company $booking_cond $job_no_cond $booking_date and a.booking_no=d.booking_no and d.job_no=b.job_no and a.status_active =1 and a.is_deleted =0 and a.item_category=2 and a.booking_type in (1,4)  group by a.company_id, a.booking_no_prefix_num, a.id, a.booking_no, a.booking_date, a.entry_form, a.booking_type, a.is_short,  a.buyer_id,b.job_no, b.style_ref_no order by a.id desc";

	?>
	<br>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" align="left">
		<thead>
			<th width="40">SL</th>
			<th width="90">Company</th>
			<th width="100">Buyer</th>
			<th width="110">Style Ref.</th>
			<th width="90">Job No</th>
			<th width="100">Booking No</th>
			<th width="90">Booking Type</th>
			<th width="100">Booking Date</th>
		</thead>
	</table>
	<div style="width:670px; max-height:265px; overflow-y:scroll; float: left;" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" id="tbl_list_search" align="left">
			<?
			$i=1;
			$result = sql_select($sql);
			foreach ($result as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if($row[csf('booking_type')]==4)
				{
					$booking_type = "Sample";
				}
				else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 2)
				{
					$booking_type = "Main";
				}
				else if($row[csf('booking_type')]==1 && $row[csf('is_short')] == 1)
				{
					$booking_type = "Short";
				}

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $i; ?>','<? echo $row[csf('is_approved')]; ?>')" id="search<? echo $i;?>">
					<td width="40" align="center">
						<? echo $i; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
						<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf("booking_no")]; ?>"/>
					</td>
					<td width="90" align="center"><? echo $company_arr[$row[csf('company_id')]]; ?></td>
					<td width="100" align="center"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
					<td width="110" align="center"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
					<td width="90" align="center"><? echo $row[csf('job_no')]; ?></td>
					<td width="100" align="center"><? echo $row[csf('booking_no')]; ?></td>
					<td width="90" align="center"><? echo $booking_type; ?></td>
					<td width="100" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<table width="650" cellspacing="0" cellpadding="0" style="border:none" align="left">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%">
					<div style="width:50%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
					</div>
					<div style="width:50%; float:left" align="left">
						<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
					</div>
				</div>
			</td>
		</tr>
	</table>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	<?
	exit();
}

if($action=="report_generate")
{
	$started = microtime(true);
	$process = array( &$_POST );
	
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_id 	= str_replace("'","",$cbo_company_id);
	$buyer_id 			= str_replace("'","",$cbo_buyer_id);
	$cbo_search_by 		= str_replace("'","",$cbo_search_by);
	$txt_search_comm 	= trim(str_replace("'","",$txt_search_comm));
	$cbo_year 			= str_replace("'","",$cbo_year);
	$cbo_value_with 	= str_replace("'","",$cbo_value_with);
	$cbo_store_id   	= str_replace("'","",$cbo_store_id);
	$txt_date_from 		= str_replace("'","",$txt_date_from);

	if($cbo_store_id) $rcvStore = " and b.store_id in(".$cbo_store_id.")";
	if($cbo_store_id) $tranqty = " and c.store_id in(".$cbo_store_id.")";

	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_id=$buyer_id";
	}

	if($db_type==0)
	{
		if($cbo_year==0) $year_cond=""; else $year_cond=" and YEAR(a.insert_date)=$cbo_year";
	}
	else if($db_type==2)
	{
		if($cbo_year==0) $year_cond=""; else $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
	}

	

	$company_arr 	= return_library_array("select id, company_name from lib_company where status_active=1","id","company_name");
	$buyer_arr 		= return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_arr 		= return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$color_arr 		= return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$sample_arr 	= return_library_array( "select id,sample_name from lib_sample", "id", "sample_name"  );
	$dealing_marchant_arr 	= return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );

	$booking_type_arr = array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");

	if($txt_search_comm!="" || $buyer_id !=0)
	{
		if($txt_search_comm!="")
		{
			if($cbo_search_by == 1)
			{
				$reference_cond = " and a.booking_no_prefix_num = '$txt_search_comm'";
			}
			elseif($cbo_search_by == 2)
			{
				$reference_cond = " and c.internal_ref='$txt_search_comm'";
			}
			elseif ($cbo_search_by == 3) 
			{
				$reference_cond = " and c.requisition_number_prefix_num='$txt_search_comm'";
			}
		}

		$booking_sql="SELECT a.buyer_id,a.dealing_marchant, c.requisition_number,a.id, a.booking_no, c.style_ref_no, a.booking_date, c.internal_ref as grouping, b.fabric_color, b.body_part, b.lib_yarn_count_deter_id, b.sample_type, b.finish_fabric from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b left join sample_development_mst c on b.style_id=c.id where a.booking_no=b.booking_no and a.booking_type=4 and a.company_id =$cbo_company_id $buyer_id_cond $reference_cond $year_cond  and b.status_active=1 and b.is_deleted=0";
		// echo $booking_sql;
		$booking_result=sql_select($booking_sql);

		foreach ($booking_result as $val) 
		{
			$bookArr[$val[csf("id")]] = $val[csf("id")];
			$booking_data[$val[csf("booking_no")]]["booking_no"] = $val[csf("booking_no")];
			$booking_data[$val[csf("booking_no")]]["style_ref_no"] = $val[csf("style_ref_no")];
			$booking_data[$val[csf("booking_no")]]["booking_date"] = date("Y",strtotime($val[csf("booking_date")]));
			$booking_data[$val[csf("booking_no")]]["buyer_id"] = $val[csf("buyer_id")];
			$booking_data[$val[csf("booking_no")]]["requisition_number"] = $val[csf("requisition_number")];
			$booking_data[$val[csf("booking_no")]]["grouping"] = $val[csf("grouping")];
			$booking_data[$val[csf("booking_no")]]["dealing_marchant"] = $val[csf("dealing_marchant")];
			$booking_data[$val[csf("booking_no")]]["sample_type"] = $val[csf("sample_type")];
			// $booking_ref_arr[$val[csf("booking_no")]][$val[csf("fabric_color")]][$val[csf("body_part")]][$val[csf("lib_yarn_count_deter_id")]]["sample_type"] = $val[csf("sample_type")];
			$booking_ref_arr[$val[csf("booking_no")]][$val[csf("fabric_color")]][$val[csf("body_part")]][$val[csf("lib_yarn_count_deter_id")]]["quantity"] += $val[csf("finish_fabric")];
		}
		unset($booking_result);

		$bookArr = array_filter($bookArr);
		if(!empty($bookArr))
		{
			$booking_ids= implode(",",$bookArr);

			$all_booking_ids_cond=""; $bookCond="";
			if($db_type==2 && count($bookArr)>999)
			{
				$bookArr_chunk=array_chunk($bookArr,999) ;
				foreach($bookArr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$bookCond.=" e.booking_no_id in($chunk_arr_value) or ";
				}
				$all_booking_ids_cond.=" and (".chop($bookCond,'or ').")";
			}
			else
			{
				$all_booking_ids_cond=" and e.booking_no_id in($booking_ids)";
			}
		}
		else
		{
			echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 20px;">Data not found.</div>';
			disconect();die();
		}
	}

	if($txt_search_comm!="" || $buyer_id ==0 && $cbo_year!=0)
	{
		if($txt_search_comm!="")
		{
			if($cbo_search_by == 1)
			{
				$reference_cond = " and a.booking_no_prefix_num = '$txt_search_comm'";
			}
			elseif($cbo_search_by == 2)
			{
				$reference_cond = " and c.internal_ref='$txt_search_comm'";
			}
			elseif ($cbo_search_by == 3) 
			{
				$reference_cond = " and c.requisition_number_prefix_num='$txt_search_comm'";
			}
		}

		$booking_sql="SELECT a.buyer_id,a.dealing_marchant, c.requisition_number,a.id, a.booking_no, c.style_ref_no, a.booking_date, c.internal_ref as grouping, b.fabric_color, b.body_part, b.lib_yarn_count_deter_id, b.sample_type, b.finish_fabric from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b left join sample_development_mst c on b.style_id=c.id where a.booking_no=b.booking_no and a.booking_type=4 and a.company_id =$cbo_company_id $buyer_id_cond $reference_cond $year_cond  and b.status_active=1 and b.is_deleted=0";
		 //echo $booking_sql;
		$booking_result=sql_select($booking_sql);

		foreach ($booking_result as $val) 
		{
			$bookArr[$val[csf("id")]] = $val[csf("id")];
			$booking_data[$val[csf("booking_no")]]["booking_no"] = $val[csf("booking_no")];
			$booking_data[$val[csf("booking_no")]]["style_ref_no"] = $val[csf("style_ref_no")];
			$booking_data[$val[csf("booking_no")]]["booking_date"] = date("Y",strtotime($val[csf("booking_date")]));
			$booking_data[$val[csf("booking_no")]]["buyer_id"] = $val[csf("buyer_id")];
			$booking_data[$val[csf("booking_no")]]["requisition_number"] = $val[csf("requisition_number")];
			$booking_data[$val[csf("booking_no")]]["grouping"] = $val[csf("grouping")];
			$booking_data[$val[csf("booking_no")]]["dealing_marchant"] = $val[csf("dealing_marchant")];
			$booking_data[$val[csf("booking_no")]]["sample_type"] = $val[csf("sample_type")];
			// $booking_ref_arr[$val[csf("booking_no")]][$val[csf("fabric_color")]][$val[csf("body_part")]][$val[csf("lib_yarn_count_deter_id")]]["sample_type"] = $val[csf("sample_type")];
			$booking_ref_arr[$val[csf("booking_no")]][$val[csf("fabric_color")]][$val[csf("body_part")]][$val[csf("lib_yarn_count_deter_id")]]["quantity"] += $val[csf("finish_fabric")];
		}
		unset($booking_result);

		$bookArr = array_filter($bookArr);
		if(!empty($bookArr))
		{
			$booking_ids= implode(",",$bookArr);

			$all_booking_ids_cond=""; $bookCond="";
			if($db_type==2 && count($bookArr)>999)
			{
				$bookArr_chunk=array_chunk($bookArr,999) ;
				foreach($bookArr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$bookCond.=" e.booking_no_id in($chunk_arr_value) or ";
				}
				$all_booking_ids_cond.=" and (".chop($bookCond,'or ').")";
			}
			else
			{
				$all_booking_ids_cond=" and e.booking_no_id in($booking_ids)";
			}
		}
		else
		{
			echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 20px;">Data not found.</div>';
			disconect();die();
		}
	}
	
	$rcv_sql = "SELECT b.id,e.booking_no, e.booking_no_id, a.company_id, b.transaction_date, b.prod_id, b.store_id, c.body_part_id, c.fabric_description_id, c.color_id, b.cons_quantity, b.pi_wo_batch_no
		FROM inv_receive_master a, inv_transaction b, pro_finish_fabric_rcv_dtls c, pro_batch_create_mst e
		WHERE a.company_id =$cbo_company_id and a.id=b.mst_id and b.id=c.trans_id and b.transaction_type=1 and a.entry_form=37 and a.status_active =1 and b.status_active =1 and c.status_active =1  and b.pi_wo_batch_no=e.id and a.entry_form=37 and e.booking_without_order=1 and b.transaction_date <= '$txt_date_from' $all_booking_ids_cond $rcvStore
		group by b.id,e.booking_no,e.booking_no_id, a.company_id, b.transaction_date, b.prod_id, b.store_id, c.body_part_id, c.fabric_description_id, 
		c.color_id,c.dia_width_type,b.cons_quantity,b.pi_wo_batch_no
		order by b.id desc";
		 //echo $rcv_sql;
	$rcv_data = sql_select($rcv_sql);
	foreach ($rcv_data as  $val)
	{
		$ref_str = $val[csf("body_part_id")]."*".$val[csf("fabric_description_id")]."*".$val[csf("color_id")];
		$recvDtlsDataArr[$val[csf("booking_no")]][$ref_str]['recv']+=$val[csf("cons_quantity")];

		$storeWiseStock[$val[csf("booking_no")]][$ref_str][$val[csf("store_id")]]+=$val[csf("cons_quantity")];
		$book_store_stock[$val[csf("booking_no")]][$val[csf("store_id")]]+=$val[csf("cons_quantity")];

		if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($val[csf('transaction_date')]))
		{
			$recvDtlsDataArr[$val[csf("booking_no")]][$ref_str]['today_recv'] +=$val[csf("cons_quantity")];
		}

		$batch_id_arr[$val[csf("pi_wo_batch_no")]] = $val[csf("pi_wo_batch_no")];
		$allStoreArr[$val[csf("store_id")]] =$val[csf("store_id")];

		$without_search_book_id_arr[$val[csf("booking_no_id")]] =$val[csf("booking_no_id")];
	}
	unset($rcv_data);
	/*echo "<pre>";
	print_r($recvDtlsDataArr);die;*/

	$trans_in_sql = "SELECT c.transaction_date, c.pi_wo_batch_no, e.batch_no,e.booking_no, e.booking_no_id, c.body_part_id, c.prod_id, c.store_id, d.detarmination_id, d.color as color_id,  sum(c.cons_quantity) as quantity
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c,  product_details_master d, pro_batch_create_mst e
	where a.id=b.mst_id and b.to_trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id=$cbo_company_id and c.item_category=2 and c.transaction_type=5 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.entry_form in (14,15,306)  and e.booking_without_order=1 and c.transaction_date <= '$txt_date_from' $all_booking_ids_cond $tranqty
	group by c.transaction_date, c.pi_wo_batch_no, e.batch_no,e.booking_no, e.booking_no_id, c.company_id, c.body_part_id, c.prod_id,c.store_id, d.detarmination_id, d.color";

	$trans_in_data = sql_select($trans_in_sql);
	foreach ($trans_in_data as  $val)
	{
		$ref_str = $val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("color_id")];
		$recvDtlsDataArr[$val[csf("booking_no")]][$ref_str]['transfer_in']+=$val[csf("quantity")];
		
		$storeWiseStock[$val[csf("booking_no")]][$ref_str][$val[csf("store_id")]]+=$val[csf("quantity")];
		$book_store_stock[$val[csf("booking_no")]][$val[csf("store_id")]]+=$val[csf("quantity")]; 
		
		if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($val[csf('transaction_date')]))
		{
			$recvDtlsDataArr[$val[csf("booking_no")]][$ref_str]['today_transfer_in'] +=$val[csf("quantity")];
		}

		$batch_id_arr[$val[csf("pi_wo_batch_no")]] = $val[csf("pi_wo_batch_no")];
		$allStoreArr[$val[csf("store_id")]] =$val[csf("store_id")];
		$without_search_book_id_arr[$val[csf("booking_no_id")]] =$val[csf("booking_no_id")];
	}
	unset($trans_in_data);
	
	$batch_id_arr = array_filter($batch_id_arr);
	if(!empty($batch_id_arr))
	{
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

		$issRtnSql = "SELECT c.transaction_date, d.knit_dye_source, b.body_part_id, b.prod_id, c.store_id, b.fabric_description_id, b.gsm, b.width, b.color_id,c.cons_uom, c.cons_quantity as quantity, c.order_rate, b.batch_id, e.batch_no, e.booking_no, e.booking_without_order from inv_receive_master a, pro_finish_fabric_rcv_dtls b,inv_transaction c, inv_issue_master d, pro_batch_create_mst e where a.id=b.mst_id and b.trans_id =c.id and c.issue_id =d.id and a.entry_form =52 and a.item_category =2 and c.pi_wo_batch_no =e.id and a.status_active =1 and b.status_active=1 and c.status_active =1 and c.company_id=$cbo_company_id $all_batch_ids_cond $tranqty and c.transaction_date <= '$txt_date_from'";
		$issRtnData = sql_select($issRtnSql);
		foreach ($issRtnData as $val)
		{
			$ref_str = $val[csf("body_part_id")]."*".$val[csf("fabric_description_id")]."*".$val[csf("color_id")];
			$recvDtlsDataArr[$val[csf("booking_no")]][$ref_str]['issue_return']+=$val[csf("quantity")];
			$storeWiseStock[$val[csf("booking_no")]][$ref_str][$val[csf("store_id")]]+=$val[csf("quantity")];

			$book_store_stock[$val[csf("booking_no")]][$val[csf("store_id")]]+=$val[csf("quantity")]; 
			
			if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($val[csf('transaction_date')]))
			{
				$recvDtlsDataArr[$val[csf("booking_no")]][$ref_str]['today_issue_return'] +=$val[csf("quantity")];
			}


			$allStoreArr[$val[csf("store_id")]] =$val[csf("store_id")];
		}
		unset($issRtnData);

		$issue_sql = sql_select("SELECT b.body_part_id, c.store_id, c.cons_quantity, c.transaction_date, d.detarmination_id, d.color, e.booking_no, e.booking_without_order from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e where a.id=b.mst_id and b.trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id=$cbo_company_id $all_batch_ids_cond $tranqty and c.transaction_date <= '$txt_date_from' and a.entry_form=18 and c.status_active=1 and b.status_active=1 and a.status_active=1 and c.item_category=2 and c.transaction_type=2 group by b.body_part_id, c.store_id, c.cons_quantity, c.transaction_date, d.detarmination_id, d.color, e.booking_no, e.booking_without_order");

		foreach ($issue_sql as $val)
		{
			$ref_str = $val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("color")];
			$recvDtlsDataArr[$val[csf("booking_no")]][$ref_str]['issue']+=$val[csf("cons_quantity")];
			$storeWiseStock[$val[csf("booking_no")]][$ref_str][$val[csf("store_id")]]-=$val[csf("cons_quantity")];

			$book_store_stock[$val[csf("booking_no")]][$val[csf("store_id")]]-=$val[csf("cons_quantity")];

			if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($val[csf('transaction_date')]))
			{
				$recvDtlsDataArr[$val[csf("booking_no")]][$ref_str]['today_issue'] +=$val[csf("cons_quantity")];
			}
		}
		unset($issue_sql);

		$rcvRtnSql = sql_select("SELECT c.transaction_date, c.company_id, c.prod_id, c.store_id, c.cons_quantity, c.cons_uom, d.detarmination_id, d.gsm, d.dia_width, d.color, e.booking_no, e.booking_without_order, b.body_part_id from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e where a.id=b.mst_id and b.trans_id=c.id and a.entry_form =46 and c.company_id=$cbo_company_id $all_batch_ids_cond $tranqty and c.transaction_date <= '$txt_date_from' and c.prod_id=d.id and c.pi_wo_batch_no=e.id and a.status_active=1 and b.status_active=1 and c.status_active=1");

		foreach ($rcvRtnSql as $val)
		{
			$ref_str = $val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("color")];
			$recvDtlsDataArr[$val[csf("booking_no")]][$ref_str]['rcv_return']+=$val[csf("cons_quantity")];
			$storeWiseStock[$val[csf("booking_no")]][$ref_str][$val[csf("store_id")]] -= $val[csf("cons_quantity")];
			
			$book_store_stock[$val[csf("booking_no")]][$val[csf("store_id")]]-=$val[csf("cons_quantity")];
			
			if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($val[csf('transaction_date')]))
			{
				$recvDtlsDataArr[$val[csf("booking_no")]][$ref_str]['today_rcv_return'] +=$val[csf("cons_quantity")];
			}

		}
		unset($rcvRtnSql);

		$transOutSql = sql_select("SELECT c.transaction_date,c.pi_wo_batch_no, e.batch_no,e.booking_no, e.booking_no_id, e.booking_without_order, c.body_part_id, c.prod_id, c.store_id, d.detarmination_id, d.gsm, d.dia_width, d.color, c.cons_uom, c.cons_quantity, c.order_rate from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c,product_details_master d, pro_batch_create_mst e where a.id=b.mst_id and b.trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id =$cbo_company_id $all_batch_ids_cond $tranqty and c.transaction_date <= '$txt_date_from' and c.item_category=2 and c.transaction_type=6 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.entry_form in (14,15,306) and b.active_dtls_id_in_transfer=1");

		foreach ($transOutSql as $val)
		{
			$ref_str = $val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("color")];
			$recvDtlsDataArr[$val[csf("booking_no")]][$ref_str]['trans_out']+=$val[csf("cons_quantity")];
			$storeWiseStock[$val[csf("booking_no")]][$ref_str][$val[csf("store_id")]]-=$val[csf("cons_quantity")];
			$book_store_stock[$val[csf("booking_no")]][$val[csf("store_id")]]-=$val[csf("cons_quantity")];
			
			if(strtotime(str_replace("'","",$txt_date_from)) == strtotime($val[csf('transaction_date')]))
			{
				$recvDtlsDataArr[$val[csf("booking_no")]][$ref_str]['today_trans_out'] +=$val[csf("cons_quantity")];
			}
		}
		unset($transOutSql);

		$stores = sql_select("SELECT a.id,a.store_name from lib_store_location a,lib_store_location_category b,lib_company c where a.id=b.store_location_id and a.company_id=c.id and a.status_active=1 and a.is_deleted=0 and b.category_type in(2) and a.company_id=$cbo_company_id and a.id in (".implode(",", $allStoreArr).") group by a.id,a.store_name order by a.store_name asc");

		$num_of_store=0;
		foreach ($stores as $s_row)
		{
			$store_name_arr[$s_row[csf("id")]] = $s_row[csf("store_name")];
			$num_of_store++;
		}

	    $composition_arr=array();
	    $sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id order by b.id asc";
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

	    if($txt_search_comm=="" && $buyer_id ==0)
	    {
	    	$without_search_book_id_arr = array_filter($without_search_book_id_arr);
			if(!empty($without_search_book_id_arr))
			{
				$without_search_book_ids= implode(",",$without_search_book_id_arr);

				$all_without_serch_book_ids_cond=""; $bookCond="";
				if($db_type==2 && count($batch_id_arr)>999)
				{
					$without_search_book_id_chunk=array_chunk($without_search_book_id_arr,999) ;
					foreach($without_search_book_id_chunk as $chunk_arr)
					{
						$chunk_arr_value=implode(",",$chunk_arr);
						$bookCond.="  a.id in($chunk_arr_value) or ";
					}
					$all_without_serch_book_ids_cond.=" and (".chop($bookCond,'or ').")";
				}
				else
				{
					$all_without_serch_book_ids_cond=" and a.id in($without_search_book_ids)";
				}
			$booking_sql="SELECT a.buyer_id,a.dealing_marchant, c.requisition_number,a.id, a.booking_no, c.style_ref_no, a.booking_date, c.internal_ref as grouping, b.fabric_color, b.body_part, b.lib_yarn_count_deter_id, b.sample_type, b.finish_fabric from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b left join sample_development_mst c on b.style_id=c.id where a.booking_no=b.booking_no and a.company_id =$cbo_company_id $all_without_serch_book_ids_cond  and b.status_active=1 and b.is_deleted=0";
			// echo "$booking_sql";
		
			$booking_result=sql_select($booking_sql);
			foreach ($booking_result as $val) 
			{
				$booking_data[$val[csf("booking_no")]]["booking_no"] = $val[csf("booking_no")];
				$booking_data[$val[csf("booking_no")]]["style_ref_no"] = $val[csf("style_ref_no")];
				$booking_data[$val[csf("booking_no")]]["booking_date"] = date("Y",strtotime($val[csf("booking_date")]));
				$booking_data[$val[csf("booking_no")]]["buyer_id"] = $val[csf("buyer_id")];
				$booking_data[$val[csf("booking_no")]]["dealing_marchant"] = $val[csf("dealing_marchant")];
				$booking_data[$val[csf("booking_no")]]["requisition_number"] = $val[csf("requisition_number")];
				$booking_data[$val[csf("booking_no")]]["grouping"] = $val[csf("grouping")];
				$booking_data[$val[csf("booking_no")]]["sample_type"] = $val[csf("sample_type")];
				
				$booking_ref_arr[$val[csf("booking_no")]][$val[csf("fabric_color")]][$val[csf("body_part")]][$val[csf("lib_yarn_count_deter_id")]]["sample_type"] = $val[csf("sample_type")];
				$booking_ref_arr[$val[csf("booking_no")]][$val[csf("fabric_color")]][$val[csf("body_part")]][$val[csf("lib_yarn_count_deter_id")]]["quantity"] += $val[csf("finish_fabric")];
			}
			unset($booking_result);

			}
		}
	}

    $width = (2715+($num_of_store*110));  
    /*echo "<pre>";
    print_r($book_store_stock);
    die;*/
    
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
	<fieldset style="width:<? echo $width+50; ?>px;">
		<table cellpadding="0" cellspacing="0" width="<? echo $width;?>">
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="<? echo 22+$num_of_store?>" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="<? echo 22+$num_of_store?>" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="<? echo 22+$num_of_store?>" style="font-size:14px"><strong> <? if($date_from!="") echo "From : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
			</tr>
		</table>
		<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
			<thead>
				<tr>
					<th colspan="13">&nbsp;</th>
					<th colspan="14">Receive/Issue Info</th>
					<th colspan="<? echo $num_of_store;?>">Store Summary</th>
				</tr>
				<tr>
					<th width="40" rowspan="2">SL</th>
					<th width="110" rowspan="2">Buyer</th>
					<th width="110" rowspan="2">Dealing Marchant </th>
					<th width="110" rowspan="2">Sample Requisition</th>
					<th width="120" rowspan="2">Sample Booking No</th>
					<th width="100" rowspan="2">Year</th>
					<th width="100" rowspan="2">Internal Ref.No</th>
					<th width="100" rowspan="2">Sample Type</th>
					<th width="100" rowspan="2">Style</th>
					<th width="100" rowspan="2">Fin. Fab. Color</th>
					<th width="120" rowspan="2">Body Part</th>
					<th width="100" rowspan="2">Fabric Type</th>
					<th width="100" rowspan="2">Req. Qty Finish</th>

					<th colspan="3">Today Receive</th>
					<th colspan="3">Total Received</th>
					<th width="100" rowspan="2">Received Balance</th>
					<th colspan="3">Today Issue</th>
					<th colspan="3">Total Issued</th>
					<th width="105" rowspan="2">Stock Qty.</th>

					<? foreach ($stores as $row)
					{
						?>
						<th width="110" rowspan="2"><? echo $row[csf("store_name")];?></th>
						<?
					}
					?>
				</tr>
				<tr>
					<th width="100">Received Qty</th>
					<th width="100">Issue Return</th>
					<th width="100">Transfer From</th>
					<th width="100">Received Qty</th>
					<th width="100">Issue Return</th>
					<th width="100">Transfer From</th>

					<th width="100">Issue</th>
					<th width="100">Receive Return</th>
					<th width="100">Transfer To</th>
					<th width="100">Issue</th>
					<th width="100">Receive Return</th>
					<th width="100">Transfer To</th>
				</tr>
			</thead>
		</table>
		<div style="width:<? echo $width+20; ?>px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
				<?
				$i=1;
				foreach ($recvDtlsDataArr as $bookingNo => $bookingData) 
				{
					foreach ($bookingData as $ref_str => $val) 
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$ref_str_data=explode("*",$ref_str);
						$body_part_id = $ref_str_data[0];
						$deter_id = $ref_str_data[1];
						$fabric_color_id = $ref_str_data[2];

						$total_receive = $val["recv"]+$val["issue_return"]+$val["transfer_in"];
						$total_issue = $val["issue"]+$val["rcv_return"]+$val["trans_out"];
						$stock_qty =$total_receive-$total_issue;
						$stock_qty = number_format($stock_qty,2,".","");

						$style_ref_no = $booking_data[$bookingNo]["style_ref_no"];
						$booking_year = $booking_data[$bookingNo]["booking_date"];
						$buyer_id = $booking_data[$bookingNo]["buyer_id"];
						$dealing_marchant = $booking_data[$bookingNo]["dealing_marchant"];
						$requisition_number =$booking_data[$bookingNo]["requisition_number"];
						$grouping =$booking_data[$bookingNo]["grouping"];
						$sample_type_id =$booking_data[$bookingNo]["sample_type"];
						// $sample_type_id =$booking_ref_arr[$bookingNo][$fabric_color_id][$body_part_id][$deter_id]["sample_type"];
						$booking_quantity =$booking_ref_arr[$bookingNo][$fabric_color_id][$body_part_id][$deter_id]["quantity"];

						$receive_balance = $booking_quantity-$total_receive;
						if($cbo_value_with ==1 || ($cbo_value_with==2 && $stock_qty>0))
						{
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="40" ><? echo $i?></td>
								<td width="110" ><? echo $buyer_arr[$buyer_id];?></td>
								<td width="110" ><? echo $dealing_marchant_arr[$dealing_marchant];?></td>
								<td width="110" ><p class="word_break_wrap"><? echo $requisition_number;?></p></td>
								<td width="120" ><p class="word_break_wrap"><? echo $bookingNo;?></p></td>
								<td width="100" ><? echo $booking_year;?></td>
								<td width="100" ><? echo $grouping;?></td>
								<td width="100" ><? echo $sample_arr[$sample_type_id];?></td>
								<td width="100" ><p class="word_break_wrap"><? echo $style_ref_no;?></p></td>
								<td width="100" title="<? echo $fabric_color_id;?>"><p class="word_break_wrap"><? echo $color_arr[$fabric_color_id];?></p></td>
								<td width="120" title="<? echo $body_part_id;?>"><? echo $body_part[$body_part_id];?></td>
								<td width="100" ><? echo $constructionArr[$deter_id];?></td>
								<td width="100" align="right"><? echo number_format($booking_quantity,2);?></td>

								<td width="100" align="right"><a href="##" onclick="openmypage_qnty('<? echo $bookingNo;?>','<? echo $ref_str;?>','openmypage_quantity','<? echo $txt_date_from;?>','1','1');"><? echo number_format($val["today_recv"],2,".","");?></a></td>
								<td width="100" align="right"><a href="##" onclick="openmypage_qnty('<? echo $bookingNo;?>','<? echo $ref_str;?>','openmypage_quantity','<? echo $txt_date_from;?>','4','1');"><? echo number_format($val["today_issue_return"],2,".","");?></a></td>
								<td width="100" align="right"><a href="##" onclick="openmypage_qnty('<? echo $bookingNo;?>','<? echo $ref_str;?>','openmypage_transfer','<? echo $txt_date_from;?>','5','1');"><? echo number_format($val["today_transfer_in"],2,".","");?></a></td>

								<td width="100" align="right"><a href="##" onclick="openmypage_qnty('<? echo $bookingNo;?>','<? echo $ref_str;?>','openmypage_quantity','<? echo $txt_date_from;?>','1','0');"><? echo number_format($val["recv"],2,".","");?></a></td>
								<td width="100" align="right"><a href="##" onclick="openmypage_qnty('<? echo $bookingNo;?>','<? echo $ref_str;?>','openmypage_quantity','<? echo $txt_date_from;?>','4','0');"><? echo number_format($val["issue_return"],2,".","");?></a></td>
								<td width="100" align="right"><a href="##" onclick="openmypage_qnty('<? echo $bookingNo;?>','<? echo $ref_str;?>','openmypage_transfer','<? echo $txt_date_from;?>','5','0');"><? echo number_format($val["transfer_in"],2,".","");?></a></td>
								<td width="100" align="right"><? echo number_format($receive_balance,2);?></td>

								<td width="100" align="right"><a href="##" onclick="openmypage_qnty('<? echo $bookingNo;?>','<? echo $ref_str;?>','openmypage_quantity','<? echo $txt_date_from;?>','2','1');"><? echo number_format($val["today_issue"],2,".","");?></a></td>
								<td width="100" align="right"><a href="##" onclick="openmypage_qnty('<? echo $bookingNo;?>','<? echo $ref_str;?>','openmypage_quantity','<? echo $txt_date_from;?>','3','1');"><? echo number_format($val["today_rcv_return"],2,".","");?></a></td>
								<td width="100" align="right"><a href="##" onclick="openmypage_qnty('<? echo $bookingNo;?>','<? echo $ref_str;?>','openmypage_transfer','<? echo $txt_date_from;?>','6','1');"><? echo number_format($val["today_trans_out"],2,".","");?></a></td>

								<td width="100" align="right"><a href="##" onclick="openmypage_qnty('<? echo $bookingNo;?>','<? echo $ref_str;?>','openmypage_quantity','<? echo $txt_date_from;?>','2','0');"><? echo number_format($val["issue"],2,".","");?></a></td>
								<td width="100" align="right"><a href="##" onclick="openmypage_qnty('<? echo $bookingNo;?>','<? echo $ref_str;?>','openmypage_quantity','<? echo $txt_date_from;?>','3','0');"><? echo number_format($val["rcv_return"],2,".","");?></a></td>
								<td width="100" align="right"><a href="##" onclick="openmypage_qnty('<? echo $bookingNo;?>','<? echo $ref_str;?>','openmypage_transfer','<? echo $txt_date_from;?>','6','0');"><? echo number_format($val["trans_out"],2,".","");?></a></td>

								<td width="105" align="right"><a href="##" onclick="openmypage_qnty('<? echo $bookingNo;?>','<? echo $ref_str;?>','openmypage_stock','<? echo $txt_date_from;?>','0');"><? echo number_format($stock_qty,2,".","");?></a></td>
								<? foreach ($stores as $row) 
								{
									?>
									<td width="110" align="right"><? echo number_format($storeWiseStock[$bookingNo][$ref_str][$row[csf("id")]],2);?></td>
									<?
									$grand_total_store[$row[csf("id")]] += $storeWiseStock[$bookingNo][$ref_str][$row[csf("id")]];
								}
								?>
							</tr>
							<?
							$i++;
							$grand_booking_quantity		+=$booking_quantity;
							$grand_today_recv 			+=$val["today_recv"];
							$grand_today_issue_ret 		+=$val["today_issue_return"];
							$grand_today_transfer_in 	+= $val["today_transfer_in"];
							$grand_recv 				+=$val["recv"];
							$grand_issue_ret 			+=$val["issue_return"];
							$grand_transfer_in 			+= $val["transfer_in"];
							$grand_receive_balance 		+=$receive_balance;

							$grand_today_issue 			+=$val["today_issue"];
							$grand_today_recv_return 	+=$val["today_rcv_return"];
							$grand_today_trans_out 		+=$val["today_trans_out"];
							$grand_issue 				+=$val["issue"];
							$grand_recv_return 			+=$val["rcv_return"];
							$grand_trans_out 			+=$val["trans_out"];
							$grand_stock_qty 			+=$stock_qty;
						}
					}
				}				
				?>
			</table>
		</div>
		<table  width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
			<tfoot>
				<tr>
				<th width="40" >&nbsp;</th>
				<th width="110">&nbsp;</th>
				<th width="110">&nbsp;</th>
				<th width="110">&nbsp; </th>
				<th width="120">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="120">&nbsp;</th>
				<th width="100">Total:</th>
				<th width="100" id="value_total_booking_quantity"><? echo number_format($grand_booking_quantity,2); ?></th>

				<th width="100" align="right" id="value_total_today_recv"><? echo number_format($grand_today_recv,2);?></th>
				<th width="100" align="right" id="value_total_today_issue_ret"><? echo number_format($grand_today_issue_ret,2);?></th>
				<th width="100" align="right" id="value_total_today_transfer_in"><? echo number_format($grand_today_transfer_in,2);?></th>
				<th width="100" align="right" id="value_total_recv"><? echo number_format($grand_recv,2);?></th>
				<th width="100" align="right" id="value_total_issue_ret"><? echo number_format($grand_issue_ret,2);?></th>
				<th width="100" align="right" id="value_total_transfer_in"><? echo number_format($grand_transfer_in,2);?></th>
				<th width="100" align="right" id="value_total_receive_balance"><? echo number_format($grand_receive_balance,2);?></th>

				<th width="100" align="right" id="value_total_today_issue"><? echo number_format($grand_today_issue,2);?></th>
				<th width="100" align="right" id="value_total_today_recv_return"><? echo number_format($grand_today_recv_return,2);?></th>
				<th width="100" align="right" id="value_total_today_trans_out"><? echo number_format($grand_today_trans_out,2);?></th>
				<th width="100" align="right" id="value_total_issue"><? echo number_format($grand_issue,2);?></th>
				<th width="100" align="right" id="value_total_recv_return"><? echo number_format($grand_recv_return,2);?></th>
				<th width="100" align="right" id="value_total_trans_out"><? echo number_format($grand_trans_out,2);?></th>
				<th width="105" align="right" id="value_total_stock_qty"><? echo number_format($grand_stock_qty,2);?></th>

				

				<? foreach ($stores as $row)
				{
					?>
					<th width="110" ><? echo number_format($grand_total_store[$row[csf("id")]],2);?></th>
					<?
				}
				?>
			</tr>
			
			</tfoot>
		</table>

	</fieldset>
	<?
	//echo "Execution Time: " . (microtime(true) - $started) . "S";
	$html = ob_get_contents();
    ob_clean();
	foreach (glob("$user_id*.xls") as $filename)
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
	echo "$html####$filename####$report_type";

	exit();
}

if($action=="openmypage_quantity")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="770" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="13">Receive Details</th>
					</tr>											
					<tr>
						<th width="30">Sl</th>
						<th width="70">Product ID</th>
						<th width="100">Transaction ID</th>
						<th width="75">Transaction Date</th>
						<th width="100">Batch No</th>
						<th width="100">Service Company</th>
						<th width="100">Service Location</th>
						<th width="100">Batch Color</th>
						<th width="100">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="60">F.Dia</th>
						<th width="80">Fin.Qty.</th>
						<th width="80">Store Name</th>
					</tr>
				</thead>
				<tbody>
					<?
					$prod_ref = explode("*", $prod_ref);
					$body_part_id = $prod_ref[0];
					$fabric_description_id = $prod_ref[1];
					$color_id = $prod_ref[2];

					$company_arr 	= return_library_array("select id, company_name from lib_company where status_active=1","id","company_name");
					$supplier_arr 		= return_library_array( "select id, buyer_name from lib_supplier", "id", "supplier_name"  );
					$location_arr 		= return_library_array( "select id, location_name from lib_location", "id", "location_name"  );
					$color_arr 		= return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
					$store_arr 		= return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
					
					if($transType==1)
					{
						$sql = "SELECT b.prod_id, a.recv_number as transaction_id, b.transaction_date, e.batch_no, a.knitting_source service_source, a.knitting_company as service_company, a.knitting_location_id as service_location, c.color_id, d.product_name_details, c.gsm, c.width, b.store_id, sum(b.cons_quantity) as quantity from inv_receive_master a, inv_transaction b, pro_finish_fabric_rcv_dtls c, product_details_master d, pro_batch_create_mst e  WHERE a.company_id =$companyID and a.id = b.mst_id and b.id = c.trans_id and b.prod_id = d.id and b.transaction_type =1 and a.entry_form = 37 and a.status_active =1 and b.status_active =1 and c.status_active =1  and b.pi_wo_batch_no = e.id and e.booking_no = '$booking_no' and c.body_part_id='$body_part_id' and c.fabric_description_id=$fabric_description_id and c.color_id=$color_id  group by b.prod_id, a.recv_number, b.transaction_date, e.batch_no, a.knitting_source, a.knitting_company, a.knitting_location_id,d.product_name_details,  c.color_id, c.gsm, c.width, b.store_id";
					}
					elseif ($transType==2) 
					{
						$sql = "select c.prod_id, a.issue_number as transaction_id, c.transaction_date, e.batch_no, a.knit_dye_source, a.knit_dye_company, a.knit_dye_location as service_location,d.color as color_id, d.product_name_details, d.gsm, d.dia_width as width, c.store_id,  sum(c.cons_quantity) as quantity from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c , product_details_master d, pro_batch_create_mst e where a.id = b.mst_id and b.trans_id = c.id and c.prod_id = d.id and c.pi_wo_batch_no= e.id and c.company_id =$companyID and b.body_part_id =$body_part_id and e.booking_no= '$booking_no' and d.color=$color_id and d.detarmination_id=$fabric_description_id and a.entry_form = 18 and c.status_active =1 and b.status_active=1 and a.status_active =1 and c.item_category =2 and c.transaction_type =2 group by c.prod_id, a.issue_number, c.transaction_date,e.batch_no, a.knit_dye_source, a.knit_dye_company, a.knit_dye_location,d.color, d.product_name_details, d.gsm, d.dia_width, c.store_id";
					}
					elseif($transType==3)
					{
						$sql ="select c.prod_id, a.issue_number as transaction_id, c.transaction_date, e.batch_no, f.knitting_source as service_source, f.knitting_company as service_company, f.knitting_location_id, d.product_name_details, d.gsm, d.dia_width as width, c.store_id, sum(c.cons_quantity) as quantity from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e , inv_receive_master f where a.id=b.mst_id and b.trans_id=c.id and a.entry_form =46 and a.received_id= f.id and c.company_id=$companyID and e.booking_no='$booking_no' and c.body_part_id=$body_part_id  and d.detarmination_id= $fabric_description_id and d.color=$color_id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and a.status_active=1 and b.status_active=1 and c.status_active=1 group by c.prod_id, a.issue_number, c.transaction_date, e.batch_no, f.knitting_source, f.knitting_company, f.knitting_location_id, d.product_name_details, d.gsm, d.dia_width, c.store_id";
					}
					elseif ($transType==4)
					{
						$sql = "select  b.prod_id, a.recv_number as transaction_id, c.transaction_date, e.batch_no, d.knit_dye_source as service_source, d.knit_dye_company as service_company, d.knit_dye_location as service_location, b.color_id, f.product_name_details,  b.gsm, b.width,c.store_id, sum(c.cons_quantity) as quantity from inv_receive_master a, pro_finish_fabric_rcv_dtls b,inv_transaction c, inv_issue_master d, pro_batch_create_mst e , product_details_master f where a.id=b.mst_id and b.trans_id =c.id and c.issue_id =d.id and c.prod_id = f.id and a.entry_form =52 and a.item_category =2 and c.pi_wo_batch_no =e.id and a.status_active =1 and b.status_active=1 and c.status_active =1 and c.company_id=$companyID and e.booking_no= '$booking_no' and b.body_part_id=$body_part_id and b.fabric_description_id=$fabric_description_id and b.color_id=$color_id group by b.prod_id, a.recv_number, c.transaction_date, e.batch_no, d.knit_dye_source, d.knit_dye_company, d.knit_dye_location, b.color_id, f.product_name_details,  b.gsm, b.width,c.store_id";
					}
					//echo $sql;//die;
					$result = sql_select($sql);
					$i=1;
					foreach($result as $row)
					{
						if($row[csf('service_source')] ==1){
							$service_company= $company_arr[$row[csf('service_company')]];
						}else{
							$service_company= $supplier_arr[$row[csf('service_company')]];
						}

						$date_frm=strtotime($from_date);
						$transaction_date=strtotime($row[csf('transaction_date')]);

						//echo $is_today."=$transaction_date == $date_frm";//die;

						if( ($transaction_date <= $date_frm && $is_today==0) || ($transaction_date == $date_frm && $is_today==1) )
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$tot_reject=$row[csf('returnable_qnty')];
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><p><? echo $i; ?></p></td>
								<td width="70"><p><? echo $row[csf('prod_id')]; ?></p></td>
								<td width="100"><p><? echo $row[csf('transaction_id')]; ?></p></td>
								<td width="75"><p><? echo change_date_format($row[csf('transaction_date')]); ?></p></td>
								<td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
								<td width="100"><p><? echo $service_company; ?></p></td>
								<td width="100"><p><? echo $location_arr[$row[csf('service_location')]]; ?></p></td>
								<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
								<td width="100"><p><? echo $row[csf('product_name_details')]; ?></p></td>
								<td width="50"><p><? echo $row[csf('gsm')]; ?></p></td>
								<td width="60"><p><? echo $row[csf('width')]; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
								<td width="80"><p><? echo $store_arr[$row[csf('store_id')]]; ?></p></td>
							</tr>
							<?
							$tot_qty+=$row[csf('quantity')];
							$i++;
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="11" align="right">Total</td>
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

if($action=="openmypage_transfer")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:770px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="770" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="11">Transfer Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="100">Transaction ID</th>
						<th width="80">Transaction Date</th>
						<th width="100">Int. Ref.</th>
						<th width="100">Batch No</th>
						<th width="100">Color</th>
						<th width="100">Fabric Des.</th>
						<th width="60">GSM</th>
						<th width="60">F.Dia</th>
						<th width="80">Quantity</th>
						<th width="100">Store Name</th>
					</tr>
				</thead>
				<tbody>
					<?
					$prod_ref = explode("*", $prod_ref);
					$body_part_id = $prod_ref[0];
					$fabric_description_id = $prod_ref[1];
					$color_id = $prod_ref[2];

					$color_arr	=return_library_array( "select id,color_name from lib_color where id = '$color_id'", "id", "color_name");
					$store_arr 	= return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
					$internal_ref_arr = return_library_array("select b.booking_no, c.internal_ref from wo_non_ord_samp_booking_dtls b, sample_development_mst c where B.STYLE_ID= c.id and b.booking_no ='$booking_no' group by b.booking_no, c.internal_ref","booking_no","internal_ref");
				
					if($transType==5)
					{
						$sql = "select a.transfer_system_id,c.transaction_date, f.grouping, e.batch_no, d.color as color_id, d.product_name_details, d.gsm, d.dia_width, c.store_id, sum(c.cons_quantity) as quantity from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e , wo_non_ord_samp_booking_mst f where a.id=b.mst_id and b.to_trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and e.booking_no_id = f.id and c.company_id=$companyID and e.booking_no= '$booking_no' and c.item_category=2 and c.transaction_type=5 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.entry_form in (14,15,306) and e.booking_without_order=1 and c.body_part_id=$body_part_id and d.detarmination_id=$fabric_description_id and d.color=$color_id group by a.transfer_system_id,c.transaction_date, f.grouping, e.batch_no, d.color, d.product_name_details, d.gsm, d.dia_width, c.store_id";
					}
					else
					{
						$sql = "select a.transfer_system_id, c.transaction_date,f.grouping, e.batch_no, d.color as color_id, d.product_name_details, d.gsm, d.dia_width, c.store_id, sum(c.cons_quantity) as quantity from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e, wo_non_ord_samp_booking_mst f  where a.id=b.mst_id and b.trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and e.booking_no_id = f.id and c.company_id =$companyID and e.booking_no= '$booking_no' and c.item_category=2 and c.transaction_type=6 and c.body_part_id=$body_part_id and d.detarmination_id=$fabric_description_id and d.color=$color_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.entry_form in (14,15,306) and b.active_dtls_id_in_transfer=1 group by a.transfer_system_id,c.transaction_date, f.grouping, e.batch_no, d.color, d.product_name_details, d.gsm, d.dia_width, c.store_id";
					}
					$i=1;
					$result = sql_select($sql);
					foreach($result as $row)
					{
						$date_frm=strtotime($from_date);
						$transaction_date=strtotime($row[csf('transaction_date')]);
						//echo $is_today."=$transaction_date == $date_frm";//die;
						if( ($transaction_date <= $date_frm && $is_today==0) || ($transaction_date == $date_frm && $is_today==1) )
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$tot_reject=$row[csf('returnable_qnty')];
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><p><? echo $i; ?></p></td>
								<td width="100"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
								<td width="80"><p><? echo change_date_format($row[csf('transaction_date')]); ?></p></td>
								<td width="100"><p><? echo $internal_ref_arr[$booking_no]; ?></p></td>
								<td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>

								<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
								<td width="100"><p><? echo $row[csf('product_name_details')]; ?></p></td>
								<td width="60"><p><? echo $row[csf('gsm')]; ?></p></td>
								<td width="60"><p><? echo $row[csf('dia_width')]; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
								<td width="100"><p><? echo $store_arr[$row[csf('store_id')]]; ?></p></td>
							</tr>
							<?
							$tot_qty+=$row[csf('quantity')];
							$i++;
						}
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

if($action=="openmypage_stock")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:650px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="11">Stock Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="100">Product ID</th>
						<th width="100">Batch No</th>
						<th width="200">Fabric Des.</th>
						<th width="100">Quantity</th>
						<th width="120">Store Name</th>
					</tr>
				</thead>
				<tbody>
					<?
					$prod_ref = explode("*", $prod_ref);
					$body_part_id = $prod_ref[0];
					$fabric_description_id = $prod_ref[1];
					$color_id = $prod_ref[2];

					$store_arr 	= return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
					$i=1;
				
					$sql="SELECT a.prod_id, a.store_id, c.batch_no, b.product_name_details, sum(case when a.transaction_type in (1,4,5) then a.cons_quantity else 0 end) as rcv,  sum(case when a.transaction_type in (2,3,6) then a.cons_quantity else 0 end) as iss, sum(case when a.transaction_type in (1,4,5) then a.cons_quantity else 0 end) -  sum(case when a.transaction_type in (2,3,6) then a.cons_quantity else 0 end) as balance, a.transaction_date
					from inv_transaction a, product_details_master b, pro_batch_create_mst c 
					where a.item_category =2 and a.prod_id = b.id and a.pi_wo_batch_no = c.id and a.status_active =1 and a.company_id =$companyID and c.booking_no='$booking_no' and a.body_part_id =$body_part_id and b.detarmination_id=$fabric_description_id and b.color=$color_id and a.transaction_date<='$from_date'
					group by a.prod_id, a.store_id, c.batch_no, b.product_name_details, a.transaction_date";
					// echo $sql;
					$result = sql_select($sql);

					foreach($result as $row)
					{
						$date_frm=date('Y-m-d',strtotime($from_date));
						$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));
						if( $from_date == "" || ( $from_date != "" && ($transaction_date <= $date_frm)))
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$tot_reject=$row[csf('returnable_qnty')];
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><p><? echo $i; ?></p></td>
								<td width="100"><p><? echo $row[csf('prod_id')]; ?></p></td>
								<td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
								<td width="200"><p><? echo $row[csf('product_name_details')]; ?></p></td>
								<td width="100" align="right"><p><? echo number_format($row[csf('balance')],2); ?></p></td>
								<td width="120"><p><? echo $store_arr[$row[csf('store_id')]]; ?></p></td>
							</tr>
							<?
							$tot_qty+=$row[csf('balance')];
							$i++;
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="4" align="right">Total</td>
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