<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------

//library array-------------------
$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
$item_group_arr=return_library_array( "select id, item_name from  lib_item_group",'id','item_name');
if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 122, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data' and b.category_type=4 and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );
	exit();
}

//item search------------------------------//
if($action=="item_description_search")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
    <script>

		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;

    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );

			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( strCon )
		{
				var splitSTR = strCon.split("_");
				var str = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];

				toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );

				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );
					selected_no.push(str);
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
					selected_no.splice( i, 1 );
				}
				var id = ''; var name = ''; var job = ''; var num='';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
					num += selected_no[i] + ',';
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 );
				num 	= num.substr( 0, num.length - 1 );

				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name );
				$('#txt_selected_no').val( num );
		}

		function fn_check_lot()
		{
			show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $company; ?>+'_'+document.getElementById('txt_prod_id').value+'_'+document.getElementById('txt_group_code').value, 'create_lot_search_list_view', 'search_div', 'trims_item_ledger_controller', 'setFilterGrid("list_view",-1)');
		}
		function fn_item_search(str)
		{
			var field_type="";
			$('#search_by_td').html('');
			$('#search_by_td_up').html('');
			if(str==1)
			{
				field_type='<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />';
				$('#search_by_td_up').html('Enter Item Description');
			}
			else if(str==2)
			{
				field_type='<? echo create_drop_down( "txt_search_common", 150,"select id,item_name  from lib_item_group where item_category=4 and status_active=1 and is_deleted=0 order by item_name","id,item_name", 1, "-- Select --", "", "","","","","",""); ?>';
				$('#search_by_td_up').html('Enter Item Group');
			}
			$('#search_by_td').html(field_type);
		}
    </script>
    <body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="550" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>
						<th width="170">Search By</th>
						<th width="100">Group Code</th>
						<th align="center" width="200" id="search_by_td_up">Enter Item Description</th>
                        <th width="100">Product Id</th>
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
						<td align="center">
							<?
								$search_by = array(1=>'Item Description', 2=>'Item Group');
								$dd="";
								echo create_drop_down( "cbo_search_by", 150, $search_by, "", 0, "--Select--", "", "fn_item_search(this.value);", 0);
							?>
						</td>
						<td align="center">
							<input type="text" style="width:80px" class="text_boxes"  name="txt_group_code" id="txt_group_code" placeholder="Write"/>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
                        <td align="center">
							<input type="text" style="width:80px" class="text_boxes_numeric"  name="txt_prod_id" id="txt_prod_id" />
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
	exit();
}

if($action=="create_lot_search_list_view")
{
 	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$company = $ex_data[2];
	$prod_id = $ex_data[3];
	$group_code = $ex_data[4];
	//echo $group_code;die;

	$sql_cond="";
	// if(trim($txt_search_common)!="")
	// {
	// 	if(trim($txt_search_by)==1) // for LOT NO
	// 	{
	// 		$sql_cond= " and product_name_details LIKE '%$txt_search_common%'";
 	// 	}
	// 	else if(trim($txt_search_by)==2) // for Yarn Count
	// 	{
	// 		if($txt_search_common==0)
	// 		{
	// 		$sql_cond= " ";
	// 		}
	// 		else
	// 		{
	// 		$sql_cond= " and item_group_id LIKE '%$txt_search_common%'";
	// 		}
 	// 	}
 	// }

	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==1) // for LOT NO
		{
			$sql_cond= " and a.product_name_details LIKE '%$txt_search_common%'";
 		}
		else if(trim($txt_search_by)==2) // for Yarn Count
		{
			if($txt_search_common==0)
			{
			$sql_cond= " ";
			}
			else
			{
			$sql_cond= " and a.item_group_id LIKE '%$txt_search_common%'";
			}
 		}
 	}
	
	if($prod_id) $sql_cond.=" and a.id = $prod_id";
	if($group_code) $sql_cond.= " and b.item_group_code LIKE '%$group_code%'";
	//and status_active=1 and is_deleted=0  
	$sql= "select a.id, a.item_category_id,a.item_group_id,a.item_description, b.item_name,b.item_group_code from product_details_master a, lib_item_group b where a.item_group_id = b.id and b.is_deleted=0 and b.status_active=1 and  a.company_id=$company and a.item_category_id =4 $sql_cond";
	
 	// $sql = "select id,item_group_id,item_description from product_details_master where company_id=$company and item_category_id =4 $sql_cond";
	// echo $sql;
	$arr=array(1=>$item_group_arr);
	echo create_list_view("list_view", "Product Id, Item Group, Item Description","70,160","500","260",0, $sql , "js_set_value", "id,item_description", "", 1, "0,item_group_id,0", $arr, "id,item_group_id,item_description", "","","0","",1) ;

	exit();
}

//report generated here--------------------//
if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//echo $rpt_type.tet;die;
	//print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));
	$search_cond="";
	if($db_type==0)
	{
 		if( $from_date!="" && $to_date!="" ) $search_cond .= " and a.transaction_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
	}
	else
	{
		if($from_date!="" && $to_date!="") $search_cond .= " and a.transaction_date  between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
	}


	$cbo_store_name=str_replace("'","",$cbo_store_name);
	if($cbo_store_name>0) $store_cond=" and a.store_id=$cbo_store_name";
 	// receive MRR array------------------------------------------------
	$pord_ids = explode(",",$txt_product_id);
	//echo count($pord_ids);die;
	if(count($pord_ids)>0)
	{
		$prod_cond=" and (";
		if(count($pord_ids)>999 && $db_type==2)
		{
			$prod_id_chank=array_chunk($pord_ids,999);
			foreach($prod_id_chank as $prod_id)
			{
				$prod_cond.=" a.prod_id in(".implode(",",$prod_id).") or";
			}
			$prod_cond=chop($prod_cond,"or");
		}
		else
		{
			//die("with naz");
			$prod_cond.=" a.prod_id in(".implode(",",$pord_ids).")";
		}
		$prod_cond.=")";

	}

	$sql_receive_mrr = "select a.id as trid, a.transaction_type, b.recv_number, b.supplier_id, b.challan_no
			from inv_transaction a, inv_receive_master b
			where a.mst_id=b.id and a.transaction_type in (1,4) and a.item_category=4 $prod_cond $store_cond"; //a.prod_id in ($txt_product_id) and
			// echo $sql_receive_mrr;die;
	$result_rcv = sql_select($sql_receive_mrr);
	$receiveMRR=array();
	$receiveChallan=array();
	$rcvSupplier=array();
	foreach($result_rcv as $row)
	{
		$receiveMRR[$row[csf("trid")]."##".$row[csf("transaction_type")]] = $row[csf("recv_number")];
		$receiveChallan[$row[csf("trid")]."##".$row[csf("transaction_type")]] = $row[csf("challan_no")];
		$rcvSupplier[$row[csf("trid")]] = $row[csf("supplier_id")];
	}
	

	// issue MRR array------------------------------------------------
	
	$sql_issue_mrr = "select a.id as trid,a.transaction_type,b.issue_number,b.issue_purpose, b.challan_no
			from inv_transaction a, inv_issue_master b
			where a.mst_id=b.id and a.transaction_type in (2,3) and a.item_category=4 $prod_cond $store_cond"; //a.prod_id in ($txt_product_id) and 
	// echo $sql_issue_mrr;
	$result_iss = sql_select($sql_issue_mrr);
	$issueMRR=array();$issuePupose=array();$issueChallan=array();
	foreach($result_iss as $row)
	{
		$issueMRR[$row[csf("trid")]."##".$row[csf("transaction_type")]] = $row[csf("issue_number")];
		$issueChallan[$row[csf("trid")]."##".$row[csf("transaction_type")]] = $row[csf("challan_no")];
		$issuePupose[$row[csf("trid")]] = $yarn_issue_purpose[$row[csf("issue_purpose")]];
	}
	
	$sql_transfer = "select a.id as trid,a.transaction_type,b.transfer_system_id, b.company_id, b.to_company, b.challan_no
			from inv_transaction a, inv_item_transfer_mst b
			where a.mst_id=b.id and a.transaction_type in (5,6) and a.item_category=4 and b.transfer_criteria in(1,2,4) $prod_cond $store_cond";
			// echo $sql_transfer;		 
	$result_trans = sql_select($sql_transfer);
	$transferMRR=array();$trWiseTransferWith=array();$transferChallan=array();
	foreach($result_trans as $row)
	{
		$transferMRR[$row[csf("trid")]."##".$row[csf("transaction_type")]] = $row[csf("transfer_system_id")];
		$transferChallan[$row[csf("trid")]."##".$row[csf("transaction_type")]] = $row[csf("challan_no")];
		if($row[csf("transaction_type")]==5)
		{
			$trWiseTransferWith[$row[csf("trid")]."##".$row[csf("transaction_type")]] = $row[csf("company_id")]; 
		}
		else
		{
			$trWiseTransferWith[$row[csf("trid")]."##".$row[csf("transaction_type")]] = $row[csf("to_company")]; 
		}
	} 

	 //var_dump($issueMRR);
	// var_dump($issuePupose);

	//array join or merge here ------------- do not delete or change
	$mrrArray = array();
	$mrrArray = $receiveMRR+$issueMRR+$transferMRR;
	$challanArray = array();
	$challanArray = $receiveChallan+$issueChallan+$transferChallan;
	if( $from_date!="" && $to_date!="" )
	{
		if($db_type==2) $from_date=date("j-M-Y",strtotime($from_date));
		if($db_type==0) $from_date=change_date_format($from_date, 'yyyy-mm-dd');
		//for opening balance
		$sqlTR = "select a.prod_id, SUM(CASE WHEN a.transaction_type in (1,4,5) THEN a.cons_quantity ELSE 0 END) as receive,
		SUM(CASE WHEN a.transaction_type in (2,3,6) THEN a.cons_quantity ELSE 0 END) as issue,
		SUM(CASE WHEN a.transaction_type in (1,4,5) THEN a.cons_amount ELSE 0 END) as rcv_balance,
		SUM(CASE WHEN a.transaction_type in (2,3,6) THEN a.cons_amount ELSE 0 END) as iss_balance
		from inv_transaction a
		where a.transaction_date < '".$from_date."' and a.status_active=1 and a.is_deleted=0 $prod_cond $store_cond group by prod_id";
		$trResult = sql_select($sqlTR);
	}
	$opning_bal_arr=array();
	foreach($trResult as $row)
	{
		$opning_bal_arr[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
		$opning_bal_arr[$row[csf("prod_id")]]["receive"]=$row[csf("receive")];
		$opning_bal_arr[$row[csf("prod_id")]]["issue"]=$row[csf("issue")];
		$opning_bal_arr[$row[csf("prod_id")]]["rcv_balance"]=$row[csf("rcv_balance")];
		$opning_bal_arr[$row[csf("prod_id")]]["iss_balance"]=$row[csf("iss_balance")];
	}
	//var_dump($opning_bal_arr);die;
	if($rpt_type==3)
	{
		if($cbo_method==0) //average rate #########################################################################
		{
			$sql = "select a.id, a.prod_id, a.transaction_date, a.receive_basis, a.insert_date, a.transaction_type, a.cons_quantity, a.cons_rate, a.cons_amount, b.product_name_details, b.unit_of_measure, b.item_group_id, b.lot, c.knit_dye_source, c.knit_dye_company, c.issue_purpose
			from product_details_master b, inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3)
			where a.prod_id=b.id and a.status_active=1 and b.status_active=1 and a.transaction_type in (1,2,3,4,5,6) and a.is_deleted=0and a.item_category=4 $search_cond $prod_cond $store_cond 
			order by a.prod_id, a.insert_date, a.id ASC";
	
		}
		else if($cbo_method==1) //FIFU #########################################################################
		{
			$sql = "select a.id, a.prod_id, a.transaction_date, a.receive_basis, a.insert_date, a.transaction_type, a.cons_quantity, a.cons_rate, a.cons_amount, b.product_name_details, b.unit_of_measure, b.item_group_id, b.lot, c.knit_dye_source, c.knit_dye_company, c.issue_purpose
			from  product_details_master b, inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3)
			where a.prod_id=b.id and a.status_active=1 and b.status_active=1 and a.transaction_type in (1,2,3,4,5,6) and a.is_deleted=0 and a.item_category=4 $search_cond $prod_cond $store_cond 
			order by a.prod_id, a.insert_date, a.id ASC";
	
		}
		else if($cbo_method==2) //LIFU #########################################################################
		{
			$sql = "select a.id, a.prod_id, a.transaction_date, a.receive_basis, a.insert_date, a.transaction_type, a.cons_quantity, a.cons_rate, a.cons_amount, b.product_name_details, b.unit_of_measure, b.item_group_id, b.lot, c.knit_dye_source, c.knit_dye_company, c.issue_purpose
			from product_details_master b , inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3)
			where a.prod_id=b.id and a.status_active=1 and b.status_active=1 and a.transaction_type in (1,2,3,4,5,6) and a.is_deleted=0 and a.item_category=4 $search_cond $prod_cond $store_cond 
			order by a.prod_id, a.insert_date, a.id ASC";
		}
	}
	else
	{
		if($cbo_method==0) //average rate #########################################################################
		{
			$sql = "select a.id, a.prod_id, a.transaction_date, a.receive_basis, a.insert_date, a.transaction_type, a.cons_quantity, a.cons_rate, a.cons_amount, b.product_name_details, b.unit_of_measure, b.item_group_id, b.lot, c.knit_dye_source, c.knit_dye_company, c.issue_purpose
			from product_details_master b, inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3)
			where a.prod_id=b.id and a.status_active=1 and b.status_active <> 1 and a.transaction_type in (1,2,3,4,5,6) and a.is_deleted=0and a.item_category=4 $search_cond $prod_cond $store_cond 
			order by a.prod_id, a.insert_date, a.id ASC";
	
		}
		else if($cbo_method==1) //FIFU #########################################################################
		{
			$sql = "select a.id, a.prod_id, a.transaction_date, a.receive_basis, a.insert_date, a.transaction_type, a.cons_quantity, a.cons_rate, a.cons_amount, b.product_name_details, b.unit_of_measure, b.item_group_id, b.lot, c.knit_dye_source, c.knit_dye_company, c.issue_purpose
			from  product_details_master b, inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3)
			where a.prod_id=b.id and a.status_active=1 and b.status_active <> 1 and a.transaction_type in (1,2,3,4,5,6) and a.is_deleted=0 and a.item_category=4 $search_cond $prod_cond $store_cond 
			order by a.prod_id, a.insert_date, a.id ASC";
	
		}
		else if($cbo_method==2) //LIFU #########################################################################
		{
			$sql = "select a.id, a.prod_id, a.transaction_date, a.receive_basis, a.insert_date, a.transaction_type, a.cons_quantity, a.cons_rate, a.cons_amount, b.product_name_details, b.unit_of_measure, b.item_group_id, b.lot, c.knit_dye_source, c.knit_dye_company, c.issue_purpose
			from product_details_master b , inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3)
			where a.prod_id=b.id and a.status_active=1 and b.status_active <> 1 and a.transaction_type in (1,2,3,4,5,6) and a.is_deleted=0 and a.item_category=4 $search_cond $prod_cond $store_cond 
			order by a.prod_id, a.insert_date, a.id ASC";
		}
	}


	// echo $sql;die;
	$result = sql_select($sql);
	$checkItemArr=array();
	$balQnty=$balValue=array();
	$rcvQnty=$rcvValue=$issQnty=$issValue=0;
	$i=1;
	ob_start();
	?>
	<style>
		.wrd_brk{word-break: break-all;}
	</style>

	<fieldset style="width:1530px">
	<div style="width:1520px">
    <table style="width:1500px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left" >
        <thead>
            <tr class="form_caption" style="border:none;">
            	<td colspan="15" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?> </td>
            </tr>
            <tr style="border:none;">
                <td colspan="15" align="center" style="border:none; font-size:14px;">
                Company Name : <? echo $companyArr[str_replace("'","",$cbo_company_name)]; ?>
                </td>
            </tr>
            <tr style="border:none;">
                <td colspan="15" align="center" style="border:none;font-size:12px; font-weight:bold">
                <? if($from_date!="" || $to_date!="")echo "From ".change_date_format($from_date)." To ".change_date_format($to_date)."" ;?>
                </td>
            </tr>
            <tr>
                <td colspan="7">&nbsp;</td>
                <td colspan="9" align="center"><b>Weighted Average Method</b></td>
            </tr>
            <tr>
                <th width="50" rowspan="2">SL</th>
                <th width="80" rowspan="2">Trans Date</th>
                <th width="120" rowspan="2">Trans Ref No</th>
                <th width="100" rowspan="2">Challan No</th>
                <th width="100" rowspan="2">Trans Type</th>
                <th width="100" rowspan="2">Purpose</th>
                <th width="100" rowspan="2">Trans With</th>
                <th width="" colspan="3">Receive</th>
                <th width="" colspan="3">Issue</th>
                <th width="" colspan="3">Balance</th>
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
                <th width="">Value</th>
            </tr>
        </thead>
    </table>
    <div style="width:1520px; overflow-y:scroll; max-height:250px" id="scroll_body" align="left" >
    <table style="width:1500px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left" >
		<?		
        $m=1;$product_id_arr=array();$k=1;
        foreach($result as $row)
        {
			$pro_id=$row[csf("prod_id")];
			//check items new or not and print product description-------------------
			if(!in_array($row[csf("prod_id")],$checkItemArr))
			{
				if($i!=1) // product wise sum/total here------------
				{
				?>
				<tr class="tbl_bottom">
                    <td colspan="7" align="right">Total</td>
                    <td class="wrd_brk"><? echo number_format($rcvQnty,2); ?></td><td></td><td class="wrd_brk"><? echo number_format($rcvValue,2); ?></td>
                    <td class="wrd_brk"><? echo number_format($issQnty,2); ?></td><td></td><td class="wrd_brk"><? echo number_format($issValue,2); ?></td>
                    <td>&nbsp;</td><td></td><td>&nbsp;</td>
				</tr>
				
				<!-- product wise herder -->
				<thead>
                    <tr>
                        <td colspan="10" class="wrd_brk"><b>Product ID : <? echo $row[csf("prod_id")]." , ".$row[csf("product_name_details")].", Item Group# ".$item_group_arr[$row[csf("item_group_id")]].", UOM# ".$unit_of_measurement[$row[csf("unit_of_measure")]]; ?></b></td>
                        <td colspan="6" align="center">&nbsp;</td>
                    </tr>
				</thead>
				<!-- product wise herder END -->
				<?
				}
				$flag=0;
				$opening_qnty=$opening_balance=$opening_rate=0;
				if($opning_bal_arr[$pro_id]['prod_id']!="")
				{
					?>
					<tr style="background-color:#FFFFCC">
                        <td colspan="12" align="right"><b>Opening Balance</b></td>
                        <?
                        $opening_qnty = $opning_bal_arr[$pro_id]['receive']- $opning_bal_arr[$pro_id]['issue'];
                        $opening_balance = $opning_bal_arr[$pro_id]['rcv_balance'] - $opning_bal_arr[$pro_id]['iss_balance'];
                        $opening_rate = $opening_balance/$opening_qnty;
                        ?>
                        <td width="80" align="right" class="wrd_brk"><? echo number_format($opening_qnty,2); ?></td>
                        <td width="60" align="right" class="wrd_brk"><? echo number_format($opening_rate,2); ?></td>
                        <td width="" align="right"  class="wrd_brk"><? echo number_format($opening_balance,2); ?></td>
					</tr>
					<?
					$balQnty[$opning_bal_arr[$pro_id]['prod_id']] = $opening_qnty;
					$balValue [$opning_bal_arr[$pro_id]['prod_id']]= $opening_balance;
					$flag=1; $opening_qnty=0; $opening_balance=0;
				} // end opening balance foreach
				
				$checkItemArr[$row[csf("prod_id")]]=$row[csf("prod_id")];
				$rcvQnty=$rcvValue=$issQnty=$issValue=0; // initialize variable
				//$balQnty=$balValue=0;
				$total_balQnty=0;$total_balValue=0;
			}
			//print product name details header---------------------------
			if($i==1)
			{
				?>
				<thead>
                    <tr>
                        <td colspan="10" class="wrd_brk"><b>Product ID : <? echo $row[csf("prod_id")]." , ".$row[csf("product_name_details")].", Item Group# ".$item_group_arr[$row[csf("item_group_id")]].", UOM# ".$unit_of_measurement[$row[csf("unit_of_measure")]]; ?></b></td>
                        <td colspan="6" align="center"></td>
                    </tr>
				</thead>
				<?
			}
			
			if ($i%2==0)$bgcolor="#E9F3FF";	else $bgcolor="#FFFFFF";
			if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6)
				$stylecolor='style="color:#A61000"';
			else
				$stylecolor='style="color:#000000"';
			if(!in_array($row[csf("prod_id")],$product_id_arr))
			{
				$k=1;
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="50"><? echo $k; ?></td>
                    <td width="80" class="wrd_brk"><p><? if($row[csf("transaction_date")] !="" && $row[csf("transaction_date")] !="0000-00-00")  echo change_date_format($row[csf("transaction_date")]); ?>&nbsp;</p></td>
                    <td width="120" class="wrd_brk"><p>
					<?
                    if( $row[csf("mst_id")]==0 && $row[csf("receive_basis")]==30)
                    {
                        echo "Adjustment"; 
                    }
                    else
                    {
                        echo $mrrArray[$row[csf("id")]."##".$row[csf("transaction_type")]]; 
                    } 
                    ?></p></td>
                    <td width="100" class="wrd_brk"><p>
					<?
                    if( $row[csf("mst_id")]==0 && $row[csf("receive_basis")]==30)
                    {
                        echo "Adjustment"; 
                    }
                    else
                    {
                        echo $challanArray[$row[csf("id")]."##".$row[csf("transaction_type")]]; 
                    } 
                    ?></p></td>
                    <td width="100" class="wrd_brk"><p><? echo $transaction_type[$row[csf("transaction_type")]]; ?></p></td>
                    <td width="100" class="wrd_brk"><p><? echo $issuePupose[$row[csf("id")]]; ?></p></td>
                    <?
                    if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 )
                    {
						if($row[csf("knit_dye_source")]==1)
							$transactionWith =  $companyArr[$row[csf("knit_dye_company")]];
						else
							$transactionWith =  $supplierArr[$row[csf("knit_dye_company")]];
                    }
                    else
                    {
                    	$transactionWith =  $supplierArr[$rcvSupplier[$row[csf("id")]]];
                    }
                    ?>
                    <td width="100" class="wrd_brk"><p><? echo $transactionWith; ?></p></td>
                    <td width="80" align="right" class="wrd_brk"><? if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) echo number_format($row[csf("cons_quantity")],2); ?></td>
                    <td width="80" align="right" class="wrd_brk" title="<? if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) echo $row[csf("cons_rate")]; ?>"><? if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) echo number_format($row[csf("cons_rate")],2); ?></td>
                    <td width="110" align="right" class="wrd_brk"><? if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4|| $row[csf("transaction_type")]==5) echo number_format($row[csf("cons_amount")],2); ?></td>
                    
                    <td width="80" align="right" class="wrd_brk"><? if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) echo number_format($row[csf("cons_quantity")],2); ?></td>
                    <td width="80" align="right" class="wrd_brk" title="<? if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) echo $row[csf("cons_rate")]; ?>"><? if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) echo number_format($row[csf("cons_rate")],2); ?></td>
                    <td width="110" align="right" class="wrd_brk"><? if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) echo number_format($row[csf("cons_amount")],2); ?></td>
                    <?
                    $each_pro_id=array();
                    
                    
                    if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) $total_balQnty =number_format($balQnty[$row[csf("prod_id")]],8,'.','')+ number_format(trim($row[csf("cons_quantity")]),8,'.','');
                    if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) $total_balQnty =number_format($balQnty[$row[csf("prod_id")]],8,'.','')-number_format(trim($row[csf("cons_quantity")]),8,'.','');
                    
                    if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5)  $total_balValue =number_format($balValue[$row[csf("prod_id")]],8,'.','')+ number_format(trim($row[csf("cons_amount")]),8,'.','');
                    if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6)  $total_balValue =number_format($balValue[$row[csf("prod_id")]],8,'.','')- number_format(trim($row[csf("cons_amount")]),8,'.','');
                    
					if($total_balQnty==0)
					{
						$total_balValue=0;
						$bal_rate=0;
					}
					else
					{
						if(number_format($total_balValue,2)!=0 && number_format($total_balQnty,2)!=0) $bal_rate=$total_balValue/$total_balQnty; else $bal_rate=0;
					}
					
					/*if(number_format($total_balValue,2)!=0 && number_format($total_balQnty,2)!=0)
					{
						$bal_rate=$total_balValue/$total_balQnty;
					}
					else
					{
						$bal_rate=0;
					}*/
					
                    ?>
                    <td width="80" align="right" class="wrd_brk"><? echo number_format($total_balQnty,2); ?></td>
                    <td width="80" align="right" class="wrd_brk" title="<? echo $total_balValue/$total_balQnty; ?>"><? echo number_format($bal_rate,2); ?></td>
                    <td width="" align="right" class="wrd_brk" style="word-break: break-all;"><? echo number_format($total_balValue,2); ?></td>
				</tr>
				<?
				$k++;
				$product_id_arr[]=$row[csf("prod_id")];
			}
			else
			{
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="50"><? echo $k; ?></td>
                    <td width="80" class="wrd_brk"><p><? if($row[csf("transaction_date")] !="" && $row[csf("transaction_date")] !="0000-00-00")  echo change_date_format($row[csf("transaction_date")]); ?>&nbsp;</p></td>
                    <td width="120" class="wrd_brk"><p>
					<?
                    if( $row[csf("mst_id")]==0 && $row[csf("receive_basis")]==30)
                    {
                        echo "Adjustment"; 
                    }
                    else
                    {
                        echo $mrrArray[$row[csf("id")]."##".$row[csf("transaction_type")]]; 
                    } 
                    ?></p></td>
                    <td width="100" class="wrd_brk"><p>
					<?
                    if( $row[csf("mst_id")]==0 && $row[csf("receive_basis")]==30)
                    {
                        echo "Adjustment"; 
                    }
                    else
                    {
                        echo $challanArray[$row[csf("id")]."##".$row[csf("transaction_type")]]; 
                    } 
                    ?></p></td>
                    <td width="100" class="wrd_brk"><p><? echo $transaction_type[$row[csf("transaction_type")]]; ?></p></td>
                    <td width="100" class="wrd_brk"><p><? echo $issuePupose[$row[csf("id")]]; ?></p></td>
                    <?
                    if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 )
                    {
						if($row[csf("knit_dye_source")]==1)
							$transactionWith =  $companyArr[$row[csf("knit_dye_company")]];
						else
							$transactionWith =  $supplierArr[$row[csf("knit_dye_company")]];
                    }
                    else
                    {
                    	$transactionWith =  $supplierArr[$rcvSupplier[$row[csf("id")]]];
                    }
                    ?>
                    <td width="100" class="wrd_brk"><p><? echo $transactionWith; ?></p></td>
                    <td width="80" align="right" class="wrd_brk"><? if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) echo number_format($row[csf("cons_quantity")],2); ?></td>
                    <td width="80" align="right" class="wrd_brk" title="<? if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) echo $row[csf("cons_rate")]; ?>"><? if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) echo number_format($row[csf("cons_rate")],2); ?></td>
                    <td width="110" align="right" class="wrd_brk"><? if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) echo number_format($row[csf("cons_amount")],2); ?></td>
                    
                    <td width="80" align="right" class="wrd_brk"><? if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) echo number_format($row[csf("cons_quantity")],2); ?></td>
                    <td width="80" align="right" class="wrd_brk" title="<? if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) echo $row[csf("cons_rate")]; ?>"><? if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) echo number_format($row[csf("cons_rate")],2); ?></td>
                    <td width="110" align="right" class="wrd_brk"><? if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) echo number_format($row[csf("cons_amount")],2); ?></td>
                    <?
                    $each_pro_id=array();
                    
                    
                    if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) $total_balQnty =number_format($total_balQnty,8,'.','') +  number_format(trim($row[csf("cons_quantity")]),8,'.','');
                    if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) $total_balQnty = number_format($total_balQnty,8,'.','') -  number_format(trim($row[csf("cons_quantity")]),8,'.','');
                    
                    if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5)  $total_balValue = number_format($total_balValue,8,'.','') +  number_format(trim($row[csf("cons_amount")]),8,'.','');
                    if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6)  $total_balValue = number_format($total_balValue,8,'.','') - number_format(trim($row[csf("cons_amount")]),8,'.','');
                    
                    if($total_balQnty==0)
					{
						$total_balValue=0;
						$bal_rate=0;
					}
					else
					{
						if(number_format($total_balValue,2)!=0 && number_format($total_balQnty,2)!=0) $bal_rate=$total_balValue/$total_balQnty; else $bal_rate=0;
					}
					
					//if(number_format($total_balValue,2)!=0 && number_format($total_balQnty,2)!=0)
//					{
//						$bal_rate=$total_balValue/$total_balQnty;
//					}
//					else
//					{
//						$bal_rate=0;
//					}
                    ?>
                    <td width="80" align="right" class="wrd_brk"><? echo number_format($total_balQnty,2); ?></td>
                    <td width="80" align="right" class="wrd_brk" title="<? echo $total_balValue/$total_balQnty; ?>"><? echo number_format($bal_rate,2); ?></td>
                    <td width="" align="right" class="wrd_brk"><? echo number_format($total_balValue,2); ?></td>
				</tr>
				<?
				$k++;
			}
			$i++;
			
			//total sum START-----------------------
			if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) $rcvQnty += $row[csf("cons_quantity")];
			if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) $rcvValue += $row[csf("cons_amount")];
			
			if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) $issQnty += $row[csf("cons_quantity")];
			if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) $issValue += $row[csf("cons_amount")];
        } 
        ?> 
        <tr class="tbl_bottom">
            <td colspan="7" align="right">Total</td>
            <td align="right" class="wrd_brk" ><? echo number_format($rcvQnty,2); ?></td><td></td><td align="right" class="wrd_brk" ><? echo number_format($rcvValue,2); ?></td>
            <td align="right" class="wrd_brk" ><? echo number_format($issQnty,2); ?></td><td></td><td align="right" class="wrd_brk" ><? echo number_format($issValue,2); ?></td>
            <td>&nbsp;</td><td></td><td>&nbsp;</td>
        </tr>
    </table>
    </div>
	</div>
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
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename";
	exit();

}

?>
