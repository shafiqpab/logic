<?php
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "")
{
    header("location:login.php");
    die;
}

$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

//actn_onDblClick_itemDescription
if ($action == "actn_onDblClick_itemDescription") 
{
	echo load_html_head_contents("Popup Info", "../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$expData = explode('_', $data);
	$company_id = $expData[0];
	?>
	<script>
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;

		function check_all_data()
		{
			var tbl_row_count = document.getElementById('list_view').rows.length;
			tbl_row_count = tbl_row_count - 1;
			for (var i = 1; i <= tbl_row_count; i++)
			{
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value(functionParam);
			}
		}

		function toggle(x, origColor)
		{
			var newColor = 'yellow';
			if (x.style)
			{
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

			if (jQuery.inArray(selectID, selected_id) == -1)
			{
				selected_id.push(selectID);
				selected_name.push(selectDESC);
				selected_no.push(str);
			}
			else
			{
				for (var i = 0; i < selected_id.length; i++)
				{
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
			for (var i = 0; i < selected_id.length; i++)
			{
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				num += selected_no[i] + ',';
			}
			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);
			num = num.substr(0, num.length - 1);

			$('#hdn_product_id').val(id);
			$('#hdn_item_description').val(name);
			parent.emailwindow.hide();
		}

		function func_onClick_show()
		{
			show_list_view(document.getElementById('cbo_search_by').value + '_' + document.getElementById('txt_search_common').value + '_' +<? echo $company_id; ?>, 'actn_onDblClick_itemDescription_listview', 'search_div', 'lot_wise_yarn_transaction_v2_controller', 'setFilterGrid("list_view",-1)');
		}
	</script>
	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="400" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<tr>
							<th width="160">Search By</th>
							<th align="center" width="180" id="search_by_td_up">Enter Lot Number</th>
							<th>
								<input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  />
								<input type='hidden' id='hdn_product_id' />
								<input type='hidden' id='hdn_item_description' />
							</th>
						</tr>
					</thead>
					<tbody>
						<tr align="center">
							<td align="center">
								<?
								$search_by = array(1 => 'Lot No', 3 => 'Product ID', 2 => 'Item Description');
								$dd = "change_search_event(this.value, '0*0', '0*0', '../../')";
								echo create_drop_down("cbo_search_by", 150, $search_by, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td width="180" align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="func_onClick_show()" style="width:100px;" />
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
 	exit();
}

//actn_onDblClick_itemDescription_listview
if ($action=="actn_onDblClick_itemDescription_listview")
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
                    <th>Item Description</th>
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
					//for LOT NO
					if (trim($txt_search_by) == 1)
					{
						//$sql_cond = " and d.lot LIKE '%$txt_search_common%'";
						$sql_cond = " and d.lot = '".$txt_search_common."'";
					}
					//for item description
					else if (trim($txt_search_by) == 2)
					{
						$sql_cond = " and d.product_name_details LIKE '%$txt_search_common%'";
					}
					//for product id
					else if (trim($txt_search_by) == 3)
					{
						$sql_cond = " and d.id = ".$txt_search_common;
					}
				}

				$sql = "select b.supplier_id, b.receive_purpose, b.booking_id, c.pay_mode, d.id, d.company_id, d.lot, d.product_name_details from inv_transaction a, inv_receive_master b left join wo_yarn_dyeing_mst c on b.booking_id=c.id and c.status_active=1 and c.is_deleted=0 , product_details_master d where a.prod_id=d.id and a.mst_id=b.id and a.transaction_type in (1,4,5) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond group by b.supplier_id, b.receive_purpose, b.booking_id, c.pay_mode, d.id, d.company_id, d.lot, d.product_name_details";

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

						$pay_mode = $row[csf('pay_mode')];
						$receive_purpose = $row[csf('receive_purpose')];

						if( $receive_purpose ==2 || $receive_purpose ==7 || $receive_purpose ==12 || $receive_purpose ==15 || $receive_purpose == 38 || $receive_purpose ==46 || $receive_purpose ==50 || $receive_purpose ==51 )
						{
							if($pay_mode==3 || $pay_mode==5)
							{
								$factory_name = $company_arr[$row[csf('supplier_id')]];
							}else{
								$factory_name = $supplier_arr[$row[csf('supplier_id')]];
							} 
						}else
						{
							$factory_name = $company_arr[$row[csf('company_id')]];
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
        <!--<div style="width:580px;" align="left">
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
        </div>-->
    </div>

    <script>
        setFilterGrid('list_view',-1);
        check_all_data();
    </script>
    <?
    exit();
}

//generate_report
if ($action == "generate_report")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$cbo_company_id = str_replace("'", "", trim($cbo_company_id));
    $txt_product_id = str_replace("'", "", trim($txt_product_id));
    /*$cbo_method = str_replace("'", "", $cbo_method);
    $from_date = str_replace("'", "", $txt_date_from);
    $to_date = str_replace("'", "", $txt_date_to);*/

	$search_cond = "";
    /*if ($db_type == 0)
	{
        if ($from_date != "" && $to_date != "")
            $search_cond .= " and a.transaction_date  between '" . change_date_format($from_date, 'yyyy-mm-dd') . "' and '" . change_date_format($to_date, 'yyyy-mm-dd') . "'";
    }
    else
	{
        if ($from_date != "" && $to_date != "")
            $search_cond .= " and a.transaction_date  between '" . date("j-M-Y", strtotime($from_date)) . "' and '" . date("j-M-Y", strtotime($to_date)) . "'";
    }*/

	/*$search_cond = "";
	if ($db_type == 0) {
		if ($from_date != "" && $to_date != "")
			$search_cond .= " and a.transaction_date  between '" . change_date_format($from_date, 'yyyy-mm-dd') . "' and '" . change_date_format($to_date, 'yyyy-mm-dd') . "'";
	}
	else {
		if ($from_date != "" && $to_date != "")
			$search_cond .= " and a.transaction_date  between '" . date("j-M-Y", strtotime($from_date)) . "' and '" . date("j-M-Y", strtotime($to_date)) . "'";
	}*/

	/*$lot = str_replace("'", "", trim($txt_lot_no));
	if (str_replace("'", "", trim($txt_lot_no)) != "")
		$search_string = " and b.lot='$lot'";
	else
		$search_string = "";*/

	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0", "id", "supplier_name");
	$transMrrArr = return_library_array("select id,transfer_system_id from  inv_item_transfer_mst", "id", "transfer_system_id");
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer", "id", "buyer_name");
	$store_arr = return_library_array("select id,store_name from lib_store_location", "id", "store_name");
	$count_arr = return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$color_library = return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$methodArr = array(0 => "Weighted Average", 1 => "FIFO", 2 => "LIFO");

	$sql_receive_mrr = "SELECT A.ID AS TRID, A.TRANSACTION_TYPE, A.BUYER_ID, A.CONS_QUANTITY, B.RECV_NUMBER, B.KNITTING_SOURCE, B.KNITTING_COMPANY, B.SUPPLIER_ID, B.RECEIVE_PURPOSE, B.LOAN_PARTY, B.BOOKING_ID, B.BOOKING_NO, B.RECEIVE_BASIS, B.CHALLAN_NO, B.REMARKS, B.ISSUE_ID 
	FROM INV_TRANSACTION A, INV_RECEIVE_MASTER B
	WHERE A.MST_ID=B.ID AND A.PROD_ID IN (".$txt_product_id.") AND A.TRANSACTION_TYPE IN (1,4) AND A.ITEM_CATEGORY=1 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.COMPANY_ID = ".$cbo_company_id."";
	//echo $sql_receive_mrr;
	$result_rcv = sql_select($sql_receive_mrr);
	$receiveMRR = array();
	$remarksArr = array();
	$trWiseReceiveMRR = array();
	$yarnDyeingIdArr = array();
	$issueReturnQtyArr = array();
	foreach ($result_rcv as $row) 
	{
		$receiveMRR[$row['TRID'] . $row['TRANSACTION_TYPE']] = $row['RECV_NUMBER'];
		$remarksArr[$row['TRID'] . $row['TRANSACTION_TYPE']] = $row['REMARKS'];
		$trWiseReceiveMRR[$row['TRID']] = $row['RECV_NUMBER'];
		$receive_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["knitting_source"] = $row['KNITTING_SOURCE'];
		$receive_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["knitting_company"] = $row['KNITTING_COMPANY'];
		$receive_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["knitting_supplier"] = $row['SUPPLIER_ID'];
		$receive_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["receive_purpose_id"] = $row['RECEIVE_PURPOSE'];
		$receive_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["loan_party"] = $row['LOAN_PARTY'];
		$receive_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["buyer_id"] = $row['BUYER_ID'];
		$receive_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["receive_purpose"] = $yarn_issue_purpose[$row['RECEIVE_PURPOSE']];
		$receive_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["receive_basis"] = $row['RECEIVE_BASIS'];
		$receive_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["challan_no"] = $row['CHALLAN_NO'];
		$receive_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["booking_no"] = $row['BOOKING_NO'];
		//$receive_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["order_rate"] = $row['ORDER_RATE'];
		//$receive_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["dye_charge"] = $row['DYE_CHARGE'];

		if($row['TRANSACTION_TYPE']==4)
		{
			$issueReturnQtyArr[$row['ISSUE_ID']] = $row['CONS_QUANTITY'];

			$issue_ret_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["knitting_source"] = $row['KNITTING_SOURCE'];
			$issue_ret_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["knitting_company"] = $row['KNITTING_COMPANY'];
			$issue_ret_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["knitting_supplier"] = $row['KNITTING_COMPANY'];
			//$issue_ret_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["knitting_supplier"] = $row['KNITTING_COMPANY'];
		}

		if( $row['RECEIVE_PURPOSE']==2 || $row['RECEIVE_PURPOSE']==7 || $row['RECEIVE_PURPOSE']==12 || $row['RECEIVE_PURPOSE']==15 || $row['RECEIVE_PURPOSE']== 38 || $row['RECEIVE_PURPOSE']==46 || $row['RECEIVE_PURPOSE']==50 || $row['RECEIVE_PURPOSE']==51 )
		{
			$wo_booking_id[] = $row['BOOKING_ID'];
			$wo_rcv_booking_id[$row['TRID']] = $row['BOOKING_ID'];
		}
		
		//for yarn dyeing
		/*if( $row['RECEIVE_BASIS'] == 2 && $row['RECEIVE_PURPOSE']==2 )
		{
			$yarnDyeingIdArr[$row['BOOKING_ID']] = $row['BOOKING_ID'];
		}*/
	}
	unset($result_rcv);
	/*echo "<pre>";
	print_r($receive_source);
	echo "</pre>";*/

	if(!empty($wo_booking_id))
	{
		$wo_sql_result =sql_select("SELECT A.ID, A.SUPPLIER_ID, A.PAY_MODE, C.LOT FROM WO_YARN_DYEING_MST A, WO_YARN_DYEING_DTLS B, PRODUCT_DETAILS_MASTER C WHERE A.ID = B.MST_ID AND B.PRODUCT_ID = C.ID AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND A.ID IN (".implode(',',array_unique($wo_booking_id)).") AND A.COMPANY_ID = ".$cbo_company_id."");
		foreach ($wo_sql_result as $row) 
		{
			$wo_data[$row['ID']]['pay_mode'] = $row['PAY_MODE'];
			$wo_data[$row['ID']]['lot'] = $row['LOT'];
		}
		unset($wo_sql_result);	
	}

    // issue MRR array------------------------------------------------
	$sql_issue_mrr = "SELECT A.ID AS TRID, A.TRANSACTION_TYPE, B.ISSUE_NUMBER, B.ISSUE_PURPOSE, B.ISSUE_BASIS, B.CHALLAN_NO, B.REMARKS FROM INV_TRANSACTION A, INV_ISSUE_MASTER B WHERE A.PROD_ID IN ($txt_product_id) AND A.MST_ID=B.ID AND A.TRANSACTION_TYPE IN (2,3) AND A.ITEM_CATEGORY=1 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.COMPANY_ID = ".$cbo_company_id."";
	$result_iss = sql_select($sql_issue_mrr);
	$issueMRR = array();
	$issueMRR = array();
	$issuePupose = array();
	$issueInfoArr = array();
	foreach ($result_iss as $row)
	{
		$issueMRR[$row['TRID'] . $row['TRANSACTION_TYPE']] = $row['ISSUE_NUMBER'];
		$remarksArr[$row['TRID'] . $row['TRANSACTION_TYPE']] = $row['REMARKS'];
		$issuePupose[$row['TRID']] = $yarn_issue_purpose[$row['ISSUE_PURPOSE']];
		$issueInfoArr[$row['TRID']]['issue_basis'] = $row['ISSUE_BASIS'];
		$issueInfoArr[$row['TRID']]['challan_no'] = $row['CHALLAN_NO'];
	}
	unset($result_iss);
	//echo "<pre>";
	//print_r($issueMRR);

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
			/*if ($from_date != "" && $to_date != "")
			{
				if ($db_type == 2)
					$from_date = date("j-M-Y", strtotime($from_date));
				if ($db_type == 0)
					$from_date = change_date_format($from_date, 'yyyy-mm-dd');
					
				$sqlTR = "select  prod_id, SUM(CASE WHEN transaction_type in (1,4,5) THEN cons_quantity ELSE 0 END) as receive,
				SUM(CASE WHEN transaction_type in (2,3,6) THEN cons_quantity ELSE 0 END) as issue,
				SUM(CASE WHEN transaction_type in (1,4,5) THEN cons_amount ELSE 0 END) as rcv_balance,
				SUM(CASE WHEN transaction_type in (2,3,6) THEN cons_amount ELSE 0 END) as iss_balance
				from inv_transaction
				where transaction_date < '" . $from_date . "' and status_active=1 and is_deleted=0 $storeCond group by prod_id";
				$trResult = sql_select($sqlTR);
				$opning_bal_arr = array();
				foreach ($trResult as $row)
				{
					$opning_bal_arr[$row[csf("prod_id")]]["prod_id"] = $row[csf("prod_id")];
					$opning_bal_arr[$row[csf("prod_id")]]["receive"] = $row[csf("receive")];
					$opning_bal_arr[$row[csf("prod_id")]]["issue"] = $row[csf("issue")];
					$opning_bal_arr[$row[csf("prod_id")]]["rcv_balance"] = $row[csf("rcv_balance")];
					$opning_bal_arr[$row[csf("prod_id")]]["iss_balance"] = $row[csf("iss_balance")];
				}
			}*/
			
			/*$cbo_store_name=str_replace("'","",$cbo_store_name);
			$store_cond="";
			if($cbo_store_name>0) $store_cond=" and a.store_id=$cbo_store_name";*/
			
			$sql = "SELECT A.ID, A.MST_ID, A.PROD_ID, A.STORE_ID, A.TRANSACTION_DATE, A.RECEIVE_BASIS, A.INSERT_DATE, A.TRANSACTION_TYPE, A.CONS_QUANTITY, A.CONS_RATE, A.CONS_AMOUNT, A.CONS_REJECT_QNTY, A.REMARKS, A.ORDER_RATE, A.DYE_CHARGE, A.RETURN_QNTY, A.REQUISITION_NO, 
			B.PRODUCT_NAME_DETAILS, B.COLOR, B.YARN_COMP_PERCENT1ST, B.YARN_COUNT_ID, YARN_COMP_TYPE1ST, B.YARN_TYPE, B.UNIT_OF_MEASURE, B.LOT, B.SUPPLIER_ID, B.COLOR, 
			C.KNIT_DYE_SOURCE, C.KNIT_DYE_COMPANY, C.ISSUE_BASIS, C.ISSUE_PURPOSE, C.LOAN_PARTY, C.BUYER_JOB_NO, C.BOOKING_ID, c.BOOKING_NO, CASE WHEN A.BUYER_ID>0 THEN A.BUYER_ID ELSE C.BUYER_ID END AS BUYER_ID
			FROM INV_TRANSACTION A LEFT JOIN INV_ISSUE_MASTER C ON A.MST_ID=C.ID AND A.TRANSACTION_TYPE IN (2,3,6), PRODUCT_DETAILS_MASTER B
			WHERE A.PROD_ID IN (".$txt_product_id.") AND A.COMPANY_ID = ".$cbo_company_id." AND A.PROD_ID=B.ID AND A.ITEM_CATEGORY=1 AND B.ITEM_CATEGORY_ID=1 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 $search_cond
			ORDER BY A.PROD_ID, A.INSERT_DATE, A.ID ASC";
			//echo $sql; die;
			$result = sql_select($sql);
			$all_issue_trans_id=array();
			$salesOrderNoArr = array();
			$requisitionNoArr = array();
			$sampleBookingNoArr = array();
			foreach($result as $row)
			{
				$all_issue_trans_id[$row['ID']]=$row['ID'];
				
				//for wo/booking basis issue
				if($row['ISSUE_BASIS'] == 1)
				{
					//for yarn dyeing purpose
					if($row['ISSUE_PURPOSE'] == 2 || $row['ISSUE_PURPOSE'] == 7 || $row['ISSUE_PURPOSE'] == 12 || $row['ISSUE_PURPOSE'] == 15 || $row['ISSUE_PURPOSE'] == 38 || $row['ISSUE_PURPOSE'] == 46 || $row['ISSUE_PURPOSE'] == 50 || $row['ISSUE_PURPOSE'] == 51)
					{
						$salesOrderNoArr[$row['BUYER_JOB_NO']] = $row['BUYER_JOB_NO'];
					}
					//for sample without order purpose
					elseif($row['ISSUE_PURPOSE'] == 8)
					{
						$sampleBookingNoArr[$row['BOOKING_NO']] = $row['BOOKING_NO'];
					}
				}
				//for requisition basis issue
				elseif($row['ISSUE_BASIS'] == 3)
				{
					$requisitionNoArr[$row['REQUISITION_NO']] = $row['REQUISITION_NO'];
				}
			}
			//echo "<pre>";
			//print_r($requisitionNoArr);
			
			//for requisition information
			$sqlReq = "SELECT A.REQUISITION_NO, C.SALES_BOOKING_NO, C.JOB_NO FROM PPL_YARN_REQUISITION_ENTRY A, PPL_PLANNING_ENTRY_PLAN_DTLS B, FABRIC_SALES_ORDER_MST C WHERE A.KNIT_ID = B.DTLS_ID AND B.BOOKING_NO = C.SALES_BOOKING_NO AND A.REQUISITION_NO IN (".implode(",",$requisitionNoArr).")";
			//echo $sqlReq;
			$sqlReqRslt = sql_select($sqlReq);
			$requisitionInfoArr = array();
			foreach($sqlReqRslt as $row)
			{
				$requisitionInfoArr[$row['REQUISITION_NO']]['fso_no'] = $row['JOB_NO'];
				$requisitionInfoArr[$row['REQUISITION_NO']]['fab_booking_no'] = $row['SALES_BOOKING_NO'];
				$requisitionInfoArr[$row['REQUISITION_NO']]['requisition_no'] = $row['REQUISITION_NO'];
			}
			unset($sqlReqRslt);
			//echo "<pre>";
			//print_r($requisitionInfoArr);
			
			//for yarn dyeing information
			$sqlSales = "SELECT C.SALES_BOOKING_NO, C.JOB_NO FROM FABRIC_SALES_ORDER_MST C WHERE C.JOB_NO IN ('".implode("','",$salesOrderNoArr)."')";
			//echo $sqlSales;
			$sqlSalesRslt = sql_select($sqlSales);
			$salesInfoArr = array();
			foreach($sqlSalesRslt as $row)
			{
				$salesInfoArr[$row['JOB_NO']]['fso_no'] = $row['JOB_NO'];
				$salesInfoArr[$row['JOB_NO']]['fab_booking_no'] = $row['SALES_BOOKING_NO'];
			}
			unset($sqlSalesRslt);
			//echo "<pre>";
			//print_r($salesInfoArr);
			
			//for sample information
			$sqlSample = "SELECT C.SALES_BOOKING_NO, C.JOB_NO FROM FABRIC_SALES_ORDER_MST C WHERE C.SALES_BOOKING_NO IN ('".implode("','",$sampleBookingNoArr)."')";
			//echo $sqlSales;
			$sqlSampleRslt = sql_select($sqlSample);
			$sampleInfoArr = array();
			foreach($sqlSampleRslt as $row)
			{
				$sampleInfoArr[$row['SALES_BOOKING_NO']]['fso_no'] = $row['JOB_NO'];
			}
			unset($sqlSampleRslt);
			//echo "<pre>";
			//print_r($salesInfoArr);

			$check_is_sales_sql = "SELECT TRANS_ID, IS_SALES FROM ORDER_WISE_PRO_DETAILS WHERE TRANS_ID IN(".implode(",",$all_issue_trans_id).")";
			$check_is_sales=sql_select($check_is_sales_sql);
			$is_sales_arr=array();
			foreach ($check_is_sales as $is_sales_row)
			{
				$is_sales_arr[$is_sales_row['TRANS_ID']]=$is_sales_row['IS_SALES'];
			}
			unset($check_is_sales);
			
			$issueTransIdArr=array_chunk($all_issue_trans_id,999);
			$issue_job_cond=" AND(";
			foreach($issueTransIdArr as $issue_trans_id)
			{
				if($issue_job_cond==" AND(")
					$issue_job_cond.=" C.TRANS_ID IN(".implode(',', $issue_trans_id).")";
				else
					$issue_job_cond.=" OR C.TRANS_ID in(".implode(',', $issue_trans_id).")";
			}
			$issue_job_cond.=")";

			if($issue_job_cond == " AND()")
			{
				$issue_job_cond = "";
			}

			$po_details_sql="SELECT A.ID AS JOB_ID, A.JOB_NO, A.STYLE_REF_NO, A.BUYER_NAME, B.ID AS PO_ID, B.PO_NUMBER, B.GROUPING, C.TRANS_ID FROM WO_PO_DETAILS_MASTER A, WO_PO_BREAK_DOWN B, ORDER_WISE_PRO_DETAILS C WHERE A.JOB_NO=B.JOB_NO_MST AND B.ID=C.PO_BREAKDOWN_ID AND A.STATUS_ACTIVE=1 AND B.STATUS_ACTIVE=1 AND C.STATUS_ACTIVE=1 AND A.COMPANY_NAME = ".$cbo_company_id." $issue_job_cond GROUP BY A.ID, A.JOB_NO, A.STYLE_REF_NO, A.BUYER_NAME, B.ID, B.PO_NUMBER, B.GROUPING, C.TRANS_ID";
			$po_details=sql_select($po_details_sql);
			$jobIssueData=$bookingData=array();
			foreach($po_details as $row)
			{
				$jobIssueData[$row['TRANS_ID']]["style_ref_no"]=$row['STYLE_REF_NO'];
				$jobIssueData[$row['TRANS_ID']]["buyer_name"]=$row['BUYER_NAME'];
				$jobIssueData[$row['TRANS_ID']]["po_number"]=$row['PO_NUMBER'];
				$jobIssueData[$row['TRANS_ID']]["grouping"]=$row['GROUPING'];
			}
			unset($po_details);

			$sales_order_po_data_sql = "SELECT A.ID, A.JOB_NO, A.SALES_BOOKING_NO, A.BUYER_ID, A.WITHIN_GROUP, A.STYLE_REF_NO, A.BOOKING_WITHOUT_ORDER, D.TRANS_ID FROM FABRIC_SALES_ORDER_MST A LEFT JOIN ORDER_WISE_PRO_DETAILS D ON A.ID=D.PO_BREAKDOWN_ID WHERE A.STATUS_ACTIVE = 1 AND A.COMPANY_ID = ".$cbo_company_id." AND D.TRANS_ID IN(".implode(',',$issue_trans_id).") GROUP BY A.ID, A.JOB_NO, A.SALES_BOOKING_NO, A.BUYER_ID, A.WITHIN_GROUP, A.STYLE_REF_NO, A.BOOKING_WITHOUT_ORDER, D.TRANS_ID";
			$sales_order_po_data = sql_select($sales_order_po_data_sql);
			foreach ($sales_order_po_data as  $val)
			{
				$sales_order_po_array[$val['TRANS_ID']]["order_no"] = $val['JOB_NO'];
				$sales_order_po_array[$val['TRANS_ID']]["booking_no"] = $val['SALES_BOOKING_NO'];
				$sales_order_po_array[$val['TRANS_ID']]["within_group"] = $val['WITHIN_GROUP'];
				$sales_order_po_array[$val['TRANS_ID']]["style_ref_no"] = $val['STYLE_REF_NO'];
				$sales_order_po_array[$val['TRANS_ID']]["buyer_id"] = $val['BUYER_ID'];
				$sales_booking[$val['SALES_BOOKING_NO']] = "'".$val['SALES_BOOKING_NO']."'";
			}
			unset($sales_order_po_data);
			/*echo "<pre>";
			print_r($sales_booking);
			echo "</pre>";*/

			if(!empty($sales_booking))
			{
				$job_sql = sql_select("SELECT A.BOOKING_NO, B.BUYER_NAME, B.STYLE_REF_NO FROM WO_BOOKING_DTLS A, WO_PO_DETAILS_MASTER B WHERE A.JOB_NO=B.JOB_NO AND A.STATUS_ACTIVE=1 AND B.STATUS_ACTIVE=1 AND A.BOOKING_NO IN(".implode(",", $sales_booking).") GROUP BY A.BOOKING_NO, B.BUYER_NAME, B.STYLE_REF_NO
				UNION ALL
				SELECT A.BOOKING_NO, A.BUYER_ID AS BUYER_NAME, B.STYLE_REF_NO FROM WO_NON_ORD_SAMP_BOOKING_MST A, FABRIC_SALES_ORDER_MST B WHERE A.BOOKING_NO = B.SALES_BOOKING_NO AND A.STATUS_ACTIVE=1 AND B.STATUS_ACTIVE=1 AND A.BOOKING_NO IN(".implode(",", $sales_booking).") GROUP BY A.BOOKING_NO, A.BUYER_ID, B.STYLE_REF_NO");
				foreach ($job_sql as $job_row)
				{
					$booking_job_arr[$job_row['BOOKING_NO']]["style_ref_no"] = $job_row['STYLE_REF_NO'];
					$booking_job_arr[$job_row['BOOKING_NO']]["buyer_name"] = $job_row['BUYER_NAME'];
				}
				unset($job_sql);
			}
			/*echo "<pre>";
			print_r($booking_job_arr);
			echo "</pre>";*/
			
			//data preparing here
			$m = 1;
			//$productIdArr = array();
			$k = 1;
			$dataArr = array();
			$headerInfoArr = array();
			foreach ($result as $row) 
			{
				//for recrive information
				$receiveInfo_buyer = $receive_source[$row['ID'] . $row['TRANSACTION_TYPE']]["buyer_id"];
				$receiveInfo_basis = $receive_source[$row['ID'] . $row['TRANSACTION_TYPE']]["receive_basis"];
				$receiveInfo_purpose = $receive_source[$row['ID'] . $row['TRANSACTION_TYPE']]["receive_purpose"];
				$receiveInfo_challanNo = $receive_source[$row['ID'] . $row['TRANSACTION_TYPE']]["challan_no"];
				$receiveInfo_bookingNo = $receive_source[$row['ID'] . $row['TRANSACTION_TYPE']]["booking_no"];
				$receiveInfo_purposeId = $receive_source[$row['ID'] . $row['TRANSACTION_TYPE']]["receive_purpose_id"];
				$receiveInfo_loanParty = $receive_source[$row['ID'] . $row['TRANSACTION_TYPE']]["loan_party"];
				$receiveInfo_knittingCompany = $receive_source[$row['ID'] . $row['TRANSACTION_TYPE']]["knitting_company"];

				$tranType = 'Receive';
				if ($row['TRANSACTION_TYPE'] == 2 || $row['TRANSACTION_TYPE'] == 3 || $row['TRANSACTION_TYPE'] == 6)
				{
					$tranType = 'Issue';
				}
				
				//for TRANSACTION_DATE
				$row['TRANSACTION_DATE'] = change_date_format($row['TRANSACTION_DATE']);
				
				//for transaction ref. no
				$trans_ref_no = '';
				if( $row['MST_ID']==0 && $row['RECEIVE_BASIS']==30)
				{
					$trans_ref_no = 'Adjustment';
				}
				else
				{
					if ($row['TRANSACTION_TYPE'] == 5 || $row['TRANSACTION_TYPE'] == 6)
					{
						$trans_ref_no = $transMrrArr[$row['MST_ID']];
					}
					else
					{
						$trans_ref_no = $mrrArray[$row['ID'].$row['TRANSACTION_TYPE']];
					} 
				}
				
				//for buyer
				$buyer_name = '';
				$is_sales = $is_sales_arr[$row['ID']];
				if($is_sales == 1)
				{
					$within_group = $sales_order_po_array[$row['ID']]["within_group"];
					$booking_no = $sales_order_po_array[$row['ID']]["booking_no"];
					if($within_group == 1)
					{
						$buyer_name = $buyer_arr[$booking_job_arr[$booking_no]["buyer_name"]];
						//$order_no = $sales_order_po_array[$row['ID']]["order_no"];
						$style_ref_no=$booking_job_arr[$booking_no]["style_ref_no"];
					}
					else
					{
						$buyer_name = $buyer_arr[$sales_order_po_array[$row['ID']]["buyer_id"]];
						//$order_no = $sales_order_po_array[$row['ID']]["order_no"];
						$style_ref_no=$sales_order_po_array[$row['ID']]["style_ref_no"];
					}
				}
				else
				{
					$buyer_name = $buyer_arr[$jobIssueData[$row['ID']]["buyer_name"]];
					//$order_no = $jobIssueData[$row['ID']]["po_number"];
					$style_ref_no = $jobIssueData[$row['ID']]["style_ref_no"];
					$grouping = $jobIssueData[$row['ID']]["grouping"];
				}
				
				if($row['TRANSACTION_TYPE'] == 1)
				{
					$buyer_name = $buyer_arr[$receiveInfo_buyer];
				}
				
				if(empty($buyer_name) && !empty($row['BUYER_ID']))
				{
					$buyer_name = $buyer_arr[$row['BUYER_ID']];
				}
				//for buyer end
				
				//for basis
				$basis = '';
				if ($row['TRANSACTION_TYPE'] == 1)
				{
					$basis = $receive_basis_arr[$receiveInfo_basis];
				}
				elseif ($row['TRANSACTION_TYPE'] == 4)
				{
					$basis = $issue_basis[$receiveInfo_basis];
				}
				elseif ($row['TRANSACTION_TYPE'] == 5)
				{
					//$basis = '';
				}
				else
				{
					$basis = $issue_basis[$issueInfoArr[$row['ID']]['issue_basis']];
				}
				//for basis end
				
				//for purpose
				$purpose = '';
				if ($row['TRANSACTION_TYPE'] == 1 )
				{
					$purpose = $receiveInfo_purpose;
				}
				else if ($row['TRANSACTION_TYPE'] == 2)
				{
					$purpose = $issuePupose[$row['ID']];
				}
				else if ($row['TRANSACTION_TYPE'] == 4)
				{
					$purpose = '';
				}
				//for purpose end
				
				//for challan no and wo/pi no
				$challan_no = '';
				$wo_pi_no = '';
				if ($row['TRANSACTION_TYPE'] == 1 || $row['TRANSACTION_TYPE'] == 4 || $row['TRANSACTION_TYPE'] == 5)
				{
					$challan_no = $receiveInfo_challanNo;
					$wo_pi_no = $receiveInfo_bookingNo;
				}
				else
				{
					$challan_no = $issueInfoArr[$row['ID']]['challan_no'];
				}
				//for challan no end
				
				//for transaction with
				if ($row['TRANSACTION_TYPE'] == 2)
				{
					if($row['ISSUE_PURPOSE'] == 5)
					{
						$transactionWith = $supplierArr[$row['LOAN_PARTY']];
					}
					else
					{
						if ($row['KNIT_DYE_SOURCE'] == 1)
							$transactionWith = $companyArr[$row['KNIT_DYE_COMPANY']];
						else
							$transactionWith = $supplierArr[$row['KNIT_DYE_COMPANY']];
					}

				}
				else if ($row['TRANSACTION_TYPE'] == 3)
				{
					$transactionWith = $supplierArr[$row['SUPPLIER_ID']];
				}
				else if ($row['TRANSACTION_TYPE'] == 1) 
				{
					if($receiveInfo_purposeId == 5)
					{
						$transactionWith = $supplierArr[$receiveInfo_loanParty];
					}
					/*else if( $receiveInfo_purposeId == 6 || $receiveInfo_purposeId==16 )
					{
						$transactionWith = $companyArr[$receiveInfo_knittingCompany];
					}*/
					else
					{
						$pay_mode = $wo_data[$wo_rcv_booking_id[$row['ID']]]['pay_mode'];

						if($pay_mode==3 || $pay_mode==5)
						{
							$transactionWith = $companyArr[$row['SUPPLIER_ID']];
						}
						else
						{
							$transactionWith = $supplierArr[$row['SUPPLIER_ID']];
						} 
					}
				}  
				else if ($row['TRANSACTION_TYPE'] == 4) 
				{
					if ($issue_ret_source[$row['ID'] . $row['TRANSACTION_TYPE']]["knitting_source"] == 1)
						$transactionWith = $companyArr[$issue_ret_source[$row['ID'] . $row['TRANSACTION_TYPE']]["knitting_company"]];
					else
						$transactionWith = $supplierArr[$issue_ret_source[$row['ID'] . $row['TRANSACTION_TYPE']]["knitting_supplier"]];
				}
				//for transaction with end
				
				//for yarn composition
				$composition_str = $composition[$row['YARN_COMP_TYPE1ST']]." ".$row['YARN_COMP_PERCENT1ST']."% ".$yarn_type[$row['YARN_TYPE']]." ".$color_library[$row['COLOR']];
				/*$productIdArr[$row['PROD_ID']] = $row['PROD_ID'];
				$countIdArr[$row['YARN_COUNT_ID']] = $count_arr[$row['YARN_COUNT_ID']];
				$compositionArr[$composition_str] = $composition_str;
				$colorIdArr[$row['COLOR']] = $color_library[$row['COLOR']];
				$lotArr[$row['LOT']] = $row['LOT'];
				$uomArr[$row['UNIT_OF_MEASURE']] = $unit_of_measurement[$row['UNIT_OF_MEASURE']];*/

				//for Grey Lot 	Grey Yarn Rate 	WO Rate
				$grey_lot = '';
				$grey_yarn_rate = '';
				$wo_rate = '';
				if ($row['TRANSACTION_TYPE'] == 1 && $receiveInfo_basis == 2 && $receiveInfo_purposeId == 2)
				{
					$grey_lot = $wo_data[$wo_rcv_booking_id[$row['ID']]]['lot'];
					$grey_yarn_rate = number_format($row['ORDER_RATE']-$row['DYE_CHARGE'],2,".","");
					$wo_rate = $row['DYE_CHARGE'];
				}

				//
				$fso_no = '';
				$fab_booking_no = '';
				$requisition_no = '';
				if($tranType == 'Issue')
				{
					//for wo/booking basis issue
					if($row['ISSUE_BASIS'] == 1)
					{
						//for yarn dyeing purpose
						if($row['ISSUE_PURPOSE'] == 2 || $row['ISSUE_PURPOSE'] == 7 || $row['ISSUE_PURPOSE'] == 12 || $row['ISSUE_PURPOSE'] == 15 || $row['ISSUE_PURPOSE'] == 38 || $row['ISSUE_PURPOSE'] == 46 || $row['ISSUE_PURPOSE'] == 50 || $row['ISSUE_PURPOSE'] == 51)
						{
							$fso_no = $salesInfoArr[$row['BUYER_JOB_NO']]['fso_no'];
							$fab_booking_no = $salesInfoArr[$row['BUYER_JOB_NO']]['fab_booking_no'];
							$requisition_no = $row['BOOKING_NO'];
						}
						//for sample without order purpose
						elseif($row['ISSUE_PURPOSE'] == 8)
						{
							$fso_no = $sampleInfoArr[$row['BOOKING_NO']]['fso_no'];
							$fab_booking_no = $row['BOOKING_NO'];
						}
					}
					//for requisition basis issue
					elseif($row['ISSUE_BASIS'] == 3)
					{
						$fso_no = $requisitionInfoArr[$row['REQUISITION_NO']]['fso_no'];
						$fab_booking_no = $requisitionInfoArr[$row['REQUISITION_NO']]['fab_booking_no'];
						$requisition_no = $requisitionInfoArr[$row['REQUISITION_NO']]['requisition_no'];
					}
				}
				
				//for remarks
				$remarks = $row['REMARKS'];
				if ($row['TRANSACTION_TYPE'] == 1 || $row['TRANSACTION_TYPE'] == 2)
				{
					$remarks = $remarksArr[$row['ID'].$row['TRANSACTION_TYPE']];
				}
				
				//for cons_qnty cons_amount
				$cons_amount = 0;
				$cons_amount = 0;
				$cons_qnty = $row['CONS_QUANTITY'];
				$cons_rate = $row['CONS_RATE'];
				$cons_amount = $cons_qnty * $row['CONS_RATE'];
				
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no][$requisition_no]['callan_no'] = $challan_no;
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no][$requisition_no]['store_name'] = $store_arr[$row['STORE_ID']];
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no][$requisition_no]['buyer_name'] = $buyer_name;
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no][$requisition_no]['purpose'] = $purpose;
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no][$requisition_no]['transfer_with'] = $transactionWith;
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no][$requisition_no]['cons_qty'] += $cons_qnty;
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no][$requisition_no]['cons_rate'] = $cons_rate;
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no][$requisition_no]['cons_amount'] = $cons_amount;
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no][$requisition_no]['remarks'] = $remarks;

$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no][$requisition_no]['style_ref_no'] = $style_ref_no;
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no][$requisition_no]['basis'] = $basis;
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no][$requisition_no]['wo_pi_no'] = $wo_pi_no;
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no][$requisition_no]['grey_lot'] = $grey_lot;
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no][$requisition_no]['grey_yarn_rate'] = $grey_yarn_rate;
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no][$requisition_no]['wo_rate'] = $wo_rate;

$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no][$requisition_no]['fso_no'] = $fso_no;
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no][$requisition_no]['fab_booking_no'] = $fab_booking_no;
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no][$requisition_no]['requisition_no'] = $requisition_no;

$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no][$requisition_no]['returnable_qty'] = $row['RETURN_QNTY'];

//for issue return qty
$receiveInfo_issueReturnQty = $issueReturnQtyArr[$row['MST_ID']];
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no][$requisition_no]['issue_return_qty'] = $receiveInfo_issueReturnQty;

$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no][$requisition_no]['yarn_count'] = $count_arr[$row['YARN_COUNT_ID']];
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no][$requisition_no]['yarn_composition'] = $composition_str;

$headerInfoArr[$tranType][$row['PROD_ID']]['yarn_count'] = $count_arr[$row['YARN_COUNT_ID']];
$headerInfoArr[$tranType][$row['PROD_ID']]['yarn_composition'] = $composition_str;
$headerInfoArr[$tranType][$row['PROD_ID']]['color'] = $color_library[$row['COLOR']];
$headerInfoArr[$tranType][$row['PROD_ID']]['lot'] = $row['LOT'];
$headerInfoArr[$tranType][$row['PROD_ID']]['unit_of_measurement'] = $unit_of_measurement[$row['UNIT_OF_MEASURE']];
			}
			/*echo "<pre>";
			print_r($dataArr);
			echo "</pre>";*/

			/*
			| If no data is found in the user's search criteria
			| Then system will give a message No Data Found and
			| Execution will be closed
			*/
			if(empty($dataArr))
			{
				echo "<div style='width:800px; text-align:center'>".get_empty_data_msg()."</div>";
				die;
			}			
			?>
            
            <table style="width:2050px; float: left;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" > 
                <tbody>
                    <tr class="form_caption" style="border:none;">
                        <td align="center" style="border:none;font-size:16px; font-weight:bold" >Lot Wise Yarn Transaction</td>
                    </tr>
                    <tr style="border:none;">
                        <td align="center" style="border:none; font-size:14px;">
                            Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_id)]; ?>
                        </td>
                    </tr>
                    <tr style="border:none;">
                        <td align="center" style="border:none;font-size:12px; font-weight:bold">
                            <? if ($from_date != "" || $to_date != "") echo "From " . change_date_format($from_date) . " To " . change_date_format($to_date) . ""; ?>
                        </td>
                    </tr>
                    <tr style="border:none;">
                        <td style="border:none;">&nbsp;</td>
                    </tr>
            </table>
            <br/>
			<?php
			//for receive
			if(!empty($dataArr['Receive']))
			{
				?>
				<!--<table style="width:2050px; float: left;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" > 
					<tbody>
                        <tr style="border:none;">
							<td style="border:none;font-size:12px; font-weight:bold;" >
								Product Id: <? echo $row[csf('id')] ?>, Count: <? echo $yarnCountArr[$row[csf("yarn_count_id")]] ?>, Composition: <? echo $row[csf("product_name_details")] ?>, Color: <? echo $colorArr[$row[csf("color")]] ?>, Lot: <? echo $row[csf("lot")] ?>, UOM: <? echo $unit_of_measurement[$row[csf("unit_of_measure")]] ?>
							</td> 
						</tr>
						<tr>
							<td style="border:none;font-size:12px; font-weight:bold" >
								Supplier: <? echo $supplierArr[$cbo_supplier_name]; ?>, Brand: <? $brandArr[$row[csf("brand")]] ?>
							</td> 
						</tr>
						<tr>
							<td style="border:none;font-size:12px; font-weight:bold" >
								Method: <? echo $methodArr[$cbo_method]; ?>
							</td> 
						</tr>
					</tbody>
				</table>
				<br/>-->
				<!--<fieldset  style="width:1950px; float:left;">-->
					<table style="width:1930px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
						<thead>
							<tr>
								<td colspan="18" style="font-size:14px; font-weight:bold" align="center">Receiving Status</td>
							</tr>
							<tr>
								<th rowspan="2" width="50">Sl</th>
								<th rowspan="2" width="80">Trans Date</th>
								<th rowspan="2" width="120">Trans Ref No</th>
								<th rowspan="2" width="100">Callan No</th>
								<th rowspan="2" width="120">Store Name</th>
								<th rowspan="2" width="120">Buyer</th>
								<th rowspan="2" width="120">Trans type</th>
								<th rowspan="2" width="120">Receive Basis</th>
								<th rowspan="2" width="120">WO/PI No</th>
								<th rowspan="2" width="120">Receive Purpose</th>
								<th rowspan="2" width="120">Transaction With</th>
								<th colspan="3" width="260">Receive</th>
								<th rowspan="2" width="120">Remarks</th>
								<th rowspan="2" width="120">Grey Lot</th>
								<th rowspan="2" width="120">Grey Yarn Rate</th>
								<th rowspan="2">WO Rate</th>
							</tr>
							<tr>
								<th width="80" align="center">Qnty</th>
								<th width="80" align="center">Rate</th>
								<th width="100" align="center">Value</th>
							</tr>
						</thead>
					</table>
					<div style="width:1950px;  max-height:250px" id="scroll_body" align="left"> 
						<table style="width:1930px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left" >
						<?php
						$i = 0;
						foreach($dataArr['Receive'][1] as $prodId=>$prodIdArr)
						{
							$transType=1;
							$tot_receive_qny = 0;
							$tot_amount_qny = 0;
							?>
                            <tr>
                            	<td colspan="18" style="font-size:12px;color:blue;font-weight:bold;" >
								Product Id: <? echo $prodId; ?>, Count: <? echo $headerInfoArr['Receive'][$prodId]['yarn_count']; ?>, Composition: <? echo $headerInfoArr['Receive'][$prodId]['yarn_composition']; ?>, Color: <? echo $headerInfoArr['Receive'][$prodId]['color']; ?>, Lot: <? echo $headerInfoArr['Receive'][$prodId]['lot']; ?>, UOM: <? echo $headerInfoArr['Receive'][$prodId]['unit_of_measurement']; ?></td>
                            </tr>
                            <?php
							foreach($prodIdArr as $transDate=>$transDateArr)
							{
								foreach($transDateArr as $refNo=>$refNoArr)
								{
									foreach($refNoArr as $reqNo=>$row)
									{
										$i++;
										if ($i % 2 == 0)
											$bgcolor = "#E9F3FF";
										else
											$bgcolor = "#FFFFFF";
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="50"><? echo $i; ?></td>
											<td width="80" style="word-wrap:break-word; word-break: break-all;"><? echo $transDate; ?></td>
											<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $refNo; ?></td>
											<td width="100" style="word-wrap:break-word; word-break: break-all; mso-number-format:'\@'; text-align: center;"><p><? echo $row['callan_no']; ?></p></td>
											<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['store_name']; ?></td>
											<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['buyer_name']; ?> &nbsp;</td>
											<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $transaction_type[$transType]; ?></td>
											<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['basis']; ?></td>
											<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['wo_pi_no']; ?></td>
											<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['purpose']; ?></td>
											<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['transfer_with']; ?></td>
											<td width="80" align="right"><? echo number_format($row['cons_qty'],2); ?></td>
											<td width="80" align="right"><? echo number_format($row['cons_rate'],2); ?></td>
											<td width="100" align="right"><? echo number_format($row['cons_amount'],2); ?></td>
											<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['remarks'] ?></td>
											<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['grey_lot']; ?></td>
											<td width="120"><? echo $row['grey_yarn_rate']; ?></td>
											<td><? echo $row['wo_rate']; ?></td>
										</tr>
										<?
										$tot_receive_qny += $row['cons_qty'];
										$tot_amount_qny += $row['cons_amount'];
										
										$grand_tot_receive_qny += $row['cons_qty'];
										$grand_tot_amount_qny += $row['cons_amount'];
	
										$currentStockQnty[$row['store_name']]["receiveQnty"] += $row['cons_qty'];
										//$currentStockReject[$row['store_name']] = $receiveRow[csf("cons_reject_qnty")];
										
										if($transType == 1)
										{
											$summary_array[$row['yarn_count']][$row['yarn_composition']][$row['store_name']]['receive'] += $row['cons_qty'];
										}
										
										$summary_array[$row['yarn_count']][$row['yarn_composition']][$row['store_name']]['totalReceive'] += $row['cons_qty'];
										//$summary_array[$row['store_name']]['reject'] += $receiveRow[csf("cons_reject_qnty")];
									}
								}
							}
							?>
							<!--<tr bgcolor="#CCCCCC" style="font-weight: bold ">-->
							<tr class="tbl_bottom" bgcolor="#CCCCCC" style="font-weight: bold ">
								<td colspan="11" align="right"><b>Total</b></td>
								<td align="right"><? echo number_format($tot_receive_qny,2); ?></td>
								<td>&nbsp;</td>
								<td align="right"><? echo number_format($tot_amount_qny,2); ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<?php
						}
						foreach($dataArr['Receive'][4] as $prodId=>$prodIdArr)
						{
							$transType=4;
							$tot_receive_qny = 0;
							$tot_amount_qny = 0;

							foreach($prodIdArr as $transDate=>$transDateArr)
							{
								foreach($transDateArr as $refNo=>$refNoArr)
								{
									foreach($refNoArr as $reqNo=>$row)
									{
										$i++;
										if ($i % 2 == 0)
											$bgcolor = "#E9F3FF";
										else
											$bgcolor = "#FFFFFF";
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="50"><? echo $i; ?></td>
											<td width="80" style="word-wrap:break-word; word-break: break-all;"><? echo $transDate; ?></td>
											<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $refNo; ?></td>
											<td width="100" style="word-wrap:break-word; word-break: break-all; mso-number-format:'\@'; text-align: center;"><p><? echo $row['callan_no']; ?></p></td>
											<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['store_name']; ?></td>
											<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['buyer_name']; ?> &nbsp;</td>
											<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $transaction_type[$transType]; ?></td>
											<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['basis']; ?></td>
											<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['wo_pi_no']; ?></td>
											<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['purpose']; ?></td>
											<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['transfer_with']; ?></td>
											<td width="80" align="right"><? echo number_format($row['cons_qty'],2); ?></td>
											<td width="80" align="right"><? echo number_format($row['cons_rate'],2); ?></td>
											<td width="100" align="right"><? echo number_format($row['cons_amount'],2); ?></td>
											<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['remarks'] ?></td>
											<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['grey_lot']; ?></td>
											<td width="120"><? echo $row['grey_yarn_rate']; ?></td>
											<td><? echo $row['wo_rate']; ?></td>
										</tr>
										<?
										$tot_receive_qny += $row['cons_qty'];
										$tot_amount_qny += $row['cons_amount'];
										
										$grand_tot_receive_qny += $row['cons_qty'];
										$grand_tot_amount_qny += $row['cons_amount'];
	
										$currentStockQnty[$row['store_name']]["receiveQnty"] += $row['cons_qty'];
										//$currentStockReject[$row['store_name']] = $receiveRow[csf("cons_reject_qnty")];
										
										if($transType == 1)
										{
											$summary_array[$row['yarn_count']][$row['yarn_composition']][$row['store_name']]['receive'] += $row['cons_qty'];
										}
										
										$summary_array[$row['yarn_count']][$row['yarn_composition']][$row['store_name']]['totalReceive'] += $row['cons_qty'];
										//$summary_array[$row['store_name']]['reject'] += $receiveRow[csf("cons_reject_qnty")];
									}
								}
							}
							?>
							<!--<tr bgcolor="#CCCCCC" style="font-weight: bold ">-->
							<tr class="tbl_bottom" bgcolor="#CCCCCC" style="font-weight: bold ">
								<td colspan="11" align="right"><b>Total</b></td>
								<td align="right"><? echo number_format($tot_receive_qny,2); ?></td>
								<td>&nbsp;</td>
								<td align="right"><? echo number_format($tot_amount_qny,2); ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<?php
						}
						unset($i);
						?>
                        <tr bgcolor="#CCCCCC" style="font-weight: bold ">
                            <td colspan="11" align="right"><b>Grand Total</b></td>
                            <td align="right"><? echo number_format($grand_tot_receive_qny,2); ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><? echo number_format($grand_tot_amount_qny,2); ?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
						</table> 
					</div>
				<!--</fieldset>-->
                <?
			}

			//for issue
			if(!empty($dataArr['Issue']))
			{
				?>
                <!--<fieldset style="width:2050px; float:left;margin-top: 5px;">-->
                    <div>
                        <table style="width:2030px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
                            <thead>
                                <tr>
                                    <td colspan="19" style="font-size:14px; font-weight:bold" align="center">Issue Status</td>
                                </tr>
                                <tr>
                                    <th rowspan="2" width="50">Sl</th>
                                    <th rowspan="2" width="80">Trans Date</th>
                                    <th rowspan="2" width="120">Trans Ref No</th>
                                    <th rowspan="2" width="100">Callan No</th>
                                    <th rowspan="2" width="120">Store Name</th>
                                    <th rowspan="2" width="120">Buyer</th>
                                    <th rowspan="2" width="120">FSO No</th>
                                    <th rowspan="2" width="120">Fabric Booking No</th>
                                    <th rowspan="2" width="120">Style</th>
                                    <th rowspan="2" width="120">Trans type</th>
                                    <th rowspan="2" width="120">Basis</th>
                                    <th rowspan="2" width="120">Issue Purpose</th>
                                    <th rowspan="2" width="120">Requsition/WO No</th>
                                    <th rowspan="2" width="120">Transaction With</th>
                                    <th colspan="3" width="260">Issue</th>
                                    <th rowspan="2" width="120">Returnable Qty</th>
                                    <th rowspan="2">Issue return Qty.</th>
                                </tr>
                                <tr>
                                    <th width="80" align="center">Qnty</th>
                                    <th width="80" align="center">Rate</th>
                                    <th width="100" align="center">Value</th>
                                </tr>
                            </thead>
                        </table>
                        <div style="width:2050px;  max-height:250px" id="scroll_body" align="left"> 
                            <table style="width:2030px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left" >
								<?php
                                $j = 0;
								foreach($dataArr['Issue'][2] as $prodId=>$prodIdArr)
								{
									$transType = 2;
									$tot_issue_qny = 0;
									$tot_issue_amount_qny = 0;
									
									foreach($prodIdArr as $transDate=>$transDateArr)
									{
										foreach($transDateArr as $refNo=>$refNoArr)
										{
											foreach($refNoArr as $reqNo=>$row)
											{
												$j++;
												if ($j % 2 == 0)
													$bgcolor = "#E9F3FF";
												else
													$bgcolor = "#FFFFFF";
												?>
												<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('itr_<? echo $j; ?>', '<? echo $bgcolor; ?>')" id="itr_<? echo $j; ?>">
													<td width="50"><? echo $j; ?></td>
													<td width="80" style="word-wrap:break-word; word-break: break-all;"><? echo $transDate; ?></td>
													<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $refNo; ?></td>
													<td width="100" style="word-wrap:break-word; word-break: break-all; mso-number-format:'\@'; text-align: center;"><p><? echo $row['callan_no']; ?></p></td>
													<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['store_name']; ?></td>
													<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['buyer_name']; ?></td>
													
													<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['fso_no']; ?></td>
													<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['fab_booking_no']; ?></td>
													<td width="120" style="word-wrap:break-word; word-break: break-all;mso-number-format:'\@';"><? echo $row['style_ref_no']; ?></td>
													<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $transaction_type[$transType]; ?></td>
													<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['basis'] ?></td>
													<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['purpose']; ?></td>
													<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['requisition_no']; ?></td>
													<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['transfer_with']; ?></td>
													<td width="80" align="right"><? echo number_format($row['cons_qty'],2); ?></td>
													<td width="80" align="right"><? echo number_format($row['cons_rate'],2); ?></td>
													<td width="100" align="right"><? echo number_format($row['cons_amount'],2); ?></td>
													<td width="120" align="right"><? echo number_format($row['returnable_qty'],2); ?></td>
													<td align="right"><? echo number_format($row['issue_return_qty'],2); ?></td>
												</tr>
												<?
												$tot_issue_qny += $row['cons_qty'];
												$tot_issue_amount_qny += $row['cons_amount'];
												
												$grand_tot_issue_qny += $row['cons_qty'];
												$grand_tot_issue_amount_qny += $row['cons_amount'];
												
												$currentStockQnty[$row['store_name']]["issueQnty"] += $row['cons_qty'];
												if($transType == 2)
												{
													$summary_array[$row['yarn_count']][$row['yarn_composition']][$row['store_name']]['issue'] += $row['cons_qty'];
												}
												
												$summary_array[$row['yarn_count']][$row['yarn_composition']][$row['store_name']]['returnable_qty'] += $row['returnable_qty'];
												$summary_array[$row['yarn_count']][$row['yarn_composition']][$row['store_name']]['issue_return_qty'] += $row['issue_return_qty'];
											}
										}
									}
									?>
									<tr bgcolor="#CCCCCC" style="font-weight: bold ">
										<td colspan="14" align="right"><b>Total</b></td>
										<td align="right"><? echo number_format($tot_issue_qny,2); ?></td>
										<td>&nbsp;</td>
										<td align="right"><? echo number_format($tot_issue_amount_qny,2); ?></td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
									<?php
								}
								
								foreach($dataArr['Issue'][3] as $prodId=>$prodIdArr)
								{
									$transType = 3;
									$tot_issue_qny = 0;
									$tot_issue_amount_qny = 0;
									
									foreach($prodIdArr as $transDate=>$transDateArr)
									{
										foreach($transDateArr as $refNo=>$refNoArr)
										{
											foreach($refNoArr as $reqNo=>$row)
											{
												$j++;
												if ($j % 2 == 0)
													$bgcolor = "#E9F3FF";
												else
													$bgcolor = "#FFFFFF";
												
												?>
												<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('itr_<? echo $j; ?>', '<? echo $bgcolor; ?>')" id="itr_<? echo $j; ?>">
													<td width="50"><? echo $j; ?></td>
													<td width="80" style="word-wrap:break-word; word-break: break-all;"><? echo $transDate; ?></td>
													<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $refNo; ?></td>
													<td width="100" style="word-wrap:break-word; word-break: break-all; mso-number-format:'\@'; text-align: center;"><p><? echo $row['callan_no']; ?></p></td>
													<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['store_name']; ?></td>
													<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['buyer_name']; ?></td>
													
													<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['fso_no']; ?></td>
													<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['fab_booking_no']; ?></td>
													<td width="120" style="word-wrap:break-word; word-break: break-all;mso-number-format:'\@';"><? echo $row['style_ref_no']; ?></td>
													<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $transaction_type[$transType]; ?></td>
													<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['basis'] ?></td>
													<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['purpose']; ?></td>
													<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['requisition_no']; ?></td>
													<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['transfer_with']; ?></td>
													<td width="80" align="right"><? echo number_format($row['cons_qty'],2); ?></td>
													<td width="80" align="right"><? echo number_format($row['cons_rate'],2); ?></td>
													<td width="100" align="right"><? echo number_format($row['cons_amount'],2); ?></td>
													<td width="120" align="right"><? echo number_format($row['returnable_qty'],2); ?></td>
													<td align="right"><? echo number_format($row['issue_return_qty'],2); ?></td>
												</tr>
												<?
												$tot_issue_qny += $row['cons_qty'];
												$tot_issue_amount_qny += $row['cons_amount'];
												
												$grand_tot_issue_qny += $row['cons_qty'];
												$grand_tot_issue_amount_qny += $row['cons_amount'];
												
												$currentStockQnty[$row['store_name']]["issueQnty"] += $row['cons_qty'];
												if($transType == 2)
												{
													$summary_array[$row['yarn_count']][$row['yarn_composition']][$row['store_name']]['issue'] += $row['cons_qty'];
												}
												
												$summary_array[$row['yarn_count']][$row['yarn_composition']][$row['store_name']]['returnable_qty'] += $row['returnable_qty'];
												$summary_array[$row['yarn_count']][$row['yarn_composition']][$row['store_name']]['issue_return_qty'] += $row['issue_return_qty'];
											}
										}
									}
									?>
									<tr bgcolor="#CCCCCC" style="font-weight: bold ">
										<td colspan="14" align="right"><b>Total</b></td>
										<td align="right"><? echo number_format($tot_issue_qny,2); ?></td>
										<td>&nbsp;</td>
										<td align="right"><? echo number_format($tot_issue_amount_qny,2); ?></td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
									<?php
								}
                                //print_r($summary_array);
                                unset($j);
                                ?>
                                <tr bgcolor="#CCCCCC" style="font-weight: bold ">
                                    <td colspan="14" align="right"><b>Grand Total</b></td>
                                    <td align="right"><? echo number_format($grand_tot_issue_qny,2); ?></td>
                                    <td>&nbsp;</td>
                                    <td align="right"><? echo number_format($grand_tot_issue_amount_qny,2); ?></td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                <!--</fieldset>-->
                <?
            }
			?>
            <br>
            <table style="width:400px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
                <thead>
                    <tr>
                        <th colspan="3" style="font-size:14px; font-weight:bold" align="center">Current Stock Status</th>
                    </tr>
                    <tr>
                        <th width="150">Company</th>
                        <th width="150">Store</th>
                        <th>Total Stock</th>
                    </tr>
                </thead>
                <tbody>
                <?php
				foreach($currentStockQnty as $key=>$val)
				{
					$totalStock = $val['receiveQnty']- $val['issueQnty'];
					
                	?>
					<tr>
                    	<td><? echo $companyArr[$cbo_company_id]; ?></td>
                    	<td><? echo $key; ?></td>
                    	<td align="right"><? echo number_format($totalStock,2); ?></td>
                    </tr>
                    <?php
				}
                ?>
                </tbody>
            </table>
            <br>
            <table style="width:1140px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
                <thead>
                    <tr>
                        <th colspan="11" style="font-size:14px; font-weight:bold" align="center">Stock Summary</th>
                    </tr>
                    <tr>
                        <th width="80">Count</th>
                        <th width="120">Composition</th>
                        <th width="120">Company</th>
                        <th width="120">Store</th>
                        <th width="100">Receive Qnty</th>
                        <th width="100">Issue Qny</th>
                        <th width="100">Returnable Qty</th>
                        <th width="100">Issue return Qty.</th>
                        <th width="100">Return Balance</th>
                        <th width="100">Stock In Hand</th>
                        <th width="100">Rejected</th>
                    </tr>
                </thead>
                <tbody>
                <?php
				foreach($summary_array as $count=>$countArr)
				{
					foreach($countArr as $compStr=>$compStrArr)
					{
						foreach($compStrArr as $store=>$val)
						{
							$return_balance = $val['returnable_qty']-$val['issue_return_qty'];
							$stockInHand = $val['totalReceive']-$val['issue'];
							?>
							<tr>
								<td><? echo $count; ?></td>
								<td><? echo $compStr; ?></td>
								<td><? echo $companyArr[$cbo_company_id]; ?></td>
								<td><? echo $store; ?></td>
								<td align="right"><? echo number_format($val['receive'],2); ?></td>
								<td align="right"><? echo number_format($val['issue'],2); ?></td>
								<td align="right"><? echo number_format($val['returnable_qty'],2); ?></td>
								<td align="right"><? echo number_format($val['issue_return_qty'],2); ?></td>
								<td align="right"><? echo number_format($return_balance,2); ?></td>
								<td align="right"><? echo number_format($stockInHand,2); ?></td>
								<td align="right"><? //echo number_format($val['receive'],2); ?></td>
							</tr>
							<?php
						}
					}
				}
				?>
                </tbody>
            </table>
            <?php
        }
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
    echo $html."####".$filename;
    exit();
}

if ($action == "generate_report_17052021")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$cbo_company_id = str_replace("'", "", trim($cbo_company_id));
    $txt_product_id = str_replace("'", "", trim($txt_product_id));
    /*$cbo_method = str_replace("'", "", $cbo_method);
    $from_date = str_replace("'", "", $txt_date_from);
    $to_date = str_replace("'", "", $txt_date_to);*/

	$search_cond = "";
    /*if ($db_type == 0)
	{
        if ($from_date != "" && $to_date != "")
            $search_cond .= " and a.transaction_date  between '" . change_date_format($from_date, 'yyyy-mm-dd') . "' and '" . change_date_format($to_date, 'yyyy-mm-dd') . "'";
    }
    else
	{
        if ($from_date != "" && $to_date != "")
            $search_cond .= " and a.transaction_date  between '" . date("j-M-Y", strtotime($from_date)) . "' and '" . date("j-M-Y", strtotime($to_date)) . "'";
    }*/

	/*$search_cond = "";
	if ($db_type == 0) {
		if ($from_date != "" && $to_date != "")
			$search_cond .= " and a.transaction_date  between '" . change_date_format($from_date, 'yyyy-mm-dd') . "' and '" . change_date_format($to_date, 'yyyy-mm-dd') . "'";
	}
	else {
		if ($from_date != "" && $to_date != "")
			$search_cond .= " and a.transaction_date  between '" . date("j-M-Y", strtotime($from_date)) . "' and '" . date("j-M-Y", strtotime($to_date)) . "'";
	}*/

	/*$lot = str_replace("'", "", trim($txt_lot_no));
	if (str_replace("'", "", trim($txt_lot_no)) != "")
		$search_string = " and b.lot='$lot'";
	else
		$search_string = "";*/

	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0", "id", "supplier_name");
	$transMrrArr = return_library_array("select id,transfer_system_id from  inv_item_transfer_mst", "id", "transfer_system_id");
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer", "id", "buyer_name");
	$store_arr = return_library_array("select id,store_name from lib_store_location", "id", "store_name");
	$count_arr = return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$color_library = return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$methodArr = array(0 => "Weighted Average", 1 => "FIFO", 2 => "LIFO");

	$sql_receive_mrr = "SELECT A.ID AS TRID, A.TRANSACTION_TYPE, A.BUYER_ID, B.RECV_NUMBER, B.KNITTING_SOURCE, B.KNITTING_COMPANY, B.SUPPLIER_ID, B.RECEIVE_PURPOSE, B.LOAN_PARTY, B.BOOKING_ID, B.BOOKING_NO, B.RECEIVE_BASIS, B.CHALLAN_NO, B.REMARKS 
	FROM INV_TRANSACTION A, INV_RECEIVE_MASTER B
	WHERE A.MST_ID=B.ID AND A.PROD_ID IN (".$txt_product_id.") AND A.TRANSACTION_TYPE IN (1,4) AND A.ITEM_CATEGORY=1 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.COMPANY_ID = ".$cbo_company_id."";
	//echo $sql_receive_mrr;
	$result_rcv = sql_select($sql_receive_mrr);
	$receiveMRR = array();
	$remarksArr = array();
	$trWiseReceiveMRR = array();
	$yarnDyeingIdArr = array();
	foreach ($result_rcv as $row) 
	{
		$receiveMRR[$row['TRID'] . $row['TRANSACTION_TYPE']] = $row['RECV_NUMBER'];
		$remarksArr[$row['TRID'] . $row['TRANSACTION_TYPE']] = $row['REMARKS'];
		$trWiseReceiveMRR[$row['TRID']] = $row['RECV_NUMBER'];
		$receive_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["knitting_source"] = $row['KNITTING_SOURCE'];
		$receive_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["knitting_company"] = $row['KNITTING_COMPANY'];
		$receive_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["knitting_supplier"] = $row['SUPPLIER_ID'];
		$receive_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["receive_purpose_id"] = $row['RECEIVE_PURPOSE'];
		$receive_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["loan_party"] = $row['LOAN_PARTY'];
		$receive_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["buyer_id"] = $row['BUYER_ID'];
		$receive_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["receive_purpose"] = $yarn_issue_purpose[$row['RECEIVE_PURPOSE']];
		$receive_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["receive_basis"] = $row['RECEIVE_BASIS'];
		$receive_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["challan_no"] = $row['CHALLAN_NO'];
		$receive_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["booking_no"] = $row['BOOKING_NO'];
		//$receive_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["order_rate"] = $row['ORDER_RATE'];
		//$receive_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["dye_charge"] = $row['DYE_CHARGE'];

		if($row['TRANSACTION_TYPE']==4)
		{
			$issue_ret_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["knitting_source"] = $row['KNITTING_SOURCE'];
			$issue_ret_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["knitting_company"] = $row['KNITTING_COMPANY'];
			$issue_ret_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["knitting_supplier"] = $row['KNITTING_COMPANY'];
			//$issue_ret_source[$row['TRID'] . $row['TRANSACTION_TYPE']]["knitting_supplier"] = $row['KNITTING_COMPANY'];
		}

		if( $row['RECEIVE_PURPOSE']==2 || $row['RECEIVE_PURPOSE']==7 || $row['RECEIVE_PURPOSE']==12 || $row['RECEIVE_PURPOSE']==15 || $row['RECEIVE_PURPOSE']== 38 || $row['RECEIVE_PURPOSE']==46 || $row['RECEIVE_PURPOSE']==50 || $row['RECEIVE_PURPOSE']==51 )
		{
			$wo_booking_id[] = $row['BOOKING_ID'];
			$wo_rcv_booking_id[$row['TRID']] = $row['BOOKING_ID'];
		}
		
		//for yarn dyeing
		/*if( $row['RECEIVE_BASIS'] == 2 && $row['RECEIVE_PURPOSE']==2 )
		{
			$yarnDyeingIdArr[$row['BOOKING_ID']] = $row['BOOKING_ID'];
		}*/
	}
	unset($result_rcv);
	//echo "<pre>";
	//print_r($wo_rcv_booking_id);

	if(!empty($wo_booking_id))
	{
		$wo_sql_result =sql_select("SELECT A.ID, A.SUPPLIER_ID, A.PAY_MODE, C.LOT FROM WO_YARN_DYEING_MST A, WO_YARN_DYEING_DTLS B, PRODUCT_DETAILS_MASTER C WHERE A.ID = B.MST_ID AND B.PRODUCT_ID = C.ID AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND A.ID IN (".implode(',',array_unique($wo_booking_id)).") AND A.COMPANY_ID = ".$cbo_company_id."");
		foreach ($wo_sql_result as $row) 
		{
			$wo_data[$row['ID']]['pay_mode'] = $row['PAY_MODE'];
			$wo_data[$row['ID']]['lot'] = $row['LOT'];
		}
		unset($wo_sql_result);	
	}

    // issue MRR array------------------------------------------------
	$sql_issue_mrr = "SELECT A.ID AS TRID, A.TRANSACTION_TYPE, B.ISSUE_NUMBER, B.ISSUE_PURPOSE, B.ISSUE_BASIS, B.CHALLAN_NO, B.REMARKS FROM INV_TRANSACTION A, INV_ISSUE_MASTER B WHERE A.PROD_ID IN ($txt_product_id) AND A.MST_ID=B.ID AND A.TRANSACTION_TYPE IN (2,3) AND A.ITEM_CATEGORY=1 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.COMPANY_ID = ".$cbo_company_id."";
	$result_iss = sql_select($sql_issue_mrr);
	$issueMRR = array();
	$issueMRR = array();
	$issuePupose = array();
	$issueInfoArr = array();
	foreach ($result_iss as $row)
	{
		$issueMRR[$row['TRID'] . $row['TRANSACTION_TYPE']] = $row['ISSUE_NUMBER'];
		$remarksArr[$row['TRID'] . $row['TRANSACTION_TYPE']] = $row['REMARKS'];
		$issuePupose[$row['TRID']] = $yarn_issue_purpose[$row['ISSUE_PURPOSE']];
		$issueInfoArr[$row['TRID']]['issue_basis'] = $row['ISSUE_BASIS'];
		$issueInfoArr[$row['TRID']]['challan_no'] = $row['CHALLAN_NO'];
	}
	unset($result_iss);
	//echo "<pre>";
	//print_r($issueMRR);

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
			/*if ($from_date != "" && $to_date != "")
			{
				if ($db_type == 2)
					$from_date = date("j-M-Y", strtotime($from_date));
				if ($db_type == 0)
					$from_date = change_date_format($from_date, 'yyyy-mm-dd');
					
				$sqlTR = "select  prod_id, SUM(CASE WHEN transaction_type in (1,4,5) THEN cons_quantity ELSE 0 END) as receive,
				SUM(CASE WHEN transaction_type in (2,3,6) THEN cons_quantity ELSE 0 END) as issue,
				SUM(CASE WHEN transaction_type in (1,4,5) THEN cons_amount ELSE 0 END) as rcv_balance,
				SUM(CASE WHEN transaction_type in (2,3,6) THEN cons_amount ELSE 0 END) as iss_balance
				from inv_transaction
				where transaction_date < '" . $from_date . "' and status_active=1 and is_deleted=0 $storeCond group by prod_id";
				$trResult = sql_select($sqlTR);
				$opning_bal_arr = array();
				foreach ($trResult as $row)
				{
					$opning_bal_arr[$row[csf("prod_id")]]["prod_id"] = $row[csf("prod_id")];
					$opning_bal_arr[$row[csf("prod_id")]]["receive"] = $row[csf("receive")];
					$opning_bal_arr[$row[csf("prod_id")]]["issue"] = $row[csf("issue")];
					$opning_bal_arr[$row[csf("prod_id")]]["rcv_balance"] = $row[csf("rcv_balance")];
					$opning_bal_arr[$row[csf("prod_id")]]["iss_balance"] = $row[csf("iss_balance")];
				}
			}*/
			
			/*$cbo_store_name=str_replace("'","",$cbo_store_name);
			$store_cond="";
			if($cbo_store_name>0) $store_cond=" and a.store_id=$cbo_store_name";*/
			
			$sql = "SELECT A.ID, A.MST_ID, A.PROD_ID, A.STORE_ID, A.TRANSACTION_DATE, A.RECEIVE_BASIS, A.INSERT_DATE, A.TRANSACTION_TYPE, A.CONS_QUANTITY, A.CONS_RATE, A.CONS_AMOUNT, A.CONS_REJECT_QNTY, A.REMARKS, A.ORDER_RATE, A.DYE_CHARGE, A.RETURN_QNTY, A.REQUISITION_NO, 
			B.PRODUCT_NAME_DETAILS, B.COLOR, B.YARN_COMP_PERCENT1ST, B.YARN_COUNT_ID, YARN_COMP_TYPE1ST, B.YARN_TYPE, B.UNIT_OF_MEASURE, B.LOT, B.SUPPLIER_ID, B.COLOR, 
			C.KNIT_DYE_SOURCE, C.KNIT_DYE_COMPANY, C.ISSUE_BASIS, C.ISSUE_PURPOSE, C.LOAN_PARTY, C.BUYER_JOB_NO, C.BOOKING_ID, c.BOOKING_NO, CASE WHEN A.BUYER_ID>0 THEN A.BUYER_ID ELSE C.BUYER_ID END AS BUYER_ID
			FROM INV_TRANSACTION A LEFT JOIN INV_ISSUE_MASTER C ON A.MST_ID=C.ID AND A.TRANSACTION_TYPE IN (2,3,6), PRODUCT_DETAILS_MASTER B
			WHERE A.PROD_ID IN (".$txt_product_id.") AND A.COMPANY_ID = ".$cbo_company_id." AND A.PROD_ID=B.ID AND A.ITEM_CATEGORY=1 AND B.ITEM_CATEGORY_ID=1 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 $search_cond
			ORDER BY A.PROD_ID, A.INSERT_DATE, A.ID ASC";
			//echo $sql; die;
			$result = sql_select($sql);
			$all_issue_trans_id=array();
			$salesOrderNoArr = array();
			$requisitionNoArr = array();
			$sampleBookingNoArr = array();
			foreach($result as $row)
			{
				$all_issue_trans_id[$row['ID']]=$row['ID'];
				
				//for wo/booking basis issue
				if($row['ISSUE_BASIS'] == 1)
				{
					//for yarn dyeing purpose
					if($row['ISSUE_PURPOSE'] == 2 || $row['ISSUE_PURPOSE'] == 7 || $row['ISSUE_PURPOSE'] == 12 || $row['ISSUE_PURPOSE'] == 15 || $row['ISSUE_PURPOSE'] == 38 || $row['ISSUE_PURPOSE'] == 46 || $row['ISSUE_PURPOSE'] == 50 || $row['ISSUE_PURPOSE'] == 51)
					{
						$salesOrderNoArr[$row['BUYER_JOB_NO']] = $row['BUYER_JOB_NO'];
					}
					//for sample without order purpose
					elseif($row['ISSUE_PURPOSE'] == 8)
					{
						$sampleBookingNoArr[$row['BOOKING_NO']] = $row['BOOKING_NO'];
					}
				}
				//for requisition basis issue
				elseif($row['ISSUE_BASIS'] == 3)
				{
					$requisitionNoArr[$row['REQUISITION_NO']] = $row['REQUISITION_NO'];
				}
			}
			//echo "<pre>";
			//print_r($requisitionNoArr);
			
			//for requisition information
			$sqlReq = "SELECT A.REQUISITION_NO, C.SALES_BOOKING_NO, C.JOB_NO FROM PPL_YARN_REQUISITION_ENTRY A, PPL_PLANNING_ENTRY_PLAN_DTLS B, FABRIC_SALES_ORDER_MST C WHERE A.KNIT_ID = B.DTLS_ID AND B.BOOKING_NO = C.SALES_BOOKING_NO AND A.REQUISITION_NO IN (".implode(",",$requisitionNoArr).")";
			//echo $sqlReq;
			$sqlReqRslt = sql_select($sqlReq);
			$requisitionInfoArr = array();
			foreach($sqlReqRslt as $row)
			{
				$requisitionInfoArr[$row['REQUISITION_NO']]['fso_no'] = $row['JOB_NO'];
				$requisitionInfoArr[$row['REQUISITION_NO']]['fab_booking_no'] = $row['SALES_BOOKING_NO'];
				$requisitionInfoArr[$row['REQUISITION_NO']]['requisition_no'] = $row['REQUISITION_NO'];
			}
			unset($sqlReqRslt);
			//echo "<pre>";
			//print_r($requisitionInfoArr);
			
			//for yarn dyeing information
			$sqlSales = "SELECT C.SALES_BOOKING_NO, C.JOB_NO FROM FABRIC_SALES_ORDER_MST C WHERE C.JOB_NO IN ('".implode("','",$salesOrderNoArr)."')";
			//echo $sqlSales;
			$sqlSalesRslt = sql_select($sqlSales);
			$salesInfoArr = array();
			foreach($sqlSalesRslt as $row)
			{
				$salesInfoArr[$row['JOB_NO']]['fso_no'] = $row['JOB_NO'];
				$salesInfoArr[$row['JOB_NO']]['fab_booking_no'] = $row['SALES_BOOKING_NO'];
			}
			unset($sqlSalesRslt);
			//echo "<pre>";
			//print_r($salesInfoArr);
			
			//for sample information
			$sqlSample = "SELECT C.SALES_BOOKING_NO, C.JOB_NO FROM FABRIC_SALES_ORDER_MST C WHERE C.SALES_BOOKING_NO IN ('".implode("','",$sampleBookingNoArr)."')";
			//echo $sqlSales;
			$sqlSampleRslt = sql_select($sqlSample);
			$sampleInfoArr = array();
			foreach($sqlSampleRslt as $row)
			{
				$sampleInfoArr[$row['SALES_BOOKING_NO']]['fso_no'] = $row['JOB_NO'];
			}
			unset($sqlSampleRslt);
			//echo "<pre>";
			//print_r($salesInfoArr);

			$check_is_sales_sql = "SELECT TRANS_ID, IS_SALES FROM ORDER_WISE_PRO_DETAILS WHERE TRANS_ID IN(".implode(",",$all_issue_trans_id).")";
			$check_is_sales=sql_select($check_is_sales_sql);
			$is_sales_arr=array();
			foreach ($check_is_sales as $is_sales_row)
			{
				$is_sales_arr[$is_sales_row['TRANS_ID']]=$is_sales_row['IS_SALES'];
			}
			unset($check_is_sales);
			
			$issueTransIdArr=array_chunk($all_issue_trans_id,999);
			$issue_job_cond=" AND(";
			foreach($issueTransIdArr as $issue_trans_id)
			{
				if($issue_job_cond==" AND(")
					$issue_job_cond.=" C.TRANS_ID IN(".implode(',', $issue_trans_id).")";
				else
					$issue_job_cond.=" OR C.TRANS_ID in(".implode(',', $issue_trans_id).")";
			}
			$issue_job_cond.=")";

			if($issue_job_cond == " AND()")
			{
				$issue_job_cond = "";
			}

			$po_details_sql="SELECT A.ID AS JOB_ID, A.JOB_NO, A.STYLE_REF_NO, A.BUYER_NAME, B.ID AS PO_ID, B.PO_NUMBER, B.GROUPING, C.TRANS_ID FROM WO_PO_DETAILS_MASTER A, WO_PO_BREAK_DOWN B, ORDER_WISE_PRO_DETAILS C WHERE A.JOB_NO=B.JOB_NO_MST AND B.ID=C.PO_BREAKDOWN_ID AND A.STATUS_ACTIVE=1 AND B.STATUS_ACTIVE=1 AND C.STATUS_ACTIVE=1 AND A.COMPANY_NAME = ".$cbo_company_id." $issue_job_cond GROUP BY A.ID, A.JOB_NO, A.STYLE_REF_NO, A.BUYER_NAME, B.ID, B.PO_NUMBER, B.GROUPING, C.TRANS_ID";
			$po_details=sql_select($po_details_sql);
			$jobIssueData=$bookingData=array();
			foreach($po_details as $row)
			{
				$jobIssueData[$row['TRANS_ID']]["style_ref_no"]=$row['STYLE_REF_NO'];
				$jobIssueData[$row['TRANS_ID']]["buyer_name"]=$row['BUYER_NAME'];
				$jobIssueData[$row['TRANS_ID']]["po_number"]=$row['PO_NUMBER'];
				$jobIssueData[$row['TRANS_ID']]["grouping"]=$row['GROUPING'];
			}
			unset($po_details);

			$sales_order_po_data_sql = "SELECT A.ID, A.JOB_NO, A.SALES_BOOKING_NO, A.BUYER_ID, A.WITHIN_GROUP, A.STYLE_REF_NO, A.BOOKING_WITHOUT_ORDER, D.TRANS_ID FROM FABRIC_SALES_ORDER_MST A LEFT JOIN ORDER_WISE_PRO_DETAILS D ON A.ID=D.PO_BREAKDOWN_ID WHERE A.STATUS_ACTIVE = 1 AND A.COMPANY_ID = ".$cbo_company_id." AND D.TRANS_ID IN(".implode(',',$issue_trans_id).") GROUP BY A.ID, A.JOB_NO, A.SALES_BOOKING_NO, A.BUYER_ID, A.WITHIN_GROUP, A.STYLE_REF_NO, A.BOOKING_WITHOUT_ORDER, D.TRANS_ID";
			$sales_order_po_data = sql_select($sales_order_po_data_sql);
			foreach ($sales_order_po_data as  $val)
			{
				$sales_order_po_array[$val['TRANS_ID']]["order_no"] = $val['JOB_NO'];
				$sales_order_po_array[$val['TRANS_ID']]["booking_no"] = $val['SALES_BOOKING_NO'];
				$sales_order_po_array[$val['TRANS_ID']]["within_group"] = $val['WITHIN_GROUP'];
				$sales_order_po_array[$val['TRANS_ID']]["style_ref_no"] = $val['STYLE_REF_NO'];
				$sales_order_po_array[$val['TRANS_ID']]["buyer_id"] = $val['BUYER_ID'];
				$sales_booking[$val['SALES_BOOKING_NO']] = "'".$val['SALES_BOOKING_NO']."'";
			}
			unset($sales_order_po_data);
			/*echo "<pre>";
			print_r($sales_booking);
			echo "</pre>";*/

			if(!empty($sales_booking))
			{
				$job_sql = sql_select("SELECT A.BOOKING_NO, B.BUYER_NAME, B.STYLE_REF_NO FROM WO_BOOKING_DTLS A, WO_PO_DETAILS_MASTER B WHERE A.JOB_NO=B.JOB_NO AND A.STATUS_ACTIVE=1 AND B.STATUS_ACTIVE=1 AND A.BOOKING_NO IN(".implode(",", $sales_booking).") GROUP BY A.BOOKING_NO, B.BUYER_NAME, B.STYLE_REF_NO
				UNION ALL
				SELECT A.BOOKING_NO, A.BUYER_ID AS BUYER_NAME, B.STYLE_REF_NO FROM WO_NON_ORD_SAMP_BOOKING_MST A, FABRIC_SALES_ORDER_MST B WHERE A.BOOKING_NO = B.SALES_BOOKING_NO AND A.STATUS_ACTIVE=1 AND B.STATUS_ACTIVE=1 AND A.BOOKING_NO IN(".implode(",", $sales_booking).") GROUP BY A.BOOKING_NO, A.BUYER_ID, B.STYLE_REF_NO");
				foreach ($job_sql as $job_row)
				{
					$booking_job_arr[$job_row['BOOKING_NO']]["style_ref_no"] = $job_row['STYLE_REF_NO'];
					$booking_job_arr[$job_row['BOOKING_NO']]["buyer_name"] = $job_row['BUYER_NAME'];
				}
				unset($job_sql);
			}
			/*echo "<pre>";
			print_r($booking_job_arr);
			echo "</pre>";*/
			
			//data preparing here
			$m = 1;
			//$productIdArr = array();
			$k = 1;
			$dataArr = array();
			$headerInfoArr = array();
			foreach ($result as $row) 
			{
				//for recrive information
				$receiveInfo_buyer = $receive_source[$row['ID'] . $row['TRANSACTION_TYPE']]["buyer_id"];
				$receiveInfo_basis = $receive_source[$row['ID'] . $row['TRANSACTION_TYPE']]["receive_basis"];
				$receiveInfo_purpose = $receive_source[$row['ID'] . $row['TRANSACTION_TYPE']]["receive_purpose"];
				$receiveInfo_challanNo = $receive_source[$row['ID'] . $row['TRANSACTION_TYPE']]["challan_no"];
				$receiveInfo_bookingNo = $receive_source[$row['ID'] . $row['TRANSACTION_TYPE']]["booking_no"];
				$receiveInfo_purposeId = $receive_source[$row['ID'] . $row['TRANSACTION_TYPE']]["receive_purpose_id"];
				$receiveInfo_loanParty = $receive_source[$row['ID'] . $row['TRANSACTION_TYPE']]["loan_party"];
				$receiveInfo_knittingCompany = $receive_source[$row['ID'] . $row['TRANSACTION_TYPE']]["knitting_company"];

				$tranType = 'Receive';
				if ($row['TRANSACTION_TYPE'] == 2 || $row['TRANSACTION_TYPE'] == 3 || $row['TRANSACTION_TYPE'] == 6)
				{
					$tranType = 'Issue';
				}
				
				//for TRANSACTION_DATE
				$row['TRANSACTION_DATE'] = change_date_format($row['TRANSACTION_DATE']);
				
				//for transaction ref. no
				$trans_ref_no = '';
				if( $row['MST_ID']==0 && $row['RECEIVE_BASIS']==30)
				{
					$trans_ref_no = 'Adjustment';
				}
				else
				{
					if ($row['TRANSACTION_TYPE'] == 5 || $row['TRANSACTION_TYPE'] == 6)
					{
						$trans_ref_no = $transMrrArr[$row['MST_ID']];
					}
					else
					{
						$trans_ref_no = $mrrArray[$row['ID'].$row['TRANSACTION_TYPE']];
					} 
				}
				
				//for buyer
				$buyer_name = '';
				$is_sales = $is_sales_arr[$row['ID']];
				if($is_sales == 1)
				{
					$within_group = $sales_order_po_array[$row['ID']]["within_group"];
					$booking_no = $sales_order_po_array[$row['ID']]["booking_no"];
					if($within_group == 1)
					{
						$buyer_name = $buyer_arr[$booking_job_arr[$booking_no]["buyer_name"]];
						//$order_no = $sales_order_po_array[$row['ID']]["order_no"];
						$style_ref_no=$booking_job_arr[$booking_no]["style_ref_no"];
					}
					else
					{
						$buyer_name = $buyer_arr[$sales_order_po_array[$row['ID']]["buyer_id"]];
						//$order_no = $sales_order_po_array[$row['ID']]["order_no"];
						$style_ref_no=$sales_order_po_array[$row['ID']]["style_ref_no"];
					}
				}
				else
				{
					$buyer_name = $buyer_arr[$jobIssueData[$row['ID']]["buyer_name"]];
					//$order_no = $jobIssueData[$row['ID']]["po_number"];
					$style_ref_no = $jobIssueData[$row['ID']]["style_ref_no"];
					$grouping = $jobIssueData[$row['ID']]["grouping"];
				}
				
				if($row['TRANSACTION_TYPE'] == 1)
				{
					$buyer_name = $buyer_arr[$receiveInfo_buyer];
				}
				
				if(empty($buyer_name) && !empty($row['BUYER_ID']))
				{
					$buyer_name = $buyer_arr[$row['BUYER_ID']];
				}
				//for buyer end
				
				//for basis
				$basis = '';
				if ($row['TRANSACTION_TYPE'] == 1)
				{
					$basis = $receive_basis_arr[$receiveInfo_basis];
				}
				elseif ($row['TRANSACTION_TYPE'] == 4)
				{
					$basis = $issue_basis[$receiveInfo_basis];
				}
				elseif ($row['TRANSACTION_TYPE'] == 5)
				{
					//$basis = '';
				}
				else
				{
					$basis = $issue_basis[$issueInfoArr[$row['ID']]['issue_basis']];
				}
				//for basis end
				
				//for purpose
				$purpose = '';
				if ($row['TRANSACTION_TYPE'] == 1 )
				{
					$purpose = $receiveInfo_purpose;
				}
				else if ($row['TRANSACTION_TYPE'] == 2)
				{
					$purpose = $issuePupose[$row['ID']];
				}
				else if ($row['TRANSACTION_TYPE'] == 4)
				{
					$purpose = '';
				}
				//for purpose end
				
				//for challan no and wo/pi no
				$challan_no = '';
				$wo_pi_no = '';
				if ($row['TRANSACTION_TYPE'] == 1 || $row['TRANSACTION_TYPE'] == 4 || $row['TRANSACTION_TYPE'] == 5)
				{
					$challan_no = $receiveInfo_challanNo;
					$wo_pi_no = $receiveInfo_bookingNo;
				}
				else
				{
					$challan_no = $issueInfoArr[$row['ID']]['challan_no'];
				}
				//for challan no end
				
				//for transaction with
				if ($row['TRANSACTION_TYPE'] == 2)
				{
					if($row['ISSUE_PURPOSE'] == 5)
					{
						$transactionWith = $supplierArr[$row['LOAN_PARTY']];
					}
					else
					{
						if ($row['KNIT_DYE_SOURCE'] == 1)
							$transactionWith = $companyArr[$row['KNIT_DYE_COMPANY']];
						else
							$transactionWith = $supplierArr[$row['KNIT_DYE_COMPANY']];
					}

				}
				else if ($row['TRANSACTION_TYPE'] == 3)
				{
					$transactionWith = $supplierArr[$row['SUPPLIER_ID']];
				}
				else if ($row['TRANSACTION_TYPE'] == 1) 
				{
					if($receiveInfo_purposeId == 5)
					{
						$transactionWith = $supplierArr[$receiveInfo_loanParty];
					}
					else if( $receiveInfo_purposeId == 6 || $receiveInfo_purposeId==16 )
					{
						$transactionWith = $companyArr[$receiveInfo_knittingCompany];
					}
					else
					{
						$pay_mode = $wo_data[$wo_rcv_booking_id[$row['ID']]]['pay_mode'];

						if($pay_mode==3 || $pay_mode==5)
						{
							$transactionWith = $companyArr[$row['SUPPLIER_ID']];
						}
						else
						{
							$transactionWith = $supplierArr[$row['SUPPLIER_ID']];
						} 
					}
				}  
				else if ($row['TRANSACTION_TYPE'] == 4) 
				{
					if ($issue_ret_source[$row['ID'] . $row['TRANSACTION_TYPE']]["knitting_source"] == 1)
						$transactionWith = $companyArr[$issue_ret_source[$row['ID'] . $row['TRANSACTION_TYPE']]["knitting_company"]];
					else
						$transactionWith = $supplierArr[$issue_ret_source[$row['ID'] . $row['TRANSACTION_TYPE']]["knitting_supplier"]];
				}
				//for transaction with end
				
				//for yarn composition
				$composition_str = $composition[$row['YARN_COMP_TYPE1ST']]." ".$row['YARN_COMP_PERCENT1ST']."% ".$yarn_type[$row['YARN_TYPE']]." ".$color_library[$row['COLOR']];
				/*$productIdArr[$row['PROD_ID']] = $row['PROD_ID'];
				$countIdArr[$row['YARN_COUNT_ID']] = $count_arr[$row['YARN_COUNT_ID']];
				$compositionArr[$composition_str] = $composition_str;
				$colorIdArr[$row['COLOR']] = $color_library[$row['COLOR']];
				$lotArr[$row['LOT']] = $row['LOT'];
				$uomArr[$row['UNIT_OF_MEASURE']] = $unit_of_measurement[$row['UNIT_OF_MEASURE']];*/

				//for Grey Lot 	Grey Yarn Rate 	WO Rate
				$grey_lot = '';
				$grey_yarn_rate = '';
				$wo_rate = '';
				if ($row['TRANSACTION_TYPE'] == 1 && $receiveInfo_basis == 2 && $receiveInfo_purposeId == 2)
				{
					$grey_lot = $wo_data[$wo_rcv_booking_id[$row['ID']]]['lot'];
					$grey_yarn_rate = $row['ORDER_RATE'];
					$wo_rate = $row['DYE_CHARGE'];
				}

				//
				$fso_no = '';
				$fab_booking_no = '';
				$requisition_no = '';
				if($tranType == 'Issue')
				{
					//for wo/booking basis issue
					if($row['ISSUE_BASIS'] == 1)
					{
						//for yarn dyeing purpose
						if($row['ISSUE_PURPOSE'] == 2 || $row['ISSUE_PURPOSE'] == 7 || $row['ISSUE_PURPOSE'] == 12 || $row['ISSUE_PURPOSE'] == 15 || $row['ISSUE_PURPOSE'] == 38 || $row['ISSUE_PURPOSE'] == 46 || $row['ISSUE_PURPOSE'] == 50 || $row['ISSUE_PURPOSE'] == 51)
						{
							$fso_no = $salesInfoArr[$row['BUYER_JOB_NO']]['fso_no'];
							$fab_booking_no = $salesInfoArr[$row['BUYER_JOB_NO']]['fab_booking_no'];
							$requisition_no = $row['BOOKING_NO'];
						}
						//for sample without order purpose
						elseif($row['ISSUE_PURPOSE'] == 8)
						{
							$fso_no = $sampleInfoArr[$row['BOOKING_NO']]['fso_no'];
							$fab_booking_no = $row['BOOKING_NO'];
						}
					}
					//for requisition basis issue
					elseif($row['ISSUE_BASIS'] == 3)
					{
						$fso_no = $requisitionInfoArr[$row['REQUISITION_NO']]['fso_no'];
						$fab_booking_no = $requisitionInfoArr[$row['REQUISITION_NO']]['fab_booking_no'];
						$requisition_no = $requisitionInfoArr[$row['REQUISITION_NO']]['requisition_no'];
					}
				}
				
				//for remarks
				$remarks = $row['REMARKS'];
				if ($row['TRANSACTION_TYPE'] == 1 || $row['TRANSACTION_TYPE'] == 2)
				{
					$remarks = $remarksArr[$row['ID'].$row['TRANSACTION_TYPE']];
				}
				
				//for cons_qnty cons_amount
				$cons_amount = 0;
				$cons_amount = 0;
				$cons_qnty = $row['CONS_QUANTITY'];
				$cons_rate = $row['CONS_RATE'];
				$cons_amount = $cons_qnty * $row['CONS_RATE'];

$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no]['callan_no'] = $challan_no;
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no]['store_name'] = $store_arr[$row['STORE_ID']];
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no]['buyer_name'] = $buyer_name;
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no]['purpose'] = $purpose;
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no]['transfer_with'] = $transactionWith;
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no]['cons_qty'] += $cons_qnty;
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no]['cons_rate'] = $cons_rate;
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no]['cons_amount'] = $cons_amount;
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no]['remarks'] = $remarks;

$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no]['style_ref_no'] = $style_ref_no;
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no]['basis'] = $basis;
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no]['wo_pi_no'] = $wo_pi_no;
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no]['grey_lot'] = $grey_lot;
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no]['grey_yarn_rate'] = $grey_yarn_rate;
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no]['wo_rate'] = $wo_rate;

$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no]['fso_no'] = $fso_no;
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no]['fab_booking_no'] = $fab_booking_no;
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no]['requisition_no'][$requisition_no] = $requisition_no;

$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no]['returnable_qty'] = $row['RETURN_QNTY'];
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no]['issue_return_qty'] = '';

$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no]['yarn_count'] = $count_arr[$row['YARN_COUNT_ID']];
$dataArr[$tranType][$row['TRANSACTION_TYPE']][$row['PROD_ID']][$row['TRANSACTION_DATE']][$trans_ref_no]['yarn_composition'] = $composition_str;

$headerInfoArr[$tranType][$row['PROD_ID']]['yarn_count'] = $count_arr[$row['YARN_COUNT_ID']];
$headerInfoArr[$tranType][$row['PROD_ID']]['yarn_composition'] = $composition_str;
$headerInfoArr[$tranType][$row['PROD_ID']]['color'] = $color_library[$row['COLOR']];
$headerInfoArr[$tranType][$row['PROD_ID']]['lot'] = $row['LOT'];
$headerInfoArr[$tranType][$row['PROD_ID']]['unit_of_measurement'] = $unit_of_measurement[$row['UNIT_OF_MEASURE']];
			}

			/*
			| If no data is found in the user's search criteria
			| Then system will give a message No Data Found and
			| Execution will be closed
			*/
			if(empty($dataArr))
			{
				echo "<div style='width:800px; text-align:center'>".get_empty_data_msg()."</div>";
				die;
			}			
			?>
            
            <table style="width:2050px; float: left;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" > 
                <tbody>
                    <tr class="form_caption" style="border:none;">
                        <td align="center" style="border:none;font-size:16px; font-weight:bold" >Lot Wise Yarn Transaction</td>
                    </tr>
                    <tr style="border:none;">
                        <td align="center" style="border:none; font-size:14px;">
                            Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_id)]; ?>
                        </td>
                    </tr>
                    <tr style="border:none;">
                        <td align="center" style="border:none;font-size:12px; font-weight:bold">
                            <? if ($from_date != "" || $to_date != "") echo "From " . change_date_format($from_date) . " To " . change_date_format($to_date) . ""; ?>
                        </td>
                    </tr>
                    <tr style="border:none;">
                        <td style="border:none;">&nbsp;</td>
                    </tr>
            </table>
            <br/>
			<?php
			//for receive
			if(!empty($dataArr['Receive']))
			{
				?>
				<!--<table style="width:2050px; float: left;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" > 
					<tbody>
                        <tr style="border:none;">
							<td style="border:none;font-size:12px; font-weight:bold;" >
								Product Id: <? echo $row[csf('id')] ?>, Count: <? echo $yarnCountArr[$row[csf("yarn_count_id")]] ?>, Composition: <? echo $row[csf("product_name_details")] ?>, Color: <? echo $colorArr[$row[csf("color")]] ?>, Lot: <? echo $row[csf("lot")] ?>, UOM: <? echo $unit_of_measurement[$row[csf("unit_of_measure")]] ?>
							</td> 
						</tr>
						<tr>
							<td style="border:none;font-size:12px; font-weight:bold" >
								Supplier: <? echo $supplierArr[$cbo_supplier_name]; ?>, Brand: <? $brandArr[$row[csf("brand")]] ?>
							</td> 
						</tr>
						<tr>
							<td style="border:none;font-size:12px; font-weight:bold" >
								Method: <? echo $methodArr[$cbo_method]; ?>
							</td> 
						</tr>
					</tbody>
				</table>
				<br/>-->
				<!--<fieldset  style="width:1950px; float:left;">-->
					<table style="width:1930px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
						<thead>
							<tr>
								<td colspan="18" style="font-size:14px; font-weight:bold" align="center">Receiving Status</td>
							</tr>
							<tr>
								<th rowspan="2" width="50">Sl</th>
								<th rowspan="2" width="80">Trans Date</th>
								<th rowspan="2" width="120">Trans Ref No</th>
								<th rowspan="2" width="100">Callan No</th>
								<th rowspan="2" width="120">Store Name</th>
								<th rowspan="2" width="120">Buyer</th>
								<th rowspan="2" width="120">Trans type</th>
								<th rowspan="2" width="120">Receive Basis</th>
								<th rowspan="2" width="120">WO/PI No</th>
								<th rowspan="2" width="120">Receive Purpose</th>
								<th rowspan="2" width="120">Transaction With</th>
								<th colspan="3" width="260">Receive</th>
								<th rowspan="2" width="120">Remarks</th>
								<th rowspan="2" width="120">Grey Lot</th>
								<th rowspan="2" width="120">Grey Yarn Rate</th>
								<th rowspan="2">WO Rate</th>
							</tr>
							<tr>
								<th width="80" align="center">Qnty</th>
								<th width="80" align="center">Rate</th>
								<th width="100" align="center">Value</th>
							</tr>
						</thead>
					</table>
					<div style="width:1950px;  max-height:250px" id="scroll_body" align="left"> 
						<table style="width:1930px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left" >
						<?php
						$i = 0;
						foreach($dataArr['Receive'][1] as $prodId=>$prodIdArr)
						{
							$transType=1;
							$tot_receive_qny = 0;
							$tot_amount_qny = 0;
							?>
                            <tr>
                            	<td colspan="18" style="font-size:12px;color:blue;font-weight:bold;" >
								Product Id: <? echo $prodId; ?>, Count: <? echo $headerInfoArr['Receive'][$prodId]['yarn_count']; ?>, Composition: <? echo $headerInfoArr['Receive'][$prodId]['yarn_composition']; ?>, Color: <? echo $headerInfoArr['Receive'][$prodId]['color']; ?>, Lot: <? echo $headerInfoArr['Receive'][$prodId]['lot']; ?>, UOM: <? echo $headerInfoArr['Receive'][$prodId]['unit_of_measurement']; ?></td>
                            </tr>
                            <?php
							foreach($prodIdArr as $transDate=>$transDateArr)
							{
								foreach($transDateArr as $refNo=>$row)
								{
									$i++;
									if ($i % 2 == 0)
										$bgcolor = "#E9F3FF";
									else
										$bgcolor = "#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="50"><? echo $i; ?></td>
										<td width="80" style="word-wrap:break-word; word-break: break-all;"><? echo $transDate; ?></td>
										<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $refNo; ?></td>
										<td width="100" style="word-wrap:break-word; word-break: break-all; mso-number-format:'\@'; text-align: center;"><p><? echo $row['callan_no']; ?></p></td>
										<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['store_name']; ?></td>
										<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['buyer_name']; ?> &nbsp;</td>
										<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $transaction_type[$transType]; ?></td>
										<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['basis']; ?></td>
										<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['wo_pi_no']; ?></td>
										<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['purpose']; ?></td>
										<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['transfer_with']; ?></td>
										<td width="80" align="right"><? echo number_format($row['cons_qty'],2); ?></td>
										<td width="80" align="right"><? echo number_format($row['cons_rate'],2); ?></td>
										<td width="100" align="right"><? echo number_format($row['cons_amount'],2); ?></td>
										<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['remarks'] ?></td>
										<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['grey_lot']; ?></td>
										<td width="120"><? echo $row['grey_yarn_rate']; ?></td>
										<td><? echo $row['wo_rate']; ?></td>
									</tr>
									<?
									$tot_receive_qny += $row['cons_qty'];
									$tot_amount_qny += $row['cons_amount'];
									
									$grand_tot_receive_qny += $row['cons_qty'];
									$grand_tot_amount_qny += $row['cons_amount'];

									$currentStockQnty[$row['store_name']]["receiveQnty"] += $row['cons_qty'];
									//$currentStockReject[$row['store_name']] = $receiveRow[csf("cons_reject_qnty")];
									
									if($transType == 1)
									{
										$summary_array[$row['yarn_count']][$row['yarn_composition']][$row['store_name']]['receive'] += $row['cons_qty'];
									}
									
									$summary_array[$row['yarn_count']][$row['yarn_composition']][$row['store_name']]['totalReceive'] += $row['cons_qty'];
									//$summary_array[$row['store_name']]['reject'] += $receiveRow[csf("cons_reject_qnty")];
								}
							}
							?>
							<!--<tr bgcolor="#CCCCCC" style="font-weight: bold ">-->
							<tr class="tbl_bottom" bgcolor="#CCCCCC" style="font-weight: bold ">
								<td colspan="11" align="right"><b>Total</b></td>
								<td align="right"><? echo number_format($tot_receive_qny,2); ?></td>
								<td>&nbsp;</td>
								<td align="right"><? echo number_format($tot_amount_qny,2); ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<?php
						}
						foreach($dataArr['Receive'][4] as $prodId=>$prodIdArr)
						{
							$transType=4;
							$tot_receive_qny = 0;
							$tot_amount_qny = 0;

							foreach($prodIdArr as $transDate=>$transDateArr)
							{
								foreach($transDateArr as $refNo=>$row)
								{
									$i++;
									if ($i % 2 == 0)
										$bgcolor = "#E9F3FF";
									else
										$bgcolor = "#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="50"><? echo $i; ?></td>
										<td width="80" style="word-wrap:break-word; word-break: break-all;"><? echo $transDate; ?></td>
										<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $refNo; ?></td>
										<td width="100" style="word-wrap:break-word; word-break: break-all; mso-number-format:'\@'; text-align: center;"><p><? echo $row['callan_no']; ?></p></td>
										<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['store_name']; ?></td>
										<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['buyer_name']; ?> &nbsp;</td>
										<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $transaction_type[$transType]; ?></td>
										<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['basis']; ?></td>
										<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['wo_pi_no']; ?></td>
										<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['purpose']; ?></td>
										<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['transfer_with']; ?></td>
										<td width="80" align="right"><? echo number_format($row['cons_qty'],2); ?></td>
										<td width="80" align="right"><? echo number_format($row['cons_rate'],2); ?></td>
										<td width="100" align="right"><? echo number_format($row['cons_amount'],2); ?></td>
										<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['remarks'] ?></td>
										<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['grey_lot']; ?></td>
										<td width="120"><? echo $row['grey_yarn_rate']; ?></td>
										<td><? echo $row['wo_rate']; ?></td>
									</tr>
									<?
									$tot_receive_qny += $row['cons_qty'];
									$tot_amount_qny += $row['cons_amount'];
									
									$grand_tot_receive_qny += $row['cons_qty'];
									$grand_tot_amount_qny += $row['cons_amount'];

									$currentStockQnty[$row['store_name']]["receiveQnty"] += $row['cons_qty'];
									//$currentStockReject[$row['store_name']] = $receiveRow[csf("cons_reject_qnty")];
									
									if($transType == 1)
									{
										$summary_array[$row['yarn_count']][$row['yarn_composition']][$row['store_name']]['receive'] += $row['cons_qty'];
									}
									
									$summary_array[$row['yarn_count']][$row['yarn_composition']][$row['store_name']]['totalReceive'] += $row['cons_qty'];
									//$summary_array[$row['store_name']]['reject'] += $receiveRow[csf("cons_reject_qnty")];
								}
							}
							?>
							<!--<tr bgcolor="#CCCCCC" style="font-weight: bold ">-->
							<tr class="tbl_bottom" bgcolor="#CCCCCC" style="font-weight: bold ">
								<td colspan="11" align="right"><b>Total</b></td>
								<td align="right"><? echo number_format($tot_receive_qny,2); ?></td>
								<td>&nbsp;</td>
								<td align="right"><? echo number_format($tot_amount_qny,2); ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<?php
						}
						unset($i);
						?>
                        <tr bgcolor="#CCCCCC" style="font-weight: bold ">
                            <td colspan="11" align="right"><b>Grand Total</b></td>
                            <td align="right"><? echo number_format($grand_tot_receive_qny,2); ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><? echo number_format($grand_tot_amount_qny,2); ?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
						</table> 
					</div>
				<!--</fieldset>-->
                <?
			}

			//for issue
			if(!empty($dataArr['Issue']))
			{
				?>
                <!--<fieldset style="width:2050px; float:left;margin-top: 5px;">-->
                    <div>
                        <table style="width:2030px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
                            <thead>
                                <tr>
                                    <td colspan="19" style="font-size:14px; font-weight:bold" align="center">Issue Status</td>
                                </tr>
                                <tr>
                                    <th rowspan="2" width="50">Sl</th>
                                    <th rowspan="2" width="80">Trans Date</th>
                                    <th rowspan="2" width="120">Trans Ref No</th>
                                    <th rowspan="2" width="100">Callan No</th>
                                    <th rowspan="2" width="120">Store Name</th>
                                    <th rowspan="2" width="120">Buyer</th>
                                    <th rowspan="2" width="120">FSO No</th>
                                    <th rowspan="2" width="120">Fabric Booking No</th>
                                    <th rowspan="2" width="120">Style</th>
                                    <th rowspan="2" width="120">Trans type</th>
                                    <th rowspan="2" width="120">Basis</th>
                                    <th rowspan="2" width="120">Issue Purpose</th>
                                    <th rowspan="2" width="120">Requsition/WO No</th>
                                    <th rowspan="2" width="120">Transaction With</th>
                                    <th colspan="3" width="260">Issue</th>
                                    <th rowspan="2" width="120">Returnable Qty</th>
                                    <th rowspan="2">Issue return Qty.</th>
                                </tr>
                                <tr>
                                    <th width="80" align="center">Qnty</th>
                                    <th width="80" align="center">Rate</th>
                                    <th width="100" align="center">Value</th>
                                </tr>
                            </thead>
                        </table>
                        <div style="width:2050px;  max-height:250px" id="scroll_body" align="left"> 
                            <table style="width:2030px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left" >
								<?php
                                $j = 0;
								foreach($dataArr['Issue'][2] as $prodId=>$prodIdArr)
								{
									$transType = 2;
									$tot_issue_qny = 0;
									$tot_issue_amount_qny = 0;
									
									foreach($prodIdArr as $transDate=>$transDateArr)
									{
										foreach($transDateArr as $refNo=>$row)
										{
											$j++;
											if ($j % 2 == 0)
												$bgcolor = "#E9F3FF";
											else
												$bgcolor = "#FFFFFF";
											
											asort($row['requisition_no']);
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('itr_<? echo $j; ?>', '<? echo $bgcolor; ?>')" id="itr_<? echo $j; ?>">
												<td width="50"><? echo $j; ?></td>
												<td width="80" style="word-wrap:break-word; word-break: break-all;"><? echo $transDate; ?></td>
												<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $refNo; ?></td>
												<td width="100" style="word-wrap:break-word; word-break: break-all; mso-number-format:'\@'; text-align: center;"><p><? echo $row['callan_no']; ?></p></td>
												<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['store_name']; ?></td>
												<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['buyer_name']; ?></td>
												
												<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['fso_no']; ?></td>
												<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['fab_booking_no']; ?></td>
												<td width="120" style="word-wrap:break-word; word-break: break-all;mso-number-format:'\@';"><? echo $row['style_ref_no']; ?></td>
												<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $transaction_type[$transType]; ?></td>
												<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['basis'] ?></td>
												<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['purpose']; ?></td>
												<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo implode(', ',$row['requisition_no']); ?></td>
												<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['transfer_with']; ?></td>
												<td width="80" align="right"><? echo number_format($row['cons_qty'],2); ?></td>
												<td width="80" align="right"><? echo number_format($row['cons_rate'],2); ?></td>
												<td width="100" align="right"><? echo number_format($row['cons_amount'],2); ?></td>
												<td width="120" align="right"><? echo number_format($row['returnable_qty'],2); ?></td>
												<td align="right"><? echo number_format($row['issue_return_qty'],2); ?></td>
											</tr>
											<?
											$tot_issue_qny += $row['cons_qty'];
											$tot_issue_amount_qny += $row['cons_amount'];
											
											$grand_tot_issue_qny += $row['cons_qty'];
											$grand_tot_issue_amount_qny += $row['cons_amount'];
											
											$currentStockQnty[$row['store_name']]["issueQnty"] += $row['cons_qty'];
											if($transType == 2)
											{
												$summary_array[$row['yarn_count']][$row['yarn_composition']][$row['store_name']]['issue'] += $row['cons_qty'];
											}
											
											$summary_array[$row['yarn_count']][$row['yarn_composition']][$row['store_name']]['returnable_qty'] += $row['returnable_qty'];
											$summary_array[$row['yarn_count']][$row['yarn_composition']][$row['store_name']]['issue_return_qty'] += $row['issue_return_qty'];
										}
									}
									?>
									<tr bgcolor="#CCCCCC" style="font-weight: bold ">
										<td colspan="14" align="right"><b>Total</b></td>
										<td align="right"><? echo number_format($tot_issue_qny,2); ?></td>
										<td>&nbsp;</td>
										<td align="right"><? echo number_format($tot_issue_amount_qny,2); ?></td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
									<?php
								}
								
								foreach($dataArr['Issue'][3] as $prodId=>$prodIdArr)
								{
									$transType = 3;
									$tot_issue_qny = 0;
									$tot_issue_amount_qny = 0;
									
									foreach($prodIdArr as $transDate=>$transDateArr)
									{
										foreach($transDateArr as $refNo=>$row)
										{
											$j++;
											if ($j % 2 == 0)
												$bgcolor = "#E9F3FF";
											else
												$bgcolor = "#FFFFFF";
											
											asort($row['requisition_no']);
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('itr_<? echo $j; ?>', '<? echo $bgcolor; ?>')" id="itr_<? echo $j; ?>">
												<td width="50"><? echo $j; ?></td>
												<td width="80" style="word-wrap:break-word; word-break: break-all;"><? echo $transDate; ?></td>
												<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $refNo; ?></td>
												<td width="100" style="word-wrap:break-word; word-break: break-all; mso-number-format:'\@'; text-align: center;"><p><? echo $row['callan_no']; ?></p></td>
												<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['store_name']; ?></td>
												<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['buyer_name']; ?></td>
												
												<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['fso_no']; ?></td>
												<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['fab_booking_no']; ?></td>
												<td width="120" style="word-wrap:break-word; word-break: break-all;mso-number-format:'\@';"><? echo $row['style_ref_no']; ?></td>
												<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $transaction_type[$transType]; ?></td>
												<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['basis'] ?></td>
												<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['purpose']; ?></td>
												<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo implode(', ', $row['requisition_no']); ?></td>
												<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $row['transfer_with']; ?></td>
												<td width="80" align="right"><? echo number_format($row['cons_qty'],2); ?></td>
												<td width="80" align="right"><? echo number_format($row['cons_rate'],2); ?></td>
												<td width="100" align="right"><? echo number_format($row['cons_amount'],2); ?></td>
												<td width="120" align="right"><? echo number_format($row['returnable_qty'],2); ?></td>
												<td align="right"><? echo number_format($row['issue_return_qty'],2); ?></td>
											</tr>
											<?
											$tot_issue_qny += $row['cons_qty'];
											$tot_issue_amount_qny += $row['cons_amount'];
											
											$grand_tot_issue_qny += $row['cons_qty'];
											$grand_tot_issue_amount_qny += $row['cons_amount'];
											
											$currentStockQnty[$row['store_name']]["issueQnty"] += $row['cons_qty'];
											if($transType == 2)
											{
												$summary_array[$row['yarn_count']][$row['yarn_composition']][$row['store_name']]['issue'] += $row['cons_qty'];
											}
											
											$summary_array[$row['yarn_count']][$row['yarn_composition']][$row['store_name']]['returnable_qty'] += $row['returnable_qty'];
											$summary_array[$row['yarn_count']][$row['yarn_composition']][$row['store_name']]['issue_return_qty'] += $row['issue_return_qty'];
										}
									}
									?>
									<tr bgcolor="#CCCCCC" style="font-weight: bold ">
										<td colspan="14" align="right"><b>Total</b></td>
										<td align="right"><? echo number_format($tot_issue_qny,2); ?></td>
										<td>&nbsp;</td>
										<td align="right"><? echo number_format($tot_issue_amount_qny,2); ?></td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
									<?php
								}
                                //print_r($summary_array);
                                unset($j);
                                ?>
                                <tr bgcolor="#CCCCCC" style="font-weight: bold ">
                                    <td colspan="14" align="right"><b>Grand Total</b></td>
                                    <td align="right"><? echo number_format($grand_tot_issue_qny,2); ?></td>
                                    <td>&nbsp;</td>
                                    <td align="right"><? echo number_format($grand_tot_issue_amount_qny,2); ?></td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                <!--</fieldset>-->
                <?
            }
			?>
            <br>
            <table style="width:400px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
                <thead>
                    <tr>
                        <th colspan="3" style="font-size:14px; font-weight:bold" align="center">Current Stock Status</th>
                    </tr>
                    <tr>
                        <th width="150">Company</th>
                        <th width="150">Store</th>
                        <th>Total Stock</th>
                    </tr>
                </thead>
                <tbody>
                <?php
				foreach($currentStockQnty as $key=>$val)
				{
					$totalStock = $val['receiveQnty']- $val['issueQnty'];
					
                	?>
					<tr>
                    	<td><? echo $companyArr[$cbo_company_id]; ?></td>
                    	<td><? echo $key; ?></td>
                    	<td align="right"><? echo number_format($totalStock,2); ?></td>
                    </tr>
                    <?php
				}
                ?>
                </tbody>
            </table>
            <br>
            <table style="width:1140px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
                <thead>
                    <tr>
                        <th colspan="11" style="font-size:14px; font-weight:bold" align="center">Stock Summary</th>
                    </tr>
                    <tr>
                        <th width="80">Count</th>
                        <th width="120">Composition</th>
                        <th width="120">Company</th>
                        <th width="120">Store</th>
                        <th width="100">Receive Qnty</th>
                        <th width="100">Issue Qny</th>
                        <th width="100">Returnable Qty</th>
                        <th width="100">Issue return Qty.</th>
                        <th width="100">Return Balance</th>
                        <th width="100">Stock In Hand</th>
                        <th width="100">Rejected</th>
                    </tr>
                </thead>
                <tbody>
                <?php
				foreach($summary_array as $count=>$countArr)
				{
					foreach($countArr as $compStr=>$compStrArr)
					{
						foreach($compStrArr as $store=>$val)
						{
							$return_balance = $val['returnable_qty']-$val['issue_return_qty'];
							$stockInHand = $val['totalReceive']-$val['issue'];
							?>
							<tr>
								<td><? echo $count; ?></td>
								<td><? echo $compStr; ?></td>
								<td><? echo $companyArr[$cbo_company_id]; ?></td>
								<td><? echo $store; ?></td>
								<td align="right"><? echo number_format($val['receive'],2); ?></td>
								<td align="right"><? echo number_format($val['issue'],2); ?></td>
								<td align="right"><? echo number_format($val['returnable_qty'],2); ?></td>
								<td align="right"><? echo number_format($val['issue_return_qty'],2); ?></td>
								<td align="right"><? echo number_format($return_balance,2); ?></td>
								<td align="right"><? echo number_format($stockInHand,2); ?></td>
								<td align="right"><? //echo number_format($val['receive'],2); ?></td>
							</tr>
							<?php
						}
					}
				}
				?>
                </tbody>
            </table>
            <?php
        }
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
    echo $html."####".$filename;
    exit();
}
?>