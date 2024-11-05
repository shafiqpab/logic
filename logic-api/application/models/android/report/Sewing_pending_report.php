<?php
class Sewing_pending_report extends CI_Model {

	function __construct() {
		error_reporting(0);
		parent::__construct();
	}

	public function get_sewing_pending($company_id=0,$location_id=0,$floor_id=0,$line_id=0,$start_date='',$end_date='') {

		if ($start_date != "" && $end_date != "") {
			if ($this->db->dbdriver == 'mysqli') {
				$start_date = date("d-M-Y", strtotime($start_date));
				$end_date = date("d-M-Y", strtotime($end_date));
				$where_con="and a.PRODUCTION_DATE between '$start_date' and  '$end_date'";
			} else {
				$start_date = date("d-M-Y", strtotime($start_date));
				$end_date = date("d-M-Y", strtotime($end_date));
				$where_con="and a.PRODUCTION_DATE between '$start_date' and  '$end_date'";

			}
		}

		if($company_id>0){$where_con .=" and a.SERVING_COMPANY=$company_id";}
		if($location_id>0){$where_con .=" and a.LOCATION=$location_id";}
		if($floor_id>0){$where_con .=" and a.FLOOR_ID=$floor_id";}
		if($line_id>0){$where_con .=" and a.SEWING_LINE=$line_id";}

		$input_sql = "SELECT c.JOB_NO_MST as JOB_NO,c.PO_NUMBER,a.PO_BREAK_DOWN_ID,b.BARCODE_NO,b.BUNDLE_NO,b.CUT_NO,b.PRODUCTION_QNTY from PRO_GARMENTS_PRODUCTION_MST a,PRO_GARMENTS_PRODUCTION_DTLS b,WO_PO_BREAK_DOWN c where a.id=b.mst_id and a.PO_BREAK_DOWN_ID=c.id and a.PRODUCTION_TYPE=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.production_type=4 and b.BARCODE_NO is not null $where_con";
		$input_data_result = sql_select($input_sql);
		$sewing_input_barcode_arr=array(0=>0);
		foreach ($input_data_result as $row){
			$input_data_arr[$row->BARCODE_NO]=$row;
			$sewing_input_barcode_arr[$row->BARCODE_NO]=$row->BARCODE_NO;
			$sewing_input_barcode_qty_arr[$row->BARCODE_NO]+=$row->PRODUCTION_QNTY;
		}


		if($this->db->dbdriver != 'mysqli' && count($sewing_input_barcode_arr)>1000)
		{
			$barcode_con=" and (";
			$chunk_arr=array_chunk($sewing_input_barcode_arr,999);
			foreach($chunk_arr as $ids)
			{
				$barcode_con.=" b.BARCODE_NO in(".implode(",",$ids).") or";
			}
			$barcode_con=chop($barcode_con,'or');
			$barcode_con.=")";
		}
		else
		{
			$barcode_con=" and b.BARCODE_NO in(".implode(",",$sewing_input_barcode_arr).")";
		}


		if(count($sewing_input_barcode_arr)>1){
			$output_sql = "SELECT BARCODE_NO,sum(PRODUCTION_QNTY) as PRODUCTION_QNTY from PRO_GARMENTS_PRODUCTION_MST a,PRO_GARMENTS_PRODUCTION_DTLS b where a.id=b.mst_id and a.PRODUCTION_TYPE=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.production_type=5 $barcode_con group by BARCODE_NO";
			$output_data_result = sql_select($output_sql);

			foreach ($output_data_result as $row){
				if(($sewing_input_barcode_qty_arr[$row->BARCODE_NO]- $row->PRODUCTION_QNTY) <1 ){
					unset($input_data_arr[$row->BARCODE_NO]);
				}
				$sewing_output_barcode_qty_arr[$row->BARCODE_NO]=$row->PRODUCTION_QNTY;
			}
		}

		$pending_input_data_arr = array();
		foreach ($input_data_arr as $row){
			$pending_input_data_arr['sewing_output_pending'][]=array(
				JOB_NO=>$row->JOB_NO,
				PO_NUMBER=>$row->PO_NUMBER,
				PO_BREAK_DOWN_ID=>$row->PO_BREAK_DOWN_ID,
				BARCODE_NO=>$row->BARCODE_NO,
				BUNDLE_NO=>$row->BUNDLE_NO,
				CUT_NO=>$row->CUT_NO,
				PRODUCTION_QNTY=>($row->PRODUCTION_QNTY-$sewing_output_barcode_qty_arr[$row->BARCODE_NO]),
			);
		}

		return $pending_input_data_arr;

	
	}
	

}
