<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];



$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$supplier_id = $userCredential[0][csf('supplier_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];

if ($company_id !='') {
    $company_credential_cond = "and comp.id in($company_id)";
}
if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)"; 
}
if ($supplier_id !='') {
    $supplier_credential_cond = "and c.id in($supplier_id)";
}

if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 122, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data' and b.category_type=4 and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );
	exit();
}

$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
$trim_group= return_library_array( "select id, item_name from lib_item_group",'id','item_name');

if ($action=="item_description_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);  
?>	
    <script>
	 var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	 
	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
	function js_set_value(id)
	{
		var str=id.split("_");
		toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
		var strdt=str[2];
		str=str[1];
	
		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str );
			selected_name.push( strdt );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i,1 );
		}
		var id = '';
		var ddd='';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			ddd += selected_name[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		ddd = ddd.substr( 0, ddd.length - 1 );
		$('#item_desc_id').val( id );
		$('#item_desc_val').val( ddd );
	} 
		  
	</script>
     <input type="hidden" id="item_desc_id" />
     <input type="hidden" id="item_desc_val" />
 <?
	if ($data[1]==0) $item_name =""; else $item_name =" and item_group_id in($data[1])";
	$sql="SELECT id, item_category_id, product_name_details from product_details_master where item_category_id=4 and status_active=1 and is_deleted=0 $item_name"; 
	$arr=array(0=>$item_category);
	echo  create_list_view("list_view", "Description,Product ID", "300,150","450","300",0, $sql , "js_set_value", "id,product_name_details", "", 1, "0,0,0", $arr , "product_name_details,id", "",'setFilterGrid("list_view",-1);','0,0,0,0,0,0','',1) ;
	exit();
}

if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$determinaArr = return_library_array("select id,construction from lib_yarn_count_determina_mst where status_active=1 and is_deleted=0","id","construction");
	$store_arr = return_library_array("select id,store_name from lib_store_location","id","store_name");
	//echo $cbo_company_name;die;
	if ($cbo_company_name==0) $company_id =""; else $company_id =" and a.company_id='$cbo_company_name'";
	
	if($db_type==0) 
	{
		$from_date=change_date_format($from_date,'yyyy-mm-dd');
		$to_date=change_date_format($to_date,'yyyy-mm-dd');
	}
	else if($db_type==2) 
	{
		$from_date=change_date_format($from_date,'','',1);
		$to_date=change_date_format($to_date,'','',1);
	}
	else  
	{
		$from_date=""; $to_date="";
	}
	if ($cbo_item_group==0) 
	{
		$items_group=""; 
		$item="";
	}
	else 
	{
		 $items_group=" and b.prod_id in ($cbo_item_group)";
		$item=" and b.item_group_id in ($cbo_item_group)";
	}
	if ($item_description_id==0) 
	{
		$item_description=""; 
		$prod_cond="";
	}
	else 
	{
		$item_description=" and b.prod_id in ($item_description_id)";
		$prod_cond=" and b.id in ($item_description_id)";
	}
	$search_cond="";
	//if($value_with==0) $search_cond ="  and b.current_stock>=0"; else $search_cond= "  and b.current_stock>0";
	$store_cond="";
	if($cbo_store_name>0)  $store_cond=" and a.store_id=$cbo_store_name";
	
	
    if($report_type == 2)
	{
		/*$data_array=array();
		$trnasactionData=sql_select("Select b.id, b.item_description,b.item_group_id, b.current_stock, b.avg_rate_per_unit, a.store_id,
		sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
		sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
		sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as rcv_total_opening_amt,
		sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as iss_total_opening_amt,
		sum(case when a.transaction_type in(1,4,5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as purchase_amount,
		sum(case when a.transaction_type in(2,3,6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as issue_amount,   
		sum(case when a.transaction_type in(1) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as receive,
		sum(case when a.transaction_type in(4) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue_return,
		sum(case when a.transaction_type in(2) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue,
		sum(case when a.transaction_type in(3) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as receive_return,
		sum(case when a.transaction_type in(5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as receive_transfer,
		sum(case when a.transaction_type in(6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue_transfer
		from inv_transaction a, product_details_master b
		where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id=4 and a.item_category=4 and a.order_id=0 $company_id $item $prod_cond  group by b.id, b.item_description,b.item_group_id, b.current_stock, b.avg_rate_per_unit, a.store_id order by b.id ASC");	
		foreach($trnasactionData as $row)
		{
			$data_array[$row[csf("id")]]['rcv_total_opening']=$row[csf("rcv_total_opening")];
			$data_array[$row[csf("id")]]['iss_total_opening']=$row[csf("iss_total_opening")];
			$data_array[$row[csf("id")]]['receive']=$row[csf("receive")];
			$data_array[$row[csf("id")]]['issue']=$row[csf("issue")];
			$data_array[$row[csf("id")]]['receive_return']=$row[csf("receive_return")];
			$data_array[$row[csf("id")]]['issue_return']=$row[csf("issue_return")];
			$data_array[$row[csf("id")]]['receive_return']=$row[csf("receive_return")];
			$data_array[$row[csf("id")]]['issue_return']=$row[csf("issue_return")];



			$data_array[$row[csf("id")]]['rcv_total_opening_amt']=$row[csf("rcv_total_opening_amt")];
			$data_array[$row[csf("id")]]['iss_total_opening_amt']=$row[csf("iss_total_opening_amt")];
			$data_array[$row[csf("id")]]['purchase_amount']=$row[csf("purchase_amount")];
			$data_array[$row[csf("id")]]['issue_amount']=$row[csf("issue_amount")];
			$data_array[$row[csf("id")]]['issue_transfer']=$row[csf("issue_transfer")];
			$data_array[$row[csf("id")]]['receive_transfer']=$row[csf("receive_transfer")];
		}*/
		
		$date_array=array();
		$returnRes_date="select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category=4 group by prod_id";
		$result_returnRes_date = sql_select($returnRes_date);
		foreach($result_returnRes_date as $row)	
		{
			$date_array[$row[csf("prod_id")]]['min_date']=$row[csf("min_date")];
			$date_array[$row[csf("prod_id")]]['max_date']=$row[csf("max_date")];
		}
		ob_start();	
		?>
		<div>
        <table style="width:2182px" border="1" cellpadding="2" cellspacing="0"  id="caption" rules="all"> 
            <thead>
                <tr class="form_caption" style="border:none;">
                    <td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td> 
                </tr>
                <tr class="form_caption" style="border:none;">
                    <td colspan="14" align="center" style="border:none; font-size:14px;">
                       <b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>                               
                    </td>
                </tr>
                <tr class="form_caption" style="border:none;">
                    <td colspan="14" align="center" style="border:none;font-size:12px; font-weight:bold">
                        <? if($from_date!="" || $to_date!="") echo "From : ".change_date_format($from_date)." To : ".change_date_format($to_date)."" ;?>
                    </td>
                </tr>
            </thead>
        </table>
        <table width="2181" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        	<thead>
               <tr>
                    <th rowspan="2" width="40">SL</th>
                    <th rowspan="2" width="60">Prod.ID</th>
                    <th colspan="2">Description</th>
                    <th rowspan="2" width="100">Store</th>
                    <th rowspan="2" width="100">Opening Rate</th>
                    <th rowspan="2" width="110">Opening Stock</th>
                    <th rowspan="2" width="100">Opening Value</th>
                    <th colspan="5">Receive</th>
                    <th colspan="5">Issue</th>
                    <th rowspan="2" width="100">Closing Stock</th>
                    <th rowspan="2" width="80">Avg. Rate (TK.)</th>
                    <th rowspan="2" width="100">Amount</th>
                    <th rowspan="2" width="80">Age(Days)</th>
                    <th rowspan="2">DOH</th>
               </tr> 
               <tr>                         
                    <th width="120">Item Group</th>
                    <th width="180">Item Description</th>
                    <th width="80">Receive</th>
                    <th width="100">Transfer In</th>
                    <th width="80">Issue Return</th>
                    <th width="100">Total Receive</th>
                    <th width="100">Receive Value</th>
                    <th width="80">Issue</th>
                    <th width="100">Transfer Out</th>
                    <th width="80">Received Return</th>
                    <th width="100">Total Issue</th>
                    <th width="100">Issue Value</th>
               </tr> 
            </thead>
        </table>
        <div style="width:2200px; max-height:350px; overflow-y:scroll" id="scroll_body" > 
            <table width="2181" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
            <?
             // $sql="select b.id, b.item_description,b.item_group_id, b.current_stock, b.avg_rate_per_unit from product_details_master b where b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_name' and b.item_category_id='4' $item $prod_cond $search_cond order by b.id";
			 
			 	$sql="Select b.id, b.item_description,b.item_group_id, b.current_stock, b.avg_rate_per_unit, a.store_id,
				sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
				sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
				sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as rcv_total_opening_amt,
				sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as iss_total_opening_amt,
				sum(case when a.transaction_type in(1,4,5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as purchase_amount,
				sum(case when a.transaction_type in(2,3,6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as issue_amount,   
				sum(case when a.transaction_type in(1) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as receive,
				sum(case when a.transaction_type in(4) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue_return,
				sum(case when a.transaction_type in(2) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue,
				sum(case when a.transaction_type in(3) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as receive_return,
				sum(case when a.transaction_type in(5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as receive_transfer,
				sum(case when a.transaction_type in(6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue_transfer
				from inv_transaction a, product_details_master b
				where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id=4 and a.item_category=4 $company_id $item $prod_cond $search_cond  $store_cond  
				group by b.id, b.item_description,b.item_group_id, b.current_stock, b.avg_rate_per_unit, a.store_id order by b.id ASC";
				 
				//echo $sql;
				 	
                $result = sql_select($sql);
				$i=1; $total_amount=0;
                foreach($result as $row)
                {
                   if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
					$ageOfDays = datediff("d",$date_array[$row[csf("id")]]['min_date'],date("Y-m-d"));
					$daysOnHand = datediff("d",$date_array[$row[csf("id")]]['max_date'],date("Y-m-d")); 
    
                    $opening_bal=$row[csf("rcv_total_opening")]-$row[csf("iss_total_opening")];
                    
					if(number_format($opening_bal,2)>0) 
					{
						$openingBalanceValue = $row[csf("rcv_total_opening_amt")]-$row[csf("iss_total_opening_amt")];
						$openingRate=$openingBalanceValue/$opening_bal;
						
					} 
					else 
					{
						$openingRate='0.00';
						$openingBalanceValue = '0.00';
					}

                    $receive = $row[csf("receive")];
                    $issue = $row[csf("issue")];
                    $issue_return=$row[csf("issue_return")];
                    $receive_return=$row[csf("receive_return")];
                    $issue_transfer=$row[csf("issue_transfer")];
                    $receive_transfer=$row[csf("receive_transfer")];
                    
                    $purchase_amount_value=$row[csf("purchase_amount")];
                    $issue_amount_value=$row[csf("issue_amount")];
                    
                  
                    $tot_receive=$receive+$issue_return+$receive_transfer;
					$tot_issue=$issue+$receive_return+$issue_transfer;
									
					$closingStock=$opening_bal+$tot_receive-$tot_issue;
					if(number_format($closingStock,2)>0)
					{
						$amount= $openingBalanceValue + $purchase_amount_value + $issue_amount_value;
						$closingRate = $amount/$closingStock;
						
					} 
					else 
					{
						$closingRate = '0.00';
						$amount= '0.00';
					}
					

                        if(((($value_with ==1) && (number_format($closingStock,2) > 0.00))||($value_with ==0)) && ( (number_format($opening_bal,2) > 0.00) || (number_format($tot_receive,2) > 0.00) || (number_format($tot_issue,2) > 0.00) ) ) //|| (number_format($closingStock,2) > 0.00)
                            {
                                if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $closingStock>$txt_qnty) || ($get_upto_qnty==2 && $closingStock<$txt_qnty) || ($get_upto_qnty==3 && $closingStock>=$txt_qnty) || ($get_upto_qnty==4 && $closingStock<=$txt_qnty) || ($get_upto_qnty==5 && $closingStock==$txt_qnty) || $get_upto_qnty==0))
                                {
                                        //$amount=$closingStock*$row[csf("avg_rate_per_unit")];

                                        ?>
                                        <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="40"><? echo $i; ?></td>	
                            <td width="60" align="center"><p><? echo $row[csf("id")]; ?></p></td>
                            <td width="120"><p><? echo $trim_group[$row[csf('item_group_id')]]; ?></p></td>
                            <td width="180" style="word-break:break-all;"><p><? echo $row[csf("item_description")]; ?></p></td>
                            <td width="100" style="word-break:break-all;"><p><? echo $store_arr[$row[csf("store_id")]]; ?></p></td>       
                            <td width="100" align="right"><p><? echo number_format($openingRate,2); ?></p></td>
                            <td width="110" align="right"><p><? echo number_format($opening_bal,2); ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($openingBalanceValue,2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($receive,2); ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($receive_transfer,2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($issue_return,2); ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($tot_receive,2); ?></p></td>
                            <td width="100" align="right"><p><?  echo number_format($purchase_amount_value,2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($issue,2); ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($issue_transfer,2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($receive_return,2); ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($tot_issue,2); ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($issue_amount_value,2); ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($closingStock,2); ?></p></td>
                            <td width="80" align="right"><? echo number_format($closingRate,2); ?></td>
                            <td width="100" align="right"><p><? echo number_format($amount,2); ?></p></td>
                            <td width="80" align="center"><? echo $ageOfDays; ?></td>
                            <td align="center"><? echo $daysOnHand; ?></td>
                                        </tr>
                                        <? 
                                        $total_opening+=$opening_bal;
                                        $total_receive+=$receive;
                                        $total_issue_return+=$issue_return;
                                        $total_receive_balance+=$tot_receive;
                                        $total_issue+=$issue;
                                        $total_receive_return+=$receive_return;
                                        $total_issue_balance+=$tot_issue;
                                        $total_closing_stock+=$closingStock;
                                        $total_amount+=$amount;								
                                        $i++; 				
                                }
                            }
                    }
				?>
            </table>
		</div> 
        <table width="2181" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" > 
           <tr>
                <td width="40">&nbsp;</td>
                <td width="60">&nbsp;</td> 
                <td width="120">&nbsp;</td> 
                <td width="180" align="right">Total</td>
                <td width="100">&nbsp;</td> 
                <td width="100">&nbsp;</td>
                <td width="110" align="right" id="value_total_opening_td"><? echo number_format($total_opening,2); ?></td>
                <td width="100" id="value_total_opening_value_td">&nbsp;</td> 
                <td width="80" align="right" id="value_total_receive_td"><? echo number_format($total_receive,2); ?></td>
                <td width="100" align="right"><p>&nbsp;</p></td>
                <td width="80" align="right" id="value_total_issue_return_td"><? echo number_format($total_issue_return,2); ?></td>
                <td width="100" align="right" id="value_total_receive_balance_td"><? echo number_format($total_receive_balance,2); ?></td>
                <td width="100" id="value_total_receive_value_td">&nbsp;</td> 
                <td width="80" align="right" id="value_total_issue_td"><? echo number_format($total_issue,2); ?></td>
                <td width="100" align="right"><p>&nbsp;</p></td>
                <td width="80" align="right" id="value_total_receive_return_td"><? echo number_format($total_receive_return,2); ?></td>
                <td width="100" align="right" id="value_total_issue_balance_td"><? echo number_format($total_issue_balance,2); ?></td>
                <td width="100" id="value_total_issue_value_td">&nbsp;</td> 
                <td width="100" align="right" id="value_total_closing_stock_td"><? echo number_format($total_closing_stock,2); ?></td>
                <td width="80">&nbsp;</td>
                <td width="100" align="right" id="value_total_closing_amnt"><? echo number_format($total_amount,2); ?></td>
                <td>&nbsp;</td>
            </tr>
        </table>
        </div>
    <?
    }
	else if($report_type == 1)
	{
        /*$data_array=array();
		$trnasactionData=sql_select("Select b.id, b.item_description,b.item_group_id, b.current_stock, b.avg_rate_per_unit, a.store_id,
		sum(case when a.transaction_type in(1,4) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
		sum(case when a.transaction_type in(2,3) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
		sum(case when a.transaction_type in(1) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as receive,
		sum(case when a.transaction_type in(4) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue_return,
		sum(case when a.transaction_type in(2) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue,
		sum(case when a.transaction_type in(3) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as receive_return
		from inv_transaction a, product_details_master b
		where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id=4 and  and a.item_category=4 $company_id $item $prod_cond  group by b.id, b.item_description,b.item_group_id, b.current_stock, b.avg_rate_per_unit, a.store_id order by b.id ASC");	
		foreach($trnasactionData as $row)
		{
			$data_array[$row[csf("id")]]['rcv_total_opening']=$row[csf("rcv_total_opening")];
			$data_array[$row[csf("id")]]['iss_total_opening']=$row[csf("iss_total_opening")];
			$data_array[$row[csf("id")]]['receive']=$row[csf("receive")];
			$data_array[$row[csf("id")]]['issue']=$row[csf("issue")];
			$data_array[$row[csf("id")]]['receive_return']=$row[csf("receive_return")];
			$data_array[$row[csf("id")]]['issue_return']=$row[csf("issue_return")];
		}*/
	
		$date_array=array();
		$returnRes_date="select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category=4 group by prod_id";
		$result_returnRes_date = sql_select($returnRes_date);
		foreach($result_returnRes_date as $row)	
		{
			$date_array[$row[csf("prod_id")]]['min_date']=$row[csf("min_date")];
			$date_array[$row[csf("prod_id")]]['max_date']=$row[csf("max_date")];
		}
		ob_start();	
		?>
		<div>
        <table style="width:1820px" border="1" cellpadding="2" cellspacing="0"  id="caption" rules="all"> 
            <thead>
                <tr class="form_caption" style="border:none;">
                    <td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td> 
                </tr>
                <tr class="form_caption" style="border:none;">
                    <td colspan="14" align="center" style="border:none; font-size:14px;">
                       <b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>                               
                    </td>
                </tr>
                <tr class="form_caption" style="border:none;">
                    <td colspan="14" align="center" style="border:none;font-size:12px; font-weight:bold">
                        <? if($from_date!="" || $to_date!="") echo "From : ".change_date_format($from_date)." To : ".change_date_format($to_date)."" ;?>
                    </td>
                </tr>
            </thead>
        </table>
        <table width="1820" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        	<thead>
               <tr>
                    <th rowspan="2" width="40">SL</th>
                    <th rowspan="2" width="60">Prod.ID</th>
                    <th colspan="3">Description</th>
                    <th rowspan="2" width="100">Store</th>
                    <th rowspan="2" width="110">Opening Stock</th>
                    <th colspan="4">Receive</th>
                    <th colspan="4">Issue</th>
                    <th rowspan="2" width="100">Closing Stock</th>
                    <th rowspan="2" width="80">Avg. Rate (TK.)</th>
                    <th rowspan="2" width="100">Amount</th>
                    <th rowspan="2" width="80">Age(Days)</th>
                    <th rowspan="2">DOH</th>
               </tr> 
               <tr>                         
                    <th width="120">Item Group</th>
                    <th width="180">Item Description</th>
                    <th width="100">Item Size</th>
                    <th width="80">Receive</th>
                    <th width="80">Issue Return</th>
                    <th width="80">Transfer In</th>
                    <th width="100">Total Receive</th>
                    <th width="80">Issue</th>
                    <th width="80">Received Return</th>
                    <th width="80">Transfer Out</th>
                    <th width="100">Total Issue</th>
               </tr> 
            </thead>
        </table>
        <div style="width:1840px; max-height:350px; overflow-y:scroll" id="scroll_body" > 
            <table width="1820" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
            <?
              	//$sql="select b.id, b.item_description,b.item_group_id, b.current_stock, b.avg_rate_per_unit from product_details_master b where b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_name' and b.item_category_id='4' $item $prod_cond $search_cond order by b.id";
			  	$sql="Select b.id, b.item_description,b.item_size,b.item_group_id, b.current_stock, b.avg_rate_per_unit, a.store_id,
				sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
				sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
				sum(case when a.transaction_type in(1) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as receive,
				sum(case when a.transaction_type in(4) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue_return,
				sum(case when a.transaction_type in(5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as transfer_in,
				sum(case when a.transaction_type in(2) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue,
				sum(case when a.transaction_type in(3) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as receive_return,
				sum(case when a.transaction_type in(6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as transfer_out
				from inv_transaction a, product_details_master b
				where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id=4 and  a.item_category=4 $company_id $item $prod_cond  group by b.id, b.item_description,b.item_size,b.item_group_id, b.current_stock, b.avg_rate_per_unit, a.store_id order by b.id ASC"; 
				//echo $sql;	
                $result = sql_select($sql);
				$i=1; $total_amount=0;
                foreach($result as $row)
                {
                   if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
					$ageOfDays = datediff("d",$date_array[$row[csf("id")]]['min_date'],date("Y-m-d"));
					$daysOnHand = datediff("d",$date_array[$row[csf("id")]]['max_date'],date("Y-m-d")); 
    
                    $opening_bal=$row[csf("rcv_total_opening")]-$row[csf("iss_total_opening")];

                    $receive = $row[csf("receive")];
                    $issue = $row[csf("issue")];
                    $issue_return=$row[csf("issue_return")];
                    $receive_return=$row[csf("receive_return")];
					$transfer_in=$row[csf("transfer_in")];
                    $transfer_out=$row[csf("transfer_out")];
                  
                    $tot_receive=$receive+$issue_return+$transfer_in;
					$tot_issue=$issue+$receive_return+$transfer_out;
									
					$closingStock=$opening_bal+$tot_receive-$tot_issue;
					if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $closingStock>$txt_qnty) || ($get_upto_qnty==2 && $closingStock<$txt_qnty) || ($get_upto_qnty==3 && $closingStock>=$txt_qnty) || ($get_upto_qnty==4 && $closingStock<=$txt_qnty) || ($get_upto_qnty==5 && $closingStock==$txt_qnty) || $get_upto_qnty==0))
					{
						$amount=$closingStock*$row[csf("avg_rate_per_unit")];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="40"><? echo $i; ?></td>	
                            <td width="60" align="center"><p><? echo $row[csf("id")]; ?></p></td>
                            <td width="120"><p><? echo $trim_group[$row[csf('item_group_id')]]; ?></p></td>
                            <td width="180" style="word-break: break-all; word-wrap:break-word "><p><? echo $row[csf("item_description")]; ?></p></td>
                            <td width="100" style="word-break: break-all; word-wrap:break-word"><p><? echo $row[csf("item_size")]; ?></p></td>
                            <td width="100"><p><? echo $store_arr[$row[csf("store_id")]]; ?></p></td>       
                            <td width="110" align="right"><p><? echo number_format($opening_bal,2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($receive,2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($issue_return,2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($transfer_in,2); ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($tot_receive,2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($issue,2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($receive_return,2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($transfer_out,2); ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($tot_issue,2); ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($closingStock,2); ?></p></td>
                            <td width="80" align="right"><? echo number_format($row[csf("avg_rate_per_unit")],2); ?></td>
                            <td width="100" align="right"><p><? echo number_format($amount,2); ?></p></td>
                            <td width="80" align="center"><? echo $ageOfDays; ?></td>
                            <td align="center"><? echo $daysOnHand; ?></td>
						</tr>
						<? 
						$total_opening+=$opening_bal;
						$total_receive+=$receive;
						$total_issue_return+=$issue_return;
						$total_transfer_in+=$transfer_in;
						$total_receive_balance+=$tot_receive;
						$total_issue+=$issue;
						$total_receive_return+=$receive_return;
						$total_transfer_out+=$transfer_out;
						$total_issue_balance+=$tot_issue;
						$total_closing_stock+=$closingStock;
						$total_amount+=$amount;								
						$i++; 				
					}
				}
				?>
            </table>
		</div> 
        <table width="1820" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" > 
           <tr>
                <td width="40">&nbsp;</td>
                <td width="60">&nbsp;</td> 
                <td width="120">&nbsp;</td> 
                <td width="180" align="right">Total</td>
                <td width="100">&nbsp;</td> 
                <td width="100">&nbsp;</td> 
                <td width="110" align="right" id="value_total_opening_td"><? echo number_format($total_opening,2); ?></td>
                <td width="80" align="right" id="value_total_receive_td"><? echo number_format($total_receive,2); ?></td>
                <td width="80" align="right" id="value_total_issue_return_td"><? echo number_format($total_issue_return,2); ?></td>
                <td width="80" align="right" id="value_total_transfer_in"><? echo number_format($total_transfer_in,2); ?></td>
                <td width="100" align="right" id="value_total_receive_balance_td"><? echo number_format($total_receive_balance,2); ?></td>
                <td width="80" align="right" id="value_total_issue_td"><? echo number_format($total_issue,2); ?></td>
                <td width="80" align="right" id="value_total_receive_return_td"><? echo number_format($total_receive_return,2); ?></td>
                <td width="80" align="right" id="value_total_transfer_out"><? echo number_format($total_transfer_out,2); ?></td>
                <td width="100" align="right" id="value_total_issue_balance_td"><? echo number_format($total_issue_balance,2); ?></td>
                <td width="100" align="right" id="value_total_closing_stock_td"><? echo number_format($total_closing_stock,2); ?></td>
                <td width="80">&nbsp;</td>
                <td width="100" align="right" id="value_total_closing_amnt"><? echo number_format($total_amount,2); ?></td>
                <td>&nbsp;</td>
            </tr>
        </table>
        </div>
         <?
	
        } else if ($report_type == 3) 
            {
            	ob_start();
                ?>
            <div>
                <table style="width:1320px" border="1" cellpadding="2" cellspacing="0"  id="caption" rules="all"> 
                    <thead>
                        <tr class="form_caption" style="border:none;">
                            <td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td> 
                        </tr>
                        <tr class="form_caption" style="border:none;">
                            <td colspan="14" align="center" style="border:none;font-size:12px; font-weight:bold">
                                <p>Monthly Closing Value status</p>
                            </td>
                        </tr>
                        <tr class="form_caption" style="border:none;">
                            <td colspan="14" align="center" style="border:none;font-size:12px; font-weight:bold">
                                <? if ($from_date != "" || $to_date != "") echo "From : " . change_date_format($from_date) . " To : " . change_date_format($to_date) . ""; ?>
                            </td>
                        </tr>
                    </thead>
                </table>
                <table width="1320" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <thead>
                        <tr>
                             <th rowspan="2" width="40">SL</th>
                             <th rowspan="2" width="100">Company Name</th>
                             <th rowspan="2" width="100">Opening Value TK</th>
                             <th colspan="4">Receive</th>
                             <th colspan="5">Issue</th>
                             <th rowspan="2" width="100">Closing Value TK</th>
                             <th rowspan="2" width="80">Previous Month Consumption value TK</th>
                        </tr> 
                        <tr>                         
                             <th width="80">Purchase Value TK</th>
                             <th width="100">Transfer In Value TK</th>
                             <th width="80">Issue Return Value TK</th>
                             <th width="100">Total Rcv Value TK</th>
                             <th width="80">Consumption Value TK</th>
                             <th width="100">Transfer Out Value TK</th>
                             <th width="80">Rcv Return Value TK</th>
                             <th width="100">Other Issue Value TK</th>
                             <th width="100">Total Issue Value TK</th>
                        </tr> 
                     </thead>
                </table>
                  <div style="width:1340px; max-height:350px;overflow-y:scroll" id="scroll_body" > 
                      <table width="1320" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
                          <? 
                          $companyArr = return_library_array("select id,company_name from lib_company","id","company_name"); 
                          $issue_sql= " select d.cons_amount as iss_amount,e.company_id, d.id as trans_id
                            from inv_issue_master c, inv_transaction d,  product_details_master e
                            where c.id = d.mst_id and d.prod_id = e.id
                            and c.issue_purpose in (4,8,36)
                            and d.transaction_date between '".$from_date."' and '".$to_date."'
                            and d.transaction_type = 2 and c.entry_form = 25 and d.item_category = 4
                            and e.item_category_id = 4 and c.status_active = 1 and c.is_deleted = 0
                            and d.status_active = 1 and d.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0
                            order by e.company_id";
                           $iss_result=  sql_select($issue_sql);
                           $iss_array= array();
                           $trans_check = array();
                          foreach($iss_result as $iss_row){
                              if(empty($trans_check[$iss_row[csf("trans_id")]])){
                                $trans_check[$iss_row[csf("trans_id")]] = $iss_row[csf("trans_id")];
                                if(empty($iss_array[$iss_row[csf("company_id")]])){
                                    $iss_array[$iss_row[csf("company_id")]] = $iss_row[csf("iss_amount")];
                                }
                                else{
                                    $iss_array[$iss_row[csf("company_id")]] += $iss_row[csf("iss_amount")];
                                }
                              }
                          }
                         
                                                
                            $sql = "select a.company_id,
                            sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as rcv_total_opening,
                            sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as iss_total_opening,
                            sum(case when a.transaction_type in(1) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as receive,
                            sum(case when a.transaction_type in(2) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as issue,
                            sum(case when a.transaction_type in(3) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as rcv_return,
                            sum(case when a.transaction_type in(4) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as iss_return,
                            sum(case when a.transaction_type in(5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as trans_in,
                            sum(case when a.transaction_type in(6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as trans_out
                            from inv_transaction a, product_details_master b
                            where a.prod_id = b.id and b.item_category_id = 4 and b.entry_form = 24 and a.status_active = 1 and a.is_deleted = 0
                            and b.status_active = 1 and b.is_deleted = 0 and a.item_category = 4 $company_id
                            group by a.company_id
                            order by a.company_id";
                            $result=  sql_select($sql);
                             $sl = 1; 
                             foreach($result as $row){
                              
                              $opening_value=  $row[csf("rcv_total_opening")] - $row[csf("iss_total_opening")];
                              $rcv_total = $row[csf("receive")]+$row[csf("trans_in")]+$row[csf("iss_return")];
                              $issue_total= $row[csf("issue")]+$row[csf("trans_out")]+$row[csf("rcv_return")];
                              $closing_value= $opening_value + $rcv_total - $issue_total;
                              $issue = $iss_array[$row[csf("company_id")]];
                              $other_issue = $row[csf("issue")] - $iss_array[$row[csf("company_id")]];
                               if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
                               if(((($value_with ==1) && (number_format($closing_value,2) > 0.00))||($value_with ==0)) && ( (number_format($opening_value,2) > 0.00) || (number_format($rcv_total,2) > 0.00) || (number_format($issue_total,2) > 0.00) ) )
                               {
                              ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
                                <td width="40"><? echo $sl;?></td>
                                <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: center; "><? echo $companyArr[$row[csf("company_id")]];?></td>
                                <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($opening_value,2);?></p></td>
                                <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($row[csf("receive")],2);?></p></td>
                                <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo $row[csf("trans_in")];?></p></td>
                                <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo $row[csf("iss_return")];?></p></td>
                                <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($rcv_total,2);?></p></td>
                                <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($issue,2);?></p></td>
                                <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo $row[csf("trans_out")];?></p></td>
                                <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo $row[csf("rcv_return")];?></p></td>
                                <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($other_issue,2);?></p></td>
                                <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($issue_total,2);?></p></td>
                                <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($closing_value,2);?></p></td>
                                <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($row[csf("iss_total_opening")],2);?></p></td>
                            </tr>
                          <?
                          $sl++;
                            $grand_opening += $opening_value;
                            $grand_rcv += $row[csf("receive")];
                            $grand_trans_in += $row[csf("trans_in")];
                            $grand_iss_return += $row[csf("iss_return")];
                            $grand_rcv_total += $rcv_total;
                            $grand_issue += $issue;
                            $grand_trans_out += $row[csf("trans_out")];
                            $grand_rcv_return += $row[csf("rcv_return")];
                            $grand_other_issue += $other_issue;
                            $grand_issue_total += $issue_total;
                            $grand_closing_value += $closing_value;
                            $grand_pre_issue += $row[csf("iss_total_opening")];
                             }
                             }
                          ?>
                      </table>
                      </div>
                    <table width="1320" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" > 
                        <tr>
                            <td style="word-break: break-all; word-wrap:break-word;width: 40px;text-align: right;"><p>&nbsp;</p></td>
                            <td style="word-break: break-all; word-wrap:break-word;width: 100px;text-align: right;"><p>Grand Total=</p></td>
                            <td width="100" title="opening" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_opening,2);?></p></td>
                            <td width="80" title="rcv" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_rcv,2);?></p></td>
                            <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_trans_in,2);?></p></td>
                            <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_iss_return,2);?></p></td>
                            <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_rcv_total,2);?></p></td>
                             <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_issue,2);?></p></td>
                             <td width="100" title="tr_out" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_trans_out,2);?></p></td>
                             <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_rcv_return,2);?></p></td>
                             <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_other_issue,2)?></p></td>
                             <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_issue_total,2);?></p></td>
                             <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_closing_value,2);?></p></td>
                             <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_pre_issue,2);?></p></td>
                        </tr> 
                    </table>
            </div>
                
         <?       
        } 
        else if($report_type == 4)
        {
            ob_start();
            ?>
            <div>
            <table style="width:1320px" border="1" cellpadding="2" cellspacing="0"  id="caption" rules="all"> 
                    <thead>
                        <tr class="form_caption" style="border:none;">
                            <td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td> 
                        </tr>
                        <tr class="form_caption" style="border:none;">
                            <td colspan="14" align="center" style="border:none;font-size:12px; font-weight:bold">
                                <p>Monthly Closing Value status</p>
                            </td>
                        </tr>
                        <tr class="form_caption" style="border:none;">
                            <td colspan="14" align="center" style="border:none;font-size:12px; font-weight:bold">
                                <? if ($from_date != "" || $to_date != "") echo "From : " . change_date_format($from_date) . " To : " . change_date_format($to_date) . ""; ?>
                            </td>
                        </tr>
                    </thead>
                </table>
                <table width="1320" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <thead>
                        <tr>
                             <th rowspan="2" width="40">SL</th>
                             <th rowspan="2" width="100">Item Group</th>
                             <th rowspan="2" width="100">Opening Value TK</th>
                             <th colspan="4">Receive</th>
                             <th colspan="5">Issue</th>
                             <th rowspan="2" width="100">Closing Value TK</th>
                             <th rowspan="2" width="80">Previous Month Consumption value TK</th>
                        </tr> 
                        <tr>                         
                             <th width="80">Purchase Value TK</th>
                             <th width="100">Transfer In Value TK</th>
                             <th width="80">Issue Return Value TK</th>
                             <th width="100">Total Rcv Value TK</th>
                             <th width="80">Issue value TK</th>
                             <th width="100">Transfer Out Value TK</th>
                             <th width="80">Rcv Return Value TK</th>
                             <th width="100">Other Issue Value TK</th>
                             <th width="100">Total Issue Value TK</th>
                        </tr> 
                     </thead>
                </table>
            <div style="width:1340px; max-height:350px;overflow-y:scroll" id="scroll_body" > 
            <table width="1320" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
            <?
            $iss_sql = " select d.cons_amount as iss_amount,e.item_group_id, d.id as trans_id
            from inv_issue_master c, inv_transaction d, product_details_master e 
            where c.id = d.mst_id and d.prod_id = e.id and c.issue_purpose in (6) 
            and d.transaction_date between '".$from_date."' and '".$to_date."' 
            and d.transaction_type = 2 and c.entry_form = 21 and d.item_category = 4 and e.item_category_id = 4 and d.company_id = '$cbo_company_name'
            and c.status_active = 1 and c.is_deleted = 0 
            and d.status_active = 1 and d.is_deleted = 0 
            and e.status_active = 1 and e.is_deleted = 0 
            order by e.item_group_id";
            $iss_result = sql_select($iss_sql);
            $iss_arr = array();$trans_check = array();
            foreach($iss_result as $i_row){
                if(empty($trans_check[$i_row[csf("trans_id")]])){
                    $trans_check[$i_row[csf("trans_id")]] = $i_row[csf("trans_id")];
                    if(empty($iss_arr[$i_row[csf("item_group_id")]])){
                        $iss_arr[$i_row[csf("item_group_id")]] = $i_row[csf("iss_amount")];
                    }else{
                        $iss_arr[$i_row[csf("item_group_id")]] += $i_row[csf("iss_amount")];
                    }
                }
            }
            //print_r($iss_arr);
        
            $sql = "select b.item_group_id,
             sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as rcv_total_opening,
             sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as iss_total_opening,
             sum(case when a.transaction_type in(1) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as receive,
             sum(case when a.transaction_type in(2) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as issue,
             sum(case when a.transaction_type in(3) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as rcv_return,
             sum(case when a.transaction_type in(4) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as iss_return,
             sum(case when a.transaction_type in(5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as trans_in,
             sum(case when a.transaction_type in(6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as trans_out
             from inv_transaction a, product_details_master b
             where a.prod_id = b.id and b.item_category_id = 4 and b.entry_form = 20 and a.status_active = 1 and a.is_deleted = 0
             and b.status_active = 1 and b.is_deleted = 0 and a.item_category = 4 $company_id
             group by b.item_group_id
             order by b.item_group_id";
            $result= sql_select($sql);
            $sl = 1;
            foreach($result as $row){
                $opening_value=  $row[csf("rcv_total_opening")] - $row[csf("iss_total_opening")];
                $rcv_total = $row[csf("receive")]+$row[csf("trans_in")]+$row[csf("iss_return")];
                $issue_total= $row[csf("issue")]+$row[csf("trans_out")]+$row[csf("rcv_return")];
                $closing_value= $opening_value + $rcv_total - $issue_total;
                $issue = $iss_arr[$row[csf("item_group_id")]];
                $other_issue = $row[csf("issue")] - $iss_arr[$row[csf("item_group_id")]];
                if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
                    <td width="40"><? echo $sl;?></td>
                    <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: center; "><? echo $trim_group[$row[csf("item_group_id")]];?></td>
                    <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($opening_value,2);?></p></td>
                    <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($row[csf("receive")],2);?></p></td>
                    <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo $row[csf("trans_in")];?></p></td>
                    <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo $row[csf("iss_return")];?></p></td>
                    <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($rcv_total,2);?></p></td>
                    <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($issue,2);?></p></td>
                    <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo $row[csf("trans_out")];?></p></td>
                    <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo $row[csf("rcv_return")];?></p></td>
                    <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($other_issue,2);?></p></td>
                    <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($issue_total,2);?></p></td>
                    <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($closing_value,2);?></p></td>
                    <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($row[csf("iss_total_opening")],2);?></p></td>
                </tr>
                
             <?   
             $sl++;
            $grand_opening += $opening_value;
            $grand_rcv += $row[csf("receive")];
            $grand_trans_in += $row[csf("trans_in")];
            $grand_iss_return += $row[csf("iss_return")];
            $grand_rcv_total += $rcv_total;
            $grand_issue += $issue;
            $grand_trans_out += $row[csf("trans_out")];
            $grand_rcv_return += $row[csf("rcv_return")];
            $grand_other_issue += $other_issue;
            $grand_issue_total += $issue_total;
            $grand_closing_value += $closing_value;
            $grand_pre_issue += $row[csf("iss_total_opening")];
            }
            ?>
            </table>
            </div>
            <table width="1320" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" > 
                <tr>
                    <td style="word-break: break-all; word-wrap:break-word;width: 40px;text-align: right;"><p>&nbsp;</p></td>
                    <td style="word-break: break-all; word-wrap:break-word;width: 100px;text-align: right;"><p>Grand Total=</p></td>
                    <td width="100" title="opening" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_opening,2);?></p></td>
                    <td width="80" title="rcv" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_rcv,2);?></p></td>
                    <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_trans_in,2);?></p></td>
                    <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_iss_return,2);?></p></td>
                    <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_rcv_total,2);?></p></td>
                     <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_issue,2);?></p></td>
                     <td width="100" title="tr_out" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_trans_out,2);?></p></td>
                     <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_rcv_return,2);?></p></td>
                     <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_other_issue,2)?></p></td>
                     <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_issue_total,2);?></p></td>
                     <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_closing_value,2);?></p></td>
                     <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_pre_issue,2);?></p></td>
                </tr> 
            </table>
            </div>
            <?
        }
        else if($report_type == 5)
        {
                   if($cbo_store_name > 0){
                 $trans_store_wise_sql="select c.buyer_name,a.id as trans_id,a.store_id,
                        (case when a.transaction_type = 5 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as trans_in,
                        (case when a.transaction_type = 6 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as trans_out
                        from inv_transaction a, order_wise_pro_details b, wo_po_details_master c, wo_po_break_down d
                        where a.id = b.trans_id and b.po_breakdown_id = d.id and c.job_no = d.job_no_mst
                        and b.entry_form = 112 and a.item_category = 4
                        and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
                        and c.status_active = 1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0
                        and a.store_id = '$cbo_store_name' $company_id
                        order by c.buyer_name";
                    $trans_result= sql_select($trans_store_wise_sql);
                    $trans_check= array();$trans_store_arr= array();
                    foreach($trans_result as $tr_row){
                        if(empty($trans_check[$tr_row[csf("trans_id")]])){
                            $trans_check[$tr_row[csf("trans_id")]] = $tr_row[csf("trans_id")];
                            if(empty($trans_store_arr[$tr_row[csf("buyer_name")]]["tr_in"])){
                                $trans_store_arr[$tr_row[csf("buyer_name")]]["tr_in"] =  $tr_row[csf("trans_in")];
                            }else{
                                $trans_store_arr[$tr_row[csf("buyer_name")]]["tr_in"] +=  $tr_row[csf("trans_in")];
                            }
                            if(empty($trans_store_arr[$tr_row[csf("buyer_name")]]["tr_out"])){
                                $trans_store_arr[$tr_row[csf("buyer_name")]]["tr_out"] =  $tr_row[csf("trans_out")];
                            }else{
                                $trans_store_arr[$tr_row[csf("buyer_name")]]["tr_out"] +=  $tr_row[csf("trans_out")];
                            }
                            
                        }
                    }
//                    echo "<pre>";
//                    print_r($trans_store_arr);
//                    echo "</pre>";
                    
                    
                    }
        $buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
        $iss_sql= " select a.cons_amount as iss_amount,a.company_id, e.buyer_name, a.id as trans_id 
                        from inv_transaction a, inv_issue_master b,  product_details_master c, order_wise_pro_details d, wo_po_details_master e, wo_po_break_down f 
                        where b.id = a.mst_id and a.prod_id = c.id and b.issue_purpose in (4,8,36) 
                        and a.transaction_date between '".$from_date."' and '".$to_date."' 
                        and a.transaction_type = 2 and b.entry_form = 25 and a.item_category = 4 
                        and c.item_category_id = 4 $company_id 
                        and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 
                        and c.status_active = 1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0
                        and e.status_active = 1 and e.is_deleted = 0 and f.status_active = 1 and f.is_deleted = 0 
                        and a.id = d.trans_id and d.po_breakdown_id = f.id and e.job_no = f.job_no_mst 
                        order by a.company_id ";
       $iss_result= sql_select($iss_sql);
       $iss_transcheck = array();$iss_buyer_arr = array();
       foreach($iss_result as $row){
           if(empty($iss_transcheck[$row[csf("trans_id")]])){
               $iss_transcheck[$row[csf("trans_id")]] = $row[csf("trans_id")];
               if($iss_buyer_arr[$row[csf("buyer_name")]]){
                    $iss_buyer_arr[$row[csf("buyer_name")]] = $row[csf("iss_amount")];
               }else{
                   $iss_buyer_arr[$row[csf("buyer_name")]] += $row[csf("iss_amount")];
               }
           }
       }
       //print_r($iss_buyer_arr);die;
       $sql = "select a.id as trans_id, d.buyer_name,
                (case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as rcv_opening,
                (case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as iss_opening,
                (case when a.transaction_type in(1) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as receive,
                (case when a.transaction_type in(2) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as tot_issue,
                (case when a.transaction_type in(3) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as rcv_return,
                (case when a.transaction_type in(4) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as iss_return,
                (case when a.transaction_type in(5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as trans_in,
                (case when a.transaction_type in(6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as trans_out
                from inv_transaction a, product_details_master b, order_wise_pro_details c, wo_po_details_master d, wo_po_break_down e
                where a.prod_id = b.id and a.id = c.trans_id and c.po_breakdown_id = e.id and d.job_no = e.job_no_mst
                and a.item_category = 4 and b.item_category_id = 4 and b.entry_form = 24 $company_id 
                and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 
                and c.status_active = 1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0
                and e.status_active = 1 and e.is_deleted = 0 
                order by d.buyer_name";
            $result = sql_select($sql);
            $transId_check = array();
              foreach($result as $row){
                     if(empty($transId_check[$row[csf("trans_id")]])){
                         $transId_check[$row[csf("trans_id")]] = $row[csf("trans_id")];
                         if(empty($data_array[$row[csf("buyer_name")]]["rcv_opening"])){
                            $data_array[$row[csf("buyer_name")]]["rcv_opening"] = $row[csf("rcv_opening")];
                         }else{
                            $data_array[$row[csf("buyer_name")]]["rcv_opening"] += $row[csf("rcv_opening")];
                         }
                          if(empty($data_array[$row[csf("buyer_name")]]["iss_opening"])){
                              $data_array[$row[csf("buyer_name")]]["iss_opening"] = $row[csf("iss_opening")];
                          }else{
                               $data_array[$row[csf("buyer_name")]]["iss_opening"] += $row[csf("iss_opening")];
                          }
                          if(empty($data_array[$row[csf("buyer_name")]]["receive"])){
                              $data_array[$row[csf("buyer_name")]]["receive"] = $row[csf("receive")];
                          }else{
                              $data_array[$row[csf("buyer_name")]]["receive"] += $row[csf("receive")];
                          }
                          if(empty($data_array[$row[csf("buyer_name")]]["tot_issue"])){
                              $data_array[$row[csf("buyer_name")]]["tot_issue"] = $row[csf("tot_issue")];
                          }else{
                              $data_array[$row[csf("buyer_name")]]["tot_issue"] += $row[csf("tot_issue")];
                          }
                          if(empty($data_array[$row[csf("buyer_name")]]["rcv_return"])){
                              $data_array[$row[csf("buyer_name")]]["rcv_return"] = $row[csf("rcv_return")];
                          }else{
                              $data_array[$row[csf("buyer_name")]]["rcv_return"] += $row[csf("rcv_return")];
                          }
                          if(empty($data_array[$row[csf("buyer_name")]]["iss_return"])){
                              $data_array[$row[csf("buyer_name")]]["iss_return"] = $row[csf("iss_return")];
                          }else{
                              $data_array[$row[csf("buyer_name")]]["iss_return"] += $row[csf("iss_return")];
                          }
                          if(empty($data_array[$row[csf("buyer_name")]]["trans_in"])){
                              $data_array[$row[csf("buyer_name")]]["trans_in"] = $row[csf("trans_in")];
                          }else{
                              $data_array[$row[csf("buyer_name")]]["trans_in"] += $row[csf("trans_in")];
                          }
                          if(empty($data_array[$row[csf("buyer_name")]]["trans_out"])){
                              $data_array[$row[csf("buyer_name")]]["trans_out"] = $row[csf("trans_out")];
                          }else{
                              $data_array[$row[csf("buyer_name")]]["trans_out"] += $row[csf("trans_out")];
                          }

                        }

                     }
//                         echo "<pre>";
//                         print_r($data_array);
//                         echo "</pre>";
        if($cbo_store_name != 0){
            $rcv_colspan = 5;
            $iss_colspan = 6;
            $width = 1520;
            $tbl_colspan = 16;
        }else{
            $rcv_colspan = 4;
            $iss_colspan = 5;
            $width = 1320;
            $tbl_colspan = 14;
        }
        ob_start();
        ?>
        <div>
            <table style="width:<? echo $width;?>px" border="1" cellpadding="2" cellspacing="0"  id="caption" rules="all"> 
                <thead>
                    <tr class="form_caption" style="border:none;">
                        <td colspan="<? echo $tbl_colspan;?>" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td> 
                    </tr>
                    <tr class="form_caption" style="border:none;">
                        <td colspan="<?echo $tbl_colspan;?>" align="center" style="border:none;font-size:12px; font-weight:bold">
                            <p>Monthly Closing Value status</p>
                        </td>
                    </tr>
                    <tr class="form_caption" style="border:none;">
                        <td colspan="<?echo $tbl_colspan;?>" align="center" style="border:none;font-size:12px; font-weight:bold">
                            <? if ($from_date != "" || $to_date != "") echo "From : " . change_date_format($from_date) . " To : " . change_date_format($to_date) . ""; ?>
                        </td>
                    </tr>
                </thead>
            </table>

        <table width="<? echo $width;?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                <thead>
                    <tr>
                         <th rowspan="2" width="40">SL</th>
                         <th rowspan="2" width="100">Buyer Name</th>
                         <th rowspan="2" width="100">Opening Value TK</th>
                         <th colspan="<? echo $rcv_colspan;?>">Receive</th>
                         <th colspan="<? echo $iss_colspan;?>">Issue</th>
                         <th rowspan="2" width="100">Closing Value TK</th>
                         <th rowspan="2" width="">Previous Month Consumption value TK</th>
                    </tr> 
                    <tr>                         
                         <th width="80">Purchase Value TK</th>
                         <th width="100"><p>Order to Order Transfer In Value TK</p></th>
                        <? if($cbo_store_name > 0){ ?>
                         <th width="100"><p>Store To Store Transfer In Value TK</p></th>
                        <? }?>
                         <th width="80"><p>Issue Return Value TK</p></th>
                         <th width="100"><p>Total Value TK</p></th>
                         <th width="80"><p>Issue Value TK</p></th>
                         <th width="100"><p>Order to Order Transfer Out Value TK</p></th>
                         <? if($cbo_store_name > 0){ ?>
                         <th width="100"><p>Store To Store Transfer Out Value TK</p></th>
                         <? }?>
                         <th width="80">Rcv Return Value TK</th>
                         <th width="100"><p>Other Issue Value TK</p></th>
                         <th width="100"><p>Total Issue Value TK</p></th>
                    </tr> 
                 </thead>
            </table>
         <div style="width:<? echo $width + 20?>px; max-height:350px;overflow-y:scroll" id="scroll_body" > 
             <table width="<? echo $width;?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
                 <? 
                 $sl = 1; 
               foreach($data_array as $buyer_id => $trans_type){
                        $opening_value  = $trans_type["rcv_opening"] - $trans_type["iss_opening"];
                         if($cbo_store_name > 0) {
                             $trans_in = $trans_type["trans_in"] -  $trans_store_arr[$buyer_id]["tr_in"];
                             $trans_out = $trans_type["trans_out"] - $trans_store_arr[$buyer_id]["tr_out"];
                             $store_trans_in = $trans_store_arr[$buyer_id]["tr_in"];
                             $store_trans_out = $trans_store_arr[$buyer_id]["tr_out"];
                         }else{
                             $trans_in = $trans_type["trans_in"];
                             $trans_out = $trans_type["trans_out"];
                             $store_trans_in = 0;
                             $store_trans_out = 0;
                         }
                        $rcv_total =  $opening_value + $trans_type["receive"] + $trans_type["iss_return"] + $trans_in + $store_trans_in;
                        $other_issue= $trans_type["tot_issue"] - $iss_buyer_arr[$buyer_id];
                        
                        $issue_total = $trans_type["tot_issue"] + $trans_type["rcv_return"] + $trans_out + $store_trans_out;
                        $closing_value = $opening_value + $rcv_total - $issue_total;
                         if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
                         ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
                            <td width="40"><? echo $sl;?></td>
                            <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: center; "><? echo $buyer_arr[$buyer_id];?></td>
                            <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($opening_value,2);?></p></td>
                            <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($trans_type["receive"],2);?></p></td>
                            <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($trans_in,2);?></p></td>
                            <? if($cbo_store_name > 0) {?>
                            <td width="100" title="store_tra_in" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p>&nbsp;<? echo number_format($store_trans_in,2);?></p></td>
                            <? }?>
                            <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($trans_type["iss_return"],2);?></p></td>
                            <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($rcv_total,2);?></p></td>
                            <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($iss_buyer_arr[$buyer_id],2);?></p></td>
                            <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($trans_out,2);?></p></td>
                            <? if($cbo_store_name > 0) {?>
                            <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p>&nbsp;<? echo number_format($store_trans_out,2);?></p></td>
                            <? }?>
                            <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($trans_type["rcv_return"],2);?></p></td>
                            <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($other_issue,2);?></p></td>
                            <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($issue_total,2);?></p></td>
                            <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($closing_value,2);?></p></td>
                            <td width="" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($trans_type["iss_opening"],2);?></p></td>
                        </tr>
                        <?
                        $sl++;
                            $grand_opening += $opening_value;
                             $grand_rcv += $trans_type["receive"];
                             $grand_trans_in += $trans_type["trans_in"];
                             $grand_iss_return += $trans_type["iss_return"];
                             $grand_rcv_total += $rcv_total; 
                             $grand_issue += $iss_buyer_arr[$buyer_id];
                             $grand_trans_out += $trans_type["trans_out"];
                             $grand_rcv_return += $trans_type["rcv_return"];
                             $grand_other_issue += $other_issue;
                             $grand_issue_total += $issue_total;
                             $grand_closing_value += $closing_value;
                             $grand_pre_issue += $trans_type["iss_opening"];
                 }
                 ?>
             </table>
         </div>
            <table width="<? echo $width;?>" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" > 
                <tr>
                    <td width="40" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p>&nbsp;</p></td>
                    <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p>Grand Total=</p></td>
                    <td width="100" title="opening" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_opening,2);?></p></td>
                    <td width="80" title="rcv" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_rcv,2);?></p></td>
                    <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_trans_in,2);?></p></td>
                    <? if($cbo_store_name > 0){?>
                    <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p>&nbsp;<? ?></p></td>
                    <? }?>
                    <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_iss_return,2);?></p></td>
                    <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_rcv_total,2);?></p></td>
                     <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_issue,2);?></p></td>
                     <td width="100" title="tr_out" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_trans_out,2);?></p></td>
                     <? if($cbo_store_name > 0){?>
                     <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p>&nbsp;<? ?></p></td>
                    <? }?>
                     <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_rcv_return,2);?></p></td>
                     <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_other_issue,2)?></p></td>
                     <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_issue_total,2);?></p></td>
                     <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_closing_value,2);?></p></td>
                     <td width="" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_pre_issue,2);?></p></td>
                </tr> 
            </table>
        </div>
    <?
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
    echo "$html**$filename**$report_type"; 
    exit();
}
?>