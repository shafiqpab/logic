<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_store")
{
	$data=explode('_',$data);
	echo create_drop_down( "cbo_store_id", 120, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[0]' and b.category_type=1 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select Store--", 0, "" );
	exit();
}

if ($action=="item_account_popup")
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
		$('#item_account_id').val( id );
		$('#item_account_val').val( ddd );
	} 
		  
	</script>
     <input type="hidden" id="item_account_id" />
     <input type="hidden" id="item_account_val" />
 <?
	
	$sql="SELECT id, item_category_id, product_name_details from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0"; 
	$arr=array(0=>$item_category);
	echo  create_list_view("list_view", "Item Category,Fabric Description,Product ID", "120,250","490","300",0, $sql , "js_set_value", "id,product_name_details", "", 1, "item_category_id,0,0", $arr , "item_category_id,product_name_details,id", "",'setFilterGrid("list_view",-1);','0,0,0,0,0,0','',1) ;
	exit();
}

if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	if ($cbo_company_id==0) $company_id =""; else $company_id =" and a.company_id='$cbo_company_id'";
	if ($txt_product_id=="") 
	{
		$item_account=""; 
		$prod_cond="";
	}
	else 
	{
		$item_account=" and a.prod_id in ($txt_product_id)";
		$prod_cond=" and id in ($txt_product_id)";
	}
	
	if ($cbo_store_id==0){ $store_id="";}else{$store_id=" and a.store_id='$cbo_store_id'";}
	
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
	
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
	$determinaArr = return_library_array("select id,construction from lib_yarn_count_determina_mst where status_active=1 and is_deleted=0","id","construction");
	
	$data_array=array();
	$trnasactionData=sql_select("Select b.id,
		sum(case when a.transaction_type=1 and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
		sum(case when a.transaction_type=2 and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
		sum(case when a.transaction_type=3 and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as rcv_return_opening,
		sum(case when a.transaction_type=4 and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as iss_return_opening,
		sum(case when a.transaction_type=1 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as receive,
		sum(case when a.transaction_type=2 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue,
		sum(case when a.transaction_type=3 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as rec_return,
		sum(case when a.transaction_type=4 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue_return
		from inv_transaction a, product_details_master b
		where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.order_id=0 $company_id $item_category_id $store_id $item_account group by b.id order by b.id ASC");	
		
	foreach($trnasactionData as $row)
	{
		$data_array[$row[csf("id")]]['rcv_total_opening']=$row[csf("rcv_total_opening")];
		$data_array[$row[csf("id")]]['iss_total_opening']=$row[csf("iss_total_opening")];
		$data_array[$row[csf("id")]]['rcv_return_opening']=$row[csf("rcv_return_opening")];
		$data_array[$row[csf("id")]]['iss_return_opening']=$row[csf("iss_return_opening")];
		$data_array[$row[csf("id")]]['receive']=$row[csf("receive")];
		$data_array[$row[csf("id")]]['issue']=$row[csf("issue")];
		$data_array[$row[csf("id")]]['rec_return']=$row[csf("rec_return")];
		$data_array[$row[csf("id")]]['issue_return']=$row[csf("issue_return")];
	}

	$date_array=array();
	$dateRes_date="select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category=13 group by prod_id";
	$result_dateRes_date = sql_select($dateRes_date);
	foreach($result_dateRes_date as $row)	
	{
		$date_array[$row[csf("prod_id")]]['min_date']=$row[csf("min_date")];
		$date_array[$row[csf("prod_id")]]['max_date']=$row[csf("max_date")];
	}
	
	ob_start();	
	?>
    <div>
        <table style="width:1541px" border="1" cellpadding="2" cellspacing="0"  id="caption" rules="all"> 
            <thead>
                <tr class="form_caption" style="border:none;">
                    <td colspan="17" align="center" style="border:none;font-size:16px; font-weight:bold" >Grey Fabric <? echo $report_title; ?></td> 
                </tr>
                <tr class="form_caption" style="border:none;">
                    <td colspan="17" align="center" style="border:none; font-size:14px;">
                       <b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>                               
                    </td>
                </tr>
                <tr class="form_caption" style="border:none;">
                    <td colspan="17" align="center" style="border:none;font-size:12px; font-weight:bold">
                        <? if($from_date!="" || $to_date!="") echo "From : ".change_date_format($from_date)." To : ".change_date_format($to_date)."" ;?>
                    </td>
                </tr>
            </thead>
        </table>
        <table width="1541" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        	<thead>
                <tr>
                    <th rowspan="2" width="40">SL</th>
                    <th rowspan="2" width="60">Prod.ID</th>
                    <th colspan="4">Description</th>
                    <th rowspan="2" width="110">Opening Stock</th>
                    <th colspan="4">Receive</th>
                    <th colspan="4">Issue</th>
                    <th rowspan="2" width="100">Closing Stock</th>
                    <th rowspan="2">DOH</th>
                </tr> 
                <tr>                         
                    <th width="120">Construction</th>
                    <th width="180">Composition</th>
                    <th width="70">GSM</th>
                    <th width="100">Dia/Width</th>
                    
                    <th width="80">Receive</th>
                    <th width="80">Issue Return</th>
                    <th width="80">Transfer In</th>
                    <th width="100">Total Receive</th>
                    
                    <th width="80">Issue</th>
                    <th width="80">Receive Return</th>
                    <th width="80">Transfer Out</th>
                    <th width="100">Total Issue</th> 
                </tr> 
            </thead>
        </table>
        <div style="width:1560px; max-height:350px; overflow-y:scroll" id="scroll_body" > 
            <table width="1541" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
            <?
                $composition_arr=array(); $i=1;
                $sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
                $deterdata_array=sql_select($sql_deter);
                if(count($deterdata_array)>0)
                {
                    foreach( $deterdata_array as $row )
                    {
                        if(array_key_exists($row[csf('id')],$composition_arr))
                        {
                            $composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
                        }
                        else
                        {
                            $composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
                        }
                    }
                }
                $trans = return_library_array("select  prod_id, sum(quantity) as qnty from order_wise_pro_details where entry_form in(2,22) and trans_id!=0 and trans_type=1 and status_active=1 and is_deleted=0 group by prod_id order by prod_id","prod_id","qnty");
              echo $sql="select id, detarmination_id, gsm, dia_width, current_stock from product_details_master a where status_active=1 and is_deleted=0 and company_id='$cbo_company_id' and item_category_id='1' $prod_cond order by id"; 	
                $result = sql_select($sql);
                foreach($result as $row)
                {
                    if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
    
                    $opening=($data_array[$row[csf("id")]]['rcv_total_opening']+$data_array[$row[csf("id")]]['iss_return_opening'])-($data_array[$row[csf("id")]]['iss_total_opening']+$data_array[$row[csf("id")]]['rcv_return_opening']);

                    $receive = $data_array[$row[csf("id")]]['receive'];
					$issue_return = $data_array[$row[csf("id")]]['issue_return'];
                    $transfer_in = $data_array[$row[csf("id")]]['transfer_in'];
                    $totalReceive=$receive+$issue_return+$transfer_in;
					
                    $issue = $data_array[$row[csf("id")]]['issue'];
					$rec_return = $data_array[$row[csf("id")]]['rec_return'];
                    $transfer_out = $data_array[$row[csf("id")]]['transfer_out'];
                    $totalIssue=$issue+$rec_return+$transfer_out;
                    
                    $closingStock=$opening+$totalReceive-$totalIssue;
                    $totalStockValue+=$closingStock;
                    
                    $tot_opening+=$opening;
                    $tot_transfer_in+=$transfer_in;
                    $tot_transfer_out+=$transfer_out;
                    $total_receive+=$totalReceive;
                    $tot_issue+=$totalIssue;
					$tot_issue_return+=$issue_return;
					$tot_rec_return+=$rec_return;
                    
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="40"><? echo $i; ?></td>	
                            <td width="60" align="center"><p><? echo $row[csf("id")]; ?></p></td>
                            <td width="120"><? echo $determinaArr[$row[csf("detarmination_id")]]; ?></td>                                 
                            <td width="180"><p><? echo $composition_arr[$row[csf('detarmination_id')]]; ?></p></td>
                            <td width="70"><p><? echo $row[csf("gsm")]; ?></p></td> 
                            <td width="100"><p><? echo $row[csf("dia_width")]; ?></p></td> 
                            <td width="110" align="right"><p><? echo number_format($opening,2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($receive,2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($issue_return,2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($transfer_in,2); ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($totalReceive,2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($issue,2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($rec_return,2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($transfer_out,2); ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($totalIssue,2); ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($closingStock,2); ?></p></td>
                            <? $daysOnHand = datediff("d",$date_array[$row[csf("id")]]['max_date'],date("Y-m-d")); ?>
                            <td align="center"><? echo $daysOnHand; ?></td>
                        </tr>
                    <? 												
                     $i++; 				
					}
				?>
            </table>
		</div> 
        <table width="1541" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" > 
           <tr>
                <td width="40" align="right">&nbsp;</th>
                <td width="60" align="right">&nbsp;</th>  
                <td width="120" align="right">&nbsp;</th>
                <td width="180" align="right">&nbsp;</th>
                <td width="70" align="right">&nbsp;</th>
                <td width="100" align="right">Total</th>

                <td width="110" align="right" id="value_tot_opening"><? echo number_format($tot_opening,2);  ?></td>
                <td width="80" align="right" id="value_tot_receive"><? echo number_format($tot_receive,2);  ?></td>
                <td width="80" align="right" id="value_tot_issue_return"><? echo number_format($tot_issue_return,2);  ?></td>
                <td width="80" align="right" id="value_tot_trans_in"><? echo number_format($tot_transfer_in,2);  ?></td>
                <td width="100" align="right" id="value_total_receive"><? echo number_format($total_receive,2);  ?></td>
                
                <td width="80" align="right" id="value_tot_issue"><? echo number_format($tot_issue,2);  ?></td>
                <td width="80" align="right" id="value_tot_rec_return"><? echo number_format($tot_rec_return,2);  ?></td>
                <td width="80" align="right" id="value_tot_transfer_out"><? echo number_format($tot_transfer_out,2);  ?></td>
                <td width="100" align="right" id="value_total_issue"><? echo number_format($total_issue,2);  ?></td>
                <td width="100" align="right" id="value_totalStock"><? echo number_format($totalStockValue,2); ?></td>
                <td align="right">&nbsp;</td>
            </tr>
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
?>