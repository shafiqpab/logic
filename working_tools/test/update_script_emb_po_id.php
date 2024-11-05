<?
include('../includes/common.php');
$con = connect();
	//, a.booking_dtls_id, a.receive_dtls_id, a.job_dtls_id
	$production_sql ="select a.id,b.po_id,b.color_size_id,b.buyer_po_id from subcon_embel_production_mst a , subcon_embel_production_dtls b where b.mst_id=a.id and a.entry_form=315 and a.id in (63035,63216,63463,63036,63462,63818,63462,64139,63953,63819,63954,64141,64473,64842,65332,65928,64174,64175,64176,64475,64846,65333,64177,64178,64179,64180,64181,64182,64474,64841,65329,65926,66242,64552,64554,64556,64557,64558,64559,64560,64561,64840,65334,65923,66243,65127,65128,65130,65132,65134,65135,65136,65139,65924,66244,66406,66407,66408,66409,66410,66414,66415,66416,66547,66548,66549,66553,66560,66561,66562,66563,66565,66568,66569,66571,66572,66573,66574,66577,66578,66579)";
	$production_result=sql_select($production_sql); 

	foreach ($production_result as $value) 
	{
		$prod_arr[$value[csf("id")]][$value[csf("color_size_id")]]['po_id']				=$value[csf("po_id")];
		$prod_arr[$value[csf("id")]][$value[csf("color_size_id")]]['buyer_po_id']		=$value[csf("buyer_po_id")];
	}

	$qc_sql ="select a.recipe_id,b.id,b.po_id,b.color_size_id,b.buyer_po_id from subcon_embel_production_mst a , subcon_embel_production_dtls b where b.mst_id=a.id and a.entry_form=324 and a.id in (65937,65938,65939,66266,66267,66268,66269,66270,66271,66277,66279,66281,66283,66284,66285,66286,66290,66291,66293,66294,66295,66296,66301,66302,66303,66304,66306,66307,66308,66310,66311,66312,66313,66316,66317,66319,66320,66321,66322,66323,66324,66337,66338,66339,66340,66341,66342,66345,66346,66348,66349,66350,66351,66355,66357,66412,66534,66538,66540,66541,66542,66543,66544,66587,66588,66589,66590,66591,66592,66593,66594,66595,66596,66597,66598,66601,66602,66603,66604,66605,66606)";
	$qc_result=sql_select($qc_sql);

	foreach ($qc_result as $value) 
	{
		//$brkID	=$value[csf('break_down_details_id')];

		$po_id 			=$prod_arr[$value[csf("recipe_id")]][$value[csf("color_size_id")]]['po_id']	;
		$buyer_po_id 	=$prod_arr[$value[csf("recipe_id")]][$value[csf("color_size_id")]]['buyer_po_id'];

		if($value[csf('id')]!="")
		{
			$data_array2[$value[csf('id')]]=explode("*",("'".$po_id."'*'".$buyer_po_id."'"));
			$hdn_dtls_id_arr[]=$value[csf('id')];
		}
	}
	//echo "<pre>";print_r($data_array2);die;
	$field_array2="po_id*buyer_po_id";
	if($data_array2!="")
	{
		echo "10**".bulk_update_sql_statement( "trims_delivery_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr);die;
		$rID2=execute_query(bulk_update_sql_statement( "subcon_embel_production_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr),1);
		if($rID2) $flag=1; else $flag=0;
	}


echo $rID2.'=='.$flag; die;

if($db_type==2)
{
	
	if($rID2 && $flag)
	{
		oci_commit($con); 
		echo " Update Successful. <br>";die;
	}
	else
	{
		oci_rollback($con);
		echo " Update Failed";
		die;
	}
}
?>