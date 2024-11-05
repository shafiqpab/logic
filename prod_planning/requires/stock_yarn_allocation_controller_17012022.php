<?php
header('Content-type:text/html; charset=utf-8');
session_start();
$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
extract($_REQUEST);
$permission = $_SESSION['page_permission'];
include('../../includes/common.php');
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

/*
|--------------------------------------------------------------------------
| load_drop_down_supplier
|--------------------------------------------------------------------------
|
*/
if ($action == "load_drop_down_supplier")
{
	echo create_drop_down("cbo_supplier_name", 130, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' and b.party_type=2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name", "id,supplier_name", 1, "-- All Supplier --", $selected, "", 0);
	exit();
}

/*
|--------------------------------------------------------------------------
| load_drop_down_buyer
|--------------------------------------------------------------------------
|
*/
if ($action == "load_drop_down_buyer")
{
	echo create_drop_down("cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "", 0);
	exit();
}

/*
|--------------------------------------------------------------------------
| pi_search_popup
|--------------------------------------------------------------------------
|
*/
if ($action == "pi_search_popup")
{
	echo load_html_head_contents("PI Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$supplierID = str_replace("'", "", $supplierID);
	?>
	<script>
		function js_set_value(data)
		{
			var data = data.split("_");
			var id = data[0];
			var pi_no = data[1];
			$('#hidden_pi_id').val(id);
			$('#hidden_pi_no').val(pi_no);
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:600px;">
					<table width="590" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
					class="rpt_table" id="tbl_list">
					<thead>
						<th>Supplier</th>
						<th>PI No</th>
						<th>Date Range</th>
						<th>
	                        <input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;">
	                        <input type="hidden" name="hidden_pi_no" id="hidden_pi_no" value=""/>
	                        <input type="hidden" name="hidden_pi_id" id="hidden_pi_id" value=""/>
	                    </th>
					</thead>
					<tbody>
						<tr class="general">
							<td>
								<?
								echo create_drop_down("cbo_supplier_name", 140, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyID' and b.party_type=2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name", "id,supplier_name", 1, "-- All Supplier--", $supplierID, "", 0);
								?>
							</td>
							<td align="center">
								<input type="text" name="txt_pi_no" id="txt_pi_no" class="text_boxes" style="width:100px" placeholder="Write" >
							</td>
							<td>
								<input type="text" name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:55px;"/>			
	                           	<input type="text" name="txt_date_to" id="txt_date_to"  class="datepicker" style="width:55px;"/>
							</td>
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show"
								onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_supplier_name').value+'**'+document.getElementById('txt_pi_no').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_pi_search_list_view', 'search_div', 'stock_yarn_allocation_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
								style="width:100px;"/>
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

/*
|--------------------------------------------------------------------------
| create_pi_search_list_view
|--------------------------------------------------------------------------
|
*/
if ($action == "create_pi_search_list_view")
{
	$data = explode('**', $data);
	$company_id = $data[0];
	$supplier_id = $data[1];
	$txt_pi_no = $data[2];
	$date_from = str_replace("'", "", trim($data[3]));
	$date_to = str_replace("'", "", trim($data[4]));

	$pi_number_cond=$pi_date_cond='';
	if (trim($txt_pi_no) !='') $pi_number_cond=" and pi_number like '%".trim($txt_pi_no)."%'";
	if ($date_from != '' && $date_to != '')
	{
		if ($db_type == 0)
		{
			$pi_date_cond = "and pi_date between '".change_date_format($date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($date_to, "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$pi_date_cond = "and pi_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
		}
	}

	$supplier_id_cond='';
	if($supplier_id !=0) $supplier_id_cond=" and supplier_id=$supplier_id";
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

	$sql= "SELECT a.id, a.pi_number, a.pi_date, a.item_category_id, a.importer_id, a.supplier_id, a.last_shipment_date, a.import_pi, a.export_pi_id, sum(b.quantity) as quantity
	from com_pi_master_details a, com_pi_item_details b
	where a.id=b.pi_id and a.importer_id=$company_id and a.item_category_id=1 and a.version=1 and a.import_pi=0 and a.status_active=1 and a.is_deleted=0 $pi_number_cond $pi_date_cond $supplier_id_cond group by a.id, a.pi_number, a.pi_date, a.item_category_id, a.importer_id, a.supplier_id, a.last_shipment_date, a.import_pi, a.export_pi_id order by id desc";
	// echo $sql;die;
	?>
	<div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="500" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="120">Supplier</th>
				<th width="120">PI No</th>
				<th width="80">Date</th>
				<th>Quentity</th>
			</thead>
		</table>
	</div>
	<div style="width:500px; max-height:270px;overflow-y:scroll;" >
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="480" class="rpt_table" id="tbl_list_search">
			<?
			$sql_res=sql_select($sql);
			$i=1;
			foreach($sql_res as $supplierId=>$row )
			{
				if ($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";

				if($row[csf("import_pi")] == 1)
				{
					$supplier_name = $comp[$row[csf("supplier_id")]];
				}else{
					$supplier_name = $supplier[$row[csf("supplier_id")]];
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf("id")].'_'.$row[csf("pi_number")]; ?>');" >
					<td width="30" align="center"><?php echo $i; ?></td>
					<td width="120"><?php echo $supplier_name; ?></td>
					<td width="120" align="center"><?php echo $row[csf("pi_number")]; ?></td>
					<td width="80" align="center"><?php echo change_date_format($row[csf("pi_date")]); ?></td>
					<td><?php echo $row[csf("quantity")]; ?></td>
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

/*
|--------------------------------------------------------------------------
| pi_item_details
|--------------------------------------------------------------------------
|
*/
if ($action == "pi_item_details")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$company_name = str_replace("'", "", $cbo_company_name);
	$cbo_supplier_name = str_replace("'", "", $cbo_supplier_name);
	$txt_pi_no = str_replace("'", "", $txt_pi_no);
	$hidden_pi_id = str_replace("'", "", $hidden_pi_id);
	
	$supplierIdCondition = '';
	if ($cbo_supplier_name>0) {
		$supplierIdCondition = " AND a.supplier_id = ".$cbo_supplier_name."";
	}

	$piNoCondition = '';
	if($txt_pi_no != '')
	{
		$piNoCondition = "AND a.pi_number LIKE '".$txt_pi_no."'";
	}

	$piIdCondition = '';
	if($hidden_pi_id != '')
	{
		$piIdCondition = "AND a.id=$hidden_pi_id";
	}

	/*
	|--------------------------------------------------------------------------
	| main query
	| type = 1 = show button
	|--------------------------------------------------------------------------
	|
	*/
	$sql= "SELECT a.id, a.pi_number, a.item_category_id, a.importer_id, a.supplier_id, a.import_pi, a.export_pi_id, b.id as dtls_id, b.quantity, b.count_name, b.color_id, b.yarn_composition_item1, b.yarn_composition_percentage1, b.yarn_type
	from com_pi_master_details a, com_pi_item_details b
	where a.id=b.pi_id and a.importer_id=$company_name and a.item_category_id=1 and a.version=1 and a.import_pi=0 and a.status_active=1 and a.is_deleted=0 $piNoCondition $piIdCondition $supplierIdCondition";
	
	// echo $sql;die;
	$rptData = sql_select($sql);
	if(empty($rptData))
	{
		echo "<div style='width:100%;margin-top:10px;text-align:center'>".get_empty_data_msg()."</div>";
		die;
	}

	$piDtlsIdArr = array();
	foreach ($rptData as $row)
	{
		$piDtlsIdArr[$row[csf('dtls_id')]] = $row[csf('dtls_id')];
	}
	$piDtlsIdCond = implode(",", $piDtlsIdArr);
	$allocation_sql = "SELECT pi_id,pi_dtls_id, sum(quantity) as allocated_qty from stock_yarn_allocation
	where item_category_id=1 and pi_dtls_id in($piDtlsIdCond) and status_active=1 and is_deleted=0 group by pi_id,pi_dtls_id";

	$allocated_sql_data = sql_select($allocation_sql);
	$allocatedQtyArr = array();
	foreach ($allocated_sql_data as $row)
	{
		$allocatedQtyArr[$row[csf('pi_dtls_id')]] = $row[csf('allocated_qty')];
	}	

	$supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count WHERE status_active = 1 AND is_deleted = 0 ORDER BY yarn_count",'id','yarn_count');
	
	?>
	<fieldset style="width:830px;">
		<legend>PI Details</legend>
			<input type="hidden" value="<? echo $type; ?>" name="txt_type" id="txt_type">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table" id="tbl_list_search" align="center">
				<thead>
					<th width="40">SL</th>
					<th width="100">Supplier Name</th>
					<th width="190">Yarn Description</th>
					<th width="80">Yarn Count</th>
					<th width="80">Yarn Type</th>
					<th width="80">Color</th>
					<th width="80">PI Qty</th>
					<th width="70">Allocated Qty</th>
					<th width="">Balance Qty</th>
				</thead>
				<tbody>
				<?php
				$sl = 1;
				foreach ($rptData as $row)
				{
					if ($sl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$allocatedQty=$allocatedQtyArr[$row[csf('dtls_id')]];
					$balanceQty=$row[csf('quantity')]-$allocatedQtyArr[$row[csf('dtls_id')]];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $row[csf('id')]."**".$row[csf('dtls_id')]."**".$row[csf('supplier_id')]."**".$row[csf('yarn_composition_item1')]."**".$row[csf('yarn_composition_percentage1')]."**".$row[csf('count_name')]."**".$row[csf('yarn_type')]."**".$row[csf('color_id')]."**".number_format($balanceQty,2,".","")."**".number_format($allocatedQty,2,".","");?>")' onClick="fnc_selected_row('<? echo $sl; ?>', '')" style="text-decoration:none; cursor:pointer; vertical-align:middle;">
						<td align="center"><? echo $sl; ?></td>
						<td><? echo $supllier_arr[$row[csf('supplier_id')]]; ?></td>
						<td><? echo $composition[$row[csf('yarn_composition_item1')]].' '.$row[csf('yarn_composition_percentage1')].'%'; ?></td>
						<td><? echo $count_arr[$row[csf('count_name')]]; ?></td>
						<td><? echo $yarn_type[$row[csf('yarn_type')]]; ?></td>
						<td><? echo $color_library[$row[csf('color_id')]]; ?></td>
						<td align="right"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
						<td align="right"><? echo number_format($allocatedQty,2,'.',''); ?></td>
						<td align="right"><? echo number_format($balanceQty,2,'.',''); ?></td>
						<input type="hidden" name="hdnSupplierId[]" id="hdnSupplierId_<? echo $sl; ?>" value="<? echo $row[csf('supplier_id')]; ?>" />
						<input type="hidden" name="hdnYarnDescId[]" id="hdnYarnDescId_<? echo $sl; ?>" value="<? echo $row[csf('yarn_composition_item1')]; ?>" />
						<input type="hidden" name="hdnCountId[]" id="hdnCountId_<? echo $sl; ?>" value="<? echo $row[csf('count_name')]; ?>" />
						<input type="hidden" name="hdnYarnTypeId[]" id="hdnYarnTypeId_<? echo $sl; ?>" value="<? echo $row[csf('yarn_type')]; ?>" />
						<input type="hidden" name="hdnColorId[]" id="hdnColorId_<? echo $sl; ?>" value="<? echo $row[csf('color_id')]; ?>" />
						<input type="hidden" name="hdnPiMstId[]" id="hdnPiMstId_<? echo $sl; ?>" value="<? echo $row[csf('id')]; ?>" />
						<input type="hidden" name="hdnPiDtlsId[]" id="hdnPiDtlsId_<? echo $sl; ?>" value="<? echo $row[csf('dtls_id')]; ?>" />
					</tr>
					<?
					$sl++;
				}
				?>
				</tbody>
			</table>
		</div>
	</fieldset>
	<?
	die;
}

/*
|--------------------------------------------------------------------------
| save_update_delete
|--------------------------------------------------------------------------
|
*/
if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	// echo "20**string";die;

	$pi_mst_id = str_replace("'","",$hdnPiMstId);
	$pi_dtls_id = str_replace("'","",$hdnPiDtlsId);


	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		// ------------------Over qty validation start-----------------
		$tot_pi_qnty=return_field_value("sum(quantity) as pi_qty","com_pi_item_details","pi_id=$pi_mst_id and id=$pi_dtls_id and item_category_id=1 and status_active=1 and is_deleted=0","pi_qty");

		$tot_allocated_qty=return_field_value("sum(quantity) as allocated_qty","stock_yarn_allocation","pi_id=$pi_mst_id and pi_dtls_id=$pi_dtls_id and item_category_id=1 and status_active=1 and is_deleted=0","allocated_qty");
		// echo "10**$tot_pi_qnty-$tot_allocated_qty-$tot_allocated_qty";die;//91.67-10=81.67

		$balance_qty = ($tot_pi_qnty - $tot_allocated_qty);
		// echo "10**$tot_pi_qnty-$tot_allocated_qty-$tot_allocated_qty";die;
		// echo "10**$balance_qty";die;
		$txt_allocated_qty=str_replace("'","",$txt_allocated_qty);
		// echo "10**$txt_allocated_qty > $balance_qty";die;
		if($txt_allocated_qty > $balance_qty*1)
		{
			echo "20**Allocated Quantity Not Over PI Balance Quantity.\nBalance Quantity :$balance_qty";
			die;
		}
		// ------------------Over qty validation End-----------------

		if(str_replace("'","",$issue_mst_id)!="")
		{
			$new_return_number[0] = str_replace("'","",$txt_system_id);
			$id=str_replace("'","",$issue_mst_id);
			// master table UPDATE here START----------------------
			$field_array_mst="buyer_id*quantity*file_no*remarks*updated_by*update_date";
			$data_array_mst=$cbo_buyer_name."*".$txt_allocated_qty."*".$txt_file_no."*".$txt_remarks."*'".$user_id."'*'".$pc_date_time."'";
		}
		else
		{
			// master table entry here START--------------------------------------
			// $id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "stock_yarn_allocation", $con);
			$id=return_next_id( "id", "stock_yarn_allocation", 1 ) ;

			$field_array_mst="id,pi_id,pi_dtls_id,supplier_id,buyer_id,color_id,count_name,yarn_composition_item1,yarn_composition_percentage1,yarn_type,quantity,file_no,remarks,item_category_id,entry_form,inserted_by,insert_date";
			$data_array_mst="(".$id.",".$hdnPiMstId.",".$hdnPiDtlsId.",".$hdnSupplierId.",".$cbo_buyer_name.",".$hdnColorId.",".$hdnCountId.",".$hdnYarnDescId.",".$hdnCompPercentage.",".$hdnYarnTypeId.",".$txt_allocated_qty.",".$txt_file_no.",".$txt_remarks.",1,0,'".$user_id."','".$pc_date_time."')";
		}

		$rID=true;
		if(str_replace("'","",$txt_system_id)!="")
		{
			$rID=sql_update("stock_yarn_allocation",$field_array_mst,$data_array_mst,"id",$id,1);
		}
		else
		{
			$rID=sql_insert("stock_yarn_allocation",$field_array_mst,$data_array_mst,1);
		}
		// echo "10**insert into stock_yarn_allocation (".$field_array_mst.") values ".$data_array_mst;die;

		// echo "10**".$rID;die;

		if($db_type==0)
		{
			if( $rID)
			{
				mysql_query("COMMIT");
				echo "0**".$id."**".str_replace("'","",$hdnPiMstId)."**".str_replace("'","",$hdnPiDtlsId);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if( $rID)
			{
				oci_commit($con);
				echo "0**".$id."**".str_replace("'","",$hdnPiMstId)."**".str_replace("'","",$hdnPiDtlsId);
			}
			else
			{
				oci_rollback($con);
				echo "10**".$id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		// ------------------Over qty validation start-----------------
		$tot_pi_qnty=return_field_value("sum(quantity) as pi_qty","com_pi_item_details","pi_id=$pi_mst_id and id=$pi_dtls_id and item_category_id=1 and status_active=1 and is_deleted=0","pi_qty");

		$tot_allocated_qty=return_field_value("sum(quantity) as allocated_qty","stock_yarn_allocation","pi_id=$pi_mst_id and pi_dtls_id=$pi_dtls_id and item_category_id=1 and status_active=1 and is_deleted=0","allocated_qty");

		$prev_updated_allocated_qty=return_field_value("sum(quantity) as allocated_qty","stock_yarn_allocation","id=$update_id and pi_id=$pi_mst_id and pi_dtls_id=$pi_dtls_id and item_category_id=1 and status_active=1 and is_deleted=0","allocated_qty");
		// echo "10**$tot_pi_qnty-$tot_allocated_qty-$tot_allocated_qty";die;//91.67-10=81.67

		// 90-80+10
		$balance_qty = ($tot_pi_qnty - $tot_allocated_qty)+$prev_updated_allocated_qty;
		// echo "10**$tot_pi_qnty-$tot_allocated_qty+$prev_updated_allocated_qty";die;
		$txt_allocated_qty=str_replace("'","",$txt_allocated_qty);
		// echo "10**$txt_allocated_qty > $balance_qty";die;
		if($txt_allocated_qty*1 > $balance_qty*1)
		{
			echo "20**Allocated Quantity Not Over PI Balance Quantity.\nBalance Quantity :$balance_qty";
			die;
		}
		// echo "20**string";die;
		// ------------------Over qty validation End-----------------

		//check update id
		if( str_replace("'","",$update_id) == "" )
		{
			echo "10";disconnect($con);die;
		}

		$update_id = str_replace("'","",$update_id);

		$field_array_update="buyer_id*quantity*file_no*remarks*updated_by*update_date";
		$data_array_update=$cbo_buyer_name."*".$txt_allocated_qty."*".$txt_file_no."*".$txt_remarks."*'".$user_id."'*'".$pc_date_time."'";
		// echo "10**$field_array_update.'='.$data_array_update";die;
		$rID=true;
		$rID = sql_update("stock_yarn_allocation",$field_array_update,$data_array_update,"id",$update_id,1);
		// echo "10**$rID";die;

		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");
				echo "1**".$update_id."**".str_replace("'","",$hdnPiMstId)."**".str_replace("'","",$hdnPiDtlsId);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);
				echo "1**".$update_id."**".str_replace("'","",$hdnPiMstId)."**".str_replace("'","",$hdnPiDtlsId);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
		die;
	}
}

/*
|--------------------------------------------------------------------------
| dtls_list_view
|--------------------------------------------------------------------------
|
*/
if($action=="show_dtls_list_view")
{
	$ex_data = explode("**",$data);

	$sql = "SELECT id,pi_id,pi_dtls_id,supplier_id,buyer_id,color_id,count_name,yarn_composition_item1,yarn_composition_percentage1,yarn_type,quantity,file_no,remarks,item_category_id,entry_form,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from stock_yarn_allocation
	where item_category_id=1 and pi_dtls_id=$ex_data[0] and status_active=1 and is_deleted=0";
	//echo $sql;
	$result = sql_select($sql);
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	?>
	<table class="rpt_table" border="1" cellpadding="2" cellspacing="0" style="width:415px" rules="all">
		<thead>
			<tr>
				<th>SL</th>
				<th>Buyer</th>
				<th>Allocated Qty</th>
				<th>Allocate File No</th>
				<th>Remarks</th>
			</tr>
		</thead>
		<tbody>
			<?
			$i=1;
			foreach($result as $row)
			{
				if($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("id")]."**".$row[csf("pi_id")]."**".$row[csf("pi_dtls_id")];?>","child_form_input_data","requires/stock_yarn_allocation_controller")' style="cursor:pointer" >
					<td width="50"><? echo $i; ?></td>
					<td width="100"><p><? echo $buyer_arr[$row[csf("buyer_id")]]; ?></p></td>
					<td width="80" align="center"><p><? echo $row[csf("quantity")]; ?></p></td>
					<td width="80"><p><? echo $row[csf("file_no")]; ?></p></td>
					<td align="right" style="padding-right:3px;"><p><? echo $row[csf("remarks")]; ?></p></td>
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

/*
|--------------------------------------------------------------------------
| populate_data_from_dtls_list_view
|--------------------------------------------------------------------------
|
*/
if($action=="child_form_input_data")
{
	$ex_data = explode("**",$data);
	$pi_id=$ex_data[1];
	$pi_dtls_id=$ex_data[2];
	$sql = "SELECT id,pi_id,pi_dtls_id,supplier_id,buyer_id,color_id,count_name,yarn_composition_item1,yarn_composition_percentage1,yarn_type,quantity,file_no,remarks,item_category_id from stock_yarn_allocation
	where item_category_id=1 and id=$ex_data[0] and status_active=1 and is_deleted=0";
	//echo $sql;

	$result = sql_select($sql);
	foreach($result as $row)
	{
		echo "$('#hdnPiMstId').val('".$row[csf("pi_id")]."');\n";
		echo "$('#hdnPiDtlsId').val('".$row[csf("pi_dtls_id")]."');\n";
		echo "$('#hdnSupplierId').val('".$row[csf("supplier_id")]."');\n";
		echo "$('#hdnYarnDescId').val('".$row[csf("yarn_composition_item1")]."');\n";
		echo "$('#hdnCompPercentage').val('".$row[csf("yarn_composition_percentage1")]."');\n";
		echo "$('#hdnCountId').val('".$row[csf("count_name")]."');\n";
		echo "$('#hdnYarnTypeId').val('".$row[csf("yarn_type")]."');\n";
		echo "$('#hdnColorId').val('".$row[csf("color_id")]."');\n";
		echo "$('#cbo_buyer_name').val('".$row[csf("buyer_id")]."');\n";
		echo "$('#txt_allocated_qty').val('".$row[csf("quantity")]."');\n";
		echo "$('#hdnThisAllocatedQty').val('".$row[csf("quantity")]."');\n";
		echo "$('#txt_file_no').val('".$row[csf("file_no")]."');\n";
		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
		echo "$('#update_id').val(".$row[csf("id")].");\n";

		$tot_pi_qnty=return_field_value("sum(quantity) as pi_qty","com_pi_item_details","pi_id=$pi_id and id=$pi_dtls_id and item_category_id=1 and status_active=1 and is_deleted=0","pi_qty");

		$tot_allocated_qty=return_field_value("sum(quantity) as allocated_qty","stock_yarn_allocation","pi_id=$pi_id and pi_dtls_id=$pi_dtls_id and item_category_id=1 and status_active=1 and is_deleted=0","allocated_qty");


		$balance_qnty = $tot_pi_qnty-$tot_allocated_qty;//$totalPiQty-$row[csf("quantity")];
		echo "$('#hdn_balance_qty').val('".$balance_qnty."');\n";
	}
	echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_allocation_entry_details',1);\n";
	// echo "alert('$balance_qnty');\n";
	// echo "set_button_status(1, permission, 'fnc_allocation_entry_details',1,1);\n";
	
	exit();
}

/*
|--------------------------------------------------------------------------
| stock_yarn_allocation_print
|--------------------------------------------------------------------------
|
*/
if ($action=="stock_yarn_allocation_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	// print_r ($data);die;

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count WHERE status_active = 1 AND is_deleted = 0 ORDER BY yarn_count",'id','yarn_count');

	$sql = "SELECT id,pi_id,pi_dtls_id,supplier_id,buyer_id,color_id,count_name,yarn_composition_item1,yarn_composition_percentage1,yarn_type,quantity,file_no,remarks,item_category_id from stock_yarn_allocation
	where item_category_id=1 and pi_id=$data[1]  and status_active=1 and is_deleted=0";
	// echo $sql;
	$sql_result= sql_select($sql);
	?>
	<div style="width:690px;">
		<table width="690" cellspacing="0" >
			<tr>
				<td rowspan="3"  valign="middle">
					<?
					$data_array2=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
					foreach($data_array2 as $img_row)
					{
						?>
						<img src='../../../<? echo $img_row[csf('image_location')]; ?>' height='60' width='170' align="middle" />
						<?
					}
					?>
				</td>
				<td colspan="5" align="center" style="font-size:22px"><strong><? echo   $company_library[$data[0]]; ?></strong></td>
			</tr>
			<tr>
				<td colspan="5" align="center">
					<?
                	//echo show_company($data[0],'','');//Aziz
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
						echo $result[csf('plot_no')];
						if($result[csf('plot_no')]!="") echo ", ";
						echo $result[csf('level_no')];
						if($result[csf('level_no')]!="") echo ", ";
						echo $result[csf('road_no')];
						if($result[csf('road_no')]!="") echo ", ";
						echo $result[csf('block_no')];
						if($result[csf('block_no')]!="") echo ", ";
						echo $result[csf('city')];
						if($result[csf('city')]!="") echo ", ";
						echo $result[csf('zip_code')];
						if($result[csf('zip_code')]!="") echo ", ";
						echo $result[csf('country_id')];
						if($result[csf('country_id')]!="") echo ", ";
						echo "<br> ";
						if($result[csf('email')]!="") echo "Email Address: ".$result[csf('email')];
						if($result[csf('website')]!="") echo "Website No: ".$result[csf('website')];
					}
					?>
				</td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" width="790"  border="1" rules="all" class="rpt_table" >
			<thead bgcolor="#dddddd">
				<th width="20">SL</th>
				<th width="100">Buyer</th>
				<th width="100">File</th>
				<th width="110">Color</th>
				<th width="180">Yarn Description</th>
				<th width="90">Allocate Qty</th>
				<th>Remarks</th>
			</thead>
			<tbody>
				<?
				$i=1;
				foreach($sql_result as $row)
				{
					if ($i%2==0)$bgcolor="#E9F3FF";	 else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td align="center"><? echo $i; ?></td>
						<td style="word-break:break-all"><? echo $buyer_library[$row[csf("buyer_id")]]; ?></td>
						<td style="word-break:break-all;"><? echo $row[csf("file_no")]; ?></td>
						<td style="word-break:break-all;"><? echo $color_library[$row[csf("color_id")]]; ?></td>
						<td style="word-break:break-all;"><? echo $composition[$row[csf("yarn_composition_item1")]].' '.$row[csf("yarn_composition_percentage1")].'%'; ?></td>
						<td align="right" style="word-break:break-all;"><? echo number_format($row[csf("quantity")],2); ?></td>
						<td style="word-break:break-all;"><? echo $row[csf("remarks")]; ?></td>
					</tr>
					<? $i++;
				}
				?>
			</tbody>
		</table>
		<br>
		<?
		echo signature_table(247, $data[0], "690px");
		?>
	</div>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<?
	exit();
}

?>