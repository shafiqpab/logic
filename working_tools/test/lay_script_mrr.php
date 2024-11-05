<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$all_company=return_library_array( "select id,id  from  lib_company where status_active=1 and is_deleted=0",'id','id');
 
$tables="PPL_CUT_LAY_MST";

$year=date("Y",time()); 
foreach($all_company as $k=>$val)
{
	
		
		$max_seq=sql_select("select max(cut_num_prefix_no) as next_id from ppl_cut_lay_mst where company_id=$k  and to_char(insert_date,'YYYY')=$year");
		$max_id=$max_seq[0][csf("next_id")];  
		if(!$max_id) {$max_id="1";}
		$insert=execute_query("INSERT into  platform_sequence_pk(table_name,next_id ,company_id,entry_form ,year,item_category_id,booking_type,production_type,emblishment_type,transfer_criteria) values('$tables','$max_id','$k','0','$year','0','0','0','0','0')" );
		
		
	
	 
}

oci_commit($con); 
echo "Success";

 
?>