<?php
class Daily_ex_factory_report extends CI_Model {

	function __construct() {
		error_reporting(0);
		parent::__construct();
	}

	
	
	public function get_daily_ex_factory_report($company_id = 0, $start_date = "", $end_date = "") {
		
		if ($this->db->dbdriver == 'mysqli') {
			$start_date = date("Y-m-d", strtotime($start_date));
			$end_date = date("Y-m-d", strtotime($end_date));
		} else {
			$start_date = date("d-M-Y", strtotime($start_date));
			$end_date = date("d-M-Y", strtotime($end_date));
		}

		
		$master_data=array();
		$sy = date('Y',strtotime($start_date));
		$basic_smv_arr=return_library_array( "select comapny_id, basic_smv from lib_capacity_calc_mst where year=$sy and comapny_id=$company_id",'comapny_id','basic_smv');
		
		
		if($start_date!="" && $end_date!="")
		{
			$str_cond="and a.ex_factory_date between '$start_date' and  '$end_date ' ";
		}
		$sql= "SELECT b.ID AS PO_ID,c.SET_SMV,c.BUYER_NAME,C.COMPANY_NAME, SUM(CASE WHEN a.ENTRY_FORM!=85 THEN a.EX_FACTORY_QNTY ELSE 0 END) AS EX_FACTORY_QNTY, SUM(a.TOTAL_CARTON_QNTY) AS CARTON_QNTY,(b.PO_QUANTITY*c.TOTAL_SET_QNTY) AS PO_QUANTITY, (b.UNIT_PRICE/c.TOTAL_SET_QNTY) AS UNIT_PRICE from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name=$company_id $str_cond  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,3) and c.is_deleted=0 and c.status_active=1 group by C.COMPANY_NAME,c.set_smv,c.buyer_name,b.id,b.unit_price,c.total_set_qnty,b.po_quantity";
		$total_po_val=0;
		$sql_result=sql_select($sql);
		foreach($sql_result as $row)
		{
			$po_id_arr[$row->PO_ID]=$row->PO_ID;
		}
		
		if($this->db->dbdriver != 'mysqli' && count($po_id_arr)>999)
		{
			$sql_con=" and (";
			$chunk_arr=array_chunk($po_id_arr,999);
			foreach($chunk_arr as $ids)
			{
				$sql_con.=" PO_BREAK_DOWN_ID in(".implode(",",$ids).") or";
			}
			$sql_con=chop($sql_con,'or');
			$sql_con.=")";
		}
		else
		{
			$sql_con=" and PO_BREAK_DOWN_ID in(".implode(",",$po_id_arr).")";
		}
		
		$exfact_sql=sql_select("SELECT PO_BREAK_DOWN_ID,
		SUM(CASE WHEN ENTRY_FORM!=85 THEN EX_FACTORY_QNTY ELSE 0 END) AS EX_FACTORY_QNTY,
		SUM(CASE WHEN ENTRY_FORM=85 THEN EX_FACTORY_QNTY ELSE 0 END) AS EX_FACTORY_RETURN_QNTY, 
		SUM(TOTAL_CARTON_QNTY) AS CARTON_QNTY FROM PRO_EX_FACTORY_MST WHERE STATUS_ACTIVE=1 AND IS_DELETED=0 $sql_con GROUP BY PO_BREAK_DOWN_ID");
		$exfact_qty_arr=$exfact_return_qty_arr=array();
		foreach($exfact_sql as $row)
		{
			$exfact_qty_arr[$row->PO_BREAK_DOWN_ID]=$row->EX_FACTORY_QNTY - $row->EX_FACTORY_RETURN_QNTY;
			$exfact_return_qty_arr[$row->PO_BREAK_DOWN_ID]=$row->EX_FACTORY_RETURN_QNTY;
		}
		
		$master_data=array();
		foreach($sql_result as $row)
		{
			$total_ex_fact_qty=$exfact_qty_arr[$row->PO_ID];
			$basic_qnty=($total_ex_fact_qty*$row->SET_SMV)/$basic_smv_arr[$row->COMPANY_NAME];
							
			$master_data[$row->BUYER_NAME]['buyer_id']=$row->BUYER_NAME;	
			$master_data[$row->BUYER_NAME]['po_qnty'] +=$row->PO_QUANTITY;
			$master_data[$row->BUYER_NAME]['po_value'] +=$row->PO_QUANTITY*$row->UNIT_PRICE;
			$master_data[$row->BUYER_NAME]['basic_qnty'] +=$basic_qnty;
			$master_data[$row->BUYER_NAME]['ex_factory_qnty'] +=$row->EX_FACTORY_QNTY-$exfact_return_qty_arr[$row->PO_ID];
			$master_data[$row->BUYER_NAME]['ex_factory_value'] +=($row->EX_FACTORY_QNTY-$exfact_return_qty_arr[$row->PO_ID])*$row->UNIT_PRICE;
			$master_data[$row->BUYER_NAME]['total_ex_fact_qty'] +=$total_ex_fact_qty;
			$master_data[$row->BUYER_NAME]['total_ex_fact_value'] +=$total_ex_fact_qty*$row->UNIT_PRICE;
		
			$total_po_val+=$row->PO_QUANTITY*$row->UNIT_PRICE;
		
		} 
		
		foreach($master_data as $rows)
		{
			$apiDataArr[]=array(
				BUYER_NAME=>$rows[buyer_id],
				PO_QTY=>$rows[po_qnty],
				PO_VAL=>$rows[po_value],
				PO_VAL_PER=>number_format(($rows[po_value]/$total_po_val)*100,2,'.',''),
				CURR_EX_FACT_QTY=>$rows[ex_factory_qnty],
				CURR_EX_FACT_VAL=>$rows[ex_factory_value],
				TOT_EX_FACT_QTY=>$rows[total_ex_fact_qty],
				TOT_EX_FACT_VAL=>$rows[total_ex_fact_value],
				TOT_EX_FACT_BASIC_QTY=>$rows[basic_qnty],
				TOT_EX_FACT_VAL_PER=>number_format(($rows[total_ex_fact_value]/$rows[po_value])*100,2)
			);
		
		}
		return $apiDataArr;
	}
	

}
