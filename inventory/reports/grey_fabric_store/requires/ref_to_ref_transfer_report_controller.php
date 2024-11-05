<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
    //$data=explode('_',$data);
    echo create_drop_down( "cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0);  
    exit();
}


if($action=="generate_report")
{ 
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
    
    $cbo_company=str_replace("'","",$cbo_company_id);
    $cbo_report=str_replace("'","",$cbo_report_type);
    $cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
    $txt_job_no=str_replace("'","",$txt_job_no);
    $txt_file_no=str_replace("'","",$txt_file_no);
    $txt_ref_no=str_replace("'","",$txt_ref_no);
    $txt_order_no=str_replace("'","",$txt_order_no);
    $txt_style_ref_no=str_replace("'","",$txt_style_ref_no);
    $txt_batch_no=str_replace("'","",$txt_batch_no);
    $date_from=str_replace("'","",$from_date);
    $date_to=str_replace("'","",$to_date);
    
    if($db_type==0)
    {
        if($date_from!="") $date_from=change_date_format($date_from,'yyyy-mm-dd');
        if($date_to!="") $date_to=change_date_format($date_to,'yyyy-mm-dd');
    }
    else
    {
        if($date_from!="") $date_from=change_date_format($date_from,'','',1);
        if($date_to!="") $date_to=change_date_format($date_to,'','',1);
    }
    //echo $date_from."==".$date_to;die;

    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
    $buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    
    $job_order_cond="";    
    if($cbo_buyer_id>0) $job_order_cond.=" and a.buyer_name=$cbo_buyer_id";
    if($txt_job_no!="") $job_order_cond.=" and  a.job_no_prefix_num=$txt_job_no";
    if($txt_style_ref_no!="") $job_order_cond.=" and  a.style_ref_no='$txt_style_ref_no'";
    if($txt_order_no!="") $job_order_cond.=" and  b.po_number='$txt_order_no'";
    if($txt_file_no!="") $job_order_cond.=" and  b.file_no='$txt_file_no'";
    if($txt_ref_no!="") $job_order_cond.=" and b.grouping='$txt_ref_no'";
   
    $job_ord_sql="select a.id as job_id, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, a.remarks, b.id as po_id, b.po_number, b.file_no, b.grouping as ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $job_order_cond";
   // echo $job_order_cond;die;
	$con=connect();
	execute_query("delete from GBL_TEMP_REPORT_ID where user_id=$user_id",1);
	oci_commit($con);
	
    $job_ord_result=sql_select($job_ord_sql);
    $all_order_id=""; $job_order_data=array();    
    foreach($job_ord_result as $row)
    {
        //$all_order_id.=$row[csf("po_id")].",";
        $job_order_data[$row[csf("po_id")]]["job_id"]=$row[csf("job_id")];
        $job_order_data[$row[csf("po_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
        $job_order_data[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
        $job_order_data[$row[csf("po_id")]]["buyer_name"]=$row[csf("buyer_name")];
        $job_order_data[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
        $job_order_data[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
        $job_order_data[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
        $job_order_data[$row[csf("po_id")]]["file_no"]=$row[csf("file_no")];
        $job_order_data[$row[csf("po_id")]]["ref_no"]=$row[csf("ref_no")];
        $job_order_data[$row[csf("po_id")]]["remarks"]=$row[csf("remarks")];
		if($job_order_cond!='') execute_query("insert into GBL_TEMP_REPORT_ID(ref_val,user_id) values(".$row[csf("po_id")].",".$user_id.")",0);
		
    }   
    //$all_order_id=chop($all_order_id,",");
    //echo $all_order_id;
    oci_commit($con);
	//if($job_order_cond!='') oci_commit($con); else oci_rollback($con);
    ob_start(); 
    //echo "tofa";die;
    ?>
    <div>
    <table width="2120" cellpadding="0" cellspacing="0" id="caption" align="center">
        <tr>
           <td align="center" width="100%" colspan="16" class="form_caption"><strong style="font-size:22px"><? echo $company_library[$cbo_company]; ?></strong></td>
        </tr> 
        <tr>  
           <td align="center" width="100%" colspan="16" class="form_caption" ><strong style="font-size:16px">Ref. To Ref. Transfer Report</strong></td>
        </tr>         
    </table>
    <br />
    <table width="2120" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
        <thead>
            <tr>
                <th width="35" rowspan="2">SL</th>
                <th colspan="10"><strong>From Order</strong></th>
                <th width="20"></th>
                <th colspan="9"><strong>To Order</strong></th>
            </tr>
            <tr>
                <th width="120">Buyer:</th>
                <th width="80">Order No:</th>
                <th width="80">Style Ref:</th>
                <th width="80">Job No:</th>
                <th width="80">Ref No:</th>
                <th width="80">File No:</th>
                <th width="80">Batch No:</th>
                <th width="80">QTY</th>
                <th width="120">Transfer ID:</th>
                <th width="80">Transfer Date</th>
                <th width="20"></th>

                <th width="120">Buyer:</th>
                <th width="80">Order No:</th>
                <th width="80">Style Ref:</th>
                <th width="80">Job No:</th>
                <th width="80">Ref No:</th>
                <th width="80">File No:</th>
                <th width="80">Batch No:</th>
                <th width="80">QTY</th>
                <th width="150">Remarks</th>
            </tr>
        </thead>
    </table>
    <div style="width:2140px; max-height:300px; overflow-y:scroll" id="scroll_body" > 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2120" class="rpt_table" id="tbl_issue_status" >
        <?

        //$order_transfer_cond="";
		//echo count(explode(",",$all_order_id));die;
       /* if($job_order_cond!="") 
        {
			$all_order_id_all=array_chunk(array_unique(explode(",",$all_order_id)),999);
			$order_transfer_cond=" and (";
			$i=1;
			foreach($all_order_id_all as $ord_id_arr)
			{
				if($i<12)
				{
					
				}
				
				if($order_transfer_cond==" and (") $order_transfer_cond.=" a.from_order_id in(".implode(',',$ord_id_arr).") or a.to_order_id in(".implode(',',$ord_id_arr).")"; else $order_transfer_cond.=" or a.from_order_id in(".implode(',',$ord_id_arr).") or a.to_order_id in(".implode(',',$ord_id_arr).")";
				//if($order_transfer_cond==" and (") $order_transfer_cond.=" a.from_order_id in(".implode(',',$ord_id_arr).")"; else $order_transfer_cond.=" or a.from_order_id in(".implode(',',$ord_id_arr).")";
				$i++;
				
			}
			$order_transfer_cond.=")";
            //$order_transfer_cond=" and (a.from_order_id in($all_order_id) or a.to_order_id in($all_order_id)) ";
        } */                
        /*$sql_transfer="select a.id as mst_id, a.transfer_prefix_number, a.transfer_system_id, a.transfer_date, a.from_order_id, a.to_order_id, 
        sum( case when b.transaction_type=6 then b.cons_quantity else 0 end) as from_order_qnty, sum( case when b.transaction_type=5 then b.cons_quantity else 0 end) as to_order_qnty 
        from inv_item_transfer_mst a, inv_transaction b
        where a.id=b.mst_id and a.transfer_criteria=4 and a.item_category=13 and b.item_category=13 and b.transaction_type in(5,6) and a.company_id=$cbo_company and a.transfer_date between '$date_from' and '$date_to' and  $order_transfer_cond
        group by a.id, a.transfer_prefix_number, a.transfer_system_id, a.transfer_date, a.from_order_id, a.to_order_id";*/



		
		if($job_order_cond=="") 
        {
			/*$sql_transfer="SELECT a.id as mst_id, a.transfer_prefix_number, a.transfer_system_id, a.transfer_date, a.from_order_id, a.to_order_id, 
			sum( case when b.transaction_type=6 then b.cons_quantity else 0 end) as from_order_qnty, sum( case when b.transaction_type=5 then b.cons_quantity else 0 end) as to_order_qnty 
			from inv_item_transfer_mst a, inv_transaction b
			where a.id=b.mst_id and a.transfer_criteria=4 and a.item_category=13 and b.item_category=13 and b.transaction_type in(5,6) and a.company_id=$cbo_company and a.transfer_date between '$date_from' and '$date_to'
			group by a.id, a.transfer_prefix_number, a.transfer_system_id, a.transfer_date, a.from_order_id, a.to_order_id";*/

            $sql_transfer="SELECT a.id as mst_id, a.transfer_prefix_number, a.transfer_system_id, a.transfer_date, c.from_order_id, c.to_order_id,
            sum(c.transfer_qnty) as qty
            from inv_item_transfer_mst a, inv_item_transfer_dtls c
            where a.id=c.mst_id and a.transfer_criteria=4 and a.item_category=13  and a.company_id=$cbo_company and a.transfer_date between '$date_from' and '$date_to' and c.item_category=13 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 
            group by a.id, a.transfer_prefix_number, a.transfer_system_id, a.transfer_date, c.from_order_id, c.to_order_id";
		}
		else
		{
			/*$sql_transfer="SELECT a.id as mst_id, a.transfer_prefix_number, a.transfer_system_id, a.transfer_date, a.from_order_id, a.to_order_id, 
			sum( case when b.transaction_type=6 then b.cons_quantity else 0 end) as from_order_qnty, sum( case when b.transaction_type=5 then b.cons_quantity else 0 end) as to_order_qnty 
			from inv_item_transfer_mst a, inv_transaction b, GBL_TEMP_REPORT_ID c 
			where a.id=b.mst_id and a.transfer_criteria=4 and a.item_category=13 and b.item_category=13 and b.transaction_type in(5,6) and a.company_id=$cbo_company and a.transfer_date between '$date_from' and '$date_to' and ( a.from_order_id=c.ref_val or a.to_order_id=c.ref_val )
			group by a.id, a.transfer_prefix_number, a.transfer_system_id, a.transfer_date, a.from_order_id, a.to_order_id";*/
            $sql_transfer="SELECT a.id as mst_id, a.transfer_prefix_number, a.transfer_system_id, a.transfer_date, b.from_order_id, b.to_order_id,
            sum(b.transfer_qnty) as qty
            from inv_item_transfer_mst a, inv_item_transfer_dtls b, gbl_temp_report_id c 
            where a.id=b.mst_id and a.transfer_criteria=4 and a.item_category=13 and b.item_category=13
            and a.company_id=$cbo_company and a.transfer_date between '$date_from' and '$date_to' and ( b.from_order_id=c.ref_val or b.to_order_id=c.ref_val )
            group by a.id, a.transfer_prefix_number, a.transfer_system_id, a.transfer_date, b.from_order_id, b.to_order_id";
		}
		 
		
        //echo $sql_transfer;die;

        $i=1;
        $sql_transfer_result=sql_select($sql_transfer);
        foreach($sql_transfer_result as $row)
        {
            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
            <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                <td width="35"><p><? echo $i; ?></p></td>
                <td width="120"><? echo $buyer_arr[$job_order_data[$row[csf("from_order_id")]]["buyer_name"]]; ?></td>
                <td width="80"><? echo $job_order_data[$row[csf("from_order_id")]]["po_number"]; ?></td>
                <td width="80"><? echo $job_order_data[$row[csf("from_order_id")]]["style_ref_no"]; ?></td>
                <td width="80"><? echo $job_order_data[$row[csf("from_order_id")]]["job_no"]; ?></td>
                <td width="80"><? echo $job_order_data[$row[csf("from_order_id")]]["ref_no"]; ?></td>
                <td width="80"><? echo $job_order_data[$row[csf("from_order_id")]]["file_no"]; ?></td>
                <td width="80"></td>
                <td width="80" align="right"><? echo number_format($row[csf("qty")],2); ?></td>
                <td width="120" align="center"><? echo $row[csf("transfer_system_id")]; ?></td>
                <td width="80" align="center"><? echo $row[csf("transfer_date")]; ?></td>

                <td width="20"><? //echo ; ?></td>

                <td width="120"><? echo  $buyer_arr[$job_order_data[$row[csf("to_order_id")]]["buyer_name"]]; ?></td>
                <td width="80"><? echo $job_order_data[$row[csf("to_order_id")]]["po_number"]; ?></td>
                <td width="80"><? echo $job_order_data[$row[csf("to_order_id")]]["style_ref_no"]; ?></td>
                <td width="80"><? echo $job_order_data[$row[csf("to_order_id")]]["job_no"]; ?></td>
                <td width="80"><? echo $job_order_data[$row[csf("to_order_id")]]["ref_no"]; ?></td>
                <td width="80"><? echo $job_order_data[$row[csf("to_order_id")]]["file_no"]; ?></td>
                <td width="80"><? //echo $i; ?></td>
                <td width="80" align="right"><? echo number_format($row[csf("qty")],2); ?></td>
                <td width="150"><? echo $job_order_data[$row[csf("to_order_id")]]["remarks"]; ?></td>
             </tr>
            <?
            $from_order_qnty+=$row[csf("qty")];
            $to_order_qnty  +=$row[csf("qty")];
            $i++;
        }
        ?>
        </table>
        <table width="2120 " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
            <tfoot>
                <th width="35">&nbsp;</th>
                <th width="120">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80">&nbsp; Total:</th>
                <th width="80" align="right" id="tot_qnty"><? echo number_format($from_order_qnty,2); ?></th>
                <th width="120">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="20">&nbsp;</th>

                <th width="120">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80">&nbsp; Total:</th>
                <th width="80" align="right" id="tot_qnty"><? echo number_format($to_order_qnty,2); ?></th>
                <th width="150">&nbsp;</th>
            </tfoot>
        </table>
    </div>
    </div>

    <?
    foreach (glob("$user_id*.xls") as $filename) 
    {
        if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc,ob_get_contents());
    $filename=$user_id."_".$name.".xls";
    //echo "$total_data####$filename";
    exit();      
    
}
?>