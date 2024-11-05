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
 //$trim_group= return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	if ($data[1]==0) $item_name =""; else $item_name =" and item_group_id in($data[1])";
	$sql="SELECT id, item_group_id,item_category_id, item_description from product_details_master where company_id=$data[0] and item_category_id=4 and status_active=1 and is_deleted=0 $item_name"; 
	$arr=array(0=>$trim_group,3=>$item_category);
	echo  create_list_view("list_view", "Item Group,Description,Product ID", "150,300,150","600","300",0, $sql , "js_set_value", "id,item_description,item_group_id", "", 1, "item_group_id,0,0", $arr , "item_group_id,item_description,id", "",'setFilterGrid("list_view",-1);','0,0,0','',1) ;
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

        $pre_from_date = date('d-M-Y',strtotime('first day of last month',strtotime($from_date)));
        $pre_to_date = date('d-M-Y',strtotime('last day of previous month',strtotime($from_date)));
        
	if($db_type==0) 
	{       
		$from_date=change_date_format($from_date,'yyyy-mm-dd');
		$to_date=change_date_format($to_date,'yyyy-mm-dd');
                $pre_from_date=change_date_format($pre_from_date,'yyyy-mm-dd');
		$pre_to_date=change_date_format($pre_to_date,'yyyy-mm-dd');
	}
	else if($db_type==2) 
	{               
		$from_date=change_date_format($from_date,'','',1);
		$to_date=change_date_format($to_date,'','',1);
        $pre_from_date=change_date_format($pre_from_date,'','',1);
		$pre_to_date=change_date_format($pre_to_date,'','',1);
	}
	else  
	{
		$from_date=""; $to_date="";
	}
	//echo $from_date."==".$to_date; die;
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
		$date_array=array();
		$returnRes_date="select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category=4 group by prod_id";
		$result_returnRes_date = sql_select($returnRes_date);
		foreach($result_returnRes_date as $row)	
		{
			$date_array[$row[csf("prod_id")]]['min_date']=$row[csf("min_date")];
			$date_array[$row[csf("prod_id")]]['max_date']=$row[csf("max_date")];
		}
		unset($result_returnRes_date);
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
			 
			  	$sql="Select b.id, b.item_description,b.item_group_id, b.current_stock, b.avg_rate_per_unit, a.store_id,b.item_size,
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
				where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id=4 and a.item_category=4 $company_id $item $prod_cond $search_cond  $store_cond and b.entry_form in ( 20, 24)
				group by b.id, b.item_description,b.item_size,b.item_group_id, b.current_stock, b.avg_rate_per_unit, a.store_id,a.company_id order by b.id ASC";
				 
				//echo $sql;
				 	
                $result = sql_select($sql);
				$i=1; $total_amount=0;
                foreach($result as $row)
				{
				   if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
					$ageOfDays = datediff("d",$date_array[$row[csf("id")]]['min_date'],date("Y-m-d"));
					$daysOnHand = datediff("d",$date_array[$row[csf("id")]]['max_date'],date("Y-m-d")); 
	
					$opening_bal=$row[csf("rcv_total_opening")]-$row[csf("iss_total_opening")];                    
					$openingRate=$openingBalanceValue=0;
					
					/*
					if($row[csf("rcv_total_opening")] > 0)
					{
						$openingRate = $row[csf("rcv_total_opening_amt")] / $row[csf("rcv_total_opening")];
					}
					$openingBalanceValue = $opening_bal*$openingRate;
					*/
					
					$openingBalanceValue = $row[csf("rcv_total_opening_amt")]-$row[csf("iss_total_opening_amt")];
					if($opening_bal>0) 
					{
						$openingRate=$openingBalanceValue/$opening_bal;
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
					$closingRate=$amount=0;
					$amount= ($openingBalanceValue + $purchase_amount_value) - $issue_amount_value;
					if(number_format($closingStock,2,'.','')>0)
					{
						$closingRate = $amount/$closingStock;
					}
					
					

					$rept_data[$row[csf("id")]]['item_group_id']=$row[csf("item_group_id")];
					$rept_data[$row[csf("id")]]['item_description']=$row[csf("item_description")];
					$rept_data[$row[csf("id")]]['store_id']=$row[csf("store_id")];
					$rept_data[$row[csf("id")]]['opening_qnty']+=$opening_bal;
					$rept_data[$row[csf("id")]]['opening']+=$openingBalanceValue;
					
					$rept_data[$row[csf("id")]]['company']=$row[csf("company_id")];
					$rept_data[$row[csf("id")]]['receive']+=$receive;
					$rept_data[$row[csf("id")]]['iss_return']+=$issue_return;
					$rept_data[$row[csf("id")]]['trans_in']+=$receive_transfer;
					$rept_data[$row[csf("id")]]['tot_receive']+=$tot_receive;
					$rept_data[$row[csf("id")]]['total_rcv_amt']+=$purchase_amount_value;
					$rept_data[$row[csf("id")]]['issue'] = $row[csf("issue")];
					$rept_data[$row[csf("id")]]['rcv_return']+=$receive_return;
					$rept_data[$row[csf("id")]]['trans_out']+=$issue_transfer;
					$rept_data[$row[csf("id")]]['total_issue']+=$tot_issue;
					$rept_data[$row[csf("id")]]['total_issue_amt']+=$issue_amount_value;
					$rept_data[$row[csf("id")]]['closingStock']+= $closingStock;
					$rept_data[$row[csf("id")]]['closing_value']+= $amount;
					$rept_data[$row[csf("id")]]['ageOfDays']= $ageOfDays;
					$rept_data[$row[csf("id")]]['daysOnHand']= $daysOnHand;
				}
				unset($result);
	
				/*if(((($value_with ==1) && (number_format($closingStock,2) > 0.00))||($value_with ==0)) && ( (number_format($opening_bal,2) > 0.00) || (number_format($tot_receive,2) > 0.00) || (number_format($tot_issue,2) > 0.00) ) ) //|| (number_format($closingStock,2) > 0.00)
				{
					if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $closingStock>$txt_qnty) || ($get_upto_qnty==2 && $closingStock<$txt_qnty) || ($get_upto_qnty==3 && $closingStock>=$txt_qnty) || ($get_upto_qnty==4 && $closingStock<=$txt_qnty) || ($get_upto_qnty==5 && $closingStock==$txt_qnty) || $get_upto_qnty==0))
								{*/
								
					
								/*
				if(($value_with ==1 && (number_format($opening_bal,2) > 0.00 || number_format($closingStock,2) > 0.00 ) )    ||    ($value_with ==0 && (number_format($opening_bal,2) > 0.00 || number_format($closingStock,2) > 0.00 || number_format($tot_receive,2) > 0.00 || number_format($tot_issue,2) > 0.00 ))) 
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
							$total_openingBalanceValue+=$openingBalanceValue;
							$total_receive+=$receive;
							$total_issue_return+=$issue_return;
							$total_receive_transfer+=$receive_transfer;
							$total_receive_balance+=$tot_receive;
							$total_purchase_amount_value+=$purchase_amount_value;
							
							$total_issue+=$issue;
							$total_receive_return+=$receive_return;
							$total_issue_transfer+=$issue_transfer;
							$total_issue_balance+=$tot_issue;
							$total_issue_amount_value+=$issue_amount_value;
							
							$total_closing_stock+=$closingStock;
							$total_amount+=$amount;								
							$i++; 				
							}
						}
						
						*/
					$i=1;
					//echo "<pre>";print_r($rept_data);die;
					foreach($rept_data as $prod_id=>$value)
					{
						
						if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
						
						//if(($value_with ==1 && (number_format($value["opening_qnty"],2) > 0.00 || number_format($value["closingStock"],2) > 0.00 ) )    ||    ($value_with ==0 && (number_format($value["opening_qnty"],2) > 0.00 || number_format($value["closingStock"],2) > 0.00 || number_format($value["tot_receive"],2) > 0.00 || number_format($value["total_issue"],2) > 0.00 )))
						//if(($value_with ==1 && (($value["opening_qnty"] != 0 && $value["opening"] !=0) || ($value["closingStock"] != 0 && $value["closing_value"] !=0)) ) || ($value_with ==0 && (($value["opening_qnty"] != 0 && $value["opening"] !=0) || ($value["closingStock"] != 0 && $value["closing_value"] !=0) || ($value["tot_receive"] != 0 && $value["total_rcv_amt"]) || ($value["total_issue"] != 0 && $value["total_issue_amt"]))))
						//if(($value_with ==1 && ($value["opening_qnty"] != 0 || $value["closingStock"] != 0) ) || ($value_with ==0 && ($value["opening_qnty"] != 0 || $value["closingStock"] != 0 || $value["tot_receive"] != 0 || $value["total_issue"] != 0)))
						
						if(($value_with ==1 && ($value["opening_qnty"] != 0 || $value["opening"] !=0 || $value["closingStock"] != 0 || $value["closing_value"] !=0) ) || ($value_with ==0 && ($value["opening_qnty"] != 0 || $value["opening"] !=0 || $value["closingStock"] != 0 || $value["closing_value"] !=0 || $value["tot_receive"] != 0 || $value["total_rcv_amt"] || $value["total_issue"] != 0 || $value["total_issue_amt"])))
						{
							if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $value["closingStock"]>$txt_qnty) || ($get_upto_qnty==2 && $value["closingStock"]<$txt_qnty) || ($get_upto_qnty==3 && $value["closingStock"]>=$txt_qnty) || ($get_upto_qnty==4 && $value["closingStock"]<=$txt_qnty) || ($get_upto_qnty==5 && $value["closingStock"]==$txt_qnty) || $get_upto_qnty==0))
							{
								$opening_qnty=number_format($value["opening_qnty"],2);
								if($opening_qnty=="-0.00") $opening_qnty=0;
								$opening=number_format($value["opening"],2);
								if($opening=="-0.00") $opening=0;
								$closingStock=number_format($value["closingStock"],2);
								if($closingStock=="-0.00") $closingStock=0;
								$closing_value=number_format($value["closing_value"],2);
								if($closing_value=="-0.00") $closing_value=0;
								
								if($opening_qnty>0)  $openingRate = $value["opening"]/ $value["opening_qnty"] ; else $openingRate =0;
								if($closingStock>0) $closingRate = $value["closing_value"]/$value["closingStock"]; else $closingRate =0;
								?>
                                <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="40"><? echo $i; ?></td>	
								<td width="60" align="center"><p><? echo $prod_id; ?></p></td>
								<td width="120"><p><? echo $trim_group[$value['item_group_id']]; ?></p></td>
								<td width="180" style="word-break:break-all;"><p><? echo $value["item_description"]; ?></p></td>
								<td width="100" style="word-break:break-all;"><p><? echo $store_arr[$value["store_id"]]; ?></p></td>       
								<td width="100" align="right"><p><? echo number_format($openingRate,2); ?></p></td>
								<td width="110" align="right"><p><? echo $opening_qnty; ?></p></td>
								<td width="100" align="right"><p><? echo $opening; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($value["receive"],2); ?></p></td>
								<td width="100" align="right"><p><? echo number_format($value["trans_in"],2); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($value["iss_return"],2); ?></p></td>
								<td width="100" align="right"><p><? echo number_format($value["tot_receive"],2); ?></p></td>
								<td width="100" align="right"><p><?  echo number_format($value["total_rcv_amt"],2); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($value["issue"],2); ?></p></td>
								<td width="100" align="right"><p><? echo number_format($value["trans_out"],2); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($value["rcv_return"],2); ?></p></td>
								<td width="100" align="right"><p><? echo number_format($value["total_issue"],2); ?></p></td>
								<td width="100" align="right"><p><? echo number_format($value["total_issue_amt"],2); ?></p></td>
								<td width="100" align="right"><p><? echo $closingStock; ?></p></td>
								<td width="80" align="right"><? echo number_format($closingRate,2); ?></td>
								<td width="100" align="right"><p><? echo $closing_value; ?></p></td>
								<td width="80" align="center"><? echo $value["ageOfDays"]; ?></td>
								<td align="center"><? echo $value["daysOnHand"]; ?></td>
							</tr>
                                
                                <?
								$total_opening+=$value["opening_qnty"];
								$total_openingBalanceValue+=$value["opening"];
								$total_receive+=$value["receive"];
								$total_issue_return+=$value["iss_return"];
								$total_receive_transfer+=$value["trans_in"];
								$total_receive_balance+=$value["tot_receive"];
								$total_purchase_amount_value+=$value["total_rcv_amt"];
								
								$total_issue+=$value["issue"];
								$total_receive_return+=$value["rcv_return"];
								$total_issue_transfer+=$value["trans_out"];
								$total_issue_balance+=$value["total_issue"];
								$total_issue_amount_value+=$value["total_issue_amt"];
								
								$total_closing_stock+=$value["closingStock"];
								$total_closing_amount+=$value["closing_value"];								
								$i++; 	
							}
						}
						//else
						//{
							/*if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $value["closingStock"]>$txt_qnty) || ($get_upto_qnty==2 && $value["closingStock"]<$txt_qnty) || ($get_upto_qnty==3 && $value["closingStock"]>=$txt_qnty) || ($get_upto_qnty==4 && $value["closingStock"]<=$txt_qnty) || ($get_upto_qnty==5 && $value["closingStock"]==$txt_qnty) || $get_upto_qnty==0))
							{
								
								$openingRate = $value["opening_qnty"] / $value["opening"];
								$closingRate = $value["closing_value"]/$value["closingStock"];
								?>
                                <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="40"><? echo $i; ?></td>	
								<td width="60" align="center"><p><? echo $prod_id; ?></p></td>
								<td width="120"><p><? echo $trim_group[$value['item_group_id']]; ?></p></td>
								<td width="180" style="word-break:break-all;"><p><? echo $value["item_description"]; ?></p></td>
								<td width="100" style="word-break:break-all;"><p><? echo $store_arr[$value["store_id"]]; ?></p></td>       
								<td width="100" align="right"><p><? echo number_format($openingRate,2); ?></p></td>
								<td width="110" align="right"><p><? echo number_format($value["opening_qnty"],2); ?></p></td>
								<td width="100" align="right"><p><? echo number_format($value["opening"],2); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($value["receive"],2); ?></p></td>
								<td width="100" align="right"><p><? echo number_format($value["trans_in"],2); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($value["iss_return"],2); ?></p></td>
								<td width="100" align="right"><p><? echo number_format($value["tot_receive"],2); ?></p></td>
								<td width="100" align="right"><p><?  echo number_format($value["total_rcv_amt"],2); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($value["issue"],2); ?></p></td>
								<td width="100" align="right"><p><? echo number_format($value["trans_out"],2); ?></p></td>
								<td width="80" align="right"><p><? echo number_format($value["rcv_return"],2); ?></p></td>
								<td width="100" align="right"><p><? echo number_format($value["total_issue"],2); ?></p></td>
								<td width="100" align="right"><p><? echo number_format($value["total_issue_amt"],2); ?></p></td>
								<td width="100" align="right"><p><? echo number_format($value["closingStock"],2); ?></p></td>
								<td width="80" align="right"><? echo number_format($closingRate,2); ?></td>
								<td width="100" align="right"><p><? echo number_format($value["closing_value"],2); ?></p></td>
								<td width="80" align="center"><? echo $value["ageOfDays"]; ?></td>
								<td align="center"><? echo $value["daysOnHand"]; ?></td>
							</tr>
                                
                                <?
								$total_opening+=$value["opening_qnty"];
								$total_openingBalanceValue+=$value["opening"];
								$total_receive+=$value["receive"];
								$total_issue_return+=$value["iss_return"];
								$total_receive_transfer+=$value["trans_in"];
								$total_receive_balance+=$value["tot_receive"];
								$total_purchase_amount_value+=$value["total_rcv_amt"];
								
								$total_issue+=$value["issue"];
								$total_receive_return+=$value["rcv_return"];
								$total_issue_transfer+=$value["trans_out"];
								$total_issue_balance+=$value["total_issue"];
								$total_issue_amount_value+=$value["total_issue_amt"];
								
								$total_closing_stock+=$value["closingStock"];
								$total_closing_amount+=$value["closing_value"];								
								$i++; 	
							}*/
						//}
						
					}
					
					//print_r(count($rept_data));die;
				?>
            </table>
		</div> 
        <table width="2181" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" > 
           <tr>
                <td width="40">&nbsp;</td>
                <td width="60">&nbsp;</td> 
                <td width="120">&nbsp;</td> 
                <td width="180" align="right" title="<? echo $i;?>">Total</td>
                <td width="100">&nbsp;</td> 
                <td width="100">&nbsp;</td>
                <td width="110" align="right" id="value_total_opening_td"><? echo number_format($total_opening,2); ?></td>
                <td width="100" id="value_total_opening_value_td"><? echo number_format($total_openingBalanceValue,2); ?></td> 
                <td width="80" align="right" id="value_total_receive_td"><? echo number_format($total_receive,2); ?></td>
                <td width="100" align="right" id="value_total_receive_transfer"><? echo number_format($total_receive_transfer,2); ?></td>
                <td width="80" align="right" id="value_total_issue_return_td"><? echo number_format($total_issue_return,2); ?></td>
                <td width="100" align="right" id="value_total_receive_balance_td"><? echo number_format($total_receive_balance,2); ?></td>
                <td width="100" align="right" id="value_total_receive_value_td"><? echo number_format($total_purchase_amount_value,2); ?></td> 
                <td width="80" align="right" id="value_total_issue_td"><? echo number_format($total_issue,2); ?></td>
                <td width="100" align="right" id="value_total_issue_transfer"><? echo number_format($total_issue_transfer,2); ?></td>
                <td width="80" align="right" id="value_total_receive_return_td"><? echo number_format($total_receive_return,2); ?></td>
                <td width="100" align="right" id="value_total_issue_balance_td"><? echo number_format($total_issue_balance,2); ?></td>
                <td width="100" align="right" id="value_total_issue_value_td"><? echo number_format($total_issue_amount_value,2); ?></td> 
                <td width="100" align="right" id="value_total_closing_stock_td"><? echo number_format($total_closing_stock,2); ?></td>
                <td width="80">&nbsp;</td>
                <td width="100" align="right" title="total amount"><? echo number_format($total_closing_amount,2); ?></td>
                <td>&nbsp;</td>
            </tr>
        </table>
        </div>
        <!---->
    	<?
    }
	else if($report_type == 1)
	{
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
                            <td width="80" align="right"><? if($closingStock>0)  echo number_format($row[csf("avg_rate_per_unit")],2); else echo "0.00"; ?></td>
                            <td width="100" align="right"><p><? if($closingStock>0) echo number_format($amount,2); else echo "0.00"; ?></p></td>
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
	 } 
	else if ($report_type == 3) 
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
					<td colspan="14" align="center" style="border:none;font-size:14px;">
						<b><? echo $companyArr[$cbo_company_name];?></b>
					</td>
				</tr>
				<!-- <tr class="form_caption" style="border:none;">
						<td colspan="14" align="center" style="border:none;font-size:12px; font-weight:bold">
							<p>Monthly Closing Value status</p>
						</td>
					</tr> -->
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
            if ($item_description_id==0 || $item_description_id=="") 
            {
                $item_description=""; 
                $item_description_issue=""; 
            }
            else 
            {
                $item_description=" and a.prod_id in ($item_description_id)";
                $item_description_issue=" and d.prod_id in ($item_description_id)";
            }
            
            if ($cbo_item_group==0 || $cbo_item_group=="") 
            {
                $items_group=""; 
                $items_group_issue=""; 

            }
            else 
            {
                $items_group=" and b.item_group_id in ($cbo_item_group)";
                $items_group_issue=" and e.item_group_id in ($cbo_item_group)";
            }
            $store_cond_issue="";
            if($cbo_store_name>0)  $store_cond_issue=" and d.store_id=$cbo_store_name";
            if ($cbo_company_name>=0) $company_id_issue =" and d.company_id='$cbo_company_name'";                    
            $companyArr = return_library_array("select id,company_name from lib_company","id","company_name"); 
            $issue_sql= "select d.cons_amount as iss_amount,e.company_id, d.id as trans_id, c.entry_form, c.issue_purpose
            from inv_issue_master c, inv_transaction d,  product_details_master e
            where c.id = d.mst_id and d.prod_id = e.id and d.transaction_date between '".$from_date."' and '".$to_date."' $items_group_issue $item_description_issue $store_cond_issue and d.transaction_type = 2 and d.item_category = 4 and e.item_category_id = 4 and c.status_active = 1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0 $company_id_issue order by e.company_id";
            //and c.issue_purpose in (4,8,36)
            //echo $issue_sql."<br>";
            $iss_result=  sql_select($issue_sql);
            $iss_array= array();
            $trans_check = array();
            foreach($iss_result as $iss_row)
            {
                if(empty($trans_check[$iss_row[csf("trans_id")]]))
                {
                    $trans_check[$iss_row[csf("trans_id")]] = $iss_row[csf("trans_id")];

                    if($iss_row[csf("entry_form")] == 25)
                    {
                        if($iss_row[csf("issue_purpose")] == 4 || $iss_row[csf("issue_purpose")] == 8 || $iss_row[csf("issue_purpose")] == 36 || $iss_row[csf("issue_purpose")] == 42)
                        {
                            $iss_array[$iss_row[csf("company_id")]] += $iss_row[csf("iss_amount")];
                            $issue_test_data[$iss_row[csf("company_id")]][1]+= $iss_row[csf("iss_amount")];
                        }
                        $issue_test_data[$iss_row[csf("company_id")]][4]+= $iss_row[csf("iss_amount")];
                    }
                    else if($iss_row[csf("entry_form")] == 21)
                    {
                         if($iss_row[csf("issue_purpose")] == 6 || $iss_row[csf("issue_purpose")] == 8 || $iss_row[csf("issue_purpose")] == 21 || $iss_row[csf("issue_purpose")] == 22)
                        {
                            $iss_array[$iss_row[csf("company_id")]] += $iss_row[csf("iss_amount")];
                            $issue_test_data[$iss_row[csf("company_id")]][2]+= $iss_row[csf("iss_amount")];
                        }
                        $issue_test_data[$iss_row[csf("company_id")]][5]+= $iss_row[csf("iss_amount")];
                    }
                    $issue_test_data[$iss_row[csf("company_id")]][3]+= $iss_row[csf("iss_amount")];
                }
            }
            unset($iss_result);
            //echo "<pre>";print_r($issue_test_data);
            //echo $iss_array[1]."<br>";
                                                
            $sql="Select b.id, b.item_description, b.item_group_id, b.current_stock, b.avg_rate_per_unit, a.store_id,a.company_id,
            sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
            sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
            sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as rcv_total_opening_amt,
            sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as iss_total_opening_amt,
            sum(case when a.transaction_type in(1,4,5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as purchase_quantity,
            sum(case when a.transaction_type in(1,4,5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as purchase_amount, 
            sum(case when a.transaction_type in(2,3,6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue_quantity, 
            sum(case when a.transaction_type in(2,3,6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as issue_amount, 
            sum(case when a.transaction_type in(1) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as receive,
            sum(case when a.transaction_type in(4) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as issue_return,
            sum(case when a.transaction_type in(2) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as issue,
            sum(case when a.transaction_type in(3) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as receive_return,
            sum(case when a.transaction_type in(5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as receive_transfer,
            sum(case when a.transaction_type in(6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as issue_transfer,
            sum(case when a.transaction_type in(2,3,6) and a.transaction_date between '".$pre_from_date."' and '".$pre_to_date."' then a.cons_amount else 0 end) as pre_month_issue
            from inv_transaction a, product_details_master b
            where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id=4 and a.item_category=4 $company_id $item_description $items_group  $store_cond   and b.entry_form in (20,24) 
            group by b.id, b.item_description,b.item_group_id, b.current_stock, b.avg_rate_per_unit, a.store_id,a.company_id order by b.id ASC";
            

            //echo $sql;
            $result=  sql_select($sql);
            $i=0;$count=1;
            foreach($result as $row)
            { 
                if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; $count++;
                $ageOfDays = datediff("d",$date_array[$row[csf("id")]]['min_date'],date("Y-m-d"));
                $daysOnHand = datediff("d",$date_array[$row[csf("id")]]['max_date'],date("Y-m-d")); 
                
                $opening_bal=$row[csf("rcv_total_opening")]-$row[csf("iss_total_opening")];                    
                /*$openingRate=$openingBalanceValue=0;
                if($row[csf("rcv_total_opening")] > 0)
                {
                    $openingRate = $row[csf("rcv_total_opening_amt")] / $row[csf("rcv_total_opening")];
                }
                //$openingBalanceValue = $opening_bal*$openingRate;
                //$openingBalanceValue = $opening_bal*$row[csf("avg_rate_per_unit")];
                */
                $openingBalanceValue=0;
                $openingBalanceValue = $row[csf("rcv_total_opening_amt")]-$row[csf("iss_total_opening_amt")];
                /*
                $openingRate=$openingBalanceValue=0;
                $openingBalanceValue = $row[csf("rcv_total_opening_amt")]-$row[csf("iss_total_opening_amt")];
                if($opening_bal>0) 
                {
                $openingRate=$openingBalanceValue/$opening_bal;
                }
                */
                
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
                $closingRate=$amount=0;
                $amount= ($openingBalanceValue + $purchase_amount_value) - $issue_amount_value;
                if($closingStock>0)
                {
                    $closingRate = $amount/$closingStock;
                }
                
                $rept_data[$row[csf("company_id")]]['opening']+=$openingBalanceValue;
                $rept_data[$row[csf("company_id")]]['opening_qnty']+=$opening_bal;
                $rept_data[$row[csf("company_id")]]['company']=$row[csf("company_id")];
                $rept_data[$row[csf("company_id")]]['receive']+=$receive;
                $rept_data[$row[csf("company_id")]]['tot_receive_qnty']+=$row[csf("purchase_quantity")];
                $rept_data[$row[csf("company_id")]]['tot_issue_qnty']+=$row[csf("issue_quantity")];
                $rept_data[$row[csf("company_id")]]['iss_return']+=$issue_return;
                $rept_data[$row[csf("company_id")]]['trans_in']+=$receive_transfer;
                $rept_data[$row[csf("company_id")]]['total_rcv']+=$purchase_amount_value;
                $rept_data[$row[csf("company_id")]]['issue'] = $iss_array[$row[csf("company_id")]];//$issue;
                $rept_data[$row[csf("company_id")]]['issue_w_other']+= $row[csf("issue")];
                $rept_data[$row[csf("company_id")]]['rcv_return']+=$receive_return;
                $rept_data[$row[csf("company_id")]]['trans_out']+=$issue_transfer;
                $rept_data[$row[csf("company_id")]]['total_issue']+=$issue_amount_value;
                $rept_data[$row[csf("company_id")]]['closing_value']+= $amount;
                $rept_data[$row[csf("company_id")]]['closingStock']+= $closingStock;
                $rept_data[$row[csf("company_id")]]['pre_month_issue']+= $row[csf("pre_month_issue")];
                
                //|| (number_format($closingStock,2) > 0.00)
                //if(((($value_with ==1) && (number_format($stockInHand,2) > 0.00)) || ($value_with ==0)) && ((number_format($openingBalance,2) > 0.00) || (number_format($totalRcv,2) > 0.00) || (number_format($totalIssue,2) > 0.00)) )
                //if(($value_with ==1 && (number_format($openingBalance,2) > 0.00 || number_format($stockInHand,2) > 0.00 ) )    ||    ($value_with ==0 && (number_format($openingBalance,2) > 0.00 || number_format($stockInHand,2) > 0.00 || number_format($totalRcv,2) > 0.00 || number_format($totalIssue,2) > 0.00 )))
                //if(((($value_with ==1) && (number_format($closingStock,2) > 0.00))||($value_with ==0)) && ( (number_format($opening_bal,2) > 0.00) || (number_format($tot_receive,2) > 0.00) || (number_format($tot_issue,2) > 0.00) ) )
                /*if(($value_with ==1 && (number_format($opening_bal,2) > 0.00 || number_format($closingStock,2) > 0.00 ) )    ||    ($value_with ==0 && (number_format($opening_bal,2) > 0.00 || number_format($closingStock,2) > 0.00 || number_format($tot_receive,2) > 0.00 || number_format($tot_issue,2) > 0.00 ))) 
                {
                    if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $closingStock>$txt_qnty) || ($get_upto_qnty==2 && $closingStock<$txt_qnty) || ($get_upto_qnty==3 && $closingStock>=$txt_qnty) || ($get_upto_qnty==4 && $closingStock<=$txt_qnty) || ($get_upto_qnty==5 && $closingStock==$txt_qnty) || $get_upto_qnty==0))
                    {
                        //$amount=$closingStock*$row[csf("avg_rate_per_unit")];
                        
                        $rept_data[$row[csf("company_id")]]['opening']+=$openingBalanceValue;
                        
                        $rept_data[$row[csf("company_id")]]['opening_qnty']+=$opening_bal;
                        
                        $rept_data[$row[csf("company_id")]]['company']=$row[csf("company_id")];
                        $rept_data[$row[csf("company_id")]]['receive']+=$receive;
                        $rept_data[$row[csf("company_id")]]['iss_return']+=$issue_return;
                        $rept_data[$row[csf("company_id")]]['trans_in']+=$receive_transfer;
                        $rept_data[$row[csf("company_id")]]['total_rcv']+=$purchase_amount_value;
                        
                        $rept_data[$row[csf("company_id")]]['issue'] = $iss_array[$row[csf("company_id")]];//$issue;
                        $rept_data[$row[csf("company_id")]]['issue_w_other']+= $row[csf("issue")];
                        $rept_data[$row[csf("company_id")]]['rcv_return']+=$receive_return;
                        $rept_data[$row[csf("company_id")]]['trans_out']+=$issue_transfer;
                        $rept_data[$row[csf("company_id")]]['total_issue']+=$issue_amount_value;
                        
                        
                        $rept_data[$row[csf("company_id")]]['closing_value']+= $amount;
                        //$rept_data[$row[csf("company_id")]]['pre_month_issue']+= $row[csf("pre_month_issue")];
                    
                    }
                }*/

            }
            //echo $rept_data[1]['issue_w_other'];die;
            unset($result);
        
             $sl = 1;
            foreach ($rept_data as $value)
            {
             if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
             
                //if(($value_with ==1 && (number_format($value["opening_qnty"],2) > 0.00 || number_format($value["closingStock"],2) > 0.00 ) )    ||    ($value_with ==0 && (number_format($value["opening_qnty"],2) > 0.00 || number_format($value["closingStock"],2) > 0.00 || number_format($value["tot_receive_qnty"],2) > 0.00 || number_format($value["tot_issue_qnty"],2) > 0.00 )))
                //if(($value_with ==1 && ($value["opening_qnty"] != 0 || $value["closingStock"] != 0) ) || ($value_with ==0 && ($value["opening_qnty"] != 0 || $value["closingStock"] != 0 || $value["tot_receive_qnty"] != 0 || $value["tot_issue_qnty"] != 0)))
                if(($value_with ==1 && ($value["opening_qnty"] != 0 || $value["opening"] !=0  || $value["closingStock"] != 0 || $value['closing_value'] !=0)) || ($value_with ==0 || ($value["opening_qnty"] != 0  || $value["opening"] !=0 || $value["closingStock"] != 0 || $value['closing_value'] !=0 || $value["tot_receive_qnty"] != 0 || $value['total_rcv'] !=0 || $value["tot_issue_qnty"] != 0  || $value['total_issue'] !=0)))
                {
                    if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $value["closingStock"]>$txt_qnty) || ($get_upto_qnty==2 && $value["closingStock"]<$txt_qnty) || ($get_upto_qnty==3 && $value["closingStock"]>=$txt_qnty) || ($get_upto_qnty==4 && $value["closingStock"]<=$txt_qnty) || ($get_upto_qnty==5 && $value["closingStock"]==$txt_qnty) || $get_upto_qnty==0))
                    {
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
                        <td width="40"><? echo $sl;?></td>
                        <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: center; "><? echo $companyArr[$value['company']];?></td>
                        <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($value["opening"],2);?></p></td>
                        <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($value["receive"],2);?></p></td>
                        <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($value["trans_in"],2);?></p></td>
                        <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($value["iss_return"],2);?></p></td>
                        <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($value['total_rcv'],2);?></p></td>
                        <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($value['issue'],2);?></p></td>
                        <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($value["trans_out"],2);?></p></td>
                        <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($value["rcv_return"],2);?></p></td>
                        <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format(($value['issue_w_other']-$value['issue']),2);?></p></td>
                        <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($value["total_issue"],2);?> </p></td>
                        <td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($value['closing_value'],2);?></p></td>
                        <td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($value['pre_month_issue'],2);?></p></td>
                    </tr>
                    
                    <?
                    $grand_opening += $value["opening"];
                    $grand_opening_qnty += $value["opening_qnty"];
                    
                    $grand_rcv += $value["receive"];
                    $grand_trans_in += $value["trans_in"];
                    $grand_iss_return += $value["iss_return"];
                    $grand_rcv_total += $value['total_rcv'];
                    $grand_issue += $value['issue'];
                    $grand_trans_out += $value["trans_out"];
                    $grand_rcv_return += $value["rcv_return"];
                    $grand_other_issue += $value['issue_w_other']-$value['issue'];
                    $grand_issue_total += $value["total_issue"];
                    $grand_closing_value += $value['closing_value'];
                    $grand_pre_issue += $value['pre_month_issue'];
                    $sl++;
                    }
                }	
            }
                                                                         
              ?>
          </table>
          </div>
        <table width="1320" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" > 
            <tr>
                <td style="word-break: break-all; word-wrap:break-word;width: 40px;text-align: right;"><p>&nbsp;</p></td>
                <td style="word-break: break-all; word-wrap:break-word;width: 100px;text-align: right;" title="<? echo $count;?>"><p>Grand Total=</p></td>
                <td width="100" title="<? echo $grand_opening_qnty; ?>" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_opening,2);?></p></td>
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
        $date_array=array();
		$returnRes_date="select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category=4 group by prod_id";
		$result_returnRes_date = sql_select($returnRes_date);
		foreach($result_returnRes_date as $row)	
		{
			$date_array[$row[csf("prod_id")]]['min_date']=$row[csf("min_date")];
			$date_array[$row[csf("prod_id")]]['max_date']=$row[csf("max_date")];
		}
		unset($result_returnRes_date);
		?>
		<div>
		<table style="width:1320px" border="1" cellpadding="2" cellspacing="0"  id="caption" rules="all"> 
				<thead>
					<tr class="form_caption" style="border:none;">
						<td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td> 
					</tr>
					<tr class="form_caption" style="border:none;">
					<td colspan="14" align="center" style="border:none;font-size:12px;">
						<b><? echo $companyArr[$cbo_company_name];?></b>
					</td>
					</tr>
				<!-- 					<tr class="form_caption" style="border:none;">
					<td colspan="14" align="center" style="border:none;font-size:12px; font-weight:bold">
						<p>Monthly Closing Value status</p>
					</td>
				</tr> -->
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
		if ($item_description_id) 
		{
			$item_description=" and a.prod_id in ($item_description_id)";
			$item_description_issue=" and d.prod_id in ($item_description_id)";
		}
		else 
		{
			$item_description=""; 
			$item_description_issue=""; 
		}

		if ($cbo_item_group) 
		{
			$items_group=" and b.item_group_id in ($cbo_item_group)";
			$items_group_issue=" and e.item_group_id in ($cbo_item_group)";
		}
		else 
		{
			$items_group=""; 
			$items_group_issue=""; 
		}
		$store_cond_issue="";
		if($cbo_store_name>0)  $store_cond_issue=" and d.store_id=$cbo_store_name";
                
                
		$iss_sql = " select d.cons_amount as iss_amount,e.item_group_id, d.id as trans_id
		from inv_issue_master c, inv_transaction d, product_details_master e 
		where c.id = d.mst_id and d.prod_id = e.id and c.issue_purpose in (6,8,21,22) 
		and d.transaction_date between '".$from_date."' and '".$to_date."' 
		and d.transaction_type = 2 and c.entry_form = 21 and d.item_category = 4 and e.item_category_id = 4 and d.company_id = '$cbo_company_name'
		and c.status_active = 1 and c.is_deleted = 0 
		and d.status_active = 1 and d.is_deleted = 0 
		and e.status_active = 1 and e.is_deleted = 0 
                $items_group_issue $item_description_issue  $store_cond_issue
		order by e.item_group_id";
		$iss_result = sql_select($iss_sql);
		$iss_arr = array();$trans_check = array();
		foreach($iss_result as $i_row)
		{
			if($trans_check[$i_row[csf("trans_id")]] == "")
			{
				$trans_check[$i_row[csf("trans_id")]] = $i_row[csf("trans_id")];
				$iss_arr[$i_row[csf("item_group_id")]] += $i_row[csf("iss_amount")];
			}
		}
		unset($iss_result);
		//print_r($iss_arr);
                
		$sql="Select b.id, b.item_description,b.item_group_id, b.current_stock, b.avg_rate_per_unit, a.store_id,a.company_id,
		sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
		sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
		sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as rcv_total_opening_amt,
		sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as iss_total_opening_amt,
		sum(case when a.transaction_type in(1,4,5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as purchase_quantity,
		sum(case when a.transaction_type in(2,3,6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue_quantity,   
		sum(case when a.transaction_type in(1) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as receive,
		sum(case when a.transaction_type in(4) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as issue_return,
		sum(case when a.transaction_type in(2) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as issue,
		sum(case when a.transaction_type in(3) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as receive_return,
		sum(case when a.transaction_type in(5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as receive_transfer,
		sum(case when a.transaction_type in(6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as issue_transfer,
		sum(case when a.transaction_type in(2,3,6) and a.transaction_date between '".$pre_from_date."' and '".$pre_to_date."' then a.cons_amount else 0 end) as pre_month_issue
		from inv_transaction a, product_details_master b
		where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id=4 and a.item_category=4 and b.entry_form in(20) $company_id $store_cond $items_group $item_description 
		group by b.id, b.item_description,b.item_group_id, b.current_stock, b.avg_rate_per_unit, a.store_id,a.company_id order by b.item_group_id ASC";

		//echo $sql;
		$result= sql_select($sql);
		$data_array = array();
		foreach($result as $row)
		{
			$ageOfDays = datediff("d",$date_array[$row[csf("id")]]['min_date'],date("Y-m-d"));
			$daysOnHand = datediff("d",$date_array[$row[csf("id")]]['max_date'],date("Y-m-d")); 
			$opening_bal=$row[csf("rcv_total_opening")]-$row[csf("iss_total_opening")];
			$openingRate=$openingBalanceValue=0;

			/*
			if($row[csf("rcv_total_opening")] > 0)
			{
				$openingRate = $row[csf("rcv_total_opening_amt")] /$row[csf("rcv_total_opening")];
			}
			
			$openingBalanceValue = $opening_bal * $row[csf("avg_rate_per_unit")];
			*/
			$openingBalanceValue = $row[csf("rcv_total_opening_amt")]-$row[csf("iss_total_opening_amt")];

			$purchase_quantity=$row[csf("purchase_quantity")];
			$issue_quantity=$row[csf("issue_quantity")];

			$receive = $row[csf("receive")];
			$issue = $row[csf("issue")];
			$issue_return=$row[csf("issue_return")];
			$receive_return=$row[csf("receive_return")];
			$issue_transfer=$row[csf("issue_transfer")];
			$receive_transfer=$row[csf("receive_transfer")];
			//$pre_month_issue=  $row[csf("pre_month_issue")];
			//$tot_receive=$receive+$issue_return+$receive_transfer;
			//$tot_issue=$issue+$receive_return+$issue_transfer;

			$closingStock=$opening_bal+$purchase_quantity-$issue_quantity;
			
			//$tmp=$row[csf("rcv_total_opening_amt")]-$row[csf("iss_total_opening_amt")];
			$total_rcv = $row[csf("receive")] + $row[csf("issue_return")] + $row[csf("receive_transfer")];
			$total_issue = $row[csf("issue")] + $row[csf("receive_return")] + $row[csf("issue_transfer")];
			
			$closing_value= $openingBalanceValue + $total_rcv - $total_issue;
			$rept_data[$row[csf("item_group_id")]]['opening']+=$openingBalanceValue;
			$rept_data[$row[csf("item_group_id")]]['opening_qnty']+=$opening_bal;
			$rept_data[$row[csf("item_group_id")]]['group']=$row[csf("item_group_id")];
			$rept_data[$row[csf("item_group_id")]]['receive']+=$row[csf("receive")];
			$rept_data[$row[csf("item_group_id")]]['purchase_quantity']+=$purchase_quantity;
			$rept_data[$row[csf("item_group_id")]]['issue_quantity']+=$issue_quantity;
			$rept_data[$row[csf("item_group_id")]]['iss_return']+=$row[csf("issue_return")];
			$rept_data[$row[csf("item_group_id")]]['trans_in']+=$row[csf("receive_transfer")];
			$rept_data[$row[csf("item_group_id")]]['total_rcv']+=$total_rcv;
			
			$rept_data[$row[csf("item_group_id")]]['issue']= $iss_arr[$row[csf("item_group_id")]];
			$rept_data[$row[csf("item_group_id")]]['issue_w_other']+= $row[csf("issue")];
			//$rept_data[$row[csf("company_id")]]['other_issue']+= $row[csf("issue")] - $iss_array[$row[csf("company_id")]];
			$rept_data[$row[csf("item_group_id")]]['rcv_return']+=$row[csf("receive_return")];
			$rept_data[$row[csf("item_group_id")]]['trans_out']+=$row[csf("issue_transfer")];
			$rept_data[$row[csf("item_group_id")]]['total_issue']+=$total_issue;
			$rept_data[$row[csf("item_group_id")]]['closing_value']+= $closing_value;
			$rept_data[$row[csf("item_group_id")]]['closingStock']+= $closingStock;
			$rept_data[$row[csf("item_group_id")]]['pre_month_issue']+= $row[csf("pre_month_issue")];

			//if(((($value_with ==1) && (number_format($closingStock,2) > 0.00))||($value_with ==0)) && ( (number_format($opening_bal,2) > 0.00) || (number_format($purchase_quantity,2) > 0.00) || (number_format($issue_quantity,2) > 0.00) ) )
			/*if(($value_with ==1 && (number_format($opening_bal,2) > 0.00 || number_format($closingStock,2) > 0.00 ) )    ||    ($value_with ==0 && (number_format($opening_bal,2) > 0.00 || number_format($closingStock,2) > 0.00 || number_format($purchase_quantity,2) > 0.00 || number_format($issue_quantity,2) > 0.00 ))) 
			{
				if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $closingStock>$txt_qnty) || ($get_upto_qnty==2 && $closingStock<$txt_qnty) || ($get_upto_qnty==3 && $closingStock>=$txt_qnty) || ($get_upto_qnty==4 && $closingStock<=$txt_qnty) || ($get_upto_qnty==5 && $closingStock==$txt_qnty) || $get_upto_qnty==0))
				{
					$tmp=$row[csf("rcv_total_opening_amt")]-$row[csf("iss_total_opening_amt")];
					$total_rcv = $row[csf("receive")] + $row[csf("issue_return")] + $row[csf("receive_transfer")];
					$total_issue = $row[csf("issue")] + $row[csf("receive_return")] + $row[csf("issue_transfer")];
					$closing_value= $tmp + $total_rcv - $total_issue;
					$rept_data[$row[csf("item_group_id")]]['opening']+=$openingBalanceValue;
					$rept_data[$row[csf("item_group_id")]]['opening_qnty']+=$opening_bal;
					$rept_data[$row[csf("item_group_id")]]['group']=$row[csf("item_group_id")];
					$rept_data[$row[csf("item_group_id")]]['receive']+=$row[csf("receive")];;
					$rept_data[$row[csf("item_group_id")]]['iss_return']+=$row[csf("issue_return")];
					$rept_data[$row[csf("item_group_id")]]['trans_in']+=$row[csf("receive_transfer")];
					$rept_data[$row[csf("item_group_id")]]['total_rcv']+=$total_rcv;
					
					$rept_data[$row[csf("item_group_id")]]['issue']= $iss_arr[$row[csf("item_group_id")]];
					$rept_data[$row[csf("item_group_id")]]['issue_w_other']+= $row[csf("issue")];
					//$rept_data[$row[csf("company_id")]]['other_issue']+= $row[csf("issue")] - $iss_array[$row[csf("company_id")]];
					$rept_data[$row[csf("item_group_id")]]['rcv_return']+=$row[csf("receive_return")];
					$rept_data[$row[csf("item_group_id")]]['trans_out']+=$row[csf("issue_transfer")];
					$rept_data[$row[csf("item_group_id")]]['total_issue']+=$total_issue;
					
					
					$rept_data[$row[csf("item_group_id")]]['closing_value']+= $closing_value;
					$rept_data[$row[csf("item_group_id")]]['pre_month_issue']+= $row[csf("pre_month_issue")];
				   
							  
				}
			}*/
			 
		}
		
		unset($result);
                
		/*echo "<pre>";
		print_r($rept_data);
		echo "</pre>";*/
	   $sl=1;
		  foreach($rept_data as $item_group => $value)
		  {
			  
			  
			  //if(($value_with ==1 && (number_format($value["opening_qnty"],2) > 0.00 || number_format($value["closingStock"],2) > 0.00 ) )    ||    ($value_with ==0 && (number_format($value["opening_qnty"],2) > 0.00 || number_format($value["closingStock"],2) > 0.00 || number_format($value["purchase_quantity"],2) > 0.00 || number_format($value["issue_quantity"],2) > 0.00 )))
			  //if(($value_with ==1 && ($value["opening_qnty"] != 0 || $value["closingStock"] != 0) ) || ($value_with ==0 && ($value["opening_qnty"] != 0 || $value["closingStock"] != 0 || $value["purchase_quantity"] != 0 || $value["issue_quantity"] != 0)))
			  if(($value_with ==1 && ($value["opening_qnty"] != 0 || $value["opening"] !=0  || $value["closingStock"] != 0 || $value['closing_value'] !=0)) || ($value_with ==0 || ($value["opening_qnty"] != 0 || $value["opening"] !=0  || $value["closingStock"] != 0 || $value['closing_value'] !=0 || $value["purchase_quantity"] != 0 || $value["issue_quantity"] != 0))) 
				{
					if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $value["closingStock"]>$txt_qnty) || ($get_upto_qnty==2 && $value["closingStock"]<$txt_qnty) || ($get_upto_qnty==3 && $value["closingStock"]>=$txt_qnty) || ($get_upto_qnty==4 && $value["closingStock"]<=$txt_qnty) || ($get_upto_qnty==5 && $value["closingStock"]==$txt_qnty) || $get_upto_qnty==0))
					{
						if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
			  ?>
					<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
					<td width="40"><? echo $sl;?></td>
					<td width="100" style="word-break: break-all; word-wrap:break-word;text-align: center; "><? echo $trim_group[$value["group"]];?></td>
					<td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($value['opening'],2);?></p></td>
					<td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($value["receive"],2);?></p></td>
					<td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo $value["trans_in"];?></p></td>
					<td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo $value["iss_return"];?></p></td>
					<td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($value['total_rcv'],2);?></p></td>
					<td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($value['issue'],2);?></p></td>
					<td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo $value["trans_out"];?></p></td>
					<td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo $value["rcv_return"];?></p></td>
					<td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format(($value['issue_w_other']-$value['issue']),2);?></p></td>
					<td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($value['total_issue'],2);?></p></td>
					<td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($value['closing_value'],2);?></p></td>
					<td width="80" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($value["pre_month_issue"],2);?></p></td>
					</tr>
					  <?
					   $sl++;
					$grand_opening += $value['opening'];
					$grand_opening_qnty += $value['opening_qnty'];
					
					$grand_rcv += $value["receive"];
					$grand_trans_in += $value["trans_in"];
					$grand_iss_return += $value["iss_return"];
					$grand_rcv_total += $value['total_rcv'];
					$grand_issue += $value['issue'];
					$grand_trans_out += $value["trans_out"];
					$grand_rcv_return += $value["rcv_return"];
					$grand_other_issue += $value['issue_w_other']-$value['issue'];
					$grand_issue_total += $value['total_issue'];
					$grand_closing_value += $value['closing_value'];
					$grand_pre_issue += $value["pre_month_issue"];
					}
				}
		  }
                               
		?>
		</table>
		</div>
		<table width="1320" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" > 
			<tr>
				<td style="word-break: break-all; word-wrap:break-word;width: 40px;text-align: right;"><p>&nbsp;</p></td>
				<td style="word-break: break-all; word-wrap:break-word;width: 100px;text-align: right;"><p>Grand Total=</p></td>
				<td width="100" title="<? echo $grand_opening_qnty; ?>" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_opening,2);?></p></td>
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
		$date_array=array();
		$returnRes_date="select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category=4 group by prod_id";
		$result_returnRes_date = sql_select($returnRes_date);
		foreach($result_returnRes_date as $row)	
		{
			$date_array[$row[csf("prod_id")]]['min_date']=$row[csf("min_date")];
			$date_array[$row[csf("prod_id")]]['max_date']=$row[csf("max_date")];
		}
		
		unset($result_returnRes_date);
            
		if ($item_description_id==0 || $item_description_id=="") 
		{
			$item_description=""; 
		}
		else 
		{
			$item_description=" and a.prod_id in ($item_description_id)";
		}

		if ($cbo_item_group==0 || $cbo_item_group=="") 
		{
			$items_group=""; 
			$items_group_issue=""; 
		}
		else 
		{
			$items_group=" and b.item_group_id in ($cbo_item_group)";
			$items_group_issue=" and c.item_group_id in ($cbo_item_group)";
		}
		
   		if($cbo_store_name > 0)
		{
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
			foreach($trans_result as $tr_row)
			{
				if($trans_check[$tr_row[csf("trans_id")]] == "")
				{
					$trans_check[$tr_row[csf("trans_id")]] = $tr_row[csf("trans_id")];
					$trans_store_arr[$tr_row[csf("buyer_name")]]["tr_in"] +=  $tr_row[csf("trans_in")];
					$trans_store_arr[$tr_row[csf("buyer_name")]]["tr_out"] +=  $tr_row[csf("trans_out")];
				}
			}
		}
		unset($trans_result);
		
		$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		
		/*$iss_sql= " select a.cons_amount as iss_amount, a.company_id, e.buyer_name, a.id as trans_id 
                    from inv_transaction a, inv_issue_master b,  product_details_master c, order_wise_pro_details d, wo_po_details_master e, wo_po_break_down f 
                    where b.id = a.mst_id and a.prod_id = c.id and b.issue_purpose in (4,8,36) 
                    and a.transaction_date between '".$from_date."' and '".$to_date."' 
                    and a.transaction_type = 2 and b.entry_form = 25 and a.item_category = 4 
                    and c.item_category_id = 4 $company_id $item_description $items_group_issue
                    and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 
                    and c.status_active = 1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0
                    and e.status_active = 1 and e.is_deleted = 0 and f.status_active = 1 and f.is_deleted = 0 
                    and a.id = d.trans_id and d.po_breakdown_id = f.id and e.job_no = f.job_no_mst 
                    order by a.company_id ";*/
					
		
		//print_r($iss_buyer_arr);//die;
		$sql_buyer_trans = "select b.buyer_name, a.trans_id, t.prod_id, t.cons_quantity, t.cons_amount, t.transaction_type
		from inv_transaction t, order_wise_pro_details a, wo_po_details_master b, wo_po_break_down c
		where t.id=a.trans_id and a.po_breakdown_id = c.id and c.job_no_mst = b.job_no 
		and a.trans_id > 0 and a.status_active = 1 and a.is_deleted = 0 and a.entry_form in(24,25,49,73,78,112)
		order by a.trans_id";
		//echo $sql_buyer_trans;die;
		$buyer_from_trans = sql_select($sql_buyer_trans);
		foreach($buyer_from_trans as $row)
		{
			$buyerTrans[$row[csf('trans_id')]] = $row[csf('buyer_name')];
			if($trans_check[$row[csf('trans_id')]]=="")
			{
				$trans_check[$row[csf('trans_id')]]=$row[csf('trans_id')];
				if($row[csf('transaction_type')]==1 || $row[csf('transaction_type')]==4 || $row[csf('transaction_type')]==5)
				{
					$buyerTransData[$row[csf('buyer_name')]][$row[csf('prod_id')]]["buyer_prod_qnty"] += $row[csf('cons_quantity')];
					$buyerTransData[$row[csf('buyer_name')]][$row[csf('prod_id')]]["buyer_prod_amt"] += $row[csf('cons_amount')];
				}
				else
				{
					$runtime_rate=$buyerTransData[$row[csf('buyer_name')]][$row[csf('prod_id')]]["buyer_prod_amt"]/$buyerTransData[$row[csf('buyer_name')]][$row[csf('prod_id')]]["buyer_prod_qnty"];
					$cons_amount=$row[csf('cons_quantity')]*$runtime_rate;
					$buyerTransData[$row[csf('buyer_name')]][$row[csf('prod_id')]]["buyer_prod_qnty"] -= $row[csf('cons_quantity')];
					$buyerTransData[$row[csf('buyer_name')]][$row[csf('prod_id')]]["buyer_prod_amt"] -= $cons_amount;
				}
			}
		}
		//echo "<pre>";print_r($buyerTrans);die;
		unset($buyer_from_trans);
		
		$nonOrder_buyer_sql="select c.id, a.buyer_id, c.transaction_type, c.prod_id, c.cons_quantity, c.cons_amount, 1 as type 
		from wo_non_ord_samp_booking_mst a, inv_receive_master b, inv_transaction c 
		where a.id=b.booking_id and b.id=c.mst_id and a.booking_type=5 and b.booking_without_order=1 and b.entry_form=24 and c.transaction_type=1 and c.item_category=4
		union all
		select c.id, a.buyer_id, c.transaction_type, c.prod_id, c.cons_quantity, c.cons_amount, 2 as type 
		from wo_non_ord_samp_booking_mst a, inv_issue_master b, inv_transaction c 
		where a.id=b.booking_id and b.id=c.mst_id and a.booking_type=5 and b.issue_basis=2 and b.entry_form=25 and c.transaction_type=2 and c.item_category=4";
		$nonOrder_buyer_result= sql_select($nonOrder_buyer_sql);
		foreach($nonOrder_buyer_result as $row)
		{
			$buyerTransNonorder[$row[csf('id')]] = $row[csf('buyer_id')];
			
			if($row[csf('transaction_type')]==1 || $row[csf('transaction_type')]==4 || $row[csf('transaction_type')]==5)
			{
				$buyerTransData[$row[csf('buyer_id')]][$row[csf('prod_id')]]["buyer_prod_qnty"] += $row[csf('cons_quantity')];
				$buyerTransData[$row[csf('buyer_id')]][$row[csf('prod_id')]]["buyer_prod_amt"] += $row[csf('cons_amount')];
			}
			else
			{
				$runtime_rate=$buyerTransData[$row[csf('buyer_id')]][$row[csf('prod_id')]]["buyer_prod_amt"]/$buyerTransData[$row[csf('buyer_id')]][$row[csf('prod_id')]]["buyer_prod_qnty"];
				$cons_amount=$row[csf('cons_quantity')]*$runtime_rate;
				$buyerTransData[$row[csf('buyer_id')]][$row[csf('prod_id')]]["buyer_prod_qnty"] -= $row[csf('cons_quantity')];
				$buyerTransData[$row[csf('buyer_id')]][$row[csf('prod_id')]]["buyer_prod_amt"] -= $cons_amount;
			}
		}
		unset($nonOrder_buyer_result);
		
		
		$iss_sql= " select a.cons_amount as iss_amount, a.cons_quantity, a.id as trans_id, c.id 
                    from inv_transaction a, inv_issue_master b, product_details_master c
                    where b.id = a.mst_id and a.prod_id = c.id and b.issue_purpose in (4,8,36,42) and a.transaction_date between '".$from_date."' and '".$to_date."' and a.transaction_type = 2 and b.entry_form = 25 and c.entry_form = 24 and a.item_category = 4 and c.item_category_id = 4 $company_id $item_description $items_group_issue and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0";
   
		//echo $iss_sql."<br>";die;
		$iss_result= sql_select($iss_sql);
		$iss_transcheck = array();$iss_buyer_arr = array();
		foreach($iss_result as $row)
		{
			$buyers_id="";
			if($buyerTrans[$row[csf('trans_id')]]>0)
			{
				$buyers_id=$buyerTrans[$row[csf('trans_id')]];
			}
			else
			{
				$buyers_id=$buyerTransNonorder[$row[csf('trans_id')]];
			}
			$buyer_pord_issue_rate=$buyerTransData[$buyers_id][$row[csf('id')]]["buyer_prod_amt"]/$buyerTransData[$buyers_id][$row[csf('id')]]["buyer_prod_qnty"];
		   	$iss_trans_arr[$row[csf("trans_id")]] += $row[csf("cons_quantity")]*$buyer_pord_issue_rate;
		}
		unset($iss_result);
		
		/*$order_wise_sql=sql_select("select b.po_breakdown_id, a.item_color as item_color_id, a.item_size, a.item_group_id, a.item_description, b.trans_id, b.quantity, b.entry_form, b.trans_type
		from product_details_master a, order_wise_pro_details b
		where a.id=b.prod_id and a.item_category_id=4 and a.entry_form=24 and b.entry_form in(24,25,49,73,78,112) and b.trans_type in(1,2,3,4,5,6) and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$trans_wise_ord_qnty=array();
		foreach($order_wise_sql as $row)
		{
			$trans_wise_ord_qnty[$row[csf("trans_id")]]+=$row[csf("quantity")];
			$ord_wise_trans_id[$row[csf("trans_id")]]=$row[csf("trans_id")];
		}*/
		 
		/*$sql="Select b.id, b.item_description,b.item_group_id, b.current_stock, b.avg_rate_per_unit, a.store_id,a.company_id,a.id as trans_id,
		sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
		sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as opening_issue_qtny,
		sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as rcv_total_opening_amt,
		sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as iss_total_opening_amt,
		sum(case when a.transaction_type in(1,4,5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as purchase_quantity,
		sum(case when a.transaction_type in(2,3,6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue_quantity,   
		sum(case when a.transaction_type in(1) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as receive,
		sum(case when a.transaction_type in(4) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as issue_return,
		sum(case when a.transaction_type in(2) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as issue,
		sum(case when a.transaction_type in(3) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as receive_return,
		sum(case when a.transaction_type in(5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as receive_transfer,
		sum(case when a.transaction_type in(6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as issue_transfer,
		sum(case when a.transaction_type in(2,3,6) and a.transaction_date between '".$pre_from_date."' and '".$pre_to_date."' then a.cons_amount else 0 end) as pre_month_issue
		from inv_transaction a, product_details_master b
		where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id=4 and a.item_category=4 and b.entry_form = 24 $company_id $store_cond $items_group $item_description 
		group by b.id, b.item_description,b.item_group_id, b.current_stock, b.avg_rate_per_unit, a.store_id,a.company_id,a.id order by b.id ASC";*/
		
		//and a.entry_form in(24,25,49,73,78,112)
		$sql="Select b.id, b.item_description, b.item_group_id, b.current_stock, b.avg_rate_per_unit, a.store_id, a.company_id, a.id as trans_id, a.transaction_type, a.cons_quantity, 
		(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
		(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as opening_issue_qtny,
		(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as rcv_total_opening_amt,
		(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as iss_total_opening_amt,
		(case when a.transaction_type in(1,4,5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as purchase_quantity,
		(case when a.transaction_type in(2,3,6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue_quantity,   
		(case when a.transaction_type in(1) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as receive_qnty,
		(case when a.transaction_type in(1) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as receive_amt,
		(case when a.transaction_type in(4) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as issue_return_amt,
		(case when a.transaction_type in(2) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue_qnty,
		(case when a.transaction_type in(3) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as receive_return_qnty,
		(case when a.transaction_type in(5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as receive_transfer_qnty,
		(case when a.transaction_type in(5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as receive_transfer_amt,
		(case when a.transaction_type in(6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue_transfer_qnty,
		(case when a.transaction_type in(2,3,6) and a.transaction_date between '".$pre_from_date."' and '".$pre_to_date."' then a.cons_amount else 0 end) as pre_month_issue
		from inv_transaction a, product_details_master b
		where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id=4 and a.item_category=4 and b.entry_form = 24 $company_id $store_cond $items_group $item_description
		order by a.id ASC";
		
		//echo $sql;die;
		//echo $sql."<br>";
		$result = sql_select($sql);
		$report_arr = array();$count=0;$mis_mas_data=array();
		foreach($result as $row)
		{
			$ageOfDays = datediff("d",$date_array[$row[csf("id")]]['min_date'],date("Y-m-d"));
			$daysOnHand = datediff("d",$date_array[$row[csf("id")]]['max_date'],date("Y-m-d")); 
			$opening_bal=$row[csf("rcv_total_opening")]-$row[csf("opening_issue_qtny")];
			
			$openingRate=$openingBalanceValue=0;
			//if(round($trans_wise_ord_qnty[$row[csf("trans_id")]],0) != round($row[csf("cons_quantity")],0))
			/*if(round($trans_wise_ord_qnty[$row[csf("trans_id")]],2) != round($row[csf("cons_quantity")],2))
			{
				$mis_mas_data[$row[csf("trans_id")]]=$trans_wise_ord_qnty[$row[csf("trans_id")]]."_".$row[csf("cons_quantity")];
			}*/
			
			
			
			//if($row[csf("rcv_total_opening")] > 0)
			//{
			  //$openingRate = $row[csf("rcv_total_opening_amt")] /$row[csf("rcv_total_opening")];  
			//}
			//$openingBalanceValue = $opening_bal * $row[csf("avg_rate_per_unit")];
			$buyers_id="";
			if($buyerTrans[$row[csf('trans_id')]]>0)
			{
				$buyers_id=$buyerTrans[$row[csf('trans_id')]];
			}
			else
			{
				$buyers_id=$buyerTransNonorder[$row[csf('trans_id')]];
			}
			
			$buyer_pord_issue_rate=$buyerTransData[$buyers_id][$row[csf('id')]]["buyer_prod_amt"]/$buyerTransData[$buyers_id][$row[csf('id')]]["buyer_prod_qnty"];
			$openingBalanceValue = $opening_bal * $buyer_pord_issue_rate;
			//$openingBalanceValue = $row[csf("rcv_total_opening_amt")]-$row[csf("iss_total_opening_amt")];
	
			$purchase_quantity=$row[csf("purchase_quantity")];
			$issue_quantity=$row[csf("issue_quantity")];
	
			$receive = $row[csf("receive_amt")];
			$issue = $row[csf("issue_qnty")]*$buyer_pord_issue_rate;
			$issue_return=$row[csf("issue_return_amt")];
			$receive_return=$row[csf("receive_return_qnty")]*$buyer_pord_issue_rate;
			$issue_transfer=$row[csf("issue_transfer_qnty")]*$buyer_pord_issue_rate;
			$receive_transfer=$row[csf("receive_transfer_amt")];
			$pre_month_issue=  $row[csf("pre_month_issue")];
			$tot_receive=$receive+$issue_return+$receive_transfer;
			$tot_issue=$issue+$receive_return+$issue_transfer;
			
			$all_rcv_amt=$row[csf('receive_amt')] + $row[csf('issue_return_amt')] + $row[csf('receive_transfer_amt')];
			$all_issue_amt=$row[csf('issue_qnty')]*$buyer_pord_issue_rate + $row[csf('issue_transfer_qnty')]*$buyer_pord_issue_rate + $row[csf('receive_return_qnty')]*$buyer_pord_issue_rate;
	
			$closingStock=$opening_bal+$purchase_quantity-$issue_quantity;
			$closingAmout=$openingBalanceValue+$all_rcv_amt-$all_issue_amt;
			
			
			
			
			//$buyers_id=$buyerTrans[$row[csf('trans_id')]];
			if($buyers_id)
			{
				$report_arr[$buyers_id]['rcv_total_opening_amt'] += $row[csf('rcv_total_opening_amt')];
				$report_arr[$buyers_id]['iss_total_opening_amt'] += $row[csf('iss_total_opening_amt')];
				$report_arr[$buyers_id]['opening'] += $openingBalanceValue;
				$report_arr[$buyers_id]['opening_qnty'] += $opening_bal;
				$report_arr[$buyers_id]['purchase_quantity'] += $row[csf('purchase_quantity')];
				$report_arr[$buyers_id]['issue_quantity'] += $row[csf('issue_quantity')];
				$report_arr[$buyers_id]['receive'] += $row[csf('receive_amt')];
				$report_arr[$buyers_id]['iss_return'] += $row[csf('issue_return_amt')];
				$report_arr[$buyers_id]['trans_in'] += $row[csf('receive_transfer_amt')];
				$report_arr[$buyers_id]['total_rcv'] += $row[csf('receive_amt')] + $row[csf('issue_return_amt')] + $row[csf('receive_transfer_amt')];
	
				$report_arr[$buyers_id]['issue'] += $iss_trans_arr[$row[csf("trans_id")]];
				$report_arr[$buyers_id]['issue_other'] += $row[csf('issue_qnty')]*$buyer_pord_issue_rate - $iss_trans_arr[$row[csf("trans_id")]];
				$report_arr[$buyers_id]['trans_out'] += $row[csf('issue_transfer_qnty')]*$buyer_pord_issue_rate;
				$report_arr[$buyers_id]['issue_w_other'] += $row[csf('issue_qnty')]*$buyer_pord_issue_rate;
				$report_arr[$buyers_id]['rcv_return'] += $row[csf('receive_return_qnty')]*$buyer_pord_issue_rate;
				$report_arr[$buyers_id]['total_issue'] += $row[csf('issue_qnty')]*$buyer_pord_issue_rate + $row[csf('issue_transfer_qnty')]*$buyer_pord_issue_rate + $row[csf('receive_return_qnty')]*$buyer_pord_issue_rate;
				$report_arr[$buyers_id]['closingStock'] += $closingStock;
				$report_arr[$buyers_id]['closingAmount'] += $closingAmout;
				$report_arr[$buyers_id]['pre_month_issue'] += $row[csf('pre_month_issue')];
			}
			//else{
				//echo $row[csf('trans_id')]."=".$buyerTrans[$row[csf('trans_id')]]."=".$buyers_id;die; 
				//$trans_non_buyer.=$row[csf('trans_id')].",";
			//}
		}
		unset($result);
		//echo $tot_rcv_qnty."<pre>";print_r($report_arr);die;
		//echo $trans_non_buyer;die;
		//$test_transId=chop( $test_transId,",");
		//echo $test_transId;die;
 		//echo '<pre>';
		//print_r($report_arr);
		//	echo "</pre>";
		
		//echo "<pre>"; print_r($report_arr[77]);die;
		
		if($cbo_store_name != 0){
			$rcv_colspan = 5;
			$iss_colspan = 6;
			$width = 1630;
			$tbl_colspan = 16;
		}else{
			$rcv_colspan = 4;
			$iss_colspan = 5;
			$width = 1430;
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
						<td colspan="<?echo $tbl_colspan;?>" align="center" style="border:none;font-size:12px;">
							<b><? echo $companyArr[$cbo_company_name];?></b>
						</td>
					</tr>
					<!-- <tr class="form_caption" style="border:none;">
						<td colspan="<?echo $tbl_colspan;?>" align="center" style="border:none;font-size:12px; font-weight:bold">
							<p>Monthly Closing Value status</p>
						</td>
					</tr> -->
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
						<th rowspan="2" width="110">Opening Value TK</th>
						<th colspan="<? echo $rcv_colspan;?>">Receive</th>
						<th colspan="<? echo $iss_colspan;?>">Issue</th>
						<th rowspan="2" width="110">Closing Value TK</th>
						<th rowspan="2" width="">Previous Month Consumption value TK</th>
					</tr> 
					<tr>
						<th width="90">Purchase Value TK</th>
						<th width="110"><p>Ord to Ord &nbsp; Trns In &nbsp; Value TK</p></th>
						<? if($cbo_store_name > 0){ ?>
							<th width="100"><p>Str To Str &nbsp; Trns In Value TK</p></th>
						<? }?>
						<th width="90"><p>Issue &nbsp; Return &nbsp; Value TK</p></th>
						<th width="110"><p>Total Rcv Value TK</p></th>
						<th width="90"><p>Issue Value TK</p></th>
						<th width="110"><p>Ord to Ord Trns Out Value TK</p></th>
						<? if($cbo_store_name > 0){ ?>
						<th width="100"><p>Str To Str Trns Out Value TK</p></th>
						<? }?>
						<th width="90">Rcv Return Value TK</th>
						<th width="110"><p>Other Issue Value TK</p></th>
						<th width="110"><p>Total Issue Value TK</p></th>
					</tr> 
				</thead>
			</table>
			<div style="width:<? echo $width + 20?>px; max-height:350px;overflow-y:scroll" id="scroll_body" > 
				<table width="<? echo $width;?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
				<? 
					$sl = 1; 
					foreach($report_arr as $buyer_id => $trans_type)
					{
						//if(($value_with ==1 && (number_format($trans_type["opening_qnty"],2) > 0.00 || number_format($trans_type["closingStock"],2) > 0.00 ) )    ||    ($value_with ==0 && (number_format($trans_type["opening_qnty"],2) > 0.00 || number_format($trans_type["closingStock"],2) > 0.00 || number_format($trans_type["purchase_quantity"],2) > 0.00 || number_format($trans_type["issue_quantity"],2) > 0.00 )))
						//if(($value_with ==1 && ($trans_type["opening_qnty"] != 0 || $trans_type["closingStock"] != 0) ) || ($value_with ==0 && ($trans_type["opening_qnty"] != 0 || $trans_type["closingStock"] != 0 || $trans_type["purchase_quantity"] != 0 || $trans_type["issue_quantity"] != 0)))
						if(($value_with ==1 && ($trans_type["opening_qnty"] != 0 || $trans_type["opening"] !=0  || $trans_type["closingStock"] != 0 || $trans_type['closingAmount'] !=0)) || ($value_with ==0 || ($trans_type["opening_qnty"] != 0 || $trans_type["opening"] !=0  || $trans_type["closingStock"] != 0 || $trans_type['closingAmount'] !=0 || $trans_type["purchase_quantity"] != 0 || $trans_type["issue_quantity"] != 0))) 
						{
						if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $trans_type["closingStock"]>$txt_qnty) || ($get_upto_qnty==2 && $trans_type["closingStock"]<$txt_qnty) || ($get_upto_qnty==3 && $trans_type["closingStock"]>=$txt_qnty) || ($get_upto_qnty==4 && $trans_type["closingStock"]<=$txt_qnty) || ($get_upto_qnty==5 && $trans_type["closingStock"]==$txt_qnty) || $get_upto_qnty==0))
							{
							$opening_value  = $trans_type["opening"];
							if($cbo_store_name > 0) 
							{
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
							$rcv_total =  $trans_type["receive"] + $trans_type["iss_return"] + $trans_in + $store_trans_in;
							//$other_issue= $trans_type["tot_issue"] - $iss_buyer_arr[$buyer_id];
							
							$issue_total = $trans_type["issue_w_other"] + $trans_type["rcv_return"] + $trans_out + $store_trans_out;
							$closing_value = $opening_value + $rcv_total - $issue_total;
							if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
								<td width="40"><? echo $sl;?></td>
								<td width="100" style="word-break: break-all; word-wrap:break-word;text-align: center; " title="<? echo $buyer_id; ?>"><? echo $buyer_arr[$buyer_id];?></td>
								<td width="110" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($opening_value,2);?></p></td>
								<td width="90" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($trans_type["receive"],2);?></p></td>
								<td width="110" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($trans_in,2);?></p></td>
								<? if($cbo_store_name > 0) {?>
								<td width="100" title="store_tra_in" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p>&nbsp;<? echo number_format($store_trans_in,2);?></p></td>
								<? }?>
								<td width="90" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($trans_type["iss_return"],2);?></p></td>
								<td width="110" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($rcv_total,2);?></p></td>
								<td width="90" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($trans_type['issue'],2);?></p></td>
								<td width="110" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($trans_out,2);?></p></td>
								<? if($cbo_store_name > 0) {?>
								<td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p>&nbsp;<? echo number_format($store_trans_out,2);?></p></td>
								<? }?>
								<td width="90" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($trans_type["rcv_return"],2);?></p></td>
								<td width="110" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($trans_type['issue_other'],2);?></p></td>
								<td width="110" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($issue_total,2);?></p></td>
								<td width="110" style="word-break: break-all; word-wrap:break-word;text-align: right; " title="<? echo $trans_type["closingStock"]; ?>"><p><? echo number_format($closing_value,2);?></p></td>
								<td width="" style="word-break: break-all; word-wrap:break-word;text-align: right; "><p><? echo number_format($trans_type["pre_month_issue"],2);?></p></td>
							</tr>
							<?
							$sl++;
								$grand_opening += $trans_type["opening"];
								$grand_opening_qnty += $trans_type["opening_qnty"];
								$grand_rcv += $trans_type["receive"];
								$grand_trans_in += $trans_type["trans_in"];
								$grand_iss_return += $trans_type["iss_return"];
								$grand_rcv_total += $rcv_total; 
								$grand_issue += $trans_type['issue'];
								$grand_trans_out += $trans_type["trans_out"];
								$grand_rcv_return += $trans_type["rcv_return"];
								$grand_other_issue += $trans_type['issue_other'];
								$grand_issue_total += $issue_total;
								$grand_closing_value += $closing_value;
								$grand_pre_issue += $trans_type["pre_month_issue"];
							}
						}
					}
					//echo $count."== $iss_qty".jahid;
				?>
				</table>
			</div>
			<table width="<? echo $width;?>" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" > 
				<tr>
					<td width="40" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p>&nbsp;</p></td>
					<td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p>Grand Total=</p></td>
					<td width="110" title="<? echo $grand_opening_qnty; ?>" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_opening,2);?></p></td>
					<td width="90" title="rcv" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_rcv,2);?></p></td>
					<td width="110" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_trans_in,2);?></p></td>
					<? if($cbo_store_name > 0){?>
					<td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p>&nbsp;<? ?></p></td>
					<? }?>
					<td width="90" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_iss_return,2);?></p></td>
					<td width="110" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_rcv_total,2);?></p></td>
					<td width="90" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_issue,2);?></p></td>
					<td width="110" title="tr_out" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_trans_out,2);?></p></td>
					<? if($cbo_store_name > 0){?>
					<td width="100" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p>&nbsp;<? ?></p></td>
					<? }?>
					<td width="90" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_rcv_return,2);?></p></td>
					<td width="110" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_other_issue,2)?></p></td>
					<td width="110" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_issue_total,2);?></p></td>
					<td width="110" style="word-break: break-all; word-wrap:break-word;text-align: right;"><p><? echo number_format($grand_closing_value,2);?></p></td>
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