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
	where a.id=b.supplier_id and b.tag_company=$company and a.status_active=1 and a.is_deleted=0 and a.id in(select supplier_id from lib_supplier_party_type where party_type in(91)) order by supplier_name";
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



//report generated here--------------------//
if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_loan_party_id=str_replace("'","",$txt_loan_party_id);
	$cbo_item_category=str_replace("'","",$cbo_item_category);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$search_cond="";
	if($db_type==0)
	{
 		if( $txt_date_from!="" && $txt_date_to!="" ) $search_cond .= " and b.transaction_date  between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
 		if( $cbo_item_category!="" || $cbo_item_category!=0 ) $search_cond .= " and b.item_category in($cbo_item_category)";
	}
	else
	{
		if($txt_date_from!="" && $txt_date_to!="") $search_cond .= " and b.transaction_date  between '".change_date_format($txt_date_from,'','',-1)."' and '".change_date_format($txt_date_to,'','',-1)."'";
		if( $cbo_item_category!="" || $cbo_item_category!=0 ) $search_cond .= " and b.item_category in($cbo_item_category)";
	}


	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$item_group_arr = return_library_array("select id, item_name from lib_item_group","id","item_name");

	$sql_item_details = "select id, product_name_details, item_group_id, item_description, item_color, item_category_id from product_details_master where company_id=$cbo_company_name and item_category_id in(4,8,9,10,11,15,16,17,18,19,20,21,22,32,33,34,35,36,37,38,39,40,41,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,89,90,91,92,93,94,99,100,101) and status_active=1 and is_deleted=0";//die;
	$item_sql= sql_select($sql_item_details);

	$item_descrip_arr=array();
	foreach($item_sql as $row)
	{
		$item_descrip_arr[$row[csf("id")]]["product_name_details"]=$row[csf("product_name_details")];
		$item_descrip_arr[$row[csf("id")]]["item_description"]=$row[csf("item_description")];
		$item_descrip_arr[$row[csf("id")]]["item_group_id"]=$row[csf("item_group_id")];
		$item_descrip_arr[$row[csf("id")]]["item_category_id"]=$row[csf("item_category_id")];
	}
	/*echo '<pre>';
	print_r($item_descrip_arr);die;*/


	$sql="SELECT a.id as mst_id, a.company_id, a.loan_party, a.recv_number as rcv_issue_no, a.challan_no, a.gate_entry_no as gate_entry_no, null as gate_pass_no, b.id as trans_id, b.item_category, b.transaction_type, b.transaction_date, b.prod_id, b.cons_quantity,b.cons_rate,b.remarks, 1 as type 
	from  inv_receive_master a, inv_transaction b 
	where a.id=b.mst_id and a.loan_party<>0 and (a.ref_closing_status <> 1 or a.ref_closing_status is null) and a.company_id=$cbo_company_name and a.loan_party in($txt_loan_party_id) and b.transaction_type=1 and b.item_category in (4,8,9,10,11,15,16,17,18,19,20,21,22,32,33,34,35,36,37,38,39,40,41,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,89,90,91,92,93,94,99,100,101) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_cond
	union all
	select a.id as mst_id, a.company_id, a.loan_party, a.issue_number as rcv_issue_no, a.challan_no, null  as gate_entry_no, a.gate_pass_no, b.id as trans_id, b.item_category, b.transaction_type, b.transaction_date, b.prod_id, b.cons_quantity,b.cons_rate,b.remarks, 2 as type from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.loan_party<>0 and a.company_id=$cbo_company_name and a.loan_party in($txt_loan_party_id) and b.transaction_type=2 and b.item_category in (4,8,9,10,11,15,16,17,18,19,20,21,22,32,33,34,35,36,37,38,39,40,41,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,89,90,91,92,93,94,99,100,101) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_cond order by loan_party,prod_id, mst_id";

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
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['item_category']=$row[csf("item_category")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['transaction_type']=$row[csf("transaction_type")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['transaction_date']=$row[csf("transaction_date")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['prod_id']=$row[csf("prod_id")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['cons_quantity']=$row[csf("cons_quantity")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['cons_rate']=$row[csf("cons_rate")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['remarks']=$row[csf("remarks")];
		$all_party_data[$row[csf("loan_party")]][$row[csf("trans_id")]]['type']=$row[csf("type")];
	}

	if($txt_date_from!="")
	{
		$opening_sql = "select a.loan_party, b.prod_id, sum(case when b.transaction_date<'".$txt_date_from."' then b.cons_quantity else 0 end) as opening_cons_qnty, sum(case when b.transaction_date<'".$txt_date_from."' then b.cons_quantity*b.cons_rate else 0 end) as opening_cons_qnty_value, 1 as type from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.loan_party<>0 and a.ref_closing_status <> 1 and a.company_id=$cbo_company_name and a.loan_party in($txt_loan_party_id) and b.transaction_type=1 and b.item_category in (4,8,9,10,11,15,16,17,18,19,20,21,22,32,33,34,35,36,37,38,39,40,41,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,89,90,91,92,93,94,99,100,101) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_cond group by loan_party, prod_id
		union all
		select a.loan_party, b.prod_id, sum(case when b.transaction_date<'".$txt_date_from."' then b.cons_quantity else 0 end) as opening_cons_qnty, sum(case when b.transaction_date<'".$txt_date_from."' then b.cons_quantity*b.cons_rate else 0 end) as opening_cons_qnty_value, 2 as type from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.loan_party<>0 and a.company_id=$cbo_company_name and a.loan_party in($txt_loan_party_id) and b.transaction_type=2 and b.item_category in (4,8,9,10,11,15,16,17,18,19,20,21,22,32,33,34,35,36,37,38,39,40,41,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,89,90,91,92,93,94,99,100,101) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_cond group by loan_party,prod_id order by loan_party, prod_id";

	}
	else
	{
		$opening_sql = "select a.loan_party, b.prod_id, 0 as opening_cons_qnty,0 as opening_cons_qnty_value, 1 as type from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.loan_party<>0 and a.ref_closing_status <> 1 and a.company_id=$cbo_company_name and a.loan_party in($txt_loan_party_id) and b.transaction_type=1 and b.item_category in (4,8,9,10,11,15,16,17,18,19,20,21,22,32,33,34,35,36,37,38,39,40,41,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,89,90,91,92,93,94,99,100,101) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_cond group by loan_party, prod_id
		union all
		select a.loan_party, b.prod_id, 0 as opening_cons_qnty, 0 as opening_cons_qnty_value, 2 as type from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.loan_party<>0 and a.company_id=$cbo_company_name and a.loan_party in($txt_loan_party_id) and b.transaction_type=2 and b.item_category in (4,8,9,10,11,15,16,17,18,19,20,21,22,32,33,34,35,36,37,38,39,40,41,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,89,90,91,92,93,94,99,100,101) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_cond group by loan_party,prod_id order by loan_party, prod_id";
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
	}
	/*echo '<pre>';
	print_r($opening_data);*/

	$loan_party_sql = "SELECT a.loan_party, 1 as type from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.loan_party<>0 and a.company_id=$cbo_company_name and a.loan_party in($txt_loan_party_id) and b.transaction_type=1 and b.item_category in (4,8,9,10,11,15,16,17,18,19,20,21,22,32,33,34,35,36,37,38,39,40,41,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,89,90,91,92,93,94,99,100,101) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_cond group by loan_party
	union all
	select a.loan_party, 2 as type from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.loan_party<>0 and a.company_id=$cbo_company_name and a.loan_party in($txt_loan_party_id) and b.transaction_type=2 and b.item_category in (4,8,9,10,11,15,16,17,18,19,20,21,22,32,33,34,35,36,37,38,39,40,41,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,89,90,91,92,93,94,99,100,101) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_cond group by loan_party order by loan_party";

	$result_loan_party=sql_select($loan_party_sql);
	$loan_party_arr=array();
	foreach($result_loan_party as $row)
	{
		$loan_party_arr[$row[csf("loan_party")]]=$row[csf("loan_party")];
	}

	//var_dump($loan_party_arr);
	//echo $sql;//die;
	$result = sql_select($sql);
	$rcvQnty=$rcvValue=$issQnty=$issValue=0;
	$i=1;
	if(count($sql_data)>0)
	{
		ob_start();
		?>
		<div style="width:1100px;" id="scroll_body">
			<table width="1050" >
	            <tr class="form_caption">
	                <td colspan="11" align="center"><b>Yarn Loan Ledger</b></td>
	            </tr>
	            <tr class="form_caption">
	                <td colspan="11" align="center"><b><? echo $companyArr[$cbo_company_name]; ?></b></td>
	            </tr>
	        </table>
				<?
	            $m=1;$k=1;$prod_taken=$prod_given=$prod_balance=0;
				foreach($loan_party_arr as $pary_id=>$val)
				{
					$party_loan_taken=$party_loan_given=$party_loan_balance=0;
					?>
	                <table width="1050" > 
	                    <tr class="form_caption">
	                        <td colspan="11" align="center"><b><? echo  $supplierArr[$pary_id]; ?></b></td>
	                    </tr>
	                </table>
	                <table width="1050" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_<? echo $m; ?>">
	                	<thead>
	                        <tr>
	                            <th width="30" align="center">SL</th>
	                            <th width="110" align="center">MRR/Issue No.</th>
	                            <th width="70" align="center">MRR/Issue Date</th>
	                            <th width="110" align="center">Challan No.</th>
	                            <th width="90" align="center">Item Category</th>
	                            <th width="70" align="center">Item Group</th> 
	                            <th width="150" align="center">Item Description</th>
	                            <th width="80" align="center">Loan Taken</th> 
	                            <th width="80" align="center">Loan Given</th> 
	                            <th width="100" align="center">Balance</th>
	                            <th align="center"> Comments</th>
	                        </tr>
	                    </thead>
	                    <tbody>
						<?
						$i=1;$n=1;
						$check_product=array();
	                    foreach($all_party_data[$pary_id] as $row)
	                    {
							if(!in_array($row["prod_id"],$check_product))
							{
								$check_product[]=$row["prod_id"];
								if($n!=1)
								{
									?>
	                                <tr bgcolor="#EEEEEE">
	                                    <td colspan="7" align="right"><b>Item Total:</b>&nbsp;</td>
	                                    <td align="right"><b><? echo number_format($prod_taken,2); ?></b></td> 
	                                    <td align="right"><b><? echo number_format($prod_given,2); ?></b></td>
	                                    <td align="right"><b><? echo number_format($prod_balance,2); $party_loan_balance+=$prod_balance; ?></b></td>
	                                    <td align="center">&nbsp;</td>
	                                </tr>
	                                <?
									//$balance=0;
									$prod_taken=$prod_given=$prod_balance=0;
									 
								}
								?>
	                             <tr bgcolor="#EFEFEF">
	                                <td colspan="7" align="right"><b>Opening:</b>&nbsp;</td>
	                                <td align="right"><b><? echo number_format($opening_data[$pary_id][$row["prod_id"]]['opening_taken'],2); ?></b></td> 
	                                <td align="right"><b><? echo number_format($opening_data[$pary_id][$row["prod_id"]]['opening_given'],2); ?></b></td> 
									
	                                <td align="right"><b><? echo number_format($opening_data[$pary_id][$row["prod_id"]]['opening_balance'],2); $prod_balance=$opening_data[$pary_id][$row["prod_id"]]['opening_balance']; ?></b></td>
	                                <td>&nbsp;</td>
	                            </tr>
	                            <?
								$n++;
							}
							$loan_taken=$loan_given=0;
							
							if($row["transaction_type"]==1)
							{ 
								$taken_rate=$row["cons_rate"];  
							}
							else
							{
								$given_rate=$row["cons_rate"];
							}
							
							?>
	                        <tr>
	                            <td align="center"><? echo $i; ?></td>
	                            <td><p><? echo $row["rcv_issue_no"];?>&nbsp;</p></td>
	                            <td align="center"><p><? echo change_date_format($row["transaction_date"]);?>&nbsp;</p></td>
	                            <td align="center"><p><? echo $row["challan_no"];?>&nbsp;</p></td>
	                            <td align="center"><p><?  echo $general_item_category[$row["item_category"]];?>&nbsp;</p></td>
	                            <td align="center"><p><?  echo $item_group_arr[$item_descrip_arr[$row["prod_id"]]["item_group_id"]];?>&nbsp;</p></td>
			                    <td width="70"><? echo $item_descrip_arr[$row["prod_id"]]["product_name_details"]; //$yarn_brand_arr[$item_descrip_arr[$row["prod_id"]]["brand"]];?></td>    

	                            <td align="right"><? if($row["transaction_type"]==1) $loan_taken=$row["cons_quantity"];  echo number_format($loan_taken,2); $prod_taken+=$loan_taken; $party_loan_taken+=$loan_taken; $prod_balance+=$loan_taken;  ?></td> 

	                            <td align="right"><? if($row["transaction_type"]==2) $loan_given=$row["cons_quantity"];  echo number_format($loan_given,2);$prod_given+=$loan_given; $party_loan_given+=$loan_given; $prod_balance -=$loan_given; ?></td> 

	                            <td align="right"><? echo number_format($prod_balance,2);  ?></td>
	                            <td align="center"><p><? echo $row["remarks"];  ?></p></td>
	                        </tr>
	                        <?
							$i++;$k++;
	                    }
	                    ?>
	                        <tr bgcolor="#EEEEEE">
	                            <td colspan="7" align="right"><b>Item Total:</b>&nbsp;</td>
	                            <td align="right"><b><? echo number_format($prod_taken,2); ?></b></td> 
	                            <td align="right"><b><? echo number_format($prod_given,2); ?></b></td> 
	                            <td align="right"><b><? echo number_format($prod_balance,2);  $party_loan_balance+=$prod_balance; ?></b></td>
	                            <td align="center">&nbsp;</td>
	                        </tr>
	                    </tbody>
	                    <tfoot>
	                        <tr>
	                            <th colspan="7" align="right"><b>Party Total:</b>&nbsp;</th>
	                            <th align="right"><b><? echo number_format($party_loan_taken,2); ?></b></th> 
	                            <th align="right"><b><? echo number_format($party_loan_given,2); ?></b></th> 
	                            <th align="right"><b><? echo number_format($party_loan_balance,2); ?></b></th>
	                            <th align="center">&nbsp;</th>
	                        </tr>
	                    </tfoot>

	                </table>
	                <?
					$m++;
				}
				?>
	    </div>
	    <style type="text/css">
	    	.color{
	    		color: red;
	    		margin: 50px;
	    		border: 1px solid;
	    	}
	    </style>
	    <? 
	}
	else
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold;margin: 50px;border: 1px solid;'>Data not found.</font>";
	}
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
