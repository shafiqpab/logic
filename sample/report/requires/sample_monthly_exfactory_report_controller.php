<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_name=$_SESSION['logic_erp']['user_id'];
/*$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
$company_arr=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0",'id','company_name');
$dealing_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0",'id','team_member_name');
$sample_name_arr=return_library_array( "select id,sample_name  from lib_sample where is_deleted=0 and status_active=1 order by sample_name", "id", "sample_name"  );
$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
$size_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0","id","size_name");
$season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0","id","season_name");*/


if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 130, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-Select Location-", $selected, "" );
	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );
	exit();
}

if($action=="report_generate")
{
    $process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$companyArr=return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0",'id','company_name');
	$buyerArr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
	
	if(str_replace("'","",$cbo_sample_type)==0) $sampleTypeCond=""; else $sampleTypeCond=" and sample_type=$cbo_sample_type";
	$sampleNameSql=sql_select("select id, sample_name, sample_type from lib_sample where is_deleted=0 and status_active=1 $sampleTypeCond");
	//echo "select id, sample_name, sample_type from lib_sample where is_deleted=0 and status_active=1 $sampleTypeCond";
	
	$sampleTypeIdArr=array();
	
	foreach($sampleNameSql as $srow)
	{
		$sampleNameArr[$srow[csf('id')]]=$srow[csf('sample_name')];
		$sampleTypeArr[$srow[csf('id')]]=$srow[csf('sample_type')];
		$sampleTypeIdArr[$srow[csf('id')]]=$srow[csf('id')];
		
		//if(str_replace("'","",$cbo_sample_type)>0) 
	}
	
	//print_r($sampleTypeIdArr);
	$dealingMctArr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0",'id','team_member_name');
	
 	$req_no=str_replace("'", "", $txt_req_no);
     
 	if(str_replace("'","",$cbo_company_name)==0) $companyCond=""; else $companyCond=" and a.company_id=$cbo_company_name";
	if(str_replace("'","",$cbo_location_name)==0) $locationCond=""; else $locationCond=" and a.location_id=$cbo_location_name";
	if(str_replace("'","",$cbo_buyer_name)==0) $buyerCond="";else $buyerCond=" and a.buyer_name=$cbo_buyer_name";
	if(str_replace("'","",$cbo_delivery_basis)==0) $deliveryBasisCond=""; else $deliveryBasisCond=" and c.delivery_basis=$cbo_delivery_basis";
	if(str_replace("'","",$cbo_shipping_status)==0) $shipStatusCond=""; else $shipStatusCond=" and nvl(b.is_complete_prod,1)=$cbo_shipping_status";
	if(str_replace("'","",$cbo_sample_type)==0) $sampleIDCond=""; else $sampleIDCond=" ".where_con_using_array($sampleTypeIdArr,1,'b.sample_name')."";
	if(str_replace("'","",$cbo_sample_stage)==0) $sampleStagesCond=""; else $sampleStagesCond=" and a.sample_stage_id=$cbo_sample_stage";
	//if(str_replace("'","",$cbo_sample_stage)==0) $sample_stages=""; else $sample_stages=" and a.sample_stage_id=$cbo_sample_stage";
	
	//if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $req_dateCond=""; else $req_dateCond=" and a.requisition_date between $txt_date_from and $txt_date_to";
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $exfac_dateCond=""; else $exfac_dateCond=" and c.ex_factory_date between $txt_date_from and $txt_date_to";

	if($req_no=="") $req_noCond=""; else $req_noCond=" and a.requisition_number_prefix_num='$req_no' ";

    $fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
    $toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );

    if($db_type==0) $yearCond="YEAR(a.insert_date)"; else if($db_type==2) $yearCond=" and to_char(a.insert_date,'YYYY')=$cbo_year_selection";

    	$query="SELECT a.id, a.company_id, to_char(a.insert_date,'YYYY') as year, a.requisition_number_prefix_num, a.buyer_name, a.style_ref_no, a.style_desc, a.requisition_date, a.dealing_marchant, a.sample_stage_id, a.quotation_id, a.uom,b.id as dtls_id, b.sample_name, b.gmts_item_id, b.smv, b.article_no, b.sample_prod_qty, b.size_data, b.sample_charge,c.sys_number, c.ex_factory_date, c.delivery_basis, c.final_destination, c.delivery_basis,  d.shiping_status, d.sample_name, sum(d.ex_factory_qty) as ex_factory_qty , sum(d.carton_qty) as carton_qty, d.remarks
	FROM sample_development_mst a,
         sample_development_dtls b,
         sample_ex_factory_mst c,
         sample_ex_factory_dtls d 
    WHERE a.id=b.sample_mst_id and 
          a.status_active=1 and 
          a.is_deleted=0 and 
          b.status_active=1 and 
          b.is_deleted=0 and 
          a.entry_form_id=203 and 
          b.entry_form_id=203 and
          a.id=d.sample_development_id and 
          c.id=d.sample_ex_factory_mst_id and 
          b.gmts_item_id=d.gmts_item_id   and
          c.status_active=1  and 
          c.is_deleted=0 and 
          d.status_active=1 and 
          d.is_deleted=0 and 
          d.entry_form_id=132 and 
          c.entry_form_id=132 
	$companyCond $locationCond $buyerCond $req_noCond $shipStatusCond $sampleIDCond $sampleStagesCond $yearCond $deliveryBasisCond $exfac_dateCond  
    group by a.id, a.company_id, a.insert_date, a.requisition_number_prefix_num, a.buyer_name,a.style_ref_no, a.style_desc, a.requisition_date, a.dealing_marchant, a.sample_stage_id, a.quotation_id, a.uom, b.id , b.sample_name, b.gmts_item_id, b.smv, b.article_no, b.sample_prod_qty, b.size_data, b.sample_charge, c.sys_number, c.ex_factory_date, c.delivery_basis, c.final_destination, c.delivery_basis, d.shiping_status, d.sample_name, d.remarks order by a.id, c.sys_number";
	
	

                
	$sqlMst=sql_select($query); $reqArr=array(); $reqIdArr=array(); $buyerSummArr=array(); $idwiseBuyer=array(); $exfactoryArr=array();
	foreach ($sqlMst as $value)
	{
        $string=$value[csf('id')].'*'.$value[csf('sample_name')].'*'.$value[csf('gmts_item_id')].'*'.$value[csf('sys_number')];
		$reqIdArr[$value[csf('id')]]=$value[csf('id')];
		$reqArr[$value[csf('sample_stage_id')]][$value[csf('id')]]=$value[csf('id')];

		
		$string3=$value[csf('id')].'*'.$value[csf('sample_name')].'*'.$value[csf('gmts_item_id')];
		$exfactoryArr[$string]['ex_factory_qty']+=$value[csf('ex_factory_qty')];
		$exfactoryArr[$string]['sysno']=$value[csf('sys_number')];
		$exfactoryArr[$string]['exdate']=$value[csf('ex_factory_date')];
		$exfactoryArr[$string]['delvbasis']=$value[csf('delivery_basis')];
		
		if($sampleTypeArr[$value[csf('sample_name')]]==14)
		{
			$exfactoryArr[$string]['ex_sms_qty']+=$value[csf('ex_factory_qty')];
            $main_data_arr[$string]['ex_sms_qty']+=$value[csf('ex_factory_qty')];
			$buyerSummArr[$value[csf('buyer_name')]]['smsqty']+=$value[csf('ex_factory_qty')];
			$buyerSummArr[$value[csf('buyer_name')]]['smsamt']+=$value[csf('current_invoice_value')];
		}

        $main_data_arr[$string]['ex_factory_qty']+=$value[csf('ex_factory_qty')];
        $main_data_arr[$string]['company_id']=$value[csf('company_id')];
        $main_data_arr[$string]['year']=$value[csf('year')];
        $main_data_arr[$string]['requisition_number_prefix_num']=$value[csf('requisition_number_prefix_num')];
        $main_data_arr[$string]['buyer_name']=$value[csf('buyer_name')];
        $main_data_arr[$string]['style_ref_no']=$value[csf('style_ref_no')];
        $main_data_arr[$string]['style_desc']=$value[csf('style_desc')];
        $main_data_arr[$string]['article_no']=$value[csf('article_no')];
        $main_data_arr[$string]['dealing_marchant']=$value[csf('dealing_marchant')];
        $main_data_arr[$string]['id']=$value[csf('id')];
        $main_data_arr[$string]['smv']=$value[csf('smv')];
        $main_data_arr[$string]['carton_qty']+=$value[csf('carton_qty')];     
        $main_data_arr[$string]['sample_charge']=$value[csf('sample_charge')];
        $main_data_arr[$string]['exdate']=$value[csf('ex_factory_date')];
        $main_data_arr[$string]['delvbasis']=$value[csf('delivery_basis')];
        $main_data_arr[$string]['shiping_status']=$value[csf('shiping_status')];

        if(isset($value[csf('remarks')])){
            $main_data_arr[$string]['remarks'] =$value[csf('remarks')];
        }

        if(isset($value[csf('final_destination')])){
            $main_data_arr[$string]['final_destination']=$value[csf('final_destination')];
        }

	} 
    
    $query2="SELECT a.id, a.company_id, to_char(a.insert_date,'YYYY') as year, a.requisition_number_prefix_num, a.buyer_name, 
    a.style_ref_no, a.style_desc, a.requisition_date, a.dealing_marchant, a.sample_stage_id, a.quotation_id, a.uom, b.id as dtls_id,
     b.sample_name, b.gmts_item_id, b.smv, b.article_no,b.sample_prod_qty, b.size_data, b.sample_charge 
     FROM sample_development_mst a,
     sample_development_dtls b 
     WHERE a.id=b.sample_mst_id and 
     a.status_active=1 and 
     a.is_deleted=0 and 
     b.status_active=1 and 
     b.is_deleted=0 and 
     a.entry_form_id=203 and 
     b.entry_form_id=203 $companyCond $locationCond $buyerCond $req_noCond $shipStatusCond $sampleIDCond $sampleStagesCond $yearCond ";
      $sqlMst2=sql_select($query2);$string="";
      foreach ($sqlMst2 as $values)
      {
          $string=$values[csf('id')].'*'.$values[csf('sample_name')].'*'.$values[csf('gmts_item_id')];
          $sizeDataArr=explode("__",$values[csf('size_data')]);
          $bhQty=$exAmt=0;
          foreach($sizeDataArr as $sizeQtyStr)
          {
              $sizeStrQty=explode("_",$sizeQtyStr);
              $bhQty+=$sizeStrQty[1];
          }
            $dtls_data_arr[$string]['sample_prod_qty']+=$values[csf('sample_prod_qty')];
            $dtls_data_arr[$string]['bhQty']+=$bhQty;
      }

	$booking_arr=array();	
	
    $booking_without_order_sql=sql_select("SELECT b.style_id, a.booking_no, a.booking_date from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no $booking_no_cond $year_cond and a.status_active=1 and b.status_active=1  group by  b.style_id, a.booking_no, a.booking_date");
	
    foreach($booking_without_order_sql as $vals)
    {
        $booking_arr[$vals[csf("style_id")]]['bookingno']=$vals[csf("booking_no")];
		$booking_arr[$vals[csf("style_id")]]['bookingdate']=$vals[csf("booking_date")];
	}
	//print_r($booking_arr[5346]);
	unset($booking_sql);

	
	
	$sqlSC="Select a.contract_no, b.wo_po_break_down_id from com_sales_contract a, com_sales_contract_order_info b where a.id=b.com_sales_contract_id and b.is_sales=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($reqIdArr,1,'b.wo_po_break_down_id')."";
	$sqlSCArr=sql_select($sqlSC);
	$scArr=array();
	foreach($sqlSCArr as $row)
	{
		$scArr[$row[csf('wo_po_break_down_id')]]['scno']=$row[csf('contract_no')];
	}
	unset($sqlSCArr);
	
	
	$sqlInv="Select a.invoice_no, b.po_breakdown_id, b.current_invoice_qnty, b.current_invoice_value from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($reqIdArr,1,'b.po_breakdown_id')."";
	$sqlInvArr=sql_select($sqlInv);
	$invArr=array();
	foreach($sqlInvArr as $row)
	{
		$invArr[$row[csf('po_breakdown_id')]]['invno']=$row[csf('invoice_no')];
		$invArr[$row[csf('po_breakdown_id')]]['invqty']+=$row[csf('current_invoice_qnty')];
		$invArr[$row[csf('po_breakdown_id')]]['invamt']+=$row[csf('current_invoice_value')];		
		$reqbuyerid=$idwiseBuyer[$row[csf('po_breakdown_id')]];		
		$buyerSummArr[$reqbuyerid]['invqty']+=$row[csf('current_invoice_qnty')];
		$buyerSummArr[$reqbuyerid]['invamt']+=$row[csf('current_invoice_value')];
	}
	unset($sqlInvArr);
	//=======================================buyer wise data============================
    $buyerSummArr=array();
            foreach($main_data_arr as $key=>$row)
                {
                    list($req_id,$sample_name,$item_name,$challan_no)=explode("*",$key);                   
                    $exStr=$row['id'].'*'.$sample_name.'*'.$item_name;
                    $exAmt=$row['ex_sms_qty']*$row['sample_charge'];
                    $buyerSummArr[$row['buyer_name']]['reqqty']+=$dtls_data_arr[$exStr]['sample_prod_qty'];;
                    $buyerSummArr[$row['buyer_name']]['bhqty']+=$dtls_data_arr[$exStr]['bhQty'];;
                    $buyerSummArr[$row['buyer_name']]['smsqty']+=$row['ex_sms_qty'];
                    $buyerSummArr[$row['buyer_name']]['smsamt']+= $exAmt;
                    $buyerSummArr[$row['buyer_name']]['exqty']+= $row['ex_factory_qty'];
                }
     //=======================================buyer wise data============================
	?>
    <div align="left">
        <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" width="730" rules="all">
            <thead>
                <th width="30">SL</th>
                <th width="120">Buyer Name</th>
                <th width="80">S.Req. Qty</th>
                <th width="80">BH Req. Qty.</th>
              
                <th width="80">Ex-Fact. Qty [SMS]</th>
                <th width="80">Ex-Fact. Value [SMS]</th>
                <th>Total Ex-Fact. Qty</th>
            </thead>
        </table>
        <div style="max-height:130px; overflow-y:scroll; width:730px;" id="scroll_body1">
            <table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="710" rules="all" id="table_body1">
            <?
            $k=1;
            

            foreach($buyerSummArr as $buyerlibid=>$bdata)
            {
                if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$k; ?>','<?=$bgcolor; ?>');" id="tr_<?=$k; ?>">
                    <td width="30" align="center"><?=$k; ?></td>       
                    <td width="120" style="word-break:break-all"><?=$buyerArr[$buyerlibid]; ?></td>    
                    <td width="80" align="right"><?=$bdata['reqqty']; ?></td>       
                    <td width="80" align="right"><?=$bdata['bhqty']; ?></td>                   
             
                    <td width="80" align="right"><?=$bdata['smsqty']; ?></td>
                    <td width="80" align="right"><?=number_format($bdata['smsamt'],2); ?></td>
                    <td align="right"><?=$bdata['exqty']; ?></td>
                </tr>
                <?
                $k++;
                $sumSmpQty+=$bdata['reqqty'];
                $sumBhQty+=$bdata['bhqty'];
                $sumInvQty+=$bdata['invqty'];
                $sumInvAmt+=$bdata['invamt'];
                $sumExQty+=$bdata['smsqty'];
                $sumExVal+=$bdata['smsamt'];
                $sumTotExQty+=$bdata['exqty'];
            }
            ?>
            </table>
        </div>
        <table cellspacing="0" cellpadding="0" border="1" class="tbl_bottom" width="730" rules="all">
            <tr>
                <td width="30">&nbsp;</td>       
                <td width="120">Total:</td>    
                
                <td width="80" align="right"><?=$sumSmpQty; ?></td>
                <td width="80" align="right"><?=$sumBhQty; ?></td>
               
                <td width="80" align="right"><?=$sumExQty; ?></td>
                <td width="80" align="right"><?=number_format($sumExVal,2); ?></td>
                <td align="right"><?=$sumTotExQty; ?></td>
                                 
            </tr>
        </table>
    </div>
    <br/>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" width="2530" rules="all" id="table_header" >
            <thead>
                <tr>
                    <th width="30">SL.</th>       
                    <th width="100">Company</th>    
                    <th width="60">Year</th>       
                    <th width="60">Requisition No</th>                   
                    <th width="100">Buyer Name</th>
                    <th width="100">Booking No</th>
                    <th width="70">Booking Date</th>
                    <th width="100">Sample Name</th>
                    <th width="100">Challan NO</th>
                    <th width="100">Invoice NO</th>
                    
                    <th width="100">LC/SC NO</th>
                    <th width="100">Style Ref.</th>
                    <th width="110">Style Description</th>
                    <th width="100">Item Name</th>
                    <th width="60">Item SMV</th>
                    <th width="100">Article No</th>
                    <th width="70">Ex-Fact. Date </th>
                    <th width="80">Delivery Basis</th>
                    <th width="60">UOM</th>
                    <th width="80">Sample Requisition Qty</th>
                    <th width="80">BH Required Qty</th>
           
                    <th width="80">Ex-Fact. Qty [SMS]</th>
                    <th width="80">Ex-Fact. Value [SMS]</th>
                    <th width="70">Total Ex-Fact. Qty.</th>
                    <th width="60">Total Carton Qty</th>
                    <th width="90">Final Destination</th>
                    <th width="90">Dealing Merchant </th>
                    
                    <th width="80">Ex-Fact Status</th>
                    <th>Ex-Fact. Comments</th>                 
                </tr>
            </thead>
        </table>
        <div style="max-height:300px; overflow-y:scroll; width:2530px;" id="scroll_body">
            <table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="2512" rules="all" id="table_body">
                <?
                $i=1; $exAmt=0;
                foreach($main_data_arr as $key=>$row)
                {
                    list($req_id,$sample_name,$item_name,$challan_no)=explode("*",$key);
                   
                    
                    $exStr=$row['id'].'*'.$sample_name.'*'.$item_name;
                    $exAmt=$row['ex_sms_qty']*$row['sample_charge'];
                    
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_2nd<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_2nd<?=$i; ?>">
                        <td width="30" align="center"><?=$i; ?></td>       
                        <td width="100" style="word-break:break-all"><?=$companyArr[$row['company_id']]; ?></td>    
                        <td width="60" align="center"><?=$row['year']; ?></td>       
                        <td width="60" align="center"><?=$row['requisition_number_prefix_num']; ?></td>                   
                        <td width="100" style="word-break:break-all"><?=$buyerArr[$row['buyer_name']]; ?></td>
                        <td width="100" style="word-break:break-all"><?=$booking_arr[$row["id"]]['bookingno']; ?></td>   
                        <td width="70"><?=change_date_format($booking_arr[$row["id"]]['bookingdate']); ?></td>  
                        <td width="100" style="word-break:break-all" title="<?=$sample_name;?>"><?=$sampleNameArr[$sample_name]; ?></td>
                        <td width="100" style="word-break:break-all"><?=$challan_no; ?></td>
                        <td width="100" style="word-break:break-all"><?=$invArr[$row['id']]['invno']; ?></td>                        
                        <td width="100" style="word-break:break-all"><?=$scArr[$row['id']]['scno']; ?></td>
                        <td width="100" style="word-break:break-all"><?=$row['style_ref_no']; ?></td>
                        <td width="110" style="word-break:break-all"><?=$row['style_desc']; ?></td>
                        <td width="100" style="word-break:break-all"  title="<?=$item_name;?>"><?=$garments_item[$item_name]; ?></td>
                        <td width="60" align="right"><?=$row['smv']; ?></td>
                        <td width="100" style="word-break:break-all"><?=$row['article_no']; ?></td>
                        <td width="70"><?=change_date_format($row['exdate']); ?></td>  
                        <td width="80"><?=$sample_delivery_basis[$row['delvbasis']]; ?></td>
                        <td width="60" align="center"><?="PCS";//$unit_of_measurement[$row[csf('uom')]]; ?></td>
                        <td width="80" align="right"><?=$dtls_data_arr[$exStr]['sample_prod_qty']; ?></td>
                        <td width="80" align="right"><?=$dtls_data_arr[$exStr]['bhQty']; ?></td>                
                        <td width="80" align="right"><?=$row['ex_sms_qty']; ?></td>
                        <td width="80" align="right"><?=number_format($exAmt,2); ?></td>
                        <td width="70" align="right"><?=$row['ex_factory_qty']; ?></td>
                        <td width="60" align="right"><?=$row['carton_qty']; ?></td>
                        <td width="90" style="word-break:break-all"><?=$row['final_destination']; ?></td>
                        <td width="90" style="word-break:break-all"><?=$dealingMctArr[$row['dealing_marchant']]; ?></td>                        
                        <td width="80" style="word-break:break-all"><?=$shipment_status[$row['shiping_status']]; ?></td>
                        <td style="word-break:break-all"><?=$row['remarks'];; ?></td>           
                    </tr>
                    <?
                    $i++;
                    $gSmpQty+=$row['sample_prod_qty'];
                    $gBhQty+=$row['bhQty'];
                  
                    $gExQty+=$row['ex_sms_qty'];;
                    $gExVal+=$exAmt;
                    $gTotExQty+=$row['ex_factory_qty'];;
                    $gCartonQty+=$row['carton_qty'];
                }
                ?>
            </table>
        </div>
        <table cellspacing="0" cellpadding="0" border="1" class="tbl_bottom" width="2530" rules="all">
            <tr bgcolor="#CCCCCC">
                <td width="30">&nbsp;</td>       
                <td width="100">&nbsp;</td>    
                <td width="60">&nbsp;</td>       
                <td width="60">&nbsp;</td>                   
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="110">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="60">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="60">Total:</td>
                <td width="80" align="right" id="val_sqty"><?=$gSmpQty; ?></td>
                <td width="80" align="right" id="val_bhqty"><?=$gBhQty; ?></td>              
                <td width="80" align="right" id="val_exqty"><?=$gExQty; ?></td>
                <td width="80" align="right" id="val_exval"><?=number_format($gExVal,2); ?></td>
                <td width="70" align="right" id="val_texqty"><?=$gTotExQty; ?></td>
                <td width="60" align="right" id="val_carqty"><?=$gCartonQty; ?></td>
                <td width="90">&nbsp;</td>
                <td width="90">&nbsp;</td>
                
                <td width="80">&nbsp;</td>
                <td>&nbsp;</td>                 
            </tr>
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
    echo "$html**$filename**$type"; 
    exit();
	exit();
}