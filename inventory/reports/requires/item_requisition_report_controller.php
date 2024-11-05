<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_location"){
	echo create_drop_down( "cbo_location_name", 120, "SELECT id,location_name from lib_location where company_id=$data and status_active =1 and is_deleted=0 group by id,location_name order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/item_requisition_report_controller', $('#cbo_company_name').val()+'**'+$('#cbo_location_name').val()+'**'+this.value, 'load_drop_down_store', 'store_td' );" );
}

if ($action=="load_drop_down_store"){
	$data=explode("**",$data);
    //print_r($data[2]);
    if($data[1]){
        echo create_drop_down( "cbo_store_name", 120, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id='$data[0]' and a.location_id='$data[1]' group by a.id,a.store_name","id,store_name", 1, "--Select Store--", 0, "");
    }
	exit();
}


if($action=="generate_report"){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
    $cbo_company_name=str_replace("'", '', $cbo_company_name);
    $cbo_location_name=str_replace("'", '', $cbo_location_name);
    $cbo_item_category_id=str_replace("'", '', $cbo_item_category_id);
    $cbo_store_name=str_replace("'", '', $cbo_store_name);
    $txt_requisition_no=str_replace("'", '', $txt_requisition_no);
    $txt_date_from=str_replace("'", '', $txt_date_from);
    $txt_date_to=str_replace("'", '', $txt_date_to);
	$sql_cond="";
	if ($cbo_company_name!=0) $sql_cond=" and a.company_id=$cbo_company_name";
	if ($cbo_location_name!=0) $sql_cond.=" and a.location_id=$cbo_location_name";
	if ($cbo_item_category_id!=0) $sql_cond .=" and c.item_category_id =$cbo_item_category_id";
	if ($cbo_store_name!=0) $sql_cond .=" and a.store_id=$cbo_store_name";
	if ($txt_requisition_no!='') $sql_cond .=" and a.itemissue_req_prefix_num=$txt_requisition_no";
	if($db_type==0){
		$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
		$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	else if($db_type==2){
		$txt_date_from=change_date_format($txt_date_from,'','',-1);
		$txt_date_to=change_date_format($txt_date_to,'','',-1);
	}
    if($txt_date_from !="" && $txt_date_to !=""){
        $sql_cond.=" and a.indent_date between '$txt_date_from' and '$txt_date_to'";
    }

	$companyArr = return_library_array("select id,company_name from lib_company","id","company_name");
	$divisionArr = return_library_array("select id,division_name from lib_division where status_active=1 and is_deleted=0","id","division_name");
	$storeArr = return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0","id","store_name");
	$departmentArr = return_library_array("select id,department_name from lib_department where status_active=1 and is_deleted=0","id","department_name");
	$sectionArr = return_library_array("select id,section_name from lib_section where status_active=1 and is_deleted=0","id","section_name");
	$itemgroupArr = return_library_array("select id,item_name from lib_item_group where status_active=1 and is_deleted=0","id","item_name");
    $user_array=return_library_array("select id,user_name from user_passwd","id","user_name");
    $approved_arr=array(0=>"No Approved",1=>"Approved",2=>"No Approved",3=>"Partial Approved");
    $sql_company=sql_select("SELECT id as ID, company_name as COMPANY_NAME, company_short_name as COMPANY_SHORT_NAME, plot_no as PLOT_NO, level_no as LEVEL_NO, road_no as ROAD_NO, block_no as BLOCK_NO, city as CITY, zip_code as ZIP_CODE from lib_company where status_active=1 and is_deleted=0 and id=$cbo_company_name");
    $com_name=$sql_company[0]["COMPANY_NAME"];
    $company_short_name=$sql_company[0]["COMPANY_SHORT_NAME"];
    $plot_no=$sql_company[0]["PLOT_NO"];
    $level_no=$sql_company[0]["LEVEL_NO"];
    $road_no=$sql_company[0]["ROAD_NO"];
    $block_no=$sql_company[0]["BLOCK_NO"];
    $city=$sql_company[0]["CITY"];
    $zip_code=$sql_company[0]["ZIP_CODE"];
	
	//echo $report_type;die;
    if($report_type == 1){ 		
		$sql="SELECT a.id as INV_ISSUE_MASTER_ID, a.itemissue_req_prefix_num as ITEMISSUE_REQ_PREFIX_NUM, a.indent_date as INDENT_DATE, a.store_id as STORE_ID, a.division_id as DIVISION_ID, a.department_id as DEPARTMENT_ID, a.section_id as SECTION_ID, a.required_date as REQUIRED_DATE, b.item_group as ITEM_GROUP, b.item_description as ITEM_DESCRIPTION, b.req_for as REQ_FOR, b.req_qty as REQ_QTY, b.remarks as REMARKS, c.id as PROD_ID, c.item_category_id as ITEM_CATEGORY_ID, c.unit_of_measure as UOM, a.is_approved as IS_APPROVED, a.inserted_by as INSERTED_BY
		from product_details_master c, inv_itemissue_requisition_dtls b, inv_item_issue_requisition_mst a
		where a.id=b.mst_id and b.product_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond and c.item_category_id in (".implode(',',array_keys($general_item_category)).")";
        // echo $sql;
        $data_result = sql_select($sql);

        $inv_issue_master_id='';$prod_id='';
        foreach($data_result as $row){
            $inv_issue_master_id.=$row['INV_ISSUE_MASTER_ID'].',';
            $prod_id.=$row['PROD_ID'].',';
        }
        $inv_issue_master_id=implode(",",array_unique(explode(",",chop($inv_issue_master_id,','))));
        $prod_id=implode(",",array_unique(explode(",",chop($prod_id,','))));

		$issue_sql="SELECT a.req_id as REQ_ID, b.item_category as ITEM_CATEGORY, b.prod_id as PROD_ID, sum(b.cons_quantity) as CONS_QUANTITY
		from inv_issue_master a, inv_transaction b
		where a.req_id in ($inv_issue_master_id) and a.id=b.mst_id and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.req_id, b.item_category, b.prod_id";
        // echo  $issue_sql;die;
        $issue_result = sql_select($issue_sql);
        $issue_arr=array();
        foreach($issue_result as $row){
            $issue_arr[$row['REQ_ID']][$row['ITEM_CATEGORY']][$row['PROD_ID']]=$row['CONS_QUANTITY'];
        }

        $stock_qnty_sql="SELECT prod_id as PROD_ID, sum(case when transaction_type in(1,4,5) then cons_quantity else 0 end) - sum(case when transaction_type in(2,3,6) then cons_quantity else 0 end) as BALANCE_STOCK from inv_transaction where prod_id in($prod_id) and status_active=1 and is_deleted=0  group by prod_id";
        // echo  $stock_qnty_sql;die;
        $stock_qnty_result = sql_select($stock_qnty_sql);
        $stock_qnty_arr=array();
        foreach($stock_qnty_result as $row){
            $stock_qnty_arr[$row['PROD_ID']]=$row['BALANCE_STOCK'];
        }

		$i=1;
		ob_start();
        // echo $report_type;die;
		?>
		<div>
			<table style="width:1650px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_header_1" rules="all" align="left">
				<thead>
					<tr style="border:none;">
						<td colspan="19" align="center" style="border:none; font-size:14px;">
							<b><? echo $companyArr[$cbo_company_name]; ?></b>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="19" align="center" style="border:none; font-size:14px;">
							<b>
                            <? if($plot_no!="") echo $plot_no.", "; if($level_no!="") echo $level_no.", "; if($road_no!="") echo $road_no.", ";if($block_no!="") echo $block_no.", "; if($city!="") echo $city.", "; if($zip_code!="") echo $zip_code.", "; ?>
                            </b>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="19" align="center" style="border:none; font-size:14px;">
							<b><? echo $report_title; ?></b>
						</td>
					</tr>
					<tr>
						<th colspan="19">Requisiton Details</th>
					</tr>
					<tr>
						<th width="30">SL</th>
						<th width="50">Req. No</th>
						<th width="60">Req. Date</th>
						<th width="100">Store Name</th>
						<th width="100">Division</th>
						<th width="100">Department</th>
						<th width="100">Section</th>
						<th width="60">Required Date</th>
						<th width="100">Item Category</th>
						<th width="80">Item Group</th>
						<th width="150">Item Description</th>
						<th width="80">Required For</th>
                        <th width="60">UOM</th>
						<th width="80">Req. Quantity</th>
						<th width="80">Issue Qty</th>
                        <th width="80">Stock Balance</th>
                        <th width="100">Inserted By</th>
                        <th width="100">Approval Status</th>
						<th width="100" style="word-break: break-all;">Remarks</th>
					</tr>
				</thead>
			</table>
			<div style="width:1670px; max-height:350px; overflow-y:scroll;" id="scroll_body"  >
				<table style="width:1650px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left">
					<?
					foreach($data_result as $row)
					{
                        if($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="30" align="center"><? echo $i; ?></td>
                            <td width="50" align="center"><p><? echo $row["ITEMISSUE_REQ_PREFIX_NUM"]; ?></p></td>
                            <td width="60"><? echo change_date_format($row['INDENT_DATE']); ?></td>
                            <td width="100"><? echo $storeArr[$row['STORE_ID']]; ?></td>
                            <td width="100"><? echo $divisionArr[$row["DIVISION_ID"]]; ?></td>
                            <td width="100"><? echo $departmentArr[$row["DEPARTMENT_ID"]]; ?></td>
                            <td width="100"><? echo $sectionArr[$row["SECTION_ID"]]; ?></td>
                            <td width="60"><? echo $row["REQUIRED_DATE"]; ?></td>
                            <td width="100"><? echo $item_category[$row["ITEM_CATEGORY_ID"]]; ?></td>
                            <td width="80"><? echo $itemgroupArr[$row["ITEM_GROUP"]];?></td>
                            <td width="150"><? echo $row["ITEM_DESCRIPTION"];?></td>
                            <td width="80" ><? echo $row["REQ_FOR"]; ?></td>
                            <td width="60" align="center"><? echo $unit_of_measurement[$row["UOM"]]; ?></td>
                            <td width="80" align="right"><? echo number_format($row["REQ_QTY"],2); ?></td>
                            <td width="80" align="right"><? echo number_format($issue_arr[$row['INV_ISSUE_MASTER_ID']][$row['ITEM_CATEGORY_ID']][$row['PROD_ID']],2); ?></td>
                            <td width="80" align="right"><? echo number_format($stock_qnty_arr[$row['PROD_ID']],2); ?></td>
                            <td width="100" align="right"><? echo $user_array[$row['INSERTED_BY']]; ?></td>
                            <td width="100" align="right"><? echo $approved_arr[$row['IS_APPROVED']]; ?></td>
                            <td width="100"><? echo $row["REMARKS"]; ?></td>
                        </tr>
                        <?
                        $i++;
					}
					?>
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
?>
