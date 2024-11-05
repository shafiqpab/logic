<?php
class Pending_shipment_monitoring_report extends CI_Model {

	function __construct() {
		error_reporting(0);
		parent::__construct();
	}

	public function get_pending_shipment_monitoring_report($company_id=0,$start_date='',$end_date='') {

		if ($start_date != "" && $end_date != "") {
			if ($this->db->dbdriver == 'mysqli') {
				$start_date = date("d-M-Y", strtotime($start_date));
				$end_date = date("d-M-Y", strtotime($end_date));
				$where_con="and b.PUB_SHIPMENT_DATE between '$start_date' and  '$end_date'";
			} else {
				$start_date = date("d-M-Y", strtotime($start_date));
				$end_date = date("d-M-Y", strtotime($end_date));
				$where_con="and b.PUB_SHIPMENT_DATE between '$start_date' and  '$end_date'";
			}
		}

		if($company_id>0){$where_con .=" and a.COMPANY_NAME=$company_id";}

		$sql = "SELECT a.COMPANY_NAME, b.ID, b.SHIPING_STATUS, b.PO_QUANTITY, a.TOTAL_SET_QNTY, b.PLAN_CUT, (b.UNIT_PRICE/a.TOTAL_SET_QNTY) AS ORDER_RATE, b.PUB_SHIPMENT_DATE FROM WO_PO_DETAILS_MASTER a, WO_PO_BREAK_DOWN b WHERE a.JOB_NO=b.JOB_NO_MST AND b.IS_CONFIRMED=1 AND a.STATUS_ACTIVE=1 AND a.IS_DELETED=0 AND b.STATUS_ACTIVE=1 and b.IS_DELETED=0 AND b.SHIPING_STATUS!=3 $where_con";

		$sql_result=sql_select($sql);
		$partial_ex_factory=$company_order_qnty=array();
		foreach ($sql_result as $row) 
		{
	        if ($row->SHIPING_STATUS == 2) {
	            $buyer_ex_quantity = 0;
	            $partial_ex_factory[$row->ID] = $row->ID;
	        }

	        $po_quantity = $row->PO_QUANTITY * $row->TOTAL_SET_QNTY;
	        if (date("Y-m-d", strtotime($row->PUB_SHIPMENT_DATE)) == date("Y-m-d", strtotime($end_date))) 
	        {
	            $company_order_qnty[$row->ID]['order_qnty'] = $po_quantity;
	            $company_order_qnty[$row->ID]['order_rate'] = $row->ORDER_RATE;
	            $company_order_qnty[$row->ID]['company_name'] = $row->COMPANY_NAME;
	        }

	    }

	    if (count($partial_ex_factory) > 0)
	    {
	    	$po_id_cond = implode(',',$partial_ex_factory);
	    	$sql_ex_factory = "SELECT PO_BREAK_DOWN_ID, sum(EX_FACTORY_QNTY) AS EX_FACTORY_QNTY from PRO_EX_FACTORY_MST WHERE PO_BREAK_DOWN_ID IN ($po_id_cond) AND STATUS_ACTIVE=1 AND IS_DELETED=0 GROUP BY PO_BREAK_DOWN_ID";
	    	$sql_ex_factory_res = sql_select($sql_ex_factory);
	    	$ex_fact_qty_arr=array();
	    	foreach($sql_ex_factory_res as $row)
			{
				$ex_fact_qty_arr[$row->PO_BREAK_DOWN_ID] = $row->EX_FACTORY_QNTY;
			}
	    }	

	    foreach ($company_order_qnty as $poid => $value) 
	    {
	        $value['order_qnty'] = $value['order_qnty'] - $ex_fact_qty_arr[$poid];

	        if ($company_order_qnty[$poid]['order_qnty'] != '') 
	        {
	            $company_order_qnty_sum[$value['company_name']]['order_qnty'] += $value['order_qnty'];
	            $company_order_qnty_sum[$value['company_name']]['order_val'] += $value['order_qnty'] * $value['order_rate'];
	        }
	    }
	    $tot_po_val=$tot_po_qnty=0;
		$apiDataArr = array();
		foreach ($company_order_qnty_sum as $row){
			$apiDataArr[]=array(
				PO_VAL=>$row[order_val],
				PO_QTY=>$row[order_qnty],
				FOB=>$row[order_val]/$row[order_qnty],			
			);
		}
		return $apiDataArr;	
	}	

}
