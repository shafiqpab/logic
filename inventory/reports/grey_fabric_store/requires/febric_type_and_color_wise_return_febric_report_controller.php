<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Fabric Type and Colorwise Return Fabric Report sales
Functionality	:
JS Functions	:
Created by		:	Mostafizur Rahman
Creation date 	: 	06-nov-2023
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/

header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$storeNameArr=return_library_array( "SELECT id,store_name from lib_store_location ", "id", "store_name" );

if($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
	if($data[1]==1) $party="1,3,21,90"; else $party="80";
	echo create_drop_down( "cbo_buyer_id", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}
if ($action == "load_drop_down_cust_buyer") 
{
        echo create_drop_down("cbo_cust_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Cust Buyer --", $selected, "", 0);
       
        exit();
}
if($action=="order_no_search_popup")
{
    echo load_html_head_contents("Sales Order Info","../../../../", 1, 1, '','1','');
    extract($_REQUEST);
    ?>
<script>
function js_set_value(booking_data) {
    document.getElementById('hidden_booking_data').value = booking_data;
    parent.emailwindow.hide();
}
</script>
</head>

<body>
    <div align="center">
        <fieldset style="width:830px;margin-left:4px;">
            <form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
                <table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
                    <thead>
                        <th>Within Group</th>
                        <th>Search By</th>
                        <th>Search</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
                                class="formbutton" />
                            <input type="hidden" name="hidden_booking_data" id="hidden_booking_data" value="">
                        </th>
                    </thead>
                    <tr class="general">
                        <td align="center">
                            <? echo create_drop_down( "cbo_within_group", 150, $yes_no,"",1, "--Select--", "",$dd,0 ); ?>
                        </td>
                        <td align="center">
                            <?
                            $search_by_arr=array(1=>"Sales Order No",2=>"Sales / Booking No",3=>"Style Ref.");
                            echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                            ?>
                        </td>
                        <td align="center">
                            <input type="text" style="width:140px" class="text_boxes" name="txt_search_common"
                                id="txt_search_common" />
                        </td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show"
                                onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('cbo_within_group').value, 'create_order_no_search_list_view', 'search_div', 'stock_barcode_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
                                style="width:100px;" />
                        </td>
                    </tr>
                </table>
                <div id="search_div" style="margin-top:10px"></div>
            </form>
        </fieldset>
    </div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>

</html>
<?
exit();
}

if($action=="report_generate")
{  $process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
    $company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
    $buyer_arr   	= return_library_array( "select id, short_name from lib_buyer where status_active=1 and is_deleted=0", 'id','short_name');
    $color_library 	= return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	

    $company_id = str_replace("'","",$cbo_company_id);
    $buyer_id = str_replace("'","",$cbo_buyer_id);
    $cust_buyer_name = str_replace("'","",$cbo_cust_buyer_name);
    $cbo_within_group = str_replace("'","",$cbo_within_group);
    $txt_fso_no = str_replace("'","",$txt_fso_no);
    $booking_no = str_replace("'","",$booking_no);
    $cbo_value = str_replace("'","",$cbo_value);
    $txt_date_from = str_replace("'","",$txt_date_from);
    $txt_date_to = str_replace("'","",$txt_date_to);
    $sql_cond ="";
    $sql_cond .= ( $cbo_within_group !=0) ? "and d.within_group = '$cbo_within_group' " : "";
    $sql_cond .= ($buyer_id !=0) ? "and d.buyer_id =$buyer_id" :"";
    $sql_cond .= ($cust_buyer_name !=0) ? "and d.customer_buyer =$cust_buyer_name" :"";
    $sql_cond .= ($booking_no !="") ? "and d.sales_booking_no ='$booking_no'" :"";
    $sql_cond .= ($txt_fso_no !="") ? "and d.job_no like'%$txt_fso_no%'" : "";
    
    $dateCond .= ($txt_date_from && $txt_date_to) ?" and a.booking_date between '".change_date_format($txt_date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($txt_date_to,'dd-mm-yyyy','-',1)."'"  : ""; 
    $date_cond="";

	if( $txt_date_from!="" && $txt_date_to!="")
	{
		if($db_type==0)
		{
			$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
		}
		elseif($db_type==2)
		{
			$txt_date_from=change_date_format($txt_date_from,'','',1);
			$txt_date_to=change_date_format($txt_date_to,'','',1);
		}

		$date_cond = " and e.transaction_date between '$txt_date_from' and '$txt_date_to' ";
	}
    


$sql = "SELECT sum(b.qnty) as receive_qty,b.po_breakdown_id as po_id, c.prod_id,c.febric_description_id,c.gsm,c.color_range_id,c.color_id,c.stitch_length, c.width,d.company_id,d.buyer_id, d.style_ref_no, d.job_no, d.job_no_prefix_num, d.sales_booking_no, d.booking_id,d.within_group,d.po_company_id as lc_company_id,d.po_buyer,d.po_job_no,d.booking_without_order,d.booking_type,d.booking_entry_form,e.transaction_date,e.store_id,b.barcode_no, d.id as sales_id
from inv_receive_master a,inv_transaction e,pro_grey_prod_entry_dtls c,pro_roll_details b,fabric_sales_order_mst d
where a.id=e.mst_id and e.id=c.trans_id and c.id=b.dtls_id and b.po_breakdown_id=d.id $within_group_cond and b.entry_form in(58,2) and c.trans_id>0 and a.receive_basis in(2,4,10) and a.item_category=13 and d.company_id=$company_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.transaction_type=1 $sql_cond    $date_cond    
group by b.po_breakdown_id,c.prod_id,c.febric_description_id,c.gsm,c.color_range_id,c.color_id,c.stitch_length,c.width,d.company_id,d.buyer_id, d.style_ref_no,d.job_no, d.job_no_prefix_num, d.sales_booking_no, d.booking_id,d.within_group,d.po_company_id, d.po_buyer,d.po_job_no,d.booking_without_order, d.booking_type, d.booking_entry_form,e.transaction_date,e.store_id,b.barcode_no,d.id";

$sql_result=sql_select($sql);

$salesIdsArr=array();
$rcvQntyArr=array();
$color_data_arr="";
foreach( $sql_result as $row )
{
    if($salesIdsChk[$row[csf('sales_id')]]=='')
    {
        $salesIdsChk[$row[csf('sales_id')]] = $row[csf('sales_id')];
        array_push($salesIdsArr, $row[csf('sales_id')]);
        if($row[csf('color_id')]!="")
        $color_data_arr .= "," .$row[csf('color_id')];
    }

    $rcvQntyArr[$row[csf('job_no')]][$row[csf('febric_description_id')]][$row[csf('gsm')]]['receive_qty'] += $row[csf('receive_qty')];
}	
       
    $main_sql = "SELECT A.BOOKING_NO, B.ID, B.START_DATE, B.END_DATE, B.COLOR_ID, B.KNITTING_SOURCE, B.KNITTING_PARTY, B.MACHINE_DIA, B.FABRIC_DIA, C.ID AS DTLS_ID, C.PO_ID, C.GSM_WEIGHT, C.FABRIC_DESC, C.DETERMINATION_ID, C.YARN_DESC, C.PROGRAM_QNTY , E.BUYER_ID, E.WITHIN_GROUP, E.STYLE_REF_NO, E.JOB_NO, E.BOOKING_ENTRY_FORM, E.BOOKING_ID, F.ID AS SALES_ID, F.GREY_QTY, B.MACHINE_GG, E.PO_JOB_NO, E.COMPANY_ID, E.CUSTOMER_BUYER, B.COLOR_RANGE, B.STITCH_LENGTH,F.FABRIC_DESC, E.DELIVERY_START_DATE,E.DELIVERY_DATE , E.id as E_ID FROM PPL_PLANNING_INFO_ENTRY_MST A, PPL_PLANNING_INFO_ENTRY_DTLS B, PPL_PLANNING_ENTRY_PLAN_DTLS C, FABRIC_SALES_ORDER_MST E, FABRIC_SALES_ORDER_DTLS F  WHERE A.ID = B.MST_ID AND B.ID = C.DTLS_ID AND C.PO_ID = E.ID AND E.ID = F.MST_ID AND C.PO_ID = F.MST_ID AND C.IS_SALES = 1 AND b.color_id= f.color_id  AND A.COMPANY_ID = ".$company_id."  AND A.IS_SALES=1 AND A.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND C.STATUS_ACTIVE=1 ".where_con_using_array($salesIdsArr,0,'E.ID')." ORDER BY B.ID, A.BOOKING_NO";
    $main_sql_rslt=sql_select($main_sql);
       

    $issue_sql = "SELECT d.po_breakdown_id,e.transaction_date,d.booking_no as prog_no,b.prod_id,d.barcode_no,d.qnty as issue_qty,f.job_no ,f.id, b.color_id from inv_issue_master a,inv_transaction e,inv_grey_fabric_issue_dtls b,pro_roll_details d,fabric_sales_order_mst f 
    where a.status_active=1 and a.is_deleted=0 and a.id=e.mst_id and e.id=b.trans_id and b.id=d.dtls_id and a.item_category=13 
    and a.entry_form=61 and d.entry_form=61 and e.transaction_type=2 and e.status_active=1 and e.is_deleted=0 and d.po_breakdown_id=f.id and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0  ".where_con_using_array($salesIdsArr,0,'f.id')." ";

	// echo $issue_sql;

	$issue_sql_result = sql_select($issue_sql);
	$issue_arry = array();
	foreach($issue_sql_result as $row )
	{
		$barcodein=$row[csf('BARCODE_NO')];
        $barcode_issue_data= sql_select("SELECT c.qc_pass_qnty,b.color_id, c.barcode_no, febric_description_id FROM  pro_grey_prod_entry_dtls b, pro_roll_details c 
        WHERE b.id=c.dtls_id  and c.entry_form=58  and c.barcode_no = $barcodein and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");
        $issue_arry[$row[csf('id')]][$barcode_issue_data[0][csf('febric_description_id')]] [$barcode_issue_data[0][csf('color_id')]]['QTY'] += $barcode_issue_data[0][csf('qc_pass_qnty')];
	}

	

    $trans_out_sql = "SELECT A.FROM_ORDER_ID,B.FROM_PROD_ID,SUM(D.QNTY) AS TRANSFER_OUT_QNTY,F.JOB_NO, d.barcode_no , E.TRANSACTION_TYPE, F.ID
    FROM INV_ITEM_TRANSFER_MST A,INV_TRANSACTION E,INV_ITEM_TRANSFER_DTLS B,PRO_ROLL_DETAILS D,FABRIC_SALES_ORDER_MST F 
    WHERE A.STATUS_ACTIVE=1 AND A.ENTRY_FORM=133 AND A.TRANSFER_CRITERIA=4 AND A.ID=E.MST_ID AND E.ID=B.TRANS_ID AND B.ID=D.DTLS_ID 
     AND A.FROM_ORDER_ID=F.ID AND B.STATUS_ACTIVE=1 AND E.STATUS_ACTIVE=1 AND E.IS_DELETED=0 AND E.TRANSACTION_TYPE=6 
    AND A.ID=D.MST_ID AND D.ENTRY_FORM=133 AND B.ID=D.DTLS_ID AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0 ".where_con_using_array($salesIdsArr,0,'F.ID')." GROUP BY A.FROM_ORDER_ID,B.FROM_PROD_ID,F.JOB_NO, d.barcode_no , E.TRANSACTION_TYPE, F.ID";
	//echo $trans_out_sql;
	$trans_out_rslt = sql_select($trans_out_sql);

	$trans_out_barcode = array();
	foreach( $trans_out_rslt as $row )
	{

        $barcodein=$row[csf('BARCODE_NO')];
        $barcode_out_data= sql_select("SELECT c.qc_pass_qnty,b.color_id, c.barcode_no, febric_description_id FROM  pro_grey_prod_entry_dtls b, pro_roll_details c 
        WHERE b.id=c.dtls_id  and c.entry_form=58  and c.barcode_no = $barcodein and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");
        $trans_out_barcode[$row[csf('id')]][$barcode_out_data[0][csf('febric_description_id')]] [$barcode_out_data[0][csf('color_id')]]['QTY'] += $barcode_out_data[0][csf('qc_pass_qnty')];
	}
	unset($trans_out_rslt);
    // var_dump($trans_out_barcode);

    $trans_in_sql = "SELECT A.TO_ORDER_ID, B.TO_PROD_ID, SUM(D.QNTY) AS TRANSFER_IN_QNTY, F.JOB_NO , D.BARCODE_NO , E.TRANSACTION_TYPE, F.ID
    FROM INV_ITEM_TRANSFER_MST A, INV_TRANSACTION E, INV_ITEM_TRANSFER_DTLS B, PRODUCT_DETAILS_MASTER C, PRO_ROLL_DETAILS D, FABRIC_SALES_ORDER_MST F WHERE A.ENTRY_FORM = 133 AND A.TRANSFER_CRITERIA = 4 AND A.ID = E.MST_ID AND E.ID = B.TRANS_ID AND B.ID = D.DTLS_ID AND B.FROM_PROD_ID = C.ID AND A.TO_ORDER_ID = F.ID  AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND C.STATUS_ACTIVE = 1 AND C.IS_DELETED = 0 AND D.STATUS_ACTIVE = 1 AND D.IS_DELETED = 0  AND E.STATUS_ACTIVE = 1 AND E.IS_DELETED = 0 AND E.TRANSACTION_TYPE = 6 AND A.ID = D.MST_ID AND D.ENTRY_FORM = 133 AND B.ID = D.DTLS_ID  ".where_con_using_array($salesIdsArr,0,'F.ID')." GROUP BY A.TO_ORDER_ID, B.TO_PROD_ID,F.JOB_NO,D.BARCODE_NO , E.TRANSACTION_TYPE, F.ID";
	// echo $trans_in_sql;
	$trans_in_sql_result = sql_select($trans_in_sql);

    $trans_in_barcode=array();
	$trnsInQtyArr = array();
	foreach($trans_in_sql_result as $row )
	{   
        $barcodein=$row[csf('BARCODE_NO')];
        $barcode_in_data= sql_select("SELECT c.qc_pass_qnty,b.color_id, c.barcode_no, febric_description_id FROM  pro_grey_prod_entry_dtls b, pro_roll_details c 
        WHERE b.id=c.dtls_id  and c.entry_form=58  and c.barcode_no = $barcodein and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");
        $trans_in_barcode[$row[csf('id')]][$barcode_in_data[0][csf('febric_description_id')]][$barcode_in_data[0][csf('color_id')]]['QTY'] += $barcode_in_data[0][csf('qc_pass_qnty')];
	}
	unset($trans_in_sql_result);

?>
<br><br>
<h3>Fabric Type and Colorwise Return Fabric Report sales</h3>
<h4>
    <? echo $company_arr[$company_id] ?>
</h4>
<br><br>
<div style="width:1900px ; overflow:scroll;height:300px">
    <table width="1860" style="padding:10px;" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
        <thead>
            <th width="40">SL</th>
            <th width="140">Company</th>
            <th width="140">Cust Buyer</th>
            <th width="100">Fso</th>
            <th width="100">Booking</th>
            <th width="200">F/Type</th>
            <th width="100">Color</th>
            <th width="75">Required Qty</th>
            <th width="75">Received Qty</th>
            <th width="75">Issue Returned</th>
            <th width="75">Transfered In</th>
            <th width="75">Transfered Out</th>
            <th width="75">Total Received</th>
            <th width="75">Received Balance</th>
            <th width="75">Issue</th>
            <th width="75">Total Issue</th>
            <th width="75">Issue Balance</th>
            <th width="75">Stock</th>
            <th width="100">T&A start</th>
            <th >T&A end</th>
        </thead>
    </table>
    
    <table width="1860" style="padding:10px;" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_body_1">
        <tbody id="tbodyx">
            <?php 
            $i=1;
                foreach($main_sql_rslt as $row)
                { 
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";


                    $e_id=$row['E_ID'];
                    $color_id= $row['COLOR_ID'];
                    $com_id=$row['COMPANY_ID'];
                    $issue_sql= "SELECT SUM(B.QNTY) AS ISSUE_RTN_QTY 
                    FROM INV_RECEIVE_MASTER A,INV_TRANSACTION E,PRO_GREY_PROD_ENTRY_DTLS C,PRO_ROLL_DETAILS B,FABRIC_SALES_ORDER_MST D 
                    WHERE A.ID=E.MST_ID AND E.ID=C.TRANS_ID AND C.ID=B.DTLS_ID AND B.PO_BREAKDOWN_ID=D.ID AND B.ENTRY_FORM IN(84) AND C.TRANS_ID>0 AND A.ITEM_CATEGORY=13 AND D.COMPANY_ID=$com_id AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 
                    AND C.IS_DELETED=0 AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0 AND E.STATUS_ACTIVE=1 AND E.IS_DELETED=0 AND C.COLOR_ID in ($color_id) AND E.TRANSACTION_TYPE=4 and (D.ID in($e_id)) ";
                    $issue_ret = sql_select($issue_sql);


                    $gray_qty = $row['GREY_QTY'];
                    $total_gray_qty += $row['GREY_QTY'];

                    $program_qty = $row['PROGRAM_QNTY'];
                    $total_program_qty +=  $row['PROGRAM_QNTY'];

                    $issuQty = $issue_ret[0]['ISSUE_RTN_QTY'];
                    $total_issuQty += $issue_ret[0]['ISSUE_RTN_QTY'];

                    $trns_in_qty = $trans_in_barcode[$row['E_ID']][$row['DETERMINATION_ID']][$row['COLOR_ID']]['QTY'];
                    $total_trns_in_qty += $trans_in_barcode[$row['E_ID']][$row['DETERMINATION_ID']][$row['COLOR_ID']]['QTY'];

                    $trns_out_qty = $trans_out_barcode[$row['E_ID']][$row['DETERMINATION_ID']][$row['COLOR_ID']]['QTY'];
                    $total_trns_out_qty += $trans_out_barcode[$row['E_ID']][$row['DETERMINATION_ID']][$row['COLOR_ID']]['QTY'];

                    $total_received = $program_qty + $issuQty + $trns_in_qty - $trns_out_qty ;
                    $total_received_sum += $program_qty + $issuQty + $trns_in_qty - $trns_out_qty ;

                    $receive_balance = $program_qty - $gray_qty;
                    $total_receive_balance += $program_qty - $gray_qty;

                    $issue_qnty = $issue_arry[$row['E_ID']][$row['DETERMINATION_ID']][$row['COLOR_ID']]['QTY'];
                    $total_issue_qnty += $issue_arry[$row['E_ID']][$row['DETERMINATION_ID']][$row['COLOR_ID']]['QTY'];

                    $total_issue = $issue_qnty - $issuQty;
                    $total_issue_sum += $issue_qnty - $issuQty;

                    $issue_balance = $gray_qty - $total_issue;
                    $total_issue_balance = $gray_qty - $total_issue;

                    $stock = $total_received - $total_issue ;
                    $total_stock += $total_received - $total_issue ;

                    $sql_3= "SELECT sum(b.y_count) as yarn_count from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, product_details_master d , fabric_sales_order_mst e WHERE a.id=b.mst_id and b.id=c.dtls_id and b.from_prod_id=d.id and a.entry_form in(133) and c.entry_form in(133) and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=e.id and e.id=$e_id and b.color_names=$color_id order by barcode_no";

                    $y_count = sql_select($sql_3);

                    ?>
                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;
                    ?>')"
                        id="tr<? echo $i;?>">
                        <td  width="40" align="center" width="100">
                            <? echo $i; ?>
                        </td>
                        <td  align="left" width="140">
                            <? echo $company_arr[$row['COMPANY_ID']]; ?>
                        </td>
                        <td  align="left" width="140">
                            <? echo $buyer_arr[$row['CUSTOMER_BUYER']]; ?>
                        </td>
                        <td   align="left" width="100">
                            <? echo $row['JOB_NO']; ?>
                        </td>
                        <td   align="left" width="100">
                            <? echo $row['BOOKING_NO']; ?>
                        </td>
                        <td  align="left" width="200">
                            <p>
                                <? echo $row['FABRIC_DESC']; ?>
                            </p>
                        </td>
                        <td  align="left" width="100">
                            <? echo $color_library[$row['COLOR_ID']]; ?> 
                        </td>
                        <td align="right" width="75">
                            <?
                            echo $row['GREY_QTY'];
                            ?>
                        </td>
                        <td align="right" width="75">
                            <? echo $row['PROGRAM_QNTY'] ; ?>
                        </td>
                        <td align="right" width="75">
                            <? echo $issue_ret[0]['ISSUE_RTN_QTY']; ?>
                        </td>
                        <td align="right" width="75">
                            <? echo $trans_in_barcode[$row['E_ID']][$row['DETERMINATION_ID']][$row['COLOR_ID']]['QTY']; ?>
                        </td>
                        <td align="right" width="75">
                            <?  echo $trans_out_barcode[$row['E_ID']][$row['DETERMINATION_ID']][$row['COLOR_ID']]['QTY'];?>
                        </td>
                        <td align="right" width="75">
                            <? echo $total_received ?>
                        </td>
                        <td align="right" width="75">
                            <? echo $receive_balance ?>
                        </td>
                        <td align="right" width="75">
                            <? echo $issue_qnty ?> 
                        </td>
                        <td align="right" width="75">
                            <? echo $total_issue ?>
                        </td>
                        <td align="right" width="75">
                            <? echo $issue_balance  ?>
                        </td>
                        <td align="right" width="75">
                            <? echo $stock ?>
                        </td>
                        <td align="center" width="100">
                            <? echo $row['DELIVERY_START_DATE'] ?>
                        </td>
                        <td align="center" >
                            <? echo $row['DELIVERY_DATE']  ?>
                        </td>

                    </tr>
                </tbody>

                    <?  
                    $i++; 
                }
       
        ?>
    </table>

    <table width="1860" style="padding:10px;" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_footer_1">
        <tfoot style="padding:10px;" cellspacing="0" border="1" class="rpt_table" rules="all" id="">
            <tr>
                <th width="40" align="right" colspan=""></th>
                <th width="140" align="right" colspan=""></th>
                <th width="140" align="right" colspan=""></th>
                <th width="100" align="right" colspan=""></th>
                <th width="100" align="right" colspan=""></th>
                <th width="200" align="right" colspan=""></th>
                <th width="100" align="right" colspan="">Total</th>

                <th width="75" id="final_booking_qty" align="right">
                    <? echo $total_gray_qty ; ?>
                </th>
                <th width="75" id="final_tot_rcv_qnty" align="right">
                    <? echo $total_program_qty ; ?>
                </th>
                <th width="75" id="final_issue_rtn_qty" align="right">
                    <? echo $total_issuQty ; ?>
                </th>
                <th width="75" id="final_trans_in_qty" align="right">
                    <? echo $total_trns_in_qty ; ?>
                </th>
                <th width="75" id="final_trans_out_qty" align="right">
                    <? echo $total_trns_out_qty ; ?>
                </th>

                <th width="75" id="final_rcv_total" align="right">
                    <? echo $total_received_sum ; ?>
                </th>
                <th width="75" id="final_rcv_blnc" align="right">
                    <? echo $total_receive_balance ; ?>
                </th>
                <th width="75" id="final_rcv_issue_qty" align="right">
                    <? echo $total_issue_qnty; ?>
                </th>
                <th width="75" id="final_total_current_issue" align="right">
                    <? echo $total_issue_sum ?>
                </th>
                <th width="75" id="final_total_issue_blnc_qty" align="right">
                    <? echo $total_issue_balance ?>
                </th>
                <th width="75" id="final_total_stock_Qty" align="right">
                    <? echo $total_stock ?>
                </th>
                <th width="100" align="right"></th>
                <th align="right"></th>
            </tr>
        </tfoot>
        <?
        ?>

    </table>
</div>
</div>

<? 
  exit();
  
}


// created by Mr. Mostafizur Rahman -> 12-nov-2023
//show button 2 starts here
if($action=='report_generate2')
{ 
    $process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
    $company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
    $buyer_arr   	= return_library_array( "select id, short_name from lib_buyer where status_active=1 and is_deleted=0", 'id','short_name');
    $color_library 	= return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$yarn_brand_arr = return_library_array("select id,brand_name from lib_brand", 'id', 'brand_name');
    $yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0 order by yarn_count", 'id', 'yarn_count');
$bodypart_with_type_arr = return_library_array("select id, body_part_full_name from lib_body_part where status_active=1 and is_deleted=0", "id", "body_part_full_name");
    $company_id = str_replace("'","",$cbo_company_id);
    $buyer_id = str_replace("'","",$cbo_buyer_id);
    $cust_buyer_name = str_replace("'","",$cbo_cust_buyer_name);
    $cbo_within_group = str_replace("'","",$cbo_within_group);
    $txt_fso_no = str_replace("'","",$txt_fso_no);
    $booking_no = str_replace("'","",$booking_no);
    $cbo_value = str_replace("'","",$cbo_value);
    $txt_date_from = str_replace("'","",$txt_date_from);
    $txt_date_to = str_replace("'","",$txt_date_to);
    $sql_cond ="";
    $sql_cond .= ( $cbo_within_group !=0) ? "and d.within_group = '$cbo_within_group' " : "";
    $sql_cond .= ($buyer_id !=0) ? "and d.buyer_id =$buyer_id" :"";
    $sql_cond .= ($cust_buyer_name !=0) ? "and d.customer_buyer =$cust_buyer_name" :"";
    $sql_cond .= ($booking_no !="") ? "and d.sales_booking_no ='$booking_no'" :"";
    $sql_cond .= ($txt_fso_no !="") ? "and d.job_no like'%$txt_fso_no%'" : "";
    
    $dateCond .= ($txt_date_from && $txt_date_to) ?" and a.booking_date between '".change_date_format($txt_date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($txt_date_to,'dd-mm-yyyy','-',1)."'"  : ""; 
    $date_cond="";

	if( $txt_date_from!="" && $txt_date_to!="")
	{
		if($db_type==0)
		{
			$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
		}
		elseif($db_type==2)
		{
			$txt_date_from=change_date_format($txt_date_from,'','',1);
			$txt_date_to=change_date_format($txt_date_to,'','',1);
		}

		$date_cond = " and e.transaction_date between '$txt_date_from' and '$txt_date_to' ";
	}
    


$sql = "SELECT sum(b.qnty) as receive_qty,b.po_breakdown_id as po_id, c.prod_id,c.febric_description_id,c.gsm,c.color_range_id,c.color_id,c.stitch_length, c.width,d.company_id,d.buyer_id, d.style_ref_no, d.job_no, d.job_no_prefix_num, d.sales_booking_no, d.booking_id,d.within_group,d.po_company_id as lc_company_id,d.po_buyer,d.po_job_no,d.booking_without_order,d.booking_type,d.booking_entry_form,e.transaction_date,e.store_id,b.barcode_no, d.id as sales_id
from inv_receive_master a,inv_transaction e,pro_grey_prod_entry_dtls c,pro_roll_details b,fabric_sales_order_mst d
where a.id=e.mst_id and e.id=c.trans_id and c.id=b.dtls_id and b.po_breakdown_id=d.id $within_group_cond and b.entry_form in(58,2) and c.trans_id>0 and a.receive_basis in(2,4,10) and a.item_category=13 and d.company_id=$company_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.transaction_type=1 $sql_cond    $date_cond    
group by b.po_breakdown_id,c.prod_id,c.febric_description_id,c.gsm,c.color_range_id,c.color_id,c.stitch_length,c.width,d.company_id,d.buyer_id, d.style_ref_no,d.job_no, d.job_no_prefix_num, d.sales_booking_no, d.booking_id,d.within_group,d.po_company_id, d.po_buyer,d.po_job_no,d.booking_without_order, d.booking_type, d.booking_entry_form,e.transaction_date,e.store_id,b.barcode_no,d.id";
 //echo $sql; die;
$sql_result=sql_select($sql);

$salesIdsArr=array();
$rcvQntyArr=array();
foreach( $sql_result as $row )
{
    if($salesIdsChk[$row[csf('sales_id')]]=='')
    {
        $salesIdsChk[$row[csf('sales_id')]] = $row[csf('sales_id')];
        array_push($salesIdsArr, $row[csf('sales_id')]);
    }

    $rcvQntyArr[$row[csf('job_no')]][$row[csf('febric_description_id')]][$row[csf('gsm')]]['receive_qty'] += $row[csf('receive_qty')];
}	
$booking_sql = "SELECT A.JOB_NO, B.DETERMINATION_ID, B.GSM_WEIGHT,B.COLOR_ID, B.GREY_QNTY_BY_UOM
FROM FABRIC_SALES_ORDER_MST A, FABRIC_SALES_ORDER_DTLS B   
WHERE A.ID = B.MST_ID  AND A.COMPANY_ID = ".$company_id."  AND A.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.STATUS_ACTIVE=1 ".where_con_using_array($salesIdsArr,0,'A.ID')."
ORDER BY A.ID";
//echo $booking_sql; 
$booking_sql_rslt=sql_select($booking_sql);
		$bookingQntyArr = array();
		foreach ($booking_sql_rslt as $row) 
		{
			$bookingQntyArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['COLOR_ID']]['GREY_QNTY_BY_UOM'] += $row['GREY_QNTY_BY_UOM'];
		}
		
		// echo "<pre>";
		// print_r($bookingQntyArr);
        $main_sql = "SELECT 
			A.BOOKING_NO, 
			B.ID, B.START_DATE, B.END_DATE, B.COLOR_ID, B.KNITTING_SOURCE, B.KNITTING_PARTY, B.MACHINE_DIA, B.FABRIC_DIA, 
			C.ID AS DTLS_ID, C.PO_ID, C.GSM_WEIGHT, C.FABRIC_DESC, C.DETERMINATION_ID, C.YARN_DESC, C.PROGRAM_QNTY ,
			E.BUYER_ID, E.WITHIN_GROUP, E.STYLE_REF_NO, E.JOB_NO, E.BOOKING_ENTRY_FORM, E.BOOKING_ID,
			F.ID AS SALES_ID, F.GREY_QTY, B.MACHINE_GG, E.PO_JOB_NO, E.COMPANY_ID, E.CUSTOMER_BUYER, B.COLOR_RANGE, B.STITCH_LENGTH,F.FABRIC_DESC,
			E.DELIVERY_START_DATE,E.DELIVERY_DATE,F.BODY_PART_ID FROM PPL_PLANNING_INFO_ENTRY_MST A, PPL_PLANNING_INFO_ENTRY_DTLS B, PPL_PLANNING_ENTRY_PLAN_DTLS C, FABRIC_SALES_ORDER_MST E, FABRIC_SALES_ORDER_DTLS F   
			WHERE A.ID = B.MST_ID AND B.ID = C.DTLS_ID AND C.PO_ID = E.ID AND E.ID = F.MST_ID AND C.PO_ID = F.MST_ID AND C.IS_SALES = 1  AND A.COMPANY_ID = ".$company_id."  AND A.IS_SALES=1 AND A.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND C.STATUS_ACTIVE=1 ".where_con_using_array($salesIdsArr,0,'E.ID')."
			ORDER BY B.ID, A.BOOKING_NO";
            $main_sql_rslt=sql_select($main_sql);
	$mainArr = array();
	$programIdsArr = array();
	foreach ($main_sql_rslt as $row) 
	{
		
		
		if($duplicate_check[$row['DTLS_ID']] != $row['DTLS_ID'])
		{
			$duplicate_check[$row['DTLS_ID']] = $row['DTLS_ID'];

			if($prog_ids_check[$row['ID']] == '')
			{
				$prog_ids_check[$row['ID']] = $row['ID'];
				array_push($programIdsArr,$row['ID']);
			}
			

			//for color
			$color_arr = array();
			$exp_color = array();
			$exp_color = explode(",", $row['COLOR_ID']);
			foreach ($exp_color as $key=>$val)
			{
				$color_arr[$val] = $color_library[$val];
			}
			//end for color

			//for color_range
			$color_range_arr = array();
			$exp_color_range = array();
			$exp_color_range = explode(",", $row['COLOR_RANGE']);
			foreach ($exp_color_range as $key=>$val)
			{
				$color_range_arr[$val] = $color_range[$val];
			}
			//end for color_range

			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['JOB_NO'] = $row['JOB_NO'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['BOOKING_NO'] = $row['BOOKING_NO'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['DETERMINATION_ID'] = $row['DETERMINATION_ID'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['GSM_WEIGHT'] = $row['GSM_WEIGHT'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['COMPANY_ID'] = $row['COMPANY_ID'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['CUSTOMER_BUYER'] = $row['CUSTOMER_BUYER'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['FABRIC_DESC'] = $row['FABRIC_DESC'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['FABRIC_DIA'] = $row['FABRIC_DIA'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['MACHINE_DIA'] = $row['MACHINE_DIA'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['PROGRAM_QNTY'] += $row['PROGRAM_QNTY'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['FABRIC_COLOR_ID'] = $row['COLOR_ID'];
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['FABRIC_COLOR'] = implode(', ', $color_arr);
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['COLOR_RANGE'] = implode(', ', $color_range_arr);
			$mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['STITCH_LENGTH'] = $row['STITCH_LENGTH'];
            $mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['DELIVERY_START_DATE'] = $row['DELIVERY_START_DATE'];
            $mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['DELIVERY_DATE'] = $row['DELIVERY_DATE'];
	        $mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['BODY_PART_ID'] = $row['BODY_PART_ID'];
            $mainArr[$row['JOB_NO']][$row['DETERMINATION_ID']][$row['GSM_WEIGHT']][$row['ID']]['GREY_QTY'] = $row['GREY_QTY'];
	
        }
	}
	unset($main_sql_rslt);
	// echo "<pre>";
	// print_r($mainArr);

	$rcv_sql =  "SELECT A.BOOKING_NO,B.BOOKING_ID, SUM(A.QNTY) QNTY FROM PRO_ROLL_DETAILS A, INV_RECEIVE_MASTER B WHERE A.ENTRY_FORM = 58 AND A.MST_ID = B.ID AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 ".where_con_using_array($programIdsArr,1,'A.BOOKING_NO')."  GROUP BY A.BOOKING_NO,B.BOOKING_ID";

	//echo $rcv_sql;//die;		
	$rcv_sql_rslt=sql_select($rcv_sql);
	$duplicate_check = array();							 
	foreach($rcv_sql_rslt as $row)
	{
		$knitProQtyArr[$row['BOOKING_NO']] += $row['QNTY'];
	} 


	$sql_requ = "SELECT KNIT_ID, REQUISITION_NO, PROD_ID, YARN_QNTY FROM PPL_YARN_REQUISITION_ENTRY WHERE STATUS_ACTIVE=1 AND IS_DELETED=0 ".where_con_using_array($programIdsArr,0,'KNIT_ID')." ";
	//echo $sql_requ;
	$sql_requ_result = sql_select($sql_requ);
	$requArr = array();
	foreach ($sql_requ_result as $row)
	{
		$requArr[$row['KNIT_ID']]['prod_id']  .= $row['PROD_ID'].', ';
	}
	//var_dump($requArr);

	$product_details_array = array();
	$yarn_info_sql = "SELECT ID, SUPPLIER_ID, LOT, CURRENT_STOCK, YARN_COMP_TYPE1ST, YARN_COMP_PERCENT1ST, YARN_COMP_TYPE2ND, YARN_COMP_PERCENT2ND, YARN_COUNT_ID, YARN_TYPE, COLOR,yarn_comp_type1st, BRAND FROM PRODUCT_DETAILS_MASTER WHERE ITEM_CATEGORY_ID=1 AND COMPANY_ID=".$company_id." AND STATUS_ACTIVE=1 AND IS_DELETED=0 AND ID IN(SELECT PROD_ID FROM PPL_YARN_REQUISITION_ENTRY WHERE STATUS_ACTIVE=1 AND IS_DELETED=0 ".where_con_using_array($programIdsArr,0,'KNIT_ID').")";

	//echo $yarn_info_sql;
    
	$yarn_info_result = sql_select($yarn_info_sql);
	foreach ($yarn_info_result as $row)
	{
		$product_details_array[$row['ID']]['count'] = $yarn_count_arr[$row['YARN_COUNT_ID']];
		$product_details_array[$row['ID']]['lot'] = $row['LOT'];
		$product_details_array[$row['ID']]['brand'] = $yarn_brand_arr[$row['BRAND']];
        $product_details_array[$row[csf("id")]]["comp"] = $composition[$row[csf("yarn_comp_type1st")]];
        $product_details_array[$row[csf("id")]]["type"] = $yarn_type[$row[csf("yarn_type")]];
	}
	unset($yarn_info_result);
	// echo "<pre>";
    // print_r($product_details_array);

	
	$issue_rtn_sql = "SELECT A.RECV_NUMBER,SUM(B.QNTY) AS ISSUE_RTN_QTY,B.PO_BREAKDOWN_ID AS PO_ID, C.PROD_ID,C.FEBRIC_DESCRIPTION_ID,C.GSM,C.COLOR_ID,D.COMPANY_ID, D.JOB_NO
	FROM INV_RECEIVE_MASTER A,INV_TRANSACTION E,PRO_GREY_PROD_ENTRY_DTLS C,PRO_ROLL_DETAILS B,FABRIC_SALES_ORDER_MST D
	WHERE A.ID=E.MST_ID AND E.ID=C.TRANS_ID AND C.ID=B.DTLS_ID AND B.PO_BREAKDOWN_ID=D.ID  AND B.ENTRY_FORM IN(84) AND C.TRANS_ID>0 AND A.ITEM_CATEGORY=13 AND D.COMPANY_ID=".$company_id." AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND 
	C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0 AND E.STATUS_ACTIVE=1 AND E.IS_DELETED=0 AND
	E.TRANSACTION_TYPE=4 ".where_con_using_array($salesIdsArr,0,'D.ID')." 
	GROUP BY A.RECV_NUMBER,B.PO_BREAKDOWN_ID, C.PROD_ID,C.FEBRIC_DESCRIPTION_ID,C.GSM,C.COLOR_ID,D.COMPANY_ID, D.JOB_NO";

	// echo $issue_rtn_sql;
    

	$issue_rtn_sql_result = sql_select($issue_rtn_sql);
	$issueRtnQtyArr = array();
	foreach ($issue_rtn_sql_result as $row)
	{
		$issueRtnQtyArr[$row['JOB_NO']][$row['FEBRIC_DESCRIPTION_ID']][$row['GSM']]['issue_rtn_qty'] += $row['ISSUE_RTN_QTY'];
	}
	unset($issue_rtn_sql_result);
	// echo "<pre>";
	// print_r($issueRtnQtyArr);

	$issue_sql = "SELECT d.po_breakdown_id,e.transaction_date,d.booking_no as prog_no,b.prod_id,d.barcode_no,d.qnty as issue_qty,f.job_no from inv_issue_master a,inv_transaction e,inv_grey_fabric_issue_dtls b,pro_roll_details d,fabric_sales_order_mst f where a.status_active=1 and a.is_deleted=0 and a.id=e.mst_id and e.id=b.trans_id and b.id=d.dtls_id and a.item_category=13 and a.entry_form=61 and d.entry_form=61 and e.transaction_type=2	and e.status_active=1 and e.is_deleted=0 and d.po_breakdown_id=f.id  and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 ".where_con_using_array($salesIdsArr,0,'f.id')." ";

	//echo $issue_sql;// and d.is_returned<>1

	$issue_sql_result = sql_select($issue_sql);
	$prodIdsArr = array();
	foreach($issue_sql_result as $row )
	{
		if($prodIdsChk[$row[csf('prod_id')]]=='')
		{
			$prodIdsChk[$row[csf('prod_id')]] = $row[csf('prod_id')];
			array_push($prodIdsArr, $row[csf('prod_id')]);
		}
	}

	$prod_info_sql = "SELECT ID, DETARMINATION_ID, GSM FROM PRODUCT_DETAILS_MASTER WHERE ITEM_CATEGORY_ID=13 AND COMPANY_ID=".$company_id." AND STATUS_ACTIVE=1 AND IS_DELETED=0   ";
	//echo $prod_info_sql; //".where_con_using_array($prodIdsArr,0,'ID')."

	$prod_info_result = sql_select($prod_info_sql);
	$prodInfoArr = array();
	foreach($prod_info_result as $row )
	{
		$prodInfoArr[$row[csf('ID')]]['detarmination_id'] = $row[csf('DETARMINATION_ID')];
		$prodInfoArr[$row[csf('ID')]]['gsm'] = $row[csf('GSM')];
	}
	//var_dump($prodInfoArr);
	$issueQtyArr = array();
	foreach($issue_sql_result as $row )
	{
		$date_frm=date('Y-m-d',strtotime($date_from));
		$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));

		if($transaction_date >= $date_frm)
		{
			$detarmination_id = $prodInfoArr[$row[csf('prod_id')]]['detarmination_id'];
			$gsm = $prodInfoArr[$row[csf('prod_id')]]['gsm'];

			$issueQtyArr[$row[csf('job_no')]][$detarmination_id][$gsm]['issue_qnty'] += $row[csf('issue_qty')];
		}
	}
	/* echo "<pre>";
	print_r($issueQtyArr); */

	$trans_out_sql = "SELECT A.FROM_ORDER_ID,B.FROM_PROD_ID,SUM(D.QNTY) AS TRANSFER_OUT_QNTY,F.JOB_NO FROM INV_ITEM_TRANSFER_MST A,INV_TRANSACTION E,INV_ITEM_TRANSFER_DTLS B,PRODUCT_DETAILS_MASTER C,PRO_ROLL_DETAILS D,FABRIC_SALES_ORDER_MST F WHERE A.STATUS_ACTIVE=1 AND A.ENTRY_FORM=133 AND A.TRANSFER_CRITERIA=4 AND A.ID=E.MST_ID AND E.ID=B.TRANS_ID AND B.ID=D.DTLS_ID AND B.FROM_PROD_ID=C.ID AND A.FROM_ORDER_ID=F.ID AND B.STATUS_ACTIVE=1 AND E.STATUS_ACTIVE=1 AND E.IS_DELETED=0 AND E.TRANSACTION_TYPE=6  AND A.ID=D.MST_ID AND D.ENTRY_FORM=133 AND B.ID=D.DTLS_ID AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0 ".where_con_using_array($salesIdsArr,0,'F.ID')." GROUP BY A.FROM_ORDER_ID,B.FROM_PROD_ID,F.JOB_NO";
	//echo $trans_out_sql;
	$trans_out_rslt = sql_select($trans_out_sql);

	$trnsOutQtyArr = array();
	foreach($trans_out_rslt as $row )
	{
		$detarmination_id = $prodInfoArr[$row[csf('FROM_PROD_ID')]]['detarmination_id'];
		$gsm = $prodInfoArr[$row[csf('FROM_PROD_ID')]]['gsm'];

		$trnsOutQtyArr[$row['JOB_NO']][$detarmination_id][$gsm]['TRANSFER_OUT_QNTY'] += $row['TRANSFER_OUT_QNTY'];
	}
	unset($trans_out_rslt);
	// echo "<pre>";
	// print_r($trnsOutQtyArr);

	$trans_in_sql = "SELECT A.TO_ORDER_ID,B.TO_PROD_ID,SUM(D.QNTY) AS TRANSFER_IN_QNTY,F.JOB_NO FROM INV_ITEM_TRANSFER_MST A,INV_TRANSACTION E,INV_ITEM_TRANSFER_DTLS B,PRODUCT_DETAILS_MASTER C,PRO_ROLL_DETAILS D,FABRIC_SALES_ORDER_MST F WHERE  A.ENTRY_FORM=133 AND A.TRANSFER_CRITERIA=4 AND A.ID=E.MST_ID AND E.ID=B.TRANS_ID AND B.ID=D.DTLS_ID AND B.FROM_PROD_ID=C.ID AND A.TO_ORDER_ID=F.ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0 AND E.STATUS_ACTIVE=1 AND E.IS_DELETED=0 AND E.TRANSACTION_TYPE=6  AND A.ID=D.MST_ID AND D.ENTRY_FORM=133 AND B.ID=D.DTLS_ID  ".where_con_using_array($salesIdsArr,0,'F.ID')." GROUP BY A.TO_ORDER_ID,B.TO_PROD_ID,F.JOB_NO";
	//echo $trans_in_sql;
	$trans_in_sql_result = sql_select($trans_in_sql);
	$trnsInQtyArr = array();
	foreach($trans_in_sql_result as $row )
	{
		$detarmination_id = $prodInfoArr[$row[csf('TO_PROD_ID')]]['detarmination_id'];
		$gsm = $prodInfoArr[$row[csf('TO_PROD_ID')]]['gsm'];

		$trnsInQtyArr[$row['JOB_NO']][$detarmination_id][$gsm]['transfer_in_qnty'] += $row['TRANSFER_IN_QNTY'];
	}
	unset($trans_in_sql_result);
?>
<br><br>
<h3>Fabric Type and Colorwise Return Fabric Report sales</h3>
<h4>
    <? echo $company_arr[$company_id] ?>
</h4>
<br><br>
<div style="width:1900px ; overflow:scroll;height:300px">
    <table width="1860" style="padding:10px;" cellspacing="0" border="1" class="rpt_table" rules="all"
        id="table_header_1" >
        <thead style="padding:10px; position:sticky;top:0">
            <th width="40">SL</th>
            <th width="140">Company</th>
            <th width="140">Cust Buyer</th>
            <th width="100">Fso</th>
            <th width="100">Booking</th>
            <th width="200">F/Type</th>
            <th width="100">Color</th>
            <th width="75">Yarn Lot</th>
            <th width="75">GSM</th>
            <th width="75">Stitch Length</th>
            <th width="75">F/Dia</th>
            <th width="75">MC Dia</th>
            <th width="75">Yarn Count</th>
            <th width="75">Yern Composition</th>
            <th width="75">Yarn Brand</th>
            <th width="75">Yarn Type</th>
            <th width="75">Body Part</th>
            <th width="75">Received Qty.</th>
            <th width="100">Issue Returned</th>
            <th width="100">Transfered In</th>
            <th width="100">Transfered Out</th>
            <th width="100">Total Received</th>
            <th width="100">Issue</th>
            <th width="100">Total Issue</th>
            <th width="100">Stock</th>

        </thead>
        <tbody id="tbody2">
   <?php 
        $i=1;
        $final_total_stock_Qty=0;$total_stock_Qty=0;
        foreach($mainArr as $k_job=>$v_job)
        {  
            foreach($v_job as $k_deter=>$v_deter)
            { 
                foreach($v_deter as $k_gsm=>$v_gsm)
                { 
                    foreach($v_gsm as $k_prog_no=>$row)
                    { if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        $booking_qty += $bookingQntyArr[$row['JOB_NO']][$k_deter][$row['GSM_WEIGHT']][$row['FABRIC_COLOR_ID']]['GREY_QNTY_BY_UOM'];
                        $tot_rcv_qnty += $rcvQntyArr[$row['JOB_NO']][$k_deter][$row['GSM_WEIGHT']]['receive_qty'];
                        $issue_rtn_qty += $issueRtnQtyArr[$row['JOB_NO']][$k_deter][$row['GSM_WEIGHT']]['issue_rtn_qty'];
                        $trans_in_qty += $trnsInQtyArr[$row['JOB_NO']][$k_deter][$row['GSM_WEIGHT']]['transfer_in_qnty'];
                        $trans_out_qty += $trnsOutQtyArr[$row['JOB_NO']][$k_deter][$row['GSM_WEIGHT']]['TRANSFER_OUT_QNTY'];
                        $total_rcv_qty_data =( $tot_rcv_qnty + $issue_rtn_qty + $trans_in_qty ) - $trans_out_qty;
                        $rcv_balance = $tot_rcv_qnty-$booking_qty;
                        $issue_qnty += $issueQtyArr[$row['JOB_NO']][$k_deter][$row['GSM_WEIGHT']]['issue_qnty'];
                        $total_current_issue = $issue_qnty - $issue_rtn_qty ;
                        $total_issue_blnc_qty = $booking_qty - $total_current_issue ;
                        $total_stock_Qty = $total_rcv_qty_data - $total_current_issue;
                        $prodIdsArrr = $requArr[$k_prog_no]['prod_id'];
                        $prodIdsArrData = array_unique(explode(", ",chop($prodIdsArrr ,",")));
                        $yarn_count = '';
                        $yarn_lot = '';
                        $yarn_brand = '';
                         $yarn_type=$yarn_comp='';
                           foreach ($prodIdsArrData as $prod_id) 
								{
                                     // $yarn_type = $product_details_array[$prod_id]['type']; 
                                     //  $yarn_comp = $product_details_array[$prod_id]['comp']; 
                                    if( $yarn_type=='')
                                    {
                                        $yarn_type = $product_details_array[$prod_id]['type'];
                                    }
                                    else
									{
										$yarn_type .= ", ".$product_details_array[$prod_id]['type'];
									}
                                     if( $yarn_comp=='')
                                    {
                                        $yarn_comp = $product_details_array[$prod_id]['comp'];
                                    }
                                    else
									{
										$yarn_comp .= ", ".$product_details_array[$prod_id]['comp'];
									}
									if($yarn_count=='')
									{
										$yarn_count = $product_details_array[$prod_id]['count']; 
                                          
									}
									else
									{
										$yarn_count .= ", ".$product_details_array[$prod_id]['count'];
									}

									if($yarn_lot=='')
									{
										$yarn_lot = $product_details_array[$prod_id]['lot']; 
									}
									else
									{
										$yarn_lot .= ', '.$product_details_array[$prod_id]['lot'];
									}

									if($yarn_brand=='')
									{
										$yarn_brand = $product_details_array[$prod_id]['brand']; 
									}
									else
									{
										$yarn_brand .= ', '.$product_details_array[$prod_id]['brand'];
								    }
								}
                           ?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')"
                   id="tr<? echo $i;?>">
            
              <td align="center" width="100">
                    <? echo $i; ?>
                </td>
                <td align="left" width="140">
                    <? echo $company_arr[$row['COMPANY_ID']]; ?>
                </td>
                <td align="left" width="140">
                    <? echo $buyer_arr[$row['CUSTOMER_BUYER']]; ?>
                </td>
                <td align="left" width="140">
                    <? echo $row['JOB_NO']; ?>
                </td>
                <td align="left" width="100">
                    <? echo $row['BOOKING_NO']; ?>
                </td>
                <td align="left" width="200">
                    <p>
                        <? echo $row['FABRIC_DESC'] ?>
                    </p>
                </td>
                <td align="left" width="100">
                    <? echo $row['FABRIC_COLOR'] ?>
                </td>
                <td align="right" width="100">
                    <? echo $yarn_lot ; ?>
                </td>
                <td align="right" width="75">
                   <? echo $row['GSM_WEIGHT']; ?>
                </td>
                <td align="right" width="75">
                    <? echo $row['STITCH_LENGTH']; ?>
                </td>
                <td align="right" width="75">
                <? echo $row['FABRIC_DIA']; ?>  
                
                </td>
                <td align="right" width="75">
                   <? echo $row['MACHINE_DIA']; ?>
                </td>
                <td align="right" width="75">
                    <? echo rtrim($yarn_count,', ');?>
                </td>
                <td align="right" width="75">
                    <? echo $yarn_comp ;?>
                   
                </td>
                <td align="right" width="75">
                    <? echo $yarn_brand ;?>
                  
                </td>
                <td align="right" width="75">
                     <? echo $yarn_type ;?>
                </td>
                <td align="right" width="75">
                   <? echo $bodypart_with_type_arr[$row['BODY_PART_ID']] ?>
                </td>
                <td align="right" width="75">
                   <? echo $tot_rcv_qnty ; ?>
                </td>
                <td align="right" width="75">
                   <? echo $issue_rtn_qty; ?>
                </td>
                <td align="right" width="75">
                    <? echo $trans_in_qty ?>
                </td>
                <td align="right" width="100">
                  <?  echo $trans_out_qty ?>
                </td>
                <td align="right" width="100">
                    <? echo $total_rcv_qty_data ?>
                </td>
                <td align="right" width="100">
                   <? echo $issue_qnty ?>
                </td>
                <td align="right" width="100">
                    <? echo  $total_current_issue ?>
                </td>
                <td align="right" width="100">
                    <? echo $total_stock_Qty ?>
                </td>
            </tr>
            <?  $i++;
             $final_total_stock_Qty += $total_stock_Qty;   
              $final_rcv_total += $total_rcv_qty_data ;
                   $final_rcv_blnc += $rcv_balance ;
                   $final_rcv_issue_qty += $issue_qnty ;
                   $final_total_current_issue += $total_current_issue ;
                   $final_total_issue_blnc_qty += $total_issue_blnc_qty ;
                  }
                  
                }
               
            }
          
                 
        }
        
                  
            ?>
        </tbody>


        <tfoot style="padding:10px;" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
            <tr>
                <th align="right" colspan="21">Total</th>
                <th align="right">
                    <? echo  $final_rcv_total ?>
                </th>
                <th align="right">
                    <? echo  $final_rcv_issue_qty  ?>
                </th>
                <th align="right">
                    <? echo $final_total_issue_blnc_qty  ?>
                </th>
                <th align="right">
                    <? echo $final_total_stock_Qty ?>
                </th>
               
            </tr>
        </tfoot>


    </table>
</div>

<?
  exit();
}
?>