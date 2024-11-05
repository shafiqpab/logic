<?php
class Pre_cost extends CI_Model {

	function __construct() {
		parent::__construct();
		error_reporting(0);
	}
  


	function get_costing_per($job_no){
		$costingPerArr=sql_select( "select a.JOB_NO,b.COSTING_PER from  wo_po_details_master a, wo_pre_cost_mst b where 1=1 and a.job_no='".$job_no."' and a.id=b.job_id", '', '');
		foreach($costingPerArr as $costingPerArrRow)
		{
			$costingPer=$costingPerArrRow->COSTING_PER;
			$costingPerQty=12;
			if($costingPer==1){
			$costingPerQty=12	;
			}
			elseif($costingPer==2){
			$costingPerQty=1;	
			}
			elseif($costingPer==3){
			$costingPerQty=24	;
			}
			elseif($costingPer==4){
			$costingPerQty=36	;
			}
			elseif($costingPer==5){
			$costingPerQty=48;
			}
			else{
			$costingPerQty=12;
			}
			return $costingPerQty;	
		}
	}



	public  function get_gmtsitem_ratio($job_no){
		$gmtsitemRatioSql=sql_select("select a.JOB_NO,b.GMTS_ITEM_ID ,b.SET_ITEM_RATIO from wo_po_details_master a, wo_po_details_mas_set_details b where 1=1 and a.job_no='".$job_no."' and a.id=b.job_id",'','');
		$gmtsitemRatioArray=array();
		foreach($gmtsitemRatioSql as $gmtsitemRatioSqlRow)
		{
			$gmtsitemRatioArray[$gmtsitemRatioSqlRow->JOB_NO][$gmtsitemRatioSqlRow->GMTS_ITEM_ID]=$gmtsitemRatioSqlRow->SET_ITEM_RATIO;	
		}
		return $gmtsitemRatioArray;
	}

	public  function get_knit_gray_finish_fabric_qty_by_order($po_id){
		
	$sql='SELECT a.job_no AS "job_no",
       b.id                          AS "id",
       c.item_number_id              AS "item_number_id",
       c.country_id                  AS "country_id",
       c.cutup_date                  AS "cutup_date",
       c.color_number_id             AS "color_number_id",
       c.size_number_id              AS "size_number_id",
       c.order_quantity              AS "order_quantity",
       c.plan_cut_qnty               AS "plan_cut_qnty",
       d.id                          AS "pre_cost_dtls_id",
       d.body_part_id                AS "body_part_id",
       d.fab_nature_id               AS "fab_nature_id",
       d.fabric_source               AS "fabric_source",
       d.rate                        AS "drate",
       d.lib_yarn_count_deter_id     AS "lib_yarn_count_deter_id",
       d.uom                         AS "uom",
       e.dia_width                   AS "dia_width",
       e.remarks                     AS "remarks",
       e.cons                        AS "cons",
       e.requirment                  AS "requirment",
       e.rate                        AS "rate"
  FROM wo_po_details_master            a,
       wo_po_break_down                b,
       wo_po_color_size_breakdown      c,
       wo_pre_cost_fabric_cost_dtls    d,
       wo_pre_cos_fab_co_avg_con_dtls  e
 WHERE     1 = 1
       AND b.id in ('.$po_id.')
       AND a.id = b.job_id
       AND a.id = c.job_id
       AND a.id = d.job_id
       AND a.id = e.job_id
       AND b.id = c.po_break_down_id
       AND d.id = e.pre_cost_fabric_cost_dtls_id
       AND c.po_break_down_id = e.po_break_down_id
       AND c.item_number_id = d.item_number_id
       AND c.color_number_id = e.color_number_id
       AND c.size_number_id = e.gmts_sizes
       AND e.cons != 0
       AND a.is_deleted = 0
       AND a.status_active = 1
       AND b.is_deleted = 0
       AND b.status_active = 1
       AND c.is_deleted = 0
       AND c.status_active = 1
       AND d.is_deleted = 0
       AND d.status_active = 1
       AND e.is_deleted = 0
       AND e.status_active = 1';
		//return $sql;
		$sql_result_arr=sql_select($sql);
	   
		$reqyarnqntyArr=array();
		foreach($sql_result_arr as $row)
		{ 
		   if($row->fab_nature_id==2){
			   $costingPerQty=$this->get_costing_per($row->job_no);
			   $set_item_ratio=$this->get_gmtsitem_ratio($row->job_no);
			   
			   $reqyarnqntyArr['knit']['finish'] +=($row->plan_cut_qnty/$set_item_ratio[$row->job_no][$row->item_number_id])*($row->cons/$costingPerQty);
			   
			   $reqyarnqntyArr['knit']['gray'] +=($row->plan_cut_qnty/$set_item_ratio[$row->job_no][$row->item_number_id])*($row->requirment/$costingPerQty);
			   
		   }
		   else if($row->fab_nature_id==3){
			   $costingPerQty=$this->get_costing_per($row->job_no);
			   $set_item_ratio=$this->get_gmtsitem_ratio($row->job_no);
			   
			   $reqyarnqntyArr['woven']['finish'] +=($row->plan_cut_qnty/$set_item_ratio[$row->job_no][$row->item_number_id])*($row->cons/$costingPerQty);
			   $reqyarnqntyArr['woven']['gray'] +=($row->plan_cut_qnty/$set_item_ratio[$row->job_no][$row->item_number_id])*($row->requirment/$costingPerQty);
		   }

		}
	   
	   return $reqyarnqntyArr;
	   
	   
	}











}
