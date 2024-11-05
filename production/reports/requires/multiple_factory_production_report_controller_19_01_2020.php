<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];



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
	//$cbo_company=0;
	$cbo_product_cat=str_replace("'","",$cbo_product_cat);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$type=str_replace("'","",$type);
	$cbo_working_company=str_replace("'","",$cbo_working_company_id);
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	$company_group_library=return_library_array( "select id,group_id from lib_company", "id", "group_id");
	$group_short_library=return_library_array( "select id,group_name from lib_group", "id", "group_name");
	$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per"); 
	$tot_cost_arr = return_library_array("select job_no, cm_cost from wo_pre_cost_dtls","job_no","cm_cost"); 
	if(str_replace("'","",$cbo_location)==0){$location_con="";}else{$location_con=" and a.location=$cbo_location";}
	//echo $cbo_working_company;
	if($cbo_company==0) $cbo_company_cond=""; else $cbo_company_cond=" and a.company_id in($cbo_company)";
	if($cbo_working_company==0) $company_working_cond=""; else $company_working_cond=" and a.knitting_company in($cbo_working_company)";

	//echo $company_working_cond;
	ob_start();	
	
	if($type==3)
	{
		
		?>
        <table width="2310px" cellpadding="0" cellspacing="0" id="caption" align="center">
            <tr>
            <td align="center" width="100%" colspan="22" class="form_caption" style="font-size:18px;"><strong>
			<? /*if($cbo_working_company==0){ echo "Company Name:". $company_library[$cbo_company];} else{ 
			$com_arr=explode(",",str_replace("'","",$cbo_working_company));
			$comName="";
			foreach($com_arr as $comID)
			{
				$comName.=$company_library[$comID].',';
			}
			//echo chop($comName,",");
			echo "Working Company Name:". chop($comName,",");}*/ 
			
			if($cbo_working_company==0){
				$comp=explode(",",$cbo_company);
				  echo "Group Name:". $group_short_library[$company_group_library[$comp[0]]];} 
			else{$wComp=explode(",",$cbo_working_company);
				  echo "Group Name:". $group_short_library[$company_group_library[$wComp[0]]];}
			?>
            
            </strong></td>
            </tr> 
             <tr>  
               <td align="center" width="100%" colspan="22" class="form_caption" style="font-size:14px;"><strong><? echo $report_title; ?></strong></td>
            </tr>
            <tr>  
               <td align="center" width="100%" colspan="22" class="form_caption" style="font-size:14px;"><strong> <? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
            </tr>  
        </table>
      
      <?
	  	if($cbo_company==0) $cbo_company_cond_1=""; else $cbo_company_cond_1="and company_name in($cbo_company)";
		//$smv_source=return_field_value("smv_source","variable_settings_production","$cbo_company_cond_1 and variable_list=25 and status_active=1 and is_deleted=0");
		$smv_source=return_field_value("smv_source","variable_settings_production","variable_list=25 $cbo_company_cond_1 and status_active=1 and is_deleted=0");
		//echo $smv_source;die;
		if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
		$item_smv_array=array();
		//$smv_source=2;
		if($smv_source==3)
		{
			$sql_item="select b.id, a.sam_style, a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$resultItem=sql_select($sql_item);
			foreach($resultItem as $itemData)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
			}
		}
		else
		{
			if($cbo_company==0) $cbo_company_cond_2=""; else $cbo_company_cond_2=" and a.company_name in($cbo_company)";
			$sql_item="select b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no $cbo_company_cond_2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$resultItem=sql_select($sql_item);
			foreach($resultItem as $itemData)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]['smv_pcs']=$itemData[csf('smv_pcs')];
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]['smv_pcs_precost']=$itemData[csf('smv_pcs_precost')];
			}
		}
		
		$tpdArr=array(); $tsmvArr=array();
		//$cbo_working_company
		if($cbo_company==0) $cbo_company_cond_3=""; else $cbo_company_cond_3=" and b.company_id in($cbo_company)";
		if($cbo_working_company==0) $cboWorkingComCondManpower=""; else $cboWorkingComCondManpower=" and b.company_id in($cbo_working_company)";
		if(str_replace("'","",$cbo_location)==0){$cboWorkingComCondLocation="";}else{$cboWorkingComCondLocation=" and b.location_id=$cbo_location";}

        $tpd_data_arr=sql_select( "select a.pr_date, sum(a.target_per_hour*a.working_hour) tpd, sum(a.man_power*a.working_hour) tsmv from prod_resource_dtls a, prod_resource_mst b where b.id=a.mst_id $cbo_company_cond_3 $cboWorkingComCondManpower $cboWorkingComCondLocation and a.pr_date between '$date_from' and '$date_to' and a.is_deleted=0 and b.is_deleted=0 group by  a.pr_date");
		
        foreach($tpd_data_arr as $row)
        {
			$production_date=date("Y-m-d", strtotime($row[csf('pr_date')])); 
           // $tpdArr[$production_date]+=$row[csf('tpd')];
			 $tsmvArr[change_date_format($production_date)]['smv']=$row[csf('tsmv')]*60;
        } //var_dump($tsmvArr[$production_date]['smv']);
		
		$job_array=array(); 
		$job_sql="SELECT a.id, a.unit_price,b.buyer_name,b.company_name,a.po_quantity, b.job_no, b.total_set_qnty,b.set_smv from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ";
		
		//$job_sql="select a.job_no, a.total_set_qnty,b.id, b.unit_price,c.smv_pcs,c.set_item_ratio from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and a.company_name in($cbo_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		
		
		
		$job_sql_result=sql_select($job_sql);
		foreach($job_sql_result as $row)
		{
			$job_array[$row[csf("id")]]['unit_price']=$row[csf("unit_price")];
			$job_array[$row[csf("id")]]['job_no']=$row[csf("job_no")];
			$job_array[$row[csf("id")]]['total_set_qnty']=$row[csf("total_set_qnty")];
			$job_array[$row[csf("id")]]['set_smv']=$row[csf("set_smv")];
			$job_array_summary[$row[csf("company_name")]][$row[csf("buyer_name")]]['po_qty']+=$row[csf("po_quantity")];
			//$job_array[$row[csf("id")]][$row[csf("set_item_ratio")]]['smv_pcs']=$row[csf("smv_pcs")];
		}	  
	  
		  $total_knit=0;
		  $total_finishing=0;
		  $total_print=0;
		  $total_emb=0;
		  $total_cutting=0;
		  $total_cutting_inhouse=0;
		  $total_cutting_subcontract=0;
		  $total_sew=0;
		  $total_sew_inhouse=0;
		  $total_sew_subcontract=0;
		  $total_finishg=0;
		  $total_finish_inhouse=0;
		  $total_finish_subcontract=0;
		  $total_carton=0;
		 if($cbo_company==0) $cbo_company_cond_4=""; else $cbo_company_cond_4=" and a.company_id in($cbo_company)";
		 if($cbo_product_cat==0) $cbo_product_cat_cond=""; else $cbo_product_cat_cond=" and c.product_category = $cbo_product_cat";
		 if($cbo_working_company==0) $company_working_cond_1=""; else $company_working_cond_1=" and a.serving_company in($cbo_working_company)";
		 $dtls_sql="SELECT a.production_date, a.po_break_down_id as po_breakdown_id, a.item_number_id,c.buyer_name,a.company_id,a.serving_company,c.product_category,
					sum(CASE WHEN a.production_type =1 THEN a.production_quantity END) AS cutting_qnty,
					sum(CASE WHEN a.production_type =1 and a.production_source=1 THEN a.production_quantity END) AS cutting_qnty_inhouse,
					sum(CASE WHEN a.production_type =1 and a.production_source=3 THEN a.production_quantity END) AS cutting_qnty_outbound, 
					sum(CASE WHEN a.production_type =3 and a.embel_name=1 THEN a.production_quantity END) AS printing_qnty,
					sum(CASE WHEN a.production_type =3 and a.embel_name=1 and a.production_source=1 THEN a.production_quantity END) AS printing_qnty_inhouse,
					sum(CASE WHEN a.production_type =3 and a.embel_name=1 and a.production_source=3 THEN a.production_quantity END) AS printing_qnty_outbound, 
					sum(CASE WHEN a.production_type =3 and a.embel_name=2 THEN a.production_quantity END) AS emb_qnty,
					sum(CASE WHEN a.production_type =3 and a.embel_name=2 and a.production_source=1 THEN a.production_quantity END) AS emb_qnty_inhouse,
					sum(CASE WHEN a.production_type =3 and a.embel_name=2 and a.production_source=3 THEN a.production_quantity END) AS emb_qnty_outbound,
					sum(CASE WHEN a.production_type =5 THEN a.production_quantity END) AS sewing_qnty,
					sum(CASE WHEN a.production_type =5 and a.production_source=1 THEN a.production_quantity END) AS sewingout_qnty_inhouse,
					sum(CASE WHEN a.production_type =5 and a.production_source=3 THEN a.production_quantity END) AS sewingout_qnty_outbound, 
					sum(CASE WHEN a.production_type =4 THEN a.production_quantity END) AS sewing_input_qnty,
					sum(CASE WHEN a.production_type =4 and a.production_source=1 THEN a.production_quantity END) AS sewing_input_qnty_inhouse,
					sum(CASE WHEN a.production_type =4 and a.production_source=3 THEN a.production_quantity END) AS sewing_input_qnty_outbound, 
					sum(CASE WHEN a.production_type =8 THEN a.production_quantity END) AS finish_qnty,
					sum(CASE WHEN a.production_type =8 and a.production_source=1 THEN a.production_quantity END) AS finish_qnty_inhouse, 
					sum(CASE WHEN a.production_type =8 and a.production_source=3 THEN a.production_quantity END) AS finish_qnty_outbound,
					sum(CASE WHEN a.production_type =8  THEN a.carton_qty END) AS carton_qty,
					
					
					sum(CASE WHEN a.production_type =1 and c.product_category in(1,3,4,5) THEN a.production_quantity END) AS cutting_qnty_all,
					sum(CASE WHEN a.production_type =1 and c.product_category in(1,3,4,5) and a.production_source=1 THEN a.production_quantity END) AS cutting_qnty_inhouse_all,
					sum(CASE WHEN a.production_type =1 and c.product_category in(1,3,4,5) and a.production_source=3 THEN a.production_quantity END) AS cutting_qnty_outbound_all, 
					sum(CASE WHEN a.production_type =3 and c.product_category in(1,3,4,5) and a.embel_name=1 THEN a.production_quantity END) AS printing_qnty_all,
					sum(CASE WHEN a.production_type =3 and c.product_category in(1,3,4,5) and a.embel_name=1 and a.production_source=1 THEN a.production_quantity END) AS printing_qnty_inhouse_all,
					sum(CASE WHEN a.production_type =3 and c.product_category in(1,3,4,5) and a.embel_name=1 and a.production_source=3 THEN a.production_quantity END) AS printing_qnty_outbound_all, 
					sum(CASE WHEN a.production_type =3 and c.product_category in(1,3,4,5) and a.embel_name=2 THEN a.production_quantity END) AS emb_qnty_all,
					sum(CASE WHEN a.production_type =3 and c.product_category in(1,3,4,5) and a.embel_name=2 and a.production_source=1 THEN a.production_quantity END) AS emb_qnty_inhouse_all,
					sum(CASE WHEN a.production_type =3 and c.product_category in(1,3,4,5) and a.embel_name=2 and a.production_source=3 THEN a.production_quantity END) AS emb_qnty_outbound_all,
					sum(CASE WHEN a.production_type =5 and c.product_category in(1,3,4,5) THEN a.production_quantity END) AS sewing_qnty_all,
					sum(CASE WHEN a.production_type =5 and c.product_category in(1,3,4,5) and a.production_source=1 THEN a.production_quantity END) AS sewingout_qnty_inhouse_all,
					sum(CASE WHEN a.production_type =5 and c.product_category in(1,3,4,5) and a.production_source=3 THEN a.production_quantity END) AS sewingout_qnty_outbound_all, 
					sum(CASE WHEN a.production_type =4 and c.product_category in(1,3,4,5) THEN a.production_quantity END) AS sewing_input_qnty_all,
					sum(CASE WHEN a.production_type =4 and c.product_category in(1,3,4,5) and a.production_source=1 THEN a.production_quantity END) AS sewing_input_qnty_inhouse_all,
					sum(CASE WHEN a.production_type =4 and c.product_category in(1,3,4,5) and a.production_source=3 THEN a.production_quantity END) AS sewing_input_qnty_outbound_all, 
					sum(CASE WHEN a.production_type =8 and c.product_category in(1,3,4,5) THEN a.production_quantity END) AS finish_qnty_all,
					sum(CASE WHEN a.production_type =8 and c.product_category in(1,3,4,5) and a.production_source=1 THEN a.production_quantity END) AS finish_qnty_inhouse_all, 
					sum(CASE WHEN a.production_type =8 and c.product_category in(1,3,4,5) and a.production_source=3 THEN a.production_quantity END) AS finish_qnty_outbound_all,
					sum(CASE WHEN a.production_type =8 and c.product_category in(1,3,4,5) THEN a.carton_qty END) AS carton_qty_all,
					
					sum(CASE WHEN a.production_type =1 and c.product_category in(2) THEN a.production_quantity END) AS cutting_qnty_lin,
					sum(CASE WHEN a.production_type =1 and c.product_category in(2) and a.production_source=1 THEN a.production_quantity END) AS cutting_qnty_inhouse_lin,
					sum(CASE WHEN a.production_type =1 and c.product_category in(2) and a.production_source=3 THEN a.production_quantity END) AS cutting_qnty_outbound_lin, 
					sum(CASE WHEN a.production_type =3 and c.product_category in(2) and a.embel_name=1 THEN a.production_quantity END) AS printing_qnty_lin,
					sum(CASE WHEN a.production_type =3 and c.product_category in(2) and a.embel_name=1 and a.production_source=1 THEN a.production_quantity END) AS printing_qnty_inhouse_lin,
					sum(CASE WHEN a.production_type =3 and c.product_category in(2) and a.embel_name=1 and a.production_source=3 THEN a.production_quantity END) AS printing_qnty_outbound_lin, 
					sum(CASE WHEN a.production_type =3 and c.product_category in(2) and a.embel_name=2 THEN a.production_quantity END) AS emb_qnty_lin,
					sum(CASE WHEN a.production_type =3 and c.product_category in(2) and a.embel_name=2 and a.production_source=1 THEN a.production_quantity END) AS emb_qnty_inhouse_lin,
					sum(CASE WHEN a.production_type =3 and c.product_category in(2) and a.embel_name=2 and a.production_source=3 THEN a.production_quantity END) AS emb_qnty_outbound_lin,
					sum(CASE WHEN a.production_type =5 and c.product_category in(2) THEN a.production_quantity END) AS sewing_qnty_lin,
					sum(CASE WHEN a.production_type =5 and c.product_category in(2) and a.production_source=1 THEN a.production_quantity END) AS sewingout_qnty_inhouse_lin,
					sum(CASE WHEN a.production_type =5 and c.product_category in(2) and a.production_source=3 THEN a.production_quantity END) AS sewingout_qnty_outbound_lin, 
					sum(CASE WHEN a.production_type =4 and c.product_category in(2) THEN a.production_quantity END) AS sewing_input_qnty_lin,
					sum(CASE WHEN a.production_type =4 and c.product_category in(2) and a.production_source=1 THEN a.production_quantity END) AS sewing_input_qnty_inhouse_lin,
					sum(CASE WHEN a.production_type =4 and c.product_category in(2) and a.production_source=3 THEN a.production_quantity END) AS sewing_input_qnty_outbound_lin, 
					sum(CASE WHEN a.production_type =8 and c.product_category in(2) THEN a.production_quantity END) AS finish_qnty_lin,
					sum(CASE WHEN a.production_type =8 and c.product_category in(2) and a.production_source=1 THEN a.production_quantity END) AS finish_qnty_inhouse_lin, 
					sum(CASE WHEN a.production_type =8 and c.product_category in(2) and a.production_source=3 THEN a.production_quantity END) AS finish_qnty_outbound_lin,
					sum(CASE WHEN a.production_type =8 and c.product_category in(2) THEN a.carton_qty END) AS carton_qty_lin
			
					 
					from pro_garments_production_mst a, wo_po_break_down b,wo_po_details_master c 
					where a.po_break_down_id=b.id and c.job_no=b.job_no_mst  $location_con $cbo_company_cond_4 $company_working_cond_1 $cbo_product_cat_cond and a.production_date between '$date_from' and '$date_to' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.production_date, a.po_break_down_id, a.item_number_id,c.buyer_name,a.company_id,a.serving_company,c.product_category  order by a.production_date asc";
					
			//and a.production_date between '$date_from' and '$date_to'
			//and b.id in(29714,29715) 
			 //echo $dtls_sql;
			 $dtls_sql_result=sql_select($dtls_sql);
			 $prod_date=array();$po_id=""; $po_sewing_qty=array();  $prod_date_buyer_wise_summary=array();
			 foreach($dtls_sql_result as $row)
			 {
				 //if($po_id=="")$po_id=$row[csf("po_breakdown_id")]; else $po_id=$po_id.",".$row[csf("po_breakdown_id")];
				 
				 $production_date=date("Y-m-d", strtotime($row[csf('production_date')])); 
				 $prod_date[change_date_format($row[csf("production_date")])]['po_breakdown_id'].=$row[csf("po_breakdown_id")].",";
				 $prod_date[change_date_format($row[csf("production_date")])]['production_date']=$row[csf("production_date")];
				 $prod_date[change_date_format($row[csf("production_date")])]['printing_qnty']+=$row[csf("printing_qnty")];
				 $prod_date[change_date_format($row[csf("production_date")])]['emb_qnty']+=$row[csf("emb_qnty")];
				 $prod_date[change_date_format($row[csf("production_date")])]['cutting_qnty_inhouse']+=$row[csf("cutting_qnty_inhouse")];
				 $prod_date[change_date_format($row[csf("production_date")])]['cutting_qnty_outbound']+=$row[csf("cutting_qnty_outbound")];
				 $prod_date[change_date_format($row[csf("production_date")])]['cutting_qnty']+=$row[csf("cutting_qnty")];
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_qnty_inhouse']+=$row[csf("sewingout_qnty_inhouse")];
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_qnty_outbound']+=$row[csf("sewingout_qnty_outbound")];
				 $prod_date[change_date_format($row[csf("production_date")])]['sewing_qnty']+=$row[csf("sewing_qnty")];
				 
				 
				 //array for summary part
				 $prod_date_buyer_wise_smry[$row[csf("buyer_name")]]['cutting_qnty_inhouse']+=$row[csf("cutting_qnty_inhouse")];
				 $prod_date_buyer_wise_smry[$row[csf("buyer_name")]]['cutting_qnty_outbound']+=$row[csf("cutting_qnty_outbound")];
				 $prod_date_buyer_wise_smry[$row[csf("buyer_name")]]['cutting_qnty']+=$row[csf("cutting_qnty")];
				 $prod_date_buyer_wise_smry[$row[csf("buyer_name")]]['sewingout_qnty_inhouse']+=$row[csf("sewingout_qnty_inhouse")];
				 $prod_date_buyer_wise_smry[$row[csf("buyer_name")]]['sewingout_qnty_outbound']+=$row[csf("sewingout_qnty_outbound")];
				 $prod_date_buyer_wise_smry[$row[csf("buyer_name")]]['sewing_input_qnty']+=$row[csf("sewing_input_qnty")];
				 $prod_date_buyer_wise_smry[$row[csf("buyer_name")]]['sewing_qnty']+=$row[csf("sewing_qnty")]; //sew output
				 //$prod_date_buyer_wise_smry[$row[csf("buyer_name")]]['sewing_qnty']+=$row[csf("sewing_qnty")]; //sew output
				 $prod_date_buyer_wise_smry[$row[csf("buyer_name")]]['finish_qnty']+=$row[csf("finish_qnty")];
				 $prod_date_buyer_wise_smry[$row[csf("buyer_name")]]['buyer_name']=$row[csf("buyer_name")];
				 
				 
				 // array for Buyer wise summary part
				// $prod_date_buyer_wise[$row[csf("company_id")]][$row[csf("buyer_name")]]['po_quantity']+=$row[csf("po_quantity")];
				
				$working_company_arr[$row[csf("company_id")]]['company_id']=$row[csf("company_id")];
				 $prod_date_buyer_wise[$row[csf("serving_company")]][$row[csf("buyer_name")]]['cutting_qnty_inhouse']+=$row[csf("cutting_qnty_inhouse")];
				 $prod_date_buyer_wise[$row[csf("serving_company")]][$row[csf("buyer_name")]]['cutting_qnty_outbound']+=$row[csf("cutting_qnty_outbound")];
				 $prod_date_buyer_wise[$row[csf("serving_company")]][$row[csf("buyer_name")]]['cutting_qnty']+=$row[csf("cutting_qnty")];
				 $prod_date_buyer_wise[$row[csf("serving_company")]][$row[csf("buyer_name")]]['sewingout_qnty_inhouse']+=$row[csf("sewingout_qnty_inhouse")];
				 $prod_date_buyer_wise[$row[csf("serving_company")]][$row[csf("buyer_name")]]['sewingout_qnty_outbound']+=$row[csf("sewingout_qnty_outbound")];
				 $prod_date_buyer_wise[$row[csf("serving_company")]][$row[csf("buyer_name")]]['sewing_input_qnty']+=$row[csf("sewing_input_qnty")];
				 $prod_date_buyer_wise[$row[csf("serving_company")]][$row[csf("buyer_name")]]['sewing_qnty']+=$row[csf("sewing_qnty")]; //sew output
				 //$prod_date_buyer_wise[$row[csf("company_id")]][$row[csf("buyer_name")]]['sewing_qnty']+=$row[csf("sewing_qnty")]; //sew output
				 $prod_date_buyer_wise[$row[csf("serving_company")]][$row[csf("buyer_name")]]['finish_qnty']+=$row[csf("finish_qnty")];
				 $prod_date_buyer_wise[$row[csf("serving_company")]][$row[csf("buyer_name")]]['buyer_name']=$row[csf("buyer_name")];

				 
				 $prod_date_buyer_wise_all[$row[csf("buyer_name")]]['cutting_qnty_inhouse_all']+=$row[csf("cutting_qnty_inhouse_all")];
				 $prod_date_buyer_wise_all[$row[csf("buyer_name")]]['cutting_qnty_outbound_all']+=$row[csf("cutting_qnty_outbound_all")];
				 $prod_date_buyer_wise_all[$row[csf("buyer_name")]]['cutting_qnty_all']+=$row[csf("cutting_qnty_all")];
				 $prod_date_buyer_wise_all[$row[csf("buyer_name")]]['sewingout_qnty_inhouse_all']+=$row[csf("sewingout_qnty_inhouse_all")];
				 $prod_date_buyer_wise_all[$row[csf("buyer_name")]]['sewingout_qnty_outbound_all']+=$row[csf("sewingout_qnty_outbound_all")];
				 $prod_date_buyer_wise_all[$row[csf("buyer_name")]]['sewing_input_qnty_all']+=$row[csf("sewing_input_qnty_all")];
				 $prod_date_buyer_wise_all[$row[csf("buyer_name")]]['sewing_qnty_all']+=$row[csf("sewing_qnty_all")]; //sew output
				 $prod_date_buyer_wise_all[$row[csf("buyer_name")]]['finish_qnty_all']+=$row[csf("finish_qnty_all")];
				 $prod_date_buyer_wise_all[$row[csf("buyer_name")]]['buyer_name']=$row[csf("buyer_name")];
				 
				 $prod_date_buyer_wise_lin[$row[csf("buyer_name")]]['cutting_qnty_inhouse_lin']+=$row[csf("cutting_qnty_inhouse_lin")];
				 $prod_date_buyer_wise_lin[$row[csf("buyer_name")]]['cutting_qnty_outbound_lin']+=$row[csf("cutting_qnty_outbound_lin")];
				 $prod_date_buyer_wise_lin[$row[csf("buyer_name")]]['cutting_qnty_lin']+=$row[csf("cutting_qnty_lin")];
				 $prod_date_buyer_wise_lin[$row[csf("buyer_name")]]['sewingout_qnty_inhouse_lin']+=$row[csf("sewingout_qnty_inhouse_lin")];
				 $prod_date_buyer_wise_lin[$row[csf("buyer_name")]]['sewingout_qnty_outbound_lin']+=$row[csf("sewingout_qnty_outbound_lin")];
				 $prod_date_buyer_wise_lin[$row[csf("buyer_name")]]['sewing_input_qnty_lin']+=$row[csf("sewing_input_qnty_lin")];
				 $prod_date_buyer_wise_lin[$row[csf("buyer_name")]]['sewing_qnty_lin']+=$row[csf("sewing_qnty_lin")]; //sew output
				 $prod_date_buyer_wise_lin[$row[csf("buyer_name")]]['finish_qnty_lin']+=$row[csf("finish_qnty_lin")];
				 $prod_date_buyer_wise_lin[$row[csf("buyer_name")]]['buyer_name']=$row[csf("buyer_name")];
				
				 /*
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_qnty_outbound_pcs']+=($row[csf("sewingout_qnty_outbound")]*$job_array[$row[csf("po_breakdown_id")]]['set_smv']);
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_qnty_inhouse_pcs']+=($row[csf("sewingout_qnty_inhouse")]*$job_array[$row[csf("po_breakdown_id")]]['set_smv']);
				 */
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_qnty_outbound_pcs']+=($row[csf("sewingout_qnty_outbound")]*$item_smv_array[$row[csf("po_breakdown_id")]][$row[csf("item_number_id")]]['smv_pcs']);
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_qnty_inhouse_pcs']+=($row[csf("sewingout_qnty_inhouse")]*$item_smv_array[$row[csf("po_breakdown_id")]][$row[csf("item_number_id")]]['smv_pcs']);
				 
				 $item_smv=0;
				if($smv_source==2)
				{
					$item_smv=$item_smv_array[$row[csf("po_breakdown_id")]][$row[csf("item_number_id")]]['smv_pcs_precost'];
				}
				else if($smv_source==3)
				{
					$item_smv=$item_smv_array[$row[csf("po_breakdown_id")]][$row[csf("item_number_id")]];	
				}
				else
				{
					$item_smv=$item_smv_array[$row[csf("po_breakdown_id")]][$row[csf("item_number_id")]]['smv_pcs'];	
				}
				 
				 $prod_date[change_date_format($row[csf("production_date")])]['sewing_qnty_smv']+=$row[csf("sewing_qnty")]*$item_smv;
				 
				 $prod_date[change_date_format($row[csf("production_date")])]['finish_qnty_inhouse']+=$row[csf("finish_qnty_inhouse")];
				 $prod_date[change_date_format($row[csf("production_date")])]['finish_qnty_outbound']+=$row[csf("finish_qnty_outbound")];
				 
				 //for summary part
				 $prod_date_buyer_wise_smry[$row[csf("buyer_name")]]['finish_qnty_inhouse']+=$row[csf("finish_qnty_inhouse")];
				 $prod_date_buyer_wise_smry[$row[csf("buyer_name")]]['finish_qnty_outbound']+=$row[csf("finish_qnty_outbound")]; 
				 //end
				 
				 
				 $prod_date_buyer_wise[$row[csf("serving_company")]][$row[csf("buyer_name")]]['finish_qnty_inhouse']+=$row[csf("finish_qnty_inhouse")];
				 $prod_date_buyer_wise[$row[csf("serving_company")]][$row[csf("buyer_name")]]['finish_qnty_outbound']+=$row[csf("finish_qnty_outbound")]; 
				 
				 $prod_date_buyer_wise_all[$row[csf("buyer_name")]]['finish_qnty_inhouse_all']+=$row[csf("finish_qnty_inhouse_all")];
				 $prod_date_buyer_wise_all[$row[csf("buyer_name")]]['finish_qnty_outbound_all']+=$row[csf("finish_qnty_outbound_all")]; 
				 
				 $prod_date_buyer_wise_lin[$row[csf("buyer_name")]]['finish_qnty_inhouse_lin']+=$row[csf("finish_qnty_inhouse_lin")];
				 $prod_date_buyer_wise_lin[$row[csf("buyer_name")]]['finish_qnty_outbound_lin']+=$row[csf("finish_qnty_outbound_lin")]; 
				 
				 
				 
				 $prod_date[change_date_format($row[csf("production_date")])]['finish_qnty']+=$row[csf("finish_qnty")];
				 $prod_date[change_date_format($row[csf("production_date")])]['carton_qty']+=$row[csf("carton_qty")];
				 
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_value_inhouse']+=$row[csf("sewingout_qnty_inhouse")]*($job_array[$row[csf("po_breakdown_id")]]['unit_price']/$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty']);
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_value_outbound']+=$row[csf("sewingout_qnty_outbound")]*($job_array[$row[csf("po_breakdown_id")]]['unit_price']/$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty']);
				  //$prod_date_buyer_wise[$row[csf("company_id")]][$row[csf("buyer_name")]]['sewing_fob_val_input_qnty']+=$row[csf("sewingout_qnty_inhouse")]*$job_array[$row[csf("po_breakdown_id")]]['unit_price'];
				  $prod_date_buyer_wise[$row[csf("serving_company")]][$row[csf("buyer_name")]]['sewingUnitprice']=($job_array[$row[csf("po_breakdown_id")]]['unit_price']/$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty']);
				  
				  //for summary part
				   $prod_date_buyer_wise_smry[$row[csf("buyer_name")]]['sewingout_value_inhouse']+=$row[csf("sewingout_qnty_inhouse")]*($job_array[$row[csf("po_breakdown_id")]]['unit_price']/$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty']);
				   $prod_date_buyer_wise_smry[$row[csf("buyer_name")]]['sewingout_value_outbound']+=$row[csf("sewingout_qnty_outbound")]*($job_array[$row[csf("po_breakdown_id")]]['unit_price']/$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty']);
				  //end
				  
				  $prod_date_buyer_wise[$row[csf("serving_company")]][$row[csf("buyer_name")]]['sewingout_value_inhouse']+=$row[csf("sewingout_qnty_inhouse")]*($job_array[$row[csf("po_breakdown_id")]]['unit_price']/$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty']);
				  $prod_date_buyer_wise[$row[csf("serving_company")]][$row[csf("buyer_name")]]['sewingout_value_outbound']+=$row[csf("sewingout_qnty_outbound")]*($job_array[$row[csf("po_breakdown_id")]]['unit_price']/$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty']);
				  
				  
				   $prod_date_buyer_wise_all[$row[csf("buyer_name")]]['sewingout_value_inhouse_all']+=$row[csf("sewingout_qnty_inhouse_all")]*($job_array[$row[csf("po_breakdown_id")]]['unit_price']/$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty']);
				  $prod_date_buyer_wise_all[$row[csf("buyer_name")]]['sewingout_value_outbound_all']+=$row[csf("sewingout_qnty_outbound_all")]*($job_array[$row[csf("po_breakdown_id")]]['unit_price']/$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty']);
				  
				    $prod_date_buyer_wise_lin[$row[csf("buyer_name")]]['sewingout_value_inhouse_lin']+=$row[csf("sewingout_qnty_inhouse_lin")]*($job_array[$row[csf("po_breakdown_id")]]['unit_price']/$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty']);
				  $prod_date_buyer_wise_lin[$row[csf("buyer_name")]]['sewingout_value_outbound_lin']+=$row[csf("sewingout_qnty_outbound_lin")]*($job_array[$row[csf("po_breakdown_id")]]['unit_price']/$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty']);
				 
				 if($row[csf("product_category")]==1 || $row[csf("product_category")]==3 || $row[csf("product_category")]==4 || $row[csf("product_category")]==5)
				 {
					$cm_value_all=0; $cm_value_in_all=0; $cm_value_out_all=0; $sewing_qty_in_all=0; $sewing_qty_out_all=0;
				 		//$sewing_qnty=$row[csf("sewing_qnty")];
					 $sewing_qty_in_all=$row[csf("sewingout_qnty_inhouse_all")];
					 $sewing_qty_out_all=$row[csf("sewingout_qnty_outbound_all")];
					 
					 $job_no_all=$job_array[$row[csf("po_breakdown_id")]]['job_no'];
					 $total_set_qnty_all=$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty'];
					 $costing_per_all=$costing_per_arr[$job_no];
					 
					 if($costing_per_all==1) $dzn_qnty_all=12;
					 else if($costing_per_all==3) $dzn_qnty_all=12*2;
					 else if($costing_per_all==4) $dzn_qnty_all=12*3;
					 else if($costing_per_all==5) $dzn_qnty_all=12*4;
					 else $dzn_qnty_all=1;
								
					 $dzn_qnty_all=$dzn_qnty_all*$total_set_qnty_all;
					 //$cm_value=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewing_qnty;
					 $cm_value_in_all=($tot_cost_arr[$job_no_all]/$dzn_qnty_all)*$sewing_qty_in_all;
					 $cm_value_out_all=($tot_cost_arr[$job_no_all]/$dzn_qnty_all)*$sewing_qty_out_all;
					
					 //$prod_date[change_date_format($row[csf("production_date")])]['cm_value']+=$cm_value;
					 $prod_date[change_date_format($row[csf("production_date")])]['cm_value_in_all']+=$cm_value_in_all;
					 $prod_date[change_date_format($row[csf("production_date")])]['cm_value_out_all']+=$cm_value_out_all;
					 $prod_date_buyer_wise_all[$row[csf("buyer_name")]]['cm_value_in_all']+=$cm_value_in_all;
				 }
				 
				 else if($row[csf("product_category")]==2)
				 {
					 $cm_value_lin=0; $cm_value_in_lin=0; $cm_value_out_lin=0; $sewing_qty_in_lin=0; $sewing_qty_out_lin=0;
				 		//$sewing_qnty=$row[csf("sewing_qnty")];
					 $sewing_qty_in_lin=$row[csf("sewingout_qnty_inhouse_lin")];
					 $sewing_qty_out_lin=$row[csf("sewingout_qnty_outbound_lin")];
					 
					 $job_no_lin=$job_array[$row[csf("po_breakdown_id")]]['job_no'];
					 $total_set_qnty_lin=$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty'];
					 $costing_per_lin=$costing_per_arr[$job_no];
					 
					 if($costing_per_lin==1) $dzn_qnty_lin=12;
					 else if($costing_per_lin==3) $dzn_qnty_lin=12*2;
					 else if($costing_per_lin==4) $dzn_qnty_lin=12*3;
					 else if($costing_per_lin==5) $dzn_qnty_lin=12*4;
					 else $dzn_qnty_lin=1;
								
					 $dzn_qnty_lin=$dzn_qnty_lin*$total_set_qnty_lin;
					 //$cm_value=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewing_qnty;
					 $cm_value_in_lin=($tot_cost_arr[$job_no_lin]/$dzn_qnty_lin)*$sewing_qty_in_lin;
					 $cm_value_out_lin=($tot_cost_arr[$job_no_lin]/$dzn_qnty_lin)*$sewing_qty_out_lin;
					
					 //$prod_date[change_date_format($row[csf("production_date")])]['cm_value']+=$cm_value;
					 $prod_date[change_date_format($row[csf("production_date")])]['cm_value_in_lin']+=$cm_value_in_lin;
					 $prod_date[change_date_format($row[csf("production_date")])]['cm_value_out_lin']+=$cm_value_out_lin;
					 $prod_date_buyer_wise_lin[$row[csf("buyer_name")]]['cm_value_in_lin']+=$cm_value_in_lin;
				 }
				 if($row[csf("product_category")]==1 || $row[csf("product_category")]==2 || $row[csf("product_category")]==3 || $row[csf("product_category")]==4 || $row[csf("product_category")]==5)
				 {
					  $cm_value=0; $cm_value_in=0; $cm_value_out=0; $sewing_qty_in=0; $sewing_qty_out=0;
				 		//$sewing_qnty=$row[csf("sewing_qnty")];
					 $sewing_qty_in=$row[csf("sewingout_qnty_inhouse")];
					 $sewing_qty_out=$row[csf("sewingout_qnty_outbound")];
					 
					 $job_no=$job_array[$row[csf("po_breakdown_id")]]['job_no'];
					 $total_set_qnty=$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty'];
					 $costing_per=$costing_per_arr[$job_no];
					 
					 if($costing_per==1) $dzn_qnty=12;
					 else if($costing_per==3) $dzn_qnty=12*2;
					 else if($costing_per==4) $dzn_qnty=12*3;
					 else if($costing_per==5) $dzn_qnty=12*4;
					 else $dzn_qnty=1;
								
					 $dzn_qnty=$dzn_qnty*$total_set_qnty;
					 //$cm_value=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewing_qnty;
					 $cm_value_in=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewing_qty_in;
					 $cm_value_out=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewing_qty_out;
					
					 //$prod_date[change_date_format($row[csf("production_date")])]['cm_value']+=$cm_value;
					 $prod_date[change_date_format($row[csf("production_date")])]['cm_value_in']+=$cm_value_in;
					 $prod_date[change_date_format($row[csf("production_date")])]['cm_value_out']+=$cm_value_out;
					 $prod_date_buyer_wise[$row[csf("serving_company")]][$row[csf("buyer_name")]]['cm_value_in']+=$cm_value_in;
					 $prod_date_buyer_wise_smry[$row[csf("buyer_name")]]['cm_value_in']+=$cm_value_in;
				 }
				
			}
			//print_r($prod_date_buyer_wise);
			//print_r($working_company_arr);
			if(str_replace("'","",$cbo_location)==0){$location_con_rcv="";}else{$location_con_rcv=" and a.location_id=$cbo_location";}
			if($cbo_company==0) $cbo_company_cond_5=""; else $cbo_company_cond_5=" and a.company_id in($cbo_company)";
			if($cbo_working_company==0) $company_working_cond_2=""; else $company_working_cond_2=" and a.knitting_company in($cbo_working_company)";
			$knited_query="select a.receive_date as production_date, sum(b.grey_receive_qnty) as kniting_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id $cbo_company_cond_5  $company_working_cond_2 $location_con_rcv and a.receive_date between '$date_from' and '$date_to' and a.entry_form=2 and a.item_category=13 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.receive_date";
			
			
			$knited_query_result=sql_select($knited_query);
			$count_knit=count($knited_query_result);
			foreach( $knited_query_result as $knit_row)
			{
				$prod_date[change_date_format($knit_row[csf("production_date")])]['kniting_qnty']=$knit_row[csf("kniting_qnty")];
			}
			//var_dump($prod_datek);
			$finish_query="select a.receive_date as production_date, sum(b.receive_qnty) as finishing_qnty from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id $cbo_company_cond_5 $company_working_cond_2 $location_con_rcv and a.receive_date between '$date_from' and '$date_to' and a.entry_form=7 and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.receive_date";
			//echo $finish_query;
			$finish_query_result=sql_select($finish_query);
			$count_finish=count($finish_query_result);
			foreach( $finish_query_result as $finish_row)
			{
				$prod_date[change_date_format($finish_row[csf("production_date")])]['finishing_qnty']=$finish_row[csf("finishing_qnty")];
			}
			//var_dump($prod_date);
			
			if($cbo_company==0) $cbo_company_cond_6=""; else $cbo_company_cond_6=" and c.company_name in($cbo_company)";
			
			$wc= explode(",",str_replace("'", "",$cbo_working_company_id));
			$multiWc="";
			foreach($wc as $row){
				$multiWc.="'".$row."'".',';
			}
			$multiWorkingComp= chop($multiWc,",");
			if( str_replace("'", "",$cbo_working_company_id)==0) $cbo_delivery_com_cond=""; else $cbo_delivery_com_cond=" and d.delivery_company_id in($multiWorkingComp)";	
			
			
			if( str_replace("'", "",$cbo_working_company_id)>0 &&  str_replace("'", "",$cbo_location)>0 )
			{
				
				$delv_location_con=" and d.delivery_location_id=$cbo_location ";
				$location_con="";
			}
			else
			{
				$delv_location_con="";
				
			}

			$exfactory_res = sql_select("SELECT a.ex_factory_date, a.po_break_down_id,c.company_name,c.buyer_name,c.product_category,d.delivery_company_id as serving_company,  
			 
			sum(case when a.entry_form!=85 then a.ex_factory_qnty else 0 end)-sum(case when a.entry_form=85 then a.ex_factory_qnty else 0 end) as ex_factory_qnty, 
			sum(case when a.entry_form!=85 and (d.delivery_company_id=0 OR d.delivery_company_id=c.company_name)  then a.ex_factory_qnty else 0 end)-sum(case when a.entry_form=85 and (d.delivery_company_id=0 OR d.delivery_company_id=c.company_name) then a.ex_factory_qnty else 0 end) as ex_factory_qnty_inhouse,
			
			
			sum(case when a.entry_form!=85 and d.delivery_company_id!=0 and d.delivery_company_id!=c.company_name then a.ex_factory_qnty else 0 end)-sum(case when a.entry_form=85 and d.delivery_company_id!=0 and d.delivery_company_id!=c.company_name then a.ex_factory_qnty else 0 end) as ex_factory_qnty_outbound, 
			
			sum(case when a.entry_form!=85 and c.product_category in(1,3,4,5) then a.ex_factory_qnty else 0 end)-sum(case when a.entry_form=85 then a.ex_factory_qnty else 0 end) as ex_factory_qnty_all, 
			sum(case when a.entry_form!=85 and (d.delivery_company_id=0 OR d.delivery_company_id=c.company_name) and c.product_category in(1,3,4,5) then a.ex_factory_qnty else 0 end)-sum(case when a.entry_form=85 and (d.delivery_company_id=0 OR d.delivery_company_id=c.company_name) then a.ex_factory_qnty else 0 end) as ex_factory_qnty_inhouse_all,
			sum(case when a.entry_form!=85 and c.product_category in(1,3,4,5) and d.delivery_company_id!=0 and d.delivery_company_id!=c.company_name then a.ex_factory_qnty else 0 end)-sum(case when a.entry_form=85 and d.delivery_company_id!=0 and d.delivery_company_id!=c.company_name then a.ex_factory_qnty else 0 end) as ex_factory_qnty_outbound_all,
			
			sum(case when a.entry_form!=85 and c.product_category in(2) then a.ex_factory_qnty else 0 end)-sum(case when a.entry_form=85 then a.ex_factory_qnty else 0 end) as ex_factory_qnty_lin, 
			sum(case when a.entry_form!=85 and c.product_category in(2)  and (d.delivery_company_id=0 OR d.delivery_company_id=c.company_name) then a.ex_factory_qnty else 0 end)-sum(case when a.entry_form=85  and (d.delivery_company_id=0 OR d.delivery_company_id=c.company_name) then a.ex_factory_qnty else 0 end) as ex_factory_qnty_inhouse_lin,
			sum(case when a.entry_form!=85 and c.product_category in(2)  and d.delivery_company_id!=0 and d.delivery_company_id!=c.company_name then a.ex_factory_qnty else 0 end)-sum(case when a.entry_form=85 and d.delivery_company_id!=0 and d.delivery_company_id!=c.company_name then a.ex_factory_qnty else 0 end) as ex_factory_qnty_outbound_lin  
			 
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c,pro_ex_factory_delivery_mst d
			
			where a.delivery_mst_id=d.id and  a.po_break_down_id=b.id and b.job_no_mst=c.job_no $cbo_company_cond_6 $cbo_delivery_com_cond $location_con $delv_location_con $cbo_product_cat_cond and a.is_deleted=0 and a.status_active=1 and a.ex_factory_date between '$date_from' and '$date_to' group by a.ex_factory_date, a.po_break_down_id,c.company_name,c.buyer_name,c.product_category,d.delivery_company_id");
			
			
			
			/*echo "SELECT a.ex_factory_date, a.po_break_down_id,c.company_name,c.buyer_name,   
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c,pro_ex_factory_delivery_mst d
			where a.delivery_mst_id=d.id and  a.po_break_down_id=b.id and b.job_no_mst=c.job_no $cbo_company_cond_6 $cbo_delivery_com_cond $location_con $delv_location_con and a.is_deleted=0 and a.status_active=1 and a.ex_factory_date between '$date_from' and '$date_to' group by a.ex_factory_date, a.po_break_down_id,c.company_name,c.buyer_name";*/
			
			foreach($exfactory_res as $ex_row)
			{
				
				 if($ex_row[csf("product_category")]==1 || $ex_row[csf("product_category")]==3 || $ex_row[csf("product_category")]==4 || $ex_row[csf("product_category")]==5)
				 {
					//for regular order part
					$prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['ex_factory_all']+=$ex_row[csf("ex_factory_qnty_all")];
					$prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['ex_factory_qnty_inhouse_all']+=$ex_row[csf("ex_factory_qnty_inhouse_all")];
					$prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['ex_factory_qnty_outbound_all']+=$ex_row[csf("ex_factory_qnty_outbound_all")];				
					
					$prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['ex_factory_val_all']+=$ex_row[csf("ex_factory_qnty_all")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
	
	  				
					 $ex_cm_value_in_all=0; $ex_cm_value_inhouse_all=0; $ex_cm_value_outbound_all=0; $ex_sewing_qty_in_all=0; $ex_sewing_qty_inhouse_all=0; $ex_sewing_qty_outbound_all=0;
					 
					 $ex_sewing_qty_in_all=$ex_row[csf("ex_factory_qnty_all")];
					 $ex_sewing_qty_inhouse_all=$ex_row[csf("ex_factory_qnty_inhouse_all")];
					 $ex_sewing_qty_outbound_all=$ex_row[csf("ex_factory_qnty_outbound_all")];
					 
					 $job_no_ex_all=$job_array[$ex_row[csf("po_break_down_id")]]['job_no'];
					 $total_ex_set_qnty_all=$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty'];
					 $costing_per_ex_all=$costing_per_arr[$job_no_ex_all];
					 
					 if($costing_per_ex_all==1) $dzn_qnty_ex_all=12;
					 else if($costing_per_ex_all==3) $dzn_qnty_ex_all=12*2;
					 else if($costing_per_ex_all==4) $dzn_qnty_ex_all=12*3;
					 else if($costing_per_ex_all==5) $dzn_qdzn_qnty_exnty_all=12*4;
					 else $dzn_qnty_ex_all=1;
								
					 $dzn_qnty_ex_all=$dzn_qnty_ex_all*$total_ex_set_qnty_all;
					 $ex_cm_value_in_all=($tot_cost_arr[$job_no_ex_all]/$dzn_qnty_ex_all)*$ex_sewing_qty_in_all;
					 $ex_cm_value_inhouse_all=($tot_cost_arr[$job_no_ex_all]/$dzn_qnty_ex_all)*$ex_sewing_qty_inhouse_all;
					 $ex_cm_value_outbound_all=($tot_cost_arr[$job_no_ex_all]/$dzn_qnty_ex_all)*$ex_sewing_qty_outbound_all;
					 
					 $prod_date_buyer_wise_all[$ex_row[csf("buyer_name")]]['ex_cm_value_in_all']+=$ex_cm_value_in_all;
					 $prod_date_buyer_wise_all[$ex_row[csf("buyer_name")]]['ex_cm_value_inhouse_all']+=$ex_cm_value_inhouse_all;
					 $prod_date_buyer_wise_all[$ex_row[csf("buyer_name")]]['ex_cm_value_outbound_all']+=$ex_cm_value_outbound_all;
					 $prod_date_buyer_wise_all[$ex_row[csf("buyer_name")]]['ex_factory_smry_all']+=$ex_row[csf("ex_factory_qnty_all")];
					 $prod_date_buyer_wise_all[$ex_row[csf("buyer_name")]]['ex_factory_qnty_inhouse_all']+=$ex_row[csf("ex_factory_qnty_inhouse_all")];
					 $prod_date_buyer_wise_all[$ex_row[csf("buyer_name")]]['ex_factory_qnty_outbound_all']+=$ex_row[csf("ex_factory_qnty_outbound_all")];	
					 $prod_date_buyer_wise_all[$ex_row[csf("buyer_name")]]['ex_factory_smry_unitPrice_all']=($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
					$prod_date_buyer_wise_all[$ex_row[csf("buyer_name")]]['ex_factory_smry_fobVal_inhouse_all']+=$ex_row[csf("ex_factory_qnty_inhouse_all")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
					$prod_date_buyer_wise_all[$ex_row[csf("buyer_name")]]['ex_factory_smry_fobVal_outbound_all']+=$ex_row[csf("ex_factory_qnty_outbound_all")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
					$prod_date_buyer_wise_all[$ex_row[csf("buyer_name")]]['ex_factory_smry_fobVal_all']+=$ex_row[csf("ex_factory_qnty_all")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
					
				}
				else  if($ex_row[csf("product_category")]==2)
				{
					//for UG order part
					$prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['ex_factory_lin']+=$ex_row[csf("ex_factory_qnty_lin")];
					$prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['ex_factory_qnty_inhouse_lin']+=$ex_row[csf("ex_factory_qnty_inhouse_lin")];
					$prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['ex_factory_qnty_outbound_lin']+=$ex_row[csf("ex_factory_qnty_outbound_lin")];				
					
					$prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['ex_factory_val_lin']+=$ex_row[csf("ex_factory_qnty_lin")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
	
	  				
					 $ex_cm_value_in_lin=0; $ex_cm_value_inhouse_lin=0; $ex_cm_value_outbound_lin=0; $ex_sewing_qty_in_lin=0; $ex_sewing_qty_inhouse_lin=0; $ex_sewing_qty_outbound_lin=0;
					 
					 $ex_sewing_qty_in_lin=$ex_row[csf("ex_factory_qnty_lin")];
					 $ex_sewing_qty_inhouse_lin=$ex_row[csf("ex_factory_qnty_inhouse_lin")];
					 $ex_sewing_qty_outbound_lin=$ex_row[csf("ex_factory_qnty_outbound_lin")];
					 
					 $job_no_ex_lin=$job_array[$ex_row[csf("po_break_down_id")]]['job_no'];
					 $total_ex_set_qnty_lin=$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty'];
					 $costing_per_ex_lin=$costing_per_arr[$job_no_ex_lin];
					 
					 if($costing_per_ex_lin==1) $dzn_qnty_ex_lin=12;
					 else if($costing_per_ex_lin==3) $dzn_qnty_ex_lin=12*2;
					 else if($costing_per_ex_lin==4) $dzn_qnty_ex_lin=12*3;
					 else if($costing_per_ex_lin==5) $dzn_qdzn_qnty_exnty_lin=12*4;
					 else $dzn_qnty_ex_lin=1;
								
					 $dzn_qnty_ex_lin=$dzn_qnty_ex_lin*$total_ex_set_qnty_lin;
					 $ex_cm_value_in_lin=($tot_cost_arr[$job_no_ex_lin]/$dzn_qnty_ex_lin)*$ex_sewing_qty_in_lin;
					 $ex_cm_value_inhouse_lin=($tot_cost_arr[$job_no_ex_lin]/$dzn_qnty_ex_lin)*$ex_sewing_qty_inhouse_lin;
					 $ex_cm_value_outbound_lin=($tot_cost_arr[$job_no_ex_lin]/$dzn_qnty_ex_lin)*$ex_sewing_qty_outbound_lin;
					 
					 $prod_date_buyer_wise_lin[$ex_row[csf("buyer_name")]]['ex_cm_value_in_lin']+=$ex_cm_value_in_lin;
					 $prod_date_buyer_wise_lin[$ex_row[csf("buyer_name")]]['ex_cm_value_inhouse_lin']+=$ex_cm_value_inhouse_lin;
					 $prod_date_buyer_wise_lin[$ex_row[csf("buyer_name")]]['ex_cm_value_outbound_lin']+=$ex_cm_value_outbound_lin;
					 $prod_date_buyer_wise_lin[$ex_row[csf("buyer_name")]]['ex_factory_smry_lin']+=$ex_row[csf("ex_factory_qnty_lin")];
					 $prod_date_buyer_wise_lin[$ex_row[csf("buyer_name")]]['ex_factory_qnty_inhouse_lin']+=$ex_row[csf("ex_factory_qnty_inhouse_lin")];
					 $prod_date_buyer_wise_lin[$ex_row[csf("buyer_name")]]['ex_factory_qnty_outbound_lin']+=$ex_row[csf("ex_factory_qnty_outbound_lin")];	
					 $prod_date_buyer_wise_lin[$ex_row[csf("buyer_name")]]['ex_factory_smry_unitPrice_lin']=($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']); 
					$prod_date_buyer_wise_lin[$ex_row[csf("buyer_name")]]['ex_factory_smry_fobVal_inhouse_lin']+=$ex_row[csf("ex_factory_qnty_inhouse_lin")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
					$prod_date_buyer_wise_lin[$ex_row[csf("buyer_name")]]['ex_factory_smry_fobVal_outbound_lin']+=$ex_row[csf("ex_factory_qnty_outbound_lin")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
					$prod_date_buyer_wise_lin[$ex_row[csf("buyer_name")]]['ex_factory_smry_fobVal_lin']+=$ex_row[csf("ex_factory_qnty_lin")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
				}
				if($ex_row[csf("product_category")]==1 || $ex_row[csf("product_category")]==2 || $ex_row[csf("product_category")]==3 || $ex_row[csf("product_category")]==4 || $ex_row[csf("product_category")]==5)
				{
					$prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['ex_factory']+=$ex_row[csf("ex_factory_qnty")];
					$prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['ex_factory_qnty_inhouse']+=$ex_row[csf("ex_factory_qnty_inhouse")];
					$prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['ex_factory_qnty_outbound']+=$ex_row[csf("ex_factory_qnty_outbound")];				
					
					$prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['ex_factory_val']+=$ex_row[csf("ex_factory_qnty")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
	
	  				//for summery part
					 $ex_cm_value_in=0; $ex_cm_value_inhouse=0; $ex_cm_value_outbound=0; $ex_sewing_qty_in=0; $ex_sewing_qty_inhouse=0; $ex_sewing_qty_outbound=0;
					 
					 $ex_sewing_qty_in=$ex_row[csf("ex_factory_qnty")];
					 $ex_sewing_qty_inhouse=$ex_row[csf("ex_factory_qnty_inhouse")];
					 $ex_sewing_qty_outbound=$ex_row[csf("ex_factory_qnty_outbound")];
					 
					 $job_no_ex=$job_array[$ex_row[csf("po_break_down_id")]]['job_no'];
					 $total_ex_set_qnty=$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty'];
					 $costing_per_ex=$costing_per_arr[$job_no_ex];
					 
					 if($costing_per_ex==1) $dzn_qnty_ex=12;
					 else if($costing_per_ex==3) $dzn_qnty_ex=12*2;
					 else if($costing_per_ex==4) $dzn_qnty_ex=12*3;
					 else if($costing_per_ex==5) $dzn_qdzn_qnty_exnty=12*4;
					 else $dzn_qnty_ex=1;
								
					 $dzn_qnty_ex=$dzn_qnty_ex*$total_ex_set_qnty;
					 $ex_cm_value_in=($tot_cost_arr[$job_no_ex]/$dzn_qnty_ex)*$ex_sewing_qty_in;
					 $ex_cm_value_inhouse=($tot_cost_arr[$job_no_ex]/$dzn_qnty_ex)*$ex_sewing_qty_inhouse;
					 $ex_cm_value_outbound=($tot_cost_arr[$job_no_ex]/$dzn_qnty_ex)*$ex_sewing_qty_outbound;
					 
					 
					 $prod_date_buyer_wise[$ex_row[csf("serving_company")]][$ex_row[csf("buyer_name")]]['ex_cm_value_in']+=$ex_cm_value_in;
					 $prod_date_buyer_wise[$ex_row[csf("serving_company")]][$ex_row[csf("buyer_name")]]['ex_cm_value_inhouse']+=$ex_cm_value_inhouse;
					 $prod_date_buyer_wise[$ex_row[csf("serving_company")]][$ex_row[csf("buyer_name")]]['ex_cm_value_outbound']+=$ex_cm_value_outbound;
					 $prod_date_buyer_wise[$ex_row[csf("serving_company")]][$ex_row[csf("buyer_name")]]['ex_factory_smry']+=$ex_row[csf("ex_factory_qnty")];
					 $prod_date_buyer_wise[$ex_row[csf("serving_company")]][$ex_row[csf("buyer_name")]]['ex_factory_qnty_inhouse']+=$ex_row[csf("ex_factory_qnty_inhouse")];
					 $prod_date_buyer_wise[$ex_row[csf("serving_company")]][$ex_row[csf("buyer_name")]]['ex_factory_qnty_outbound']+=$ex_row[csf("ex_factory_qnty_outbound")];	
					 $prod_date_buyer_wise[$ex_row[csf("serving_company")]][$ex_row[csf("buyer_name")]]['ex_factory_smry_unitPrice']=$job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty'];
					$prod_date_buyer_wise[$ex_row[csf("serving_company")]][$ex_row[csf("buyer_name")]]['ex_factory_smry_fobVal_inhouse']+=$ex_row[csf("ex_factory_qnty_inhouse")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
					$prod_date_buyer_wise[$ex_row[csf("serving_company")]][$ex_row[csf("buyer_name")]]['ex_factory_smry_fobVal_outbound']+=$ex_row[csf("ex_factory_qnty_outbound")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
					$prod_date_buyer_wise[$ex_row[csf("serving_company")]][$ex_row[csf("buyer_name")]]['ex_factory_smry_fobVal']+=$ex_row[csf("ex_factory_qnty")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
					
					//for summary part
					 $prod_date_buyer_wise_smry[$ex_row[csf("buyer_name")]]['ex_cm_value_in']+=$ex_cm_value_in;
					 $prod_date_buyer_wise_smry[$ex_row[csf("buyer_name")]]['ex_cm_value_inhouse']+=$ex_cm_value_inhouse;
					 $prod_date_buyer_wise_smry[$ex_row[csf("buyer_name")]]['ex_cm_value_outbound']+=$ex_cm_value_outbound;
					 $prod_date_buyer_wise_smry[$ex_row[csf("buyer_name")]]['ex_factory_smry']+=$ex_row[csf("ex_factory_qnty")];
					 $prod_date_buyer_wise_smry[$ex_row[csf("buyer_name")]]['ex_factory_qnty_inhouse']+=$ex_row[csf("ex_factory_qnty_inhouse")];
					 $prod_date_buyer_wise_smry[$ex_row[csf("buyer_name")]]['ex_factory_qnty_outbound']+=$ex_row[csf("ex_factory_qnty_outbound")];	
					 $prod_date_buyer_wise_smry[$ex_row[csf("buyer_name")]]['ex_factory_smry_unitPrice']=$job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty'];
					 $prod_date_buyer_wise_smry[$ex_row[csf("buyer_name")]]['ex_factory_smry_fobVal_inhouse']+=$ex_row[csf("ex_factory_qnty_inhouse")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
					$prod_date_buyer_wise_smry[$ex_row[csf("buyer_name")]]['ex_factory_smry_fobVal_outbound']+=$ex_row[csf("ex_factory_qnty_outbound")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
					$prod_date_buyer_wise_smry[$ex_row[csf("buyer_name")]]['ex_factory_smry_fobVal']+=$ex_row[csf("ex_factory_qnty")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
					//end
					
				}
				 
				// end for summary part
			}
			
			 ksort($prod_date);
			 $i=1;
			 $printing=0; $embing=0; $cuting_in=0; $cuting_out=0; $cuting=0; $sewing_in=0; $sewing_out=0; $sewing=0; $finish_in=0; $finish_out=0; $finish=0; $carton=0; $ord_in=0; $ord_out=0; $ord_tot=0;
			 
			if($cbo_company==0) $cbo_company_yes=$cbo_working_company.'_'.'workingComp'; else $cbo_company_yes=$cbo_company.'_'.'mainComp';
		?>
        <table width="2300px " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
         <p style="float:left"><strong>Buyer Wise Summary Part <? if($cbo_product_cat!=0){ echo '('.$product_category[$cbo_product_cat].')';} else {} ?></strong></p>
            <thead>
                <tr>
                    <th colspan="15">Production Status <? echo str_replace("'","",$txt_date_from) .' To '. str_replace("'","",$txt_date_to); ?> </th>
                    <th colspan="9">Export status  <? echo str_replace("'","",$txt_date_from) .' To '. str_replace("'","",$txt_date_to); ?></th>
                    <!--<th colspan="2">Wating for Export</th>-->
                </tr>
                <tr>
                 	<th width="30" rowspan="3" valign="middle">SL</th>
                    <th width="150" rowspan="3" valign="middle">Buyer Name</th>
                    <th width="100" colspan="3">Cutting</th>
                    <th width="100" colspan="3">Sewing</th>
                    <th width="100" colspan="3">Finishing</th>
                    <th width="100">Sewing CM Value</th>
                    <th width="100" colspan="3">FOB Value(On Sewing Qty)</th>
                    <th width="100" colspan="3">Ex-Factory Qty</th>
                    <th width="100" colspan="3">Ex-Factory CM Value</th>
                    <th width="100" colspan="3">FOB Value(On Ex-Factory Qty)</th>
                    <!--<th width="150" rowspan="3" valign="middle">Sew Out to Ship Bal</th>
                    <th rowspan="3" valign="middle">Sew Out to Ship Bal. FOB Value</th>-->
                </tr>
                <tr>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                </tr>
            </thead>
        </table>
        <div style="max-height:420px; overflow-y:scroll; width:2320px" id="scroll_body">
       	<table cellspacing="0" border="1" class="rpt_table"  width="2300px" rules="all" id="scroll_body" >
        <?
		$inc=1;
		$all_po_ids="";
		
		foreach($prod_date_buyer_wise_smry as $buyerKey =>$buyer_value)
		{
			if($buyerKey!="")
			{
				
				//if($prod_date_buyer_wise_smry[$buyerKey]['cutting_qnty']!="" || $prod_date_buyer_wise_smry[$buyerKey]['sewing_qnty']!="" || $prod_date_buyer_wise_smry[$buyerKey]['finish_qnty']!="")
				//{
				/*foreach($comp_value as $buyerKey =>$buyer_value)
				{*/
				
					if ($inc%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
		
				/*$date_all=add_date(str_replace("'","",$txt_date_from),$jk);
				$newdate =change_date_format($date_all);
				$po_id=$prod_date[$newdate]['po_breakdown_id'];
				//echo $all_po_id=chop($po_id,',');
				
				$all_po_ids.=$prod_date[$newdate]['po_breakdown_id'];
				//echo $sql_select_buyer="select b.buyer_name from a.wo_po_break_down,b.wo_po_details_master where a.id in($all_po_id) and a.job_no_mst=b.job_no";
				
				$produce_qty=$prod_date[$newdate]['sewingout_qnty_inhouse_pcs']/60;
				$effiecy_aff_perc=$produce_qty/($tsmvArr[$newdate]['smv']/60)*100;*/
				$poQTY=$job_array_summary[$companyKey][$buyerKey]['po_qty'];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_<? echo $inc; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $inc; ?>">
					<td width="30" ><? echo $inc; ?></td>
					<td width="150"><? echo $buyer_short_library[$buyerKey];//$buyer_short_library[$prod_date_buyer_wise_smry[$buyerKey]['buyer_name']] ?></td>
					<?php /*?><td width="100" align="right"><? echo number_format($poQTY,2); $smry_tot_po_qnty+=$poQTY; ?></td><?php */?>
					
				   
				   
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['cutting_qnty_inhouse'],2); $smry_tot_cutting_qnty_inhouse+=$prod_date_buyer_wise_smry[$buyerKey]['cutting_qnty_inhouse']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['cutting_qnty_outbound'],2); $smry_tot_cutting_qnty_outbound+=$prod_date_buyer_wise_smry[$buyerKey]['cutting_qnty_outbound']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['cutting_qnty'],2); $smry_tot_cutting_qnty+=$prod_date_buyer_wise_smry[$buyerKey]['cutting_qnty']; ?></td>
					
					
					<?php /*?><td width="100" align="right"><? echo number_format($prod_date_buyer_wise_smry[$companyKey][$buyerKey]['sewing_input_qnty'],2); $smry_tot_sewing_input_qnty+=$prod_date_buyer_wise_smry[$companyKey][$buyerKey]['sewing_input_qnty']; ?></td><?php */?>
					<?php /*?><td width="100" align="right"><? echo number_format($prod_date_buyer_wise_smry[$companyKey][$buyerKey]['sewing_qnty'],2); $smry_tot_sewing_qnty+=$prod_date_buyer_wise_smry[$companyKey][$buyerKey]['sewing_qnty'];  ?></td><?php */?>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['sewingout_qnty_inhouse'],2); $smry_tot_sewing_qnty_inhouse+=$prod_date_buyer_wise_smry[$buyerKey]['sewingout_qnty_inhouse']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['sewingout_qnty_outbound'],2); $smry_tot_sewing_qnty_outbound+=$prod_date_buyer_wise_smry[$buyerKey]['sewingout_qnty_outbound']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['sewing_qnty'],2); $smry_tot_sewing_qnty+=$prod_date_buyer_wise_smry[$buyerKey]['sewing_qnty']; ?></td>
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['finish_qnty_inhouse'],2); $smry_tot_finish_qnty_inhouse+=$prod_date_buyer_wise_smry[$buyerKey]['finish_qnty_inhouse']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['finish_qnty_outbound'],2); $smry_tot_finish_qnty_outbound+=$prod_date_buyer_wise_smry[$buyerKey]['finish_qnty_outbound']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['finish_qnty'],2); $smry_tot_finish_qnty+=$prod_date_buyer_wise_smry[$buyerKey]['finish_qnty']; ?></td>
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['cm_value_in'],2); $smry_tot_cm_value_in+=$prod_date_buyer_wise_smry[$buyerKey]['cm_value_in']; ?></td>
					
					
					<td width="80" align="right"><? $swing_val_inhouse= $prod_date_buyer_wise_smry[$buyerKey]['sewingout_value_inhouse']; echo number_format($swing_val_inhouse,2); $smry_tot_sewing_fob_val_input_qnty_inhouse+=$swing_val_inhouse; ?></td>
					<td width="80" align="right"><? $swing_val_outbound= $prod_date_buyer_wise_smry[$buyerKey]['sewingout_value_outbound']; echo number_format($swing_val_outbound,2); $smry_tot_sewing_fob_val_input_qnty_outbound+=$swing_val_outbound; ?></td>
					<td width="80" align="right"><? echo number_format(($swing_val_inhouse+$swing_val_outbound),2);  $total_sew_order_value+=($swing_val_inhouse+$swing_val_outbound);  ?></td>
					
					
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['ex_factory_qnty_inhouse'],2); $smry_tot_ex_factory_inhouse+=$prod_date_buyer_wise_smry[$buyerKey]['ex_factory_qnty_inhouse']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['ex_factory_qnty_outbound'],2); $smry_tot_ex_factory_outbound+=$prod_date_buyer_wise_smry[$buyerKey]['ex_factory_qnty_outbound']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['ex_factory_smry'],2); $smry_tot_ex_factory+=$prod_date_buyer_wise_smry[$buyerKey]['ex_factory_smry']; ?></td>
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['ex_cm_value_inhouse'],2); $smry_tot_ex_factory_val_inhouse+=$prod_date_buyer_wise_smry[$buyerKey]['ex_cm_value_inhouse']; ?></td> 
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['ex_cm_value_outbound'],2); $smry_tot_ex_factory_val_outbound+=$prod_date_buyer_wise_smry[$buyerKey]['ex_cm_value_outbound']; ?></td> 
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['ex_cm_value_in'],2); $smry_tot_ex_factory_val+=$prod_date_buyer_wise_smry[$buyerKey]['ex_cm_value_in']; ?></td> 
					
				   <?php /*?> <td width="100" align="right"><? $exfactory_unitPrice= $prod_date_buyer_wise_smry[$companyKey][$buyerKey]['ex_factory_smry_unitPrice']*$prod_date_buyer_wise_smry[$companyKey][$buyerKey]['ex_factory_smry']; echo number_format($exfactory_unitPrice,2); $smry_tot_ex_factory_fob_val+=$exfactory_unitPrice; ?></td><?php */?>
					
					
					<td width="80" align="right"><? $exfactory_unitPrice= $prod_date_buyer_wise_smry[$buyerKey]['ex_factory_smry_fobVal_inhouse']; echo number_format($exfactory_unitPrice,2); $smry_tot_ex_factory_fob_val_inhouse+=$exfactory_unitPrice; ?></td>
					 <td width="80" align="right"><? $exfactory_unitPrice= $prod_date_buyer_wise_smry[$buyerKey]['ex_factory_smry_fobVal_outbound']; echo number_format($exfactory_unitPrice,2); $smry_tot_ex_factory_fob_val_outbound+=$exfactory_unitPrice; ?></td>
					 <td width="80" align="right"><? $exfactory_unitPrice= $prod_date_buyer_wise_smry[$buyerKey]['ex_factory_smry_fobVal']; echo number_format($exfactory_unitPrice,2); $smry_tot_ex_factory_fob_val+=$exfactory_unitPrice; ?></td>
					 
					 
					<?php /*?><td width="150" align="right"><? $ship_balance=$prod_date_buyer_wise_smry[$buyerKey]['sewing_qnty']-$prod_date_buyer_wise_smry[$buyerKey]['ex_factory_smry']; echo number_format($ship_balance,2); $smry_tot_ship_balance+=$ship_balance; ?></td><?php */?>
					
					
					<?php /*?><td  align="right"><? $sew_out_ship_ex_fob_balance=($swing_val_inhouse+$swing_val_outbound)-$prod_date_buyer_wise_smry[$buyerKey]['ex_factory_smry_fobVal']; echo number_format($sew_out_ship_ex_fob_balance,2); $smry_tot_sew_out_ship_ex_fob_balance+=$sew_out_ship_ex_fob_balance; ?></td><?php */?>
				   
				</tr>
			<?
			//number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_fob_val_input_qnty'],2).'_'.
		$inc++;
				//}
			}
			//}
		}
	  	echo $all_po_ids;
		?>
         <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                <th width="150">Total</th>
              <?php /*?>  <th width="100"><? echo number_format($smry_tot_po_qnty,2); ?></th><?php */?>
                <th width="80"><? echo number_format($smry_tot_cutting_qnty_inhouse,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_cutting_qnty_outbound,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_cutting_qnty,2); ?></th>
                <?php /*?><th width="100"><? echo number_format($smry_tot_sewing_input_qnty,2); ?></th><?php */?>
               <?php /*?> <th width="100"><? echo number_format($smry_tot_sewing_qnty,2); ?></th><?php */?>
               
                <th width="80"><? echo number_format($smry_tot_sewing_qnty_inhouse,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_sewing_qnty_outbound,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_sewing_qnty,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_finish_qnty_inhouse,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_finish_qnty_outbound,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_finish_qnty,2); ?></th>
                
                <th width="100"><? echo number_format($smry_tot_cm_value_in,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_sewing_fob_val_input_qnty_inhouse,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_sewing_fob_val_input_qnty_outbound,2); ?></th>
                <th width="80"><? echo number_format($total_sew_order_value,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_ex_factory_inhouse,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_outbound,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_ex_factory_val_inhouse,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_val_outbound,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_val,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_ex_factory_fob_val_inhouse,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_fob_val_outbound,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_fob_val,2); ?></th>

               <?php /*?> <th width="150"><? echo number_format($smry_tot_ship_balance,2); ?></th>
                <th><? echo number_format($smry_tot_sew_out_ship_ex_fob_balance,2); ?></th><?php */?>
             </tr>
           </tfoot>
        </table>
        </div>
         <br/>
         <!--  ITEM wise-->
         <?
         if($cbo_product_cat==0)
         {
		 ?>
        <table width="2300px " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
         <p style="float:left"><strong>Summary all Company: Regular Order</strong></p>
            <thead>
                <tr>
                    <th colspan="15">Production Status <? echo str_replace("'","",$txt_date_from) .' To '. str_replace("'","",$txt_date_to); ?> </th>
                    <th colspan="9">Export status  <? echo str_replace("'","",$txt_date_from) .' To '. str_replace("'","",$txt_date_to); ?></th>
                    <!--<th colspan="2">Wating for Export</th>-->
                </tr>
                <tr>
                 	<th width="30" rowspan="3" valign="middle">SL</th>
                    <th width="150" rowspan="3" valign="middle">Buyer Name</th>
                    <th width="100" colspan="3">Cutting</th>
                    <th width="100" colspan="3">Sewing</th>
                    <th width="100" colspan="3">Finishing</th>
                    <th width="100">Sewing CM Value</th>
                    <th width="100" colspan="3">FOB Value(On Sewing Qty)</th>
                    <th width="100" colspan="3">Ex-Factory Qty</th>
                    <th width="100" colspan="3">Ex-Factory CM Value</th>
                    <th width="100" colspan="3">FOB Value(On Ex-Factory Qty)</th>
                   <!-- <th width="150" rowspan="3" valign="middle">Sew Out to Ship Bal</th>
                    <th rowspan="3" valign="middle">Sew Out to Ship Bal. FOB Value</th>-->
                </tr>
                <tr>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                </tr>
            </thead>
        </table>
        <div> 
       	<table cellspacing="0" border="1" class="rpt_table"  width="2300px" rules="all" id="scroll_body_summery_itemCat">
        <?
		$incs=1;
		$all_po_ids="";
		/*for($jk=0;$jk<$datediff;$jk++)
			{*/
		foreach($prod_date_buyer_wise_all as $buyerKey =>$buyer_value)
		{
			if($buyerKey!="")
			{
				/*foreach($prod_date_buyer_wise_all as $buyerKey =>$buyer_value)
				{*/
				//if($prod_date_buyer_wise_all[$buyerKey]['cutting_qnty_all']!="" || $prod_date_buyer_wise_all[$buyerKey]['sewing_qnty_all']!="" || $prod_date_buyer_wise_all[$buyerKey]['finish_qnty_all']!="")
				//{
				if ($incs%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				$poQTY=$job_array_summary[$companyKey][$buyerKey]['po_qty'];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_2_<? echo $incs; ?>','<? echo $bgcolor; ?>')" id="trs_2_<? echo $incs; ?>">
					<td width="30" ><? echo $incs; ?></td>
					<td width="150"><? echo $buyer_short_library[$buyerKey];//$buyer_short_library[$prod_date_buyer_wise_all[$buyerKey]['buyer_name']]; ?></td>
					<?php /*?><td width="100" align="right"><? echo number_format($poQTY,2); $smry_tot_po_qnty+=$poQTY; ?></td><?php */?>
					
				   
				   
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['cutting_qnty_inhouse_all'],2); $smry_tot_cutting_qnty_inhouse_all+=$prod_date_buyer_wise_all[$buyerKey]['cutting_qnty_inhouse_all']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['cutting_qnty_outbound_all'],2); $smry_tot_cutting_qnty_outbound_all+=$prod_date_buyer_wise_all[$buyerKey]['cutting_qnty_outbound_all']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['cutting_qnty_all'],2); $smry_tot_cutting_qnty_all+=$prod_date_buyer_wise_all[$buyerKey]['cutting_qnty_all']; ?></td>
					
					
					<?php /*?><td width="100" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_input_qnty'],2); $smry_tot_sewing_input_qnty+=$prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_input_qnty']; ?></td><?php */?>
					<?php /*?><td width="100" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_qnty'],2); $smry_tot_sewing_qnty+=$prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_qnty'];  ?></td><?php */?>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['sewingout_qnty_inhouse_all'],2); $smry_tot_sewing_qnty_inhouse_all+=$prod_date_buyer_wise_all[$buyerKey]['sewingout_qnty_inhouse_all']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['sewingout_qnty_outbound_all'],2); $smry_tot_sewing_qnty_outbound_all+=$prod_date_buyer_wise_all[$buyerKey]['sewingout_qnty_outbound_all']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['sewing_qnty_all'],2); $smry_tot_sewing_qnty_all+=$prod_date_buyer_wise_all[$buyerKey]['sewing_qnty_all']; ?></td>
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['finish_qnty_inhouse_all'],2); $smry_tot_finish_qnty_inhouse_all+=$prod_date_buyer_wise_all[$buyerKey]['finish_qnty_inhouse_all']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['finish_qnty_outbound_all'],2); $smry_tot_finish_qnty_outbound_all+=$prod_date_buyer_wise_all[$buyerKey]['finish_qnty_outbound_all']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['finish_qnty_all'],2); $smry_tot_finish_qnty_all+=$prod_date_buyer_wise_all[$buyerKey]['finish_qnty_all']; ?></td>
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['cm_value_in_all'],2); $smry_tot_cm_value_in_all+=$prod_date_buyer_wise_all[$buyerKey]['cm_value_in_all']; ?></td>
					
					
					<td width="80" align="right"><? $swing_val_inhouse_all= $prod_date_buyer_wise_all[$buyerKey]['sewingout_value_inhouse_all']; echo number_format($swing_val_inhouse_all,2); $smry_tot_sewing_fob_val_input_qnty_inhouse_all+=$swing_val_inhouse_all; ?></td>
					<td width="80" align="right"><? $swing_val_outbound_all= $prod_date_buyer_wise_all[$buyerKey]['sewingout_value_outbound_all']; echo number_format($swing_val_outbound_all,2); $smry_tot_sewing_fob_val_input_qnty_outbound_all+=$swing_val_outbound_all; ?></td>
					<td width="80" align="right"><? echo number_format(($swing_val_inhouse_all+$swing_val_outbound_all),2);  $total_sew_order_value_all+=($swing_val_inhouse_all+$swing_val_outbound_all);  ?></td>
					
					
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['ex_factory_qnty_inhouse_all'],2); $smry_tot_ex_factory_inhouse_all+=$prod_date_buyer_wise_all[$buyerKey]['ex_factory_qnty_inhouse_all']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['ex_factory_qnty_outbound_all'],2); $smry_tot_ex_factory_outbound_all+=$prod_date_buyer_wise_all[$buyerKey]['ex_factory_qnty_outbound_all']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['ex_factory_smry_all'],2); $smry_tot_ex_factory_all+=$prod_date_buyer_wise_all[$buyerKey]['ex_factory_smry_all']; ?></td>
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['ex_cm_value_inhouse_all'],2); $smry_tot_ex_factory_val_inhouse_all+=$prod_date_buyer_wise_all[$buyerKey]['ex_cm_value_inhouse_all']; ?></td> 
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['ex_cm_value_outbound_all'],2); $smry_tot_ex_factory_val_outbound_all+=$prod_date_buyer_wise_all[$buyerKey]['ex_cm_value_outbound_all']; ?></td> 
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['ex_cm_value_in_all'],2); $smry_tot_ex_factory_val_all+=$prod_date_buyer_wise_all[$buyerKey]['ex_cm_value_in_all']; ?></td> 
					
				   <?php /*?> <td width="100" align="right"><? $exfactory_unitPrice= $prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_smry_unitPrice']*$prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_smry']; echo number_format($exfactory_unitPrice,2); $smry_tot_ex_factory_fob_val+=$exfactory_unitPrice; ?></td><?php */?>
					
					
					<td width="80" align="right"><? $exfactory_unitPrice_all= $prod_date_buyer_wise_all[$buyerKey]['ex_factory_smry_fobVal_inhouse_all']; echo number_format($exfactory_unitPrice_all,2); $smry_tot_ex_factory_fob_val_inhouse_all+=$exfactory_unitPrice_all; ?></td>
					 <td width="80" align="right"><? $exfactory_unitPrice_all= $prod_date_buyer_wise_all[$buyerKey]['ex_factory_smry_fobVal_outbound_all']; echo number_format($exfactory_unitPrice_all,2); $smry_tot_ex_factory_fob_val_outbound_all+=$exfactory_unitPrice_all; ?></td>
					 <td width="80" align="right"><? $exfactory_unitPrice_all= $prod_date_buyer_wise_all[$buyerKey]['ex_factory_smry_fobVal_all']; echo number_format($exfactory_unitPrice_all,2); $smry_tot_ex_factory_fob_val_all+=$exfactory_unitPrice_all; ?></td>
					 
					 
					<?php /*?><td width="150" align="right"><? $ship_balance_all=$prod_date_buyer_wise_all[$buyerKey]['sewing_qnty_all']-$prod_date_buyer_wise_all[$buyerKey]['ex_factory_smry_all']; echo number_format($ship_balance_all,2); $smry_tot_ship_balance_all+=$ship_balance_all; ?></td><?php */?>
					
					
					<?php /*?><td  align="right"><? $sew_out_ship_ex_fob_balance_all=($swing_val_inhouse_all+$swing_val_outbound_all)-$prod_date_buyer_wise_all[$buyerKey]['ex_factory_smry_fobVal_all']; echo number_format($sew_out_ship_ex_fob_balance_all,2); $smry_tot_sew_out_ship_ex_fob_balance_all+=$sew_out_ship_ex_fob_balance_all; ?></td><?php */?>
				   
				</tr>
			<?
			//number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_fob_val_input_qnty'],2).'_'.
		$incs++;
				//}
			}
		}
		//}
		//echo $all_po_ids;
		?>
         <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                <th width="150">Total</th>
              <?php /*?>  <th width="100"><? echo number_format($smry_tot_po_qnty,2); ?></th><?php */?>
                <th width="80"><? echo number_format($smry_tot_cutting_qnty_inhouse_all,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_cutting_qnty_outbound_all,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_cutting_qnty_all,2); ?></th>
                <?php /*?><th width="100"><? echo number_format($smry_tot_sewing_input_qnty,2); ?></th><?php */?>
               <?php /*?> <th width="100"><? echo number_format($smry_tot_sewing_qnty,2); ?></th><?php */?>
               
                <th width="80"><? echo number_format($smry_tot_sewing_qnty_inhouse_all,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_sewing_qnty_outbound_all,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_sewing_qnty_all,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_finish_qnty_inhouse_all,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_finish_qnty_outbound_all,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_finish_qnty_all,2); ?></th>
                
                <th width="100"><? echo number_format($smry_tot_cm_value_in_all,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_sewing_fob_val_input_qnty_inhouse_all,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_sewing_fob_val_input_qnty_outbound_all,2); ?></th>
                <th width="80"><? echo number_format($total_sew_order_value_all,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_ex_factory_inhouse_all,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_outbound_all,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_all,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_ex_factory_val_inhouse_all,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_val_outbound_all,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_val_all,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_ex_factory_fob_val_inhouse_all,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_fob_val_outbound_all,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_fob_val_all,2); ?></th>

               <?php /*?> <th width="150"><? echo number_format($smry_tot_ship_balance_all,2); ?></th>
                <th><? echo number_format($smry_tot_sew_out_ship_ex_fob_balance_all,2); ?></th><?php */?>
             </tr>
           </tfoot>
        </table>
        </div>
         <br/>

         <table width="2300px " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
         <p style="float:left"><strong>Summary all Company: UG Order</strong></p>
            <thead>
                <tr>
                    <th colspan="15">Production Status <? echo str_replace("'","",$txt_date_from) .' To '. str_replace("'","",$txt_date_to); ?> </th>
                    <th colspan="9">Export status  <? echo str_replace("'","",$txt_date_from) .' To '. str_replace("'","",$txt_date_to); ?></th>
                    <!--<th colspan="2">Wating for Export</th>-->
                </tr>
                <tr>
                 	<th width="30" rowspan="3" valign="middle">SL</th>
                    <th width="150" rowspan="3" valign="middle">Buyer Name</th>
                    <th width="100" colspan="3">Cutting</th>
                    <th width="100" colspan="3">Sewing</th>
                    <th width="100" colspan="3">Finishing</th>
                    <th width="100">Sewing CM Value</th>
                    <th width="100" colspan="3">FOB Value(On Sewing Qty)</th>
                    <th width="100" colspan="3">Ex-Factory Qty</th>
                    <th width="100" colspan="3">Ex-Factory CM Value</th>
                    <th width="100" colspan="3">FOB Value(On Ex-Factory Qty)</th>
                   <!-- <th width="150" rowspan="3" valign="middle">Sew Out to Ship Bal</th>
                    <th rowspan="3" valign="middle">Sew Out to Ship Bal. FOB Value</th>-->
                </tr>
                <tr>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                </tr>
            </thead>
         </table>
        <div> 
       	<table cellspacing="0" border="1" class="rpt_table"  width="2300px" rules="all" id="scroll_body_summery_itemCat_ug">
        <?
		$incss=1;
		$all_po_idss="";
		/*for($jk=0;$jk<$datediff;$jk++)
			{*/
		foreach($prod_date_buyer_wise_lin as $buyerKey =>$buyer_value)
		{
			if($buyerKey!="")
			{
				/*foreach($prod_date_buyer_wise_lin as $buyerKey =>$buyer_value)
				{*/
				
				//if($prod_date_buyer_wise_lin[$buyerKey]['cutting_qnty_lin']!="" || $prod_date_buyer_wise_lin[$buyerKey]['sewing_qnty_lin']!="" || $prod_date_buyer_wise_lin[$buyerKey]['finish_qnty_lin']!="")
				//{
				if ($incss%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				$poQTY=$job_array_summary[$companyKey][$buyerKey]['po_qty'];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_3_<? echo $incss; ?>','<? echo $bgcolor; ?>')" id="trs_3_<? echo $incss; ?>">
					<td width="30" ><? echo $incss; ?></td>
					<td width="150"><? echo $buyer_short_library[$buyerKey];//$buyer_short_library[$prod_date_buyer_wise_lin[$buyerKey]['buyer_name']]; ?></td>
					<?php /*?><td width="100" align="right"><? echo number_format($poQTY,2); $smry_tot_po_qnty+=$poQTY; ?></td><?php */?>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['cutting_qnty_inhouse_lin'],2); $smry_tot_cutting_qnty_inhouse_lin+=$prod_date_buyer_wise_lin[$buyerKey]['cutting_qnty_inhouse_lin']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['cutting_qnty_outbound_lin'],2); $smry_tot_cutting_qnty_outbound_lin+=$prod_date_buyer_wise_lin[$buyerKey]['cutting_qnty_outbound_lin']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['cutting_qnty_lin'],2); $smry_tot_cutting_qnty_lin+=$prod_date_buyer_wise_lin[$buyerKey]['cutting_qnty_lin']; ?></td>
					
					
					<?php /*?><td width="100" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_input_qnty'],2); $smry_tot_sewing_input_qnty+=$prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_input_qnty']; ?></td><?php */?>
					<?php /*?><td width="100" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_qnty'],2); $smry_tot_sewing_qnty+=$prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_qnty'];  ?></td><?php */?>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['sewingout_qnty_inhouse_lin'],2); $smry_tot_sewing_qnty_inhouse_lin+=$prod_date_buyer_wise_lin[$buyerKey]['sewingout_qnty_inhouse_lin']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['sewingout_qnty_outbound_lin'],2); $smry_tot_sewing_qnty_outbound_lin+=$prod_date_buyer_wise_lin[$buyerKey]['sewingout_qnty_outbound_lin']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['sewing_qnty_lin'],2); $smry_tot_sewing_qnty_lin+=$prod_date_buyer_wise_lin[$buyerKey]['sewing_qnty_lin']; ?></td>
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['finish_qnty_inhouse_lin'],2); $smry_tot_finish_qnty_inhouse_lin+=$prod_date_buyer_wise_lin[$buyerKey]['finish_qnty_inhouse_lin']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['finish_qnty_outbound_lin'],2); $smry_tot_finish_qnty_outbound_lin+=$prod_date_buyer_wise_lin[$buyerKey]['finish_qnty_outbound_lin']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['finish_qnty_lin'],2); $smry_tot_finish_qnty_lin+=$prod_date_buyer_wise_lin[$buyerKey]['finish_qnty_lin']; ?></td>
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['cm_value_in_lin'],2); $smry_tot_cm_value_in_lin+=$prod_date_buyer_wise_lin[$buyerKey]['cm_value_in_lin']; ?></td>
					
					
					<td width="80" align="right"><? $swing_val_inhouse_lin= $prod_date_buyer_wise_lin[$buyerKey]['sewingout_value_inhouse_lin']; echo number_format($swing_val_inhouse_lin,2); $smry_tot_sewing_fob_val_input_qnty_inhouse_lin+=$swing_val_inhouse_lin; ?></td>
					<td width="80" align="right"><? $swing_val_outbound_lin= $prod_date_buyer_wise_lin[$buyerKey]['sewingout_value_outbound_lin']; echo number_format($swing_val_outbound_lin,2); $smry_tot_sewing_fob_val_input_qnty_outbound_lin+=$swing_val_outbound_lin; ?></td>
					<td width="80" align="right"><? echo number_format(($swing_val_inhouse_lin+$swing_val_outbound_lin),2);  $total_sew_order_value_lin+=($swing_val_inhouse_lin+$swing_val_outbound_lin);  ?></td>
					
					
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['ex_factory_qnty_inhouse_lin'],2); $smry_tot_ex_factory_inhouse_lin+=$prod_date_buyer_wise_lin[$buyerKey]['ex_factory_qnty_inhouse_lin']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['ex_factory_qnty_outbound_lin'],2); $smry_tot_ex_factory_outbound_lin+=$prod_date_buyer_wise_lin[$buyerKey]['ex_factory_qnty_outbound_lin']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['ex_factory_smry_lin'],2); $smry_tot_ex_factory_lin+=$prod_date_buyer_wise_lin[$buyerKey]['ex_factory_smry_lin']; ?></td>
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['ex_cm_value_inhouse_lin'],2); $smry_tot_ex_factory_val_inhouse_lin+=$prod_date_buyer_wise_lin[$buyerKey]['ex_cm_value_inhouse_lin']; ?></td> 
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['ex_cm_value_outbound_lin'],2); $smry_tot_ex_factory_val_outbound_lin+=$prod_date_buyer_wise_lin[$buyerKey]['ex_cm_value_outbound_lin']; ?></td> 
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['ex_cm_value_in_lin'],2); $smry_tot_ex_factory_val_lin+=$prod_date_buyer_wise_lin[$buyerKey]['ex_cm_value_in_lin']; ?></td> 
					
				   <?php /*?> <td width="100" align="right"><? $exfactory_unitPrice= $prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_smry_unitPrice']*$prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_smry']; echo number_format($exfactory_unitPrice,2); $smry_tot_ex_factory_fob_val+=$exfactory_unitPrice; ?></td><?php */?>
					
					
					<td width="80" align="right"><? $exfactory_unitPrice_lin= $prod_date_buyer_wise_lin[$buyerKey]['ex_factory_smry_fobVal_inhouse_lin']; echo number_format($exfactory_unitPrice_lin,2); $smry_tot_ex_factory_fob_val_inhouse_lin+=$exfactory_unitPrice_lin; ?></td>
					 <td width="80" align="right"><? $exfactory_unitPrice_lin= $prod_date_buyer_wise_lin[$buyerKey]['ex_factory_smry_fobVal_outbound_lin']; echo number_format($exfactory_unitPrice_lin,2); $smry_tot_ex_factory_fob_val_outbound_lin+=$exfactory_unitPrice_lin; ?></td>
					 <td width="80" align="right"><? $exfactory_unitPrice_lin= $prod_date_buyer_wise_lin[$buyerKey]['ex_factory_smry_fobVal_lin']; echo number_format($exfactory_unitPrice_lin,2); $smry_tot_ex_factory_fob_val_lin+=$exfactory_unitPrice_lin; ?></td>
					 
					<?php /*?> 
					<td width="150" align="right"><? $ship_balance_lin=$prod_date_buyer_wise_lin[$buyerKey]['sewing_qnty_lin']-$prod_date_buyer_wise_lin[$buyerKey]['ex_factory_smry_lin']; echo number_format($ship_balance_lin,2); $smry_tot_ship_balance_lin+=$ship_balance_lin; ?></td><?php */?>
					
					
					<?php /*?><td  align="right"><? $sew_out_ship_ex_fob_balance_lin=($swing_val_inhouse_lin+$swing_val_outbound_lin)-$prod_date_buyer_wise_lin[$buyerKey]['ex_factory_smry_fobVal_lin']; echo number_format($sew_out_ship_ex_fob_balance_lin,2); $smry_tot_sew_out_ship_ex_fob_balance_lin+=$sew_out_ship_ex_fob_balance_lin; ?></td><?php */?>
				   
				</tr>
			<?
			//number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_fob_val_input_qnty'],2).'_'.
		$incss++;
				//}
			//}
			}
		}
		//echo $all_po_idss;
		?>
         <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                <th width="150">Total</th>
              <?php /*?>  <th width="100"><? echo number_format($smry_tot_po_qnty,2); ?></th><?php */?>
                <th width="80"><? echo number_format($smry_tot_cutting_qnty_inhouse_lin,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_cutting_qnty_outbound_lin,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_cutting_qnty_lin,2); ?></th>
                <?php /*?><th width="100"><? echo number_format($smry_tot_sewing_input_qnty,2); ?></th><?php */?>
               <?php /*?> <th width="100"><? echo number_format($smry_tot_sewing_qnty,2); ?></th><?php */?>
               
                <th width="80"><? echo number_format($smry_tot_sewing_qnty_inhouse_lin,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_sewing_qnty_outbound_lin,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_sewing_qnty_lin,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_finish_qnty_inhouse_lin,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_finish_qnty_outbound_lin,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_finish_qnty_lin,2); ?></th>
                
                <th width="100"><? echo number_format($smry_tot_cm_value_in_lin,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_sewing_fob_val_input_qnty_inhouse_lin,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_sewing_fob_val_input_qnty_outbound_lin,2); ?></th>
                <th width="80"><? echo number_format($total_sew_order_value_lin,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_ex_factory_inhouse_lin,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_outbound_lin,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_lin,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_ex_factory_val_inhouse_lin,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_val_outbound_lin,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_val_lin,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_ex_factory_fob_val_inhouse_lin,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_fob_val_outbound_lin,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_fob_val_lin,2); ?></th>

               <?php /*?> <th width="150"><? echo number_format($smry_tot_ship_balance_lin,2); ?></th>
                <th><? echo number_format($smry_tot_sew_out_ship_ex_fob_balance_lin,2); ?></th><?php */?>
             </tr>
           </tfoot>
        </table>
        </div>
         <br/>
      
    	<? 
		}  //end prod cat if condition 
		
		//details part start here
		//$a="";
		$all_po_ids_dtls="";
		$workCom=explode(",",$cbo_working_company);
		foreach($prod_date_buyer_wise as $companyKey =>$comp_value)
		{
			//$a.=$companyKey.',';
			
			$inc_dtls=1;
			if($companyKey!="")
			{
		?>
			<table width="2300px" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
	         <p style="float:left;"><strong>Working Company:  <? echo $company_library[$companyKey];?></strong></p>
	            <thead>
	                <tr>
	                    <th colspan="15">Production Status <? echo str_replace("'","",$txt_date_from) .' To '. str_replace("'","",$txt_date_to); ?> </th>
	                    <th colspan="9">Export status  <? echo str_replace("'","",$txt_date_from) .' To '. str_replace("'","",$txt_date_to); ?></th>
	                    <!--<th colspan="2">Wating for Export</th>-->
	                </tr>
	                <tr>
	                 	<th width="30" rowspan="3" valign="middle">SL</th>
	                    <th width="150" rowspan="3" valign="middle">Buyer Name</th>
	                    <th width="100" colspan="3">Cutting</th>
	                    <th width="100" colspan="3">Sewing</th>
	                    <th width="100" colspan="3">Finishing</th>
	                    <th width="100">Sewing CM Value</th>
	                    <th width="100" colspan="3">FOB Value(On Sewing Qty)</th>
	                    <th width="100" colspan="3">Ex-Factory Qty</th>
	                    <th width="100" colspan="3">Ex-Factor CM Value</th>
	                    <th width="100" colspan="3">FOB Value(On Ex-Factory Qty)</th>
	                    <!--<th width="150" rowspan="3" valign="middle">Sew Out to Ship Bal</th>
	                    <th rowspan="3" valign="middle">Sew Out to Ship Bal. FOB Value</th>-->
	                </tr>
	                <tr>
	                    <th rowspan="2" width="80">In House</th>
	                    <th rowspan="2" width="80">Sub Contact</th>
	                    <th rowspan="2" width="80">Total</th>
	                    <th rowspan="2" width="80">In House</th>
	                    <th rowspan="2" width="80">Sub Contact</th>
	                    <th rowspan="2" width="80">Total</th>
	                    <th rowspan="2" width="80">In House</th>
	                    <th rowspan="2" width="80">Sub Contact</th>
	                    <th rowspan="2" width="80">Total</th>
	                    <th rowspan="2" width="80">In House</th>
	                    <th rowspan="2" width="80">In House</th>
	                    <th rowspan="2" width="80">Sub Contact</th>
	                    <th rowspan="2" width="80">Total</th>
	                    <th rowspan="2" width="80">In House</th>
	                    <th rowspan="2" width="80">Sub Contact</th>
	                    <th rowspan="2" width="80">Total</th>
	                    <th rowspan="2" width="80">In House</th>
	                    <th rowspan="2" width="80">Sub Contact</th>
	                    <th rowspan="2" width="80">Total</th>
	                    <th rowspan="2" width="80">In House</th>
	                    <th rowspan="2" width="80">Sub Contact</th>
	                    <th rowspan="2" width="80">Total</th>
	                </tr>
	            </thead>
	        </table>
	        <div style="max-height:420px; overflow-y:scroll; width:2320px;" id="scroll_body_summery_dtls">
	       	<table cellspacing="0" border="1" class="rpt_table"  width="2300px" rules="all" id="scroll_body_summery_dtls" >
        <?
		
			
				foreach($comp_value as $buyerKey =>$buyer_value)
				{
				
					if ($inc_dtls%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
		
				$poQTY=$job_array_summary[$companyKey][$buyerKey]['po_qty'];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_4_<? echo $inc_dtls; ?>','<? echo $bgcolor; ?>')" id="trs_4_<? echo $inc_dtls; ?>">
					<td width="30" ><? echo $inc_dtls; ?></td>
					<td width="150" title="<? echo $company_library[$companyKey]; ?>"><? echo $buyer_short_library[$buyerKey];//$buyer_short_library[$prod_date_buyer_wise[$companyKey][$buyerKey]['buyer_name']]; ?></td>
					
					
				   
				   
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['cutting_qnty_inhouse'],2); $smry_tot_cutting_qnty_inhouse_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['cutting_qnty_inhouse']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['cutting_qnty_outbound'],2); $smry_tot_cutting_qnty_outbound_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['cutting_qnty_outbound']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['cutting_qnty'],2); $smry_tot_cutting_qnty_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['cutting_qnty']; ?></td>
					
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['sewingout_qnty_inhouse'],2); $smry_tot_sewing_qnty_inhouse_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['sewingout_qnty_inhouse']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['sewingout_qnty_outbound'],2); $smry_tot_sewing_qnty_outbound_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['sewingout_qnty_outbound']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_qnty'],2); $smry_tot_sewing_qnty_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_qnty']; ?></td>
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['finish_qnty_inhouse'],2); $smry_tot_finish_qnty_inhouse_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['finish_qnty_inhouse']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['finish_qnty_outbound'],2); $smry_tot_finish_qnty_outbound_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['finish_qnty_outbound']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['finish_qnty'],2); $smry_tot_finish_qnty_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['finish_qnty']; ?></td>
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['cm_value_in'],2); $smry_tot_cm_value_in_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['cm_value_in']; ?></td>
					
					
					<td width="80" align="right"><? $swing_val_inhouse_dtls= $prod_date_buyer_wise[$companyKey][$buyerKey]['sewingout_value_inhouse']; echo number_format($swing_val_inhouse_dtls,2); $smry_tot_sewing_fob_val_input_qnty_inhouse_dtls+=$swing_val_inhouse_dtls; ?></td>
					<td width="80" align="right"><? $swing_val_outbound_dtls= $prod_date_buyer_wise[$companyKey][$buyerKey]['sewingout_value_outbound']; echo number_format($swing_val_outbound_dtls,2); $smry_tot_sewing_fob_val_input_qnty_outbound_dtls+=$swing_val_outbound_dtls; ?></td>
					<td width="80" align="right"><? echo number_format(($swing_val_inhouse_dtls+$swing_val_outbound_dtls),2);  $total_sew_order_value_dtls+=($swing_val_inhouse_dtls+$swing_val_outbound_dtls);  ?></td>
					
					
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_qnty_inhouse'],2); $smry_tot_ex_factory_inhouse_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_qnty_inhouse']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_qnty_outbound'],2); $smry_tot_ex_factory_outbound_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_qnty_outbound']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_smry'],2); $smry_tot_ex_factory_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_smry']; ?></td>
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['ex_cm_value_inhouse'],2); $smry_tot_ex_factory_val_inhouse_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['ex_cm_value_inhouse']; ?></td> 
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['ex_cm_value_outbound'],2); $smry_tot_ex_factory_val_outbound_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['ex_cm_value_outbound']; ?></td> 
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['ex_cm_value_in'],2); $smry_tot_ex_factory_val_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['ex_cm_value_in']; ?></td> 
					
				  
					
					
					<td width="80" align="right"><? $exfactory_unitPrice_dtls= $prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_smry_fobVal_inhouse']; echo number_format($exfactory_unitPrice_dtls,2); $smry_tot_ex_factory_fob_val_inhouse_dtls+=$exfactory_unitPrice_dtls; ?></td>
					 <td width="80" align="right"><? $exfactory_unitPrice_dtls= $prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_smry_fobVal_outbound']; echo number_format($exfactory_unitPrice_dtls,2); $smry_tot_ex_factory_fob_val_outbound_dtls+=$exfactory_unitPrice_dtls; ?></td>
					 <td width="80" align="right"><? $exfactory_unitPrice_dtls= $prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_smry_fobVal']; echo number_format($exfactory_unitPrice_dtls,2); $smry_tot_ex_factory_fob_val_dtls+=$exfactory_unitPrice_dtls; ?></td>
					 
					 
					<?php /*?><td width="150" align="right"><? $ship_balance_dtls=$prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_qnty']-$prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_smry']; echo number_format($ship_balance_dtls,2); $smry_tot_ship_balance_dtls+=$ship_balance_dtls; ?></td><?php */?>
					
					
					<?php /*?><td  align="right"><? $sew_out_ship_ex_fob_balance_dtls=($swing_val_inhouse_dtls+$swing_val_outbound_dtls)-$prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_smry_fobVal']; echo number_format($sew_out_ship_ex_fob_balance_dtls,2); $smry_tot_sew_out_ship_ex_fob_balance_dtls+=$sew_out_ship_ex_fob_balance_dtls; ?></td><?php */?>
				   
				</tr>
			<?
			
		$inc_dtls++;
				}
			
		
			echo $all_po_ids_dtls;
		?>
         <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                <th width="150">Total</th>
             
                <th width="80"><? echo number_format($smry_tot_cutting_qnty_inhouse_dtls,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_cutting_qnty_outbound_dtls,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_cutting_qnty_dtls,2); ?></th>
               
               
                <th width="80"><? echo number_format($smry_tot_sewing_qnty_inhouse_dtls,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_sewing_qnty_outbound_dtls,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_sewing_qnty_dtls,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_finish_qnty_inhouse_dtls,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_finish_qnty_outbound_dtls,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_finish_qnty_dtls,2); ?></th>
                
                <th width="100"><? echo number_format($smry_tot_cm_value_in_dtls,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_sewing_fob_val_input_qnty_inhouse_dtls,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_sewing_fob_val_input_qnty_outbound_dtls,2); ?></th>
                <th width="80"><? echo number_format($total_sew_order_value_dtls,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_ex_factory_inhouse_dtls,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_outbound_dtls,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_ex_factory_val_inhouse,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_val_outbound,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_val,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_ex_factory_fob_val_inhouse_dtls,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_fob_val_outbound_dtls,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_fob_val_dtls,2); ?></th>

              <?php /*?>  <th width="150"><? echo number_format($smry_tot_ship_balance_dtls,2); ?></th>
                <th><? echo number_format($smry_tot_sew_out_ship_ex_fob_balance_dtls,2); ?></th><?php */?>
             </tr>
           </tfoot>
           <?
		   
		   }
			$smry_tot_cutting_qnty_inhouse_dtls=0;
			$smry_tot_cutting_qnty_outbound_dtls=0;
			$smry_tot_cutting_qnty_dtls=0;
			
			$smry_tot_sewing_qnty_inhouse_dtls=0;
			$smry_tot_sewing_qnty_outbound_dtls=0;
			$smry_tot_sewing_qnty_dtls=0;
			
			$smry_tot_finish_qnty_inhouse_dtls=0;
			$smry_tot_finish_qnty_outbound_dtls=0;
			$smry_tot_finish_qnty_dtls=0;
			
			$smry_tot_cm_value_in_dtls=0;
			
			$smry_tot_sewing_fob_val_input_qnty_inhouse_dtls=0;
			$smry_tot_sewing_fob_val_input_qnty_outbound_dtls=0;
			$total_sew_order_value_dtls=0;
			
			$smry_tot_ex_factory_inhouse_dtls=0;
			$smry_tot_ex_factory_outbound_dtls=0;
			$smry_tot_ex_factory=0;
			
			$smry_tot_ex_factory_val_inhouse=0;
			$smry_tot_ex_factory_val_outbound=0;
			$smry_tot_ex_factory_val=0;
			
			$smry_tot_ex_factory_fob_val_inhouse_dtls=0;
			$smry_tot_ex_factory_fob_val_outbound_dtls=0;
			$smry_tot_ex_factory_fob_val_dtls=0;
			
			$smry_tot_ship_balance_dtls=0;
			$smry_tot_sew_out_ship_ex_fob_balance_dtls=0;
			//if(count($workCom)==1){ exit;} 
			//if(count($workCom)==1){ break;}  
			    
         }
		 
		//echo $a;
		 ?>
        </table>
        </div>
         <br/>
		<?		
		
	
	
	}
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
	//$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit(); 
    
}

if($action=="report_generate_backup")// 3/9/2019
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$cbo_company=str_replace("'","",$cbo_company_id);
	//$cbo_company=0;
	$cbo_product_cat=str_replace("'","",$cbo_product_cat);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$type=str_replace("'","",$type);
	$cbo_working_company=str_replace("'","",$cbo_working_company_id);
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	$company_group_library=return_library_array( "select id,group_id from lib_company", "id", "group_id");
	$group_short_library=return_library_array( "select id,group_name from lib_group", "id", "group_name");
	$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per"); 
	$tot_cost_arr = return_library_array("select job_no, cm_cost from wo_pre_cost_dtls","job_no","cm_cost"); 
	if(str_replace("'","",$cbo_location)==0){$location_con="";}else{$location_con=" and a.location=$cbo_location";}
	//echo $cbo_working_company;
	if($cbo_company==0) $cbo_company_cond=""; else $cbo_company_cond=" and a.company_id in($cbo_company)";
	if($cbo_working_company==0) $company_working_cond=""; else $company_working_cond=" and a.knitting_company in($cbo_working_company)";

	//echo $company_working_cond;
	ob_start();	
	
	if($type==3)
	{
		
	?>
        <table width="2310px" cellpadding="0" cellspacing="0" id="caption" align="center">
            <tr>
            <td align="center" width="100%" colspan="22" class="form_caption" style="font-size:18px;"><strong>
			<? /*if($cbo_working_company==0){ echo "Company Name:". $company_library[$cbo_company];} else{ 
			$com_arr=explode(",",str_replace("'","",$cbo_working_company));
			$comName="";
			foreach($com_arr as $comID)
			{
				$comName.=$company_library[$comID].',';
			}
			//echo chop($comName,",");
			echo "Working Company Name:". chop($comName,",");}*/ 
			
			if($cbo_working_company==0){
				$comp=explode(",",$cbo_company);
				  echo "Group Name:". $group_short_library[$company_group_library[$comp[0]]];} 
			else{$wComp=explode(",",$cbo_working_company);
				  echo "Group Name:". $group_short_library[$company_group_library[$wComp[0]]];}
			?>
            
            </strong></td>
            </tr> 
             <tr>  
               <td align="center" width="100%" colspan="22" class="form_caption" style="font-size:14px;"><strong><? echo $report_title; ?></strong></td>
            </tr>
            <tr>  
               <td align="center" width="100%" colspan="22" class="form_caption" style="font-size:14px;"><strong> <? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
            </tr>  
        </table>
      
      <?
	  	if($cbo_company==0) $cbo_company_cond_1=""; else $cbo_company_cond_1="and company_name in($cbo_company)";
		//$smv_source=return_field_value("smv_source","variable_settings_production","$cbo_company_cond_1 and variable_list=25 and status_active=1 and is_deleted=0");
		$smv_source=return_field_value("smv_source","variable_settings_production","variable_list=25 $cbo_company_cond_1 and status_active=1 and is_deleted=0");
		//echo $smv_source;die;
		if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
		$item_smv_array=array();
		//$smv_source=2;
		if($smv_source==3)
		{
			$sql_item="select b.id, a.sam_style, a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$resultItem=sql_select($sql_item);
			foreach($resultItem as $itemData)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
			}
		}
		else
		{
			if($cbo_company==0) $cbo_company_cond_2=""; else $cbo_company_cond_2=" and a.company_name in($cbo_company)";
			$sql_item="select b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no $cbo_company_cond_2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$resultItem=sql_select($sql_item);
			foreach($resultItem as $itemData)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]['smv_pcs']=$itemData[csf('smv_pcs')];
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]['smv_pcs_precost']=$itemData[csf('smv_pcs_precost')];
			}
		}
		
		$tpdArr=array(); $tsmvArr=array();
		//$cbo_working_company
		if($cbo_company==0) $cbo_company_cond_3=""; else $cbo_company_cond_3=" and b.company_id in($cbo_company)";
		if($cbo_working_company==0) $cboWorkingComCondManpower=""; else $cboWorkingComCondManpower=" and b.company_id in($cbo_working_company)";
		if(str_replace("'","",$cbo_location)==0){$cboWorkingComCondLocation="";}else{$cboWorkingComCondLocation=" and b.location_id=$cbo_location";}

        $tpd_data_arr=sql_select( "select a.pr_date, sum(a.target_per_hour*a.working_hour) tpd, sum(a.man_power*a.working_hour) tsmv from prod_resource_dtls a, prod_resource_mst b where b.id=a.mst_id $cbo_company_cond_3 $cboWorkingComCondManpower $cboWorkingComCondLocation and a.pr_date between '$date_from' and '$date_to' and a.is_deleted=0 and b.is_deleted=0 group by  a.pr_date");
		
        foreach($tpd_data_arr as $row)
        {
			$production_date=date("Y-m-d", strtotime($row[csf('pr_date')])); 
           // $tpdArr[$production_date]+=$row[csf('tpd')];
			 $tsmvArr[change_date_format($production_date)]['smv']=$row[csf('tsmv')]*60;
        } //var_dump($tsmvArr[$production_date]['smv']);
		
		$job_array=array(); 
		$job_sql="select a.id, a.unit_price,b.buyer_name,b.company_name,a.po_quantity, b.job_no, b.total_set_qnty,b.set_smv from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ";
		
	//$job_sql="select a.job_no, a.total_set_qnty,b.id, b.unit_price,c.smv_pcs,c.set_item_ratio from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and a.company_name in($cbo_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		
		
		
		$job_sql_result=sql_select($job_sql);
		foreach($job_sql_result as $row)
		{
			$job_array[$row[csf("id")]]['unit_price']=$row[csf("unit_price")];
			$job_array[$row[csf("id")]]['job_no']=$row[csf("job_no")];
			$job_array[$row[csf("id")]]['total_set_qnty']=$row[csf("total_set_qnty")];
			$job_array[$row[csf("id")]]['set_smv']=$row[csf("set_smv")];
			$job_array_summary[$row[csf("company_name")]][$row[csf("buyer_name")]]['po_qty']+=$row[csf("po_quantity")];
			//$job_array[$row[csf("id")]][$row[csf("set_item_ratio")]]['smv_pcs']=$row[csf("smv_pcs")];
		}	  
	  
		  $total_knit=0;
		  $total_finishing=0;
		  $total_print=0;
		  $total_emb=0;
		  $total_cutting=0;
		  $total_cutting_inhouse=0;
		  $total_cutting_subcontract=0;
		  $total_sew=0;
		  $total_sew_inhouse=0;
		  $total_sew_subcontract=0;
		  $total_finishg=0;
		  $total_finish_inhouse=0;
		  $total_finish_subcontract=0;
		  $total_carton=0;
		 if($cbo_company==0) $cbo_company_cond_4=""; else $cbo_company_cond_4=" and a.company_id in($cbo_company)";
		 if($cbo_product_cat==0) $cbo_product_cat_cond=""; else $cbo_product_cat_cond=" and c.product_category = $cbo_product_cat";
		 if($cbo_working_company==0) $company_working_cond_1=""; else $company_working_cond_1=" and a.serving_company in($cbo_working_company)";
		 $dtls_sql="SELECT a.production_date, a.po_break_down_id as po_breakdown_id, a.item_number_id,c.buyer_name,a.company_id,a.serving_company,c.product_category,
					sum(CASE WHEN a.production_type =1 THEN a.production_quantity END) AS cutting_qnty,
					sum(CASE WHEN a.production_type =1 and a.production_source=1 THEN a.production_quantity END) AS cutting_qnty_inhouse,
					sum(CASE WHEN a.production_type =1 and a.production_source=3 THEN a.production_quantity END) AS cutting_qnty_outbound, 
					sum(CASE WHEN a.production_type =3 and a.embel_name=1 THEN a.production_quantity END) AS printing_qnty,
					sum(CASE WHEN a.production_type =3 and a.embel_name=1 and a.production_source=1 THEN a.production_quantity END) AS printing_qnty_inhouse,
					sum(CASE WHEN a.production_type =3 and a.embel_name=1 and a.production_source=3 THEN a.production_quantity END) AS printing_qnty_outbound, 
					sum(CASE WHEN a.production_type =3 and a.embel_name=2 THEN a.production_quantity END) AS emb_qnty,
					sum(CASE WHEN a.production_type =3 and a.embel_name=2 and a.production_source=1 THEN a.production_quantity END) AS emb_qnty_inhouse,
					sum(CASE WHEN a.production_type =3 and a.embel_name=2 and a.production_source=3 THEN a.production_quantity END) AS emb_qnty_outbound,
					sum(CASE WHEN a.production_type =5 THEN a.production_quantity END) AS sewing_qnty,
					sum(CASE WHEN a.production_type =5 and a.production_source=1 THEN a.production_quantity END) AS sewingout_qnty_inhouse,
					sum(CASE WHEN a.production_type =5 and a.production_source=3 THEN a.production_quantity END) AS sewingout_qnty_outbound, 
					sum(CASE WHEN a.production_type =4 THEN a.production_quantity END) AS sewing_input_qnty,
					sum(CASE WHEN a.production_type =4 and a.production_source=1 THEN a.production_quantity END) AS sewing_input_qnty_inhouse,
					sum(CASE WHEN a.production_type =4 and a.production_source=3 THEN a.production_quantity END) AS sewing_input_qnty_outbound, 
					sum(CASE WHEN a.production_type =8 THEN a.production_quantity END) AS finish_qnty,
					sum(CASE WHEN a.production_type =8 and a.production_source=1 THEN a.production_quantity END) AS finish_qnty_inhouse, 
					sum(CASE WHEN a.production_type =8 and a.production_source=3 THEN a.production_quantity END) AS finish_qnty_outbound,
					sum(CASE WHEN a.production_type =8  THEN a.carton_qty END) AS carton_qty,
					
					
					sum(CASE WHEN a.production_type =1 and c.product_category in(1,3,4,5) THEN a.production_quantity END) AS cutting_qnty_all,
					sum(CASE WHEN a.production_type =1 and c.product_category in(1,3,4,5) and a.production_source=1 THEN a.production_quantity END) AS cutting_qnty_inhouse_all,
					sum(CASE WHEN a.production_type =1 and c.product_category in(1,3,4,5) and a.production_source=3 THEN a.production_quantity END) AS cutting_qnty_outbound_all, 
					sum(CASE WHEN a.production_type =3 and c.product_category in(1,3,4,5) and a.embel_name=1 THEN a.production_quantity END) AS printing_qnty_all,
					sum(CASE WHEN a.production_type =3 and c.product_category in(1,3,4,5) and a.embel_name=1 and a.production_source=1 THEN a.production_quantity END) AS printing_qnty_inhouse_all,
					sum(CASE WHEN a.production_type =3 and c.product_category in(1,3,4,5) and a.embel_name=1 and a.production_source=3 THEN a.production_quantity END) AS printing_qnty_outbound_all, 
					sum(CASE WHEN a.production_type =3 and c.product_category in(1,3,4,5) and a.embel_name=2 THEN a.production_quantity END) AS emb_qnty_all,
					sum(CASE WHEN a.production_type =3 and c.product_category in(1,3,4,5) and a.embel_name=2 and a.production_source=1 THEN a.production_quantity END) AS emb_qnty_inhouse_all,
					sum(CASE WHEN a.production_type =3 and c.product_category in(1,3,4,5) and a.embel_name=2 and a.production_source=3 THEN a.production_quantity END) AS emb_qnty_outbound_all,
					sum(CASE WHEN a.production_type =5 and c.product_category in(1,3,4,5) THEN a.production_quantity END) AS sewing_qnty_all,
					sum(CASE WHEN a.production_type =5 and c.product_category in(1,3,4,5) and a.production_source=1 THEN a.production_quantity END) AS sewingout_qnty_inhouse_all,
					sum(CASE WHEN a.production_type =5 and c.product_category in(1,3,4,5) and a.production_source=3 THEN a.production_quantity END) AS sewingout_qnty_outbound_all, 
					sum(CASE WHEN a.production_type =4 and c.product_category in(1,3,4,5) THEN a.production_quantity END) AS sewing_input_qnty_all,
					sum(CASE WHEN a.production_type =4 and c.product_category in(1,3,4,5) and a.production_source=1 THEN a.production_quantity END) AS sewing_input_qnty_inhouse_all,
					sum(CASE WHEN a.production_type =4 and c.product_category in(1,3,4,5) and a.production_source=3 THEN a.production_quantity END) AS sewing_input_qnty_outbound_all, 
					sum(CASE WHEN a.production_type =8 and c.product_category in(1,3,4,5) THEN a.production_quantity END) AS finish_qnty_all,
					sum(CASE WHEN a.production_type =8 and c.product_category in(1,3,4,5) and a.production_source=1 THEN a.production_quantity END) AS finish_qnty_inhouse_all, 
					sum(CASE WHEN a.production_type =8 and c.product_category in(1,3,4,5) and a.production_source=3 THEN a.production_quantity END) AS finish_qnty_outbound_all,
					sum(CASE WHEN a.production_type =8 and c.product_category in(1,3,4,5) THEN a.carton_qty END) AS carton_qty_all,
					
					sum(CASE WHEN a.production_type =1 and c.product_category in(2) THEN a.production_quantity END) AS cutting_qnty_lin,
					sum(CASE WHEN a.production_type =1 and c.product_category in(2) and a.production_source=1 THEN a.production_quantity END) AS cutting_qnty_inhouse_lin,
					sum(CASE WHEN a.production_type =1 and c.product_category in(2) and a.production_source=3 THEN a.production_quantity END) AS cutting_qnty_outbound_lin, 
					sum(CASE WHEN a.production_type =3 and c.product_category in(2) and a.embel_name=1 THEN a.production_quantity END) AS printing_qnty_lin,
					sum(CASE WHEN a.production_type =3 and c.product_category in(2) and a.embel_name=1 and a.production_source=1 THEN a.production_quantity END) AS printing_qnty_inhouse_lin,
					sum(CASE WHEN a.production_type =3 and c.product_category in(2) and a.embel_name=1 and a.production_source=3 THEN a.production_quantity END) AS printing_qnty_outbound_lin, 
					sum(CASE WHEN a.production_type =3 and c.product_category in(2) and a.embel_name=2 THEN a.production_quantity END) AS emb_qnty_lin,
					sum(CASE WHEN a.production_type =3 and c.product_category in(2) and a.embel_name=2 and a.production_source=1 THEN a.production_quantity END) AS emb_qnty_inhouse_lin,
					sum(CASE WHEN a.production_type =3 and c.product_category in(2) and a.embel_name=2 and a.production_source=3 THEN a.production_quantity END) AS emb_qnty_outbound_lin,
					sum(CASE WHEN a.production_type =5 and c.product_category in(2) THEN a.production_quantity END) AS sewing_qnty_lin,
					sum(CASE WHEN a.production_type =5 and c.product_category in(2) and a.production_source=1 THEN a.production_quantity END) AS sewingout_qnty_inhouse_lin,
					sum(CASE WHEN a.production_type =5 and c.product_category in(2) and a.production_source=3 THEN a.production_quantity END) AS sewingout_qnty_outbound_lin, 
					sum(CASE WHEN a.production_type =4 and c.product_category in(2) THEN a.production_quantity END) AS sewing_input_qnty_lin,
					sum(CASE WHEN a.production_type =4 and c.product_category in(2) and a.production_source=1 THEN a.production_quantity END) AS sewing_input_qnty_inhouse_lin,
					sum(CASE WHEN a.production_type =4 and c.product_category in(2) and a.production_source=3 THEN a.production_quantity END) AS sewing_input_qnty_outbound_lin, 
					sum(CASE WHEN a.production_type =8 and c.product_category in(2) THEN a.production_quantity END) AS finish_qnty_lin,
					sum(CASE WHEN a.production_type =8 and c.product_category in(2) and a.production_source=1 THEN a.production_quantity END) AS finish_qnty_inhouse_lin, 
					sum(CASE WHEN a.production_type =8 and c.product_category in(2) and a.production_source=3 THEN a.production_quantity END) AS finish_qnty_outbound_lin,
					sum(CASE WHEN a.production_type =8 and c.product_category in(2) THEN a.carton_qty END) AS carton_qty_lin
			
					 
					from pro_garments_production_mst a, wo_po_break_down b,wo_po_details_master c 
					where a.po_break_down_id=b.id and c.job_no=b.job_no_mst  $location_con $cbo_company_cond_4 $company_working_cond_1 $cbo_product_cat_cond and a.production_date between '$date_from' and '$date_to' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.production_date, a.po_break_down_id, a.item_number_id,c.buyer_name,a.company_id,a.serving_company,c.product_category  order by a.production_date asc";
					
			//and a.production_date between '$date_from' and '$date_to'
			//and b.id in(29714,29715) 
			 //echo $dtls_sql;
			 $dtls_sql_result=sql_select($dtls_sql);
			 $prod_date=array();$po_id=""; $po_sewing_qty=array();  $prod_date_buyer_wise_summary=array();
			 foreach($dtls_sql_result as $row)
			 {
				 //if($po_id=="")$po_id=$row[csf("po_breakdown_id")]; else $po_id=$po_id.",".$row[csf("po_breakdown_id")];
				 
				 $production_date=date("Y-m-d", strtotime($row[csf('production_date')])); 
				 $prod_date[change_date_format($row[csf("production_date")])]['po_breakdown_id'].=$row[csf("po_breakdown_id")].",";
				 $prod_date[change_date_format($row[csf("production_date")])]['production_date']=$row[csf("production_date")];
				 $prod_date[change_date_format($row[csf("production_date")])]['printing_qnty']+=$row[csf("printing_qnty")];
				 $prod_date[change_date_format($row[csf("production_date")])]['emb_qnty']+=$row[csf("emb_qnty")];
				 $prod_date[change_date_format($row[csf("production_date")])]['cutting_qnty_inhouse']+=$row[csf("cutting_qnty_inhouse")];
				 $prod_date[change_date_format($row[csf("production_date")])]['cutting_qnty_outbound']+=$row[csf("cutting_qnty_outbound")];
				 $prod_date[change_date_format($row[csf("production_date")])]['cutting_qnty']+=$row[csf("cutting_qnty")];
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_qnty_inhouse']+=$row[csf("sewingout_qnty_inhouse")];
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_qnty_outbound']+=$row[csf("sewingout_qnty_outbound")];
				 $prod_date[change_date_format($row[csf("production_date")])]['sewing_qnty']+=$row[csf("sewing_qnty")];
				 
				 
				 //array for summary part
				 $prod_date_buyer_wise_smry[$row[csf("buyer_name")]]['cutting_qnty_inhouse']+=$row[csf("cutting_qnty_inhouse")];
				 $prod_date_buyer_wise_smry[$row[csf("buyer_name")]]['cutting_qnty_outbound']+=$row[csf("cutting_qnty_outbound")];
				 $prod_date_buyer_wise_smry[$row[csf("buyer_name")]]['cutting_qnty']+=$row[csf("cutting_qnty")];
				 $prod_date_buyer_wise_smry[$row[csf("buyer_name")]]['sewingout_qnty_inhouse']+=$row[csf("sewingout_qnty_inhouse")];
				 $prod_date_buyer_wise_smry[$row[csf("buyer_name")]]['sewingout_qnty_outbound']+=$row[csf("sewingout_qnty_outbound")];
				 $prod_date_buyer_wise_smry[$row[csf("buyer_name")]]['sewing_input_qnty']+=$row[csf("sewing_input_qnty")];
				 $prod_date_buyer_wise_smry[$row[csf("buyer_name")]]['sewing_qnty']+=$row[csf("sewing_qnty")]; //sew output
				 //$prod_date_buyer_wise_smry[$row[csf("buyer_name")]]['sewing_qnty']+=$row[csf("sewing_qnty")]; //sew output
				 $prod_date_buyer_wise_smry[$row[csf("buyer_name")]]['finish_qnty']+=$row[csf("finish_qnty")];
				 $prod_date_buyer_wise_smry[$row[csf("buyer_name")]]['buyer_name']=$row[csf("buyer_name")];
				 
				 
				 // array for Buyer wise summary part
				// $prod_date_buyer_wise[$row[csf("company_id")]][$row[csf("buyer_name")]]['po_quantity']+=$row[csf("po_quantity")];
				
				$working_company_arr[$row[csf("company_id")]]['company_id']=$row[csf("company_id")];
				 $prod_date_buyer_wise[$row[csf("serving_company")]][$row[csf("buyer_name")]]['cutting_qnty_inhouse']+=$row[csf("cutting_qnty_inhouse")];
				 $prod_date_buyer_wise[$row[csf("serving_company")]][$row[csf("buyer_name")]]['cutting_qnty_outbound']+=$row[csf("cutting_qnty_outbound")];
				 $prod_date_buyer_wise[$row[csf("serving_company")]][$row[csf("buyer_name")]]['cutting_qnty']+=$row[csf("cutting_qnty")];
				 $prod_date_buyer_wise[$row[csf("serving_company")]][$row[csf("buyer_name")]]['sewingout_qnty_inhouse']+=$row[csf("sewingout_qnty_inhouse")];
				 $prod_date_buyer_wise[$row[csf("serving_company")]][$row[csf("buyer_name")]]['sewingout_qnty_outbound']+=$row[csf("sewingout_qnty_outbound")];
				 $prod_date_buyer_wise[$row[csf("serving_company")]][$row[csf("buyer_name")]]['sewing_input_qnty']+=$row[csf("sewing_input_qnty")];
				 $prod_date_buyer_wise[$row[csf("serving_company")]][$row[csf("buyer_name")]]['sewing_qnty']+=$row[csf("sewing_qnty")]; //sew output
				 //$prod_date_buyer_wise[$row[csf("company_id")]][$row[csf("buyer_name")]]['sewing_qnty']+=$row[csf("sewing_qnty")]; //sew output
				 $prod_date_buyer_wise[$row[csf("serving_company")]][$row[csf("buyer_name")]]['finish_qnty']+=$row[csf("finish_qnty")];
				 $prod_date_buyer_wise[$row[csf("serving_company")]][$row[csf("buyer_name")]]['buyer_name']=$row[csf("buyer_name")];

				 
				 $prod_date_buyer_wise_all[$row[csf("buyer_name")]]['cutting_qnty_inhouse_all']+=$row[csf("cutting_qnty_inhouse_all")];
				 $prod_date_buyer_wise_all[$row[csf("buyer_name")]]['cutting_qnty_outbound_all']+=$row[csf("cutting_qnty_outbound_all")];
				 $prod_date_buyer_wise_all[$row[csf("buyer_name")]]['cutting_qnty_all']+=$row[csf("cutting_qnty_all")];
				 $prod_date_buyer_wise_all[$row[csf("buyer_name")]]['sewingout_qnty_inhouse_all']+=$row[csf("sewingout_qnty_inhouse_all")];
				 $prod_date_buyer_wise_all[$row[csf("buyer_name")]]['sewingout_qnty_outbound_all']+=$row[csf("sewingout_qnty_outbound_all")];
				 $prod_date_buyer_wise_all[$row[csf("buyer_name")]]['sewing_input_qnty_all']+=$row[csf("sewing_input_qnty_all")];
				 $prod_date_buyer_wise_all[$row[csf("buyer_name")]]['sewing_qnty_all']+=$row[csf("sewing_qnty_all")]; //sew output
				 $prod_date_buyer_wise_all[$row[csf("buyer_name")]]['finish_qnty_all']+=$row[csf("finish_qnty_all")];
				 $prod_date_buyer_wise_all[$row[csf("buyer_name")]]['buyer_name']=$row[csf("buyer_name")];
				 
				 $prod_date_buyer_wise_lin[$row[csf("buyer_name")]]['cutting_qnty_inhouse_lin']+=$row[csf("cutting_qnty_inhouse_lin")];
				 $prod_date_buyer_wise_lin[$row[csf("buyer_name")]]['cutting_qnty_outbound_lin']+=$row[csf("cutting_qnty_outbound_lin")];
				 $prod_date_buyer_wise_lin[$row[csf("buyer_name")]]['cutting_qnty_lin']+=$row[csf("cutting_qnty_lin")];
				 $prod_date_buyer_wise_lin[$row[csf("buyer_name")]]['sewingout_qnty_inhouse_lin']+=$row[csf("sewingout_qnty_inhouse_lin")];
				 $prod_date_buyer_wise_lin[$row[csf("buyer_name")]]['sewingout_qnty_outbound_lin']+=$row[csf("sewingout_qnty_outbound_lin")];
				 $prod_date_buyer_wise_lin[$row[csf("buyer_name")]]['sewing_input_qnty_lin']+=$row[csf("sewing_input_qnty_lin")];
				 $prod_date_buyer_wise_lin[$row[csf("buyer_name")]]['sewing_qnty_lin']+=$row[csf("sewing_qnty_lin")]; //sew output
				 $prod_date_buyer_wise_lin[$row[csf("buyer_name")]]['finish_qnty_lin']+=$row[csf("finish_qnty_lin")];
				 $prod_date_buyer_wise_lin[$row[csf("buyer_name")]]['buyer_name']=$row[csf("buyer_name")];
				
				 /*
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_qnty_outbound_pcs']+=($row[csf("sewingout_qnty_outbound")]*$job_array[$row[csf("po_breakdown_id")]]['set_smv']);
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_qnty_inhouse_pcs']+=($row[csf("sewingout_qnty_inhouse")]*$job_array[$row[csf("po_breakdown_id")]]['set_smv']);
				 */
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_qnty_outbound_pcs']+=($row[csf("sewingout_qnty_outbound")]*$item_smv_array[$row[csf("po_breakdown_id")]][$row[csf("item_number_id")]]['smv_pcs']);
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_qnty_inhouse_pcs']+=($row[csf("sewingout_qnty_inhouse")]*$item_smv_array[$row[csf("po_breakdown_id")]][$row[csf("item_number_id")]]['smv_pcs']);
				 
				 $item_smv=0;
				if($smv_source==2)
				{
					$item_smv=$item_smv_array[$row[csf("po_breakdown_id")]][$row[csf("item_number_id")]]['smv_pcs_precost'];
				}
				else if($smv_source==3)
				{
					$item_smv=$item_smv_array[$row[csf("po_breakdown_id")]][$row[csf("item_number_id")]];	
				}
				else
				{
					$item_smv=$item_smv_array[$row[csf("po_breakdown_id")]][$row[csf("item_number_id")]]['smv_pcs'];	
				}
				 
				 $prod_date[change_date_format($row[csf("production_date")])]['sewing_qnty_smv']+=$row[csf("sewing_qnty")]*$item_smv;
				 
				 $prod_date[change_date_format($row[csf("production_date")])]['finish_qnty_inhouse']+=$row[csf("finish_qnty_inhouse")];
				 $prod_date[change_date_format($row[csf("production_date")])]['finish_qnty_outbound']+=$row[csf("finish_qnty_outbound")];
				 
				 //for summary part
				 $prod_date_buyer_wise_smry[$row[csf("buyer_name")]]['finish_qnty_inhouse']+=$row[csf("finish_qnty_inhouse")];
				 $prod_date_buyer_wise_smry[$row[csf("buyer_name")]]['finish_qnty_outbound']+=$row[csf("finish_qnty_outbound")]; 
				 //end
				 
				 
				 $prod_date_buyer_wise[$row[csf("serving_company")]][$row[csf("buyer_name")]]['finish_qnty_inhouse']+=$row[csf("finish_qnty_inhouse")];
				 $prod_date_buyer_wise[$row[csf("serving_company")]][$row[csf("buyer_name")]]['finish_qnty_outbound']+=$row[csf("finish_qnty_outbound")]; 
				 
				 $prod_date_buyer_wise_all[$row[csf("buyer_name")]]['finish_qnty_inhouse_all']+=$row[csf("finish_qnty_inhouse_all")];
				 $prod_date_buyer_wise_all[$row[csf("buyer_name")]]['finish_qnty_outbound_all']+=$row[csf("finish_qnty_outbound_all")]; 
				 
				 $prod_date_buyer_wise_lin[$row[csf("buyer_name")]]['finish_qnty_inhouse_lin']+=$row[csf("finish_qnty_inhouse_lin")];
				 $prod_date_buyer_wise_lin[$row[csf("buyer_name")]]['finish_qnty_outbound_lin']+=$row[csf("finish_qnty_outbound_lin")]; 
				 
				 
				 
				 $prod_date[change_date_format($row[csf("production_date")])]['finish_qnty']+=$row[csf("finish_qnty")];
				 $prod_date[change_date_format($row[csf("production_date")])]['carton_qty']+=$row[csf("carton_qty")];
				 
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_value_inhouse']+=$row[csf("sewingout_qnty_inhouse")]*($job_array[$row[csf("po_breakdown_id")]]['unit_price']/$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty']);
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_value_outbound']+=$row[csf("sewingout_qnty_outbound")]*($job_array[$row[csf("po_breakdown_id")]]['unit_price']/$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty']);
				  //$prod_date_buyer_wise[$row[csf("company_id")]][$row[csf("buyer_name")]]['sewing_fob_val_input_qnty']+=$row[csf("sewingout_qnty_inhouse")]*$job_array[$row[csf("po_breakdown_id")]]['unit_price'];
				  $prod_date_buyer_wise[$row[csf("serving_company")]][$row[csf("buyer_name")]]['sewingUnitprice']=($job_array[$row[csf("po_breakdown_id")]]['unit_price']/$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty']);
				  
				  //for summary part
				   $prod_date_buyer_wise_smry[$row[csf("buyer_name")]]['sewingout_value_inhouse']+=$row[csf("sewingout_qnty_inhouse")]*($job_array[$row[csf("po_breakdown_id")]]['unit_price']/$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty']);
				   $prod_date_buyer_wise_smry[$row[csf("buyer_name")]]['sewingout_value_outbound']+=$row[csf("sewingout_qnty_outbound")]*($job_array[$row[csf("po_breakdown_id")]]['unit_price']/$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty']);
				  //end
				  
				  $prod_date_buyer_wise[$row[csf("serving_company")]][$row[csf("buyer_name")]]['sewingout_value_inhouse']+=$row[csf("sewingout_qnty_inhouse")]*($job_array[$row[csf("po_breakdown_id")]]['unit_price']/$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty']);
				  $prod_date_buyer_wise[$row[csf("serving_company")]][$row[csf("buyer_name")]]['sewingout_value_outbound']+=$row[csf("sewingout_qnty_outbound")]*($job_array[$row[csf("po_breakdown_id")]]['unit_price']/$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty']);
				  
				  
				   $prod_date_buyer_wise_all[$row[csf("buyer_name")]]['sewingout_value_inhouse_all']+=$row[csf("sewingout_qnty_inhouse_all")]*($job_array[$row[csf("po_breakdown_id")]]['unit_price']/$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty']);
				  $prod_date_buyer_wise_all[$row[csf("buyer_name")]]['sewingout_value_outbound_all']+=$row[csf("sewingout_qnty_outbound_all")]*($job_array[$row[csf("po_breakdown_id")]]['unit_price']/$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty']);
				  
				    $prod_date_buyer_wise_lin[$row[csf("buyer_name")]]['sewingout_value_inhouse_lin']+=$row[csf("sewingout_qnty_inhouse_lin")]*($job_array[$row[csf("po_breakdown_id")]]['unit_price']/$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty']);
				  $prod_date_buyer_wise_lin[$row[csf("buyer_name")]]['sewingout_value_outbound_lin']+=$row[csf("sewingout_qnty_outbound_lin")]*($job_array[$row[csf("po_breakdown_id")]]['unit_price']/$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty']);
				 
				 if($row[csf("product_category")]==1 || $row[csf("product_category")]==3 || $row[csf("product_category")]==4 || $row[csf("product_category")]==5)
				 {
					$cm_value_all=0; $cm_value_in_all=0; $cm_value_out_all=0; $sewing_qty_in_all=0; $sewing_qty_out_all=0;
				 		//$sewing_qnty=$row[csf("sewing_qnty")];
					 $sewing_qty_in_all=$row[csf("sewingout_qnty_inhouse_all")];
					 $sewing_qty_out_all=$row[csf("sewingout_qnty_outbound_all")];
					 
					 $job_no_all=$job_array[$row[csf("po_breakdown_id")]]['job_no'];
					 $total_set_qnty_all=$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty'];
					 $costing_per_all=$costing_per_arr[$job_no];
					 
					 if($costing_per_all==1) $dzn_qnty_all=12;
					 else if($costing_per_all==3) $dzn_qnty_all=12*2;
					 else if($costing_per_all==4) $dzn_qnty_all=12*3;
					 else if($costing_per_all==5) $dzn_qnty_all=12*4;
					 else $dzn_qnty_all=1;
								
					 $dzn_qnty_all=$dzn_qnty_all*$total_set_qnty_all;
					 //$cm_value=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewing_qnty;
					 $cm_value_in_all=($tot_cost_arr[$job_no_all]/$dzn_qnty_all)*$sewing_qty_in_all;
					 $cm_value_out_all=($tot_cost_arr[$job_no_all]/$dzn_qnty_all)*$sewing_qty_out_all;
					
					 //$prod_date[change_date_format($row[csf("production_date")])]['cm_value']+=$cm_value;
					 $prod_date[change_date_format($row[csf("production_date")])]['cm_value_in_all']+=$cm_value_in_all;
					 $prod_date[change_date_format($row[csf("production_date")])]['cm_value_out_all']+=$cm_value_out_all;
					 $prod_date_buyer_wise_all[$row[csf("buyer_name")]]['cm_value_in_all']+=$cm_value_in_all;
				 }
				 
				 else if($row[csf("product_category")]==2)
				 {
					 $cm_value_lin=0; $cm_value_in_lin=0; $cm_value_out_lin=0; $sewing_qty_in_lin=0; $sewing_qty_out_lin=0;
				 		//$sewing_qnty=$row[csf("sewing_qnty")];
					 $sewing_qty_in_lin=$row[csf("sewingout_qnty_inhouse_lin")];
					 $sewing_qty_out_lin=$row[csf("sewingout_qnty_outbound_lin")];
					 
					 $job_no_lin=$job_array[$row[csf("po_breakdown_id")]]['job_no'];
					 $total_set_qnty_lin=$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty'];
					 $costing_per_lin=$costing_per_arr[$job_no];
					 
					 if($costing_per_lin==1) $dzn_qnty_lin=12;
					 else if($costing_per_lin==3) $dzn_qnty_lin=12*2;
					 else if($costing_per_lin==4) $dzn_qnty_lin=12*3;
					 else if($costing_per_lin==5) $dzn_qnty_lin=12*4;
					 else $dzn_qnty_lin=1;
								
					 $dzn_qnty_lin=$dzn_qnty_lin*$total_set_qnty_lin;
					 //$cm_value=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewing_qnty;
					 $cm_value_in_lin=($tot_cost_arr[$job_no_lin]/$dzn_qnty_lin)*$sewing_qty_in_lin;
					 $cm_value_out_lin=($tot_cost_arr[$job_no_lin]/$dzn_qnty_lin)*$sewing_qty_out_lin;
					
					 //$prod_date[change_date_format($row[csf("production_date")])]['cm_value']+=$cm_value;
					 $prod_date[change_date_format($row[csf("production_date")])]['cm_value_in_lin']+=$cm_value_in_lin;
					 $prod_date[change_date_format($row[csf("production_date")])]['cm_value_out_lin']+=$cm_value_out_lin;
					 $prod_date_buyer_wise_lin[$row[csf("buyer_name")]]['cm_value_in_lin']+=$cm_value_in_lin;
				 }
				 if($row[csf("product_category")]==1 || $row[csf("product_category")]==2 || $row[csf("product_category")]==3 || $row[csf("product_category")]==4 || $row[csf("product_category")]==5)
				 {
					  $cm_value=0; $cm_value_in=0; $cm_value_out=0; $sewing_qty_in=0; $sewing_qty_out=0;
				 		//$sewing_qnty=$row[csf("sewing_qnty")];
					 $sewing_qty_in=$row[csf("sewingout_qnty_inhouse")];
					 $sewing_qty_out=$row[csf("sewingout_qnty_outbound")];
					 
					 $job_no=$job_array[$row[csf("po_breakdown_id")]]['job_no'];
					 $total_set_qnty=$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty'];
					 $costing_per=$costing_per_arr[$job_no];
					 
					 if($costing_per==1) $dzn_qnty=12;
					 else if($costing_per==3) $dzn_qnty=12*2;
					 else if($costing_per==4) $dzn_qnty=12*3;
					 else if($costing_per==5) $dzn_qnty=12*4;
					 else $dzn_qnty=1;
								
					 $dzn_qnty=$dzn_qnty*$total_set_qnty;
					 //$cm_value=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewing_qnty;
					 $cm_value_in=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewing_qty_in;
					 $cm_value_out=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewing_qty_out;
					
					 //$prod_date[change_date_format($row[csf("production_date")])]['cm_value']+=$cm_value;
					 $prod_date[change_date_format($row[csf("production_date")])]['cm_value_in']+=$cm_value_in;
					 $prod_date[change_date_format($row[csf("production_date")])]['cm_value_out']+=$cm_value_out;
					 $prod_date_buyer_wise[$row[csf("serving_company")]][$row[csf("buyer_name")]]['cm_value_in']+=$cm_value_in;
					 $prod_date_buyer_wise_smry[$row[csf("buyer_name")]]['cm_value_in']+=$cm_value_in;
				 }
				
			}
			//print_r($prod_date_buyer_wise);
			//print_r($working_company_arr);
			if(str_replace("'","",$cbo_location)==0){$location_con_rcv="";}else{$location_con_rcv=" and a.location_id=$cbo_location";}
			if($cbo_company==0) $cbo_company_cond_5=""; else $cbo_company_cond_5=" and a.company_id in($cbo_company)";
			if($cbo_working_company==0) $company_working_cond_2=""; else $company_working_cond_2=" and a.knitting_company in($cbo_working_company)";
			$knited_query="select a.receive_date as production_date, sum(b.grey_receive_qnty) as kniting_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id $cbo_company_cond_5  $company_working_cond_2 $location_con_rcv and a.receive_date between '$date_from' and '$date_to' and a.entry_form=2 and a.item_category=13 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.receive_date";
			
			
			$knited_query_result=sql_select($knited_query);
			$count_knit=count($knited_query_result);
			foreach( $knited_query_result as $knit_row)
			{
				$prod_date[change_date_format($knit_row[csf("production_date")])]['kniting_qnty']=$knit_row[csf("kniting_qnty")];
			}
			//var_dump($prod_datek);
			$finish_query="select a.receive_date as production_date, sum(b.receive_qnty) as finishing_qnty from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id $cbo_company_cond_5 $company_working_cond_2 $location_con_rcv and a.receive_date between '$date_from' and '$date_to' and a.entry_form=7 and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.receive_date";
			//echo $finish_query;
			$finish_query_result=sql_select($finish_query);
			$count_finish=count($finish_query_result);
			foreach( $finish_query_result as $finish_row)
			{
				$prod_date[change_date_format($finish_row[csf("production_date")])]['finishing_qnty']=$finish_row[csf("finishing_qnty")];
			}
			//var_dump($prod_date);
			
			if($cbo_company==0) $cbo_company_cond_6=""; else $cbo_company_cond_6=" and c.company_name in($cbo_company)";
			
			$wc= explode(",",str_replace("'", "",$cbo_working_company_id));
			$multiWc="";
			foreach($wc as $row){
				$multiWc.="'".$row."'".',';
			}
			$multiWorkingComp= chop($multiWc,",");
			if( str_replace("'", "",$cbo_working_company_id)==0) $cbo_delivery_com_cond=""; else $cbo_delivery_com_cond=" and d.delivery_company_id in($multiWorkingComp)";	
			
			
			if( str_replace("'", "",$cbo_working_company_id)>0 &&  str_replace("'", "",$cbo_location)>0 )
			{
				
				$delv_location_con=" and d.delivery_location_id=$cbo_location ";
				$location_con="";
			}
			else
			{
				$delv_location_con="";
				
			}

			$exfactory_res = sql_select("SELECT a.ex_factory_date, a.po_break_down_id,c.company_name,c.buyer_name,c.product_category,d.delivery_company_id as serving_company,  
			 
			sum(case when a.entry_form!=85 then a.ex_factory_qnty else 0 end)-sum(case when a.entry_form=85 then a.ex_factory_qnty else 0 end) as ex_factory_qnty, 
			sum(case when a.entry_form!=85 and d.source=1 and (d.delivery_company_id=0 OR d.delivery_company_id=c.company_name)  then a.ex_factory_qnty else 0 end)-sum(case when a.entry_form=85 and (d.delivery_company_id=0 OR d.delivery_company_id=c.company_name) then a.ex_factory_qnty else 0 end) as ex_factory_qnty_inhouse,
			
			
			sum(case when a.entry_form!=85  and d.source=3 and d.delivery_company_id!=0 and d.delivery_company_id!=c.company_name then a.ex_factory_qnty else 0 end)-sum(case when a.entry_form=85 and d.delivery_company_id!=0 and d.delivery_company_id!=c.company_name then a.ex_factory_qnty else 0 end) as ex_factory_qnty_outbound, 
			
			sum(case when a.entry_form!=85 and c.product_category in(1,3,4,5) then a.ex_factory_qnty else 0 end)-sum(case when a.entry_form=85 then a.ex_factory_qnty else 0 end) as ex_factory_qnty_all, 
			sum(case when a.entry_form!=85  and d.source=1  and (d.delivery_company_id=0 OR d.delivery_company_id=c.company_name) and c.product_category in(1,3,4,5) then a.ex_factory_qnty else 0 end)-sum(case when a.entry_form=85 and (d.delivery_company_id=0 OR d.delivery_company_id=c.company_name) then a.ex_factory_qnty else 0 end) as ex_factory_qnty_inhouse_all,
			sum(case when a.entry_form!=85  and d.source=3 and c.product_category in(1,3,4,5) and d.delivery_company_id!=0 and d.delivery_company_id!=c.company_name then a.ex_factory_qnty else 0 end)-sum(case when a.entry_form=85 and d.delivery_company_id!=0 and d.delivery_company_id!=c.company_name then a.ex_factory_qnty else 0 end) as ex_factory_qnty_outbound_all,
			
			sum(case when a.entry_form!=85  and d.source=1  and c.product_category in(2) then a.ex_factory_qnty else 0 end)-sum(case when a.entry_form=85 then a.ex_factory_qnty else 0 end) as ex_factory_qnty_lin, 
			sum(case when a.entry_form!=85 and c.product_category in(2)  and (d.delivery_company_id=0 OR d.delivery_company_id=c.company_name) then a.ex_factory_qnty else 0 end)-sum(case when a.entry_form=85  and (d.delivery_company_id=0 OR d.delivery_company_id=c.company_name) then a.ex_factory_qnty else 0 end) as ex_factory_qnty_inhouse_lin,
			sum(case when a.entry_form!=85  and d.source=3 and c.product_category in(2)  and d.delivery_company_id!=0 and d.delivery_company_id!=c.company_name then a.ex_factory_qnty else 0 end)-sum(case when a.entry_form=85 and d.delivery_company_id!=0 and d.delivery_company_id!=c.company_name then a.ex_factory_qnty else 0 end) as ex_factory_qnty_outbound_lin  
			 
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c,pro_ex_factory_delivery_mst d
			
			where a.delivery_mst_id=d.id and  a.po_break_down_id=b.id and b.job_no_mst=c.job_no $cbo_company_cond_6 $cbo_delivery_com_cond $location_con $delv_location_con $cbo_product_cat_cond and a.is_deleted=0 and a.status_active=1 and a.ex_factory_date between '$date_from' and '$date_to' group by a.ex_factory_date, a.po_break_down_id,c.company_name,c.buyer_name,c.product_category,d.delivery_company_id");
			
			
			
			/*echo "SELECT a.ex_factory_date, a.po_break_down_id,c.company_name,c.buyer_name,   
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c,pro_ex_factory_delivery_mst d
			where a.delivery_mst_id=d.id and  a.po_break_down_id=b.id and b.job_no_mst=c.job_no $cbo_company_cond_6 $cbo_delivery_com_cond $location_con $delv_location_con and a.is_deleted=0 and a.status_active=1 and a.ex_factory_date between '$date_from' and '$date_to' group by a.ex_factory_date, a.po_break_down_id,c.company_name,c.buyer_name";*/
			
			foreach($exfactory_res as $ex_row)
			{
				
				 if($ex_row[csf("product_category")]==1 || $ex_row[csf("product_category")]==3 || $ex_row[csf("product_category")]==4 || $ex_row[csf("product_category")]==5)
				 {
					//for regular order part
					$prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['ex_factory_all']+=$ex_row[csf("ex_factory_qnty_all")];
					$prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['ex_factory_qnty_inhouse_all']+=$ex_row[csf("ex_factory_qnty_inhouse_all")];
					$prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['ex_factory_qnty_outbound_all']+=$ex_row[csf("ex_factory_qnty_outbound_all")];				
					
					$prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['ex_factory_val_all']+=$ex_row[csf("ex_factory_qnty_all")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
	
	  				
					 $ex_cm_value_in_all=0; $ex_cm_value_inhouse_all=0; $ex_cm_value_outbound_all=0; $ex_sewing_qty_in_all=0; $ex_sewing_qty_inhouse_all=0; $ex_sewing_qty_outbound_all=0;
					 
					 $ex_sewing_qty_in_all=$ex_row[csf("ex_factory_qnty_all")];
					 $ex_sewing_qty_inhouse_all=$ex_row[csf("ex_factory_qnty_inhouse_all")];
					 $ex_sewing_qty_outbound_all=$ex_row[csf("ex_factory_qnty_outbound_all")];
					 
					 $job_no_ex_all=$job_array[$ex_row[csf("po_break_down_id")]]['job_no'];
					 $total_ex_set_qnty_all=$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty'];
					 $costing_per_ex_all=$costing_per_arr[$job_no_ex_all];
					 
					 if($costing_per_ex_all==1) $dzn_qnty_ex_all=12;
					 else if($costing_per_ex_all==3) $dzn_qnty_ex_all=12*2;
					 else if($costing_per_ex_all==4) $dzn_qnty_ex_all=12*3;
					 else if($costing_per_ex_all==5) $dzn_qdzn_qnty_exnty_all=12*4;
					 else $dzn_qnty_ex_all=1;
								
					 $dzn_qnty_ex_all=$dzn_qnty_ex_all*$total_ex_set_qnty_all;
					 $ex_cm_value_in_all=($tot_cost_arr[$job_no_ex_all]/$dzn_qnty_ex_all)*$ex_sewing_qty_in_all;
					 $ex_cm_value_inhouse_all=($tot_cost_arr[$job_no_ex_all]/$dzn_qnty_ex_all)*$ex_sewing_qty_inhouse_all;
					 $ex_cm_value_outbound_all=($tot_cost_arr[$job_no_ex_all]/$dzn_qnty_ex_all)*$ex_sewing_qty_outbound_all;
					 
					 $prod_date_buyer_wise_all[$ex_row[csf("buyer_name")]]['ex_cm_value_in_all']+=$ex_cm_value_in_all;
					 $prod_date_buyer_wise_all[$ex_row[csf("buyer_name")]]['ex_cm_value_inhouse_all']+=$ex_cm_value_inhouse_all;
					 $prod_date_buyer_wise_all[$ex_row[csf("buyer_name")]]['ex_cm_value_outbound_all']+=$ex_cm_value_outbound_all;
					 $prod_date_buyer_wise_all[$ex_row[csf("buyer_name")]]['ex_factory_smry_all']+=$ex_row[csf("ex_factory_qnty_all")];
					 $prod_date_buyer_wise_all[$ex_row[csf("buyer_name")]]['ex_factory_qnty_inhouse_all']+=$ex_row[csf("ex_factory_qnty_inhouse_all")];
					 $prod_date_buyer_wise_all[$ex_row[csf("buyer_name")]]['ex_factory_qnty_outbound_all']+=$ex_row[csf("ex_factory_qnty_outbound_all")];	
					 $prod_date_buyer_wise_all[$ex_row[csf("buyer_name")]]['ex_factory_smry_unitPrice_all']=($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
					$prod_date_buyer_wise_all[$ex_row[csf("buyer_name")]]['ex_factory_smry_fobVal_inhouse_all']+=$ex_row[csf("ex_factory_qnty_inhouse_all")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
					$prod_date_buyer_wise_all[$ex_row[csf("buyer_name")]]['ex_factory_smry_fobVal_outbound_all']+=$ex_row[csf("ex_factory_qnty_outbound_all")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
					$prod_date_buyer_wise_all[$ex_row[csf("buyer_name")]]['ex_factory_smry_fobVal_all']+=$ex_row[csf("ex_factory_qnty_all")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
					
				}
				else  if($ex_row[csf("product_category")]==2)
				{
					//for UG order part
					$prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['ex_factory_lin']+=$ex_row[csf("ex_factory_qnty_lin")];
					$prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['ex_factory_qnty_inhouse_lin']+=$ex_row[csf("ex_factory_qnty_inhouse_lin")];
					$prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['ex_factory_qnty_outbound_lin']+=$ex_row[csf("ex_factory_qnty_outbound_lin")];				
					
					$prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['ex_factory_val_lin']+=$ex_row[csf("ex_factory_qnty_lin")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
	
	  				
					 $ex_cm_value_in_lin=0; $ex_cm_value_inhouse_lin=0; $ex_cm_value_outbound_lin=0; $ex_sewing_qty_in_lin=0; $ex_sewing_qty_inhouse_lin=0; $ex_sewing_qty_outbound_lin=0;
					 
					 $ex_sewing_qty_in_lin=$ex_row[csf("ex_factory_qnty_lin")];
					 $ex_sewing_qty_inhouse_lin=$ex_row[csf("ex_factory_qnty_inhouse_lin")];
					 $ex_sewing_qty_outbound_lin=$ex_row[csf("ex_factory_qnty_outbound_lin")];
					 
					 $job_no_ex_lin=$job_array[$ex_row[csf("po_break_down_id")]]['job_no'];
					 $total_ex_set_qnty_lin=$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty'];
					 $costing_per_ex_lin=$costing_per_arr[$job_no_ex_lin];
					 
					 if($costing_per_ex_lin==1) $dzn_qnty_ex_lin=12;
					 else if($costing_per_ex_lin==3) $dzn_qnty_ex_lin=12*2;
					 else if($costing_per_ex_lin==4) $dzn_qnty_ex_lin=12*3;
					 else if($costing_per_ex_lin==5) $dzn_qdzn_qnty_exnty_lin=12*4;
					 else $dzn_qnty_ex_lin=1;
								
					 $dzn_qnty_ex_lin=$dzn_qnty_ex_lin*$total_ex_set_qnty_lin;
					 $ex_cm_value_in_lin=($tot_cost_arr[$job_no_ex_lin]/$dzn_qnty_ex_lin)*$ex_sewing_qty_in_lin;
					 $ex_cm_value_inhouse_lin=($tot_cost_arr[$job_no_ex_lin]/$dzn_qnty_ex_lin)*$ex_sewing_qty_inhouse_lin;
					 $ex_cm_value_outbound_lin=($tot_cost_arr[$job_no_ex_lin]/$dzn_qnty_ex_lin)*$ex_sewing_qty_outbound_lin;
					 
					 $prod_date_buyer_wise_lin[$ex_row[csf("buyer_name")]]['ex_cm_value_in_lin']+=$ex_cm_value_in_lin;
					 $prod_date_buyer_wise_lin[$ex_row[csf("buyer_name")]]['ex_cm_value_inhouse_lin']+=$ex_cm_value_inhouse_lin;
					 $prod_date_buyer_wise_lin[$ex_row[csf("buyer_name")]]['ex_cm_value_outbound_lin']+=$ex_cm_value_outbound_lin;
					 $prod_date_buyer_wise_lin[$ex_row[csf("buyer_name")]]['ex_factory_smry_lin']+=$ex_row[csf("ex_factory_qnty_lin")];
					 $prod_date_buyer_wise_lin[$ex_row[csf("buyer_name")]]['ex_factory_qnty_inhouse_lin']+=$ex_row[csf("ex_factory_qnty_inhouse_lin")];
					 $prod_date_buyer_wise_lin[$ex_row[csf("buyer_name")]]['ex_factory_qnty_outbound_lin']+=$ex_row[csf("ex_factory_qnty_outbound_lin")];	
					 $prod_date_buyer_wise_lin[$ex_row[csf("buyer_name")]]['ex_factory_smry_unitPrice_lin']=($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
					$prod_date_buyer_wise_lin[$ex_row[csf("buyer_name")]]['ex_factory_smry_fobVal_inhouse_lin']+=$ex_row[csf("ex_factory_qnty_inhouse_lin")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
					$prod_date_buyer_wise_lin[$ex_row[csf("buyer_name")]]['ex_factory_smry_fobVal_outbound_lin']+=$ex_row[csf("ex_factory_qnty_outbound_lin")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
					$prod_date_buyer_wise_lin[$ex_row[csf("buyer_name")]]['ex_factory_smry_fobVal_lin']+=$ex_row[csf("ex_factory_qnty_lin")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
				}
				if($ex_row[csf("product_category")]==1 || $ex_row[csf("product_category")]==2 || $ex_row[csf("product_category")]==3 || $ex_row[csf("product_category")]==4 || $ex_row[csf("product_category")]==5)
				{
					$prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['ex_factory']+=$ex_row[csf("ex_factory_qnty")];
					$prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['ex_factory_qnty_inhouse']+=$ex_row[csf("ex_factory_qnty_inhouse")];
					$prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['ex_factory_qnty_outbound']+=$ex_row[csf("ex_factory_qnty_outbound")];				
					
					$prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['ex_factory_val']+=$ex_row[csf("ex_factory_qnty")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
	
	  				//for summery part
					 $ex_cm_value_in=0; $ex_cm_value_inhouse=0; $ex_cm_value_outbound=0; $ex_sewing_qty_in=0; $ex_sewing_qty_inhouse=0; $ex_sewing_qty_outbound=0;
					 
					 $ex_sewing_qty_in=$ex_row[csf("ex_factory_qnty")];
					 $ex_sewing_qty_inhouse=$ex_row[csf("ex_factory_qnty_inhouse")];
					 $ex_sewing_qty_outbound=$ex_row[csf("ex_factory_qnty_outbound")];
					 
					 $job_no_ex=$job_array[$ex_row[csf("po_break_down_id")]]['job_no'];
					 $total_ex_set_qnty=$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty'];
					 $costing_per_ex=$costing_per_arr[$job_no_ex];
					 
					 if($costing_per_ex==1) $dzn_qnty_ex=12;
					 else if($costing_per_ex==3) $dzn_qnty_ex=12*2;
					 else if($costing_per_ex==4) $dzn_qnty_ex=12*3;
					 else if($costing_per_ex==5) $dzn_qdzn_qnty_exnty=12*4;
					 else $dzn_qnty_ex=1;
								
					 $dzn_qnty_ex=$dzn_qnty_ex*$total_ex_set_qnty;
					 $ex_cm_value_in=($tot_cost_arr[$job_no_ex]/$dzn_qnty_ex)*$ex_sewing_qty_in;
					 $ex_cm_value_inhouse=($tot_cost_arr[$job_no_ex]/$dzn_qnty_ex)*$ex_sewing_qty_inhouse;
					 $ex_cm_value_outbound=($tot_cost_arr[$job_no_ex]/$dzn_qnty_ex)*$ex_sewing_qty_outbound;
					 
					 
					 $prod_date_buyer_wise[$ex_row[csf("serving_company")]][$ex_row[csf("buyer_name")]]['ex_cm_value_in']+=$ex_cm_value_in;
					 $prod_date_buyer_wise[$ex_row[csf("serving_company")]][$ex_row[csf("buyer_name")]]['ex_cm_value_inhouse']+=$ex_cm_value_inhouse;
					 $prod_date_buyer_wise[$ex_row[csf("serving_company")]][$ex_row[csf("buyer_name")]]['ex_cm_value_outbound']+=$ex_cm_value_outbound;
					 $prod_date_buyer_wise[$ex_row[csf("serving_company")]][$ex_row[csf("buyer_name")]]['ex_factory_smry']+=$ex_row[csf("ex_factory_qnty")];
					 $prod_date_buyer_wise[$ex_row[csf("serving_company")]][$ex_row[csf("buyer_name")]]['ex_factory_qnty_inhouse']+=$ex_row[csf("ex_factory_qnty_inhouse")];
					 $prod_date_buyer_wise[$ex_row[csf("serving_company")]][$ex_row[csf("buyer_name")]]['ex_factory_qnty_outbound']+=$ex_row[csf("ex_factory_qnty_outbound")];	
					 $prod_date_buyer_wise[$ex_row[csf("serving_company")]][$ex_row[csf("buyer_name")]]['ex_factory_smry_unitPrice']=($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
					$prod_date_buyer_wise[$ex_row[csf("serving_company")]][$ex_row[csf("buyer_name")]]['ex_factory_smry_fobVal_inhouse']+=$ex_row[csf("ex_factory_qnty_inhouse")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
					$prod_date_buyer_wise[$ex_row[csf("serving_company")]][$ex_row[csf("buyer_name")]]['ex_factory_smry_fobVal_outbound']+=$ex_row[csf("ex_factory_qnty_outbound")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
					$prod_date_buyer_wise[$ex_row[csf("serving_company")]][$ex_row[csf("buyer_name")]]['ex_factory_smry_fobVal']+=$ex_row[csf("ex_factory_qnty")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
					
					//for summary part
					 $prod_date_buyer_wise_smry[$ex_row[csf("buyer_name")]]['ex_cm_value_in']+=$ex_cm_value_in;
					 $prod_date_buyer_wise_smry[$ex_row[csf("buyer_name")]]['ex_cm_value_inhouse']+=$ex_cm_value_inhouse;
					 $prod_date_buyer_wise_smry[$ex_row[csf("buyer_name")]]['ex_cm_value_outbound']+=$ex_cm_value_outbound;
					 $prod_date_buyer_wise_smry[$ex_row[csf("buyer_name")]]['ex_factory_smry']+=$ex_row[csf("ex_factory_qnty")];
					 $prod_date_buyer_wise_smry[$ex_row[csf("buyer_name")]]['ex_factory_qnty_inhouse']+=$ex_row[csf("ex_factory_qnty_inhouse")];
					 $prod_date_buyer_wise_smry[$ex_row[csf("buyer_name")]]['ex_factory_qnty_outbound']+=$ex_row[csf("ex_factory_qnty_outbound")];	
					 $prod_date_buyer_wise_smry[$ex_row[csf("buyer_name")]]['ex_factory_smry_unitPrice']=($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
					 $prod_date_buyer_wise_smry[$ex_row[csf("buyer_name")]]['ex_factory_smry_fobVal_inhouse']+=$ex_row[csf("ex_factory_qnty_inhouse")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
					$prod_date_buyer_wise_smry[$ex_row[csf("buyer_name")]]['ex_factory_smry_fobVal_outbound']+=$ex_row[csf("ex_factory_qnty_outbound")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
					$prod_date_buyer_wise_smry[$ex_row[csf("buyer_name")]]['ex_factory_smry_fobVal']+=$ex_row[csf("ex_factory_qnty")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
					//end
					
				}
				 
				// end for summary part
			}
			
			 ksort($prod_date);
			 $i=1;
			 $printing=0; $embing=0; $cuting_in=0; $cuting_out=0; $cuting=0; $sewing_in=0; $sewing_out=0; $sewing=0; $finish_in=0; $finish_out=0; $finish=0; $carton=0; $ord_in=0; $ord_out=0; $ord_tot=0;
			 
			if($cbo_company==0) $cbo_company_yes=$cbo_working_company.'_'.'workingComp'; else $cbo_company_yes=$cbo_company.'_'.'mainComp';
		?>
        <table width="2300px " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
         <p style="float:left"><strong>Buyer Wise Summary Part <? if($cbo_product_cat!=0){ echo '('.$product_category[$cbo_product_cat].')';} else {} ?></strong></p>
            <thead>
                <tr>
                    <th colspan="15">Production Status <? echo str_replace("'","",$txt_date_from) .' To '. str_replace("'","",$txt_date_to); ?> </th>
                    <th colspan="9">Export status  <? echo str_replace("'","",$txt_date_from) .' To '. str_replace("'","",$txt_date_to); ?></th>
                    <!--<th colspan="2">Wating for Export</th>-->
                </tr>
                <tr>
                 	<th width="30" rowspan="3" valign="middle">SL</th>
                    <th width="150" rowspan="3" valign="middle">Buyer Name</th>
                    <th width="100" colspan="3">Cutting</th>
                    <th width="100" colspan="3">Sewing</th>
                    <th width="100" colspan="3">Finishing</th>
                    <th width="100">Sewing CM Value</th>
                    <th width="100" colspan="3">FOB Value(On Sewing Qty)</th>
                    <th width="100" colspan="3">Ex-Factory Qty</th>
                    <th width="100" colspan="3">Ex-Factory CM Value</th>
                    <th width="100" colspan="3">FOB Value(On Ex-Factory Qty)</th>
                    <!--<th width="150" rowspan="3" valign="middle">Sew Out to Ship Bal</th>
                    <th rowspan="3" valign="middle">Sew Out to Ship Bal. FOB Value</th>-->
                </tr>
                <tr>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                </tr>
            </thead>
        </table>
        <div style="max-height:420px; overflow-y:scroll; width:2320px" id="scroll_body">
       	<table cellspacing="0" border="1" class="rpt_table"  width="2300px" rules="all" id="scroll_body" >
        <?
		$inc=1;
		$all_po_ids="";
		
		foreach($prod_date_buyer_wise_smry as $buyerKey =>$buyer_value)
		{
			if($buyerKey!="")
			{
				
				if($prod_date_buyer_wise_smry[$buyerKey]['cutting_qnty']!="" || $prod_date_buyer_wise_smry[$buyerKey]['sewing_qnty']!="" || $prod_date_buyer_wise_smry[$buyerKey]['finish_qnty']!="")
				{
				/*foreach($comp_value as $buyerKey =>$buyer_value)
				{*/
				
					if ($inc%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
		
				/*$date_all=add_date(str_replace("'","",$txt_date_from),$jk);
				$newdate =change_date_format($date_all);
				$po_id=$prod_date[$newdate]['po_breakdown_id'];
				//echo $all_po_id=chop($po_id,',');
				
				$all_po_ids.=$prod_date[$newdate]['po_breakdown_id'];
				//echo $sql_select_buyer="select b.buyer_name from a.wo_po_break_down,b.wo_po_details_master where a.id in($all_po_id) and a.job_no_mst=b.job_no";
				
				$produce_qty=$prod_date[$newdate]['sewingout_qnty_inhouse_pcs']/60;
				$effiecy_aff_perc=$produce_qty/($tsmvArr[$newdate]['smv']/60)*100;*/
				$poQTY=$job_array_summary[$companyKey][$buyerKey]['po_qty'];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_<? echo $inc; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $inc; ?>">
					<td width="30" ><? echo $inc; ?></td>
					<td width="150"><? echo $buyer_short_library[$prod_date_buyer_wise_smry[$buyerKey]['buyer_name']]; ?></td>
					<?php /*?><td width="100" align="right"><? echo number_format($poQTY,2); $smry_tot_po_qnty+=$poQTY; ?></td><?php */?>
					
				   
				   
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['cutting_qnty_inhouse'],2); $smry_tot_cutting_qnty_inhouse+=$prod_date_buyer_wise_smry[$buyerKey]['cutting_qnty_inhouse']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['cutting_qnty_outbound'],2); $smry_tot_cutting_qnty_outbound+=$prod_date_buyer_wise_smry[$buyerKey]['cutting_qnty_outbound']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['cutting_qnty'],2); $smry_tot_cutting_qnty+=$prod_date_buyer_wise_smry[$buyerKey]['cutting_qnty']; ?></td>
					
					
					<?php /*?><td width="100" align="right"><? echo number_format($prod_date_buyer_wise_smry[$companyKey][$buyerKey]['sewing_input_qnty'],2); $smry_tot_sewing_input_qnty+=$prod_date_buyer_wise_smry[$companyKey][$buyerKey]['sewing_input_qnty']; ?></td><?php */?>
					<?php /*?><td width="100" align="right"><? echo number_format($prod_date_buyer_wise_smry[$companyKey][$buyerKey]['sewing_qnty'],2); $smry_tot_sewing_qnty+=$prod_date_buyer_wise_smry[$companyKey][$buyerKey]['sewing_qnty'];  ?></td><?php */?>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['sewingout_qnty_inhouse'],2); $smry_tot_sewing_qnty_inhouse+=$prod_date_buyer_wise_smry[$buyerKey]['sewingout_qnty_inhouse']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['sewingout_qnty_outbound'],2); $smry_tot_sewing_qnty_outbound+=$prod_date_buyer_wise_smry[$buyerKey]['sewingout_qnty_outbound']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['sewing_qnty'],2); $smry_tot_sewing_qnty+=$prod_date_buyer_wise_smry[$buyerKey]['sewing_qnty']; ?></td>
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['finish_qnty_inhouse'],2); $smry_tot_finish_qnty_inhouse+=$prod_date_buyer_wise_smry[$buyerKey]['finish_qnty_inhouse']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['finish_qnty_outbound'],2); $smry_tot_finish_qnty_outbound+=$prod_date_buyer_wise_smry[$buyerKey]['finish_qnty_outbound']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['finish_qnty'],2); $smry_tot_finish_qnty+=$prod_date_buyer_wise_smry[$buyerKey]['finish_qnty']; ?></td>
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['cm_value_in'],2); $smry_tot_cm_value_in+=$prod_date_buyer_wise_smry[$buyerKey]['cm_value_in']; ?></td>
					
					
					<td width="80" align="right"><? $swing_val_inhouse= $prod_date_buyer_wise_smry[$buyerKey]['sewingout_value_inhouse']; echo number_format($swing_val_inhouse,2); $smry_tot_sewing_fob_val_input_qnty_inhouse+=$swing_val_inhouse; ?></td>
					<td width="80" align="right"><? $swing_val_outbound= $prod_date_buyer_wise_smry[$buyerKey]['sewingout_value_outbound']; echo number_format($swing_val_outbound,2); $smry_tot_sewing_fob_val_input_qnty_outbound+=$swing_val_outbound; ?></td>
					<td width="80" align="right"><? echo number_format(($swing_val_inhouse+$swing_val_outbound),2);  $total_sew_order_value+=($swing_val_inhouse+$swing_val_outbound);  ?></td>
					
					
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['ex_factory_qnty_inhouse'],2); $smry_tot_ex_factory_inhouse+=$prod_date_buyer_wise_smry[$buyerKey]['ex_factory_qnty_inhouse']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['ex_factory_qnty_outbound'],2); $smry_tot_ex_factory_outbound+=$prod_date_buyer_wise_smry[$buyerKey]['ex_factory_qnty_outbound']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['ex_factory_smry'],2); $smry_tot_ex_factory+=$prod_date_buyer_wise_smry[$buyerKey]['ex_factory_smry']; ?></td>
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['ex_cm_value_inhouse'],2); $smry_tot_ex_factory_val_inhouse+=$prod_date_buyer_wise_smry[$buyerKey]['ex_cm_value_inhouse']; ?></td> 
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['ex_cm_value_outbound'],2); $smry_tot_ex_factory_val_outbound+=$prod_date_buyer_wise_smry[$buyerKey]['ex_cm_value_outbound']; ?></td> 
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_smry[$buyerKey]['ex_cm_value_in'],2); $smry_tot_ex_factory_val+=$prod_date_buyer_wise_smry[$buyerKey]['ex_cm_value_in']; ?></td> 
					
				   <?php /*?> <td width="100" align="right"><? $exfactory_unitPrice= $prod_date_buyer_wise_smry[$companyKey][$buyerKey]['ex_factory_smry_unitPrice']*$prod_date_buyer_wise_smry[$companyKey][$buyerKey]['ex_factory_smry']; echo number_format($exfactory_unitPrice,2); $smry_tot_ex_factory_fob_val+=$exfactory_unitPrice; ?></td><?php */?>
					
					
					<td width="80" align="right"><? $exfactory_unitPrice= $prod_date_buyer_wise_smry[$buyerKey]['ex_factory_smry_fobVal_inhouse']; echo number_format($exfactory_unitPrice,2); $smry_tot_ex_factory_fob_val_inhouse+=$exfactory_unitPrice; ?></td>
					 <td width="80" align="right"><? $exfactory_unitPrice= $prod_date_buyer_wise_smry[$buyerKey]['ex_factory_smry_fobVal_outbound']; echo number_format($exfactory_unitPrice,2); $smry_tot_ex_factory_fob_val_outbound+=$exfactory_unitPrice; ?></td>
					 <td width="80" align="right"><? $exfactory_unitPrice= $prod_date_buyer_wise_smry[$buyerKey]['ex_factory_smry_fobVal']; echo number_format($exfactory_unitPrice,2); $smry_tot_ex_factory_fob_val+=$exfactory_unitPrice; ?></td>
					 
					 
					<?php /*?><td width="150" align="right"><? $ship_balance=$prod_date_buyer_wise_smry[$buyerKey]['sewing_qnty']-$prod_date_buyer_wise_smry[$buyerKey]['ex_factory_smry']; echo number_format($ship_balance,2); $smry_tot_ship_balance+=$ship_balance; ?></td><?php */?>
					
					
					<?php /*?><td  align="right"><? $sew_out_ship_ex_fob_balance=($swing_val_inhouse+$swing_val_outbound)-$prod_date_buyer_wise_smry[$buyerKey]['ex_factory_smry_fobVal']; echo number_format($sew_out_ship_ex_fob_balance,2); $smry_tot_sew_out_ship_ex_fob_balance+=$sew_out_ship_ex_fob_balance; ?></td><?php */?>
				   
				</tr>
			<?
			//number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_fob_val_input_qnty'],2).'_'.
		$inc++;
				}
			}
			//}
		}
	  	echo $all_po_ids;
		?>
         <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                <th width="150">Total</th>
              <?php /*?>  <th width="100"><? echo number_format($smry_tot_po_qnty,2); ?></th><?php */?>
                <th width="80"><? echo number_format($smry_tot_cutting_qnty_inhouse,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_cutting_qnty_outbound,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_cutting_qnty,2); ?></th>
                <?php /*?><th width="100"><? echo number_format($smry_tot_sewing_input_qnty,2); ?></th><?php */?>
               <?php /*?> <th width="100"><? echo number_format($smry_tot_sewing_qnty,2); ?></th><?php */?>
               
                <th width="80"><? echo number_format($smry_tot_sewing_qnty_inhouse,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_sewing_qnty_outbound,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_sewing_qnty,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_finish_qnty_inhouse,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_finish_qnty_outbound,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_finish_qnty,2); ?></th>
                
                <th width="100"><? echo number_format($smry_tot_cm_value_in,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_sewing_fob_val_input_qnty_inhouse,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_sewing_fob_val_input_qnty_outbound,2); ?></th>
                <th width="80"><? echo number_format($total_sew_order_value,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_ex_factory_inhouse,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_outbound,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_ex_factory_val_inhouse,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_val_outbound,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_val,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_ex_factory_fob_val_inhouse,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_fob_val_outbound,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_fob_val,2); ?></th>

               <?php /*?> <th width="150"><? echo number_format($smry_tot_ship_balance,2); ?></th>
                <th><? echo number_format($smry_tot_sew_out_ship_ex_fob_balance,2); ?></th><?php */?>
             </tr>
           </tfoot>
        </table>
        </div>
         <br/>
         <!--  ITEM wise-->
         <?
         if($cbo_product_cat==0)
         {
		 ?>
        <table width="2300px " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
         <p style="float:left"><strong>Summary all Company: Regular Order</strong></p>
            <thead>
                <tr>
                    <th colspan="15">Production Status <? echo str_replace("'","",$txt_date_from) .' To '. str_replace("'","",$txt_date_to); ?> </th>
                    <th colspan="9">Export status  <? echo str_replace("'","",$txt_date_from) .' To '. str_replace("'","",$txt_date_to); ?></th>
                    <!--<th colspan="2">Wating for Export</th>-->
                </tr>
                <tr>
                 	<th width="30" rowspan="3" valign="middle">SL</th>
                    <th width="150" rowspan="3" valign="middle">Buyer Name</th>
                    <th width="100" colspan="3">Cutting</th>
                    <th width="100" colspan="3">Sewing</th>
                    <th width="100" colspan="3">Finishing</th>
                    <th width="100">Sewing CM Value</th>
                    <th width="100" colspan="3">FOB Value(On Sewing Qty)</th>
                    <th width="100" colspan="3">Ex-Factory Qty</th>
                    <th width="100" colspan="3">Ex-Factory CM Value</th>
                    <th width="100" colspan="3">FOB Value(On Ex-Factory Qty)</th>
                   <!-- <th width="150" rowspan="3" valign="middle">Sew Out to Ship Bal</th>
                    <th rowspan="3" valign="middle">Sew Out to Ship Bal. FOB Value</th>-->
                </tr>
                <tr>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                </tr>
            </thead>
        </table>
        <div> 
       	<table cellspacing="0" border="1" class="rpt_table"  width="2300px" rules="all" id="scroll_body_summery_itemCat">
        <?
		$incs=1;
		$all_po_ids="";
		/*for($jk=0;$jk<$datediff;$jk++)
			{*/
		foreach($prod_date_buyer_wise_all as $buyerKey =>$buyer_value)
		{
			if($buyerKey!="")
			{
				/*foreach($prod_date_buyer_wise_all as $buyerKey =>$buyer_value)
				{*/
				if($prod_date_buyer_wise_all[$buyerKey]['cutting_qnty_all']!="" || $prod_date_buyer_wise_all[$buyerKey]['sewing_qnty_all']!="" || $prod_date_buyer_wise_all[$buyerKey]['finish_qnty_all']!="")
				{
				if ($incs%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				$poQTY=$job_array_summary[$companyKey][$buyerKey]['po_qty'];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_2_<? echo $incs; ?>','<? echo $bgcolor; ?>')" id="trs_2_<? echo $incs; ?>">
					<td width="30" ><? echo $incs; ?></td>
					<td width="150"><? echo $buyer_short_library[$prod_date_buyer_wise_all[$buyerKey]['buyer_name']]; ?></td>
					<?php /*?><td width="100" align="right"><? echo number_format($poQTY,2); $smry_tot_po_qnty+=$poQTY; ?></td><?php */?>
					
				   
				   
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['cutting_qnty_inhouse_all'],2); $smry_tot_cutting_qnty_inhouse_all+=$prod_date_buyer_wise_all[$buyerKey]['cutting_qnty_inhouse_all']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['cutting_qnty_outbound_all'],2); $smry_tot_cutting_qnty_outbound_all+=$prod_date_buyer_wise_all[$buyerKey]['cutting_qnty_outbound_all']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['cutting_qnty_all'],2); $smry_tot_cutting_qnty_all+=$prod_date_buyer_wise_all[$buyerKey]['cutting_qnty_all']; ?></td>
					
					
					<?php /*?><td width="100" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_input_qnty'],2); $smry_tot_sewing_input_qnty+=$prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_input_qnty']; ?></td><?php */?>
					<?php /*?><td width="100" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_qnty'],2); $smry_tot_sewing_qnty+=$prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_qnty'];  ?></td><?php */?>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['sewingout_qnty_inhouse_all'],2); $smry_tot_sewing_qnty_inhouse_all+=$prod_date_buyer_wise_all[$buyerKey]['sewingout_qnty_inhouse_all']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['sewingout_qnty_outbound_all'],2); $smry_tot_sewing_qnty_outbound_all+=$prod_date_buyer_wise_all[$buyerKey]['sewingout_qnty_outbound_all']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['sewing_qnty_all'],2); $smry_tot_sewing_qnty_all+=$prod_date_buyer_wise_all[$buyerKey]['sewing_qnty_all']; ?></td>
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['finish_qnty_inhouse_all'],2); $smry_tot_finish_qnty_inhouse_all+=$prod_date_buyer_wise_all[$buyerKey]['finish_qnty_inhouse_all']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['finish_qnty_outbound_all'],2); $smry_tot_finish_qnty_outbound_all+=$prod_date_buyer_wise_all[$buyerKey]['finish_qnty_outbound_all']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['finish_qnty_all'],2); $smry_tot_finish_qnty_all+=$prod_date_buyer_wise_all[$buyerKey]['finish_qnty_all']; ?></td>
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['cm_value_in_all'],2); $smry_tot_cm_value_in_all+=$prod_date_buyer_wise_all[$buyerKey]['cm_value_in_all']; ?></td>
					
					
					<td width="80" align="right"><? $swing_val_inhouse_all= $prod_date_buyer_wise_all[$buyerKey]['sewingout_value_inhouse_all']; echo number_format($swing_val_inhouse_all,2); $smry_tot_sewing_fob_val_input_qnty_inhouse_all+=$swing_val_inhouse_all; ?></td>
					<td width="80" align="right"><? $swing_val_outbound_all= $prod_date_buyer_wise_all[$buyerKey]['sewingout_value_outbound_all']; echo number_format($swing_val_outbound_all,2); $smry_tot_sewing_fob_val_input_qnty_outbound_all+=$swing_val_outbound_all; ?></td>
					<td width="80" align="right"><? echo number_format(($swing_val_inhouse_all+$swing_val_outbound_all),2);  $total_sew_order_value_all+=($swing_val_inhouse_all+$swing_val_outbound_all);  ?></td>
					
					
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['ex_factory_qnty_inhouse_all'],2); $smry_tot_ex_factory_inhouse_all+=$prod_date_buyer_wise_all[$buyerKey]['ex_factory_qnty_inhouse_all']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['ex_factory_qnty_outbound_all'],2); $smry_tot_ex_factory_outbound_all+=$prod_date_buyer_wise_all[$buyerKey]['ex_factory_qnty_outbound_all']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['ex_factory_smry_all'],2); $smry_tot_ex_factory_all+=$prod_date_buyer_wise_all[$buyerKey]['ex_factory_smry_all']; ?></td>
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['ex_cm_value_inhouse_all'],2); $smry_tot_ex_factory_val_inhouse_all+=$prod_date_buyer_wise_all[$buyerKey]['ex_cm_value_inhouse_all']; ?></td> 
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['ex_cm_value_outbound_all'],2); $smry_tot_ex_factory_val_outbound_all+=$prod_date_buyer_wise_all[$buyerKey]['ex_cm_value_outbound_all']; ?></td> 
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_all[$buyerKey]['ex_cm_value_in_all'],2); $smry_tot_ex_factory_val_all+=$prod_date_buyer_wise_all[$buyerKey]['ex_cm_value_in_all']; ?></td> 
					
				   <?php /*?> <td width="100" align="right"><? $exfactory_unitPrice= $prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_smry_unitPrice']*$prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_smry']; echo number_format($exfactory_unitPrice,2); $smry_tot_ex_factory_fob_val+=$exfactory_unitPrice; ?></td><?php */?>
					
					
					<td width="80" align="right"><? $exfactory_unitPrice_all= $prod_date_buyer_wise_all[$buyerKey]['ex_factory_smry_fobVal_inhouse_all']; echo number_format($exfactory_unitPrice_all,2); $smry_tot_ex_factory_fob_val_inhouse_all+=$exfactory_unitPrice_all; ?></td>
					 <td width="80" align="right"><? $exfactory_unitPrice_all= $prod_date_buyer_wise_all[$buyerKey]['ex_factory_smry_fobVal_outbound_all']; echo number_format($exfactory_unitPrice_all,2); $smry_tot_ex_factory_fob_val_outbound_all+=$exfactory_unitPrice_all; ?></td>
					 <td width="80" align="right"><? $exfactory_unitPrice_all= $prod_date_buyer_wise_all[$buyerKey]['ex_factory_smry_fobVal_all']; echo number_format($exfactory_unitPrice_all,2); $smry_tot_ex_factory_fob_val_all+=$exfactory_unitPrice_all; ?></td>
					 
					 
					<?php /*?><td width="150" align="right"><? $ship_balance_all=$prod_date_buyer_wise_all[$buyerKey]['sewing_qnty_all']-$prod_date_buyer_wise_all[$buyerKey]['ex_factory_smry_all']; echo number_format($ship_balance_all,2); $smry_tot_ship_balance_all+=$ship_balance_all; ?></td><?php */?>
					
					
					<?php /*?><td  align="right"><? $sew_out_ship_ex_fob_balance_all=($swing_val_inhouse_all+$swing_val_outbound_all)-$prod_date_buyer_wise_all[$buyerKey]['ex_factory_smry_fobVal_all']; echo number_format($sew_out_ship_ex_fob_balance_all,2); $smry_tot_sew_out_ship_ex_fob_balance_all+=$sew_out_ship_ex_fob_balance_all; ?></td><?php */?>
				   
				</tr>
			<?
			//number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_fob_val_input_qnty'],2).'_'.
		$incs++;
				}
			}
		}
		//}
	//echo $all_po_ids;
		?>
         <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                <th width="150">Total</th>
              <?php /*?>  <th width="100"><? echo number_format($smry_tot_po_qnty,2); ?></th><?php */?>
                <th width="80"><? echo number_format($smry_tot_cutting_qnty_inhouse_all,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_cutting_qnty_outbound_all,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_cutting_qnty_all,2); ?></th>
                <?php /*?><th width="100"><? echo number_format($smry_tot_sewing_input_qnty,2); ?></th><?php */?>
               <?php /*?> <th width="100"><? echo number_format($smry_tot_sewing_qnty,2); ?></th><?php */?>
               
                <th width="80"><? echo number_format($smry_tot_sewing_qnty_inhouse_all,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_sewing_qnty_outbound_all,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_sewing_qnty_all,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_finish_qnty_inhouse_all,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_finish_qnty_outbound_all,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_finish_qnty_all,2); ?></th>
                
                <th width="100"><? echo number_format($smry_tot_cm_value_in_all,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_sewing_fob_val_input_qnty_inhouse_all,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_sewing_fob_val_input_qnty_outbound_all,2); ?></th>
                <th width="80"><? echo number_format($total_sew_order_value_all,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_ex_factory_inhouse_all,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_outbound_all,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_all,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_ex_factory_val_inhouse_all,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_val_outbound_all,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_val_all,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_ex_factory_fob_val_inhouse_all,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_fob_val_outbound_all,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_fob_val_all,2); ?></th>

               <?php /*?> <th width="150"><? echo number_format($smry_tot_ship_balance_all,2); ?></th>
                <th><? echo number_format($smry_tot_sew_out_ship_ex_fob_balance_all,2); ?></th><?php */?>
             </tr>
           </tfoot>
        </table>
        </div>
         <br/>

         <table width="2300px " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
         <p style="float:left"><strong>Summary all Company: UG Order</strong></p>
            <thead>
                <tr>
                    <th colspan="15">Production Status <? echo str_replace("'","",$txt_date_from) .' To '. str_replace("'","",$txt_date_to); ?> </th>
                    <th colspan="9">Export status  <? echo str_replace("'","",$txt_date_from) .' To '. str_replace("'","",$txt_date_to); ?></th>
                    <!--<th colspan="2">Wating for Export</th>-->
                </tr>
                <tr>
                 	<th width="30" rowspan="3" valign="middle">SL</th>
                    <th width="150" rowspan="3" valign="middle">Buyer Name</th>
                    <th width="100" colspan="3">Cutting</th>
                    <th width="100" colspan="3">Sewing</th>
                    <th width="100" colspan="3">Finishing</th>
                    <th width="100">Sewing CM Value</th>
                    <th width="100" colspan="3">FOB Value(On Sewing Qty)</th>
                    <th width="100" colspan="3">Ex-Factory Qty</th>
                    <th width="100" colspan="3">Ex-Factory CM Value</th>
                    <th width="100" colspan="3">FOB Value(On Ex-Factory Qty)</th>
                   <!-- <th width="150" rowspan="3" valign="middle">Sew Out to Ship Bal</th>
                    <th rowspan="3" valign="middle">Sew Out to Ship Bal. FOB Value</th>-->
                </tr>
                <tr>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                </tr>
            </thead>
         </table>
        <div> 
       	<table cellspacing="0" border="1" class="rpt_table"  width="2300px" rules="all" id="scroll_body_summery_itemCat_ug">
        <?
		$incss=1;
		$all_po_idss="";
		/*for($jk=0;$jk<$datediff;$jk++)
			{*/
		foreach($prod_date_buyer_wise_lin as $buyerKey =>$buyer_value)
		{
			if($buyerKey!="")
			{
				/*foreach($prod_date_buyer_wise_lin as $buyerKey =>$buyer_value)
				{*/
				
				if($prod_date_buyer_wise_lin[$buyerKey]['cutting_qnty_lin']!="" || $prod_date_buyer_wise_lin[$buyerKey]['sewing_qnty_lin']!="" || $prod_date_buyer_wise_lin[$buyerKey]['finish_qnty_lin']!="")
				{
				if ($incss%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				$poQTY=$job_array_summary[$companyKey][$buyerKey]['po_qty'];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_3_<? echo $incss; ?>','<? echo $bgcolor; ?>')" id="trs_3_<? echo $incss; ?>">
					<td width="30" ><? echo $incss; ?></td>
					<td width="150"><? echo $buyer_short_library[$prod_date_buyer_wise_lin[$buyerKey]['buyer_name']]; ?></td>
					<?php /*?><td width="100" align="right"><? echo number_format($poQTY,2); $smry_tot_po_qnty+=$poQTY; ?></td><?php */?>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['cutting_qnty_inhouse_lin'],2); $smry_tot_cutting_qnty_inhouse_lin+=$prod_date_buyer_wise_lin[$buyerKey]['cutting_qnty_inhouse_lin']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['cutting_qnty_outbound_lin'],2); $smry_tot_cutting_qnty_outbound_lin+=$prod_date_buyer_wise_lin[$buyerKey]['cutting_qnty_outbound_lin']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['cutting_qnty_lin'],2); $smry_tot_cutting_qnty_lin+=$prod_date_buyer_wise_lin[$buyerKey]['cutting_qnty_lin']; ?></td>
					
					
					<?php /*?><td width="100" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_input_qnty'],2); $smry_tot_sewing_input_qnty+=$prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_input_qnty']; ?></td><?php */?>
					<?php /*?><td width="100" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_qnty'],2); $smry_tot_sewing_qnty+=$prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_qnty'];  ?></td><?php */?>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['sewingout_qnty_inhouse_lin'],2); $smry_tot_sewing_qnty_inhouse_lin+=$prod_date_buyer_wise_lin[$buyerKey]['sewingout_qnty_inhouse_lin']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['sewingout_qnty_outbound_lin'],2); $smry_tot_sewing_qnty_outbound_lin+=$prod_date_buyer_wise_lin[$buyerKey]['sewingout_qnty_outbound_lin']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['sewing_qnty_lin'],2); $smry_tot_sewing_qnty_lin+=$prod_date_buyer_wise_lin[$buyerKey]['sewing_qnty_lin']; ?></td>
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['finish_qnty_inhouse_lin'],2); $smry_tot_finish_qnty_inhouse_lin+=$prod_date_buyer_wise_lin[$buyerKey]['finish_qnty_inhouse_lin']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['finish_qnty_outbound_lin'],2); $smry_tot_finish_qnty_outbound_lin+=$prod_date_buyer_wise_lin[$buyerKey]['finish_qnty_outbound_lin']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['finish_qnty_lin'],2); $smry_tot_finish_qnty_lin+=$prod_date_buyer_wise_lin[$buyerKey]['finish_qnty_lin']; ?></td>
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['cm_value_in_lin'],2); $smry_tot_cm_value_in_lin+=$prod_date_buyer_wise_lin[$buyerKey]['cm_value_in_lin']; ?></td>
					
					
					<td width="80" align="right"><? $swing_val_inhouse_lin= $prod_date_buyer_wise_lin[$buyerKey]['sewingout_value_inhouse_lin']; echo number_format($swing_val_inhouse_lin,2); $smry_tot_sewing_fob_val_input_qnty_inhouse_lin+=$swing_val_inhouse_lin; ?></td>
					<td width="80" align="right"><? $swing_val_outbound_lin= $prod_date_buyer_wise_lin[$buyerKey]['sewingout_value_outbound_lin']; echo number_format($swing_val_outbound_lin,2); $smry_tot_sewing_fob_val_input_qnty_outbound_lin+=$swing_val_outbound_lin; ?></td>
					<td width="80" align="right"><? echo number_format(($swing_val_inhouse_lin+$swing_val_outbound_lin),2);  $total_sew_order_value_lin+=($swing_val_inhouse_lin+$swing_val_outbound_lin);  ?></td>
					
					
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['ex_factory_qnty_inhouse_lin'],2); $smry_tot_ex_factory_inhouse_lin+=$prod_date_buyer_wise_lin[$buyerKey]['ex_factory_qnty_inhouse_lin']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['ex_factory_qnty_outbound_lin'],2); $smry_tot_ex_factory_outbound_lin+=$prod_date_buyer_wise_lin[$buyerKey]['ex_factory_qnty_outbound_lin']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['ex_factory_smry_lin'],2); $smry_tot_ex_factory_lin+=$prod_date_buyer_wise_lin[$buyerKey]['ex_factory_smry_lin']; ?></td>
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['ex_cm_value_inhouse_lin'],2); $smry_tot_ex_factory_val_inhouse_lin+=$prod_date_buyer_wise_lin[$buyerKey]['ex_cm_value_inhouse_lin']; ?></td> 
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['ex_cm_value_outbound_lin'],2); $smry_tot_ex_factory_val_outbound_lin+=$prod_date_buyer_wise_lin[$buyerKey]['ex_cm_value_outbound_lin']; ?></td> 
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise_lin[$buyerKey]['ex_cm_value_in_lin'],2); $smry_tot_ex_factory_val_lin+=$prod_date_buyer_wise_lin[$buyerKey]['ex_cm_value_in_lin']; ?></td> 
					
				   <?php /*?> <td width="100" align="right"><? $exfactory_unitPrice= $prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_smry_unitPrice']*$prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_smry']; echo number_format($exfactory_unitPrice,2); $smry_tot_ex_factory_fob_val+=$exfactory_unitPrice; ?></td><?php */?>
					
					
					<td width="80" align="right"><? $exfactory_unitPrice_lin= $prod_date_buyer_wise_lin[$buyerKey]['ex_factory_smry_fobVal_inhouse_lin']; echo number_format($exfactory_unitPrice_lin,2); $smry_tot_ex_factory_fob_val_inhouse_lin+=$exfactory_unitPrice_lin; ?></td>
					 <td width="80" align="right"><? $exfactory_unitPrice_lin= $prod_date_buyer_wise_lin[$buyerKey]['ex_factory_smry_fobVal_outbound_lin']; echo number_format($exfactory_unitPrice_lin,2); $smry_tot_ex_factory_fob_val_outbound_lin+=$exfactory_unitPrice_lin; ?></td>
					 <td width="80" align="right"><? $exfactory_unitPrice_lin= $prod_date_buyer_wise_lin[$buyerKey]['ex_factory_smry_fobVal_lin']; echo number_format($exfactory_unitPrice_lin,2); $smry_tot_ex_factory_fob_val_lin+=$exfactory_unitPrice_lin; ?></td>
					 
					<?php /*?> 
					<td width="150" align="right"><? $ship_balance_lin=$prod_date_buyer_wise_lin[$buyerKey]['sewing_qnty_lin']-$prod_date_buyer_wise_lin[$buyerKey]['ex_factory_smry_lin']; echo number_format($ship_balance_lin,2); $smry_tot_ship_balance_lin+=$ship_balance_lin; ?></td><?php */?>
					
					
					<?php /*?><td  align="right"><? $sew_out_ship_ex_fob_balance_lin=($swing_val_inhouse_lin+$swing_val_outbound_lin)-$prod_date_buyer_wise_lin[$buyerKey]['ex_factory_smry_fobVal_lin']; echo number_format($sew_out_ship_ex_fob_balance_lin,2); $smry_tot_sew_out_ship_ex_fob_balance_lin+=$sew_out_ship_ex_fob_balance_lin; ?></td><?php */?>
				   
				</tr>
			<?
			//number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_fob_val_input_qnty'],2).'_'.
		$incss++;
				}
			//}
			}
		}
	//echo $all_po_idss;
		?>
         <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                <th width="150">Total</th>
              <?php /*?>  <th width="100"><? echo number_format($smry_tot_po_qnty,2); ?></th><?php */?>
                <th width="80"><? echo number_format($smry_tot_cutting_qnty_inhouse_lin,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_cutting_qnty_outbound_lin,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_cutting_qnty_lin,2); ?></th>
                <?php /*?><th width="100"><? echo number_format($smry_tot_sewing_input_qnty,2); ?></th><?php */?>
               <?php /*?> <th width="100"><? echo number_format($smry_tot_sewing_qnty,2); ?></th><?php */?>
               
                <th width="80"><? echo number_format($smry_tot_sewing_qnty_inhouse_lin,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_sewing_qnty_outbound_lin,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_sewing_qnty_lin,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_finish_qnty_inhouse_lin,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_finish_qnty_outbound_lin,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_finish_qnty_lin,2); ?></th>
                
                <th width="100"><? echo number_format($smry_tot_cm_value_in_lin,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_sewing_fob_val_input_qnty_inhouse_lin,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_sewing_fob_val_input_qnty_outbound_lin,2); ?></th>
                <th width="80"><? echo number_format($total_sew_order_value_lin,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_ex_factory_inhouse_lin,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_outbound_lin,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_lin,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_ex_factory_val_inhouse_lin,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_val_outbound_lin,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_val_lin,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_ex_factory_fob_val_inhouse_lin,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_fob_val_outbound_lin,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_fob_val_lin,2); ?></th>

               <?php /*?> <th width="150"><? echo number_format($smry_tot_ship_balance_lin,2); ?></th>
                <th><? echo number_format($smry_tot_sew_out_ship_ex_fob_balance_lin,2); ?></th><?php */?>
             </tr>
           </tfoot>
        </table>
        </div>
         <br/>
      
    <? 
		}  //end prod cat if condition 
		
		//details part start here
		//$a="";
		$all_po_ids_dtls="";
		$workCom=explode(",",$cbo_working_company);
		foreach($prod_date_buyer_wise as $companyKey =>$comp_value)
		{
			//$a.=$companyKey.',';
			
			$inc_dtls=1;
			if($companyKey!="")
			{
		?>
			<table width="2300px" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
	         <p style="float:left;"><strong>Working Company:  <? echo $company_library[$companyKey];?></strong></p>
	            <thead>
	                <tr>
	                    <th colspan="15">Production Status <? echo str_replace("'","",$txt_date_from) .' To '. str_replace("'","",$txt_date_to); ?> </th>
	                    <th colspan="9">Export status  <? echo str_replace("'","",$txt_date_from) .' To '. str_replace("'","",$txt_date_to); ?></th>
	                    <!--<th colspan="2">Wating for Export</th>-->
	                </tr>
	                <tr>
	                 	<th width="30" rowspan="3" valign="middle">SL</th>
	                    <th width="150" rowspan="3" valign="middle">Buyer Name</th>
	                    <th width="100" colspan="3">Cutting</th>
	                    <th width="100" colspan="3">Sewing</th>
	                    <th width="100" colspan="3">Finishing</th>
	                    <th width="100">Sewing CM Value</th>
	                    <th width="100" colspan="3">FOB Value(On Sewing Qty)</th>
	                    <th width="100" colspan="3">Ex-Factory Qty</th>
	                    <th width="100" colspan="3">Ex-Factor CM Value</th>
	                    <th width="100" colspan="3">FOB Value(On Ex-Factory Qty)</th>
	                    <!--<th width="150" rowspan="3" valign="middle">Sew Out to Ship Bal</th>
	                    <th rowspan="3" valign="middle">Sew Out to Ship Bal. FOB Value</th>-->
	                </tr>
	                <tr>
	                    <th rowspan="2" width="80">In House</th>
	                    <th rowspan="2" width="80">Sub Contact</th>
	                    <th rowspan="2" width="80">Total</th>
	                    <th rowspan="2" width="80">In House</th>
	                    <th rowspan="2" width="80">Sub Contact</th>
	                    <th rowspan="2" width="80">Total</th>
	                    <th rowspan="2" width="80">In House</th>
	                    <th rowspan="2" width="80">Sub Contact</th>
	                    <th rowspan="2" width="80">Total</th>
	                    <th rowspan="2" width="80">In House</th>
	                    <th rowspan="2" width="80">In House</th>
	                    <th rowspan="2" width="80">Sub Contact</th>
	                    <th rowspan="2" width="80">Total</th>
	                    <th rowspan="2" width="80">In House</th>
	                    <th rowspan="2" width="80">Sub Contact</th>
	                    <th rowspan="2" width="80">Total</th>
	                    <th rowspan="2" width="80">In House</th>
	                    <th rowspan="2" width="80">Sub Contact</th>
	                    <th rowspan="2" width="80">Total</th>
	                    <th rowspan="2" width="80">In House</th>
	                    <th rowspan="2" width="80">Sub Contact</th>
	                    <th rowspan="2" width="80">Total</th>
	                </tr>
	            </thead>
	        </table>
	        <div style="max-height:420px; overflow-y:scroll; width:2320px;" id="scroll_body_summery_dtls">
	       	<table cellspacing="0" border="1" class="rpt_table"  width="2300px" rules="all" id="scroll_body_summery_dtls" >
        <?
		
			
				foreach($comp_value as $buyerKey =>$buyer_value)
				{
				
					if ($inc_dtls%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
		
				$poQTY=$job_array_summary[$companyKey][$buyerKey]['po_qty'];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_4_<? echo $inc_dtls; ?>','<? echo $bgcolor; ?>')" id="trs_4_<? echo $inc_dtls; ?>">
					<td width="30" ><? echo $inc_dtls; ?></td>
					<td width="150" title="<? echo $company_library[$companyKey]; ?>"><? echo $buyer_short_library[$prod_date_buyer_wise[$companyKey][$buyerKey]['buyer_name']]; ?></td>
					
					
				   
				   
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['cutting_qnty_inhouse'],2); $smry_tot_cutting_qnty_inhouse_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['cutting_qnty_inhouse']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['cutting_qnty_outbound'],2); $smry_tot_cutting_qnty_outbound_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['cutting_qnty_outbound']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['cutting_qnty'],2); $smry_tot_cutting_qnty_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['cutting_qnty']; ?></td>
					
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['sewingout_qnty_inhouse'],2); $smry_tot_sewing_qnty_inhouse_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['sewingout_qnty_inhouse']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['sewingout_qnty_outbound'],2); $smry_tot_sewing_qnty_outbound_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['sewingout_qnty_outbound']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_qnty'],2); $smry_tot_sewing_qnty_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_qnty']; ?></td>
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['finish_qnty_inhouse'],2); $smry_tot_finish_qnty_inhouse_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['finish_qnty_inhouse']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['finish_qnty_outbound'],2); $smry_tot_finish_qnty_outbound_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['finish_qnty_outbound']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['finish_qnty'],2); $smry_tot_finish_qnty_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['finish_qnty']; ?></td>
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['cm_value_in'],2); $smry_tot_cm_value_in_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['cm_value_in']; ?></td>
					
					
					<td width="80" align="right"><? $swing_val_inhouse_dtls= $prod_date_buyer_wise[$companyKey][$buyerKey]['sewingout_value_inhouse']; echo number_format($swing_val_inhouse_dtls,2); $smry_tot_sewing_fob_val_input_qnty_inhouse_dtls+=$swing_val_inhouse_dtls; ?></td>
					<td width="80" align="right"><? $swing_val_outbound_dtls= $prod_date_buyer_wise[$companyKey][$buyerKey]['sewingout_value_outbound']; echo number_format($swing_val_outbound_dtls,2); $smry_tot_sewing_fob_val_input_qnty_outbound_dtls+=$swing_val_outbound_dtls; ?></td>
					<td width="80" align="right"><? echo number_format(($swing_val_inhouse_dtls+$swing_val_outbound_dtls),2);  $total_sew_order_value_dtls+=($swing_val_inhouse_dtls+$swing_val_outbound_dtls);  ?></td>
					
					
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_qnty_inhouse'],2); $smry_tot_ex_factory_inhouse_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_qnty_inhouse']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_qnty_outbound'],2); $smry_tot_ex_factory_outbound_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_qnty_outbound']; ?></td>
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_smry'],2); $smry_tot_ex_factory_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_smry']; ?></td>
					
					
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['ex_cm_value_inhouse'],2); $smry_tot_ex_factory_val_inhouse_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['ex_cm_value_inhouse']; ?></td> 
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['ex_cm_value_outbound'],2); $smry_tot_ex_factory_val_outbound_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['ex_cm_value_outbound']; ?></td> 
					<td width="80" align="right"><? echo number_format($prod_date_buyer_wise[$companyKey][$buyerKey]['ex_cm_value_in'],2); $smry_tot_ex_factory_val_dtls+=$prod_date_buyer_wise[$companyKey][$buyerKey]['ex_cm_value_in']; ?></td> 
					
				  
					
					
					<td width="80" align="right"><? $exfactory_unitPrice_dtls= $prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_smry_fobVal_inhouse']; echo number_format($exfactory_unitPrice_dtls,2); $smry_tot_ex_factory_fob_val_inhouse_dtls+=$exfactory_unitPrice_dtls; ?></td>
					 <td width="80" align="right"><? $exfactory_unitPrice_dtls= $prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_smry_fobVal_outbound']; echo number_format($exfactory_unitPrice_dtls,2); $smry_tot_ex_factory_fob_val_outbound_dtls+=$exfactory_unitPrice_dtls; ?></td>
					 <td width="80" align="right"><? $exfactory_unitPrice_dtls= $prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_smry_fobVal']; echo number_format($exfactory_unitPrice_dtls,2); $smry_tot_ex_factory_fob_val_dtls+=$exfactory_unitPrice_dtls; ?></td>
					 
					 
					<?php /*?><td width="150" align="right"><? $ship_balance_dtls=$prod_date_buyer_wise[$companyKey][$buyerKey]['sewing_qnty']-$prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_smry']; echo number_format($ship_balance_dtls,2); $smry_tot_ship_balance_dtls+=$ship_balance_dtls; ?></td><?php */?>
					
					
					<?php /*?><td  align="right"><? $sew_out_ship_ex_fob_balance_dtls=($swing_val_inhouse_dtls+$swing_val_outbound_dtls)-$prod_date_buyer_wise[$companyKey][$buyerKey]['ex_factory_smry_fobVal']; echo number_format($sew_out_ship_ex_fob_balance_dtls,2); $smry_tot_sew_out_ship_ex_fob_balance_dtls+=$sew_out_ship_ex_fob_balance_dtls; ?></td><?php */?>
				   
				</tr>
			<?
			
		$inc_dtls++;
				}
			
		
			echo $all_po_ids_dtls;
		?>
         <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                <th width="150">Total</th>
             
                <th width="80"><? echo number_format($smry_tot_cutting_qnty_inhouse_dtls,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_cutting_qnty_outbound_dtls,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_cutting_qnty_dtls,2); ?></th>
               
               
                <th width="80"><? echo number_format($smry_tot_sewing_qnty_inhouse_dtls,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_sewing_qnty_outbound_dtls,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_sewing_qnty_dtls,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_finish_qnty_inhouse_dtls,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_finish_qnty_outbound_dtls,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_finish_qnty_dtls,2); ?></th>
                
                <th width="100"><? echo number_format($smry_tot_cm_value_in_dtls,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_sewing_fob_val_input_qnty_inhouse_dtls,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_sewing_fob_val_input_qnty_outbound_dtls,2); ?></th>
                <th width="80"><? echo number_format($total_sew_order_value_dtls,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_ex_factory_inhouse_dtls,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_outbound_dtls,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_ex_factory_val_inhouse,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_val_outbound,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_val,2); ?></th>
                
                <th width="80"><? echo number_format($smry_tot_ex_factory_fob_val_inhouse_dtls,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_fob_val_outbound_dtls,2); ?></th>
                <th width="80"><? echo number_format($smry_tot_ex_factory_fob_val_dtls,2); ?></th>

              <?php /*?>  <th width="150"><? echo number_format($smry_tot_ship_balance_dtls,2); ?></th>
                <th><? echo number_format($smry_tot_sew_out_ship_ex_fob_balance_dtls,2); ?></th><?php */?>
             </tr>
           </tfoot>
           <?
		   
		   }
			$smry_tot_cutting_qnty_inhouse_dtls=0;
			$smry_tot_cutting_qnty_outbound_dtls=0;
			$smry_tot_cutting_qnty_dtls=0;
			
			$smry_tot_sewing_qnty_inhouse_dtls=0;
			$smry_tot_sewing_qnty_outbound_dtls=0;
			$smry_tot_sewing_qnty_dtls=0;
			
			$smry_tot_finish_qnty_inhouse_dtls=0;
			$smry_tot_finish_qnty_outbound_dtls=0;
			$smry_tot_finish_qnty_dtls=0;
			
			$smry_tot_cm_value_in_dtls=0;
			
			$smry_tot_sewing_fob_val_input_qnty_inhouse_dtls=0;
			$smry_tot_sewing_fob_val_input_qnty_outbound_dtls=0;
			$total_sew_order_value_dtls=0;
			
			$smry_tot_ex_factory_inhouse_dtls=0;
			$smry_tot_ex_factory_outbound_dtls=0;
			$smry_tot_ex_factory=0;
			
			$smry_tot_ex_factory_val_inhouse=0;
			$smry_tot_ex_factory_val_outbound=0;
			$smry_tot_ex_factory_val=0;
			
			$smry_tot_ex_factory_fob_val_inhouse_dtls=0;
			$smry_tot_ex_factory_fob_val_outbound_dtls=0;
			$smry_tot_ex_factory_fob_val_dtls=0;
			
			$smry_tot_ship_balance_dtls=0;
			$smry_tot_sew_out_ship_ex_fob_balance_dtls=0;
			//if(count($workCom)==1){ exit;} 
			//if(count($workCom)==1){ break;}  
			    
         }
		 
		//echo $a;
		 ?>
        </table>
        </div>
         <br/>
		<?		
		
	
	
	}
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
	//$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit(); 
    
}


?>