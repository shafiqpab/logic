<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.others.php');
//require_once('../../../includes/class4/class.commisions.php');
//require_once('../../../includes/class4/class.fabrics.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="load_report_format")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id in(60) and is_deleted=0 and status_active=1");
 	echo trim($print_report_format);	
	exit();

}

if ($action=="load_drop_down_location")
{
	extract($_REQUEST);
    //$choosenCompany = $choosenCompany; 
	$choosenCompany = $data; 
	echo create_drop_down( "cbo_location", 130, "select distinct id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in($choosenCompany) order by location_name","id,location_name", 1, "-- Select --", "", "",0 );     
	exit();	 
}



if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$cbo_company=str_replace("'","",$cbo_company_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$type=str_replace("'","",$type);

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$buyer_lib=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	if(str_replace("'","",$cbo_location)==0){$location_con="";}else{$location_con=" and a.location=$cbo_location";}
	
	if($cbo_company==0){ $cbo_company_cond="";$company_cond2="";}else{ $cbo_company_cond=" and a.company_id in($cbo_company)";$company_cond2=" and d.company_id in($cbo_company)";}

	$comp=str_replace("'", "", $cbo_company_id);
	$work_comp=$cbo_working_company;
	$_SESSION["comp"]=""; 
	$_SESSION["comp"]=$comp;
	$_SESSION["work_comp"]=""; 
	$_SESSION["work_comp"]=$work_comp;


	$date_cond= "";$date_cond2= "";
	if($db_type==0)
	{ 
		if ($date_from!="" &&  $date_to!="") $date_cond= "and c.production_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; else $date_cond= "";
		$year_cond= "year(b.insert_date)as year";
	}
	else if ($db_type==2)
	{
		if ($date_from!="" &&  $date_to!=""){ $date_cond= "and c.production_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";
		$date_cond2= "and a.delivery_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";  }else{ $date_cond2= "";}

		$year_cond= "TO_CHAR(b.insert_date,'YYYY') as year";
	}
	

	ob_start();	
	
	if($type==3)
	{		
		?>
        <table width="2410px" cellpadding="0" cellspacing="0" id="caption" align="center">
            <tr>
            <td align="center" width="100%" colspan="23" class="form_caption"><strong style="font-size:18px">
			<? if($cbo_company==0){ echo "Working Company Name:". $company_library[$cbo_working_company];} else{ 
			$com_arr=explode(",",str_replace("'","",$cbo_company));
			$comName="";
			foreach($com_arr as $comID)
			{
				$comName.=$company_library[$comID].',';
			}
			//echo chop($comName,",");
			echo "Company Name:". chop($comName,",");} 
			?></strong></td>
            </tr> 
            <tr>  
               <td align="center" width="100%" colspan="23" class="form_caption" ><strong style="font-size:14px"><? echo $report_title; ?></strong></td>
            </tr>
            <tr>  
               <td align="center" width="100%" colspan="23" class="form_caption" ><strong style="font-size:14px"> <? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
            </tr>  
        </table>
        
      
      	<?

	
		if(str_replace("'","",$cbo_location)==0){$location_con_rcv="";}else{$location_con_rcv=" and a.location_id=$cbo_location";}
			

	

		$cutting_sql=" select b.id, b.delivery_date, b.main_process_id,b.order_no,b.cust_style_ref,a.subcon_job,a.party_id,a.company_id, a.location_id ,c.production_qnty,b.cust_buyer,c.production_type,b.smv,b.rate,c.production_date from subcon_ord_mst a ,subcon_ord_dtls b,subcon_gmts_prod_dtls c where b.job_no_mst=a.subcon_job  and c.production_type in (1,2,7,4) and c.order_id=b.id and a.status_active=1 and b.status_active=1 and c.status_active=1  $date_cond $location_con_rcv $cbo_company_cond";
		// echo $cutting_sql;
		$cutting_data=sql_select($cutting_sql);

		foreach($cutting_data as $val){

			if($val[csf('production_type')]==1){
			$buyer_wise_data[$val[csf('party_id')]]['cutting_qnty']+=$val[csf('production_qnty')];
			$date_wise_data[$val[csf('production_date')]]['cutting_qnty']+=$val[csf('production_qnty')];
			}elseif($val[csf('production_type')]==2){
				$buyer_wise_data[$val[csf('party_id')]]['sewing_out_qnty']+=$val[csf('production_qnty')];
				$date_wise_data[$val[csf('production_date')]]['sewing_out_qnty']+=$val[csf('production_qnty')];
			}elseif($val[csf('production_type')]==7){

			$buyer_wise_data[$val[csf('party_id')]]['sewing_qnty']+=$val[csf('production_qnty')];
			$buyer_wise_data[$val[csf('party_id')]]['prod_min']+=$val[csf('smv')]*$val[csf('production_qnty')];
			$buyer_wise_data[$val[csf('party_id')]]['sewing_fob_val']+=$val[csf('production_qnty')]*$val[csf('rate')];

			$date_wise_data[$val[csf('production_date')]]['sewing_qnty']+=$val[csf('production_qnty')];
			$date_wise_data[$val[csf('production_date')]]['sah_prod']+=($val[csf('production_qnty')]*$val[csf('smv')])/60;
			$date_wise_data[$val[csf('production_date')]]['sewing_fob_val']+=$val[csf('production_qnty')]*$val[csf('rate')];
			}elseif($val[csf('production_type')]==4){
			$buyer_wise_data[$val[csf('party_id')]]['finish_qnty']+=$val[csf('production_qnty')];
			$date_wise_data[$val[csf('production_date')]]['finish_qnty']+=$val[csf('production_qnty')];
			}

		};

			$exfactory_query="select a.id,b.id as dtls_id,b.order_id,b.item_id,a.delivery_date,b.process_id,b.delivery_qty,a.location_id,a.challan_no,c.rate,c.cust_buyer,d.party_id
 from  subcon_delivery_mst a,  subcon_delivery_dtls b,subcon_ord_dtls c,subcon_ord_mst d
 where a.id=b.mst_id and   c.id=b.order_id and c.job_no_mst=d.subcon_job and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond2 $location_con_rcv $company_cond2 order by a.id";


 			$exfactory_data=sql_select($exfactory_query);
 			foreach($exfactory_data as $val){

			
			$buyer_wise_data[$val[csf('party_id')]]['ex_factory_qnty']+=$val[csf('delivery_qty')];
			$buyer_wise_data[$val[csf('party_id')]]['ex_factory_val']+=$val[csf('delivery_qty')]*$val[csf('rate')];

			$date_wise_data[$val[csf('delivery_date')]]['ex_factory_qnty']+=$val[csf('delivery_qty')];
			$date_wise_data[$val[csf('delivery_date')]]['ex_factory_val']+=$val[csf('delivery_qty')]*$val[csf('rate')];
			}


		?>
        <table width="1050px " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
         <p style="float:left"><strong>Buyer Wise Summary Part</strong></p>
            <thead>
            	<tr>
                    <th width="30">SL</th>
                    <th width="150">Buyer Name</th>                    
                    <th width="100">Cutting Qty</th>                 
                    <th width="100">Sew Output</th>
                    <th width="100">Produced Minute</th>
                    <th width="100">Total Finish</th>
             
                    <th width="100">Sewing FOB Value</th>
                    <th width="100">Ex Factory</th>               
                    <th width="100">Ex Factory FOB Value</th>
                    <th>Sew Out to Ship Bal</th>
                </tr>
            </thead>
        </table>
        <div style="max-height:420px; overflow-y:scroll; width:1070px" id="scroll_body_summery">
       	<table cellspacing="0" border="1" class="rpt_table"  width="1050px" rules="all" id="scroll_body_summery" >
        <?
     
     
		$inc=1;
		$all_po_ids="";
		
			foreach($buyer_wise_data as $buyerKey =>$val)
			{
				
				
				if ($inc%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				
				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_<? echo $inc; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $inc; ?>">
                    <td width="30" ><? echo $inc; ?></td>
					<td width="150"><? echo $buyer_lib[$buyerKey]; ?></td>
                 
					<td width="100" align="right"><? echo number_format($val['cutting_qnty'],2); ?></td>
                    <td width="100" align="right"><? echo number_format($val['sewing_out_qnty'],2); ?></td>
                    <td width="100" align="right"><? echo number_format($val['prod_min'],2) ?></td>
                    <td width="100" align="right"><? echo number_format($val['finish_qnty'],2) ?></td>
                    
                    <td width="100" align="right"><? echo number_format($val['sewing_fob_val'],2) ?></td>
                    <td width="100" align="right"><? echo number_format($val['ex_factory_qnty'],2) ?></td>
                     <td width="100" align="right"><? echo number_format($val['ex_factory_val'],2) ?></td>
                    
                    
                    
					<td  align="right"><? echo number_format($val['ex_factory_qnty']-$val['sewing_out_qnty'],2) ?></td>
                   
				</tr>
			<?
			
		$inc++;
		$buyer_tot_cutting+=$val['cutting_qnty'];
		$buyer_tot_sewing+=$val['sewing_out_qnty'];
		$buyer_tot_prod_min+=$val['prod_min'];
		$buyer_tot_finish+=$val['finish_qnty'];
		$buyer_tot_sewing_fob_val+=$val['sewing_fob_val'];
		$buyer_tot_ex_factory_qnty+=$val['ex_factory_qnty'];
		$buyer_tot_ex_factory_val+=$val['ex_factory_val'];
		$buyer_tot_ship_val+=$val['ex_factory_qnty']-$val['sewing_out_qnty'];
		}
			//echo $all_po_ids.jahid;
		?>
         <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                <th width="150">Total</th>
                <th width="100"><? echo number_format($buyer_tot_cutting,2); ?></th>
               
                <th width="100"><? echo number_format($buyer_tot_sewing,2); ?></th>
                <th width="100"><? echo number_format($buyer_tot_prod_min,2); ?></th>
                <th width="100"><? echo number_format($buyer_tot_finish,2); ?></th>
                <th width="100"><? echo number_format($buyer_tot_sewing_fob_val,2); ?></th>
                <th width="100"><? echo number_format($buyer_tot_ex_factory_qnty,2); ?></th>
                <th width="100"><? echo number_format($buyer_tot_ex_factory_val,2); ?></th>
 
                <th><? echo number_format($buyer_tot_ship_val,2); ?></th>
             </tr>
        </table>
        </div>
         <br/>
         
         
         <table width="1000px " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
         <p style="float:left"><strong>Details Part</strong></p>
            <thead>
            	<tr>
                    <th width="30">SL</th>
                    <th width="150">Production Date</th>                    
                    <th width="100">Cutting Qty</th>                 
                    <th width="100">Sewing Qty</th>
                    <th width="100">Sewing FOB Value</th>
                    <th width="100">Finishing Qty</th>             
                    <th width="100">SAH Produced</th>                   
                    <th width="100">Ex-Factory Qty</th>
                    <th>Ex-Factory Value</th>
                </tr>
            </thead>
        </table>
        <div style="max-height:420px; overflow-y:scroll; width:1020px" id="scroll_body_summery">
       	<table cellspacing="0" border="1" class="rpt_table"  width="1000px" rules="all" id="scroll_body_summery" >
        <?
     
     	// echo "<pre>";
     	// print_r($buyer_wise_data);
		$inc=1;
		$all_po_ids="";
		
			foreach($date_wise_data as $dateKey =>$val)
			{
				
				
				if ($inc%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				
				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_<? echo $inc; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $inc; ?>">
                    <td width="30" ><? echo $inc; ?></td>
					<td width="150"><? echo $dateKey; ?></td>                 
					<td width="100" align="right"><? echo number_format($val['cutting_qnty'],2); ?></td>
                    <td width="100" align="right"><? echo number_format($val['sewing_out_qnty'],2); ?></td>
                    <td width="100" align="right"><? echo number_format($val['sewing_fob_val'],2) ?></td>
                    <td width="100" align="right"><? echo number_format($val['finish_qnty'],2) ?></td>                    
                    <td width="100" align="right"><? echo number_format($val['sah_prod'],2) ?></td>                 
                     <td width="100" align="right"><? echo number_format($val['ex_factory_qnty'],2) ?></td>                    
					<td  align="right"><? echo number_format($val['ex_factory_val'],2) ?></td>
                   
				</tr>
			<?
		$details_tot_cutting+=$val['cutting_qnty'];
		$details_tot_sewing+=$val['sewing_out_qnty'];
		$details_tot_sah_prod+=$val['sah_prod'];
		$details_tot_finish+=$val['finish_qnty'];
		$details_tot_sewing_fob_val+=$val['sewing_fob_val'];
		$details_tot_ex_factory_qnty+=$val['ex_factory_qnty'];
		$details_tot_ex_factory_val+=$val['ex_factory_val'];
		
		$inc++;
		
		}
			//echo $all_po_ids.jahid;
		?>
         <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                <th width="150">Total</th>
                <th width="100"><? echo number_format($details_tot_cutting,2); ?></th>               
                <th width="100"><? echo number_format($details_tot_sewing,2); ?></th>
                <th width="100"><? echo number_format($details_tot_sewing_fob_val,2); ?></th>
                <th width="100"><? echo number_format($details_tot_finish,2); ?></th>
                <th width="100"><? echo number_format($details_tot_sah_prod,2); ?></th>             
                <th width="100"><? echo number_format($details_tot_ex_factory_qnty,2); ?></th> 
                <th><? echo number_format($details_tot_ex_factory_val,2); ?></th>
             </tr>
        </table>
        </div>
	    <? 
	}
	
	
	foreach (glob("$user_id*.xls") as $filename) 
	{
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();      
}



?>