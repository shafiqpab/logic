<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------
$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');

if ($action=="load_variable_settings")
{
	$variable_setting_production=return_field_value("fabric_roll_level","variable_settings_production","company_name='$data' and item_category_id=2 and variable_list=3 and status_active=1","fabric_roll_level");
	if($variable_setting_production==1)
	{
		echo "$('#txt_roll').attr('readonly');\n";
	}
	else
	{
		echo "$('#txt_roll').removeAttr('readonly');\n";
	}
	$variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name='$data' and item_category_id=2 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	echo "$('#store_update_upto').val($variable_inventory);\n";

	$varialbe_production_sql = sql_select("select variable_list, distribute_qnty, process_costing_maintain from variable_settings_production where company_name ='$data' and variable_list in(34) and is_deleted=0 and status_active=1");
	foreach ($varialbe_production_sql as $row) 
	{
		if ($row[csf('variable_list')] == 34) 
		{
			$process_cost_maintain = $row[csf('process_costing_maintain')];
			echo "document.getElementById('process_costing_maintain').value='" . $process_cost_maintain . "';\n";
		}
	}

	$varialbe_production_allow_fin_fab_NR=return_field_value("allow_fin_fab_rcv","variable_settings_production","company_name='$data' and variable_list=75 and is_deleted=0 and status_active=1");

	if($process_cost_maintain==1)
	{
		echo "document.getElementById('var_allow_fin_rcv_NR_hdn').value='" . $varialbe_production_allow_fin_fab_NR . "';\n";
		
	}

	
	exit();
}
if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/finish_fab_garments_receive_rtn_controller",$data);
}
if($action=="mrr_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(mrr)
		{
			$("#hidden_recv_number").val(mrr); // mrr number
			parent.emailwindow.hide();
		}

		function change_caption(type)
		{
			if(type==1)
			{
				$('#td_search').html('MRR No');
			}
			else if(type==2)
			{
				$('#td_search').html('Challan No');
			}
			else if(type==3)
			{
				$('#td_search').html('Batch No');
			}
			else if(type==4)
			{
				$('#td_search').html('Booking No');
			}
			else if(type==5)
			{
				$('#td_search').html('Internal Ref');
			}
		}
		function form_submit()
		{
			var txt_search_common=document.getElementById('txt_search_common').value.trim();
			var txt_date_from=document.getElementById('txt_date_from').value;
			var txt_date_to=document.getElementById('txt_date_to').value;

			if(txt_search_common == "" && (txt_date_from =="" && txt_date_to ==""))
			{
				if(form_validation('txt_search_common*txt_date_from*txt_date_to','txt_search_common*From Date*To Date')==false )
				{
					return;
				}
			}

			show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $var_allow_fin_rcv_NR_hdn; ?>+'_'+<? echo $process_costing_maintain; ?>, 'create_mrr_search_list_view', 'search_div', 'finish_fab_garments_receive_rtn_controller', 'setFilterGrid(\'list_view\',-1)')
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
						<th align="center" id="td_search">Enter MRR Number</th>
						<th>Date Range</th>
						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
					</tr>
				</thead>
				<tbody>
					<tr class="general">
						<td>
							<?
							$search_by = array(1=>'MRR No',2=>'Challan No',3=>'Batch No',4=>'Booking No',5=>'Internal Ref');
							//$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							//echo create_drop_down( "cbo_search_by", 120, $search_by,"",0, "--Select--", "",$dd,0 );

							echo create_drop_down( "cbo_search_by", 120, $search_by,"", 0, "--Select--", 1, "change_caption(this.value);",0 );
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
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="form_submit()" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td align="center" height="40" valign="middle" colspan="5">
							<? echo load_month_buttons(1);  ?>
							<!-- Hidden field here -->
							<input type="hidden" id="hidden_recv_number" value="" />
							<!-- -END  -->
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
	$var_allow_fin_rcv_NR_hdn = $ex_data[5];
	$process_costing_maintain = $ex_data[6];

	$variable_setting_inventory=return_field_value("auto_update","variable_settings_production","company_name='$company' and variable_list=15 and status_active=1","auto_update");

	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==1) // for mrr
		{
			$sql_cond .= " and a.recv_number LIKE '%$txt_search_common'";
		}
		else if(trim($txt_search_by)==2) // for chllan no
		{
			$sql_cond .= " and a.challan_no LIKE '%$txt_search_common%'";
		}
		else if(trim($txt_search_by)==3) // for Batch
		{
			$sql_cond .= " and d.batch_no LIKE '%$txt_search_common'";
		}
		else if(trim($txt_search_by)==4) // for Booking no
		{
			$sql_cond .= " and d.booking_no LIKE '%$txt_search_common%'";
		}
		else if(trim($txt_search_by)==5) // for Internal Ref.
		{
			$reference_sql = sql_select("select b.booking_no, a.po_number, a.grouping, c.id as batch_id, c.batch_no
			from wo_po_break_down a,wo_booking_dtls b, pro_batch_create_mst c
			where a.id = b.po_break_down_id and b.booking_no = c.booking_no and a.grouping like '%$txt_search_common%'
			group by b.booking_no, a.po_number, a.grouping, c.id, c.batch_no
			union all
			select b.booking_no, null as po_number, b.grouping, c.id as batch_id, c.batch_no
			from wo_non_ord_samp_booking_mst b, pro_batch_create_mst c
			where  b.booking_no = c.booking_no and b.grouping like '%$txt_search_common%'
			group by  b.booking_no, b.grouping, c.id, c.batch_no");

			foreach ($reference_sql as $val) 
			{
				$batch_arr[$val[csf('batch_id')]] = $val[csf('batch_id')];
				$batch_ref_arr[$val[csf('batch_id')]] .= $val[csf('grouping')].",";
			}

			if(!empty($batch_arr))
			{
				$batch_ids=implode(",",$batch_arr);
				$all_search_batch_id_cond=""; $batchCond="";
				if($db_type==2 && count($batch_arr)>999)
				{
					$batch_arr_chunk=array_chunk($batch_arr,999) ;
					foreach($batch_arr_chunk as $chunk_arr)
					{
						$chunk_arr_value=implode(",",$chunk_arr);
						$batchCond.=" d.id in($chunk_arr_value) or ";
					}

					$all_search_batch_id_cond.=" and (".chop($batchCond,'or ').")";
				}
				else
				{
					$all_search_batch_id_cond=" and d.id in($batch_ids)";
				}
			}
			unset($batch_arr);
			unset($reference_sql);
		}
	}

	if( $fromDate!="" && $toDate!="" )
	{
		if($db_type==0)
		{
			$sql_cond .= " and a.receive_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
		}
		else
		{
			$sql_cond .= " and a.receive_date  between '".change_date_format($fromDate,'','',1)."' and '".change_date_format($toDate,'','',1)."'";
		}
	}
	if(trim($company)!="") $sql_cond .= " and a.company_id='$company'";

	if($db_type==0) $year_field="YEAR(a.insert_date) as year";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";

	if($variable_setting_inventory==1) $entry_form_ref=" and a.entry_form in(7,37)"; else $entry_form_ref=" and a.entry_form in(37)";

	//$sql = "SELECT a.id,a.recv_number_prefix_num,a.recv_number,to_char(a.insert_date,'YYYY') as year, a.challan_no,a.receive_date,a.receive_basis,  a.knitting_source,sum(c.quantity) as receive_qnty,c.is_sales,d.batch_no, d.booking_no from inv_receive_master a,inv_transaction b, order_wise_pro_details c,pro_batch_create_mst d where a.id=b.mst_id and b.id=c.trans_id and b.pi_wo_batch_no =d.id $entry_form_ref and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $sql_cond and a.company_id='$company' and c.entry_form=37 group by a.id, a.recv_number_prefix_num, a.recv_number,a.challan_no,a.receive_date,a.receive_basis,a.insert_date,a.knitting_source,c.is_sales,d.batch_no, d.booking_no order by a.id";

	//N.B :- Delivery from textile and production basis receive will not return.
	//N.B :- Only Service booking and PI basis receive will return.


	/*$sql = "SELECT a.id,a.recv_number_prefix_num,a.recv_number,$year_field, a.challan_no,a.receive_date,a.receive_basis, a.knitting_source, sum(b.cons_quantity) as receive_qnty, d.id as batch_id, d.batch_no, d.booking_no from inv_receive_master a,inv_transaction b, pro_batch_create_mst d where a.id=b.mst_id  and b.pi_wo_batch_no =d.id $entry_form_ref and a.status_active=1 and b.status_active=1 and d.status_active=1 and b.transaction_type =1 $sql_cond and a.company_id='$company' and a.receive_basis in (1,11) $all_search_batch_id_cond group by a.id, a.recv_number_prefix_num, a.recv_number,a.challan_no,a.receive_date, a.receive_basis,a.insert_date, a.knitting_source, d.id, d.batch_no, d.booking_no order by a.id";*/


	if($process_costing_maintain==1 && $var_allow_fin_rcv_NR_hdn==1)
	{
		$fabric_source_cond="and c.fabric_source in (1)";
		$payment_cond="and c.pay_mode in(3,5)";
	}
	else
	{
		$fabric_source_cond="and c.fabric_source in (2)";
		$payment_cond="and c.pay_mode in(1)";
	}



	$sql = "SELECT a.id,a.recv_number_prefix_num,a.recv_number,$year_field, a.challan_no,a.receive_date,a.receive_basis, a.knitting_source, sum(b.cons_quantity) as receive_qnty, d.id as batch_id, d.batch_no, d.booking_no 
	from inv_receive_master a,inv_transaction b, pro_batch_create_mst d where a.id=b.mst_id  and b.pi_wo_batch_no =d.id $entry_form_ref and a.status_active=1 and b.status_active=1 and d.status_active=1 and b.transaction_type =1 $sql_cond and a.company_id='$company' and a.receive_basis in (1,10,11) $all_search_batch_id_cond 
	group by a.id, a.recv_number_prefix_num, a.recv_number,a.challan_no,a.receive_date, a.receive_basis,a.insert_date, a.knitting_source, d.id, d.batch_no, d.booking_no 
	union all 
	SELECT a.id,a.recv_number_prefix_num,a.recv_number,$year_field, a.challan_no,a.receive_date,a.receive_basis, a.knitting_source, sum(b.cons_quantity) as receive_qnty, d.id as batch_id, d.batch_no, d.booking_no 
	from inv_receive_master a,inv_transaction b, pro_batch_create_mst d, wo_booking_mst c 
	where a.id=b.mst_id  and b.pi_wo_batch_no=d.id $entry_form_ref and a.status_active=1 and b.status_active=1 and d.status_active=1 and b.transaction_type =1 $sql_cond and a.company_id='$company' and a.receive_basis=2 and a.booking_no=c.booking_no $fabric_source_cond $payment_cond and a.booking_without_order = 0 $all_search_batch_id_cond 
	group by a.id, a.recv_number_prefix_num, a.recv_number,a.challan_no,a.receive_date, a.receive_basis,a.insert_date, a.knitting_source, d.id, d.batch_no, d.booking_no
	union all
	SELECT a.id,a.recv_number_prefix_num,a.recv_number,$year_field, a.challan_no,a.receive_date,a.receive_basis, a.knitting_source, sum(b.cons_quantity) as receive_qnty, d.id as batch_id, d.batch_no, d.booking_no 
	from inv_receive_master a,inv_transaction b, pro_batch_create_mst d, wo_non_ord_samp_booking_mst c
	where a.id=b.mst_id and b.pi_wo_batch_no =d.id $entry_form_ref and a.status_active=1 and b.status_active=1 and d.status_active=1 and b.transaction_type =1 $sql_cond and a.company_id='$company' and a.receive_basis=2 and a.booking_no=c.booking_no and c.fabric_source=2 and a.booking_without_order=1 $all_search_batch_id_cond 
	group by a.id, a.recv_number_prefix_num, a.recv_number,a.challan_no,a.receive_date, a.receive_basis,a.insert_date, a.knitting_source, d.id, d.batch_no, d.booking_no
	order by id";

	$sql_result=sql_select($sql);

	if(empty($batch_ref_arr)) 
	{
		foreach($sql_result as $row)
		{
			$batch_arr[$row[csf("batch_id")]] = $row[csf("batch_id")];
		}

		if(!empty($batch_arr))
		{
			$batch_ids=implode(",",$batch_arr);
			$all_ref_batch_id_cond=""; $batchCond="";
			if($db_type==2 && count($batch_arr)>999)
			{
				$batch_arr_chunk=array_chunk($batch_arr,999) ;
				foreach($batch_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$batchCond.=" c.id in($chunk_arr_value) or ";
				}

				$all_ref_batch_id_cond.=" and (".chop($batchCond,'or ').")";
			}
			else
			{
				$all_ref_batch_id_cond=" and c.id in($batch_ids)";
			}
		}

		$reference_sql = sql_select("select b.booking_no, a.po_number, a.grouping, c.id as batch_id, c.batch_no
			from wo_po_break_down a,wo_booking_dtls b, pro_batch_create_mst c
			where a.id = b.po_break_down_id and b.booking_no = c.booking_no $all_ref_batch_id_cond
			group by b.booking_no, a.po_number, a.grouping, c.id, c.batch_no
			union all
			select b.booking_no, null as po_number, b.grouping, c.id as batch_id, c.batch_no
			from wo_non_ord_samp_booking_mst b, pro_batch_create_mst c
			where  b.booking_no = c.booking_no $all_ref_batch_id_cond
			group by  b.booking_no, b.grouping, c.id, c.batch_no");
			foreach ($reference_sql as $val) 
			{
				$batch_ref_arr[$val[csf('batch_id')]] .= $val[csf('grouping')].",";
			}
	}
	//echo $sql;
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="1060">
		<thead>
			<tr>
				<th width="50">SL</th>
				<th width="70">MRR No</th>
				<th width="50">Year</th>
				<th width="100">Batch No</th>
				<th width="100">Booking No</th>
				<th width="120">Internal Ref.</th>
				<th width="120">Challan No</th>
				<th width="130">Dyeing Source</th>
				<th width="100">Receive Date</th>
				<th width="100">Receive Basis</th>
				<th>Receive Qnty</th>
			</tr>
		</thead>
	</table>

	<div style="width:1060px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="1040" id="list_view">
			<tbody>
				<?
				$i=1;
				
				foreach($sql_result as $row)
				{
					$is_sales =0;
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer;" onClick="js_set_value('<? echo $row[csf("id")]; ?>_<? echo $row[csf("recv_number")]; ?>_<? echo $is_sales; ?>')">
						<td width="50" align="center"><p><? echo $i; ?></p></td>
						<td width="70" align="center"><p><? echo $row[csf("recv_number_prefix_num")]; ?></p></td>
						<td width="50" align="center"><p><? echo $row[csf("year")]; ?></p></td>
						<td width="100" ><p><? echo $row[csf("batch_no")]; ?></p></td>
						<td width="100" ><p><? echo $row[csf("booking_no")]; ?></p></td>
						<td width="120" style="word-break: break-all;word-wrap: break-word;">
							<p>
							<? 
								echo implode(",",array_filter(array_unique(explode(",",chop($batch_ref_arr[$row[csf("batch_id")]],",")))));// $row[csf("booking_no")]; ?>
							</p>
						</td>
						<td width="120" ><p><? echo $row[csf("challan_no")]; ?></p></td>
						<td width="130" ><p><? echo $knitting_source[$row[csf("knitting_source")]]; ?></p></td>
						<td width="100" align="center"><p><? if($row[csf("receive_date")]!="" && $row[csf("receive_date")]!="0000-00-00") echo change_date_format($row[csf("receive_date")]); ?></p></td>
						<td width="100"><p><? echo $receive_basis_arr[$row[csf("receive_basis")]]; ?></p></td>
						<td align="right"><p><? echo number_format($row[csf("receive_qnty")],2,".",""); ?></p></td>
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

if($action=="populate_data_from_data")
{

	$sql = "select id,recv_number,entry_form,company_id,receive_basis,receive_purpose,lc_no,knitting_source,knitting_company,booking_no,booking_id
	from inv_receive_master
	where id='$data' and entry_form in(7,37) and status_active=1 and is_deleted=0";
	//echo $sql;
	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "$('#txt_received_id').val('".$row[csf("id")]."');\n";
		echo "$('#txt_mrr_no').val('".$row[csf("recv_number")]."');\n";
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";

		$kniting_company=$row[csf("knitting_company")];
		$kniting_source=$row[csf("knitting_source")];
		$company_id=$row[csf("company_id")];
		echo "load_drop_down( 'requires/finish_fab_garments_receive_rtn_controller', $kniting_company+'_'+$kniting_source+'_'+$company_id, 'load_drop_down_knitting_com','knitting_com');\n";
		if($row[csf("receive_basis")]==1)
		{
			echo "$('#txt_pi_no').removeAttr('disabled','disabled');\n";
			echo "$('#txt_pi_no').val('".$row[csf("booking_no")]."');\n";
			echo "$('#pi_id').val(".$row[csf("booking_id")].");\n";
		}
		else
		{
			echo "$('#txt_pi_no').attr('disabled','disabled');\n";
		}

		//right side list view
		echo"show_list_view('".$row[csf("id")]."','show_product_listview','list_product_container','requires/finish_fab_garments_receive_rtn_controller','');\n";
	}
	exit();
}

if($action=="load_drop_down_knitting_com")
{
	$data = explode("_",$data);
	$kniting_company=$data[0];
	$company_id=$data[2];

	if($data[1]==1)
	{
		echo create_drop_down( "cbo_return_to", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "--Select Knit Company--", "$company_id", "",1 );
	}
	else if($data[1]==3)
	{
		echo create_drop_down( "cbo_return_to", 170, "select a.id,a.supplier_name from lib_supplier a","id,supplier_name", 1, "--Select--", "$kniting_company", "" ,1);
	}
	else
	{
		echo create_drop_down( "cbo_return_to", 170, $blank_array,"",1, "--Select Knit Company--", 0, "" ,1);
	}
	exit();
}

//right side product list create here--------------------//
if($action=="show_product_listview")
{
	$mrr_no = $data;

	/*$sql = "SELECT a.company_id, a.location_id location, a.id as mrr_id, a.store_id, c.id as prod_id, c.product_name_details, b.batch_id, b.color_id, d.body_part_id, sum (d.cons_quantity) as receive_qnty, sum (d.cons_amount) as receive_amount, b.uom, b.fabric_shade, b.dia_width_type, b.is_sales, b.order_id, b.fabric_description_id, d.store_id, d.floor_id,(case when d.room is null or d.room=0 then 0 else d.room end) room, (case when d.rack is null or d.rack=0 then 0 else d.rack end) rack, (case when d.self is null or d.self=0 then 0 else d.self end) self, (case when d.bin_box is null or d.bin_box=0 then 0 else d.bin_box end) bin_box 
	FROM inv_receive_master a, inv_transaction d, pro_finish_fabric_rcv_dtls b, product_details_master c WHERE a.id='$mrr_no' and a.entry_form in (7,37) and b.trans_id>0 and a.id=d.mst_id and d.id=b.trans_id and b.prod_id=c.id and d.item_category=2 and d.transaction_type=1 and d.status_active=1 and a.status_active=1 and b.status_active=1 group by a.company_id, a.location_id, a.id, a.store_id, c.id, c.product_name_details, b.batch_id, b.color_id, d.body_part_id, b.uom, b.fabric_shade, b.dia_width_type, b.is_sales, b.order_id, b.fabric_description_id, d.store_id, d.floor_id, d.room, d.rack, d.self,d.bin_box ";*/
	$sql = "SELECT a.company_id, a.location_id location, a.id as mrr_id, a.store_id, c.id as prod_id, c.product_name_details, b.batch_id, b.color_id, d.body_part_id, sum (d.cons_quantity) as receive_qnty, sum (d.cons_amount) as receive_amount, b.uom, b.fabric_shade, b.dia_width_type, b.is_sales, b.fabric_description_id, d.store_id, d.floor_id,(case when d.room is null or d.room=0 then 0 else d.room end) room, (case when d.rack is null or d.rack=0 then 0 else d.rack end) rack, (case when d.self is null or d.self=0 then 0 else d.self end) self, (case when d.bin_box is null or d.bin_box=0 then 0 else d.bin_box end) bin_box 
	FROM inv_receive_master a, inv_transaction d, pro_finish_fabric_rcv_dtls b, product_details_master c WHERE a.id='$mrr_no' and a.entry_form in (7,37) and b.trans_id>0 and a.id=d.mst_id and d.id=b.trans_id and b.prod_id=c.id and d.item_category=2 and d.transaction_type=1 and d.status_active=1 and a.status_active=1 and b.status_active=1 group by a.company_id, a.location_id, a.id, a.store_id, c.id, c.product_name_details, b.batch_id, b.color_id, d.body_part_id, b.uom, b.fabric_shade, b.dia_width_type, b.is_sales, b.fabric_description_id, d.store_id, d.floor_id, d.room, d.rack, d.self,d.bin_box ";
	//listagg(cast(b.trans_id as varchar2(4000)),',') within group (order by b.trans_id)  as tr_id,
	//echo $sql;

	$result = sql_select($sql);
	foreach($result as $row)
	{
		$store_arr[$row[csf("store_id")]] = $row[csf("store_id")];
		$company_id_arr[$row[csf("company_id")]] = $row[csf("company_id")];
		$batch_id_arr[$row[csf("batch_id")]] = $row[csf("batch_id")];
		$trans_id_id_arr[$row[csf("tr_id")]] = $row[csf("tr_id")];
		$company_id = $row[csf("company_id")];
		$store_id = $row[csf("store_id")];

		//$this_challan_rcv_qty_arr[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]]+=$row[csf('receive_qnty')];
	}

	if(empty($result))
	{
		echo "Data Not Found";
		die;
	}


	//$return_sql = "select a.received_id, d.pi_wo_batch_no, d.prod_id, d.cons_uom, d.body_part_id, d.fabric_shade, d.floor_id, d.room, d.rack, d.self, sum(c.quantity) as cumu_qnty from inv_issue_master a, order_wise_pro_details c, inv_transaction d where a.id = d.mst_id and c.trans_id=d.id and c.status_active=1 and c.entry_form in (46) and d.status_active=1 and d.is_deleted=0 and a.company_id=$company_id and d.store_id=$store_id and d.pi_wo_batch_no in (".implode($batch_id_arr,',').") group by a.received_id,d.pi_wo_batch_no, d.prod_id, d.cons_uom, d.floor_id,d.room,d.rack,d.self, d.fabric_shade, d.body_part_id";
	$return_sql = "select a.received_id, d.pi_wo_batch_no, d.prod_id, d.cons_uom, d.body_part_id, d.fabric_shade, d.floor_id, d.room, d.rack, d.self,d.bin_box, sum(d.cons_quantity) as cumu_qnty from inv_issue_master a, inv_transaction d where a.id = d.mst_id and a.entry_form in (46) and d.transaction_type=3 and d.item_category=2 and d.status_active=1 and d.is_deleted=0 and a.company_id=$company_id and d.store_id=$store_id and d.pi_wo_batch_no in (".implode($batch_id_arr,',').") group by a.received_id,d.pi_wo_batch_no, d.prod_id, d.cons_uom, d.floor_id,d.room,d.rack,d.self,d.bin_box, d.fabric_shade, d.body_part_id";
	// echo $return_sql;
	$return_data = sql_select($return_sql);
	foreach ($return_data as $row) 
	{

		if($row[csf("received_id")] == $mrr_no)
		{
			$this_challan_return_arr[$row[csf("prod_id")]][$row[csf("pi_wo_batch_no")]][$row[csf("fabric_shade")]][$row[csf("body_part_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]] += $row[csf("cumu_qnty")];
		}
		
		$return_arr[$row[csf("prod_id")]][$row[csf("pi_wo_batch_no")]][$row[csf("fabric_shade")]][$row[csf("body_part_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]] += $row[csf("cumu_qnty")];
		
	}

	if($db_type==0)
	{
		$rackCond = " IFNULL(a.rack, 0) rack";
		$rackCond2 = " IFNULL(b.rack, 0) rack_no";
		$rackCond3 = " (case when b.rack_no is null or b.rack_no=0 then 0 else b.rack_no end) rack_no";
		$rackCond4 = " (case when b.to_rack is null or b.to_rack=0 then 0 else b.to_rack end) rack_no";
	}else{
		$rackCond = " nvl(a.rack, 0) rack";
		$rackCond2 = " nvl(b.rack, 0) rack_no";
		$rackCond3 = " cast(b.rack_no as varchar(4000)) as rack_no";
		$rackCond4 = " cast(b.to_rack as varchar(4000)) as rack_no";
	}
	$issData = sql_select("SELECT a.prod_id, a.pi_wo_batch_no as batch_id,c.fabric_shade,c.body_part_id,(case when a.floor_id is null or a.floor_id=0 then 0 else a.floor_id end) floor_id,$rackCond,(case when a.room is null or a.room=0 then 0 else a.room end) room,(case when a.self is null or a.self=0 then 0 else a.self end) self,(case when a.bin_box is null or a.bin_box=0 then 0 else a.bin_box end) bin, sum(case when a.transaction_type=2 then cons_quantity end) as issue_qnty 
	from inv_issue_master b,inv_transaction a,inv_finish_fabric_issue_dtls c, pro_batch_create_mst d 
	where b.entry_form=18 and b.id=a.mst_id and a.id=c.trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=2 and a.transaction_type=2 and c.status_active=1 and c.is_deleted=0 and a.pi_wo_batch_no = d.id and b.status_active=1 and b.is_deleted=0 and b.company_id=$company_id and a.store_id ='$store_id' and a.pi_wo_batch_no in(".implode(',', $batch_id_arr).") 
	group by a.prod_id, a.pi_wo_batch_no,c.fabric_shade, c.body_part_id,a.floor_id, a.room, a.rack, a.self,a.bin_box");
		
	foreach($issData as $row)
	{
		$issue_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin')]]+=$row[csf('issue_qnty')];
	}


	$issueReturnData=sql_select("select a.prod_id, a.batch_id_from_fissuertn as batch_id, b.fabric_shade, b.body_part_id,  a.floor_id,a.room, a.rack, a.self,a.bin_box, sum(case when a.transaction_type=4 then a.cons_quantity end) as issrqnty from inv_transaction a,pro_finish_fabric_rcv_dtls b,pro_batch_create_mst c where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=2 and a.transaction_type in(4) and a.batch_id_from_fissuertn = c.id and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id and a.store_id ='$store_id' and a.batch_id_from_fissuertn in(".implode(',', $batch_id_arr).")  group by a.prod_id, b.fabric_shade, b.body_part_id, a.batch_id_from_fissuertn,a.floor_id,a.room, a.rack, a.self,a.bin_box");
	foreach($issueReturnData as $row)
	{
		$floor_id = ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
		$room = ($row[csf('room')]=="")?0:$row[csf('room')];
		$rack = ($row[csf('rack')]=="")?0:$row[csf('rack')];
		$self = ($row[csf('self')]=="")?0:$row[csf('self')];
		$bin = ($row[csf('bin_box')]=="")?0:$row[csf('bin_box')];
		$issRt_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$floor_id][$room][$rack][$self][$bin] +=$row[csf('issrqnty')];
	}

	$transOutData = sql_select("select b.batch_id, b.from_store, (case when b.floor_id is null or b.floor_id=0 then 0 else b.floor_id end) as floor, b.fabric_shade,c.body_part_id, (case when b.room is null or b.room=0 then 0 else b.room end) room, $rackCond2,(case when b.shelf is null or b.shelf=0 then 0 else b.shelf end) shelf_no,(case when b.bin_box is null or b.bin_box=0 then 0 else b.bin_box end) bin_box, sum(b.transfer_qnty) as trans_out_qnty, b.from_prod_id as prod_id
		from inv_transaction c,order_wise_pro_details d,inv_item_transfer_dtls b, pro_batch_create_mst a
		where c.id=d.trans_id and d.dtls_id=b.id and c.company_id =$company_id and b.from_store =$store_id and c.transaction_type=6 and c.item_category=2 and b.batch_id=a.id and c.status_active=1 and c.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and d.status_active=1 and d.is_deleted =0 and d.entry_form=14 and d.trans_type=6 and b.active_dtls_id_in_transfer=1 and b.batch_id in (".implode(',', $batch_id_arr).")
		group by b.batch_id, b.from_store, b.floor_id, b.fabric_shade, c.body_part_id, b.room,b.rack,b.shelf,b.bin_box,b.from_prod_id");

	foreach($transOutData as $row)
	{
		$trans_out_qnty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin_box')]]=$row[csf('trans_out_qnty')];
	}

	if($db_type ==0)
	{
		$sales_flag_cond = " and (b.is_sales=0 or b.is_sales ='' ) ";
	}
	else
	{
		$sales_flag_cond = " and (b.is_sales=0 or b.is_sales is null) ";
	}
	$data_sql="select x.id, x.product_name_details, x.color, x.unit_of_measure, x.current_stock, x.company_id, x.store_id, x.floor, x.fabric_shade, x.body_part_id, x.batch_id,x.room,x.rack_no, x.shelf_no,x.bin_box, sum(x.cons_amount) as cons_amount, sum(x.qnty) as qnty, x.prod_id,x.detarmination_id
		from
		(
		select a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock,a.company_id,c.store_id,(case when b.floor is null or b.floor=0 then 0 else b.floor end) as  floor,b.fabric_shade, b.body_part_id, b.batch_id,(case when b.room is null or b.room=0 then 0 else b.room end) room,$rackCond3,(case when b.shelf_no is null or b.shelf_no=0 then 0 else b.shelf_no end) shelf_no,(case when b.bin is null or b.bin=0 then 0 else b.bin end) bin_box,sum(d.cons_quantity) as qnty, d.gmt_item_id, b.prod_id, sum(d.cons_amount) as cons_amount,a.detarmination_id
		from product_details_master a, pro_finish_fabric_rcv_dtls b,inv_transaction d, inv_receive_master c, pro_batch_create_mst e
		where a.id=b.prod_id and b.trans_id=d.id and d.mst_id=c.id and b.batch_id = e.id and b.batch_id in(".implode(',', $batch_id_arr).") and c.company_id=$company_id and d.store_id =$store_id and c.entry_form in (7,37) $sales_flag_cond and a.item_category_id=2 and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 group by a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock,a.company_id,c.store_id, b.floor, b.fabric_shade,b.body_part_id, b.batch_id, b.room, b.rack_no, b.shelf_no,b.bin, d.gmt_item_id, b.prod_id, a.detarmination_id
		union all
		select a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock,a.company_id,c.store_id,(case when b.to_floor_id is null or b.to_floor_id=0 then 0 else b.to_floor_id end) as  floor, b.fabric_shade, c.body_part_id, b.to_batch_id as batch_id,(case when b.to_room is null or b.to_room=0 then 0 else b.to_room end) room, $rackCond4, (case when b.to_shelf is null or b.to_shelf=0 then 0 else b.to_shelf end) shelf_no, (case when b.to_bin_box is null or b.to_bin_box=0 then 0 else b.to_bin_box end) bin_box, sum(c.cons_quantity) as qnty, 0 as gmt_item_id, b.to_prod_id as prod_id, sum(c.cons_amount) as cons_amount,a.detarmination_id
		from product_details_master a, inv_item_transfer_dtls b, inv_transaction c, inv_item_transfer_mst d, pro_batch_create_mst e
		where a.id = b.to_prod_id and b.to_trans_id = c.id and b.mst_id = d.id and d.to_company = $company_id and c.store_id =$store_id and c.transaction_type = 5 and c.item_category = 2  and b.to_batch_id = e.id  and b.to_batch_id in (".implode(',', $batch_id_arr).") and c.status_active =1 and c.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and d.status_active = 1 and d.is_deleted =0 and a.status_active=1 and a.is_deleted=0
		group by a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock,a.company_id,c.store_id, b.to_floor_id, b.fabric_shade, c.body_part_id, b.to_batch_id, b.to_room, b.to_rack, b.to_shelf, b.to_bin_box, b.to_prod_id, a.detarmination_id
		) x
		group by x.id, x.product_name_details, x.color, x.unit_of_measure, x.current_stock, x.company_id,x.store_id, x.floor, x.fabric_shade, x.body_part_id, x.batch_id, x.room,x.rack_no, x.shelf_no, x.bin_box,  x.prod_id, x.detarmination_id";

	$data_array=sql_select($data_sql);
	foreach ($data_array as $row) 
	{
		$rcv_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin_box')]]+=$row[csf('qnty')];
	}

	$batch_arr=return_library_array("select id, batch_no from pro_batch_create_mst where id in(".implode(",",$batch_id_arr).")","id","batch_no");
	$floor_roo_rak_arr=return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst","floor_room_rack_id","floor_room_rack_name");

	$i=1;
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="650">
		<thead>
			<tr>
				<th width="30">SL</th>
				<th width="40">Product Id</th>
				<th width="140">Product Name</th>
				<th width="70">Batch No.</th>
				<th width="70">Color</th>
				<th width="70">Uom</th>
				<th width="50">Shade</th>
				<th width="50">Floor</th>
				<th width="50">Room</th>
				<th width="50">Rack</th>
				<th width="50">Shelf</th>
				<th width="50">Bin</th>
				<th>Curr.Stock</th>
			</tr>
		</thead>
		<tbody>
			<?

			foreach($result as $row)
			{
				if ($i%2==0)$bgcolor="#E9F3FF";
				else $bgcolor="#FFFFFF";

				$this_challan_return_qnty = $this_challan_return_arr[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]];
				$issue_quantity = $issue_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$row[csf('floor_id')]][$row[csf("room")]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]];
				$issue_return_quantity = $issRt_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]];
				$trans_out_qnty = $trans_out_qnty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf("bin_box")]];
				
				$return_quantity = $return_arr[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf("bin_box")]];
				$rcv_transfer_in_qty = $rcv_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf("bin_box")]];


				$global_ref_stock = ($rcv_transfer_in_qty +$issue_return_quantity) - ($issue_quantity + $trans_out_qnty + $return_quantity);

				//echo "global_stock = (".$rcv_transfer_in_qty."+".$issue_return_quantity.") - (".$issue_quantity."+".$trans_out_qnty."-".$return_quantity.")<br>";
				//echo "balance_stock = (".$row[csf("receive_qnty")].") - (".$this_challan_return_qnty.")<br>";
				//echo $row[csf("receive_qnty")].", this return=".$this_challan_return_qnty.", iss=".$issue_quantity.", iss ret=".$issue_return_quantity.", tr out=".$trans_out_qnty.", return=".$return_quantity.", rcv_trans_in =".$rcv_transfer_in_qty."<br>";

				$balance_qnty=$row[csf("receive_qnty")]-$this_challan_return_qnty;

				$floor 		= $floor_roo_rak_arr[$row[csf("floor_id")]];
				$room 		= $floor_roo_rak_arr[$row[csf("room")]];
				$rack_no	= $floor_roo_rak_arr[$row[csf("rack")]];
				$shelf_no 	= $floor_roo_rak_arr[$row[csf("self")]];
				$bin_no 	= $floor_roo_rak_arr[$row[csf("bin_box")]];

				$cons_rate = $row[csf('receive_amount')]/$row[csf('receive_qnty')];
				$cons_rate = number_format($cons_rate,2,".","");
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf('prod_id')]."**".$row[csf('rack')]."**".$row[csf('self')]."**".$row[csf('color_id')]."**".$row[csf('uom')]."**".$row[csf('fabric_shade')]."**".$floor."**".$room."**".$rack_no."**".$shelf_no."**".$row[csf('floor_id')]."**".$row[csf('room')]."**".$row[csf('batch_id')]."**".$batch_arr[$row[csf('batch_id')]]."**".$row[csf('store_id')]."**".$row[csf('body_part_id')]."**".$cons_rate."**".$row[csf('fabric_description_id')]."**".$row[csf("mrr_id")]."**".$row[csf("dia_width_type")]."**".$row[csf("receive_qnty")]."**".$this_challan_return_qnty."**".$global_ref_stock."**".$row[csf('bin_box')]."**".$bin_no; ?>","item_details_form_input","requires/finish_fab_garments_receive_rtn_controller")' style="cursor:pointer" >
					<td align="center"><? echo $i; ?></td>
					<td align="center"><? echo $row[csf("prod_id")]; ?></td>
					<td><? echo $row[csf("product_name_details")]; ?></td>
					<td><? echo $batch_arr[$row[csf("batch_id")]]; ?></td>
					<td><? echo $color_arr[$row[csf("color_id")]]; ?></td>
					<td align="center"><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
					<td align="center"><? echo $fabric_shade[$row[csf("fabric_shade")]]; ?></td>
					<td><? echo $floor; ?></td>
					<td><? echo $room; ?></td>
					<td><? echo $rack_no; ?></td>
					<td><? echo $bin_no; ?></td>
					<td title="<? echo 'Rcv: ('.$rcv_transfer_in_qty.' + Issue Return: '.$issue_return_quantity.') - (Issue qty: '.$issue_quantity.' + Trans Out: '.$trans_out_qnty.'+ Return Qty: '.$return_quantity.')'; ?>"><? echo $shelf_no; ?></td>
					<td align="right" title="<? echo $global_ref_stock;?>"><? echo number_format($balance_qnty,0); ?></td>
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

//child form data input here-----------------------------//
if($action=="item_details_form_input")
{
	$data_ref=explode("**",$data);

	$prod_id = $data_ref[0];
	$rack = $data_ref[1];
	$shelf = $data_ref[2];
	$color_id = $data_ref[3];
	$uom = $data_ref[4];
	$fabric_shade = $data_ref[5];
	$floor_name = $data_ref[6];
	$room_name = $data_ref[7];
	$rack_name = $data_ref[8];
	$shelf_name = $data_ref[9];
	$floor_id = $data_ref[10];
	$room_id = $data_ref[11];
	$batch_id = $data_ref[12];
	$batch_no = $data_ref[13];
	$store_id = $data_ref[14];
	$body_part_id = $data_ref[15];
	$cons_rate = $data_ref[16];
	$fabric_description_id = $data_ref[17];
	$mrr_id = $data_ref[18];
	$dia_width_type = $data_ref[19];
	$receive_quantity = $data_ref[20];
	$return_quantity = $data_ref[21];
	$global_ref_stock = $data_ref[22];
	$bin = $data_ref[23];
	$bin_name = $data_ref[24];

	//$row[csf('prod_id')]."**".$row[csf('rack')]."**".$row[csf('self')]."**".$color_arr[$row[csf('color_id')]]."**".$row[csf('uom')]."**".$row[csf('fabric_shade')]."**".$floor."**".$room."**".$rack_no."**".$shelf_no."**".$row[csf('floor_id')]."**".$row[csf('room')]."**".$row[csf('batch_id')]."**".$batch_arr[$row[csf('batch_id')]]."**".$row[csf('store_id')]."**".$row[csf('body_part_id')]."**".$row[csf('cons_rate')]."**".$row[csf('fabric_description_id')]."**".$row[csf("mrr_id")];

	$sql = "select a.id as mst_id, a.booking_without_order, a.store_id, b.company_id, c.floor,c.room,c.rack_no, c.shelf_no,c.bin, c.body_part_id, b.cons_uom, b.cons_rate, c.batch_id, c.color_id, c.order_id, c.dia_width_type,c.fabric_shade,c.is_sales, d.id as prod_id, d.product_name_details, d.gsm, d.dia_width, d.current_stock
	from inv_receive_master a, inv_transaction b, pro_finish_fabric_rcv_dtls c, product_details_master d
	where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and a.id=".$mrr_id." and b.pi_wo_batch_no=$batch_id and b.body_part_id = $body_part_id and b.store_id=$store_id and b.prod_id =$prod_id and b.fabric_shade=$fabric_shade and b.floor_id='$floor_id' and b.room='$room_id' and b.rack='$rack' and b.self='$shelf' and b.bin_box='$bin'  and a.entry_form in (7,37) and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.is_sales=0
	group by a.id, a.booking_without_order, a.store_id, b.company_id, c.floor,c.room,c.rack_no, c.shelf_no,c.bin, c.body_part_id, b.cons_uom, b.cons_rate, c.batch_id, c.color_id, c.order_id, c.dia_width_type, c.fabric_shade, c.is_sales, d.id, d.product_name_details, d.gsm, d.dia_width, d.current_stock"; 
	//b.id as trans_id,

	$result = sql_select($sql);
	foreach($result as $row)
	{
		$batch_id_arr[$row[csf("batch_id")]] = $row[csf("batch_id")];
		$prod_id_arr[$row[csf("prod_id")]] = $row[csf("prod_id")];
		$trans_id_arr[$row[csf("trans_id")]] = $row[csf("trans_id")];

		if($row[csf('is_sales')]==1)
		{
			$sales_arr[$row[csf('order_id')]] = $row[csf('order_id')];
		}
		else
		{
			$po_arr[$row[csf('order_id')]] = $row[csf('order_id')];
		}
		$product_name_details = $row[csf('product_name_details')];
		$gsm = $row[csf('gsm')];
		$dia_width = $row[csf('dia_width')];
		$is_sales = $row[csf('is_sales')];
	}
	$po_arr = array_filter($po_arr);
	$batch_id_arr = array_filter($batch_id_arr);
	if(!empty($batch_id_arr)){

		$batch_arr=return_library_array("select id,batch_no from pro_batch_create_mst where id in(".implode(",",$batch_id_arr).")","id","batch_no");
		$bookingNO = return_field_value( 'booking_no', 'pro_batch_create_mst', "id in(".implode(",",$batch_id_arr).")", 'booking_no' );
	}

	/*if(!empty($po_arr))
	{
		$sql_booking=sql_select("select a.po_break_down_id,a.booking_no from wo_booking_dtls a where a.po_break_down_id in(".implode(",",$po_arr).") and a.status_active=1 and a.is_deleted=0  and a.booking_type in(1,4)  group by a.po_break_down_id,a.booking_no");

		foreach($sql_booking as $row)
		{
			$bookingData[$row[csf("po_break_down_id")]] = $row[csf("booking_no")];
			$bookingNO= $row[csf("booking_no")];
		}
	}*/

	/*if(!empty($trans_id_arr))
	{
		$cumilitive_rtn = sql_select("select prod_id,recv_trans_id,sum(issue_qnty) as issue_qnty from inv_mrr_wise_issue_details where status_active=1 and prod_id in(".implode(",",$prod_id_arr).") and recv_trans_id in(".implode(",",$trans_id_arr).") group by prod_id,recv_trans_id");
		foreach ($cumilitive_rtn as $return_row) {
			$cumilitive_rtn_arr[$return_row[csf("prod_id")]][$return_row[csf("recv_trans_id")]] = $return_row[csf("issue_qnty")];
		}
	}*/

		//==========================================================================

		
		echo "$('#tbl_child').find('input,select').val('');\n";



		echo "$('#txt_item_description').val('".$product_name_details."');\n";
		echo "$('#txt_prod_id').val('".$prod_id."');\n";
		echo "$('#txt_dia_width_type').val('".$dia_width_type."');\n";
		echo "$('#txt_fabric_shade').val('".$fabric_shade."');\n";
		echo "$('#txt_gsm').val('".$gsm."');\n";
		echo "$('#txt_dia').val('".$dia_width."');\n";

		echo "$('#txt_batch_no').val('".$batch_no."');\n";
		echo "$('#cbo_body_part').val('".$body_part_id."');\n";

		//echo "load_room_rack_self_bin('requires/finish_fab_garments_receive_rtn_controller*2', 'store','store_td', '".$row[csf('company_id')]."','"."',this.value);\n";
		echo "$('#cbo_store_name').val('".$store_id."');\n";

		//echo "load_room_rack_self_bin('requires/finish_fab_garments_receive_rtn_controller', 'floor','floor_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."',this.value);\n";
		echo "$('#cbo_floor').val('".$floor_id."');\n";
		echo "$('#cbo_floor_name').val('".$floor_name."');\n";
		//echo "load_room_rack_self_bin('requires/finish_fab_garments_receive_rtn_controller', 'room','room_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor')]."',this.value);\n";
		echo "$('#cbo_room').val('".$room_id."');\n";
		echo "$('#cbo_room_name').val('".$room_name."');\n";
		//echo "load_room_rack_self_bin('requires/finish_fab_garments_receive_rtn_controller', 'rack','rack_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor')]."','".$row[csf('room')]."',this.value);\n";
		echo "$('#txt_rack').val('".$rack."');\n";
		echo "$('#txt_rack_name').val('".$rack_name."');\n";
		//echo "load_room_rack_self_bin('requires/finish_fab_garments_receive_rtn_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor')]."','".$row[csf('room')]."','".$row[csf('rack_no')]."',this.value);\n";
		echo "$('#txt_shelf').val('".$shelf."');\n";
		echo "$('#txt_bin').val('".$bin."');\n";
		echo "$('#txt_shelf_name').val('".$shelf_name."');\n";
		echo "$('#txt_bin_name').val('".$bin_name."');\n";


		echo "$('#txt_return_qnty').val('');\n";
		echo "$('#txt_break_qnty').val('');\n";
		echo "$('#txt_break_roll').val('');\n";
		echo "$('#txt_order_id_all').val('');\n";
		echo "$('#txt_roll').val('');\n";

		echo "$('#before_prod_id').val('');\n";
		echo "$('#update_details_id').val('');\n";
		echo "$('#update_id').val('');\n";

		echo "$('#txt_fabric_received').val('".$receive_quantity."');\n";
		//echo "$('#hidden_receive_trans_id').val('".$row[csf("trans_id")]."');\n";
		echo "$('#hidden_batch_id').val('".$batch_id."');\n";

		echo "$('#txt_cons_rate').val('".$cons_rate."');\n";

		$cons_amount =$cons_rate*$receive_quantity;

		echo "$('#txt_amount').val('".$cons_amount."');\n";
		echo "$('#txt_booking_no').val('".$bookingNO."');\n";


		if(empty($po_arr))
		{
			echo "$('#txt_return_qnty').removeAttr('placeholder').removeAttr('readonly').removeAttr('onDblClick').attr('placeholder','write');\n";
		}
		else
		{
			echo "$('#txt_return_qnty').removeAttr('placeholder').removeAttr('readonly').removeAttr('onDblClick').attr('placeholder','Double Click To Search').attr('readonly','').attr('onDblClick','openmypage_rtn_qty();');\n";
		}

		$variable_setting_production=return_field_value("fabric_roll_level","variable_settings_production","company_name='".$row[csf("company_id")]."'  and item_category_id=2 and variable_list=3 and status_active=1","fabric_roll_level");
		if($variable_setting_production==1)
		{
			echo "$('#txt_roll').attr('readonly');\n";
		}
		else
		{
			echo "$('#txt_roll').removeAttr('readonly');\n";
		}

		//$cumilitive_rtn = $cumilitive_rtn_arr[$row[csf("prod_id")]][$row[csf("trans_id")]];
		$yet_to_issue=$receive_quantity-$return_quantity;
		echo "$('#cbo_uom').val('".$uom."');\n";
		echo "$('#txt_color_name').val('".$color_arr[$color_id]."');\n";
		echo "$('#txt_color_id').val('".$color_id."');\n";  

		echo "$('#txt_cumulative_issued').val('$return_quantity');\n";
		echo "$('#txt_yet_to_issue').val('$yet_to_issue');\n";
		echo "$('#txt_global_stock').val('".$global_ref_stock."');\n";

		echo "$('#cbo_store_name').attr('disabled','disabled');\n";
		echo "$('#cbo_floor').attr('disabled','disabled');\n";
		echo "$('#cbo_room').attr('disabled','disabled');\n";
		echo "$('#txt_rack').attr('disabled','disabled');\n";
		echo "$('#txt_shelf').attr('disabled','disabled');\n";
		echo "$('#txt_bin').attr('disabled','disabled');\n";

		echo "set_button_status(0, permission, 'fnc_yarn_receive_return_entry',1,1);\n";

	
	/*foreach($result as $row)
	{
		if($row[csf('is_sales')]==0){
			$bookingNO = $bookingData[$row[csf("order_id")]];
		}

		echo "$('#txt_item_description').val('".$row[csf("product_name_details")]."');\n";
		echo "$('#txt_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#txt_dia_width_type').val('".$row[csf("dia_width_type")]."');\n";
		echo "$('#txt_fabric_shade').val('".$row[csf("fabric_shade")]."');\n";
		echo "$('#txt_gsm').val('".$row[csf("gsm")]."');\n";
		echo "$('#txt_dia').val('".$row[csf("dia_width")]."');\n";

		echo "$('#txt_batch_no').val('".$batch_arr[$row[csf("batch_id")]]."');\n";
		echo "$('#cbo_body_part').val('".$row[csf("body_part_id")]."');\n";

		echo "load_room_rack_self_bin('requires/finish_fab_garments_receive_rtn_controller*2', 'store','store_td', '".$row[csf('company_id')]."','"."',this.value);\n";
		echo "$('#cbo_store_name').val('".$row[csf("store_id")]."');\n";

		echo "load_room_rack_self_bin('requires/finish_fab_garments_receive_rtn_controller', 'floor','floor_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."',this.value);\n";
		echo "$('#cbo_floor').val('".$row[csf("floor")]."');\n";
		echo "load_room_rack_self_bin('requires/finish_fab_garments_receive_rtn_controller', 'room','room_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor')]."',this.value);\n";
		echo "$('#cbo_room').val('".$row[csf("room")]."');\n";
		echo "load_room_rack_self_bin('requires/finish_fab_garments_receive_rtn_controller', 'rack','rack_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor')]."','".$row[csf('room')]."',this.value);\n";
		echo "$('#txt_rack').val('".$row[csf("rack_no")]."');\n";
		echo "load_room_rack_self_bin('requires/finish_fab_garments_receive_rtn_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor')]."','".$row[csf('room')]."','".$row[csf('rack_no')]."',this.value);\n";
		echo "$('#txt_shelf').val('".$row[csf("shelf_no")]."');\n";


		echo "$('#txt_return_qnty').val('');\n";
		echo "$('#txt_break_qnty').val('');\n";
		echo "$('#txt_break_roll').val('');\n";
		echo "$('#txt_order_id_all').val('');\n";
		echo "$('#txt_roll').val('');\n";

		echo "$('#txt_fabric_received').val('".$row[csf("cons_quantity")]."');\n";
		echo "$('#hidden_receive_trans_id').val('".$row[csf("trans_id")]."');\n";
		echo "$('#hidden_batch_id').val('".$row[csf("batch_id")]."');\n";

		echo "$('#txt_cons_rate').val('".$row[csf("cons_rate")]."');\n";
		echo "$('#txt_amount').val('".$row[csf("cons_amount")]."');\n";
		echo "$('#txt_booking_no').val('".$bookingNO."');\n";


		if($row[csf("order_id")]=="")
		{
			echo "$('#txt_return_qnty').removeAttr('placeholder').removeAttr('readonly').removeAttr('onDblClick').attr('placeholder','write');\n";
		}
		else
		{
			echo "$('#txt_return_qnty').removeAttr('placeholder').removeAttr('readonly').removeAttr('onDblClick').attr('placeholder','Double Click To Search').attr('readonly','').attr('onDblClick','openmypage_rtn_qty();');\n";
		}

		$variable_setting_production=return_field_value("fabric_roll_level","variable_settings_production","company_name='".$row[csf("company_id")]."'  and item_category_id=2 and variable_list=3 and status_active=1","fabric_roll_level");
		if($variable_setting_production==1)
		{
			echo "$('#txt_roll').attr('readonly');\n";
		}
		else
		{
			echo "$('#txt_roll').removeAttr('readonly');\n";
		}

		$cumilitive_rtn = $cumilitive_rtn_arr[$row[csf("prod_id")]][$row[csf("trans_id")]];
		$yet_to_issue=$row[csf("cons_quantity")]-$cumilitive_rtn;
		echo "$('#cbo_uom').val('".$row[csf("cons_uom")]."');\n";
		echo "$('#txt_color_name').val('".$color_arr[$row[csf("color_id")]]."');\n";
		echo "$('#txt_cumulative_issued').val('$cumilitive_rtn');\n";
		echo "$('#txt_yet_to_issue').val('$yet_to_issue');\n";
		echo "$('#txt_global_stock').val('".$row[csf("current_stock")]."');\n";

		echo "$('#cbo_store_name').attr('disabled','disabled');\n";
		echo "$('#cbo_floor').attr('disabled','disabled');\n";
		echo "$('#cbo_room').attr('disabled','disabled');\n";
		echo "$('#txt_rack').attr('disabled','disabled');\n";
		echo "$('#txt_shelf').attr('disabled','disabled');\n";

	}*/
	exit();
}


/*if($action=="return_po_popup_old")
{
	echo load_html_head_contents("Receive Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$txt_received_id=str_replace("'","",$txt_received_id);
	$txt_prod_id=str_replace("'","",$txt_prod_id);
	$hidden_receive_trans_id=str_replace("'","",$hidden_receive_trans_id);
	$update_id=str_replace("'","",$update_id);

	$variable_setting_production=return_field_value("fabric_roll_level","variable_settings_production","company_name='$cbo_company_id'  and item_category_id=2 and variable_list=3 and status_active=1","fabric_roll_level");
	//echo $variable_setting_production.Fuad;//die;
	if($variable_setting_production==1)
	{
		$table_width=600;
		$txt_break_roll=explode("_",$txt_break_roll);
		foreach($txt_break_roll as $val)
		{
			$txt_break_roll_data=explode("**",$val);
			$po_id=$txt_break_roll_data[0];
			$roll_no=$txt_break_roll_data[1];
			$qty=$txt_break_roll_data[2];
			$roll_id=$txt_break_roll_data[3];

			$order_wise_qnty_arr[$po_id][$roll_id]=$qty;
		}
	}
	else
	{
		$table_width=500;
		$txt_break_qnty=explode("_",$txt_break_qnty);
		foreach($txt_break_qnty as $val)
		{
			$txt_break_qnty_data=explode("**",$val);
			$po_id=$txt_break_qnty_data[0];
			$qty=$txt_break_qnty_data[1];

			$order_wise_qnty_arr[$po_id]=$qty;
		}
	}
	?>
	<script>
		function js_set_value()
		{
			var table_legth=$('#pop_table tbody tr').length;
			var break_qnty=break_roll=break_id="";
			var tot_qnty=0; var tot_roll='';
			for(var i=1; i<=table_legth; i++)
			{

				tot_qnty +=($("#issueqnty_"+i).val()*1);
				if(break_qnty!="") break_qnty +="_";
				break_qnty+=($("#poId_"+i).val()*1)+'**'+($("#issueqnty_"+i).val()*1);
				if(break_roll!="") break_roll +="_";
				break_roll+=($("#poId_"+i).val()*1)+'**'+($("#roll_"+i).val()*1)+'**'+($("#issueqnty_"+i).val()*1)+'**'+($("#rollId_"+i).val()*1);
				if(break_id!="") break_id +=",";
				break_id+=($("#poId_"+i).val()*1);

				if($("#issueqnty_"+i).val()*1>0 && $("#rollId_"+i).val()*1>0)
				{
					tot_roll+=1;
				}
			}

			$("#tot_qnty").val(tot_qnty);
			$("#break_qnty").val(break_qnty);
			$("#break_roll").val(break_roll);
			$("#break_order_id").val(break_id);
			$("#tot_roll").val(tot_roll);
			parent.emailwindow.hide();
		}

		function fn_calculate(id)
		{
			var recv_qnty=($("#recevqnty_"+id).val()*1);
			var cumu_qnty=($("#cumulativeIssue_"+id).val()*1);
			var issue_qnty=($("#issueqnty_"+id).val()*1);
			var hiddenissue_qnty=($("#hiddenissueqnty_"+id).val()*1);
			if(((cumu_qnty*1)+(issue_qnty*1))>((recv_qnty*1)+(hiddenissue_qnty*1)))
			{
				alert("Return Quantity Can not be Greater Than Receive Quantity.");
				$("#issueqnty_"+id).val(0);
			}
		}
	</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" id="pop_table">
					<thead>
						<tr>
							<th width="140">Order/FSO No</th>
							<th width="140">Booking No</th>
							<th width="120">Receive Quantity</th>
							<th width="120">Cumulative Issue</th>
							<?
							if($variable_setting_production==1)
							{
								?>
								<th>Roll</th>
								<?
							}
							?>
							<th width="120">Return Quantity</th>
						</tr>
					</thead>
					<tbody>
						<?
						$cumu_iss_arr=array();
						if($variable_setting_production==1)
						{
							$cumu_iss_data_arr = sql_select("select d.po_breakdown_id, d.roll_id, d.qnty from inv_mrr_wise_issue_details b, order_wise_pro_details c, pro_roll_details d where b.issue_trans_id=c.trans_id and c.trans_id=d.dtls_id and c.status_active=1 and b.recv_trans_id='$hidden_receive_trans_id' and c.entry_form=46 and d.entry_form=46 group by d.po_breakdown_id, d.roll_id, d.qnty");
							foreach($cumu_iss_data_arr as $rowR)
							{
								$cumu_iss_arr[$rowR[csf('po_breakdown_id')]][$rowR[csf('roll_id')]]+=$rowR[csf('qnty')];
							}
						}
						else
						{
							$cumu_iss_arr = return_library_array("select c.po_breakdown_id, sum(c.quantity) as cumu_qnty from inv_mrr_wise_issue_details b, order_wise_pro_details c where b.issue_trans_id=c.trans_id and c.status_active=1 and b.recv_trans_id='$hidden_receive_trans_id' and c.entry_form=46 group by c.po_breakdown_id","po_breakdown_id","cumu_qnty");
						}

						if($variable_setting_production==1)
						{
							$sql="select a.is_sales,c.id as roll_id, c.po_breakdown_id, c.roll_no, c.qnty as receive_qnty from inv_transaction b, order_wise_pro_details a, pro_roll_details c where b.id=a.trans_id and a.dtls_id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_id' and b.mst_id='$txt_received_id' and b.id='$hidden_receive_trans_id' and b.prod_id='$txt_prod_id' and a.entry_form in(7,37) and c.entry_form in(7,37) and a.trans_type in(1,4) and b.transaction_type in(1,4) group by a.is_sales,c.id, c.po_breakdown_id, c.roll_no, c.qnty";
						}
						else
						{
							$sql="select a.po_breakdown_id,a.is_sales, sum(a.quantity) as receive_qnty from  order_wise_pro_details a, inv_transaction b where a.trans_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_id' and b.mst_id='$txt_received_id' and b.prod_id='$txt_prod_id' and a.entry_form in(7,37) and b.id='$hidden_receive_trans_id' and b.transaction_type in(1,4) and a.trans_type in(1,4) group by a.po_breakdown_id,a.is_sales";
						}
						//echo $sql;
						$sql_result=sql_select($sql);
						$po_arr=$sales_arr=array();
						foreach($sql_result as $row)
						{
							$po_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
						}

						if(!empty($po_arr)){
							$po_no_arr = return_library_array("select id,po_number from wo_po_break_down where id in(".implode(",",$po_arr).")","id","po_number");
						}

						$i=1;
						foreach($sql_result as $row)
						{
							if($variable_setting_production==1)
							{
								$cumilitive_issue=$cumu_iss_arr[$row[csf('po_breakdown_id')]][$row[csf('roll_id')]];
							}
							else
							{
								$cumilitive_issue=$cumu_iss_arr[$row[csf('po_breakdown_id')]];
							}
							?>
							<tr>
								<td align="center"><input type="text" id="poNo_<? echo $i; ?>" name="poNo_<? echo $i; ?>" class="text_boxes" style="width:140px" value="<? echo $po_no_arr[$row[csf("po_breakdown_id")]];  ?>"  readonly disabled >
									<input type="hidden" id="poId_<? echo $i; ?>" name="poId_<? echo $i; ?>" class="text_boxes" style="width:140px" value="<? echo $row[csf("po_breakdown_id")];  ?>"  readonly disabled >
								</td>

								<td align="center">
									<input type="text" id="bookingno_<? echo $i; ?>" name="bookingno_<? echo $i; ?>" class="text_boxes" style="width:140px" value="<? echo $txt_booking_no;  ?>"  readonly disabled >

								</td>

								<td align="center"> <input type="text" id="recevqnty_<? echo $i; ?>" name="recevqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:110px" value="<? echo number_format($row[csf("receive_qnty")],2);  ?>" readonly disabled ></td>
								<td align="center">
									<input type="text" id="cumulativeIssue_<? echo $i; ?>" name="cumulativeIssue_<? echo $i; ?>" value="<? echo number_format($cumilitive_issue,2); ?>" class="text_boxes_numeric" style="width:110px" readonly disabled >
								</td>
								<?
								if($variable_setting_production==1)
								{
									?>
									<td align="center">
										<input type="text" id="roll_<? echo $i; ?>" name="roll_<? echo $i; ?>" value="<? echo $row[csf("roll_no")]; ?>" class="text_boxes_numeric" style="width:80px" readonly disabled >
										<input type="hidden" id="rollId_<? echo $i; ?>" name="rollId_<? echo $i; ?>" value="<? echo $row[csf("roll_id")]; ?>">
									</td>
									<td align="center">
										<input type="text" id="issueqnty_<? echo $i; ?>" name="issueqnty_<? echo $i; ?>" onKeyUp="fn_calculate(<? echo $i; ?>);" class="text_boxes_numeric" value="<? echo $order_wise_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("roll_id")]]; ?>" style="width:110px" >
										<input type="hidden" id="hiddenissueqnty_<? echo $i; ?>" name="hiddenissueqnty_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $order_wise_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("roll_id")]]; ?>">
									</td>
									<?
								}
								else
								{
									?>
									<td align="center" style="display:none;">
										<input type="text" id="roll_<? echo $i; ?>" name="roll_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" >
										<input type="hidden" id="rollId_<? echo $i; ?>" name="rollId_<? echo $i; ?>" value="">
									</td>
									<td align="center">
										<input type="text" id="issueqnty_<? echo $i; ?>" name="issueqnty_<? echo $i; ?>" onKeyUp="fn_calculate(<? echo $i; ?>);" class="text_boxes_numeric" value="<? echo $order_wise_qnty_arr[$row[csf("po_breakdown_id")]]; ?>" style="width:110px" >
										<input type="hidden" id="hiddenissueqnty_<? echo $i; ?>" name="hiddenissueqnty_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $order_wise_qnty_arr[$row[csf("po_breakdown_id")]]; ?>">
									</td>
									<?
								}
								?>
							</tr>
							<?
							$i++;
						}
						?>
					</tbody>
				</table>
				<table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0" border="0" rules="all" align="center">
					<tr>
						<td align="center">
							<input type="button" id="btn_close" name="" value="Close" onClick="js_set_value();" style="width:150px;" class="formbutton" >
							<input type="hidden" id="tot_qnty" name="tot_qnty" >
							<input type="hidden" id="break_qnty" name="break_qnty" >
							<input type="hidden" id="break_roll" name="break_roll" >
							<input type="hidden" id="break_order_id" name="break_order_id" >
							<input type="hidden" id="tot_roll" name="tot_roll" >
						</td>
					</tr>
				</table>
			</form>
		</div>
	</body>
	<?
}
*/

/*
if($action=="return_po_popup_22_03_2020")
{
	echo load_html_head_contents("Receive Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$txt_received_id=str_replace("'","",$txt_received_id);
	$txt_prod_id=str_replace("'","",$txt_prod_id);
	$hidden_receive_trans_id=str_replace("'","",$hidden_receive_trans_id);
	$update_id=str_replace("'","",$update_id);

	$variable_setting_production=return_field_value("fabric_roll_level","variable_settings_production","company_name='$cbo_company_id' and item_category_id=2 and variable_list=3 and status_active=1","fabric_roll_level");

	if($variable_setting_production==1)
	{
		$table_width=600;
		$txt_break_roll=explode("_",$txt_break_roll);
		foreach($txt_break_roll as $val)
		{
			$txt_break_roll_data=explode("**",$val);
			$po_id=$txt_break_roll_data[0];
			$roll_no=$txt_break_roll_data[1];
			$qty=$txt_break_roll_data[2];
			$roll_id=$txt_break_roll_data[3];

			$order_wise_qnty_arr[$po_id][$roll_id]=$qty;
		}
		$disabled="disabled='disabled'";
	}
	else
	{
		$table_width=500;
		$txt_break_qnty=explode("_",$txt_break_qnty);
		foreach($txt_break_qnty as $val)
		{
			$txt_break_qnty_data=explode("**",$val);
			$po_id=$txt_break_qnty_data[0];
			$qty=$txt_break_qnty_data[1];

			$order_wise_qnty_arr[$po_id]=$qty;
		}
		$disabled='';
	}
	?>
	<script>
		function distribute_qnty(str)
		{
			if(str==1)
			{
				var total_balance_quantity=$('#total_balance_quantity').val()*1;
				var txt_prop_finish_qnty=$('#txt_prop_finish_qnty').val()*1;

				if(txt_prop_finish_qnty>total_balance_quantity)
				{
					alert("Return Qnty not available");
					$('#txt_prop_finish_qnty').val("");
					return;
				}
				var len=totalFinish=0;
				$("#pop_table tbody").find('tr').each(function()
				{
					len=len+1;
					var row_balance = $("#issueqnty_"+len).attr("placeholder")*1;
					var perc=(row_balance/total_balance_quantity)*100;
					var return_qnty=(perc*txt_prop_finish_qnty)/100;
					return_qnty = return_qnty.toFixed(2);
					$("#issueqnty_"+len).val(return_qnty);

				});
			}  
			else
			{
				$('#txt_prop_finish_qnty').val('');
				$("#pop_table tbody").find('tr').each(function()
				{ 
					$(this).find('input[name="issueqnty[]"]').val('');
				});
			}
		}

		function js_set_value()
		{
			var table_legth=$('#pop_table tbody tr').length;
			var break_qnty=break_roll=break_id="";
			var tot_qnty=0; var tot_roll='';
			for(var i=1; i<=table_legth; i++)
			{
				if($("#issueqnty_"+i).val()*1  > $("#recevqnty_"+i).val()*1)
				{
					alert("Return Quantity Can not be Greater Than Receive Quantity.");
					$("#issueqnty_"+i).val("");
					return;
				}

				tot_qnty +=($("#issueqnty_"+i).val()*1);
				if(break_qnty!="") break_qnty +="_";
				break_qnty+=($("#poId_"+i).val()*1)+'**'+($("#issueqnty_"+i).val()*1);
				if(break_roll!="") break_roll +="_";
				break_roll+=($("#poId_"+i).val()*1)+'**'+($("#roll_"+i).val()*1)+'**'+($("#issueqnty_"+i).val()*1)+'**'+($("#rollId_"+i).val()*1);
				if(break_id!="") break_id +=",";
				break_id+=($("#poId_"+i).val()*1);

				if($("#issueqnty_"+i).val()*1>0 && $("#rollId_"+i).val()*1>0)
				{
					tot_roll+=1;
				}
			}

			$("#tot_qnty").val(tot_qnty);
			$("#break_qnty").val(break_qnty);
			$("#break_roll").val(break_roll);
			$("#break_order_id").val(break_id);
			$("#tot_roll").val(tot_roll);
			$('#distribution_method').val( $('#cbo_distribiution_method').val());
			parent.emailwindow.hide();
		}

		function fn_calculate(id)
		{
			var recv_qnty=($("#recevqnty_"+id).val()*1);
			var cumu_qnty=($("#cumulativeIssue_"+id).val()*1);
			var issue_qnty=($("#issueqnty_"+id).val()*1);
			var hiddenissue_qnty=($("#hiddenissueqnty_"+id).val()*1);
			if(((cumu_qnty*1)+(issue_qnty*1))>((recv_qnty*1)+(hiddenissue_qnty*1)))
			{
				alert("Return Quantity Can not be Greater Than Receive Quantity.");
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
					<td><input type="text" name="txt_prop_finish_qnty" id="txt_prop_finish_qnty" class="text_boxes_numeric" value="<? if($prev_distribution_method==1) echo $txt_return_qnty; ?>" style="width:120px"  onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)" <? echo $disabled; ?>/></td>

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
						<th width="140">Booking No</th>
						<th width="100">Receive Balance</th>
						<?
						if($variable_setting_production==1)
						{
							?>
							<th>Roll</th>
							<?
						}
						?>
						<th width="120">Return Quantity</th>
						<th width="80">Return Balance</th>
					</tr>
				</thead>
				<tbody>
					<?
					
					$cumu_iss_arr=array();
					if($variable_setting_production==1)
					{
						$cumu_iss_data_arr = sql_select("select d.po_breakdown_id,b.issue_trans_id, d.roll_id, d.qnty from inv_mrr_wise_issue_details b, order_wise_pro_details c, pro_roll_details d where b.issue_trans_id=c.trans_id and c.trans_id=d.dtls_id and c.status_active=1 and b.recv_trans_id='$hidden_receive_trans_id' and c.entry_form in (18) and d.entry_form in (18) group by d.po_breakdown_id, d.roll_id, d.qnty");
						foreach($cumu_iss_data_arr as $rowR)
						{
							$cumu_iss_arr[$rowR[csf('po_breakdown_id')]][$rowR[csf('roll_id')]]+=$rowR[csf('qnty')];
						}
					}
					else
					{

						$issue_sql = " select c.po_breakdown_id,b.issue_trans_id,c.prod_id,d.mst_id, (c.quantity) as cumu_qnty 
						from inv_mrr_wise_issue_details b, order_wise_pro_details c,inv_transaction d
						where b.issue_trans_id=c.trans_id and c.trans_id=d.id and c.status_active=1 and b.recv_trans_id='$hidden_receive_trans_id' and c.entry_form in (18,46) and d.status_active=1 and d.is_deleted=0
						group by c.po_breakdown_id,b.issue_trans_id,c.prod_id,d.mst_id,c.quantity";
						$issue_data = sql_select($issue_sql);
						foreach ($issue_data as $issue_row) {
							$cumu_iss_arr[$issue_row[csf("po_breakdown_id")]] += $issue_row[csf("cumu_qnty")];
							$issue_id_arr[$issue_row[csf("mst_id")]] = $issue_row[csf("mst_id")];
							$prod_id_arr[$issue_row[csf("prod_id")]] = $issue_row[csf("prod_id")];
						}
						
						if(!empty($issue_id_arr)){
							$issue_return_sql = "select c.po_breakdown_id, sum(c.quantity) return_qnty from inv_receive_master a,inv_transaction b,order_wise_pro_details c where a.status_active=1 and b.is_deleted=0 and a.id=b.mst_id and a.issue_id in(".implode(",",$issue_id_arr).") and b.status_active=1 and b.is_deleted=0 and b.prod_id in(".implode(",",$prod_id_arr).") and b.id=c.trans_id and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id";
							$issue_return_data = sql_select($issue_return_sql);
							foreach ($issue_return_data as $return_row) {
								$cumu_return_arr[$return_row[csf("po_breakdown_id")]] = $return_row[csf("return_qnty")];
							}
						}
					}

					if($variable_setting_production==1)
					{
						$sql="select a.is_sales,c.id as roll_id, c.po_breakdown_id, c.roll_no, c.qnty as receive_qnty from inv_transaction b, order_wise_pro_details a, pro_roll_details c where b.id=a.trans_id and a.dtls_id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_id' and b.mst_id='$txt_received_id' and b.id='$hidden_receive_trans_id' and b.prod_id='$txt_prod_id' and a.entry_form in(7,37) and c.entry_form in(7,37) and a.trans_type in(1,4) and b.transaction_type in(1,4) group by a.is_sales,c.id, c.po_breakdown_id, c.roll_no, c.qnty";
					}
					else
					{
						$sql="select a.po_breakdown_id,a.is_sales, sum(a.quantity) as receive_qnty from  order_wise_pro_details a, inv_transaction b where a.trans_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_id' and b.mst_id='$txt_received_id' and b.prod_id='$txt_prod_id' and a.entry_form in(7,37) and b.id='$hidden_receive_trans_id' and b.transaction_type in(1,4) and a.trans_type in(1,4) group by a.po_breakdown_id,a.is_sales";
					}

					$sql_result=sql_select($sql);
					$po_arr=$sales_arr=array();
					foreach($sql_result as $row)
					{
						$po_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
					}

					if(!empty($po_arr)){
						$po_no_arr = return_library_array("select id,po_number from wo_po_break_down where id in(".implode(",",$po_arr).")","id","po_number");
					}

					$i=1;
					foreach($sql_result as $row)
					{
						if($variable_setting_production==1)
						{
							$cumilitive_issue = $cumu_iss_arr[$row[csf('po_breakdown_id')]][$row[csf('roll_id')]];
							$receive = ($row[csf("receive_qnty")]-$cumilitive_issue) + $order_wise_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf('roll_id')]];
						}
						else
						{
							//echo $cumu_iss_arr[$row[csf('po_breakdown_id')]] ."-" .$cumu_return_arr[$row[csf("po_breakdown_id")]];
							$cumilitive_issue=$cumu_iss_arr[$row[csf('po_breakdown_id')]] - $cumu_return_arr[$row[csf("po_breakdown_id")]];
							$receive = ($row[csf("receive_qnty")]-$cumilitive_issue) + $order_wise_qnty_arr[$row[csf("po_breakdown_id")]];
						}

						$total_cumu_issue += $cumilitive_issue;
						?>
						<tr>
							<td align="center"><input type="text" id="poNo_<? echo $i; ?>" name="poNo_<? echo $i; ?>" class="text_boxes" style="width:140px" value="<? echo $po_no_arr[$row[csf("po_breakdown_id")]];  ?>"  readonly disabled >
								<input type="hidden" id="poId_<? echo $i; ?>" name="poId_<? echo $i; ?>" class="text_boxes" style="width:140px" value="<? echo $row[csf("po_breakdown_id")];  ?>"  readonly disabled >
							</td>
							<td align="center">
								<input type="text" id="bookingno_<? echo $i; ?>" name="bookingno_<? echo $i; ?>" class="text_boxes" style="width:140px" value="<? echo $txt_booking_no;  ?>"  readonly disabled >
							</td>
							<td align="center"> <input type="text" id="recevqnty_<? echo $i; ?>" name="recevqnty[]" class="text_boxes_numeric" style="width:100px" value="<? echo number_format($receive,2);  ?>" readonly disabled ></td>
							<?
							$total_rcv_qnty += $row[csf("receive_qnty")];
							if($variable_setting_production==1)
							{
								?>
								<td align="center">
									<input type="text" id="roll_<? echo $i; ?>" name="roll_<? echo $i; ?>" value="<? echo $row[csf("roll_no")]; ?>" class="text_boxes_numeric" style="width:80px" readonly disabled >
									<input type="hidden" id="rollId_<? echo $i; ?>" name="rollId_<? echo $i; ?>" value="<? echo $row[csf("roll_id")]; ?>">
								</td>
								<td align="center">
									<input type="text" id="issueqnty_<? echo $i; ?>" name="issueqnty[]" onKeyUp="fn_calculate(<? echo $i; ?>);" class="text_boxes_numeric" placeholder="<? echo $row[csf("receive_qnty")]-$cumu_iss_arr[$row[csf('po_breakdown_id')]][$row[csf('roll_id')]];?>" value="<? echo $order_wise_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("roll_id")]]; ?>" style="width:110px" >
									<input type="hidden" id="hiddenissueqnty_<? echo $i; ?>" name="hiddenissueqnty_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $order_wise_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("roll_id")]]; ?>">
								</td>
								<?
								$balance_quantity += $receive-$order_wise_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("roll_id")]];
							}
							else
							{
								$balance_quantity += $receive-$order_wise_qnty_arr[$row[csf("po_breakdown_id")]];
								?>
								<td align="center" style="display:none;">
									<input type="text" id="roll_<? echo $i; ?>" name="roll_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" >
									<input type="hidden" id="rollId_<? echo $i; ?>" name="rollId_<? echo $i; ?>" value="">
								</td>
								<td align="center">
									<input type="text" id="issueqnty_<? echo $i; ?>" name="issueqnty[]" onKeyUp="fn_calculate(<? echo $i; ?>);" class="text_boxes_numeric" placeholder="<? echo number_format(($receive-$order_wise_qnty_arr[$row[csf("po_breakdown_id")]]),2,'.','');?>" value="<? echo $order_wise_qnty_arr[$row[csf("po_breakdown_id")]]; ?>" style="width:100px" >
									
									<input type="hidden" id="hiddenissueqnty_<? echo $i; ?>" name="hiddenissueqnty_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo number_format($order_wise_qnty_arr[$row[csf("po_breakdown_id")]],2,'.',''); ?>">
								</td>
								<?
								
							}
							?>
							<td align="center">
								<input type="text" id="txt_balance_<? echo $i; ?>" name="txt_balance_[]" value="<? echo number_format($receive-$order_wise_qnty_arr[$row[csf("po_breakdown_id")]],2); ?>" class="text_boxes_numeric" style="width:70px" readonly disabled >
							</td>
						</tr>
						<?
						$i++;
					}
					?>
				</tbody>
			</table>
			<table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0" border="0" rules="all" align="center">
				<tr>
					<td align="center">
						<input type="button" id="btn_close" name="" value="Close" onClick="js_set_value();" style="width:150px;" class="formbutton" >
						<input type="hidden" id="tot_qnty" name="tot_qnty" >
						<input type="hidden" id="break_qnty" name="break_qnty" >
						<input type="hidden" id="break_roll" name="break_roll" >
						<input type="hidden" id="break_order_id" name="break_order_id" >
						<input type="hidden" id="tot_roll" name="tot_roll" >
						<input type="hidden" id="total_balance_quantity" name="total_balance_quantity" value="<? echo $balance_quantity;?>">
						<input type="hidden" id="tot_qnty" name="tot_qnty" >
						<input type="hidden" name="distribution_method" id="distribution_method" class="text_boxes" value="">
					</td>
				</tr>
			</table>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<?
}
*/

if($action=="return_po_popup")
{
	echo load_html_head_contents("Receive Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$txt_received_id=str_replace("'","",$txt_received_id);
	$txt_prod_id=str_replace("'","",$txt_prod_id);
	$hidden_receive_trans_id=str_replace("'","",$hidden_receive_trans_id);
	$update_id=str_replace("'","",$update_id);

	$txt_dia_width_type=str_replace("'","",$txt_dia_width_type);
	$cbo_body_part=str_replace("'","",$cbo_body_part);
	$txt_fabric_shade=str_replace("'","",$txt_fabric_shade);
	$txt_gsm=str_replace("'","",$txt_gsm);
	$txt_dia=str_replace("'","",$txt_dia);
	$hidden_batch_id=str_replace("'","",$hidden_batch_id);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	$cbo_floor=str_replace("'","",$cbo_floor);
	$cbo_room=str_replace("'","",$cbo_room);
	$txt_rack=str_replace("'","",$txt_rack);
	$txt_shelf=str_replace("'","",$txt_shelf);
	$txt_bin=str_replace("'","",$txt_bin);

	$variable_setting_production=return_field_value("fabric_roll_level","variable_settings_production","company_name='$cbo_company_id' and item_category_id=2 and variable_list=3 and status_active=1","fabric_roll_level");

	if($variable_setting_production==1)
	{
		$table_width=700;
		$txt_break_roll=explode("_",$txt_break_roll);
		foreach($txt_break_roll as $val)
		{
			$txt_break_roll_data=explode("**",$val);
			$po_id=$txt_break_roll_data[0];
			$roll_no=$txt_break_roll_data[1];
			$qty=$txt_break_roll_data[2];
			$roll_id=$txt_break_roll_data[3];

			$order_wise_qnty_arr[$po_id][$roll_id]=$qty;
		}
		$disabled="disabled='disabled'";
	}
	else
	{
		$table_width=600;
		$txt_break_qnty=explode("_",$txt_break_qnty);
		foreach($txt_break_qnty as $val)
		{
			$txt_break_qnty_data=explode("**",$val);
			$po_id=$txt_break_qnty_data[0];
			$qty=$txt_break_qnty_data[1];

			$order_wise_qnty_arr[$po_id]=$qty;
		}
		$disabled='';
	}
	?>
	<script>
		function distribute_qnty(str)
		{
			if(str==1)
			{
				var total_balance_quantity=$('#total_balance_quantity').val()*1;
				var txt_prop_finish_qnty=$('#txt_prop_finish_qnty').val()*1;

				if(txt_prop_finish_qnty>total_balance_quantity)
				{
					alert("Return Qnty not available");
					$('#txt_prop_finish_qnty').val("");
					return;
				}
				var len=totalFinish=0;
				$("#pop_table tbody").find('tr').each(function()
				{
					len=len+1;
					var row_balance = $("#issueqnty_"+len).attr("placeholder")*1;
					var perc=(row_balance/total_balance_quantity)*100;
					var return_qnty=(perc*txt_prop_finish_qnty)/100;
					return_qnty = return_qnty.toFixed(2);
					$("#issueqnty_"+len).val(return_qnty);
				});
			}  
			else
			{
				$('#txt_prop_finish_qnty').val('');
				$("#pop_table tbody").find('tr').each(function()
				{ 
					$(this).find('input[name="issueqnty[]"]').val('');
				});
			}
		}

		function js_set_value()
		{
			var table_legth=$('#pop_table tbody tr').length;
			var break_qnty=break_roll=break_id="";
			var tot_qnty=0; var tot_roll='';
			for(var i=1; i<=table_legth; i++)
			{
				if($("#issueqnty_"+i).val()*1  > $("#recevqnty_"+i).val()*1)
				{
					alert("Return Quantity Can not be Greater Than Receive Quantity.");
					$("#issueqnty_"+i).val("");
					return;
				}

				tot_qnty +=($("#issueqnty_"+i).val()*1);
				if(break_qnty!="") break_qnty +="_";
				break_qnty+=($("#poId_"+i).val()*1)+'**'+($("#issueqnty_"+i).val()*1);
				if(break_roll!="") break_roll +="_";
				break_roll+=($("#poId_"+i).val()*1)+'**'+($("#roll_"+i).val()*1)+'**'+($("#issueqnty_"+i).val()*1)+'**'+($("#rollId_"+i).val()*1);
				if(break_id!="") break_id +=",";
				break_id+=($("#poId_"+i).val()*1);

				if($("#issueqnty_"+i).val()*1>0 && $("#rollId_"+i).val()*1>0)
				{
					tot_roll+=1;
				}
			}

			$("#tot_qnty").val(tot_qnty);
			$("#break_qnty").val(break_qnty);
			$("#break_roll").val(break_roll);
			$("#break_order_id").val(break_id);
			$("#tot_roll").val(tot_roll);
			$('#distribution_method').val( $('#cbo_distribiution_method').val());
			parent.emailwindow.hide();
		}

		function fn_calculate(id)
		{
			var recv_qnty=($("#recevqnty_"+id).val()*1);
			var cumu_qnty=($("#cumulativeIssue_"+id).val()*1);
			var issue_qnty=($("#issueqnty_"+id).val()*1);
			var hiddenissue_qnty=($("#hiddenissueqnty_"+id).val()*1);
			var txt_balance=($("#txt_balance_"+id).val()*1);

			/*if(((cumu_qnty*1)+(issue_qnty*1))>((recv_qnty*1)+(hiddenissue_qnty*1)))
			{
				alert("Return Quantity Can not be Greater Than Receive Quantity.");
				$("#issueqnty_"+id).val(0);
			}*/

			if(issue_qnty > txt_balance)
			{
				alert("Return Quantity Can not be Greater Than Receive Quantity.");
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
					<td><input type="text" name="txt_prop_finish_qnty" id="txt_prop_finish_qnty" class="text_boxes_numeric" value="<? if($prev_distribution_method==1) echo $txt_return_qnty; ?>" style="width:120px"  onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)" <? echo $disabled; ?>/></td>

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
						<th width="100">Internal Ref.</th>
						<th width="140">Booking No</th>
						<th width="100">Receive Balance</th>
						<?
						if($variable_setting_production==1)
						{
							?>
							<th>Roll</th>
							<?
						}
						?>
						<th width="120">Return Quantity</th>
						<th width="80">Return Balance</th>
					</tr>
				</thead>
				<tbody>
					<?
					
					$cumu_iss_arr=array();
					if($variable_setting_production==1)
					{
						$cumu_iss_data_arr = sql_select("select d.po_breakdown_id,b.issue_trans_id, d.roll_id, d.qnty from inv_mrr_wise_issue_details b, order_wise_pro_details c, pro_roll_details d where b.issue_trans_id=c.trans_id and c.trans_id=d.dtls_id and c.status_active=1 and b.recv_trans_id='$hidden_receive_trans_id' and c.entry_form in (18) and d.entry_form in (18) group by d.po_breakdown_id, d.roll_id, d.qnty");
						foreach($cumu_iss_data_arr as $rowR)
						{
							$cumu_iss_arr[$rowR[csf('po_breakdown_id')]][$rowR[csf('roll_id')]]+=$rowR[csf('qnty')];
						}
					}
					else
					{
						
						$return_sql = "select c.po_breakdown_id, d.id as trans_id, sum(c.quantity) as return_qnty from inv_issue_master a, order_wise_pro_details c, inv_transaction d where a.id = d.mst_id and c.trans_id=d.id and c.status_active=1 and c.entry_form in (46) and d.status_active=1 and d.is_deleted=0 and a.company_id=$cbo_company_id and d.store_id=$cbo_store_name and d.pi_wo_batch_no =$batch_id and a.received_id=$txt_received_id and d.prod_id=$txt_prod_id and d.body_part_id='$cbo_body_part' and d.fabric_shade='$txt_fabric_shade' and d.floor_id='$cbo_floor' and d.room='$cbo_room' and d.rack='$txt_rack' and d.self = '$txt_shelf' group by c.po_breakdown_id, d.id";
						//echo "$return_sql<br>";
						$return_data = sql_select($return_sql);
						foreach ($return_data as $row) 
						{
							if($row[csf('trans_id')] == $update_id)
							{
								$this_trans_return_arr[$row[csf("po_breakdown_id")]]+= $row[csf("return_qnty")];
							}
							else
							{
								//Here return quanity shows without this transaction quantity
								$cumu_return_arr[$row[csf("po_breakdown_id")]]+= $row[csf("return_qnty")];
							}
						}
					}

					if($variable_setting_production==1)
					{
						$sql="select a.is_sales,c.id as roll_id, c.po_breakdown_id, c.roll_no, c.qnty as receive_qnty,c.grouping as internal_ref from inv_transaction b, order_wise_pro_details a, pro_roll_details c, wo_po_break_down d where b.id=a.trans_id and a.dtls_id=c.dtls_id and c.po_breakdown_id=d.id and d.id=a.po_breakdown_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_id' and b.mst_id='$txt_received_id' and b.id='$hidden_receive_trans_id' and b.prod_id='$txt_prod_id' and a.entry_form in(7,37) and c.entry_form in(7,37) and a.trans_type in(1,4) and b.transaction_type in(1,4) group by a.is_sales,c.id, c.po_breakdown_id, c.roll_no, c.qnty,d.grouping";
					}
					else
					{
						$sql="select a.po_breakdown_id,a.is_sales, sum(a.quantity) as receive_qnty,c.grouping as internal_ref from wo_po_break_down c,order_wise_pro_details a, inv_transaction b where c.id=a.po_breakdown_id and a.trans_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_id' and b.mst_id='$txt_received_id' and b.prod_id='$txt_prod_id' and b.pi_wo_batch_no=$batch_id and a.entry_form in(7,37) and b.body_part_id=$cbo_body_part and b.fabric_shade='$txt_fabric_shade' and b.floor_id='$cbo_floor' and b.room='$cbo_room' and b.rack='$txt_rack' and b.self ='$txt_shelf' and b.bin_box ='$txt_bin' and b.transaction_type in(1) and a.trans_type in(1) and (a.is_sales=0 or a.is_sales is null) group by a.po_breakdown_id,a.is_sales,c.grouping";

					}
					//echo $sql;
					$sql_result=sql_select($sql);
					$po_arr=$sales_arr=array();
					foreach($sql_result as $row)
					{
						$po_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
					}

					if(!empty($po_arr))
					{
						$po_no_arr = return_library_array("select id,po_number from wo_po_break_down where id in(".implode(",",$po_arr).")","id","po_number");


						$sql_cuml="select b.po_breakdown_id,
						sum(case when b.entry_form in(7,37) and b.trans_type=1 and a.transaction_type=1 and a.pi_wo_batch_no=$batch_id then b.quantity end) as finish_fabric_recv,
						sum(case when b.entry_form=18 and b.trans_type=2 and a.transaction_type=2 and a.pi_wo_batch_no=$batch_id then b.quantity end) as finish_fabric_issue,
						sum(CASE WHEN b.entry_form=46 and b.trans_type=3 and a.transaction_type=3 and a.batch_id_from_fissuertn=$batch_id THEN b.quantity ELSE 0 END) AS recv_rtn_qnty,
						sum(CASE WHEN b.entry_form=52 and b.trans_type=4 and a.transaction_type=4 and a.batch_id_from_fissuertn=$batch_id THEN b.quantity ELSE 0 END) AS iss_retn_qnty,
						sum(case when b.entry_form in(14,15,306) and b.trans_type=5 and a.transaction_type=5 and a.pi_wo_batch_no=$batch_id then b.quantity end) as finish_fabric_trans_recv,
						sum(case when b.entry_form in(14,15,306) and b.trans_type=6 and a.transaction_type=6 and a.pi_wo_batch_no=$batch_id then b.quantity end) as finish_fabric_trans_issued
						from inv_transaction a, order_wise_pro_details b
						where a.id=b.trans_id and b.po_breakdown_id in(".implode(',',$po_arr). ") and a.prod_id=$txt_prod_id and b.prod_id=$txt_prod_id and a.floor_id='$cbo_floor' and a.room='$cbo_room' and a.rack='$txt_rack' and a.self = '$txt_shelf' and a.bin_box = '$txt_bin' and a.fabric_shade='$txt_fabric_shade' and a.store_id=$cbo_store_name and a.body_part_id='$cbo_body_part' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.po_breakdown_id";

						$sql_result_cuml=sql_select($sql_cuml);
						foreach($sql_result_cuml as $row)
						{
							$cumu_rec_qty[$row[csf('po_breakdown_id')]]=($row[csf('finish_fabric_recv')]+$row[csf('finish_fabric_trans_recv')])-$row[csf('recv_rtn_qnty')];
							$cumu_iss_qty[$row[csf('po_breakdown_id')]]=($row[csf('finish_fabric_issue')]+$row[csf('finish_fabric_trans_issued')])-$row[csf('iss_retn_qnty')];
						}
					}

					$i=1;
					foreach($sql_result as $row)
					{
						if($variable_setting_production==1)
						{
							$cumilitive_issue = $cumu_iss_arr[$row[csf('po_breakdown_id')]][$row[csf('roll_id')]];
							$receive = ($row[csf("receive_qnty")]-$cumilitive_issue) + $order_wise_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf('roll_id')]];
						}
						else
						{
							$receive = $row[csf("receive_qnty")]-$cumu_return_arr[$row[csf("po_breakdown_id")]];
						}

						$total_cumu_issue += $cumilitive_issue;

						//
						$cumul_balance=$cumu_rec_qty[$row[csf('po_breakdown_id')]]-$cumu_iss_qty[$row[csf('po_breakdown_id')]] +$this_trans_return_arr[$row[csf('po_breakdown_id')]];

						//
						if($receive > $cumul_balance){
							$title = "Cumulative Balance = ".$cumul_balance;
							$show_return_balance = $cumul_balance;
						}
						else
						{
							$title = "Receive Balance = ".$receive;
							$show_return_balance = $receive;
						}

						?>
						<tr>
							<td align="center"><input type="text" id="poNo_<? echo $i; ?>" name="poNo_<? echo $i; ?>" class="text_boxes" style="width:140px" value="<? echo $po_no_arr[$row[csf("po_breakdown_id")]];  ?>"  readonly disabled >
								<input type="hidden" id="poId_<? echo $i; ?>" name="poId_<? echo $i; ?>" class="text_boxes" style="width:140px" value="<? echo $row[csf("po_breakdown_id")];  ?>"  readonly disabled >
							</td>
							<td align="center" >
								<input type="text" class="text_boxes" style="width:100px" value="<? echo $row[csf("internal_ref")];  ?>"  readonly disabled >
							</td>
							<td align="center">
								<input type="text" id="bookingno_<? echo $i; ?>" name="bookingno_<? echo $i; ?>" class="text_boxes" style="width:140px" value="<? echo $txt_booking_no;  ?>"  readonly disabled >
							</td>
							<td align="center"> <input type="text" id="recevqnty_<? echo $i; ?>" name="recevqnty[]" class="text_boxes_numeric" style="width:100px" value="<? echo number_format($receive,2);  ?>" readonly disabled ></td>
							<?
							$total_rcv_qnty += $row[csf("receive_qnty")];
							if($variable_setting_production==1)
							{
								?>
								<td align="center">
									<input type="text" id="roll_<? echo $i; ?>" name="roll_<? echo $i; ?>" value="<? echo $row[csf("roll_no")]; ?>" class="text_boxes_numeric" style="width:80px" readonly disabled >
									<input type="hidden" id="rollId_<? echo $i; ?>" name="rollId_<? echo $i; ?>" value="<? echo $row[csf("roll_id")]; ?>">
								</td>
								<td align="center">
									<input type="text" id="issueqnty_<? echo $i; ?>" name="issueqnty[]" onKeyUp="fn_calculate(<? echo $i; ?>);" class="text_boxes_numeric" placeholder="<? echo $row[csf("receive_qnty")]-$cumu_iss_arr[$row[csf('po_breakdown_id')]][$row[csf('roll_id')]];?>" value="<? echo $order_wise_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("roll_id")]]; ?>" style="width:110px" >
									<input type="hidden" id="hiddenissueqnty_<? echo $i; ?>" name="hiddenissueqnty_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $order_wise_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("roll_id")]]; ?>">
								</td>
								<?
								$balance_quantity += $receive-$order_wise_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("roll_id")]];
							}
							else
							{
								$balance_quantity += $show_return_balance;//$receive-$order_wise_qnty_arr[$row[csf("po_breakdown_id")]];
								?>
								<td align="center" style="display:none;">
									<input type="text" id="roll_<? echo $i; ?>" name="roll_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" >
									<input type="hidden" id="rollId_<? echo $i; ?>" name="rollId_<? echo $i; ?>" value="">
								</td>
								<td align="center">
									<input type="text" id="issueqnty_<? echo $i; ?>" name="issueqnty[]" onKeyUp="fn_calculate(<? echo $i; ?>);" class="text_boxes_numeric" placeholder="<? echo $show_return_balance;//number_format(($receive-$order_wise_qnty_arr[$row[csf("po_breakdown_id")]]),2,'.','');?>" value="<? echo $order_wise_qnty_arr[$row[csf("po_breakdown_id")]]; ?>" style="width:100px" >
									
									<input type="hidden" id="hiddenissueqnty_<? echo $i; ?>" name="hiddenissueqnty_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo number_format($order_wise_qnty_arr[$row[csf("po_breakdown_id")]],2,'.',''); ?>">
								</td>
								<?	
							}
							?>
							<td align="center">
								<input type="text" id="txt_balance_<? echo $i; ?>" name="txt_balance_[]" value="<? echo $show_return_balance;//number_format($receive-$order_wise_qnty_arr[$row[csf("po_breakdown_id")]],2); ?>" title="<? echo $title ;?>" class="text_boxes_numeric" style="width:70px" readonly disabled >
							</td>
						</tr>
						<?
						$i++;
					}
					?>
				</tbody>
			</table>
			<table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0" border="0" rules="all" align="center">
				<tr>
					<td align="center">
						<input type="button" id="btn_close" name="" value="Close" onClick="js_set_value();" style="width:150px;" class="formbutton" >
						<input type="hidden" id="tot_qnty" name="tot_qnty" >
						<input type="hidden" id="break_qnty" name="break_qnty" >
						<input type="hidden" id="break_roll" name="break_roll" >
						<input type="hidden" id="break_order_id" name="break_order_id" >
						<input type="hidden" id="tot_roll" name="tot_roll" >
						<input type="hidden" id="total_balance_quantity" name="total_balance_quantity" value="<? echo $balance_quantity;?>">
						<input type="hidden" id="tot_qnty" name="tot_qnty" >
						<input type="hidden" name="distribution_method" id="distribution_method" class="text_boxes" value="">
					</td>
				</tr>
			</table>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<?
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));


	$txt_fabric_shade 	= str_replace("'", "", $txt_fabric_shade);
	$txt_rack 			= str_replace("'", "", $txt_rack);
	$txt_shelf 			= str_replace("'", "", $txt_shelf);
	$txt_bin 			= str_replace("'", "", $txt_bin);
	$cbo_room 			= str_replace("'", "", $cbo_room);
	$cbo_floor 			= str_replace("'", "", $cbo_floor);

	if($txt_fabric_shade==""){$txt_fabric_shade=0;}
	if($txt_rack==""){$txt_rack=0;}
	if($txt_shelf==""){$txt_shelf=0;}
	if($txt_bin==""){$txt_bin=0;}
	if($txt_room==""){$txt_room=0;}
	if($cbo_floor==""){$cbo_floor=0;}

	$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$txt_prod_id and store_id=$cbo_store_name and status_active=1 and is_deleted=0 and transaction_type in (1,4,5)", "max_date");
	if($max_recv_date !="")
	{
		$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
		$return_date = date("Y-m-d", strtotime(str_replace("'","",$txt_return_date)));
		if ($return_date < $max_recv_date)
		{
			echo "20**Return Date Can not Be Less Than Last Receive Date.\nReceive Date = $max_recv_date";
			die;
		}
	}
	$variable_setting_production=return_field_value("fabric_roll_level","variable_settings_production","company_name=$cbo_company_id  and item_category_id=2 and variable_list=3 and status_active=1","fabric_roll_level");

	//================================Balance Check Start======================================
	if ($operation==0 || $operation==1) 
	{
		if($operation ==1)
		{
			$up_cond = " and b.id <>$update_details_id";
			$up_trans_cond = " and a.id<>$update_id";
			$up_trans_cond2 = " and d.id<>$update_id";
		}else{
			$up_cond="";
			$up_trans_cond="";
			$up_trans_cond2="";
		}
		
		$return_sql = "select a.received_id, d.pi_wo_batch_no, d.prod_id, d.cons_uom, d.body_part_id, d.fabric_shade, d.floor_id, d.room, d.rack, d.self,d.bin_box, sum(d.cons_quantity) as cumu_qnty from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction d where a.id = b.mst_id and b.trans_id=d.id and a.id = d.mst_id and a.entry_form in (46) and d.transaction_type=3 and d.item_category=2 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_id and d.store_id=$cbo_store_name and d.prod_id=$txt_prod_id and d.pi_wo_batch_no=$hidden_batch_id and d.body_part_id=$cbo_body_part and d.fabric_shade=$txt_fabric_shade and d.floor_id=$cbo_floor and d.room=$cbo_room and d.rack=$txt_rack and d.self=$txt_shelf and d.bin_box=$txt_bin $up_cond
		group by a.received_id,d.pi_wo_batch_no, d.prod_id, d.cons_uom, d.floor_id,d.room,d.rack,d.self,d.bin_box, d.fabric_shade, d.body_part_id";
	 	//echo  "10**$return_sql";die;
		$return_data = sql_select($return_sql);  
		foreach ($return_data as $row) 
		{
			if($row[csf("received_id")] == str_replace("'", "", $txt_received_id))
			{
				$this_challan_return_arr[$row[csf("prod_id")]][$row[csf("pi_wo_batch_no")]][$row[csf("fabric_shade")]][$row[csf("body_part_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]] += $row[csf("cumu_qnty")];
			}

			$return_arr[$row[csf("prod_id")]][$row[csf("pi_wo_batch_no")]][$row[csf("fabric_shade")]][$row[csf("body_part_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]] += $row[csf("cumu_qnty")];
			
		}
		unset($return_data);


		if($db_type==0)
		{
			$rackCond = " IFNULL(a.rack, 0) rack";
			$rackCond2 = " IFNULL(b.rack, 0) rack_no";
			$rackCond3 = " (case when b.rack_no is null or b.rack_no=0 then 0 else b.rack_no end) rack_no";
			$rackCond4 = " (case when b.to_rack is null or b.to_rack=0 then 0 else b.to_rack end) rack_no";
		}else{
			$rackCond = " nvl(a.rack, 0) rack";
			$rackCond2 = " nvl(b.rack, 0) rack_no";
			$rackCond3 = " cast(b.rack_no as varchar(4000)) as rack_no";
			$rackCond4 = " cast(b.to_rack as varchar(4000)) as rack_no";
		}
		$issData = sql_select("SELECT a.prod_id, a.pi_wo_batch_no as batch_id,c.fabric_shade,c.body_part_id,(case when a.floor_id is null or a.floor_id=0 then 0 else a.floor_id end) floor_id,$rackCond,(case when a.room is null or a.room=0 then 0 else a.room end) room,(case when a.self is null or a.self=0 then 0 else a.self end) self,(case when a.bin_box is null or a.to_bin_box=0 then 0 else a.bin_box end) bin_box, sum(case when a.transaction_type=2 then cons_quantity end) as issue_qnty 
		from inv_issue_master b,inv_transaction a,inv_finish_fabric_issue_dtls c, pro_batch_create_mst d 
		where b.entry_form=18 and b.id=a.mst_id and a.id=c.trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=2 and a.transaction_type=2 and c.status_active=1 and c.is_deleted=0 and a.pi_wo_batch_no = d.id and b.status_active=1 and b.is_deleted=0 and b.company_id=$cbo_company_id and a.store_id =$cbo_store_name and a.prod_id=$txt_prod_id and a.pi_wo_batch_no=$hidden_batch_id and c.body_part_id=$cbo_body_part and c.fabric_shade=$txt_fabric_shade
		group by a.prod_id, a.pi_wo_batch_no,c.fabric_shade, c.body_part_id,a.floor_id, a.room, a.rack, a.self,a.bin_box");
			//and a.floor_id=$cbo_floor and a.room=$cbo_room and a.rack=$txt_rack and a.self=$txt_shelf
		foreach($issData as $row)
		{
			$issue_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]+=$row[csf('issue_qnty')];
		}

		$issueReturnData=sql_select("select a.prod_id, a.batch_id_from_fissuertn as batch_id, b.fabric_shade, b.body_part_id, a.floor_id,a.room, a.rack, a.self,a.bin_box, sum(case when a.transaction_type=4 then a.cons_quantity end) as issrqnty from inv_transaction a,pro_finish_fabric_rcv_dtls b,pro_batch_create_mst c where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=2 and a.transaction_type in(4) and a.batch_id_from_fissuertn=c.id and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_id and a.store_id=$cbo_store_name and a.batch_id_from_fissuertn=$hidden_batch_id and a.prod_id=$txt_prod_id and b.fabric_shade=$txt_fabric_shade group by a.prod_id, b.fabric_shade, b.body_part_id, a.batch_id_from_fissuertn, a.floor_id, a.room, a.rack, a.self,a.bin_box");
		foreach($issueReturnData as $row)
		{
			$ir_floor_id = ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
			$ir_room = ($row[csf('room')]=="")?0:$row[csf('room')];
			$ir_rack = ($row[csf('rack')]=="")?0:$row[csf('rack')];
			$ir_self = ($row[csf('self')]=="")?0:$row[csf('self')];
			$ir_bin = ($row[csf('bin_box')]=="")?0:$row[csf('bin_box')];
			$issRt_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$ir_floor_id][$ir_room][$ir_rack][$ir_self][$ir_bin] +=$row[csf('issrqnty')];
		}

		$transOutData = sql_select("select b.batch_id, b.from_store, (case when b.floor_id is null or b.floor_id=0 then 0 else b.floor_id end) as floor, b.fabric_shade, c.body_part_id, (case when b.room is null or b.room=0 then 0 else b.room end) room, $rackCond2,(case when b.shelf is null or b.shelf=0 then 0 else b.shelf end) shelf_no,(case when b.bin_box is null or b.bin_box=0 then 0 else b.bin_box end) bin_box, sum(b.transfer_qnty) as trans_out_qnty, b.from_prod_id as prod_id
			from inv_transaction c,order_wise_pro_details d,inv_item_transfer_dtls b, pro_batch_create_mst a
			where c.id=d.trans_id and d.dtls_id=b.id and c.company_id =$cbo_company_id and b.from_store =$cbo_store_name and b.from_prod_id=$txt_prod_id and c.transaction_type=6 and c.item_category=2 and b.batch_id=a.id and c.status_active=1 and c.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and d.status_active=1 and d.is_deleted =0 and d.entry_form=14 and d.trans_type=6 and b.active_dtls_id_in_transfer=1 and b.batch_id =$hidden_batch_id and b.fabric_shade=$txt_fabric_shade
			group by b.batch_id, b.from_store, b.floor_id, b.fabric_shade, c.body_part_id, b.room,b.rack,b.shelf,b.bin_box,b.from_prod_id");

		foreach($transOutData as $row)
		{
			$trans_out_qnty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin_box')]]=$row[csf('trans_out_qnty')];
		}

		if($db_type ==0)
		{
			$sales_flag_cond = " and (b.is_sales=0 or b.is_sales ='' ) ";
		}
		else
		{
			$sales_flag_cond = " and (b.is_sales=0 or b.is_sales is null) ";
		}
		$data_sql="select x.floor, x.fabric_shade, x.body_part_id, x.batch_id,x.room,x.rack_no, x.shelf_no,x.bin_box, sum(x.qnty) as qnty, x.prod_id, x.sys_id, x.type
			from
			(
				select (case when b.floor is null or b.floor=0 then 0 else b.floor end) as  floor,b.fabric_shade, b.body_part_id, b.batch_id,(case when b.room is null or b.room=0 then 0 else b.room end) room,$rackCond3,(case when b.shelf_no is null or b.shelf_no=0 then 0 else b.shelf_no end) shelf_no,(case when b.bin is null or b.bin=0 then 0 else b.bin end) bin_box, sum(d.cons_quantity) as qnty, b.prod_id, c.id as sys_id, 1 as type
				from product_details_master a, pro_finish_fabric_rcv_dtls b,inv_transaction d, inv_receive_master c, pro_batch_create_mst e
				where a.id=b.prod_id and b.trans_id=d.id and d.mst_id=c.id and b.batch_id = e.id and b.batch_id =$hidden_batch_id and c.company_id=$cbo_company_id and d.store_id =$cbo_store_name and b.prod_id=$txt_prod_id and b.fabric_shade=$txt_fabric_shade and c.entry_form in (7,37) $sales_flag_cond and a.item_category_id=2 and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 
				group by b.floor, b.fabric_shade,b.body_part_id, b.batch_id, b.room, b.rack_no, b.shelf_no,b.bin, d.gmt_item_id, b.prod_id, c.id
				union all
				select (case when b.to_floor_id is null or b.to_floor_id=0 then 0 else b.to_floor_id end) as  floor, b.fabric_shade, c.body_part_id, b.to_batch_id as batch_id,(case when b.to_room is null or b.to_room=0 then 0 else b.to_room end) room, $rackCond4, (case when b.to_shelf is null or b.to_shelf=0 then 0 else b.to_shelf end) shelf_no, (case when b.to_bin_box is null or b.to_bin_box=0 then 0 else b.to_bin_box end) bin_box, sum(c.cons_quantity) as qnty, b.to_prod_id as prod_id, d.id as sys_id, 2 as type
				from product_details_master a, inv_item_transfer_dtls b, inv_transaction c, inv_item_transfer_mst d, pro_batch_create_mst e
				where a.id = b.to_prod_id and b.to_trans_id=c.id and b.mst_id=d.id and d.to_company = $cbo_company_id and c.store_id =$cbo_store_name and b.to_prod_id=$txt_prod_id and b.fabric_shade=$txt_fabric_shade and c.transaction_type=5 and c.item_category=2  and b.to_batch_id=e.id and b.to_batch_id =$hidden_batch_id and c.status_active =1 and c.is_deleted =0 and b.status_active=1 and b.is_deleted =0 and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0
				group by b.to_floor_id, b.fabric_shade, c.body_part_id, b.to_batch_id, b.to_room, b.to_rack, b.to_shelf, b.to_bin_box, b.to_prod_id, d.id
			) x
			group by x.floor, x.fabric_shade, x.body_part_id, x.batch_id, x.room,x.rack_no, x.shelf_no,x.bin_box, x.prod_id, x.sys_id, x.type";

		$data_array=sql_select($data_sql);
		foreach ($data_array as $row) 
		{
			$rcv_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin_box')]]+=$row[csf('qnty')];

			if($row[csf("type")] ==1 && $row[csf("sys_id")] == str_replace("'", "", $txt_received_id))
			{
				$this_receive_mrr_arr[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin_box')]]+=$row[csf('qnty')];
			}

		}

		$rcv_transfer_in_qty 	= $rcv_qty_array[str_replace("'", "", $txt_prod_id)][str_replace("'", "", $hidden_batch_id)][str_replace("'", "", $txt_fabric_shade)][str_replace("'", "", $cbo_body_part)][$cbo_floor][$cbo_room][$txt_rack][$txt_shelf][$txt_bin];
		$issue_return_quantity 	= $issRt_qty_array[str_replace("'", "", $txt_prod_id)][str_replace("'", "", $hidden_batch_id)][str_replace("'", "", $txt_fabric_shade)][str_replace("'", "", $cbo_body_part)][$cbo_floor][$cbo_room][$txt_rack][$txt_shelf][$txt_bin];

		$issue_quantity 		= $issue_qty_array[str_replace("'", "", $txt_prod_id)][str_replace("'", "", $hidden_batch_id)][str_replace("'", "", $txt_fabric_shade)][str_replace("'", "", $cbo_body_part)][$cbo_floor][$cbo_room][$txt_rack][$txt_shelf][$txt_bin];
		$trans_out_qnty 		= $trans_out_qnty_array[str_replace("'", "", $txt_prod_id)][str_replace("'", "", $hidden_batch_id)][str_replace("'", "", $txt_fabric_shade)][str_replace("'", "", $cbo_body_part)][$cbo_floor][$cbo_room][$txt_rack][$txt_shelf][$txt_bin];
		$return_quantity 		= $return_arr[str_replace("'", "", $txt_prod_id)][str_replace("'", "", $hidden_batch_id)][str_replace("'", "", $txt_fabric_shade)][str_replace("'", "", $cbo_body_part)][$cbo_floor][$cbo_room][$txt_rack][$txt_shelf][$txt_bin];
		$global_ref_stock = ($rcv_transfer_in_qty +$issue_return_quantity) - ($issue_quantity + $trans_out_qnty + $return_quantity);


		$this_challan_ret_qnty 	= $this_challan_return_arr[str_replace("'", "", $txt_prod_id)][str_replace("'", "", $hidden_batch_id)][str_replace("'", "", $txt_fabric_shade)][str_replace("'", "", $cbo_body_part)][$cbo_floor][$cbo_room][$txt_rack][$txt_shelf][$txt_bin];

		$this_receive_mrr_qnty 	= $this_receive_mrr_arr[str_replace("'", "", $txt_prod_id)][str_replace("'", "", $hidden_batch_id)][str_replace("'", "", $txt_fabric_shade)][str_replace("'", "", $cbo_body_part)][$cbo_floor][$cbo_room][$txt_rack][$txt_shelf][$txt_bin];
		$balance_qnty = $this_receive_mrr_qnty-$this_challan_ret_qnty;

		/*echo "10**$global_ref_stock = ($rcv_transfer_in_qty +$issue_return_quantity) - ($issue_quantity + $trans_out_qnty + $return_quantity) , $this_challan_ret_qnty=$this_receive_mrr_qnty";
		die;*/


		$txt_return_qnty=str_replace("'","",$txt_return_qnty);
		if($txt_return_qnty>$global_ref_stock)
		{
			echo "30**Return Quantity Not Over Global Stock.\nGlobal stock :$global_ref_stock";
			die;
		}
		
		if($txt_return_qnty > $balance_qnty)
		{
			echo "30**Return Quantity Can not be Greater Than MRR Balance Quantity.\nBalance :$balance_qnty";
			die;
		}

		//----------------------------Order lvl array-----------

		if(str_replace("'","",$txt_break_qnty))
		{
			$order_sql = sql_select("select a.po_breakdown_id, sum(a.quantity) as receive_qnty from  order_wise_pro_details a, inv_transaction b where a.trans_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.company_id=$cbo_company_id and b.mst_id=$txt_received_id and b.prod_id=$txt_prod_id and b.pi_wo_batch_no=$hidden_batch_id and a.entry_form in(7,37) and b.body_part_id=$cbo_body_part and b.fabric_shade=$txt_fabric_shade and b.floor_id=$cbo_floor and b.room=$cbo_room and b.rack=$txt_rack and b.self =$txt_shelf and b.bin_box =$txt_bin and b.transaction_type in(1) and a.trans_type in(1) and (a.is_sales=0 or a.is_sales is null) group by a.po_breakdown_id");
			
			if(empty($order_sql))
			{
				echo "10**receive not found";die;
			}
			foreach($order_sql as $row)
			{
				$po_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
				$rcv_po_qnty_arr[$row[csf('po_breakdown_id')]] = $row[csf('receive_qnty')];
			}
			
			$return_qnty_sql = sql_select("select c.po_breakdown_id, sum(c.quantity) as return_qnty from inv_issue_master a, order_wise_pro_details c, inv_transaction d where a.id = d.mst_id and c.trans_id=d.id and c.status_active=1 and c.entry_form in (46) and d.status_active=1 and d.is_deleted=0 and a.company_id=$cbo_company_id and d.store_id=$cbo_store_name and d.pi_wo_batch_no =$hidden_batch_id and a.received_id=$txt_received_id and d.prod_id=$txt_prod_id and d.body_part_id=$cbo_body_part and d.fabric_shade=$txt_fabric_shade and d.floor_id=$cbo_floor and d.room=$cbo_room and d.rack=$txt_rack and d.self = $txt_shelf and d.bin_box = $txt_bin $up_trans_cond2 group by c.po_breakdown_id");
			
			foreach ($return_qnty_sql as $row) 
			{
				$cumu_return_qnty_arr[$row[csf("po_breakdown_id")]]+= $row[csf("return_qnty")];
			}

			$sql_cuml="select b.po_breakdown_id,
			sum(case when b.entry_form in(7,37) and b.trans_type=1 and a.transaction_type=1 and a.pi_wo_batch_no=$hidden_batch_id then b.quantity end) as finish_fabric_recv,
			sum(case when b.entry_form=18 and b.trans_type=2 and a.transaction_type=2 and a.pi_wo_batch_no=$hidden_batch_id then b.quantity end) as finish_fabric_issue,
			sum(CASE WHEN b.entry_form=46 and b.trans_type=3 and a.transaction_type=3 and a.batch_id_from_fissuertn=$hidden_batch_id THEN b.quantity ELSE 0 END) AS recv_rtn_qnty,
			sum(CASE WHEN b.entry_form=52 and b.trans_type=4 and a.transaction_type=4 and a.batch_id_from_fissuertn=$hidden_batch_id THEN b.quantity ELSE 0 END) AS iss_retn_qnty,
			sum(case when b.entry_form in(14,15,306) and b.trans_type=5 and a.transaction_type=5 and a.pi_wo_batch_no=$hidden_batch_id then b.quantity end) as finish_fabric_trans_recv,
			sum(case when b.entry_form in(14,15,306) and b.trans_type=6 and a.transaction_type=6 and a.pi_wo_batch_no=$hidden_batch_id then b.quantity end) as finish_fabric_trans_issued
			from inv_transaction a, order_wise_pro_details b
			where a.id=b.trans_id and b.po_breakdown_id in(".implode(',',$po_arr). ") $up_trans_cond and a.prod_id=$txt_prod_id and b.prod_id=$txt_prod_id and a.floor_id=$cbo_floor and a.room=$cbo_room and a.rack=$txt_rack and a.self = $txt_shelf and a.bin_box = $txt_bin and a.fabric_shade=$txt_fabric_shade and a.store_id=$cbo_store_name and a.body_part_id=$cbo_body_part and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.po_breakdown_id";

			$sql_result_cuml=sql_select($sql_cuml);
			foreach($sql_result_cuml as $row)
			{
				$cumu_rec_qty[$row[csf('po_breakdown_id')]]=($row[csf('finish_fabric_recv')]+$row[csf('finish_fabric_trans_recv')])-$row[csf('recv_rtn_qnty')];
				$cumu_iss_qty[$row[csf('po_breakdown_id')]]=($row[csf('finish_fabric_issue')]+$row[csf('finish_fabric_trans_issued')])-$row[csf('iss_retn_qnty')];
			}

		}
	}
	//----------------------------Order lvl End----------- 

	//echo "10**".$sql_cuml;die;
	//==================================Balance Check End ============================================

	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		//------------------------------Check Duplicate END---------------------------------------//
		
		
		/*$txt_global_stock=str_replace("'","",$txt_global_stock);
		if($txt_return_qnty>$txt_global_stock)
		{
			echo "30**Return Quantity Not Over Global Stock.";
			die;
		}

		$sql_receive=sql_select("select id,balance_qnty from inv_transaction where id=$hidden_receive_trans_id and balance_qnty>0");
		$balance_qnty = $sql_receive[0][csf("balance_qnty")];

		if($txt_return_qnty > $balance_qnty)
		{
			echo "30**Return Quantity Can not be Greater Than Balance Quantity.";
			die;
		}*/
		//echo "10**failed";die;

		if(str_replace("'","",$issue_mst_id)!="")
		{
			$new_return_number[0] = str_replace("'","",$txt_return_no);
			$id=str_replace("'","",$issue_mst_id);
			//issue master table UPDATE here START----------------------//
			$field_array_mst="company_id*issue_date*received_id*received_mrr_no*pi_id*updated_by*update_date";
			$data_array_mst=$cbo_company_id."*".$txt_return_date."*".$txt_received_id."*".$txt_mrr_no."*".$pi_id."*'".$user_id."'*'".$pc_date_time."'";
		}
		else
		{
			//issue master table entry here START---------------------------------------//
			if($db_type==0) $year_cond="YEAR(insert_date)";
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later

			$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
			$new_return_number = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,str_replace("'","",$cbo_company_id),'FRR',46,date("Y",time())));

			$field_array_mst="id, issue_number_prefix, issue_number_prefix_num, issue_number, entry_form, item_category, company_id, issue_date, received_id, received_mrr_no, pi_id, inserted_by, insert_date";
			$data_array_mst="(".$id.",'".$new_return_number[1]."','".$new_return_number[2]."','".$new_return_number[0]."',46,2,".$cbo_company_id.",".$txt_return_date.",".$txt_received_id.",".$txt_mrr_no.",".$pi_id.",'".$user_id."','".$pc_date_time."')";
		}

		//transaction table insert here START--------------------------------//cbo_uom
		$txt_return_qnty = str_replace("'","",$txt_return_qnty);
		$cons_rate 		 = str_replace("'","",$txt_cons_rate);
		if ($cons_rate=="")
		{
			$cons_rate=0;
		}
		$cons_amount 	 = str_replace("'","",$txt_cons_rate)*$txt_return_qnty;



		//$transactionID = return_next_id("id", "inv_transaction", 1);
		$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$field_array_trans = "id,mst_id,company_id,prod_id,batch_id_from_fissuertn,pi_wo_batch_no,item_category,transaction_type,transaction_date,store_id,body_part_id,floor_id,room,rack,self,bin_box,order_uom,order_qnty,order_rate,order_amount,cons_uom,cons_quantity,cons_rate,cons_amount,no_of_roll,remarks,inserted_by,insert_date,booking_no,fabric_shade";
		$data_array_trans = "(".$transactionID.",".$id.",".$cbo_company_id.",".$txt_prod_id.",".$hidden_batch_id.",".$hidden_batch_id.",2,3,".$txt_return_date.",".$cbo_store_name.",".$cbo_body_part.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$txt_bin.",".$cbo_uom.",".$txt_return_qnty.",".$cons_rate.",".$cons_amount.",".$cbo_uom.",".$txt_return_qnty.",".$cons_rate.",".$cons_amount.",".$txt_roll.",".$txt_remarks.",'".$user_id."','".$pc_date_time."',".$txt_booking_no.",".$txt_fabric_shade.")";
		//transaction table insert here END ---------------------------------//

		//Issue Details Table Starts here
		$id_dtls=return_next_id_by_sequence( "INV_FIN_FAB_ISSUE_DTLS_PK_SEQ", "inv_finish_fabric_issue_dtls", $con) ;
		$field_array_dtls="id,mst_id,trans_id,batch_id,prod_id,uom,issue_qnty,fabric_shade,store_id,no_of_roll,body_part_id,rack_no,shelf_no,bin_box,floor,room, order_id,inserted_by,insert_date,width_type,booking_no";

		$data_array_dtls="(".$id_dtls.",".$id.",".$transactionID.",".$hidden_batch_id.",".$txt_prod_id.",".$cbo_uom.",".$txt_return_qnty.",".$txt_fabric_shade.",".$cbo_store_name.",".$txt_roll.",".$cbo_body_part.",".$txt_rack.",".$txt_shelf.",".$txt_bin.",".$cbo_floor.",".$cbo_room.",".$txt_order_id_all.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_dia_width_type.",".$txt_booking_no.")";
		//Issue Details Table Ends here

		//adjust product master table START-------------------------------------//
		$sql = sql_select("select product_name_details,last_purchased_qnty,current_stock, stock_value, color from product_details_master where id=$txt_prod_id");
		$presentStock=$available_qnty=0;$color_id=0;
		$product_name_details="";
		foreach($sql as $result)
		{
			$presentStock			= $result[csf("current_stock")];
			$presentStockValue		= $result[csf("stock_value")];
			$product_name_details 	= $result[csf("product_name_details")];
			$available_qnty			= $result[csf("available_qnty")];
			$color_id				= $result[csf("color")];
		}
		$nowStock 		= $presentStock-$txt_return_qnty;
		$nowStockValue 	= $presentStockValue-$cons_amount;
		$available_qnty = $available_qnty-$txt_return_qnty;

		if($nowStock > 0){
			$nowStockRate = $nowStockValue/$nowStock;
		}else{
			$nowStockRate =0;
			$nowStockValue =0;
		}

		$nowStock = number_format($nowStock,2,".","");
		$field_array_prod="last_issued_qnty*current_stock*stock_value*avg_rate_per_unit*available_qnty*updated_by*update_date";
		$data_array_prod=$txt_return_qnty."*".$nowStock."*'".$nowStockValue."'*'".$nowStockRate."'*".$available_qnty."*'".$user_id."'*'".$pc_date_time."'";

		//order_wise_pro_detail table insert here
		$txt_break_qnty=str_replace("'","",$txt_break_qnty);
		$txt_break_roll=str_replace("'","",$txt_break_roll);
		$txt_order_id_all=str_replace("'","",$txt_order_id_all);
		$ordr_wise_rtn_qnty_arr=explode("_",$txt_break_qnty);
		$ordr_wise_rtn_roll_arr=explode("_",$txt_break_roll);
		$ordr_id_arr=explode(",",$txt_order_id_all);

		$data_array_proportion=$data_array_roll="";
		if(!empty($txt_break_qnty))
		{
			if($variable_setting_production==1)
			{
				$field_array_proportion="id,trans_id,trans_type,entry_form,po_breakdown_id,prod_id,quantity,color_id,inserted_by,insert_date,is_sales";
				$order_array=array();
				$field_array_roll="id,mst_id,dtls_id,po_breakdown_id,entry_form,roll_no,roll_id,qnty,inserted_by,insert_date";
				foreach($ordr_wise_rtn_roll_arr as $val)
				{
					$order_roll_arr=explode("**",$val);
					$po_id=$order_roll_arr[0];
					$roll_no=$order_roll_arr[1];
					$qty=$order_roll_arr[2];
					$rollId=$order_roll_arr[3];

					if($qty>0)
					{
						$roll_id = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
						if($data_array_roll!="") $data_array_roll.=", ";
						$data_array_roll.="(".$roll_id.",".$id.",".$transactionID.",".$po_id.",46,'".$roll_no."','".$rollId."',".$qty.",'".$user_id."','".$pc_date_time."')";

						$order_array[$po_id]+=$qty;
					}
				}

				foreach($order_array as $po_id=>$po_qty)
				{
					$proportion_id = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					if($data_array_proportion!="") $data_array_proportion.=", ";
					$data_array_proportion.="(".$proportion_id.",".$transactionID.",3,46,".$po_id.",".$txt_prod_id.",".$po_qty.",".$color_id.",'".$user_id."','".$pc_date_time."',".$txt_is_sales.")";
				}
			}
			else
			{
				$field_array_proportion="id,trans_id,dtls_id,trans_type,entry_form,po_breakdown_id,prod_id,quantity,color_id,inserted_by,insert_date,is_sales";
				foreach($ordr_wise_rtn_qnty_arr as $val)
				{
					$order_qnty_arr=explode("**",$val);
					if($order_qnty_arr[1]>0)
					{

						$order_global_bal_qnty = $cumu_rec_qty[$order_qnty_arr[0]] - $cumu_iss_qty[$order_qnty_arr[0]];
						$order_global_bal_qnty=number_format($order_global_bal_qnty,4);
						
						$order_qnty_arrx=number_format($order_qnty_arr[1],4);
						if($order_qnty_arrx > $order_global_bal_qnty )
						{
							echo "20**Order Quantity Can not Greater than Stock Balance Quantity.";
							disconnect($con);die;
						}

						$order_rcv_bal_qnty = $rcv_po_qnty_arr[$order_qnty_arr[0]] - $cumu_return_qnty_arr[$order_qnty_arr[0]];
						$order_rcv_bal_qnty=number_format($order_rcv_bal_qnty,4);
						if($order_qnty_arrx > $order_rcv_bal_qnty)
						{
							echo "20**Order Quantity Can not Greater than Receive Balance Quantity.";
							disconnect($con);die;
						}

						$proportion_id = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
						if($data_array_proportion!="") $data_array_proportion.=", ";
						$data_array_proportion.="(".$proportion_id.",".$transactionID.",".$id_dtls.",3,46,".$order_qnty_arr[0].",".$txt_prod_id.",".$order_qnty_arr[1].",".$color_id.",'".$user_id."','".$pc_date_time."',".$txt_is_sales.")";
					}
				}
			}
		}

		/*$hidden_receive_trans_id=str_replace("'","",$hidden_receive_trans_id);
		$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
		$field_array = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,inserted_by,insert_date";
		$data_array="(".$mrrWiseIsID.",".$hidden_receive_trans_id.",".$transactionID.",46,".$txt_prod_id.",".$txt_return_qnty.",'".$user_id."','".$pc_date_time."')";
		$update_array = "balance_qnty*updated_by*update_date";
		
		$issueQntyBalance=$balance_qnty-$txt_return_qnty;
		$update_data="".$issueQntyBalance."*'".$user_id."'*'".$pc_date_time."'";*/


		$rID=$transID=$detailsID=$prodUpdate=$propoId=$rollId=$mrrWiseIssueID=$upTrID=true;
		if(str_replace("'","",$txt_return_no)!="")
		{
			$rID=sql_update("inv_issue_master",$field_array_mst,$data_array_mst,"id",$id,1);
		}
		else
		{
			$rID=sql_insert("inv_issue_master",$field_array_mst,$data_array_mst,1);
		}

		// echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		$transID = sql_insert("inv_transaction",$field_array_trans,$data_array_trans,1);
		//echo "10**insert into inv_finish_fabric_issue_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$detailsID = sql_insert("inv_finish_fabric_issue_dtls",$field_array_dtls,$data_array_dtls,1);

		$prodUpdate = sql_update("product_details_master",$field_array_prod,$data_array_prod,"id",$txt_prod_id,1);
		if($data_array_proportion!="")
		{
			$propoId=sql_insert("order_wise_pro_details",$field_array_proportion,$data_array_proportion,1);
			if($variable_setting_production==1)
			{
				$rollId=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,1);
			}
		}

		/*//mrr wise issue data insert here----------------------------//
		if($data_array!="")
		{
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array,$data_array,1);
		}

		//transaction table stock update here------------------------//
		if($balance_qnty>0)
		{
			$upTrID=sql_update("inv_transaction",$update_array,$update_data,"id",$hidden_receive_trans_id,1);
		}*/

		/*echo "10**$rID && $transID && $detailsID && $prodUpdate && $propoId && $rollId && $mrrWiseIssueID && $upTrID";
		oci_rollback($con);
		die;*/

		if($db_type==0)
		{
			if( $rID && $transID && $detailsID && $prodUpdate && $propoId && $rollId && $mrrWiseIssueID && $upTrID)
			{
				mysql_query("COMMIT");
				echo "0**".$new_return_number[0]."**".$id;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$new_return_number[0];
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if( $rID && $transID && $detailsID && $prodUpdate && $propoId && $rollId && $mrrWiseIssueID && $upTrID)
			{
				oci_commit($con);
				echo "0**".$new_return_number[0]."**".$id;
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
		//check update id
		if( str_replace("'","",$update_id) == "" )
		{
			echo "10";disconnect($con);die;
		}

		$txt_return_qnty=str_replace("'","",$txt_return_qnty);
		$txt_global_stock=str_replace("'","",$txt_global_stock);
		$prev_return_qnty = str_replace("'","",$prev_return_qnty);

		$cons_rate 		 = str_replace("'","",$txt_cons_rate);
		$cons_amount 	 = str_replace("'","",$txt_cons_rate)*$txt_return_qnty;

		/*if($txt_return_qnty>($txt_global_stock+$prev_return_qnty))
		{
			echo "30**Return Quantity Not Over Global Stock.";
			die;
		}*/


		//****************************************** BEFORE ENTRY ADJUST START *****************************************//
		//product master table information
		//before stock update
		$sql = sql_select( "select a.id,a.current_stock, a.stock_value, b.cons_quantity, b.cons_amount from product_details_master a, inv_transaction b where a.id=b.prod_id and a.id=$before_prod_id and b.id=$update_id and a.item_category_id=2 and b.item_category=2 and b.transaction_type=3" );
		$before_prod_id=$before_issue_qnty=$before_stock_qnty=0;
		foreach($sql as $result)
		{
			$before_prod_id 	= $result[csf("id")];
			$before_stock_qnty 	= $result[csf("current_stock")];
			$before_stock_value 	= $result[csf("stock_value")];
			//before quantity and stock value
			$before_issue_qnty	= $result[csf("cons_quantity")];
			$before_issue_amount	= $result[csf("cons_amount")];
		}

		//current product ID
		$txt_prod_id = str_replace("'","",$txt_prod_id);
		$txt_return_qnty = str_replace("'","",$txt_return_qnty);
		$before_prod_id= str_replace("'","",$before_prod_id);
		//$curr_stock_qnty=return_field_value("current_stock","product_details_master","id=$txt_prod_id and item_category_id=2");
		$curr_sql = sql_select("select current_stock, stock_value from product_details_master where id=$txt_prod_id and item_category_id=2");
		$curr_stock_qnty= $curr_sql[0][csf("current_stock")];
		$curr_stock_value= $curr_sql[0][csf("stock_value")];

		$receive_purpose=return_field_value("receive_purpose","inv_receive_master","id=$txt_received_id");
 		//echo $receive_purpose;die;
		//weighted and average rate START here------------------------//
		//product master table data UPDATE START----------------------//
		$update_array_prod= "last_issued_qnty*current_stock*stock_value*avg_rate_per_unit*updated_by*update_date";
		if($before_prod_id==$txt_prod_id)
		{
			$adj_stock_qnty = $curr_stock_qnty+$before_issue_qnty-$txt_return_qnty; // CurrentStock + Before Issue Qnty - Current Issue Qnty
			if($adj_stock_qnty<0) //Aziz
			{
				echo "30**Stock cannot be less than zero.";disconnect($con);die;
			}

			$adj_stock_value = $curr_stock_value+$before_issue_amount-$cons_amount; 
			if($adj_stock_qnty>0) 
			{
				$adj_stock_rate = $adj_stock_value/$adj_stock_qnty;
			}else{
				$adj_stock_rate =0;
				$adj_stock_value=0;
			}

			$adj_stock_qnty = number_format($adj_stock_qnty,2,".","");
			$data_array_prod= $txt_return_qnty."*".$adj_stock_qnty."*'".$adj_stock_value."'*'".$adj_stock_rate."'*'".$user_id."'*'".$pc_date_time."'";
			//if($query1) echo "20**OK"; else echo "20**ERROR";die;
			//now current stock
			$curr_stock_qnty 	= $adj_stock_qnty;
		}
		else
		{
			$updateIdprod_array = $update_dataProd = array();
			//before product adjust
			$adj_before_stock_qnty 	= $before_stock_qnty+$prev_return_qnty; // CurrentStock + Before Issue Qnty
			 if($adj_before_stock_qnty<0) //Aziz
			 {
			 	echo "30**Stock cannot be less than zero.";disconnect($con);die;
			 }
			 $adj_before_stock_value = $before_stock_value+$before_issue_amount;

			 if($adj_before_stock_qnty>0){
			 	$adj_before_stock_rate = $adj_before_stock_value/$adj_before_stock_qnty;
			 }else{
			 	$adj_before_stock_rate =0;
			 	$adj_before_stock_value=0;
			 }

			 $adj_before_stock_qnty = number_format($adj_before_stock_qnty,2,".","");
			 $updateIdprod_array[]=$before_prod_id;
			 $update_dataProd[$before_prod_id]=explode("*",("".$prev_return_qnty."*".$adj_before_stock_qnty."*'".$adj_before_stock_value."'*'".$adj_before_stock_rate."'*'".$user_id."'*'".$pc_date_time."'"));

			//current product adjust
			$adj_curr_stock_qnty = 	$curr_stock_qnty-$txt_return_qnty; // CurrentStock + Before Issue Qnty
			$adj_curr_stock_value = $curr_stock_value-$cons_amount; 

			if($adj_curr_stock_qnty >0){
				$adj_curr_stock_rate = $adj_curr_stock_value/$adj_curr_stock_qnty;
			}else{
				$adj_curr_stock_rate =0;
				$adj_curr_stock_value=0;
			}

			$adj_curr_stock_qnty = number_format($adj_curr_stock_qnty,2,".","");
			$updateIdprod_array[]=$txt_prod_id;
			$update_dataProd[$txt_prod_id]=explode("*",("".$txt_return_qnty."*".$adj_curr_stock_qnty."*'".$adj_curr_stock_value."'*'".$adj_curr_stock_rate."'*'".$user_id."'*'".$pc_date_time."'"));
			//$query1=execute_query(bulk_update_sql_statement("product_details_master","id",$update_array_prod,$update_dataProd,$updateIdprod_array));

			//now current stock
			$curr_stock_qnty 	= $adj_curr_stock_qnty;
		}



		//****************************************** BEFORE ENTRY ADJUST END *****************************************//

		$id=$issue_mst_id;
		//yarn master table UPDATE here START----------------------//
		$field_array_mst="company_id*issue_date*received_id*pi_id*received_mrr_no*updated_by*update_date";
		$data_array_mst=$cbo_company_id."*".$txt_return_date."*".$txt_received_id."*".$pi_id."*".$txt_mrr_no."*'".$user_id."'*'".$pc_date_time."'";

		

		$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$field_array_trans="company_id*prod_id*batch_id_from_fissuertn*pi_wo_batch_no*item_category*transaction_type*transaction_date*store_id*body_part_id*floor_id*room*rack*self*bin_box*order_uom*order_qnty*order_rate*order_amount*cons_uom*cons_quantity*cons_rate*cons_amount*no_of_roll*remarks*updated_by*update_date*booking_no*fabric_shade";
		$data_array_trans= "".$cbo_company_id."*".$txt_prod_id."*".$hidden_batch_id."*".$hidden_batch_id."*2*3*".$txt_return_date."*".$cbo_store_name."*".$cbo_body_part."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$txt_bin."*".$cbo_uom."*".$txt_return_qnty."*".$cons_rate."*".$cons_amount."*".$cbo_uom."*".$txt_return_qnty."*".$cons_rate."*".$cons_amount."*".$txt_roll."*".$txt_remarks."*'".$user_id."'*'".$pc_date_time."'*".$txt_booking_no."*".$txt_fabric_shade."";

		$field_array_dtls="batch_id*prod_id*uom*issue_qnty*fabric_shade*store_id*no_of_roll*body_part_id*rack_no*shelf_no*bin_box*floor*room*updated_by*update_date*width_type*booking_no";
		$data_array_dtls=$hidden_batch_id."*".$txt_prod_id."*".$cbo_uom."*".$txt_return_qnty."*".$txt_fabric_shade."*".$cbo_store_name."*".$txt_roll."*".$cbo_body_part."*".$txt_rack."*".$txt_shelf."*".$txt_bin."*".$cbo_floor."*".$cbo_room."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_dia_width_type."*".$txt_booking_no."";

		$update_id = str_replace("'","",$update_id);
		$update_details_id = str_replace("'","",$update_details_id);
		$hidden_receive_trans_id = str_replace("'","",$hidden_receive_trans_id);
		$before_receive_trans_id = str_replace("'","",$before_receive_trans_id);
		$prev_return_qnty = str_replace("'","",$prev_return_qnty);

		/*
			$update_array_trans = "balance_qnty*updated_by*update_date";
			if($before_prod_id==$txt_prod_id)
			{
				if($hidden_receive_trans_id>0)
				{
					$sql_receive = sql_select("select a.id,a.balance_qnty from inv_transaction a where a.id=$hidden_receive_trans_id and a.transaction_type =1");
					$adjBalance = ($sql_receive[0][csf("balance_qnty")]+$prev_return_qnty)-$txt_return_qnty;
					$update_data_trans="".$adjBalance."*'".$user_id."'*'".$pc_date_time."'";
				}
			}
			else
			{
				if($before_receive_trans_id>0)
				{
					$sql_receive_before = sql_select("select a.id,a.balance_qnty from inv_transaction a where a.id=$before_receive_trans_id and a.transaction_type =1");
					$adjBalance = ($sql_receive_before[0][csf("balance_qnty")]+$prev_return_qnty);
					$update_data_before_trans="".$adjBalance."*'".$user_id."'*'".$pc_date_time."'";
				}

				if($hidden_receive_trans_id>0)
				{
					$sql_receive = sql_select("select a.id,a.balance_qnty from inv_transaction a where a.id=$hidden_receive_trans_id and a.transaction_type =1");
					$adjBalance = $sql_receive[0][csf("balance_qnty")]-$txt_return_qnty;
					$update_data_trans="".$adjBalance."*'".$user_id."'*'".$pc_date_time."'";
				}

			}

			$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
			$field_array_mrr = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,inserted_by,insert_date";
			$data_array_mrr = "(".$mrrWiseIsID.",".$hidden_receive_trans_id.",".$update_id.",46,".$txt_prod_id.",".$txt_return_qnty.",'".$user_id."','".$pc_date_time."')";

		*/

		//order_wise_pro_detail table insert here
		$txt_break_qnty=str_replace("'","",$txt_break_qnty);
		$txt_break_roll=str_replace("'","",$txt_break_roll);
		$txt_order_id_all=str_replace("'","",$txt_order_id_all);
		$ordr_wise_rtn_qnty_arr=explode("_",$txt_break_qnty);
		$ordr_wise_rtn_roll_arr=explode("_",$txt_break_roll);
		$ordr_id_arr=explode(",",$txt_order_id_all);
		//$color_id=return_field_value("color","product_details_master","id=$txt_prod_id and item_category_id=2");
		$color_id= str_replace("'","",$txt_color_id);

		$data_array_proportion=$data_array_roll="";
		if(!empty($txt_break_qnty))
		{
			if($variable_setting_production==1)
			{
				$field_array_proportion="id,trans_id,trans_type,entry_form,po_breakdown_id,prod_id,quantity,color_id,inserted_by,insert_date,is_sales";
				$order_array=array();
				$field_array_roll="id,mst_id,dtls_id,po_breakdown_id,entry_form,roll_no,roll_id,qnty,inserted_by,insert_date";
				foreach($ordr_wise_rtn_roll_arr as $val)
				{
					$order_roll_arr=explode("**",$val);
					$po_id=$order_roll_arr[0];
					$roll_no=$order_roll_arr[1];
					$qty=$order_roll_arr[2];
					$rollId=$order_roll_arr[3];

					if($qty>0)
					{
						$roll_id = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
						if($data_array_roll!="") $data_array_roll.=", ";
						$data_array_roll.="(".$roll_id.",".$id.",".$update_id.",".$po_id.",46,'".$roll_no."','".$rollId."',".$qty.",'".$user_id."','".$pc_date_time."')";
						$order_array[$po_id]+=$qty;
					}
				}

				foreach($order_array as $po_id=>$po_qty)
				{
					$proportion_id = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					if($data_array_proportion!="") $data_array_proportion.=", ";
					$data_array_proportion.="(".$proportion_id.",".$update_id.",3,46,".$po_id.",".$txt_prod_id.",".$po_qty.",".$color_id.",'".$user_id."','".$pc_date_time."',".$txt_is_sales.")";
				}
			}
			else
			{
				$field_array_proportion="id,trans_id,dtls_id,trans_type,entry_form,po_breakdown_id,prod_id,quantity,color_id,inserted_by,insert_date,is_sales";
				foreach($ordr_wise_rtn_qnty_arr as $val)
				{
					$order_qnty_arr=explode("**",$val);
					if($order_qnty_arr[1]>0)
					{
						$order_global_bal_qnty = $cumu_rec_qty[$order_qnty_arr[0]] - $cumu_iss_qty[$order_qnty_arr[0]];
						if($order_qnty_arr[1] > $order_global_bal_qnty )
						{
							echo "20**Order Quantity Can not Greater than Stock Balance Quantity.";
							disconnect($con);die;
						}

						$order_rcv_bal_qnty = $rcv_po_qnty_arr[$order_qnty_arr[0]] - $cumu_return_qnty_arr[$order_qnty_arr[0]];
						if($order_qnty_arr[1] > $order_rcv_bal_qnty)
						{
							echo "20**Order Quantity Can not Greater than Receive Balance Quantity.";
							disconnect($con);die;
						}

						$proportion_id = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
						if($data_array_proportion!="") $data_array_proportion.=", ";
						$data_array_proportion.="(".$proportion_id.",".$update_id.",".$update_details_id.",3,46,".$order_qnty_arr[0].",".$txt_prod_id.",".$order_qnty_arr[1].",".$color_id.",'".$user_id."','".$pc_date_time."',".$txt_is_sales.")";
					}
				}
			}
		}

		//echo "10**insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
		$query1=$query2=$query3=$query4=$query5=$rID=$transID=$detailsID=$propoId=$rollId=$mrrWiseIssueID=$upTrID=true;

		if($before_prod_id==$txt_prod_id)
		{
			$query1= sql_update("product_details_master",$update_array_prod,$data_array_prod,"id",$before_prod_id,1);
			$rID=sql_update("inv_issue_master",$field_array_mst,$data_array_mst,"id",$id,1);
			$transID = sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_id,1);
			$detailsID = sql_update("inv_finish_fabric_issue_dtls",$field_array_dtls,$data_array_dtls,"id",$update_details_id,1);
			
			/*if($hidden_receive_trans_id>0)
			{
				$query3 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_id and entry_form=46");
				$query2 = sql_update("inv_transaction",$update_array_trans,$update_data_trans,"id",$hidden_receive_trans_id,1);
				$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
			}*/

			if($data_array_proportion!="")
			{
				$query4 = execute_query("DELETE FROM order_wise_pro_details WHERE trans_id=$update_id and entry_form=46");
				$propoId=sql_insert("order_wise_pro_details",$field_array_proportion,$data_array_proportion,1);
				if($variable_setting_production==1)
				{
					$query5 = execute_query("DELETE FROM pro_roll_details WHERE dtls_id=$update_id and entry_form=46");
					$rollId=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,1);
				}
			}
			//mrr wise issue data insert here----------------------------//
		}
		else
		{
			$query1=execute_query(bulk_update_sql_statement("product_details_master","id",$update_array_prod,$update_dataProd,$updateIdprod_array));

			$rID=sql_update("inv_issue_master",$field_array_mst,$data_array_mst,"id",$id,1);
			$transID = sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_id,1);
			$detailsID = sql_update("inv_finish_fabric_issue_dtls",$field_array_dtls,$data_array_dtls,"id",$update_details_id,1);
			
			/*if($hidden_receive_trans_id>0)
			{
				$query3 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_id and entry_form=46");
				$query2 = sql_update("inv_transaction",$update_array_trans,$update_data_trans,"id",$hidden_receive_trans_id,1);
				$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
			}
			if($before_receive_trans_id>0)
			{
				$upTrID = sql_update("inv_transaction",$update_array_trans,$update_data_before_trans,"id",$before_receive_trans_id,1);
			}*/

			if($data_array_proportion!="")
			{
				$query4 = execute_query("DELETE FROM order_wise_pro_details WHERE trans_id=$update_id and entry_form=46");
				$propoId=sql_insert("order_wise_pro_details",$field_array_proportion,$data_array_proportion,1);
				if($variable_setting_production==1)
				{
					$query5 = execute_query("DELETE FROM pro_roll_details WHERE dtls_id=$update_id and entry_form=46");
					$rollId=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,1);
				}
			}
			//mrr wise issue data insert here----------------------------//

		}

		
		/*echo "10**".$query1."&&".$query2."&&".$query3."&&".$query4 ."&&". $query5 ."&&".$rID."&&".$transID."&&".$detailsID."&&".$propoId."&&".$rollId."&&".$mrrWiseIssueID."&&".$upTrID;
		oci_rollback($con);
		die;*/

		if($db_type==0)
		{
			if($query1 && $query2 && $query3 && $query4 && $query5 && $rID && $transID && $detailsID && $propoId && $rollId && $mrrWiseIssueID && $upTrID)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$txt_return_no)."**".$issue_mst_id;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_return_no);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($query1 && $query2 && $query3 && $query4 && $query5 && $rID && $transID && $detailsID && $propoId && $rollId && $mrrWiseIssueID && $upTrID)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_return_no)."**".$issue_mst_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_return_no);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		$con = connect(); 
		if($db_type==0)	{ mysql_query("BEGIN"); }
		// master table delete here---------------------------------------

		$mst_id = str_replace("'","",$issue_mst_id);
		if($mst_id=="" || $mst_id==0)
		{ 
			echo "30**Delete not allowed. Problem occurred"; disconnect($con);die;
		}
		else 
		{
			$update_trans_id = str_replace("'","",$update_id); // trans_id
			$txt_prod_id = str_replace("'","",$txt_prod_id);
			$txt_break_qnty=str_replace("'","",$txt_break_qnty);
			$prev_return_qnty = str_replace("'","",$prev_return_qnty);

			if( str_replace("'","",$update_trans_id) == "" )
			{
				echo "30**Delete not allowed. Problem occurred"; die;
				//check_table_status( $_SESSION['menu_id'],0);
				disconnect($con);
				exit(); 
			}
			/*$chk_next_transaction=return_field_value("id","inv_transaction","transaction_type in(1,2,3,4,5,6) and prod_id=$txt_prod_id and status_active=1 and is_deleted=0 and issue_trans_id >$update_trans_id ","id");
			if($chk_next_transaction !="")
			{ 
				echo "30**Delete not allowed.This item is used in another transaction"; disconnect($con);die;
			}*/
			else
			{
				$sql = sql_select( "SELECT a.id,a.current_stock, a.stock_value, b.cons_quantity, b.cons_amount, b.store_id from product_details_master a, inv_transaction b where a.id=b.prod_id and b.id=$update_trans_id and a.item_category_id=2 and b.item_category=2 and b.transaction_type=3" );
				// and a.id=$txt_prod_id
				$before_issue_qnty=$before_stock_qnty=0;
				foreach($sql as $result)
				{
					$before_prod_id 		= $result[csf("id")];
					$before_stock_qnty 		= $result[csf("current_stock")];
					$before_stock_value 	= $result[csf("stock_value")];
					$before_issue_qnty		= $result[csf("cons_quantity")];
					$before_issue_amount	= $result[csf("cons_amount")];
					$before_store_id		= $result[csf("store_id")];
				}

				$max_trans_query = sql_select("SELECT max(id) as max_id from inv_transaction where prod_id=$before_prod_id and store_id=$before_store_id and item_category=2 and status_active=1");
				$max_trans_id = $max_trans_query[0][csf('max_id')];

				if($max_trans_id > str_replace("'", "", $update_trans_id))
				{
					echo "20**Next transaction found of this store and product. delete not allowed.";
					die;
				}

				$adj_stock_qnty				= $before_stock_qnty+$before_issue_qnty;
				$adj_stock_value			= $before_stock_value+$before_issue_amount;

				if($adj_stock_qnty	>0){
					$adj_stock_rate= $adj_stock_value/$adj_stock_qnty;
				}else{
					$adj_stock_rate=0;
					$adj_stock_value=0;
				}

				$adj_stock_qnty = number_format($adj_stock_qnty,2,".","");
				$field_array_product="last_issued_qnty*current_stock*stock_value*avg_rate_per_unit*updated_by*update_date";
				$data_array_product=$before_issue_qnty."*".$adj_stock_qnty."*'".$adj_stock_value."'*'".$adj_stock_rate."'*'".$user_id."'*'".$pc_date_time."'";


				$checkTransaction = sql_select("SELECT id from inv_finish_fabric_issue_dtls where status_active=1 and is_deleted=0 and mst_id = ".$issue_mst_id." and id !=".$update_details_id."");
				if(count($checkTransaction) == 0)
				{
					$field_array = "updated_by*update_date*status_active*is_deleted";
					$data_array = "'" . $user_id . "'*'" . $pc_date_time . "'*0*1";
					$is_mst_del = sql_update("inv_issue_master", $field_array, $data_array, "id", $issue_mst_id, 1);
					if($is_mst_del) $flag=1; else $flag=0;
				}				

				$field_array_trans="updated_by*update_date*status_active*is_deleted";
				$data_array_trans="'".$user_id."'*'".$pc_date_time."'*0*1";
				$rID=sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_trans_id,1);
				if($rID) $flag=1; else $flag=0;

				$field_array_dtls="updated_by*update_date*status_active*is_deleted";
				$data_array_dtls="'".$user_id."'*'".$pc_date_time."'*0*1";
				$rID2=sql_update("inv_finish_fabric_issue_dtls",$field_array_dtls,$data_array_dtls,"id",$update_details_id,1);
				if($rID2) $flag=1; else $flag=0;

				$rID3=sql_update("product_details_master",$field_array_product,$data_array_product,"id",$before_prod_id,1);
				if($rID3) $flag=1; else $flag=0;

				if(!empty($txt_break_qnty))
				{
					if($variable_setting_production==1)
					{
						$field_array_roll="updated_by*update_date*status_active*is_deleted";
						$data_array_roll="'".$user_id."'*'".$pc_date_time."'*0*1";
						$rID4=sql_update("pro_roll_details",$field_array_roll,$data_array_roll,"mst_id*dtls_id*entry_form","$issue_mst_id*$update_trans_id*46",1);
						if($rID4) $flag=1; else $flag=0;
					}
					$field_array_prop="updated_by*update_date*status_active*is_deleted";
					$data_array_prop="'".$user_id."'*'".$pc_date_time."'*0*1";
					$rID5=sql_update("order_wise_pro_details",$field_array_prop,$data_array_prop,"dtls_id*trans_id*entry_form","$update_details_id*$update_trans_id*46",1);
					if($rID5) $flag=1; else $flag=0;
				}
			}
		}
		// echo "10**$rID##$rID2##$rID3##$rID4##$rID5##$is_mst_del**$flag";
		// oci_rollback($con);disconnect($con);die;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_return_no)."**".$issue_mst_id."**".$is_mst_del;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_return_no)."**".$issue_mst_id."**".$is_mst_del;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_return_no)."**".$issue_mst_id."**".$is_mst_del;
			}
			else
			{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$txt_return_no)."**".$issue_mst_id."**".$is_mst_del;
			}
		}
		disconnect($con);
		die;
	}
}



if($action=="return_number_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(mrr)
		{
 		$("#hidden_return_number").val(mrr); // mrr number
 		parent.emailwindow.hide();
 	}
 </script>

</head>

<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>
						<th width="180">Search By</th>
						<th width="200" align="center" id="search_by_td_up">Enter Return Number</th>
						<th width="220">Date Range</th>
						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td align="center">
							<?
							$search_by = array(1=>'Return Number',2=>"Batch No");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 140, $search_by,"",0, "--Select--", "",$dd,0 );
							?>
						</td>
						<td width="" align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td align="center">
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" />&nbsp;&nbsp;&nbsp;
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" />
						</td>
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_return_search_list_view', 'search_div', 'finish_fab_garments_receive_rtn_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td align="center" height="40" valign="middle" colspan="5">
							<? echo load_month_buttons(1);  ?>
							<!-- Hidden field here -->
							<input type="hidden" id="hidden_return_number" value="" />
							<!-- END -->
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


if($action=="create_return_search_list_view")
{

	$ex_data = explode("_",$data);
	$search_by = $ex_data[0];
	$search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];

	$sql_cond="";
	if($search_by==1)
	{
		if($search_common!="") $sql_cond .= " and a.issue_number like '%$search_common'";
	}
	else
	{
		if($search_common!="") $sql_cond .= " and c.batch_no like '%$search_common'";
	}

	if( $txt_date_from!="" && $txt_date_to!="" )
	{
		if($db_type==0)
		{
			$sql_cond .= " and a.issue_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else
		{
			$sql_cond .= " and a.issue_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
		}
	}

	if(trim($company)!="") $sql_cond .= " and a.company_id='$company'";

	if($db_type==0) $year_field="YEAR(a.insert_date) as year,";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	if($db_type==0)
	{
		$sql = "select a.id, YEAR(a.insert_date) as year, a.issue_number_prefix_num, a.issue_number, a.company_id, a.supplier_id,a.issue_date, a.item_category, a.received_id,a.received_mrr_no, sum(b.cons_quantity)as cons_quantity, group_concat(c.batch_no) as batch_no
		from inv_issue_master a, inv_transaction b, pro_batch_create_mst c
		where a.id=b.mst_id and b.batch_id_from_fissuertn=c.id and b.transaction_type=3 and a.status_active=1 and a.item_category=2 and b.item_category=2 and a.entry_form=46 $sql_cond
		group by a.id, a.issue_number_prefix_num, a.issue_number, a.company_id, a.supplier_id, a.issue_date, a.item_category, a.received_id, a.received_mrr_no, a.insert_date order by a.id";
	}
	else
	{
		$sql = "select a.id, to_char(a.insert_date,'YYYY') as year, a.issue_number_prefix_num, a.issue_number, a.company_id, a.supplier_id,a.issue_date, a.item_category, a.received_id,a.received_mrr_no, sum(b.cons_quantity)as cons_quantity, listagg(cast(c.batch_no as varchar(4000)), ',' ) within group(order by c.batch_no) as batch_no
		from inv_issue_master a, inv_transaction b, pro_batch_create_mst c
		where a.id=b.mst_id and b.batch_id_from_fissuertn=c.id and b.transaction_type=3 and a.status_active=1 and a.item_category=2 and b.item_category=2 and a.entry_form=46 $sql_cond
		group by a.id, a.issue_number_prefix_num, a.issue_number, a.company_id, a.supplier_id, a.issue_date, a.item_category, a.received_id, a.received_mrr_no, a.insert_date order by a.id";
	}

	//echo $sql;
	$sql_result=sql_select($sql);
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	?>
	<table class="rpt_table" border="1" cellpadding="2" cellspacing="0" width="750" rules="all">
		<thead>
			<tr>
				<th width="40">SL</th>
				<th width="60">Return No</th>
				<th width="50">Year</th>
				<th width="130">Company Name</th>
				<th width="80">Return Date</th>
				<th width="100">Return Qty</th>
				<th width="120">Receive MRR</th>
				<th>Batch NO</th>
			</tr>
		</thead>
	</table>
	<div style="width:750px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
		<table class="rpt_table" border="1" cellpadding="2" cellspacing="0" width="730" rules="all" id="list_view">
			<tbody>
				<?
				$i=1;
				foreach($sql_result as $row)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('issue_number')]."_".$row[csf('received_id')]; ?>');">
						<td width="40" align="center"><? echo $i; ?></td>
						<td width="60" align="center"><p><? echo $row[csf("issue_number_prefix_num")]; ?>&nbsp;</p></td>
						<td width="50" align="center"><p><? echo $row[csf("year")]; ?>&nbsp;</p></td>
						<td width="130"><p><? echo $company_arr[$row[csf("company_id")]]; ?>&nbsp;</p></td>
						<td width="80" align="center"><? if($row[csf("issue_date")]!="" && $row[csf("issue_date")]!="0000-00-00") echo change_date_format($row[csf("issue_date")]); ?></td>
						<td width="100" align="right"><? echo number_format($row[csf("cons_quantity")],2); ?></td>
						<td width="120" align="center"><p><? echo $row[csf("received_mrr_no")]; ?>&nbsp;</p></td>
						<td><p><? echo implode(",",array_unique(explode(",",$row[csf("batch_no")]))); ?>&nbsp;</p></td>
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



if($action=="populate_master_from_data")
{
	$sql = "select id,issue_number,company_id,supplier_id,issue_date,item_category,received_id,received_mrr_no,pi_id
	from inv_issue_master
	where id='$data' and item_category=2 and entry_form=46";
	//echo $sql;
	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "$('#txt_return_no').val('".$row[csf("issue_number")]."');\n";
		echo "$('#issue_mst_id').val('".$row[csf("id")]."');\n";
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		echo "$('#txt_return_date').val('".change_date_format($row[csf("issue_date")])."');\n";
		echo "$('#txt_mrr_no').val('".$row[csf("received_mrr_no")]."');\n";
		echo "$('#txt_received_id').val('".$row[csf("received_id")]."');\n";

		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "$('#txt_mrr_no').attr('disabled','disabled');\n";

		$receive_basis=return_field_value("receive_basis","inv_receive_master","id=".$row[csf("received_id")]);
		if($receive_basis==1)
		{
			$pi_no=return_field_value("pi_number","com_pi_master_details","id='".$row[csf("pi_id")]."'");
			echo "$('#txt_pi_no').removeAttr('disabled','disabled');\n";
			echo "$('#txt_pi_no').val('".$pi_no."');\n";
			echo "$('#pi_id').val('".$row[csf("pi_id")]."');\n";
		}
		else
		{
			echo "$('#txt_pi_no').attr('disabled','disabled');\n";
			echo "$('#txt_pi_no').val('');\n";
			echo "$('#pi_id').val('');\n";
		}
	}
	exit();
}

if($action=="show_dtls_list_view")
{

	$sql = "SELECT a.id as issue_id, a.issue_number, a.company_id, a.supplier_id, a.issue_date, a.item_category, a.received_id, a.received_mrr_no, b.id as trans_id, b.cons_quantity, b.cons_uom, b.cons_rate, b.cons_amount, c.product_name_details, c.id as prod_id, c.color as color_id
	from inv_issue_master a, inv_transaction b left join product_details_master c on b.prod_id=c.id
	where a.id=b.mst_id and b.item_category=2 and b.transaction_type=3 and a.id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	//echo $sql;
	$result = sql_select($sql);
	$i=1;
	$rettotalQnty=0;
	$rcvtotalQnty=0;
	$totalAmount=0;
	?>
	<table class="rpt_table" border="1" cellpadding="2" cellspacing="0" width="820" rules="all">
		<thead>
			<tr>
				<th width="50">SL</th>
				<th width="130">Return No</th>
				<th width="70">Return Date</th>
				<th width="50">Product ID</th>
				<th width="180">Item Description</th>
				<th width="100">Color</th>
				<th width="130">Received No</th>
				<th>Return Qnty</th>
			</tr>
		</thead>
		<tbody>
			<?
			foreach($result as $row)
			{
				if($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";

				/*echo "select b.balance_qnty from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.prod_id=".$row[csf("prod_id")]." and b.item_category=1 and b.transaction_type=1 and a.recv_number='".$row[csf("received_mrr_no")]."'";*/
				if($row[csf("prod_id")]!="")
				{
					$sqlTr = sql_select("select b.balance_qnty from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.prod_id=".$row[csf("prod_id")]." and b.item_category=2 and b.transaction_type in (1,4) and a.id='".$row[csf("received_id")]."'");
				}
				$rcvQnty = $sqlTr[0][csf('balance_qnty')];

				$rettotalQnty +=$row[csf("cons_quantity")];
					//$rcvtotalQnty +=$rcvQnty;
				$totalAmount +=$row[csf("cons_amount")];


				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("trans_id")];?>,<? echo $rcvQnty;?>,<? echo $row[csf("issue_id")];?>,<? echo $row[csf("received_id")];?>,<? echo $row[csf("company_id")];?>","child_form_input_data","requires/finish_fab_garments_receive_rtn_controller")' style="cursor:pointer" >
					<td><? echo $i; ?></td>
					<td><p><? echo $row[csf("issue_number")]; ?></p></td>
					<td><p><? echo change_date_format($row[csf("issue_date")],2); ?></p></td>
					<td align="center"><p><? echo $row[csf("prod_id")]; ?></p></td>
					<td ><p><? echo $row[csf("product_name_details")]; ?></p></td>
					<td ><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
					<td ><p><? echo $row[csf("received_mrr_no")]; ?></p></td>
					<td align="right"><p><? 
						echo number_format($row[csf("cons_quantity")],2,".","");
						//echo number_format($row[csf("cons_quantity")],0); 
					?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
			<tfoot>
				<th colspan="7">Total</th>
				<th><? echo number_format($rettotalQnty,2,".",""); ?></th>
			</tfoot>
		</tbody>
	</table>
	<?
	exit();
}


if($action=="child_form_input_data")
{
	$ex_data = explode(",",$data);
	$data = $ex_data[0]; 	// transaction id
	$rcvQnty = $ex_data[1];
	$issue_id=str_replace("'","",$ex_data[2]);
	$received_id=str_replace("'","",$ex_data[3]);
	$company_id=str_replace("'","",$ex_data[4]);
	/*
		$production_mrr=return_field_value("booking_no","inv_receive_master","company_id='".$company_id."' and entry_form=37 and id=$received_id and receive_basis=9 and item_category=2  and status_active=1","booking_no");

		$chk_production_order=return_field_value("booking_no","inv_receive_master","company_id='".$company_id."' and entry_form=7 and recv_number='".$production_mrr."' and receive_basis=5 and item_category=2  and status_active=1","booking_no");

		if($chk_production_order=="")
		{
			$sql = "select a.company_id,b.id as prod_id, b.product_name_details, b.current_stock, b.gsm, b.dia_width, b.color, a.id as tr_id, a.cons_uom, a.cons_quantity,a.cons_rate,a.cons_amount, a.batch_id_from_fissuertn, a.remarks,a.store_id,a.body_part_id, a.floor_id,a.room,a.rack, a.self, a.no_of_roll,0 as is_sales, d.width_type, d.fabric_shade, d.id as details_id,d.booking_no from inv_transaction a,inv_issue_master c, product_details_master b, inv_finish_fabric_issue_dtls d where a.id=$data and a.id = d.trans_id and a.status_active=1 and a.item_category=2 and a.transaction_type=3 and a.prod_id=b.id  and b.status_active=1 and d.mst_id=c.id and c.entry_form=46";
			//,c.is_sales,c.po_breakdown_id
		}
		else
		{
			$sql = "select a.company_id,b.id as prod_id, b.product_name_details, b.current_stock, b.gsm, b.dia_width, b.color, a.id as tr_id, a.cons_uom, a.cons_quantity,a.cons_rate,a.cons_amount, a.batch_id_from_fissuertn, a.remarks,a.store_id,a.body_part_id, a.floor_id,a.room,a.rack, a.self, a.no_of_roll, c.is_sales, c.po_breakdown_id, d.width_type, d.fabric_shade, d.id as details_id,d.booking_no from inv_transaction a,order_wise_pro_details c, product_details_master b, inv_finish_fabric_issue_dtls d where a.id=$data and a.id = d.trans_id and a.status_active=1 and a.item_category=2 and a.transaction_type=3 and a.prod_id=b.id  and b.status_active=1 and a.id=c.trans_id and c.entry_form=46";
		}
	*/

	$sql = "select a.company_id,b.id as prod_id, b.product_name_details, b.current_stock, b.gsm, b.dia_width, b.color, a.id as tr_id, a.cons_uom, a.cons_quantity,a.cons_rate,a.cons_amount, a.pi_wo_batch_no, a.remarks,a.store_id,a.body_part_id, a.floor_id,a.room,a.rack, a.self,a.bin_box, a.no_of_roll, c.is_sales, c.po_breakdown_id, d.width_type, d.fabric_shade, d.id as details_id,d.booking_no from inv_transaction a left join order_wise_pro_details c on a.id=c.trans_id and c.entry_form=46, product_details_master b, inv_finish_fabric_issue_dtls d where a.id=$data and a.id = d.trans_id and a.status_active=1 and a.item_category=2 and a.transaction_type=3 and a.prod_id=b.id  and b.status_active=1";
	//echo $sql;
	$result = sql_select($sql);

	foreach($result as $row)
	{
		$product_name_details = $row[csf("product_name_details")];
		$prod_id = $row[csf("prod_id")];
		$width_type = $row[csf("width_type")];
		$fabric_shade = $row[csf("fabric_shade")];
		$gsm = $row[csf("gsm")];
		$dia_width = $row[csf("dia_width")];
		$cons_quantity = $row[csf("cons_quantity")];
		$cons_rate = $row[csf("cons_rate")];
		$cons_amount = $row[csf("cons_amount")];
		$cons_uom = $row[csf("cons_uom")];
		$color = $row[csf("color")];
		$tr_id = $row[csf("tr_id")];
		$no_of_roll = $row[csf("no_of_roll")];
		$remarks = $row[csf("remarks")];
		$is_sales = $row[csf("is_sales")];
		$pi_wo_batch_no = $row[csf("pi_wo_batch_no")];
		$body_part_id = $row[csf("body_part_id")];

		$store_id = $row[csf("store_id")];
		$floor_id = $row[csf("floor_id")];
		$room = $row[csf("room")];
		$rack = $row[csf("rack")];
		$self = $row[csf("self")];
		$bin = $row[csf("bin_box")];
		$details_id = $row[csf("details_id")];
		$booking_no = $row[csf("booking_no")];
	}

	$floor_room_rack_arr = return_library_array("select a.floor_room_rack_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id = b.floor_room_rack_dtls_id and a.company_id =$company_id and b.store_id=$store_id and a.status_active=1 and b.status_active=1 group by a.floor_room_rack_id, a.floor_room_rack_name","floor_room_rack_id","floor_room_rack_name");

	$return_sql = "select a.received_id, d.pi_wo_batch_no, d.prod_id, d.cons_uom, d.body_part_id, d.fabric_shade, d.floor_id, d.room, d.rack, d.self,d.bin_box, sum(d.cons_quantity) as cumu_qnty from inv_issue_master a, inv_transaction d where a.id = d.mst_id and a.entry_form in (46) and d.transaction_type=3 and d.item_category=2 and d.status_active=1 and d.is_deleted=0 and a.company_id=$company_id and d.store_id=$store_id and d.prod_id=$prod_id and d.pi_wo_batch_no=$pi_wo_batch_no and d.body_part_id=$body_part_id and d.fabric_shade='$fabric_shade' and d.id <> $tr_id
	group by a.received_id,d.pi_wo_batch_no, d.prod_id, d.cons_uom, d.floor_id,d.room,d.rack,d.self,d.bin_box, d.fabric_shade, d.body_part_id";

	$return_data = sql_select($return_sql);
	foreach ($return_data as $row) 
	{
		if($row[csf("received_id")] == $received_id)
		{
			$this_challan_return_arr[$row[csf("prod_id")]][$row[csf("pi_wo_batch_no")]][$row[csf("fabric_shade")]][$row[csf("body_part_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]] += $row[csf("cumu_qnty")];
		}
		
		$return_arr[$row[csf("prod_id")]][$row[csf("pi_wo_batch_no")]][$row[csf("fabric_shade")]][$row[csf("body_part_id")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]] += $row[csf("cumu_qnty")];
		
	}
	unset($return_data);


	if($db_type==0)
	{
		$rackCond = " IFNULL(a.rack, 0) rack";
		$rackCond2 = " IFNULL(b.rack, 0) rack_no";
		$rackCond3 = " (case when b.rack_no is null or b.rack_no=0 then 0 else b.rack_no end) rack_no";
		$rackCond4 = " (case when b.to_rack is null or b.to_rack=0 then 0 else b.to_rack end) rack_no";
	}else{
		$rackCond = " nvl(a.rack, 0) rack";
		$rackCond2 = " nvl(b.rack, 0) rack_no";
		$rackCond3 = " cast(b.rack_no as varchar(4000)) as rack_no";
		$rackCond4 = " cast(b.to_rack as varchar(4000)) as rack_no";
	}
	$issData = sql_select("SELECT a.prod_id, a.pi_wo_batch_no as batch_id,c.fabric_shade,c.body_part_id,(case when a.floor_id is null or a.floor_id=0 then 0 else a.floor_id end) floor_id,$rackCond,(case when a.room is null or a.room=0 then 0 else a.room end) room,(case when a.self is null or a.self=0 then 0 else a.self end) self,(case when a.bin_box is null or a.bin_box=0 then 0 else a.bin_box end) bin_box, sum(case when a.transaction_type=2 then cons_quantity end) as issue_qnty 
	from inv_issue_master b,inv_transaction a,inv_finish_fabric_issue_dtls c, pro_batch_create_mst d 
	where b.entry_form=18 and b.id=a.mst_id and a.id=c.trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=2 and a.transaction_type=2 and c.status_active=1 and c.is_deleted=0 and a.pi_wo_batch_no = d.id and b.status_active=1 and b.is_deleted=0 and b.company_id=$company_id and a.store_id ='$store_id' and a.pi_wo_batch_no=$pi_wo_batch_no 
	group by a.prod_id, a.pi_wo_batch_no,c.fabric_shade, c.body_part_id,a.floor_id, a.room, a.rack, a.self,a.bin_box");
		
	foreach($issData as $row)
	{
		$issue_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]+=$row[csf('issue_qnty')];
	}

	$issueReturnData=sql_select("select a.prod_id, a.batch_id_from_fissuertn as batch_id, b.fabric_shade, b.body_part_id,  a.floor_id,a.room, a.rack, a.self,a.bin_box, sum(case when a.transaction_type=4 then a.cons_quantity end) as issrqnty from inv_transaction a,pro_finish_fabric_rcv_dtls b,pro_batch_create_mst c where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=2 and a.transaction_type in(4) and a.batch_id_from_fissuertn = c.id and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id and a.store_id ='$store_id' and a.batch_id_from_fissuertn =$pi_wo_batch_no  group by a.prod_id, b.fabric_shade, b.body_part_id, a.batch_id_from_fissuertn,a.floor_id,a.room, a.rack, a.self,a.bin_box");
	foreach($issueReturnData as $row)
	{
		$ir_floor_id = ($row[csf('floor_id')]=="")?0:$row[csf('floor_id')];
		$ir_room = ($row[csf('room')]=="")?0:$row[csf('room')];
		$ir_rack = ($row[csf('rack')]=="")?0:$row[csf('rack')];
		$ir_self = ($row[csf('self')]=="")?0:$row[csf('self')];
		$ir_bin = ($row[csf('bin_box')]=="")?0:$row[csf('bin_box')];
		$issRt_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$ir_floor_id][$ir_room][$ir_rack][$ir_self][$ir_bin] +=$row[csf('issrqnty')];
	}

	$transOutData = sql_select("select b.batch_id, b.from_store, (case when b.floor_id is null or b.floor_id=0 then 0 else b.floor_id end) as floor, b.fabric_shade,c.body_part_id, (case when b.room is null or b.room=0 then 0 else b.room end) room, $rackCond2,(case when b.shelf is null or b.shelf=0 then 0 else b.shelf end) shelf_no,(case when b.bin_box is null or b.bin_box=0 then 0 else b.bin_box end) bin_box, sum(b.transfer_qnty) as trans_out_qnty, b.from_prod_id as prod_id
		from inv_transaction c,order_wise_pro_details d,inv_item_transfer_dtls b, pro_batch_create_mst a
		where c.id=d.trans_id and d.dtls_id=b.id and c.company_id =$company_id and b.from_store =$store_id and c.transaction_type=6 and c.item_category=2 and b.batch_id=a.id and c.status_active=1 and c.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and d.status_active=1 and d.is_deleted =0 and d.entry_form=14 and d.trans_type=6 and b.active_dtls_id_in_transfer=1 and b.batch_id =$pi_wo_batch_no
		group by b.batch_id, b.from_store, b.floor_id, b.fabric_shade, c.body_part_id, b.room,b.rack,b.shelf,b.bin_box,b.from_prod_id");

	foreach($transOutData as $row)
	{
		$trans_out_qnty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin_box')]]=$row[csf('trans_out_qnty')];
	}

	if($db_type ==0)
	{
		$sales_flag_cond = " and (b.is_sales=0 or b.is_sales ='' ) ";
	}
	else
	{
		$sales_flag_cond = " and (b.is_sales=0 or b.is_sales is null) ";
	}
	$data_sql="select x.id, x.product_name_details, x.color, x.unit_of_measure, x.current_stock, x.company_id, x.store_id, x.floor, x.fabric_shade, x.body_part_id, x.batch_id,x.room,x.rack_no, x.shelf_no, x.bin_box, sum(x.cons_amount) as cons_amount, sum(x.qnty) as qnty, x.prod_id,x.detarmination_id
		from
		(
		select a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock,a.company_id,c.store_id,(case when b.floor is null or b.floor=0 then 0 else b.floor end) as  floor,b.fabric_shade, b.body_part_id, b.batch_id,(case when b.room is null or b.room=0 then 0 else b.room end) room,$rackCond3,(case when b.shelf_no is null or b.shelf_no=0 then 0 else b.shelf_no end) shelf_no,(case when b.bin is null or b.bin=0 then 0 else b.bin end) bin_box,sum(d.cons_quantity) as qnty, d.gmt_item_id, b.prod_id, sum(d.cons_amount) as cons_amount,a.detarmination_id
		from product_details_master a, pro_finish_fabric_rcv_dtls b,inv_transaction d, inv_receive_master c, pro_batch_create_mst e
		where a.id=b.prod_id and b.trans_id=d.id and d.mst_id=c.id and b.batch_id = e.id and b.batch_id =$pi_wo_batch_no and c.company_id=$company_id and d.store_id =$store_id and c.entry_form in (7,37) $sales_flag_cond and a.item_category_id=2 and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 group by a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock,a.company_id,c.store_id, b.floor, b.fabric_shade,b.body_part_id, b.batch_id, b.room, b.rack_no, b.shelf_no, b.bin, d.gmt_item_id, b.prod_id, a.detarmination_id
		union all
		select a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock,a.company_id,c.store_id,(case when b.to_floor_id is null or b.to_floor_id=0 then 0 else b.to_floor_id end) as  floor, b.fabric_shade, c.body_part_id, b.to_batch_id as batch_id,(case when b.to_room is null or b.to_room=0 then 0 else b.to_room end) room, $rackCond4, (case when b.to_shelf is null or b.to_shelf=0 then 0 else b.to_shelf end) shelf_no, (case when b.to_bin_box is null or b.to_bin_box=0 then 0 else b.to_bin_box end) bin_box, sum(c.cons_quantity) as qnty, 0 as gmt_item_id, b.to_prod_id as prod_id, sum(c.cons_amount) as cons_amount,a.detarmination_id
		from product_details_master a, inv_item_transfer_dtls b, inv_transaction c, inv_item_transfer_mst d, pro_batch_create_mst e
		where a.id = b.to_prod_id and b.to_trans_id = c.id and b.mst_id = d.id and d.to_company = $company_id and c.store_id =$store_id and c.transaction_type = 5 and c.item_category = 2  and b.to_batch_id = e.id  and b.to_batch_id =$pi_wo_batch_no and c.status_active =1 and c.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and d.status_active = 1 and d.is_deleted =0 and a.status_active=1 and a.is_deleted=0
		group by a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock,a.company_id,c.store_id, b.to_floor_id, b.fabric_shade, c.body_part_id, b.to_batch_id, b.to_room, b.to_rack, b.to_shelf,b.to_bin_box, b.to_prod_id, a.detarmination_id
		) x
		group by x.id, x.product_name_details, x.color, x.unit_of_measure, x.current_stock, x.company_id,x.store_id, x.floor, x.fabric_shade, x.body_part_id, x.batch_id, x.room,x.rack_no, x.shelf_no,x.bin_box, x.prod_id, x.detarmination_id";

	$data_array=sql_select($data_sql);
	foreach ($data_array as $row) 
	{
		$rcv_qty_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]][$row[csf('bin_box')]]+=$row[csf('qnty')];
	}

	$rcv_transfer_in_qty = $rcv_qty_array[$prod_id][$pi_wo_batch_no][$fabric_shade][$body_part_id][$floor_id][$room][$rack][$self][$bin];
	$issue_return_quantity = $issRt_qty_array[$prod_id][$pi_wo_batch_no][$fabric_shade][$body_part_id][$floor_id][$room][$rack][$self][$bin];

	$issue_quantity = $issue_qty_array[$prod_id][$pi_wo_batch_no][$fabric_shade][$body_part_id][$floor_id][$room][$rack][$self][$bin];
	$trans_out_qnty = $trans_out_qnty_array[$prod_id][$pi_wo_batch_no][$fabric_shade][$body_part_id][$floor_id][$room][$rack][$self][$bin];
	$return_quantity = $return_arr[$prod_id][$pi_wo_batch_no][$fabric_shade][$body_part_id][$floor_id][$room][$rack][$self][$bin];


	$global_ref_stock = ($rcv_transfer_in_qty +$issue_return_quantity) - ($issue_quantity + $trans_out_qnty + $return_quantity);

	//=================================================================
	//echo "($rcv_transfer_in_qty + $issue_return_quantity) - ($issue_quantity + $trans_out_qnty + $return_quantity)";die;

	$variable_setting_production=return_field_value("fabric_roll_level","variable_settings_production","company_name='".$company_id."'  and item_category_id=2 and variable_list=3 and status_active=1","fabric_roll_level");

	echo "$('#txt_item_description').val('".$product_name_details."');\n";
	echo "$('#txt_prod_id').val('".$prod_id."');\n";
	echo "$('#txt_dia_width_type').val('".$width_type."');\n";
	echo "$('#txt_fabric_shade').val('".$fabric_shade."');\n";
	echo "$('#before_prod_id').val('".$prod_id."');\n";
	echo "$('#txt_gsm').val('".$gsm."');\n";
	echo "$('#txt_dia').val('".$dia_width."');\n";
	echo "$('#prev_return_qnty').val('".number_format($cons_quantity,2,".","")."');\n";
	echo "$('#txt_cons_rate').val('".$cons_rate."');\n";
	echo "$('#txt_amount').val('".number_format($cons_amount,2,".","")."');\n";
	echo "$('#cbo_uom').val(".$cons_uom.");\n";
	echo "$('#txt_color_name').val('".$color_arr[$color]."');\n";
	echo "$('#txt_color_id').val('".$color."');\n";
	echo "$('#txt_roll').val('".$no_of_roll."');\n";
	echo "$('#txt_remarks').val('".$remarks."');\n";
	echo "$('#txt_is_sales').val('".$is_sales."');\n";
	echo "$('#hidden_batch_id').val(".$pi_wo_batch_no.");\n";


	$receive_sql = sql_select("select b.id, b.cons_quantity as receive_quantity, a.booking_without_order, a.store_id, b.company_id, c.floor,c.room,c.rack_no, c.shelf_no,c.bin, c.body_part_id, b.cons_uom, b.cons_rate, c.batch_id, c.color_id, c.order_id, c.dia_width_type,c.fabric_shade,c.is_sales, d.id as prod_id, d.product_name_details, d.gsm, d.dia_width, d.current_stock
	from inv_receive_master a, inv_transaction b, pro_finish_fabric_rcv_dtls c, product_details_master d
	where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and a.id='$received_id' and b.pi_wo_batch_no=".$pi_wo_batch_no." and b.body_part_id= ".$body_part_id." and b.prod_id =".$prod_id." and b.fabric_shade='".$fabric_shade."' and b.floor_id='".$floor_id."' and b.room='".$room."' and b.rack='".$rack."' and b.self='".$self."' and b.bin_box='".$bin."'  and a.entry_form in (7,37) and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.is_sales=0");

	$receive_quantity=0;
	foreach($receive_sql as $val)
	{
		$po_arr[$val[csf('order_id')]] = $val[csf('order_id')];
		$receive_quantity += $val[csf("receive_quantity")];
		//$val[csf("receive_quantity")];
	}
	unset($recv_result);
	//echo $receive_quantity;die;

	$cumilitive_rtn = $this_challan_return_arr[$prod_id][$pi_wo_batch_no][$fabric_shade][$body_part_id][$floor_id][$room][$rack][$self][$bin];


	//$order_id_string=return_field_value("order_id","pro_finish_fabric_rcv_dtls ","trans_id=$recv_trans_id","order_id" );
	$po_arr = array_filter($po_arr);
	if(empty($po_arr))
	{
		echo "$('#txt_return_qnty').removeAttr('placeholder').removeAttr('readonly').removeAttr('onDblClick').attr('placeholder','write');\n";
	}
	else
	{
		echo "$('#txt_return_qnty').removeAttr('placeholder').removeAttr('readonly').removeAttr('onDblClick').attr('placeholder','Double Click To Search').attr('readonly','').attr('onDblClick','openmypage_rtn_qty();');\n";
	}

	echo "$('#txt_return_qnty').val('".number_format($cons_quantity,2,".","")."');\n";

	$batch_no=return_field_value("batch_no","pro_batch_create_mst","id='".$pi_wo_batch_no."'","batch_no" );
	echo "$('#txt_batch_no').val('".$batch_no."');\n";
	echo "$('#cbo_body_part').val('".$body_part_id."');\n";

	echo "load_room_rack_self_bin('requires/finish_fab_garments_receive_rtn_controller*2', 'store','store_td', '".$company_id."','"."',this.value);\n";
	echo "$('#cbo_store_name').val('".$store_id."');\n";

	
	echo "$('#cbo_floor').val('".$floor_id."');\n";
	echo "$('#cbo_floor_name').val('".$floor_room_rack_arr[$floor_id]."');\n";

	echo "$('#cbo_room').val('".$room."');\n";
	echo "$('#cbo_room_name').val('".$floor_room_rack_arr[$room]."');\n";

	echo "$('#txt_rack').val('".$rack."');\n";
	echo "$('#txt_rack_name').val('".$floor_room_rack_arr[$rack]."');\n";

	echo "$('#txt_shelf').val('".$self."');\n";
	echo "$('#txt_shelf_name').val('".$floor_room_rack_arr[$self]."');\n";

	echo "$('#txt_bin').val('".$bin."');\n";
	echo "$('#txt_bin_name').val('".$floor_room_rack_arr[$bin]."');\n";

	$propotion_sql=sql_select("select po_breakdown_id, quantity from order_wise_pro_details where trans_id='".$tr_id."'");
	$po_wise_qnty="";$po_id_all="";
	foreach($propotion_sql as $row_order)
	{
		if($po_wise_qnty!="") $po_wise_qnty .="_";
		$po_wise_qnty .=$row_order[csf("po_breakdown_id")]."**".$row_order[csf("quantity")];
		if($po_id_all!="") $po_id_all .=",";
		$po_id_all .=$row_order[csf("po_breakdown_id")];
	}

	if($variable_setting_production==1)
	{
		$roll_sql=sql_select("select po_breakdown_id, roll_no, roll_id, qnty from  pro_roll_details where mst_id='$issue_id' and dtls_id='".$tr_id."'");
		$roll_ref="";
		foreach($roll_sql as $row_roll)
		{
			if($roll_ref!="") $roll_ref .="_";
			$roll_ref .=$row_roll[csf("po_breakdown_id")]."**".$row_roll[csf("roll_no")]."**".$row_roll[csf("qnty")]."**".$row_roll[csf("roll_id")];
		}
	}
	echo "$('#txt_break_qnty').val('$po_wise_qnty');\n";
	echo "$('#txt_break_roll').val('$roll_ref');\n";
	echo "$('#txt_order_id_all').val('$po_id_all');\n";

	$yet_to_iss=$receive_quantity-$cumilitive_rtn;
	echo "$('#txt_fabric_received').val('$receive_quantity');\n";
	echo "$('#txt_cumulative_issued').val('$cumilitive_rtn');\n";
	echo "$('#txt_yet_to_issue').val('$yet_to_iss');\n";

	echo "$('#hidden_receive_trans_id').val('$recv_trans_id');\n";
	echo "$('#before_receive_trans_id').val('$recv_trans_id');\n";

	//echo "$('#txt_global_stock').val('".$row[csf("current_stock")]."');\n";
	echo "$('#txt_global_stock').val('".$global_ref_stock."');\n";
	echo "$('#update_id').val('".$tr_id."');\n";
	echo "$('#update_details_id').val('".$details_id."');\n";
	echo "$('#txt_booking_no').val('".$booking_no."');\n";

	echo "$('#cbo_store_name').attr('disabled','disabled');\n";
	echo "$('#cbo_floor').attr('disabled','disabled');\n";
	echo "$('#cbo_room').attr('disabled','disabled');\n";
	echo "$('#txt_rack').attr('disabled','disabled');\n";
	echo "$('#txt_shelf').attr('disabled','disabled');\n";
	echo "$('#txt_bin').attr('disabled','disabled');\n";
	echo "set_button_status(1, permission, 'fnc_yarn_receive_return_entry',1,1);\n";
	exit();
}

// pi popup here----------------------//
if ($action=="pi_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(str)
		{
			var splitData = str.split("_");
		$("#hidden_tbl_id").val(splitData[0]); // pi id
		$("#hidden_pi_number").val(splitData[1]); // pi number
		parent.emailwindow.hide();
	}
</script>

</head>

<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>
						<th align="center" id="search_by_th_up">Enter PI Number</th>
						<th>Date Range</th>
						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
					</tr>
				</thead>
				<tbody>
					<tr class="general">
						<td width="180" align="center" id="search_by_td">
							<input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td align="center">
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
						</td>
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_wopi_search_list_view', 'search_div', 'finish_fab_garments_receive_rtn_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td align="center" height="40" valign="middle" colspan="4">
							<? echo load_month_buttons(1);  ?>
							<!-- Hidden field here -->
							<input type="hidden" id="hidden_tbl_id" value="" />
							<input type="hidden" id="hidden_pi_number" value="hidden_pi_number" />
							<!-- END  -->
						</td>
					</tr>
				</tbody>
			</tr>
		</table>
		<div align="center" style="margin-top:5px" valign="top" id="search_div"> </div>
	</form>
</div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_wopi_search_list_view")
{
	$ex_data = explode("_",$data);
	$txt_search_common = trim($ex_data[0]);
	$txt_date_from = $ex_data[1];
	$txt_date_to = $ex_data[2];
	$company = $ex_data[3];

	$sql_cond="";
	$sql_cond .= " and a.pi_number LIKE '%$txt_search_common%'";
	if(trim($company)!=0) $sql_cond .= " and a.importer_id='$company'";

	if($txt_date_from!="" && $txt_date_to!="" )
	{
		if($db_type==0)
		{
			$sql_cond .= " and a.pi_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else
		{
			$sql_cond .= " and a.pi_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
		}
	}

	$sql = "select a.id, a.pi_number, a.pi_date, a.supplier_id, a.currency_id, a.source, c.lc_number as lc_number
	from com_pi_master_details a
	left join com_btb_lc_pi b on a.id=b.pi_id
	left join com_btb_lc_master_details c on b.com_btb_lc_master_details_id=c.id
	where
	a.item_category_id = 2 and
	a.status_active=1 and a.is_deleted=0
	$sql_cond order by a.id";
	//echo $sql;
	$result = sql_select($sql);
	$arr=array(3=>$currency,4=>$source);

	echo  create_list_view("list_view", "PI No, LC ,Date, Currency, Source","150,200,100,100","750","230",0, $sql , "js_set_value", "id,pi_number", "", 1, "0,0,0,currency_id,source", $arr, "pi_number,lc_number,pi_date,currency_id,source", "",'','0,0,3,1,0') ;
	exit();
}

if ($action=="fabric_receive_return_print") 
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);die;
	$company_name_arr=return_library_array( "select id, company_name from  lib_company",'id','company_name');
	$supplier_name_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr = return_library_array("select id,country_name from lib_country","id","country_name");

	//echo $sql_buyer_style;
	$sql=" select id, issue_number, received_id, issue_date, supplier_id from  inv_issue_master where id='$data[1]' and entry_form=46 and item_category=2 and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql);

	$sql_rretrn_to= sql_select("select id,recv_number,entry_form,company_id,receive_basis,receive_purpose,lc_no,knitting_source,knitting_company from inv_receive_master where id='".$dataArray[0][csf('received_id')]."' and entry_form in(7,37)");

	$kniting_company=$sql_rretrn_to[0][csf('knitting_company')];
	$kniting_source=$sql_rretrn_to[0][csf('knitting_source')];
	$rcv_num=$sql_rretrn_to[0][csf('recv_number')];

	if($kniting_source==1)
	{
		$company_nam=$company_name_arr[$kniting_company];
	}
	else if($kniting_source==3)
	{
		$company_nam=$supplier_name_arr[$kniting_company];
	}

 	$sql_dtls = "SELECT b.id as prod_id, b.product_name_details, a.id as tr_id, a.batch_id_from_fissuertn, a.store_id, a.cons_uom, a.cons_quantity, b.color as color_id, a.no_of_roll, a.remarks from inv_transaction a, product_details_master b, inv_issue_master c where c.id=a.mst_id and a.status_active=1 and a.company_id='$data[0]' and c.id='$data[1]' and a.item_category=2 and transaction_type=3 and a.prod_id=b.id and b.status_active=1 ";
 	// echo $sql_dtls;
	$sql_result= sql_select($sql_dtls);
	$batch_id_arr=array();
	foreach($sql_result as $row)
	{
		$batch_id_arr[$row[csf("batch_id_from_fissuertn")]] = $row[csf("batch_id_from_fissuertn")];
		$trans_id_arr[$row[csf("tr_id")]] = $row[csf("tr_id")];
	}
	$batch_id_arr = array_filter($batch_id_arr);

	$propotion_sql=sql_select("SELECT a.prod_id, a.po_breakdown_id, b.po_number from order_wise_pro_details a, wo_po_break_down b where a.po_breakdown_id=b.id and a.entry_form=46 and a.trans_type=3 and a.trans_id in (".implode(',',$trans_id_arr).") and a.status_active=1 and a.is_deleted=0");
	// echo "SELECT a.prod_id, a.po_breakdown_id, b.po_number from order_wise_pro_details a, wo_po_break_down b where a.po_breakdown_id=b.id and a.trans_id in (".implode(',',$trans_id_arr).") and a.status_active=1 and a.is_deleted=0";
	foreach($propotion_sql as $row_order)
	{
		$po_arr[$row_order[csf("prod_id")]].=$row_order[csf("po_number")].',';
	}
	// echo "<pre>";print_r($po_arr);

	$sql_buyer_style=sql_select("SELECT a.id, a.batch_no, a.booking_no, c.buyer_name, c.style_ref_no, d.grouping
	from pro_batch_create_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d
	where a.booking_no = b.booking_no and b.job_no=c.job_no and b.po_break_down_id = d.id and a.id in (".implode(',',$batch_id_arr).")
	group by a.id, a.batch_no, a.booking_no, c.buyer_name, c.style_ref_no, d.grouping
	union all 
	select a.id, a.batch_no, a.booking_no, b.buyer_id as buyer_name, null as style_ref_no, b.grouping
	from pro_batch_create_mst a, wo_non_ord_samp_booking_mst b
	where a.booking_no = b.booking_no and a.id in (".implode(',',$batch_id_arr).")
	group by a.id, a.batch_no, a.booking_no, b.buyer_id, b.grouping
	union all
	select a.id, a.batch_no, a.booking_no, b.buyer_id as buyer_name, null as style_ref_no, null as grouping
	from pro_batch_create_mst a, wo_non_ord_knitdye_booking_mst b
	where a.booking_no = b.booking_no and a.id in (".implode(',',$batch_id_arr).")
	group by a.id, a.batch_no, a.booking_no, b.buyer_id");

	foreach($sql_buyer_style as $row)
	{
		$buyer_name[$buyer_arr[$row[csf("buyer_name")]]] =$buyer_arr[$row[csf("buyer_name")]];
		$style_ref[$row[csf("style_ref_no")]] =$row[csf("style_ref_no")];
		$bookingNo[$row[csf("booking_no")]] = $row[csf("booking_no")];
		$batch_arr[$row[csf("id")]]=$row[csf("batch_no")];
		$grouping[$row[csf("grouping")]]=$row[csf("grouping")];
	}
	
	$buyer_name=implode(", ",$buyer_name);
	$style_ref=implode(", ",$style_ref);
	$bookingNo=implode(", ",$bookingNo);
	$grouping=implode(", ",array_filter($grouping));
	?>
	<div style="width:1130px;">
		<table width="1130" cellspacing="0" align="right">
			<tr>
				<td colspan="6" align="center" style="font-size:20px"><strong><? echo $company_name_arr[$data[0]]; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<?
				$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
				?>
				<td  align="left">
					<?
					foreach($data_array as $img_row)
					{
						?>
						<img src='../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50' align="middle" />
						<?
					}
					?>
				</td>
				<td colspan="4" align="center" style="font-size:14px">
					<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
						?>
						<? echo $result[csf('plot_no')]; ?>
						<? echo $result[csf('level_no')]?>
						<? echo $result[csf('road_no')]; ?>
						<? echo $result[csf('block_no')];?>
						<? echo $result[csf('city')];?>
						<? echo $result[csf('zip_code')]; ?>
						<? echo $result[csf('province')];?>
						<? echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email Address: <? echo $result[csf('email')];?>
						Website No: <? echo $result[csf('website')];
					}
					?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:16px"><strong><u> Return Delivery Challan</u></strong></td>
			</tr>
			<tr>
				<td width="120"><strong>Return Number:</strong></td>
				<td width="175"><? echo $dataArray[0][csf('issue_number')]; ?></td>
				<td width="110"><strong>Receive ID:</strong></td>
				<td width="175"><? echo $rcv_num; ?></td>
				<td width="100"><strong>Return To :</strong></td>
				<td width="175"><? echo $company_nam; ?></td>
			</tr>
			<tr>
				<td ><strong>Buyer:</strong></td>
				<td ><? echo $buyer_name; ?></td>
				<td ><strong>Style:</strong></td>
				<td ><? echo $style_ref; ?></td>
				<td><strong>Return Date:</strong></td>
				<td ><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>

			</tr>

			<tr>
				<!-- <td ><strong>FSO No:</strong></td>
				<td ><? //echo $fsoNo; ?></td> -->
				<td ><strong>Booking No:</strong></td>
				<td ><? echo $bookingNo; ?></td>
				<td ><strong>Internal Ref.</strong></td>
				<td ><? echo $grouping;?></td>
			</tr>
		</table>
		<div style="width:100%;">
			<table align="right" cellspacing="0" width="1130" border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="180">Item Description</th>
					<th width="70">Color</th>
					<th width="80">Batch</th>
					<th width="50">UOM</th>
					<th width="60">No Of Roll</th>
					<th width="80">Return Qty.</th>
					<th width="200">Order No.</th>
					<th width="100">Store</th>
					<th>Remarks</th>
				</thead>
				<?
				$mrr_no = $dataArray[0][csf('issue_number')];;
				$cond="";
				if($mrr_no!="") $cond .= " and c.issue_number='$mrr_no'";
				$i=1;

				foreach($sql_result as $row)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					$qnty+=$row[csf('cons_quantity')];
					$roll_qnty+=$row[csf('no_of_roll')];

					$all_po_no=chop($po_arr[$row[csf('prod_id')]],',');
					$all_po=implode(",", array_unique(explode(",", $all_po_no)));
					?>

					<tr bgcolor="<? echo $bgcolor; ?>">
						<td><? echo $i; ?></td>
						<td><? echo $row[csf('product_name_details')]; ?></td>
						<td align="center"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
						<td align="center"><? echo $batch_arr[$row[csf("batch_id_from_fissuertn")]]; ?></td>
						<td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
						<td align="right"><? echo number_format($row[csf('no_of_roll')]); ?></td>
						<td align="right"><? echo number_format($row[csf('cons_quantity')],2); ?></td>
						<td align="center"><? echo $all_po; ?></td>
						<td><? echo $store_library[$row[csf('store_id')]]; ?></td>
						<td><? echo $row[csf('remarks')]; ?></td>
					</tr>
					<?
					$i++;
				}
				?>
				<tr>
					<td align="right" colspan="5" >Total</td>
					<td align="right"><? echo number_format($roll_qnty,0,'',','); ?></td>
					<td align="right"><? echo number_format($qnty,2); ?></td>
					<td align="right">&nbsp;</td>
					<td align="right">&nbsp;</td>
					<td align="right">&nbsp;</td>
				</tr>
			</table>
			<br>
			<?
			echo signature_table(83, $data[0], "930px");
			?>
		</div>
	</div>
	<?
	exit();
}
?>
