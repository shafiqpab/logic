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

if($action=="set_print_button")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=6 and report_id=274 and is_deleted=0 and status_active=1");
	echo "print_report_button_setting('$print_report_format');\n"; 
die;
}


if($action=="party_search_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	//echo $company;die;
	?>
    <script>

		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ )
			{
				var onclickString = $('#tr_' + i).attr('onclick');
				//alert(onclickString);return;
				var paramArr = onclickString.split("'");
				//alert(paramArr);return;
				var functionParam = paramArr[1];
				//alert(functionParam);return;
				js_set_value( functionParam );
			}
		}

		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style )
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( strCon )
		{
			//alert(strCon);
				var splitSTR = strCon.split("_");
				var str_or = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				toggle( document.getElementById( 'tr_' + str_or ), '#FFFFCC' );

				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );
					selected_no.push( str_or );
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
				//alert(num);
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name );
				$('#txt_selected_no').val( num );
		}
    </script>
    <?
	$company=str_replace("'","",$company);
	$sql = "select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b
	where a.id=b.supplier_id and b.tag_company=$company and a.status_active=1 and a.is_deleted=0 and a.id in(select supplier_id from lib_supplier_party_type where party_type=91) order by supplier_name";
	//echo $sql; die;
	echo create_list_view("list_view", "Loan Party Name","300","350","310",0, $sql , "js_set_value", "id,supplier_name", "", 1, "0", $arr, "supplier_name", "","setFilterGrid('list_view',-1)","0","",1) ;
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";

	?>
    <script language="javascript" type="text/javascript">
	var txt_loan_party_no='<? echo $txt_loan_party_no;?>';
	var txt_loan_party_id='<? echo $txt_loan_party_id;?>';
	var txt_loan_party_name='<? echo $txt_loan_party_name;?>';
	//alert(style_id);
	if(txt_loan_party_no!="")
	{
		loan_party_no_arr=txt_loan_party_no.split(",");
		loan_party_id_arr=txt_loan_party_id.split(",");
		loan_party_name_arr=txt_loan_party_name.split(",");
		var loan_pary_ref="";
		for(var k=0;k<loan_party_no_arr.length; k++)
		{
			loan_pary_ref=loan_party_no_arr[k]+'_'+loan_party_id_arr[k]+'_'+loan_party_name_arr[k];
			js_set_value(loan_pary_ref);
		}
	}
	</script>

    <?

	exit();
}

if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 100, "select a.id, a.store_name from lib_store_location a,lib_store_location_category b  where a.id=b.store_location_id and a.company_id='$data' and b.category_type in(5,6,7) and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "" );
	exit();
}

//report generated here--------------------//
if($action=="generate_report")
{

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_loan_party_id=str_replace("'","",$txt_loan_party_id);
	$cbo_item_cat=str_replace("'","",$cbo_item_cat);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_store_id=str_replace("'",'',$cbo_store_name);
	if($cbo_store_id!=0) $store_cond=" and b.store_id in($cbo_store_id)"; else $store_cond='';
	
	$search_cond="";
	if($db_type==0)
	{
 		if( $txt_date_from!="" && $txt_date_to!="" ) $search_cond .= " and b.transaction_date  between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
	}
	else
	{
		if($txt_date_from!="" && $txt_date_to!="") $search_cond .= " and b.transaction_date  between '".change_date_format($txt_date_from,'','',-1)."' and '".change_date_format($txt_date_to,'','',-1)."'";
	}

	if($cbo_item_cat>0) $search_cond .= " and b.item_category=$cbo_item_cat";


	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$item_group_arr = return_library_array("select id, item_name from lib_item_group","id","item_name");
	$sub_group_arr = return_library_array("select item_group_id, sub_group_name from product_details_master","item_group_id","sub_group_name");

	$item_sql= sql_select("select id, product_name_details, item_group_id,item_description from product_details_master where item_category_id in(5,6,7,22,23) and status_active=1 and is_deleted=0");
	$item_descrip_arr=array();
	foreach($item_sql as $row)
	{
		$item_descrip_arr[$row[csf("id")]]["product_name_details"]=$row[csf("item_description")];
		$item_descrip_arr[$row[csf("id")]]["item_group_id"]=$row[csf("item_group_id")];
	}

	$store_name_arr = return_library_array("select id, store_name from lib_store_location where status_active=1 and is_deleted=0","id","store_name");
	$sql="select a.id as mst_id,b.id as tran_id, a.company_id, a.loan_party, a.recv_number as rcv_issue_no, a.challan_no, a.gate_entry_no as gate_entry_no, null as gate_pass_no, b.id as trans_id, b.item_category, b.transaction_type, b.transaction_date, b.prod_id, b.cons_quantity,b.cons_rate,b.remarks, 1 as type ,b.store_id
	from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.receive_purpose=5 and a.loan_party<>0 and a.ref_closing_status <>1 and a.company_id=$cbo_company_name and a.loan_party in($txt_loan_party_id) and b.transaction_type=1 and b.item_category in (5,6,7,22,23) $search_cond $store_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	union all
	select a.id as mst_id,b.id, a.company_id, a.loan_party, a.issue_number as rcv_issue_no, a.challan_no, null  as gate_entry_no, a.gate_pass_no, b.id as trans_id, b.item_category, b.transaction_type, b.transaction_date, b.prod_id, b.cons_quantity,b.cons_rate,b.remarks, 2 as type ,b.store_id
	from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.issue_purpose=5 and a.loan_party<>0 and a.company_id=$cbo_company_name and a.loan_party in($txt_loan_party_id) and b.transaction_type=2 and b.item_category in (5,6,7,22,23) $search_cond $store_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	order by loan_party,prod_id, tran_id";  //rcv_issue_no,loan_party,prod_id, mst_id ASC

	//echo $sql;
	$sql_data=sql_select($sql);
	$all_party_data=array();
	foreach($sql_data as $row)
	{
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['mst_id']=$row[csf("mst_id")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['trans_id']=$row[csf("trans_id")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['company_id']=$row[csf("company_id")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['loan_party']=$row[csf("loan_party")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['rcv_issue_no']=$row[csf("rcv_issue_no")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['challan_no']=$row[csf("challan_no")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['gate_entry_no']=$row[csf("gate_entry_no")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['gate_pass_no']=$row[csf("gate_pass_no")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['item_category']=$row[csf("item_category")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['transaction_type']=$row[csf("transaction_type")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['transaction_date']=$row[csf("transaction_date")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['prod_id']=$row[csf("prod_id")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['cons_quantity']=$row[csf("cons_quantity")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['cons_rate']=$row[csf("cons_rate")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['remarks']=$row[csf("remarks")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['type']=$row[csf("type")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['store_id']=$row[csf("store_id")];
	}



	if($txt_date_from!="")
	{
		$opening_sql = "select a.loan_party, b.prod_id, sum(case when b.transaction_date<'".$txt_date_from."' then b.cons_quantity else 0 end) as opening_cons_qnty, sum(case when b.transaction_date<'".$txt_date_from."' then b.cons_quantity*b.cons_rate else 0 end) as opening_cons_qnty_value, 1 as type from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.receive_purpose=5 and a.loan_party<>0 and a.ref_closing_status <> 1 and a.company_id=$cbo_company_name and a.loan_party in($txt_loan_party_id) and b.transaction_type=1 and b.item_category in (5,6,7,22,23) $search_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by loan_party, prod_id
		union all
		select a.loan_party, b.prod_id, sum(case when b.transaction_date<'".$txt_date_from."' then b.cons_quantity else 0 end) as opening_cons_qnty, sum(case when b.transaction_date<'".$txt_date_from."' then b.cons_quantity*b.cons_rate else 0 end) as opening_cons_qnty_value, 2 as type from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.issue_purpose=5 and a.loan_party<>0 and a.company_id=$cbo_company_name and a.loan_party in($txt_loan_party_id) and b.transaction_type=2 and b.item_category in (5,6,7,22,23) $search_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by loan_party,prod_id order by loan_party, prod_id";

	}
	else
	{
		 $opening_sql = "select a.loan_party, b.prod_id, 0 as opening_cons_qnty,0 as opening_cons_qnty_value, 1 as type from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.receive_purpose=5 and a.loan_party<>0 and a.ref_closing_status <> 1 and a.company_id=$cbo_company_name and a.loan_party in($txt_loan_party_id) and b.transaction_type=1 and b.item_category in (5,6,7,22,23) $search_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by loan_party, prod_id
		union all
		select a.loan_party, b.prod_id, 0 as opening_cons_qnty, 0 as opening_cons_qnty_value, 2 as type from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.issue_purpose=5 and a.loan_party<>0 and a.company_id=$cbo_company_name and a.loan_party in($txt_loan_party_id) and b.transaction_type=2 and b.item_category in (5,6,7,22,23) $search_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by loan_party,prod_id order by loan_party, prod_id";
		//$result_opening=sql_select($openning_sql);
	}
	//echo $opening_sql;
	$result_opening=sql_select($opening_sql);
	$opening_data=array();$opening_taken=$opening_given=0;
	foreach($result_opening as $row)
	{
		if($row[csf("type")]==1) $opening_taken=$row[csf("opening_cons_qnty")]; else $opening_given=$row[csf("opening_cons_qnty")];
		if($row[csf("type")]==1) $opening_taken_value=$row[csf("opening_cons_qnty_value")]; else $opening_given_value=$row[csf("opening_cons_qnty_value")];

		$opening_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['opening_taken']=$opening_taken;
		$opening_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['opening_given']=$opening_given;
		
		$opening_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['opening_taken_value']=$opening_taken_value;
		$opening_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['opening_given_value']=$opening_given_value;
		
		$opening_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['opening_balance']=$opening_taken-$opening_given;
		$opening_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['opening_balance_value']=$opening_taken_value-$opening_given_value;
	}
	//var_dump($opening_data);die;
	/*$sql="Select a.prod_id,
		sum(case when a.transaction_date<'".$txt_date_from."' and a.transaction_type=1  then a.cons_quantity else 0 end) as opening_taken,
		sum(case when a.transaction_date<'".$txt_date_from."' and a.transaction_type=2  then a.cons_quantity else 0 end) as opening_given
		from inv_transaction a
		where a.transaction_type in (1,2)  and a.item_category=$cbo_item_cat and a.status_active=1 and a.is_deleted=0
		group by a.prod_id,b.id,b.store_id,b.item_category_id,b.item_group_id,b.avg_rate_per_unit,b.sub_group_name,b.item_description,b.item_size,b.unit_of_measure,b.item_code order by b.item_category_id";*/


	$loan_party_sql = "select a.loan_party, 1 as type from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.receive_purpose=5 and a.loan_party<>0 and a.company_id=$cbo_company_name and a.loan_party in($txt_loan_party_id) and b.transaction_type=1 and b.item_category in (5,6,7,22,23) $search_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by loan_party
	union all
	select a.loan_party, 2 as type from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.issue_purpose=5 and a.loan_party<>0 and a.company_id=$cbo_company_name and a.loan_party in($txt_loan_party_id) and b.transaction_type=2 and b.item_category in (5,6,7,22,23) $search_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by loan_party order by loan_party";

	$result_loan_party=sql_select($loan_party_sql);
	$loan_party_arr=array();
	foreach($result_loan_party as $row)
	{
		$loan_party_arr[$row[csf("loan_party")]]=$row[csf("loan_party")];
	}

	$gate_pass_sql = "select issue_id,sys_number as gate_pass_no from inv_gate_pass_mst where basis=6 and status_active=1 and is_deleted=0 and issue_id is not null ";

	$result_gate_pass=sql_select($gate_pass_sql);
	$gatePassArr=array();
	foreach($result_gate_pass as $psss_row)
	{
		//echo $psss_row[csf("sys_number")];
		$issue_ids=explode(",",$psss_row[csf("issue_id")]);

		foreach($issue_ids as $issue_id)
		{
			$gatePassArr[trim($issue_id)]=$psss_row[csf("gate_pass_no")];
		}
	}

	$gate_pass_in_sql = "select issue_id as challan_no ,sys_number as gate_pass_in_no ,1 as type from inv_gate_pass_mst where basis=6 and status_active=1 and is_deleted=0 and issue_id is not null union select challan_no,sys_number as gate_pass_in_no , 2 as type from inv_gate_in_mst where gate_pass_no is not null and challan_no is not null  and status_active=1 and is_deleted=0";

	$result_gate_pass_in=sql_select($gate_pass_in_sql);
	$gatePassArr=array(); $gateInArr=array();
	foreach($result_gate_pass_in as $psss_row)
	{
		//echo $psss_row[csf("gate_pass_in_no")];
		if($psss_row[csf("type")]==1)
		{
			$issue_ids=explode(",",$psss_row[csf("challan_no")]);
			foreach($issue_ids as $issue_id)
			{
				$gatePassArr[trim($issue_id)]=$psss_row[csf("gate_pass_in_no")];
			}
		}
		else
		{
			$gateInArr[$psss_row[csf("challan_no")]]=$psss_row[csf("gate_pass_in_no")];
		}
		
	}
	//print_r($gateInArr);
	//$gate_pass_arr=array_unique($gatePassArr);
	//var_dump($loan_party_arr);
	//echo $sql;//die;
	$result = sql_select($sql);
	$rcvQnty=$rcvValue=$issQnty=$issValue=0;
	$i=1;
	ob_start();
	?>

	<div style="width:1660px;" id="scroll_body">
    		<table width="1600" >
                <tr class="form_caption">
                    <td colspan="15" align="center"><b>Dyes and Chemical Loan Ledger</b></td>
                </tr>
            </table>
			<?
            $m=1;$k=1;$prod_taken=$prod_given=$prod_balance=$prod_taken_value=$prod_given_value=0;
			foreach($loan_party_arr as $pary_id=>$val)
			{
				$party_loan_taken=$party_loan_given=$party_loan_balance=$party_loan_taken_value=$party_loan_given_value=$prod_balance_value=0;
				?>
                <table width="1600" >	
                    <tr class="form_caption">
                        <td colspan="15" align="center"><b><? echo $companyArr[$cbo_company_name]; ?></b></td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="15" align="center"><b><? echo  $supplierArr[$pary_id]; ?></b></td>
                    </tr>
                </table>
                <table width="1700" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_<? echo $m; ?>">
                	<thead>
                        <tr>
                            <th width="30" align="center">SL</th>
                            <th width="100" align="center">MRR/Issue No.</th>
							<th width="100" align="center">Store</th>
                            <th width="70" align="center">MRR/Issue Date</th>
                            <th width="80" align="center">Challan No.</th>
                            <th width="60" align="center">Gate Entry No.</th>
                            <th width="60" align="center">Gate Pass No.</th>
                            <th width="130" align="center">Item Category</th>
                            <th width="100" align="center">Item Group</th>
                            <th width="100" align="center">Sub Group Name</th>
                            <th width="180" align="center">Item Description</th>
                            <th width="80" align="center">Loan Taken</th>
							<th width="90" align="center">Loan Taken Value</th> 
                            <th width="80" align="center">Loan Given</th>
							<th width="90" align="center">Loan Given Value</th> 
                            <th width="80" align="center">Balance</th>
                            <th width="90" align="center">Balance Value</th>
                            <th align="center">Comments</th>
                        </tr>
                    </thead>
                    <tbody>
					<?
					$i=1;$n=1;
					$check_product=array();
					//echo "<pre>";print_r($all_party_data[$pary_id]);die;
                    foreach($all_party_data[$pary_id] as $row)
                    {
						if(!in_array($row["prod_id"],$check_product))
						{
							$check_product[]=$row["prod_id"];
							if($n!=1)
							{
								?>
                                <tr bgcolor="#CCCCCC">
                                    <td colspan="11" align="right"><b>Item Total:</b>&nbsp;</td>
                                    <td align="right"><b><? echo number_format($prod_taken,2); ?></b></td>
									<td align="right"><b><? echo number_format($prod_taken_value,2); ?></b></td>
                                    <td align="right"><b><? echo number_format($prod_given,2); ?></b></td>
									<td align="right"><b><? echo number_format($prod_given_value,2); ?></b></td>
                                    <td align="right"><b><? echo number_format($prod_balance,2); $party_loan_balance+=$prod_balance; ?></b></td>
                                    <td align="right"><b><? echo number_format($prod_balance_value,2); $party_loan_balance_value+=$prod_balance_value; ?></b></td>
                                    <td align="center">&nbsp;</td>
                                </tr>
                                <?
								//$balance=0;
								$prod_taken=$prod_given=$prod_balance=$prod_taken_value=$prod_given_value=$prod_balance_value=0;
							}
							?>
                             <tr bgcolor="#DFDFDF">
                                <td colspan="11" align="right"><b>Opening:</b>&nbsp;</td>
                                <td align="right"><b><? echo number_format($opening_data[$pary_id][$row["prod_id"]]['opening_taken'],2); ?></b></td>
								<td align="right"><b><? echo number_format($opening_data[$pary_id][$row["prod_id"]]['opening_taken_value'],2); ?></b></td>
                                <td align="right"><b><? echo number_format($opening_data[$pary_id][$row["prod_id"]]['opening_given'],2); ?></b></td>
								<td align="right"><b><? echo number_format($opening_data[$pary_id][$row["prod_id"]]['opening_given_value'],2); ?></b></td>
								
                                <td align="right"><b><? echo number_format($opening_data[$pary_id][$row["prod_id"]]['opening_balance'],2); $prod_balance=$opening_data[$pary_id][$row["prod_id"]]['opening_balance']; ?></b></td>
                                <td align="right"><b><? echo number_format($opening_data[$pary_id][$row["prod_id"]]['opening_balance_value'],2); $prod_balance_value=$opening_data[$pary_id][$row["prod_id"]]['opening_balance_value']; ?></b></td>
                                <td>&nbsp;</td>
                            </tr>
                            <?
							$n++;
						}
						
						$loan_taken=$loan_given=$loan_taken_value=$loan_given_value=0;
						//echo $row["transaction_type"].test;die;
						if($row["transaction_type"]==1)
						{ 
							$taken_rate=$row["cons_rate"]; 
							$loan_taken=$row["cons_quantity"]; 
							$prod_balance +=$loan_taken;
							$prod_balance_value +=$loan_taken*$taken_rate;
						}
						else
						{
							$given_rate=$row["cons_rate"];
							$loan_given=$row["cons_quantity"];
							$prod_balance -=$loan_given;
							$prod_balance_value -=$loan_given*$given_rate;
						}
						$loan_taken_value=$loan_taken*$taken_rate;
						$loan_given_value=$loan_given*$given_rate;
						
						//$prod_balance -=$loan_given; 
						?>
                        <tr>
                            <td align="center"><? echo $i; ?></td>
                            <td><p><? echo $row["rcv_issue_no"];?>&nbsp;</p></td>
							<td width="100"><? echo $store_name_arr[$row['store_id']]; //echo "<pre>";print_r($store_name_arr);?></td>  
                            <td align="center"><p><? echo change_date_format($row["transaction_date"]);?>&nbsp;</p></td>
                            <td align="center" style="word-break:break-all;"><p><? echo $row["challan_no"];?>&nbsp;</p></td>
                            <td align="center"><p><?  echo $gateInArr[$row["rcv_issue_no"]];?>&nbsp;</p></td>
                            <td align="center"><p><?  echo $gatePassArr[$row["mst_id"]];?>&nbsp;</p></td>

                            <td><p><? echo $item_category[$row["item_category"]];?>&nbsp;</p></td>
                            <td><p><? echo $item_group_arr[$item_descrip_arr[$row["prod_id"]]["item_group_id"]];?>&nbsp;</p></td>
                            <td><p><? echo $sub_group_arr[$item_descrip_arr[$row["prod_id"]]["item_group_id"]];?>&nbsp;</p></td>

                            <td><? echo $item_descrip_arr[$row["prod_id"]]["product_name_details"];?></td>
                            <td align="right"><?  echo number_format($loan_taken,2); $prod_taken+=$loan_taken; $party_loan_taken+=$loan_taken;  ?></td>
							<td align="right" title="<? if($row["transaction_type"]==1) echo "Taken Rate=". $taken_rate;?>"><? echo number_format($loan_taken_value,2); $prod_taken_value+=$loan_taken_value; $party_loan_taken_value+=$loan_taken_value;  ?></td>
                            <td align="right"><? echo number_format($loan_given,2);$prod_given+=$loan_given; $party_loan_given+=$loan_given; ?></td>
							<td align="right" title="<? if($row["transaction_type"]==2) echo "Given Rate=".$given_rate;?>"><? echo number_format($loan_given_value,2);$prod_given_value+=$loan_given_value; $party_loan_given_value+=$loan_given_value; ?></td>
                            <td align="right"><? echo number_format($prod_balance,2);  ?></td>
                            <td align="right"><? echo number_format($prod_balance_value,2);  ?></td>
                            <td align="center"><p><? echo $row["remarks"];  ?></p></td>
                        </tr>
                        <?
						$i++;$k++;
                    }
                    ?>
                        <tr bgcolor="#CCCCCC">
                            <td colspan="11" align="right"><b>Item Total:</b>&nbsp;</td>
                            <td align="right"><b><? echo number_format($prod_taken,2); ?></b></td>
							<td align="right"><b><? echo number_format($prod_taken_value,2); ?></b></td>
                            <td align="right"><b><? echo number_format($prod_given,2); ?></b></td>
							<td align="right"><b><? echo number_format($prod_given_value,2); ?></b></td>
                            <td align="right"><b><? echo number_format($prod_balance,2);  $party_loan_balance+=$prod_balance; ?></b></td>
                            <td align="right"><b><? echo number_format($prod_balance_value,2); $party_loan_balance_value+=$prod_balance_value; ?></b></td>
                            <td align="center">&nbsp;</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="11" align="right"><b>Party Total:</b>&nbsp;</th>
                            <th align="right"><b><? echo number_format($party_loan_taken,2); $grand_party_loan_taken +=$party_loan_taken; ?></b></th>
							<th align="right"><b><? echo number_format($party_loan_taken_value,2); $grand_party_loan_taken_value +=$party_loan_taken_value; ?></b></th>
                            <th align="right"><b><? echo number_format($party_loan_given,2); $grand_party_loan_given +=$party_loan_given; ?></b></th>
							<th align="right"><b><?  echo number_format($party_loan_given_value,2); $grand_party_loan_given_value +=$party_loan_given_value; ?></b></th>
                            <th align="right"><b><? echo number_format($party_loan_balance,2); $grand_party_loan_balance +=$party_loan_balance; ?></b></th>
                            <th align="right"><b><?  echo number_format($party_loan_balance_value,2); $grand_party_loan_balance_value +=$party_loan_balance_value; $party_loan_balance_value=0; ?></b></th>
                            <th align="center">&nbsp;</th>
                        </tr>
                    </tfoot>
                </table>      
                <?
				$m++;
				$prod_taken=$prod_given=$prod_balance=$prod_taken_value=$prod_given_value=$prod_balance_value=0;
			}
				?>
				<table width="1700" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
					<tfoot>
						<tr>
						    <th width="30" align="center"></th>
                            <th width="100" align="center"></th>
                            <th width="70" align="center"></th>
                            <th width="80" align="center"></th>
                            <th width="60" align="center"></th>
                            <th width="60" align="center"></th>
                            <th width="130" align="center"></th>
                            <th width="100" align="center"></th>
                            <th width="100" align="center"></th>
							<th width="100" align="center"></th>
							<th  width="180" align="right"><b>Grand Total:</b>&nbsp;</th>
							<th width="80" align="right"><b><? echo number_format($grand_party_loan_taken ,2); ?></b></th>
							<th width="90" align="right"><b><? echo number_format($grand_party_loan_taken_value,2); ?></b></th>
							<th width="80" align="right"><b><? echo number_format($grand_party_loan_given,2); ?></b></th>
							<th width="90" align="right"><b><?  echo number_format($grand_party_loan_given_value,2); ?></b></th>
							<th width="80" align="right"><b><? echo number_format($grand_party_loan_balance,2); ?></b></th>
							<th width="90" align="right"><b><?  echo number_format($grand_party_loan_balance_value,2); ?></b></th>
							<th align="center">&nbsp;</th>
						</tr>
					</tfoot>	
				</table>
    </div>
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


if($action=="generate_report2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_loan_party_id=str_replace("'","",$txt_loan_party_id);
	$cbo_item_cat=str_replace("'","",$cbo_item_cat);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$search_cond="";
	if($db_type==0)
	{
 		if( $txt_date_from!="" && $txt_date_to!="" ) $search_cond .= " and b.transaction_date  between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
	}
	else
	{
		if($txt_date_from!="" && $txt_date_to!="") $search_cond .= " and b.transaction_date  between '".change_date_format($txt_date_from,'','',-1)."' and '".change_date_format($txt_date_to,'','',-1)."'";
	}

	if($cbo_item_cat>0) $search_cond .= " and b.item_category=$cbo_item_cat";


	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$item_group_arr = return_library_array("select id, item_name from lib_item_group","id","item_name");
	$sub_group_arr = return_library_array("select item_group_id, sub_group_name from product_details_master","item_group_id","sub_group_name");

	$item_sql= sql_select("select id, product_name_details, item_group_id,item_description from product_details_master where item_category_id in(5,6,7,22,23) and status_active=1 and is_deleted=0");
	$item_descrip_arr=array();
	foreach($item_sql as $row)
	{
		$item_descrip_arr[$row[csf("id")]]["product_name_details"]=$row[csf("item_description")];
		$item_descrip_arr[$row[csf("id")]]["item_group_id"]=$row[csf("item_group_id")];
	}
	
	
	$loan_party_sql = "select a.loan_party, 1 as type 
	from  inv_receive_master a, inv_transaction b 
	where a.id=b.mst_id and a.receive_purpose=5 and a.loan_party<>0 and a.company_id=$cbo_company_name and a.loan_party in($txt_loan_party_id) and b.transaction_type=1 and b.item_category in (5,6,7,22,23) $search_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	group by loan_party
	union all
	select a.loan_party, 2 as type 
	from inv_issue_master a, inv_transaction b 
	where a.id=b.mst_id and a.issue_purpose=5 and a.loan_party<>0 and a.company_id=$cbo_company_name and a.loan_party in($txt_loan_party_id) and b.transaction_type=2 and b.item_category in (5,6,7,22,23) $search_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	group by loan_party 
	order by loan_party";

	$result_loan_party=sql_select($loan_party_sql);
	$loan_party_arr=array();
	foreach($result_loan_party as $row)
	{
		$loan_party_arr[$row[csf("loan_party")]]=$row[csf("loan_party")];
	}
	
	if($txt_date_from!="")
	{
		$opening_sql = "select a.loan_party, b.prod_id, sum(case when b.transaction_date<'".$txt_date_from."' then b.cons_quantity else 0 end) as opening_cons_qnty, sum(case when b.transaction_date<'".$txt_date_from."' then b.cons_quantity*b.cons_rate else 0 end) as opening_cons_qnty_value, 1 as type 
		from  inv_receive_master a, inv_transaction b 
		where a.id=b.mst_id and a.receive_purpose=5 and a.loan_party<>0 and a.ref_closing_status <> 1 and a.company_id=$cbo_company_name and a.loan_party in($txt_loan_party_id) and b.transaction_type=1 and b.item_category in (5,6,7,22,23) $search_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		group by loan_party, prod_id
		union all
		select a.loan_party, b.prod_id, sum(case when b.transaction_date<'".$txt_date_from."' then b.cons_quantity else 0 end) as opening_cons_qnty, sum(case when b.transaction_date<'".$txt_date_from."' then b.cons_quantity*b.cons_rate else 0 end) as opening_cons_qnty_value, 2 as type 
		from inv_issue_master a, inv_transaction b 
		where a.id=b.mst_id and a.issue_purpose=5 and a.loan_party<>0 and a.company_id=$cbo_company_name and a.loan_party in($txt_loan_party_id) and b.transaction_type=2 and b.item_category in (5,6,7,22,23) $search_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		group by loan_party,prod_id 
		order by loan_party, prod_id";

	}
	else
	{
		 $opening_sql = "select a.loan_party, b.prod_id, 0 as opening_cons_qnty,0 as opening_cons_qnty_value, 1 as type 
		 from  inv_receive_master a, inv_transaction b 
		 where a.id=b.mst_id and a.receive_purpose=5 and a.loan_party<>0 and a.ref_closing_status <> 1 and a.company_id=$cbo_company_name and a.loan_party in($txt_loan_party_id) and b.transaction_type=1 and b.item_category in (5,6,7,22,23) $search_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		 group by loan_party, prod_id
		 union all
		 select a.loan_party, b.prod_id, 0 as opening_cons_qnty, 0 as opening_cons_qnty_value, 2 as type 
		 from inv_issue_master a, inv_transaction b 
		 where a.id=b.mst_id and a.issue_purpose=5 and a.loan_party<>0 and a.company_id=$cbo_company_name and a.loan_party in($txt_loan_party_id) and b.transaction_type=2 and b.item_category in (5,6,7,22,23) $search_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		 group by loan_party,prod_id 
		 order by loan_party, prod_id";
		//$result_opening=sql_select($openning_sql);
	}
	//echo $opening_sql;
	$result_opening=sql_select($opening_sql);
	$opening_data=array();$opening_taken=$opening_given=0;
	foreach($result_opening as $row)
	{
		if($row[csf("type")]==1) $opening_taken=$row[csf("opening_cons_qnty")]; else $opening_given=$row[csf("opening_cons_qnty")];
		if($row[csf("type")]==1) $opening_taken_value=$row[csf("opening_cons_qnty_value")]; else $opening_given_value=$row[csf("opening_cons_qnty_value")];

		$opening_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['opening_taken']=$opening_taken;
		$opening_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['opening_given']=$opening_given;
		
		$opening_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['opening_taken_value']=$opening_taken_value;
		$opening_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['opening_given_value']=$opening_given_value;
		
		$opening_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['opening_balance']=$opening_taken-$opening_given;
		$opening_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['opening_balance_value']=$opening_taken_value-$opening_given_value;
	}

	$sql="select a.id as mst_id, a.company_id, a.loan_party, a.recv_number as rcv_issue_no, a.challan_no, a.gate_entry_no as gate_entry_no, null as gate_pass_no, b.id as trans_id, b.item_category, b.transaction_type, b.transaction_date, b.prod_id, b.cons_quantity,b.cons_rate,b.remarks, 1 as type 
	from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.receive_purpose=5 and a.loan_party<>0 and a.ref_closing_status <>1 and a.company_id=$cbo_company_name and a.loan_party in($txt_loan_party_id) and b.transaction_type=1 and b.item_category in (5,6,7,22,23) $search_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	union all
	select a.id as mst_id, a.company_id, a.loan_party, a.issue_number as rcv_issue_no, a.challan_no, null  as gate_entry_no, a.gate_pass_no, b.id as trans_id, b.item_category, b.transaction_type, b.transaction_date, b.prod_id, b.cons_quantity,b.cons_rate,b.remarks, 2 as type 
	from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.issue_purpose=5 and a.loan_party<>0 and a.company_id=$cbo_company_name and a.loan_party in($txt_loan_party_id) and b.transaction_type=2 and b.item_category in (5,6,7,22,23) $search_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	order by loan_party,prod_id, mst_id";  //rcv_issue_no,loan_party,prod_id, mst_id ASC

	//echo $sql;
	$sql_data=sql_select($sql);
	$all_party_data=array();
	foreach($sql_data as $row)
	{
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['mst_id']=$row[csf("mst_id")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['trans_id']=$row[csf("trans_id")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['company_id']=$row[csf("company_id")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['loan_party']=$row[csf("loan_party")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['rcv_issue_no']=$row[csf("rcv_issue_no")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['challan_no']=$row[csf("challan_no")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['gate_entry_no']=$row[csf("gate_entry_no")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['gate_pass_no']=$row[csf("gate_pass_no")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['item_category']=$row[csf("item_category")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['transaction_type']=$row[csf("transaction_type")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['transaction_date']=$row[csf("transaction_date")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['prod_id']=$row[csf("prod_id")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['cons_quantity']=$row[csf("cons_quantity")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['cons_rate']=$row[csf("cons_rate")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['remarks']=$row[csf("remarks")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['type']=$row[csf("type")];
	}


	$rcvQnty=$rcvValue=$issQnty=$issValue=0;
	$i=1;
	ob_start();
	?>

	<div style="width:1100px;" id="scroll_body">
    		<table width="1100" >
                <tr class="form_caption">
                    <td colspan="15" align="center"><b>Dyes and Chemical Loan Ledger</b></td>
                </tr>
            </table>
			<?
            $m=1;$k=1;$prod_taken=$prod_given=$prod_balance=$prod_taken_value=$prod_given_value=0;
			foreach($loan_party_arr as $pary_id=>$val)
			{
				$party_loan_taken=$party_loan_given=$party_loan_balance=$party_loan_taken_value=$party_loan_given_value=$prod_balance_value=0;
				?>
                <table width="1100" >	
                    <tr class="form_caption">
                        <td colspan="15" align="center"><b><? echo $companyArr[$cbo_company_name]; ?></b></td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="15" align="center"><b><? echo  $supplierArr[$pary_id]; ?></b></td>
                    </tr>
                </table>
                <table width="1100" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_<? echo $m; ?>">
                	<thead>
                        <tr>
                            <th width="30" align="center">SL</th>
                            <th width="130" align="center">Item Category</th>
                            <th width="100" align="center">Item Group</th>
                            <th width="100" align="center">Sub Group Name</th>
                            <th width="180" align="center">Item Description</th>
                            <th width="80" align="center">Loan Taken</th>
							<th width="90" align="center">Loan Taken Value</th> 
                            <th width="80" align="center">Loan Given</th>
							<th width="90" align="center">Loan Given Value</th> 
                            <th width="80" align="center">Balance</th>
                            <th width="90" align="center">Balance Value</th>
                            <th width="90" align="center">Comments</th>
                        </tr>
                    </thead>
                    <tbody>
					<?
					$i=1;$n=1;
					$check_product=array();
					//echo "<pre>";print_r($all_party_data[$pary_id]);die;
                    foreach($all_party_data[$pary_id] as $row)
                    {
						if(!in_array($row["prod_id"],$check_product))
						{
							$check_product[]=$row["prod_id"];
							if($n!=1)
							{
								?>
                                <tr bgcolor="#CCCCCC">
                                    <td colspan="5" align="right"><b>Item Total:</b>&nbsp;</td>
                                    <td align="right"><b><? echo number_format($prod_taken,2); ?></b></td>
									<td align="right"><b><? echo number_format($prod_taken_value,2); ?></b></td>
                                    <td align="right"><b><? echo number_format($prod_given,2); ?></b></td>
									<td align="right"><b><? echo number_format($prod_given_value,2); ?></b></td>
                                    <td align="right"><b><? echo number_format($prod_balance,2); $party_loan_balance+=$prod_balance; ?></b></td>
                                    <td align="right"><b><? echo number_format($prod_balance_value,2); $party_loan_balance_value+=$prod_balance_value; ?></b></td>
                                    <td align="center">&nbsp;</td>
                                </tr>
                                <?
								$prod_taken=$prod_given=$prod_balance=$prod_taken_value=$prod_given_value=$prod_balance_value=0;
							}
							?>
                             <tr bgcolor="#DFDFDF">
                                <td colspan="5" align="right"><b>Opening:</b>&nbsp;</td>
                                <td align="right"><b><? echo number_format($opening_data[$pary_id][$row["prod_id"]]['opening_taken'],2); ?></b></td>
								<td align="right"><b><? echo number_format($opening_data[$pary_id][$row["prod_id"]]['opening_taken_value'],2); ?></b></td>
                                <td align="right"><b><? echo number_format($opening_data[$pary_id][$row["prod_id"]]['opening_given'],2); ?></b></td>
								<td align="right"><b><? echo number_format($opening_data[$pary_id][$row["prod_id"]]['opening_given_value'],2); ?></b></td>
								
                                <td align="right"><b><? echo number_format($opening_data[$pary_id][$row["prod_id"]]['opening_balance'],2); $prod_balance=$opening_data[$pary_id][$row["prod_id"]]['opening_balance']; ?></b></td>
                                <td align="right"><b><? echo number_format($opening_data[$pary_id][$row["prod_id"]]['opening_balance_value'],2); $prod_balance_value=$opening_data[$pary_id][$row["prod_id"]]['opening_balance_value']; ?></b></td>
                                <td>&nbsp;</td>
                            </tr>
                            <?
							$n++;
						}
						
						$loan_taken=$loan_given=$loan_taken_value=$loan_given_value=0;
						//echo $row["transaction_type"].test;die;
						if($row["transaction_type"]==1)
						{ 
							$taken_rate=$row["cons_rate"]; 
							$loan_taken=$row["cons_quantity"]; 
							$prod_balance +=$loan_taken;
							$prod_balance_value +=$loan_taken*$taken_rate;
						}
						else
						{
							$given_rate=$row["cons_rate"];
							$loan_given=$row["cons_quantity"];
							$prod_balance -=$loan_given;
							$prod_balance_value -=$loan_given*$given_rate;
						}
						$loan_taken_value=$loan_taken*$taken_rate;
						$loan_given_value=$loan_given*$given_rate;
						
						//$prod_balance -=$loan_given; 
						?>
                        <tr>
                            <td align="center"><? echo $i; ?></td>

                            <td><p><? echo $item_category[$row["item_category"]];?>&nbsp;</p></td>
                            <td><p><? echo $item_group_arr[$item_descrip_arr[$row["prod_id"]]["item_group_id"]];?>&nbsp;</p></td>
                            <td><p><? echo $sub_group_arr[$item_descrip_arr[$row["prod_id"]]["item_group_id"]];?>&nbsp;</p></td>

                            <td><? echo $item_descrip_arr[$row["prod_id"]]["product_name_details"];?></td>
                            <td align="right"><?  echo number_format($loan_taken,2); $prod_taken+=$loan_taken; $party_loan_taken+=$loan_taken;  ?></td>
							<td align="right" title="<? if($row["transaction_type"]==1) echo "Taken Rate=". $taken_rate;?>"><? echo number_format($loan_taken_value,2); $prod_taken_value+=$loan_taken_value; $party_loan_taken_value+=$loan_taken_value;  ?></td>
                            <td align="right"><? echo number_format($loan_given,2);$prod_given+=$loan_given; $party_loan_given+=$loan_given; ?></td>
							<td align="right" title="<? if($row["transaction_type"]==2) echo "Given Rate=".$given_rate;?>"><? echo number_format($loan_given_value,2);$prod_given_value+=$loan_given_value; $party_loan_given_value+=$loan_given_value; ?></td>
                            <td align="right"><? echo number_format($prod_balance,2);  ?></td>
                            <td align="right"><? echo number_format($prod_balance_value,2);  ?></td>
                            <td align="center"><p><? echo $row["remarks"];  ?></p></td>
                        </tr>
                        <?
						$i++;$k++;
                    }
                    ?>
                        <tr bgcolor="#CCCCCC">
                            <td colspan="5" align="right"><b>Item Total:</b>&nbsp;</td>
                            <td align="right"><b><? echo number_format($prod_taken,2); ?></b></td>
							<td align="right"><b><? echo number_format($prod_taken_value,2); ?></b></td>
                            <td align="right"><b><? echo number_format($prod_given,2); ?></b></td>
							<td align="right"><b><? echo number_format($prod_given_value,2); ?></b></td>
                            <td align="right"><b><? echo number_format($prod_balance,2);  $party_loan_balance+=$prod_balance; ?></b></td>
                            <td align="right"><b><? echo number_format($prod_balance_value,2); $party_loan_balance_value+=$prod_balance_value; ?></b></td>
                            <td align="center">&nbsp;</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" align="right"><b>Party Total:</b>&nbsp;</th>
                            <th align="right"><b><? echo number_format($party_loan_taken,2); $grand_party_loan_taken +=$party_loan_taken; ?></b></th>
							<th align="right"><b><? echo number_format($party_loan_taken_value,2); $grand_party_loan_taken_value +=$party_loan_taken_value; ?></b></th>
                            <th align="right"><b><? echo number_format($party_loan_given,2); $grand_party_loan_given +=$party_loan_given; ?></b></th>
							<th align="right"><b><?  echo number_format($party_loan_given_value,2); $grand_party_loan_given_value +=$party_loan_given_value; ?></b></th>
                            <th align="right"><b><? echo number_format($party_loan_balance,2); $grand_party_loan_balance +=$party_loan_balance; ?></b></th>
                            <th align="right"><b><?  echo number_format($party_loan_balance_value,2); $grand_party_loan_balance_value +=$party_loan_balance_value; $party_loan_balance_value=0; ?></b></th>
                            <th align="center">&nbsp;</th>
                        </tr>
                    </tfoot>
                </table>      
                <?
				$m++;
				$prod_taken=$prod_given=$prod_balance=$prod_taken_value=$prod_given_value=$prod_balance_value=0;
			}
			?>
			<table width="1100" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
				<tfoot>
					<tr>
						<th width="30" align="center"></th>                   
						<th width="130" align="center"></th>
						<th width="100" align="center"></th>
						<th width="100" align="center"></th>
						<th  width="180" align="right"><b>Grand Total:</b>&nbsp;</th>
						<th width="80" align="right"><b><? echo number_format($grand_party_loan_taken ,2); ?></b></th>
						<th width="90" align="right"><b><? echo number_format($grand_party_loan_taken_value,2); ?></b></th>
						<th width="80" align="right"><b><? echo number_format($grand_party_loan_given,2); ?></b></th>
						<th width="90" align="right"><b><?  echo number_format($grand_party_loan_given_value,2); ?></b></th>
						<th width="80" align="right"><b><? echo number_format($grand_party_loan_balance,2); ?></b></th>
						<th width="90" align="right"><b><?  echo number_format($grand_party_loan_balance_value,2); ?></b></th>
						<th width="90" align="center"></th>
					</tr>
				</tfoot>	
			</table>
    </div>
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

if($action=="generate_report3")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_loan_party_id=str_replace("'","",$txt_loan_party_id);
	$cbo_item_cat=str_replace("'","",$cbo_item_cat);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$search_cond="";
	if($cbo_item_cat>0) $search_cond .= " and b.item_category=$cbo_item_cat";

	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$item_group_arr = return_library_array("select id, item_name from lib_item_group","id","item_name");
	$item_sql= sql_select("select id, product_name_details, item_group_id,item_description, sub_group_name from product_details_master where item_category_id in(5,6,7,22,23) and status_active=1 and is_deleted=0");
	$item_descrip_arr=array();
	foreach($item_sql as $row)
	{
		$item_descrip_arr[$row[csf("id")]]["product_name_details"]=$row[csf("item_description")];
		$item_descrip_arr[$row[csf("id")]]["item_group_id"]=$row[csf("item_group_id")];
		$item_descrip_arr[$row[csf("id")]]["sub_group_name"]=$row[csf("sub_group_name")];
	}
	//echo $txt_date_from."=".$txt_date_to;die;
	
	//echo $opening_sql;
	$result_opening=sql_select($opening_sql);
	$sql="select a.id as mst_id, a.company_id, a.loan_party, a.recv_number as rcv_issue_no, a.challan_no, a.gate_entry_no as gate_entry_no, null as gate_pass_no, b.id as trans_id, b.item_category, b.transaction_type, b.transaction_date, b.prod_id, b.cons_quantity, b.cons_rate, b.cons_amount, b.remarks, 1 as type 
	from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.receive_purpose=5 and a.loan_party<>0 and a.ref_closing_status <>1 and a.company_id=$cbo_company_name and a.loan_party in($txt_loan_party_id) and b.transaction_type=1 and b.item_category in (5,6,7,22,23) $search_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	union all
	select a.id as mst_id, a.company_id, a.loan_party, a.issue_number as rcv_issue_no, a.challan_no, null  as gate_entry_no, a.gate_pass_no, b.id as trans_id, b.item_category, b.transaction_type, b.transaction_date, b.prod_id, b.cons_quantity,b.cons_rate, b.cons_amount, b.remarks, 2 as type 
	from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.issue_purpose=5 and a.loan_party<>0 and a.company_id=$cbo_company_name and a.loan_party in($txt_loan_party_id) and b.transaction_type=2 and b.item_category in (5,6,7,22,23) $search_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	order by loan_party,prod_id,trans_id";

	//echo $sql;//die;
	$sql_data=sql_select($sql);
	foreach($sql_data as $row)
	{
		$loan_party_arr[$row[csf("loan_party")]]=$row[csf("loan_party")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['company_id']=$row[csf("company_id")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['loan_party']=$row[csf("loan_party")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['item_category']=$row[csf("item_category")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['prod_id']=$row[csf("prod_id")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['remarks']=$row[csf("remarks")];
		if($txt_date_from!="" && $txt_date_to!="")
		{
			if(strtotime($row[csf("transaction_date")])<strtotime($txt_date_from))
			{
				if($row[csf("type")]==1)
				{
					$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['opening_taken']+=$row[csf("cons_quantity")];
					$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['opening_balance']+=$row[csf("cons_quantity")];
					$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['opening_taken_value']+=$row[csf("cons_amount")];
					$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['opening_balance_value']+=$row[csf("cons_amount")];
				}
				else
				{
					$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['opening_given']-=$row[csf("cons_quantity")];
					$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['opening_balance']-=$row[csf("cons_quantity")];
					$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['opening_given_value']-=$row[csf("cons_amount")];
					$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['opening_balance_value']-=$row[csf("cons_amount")];
				}
			}
			else if(strtotime($row[csf("transaction_date")])>=strtotime($txt_date_from) && strtotime($row[csf("transaction_date")])<=strtotime($txt_date_to))
			{
				if($row[csf("type")]==1)
				{
					$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['taken']+=$row[csf("cons_quantity")];
					$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['balance']+=$row[csf("cons_quantity")];
					$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['taken_value']+=$row[csf("cons_amount")];
					$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['balance_value']+=$row[csf("cons_amount")];
				}
				else
				{
					$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['given']-=$row[csf("cons_quantity")];
					$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['balance']-=$row[csf("cons_quantity")];
					$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['given_value']-=$row[csf("cons_amount")];
					$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['balance_value']-=$row[csf("cons_amount")];
				}
			}
		}
		else
		{
			$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['opening_taken']=0;
			$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['opening_balance']=0;
			$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['opening_taken_value']=0;
			$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['opening_balance_value']=0;
			if($row[csf("type")]==1)
			{
				$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['taken']+=$row[csf("cons_quantity")];
				$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['balance']+=$row[csf("cons_quantity")];
				$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['taken_value']+=$row[csf("cons_amount")];
				$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['balance_value']+=$row[csf("cons_amount")];
			}
			else
			{
				$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['given']-=$row[csf("cons_quantity")];
				$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['balance']-=$row[csf("cons_quantity")];
				$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['given_value']-=$row[csf("cons_amount")];
				$all_party_data[$row[csf("loan_party")]][$row[csf("prod_id")]]['balance_value']-=$row[csf("cons_amount")];
			}
		}
		
	}

	$rcvQnty=$rcvValue=$issQnty=$issValue=0;
	$i=1;
	ob_start();
	?>

	<div style="width:1100px;" id="scroll_body">
    		<table width="1100" >
                <tr class="form_caption">
                    <td colspan="15" align="center"><b>Dyes and Chemical Loan Ledger</b></td>
                </tr>
            </table>
			<?
            $m=1;$k=1;$prod_taken=$prod_given=$prod_balance=$prod_taken_value=$prod_given_value=0;
			foreach($loan_party_arr as $pary_id=>$val)
			{
				$party_loan_taken=$party_loan_given=$party_loan_balance=$party_loan_taken_value=$party_loan_given_value=$prod_balance_value=0;
				?>
                <table width="1100" >	
                    <tr class="form_caption">
                        <td colspan="15" align="center"><b><? echo $companyArr[$cbo_company_name]; ?></b></td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="15" align="center"><b><? echo  $supplierArr[$pary_id]; ?></b></td>
                    </tr>
                </table>
                <table width="1100" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_<? echo $m; ?>">
                	<thead>
                        <tr>
                            <th width="30" align="center">SL</th>
                            <th width="130" align="center">Item Category</th>
                            <th width="100" align="center">Item Group</th>
                            <th width="100" align="center">Sub Group Name</th>
                            <th width="180" align="center">Item Description</th>
                            <th width="80" align="center">Loan Taken</th>
							<th width="90" align="center">Loan Taken Value</th> 
                            <th width="80" align="center">Loan Given</th>
							<th width="90" align="center">Loan Given Value</th> 
                            <th width="80" align="center">Balance</th>
                            <th width="90" align="center">Balance Value</th>
                            <th width="90" align="center">Comments</th>
                        </tr>
                    </thead>
                    <tbody>
					<?
					$i=1;$n=1;
                    foreach($all_party_data[$pary_id] as $prod_id=>$row)
                    {
						?>
                         <tr bgcolor="#DFDFDF">
                            <td colspan="5" align="right"><b>Opening:</b>&nbsp;</td>
                            <td align="right"><b><? echo number_format($row["opening_taken"],2); ?></b></td>
                            <td align="right"><b><? echo number_format($row["opening_taken_value"],2); ?></b></td>
                            <td align="right"><b><? echo number_format($row["opening_given"],2); ?></b></td>
                            <td align="right"><b><? echo number_format($row["opening_given_value"],2); ?></b></td>
                            
                            <td align="right"><b><? echo number_format($row["opening_balance"],2);?></b></td>
                            <td align="right"><b><? echo number_format($row["opening_balance_value"],2);?></b></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td align="center"><? echo $i; ?></td>
                            <td><p><? echo $item_category[$row["item_category"]];?>&nbsp;</p></td>
                            <td><p><? echo $item_group_arr[$item_descrip_arr[$row["prod_id"]]["item_group_id"]];?>&nbsp;</p></td>
                            <td><p><? echo $item_descrip_arr[$row["prod_id"]]["sub_group_name"]?>&nbsp;</p></td>
                            <td><p><? echo $item_descrip_arr[$row["prod_id"]]["product_name_details"];?>&nbsp;</p></td>
                            <td align="right"><?  echo number_format($row["taken"],2); $party_loan_taken+=$row["taken"];  ?></td>
							<td align="right"><? echo number_format($row["taken_value"],2); $party_loan_taken_value+=$row["taken_value"];  ?></td>
                            <td align="right"><? echo number_format(abs($row["given"]),2); $party_loan_given+=abs($row["given"]); ?></td>
							<td align="right"><? echo number_format(abs($row["given_value"]),2); $party_loan_given_value+=abs($row["given_value"]); ?></td>
                            <td align="right"><? echo number_format($row["balance"],2);  ?></td>
                            <td align="right"><? echo number_format($row["balance_value"],2);  ?></td>
                            <td align="center"><p><? echo $row["remarks"];  ?></p></td>
                        </tr>
                        <?
						$i++;$k++;
                    }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" align="right"><b>Party Total:</b>&nbsp;</th>
                            <th align="right"><b><? echo number_format($party_loan_taken,2); $grand_party_loan_taken +=$party_loan_taken; ?></b></th>
							<th align="right"><b><? echo number_format($party_loan_taken_value,2); $grand_party_loan_taken_value +=$party_loan_taken_value; ?></b></th>
                            <th align="right"><b><? echo number_format($party_loan_given,2); $grand_party_loan_given +=$party_loan_given; ?></b></th>
							<th align="right"><b><?  echo number_format($party_loan_given_value,2); $grand_party_loan_given_value +=$party_loan_given_value; ?></b></th>
                            <th align="right"><b><? $party_balance=$party_loan_taken-abs($party_loan_given); echo number_format($party_balance,2); $grand_party_loan_balance +=$party_balance; ?></b></th>
                            <th align="right"><b><? $party_balace_value=$party_loan_taken_value-abs($party_loan_given_value); echo number_format($party_balace_value,2); $grand_party_loan_balance_value +=$party_balace_value; ?></b></th>
                            <th align="center">&nbsp;</th>
                        </tr>
                    </tfoot>
                </table>      
                <?
				$m++;
				$prod_taken=$prod_given=$prod_balance=$prod_taken_value=$prod_given_value=$prod_balance_value=0;
			}
			?>
			<table width="1100" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
				<tfoot>
					<tr>
						<th width="30" align="center"></th>                   
						<th width="130" align="center"></th>
						<th width="100" align="center"></th>
						<th width="100" align="center"></th>
						<th  width="180" align="right"><b>Grand Total:</b>&nbsp;</th>
						<th width="80" align="right"><b><? echo number_format($grand_party_loan_taken ,2); ?></b></th>
						<th width="90" align="right"><b><? echo number_format($grand_party_loan_taken_value,2); ?></b></th>
						<th width="80" align="right"><b><? echo number_format($grand_party_loan_given,2); ?></b></th>
						<th width="90" align="right"><b><?  echo number_format($grand_party_loan_given_value,2); ?></b></th>
						<th width="80" align="right"><b><? echo number_format($grand_party_loan_balance,2); ?></b></th>
						<th width="90" align="right"><b><?  echo number_format($grand_party_loan_balance_value,2); ?></b></th>
						<th width="90" align="center"></th>
					</tr>
				</tfoot>	
			</table>
    </div>
    <?
	$html = ob_get_contents();
	ob_clean();
	//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	foreach (glob("$user_id*.xls") as $filename) {
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

if($action=="generate_report4")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_loan_party_id=str_replace("'","",$txt_loan_party_id);
	$cbo_item_cat=str_replace("'","",$cbo_item_cat);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$search_cond="";
	if($cbo_item_cat>0) $search_cond .= " and b.item_category=$cbo_item_cat";

	if($db_type==0)
	{
 		if( $txt_date_from!="" && $txt_date_to!="" ) $search_cond .= " and b.transaction_date  between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
	}
	else
	{
		if($txt_date_from!="" && $txt_date_to!="") $search_cond .= " and b.transaction_date  between '".change_date_format($txt_date_from,'','',-1)."' and '".change_date_format($txt_date_to,'','',-1)."'";

		// if($txt_date_from!="" && $txt_date_to!="") $search_cond .= " and b.transaction_date  between '".date("d-M-Y", strtotime($txt_date_from))."' and '".date("d-M-Y", strtotime($txt_date_to))."'";
	}

	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$item_group_arr = return_library_array("select id, item_name from lib_item_group","id","item_name");

	$sql="SELECT b.item_category, b.receive_basis, a.company_id, sum(b.cons_quantity) as loan_qty, 1 as type,c.unit_of_measure,c.item_group_id,c.ITEM_DESCRIPTION from  inv_receive_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.receive_purpose=5 and a.loan_party<>0 and a.ref_closing_status <>1 and a.company_id=$cbo_company_name and a.loan_party in($txt_loan_party_id) and b.transaction_type=1 and b.item_category in (5,6,7,23) $search_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.item_category, b.receive_basis, a.company_id, c.unit_of_measure, c.item_group_id, c.ITEM_DESCRIPTION
	union all
	select b.item_category, b.receive_basis, a.company_id, sum(b.cons_quantity) as loan_qty, 2 as type ,c.unit_of_measure,c.item_group_id,c.ITEM_DESCRIPTION from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.issue_purpose=5 and a.loan_party<>0 and a.company_id=$cbo_company_name and a.loan_party in($txt_loan_party_id) and b.transaction_type=2 and b.item_category in (5,6,7,23) $search_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.item_category, b.receive_basis, a.company_id, c.unit_of_measure, c.item_group_id, c.ITEM_DESCRIPTION";
	// and a.entry_form=298
	// echo $sql; die;
	$loan_rec_issue=array();
	$sql_data=sql_select($sql);

	foreach($sql_data as $row){

		$key=$row["ITEM_CATEGORY"]."_".$row["ITEM_GROUP_ID"]."_".$row["ITEM_DESCRIPTION"]."_".$row["UNIT_OF_MEASURE"];
		$loan_rec_issue[$key]["ITEM_CATEGORY"]=$row["ITEM_CATEGORY"];
		$loan_rec_issue[$key]["UNIT_OF_MEASURE"]=$row["UNIT_OF_MEASURE"];
		$loan_rec_issue[$key]["COMPANY_ID"]=$row["COMPANY_ID"];
		$loan_rec_issue[$key]["ITEM_GROUP_ID"]=$row["ITEM_GROUP_ID"];
		$loan_rec_issue[$key]["ITEM_DESCRIPTION"]=$row["ITEM_DESCRIPTION"];
		$loan_rec_issue[$key]["RECEIVE_BASIS"]=$row["RECEIVE_BASIS"];
		if($row["TYPE"]==1){
		$loan_rec_issue[$key]["REC_LOAN_QTY"]+=$row["LOAN_QTY"];
		}else{
		$loan_rec_issue[$key]["ISSUE_LOAN_QTY"]+=$row["LOAN_QTY"];
		}		
	}
	
	$i=1;
	ob_start();
	?>
	<div style="width:1100px;" id="scroll_body">
		<table width="1100" >
			<tr class="form_caption">
				<td colspan="8" align="center"><b style="font-size: 14px;">Dyes and Chemical Loan Ledger</b></td>
			</tr>
		</table>
		<table width="1100" >	
			<tr class="form_caption">
				<td colspan="8" align="center"><b style="font-size: 14px;"><? echo $companyArr[$cbo_company_name]; ?></b></td>
			</tr>
			<tr class="form_caption">
				<td colspan="8" align="center"><b><? echo  $supplierArr[$pary_id]; ?></b></td>
			</tr>
		</table>
		<table width="1100" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th width="30" align="center">SL</th>
					<th width="130" align="center">Item Category</th>
					<th width="100" align="center">Item Group</th>
					<th width="300" align="center">Item Description</th>
					<th width="80" align="center">Uom</th>
					<th width="80" align="center">Loan Taken</th>							 
					<th width="80" align="center">Loan Given</th>						
					<th width="80" align="center">Balance</th>                          
				</tr>
			</thead>
		</table>
		<table width="1100" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_four">
			<tbody>
				<?
				$i=1;$n=1; $balance=0;
				// abs(
				foreach($loan_rec_issue as $row)
				{
					$balance=$row["REC_LOAN_QTY"]-$row["ISSUE_LOAN_QTY"];
					$item_cat=$row['ITEM_CATEGORY'];
					$item_grup=$row['ITEM_GROUP_ID'];
					$prod_dtls_name=$row['ITEM_DESCRIPTION'];
					$umo=$row['UNIT_OF_MEASURE'];
					$company_id=$row['COMPANY_ID'];
					$rcv_basis = $row['RECEIVE_BASIS'];
					?>                     
					<tr>
						<td width="30" align="center"><p><? echo $i; ?>&nbsp;</p></td>
						<td width="130" align="center"><p><? echo $item_category[$row["ITEM_CATEGORY"]];?></p></td>
						<td width="100" align="center"><p><? echo $item_group_arr[$row["ITEM_GROUP_ID"]];?></p></td>
						<td width="300" align="center"><p><? echo  $row["ITEM_DESCRIPTION"];?></p></td>
						<td width="80" align="center"><p><? echo  $unit_of_measurement[$row["UNIT_OF_MEASURE"]];?></p></td>
						<td width="80" align="right">
							<a href='##' onclick="fnc_rcv_details('<? echo $item_cat; ?>','<? echo $item_grup ;?>','<? echo $prod_dtls_name;?>','<? echo $umo;?>','<? echo $company_id;?>','Loan Receive','rcv_popup_details','<? echo $txt_loan_party_id;?>','<? echo $rcv_basis;?>','<? echo $txt_date_to;?>','<? echo $txt_date_from;?>')">
								<? echo number_format($row["REC_LOAN_QTY"],2); ?>
							<a>
						</td>
						<td width="80" align="right">
							<a href='##' onclick="fnc_rcv_details('<? echo $item_cat; ?>','<? echo $item_grup ;?>','<? echo $prod_dtls_name;?>','<? echo $umo;?>','<? echo $company_id;?>','Loan Issue','issue_popup_details','<? echo $txt_loan_party_id;?>')">
								<? echo number_format($row["ISSUE_LOAN_QTY"],2); ?>
							<a>
						</td>							
						<td width="80" align="right"><? echo number_format($balance,2);  ?></td>						
					</tr>
					<?
					$i++;
				}
				?>
			</tbody>                 
		</table>      			
    </div>
    <?
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("$user_id*.xls") as $filename) {
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

if ($action=="rcv_popup_details") 
{
	echo load_html_head_contents("Receive Details", "../../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);
	?>
	<script>

		$(function(){
            setFilterGrid("table_body_one",-1);
        });

		function print_window()
		{
			$('.fltrow').hide();
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
		
			d.close();
			$('.fltrow').show();
		}	
	</script>
	<?
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$item_group_arr = return_library_array("select id, item_name from lib_item_group","id","item_name");

	if($db_type==0)
	{
 		if( $from_date!="" && $to_date!="" ) $date_cond .= " and b.transaction_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
	}
	else
	{
		if($from_date!="" && $to_date!="") $date_cond .= " and b.transaction_date  between '".change_date_format($from_date,'','',-1)."' and '".change_date_format($to_date,'','',-1)."'";
	}

    $sql="SELECT a.recv_number as RECV_NUMBER,a.company_id as COMPANY_ID, a.loan_party as LOAN_PARTY,b.item_category as ITEM_CATEGORY,c.item_group_id as ITEM_GROUP_ID, a.challan_no as CHALLAN_NO,a.challan_date as CHALLAN_DATE, a.receive_date as RECEIVE_DATE, b.cons_uom as CONS_UOM, b.cons_rate as CONS_RATE, b.cons_amount as CONS_AMOUNT, b.remarks as REMARKS, b.inserted_by as INSERTED_BY,c.item_description as ITEM_DESCRIPTION,c.unit_of_measure as UNIT_OF_MEASURE,b.cons_quantity as CONS_QUANTITY
	from inv_receive_master a, inv_transaction b, product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and a.receive_purpose=5 and a.company_id=$company and b.item_category=$item_cat_id $date_cond and c.item_group_id=$item_grup_id and c.item_description='$item_descrip' and c.unit_of_measure=$umo_id and b.transaction_type=1 and a.status_active=1 and b.status_active=1 and a.loan_party in($loan_party)";
    // echo $sql;
    $result = sql_select($sql);

	foreach( $result as $row){
		$party=$row['LOAN_PARTY'];
		$company_id=$row['COMPANY_ID'];
	}

	?>
	<div style="width:1115px;" style="margin: 0 auto;">
		<div style="float: left; margin-left: 455px;"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
		<div id="report_container" style="float: left;"></div>
	</div>
	<? ob_start(); ?>
	<fieldset style="width:1110px">
		<div style="width:910px;" id="scroll_body">
			<table width="1100" >
				<tr class="form_caption">
					<td colspan="12" align="center"><b>Dyes and Chemical Loan Ledger</b></td>
				</tr>
			</table>
			<table width="1100" >	
				<tr class="form_caption">
					<td colspan="12" align="center"><b><? echo $companyArr[$company_id]; ?></b></td>
				</tr>
				<tr class="form_caption">
					<td colspan="12" align="center"><b><? echo  $supplierArr[$party]; ?></b></td>
				</tr>
			</table>
			<table width="1110" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
				<thead>
					<th width="30">SL</th>
					<th width="150">MRR No</th>
					<th width="80">MRR Date</th>
					<th width="80">Challan No</th>
					<th width="80">Challan Date</th>
					<th width="100">Item Category</th>
					<th width="80">Item Group</th>
					<th width="80">Item Description</th>
					<th width="60">Uom</th>
					<th width="100">Loan Taken</th>
					<th width="80">Loan Taken Value</th>
					<th>Comment</th>
				</thead>
			</table>
			<table width="1110" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_one">
				<tbody>
					<?
					$i = 1;$loan_tot=0;
					foreach ($result as $row) 
					{
						if($i % 2 == 0){ $bgcolor = "#E9F3FF"; }else{ $bgcolor = "#FFFFFF"; }				
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td  width="30" align="center"><p><? echo $i; ?></p></td>
							<td  width="150" align="center" ><p><? echo $row["RECV_NUMBER"]; ?>&nbsp;</p></td>
							<td width="80" align="center"><p><? echo change_date_format($row["RECEIVE_DATE"]); ?>&nbsp;</p></td>
							<td width="80" align="center"><p><? echo $row["CHALLAN_NO"]; ?>&nbsp;</p></td>
							<td width="80" align="center"><p><? echo change_date_format($row["CHALLAN_DATE"]); ?>&nbsp;</p></td>
							<td width="100" align="center"><p><? echo $item_category[$row["ITEM_CATEGORY"]]; ?>&nbsp;</p></td>
							<td width="80" align="center"><p><? echo $item_group_arr[$row["ITEM_GROUP_ID"]]; ?></p></td>
							<td width="80" align="center"><? echo $row["ITEM_DESCRIPTION"]; ?></td>
							<td width="60" align="center"><? echo $unit_of_measurement[$row["UNIT_OF_MEASURE"]]; ?></td>
							<td width="100" align="right" ><? echo number_format($row["CONS_QUANTITY"],2); ?>&nbsp;</td>
							<td width="80" align="right"><? echo number_format($row["CONS_AMOUNT"],2); ?>&nbsp;</td>
							<td align="center" ><? echo $row["REMARKS"]; ?>&nbsp;</td>
						</tr>
						<?
						$i++;
						$loan_tot+=$row["CONS_QUANTITY"];
						$loan_amount_tot+=$row["CONS_AMOUNT"];
					}
					?>
					<tr>
						<td align="right" colspan="8"><b>Total</b></td>
						<td>&nbsp;</td>
						<td align="right"><b><?=number_format($loan_tot,2)?></b></td>
						<td align="right"><b><?=number_format($loan_amount_tot,2)?></b></td>
						<td>&nbsp;</td>
					</tr>
				</tbody>
			</table>
		</div>
	</fieldset>
	<?
	$html = ob_get_contents();
	ob_flush();
	foreach (glob("$user_id*.xls") as $filename) {
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	// echo "$html**$filename";
	?>
	<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$filename; ?>" />
	<script>
        $(document).ready(function(e) {
            document.getElementById('report_container').innerHTML='<a href="<? echo $filename?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
        }); 
    </script>
	<?
	exit();
}

if ($action=="issue_popup_details") 
{
	echo load_html_head_contents("Receive Details", "../../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);

	?>
	<script>

		$(function(){
            setFilterGrid("table_body_two",-1);
        });

		function print_window()
		{
			$('.fltrow').hide();
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
		
			d.close();
			$('.fltrow').show();
		}	
	</script>
	<?

	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$item_group_arr = return_library_array("select id, item_name from lib_item_group","id","item_name");
	
    $sql="SELECT a.issue_number as ISSUE_NUMBER, a.company_id as COMPANY_ID,a.loan_party as LOAN_PARTY,b.item_category as ITEM_CATEGORY,c.item_group_id as ITEM_GROUP_ID, a.challan_no as CHALLAN_NO, a.issue_date as ISSUE_DATE, b.cons_uom as CONS_UOM, b.cons_rate as CONS_RATE, b.cons_amount as CONS_AMOUNT, a.remarks as REMARKS, b.inserted_by as INSERTED_BY,c.item_description as ITEM_DESCRIPTION,c.unit_of_measure as UNIT_OF_MEASURE,b.cons_quantity as CONS_QUANTITY
	from inv_issue_master a, inv_transaction b, product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and a.company_id=$company and a.issue_purpose=5 and b.item_category=$item_cat_id and c.item_group_id=$item_grup_id and c.item_description='$item_descrip' and c.unit_of_measure=$umo_id and b.transaction_type=2 and a.status_active=1 and b.status_active=1 and a.loan_party in($loan_party)";
	// and a.entry_form=298 
    // echo $sql;
    $result = sql_select($sql);

	foreach( $result as $row){
		$party=$row['LOAN_PARTY'];
		$company_id=$row['COMPANY_ID'];
	}
	?>
		<div style="width:1115px;" style="margin: 0 auto;">
		<div style="float: left; margin-left: 455px;"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
		<div id="report_container" style="float: left;"></div>
	</div>
	<?	ob_start(); ?>
	<fieldset style="width:1110px">
		<div style="width:1110px;" id="scroll_body">
			<table width="1100" >
				<tr class="form_caption">
					<td colspan="12" align="center"><b>Dyes and Chemical Loan Ledger</b></td>
				</tr>
			</table>
			<table width="1100" >	
				<tr class="form_caption">
					<td colspan="12" align="center"><b><? echo $companyArr[$company_id]; ?></b></td>
				</tr>
				<tr class="form_caption">
					<td colspan="12" align="center"><b><? echo  $supplierArr[$party]; ?></b></td>
				</tr>
			</table>
			<table width="1110" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
				<thead>
					<th width="30">SL</th>
					<th width="150">Issue NO</th>
					<th width="80">Issue Date</th>
					<th width="80">Challan No</th>
					<th width="100">Item Category</th>
					<th width="100">Item Group</th>
					<th width="80">Item Description</th>
					<th width="80">Uom</th>
					<th width="100">Loan Given</th>
					<th width="100">Loan Given Value</th>
					<th>Comment</th>
				</thead>
			</table>
			<table width="1110" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_two">
				<tbody>
					<?
					$i = 1;$loan_tot=0;
					foreach ($result as $row) 
					{
						if($i % 2 == 0){ $bgcolor = "#E9F3FF"; }else{ $bgcolor = "#FFFFFF"; }				
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30" align="center"><p><? echo $i; ?>&nbsp;</p></td>
							<td width="150" align="center" ><p><? echo $row["ISSUE_NUMBER"]; ?>&nbsp;</p></td>
							<td width="80" align="center"><p><? echo change_date_format($row["ISSUE_DATE"]); ?>&nbsp;</p></td>
							<td width="80" align="center"><p><? echo $row["CHALLAN_NO"]; ?>&nbsp;</p></td>
							<td width="100" align="center"><p><? echo $item_category[$row["ITEM_CATEGORY"]]; ?>&nbsp;</p></td>
							<td width="100" align="center"><p><? echo $item_group_arr[$row["ITEM_GROUP_ID"]]; ?></p></td>
							<td width="80" align="center"><? echo $row["ITEM_DESCRIPTION"]; ?></td>
							<td width="80" align="center"><? echo $unit_of_measurement[$row["UNIT_OF_MEASURE"]]; ?></td>
							<td width="100" align="right" ><? echo number_format($row["CONS_QUANTITY"],2); ?>&nbsp;</td>
							<td width="100" align="right"><? echo  number_format($row["CONS_AMOUNT"],2); ?>&nbsp;</td>
							<td align="center" ><? echo $row["REMARKS"]; ?>&nbsp;</td>
						</tr>
						<?
						$i++;
						$loan_tot+=$row["CONS_QUANTITY"];
						$loan_amount_tot+=$row["CONS_AMOUNT"];
					}
					?>
					<tr>
						<td align="right" colspan="7"><b>Total</b></td>
						<td></td>
						<td  align="right"><b><?=number_format($loan_tot,2)?></b></td>
						<td  align="right"><b><?=number_format($loan_amount_tot,2)?></b></td>
						<td></td>
					</tr>
				</tbody>
			</table>
		</div>
	</fieldset>

	<?
	$html = ob_get_contents();
	ob_flush();
	foreach (glob("$user_id*.xls") as $filename) {
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	?>
	<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$filename; ?>" />
	<script>
        $(document).ready(function(e) {
            document.getElementById('report_container').innerHTML='<a href="<? echo $filename?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
        }); 
    </script>
	<?
	exit();
}

?>
