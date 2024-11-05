<?php

class Salesorder_model extends CI_Model {

	private $buyer 		=array();
	private $company 	=array();
	private $supllier 	=array();
	private $color 		=array();
	private $yarnCount 	=array();
	private $machine 	=array();
	private $company_id =19;
	private $company_cond="";

	  function __construct() {
		  parent::__construct();
		  $this->buyer=return_library_array("select id,buyer_name from  lib_buyer  order by id","id","buyer_name");
		  $this->company=return_library_array("select id,company_name from  lib_company order by id","id","company_name");
		  $this->supllier=return_library_array("select id,supplier_name from  lib_supplier  order by id","id","supplier_name");
		  $this->color=return_library_array("select id,color_name from  lib_color  order by id","id","color_name");
		  $this->yarnCount=return_library_array("select id,yarn_count from  lib_yarn_count  order by id","id","yarn_count");
		  $this->machine=return_library_array("select id,machine_no from  lib_machine_name  order by id","id","machine_no");
		  $this->brand_arr=return_library_array("select id,brand_name from  lib_brand  order by id","id","brand_name");
		  $this->company_cond=" and pdd.company_id=$this->company_id";
	  }
	 
	  function get_sales_order_data_info($fromDate,$toDate,$programNo){

		 if($fromDate && $toDate){
			 $date=" and pd.program_date between '".date('d-M-Y',strtotime($fromDate))."' and '".date('d-M-Y',strtotime($toDate))."'";
		 }else{
			 $date="";
		 }
		 
		 if($programNo){
			 $program=" and pdd.dtls_id = '".$programNo."'";
		 }else{
			 $program="";
		 }
		 $rows=array();
	
		 $sql = $this->db->query("select a.buyer_id,a.job_no,a.sales_booking_no,CASE WHEN b.booking_type=1 THEN 'Non Sample Booking' WHEN b.booking_type = 4 THEN 'Sample' ELSE 'Non Sample Booking' END AS booking_type,a.booking_id,a.style_ref_no,a.booking_date,a.delivery_date,a.update_date,a.is_deleted,a.remarks, pdd.within_group ,max(pdd.update_sl) as update_sl from ppl_planning_entry_plan_dtls pdd,ppl_planning_info_entry_dtls pd, fabric_sales_order_mst a left join  wo_booking_dtls b on a.sales_booking_no=b.booking_no where a.booking_without_order in (0,2) and pdd.dtls_id=pd.id and  pdd.po_id=a.id and pdd.is_sales=1 and pdd.within_group=1   $date $program $this->company_cond  group by knitting_party,a.buyer_id,a.job_no, a.sales_booking_no, b.booking_type, a.booking_id, a.style_ref_no, a.booking_date, a.delivery_date, a.update_date,a.is_deleted,a.remarks, pdd.within_group");
		// echo $sql;die;
         foreach($sql->result() as $data){
			 
			if($data->WITHIN_GROUP==1) 
			{
				$buyer_id_data		= $this->salesorder_model->get_buyer_from_booking($data->SALES_BOOKING_NO);
				$row['BUYER_ID'] 	=$buyer_id_data->BUYER_ID;
				$row['BUYER_NAME']	=$this->buyer[$buyer_id_data->BUYER_ID];
			}
			else
			{
				$row['BUYER_ID'] =$data->BUYER_ID;
				$row['BUYER_NAME'] =$this->buyer[$data->BUYER_ID];
			}
			$row['JOB_NO'] =$data->JOB_NO;
			$row['SALES_BOOKING_NO'] =$data->SALES_BOOKING_NO;
			$row['BOOKING_TYPE'] =$data->BOOKING_TYPE;
			$row['BOOKING_ID'] =$data->BOOKING_ID;
			$row['STYLE_REF_NO'] =$data->STYLE_REF_NO;
			$row['BOOKING_DATE'] =$data->BOOKING_DATE;
			$row['DELIVERY_DATE'] =$data->DELIVERY_DATE;
			$row['UPDATE_DATE'] =$data->UPDATE_DATE;
			$row['IS_DELETED'] =$data->IS_DELETED;
			$row['REMARKS'] =$data->REMARKS;
			$row['STATUS'] =$data->UPDATE_SL;
			$rows[]=$row;
		 }
		
		 $progs=$this->get_sales_order_prog_data_info($fromDate,$toDate,$programNo);
		 $febric=$this->get_sales_order_fabric_data_info($fromDate,$toDate,$programNo);
		 $yarn=$this->get_sales_order_yarn_data_info($fromDate,$toDate,$programNo);
		 $machine=$this->get_sales_order_machine_data_info($fromDate,$toDate,$programNo);
		 $colarcuff=$this->get_sales_order_colarcuff_data_info($fromDate,$toDate,$programNo);
		 return array("Order Master Data"=>$rows,"Program Info/Plan"=>$progs,"Fabric Part"=>$febric,"Yarn Part"=>$yarn,"Machine Info/Job Card"=>$machine,"Collar_cuff details"=>$colarcuff);
	 }
	 
	 function get_sales_order_prog_data_info($fromDate,$toDate,$programNo){
		include('../includes/array_function.php');
		 if($fromDate && $toDate){
			 $date=" and pd.program_date between '".date('d-M-Y',strtotime($fromDate))."' and '".date('d-M-Y',strtotime($toDate))."'";
		 }else{
			 $date="";
		 }
		 
		 if($programNo){
			 $program=" and pdd.dtls_id = '".$programNo."'";
		 }else{
			 $program="";
		 }
		 $rows=array();
		 $sql = $this->db->query("select pdd.dtls_id,pd.program_date,pd.update_date as pd_update_date,pd.is_deleted as pd_is_deleted,pd.knitting_source,pd.knitting_party  ,a.buyer_id,a.job_no,a.sales_booking_no,CASE WHEN b.booking_type=1 THEN 'Non Sample Booking' WHEN b.booking_type = 4 THEN 'Sample' ELSE 'Non Sample Booking'
  END AS booking_type,a.booking_id,a.style_ref_no,a.booking_date,a.delivery_date,a.update_date,a.is_deleted,a.remarks, pdd.within_group from ppl_planning_entry_plan_dtls pdd,ppl_planning_info_entry_dtls pd, fabric_sales_order_mst a left join  wo_booking_dtls b on a.sales_booking_no=b.booking_no where a.booking_without_order in (0,2) and pdd.dtls_id=pd.id and  pdd.po_id=a.id and pdd.is_sales=1 and pdd.within_group=1 $date $program $this->company_cond   group by  pdd.dtls_id, pd.program_date,pd.update_date,pd.is_deleted, pd.knitting_source, pd.knitting_party,a.buyer_id,a.job_no, a.sales_booking_no,b.booking_type, a.booking_id,a.style_ref_no, a.booking_date,a.delivery_date, a.update_date,a.is_deleted,a.remarks, pdd.within_group");
  
		foreach($sql->result() as $data){
			$row['DTLS_ID'] =$data->DTLS_ID;
			$row['PROGRAM_DATE'] =$data->PROGRAM_DATE;
			$row['PD_UPDATE_DATE'] =$data->PD_UPDATE_DATE;
			$row['PD_IS_DELETED'] =$data->PD_IS_DELETED;
			$row['KNITTING_SOURCE'] =$data->KNITTING_SOURCE;
			$row['KNITTING_SOURCE_NAME'] =$knitting_source[$data->KNITTING_SOURCE];
			$row['KNITTING_PARTY'] =$data->KNITTING_PARTY;
			if($data->KNITTING_SOURCE==1){
				$row['KNITTING_PARTY_NAME'] =$this->company[$data->KNITTING_PARTY];
			}else{
				$row['KNITTING_PARTY_NAME'] =$this->supllier[$data->KNITTING_PARTY];
			}
			
			if($data->WITHIN_GROUP==1) 
			{
				$buyer_id_data		= $this->salesorder_model->get_buyer_from_booking($data->SALES_BOOKING_NO);
				$row['BUYER_ID'] 	=$buyer_id_data->BUYER_ID;
				$row['BUYER_NAME']	=$this->buyer[$buyer_id_data->BUYER_ID];
			}
			else
			{
				$row['BUYER_ID'] =$data->BUYER_ID;
				$row['BUYER_NAME'] =$this->buyer[$data->BUYER_ID];
			}
			//$row['BUYER_ID'] =$data->BUYER_ID;
			//$row['BUYER_NAME'] =$this->buyer[$data->BUYER_ID];
			$row['JOB_NO'] =$data->JOB_NO;
			$row['SALES_BOOKING_NO'] =$data->SALES_BOOKING_NO;
			$row['BOOKING_TYPE'] =$data->BOOKING_TYPE;
			$row['BOOKING_ID'] =$data->BOOKING_ID;
			$row['STYLE_REF_NO'] =$data->STYLE_REF_NO;
			$row['BOOKING_DATE'] =$data->BOOKING_DATE;
			$row['DELIVERY_DATE'] =$data->DELIVERY_DATE;
			$row['UPDATE_DATE'] =$data->UPDATE_DATE;
			$row['IS_DELETED'] =$data->IS_DELETED;
			$row['REMARKS'] =$data->REMARKS;
			
			$rows[]=$row;
		}
		
		 return $rows;
	 }
	 function get_sales_order_fabric_data_info($fromDate,$toDate,$programNo){
		 include('../includes/array_function.php');
		 if($fromDate && $toDate){
			 $date=" and pd.program_date between '".date('d-M-Y',strtotime($fromDate))."' and '".date('d-M-Y',strtotime($toDate))."'";
		 }else{
			 $date="";
		 }
		 
		 if($programNo){
			 $program=" and pdd.dtls_id = '".$programNo."'";
		 }else{
			 $program="";
		 }
		 $rows=array();
		 //$sql = $this->db->query("select sod.body_part_id,sod.color_type_id,sod.color_id, sod.fabric_desc, sod.gsm_weight, sod.dia, sod.finish_qty, sod.process_loss, sod.grey_qty, sod.pre_cost_remarks,pd.machine_gg,pd.machine_dia,pd.program_date,pd.start_date,pd.end_date,pd.remarks,pdd.dtls_id as program_no,pdd.dtls_id from ppl_planning_entry_plan_dtls pdd,ppl_planning_info_entry_dtls pd,fabric_sales_order_dtls sod, fabric_sales_order_mst a  where a.booking_without_order in (0,2) and pdd.dtls_id=pd.id and  pdd.po_id=a.id and pdd.po_id=sod.mst_id and a.id=sod.mst_id and pdd.is_sales=1 $date $program  group by sod.body_part_id,sod.color_type_id,sod.color_id, sod.fabric_desc, sod.gsm_weight, sod.dia, sod.finish_qty, sod.process_loss, sod.grey_qty, sod.pre_cost_remarks,pd.machine_gg,pd.machine_dia,pd.program_date,pd.start_date,pd.end_date,pd.remarks,pdd.dtls_id");
		 		 $sql = $this->db->query("select pdd.body_part_id,pdd.color_type_id,pd.color_id, pdd.fabric_desc, pdd.gsm_weight, pdd.dia, pdd.program_qnty as finish_qty,  pdd.program_qnty as grey_qty, pd.machine_gg,pd.machine_dia,pd.program_date,pd.start_date,pd.end_date,pd.remarks,pdd.dtls_id as program_no,pdd.dtls_id from ppl_planning_entry_plan_dtls pdd,ppl_planning_info_entry_dtls pd,fabric_sales_order_dtls sod, fabric_sales_order_mst a  where a.booking_without_order in (0,2) and pdd.dtls_id=pd.id and  pdd.po_id=a.id and pdd.po_id=sod.mst_id and a.id=sod.mst_id and pdd.is_sales=1 and pdd.within_group=1 $date $program $this->company_cond   group by pdd.body_part_id,pdd.color_type_id,pd.color_id, pdd.fabric_desc, pdd.gsm_weight, pdd.dia, pdd.program_qnty, pd.machine_gg,pd.machine_dia,pd.program_date,pd.start_date,pd.end_date,pd.remarks,pdd.dtls_id");

		 foreach($sql->result() as $data){
			$row['BODY_PART_ID'] =$data->BODY_PART_ID;
			$row['BODY_PART_NAME'] =$body_part[$data->BODY_PART_ID];
			$row['COLOR_TYPE_ID'] =$data->COLOR_TYPE_ID;
			$row['COLOR_TYPE_NAME'] =$color_type[$data->COLOR_TYPE_ID];
			$row['COLOR_ID'] =$data->COLOR_ID;
			if($data->COLOR_ID){
				$coArr=explode(",",$data->COLOR_ID);
				$colorName=array();
				for($c=0;$c<count($coArr);$c++){
				$colorName[$c] =$this->color[$coArr[$c]];
				}
				$row['COLOR_NAME'] =implode(",",$colorName);
			}else{
				$row['COLOR_NAME'] =null;
			}
			
			$fabric_type_arr=explode(",",$data->FABRIC_DESC);
			$row['FABRIC_DESC'] =$data->FABRIC_DESC;
			$row['FABRIC_TYPE'] =$fabric_type_arr[0];
			$row['GSM_WEIGHT'] =$data->GSM_WEIGHT;
			$row['DIA'] =$data->DIA;
			$row['FINISH_QTY'] =$data->FINISH_QTY;
			//$row['PROCESS_LOSS'] =$data->PROCESS_LOSS;
			$row['GREY_QTY'] =$data->GREY_QTY;
			//$row['PRE_COST_REMARKS'] =$data->PRE_COST_REMARKS;
			$row['MACHINE_GG'] =$data->MACHINE_GG;
			$row['MACHINE_DIA'] =$data->MACHINE_DIA;
			$row['PROGRAM_DATE'] =$data->PROGRAM_DATE;
			$row['START_DATE'] =$data->START_DATE;
			$row['END_DATE'] =$data->END_DATE;
			$row['REMARKS'] =$data->REMARKS;
			$row['PROGRAM_NO'] =$data->PROGRAM_NO;
			$row['DTLS_ID'] =$data->DTLS_ID;
			$rows[]=$row;
		}
		 return $rows;
	 }
	 
	  function get_sales_order_yarn_data_info($fromDate,$toDate,$programNo){
		 include('../includes/array_function.php');
		 if($fromDate && $toDate){
			 $date=" and pd.program_date between '".date('d-M-Y',strtotime($fromDate))."' and '".date('d-M-Y',strtotime($toDate))."'";
		 }else{
			 $date="";
		 }
		 
		 if($programNo){
			 $program=" and pdd.dtls_id = '".$programNo."'";
		 }else{
			 $program="";
		 }
		 $rows=array();
		// $sql = $this->db->query("select soy.yarn_count_id,soy.yarn_type,soy.yarn_dtls_id,pd.stitch_length,pdd.dtls_id as program_no,pdd.dtls_id,'' as brand,'' as lot from ppl_planning_entry_plan_dtls pdd,ppl_planning_info_entry_dtls pd,fabric_sales_order_yarn_dtls soy, fabric_sales_order_mst a  where a.booking_without_order in (0,2) and pdd.dtls_id=pd.id and  pdd.po_id=a.id and pdd.po_id=soy.mst_id and a.id=soy.mst_id and pdd.is_sales=1 $date $program  group by soy.yarn_count_id,soy.yarn_type,soy.yarn_dtls_id,pd.stitch_length,pdd.dtls_id");
		 $sql = $this->db->query("select a.yarn_count_id,a.yarn_type,pd.stitch_length,pdd.dtls_id as program_no,pdd.dtls_id,a.brand as brand,a.lot as lot, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd from ppl_planning_entry_plan_dtls pdd,ppl_planning_info_entry_dtls pd,ppl_yarn_requisition_entry soy, product_details_master a  
		 where 
		  pdd.dtls_id=pd.id and  
		  pdd.dtls_id=soy.knit_id and 
		  a.id=soy.prod_id and 
		  pdd.is_sales=1 and
		  pdd.within_group=1 $date $program $this->company_cond  group by a.yarn_count_id,a.yarn_type,pd.stitch_length,pdd.dtls_id,a.brand,a.lot, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd");
		 $composition[0]='';
		 foreach($sql->result() as $data){
			$row['YARN_COUNT_ID'] =$data->YARN_COUNT_ID;
			$row['YARN_COUNT_NAME'] =$this->yarnCount[$data->YARN_COUNT_ID];
			$row['YARN_TYPE'] =$data->YARN_TYPE;
			if($data->YARN_TYPE){

				$compos = '';
				if ($data->YARN_COMP_PERCENT2ND != 0) {
					$compos = $composition[$data->YARN_COMP_TYPE1ST] . " " . $data->YARN_COMP_PERCENT1ST . "%" . " " . $composition[$data->YARN_COMP_TYPE2ND] . " " . $data->YARN_COMP_PERCENT2ND . "%";
				} else {
					$compos = $composition[$data->YARN_COMP_TYPE1ST] . " " . $data->YARN_COMP_PERCENT1ST . "%" . " " . $composition[$data->YARN_COMP_TYPE2ND];
				}

				$row['YARN_TYPE_NAME'] =$yarn_type[$data->YARN_TYPE].",".$compos;
			}else{
				$row['YARN_TYPE_NAME'] =null;
			}
			//$row['YARN_DTLS_ID'] =$data->YARN_DTLS_ID;
			$row['STITCH_LENGTH'] =$data->STITCH_LENGTH;
			$row['PROGRAM_NO'] =$data->PROGRAM_NO;
			$row['DTLS_ID'] =$data->DTLS_ID;
			//$row['BRAND'] =$data->BRAND;
			if($data->BRAND){
				$row['BRAND'] =$this->brand_arr[$data->BRAND];
			}else{
				$row['BRAND'] =null;
			}
			$row['LOT'] =$data->LOT;
			$rows[]=$row;
		}
		 return $rows;
	 }
	 
	 function get_sales_order_machine_data_info($fromDate,$toDate,$programNo){
		 include('../includes/array_function.php');
		 if($fromDate && $toDate){
			 $date=" and pd.program_date between '".date('d-M-Y',strtotime($fromDate))."' and '".date('d-M-Y',strtotime($toDate))."'";
		 }else{
			 $date="";
		 }
		 
		 if($programNo){
			 $program=" and pdd.dtls_id = '".$programNo."'";
		 }else{
			 $program="";
		 }
		 $rows=array();
		 $sql = $this->db->query("select pdd.dtls_id as program_no,pdd.dtls_id,pm.machine_id,pm.distribution_qnty,pm.capacity,pd.knitting_source from ppl_planning_entry_plan_dtls pdd,ppl_planning_info_entry_dtls pd,ppl_planning_info_machine_dtls pm, fabric_sales_order_mst a  where a.booking_without_order in (0,2) and pdd.dtls_id=pd.id and pm.dtls_id=pd.id and  pdd.po_id=a.id and pdd.is_sales=1 and pdd.within_group=1 $date $program $this->company_cond   group by pdd.dtls_id ,pm.machine_id,pm.distribution_qnty,pm.capacity,pd.knitting_source");
		 foreach($sql->result() as $data){
			$row['PROGRAM_NO'] =$data->PROGRAM_NO;
			$row['DTLS_ID'] =$data->DTLS_ID;
			$row['MACHINE_ID'] =$data->MACHINE_ID;
			if($data->MACHINE_ID){
				$row['MACHINE_NAME'] =$this->machine[$data->MACHINE_ID];
			}else{
				$row['MACHINE_NAME'] =null;
			}
			$row['DISTRIBUTION_QNTY'] =$data->DISTRIBUTION_QNTY;
			$row['CAPACITY'] =$data->CAPACITY;
			$row['KNITTING_SOURCE'] =$data->KNITTING_SOURCE;
			$row['KNITTING_SOURCE_NAME'] =$knitting_source[$data->KNITTING_SOURCE];
			$rows[]=$row;
		}
		 return $rows;
	 }
	 
	 
	 
	 
	 function get_sales_order_colarcuff_data_info($fromDate,$toDate,$programNo){
		
		 if($fromDate && $toDate){
			 $date=" and pd.program_date between '".date('d-M-Y',strtotime($fromDate))."' and '".date('d-M-Y',strtotime($toDate))."'";
		 }else{
			 $date="";
		 }
		 
		 if($programNo){
			 $program=" and pdd.dtls_id = '".$programNo."'";
		 }else{
			 $program="";
		 }
		 $rows=array();
		 $prog=array();
		 $sql = $this->db->query("select pdd.dtls_id as program_no,pdd.dtls_id,pc.finish_size,pc.qty_pcs from ppl_planning_entry_plan_dtls pdd,ppl_planning_info_entry_dtls pd,ppl_planning_collar_cuff_dtls pc, fabric_sales_order_mst a  where a.booking_without_order in (0,2) and pdd.dtls_id=pd.id and pc.dtls_id=pd.id and  pdd.po_id=a.id and pdd.is_sales=1 and pdd.within_group=1 $date $program $this->company_cond  group by pdd.dtls_id ,pdd.dtls_id,pc.finish_size,pc.qty_pcs");
		 return $sql->result();
	 }
	 
	 function get_plan_data_info($planId){
		
		 $sql = $this->db->query("select a.*,b.knitting_party,b.knitting_source,b.color_range,b.machine_id,b.location_id ,b.color_id as fabric_color  from ppl_planning_entry_plan_dtls a,ppl_planning_info_entry_dtls   b,ppl_planning_info_entry_mst c  where b.id=$planId and c.id=b.mst_id and  b.id=a.dtls_id and a.mst_id=c.id");
		 return $sql->result();
	 }
	 
	 
	  function get_knitting_production_data_info($barcode_arr){
		//print_r($barcode_arr);die;

		$inserted_roll=array();
		$sql = $this->db->query("select  c.barcode_no from pro_roll_details c where  c.entry_form=56 and c.barcode_no in(".implode(",",$barcode_arr).") and c.status_active=1 and c.is_deleted=0 order by c.barcode_no");
		 foreach($sql->result() as $data){
			$inserted_roll[$data->BARCODE_NO] =$data->BARCODE_NO;
		 }
		 $roll_cond="";
		 if(!empty($inserted_roll)) $roll_cond=" and c.barcode_no  not in(".implode(",",$inserted_roll).")"; 

		 $sql = $this->db->query("select a.company_id, a.knitting_source ,a.knitting_company,b.prod_id,b.febric_description_id,b.body_part_id,b.gsm ,b.width ,b.uom ,b.yarn_lot ,b.yarn_count ,b.brand_id ,b.shift_name ,b.machine_no_id ,b.floor_id,b.color_range_id ,b.stitch_length ,b.color_id, c.mst_id, c.dtls_id, c.po_breakdown_id, c.id, c.roll_no, c.qc_pass_qnty, c.is_sales, c.booking_no, c.booking_without_order, c.barcode_no from inv_receive_master a,pro_grey_prod_entry_dtls b, pro_roll_details c where  a.id=b.mst_id   and b.id=c.dtls_id and a.id=c.mst_id and a.entry_form=2 and c.entry_form=2 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in(".implode(",",$barcode_arr).") $roll_cond order by c.barcode_no");
		 return $sql->result();
	 }

	  function get_buyer_from_booking($booking_no){
		
		 $sql = $this->db->query("select buyer_id  from wo_booking_mst  where booking_no='$booking_no'");
		 return $sql->row();
	 }
	 
	 
	 
	
	 
}
