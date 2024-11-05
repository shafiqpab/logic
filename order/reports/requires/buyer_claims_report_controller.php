<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer"){
	echo create_drop_down( "cbo_buyer_id", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","0","" );
	exit();
}

if ($action=="report_generate")
{
	extract($_REQUEST);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$txt_exdate_from=str_replace("'","",$txt_exdate_from);
	$txt_exdate_to=str_replace("'","",$txt_exdate_to);
	
	$companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$teamArr = return_library_array("select id, team_leader_name from lib_marketing_team ","id","team_leader_name");
	$merchantArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	
	ob_start();
	?>
    <div align="center">
    <table width="2020px" cellspacing="0">
        <tr class="form_caption" style="border:none;">
            <td colspan="19" align="center" style="border:none;font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
        </tr>
        <tr style="border:none;">
            <td colspan="22" align="center" style="border:none; font-size:16px; font-weight:bold"><? echo $companyArr[$cbo_company_id]; ?></td>
        </tr>
        <tr style="border:none;">
            <td colspan="22" align="center" style="border:none;font-size:12px; font-weight:bold">
                <? echo "Claim Date From ".change_date_format($txt_date_from)." To ".change_date_format($txt_date_to); ?>
            </td>
        </tr>
    </table>
    <table width="2035px" cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead>
            <tr style="font-size:13px">
                <th width="30">SL.</th> 
                <th width="70">Claim Date</th>
                <th width="100">Team Leader</th>   
                <th width="100">Dealing Merchant</th>
                <th width="100">Buyer Name</th>
                <th width="100">Style Name</th>
                <th width="100">Job No</th>
                <th width="100">PO Number</th>
                <th width="80">Order Qty</th>
                
                <th width="80">Order Value</th>
                <th width="70">Shipment Date</th>   
                <th width="70">Ex-factory Date</th>
                <th width="80">Ex-factory Value</th>
                <th width="80">Claim Value</th>

                <th width="100">Discount Val</th>
                <th width="100">Air Freight Val</th>
                <th width="100">Sea Freight Val</th>

                <th width="100">Inspected Comp</th>
                <th width="130">NATURE OF CLAIMS</th>
                <th width="150">Remarks</th>
                <th width="80">Responsible Dept</th>
                <th width="100">Claim Validated By</th>
             </tr>
        </thead>
    </table>
    <div style="width:2048px; max-height:300px; overflow-y:scroll" id="scroll_body" align="left"> 
        <table width="2035px" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left">
		<?
		if($cbo_buyer_id!=0) $buyerCond=" and a.buyer_name='$cbo_buyer_id'"; else $buyerCond="";
		
		if($txt_date_from!="" && $txt_date_to!="") $claimDateCond="and d.claim_entry_date between '$txt_date_from' and '$txt_date_to'"; else $claimDateCond="";
		if($txt_exdate_from!="" && $txt_exdate_to!="") $exfactoryDateCond="and c.ex_factory_date between '$txt_exdate_from' and '$txt_exdate_to'"; else $exfactoryDateCond="";
		
		$sql_dtls="select id, mst_id, claim_id, remarks from wo_buyer_claim_dtls where status_active=1 and is_deleted=0";
		//echo $sql_dtls;
		$sql_dtls_arr=sql_select($sql_dtls); $claim_nature_arr=array();
		foreach($sql_dtls_arr as $row)
		{
			$claim_nature_arr[$row[csf("mst_id")]]['dtls'][$row[csf("id")]]=$row[csf("claim_id")].'***'.$row[csf("remarks")];
		}
		unset($sql_dtls_arr);
		
	$sql="select a.id, a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.garments_nature, a.job_quantity, a.team_leader, a.dealing_marchant, a.order_uom, b.id as po_id, b.po_number, b.po_quantity, b.unit_price, b.po_total_price, b.plan_cut, b.pub_shipment_date, MAX(c.ex_factory_date) AS ex_factory_date, sum(CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN c.entry_form=85 THEN c.ex_factory_qnty ELSE 0 END) AS ex_factory_qnty, d.id as dtls_id, d.claim_entry_date, d.claim_amount_per, d.base_on_ex_val, d.inspected_by, d.inspected_company, d.comments, d.responsible_dept, d.claim_validated_by,d.air_freight,d.sea_freight,d.discount
	
		from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c, wo_buyer_claim_mst d where a.job_no=b.job_no_mst and a.company_name='$cbo_company_id' and b.id=c.po_break_down_id and b.id=d.po_id and c.po_break_down_id=d.po_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $buyerCond $claimDateCond $exfactoryDateCond
		
		
	group by a.id, a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.garments_nature, a.job_quantity, a.team_leader, a.dealing_marchant, a.order_uom, b.id, b.po_number, b.po_quantity, b.unit_price, b.po_total_price, b.plan_cut, b.pub_shipment_date, d.id, d.claim_entry_date, d.claim_amount_per, d.base_on_ex_val, d.inspected_by, d.inspected_company, d.comments, d.responsible_dept, d.claim_validated_by,d.air_freight,d.sea_freight,d.discount
		order by b.id DESC";
		//echo $sql;
		$sql_data=sql_select($sql);
		$i=1; $tot_rows=0; 
        //echo count($sql_data); die;
        $grand_po_qty=0;
		foreach($sql_data as $row)
		{
			$claim_data=$claim_nature_arr[$row[csf("dtls_id")]]['dtls'];
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$k=1; $tot_rows++;
			foreach($claim_nature_arr[$row[csf("dtls_id")]]['dtls'] as $id=>$cdata)
			{
				if($k==1) $style_color=''; else $style_color=$bgcolor."; border: none"; 
				$exFactoryValue=0;
				$exFactoryValue=$row[csf("ex_factory_qnty")]*$row[csf("unit_price")];    
				?>
				<tr align="left" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
					<td width="30" style="color:<? echo $style_color; ?>"><? echo $i; ?></td>
                    <td width="70" style="color:<? echo $style_color; ?>"><? echo change_date_format($row[csf("claim_entry_date")]); ?></td>
                    <td width="100" style="word-break:break-all;color:<? echo $style_color; ?>"><? echo $teamArr[$row[csf("team_leader")]]; ?></td>
                    <td width="100" style="word-break:break-all;color:<? echo $style_color; ?>"><? echo $merchantArr[$row[csf("dealing_marchant")]]; ?></td>
                    <td width="100" style="word-break:break-all;color:<? echo $style_color; ?>"><? echo $buyerArr[$row[csf("buyer_name")]]; ?></td>
                    <td width="100" style="word-break:break-all;color:<? echo $style_color; ?>"><? echo $row[csf("style_ref_no")]; ?></td>
                    <td width="100" style="word-break:break-all;color:<? echo $style_color; ?>"><? echo $row[csf("job_no")]; ?></td>
                    <td width="100" style="word-break:break-all;color:<? echo $style_color; ?>"><? echo $row[csf("po_number")]; ?></td>
                    <td width="80" style="word-break:break-all;color:<? echo $style_color; ?>" align="right"><? echo number_format($row[csf("po_quantity")],0);  ?></td>
                    
                    <td width="80" style="word-break:break-all;color:<? echo $style_color; ?>" align="right"><? echo number_format($row[csf("po_total_price")],2); ?></td>
                  
                    <td width="70" style="word-break:break-all;color:<? echo $style_color; ?>"><? echo change_date_format($row[csf("pub_shipment_date")]); ?></td>
                    <td width="70" style="word-break:break-all;color:<? echo $style_color; ?>"><? echo change_date_format($row[csf("ex_factory_date")]); ?></td>
                    <td width="80" style="word-break:break-all;color:<? echo $style_color; ?>" align="right"><? echo number_format($exFactoryValue,2); ?></td>
                    <td width="80" style="word-break:break-all;color:<? echo $style_color; ?>" align="right"><? echo number_format($row[csf("base_on_ex_val")],2); ?></td>

                    <td width="100" style="word-break:break-all;color:<? echo $style_color; ?>" align="right"><? echo $row[csf("discount")]; ?></td>
                    <td width="100" style="word-break:break-all;color:<? echo $style_color; ?>" align="right"><? echo $row[csf("air_freight")]; ?></td>
                    <td width="100" style="word-break:break-all;color:<? echo $style_color; ?>" align="right"><? echo $row[csf("sea_freight")]; ?></td>
                    


                    <td width="100" style="word-break:break-all;color:<? echo $style_color; ?>"><? echo $row[csf("inspected_company")]; ?></td>
                    <?
						$claim_id=$claim_remarks="";
						$excdata=explode("***",$cdata);
						$claim_id=$excdata[0];
						$claim_remarks=$excdata[1];
					?>
					<td width="130" style="word-break:break-all"><? echo $nature_of_buyer_claim[$claim_id]; ?></td>
					<td width="150" style="word-break:break-all"><? echo $claim_remarks; ?></td>
                    <td width="80" style="word-break:break-all;color:<? echo $style_color; ?>"><? echo $row[csf("responsible_dept")]; ?></td>
                    <td width="100"  style="word-break:break-all;color:<? echo $style_color; ?>"><? echo $row[csf("claim_validated_by")]; ?></td>
                 </tr>   
            	<?
				$k++;
            }
			$grand_po_qty+=$row[csf("po_quantity")];
			$grand_po_value+=$row[csf("po_total_price")];
			$grand_exfactory_value+=$exFactoryValue;
			$grand_claim_value+=$row[csf("base_on_ex_val")];
            $grand_discount+=$row[csf("discount")]; 
            $grand_air_freight+=$row[csf("air_freight")];
            $grand_sea_freight+=$row[csf("sea_freight")];
			$i++;
        }
        ?>
        <tfoot align="left">
        <tr align="left" style="font-size:13px">
            <th width="30">&nbsp;</th> 
            <th width="70">&nbsp;</th>
            <th width="100">&nbsp;</th>   
            <th width="100">&nbsp;</th>
            <th width="100">&nbsp;</th>
            <th width="100">&nbsp;</th>
            <th width="100">&nbsp;</th>
            <th width="100">Total</th>
            <th width="80" align="right" id="td_po_qty"><? echo number_format($grand_po_qty,0); ?></th>
            
            <th width="80" align="right" id="td_po_value"><? echo number_format($grand_po_value,2); ?></th>
            <th width="70">&nbsp;</th>   
            <th width="70">&nbsp;</th>
            <th width="80" align="right" id="td_exfactory_value"><? echo number_format($grand_exfactory_value,2); ?></th>
            <th width="80" align="right" id="td_claim_value"><? echo number_format($grand_claim_value,2); ?></th>
            <th width="100"><?=number_format($grand_discount,2);?></th>
            <th width="100"><?=number_format($grand_air_freight,2);?></th>
            <th width="100"><?=number_format($grand_sea_freight,2);?></th>
            <th width="100">&nbsp;</th>
            <th width="130">&nbsp;</th>
            <th width="150">&nbsp;</th>
            <th width="80">&nbsp;</th>
            <th width="100">&nbsp;</th>
         </tr>
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
	echo "$total_data####$filename####$tot_rows";
	exit();
}
?>
