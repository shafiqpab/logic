<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];


//load drop down company Store location
if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 140, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data' and b.category_type=1 $store_location_credential_cond and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", $selected, "",0 );
	exit();
}

//--------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------
//item search------------------------------//
if ($action == "item_description_search")
{
	echo load_html_head_contents("Popup Info", "../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>

		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;

		function check_all_data() {
			var tbl_row_count = document.getElementById('list_view').rows.length;
			tbl_row_count = tbl_row_count - 1;
			for (var i = 1; i <= tbl_row_count; i++) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value(functionParam);

			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value(strCon)
		{
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];

			toggle(document.getElementById('tr_' + str), '#FFFFCC');

			if (jQuery.inArray(selectID, selected_id) == -1) {
				selected_id.push(selectID);
				selected_name.push(selectDESC);
				selected_no.push(str);
			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == selectID)
						break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
				selected_no.splice(i, 1);
			}
			var id = '';
			var name = '';
			var job = '';
			var num = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				num += selected_no[i] + ',';
			}
			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);
			num = num.substr(0, num.length - 1);

			$('#txt_selected_id').val(id);
			$('#txt_selected').val(name);
			$('#txt_selected_no').val(num);
		}

		function fn_check_lot()
		{
			show_list_view(document.getElementById('cbo_search_by').value + '_' + document.getElementById('txt_search_common').value + '_' +<? echo $company; ?>+ '_' + document.getElementById('txt_prod_id').value+ '_' + document.getElementById('cbo_supplier').value, 'create_lot_search_list_view', 'search_div', 'item_ledger_report_controller', 'setFilterGrid("list_view",-1)');
		}
	</script>
	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<tr>
                        	<th>Supplier</th>
							<th>Search By</th>
							<th align="center" width="180" id="search_by_td_up">Enter Lot Number</th>
                            <th>Product Id</th>
							<th>
								<input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  />
								<input type='hidden' id='txt_selected_id' />
								<input type='hidden' id='txt_selected' />
								<input type='hidden' id='txt_selected_no' />
							</th>
						</tr>
					</thead>
					<tbody>
						<tr align="center">
                        	<td>
                            <?
							echo create_drop_down("cbo_supplier", 150, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company' $user_supplier_cond and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
							?>
                            </td>
							<td align="center" width="160">
								<?
								$search_by = array(1 => 'Lot No', 2 => 'Item Description');
								$dd = "change_search_event(this.value, '0*0', '0*0', '../../')";
								echo create_drop_down("cbo_search_by", 150, $search_by, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td width="180" align="center" id="search_by_td">

								<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
							</td>
                            <td width="110" align="center">
								<input type="text" style="width:90px" class="text_boxes_numeric"  name="txt_prod_id" id="txt_prod_id" />
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="fn_check_lot()" style="width:100px;" />
							</td>
						</tr>
					</tbody>
				</tr>
			</table>
			<div align="center" valign="top" style="margin-top:5px" id="search_div"> </div>
		</form>
	</div>
	</body>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
    /* $sql = "select id,supplier_id,lot,product_name_details from product_details_master where company_id=$company and item_category_id=1";
      //echo $sql;
      $supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
      $arr=array(1=>$supplier_arr);
      echo create_list_view("list_view", "Product Id, Supplier, Lot, Item Description","70,160,70","600","300",0, $sql , "js_set_value", "id,product_name_details", "", 1, "0,supplier_id,0,0", $arr, "id,supplier_id,lot,product_name_details", "","setFilterGrid('list_view',-1)","0","",1) ;
      echo "<input type='hidden' id='txt_selected_id' />";
      echo "<input type='hidden' id='txt_selected' />";
      echo "<input type='hidden' id='txt_selected_no' />"; */
      ?>
      <script language="javascript" type="text/javascript">
        /*var style_no='<? echo $txt_produc_no; ?>';
         var style_id='<? echo $txt_produc_id; ?>';
         var style_des='<? echo $txt_product; ?>';
         //alert(style_id);
         if(style_no!="")
         {
         style_no_arr=style_no.split(",");
         style_id_arr=style_id.split(",");
         style_des_arr=style_des.split(",");
         var str_ref="";
         for(var k=0;k<style_no_arr.length; k++)
         {
         str_ref=style_no_arr[k]+'_'+style_id_arr[k]+'_'+style_des_arr[k];
         js_set_value(str_ref);
         }
     }*/
 	</script>
 	<?
 	exit();
}

if ($action=="create_lot_search_list_view")
{
	echo load_html_head_contents("Popup Info", "../../../../", 1, 1, $unicode);
	$ex_data = explode("_", $data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$company = $ex_data[2];
	$prod_id = trim($ex_data[3]);
	$supplier_id = trim($ex_data[4]);

	//echo $supplier_id;die;
	if($prod_id=="" && $txt_search_common=="" && $supplier_id==0)
	{
		echo "Please Select At List One Field Supplier/Lot/Product Id";die;
	}
	?>
    <div>
        <div style="width:680px;" align="left">
            <table cellspacing="0" cellpadding="0" width="100%" class="rpt_table" border="1" rules="all">
                <thead>
                	<th width="30">SL No</th>
                    <th width="60">Product ID</th>
                    <th width="200">Supplier</th>
                    <th width="80">Lot</th>
                    <th>Item Description</th>
                </thead>
            </table>
        </div>

        <div style="width:680px; overflow-y:scroll; min-height:50px; max-height:230px;" id="buyer_list_view" align="left">
            <table cellspacing="0" cellpadding="0" width="660" class="rpt_table" id="list_view" border="1" rules="all" >
	            <?php

	            //$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	            $supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
				$sql_cond = "";
				if (trim($txt_search_common) != "")
				{
					if (trim($txt_search_by) == 1) { // for LOT NO
						$sql_cond = " and d.lot LIKE '%$txt_search_common%'";
					} else if (trim($txt_search_by) == 2) { // for Yarn Count
						$sql_cond = " and d.product_name_details LIKE '%$txt_search_common%'";
					}
				}

				if($prod_id) $sql_cond .= " and d.id = $prod_id";

				if($supplier_id) $sql_cond .= " and D.SUPPLIER_ID = $supplier_id";

				$sql = "select D.ID ,D.COMPANY_ID, D.SUPPLIER_ID, D.LOT, D.PRODUCT_NAME_DETAILS
				from product_details_master d
				where d.item_category_id=1 and d.status_active=1 and d.is_deleted=0 and d.company_id=$company $sql_cond";
				//echo $sql;die;

				$sql_result = sql_select($sql);
				$i = 1;
				foreach ($sql_result as $row)
				{
					$id_arr[] = $row['ID'];

					if ($i % 2 == 0) {
						$bgcolor = "#E9F3FF";
					} else {
						$bgcolor = "#FFFFFF";
					}


					$factory_name=$supplier_arr[$row['SUPPLIER_ID']];
					$selectedString = "'".$i.'_'.$row['ID'].'_'.$row['PRODUCT_NAME_DETAILS']."'";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value(<? echo $selectedString;?>)">
						<td width="30" align="center"><?php echo $i; ?></td>
						<td width="60" align="center"><?php echo $row['ID']; ?></td>
						<td width="200" style="word-break:break-all;">&nbsp;<?php echo $factory_name; ?></td>
						<td width="80" style="word-break:break-all;">&nbsp; <?php echo $row['LOT']; ?></td>
						<td style="word-break:break-all">&nbsp; <?php echo $row['PRODUCT_NAME_DETAILS']; ?></td>
					</tr>
					<?php
					$i++;
				}
				?>
            </table>
        </div>

        <div style="width:580px;" align="left">
            <table width="100%">
                <tr>
                    <td align="center" colspan="6" height="30" valign="bottom">
                        <div style="width:100%">
                                <div style="width:50%; float:left" align="left">
                                    <!--<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data('<?// echo implode(',',$id_arr);?>')" /> Check / Uncheck All-->
                                </div>
                                <div style="width:50%;" align="center">
                                <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                                </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <script>
        setFilterGrid('list_view',-1);
        check_all_data();
    </script>
    <?
    exit();
}

if ($action=="create_lot_search_list_view_31102021")
{
	echo load_html_head_contents("Popup Info", "../../../../", 1, 1, $unicode);
	?>
    <div>
        <div style="width:580px;" align="left">
            <table cellspacing="0" cellpadding="0" width="100%" class="rpt_table" >
                <thead>
                	<th width="50">SL No</th>
                    <th width="100">Product ID</th>
                    <th width="150">Supplier</th>
                    <th width="80">Lot</th>
                    <th width="">Item Description</th>
                </thead>
            </table>
        </div>

        <div style="width:580px; overflow-y:scroll; min-height:50px; max-height:250px;" id="buyer_list_view" align="left">
            <table  cellspacing="0" cellpadding="0" width="100%" class="rpt_table" id="list_view" >
	            <?php

	            $company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	            $supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');

				$ex_data = explode("_", $data);
				$txt_search_by = $ex_data[0];
				$txt_search_common = trim($ex_data[1]);
				$company = $ex_data[2];
				$prod_id = trim($ex_data[3]);

				$sql_cond = "";
				if (trim($txt_search_common) != "")
				{
					if (trim($txt_search_by) == 1) { // for LOT NO
						$sql_cond = " and d.lot LIKE '%$txt_search_common%'";
					} else if (trim($txt_search_by) == 2) { // for Yarn Count
						$sql_cond = " and d.product_name_details LIKE '%$txt_search_common%'";
					}
				}

				if($prod_id) $sql_cond .= " and d.id = $prod_id";

				$sql = "select a.mst_id,a.transaction_type,d.id,d.company_id,d.supplier_id, d.lot,d.product_name_details from inv_transaction a,product_details_master d where a.prod_id=d.id and a.transaction_type in (1,4,5) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and d.company_id=$company $sql_cond group by a.mst_id,a.transaction_type,d.id,d.company_id,d.supplier_id,d.lot,d.product_name_details";
				echo $sql; die;

				$sql_result = sql_select($sql);
				$i = 1;
				foreach ($sql_result as $row)
				{
					$id_arr[] = $row[csf('id')];

					if($prodIdChk[$row[csf('id')]]=="")
	        		{

						if ($i % 2 == 0) {
							$bgcolor = "#E9F3FF";
						} else {
							$bgcolor = "#FFFFFF";
						}

						$prodIdChk[$row[csf('id')]] = $row[csf('id')];

						$mst_id = $row[csf('mst_id')];
						$rcv_supplier_id = return_field_value("supplier_id", "inv_receive_master", "id=$mst_id","supplier_id");
						$receive_purpose = return_field_value("receive_purpose", "inv_receive_master", "id=$mst_id","receive_purpose");
						$pay_mode = return_field_value("b.pay_mode", "inv_receive_master a,wo_yarn_dyeing_mst b", "a.booking_id=b.id and b.id=$mst_id","pay_mode");

						if( $row[csf('transaction_type')] ==1 || $row[csf('transaction_type')]==4)
						{
							if( $receive_purpose ==2 || $receive_purpose ==7 || $receive_purpose ==12 || $receive_purpose ==15 || $receive_purpose == 38 || $receive_purpose ==46 || $receive_purpose ==50 || $receive_purpose ==51 )
							{
								if($pay_mode==3 || $pay_mode==5)
								{
									$factory_name = $company_arr[$rcv_supplier_id];
								}else{
									$factory_name = $supplier_arr[$rcv_supplier_id];
								}
							}
							else
							{
								$factory_name = $supplier_arr[$rcv_supplier_id];
							}
						}
						else
						{
							$factory_name=$supplier_arr[$row[csf('supplier_id')]];
						}

						$selectedString = "'".$i.'_'.$row[csf('id')].'_'.$row[csf('product_name_details')]."'";
						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value(<? echo $selectedString;?>)">
	                        <td width="50" align="center"><?php echo $i; ?></td>
	                        <td width="100" align="center"><?php echo $row[csf('id')]; ?></td>
	                        <td width="150">&nbsp;<?php echo $factory_name; ?></td>
	                        <td width="80">&nbsp; <?php echo $row[csf('lot')]; ?></td>
	                        <td>&nbsp; <?php echo $row[csf('product_name_details')]; ?></td>
	                    </tr>
	                    <?php
						$i++;
					}
				}
				?>
            </table>
        </div>

        <div style="width:580px;" align="left">
            <table width="100%">
                <tr>
                    <td align="center" colspan="6" height="30" valign="bottom">
                        <div style="width:100%">
                                <div style="width:50%; float:left" align="left">
                                    <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data('<? echo implode(',',$id_arr);?>')" /> Check / Uncheck All
                                </div>
                                <div style="width:50%; float:left" align="left">
                                <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                                </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <script>
        setFilterGrid('list_view',-1);
        check_all_data();
    </script>
    <?
    exit();
}

//report generated here--------------------//
if ($action == "generate_report")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
    //print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));
	$search_cond = "";
	if ($db_type == 0) {
		if ($from_date != "" && $to_date != "")
			$search_cond .= " and a.transaction_date  between '" . change_date_format($from_date, 'yyyy-mm-dd') . "' and '" . change_date_format($to_date, 'yyyy-mm-dd') . "'";
	}
	else {
		if ($from_date != "" && $to_date != "")
			$search_cond .= " and a.transaction_date  between '" . date("j-M-Y", strtotime($from_date)) . "' and '" . date("j-M-Y", strtotime($to_date)) . "'";
	}

	$lot = str_replace("'", "", trim($txt_lot_no));
	if (str_replace("'", "", trim($txt_lot_no)) != "")
		$search_string = " and b.lot='$lot'";
	else
		$search_string = "";

	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0", "id", "supplier_name");

	$variable_store_wise_rate=return_field_value("auto_transfer_rcv","variable_settings_inventory","company_name=$cbo_company_name and variable_list=47 and item_category_id=1 and status_active=1 and is_deleted=0","auto_transfer_rcv");

	$sql_receive_mrr = "select a.id as trid, a.transaction_type, a.buyer_id, b.recv_number, b.knitting_source, b.knitting_company, b.supplier_id, b.receive_purpose, b.receive_basis, booking_id
	from inv_transaction a, inv_receive_master b
	where a.prod_id in ($txt_product_id) and a.mst_id=b.id and a.transaction_type in (1,4) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

	//echo $sql_receive_mrr;die;

	$result_rcv = sql_select($sql_receive_mrr);
	$receiveMRR = array();
	$trWiseReceiveMRR = array();
	foreach ($result_rcv as $row)
	{
		$receiveMRR[$row[csf("trid")] . $row[csf("transaction_type")]] = $row[csf("recv_number")];
		$trWiseReceiveMRR[$row[csf("trid")]] = $row[csf("recv_number")];
		$receive_source[$row[csf("trid")] . $row[csf("transaction_type")]]["knitting_source"] = $row[csf("knitting_source")];
		$receive_source[$row[csf("trid")] . $row[csf("transaction_type")]]["knitting_company"] = $row[csf("knitting_company")];
		$receive_source[$row[csf("trid")] . $row[csf("transaction_type")]]["knitting_supplier"] = $row[csf("supplier_id")];
		$receive_source[$row[csf("trid")] . $row[csf("transaction_type")]]["receive_basis"] = $row[csf("receive_basis")];
		$receive_source[$row[csf("trid")] . $row[csf("transaction_type")]]["receive_purpose_id"] = $row[csf("receive_purpose")];
		$receive_source[$row[csf("trid")] . $row[csf("transaction_type")]]["loan_party"] = $row[csf("loan_party")];

		$receive_source[$row[csf("trid")] . $row[csf("transaction_type")]]["buyer_id"] = $row[csf("buyer_id")];

		$receive_source[$row[csf("trid")] . $row[csf("transaction_type")]]["receive_purpose"] = $yarn_issue_purpose[$row[csf("receive_purpose")]];

		if($row[csf("transaction_type")]==4)
		{
			$issue_ret_source[$row[csf("trid")] . $row[csf("transaction_type")]]["knitting_source"] = $row[csf("knitting_source")];
			$issue_ret_source[$row[csf("trid")] . $row[csf("transaction_type")]]["knitting_company"] = $row[csf("knitting_company")];
			$issue_ret_source[$row[csf("trid")] . $row[csf("transaction_type")]]["knitting_supplier"] = $row[csf("knitting_company")];
			$issue_ret_source[$row[csf("trid")] . $row[csf("transaction_type")]]["knitting_supplier"] = $row[csf("knitting_company")];
		}

		if( $row[csf("receive_purpose")]==2 || $row[csf("receive_purpose")]==7 || $row[csf("receive_purpose")]==12 || $row[csf("receive_purpose")]==15 || $row[csf("receive_purpose")]== 38 || $row[csf("receive_purpose")]==46 || $row[csf("receive_purpose")]==50 || $row[csf("receive_purpose")]==51 )
		{
			$wo_booking_id[$row[csf('booking_id')]] = $row[csf('booking_id')];
			$wo_rcv_booking_id[$row[csf("trid")]] = $row[csf("booking_id")];
		}

	}
	$con = connect();
	$r_id=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id");
	if($r_id)
	{
		oci_commit($con);
	}

	if(count($wo_booking_id)>0)
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 770, 2,$wo_booking_id, $empty_arr);
	}
	//echo "<pre>";
	//print_r($wo_rcv_booking_id);
	if(!empty($wo_booking_id))
	{
		$wo_sql_result =sql_select("select a.id, a.supplier_id, a.pay_mode from wo_yarn_dyeing_mst a, GBL_TEMP_ENGINE b
		where a.id=b.ref_val and b.REF_FROM=2 and a.status_active=1 and a.is_deleted=0");
		foreach ($wo_sql_result as $row)
		{
			$wo_data[$row[csf("id")]]['pay_mode'] = $row[csf("pay_mode")];
		}
	}

    // issue MRR array------------------------------------------------
	$sql_issue_mrr = "select a.id as trid,a.transaction_type,b.issue_number,b.issue_purpose
	from inv_transaction a, inv_issue_master b
	where a.prod_id in ($txt_product_id) and a.mst_id=b.id and a.transaction_type in (2,3) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$result_iss = sql_select($sql_issue_mrr);
	$issueMRR = array();
	$issuePupose = array();
	foreach ($result_iss as $row) {
		$issueMRR[$row[csf("trid")] . $row[csf("transaction_type")]] = $row[csf("issue_number")];
		$issuePupose[$row[csf("trid")]] = $yarn_issue_purpose[$row[csf("issue_purpose")]];
	}

	$transMrrArr = return_library_array("select a.id, a.transfer_system_id from inv_item_transfer_mst a, inv_transaction b where a.id=b.mst_id and b.prod_id in($txt_product_id) group by a.id, a.transfer_system_id", "id", "transfer_system_id");
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer", "id", "buyer_name");
	$store_arr = return_library_array("select id,store_name from lib_store_location", "id", "store_name");
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");

	$mrrArray = array();
	$mrrArray = $receiveMRR + $issueMRR;
	?>
	<fieldset>
		<?
		$store_id=str_replace("'", "", $cbo_store_name);

		if($store_id>0)
		{
			$storeCond = "and store_id=$store_id";
		}

		if ($cbo_method == 0)
		{
			$opning_bal_arr = array();
			if ($from_date != "" && $to_date != "")
			{
				if ($db_type == 2)
					$from_date = date("j-M-Y", strtotime($from_date));
				if ($db_type == 0)
					$from_date = change_date_format($from_date, 'yyyy-mm-dd');
				if($variable_store_wise_rate==1)
				{
					$sqlTR = "select  prod_id, SUM(CASE WHEN transaction_type in (1,4,5) THEN cons_quantity ELSE 0 END) as receive,
					SUM(CASE WHEN transaction_type in (2,3,6) THEN cons_quantity ELSE 0 END) as issue,
					SUM(CASE WHEN transaction_type in (1,4,5) THEN store_amount ELSE 0 END) as rcv_balance,
					SUM(CASE WHEN transaction_type in (2,3,6) THEN store_amount ELSE 0 END) as iss_balance
					from inv_transaction
					where transaction_date < '" . $from_date . "' and status_active=1 and is_deleted=0 and prod_id in($txt_product_id) $storeCond group by prod_id";
				}
				else
				{
					$sqlTR = "select  prod_id, SUM(CASE WHEN transaction_type in (1,4,5) THEN cons_quantity ELSE 0 END) as receive,
					SUM(CASE WHEN transaction_type in (2,3,6) THEN cons_quantity ELSE 0 END) as issue,
					SUM(CASE WHEN transaction_type in (1,4,5) THEN cons_amount ELSE 0 END) as rcv_balance,
					SUM(CASE WHEN transaction_type in (2,3,6) THEN cons_amount ELSE 0 END) as iss_balance
					from inv_transaction
					where transaction_date < '" . $from_date . "' and status_active=1 and is_deleted=0 and prod_id in($txt_product_id) $storeCond group by prod_id";
				}

				$trResult = sql_select($sqlTR);
				foreach ($trResult as $row)
				{
					$opning_bal_arr[$row[csf("prod_id")]]["prod_id"] = $row[csf("prod_id")];
					$opning_bal_arr[$row[csf("prod_id")]]["receive"] = $row[csf("receive")];
					$opning_bal_arr[$row[csf("prod_id")]]["issue"] = $row[csf("issue")];
					$opning_bal_arr[$row[csf("prod_id")]]["rcv_balance"] = $row[csf("rcv_balance")];
					$opning_bal_arr[$row[csf("prod_id")]]["iss_balance"] = $row[csf("iss_balance")];
				}
				unset($trResult);
			}



			$cbo_store_name=str_replace("'","",$cbo_store_name);
			$store_cond="";

			if($cbo_store_name>0) $store_cond=" and a.store_id=$cbo_store_name";
			if($variable_store_wise_rate==1)
			{
				$sql = "select a.id, a.mst_id, a.prod_id, a.store_id, a.transaction_date, a.receive_basis, a.insert_date, a.transaction_type, a.cons_quantity, a.store_rate as cons_rate, a.store_amount as cons_amount, a.cons_reject_qnty, a.remarks, b.product_name_details,b.color,b.yarn_comp_percent1st,b.yarn_count_id, yarn_comp_type1st, b.yarn_type, b.unit_of_measure, b.lot, b.supplier_id, c.knit_dye_source, c.knit_dye_company, c.issue_purpose, c.loan_party, case when a.buyer_id>0 then a.buyer_id else c.buyer_id end as buyer_id
				from inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3,6),product_details_master b
				where a.prod_id in ($txt_product_id) and a.prod_id=b.id $search_string and a.item_category=1 and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $search_cond $store_cond
				order by a.prod_id, a.insert_date, a.id ASC";
			}
			else
			{
				$sql = "select a.id, a.mst_id, a.prod_id, a.store_id, a.transaction_date, a.receive_basis, a.insert_date, a.transaction_type, a.cons_quantity, a.cons_rate, a.cons_amount, a.cons_reject_qnty, a.remarks, b.product_name_details,b.color,b.yarn_comp_percent1st,b.yarn_count_id, yarn_comp_type1st,b.yarn_type, b.unit_of_measure, b.lot, b.supplier_id,c.knit_dye_source, c.knit_dye_company, c.issue_purpose, c.loan_party,case when a.buyer_id>0 then a.buyer_id else c.buyer_id end as buyer_id
				from inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3,6),product_details_master b
				where a.prod_id in ($txt_product_id) and a.prod_id=b.id $search_string and a.item_category=1 and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $search_cond $store_cond
				order by a.prod_id, a.insert_date, a.id ASC";
			}

			//echo $sql;die;

			$result = sql_select($sql);
			$all_issue_trans_id=array();
			foreach($result as $row)
			{
				if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6)
				{
					$all_issue_trans_id[$row[csf("id")]]=$row[csf("id")];
				}
			}



			/*$check_is_sales_sql = "select trans_id,is_sales from order_wise_pro_details where trans_id in(".implode(",",$all_issue_trans_id).")";
			//echo $check_is_sales_sql;die;
			$check_is_sales=sql_select($check_is_sales_sql);
			$is_sales_arr=array();
			foreach ($check_is_sales as $is_sales_row) {
				$is_sales_arr[$is_sales_row[csf("trans_id")]]=$is_sales_row[csf("is_sales")];
			}
			$issueTransIdArr=array_chunk($all_issue_trans_id,999);
			$issue_job_cond=" and(";
			foreach($issueTransIdArr as $issue_trans_id)
			{
				if($issue_job_cond==" and(") $issue_job_cond.=" c.trans_id in(".implode(',',$issue_trans_id).")"; else $issue_job_cond.=" or c.trans_id in(".implode(',',$issue_trans_id).")";
			}
			$issue_job_cond.=")";

			if($issue_job_cond == " and()"){
				$issue_job_cond = "";
			}*/

			if(count($all_issue_trans_id)>0)
			{
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 770, 1,$all_issue_trans_id, $empty_arr);
			}


			$po_details_sql=" select a.id as job_id, a.job_no, a.style_ref_no, a.buyer_name, b.id as po_id, b.po_number, b.grouping, c.trans_id, c.is_sales
			from wo_po_details_master a, wo_po_break_down b, order_wise_pro_details c, GBL_TEMP_ENGINE d
			where a.job_no=b.job_no_mst and b.id=c.po_breakdown_id and c.trans_id=d.ref_val and d.REF_FROM=1 and a.status_active=1 and b.status_active=1 and c.status_active=1";
			$po_details=sql_select($po_details_sql);
			$jobIssueData=$bookingData=array();
			foreach($po_details as $row)
			{
				$jobIssueData[$row[csf("trans_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
				$jobIssueData[$row[csf("trans_id")]]["buyer_name"]=$row[csf("buyer_name")];
				$jobIssueData[$row[csf("trans_id")]]["po_number"]=$row[csf("po_number")];
				$jobIssueData[$row[csf("trans_id")]]["grouping"]=$row[csf("grouping")];
				$is_sales_arr[$row[csf("trans_id")]]=$row[csf("is_sales")];
			}
			unset($po_details);

			$sales_order_po_data_sql = "select a.id, a.job_no, a.sales_booking_no, a.buyer_id, a.within_group, a.style_ref_no, c.trans_id, a.booking_id
			from fabric_sales_order_mst a, order_wise_pro_details c, GBL_TEMP_ENGINE d
			where a.id=c.po_breakdown_id and c.is_sales=1 and c.trans_id=d.ref_val and d.REF_FROM=1 and a.status_active = 1 and c.status_active = 1";
			$sales_order_po_data = sql_select($sales_order_po_data_sql);
			$bookingId_duplicate_chk = array();
			$booking_id_arr = array();
			foreach ($sales_order_po_data as  $val)
			{
				$sales_order_po_array[$val[csf("trans_id")]]["order_no"] = $val[csf("job_no")];
				$sales_order_po_array[$val[csf("trans_id")]]["booking_no"] = $val[csf("sales_booking_no")];
				$sales_order_po_array[$val[csf("trans_id")]]["within_group"] = $val[csf("within_group")];
				$sales_order_po_array[$val[csf("trans_id")]]["style_ref_no"] = $val[csf("style_ref_no")];
				$sales_order_po_array[$val[csf("trans_id")]]["buyer_id"] = $val[csf("buyer_id")];

				if($val[csf("within_group")]==1)
				{
					//$sales_booking[$val[csf("sales_booking_no")]] = "'".$val[csf("sales_booking_no")]."'";

					if($bookingId_duplicate_chk[$val[csf("booking_id")]] == "")
					{
						$bookingId_duplicate_chk[$val[csf("booking_id")]] = $val[csf("booking_id")];
						array_push($booking_id_arr,$val[csf("booking_id")]);
					}
				}
			}

			unset($sales_order_po_data);

			$booking_id_arr = array_filter($booking_id_arr);
			if(!empty($booking_id_arr))
			{
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 770, 3,$booking_id_arr, $empty_arr);
				//die;
				$job_sql = sql_select("SELECT a.booking_no, b.buyer_name, b.style_ref_no
				from wo_booking_dtls a, wo_po_details_master b, gbl_temp_engine c where a.job_no=b.job_no and a.status_active=1 and b.status_active=1  and a.booking_mst_id=c.ref_val and c.ref_from=3 group by a.booking_no,b.buyer_name,b.style_ref_no");
				foreach ($job_sql as $job_row) {
					$booking_job_ar[$job_row[csf("booking_no")]]["style_ref_no"] = $job_row[csf("style_ref_no")];
					$booking_job_ar[$job_row[csf("booking_no")]]["buyer_name"] = $job_row[csf("buyer_name")];
				}
				unset($job_sql);
			}


			$checkItemArr = array();
			$balQnty = $balValue = array();
			$rcvQnty = $rcvValue = $issQnty = $issValue = 0;
			$i = 1;
			ob_start();
			?>
			<div>
				<table style="width:2150px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
					<thead>
						<tr class="form_caption" style="border:none;">
							<td colspan="22" align="center" style="border:none;font-size:16px; font-weight:bold" >Yarn Item Ledger </td>
						</tr>
						<tr style="border:none;">
							<td colspan="22" align="center" style="border:none; font-size:14px;">
								Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
							</td>
						</tr>
						<tr style="border:none;">
							<td colspan="22" align="center" style="border:none;font-size:12px; font-weight:bold">
								<? if ($from_date != "" || $to_date != "") echo "From " . change_date_format($from_date) . " To " . change_date_format($to_date) . ""; ?>
							</td>
						</tr>
						<tr>
							<td colspan="9">&nbsp;</td>
							<td colspan="12" align="center"><b>Weighted Average Method</b></td>
							<td >&nbsp;</td>
						</tr>
						<tr>
							<th width="50" rowspan="2">SL</th>
							<th width="120" rowspan="2">Buyer</th>
							<th width="100" rowspan="2">Job NO</th>
							<th width="110" rowspan="2">Order No</th>
							<th width="100" rowspan="2">Int. Ref. No</th>
							<th width="100" rowspan="2">Styles</th>
							<th width="100" rowspan="2">Store_name</th>
							<th width="80" rowspan="2">Trans Date</th>
							<th width="120" rowspan="2">Trans Ref No</th>
							<th width="100" rowspan="2">Trans Type</th>
							<th width="100" rowspan="2">Purpose</th>
							<th width="100" rowspan="2">Trans With</th>
							<th width="" colspan="3">Receive</th>
							<th width="" colspan="3">Issue</th>
							<th width="" colspan="3">Balance</th>
							<th width="" rowspan="2">Remarks</th>
						</tr>
						<tr>
							<th width="80">Qnty</th>
							<th width="80">Rate</th>
							<th width="110">Value</th>
							<th width="80">Qnty</th>
							<th width="80">Rate</th>
							<th width="110">Value</th>
							<th width="80">Qnty</th>
							<th width="80">Rate</th>
							<th width="110">Value</th>
						</tr>
					</thead>
				</table>
				<div style="width:2150px; overflow-y:scroll; max-height:250px" id="scroll_body" >
					<table style="width:2130px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body"  >
						<?
						$m = 1;
						$product_id_arr = array();
						$k = 1;
						foreach ($result as $row)
						{
							//if ($i >=4) { break;}

							$pro_id = $row[csf("prod_id")];
							$product_description = $count_arr[$row[csf('yarn_count_id')]]." ".$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]."% ".$yarn_type[$row[csf('yarn_type')]]." ".$color_library[$row[csf('color')]];
							if (!in_array($row[csf("prod_id")], $checkItemArr))
							{

								if ($i != 1) {
									?>
									<tr class="tbl_bottom">
										<td colspan="12" align="right">Total</td>
										<td><? echo number_format($rcvQnty, 2); ?></td>
										<td></td>
										<td><? echo number_format($rcvValue, 2); ?></td>
										<td><? echo number_format($issQnty, 2); ?></td>
										<td></td>
										<td><? echo number_format($issValue, 2); ?></td>
										<td>&nbsp;</td>
										<td></td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
									<!-- product wise herder -->
									<thead>
										<tr>
											<td colspan="13" style="color:blue;"><b>Product ID : <? echo $row[csf("prod_id")] . " , " . $product_description . ", Lot#" . $row[csf("lot")] . ", UOM#" . $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></b></td>
											<td colspan="6" align="center">&nbsp;</td>
										</tr>
									</thead>
									<!-- product wise herder END -->
									<?
								}
								$flag = 0;
								$opening_qnty = $opening_balance = $opening_rate = 0;
								if ($opning_bal_arr[$pro_id]['prod_id'] != "")
								{

									?>

									<tr style="background-color:#FFFFCC">
										<td colspan="16" align="right"><b>Opening Balance</b></td>
										<?
										$opening_qnty = $opning_bal_arr[$pro_id]['receive'] - $opning_bal_arr[$pro_id]['issue'];
										$opening_balance = $opning_bal_arr[$pro_id]['rcv_balance'] - $opning_bal_arr[$pro_id]['iss_balance'];
										$opening_rate = $opening_balance / $opening_qnty;
										?>
										<td width="80" align="right"><? echo number_format($opening_qnty, 2); ?></td>
										<td width="60" align="right"><? echo number_format($opening_rate, 2); ?></td>
										<td width="" align="right"><? echo number_format($opening_balance, 2); ?></td>
									</tr>

									<?
									$balQnty[$opning_bal_arr[$pro_id]['prod_id']] = $opening_qnty;
									$balValue[$opning_bal_arr[$pro_id]['prod_id']] = $opening_balance;

									$flag = 1;
									$opening_qnty = 0;
									$opening_balance = 0;
                                } // end opening balance foreach

                                $checkItemArr[$row[csf("prod_id")]] = $row[csf("prod_id")];
                                $rcvQnty = $rcvValue = $issQnty = $issValue = 0; // initialize variable
                                //$balQnty=$balValue=0;
                                $total_balQnty = 0;
                                $total_balValue = 0;
                            }
                            if ($i == 1)
                            {
                            	?>
                            	<thead>
                            		<tr>
                            			<td colspan="14" style="color:blue;"><b>Product ID : <? echo $row[csf("prod_id")] . " , " . $product_description . ", Lot#" . $row[csf("lot")] . ", UOM#" . $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></b></td>
                            			<td colspan="6" align="center"></td>
                            		</tr>
                            	</thead>
                            	<?
                            }

                            if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6)
                            	$stylecolor = 'style="color:#A61000"';
                            else
                            	$stylecolor = 'style="color:#000000"';

                            $cons_amount = $cons_amount = 0;
                            $cons_qnty = $row[csf("cons_quantity")];// - $row[csf("cons_reject_qnty")]

                            //echo $cons_qnty ."*". $row[csf("cons_rate")]."</br>";
                            $cons_amount = $cons_qnty * $row[csf("cons_rate")];

                            $is_sales = $is_sales_arr[$row[csf("id")]];
                            if($is_sales == 1)
                            {
                            	$within_group = $sales_order_po_array[$row[csf("id")]]["within_group"];
                            	$booking_no = $sales_order_po_array[$row[csf("id")]]["booking_no"];
                            	$job_no = $sales_order_po_array[$row[csf("id")]]["job_no"];
                            	$order_no = $sales_order_po_array[$row[csf("id")]]["order_no"];

                            	if($within_group == 1)
                            	{
                            		$buyer_name = $buyer_arr[$booking_job_ar[$booking_no]["buyer_name"]];
                            		$style_ref_no=$booking_job_ar[$booking_no]["style_ref_no"];
                            	}
                            	else
                            	{
                            		$buyer_name = $buyer_arr[$sales_order_po_array[$row[csf("id")]]["buyer_id"]];
                            		$style_ref_no=$sales_order_po_array[$row[csf("id")]]["style_ref_no"];
                            	}
                            }
                            else
                            {
                            	$buyer_name = $buyer_arr[$jobIssueData[$row[csf("id")]]["buyer_name"]];
                            	$job_no = $jobIssueData[$row[csf("id")]]["job_no"];
                            	$order_no = $jobIssueData[$row[csf("id")]]["po_number"];
                            	$style_ref_no = $jobIssueData[$row[csf("id")]]["style_ref_no"];
                            	$grouping = $jobIssueData[$row[csf("id")]]["grouping"];
                            	$po_status = $jobIssueData[$row[csf("id")]]["po_status"];
                            }

                            if($po_status==3)
                            {
                            	$bgcolor = "#f0ad4e";
								$title="This PO is cancel PO";
                            }
                            else if ($i % 2 == 0)
                            {
                            	$bgcolor = "#E9F3FF";
                            	$title="";
                            }
                            else
                            {
                            	$bgcolor = "#FFFFFF";
                            }

                            if($row[csf("transaction_type")] == 1)
                            {
                            	//$buyer_name=$buyer_arr[$row[csf("buyer_id")]];
                            	$buyer_name = $buyer_arr[$receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["buyer_id"]];
                            }
                            if(empty($buyer_name) && !empty($row[csf('buyer_id')]))
                            {
                            	$buyer_name = $buyer_arr[$row[csf('buyer_id')]];
                            }

                            if (!in_array($row[csf("prod_id")], $product_id_arr))
                            {

                            	?>
                            	<tr bgcolor="<? echo $bgcolor; ?>" title="<? echo $title; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            		<td width="50"><? echo $i; ?></td>
                            		<td width="120"><p><?  echo $buyer_name; ?></p></td>
                            		<td width="100" style="word-break: break-all;"><? echo $job_no; ?></td>
                            		<td width="110" style="word-break: break-all;"><? echo $order_no; ?></td>
                            		<td width="100" style="word-break: break-all;"><? echo $grouping; ?></td>
                            		<td width="100" style="word-break: break-all;"><p><?  echo $style_ref_no; ?></p></td>
                            		<td width="100"><p><? echo $store_arr[$row[csf("store_id")]]; ?></p></td>
                            		<td width="80"><? echo change_date_format($row[csf("transaction_date")]); ?></td>
                            		<td width="120">
                            			<?
										if( $row[csf("mst_id")]==0 && $row[csf("receive_basis")]==30)
										{
											echo "Adjustment";
										}
										else
										{
											if ($row[csf("transaction_type")] == 5 || $row[csf("transaction_type")] == 6) {
												echo $transMrrArr[$row[csf("mst_id")]];
											} else {
												echo $mrrArray[$row[csf("id")] . $row[csf("transaction_type")]];
											}
										}

                            			?>
                            		</td>
                            		<td width="100"><p><? echo $transaction_type[$row[csf("transaction_type")]]; ?></p></td>
                            		<?
                            		if ($row[csf("transaction_type")] == 1 ) {
                            			$issuePuposeS = $receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["receive_purpose"];
                            		} elseif ($row[csf("transaction_type")] == 2) {
                            			$issuePuposeS = $issuePupose[$row[csf("id")]];
                            		}
                            		elseif ($row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5 || $row[csf("transaction_type")] == 6) {
                            			$issuePuposeS = '';
                            		}
                            		?>
                            		<td width="100"><p><? echo $issuePuposeS ?></p></td>

                            		<?
                            		if ($row[csf("transaction_type")] == 1)
                            		{
                            			if($receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["receive_purpose_id"]==5)
                            			{
                            				$transactionWith = $supplierArr[$receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["loan_party"]];
                            			}
                            			else if( $receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["receive_purpose_id"]==6 || $receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["receive_purpose_id"]==16 )
                            			{
                            				if($receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["receive_basis"]==1)
                            				{
                            					$pay_mode = $wo_data[$wo_rcv_booking_id[$row[csf("id")]]]['pay_mode'];

	                            				if($pay_mode==3 || $pay_mode==5)
												{
													$transactionWith = $companyArr[$row[csf("supplier_id")]];
												}else{
													$transactionWith = $supplierArr[$row[csf("supplier_id")]];
												}
                            				}
                            				else if($receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["receive_basis"]==2 && $receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["receive_purpose_id"]==16)
                            				{
                            					$transactionWith = $supplierArr[$receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["knitting_supplier"]];
                            				}else{
                            					$transactionWith = $companyArr[$receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["knitting_company"]];
                            				}
                            			}
                            			else
                            			{

                            				$pay_mode = $wo_data[$wo_rcv_booking_id[$row[csf("id")]]]['pay_mode'];

                            				if($pay_mode==3 || $pay_mode==5)
											{
												$transactionWith = $companyArr[$row[csf("supplier_id")]];
											}else{
												$transactionWith = $supplierArr[$row[csf("supplier_id")]];
											}
                            			}
                            		}
                            		else if ($row[csf("transaction_type")] == 2)
									{
										if($row[csf("issue_purpose")]==5)
										{
											$transactionWith = $supplierArr[$row[csf("loan_party")]];
										}
										else
										{
											if ($row[csf("knit_dye_source")] == 1)
                            					$transactionWith = $companyArr[$row[csf("knit_dye_company")]];
											else
												$transactionWith = $supplierArr[$row[csf("knit_dye_company")]];
										}
                            		}
                            		else if ($row[csf("transaction_type")] == 3) {
                            			$transactionWith = $supplierArr[$row[csf("supplier_id")]];
                            		}
                            		else if ($row[csf("transaction_type")] == 4)
                            		{

                            			if ($issue_ret_source[$row[csf("id")] . $row[csf("transaction_type")]]["knitting_source"] == 1)
                            				$transactionWith = $companyArr[$issue_ret_source[$row[csf("id")] . $row[csf("transaction_type")]]["knitting_company"]];
                            			else
                            				$transactionWith = $supplierArr[$issue_ret_source[$row[csf("id")] . $row[csf("transaction_type")]]["knitting_supplier"]];
                            		}
                            		else if ($row[csf("transaction_type")] == 5)
                            		{
                            			$mst_id = $row[csf("mst_id")];
                            			$company_id = return_field_value("company_id", "inv_item_transfer_mst", "id=$mst_id","company_id");
                            			$transactionWith=$companyArr[$company_id];
                            		}
                            		else if ($row[csf("transaction_type")] == 6)
                            		{
                            			$mst_id = $row[csf("mst_id")];
                            			$to_company = return_field_value("to_company", "inv_item_transfer_mst", "id=$mst_id","to_company");
                            			$transactionWith=$companyArr[$to_company];
                            		}
									//echo $row[csf("transaction_type")];
                            		?>
                            		<td width="100" style="word-break: break-all;" title="<? echo $row[csf("knit_dye_company")]; ?>"><p><? echo $transactionWith; ?></p></td>
                            		<td width="80" align="right"><? if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5) echo number_format($cons_qnty, 4); ?></td>
                            		<td width="80" align="right" title="<? if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5) echo $row[csf("cons_rate")]; ?>"><? if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5) echo number_format($row[csf("cons_rate")], 2); ?></td>
                            		<td width="110" align="right"><? if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5) echo number_format($cons_amount, 2); ?></td>

                            		<td width="80" align="right"><? if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6) echo number_format($cons_qnty, 4); ?></td>
                            		<td width="80" align="right" title="<? if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6) echo $row[csf("cons_rate")]; ?>"><? if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6) echo number_format($row[csf("cons_rate")], 2); ?></td>
                            		<td width="110" align="right"><? if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6) echo number_format($cons_amount, 2); ?></td>
                            		<?
                            		$each_pro_id = array();
                            		if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5)
                            			$total_balQnty = $balQnty[$row[csf("prod_id")]] + $cons_qnty;
                            		if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6)
                            			$total_balQnty = $balQnty[$row[csf("prod_id")]] - $cons_qnty;

                            		if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5)
                            			$total_balValue = $balValue[$row[csf("prod_id")]] + $cons_amount;
                            		if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6)
                            			$total_balValue = $balValue[$row[csf("prod_id")]] - $cons_amount;


									if(number_format($total_balValue, 2, '.', '')!=0 && number_format($total_balQnty, 4, '.', '') !=0)
									{
										$bal_rate=$total_balValue/$total_balQnty;
									}
									else
									{
										$bal_rate=0;
									}
                            		?>
                            		<td width="80" align="right"><? echo number_format($total_balQnty, 4, '.', ''); ?></td>
                            		<td width="80" align="right" title="<? echo $bal_rate; ?>"><? echo number_format($bal_rate, 2); ?></td>
                            		<td width="110" align="right" title="<? echo $balValue[$row[csf("prod_id")]]."=".$cons_amount;?>"><? echo number_format($total_balValue, 2, '.', ''); ?></td>
                            		<td width="" >
										<?
										if ($row[csf("transaction_type")] == 5 || $row[csf("transaction_type")] == 6) {
											echo $transRemarksArr[$row[csf("mst_id")]];
										} else if ( $row[csf("transaction_type")] == 3) {
											echo $issueRemarks[$row[csf("id")] . $row[csf("transaction_type")]];
										}else {
											echo $row[csf("remarks")];
										}
										//$row[csf("transaction_type")] == 2 ||
										?>
									</td>
                            	</tr>
                            	<?
                            	$k++;
                            	$product_id_arr[] = $row[csf("prod_id")];
                            }
                            else
                            {

                            	?>
                            	<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            		<td width="50"><? echo $i; ?></td>
                            		<td width="120"><p><?  echo $buyer_name; ?></p></td>
                            		<td width="100" style="word-break: break-all;"><? echo $job_no; ?></td>
                            		<td width="110" style="word-break: break-all;"><? echo $order_no; ?></td>
                            		<td width="100" style="word-break: break-all;"><? echo $grouping; ?></td>
                            		<td width="100" style="word-break: break-all;"><p><?  echo $style_ref_no; ?></p></td>
                            		<td width="100"><p><? echo $store_arr[$row[csf("store_id")]]; ?></p></td>
                            		<td width="80"><? echo change_date_format($row[csf("transaction_date")]); ?></td>
                            		<td width="120">
                            			<p>
                            			<?
                            			if( $row[csf("mst_id")]==0 && $row[csf("receive_basis")]==30)
										{
											echo "Adjustment";
										}
										else
										{
											if ($row[csf("transaction_type")] == 5 || $row[csf("transaction_type")] == 6) {
												echo $transMrrArr[$row[csf("mst_id")]];
											} else {
												echo $mrrArray[$row[csf("id")] . $row[csf("transaction_type")]];
											}
										}
                            			?>
                            			</p>
                            		</td>
                            		<td width="100" title="<?echo $row[csf("transaction_type")];?>"><p><? echo $transaction_type[$row[csf("transaction_type")]]; ?></p></td>

                            		<?
                            		if ($row[csf("transaction_type")] == 1 ) {
                            			$issuePuposeS = $receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["receive_purpose"];
                            		}
									else if ($row[csf("transaction_type")] == 3 )
									{
										$issuePuposeS = $ReceiveBasisArr[$recvMRRId[$row[csf("id")] . $row[csf("transaction_type")]]]["receive_purpose"];
									}
									else if ($row[csf("transaction_type")] == 2) {
                            			$issuePuposeS = $issuePupose[$row[csf("id")]];
                            		}
                            		else if ($row[csf("transaction_type")] == 4)
                            		{
                            			$issuePuposeS = $receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["receive_purpose"];
                            		}
                            		else if ($row[csf("transaction_type")] == 5 || $row[csf("transaction_type")] == 6 ) {
                            			$issuePuposeS = '';
                            		}

                            		?>
                            		<td width="100"><p><? echo $issuePuposeS ?></p></td>
                            		<?
                            		if ($row[csf("transaction_type")] == 1)
                            		{
                            			if($receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["receive_purpose_id"]==5)
                            			{
                            				$transactionWith = $supplierArr[$receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["loan_party"]];
                            			}
                            			else if( $receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["receive_purpose_id"]==6 || $receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["receive_purpose_id"]==16 )
                            			{
                            				if($receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["receive_basis"]==1)
                            				{
                            					$pay_mode = $wo_data[$wo_rcv_booking_id[$row[csf("id")]]]['pay_mode'];

	                            				if($pay_mode==3 || $pay_mode==5)
												{
													$transactionWith = $companyArr[$row[csf("supplier_id")]];
												}else{
													$transactionWith = $supplierArr[$row[csf("supplier_id")]];
												}
                            				}
                            				else if($receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["receive_basis"]==2 && $receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["receive_purpose_id"]==16)
                            				{
                            					$transactionWith = $supplierArr[$receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["knitting_supplier"]];
                            				}else{
                            					$transactionWith = $companyArr[$receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["knitting_company"]];
                            				}
                            			}
                            			else
                            			{
                            				$pay_mode = $wo_data[$wo_rcv_booking_id[$row[csf("id")]]]['pay_mode'];

                            				if($pay_mode==3 || $pay_mode==5)
											{
												$transactionWith = $companyArr[$row[csf("supplier_id")]];
											}else{
												$transactionWith = $supplierArr[$row[csf("supplier_id")]];
											}
                            			}
                            		}
                            		else if ($row[csf("transaction_type")] == 2 )
									{
										if($row[csf("issue_purpose")]==5)
										{
											$transactionWith = $supplierArr[$row[csf("loan_party")]];
										}
										else
										{
											if ($row[csf("knit_dye_source")] == 1)
                            					$transactionWith = $companyArr[$row[csf("knit_dye_company")]];
											else
												$transactionWith = $supplierArr[$row[csf("knit_dye_company")]];
										}
                            		}
                            		else if ($row[csf("transaction_type")] == 3) {
                            			$transactionWith = $supplierArr[$row[csf("supplier_id")]];
                            		}
                            		else if ($row[csf("transaction_type")] == 4)
                            		{
                            			if ($issue_ret_source[$row[csf("id")] . $row[csf("transaction_type")]]["knitting_source"] == 1)
                            				$transactionWith = $companyArr[$issue_ret_source[$row[csf("id")] . $row[csf("transaction_type")]]["knitting_company"]];
                            			else
                            				$transactionWith = $supplierArr[$issue_ret_source[$row[csf("id")] . $row[csf("transaction_type")]]["knitting_supplier"]];
                            		}
                            		else if ($row[csf("transaction_type")] == 5)
                            		{
                            			$mst_id = $row[csf("mst_id")];
                            			$company_id = return_field_value("company_id", "inv_item_transfer_mst", "id=$mst_id","company_id");
                            			$transactionWith=$companyArr[$company_id];
                            		}
                            		else if ($row[csf("transaction_type")] == 6)
                            		{
                            			$mst_id = $row[csf("mst_id")];
                            			$to_company = return_field_value("to_company", "inv_item_transfer_mst", "id=$mst_id","to_company");
                            			$transactionWith=$companyArr[$to_company];
                            		}
                            		?>

                            		<td width="100" style="word-break: break-all;"><p><? echo $transactionWith; ?></p></td>
                            		<td width="80" align="right"><? if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5) echo number_format($cons_qnty, 4); ?></td>
                            		<td width="80" align="right" title="<? if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5) echo $row[csf("cons_rate")]; ?>"><? if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5) echo number_format($row[csf("cons_rate")], 2); ?></td>
                            		<td width="110" align="right"><? if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5) echo number_format($cons_amount, 2); ?></td>

                            		<td width="80" align="right"><? if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6) echo number_format($cons_qnty, 4); ?></td>
                            		<td width="80" align="right" title="<? if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6) echo $row[csf("cons_rate")]; ?>"><? if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6) echo number_format($row[csf("cons_rate")], 2); ?></td>
                            		<td width="110" align="right"><? if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6) echo number_format($cons_amount, 2); ?></td>
                            		<?
                            		$each_pro_id = array();
									$total_balQnty = $total_balQnty*1;
                            		$total_balValue = $total_balValue*1;
                            		if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5)
                            			$total_balQnty += $cons_qnty;
                            		if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6)
                            			$total_balQnty -= $cons_qnty;

                            		if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5)
                            			$total_balValue += $cons_amount;
                            		if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6)
                            			$total_balValue -= $cons_amount;

									if(number_format($total_balValue, 2, '.', '')!=0 && number_format($total_balQnty, 4, '.', '') !=0)
									{
										$bal_rate=$total_balValue/$total_balQnty;
									}
									else
									{
										$bal_rate=0;
									}
                            		?>
                            		<td width="80" align="right"><? echo number_format($total_balQnty, 4, '.', ''); ?></td>
                            		<td width="80" align="right" title="<? echo $bal_rate; ?>"><? echo number_format($bal_rate, 2); ?></td>
                            		<td width="110" align="right" title="<? echo number_format($test_bal, 2)."=".number_format($cons_amount,2); ?>"><? echo number_format($total_balValue, 2, '.', ''); ?></td>
                            		<td width="">
										<?
										if ($row[csf("transaction_type")] == 5 || $row[csf("transaction_type")] == 6) {
											echo $transRemarksArr[$row[csf("mst_id")]];
										}else if ($row[csf("transaction_type")] == 3) {
											echo $issueRemarks[$row[csf("id")] . $row[csf("transaction_type")]];
										}else {
											echo $row[csf("remarks")];
										}
										?>
									</td>
                            	</tr>
                            	<?
                            }
                            $i++;
                            //total sum START-----------------------
                            if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5)
                            	$rcvQnty += $cons_qnty;
                            if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5)
                            	$rcvValue += $cons_amount;

                            if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6)
                            	$issQnty += $cons_qnty;

                            if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6)
                            	$issValue += $cons_amount;
                        }

                        ?>
                        <tr class="tbl_bottom">
                        	<td colspan="12" align="right">Total</td>
                        	<td align="right" ><? echo number_format($rcvQnty, 4); ?></td>
                        	<td>&nbsp;</td>
                        	<td align="right" ><? echo number_format($rcvValue, 2); ?></td>
                        	<td align="right" ><? echo number_format($issQnty, 4); ?></td>
                        	<td>&nbsp;</td>
                        	<td align="right" ><? echo number_format($issValue, 2); ?></td>
                        	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        </tr>
                    </table>
                </div>
            </div>
            <?
        }

        if ($cbo_method == 1 || $cbo_method == 2)
        {
			//FIFO=1 //LIFO=2 ################################################################################
			$opning_bal_arr = array();
        	if ($from_date != "" && $to_date != "") {
        		if ($db_type == 2)
        			$from_date = date("j-M-Y", strtotime($from_date));
        		if ($db_type == 0)
        			$from_date = change_date_format($from_date, 'yyyy-mm-dd');
                //for opening balance
        		if($variable_store_wise_rate==1)
				{
					$sqlTR = "select  prod_id, SUM(CASE WHEN transaction_type in (1,4,5) THEN cons_quantity ELSE 0 END) as receive,
					SUM(CASE WHEN transaction_type in (2,3,6) THEN cons_quantity ELSE 0 END) as issue,
					SUM(CASE WHEN transaction_type in (1,4,5) THEN store_amount ELSE 0 END) as rcv_balance,
					SUM(CASE WHEN transaction_type in (2,3,6) THEN store_amount ELSE 0 END) as iss_balance
					from inv_transaction
					where transaction_date < '" . $from_date . "' and status_active=1 and is_deleted=0 $storeCond group by prod_id";
				}
				else
				{
					$sqlTR = "select  prod_id, SUM(CASE WHEN transaction_type in (1,4,5) THEN cons_quantity ELSE 0 END) as receive,
					SUM(CASE WHEN transaction_type in (2,3,6) THEN cons_quantity ELSE 0 END) as issue,
					SUM(CASE WHEN transaction_type in (1,4,5) THEN cons_amount ELSE 0 END) as rcv_balance,
					SUM(CASE WHEN transaction_type in (2,3,6) THEN cons_amount ELSE 0 END) as iss_balance
					from inv_transaction
					where transaction_date < '" . $from_date . "' and status_active=1 and is_deleted=0 $storeCond group by prod_id";
				}

        		$trResult = sql_select($sqlTR);
				foreach ($trResult as $row) {
					$opning_bal_arr[$row[csf("prod_id")]]["prod_id"] = $row[csf("prod_id")];
					$opning_bal_arr[$row[csf("prod_id")]]["receive"] = $row[csf("receive")];
					$opning_bal_arr[$row[csf("prod_id")]]["issue"] = $row[csf("issue")];
					$opning_bal_arr[$row[csf("prod_id")]]["rcv_balance"] = $row[csf("rcv_balance")];
					$opning_bal_arr[$row[csf("prod_id")]]["iss_balance"] = $row[csf("iss_balance")];
				}
				unset($trResult);
        	}

			//
        	$store_cond="";
        	if($cbo_store_name>0) $store_cond=" and a.store_id=$cbo_store_name";
			if($variable_store_wise_rate==1)
			{
				$sql = "select a.id, a.prod_id, a.store_id, a.transaction_date, a.transaction_type, a.cons_quantity, a.store_rate as cons_rate, a.store_amount as cons_amount, a.remarks, b.supplier_id, b.product_name_details, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_type, b.color, b.unit_of_measure, b.lot, c.knit_dye_source, c.knit_dye_company, c.issue_purpose
				from inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3,6), product_details_master b
				where a.prod_id in ($txt_product_id) and a.prod_id=b.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 $search_cond $store_cond
				order by a.prod_id,a.transaction_date,a.id ASC";
			}
			else
			{
				$sql = "select a.id, a.prod_id, a.store_id, a.transaction_date, a.transaction_type, a.cons_quantity, a.cons_rate, a.cons_amount, a.remarks, b.supplier_id, b.product_name_details,b.yarn_count_id,b.yarn_comp_type1st,b.yarn_comp_percent1st,b.yarn_type,b.color, b.unit_of_measure, b.lot, c.knit_dye_source, c.knit_dye_company, c.issue_purpose
				from inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3,6), product_details_master b
				where a.prod_id in ($txt_product_id) and a.prod_id=b.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 $search_cond $store_cond
				order by a.prod_id,a.transaction_date,a.id ASC";
			}


            //echo $sql;die;
        	$result = sql_select($sql);

        	$all_issue_trans_id=array();
        	foreach($result as $row)
        	{
        		if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6)
				{
					$all_issue_trans_id[$row[csf("id")]]=$row[csf("id")];
				}
        	}

        	/*$issueTransIdArr=array_chunk($all_issue_trans_id,999);
        	$issue_job_cond=" and(";
        	foreach($issueTransIdArr as $issue_trans_id)
        	{
        		if($issue_job_cond==" and(") $issue_job_cond.=" c.trans_id in(".implode(',',$issue_trans_id).")"; else $issue_job_cond.=" or c.trans_id in(".implode(',',$issue_trans_id).")";
        	}
        	$issue_job_cond.=")";*/

			if(count($all_issue_trans_id)>0)
			{
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 770, 1,$all_issue_trans_id, $empty_arr);
			}



        	$issue_job_order=sql_select("select a.id as job_id, a.job_no, a.style_ref_no, a.buyer_name, b.id as po_id, b.po_number, b.grouping, c.trans_id
			from wo_po_details_master a, wo_po_break_down b, order_wise_pro_details c, GBL_TEMP_ENGINE d
			where a.job_no=b.job_no_mst and b.id=c.po_breakdown_id and c.trans_id=d.ref_val and d.REF_FROM=1 and c.trans_type=2 and c.entry_form=3");
        	$jobIssueData=array();
        	foreach($issue_job_order as $row)
        	{
        		$jobIssueData[$row[csf("trans_id")]]["job_id"]=$row[csf("job_id")];
        		$jobIssueData[$row[csf("trans_id")]]["job_no"]=$row[csf("job_no")];
        		$jobIssueData[$row[csf("trans_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
        		$jobIssueData[$row[csf("trans_id")]]["buyer_name"]=$row[csf("buyer_name")];
        		$jobIssueData[$row[csf("trans_id")]]["po_id"]=$row[csf("po_id")];
        		$jobIssueData[$row[csf("trans_id")]]["po_number"]=$row[csf("po_number")];
        		$jobIssueData[$row[csf("trans_id")]]["grouping"]=$row[csf("grouping")];
        	}

        	$checkItemArr = array();
        	$balQnty = $balValue = 0;
        	$rcvQnty = $rcvValue = $issQnty = $issValue = 0;
        	$balMRRArray = $qntyMRRArray = $amtMRRArray = array();
        	$deductQntyArr = $deductAmtArr = array();
        	$i = 1;
        	ob_start();
        	?>
        	<div>
        		<table style="width:2040px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
        			<thead>
        				<tr class="form_caption" style="border:none;">
        					<td colspan="22" align="center" style="border:none;font-size:16px; font-weight:bold" >Yarn Item Ledger </td>
        				</tr>
        				<tr style="border:none;">
        					<td colspan="22" align="center" style="border:none; font-size:14px;">
        						Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
        					</td>
        				</tr>
        				<tr style="border:none;">
        					<td colspan="22" align="center" style="border:none;font-size:12px; font-weight:bold">
        						<? if ($from_date != "" || $to_date != "") echo "From " . change_date_format($from_date) . " To " . change_date_format($to_date) . ""; ?>
        					</td>
        				</tr>
        				<tr>
        					<td colspan="9">&nbsp;</td>
        					<td colspan="12" align="center"><b>Weighted Average Method</b></td>
        					<td >&nbsp;</td>
        				</tr>
        				<tr>
        					<th width="50" rowspan="2">SL</th>
        					<th width="120" rowspan="2">Buyer</th>
        					<th width="100" rowspan="2">Job No</th>
        					<th width="110" rowspan="2">Order No</th>
        					<th width="100" rowspan="2">Int. Ref. No</th>
        					<th width="100" rowspan="2">Styles</th>
        					<th width="100" rowspan="2">Store</th>
        					<th width="80" rowspan="2">Trans Date</th>
        					<th width="120" rowspan="2">Trans Ref No</th>
        					<th width="100" rowspan="2">Trans Type</th>
        					<th width="100" rowspan="2">Purpose</th>
        					<th width="100" rowspan="2">Trans With</th>
        					<th width="" colspan="3">Receive</th>
        					<th width="" colspan="3">Issue</th>
        					<th width="" colspan="3">Balance</th>
        					<th width="" rowspan="2">Remarks</th>
        				</tr>

        				<tr>
        					<th width="80">Qnty</th>
        					<th width="60">Rate</th>
        					<th width="110">Value</th>
        					<th width="80">Qnty</th>
        					<th width="60">Rate</th>
        					<th width="110">Value</th>
        					<th width="80">Qnty</th>
        					<th width="60">Rate</th>
        					<th width="100">Value</th>
        				</tr>
        			</thead>
        		</table>
        		<div style="width:2040px; overflow-y:scroll; max-height:250px" id="scroll_body" >
        			<table style="width:2020px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body"  >
        				<?
        				$m = 1;
        				$product_id_arr = array();
        				$k = 1;

        				foreach ($result as $row)
        				{
        					$pro_id = $row[csf("prod_id")];
        					$product_description = $count_arr[$row[csf('yarn_count_id')]]." ".$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]."% ".$yarn_type[$row[csf('yarn_type')]]." ".$color_library[$row[csf('color')]];
                            //check items new or not and print product description-------------------
        					if (!in_array($row[csf("prod_id")], $checkItemArr))
        					{

                                if ($i != 1)
                                { // product wise sum/total here------------
                                	?>
                                	<tr class="tbl_bottom">
                                		<td colspan="12" align="right">Total</td>
                                		<td><? echo number_format($rcvQnty, 2); ?></td>
                                		<td></td>
                                		<td><? echo number_format($rcvValue, 2); ?></td>
                                		<td><? echo number_format($issQnty, 2); ?></td>
                                		<td></td>
                                		<td><? echo number_format($issValue, 2); ?></td>
                                		<td>&nbsp;</td>
                                		<td></td>
                                		<td>&nbsp;</td>
                                		<td>&nbsp;</td>
                                	</tr>

                                	<!-- product wise herder -->
                                	<thead>
                                		<tr>
                                			<td colspan="19" style="color:blue;"><b>Product ID : <? echo $row[csf("prod_id")] . " , " . $product_description . ", Lot#" . $row[csf("lot")] . ", UOM#" . $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></b></td>
                                			<td colspan="6" align="center">&nbsp;</td>
                                		</tr>
                                	</thead>
                                	<!-- product wise herder END -->
                                	<?
                                }


                                //opening balance query-----------
                                /* if( $from_date!="" && $to_date!="" )
                                  {
                                  if($db_type==2) $from_date=date("j-M-Y",strtotime($from_date));
                                  if($db_type==0) $from_date=change_date_format($from_date, 'yyyy-mm-dd');
                                  //for opening balance
                                  $sqlTR = "select prod_id, SUM(CASE WHEN transaction_type in (1,4) THEN cons_quantity ELSE 0 END) as receive,
                                  SUM(CASE WHEN transaction_type in (2,3) THEN cons_quantity ELSE 0 END) as issue,
                                  SUM(CASE WHEN transaction_type in (1,4) THEN cons_amount ELSE 0 END) as rcv_balance,
                                  SUM(CASE WHEN transaction_type in (2,3) THEN cons_amount ELSE 0 END) as iss_balance
                                  from inv_transaction
                                  where prod_id in ($pro_id) and transaction_date < '".$from_date."' and status_active=1 and is_deleted=0 group by prod_id,id";
                                  $trResult = sql_select($sqlTR);
                              } */
                                //echo $sqlTR ;die;



                              $flag = 0;
                              $opening_qnty = $opening_balance = $opening_rate = 0;
                              if ($opning_bal_arr[$pro_id]['prod_id'] != "") {
                              	?>

                              	<tr style="background-color:#FFFFCC">
                              		<td colspan="17" align="right"><b>Opening Balance</b></td>
                              		<?
                              		$opening_qnty = $opning_bal_arr[$pro_id]['receive'] - $opning_bal_arr[$pro_id]['issue'];
                              		$opening_balance = $opning_bal_arr[$pro_id]['rcv_balance'] - $opning_bal_arr[$pro_id]['iss_balance'];
                              		$opening_rate = $opening_balance / $opening_qnty;
                              		?>
                              		<td width="80" align="right"><? echo number_format($opening_qnty, 2); ?></td>
                              		<td width="60" align="right"><? echo number_format($opening_rate, 2); ?></td>
                              		<td width="" align="right"><? echo number_format($opening_balance, 2); ?></td>
                              	</tr>

                              	<?
                              	$balQnty[$opning_bal_arr[$pro_id]['prod_id']] = $opening_qnty;
                              	$balValue [$opning_bal_arr[$pro_id]['prod_id']] = $opening_balance;

                              	$flag = 1;
                              	$opening_qnty = 0;
                              	$opening_balance = 0;
                                } // end opening balance foreach

                                $checkItemArr[$row[csf("prod_id")]] = $row[csf("prod_id")];
                                $rcvQnty = $rcvValue = $issQnty = $issValue = 0; // initialize variable
                                //$balQnty=$balValue=0;
                                $total_balQnty = 0;
                                $total_balValue = 0;
                            }
                            //var_dump($balQnty);
                            //print product name details header---------------------------
                            if ($i == 1) {
                            	?>
                            	<thead>
                            		<tr>
                            			<td colspan="16" style="color:blue;"><b>Product ID : <? echo $row[csf("prod_id")] . " , " . $product_description . ", Lot#" . $row[csf("lot")] . ", UOM#" . $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></b></td>
                            			<td colspan="6" align="center"></td>

                            		</tr>
                            	</thead>
                            	<?
                            }
                            //print product name details header END -------------------------


                            /* if($flag==1) // adjust opening balance
                              {
                              $balQnty = $balQnty+$opening_qnty;
                              $balValue = $balValue+$opening_balance;
                              }
                              else
                              {
                              $flag=0;
                          } */


                          if ($i % 2 == 0)
                          	$bgcolor = "#E9F3FF";
                          else
                          	$bgcolor = "#FFFFFF";
                          if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3)
                          	$stylecolor = 'style="color:#A61000"';
                          else
                          	$stylecolor = 'style="color:#000000"';
                            //var_dump($balQnty);
                            /* if(!in_array($row[csf("prod_id")],$each_pro_id))
                            { */

                            	$cons_amount = $cons_amount = 0;
                            	$cons_qnty = $row[csf("cons_quantity")];// + $row[csf("cons_reject_qnty")]
                            	$cons_amount = $cons_qnty * $row[csf("cons_rate")];

                            	if (!in_array($row[csf("prod_id")], $product_id_arr))
                            	{
                            		?>
                            		<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            			<td width="50"><? echo $i; ?></td>
                            			<td width="120"><p><?  if ($row[csf("transaction_type")] == 2) echo  $buyer_arr[$jobIssueData[$row[csf("id")]]["buyer_name"]]; else echo "&nbsp;" ?></p></td>
                            			<td width="100" style="word-break: break-all;"><p><?  if ($row[csf("transaction_type")] == 2) echo  $jobIssueData[$row[csf("id")]]["job_no"]; else echo "&nbsp;" ?></p></td>
                            			<td width="110" style="word-break: break-all;"><p><?  if ($row[csf("transaction_type")] == 2) echo  $jobIssueData[$row[csf("id")]]["po_number"]; else echo "&nbsp;" ?></p></td>
                            			<td width="100" style="word-break: break-all;"><p><?  if ($row[csf("transaction_type")] == 2) echo  $jobIssueData[$row[csf("id")]]["grouping"]; else echo "&nbsp;" ?></p></td>
                            			<td width="100" style="word-break: break-all;"><p><?  if ($row[csf("transaction_type")] == 2) echo  $jobIssueData[$row[csf("id")]]["style_ref_no"]; else echo "&nbsp;" ?></p></td>
                            			<td width="100"><p><? echo $store_arr[$row[csf("store_id")]]; ?></p></td>
                            			<td width="80"><? echo change_date_format($row[csf("transaction_date")]); ?></td>
                            			<td width="120">
                            				<p>
                            					<?
                            					if ($row[csf("transaction_type")] == 5 || $row[csf("transaction_type")] == 6) {
                            						echo $transMrrArr[$row[csf("mst_id")]];
                            					} else {
                            						echo $mrrArray[$row[csf("id")] . $row[csf("transaction_type")]];
                            					}
                            					?>
                            				</p>
                            			</td>
                            			<td width="100"><p><? echo $transaction_type[$row[csf("transaction_type")]]; ?></p></td>

                            			<?
                            			if ($row[csf("transaction_type")] == 1 )
                            			{
                            				$issuePuposeS = $receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["receive_purpose"];
                            			}
                            			elseif ($row[csf("transaction_type")] == 2)
                            			{
                            				$issuePuposeS = $issuePupose[$row[csf("id")]];
                            			}
                            			elseif ($row[csf("transaction_type")] == 4)
                            			{
                            				$issuePuposeS = '';
                            			}
                            			?>
                            			<td width="100"><p><? echo $issuePuposeS ?></p></td>

                            			<?
                            			if ($row[csf("transaction_type")] == 2)
										{
											if($row[csf("issue_purpose")]==5)
											{
												$transactionWith = $supplierArr[$row[csf("loan_party")]];
											}
											else
											{
												if ($row[csf("knit_dye_source")] == 1)
													$transactionWith = $companyArr[$row[csf("knit_dye_company")]];
												else
													$transactionWith = $supplierArr[$row[csf("knit_dye_company")]];
											}
                            			}
                            			else if ($row[csf("transaction_type")] == 3) {
                            				$transactionWith = $supplierArr[$row[csf("supplier_id")]];
                            			}
                            			else if ($row[csf("transaction_type")] == 1) {
                            				$transactionWith = $supplierArr[$receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["knitting_supplier"]];
                            			} else if ($row[csf("transaction_type")] == 4) {
                            				if ($issue_ret_source[$row[csf("id")] . $row[csf("transaction_type")]]["knitting_source"] == 1)
                            					$transactionWith = $companyArr[$issue_ret_source[$row[csf("id")] . $row[csf("transaction_type")]]["knitting_company"]];
                            				else
                            					$transactionWith = $supplierArr[$issue_ret_source[$row[csf("id")] . $row[csf("transaction_type")]]["knitting_supplier"]];
                            			}
                            			?>
                            			<td width="100" style="word-break: break-all;"><p><? echo $transactionWith; ?></p></td>
                            			<td width="80" align="right"><? if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5) echo number_format($cons_qnty, 2); ?></td>
                            			<td width="60" align="right"><? if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5) echo number_format($row[csf("cons_rate")], 2); ?></td>
                            			<td width="110" align="right"><? if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5) echo number_format($cons_amount, 2); ?></td>

                            			<td width="80" align="right"><? if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6) echo number_format($cons_qnty, 2); ?></td>
                            			<td width="60" align="right"><? if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6) echo number_format($row[csf("cons_rate")], 2); ?></td>
                            			<td width="110" align="right"><? if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6) echo number_format($cons_amount, 2); ?></td>
                            			<?
                            			$each_pro_id = array();
                            			if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5)
                            				$total_balQnty = $balQnty[$row[csf("prod_id")]] + $cons_qnty;
                            			if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6)
                            				$total_balQnty = $balQnty[$row[csf("prod_id")]] - $cons_qnty;

                            			if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5)
                            				$total_balValue = $balValue[$row[csf("prod_id")]] + $cons_amount;
                            			if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6)
                            				$total_balValue = $balValue[$row[csf("prod_id")]] - $cons_amount;

                                    //if($m==1) $total_balQnty= $total_balQnty+$balQnty[$row[csf("prod_id")]]; else $total_balQnty+=$total_balQnty;
                                    //if($m==1) $total_balValue= $total_balValue+$balValue[$row[csf("prod_id")]]; else $total_balValue+=$total_balValue;
                                    //$total_balQnty=number_format($total_balQnty,2,'.','');
                                    //$total_balValue=number_format($total_balValue,2,'.','');

                            			//$total_balQnty = number_format($total_balQnty, 4, '.', '');
                            			//$total_balValue = number_format($total_balValue, 2, '.', '');
                            			if ($total_balQnty < 0.00009) {
                            				$bal_rate = 0;
                            				$total_balValue = 0.00;
                            			} else {
                            				$bal_rate = $total_balValue / $total_balQnty;
                            			}
                            			?>
                            			<td width="80" align="right"><? echo number_format($total_balQnty, 4, '.', ''); ?></td>
                            			<td width="60" align="right"><? echo number_format($bal_rate, 2); ?></td>
                            			<td width="100" align="right"><? echo number_format($total_balValue, 2, '.', ''); ?></td>
                            			<td width="" >
											<?
											if ($row[csf("transaction_type")] == 5 || $row[csf("transaction_type")] == 6) {
												echo $transRemarksArr[$row[csf("mst_id")]];
											}else if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3) {
												echo $issueRemarks[$row[csf("id")] . $row[csf("transaction_type")]];
											}else {
												echo $row[csf("remarks")];
											}
											//echo $row[csf("remarks")];
											?>
										</td>
                            		</tr>
                            		<?
                            		$k++;
                            		$product_id_arr[] = $row[csf("prod_id")];
                            	}
                            	else
                            	{
                            		?>
                            		<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            			<td width="50"><? echo $i; ?></td>
                            			<td width="120"><p><?  if ($row[csf("transaction_type")] == 2) echo  $buyer_arr[$jobIssueData[$row[csf("id")]]["buyer_name"]]; else echo "&nbsp;" ?></p></td>
                            			<td width="100" style="word-break: break-all;"><p><?  if ($row[csf("transaction_type")] == 2) echo  $jobIssueData[$row[csf("id")]]["job_no"]; else echo "&nbsp;" ?></p></td>
                            			<td width="110" style="word-break: break-all;"><p><?  if ($row[csf("transaction_type")] == 2) echo  $jobIssueData[$row[csf("id")]]["po_number"]; else echo "&nbsp;" ?></p></td>
                            			<td width="100" style="word-break: break-all;"><p><?  if ($row[csf("transaction_type")] == 2) echo  $jobIssueData[$row[csf("id")]]["grouping"]; else echo "&nbsp;" ?></p></td>
                            			<td width="100" style="word-break: break-all;"><p><?  if ($row[csf("transaction_type")] == 2) echo  $jobIssueData[$row[csf("id")]]["style_ref_no"]; else echo "&nbsp;" ?></p></td>
                            			<td width="100"><p><? echo $store_arr[$row[csf("store_id")]]; ?></p></td>
                            			<td width="80"><? echo change_date_format($row[csf("transaction_date")]); ?></td>
                            			<td width="120">
                            				<p>
                            					<?
                            					if ($row[csf("transaction_type")] == 5 || $row[csf("transaction_type")] == 6) {
                            						echo $transMrrArr[$row[csf("mst_id")]];
                            					} else {
                            						echo $mrrArray[$row[csf("id")] . $row[csf("transaction_type")]];
                            					}
                            					?>
                            				</p>
                            			</td>
                            			<td width="100" title="<?echo $row[csf("transaction_type")];?>"><p><? echo $transaction_type[$row[csf("transaction_type")]]; ?></p></td>

                            			<?
                            			if ($row[csf("transaction_type")] == 1 ) {
                            				$issuePuposeS = $receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["receive_purpose"];
                            			} else if ($row[csf("transaction_type")] == 2) {
                            				$issuePuposeS = $issuePupose[$row[csf("id")]];
                            			}
                            			else if ($row[csf("transaction_type")] == 4) {
                            				$issuePuposeS = '';
                            			}
                            			?>
                            			<td width="100"><p><? echo $issuePuposeS ?></p></td>
                            			<?
                            			if ($row[csf("transaction_type")] == 2 )
										{
											if($row[csf("issue_purpose")]==5)
											{
												$transactionWith = $supplierArr[$row[csf("loan_party")]];
											}
											else
											{
												if ($row[csf("knit_dye_source")] == 1)
													$transactionWith = $companyArr[$row[csf("knit_dye_company")]];
												else
													$transactionWith = $supplierArr[$row[csf("knit_dye_company")]];
											}
                            			}
                            			else if ($row[csf("transaction_type")] == 3) {
                            				$transactionWith = $supplierArr[$row[csf("supplier_id")]];
                            			}

                            			else if ($row[csf("transaction_type")] == 1) {
                            				$transactionWith = $supplierArr[$receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["knitting_supplier"]];
                            			} else if ($row[csf("transaction_type")] == 4) {
                            				if ($issue_ret_source[$row[csf("id")] . $row[csf("transaction_type")]]["knitting_source"] == 1)
                            					$transactionWith = $companyArr[$issue_ret_source[$row[csf("id")] . $row[csf("transaction_type")]]["knitting_company"]];
                            				else
                                        //$transactionWith =  $supplierArr[$receive_source[$row[csf("id")].$row[csf("transaction_type")]]["knitting_company"]];
                            					$transactionWith = $supplierArr[$issue_ret_source[$row[csf("id")] . $row[csf("transaction_type")]]["knitting_supplier"]];
                            			}
                            			?>

                            			<td width="100" style="word-break: break-all;"><p><? echo $transactionWith; ?></p></td>
                            			<td width="80" align="right"><? if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5) echo number_format($cons_qnty, 2); ?></td>
                            			<td width="60" align="right"><? if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5) echo number_format($row[csf("cons_rate")], 2); ?></td>
                            			<td width="110" align="right"><? if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5) echo number_format($cons_amount, 2); ?></td>

                            			<td width="80" align="right"><? if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6) echo number_format($cons_qnty, 2); ?></td>
                            			<td width="60" align="right"><? if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6) echo number_format($row[csf("cons_rate")], 2); ?></td>
                            			<td width="110" align="right"><? if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6) echo number_format($cons_amount, 2); ?></td>
                            			<?
                            			$each_pro_id = array();
                            			$total_balQnty = str_replace(",", "", $total_balQnty);
                            			$total_balValue = str_replace(",", "", $total_balValue);
										$test_bal=$total_balValue;
                            			if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5)
                            				$total_balQnty += $cons_qnty;
                            			if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6)
                            				$total_balQnty -= $cons_qnty;

                            			if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5)
                            				$total_balValue += $cons_amount;
                            			if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6)
                            				$total_balValue -= $cons_amount;

                                    //if($m==1) $total_balQnty= $total_balQnty+$balQnty[$row[csf("prod_id")]]; else $total_balQnty+=$total_balQnty;
                                    //if($m==1) $total_balValue= $total_balValue+$balValue[$row[csf("prod_id")]]; else $total_balValue+=$total_balValue;
                                    //if(
                                    //$total_balQnty=number_format($total_balQnty,2,'.','');
                                    //$total_balValue=number_format($total_balValue,2,'.','');
                                    //$x=$total_balValue/$total_balQnty;

                            			//$total_balQnty = number_format($total_balQnty, 4, '.', '');
                            			//$total_balValue = number_format($total_balValue, 2, '.', '');
                            			if ($total_balQnty < 0.00009) {
                            				$bal_rate = 0;
                            				$total_balValue = 0.00;
                            			} else {
                            				$bal_rate = $total_balValue / $total_balQnty;
                            			}
                            			?>
                            			<td width="80" align="right"><? echo number_format($total_balQnty, 4, '.', ''); ?></td>
                            			<td width="60" align="right"><? echo number_format($bal_rate, 2); ?></td>
                            			<td width="100" align="right" title="<? echo "";?>"><? echo number_format($total_balValue, 2, '.', ''); ?></td>
                            			<td width="" >
											<?
											if ($row[csf("transaction_type")] == 5 || $row[csf("transaction_type")] == 6) {
												echo $transRemarksArr[$row[csf("mst_id")]];
											}else if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3) {
												echo $issueRemarks[$row[csf("id")] . $row[csf("transaction_type")]];
											}else {
												echo $row[csf("remarks")];
											}

											?>
										</td>
                            		</tr>
                            		<?
									if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5)
										$test_bal += $cons_amount;
									else
                            			$test_bal -= $cons_amount;
                            	}

                            	//$total_balQnty=0;
                            	//$total_balValue=0;

                            	$i++;

                            	//total sum START-----------------------
                            	if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5)
                            		$rcvQnty += $cons_qnty;
                            	if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5)
                            		$rcvValue += $cons_amount;

                            	if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6)
                            		$issQnty += $cons_qnty;
                            	if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6)
                            		$issValue += $cons_amount;

								/* 		//total sum END-----------------------
								$each_pro_id[$row[csf("prod_id")]]=$row[csf("prod_id")];
								$m++;
								}
								$total_balQnty=0;
								$total_balValue=0; */
                        }
                        ?> <!-- END FOREACH LOOP-->


                        <tr class="tbl_bottom">
                            <td colspan="12" align="right">Total</td>
                            <td align="right" ><? echo number_format($rcvQnty, 2); ?></td>
                            <td>&nbsp;</td>
                            <td align="right" ><? echo number_format($rcvValue, 2); ?></td>
                            <td align="right" ><? echo number_format($issQnty, 2); ?></td>
                            <td>&nbsp;</td>
                            <td align="right" ><? echo number_format($issValue, 2); ?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                </div>
            </div>
            <?
        }

		$r_id=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id");
		if($r_id)
		{
			oci_commit($con);
		}
		disconnect($con);
        ?>
    </fieldset>
    <?
    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
        //if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
    }
    //---------end------------//
    $name = time();
    $filename = $user_id . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename";
    exit();
}
?>
