<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];

if ($action=="load_drop_down_buyer"){
	echo create_drop_down( "cbo_buyer_id", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","0","" );
	exit();
}

if ($action=="report_generate")
{
	extract($_REQUEST);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$txt_job_no=str_replace("'","",$txt_job_no);
    $txt_style_ref=str_replace("'","",$txt_style_ref);
    $txt_order_no=str_replace("'","",$txt_order_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);

	$txt_unapproval_date_from=str_replace("'","",$txt_unapproval_date_from);
	$txt_unapproval_date_to=str_replace("'","",$txt_unapproval_date_to);
	
	$companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$teamArr = return_library_array("select id, team_leader_name from lib_marketing_team ","id","team_leader_name");
	$merchantArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");

	ob_start();
	?>
    <div align="center">
    <table width="1750px" cellspacing="0">
        <tr class="form_caption" style="border:none;">
            <td colspan="19" align="center" style="border:none;font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
        </tr>
        <tr style="border:none;">
            <td colspan="19" align="center" style="border:none; font-size:16px; font-weight:bold"><? echo $companyArr[$cbo_company_id]; ?></td>
        </tr>
        <tr style="border:none;">
            <td colspan="19" align="center" style="border:none;font-size:12px; font-weight:bold">
                <? echo "Claim Date From ".change_date_format($txt_date_from)." To ".change_date_format($txt_date_to); ?>
            </td>
        </tr>
    </table>
    <table width="1200px" cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead>
            <tr style="font-size:13px">
                <th width="30">SL.</th> 
                <th width="100">Job No</th>      
                <th width="100">Buyer </th>               
                <th width="100">Order No</th>
                <th width="100">Style </th>
                <th width="100">Job Insert Date</th>
                <th width="80">Job Qnty(Pcs)</th>   
                <th width="100">Job Value</th>
                <th width="120">Dealing Merchant</th>
                <th width="70">Approval Status</th>                
                <th width="100">1st Final Approval Date</th>
                <th width="80">Approval Duration (Days)</th>   
                <th>Last unapproved Date</th>
             </tr>
        </thead>
    </table>
    <div style="width:1210px; max-height:300px; overflow-y:scroll" id="scroll_body"> 
        <table width="1180px" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
		<?
		if($txt_order_no!="") $po_numberCond=" and b.po_number='$txt_order_no'"; else $po_numberCond="";
        if($txt_style_ref!="") $style_refCond=" and a.style_ref_no='$txt_style_ref'"; else $style_refCond="";
        if($txt_job_no!="") $jobCond=" and a.job_no_prefix_num='$txt_job_no'"; else $jobCond="";
        
        
		if($txt_date_from!="" && $txt_date_to!="") $insertDateCond="and a.insert_date between '$txt_date_from' and '$txt_date_to'"; else $insertDateCond="";
		if($txt_unapproval_date_from!="" && $txt_unapproval_date_to!=""){ $unapprovedDateCond="and d.approved_date between '$txt_unapproval_date_from' and '$txt_unapproval_date_to'";$unapproved="and c.approved in (0,2)";}else{ $unapprovedDateCond="";$unapproved="";}
		
       
		

		
	$sql="select a.id, a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, a.dealing_marchant, a.order_uom, b.id as po_id, b.po_number, b.po_quantity, b.unit_price, b.po_total_price, b.plan_cut, a.insert_date,a.job_quantity,a.total_price,c.id as pre_cost_id,c.approved,c.insert_date as unapproved_date,c.job_id from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c   left join approval_history d on  c.id=d.mst_id and d.entry_form=15  where a.job_no=b.job_no_mst and c.job_id=a.id and a.company_name=$cbo_company_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and b.is_confirmed=1 $insertDateCond $unapprovedDateCond $po_numberCond $style_refCond $jobCond group by a.id, a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, a.dealing_marchant, a.order_uom, b.id , b.po_number, b.po_quantity, b.unit_price, b.po_total_price, b.plan_cut, a.insert_date,a.job_quantity,a.total_price,c.id ,c.approved,c.insert_date ,c.job_id    order by a.id asc";

		//echo $sql;
		$sql_data=sql_select($sql);
		$i=1; $tot_rows=0; 
        //echo count($sql_data); die;
        foreach($sql_data as $row)
		{
                $job_id_arr[$row[csf("pre_cost_id")]]=$row[csf("pre_cost_id")];
        }
    
        $max_sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","company_id=$cbo_company_id and page_id=428 and is_deleted=0");

      
      

       $approved_data=sql_select("select a.job_no,b.id,b.mst_id,b.approved_by,b.approved_no, b.approved_date,b.un_approved_reason,b.SEQUENCE_NO ,a.approved from wo_pre_cost_mst a , approval_history b    where a.id=b.mst_id and b.entry_form=15 and b.sequence_no=$max_sequence_no  ".where_con_using_array($job_id_arr,1,'b.mst_id')." order by b.id asc ");

      
        foreach($approved_data as $row){
            
            $approval_dataArr[$row[csf("mst_id")]][]=$row[csf("approved_date")];
           
        }
        $unapproved_data=sql_select("select a.job_no,b.id,b.mst_id,b.approved_by,b.approved_no, b.approved_date,b.un_approved_reason,b.SEQUENCE_NO ,a.approved,b.CURRENT_APPROVAL_STATUS from wo_pre_cost_mst a , approval_history b where a.id=b.mst_id and b.entry_form=15  and b.CURRENT_APPROVAL_STATUS=0 and b.sequence_no=$max_sequence_no  ".where_con_using_array($job_id_arr,1,'b.mst_id')." order by b.id desc ");
    

       
         foreach($unapproved_data as $row){
             
             $unapproval_dataArr[$row[csf("mst_id")]][]=$row[csf("approved_date")];
             
         }
     
    //    echo "<pre>";
    //    print_r($approval_dataArr);
        
        $grand_po_qty=0;
        $date2="";
        $i=1;
		foreach($sql_data as $row)
		{

			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$k=1; $tot_rows++;
		
				if($k==1) $style_color=''; else $style_color=$bgcolor."; border: none"; 
                $date1=change_date_format($row[csf("insert_date")]);;
              
                if($approval_dataArr[$row[csf("pre_cost_id")]][0]!=""){
          
                 $date2=change_date_format($approval_dataArr[$row[csf("pre_cost_id")]][0]);
                }else{
                    $date2 = date( 'Y-m-d');
                }
                
             
               
                $diff = abs(strtotime($date1) - strtotime($date2));
                
                $days = floor($diff / (60*60*24));
            



				?>
				<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
					<td width="30" style="color:<? echo $style_color; ?>"><? echo $i; ?></td>
                    <td width="100" style="word-break:break-all;color:<? echo $style_color; ?>"><? echo $row[csf("job_no")]; ?></td>
                    <td width="100" style="word-break:break-all;color:<? echo $style_color; ?>"><? echo $buyerArr[$row[csf("buyer_name")]]; ?></td>
                    <td width="100" style="word-break:break-all;color:<? echo $style_color; ?>"><? echo $row[csf("po_number")]; ?></td>
                    <td width="100" style="word-break:break-all;color:<? echo $style_color; ?>"><? echo $row[csf("style_ref_no")]; ?></td>

                    <td width="100" style="color:<? echo $style_color; ?>"><? echo change_date_format($row[csf("insert_date")]); ?></td> 
                    <td width="80" style="color:<? echo $style_color; ?>" align="right"><? echo number_format($row[csf("job_quantity")],2); ?></td>
                    <td width="100" style="word-break:break-all;color:<? echo $style_color; ?>"><? echo $row[csf("total_price")]; ?></td>                
                    <td width="120" style="word-break:break-all;color:<? echo $style_color; ?>"><? echo $merchantArr[$row[csf("dealing_marchant")]]; ?></td>                   
               
                    <td width="70" style="color:<? echo $style_color; ?>"><? if($row[csf("approved")]==1){ echo "Approved";}elseif($row[csf("approved")]==2){ echo "Un-Approved";}else{
                        echo "Partially Approved";
                    }; ?></td>
                    <td width="100" style="color:<? echo $style_color; ?>" title="<?=$row[csf("pre_cost_id")];?>"><? echo change_date_format($approval_dataArr[$row[csf("pre_cost_id")]][0]); ?></td>
                    <td width="80" style="color:<? echo $style_color; ?>"><? echo $days; ?></td>
             

                    <td style="word-break:break-all;color:<? echo $style_color; ?>"><? echo  change_date_format($unapproval_dataArr[$row[csf("pre_cost_id")]][0]); ?></td>
                 </tr>   
            	<?
				$k++;
            
			$grand_po_qty+=$row[csf("po_quantity")];
			$grand_po_value+=$row[csf("po_total_price")];
			$grand_exfactory_value+=$exFactoryValue;
			$grand_claim_value+=$row[csf("base_on_ex_val")];
			$i++;
        }
        ?>
        </table>
    </div>
    <table width="1200px" cellspacing="0" border="1" class="tbl_bottom" rules="all">
        <tr style="font-size:13px">
            <td width="30">&nbsp;</td>            
            <td width="100">&nbsp;</td>   
            <td width="100">&nbsp;</td>
            <td width="100">&nbsp;</td>
            <td width="100">&nbsp;</td>
            <td width="100">&nbsp;</td>
            <td width="80">&nbsp;</td>   
            <td width="100">&nbsp;</td>
            <td width="120" align="right" id="td_exfactory_value"></td>
            <td width="70" align="right" id="td_claim_value"></td>
            <td width="100">&nbsp;</td>          
            <td width="80">&nbsp;</td>
            <td>&nbsp;</td>
         </tr>
    </table>
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
	echo "$total_data####$filename####$tot_rows";
	exit();
}
?>
