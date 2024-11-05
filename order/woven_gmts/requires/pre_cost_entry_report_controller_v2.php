<?
/*-------------------------------------------- Comments -----------------------
Purpose			         : 	This page Will Create Wovrn Garments Pre Cost Report.
Functionality	         :
JS Functions	         :
Created by		         :	Zakaria
Creation date 	         : 	19-07-2023
-------------------------------------------------------------------------------*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.fabrics.php');
include('../../../includes/class4/class.yarns.php');
include('../../../includes/class4/class.conversions.php');
include('../../../includes/class4/class.trims.php');
include('../../../includes/class4/class.emblishments.php');
include('../../../includes/class4/class.washes.php');
include('../../../includes/class4/class.commercials.php');
include('../../../includes/class4/class.commisions.php');
include('../../../includes/class4/class.others.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$type=$_REQUEST['type'];
$permission=$_SESSION['page_permission'];

if($action == "bom_pcs_woven4") //BOM PCS4 Btn 23
{ 
   	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if($txt_job_no=="") $job_no=''; else $job_no=" and a.job_no=".$txt_job_no."";
	if($cbo_company_name=="" || $cbo_company_name==0) $company_name=''; else $company_name=" and a.company_name=".$cbo_company_name."";
	if($cbo_buyer_name=="" || $cbo_buyer_name==0) $cbo_buyer_name=''; else  $cbo_buyer_name=" and a.buyer_name=".$cbo_buyer_name."";
	if($txt_style_ref=="") $txt_style_ref=''; else $txt_style_ref=" and a.style_ref_no=".$txt_style_ref."";

	$txt_costing_date=change_date_format(str_replace("'","",$txt_costing_date),'yyyy-mm-dd','-');
	if($txt_costing_date=="") $txt_costing_date=''; else $txt_costing_date=" and b.costing_date='".$txt_costing_date."'";
	$txt_po_breack_down_id=str_replace("'",'',$txt_po_breack_down_id);
	if(str_replace("'",'',$txt_po_breack_down_id)=="") $txt_po_breack_down_id_cond=''; else $txt_po_breack_down_id_cond=" and d.id in(".$txt_po_breack_down_id.")";
	$zero_value=str_replace("'",'',$zero_value);
	//echo $zero_value.'dssssss';
    //if($txt_quotation_date=="") $txt_quotation_date=''; else $txt_quotation_date=" and a.quot_date ='".$txt_quotation_date."'";
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');

    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $season_name_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');
    $comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$user_passArr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand where status_active=1 and is_deleted=0",'id','brand_name');
	 //pro_ex_factory_mst 
	$sql_ex="select max(ex_factory_date) as ex_factory_date from pro_ex_factory_mst b,wo_po_break_down d where d.id=b.po_break_down_id and d.job_no_mst=$txt_job_no and d.status_active=1";
	$exf_data_array=sql_select($sql_ex);
	foreach( $exf_data_array as $row)
	{
		$ex_factory_date=$row[csf("ex_factory_date")];
	}
	$excess_cut=0;
	$sql_excess="select avg(b.excess_cut_perc) as excess_cut_perc,max(d.pub_shipment_date) as last_pub_shipment_date,min(d.pub_shipment_date) as first_pub_shipment_date from wo_po_color_size_breakdown b,wo_po_break_down d where d.id=b.po_break_down_id and d.job_no_mst=$txt_job_no and b.status_active=1 and d.status_active=1";
	$excess_data_array=sql_select($sql_excess);
	foreach( $excess_data_array as $row)
	{
		$excess_cut=$row[csf("excess_cut_perc")];
		$last_pub_shipment_date=$row[csf("last_pub_shipment_date")];
		$first_pub_shipment_date=$row[csf("first_pub_shipment_date")];
	}
	 
	$sql_po="select a.total_set_qnty as ratio, d.po_number, (d.po_quantity) as po_qnty, d.plan_cut, d.excess_cut, d.unit_price, d.pub_shipment_date, d.po_received_date, d.pack_handover_date from wo_po_break_down d, wo_po_details_master a where d.job_no_mst=a.job_no and d.job_no_mst=$txt_job_no and d.status_active=1 $txt_po_breack_down_id_cond";
	$po_data_array=sql_select($sql_po);
	$order_job_qnty=0; $plan_cut=0; $leadtime_days_remian_cal=""; $ordQtyUom=$planqtyUom=0;
	foreach( $po_data_array as $row)
	{
		$po_received_dateArr.=$row[csf("po_received_date")].',';
		$pack_handover_dateArr.=$row[csf("pack_handover_date")].',';
		$order_job_qnty+=$row[csf("po_qnty")]*$row[csf("ratio")];
		
		$plan_cut+=$row[csf("plan_cut")]*$row[csf("ratio")];
		
		$ordQtyUom+=$row[csf("po_qnty")];
		$planqtyUom+=$row[csf("plan_cut")];
		$days_tot=datediff('d',$row[csf("po_received_date")],$row[csf("pub_shipment_date")])-1;
		$leadtime_days_remian_cal.=$days_tot.',';
		$pub_shipment_dateArr[$row[csf("pub_shipment_date")]]=$row[csf("pub_shipment_date")];
	}
	/*$pub_shipment_dateAll=implode(',',$pub_shipment_dateArr);
	$pub_shipment_dateAllArr=explode(",",$pub_shipment_dateAll);
	$last_pub_shipment_date=max($pub_shipment_dateAllArr);
	$first_pub_shipment_date=min($pub_shipment_dateAllArr);*/
	
	$leadtime_days_remian_calArr=rtrim($leadtime_days_remian_cal,',');
	$leadtime_days_remian_calArr=explode(",",$leadtime_days_remian_calArr);
	$leadtime_days_remian=max($leadtime_days_remian_calArr);
	  
	$po_received_dateArr=rtrim($po_received_dateArr,',');
	$po_received_dateArr=explode(",",$po_received_dateArr);
	$po_received_date=max($po_received_dateArr);
	$pack_handover_dateArr=rtrim($pack_handover_dateArr,',');
	$pack_handover_dateArr=explode(",",$pack_handover_dateArr);
	$pack_handover_date=max($pack_handover_dateArr);
	 
	// $leadtime_days_remian=datediff('d',$po_received_date,$ex_factory_date)-1;
	 
	 $sql = "select a.job_no, a.company_name, b.approved, a.buyer_name, a.quotation_id, a.season_buyer_wise, a.season_year, a.brand_id, a.style_ref_no, a.set_smv, a.gmts_item_id, a.order_uom, a.job_quantity, a.avg_unit_price,b.costing_date,b.sourcing_date, b.costing_per, c.fab_knit_req_kg, c.fab_woven_req_yds, c.fab_yarn_req_kg, a.total_set_qnty as ratio,a.working_company_id from wo_po_details_master a, wo_pre_cost_mst b,wo_pre_cost_sum_dtls c  where a.id=b.job_id and b.job_id=c.job_id and a.status_active=1 and a.job_no=$txt_job_no $company_name $cbo_buyer_name $txt_style_ref";
    //echo $sql; die;
    $data_array=sql_select($sql);
	$working_company_arr=return_library_array("SELECT id,company_name from lib_company ","id","company_name");  
	$working_company='';
	foreach ($data_array as $row)
	{
		$working_company=$working_company_arr[$row[csf("working_company_id")]];
		$approved_id=$row[csf("approved")];
	}
			//echo $approved_id.'d';
    if(empty($path)) $path="../../"; else $path=str_replace("'", "", $path);
    //echo $path;

	ob_start();
    ?>
    <div style="width:1220px; border:1px solid black;margin:5px;" align="left">  
      <style>
	 /* p{ padding:0px !important; margin:0px !important;}*/
	 table td{ font-size:15px;}
	</style>
      <table width="1200" cellpadding="0" cellspacing="0" style="border:1px solid black;margin:5px; font-size:14px;">
           <tr>
               <td width="100"> 
               <img src='<? echo $path .''. $imge_arr[str_replace("'","",$cbo_company_name)]; ?>' height='100%' width='100%' />
               </td>
               <td width="1200">                                     
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                        	<td align="center" style="font-size:20px;"><b><?='Pre Cost Sheet'; ?></b></td>
                        </tr>
                    </table>
                    <br>
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr align="center">
                            <td> <b style="font-size:21px;"> PO Rec Unit: </b><span style="font-size:19px;"><?=$comp[str_replace("'","",$cbo_company_name)];?> </span></td>
                            <td ><b style="font-size:21px;">Prod Unit: </b><span style="font-size:19px;"><?=$working_company;?></span></td>
                        </tr>
                    </table>
                </td>       
            </tr>
       </table>
       <br>
       <table align="left" border="0" cellpadding="0" cellspacing="0" style="width:550px; margin:5px;">
            <tr>
            <?
            $order_price_per_dzn=0; $orderUom=0;
		
			foreach ($data_array as $row)
			{
				$avg_unit_price=$row[csf("avg_unit_price")];
				$sourcing_date=$row[csf("sourcing_date")];
				$buyer_name_id=$row[csf("buyer_name")];
				$costing_date=$row[csf("costing_date")];
				$order_values = $order_job_qnty*$avg_unit_price;
				if($row[csf("costing_per")]==1){$order_price_per_dzn=12;$costing_val=" DZN";}
				else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;$costing_val=" PCS";}
				else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;$costing_val=" 2 DZN";}
				else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;$costing_val=" 3 DZN";}
				else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;$costing_val=" 4 DZN";}
			
				$quot_id=$row[csf("quotation_id")];
				$sew_smv=$row[csf("set_smv")];
				$inserted_by=$user_passArr[$row[csf("inserted_by")]];
				?>
                <td valign="top">
                <table class="rpt_table" align="left" border="1" cellpadding="1" cellspacing="1" style="width:550px; margin:5px; font-size:14px;" rules="all">
                    <tr>
                        <td width="80">Job No</td>
                        <td width="80"><b><? echo $row[csf("job_no")]; ?></b></td>
                        <td width="90">Costing </td>
                    </tr>
                    <tr>
                        <td width="80">Quotation ID</td>
                        <td width="80"><b><? echo $quot_id; ?></b></td>
                        <td width="100"><b><? echo $costing_per[$row[csf("costing_per")]]; ?></b></td>
                    </tr>
                    <tr>
                        <td> Costing Date :  </td>
                        <td colspan="2"><b><? echo change_date_format($row[csf("costing_date")],4); ?></b></td>
                    </tr>
                    <tr>
                        <td>Buyer </td>
                        <td colspan="2"><b><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></b></td>
                     </tr> 
                     <tr>
                        <td>Brand </td>
                        <td colspan="2"><b><? //echo $buyer_arr[$row[csf("buyer_name")]];
                        if($row[csf("brand_id")]>0)
                        {
                            echo $brand_arr[$row[csf("brand_id")]];
                        }
                        ?></b></td>
                     </tr> 
                     <tr>
                        <td>Style </td>
                        <td colspan="2"><b><? echo $row[csf("style_ref_no")]; ?></b></td>
                     </tr>
                    <tr>
                        <td width="80">Item</td>
                        <?
                            if($row[csf("order_uom")]==1)
                            {
                              $grmnt_items=$garments_item[$row[csf("gmts_item_id")]];
                            }
                            else
                            {
                                $gmt_item=explode(',',$row[csf("gmts_item_id")]);
                                foreach($gmt_item as $key=>$val)
                                {
                                    $grmnt_items .=$garments_item[$val].", ";
                                }
                            }
                        ?>
                        <td width="100" colspan="2"><b><? echo $grmnt_items; ?></b></td>
                    </tr>
                    <tr>
                        <td>Season</td>
                        <td colspan="2"><b><? echo $season_name_arr[$row[csf('season_buyer_wise')]].'-'.substr( $row[csf('season_year')], -2);  ?></b></td>
                     </tr>
                    <tr>
                        <td>P.O. Qty</td>
                        <td><b><? echo $ordQtyUom.'-'.$unit_of_measurement[$row[csf("order_uom")]]; $po_qty_dzn=$order_job_qnty/$order_price_per_dzn; ?></b></td>
                        <td title="<? echo $offer_qty_dzn;?>"><b><? // echo number_format($po_qty_dzn,0).' '.$costing_val; ?></b></td>
                    </tr>
                    <tr>
                        <td>Plan Cut Qty[<?=number_format($excess_cut,2).'%'; ?>]</td>
                        <td><b><? echo $planqtyUom.'-'.$unit_of_measurement[$row[csf("order_uom")]]; $orderUom=$row[csf("order_uom")]; $offer_qty_dzn=$plan_cut/$order_price_per_dzn; ?></b></td>
                        <td title="<? echo $offer_qty_dzn;?>"><b><? // echo number_format($offer_qty_dzn,0).' '.$costing_val; ?></b></td>
                    </tr>
                </table>
                </td>
              	<?
			} //master part end
			//die;
			$condition= new condition();
			if(str_replace("'","",$txt_job_no) !=''){
				$condition->job_no("=$txt_job_no");
			}
			if(str_replace("'",'',$txt_po_breack_down_id) !="")
			{
				$condition->po_id("in($txt_po_breack_down_id)");
			}
			$condition->init();
			$fabric= new fabric($condition);
			$trim= new trims($condition);
			$wash= new wash($condition);
			$emblishment= new emblishment($condition);
			$others_cost= new other($condition);
			//echo $fabric->getQuery();die;
			$fabric_qty_arr=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
			//print_r($fabric_qty_arr);
			$fabric_amount_arr=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
			
			$trim_amount_arr=$trim->getAmountArray_precostdtlsid();
			$trim_qty_arr=$trim->getQtyArray_by_precostdtlsid();
			
			$emblishment_qtyArr=$emblishment->getQtyArray_by_jobAndEmblishmentid();
			$emblishment_amountArr=$emblishment->getAmountArray_by_jobAndEmblishmentid();
			$wash_qtyArr=$wash->getQtyArray_by_jobAndEmblishmentid();
			//	print_r($wash_qtyArr);
			$wash_amountArr=$wash->getAmountArray_by_jobAndEmblishmentid();
			$other_amountArr=$others_cost->getAmountArray_by_job();
			
			$sql_determin=sql_select("select a.id,a.type from lib_yarn_count_determina_mst a where a.status_active=1 and a.entry_form=426");
			foreach($sql_determin as $row)
			{
				$determin_type_arr[$row[csf('id')]]=$row[csf('type')];	
			}
			
			if($zero_value==1) $cons_qty_chk=""; else  $cons_qty_chk="b.avg_cons>0";
			
			$pre_fab_arr="select  b.id, b.job_no,b.body_part_id,b.lib_yarn_count_deter_id as deter_min_id, b.fab_nature_id, b.color_type_id, b.fabric_description as fab_desc,b.uom,b.avg_cons,b.avg_cons_yarn, b.avg_process_loss,b.construction,b.composition,b.fabric_source,b.gsm_weight, b.rate,b.amount,b.avg_finish_cons,b.sourcing_rate,b.sourcing_nominated_supp from wo_pre_cost_fabric_cost_dtls  b where  b.status_active=1 and b.is_deleted=0 and b.job_no=$txt_job_no and b.avg_cons>0 order by b.id ";//and b.fabric_source=2
			 //echo $pre_fab_arr; die;
			
			$pre_fab_result=sql_select($pre_fab_arr);
			
			$summ_fob_pcs=0;$summ_fob_gross_value_amt=$summ_sourcing_tot_budget_dzn_val=0;
			foreach($pre_fab_result as $row)
			{
				$pre_c_id=$row[csf('id')];
				$remarks_arr=sql_select("select remarks from wo_pre_cos_fab_co_avg_con_dtls where job_no=$txt_job_no and pre_cost_fabric_cost_dtls_id=$pre_c_id group by remarks");
				$determin_type=$determin_type_arr[$row[csf('deter_min_id')]];
				$body_partId=$body_part[$row[csf('body_part_id')]];
				//$fab_desc=$body_partId.','.$row[csf('fab_desc')];
				$fab_desc=$row[csf('fab_desc')];
				//echo $determin_type.'d';
				$tot_amt=$row[csf('avg_cons')]*$row[csf('rate')];
				$fab_req_qty=$fabric_qty_arr['knit']['grey'][$row[csf("id")]][$row[csf("uom")]]+$fabric_qty_arr['woven']['grey'][$row[csf("id")]][$row[csf("uom")]];
				$fab_req_amount=$fabric_amount_arr['knit']['grey'][$row[csf("id")]][$row[csf("uom")]]+$fabric_amount_arr['woven']['grey'][$row[csf("id")]][$row[csf("uom")]];
				$fab_cost_dtls_id=$row[csf('id')];
				$p_fab_precost_arr[$fab_cost_dtls_id][$determin_type][$fab_desc]['req_qty']+=$fab_req_qty;
				$p_fab_precost_arr[$fab_cost_dtls_id][$determin_type][$fab_desc]['req_amount']+=$fab_req_amount;
				$p_fab_precost_arr[$fab_cost_dtls_id][$determin_type][$fab_desc]['cons']+=$row[csf('avg_finish_cons')];
				$p_fab_precost_arr[$fab_cost_dtls_id][$determin_type][$fab_desc]['tot_cons']+=$row[csf('avg_cons')];
				$p_fab_precost_arr[$fab_cost_dtls_id][$determin_type][$fab_desc]['amount']+=$row[csf('amount')];
				$p_fab_precost_arr[$fab_cost_dtls_id][$determin_type][$fab_desc]['p_loss']=$row[csf('avg_process_loss')];
				$p_fab_precost_arr[$fab_cost_dtls_id][$determin_type][$fab_desc]['sourcing_nominated_supp']=$row[csf('sourcing_nominated_supp')];
				$p_fab_precost_arr[$fab_cost_dtls_id][$determin_type][$fab_desc]['sourcing_rate']=$row[csf('sourcing_rate')];
				$p_fab_precost_arr[$fab_cost_dtls_id][$determin_type][$fab_desc]['fab_desc']=$row[csf('construction')].','.$row[csf('composition')];
				$p_fab_precost_arr[$fab_cost_dtls_id][$determin_type][$fab_desc]['uom']=$unit_of_measurement[$row[csf('uom')]];
				$p_fab_precost_arr[$fab_cost_dtls_id][$determin_type][$fab_desc]['remarks']=$remarks_arr[0][REMARKS];
				$p_fab_precost_tot_row+=1;	
				//Summary
				$summ_fob_pcs+=$row[csf('amount')];
			//	$summ_sourcing_tot_budget_dzn_val+=$fab_req_qty*$row[csf('sourcing_rate')];
				
				$summ_fob_gross_value_amt+=$fab_req_amount;
			}
			$pre_trim_consarr="select b.id,c.trim_type,c.item_name,b.description,b.trim_group,b.cons_dzn_gmts,b.seq,b.cons_uom as uom,avg(d.excess_per) as  excess_per from wo_pre_cost_trim_cost_dtls b,lib_item_group c,wo_pre_cost_trim_co_cons_dtls d where  c.id=b.trim_group and b.id=d.wo_pre_cost_trim_cost_dtls_id and c.trim_type in(1,2) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and b.job_no=$txt_job_no and b.cons_dzn_gmts>0  group by b.id,c.trim_type,c.item_name,b.description,b.trim_group,b.cons_dzn_gmts,b.cons_uom,b.seq order by b.seq";
			$pre_trim_cons_result=sql_select($pre_trim_consarr);
			foreach($pre_trim_cons_result as $row)
			{
				$trims_type=$row[csf('trim_type')];
				$description=$row[csf('description')];
				if($description!="") $descriptionCond=", ".$description; else $descriptionCond="";
				$item_id=$row[csf('item_name')].$descriptionCond;
				if($trims_type==1) //Sewing
				{
					$p_sew_trim_precost_excess_arr[$item_id]['p_loss']+=$row[csf('excess_per')];
				}
				else
				{
					$p_fin_trim_precost_excess_arr[$item_id]['p_loss']+=$row[csf('excess_per')];;
				}
			}
			//  $pre_trim_arr="select b.seq,b.id,c.trim_type,c.item_name,b.description,b.trim_group,b.tot_cons,b.ex_per ,b.cons_dzn_gmts,b.cons_uom as uom, b.rate,b.amount,b.sourcing_rate,b.sourcing_nominated_supp from wo_pre_cost_trim_cost_dtls b,lib_item_group c where  c.id=b.trim_group and c.trim_type in(1,2) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.job_no=$txt_job_no order by b.seq";//and b.fabric_source=2

			$pre_trim_arr="select b.seq,b.id,c.trim_type,c.item_name,b.description,b.trim_group,b.tot_cons,b.ex_per ,b.cons_dzn_gmts,b.cons_uom as uom, b.rate,b.amount,b.sourcing_rate,b.sourcing_nominated_supp,b.remark from wo_pre_cost_trim_cost_dtls b,lib_item_group c where  c.id=b.trim_group  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.cons_dzn_gmts>0  and b.job_no=$txt_job_no order by b.seq";//and b.fabric_source=2
			$pre_trim_result=sql_select($pre_trim_arr);
			$p_sew_trim_precost_arr=$p_fin_trim_precost_arr=array();
		
			//print_r($p_sew_trim_precost_arr2);
			$pre_wash_arr="select b.job_no,b.id,b.emb_name,b.emb_type,b.cons_dzn_gmts, b.rate,b.amount,b.sourcing_rate,b.sourcing_nominated_supp from wo_pre_cost_embe_cost_dtls  b where  b.status_active=1 and b.is_deleted=0  and b.job_no=$txt_job_no   and b.cons_dzn_gmts>0 order by b.emb_name";//and b.fabric_source=2
			$pre_wash_result=sql_select($pre_wash_arr);
			
			//$summ_sourcing_tot_budget_dzn_val=0;
		 
			foreach($pre_wash_result as $row)
			{
				$emb_name_id=$row[csf('emb_name')];
				$emb_type=$row[csf('emb_type')];
			
				//emblishment_embroy_type_arr
				if($emb_name_id==1) //Print type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_print_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				else if($emb_name_id==2) //embro type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_embroy_type_arr[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				else if($emb_name_id==4) //Special type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_spwork_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				else if($emb_name_id==5) //GMT type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_gmts_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				else if($emb_name_id==99) //GMT type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_other_type_arr[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}

				$wash_req_amount=$emb_req_amount=0;
				if($row[csf('emb_name')]==3) //Wash
				{
					if($row[csf('emb_type')]>0) $wash_emb_typeCond=", ".$emblishment_wash_type[$row[csf('emb_type')]];else $wash_emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$wash_emb_typeCond;
					$wash_req_qty=$wash_qtyArr[$row[csf('job_no')]][$row[csf('id')]];
					$wash_req_amount=$wash_amountArr[$row[csf('job_no')]][$row[csf('id')]];
					// echo $emb_name.'='.$wash_emb_typeCond.' <br>';
						 
					$p_wash_precost_arr[$emb_name]['req_qty']+=$wash_req_qty;
					$p_wash_precost_arr[$emb_name]['req_amount']+=$wash_req_amount;
					$p_wash_precost_arr[$emb_name]['cons']+=$row[csf('cons_dzn_gmts')];
					//$p_wash_precost_arr[$emb_name]['tot_cons']+=$row[csf('cons_dzn_gmts')];
					$p_wash_precost_arr[$emb_name]['amount']+=$row[csf('amount')];
					$p_wash_precost_arr[$emb_name]['p_loss']=$row[csf('p_loss')];
					$p_wash_precost_arr[$emb_name]['sourcing_rate']=$row[csf('sourcing_rate')];
					$p_wash_precost_arr[$emb_name]['pre_rate']=$row[csf('rate')];
					$p_wash_precost_arr[$emb_name]['sourcing_nominated_supp']=$row[csf('sourcing_nominated_supp')];
					$p_wash_precost_arr[$emb_name]['uom']=$unit_of_measurement[$row[csf('uom')]];
					$summ_fob_pcs+=$row[csf('amount')];
					$summ_sourcing_tot_budget_dzn_val+=$wash_req_qty*$row[csf('sourcing_rate')];
					$p_wash_tot_row+=1;	
				}
				else
				{
					$emb_req_qty=$emblishment_qtyArr[$row[csf('job_no')]][$row[csf('id')]];
					$emb_req_amount=$emblishment_amountArr[$row[csf('job_no')]][$row[csf('id')]];
					$p_embro_precost_arr[$emb_name]['req_qty']+=$emb_req_qty;
					$p_embro_precost_arr[$emb_name]['req_amount']+=$emb_req_amount;
					$p_embro_precost_arr[$emb_name]['cons']+=$row[csf('cons_dzn_gmts')];
					//$p_wash_precost_arr[$emb_name]['tot_cons']+=$row[csf('cons_dzn_gmts')];
					$p_embro_precost_arr[$emb_name]['amount']+=$row[csf('amount')];
					$p_embro_precost_arr[$emb_name]['p_loss']=$row[csf('p_loss')];
					$p_embro_precost_arr[$emb_name]['pre_rate']=$row[csf('rate')];
					if($row[csf('sourcing_rate')]>0)
					{
						$p_embro_precost_arr[$emb_name]['sourcing_rate']=$row[csf('sourcing_rate')];
					}
					$p_embro_precost_arr[$emb_name]['sourcing_nominated_supp']=$row[csf('sourcing_nominated_supp')];
					$p_embro_precost_arr[$emb_name]['uom']=$unit_of_measurement[$row[csf('uom')]];
					$summ_fob_pcs+=$row[csf('amount')];
					$summ_sourcing_tot_budget_dzn_val+=$emb_req_qty*$row[csf('sourcing_rate')];
					$p_embro_tot_row+=1;	
				}
				//$summ_fob_value_pcs+=$row[csf('amount')]/$order_price_per_dzn;
				$summ_fob_gross_value_amt+=$emb_req_amount+$wash_req_amount;
			}
			$sql_other = "select fabric_cost, trims_cost, embel_cost, wash_cost, margin_dzn,comm_cost, commission, lab_test, inspection, cm_cost, freight, currier_pre_cost, certificate_pre_cost, design_cost, studio_cost, common_oh,common_oh_percent, depr_amor_pre_cost, interest_cost, incometax_cost, total_cost,price_dzn, job_no from wo_pre_cost_dtls where  job_no=$txt_job_no  and status_active=1 and  is_deleted=0";
			$pre_other_result=sql_select($sql_other);//$summ_fob_value_pcs=0; TOTAL_COST
			foreach( $pre_other_result as $row )
			{
				$cm_cost=$row[csf('cm_cost')];
				$total_fob=$row[csf('price_dzn')];
				$total_cost=$row[csf('total_cost')];
				
				$lab_test=($row[csf('lab_test')]/$order_price_per_dzn)*$ordQtyUom;
				$currier_pre_cost=($row[csf('currier_pre_cost')]/$order_price_per_dzn)*$ordQtyUom;
				$inspection=($row[csf('inspection')]/$order_price_per_dzn)*$ordQtyUom;
				$comarcial=($row[csf('comm_cost')]/$order_price_per_dzn)*$ordQtyUom;
				
				$freight=($row[csf('freight')]/$order_price_per_dzn)*$ordQtyUom;
				$certificate_pre_cost=($row[csf('certificate_pre_cost')]/$order_price_per_dzn)*$ordQtyUom;
				$design_pre_cost=($row[csf('design_cost')]/$order_price_per_dzn)*$ordQtyUom;
				$studio_pre_cost=($row[csf('studio_cost')]/$order_price_per_dzn)*$ordQtyUom;
				$common_oh=($row[csf('common_oh')]/$order_price_per_dzn)*$order_job_qnty;
				$depr_amor_pre_cost=($row[csf('depr_amor_pre_cost')]/$order_price_per_dzn)*$ordQtyUom;
				$interest_pre_cost=($row[csf('interest_cost')]/$order_price_per_dzn)*$ordQtyUom;
				$income_tax_pre_cost=($row[csf('incometax_cost')]/$order_price_per_dzn)*$ordQtyUom;
				
				$operating_oh=$row[csf('common_oh')];
				$operating_oh_per=$row[csf('common_oh_percent')];
				
				$tot_other_for_fob_value=$lab_test+$currier_pre_cost+$inspection+$comarcial+$freight+$certificate_pre_cost+$design_pre_cost+$studio_pre_cost+$common_oh+$interest_pre_cost+$income_tax_pre_cost+$depr_amor_pre_cost;
				//echo $tot_other_for_fob_value;
				$lab_test_dzn=$row[csf('lab_test')];
				$fob_pcs=$row[csf('price_with_commn_pcs')];
				$currier_pre_cost_dzn=$row[csf('currier_pre_cost')];
				$inspection_dzn=$row[csf('inspection')];
				$comarcial_dzn=$row[csf('comm_cost')];
				
				$common_oh_dzn=$row[csf('common_oh')];
				$studio_pre_cost_dzn=$row[csf('studio_cost')];
				$design_pre_cost_dzn=$row[csf('design_cost')];
				$certificate_pre_cost_dzn=$row[csf('certificate_pre_cost')];
				
				$freight_dzn=$row[csf('freight')];
				//$comm_cost_dzn=$row[csf('comm_cost')];
				$depr_amor_pre_cost_dzn=$row[csf('depr_amor_pre_cost')];
				$income_tax_pre_cost_dzn=$row[csf('incometax_cost')];
				$interest_pre_cost_dzn=$row[csf('interest_cost')];
				
				$cm_cost_dzn=$row[csf('cm_cost')];
				$cm_cost_pcs=$row[csf('cm_cost')]/$order_price_per_dzn;
				$cm_cost_req=($row[csf('cm_cost')]/$order_price_per_dzn)*$order_job_qnty;
				$tot_cm_qty_dzn=$row[csf('cm_cost')]*$po_qty_dzn;
				//$cmCost=(($row[csf('cm_cost')]+$row[csf('margin_dzn')])/$order_price_per_dzn)*$ordQtyUom;
				$cmCost=$other_amountArr[$row[csf('job_no')]]['cm_cost'];

				$tot_summ_fob_pcs=$row[csf('total_cost')];
				$tot_other_cost_dzn=$studio_pre_cost_dzn+$design_pre_cost_dzn+$certificate_pre_cost_dzn+$freight_dzn+$depr_amor_pre_cost_dzn+$income_tax_pre_cost_dzn+$interest_pre_cost_dzn;
				 
				$tot_other_cost=($tot_other_cost_dzn/$order_price_per_dzn)*$ordQtyUom;
				//$summ_fob_value_pcs+=($tot_other_cost_dzn+$currier_pre_cost_dzn+$lab_test_dzn+$inspection_dzn+$comarcial_dzn)*$order_price_per_dzn+$cm_cost_pcs;
				
				// echo $tot_other_for_fob_value.'m';
				$summ_fob_gross_value_amt+=$tot_other_for_fob_value+$tot_cm_qty_dzn ;
				$summ_fob_pcs+=$tot_other_cost_dzn+$lab_test_dzn+$currier_pre_cost_dzn+$inspection_dzn+$comarcial_dzn+$cm_cost_dzn;
				$summ_sourcing_tot_budget_dzn_val+=$tot_other_for_fob_value;
			}
			
			$sql_commi = "select id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount, status_active from  wo_pre_cost_commiss_cost_dtls  where job_no=".$txt_job_no." and status_active=1  and commission_amount>0";
			$result_commi=sql_select($sql_commi);
		 	foreach( $result_commi as $row )
			{
				$commission_type_id=$row[csf('particulars_id')];
				$com_type_id=$row[csf('commission_base_id')];
				
				$commission_arr[$commission_type_id]['commi_req_amt']=($row[csf('commission_amount')]/$order_price_per_dzn)*$ordQtyUom;
				$commission_arr[$commission_type_id]['commi_amt']=$row[csf('commission_amount')];
				$commission_arr[$commission_type_id]['commi_amt_pcs']=$row[csf('commission_amount')]*$order_price_per_dzn;
				//$summ_fob_value_pcs+=$row[csf('commission_amount')]/$order_price_per_dzn;
				$summ_fob_gross_value_amt+=($row[csf('commission_amount')]/$order_price_per_dzn)*$order_job_qnty;
				$summ_fob_pcs+=$row[csf('commission_amount')];
				$summ_sourcing_tot_budget_dzn_val+=($row[csf('commission_amount')]/$order_price_per_dzn)*$order_job_qnty;
			} 
			$summ_sourcing_fob_pcs=$summ_sourcing_tot_budget_dzn_val/$order_job_qnty;
			$summ_tot_final_cm=($summ_fob_gross_value_amt-$summ_sourcing_tot_budget_dzn_val)/$offer_qty_dzn;
			// $tot_summ_fob_pcs=$summ_fob_pcs/$order_price_per_dzn;
			
			$supplier_library_arr=return_library_array( "select a.short_name, a.id from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id   and a.is_deleted=0  and a.status_active=1 group by a.id,a.short_name order by a.short_name", "id", "short_name");
			?>
			<style>
			#th_tbl_height {
			  height: 18px;
			  width: 100%;
			  border: 1px solid #4CAF50;
			}
			</style>
            <script>
				//gross_fob_value_th
				function fnc_fob_verify()
				{
					var gross_fob_value=document.getElementById('gross_fob_value_th').value*1;
					//alert(gross_fob_value);
					document.getElementById('fob_verify_td').innerHTML=number_format_common(gross_fob_value,2);
				}
				
			</script>
			<td valign="top">
				<table class="rpt_table" align="left" border="1" cellpadding="1" cellspacing="1" style="width:280px; margin:5px; font-size:14px;" rules="all">
					<tr>
						<td width="80" colspan="2" align="center"><b>Summary </b></td>
					</tr>
					<tr>
						<td width="80"><b>Header </b></td>
						<td width="80"><b>Pre Cost</b> </td>
					</tr>
					<tr>
						<td>PO Qty <?='-'.$unit_of_measurement[$orderUom]; ?></td>
						<td title="=<? echo $ordQtyUom;?>"><b><?=number_format($ordQtyUom,0); ?></b></td>
					</tr> 
					<tr>
						<td>FOB <?='-'.$unit_of_measurement[$orderUom]; ?></td>
						<td title="Price=<? echo $avg_unit_price;?>"><b><?=number_format($avg_unit_price,4); ?></b></td>
					</tr>
					<tr>
						<td>FOB Value</td>
						<td title=""><b><?=number_format($avg_unit_price*$ordQtyUom,2); ?></b></td>
					</tr> 
					<tr>
						<td>SMV/<?=$unit_of_measurement[$orderUom]; ?></td>
						<td title="=<? //echo $summ_fob_gross_value_amt;?>"><b><?=number_format($sew_smv,4); ?></b></td>
					</tr> 
					<tr>
						<td>Budget Cost/<?=$unit_of_measurement[$orderUom]; ?></td>
						<td  title="Budget Cost/<?=$unit_of_measurement[$orderUom]; ?>=<? echo $tot_summ_fob_pcs;?>"><b><?=number_format($tot_summ_fob_pcs,4); ?></b></td>
					</tr> 
					<tr>
						<td>Margin/<?=$unit_of_measurement[$orderUom]; ?>[USD] &nbsp; <? echo $operating_oh_per;?>%</td>
						<td  title="<? //echo $summ_fob_gross_value_amt;?>"><b><? 
						$margin_pre=$operating_oh;//$avg_unit_price-$tot_summ_fob_pcs;
						$margin_final=$avg_unit_price-$summ_sourcing_fob_pcs;
						echo  number_format($margin_pre,4); ?></b></td>
					</tr> 
					<tr>
						<td>Total Margin Value</td>
						<td title="Margin Pcs*PO Qty <?=$unit_of_measurement[$orderUom]; ?>"><b><?=number_format($ordQtyUom*$margin_pre,2);
						$tot_margin_value=$ordQtyUom*$margin_pre; ?></b></td>
					</tr> 
					
					<tr>
						<td><b>CM/<?=$unit_of_measurement[$orderUom]; ?>[USD]</b></td> 
						<td title="CM Cost/1 Pcs"><b><? echo number_format($cm_cost_dzn,4); ?></b></td>
					</tr> 
					<tr>
						<td>Total CM Value</td>
						<td  title="CM Pcs*PO Qty <?=$unit_of_measurement[$orderUom]; ?>"><b><?=number_format($ordQtyUom*$cm_cost_dzn,2); ?></b></td>
					</tr> 
					<tr>
						<td>E.P.M [USD]</td>
						<td title="CM/Costing Per/Sew SMV"><b><?=number_format($cm_cost_dzn/$order_price_per_dzn/$sew_smv,4); ?></b></td>
					</tr>
					<? 
                    $sqlcpm=sql_select("select asking_profit from lib_standard_cm_entry where company_id=$cbo_company_name and '$costing_date' between applying_period_date and applying_period_to_date and status_active=1 and is_deleted=0");
                    $asking_profit=0;
                    foreach($sqlcpm as $sqlcpmrow){
                        $asking_profit=$sqlcpmrow[csf('asking_profit')];
                    }
                    $profit_margin=$cm_cost*$asking_profit/100;
                    $buffer=$total_fob-($total_cost+$profit_margin);
                    ?>
                    <tr>
                    <td>Profit Margin</td>
                    <td  title="CM/<?=$unit_of_measurement[$orderUom]; ?> X Asking Profit <?= $asking_profit ?> %"><b><?=number_format($profit_margin,4); ?></b></td>
                    </tr> 
                    <tr>
                        <td>Buffer</td>
                        <td title="Total FOB - Total Cost - Profit Margin"><b><?=number_format($buffer,4); ?></b></td>
                    </tr>
				</table>
			</td>
			<td valign="top">
				<? $nameArray_imge =sql_select("SELECT image_location,real_file_name FROM common_photo_library where master_tble_id=".$txt_job_no." and file_type=1"); ?>
				<br>      
				<table class="rpt_table" width="300"  border="1" cellpadding="0" cellspacing="0" rules="all">
					<tr>
						<td width="120">First Shipment Date:</td><td><?=change_date_format($first_pub_shipment_date); ?></td> 
					</tr>
					<tr>
						<td>Last Shipment Date:</td><td><?=change_date_format($last_pub_shipment_date);?></td>
					</tr>
					<tr>
						<td title="(CM/<?=$unit_of_measurement[$orderUom]; ?>[USD]+Margin/<?=$unit_of_measurement[$orderUom]; ?>[USD] &nbsp; <? echo $operating_oh_per;?>%)/SMV/<?=$unit_of_measurement[$orderUom]; ?>">EPM with Margin</td><td title="<?='('.$cm_cost_dzn.'+'.$margin_pre.')/'.$sew_smv; ?>"><?=number_format(($cm_cost_dzn+$margin_pre)/$sew_smv,4); ?></td>
					</tr>
                    <tr>
						<td>FOB Verify:</td>
                        <td id="fob_verify_td" title="Total Margin Value+GrossFOB Value [A+B+C+D+E+F+G]"><? $fob_varify=$avg_unit_price*$ordQtyUom;echo number_format($fob_varify,2);?></td>
					</tr>
				</table>
				<br/> <br/>
				<?
				if($approved_id==1) $app_msg="Approved"; else if($approved_id==3) $app_msg="Partial Approved";  else $app_msg="Un-Approved";
				
				if($app_msg!='')
				{
					?>
					<table class="rpt_table" width="300"  border="1" cellpadding="0" cellspacing="0" rules="all">
						<tr>
						<td style="color:#F00; font-size:16px"><b> Approval Status: &nbsp; <?=$app_msg;?> </b></td>
						</tr>
					</table>
					<?
				}
				?>
			</td>
		</tr>
		</table>
		<?
		//end first foearch
		if($zero_value==1)
		{	
			?>
			<table align="left" class="rpt_table" border="1" cellpadding="1" cellspacing="1"  width="1200" style=" margin:5px;font-size:14px;" rules="all"> 
                <caption><b>Details Part</b></caption>
                <tr style="font-weight:bold">
                    <td  width="20">SL </td>
                    <td  width="350" title="Fabric">ITEM DESCRIPTION </td>
                    <td  width="70">Cons/<?=$unit_of_measurement[$orderUom]; ?></td>
                    <td  width="70">Wast % </td>
                    <td  width="70">Total Cons/<?=$unit_of_measurement[$orderUom]; ?></td>
                    <td  width="50">UOM </td>
                    <td  width="70">Req. Qty</td>
                    <td  width="70">Rate(USD)</td>
                    <td  width="70">Cost/<?=$unit_of_measurement[$orderUom]; ?></td>
                    <td  width="70">Total Budget</td>
                    <td  width="170">Remarks</td>
                </tr>
                <?
                $f=1;$tot_fab_amount=$tot_fab_amount_pcs=$tot_fab_req_amount=0;$ff=1;$tot_fab_req_sourcing_amount=$tot_fab_req_sourcing_bal_amount=0;
                foreach($p_fab_precost_arr as $fabriccost_dtls_id=>$fabriccost_dtls_data)
                {
					foreach ($fabriccost_dtls_data as  $fab_type=>$fab_data) 
					{
						foreach($fab_data as $fab_desc=>$row)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							$nominated_supp_str=""; 
							$exnominated_supp=explode(",",$row[('sourcing_nominated_supp')]);
							foreach($exnominated_supp as $supp)
							{
								if($nominated_supp_str=="") $nominated_supp_str=$supplier_library_arr[$supp]; else $nominated_supp_str.=','.$supplier_library_arr[$supp];
							}	
							?>
							<tr id="" bgcolor="<? //echo $bgcolor;?>">
                                <td width="20" align="center"><p><? echo $f; ?></p></td>
                                <td width="350"><div style="word-break:break-all"><? echo $fab_desc; //echo $fab_type.','.$fab_desc; ?></div></td>
                                <td width="70" align="right"><p><? echo number_format($row['cons'],4); ?></p></td>
                                <td width="70" align="right"><p><? echo number_format($row[('p_loss')],4); ?></p></td>
                                <td width="70" align="right"><p><? echo number_format($row[('tot_cons')],4); ?></p></td>
                                <td width="50"><p><? echo $row[('uom')]; ?></p></td>
                                <td width="70" align="right"><p><? echo number_format($row[('req_qty')],4); ?></p></td>
                                <td width="70" align="right"><p><? echo number_format($row[('req_amount')]/$row[('req_qty')],4); ?></p></td>
                                <td width="70" align="right"><p><? echo number_format($row[('amount')]/$order_price_per_dzn,4); ?></p></td>
                                <td width="70" align="right"><p><? echo number_format($row[('req_amount')],4); ?></p></td>
                                <td width="170" align="left"><p><?=$row[('remarks')];?></p></td>
							</tr>
							<?
							$f++;$ff++;
							$tot_fab_amount+=$row[('amount')];
							$tot_fab_amount_pcs+=$row[('amount')]/$order_price_per_dzn;
							$tot_fab_req_amount+=$row[('req_amount')];
							$tot_fab_req_sourcing_amount+=$row[('sourcing_rate')]*$row[('req_qty')];
							$tot_fab_req_sourcing_bal_amount+=$bal_sourcing_amount;
						}
					}
                }
                ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="8"> <b style="float:left">A Total Fabric Cost</b></td>
                    <td  align="right"><b><? echo number_format($tot_fab_amount_pcs,4); ?></b></td>
                    <td  align="right"><b><? echo number_format($tot_fab_req_amount,4); ?></b></td>
                    <td  align="right"><b></b></td>
                </tr>
			</table>
			<?
		}
		else
		{ 
			if(count($p_fab_precost_arr)>0)
			{
				?>
				<table  align="left" class="rpt_table" border="1" cellpadding="1" cellspacing="1"  width="1200" style=" margin:5px; font-size:14px;" rules="all"> 
                    <caption><b>Details Part</b></caption>
                    <tr style="font-weight:bold">
                        <td  width="20">SL </td>
                        <td  width="350" title="Fabric">ITEM DESCRIPTION </td>
                        <td  width="70">Cons/<?=$unit_of_measurement[$orderUom]; ?></td>
                        <td  width="70">Wast % </td>
                        <td  width="70">Total Cons/<?=$unit_of_measurement[$orderUom]; ?></td>
                        <td  width="50">UOM </td>
                        <td  width="70">Req. Qty</td>
                        <td  width="70">Rate[USD]</td>
                        <td  width="70">Cost/<?=$unit_of_measurement[$orderUom]; ?></td>
                        <td  width="70">Total Budget</td>
                        <td  width="170">Remarks</td>
                    </tr>
                    
                    <?
                    $f=1;$tot_fab_amount=$tot_fab_amount_pcs=$tot_fab_req_amount=0;$ff=1;$tot_fab_req_sourcing_amount=$tot_fab_req_sourcing_bal_amount=0;
                    foreach($p_fab_precost_arr as $fabriccost_dtls_id=>$fabriccost_dtls_data)
                    {
						foreach ($fabriccost_dtls_data as  $fab_type=>$fab_data) 
						{
							foreach($fab_data as $fab_desc=>$row)
							{
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								
								$nominated_supp_str=""; 
								$exnominated_supp=explode(",",$row[('sourcing_nominated_supp')]);
								foreach($exnominated_supp as $supp)
								{
									if($nominated_supp_str=="") $nominated_supp_str=$supplier_library_arr[$supp]; else $nominated_supp_str.=','.$supplier_library_arr[$supp];
								}	
								?>
								<tr  id="" bgcolor="<? //echo $bgcolor;?>">
                                    <td width="20" align="center"><p><? echo $f; ?></p></td>
                                    <td width="450"><div style="word-break:break-all"><? echo $fab_desc; //echo $fab_type.','.$fab_desc; ?></div></td>
                                    <td width="70" align="right"><p><? echo number_format($row['cons'],4); ?></p></td>
                                    <td width="70" align="right"><p><? echo number_format($row[('p_loss')],4); ?></p></td>
                                    <td width="70" align="right"><p><? echo number_format($row[('tot_cons')],4); ?></p></td>
                                    <td width="50"><p><? echo $row[('uom')]; ?></p></td>
                                    <td width="70" align="right"><p><? echo number_format($row[('req_qty')],4); ?></p></td>
                                    <td width="70" align="right"><p><? echo number_format($row[('req_amount')]/$row[('req_qty')],4); ?></p></td>
                                    <td width="70" align="right"><p><? echo number_format($row[('amount')]/$order_price_per_dzn,4); ?></p></td>
                                    <td width="70" align="right"><p><? echo number_format($row[('req_amount')],4); ?></p></td>
                                    <td width="170" align="left"><p><?=$row[('remarks')];?></p></td>
								</tr>
								<?
								$f++;$ff++;
								$tot_fab_amount+=$row[('amount')];
								$tot_fab_amount_pcs+=$row[('amount')]/$order_price_per_dzn;
								$tot_fab_req_amount+=$row[('req_amount')];
								$tot_fab_req_sourcing_amount+=$row[('sourcing_rate')]*$row[('req_qty')];
								$tot_fab_req_sourcing_bal_amount+=$bal_sourcing_amount;
							}
						}
                    }
                    ?>
                    <tr class="rpt_bottom" style="font-weight:bold">
                        <td colspan="8"> <b style="float:left">A Total Fabric Cost</b></td>
                        <td  align="right"><b><? echo number_format($tot_fab_amount_pcs,4); ?></b></td>
                        <td  align="right"><b><? echo number_format($tot_fab_req_amount,4); ?></b></td>
                        <td  align="right"><b></b></td>
                    </tr>
				</table>
				<? 
			}// zero value check end
		}
               // die;
			   
		if($zero_value==1)
		{
			?>
			<table align="left" class="rpt_table" border="1" cellpadding="0" cellspacing="0"  width="1200" style="margin:5px; font-size:14px;" rules="all">
                <tr style="font-weight:bold">
                    <td  width="20">SL </td>
                    <td  width="350" title="Trim sew">ITEM DESCRIPTION </td>
                    <td  width="70">Cons/<?=$unit_of_measurement[$orderUom]; ?></td>
                    <td  width="70">Wast % </td>
                    <td  width="70">Total Cons/<?=$unit_of_measurement[$orderUom]; ?></td>
                    <td  width="50">UOM </td>
                    <td  width="70">Req. Qty</td>
                    <td  width="70">Rate[USD]</td>
                    <td  width="70">Cost/<?=$unit_of_measurement[$orderUom]; ?></td>
                    <td  width="70">Total Budget</td>
                    <td  width="170">Remarks</td>
                </tr>
                <?
                $trims_tot_amount=0;
                $ts=1;$tot_sew_amount=$tot_amount_pcs=$tot_sew_amount_pcs=$tot_sew_req_amount=0;$ttts=1;$tot_sew_req_sourcing_amount=$tot_sew_req_sourcing_bal_amount=0;
                
                foreach($pre_trim_result as $row){
                
					$trims_type=$row[csf('trim_type')];
					$description=$row[csf('description')];
					if($description!="") $descriptionCond=", ".$description; else $descriptionCond="";
					$item_id=$row[csf('item_name')].$descriptionCond;
					//$item_name_arr[$item_id]=$row[csf('item_name')].$descriptionCond;
					
					if($ts%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$nominated_supp_str=""; 
					$exnominated_supp=explode(",",$row[('sourcing_nominated_supp')]);
					foreach($exnominated_supp as $supp)
					{
						if($nominated_supp_str=="") $nominated_supp_str=$supplier_library_arr[$supp]; else $nominated_supp_str.=','.$supplier_library_arr[$supp];
					}	
					
					$req_amt=$row[csf('cons_dzn_gmts')]*$row[csf('rate')];
					
					$p_sew_loss=$row[csf('ex_per')];
					$ex_tot=$row[csf('cons_dzn_gmts')]+(($row[csf('cons_dzn_gmts')]*$p_sew_loss)/100);
					
					$trim_req_qty=$trim_qty_arr[$row[csf('id')]];
					$trim_req_amount=$trim_amount_arr[$row[csf('id')]];
					
					//$p_sew_trim_precost_arr[$item_id]['tot_row']+=1;
					$p_sew_trim_tot_row+=1;
					
					$summ_fob_pcs+=$row[csf('amount')];	
					$summ_sourcing_tot_budget_dzn_val+=$trim_req_qty*$row[csf('sourcing_rate')];
					$summ_fob_gross_value_amt+=$trim_req_amount;
					?>
					<tr bgcolor="<? //echo $bgcolor;?>">
                        <td width="20" align="center"><p><? echo $ts; ?></p></td>
                        <td width="350" ><div style="word-break:break-all"><? echo $item_id; ?></div></td> 
                        <td width="70" align="right"><p><? echo number_format($row[csf('tot_cons')],6); ?></p></td>
                        <td width="70" align="right"><p><? echo number_format($p_sew_loss,6); ?></p></td>
                        <td width="70" align="right"><p><?  echo number_format($row[csf('cons_dzn_gmts')],6); ?></p></td>
                        <td width="50" align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
                        <td width="70" align="right"><p><? echo number_format($trim_req_qty,5); ?></p></td>
                        <td width="70" align="right"><p><? echo number_format($trim_req_amount/$trim_req_qty,6); ?></p></td>
                        <td width="70" align="right"><p><? echo number_format($row[csf('amount')]/$order_price_per_dzn,6); ?></p></td>
                        <td width="70" align="right"><p><? echo number_format($trim_req_amount,6); ?></p></td>
                        <td width="170" align="left"><p><?=$row[csf('remark')];?></p></td>
					</tr>
					<?
					$ts++;$ttts++;
					$tot_sew_amount+=$row[csf('amount')];
					$tot_sew_amount_pcs+=$row[csf('amount')]/$order_price_per_dzn;
					$tot_sew_req_amount+=$trim_req_amount;
					$tot_sew_req_sourcing_amount+=$row[csf('sourcing_rate')]*$trim_req_qty;
					$tot_sew_req_sourcing_bal_amount+=$bal_sourcing_amout;
					$trims_tot_amount+=$trim_req_amount;
                }
                ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="8" align="left"><b>B-Total Trims Cost:</b></td>
                    <td  align="right"><b><? echo number_format($tot_sew_amount_pcs,6); ?></b></td>
                    <td  align="right"><b><? echo number_format($tot_sew_req_amount,6); ?></b></td>
                    <td  align="right"><b></b></td>
                </tr>
			</table>
			<?
		}
		else
		{ 
			if(count($pre_trim_result)>0)
			{
				?>
				<table align="left" class="rpt_table" border="1" cellpadding="0" cellspacing="0"  width="1200" style="margin:5px; font-size:14px;" rules="all">
                    <tr style="font-weight:bold">
                        <td  width="20">SL </td>
                        <td  width="350" title="Trim sew">ITEM DESCRIPTION </td>
                        <td  width="70">Cons/<?=$unit_of_measurement[$orderUom]; ?></td>
                        <td  width="70">Wast % </td>
                        <td  width="70">Total Cons/<?=$unit_of_measurement[$orderUom]; ?></td>
                        <td  width="50">UOM </td>
                        <td  width="70">Req. Qty</td>
                        <td  width="70">Rate(USD)</td>
                        <td  width="70">Cost/<?=$unit_of_measurement[$orderUom]; ?></td>
                        <td  width="70">Total Budget</td>
                        <td  width="170">Remarks</td>
                    </tr>
                    <?
                    $trims_tot_amount=0;
                    $ts=1;$tot_sew_amount=$tot_amount_pcs=$tot_sew_amount_pcs=$tot_sew_req_amount=0;$ttts=1;$tot_sew_req_sourcing_amount=$tot_sew_req_sourcing_bal_amount=0;
                    
                    foreach($pre_trim_result as $row){
						$trims_type=$row[csf('trim_type')];
						$description=$row[csf('description')];
						if($description!="") $descriptionCond=", ".$description; else $descriptionCond="";
						$item_id=$row[csf('item_name')].$descriptionCond;
						//$item_name_arr[$item_id]=$row[csf('item_name')].$descriptionCond;
						
						if($ts%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$nominated_supp_str=""; 
						$exnominated_supp=explode(",",$row[('sourcing_nominated_supp')]);
						foreach($exnominated_supp as $supp)
						{
							if($nominated_supp_str=="") $nominated_supp_str=$supplier_library_arr[$supp]; else $nominated_supp_str.=','.$supplier_library_arr[$supp];
						}	
						
						$req_amt=$row[csf('cons_dzn_gmts')]*$row[csf('rate')];
						
						$p_sew_loss=$row[csf('ex_per')];
						$ex_tot=$row[csf('cons_dzn_gmts')]+(($row[csf('cons_dzn_gmts')]*$p_sew_loss)/100);
						
						$trim_req_qty=$trim_qty_arr[$row[csf('id')]];
						$trim_req_amount=$trim_amount_arr[$row[csf('id')]];
						
						//$p_sew_trim_precost_arr[$item_id]['tot_row']+=1;
						$p_sew_trim_tot_row+=1;
						
						$summ_fob_pcs+=$row[csf('amount')];	
						$summ_sourcing_tot_budget_dzn_val+=$trim_req_qty*$row[csf('sourcing_rate')];
						$summ_fob_gross_value_amt+=$trim_req_amount;
						?>
						<tr bgcolor="<? //echo $bgcolor;?>">
                            <td width="20" align="center"><p><? echo $ts; ?></p></td>
                            <td width="350" ><div style="word-break:break-all"><? echo $item_id; ?></div></td> 
                            <td width="70" align="right"><p><? echo number_format($row[csf('tot_cons')],6); ?></p></td>
                            <td width="70" align="right"><p><? echo number_format($p_sew_loss,6); ?></p></td>
                            <td width="70" align="right"><p><?  echo number_format($row[csf('cons_dzn_gmts')],6); ?></p></td>
                            <td width="50" align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
                            <td width="70" align="right"><p><? echo number_format($trim_req_qty,5); ?></p></td>
                            <td width="70" align="right"><p><? echo number_format($trim_req_amount/$trim_req_qty,6); ?></p></td>
                            <td width="70" align="right"><p><? echo number_format($row[csf('amount')]/$order_price_per_dzn,6); ?></p></td>
                            <td width="70" align="right"><p><? echo number_format($trim_req_amount,6); ?></p></td>
                            <td width="170" align="left"><p><?=$row[csf('remark')];?></p></td>
						</tr>
						<?
						$ts++;$ttts++;
						$tot_sew_amount+=$row[csf('amount')];
						$tot_sew_amount_pcs+=$row[csf('amount')]/$order_price_per_dzn;
						$tot_sew_req_amount+=$trim_req_amount;
						$tot_sew_req_sourcing_amount+=$row[csf('sourcing_rate')]*$trim_req_qty;
						$tot_sew_req_sourcing_bal_amount+=$bal_sourcing_amout;
						$trims_tot_amount+=$trim_req_amount;
					}
                    ?>
                    <tr class="rpt_bottom" style="font-weight:bold">
                        <td colspan="8" align="left"><b>B-Total Trims Cost:</b></td>
                        <td  align="right"><b><? echo number_format($tot_sew_amount_pcs,6); ?></b></td>
                        <td  align="right"><b><? echo number_format($tot_sew_req_amount,6); ?></b></td>
                        <td  align="right"><b></b></td>
                    </tr>
				</table>
				<?    
			}
		}
                // die;
		if($zero_value==1)
		{
			?>
			<table align="left" class="rpt_table" border="1" cellpadding="0" cellspacing="0"  width="1000" style=" margin:5px; font-size:14px;" rules="all"> 
                <caption><b style="float:left">Gmts Wash</b></caption>
                <tr align="center">
                    <td width="20">SL </td>
                    <td width="400" title="Wash ">ITEM DESCRIPTION </td>
                    <td width="70">Cons/PCS</td>
                    <td width="70">Wast % </td>
                    <td width="70">Total Cons/<?=$unit_of_measurement[$orderUom]; ?></td>
                    <td width="50">UOM </td>
                    <td width="70">Req. Qty</td>
                    <td width="70"> Rate(USD)</td>
                    <td width="70">Cost/<?=$unit_of_measurement[$orderUom]; ?></td>
                    <td width="70">Total Budget</td>
                </tr>
                <?
                $w=1;$tot_wash_amount=$tot_wash_amount_pcs=$tot_wash_req_amount=$tot_wash_sourcing_req_amount=$tot_wash_sourcing_req_amount_bal=0;$ws=1;
                foreach($p_wash_precost_arr as $embname_id=>$row)
                {
					if($w%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$wash_nominated_supp_str=""; 
					$exnominated_suppArr=explode(",",$row[('sourcing_nominated_supp')]);
					foreach($exnominated_suppArr as $supp)
					{
						if($wash_nominated_supp_str=="") $wash_nominated_supp_str=$supplier_library_arr[$supp]; else $wash_nominated_supp_str.=','.$supplier_library_arr[$supp];
					}		
					?>
					<tr bgcolor="<? //echo $bgcolor;?>">
                        <td width="20" align="center"><p><? echo $w; ?></p></td>
                        <td width="400"><div style="word-break:break-all"><? $embname_id=explode(", ",$embname_id);echo $embname_id[1]; ?></div></td> 
                        <td width="70" align="right"><p><? echo number_format($row[('cons')],4); ?></p></td>
                        <td width="70" align="right"><p><? //echo $row[('p_loss')]; ?></p></td>
                        <td width="70" align="right"><p><?  echo number_format($row[('cons')],4) ?></p></td>
                        <td width="50"  align="center"><p><? echo $costing_val; ?></p></td>
                        <td width="70" align="right"><p><? echo number_format($row[('req_qty')],4); ?></p></td>
                        <td width="70" align="right" title="Rate=<? echo $row[('pre_rate')];?>"><p><? echo number_format($row[('pre_rate')],4); ?></p></td>
                        <td width="70" align="right"><p><? echo number_format($row[('amount')]/$order_price_per_dzn,4); ?></p></td>
                        <td width="70" align="right"><p><? echo number_format($row[('req_qty')]*$row[('pre_rate')],4); ?></p></td>
					</tr>
					<?
					$w++;$ws++;
					$tot_wash_amount+=$row[('amount')];
					$tot_wash_amount_pcs+=$row[('amount')]/$order_price_per_dzn;
					$tot_wash_req_amount+=$row[('req_qty')]*$row[('pre_rate')];
					$tot_wash_sourcing_req_amount+=$sourcing_tot_amount;
					$tot_wash_sourcing_req_amount_bal+=$sourcing_tot_amount_bal;
                }
                ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="8"> <b>C-Total Wash Cost :</b></td>
                    <td  align="right"><b><? echo number_format($tot_wash_amount_pcs,4); ?></b></td>
                    <td  align="right"><b><? echo number_format($tot_wash_req_amount,4); ?></b></td>
                </tr>
			</table>
			<?
		}
		else
		{ 
			if(count($p_wash_precost_arr)>0)
			{
				?>
				<table align="left" class="rpt_table" border="1" cellpadding="0" cellspacing="0"  width="1000" style=" margin:5px; font-size:14px;" rules="all"> 
                    <caption><b style="float:left">Gmts Wash</b></caption>
                    <tr align="center">
                        <td width="20">SL </td>
                        <td width="450" title="Wash ">ITEM DESCRIPTION </td>
                        <td width="70">Cons/PCS</td>
                        <td width="70">Wast % </td>
                        <td width="70">Total Cons/<?=$unit_of_measurement[$orderUom]; ?></td>
                        <td width="50">UOM </td>
                        <td width="70">Req. Qty</td>
                        <td width="70"> Rate(USD)</td>
                        <td width="70">Cost/<?=$unit_of_measurement[$orderUom]; ?></td>
                        <td width="70">Total Budget</td>
                    </tr>
                    <?
                    $w=1;$tot_wash_amount=$tot_wash_amount_pcs=$tot_wash_req_amount=$tot_wash_sourcing_req_amount=$tot_wash_sourcing_req_amount_bal=0;$ws=1;
                    foreach($p_wash_precost_arr as $embname_id=>$row)
                    {
						if($w%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$wash_nominated_supp_str=""; 
						$exnominated_suppArr=explode(",",$row[('sourcing_nominated_supp')]);
						foreach($exnominated_suppArr as $supp)
						{
							if($wash_nominated_supp_str=="") $wash_nominated_supp_str=$supplier_library_arr[$supp]; else $wash_nominated_supp_str.=','.$supplier_library_arr[$supp];
						}		
						?>
						<tr bgcolor="<? //echo $bgcolor;?>">
                            <td width="20" align="center"><p><? echo $w; ?></p></td>
                            <td width="450"><div style="word-break:break-all"><? $embname_id=explode(", ",$embname_id);echo $embname_id[1]; ?></div></td> 
                            <td width="70" align="right"><p><? echo number_format($row[('cons')],4); ?></p></td>
                            <td width="70" align="right"><p><? //echo $row[('p_loss')]; ?></p></td>
                            <td width="70" align="right"><p><?  echo number_format($row[('cons')],4) ?></p></td>
                            <td width="50"  align="center"><p><? echo $costing_val; ?></p></td>
                            <td width="70" align="right"><p><? echo number_format($row[('req_qty')],4); ?></p></td>
                            <td width="70" align="right" title="Rate=<? echo $row[('pre_rate')];?>"><p><? echo number_format($row[('pre_rate')],4); ?></p></td>
                            <td width="70" align="right"><p><? echo number_format($row[('amount')]/$order_price_per_dzn,4); ?></p></td>
                            <td width="70" align="right"><p><? echo number_format($row[('req_qty')]*$row[('pre_rate')],4); ?></p></td>
						</tr>
						<?
						$w++;$ws++;
						$tot_wash_amount+=$row[('amount')];
						$tot_wash_amount_pcs+=$row[('amount')]/$order_price_per_dzn;
						$tot_wash_req_amount+=$row[('req_qty')]*$row[('pre_rate')];
						$tot_wash_sourcing_req_amount+=$sourcing_tot_amount;
						$tot_wash_sourcing_req_amount_bal+=$sourcing_tot_amount_bal;
                    }
                    ?>
                    <tr class="rpt_bottom" style="font-weight:bold">
                        <td colspan="8"> <b>C-Total Wash Cost :</b></td>
                        <td  align="right"><b><? echo number_format($tot_wash_amount_pcs,4); ?></b></td>
                        <td  align="right"><b><? echo number_format($tot_wash_req_amount,4); ?></b></td>
                    </tr>
				</table>
				<? 
			}
		}
		if($zero_value==1)
		{
			?>
			<table align="left" class="rpt_table" border="1" cellpadding="0" cellspacing="0"  width="1000" style="margin:5px; text-align:center; font-size:14px;" rules="all"> 
                <caption><b style="float:left">Embellishment Cost</b></caption>
                <tr>
                    <td width="20">SL </td>
                    <td width="450" title="">ITEM DESCRIPTION </td>
                    <td width="70">Cons/PCS</td>
                    <td width="70">Wast % </td>
                    <td width="70">Total Cons/<?=$unit_of_measurement[$orderUom]; ?></td>
                    <td width="50">UOM </td>
                    <td width="70">Req. Qty</td>
                    <td width="70">Rate(USD)</td>
                    <td width="70">Cost/<?=$unit_of_measurement[$orderUom]; ?></td>
                    <td width="70">Total Budget</td>
                </tr>
                <?
                $em=1;$tot_embro_amount=$tot_embro_amount_pcs=$tot_embro_req_amount=$tot_emb_sourcing_req_amount=$tot_emb_sourcing_req_amount_bal=0;$emb=1;
                foreach($p_embro_precost_arr as $embname_id=>$row)
                {
					if($w%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$emb_nominated_supp_str=""; 
					$emb_exnominated_suppArr=explode(",",$row[('sourcing_nominated_supp')]);
					foreach($emb_exnominated_suppArr as $supp)
					{
						if($emb_nominated_supp_str=="") $emb_nominated_supp_str=$supplier_library_arr[$supp]; else $emb_nominated_supp_str.=','.$supplier_library_arr[$supp];
					}		
					?>
					<tr bgcolor="<? echo $bgcolor;?>">
                        <td width="20" align="center"><p><? echo $em; ?></p></td>
                        <td width="450"><div style="word-break:break-all"><? echo $embname_id; ?></div></td> 
                        <td width="70" align="right"><p><? echo number_format($row[('cons')],4); ?></p></td>
                        <td width="70" align="right"><p><? //echo $row[('p_loss')]; ?></p></td>
                        <td width="70" align="right"><p><?  echo number_format($row[('cons')],4); ?></p></td>
                        <td width="50"  align="center"><p><? echo $costing_val; ?></p></td>
                        <td width="70" align="right"><p><? echo number_format($row[('req_qty')],4); ?></p></td>
                        <td width="70" align="right"><p><? echo number_format($row[('req_amount')]/$row[('req_qty')],4); ?></p></td>
                        <td width="70" align="right"><p><? echo number_format($row[('amount')]/$order_price_per_dzn,4); ?></p></td>
                        <td width="70" align="right"><b><? echo number_format($row[('req_amount')],4); ?></b></td>
					</tr>
					<?
					$em++;$emb++;
					$tot_embro_amount+=$row[('amount')];
					$tot_embro_amount_pcs+=$row[('amount')]/$order_price_per_dzn;
					$tot_embro_req_amount+=$row[('req_amount')];
					$tot_emb_sourcing_req_amount+=$emb_sourcing_tot_amount;
					$tot_emb_sourcing_req_amount_bal+=$emb_sourcing_tot_amount_bal;
                }
                ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="8"> <b>D-Total Embellishment Cost:</b></td>
                    <td  align="right"><b><? echo number_format($tot_embro_amount_pcs,4); ?></b></td>
                    <td  align="right"><b><? echo number_format($tot_embro_req_amount,4); ?></b></td>
                </tr>
			</table>
			<?
		}
		else{ 
			if(count($p_embro_precost_arr)>0)
			{
				?>
				<table align="left" class="rpt_table" border="1" cellpadding="0" cellspacing="0"  width="1000" style="margin:5px; text-align:center; font-size:14px;" rules="all"> 
                    <caption><b style="float:left">Embellishment Cost</b></caption>
                    <tr>
                        <td width="20">SL </td>
                        <td width="450" title="">ITEM DESCRIPTION </td>
                        <td width="70">Cons/<?=$unit_of_measurement[$orderUom]; ?></td>
                        <td width="70">Wast % </td>
                        <td width="70">Total Cons/<?=$unit_of_measurement[$orderUom]; ?></td>
                        <td width="50">UOM </td>
                        <td width="70">Req. Qty</td>
                        <td width="70">Rate(USD)</td>
                        <td width="70">Cost/<?=$unit_of_measurement[$orderUom]; ?></td>
                        <td width="70">Total Budget</td>
                    </tr>
                    <?
                    $em=1;$tot_embro_amount=$tot_embro_amount_pcs=$tot_embro_req_amount=$tot_emb_sourcing_req_amount=$tot_emb_sourcing_req_amount_bal=0;$emb=1;
                    foreach($p_embro_precost_arr as $embname_id=>$row)
                    {
						if($w%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$emb_nominated_supp_str=""; 
						$emb_exnominated_suppArr=explode(",",$row[('sourcing_nominated_supp')]);
						foreach($emb_exnominated_suppArr as $supp)
						{
							if($emb_nominated_supp_str=="") $emb_nominated_supp_str=$supplier_library_arr[$supp]; else $emb_nominated_supp_str.=','.$supplier_library_arr[$supp];
						}		
						?>
						<tr bgcolor="<? echo $bgcolor;?>">
                            <td width="20" align="center"><p><? echo $em; ?></p></td>
                            <td width="450"><div style="word-break:break-all"><? echo $embname_id; ?></div></td> 
                            <td width="70" align="right"><p><? echo number_format($row[('cons')],4); ?></p></td>
                            <td width="70" align="right"><p><? //echo $row[('p_loss')]; ?></p></td>
                            <td width="70" align="right"><p><?  echo number_format($row[('cons')],4); ?></p></td>
                            <td width="50" align="center"><p><? echo $costing_val; ?></p></td>
                            <td width="70" align="right"><p><? echo number_format($row[('req_qty')],4); ?></p></td>
                            <td width="70" align="right"><p><? echo number_format($row[('req_amount')]/$row[('req_qty')],4); ?></p></td>
                            <td width="70" align="right"><p><? echo number_format($row[('amount')]/$order_price_per_dzn,4); ?></p></td>
                            <td width="70" align="right"><b><? echo number_format($row[('req_amount')],4); ?></b></td>
						</tr>
						<?
						$em++;$emb++;
						$tot_embro_amount+=$row[('amount')];
						$tot_embro_amount_pcs+=$row[('amount')]/$order_price_per_dzn;
						$tot_embro_req_amount+=$row[('req_amount')];
						$tot_emb_sourcing_req_amount+=$emb_sourcing_tot_amount;
						$tot_emb_sourcing_req_amount_bal+=$emb_sourcing_tot_amount_bal;
                    }
                    ?>
                    <tr class="rpt_bottom" style="font-weight:bold">
                        <td colspan="8"> <b>D-Total Embellishment Cost:</b></td>
                        <td  align="right"><b><? echo number_format($tot_embro_amount_pcs,4); ?></b></td>
                        <td  align="right"><b><? echo number_format($tot_embro_req_amount,4); ?></b></td>
                    </tr>
				</table>
				<?  
			}
		}
		?>
                 
        <table align="left" class="rpt_table" border="1" cellpadding="0" cellspacing="0"  width="750" style=" margin:5px; font-size:14px;" rules="all"> 
            <caption><b style="float:left">Others Components</b></caption>
            <tr>
                <td width="20">SL </td>
                <td title="450"><b>Others Components</b> </td>
                <td width="70"><b>Cost/<?=$unit_of_measurement[$orderUom]; ?></b></td>
                <td width="70"><b>Total Budget</b></td>
            </tr>
            <tbody> 
				<?
                //$em=1;$tot_embro_amount=$tot_embro_amount_pcs=$tot_embro_req_amount=0;$emb=1;
                $bgcolor="#E9F3FF";
                $bgcolor2="#FFFFFF";//currier_pre_cost_dzn
                
                // $tot_other_cost=0; 
                $tot_other_cost_first=$tot_other_cost+$comarcial+$inspection+$currier_pre_cost+$lab_test;
                
                $total_other_cost_dzn=$tot_other_cost_dzn+$comarcial_dzn+$inspection_dzn+$lab_test_dzn+$currier_pre_cost_dzn;
                $tot_other_cost_pcs=$tot_other_cost_dzn/$order_price_per_dzn;
                $tot_comarcial_dzn_pcs=$comarcial_dzn/$order_price_per_dzn;
                $tot_inspection_dzn_pcs=$inspection_dzn/$order_price_per_dzn;
                $currier_pre_cost_dzn_pcs=$currier_pre_cost_dzn/$order_price_per_dzn;
                $tot_lab_test_dzn_pcs=$lab_test_dzn/$order_price_per_dzn;
                
                $total_other_cost_pcs=$tot_other_cost_pcs+$tot_comarcial_dzn_pcs+$tot_lab_test_dzn_pcs+$tot_inspection_dzn_pcs+$currier_pre_cost_dzn_pcs;
                $tot_other_cost_req_amount=$tot_other_cost_first;
                
                if($zero_value==1)
                {
                    ?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                        <td width="20" align="center"><p>1</p></td>
                        <td width="450" align="" ><b>Test Charge</b></td> 
                        <td width="70" align="right"><p><? echo number_format($tot_lab_test_dzn_pcs,4); ?></p></td>
                        <td width="70" align="right"><p><? echo number_format($lab_test,4); ?></p></td>
                     </tr>
                    <?
                }
                else
                {
                    if($tot_lab_test_dzn_pcs>0)
                    {
                        ?>
                        <tr bgcolor="<? echo $bgcolor;?>">
                            <td width="20" align="center"><p>1</p></td>
                            <td width="450" align="" ><b>Test Charge</b></td> 
                            <td width="70" align="right"><p><?   echo number_format($tot_lab_test_dzn_pcs,4); ?></p></td>
                            <td width="70" align="right"><p><? echo number_format($lab_test,4); ?></p></td>
                        </tr>
                        <? 
                    }
                }
                
                if($zero_value==1)
                {
                    ?>
                    <tr bgcolor="<? echo $bgcolor2;?>">
                        <td width="20" align="center" ><p>2</p></td>
                        <td width="450"><b>Courier Charge</b></td> 
                        <td width="70" align="right"><p><?  echo number_format($currier_pre_cost_dzn_pcs,4); ?></p></td>
                        <td width="70" align="right"><p><? echo number_format($currier_pre_cost,4); ?></p></td>
                    </tr>
                    <?
                }
                else
                {  
                    if($currier_pre_cost_dzn_pcs>0)
                    {  ?>
                        <tr bgcolor="<? echo $bgcolor2;?>">
                            <td width="20" align="center" ><p>2</p></td>
                            <td width="450"><b>Courier Charge</b></td> 
                            <td width="70" align="right"><p><?  echo number_format($currier_pre_cost_dzn_pcs,4); ?></p></td>
                            <td width="70" align="right"><p><? echo number_format($currier_pre_cost,4); ?></p></td>
                        </tr> 
                        <? 
                    }
                }
                
                if($zero_value==1)
                {
                    ?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                        <td width="20" align="center" ><p>3</p></td>
                        <td width="450"><b>Inspection Charge</b></td> 
                        <td width="70" align="right"><p><? echo number_format($tot_inspection_dzn_pcs,4); ?></p></td>
                        <td width="70" align="right"><p><? echo number_format($inspection,4); ?></p></td>
                    </tr>
                    <?
                }
                else
                { 
                    if($tot_inspection_dzn_pcs>0)
                    {
                        ?>
                        <tr bgcolor="<? echo $bgcolor;?>">
                            <td width="20" align="center" ><p>3</p></td>
                            <td width="450"><b>Inspection Charge</b></td> 
                            <td width="70" align="right"><p><? echo number_format($tot_inspection_dzn_pcs,4); ?></p></td>
                            <td width="70" align="right"><p><? echo number_format($inspection,4); ?></p></td>
                        </tr>
                        <? 
                    }
                }
                
                if($zero_value==1)
                {
                    ?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                        <td width="20" align="center" ><p>4</p></td>
                        <td width="450"><b>Commercial Charge</b></td> 
                        <td width="70" align="right"><p><?  echo number_format($tot_comarcial_dzn_pcs,4); ?></p></td>
                        <td width="70" align="right"><p><? echo number_format($comarcial,4); ?></p></td>
                    </tr>
                    <?
                }
                else
                { 
                    if($tot_comarcial_dzn_pcs>0)
                    {
                        ?>
                        <tr bgcolor="<? echo $bgcolor;?>">
                            <td width="20" align="center" ><p>4</p></td>
                            <td width="450"><b>Commercial Charge</b></td> 
                            <td width="70" align="right"><p><?  echo number_format($tot_comarcial_dzn_pcs,4); ?></p></td>
                            <td width="70" align="right"><p><? echo number_format($comarcial,4); ?></p></td>
                        </tr>
                        <? 
                    }
                }
                ?>
                <tr bgcolor="<? echo $bgcolor2;?>">
                    <td width="20" align="center"><p>5</p></td>
                    <td width="450" title="Freight+Certif.Cost+Design Cost+Studio Cost+Deprec.&Amort.+Deprec.&Amort.+Interest+Income Tax">
                    <b>Others Charge</b></td> 
                    <td width="70" align="right"><p><? echo number_format($tot_other_cost_pcs,4); ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($tot_other_cost,4); ?></p></td>
                </tr>
            </tbody>
            <tfoot>
                <tr style="font-size:12px; background-color:#CCC">
                    <td colspan="2"> <b>E-Total Others Cost:</b></td>
                    <td  align="right"><b><? echo number_format($total_other_cost_pcs,4); ?></b></td>
                    <td  align="right"><b><? echo number_format($tot_other_cost_req_amount,4); ?></b></td>
                </tr>
            </tfoot>
        </table>
        <table align="left" class="rpt_table" border="1" cellpadding="0" cellspacing="0"  width="750" style=" margin:5px; font-size:14px;" rules="all"> 
            <caption><b style="float:left"> Commission Cost:</b></caption>
            <tr>
                <td  width="20">SL </td>
                <td width="450" title="">Commission Cost </td>
                <td  width="70">Cost/<?=$unit_of_measurement[$orderUom]; ?></td>
                <td  width="70">Total Budget</td>
            </tr>
            <tbody> 
				<?
                $tot_commission_amount=$commission_arr[1]['commi_amt']+$commission_arr[2]['commi_amt'];
                $tot_commission_amount_pcs=($commission_arr[1]['commi_amt']/$order_price_per_dzn)+($commission_arr[2]['commi_amt']/$order_price_per_dzn);
                $tot_commision_req_amount=$commission_arr[1]['commi_req_amt']+$commission_arr[2]['commi_req_amt'];
                ?>
                <tr bgcolor="<? echo $bgcolor;?>">
                    <td width="20" align="center"><p>1</p></td>
                    <td width="450" ><b>Local </b></td> 
                    <td width="70" align="right"><p><? echo number_format($commission_arr[2]['commi_amt']/$order_price_per_dzn,4); ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($commission_arr[2]['commi_req_amt'],4); ?></p></td>
                </tr>
                <tr bgcolor="<? echo $bgcolor2;?>">                     
                    <td width="20" align="center"><p>2</p></td>
                    <td width="450" ><b>Foreign</b></td> 
                    <td width="70" align="right"><p><? echo number_format($commission_arr[1]['commi_amt']/$order_price_per_dzn,4); ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($commission_arr[1]['commi_req_amt'],4); ?></p></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2"> <b>F-Total Commission Cost:</b></td>
                    <td  align="right"><b><? echo number_format($tot_commission_amount_pcs,4); ?></b></td>
                    <td  align="right"><b><? echo number_format($tot_commision_req_amount,4); ?></b></td>
                </tr>
                <tr bgcolor="<? echo $bgcolor;?>">
                    <td width="20" align="center"><p>1</p></td>
                    <td width="450"><b>G-Total CM Cost</b></td> 
                    <td width="70" align="right"><b><? $tot_cm_cost_pcs=$cm_cost_dzn/$order_price_per_dzn;echo number_format($tot_cm_cost_pcs,4); ?></b></td>
                    <td width="70" title="CM Cost*PO Qty " align="right"><b><? echo number_format($cmCost,4); ?></b></td>
                </tr>
                <tr bgcolor="<? echo $bgcolor;?>"> 
                    <td width="450" colspan="2"><p> <b>Gross FOB Value [A+B+C+D+E+F+G]</b></p></td> 
                    <?php //tot_sew_amount_pcs
                    $gross_fob_value_dzn=$tot_fab_amount+$trim_sew_fin_amt+$tot_wash_amount+$tot_embro_amount+$tot_other_cost_dzn+$tot_commission_amount+$cm_cost_dzn;
                    $gross_fob_value_pcs=$tot_fab_amount_pcs+$trim_sew_fin_amt_pcs+$tot_wash_amount_pcs+$tot_embro_amount_pcs+$tot_other_cost_pcs+$tot_commission_amount_pcs+$tot_cm_cost_pcs;                    
                    $gross_fob_value_req=$tot_fab_req_amount+$trims_tot_amount+$tot_wash_req_amount+$tot_embro_req_amount+$tot_other_cost_req_amount+$tot_commision_req_amount+$cmCost;
                    
                    //---fob value
                    $total_fob_gross_pcs=$tot_fab_amount_pcs+$trim_sew_fin_amt_pcs+$tot_wash_amount_pcs+$tot_embro_amount_pcs+$tot_other_cost_pcs+$tot_commission_amount_pcs+$tot_cm_cost_pcs;
                    $total_fob_gross_dzn=$tot_fab_amount+$trim_sew_fin_amt+$tot_sew_amount_pcs+$tot_wash_amount+$tot_embro_amount+$total_other_cost_dzn+$tot_commission_amount+$cm_cost_dzn;
                    $tot_gross_fob_pcs=$total_fob_gross_dzn/$order_price_per_dzn;
                    $gross_fob_value_dzn_without_commi=$total_fob_gross_dzn-$tot_commission_amount;
                    $gross_fob_value_pcs_without_commi=$tot_gross_fob_pcs-$tot_commission_amount_pcs;
                    $gross_fob_value_req_without_commi=$gross_fob_value_req-$tot_commision_req_amount;
                    ?>					
                    <td width="70" align="right"><b> <? echo number_format($tot_gross_fob_pcs,4); ?></b></td>
                    <td width="70" id="gross_fob_value_td" align="right" title="<? echo $gross_fob_value_req."=".$tot_fab_req_amount."+".$trims_tot_amount."+".$tot_wash_req_amount."+".$tot_embro_req_amount."+".$tot_other_cost_req_amount."+".$tot_commision_req_amount."+".$cmCost;?>"><b><? echo number_format($gross_fob_value_req,4); ?></b>  
                    <input type="hidden" id="gross_fob_value_th" width="70px" value="<? echo $gross_fob_value_req+$tot_margin_value; ?>"></td>
               
                </tr>
                <tr bgcolor="<? echo $bgcolor2;?>">
                    <td width="450" colspan="2"><b>Net FOB Value (Without Commission)</b></td> 
                    <td width="70" align="right"><b><? echo number_format($gross_fob_value_pcs_without_commi,4); ?> </b></td>
                    <td width="70" align="right"><b><? echo number_format($gross_fob_value_req_without_commi,4); ?> </b></td>
                </tr>
            </tfoot>
        </table>
        <script>
				fnc_fob_verify();
		</script>
        
        <div style="float:left" align="left">  <b> &nbsp;&nbsp;&nbsp;&nbsp; <? //echo $inserted_by;?> </b></div>
        <div style="clear:both"></div>
        <? 
		$cbo_company_name=str_replace("'","",$cbo_company_name);
		if ($cbo_template_id != '') {
			$template_id = " and a.template_id=$cbo_template_id ";
		}
		 
		$path=($path!='')?$path:"../../";
		//$inserted_by=return_field_value("inserted_by", "wo_pre_cost_mst", "job_no='".str_replace("'","",$txt_job_no)."'");
		$job_info=sql_select("SELECT id, inserted_by from wo_pre_cost_mst where status_active=1 and is_deleted=0 and job_no='".str_replace("'","",$txt_job_no)."'");
		foreach ($job_info as $row) {
			$inserted_by=$row[csf('inserted_by')];
			$job_id=$row[csf('id')];
		}
		
	   $signature_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='user_signature'",'master_tble_id','image_location');
		$appSql="select approved_by from approval_history where entry_form=46 and mst_id = $job_id and is_signing=1";
		$appSqlRes=sql_select($appSql);
		foreach($appSqlRes as $row){
			$userSignatureArr[$row[csf('approved_by')]]=$path.$signature_arr[$row[csf('approved_by')]];	
		}
		$userSignatureArr[$inserted_by]=$path.$signature_arr[$inserted_by];

		echo signature_table(218, $cbo_company_name, "1200px",$cbo_template_id,50,$inserted_by,$userSignatureArr);
		?>            
     </div>
     <div style="clear:both"></div>
    <?
	$mailBody=ob_get_contents();
	ob_clean();
	echo $mailBody;

	//Mail send------------------------------------------
	list($msil_address,$is_mail_send,$mail_body)=explode('**',$mail_data);
	if($is_mail_send==1){
	
		require_once('../../../mailer/class.phpmailer.php');
		require_once('../../../auto_mail/setting/mail_setting.php');
		
		$mailBody = preg_replace("/<img[^>]+\>/i", " ", $mailBody); 	
		$mailToArr=array();
		if($msil_address){$mailToArr[]=$msil_address;}
			
		$mailSql = "SELECT c.TEAM_LEADER_EMAIL, d.USER_EMAIL FROM wo_po_details_master  a,  wo_pre_cost_mst b, lib_marketing_team c, USER_PASSWD d WHERE a.job_no = b.job_no  AND a.TEAM_LEADER = c.id AND b.INSERTED_BY = d.id AND a.status_active = 1  AND a.job_no=$txt_job_no";
		//echo $mailSql;die;
		$mailSqlRes=sql_select($mailSql);
		foreach($mailSqlRes as $rows){
			if($rows['TEAM_LEADER_EMAIL']){$mailToArr[$rows['TEAM_LEADER_EMAIL']]=$rows['TEAM_LEADER_EMAIL'];}
			if($rows['USER_EMAIL']){$mailToArr[$rows['USER_EMAIL']]=$rows['USER_EMAIL'];}
		}


		$sql = "SELECT a.BRAND_IDS,a.BUYER_IDS,c.email_address as MAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id  and a.mail_item=b.MAIL_ITEM_MST and a.mail_item=78 and b.mail_user_setup_id=c.id and a.company_id =".$cbo_company_name."  and   A.IS_DELETED=0 and A.STATUS_ACTIVE=1  and b.IS_DELETED=0 and b.STATUS_ACTIVE=1  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
		$mail_sql=sql_select($sql);
		$receverMailArr=array();
		foreach($mail_sql as $row)
		{
			$mailToArr[$row['MAIL']]=$row['MAIL'];
		}
		
		
		$elcetronicSql = "SELECT a.BUYER_ID,a.SEQUENCE_NO,a.BYPASS,b.USER_EMAIL  from electronic_approval_setup a join user_passwd b on a.user_id=b.id where b.valid=1  AND a.page_id in(428,1717,2150) and a.company_id=$cbo_company_name order by a.SEQUENCE_NO"; //and a.
		
		$elcetronicSqlRes=sql_select($elcetronicSql);
		foreach($elcetronicSqlRes as $rows){
			
		
			if($rows['BUYER_ID']!=''){
				foreach(explode(',',$rows['BUYER_ID']) as $bi){
					if($rows['USER_EMAIL']!='' && $bi==$buyer_name_id){$mailToArr[$rows['USER_EMAIL']]=$rows['USER_EMAIL'];}
					if($rows['BYPASS']==2){break;}
				}
			}
			else{
				if($rows['USER_EMAIL']){$mailToArr[$rows['USER_EMAIL']]=$rows['USER_EMAIL'];}
				if($rows['BYPASS']==2){break;}
			}
		}
		
		//Un-approve request mail......................................................
		$user_id=$_SESSION['logic_erp']['user_id'];
		$job_id=return_field_value("id", "wo_pre_cost_mst", "job_no='".str_replace("'","",$txt_job_no)."'");
		$approved_no=return_field_value("MAX(approved_no) as approved_no","approval_history","entry_form=46 and mst_id=$job_id","approved_no")+1;
		$unapproved_request=return_field_value("APPROVAL_CAUSE","fabric_booking_approval_cause","entry_form=46 and user_id=$user_id and booking_id=$job_id and approval_type=2 and approval_no='$approved_no'");//page_id=$page_id and
		if($unapproved_request){
			$mailToArr=array();
			if($msil_address){$mailToArr[]=$msil_address;}
			$final_app_user_mail=return_field_value("USER_EMAIL","user_passwd","id in(select APPROVED_BY from APPROVAL_HISTORY where id in(select max(id) from APPROVAL_HISTORY where mst_id=$job_id and ENTRY_FORM=46 and CURRENT_APPROVAL_STATUS=1))");
			$mailToArr[]= $final_app_user_mail;
		}
		$mailBody=$mail_body."<br>".$unapproved_request."<br>".$mailBody;
		//......................................................Un-approve request mail;		
		$to=implode(',',$mailToArr);
		//echo $to;die;
		//Att file....
		$imgSql="select IMAGE_LOCATION,REAL_FILE_NAME from common_photo_library where is_deleted=0  and MASTER_TBLE_ID=$txt_job_no and file_type=1";
		$imgSqlResult=sql_select($imgSql);
		foreach($imgSqlResult as $rows){
			$att_file_arr[]='../../../'.$rows['IMAGE_LOCATION'].'**'.$rows['REAL_FILE_NAME'];
		}
		$subject="Pre Cost Sheet";
		$header=mailHeader();
		echo sendMailMailer( $to, $subject, $mailBody, $from_mail,$att_file_arr );
	}
	//------------------------------------End;
	exit();
}

if($action=="preCostRpt4")//Cost Rpt4 Btn Id 3 ISD-23-21618 for Renaissance
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if($txt_job_no=="") $job_noCond=''; else $job_noCond=" and a.job_no=".$txt_job_no."";

	$txt_costing_date=change_date_format(str_replace("'","",$txt_costing_date),'yyyy-mm-dd','-');
	if($cbo_company_name=="") $company_name=''; else $company_name=" and a.company_name=".$cbo_company_name."";
	if($cbo_buyer_name=="") $cbo_buyer_name=''; else $cbo_buyer_name=" and a.buyer_name=".$cbo_buyer_name."";
	if($txt_style_ref=="") $txt_style_ref=''; else $txt_style_ref=" and a.style_ref_no=".$txt_style_ref."";
	if($txt_costing_date=="") $txt_costing_date=''; else $txt_costing_date=" and b.costing_date='".$txt_costing_date."'";

	//array for display name
	
	$comp=return_library_array("select id, company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array("select id, buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array("select id, brand_name from lib_buyer_brand",'id','brand_name');
	$season_arr=return_library_array("select id, season_name from lib_buyer_season",'id','season_name');
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

	 $po_qty=0; $po_plun_cut_qty=0; $total_set_qnty=0; $job_in_orders = ''; $pulich_ship_date=''; $job_in_file = ''; $job_in_ref = ''; $postatus='';
	 $sql_po="select a.job_no, a.total_set_qnty, b.id, b.po_number, b.grouping, b.file_no, b.pub_shipment_date, b.is_confirmed, c.item_number_id, c.country_id, c.color_number_id, c.size_number_id, c.order_quantity, c.plan_cut_qnty from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id  and a.job_no =".$txt_job_no."   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by b.id";
	$sql_po_data=sql_select($sql_po);
	foreach($sql_po_data as $sql_po_row){
		$po_qty+=$sql_po_row[csf('order_quantity')];
		$po_plun_cut_qty+=$sql_po_row[csf('plan_cut_qnty')];
		$total_set_qnty=$sql_po_row[csf('total_set_qnty')];
		
		$job_in_orders .= $sql_po_row[csf('po_number')].", ";
		$pulich_ship_date = $sql_po_row[csf('pub_shipment_date')];
		$job_in_file .= $sql_po_row[csf('file_no')].",";
		$job_in_ref .= $sql_po_row[csf('grouping')].",";
		$postatus .= $order_status[$sql_po_row[csf('is_confirmed')]].",";
	}
	
	$job_in_orders = substr(trim($job_in_orders),0,-1);
	$job_ref=array_unique(explode(",",rtrim($job_in_ref,", ")));
	$job_file=array_unique(explode(",",rtrim($job_in_file,", ")));
	$postatus=array_unique(explode(",",rtrim($postatus,", ")));

	foreach ($job_ref as $ref){
		$ref_cond.=", ".$ref;
	}
	$file_con='';
	foreach ($job_file as $file){
		if($file_con=='') $file_cond=$file; else $file_cond.=", ".$file;
	}
	$postatusstrshow='';
	foreach ($postatus as $postatusstr){
		if($postatusstrshow=='') $postatusstrshow=$postatusstr; else $postatusstrshow.=", ".$postatusstr;
	}

	$gmtsitem_ratio_array=array();
	$gmtsitem_ratio_sql=sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details where job_no =".$txt_job_no."");// where job_no ='FAL-14-01157'
	foreach($gmtsitem_ratio_sql as $gmtsitem_ratio_sql_row){
		$gmtsitem_ratio_array[$gmtsitem_ratio_sql_row[csf('job_no')]][$gmtsitem_ratio_sql_row[csf('gmts_item_id')]]=$gmtsitem_ratio_sql_row[csf('set_item_ratio')];
	}
	
	$financial_para=array();
	$sql_std_para=sql_select("select interest_expense,income_tax,cost_per_minute,applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$cbo_company_name and status_active=1 and is_deleted=0 order by id");
	foreach($sql_std_para as $row )
	{
		$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
		$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
		$diff=datediff('d',$applying_period_date,$applying_period_to_date);
		for($j=0;$j<$diff;$j++)
		{
			//$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
			$date_all=add_date(str_replace("'","",$applying_period_date),$j);
			$newdate =change_date_format($date_all,'','',1);
			$financial_para[$newdate][interest_expense]=$row[csf('interest_expense')];
			$financial_para[$newdate][income_tax]=$row[csf('income_tax')];
			$financial_para[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
		}
	}
	//print_r($financial_para);
	
	$job_no=str_replace("'","",$txt_job_no);
	
	$condition= new condition();
	if(str_replace("'","",$txt_job_no) !=''){
		$condition->job_no("=$txt_job_no");
	}
	if(str_replace("'",'',$txt_po_breack_down_id) !=""){
		$condition->po_id("in($txt_po_breack_down_id)");
	}
	$condition->init();
	$fabric= new fabric($condition);
	
	$trim= new trims($condition);
	$emblishment= new emblishment($condition);
	$wash= new wash($condition);
	$other= new other($condition);
	
	$commercial= new commercial($condition);
	$commision= new commision($condition);
	
	$fabric_costing_arr=$fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
	$trims_costing_arr=$trim->getAmountArray_by_job();
	$emblishment_costing_arr=$emblishment->getAmountArray_by_job();
	$emblishment_costing_arr_wash=$wash->getAmountArray_by_job();
	
	$commercial_costing_arr=$commercial->getAmountArray_by_job();
	$commission_costing_arr=$commision->getAmountArray_by_job();
	$other_costing_arr=$other->getAmountArray_by_job();
	
	$ttl_cm_cost=$other_costing_arr[$job_no]['cm_cost'];
	
	$sql_dtls = "select job_no, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, commission, commission_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, common_oh, common_oh_percent, depr_amor_pre_cost, deffdlc_cost, deffdlc_percent, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche
	from wo_pre_cost_dtls where job_no=".$txt_job_no." and status_active=1 and is_deleted=0";
	$data_array_new=sql_select($sql_dtls);
	
	$summary_data=array();
	foreach($data_array_new as $row_new ){
		$summary_data[price_dzn]=$row_new[csf("price_dzn")];
		$summary_data[price_dzn_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("price_dzn")];
		$summary_data[commission]=$row_new[csf("commission")];
		$summary_data[trims_cost]=$row_new[csf("trims_cost")];
		$summary_data[emb_cost]=$row_new[csf("embel_cost")];

		$summary_data[lab_test]=$row_new[csf("lab_test")];
		$summary_data[lab_test_job]=$other_costing_arr[$row_new[csf("job_no")]]['lab_test'];

		$summary_data[inspection]=$row_new[csf("inspection")];
		$summary_data[inspection_job]=$other_costing_arr[$row_new[csf("job_no")]]['inspection'];

		$summary_data[freight]=$row_new[csf("freight")];
		$summary_data[freight_job]=$other_costing_arr[$row_new[csf("job_no")]]['freight'];

		$summary_data[currier_pre_cost]=$row_new[csf("currier_pre_cost")];
		$summary_data[currier_pre_cost_job]=$other_costing_arr[$row_new[csf("job_no")]]['currier_pre_cost'];

		$summary_data[certificate_pre_cost]=$row_new[csf("certificate_pre_cost")];
		$summary_data[certificate_pre_cost_job]=$other_costing_arr[$row_new[csf("job_no")]]['certificate_pre_cost'];
		$summary_data[wash_cost]=$row_new[csf("wash_cost")];

		$summary_data[OtherDirectExpenses]=$row_new[csf("lab_test")]+$row_new[csf("inspection")]+$row_new[csf("freight")]+$row_new[csf("currier_pre_cost")]+$row_new[csf("certificate_pre_cost")]+$row_new[csf("wash_cost")];
		$summary_data[OtherDirectExpenses_job]=$summary_data[lab_test_job]+$summary_data[inspection_job]+$summary_data[freight_job]+$summary_data[currier_pre_cost_job]+$summary_data[certificate_pre_cost_job];

		$summary_data[cm_cost]=$row_new[csf("cm_cost")];
		$summary_data[cm_cost_job]=$other_costing_arr[$row_new[csf("job_no")]]['cm_cost'];
		$summary_data[comm_cost]=$row_new[csf("comm_cost")];
		$summary_data[common_oh]=$row_new[csf("common_oh")];
		$summary_data[common_oh_job]=$other_costing_arr[$row_new[csf("job_no")]]['common_oh'];
		$summary_data[depr_amor_pre_cost]=$row_new[csf("depr_amor_pre_cost")];
		$summary_data[depr_amor_pre_cost_job]=$other_costing_arr[$row_new[csf("job_no")]]['depr_amor_pre_cost'];
		$summary_data[margindzn]=$row_new[csf("margin_dzn")];
		$summary_data[fabric_percent]=$row_new[csf("fabric_cost_percent")];
		$summary_data[trims_percent]=$row_new[csf("trims_cost_percent")];
		$summary_data[wash_percent]=$row_new[csf("wash_cost_percent")];
		$summary_data[emb_percent]=$row_new[csf("embel_cost_percent")];
		$summary_data[commercial_percent]=$row_new[csf("comm_cost_percent")];
		$summary_data[currier_percent]=$row_new[csf("currier_percent")];
		$summary_data[commission_percent]=$row_new[csf("commission_percent")];
		$summary_data[lab_test_percent]=$row_new[csf("lab_test_percent")];
		$summary_data[freight_percent]=$row_new[csf("freight_percent")];
		$summary_data[margin_dzn_percent]=$row_new[csf("margin_dzn_percent")];
		$summary_data[cm_cost_percent]=$row_new[csf("cm_cost_percent")];
		$summary_data[inspection_percent]=$row_new[csf("inspection_percent")];
		$summary_data[deffdlc_percent]=$row_new[csf("deffdlc_percent")];
		$summary_data[common_oh_percent]=$row_new[csf("common_oh_percent")];
	}
	unset($data_array_new);

	$fab_knit_req_kg_avg=0; $fab_woven_req_yds_avg=0;
	
    $sql = "select a.job_no, a.company_name, a.buyer_name, a.brand_id, a.season_buyer_wise, a.style_ref_no, a.quotation_id, a.gmts_item_id, a.order_uom, a.job_quantity, a.avg_unit_price, b.costing_per, b.budget_minute, b.costing_date, b.exchange_rate, b.incoterm, b.sew_smv, b.cut_smv, b.sew_effi_percent, b.cut_effi_percent, b.approved, c.fab_knit_req_kg, c.fab_knit_fin_req_kg, c.fab_woven_req_yds, c.fab_woven_fin_req_yds, c.fab_yarn_req_kg from wo_po_details_master a, wo_pre_cost_mst b left join wo_pre_cost_sum_dtls c on b.job_no=c.job_no where a.job_no=b.job_no and a.status_active=1 $job_noCond $company_name $cbo_buyer_name $txt_style_ref order by a.job_no";
	$data_array=sql_select($sql);
	$uom=""; $sew_smv=0; $cut_smv=0; $sew_effi_percent=0; $cut_effi_percent=0;  $cpmCal=0; $poQty=0;
	foreach ($data_array as $row){
		$order_price_per_dzn=0; $order_job_qnty=0; $avg_unit_price=0;
		$sew_smv=$row[csf("sew_smv")];
	    $cut_smv=$row[csf("cut_smv")];
	    $sew_effi_percent=$row[csf("sew_effi_percent")];
	    $cut_effi_percent=$row[csf("cut_effi_percent")];
		$order_values = $row[csf("job_quantity")]*$row[csf("avg_unit_price")];
		$pre_costing_date=change_date_format($row[csf('costing_date')],'','',1);
		$poQty=$row[csf("job_quantity")];
		
		if($row[csf("costing_per")]==1){
            $order_price_per_dzn=12;
            $costing_for=" DZN";
        }
        else if($row[csf("costing_per")]==2){
            $order_price_per_dzn=1;
            $costing_for=" PCS";
        }
        else if($row[csf("costing_per")]==3){
            $order_price_per_dzn=24;
            $costing_for=" 2 DZN";
        }
        else if($row[csf("costing_per")]==4){
            $order_price_per_dzn=36;
            $costing_for=" 3 DZN";
        }
        else if($row[csf("costing_per")]==5){
            $order_price_per_dzn=48;
            $costing_for=" 4 DZN";
        }
		?>
        <style>
		.vl {
		  border-left: 3px solid green;
		  position: absolute;
		  left: 50%;
		  top: 0;
		}
		</style>
        <table style="width:930px">
        	<tr>
            	<td colspan="3" align="left" style="font-size:14px; font-family:'Calibri Light';">
                	<b>COST SHEET</b>
                    <br />
                    <b style="font-size:14px; font-family:'Calibri Light'; background-color:#FFFFCC">Cost Sheet Revised No: <?
						$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_pre_cost_mst a, approval_history b where a.id=b.mst_id and a.job_no=".$txt_job_no." and b.entry_form=46");
							if(isset($nameArray_approved[0][csf('approved_no')]) ){
								echo ($nameArray_approved[0][csf('approved_no')]-1)*1;
							}
							else{ echo 0; }
						 ?>
					</b>
                </td>
			    <?
                $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id=$cbo_company_name","image_location");
                $path=($path)?$path:'../../';
                ?>
                <td colspan="3" align="right"><img src='<? echo $path.$image_location; ?>' height='40' width='100' /><br /><?=date("l\, jS \of F\, Y"); ?></td>
           </tr>
       </table>

       <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:930px" rules="all">
            <tr>
                <td width="120" style="font-size:14px; font-family:'Calibri Light';"><b>Company</b></td>
                <td width="150" style="font-size:14px; font-family:'Calibri Light';"><b><? echo $comp[$row[csf("company_name")]]; ?></b></td>
                <td width="130" style="font-size:14px; font-family:'Calibri Light';" ><b>SMV/<?=$costing_for; ?></b></td>
                <td width="150" style="font-size:14px; font-family:'Calibri Light';" colspan="2"><b><? echo $sew_smv; ?></b></td>
                <td rowspan="12">
                	<?
					$fab_purchase_knit=array_sum($fabric_costing_arr['knit']['grey'][$job_no]);
					$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['grey'][$job_no]);
					
					$fabricCost=$fab_purchase_knit+$fab_purchase_woven;
					//echo $financial_para[$pre_costing_date][cost_per_minute].'=='.$row[csf("exchange_rate")].'=='.($sew_effi_percent/100);
					$cpmCal=($financial_para[$pre_costing_date][cost_per_minute]/$row[csf("exchange_rate")])/($sew_effi_percent/100);
					$totMaterialCost=$fabricCost+$trims_costing_arr[$job_no]+$emblishment_costing_arr_wash[$job_no]+$emblishment_costing_arr[$job_no];
					$otherCost=$commercial_costing_arr[$job_no]+$other_costing_arr[$job_no]['currier_pre_cost']+$commission_costing_arr[$job_no]+$other_costing_arr[$job_no]['lab_test']+$other_costing_arr[$job_no]['inspection']+$other_costing_arr[$job_no]['common_oh']+$other_costing_arr[$job_no]['deffdlc_cost']+$other_costing_arr[$job_no]['freight'];
					
					$breakevencm=$cpmCal*$sew_smv*$poQty;
					$calCM=$order_values-($totMaterialCost+$otherCost);
					$cmPcs=$calCM/$poQty;
					$totalMargin=$calCM-$breakevencm;
					$marginPcs=$totalMargin/$poQty;
					// image show here  -------------------------------------------
					$sqlData = "select id,master_tble_id,image_location from common_photo_library where master_tble_id=".$txt_job_no."";
					$data_array_img=sql_select($sqlData);
					$path=($path)?$path:'../../';
					?>
					<div>
					<? foreach($data_array_img as $inf){ ?>
						<img  src='<? echo $path.$inf[csf("image_location")]; ?>' border="1" height='200px' width='150px' />
					<?  } ?>
					</div>
                </td>
            </tr>
            <tr>
            	<td style="font-size:14px; font-family:'Calibri Light';"><b>Job Number</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b><?=$row[csf("job_no")]; ?></b></td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b>Planned Efficiency</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';" colspan="2"><b><?=$sew_effi_percent; ?>%</b></td>
            </tr>
            <tr>
            	<td style="font-size:14px; font-family:'Calibri Light';"><b>Buyer</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b><?=$buyer_arr[$row[csf("buyer_name")]]; ?></b></td>
                <td style="font-size:14px; font-family:'Calibri Light';background-color:#FFC"><b>Margin/<?=$costing_for; ?></b></td>
                <td style="font-size:14px; font-family:'Calibri Light';background-color:#FFC" width="75"><b><?=fn_number_format($marginPcs,3); ?></b></td>
                <td style="font-size:14px; font-family:'Calibri Light';background-color:#FFC" width="75" align="center"><b><?=fn_number_format((($totalMargin/$order_values)*100),2).' %';//$summary_data[margin_dzn_percent].' %'; ?></b></td>
            </tr>
            <tr>
            	<td style="font-size:14px; font-family:'Calibri Light';"><b>Brand</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b><?=$brand_arr[$row[csf("brand_id")]]; ?></b></td>
                <td style="font-size:14px; font-family:'Calibri Light';background-color:#FFC" ><b>CM/<?=$costing_for; ?></b></td>
                <td style="font-size:14px; font-family:'Calibri Light';background-color:#FFC" width="75"><b><?=fn_number_format($cmPcs,3);//$summary_data[cm_cost]; ?></b></td>
                <td style="font-size:14px; font-family:'Calibri Light';background-color:#FFC" width="75" align="center"><b><?=fn_number_format((($calCM/$order_values)*100),2).' %';//$summary_data[cm_cost_percent].' %'; ?></b></td>
            </tr>
            <tr>
            	<td style="font-size:14px; font-family:'Calibri Light';"><b>Costed Date</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b><?=change_date_format($pre_costing_date); ?></b></td>
                <td style="font-size:14px; font-family:'Calibri Light';background-color:#FFC"><b>EPM</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';background-color:#FFC" colspan="2"><b><? $epm=$cmPcs/$sew_smv; echo fn_number_format($epm,3); ?></b></td>
            </tr>
            <tr>
            	<td style="font-size:14px; font-family:'Calibri Light';"><b>Season</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b><?=$season_arr[$row[csf("season_buyer_wise")]]; ?></b></td>
                <td style="font-size:14px; font-family:'Calibri Light';background-color:#FFC"><b>CPM</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';background-color:#FFC" colspan="2" title="CPM (USD)/Planned Efficiency"><b><?=fn_number_format($cpmCal,3); ?></b></td>
            </tr>
            <tr>
            	<td style="font-size:14px; font-family:'Calibri Light';"><b>Garments Item</b></td>
                <?
                    $grmnt_items = "";
                    if($garments_item[$row[csf("gmts_item_id")]]=="")
                    {

                        $grmts_sql = sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details where job_no=$txt_job_no");
                        foreach($grmts_sql as $key=>$val){
                            $grmnt_items .=$garments_item[$val[csf("gmts_item_id")]].", ";
                        }
                        $grmnt_items = substr_replace($grmnt_items,"",-1,1);
                    }else{
                        $grmnt_items = $garments_item[$row[csf("gmts_item_id")]];
                    }
					
					$appStatus="No"; $appStatusTdColor="#FF7377";
					if($row[csf("approved")]==1 || $row[csf("approved")]==3) { $appStatus="Yes";  $appStatusTdColor="#90EE90"; }
                ?>
                <td style="font-size:14px; font-family:'Calibri Light';"><b><?=$grmnt_items; ?></b></td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b>Order Qty</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';" colspan="2"><b><?=$row[csf("job_quantity")]." ". $unit_of_measurement[$row[csf("order_uom")]]; ?></b></td>
            </tr>
            <tr>
            	<td style="font-size:14px; font-family:'Calibri Light';"><b>Buyer Style Ref. No</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b><?=$row[csf("style_ref_no")]; ?></b></td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b>Plan Qty</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';" colspan="2"><b><?=$po_plun_cut_qty/$total_set_qnty." ". $unit_of_measurement[$row[csf("order_uom")]];?></b></td>
            </tr>
            <tr>
            	<td style="font-size:14px; font-family:'Calibri Light';"><b>Costing Per</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b><?=$costing_per[$row[csf("costing_per")]]; ?></b></td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b>Price Per Piece</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';" colspan="2"><b><?=$row[csf("avg_unit_price")]; ?> USD</b></td>
            </tr>
            <tr>
            	<td style="font-size:14px; font-family:'Calibri Light';"><b>Order Status</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b><?=$postatusstrshow; ?></b></td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b>Total Value</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';" colspan="2"><b><?=fn_number_format($order_values,3); ?> USD</b></td>
                
            </tr>
             <tr>
            	<td style="font-size:14px; font-family:'Calibri Light';"><b>Terms</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b><?=$incoterm[$row[csf("incoterm")]]; ?></b></td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b>Cost Sheet Approved</b></td>
                <td style="font-size:14px; font-family:'Calibri Light'; background-color:<?=$appStatusTdColor; ?>" colspan="2"><b><?=$appStatus; ?></b></td>
            </tr>
            <tr>
                <td style="font-size:14px; font-family:'Calibri Light';">&nbsp;</td>
                <td style="font-size:14px; font-family:'Calibri Light';">&nbsp;</td>
                <td style="font-size:14px; font-family:'Calibri Light';"><b>Commercial Cost %</b></td>
                <td style="font-size:14px; font-family:'Calibri Light';" colspan="2"><b><?=$summary_data[commercial_percent]; ?></b></td>
            </tr>
        </table>
        <?
        $order_job_qnty=$row[csf("job_quantity")];
        $avg_unit_price=$row[csf("avg_unit_price")];
	}//end first foearch
	//start	all summary report here -------------------------------------------
	
	
	?>
    <br />
    <div style="border:2px solid black; width:930px">
    <table cellpadding="1" cellspacing="1" style="width:920px">
    	<tr>
        	<td width="306" valign="top">
            	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:305px" rules="all">
                	<tr>
                    	<td style="font-size:14px; font-family:'Calibri Light';" colspan="3" align="center"><b>Materials Cost Summary</b></td>
                    </tr>
                	<tr>
                        <td width="120" style="font-size:14px; font-family:'Calibri Light';" align="center"><b>Item</b></td>
                        <td width="100" style="font-size:14px; font-family:'Calibri Light';" align="center"><b>Value</b></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><b>%</b></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';">Fabric</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <?=fn_number_format($fabricCost,3); ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=fn_number_format((($fabricCost/$order_values)*100),2); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';">Trims</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <?=fn_number_format($trims_costing_arr[$job_no],3); ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=fn_number_format((($trims_costing_arr[$job_no]/$order_values)*100),2); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';">Wash</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <?=fn_number_format($emblishment_costing_arr_wash[$job_no],3); ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=fn_number_format((($emblishment_costing_arr_wash[$job_no]/$order_values)*100),2); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';">Embellishment</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <?=fn_number_format($emblishment_costing_arr[$job_no],3); ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=fn_number_format((($emblishment_costing_arr[$job_no]/$order_values)*100),2); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';"><b>&nbsp;</b></td>
                        <td style="font-size:14px; font-family:'Calibri Light';"><b>&nbsp;</b></td>
                        <td style="font-size:14px; font-family:'Calibri Light';"><b>&nbsp;</b></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="right"><b>Total :</b></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><b><?  echo fn_number_format($totMaterialCost,3); ?></b></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><b><?=fn_number_format((($totMaterialCost/$order_values)*100),2); ?></b></td>
                    </tr>
                </table>
            </td>
            <td width="3">&nbsp;</td>
        	<td width="306" valign="top">
            	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:305px" rules="all">
                	<tr>
                    	<td style="font-size:14px; font-family:'Calibri Light';" colspan="3" align="center"><b>Other Costs Summary</b></td>
                    </tr>
                	<tr>
                        <td width="120" style="font-size:14px; font-family:'Calibri Light';" align="center"><b>Item</b></td>
                        <td width="100" style="font-size:14px; font-family:'Calibri Light';" align="center"><b>Value</b></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><b>%</b></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" >Commercial Cost</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <?=fn_number_format($commercial_costing_arr[$job_no],3); ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=fn_number_format((($commercial_costing_arr[$job_no]/$order_values)*100),2); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" >Courier Cost</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <?=fn_number_format($other_costing_arr[$job_no]['currier_pre_cost'],3); ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=fn_number_format((($other_costing_arr[$job_no]['currier_pre_cost']/$order_values)*100),2); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" >Commission</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <?=fn_number_format($commission_costing_arr[$job_no],3); ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=fn_number_format((($commission_costing_arr[$job_no]/$order_values)*100),2); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" >Lab Test</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <?=fn_number_format($other_costing_arr[$job_no]['lab_test'],3); ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=fn_number_format((($other_costing_arr[$job_no]['lab_test']/$order_values)*100),2); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" >Inspection Cost</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <?=fn_number_format($other_costing_arr[$job_no]['inspection'],3); ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=fn_number_format((($other_costing_arr[$job_no]['inspection']/$order_values)*100),2); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" >Opt Exp Cost</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <?=fn_number_format($other_costing_arr[$job_no]['common_oh'],3); ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=fn_number_format((($other_costing_arr[$job_no]['common_oh']/$order_values)*100),2); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" >Deffd. LC/DC Cost</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <?=fn_number_format($other_costing_arr[$job_no]['deffdlc_cost'],3); ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=fn_number_format((($other_costing_arr[$job_no]['deffdlc_cost']/$order_values)*100),2); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" >Freight Cost</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <?=fn_number_format($other_costing_arr[$job_no]['freight'],3); ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=fn_number_format((($other_costing_arr[$job_no]['freight']/$order_values)*100),2); ?></td>
                    </tr>
                   <!-- <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" >CM Cost</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <? //=fn_number_format($other_costing_arr[$job_no]['cm_cost'],3); ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><? //=fn_number_format((($other_costing_arr[$job_no]['cm_cost']/$order_values)*100),2); ?></td>
                    </tr>-->
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="right"><b>Total :</b></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><b><?  echo fn_number_format($otherCost,3); ?></b></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><b><?=fn_number_format((($otherCost/$order_values)*100),2); ?></b></td>
                    </tr>
                </table>
            </td>
            <td width="3">&nbsp;</td>
            <td valign="top">
            	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:296px" rules="all">
                	<tr>
                    	<td style="font-size:14px; font-family:'Calibri Light';" colspan="3" align="center"><b>Order Profitability Summary</b></td>
                    </tr>
                    <tr>
                        <td width="150" style="font-size:14px; font-family:'Calibri Light';" >Total FOB Value</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <?=fn_number_format($order_values,3); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" >Materials Costs</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <?=fn_number_format($totMaterialCost,3); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" >Other Costs</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <?=fn_number_format($otherCost,3); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" >CM</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center">$ <?  echo fn_number_format($calCM,3); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';">Breakeven CM :</td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center" title="CPM*SMV*PO QTY">$<? echo fn_number_format($breakevencm,3); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="right"><b>Margin :</b></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><b><? echo fn_number_format($totalMargin,3); ?></b></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    </div>
   
	<!--Fabric =====================-->
    <? if($zero_value==1) { ?>
    <div style="margin-top:15px">
    	<label style="font-size:14px; font-family:'Calibri Light'; background-color:#CCCCCC"><b>Fabric Cost Details</b></label>
    	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:930px;" rules="all">
        	<tbody>
                <tr style="font-weight:bold; background-color:#FFC" align="center">
                    <td width="82" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Fabric Nature</td>
                    <td width="80" style="font-size:14px; font-family:'Calibri Light';">Fabric Part</td>
                    <td width="200" style="font-size:14px; font-family:'Calibri Light';">Fabric Description</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">UOM</td>
                    <td width="60" style="font-size:14px; font-family:'Calibri Light';">YY</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Wastage %</td>
                    <td width="60" style="font-size:14px; font-family:'Calibri Light';">Total YY</td>
                    
                    <td width="80" style="font-size:14px; font-family:'Calibri Light';">Req. Qty</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Rate</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Cost/<?=$costing_for; ?></td>
                    <td width="80" style="font-size:14px; font-family:'Calibri Light';">Total Value</td>
                    <td style="font-size:14px; font-family:'Calibri Light';">%</td>
                </tr>
            
			<?
            $fabric_req_qty_arr=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
            $fabric_req_amt_arr=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
            $sql_fabric = "select ID, JOB_NO, BODY_PART_ID, FAB_NATURE_ID, COLOR_TYPE_ID, FABRIC_DESCRIPTION, UOM, AVG_CONS, AVG_CONS_YARN, FABRIC_SOURCE, GSM_WEIGHT, RATE, AMOUNT, AVG_FINISH_CONS, AVG_PROCESS_LOSS from wo_pre_cost_fabric_cost_dtls where job_no=".$txt_job_no." and is_deleted=0 and status_active=1";
            $sql_fabricArr=sql_select($sql_fabric); $i=1; $fabamtTotDzn=$fabReqAmttotal=$fabPerTotal=0;
            foreach($sql_fabricArr as $frow)
            {
                $rowFabReqQty=$rowFabReqAmt=$fabric_per=0;
                $rowFabReqQty=$fabric_req_qty_arr['knit']['grey'][$frow["ID"]][$frow["UOM"]]+$fabric_req_qty_arr['woven']['grey'][$frow["ID"]][$frow["UOM"]];
                $rowFabReqAmt=$fabric_req_amt_arr['knit']['grey'][$frow["ID"]][$frow["UOM"]]+$fabric_req_amt_arr['woven']['grey'][$frow["ID"]][$frow["UOM"]];
                
                $fabric_per=($rowFabReqAmt/$order_values)*100;
                ?>
                <tr>
                    <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$item_category[$frow['FAB_NATURE_ID']]; ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$body_part[$frow["BODY_PART_ID"]]; ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$frow["FABRIC_DESCRIPTION"]; ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$unit_of_measurement[$frow["UOM"]]; ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($frow["AVG_FINISH_CONS"],3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($frow["AVG_PROCESS_LOSS"],2); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($frow["AVG_CONS"],3); ?></td>
                    
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowFabReqQty,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($frow["RATE"],3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($frow["AMOUNT"],3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowFabReqAmt,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($fabric_per,2); ?></td>
                </tr>
                <?	
				 $fabamtTotDzn+=$frow["AMOUNT"];
				 $fabReqAmttotal+=$rowFabReqAmt;
				 $fabPerTotal+=$fabric_per;
            }
            ?>
            </tbody>
            <tfoot>
            	<tr style="background-color:#CCFFFF">
                    <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all" colspan="7" align="right"><b>Total Fabric Cost</b></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right">&nbsp;</td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right">&nbsp;</td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($fabamtTotDzn,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($fabReqAmttotal,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($fabPerTotal,2); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <? } else { 
		if(($fabricCost*1)>0)
		{
			?>
            <div style="margin-top:15px">
                <label style="font-size:14px; font-family:'Calibri Light'; background-color:#CCCCCC"><b>Fabric Cost Details</b></label>
                <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:930px;" rules="all">
                    <thead>
                        <tr style="font-weight:bold; background-color:#FFC" align="center">
                            <th width="82" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Fabric Nature</th>
                            <th width="80" style="font-size:14px; font-family:'Calibri Light';">Fabric Part</th>
                            <th width="200" style="font-size:14px; font-family:'Calibri Light';">Fabric Description</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">UOM</th>
                            <th width="60" style="font-size:14px; font-family:'Calibri Light';">YY</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">Wastage %</th>
                            <th width="60" style="font-size:14px; font-family:'Calibri Light';">Total YY</th>
                            
                            <th width="80" style="font-size:14px; font-family:'Calibri Light';">Req. Qty</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">Rate</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">Cost/<?=$costing_for; ?></th>
                            <th width="80" style="font-size:14px; font-family:'Calibri Light';">Total Value</th>
                            <th style="font-size:14px; font-family:'Calibri Light';">%</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $fabric_req_qty_arr=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
                    $fabric_req_amt_arr=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
                    $sql_fabric = "select ID, JOB_NO, BODY_PART_ID, FAB_NATURE_ID, COLOR_TYPE_ID, FABRIC_DESCRIPTION, UOM, AVG_CONS, AVG_CONS_YARN, FABRIC_SOURCE, GSM_WEIGHT, RATE, AMOUNT, AVG_FINISH_CONS, AVG_PROCESS_LOSS from wo_pre_cost_fabric_cost_dtls where job_no=".$txt_job_no." and is_deleted=0 and status_active=1";
                    $sql_fabricArr=sql_select($sql_fabric); $i=1; $fabamtTotDzn=$fabReqAmttotal=$fabPerTotal=0;
                    foreach($sql_fabricArr as $frow)
                    {
                        $rowFabReqQty=$rowFabReqAmt=$fabric_per=0;
                        $rowFabReqQty=$fabric_req_qty_arr['knit']['grey'][$frow["ID"]][$frow["UOM"]]+$fabric_req_qty_arr['woven']['grey'][$frow["ID"]][$frow["UOM"]];
                        $rowFabReqAmt=$fabric_req_amt_arr['knit']['grey'][$frow["ID"]][$frow["UOM"]]+$fabric_req_amt_arr['woven']['grey'][$frow["ID"]][$frow["UOM"]];
                        
                        $fabric_per=($rowFabReqAmt/$order_values)*100;
                        ?>
                        <tr>
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$item_category[$frow['FAB_NATURE_ID']]; ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$body_part[$frow["BODY_PART_ID"]]; ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$frow["FABRIC_DESCRIPTION"]; ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$unit_of_measurement[$frow["UOM"]]; ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($frow["AVG_FINISH_CONS"],3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($frow["AVG_PROCESS_LOSS"],2); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($frow["AVG_CONS"],3); ?></td>
                            
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowFabReqQty,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($frow["RATE"],3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($frow["AMOUNT"],3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowFabReqAmt,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($fabric_per,2); ?></td>
                        </tr>
                        <?	
                         $fabamtTotDzn+=$frow["AMOUNT"];
                         $fabReqAmttotal+=$rowFabReqAmt;
                         $fabPerTotal+=$fabric_per;
                    }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color:#CCFFFF">
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all" colspan="7" align="right"><b>Total Fabric Cost</b></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right">&nbsp;</td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right">&nbsp;</td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($fabamtTotDzn,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($fabReqAmttotal,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($fabPerTotal,2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?
		}
	}
	?>
    
    <!--Trims =====================-->
    
    <?
	$sql_trim = "select a.ID, a.JOB_NO, a.TRIM_GROUP, a.DESCRIPTION, a.CONS_UOM, a.TOT_CONS, a.EX_PER, a.CONS_DZN_GMTS, a.RATE, a.AMOUNT, b.ITEM_NAME, b.TRIM_TYPE from wo_pre_cost_trim_cost_dtls a, lib_item_group b where a.TRIM_GROUP=b.id and a.job_no=".$txt_job_no." and a.status_active=1 and a.is_deleted=0 order by b.TRIM_TYPE, a.seq Asc";
	
	$data_array_trim=sql_select($sql_trim);
	
	$trim_qty_arr=$trim->getQtyArray_by_precostdtlsid();
	$trim_amount_arr=$trim->getAmountArray_precostdtlsid(); $trimsDataArr=array();
	foreach($data_array_trim as $trow)
	{
		$trimsDataArr[$trow["TRIM_TYPE"]][$trow["ID"]]['ITEM_NAME']=$trow["ITEM_NAME"];
		$trimsDataArr[$trow["TRIM_TYPE"]][$trow["ID"]]['DESCRIPTION']=$trow["DESCRIPTION"];
		$trimsDataArr[$trow["TRIM_TYPE"]][$trow["ID"]]['CONS_UOM']=$trow["CONS_UOM"];
		$trimsDataArr[$trow["TRIM_TYPE"]][$trow["ID"]]['NET_CONS']=$trow["TOT_CONS"];
		$trimsDataArr[$trow["TRIM_TYPE"]][$trow["ID"]]['EX_PER']=$trow["EX_PER"];
		$trimsDataArr[$trow["TRIM_TYPE"]][$trow["ID"]]['GCONS']=$trow["CONS_DZN_GMTS"];
		$trimsDataArr[$trow["TRIM_TYPE"]][$trow["ID"]]['RATE']=$trow["RATE"];
		$trimsDataArr[$trow["TRIM_TYPE"]][$trow["ID"]]['AMOUNT']=$trow["AMOUNT"];
	}
	unset($data_array_trim);
	if($zero_value==1) { 
	?>
    <div style="margin-top:15px">
    	<label style="font-size:14px; font-family:'Calibri Light'; background-color:#CCCCCC"><b>Trims Cost Details</b></label>
    	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:930px;" rules="all">
        	<tbody>
                <tr style="font-weight:bold;background-color:#FFC" align="center">
                    <td width="130" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Item Name</td>
                    <td width="230" style="font-size:14px; font-family:'Calibri Light';">Item Description</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">UOM</td>
                    <td width="60" style="font-size:14px; font-family:'Calibri Light';">YY</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Wastage %</td>
                    <td width="60" style="font-size:14px; font-family:'Calibri Light';">Total YY</td>
                    <td width="80" style="font-size:14px; font-family:'Calibri Light';">Req. Qty</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Rate</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Cost /<?=$costing_for; ?></td>
                    <td width="80" style="font-size:14px; font-family:'Calibri Light';">Total Value</td>
                    <td style="font-size:14px; font-family:'Calibri Light';">%</td>
                </tr>
            
			<?
            
			$i=1; $trimamtTotDzn=$trimReqAmttotal=$trimPerTotal=0;
            foreach($trimsDataArr as $trimtype=>$trimtypedata)
            {
				?>
                <tr>
                    <td colspan="11" style="font-size:14px; font-family:'Calibri Light';word-break:break-all; background-color:#FFC"><b><?=$trim_type[$trimtype]; ?> Trims Details</b></td>
                </tr>
                <?
				$typeTrimsAmtDzn=$typeTrimsAmt=$typeTrimsAmtPer=0;
				foreach($trimtypedata as $trimid=>$trimdata)
				{
					$rowTrimReqQty=$rowTrimReqAmt=$trim_per=0;
					$rowTrimReqQty=$trim_qty_arr[$trimid];
					$rowTrimReqAmt=$trim_amount_arr[$trimid];
					
					$trim_per=($rowTrimReqAmt/$order_values)*100;
					?>
					<tr>
						<td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$trimdata['ITEM_NAME']; ?></td>
						<td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$trimdata['DESCRIPTION']; ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$unit_of_measurement[$trimdata['CONS_UOM']]; ?></td>
						<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimdata["NET_CONS"],3); ?></td>
						<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimdata["EX_PER"],2); ?></td>
						<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimdata["GCONS"],3); ?></td>
						
						<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowTrimReqQty,3); ?></td>
						<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimdata["RATE"],3); ?></td>
						<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimdata["AMOUNT"],3); ?></td>
						<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowTrimReqAmt,3); ?></td>
						<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trim_per,2); ?></td>
					</tr>
					<?
						$typeTrimsAmtDzn+=$trimdata["AMOUNT"];
						$typeTrimsAmt+=$rowTrimReqAmt;
						$typeTrimsAmtPer+=$trim_per;
						
						$trimamtTotDzn+=$trimdata["AMOUNT"];
						$trimReqAmttotal+=$rowTrimReqAmt;
						$trimPerTotal+=$trim_per;
				}
				 ?>
                <tr style="background-color:#CCCCCC">
                    <td colspan="8" style="font-size:14px; font-family:'Calibri Light';word-break:break-all;" align="right"><b>Total <?=$trim_type[$trimtype]; ?> Trims Cost :</b></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($typeTrimsAmtDzn,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($typeTrimsAmt,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($typeTrimsAmtPer,2); ?></td>
                </tr>
                <?
            }
            ?>
            </tbody>
            <tfoot>
            	<tr style="background-color:#CCFFFF">
                    <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all" colspan="6" align="right"><b>Grand Total Trims Cost :</b></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right">&nbsp;</td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right">&nbsp;</td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimamtTotDzn,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimReqAmttotal,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimPerTotal,2); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <? } else {
		if(($trims_costing_arr[$job_no]*1)>0)
		{
			?>
            <div style="margin-top:15px">
                <label style="font-size:14px; font-family:'Calibri Light'; background-color:#CCCCCC"><b>Trims Cost Details</b></label>
                <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:930px;" rules="all">
                    <thead>
                        <tr style="font-weight:bold;background-color:#FFC" align="center">
                            <th width="130" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Item Name</th>
                            <th width="230" style="font-size:14px; font-family:'Calibri Light';">Item Description</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">UOM</th>
                            <th width="60" style="font-size:14px; font-family:'Calibri Light';">YY</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">Wastage %</th>
                            <th width="60" style="font-size:14px; font-family:'Calibri Light';">Total YY</th>
                            <th width="80" style="font-size:14px; font-family:'Calibri Light';">Req. Qty</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">Rate</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">Cost /<?=$costing_for; ?></th>
                            <th width="80" style="font-size:14px; font-family:'Calibri Light';">Total Value</th>
                            <th style="font-size:14px; font-family:'Calibri Light';">%</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    
                    $i=1; $trimamtTotDzn=$trimReqAmttotal=$trimPerTotal=0;
                    foreach($trimsDataArr as $trimtype=>$trimtypedata)
                    {
                        ?>
                        <tr>
                            <td colspan="11" style="font-size:14px; font-family:'Calibri Light';word-break:break-all; background-color:#FFC"><b><?=$trim_type[$trimtype]; ?> Trims Details</b></td>
                        </tr>
                        <?
                        $typeTrimsAmtDzn=$typeTrimsAmt=$typeTrimsAmtPer=0;
                        foreach($trimtypedata as $trimid=>$trimdata)
                        {
                            $rowTrimReqQty=$rowTrimReqAmt=$trim_per=0;
                            $rowTrimReqQty=$trim_qty_arr[$trimid];
                            $rowTrimReqAmt=$trim_amount_arr[$trimid];
                            
                            $trim_per=($rowTrimReqAmt/$order_values)*100;
                            ?>
                            <tr>
                                <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$trimdata['ITEM_NAME']; ?></td>
                                <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$trimdata['DESCRIPTION']; ?></td>
                                <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$unit_of_measurement[$trimdata['CONS_UOM']]; ?></td>
                                <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimdata["NET_CONS"],3); ?></td>
                                <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimdata["EX_PER"],2); ?></td>
                                <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimdata["GCONS"],3); ?></td>
                                
                                <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowTrimReqQty,3); ?></td>
                                <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimdata["RATE"],3); ?></td>
                                <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimdata["AMOUNT"],3); ?></td>
                                <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowTrimReqAmt,3); ?></td>
                                <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trim_per,2); ?></td>
                            </tr>
                            <?
                                $typeTrimsAmtDzn+=$trimdata["AMOUNT"];
                                $typeTrimsAmt+=$rowTrimReqAmt;
                                $typeTrimsAmtPer+=$trim_per;
                                
                                $trimamtTotDzn+=$trimdata["AMOUNT"];
                                $trimReqAmttotal+=$rowTrimReqAmt;
                                $trimPerTotal+=$trim_per;
                        }
                         ?>
                        <tr style="background-color:#CCCCCC">
                            <td colspan="8" style="font-size:14px; font-family:'Calibri Light';word-break:break-all;" align="right"><b>Total <?=$trim_type[$trimtype]; ?> Trims Cost :</b></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($typeTrimsAmtDzn,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($typeTrimsAmt,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($typeTrimsAmtPer,2); ?></td>
                        </tr>
                        <?
                    }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color:#CCFFFF">
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all" colspan="6" align="right"><b>Grand Total Trims Cost :</b></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right">&nbsp;</td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right">&nbsp;</td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimamtTotDzn,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimReqAmttotal,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($trimPerTotal,2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
			<?
		}
	}
	?>
    
    <!--Wash =====================-->
    <? if($zero_value==1) { ?>
    <div style="margin-top:15px">
    	<label style="font-size:14px; font-family:'Calibri Light'; background-color:#CCCCCC"><b>Wash Cost Details</b></label>
    	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:930px;" rules="all">
        	<tbody>
                <tr style="font-weight:bold; background-color:#FFC" align="center">
                	<td width="130" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Item</td>
                    <td width="230" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Wash Type</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">UOM</td>
                    <td width="60" style="font-size:14px; font-family:'Calibri Light';">YY</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Wastage %</td>
                    <td width="60" style="font-size:14px; font-family:'Calibri Light';">Total YY</td>
                    
                    <td width="80" style="font-size:14px; font-family:'Calibri Light';">Req. Qty</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Rate</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Cost /<?=$costing_for; ?></td>
                    <td width="80" style="font-size:14px; font-family:'Calibri Light';">Total Value</td>
                    <td style="font-size:14px; font-family:'Calibri Light';">%</td>
                </tr>
            
			<?
            $sql = "select ID, JOB_NO, EMB_NAME, EMB_TYPE, CONS_DZN_GMTS, RATE, AMOUNT from wo_pre_cost_embe_cost_dtls where job_no=".$txt_job_no." and emb_name =3 and is_deleted=0 and status_active=1";
			$data_arrayWash=sql_select($sql);
			$wash_qty=$wash->getQtyArray_by_jobAndEmblishmentid();
			$wash_amount=$wash->getAmountArray_by_jobAndEmblishmentid();
            $washQtyTot=$washamtTotDzn=$washReqAmttotal=$washPerTotal=0;
            foreach($data_arrayWash as $wrow)
            {
                $rowWashReqQty=$rowWashReqAmt=$wash_per=0;
                $rowWashReqQty=$wash_qty[$wrow["JOB_NO"]][$wrow["ID"]];
                $rowWashReqAmt=$wash_amount[$wrow["JOB_NO"]][$wrow["ID"]];
                
                $wash_per=($rowWashReqAmt/$order_values)*100;
                ?>
                <tr>
                	<td style="font-size:14px; font-family:'Calibri Light';word-break:break-all">Wash</td>
                    <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$emblishment_wash_type[$wrow['EMB_TYPE']]; ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$costing_for; ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($wrow["CONS_DZN_GMTS"],3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><? //=fn_number_format($wrow["AVG_PROCESS_LOSS"],2); ?>&nbsp;</td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><? //=fn_number_format($wrow["AVG_CONS"],4); ?>&nbsp;</td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowWashReqQty,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($wrow["RATE"],3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($wrow["AMOUNT"],3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowWashReqAmt,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($wash_per,2); ?></td>
                </tr>
                <?	
				$washQtyTot+=$rowWashReqQty;
				$washamtTotDzn+=$wrow["AMOUNT"];
				$washReqAmttotal+=$rowWashReqAmt;
				$washPerTotal+=$wash_per;
            }
            ?>
            </tbody>
            <tfoot>
            	<tr style="background-color:#CCFFFF">
                    <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all" colspan="6" align="right"><b>Total Wash Cost :</b></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($washQtyTot,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right">&nbsp;</td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($washamtTotDzn,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($washReqAmttotal,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($washPerTotal,2); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <? } else {
		if(($emblishment_costing_arr_wash[$job_no]*1)>0)
		{
			?>
            <div style="margin-top:15px">
                <label style="font-size:14px; font-family:'Calibri Light'; background-color:#CCCCCC"><b>Wash Cost Details</b></label>
                <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:930px;" rules="all">
                    <thead>
                        <tr style="font-weight:bold; background-color:#FFC" align="center">
                            <th width="130" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Item</th>
                            <th width="230" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Wash Type</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">UOM</th>
                            <th width="60" style="font-size:14px; font-family:'Calibri Light';">YY</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">Wastage %</th>
                            <th width="60" style="font-size:14px; font-family:'Calibri Light';">Total YY</th>
                            
                            <th width="80" style="font-size:14px; font-family:'Calibri Light';">Req. Qty</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">Rate</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">Cost /<?=$costing_for; ?></th>
                            <th width="80" style="font-size:14px; font-family:'Calibri Light';">Total Value</th>
                            <th style="font-size:14px; font-family:'Calibri Light';">%</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $sql = "select ID, JOB_NO, EMB_NAME, EMB_TYPE, CONS_DZN_GMTS, RATE, AMOUNT from wo_pre_cost_embe_cost_dtls where job_no=".$txt_job_no." and emb_name =3 and is_deleted=0 and status_active=1";
                    $data_arrayWash=sql_select($sql);
                    $wash_qty=$wash->getQtyArray_by_jobAndEmblishmentid();
                    $wash_amount=$wash->getAmountArray_by_jobAndEmblishmentid();
                    $washQtyTot=$washamtTotDzn=$washReqAmttotal=$washPerTotal=0;
                    foreach($data_arrayWash as $wrow)
                    {
                        $rowWashReqQty=$rowWashReqAmt=$wash_per=0;
                        $rowWashReqQty=$wash_qty[$wrow["JOB_NO"]][$wrow["ID"]];
                        $rowWashReqAmt=$wash_amount[$wrow["JOB_NO"]][$wrow["ID"]];
                        
                        $wash_per=($rowWashReqAmt/$order_values)*100;
                        ?>
                        <tr>
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all">Wash</td>
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$emblishment_wash_type[$wrow['EMB_TYPE']]; ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$costing_for; ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($wrow["CONS_DZN_GMTS"],3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><? //=fn_number_format($wrow["AVG_PROCESS_LOSS"],2); ?>&nbsp;</td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><? //=fn_number_format($wrow["AVG_CONS"],4); ?>&nbsp;</td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowWashReqQty,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($wrow["RATE"],3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($wrow["AMOUNT"],3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowWashReqAmt,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($wash_per,2); ?></td>
                        </tr>
                        <?	
                        $washQtyTot+=$rowWashReqQty;
                        $washamtTotDzn+=$wrow["AMOUNT"];
        
                        $washReqAmttotal+=$rowWashReqAmt;
                        $washPerTotal+=$wash_per;
                    }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color:#CCFFFF">
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all" colspan="6" align="right"><b>Total Wash Cost :</b></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($washQtyTot,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right">&nbsp;</td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($washamtTotDzn,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($washReqAmttotal,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($washPerTotal,2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?
		}
	}
	?>
    
    <!--Embellishment =====================-->
    <? if($zero_value==1) { ?>
    <div style="margin-top:15px">
    	<label style="font-size:14px; font-family:'Calibri Light'; background-color:#CCCCCC"><b>Embellishment Cost Details</b></label>
    	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:930px;" rules="all">
        	<tbody>
                <tr style="font-weight:bold; background-color:#FFC" align="center">
                	<td width="130" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Embellishment Name</td>
                    <td width="230" style="font-size:14px; font-family:'Calibri Light';">Embl. Type</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">UOM</td>
                    <td width="60" style="font-size:14px; font-family:'Calibri Light';">YY</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Wastage %</td>
                    <td width="60" style="font-size:14px; font-family:'Calibri Light';">Total YY</td>
                    
                    <td width="80" style="font-size:14px; font-family:'Calibri Light';">Req. Qty</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Rate</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Cost/<?=$costing_for; ?></td>
                    <td width="80" style="font-size:14px; font-family:'Calibri Light';">Total Value</td>
                    <td style="font-size:14px; font-family:'Calibri Light';">%</td>
                </tr>
          	
            
			<?
            $sql_emb = "select ID, JOB_NO, EMB_NAME, EMB_TYPE, CONS_DZN_GMTS, RATE, AMOUNT from wo_pre_cost_embe_cost_dtls where job_no=".$txt_job_no." and emb_name !=3 and is_deleted=0 and status_active=1";
			$data_arrayEmb=sql_select($sql_emb);
			$emblishment_qty=$emblishment->getQtyArray_by_jobAndEmblishmentid();
			$emblishment_amount=$emblishment->getAmountArray_by_jobAndEmblishmentid();
            $embQtyTot=$embamtTotDzn=$embReqAmttotal=$embPerTotal=0;
            foreach($data_arrayEmb as $erow)
            {
				//$type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type,6=>$blank_array,99=>$emblishment_other_type_arr);
				
				if($erow['EMB_NAME']==1) $type_array=$emblishment_print_type;
				else if($erow['EMB_NAME']==2) $type_array=$emblishment_embroy_type;
				else if($erow['EMB_NAME']==3) $type_array=$emblishment_wash_type;
				else if($erow['EMB_NAME']==4) $type_array=$emblishment_spwork_type;
				else if($erow['EMB_NAME']==5) $type_array=$emblishment_gmts_type;
				else if($erow['EMB_NAME']==99) $type_array=$emblishment_other_type_arr;
				else $type_array=$blank_array;
				
                $rowEmbReqQty=$rowEmbReqAmt=$emb_per=0;
                $rowEmbReqQty=$emblishment_qty[$erow["JOB_NO"]][$erow["ID"]];
                $rowEmbReqAmt=$emblishment_amount[$erow["JOB_NO"]][$erow["ID"]];
                
                $emb_per=($rowEmbReqAmt/$order_values)*100;
                ?>
                <tr>
                	<td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$emblishment_name_array[$erow['EMB_NAME']]; ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$type_array[$erow['EMB_TYPE']]; ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$costing_for; ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($erow["CONS_DZN_GMTS"],3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><? //=fn_number_format($wrow["AVG_PROCESS_LOSS"],2); ?>&nbsp;</td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><? //=fn_number_format($wrow["AVG_CONS"],4); ?>&nbsp;</td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowEmbReqQty,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($erow["RATE"],3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($erow["AMOUNT"],3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowEmbReqAmt,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($emb_per,2); ?></td>
                </tr>
                <?	
				$embQtyTot+=$rowWashReqQty;
				$embamtTotDzn+=$erow["AMOUNT"];
				$embReqAmttotal+=$rowEmbReqAmt;
				$embPerTotal+=$emb_per;
            }
            ?>
            </tbody>
            <tfoot>
            	<tr style="background-color:#CCFFFF">
                    <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all" colspan="6" align="right"><b>Total Embellishment Cost :</b></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($embQtyTot,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right">&nbsp;</td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($embamtTotDzn,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($embReqAmttotal,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($embPerTotal,2); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <? } else {
		if(($emblishment_costing_arr[$job_no]*1)>0)
		{
			?>
            <div style="margin-top:15px">
                <label style="font-size:14px; font-family:'Calibri Light'; background-color:#CCCCCC"><b>Embellishment Cost Details</b></label>
                <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:930px;" rules="all">
                    <thead>
                        <tr style="font-weight:bold; background-color:#FFC" align="center">
                            <th width="130" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Embellishment Name</th>
                            <th width="230" style="font-size:14px; font-family:'Calibri Light';">Embl. Type</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">UOM</th>
                            <th width="60" style="font-size:14px; font-family:'Calibri Light';">YY</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">Wastage %</th>
                            <th width="60" style="font-size:14px; font-family:'Calibri Light';">Total YY</th>
                            
                            <th width="80" style="font-size:14px; font-family:'Calibri Light';">Req. Qty</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">Rate</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">Cost/<?=$costing_for; ?></th>
                            <th width="80" style="font-size:14px; font-family:'Calibri Light';">Total Value</th>
                            <th style="font-size:14px; font-family:'Calibri Light';">%</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $sql_emb = "select ID, JOB_NO, EMB_NAME, EMB_TYPE, CONS_DZN_GMTS, RATE, AMOUNT from wo_pre_cost_embe_cost_dtls where job_no=".$txt_job_no." and emb_name !=3 and is_deleted=0 and status_active=1";
                    $data_arrayEmb=sql_select($sql_emb);
                    $emblishment_qty=$emblishment->getQtyArray_by_jobAndEmblishmentid();
                    $emblishment_amount=$emblishment->getAmountArray_by_jobAndEmblishmentid();
                    $embQtyTot=$embamtTotDzn=$embReqAmttotal=$embPerTotal=0;
                    foreach($data_arrayEmb as $erow)
                    {
                        //$type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type,6=>$blank_array,99=>$emblishment_other_type_arr);
                        
                        if($erow['EMB_NAME']==1) $type_array=$emblishment_print_type;
                        else if($erow['EMB_NAME']==2) $type_array=$emblishment_embroy_type;
                        else if($erow['EMB_NAME']==3) $type_array=$emblishment_wash_type;
                        else if($erow['EMB_NAME']==4) $type_array=$emblishment_spwork_type;
                        else if($erow['EMB_NAME']==5) $type_array=$emblishment_gmts_type;
                        else if($erow['EMB_NAME']==99) $type_array=$emblishment_other_type_arr;
                        else $type_array=$blank_array;
                        
                        $rowEmbReqQty=$rowEmbReqAmt=$emb_per=0;
                        $rowEmbReqQty=$emblishment_qty[$erow["JOB_NO"]][$erow["ID"]];
                        $rowEmbReqAmt=$emblishment_amount[$erow["JOB_NO"]][$erow["ID"]];
                        
                        $emb_per=($rowEmbReqAmt/$order_values)*100;
                        ?>
                        <tr>
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$emblishment_name_array[$erow['EMB_NAME']]; ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$type_array[$erow['EMB_TYPE']]; ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$costing_for; ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($erow["CONS_DZN_GMTS"],3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><? //=fn_number_format($wrow["AVG_PROCESS_LOSS"],2); ?>&nbsp;</td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><? //=fn_number_format($wrow["AVG_CONS"],4); ?>&nbsp;</td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowEmbReqQty,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($erow["RATE"],3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($erow["AMOUNT"],3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowEmbReqAmt,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($emb_per,2); ?></td>
                        </tr>
                        <?	
                        $embQtyTot+=$rowWashReqQty;
                        $embamtTotDzn+=$erow["AMOUNT"];
                        $embReqAmttotal+=$rowEmbReqAmt;
                        $embPerTotal+=$emb_per;
                    }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color:#CCFFFF">
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all" colspan="6" align="right"><b>Total Embellishment Cost :</b></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($embQtyTot,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right">&nbsp;</td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($embamtTotDzn,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($embReqAmttotal,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($embPerTotal,2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?
		}
	}
	?>
    
    <!--Commercial =====================-->
    <? if($zero_value==1) { ?>
    <div style="margin-top:15px">
    	<label style="font-size:14px; font-family:'Calibri Light'; background-color:#CCCCCC"><b>Commercial Cost Details</b></label>
    	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:380px;" rules="all">
        	<tbody>
                <tr style="font-weight:bold; background-color:#FFC" align="center">
                	<td width="120" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Particulars</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Commercial %</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Cost/PCS</td>
                    <td width="80" style="font-size:14px; font-family:'Calibri Light';">Total Value</td>
                    <td style="font-size:14px; font-family:'Calibri Light';">%</td>
                </tr>
            
			<?
            $sql_comml = "select ID, JOB_NO, ITEM_ID, RATE, AMOUNT from  wo_pre_cost_comarci_cost_dtls  where job_no=".$txt_job_no." and status_active=1 and is_deleted=0";
			$data_arrayComer=sql_select($sql_comml);
			$commarcial_amount=$commercial->getAmountArray_by_jobAndPrecostdtlsid();
            foreach($data_arrayComer as $cmlrow)
            {
                $rowComlReqAmt=$coml_per=$commlAmt=0;
                $rowComlReqAmt=$commarcial_amount[$cmlrow["JOB_NO"]][$cmlrow["ID"]];
                $commlAmt=$rowComlReqAmt/$order_job_qnty;
                $coml_per=($rowComlReqAmt/$order_values)*100;
                ?>
                <tr>
                	<td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$camarcial_items[$cmlrow['ITEM_ID']]; ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($cmlrow["RATE"],3); ?></td>
                    
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($commlAmt,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowComlReqAmt,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($coml_per,2); ?></td>
                </tr>
                <?	
            }
            ?>
            </tbody>
        </table>
    </div>
    <? } else {
		if(($commercial_costing_arr[$job_no]*1)>0)
		{
			?>
            <div style="margin-top:15px">
                <label style="font-size:14px; font-family:'Calibri Light'; background-color:#CCCCCC"><b>Commercial Cost Details</b></label>
                <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:380px;" rules="all">
                    <thead>
                        <tr style="font-weight:bold; background-color:#FFC" align="center">
                            <th width="120" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Particulars</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">Commercial %</th>
                            <th width="50" style="font-size:14px; font-family:'Calibri Light';">Cost/PCS</th>
                            <th width="80" style="font-size:14px; font-family:'Calibri Light';">Total Value</th>
                            <th style="font-size:14px; font-family:'Calibri Light';">%</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $sql_comml = "select ID, JOB_NO, ITEM_ID, RATE, AMOUNT from  wo_pre_cost_comarci_cost_dtls  where job_no=".$txt_job_no." and status_active=1 and is_deleted=0";
                    $data_arrayComer=sql_select($sql_comml);
                    $commarcial_amount=$commercial->getAmountArray_by_jobAndPrecostdtlsid();
                    foreach($data_arrayComer as $cmlrow)
                    {
                        $rowComlReqAmt=$coml_per=$commlAmt=0;
                        $rowComlReqAmt=$commarcial_amount[$cmlrow["JOB_NO"]][$cmlrow["ID"]];
                        $commlAmt=$rowComlReqAmt/$order_job_qnty;
                        $coml_per=($rowComlReqAmt/$order_values)*100;
                        ?>
                        <tr>
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$camarcial_items[$cmlrow['ITEM_ID']]; ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($cmlrow["RATE"],3); ?></td>
                            
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($commlAmt,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowComlReqAmt,3); ?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($coml_per,2); ?></td>
                        </tr>
                        <?	
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <?
		}
	}
	?>
    
    <!--Commission =====================-->
    <? if($zero_value==1) { ?>
    <div style="margin-top:15px">
    	<label style="font-size:14px; font-family:'Calibri Light'; background-color:#CCCCCC"><b>Commission Cost Details</b></label>
    	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:380px;" rules="all">
        	 <tbody>
                <tr style="font-weight:bold; background-color:#FFC" align="center">
                	<td width="120" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Particulars</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Commission %</td>
                    <td width="50" style="font-size:14px; font-family:'Calibri Light';">Cost/PCS</td>
                    <td width="80" style="font-size:14px; font-family:'Calibri Light';">Total Value</td>
                    <td style="font-size:14px; font-family:'Calibri Light';">%</td>
                </tr>
          	
           
			<?
			$sql_comm = "select ID, JOB_NO, PARTICULARS_ID, COMMISSION_BASE_ID, COMMISION_RATE, COMMISSION_AMOUNT from wo_pre_cost_commiss_cost_dtls where job_no=".$txt_job_no." and status_active=1";
			$data_arrayComi=sql_select($sql_comm);
			$commision_amount=$commision->getAmountArray_by_jobAndPrecostdtlsid();
            foreach($data_arrayComi as $cmmrow)
            {
                $rowCommReqAmt=$comm_per=$commAmt=0;
                $rowCommReqAmt=$commision_amount[$cmmrow["JOB_NO"]][$cmmrow["ID"]];
                $commAmt=$rowCommReqAmt/$order_job_qnty;
                $comm_per=($rowCommReqAmt/$order_values)*100;
                ?>
                <tr>
                	<td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$commission_particulars[$cmmrow['PARTICULARS_ID']]; ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($cmmrow["COMMISION_RATE"],4); ?></td>
                    
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($commAmt,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowCommReqAmt,3); ?></td>
                    <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($comm_per,2); ?></td>
                </tr>
                <?	
            }
            ?>
            </tbody>
        </table>
    </div>
    <? } else { 
		if(($commission_costing_arr[$job_no]*1)>0)
		{
			?>
            <div style="margin-top:15px">
            <label style="font-size:14px; font-family:'Calibri Light'; background-color:#CCCCCC"><b>Commission Cost Details</b></label>
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:380px;" rules="all">
                <thead>
                    <tr style="font-weight:bold; background-color:#FFC" align="center">
                        <th width="120" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Particulars</th>
                        <th width="50" style="font-size:14px; font-family:'Calibri Light';">Commission %</th>
                        <th width="50" style="font-size:14px; font-family:'Calibri Light';">Cost/PCS</th>
                        <th width="80" style="font-size:14px; font-family:'Calibri Light';">Total Value</th>
                        <th style="font-size:14px; font-family:'Calibri Light';">%</th>
                    </tr>
                </thead>
                <tbody>
                <?
                $sql_comm = "select ID, JOB_NO, PARTICULARS_ID, COMMISSION_BASE_ID, COMMISION_RATE, COMMISSION_AMOUNT from wo_pre_cost_commiss_cost_dtls where job_no=".$txt_job_no." and status_active=1";
                $data_arrayComi=sql_select($sql_comm);
                $commision_amount=$commision->getAmountArray_by_jobAndPrecostdtlsid();
                foreach($data_arrayComi as $cmmrow)
                {
                    $rowCommReqAmt=$comm_per=$commAmt=0;
                    $rowCommReqAmt=$commision_amount[$cmmrow["JOB_NO"]][$cmmrow["ID"]];
                    $commAmt=$rowCommReqAmt/$order_job_qnty;
                    $comm_per=($rowCommReqAmt/$order_values)*100;
                    ?>
                    <tr>
                        <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$commission_particulars[$cmmrow['PARTICULARS_ID']]; ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($cmmrow["COMMISION_RATE"],4); ?></td>
                        
                        <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($commAmt,3); ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowCommReqAmt,3); ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($comm_per,2); ?></td>
                    </tr>
                    <?	
                }
                ?>
                </tbody>
            </table>
        </div>
            <?
		}
	}
	$otherCostHeadArr=array("currier_pre_cost"=>"Courier Cost","inspection"=>"Inspection Cost","common_oh"=>"Opt Exp","deffdlc_cost"=>"Deffd. LC/DC");
	
	$otherCostHeadPerArr=array("currier_pre_cost"=>"currier_percent","inspection"=>"inspection_percent","common_oh"=>"common_oh_percent","deffdlc_cost"=>"deffdlc_percent");
	
    foreach($otherCostHeadArr as $indexcost=>$indexhead)
	{
		if($zero_value==1) { ?>
		<div style="margin-top:15px">
			<label style="font-size:14px; font-family:'Calibri Light'; background-color:#CCCCCC"><b><?=$indexhead; ?> Details</b></label>
			<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:380px;" rules="all">
				<tbody>
					<tr style="font-weight:bold; background-color:#FFC" align="center">
						<td width="120" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Particulars</td>
						<td width="50" style="font-size:14px; font-family:'Calibri Light';">%</td>
						<td width="50" style="font-size:14px; font-family:'Calibri Light';">Cost/PCS</td>
						<td width="80" style="font-size:14px; font-family:'Calibri Light';">Total Value</td>
						<td style="font-size:14px; font-family:'Calibri Light';">%</td>
					</tr>
				
				
				<?
					$rowcostReqAmt=$rowcost_per=$rowAmt=0;
					$rowcostReqAmt=$other_costing_arr[$job_no][$indexcost];
					$rowAmt=$rowcostReqAmt/$order_job_qnty;
					$rowcost_per=($rowcostReqAmt/$order_values)*100;
					?>
					<tr>
						<td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$indexhead; ?></td>
						<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($summary_data[$otherCostHeadPerArr[$indexcost]],3); ?></td>
						
						<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowAmt,3); ?></td>
						<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowcostReqAmt,3); ?></td>
						<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowcost_per,2); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
		<? } else {
			if(($other_costing_arr[$job_no][$indexcost]*1)>0)
			{
				?>
				<div style="margin-top:15px">
					<label style="font-size:14px; font-family:'Calibri Light'; background-color:#CCCCCC"><b><?=$indexhead; ?> Cost Details</b></label>
					<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:380px;" rules="all">
						<thead>
							<tr style="font-weight:bold; background-color:#FFC" align="center">
								<th width="120" style="font-size:14px; font-family:'Calibri Light';" rowspan="<?=$i; ?>" >Particulars</th>
								<th width="50" style="font-size:14px; font-family:'Calibri Light';">%</th>
								<th width="50" style="font-size:14px; font-family:'Calibri Light';">Cost/PCS</th>
								<th width="80" style="font-size:14px; font-family:'Calibri Light';">Total Value</th>
								<th style="font-size:14px; font-family:'Calibri Light';">%</th>
							</tr>
						</thead>
						<tbody>
						<?
							$rowcostReqAmt=$rowcost_per=$rowAmt=0;
							$rowcostReqAmt=$other_costing_arr[$job_no][$indexcost];
							$rowAmt=$rowcostReqAmt/$order_job_qnty;
							$rowcost_per=($rowcostReqAmt/$order_values)*100;
							?>
							<tr>
								<td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$indexhead; ?></td>
								<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($summary_data[$otherCostHeadPerArr[$indexcost]],3); ?></td>
								
								<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowAmt,3); ?></td>
								<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowcostReqAmt,3); ?></td>
								<td style="font-size:14px; font-family:'Calibri Light';" align="right"><?=fn_number_format($rowcost_per,2); ?></td>
							</tr>
						</tbody>
					</table>
				</div>
				<?
			}
		}
	}
	?>
    <br/>
    <?
    $lib_designation_arr=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");
	$user_lib_designation_arr=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
	$user_lib_name_arr=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

	$mst_id=return_field_value("id as mst_id","wo_pre_cost_mst","job_no=$txt_job_no","mst_id");
	$unapprove_sql_data_array=sql_select("select b.approved_by, b.un_approved_by, b.approved_date, b.un_approved_reason, b.un_approved_date, b.approved_no,b.APPROVED,b.UN_APPROVED_REASON from approval_history b where b.mst_id=$mst_id and b.entry_form=46 order by b.id");


    foreach($unapprove_sql_data_array as $row){
        $unapprove_data_array[$row['APPROVED_BY']]=$row;
    }


	if(count($unapprove_data_array)>0)
	{
		$app_status_arr = array(0=>'Un App',1=>'Full App',2=>'Deny',3=>'Partial App');
        ?>
		<table width="930" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
                <tr>
                	<th colspan="7" style="font-size:14px; font-family:'Calibri Light';word-break:break-all">Approval/Un Approval History</th>
                </tr>
                <tr>
                    <th width="30" style="font-size:14px; font-family:'Calibri Light';word-break:break-all">SL</th>
                    <th width="150" style="font-size:14px; font-family:'Calibri Light';word-break:break-all">Name</th>
                    <th width="120" style="font-size:14px; font-family:'Calibri Light';word-break:break-all">Designation</th>
                    <th width="60" style="font-size:14px; font-family:'Calibri Light';word-break:break-all">Approval Status</th>
                    <th width="60" style="font-size:14px; font-family:'Calibri Light';">Approval No</th>
                    <th width="120" style="font-size:14px; font-family:'Calibri Light';word-break:break-all">App. Date & Time</th>
                    <th style="font-size:14px; font-family:'Calibri Light';word-break:break-all">Reason For Un-Approval</th>
                </tr>
            </thead>
            <tbody>
				<?
                $i=1;
                foreach($unapprove_data_array as $row)
				{
					?>
					<tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=$i; ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$user_lib_name_arr[$row[csf('approved_by')]]; ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';"><?=$lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]]; ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?= $app_status_arr[$row['APPROVED']]; ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=$row[csf('approved_no')];?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><? if($row[csf('approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); else echo ""; ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';"> &nbsp; <?= $row['UN_APPROVED_REASON']; ?></td>
					</tr>
					<?
					$i++;
					//$un_approved_date= explode(" ",$row[csf('un_approved_date')]);
					//$un_approved_date=$un_approved_date[0];
					//if($un_approved_date=="") $un_approved_date="";else $un_approved_date=$un_approved_date;

					if($un_approved_date!="")
					{
						if($row[csf('un_approved_by')]!==0)  $row[csf('approved_by')]=$row[csf('un_approved_by')];
						?>
						<tr>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=$i;?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$user_lib_name_arr[$row[csf('approved_by')]];?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';"><?=$lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?='No';?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=$row[csf('approved_no')];?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><? if($row[csf('un_approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('un_approved_date')])); else echo "";?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$unapproved_request_arr[$mst_id];?></td>
						</tr>
						<?
						$i++;
					}


                }
                ?>
            </tbody>
		</table>
		<?
	}
	?>
    <br>
    <table width="930" align="center">
            <tr>
            <div style="text-align:center;font-size:xx-large; font-style:italic; margin-top:20px; color:#FF0000;">
				<?
                if(count($approval_arr)>0)
                {
                    if($approved == 2 || $approved == 0){echo "Draft";}else{}
                }
                ?>
            </div>
            </tr>
    </table>
 	<?
	echo signature_table(109, $cbo_company_name, "930px",'','1','','');
	//signature_table($report_id, $company, $width, $template_id="", $padding_top = 70,$prepared_by='',$userSignatureArr=array(),$break_tr=7, $custom_style='')
	exit();
}

if($action=="bomRptWoven_3")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if($txt_job_no=="") $job_noCond=''; else $job_noCond=" and a.job_no=".$txt_job_no."";
    if($txt_job_no=="") $job_noCond=''; else $job_noCond2=" and job_no_mst=".$txt_job_no."";

	$txt_costing_date=change_date_format(str_replace("'","",$txt_costing_date),'yyyy-mm-dd','-');
	if($cbo_company_name=="") $company_name=''; else $company_name=" and a.company_name=".$cbo_company_name."";
	if($cbo_buyer_name=="") $cbo_buyer_name=''; else $cbo_buyer_name=" and a.buyer_name=".$cbo_buyer_name."";
	if($txt_style_ref=="") $txt_style_ref=''; else $txt_style_ref=" and a.style_ref_no=".$txt_style_ref."";
	if($txt_costing_date=="") $txt_costing_date=''; else $txt_costing_date=" and b.costing_date='".$txt_costing_date."'";
    if($poBreackDownId==""){$poId_cond='';$poId_cond2='';}else{$poId_cond=" and id in ($poBreackDownId)"; $poId_cond2=" and b.id in ($poBreackDownId)";}

	//array for display name
	
	$comp=return_library_array("select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array("select id, buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array("select id, brand_name from lib_buyer_brand",'id','brand_name');
	$season_arr=return_library_array("select id, season_name from lib_buyer_season",'id','season_name');
    $supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
    $size_library=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");

	 $po_qty=0; $po_plun_cut_qty=0; $total_set_qnty=0; $job_in_orders = ''; $pulich_ship_date=''; $job_in_file = ''; $job_in_ref = ''; $postatus='';
	 $sql_po="select a.job_no, a.total_set_qnty, b.id, b.po_number, b.grouping, b.file_no, b.pub_shipment_date, b.is_confirmed, c.item_number_id, c.country_id, c.color_number_id, c.size_number_id, c.order_quantity, c.plan_cut_qnty from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id  and a.job_no =".$txt_job_no."   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by b.id";
	$sql_po_data=sql_select($sql_po);
	foreach($sql_po_data as $sql_po_row){
		$po_qty+=$sql_po_row[csf('order_quantity')];
		$po_plun_cut_qty+=$sql_po_row[csf('plan_cut_qnty')];
		$total_set_qnty=$sql_po_row[csf('total_set_qnty')];
		
		$job_in_orders .= $sql_po_row[csf('po_number')].", ";
		$pulich_ship_date = $sql_po_row[csf('pub_shipment_date')];
		$job_in_file .= $sql_po_row[csf('file_no')].",";
		$job_in_ref .= $sql_po_row[csf('grouping')].",";
		$postatus .= $order_status[$sql_po_row[csf('is_confirmed')]].",";
        $po_id_arr[$sql_po_row[csf('id')]]=$sql_po_row[csf('id')];
	}
    $po_id_str= implode( ",",$po_id_arr);
	
	$job_in_orders = substr(trim($job_in_orders),0,-1);
	$job_ref=array_unique(explode(",",rtrim($job_in_ref,", ")));
	$job_file=array_unique(explode(",",rtrim($job_in_file,", ")));
	$postatus=array_unique(explode(",",rtrim($postatus,", ")));

	foreach ($job_ref as $ref){
		$ref_cond.=", ".$ref;
	}
	$file_con='';
	foreach ($job_file as $file){
		if($file_con=='') $file_cond=$file; else $file_cond.=", ".$file;
	}
	$postatusstrshow='';
	foreach ($postatus as $postatusstr){
		if($postatusstrshow=='') $postatusstrshow=$postatusstr; else $postatusstrshow.=", ".$postatusstr;
	}

	$gmtsitem_ratio_array=array();
	$gmtsitem_ratio_sql=sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details where job_no =".$txt_job_no."");// where job_no ='FAL-14-01157'
	foreach($gmtsitem_ratio_sql as $gmtsitem_ratio_sql_row){
		$gmtsitem_ratio_array[$gmtsitem_ratio_sql_row[csf('job_no')]][$gmtsitem_ratio_sql_row[csf('gmts_item_id')]]=$gmtsitem_ratio_sql_row[csf('set_item_ratio')];
	}
	
	$financial_para=array();
	$sql_std_para=sql_select("select interest_expense,income_tax,cost_per_minute,applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$cbo_company_name and status_active=1 and is_deleted=0 order by id");
	foreach($sql_std_para as $row )
	{
		$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
		$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
		$diff=datediff('d',$applying_period_date,$applying_period_to_date);
		for($j=0;$j<$diff;$j++)
		{
			//$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
			$date_all=add_date(str_replace("'","",$applying_period_date),$j);
			$newdate =change_date_format($date_all,'','',1);
			$financial_para[$newdate][interest_expense]=$row[csf('interest_expense')];
			$financial_para[$newdate][income_tax]=$row[csf('income_tax')];
			$financial_para[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
		}
	}
	//print_r($financial_para);
	
	$job_no=str_replace("'","",$txt_job_no);
	
	$condition= new condition();
	if(str_replace("'","",$txt_job_no) !=''){
		$condition->job_no("=$txt_job_no");
	}
	if(str_replace("'",'',$txt_po_breack_down_id) !=""){
		$condition->po_id("in($txt_po_breack_down_id)");
	}
	$condition->init();
	$fabric= new fabric($condition);
	
	$trim= new trims($condition);
	$emblishment= new emblishment($condition);
	$wash= new wash($condition);
	$other= new other($condition);
	
	$commercial= new commercial($condition);
	$commision= new commision($condition);
	
	$fabric_costing_arr=$fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
	$trims_costing_arr=$trim->getAmountArray_by_job();
	$emblishment_costing_arr=$emblishment->getAmountArray_by_job();
	$emblishment_costing_arr_wash=$wash->getAmountArray_by_job();
	
	$commercial_costing_arr=$commercial->getAmountArray_by_job();
	$commission_costing_arr=$commision->getAmountArray_by_job();
	$other_costing_arr=$other->getAmountArray_by_job();
	
	$ttl_cm_cost=$other_costing_arr[$job_no]['cm_cost'];
	
	$sql_dtls = "select job_no, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, commission, commission_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, common_oh, common_oh_percent, depr_amor_pre_cost, deffdlc_cost, deffdlc_percent, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche
	from wo_pre_cost_dtls where job_no=".$txt_job_no." and status_active=1 and is_deleted=0";
	$data_array_new=sql_select($sql_dtls);
	
	$summary_data=array();
	foreach($data_array_new as $row_new ){
		$summary_data[price_dzn]=$row_new[csf("price_dzn")];
		$price_dzn=$row_new[csf("price_dzn")];
		$summary_data[price_dzn_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("price_dzn")];
		$summary_data[commission]=$row_new[csf("commission")];
		$summary_data[trims_cost]=$row_new[csf("trims_cost")];
		$summary_data[emb_cost]=$row_new[csf("embel_cost")];

		$summary_data[lab_test]=$row_new[csf("lab_test")];
		$summary_data[lab_test_job]=$other_costing_arr[$row_new[csf("job_no")]]['lab_test'];

		$summary_data[inspection]=$row_new[csf("inspection")];
		$summary_data[inspection_job]=$other_costing_arr[$row_new[csf("job_no")]]['inspection'];

		$summary_data[freight]=$row_new[csf("freight")];
		$summary_data[freight_job]=$other_costing_arr[$row_new[csf("job_no")]]['freight'];

		$summary_data[currier_pre_cost]=$row_new[csf("currier_pre_cost")];
		$summary_data[currier_pre_cost_job]=$other_costing_arr[$row_new[csf("job_no")]]['currier_pre_cost'];

		$summary_data[certificate_pre_cost]=$row_new[csf("certificate_pre_cost")];
		$summary_data[certificate_pre_cost_job]=$other_costing_arr[$row_new[csf("job_no")]]['certificate_pre_cost'];
		$summary_data[wash_cost]=$row_new[csf("wash_cost")];

		$summary_data[OtherDirectExpenses]=$row_new[csf("lab_test")]+$row_new[csf("inspection")]+$row_new[csf("freight")]+$row_new[csf("currier_pre_cost")]+$row_new[csf("certificate_pre_cost")]+$row_new[csf("wash_cost")];
		$summary_data[OtherDirectExpenses_job]=$summary_data[lab_test_job]+$summary_data[inspection_job]+$summary_data[freight_job]+$summary_data[currier_pre_cost_job]+$summary_data[certificate_pre_cost_job];

		$summary_data[cm_cost]=$row_new[csf("cm_cost")];
		$summary_data[cm_cost_job]=$other_costing_arr[$row_new[csf("job_no")]]['cm_cost'];
		$summary_data[comm_cost]=$row_new[csf("comm_cost")];
		$summary_data[common_oh]=$row_new[csf("common_oh")];
		$summary_data[common_oh_job]=$other_costing_arr[$row_new[csf("job_no")]]['common_oh'];
		$summary_data[depr_amor_pre_cost]=$row_new[csf("depr_amor_pre_cost")];
		$summary_data[depr_amor_pre_cost_job]=$other_costing_arr[$row_new[csf("job_no")]]['depr_amor_pre_cost'];
		$summary_data[margindzn]=$row_new[csf("margin_dzn")];
		$summary_data[fabric_percent]=$row_new[csf("fabric_cost_percent")];
		$summary_data[trims_percent]=$row_new[csf("trims_cost_percent")];
		$summary_data[wash_percent]=$row_new[csf("wash_cost_percent")];
		$summary_data[emb_percent]=$row_new[csf("embel_cost_percent")];
		$summary_data[commercial_percent]=$row_new[csf("comm_cost_percent")];
		$summary_data[currier_percent]=$row_new[csf("currier_percent")];
		$summary_data[commission_percent]=$row_new[csf("commission_percent")];
		$summary_data[lab_test_percent]=$row_new[csf("lab_test_percent")];
		$summary_data[freight_percent]=$row_new[csf("freight_percent")];
		$summary_data[margin_dzn_percent]=$row_new[csf("margin_dzn_percent")];
		$summary_data[cm_cost_percent]=$row_new[csf("cm_cost_percent")];
		$summary_data[inspection_percent]=$row_new[csf("inspection_percent")];
		$summary_data[deffdlc_percent]=$row_new[csf("deffdlc_percent")];
		$summary_data[common_oh_percent]=$row_new[csf("common_oh_percent")];
	}
	unset($data_array_new);
    //Fabric =====================
	$fabric_qty=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
	$fabric_amount=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
	$sql_fabric = "select id, job_no, body_part_id, fab_nature_id, color_type_id, fabric_description,uom, avg_cons,avg_cons_yarn, fabric_source,gsm_weight, rate, amount, avg_finish_cons, status_active,nominated_supp_multi from wo_pre_cost_fabric_cost_dtls where job_no=".$txt_job_no." order by seq";
	$data_arr_fabric=sql_select($sql_fabric);
	$totFabQty=0;
	$totFabAmt=0;
	$FabricData=array();
	foreach($data_arr_fabric as $fab_row){
		$summary_data[fabric_cost][$fab_row[csf("id")]]=$fab_row[csf("amount")];
		if($fab_row[csf("fab_nature_id")] == 3)
		{
			$summary_data[fabric_cost_job]+=$fabric_amount['woven']['grey'][$fab_row[csf("id")]][$fab_row[csf("uom")]];
		}
		if($fab_row[csf("fab_nature_id")] == 2)
		{
			$summary_data[fabric_cost_job]+=$fabric_amount['knit']['grey'][$fab_row[csf("id")]][$fab_row[csf("uom")]];
		}
		$totFabQty+=$fabric_qty['woven']['grey'][$fab_row[csf("id")]][$fab_row[csf("uom")]];
		$totFabAmt+=$fabric_amount['woven']['grey'][$fab_row[csf("id")]][$fab_row[csf("uom")]];

		$FabricData[$fab_row[csf("id")]]['description']=$body_part[$fab_row[csf("body_part_id")]].", ".$color_type[$fab_row[csf("color_type_id")]].", ".$fab_row[csf("fabric_description")].", ".$fab_row[csf("gsm_weight")];
		$FabricData[$fab_row[csf("id")]]['fabric_source']=$fabric_source[$fab_row[csf("fabric_source")]];
		//$FabricData[$fab_row[csf("id")]]['nominated_supp']=$supplier_name_arr[$fab_row[csf("nominated_supp")]];
		$FabricData[$fab_row[csf("id")]]['nominated_supp']=$fab_row[csf("nominated_supp_multi")];
		$FabricData[$fab_row[csf("id")]]['avg_cons']=$fab_row[csf("avg_cons")];
		if($fab_row[csf("fab_nature_id")] == 3){
			$FabricData[$fab_row[csf("id")]]['totalConsWoven']=$fabric_qty['woven']['grey'][$fab_row[csf("id")]][$fab_row[csf("uom")]];
			$FabricData[$fab_row[csf("id")]]['totalAmountWoven']=$fabric_amount['woven']['grey'][$fab_row[csf("id")]][$fab_row[csf("uom")]];
		}
		if($fab_row[csf("fab_nature_id")] == 2){
			$FabricData[$fab_row[csf("id")]]['totalConsWoven']=$fabric_qty['knit']['grey'][$fab_row[csf("id")]][$fab_row[csf("uom")]];
			$FabricData[$fab_row[csf("id")]]['totalAmountWoven']=$fabric_amount['knit']['grey'][$fab_row[csf("id")]][$fab_row[csf("uom")]];
		}
		$FabricData[$fab_row[csf("id")]]['uom']=$unit_of_measurement[$fab_row[csf("uom")]];
		$FabricData[$fab_row[csf("id")]]['rate']=$fab_row[csf("rate")];
		$FabricData[$fab_row[csf("id")]]['amount']=$fab_row[csf("rate")]*$fab_row[csf("avg_cons")];
	}
	$totFabQtyAsCostPer=($totFabQty/$poPlunCutQtyUom)*$order_price_per_dzn;
	//Fabric End======================
	//start	Trims Cost part report here -------------------------------------------
	$sql_trim = "select id, job_no, trim_group,description,brand_sup_ref,remark, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp_multi, status_active, seq
	from wo_pre_cost_trim_cost_dtls
	where job_no=".$txt_job_no." and is_deleted= 0 order by seq";
	$data_array_trim=sql_select($sql_trim);
	$trim_amount_arr=$trim->getAmountArray_by_jobAndPrecostdtlsid();
	$trim_qty_arr=$trim->getQtyArray_by_jobAndPrecostdtlsid();
	//$trim_amount_arr=$trim->getAmountArray_by_jobAndPrecostdtlsid_consAndTotcons();
	//$trim_qty_arr=$trim->getQtyArray_by_jobAndPrecostdtlsid_consAndTotcons();
	$totTrim=0;
	$TrimData=array();
	foreach( $data_array_trim as $row_trim ){
		$trim_qty=$trim_qty_arr[$row_trim[csf("job_no")]][$row_trim[csf("id")]];
		$trim_amount=$trim_amount_arr[$row_trim[csf("job_no")]][$row_trim[csf("id")]];
		$summary_data[trims_cost_job]+=$trim_amount;
		$TrimData[$row_trim[csf('id')]]['trim_group']=$row_trim[csf('trim_group')];
		$TrimData[$row_trim[csf('id')]]['description']=$row_trim[csf('description')];
		$TrimData[$row_trim[csf('id')]]['brand_sup_ref']=$row_trim[csf('brand_sup_ref')];
		$TrimData[$row_trim[csf('id')]]['nominated_supp']=$row_trim[csf('nominated_supp_multi')];
		$TrimData[$row_trim[csf('id')]]['remark']=$row_trim[csf('remark')];
		$TrimData[$row_trim[csf('id')]]['cons_uom']=$row_trim[csf('cons_uom')];
		$TrimData[$row_trim[csf('id')]]['cons_dzn_gmts']=$row_trim[csf('cons_dzn_gmts')];
		$TrimData[$row_trim[csf('id')]]['rate']=$row_trim[csf('rate')];
		$TrimData[$row_trim[csf('id')]]['amount']=$row_trim[csf('amount')];
		$TrimData[$row_trim[csf('id')]]['apvl_req']=$row_trim[csf('apvl_req')];
		$TrimData[$row_trim[csf('id')]]['tot_cons']=$trim_qty;
		$TrimData[$row_trim[csf('id')]]['tot_amount']=$trim_amount;
		$totTrim+=$row_trim[csf('cons_dzn_gmts')];
	}
	//End	Trims Cost part report here -------------------------------------------
	//Emb cost Cost part report here -------------------------------------------

	$sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active,nominated_supp_multi from wo_pre_cost_embe_cost_dtls where job_no=".$txt_job_no." and emb_name in(1,2,4,5)";
	$data_array=sql_select($sql);
	$emblishment_qty=$emblishment->getQtyArray_by_jobAndEmblishmentid();
	$emblishment_amount=$emblishment->getAmountArray_by_jobAndEmblishmentid();

	$totEmb=0;
	$EmbData=array();

	foreach( $data_array as $row ){
	$embqty=$emblishment_qty[$row[csf("job_no")]][$row[csf("id")]];
	$embamount=$emblishment_amount[$row[csf("job_no")]][$row[csf("id")]];
	$summary_data[emb_cost_job]+=$embamount;
	$EmbData[$row[csf("id")]]['emb_name']=$row[csf("emb_name")];
	$EmbData[$row[csf("id")]]['emb_type']=$row[csf("emb_type")];
	$EmbData[$row[csf("id")]]['cons_dzn_gmts']=$row[csf("cons_dzn_gmts")];
	$EmbData[$row[csf("id")]]['rate']=$row[csf("rate")];
	$EmbData[$row[csf("id")]]['amount']=$row[csf("amount")];
    $EmbData[$row[csf('id')]]['nominated_supp']=$row[csf('nominated_supp_multi')];
	$EmbData[$row[csf("id")]]['tot_cons']=$embqty;
	$EmbData[$row[csf("id")]]['tot_amount']=$embamount;
	$totEmb+=$row[csf("cons_dzn_gmts")];
	}
	//End Emb cost Cost part report here -------------------------------------------
	//Wash cost Cost part report here -------------------------------------------
	$sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active,nominated_supp_multi from wo_pre_cost_embe_cost_dtls where job_no=".$txt_job_no." and emb_name =3";
	$data_array=sql_select($sql);
	$wash_qty=$wash->getQtyArray_by_jobAndEmblishmentid();
	$wash_amount=$wash->getAmountArray_by_jobAndEmblishmentid();
	foreach( $data_array as $row ){
		$washqty=$wash_qty[$row[csf("job_no")]][$row[csf("id")]];
		$washamount=$wash_amount[$row[csf("job_no")]][$row[csf("id")]];
		$summary_data[wash_cost_job]+=$washamount;
		$summary_data[OtherDirectExpenses_job]+=$washamount;
		$WashData[$row[csf("id")]]['emb_name']=$row[csf("emb_name")];
		$WashData[$row[csf("id")]]['emb_type']=$row[csf("emb_type")];
		$WashData[$row[csf("id")]]['cons_dzn_gmts']=$row[csf("cons_dzn_gmts")];
		$WashData[$row[csf("id")]]['rate']=$row[csf("rate")];
		$WashData[$row[csf("id")]]['amount']=$row[csf("amount")];
        $WashData[$row[csf('id')]]['nominated_supp']=$row[csf('nominated_supp_multi')];
		$WashData[$row[csf("id")]]['tot_cons']=$washqty;
		$WashData[$row[csf("id")]]['tot_amount']=$washamount;
		$totEmb+=$row[csf("cons_dzn_gmts")];
	}
	//End Wash cost Cost part report here -------------------------------------------

	//Commision cost Cost part report here -------------------------------------------
	$sql = "select id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount, status_active from  wo_pre_cost_commiss_cost_dtls  where job_no=".$txt_job_no."";
	$data_array=sql_select($sql);
	$commision_amount=$commision->getAmountArray_by_jobAndPrecostdtlsid();
	$totCommi=0;
	$CommiData=array();
	foreach( $data_array as $row ){
		$CommiData[$row[csf("id")]]['particulars_id']=$row[csf("particulars_id")];
		$CommiData[$row[csf("id")]]['commission_base_id']=$row[csf("commission_base_id")];
		$CommiData[$row[csf("id")]]['commision_rate']=$row[csf("commision_rate")];
		$CommiData[$row[csf("id")]]['commission_amount']=$row[csf("commission_amount")];
		$CommiData[$row[csf("id")]]['tot_commission_amount']=$commision_amount[$row[csf("job_no")]][$row[csf("id")]];
		$totCommi+=$row[csf("commission_amount")];
		$summary_data[commission_job]+=$commision_amount[$row[csf("job_no")]][$row[csf("id")]];
	}
	$summary_data[commission_job_per]=($summary_data[commission_job]/$summary_data[price_dzn_job])*100;
	//End Commision cost Cost part report here -------------------------------------------

	//Commarcial cost Cost part report here -------------------------------------------
	$sql = "select id, job_no, item_id, rate, amount, status_active from  wo_pre_cost_comarci_cost_dtls  where job_no=".$txt_job_no."";
	$data_array=sql_select($sql);
	$commarcial_amount=$commercial->getAmountArray_by_jobAndPrecostdtlsid();

	$totCommar=0;
	$CommarData=array();
	foreach( $data_array as $row ){
		$commarcialamount=$commarcial_amount[$row[csf("job_no")]][$row[csf("id")]];
		$summary_data[comm_cost_job]+=$commarcialamount;
		$CommarData[$row[csf("id")]]['item_id']=$row[csf("item_id")];
		$CommarData[$row[csf("id")]]['rate']=$row[csf("rate")];
		$CommarData[$row[csf("id")]]['amount']=$row[csf("amount")];
		$CommarData[$row[csf("id")]]['tot_amount']=$commarcialamount;
		$totCommar+=$row[csf("amount")];
	}
	//End Commarcial cost Cost part report here -------------------------------------------
	$NetFOBValue=$summary_data[price_dzn]-$summary_data[commission];
	$NetFOBValue_job=$summary_data[price_dzn_job]-$summary_data[commission_job];

	$Less_Cost_Material_Services=array_sum($summary_data[fabric_cost])+$summary_data[trims_cost]+$summary_data[emb_cost]+$summary_data[lab_test]+$summary_data[inspection]+$summary_data[freight]+$summary_data[currier_pre_cost]+$summary_data[certificate_pre_cost]+$summary_data[wash_cost];
	$Less_Cost_Material_Services_job=$summary_data[fabric_cost_job]+$summary_data[trims_cost_job]+$summary_data[emb_cost_job]+$summary_data[OtherDirectExpenses_job];

	$Contribution_Margin=$NetFOBValue-$Less_Cost_Material_Services;
	$Contribution_Margin_job=$NetFOBValue_job-$Less_Cost_Material_Services_job;

	$Gross_Profit=$Contribution_Margin-$summary_data[cm_cost];
	$Gross_Profit_job=$Contribution_Margin_job-$summary_data[cm_cost_job];

	$OperatingProfitLoss=$Gross_Profit-($summary_data[comm_cost]+$summary_data[common_oh]);
	$OperatingProfitLoss_job=$Gross_Profit_job-($summary_data[comm_cost_job]+$summary_data[common_oh_job]);

	$interest_expense=$NetFOBValue*$financial_para[interest_expense]/100;
	$income_tax=$NetFOBValue*$financial_para[income_tax]/100;
	$interest_expense_job=$NetFOBValue_job*$financial_para[interest_expense]/100;
	$income_tax_job=$NetFOBValue_job*$financial_para[income_tax]/100;
	$Netprofit=$OperatingProfitLoss-($summary_data[depr_amor_pre_cost]+$interest_expense+$income_tax);
	$Netprofit_job=$OperatingProfitLoss_job-($summary_data[depr_amor_pre_cost_job]+$interest_expense_job+$income_tax_job);

    $total_other_components_sum += $summary_data[lab_test_job]+$summary_data[inspection_job]+$summary_data[freight_job]+$summary_data[currier_pre_cost_job]+$summary_data[certificate_pre_cost_job]+$summary_data[common_oh_job]+$summary_data[depr_amor_pre_cost_job]+$interest_expense_tot+$income_tax_tot+$TotalAountCommi+$TotalAountComr;
	
    $dzn_other_components_sum += $summary_data[lab_test]+$summary_data[inspection]+$summary_data[freight]+$summary_data[currier_pre_cost]+$summary_data[certificate_pre_cost]+$summary_data[common_oh]+$summary_data[depr_amor_pre_cost]+$interest_expense_dzn+$income_tax_dzn+$summary_data[commission]+$summary_data[comm_cost];

	$total_other_components += $summary_data[lab_test_job]+$summary_data[inspection_job]+$summary_data[freight_job]+$summary_data[currier_pre_cost_job]+$summary_data[certificate_pre_cost_job]+$summary_data[common_oh_job]+$summary_data[depr_amor_pre_cost_job]+$interest_expense_tot+$income_tax_tot+$TotalAountCommi;

	$dzn_other_components += $summary_data[lab_test]+$summary_data[inspection]+$summary_data[freight]+$summary_data[currier_pre_cost]+$summary_data[certificate_pre_cost]+$summary_data[common_oh]+$summary_data[depr_amor_pre_cost]+$interest_expense_dzn+$income_tax_dzn+$summary_data[commission];

	$fab_knit_req_kg_avg=0; $fab_woven_req_yds_avg=0;

    $excess_cut=0;
	$sql_excess="select b.excess_cut_perc from wo_po_color_size_breakdown b,wo_po_break_down d where d.id=b.po_break_down_id and d.job_no_mst=".$txt_job_no." and d.id in ($po_id_str) and b.status_active=1 and d.status_active=1";
	$excess_data_array=sql_select($sql_excess);
	foreach( $excess_data_array as $row)
	{
		$excess_cut=$row[csf("excess_cut_perc")];
	}
    $sales_contract_sql=sql_select("SELECT a.contract_no,b.wo_po_break_down_id from com_sales_contract a join com_sales_contract_order_info b on a.id=b.com_sales_contract_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.wo_po_break_down_id in ($po_id_str) group by a.contract_no, b.wo_po_break_down_id");
	$sales_contract_arr=array();
	if(count($sales_contract_sql)>0)
	{
		foreach ($sales_contract_sql as $key => $row) {
			$sales_contract_arr[$row[csf('wo_po_break_down_id')]]=$row[csf('contract_no')];
		}
	}

	$export_lc_sql=sql_select("SELECT a.export_lc_no,b.wo_po_break_down_id from com_export_lc a join com_export_lc_order_info b on a.id=b.com_export_lc_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.wo_po_break_down_id in ($po_id_str) group by a.export_lc_no,b.wo_po_break_down_id");
	$export_lc_arr=array();
	if(count($export_lc_sql)>0)
	{
		foreach ($export_lc_sql as $key => $row) {
			$export_lc_arr[$row[csf('wo_po_break_down_id')]]=$row[csf('export_lc_no')];
		}
	}
    $sc_no="";
	$lc_no="";
	if(count($sales_contract_arr)>0)

	{
		$sc_no=implode(",", $sales_contract_arr);
	}
	if(count($export_lc_arr)>0)
	{
		$lc_no=implode(",", $export_lc_arr);
	}
	$result =sql_select("SELECT po_number,grouping,file_no,pub_shipment_date from wo_po_break_down where job_no_mst=$txt_job_no $poId_cond and status_active=1 and is_deleted=0 order by pub_shipment_date DESC");
		$job_in_orders = '';$pulich_ship_date='';$job_in_file = '';$job_in_ref = '';
		foreach ($result as $val){
			$job_in_orders .= $val[csf('po_number')].", ";
			$pulich_ship_date = $val[csf('pub_shipment_date')];
			$job_in_file .= $val[csf('file_no')].",";
			$job_in_ref .= $val[csf('grouping')].",";
		}
		$job_in_orders = substr(trim($job_in_orders),0,-1);
		$unit_result =sql_select("SELECT AVG(unit_price) as unit_price,SUM(plan_cut) as plan_cut from wo_po_break_down where job_no_mst=$txt_job_no $poId_cond and status_active=1 and is_deleted=0");
		foreach ($unit_result as $val){
			$unit_price= $val[csf('unit_price')];
			$plan_cut= $val[csf('plan_cut')];
		}

		$consumption_result =sql_select("SELECT SUM(avg_cons) as sum_cons from wo_pre_cost_fabric_cost_dtls where job_no=$txt_job_no and status_active=1 and is_deleted=0");
		foreach ($consumption_result as $val){
			$sum_cons= $val[csf('sum_cons')];
		}

	$po_shipment_date=sql_select("select  MIN(pub_shipment_date) as min_shipment_date,max(pub_shipment_date) as max_shipment_date from wo_po_break_down where id in ($po_id_str) order by shipment_date asc ");
	$min_shipment_date='';
	$max_shipment_date='';
	foreach ($po_shipment_date as $row) {
		$min_shipment_date=$row[csf('min_shipment_date')];
		$max_shipment_date=$row[csf('max_shipment_date')];
		break;
	}
	$shipment_date='';
	if(count($result)==1){
		$shipment_date=change_date_format($min_shipment_date);
	}else{
		$shipment_date=change_date_format($min_shipment_date)." to ".change_date_format($max_shipment_date);
	}

    $sql = "select a.job_no, a.company_name, a.buyer_name, a.brand_id, a.season_buyer_wise, a.style_ref_no, a.quotation_id, a.gmts_item_id, a.order_uom, a.job_quantity, a.avg_unit_price, b.costing_per, b.budget_minute, b.costing_date, b.exchange_rate, b.incoterm, b.sew_smv, b.cut_smv, b.sew_effi_percent, b.cut_effi_percent, b.approved, c.fab_knit_req_kg, c.fab_knit_fin_req_kg, c.fab_woven_req_yds, c.fab_woven_fin_req_yds, c.fab_yarn_req_kg from wo_po_details_master a, wo_pre_cost_mst b left join wo_pre_cost_sum_dtls c on b.job_no=c.job_no where a.job_no=b.job_no and a.status_active=1 $job_noCond $company_name $cbo_buyer_name $txt_style_ref order by a.job_no";
	$data_array=sql_select($sql);
	$uom=""; $sew_smv=0; $cut_smv=0; $sew_effi_percent=0; $cut_effi_percent=0;  $cpmCal=0; $poQty=0;
	foreach ($data_array as $row){
		$order_price_per_dzn=0; $order_job_qnty=0; $avg_unit_price=0;
		$sew_smv=$row[csf("sew_smv")];
	    $cut_smv=$row[csf("cut_smv")];
	    $sew_effi_percent=$row[csf("sew_effi_percent")];
	    $cut_effi_percent=$row[csf("cut_effi_percent")];
		$order_values = $row[csf("job_quantity")]*$row[csf("avg_unit_price")];
		$pre_costing_date=change_date_format($row[csf('costing_date')],'','',1);
        $costing_date=$row[csf("costing_date")];
		$poQty=$row[csf("job_quantity")];
        
		
		if($row[csf("costing_per")]==1){
            $order_price_per_dzn=12;
            $costing_for=" DZN";
        }
        else if($row[csf("costing_per")]==2){
            $order_price_per_dzn=1;
            $costing_for=" PCS";
        }
        else if($row[csf("costing_per")]==3){
            $order_price_per_dzn=24;
            $costing_for=" 2 DZN";
        }
        else if($row[csf("costing_per")]==4){
            $order_price_per_dzn=36;
            $costing_for=" 3 DZN";
        }
        else if($row[csf("costing_per")]==5){
            $order_price_per_dzn=48;
            $costing_for=" 4 DZN";
        }	
    ?>
        <style>
		.vl {
		  border-left: 3px solid green;
		  position: absolute;
		  left: 50%;
		  top: 0;
		}
		</style>
        <table style="width:930px">
        	<tr>
            <?
                $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id=$cbo_company_name","image_location");
                $path=($path)?$path:'../../';
                ?>
                <td colspan="3" align="left"><img src='<? echo $path.$image_location; ?>' height='40' width='100' /></td>
            	<td colspan="3" align="center" style="font-size:20px; ">
                	<b><? echo strtoupper($comp[$row[csf("company_name")]]); ?></b>
                    <br />
                    <b style="font-size:18px; background-color:#FFFFCC">BUDGET SHEET
					</b>
                </td>		   
                <td colspan="3" align="right"><b>DATE:&nbsp;<?=change_date_format($pre_costing_date); ?></b></td>
           </tr>
       </table>

       <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:930px" rules="all">
       <tr>
        <td width="270">
        <table  class="rpt_table"  border="1" align="left" cellpadding="0" width="270" cellspacing="0" rules="all" >
        <tr>
                <td width="120" style="font-size:14px; "><b>Product Picture</b></td>
                <td width="150" style="font-size:14px; "><?
                // image show here  -------------------------------------------
					$sqlData = "select id,master_tble_id,image_location from common_photo_library where master_tble_id=".$txt_job_no."";
					$data_array_img=sql_select($sqlData);
					$path=($path)?$path:'../../';
					?>
					<div>
					<? foreach($data_array_img as $inf){ ?>
						<img  src='<? echo $path.$inf[csf("image_location")]; ?>' border="1" height='200px' width='150px' />
					<?  } ?>
					</div>
                </td>
                <tr>
                    <td style="font-size:14px; "><b>Job Number</b></td>
                    <td style="font-size:14px; "><b><?=$row[csf("job_no")]; ?></b></td>
                </tr>
                <tr>
                    <td style="font-size:14px; "><b>Buyer</b></td>
                    <td style="font-size:14px; "><b><?=$buyer_arr[$row[csf("buyer_name")]]; ?></b></td>
                </tr>
                <tr>
                    <td style="font-size:14px; "><b>Order No</b></td>
                    <td style="font-size:14px; "><b><?=$job_in_orders; ?></b></td>
                </tr>
                <tr>
                    <td style="font-size:14px; "><b>Style Ref. No</b></td>
                    <td style="font-size:14px; "><b><?=$row[csf("style_ref_no")]; ?></b></td>
                </tr>
                <tr>
                    <td style="font-size:14px; "><b>Season</b></td>
                    <td style="font-size:14px; "><b><?=$season_arr[$row[csf("season_buyer_wise")]]; ?></b></td>
                </tr>
                <tr>
                    <td style="font-size:14px; "><b>Garments Item</b></td>
                    <?
                        $grmnt_items = "";
                        if($garments_item[$row[csf("gmts_item_id")]]=="")
                        {

                            $grmts_sql = sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details where job_no=$txt_job_no");
                            foreach($grmts_sql as $key=>$val){
                                $grmnt_items .=$garments_item[$val[csf("gmts_item_id")]].", ";
                            }
                            $grmnt_items = substr_replace($grmnt_items,"",-1,1);
                        }else{
                            $grmnt_items = $garments_item[$row[csf("gmts_item_id")]];
                        }
                        
                        $appStatus="No"; $appStatusTdColor="#FF7377";
                        if($row[csf("approved")]==1 || $row[csf("approved")]==3) { $appStatus="Yes";  $appStatusTdColor="#90EE90"; }
                    ?>
                    <td style="font-size:14px; "><b><?=$grmnt_items; ?></b></td>
                </tr>
                <tr>
                    <td style="font-size:14px; "><b>Original Order Qty(<?= $unit_of_measurement[$row[csf("order_uom")]]?>)</b></td>
                    <td style="font-size:14px; " ><b><?=$row[csf("job_quantity")]; ?></b></td>
                </tr>
                <tr>
                    <td style="font-size:14px; "><b>Unit Price($)</b></td>
                    <td style="font-size:14px; " ><b>$<?=fn_number_format($row[csf("avg_unit_price")],2); ?> </b></td>
                </tr>
                <tr>
                    <td style="font-size:14px; "><b>Total Value($)</b></td>
                    <td style="font-size:14px; " ><b>$<?=fn_number_format($row[csf("job_quantity")]*$row[csf("avg_unit_price")],3); ?></b></td>
                </tr>
                <tr>
                    <td style="font-size:14px; "><b>Excess Cut (%)</b></td>
                    <td style="font-size:14px; " ><b><?=fn_number_format($excess_cut,2); ?>%</b></td>
                </tr>
                <tr>
                    <td style="font-size:14px; "><b>Plan Cut Qty(<?=$unit_of_measurement[$row[csf("order_uom")]];?>)</b></td>
                    <td style="font-size:14px; " ><b><?=$po_plun_cut_qty/$total_set_qnty;?><? $plan_cut=$po_plun_cut_qty/$total_set_qnty;?></b></td>
                </tr>
                <tr>
                    <td style="font-size:14px; "><b>Costing For</b></td>
                    <td style="font-size:14px; "><b><?=$costing_per[$row[csf("costing_per")]]; ?></b></td>  
                </tr>
                <tr>
                    <td style="font-size:14px; "><b>Shipment Date</b></td>
                    <td style="font-size:14px; "><b>&nbsp;<? echo $shipment_date;?></b></td>
                </tr>
                <tr>
                    <td style="font-size:14px; "><b>Fabric Consumpiton</b></td>
                    <td style="font-size:14px; "><?= $sum_cons;?></td>
                </tr>
                <tr>
                    <td style="font-size:14px; "><b>Sales Contract Number</b></td>
                    <td style="font-size:14px; "><b><?=implode(",",array_unique(explode(",",$sc_no))); ?></b></td>
                </tr>
                <tr>
                    <td style="font-size:14px; "><b>LC Number</b></td>
                    <td style="font-size:14px; "><b><?=implode(",",array_unique(explode(",",$lc_no))); ?></b></td>
                </tr>
                </table>
        </td>
        <td>
        </td>
        <?
        }
        $nameArray_size=sql_select( "select  size_number_id,min(id) as id,	min(size_order) as size_order from wo_po_color_size_breakdown where is_deleted=0 and status_active!=0 $job_noCond2 group by size_number_id order by size_order");
        ?>
        <td width="800" style="vertical-align: top;">
                <div id="div_size_color_matrix" style="float:left; max-width:1000;">
            	<fieldset id="div_size_color_matrix" style="max-width:1000;">
 				<table  class="rpt_table"  border="1" align="left" cellpadding="0" width="750" cellspacing="0" rules="all" >
                    <tr>
                        <td colspan="<? echo count($nameArray_size)+2?>" style="border:1px solid black;background-color: #787878;" align='center'><strong>ORDER RATIO SUMMERY</strong></td>
                    </tr>
                    <tr>
                        <td style="border:1px solid black" align='center'><strong>Color/Size</strong></td>
                    <?
						foreach($nameArray_size  as $result_size)
                        {	     ?>
                        <td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
                    <?	}    ?>
                        <td style="border:1px solid black; width:130px" align="center"><strong>Total</strong></td>
                    </tr>
                    <?
					$color_size_order_qnty_array=array();
					$color_size_qnty_array=array();
					$size_tatal=array();
					$size_tatal_order=array();
					$item_size_tatal=array();
					$item_size_tatal_order=array();
					$item_grand_total=0;
					$item_grand_total_order=0;
					$nameArray_color=sql_select( "select  color_number_id,min(id) as id,min(color_order) as color_order from wo_po_color_size_breakdown where is_deleted=0 and status_active!=0 $job_noCond2 group by color_number_id  order by color_order");
					?>
                    <?
					foreach($nameArray_color as $result_color)
                    {
                    ?>
                    <tr>
                        <td align="center" style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; // echo $row_num_tr; ?></td>
                        <?
						$color_total=0;
						$color_total_order=0;

						foreach($nameArray_size  as $result_size)
						{
						$nameArray_color_size_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as  order_quantity  from wo_po_color_size_breakdown where  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$result_color[csf('color_number_id')]."  $job_noCond2 and  status_active!=0 and is_deleted =0");
						 
						foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                        {
                        ?>
                            <td style="border:1px solid black; text-align:right">
							<?
								if($result_color_size_qnty[csf('order_quantity')]!= "")
								{
									 echo number_format($result_color_size_qnty[csf('order_quantity')],0);
                                     $color_total_order += $result_color_size_qnty[csf('order_quantity')] ;
									 $grand_total_order +=$result_color_size_qnty[csf('order_quantity')];
                                     if (array_key_exists($result_size[csf('size_number_id')], $size_tatal))
                                    {
                                        $size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
                                        $size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
                                    }
                                    else
                                    {
                                    $size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
                                    $size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
                                    }
								}
								else echo "0";
							 ?>
							</td>

                    <?
						}
                        }
                        ?>
                          <td style="border:1px solid black; text-align:right"><?  echo number_format(round($color_total_order),0); ?></td>

                    </tr>
                    <?
                    }
					?>
                     <tr>
                        <td style="border:1px solid black" align="center" colspan="<? echo count($nameArray_size)+3; ?>"><strong>&nbsp;</strong></td>
                        </tr>
                    <tr>
                    <tr>
                        <td align="center" style="border:1px solid black"><strong>Grand Total</strong></td>
                        <?
						foreach($nameArray_size  as $result_size)
                        {
                        ?>
                        <td style="border:1px solid black;  text-align:right"><? echo $size_tatal_order[$result_size[csf('size_number_id')]];  ?></td>
                        <?
                        }
                        ?>
                        <td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($grand_total_order),0); ?></td>
                    </tr>
                </table>
                <br><br>
                <table  class="rpt_table"  border="1" align="left" cellpadding="0" width="750" cellspacing="0" rules="all" >
                    <tr>
                        <td colspan="<? echo count($nameArray_size)+2?>" style="border:1px solid black;background-color: #787878;" align='center'><strong>BUDGET RATIO SUMMERY</strong></td>
                    </tr>
                    <tr>
                        <td style="border:1px solid black" align='center'><strong>Color/Size</strong></td>
                    <?
						foreach($nameArray_size  as $result_size)
                        {	     ?>
                        <td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
                    <?	}    ?>
                        <td style="border:1px solid black; width:130px" align="center"><strong>Total</strong></td>
                    </tr>
                    <?
					$color_size_order_qnty_array=array();
					$color_size_qnty_array=array();
					$size_tatal=array();
					$size_tatal_order=array();
					$item_size_tatal=array();
					$item_size_tatal_order=array();
					$item_grand_total=0;
					$item_grand_total_order=0;
					$nameArray_color=sql_select( "select  color_number_id,min(id) as id,min(color_order) as color_order from wo_po_color_size_breakdown where is_deleted=0 and status_active!=0 $job_noCond2 group by color_number_id  order by color_order");
					?>
                    <?
					foreach($nameArray_color as $result_color)
                    {
                    ?>
                    <tr>
                        <td align="center" style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; // echo $row_num_tr; ?></td>
                        <?
						$color_total=0;
						$color_total_order=0;

						foreach($nameArray_size  as $result_size)
						{
						$nameArray_color_size_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as  order_quantity  from wo_po_color_size_breakdown where  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$result_color[csf('color_number_id')]."  $job_noCond2 and  status_active!=0 and is_deleted =0");
						 
						foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                        {
                        ?>
                            <td style="border:1px solid black; text-align:right">
							<?
								if($result_color_size_qnty[csf('plan_cut_qnty')]!= "")
								{
									 echo number_format($result_color_size_qnty[csf('plan_cut_qnty')],0);
                                     $color_plan__total_order += $result_color_size_qnty[csf('plan_cut_qnty')] ;
									 $grand_plan_total_order +=$result_color_size_qnty[csf('plan_cut_qnty')];
                                     if (array_key_exists($result_size[csf('size_number_id')], $size_tatal))
                                    {
                                        $size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
                                    }
                                    else
                                    {
                                    $size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
                                    }
								}
								else echo "0";
							 ?>
							</td>

                    <?
						}
                        }
                        ?>
                          <td style="border:1px solid black; text-align:right"><?  echo number_format(round($color_plan__total_order),0); ?></td>

                    </tr>
                    <?
                    }
					?>
                     <tr>
                        <td style="border:1px solid black" align="center" colspan="<? echo count($nameArray_size)+3; ?>"><strong>&nbsp;</strong></td>
                        </tr>
                    <tr>
                    <tr>
                        <td align="center" style="border:1px solid black"><strong>Grand Total</strong></td>
                        <?
						foreach($nameArray_size  as $result_size)
                        {
                        ?>
                        <td style="border:1px solid black;  text-align:right"><? echo $size_tatal[$result_size[csf('size_number_id')]];  ?></td>
                        <?
                        }
                        ?>
                        <td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($grand_plan_total_order),0); ?></td>
                    </tr>
                </table>
                </fieldset>
                </div>
                </td>
       </tr>
            
        </table>
        <?
        $order_job_qnty=$row[csf("job_quantity")];
        $avg_unit_price=$row[csf("avg_unit_price")];
	//end first foearch
	//start	all summary report here -------------------------------------------
	
	
	?>
    <br />
    <?
		//End all summary report here -------------------------------------------
		//2	All Fabric Cost part here-------------------------------------------
			?>
			<div style="margin-top:15px">
					<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
						<label><b style="background-color: yellow;"> Fabric Cost  </b></label>
							<tr style="font-weight:bold;background-color: #787878;"  align="center">
								<td width="350">FABRIC DESCRIPTION</td>
								<td width="80">Source</td>
								<td width="100">NOMINATED SUPPLIER</td>
								<td width="100">FABRIC CONSUMPTION(<? echo $costing_for;?>)</td>
								<td width="100">TOTAL CONSUMPTION</td>
								<td width="50">UNIT</td>
								<td width="50">UNIT PRICE (USD) <? echo $costing_for;?></td>
								<td width="100">TOTAL AMOUNT (USD)/<? echo $costing_for;?></td>
								<td width="100">COST (USD) <? echo $costing_for;?></td>
							</tr>
			<?
			$sum_percent_to_order_value=0;
			foreach( $FabricData as $index=>$row ) {
					$uom=$row["uom"];
					$item_descrition =$row['description'];
					$totalConsWoven=$row["totalConsWoven"];
					$totalAmountWoven=$row["totalAmountWoven"];
					$amount=$totalAmountWoven/$plan_cut;
					$percent_to_order_value=($amount/$price_dzn)*100;
					?>
					<tr>
						<td align="left"><?  echo $item_descrition;?></td>
						<td align="left"><?  echo $row["fabric_source"];?></td>
						<td align="left">
						<?  
							$nominated_supp_str="";
							$exnominated_supp=explode(",",$row['nominated_supp']);
							foreach($exnominated_supp as $supp)
							{
								if($nominated_supp_str=="") $nominated_supp_str=$supplier_name_arr[$supp]; else $nominated_supp_str.=','.$supplier_name_arr[$supp];
							}
							echo $nominated_supp_str;
							//echo $row["nominated_supp"];
						?>
							
						</td>
						<td align="right"><? echo number_format($row["avg_cons"],4);?></td>
						<td align="right"><? echo number_format($totalConsWoven,4);?></td>
						<td align="left"><?  echo $uom; ?></td>
                        <td align="right"><? echo number_format($row["rate"],4);?></td>
						<td align="right"><? echo number_format($totalAmountWoven,4);?></td>
                        <td align="right"><? echo number_format($amount,4);$sum_percent_to_order_value+=$percent_to_order_value?></td>
                        
					</tr>
					<?
					$GrandtotalConsWoven+=$totalConsWoven;
					$GrandtotalDznAmount+=$amount;
					$GrandtotalAmount+=$totalAmountWoven;
			}
			?>
				<tr class="rpt_bottom" style="font-weight:bold">
				<td colspan="4" align="left">Total</td>
				<td align="right"><? echo number_format(($GrandtotalConsWoven),4);?></td>
				<td></td>
				<td></td>
				<td align="right"><? echo number_format(($GrandtotalAmount),4);?></td>
                <td align="right"><? echo number_format(($GrandtotalDznAmount),4);?></td>
				</tr>
				</table>
				</div>
			<?
			if($zero_value==1){
				echo $woven_fab;
			}
			else{
				if($row[csf("avg_cons")]>0){
					echo $woven_fab;
				}
				else{
					echo "";
				}
			}
			//end 	All Fabric Cost part report-------------------------------------------
		//start	Trims Cost part report here -------------------------------------------
		$trim_group=return_library_array( "select item_name,id from  lib_item_group", "id", "item_name"  );
		if($zero_value==1)
		{
		?>


			<div style="margin-top:15px">
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
				<label><b style="background-color: yellow;">Trims Cost</b></label>
					<tr style="font-weight:bold;background-color: #787878;">
						<td width="150">Item Group</td>
						<td width="150">Description</td>
						<td width="150">Nominated Supp</td>
						<td width="50">UNIT</td>
						<td width="100">CONSUMPTION (<? echo $costing_for;?>)</td>
						<td width="100">TOTAL QUANTITY(<? echo $costing_for;?>)</td>
						<td width="50">UNIT PRICE (USD) <? echo $costing_for;?></td>
						<td width="80">TOTAL AMOUNT (USD) <? echo $costing_for;?></td>
						<td width="100">COST (USD) <? echo $costing_for;?></td>
					</tr>
				<?
				$TotalTrimDznAmount=0;
				$TotalTrimAmount=0;
				foreach( $TrimData as $index=>$row )
				{

				?>
					<tr>
						<td align="left"><? echo $trim_group[$row["trim_group"]]; ?></td>
						<td align="left"><? echo $row["description"]; ?></td>
						<td align="left">
						<? 
							$nominated_supp_str="";
							$exnominated_supp=explode(",",$row['nominated_supp']);
							foreach($exnominated_supp as $supp)
							{
								if($nominated_supp_str=="") $nominated_supp_str=$supplier_name_arr[$supp]; else $nominated_supp_str.=','.$supplier_name_arr[$supp];
							}
							echo $nominated_supp_str;
							//echo $row["nominated_supp"]; 
						?>
							
						<td align="left"><? echo $unit_of_measurement[$row["cons_uom"]]; ?></td>
						<td align="right"><? echo number_format($row["cons_dzn_gmts"],4); ?></td>
						<td align="right"><? echo number_format($row["tot_cons"],4); ?></td>
						<td align="right"><? echo number_format($row["rate"],4); ?></td>
						<td align="right"><? echo number_format($row["tot_amount"],4); ?></td>
                        <td align="right"><? echo number_format($row["amount"],4); ?></td>
					</tr>
				<?
					$TotalTrimDznAmount += $row["amount"];
					$TotalTrimAmount += $row["tot_amount"];
				}
				?>
					<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="7" align="left">Total</td>
						<td align="right"><? echo number_format($TotalTrimAmount,4); ?></td>
                        <td align="right"><? echo number_format($TotalTrimDznAmount,4); ?></td>
					</tr>
				</table>
		</div>
		<?
		}
		else
		{
			if($totTrim>0)
			{
		?>
		<div style="margin-top:15px">
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
				<label><b tyle="background-color: yellow;">Trims Cost</b></label>
					<tr style="font-weight:bold;background-color: #787878;">
						<td width="150">Item Group</td>
						<td width="150">Description</td>
						<td width="150">Nominated Supp</td>
						<td width="50">UNIT</td>
						<td width="100">CONSUMPTION (<? echo $costing_for;?>)</td>
						<td width="100">TOTAL QUANTITY(<? echo $costing_for;?>)</td>
						<td width="50">UNIT PRICE (USD) <? echo $costing_for;?></td>
						<td width="80">TOTAL AMOUNT (USD) <? echo $costing_for;?></td>
						<td width="100">COST (USD) <? echo $costing_for;?></td>
					</tr>
				<?
				$TotalTrimDznAmount=0;
				$TotalTrimAmount=0;
				foreach( $TrimData as $index=>$row)
				{
					$trim_group=return_library_array( "select item_name,id from  lib_item_group where id=".$row["trim_group"], "id", "item_name"  );

				?>
					<tr>
						<td align="left"><? echo $trim_group[$row["trim_group"]]; ?></td>
						<td align="left"><? echo $row["description"]; ?></td>
						<td align="left">
						<? 
							$nominated_supp_str="";
							$exnominated_supp=explode(",",$row['nominated_supp']);
							foreach($exnominated_supp as $supp)
							{
								if($nominated_supp_str=="") $nominated_supp_str=$supplier_name_arr[$supp]; else $nominated_supp_str.=','.$supplier_name_arr[$supp];
							}
							echo $nominated_supp_str;
							//echo $row["nominated_supp"]; 
						?>
							
						</td>
						<td align="left"><? echo $unit_of_measurement[$row["cons_uom"]]; ?></td>
						<td align="right"><? echo number_format($row["cons_dzn_gmts"],4); ?></td>
						<td align="right"><? echo number_format($row["tot_cons"],4); ?></td>
						<td align="right"><? echo number_format($row["rate"],4); ?></td>
						<td align="right"><? echo number_format($row["tot_amount"],4); ?></td>
                        <td align="right"><? echo number_format($row["amount"],4); ?></td>
					</tr>
				<?
					$TotalTrimDznAmount += $row["amount"];
					$TotalTrimAmount += $row["tot_amount"];
				}
				?>
					<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="7" align="left">Total</td>
						<td align="right"><? echo number_format($TotalTrimAmount,4); ?></td>
                        <td align="right"><? echo number_format($TotalTrimDznAmount,4); ?></td>
					</tr>
				</table>
		</div>
		<?
			}
			else
			{
			echo "";
			}
		}
		//End Trims Cost Part report here -------------------------------------------
		//start	Embellishment Details part report here -------------------------------------------
		if($zero_value==1)
		{
		?>
			<div style="margin-top:15px">
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
				<label><b style="background-color: yellow;">Embellishment Details</b></label>
					<tr style="font-weight:bold;background-color: #787878;">
						<td width="100">Particulars</td>
						<td width="100">Type</td>
                        <td width="100">NOMINATED SUPPLIER</td>
                        <td width="100">UNIT</td>
						<td width="100">CONSUMPTION (<? echo $costing_for;?>)</td>
						<td width="100">TOTAL QUANTITY (<? echo $costing_for;?>)</td>
						<td width="100">UNIT PRICE (USD) <? echo $costing_for;?></td>
						<td width="100">TOTAL AMOUNT (USD)</td>
						<td width="100">COST (USD) <? echo $costing_for;?></td>
					</tr>
				<?
				$TotalEmbDznAmount=0;
				$TotalEmbAmount =0;
				foreach( $EmbData as $index=>$row )
				{
					$em_type ="";
					if($row["emb_name"]==1)$em_type = $emblishment_print_type[$row["emb_type"]];
					else if($row["emb_name"]==2)$em_type = $emblishment_embroy_type[$row["emb_type"]];
					else if($row["emb_name"]==4)$em_type = $emblishment_spwork_type[$row["emb_type"]];
					else if($row["emb_name"]==5)$em_type = $emblishment_gmts_type[$row["emb_type"]];
					else if($row["emb_name"]==99)$em_type = $emblishment_other_type_arr[$row["emb_type"]];
				?>
					<tr>
						<td align="left"><? echo $emblishment_name_array[$row["emb_name"]]; ?></td>
						<td align="left"><? echo $em_type; ?></td>
                        <td align="left">
						<?  
							$nominated_supp_str="";
							$exnominated_supp=explode("_",$row['nominated_supp']);
							foreach($exnominated_supp as $supp)
							{
								 if($nominated_supp_str=="") $nominated_supp_str=$supplier_name_arr[$supp]; else $nominated_supp_str.=','.$supplier_name_arr[$supp]; 
							}
							echo $nominated_supp_str;
						?>
						</td>
                        <td align="right"><? echo $costing_for; ?></td>
						<td align="right"><? echo number_format($row["cons_dzn_gmts"],4); ?></td>
						<td align="right"><? echo number_format($row["tot_cons"],4); ?></td>
						<td align="right"><? echo number_format($row["rate"],4); ?></td>
						<td align="right"><? echo number_format($row["tot_amount"],4); ?></td>
                        <td align="right"><? echo number_format($row["amount"],4); ?></td>
					</tr>
				<?
					$TotalEmbDznAmount+= $row["amount"];
					$TotalEmbAmount += $row["tot_amount"];
				}
				?>
					<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="7" align="left">Total</td>
						<td align="right"><? echo number_format($TotalEmbAmount,4); ?></td>
                        <td align="right"><? echo number_format($TotalEmbDznAmount,4); ?></td>
					</tr>
				</table>
		</div>
		<?
		}
		else
		{
			if($totEmb>0)
			{
		?>
		<div style="margin-top:15px">
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
				<label><b style="background-color: yellow;">Embellishment Details</b></label>
					<tr style="font-weight:bold;background-color: #787878;">
                        <td width="100">Particulars</td>
						<td width="100">Type</td>
                        <td width="100">NOMINATED SUPPLIER</td>
                        <td width="100">UNIT</td>
						<td width="100">CONSUMPTION (<? echo $costing_for;?>)</td>
						<td width="100">TOTAL QUANTITY (<? echo $costing_for;?>)</td>
						<td width="100">UNIT PRICE (USD) <? echo $costing_for;?></td>
						<td width="100">TOTAL AMOUNT (USD)</td>
						<td width="100">COST (USD) <? echo $costing_for;?></td>
					</tr>
				<?
			$TotalEmbDznAmount=0;
			$TotalEmbAmount =0;
				foreach( $EmbData as $index=>$row  )
				{
					$em_type ="";
					if($row["emb_name"]==1)$em_type = $emblishment_print_type[$row["emb_type"]];
					else if($row["emb_name"]==2)$em_type = $emblishment_embroy_type[$row["emb_type"]];
					else if($row["emb_name"]==4)$em_type = $emblishment_spwork_type[$row["emb_type"]];
					else if($row["emb_name"]==5)$em_type = $emblishment_gmts_type[$row["emb_type"]];
					else if($row["emb_name"]==99)$em_type = $emblishment_other_type_arr[$row["emb_type"]];



				?>
					<tr>
                        <td align="left"><? echo $emblishment_name_array[$row["emb_name"]]; ?></td>
						<td align="left"><? echo $em_type; ?></td>
                        <td align="left">
						<?  
							$nominated_supp_str="";
							$exnominated_supp=explode("_",$row['nominated_supp']);
							foreach($exnominated_supp as $supp)
							{
								 if($nominated_supp_str=="") $nominated_supp_str=$supplier_name_arr[$supp]; else $nominated_supp_str.=','.$supplier_name_arr[$supp]; 
							}
							echo $nominated_supp_str;
						?>
						</td>
                        <td align="right"><? echo $costing_for; ?></td>
						<td align="right"><? echo number_format($row["cons_dzn_gmts"],4); ?></td>
						<td align="right"><? echo number_format($row["tot_cons"],4); ?></td>
						<td align="right"><? echo number_format($row["rate"],4); ?></td>
						<td align="right"><? echo number_format($row["tot_amount"],4); ?></td>
                        <td align="right"><? echo number_format($row["amount"],4); ?></td>
					</tr>
				<?
					$TotalEmbDznAmount += $row["amount"];
					$TotalEmbAmount += $row["tot_amount"];
				}
				?>
					<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="7" align="left">Total</td>
						<td align="right"><? echo number_format($TotalEmbAmount,4); ?></td>
                        <td align="right"><? echo number_format($TotalEmbDznAmount,4); ?></td>
					</tr>
				</table>
		</div>
		<?

			}
			else
			{
				echo "";
			}
		}
		//End Embellishment Details Part report here -------------------------------------------

        //start	Wash part report here -------------------------------------------
		if($zero_value==1)
		{
		?>
			<div style="margin-top:15px">
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
				<label><b style="background-color: yellow;">WASH/LAUNDERY</b></label>
					<tr style="font-weight:bold;background-color: #787878;">
						<td width="100">PARTICULARS</td>
						<td width="100">TYPE</td>
                        <td width="100">NOMINATED SUPPLIER</td>
                        <td width="100">UNIT</td>
						<td width="100">CONSUMPTION (<? echo $costing_for;?>)</td>
						<td width="100">TOTAL QUANTITY (<? echo $costing_for;?>)</td>
						<td width="100">UNIT PRICE (USD) <? echo $costing_for;?></td>
						<td width="100">TOTAL AMOUNT (USD)</td>
						<td width="100">COST (USD) <? echo $costing_for;?></td>
					</tr>
				<?
				$TotalWashDznAmount=0;
				$TotalWashAmount =0;
				foreach( $WashData as $index=>$row )
				{
					$em_type ="";
					if($row["emb_name"]==3)$em_type = $emblishment_wash_type[$row["emb_type"]];
				?>
					<tr>
						<td align="left"><? echo $emblishment_name_array[$row["emb_name"]]; ?></td>
						<td align="left"><? echo $em_type; ?></td>
                        <td align="left">
						<?  
							$nominated_supp_str="";
							$exnominated_supp=explode("_",$row['nominated_supp']);
							foreach($exnominated_supp as $supp)
							{
								if($nominated_supp_str=="") $nominated_supp_str=$supplier_name_arr[$supp]; else $nominated_supp_str.=','.$supplier_name_arr[$supp]; 
							}
							echo $nominated_supp_str;
						?>
						</td>
                        <td align="right"><? echo $costing_for; ?></td>
						<td align="right"><? echo number_format($row["cons_dzn_gmts"],4); ?></td>
						<td align="right"><? echo number_format($row["tot_cons"],4); ?></td>
						<td align="right"><? echo number_format($row["rate"],4); ?></td>
						<td align="right"><? echo number_format($row["tot_amount"],4); ?></td>
                        <td align="right"><? echo number_format($row["amount"],4); ?></td>
					</tr>
				<?
					$TotalWashDznAmount += $row["amount"];
					$TotalWashAmount += $row["tot_amount"];
				}
				?>
					<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="7" align="left">Total</td>
						<td align="right"><? echo number_format($TotalWashAmount,4); ?></td>
                        <td align="right"><? echo number_format($TotalWashDznAmount,4); ?></td>
					</tr>
				</table>
		</div>
		<?
		}
		else
		{
			if($totEmb>0)
			{
		?>
		<div style="margin-top:15px">
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
				<label><b style="background-color: yellow;">WASH/LAUNDERY</b></label>
					<tr style="font-weight:bold;background-color: #787878;">
                        <td width="100">PARTICULARS</td>
						<td width="100">TYPE</td>
                        <td width="100">NOMINATED SUPPLIER</td>
                        <td width="100">UNIT</td>
						<td width="100">CONSUMPTION (<? echo $costing_for;?>)</td>
						<td width="100">TOTAL QUANTITY (<? echo $costing_for;?>)</td>
						<td width="100">UNIT PRICE (USD) <? echo $costing_for;?></td>
						<td width="100">TOTAL AMOUNT (USD)</td>
						<td width="100">COST (USD) <? echo $costing_for;?></td>
					</tr>
				<?
			$TotalWashDznAmount=0;
			$TotalWashAmount =0;
				foreach( $WashData as $index=>$row  )
				{
					$em_type ="";
					if($row["emb_name"]==3)$em_type = $emblishment_wash_type[$row["emb_type"]];
				?>
					<tr>
                        <td align="left"><? echo $emblishment_name_array[$row["emb_name"]]; ?></td>
						<td align="left"><? echo $em_type; ?></td>
                        <td align="left">
						<?  
							$nominated_supp_str="";
							$exnominated_supp=explode("_",$row['nominated_supp']);
							foreach($exnominated_supp as $supp)
							{
								 if($nominated_supp_str=="") $nominated_supp_str=$supplier_name_arr[$supp]; else $nominated_supp_str.=','.$supplier_name_arr[$supp]; 
							}
							echo $nominated_supp_str;
						?>
						</td>
                        <td align="right"><? echo $costing_for; ?></td>
						<td align="right"><? echo number_format($row["cons_dzn_gmts"],4); ?></td>
						<td align="right"><? echo number_format($row["tot_cons"],4); ?></td>
						<td align="right"><? echo number_format($row["rate"],4); ?></td>
						<td align="right"><? echo number_format($row["tot_amount"],4); ?></td>
                        <td align="right"><? echo number_format($row["amount"],4); ?></td>
					</tr>
				<?
					$TotalWashDznAmount += $row["amount"];
					$TotalWashAmount += $row["tot_amount"];
				}
				?>
					<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="7" align="left">Total</td>
						<td align="right"><? echo number_format($TotalWashAmount,4); ?></td>
                        <td align="right"><? echo number_format($TotalWashDznAmount,4); ?></td>
					</tr>
				</table>
		</div>
		<?

			}
			else
			{
				echo "";
			}
		}
		//End Wash Part report here -------------------------------------------


		//start	Commercial Cost part report here -------------------------------------------
		if($zero_value==1)
		{
		?>

			<div style="margin-top:15px">
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:400px;text-align:center;" rules="all">
				<label><b style="background-color: yellow;">Commercial Cost</b></label>
					<tr style="font-weight:bold;background-color: #787878;">
						<td width="100">COMMERCIAL COST PER <? echo $costing_for ?></td>
						<td width="100">TOTAL COST</td>
					</tr>
				<?
			$TotalCmslDznAount=0;
			$TotalCmslAount =0;
				foreach( $CommarData as $index=>$row )
				{
					$TotalCmslDznAount += $row["amount"];
					$TotalCmslAount += $row["tot_amount"];
				}
				?>
					<tr class="rpt_bottom" style="font-weight:bold">
						<td align="right"><? echo number_format($TotalCmslDznAount,4); ?></td>
						<td align="right"><? echo number_format($TotalCmslAount,4); ?></td>
					</tr>
				</table>
		</div>
		<?
		}
		else
		{
			if($totCommar>0)
			{
			?>
			<div style="margin-top:15px">
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:400px;text-align:center;" rules="all">
				<label><b style="background-color: yellow;">Commercial Cost</b></label>
					<tr style="font-weight:bold;background-color: #787878;">
                        <td width="100">COMMERCIAL COST PER <? echo $costing_for ?></td>
						<td width="100">TOTAL COST</td>
					</tr>
				<?
			$TotalCmslDznAount=0;
			$TotalCmslAount =0;
				foreach( $CommarData as $index=>$row  )
				{
					$TotalCmslDznAount += $row["amount"];
					$TotalCmslAount += $row["tot_amount"];
				}
				?>
					<tr class="rpt_bottom" style="font-weight:bold">
                        <td align="right"><? echo number_format($TotalCmslDznAount,4); ?></td>
						<td align="right"><? echo number_format($TotalCmslAount,4); ?></td>
					</tr>
				</table>
		</div>
			<?
			}
			else
			{
				echo "";
			}

		}
        $TotalDznAountCommi = 0;
        $TotalAountCommi = 0;
        foreach( $CommiData as $index=>$row )
        {

             $TotalDznAountCommi += $row["commission_amount"];
             $TotalAountCommi += $row["tot_commission_amount"];
        }
		//End Commercial Cost Part report here -------------------------------------------
        //Start CM Cost Part report here -------------------------------------------
        ?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:400px;text-align:center;" rules="all">
            <label><b style="background-color: yellow;">CM Cost - IE</b></label>
                <tr style="font-weight:bold;background-color: #787878;">
                    <td width="100">CM COST PER <? echo $costing_for ?></td>
                    <td width="100">TOTAL CM COST</td>
                 </tr>
                 <tr>
                    <td width="100" align="right"><? echo number_format($summary_data[cm_cost],4);  ?></td>
                    <td width="100" align="right"><? $cm_tot_cost=$plan_cut*$summary_data[cm_cost];echo number_format($cm_tot_cost,4);//echo number_format($summary_data[cm_cost_job],4);  ?></td>
                 </tr>
                 </table>
      </div>
    <?
    //End CM Cost Part report here -------------------------------------------
    //Start Other Cost Part report here -------------------------------------------
        ?>
    <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:450px;text-align:center;" rules="all">
            <label><b style="background-color: yellow;">Others Costs</b></label>
                <tr style="font-weight:bold;background-color: #787878;">
                    <td width="150">PARTICULARS</td>
                    <td width="100">UNIT PRICE (USD) <? echo $costing_for ?> </td>
                    <td width="100">TOTAL AMOUNT (USD) </td>
                 </tr>

                <tr>
                    <td align="left">Lab Test </td>
                    <td align="right"><? echo number_format($summary_data[lab_test],4); ?></td>
                    <td align="right"><? echo number_format($summary_data[lab_test_job],4); ?></td>
                </tr>
                <tr>
                    <td align="left">Inspection Cost</td>
                    <td align="right"><? echo number_format($summary_data[inspection],4); ?></td>
                    <td align="right"><? echo number_format($summary_data[inspection_job],4); ?></td>
                </tr>

                <tr>
                    <td align="left">Freight Cost</td>
                    <td align="right"><? echo number_format($summary_data[freight],4); ?></td>
                    <td align="right"><? echo number_format($summary_data[freight_job],4); ?></td>
                </tr>
                 <tr>
                    <td align="left">Currier Cost </td>
                    <td align="right"><? echo number_format($summary_data[currier_pre_cost],4); ?></td>
                    <td align="right"><? echo number_format($summary_data[currier_pre_cost_job],4); ?></td>
                </tr>
                 <tr>
                    <td align="left">Certificate Cost </td>
                    <td align="right"><? echo number_format($summary_data[certificate_pre_cost],4); ?></td>
                    <td align="right"><? echo number_format($summary_data[certificate_pre_cost_job],4); ?></td>
                </tr>
                <tr>
                    <td align="left">Operating Expenses</td>
                    <td align="right"><? echo number_format($summary_data[common_oh],4); ?></td>
                    <td align="right"><? echo number_format($summary_data[common_oh_job],4); ?></td>
                </tr>
                  <tr>
                    <td align="left">Commission </td>
                    <td align="right"><? echo number_format($TotalDznAountCommi,4); ?></td>
                    <td align="right"><? echo number_format($TotalAountCommi,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Depreciation & Amortization </td>
                    <td align="right"><? echo number_format($summary_data[depr_amor_pre_cost],4); ?></td>
                    <td align="right"><? echo number_format($summary_data[depr_amor_pre_cost_job],4); ?></td>
                </tr>
                 <tr>
                    <td align="left">Interest</td>
                    <td align="right"><? echo fn_number_format($interest_expense,4); ?></td>
                    <td align="right"><? echo fn_number_format($interest_expense_job,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Income Tax</td>
                    <td align="right"><? echo fn_number_format($income_tax,4); ?></td>
                    <td align="right"><? echo fn_number_format($income_tax_job,4); ?></td>
                </tr>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td>Total</td>
                    <td align="right"><? echo number_format($dzn_other_components,4); ?></td>
                    <td align="right"><? echo number_format($total_other_components,4); ?></td>
                </tr>
            </table>
            <?
    //End Other Cost Part report here -------------------------------------------
	$total_summary_amount_without = $GrandtotalAmount+$TotalTrimAmount+$TotalEmbAmount+$TotalWashAmount+$TotalCmslAount+$cm_tot_cost+$total_other_components_sum;
    //Start Profit Part report here -------------------------------------------
    ?>
    <div style="margin-top:15px">
        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:500px;text-align:center;" rules="all">
        <label><b style="background-color: yellow;">PROFIT MARGINS</b></label>
            <tr style="font-weight:bold;background-color: #787878;">
                <td width="100">MARGINS (USD) <? echo $costing_for ?></td>
                <td width="100">MARGINS (USD) <? echo $costing_for ?></td>
                <td width="100">TOTAL MARGINS (USD)</td>
                <td width="100">REMARKS</td>
            </tr>
            <tr>
                <td width="100" align="right"><? echo $summary_data[margin_dzn_percent];  ?>%</td>
                <td width="100" align="right"><? echo $summary_data[margindzn];  ?></td>
                <td width="100" align="right"><? $profit_margin=$poQty*$summary_data[margindzn];echo number_format($profit_margin,4); //$profit_margin=$order_values-$total_summary_amount_without; ?></td>
                <td width="100" align="right"><? //echo number_format($summary_data[cm_cost_job],4);  ?></td>
            </tr>
            </table>
    </div>
    <?
    //End Profit Part report here -------------------------------------------
	//calcualtion----------------------------------------
	$total_summary_dzn=$GrandtotalDznAmount+$TotalTrimDznAmount+$TotalEmbDznAmount+$TotalWashDznAmount+$TotalCmslDznAount+$summary_data[cm_cost]+$dzn_other_components+$summary_data[margindzn];
	$total_summary_amount = $GrandtotalAmount+$TotalTrimAmount+$TotalEmbAmount+$TotalWashAmount+$TotalCmslAount+$summary_data[cm_cost_job]+$total_other_components_sum+$profit_margin;
	$total_summary_percent=$summary_data[fabric_percent]+$summary_data[trims_percent]+(($TotalEmbAmount/$order_values)*100)+$summary_data[wash_percent]+(($TotalCmslAount/$order_values)*100)+(($summary_data[cm_cost_job]/$order_values)*100)+(($total_other_components/$order_values)*100)+$summary_data[margin_dzn_percent];
	//calculation------------------------------------------
           
    ?>
	 <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:450px;text-align:center" rules="all">
            <label><b>Cost Summary</b></label>
                <tr style="font-weight:bold">
                    <td width="150">PARTICULARS</td>
                    <td width="100">Cost/<? echo $costing_for ?></td>
                    <td width="100">Total</td>
					<td width="100">%</td>
                 </tr>
                <tr>
                    <td align="left"> Fabric Cost</td>
                    <td align="right"><? echo number_format($GrandtotalDznAmount,4); ?></td>
                    <td align="right"><? echo number_format($GrandtotalAmount,4); ?></td>
					<td align="right"><? echo number_format($summary_data[fabric_percent],2); ?><?//=fn_number_format((($GrandtotalAmount/$order_values)*100),2);//echo  number_format($sum_percent_to_order_value,2); ?></td>
                </tr>
                <tr>
                    <td align="left">Trims</td>
                    <td align="right"><? echo number_format($TotalTrimDznAmount,4); ?></td>
                    <td align="right"><? echo number_format($TotalTrimAmount,4); ?></td>
					<td align="right"><? echo number_format($summary_data[trims_percent],2); ?><?//=fn_number_format((($TotalTrimAmount/$order_values)*100),2);?></td>
                </tr>
                <tr>
                    <td align="left">Embellishment</td>
                    <td align="right"><? echo number_format($TotalEmbDznAmount,4); ?></td>
                    <td align="right"><? echo number_format($TotalEmbAmount,4); ?></td>
					<td align="right"><?=fn_number_format((($TotalEmbAmount/$order_values)*100),2);?></td>
                </tr>
                <tr>
                    <td align="left">WASH/LAUNDERY</td>
                    <td align="right"><? echo number_format($TotalWashDznAmount,4); ?></td>
                    <td align="right"><? echo number_format($TotalWashAmount,4); ?></td>
					<td align="right"><?=fn_number_format($summary_data[wash_percent],2);?></td>
                </tr>
                <tr>
                    <td align="left">COMMERCIAL COST</td>
                    <td align="right"><? echo number_format($TotalCmslDznAount,4); ?></td>
                    <td align="right"><? echo number_format($TotalCmslAount,4); ?></td>
					<td align="right"><?=fn_number_format((($TotalCmslAount/$order_values)*100),2);?></td>
                </tr>
                <tr>
                    <td align="left">CM Cost - IE</td>
                    <td align="right"><? echo number_format($summary_data[cm_cost],4); ?></td>
                    <td align="right"><? echo number_format($cm_tot_cost,4); ?></td>
					<td align="right"><?=fn_number_format((($summary_data[cm_cost_job]/$order_values)*100),2);?></td>
                </tr>
                <tr>
                    <td align="left">Others Cost</td>
                    <td align="right"><? echo number_format($dzn_other_components,4); ?></td>
                    <td align="right"><? echo number_format($total_other_components,4); ?></td>
					<td align="right"><?=fn_number_format((($total_other_components/$order_values)*100),2);?></td>
                </tr>
				<tr>
                    <td align="left">Profit Margin</td>
                    <td align="right"><? echo number_format($summary_data[margindzn],4); ?></td>
					<td align="right"><? echo number_format($profit_margin,4); ?></td>
					<td align="right"><?= fn_number_format($summary_data[margin_dzn_percent],2);?></td>
                </tr>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_summary_dzn,4); ?></td>
                    <td align="right"><? echo number_format($total_summary_amount,4); ?></td>
					<td align="right"><? echo number_format($total_summary_percent,2);//=fn_number_format((($total_summary_amount/$order_values)*100),2);?></td>
                </tr>
            </table>
          </td>
          </tr>
          </table>
      </div>
      <br>
        <?
    $lib_designation_arr=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");
	$user_lib_designation_arr=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
	$user_lib_name_arr=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

	$mst_id=return_field_value("id as mst_id","wo_pre_cost_mst","job_no=$txt_job_no","mst_id");
	$unapprove_sql_data_array=sql_select("select b.approved_by, b.un_approved_by, b.approved_date, b.un_approved_reason, b.un_approved_date, b.approved_no,b.APPROVED,b.UN_APPROVED_REASON from approval_history b where b.mst_id=$mst_id and b.entry_form=46 order by b.id");

    foreach($unapprove_sql_data_array as $row){
        $unapprove_data_array[$row['APPROVED_BY']]=$row;
    }


	if(count($unapprove_data_array)>0)
	{
		$app_status_arr = array(0=>'Un App',1=>'Full App',2=>'Deny',3=>'Full App');
        ?>
		<table width="930" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
                <tr>
                	<th colspan="7" style="font-size:14px; font-family:'Calibri Light';word-break:break-all">Approval/Un Approval History</th>
                </tr>
                <tr>
                    <th width="30" style="font-size:14px; font-family:'Calibri Light';word-break:break-all">SL</th>
                    <th width="150" style="font-size:14px; font-family:'Calibri Light';word-break:break-all">Name</th>
                    <th width="120" style="font-size:14px; font-family:'Calibri Light';word-break:break-all">Designation</th>
                    <th width="60" style="font-size:14px; font-family:'Calibri Light';word-break:break-all">Approval Status</th>
                    <th width="60" style="font-size:14px; font-family:'Calibri Light';">Approval No</th>
                    <th width="120" style="font-size:14px; font-family:'Calibri Light';word-break:break-all">App. Date & Time</th>
                    <th style="font-size:14px; font-family:'Calibri Light';word-break:break-all">Reason For Un-Approval</th>
                </tr>
            </thead>
            <tbody>
				<?
                $i=1;
                foreach($unapprove_data_array as $row)
				{
					?>
					<tr>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=$i; ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$user_lib_name_arr[$row[csf('approved_by')]]; ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';"><?=$lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]]; ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?= $app_status_arr[$row['APPROVED']]; ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=$row[csf('approved_no')];?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><? if($row[csf('approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); else echo ""; ?></td>
                        <td style="font-size:14px; font-family:'Calibri Light';"> &nbsp; <?= $row['UN_APPROVED_REASON']; ?></td>
					</tr>
					<?
					$i++;
					//$un_approved_date= explode(" ",$row[csf('un_approved_date')]);
					//$un_approved_date=$un_approved_date[0];
					//if($un_approved_date=="") $un_approved_date="";else $un_approved_date=$un_approved_date;

					if($un_approved_date!="")
					{
						if($row[csf('un_approved_by')]!==0)  $row[csf('approved_by')]=$row[csf('un_approved_by')];
						?>
						<tr>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=$i;?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$user_lib_name_arr[$row[csf('approved_by')]];?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';"><?=$lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?='No';?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';" align="center"><?=$row[csf('approved_no')];?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><? if($row[csf('un_approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('un_approved_date')])); else echo "";?></td>
                            <td style="font-size:14px; font-family:'Calibri Light';word-break:break-all"><?=$unapproved_request_arr[$mst_id];?></td>
						</tr>
						<?
						$i++;
					}


                }
                ?>
            </tbody>
		</table>
		<?
	}
	?>
    <br>
	<div style="clear:both"></div>
        <? 
		$cbo_company_name=str_replace("'","",$cbo_company_name);
		if ($cbo_template_id != '') {
			$template_id = " and a.template_id=$cbo_template_id ";
		}
		 
		$path=($path!='')?$path:"../../";
		//$inserted_by=return_field_value("inserted_by", "wo_pre_cost_mst", "job_no='".str_replace("'","",$txt_job_no)."'");
		$job_info=sql_select("SELECT id, inserted_by from wo_pre_cost_mst where status_active=1 and is_deleted=0 and job_no='".str_replace("'","",$txt_job_no)."'");
		foreach ($job_info as $row) {
			$inserted_by=$row[csf('inserted_by')];
			$job_id=$row[csf('id')];
		}
		
	   $signature_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='user_signature'",'master_tble_id','image_location');
		$appSql="select approved_by from approval_history where entry_form=46 and mst_id = $job_id and is_signing=1";
		$appSqlRes=sql_select($appSql);
		foreach($appSqlRes as $row){
			$userSignatureArr[$row[csf('approved_by')]]=$path.$signature_arr[$row[csf('approved_by')]];	
		}
		$userSignatureArr[$inserted_by]=$path.$signature_arr[$inserted_by];

		echo signature_table(218, $cbo_company_name, "930px",$cbo_template_id,50,$inserted_by,$userSignatureArr);
		?>            
     </div>
 	<?
	//echo signature_table(109, $cbo_company_name, "930px",'','1','','');
	exit();
}

if($action == "mkt_source_cost15") //Mkt vs Source  unused
{
   	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if($txt_job_no==""){
		$job_no='';
	}else{
		$job_no=" and a.job_no=".$txt_job_no."";
	}

	if($cbo_company_name=="") {
		$company_name='';
	} else {
		 $company_name=" and a.company_name=".$cbo_company_name."";
	}

	if($cbo_buyer_name==""){
		 $cbo_buyer_name='';
	} else {
		$cbo_buyer_name=" and a.buyer_name=".$cbo_buyer_name."";
	}

	if($txt_style_ref==""){
		 $txt_style_ref='';
	} else {
		$txt_style_ref=" and a.style_ref_no=".$txt_style_ref."";
	}

	$txt_costing_date=change_date_format(str_replace("'","",$txt_costing_date),'yyyy-mm-dd','-');
	if($txt_costing_date=="") {
		$txt_costing_date='';
	} else {
		$txt_costing_date=" and b.costing_date='".$txt_costing_date."'";
	}
$txt_po_breack_down_id=str_replace("'",'',$txt_po_breack_down_id);
	if(str_replace("'",'',$txt_po_breack_down_id)==""){
		$txt_po_breack_down_id_cond='';
	}
	else{
		$txt_po_breack_down_id_cond=" and d.id in(".$txt_po_breack_down_id.")";
	}
    //if($txt_quotation_date=="") $txt_quotation_date=''; else $txt_quotation_date=" and a.quot_date ='".$txt_quotation_date."'";
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	

    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $season_name_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');
    $comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	 $user_passArr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
	 //pro_ex_factory_mst 
	 $sql_ex="select max(ex_factory_date) as ex_factory_date from pro_ex_factory_mst b,wo_po_break_down d where d.id=b.po_break_down_id and d.job_no_mst=$txt_job_no and d.status_active=1";
	  $exf_data_array=sql_select($sql_ex);
	 foreach( $exf_data_array as $row)
	 {
		 	$ex_factory_date=$row[csf("ex_factory_date")];
	 }
	  	
	$excess_cut=0;
	  $sql_excess="select b.excess_cut_perc from wo_po_color_size_breakdown b,wo_po_break_down d where d.id=b.po_break_down_id and d.job_no_mst=$txt_job_no and b.status_active=1 and d.status_active=1";
	  $excess_data_array=sql_select($sql_excess);
	 foreach( $excess_data_array as $row)
	 {
		 $excess_cut=$row[csf("excess_cut_perc")];
	 }
	  
	$sql_po="select  a.total_set_qnty as ratio,d.po_number,(d.po_quantity) as po_qnty,d.plan_cut, d.unit_price, d.pub_shipment_date,d.shipment_date,d.po_received_date,d.pack_handover_date  from wo_po_break_down d,wo_po_details_master a where   d.job_no_mst=a.job_no and d.job_no_mst=$txt_job_no and d.status_active=1 $txt_po_breack_down_id_cond";
	 $po_data_array=sql_select($sql_po);
	 	$order_job_qnty=0;$plan_cut=0;
		$leadtime_days_remian_cal="";
	 foreach( $po_data_array as $row)
	 {
		 	$po_received_dateArr.=$row[csf("po_received_date")].',';
			$pack_handover_dateArr.=$row[csf("pack_handover_date")].',';
			$shipment_date_dateArr.=$row[csf("pub_shipment_date")].',';
			$order_job_qnty+=$row[csf("po_qnty")]*$row[csf("ratio")];
			$plan_cut+=$row[csf("plan_cut")]*$row[csf("ratio")];
			$days_tot=datediff('d',$row[csf("po_received_date")],$row[csf("pub_shipment_date")])-1;
			
			 $leadtime_days_remian_cal.=$days_tot.',';
	 }
	  	   $leadtime_days_remian_calArr=rtrim($leadtime_days_remian_cal,',');
		    $leadtime_days_remian_calArr=explode(",",$leadtime_days_remian_calArr);
			 $leadtime_days_remian=max($leadtime_days_remian_calArr);
		//  echo $leadtime_days_remian_cal;
	 $po_received_dateArr=rtrim($po_received_dateArr,',');
	 $po_received_dateArr=explode(",",$po_received_dateArr);
	  $po_received_date=max($po_received_dateArr);
	 $pack_handover_dateArr=rtrim($pack_handover_dateArr,',');
	 $pack_handover_dateArr=explode(",",$pack_handover_dateArr);
	 $pack_handover_date=max($pack_handover_dateArr);
	 
	  $shipment_date_dateArr=rtrim($shipment_date_dateArr,',');
	 $shipment_date_dateActual=explode(",",$shipment_date_dateArr);
	  $shipment_dateActual=max($shipment_date_dateActual);
	 
	// $leadtime_days_remian=datediff('d',$po_received_date,$shipment_dateActual)-1;
	 
	 $sql = "select a.job_no,a.company_name,a.buyer_name,a.quotation_id,a.season_buyer_wise,a.season_year,a.style_ref_no,a.set_smv, a.gmts_item_id, a.order_uom, a.job_quantity, a.avg_unit_price,b.costing_date,b.sourcing_date, b.costing_per, c.fab_knit_req_kg, c.fab_woven_req_yds, c.fab_yarn_req_kg, a.total_set_qnty as ratio from wo_po_details_master a, wo_pre_cost_mst b,wo_pre_cost_sum_dtls c  where a.job_no=b.job_no and b.job_no=c.job_no and a.status_active=1 and a.job_no=$txt_job_no $company_name $cbo_buyer_name $txt_style_ref";
          // echo $sql;
    $data_array=sql_select($sql);
$path="../../";

    ?>
    <div style="width:1320px" align="center">  
     <style type="text/css" media="print">
   			table { page-break-inside:auto }
		
		/* p{ padding:0px !important; margin:0px !important;}*/

		</style>
      <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100"> 
               <img src='<? echo $path .''. $imge_arr[str_replace("'","",$cbo_company_name)]; ?>' height='100%' width='100%' />
               </td>
               <td width="1250">                                     
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;">
                            <b>  <?php      
                                    echo $comp[str_replace("'","",$cbo_company_name)].'<br>Sourcing Post Cost Report';
                              ?>
                              </b>
                            </td>
                        </tr>
                      </table>
                </td>       
            </tr>
       </table>
       <br>
            <?
             $order_price_per_dzn=0;
		
			foreach ($data_array as $row)
			{
				
				$avg_unit_price=$row[csf("avg_unit_price")];
				$sourcing_date=$row[csf("sourcing_date")];
			
			 
				$order_values = $order_job_qnty*$avg_unit_price;
				 if($row[csf("costing_per")]==1){$order_price_per_dzn=12;$costing_val=" DZN";}
					else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;$costing_val=" PCS";}
					else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;$costing_val=" 2 DZN";}
					else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;$costing_val=" 3 DZN";}
					else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;$costing_val=" 4 DZN";}
				
					$quot_id=$row[csf("quotation_id")];
					$sew_smv=$row[csf("set_smv")];
					$inserted_by=$user_passArr[$row[csf("inserted_by")]];
				?>
						<table align="left" border="0" cellpadding="0" cellspacing="0" style="width:450px; margin:5px;">
                        <tr>
                        <td>
                        
                        <table align="left" border="1" cellpadding="1" cellspacing="1" style="width:550px; margin:5px;" rules="all">
							<tr>
								<td width="80">Job No</td>
								<td width="80"><b><? echo $row[csf("job_no")]; ?></b></td>
								<td width="90">Costing</td>
								
								</tr>
							<tr>
                            <tr>
								<td width="80">Quotation ID</td>
								<td width="80"><b><? echo $quot_id; ?></b></td>
								<td width="100"><b><? echo $costing_per[$row[csf("costing_per")]]; ?></b></td>
							</tr>
                            <tr>
							<td> Costing Date :  </td>
							<td colspan="2"><b><? echo $row[csf("costing_date")]; ?></b></td>
							</tr>
                             
							<tr>
								<td>Buyer </td>
								<td colspan="2"><b><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></b></td>
							 </tr> 
							 <tr>
								<td>Style </td>
								<td colspan="2"><b><? echo $row[csf("style_ref_no")]; ?></b></td>
							 </tr>
							<tr>
								<td width="80">Item</td>
								<?
									if($row[csf("order_uom")]==1)
									{
									  $grmnt_items=$garments_item[$row[csf("gmts_item_id")]];
									}
									else
									{
										$gmt_item=explode(',',$row[csf("gmts_item_id")]);
										foreach($gmt_item as $key=>$val)
										{
											$grmnt_items .=$garments_item[$val].", ";
										}
									}
								?>
								<td width="100" colspan="2"><b><? echo $grmnt_items; ?></b></td>
							</tr>
							<tr>
								<td>Season</td>
								<td colspan="2"><b><? //echo $season_name_arr[$row[csf("season_buyer_wise")]];
								
								echo $season_brand = $season_name_arr[$row[csf('season_buyer_wise')]].'-'.substr( $row[csf('season_year')], -2); ?></b></td>
							 </tr>
							<tr>
								<td>P.O. Qnty</td>
								<td><b><?  echo $order_job_qnty.' Pcs';
								$offer_qty_dzn=$order_job_qnty/$order_price_per_dzn;
								 ?></b></td>
								<td  title="<? echo $offer_qty_dzn;?>"><b><? echo number_format($offer_qty_dzn,0).' '.$costing_val; ?></b></td>
							</tr>
							
							  <tr>
								<td>Plan Cut Qnty(<? echo $excess_cut.'%';?>)</td>
								<td><b><? echo $plan_cut.' Pcs';
								$plan_offer_qty_dzn=$plan_cut/$order_price_per_dzn;
								 ?></b></td>
								<td  title="<? echo $plan_offer_qty_dzn;?>"><b><? echo number_format($plan_offer_qty_dzn,0).' '.$costing_val; ?></b></td>
							</tr>
						</table>
                        </td>
              <?
			} //master part end
		//	die;
           $condition= new condition();
			if(str_replace("'","",$txt_job_no) !=''){
				$condition->job_no("=$txt_job_no");
			}
			if(str_replace("'",'',$txt_po_breack_down_id) !="")
			{
				$condition->po_id("in($txt_po_breack_down_id)");
			}
			$condition->init();
			$fabric= new fabric($condition);
			$trim= new trims($condition);
			$wash= new wash($condition);
			$emblishment= new emblishment($condition);
			//echo $fabric->getQuery();die;
			$fabric_qty_arr=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
			//print_r($fabric_qty_arr);
			$fabric_amount_arr=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
			
			$trim_amount_arr=$trim->getAmountArray_precostdtlsid();
			$trim_qty_arr=$trim->getQtyArray_by_precostdtlsid();
			
			$emblishment_qtyArr=$emblishment->getQtyArray_by_jobAndEmblishmentid();
			$emblishment_amountArr=$emblishment->getAmountArray_by_jobAndEmblishmentid();
			$wash_qtyArr=$wash->getQtyArray_by_jobAndEmblishmentid();
		//	print_r($wash_qtyArr);
			$wash_amountArr=$wash->getAmountArray_by_jobAndEmblishmentid();
	           
			$sql_determin=sql_select("select a.id,a.type from lib_yarn_count_determina_mst a where  a.status_active=1  and a.entry_form=426");
			foreach($sql_determin as $row)
			{
				$determin_type_arr[$row[csf('id')]]=$row[csf('type')];	
			}
			
			
			// $pri_fab_arr="select a.quotation_id,b.fabric_source,b.lib_yarn_count_deter_id as deter_min_id,b.fabric_description as fab_desc,b.fabric_description,b.body_part_id,b.gsm_weight,b.uom, a.rate,a.amount, (a.requirment) as requirment,a.cons, (a.pcs) as pcs,a.process_loss_percent as p_loss from wo_pri_quo_fab_co_avg_con_dtls a,wo_pri_quo_fabric_cost_dtls  b where a.wo_pri_quo_fab_co_dtls_id=b.id and a.quotation_id=b.quotation_id  and b.status_active=1 and b.is_deleted=0 and a.quotation_id=$quot_id ";//and b.fabric_source=2
			//$pri_fab_result=sql_select($pri_fab_arr);
			
			  $pre_fab_arr="select  b.id, b.job_no,b.body_part_id,b.lib_yarn_count_deter_id as deter_min_id, b.fab_nature_id, b.color_type_id, b.fabric_description as fab_desc,b.uom,b.avg_cons,b.avg_cons_yarn, b.avg_process_loss,b.construction,b.composition,b.fabric_source,b.gsm_weight, b.rate,b.amount,b.avg_finish_cons,b.sourcing_rate,b.sourcing_nominated_supp from wo_pre_cost_fabric_cost_dtls  b where  b.status_active=1 and b.is_deleted=0 and b.job_no=$txt_job_no";//and b.fabric_source=2
			$pre_fab_result=sql_select($pre_fab_arr);
			
			$summ_fob_pcs=0;$summ_fob_gross_value_amt=$summ_sourcing_tot_budget_dzn_val=0;
			foreach($pre_fab_result as $row)
			{
				$determin_type=$determin_type_arr[$row[csf('deter_min_id')]];
				//echo $determin_type.'d';
				$tot_amt=$row[csf('avg_cons')]*$row[csf('rate')];
				$fab_req_qty=$fabric_qty_arr['knit']['grey'][$row[csf("id")]][$row[csf("uom")]]+$fabric_qty_arr['woven']['grey'][$row[csf("id")]][$row[csf("uom")]];
				$fab_req_amount=$fabric_amount_arr['knit']['grey'][$row[csf("id")]][$row[csf("uom")]]+$fabric_amount_arr['woven']['grey'][$row[csf("id")]][$row[csf("uom")]];
				
				$p_fab_precost_arr[$determin_type][$row[csf('fab_desc')]]['req_qty']+=$fab_req_qty;
				$p_fab_precost_arr[$determin_type][$row[csf('fab_desc')]]['req_amount']+=$fab_req_amount;
				$p_fab_precost_arr[$determin_type][$row[csf('fab_desc')]]['cons']+=$row[csf('avg_finish_cons')];
				$p_fab_precost_arr[$determin_type][$row[csf('fab_desc')]]['tot_cons']+=$row[csf('avg_cons')];
				$p_fab_precost_arr[$determin_type][$row[csf('fab_desc')]]['amount']+=$row[csf('amount')];
				$p_fab_precost_arr[$determin_type][$row[csf('fab_desc')]]['p_loss']=$row[csf('avg_process_loss')];
				$p_fab_precost_arr[$determin_type][$row[csf('fab_desc')]]['sourcing_nominated_supp']=$row[csf('sourcing_nominated_supp')];
				$p_fab_precost_arr[$determin_type][$row[csf('fab_desc')]]['sourcing_rate']=$row[csf('sourcing_rate')];
				$p_fab_precost_arr[$determin_type][$row[csf('fab_desc')]]['fab_desc']=$row[csf('construction')].','.$row[csf('composition')];
				$p_fab_precost_arr[$determin_type][$row[csf('fab_desc')]]['uom']=$unit_of_measurement[$row[csf('uom')]];
				$p_fab_precost_tot_row+=1;	
				//Summary
				$summ_fob_pcs+=$row[csf('amount')];
				$summ_sourcing_tot_budget_dzn_val+=$fab_req_qty*$row[csf('sourcing_rate')];
				
				$summ_fob_gross_value_amt+=$fab_req_amount;
			}
		 $pre_trim_consarr="select b.id,c.trim_type,c.item_name,b.description,b.seq,b.trim_group,b.cons_dzn_gmts,b.cons_uom as uom,avg(d.excess_per) as  excess_per from wo_pre_cost_trim_cost_dtls b,lib_item_group c,wo_pre_cost_trim_co_cons_dtls d where  c.id=b.trim_group and b.id=d.wo_pre_cost_trim_cost_dtls_id and c.trim_type in(1,2) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and b.job_no=$txt_job_no group by b.id,c.trim_type,c.item_name,b.description,b.trim_group,b.cons_dzn_gmts,b.cons_uom,b.seq order by b.seq";
		$pre_trim_cons_result=sql_select($pre_trim_consarr);
			foreach($pre_trim_cons_result as $row)
			{
				$trims_type=$row[csf('trim_type')];
				
				
				$description=$row[csf('description')];
				if($description!="") $descriptionCond=", ".$description; else $descriptionCond="";
				$item_id=$row[csf('item_name')].$descriptionCond;
				if($trims_type==1) //Sewing
				{
						$p_sew_trim_precost_excess_arr[$item_id]['p_loss']+=$row[csf('excess_per')];
				}
				else
				{
						$p_fin_trim_precost_excess_arr[$item_id]['p_loss']+=$row[csf('excess_per')];;
				}
				
			}
		//	print_r($p_sew_trim_precost_excess_arr);
		
			
			$pre_trim_arr="select b.seq,b.id,c.trim_type,c.item_name,b.description,b.trim_group,b.tot_cons,b.ex_per,b.cons_dzn_gmts,b.cons_uom as uom, b.rate,b.amount,b.sourcing_rate,b.sourcing_nominated_supp from wo_pre_cost_trim_cost_dtls b,lib_item_group c where  c.id=b.trim_group and c.trim_type in(1,2) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.job_no=$txt_job_no order by b.seq";//and b.fabric_source=2
			$pre_trim_result=sql_select($pre_trim_arr);
			
			$p_sew_trim_precost_arr=$p_fin_trim_precost_arr=array();
			foreach($pre_trim_result as $row)
			{
				$trims_type=$row[csf('trim_type')];
				
				
				$description=$row[csf('description')];
				if($description!="") $descriptionCond=", ".$description; else $descriptionCond="";
				$item_id=$row[csf('item_name')].$descriptionCond;
				//$item_name_arr[$item_id]=$row[csf('item_name')].$descriptionCond;
				$req_amt=$row[csf('cons_dzn_gmts')]*$row[csf('rate')];
				
				
				
				if($trims_type==1) //Sewing
				{
					$p_sew_loss=$row[csf('ex_per')];
					$ex_tot=$row[csf('cons_dzn_gmts')]+(($row[csf('cons_dzn_gmts')]*$p_sew_loss)/100);
					
					$trim_req_qty=$trim_qty_arr[$row[csf('id')]];
					
				$trim_req_amount=$trim_amount_arr[$row[csf('id')]];
				$p_sew_trim_precost_arr[$item_id]['req_qty']+=$trim_req_qty;
				$p_sew_trim_precost_arr[$item_id]['req_amount']+=$trim_req_amount;
				$p_sew_trim_precost_arr[$item_id]['cons']+=$row[csf('tot_cons')];
				$p_sew_trim_precost_arr[$item_id]['tot_cons']+=$row[csf('cons_dzn_gmts')];
				$p_sew_trim_precost_arr[$item_id]['amount']+=$row[csf('amount')];
				$p_sew_trim_precost_arr[$item_id]['sourcing_rate']=$row[csf('sourcing_rate')];
				$p_sew_trim_precost_arr[$item_id]['sourcing_nominated_supp']=$row[csf('sourcing_nominated_supp')];
				$p_sew_trim_precost_arr[$item_id]['p_loss']=$p_sew_loss;
				//$p_sew_trim_precost_arr[$item_id]['tot_row']+=1;
				$p_sew_trim_tot_row+=1;
				$p_sew_trim_precost_arr[$item_id]['uom']=$unit_of_measurement[$row[csf('uom')]];
				$summ_fob_pcs+=$row[csf('amount')];	
				$summ_sourcing_tot_budget_dzn_val+=$trim_req_qty*$row[csf('sourcing_rate')];
				$summ_fob_gross_value_amt+=$trim_req_amount;
				}
				else //packing/Finish
				{
					$p_fin_loss=$row[csf('ex_per')];
				//	$ex_tot=$row[csf('cons_dzn_gmts')]+(($row[csf('cons_dzn_gmts')]*$p_fin_loss)/100);
				$trim_fin_req_qty=$trim_qty_arr[$row[csf('id')]];
				$trim_fin_req_amount=$trim_amount_arr[$row[csf('id')]];
				//echo $trim_fin_req_qty.'='.$row[csf('sourcing_rate')];
				$p_fin_trim_precost_arr[$item_id]['req_qty']+=$trim_fin_req_qty;
				$p_fin_trim_precost_arr[$item_id]['req_amount']+=$trim_fin_req_amount;
				$p_fin_trim_precost_arr[$item_id]['cons']+=$row[csf('tot_cons')];
				$p_fin_trim_precost_arr[$item_id]['tot_cons']+=$row[csf('cons_dzn_gmts')];
				$p_fin_trim_precost_arr[$item_id]['amount']+=$row[csf('amount')];
				if($row[csf('sourcing_rate')]>0)
				{
				$p_fin_trim_precost_arr[$item_id]['fin_sourcing_rate']=$row[csf('sourcing_rate')];
				}
				$p_fin_trim_precost_arr[$item_id]['sourcing_nominated_supp']=$row[csf('sourcing_nominated_supp')];
				$p_fin_trim_precost_arr[$item_id]['p_loss']=$p_fin_loss;
				$p_fin_trim_tot_row+=1;
				$p_fin_trim_precost_arr[$item_id]['uom']=$unit_of_measurement[$row[csf('uom')]];	
				$summ_fob_pcs+=$row[csf('amount')];
				$summ_sourcing_tot_budget_dzn_val+=$trim_fin_req_qty*$row[csf('sourcing_rate')];
				$summ_fob_gross_value_amt+=$trim_fin_req_amount;
				}
				//$summ_fob_value_pcs+=$row[csf('amount')]*$order_price_per_dzn;
				
			}
			//print_r($p_sew_trim_precost_arr2);
			
		
			 $pre_wash_arr="select b.job_no,b.id,b.emb_name,b.emb_type,b.cons_dzn_gmts, b.rate,b.amount,b.sourcing_rate,b.sourcing_nominated_supp from wo_pre_cost_embe_cost_dtls  b where  b.status_active=1 and b.is_deleted=0  and b.job_no=$txt_job_no  order by b.emb_name";//and b.fabric_source=2
			$pre_wash_result=sql_select($pre_wash_arr);
			
		//	$summ_sourcing_tot_budget_dzn_val=0;
		 
			foreach($pre_wash_result as $row)
			{
				$emb_name_id=$row[csf('emb_name')];
				$emb_type=$row[csf('emb_type')];
			
				//emblishment_embroy_type_arr
				if($emb_name_id==1) //Print type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_print_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				else if($emb_name_id==2) //embro type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_embroy_type_arr[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				else if($emb_name_id==4) //Special type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_spwork_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				else if($emb_name_id==5) //GMT type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_gmts_type[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				else if($emb_name_id==99) //GMT type
				{
					if($emb_type>0) $emb_typeCond=", ".$emblishment_other_type_arr[$emb_type];else $emb_typeCond="";
					$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
				}
				 
				$wash_req_amount=$emb_req_amount=0;
				if($row[csf('emb_name')]==3) //Wash
				{
					
						
						if($row[csf('emb_type')]>0) $wash_emb_typeCond=", ".$emblishment_wash_type[$row[csf('emb_type')]];else $wash_emb_typeCond="";
						$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$wash_emb_typeCond;
						$wash_req_qty=$wash_qtyArr[$row[csf('job_no')]][$row[csf('id')]];
						$wash_req_amount=$wash_amountArr[$row[csf('job_no')]][$row[csf('id')]];
						// echo $emb_name.'='.$wash_emb_typeCond.' <br>';
					 
				$p_wash_precost_arr[$emb_name]['req_qty']+=$wash_req_qty;
				$p_wash_precost_arr[$emb_name]['req_amount']+=$wash_req_amount;
				$p_wash_precost_arr[$emb_name]['cons']+=$row[csf('cons_dzn_gmts')];
				//$p_wash_precost_arr[$emb_name]['tot_cons']+=$row[csf('cons_dzn_gmts')];
				$p_wash_precost_arr[$emb_name]['amount']+=$row[csf('amount')];
				$p_wash_precost_arr[$emb_name]['p_loss']=$row[csf('p_loss')];
				$p_wash_precost_arr[$emb_name]['sourcing_rate']=$row[csf('sourcing_rate')];
				$p_wash_precost_arr[$emb_name]['sourcing_nominated_supp']=$row[csf('sourcing_nominated_supp')];
				$p_wash_precost_arr[$emb_name]['uom']=$unit_of_measurement[$row[csf('uom')]];
				$summ_fob_pcs+=$row[csf('amount')];
				$summ_sourcing_tot_budget_dzn_val+=$wash_req_qty*$row[csf('sourcing_rate')];
				$p_wash_tot_row+=1;	
				}
				else
				{
				$emb_req_qty=$emblishment_qtyArr[$row[csf('job_no')]][$row[csf('id')]];
				$emb_req_amount=$emblishment_amountArr[$row[csf('job_no')]][$row[csf('id')]];
				$p_embro_precost_arr[$emb_name]['req_qty']+=$emb_req_qty;
				$p_embro_precost_arr[$emb_name]['req_amount']+=$emb_req_amount;
				$p_embro_precost_arr[$emb_name]['cons']+=$row[csf('cons_dzn_gmts')];
				//$p_wash_precost_arr[$emb_name]['tot_cons']+=$row[csf('cons_dzn_gmts')];
				$p_embro_precost_arr[$emb_name]['amount']+=$row[csf('amount')];
				$p_embro_precost_arr[$emb_name]['p_loss']=$row[csf('p_loss')];
				if($row[csf('sourcing_rate')]>0)
				{
				$p_embro_precost_arr[$emb_name]['sourcing_rate']=$row[csf('sourcing_rate')];
				}
				$p_embro_precost_arr[$emb_name]['sourcing_nominated_supp']=$row[csf('sourcing_nominated_supp')];
				$p_embro_precost_arr[$emb_name]['uom']=$unit_of_measurement[$row[csf('uom')]];
				$summ_fob_pcs+=$row[csf('amount')];
				$summ_sourcing_tot_budget_dzn_val+=$emb_req_qty*$row[csf('sourcing_rate')];
				$p_embro_tot_row+=1;	
				}
				//$summ_fob_value_pcs+=$row[csf('amount')]/$order_price_per_dzn;
				$summ_fob_gross_value_amt+=$emb_req_amount+$wash_req_amount;
			}
			//echo $summ_fob_gross_value_amt.'=';
				//echo $summ_fob_pcs.'A';
				//echo $summ_fob_value_pcs.'C,';
			//wo_pri_quo_comarcial_cost_dtls 
			$sql_other = "select fabric_cost, trims_cost, embel_cost, wash_cost, comm_cost, commission, lab_test, inspection, cm_cost, freight, currier_pre_cost, certificate_pre_cost, design_cost, studio_cost, common_oh, depr_amor_pre_cost, interest_cost, incometax_cost, total_cost from wo_pre_cost_dtls where  job_no=$txt_job_no  and status_active=1 and  is_deleted=0";
			$pre_other_result=sql_select($sql_other);//$summ_fob_value_pcs=0;
			 foreach( $pre_other_result as $row )
			{
				$lab_test=($row[csf('lab_test')]/$order_price_per_dzn)*$order_job_qnty;
				$currier_pre_cost=($row[csf('currier_pre_cost')]/$order_price_per_dzn)*$order_job_qnty;
				$inspection=($row[csf('inspection')]/$order_price_per_dzn)*$order_job_qnty;
				$comarcial=($row[csf('comm_cost')]/$order_price_per_dzn)*$order_job_qnty;
				
				$freight=($row[csf('freight')]/$order_price_per_dzn)*$order_job_qnty;
				$certificate_pre_cost=($row[csf('certificate_pre_cost')]/$order_price_per_dzn)*$order_job_qnty;
				$design_pre_cost=($row[csf('design_cost')]/$order_price_per_dzn)*$order_job_qnty;
				$studio_pre_cost=($row[csf('studio_cost')]/$order_price_per_dzn)*$order_job_qnty;
				$common_oh=($row[csf('common_oh')]/$order_price_per_dzn)*$order_job_qnty;
				$depr_amor_pre_cost=($row[csf('depr_amor_pre_cost')]/$order_price_per_dzn)*$order_job_qnty;
				$interest_pre_cost=($row[csf('interest_cost')]/$order_price_per_dzn)*$order_job_qnty;
				$income_tax_pre_cost=($row[csf('incometax_cost')]/$order_price_per_dzn)*$order_job_qnty;
				
				$tot_other_for_fob_value=$lab_test+$currier_pre_cost+$inspection+$comarcial+$freight+$certificate_pre_cost+$design_pre_cost+$studio_pre_cost+$common_oh+$interest_pre_cost+$income_tax_pre_cost+$depr_amor_pre_cost;
				//echo $tot_other_for_fob_value;
				$lab_test_dzn=$row[csf('lab_test')];
				$fob_pcs=$row[csf('price_with_commn_pcs')];
				$currier_pre_cost_dzn=$row[csf('currier_pre_cost')];
				$inspection_dzn=$row[csf('inspection')];
				$comarcial_dzn=$row[csf('comm_cost')];
				echo $comarcial_dzn.'ttttttttttttttttt'.$order_price_per_dzn.'=';;
				$common_oh_dzn=$row[csf('common_oh')];
				$studio_pre_cost_dzn=$row[csf('studio_cost')];
				$design_pre_cost_dzn=$row[csf('design_cost')];
				$certificate_pre_cost_dzn=$row[csf('certificate_pre_cost')];
				
				$freight_dzn=$row[csf('freight')];
				//$comm_cost_dzn=$row[csf('comm_cost')];
				$depr_amor_pre_cost_dzn=$row[csf('depr_amor_pre_cost')];
				$income_tax_pre_cost_dzn=$row[csf('incometax_cost')];
				$interest_pre_cost_dzn=$row[csf('interest_cost')];
				
				$cm_cost_dzn=$row[csf('cm_cost')];
				$cm_cost_pcs=$row[csf('cm_cost')]/$order_price_per_dzn;
				$cm_cost_req=($row[csf('cm_cost')]/$order_price_per_dzn)*$order_job_qnty;
				$tot_cm_qty_dzn=$row[csf('cm_cost')]*$offer_qty_dzn;
				//$lab_test_dzn=$row[csf('lab_test')];
				
				$tot_other_cost_dzn=$common_oh_dzn+$studio_pre_cost_dzn+$design_pre_cost_dzn+$certificate_pre_cost_dzn+$freight_dzn+$depr_amor_pre_cost_dzn+$income_tax_pre_cost_dzn+$interest_pre_cost_dzn;
				
				$tot_other_cost=($tot_other_cost_dzn/$order_price_per_dzn)*$order_job_qnty;
				//$summ_fob_value_pcs+=($tot_other_cost_dzn+$currier_pre_cost_dzn+$lab_test_dzn+$inspection_dzn+$comarcial_dzn)*$order_price_per_dzn+$cm_cost_pcs;
				
				// echo $tot_other_for_fob_value.'m';
				$summ_fob_gross_value_amt+=$tot_other_for_fob_value+$tot_cm_qty_dzn ;
				
				$summ_fob_pcs+=$tot_other_cost_dzn+$lab_test_dzn+$currier_pre_cost_dzn+$inspection_dzn+$comarcial_dzn+$cm_cost_dzn;
				
				$summ_sourcing_tot_budget_dzn_val+=$tot_other_for_fob_value;
				 
			}
			
			//echo $summ_fob_pcs.'S';
		 	//echo $summ_fob_gross_value_amt.'H';
			//echo $common_oh_dzn.'='.$studio_pre_cost_dzn.'='.$design_pre_cost_dzn.'='.$certificate_pre_cost_dzn.'='.$freight_dzn.'='.$depr_amor_pre_cost_dzn.'='.$income_tax_pre_cost_dzn.'='.$interest_pre_cost_dzn; 
			$sql_commi = "select id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount, status_active from  wo_pre_cost_commiss_cost_dtls  where job_no=".$txt_job_no." and status_active=1";
		$result_commi=sql_select($sql_commi);

		 foreach( $result_commi as $row )
			{
				$commission_type_id=$row[csf('particulars_id')];
				$com_type_id=$row[csf('commission_base_id')];
				
				$commission_arr[$commission_type_id]['commi_req_amt']=($row[csf('commission_amount')]/$order_price_per_dzn)*$order_job_qnty;
				$commission_arr[$commission_type_id]['commi_amt']=$row[csf('commission_amount')];
				$commission_arr[$commission_type_id]['commi_amt_pcs']=$row[csf('commission_amount')]*$order_price_per_dzn;
				//$summ_fob_value_pcs+=$row[csf('commission_amount')]/$order_price_per_dzn;
				$summ_fob_gross_value_amt+=($row[csf('commission_amount')]/$order_price_per_dzn)*$order_job_qnty;
				$summ_fob_pcs+=$row[csf('commission_amount')];
				$summ_sourcing_tot_budget_dzn_val+=($row[csf('commission_amount')]/$order_price_per_dzn)*$order_job_qnty;
			} 
		
		//	$summ_fob_pcs=$summ_fob_pcs/$order_price_per_dzn;
				//echo $summ_fob_pcs.'S';
				$tot_summ_fob_pcs=$summ_fob_pcs/$order_price_per_dzn;
			
			
			$summ_tot_final_cm=($summ_fob_gross_value_amt-$summ_sourcing_tot_budget_dzn_val)/$offer_qty_dzn;
			
			
			$summ_sourcing_fob_pcs=($summ_sourcing_tot_budget_dzn_val+$tot_cm_qty_dzn)/$order_job_qnty;
			//echo $summ_tot_final_cm.'='.$tot_cm_qty_dzn.'='.$summ_sourcing_tot_budget_dzn_val;
			
			 $supplier_library_arr=return_library_array( "select a.short_name, a.id from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id   and a.is_deleted=0  and a.status_active=1 group by a.id,a.short_name order by a.short_name", "id", "short_name");

				?>
                        <td valign="top">
                        	 <table align="left" border="1" cellpadding="1" cellspacing="1" style="width:270px; margin:5px;" rules="all">
                             <caption> <b>Summary </b></caption>
							<tr>
								<td width="80"><b>Header </b></td>
                                <td width="80"><b>Pre Cost</b> </td>
								<td width="80"><b>Final Cost</b></td>
								
							</tr>
                            <tr>
								<td>Po Qty (Pcs)</td>
								<td colspan="3" title="=<? echo $order_job_qnty.' Pcs';?>"><b><? 
								//$fob_pcs=$fob_pcs;//$summ_fob_gross_value_amt/$offer_qty_dzn;
								echo  number_format($order_job_qnty,0); ?></b></td>
							 </tr> 
                             
                              <tr>
								<td>Unit Price/Pc</td>
								<td colspan="3" title="Price=<? echo $avg_unit_price;?>"><b><? 
								//$fob_pcs=$fob_pcs;//$summ_fob_gross_value_amt/$offer_qty_dzn;
								echo  number_format($avg_unit_price,4); ?></b></td>
							 </tr> 
                             <tr>
								<td>SMV/Pc</td>
								<td colspan="3" title="=<? //echo $summ_fob_gross_value_amt;?>"><b><? 
								//$fob_pcs=$fob_pcs;//$summ_fob_gross_value_amt/$offer_qty_dzn;
								echo  number_format($sew_smv,4); ?></b></td>
							 </tr> 
							<tr>
								<td>FOB/Pc</td>
								<td  title="FOBValue=<? echo $summ_fob_pcs;?>"><b><? 
								//$fob_pcs=$fob_pcs;//$summ_fob_gross_value_amt/$offer_qty_dzn;
								echo  number_format($tot_summ_fob_pcs,4); ?></b></td>
                                <td  title="Total value(<? echo $summ_sourcing_tot_budget_dzn_val+$tot_cm_qty_dzn;?>)/PO Qty Pcs"><b><? 
								//$fob_pcs=$fob_pcs;//$summ_fob_gross_value_amt/$offer_qty_dzn;
								echo  number_format($summ_sourcing_fob_pcs,4); ?></b></td>
							 </tr> 
                             <tr>
								<td>Margin/Pc(USD)</td>
								<td  title="Avg Rate-FoB Pcs=<? //echo $summ_fob_gross_value_amt;?>"><b><? 
								 $margin_pre=$avg_unit_price-$tot_summ_fob_pcs;
								 $margin_final=$avg_unit_price-$summ_sourcing_fob_pcs;
								echo  number_format($margin_pre,4); ?></b></td>
                                <td  title="Avg Rate-Sourcing FoB Pcs=<? //echo $summ_fob_gross_value_amt;?>"><b><? 
								//$fob_pcs=$fob_pcs;//$summ_fob_gross_value_amt/$offer_qty_dzn;
								echo  number_format($margin_final,4); ?></b></td>
							 </tr> 
							<tr>
								<td>CM/Dzn(USD)</td>  
								<td><b><? echo number_format($cm_cost_dzn,4); ?></b></td>
                                <td title="Gross Fob-Sourcing Budget Dzn/PO Qty Dzn"><b><? echo number_format($summ_tot_final_cm,4); ?></b></td>
							 </tr> 
                             <tr>
								<td>E.P.M(USD)</td>
								<td title="CM/Costing Per/Sew SMV"><b><? echo number_format($cm_cost_dzn/$order_price_per_dzn/$sew_smv,4); ?></b></td>
                                <td title="CM Final /Costing Per/Sew SMV"><b><? echo number_format($summ_tot_final_cm/$order_price_per_dzn/$sew_smv,4); ?></b></td>
							 </tr> 
						</table>
                        </td>
                        <td valign="top">
                        <?
                        $nameArray_imge =sql_select("SELECT image_location,real_file_name FROM common_photo_library where master_tble_id=".$txt_job_no." and file_type=1");
						//echo "SELECT image_location,real_file_name FROM common_photo_library where master_tble_id=".$txt_job_no." and file_type=1";
						?>
                <table width="210">
                <tr>
                <?
				 $path="../../";
				$img_counter = 0;
                foreach($nameArray_imge as $result_imge)
				{
				     

					?>
					<td>
						<!--<img src="../../<? //echo $result_imge[csf('image_location')]; ?>" width="90" height="100" border="2" />-->
                        <img src="<? echo $path .''. $result_imge[csf('image_location')]; ?>" width="90" height="100" border="2" />
                       <?
					  
					   ?>
					</td>
					<?

					$img_counter++;
				}
				?>
               	 </tr>
           		</table>
                        </td>
                         <td valign="top">
                        	 <table align="left" border="1" cellpadding="1" cellspacing="1" style="width:260px; margin:1px;" rules="all">
                            
                            <tr>
								<td>Order Confirmation Date	</td>
								 <td><b><? echo $po_received_date; ?></b></td>
							 </tr> 
                             
                              <tr>
								<td>Sourcing Rcvd Date	</td>
								<td><b><? echo $sourcing_date; ?></b></td>
							 </tr> 
                              <tr>
								<td>Pack Handover Date</td>
								 <td><b><? echo $pack_handover_date; ?></b></td>
							 </tr> 
                              <tr>
								<td title="Ship Date">Garments Delivery Date	</td>
								<td><b><? echo $shipment_dateActual; ?></b></td>
							 </tr> 
                              <tr>
								<td>Total Garment Lead Time	</td>
								<td><b><? if($shipment_dateActual!="") echo $leadtime_days_remian.' Days';else echo " "; ?></b></td>
							 </tr> 
                             
						</table>
                        </td>
                        </tr>
                       
                        </table>
                        <style>
						#td_boder{ border-right:solid 3px;};
						</style>
					<?
			
			 //end first foearch
			
			 
			
			?>
            <div style="">
             	<table align="left" border="1" cellpadding="0" cellspacing="0"  width="98%" style=" margin:5px;" rules="all"> 
                <tr>
                <th colspan="11" style="background-color:#963;font-size:20px;" id="td_boder"> <b>Merchandiser's Part</b></th>
                 <th colspan="4"  style="background-color: #996;font-size:20px;"> <b>Budget- Sourcing Part</b></th>
                </tr>
                 <tr>
                    <th  width="20">SL </th>
                    <th  width="100" title="Fabric">ITEM DESCRIPTION </th>
                    <th  width="70">Cons/Dzn</th>
                    <th  width="70">Wast % </th>
                    <th  width="70">Total Cons/Dzn</th>
                    <th  width="50">UOM </th>
                    <th  width="70">Req. Qty</th>
                    <th  width="70">Rate(USD)</th>
                    <th  width="70">Cost/Dzn</th>
                    <th  width="70">Cost/Pc</th>
                    <th  width="70" id="td_boder">Total <br>Budget</th>
                    
                    <th  width="70">Rate(USD)</th>
                    <th  width="70">Total Budget</th>
                    <th  width="70">Balance /<br>Excess</th>
                    <th  width="70">Supplier</th>
                   
                    </tr>
                    
                    <?
					$f=1;$tot_fab_amount=$tot_fab_amount_pcs=$tot_fab_req_amount=0;$ff=1;$tot_fab_req_sourcing_amount=$tot_fab_req_sourcing_bal_amount=0;
                    foreach($p_fab_precost_arr as $fab_type=>$fab_data)
					{
						 foreach($fab_data as $fab_desc=>$row)
						{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$nominated_supp_str=""; 
						 $exnominated_supp=explode(",",$row[('sourcing_nominated_supp')]);
						 foreach($exnominated_supp as $supp)
						 {
							if($nominated_supp_str=="") $nominated_supp_str=$supplier_library_arr[$supp]; else $nominated_supp_str.=','.$supplier_library_arr[$supp];
						 }	
                    ?>
                	<tr bgcolor="<? echo $bgcolor;?>">
                   
                    <td width="20" align="center"><p><? echo $f; ?></p></td>
                   
                    <td width="100"><div style="word-break:break-all"><? echo $fab_type.','.$row[('fab_desc')]; ?></div></td>
                    <td width="70" align="right"><p><? echo number_format($row[('cons')],4); ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($row[('p_loss')],4); ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($row[('tot_cons')],4); ?></p></td>
                    <td width="50"><p><? echo $row[('uom')]; ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($row[('req_qty')],4); ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($row[('req_amount')]/$row[('req_qty')],4); ?></p></td>
                    <td width="70"align="right"><p><? echo number_format($row[('amount')],4); ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($row[('amount')]/$order_price_per_dzn,4); ?></p></td>
                    <td width="70"  id="td_boder" align="right"><p><? echo number_format($row[('req_amount')],4); ?></p></td>
                    
                    <td width="70" align="right"><p><? echo number_format($row[('sourcing_rate')],4); ?></p></td>
                    <td width="70"align="right"><p><? $sourcing_amount=$row[('sourcing_rate')]*$row[('req_qty')];echo number_format($sourcing_amount,4); ?></p></td>
                    <td width="70" align="right" title="Marchandiser Amount-Sourcing Amount"><p><?  $bal_sourcing_amount=$row[('req_amount')]-$sourcing_amount;echo number_format($bal_sourcing_amount,4); ?></p></td>
                    <td width="70" align="center"><div style="word-break:break-all"><?  echo $nominated_supp_str; ?></div></td>
                   
                    </tr>
					<?
						$f++;$ff++;
						$tot_fab_amount+=$row[('amount')];
						$tot_fab_amount_pcs+=$row[('amount')]/$order_price_per_dzn;
						$tot_fab_req_amount+=$row[('req_amount')];
						$tot_fab_req_sourcing_amount+=$row[('sourcing_rate')]*$row[('req_qty')];
						$tot_fab_req_sourcing_bal_amount+=$bal_sourcing_amount;
						}
					}
					?>
                     
                   
                      <tr style="font-size:17px; background-color:#CCC">
                        <td colspan="8"> <b style="float:left">A Total Fabric Cost</b></td>
                        <td  align="right"><b><? echo number_format($tot_fab_amount,4); ?></b></td>
                        <td  align="right"><b><? echo number_format($tot_fab_amount_pcs,4); ?></b></td>
                        <td  align="right" id="td_boder"><b><? echo number_format($tot_fab_req_amount,4); ?></b></td>
                        <td  align="right"><b><? //echo number_format($tot_fab_amount,4); ?></b></td>
                        <td  align="right"><b><? echo number_format($tot_fab_req_sourcing_amount,4); ?></b></td>
                        <td  align="right"><b><?  echo number_format($tot_fab_req_sourcing_bal_amount,4); ?></b></td>
                        <td  align="right"><b><? //echo number_format($tot_fab_req_amount,4); ?></b></td>
                        
                    </tr>
                     
                </table>
                <br>
                <?
               // die;
				?>
                <table class="rpt_table" align="left" border="1" cellpadding="1" cellspacing="1"  width="98%" style="margin:5px;" rules="all">
                
                 <thead>
                 <tr>
                 	
                    <th  width="20">SL </th>
                    <th  width="100" title="Trim sew">ITEM DESCRIPTION </th>
                    <th  width="70">Cons/Dzn</th>
                    <th  width="70">Wast % </th>
                    <th  width="70">Total Cons/Dzn</th>
                    <th  width="50">UOM </th>
                    <th  width="70">Req. Qty</th>
                    <th  width="70">Rate(USD)</th>
                    <th  width="70">Cost/Dzn</th>
                    <th  width="70">Cost/Pc</th>
                    <th  width="70" id="td_boder">Total Budget</th>
                  
                    <th  width="70">Rate(USD)</th>
                    <th  width="70">Total Budget</th>
                    <th  width="70">Balance /<br>Excess</th>
                    <th  width="70">Supplier</th>
                    
                    </tr>
                    </thead>
                     
                    <?
					$ts=1;$tot_sew_amount=$tot_amount_pcs=$tot_sew_amount_pcs=$tot_sew_req_amount=0;$ttts=1;$tot_sew_req_sourcing_amount=$tot_sew_req_sourcing_bal_amount=0;
                    foreach($p_sew_trim_precost_arr as $item_id=>$row)
					{
						
						if($ts%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$nominated_supp_str=""; 
						 $exnominated_supp=explode(",",$row[('sourcing_nominated_supp')]);
						 foreach($exnominated_supp as $supp)
						 {
							if($nominated_supp_str=="") $nominated_supp_str=$supplier_library_arr[$supp]; else $nominated_supp_str.=','.$supplier_library_arr[$supp];
						 }		
                    ?>
                	<tr bgcolor="<? echo $bgcolor;?>">
                   
                    <td width="20" align="center"><p><? echo $ts; ?></p></td>
                    <td width="100" ><div style="word-break:break-all"><? echo $item_id; ?></div></td> 
                    <td width="70" align="right"><p><? echo number_format($row[('cons')],4); ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($row[('p_loss')],4); ?></p></td>
                    <td width="70" align="right"><p><?  if($row[('p_loss')]>0) echo number_format($row[('tot_cons')],4);else echo number_format($row[('cons')],4);?></p></td>
                    <td width="50" align="left"><p><? echo $row[('uom')]; ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($row[('req_qty')],4); ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($row[('req_amount')]/$row[('req_qty')],4); ?></p></td>
                    <td width="70"align="right"><p><? echo number_format($row[('amount')],4); ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($row[('amount')]/$order_price_per_dzn,4); ?></p></td>
                    <td width="70" id="td_boder" align="right"><p><? echo number_format($row[('req_amount')],4); ?></p></td>
                    
                    <td width="70" align="right"><p><? echo number_format($row[('sourcing_rate')],4); ?></p></td>
                    <td width="70"align="right"><p><? $sourcing_amount=$row[('sourcing_rate')]*$row[('req_qty')];echo number_format($sourcing_amount,4); ?></p></td>
                    <td width="70" align="right"><p><?  $bal_sourcing_amout=$row[('req_amount')]-$sourcing_amount;echo number_format($bal_sourcing_amout,4); ?></p></td>
                    <td width="70" align="center"><div style="word-break:break-all"><? echo $nominated_supp_str; ?></div></td>
                     
                    </tr>
					<?
						$ts++;$ttts++;
						$tot_sew_amount+=$row[('amount')];
						$tot_sew_amount_pcs+=$row[('amount')]/$order_price_per_dzn;
						$tot_sew_req_amount+=$row[('req_amount')];
						$tot_sew_req_sourcing_amount+=$row[('sourcing_rate')]*$row[('req_qty')];
						$tot_sew_req_sourcing_bal_amount+=$bal_sourcing_amout;
						
					}
					?>
                     
                     
                    <tr style="font-size:17px; background-color:#CCC">
                        <td colspan="8" align="left"><b>B-SubTotal for Sewing Trims Cost:</b></td>
                        <td  align="right"><b><? echo number_format($tot_sew_amount,4); ?></b></td>
                        <td  align="right"><b><? echo number_format($tot_sew_amount_pcs,4); ?></b></td>
                        <td  align="right" id="td_boder"><b><? echo number_format($tot_sew_req_amount,4); ?></b></td>
                        
                         <td  align="right"><b><? //echo number_format($tot_sew_amount,4); ?></b></td>
                          <td  align="right"><b><? echo number_format($tot_sew_req_sourcing_amount,4); ?></b></td>
                        <td  align="right"><b><? echo number_format($tot_sew_req_sourcing_bal_amount,4); ?></b></td>
                        <td  align="right"><b><? //echo number_format($tot_sew_req_amount,4); ?></b></td>
                       
                        
                    </tr>
                    
                     
                </table>
                 <br>
                <table align="left" border="1" cellpadding="0" cellspacing="0"  width="98%" style=" margin:5px;" rules="all"> 
                 <tr>
                 	
                    <th width="20">SL </th>
                    <th  width="100" title="Trim Fin">ITEM DESCRIPTION </th>
                    <th  width="70">Cons/Dzn</th>
                    <th width="70">Wast % </th>
                    <th width="70">Total Cons/Dzn</th>
                    <th width="50">UOM </th>
                    <th width="70">Req. Qty</th>
                    <th width="70">Rate(USD)</th>
                    <th width="70">Cost/Dzn</th>
                    <th width="70">Cost/Pc</th>
                    <th width="70" id="td_boder">Total Budget</th>
                    
                    <th  width="70">Rate(USD)</th>
                    <th  width="70">Total Budget</th>
                    <th  width="70">Balance /Excess</th>
                    <th  width="70">Supplier</th>
                    </tr>
                     
                    <?
					$tf=1;$tot_fin_amount=$tot_fin_amount_pcs=$tot_fin_req_amount=0;$tttf=1;$tot_fin_req_sourcing_amount=$tot_fin_req_sourcing_bal_amount=0;
                    foreach($p_fin_trim_precost_arr as $item_id=>$row)
					{
						
						if($tf%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$nominated_supp_str=""; 
						 $exnominated_supp=explode(",",$row[('sourcing_nominated_supp')]);
						 foreach($exnominated_supp as $supp)
						 {
							if($nominated_supp_str=="") $nominated_supp_str=$supplier_library_arr[$supp]; else $nominated_supp_str.=','.$supplier_library_arr[$supp];
						 }			
                    ?>
                	<tr bgcolor="<? echo $bgcolor;?>">                     
                    <td width="20" align="center"><p><? echo $tf; ?></p></td>
                    <td width="100"><div style="word-break:break-all"><? echo  $item_id; ?></div></td> 
                    
                    <td width="70" align="right"><p><? echo number_format($row[('cons')],4); ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($row[('p_loss')],4); ?></p></td>
                    <td width="70" align="right"><p><?   if($row[('p_loss')]>0) echo number_format($row[('tot_cons')],4);else echo number_format($row[('cons')],4); //echo number_format($row[('tot_cons')],4); ?></p></td>
                    <td width="50"><p><? echo $row[('uom')]; ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($row[('req_qty')],4); ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($row[('req_amount')]/$row[('req_qty')],2); ?></p></td>
                    <td width="70"align="right"><p><? echo number_format($row[('amount')],4); ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($row[('amount')]/$order_price_per_dzn,4); ?></p></td>
                    <td width="70" align="right" id="td_boder"><p><? echo number_format($row[('req_amount')],4); ?></p></td>
                    
                    <td width="70" align="right" title="Rate=<? echo $row[('fin_sourcing_rate')];?>"><p><? echo number_format($row[('fin_sourcing_rate')],4); ?></p></td>
                     <td width="70"align="right"><p><? $fin_sourcing_amount=$row[('fin_sourcing_rate')]*$row[('req_qty')];echo number_format($fin_sourcing_amount,4); ?></p></td>
                    <td width="70" align="right"><p><?  $fin_bal_sourcing_amout=$row[('req_amount')]-$fin_sourcing_amount;echo number_format($fin_bal_sourcing_amout,4); ?></p></td>
                    <td width="70" align="center"><div style="word-break:break-all"><? echo $nominated_supp_str; ?></div></td>
                     
                    </tr>
                    
                    
					<?
						$tf++;$tttf++;
						$tot_fin_amount+=$row[('amount')];
						$tot_fin_amount_pcs+=$row[('amount')]/$order_price_per_dzn;
						$tot_fin_req_amount+=$row[('req_amount')];
						$tot_fin_req_sourcing_amount+=$fin_sourcing_amount;
						$tot_fin_req_sourcing_bal_amount+=$fin_bal_sourcing_amout;
						
					}
					?>
                     
                    
                     <tr style="font-size:17px; background-color:#CCC">
                      
                        <td colspan="8"> <b>C-Sub Total for Finishing & Packing Trims Cost:</b></td>
                        <td  align="right"><b><? echo number_format($tot_fin_amount,4); ?></b></td>
                        <td  align="right"><b><? echo number_format($tot_fin_amount_pcs,4); ?></b></td>
                        <td  align="right" id="td_boder"><b><? echo number_format($tot_fin_req_amount,4); ?></b></td>
                        <td  align="right"><b><? //echo ($row[('req_amount')]); ?></b></td>
                      
                        <td  align="right"><b><? echo number_format($tot_fin_req_sourcing_amount,4); ?></b></td>
                        <td  align="right"><b><?  echo number_format($tot_fin_req_sourcing_bal_amount,4); ?></b></td>
                        <td  align="right"><b><? //echo number_format($tot_fin_req_amount,4); ?></b></td>
                        
                        
                    </tr>
                    
                    <tr style="font-size:17px; background-color:#CCC">
                       
                        <td colspan="8"><b>Total Trims Cost [B+C]:</b></td>
                        <td  align="right"><b><? $trim_sew_fin_amt=$tot_sew_amount+$tot_fin_amount;echo number_format($trim_sew_fin_amt,4); ?></b></td>
                        <td  align="right"><b><? $trim_sew_fin_amt_pcs=$tot_sew_amount_pcs+$tot_fin_amount_pcs; echo number_format($trim_sew_fin_amt_pcs,4); ?></b></td>
                        <td  align="right" id="td_boder"><b><? $trim_fin_req_amount=$tot_sew_req_amount+$tot_fin_req_amount; echo number_format($trim_fin_req_amount,4); ?></b></td>
                        <td  align="right"><b><? //echo ($row[('req_amount')]); ?></b></td>
                        
                        <td  align="right"><b><? $tot_sourcing_trim_sew_fin_amount=$tot_fin_req_sourcing_amount+$tot_sew_req_sourcing_amount; echo number_format($tot_sourcing_trim_sew_fin_amount,4); ?></b></td>
                        <td  align="right"><b><?  $tot_trim_source_amount_bal=$trim_fin_req_amount-$tot_sourcing_trim_sew_fin_amount;echo number_format($tot_trim_source_amount_bal,4); ?></b></td>
                        <td  align="right"><b><? //$trim_fin_req_amount=$tot_sew_req_amount+$tot_fin_req_amount; echo number_format($trim_fin_req_amount,4); ?></b></td>
                       
                        
                    </tr>
                  
                </table>
                <br>
                 <?
                // die;
				 ?>
                <table align="left" border="1" cellpadding="0" cellspacing="0"  width="98%" style=" margin:5px;" rules="all"> 
                <caption><b style="float:left">Gmts Wash</b></caption>
                 <tr>
                  
                    <th width="20">SL </th>
                    <th width="100" title="Wash ">ITEM DESCRIPTION </th>
                    <th width="70">Cons/Dzn</th>
                    <th width="70">Wast % </th>
                    <th width="70">Total Cons/Dzn</th>
                    <th width="50">UOM </th>
                    <th width="70">Req. Qty</th>
                    <th width="70"> Rate(USD)</th>
                    <th width="70">Cost/Dzn</th>
                    <th width="70">Cost/Pc</th>
                    <th width="70" id="td_boder">Total Budget</th>
                    
                    <th  width="70">Rate(USD)</th>
                    <th  width="70">Total Budget</th>
                    <th  width="70">Balance /Excess</th>
                    <th  width="70">Supplier</th>
                    
                    </tr>
                    
                    <?
					$w=1;$tot_wash_amount=$tot_wash_amount_pcs=$tot_wash_req_amount=$tot_wash_sourcing_req_amount=$tot_wash_sourcing_req_amount_bal=0;$ws=1;
                    foreach($p_wash_precost_arr as $embname_id=>$row)
					{
						
						if($w%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$wash_nominated_supp_str=""; 
						 $exnominated_suppArr=explode(",",$row[('sourcing_nominated_supp')]);
						 foreach($exnominated_suppArr as $supp)
						 {
							if($wash_nominated_supp_str=="") $wash_nominated_supp_str=$supplier_library_arr[$supp]; else $wash_nominated_supp_str.=','.$supplier_library_arr[$supp];
						 }		
                    ?>
                	<tr bgcolor="<? echo $bgcolor;?>">
                    
                    <td width="20" align="center"><p><? echo $w; ?></p></td>
                    <td width="100"><div style="word-break:break-all"><? $embname_id=explode(", ",$embname_id);echo $embname_id[1]; ?></div></td> 
                    
                    <td width="70" align="right"><p><? echo number_format($row[('cons')],4); ?></p></td>
                    <td width="70" align="right"><p><? //echo $row[('p_loss')]; ?></p></td>
                    <td width="70" align="right"><p><?  echo number_format($row[('cons')],4) ?></p></td>
                    <td width="50"><p><? echo 'Dzn'; ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($row[('req_qty')],4); ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($row[('req_amount')]/$row[('req_qty')],4); ?></p></td>
                    <td width="70"align="right"><p><? echo number_format($row[('amount')],4); ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($row[('amount')]/$order_price_per_dzn,4); ?></p></td>
                    <td width="70" align="right" id="td_boder"><p><? echo number_format($row[('req_amount')],4); ?></p></td>
                    
                    <td width="70" align="right"><p><? echo number_format($row[('sourcing_rate')],4); ?></p></td>
                    <td width="70"align="right"><p><? $sourcing_tot_amount=$row[('req_qty')]*$row[('sourcing_rate')]; echo number_format($sourcing_tot_amount,4); ?></p></td>
                    <td width="70" align="right"><p><? $sourcing_tot_amount_bal=$row[('req_amount')]-$sourcing_tot_amount;echo number_format($sourcing_tot_amount_bal,4); ?></p></td>
                    <td width="70" align="center"><div style="word-break:break-all"><? echo $wash_nominated_supp_str; ?></div></td>
                   
                    </tr>
                    
                    
					<?
						$w++;$ws++;
						$tot_wash_amount+=$row[('amount')];
						$tot_wash_amount_pcs+=$row[('amount')]/$order_price_per_dzn;
						$tot_wash_req_amount+=$row[('req_amount')];
						$tot_wash_sourcing_req_amount+=$sourcing_tot_amount;
						$tot_wash_sourcing_req_amount_bal+=$sourcing_tot_amount_bal;
						
					}
					?>
                     
                     
                    <tr style="font-size:17px; background-color:#CCC">
                         
                        <td colspan="8"> <b>D-Total Wash Cost :</b></td>
                        <td  align="right"><b><? echo number_format($tot_wash_amount,4); ?></b></td>
                        <td  align="right"><b><? echo number_format($tot_wash_amount_pcs,4); ?></b></td>
                        <td  align="right" id="td_boder"><b><? echo number_format($tot_wash_req_amount,4); ?></b></td>
                        <td  align="right"><b><? //echo ($row[('req_amount')]); ?></b></td>
                        
                         <td  align="right"><b><? echo number_format($tot_wash_sourcing_req_amount,4); ?></b></td>
                        <td  align="right"><b><? echo number_format($tot_wash_sourcing_req_amount_bal,4); ?>&nbsp;</b></td>
                        <td  align="right"><b><? //echo number_format($tot_wash_req_amount,4); ?></b></td>
                        
                    </tr>
                </table>

                <br>
               
                <table align="left" border="1" cellpadding="0" cellspacing="0"  width="98%" style=" margin:5px;" rules="all"> 
                <caption><b style="float:left">Embellishment Cost</b></caption>
                 <tr>
                 	 
                    <th width="20">SL </th>
                    <th width="100" title="">ITEM DESCRIPTION </th>
                    <th width="70">Cons/Dzn</th>
                    <th width="70">Wast % </th>
                    <th width="70">Total Cons/Dzn</th>
                    <th width="50">UOM </th>
                    <th width="70">Req. Qty</th>
                    <th width="70">Rate(USD)</th>
                    
                    <th width="70">Cost/Dzn</th>
                    <th width="70">Cost/Pc</th>
                    <th width="70" id="td_boder">Total Budget</th>
                    
                    <th  width="70">Rate(USD)</th>
                    <th  width="70">Total Budget</th>
                    <th  width="70">Balance /Excess</th>
                    <th  width="70">Supplier</th>
                    
                    </tr>
                      
                    <?
					$em=1;$tot_embro_amount=$tot_embro_amount_pcs=$tot_embro_req_amount=$tot_emb_sourcing_req_amount=$tot_emb_sourcing_req_amount_bal=0;$emb=1;
                    foreach($p_embro_precost_arr as $embname_id=>$row)
					{
						
						if($w%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$emb_nominated_supp_str=""; 
						 $emb_exnominated_suppArr=explode(",",$row[('sourcing_nominated_supp')]);
						 foreach($emb_exnominated_suppArr as $supp)
						 {
							if($emb_nominated_supp_str=="") $emb_nominated_supp_str=$supplier_library_arr[$supp]; else $emb_nominated_supp_str.=','.$supplier_library_arr[$supp];
						 }		
                    ?>
                	<tr bgcolor="<? echo $bgcolor;?>">
                     
                    <td width="20" align="center"><p><? echo $em; ?></p></td>
                    <td width="100"><div style="word-break:break-all"><? echo $embname_id; ?></div></td> 
                    
                    <td width="70" align="right"><p><? echo number_format($row[('cons')],4); ?></p></td>
                    <td width="70" align="right"><p><? //echo $row[('p_loss')]; ?></p></td>
                    <td width="70" align="right"><p><?  echo number_format($row[('cons')],4); ?></p></td>
                    <td width="50"><p><? echo 'Dzn'; ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($row[('req_qty')],4); ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($row[('req_amount')]/$row[('req_qty')],4); ?></p></td>
                    
                    <td width="70"align="right"><p><? echo number_format($row[('amount')],4); ?></p></td>
                    <td width="70" align="right"><p><? echo number_format($row[('amount')]/$order_price_per_dzn,4); ?></p></td>
                    <td width="70" align="right" id="td_boder"><b><? echo number_format($row[('req_amount')],4); ?></b></td>
                    
                     <td width="70" align="right"><p><? echo number_format($row[('sourcing_rate')],4); ?></p></td>
                    <td width="70"align="right"><p><? $emb_sourcing_tot_amount=$row[('req_qty')]*$row[('sourcing_rate')]; echo number_format($emb_sourcing_tot_amount,4); ?></p></td>
                    <td width="70" align="right"><p><? $emb_sourcing_tot_amount_bal=$row[('req_amount')]-$emb_sourcing_tot_amount;echo number_format($emb_sourcing_tot_amount_bal,4); ?></p></td>
                    <td width="70" align="center"><div style="word-break:break-all"><? echo $emb_nominated_supp_str; ?></div></td>
                    
                      
                    </tr>
					<?
						$em++;$emb++;
						$tot_embro_amount+=$row[('amount')];
						$tot_embro_amount_pcs+=$row[('amount')]/$order_price_per_dzn;
						$tot_embro_req_amount+=$row[('req_amount')];
						$tot_emb_sourcing_req_amount+=$emb_sourcing_tot_amount;
						$tot_emb_sourcing_req_amount_bal+=$emb_sourcing_tot_amount_bal;
						
					}
					?>
                     
                     
                     <tr style="font-size:17px; background-color:#CCC">
                         
                        <td colspan="8"> <b>E-Total Embellishment Cost:</b></td>
                        <td  align="right"><b><? echo number_format($tot_embro_amount,4); ?></b></td>
                        <td  align="right"><b><? echo number_format($tot_embro_amount_pcs,4); ?></b></td>
                        <td  align="right" id="td_boder"><b><? echo number_format($tot_embro_req_amount,4); ?></b></td>
                        
                        <td  align="right"><b><? //echo number_format($tot_embro_amount,4); ?></b></td>
                        <td  align="right"><b><? echo number_format($tot_emb_sourcing_req_amount,4); ?></b></td>
                        <td  align="right"><b><? echo number_format($tot_emb_sourcing_req_amount_bal,4); ?></b></td>
                        <td  align="right"><b><? //echo number_format($tot_wash_req_amount,4); ?></b></td>
                    </tr>
                   
                </table>
                <br>
                 
                <table align="left" border="1" cellpadding="0" cellspacing="0"  width="98%" style=" margin:5px;" rules="all"> 
               		 <caption><b style="float:left">Others Components</b></caption>
                 <tr>
                 	 
                    <th width="20">SL </th>
                    <th title="520"><b>Others Components</b> </th>
                   
                    <th width="70"><b>Cost/Dzn</b></th>
                    <th width="70"><b>Cost/Pc</b></th>
                    <th width="70" id="td_boder"><b>Total Budget</b></th>
                    
                    <th  width="70">Rate(USD)</th>
                    <th  width="70">Total Budget</th>
                    <th  width="70">Balance /Excess</th>
                    <th  width="70">&nbsp;</th>
                    </tr>
                    
                    <?
					//$em=1;$tot_embro_amount=$tot_embro_amount_pcs=$tot_embro_req_amount=0;$emb=1;
						$bgcolor="#E9F3FF";
						$bgcolor2="#FFFFFF";//currier_pre_cost_dzn
					 
                      
                   // $tot_other_cost=0;
					$tot_other_cost_first=$tot_other_cost+$comarcial+$inspection+$currier_pre_cost+$lab_test;
					
						$total_other_cost_dzn=$tot_other_cost_dzn+$comarcial_dzn+$inspection_dzn+$lab_test_dzn+$currier_pre_cost_dzn;
						 $tot_other_cost_pcs=$tot_other_cost_dzn/$order_price_per_dzn;
						// echo $comarcial_dzn.'ttttttttttttttttt'.$order_price_per_dzn.'=';;
						 $tot_comarcial_dzn_pcs=$comarcial_dzn/$order_price_per_dzn;
						 $tot_inspection_dzn_pcs=$inspection_dzn/$order_price_per_dzn;
						  $currier_pre_cost_dzn_pcs=$currier_pre_cost_dzn/$order_price_per_dzn;
						  $tot_lab_test_dzn_pcs=$lab_test_dzn/$order_price_per_dzn;
						 
						$total_other_cost_pcs=$tot_other_cost_pcs+$tot_comarcial_dzn_pcs+$tot_lab_test_dzn_pcs+$tot_inspection_dzn_pcs+$currier_pre_cost_dzn_pcs;
						
						
						
						$tot_other_cost_req_amount=$tot_other_cost_first;
						
					
					?>
                	<tr bgcolor="<? echo $bgcolor;?>">
                    
                    <td width="20" align="center"><p>1</p></td>
                    <td width="520" align="" ><b>Test Charge</b></td> 
                    <td width="70"align="right"><p><? echo number_format($lab_test_dzn,4); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><?   echo number_format($tot_lab_test_dzn_pcs,4); ?>&nbsp;</p></td>
                    <td width="70" align="right" id="td_boder"><p><? echo number_format($lab_test,4); ?>&nbsp;</p></td>
                    
                    <td width="70" align="right"><p><? //echo number_format($lab_test,4); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? echo number_format($lab_test,4); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? echo '0'; ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? //echo number_format($lab_test,4); ?>&nbsp;</p></td>
                     
                    
                    </tr>
                    <tr>
                     <td width="20" align="center" ><p>2</p></td>
                    <td width="520"><b>Courier Charge</b></td> 
                    <td width="70"align="right"><p><? echo number_format($currier_pre_cost_dzn,4); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><?  echo number_format($currier_pre_cost_dzn_pcs,4); ?>&nbsp;</p></td>
                    <td width="70" align="right" id="td_boder"><p><? echo number_format($currier_pre_cost,4); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? //echo number_format($lab_test,4); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? echo number_format($currier_pre_cost,4); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><?  echo '0'; ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? //echo number_format($currier_pre_cost,4); ?>&nbsp;</p></td>
                      
                    </tr>
                    
                    <tr bgcolor="<? echo $bgcolor2;?>">
                    
                    <td width="20" align="center" ><p>3</p></td>
                    <td width="520"><b>Inspection Charge</b></td> 
                    <td width="70"align="right"><p><? echo number_format($inspection_dzn,4); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? echo number_format($tot_inspection_dzn_pcs,4); ?>&nbsp;</p></td>
                    <td width="70" align="right" id="td_boder"><p><? echo number_format($inspection,4); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? //echo number_format($lab_test,4); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? echo number_format($inspection,4); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><?  echo '0'; ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? //echo number_format($inspection,4); ?>&nbsp;</p></td>
                     
                    </tr>
                    
                    <tr bgcolor="<? echo $bgcolor;?>">
                    
                    <td width="20" align="center" ><p>4</p></td>
                    <td width="520"><b>Commercial Charge</b></td> 
                    <td width="70"align="right"><p><? echo number_format($comarcial_dzn,4); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><?  echo number_format($tot_comarcial_dzn_pcs,4); ?>&nbsp;</p></td>
                    <td width="70" align="right" id="td_boder"><p><? echo number_format($comarcial,4); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? //echo number_format($lab_test,4); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? echo number_format($comarcial,4); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? echo '0'; ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? //echo number_format($comarcial,4); ?>&nbsp;</p></td>
                       
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2;?>">
                     
                    <td width="20" align="center"><p>5</p></td>
                    <td width="520" title="Freight+Certif.Cost+Design Cost+Studio Cost+Deprec.&Amort.+Operating Expenses+Deprec.&Amort.+Interest+Income Tax">
                    <b>Others Charge</b></td> 
                   
                     <td width="70"align="right"><p><? echo number_format($tot_other_cost_dzn,4); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? echo number_format($tot_other_cost_pcs,4); ?>&nbsp;</p></td>
                    <td width="70" align="right" id="td_boder"><p><? echo number_format($tot_other_cost,4); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? //echo number_format($lab_test,4); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? echo number_format($tot_other_cost,4); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? echo '0'; ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? //echo number_format($lab_test,4); ?>&nbsp;</p></td>
                     
                    </tr>
					
                   
                     
                     <tr style="font-size:17px; background-color:#CCC">
                         
                        <td width="540" colspan="2"> <b>F-Total Others Cost:</b></td>
                        <td  align="right"><b><? echo number_format($total_other_cost_dzn,4); ?>&nbsp;</b></td>
                        <td  align="right"><b><? echo number_format($total_other_cost_pcs,4); ?>&nbsp;</b></td>
                        <td  align="right" id="td_boder"><b><? echo number_format($tot_other_cost_req_amount,4); ?>&nbsp;</b></td>
                        
                        
                        <td  align="right"><b><? //echo ($row[('req_amount')]); ?>&nbsp;</b></td>
                        <td  align="right"><b><? echo number_format($tot_other_cost_req_amount,4); ?>&nbsp;</b></td>
                        <td  align="right"><b><?   echo '0';?>&nbsp;</b></td>
                        <td  align="right"><b><? //echo ($row[('req_amount')]); ?>&nbsp;</b></td>
                        
                    </tr>
                   
                </table>
                <br>
             
                 <table align="left" border="1" cellpadding="0" cellspacing="0"  width="98%" style=" margin:5px;" rules="all"> 
               		 <caption><b style="float:left"> Commission Cost:</b></caption>
                   <tr>
                 	 
                    <th  width="20">SL </th>
                    <th width="520" title="">Commission Cost </th>
                    <th width="70">Cost/Dzn</th>
                    <th  width="70">Cost/Pc</th>
                    <th  width="70" id="td_boder">Total Budget</th>
                    <th  width="70">Rate(USD)</th>
                    <th  width="70">Total Budget</th>
                    <th  width="70">Balance /Excess</th>
                    <th  width="70">&nbsp;</th>
                    
                    </tr>
                   
                    <?
                     
						$tot_commission_amount=$commission_arr[1]['commi_amt']+$commission_arr[2]['commi_amt'];
						$tot_commission_amount_pcs=($commission_arr[1]['commi_amt']/$order_price_per_dzn)+($commission_arr[2]['commi_amt']/$order_price_per_dzn);
						$tot_commision_req_amount=$commission_arr[1]['commi_req_amt']+$commission_arr[2]['commi_req_amt'];
						
					
					?>
					 
                   <tr bgcolor="<? echo $bgcolor;?>">
                    
                    <td width="20" align="center"><p>1</p></td>
                    <td width="520"  title="Local"><b>UK Office Commission</b></td> 
                    <td width="70"align="right"><p><? echo number_format($commission_arr[2]['commi_amt'],4); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? echo number_format($commission_arr[2]['commi_amt']/$order_price_per_dzn,4); ?>&nbsp;</p></td>
                    <td width="70" align="right" id="td_boder"><p><? echo number_format($commission_arr[2]['commi_req_amt'],4); ?>&nbsp;</p></td>
                    
                     <td width="70" align="right"><p><? //echo number_format($lab_test,4); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><?  echo number_format($commission_arr[2]['commi_req_amt'],4); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? echo '0'; ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? //echo number_format($lab_test,4); ?>&nbsp;</p></td>
                     
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2;?>">
                     
                    <td width="20" align="center"><p>2</p></td>
                    <td width="520"><b>Buying Commission</b></td> 
                    <td width="70"align="right"><p><? echo number_format($commission_arr[1]['commi_amt'],4); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? echo number_format($commission_arr[1]['commi_amt']/$order_price_per_dzn,4); ?>&nbsp;</p></td>
                    <td width="70" align="right" id="td_boder"><p><? echo number_format($commission_arr[1]['commi_req_amt'],4); ?>&nbsp;</p></td>
                     <td width="70" align="right"><p><? //echo number_format($lab_test,4); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? echo number_format($commission_arr[1]['commi_req_amt'],4); ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? echo '0'; ?>&nbsp;</p></td>
                    <td width="70" align="right"><p><? //echo number_format($lab_test,4); ?>&nbsp;</p></td>
                     
                    </tr>
					
                    <tr>
                         
                        <td width="540" colspan="2"> <b>G-Total Commission Cost:</b></td>
                        <td  align="right"><b><? echo number_format($tot_commission_amount,4); ?>&nbsp;</b></td>
                        <td  align="right"><b><? echo number_format($tot_commission_amount_pcs,4); ?>&nbsp;</b></td>
                        <td  align="right" id="td_boder"><b><? echo number_format($tot_commision_req_amount,4); ?>&nbsp;</b></td>
                        <td width="70" align="right"><b><? //echo number_format($lab_test,4); ?>&nbsp;</b></td>
                   		 <td width="70" align="right"><b><? echo number_format($tot_commision_req_amount,4); ?>&nbsp;</b></td>
                    	<td width="70" align="right"><b><? echo '0'; ?>&nbsp;</b></td>
                    	<td width="70" align="right"><b><? //echo number_format($lab_test,4); ?>&nbsp;</b></td>
                        
                    </tr>
                    
                    <tr bgcolor="<? echo $bgcolor;?>">
                    
                    <td width="20" align="center"><p>1</p></td>
                    <td width="520"><b>H-Total CM Cost</b></td> 
                    <td width="70"align="right"><b><? echo number_format($cm_cost_dzn,4); ?>&nbsp;</b></td>
                    <td width="70" align="right"><b><? $tot_cm_cost_pcs=$cm_cost_dzn/$order_price_per_dzn;echo number_format($tot_cm_cost_pcs,4); ?>&nbsp;</b></td>
                    <td width="70" title="CM Dzn*PO Qty Dzn" id="td_boder" align="right"><b><? echo number_format($tot_cm_qty_dzn,4); ?>&nbsp;</b></td>
                    <td width="70" align="left"  title="Commision+OtherCost+Wash+Emblishmnet+Trims+Fabric">
                    <p><b>Total Final Amount</b></p></td>
                   	<td width="70" title="Commision+OtherCost+Wash+Emblishmnet+Trims+Fabric" align="right"><b> <? $total_final_amount=$tot_commision_req_amount+$tot_other_cost_req_amount+$tot_wash_sourcing_req_amount+$tot_emb_sourcing_req_amount+$tot_sourcing_trim_sew_fin_amount+$tot_fab_req_sourcing_amount;
					 echo number_format($total_final_amount,4); ?>&nbsp;</b></td>
                    <td width="70" align="right"><b><? 
					$total_final_amount_bal=$tot_fab_req_sourcing_bal_amount+$tot_emb_sourcing_req_amount_bal+$tot_wash_sourcing_req_amount_bal+$tot_trim_source_amount_bal;
					echo number_format($total_final_amount_bal,4); ?>&nbsp;</b></td>
                    <td width="70" align="right"><p><? //echo number_format($lab_test,4); ?>&nbsp;</p></td>
                    </tr>
                    
                    <tr bgcolor="<? echo $bgcolor;?>">
                    <td width="540" colspan="2"><p> <b>Gross FOB Value [A+B+C+D+E+F+G+H]</b></p></td> 
                    <td width="70"align="right"><b><?  //tot_embro_amount_pcs tot_embro_req_amount
					$gross_fob_value_dzn=$tot_fab_amount+$trim_sew_fin_amt+$tot_wash_amount+$tot_embro_amount+$tot_other_cost_dzn+$tot_commission_amount+$cm_cost_dzn;
					$gross_fob_value_pcs=$tot_fab_amount_pcs+$trim_sew_fin_amt_pcs+$tot_wash_amount_pcs+$tot_embro_amount_pcs+$tot_other_cost_pcs+$tot_commission_amount_pcs+$tot_cm_cost_pcs;
					//echo $tot_fab_amount_pcs.'=='.$trim_sew_fin_amt_pcs.'=='.$tot_wash_amount_pcs.'=='.$tot_embro_amount_pcs.'=='.$tot_commission_amount_pcs.'=='.$tot_cm_cost_pcs;
					//$gross_fob_value_req=$tot_fab_req_amount+$trim_fin_req_amount+$tot_wash_req_amount+$tot_embro_req_amount+$tot_commision_req_amount+$tot_other_cost_req_amount+$cm_cost_req;
					
					$gross_fob_value_req=$tot_fab_req_amount+$trim_fin_req_amount+$tot_wash_req_amount+$tot_embro_req_amount+$tot_other_cost_req_amount+$tot_commision_req_amount+$tot_cm_qty_dzn;

					
					 //---fob value
					 $total_fob_gross_pcs=$tot_fab_amount_pcs+$trim_sew_fin_amt_pcs+$tot_wash_amount_pcs+$tot_embro_amount_pcs+$tot_other_cost_pcs+$tot_commission_amount_pcs+$tot_cm_cost_pcs;
$total_fob_gross_dzn=$tot_fab_amount+$trim_sew_fin_amt+$tot_wash_amount+$tot_embro_amount+$total_other_cost_dzn+$tot_commission_amount+$cm_cost_dzn;
					$tot_gross_fob_pcs=$total_fob_gross_dzn/$order_price_per_dzn;
					 $gross_fob_value_dzn_without_commi=$total_fob_gross_dzn-$tot_commission_amount;
					$gross_fob_value_pcs_without_commi=$tot_gross_fob_pcs-$tot_commission_amount_pcs;
					$gross_fob_value_req_without_commi=$gross_fob_value_req-$tot_commision_req_amount;
					
					echo number_format($total_fob_gross_dzn,4); ?>&nbsp;</b></td>
                    <td width="70" align="right"><b><? echo number_format($tot_gross_fob_pcs,4); ?>&nbsp;</b></td>
                    <td width="70" align="right" id="td_boder"><b><? echo number_format($gross_fob_value_req,4); ?>&nbsp;</b></td>
                     
                     <td width="70" align="left"><b>Total Final CM Amount</b></td>
                   	<td width="70" align="right" title="Gross FOB Value[A+B+C+D+E+F+G+H]-Total Final Amount"><b><? 
					$total_final_cm_amount=$gross_fob_value_req-$total_final_amount;echo number_format($total_final_cm_amount,4); ?>&nbsp;</b></td>
                    <td width="70" align="right"><b><? //echo number_format($gross_fob_value_req,4); ?>&nbsp;</b></td>
                      <td width="70" align="right"><p><? //echo number_format($lab_test,4); ?>&nbsp;</p></td>
                   
                    </tr>
                    <?

                   
					?>
                     <tr bgcolor="<? echo $bgcolor2;?>">
                    <td width="540" colspan="2"><b>Net FOB Value (Without Commission)</b></td> 
                    <td width="70"align="right"><b><? echo number_format($gross_fob_value_dzn_without_commi,4); ?> </b></td>
                    <td width="70" align="right"><b><? echo number_format($gross_fob_value_pcs_without_commi,4); ?> </b></td>
                    <td width="70" align="right" id="td_boder"><b><? echo number_format($gross_fob_value_req_without_commi,4); ?> </b></td>
                   
                    <td width="70" align="left"><b>Final CM [Dzn]</b></td>
                   	<td width="70" align="right" title="Total Final CM Amount/PO Qty Dzn"><b><? echo number_format($total_final_cm_amount/$offer_qty_dzn,4); ?>&nbsp;</b></td>
                    <td width="70" align="right"><b><? //echo number_format($gross_fob_value_req_without_commi,4); ?>&nbsp;</b></td>
                      <td width="70" align="right"><b><? //echo number_format($lab_test,4); ?>&nbsp;</b></td>
                    
                    </tr>
                    
                     <tr bgcolor="<? echo $bgcolor;?>">
                    <td width="540" colspan="2"><b></b></td> 
                    <td width="70"align="right"><b><? //echo number_format($gross_fob_value_dzn_without_commi,4); ?> </b></td>
                    <td width="70" align="right"><b><? //echo number_format($gross_fob_value_pcs_without_commi,4); ?> </b></td>
                    
                    <td width="140" colspan="2" align="right"><b>Total Value</b></td>
                   	<td width="70" title="CM tot Cost+Tot Final Amount" align="right"><b><? echo number_format($tot_cm_qty_dzn+$total_final_amount,4); ?>&nbsp;</b></td>
                    <td width="70" align="right"><b><? //echo number_format($gross_fob_value_req_without_commi,4); ?>&nbsp;</b></td>
                      <td width="70" align="right"><b><? //echo number_format($lab_test,4); ?>&nbsp;</b></td>
                    </tr>
                </table>
             
              
           </div>
          
         
           <? 
		   
		   $cbo_company_name=str_replace("'","",$cbo_company_name);
		   echo signature_table(219, $cbo_company_name, "1320px"); ?>
            
     </div>
    
      <div style="clear:both"></div>
     
    <?
	exit();
}


if($action=="Rpt_old")// unused
{
	///extract($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$txt_costing_date=change_date_format(str_replace("'","",$txt_costing_date),'yyyy-mm-dd','-');
	if($txt_job_no=="") $job_no=''; else $job_no=" and a.job_no=".$txt_job_no."";
	if($cbo_company_name=="") $company_name=''; else $company_name=" and a.company_name=".$cbo_company_name."";
	if($cbo_buyer_name=="") $cbo_buyer_name=''; else $cbo_buyer_name=" and a.buyer_name=".$cbo_buyer_name."";
	if($txt_style_ref=="") $txt_style_ref=''; else $txt_style_ref=" and a.style_ref_no=".$txt_style_ref."";
	if($txt_costing_date=="") $txt_costing_date=''; else $txt_costing_date=" and b.costing_date='".$txt_costing_date."'";

	//array for display name
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if($db_type==0)
	{
	  $sql = "select a.job_no,a.company_name, a.buyer_name,a.style_ref_no, a.gmts_item_id,a.order_uom, a.avg_unit_price, b.costing_per, c.fab_knit_req_kg, c.fab_woven_req_yds, c.fab_yarn_req_kg,sum(d.plan_cut) as job_quantity,sum(d.po_quantity) as ord_qty
			from wo_po_details_master a, wo_pre_cost_mst b,wo_pre_cost_sum_dtls c,wo_po_break_down d
			where a.job_no=b.job_no and b.job_no=c.job_no and c.job_no=d.job_no_mst and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $job_no $company_name $cbo_buyer_name $txt_style_ref $txt_costing_date group by a.job_no,a.company_name, a.buyer_name,a.style_ref_no, a.gmts_item_id,a.order_uom, a.avg_unit_price, b.costing_per, c.fab_knit_req_kg, c.fab_woven_req_yds, c.fab_yarn_req_kg order by a.job_no";
	}
	if($db_type==2)
	{
	  $sql = "select a.job_no,a.company_name, a.buyer_name,a.style_ref_no, a.gmts_item_id,a.order_uom, a.avg_unit_price, b.costing_per, c.fab_knit_req_kg, c.fab_woven_req_yds, c.fab_yarn_req_kg,sum(d.plan_cut) as job_quantity,sum(d.po_quantity) as ord_qty
			from wo_po_details_master a, wo_pre_cost_mst b,wo_pre_cost_sum_dtls c,wo_po_break_down d
			where a.job_no=b.job_no and b.job_no=c.job_no and c.job_no=d.job_no_mst and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $job_no $company_name $cbo_buyer_name $txt_style_ref group by a.job_no,a.company_name, a.buyer_name,a.style_ref_no, a.gmts_item_id,a.order_uom, a.avg_unit_price, b.costing_per, c.fab_knit_req_kg, c.fab_woven_req_yds, c.fab_yarn_req_kg  order by a.job_no"; //a.job_quantity as job_quantity,
	}
	//echo $sql;die;
	$data_array=sql_select($sql);


	?>
    <div style="width:850px; font-size:20px; font-weight:bold" align="center"><? echo $comp[str_replace("'","",$cbo_company_name)]; ?></div>
    <div style="width:850px; font-size:14px; font-weight:bold" align="center">BOM Report</div>
	<?
	foreach ($data_array as $row)
	{
		$order_price_per_dzn=0;
		$order_job_qnty=0;
		$ord_qty=0;
		$avg_unit_price=0;
		$order_values = $row[csf("job_quantity")]*$row[csf("avg_unit_price")];
		$result =sql_select("select po_number,pub_shipment_date from wo_po_break_down where job_no_mst=$txt_job_no and status_active=1 and is_deleted=0 order by pub_shipment_date DESC");
		$job_in_orders = '';$pulich_ship_date='';
		foreach ($result as $val)
		{
			$job_in_orders .= $val[csf('po_number')].", ";
			$pulich_ship_date = $val[csf('pub_shipment_date')];
		}
		$job_in_orders = substr(trim($job_in_orders),0,-1);
		?>
            	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px" rules="all">
                	<tr>
                    	<td width="80">Job Number</td>
                        <td width="80"><b><? echo $row[csf("job_no")]; ?></b></td>
                        <td width="90">Buyer</td>
                        <td width="100"><b><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></b></td>
                        <td width="80">Plan Cut Qnty</td>
                        <?
							$grmnt_items = "";
							if($garments_item[$row[csf("gmts_item_id")]]=="")
							{

								$grmts_sql = sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details where job_no=$txt_job_no");
								foreach($grmts_sql as $key=>$val){
									$grmnt_items .=$garments_item[$val[csf("gmts_item_id")]].", ";
								}
								$grmnt_items = substr_replace($grmnt_items,"",-1,1);
							}else{
								$grmnt_items = $garments_item[$row[csf("gmts_item_id")]];
							}

						?>
                        <td width="100"><b><? echo $row[csf("job_quantity")]." ".$unit_of_measurement[$row[csf("order_uom")]]; ?></b></td>
                    </tr>
                    <tr>
                        <td>Order Qty</td>
                        <td><b><? echo $row[csf("ord_qty")]." ".$unit_of_measurement[$row[csf("order_uom")]]; ?></b></td>
                    	<td>Style Ref. No</td>
                        <td><b><? echo $row[csf("style_ref_no")]; ?></b></td>

                        <td>Price Per Unit</td>
                        <td><b><? echo $row[csf("avg_unit_price")]; ?></b></td>
                    </tr>
                    <tr>
                    	<td>Order Numbers</td>
                        <td colspan="5"><? echo $job_in_orders; ?></td>
                    </tr>
                    <tr>
                    	<td>Garments Item</td>
                        <td colspan="3"><b><? echo $grmnt_items; ?></b></td>
                        <td>Shipment Date</td>
                        <td><b><? echo change_date_format($pulich_ship_date); ?></b></td>
                    </tr>
                </table>

            <?
			if($row[csf("costing_per")]==1){$order_price_per_dzn=12;$costing_for=" DZN";}
			else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;$costing_for=" PCS";}
			else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;$costing_for=" 2 DZN";}
			else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;$costing_for=" 3 DZN";}
			else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;$costing_for=" 4 DZN";}
			$order_job_qnty=$row[csf("job_quantity")];
			$avg_unit_price=$row[csf("avg_unit_price")];
			$ord_qty=$row[csf("ord_qty")];
	}//end first foearch




	//2 Fabric Cost part here-------------------------------------------
	$sql = "select id, job_no,item_number_id, body_part_id, fab_nature_id, color_type_id, fabric_description, avg_cons, fabric_source, rate, amount,avg_finish_cons,status_active            from wo_pre_cost_fabric_cost_dtls
			where job_no=".$txt_job_no."";
	$data_array=sql_select($sql);

		$knit_fab="";$woven_fab="";
		$knit_subtotal_amount=0;
		$woven_subtotal_amount=0;
        $i=2;$j=2;
		foreach( $data_array as $row )
        {
			    $set_item_ratio=return_field_value("set_item_ratio"," wo_po_details_mas_set_details", "job_no='".$row[csf('job_no')]."' and gmts_item_id='".$row[csf('item_number_id')]."'");

			   $fincons=0;
			   $greycons=0;
			   $order_qty_fab=0;
			   $fab_dtls_data=sql_select("select po_break_down_id,color_number_id,gmts_sizes,cons,requirment from wo_pre_cos_fab_co_avg_con_dtls where pre_cost_fabric_cost_dtls_id=".$row[csf("id")]." and cons !=0");
			   foreach($fab_dtls_data as $fab_dtls_data_row )
			   {
					 $sql_po_qty_fab=sql_select("select sum(c.plan_cut_qnty) as order_quantity  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id=".$fab_dtls_data_row[csf('po_break_down_id')]." and item_number_id='".$row[csf('item_number_id')]."' and size_number_id='".$fab_dtls_data_row[csf('gmts_sizes')]."' and  color_number_id= '".$fab_dtls_data_row[csf('color_number_id')]."' and a.status_active=1 and b.status_active=1 and c.status_active=1");

					 list($sql_po_qty_row_fab)=$sql_po_qty_fab;
	                 $po_qty_fab=$sql_po_qty_row_fab[csf('order_quantity')];
					 //$cons+=($fab_dtls_data_row[csf("cons")]/($order_price_per_dzn*$set_item_ratio))*($po_qty_fab*$set_item_ratio);
					 //echo "(".$po_qty_fab."/(".$order_price_per_dzn."*".$set_item_ratio."))*".$fab_dtls_data_row[csf("cons")]."<br/>";
					 $fincons+=($po_qty_fab/($order_price_per_dzn*$set_item_ratio))*$fab_dtls_data_row[csf("cons")];
					 $greycons+=($po_qty_fab/($order_price_per_dzn*$set_item_ratio))*$fab_dtls_data_row[csf("requirment")];
					 $order_qty_fab+=$po_qty_fab;
			   }
			//$row[csf("avg_cons")] = $greycons;
			//$row[csf("avg_finish_cons")] = $fincons;
 			$row[csf("amount")] = ($row[csf("amount")]/($order_price_per_dzn*$set_item_ratio))*($order_qty_fab*$set_item_ratio);


			if($row[csf("fab_nature_id")]==2)//knit fabrics
			{
				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")];

 				$i++;
                $knit_fab .= '<tr>
                    <td align="left">'.$item_descrition.'</td>
                    <td align="left">'.$fabric_source[$row[csf("fabric_source")]].'</td>
                    <td align="right">'.number_format($greycons,4).'</td>
 					<td align="right">'.number_format($fincons,4).'</td>
                    <td align="right">'.number_format($row[csf("rate")],4).'</td>
                    <td align="right">'.number_format($row[csf("amount")],4).'</td>
                </tr>';
				$knit_subtotal_avg_cons+=$greycons;
				$knit_subtotal_avg_finish_cons+=$fincons;
            	$knit_subtotal_amount+=$row[csf("amount")];
			}
			if($row[csf("fab_nature_id")]==3)//woven fabrics
			{
				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")];
				$j++;
                 $woven_fab .= '<tr>
                    <td align="left">'.$item_descrition.'</td>
                    <td align="left">'.$fabric_source[$row[csf("fabric_source")]].'</td>
                    <td align="right">'.number_format($greycons,4).'</td>
 					<td align="right">'.number_format($fincons,4).'</td>
                    <td align="right">'.number_format($row[csf("rate")],4).'</td>
                    <td align="right">'.number_format($row[csf("amount")],4).'</td>
                </tr>';
				$woven_subtotal_avg_cons+=$greycons;
				$woven_subtotal_avg_finish_cons+=$fincons;
				$woven_subtotal_amount+=$row[csf("amount")];
			}
        }

		$knit_fab= '<div style="margin-top:15px">
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
						<tr style="font-weight:bold"  align="center">
							<td width="80" rowspan="'.$i.'" ><div class="verticalText"><b>Knit Fabric</b></div></td>
							<td width="300">Description</td>
							<td width="100">Source</td>
							<td width="100">Gray Fabric Qnty</td>
							<td width="100">Finish Fab Qnty</td>
 							<td width="50">Rate</td>
							<td width="50">Amount</td>
						</tr>'.$knit_fab;
		$woven_fab = '<tr><td colspan="7">&nbsp;</td></tr><tr>
						<td width="80" rowspan="'.$j.'"><div class="verticalText"><b>Woven Fabric</b></div></td></tr>'.$woven_fab;

		//knit fabrics table here
		$knit_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2">Total</td>
						<td align="right">'.number_format($knit_subtotal_avg_cons,4).'</td>
						<td align="right">'.number_format($knit_subtotal_avg_finish_cons,4).'</td>
						<td align="right"></td>
						<td align="right">'.number_format($knit_subtotal_amount,4).'</td>
					</tr>';
  		echo $knit_fab;

		//woven fabrics table here
		$woven_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2">Total</td>
						<td align="right">'.number_format($woven_subtotal_avg_cons,4).'</td>
						<td align="right">'.number_format($woven_subtotal_avg_finish_cons,4).'</td>
						<td align="right"></td>
						<td align="right">'.number_format($woven_subtotal_amount,4).'</td>
					</tr>
   					</table></div>';
        echo $woven_fab;

		//end 	All Fabric Cost part report-------------------------------------------


		//Start	Yarn Cost part report here -------------------------------------------
		$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
		//oracle
				$sql = "select min(id) as id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, min(cons_ratio) as cons_ratio, sum(cons_qnty) as cons_qnty, rate, sum(amount) as amount from wo_pre_cost_fab_yarn_cost_dtls where job_no=".$txt_job_no." group by count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, rate";
		        $data_array=sql_select($sql);
		?>
        <div style="margin-top:15px">
        	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="70" rowspan="<? echo count($data_array)+2; ?>"><div class="verticalText"><b>Yarn Cost</b></div></td>
                    <td width="350">Yarn Desc</td>
                    <td width="100">Yarn Qnty</td>
                    <td width="100">Rate</td>
                    <td width="100">Amount</td>
                </tr>
			<?
            $total_yarn_amount = 0;
			foreach( $data_array as $row )
            {
				if($row[csf("percent_one")]==100)
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$yarn_type[$row[csf("type_id")]];
            	else
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$composition[$row[csf("copm_two_id")]]." ".$row[csf("percent_two")]."% ".$yarn_type[$row[csf("type_id")]];

 				$row[csf("cons_qnty")] = $row[csf("cons_qnty")]/$order_price_per_dzn*$order_job_qnty;
				$row[csf("amount")] = $row[csf("amount")]/$order_price_per_dzn*$order_job_qnty;
			?>
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
				 $total_yarn_amount +=$row[csf("amount")];

            }
            ?>
            	<tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="3">Total</td>
                    <td align="right"><? echo number_format($total_yarn_amount,4); ?></td>
                </tr>
        	</table>
      </div>
      <?

	//End Yarn Cost part report here -------------------------------------------

	//start	Conversion Cost to Fabric report here -------------------------------------------
   	 $sql = "select a.id,a.fabric_description as pre_cost_fabric_cost_dtls_id, a.job_no, a.cons_process, a.req_qnty, a.charge_unit, a.amount,a.color_break_down, a.status_active,b.body_part_id,b.fab_nature_id,b.color_type_id,b.fabric_description,b.item_number_id
			from wo_pre_cost_fab_conv_cost_dtls a left join wo_pre_cost_fabric_cost_dtls b on a.job_no=b.job_no and a.fabric_description=b.id
			where a.job_no=".$txt_job_no."";
	$data_array=sql_select($sql);

 	?>

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="80" rowspan="<? echo count($data_array)+2; ?>"><div class="verticalText"><b>Conversion Cost to Fabric</b></div></td>
                    <td width="350">Particulars</td>
                    <td width="100">Process</td>
                    <td width="100">Required</td>
                    <td width="100">Rate</td>
                    <td width="100">Amount</td>
                </tr>
            <?
            $total_conversion_cost=0;
            foreach( $data_array as $row )
            {

			    $color_id_string="";
				$color_break_down_arr=explode("__",$row[csf("color_break_down")]);
				for($co=0; $co<=count($color_break_down_arr); $co++)
				{
					$color_break_down_arr_row=explode("_",$color_break_down_arr[$co]);


					//for($cow=0; $cow<=count($color_break_down_arr_row);  $cow++)
					//{
						if($color_break_down_arr_row[1] !=0)
						{
				          $color_id_string.=$color_break_down_arr_row[0].",";
						}
					//}
				}
				$color_id_string=rtrim($color_id_string,",");
				if($color_id_string =="")
				{
					$color_cond="";
				}
				else
				{
				  $color_cond="and c.color_number_id in(".$color_id_string.")";
				}

				$po_break_down_id_string="";
				if($row[csf("pre_cost_fabric_cost_dtls_id")] ==0)
				{
					//$po_data_array=sql_select("Select distinct po_break_down_id from  wo_pre_cos_fab_co_avg_con_dtls where job_no='".$row[csf("job_no")]."' and cons !=0");
					$po_data_array=sql_select("select c.plan_cut_qnty/a.total_set_qnty as order_quantity  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls f where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=f.job_no  and b.id=c.po_break_down_id and b.id=f.po_break_down_id and c.po_break_down_id=f.po_break_down_id and c.item_number_id=d.item_number_id and a.job_no='".$row[csf("job_no")]."' and f.cons !=0   $color_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 group by c.id,c.plan_cut_qnty,a.total_set_qnty");
					$po_qty_con=0;
					foreach($po_data_array as $po_data_array_row)
					{
					  $po_qty_con+=$po_data_array_row[csf('order_quantity')];
					}

 				$row[csf("req_qnty")] = $row[csf("req_qnty")]/$order_price_per_dzn*($po_qty_con);
				$row[csf("amount")] = $row[csf("amount")]/$order_price_per_dzn*($po_qty_con);
				$item_descrition = "All Fabrics";
				}
				else
				{
					$po_data_array=sql_select("Select distinct po_break_down_id from  wo_pre_cos_fab_co_avg_con_dtls where pre_cost_fabric_cost_dtls_id=".$row[csf("pre_cost_fabric_cost_dtls_id")]." and cons !=0");
					foreach($po_data_array as $po_data_array_row)
					{
					$po_break_down_id_string.=$po_data_array_row[csf('po_break_down_id')].",";
					}

					$sql_po_qty_con=sql_select("select sum(c.plan_cut_qnty) as order_quantity  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in(".rtrim($po_break_down_id_string,",").") and c.item_number_id='".$row[csf('item_number_id')]."' $color_cond and a.status_active=1 and b.status_active=1 and c.status_active=1");
				 list($sql_po_qty_row_con)=$sql_po_qty_con;
	             $po_qty_con=$sql_po_qty_row_con[csf('order_quantity')];

				$set_item_ratio=return_field_value("set_item_ratio"," wo_po_details_mas_set_details", "job_no='".$row[csf('job_no')]."' and gmts_item_id='".$row[csf('item_number_id')]."'");
 				$row[csf("req_qnty")] = $row[csf("req_qnty")]/$order_price_per_dzn*($po_qty_con/$set_item_ratio);
				$row[csf("amount")] = $row[csf("amount")]/$order_price_per_dzn*($po_qty_con/$set_item_ratio);
				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")];
				}


			?>
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="left"><? echo $conversion_cost_head_array[$row[csf("cons_process")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("req_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("charge_unit")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_conversion_cost += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="4">Total</td>
                    <td align="right"><? echo $total_conversion_cost; ?></td>
                </tr>
            </table>
      </div>
      <?
	//End Conversion Cost to Fabric report here -------------------------------------------



	//start	Trims Cost part report here -------------------------------------------
   	$sql = "select id, job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active
			from wo_pre_cost_trim_cost_dtls
			where job_no=".$txt_job_no."";
	$data_array=sql_select($sql);
 	?>


        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Trims Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Item Group</td>
                    <td width="150">Description</td>
                    <td width="150">Brand/Supp Ref</td>
                    <td width="100">UOM</td>
                    <td width="100">Consumption</td>
                    <td width="100">Rate</td>
                    <td width="100">Amount</td>
                </tr>
            <?
            $total_trims_cost=0;
            foreach( $data_array as $row )
            {
			   $order_qty_tr=0;
			   $dtls_data=sql_select("select po_break_down_id,cons,country_id from wo_pre_cost_trim_co_cons_dtls where wo_pre_cost_trim_cost_dtls_id=".$row[csf("id")]." and cons !=0");
			   foreach($dtls_data as $dtls_data_row )
			   {
				   if($dtls_data_row[csf('country_id')]==0)
					 {
						 $txt_country_cond="";
					 }
					 else
					 {
						 $txt_country_cond ="and c.country_id in (".$dtls_data_row[csf('country_id')].")";
					 }

					 $sql_po_qty=sql_select("select sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id=".$dtls_data_row[csf('po_break_down_id')]."  $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty");
	                 list($sql_po_qty_row)=$sql_po_qty;
	                 $po_qty=$sql_po_qty_row[csf('order_quantity_set')];
					 $order_qty_tr+=$po_qty;
			   }
				$row[csf("cons_dzn_gmts")] = $row[csf("cons_dzn_gmts")]/$order_price_per_dzn*$order_qty_tr;
				$row[csf("amount")] = $row[csf("amount")]/$order_price_per_dzn*$order_qty_tr;
 				$trim_group=return_library_array( "select item_name,id from  lib_item_group where id=".$row[csf("trim_group")], "id", "item_name" );
			?>
                <tr>
                    <td align="left"><? echo $trim_group[$row[csf("trim_group")]]; ?></td>
                    <td align="left"><? echo $row[csf("description")]; ?></td>
                    <td align="left"><? echo $row[csf("brand_sup_ref")]; ?></td>
                    <td align="left"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_trims_cost += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="6">Total</td>
                    <td align="right"><? echo number_format($total_trims_cost,4); ?></td>
                </tr>
            </table>
      </div>
      <?
	 //End Trims Cost Part report here -------------------------------------------



	 //start	Embellishment Details part report here -------------------------------------------
    	$sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active
			from wo_pre_cost_embe_cost_dtls
			where job_no=".$txt_job_no."";
	$data_array=sql_select($sql);
	?>


        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Embellishment Details</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Type</td>
                    <td width="150">Gmts. Qnty (Dzn)</td>
                    <td width="100">Rate</td>
                    <td width="100">Amount</td>
                 </tr>
            <?
            $total_embellishment_amt=0;
            foreach( $data_array as $row )
            {
 				$em_type ="";
				//$emblishment_name_array=array(1=>"Printing",2=>"Embroidery",3=>"Wash",4=>"Special Works",5=>"Others");
 				if($row[csf("emb_name")]==1)$em_type = $emblishment_print_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==2)$em_type = $emblishment_embroy_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==3)$em_type = $emblishment_wash_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==4)$em_type = $emblishment_spwork_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==5)$em_type = $emblishment_gmts_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==99)$em_type = $emblishment_other_type_arr[$row[csf("emb_type")]];
				$row[csf("cons_dzn_gmts")] = $row[csf("cons_dzn_gmts")]/$order_price_per_dzn*$order_job_qnty;
				$row[csf("amount")] = $row[csf("amount")]/$order_price_per_dzn*$order_job_qnty;
			?>
                <tr>
                    <td align="left"><? echo $emblishment_name_array[$row[csf("emb_name")]]; ?></td>
                    <td align="left"><? echo $em_type; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_embellishment_amt += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="4">Total</td>
                    <td align="right"><? echo number_format($total_embellishment_amt,4); ?></td>
                </tr>
            </table>
      </div>
      <?
	 //End Embellishment Details Part report here -------------------------------------------



	 //start	Commercial Cost part report here -------------------------------------------
   	$sql = "select id, job_no, item_id, rate, amount, status_active
			from  wo_pre_cost_comarci_cost_dtls
			where job_no=".$txt_job_no."";
	$data_array=sql_select($sql);
	?>

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commercial Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Rate In %</td>
                    <td width="100">Amount</td>
                 </tr>
            <?
            $total_commercial_cost=0;
            foreach( $data_array as $row )
            {
				$row[csf("amount")] = $row[csf("amount")]/$order_price_per_dzn*$order_job_qnty;
  			?>
                <tr>
                    <td align="left"><? echo $camarcial_items[$row[csf("item_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_commercial_cost += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="2">Total</td>
                    <td align="right"><? echo number_format($total_commercial_cost,4); ?></td>
                </tr>
            </table>
      </div>
      <?
	 //End Commercial Cost Part report here -------------------------------------------


  	//start	Commission Cost part report here -------------------------------------------
   	$sql = "select id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount, status_active
			from  wo_pre_cost_commiss_cost_dtls
			where job_no=".$txt_job_no."";
	$data_array=sql_select($sql);
	?>


        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commission Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Commission Basis</td>
                    <td width="100">Rate</td>
                    <td width="100">Amount</td>
                 </tr>
            <?
            $total_commission_cost=0;
            foreach( $data_array as $row )
            {
				$row[csf("commission_amount")] = $row[csf("commission_amount")]/$order_price_per_dzn*$order_job_qnty;
  			?>
                <tr>
                    <td align="left"><? echo $commission_particulars[$row[csf("particulars_id")]]; ?></td>
                    <td align="left"><? echo $commission_base_array[$row[csf("commission_base_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("commision_rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("commission_amount")],4); ?></td>
                </tr>
            <?
                 $total_commission_cost += $row[csf("commission_amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="3">Total</td>
                    <td align="right"><? echo number_format($total_commission_cost,4); ?></td>
                </tr>
            </table>
      </div>
      <?
	//End Commission Cost Part report here -------------------------------------------


	//start	Other Components part report here -------------------------------------------
   	$sql = "select id,job_no,costing_per_id,order_uom_id,fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,common_oh,common_oh_percent,total_cost,total_cost_percent,price_dzn,price_dzn_percent,margin_dzn,margin_dzn_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche
			from wo_pre_cost_dtls
			where job_no=".$txt_job_no."";
	$data_array=sql_select($sql);
	?>

        <div style="margin-top:15px">
         <table>
         <tr>
         <td>
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:450px;text-align:center;" rules="all">
            <label><b>Others Components</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Amount</td>
                 </tr>
            <?
            $total_other_components=0;
			$lab_test = 0;
			$inspection = 0;
			$cm_cost = 0;
			$freight = 0;
			$common_oh = 0;
            foreach( $data_array as $row )
            {
				$lab_test = $row[csf("lab_test")]/$order_price_per_dzn*$order_job_qnty;
				$inspection = $row[csf("inspection")]/$order_price_per_dzn*$order_job_qnty;
				$cm_cost = $row[csf("cm_cost")]/$order_price_per_dzn*$order_job_qnty;
				$freight = $row[csf("freight")]/$order_price_per_dzn*$order_job_qnty;
				$common_oh = $row[csf("common_oh")]/$order_price_per_dzn*$order_job_qnty;
   			?>
                <tr>
                    <td align="left"s>Lab Test </td>
                    <td align="right"><? echo number_format($lab_test,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Inspection Cost</td>
                    <td align="right"><? echo number_format($inspection,4); ?></td>
                </tr>
                <tr>
                    <td align="left">CM Cost - IE</td>
                    <td align="right"><? echo number_format($cm_cost,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Freight Cost</td>
                    <td align="right"><? echo number_format($freight,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Office OH</td>
                    <td align="right"><? echo number_format($common_oh,4); ?></td>
                </tr>
            <?
                 $total_other_components += $lab_test+$inspection+$cm_cost+$freight+$common_oh;
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td>Total</td>
                    <td align="right"><? echo number_format($total_other_components,4); ?></td>
                </tr>
            </table>
            </td>
            <td valign="top" rowspan="2">

            <?
     	 // image show here  -------------------------------------------
		 $sql = "select id,master_tble_id,image_location
				from common_photo_library
				where master_tble_id=$txt_job_no limit 1";
		$data_array=sql_select($sql);
 	  ?>
          <div style="margin:15px 5px;float:right;width:500px" >
          	<? foreach($data_array AS $inf){ ?>
                <img  src='../../<? echo $inf[csf("image_location")]; ?>' height='400' width='300' />
            <?  } ?>
          </div>
          </td>
          </tr>
          <tr>
          <td>
           <?

	 $total_summary_amount = 0;
	 $total_summary_amount = $total_commission_cost+$total_commercial_cost+$total_embellishment_amt+$total_trims_cost+$total_conversion_cost+$total_yarn_amount +$woven_subtotal_amount+$knit_subtotal_amount+$lab_test+$inspection+$cm_cost+$freight+$common_oh;

	 ?>
	 <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:450px;text-align:center;" rules="all">
            <label><b>Summary</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Cost Summary</td>
                    <td width="100">Total</td>
                 </tr>
                 <tr>
                    <td align="left">Knit Fabric (Purchase) </td>
                    <td align="right"><? echo number_format($knit_subtotal_amount,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Woven Fabric (Purchase)</td>
                    <td align="right"><? echo number_format($woven_subtotal_amount,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Yarn</td>
                    <td align="right"><? echo number_format($total_yarn_amount,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Conversion to Fabric</td>
                    <td align="right"><? echo number_format($total_conversion_cost,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Trims</td>
                    <td align="right"><? echo number_format($total_trims_cost,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Embellishment</td>
                    <td align="right"><? echo number_format($total_embellishment_amt,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Commercial</td>
                    <td align="right"><? echo number_format($total_commercial_cost,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Commission</td>
                    <td align="right"><? echo number_format($total_commission_cost,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Lab Test</td>
                    <td align="right"><? echo number_format($lab_test,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Inspection Cost</td>
                    <td align="right"><? echo number_format($inspection,4); ?></td>
                </tr>
                <tr>
                    <td align="left">CM Cost - IE</td>
                    <td align="right"><? echo number_format($cm_cost,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Freight Cost</td>
                    <td align="right"><? echo number_format($freight,4); ?></td>
                </tr>
                <tr>
                    <td align="left">Office OH</td>
                    <td align="right"><? echo number_format($common_oh,4); ?></td>
                </tr>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td>Total</td>
                    <td align="right"><? echo number_format($total_summary_amount,4); ?></td>
                </tr>
            </table>
          </td>
          </tr>
          </table>
      </div>


      </div>

     //End CM on Net Order Value Part report here
 	<table class="rpt_table" border="0" cellpadding="1" cellspacing="1" style="width:800px;text-align:center;" rules="all">
		<tr style="alignment-baseline:baseline;">
        	<td height="130" width="33%" style="text-decoration:overline; border:none">Prepared By</td>
            <td width="33%" style="text-decoration:overline; border:none">Checked By</td>
            <td width="33%" style="text-decoration:overline; border:none">Approved By</td>
        </tr>
    </table>

	 <?
	exit();
}
