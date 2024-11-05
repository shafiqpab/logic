<?php
class Production_process_by_barcode_report extends CI_Model {

	function __construct() {
		error_reporting(0);
		parent::__construct();
	}

	public function get_production_process_by_barcode_report($barcode_no=''){
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
		
		$color_arr = return_library_array("select id, color_name from lib_color", "id", "color_name");
		$size_arr = return_library_array("select id, size_name from lib_size", 'id', 'size_name');
		$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name"); 
		$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');



		$cutSql="select a.CUTTING_NO,a.JOB_NO,a.ENTRY_DATE,b.COLOR_ID,b.ORDER_QTY,c.ORDER_ID,c.BARCODE_NO,c.SIZE_ID,c.NUMBER_START,c.NUMBER_END,c.SIZE_QTY,d.STYLE_REF_NO,e.PO_NUMBER,e.id as PO_ID from PPL_CUT_LAY_MST a,PPL_CUT_LAY_DTLS b,PPL_CUT_LAY_BUNDLE c,WO_PO_DETAILS_MASTER d,WO_PO_BREAK_DOWN e where  a.id=b.mst_id and b.mst_id=c.mst_id and b.id=c.DTLS_ID and a.JOB_NO=d.job_no and d.job_no=e.job_no_mst and c.BARCODE_NO='$barcode_no'";
		$cutSqlResult = sql_select($cutSql);
		$dataArr=array();
			$dataArr['order_info']['job_no']='';
			$dataArr['order_info']['order_no']='';
			$dataArr['order_info']['style_no']='';
			$dataArr['order_info']['color_name']='';
			$dataArr['order_info']['order_qty']=0;
			
			$dataArr['cut_and_lay_info']['cut_no']='';
			$dataArr['cut_and_lay_info']['date_and_time']='';
			$dataArr['cut_and_lay_info']['color']='';
			$dataArr['cut_and_lay_info']['size']='';
			$dataArr['cut_and_lay_info']['rmg_number']='';
			$dataArr['cut_and_lay_info']['qty']=0;
		
		
		$order_no_arr=array();$order_qty_arr=array();
		foreach ($cutSqlResult as $row){
			$order_no_arr[$row->PO_NUMBER]=$row->PO_NUMBER;
			$order_qty_arr[$row->PO_ID]=$row->ORDER_QTY;

			$dataArr['order_info']['job_no']=$row->JOB_NO;
			$dataArr['order_info']['style_no']=$row->STYLE_REF_NO;
			$dataArr['order_info']['color_name']=$color_arr[$row->COLOR_ID];
			
			$dataArr['cut_and_lay_info']['cut_no']=$row->CUTTING_NO;
			$dataArr['cut_and_lay_info']['date_and_time']=$row->ENTRY_DATE;
			$dataArr['cut_and_lay_info']['color']=$color_arr[$row->COLOR_ID];
			$dataArr['cut_and_lay_info']['size']=$size_arr[$row->SIZE_ID];
			$dataArr['cut_and_lay_info']['rmg_number']=$row->NUMBER_START.'-'.$row->NUMBER_END;
			$dataArr['cut_and_lay_info']['qty']=$row->SIZE_QTY;
			
		}
		
		$dataArr['order_info']['order_no']=implode(',',$order_no_arr);
		$dataArr['order_info']['order_qty']=array_sum($order_qty_arr);
		
		
		
		
	/*$production_type=array(1=>"Cutting",2=>"Printing",3=>"Print Received",4=>"Sweing In",5=>"Sewing Out",6=>"Finish Input",7=>"Iron Output",8=>"Gmts Finish",9=>"Cutting Delivery",10=>"Finish Garments Order to Order transfer",11=>"Poly Entry",
12=>"Sewing Line input",13=>"Sewing Line Output",40 => "Plan Cut",50 => "Bundle Issue to Knitting Floor", 51 => "Bundle Receive from Knitting Floor", 52 => "Knitting QC", 53 => "Bundle issue to Linking ", 54 => "Bundle receive in Linking", 55 => "Bundle Wise Linking Input", 56 => "Bundle Wise Linking Output", 57 => "Delivery to Wash", 58 => "Receive in Wash", 59 => "Batch Creation for Wash", 60 => "Recipe for Wash", 61 => "Wash Chemical Issue Requisition", 62 => "Wash Production Entry (QC Passed)", 63 => "Embellishment Issue", 64 => "Embellishment Receive", 65=> "Re-linking", 66 => "Special Operation", 67 => "Iron entry", 68 => "Poly entry", 69 => "Packing and Finishing", 70 => "Final Inspection", 71 => "Ex-factory", 72 => "Operation wise entry", 73 => "Linking QC", 74 => "Lot Ratio", 75 => "Linking Operation Track");*/
	

		$dataArr['cutting_qc_info']['cutting_qc_id']='';
		$dataArr['cutting_qc_info']['date_and_time']='';
		$dataArr['cutting_qc_info']['bundle_qty']= 0;
		$dataArr['cutting_qc_info']['qc_pass_qty']=0;
		$dataArr['cutting_qc_info']['reject_qty']=0;
		$dataArr['cutting_qc_info']['replace_qty']=0;

		$dataArr['print_issue_info']['issue_id']='';
		$dataArr['print_issue_info']['date_and_time']='';
		$dataArr['print_issue_info']['issue_qty']=0;

		$dataArr['print_receive_info']['issue_id']='';
		$dataArr['print_receive_info']['date_and_time']='';
		$dataArr['print_receive_info']['issue_qty']=0;
		$dataArr['print_receive_info']['reject_qty']=0;

		$dataArr['embroidery_issue_info']['issue_id']='';
		$dataArr['embroidery_issue_info']['date_and_time']='';
		$dataArr['embroidery_issue_info']['issue_qty']=0;

		$dataArr['embroidery_receive_info']['issue_id']='';
		$dataArr['embroidery_receive_info']['date_and_time']='';
		$dataArr['embroidery_receive_info']['issue_qty']=0;
		$dataArr['embroidery_receive_info']['reject_qty']=0;

		$dataArr['sewing_input_info']['input_id']='';
		$dataArr['sewing_input_info']['date_and_time']='';
		$dataArr['sewing_input_info']['input_qty']=0;
		$dataArr['sewing_input_info']['line_no']='';
		$dataArr['sewing_input_info']['line_id']='';

		$dataArr['sewing_output_info']['output_id']='';
		$dataArr['sewing_output_info']['date_and_time']='';
		$dataArr['sewing_output_info']['output_qty']=0;
		$dataArr['sewing_output_info']['Alter_spot_reject_qty']='';

		$dataArr['line_input_info']['line_input_id']='';
		$dataArr['line_input_info']['date_and_time']='';
		$dataArr['line_input_info']['line_no']='';
		$dataArr['line_input_info']['line_id']='';

		$dataArr['line_output_info']['line_output_id']='';
		$dataArr['line_output_info']['date_and_time']='';
		$dataArr['line_output_info']['Qty']=0;
		
		
	//PRO_GMTS_CUTTING_QC_MST	d.CUTTING_QC_NO,
		
		
		$proSql="select a.PRODUCTION_DATE,a.PO_BREAK_DOWN_ID,a.SEWING_LINE,a.EMBEL_NAME,a.EMBEL_TYPE,a.PRODUCTION_DATE,a.PROD_RESO_ALLO,b.BUNDLE_QTY,b.PRODUCTION_QNTY,b.REJECT_QTY,b.REPLACE_QTY,b.ALTER_QTY,b.SPOT_QTY,b.PRODUCTION_TYPE,c.SYS_NUMBER,d.CUTTING_QC_NO from PRO_GARMENTS_PRODUCTION_MST a,PRO_GMTS_DELIVERY_MST c,PRO_GARMENTS_PRODUCTION_DTLS b 
		left join PRO_GMTS_CUTTING_QC_MST d on d.id=b.DELIVERY_MST_ID
		
		where a.id=b.mst_id and c.id=a.DELIVERY_MST_ID and c.id=b.DELIVERY_MST_ID and b.BARCODE_NO = '$barcode_no'";
		$proSqlResult = sql_select($proSql);
		foreach ($proSqlResult as $row){
			
			
			$line_name='';
			if($row->PROD_RESO_ALLO==1)
			{
				$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$row->SEWING_LINE]);
			
				$line_name_arr=array();
				foreach($line_resource_mst_arr as $resource_id)
				{
					$line_name_arr[$resource_id]=$lineArr[$resource_id];
				}
				$line_name=implode(',',$line_name_arr);
			
			}
			else{
				$line_name=$lineArr[$row->SEWING_LINE];
			}
			
			
			if($row->PRODUCTION_TYPE==1){
				$dataArr['cutting_qc_info']['cutting_qc_id']=$row->CUTTING_QC_NO ;
				$dataArr['cutting_qc_info']['date_and_time']=$row->PRODUCTION_DATE;
				$dataArr['cutting_qc_info']['bundle_qty']= $dataArr['cut_and_lay_info']['qty'];
				$dataArr['cutting_qc_info']['qc_pass_qty']=$row->PRODUCTION_QNTY;
				$dataArr['cutting_qc_info']['reject_qty']=$row->REJECT_QTY;
				$dataArr['cutting_qc_info']['replace_qty']=$row->REPLACE_QTY;
			}
			else if($row->PRODUCTION_TYPE==2 && $row->EMBEL_NAME==1){
				$dataArr['print_issue_info']['issue_id']=$row->SYS_NUMBER;
				$dataArr['print_issue_info']['date_and_time']=$row->PRODUCTION_DATE;
				$dataArr['print_issue_info']['issue_qty']=$row->PRODUCTION_QNTY;
			}
			else if($row->PRODUCTION_TYPE==3 && $row->EMBEL_NAME==1){
				$dataArr['print_receive_info']['issue_id']=$row->SYS_NUMBER;
				$dataArr['print_receive_info']['date_and_time']=$row->PRODUCTION_DATE;
				$dataArr['print_receive_info']['issue_qty']=$row->PRODUCTION_QNTY;
				$dataArr['print_receive_info']['reject_qty']=$row->REJECT_QTY;
			}
			
			
			if($row->PRODUCTION_TYPE==2 && $row->EMBEL_NAME==2){
				$dataArr['embroidery_issue_info']['issue_id']=$row->SYS_NUMBER;
				$dataArr['embroidery_issue_info']['date_and_time']=$row->PRODUCTION_DATE;
				$dataArr['embroidery_issue_info']['issue_qty']=$row->PRODUCTION_QNTY;
			}
			else if($row->PRODUCTION_TYPE==3 && $row->EMBEL_NAME==2){
				$dataArr['embroidery_receive_info']['issue_id']=$row->SYS_NUMBER;
				$dataArr['embroidery_receive_info']['date_and_time']=$row->PRODUCTION_DATE;
				$dataArr['embroidery_receive_info']['issue_qty']=$row->PRODUCTION_QNTY;
				$dataArr['embroidery_receive_info']['reject_qty']=$row->REJECT_QTY;
			}
			else if($row->PRODUCTION_TYPE==4){
				$dataArr['sewing_input_info']['input_id']=$row->SYS_NUMBER;
				$dataArr['sewing_input_info']['date_and_time']=$row->PRODUCTION_DATE;
				$dataArr['sewing_input_info']['input_qty']=$row->PRODUCTION_QNTY;
				$dataArr['sewing_input_info']['line_no']=$line_name;
				$dataArr['sewing_input_info']['line_id']=$row->SEWING_LINE;
			}
			else if($row->PRODUCTION_TYPE==5){
				$dataArr['sewing_output_info']['output_id']=$row->SYS_NUMBER;
				$dataArr['sewing_output_info']['date_and_time']=$row->PRODUCTION_DATE;
				$dataArr['sewing_output_info']['output_qty']=$row->PRODUCTION_QNTY;
				$dataArr['sewing_output_info']['Alter_spot_reject_qty']=$row->ALTER_QTY.'/'.$row->SPOT_QTY.'/'.$row->REJECT_QTY;
			}
			else if($row->PRODUCTION_TYPE==12){
				$dataArr['line_input_info']['line_input_id']=$row->SYS_NUMBER;
				$dataArr['line_input_info']['date_and_time']=$row->PRODUCTION_DATE;
				$dataArr['line_input_info']['line_no']=$line_name;
				$dataArr['line_input_info']['line_id']=$row->SEWING_LINE;
			}
			else if($row->PRODUCTION_TYPE==13){
				$dataArr['line_output_info']['line_output_id']=$row->SYS_NUMBER;
				$dataArr['line_output_info']['date_and_time']=$row->PRODUCTION_DATE;
				$dataArr['line_output_info']['Qty']=$row->PRODUCTION_QNTY;
			}
			
		}
		
		return $dataArr;
	
	}
	
	
	

}
