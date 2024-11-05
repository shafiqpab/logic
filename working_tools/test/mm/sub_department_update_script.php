<?
/*******************************************************************
|	Purpose			:	This controller is for Sub department code to sub department id for order entry page
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Md. Zakaria
|	Creation date 	:	16-02-2021
|	Updated by 		: 		
|	Update date		:    
|	QC Performed BY	:		
|	QC Date			:	
|	Comments		:
*********************************************************************/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
$con=connect();
$sel_sql="SELECT id,buyer_name,product_dept,product_code from  wo_po_details_master where is_deleted=0 and pro_sub_dep is null and product_code is not null order by id";
 $data=sql_select($sel_sql);
 $field_array="id,sub_department_name,department_id,buyer_id,status_active,inserted_by,insert_date";
 if(count($data)>0){
	foreach($data as $row)
	{
		$sub_dept_id='';
        $sub_dept_data=sql_select("SELECT id from lib_pro_sub_deparatment where sub_department_name='".$row[csf('product_code')]."' and department_id=".$row[csf('product_dept')]." and buyer_id=".$row[csf('buyer_name')]."");
        foreach($sub_dept_data as $data){
            $sub_dept_id=$data[csf('id')];
        }        
		if($sub_dept_id!=''){
			$id=$row[csf('id')];
			$update_dtls=execute_query("UPDATE  wo_po_details_master set pro_sub_dep='$sub_dept_id' where id='$id'",1);
			if($update_dtls)
			{
				oci_commit($con);
				echo "Data update Successfully<br>";
			}
			else{
				oci_rollback($con);
				echo $update_id."Data not update<br>";
			}
		}
		else{
            $update_id=$row[csf('id')];
			$sub_department_name=$row[csf('product_code')];
			$department_id=$row[csf('product_dept')];
			$buyer_id=$row[csf('buyer_name')];
			$id = return_next_id( "id", "lib_pro_sub_deparatment", 1 );
			$data_array="(".$id.",'".$sub_department_name."',".$department_id.",".$buyer_id.",1,1,'".$pc_date_time."')";
			$rID=sql_insert("lib_pro_sub_deparatment",$field_array,$data_array,1);
			if($rID)
			{
				$update_dtls=execute_query("UPDATE  wo_po_details_master set pro_sub_dep='$id' where id='$update_id'",1);
			}		
			if($update_dtls && $rID)
			{
				oci_commit($con);
				echo $update_id."Data Saved Successfully<br>";
			}
			else{
				oci_rollback($con);
				echo $update_id."Data not update<br>";
			}
		}
	}
}
else{
	echo "No data Found";
}
 
 
 

  
?>