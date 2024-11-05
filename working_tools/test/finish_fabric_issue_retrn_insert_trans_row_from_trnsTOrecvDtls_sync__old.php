<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

function sql_insert2( $strTable, $arrNames, $arrValues, $commit, $contain_lob )
{
	global $con ;
	if($contain_lob=="") $contain_lob=0;
	if( $contain_lob==0)
	{
		$tmpv=explode(")",$arrValues);
		if(count($tmpv)>2)
			$strQuery= "INSERT ALL \n";
		else
			$strQuery= "INSERT  \n";

		for($i=0; $i<count($tmpv)-1; $i++)
		{
			if( strpos(trim($tmpv[$i]), ",")==0)
				$tmpv[$i]=substr_replace($tmpv[$i], " ", 0, 1);
			$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$tmpv[$i].") \n";
		}

		if(count($tmpv)>2) $strQuery .= "SELECT * FROM dual";
	 //return $strQuery ;
	}
	else
	{
		$tmpv=explode(")",$arrValues);

		for($i=0; $i<count($tmpv)-1; $i++)
		{
			$strQuery="";
			$strQuery= "INSERT  \n";
			if( strpos(trim($tmpv[$i]), ",")==0)
				$tmpv[$i]=substr_replace($tmpv[$i], " ", 0, 1);
			$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$tmpv[$i].") \n";
			//return $strQuery ;
			$stid =  oci_parse($con, $strQuery);
			$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
			if (!$exestd) return "0";
		}
		return "1";

	}
  	//return  $strQuery; die;
	//echo $strQuery;die;
	//$_SESSION['last_query']=$_SESSION['last_query'].";;".$strQuery;



	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);

	if ($exestd)
		return "1";
	else
		return "0";

	die;

	if ( $commit==1 )
	{
		if (!oci_error($exestd))
		{
			$pc_time= add_time(date("H:i:s",time()),360);
			$pc_date_time = date("d-M-Y h:i:s",strtotime(add_time(date("H:i:s",time()),360)));
			$pc_date = date("d-M-Y",strtotime(add_time(date("H:i:s",time()),360)));

			$strQuery= "INSERT INTO activities_history ( session_id,user_id,ip_address,entry_time,entry_date,module_name,form_name,query_details,query_type) VALUES ('".$_SESSION['logic_erp']["history_id"]."','".$_SESSION['logic_erp']["user_id"]."','".$_SESSION['logic_erp']["pc_local_ip"]."','".$pc_date_time."','".$pc_date."','".$_SESSION["module_id"]."','".$_SESSION['menu_id']."','".encrypt($_SESSION['last_query'])."','0')";
			$resultss=oci_parse($con, $strQuery);
			oci_execute($resultss);
			$_SESSION['last_query']="";
			//oci_commit($con);
			return "0";
		}
		else
		{
			//oci_rollback($con);
			return "10";
		}
	}
	else return 1;
	//else
		//return 0;

	die;
}

$issue_return_trans_sql = sql_select("select a.id,b.id trans_id,b.pi_wo_batch_no batch_id, b.prod_id,b.cons_quantity,b.fabric_shade,b.store_id,b.no_of_roll,b.body_part_id,b.rack,b.self,b.floor_id,b.room,b.booking_no,c.unit_of_measure,c.detarmination_id,c.color,c.gsm,c.dia_width
	from inv_receive_master a,inv_transaction b,product_details_master c
	where a.id=b.mst_id and b.prod_id=c.id and a.entry_form=52 and a.status_active=1 and a.is_deleted=0 and b.item_category=2
	and b.transaction_type=4 and b.status_active=1 and b.is_deleted=0 and c.item_category_id=2 and c.status_active=1 and c.is_deleted=0");

if(empty($issue_return_trans_sql))
{
	echo "Data Not Found";
	die;
}


$sql_ordWiseProf=sql_select("select a.trans_id,a.po_breakdown_id from order_wise_pro_details a,inv_transaction b where b.id=a.trans_id and b.transaction_type=4 and a.trans_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=52");

$orderids_arr=array();
foreach($sql_ordWiseProf  as $row)
{
	if($orderids_arr[$row[csf("trans_id")]]["po_breakdown_id"] == "")
	{
		$orderids_arr[$row[csf("trans_id")]]["po_breakdown_id"]= $row[csf("po_breakdown_id")];
	}else{
		$orderids_arr[$row[csf("trans_id")]]["po_breakdown_id"].= ",".$row[csf("po_breakdown_id")];
	}
}

$field_details_array = "id,mst_id,trans_id,prod_id,batch_id,fabric_description_id,gsm,width,color_id,order_id,uom, fabric_shade,receive_qnty,floor,room,rack_no,shelf_no,inserted_by,insert_date";
foreach($issue_return_trans_sql  as $row)
{
	$mst_id   = $val[csf("id")];
	$trans_id = $val[csf("trans_id")];
	$batch_id = $val[csf("batch_id")];
	$order_ids = $orderids_arr[$val[csf("trans_id")]]["po_breakdown_id"]
	$cons_quantity = ($val[csf("cons_quantity")]!="")?$val[csf("cons_quantity")]:0;
	$dtls_id = return_next_id_by_sequence("PRO_FIN_FAB_RCV_DTLS_PK_SEQ", "pro_finish_fabric_rcv_dtls", $con);

	if($data_array_dtls!="") $data_array_dtls.=", ";
	$user_id=777;
	$data_array_dtls .= "(" . $dtls_id . ",". $mst_id .",". $trans_id .",". $val[csf("prod_id")] .",". $batch_id.",".$val[csf("detarmination_id")] .",". $val[csf("gsm")] .",'". $val[csf("dia_width")] ."',". $val[csf("color")] .",". $order_ids .",". $val[csf("unit_of_measure")] ."," . $val[csf("fabric_shade")] . "," . $cons_quantity . ",'" .$val[csf("floor_id")]."','". $val[csf("room")] . "','" . $val[csf("rack")] . "','" . $val[csf("self")] . "',". $user_id . ",'" . $pc_date_time ."')";
}
echo "10**insert into pro_finish_fabric_rcv_dtls (".$field_details_array.") values ".$data_array_dtls;die;
/*$rID = sql_insert2("pro_finish_fabric_rcv_dtls",$field_details_array,$data_array_dtls,1);
if($rID==1){
	oci_commit($con);
	echo "Success";
}else{
	oci_rollback($con);
	echo "failed";
}*/
die;

/*$sql = sql_select("select a.id,a.mst_id,a.cons_quantity,a.cons_reject_qnty,a.machine_id,a.cons_rate,a.cons_amount,
a.fabric_shade,a.buyer_id,a.batch_id,a.prod_id,
b.detarmination_id,b.dia_width,b.color,b.unit_of_measure,b.gsm
from inv_transaction a, product_details_master b
 where a.status_active=1 and a.item_category=2 and a.transaction_type=4  and a.prod_id=b.id and b.status_active=1");
// a.id=1127139 and*/

 $sql = sql_select("select a.id,a.mst_id,a.cons_quantity,a.cons_reject_qnty,a.machine_id,a.cons_rate,a.cons_amount,
 	a.fabric_shade,a.buyer_id,a.batch_id,a.prod_id,
 	b.detarmination_id,b.dia_width,b.color,b.unit_of_measure,b.gsm,a.insert_date,c.id dtls_id
 	from inv_transaction a
 	left join pro_finish_fabric_rcv_dtls c on a.id=c.trans_id
 	left join product_details_master b on a.prod_id=b.id
 	where a.status_active=1 and a.item_category=2 and a.transaction_type=4");

//a.id=1127139 and
 $field_details_array = "id,trans_id,mst_id,receive_qnty,reject_qty,machine_no_id,rate,amount,fabric_shade,buyer_id,batch_id,prod_id,fabric_description_id,width,color_id,uom,gsm,order_id,insert_date,inserted_by";


 $insertQry="";
 foreach($sql  as $row)
 {
 	if($row[csf("dtls_id")]==""){
 		$trans_id 				=$row[csf("id")];
 		$mst_id 				=$row[csf("mst_id")];
 		$receive_qnty 			=$row[csf("cons_quantity")];
 		$reject_qty 			=$row[csf("cons_reject_qnty")];
 		$machine_no_id 			=$row[csf("machine_id")];
 		$rate 					=$row[csf("cons_rate")];
 		$amount  				=$row[csf("cons_amount")];
 		$fabric_shade 			=$row[csf("fabric_shade")];
 		$buyer_id 				=$row[csf("buyer_id")];
 		$batch_id 				=$row[csf("batch_id")];
 		$prod_id 				=$row[csf("prod_id")];
 		$fabric_description_id 	=$row[csf("detarmination_id")];
 		$width 					="'".$row[csf("dia_width")]."'";
 		$color_id 				=$row[csf("color")];
 		$uom 					=$row[csf("unit_of_measure")];
 		$gsm 					=$row[csf("gsm")];
 		$order_id 				=$orderids_arr[$row[csf("id")]]["po_breakdown_id"];
 		$insert_date 			=$row[csf("insert_date")];

 		$id = return_next_id_by_sequence("PRO_FIN_FAB_RCV_DTLS_PK_SEQ", "pro_finish_fabric_rcv_dtls", $con);
 		$data_array_dtls = "(" . $id . "," . $trans_id . ",". $mst_id .",". $receive_qnty .",". $reject_qty .",". $machine_no_id .",". $rate.",".$amount .",". $fabric_shade .",'". $buyer_id ."','". $batch_id ."',". $prod_id .",". $fabric_description_id .",". $width ."," . $color_id . "," . $uom . "," .$gsm.",'" .$order_id."','" .$insert_date."',777)";

		//$insertQry.="insert into pro_finish_fabric_rcv_dtls (".$field_details_array.") values ".$data_array_dtls."##";

 		$rID=sql_insert("pro_finish_fabric_rcv_dtls",$field_details_array,$data_array_dtls,1);
 	}
 }
//echo $insertQry;
//die;
 if( $rID )
 {
 	oci_commit($con);
 	echo "s".$trans_id[0];
 }
 else
 {
 	oci_rollback($con);
 	echo "f".$trans_id[0];
 }






 ?>