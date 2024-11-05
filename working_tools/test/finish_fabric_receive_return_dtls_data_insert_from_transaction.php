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

$transaction_sql=sql_select("select a.id,b.id trans_id,b.pi_wo_batch_no, b.prod_id,b.cons_quantity,b.fabric_shade,b.store_id,b.no_of_roll,b.body_part_id,b.rack,b.self,b.floor_id,b.room,b.booking_no,c.unit_of_measure
	from inv_issue_master a,inv_transaction b,product_details_master c
	where a.id=b.mst_id and b.prod_id=c.id and a.entry_form=46 and a.status_active=1 and a.is_deleted=0 and b.item_category=2
	and b.transaction_type=3 and b.status_active=1 and b.is_deleted=0 and c.item_category_id=2 and c.status_active=1 and c.is_deleted=0");

if(empty($transaction_sql))
{
	echo "Data Not Found";
	die;
}
if(!empty($transaction_sql)){

	$field_array_dtls="id,mst_id,trans_id,batch_id,prod_id,uom,issue_qnty,fabric_shade,store_id,no_of_roll,body_part_id,rack_no,shelf_no,floor,room,inserted_by,insert_date,booking_no";

	foreach($transaction_sql as $val)
	{
		$mst_id   = $val[csf("id")];
		$trans_id = $val[csf("trans_id")];
		$batch_id = $val[csf("pi_wo_batch_no")];
		$cons_quantity = ($val[csf("cons_quantity")]!="")?$val[csf("cons_quantity")]:0;

		$id_dtls=return_next_id_by_sequence( "INV_FIN_FAB_ISSUE_DTLS_PK_SEQ", "inv_finish_fabric_issue_dtls", $con) ;
		//$data_array_mrr[]= "(".$mrrWiseIsID.",".$receive_trans_id.",".$issue_trans_id.",".$entry_form.",".$prod_id.",'".$mrr_issue_qnty."',".$cons_rate.",".$mrr_issue_amt.",'1','".$pc_date_time."')";
		if($data_array_dtls!="") $data_array_dtls.=", ";
		$user_id=777;
		$data_array_dtls.="(".$id_dtls.",".$mst_id.",'".$trans_id."','".$batch_id."','".$val[csf("prod_id")]."','".$val[csf("unit_of_measure")]."',".$cons_quantity.",'".$val[csf("fabric_shade")]."','".$val[csf("store_id")]."','".$val[csf("no_of_roll")]."','".$val[csf("body_part_id")]."','".$val[csf("rack")]."','".$val[csf("self")]."','".$val[csf("floor_id")]."','".$val[csf("room")]."',".$user_id.",'".$pc_date_time."','".$val[csf("booking_no")]."')";

		//print_r($data_array_dtls);
	//die;
	}
}
//echo "10**insert into inv_finish_fabric_issue_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
/*$rID = sql_insert2("inv_finish_fabric_issue_dtls",$field_array_dtls,$data_array_dtls,1);
if($rID==1){
	oci_commit($con);
	echo "Success";
}else{
	oci_rollback($con);
	echo "failed";
}*/
die;


?>