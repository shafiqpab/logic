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
	echo "document.getElementById('store_update_upto').value 				= '".$variable_inventory."';\n";
	exit();
}
if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/finish_fab_receive_rtn_controller",$data);
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
	</script>

</head>

<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="750" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
				<thead>
					<tr>
						<th>Search By</th>
						<th align="center" id="search_by_td_up">Enter MRR Number</th>
						<th>Date Range</th>
						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
					</tr>
				</thead>
				<tbody>
					<tr class="general">
						<td>
							<?
							$search_by = array(1=>'MRR No',2=>'Challan No',3=>'Batch No');
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
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
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_mrr_search_list_view', 'search_div', 'finish_fab_receive_rtn_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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
		else if(trim($txt_search_by)==3) // for batch no
		{
			$sql_cond .= " and d.batch_no LIKE '%$txt_search_common%'";
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

	if($variable_setting_inventory==1) $entry_form_ref=" and a.entry_form in(7,225)"; else $entry_form_ref=" and a.entry_form in(225)";

	if($db_type==0) 
	{
		$batch_field = "group_concat(d.batch_no) as batch_no";
		$year_cond="YEAR(a.insert_date),";
	}
	else
	{
		$batch_field = " listagg(cast(d.batch_no as varchar(4000)),',') within group (order by d.batch_no) as batch_no ";
		$year_cond="to_char(a.insert_date,'YYYY'),";
	} 
	$sql = "select a.id,a.recv_number_prefix_num,a.recv_number,$year_cond a.challan_no,a.receive_date,a.receive_basis,  a.knitting_source,sum(b.cons_quantity) as receive_qnty,c.is_sales, $batch_field from inv_receive_master a,inv_transaction b, order_wise_pro_details c , pro_batch_create_mst d where a.id=b.mst_id and b.id=c.trans_id and b.pi_wo_batch_no = d.id $entry_form_ref and a.status_active=1 and b.status_active=1 and c.status_active=1 $sql_cond and a.company_id='$company' and c.entry_form=225 group by a.id, a.recv_number_prefix_num, a.recv_number,a.challan_no,a.receive_date,a.receive_basis,a.insert_date, a.knitting_source,c.is_sales order by a.id";
	//echo $sql;
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="800" align="left">
		<thead>
			<tr>
				<th width="50">SL</th>
				<th width="70">MRR No</th>
				<th width="50">Year</th>
				<th width="100">Challan No</th>
				<th width="100">Batch No</th>
				<th width="130">Dyeing Source</th>
				<th width="100">Receive Date</th>
				<th width="100">Receive Basis</th>
				<th>Receive Qnty</th>
			</tr>
		</thead>
	</table>

	<div style="width:820px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body" align="left">
		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="800" id="list_view" align="left">
			<tbody>
				<?
				$i=1;
				$sql_result=sql_select($sql);
				foreach($sql_result as $row)
				{
					if ($k%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer;" onClick="js_set_value('<? echo $row[csf("id")]; ?>_<? echo $row[csf("recv_number")]; ?>_<? echo $row[csf("is_sales")]; ?>')">
						<td width="50" align="center"><p><? echo $i; ?>&nbsp;</p></td>
						<td width="70" align="center"><p><? echo $row[csf("recv_number_prefix_num")]; ?>&nbsp;</p></td>
						<td width="50" align="center"><p><? echo $row[csf("year")]; ?>&nbsp;</p></td>
						<td width="100" ><p><? echo $row[csf("challan_no")]; ?>&nbsp;</p></td>
						<td width="100" ><p><? echo $row[csf("batch_no")]; ?>&nbsp;</p></td>
						<td width="130" ><p><? echo $knitting_source[$row[csf("knitting_source")]]; ?>&nbsp;</p></td>
						<td width="100" align="center"><p><? if($row[csf("receive_date")]!="" && $row[csf("receive_date")]!="0000-00-00") echo change_date_format($row[csf("receive_date")]); ?>&nbsp;</p></td>
						<td width="100"><p><? echo $receive_basis_arr[$row[csf("receive_basis")]]; ?>&nbsp;</p></td>
						<td align="right"><p><? echo number_format($row[csf("receive_qnty")],2,".",""); ?>&nbsp;</p></td>
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

	$sql = "select id,recv_number,entry_form,company_id,receive_basis,receive_purpose,lc_no,knitting_source,knitting_company
	from inv_receive_master
	where id='$data' and entry_form in(7,225)";
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
		echo "load_drop_down( 'requires/finish_fab_receive_rtn_controller', $kniting_company+'_'+$kniting_source+'_'+$company_id, 'load_drop_down_knitting_com','knitting_com');\n";
		if($row[csf("receive_basis")]==1)
		{
			echo "$('#txt_pi_no').removeAttr('disabled','disabled');\n";
		}
		else
		{
			echo "$('#txt_pi_no').attr('disabled','disabled');\n";
		}

		//right side list view
		echo"show_list_view('".$row[csf("id")]."','show_product_listview','list_product_container','requires/finish_fab_receive_rtn_controller','');\n";
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
		//echo $kniting_company;
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
	$sql = "select a.id as mrr_id, c.id as prod_id, c.product_name_details, b.batch_id, b.color_id, sum (d.cons_quantity) as receive_qnty, sum(d.cons_amount) as receive_amount, sum (d.order_qnty) as order_qnty, sum(d.order_amount) as order_amount, sum(b.aop_amount) as aop_amount, b.rack_no, b.shelf_no, b.is_sales,b.order_id, d.cons_uom, b.fabric_shade, b.floor, b.room, a.store_id, b.dia_width_type, b.body_part_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b, product_details_master c, inv_transaction d where a.id=b.mst_id and b.prod_id=c.id and a.id='$mrr_no' and b.mst_id=d.mst_id and d.item_category=2 and d.transaction_type=1 and b.trans_id=d.id and a.entry_form =225 group by a.id, c.id, c.product_name_details, b.batch_id, b.color_id, b.rack_no, b.shelf_no,b.is_sales,b.order_id,d.cons_uom, b.fabric_shade, b.floor, b.room, a.store_id, b.dia_width_type, b.body_part_id";

	//d.cons_rate,  
	
	$result = sql_select($sql);
	foreach($result as $row)
	{
		$batch_id_arr[$row[csf("batch_id")]] =$row[csf("batch_id")];
		$prod_id_arr[$row[csf("prod_id")]] =$row[csf("prod_id")];
		$cbo_store_name = $row[csf("store_id")];
	}


	$batch_id_arr = array_filter($batch_id_arr);
	if(count($batch_id_arr)>0)
	{	
		$all_batch_ids = implode(",", $batch_id_arr);
		$batchCond=""; $all_batch_id_cond="";
		$batchCond_1=""; $all_batch_id_cond_1="";
		$batchCond_2=""; $all_batch_id_cond_2="";
		if($db_type==2 && count($batch_id_arr)>999)
		{
			$batch_id_arr_chunk=array_chunk($batch_id_arr,999);
			foreach($batch_id_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$batchCond.="  c.batch_id in($chunk_arr_value) or ";
				$batchCond_1.="  b.batch_id in($chunk_arr_value) or ";
				$batchCond_2.="  c.pi_wo_batch_no in($chunk_arr_value) or ";
			}

			$all_batch_id_cond.=" and (".chop($batchCond,'or ').")";
			$all_batch_id_cond_1.=" and (".chop($batchCond_1,'or ').")";
			$all_batch_id_cond_2.=" and (".chop($batchCond_2,'or ').")";
		}
		else
		{
			$all_batch_id_cond=" and c.batch_id in($all_batch_ids)";
			$all_batch_id_cond_1=" and b.batch_id in($all_batch_ids)";
			$all_batch_id_cond_2=" and c.pi_wo_batch_no in($all_batch_ids)";
		}
	}

	$prod_id_arr = array_filter($prod_id_arr);
	if(count($prod_id_arr)>0)
	{	
		$all_prod_ids = implode(",", $prod_id_arr);
		$prodCond=""; $all_product_id_cond="";
		$prodCond_1=""; $all_product_id_cond_1="";
		if($db_type==2 && count($prod_id_arr)>999)
		{
			$prod_id_arr_chunk=array_chunk($prod_id_arr,999) ;
			foreach($prod_id_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$prodCond.="  d.id in($chunk_arr_value) or ";
				$prodCond_1.="  c.id in($chunk_arr_value) or ";
			}

			$all_product_id_cond.=" and (".chop($prodCond,'or ').")";
			$all_product_id_cond_1.=" and (".chop($prodCond_1,'or ').")";
		}
		else
		{
			$all_product_id_cond=" and d.id in($all_prod_ids)";
			$all_product_id_cond_1=" and c.id in($all_prod_ids)";
		}
	}

	$iss_rcvret_sql = sql_select("select a.received_id, a.entry_form, b.transaction_type, c.room,c.rack_no,c.shelf_no,c.floor,c.width_type, d.color,d.id as product_id, sum(b.cons_quantity) as qnty,c.batch_id, c.uom,c.fabric_shade, c.body_part_id
    from inv_issue_master a, inv_transaction b, inv_finish_fabric_issue_dtls c, product_details_master d
    where a.id=b.mst_id and a.id=c.mst_id and b.prod_id=d.id and b.id=c.trans_id and a.entry_form in (224,287) and b.status_active =1 and b.transaction_type in (2,3) and b.item_category =2 and a.status_active=1 and b.store_id=$cbo_store_name $all_batch_id_cond $all_product_id_cond
    group by a.received_id, a.entry_form, b.transaction_type, c.room,c.rack_no,c.shelf_no,c.floor,c.width_type, d.color, d.id,c.batch_id, c.uom, c.fabric_shade, c.body_part_id");

	foreach ($iss_rcvret_sql as $val) 
	{
		if($val[csf("entry_form")] == 224)
		{
			$issue_qnty_arr[$val[csf("batch_id")]][$val[csf("product_id")]][$val[csf("uom")]][$val[csf("width_type")]][$val[csf("color")]][$val[csf("floor")]][$val[csf("room")]][$val[csf("rack_no")]][$val[csf("shelf_no")]][$val[csf("fabric_shade")]][$val[csf("body_part_id")]] += $val[csf("qnty")];

		}
		else
		{
			if($val[csf("received_id")] == $mrr_no)
			{
				$this_challan_return_arr[$val[csf("batch_id")]][$val[csf("product_id")]][$val[csf("uom")]][$val[csf("width_type")]][$val[csf("color")]][$val[csf("floor")]][$val[csf("room")]][$val[csf("rack_no")]][$val[csf("shelf_no")]][$val[csf("fabric_shade")]][$val[csf("body_part_id")]] += $val[csf("qnty")];
			}

			$rcv_return_qnty_arr[$val[csf("batch_id")]][$val[csf("product_id")]][$val[csf("uom")]][$val[csf("width_type")]][$val[csf("color")]][$val[csf("floor")]][$val[csf("room")]][$val[csf("rack_no")]][$val[csf("shelf_no")]][$val[csf("fabric_shade")]][$val[csf("body_part_id")]] += $val[csf("qnty")];
		}
	}

	/*echo "<pre>***";
	print_r($this_challan_return_arr);
	die;*/

	$rcv_issret_sql = sql_select("select a.entry_form,d.transaction_type, c.id as product_id, b.batch_id, b.color_id, b.trans_id as tr_id, sum(d.cons_quantity) as qnty, b.rack_no, b.shelf_no,b.is_sales,b.order_id,d.cons_uom as uom, b.fabric_shade, b.floor, b.room, b.dia_width_type, b.fabric_shade, b.body_part_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b, product_details_master c,inv_transaction d where a.id=b.mst_id and b.prod_id=c.id and b.mst_id=d.mst_id and b.trans_id = d.id and d.item_category=2 and d.transaction_type in (1,4) and a.entry_form in (225,233) and d.store_id=$cbo_store_name $all_product_id_cond_1 $all_batch_id_cond_1 group by a.entry_form, d.transaction_type, c.id, b.batch_id, b.color_id, b.trans_id, b.receive_qnty, b.rack_no, b.shelf_no,b.is_sales,b.order_id, d.cons_uom, b.fabric_shade, b.floor, b.room, b.dia_width_type, b.fabric_shade, b.body_part_id");

	//d.cons_rate,

	foreach ($rcv_issret_sql as $val) 
	{
		if($val[csf("entry_form")] == 225)
		{
			$rcv_qnty_arr[$val[csf("batch_id")]][$val[csf("product_id")]][$val[csf("uom")]][$val[csf("dia_width_type")]][$val[csf("color_id")]][$val[csf("floor")]][$val[csf("room")]][$val[csf("rack_no")]][$val[csf("shelf_no")]][$val[csf("fabric_shade")]][$val[csf("body_part_id")]] += $val[csf("qnty")];
		}
		else
		{
			$issue_return_qnty_arr[$val[csf("batch_id")]][$val[csf("product_id")]][$val[csf("uom")]][$val[csf("dia_width_type")]][$val[csf("color_id")]][$val[csf("floor")]][$val[csf("room")]][$val[csf("rack_no")]][$val[csf("shelf_no")]][$val[csf("fabric_shade")]][$val[csf("body_part_id")]] += $val[csf("qnty")];
		}
	}

	$trans_out_sql = "SELECT sum(c.cons_quantity) as quantity, c.pi_wo_batch_no, c.prod_id, c.cons_uom, b.dia_width_type, d.color, c.floor_id, c.room, c.rack, c.self, b.fabric_shade, b.body_part_id
    from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c, product_details_master d
    where a.id=b.mst_id and b.trans_id=c.id and a.entry_form =230 and c.transaction_type=6 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.prod_id=d.id and c.store_id=$cbo_store_name $all_batch_id_cond_2
 	group by c.pi_wo_batch_no, c.prod_id, c.cons_uom, b.dia_width_type, d.color, c.floor_id, c.room, c.rack, c.self, b.fabric_shade, b.body_part_id";

	$trans_out_Data = sql_select($trans_out_sql);
	$sql_floor_id=$sql_room_id=$sql_rack_id=$sql_shelf_id=0;
	foreach ($trans_out_Data as $val) 
	{
		if($val[csf("floor_id")]=="") $sql_floor_id = 0; else $sql_floor_id = $val[csf("floor_id")];
		if($val[csf("room")]=="") $sql_room_id = 0; else $sql_room_id = $val[csf("room")];
		if($val[csf("rack")]=="") $sql_rack_id = 0; else $sql_rack_id = $val[csf("rack")];
		if($val[csf("self")]=="") $sql_shelf_id = 0; else $sql_shelf_id = $val[csf("self")];

		$trans_out_arr[$val[csf("pi_wo_batch_no")]][$val[csf("prod_id")]][$val[csf("cons_uom")]][$val[csf("dia_width_type")]][$val[csf("color")]][$sql_floor_id][$sql_room_id][$sql_rack_id][$sql_shelf_id][$val[csf("fabric_shade")]][$val[csf("body_part_id")]] += $val[csf("quantity")];
	}
	unset($trans_out_Data);

	$trans_in_sql = "SELECT sum(c.cons_quantity) as quantity, c.pi_wo_batch_no, c.prod_id, c.cons_uom, b.dia_width_type, d.color, c.floor_id, c.room, c.rack, c.self, b.fabric_shade, b.body_part_id
    from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c, product_details_master d
    where a.id=b.mst_id and b.to_trans_id=c.id and a.entry_form =230 and c.transaction_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.prod_id=d.id and c.store_id=$cbo_store_name $all_batch_id_cond_2
 	group by c.pi_wo_batch_no, c.prod_id, c.cons_uom, b.dia_width_type, d.color, c.floor_id, c.room, c.rack, c.self, b.fabric_shade, b.body_part_id";

	$trans_in_Data = sql_select($trans_in_sql);
	$sql_floor_id=$sql_room_id=$sql_rack_id=$sql_shelf_id=0;
	foreach ($trans_in_Data as $val) 
	{
		if($val[csf("floor_id")]=="") $sql_floor_id = 0; else $sql_floor_id = $val[csf("floor_id")];
		if($val[csf("room")]=="") $sql_room_id = 0; else $sql_room_id = $val[csf("room")];
		if($val[csf("rack")]=="") $sql_rack_id = 0; else $sql_rack_id = $val[csf("rack")];
		if($val[csf("self")]=="") $sql_shelf_id = 0; else $sql_shelf_id = $val[csf("self")];

		$trans_in_arr[$val[csf("pi_wo_batch_no")]][$val[csf("prod_id")]][$val[csf("cons_uom")]][$val[csf("dia_width_type")]][$val[csf("color")]][$sql_floor_id][$sql_room_id][$sql_rack_id][$sql_shelf_id][$val[csf("fabric_shade")]][$val[csf("body_part_id")]] += $val[csf("quantity")];
	}
	unset($trans_in_Data);

	$floor_roo_rak_arr=return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst","floor_room_rack_id","floor_room_rack_name");
	$batch_arr=return_library_array("select id, batch_no from pro_batch_create_mst","id","batch_no");


	/*
		$sql = "select a.id as mrr_id, c.id as prod_id, c.product_name_details, b.batch_id, b.color_id, b.trans_id as tr_id, b.receive_qnty as receive_qnty, b.rack_no, b.shelf_no,b.is_sales,b.order_id, d.cons_uom, b.fabric_shade, b.floor, b.room, a.store_id, b.dia_width_type from inv_receive_master a, pro_finish_fabric_rcv_dtls b, product_details_master c,inv_transaction d where a.id=b.mst_id and b.prod_id=c.id and a.id='$mrr_no' and b.mst_id=d.mst_id and d.item_category=2 and d.transaction_type=1 and b.trans_id = d.id and a.entry_form =225 group by a.id , c.id , c.product_name_details, b.batch_id, b.color_id, b.trans_id, b.receive_qnty, b.rack_no, b.shelf_no,b.is_sales,b.order_id,d.cons_uom, b.fabric_shade, b.floor, b.room, a.store_id, b.dia_width_type";
	*/


	$i=1;
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="450">
		<thead>
			<tr>
				<th width="30">SL</th>
				<th width="70">Batch No.</th>
				<th width="40">Product Id</th>
				<th width="140">Product Name</th>
				<th width="40">UOM</th>
				<th width="70">Dia/ W. Type</th>
				<th width="70">Color</th>
				<th width="70">F. Shade</th>
				<th width="50">Floor</th>
				<th width="50">Room</th>
				<th width="50">Rack</th>
				<th width="50">Shelf</th>
				<th>Curr.Stock</th>
			</tr>
		</thead>
		<tbody>
			<?
			foreach($result as $row)
			{
				$rcv_qnty = $rcv_qnty_arr[$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("cons_uom")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("floor")]][$row[csf("room")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("fabric_shade")]][$row[csf("body_part_id")]];
				$issue_return_qnty = $issue_return_qnty_arr[$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("cons_uom")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("floor")]][$row[csf("room")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("fabric_shade")]][$row[csf("body_part_id")]];

				$issue_qnty = $issue_qnty_arr[$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("cons_uom")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("floor")]][$row[csf("room")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("fabric_shade")]][$row[csf("body_part_id")]];

				$rcv_return_qnty = $rcv_return_qnty_arr[$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("cons_uom")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("floor")]][$row[csf("room")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("fabric_shade")]][$row[csf("body_part_id")]];

				$this_challan_return_qnty = $this_challan_return_arr[$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("cons_uom")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("floor")]][$row[csf("room")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("fabric_shade")]][$row[csf("body_part_id")]];

				$trans_out_qnty = $trans_out_arr[$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("cons_uom")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("floor")]][$row[csf("room")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("fabric_shade")]][$row[csf("body_part_id")]];

				$trans_in_qnty = $trans_in_arr[$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("cons_uom")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("floor")]][$row[csf("room")]][$row[csf("rack_no")]][$row[csf("shelf_no")]][$row[csf("fabric_shade")]][$row[csf("body_part_id")]];

				$total_rcv = $rcv_qnty + $issue_return_qnty;
				$total_issue = $issue_qnty + $rcv_return_qnty;
				//$balance_qnty = ($rcv_qnty + $issue_return_qnty ) - ($issue_qnty + $rcv_return_qnty);
				$balance_qnty=$row[csf("receive_qnty")]-$this_challan_return_qnty;

				$global_ref_stock = ($rcv_qnty +$issue_return_qnty + $trans_in_qnty) - ($issue_qnty + $trans_out_qnty + $rcv_return_qnty);
				

				$floor 		= $floor_roo_rak_arr[$row[csf("floor")]];
				$room 		= $floor_roo_rak_arr[$row[csf("room")]];
				$rack_no	= $floor_roo_rak_arr[$row[csf("rack_no")]];
				$shelf_no 	= $floor_roo_rak_arr[$row[csf("shelf_no")]];

				$cons_rate = $row[csf('receive_amount')]/$row[csf('receive_qnty')];
				$cons_rate = number_format($cons_rate,2,".","");

				$order_rate = $row[csf('order_amount')]/$row[csf('order_qnty')];
				$order_rate = number_format($order_rate,2,".","");

				$aop_rate = $row[csf('aop_amount')]/$row[csf('receive_qnty')];
				$aop_rate = number_format($aop_rate,4,".","");
				
				
				if ($i%2==0)$bgcolor="#E9F3FF";
				else $bgcolor="#FFFFFF";

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf('prod_id')]."**".$row[csf('rack_no')]."**".$row[csf('shelf_no')]."**".$row[csf('color_id')]."**".$row[csf('cons_uom')]."**".$row[csf('fabric_shade')]."**".$floor."**".$room."**".$rack_no."**".$shelf_no."**".$row[csf('floor')]."**".$row[csf('room')]."**".$row[csf('batch_id')]."**".$batch_arr[$row[csf('batch_id')]]."**".$row[csf('store_id')]."**".$row[csf('body_part_id')]."**".$cons_rate."**".$row[csf('fabric_description_id')]."**".$row[csf("mrr_id")]."**".$row[csf("dia_width_type")]."**".$row[csf("receive_qnty")]."**".$this_challan_return_qnty."**".$global_ref_stock."**".$order_rate."**".$aop_rate; ?>","item_details_form_input","requires/finish_fab_receive_rtn_controller")' style="cursor:pointer" >


					<td><? echo $i; ?></td>
					<td><? echo $batch_arr[$row[csf("batch_id")]]; ?></td>
					<td align="center"><? echo $row[csf("prod_id")]; ?></td>
					<td><? echo $row[csf("product_name_details")]; ?></td>
					<td><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
					<td><? echo $fabric_typee[$row[csf("dia_width_type")]]; ?></td>
					<td><? echo $color_arr[$row[csf("color_id")]]; ?></td>
					<td><? echo $fabric_shade[$row[csf("fabric_shade")]]; ?></td>
					<td><? echo $row[csf("floor")]; ?></td>
					<td><? echo $row[csf("room")]; ?></td>
					<td><? echo $row[csf("rack_no")]; ?></td>
					<td><? echo $row[csf("shelf_no")]; ?></td>
					<td align="right"><? echo number_format($balance_qnty,0); ?></td>
				</tr>
				<?
				$i++;
			} ?>
		</tbody>
	</table>
</fieldset>
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
	$order_rate = $data_ref[23];
	$aop_rate = $data_ref[24];


	$sql = "SELECT a.id as mrr_id, c.id as prod_id, c.product_name_details, b.batch_id, b.color_id, sum (d.cons_quantity) as receive_qnty, sum(d.cons_amount) as receive_amount, b.rack_no, b.shelf_no, b.is_sales, b.order_id, d.cons_uom, b.fabric_shade, b.floor, b.room, a.store_id, b.dia_width_type, b.body_part_id, c.gsm, c.dia_width from inv_receive_master a, pro_finish_fabric_rcv_dtls b, product_details_master c, inv_transaction d where a.id=b.mst_id and b.prod_id=c.id and a.id='$mrr_id' and b.mst_id=d.mst_id and d.item_category=2 and d.transaction_type=1 and b.trans_id=d.id and a.entry_form =225 and d.pi_wo_batch_no=$batch_id and d.prod_id=$prod_id and b.dia_width_type='$dia_width_type' and b.floor='$floor_id' and b.room='$room_id' and b.rack_no='$rack' and b.shelf_no='$shelf' and b.fabric_shade='$fabric_shade' and b.body_part_id='$body_part_id' group by a.id, c.id, c.product_name_details, b.batch_id, b.color_id, b.rack_no, b.shelf_no,b.is_sales,b.order_id,d.cons_uom, b.fabric_shade, b.floor, b.room, a.store_id, b.dia_width_type, b.body_part_id, c.gsm, c.dia_width";



	//[$val[csf("pi_wo_batch_no")]][$val[csf("prod_id")]][$val[csf("cons_uom")]][$val[csf("dia_width_type")]][$val[csf("color")]][$sql_floor_id][$sql_room_id][$sql_rack_id][$sql_shelf_id][$val[csf("fabric_shade")]][$val[csf("body_part_id")]]

	//and b.pi_wo_batch_no=$batch_id and b.body_part_id = $body_part_id and b.store_id=$store_id and b.prod_id =$prod_id and b.fabric_shade=$fabric_shade and b.floor_id='$floor_id' and b.room='$room_id' and b.rack='$rack' and b.self='$shelf'


	$result = sql_select($sql);
	foreach($result as $row)
	{
		$batch_id_arr[$row[csf("batch_id")]] = $row[csf("batch_id")];
		$prod_id_arr[$row[csf("prod_id")]] = $row[csf("prod_id")];
		//$trans_id_arr[$row[csf("trans_id")]] = $row[csf("trans_id")];

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

	$batch_id_arr = array_filter($batch_id_arr);
	if(!empty($batch_id_arr)){

		$batch_arr=return_library_array("select id,batch_no from pro_batch_create_mst where id in(".implode(",",$batch_id_arr).")","id","batch_no");
		$bookingNO = return_field_value( 'booking_no', 'pro_batch_create_mst', "id in(".implode(",",$batch_id_arr).")", 'booking_no' );
	}

	echo "$('#tbl_child').find('input,select').val('');\n";

	echo "$('#txt_item_description').val('".$product_name_details."');\n";
	echo "$('#txt_prod_id').val('".$prod_id."');\n";
	echo "$('#txt_dia_width_type').val('".$dia_width_type."');\n";
	echo "$('#txt_fabric_shade').val('".$fabric_shade."');\n";
	echo "$('#txt_gsm').val('".$gsm."');\n";
	echo "$('#txt_dia').val('".$dia_width."');\n";

	echo "$('#txt_batch_no').val('".$batch_no."');\n";
	echo "$('#cbo_body_part').val('".$body_part_id."');\n";

	echo "$('#cbo_store_name').val('".$store_id."');\n";
	echo "$('#cbo_floor').val('".$floor_id."');\n";
	echo "$('#cbo_floor_name').val('".$floor_name."');\n";
	echo "$('#cbo_room').val('".$room_id."');\n";
	echo "$('#cbo_room_name').val('".$room_name."');\n";
	echo "$('#txt_rack').val('".$rack."');\n";
	echo "$('#txt_rack_name').val('".$rack_name."');\n";
	echo "$('#txt_shelf').val('".$shelf."');\n";
	echo "$('#txt_shelf_name').val('".$shelf_name."');\n";


	echo "$('#txt_return_qnty').val('');\n";
	echo "$('#txt_break_qnty').val('');\n";
	echo "$('#txt_break_roll').val('');\n";
	echo "$('#txt_order_id_all').val('');\n";
	echo "$('#txt_roll').val('');\n";
	echo "$('#before_prod_id').val('');\n";
	echo "$('#update_id').val('');\n";
	echo "$('#update_details_id').val('');\n";

	$receive_quantity = number_format($receive_quantity,2,'.','');
	echo "$('#txt_fabric_received').val('".$receive_quantity."');\n";

	echo "$('#hidden_batch_id').val('".$batch_id."');\n";

	echo "$('#txt_cons_rate').val('".$cons_rate."');\n";
	$order_amount =$order_rate*$receive_quantity;

	echo "$('#txt_order_rate').val('".$order_rate."');\n";
	echo "$('#txt_amount').val('".$order_amount."');\n";
	echo "$('#txt_booking_no').val('".$bookingNO."');\n";
	echo "$('#txt_aop_rate').val('".$aop_rate."');\n";


	echo "$('#txt_return_qnty').removeAttr('placeholder').removeAttr('readonly').removeAttr('onDblClick').attr('placeholder','Double Click To Search').attr('readonly','').attr('onDblClick','openmypage_rtn_qty();');\n";

	$variable_setting_production=return_field_value("fabric_roll_level","variable_settings_production","company_name='".$row[csf("company_id")]."'  and item_category_id=2 and variable_list=3 and status_active=1","fabric_roll_level");
	if($variable_setting_production==1)
	{
		echo "$('#txt_roll').attr('readonly');\n";
	}
	else
	{
		echo "$('#txt_roll').removeAttr('readonly');\n";
	}

	$return_quantity = number_format($return_quantity,2,'.','');
	$yet_to_issue=$receive_quantity-$return_quantity;

	$yet_to_issue = number_format($yet_to_issue,2,'.','');
	$global_ref_stock = number_format($global_ref_stock,2,'.','');

	echo "$('#cbo_uom').val('".$uom."');\n";
	echo "$('#txt_color_name').val('".$color_arr[$color_id]."');\n";
	echo "$('#txt_color_id').val('".$color_id."');\n"; 
	echo "$('#txt_cumulative_issued').val('$return_quantity');\n";

	echo "$('#txt_yet_to_issue').val('$yet_to_issue');\n";
	echo "$('#txt_global_stock').val('".$global_ref_stock."');\n";

	/*echo "$('#txt_total_receive').val('".$data_ref[3]."');\n";
	echo "$('#txt_total_issue').val('".$data_ref[4]."');\n";*/

	echo "$('#cbo_store_name').attr('disabled','disabled');\n";
	echo "$('#cbo_floor').attr('disabled','disabled');\n";
	echo "$('#cbo_room').attr('disabled','disabled');\n";
	echo "$('#txt_rack').attr('disabled','disabled');\n";
	echo "$('#txt_shelf').attr('disabled','disabled');\n";
	echo "set_button_status(0, permission, 'fnc_yarn_receive_return_entry',1,1);\n";

	exit();
}

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
						$cumu_iss_data_arr = sql_select("select d.po_breakdown_id, d.roll_id, d.qnty from inv_mrr_wise_issue_details b, order_wise_pro_details c, pro_roll_details d where b.issue_trans_id=c.trans_id and c.trans_id=d.dtls_id and c.status_active=1 and b.recv_trans_id='$hidden_receive_trans_id' and c.entry_form=287 and d.entry_form=287 group by d.po_breakdown_id, d.roll_id, d.qnty");
						foreach($cumu_iss_data_arr as $rowR)
						{
							$cumu_iss_arr[$rowR[csf('po_breakdown_id')]][$rowR[csf('roll_id')]]+=$rowR[csf('qnty')];
						}
					}
					else
					{
						$return_sql = "select c.po_breakdown_id, d.id as trans_id, sum(c.quantity) as return_qnty from inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c, inv_transaction d where a.id = d.mst_id and a.id = b.mst_id and b.id=c.dtls_id and c.trans_id=d.id and c.status_active=1 and c.entry_form in (287) and d.status_active=1 and d.is_deleted=0 and a.company_id=$cbo_company_id and d.store_id=$cbo_store_name and d.pi_wo_batch_no =$batch_id and a.received_id=$txt_received_id and d.prod_id=$txt_prod_id and b.body_part_id='$cbo_body_part' and b.fabric_shade='$txt_fabric_shade' and d.floor_id='$cbo_floor' and d.room='$cbo_room' and d.rack='$txt_rack' and d.self ='$txt_shelf' group by c.po_breakdown_id, d.id";

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
						$sql="select a.is_sales,c.id as roll_id, c.po_breakdown_id, c.roll_no, c.qnty as receive_qnty from inv_transaction b, order_wise_pro_details a, pro_roll_details c where b.id=a.trans_id and a.dtls_id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_id' and b.mst_id='$txt_received_id' and b.id='$hidden_receive_trans_id' and b.prod_id='$txt_prod_id' and a.entry_form in(7,225) and c.entry_form in(7,225) and a.trans_type in(1,4) and b.transaction_type in(1,4) group by a.is_sales,c.id, c.po_breakdown_id, c.roll_no, c.qnty";
					}
					else
					{
						$sql="select a.po_breakdown_id, sum(a.quantity) as receive_qnty from  order_wise_pro_details a, inv_transaction b , pro_finish_fabric_rcv_dtls c where a.trans_id=b.id and b.id=c.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_id' and b.mst_id='$txt_received_id' and b.prod_id='$txt_prod_id' and a.entry_form in(7,225) and b.pi_wo_batch_no =$batch_id and c.body_part_id='$cbo_body_part' and c.dia_width_type='$txt_dia_width_type' and c.fabric_shade='$txt_fabric_shade' and b.store_id=$cbo_store_name and b.floor_id='$cbo_floor' and b.room='$cbo_room' and b.rack='$txt_rack' and b.self ='$txt_shelf' and b.transaction_type in(1) and a.trans_type in(1) and a.is_sales=1 group by a.po_breakdown_id";
					}
					//echo $sql;
					$sql_result=sql_select($sql);
					$sales_arr=array();
					foreach($sql_result as $row)
					{
						$sales_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
					}

					if(!empty($sales_arr))
					{
						$po_no_arr = return_library_array("select id,job_no from fabric_sales_order_mst where id in(".implode(",",$sales_arr).")","id","job_no");

						$rcv_issret_sql = sql_select("select c.po_breakdown_id, sum(c.quantity) as quantity from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c,inv_transaction d  where a.id=b.mst_id and b.trans_id=c.trans_id and b.mst_id=d.mst_id and b.trans_id = d.id and d.item_category=2 and d.transaction_type in (1,4) and a.entry_form in (225,233) and c.entry_form in (225,233) and d.store_id=$cbo_store_name and a.company_id=$cbo_company_id and d.pi_wo_batch_no =$batch_id and d.prod_id=$txt_prod_id and b.body_part_id='$cbo_body_part' and b.fabric_shade='$txt_fabric_shade' and d.floor_id='$cbo_floor' and d.room='$cbo_room' and d.rack='$txt_rack' and d.self ='$txt_shelf' group by c.po_breakdown_id");

						foreach ($rcv_issret_sql as $row) 
						{
							$cumu_rec_qty[$row[csf('po_breakdown_id')]] += $row[csf('quantity')];
						}
						unset($rcv_issret_sql);
						$trans_in_sql = sql_select("SELECT d.po_breakdown_id, sum(c.cons_quantity) as quantity from  inv_item_transfer_dtls b, inv_transaction c, order_wise_pro_details d where b.trans_id=c.id and c.id = d.to_trans_id and d.entry_form =230 and c.transaction_type=5 and c.item_category=2 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.store_id=$cbo_store_name and c.pi_wo_batch_no=$batch_id and c.prod_id=$txt_prod_id and c.floor_id='$cbo_floor' and c.room='$cbo_room' and c.rack='$txt_rack' and c.self ='$txt_shelf' and b.fabric_shade='$txt_fabric_shade' and  b.body_part_id='$cbo_body_part' group by  d.po_breakdown_id");
						foreach ($trans_in_sql as $row) 
						{
							$cumu_rec_qty[$row[csf('po_breakdown_id')]] += $row[csf('quantity')];
						}
						unset($trans_in_sql);

						$iss_rcvret_sql = sql_select("select c.po_breakdown_id, c.entry_form, sum(c.quantity) as quantity from inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c, inv_transaction d where a.id=b.mst_id and b.id=c.dtls_id and b.trans_id=d.id and c.entry_form in (287,224) and a.entry_form in (287,224) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_id=$cbo_company_id and d.store_id=$cbo_store_name and d.pi_wo_batch_no =$batch_id and d.prod_id=$txt_prod_id and b.body_part_id='$cbo_body_part' and b.fabric_shade='$txt_fabric_shade' and b.store_id=$cbo_store_name and d.floor_id='$cbo_floor' and d.room='$cbo_room' and d.rack='$txt_rack' and d.self ='$txt_shelf' group by c.entry_form, c.po_breakdown_id");

						foreach ($iss_rcvret_sql as $row) 
						{
							$cumu_iss_qty[$row[csf('po_breakdown_id')]] += $row[csf('quantity')];
						}
						unset($iss_rcvret_sql);

						$trans_out_sql = sql_select("SELECT d.po_breakdown_id, sum(c.cons_quantity) as quantity from  inv_item_transfer_dtls b, inv_transaction c, order_wise_pro_details d where b.trans_id=c.id and c.id = d.trans_id and d.entry_form =230 and c.transaction_type=6 and c.item_category=2 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.store_id=$cbo_store_name and c.pi_wo_batch_no=$batch_id and c.prod_id=$txt_prod_id and c.floor_id='$cbo_floor' and c.room='$cbo_room' and c.rack='$txt_rack' and c.self ='$txt_shelf' and b.fabric_shade='$txt_fabric_shade' and  b.body_part_id='$cbo_body_part' group by  d.po_breakdown_id");
						foreach ($trans_out_sql as $row) 
						{
							$cumu_iss_qty[$row[csf('po_breakdown_id')]] += $row[csf('quantity')];
						}
						unset($trans_out_sql);
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

						//echo $row[csf("receive_qnty")]."- cumu return=".$cumu_return_arr[$row[csf("po_breakdown_id")]]."<br>";
						//echo 'cumu rcv='.$cumu_rec_qty[$row[csf('po_breakdown_id')]]."- cumu issue".$cumu_iss_qty[$row[csf('po_breakdown_id')]]."+ this return=".$this_trans_return_arr[$row[csf('po_breakdown_id')]];

						$cumul_balance=$cumu_rec_qty[$row[csf('po_breakdown_id')]]-$cumu_iss_qty[$row[csf('po_breakdown_id')]] +$this_trans_return_arr[$row[csf('po_breakdown_id')]];

						$cumul_balance = number_format($cumul_balance,2,'.','');
						$receive = number_format($receive,2,'.','');

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
								$balance_quantity += $show_return_balance;
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
	$cbo_room 			= str_replace("'", "", $cbo_room);
	$cbo_floor 			= str_replace("'", "", $cbo_floor);

	if($txt_fabric_shade==""){$txt_fabric_shade=0;}
	if($txt_rack==""){$txt_rack=0;}
	if($txt_shelf==""){$txt_shelf=0;}
	if($cbo_room==""){$cbo_room=0;}
	if($cbo_floor==""){$cbo_floor=0;}

	if(str_replace("'","",$txt_received_id)!="")
	{
		$sql_issue=sql_select("select knitting_source,knitting_company from inv_receive_master where id=$txt_received_id and status_active=1 and is_deleted=0");
		$knitting_source=$sql_issue[0][csf("knitting_source")];
		$knitting_company=$sql_issue[0][csf("knitting_company")];
	}

	$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id = $txt_prod_id and store_id=$cbo_store_name and status_active=1 and is_deleted=0 and transaction_type in (1,4,5)", "max_date");
	if($max_recv_date !="")
	{
		$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
		$return_date = date("Y-m-d", strtotime(str_replace("'","",$txt_return_date)));
		if ($return_date < $max_recv_date)
		{
			echo "20**Return Date Can not Be Less Than Last Receive Date Of This Lot";
			die;
		}
	}
	$variable_setting_production=return_field_value("fabric_roll_level","variable_settings_production","company_name=$cbo_company_id  and item_category_id=2 and variable_list=3 and status_active=1","fabric_roll_level");

	//==============================Balance Check Start=======================================

	if($operation ==1 || $operation==2)
	{
		$up_cond = " and c.id <>$update_details_id";
		$up_trans_cond = " and a.id<>$update_id";
		$up_trans_cond2 = " and d.id<>$update_id";
	}else{
		$up_cond="";
		$up_trans_cond="";
		$up_trans_cond2="";
	}

	$iss_rcvret_sql = sql_select("select a.received_id, a.entry_form, b.transaction_type, c.room,c.rack_no,c.shelf_no,c.floor,c.width_type, d.color,d.id as product_id, sum(b.cons_quantity) as qnty,c.batch_id, c.uom,c.fabric_shade, c.body_part_id
    from inv_issue_master a, inv_transaction b, inv_finish_fabric_issue_dtls c, product_details_master d
    where a.id=b.mst_id and a.id=c.mst_id and b.prod_id=d.id and b.id=c.trans_id and a.entry_form in (224,287) and b.status_active =1 and b.transaction_type in (2,3) and b.item_category =2 and a.status_active=1 and b.store_id=$cbo_store_name and b.prod_id=$txt_prod_id and b.pi_wo_batch_no=$hidden_batch_id and c.body_part_id=$cbo_body_part and c.fabric_shade=$txt_fabric_shade and b.floor_id=$cbo_floor and b.room=$cbo_room and b.rack=$txt_rack and b.self=$txt_shelf $up_cond
    group by a.received_id, a.entry_form, b.transaction_type, c.room,c.rack_no,c.shelf_no,c.floor,c.width_type, d.color, d.id,c.batch_id, c.uom, c.fabric_shade, c.body_part_id");

	foreach ($iss_rcvret_sql as $val) 
	{
		if($val[csf("entry_form")] == 224)
		{
			$issue_qnty_arr[$val[csf("batch_id")]][$val[csf("product_id")]][$val[csf("width_type")]][$val[csf("floor")]][$val[csf("room")]][$val[csf("rack_no")]][$val[csf("shelf_no")]][$val[csf("fabric_shade")]][$val[csf("body_part_id")]] += $val[csf("qnty")];
		}
		else
		{
			if($val[csf("received_id")] == str_replace("'", "", $txt_received_id))
			{
				$this_challan_return_arr[$val[csf("batch_id")]][$val[csf("product_id")]][$val[csf("width_type")]][$val[csf("floor")]][$val[csf("room")]][$val[csf("rack_no")]][$val[csf("shelf_no")]][$val[csf("fabric_shade")]][$val[csf("body_part_id")]] += $val[csf("qnty")];
			}

			$rcv_return_qnty_arr[$val[csf("batch_id")]][$val[csf("product_id")]][$val[csf("width_type")]][$val[csf("floor")]][$val[csf("room")]][$val[csf("rack_no")]][$val[csf("shelf_no")]][$val[csf("fabric_shade")]][$val[csf("body_part_id")]] += $val[csf("qnty")];
		}
	}
	unset($iss_rcvret_sql);

	$rcv_issret_sql = sql_select("select a.id as received_id, a.entry_form,d.transaction_type, c.id as product_id, b.batch_id, b.color_id, b.trans_id as tr_id, sum(d.cons_quantity) as qnty, b.rack_no, b.shelf_no,b.is_sales,b.order_id,d.cons_uom as uom, b.floor, b.room, b.dia_width_type, b.fabric_shade, b.body_part_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b, product_details_master c,inv_transaction d where a.id=b.mst_id and b.prod_id=c.id and b.mst_id=d.mst_id and b.trans_id = d.id and d.item_category=2 and d.transaction_type in (1,4) and a.entry_form in (225,233) and a.company_id=$cbo_company_id and d.store_id=$cbo_store_name and d.prod_id=$txt_prod_id and d.pi_wo_batch_no=$hidden_batch_id and b.body_part_id=$cbo_body_part and b.fabric_shade=$txt_fabric_shade and d.floor_id=$cbo_floor and d.room=$cbo_room and d.rack=$txt_rack and d.self=$txt_shelf group by a.id, a.entry_form, d.transaction_type, c.id, b.batch_id, b.color_id, b.trans_id, b.receive_qnty, b.rack_no, b.shelf_no,b.is_sales,b.order_id, d.cons_uom, b.floor, b.room, b.dia_width_type, b.fabric_shade, b.body_part_id");

	foreach ($rcv_issret_sql as $val) 
	{
		if($val[csf("entry_form")] == 225)
		{
			if($val[csf("received_id")]  == str_replace("'", "", $txt_received_id))
			{
				$this_challan_rcv_arr[$val[csf("batch_id")]][$val[csf("product_id")]][$val[csf("dia_width_type")]][$val[csf("floor")]][$val[csf("room")]][$val[csf("rack_no")]][$val[csf("shelf_no")]][$val[csf("fabric_shade")]][$val[csf("body_part_id")]] += $val[csf("qnty")];
			}

			$rcv_qnty_arr[$val[csf("batch_id")]][$val[csf("product_id")]][$val[csf("dia_width_type")]][$val[csf("floor")]][$val[csf("room")]][$val[csf("rack_no")]][$val[csf("shelf_no")]][$val[csf("fabric_shade")]][$val[csf("body_part_id")]] += $val[csf("qnty")];
		}
		else
		{
			$issue_return_qnty_arr[$val[csf("batch_id")]][$val[csf("product_id")]][$val[csf("dia_width_type")]][$val[csf("floor")]][$val[csf("room")]][$val[csf("rack_no")]][$val[csf("shelf_no")]][$val[csf("fabric_shade")]][$val[csf("body_part_id")]] += $val[csf("qnty")];
		}
	}
	unset($rcv_issret_sql);

	$trans_out_sql = "SELECT sum(c.cons_quantity) as quantity, c.pi_wo_batch_no, c.prod_id, c.cons_uom, b.dia_width_type, d.color, c.floor_id, c.room, c.rack, c.self, b.fabric_shade, b.body_part_id
    from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c, product_details_master d
    where a.id=b.mst_id and b.trans_id=c.id and a.entry_form =230 and c.transaction_type=6 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.prod_id=d.id and c.store_id=$cbo_store_name and c.prod_id=$txt_prod_id and c.pi_wo_batch_no=$hidden_batch_id and b.body_part_id=$cbo_body_part and b.fabric_shade='$txt_fabric_shade' and c.floor_id=$cbo_floor and c.room=$cbo_room and c.rack=$txt_rack and c.self=$txt_shelf
 	group by c.pi_wo_batch_no, c.prod_id, c.cons_uom, b.dia_width_type, d.color, c.floor_id, c.room, c.rack, c.self, b.fabric_shade, b.body_part_id";

	$trans_out_Data = sql_select($trans_out_sql);
	$sql_floor_id=$sql_room_id=$sql_rack_id=$sql_shelf_id=0;
	foreach ($trans_out_Data as $val) 
	{
		if($val[csf("floor_id")]=="") $sql_floor_id = 0; else $sql_floor_id = $val[csf("floor_id")];
		if($val[csf("room")]=="") $sql_room_id = 0; else $sql_room_id = $val[csf("room")];
		if($val[csf("rack")]=="") $sql_rack_id = 0; else $sql_rack_id = $val[csf("rack")];
		if($val[csf("self")]=="") $sql_shelf_id = 0; else $sql_shelf_id = $val[csf("self")];

		$trans_out_arr[$val[csf("pi_wo_batch_no")]][$val[csf("prod_id")]][$val[csf("dia_width_type")]][$sql_floor_id][$sql_room_id][$sql_rack_id][$sql_shelf_id][$val[csf("fabric_shade")]][$val[csf("body_part_id")]] += $val[csf("quantity")];
	}
	unset($trans_out_Data);


	$trans_in_sql = "SELECT sum(c.cons_quantity) as quantity, c.pi_wo_batch_no, c.prod_id, c.cons_uom, b.dia_width_type, d.color, c.floor_id, c.room, c.rack, c.self, b.fabric_shade, b.body_part_id
    from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c, product_details_master d
    where a.id=b.mst_id and b.to_trans_id=c.id and a.entry_form =230 and c.transaction_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.prod_id=d.id and c.store_id=$cbo_store_name and c.prod_id=$txt_prod_id and c.pi_wo_batch_no=$hidden_batch_id and b.body_part_id=$cbo_body_part and b.fabric_shade='$txt_fabric_shade' and c.floor_id=$cbo_floor and c.room=$cbo_room and c.rack=$txt_rack and c.self=$txt_shelf
 	group by c.pi_wo_batch_no, c.prod_id, c.cons_uom, b.dia_width_type, d.color, c.floor_id, c.room, c.rack, c.self, b.fabric_shade, b.body_part_id";

	$trans_in_Data = sql_select($trans_in_sql);
	$sql_floor_id=$sql_room_id=$sql_rack_id=$sql_shelf_id=0;
	foreach ($trans_in_Data as $val) 
	{
		if($val[csf("floor_id")]=="") $sql_floor_id = 0; else $sql_floor_id = $val[csf("floor_id")];
		if($val[csf("room")]=="") $sql_room_id = 0; else $sql_room_id = $val[csf("room")];
		if($val[csf("rack")]=="") $sql_rack_id = 0; else $sql_rack_id = $val[csf("rack")];
		if($val[csf("self")]=="") $sql_shelf_id = 0; else $sql_shelf_id = $val[csf("self")];

		$trans_in_arr[$val[csf("pi_wo_batch_no")]][$val[csf("prod_id")]][$val[csf("dia_width_type")]][$sql_floor_id][$sql_room_id][$sql_rack_id][$sql_shelf_id][$val[csf("fabric_shade")]][$val[csf("body_part_id")]] += $val[csf("quantity")];
	}
	unset($trans_in_Data);

	$rcv_quantity = $rcv_qnty_arr[str_replace("'", "", $hidden_batch_id)][str_replace("'", "", $txt_prod_id)][str_replace("'", "", $txt_dia_width_type)][$cbo_floor][$cbo_room][$txt_rack][$txt_shelf][str_replace("'", "", $txt_fabric_shade)][str_replace("'", "", $cbo_body_part)];

	$issue_return_quantity =$issue_return_qnty_arr[str_replace("'", "", $hidden_batch_id)][str_replace("'", "", $txt_prod_id)][str_replace("'", "", $txt_dia_width_type)][$cbo_floor][$cbo_room][$txt_rack][$txt_shelf][str_replace("'", "", $txt_fabric_shade)][str_replace("'", "", $cbo_body_part)];

	$trans_in_quantity = $trans_in_arr[str_replace("'", "", $hidden_batch_id)][str_replace("'", "", $txt_prod_id)][str_replace("'", "", $txt_dia_width_type)][$cbo_floor][$cbo_room][$txt_rack][$txt_shelf][str_replace("'", "", $txt_fabric_shade)][str_replace("'", "", $cbo_body_part)];

	$issue_quantity = $issue_qnty_arr[str_replace("'", "", $hidden_batch_id)][str_replace("'", "", $txt_prod_id)][str_replace("'", "", $txt_dia_width_type)][$cbo_floor][$cbo_room][$txt_rack][$txt_shelf][str_replace("'", "", $txt_fabric_shade)][str_replace("'", "", $cbo_body_part)];

	$return_quantity = $rcv_return_qnty_arr[str_replace("'", "", $hidden_batch_id)][str_replace("'", "", $txt_prod_id)][str_replace("'", "", $txt_dia_width_type)][$cbo_floor][$cbo_room][$txt_rack][$txt_shelf][str_replace("'", "", $txt_fabric_shade)][str_replace("'", "", $cbo_body_part)];

	$trans_out_qnty = $trans_out_arr[str_replace("'", "", $hidden_batch_id)][str_replace("'", "", $txt_prod_id)][str_replace("'", "", $txt_dia_width_type)][$cbo_floor][$cbo_room][$txt_rack][$txt_shelf][str_replace("'", "", $txt_fabric_shade)][str_replace("'", "", $cbo_body_part)];

	$global_ref_stock = ($rcv_quantity + $trans_in_quantity + $issue_return_quantity) - ($issue_quantity + $trans_out_qnty + $return_quantity);
	
	// echo "30** ($rcv_quantity + $trans_in_quantity + $issue_return_quantity) - ($issue_quantity + $trans_out_qnty + $return_quantity)"; die;

	$this_challan_ret_qnty 	= $this_challan_return_arr[str_replace("'", "", $hidden_batch_id)][str_replace("'", "", $txt_prod_id)][str_replace("'", "", $txt_dia_width_type)][$cbo_floor][$cbo_room][$txt_rack][$txt_shelf][str_replace("'", "", $txt_fabric_shade)][str_replace("'", "", $cbo_body_part)];

	$this_receive_mrr_qnty 	= $this_challan_rcv_arr[str_replace("'", "", $hidden_batch_id)][str_replace("'", "", $txt_prod_id)][str_replace("'", "", $txt_dia_width_type)][$cbo_floor][$cbo_room][$txt_rack][$txt_shelf][str_replace("'", "", $txt_fabric_shade)][str_replace("'", "", $cbo_body_part)];

	$balance_qnty = $this_receive_mrr_qnty-$this_challan_ret_qnty;
	//echo "30**$this_receive_mrr_qnty-$this_challan_ret_qnty";
	//die;


	$txt_return_qnty=str_replace("'","",$txt_return_qnty);
	if($txt_return_qnty>$global_ref_stock)
	{
		echo "30**Return quantity not allow over global stock.\nGlobal stock :$global_ref_stock";
		die;
	}
	
	if($txt_return_qnty > $balance_qnty)
	{
		echo "30**Return Quantity Can not be Greater Than MRR Balance Quantity.\nBalance :$balance_qnty";
		die;
	}

	//===============================Balance Check End==========================================


	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		//------------------------------Check Duplicate END---------------------------------------//
		/*$txt_return_qnty=str_replace("'","",$txt_return_qnty);
		$txt_global_stock=str_replace("'","",$txt_global_stock);
		if($txt_return_qnty>$txt_global_stock)
		{
			echo "30**Return Quantity Not Over Global Stock.";
			disconnect($con);die;
		}*/


		if(str_replace("'","",$issue_mst_id)!="")
		{
			$new_return_number[0] = str_replace("'","",$txt_return_no);
			$id=str_replace("'","",$issue_mst_id);
			//issue master table UPDATE here START----------------------//
			$field_array_mst="company_id*issue_date*received_id*received_mrr_no*pi_id*knit_dye_source*knit_dye_company*updated_by*update_date";
			$data_array_mst=$cbo_company_id."*".$txt_return_date."*".$txt_received_id."*".$txt_mrr_no."*".$pi_id."*".$knitting_source."*".$knitting_company."*'".$user_id."'*'".$pc_date_time."'";
		}
		else
		{
			//issue master table entry here START---------------------------------------//
			if($db_type==0) $year_cond="YEAR(insert_date)";
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later

			$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
			$new_return_number = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,str_replace("'","",$cbo_company_id),'FRR',287,date("Y",time())));

			$field_array_mst="id, issue_number_prefix, issue_number_prefix_num, issue_number, entry_form, item_category, company_id, issue_date, received_id, received_mrr_no, pi_id,knit_dye_source,knit_dye_company, inserted_by, insert_date";
			$data_array_mst="(".$id.",'".$new_return_number[1]."','".$new_return_number[2]."','".$new_return_number[0]."',287,2,".$cbo_company_id.",".$txt_return_date.",".$txt_received_id.",".$txt_mrr_no.",".$pi_id.",".$knitting_source.",".$knitting_company.",'".$user_id."','".$pc_date_time."')";
		}

		//transaction table insert here START--------------------------------//cbo_uom
		$txt_return_qnty = str_replace("'","",$txt_return_qnty);
		$txt_cons_rate = str_replace("'","",$txt_cons_rate);
		$txt_order_rate = str_replace("'","",$txt_order_rate);
		$txt_aop_rate = str_replace("'","",$txt_aop_rate);

		$cons_amount = $txt_return_qnty*$txt_cons_rate;
		$order_amount = $txt_return_qnty*$txt_order_rate;
		$aop_amount = $txt_return_qnty*$txt_aop_rate;

		$aop_amount = number_format($aop_amount,4,".","");

		$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$field_array_trans = "id,mst_id,company_id,prod_id,batch_id_from_fissuertn,pi_wo_batch_no,item_category,transaction_type,transaction_date,store_id,body_part_id,floor_id,room,rack,self,order_uom,order_qnty,cons_uom,cons_quantity,order_rate,cons_rate,order_amount,cons_amount,no_of_roll,remarks,inserted_by,insert_date,booking_no,fabric_shade";
		$data_array_trans = "(".$transactionID.",".$id.",".$cbo_company_id.",".$txt_prod_id.",".$hidden_batch_id.",".$hidden_batch_id.",2,3,".$txt_return_date.",".$cbo_store_name.",".$cbo_body_part.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_uom.",".$txt_return_qnty.",".$cbo_uom.",".$txt_return_qnty.",".$txt_order_rate.",".$txt_cons_rate.",".$order_amount.",".$cons_amount.",".$txt_roll.",".$txt_remarks.",'".$user_id."','".$pc_date_time."',".$txt_booking_no.",".$txt_fabric_shade.")";
		//transaction table insert here END ---------------------------------//

		//Issue Details Table Starts here
		$id_dtls=return_next_id_by_sequence( "INV_FIN_FAB_ISSUE_DTLS_PK_SEQ", "inv_finish_fabric_issue_dtls", $con) ;
		$field_array_dtls="id,mst_id,trans_id,batch_id,prod_id,uom,issue_qnty,fabric_shade,store_id,no_of_roll,body_part_id,rack_no,shelf_no,floor,room, order_id,inserted_by,insert_date,width_type,rate,rate_in_usd,booking_no,aop_rate,aop_amount";

		$data_array_dtls="(".$id_dtls.",".$id.",".$transactionID.",".$hidden_batch_id.",".$txt_prod_id.",".$cbo_uom.",".$txt_return_qnty.",".$txt_fabric_shade.",".$cbo_store_name.",".$txt_roll.",".$cbo_body_part.",".$txt_rack.",".$txt_shelf.",".$cbo_floor.",".$cbo_room.",".$txt_order_id_all.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_dia_width_type.",".$txt_cons_rate.",".$txt_order_rate.",".$txt_booking_no.",'".$txt_aop_rate."','".$aop_amount."')";
		//Issue Details Table Ends here


		//adjust product master table START-------------------------------------//
		$sql = sql_select("select product_name_details,last_purchased_qnty,current_stock, stock_value, color from product_details_master where id=$txt_prod_id");
		$presentStock=$available_qnty=0;$color_id=0;
		$product_name_details="";
		foreach($sql as $result)
		{
			$presentStock			= $result[csf("current_stock")];
			$product_name_details 	= $result[csf("product_name_details")];
			$available_qnty			= $result[csf("available_qnty")];
			$color_id				= $result[csf("color")];
			$stock_value			= $result[csf("stock_value")];
		}
		$nowStock 		= $presentStock-$txt_return_qnty;
		$available_qnty = $available_qnty-$txt_return_qnty;

		$nowStockValue = $stock_value-$cons_amount;

		if($nowStock >0 ){
			$nowStockRate = $nowStockValue/$nowStock;
		}else{
			$nowStockRate = 0;
			$nowStockValue=0;
		}


		$field_array_prod="last_issued_qnty*current_stock*available_qnty*stock_value*avg_rate_per_unit*updated_by*update_date";
		$data_array_prod=$txt_return_qnty."*".$nowStock."*".$available_qnty."*'".$nowStockValue."'*'".$nowStockRate."'*'".$user_id."'*'".$pc_date_time."'";

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
						$data_array_roll.="(".$roll_id.",".$id.",".$transactionID.",".$po_id.",287,'".$roll_no."','".$rollId."',".$qty.",'".$user_id."','".$pc_date_time."')";

						$order_array[$po_id]+=$qty;
					}
				}

				foreach($order_array as $po_id=>$po_qty)
				{
					$proportion_id = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					if($data_array_proportion!="") $data_array_proportion.=", ";
					$data_array_proportion.="(".$proportion_id.",".$transactionID.",3,287,".$po_id.",".$txt_prod_id.",".$po_qty.",".$color_id.",'".$user_id."','".$pc_date_time."',".$txt_is_sales.")";
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
						$proportion_id = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
						if($data_array_proportion!="") $data_array_proportion.=", ";
						$data_array_proportion.="(".$proportion_id.",".$transactionID.",".$id_dtls.",3,287,".$order_qnty_arr[0].",".$txt_prod_id.",".$order_qnty_arr[1].",".$color_id.",'".$user_id."','".$pc_date_time."',".$txt_is_sales.")";
					}
				}
			}
		}


		$rID=$transID=$detailsID=$prodUpdate=$propoId=$rollId=$mrrWiseIssueID=$upTrID=true;
		if(str_replace("'","",$txt_return_no)!="")
		{
			$rID=sql_update("inv_issue_master",$field_array_mst,$data_array_mst,"id",$id,1);
		}
		else
		{
			$rID=sql_insert("inv_issue_master",$field_array_mst,$data_array_mst,1);
		}

		$transID = sql_insert("inv_transaction",$field_array_trans,$data_array_trans,1);
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

		/*echo "10**insert into inv_finish_fabric_issue_dtls (".$field_array_dtls.") values ".$data_array_dtls."<br>";
		echo "10**$rID && $transID && $detailsID && $prodUpdate && $propoId && $rollId && $mrrWiseIssueID && $upTrID";
		oci_rollback($con);
		disconnect($con);
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

		/*if($txt_return_qnty>($txt_global_stock+$prev_return_qnty))
		{
			echo "30**Return Quantity Not Over Global Stock.";
			disconnect($con);die;
		}*/


		//****************************************** BEFORE ENTRY ADJUST START *****************************************//
		//product master table information
		//before stock update
		$sql = sql_select( "select a.id,a.current_stock, b.cons_quantity, b.cons_amount, a.stock_value from product_details_master a, inv_transaction b where a.id=b.prod_id and a.id=$before_prod_id and b.id=$update_id and a.item_category_id=2 and b.item_category=2 and b.transaction_type=3" );
		$before_prod_id=$before_issue_qnty=$before_stock_qnty=0;
		foreach($sql as $result)
		{
			$before_prod_id 	= $result[csf("id")];
			$before_stock_qnty 	= $result[csf("current_stock")];
			//before quantity and stock value
			$before_issue_qnty	= $result[csf("cons_quantity")];
			$before_stock_value	= $result[csf("stock_value")];
			$before_issue_amount	= $result[csf("cons_amount")];
		}

		//current product ID
		$txt_prod_id = str_replace("'","",$txt_prod_id);
		$txt_return_qnty = str_replace("'","",$txt_return_qnty);
		$before_prod_id= str_replace("'","",$before_prod_id);
		//$curr_stock_qnty=return_field_value("current_stock","product_details_master","id=$txt_prod_id and item_category_id=2");


		$sql = sql_select( "select a.id,a.current_stock, a.stock_value from product_details_master a where a.id=$txt_prod_id and a.item_category_id=2" );
		$curr_stock_qnty= $sql[0][csf("current_stock")];
		$curr_stock_value=$sql[0][csf("stock_value")];


		$receive_purpose=return_field_value("receive_purpose","inv_receive_master","id=$txt_received_id");


		$txt_return_qnty = str_replace("'","",$txt_return_qnty);
		$txt_cons_rate = str_replace("'","",$txt_cons_rate);
		$txt_order_rate = str_replace("'","",$txt_order_rate);
		$txt_aop_rate = str_replace("'","",$txt_aop_rate);

		$cons_amount = $txt_return_qnty*$txt_cons_rate;
		$order_amount = $txt_return_qnty*$txt_order_rate;
		$aop_amount = $txt_return_qnty*$txt_aop_rate;
		$aop_amount = number_format($aop_amount,4,".","");


 		//echo $receive_purpose;die;
		//weighted and average rate START here------------------------//
		//product master table data UPDATE START----------------------//
		$update_array_prod= "last_issued_qnty*current_stock*stock_value*avg_rate_per_unit*updated_by*update_date";
		if($before_prod_id==$txt_prod_id)
		{
			$adj_stock_qnty = $curr_stock_qnty+$before_issue_qnty-$txt_return_qnty; // CurrentStock + Before Issue Qnty - Current Issue Qnty

			$adj_stock_value = $curr_stock_value + $before_issue_amount - $cons_amount;

			if($adj_stock_qnty > 0){
				$adj_stock_rate = $adj_stock_value/$adj_stock_qnty;
			}else{
				$adj_stock_rate=0;
				$adj_stock_value=0;
			}

			if($adj_stock_qnty<0) //Aziz
			{
				echo "30**Stock cannot be less than zero.";disconnect($con);die;
			}

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

			$adj_before_stock_value = $before_stock_value + $before_issue_amount;

			if($adj_before_stock_qnty >0){
				$adj_before_stock_rate = $adj_before_stock_value/$adj_before_stock_qnty;
			}else{
				$adj_before_stock_rate=0;
				$adj_before_stock_value=0;
			}


			 if($adj_before_stock_qnty<0) //Aziz
			 {
			 	echo "30**Stock cannot be less than zero.";disconnect($con);die;
			 }


			 $updateIdprod_array[]=$before_prod_id;
			 $update_dataProd[$before_prod_id]=explode("*",("".$prev_return_qnty."*".$adj_before_stock_qnty."*'".$adj_before_stock_value."'*'".$adj_before_stock_rate."'*'".$user_id."'*'".$pc_date_time."'"));

			//current product adjust
			$adj_curr_stock_qnty = 	$curr_stock_qnty-$txt_return_qnty; // CurrentStock + Before Issue Qnty

			$adj_curr_stock_value = $curr_stock_value - $cons_amount;
			if($adj_curr_stock_qnty > 0){
				$adj_curr_stock_rate = $adj_curr_stock_value/$adj_curr_stock_qnty;
			}else{
				$adj_curr_stock_rate=0;
				$adj_curr_stock_value=0;
			}

			$updateIdprod_array[]=$txt_prod_id;
			$update_dataProd[$txt_prod_id]=explode("*",("".$txt_return_qnty."*".$adj_curr_stock_qnty."*'".$adj_curr_stock_value."'*'".$adj_curr_stock_rate."'*'".$user_id."'*'".$pc_date_time."'"));
			//$query1=execute_query(bulk_update_sql_statement("product_details_master","id",$update_array_prod,$update_dataProd,$updateIdprod_array));

			//now current stock
			$curr_stock_qnty 	= $adj_curr_stock_qnty;
		}



		//****************************************** BEFORE ENTRY ADJUST END *****************************************//

		


		$id=$issue_mst_id;
		//yarn master table UPDATE here START----------------------//
		$field_array_mst="company_id*issue_date*received_id*pi_id*received_mrr_no*knit_dye_source*knit_dye_company*updated_by*update_date";
		$data_array_mst=$cbo_company_id."*".$txt_return_date."*".$txt_received_id."*".$pi_id."*".$txt_mrr_no."*".$knitting_source."*".$knitting_company."*'".$user_id."'*'".$pc_date_time."'";

		$field_array_trans="company_id*prod_id*batch_id_from_fissuertn*pi_wo_batch_no*item_category*transaction_type*transaction_date*store_id*body_part_id*floor_id*room*rack*self*order_uom*order_qnty*cons_uom*cons_quantity*order_rate*cons_rate*order_amount*cons_amount*no_of_roll*remarks*updated_by*update_date*booking_no*fabric_shade";
		$data_array_trans= "".$cbo_company_id."*".$txt_prod_id."*".$hidden_batch_id."*".$hidden_batch_id."*2*3*".$txt_return_date."*".$cbo_store_name."*".$cbo_body_part."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_uom."*".$txt_return_qnty."*".$cbo_uom."*".$txt_return_qnty."*".$txt_order_rate."*".$txt_cons_rate."*".$order_amount."*".$cons_amount."*".$txt_roll."*".$txt_remarks."*'".$user_id."'*'".$pc_date_time."'*".$txt_booking_no."*".$txt_fabric_shade;
		//echo "10**".$field_array_trans."<br>".$data_array_trans;die;


		$field_array_dtls="batch_id*prod_id*uom*issue_qnty*fabric_shade*store_id*no_of_roll*body_part_id*rack_no*shelf_no*floor*room*updated_by*update_date*width_type*rate*rate_in_usd*booking_no*aop_rate*aop_amount";
		//$hidden_fabric_rate=str_replace("'","",$hidden_fabric_rate);
		$data_array_dtls=$hidden_batch_id."*".$txt_prod_id."*".$cbo_uom."*".$txt_return_qnty."*".$txt_fabric_shade."*".$cbo_store_name."*".$txt_roll."*".$cbo_body_part."*".$txt_rack."*".$txt_shelf."*".$cbo_floor."*".$cbo_room."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_dia_width_type."*".$txt_cons_rate."*".$txt_order_rate."*".$txt_booking_no."*'".$txt_aop_rate."'*'".$aop_amount."'";


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
			//$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);
			$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
			$field_array_mrr = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,inserted_by,insert_date";
			$data_array_mrr = "(".$mrrWiseIsID.",".$hidden_receive_trans_id.",".$update_id.",287,".$txt_prod_id.",".$txt_return_qnty.",'".$user_id."','".$pc_date_time."')";
		*/

		//order_wise_pro_detail table insert here

		$txt_break_qnty=str_replace("'","",$txt_break_qnty);
		$txt_break_roll=str_replace("'","",$txt_break_roll);
		$txt_order_id_all=str_replace("'","",$txt_order_id_all);
		$ordr_wise_rtn_qnty_arr=explode("_",$txt_break_qnty);
		$ordr_wise_rtn_roll_arr=explode("_",$txt_break_roll);
		$ordr_id_arr=explode(",",$txt_order_id_all);


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
						$data_array_roll.="(".$roll_id.",".$id.",".$update_id.",".$po_id.",287,'".$roll_no."','".$rollId."',".$qty.",'".$user_id."','".$pc_date_time."')";
						//$roll_id++;

						$order_array[$po_id]+=$qty;
					}
				}

				foreach($order_array as $po_id=>$po_qty)
				{
					$proportion_id = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					if($data_array_proportion!="") $data_array_proportion.=", ";
					$data_array_proportion.="(".$proportion_id.",".$update_id.",3,287,".$po_id.",".$txt_prod_id.",".$po_qty.",".$color_id.",'".$user_id."','".$pc_date_time."',".$txt_is_sales.")";
					//$proportion_id++;
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
						$proportion_id = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
						if($data_array_proportion!="") $data_array_proportion.=", ";
						$data_array_proportion.="(".$proportion_id.",".$update_id.",".$update_details_id.",3,287,".$order_qnty_arr[0].",".$txt_prod_id.",".$order_qnty_arr[1].",".$color_id.",'".$user_id."','".$pc_date_time."',".$txt_is_sales.")";
						//$proportion_id++;
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
				$query3 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_id and entry_form=287");
				$query2 = sql_update("inv_transaction",$update_array_trans,$update_data_trans,"id",$hidden_receive_trans_id,1);
				$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
			}*/

			if($data_array_proportion!="")
			{
				$query4 = execute_query("DELETE FROM order_wise_pro_details WHERE trans_id=$update_id and entry_form=287");
				$propoId=sql_insert("order_wise_pro_details",$field_array_proportion,$data_array_proportion,1);
				if($variable_setting_production==1)
				{
					$query5 = execute_query("DELETE FROM pro_roll_details WHERE dtls_id=$update_id and entry_form=287");
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
				$query3 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_id and entry_form=287");
				$query2 = sql_update("inv_transaction",$update_array_trans,$update_data_trans,"id",$hidden_receive_trans_id,1);
				$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
			}
			if($before_receive_trans_id>0)
			{
				$upTrID = sql_update("inv_transaction",$update_array_trans,$update_data_before_trans,"id",$before_receive_trans_id,1);
			}*/

			if($data_array_proportion!="")
			{
				$query4 = execute_query("DELETE FROM order_wise_pro_details WHERE trans_id=$update_id and entry_form=287");
				$propoId=sql_insert("order_wise_pro_details",$field_array_proportion,$data_array_proportion,1);
				if($variable_setting_production==1)
				{
					$query5 = execute_query("DELETE FROM pro_roll_details WHERE dtls_id=$update_id and entry_form=287");
					$rollId=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,1);
				}
			}
			//mrr wise issue data insert here----------------------------//

		}

		
		/*echo "10**".$query1."&&".$query2."&&".$query3."&&".$query4 ."&&". $query5 ."&&".$rID."&&".$transID."&&".$detailsID."&&".$propoId."&&".$rollId."&&".$mrrWiseIssueID."&&".$upTrID;
		oci_rollback($con);
		disconnect($con);
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
			else
			{
				$sql = sql_select( "SELECT a.id,a.current_stock, a.stock_value, b.cons_quantity, b.cons_amount,  b.store_id from product_details_master a, inv_transaction b where a.id=b.prod_id and b.id=$update_trans_id and a.item_category_id=2 and b.item_category=2 and b.transaction_type=3" );
				// and a.id=$txt_prod_id
				if (empty($sql)) 
				{
					echo "30**Delete Failed";
					disconnect($con);die;
				}
				$before_issue_qnty=$before_stock_qnty=0;
				foreach($sql as $result)
				{
					$before_prod_id 		= $result[csf("id")];
					$before_stock_qnty 		= $result[csf("current_stock")];
					$before_stock_value		= $result[csf("stock_value")];
					
					$before_issue_qnty		= $result[csf("cons_quantity")];
					$before_issue_amount	= $result[csf("cons_amount")];
					$before_store_id		= $result[csf("store_id")];
				}

				$max_trans_query = sql_select("SELECT max(id) as max_id from inv_transaction where prod_id=$before_prod_id and store_id=$before_store_id and item_category=2 and status_active=1");
				$max_trans_id = $max_trans_query[0][csf('max_id')];

				if($max_trans_id > str_replace("'", "", $update_trans_id))
				{
					echo "30**Next transaction found of this store and product. delete not allowed.";
					die;
				}

				$adj_stock_qnty				= $before_stock_qnty+$before_issue_qnty;
				$adj_stock_value			= $before_stock_value+$before_issue_amount;

				if($adj_stock_qnty>0){
					$adj_stock_rate	=$adj_stock_value/$adj_stock_qnty;
				}else{
					$adj_stock_rate=0;
					$adj_stock_value=0;
				}

				$field_array_product="last_issued_qnty*current_stock*stock_value*avg_rate_per_unit*updated_by*update_date";
				$data_array_product=$before_issue_qnty."*".number_format($adj_stock_qnty,$dec_place[4],'.','')."*'".$adj_stock_value."'*'".$adj_stock_rate."'*'".$user_id."'*'".$pc_date_time."'";


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
						$rID4=sql_update("pro_roll_details",$field_array_roll,$data_array_roll,"mst_id*dtls_id","$issue_mst_id*$update_trans_id",1);
						if($rID4) $flag=1; else $flag=0;
					}
					$field_array_prop="updated_by*update_date*status_active*is_deleted";
					$data_array_prop="'".$user_id."'*'".$pc_date_time."'*0*1";
					$rID5=sql_update("order_wise_pro_details",$field_array_prop,$data_array_prop,"dtls_id*trans_id*entry_form","$update_details_id*$update_trans_id*287",1);
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
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_return_search_list_view', 'search_div', 'finish_fab_receive_rtn_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
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
		where a.id=b.mst_id and b.batch_id_from_fissuertn=c.id and b.transaction_type=3 and a.status_active=1 and a.item_category=2 and b.item_category=2 and a.entry_form=287 $sql_cond
		group by a.id, a.issue_number_prefix_num, a.issue_number, a.company_id, a.supplier_id, a.issue_date, a.item_category, a.received_id, a.received_mrr_no, a.insert_date order by a.id";
	}
	else
	{
		$sql = "select a.id, to_char(a.insert_date,'YYYY') as year, a.issue_number_prefix_num, a.issue_number, a.company_id, a.supplier_id,a.issue_date, a.item_category, a.received_id,a.received_mrr_no, sum(b.cons_quantity)as cons_quantity, listagg(cast(c.batch_no as varchar(4000)), ',' ) within group(order by c.batch_no) as batch_no
		from inv_issue_master a, inv_transaction b, pro_batch_create_mst c
		where a.id=b.mst_id and b.batch_id_from_fissuertn=c.id and b.transaction_type=3 and a.status_active=1 and a.item_category=2 and b.item_category=2 and a.entry_form=287 $sql_cond
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
	where id='$data' and item_category=2 and entry_form=287";
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
		//right side list view
		//echo "show_list_view('".$row[csf("received_id")]."','show_product_listview','list_product_container','requires/finish_fab_receive_rtn_controller','');\n";
	}
	exit();
}



if($action=="show_dtls_list_view")
{

	$sql = "SELECT a.id as issue_id, a.issue_number, a.company_id, a.supplier_id, a.issue_date, a.item_category, a.received_id, a.received_mrr_no, b.id as trans_id, b.cons_quantity, b.cons_uom, b.cons_rate, b.cons_amount, c.product_name_details, c.id as prod_id, c.color as color_id
	from inv_issue_master a, inv_transaction b left join product_details_master c on b.prod_id=c.id
	where a.id=b.mst_id and b.item_category=2 and b.transaction_type=3 and a.id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
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
				<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("trans_id")];?>,<? echo $rcvQnty;?>,<? echo $row[csf("issue_id")];?>,<? echo $row[csf("received_id")];?>,<? echo $row[csf("company_id")];?>","child_form_input_data","requires/finish_fab_receive_rtn_controller")' style="cursor:pointer">
					<td><? echo $i; ?></td>
					<td><p><? echo $row[csf("issue_number")]; ?></p></td>
					<td><p><? echo change_date_format($row[csf("issue_date")],2); ?></p></td>
					<td align="center"><p><? echo $row[csf("prod_id")]; ?></p></td>
					<td ><p><? echo $row[csf("product_name_details")]; ?></p></td>
					<td ><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
					<td ><p><? echo $row[csf("received_mrr_no")]; ?></p></td>
					<td align="right"><p><? echo number_format($row[csf("cons_quantity")],2,'.',''); ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
			<tfoot>
				<th colspan="7">Total</th>
				<th><? echo number_format($rettotalQnty,2,'.',''); ?></th>
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

	//$sql = "select a.company_id,b.id as prod_id, b.product_name_details, b.current_stock, b.gsm, b.dia_width, b.color, a.id as tr_id, a.cons_uom, a.cons_quantity,a.cons_rate,a.cons_amount, a.order_rate, a.order_amount, a.batch_id_from_fissuertn, a.pi_wo_batch_no, a.remarks, a.store_id, a.body_part_id, a.floor_id, a.room, a.rack, a.self, a.no_of_roll,c.is_sales,c.po_breakdown_id, d.width_type, d.fabric_shade, d.id as details_id from inv_transaction a, order_wise_pro_details c, product_details_master b, inv_finish_fabric_issue_dtls d where a.id=$data and a.id = d.trans_id and a.status_active=1 and a.item_category=2 and a.transaction_type=3 and a.prod_id=b.id  and b.status_active=1 and a.id=c.trans_id and c.entry_form=287";

	$sql ="SELECT a.company_id,b.id as prod_id, b.product_name_details, b.current_stock, b.gsm, b.dia_width, b.color, a.id as tr_id, a.cons_uom, a.cons_quantity,a.cons_rate,a.cons_amount, a.order_rate, a.order_amount, a.batch_id_from_fissuertn, a.pi_wo_batch_no, a.remarks, a.store_id, a.body_part_id, a.floor_id, a.room, a.rack, a.self, a.no_of_roll,c.is_sales,c.po_breakdown_id, d.width_type, d.fabric_shade, d.id as details_id , e.batch_no, e.booking_no, d.aop_rate from inv_transaction a, order_wise_pro_details c, product_details_master b, inv_finish_fabric_issue_dtls d, pro_batch_create_mst e where a.id=$data and a.id=d.trans_id and a.id=c.trans_id  and a.prod_id=b.id and a.pi_wo_batch_no=e.id and a.status_active=1 and c.status_active=1 and a.item_category=2 and a.transaction_type=3 and c.entry_form=287";
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
		$order_rate = $row[csf("order_rate")];
		$cons_amount = $row[csf("cons_amount")];
		$order_amount = $row[csf("order_amount")];
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
		$details_id = $row[csf("details_id")];
		$batch_no = $row[csf("batch_no")];
		$booking_no = $row[csf("booking_no")];
		$aop_rate = $row[csf("aop_rate")];
	}

	$floor_room_rack_arr = return_library_array("select a.floor_room_rack_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id = b.floor_room_rack_dtls_id and a.company_id =$company_id and b.store_id=$store_id and a.status_active=1 and b.status_active=1 group by a.floor_room_rack_id, a.floor_room_rack_name","floor_room_rack_id","floor_room_rack_name");

	/*
		$return_sql = "select a.received_id, d.pi_wo_batch_no, d.prod_id, d.cons_uom, b.body_part_id, b.width_type, b.fabric_shade, d.floor_id, d.room, d.rack, d.self, sum(d.cons_quantity) as cumu_qnty from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction d where a.id=b.mst_id and b.trans_id = d.id and a.id = d.mst_id and a.entry_form in (287) and d.transaction_type=3 and d.item_category=2 and d.status_active=1 and d.is_deleted=0 and a.company_id=$company_id and d.store_id=$store_id and d.prod_id=$prod_id and d.pi_wo_batch_no=$pi_wo_batch_no and b.body_part_id=$body_part_id and b.fabric_shade='$fabric_shade' and d.id <> $tr_id
		group by a.received_id, d.pi_wo_batch_no, d.prod_id, d.cons_uom, d.floor_id, d.room, d.rack, d.self, b.fabric_shade, b.body_part_id, b.width_type";

		$return_data = sql_select($return_sql);
		foreach ($return_data as $row) 
		{
			if($row[csf("received_id")] == $received_id)
			{
				$this_challan_return_arr[$row[csf("pi_wo_batch_no")]][$row[csf("prod_id")]][$row[csf("width_type")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("fabric_shade")]][$row[csf("body_part_id")]] += $row[csf("cumu_qnty")];
			}
			
			$return_arr[$row[csf("pi_wo_batch_no")]][$row[csf("prod_id")]][$row[csf("width_type")]][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("fabric_shade")]][$row[csf("body_part_id")]] += $row[csf("cumu_qnty")];
		}
		unset($return_data);

	*/


	$iss_rcvret_sql = sql_select("select a.received_id, a.entry_form, b.transaction_type, c.room,c.rack_no,c.shelf_no,c.floor,c.width_type, d.color,d.id as product_id, sum(b.cons_quantity) as qnty,c.batch_id, c.uom,c.fabric_shade, c.body_part_id
    from inv_issue_master a, inv_transaction b, inv_finish_fabric_issue_dtls c, product_details_master d
    where a.id=b.mst_id and a.id=c.mst_id and b.prod_id=d.id and b.id=c.trans_id and a.entry_form in (224,287) and b.status_active =1 and b.transaction_type in (2,3) and b.item_category =2 and a.status_active=1 and b.store_id=$store_id and b.prod_id=$prod_id and b.pi_wo_batch_no=$pi_wo_batch_no and c.body_part_id=$body_part_id and c.fabric_shade='$fabric_shade' and b.id <> $tr_id
    group by a.received_id, a.entry_form, b.transaction_type, c.room,c.rack_no,c.shelf_no,c.floor,c.width_type, d.color, d.id,c.batch_id, c.uom, c.fabric_shade, c.body_part_id");

	foreach ($iss_rcvret_sql as $val) 
	{
		if($val[csf("entry_form")] == 224)
		{
			$issue_qnty_arr[$val[csf("batch_id")]][$val[csf("product_id")]][$val[csf("width_type")]][$val[csf("floor")]][$val[csf("room")]][$val[csf("rack_no")]][$val[csf("shelf_no")]][$val[csf("fabric_shade")]][$val[csf("body_part_id")]] += $val[csf("qnty")];
		}
		else
		{
			if($val[csf("received_id")] == $received_id)
			{
				$this_challan_return_arr[$val[csf("batch_id")]][$val[csf("product_id")]][$val[csf("width_type")]][$val[csf("floor")]][$val[csf("room")]][$val[csf("rack_no")]][$val[csf("shelf_no")]][$val[csf("fabric_shade")]][$val[csf("body_part_id")]] += $val[csf("qnty")];
			}

			$rcv_return_qnty_arr[$val[csf("batch_id")]][$val[csf("product_id")]][$val[csf("width_type")]][$val[csf("floor")]][$val[csf("room")]][$val[csf("rack_no")]][$val[csf("shelf_no")]][$val[csf("fabric_shade")]][$val[csf("body_part_id")]] += $val[csf("qnty")];
		}
	}

	$rcv_issret_sql = sql_select("select a.id as received_id, a.entry_form,d.transaction_type, c.id as product_id, b.batch_id, b.color_id, b.trans_id as tr_id, sum(d.cons_quantity) as qnty, b.rack_no, b.shelf_no,b.is_sales,b.order_id,d.cons_uom as uom, b.floor, b.room, b.dia_width_type, b.fabric_shade, b.body_part_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b, product_details_master c,inv_transaction d where a.id=b.mst_id and b.prod_id=c.id and b.mst_id=d.mst_id and b.trans_id = d.id and d.item_category=2 and d.transaction_type in (1,4) and a.entry_form in (225,233) and a.company_id=$company_id and d.store_id=$store_id and d.prod_id=$prod_id and d.pi_wo_batch_no=$pi_wo_batch_no and b.body_part_id=$body_part_id and b.fabric_shade='$fabric_shade' group by a.id, a.entry_form, d.transaction_type, c.id, b.batch_id, b.color_id, b.trans_id, b.receive_qnty, b.rack_no, b.shelf_no,b.is_sales,b.order_id, d.cons_uom, b.floor, b.room, b.dia_width_type, b.fabric_shade, b.body_part_id");

	foreach ($rcv_issret_sql as $val) 
	{
		if($val[csf("entry_form")] == 225)
		{
			if($val[csf("received_id")] == $received_id)
			{
				$this_challan_rcv_arr[$val[csf("batch_id")]][$val[csf("product_id")]][$val[csf("dia_width_type")]][$val[csf("floor")]][$val[csf("room")]][$val[csf("rack_no")]][$val[csf("shelf_no")]][$val[csf("fabric_shade")]][$val[csf("body_part_id")]] += $val[csf("qnty")];
			}

			$rcv_qnty_arr[$val[csf("batch_id")]][$val[csf("product_id")]][$val[csf("dia_width_type")]][$val[csf("floor")]][$val[csf("room")]][$val[csf("rack_no")]][$val[csf("shelf_no")]][$val[csf("fabric_shade")]][$val[csf("body_part_id")]] += $val[csf("qnty")];
		}
		else
		{
			$issue_return_qnty_arr[$val[csf("batch_id")]][$val[csf("product_id")]][$val[csf("dia_width_type")]][$val[csf("floor")]][$val[csf("room")]][$val[csf("rack_no")]][$val[csf("shelf_no")]][$val[csf("fabric_shade")]][$val[csf("body_part_id")]] += $val[csf("qnty")];
		}
	}
	/*echo "<pre>";
	print_r($this_challan_rcv_arr);
	echo "<br>";
	echo $this_challan_rcv_arr[$pi_wo_batch_no][$prod_id][$width_type][$floor_id][$room][$rack][$self][$fabric_shade][$body_part_id];
	die;*/

	$trans_out_sql = "SELECT sum(c.cons_quantity) as quantity, c.pi_wo_batch_no, c.prod_id, c.cons_uom, b.dia_width_type, d.color, c.floor_id, c.room, c.rack, c.self, b.fabric_shade, b.body_part_id
    from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c, product_details_master d
    where a.id=b.mst_id and b.trans_id=c.id and a.entry_form =230 and c.transaction_type=6 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.prod_id=d.id and c.store_id=$store_id and c.prod_id=$prod_id and c.pi_wo_batch_no=$pi_wo_batch_no and b.body_part_id=$body_part_id and b.fabric_shade='$fabric_shade'
 	group by c.pi_wo_batch_no, c.prod_id, c.cons_uom, b.dia_width_type, d.color, c.floor_id, c.room, c.rack, c.self, b.fabric_shade, b.body_part_id";

	$trans_out_Data = sql_select($trans_out_sql);
	$sql_floor_id=$sql_room_id=$sql_rack_id=$sql_shelf_id=0;
	foreach ($trans_out_Data as $val) 
	{
		if($val[csf("floor_id")]=="") $sql_floor_id = 0; else $sql_floor_id = $val[csf("floor_id")];
		if($val[csf("room")]=="") $sql_room_id = 0; else $sql_room_id = $val[csf("room")];
		if($val[csf("rack")]=="") $sql_rack_id = 0; else $sql_rack_id = $val[csf("rack")];
		if($val[csf("self")]=="") $sql_shelf_id = 0; else $sql_shelf_id = $val[csf("self")];

		$trans_out_arr[$val[csf("pi_wo_batch_no")]][$val[csf("prod_id")]][$val[csf("dia_width_type")]][$sql_floor_id][$sql_room_id][$sql_rack_id][$sql_shelf_id][$val[csf("fabric_shade")]][$val[csf("body_part_id")]] += $val[csf("quantity")];
	}
	unset($trans_out_Data);


	$trans_in_sql = "SELECT sum(c.cons_quantity) as quantity, c.pi_wo_batch_no, c.prod_id, c.cons_uom, b.dia_width_type, d.color, c.floor_id, c.room, c.rack, c.self, b.fabric_shade, b.body_part_id
    from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c, product_details_master d
    where a.id=b.mst_id and b.to_trans_id=c.id and a.entry_form =230 and c.transaction_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.prod_id=d.id and c.store_id=$store_id and c.prod_id=$prod_id and c.pi_wo_batch_no=$pi_wo_batch_no and b.body_part_id=$body_part_id and b.fabric_shade='$fabric_shade'
 	group by c.pi_wo_batch_no, c.prod_id, c.cons_uom, b.dia_width_type, d.color, c.floor_id, c.room, c.rack, c.self, b.fabric_shade, b.body_part_id";

	$trans_in_Data = sql_select($trans_in_sql);
	$sql_floor_id=$sql_room_id=$sql_rack_id=$sql_shelf_id=0;
	foreach ($trans_in_Data as $val) 
	{
		if($val[csf("floor_id")]=="") $sql_floor_id = 0; else $sql_floor_id = $val[csf("floor_id")];
		if($val[csf("room")]=="") $sql_room_id = 0; else $sql_room_id = $val[csf("room")];
		if($val[csf("rack")]=="") $sql_rack_id = 0; else $sql_rack_id = $val[csf("rack")];
		if($val[csf("self")]=="") $sql_shelf_id = 0; else $sql_shelf_id = $val[csf("self")];

		$trans_in_arr[$val[csf("pi_wo_batch_no")]][$val[csf("prod_id")]][$val[csf("dia_width_type")]][$sql_floor_id][$sql_room_id][$sql_rack_id][$sql_shelf_id][$val[csf("fabric_shade")]][$val[csf("body_part_id")]] += $val[csf("quantity")];
	}
	unset($trans_in_Data);



	//$rcv_qnty_arr[$val[csf("batch_id")]][$val[csf("product_id")]][$val[csf("dia_width_type")]][$val[csf("floor")]][$val[csf("room")]][$val[csf("rack_no")]][$val[csf("shelf_no")]][$val[csf("fabric_shade")]][$val[csf("body_part_id")]]


	$rcv_quantity = $rcv_qnty_arr[$pi_wo_batch_no][$prod_id][$width_type][$floor_id][$room][$rack][$self][$fabric_shade][$body_part_id];
	$issue_return_quantity =$issue_return_qnty_arr[$pi_wo_batch_no][$prod_id][$width_type][$floor_id][$room][$rack][$self][$fabric_shade][$body_part_id];
	$trans_in_quantity = $trans_in_arr[$pi_wo_batch_no][$prod_id][$width_type][$floor_id][$room][$rack][$self][$fabric_shade][$body_part_id];

	$issue_quantity = $issue_qnty_arr[$pi_wo_batch_no][$prod_id][$width_type][$floor_id][$room][$rack][$self][$fabric_shade][$body_part_id];
	$return_quantity = $rcv_return_qnty_arr[$pi_wo_batch_no][$prod_id][$width_type][$floor_id][$room][$rack][$self][$fabric_shade][$body_part_id];
	$trans_out_qnty = $trans_out_arr[$pi_wo_batch_no][$prod_id][$width_type][$floor_id][$room][$rack][$self][$fabric_shade][$body_part_id];

	$global_ref_stock = ($rcv_quantity + $trans_in_quantity + $issue_return_quantity) - ($issue_quantity + $trans_out_qnty + $return_quantity);

	//echo "(".$rcv_quantity ."+". $trans_in_quantity ."+". $issue_return_quantity.")" ."-(". $issue_quantity ."+". $trans_out_qnty ."+". $return_quantity.")";


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
	echo "$('#txt_order_rate').val('".$order_rate."');\n";
	echo "$('#txt_amount').val('".number_format($order_amount,2,".","")."');\n";
	echo "$('#txt_aop_rate').val('".$aop_rate."');\n";
	echo "$('#cbo_uom').val(".$cons_uom.");\n";
	echo "$('#txt_color_name').val('".$color_arr[$color]."');\n";
	echo "$('#txt_color_id').val('".$color."');\n";
	echo "$('#txt_roll').val('".$no_of_roll."');\n";
	echo "$('#txt_remarks').val('".$remarks."');\n";
	echo "$('#txt_is_sales').val('".$is_sales."');\n";
	echo "$('#hidden_batch_id').val(".$pi_wo_batch_no.");\n";

	echo "$('#txt_return_qnty').removeAttr('placeholder').removeAttr('readonly').removeAttr('onDblClick').attr('placeholder','Double Click To Search').attr('readonly','').attr('onDblClick','openmypage_rtn_qty();');\n";

	echo "$('#txt_return_qnty').val('".$cons_quantity."');\n";

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


	$receive_quantity = $this_challan_rcv_arr[$pi_wo_batch_no][$prod_id][$width_type][$floor_id][$room][$rack][$self][$fabric_shade][$body_part_id];
	$cumilitive_rtn = $this_challan_return_arr[$pi_wo_batch_no][$prod_id][$width_type][$floor_id][$room][$rack][$self][$fabric_shade][$body_part_id];

	$receive_quantity = number_format($receive_quantity,2,'.','');
	$cumilitive_rtn = number_format($cumilitive_rtn,2,'.','');
	
	$yet_to_iss=$receive_quantity-$cumilitive_rtn;
	$yet_to_iss = number_format($yet_to_iss,2,'.','');
	echo "$('#txt_fabric_received').val('$receive_quantity');\n";
	echo "$('#txt_cumulative_issued').val('$cumilitive_rtn');\n";
	echo "$('#txt_yet_to_issue').val('$yet_to_iss');\n";

	//Needtoidentify
	echo "$('#hidden_receive_trans_id').val('$recv_trans_id');\n";
	echo "$('#before_receive_trans_id').val('$recv_trans_id');\n";

	$global_ref_stock = number_format($global_ref_stock,2,'.','');
	echo "$('#txt_global_stock').val('".$global_ref_stock."');\n";
	echo "$('#update_id').val('".$tr_id."');\n";
	echo "$('#update_details_id').val('".$details_id."');\n";
	echo "$('#txt_booking_no').val('".$booking_no."');\n";

	echo "$('#cbo_store_name').attr('disabled','disabled');\n";
	echo "$('#cbo_floor').attr('disabled','disabled');\n";
	echo "$('#cbo_room').attr('disabled','disabled');\n";
	echo "$('#txt_rack').attr('disabled','disabled');\n";
	echo "$('#txt_shelf').attr('disabled','disabled');\n";
	echo "set_button_status(1, permission, 'fnc_yarn_receive_return_entry',1,1);\n";




	//=================================================================================================	
/*
	foreach($result as $row)
	{
		$variable_setting_production=return_field_value("fabric_roll_level","variable_settings_production","company_name='".$row[csf("company_id")]."'  and item_category_id=2 and variable_list=3 and status_active=1","fabric_roll_level");

		echo "$('#txt_item_description').val('".$row[csf("product_name_details")]."');\n";
		echo "$('#txt_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#txt_dia_width_type').val('".$row[csf("width_type")]."');\n";
		echo "$('#txt_fabric_shade').val('".$row[csf("fabric_shade")]."');\n";
		echo "$('#before_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#txt_gsm').val('".$row[csf("gsm")]."');\n";
		echo "$('#txt_dia').val('".$row[csf("dia_width")]."');\n";
		echo "$('#prev_return_qnty').val('".$row[csf("cons_quantity")]."');\n";


		echo "$('#txt_cons_rate').val('".$row[csf("cons_rate")]."');\n";
		echo "$('#txt_order_rate').val('".$row[csf("order_rate")]."');\n";
		echo "$('#txt_amount').val('".$row[csf("order_amount")]."');\n";


		echo "$('#cbo_uom').val(".$row[csf("cons_uom")].");\n";
		echo "$('#txt_color_name').val('".$color_arr[$row[csf("color")]]."');\n";
		echo "$('#txt_roll').val('".$row[csf("no_of_roll")]."');\n";
		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
		echo "$('#txt_is_sales').val('".$row[csf("is_sales")]."');\n";
		$recv_trans_id=return_field_value("recv_trans_id as recv_trans_id","inv_mrr_wise_issue_details","issue_trans_id='".$row[csf('tr_id')]."'","recv_trans_id" );
		if($recv_trans_id=="") $recv_trans_id=0;
		if($row[csf("batch_id_from_fissuertn")]>0)
		{
			$recv_batch_id=$row[csf("batch_id_from_fissuertn")];
			echo "$('#hidden_batch_id').val(".$row[csf("batch_id_from_fissuertn")].");\n";
		}
		else
		{
			$recv_batch_id=return_field_value("b.batch_id as batch_id","inv_mrr_wise_issue_details a, pro_finish_fabric_rcv_dtls b","a.recv_trans_id=b.trans_id and a.recv_trans_id='".$recv_trans_id."'","batch_id" );
			echo "$('#hidden_batch_id').val(".$recv_batch_id.");\n";
		}

		$order_id_string=return_field_value("order_id","pro_finish_fabric_rcv_dtls ","trans_id=$recv_trans_id","order_id" );
		if($order_id_string=="")
		{
			echo "$('#txt_return_qnty').removeAttr('placeholder').removeAttr('readonly').removeAttr('onDblClick').attr('placeholder','write');\n";
		}
		else
		{
			echo "$('#txt_return_qnty').removeAttr('placeholder').removeAttr('readonly').removeAttr('onDblClick').attr('placeholder','Double Click To Search').attr('readonly','').attr('onDblClick','openmypage_rtn_qty();');\n";
		}

		echo "$('#txt_return_qnty').val('".$row[csf("cons_quantity")]."');\n";

		$batch_no=return_field_value("batch_no","pro_batch_create_mst","id='".$recv_batch_id."'","batch_no" );
		echo "$('#txt_batch_no').val('".$batch_no."');\n";
		echo "$('#cbo_body_part').val('".$row[csf("body_part_id")]."');\n";

		echo "load_room_rack_self_bin('requires/finish_fab_receive_rtn_controller*2', 'store','store_td', '".$row[csf('company_id')]."','"."',this.value);\n";
		echo "$('#cbo_store_name').val('".$row[csf("store_id")]."');\n";

		echo "load_room_rack_self_bin('requires/finish_fab_receive_rtn_controller', 'floor','floor_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."',this.value);\n";
		echo "$('#cbo_floor').val('".$row[csf("floor_id")]."');\n";
		echo "load_room_rack_self_bin('requires/finish_fab_receive_rtn_controller', 'room','room_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."',this.value);\n";
		echo "$('#cbo_room').val('".$row[csf("room")]."');\n";
		echo "load_room_rack_self_bin('requires/finish_fab_receive_rtn_controller', 'rack','rack_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		echo "$('#txt_rack').val('".$row[csf("rack")]."');\n";
		echo "load_room_rack_self_bin('requires/finish_fab_receive_rtn_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";
		echo "$('#txt_shelf').val('".$row[csf("self")]."');\n";

		$receive_quantity=return_field_value("cons_quantity","inv_transaction ","id=$recv_trans_id","cons_quantity" );
		$cumilitive_rtn=return_field_value("sum(b.issue_qnty) as issue_qnty","inv_mrr_wise_issue_details b","b.status_active=1 and b.prod_id='".$row[csf("prod_id")]."' and b.recv_trans_id='$recv_trans_id'","issue_qnty" );

		$propotion_sql=sql_select("select po_breakdown_id, quantity from order_wise_pro_details where trans_id='".$row[csf("tr_id")]."'");
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
			$roll_sql=sql_select("select po_breakdown_id, roll_no, roll_id, qnty from  pro_roll_details where mst_id='$issue_id' and dtls_id='".$row[csf("tr_id")]."'");
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
		echo "$('#txt_global_stock').val('".$row[csf("current_stock")]."');\n";
		echo "$('#update_id').val('".$row[csf("tr_id")]."');\n";
		echo "$('#update_details_id').val('".$row[csf("details_id")]."');\n";
		if($row[csf("is_sales")]==1)
		{
			$bookingno=sql_select("select id,sales_booking_no from fabric_sales_order_mst where id in(".$row[csf("po_breakdown_id")].") and status_active=1 ");
			echo "$('#txt_booking_no').val('".$bookingno[0][csf("sales_booking_no")]."');\n";
		}
	}

	echo "$('#cbo_store_name').attr('disabled','disabled');\n";
	echo "$('#cbo_floor').attr('disabled','disabled');\n";
	echo "$('#cbo_room').attr('disabled','disabled');\n";
	echo "$('#txt_rack').attr('disabled','disabled');\n";
	echo "$('#txt_shelf').attr('disabled','disabled');\n";
	echo "set_button_status(1, permission, 'fnc_yarn_receive_return_entry',1,1);\n";



	//For balancing start ==============================
	$iss_rcvret_sql = sql_select("select a.entry_form, b.transaction_type, c.room,c.rack_no,c.shelf_no,c.floor,c.width_type, d.color,d.id as product_id, sum(b.cons_quantity) as qnty,c.batch_id, c.uom,c.fabric_shade
    from inv_issue_master a, inv_transaction b, inv_finish_fabric_issue_dtls c, product_details_master d
    where a.id = b.mst_id and a.id = c.mst_id and b.prod_id = d.id  and a.entry_form in (224,287) and b.id = c.trans_id and b.status_active =1 and b.transaction_type in (2,3) and b.item_category = 2 and a.status_active =1 and d.id = " .$result[0][csf("prod_id")] ." and c.batch_id = ".$result[0][csf("batch_id_from_fissuertn")]."
    group by a.entry_form, b.transaction_type, c.room,c.rack_no,c.shelf_no,c.floor,c.width_type, d.color, d.id,c.batch_id, c.uom,c.fabric_shade");

   
	foreach ($iss_rcvret_sql as $val) 
	{
		if($val[csf("entry_form")] == 224)
		{
			$issue_qnty_arr[$val[csf("batch_id")]][$val[csf("product_id")]][$val[csf("uom")]][$val[csf("width_type")]][$val[csf("color")]][$val[csf("floor")]][$val[csf("room")]][$val[csf("rack_no")]][$val[csf("shelf_no")]][$val[csf("fabric_shade")]] += $val[csf("qnty")];

		}
		else
		{
			$rcv_return_qnty_arr[$val[csf("batch_id")]][$val[csf("product_id")]][$val[csf("uom")]][$val[csf("width_type")]][$val[csf("color")]][$val[csf("floor")]][$val[csf("room")]][$val[csf("rack_no")]][$val[csf("shelf_no")]][$val[csf("fabric_shade")]] += $val[csf("qnty")];

		}
	}


	$rcv_issret_sql = sql_select("select a.entry_form,d.transaction_type, c.id as product_id, b.batch_id, b.color_id, b.trans_id as tr_id, sum(d.cons_quantity) as qnty, b.rack_no, b.shelf_no,b.is_sales,b.order_id,d.cons_rate, d.cons_uom as uom, b.fabric_shade, b.floor, b.room, b.dia_width_type,b.fabric_shade from inv_receive_master a, pro_finish_fabric_rcv_dtls b, product_details_master c,inv_transaction d where a.id=b.mst_id and b.prod_id=c.id and b.mst_id=d.mst_id and b.trans_id = d.id and d.item_category=2 and d.transaction_type in (1,4) and a.entry_form in (225,233) and c.id = " .$result[0][csf("prod_id")] ." and b.batch_id = ".$result[0][csf("batch_id_from_fissuertn")]." group by a.entry_form, d.transaction_type, c.id, b.batch_id, b.color_id, b.trans_id, b.receive_qnty, b.rack_no, b.shelf_no,b.is_sales,b.order_id, d.cons_rate, d.cons_uom, b.fabric_shade, b.floor, b.room, b.dia_width_type,b.fabric_shade");



	foreach ($rcv_issret_sql as $val) 
	{
		if($val[csf("entry_form")] == 225)
		{
			$rcv_qnty_arr[$val[csf("batch_id")]][$val[csf("product_id")]][$val[csf("uom")]][$val[csf("dia_width_type")]][$val[csf("color_id")]][$val[csf("floor")]][$val[csf("room")]][$val[csf("rack_no")]][$val[csf("shelf_no")]][$val[csf("fabric_shade")]] += $val[csf("qnty")];

		}
		else
		{
			$issue_return_qnty_arr[$val[csf("batch_id")]][$val[csf("product_id")]][$val[csf("uom")]][$val[csf("dia_width_type")]][$val[csf("color_id")]][$val[csf("floor")]][$val[csf("room")]][$val[csf("rack_no")]][$val[csf("shelf_no")]][$val[csf("fabric_shade")]] += $val[csf("qnty")];

		}
	}

	$sql = "select a.company_id,b.id as prod_id, b.product_name_details, b.current_stock, b.gsm, b.dia_width, b.color, a.id as tr_id, a.cons_uom, a.cons_quantity,a.cons_rate,a.cons_amount, a.batch_id_from_fissuertn, a.remarks,a.store_id,a.body_part_id, a.floor_id,a.room,a.rack, a.self, a.no_of_roll,c.is_sales,c.po_breakdown_id, d.width_type, d.fabric_shade, d.id as details_id from inv_transaction a,order_wise_pro_details c, product_details_master b, inv_finish_fabric_issue_dtls d where a.id=$data and a.id = d.trans_id and a.status_active=1 and a.item_category=2 and a.transaction_type=3 and a.prod_id=b.id  and b.status_active=1 and a.id=c.trans_id and c.entry_form=287";

	$rcv_qnty  = $rcv_qnty_arr[$result[0][csf("batch_id_from_fissuertn")]][$result[0][csf("prod_id")]][$result[0][csf("cons_uom")]][$result[0][csf("width_type")]][$result[0][csf("color")]][$result[0][csf("floor_id")]][$result[0][csf("room")]][$result[0][csf("rack")]][$result[0][csf("self")]][$result[0][csf("fabric_shade")]];
	$issue_return_qnty = $issue_return_qnty_arr[$result[0][csf("batch_id_from_fissuertn")]][$result[0][csf("prod_id")]][$result[0][csf("cons_uom")]][$result[0][csf("width_type")]][$result[0][csf("color")]][$result[0][csf("floor_id")]][$result[0][csf("room")]][$result[0][csf("rack")]][$result[0][csf("self")]][$result[0][csf("fabric_shade")]];

	$issue_qnty = $issue_qnty_arr[$result[0][csf("batch_id_from_fissuertn")]][$result[0][csf("prod_id")]][$result[0][csf("cons_uom")]][$result[0][csf("width_type")]][$result[0][csf("color")]][$result[0][csf("floor_id")]][$result[0][csf("room")]][$result[0][csf("rack")]][$result[0][csf("self")]][$result[0][csf("fabric_shade")]];
	$rcv_return_qnty = $rcv_return_qnty_arr[$result[0][csf("batch_id_from_fissuertn")]][$result[0][csf("prod_id")]][$result[0][csf("cons_uom")]][$result[0][csf("width_type")]][$result[0][csf("color")]][$result[0][csf("floor_id")]][$result[0][csf("room")]][$result[0][csf("rack")]][$result[0][csf("self")]][$result[0][csf("fabric_shade")]];

	$total_rcv = $rcv_qnty + $issue_return_qnty ;

	$total_issue = $issue_qnty + $rcv_return_qnty -$result[0][csf("cons_quantity")];

	$yet_to_issue = $total_rcv - $total_issue;

	

	echo "$('#txt_total_receive').val('$total_rcv');\n";
	echo "$('#txt_total_issue').val('$total_issue');\n";
	echo "$('#txt_yet_to_issue').val('$yet_to_issue');\n";

*/
	
	//For balancing sql end ==============================

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
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_wopi_search_list_view', 'search_div', 'finish_fab_receive_rtn_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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


if ($action=="yarn_receive_return_print") //Aziz--3-8-15---query not ok
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
	$sql=" select id, issue_number, received_id, issue_date, supplier_id from  inv_issue_master where id='$data[1]' and entry_form=287 and item_category=2 and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql);

	$sql_rretrn_to= sql_select("select id,recv_number,entry_form,company_id,receive_basis,receive_purpose,lc_no,knitting_source,knitting_company from inv_receive_master where id='".$dataArray[0][csf('received_id')]."' and entry_form in(7,225)");

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

	$sql_dtls = "select b.id as prod_id, b.product_name_details, a.id as tr_id, a.batch_id_from_fissuertn, a.store_id, a.cons_uom, a.cons_quantity, b.color as color_id, a.no_of_roll, a.remarks,d.po_breakdown_id,d.is_sales from inv_transaction a,order_wise_pro_details d, product_details_master b, inv_issue_master c where c.id=a.mst_id and a.prod_id=b.id and a.id=d.trans_id and a.status_active=1 and a.company_id='$data[0]' and c.id='$data[1]' and a.item_category=2 and transaction_type=3 and c.entry_form = 287 and d.entry_form=287 and b.status_active=1";
	$sql_result= sql_select($sql_dtls);
	$batch_id_arr=array();
	foreach($sql_result as $row)
	{
		$batch_id_arr[] = $row[csf("batch_id_from_fissuertn")];
		if($row[csf('is_sales')]==1){
			$sales_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
		}else{
			$po_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
		}
		$is_sales = $row[csf('is_sales')];
	}

	if(!empty($batch_id_arr)){
		$batch_arr=return_library_array("select id,batch_no from pro_batch_create_mst where id in(".implode(",",$batch_id_arr).")","id","batch_no");
	}

	if($is_sales==1){
		if(!empty($sales_arr)){
			$sql_buyer_style=sql_select("select c.buyer_name, c.style_ref_no,d.sales_booking_no,d.job_no from fabric_sales_order_mst d,wo_booking_dtls a, wo_po_break_down b, wo_po_details_master c where d.id in(".implode(",",$sales_arr).") and d.sales_booking_no=a.booking_no and a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and b.status_active=1 and c.status_active=1 group by c.buyer_name, c.style_ref_no,d.sales_booking_no,d.job_no");

			$buyer_name=$style_ref="";
			foreach($sql_buyer_style as $row)
			{
				$buyer_name.=$buyer_arr[$row[csf("buyer_name")]]." , ";
				$style_ref.=$row[csf("style_ref_no")]." , ";
				$bookingNo =$row[csf("sales_booking_no")];
				$fsoNo = $row[csf("job_no")];
			}
		}
	}else{
		if(!empty($po_arr)){
			$sql_buyer_style=sql_select("select c.buyer_name, c.style_ref_no,a.booking_no from wo_booking_dtls a, wo_po_break_down b, wo_po_details_master c where a.id in(".implode(",",$po_arr).") and a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and b.status_active=1 and c.status_active=1 group by c.buyer_name, c.style_ref_no,a.booking_no ");

			$buyer_name=$style_ref="";
			foreach($sql_buyer_style as $row)
			{
				$buyer_name.=$buyer_arr[$row[csf("buyer_name")]]." , ";
				$style_ref.=$row[csf("style_ref_no")]." , ";
				$bookingNo = $row[csf("booking_no")];
			}
		}
	}
	$buyer_name=chop($buyer_name, " , ");
	$style_ref=chop($style_ref, " , ");
	?>
	<div style="width:930px;">
		<table width="930" cellspacing="0" align="right">
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
				<td ><strong>FSO No:</strong></td>
				<td ><? echo $fsoNo; ?></td>
				<td ><strong>Booking No:</strong></td>
				<td ><? echo $bookingNo; ?></td>
			</tr>

		</table>
		<div style="width:100%;">
			<table align="right" cellspacing="0" width="930" border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="180">Item Description</th>
					<th width="70">Color</th>
					<th width="80">Batch</th>
					<th width="50">UOM</th>
					<th width="60">No Of Roll</th>
					<th width="80">Return Qty.</th>
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
					?>

					<tr bgcolor="<? echo $bgcolor; ?>">
						<td><? echo $i; ?></td>
						<td><? echo $row[csf('product_name_details')]; ?></td>
						<td align="center"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
						<td align="center"><? echo $batch_arr[$row[csf("batch_id_from_fissuertn")]]; ?></td>
						<td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
						<td align="right"><? echo number_format($row[csf('no_of_roll')]); ?></td>
						<td align="right"><? echo number_format($row[csf('cons_quantity')],2); ?></td>
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
