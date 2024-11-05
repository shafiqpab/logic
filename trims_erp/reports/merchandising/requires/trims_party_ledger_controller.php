<?

header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];



//--------------------------------------------------------------------------------------------------------------------


if ($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
	//if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	//else $load_function="";
	//echo $data[2];
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_customer_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- select Company --",'', "");
	}
	else
	{
		echo create_drop_down( "cbo_customer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- select Party --", '', "" );
	}	
	exit();	 
} 


if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//cbo_company_id*cbo_customer_source*cbo_customer_name*cbo_search_by*txt_search_common*txt_date_from*txt_date_to*hid_order_id
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_customer_name=str_replace("'","",$cbo_customer_name);
	$cbo_customer_source=str_replace("'","",$cbo_customer_source);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$txt_search_common=str_replace("'","",$txt_search_common);
	$start_date=str_replace("'","",$txt_date_from);
	$end_date=str_replace("'","",$txt_date_to);
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		//$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$customer_source = array(1 => 'Inside', 2 => 'Outside');
	
	//--------------------------------------------------start	
	if($db_type==0) 
	{
		$start_date=change_date_format($start_date,'yyyy-mm-dd');
		$end_date=change_date_format($end_date,'yyyy-mm-dd');
	}
	else if($db_type==2) 
	{
		$start_date=change_date_format($start_date,'','',1);
		$end_date=change_date_format($end_date,'','',1);
	}
	
	if($db_type==0) 
	{
		//( year_id=2019 and month_id>=1)  or  ( year_id=2020 and month_id<=1) or  ( year_id=2021 and month_id<=1) 
		$date_cond=" and b.bill_date between '$start_date' and '$end_date'";
	}
	if($db_type==2) 
	{
		$date_cond=" and a.bill_date between '".date("j-M-Y",strtotime($start_date))."' and '".date("j-M-Y",strtotime($end_date))."'";
	}

	if($txt_search_common!='')
	{
		if($cbo_search_by==2) 
		{
			$string_cond=" and a.trims_bill like '%$txt_search_common%'";
		}
		else
		{
			$string_cond=" and b.order_no  like '%$txt_search_common%' ";
			/*
			$con = connect();
			$sql_insert_id=sql_select("select a.id from subcon_ord_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$cbo_company_id $within_group_cond and order_no like '%$txt_search_common%' ");
			$id="";
			foreach($sql_insert_id as $iss_id)
			{
				$issue_row_id=$iss_id[csf('id')];
				if($issue_row_id!=0)
				{
					$id=$iss_id[csf('id')];
					$r_id2=execute_query("insert into tmp_poid (userid, poid) values ($user_id,$id)");
					//echo $r_id2; die;
					if($id=="") $id=$iss_id[csf('id')];else $id.=",".$iss_id[csf('id')];
				}
				
			}

			if($db_type==0)
			{
				if($r_id2)
				{
					mysql_query("COMMIT");  
				}
			}
			if($db_type==2 || $db_type==1 )
			{
				if($r_id2)
				{
					oci_commit($con);  
				}
			}
			$string_cond=" and a.received_id in(select poid from tmp_poid where userid=$user_id) ";*/
		}
	}
	
	
	if($cbo_customer_name!=0) $customer_cond=" and a.party_id=$cbo_customer_name";
	if($cbo_customer_source!=0) $within_group_cond=" and a.within_group=$cbo_customer_source";

	
	
	//echo $within_group_cond; die;
	$sql = "select a.received_id,a.company_id,a.id,a.bill_date,a.trims_bill,a.party_id,a.within_group, b.mst_id,b.booking_dtls_id,b.receive_dtls_id, b.job_dtls_id,b.production_dtls_id , b.order_id, b.order_no, b.section, b.item_description, b.challan_no, b.color_id, b.size_id, b.order_uom, b.total_delv_qty, b.previous_bill_qty, b.quantity as delevery_qty, b.wo_rate, b.bill_rate, b.bill_amount,b.job_dtls_id, b.production_dtls_id from trims_bill_mst a, trims_bill_dtls b where a.id=b.mst_id $date_cond $customer_cond $within_group_cond $string_cond order by a.party_id,a.within_group ASC";
	//die;
	$sql_res=sql_select($sql);
		 
	foreach ($sql_res as $row)
	{ 
		$billDataArr[$row[csf("party_id")]][$row[csf("within_group")]][$row[csf("id")]]['company_id']=$row[csf("company_id")];
		$billDataArr[$row[csf("party_id")]][$row[csf("within_group")]][$row[csf("id")]]['trims_bill']=$row[csf("trims_bill")];
		$billDataArr[$row[csf("party_id")]][$row[csf("within_group")]][$row[csf("id")]]['bill_date']=$row[csf("bill_date")];
		$billDataArr[$row[csf("party_id")]][$row[csf("within_group")]][$row[csf("id")]]['bill_amount'] +=$row[csf("bill_amount")];
		$billDataArr[$row[csf("party_id")]][$row[csf("within_group")]][$row[csf("id")]]['order_no'] .=$row[csf("order_no")].',';
	}
	
	//echo "<pre>";	
	//print_r($billDataArr);
	$width=800;

ob_start();
?>
<div style="width:800px;">
	<table align="center" cellspacing="0" width="800"  border="0" >
		<tr><td style="font-size:large" align="center"><strong><? echo $company_arr[$cbo_company_id];?></strong></td></tr>
		<? if($start_date !='' && $end_date!='') {
			?><tr><td style="font-size:large" align="center"> <? echo 'Trims Party Ledger';?></td></tr><?
		}
		?>
	</table>
	<br>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width; ?>" class="rpt_table">
        <thead>
        	<tr>
                <th width="40" >SL</th>
                <th width="120" >Customer Name</th>
                <th width="80" >Customer Source</th>
                <th width="120" >Bill No/MR No</th>
                <th width="80" >Bill/Recv Date</th>
                <th width="100" >Bill Amnt (Tk)</th>
                <th width="100" >Recc/ Adj Amnt (Tk)</th>
                <th >Balance</th>
            </tr>
        </thead>
    </table>
	<div style="width:<? echo $width+18; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width; ?>" class="rpt_table" id="table_body" >
        	<tbody>
			<? 
				$i=1; 
				 
				$lead_var_arr=array(); $totLeadVariQty=0; $totLedVariAmt=0; $totQty=0; $grandAmt=0; $grand_balance=0; $grand_recc_adj_amnt=0;
                foreach($billDataArr as $party_id=>$party_data)
                { 
                	$subAmount=0;  $recc_adj_amnt =0; $tot_balance =0;  $tot_recc_adj_amnt=0;
                	foreach($party_data as $within_group=>$within_group_data)
                    { 
                    	foreach($within_group_data as $id=>$row)
                    	{
                    		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
	                    	$subAmount+=$row['bill_amount'];
	                    	$grandAmt+=$row['bill_amount'];
	                    	$order_no =chop($row['order_no'],',');
	                    	$balance =$row['bill_amount']-$recc_adj_amnt;
	                    	$tot_balance +=$balance ;
	                    	$tot_recc_adj_amnt +=$recc_adj_amnt ;
	                    	$grand_balance+=$balance ;
	                    	$grand_recc_adj_amnt+=$recc_adj_amnt ;
	                    	if($within_group==1) $party_name=$company_arr[$party_id]; else $party_name=$party_arr[$party_id];
	                    	?>
	                    	<tr bgcolor="<? echo $bgcolor; ?>">
		                    	<td  width="40"><? echo $i; ?></td>
		                    	<td width="120"><? echo $party_name; ?></td>
		                    	<td width="80"><? echo $customer_source[$within_group]; ?></td>
		                    	<!-- print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#cbo_within_group').val()+'*'+report_title+'*'+$('#txt_search_common').val()+'*'+$('#txt_bill_no').val()+'*'+$('#txt_bill_date').val(), "challan_print", "requires/trims_bill_issue_controller")  -->
		                    	<td width="120" align="center"><a href='##' style='color:#000' onClick="print_report('<? echo $row['company_id'].'*'.$id.'*'.$within_group.'*'."Trims Bill Entry".'*'.$row['order_no'].'*'.$row['trims_bill'].'*'.$row['bill_date'];?>','challan_print', '../../delivery_billing/requires/trims_bill_issue_controller')"><font color="blue"><strong><? echo $row['trims_bill']; ?></strong></font></a></td>

		                    	<td width="80"><? echo change_date_format($row['bill_date']);?></td>
		                    	<td width="100" align="right"><? echo number_format($row['bill_amount'],2,'.',','); ?></td>
		                    	<td width="100" align="right"><? echo number_format($recc_adj_amnt,2,'.',','); ?> </td>
		                    	<td align="right"><? echo number_format($balance,2,'.',','); ?></td>
		                    </tr>
	                    	<?
	                    	$i++;
	                    }
                    }
                    ?>
                	<tr bgcolor="<? echo  '#fffadd' ; ?>">
                    	<td colspan="5" align="right"><strong> Sub-Total</strong></td>
                    	<td width="100" align="right"><strong><? echo number_format($subAmount,2,'.',','); ?></strong></td>
                    	<td width="100" align="right"><strong><? echo number_format($tot_recc_adj_amnt,2,'.',',');  ?></strong></td>
                    	<td align="right"><strong><? echo number_format($tot_balance,2,'.',',');  ?></strong></td>
                    </tr>
                	<?
                }
                ?>
            </tbody>
            <tfoot>
            	<tr bgcolor="<? echo  '#ddffdf' ; ?>">
                	<td colspan="5" align="right"><strong> Grand Total</strong></td>
                	<td width="100" align="right"><strong><? echo number_format($grandAmt,2,'.',','); ?></strong></td>
                	<td width="100" align="right"><strong><? echo number_format($grand_recc_adj_amnt,2,'.',','); ?></strong></td>
                	<td align="right"><strong><? echo number_format($grand_balance,2,'.',','); ?></strong></td>
                </tr>
            </tfoot>
		</table>
	</div>
</div>
	<?
	/*$r_id3=execute_query("delete from tmp_poid where userid=$user_id");
	if($db_type==0)
	{
		if($r_id3)
		{
			mysql_query("COMMIT");  
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		if($r_id3)
		{
			oci_commit($con);  
		}
	}
	disconnect($con);*/
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
	
	exit();	
}