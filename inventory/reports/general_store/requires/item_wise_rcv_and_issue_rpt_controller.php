<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];


if ($action=="load_drop_down_store")
{
	$data=explode("**",$data);
	if($data[1]==2) $disable=1; else $disable=0;
	$userCredential = sql_select("SELECT store_location_id FROM user_passwd where id=$user_id");
	$store_cond = ($userCredential[0][csf("store_location_id")]) ? " and a.id in (".$userCredential[0][csf("store_location_id")].")" : "" ;
	echo create_drop_down( "cbo_store_name", 120, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where  a.status_active=1 and a.is_deleted=0 and a.company_id='$data[0]' $store_cond group by a.id,a.store_name","id,store_name", 1, "--Select Store--", 0, "",$disable );
	exit();
}

if ($action == "item_account_popup") 
{
	echo load_html_head_contents("Item Details Info", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
    <script>
        var selected_id = new Array, selected_name = new Array();
        selected_attach_id = new Array();

        function js_set_value(id) {
            $('#txt_selected_id').val(id);
			parent.emailwindow.hide();
        }
        
    </script>
    </head>
    <body>
    <div align="center">
        <form name="item_detailsfrm" id="item_detailsfrm">
            <fieldset style="width:500px;">
                <table width="490" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
                       class="rpt_table" id="tbl_list_search">
                    <thead>
                    <th>Product ID</th>
                    <th>Item Description</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;"
                               onClick="reset_form('item_detailsfrm','search_div','','','','');"></th>
                    </thead>
                    <tbody>
                    <tr>
						<td align="center"><input type="text" style="width:130px" class="text_boxes" name="txt_prod_id" id="txt_prod_id"/></td>
                        <td align="center"><input type="text" style="width:130px" class="text_boxes" name="txt_item_description" id="txt_item_description"/></td>
                        <td align="center"><input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('txt_prod_id').value+'**'+document.getElementById('txt_item_description').value+'**'+<? echo $company; ?> , 'item_account_popup_list_view', 'search_div', 'item_wise_rcv_and_issue_rpt_controller', 'setFilterGrid(\'list_view\',-1,\'tableFilters\')');" style="width:100px;"/>
                            <input type="hidden" name="txt_selected_id" id="txt_selected_id" value=""/></td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>
            <div style="margin-top:15px" id="search_div"></div>
        </form>
    </div>
    </body>
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    
    </html>
	<?
	exit();
}

if ($action == "item_account_popup_list_view") 
{
	echo load_html_head_contents("Item Creation popup", "../../../../", 1, 1,'','1','');
	list($product_id,$item_description,$company) = explode('**', $data);
	
	$search_cond = "";
	if ($item_description != "") $search_cond .= " and a.item_description LIKE '%$item_description%' ";
	if ($product_id != "") $search_cond .= " and a.id =$product_id ";
	$item_category_cond=implode(",",array_diff(array_flip($general_item_category), array("4")));

	$sql = "SELECT a.id, a.item_category_id, a.item_description from product_details_master a where a.company_id=$company and a.status_active=1 and a.is_deleted=0 and a.item_category_id in ($item_category_cond) $search_cond order by a.id desc";

	$arr=array(0=>$general_item_category);
	echo  create_list_view("list_view", "Item Description,Product ID", "200,100","350","260",0, $sql , "js_set_value", "id,item_description", "", 1, "item_category_id,0,0", $arr , "item_description,id", "",'setFilterGrid("list_view",-1);','0,0') ;
	?>
	<script>
		<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</script>
	<?
	exit(); 
}

if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_prod_id=str_replace("'","",$txt_prod_id);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	$cbo_yes_no=str_replace("'","",$cbo_yes_no);
    $report_type=str_replace("'","",$report_type);
	
	$str_cond="";
	if ($cbo_company_name) $str_cond =" and a.company_id='$cbo_company_name'";
	if ($txt_prod_id) $str_cond .=" and a.prod_id=$txt_prod_id";
	if($cbo_yes_no==1)
	{
		if ($cbo_store_name) $str_cond .=" and a.store_id='$cbo_store_name'";
	}
	if($db_type==0) $select_from_date=change_date_format($from_date,'yyyy-mm-dd');
	if($db_type==2) $select_from_date=change_date_format($from_date,'','',1);

	if($db_type==0) $select_to_date=change_date_format($to_date,'yyyy-mm-dd');
	if($db_type==2) $select_to_date=change_date_format($to_date,'','',1);

	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$itemgroupArr = return_library_array("select id,item_name from lib_item_group where status_active=1 and is_deleted=0","id","item_name");

    if($report_type == 1)
	{		
		$sql="SELECT a.transaction_date as TRANSACTION_DATE, a.prod_id as PROD_ID, b.item_category_id as ITEM_CATEGORY_ID, b.item_group_id as ITEM_GROUP_ID, b.item_description as ITEM_DESCRIPTION, b.unit_of_measure as UNIT_OF_MEASURE, 
		sum(case when a.transaction_type=1 then a.cons_quantity else 0 end) as PURCHASE,
		sum(case when a.transaction_type=4  then a.cons_quantity else 0 end) as ISSUE_RETURN,
		sum(case when a.transaction_type=5  then a.cons_quantity else 0 end) as ITEM_TRANSFER_RECEIVE
		from inv_transaction a, product_details_master b
		where a.prod_id=b.id and a.transaction_type in (1,4,5) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $str_cond and a.transaction_date between '$select_from_date' and '$select_to_date'
		group by a.transaction_date, a.prod_id, b.item_category_id, b.item_group_id, b.item_description, b.unit_of_measure
		order by a.transaction_date";
		//echo  $sql;die;
		$result = sql_select($sql);
		$table_width="940";
		$i=1;
		ob_start();
		?>
		<div>
			<table style="width:<? echo $table_width+18; ?>px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_header_1" rules="all" >
				<thead>
					<tr style="border:none;">
						<td colspan="10" align="center" style="border:none; font-size:14px;">
							<b><? echo $companyArr[$cbo_company_name]; ?></b>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="10" align="center" style="border:none;font-size:12px; font-weight:bold">
							<b>Item Wise Receive Summery</b>
						</td>
					</tr>
					<tr>
						<th colspan="5">Description</th>
						<th colspan="5">Receive</th>
					</tr>
					<tr>
						<th width="80">Date</th>
						<th width="80">Prod. ID</th>
						<th width="100">Item Group</th>
						<th width="200">Item Description</th>
						<th width="80">UOM</th>
						<th width="80">Purchase</th>
						<th width="80">Issue Return</th>
						<th width="80">Transfer In</th>
						<th width="80">Loan Received</th>
						<th >Total Received</th>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $table_width+18; ?>px; max-height:350px; overflow-y:scroll;" id="scroll_body" >
				<table style="width:<? echo $table_width; ?>px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left">
					<?
					$total_purchase=$total_issue_return=$total_item_transfer_receive=$total_rcv=0;
					foreach($result as $row)
					{
						$totalReceive=0;
						if($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
						$totalReceive=$row["PURCHASE"]+$row[("ISSUE_RETURN")]+$row[("ITEM_TRANSFER_RECEIVE")];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="80" align="center"><p><? echo change_date_format($row["TRANSACTION_DATE"]); ?></p></td>
							<td width="80" align="center"><p><? echo $row["PROD_ID"]; ?></p></td>
							<td width="100"><p><? echo $itemgroupArr[$row["ITEM_GROUP_ID"]]; ?></p></td>
							<td width="200"><p><? echo $row["ITEM_DESCRIPTION"]; ?></p></td>
							<td width="80" align="center"><p><? echo $unit_of_measurement[$row["UNIT_OF_MEASURE"]]; ?></p></td>
							<td width="80" align="right">
								<a href='##' onclick="fnc_prod_details('<? echo $row['PROD_ID'];?>','<? echo $row['TRANSACTION_DATE']; ?>','<? echo $cbo_yes_no; ?>','<? echo $cbo_store_name; ?>','Receive Details','rcv_popup_details')"><? echo number_format($row["PURCHASE"],2); ?></a>
							</td>
							<td width="80" align="right"><? echo number_format($row["ISSUE_RETURN"],2); ?></td>
							<td width="80" align="right"><? echo number_format($row["ITEM_TRANSFER_RECEIVE"],2); ?></td>
							<td width="80" align="right"></td>
							<td align="right"><? echo number_format($totalReceive,2); ?></td>
						</tr>
						<?
 						$total_purchase+=$row["PURCHASE"];
 						$total_issue_return+=$row["ISSUE_RETURN"];
						$total_item_transfer_receive+=$row["ITEM_TRANSFER_RECEIVE"];
 						$total_rcv+=$totalReceive;
						$i++;
					}
					?>
					<tr bgcolor="#CCCCCC" style="font-weight:bold">
						<td colspan="5" align="right"><strong>Total:</strong></td>
                        <td align="right"><? echo number_format($total_purchase,2); ?></td>
						<td align="right"><? echo number_format($total_issue_return,2); ?></td>
						<td align="right"><? echo number_format($total_item_transfer_receive,2); ?></td>
						<td align="right"></td>
						<td align="right"><? echo number_format($total_rcv,2); ?></td>
					</tr>
				</table>
			</div>
		</div>
        <?
    }

    if($report_type == 2)
	{		
		$sql="SELECT a.transaction_date as TRANSACTION_DATE, a.prod_id as PROD_ID, b.item_category_id as ITEM_CATEGORY_ID, b.item_group_id as ITEM_GROUP_ID, b.item_description as ITEM_DESCRIPTION, b.unit_of_measure as UNIT_OF_MEASURE, 
		sum(case when a.transaction_type=2 then a.cons_quantity else 0 end) as ISSUE,
		sum(case when a.transaction_type=3  then a.cons_quantity else 0 end) as RECEIVE_RETURN,
		sum(case when a.transaction_type=6  then a.cons_quantity else 0 end) as ITEM_TRANSFER_ISSUE
		from inv_transaction a, product_details_master b
		where a.prod_id=b.id and a.transaction_type in (2,3,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $str_cond and a.transaction_date between '$select_from_date' and '$select_to_date'
		group by a.transaction_date, a.prod_id, b.item_category_id, b.item_group_id, b.item_description, b.unit_of_measure
		order by a.transaction_date";
		// echo  $sql;die;
		$result = sql_select($sql);
		$all_data_array=array();
		foreach($result as $row)
		{
			$all_data_array[$row['TRANSACTION_DATE']]['TRANSACTION_DATE']=$row['TRANSACTION_DATE'];
			$all_data_array[$row['TRANSACTION_DATE']]['PROD_ID']=$row['PROD_ID'];
			$all_data_array[$row['TRANSACTION_DATE']]['ITEM_CATEGORY_ID']=$row['ITEM_CATEGORY_ID'];
			$all_data_array[$row['TRANSACTION_DATE']]['ITEM_GROUP_ID']=$row['ITEM_GROUP_ID'];
			$all_data_array[$row['TRANSACTION_DATE']]['ITEM_DESCRIPTION']=$row['ITEM_DESCRIPTION'];
			$all_data_array[$row['TRANSACTION_DATE']]['UNIT_OF_MEASURE']=$row['UNIT_OF_MEASURE'];
			$all_data_array[$row['TRANSACTION_DATE']]['AVG_RATE_PER_UNIT']=$row['AVG_RATE_PER_UNIT'];
			$all_data_array[$row['TRANSACTION_DATE']]['PURCHASE']=$row['PURCHASE'];
			$all_data_array[$row['TRANSACTION_DATE']]['RECEIVE_RETURN']=$row['RECEIVE_RETURN'];
			$all_data_array[$row['TRANSACTION_DATE']]['ISSUE']=$row['ISSUE'];
			$all_data_array[$row['TRANSACTION_DATE']]['ISSUE_RETURN']=$row['ISSUE_RETURN'];
			$all_data_array[$row['TRANSACTION_DATE']]['ITEM_TRANSFER_ISSUE']=$row['ITEM_TRANSFER_ISSUE'];
			$all_data_array[$row['TRANSACTION_DATE']]['TOTAL_RCV_AMT_VALUE']=$row['TOTAL_RCV_AMT_VALUE'];
			$all_data_array[$row['TRANSACTION_DATE']]['TOTAL_ISS_AMT_VALUE']=$row['TOTAL_ISS_AMT_VALUE'];
		}
		$table_width="940";
		$i=1;
		ob_start();
		?>
		<div>
			<table style="width:<? echo $table_width+18; ?>px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_header_1" rules="all" >
				<thead>
					<tr style="border:none;">
						<td colspan="10" align="center" style="border:none; font-size:14px;">
							<b><? echo $companyArr[$cbo_company_name]; ?></b>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="10" align="center" style="border:none;font-size:12px; font-weight:bold">
							<b>Item Wise Issue Summery</b>
						</td>
					</tr>
					<tr>
						<th colspan="5">Description</th>
						<th colspan="5">Issue</th>
					</tr>
					<tr>
						<th width="80">Date</th>
						<th width="80">Prod. ID</th>
						<th width="100">Item Group</th>
						<th width="200">Item Description</th>
						<th width="80">UOM</th>
						<th width="80">Issue</th>
						<th width="80">As Loan</th>
						<th width="80">Receive Return</th>
						<th width="80">Transfer Out</th>
						<th >Total Issue</th>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $table_width+18; ?>px; max-height:350px; overflow-y:scroll;" id="scroll_body" >
				<table style="width:<? echo $table_width; ?>px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
					<?
					$total_issue=$total_receive_return=$total_item_transfer_issue=$total_issue=0;
					foreach($result as $row)
					{
						$totalIssue=0;
						if($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
						$totalIssue=$row["ISSUE"]+$row[("RECEIVE_RETURN")]+$row[("ITEM_TRANSFER_ISSUE")];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="80" align="center"><p><? echo change_date_format($row["TRANSACTION_DATE"]); ?></p></td>
							<td width="80" align="center"><p><? echo $row["PROD_ID"]; ?></p></td>
							<td width="100"><p><? echo $itemgroupArr[$row["ITEM_GROUP_ID"]]; ?></p></td>
							<td width="200"><p><? echo $row["ITEM_DESCRIPTION"]; ?></p></td>
							<td width="80" align="center"><p><? echo $unit_of_measurement[$row["UNIT_OF_MEASURE"]]; ?></p></td>
							<td width="80" align="right"><? echo number_format($row["ISSUE"],2);?></td>
							<td width="80" align="right"></td>
							<td width="80" align="right"><? echo number_format($row["RECEIVE_RETURN"],2); ?></td>
							<td width="80" align="right"><? echo number_format($row["ITEM_TRANSFER_ISSUE"],2); ?></td>
							<td align="right"><? echo number_format($totalIssue,2); ?></td>
						</tr>
						<?
 						$total_issue+=$row["ISSUE"];
 						$total_receive_return+=$row["RECEIVE_RETURN"];
						$total_item_transfer_issue+=$row["ITEM_TRANSFER_ISSUE"];
 						$total_issue+=$totalIssue;
						$i++;
					}
					?>
					<tr bgcolor="#CCCCCC" style="font-weight:bold">
						<td colspan="5" align="right"><strong>Total:</strong></td>
                        <td align="right"><? echo number_format($total_issue,2); ?></td>
						<td align="right"></td>
						<td align="right"><? echo number_format($total_receive_return,2); ?></td>
						<td align="right"><? echo number_format($total_item_transfer_issue,2); ?></td>
						<td align="right"><? echo number_format($total_issue,2); ?></td>
					</tr>
				</table>
			</div>
		</div>
        <?
    }

	if($report_type == 3)
	{		
		$opening_sql="SELECT a.transaction_type as TRANSACTION_TYPE, a.prod_id as PROD_ID,
		sum(case when a.transaction_date<'$select_from_date' and a.transaction_type in (1,4,5)  then a.cons_quantity else 0 end) as OPENING_TOTAL_RECEIVE,
		sum(case when a.transaction_date<'$select_from_date' and a.transaction_type in (1,4,5)  then a.cons_amount else 0 end) as OPENING_TOTAL_RECEIVE_AMT,
		sum(case when a.transaction_date<'$select_from_date' and a.transaction_type in (2,3,6)  then a.cons_quantity else 0 end) as OPENING_TOTAL_ISSUE,
		sum(case when a.transaction_date<'$select_from_date' and a.transaction_type in (2,3,6)  then a.cons_amount else 0 end) as OPENING_TOTAL_ISSUE_AMT
		from inv_transaction a
		where a.transaction_type in (1,2,3,4,5,6) and a.status_active=1 and a.is_deleted=0 $str_cond group by a.transaction_type, a.prod_id order by a.transaction_type";
		// echo $opening_sql;die;
		$opening_result = sql_select($opening_sql);
		$opening_data=array();
		foreach($opening_result as $row)
		{
			$opening_data[$row["PROD_ID"]]["qnty"]+=$row["OPENING_TOTAL_RECEIVE"]-$row["OPENING_TOTAL_ISSUE"];
			$opening_data[$row["PROD_ID"]]["amount"]+=$row["OPENING_TOTAL_RECEIVE_AMT"]-$row["OPENING_TOTAL_ISSUE_AMT"];
		}
		// var_dump($opening_data);
		$sql="SELECT a.transaction_date as TRANSACTION_DATE, a.prod_id as PROD_ID, b.item_category_id as ITEM_CATEGORY_ID, b.item_group_id as ITEM_GROUP_ID, b.item_description as ITEM_DESCRIPTION, b.unit_of_measure as UNIT_OF_MEASURE,b.avg_rate_per_unit as AVG_RATE_PER_UNIT,
		sum(case when a.transaction_type=1 and a.transaction_date between '$select_from_date' and '$select_to_date' then a.cons_quantity else 0 end) as PURCHASE,
		sum(case when a.transaction_type=3 and a.transaction_date between '$select_from_date' and '$select_to_date' then a.cons_quantity else 0 end) as RECEIVE_RETURN,
		sum(case when a.transaction_type=5 and a.transaction_date between '$select_from_date' and '$select_to_date' then a.cons_quantity else 0 end) as ITEM_TRANSFER_RECEIVE,
		sum(case when a.transaction_type=2 and a.transaction_date between '$select_from_date' and '$select_to_date' then a.cons_quantity else 0 end) as ISSUE,
		sum(case when a.transaction_type=4 and a.transaction_date between '$select_from_date' and '$select_to_date' then a.cons_quantity else 0 end) as ISSUE_RETURN,
		sum(case when a.transaction_type=6 and a.transaction_date between '$select_from_date' and '$select_to_date' then a.cons_quantity else 0 end) as ITEM_TRANSFER_ISSUE,
		sum(case when a.transaction_type in (1,4,5) and a.transaction_date  between '$select_from_date' and '$select_to_date' then a.cons_amount else 0 end) as TOTAL_RCV_AMT_VALUE,
		sum(case when a.transaction_type in (2,3,6) and a.transaction_date  between '$select_from_date' and '$select_to_date' then a.cons_amount else 0 end) as TOTAL_ISS_AMT_VALUE
		from inv_transaction a, product_details_master b
		where a.prod_id=b.id and a.transaction_type in (1,2,3,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $str_cond and a.transaction_date between '$select_from_date' and '$select_to_date'
		group by a.transaction_date, a.prod_id, b.item_category_id, b.item_group_id, b.item_description, b.unit_of_measure,b.avg_rate_per_unit
		order by a.transaction_date";
		//echo  $sql;die;
		$result = sql_select($sql);
		$table_width="1920";
		$i=1;
		ob_start();
		?>
		<div>
			<table style="width:<? echo $table_width+18; ?>px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_header_1" rules="all" align="left" >
				<thead>
					<tr style="border:none;">
						<td colspan="22" align="center" style="border:none; font-size:14px;">
							<b><? echo $companyArr[$cbo_company_name]; ?></b>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="22" align="center" style="border:none;font-size:12px; font-weight:bold">
							<b>Item wise Summery Report</b>
						</td>
					</tr>
					<tr>
						<th colspan="5">Description</th>
						<th rowspan="2" width="80">Opening Stock</th>
						<th rowspan="2" width="80">Opening Value</th>
						<th colspan="6">Receive</th>
						<th colspan="6">Issue</th>
						<th rowspan="2" width="80">Closing Stock</th>
						<th rowspan="2" width="80">Avg. Rate</th>
						<th rowspan="2">Stock Value</th>
					</tr>
					<tr>
						<th width="80">Date</th>
						<th width="80">Prod. ID</th>
						<!--<th width="120">Item Category</th>-->
						<th width="100">Item Group</th>
						<th width="200">Item Description</th>
						<th width="80">UOM</th>
						<th width="80">Purchase</th>
						<th width="80">Issue Return</th>
						<th width="80">Transfer In</th>
						<th width="80">Loan Received</th>
						<th width="80">Total Received</th>
						<th width="80">Total RCV Value</th>
						<th width="80">Issue</th>
						<th width="80">As Loan</th>
						<th width="80">Receive Return</th>
						<th width="80">Transfer Out</th>
						<th width="80">Total Issue</th>
						<th width="80">Total Issue Value</th>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $table_width+18; ?>px; max-height:350px; overflow-y:scroll;" id="scroll_body" >
				<table style="width:<? echo $table_width; ?>px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left">
					<?
					foreach($result as $row)
					{
						if($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
						$totalReceive=$row["PURCHASE"]+$row[("ISSUE_RETURN")]+$row[("ITEM_TRANSFER_RECEIVE")];
						$totalIssue=$row["ISSUE"]+$row[("RECEIVE_RETURN")]+$row[("ITEM_TRANSFER_ISSUE")];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="80" align="center"><p><? echo change_date_format($row["TRANSACTION_DATE"]); ?></p></td>
							<td width="80" align="center"><p><? echo $row["PROD_ID"]; ?></p></td>
							<td width="100"><p><? echo $itemgroupArr[$row["ITEM_GROUP_ID"]]; ?></p></td>
							<td width="200"><p><? echo $row["ITEM_DESCRIPTION"]; ?></p></td>
							<td width="80" align="center"><p><? echo $unit_of_measurement[$row["UNIT_OF_MEASURE"]]; ?></p></td>
							<td width="80" align="right">
								<? 
									if($i==1)
									{
										echo number_format($opening_data[$row["PROD_ID"]]["qnty"],2);
										$closing_stock=$opening_data[$row["PROD_ID"]]["qnty"]+$totalReceive-$totalIssue;
									}
									else
									{
										echo number_format($closing_stock,2);
										$closing_stock+=$totalReceive-$totalIssue;
									}
								?>
							</td>
							<td width="80" align="right">
								<? 
									if($i==1)
									{
										echo number_format($opening_data[$row["PROD_ID"]]["amount"],2);
										$stock_value=$opening_data[$row["PROD_ID"]]["amount"]+$row["TOTAL_RCV_AMT_VALUE"]-$row["TOTAL_ISS_AMT_VALUE"];
									}
									else
									{
										echo number_format($stock_value,2);
										$stock_value+=$row["TOTAL_RCV_AMT_VALUE"]-$row["TOTAL_ISS_AMT_VALUE"];
									}
								?>
							</td>
							<td width="80" align="right">
								<a href='##' onclick="fnc_prod_details('<? echo $row['PROD_ID'];?>','<? echo $row['TRANSACTION_DATE']; ?>','<? echo $cbo_yes_no; ?>','<? echo $cbo_store_name; ?>','Receive Details','rcv_popup_details')"><? echo number_format($row["PURCHASE"],2); ?></a>
							</td>
							<td width="80" align="right"><? echo number_format($row["ISSUE_RETURN"],2); ?></td>
							<td width="80" align="right"><? echo number_format($row["ITEM_TRANSFER_RECEIVE"],2); ?></td>
							<td width="80" align="right"></td>
							<td width="80" align="right"><? echo number_format($totalReceive,2); ?></td>
							<td width="80" align="right"><? echo number_format($row["TOTAL_RCV_AMT_VALUE"],2); ?></td>
							<td width="80" align="right"><? echo number_format($row["ISSUE"],2);?></td>
							<td width="80" align="right"></td>
							<td width="80" align="right"><? echo number_format($row["RECEIVE_RETURN"],2); ?></td>
							<td width="80" align="right"><? echo number_format($row["ITEM_TRANSFER_ISSUE"],2); ?></td>
							<td width="80" align="right"><? echo number_format($totalIssue,2); ?></td>
							<td width="80" align="right"><? echo number_format($row["TOTAL_ISS_AMT_VALUE"],2); ?></td>
							<td width="80" align="right"><? echo number_format($closing_stock,2); ?></td>
							<td width="80" align="right"><? echo number_format($row["AVG_RATE_PER_UNIT"],2); ?></td>
							<td align="right"><? echo number_format($stock_value,2); ?></td>
						</tr>
						<?
						$total_purchase+=$row["PURCHASE"];
						$total_issue_return+=$row["ISSUE_RETURN"];
						$total_item_transfer_receive+=$row["ITEM_TRANSFER_RECEIVE"];
						$total_rcv_qnty+=$totalReceive;
						$total_rcv_amount+=$row["TOTAL_RCV_AMT_VALUE"];
 						$total_issue+=$row["ISSUE"];
 						$total_receive_return+=$row["RECEIVE_RETURN"];
						$total_item_transfer_issue+=$row["ITEM_TRANSFER_ISSUE"];
 						$total_issue_qnty+=$totalIssue;
 						$total_issue_amount+=$row["TOTAL_ISS_AMT_VALUE"];
						$i++;
					}
					?>
					<tr bgcolor="#CCCCCC" style="font-weight:bold">
						<td colspan="7" align="right"><strong>Total:</strong></td>
						<td align="right"><? echo number_format($total_purchase,2); ?></td>
						<td align="right"><? echo number_format($total_issue_return,2); ?></td>
						<td align="right"><? echo number_format($total_item_transfer_receive,2); ?></td>
						<td align="right"></td>
						<td align="right"><? echo number_format($total_rcv_qnty,2); ?></td>
						<td align="right"><? echo number_format($total_rcv_amount,2); ?></td>
                        <td align="right"><? echo number_format($total_issue,2); ?></td>
						<td align="right"></td>
						<td align="right"><? echo number_format($total_receive_return,2); ?></td>
						<td align="right"><? echo number_format($total_item_transfer_issue,2); ?></td>
						<td align="right"><? echo number_format($total_issue_qnty,2); ?></td>
						<td align="right"><? echo number_format($total_issue_amount,2); ?></td>
						<td colspan="3" ></td>
					</tr>
				</table>
			</div>
		</div>
		<?
	}

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
    echo "$html**$filename**$report_type";
    exit();
}

if ($action=="rcv_popup_details") 
{
	echo load_html_head_contents("Receive Details", "../../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);
	$prod_id = str_replace("'","",$prod_id);
	$date_transaction = str_replace("'","",$date_transaction);
	$store_wise = str_replace("'","",$store_wise);
	$store_id = str_replace("'","",$store_id);
	if($db_type==0) $date_transaction=change_date_format($date_transaction,'yyyy-mm-dd');
	if($db_type==2) $date_transaction=change_date_format($date_transaction,'','',1);

	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	?>
	<fieldset style="width:650px">
		<legend>Item Details</legend>
		<table width="620" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
			<thead>
				<th width="80">Receive date</th>
				<th width="100">Req. No</th>
				<th width="120">Supllier</th>
				<th width="80">Rev. qty</th>
                <th width="80">UOM</th>
				<th width="80">Rate</th>
                <th>Value</th>
			</thead>
		</table>
		<div style="width:648px; overflow-y:scroll; max-height:300px" id="scroll_body">
			<table width="620" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
				<?
				$i = 1;
				if($store_wise==1){if($store_id>0) {$store_cond=" and a.store_id=$store_id";}}

				$sql = "SELECT a.transaction_date as TRANSACTION_DATE, b.booking_no as BOOKING_NO, b.supplier_id as SUPPLIER_ID,
				sum(a.cons_quantity) as RCV_QNTY, a.cons_rate as CONS_RATE, sum(a.cons_amount) as RCV_AMOUNT, a.cons_uom as CONS_UOM
				from inv_transaction a, inv_receive_master b
				where a.mst_id=b.id and a.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.prod_id=$prod_id and a.transaction_date = '$date_transaction' $store_cond  
				group by b.booking_no, a.transaction_date, b.supplier_id, a.cons_rate, a.cons_uom";
				// echo $sql;die;
				$result = sql_select($sql);
				foreach ($result as $row) {
					if($i % 2 == 0){ $bgcolor = "#E9F3FF"; }else{ $bgcolor = "#FFFFFF"; }				
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="80" align="center"><p><? echo change_date_format($row["TRANSACTION_DATE"]); ?>&nbsp;</p></td>
						<td width="100"><p><? echo $row["BOOKING_NO"]; ?>&nbsp;</p></td>
                        <td width="120"><p><? echo $supplier_arr[$row["SUPPLIER_ID"]]; ?>&nbsp;</p></td>
                        <td width="80" align="right"><p><? echo number_format($row["RCV_QNTY"],2); ?>&nbsp;</p></td>
                        <td width="80" align="center"><p><? echo $unit_of_measurement[$row["CONS_UOM"]]; ?>&nbsp;</p></td>
						<td width="80" align="right"><? echo number_format($row["CONS_RATE"],2); ?>&nbsp;</td>
						<td align="right"><? echo number_format($row["RCV_AMOUNT"],2); ?>&nbsp;</td>
					</tr>
					<?
					$i++;
				}
				?>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}
?>
