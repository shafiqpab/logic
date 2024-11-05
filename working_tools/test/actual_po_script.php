<?
header('Content-type:text/html; charset=utf-8');
session_start();
require_once('../../includes/common.php');
$con=connect();
 
$sel_sql="SELECT id,job_no,po_break_down_id ,acc_po_no, acc_po_qty ,inserted_by ,insert_date,updated_by ,update_date ,status_active ,is_deleted ,acc_ship_date,gmts_item,gmts_color_id,country_id,gmts_size_id from wo_po_acc_po_info_bk where PO_BREAK_DOWN_ID is not null order by id ASC,status_active ASC";

//and to_char(insert_date,'MM')=08 
//$sel_sql="SELECT id,job_no,po_break_down_id ,acc_po_no, acc_po_qty ,inserted_by ,insert_date,updated_by ,update_date ,status_active ,is_deleted ,acc_ship_date,gmts_item,gmts_color_id,country_id,gmts_size_id from wo_po_acc_po_info_bk where status_active=1 and is_deleted=0 and PO_BREAK_DOWN_ID is not null and job_no='KAL-22-00489' order by id ASC";
 $previous_data=sql_select($sel_sql);
 $mst_id = return_next_id( "id", "wo_po_acc_po_info", 1 );
 //$dtls_id = return_next_id( "id", "wo_po_acc_po_info_dtls", 1 );
 $mst_field_array="id,job_no,job_id,po_break_down_id,acc_po_no,acc_rcv_date,acc_ship_date,acc_ship_mode,acc_po_status,acc_po_qty,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted";
 $dtls_field_array="id,mst_id,po_break_down_id,country_id,gmts_item,gmts_color_id,gmts_size_id,po_qty,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted";
 $job_id_arr=return_library_array( "select id, job_no from wo_po_details_master",'job_no','id');
 foreach($previous_data as $row)
 {
	$key=$row[csf('acc_po_no')]."**".$row[csf('acc_ship_date')];
	$previous_data_arr[$row[csf('po_break_down_id')]][$key]['job_no']=$row[csf('job_no')];
	$previous_data_arr[$row[csf('po_break_down_id')]][$key]['job_id']=$job_id_arr[$row[csf('job_no')]];
	$previous_data_arr[$row[csf('po_break_down_id')]][$key]['acc_po_no']=$row[csf('acc_po_no')];
	if($row[csf('is_deleted')]==0){
		$previous_data_arr[$row[csf('po_break_down_id')]][$key]['acc_po_qty']+=$row[csf('acc_po_qty')];
	}	
	$previous_data_arr[$row[csf('po_break_down_id')]][$key]['acc_ship_date'] =$row[csf('acc_ship_date')];
	$previous_data_arr[$row[csf('po_break_down_id')]][$key]['inserted_by'] =$row[csf('inserted_by')];
	$previous_data_arr[$row[csf('po_break_down_id')]][$key]['insert_date'] =$row[csf('insert_date')];
	$previous_data_arr[$row[csf('po_break_down_id')]][$key]['updated_by'] =$row[csf('updated_by')];
	$previous_data_arr[$row[csf('po_break_down_id')]][$key]['update_date'] =$row[csf('update_date')];
	$previous_data_arr[$row[csf('po_break_down_id')]][$key]['status_active'] =$row[csf('status_active')];
	$previous_data_arr[$row[csf('po_break_down_id')]][$key]['is_deleted'] =$row[csf('is_deleted')];

	$previous_data_arr[$row[csf('po_break_down_id')]][$key]['dtls_data'][$row[csf('id')]]['country_id'] =$row[csf('country_id')];
	$previous_data_arr[$row[csf('po_break_down_id')]][$key]['dtls_data'][$row[csf('id')]]['gmts_item'] =$row[csf('gmts_item')];
	$previous_data_arr[$row[csf('po_break_down_id')]][$key]['dtls_data'][$row[csf('id')]]['gmts_color_id'] =$row[csf('gmts_color_id')];
	$previous_data_arr[$row[csf('po_break_down_id')]][$key]['dtls_data'][$row[csf('id')]]['gmts_size_id'] =$row[csf('gmts_size_id')];
	$previous_data_arr[$row[csf('po_break_down_id')]][$key]['dtls_data'][$row[csf('id')]]['po_qty'] =$row[csf('acc_po_qty')];
	$previous_data_arr[$row[csf('po_break_down_id')]][$key]['dtls_data'][$row[csf('id')]]['inserted_by'] =$row[csf('inserted_by')];
	$previous_data_arr[$row[csf('po_break_down_id')]][$key]['dtls_data'][$row[csf('id')]]['insert_date'] =$row[csf('insert_date')];
	$previous_data_arr[$row[csf('po_break_down_id')]][$key]['dtls_data'][$row[csf('id')]]['updated_by'] =$row[csf('updated_by')];
	$previous_data_arr[$row[csf('po_break_down_id')]][$key]['dtls_data'][$row[csf('id')]]['update_date'] =$row[csf('update_date')];
	$previous_data_arr[$row[csf('po_break_down_id')]][$key]['dtls_data'][$row[csf('id')]]['status_active'] =$row[csf('status_active')];
	$previous_data_arr[$row[csf('po_break_down_id')]][$key]['dtls_data'][$row[csf('id')]]['is_deleted'] =$row[csf('is_deleted')];
 }
/*  echo "<pre>";
 print_r($previous_data_arr);die; */
 $add_comma=0; $mst_data_array=""; $dtls_data_array=""; $add_comma_dtls=0; $i=1; 
 foreach($previous_data_arr as $po_id=>$act_po_data){
	foreach($act_po_data as $po_data){
		$mst_id = return_next_id( "id", "wo_po_acc_po_info", 1 );
		$mst_data_array ="(".$mst_id.",'".$po_data['job_no']."',".$po_data['job_id'].",".$po_id.",'".$po_data['acc_po_no']."','".$po_data['acc_ship_date']."','".$po_data['acc_ship_date']."',1,1,".$po_data['acc_po_qty'].",".$po_data['inserted_by'].",'".$po_data['insert_date']."',".$po_data['updated_by'].",'".$po_data['update_date']."',".$po_data['status_active'].",".$po_data['is_deleted'].")";
		$dtls_data_array="";$k=1;
		foreach($po_data['dtls_data'] as $dtls_id=>$dtls_data){
			if ($add_comma_dtls!=0) $dtls_data_array .=",";
			if($dtls_data['country_id']=='') $country_id=0; else $country_id=$dtls_data['country_id'];
			if($dtls_data['gmts_item']=='') $gmts_item=0; else $gmts_item=$dtls_data['gmts_item'];
			if($dtls_data['gmts_color_id']=='') $gmts_color_id=0; else $gmts_color_id=$dtls_data['gmts_color_id'];
			if($dtls_data['gmts_size_id']=='') $gmts_size_id=0; else $gmts_size_id=$dtls_data['gmts_size_id'];

			$dtls_data_array .="(".$dtls_id.",".$mst_id.",".$po_id.",".$country_id.",".$gmts_item.",".$gmts_color_id.",".$gmts_size_id.",".$dtls_data['po_qty'].",".$dtls_data['inserted_by'].",'".$dtls_data['insert_date']."',".$dtls_data['updated_by'].",'".$dtls_data['update_date']."',".$dtls_data['status_active'].",".$dtls_data['is_deleted'].")";

			//$dtls_id=$dtls_id+1;
			$add_comma_dtls++;
			$k++;
		}
		$rID=sql_insert("WO_PO_ACC_PO_INFO",$mst_field_array,$mst_data_array,0);
		if($dtls_data_array !=''){
			//echo "Insert INTO WO_PO_ACC_PO_INFO_DTLS ($dtls_field_array) values $dtls_data_array"; die;
			$rID2=sql_insert("WO_PO_ACC_PO_INFO_DTLS",$dtls_field_array,$dtls_data_array,0);
		}
		//echo $rID.'--'.$rID2; die;
		if($rID && $rID2)
		{
			oci_commit($con);
			echo $mst_id." Total Actual PO & ".$k." Details Insert<br>";
		}
		else
		{
			oci_rollback($con);
			echo $mst_id." Data Not insert<br>";
		}
		/* $mst_id=$mst_id+1;
		$add_comma++; */
		$i++;
	}
 }
 //echo "Insert INTO WO_PO_ACC_PO_INFO_DTLS ($dtls_field_array) values $dtls_data_array"; die;
 

disconnect($con);
die;
 //echo "Insert INTO WO_PO_ACC_PO_INFO_DTLS ($dtls_field_array) values $dtls_data_array"; die;

/*  echo '<pre>';
 print_r($previous_data_arr); die; */
   
?>