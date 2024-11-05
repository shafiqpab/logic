<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');die;
$con=connect();
 
$sel_sql="SELECT a.id,a.construction,b.copmposition_id,b.percent from  LIB_YARN_COUNT_DETERMINA_MST a ,LIB_YARN_COUNT_DETERMINA_DTLS b where a.id=b.mst_id    order by b.id";
 $data=sql_select($sel_sql);
 $data_arr=array();
 $dup_chk=array();
 foreach($data as $v)
 {
 	if(!in_array($v[csf("id")], $dup_chk))
 	{
 		$st="";
 		$st.=$v[csf("construction")]." ".$composition[$v[csf("copmposition_id")]]." ".$v[csf("percent")]."%";
 		$dup_chk[$v[csf("id")]]=$v[csf("id")];
 	}
 	else
 	{
 		if($composition[$v[csf("copmposition_id")]])$val=$composition[$v[csf("copmposition_id")]]." ";
 		else $val="&nbsp;";
 		$st.=" ".$val."".$v[csf("percent")]."%";
 	}
 	$st=trim($st);
 	$data_arr[$st] =$v[csf("id")];
 }
 $sample_sql="SELECT fabric_description,id from SAMPLE_DEVELOPMENT_FABRIC_ACC where form_type=1 and (determination_id is null or determination_id=0)  and fabric_description is not null order by id desc";
 foreach(sql_select($sample_sql) as $value)
 {
 	$id=trim($value[csf("id")]);
 	$desc=trim($value[csf("fabric_description")]);
 	$desc=trim($desc);
 	$det_id=$data_arr[$desc];
 	$update_dtls=execute_query("UPDATE  SAMPLE_DEVELOPMENT_FABRIC_ACC set determination_id='$det_id' where id='$id'",1);
 }



 $booking_sql="SELECT fabric_description,id from wo_non_ord_samp_booking_dtls where entry_form_id=140 and (lib_yarn_count_deter_id is null or lib_yarn_count_deter_id=0)  and fabric_description   is not null order by id desc";
 foreach(sql_select($booking_sql) as $values)
 {
 	$id=trim($values[csf("id")]);
 	$desc=trim($values[csf("fabric_description")]);
 	$desc=trim($desc);
 	$det_id=$data_arr[$desc];
 	$update_dtls=execute_query("UPDATE  wo_non_ord_samp_booking_dtls set lib_yarn_count_deter_id='$det_id' where id='$id'",1);
 }





 if($update_dtls)
 {
 	oci_commit($con);
 	echo "Data Saved Successfully";
 }
 

  
?>