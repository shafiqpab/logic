<?
/*--------------------------------------------Comments----------------
Version (MySql)          :  V1
Version (Oracle)         :  V1
Converted by             :  MONZU
Converted Date           :  
Purpose			         : 	This form will create Bom Process
Functionality	         :	
JS Functions	         :
Created by		         :	Monzu 
Creation date 	         : 	17-11-2014
Requirment Client        : 
Requirment By            : 
Requirment type          : 
Requirment               : 
Affected page            : 
Affected Code            :                   
DB Script                : 
Updated by 		         : 		
Update date		         : 		   
QC Performed BY	         :		
QC Date			         :	
Comments		         :
----------------------------------------------------------------------*/
 
//echo load_freeze_divs ("../../",$permission); 
include('../../includes/common.php');
    if($db_type==0)
	{
	$shipment_date="and c.country_ship_date >= '2014-11-01'";
    }
	if($db_type==2)
	{
	$shipment_date="and c.country_ship_date >= '01-Oct-2014'";
    }
	$shiping_status="and c.shiping_status !=3";
	
	$po_arr=array();
	$ft_data_arr=array();
	 
	$sql_po="select a.id,a.job_no,a.style_ref_no,a.gmts_item_id,d.costing_per,d.sew_smv,e.fab_knit_req_kg   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_mst d, wo_pre_cost_sum_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no  and b.id=c.po_break_down_id  $shipment_date   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by a.id,b.id";
	$sql_po_data=sql_select($sql_po);
	foreach($sql_po_data as $sql_po_row)
	{
	$po_arr[po_id][$sql_po_row[csf('id')]]=$sql_po_row[csf('id')];
	$po_arr[job_no][$sql_po_row[csf('job_no')]]="'".$sql_po_row[csf('job_no')]."'";
	
	$ft_data_arr[$sql_po_row[csf('job_no')]][job_no]=$sql_po_row[csf('job_no')];//P.CODE
	$ft_data_arr[$sql_po_row[csf('job_no')]][gmts_item_id]=$sql_po_row[csf('gmts_item_id')];//P.TYPE
	$ft_data_arr[$sql_po_row[csf('job_no')]][style_ref_no]=$sql_po_row[csf('style_ref_no')];//P.DESCRIP
	$fab_knit_req_kg=0;
	if($sql_po_row[csf('costing_per')]==1)
	{
		$fab_knit_req_kg=$sql_po_row[csf('fab_knit_req_kg')]/12;
	}
	if($sql_po_row[csf('costing_per')]==2)
	{
		$fab_knit_req_kg=$sql_po_row[csf('fab_knit_req_kg')];
	}
	if($sql_po_row[csf('costing_per')]==3)
	{
		$fab_knit_req_kg=$sql_po_row[csf('fab_knit_req_kg')]/24;
	}
	if($sql_po_row[csf('costing_per')]==4)
	{
		$fab_knit_req_kg=$sql_po_row[csf('fab_knit_req_kg')]/36;
	}
	if($sql_po_row[csf('costing_per')]==5)
	{
		$fab_knit_req_kg=$sql_po_row[csf('fab_knit_req_kg')]/48;
	}
	$ft_data_arr[$sql_po_row[csf('job_no')]][fab_knit_req_kg]=number_format($fab_knit_req_kg,3);//P^CF:5
	$ft_data_arr[$sql_po_row[csf('job_no')]][cutting]=1;//P^WC:10
	
	
	
	$ft_data_arr[$sql_po_row[csf('job_no')]][sew_input]=1;//P^WC:35
	$ft_data_arr[$sql_po_row[csf('job_no')]][sew_output]=$sql_po_row[csf('sew_smv')];//P^WC:140
	
	$ft_data_arr[$sql_po_row[csf('job_no')]][poly]=1;//P^WC:160
	$ft_data_arr[$sql_po_row[csf('job_no')]][shiped]=$sql_po_row[csf('sew_smv')];//P^WC:165

	}
	
	
	$po_string= implode(",",$po_arr[po_id]);
	$job_string= implode(",",$po_arr[job_no]);
	
   $job=array_chunk($po_arr[job_no],1000, true);
   $job_cond_in="";
   $ji=0;
   foreach($job as $key=> $value)
   {
	   if($ji==0)
	   {
	    $job_cond_in="job_no in(".implode(",",$value).")"; 
	   }
	   else
	   {
		    $job_cond_in.=" or job_no in(".implode(",",$value).")"; 
	   }
	   $ji++;
   }
   
   
   $sql_fabric_prod=sql_select("select min(id) as id,job_no  from wo_pre_cost_fabric_cost_dtls where $job_cond_in and fabric_source=1 and avg_cons>0  and status_active=1 and is_deleted=0 group by job_no");
   foreach($sql_fabric_prod as $row_fabric_prod)
   {
	$ft_data_arr[fab_delivery][$row_fabric_prod[csf('job_no')]]=1;//P^WC:5   
   }
  $sql_print_embroid=sql_select("select min(id) as id,job_no,emb_name,avg(cons_dzn_gmts) as cons_dzn_gmts  from wo_pre_cost_embe_cost_dtls where $job_cond_in and emb_name in(1,2,3) and cons_dzn_gmts>0  and status_active=1 and is_deleted=0 group by job_no,emb_name");
  foreach($sql_print_embroid as $row_print_embroid)
  {
	  if($row_print_embroid[csf('emb_name')]==1)
	  {
		$ft_data_arr[$row_print_embroid[csf('job_no')]][dv_printing]=1; //P^WC:15
		$ft_data_arr[$row_print_embroid[csf('job_no')]][rv_printing]=1; //P^WC:20
	  }
	  if($row_print_embroid[csf('emb_name')]==2)
	  {
		$ft_data_arr[$row_print_embroid[csf('job_no')]][dv_embrodi]=1; //P^WC:25
		$ft_data_arr[$row_print_embroid[csf('job_no')]][rv_embrodi]=1; //P^WC:30
	  }
	  if($row_print_embroid[csf('emb_name')]==3)
	  {
		$ft_data_arr[$row_print_embroid[csf('job_no')]][dv_wash]=1; //P^WC:145
		$ft_data_arr[$row_print_embroid[csf('job_no')]][rv_wash]=1; //P^WC:150
	  }
  }
  
  //=================================Item wise Array Srart=====================================
		   $arr_itemsmv=array();
		   $sql_itemsmv=sql_select("select job_no,gmts_item_id,set_item_ratio,smv_pcs_precost,smv_set_precost  from wo_po_details_mas_set_details where $job_cond_in");
		   foreach($sql_itemsmv as $row_itemsmv)
		   {
			$arr_itemsmv[$row_itemsmv[csf('job_no')]][$row_itemsmv[csf('gmts_item_id')]]=$row_itemsmv[csf('smv_set_precost')];  
		   }
		   $array_fabric_cons_item=array();
		   $sql_fabric_cons_item=sql_select("select job_no,item_number_id,sum(avg_cons) as  avg_cons	  from wo_pre_cost_fabric_cost_dtls where $job_cond_in  and status_active=1 and is_deleted=0  group by job_no,item_number_id");
		   foreach($sql_fabric_cons_item as $row_fabric_cons_item)
		   {
			   $costingper=$po_arr[costing_per][$row_fabric_cons_item[csf('job_no')]];
			   $fab_knit_req_kg=0;
			   if($costingper==1)
				{
					$fab_knit_req_kg=$row_fabric_cons_item[csf('avg_cons')]/12;
				}
				if($costingper==2)
				{
					$fab_knit_req_kg=$row_fabric_cons_item[csf('avg_cons')];
				}
				if($costingper==3)
				{
					$fab_knit_req_kg=$row_fabric_cons_item[csf('avg_cons')]/24;
				}
				if($costingper==4)
				{
					$fab_knit_req_kg=$row_fabric_cons_item[csf('avg_cons')]/36;
				}
				if($costingper==5)
				{
					$fab_knit_req_kg=$row_fabric_cons_item[csf('avg_cons')]/48;
				}
			    $array_fabric_cons_item[$row_fabric_cons_item[csf('job_no')]][$row_fabric_cons_item[csf('item_number_id')]]=$fab_knit_req_kg;   
		   }
		   
		   $array_fabric_prod_item=array();
		   $sql_fabric_prod_item=sql_select("select min(id) as id,job_no,item_number_id  from wo_pre_cost_fabric_cost_dtls where $job_cond_in and fabric_source=1 and avg_cons>0  and status_active=1 and is_deleted=0  group by job_no,item_number_id");
		   foreach($sql_fabric_prod_item as $row_fabric_prod_item)
		   {
			$array_fabric_prod_item[fab_delivery][$row_fabric_prod_item[csf('job_no')]][$row_fabric_prod_item[csf('item_number_id')]]=1;   
		   }
		   //=================================Item wise Array End=====================================
	
print_r($ft_data_arr);

	
 

?>
 