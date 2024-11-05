<?
//Note: Please follow "approval/migrate/app_migrate_api.php?action=pi_approval_v2_controller" to create new action
header('Content-type:text/html; charset=utf-8');
session_start();
 

require_once('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

//api link : approval/migrate/app_migrate_api.php?action=pi_approval_v2_controller

if ($action=="pi_approval_v2_controller")
{
    /*
        Note: This migration use for data transfer from old approval to new app "pi_approval_v2_controller";
    */

    $entry_form = 27;
    
    $old_data_sql="select  max(id) as ID from approval_mst where entry_form = $entry_form";
    $old_data_sql_res=sql_select($old_data_sql);
    if( $old_data_sql_res[0]['ID']){
        echo "Old data found, Not allow migration. If you migrate you will lost your all data.<br> [পুরানো ডেটা পাওয়া গেছে, মাইগ্রেশনের অনুমতি নেই৷ আপনি মাইগ্রেট করলে আপনার সমস্ত ডেটা হারিয়ে যাবে।]";
        exit();
    }

    $tmpSql = "select id from COM_PI_MASTER_DETAILS where APPROVED=1 and STATUS_ACTIVE=1 and id not in( select MST_ID from APPROVAL_MST where entry_form=27)";


    $appHisSql="select MST_ID,APPROVED_BY,SEQUENCE_NO,INSERTED_BY from APPROVAL_HISTORY where ENTRY_FORM=27 and CURRENT_APPROVAL_STATUS=1 and MST_ID in($tmpSql) ";
    $appHisSqlRes=sql_select($appHisSql);
    //print_r($appHisSqlRes);die;

    $appHisSqlNewRes = array_chunk($appHisSqlRes,200);
    $id=return_next_id( "id","approval_mst", 1 ) ;
    $con = connect();
    $flag=1;
    foreach($appHisSqlNewRes as $appHisSqlChankRes){
        $data_array=''; $target_app_id_arr=array();$data_array_up=array();
        
        foreach($appHisSqlChankRes as $row){
            $row['INSERTED_BY'] =($row['INSERTED_BY'])?$row['INSERTED_BY']:$_SESSION['logic_erp']['user_id'];
            
            $data_array_up[$row['MST_ID']] = explode(",",("".$row['SEQUENCE_NO']."")); 
            $target_app_id_arr[]=$row['MST_ID'];
            //......................

            if($data_array!=''){$data_array.=",";}
            $data_array.="(".$id.",27,".$row['MST_ID'].",".$row['SEQUENCE_NO'].",".$row['APPROVED_BY'].",'".$pc_date_time."','".$row['INSERTED_BY']."','".$pc_date_time."','".$user_ip."')"; 
            $id=$id+1;
        }

        if($flag == 1){
            $field_array_up="APPROVED_SEQU_BY"; 
            $multi_up_sql=bulk_update_sql_statement( "com_pi_master_details", "id", $field_array_up, $data_array_up, $target_app_id_arr,1 );
            $rID=execute_query($multi_up_sql); 
            if($rID){$flag=1;}else{$flag=0;}
        }

        if($flag == 1){
            $field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
            $rID1=sql_insert("approval_mst",$field_array,$data_array,0);
            if($rID1){$flag=1;}else{$flag=0;}
        }
        

    }


	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo 0;
	}
	disconnect($con);
    die;

}

else if ($action=="yarn_work_order_approval_sweater_v2_controller")
{
    /*
        Note: This migration use for data transfer from old approval to new app "yarn_work_order_approval_sweater_v2_controller";
    */

    $entry_form = 43;
    
    $old_data_sql="select  max(id) as ID from approval_mst where entry_form = $entry_form";
    $old_data_sql_res=sql_select($old_data_sql);
    if( $old_data_sql_res[0]['ID']){
        echo "Old data found, Not allow migration. If you migrate you will lost your all data.<br> [পুরানো ডেটা পাওয়া গেছে, মাইগ্রেশনের অনুমতি নেই৷ আপনি মাইগ্রেট করলে আপনার সমস্ত ডেটা হারিয়ে যাবে।]";
        exit();
    }

    $tmpSql = "select id from wo_non_order_info_mst where APPROVED=1 and STATUS_ACTIVE=1 and id not in( select MST_ID from APPROVAL_MST where entry_form= $entry_form)";


    $appHisSql="select MST_ID,APPROVED_BY,SEQUENCE_NO,INSERTED_BY from APPROVAL_HISTORY where ENTRY_FORM= $entry_form and CURRENT_APPROVAL_STATUS=1 and MST_ID in($tmpSql) ";
    $appHisSqlRes=sql_select($appHisSql);
    //print_r($appHisSqlRes);die;

    $appHisSqlNewRes = array_chunk($appHisSqlRes,200);
    $id=return_next_id( "id","approval_mst", 1 ) ;
    $con = connect();
    $flag=1;
    foreach($appHisSqlNewRes as $appHisSqlChankRes){
        $data_array=''; $target_app_id_arr=array();$data_array_up=array();
        
        foreach($appHisSqlChankRes as $row){
            $row['INSERTED_BY'] =($row['INSERTED_BY'])?$row['INSERTED_BY']:$_SESSION['logic_erp']['user_id'];
            
            $data_array_up[$row['MST_ID']] = explode(",",("".$row['SEQUENCE_NO']."")); 
            $target_app_id_arr[]=$row['MST_ID'];
            //......................

            if($data_array!=''){$data_array.=",";}
            $data_array.="(".$id.",$entry_form,".$row['MST_ID'].",".$row['SEQUENCE_NO'].",".$row['APPROVED_BY'].",'".$pc_date_time."','".$row['INSERTED_BY']."','".$pc_date_time."','".$user_ip."')"; 
            $id=$id+1;
        }

        if($flag == 1){
            $field_array_up="APPROVED_SEQU_BY"; 
            $multi_up_sql=bulk_update_sql_statement( "wo_non_order_info_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr,1 );
            $rID=execute_query($multi_up_sql); 
            if($rID){$flag=1;}else{$flag=0;}
        }

        if($flag == 1){
            $field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
            $rID1=sql_insert("approval_mst",$field_array,$data_array,0);
            if($rID1){$flag=1;}else{$flag=0;}
        }
        

    }


	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo 0;
	}
	disconnect($con);
    die;

}

else if ($action=="pre_costing_approval_wvn_v2_controller")
{
    /*
        /approval/migrate/app_migrate_api.php?action=pre_costing_approval_wvn_v2_controller
    */

    $entry_form = 46;
    
    $old_data_sql="select  max(id) as ID from approval_mst where entry_form = $entry_form";
    $old_data_sql_res=sql_select($old_data_sql);
    if( $old_data_sql_res[0]['ID']){
       // echo "Old data found, Not allow migration. If you migrate you will lost your all data.<br> [পুরানো ডেটা পাওয়া গেছে, মাইগ্রেশনের অনুমতি নেই৷ আপনি মাইগ্রেট করলে আপনার সমস্ত ডেটা হারিয়ে যাবে।]";
       // exit();
    }

    $tmpSql = "select id from wo_pre_cost_mst where APPROVED=1 and STATUS_ACTIVE=1 and id not in( select MST_ID from APPROVAL_MST where entry_form=$entry_form)";

    
    $appHisSql="select MST_ID,APPROVED_BY,SEQUENCE_NO from APPROVAL_HISTORY where ENTRY_FORM=15 and CURRENT_APPROVAL_STATUS=1 and MST_ID in($tmpSql)";
   // echo $appHisSql;die;
    $appHisSqlRes=sql_select($appHisSql);
    
    $appHisSqlNewRes = array_chunk($appHisSqlRes,500);

    foreach($appHisSqlNewRes as $appHisSqlChankRes){

        $id=return_next_id( "id","approval_mst", 1 );
        $con = connect();
        $data_array_up = array(); $target_app_id_arr = array();
        foreach($appHisSqlChankRes as $row){
            
            $data_array_up[$row['MST_ID']] = explode(",",("".$row['SEQUENCE_NO']."")); 
            $target_app_id_arr[]=$row['MST_ID'];
            //......................
    
            if($data_array!=''){$data_array.=",";}
            $data_array.="(".$id.",$entry_form,".$row['MST_ID'].",".$row['SEQUENCE_NO'].",".$row['APPROVED_BY'].",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
            $id=$id+1;
        }


        $field_array_up="APPROVED_SEQU_BY"; 
        $multi_up_sql=bulk_update_sql_statement( "wo_pre_cost_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr );
        //echo $multi_up_sql;die;
        $rID=execute_query($multi_up_sql);


        $field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
        $rID1=sql_insert("approval_mst",$field_array,$data_array,0);

    } 
    
    
   // echo $rID.'=='.$rID1;oci_rollback($con);die;
    
    
    
    if($rID==1 and $rID1==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo 0;
	}
	disconnect($con);
    die;

}

else if ($action=="component_wise_precost")
{
   

    $appHisSql="select ID,JOB_ID,READY_TO_APPROVED from WO_PRE_COST_MST where APPROVED in(0,2) and STATUS_ACTIVE=1 and IS_DELETED=0 and JOB_ID is not null and job_id=33346 --and  ID between 3001 and 6000";
    //echo $appHisSql;die;
    $appHisSqlRes=sql_select($appHisSql);
    $id=return_next_id( "id","PRECOST_COMPONENT_APP_MST", 1 ) ;
    $con = connect();
    foreach($appHisSqlRes as $row){
        $data_array='';
        foreach($cost_components as $component_id=>$val){ 
     
            if($data_array!=''){$data_array.=",";}
            $data_array.="(".$id.",11,".$row['JOB_ID'].",".$component_id.",".$row['READY_TO_APPROVED'].",'0')"; 
            $id=$id+1;
        }
        $field_array="ID,ENTRY_FORM,JOB_ID,COST_COMPONENT_ID,READY_TO_APPROVED,APPROVED";
        $rID=sql_insert("PRECOST_COMPONENT_APP_MST",$field_array,$data_array,0);
    }

   
  
	if($rID==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo 0;
	}
	disconnect($con);
    die;


}


else if ($action=="sample_booking_without_order_approval")
{
    /*
        Note: This migration use for data transfer from old approval to new app "Sample Fabric Booking Without Order";
        api link : approval/migrate/app_migrate_api.php?action=sample_booking_without_order_approval
    */

    $entry_form = 9;
    
    $old_data_sql="select  max(id) as ID from approval_mst where entry_form = $entry_form";
    $old_data_sql_res=sql_select($old_data_sql);
    if( $old_data_sql_res[0]['ID']){
        echo "Old data found, Not allow migration. If you migrate you will lost your all data.<br> [পুরানো ডেটা পাওয়া গেছে, মাইগ্রেশনের অনুমতি নেই৷ আপনি মাইগ্রেট করলে আপনার সমস্ত ডেটা হারিয়ে যাবে।]";
        exit();
    }

    
    $appHisSql="select MST_ID, APPROVED_BY, SEQUENCE_NO from APPROVAL_HISTORY where entry_form=9 and current_approval_status=1 ";
    $appHisSqlRes=sql_select($appHisSql);
    $appHisSqlNewRes = array_chunk($appHisSqlRes,500);

    foreach($appHisSqlNewRes as $appHisSqlChankRes){

        $id=return_next_id( "id","approval_mst", 1 );
        $con = connect();
        $data_array_up = array(); $target_app_id_arr = array();
        foreach($appHisSqlChankRes as $row){
            
            $data_array_up[$row['MST_ID']] = explode(",",("".$row['SEQUENCE_NO']."")); 
            $target_app_id_arr[] = $row['MST_ID'];
            //......................

            if($data_array!=''){$data_array.=",";}
            $data_array.="(".$id.",9,".$row['MST_ID'].",".$row['SEQUENCE_NO'].",".$row['APPROVED_BY'].",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
            $id=$id+1;
        }


        $field_array_up="APPROVED_SEQU_BY"; 
        $multi_up_sql=bulk_update_sql_statement( "wo_non_ord_samp_booking_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr );
        $rID=execute_query($multi_up_sql);


        $field_array="id, entry_form, mst_id, sequence_no, approved_by, approved_date, INSERTED_BY, INSERT_DATE, user_ip";
        $rID1=sql_insert("approval_mst",$field_array,$data_array,0);
    }
 

	if($rID==1 and $rID1==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo 0;
	}
	disconnect($con);
    die;

}

else if ($action=="pre_costing_approval_v3_controller")
{
    /*
        Note: This migration use for data transfer from old approval to new app "pre_costing_approval_v3_controller";
    */

    $entry_form = 15;
    
    $old_data_sql="select  max(id) as ID from approval_mst where entry_form = $entry_form";
    $old_data_sql_res=sql_select($old_data_sql);
    if( $old_data_sql_res[0]['ID']){
        echo "Old data found, Not allow migration. If you migrate you will lost your all data.<br> [পুরানো ডেটা পাওয়া গেছে, মাইগ্রেশনের অনুমতি নেই৷ আপনি মাইগ্রেট করলে আপনার সমস্ত ডেটা হারিয়ে যাবে।]";
        exit();
    }

    
    $appHisSql="select MST_ID,APPROVED_BY,SEQUENCE_NO from APPROVAL_HISTORY where ENTRY_FORM=15 and CURRENT_APPROVAL_STATUS=1";
    $appHisSqlRes=sql_select($appHisSql);


    $appHisSqlNewRes = array_chunk($appHisSqlRes,500);
   // echo "<pre>"; var_dump($appHisSqlNewRes[4]);echo "</pre>";die;

   $id=return_next_id( "id","approval_mst", 1 );
   $con = connect();

    foreach($appHisSqlNewRes as $appHisSqlChankRes){

        $data_array='';$target_app_id_arr=array();
        foreach($appHisSqlChankRes as $row){
            
            $data_array_up[$row['MST_ID']] = explode(",",("".$row['SEQUENCE_NO']."")); 
            $target_app_id_arr[]=$row['MST_ID'];
            //......................

            if($data_array!=''){$data_array.=",";}
            $data_array.="(".$id.",15,".$row['MST_ID'].",".$row['SEQUENCE_NO'].",".$row['APPROVED_BY'].",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
            $id=$id+1;
        }

           // echo $data_array;die;

        $field_array_up="APPROVED_SEQU_BY"; 
        $multi_up_sql=bulk_update_sql_statement( "wo_pre_cost_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr );
        $rID=execute_query($multi_up_sql);


        $field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
        $rID1=sql_insert("approval_mst",$field_array,$data_array,0);

    }


   
    if($rID==1 and $rID1==1)
    {
        oci_commit($con);
        echo 1;
    }
    else
    {
        oci_rollback($con);
        echo 0;
    }

    disconnect($con);
    die;
   


}


else if ($action=="fabric_booking_approval_controller_v2")
{
    /*
       approval/migrate/app_migrate_api.php?action=fabric_booking_approval_controller_v2
    */
    $entry_form = 7;
    $company_id = 1;
    
    $old_data_sql="select  max(id) as ID from approval_mst where entry_form = $entry_form";
    $old_data_sql_res=sql_select($old_data_sql);
    if( $old_data_sql_res[0]['ID']){
        echo "Old data found, Not allow migration. If you migrate you will lost your all data.<br> [পুরানো ডেটা পাওয়া গেছে, মাইগ্রেশনের অনুমতি নেই৷ আপনি মাইগ্রেট করলে আপনার সমস্ত ডেটা হারিয়ে যাবে।]";
        exit();
    }



    $tmpSql = "select id from WO_BOOKING_MST where COMPANY_ID=$company_id and IS_APPROVED=1 and STATUS_ACTIVE=1 and id not in( select MST_ID from APPROVAL_MST where entry_form=$entry_form)";

    $appHisSql="select MST_ID,APPROVED_BY,SEQUENCE_NO,INSERTED_BY from APPROVAL_HISTORY where ENTRY_FORM=$entry_form and CURRENT_APPROVAL_STATUS=1 and MST_ID in($tmpSql) ";
   // echo $appHisSql;die;
    $appHisSqlRes=sql_select($appHisSql);


    $appHisSqlNewRes = array_chunk($appHisSqlRes,500);
   // echo "<pre>"; var_dump($appHisSqlNewRes[4]);echo "</pre>";die;

   $id=return_next_id( "id","approval_mst", 1 );
   $con = connect();

    foreach($appHisSqlNewRes as $appHisSqlChankRes){

        $data_array='';$target_app_id_arr=array();
        foreach($appHisSqlChankRes as $row){
            
            $data_array_up[$row['MST_ID']] = explode(",",("".$row['SEQUENCE_NO'].",".$row['APPROVED_BY']."")); 
            $target_app_id_arr[]=$row['MST_ID'];
            //......................

            if($data_array!=''){$data_array.=",";}
            $data_array.="(".$id.",$entry_form,".$row['MST_ID'].",".$row['SEQUENCE_NO'].",".$row['APPROVED_BY'].",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
            $id=$id+1;
        }

           // echo $data_array;die;

        $field_array_up="APPROVED_SEQU_BY*APPROVED_BY"; 
        $multi_up_sql=bulk_update_sql_statement( "WO_BOOKING_MST", "id", $field_array_up, $data_array_up, $target_app_id_arr );
        $rID=execute_query($multi_up_sql);


        $field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
        $rID1=sql_insert("approval_mst",$field_array,$data_array,0);

    }


   
    if($rID==1 and $rID1==1)
    {
        oci_commit($con);
        echo 1;
    }
    else
    {
        oci_rollback($con);
        echo 0;
    }

    disconnect($con);
    die;
   


}

else if ($action=="non_order_sample_booking_approval_controller")
{
    /*
        Note: This migration use for data transfer from old approval to new app "non_order_sample_booking_approval_controller.php";
        api link : approval/migrate/app_migrate_api.php?action=non_order_sample_booking_approval_controller
    */

    $entry_form = 9;
    
    $old_data_sql="select  max(id) as ID from approval_mst where entry_form = $entry_form";
    $old_data_sql_res=sql_select($old_data_sql);
    if( $old_data_sql_res[0]['ID']){
        echo "Old data found, Not allow migration. If you migrate you will lost your all data.<br> [পুরানো ডেটা পাওয়া গেছে, মাইগ্রেশনের অনুমতি নেই৷ আপনি মাইগ্রেট করলে আপনার সমস্ত ডেটা হারিয়ে যাবে।]";
        exit();
    }



    
    $appHisSql="select MST_ID,APPROVED_BY,SEQUENCE_NO from APPROVAL_HISTORY where ENTRY_FORM=$entry_form and CURRENT_APPROVAL_STATUS=1";
    $appHisSqlRes=sql_select($appHisSql);


    $appHisSqlNewRes = array_chunk($appHisSqlRes,500);
   // echo "<pre>"; var_dump($appHisSqlNewRes[4]);echo "</pre>";die;

   $id=return_next_id( "id","approval_mst", 1 );
   $con = connect();

    foreach($appHisSqlNewRes as $appHisSqlChankRes){

        $data_array='';$target_app_id_arr=array();
        foreach($appHisSqlChankRes as $row){
            
            $data_array_up[$row['MST_ID']] = explode(",",("".$row['SEQUENCE_NO']."")); 
            $target_app_id_arr[]=$row['MST_ID'];
            //......................

            if($data_array!=''){$data_array.=",";}
            $data_array.="(".$id.",".$entry_form.",".$row['MST_ID'].",".$row['SEQUENCE_NO'].",".$row['APPROVED_BY'].",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
            $id=$id+1;
        }

           // echo $data_array;die;

        $field_array_up="APPROVED_SEQU_BY"; 
        $multi_up_sql=bulk_update_sql_statement( "wo_non_ord_samp_booking_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr );
        $rID=execute_query($multi_up_sql);


        $field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
        $rID1=sql_insert("approval_mst",$field_array,$data_array,0);

    }


   
    if($rID==1 and $rID1==1)
    {
        oci_commit($con);
        echo 1;
    }
    else
    {
        oci_rollback($con);
        echo 0;
    }

    disconnect($con);
    die;
   


}

else if ($action=="item_issue_requisition_approval_controller_v2")
{
    /*
        approval/migrate/app_migrate_api.php?action=item_issue_requisition_approval_controller_v2;
    */

    $entry_form = 56;
    $from_entry_form = 26;
    $company_id = 1;
    
    $old_data_sql="select  max(id) as ID from approval_mst where entry_form = $entry_form";
    $old_data_sql_res=sql_select($old_data_sql);
    if( $old_data_sql_res[0]['ID']){
        // echo "Old data found, Not allow migration. If you migrate you will lost your all data.<br> [পুরানো ডেটা পাওয়া গেছে, মাইগ্রেশনের অনুমতি নেই৷ আপনি মাইগ্রেট করলে আপনার সমস্ত ডেটা হারিয়ে যাবে।]";
        // exit();
    }

    $tmpSql = "select id from INV_ITEM_ISSUE_REQUISITION_MST where COMPANY_ID=$company_id and IS_APPROVED=1 and STATUS_ACTIVE=1 and id not in( select MST_ID from APPROVAL_MST where entry_form=$entry_form)";

    $appHisSql="select MST_ID,APPROVED_BY,SEQUENCE_NO,INSERTED_BY from APPROVAL_HISTORY where ENTRY_FORM=$from_entry_form and CURRENT_APPROVAL_STATUS=1 and MST_ID in($tmpSql) ";
    echo $appHisSql;die;
    $appHisSqlRes=sql_select($appHisSql);

      print_r($appHisSqlRes);die;

    $appHisSqlNewRes = array_chunk($appHisSqlRes,500);
    $id=return_next_id( "id","approval_mst", 1 ) ;
    $con = connect();
   
    foreach($appHisSqlNewRes as $appHisSqlChankRes){
        $data_array=''; $data_array_up=array();$target_app_id_arr=array();
       
        foreach($appHisSqlChankRes as $row){
            $row['INSERTED_BY'] =($row['INSERTED_BY'])?$row['INSERTED_BY']:$_SESSION['logic_erp']['user_id'];
            
            $data_array_up[$row['MST_ID']] = explode(",",("".$row['SEQUENCE_NO'].",".$row['APPROVED_BY']."")); 
            $target_app_id_arr[]=$row['MST_ID'];
            //......................

            if($data_array!=''){$data_array.=",";}
            $data_array.="(".$id.",$entry_form,".$row['MST_ID'].",".$row['SEQUENCE_NO'].",".$row['APPROVED_BY'].",'".$pc_date_time."','".$row['INSERTED_BY']."','".$pc_date_time."','".$user_ip."')"; 
            $id=$id+1;
        }

        $field_array_up="APPROVED_SEQU_BY*APPROVED_BY"; 
        $multi_up_sql=bulk_update_sql_statement( "INV_ITEM_ISSUE_REQUISITION_MST", "id", $field_array_up, $data_array_up, $target_app_id_arr );
        $rID=execute_query($multi_up_sql);

        $field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,USER_IP";
        $rID1=sql_insert("approval_mst",$field_array,$data_array,0);
    }
 
   // echo $rID.'**'.$rID1;oci_rollback($con);die;

	if($rID==1 and $rID1==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo 0;
	}
	disconnect($con);
    die;

}

 
else if ($action=="import_document_acceptance_approval_controller_v2")
{
    /*
        //approval/migrate/app_migrate_api.php?action=import_document_acceptance_approval_controller_v2
    */

    $entry_form = 38;
    
    $old_data_sql="select  max(id) as ID from approval_mst where entry_form = $entry_form";
    $old_data_sql_res=sql_select($old_data_sql);
    if( $old_data_sql_res[0]['ID']){
        echo "Old data found, Not allow migration. If you migrate you will lost your all data.<br> [পুরানো ডেটা পাওয়া গেছে, মাইগ্রেশনের অনুমতি নেই৷ আপনি মাইগ্রেট করলে আপনার সমস্ত ডেটা হারিয়ে যাবে।]";
        exit();
    }

    $tmpSql = "select id from com_import_invoice_mst where APPROVED=1 and STATUS_ACTIVE=1 and id not in( select MST_ID from APPROVAL_MST where entry_form= $entry_form)";


    $appHisSql="select MST_ID,APPROVED_BY,SEQUENCE_NO,INSERTED_BY from APPROVAL_HISTORY where ENTRY_FORM= $entry_form and CURRENT_APPROVAL_STATUS=1 and MST_ID in($tmpSql) and SEQUENCE_NO is not null ";
    $appHisSqlRes=sql_select($appHisSql);
    //print_r($appHisSqlRes);die;

    $appHisSqlNewRes = array_chunk($appHisSqlRes,200);
    $id=return_next_id( "id","approval_mst", 1 ) ;
    $con = connect();
    $flag=1;
    foreach($appHisSqlNewRes as $appHisSqlChankRes){
        $data_array=''; $target_app_id_arr=array();$data_array_up=array();
        
        foreach($appHisSqlChankRes as $row){
            $row['INSERTED_BY'] =($row['INSERTED_BY'])?$row['INSERTED_BY']:$_SESSION['logic_erp']['user_id'];
            
            $data_array_up[$row['MST_ID']] = explode(",",("".$row['SEQUENCE_NO']."")); 
            $target_app_id_arr[]=$row['MST_ID'];
            //......................

            if($data_array!=''){$data_array.=",";}
            $data_array.="(".$id.", $entry_form,".$row['MST_ID'].",".$row['SEQUENCE_NO'].",".$row['APPROVED_BY'].",'".$pc_date_time."','".$row['INSERTED_BY']."','".$pc_date_time."','".$user_ip."')"; 
            $id=$id+1;
        }

        if($flag == 1){
            $field_array_up="APPROVED_SEQU_BY"; 
            $multi_up_sql=bulk_update_sql_statement( "com_import_invoice_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr,1 );
           // echo $multi_up_sql;die;
            $rID=execute_query($multi_up_sql); 
            if($rID){$flag=1;}else{$flag=0;}
        }

        if($flag == 1){
            $field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
            $rID1=sql_insert("approval_mst",$field_array,$data_array,0);
            if($rID1){$flag=1;}else{$flag=0;}
        }
        

    }


	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo 0;
	}
	disconnect($con);
    die;

}


else if ($action=="yarn_requisition_approval_controller_v2")
{
    /*
        //approval/migrate/app_migrate_api.php?action=yarn_requisition_approval_controller_v2
    */

    $entry_form = 20;
    
    $old_data_sql="select  max(id) as ID from approval_mst where entry_form = $entry_form";
    $old_data_sql_res=sql_select($old_data_sql);
    if( $old_data_sql_res[0]['ID']){
        echo "Old data found, Not allow migration. If you migrate you will lost your all data.<br> [পুরানো ডেটা পাওয়া গেছে, মাইগ্রেশনের অনুমতি নেই৷ আপনি মাইগ্রেট করলে আপনার সমস্ত ডেটা হারিয়ে যাবে।]";
        exit();
    }

    $tmpSql = "select id from inv_purchase_requisition_mst where IS_APPROVED=1 and STATUS_ACTIVE=1 and id not in( select MST_ID from APPROVAL_MST where entry_form= $entry_form)";


    $appHisSql="select MST_ID,APPROVED_BY,SEQUENCE_NO,INSERTED_BY from APPROVAL_HISTORY where ENTRY_FORM= $entry_form and CURRENT_APPROVAL_STATUS=1 and MST_ID in($tmpSql) and SEQUENCE_NO is not null ";
    $appHisSqlRes=sql_select($appHisSql);
    //print_r($appHisSqlRes);die;

    $appHisSqlNewRes = array_chunk($appHisSqlRes,200);
    $id=return_next_id( "id","approval_mst", 1 ) ;
    $con = connect();
    $flag=1;
    foreach($appHisSqlNewRes as $appHisSqlChankRes){
        $data_array=''; $target_app_id_arr=array();$data_array_up=array();
        
        foreach($appHisSqlChankRes as $row){
            $row['INSERTED_BY'] =($row['INSERTED_BY'])?$row['INSERTED_BY']:$_SESSION['logic_erp']['user_id'];
            
            $data_array_up[$row['MST_ID']] = explode(",",("".$row['SEQUENCE_NO']."")); 
            $target_app_id_arr[]=$row['MST_ID'];
            //......................

            if($data_array!=''){$data_array.=",";}
            $data_array.="(".$id.", $entry_form,".$row['MST_ID'].",".$row['SEQUENCE_NO'].",".$row['APPROVED_BY'].",'".$pc_date_time."','".$row['INSERTED_BY']."','".$pc_date_time."','".$user_ip."')"; 
            $id=$id+1;
        }

        if($flag == 1){
            $field_array_up="APPROVED_SEQU_BY"; 
            $multi_up_sql=bulk_update_sql_statement( "inv_purchase_requisition_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr,1 );
           // echo $multi_up_sql;die;
            $rID=execute_query($multi_up_sql); 
            if($rID){$flag=1;}else{$flag=0;}
        }

        if($flag == 1){
            $field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
            $rID1=sql_insert("approval_mst",$field_array,$data_array,0);
            if($rID1){$flag=1;}else{$flag=0;}
        }
        

    }


	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo 0;
	}
	disconnect($con);
    die;

}
 
else if ($action=="price_quatation_approval_group_by_controller")
{
    /*
        //approval/migrate/app_migrate_api.php?action=price_quatation_approval_group_by_controller
    */

    $entry_form = 10;
    
    $old_data_sql="select  max(id) as ID from approval_mst where entry_form = $entry_form";
    $old_data_sql_res=sql_select($old_data_sql);
    if( $old_data_sql_res[0]['ID']){
       echo "Old data found, Not allow migration. If you migrate you will lost your all data.<br> [পুরানো ডেটা পাওয়া গেছে, মাইগ্রেশনের অনুমতি নেই৷ আপনি মাইগ্রেট করলে আপনার সমস্ত ডেটা হারিয়ে যাবে।]";
       exit();
    }


  $elec_setup_data_sql = "SELECT COMPANY_ID,USER_ID,SEQUENCE_NO,GROUP_NO FROM ELECTRONIC_APPROVAL_SETUP WHERE ENTRY_FORM = $entry_form AND  GROUP_NO IS NOT NULL AND IS_DELETED=0";
    $elec_setup_data_sql_res = sql_select($elec_setup_data_sql);
    foreach( $elec_setup_data_sql_res as $row){
        $electronic_app_data_arr[$row['COMPANY_ID']][$row['USER_ID']] = $row;
    }  

    $tmpSql = "select id from wo_price_quotation where APPROVED=1 and STATUS_ACTIVE=1 and id not in( select MST_ID from APPROVAL_MST where entry_form= $entry_form)";


    $appHisSql="select b.COMPANY_ID,a.MST_ID,a.APPROVED_BY,a.SEQUENCE_NO,a.INSERTED_BY from APPROVAL_HISTORY a,WO_PRICE_QUOTATION b where a.mst_id = b.id and a.ENTRY_FORM= $entry_form and a.CURRENT_APPROVAL_STATUS=1 and a.MST_ID in($tmpSql) and a.SEQUENCE_NO is not null ";
    $appHisSqlRes=sql_select($appHisSql);
     //print_r($appHisSql);die;

    $appHisSqlNewRes = array_chunk($appHisSqlRes,900);
    $id=return_next_id( "id","approval_mst", 1 ) ;
    $con = connect();
    $flag=1;
    foreach($appHisSqlNewRes as $appHisSqlChankRes){
        $data_array=''; $target_app_id_arr=array();$data_array_up=array();
        
        foreach($appHisSqlChankRes as $row){
            $row['INSERTED_BY'] = ($row['INSERTED_BY']) ? $row['INSERTED_BY'] : $_SESSION['logic_erp']['user_id'];
            $row['GROUP_NO'] = $electronic_app_data_arr[$row['COMPANY_ID']][$row['APPROVED_BY']]['GROUP_NO']*1;
            $row['SEQUENCE_NO'] = $electronic_app_data_arr[$row['COMPANY_ID']][$row['APPROVED_BY']]['SEQUENCE_NO']*1;
            if($row['SEQUENCE_NO'] != '' && $row['GROUP_NO'] != ''){
                $data_array_up[$row['MST_ID']] = explode(",",("".$row['SEQUENCE_NO'].",".$row['GROUP_NO']."")); 
                $target_app_id_arr[]=$row['MST_ID'];
                //......................

                if($data_array!=''){$data_array.=",";}
                $data_array.="(".$id.", $entry_form,".$row['MST_ID'].",".$row['SEQUENCE_NO'].",".$row['GROUP_NO'].",".$row['APPROVED_BY'].",'".$pc_date_time."','".$row['INSERTED_BY']."','".$pc_date_time."','".$user_ip."')"; 
                $id=$id+1;
            }
            

        }


        //print_r($data_array_up);die;

        if($flag == 1){
            $field_array_up="APPROVED_SEQU_BY*APPROVED_GROUP_BY"; 
            $multi_up_sql=bulk_update_sql_statement( "wo_price_quotation", "id", $field_array_up, $data_array_up, $target_app_id_arr,1 );
           //echo $multi_up_sql;die;
            $rID=execute_query($multi_up_sql); 
            if($rID){$flag=1;}else{$flag=0;}
        }

        if($flag == 1){
            $field_array="id, entry_form, mst_id,SEQUENCE_NO,GROUP_NO,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
            $rID1=sql_insert("approval_mst",$field_array,$data_array,0);
            if($rID1){$flag=1;}else{$flag=0;}
        }
        
        
    }


	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo 0;
	}
	disconnect($con);
    die;

}

else if ($action=="fabric_booking_approval_group_by_controller")
{
    /*
        //approval/migrate/app_migrate_api.php?action=fabric_booking_approval_group_by_controller
    */

    $entry_form = 7;
    
    $old_data_sql="select  max(id) as ID from approval_mst where entry_form = $entry_form";
    $old_data_sql_res=sql_select($old_data_sql);
    if( $old_data_sql_res[0]['ID']){
       echo "Old data found, Not allow migration. If you migrate you will lost your all data.<br> [পুরানো ডেটা পাওয়া গেছে, মাইগ্রেশনের অনুমতি নেই৷ আপনি মাইগ্রেট করলে আপনার সমস্ত ডেটা হারিয়ে যাবে।]";
       exit();
    }


  $elec_setup_data_sql = "SELECT COMPANY_ID,USER_ID,SEQUENCE_NO,GROUP_NO FROM ELECTRONIC_APPROVAL_SETUP WHERE ENTRY_FORM = $entry_form AND  GROUP_NO IS NOT NULL AND IS_DELETED=0";
    $elec_setup_data_sql_res = sql_select($elec_setup_data_sql);
    foreach( $elec_setup_data_sql_res as $row){
        $electronic_app_data_arr[$row['COMPANY_ID']][$row['USER_ID']] = $row;
    }  

    $tmpSql = "select id from WO_BOOKING_MST where IS_APPROVED=1 and STATUS_ACTIVE=1 and id not in( select MST_ID from APPROVAL_MST where entry_form= $entry_form)";


    $appHisSql="select b.COMPANY_ID,a.MST_ID,a.APPROVED_BY,a.SEQUENCE_NO,a.INSERTED_BY from APPROVAL_HISTORY a,WO_BOOKING_MST b where a.mst_id = b.id and a.ENTRY_FORM= $entry_form and a.CURRENT_APPROVAL_STATUS=1 and a.MST_ID in($tmpSql) and a.SEQUENCE_NO is not null ";
    $appHisSqlRes=sql_select($appHisSql);
     //print_r($appHisSql);die;

    $appHisSqlNewRes = array_chunk($appHisSqlRes,900);
    $id=return_next_id( "id","approval_mst", 1 ) ;
    $con = connect();
    $flag=1;
    foreach($appHisSqlNewRes as $appHisSqlChankRes){
        $data_array=''; $target_app_id_arr=array();$data_array_up=array();
        
        foreach($appHisSqlChankRes as $row){
            $row['INSERTED_BY'] = ($row['INSERTED_BY']) ? $row['INSERTED_BY'] : $_SESSION['logic_erp']['user_id'];
            $row['GROUP_NO'] = $electronic_app_data_arr[$row['COMPANY_ID']][$row['APPROVED_BY']]['GROUP_NO']*1;
            $row['SEQUENCE_NO'] = $electronic_app_data_arr[$row['COMPANY_ID']][$row['APPROVED_BY']]['SEQUENCE_NO']*1;
            if($row['SEQUENCE_NO'] != '' && $row['GROUP_NO'] != ''){
                $data_array_up[$row['MST_ID']] = explode(",",("".$row['SEQUENCE_NO'].",".$row['GROUP_NO']."")); 
                $target_app_id_arr[]=$row['MST_ID'];
                //......................

                if($data_array!=''){$data_array.=",";}
                $data_array.="(".$id.", $entry_form,".$row['MST_ID'].",".$row['SEQUENCE_NO'].",".$row['GROUP_NO'].",".$row['APPROVED_BY'].",'".$pc_date_time."','".$row['INSERTED_BY']."','".$pc_date_time."','".$user_ip."')"; 
                $id=$id+1;
            }

        }


        //print_r($data_array_up);die;

        if($flag == 1){
            $field_array_up="APPROVED_SEQU_BY*APPROVED_GROUP_BY"; 
            $multi_up_sql=bulk_update_sql_statement( "WO_BOOKING_MST", "id", $field_array_up, $data_array_up, $target_app_id_arr,1 );
            //echo $multi_up_sql;die;
            $rID=execute_query($multi_up_sql); 
            if($rID){$flag=1;}else{$flag=0;}
        }

        if($flag == 1){
            $field_array="id, entry_form, mst_id,SEQUENCE_NO,GROUP_NO,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
            $rID1=sql_insert("approval_mst",$field_array,$data_array,0);
            if($rID1){$flag=1;}else{$flag=0;}
        }
        
        
    }


	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo 0;
	}
	disconnect($con);
    die;

}


 

else if ($action=="purchase_requisition_approval_controller_v2")
{
    /*
        /approval/migrate/app_migrate_api.php?action=purchase_requisition_approval_controller_v2
    */

    $entry_form = 1;
    
     $old_data_sql="select  max(id) as ID from approval_mst where entry_form = $entry_form";
     $old_data_sql_res=sql_select($old_data_sql);
     if( $old_data_sql_res[0]['ID']){
       echo "Old data found, Not allow migration. If you migrate you will lost your all data.<br> [পুরানো ডেটা পাওয়া গেছে, মাইগ্রেশনের অনুমতি নেই৷ আপনি মাইগ্রেট করলে আপনার সমস্ত ডেটা হারিয়ে যাবে।]";
       exit();
     }

    //Current setup...........
    $seq_data_sql="select USER_ID,SEQUENCE_NO from ELECTRONIC_APPROVAL_SETUP where IS_DELETED=0 and entry_form = $entry_form order by id";
    $seq_data_sql_res=sql_select($seq_data_sql);
    foreach($seq_data_sql_res as $row){
        $seq_user_seq_arr[$row['USER_ID']] = $row['SEQUENCE_NO'];
    }

   // print_r($seq_user_seq_arr);die;

    $tmpSql = "select id from inv_purchase_requisition_mst where IS_APPROVED=1 and STATUS_ACTIVE=1 and id not in( select MST_ID from APPROVAL_MST where entry_form=$entry_form)";
    
    $appHisSql="select MST_ID,APPROVED_BY,SEQUENCE_NO from APPROVAL_HISTORY where ENTRY_FORM=$entry_form and CURRENT_APPROVAL_STATUS=1 and MST_ID in($tmpSql)";
   // echo $appHisSql;die;
    $appHisSqlRes=sql_select($appHisSql);
    
    $appHisSqlNewRes = array_chunk($appHisSqlRes,500);

    foreach($appHisSqlNewRes as $appHisSqlChankRes){

        $id=return_next_id( "id","approval_mst", 1 );
        $con = connect();
        $data_array_up = array(); $target_app_id_arr = array();
        foreach($appHisSqlChankRes as $row){
            $row['SEQUENCE_NO'] = $seq_user_seq_arr[$row['APPROVED_BY']];
            $data_array_up[$row['MST_ID']] = explode(",",("".$row['SEQUENCE_NO']."")); 
            $target_app_id_arr[]=$row['MST_ID'];
            //......................
    
            if($data_array!=''){$data_array.=",";}
            $data_array.="(".$id.",$entry_form,".$row['MST_ID'].",".$row['SEQUENCE_NO'].",".$row['APPROVED_BY'].",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
            $id=$id+1;
        }


        $field_array_up="APPROVED_SEQU_BY"; 
        $multi_up_sql=bulk_update_sql_statement( "inv_purchase_requisition_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr );
        //echo $multi_up_sql;die;
        $rID=execute_query($multi_up_sql);


        $field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
        $rID1=sql_insert("approval_mst",$field_array,$data_array,0);

    } 
    
    
   // echo $rID.'=='.$rID1;oci_rollback($con);die;
    
    
    
    if($rID==1 and $rID1==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo 0;
	}
	disconnect($con);
    die;

}

else if ($action=="gate_pass_entry_approval_v2_controller")
{
    /*
        /approval/migrate/app_migrate_api.php?action=gate_pass_entry_approval_v2_controller
    */

    $entry_form = 59;
    
     $old_data_sql="select  max(id) as ID from approval_mst where entry_form = $entry_form";
     $old_data_sql_res=sql_select($old_data_sql);
     if( $old_data_sql_res[0]['ID']){
       echo "Old data found, Not allow migration. If you migrate you will lost your all data.<br> [পুরানো ডেটা পাওয়া গেছে, মাইগ্রেশনের অনুমতি নেই৷ আপনি মাইগ্রেট করলে আপনার সমস্ত ডেটা হারিয়ে যাবে।]";
       exit();
     }

    //Current setup...........
    $seq_data_sql="select COMPANY_ID,USER_ID,SEQUENCE_NO from ELECTRONIC_APPROVAL_SETUP where IS_DELETED=0 and entry_form = $entry_form order by id";
    $seq_data_sql_res=sql_select($seq_data_sql);
    foreach($seq_data_sql_res as $row){
        $seq_user_seq_arr[$row['COMPANY_ID']][$row['USER_ID']] = $row['SEQUENCE_NO'];
    }

   // print_r($seq_user_seq_arr);die;

 
    
    $appHisSql="select a.MST_ID,a.APPROVED_BY,a.SEQUENCE_NO,b.COMPANY_ID from APPROVAL_HISTORY a,INV_GATE_PASS_MST b where b.id=a.mst_id and a.ENTRY_FORM=$entry_form and a.CURRENT_APPROVAL_STATUS=1 and b.id not in( select MST_ID from APPROVAL_MST where entry_form=$entry_form)";
   // echo $appHisSql;die;
    $appHisSqlRes=sql_select($appHisSql);
    
    $appHisSqlNewRes = array_chunk($appHisSqlRes,500);

    foreach($appHisSqlNewRes as $appHisSqlChankRes){

        $id=return_next_id( "id","approval_mst", 1 );
        $con = connect();
        $data_array_up = array(); $target_app_id_arr = array();
        foreach($appHisSqlChankRes as $row){
            $row['SEQUENCE_NO'] = $seq_user_seq_arr[$row['COMPANY_ID']][$row['APPROVED_BY']];
            $data_array_up[$row['MST_ID']] = explode(",",("".$row['SEQUENCE_NO']."")); 
            $target_app_id_arr[]=$row['MST_ID'];
            //......................
    
            if($data_array!=''){$data_array.=",";}
            $data_array.="(".$id.",$entry_form,".$row['MST_ID'].",".$row['SEQUENCE_NO'].",".$row['APPROVED_BY'].",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
            $id=$id+1;
        }


        $field_array_up="APPROVED_SEQU_BY"; 
        $multi_up_sql=bulk_update_sql_statement( "INV_GATE_PASS_MST", "id", $field_array_up, $data_array_up, $target_app_id_arr );
        //echo $multi_up_sql;die;
        $rID=execute_query($multi_up_sql);


        $field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
        $rID1=sql_insert("approval_mst",$field_array,$data_array,0);

    } 
    
    
   // echo $rID.'=='.$rID1;oci_rollback($con);die;
    
    
    
    if($rID==1 and $rID1==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo 0;
	}
	disconnect($con);
    die;

}



else if ($action=="other_purchase_work_order_approval_v2_controller")
{
    /*
        /approval/migrate/app_migrate_api.php?action=other_purchase_work_order_approval_v2_controller
    */

    $entry_form = 17;
    
     $old_data_sql="select  max(id) as ID from approval_mst where entry_form = $entry_form";
     $old_data_sql_res=sql_select($old_data_sql);
     if( $old_data_sql_res[0]['ID']){
       echo "Old data found, Not allow migration. If you migrate you will lost your all data.<br> [পুরানো ডেটা পাওয়া গেছে, মাইগ্রেশনের অনুমতি নেই৷ আপনি মাইগ্রেট করলে আপনার সমস্ত ডেটা হারিয়ে যাবে।]";
       exit();
     }

    //Current setup...........
    $seq_data_sql="select COMPANY_ID,USER_ID,SEQUENCE_NO from ELECTRONIC_APPROVAL_SETUP where IS_DELETED=0 and entry_form = $entry_form order by id";
    $seq_data_sql_res=sql_select($seq_data_sql);
    foreach($seq_data_sql_res as $row){
        $seq_user_seq_arr[$row['COMPANY_ID']][$row['USER_ID']] = $row['SEQUENCE_NO'];
    }

   // print_r($seq_user_seq_arr);die;

 
    
    $appHisSql="select a.MST_ID,a.APPROVED_BY,a.SEQUENCE_NO,b.COMPANY_NAME as COMPANY_ID from APPROVAL_HISTORY a,WO_NON_ORDER_INFO_MST b, wo_non_order_info_dtls c where b.id=c.mst_id and b.id=a.mst_id and a.ENTRY_FORM=$entry_form and a.CURRENT_APPROVAL_STATUS=1 and b.entry_form=147 and c.item_category_id not in(1,5,6,7,23) and b.id not in( select MST_ID from APPROVAL_MST where entry_form=$entry_form)";
   // echo $appHisSql;die;
    $appHisSqlRes=sql_select($appHisSql);
    
    $appHisSqlNewRes = array_chunk($appHisSqlRes,500);

    $id=return_next_id( "id","approval_mst", 1 );
    $con = connect();

    foreach($appHisSqlNewRes as $appHisSqlChankRes){
        $data_array = '';
        $data_array_up = array(); $target_app_id_arr = array();
        foreach($appHisSqlChankRes as $row){
            $row['SEQUENCE_NO'] = $seq_user_seq_arr[$row['COMPANY_ID']][$row['APPROVED_BY']];
           if($row['SEQUENCE_NO']){
                $data_array_up[$row['MST_ID']] = explode(",",("".$row['SEQUENCE_NO']."")); 
                $target_app_id_arr[]=$row['MST_ID'];
                //......................
        
                if($data_array!=''){$data_array.=",";}
                $data_array.="(".$id.",$entry_form,".$row['MST_ID'].",".$row['SEQUENCE_NO'].",".$row['APPROVED_BY'].",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
                $id=$id+1;
           }

        }


        $field_array_up="APPROVED_SEQU_BY"; 
        $multi_up_sql=bulk_update_sql_statement( "WO_NON_ORDER_INFO_MST", "id", $field_array_up, $data_array_up, $target_app_id_arr );
         //echo $multi_up_sql;die;
        $rID=execute_query($multi_up_sql);


        $field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
        $rID1=sql_insert("approval_mst",$field_array,$data_array,0);

    } 
    
    
   //echo $rID.'=='.$rID1;oci_rollback($con);die;
    
    
    
    if($rID==1 and $rID1==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo 0;
	}
	disconnect($con);
    die;

}

else if ($action=="short_feb_booking_approval_group_by_controller")
{
    /* 
        //approval/migrate/app_migrate_api.php?action=short_feb_booking_approval_group_by_controller
    */

    $entry_form = 12;
    
    $old_data_sql="select  max(id) as ID from approval_mst where entry_form = $entry_form";
    $old_data_sql_res=sql_select($old_data_sql);
    if( $old_data_sql_res[0]['ID']){
       echo "Old data found, Not allow migration. If you migrate you will lost your all data.<br> [পুরানো ডেটা পাওয়া গেছে, মাইগ্রেশনের অনুমতি নেই৷ আপনি মাইগ্রেট করলে আপনার সমস্ত ডেটা হারিয়ে যাবে।]";
       exit();
    }


  $elec_setup_data_sql = "SELECT COMPANY_ID,USER_ID,SEQUENCE_NO,GROUP_NO FROM ELECTRONIC_APPROVAL_SETUP WHERE ENTRY_FORM = $entry_form AND  GROUP_NO IS NOT NULL AND IS_DELETED=0";
    $elec_setup_data_sql_res = sql_select($elec_setup_data_sql);
    foreach( $elec_setup_data_sql_res as $row){
        $electronic_app_data_arr[$row['COMPANY_ID']][$row['USER_ID']] = $row;
    }  

    $tmpSql = "select id from WO_BOOKING_MST where IS_APPROVED=1 and STATUS_ACTIVE=1 and is_short=1 and booking_type=1 and id not in( select MST_ID from APPROVAL_MST where entry_form= $entry_form)";


    $appHisSql="select b.COMPANY_ID,a.MST_ID,a.APPROVED_BY,a.SEQUENCE_NO,a.INSERTED_BY from APPROVAL_HISTORY a,WO_BOOKING_MST b where a.mst_id = b.id and a.ENTRY_FORM= $entry_form and b.is_short=1 and b.booking_type=1 and a.CURRENT_APPROVAL_STATUS=1 and a.MST_ID in($tmpSql) and a.SEQUENCE_NO is not null ";
    $appHisSqlRes=sql_select($appHisSql);
     //print_r($appHisSql);die;

    $appHisSqlNewRes = array_chunk($appHisSqlRes,900);
    $id=return_next_id( "id","approval_mst", 1 ) ;
    $con = connect();
    $flag=1;
    foreach($appHisSqlNewRes as $appHisSqlChankRes){
        $data_array=''; $target_app_id_arr=array();$data_array_up=array();
        
        foreach($appHisSqlChankRes as $row){
            $row['INSERTED_BY'] = ($row['INSERTED_BY']) ? $row['INSERTED_BY'] : $_SESSION['logic_erp']['user_id'];
            $row['GROUP_NO'] = $electronic_app_data_arr[$row['COMPANY_ID']][$row['APPROVED_BY']]['GROUP_NO']*1;
            $row['SEQUENCE_NO'] = $electronic_app_data_arr[$row['COMPANY_ID']][$row['APPROVED_BY']]['SEQUENCE_NO']*1;

            if($row['SEQUENCE_NO'] != '' && $row['GROUP_NO'] != ''){
                $data_array_up[$row['MST_ID']] = explode(",",("".$row['SEQUENCE_NO'].",".$row['GROUP_NO']."")); 
                $target_app_id_arr[]=$row['MST_ID'];
                //......................

                if($data_array!=''){$data_array.=",";}
                $data_array.="(".$id.", $entry_form,".$row['MST_ID'].",".$row['SEQUENCE_NO'].",".$row['GROUP_NO'].",".$row['APPROVED_BY'].",'".$pc_date_time."','".$row['INSERTED_BY']."','".$pc_date_time."','".$user_ip."')"; 
                $id=$id+1;
            }
            

        }


        //print_r($data_array_up);die;

        if($flag == 1){
            $field_array_up="APPROVED_SEQU_BY*APPROVED_GROUP_BY"; 
            $multi_up_sql=bulk_update_sql_statement( "WO_BOOKING_MST", "id", $field_array_up, $data_array_up, $target_app_id_arr,1 );
           //echo $multi_up_sql;die;
            $rID=execute_query($multi_up_sql); 
            if($rID){$flag=1;}else{$flag=0;}
        }

        if($flag == 1){
            $field_array="id, entry_form, mst_id,SEQUENCE_NO,GROUP_NO,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
            $rID1=sql_insert("approval_mst",$field_array,$data_array,0);
            if($rID1){$flag=1;}else{$flag=0;}
        }
        
        
    }


	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo 0;
	}
	disconnect($con);
    die;

}

else if ($action=="pre_costing_approval_group_by_controller")
{
    /*
        //approval/migrate/app_migrate_api.php?action=pre_costing_approval_group_by_controller
    */

    $entry_form = 77;
    
    $old_data_sql="select  max(id) as ID from approval_mst where entry_form = $entry_form";
    $old_data_sql_res=sql_select($old_data_sql);
    if( $old_data_sql_res[0]['ID']){
       echo "Old data found, Not allow migration. If you migrate you will lost your all data.<br> [পুরানো ডেটা পাওয়া গেছে, মাইগ্রেশনের অনুমতি নেই৷ আপনি মাইগ্রেট করলে আপনার সমস্ত ডেটা হারিয়ে যাবে।]";
       exit();
    }


  $elec_setup_data_sql = "SELECT COMPANY_ID,USER_ID,SEQUENCE_NO,GROUP_NO FROM ELECTRONIC_APPROVAL_SETUP WHERE ENTRY_FORM = $entry_form AND  GROUP_NO IS NOT NULL AND IS_DELETED=0";
    $elec_setup_data_sql_res = sql_select($elec_setup_data_sql);
    foreach( $elec_setup_data_sql_res as $row){
        $electronic_app_data_arr[$row['COMPANY_ID']][$row['USER_ID']] = $row;
    }  

    $tmpSql = "select id from wo_pre_cost_mst where approved=1 and STATUS_ACTIVE=1 and id not in( select MST_ID from APPROVAL_MST where entry_form= $entry_form)";


    $appHisSql="select c.company_name as COMPANY_ID,a.MST_ID,a.APPROVED_BY,a.SEQUENCE_NO,a.INSERTED_BY from APPROVAL_HISTORY a,wo_pre_cost_mst b,wo_po_details_master c where a.mst_id = b.id and c.id=b.job_id and a.ENTRY_FORM=15 and a.CURRENT_APPROVAL_STATUS=1 and b.is_deleted=0 and b.status_active=1 and a.MST_ID in($tmpSql) and a.SEQUENCE_NO is not null ";
    $appHisSqlRes=sql_select($appHisSql);
     //print_r($appHisSql);die;

    $appHisSqlNewRes = array_chunk($appHisSqlRes,900);
    $id=return_next_id( "id","approval_mst", 1 ) ;
    $con = connect();
    $flag=1;
    foreach($appHisSqlNewRes as $appHisSqlChankRes){
        $data_array=''; $target_app_id_arr=array();$data_array_up=array();
        
        foreach($appHisSqlChankRes as $row){
            $row['INSERTED_BY'] = ($row['INSERTED_BY']) ? $row['INSERTED_BY'] : $_SESSION['logic_erp']['user_id'];
            $row['GROUP_NO'] = $electronic_app_data_arr[$row['COMPANY_ID']][$row['APPROVED_BY']]['GROUP_NO']*1;
            $row['SEQUENCE_NO'] = $electronic_app_data_arr[$row['COMPANY_ID']][$row['APPROVED_BY']]['SEQUENCE_NO']*1;

            if($row['SEQUENCE_NO'] != '' && $row['GROUP_NO'] != ''){
                $data_array_up[$row['MST_ID']] = explode(",",("".$row['SEQUENCE_NO'].",".$row['GROUP_NO']."")); 
                $target_app_id_arr[]=$row['MST_ID'];
                //......................

                if($data_array!=''){$data_array.=",";}
                $data_array.="(".$id.", $entry_form,".$row['MST_ID'].",".$row['SEQUENCE_NO'].",".$row['GROUP_NO'].",".$row['APPROVED_BY'].",'".$pc_date_time."','".$row['INSERTED_BY']."','".$pc_date_time."','".$user_ip."')"; 
                $id=$id+1;
            }
            

        }


        //print_r($data_array_up);die;

        if($flag == 1){
            $field_array_up="APPROVED_SEQU_BY*APPROVED_GROUP_BY"; 
            $multi_up_sql=bulk_update_sql_statement( "wo_pre_cost_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr,1 );
           //echo $multi_up_sql;die;
            $rID=execute_query($multi_up_sql); 
            if($rID){$flag=1;}else{$flag=0;}
        }

        if($flag == 1){
            $field_array="id, entry_form, mst_id,SEQUENCE_NO,GROUP_NO,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
            $rID1=sql_insert("approval_mst",$field_array,$data_array,0);
            if($rID1){$flag=1;}else{$flag=0;}
        }
        
        
    }


	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo 0;
	}
	disconnect($con);
    die;

}

else if ($action=="sample_additional_yarn_workorder_approval_group_by_controller")
{
    /*
        //approval/migrate/app_migrate_api.php?action=sample_additional_yarn_workorder_approval_group_by_controller
    */

    $entry_form = 53;
    
    $old_data_sql="select  max(id) as ID from approval_mst where entry_form = $entry_form";
    $old_data_sql_res=sql_select($old_data_sql);
    if( $old_data_sql_res[0]['ID']){
       echo "Old data found, Not allow migration. If you migrate you will lost your all data.<br> [পুরানো ডেটা পাওয়া গেছে, মাইগ্রেশনের অনুমতি নেই৷ আপনি মাইগ্রেট করলে আপনার সমস্ত ডেটা হারিয়ে যাবে।]";
       exit();
    }


  $elec_setup_data_sql = "SELECT COMPANY_ID,USER_ID,SEQUENCE_NO,GROUP_NO FROM ELECTRONIC_APPROVAL_SETUP WHERE ENTRY_FORM = $entry_form AND  GROUP_NO IS NOT NULL AND IS_DELETED=0";
    $elec_setup_data_sql_res = sql_select($elec_setup_data_sql);
    foreach( $elec_setup_data_sql_res as $row){
        $electronic_app_data_arr[$row['COMPANY_ID']][$row['USER_ID']] = $row;
    }  

    $tmpSql = "select id from wo_non_order_info_mst where is_approved=1 and STATUS_ACTIVE=1 and entry_form=284 and id not in( select MST_ID from APPROVAL_MST where entry_form= $entry_form)";
    //echo $tmpSql;die;


    $appHisSql="select b.COMPANY_NAME as COMPANY_ID,a.MST_ID,a.APPROVED_BY,a.SEQUENCE_NO,a.INSERTED_BY from APPROVAL_HISTORY a,wo_non_order_info_mst b where a.mst_id = b.id and b.entry_form=284 and a.ENTRY_FORM=$entry_form and a.CURRENT_APPROVAL_STATUS=1 and b.is_deleted=0 and b.status_active=1 and a.MST_ID in($tmpSql) and a.SEQUENCE_NO is not null ";
    $appHisSqlRes=sql_select($appHisSql);
     // print_r($appHisSql);die;

    $appHisSqlNewRes = array_chunk($appHisSqlRes,900);
    $id=return_next_id( "id","approval_mst", 1 ) ;
    $con = connect();
    $flag=1;
    foreach($appHisSqlNewRes as $appHisSqlChankRes){
        $data_array=''; $target_app_id_arr=array();$data_array_up=array();
        
        foreach($appHisSqlChankRes as $row){
            $row['INSERTED_BY'] = ($row['INSERTED_BY']) ? $row['INSERTED_BY'] : $_SESSION['logic_erp']['user_id'];
            $row['GROUP_NO'] = $electronic_app_data_arr[$row['COMPANY_ID']][$row['APPROVED_BY']]['GROUP_NO']*1;
            $row['SEQUENCE_NO'] = $electronic_app_data_arr[$row['COMPANY_ID']][$row['APPROVED_BY']]['SEQUENCE_NO']*1;

            if($row['SEQUENCE_NO'] != '' && $row['GROUP_NO'] != ''){
                $data_array_up[$row['MST_ID']] = explode(",",("".$row['SEQUENCE_NO'].",".$row['GROUP_NO']."")); 
                $target_app_id_arr[]=$row['MST_ID'];
                //......................

                if($data_array!=''){$data_array.=",";}
                $data_array.="(".$id.", $entry_form,".$row['MST_ID'].",".$row['SEQUENCE_NO'].",".$row['GROUP_NO'].",".$row['APPROVED_BY'].",'".$pc_date_time."','".$row['INSERTED_BY']."','".$pc_date_time."','".$user_ip."')"; 
                $id=$id+1;
            }
            

        }


        //print_r($data_array_up);die;

        if($flag == 1){
            $field_array_up="APPROVED_SEQU_BY*APPROVED_GROUP_BY"; 
            $multi_up_sql=bulk_update_sql_statement( "wo_non_order_info_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr,1 );
           //echo $multi_up_sql;die;
            $rID=execute_query($multi_up_sql); 
            if($rID){$flag=1;}else{$flag=0;}
        }

        if($flag == 1){
            $field_array="id, entry_form, mst_id,SEQUENCE_NO,GROUP_NO,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
            $rID1=sql_insert("approval_mst",$field_array,$data_array,0);
            if($rID1){$flag=1;}else{$flag=0;}
        }
        
        
    }


	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo 0;
	}
	disconnect($con);
    die;

}



else if ($action=="yarn_work_order_approval_group_by_controller")
{
    /*
        //approval/migrate/app_migrate_api.php?action=yarn_work_order_approval_group_by_controller
    */

    $entry_form = 2;
    
    $old_data_sql="select  max(id) as ID from approval_mst where entry_form = $entry_form";
    $old_data_sql_res=sql_select($old_data_sql);
    if( $old_data_sql_res[0]['ID']){
       echo "Old data found, Not allow migration. If you migrate you will lost your all data.<br> [পুরানো ডেটা পাওয়া গেছে, মাইগ্রেশনের অনুমতি নেই৷ আপনি মাইগ্রেট করলে আপনার সমস্ত ডেটা হারিয়ে যাবে।]";
       exit();
    }


  $elec_setup_data_sql = "SELECT COMPANY_ID,USER_ID,SEQUENCE_NO,GROUP_NO FROM ELECTRONIC_APPROVAL_SETUP WHERE ENTRY_FORM = $entry_form AND  GROUP_NO IS NOT NULL AND IS_DELETED=0";
    $elec_setup_data_sql_res = sql_select($elec_setup_data_sql);
    foreach( $elec_setup_data_sql_res as $row){
        $electronic_app_data_arr[$row['COMPANY_ID']][$row['USER_ID']] = $row;
    }  

    $tmpSql = "select id from wo_non_order_info_mst where is_approved in(1,3) and STATUS_ACTIVE=1 and entry_form=144 and id not in( select MST_ID from APPROVAL_MST where entry_form= $entry_form)";
    //echo $tmpSql;die;


    $appHisSql="select b.COMPANY_NAME as COMPANY_ID,a.MST_ID,a.APPROVED_BY,a.SEQUENCE_NO,a.INSERTED_BY from APPROVAL_HISTORY a,wo_non_order_info_mst b where a.mst_id = b.id  and b.entry_form=144 and a.ENTRY_FORM=$entry_form and a.CURRENT_APPROVAL_STATUS=1 and b.is_deleted=0 and b.status_active=1 and a.MST_ID in($tmpSql) and a.SEQUENCE_NO is not null ";
    $appHisSqlRes=sql_select($appHisSql);
     // print_r($appHisSql);die;

    $appHisSqlNewRes = array_chunk($appHisSqlRes,900);
    $id=return_next_id( "id","approval_mst", 1 ) ;
    $con = connect();
    $flag=1;
    foreach($appHisSqlNewRes as $appHisSqlChankRes){
        $data_array=''; $target_app_id_arr=array();$data_array_up=array();
        
        foreach($appHisSqlChankRes as $row){
            $row['INSERTED_BY'] = ($row['INSERTED_BY']) ? $row['INSERTED_BY'] : $_SESSION['logic_erp']['user_id'];
            $row['GROUP_NO'] = $electronic_app_data_arr[$row['COMPANY_ID']][$row['APPROVED_BY']]['GROUP_NO']*1;
            $row['SEQUENCE_NO'] = $electronic_app_data_arr[$row['COMPANY_ID']][$row['APPROVED_BY']]['SEQUENCE_NO']*1;

            if($row['SEQUENCE_NO'] != '' && $row['GROUP_NO'] != ''){
                $data_array_up[$row['MST_ID']] = explode(",",("".$row['SEQUENCE_NO'].",".$row['GROUP_NO']."")); 
                $target_app_id_arr[]=$row['MST_ID'];
                //......................

                if($data_array!=''){$data_array.=",";}
                $data_array.="(".$id.", $entry_form,".$row['MST_ID'].",".$row['SEQUENCE_NO'].",".$row['GROUP_NO'].",".$row['APPROVED_BY'].",'".$pc_date_time."','".$row['INSERTED_BY']."','".$pc_date_time."','".$user_ip."')"; 
                $id=$id+1;
            }
            

        }


        //print_r($data_array_up);die;

        if($flag == 1){
            $field_array_up="APPROVED_SEQU_BY*APPROVED_GROUP_BY"; 
            $multi_up_sql=bulk_update_sql_statement( "wo_non_order_info_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr,1 );
           //echo $multi_up_sql;die;
            $rID=execute_query($multi_up_sql); 
            if($rID){$flag=1;}else{$flag=0;}
        }

        if($flag == 1){
            $field_array="id, entry_form, mst_id,SEQUENCE_NO,GROUP_NO,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
            $rID1=sql_insert("approval_mst",$field_array,$data_array,0);
            if($rID1){$flag=1;}else{$flag=0;}
        }
        
        
    }


	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo 0;
	}
	disconnect($con);
    die;

}

else if ($action=="embellishment_work_order_approval_group_by")
{
    /*
        //approval/migrate/app_migrate_api.php?action=embellishment_work_order_approval_group_by
    */

    $entry_form = 32;
    
    $old_data_sql="select  max(id) as ID from approval_mst where entry_form = $entry_form";
    $old_data_sql_res=sql_select($old_data_sql);
    if( $old_data_sql_res[0]['ID']){
       echo "Old data found, Not allow migration. If you migrate you will lost your all data.<br> [পুরানো ডেটা পাওয়া গেছে, মাইগ্রেশনের অনুমতি নেই৷ আপনি মাইগ্রেট করলে আপনার সমস্ত ডেটা হারিয়ে যাবে।]";
       exit();
    }


  $elec_setup_data_sql = "SELECT COMPANY_ID,USER_ID,SEQUENCE_NO,GROUP_NO FROM ELECTRONIC_APPROVAL_SETUP WHERE ENTRY_FORM = $entry_form AND  GROUP_NO IS NOT NULL AND IS_DELETED=0";
    $elec_setup_data_sql_res = sql_select($elec_setup_data_sql);
    foreach( $elec_setup_data_sql_res as $row){
        $electronic_app_data_arr[$row['COMPANY_ID']][$row['USER_ID']] = $row;
    }  

    $tmpSql = "SELECT ID from wo_booking_mst where STATUS_ACTIVE=1 and IS_DELETED=0  and IS_APPROVED in(1,3) and booking_type=6 and item_category in(25) and is_short in(2,3) and is_approved in(1,3) and id not in( select MST_ID from APPROVAL_MST where entry_form= $entry_form)";
   // echo $tmpSql;die;

	


    $appHisSql="select b.company_id as COMPANY_ID,a.MST_ID,a.APPROVED_BY,a.SEQUENCE_NO,a.INSERTED_BY from APPROVAL_HISTORY a,wo_booking_mst b where a.mst_id = b.id  and b.booking_type=6 and b.item_category in(25) and b.is_short in(2,3) and a.ENTRY_FORM=$entry_form and a.CURRENT_APPROVAL_STATUS=1 and a.MST_ID in($tmpSql) and a.SEQUENCE_NO is not null ";
    $appHisSqlRes=sql_select($appHisSql);
     // echo($appHisSql);die;

    $appHisSqlNewRes = array_chunk($appHisSqlRes,900);
    $id=return_next_id( "id","approval_mst", 1 ) ;
    $con = connect();
    $flag=1;
    foreach($appHisSqlNewRes as $appHisSqlChankRes){
        $data_array=''; $target_app_id_arr=array();$data_array_up=array();
        
        foreach($appHisSqlChankRes as $row){
            $row['INSERTED_BY'] = ($row['INSERTED_BY']) ? $row['INSERTED_BY'] : $_SESSION['logic_erp']['user_id'];
            $row['GROUP_NO'] = $electronic_app_data_arr[$row['COMPANY_ID']][$row['APPROVED_BY']]['GROUP_NO']*1;
            $row['SEQUENCE_NO'] = $electronic_app_data_arr[$row['COMPANY_ID']][$row['APPROVED_BY']]['SEQUENCE_NO']*1;

            if($row['SEQUENCE_NO'] != '' && $row['GROUP_NO'] != ''){
                $data_array_up[$row['MST_ID']] = explode(",",("".$row['SEQUENCE_NO'].",".$row['GROUP_NO']."")); 
                $target_app_id_arr[]=$row['MST_ID'];
                //......................

                if($data_array!=''){$data_array.=",";}
                $data_array.="(".$id.", $entry_form,".$row['MST_ID'].",".$row['SEQUENCE_NO'].",".$row['GROUP_NO'].",".$row['APPROVED_BY'].",'".$pc_date_time."','".$row['INSERTED_BY']."','".$pc_date_time."','".$user_ip."')"; 
                $id=$id+1;
            }
            

        }


        //print_r($data_array_up);die;

        if($flag == 1){
            $field_array_up="APPROVED_SEQU_BY*APPROVED_GROUP_BY"; 
            $multi_up_sql=bulk_update_sql_statement( "wo_booking_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr,1 );
           //echo $multi_up_sql;die;
            $rID=execute_query($multi_up_sql); 
            if($rID){$flag=1;}else{$flag=0;}
        }

        if($flag == 1){
            $field_array="id, entry_form, mst_id,SEQUENCE_NO,GROUP_NO,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
            $rID1=sql_insert("approval_mst",$field_array,$data_array,0);
            if($rID1){$flag=1;}else{$flag=0;}
        }
        
        
    }


	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo 0;
	}
	disconnect($con);
    die;

}


else if ($action=="trims_booking_approval_group_by_controller")
{
    /*
        //approval/migrate/app_migrate_api.php?action=trims_booking_approval_group_by_controller
    */

    $entry_form = 8;
    
    $old_data_sql="select  max(id) as ID from approval_mst where entry_form = $entry_form";
    $old_data_sql_res=sql_select($old_data_sql);
    if( $old_data_sql_res[0]['ID']){
       echo "Old data found, Not allow migration. If you migrate you will lost your all data.<br> [পুরানো ডেটা পাওয়া গেছে, মাইগ্রেশনের অনুমতি নেই৷ আপনি মাইগ্রেট করলে আপনার সমস্ত ডেটা হারিয়ে যাবে।]";
       exit();
    }


  $elec_setup_data_sql = "SELECT COMPANY_ID,USER_ID,SEQUENCE_NO,GROUP_NO FROM ELECTRONIC_APPROVAL_SETUP WHERE ENTRY_FORM = $entry_form AND  GROUP_NO IS NOT NULL AND IS_DELETED=0";
    $elec_setup_data_sql_res = sql_select($elec_setup_data_sql);
    foreach( $elec_setup_data_sql_res as $row){
        $electronic_app_data_arr[$row['COMPANY_ID']][$row['USER_ID']] = $row;
    }  

    $tmpSql = "SELECT ID from wo_booking_mst where STATUS_ACTIVE=1 and IS_DELETED=0  and IS_APPROVED in(1,3)  and item_category in(4) and SHORT_BOOKING_AVAILABLE in (0,1) and is_approved in(1,3) and id not in( select MST_ID from APPROVAL_MST where entry_form= $entry_form)";
   // echo $tmpSql;die;

	


    $appHisSql="select b.company_id as COMPANY_ID,a.MST_ID,a.APPROVED_BY,a.SEQUENCE_NO,a.INSERTED_BY from APPROVAL_HISTORY a,wo_booking_mst b where a.mst_id = b.id and b.item_category in(4) and b.SHORT_BOOKING_AVAILABLE in (0,1) and a.ENTRY_FORM=$entry_form and a.CURRENT_APPROVAL_STATUS=1 and a.MST_ID in($tmpSql) and a.SEQUENCE_NO is not null ";
    $appHisSqlRes=sql_select($appHisSql);
     // echo($appHisSql);die;

    $appHisSqlNewRes = array_chunk($appHisSqlRes,900);
    $id=return_next_id( "id","approval_mst", 1 ) ;
    $con = connect();
    $flag=1;
    foreach($appHisSqlNewRes as $appHisSqlChankRes){
        $data_array=''; $target_app_id_arr=array();$data_array_up=array();
        
        foreach($appHisSqlChankRes as $row){
            $row['INSERTED_BY'] = ($row['INSERTED_BY']) ? $row['INSERTED_BY'] : $_SESSION['logic_erp']['user_id'];
            $row['GROUP_NO'] = $electronic_app_data_arr[$row['COMPANY_ID']][$row['APPROVED_BY']]['GROUP_NO']*1;
            $row['SEQUENCE_NO'] = $electronic_app_data_arr[$row['COMPANY_ID']][$row['APPROVED_BY']]['SEQUENCE_NO']*1;

            if($row['SEQUENCE_NO'] != '' && $row['GROUP_NO'] != ''){
                $data_array_up[$row['MST_ID']] = explode(",",("".$row['SEQUENCE_NO'].",".$row['GROUP_NO']."")); 
                $target_app_id_arr[]=$row['MST_ID'];
                //......................

                if($data_array!=''){$data_array.=",";}
                $data_array.="(".$id.", $entry_form,".$row['MST_ID'].",".$row['SEQUENCE_NO'].",".$row['GROUP_NO'].",".$row['APPROVED_BY'].",'".$pc_date_time."','".$row['INSERTED_BY']."','".$pc_date_time."','".$user_ip."')"; 
                $id=$id+1;
            }
            

        }


        //print_r($data_array_up);die;

        if($flag == 1){
            $field_array_up="APPROVED_SEQU_BY*APPROVED_GROUP_BY"; 
            $multi_up_sql=bulk_update_sql_statement( "wo_booking_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr,1 );
           //echo $multi_up_sql;die;
            $rID=execute_query($multi_up_sql); 
            if($rID){$flag=1;}else{$flag=0;}
        }

        if($flag == 1){
            $field_array="id, entry_form, mst_id,SEQUENCE_NO,GROUP_NO,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
            $rID1=sql_insert("approval_mst",$field_array,$data_array,0);
            if($rID1){$flag=1;}else{$flag=0;}
        }
        
        
    }


	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo 0;
	}
	disconnect($con);
    die;

}



else if ($action=="pi_approval_group_by_controller")
{
    /*
        //approval/migrate/app_migrate_api.php?action=pi_approval_group_by_controller
    */

    $entry_form = 27;
    
    $old_data_sql="select  max(id) as ID from approval_mst where entry_form = $entry_form";
    $old_data_sql_res=sql_select($old_data_sql);
    if( $old_data_sql_res[0]['ID']){
       echo "Old data found, Not allow migration. If you migrate you will lost your all data.<br> [পুরানো ডেটা পাওয়া গেছে, মাইগ্রেশনের অনুমতি নেই৷ আপনি মাইগ্রেট করলে আপনার সমস্ত ডেটা হারিয়ে যাবে।]";
       exit();
    }


  $elec_setup_data_sql = "SELECT COMPANY_ID,USER_ID,SEQUENCE_NO,GROUP_NO FROM ELECTRONIC_APPROVAL_SETUP WHERE ENTRY_FORM = $entry_form AND  GROUP_NO IS NOT NULL AND IS_DELETED=0";
    $elec_setup_data_sql_res = sql_select($elec_setup_data_sql);
    foreach( $elec_setup_data_sql_res as $row){
        $electronic_app_data_arr[$row['COMPANY_ID']][$row['USER_ID']] = $row;
    }  

    $tmpSql = "SELECT ID from COM_PI_MASTER_DETAILS where STATUS_ACTIVE=1 and IS_DELETED=0 and is_approved in(1,3) and id not in( select MST_ID from APPROVAL_MST where entry_form= $entry_form)";
   // echo $tmpSql;die;

    $appHisSql="select b.importer_id as COMPANY_ID,a.MST_ID,a.APPROVED_BY,a.SEQUENCE_NO,a.INSERTED_BY from APPROVAL_HISTORY a,COM_PI_MASTER_DETAILS b where a.mst_id = b.id and a.ENTRY_FORM=$entry_form and a.CURRENT_APPROVAL_STATUS=1 and a.MST_ID in($tmpSql) and a.SEQUENCE_NO is not null ";
    $appHisSqlRes=sql_select($appHisSql);
     // echo($appHisSql);die;

    $appHisSqlNewRes = array_chunk($appHisSqlRes,900);
    $id=return_next_id( "id","approval_mst", 1 ) ;
    $con = connect();
    $flag=1;
    foreach($appHisSqlNewRes as $appHisSqlChankRes){
        $data_array=''; $target_app_id_arr=array();$data_array_up=array();
        
        foreach($appHisSqlChankRes as $row){
            $row['INSERTED_BY'] = ($row['INSERTED_BY']) ? $row['INSERTED_BY'] : $_SESSION['logic_erp']['user_id'];
            $row['GROUP_NO'] = $electronic_app_data_arr[$row['COMPANY_ID']][$row['APPROVED_BY']]['GROUP_NO']*1;
            $row['SEQUENCE_NO'] = $electronic_app_data_arr[$row['COMPANY_ID']][$row['APPROVED_BY']]['SEQUENCE_NO']*1;

            if($row['SEQUENCE_NO'] != '' && $row['GROUP_NO'] != ''){
                $data_array_up[$row['MST_ID']] = explode(",",("".$row['SEQUENCE_NO'].",".$row['GROUP_NO']."")); 
                $target_app_id_arr[]=$row['MST_ID'];
                //......................
                if($data_array!=''){$data_array.=",";}
                $data_array.="(".$id.", $entry_form,".$row['MST_ID'].",".$row['SEQUENCE_NO'].",".$row['GROUP_NO'].",".$row['APPROVED_BY'].",'".$pc_date_time."','".$row['INSERTED_BY']."','".$pc_date_time."','".$user_ip."')"; 
                $id=$id+1;
            }
            

        }


        //print_r($data_array_up);die;

        if($flag == 1){
            $field_array_up="APPROVED_SEQU_BY*APPROVED_GROUP_BY"; 
            $multi_up_sql=bulk_update_sql_statement( "COM_PI_MASTER_DETAILS", "id", $field_array_up, $data_array_up, $target_app_id_arr,1 );
           //echo $multi_up_sql;die;
            $rID=execute_query($multi_up_sql); 
            if($rID){$flag=1;}else{$flag=0;}
        }

        if($flag == 1){
            $field_array="ID, ENTRY_FORM, MST_ID,SEQUENCE_NO,GROUP_NO,APPROVED_BY, APPROVED_DATE,INSERTED_BY,INSERT_DATE,USER_IP";
            $rID1=sql_insert("approval_mst",$field_array,$data_array,0);
            if($rID1){$flag=1;}else{$flag=0;}
        }
        
        
    }


	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo 0;
	}
	disconnect($con);
    die;

}



else if ($action=="other_purchase_work_order_approval_group_by_controller")
{
    /*
        //approval/migrate/app_migrate_api.php?action=other_purchase_work_order_approval_group_by_controller
    */

    $entry_form = 17;
    
    $old_data_sql="select  max(id) as ID from approval_mst where entry_form = $entry_form";
    $old_data_sql_res=sql_select($old_data_sql);
    if( $old_data_sql_res[0]['ID']){
       echo "Old data found, Not allow migration. If you migrate you will lost your all data.<br> [পুরানো ডেটা পাওয়া গেছে, মাইগ্রেশনের অনুমতি নেই৷ আপনি মাইগ্রেট করলে আপনার সমস্ত ডেটা হারিয়ে যাবে।]";
       exit();
    }


  $elec_setup_data_sql = "SELECT COMPANY_ID,USER_ID,SEQUENCE_NO,GROUP_NO FROM ELECTRONIC_APPROVAL_SETUP WHERE ENTRY_FORM = $entry_form AND  GROUP_NO IS NOT NULL AND IS_DELETED=0";
    $elec_setup_data_sql_res = sql_select($elec_setup_data_sql);
    foreach( $elec_setup_data_sql_res as $row){
        $electronic_app_data_arr[$row['COMPANY_ID']][$row['USER_ID']] = $row;
    }  

    $tmpSql = "SELECT ID from wo_non_order_info_mst where STATUS_ACTIVE=1 and IS_DELETED=0 and READY_TO_APPROVED=1  and entry_form=147 and IS_APPROVED in(1,3) and id not in( select MST_ID from APPROVAL_MST where entry_form= $entry_form)";
   // echo $tmpSql;die;

    $appHisSql="select b.COMPANY_NAME as COMPANY_ID,a.MST_ID,a.APPROVED_BY,a.SEQUENCE_NO,a.INSERTED_BY from APPROVAL_HISTORY a,wo_non_order_info_mst b where a.mst_id = b.id  and b.READY_TO_APPROVED=1  and b.entry_form=147 and a.ENTRY_FORM=$entry_form and a.CURRENT_APPROVAL_STATUS=1 and a.MST_ID in($tmpSql) and a.SEQUENCE_NO is not null ";
    $appHisSqlRes=sql_select($appHisSql);
    //echo($appHisSql);die;

    $appHisSqlNewRes = array_chunk($appHisSqlRes,900);
    $id=return_next_id( "id","approval_mst", 1 ) ;
    $con = connect();
    $flag=1;
    foreach($appHisSqlNewRes as $appHisSqlChankRes){
        $data_array=''; $target_app_id_arr=array();$data_array_up=array();
        
        foreach($appHisSqlChankRes as $row){
            $row['INSERTED_BY'] = ($row['INSERTED_BY']) ? $row['INSERTED_BY'] : $_SESSION['logic_erp']['user_id'];
            $row['GROUP_NO'] = $electronic_app_data_arr[$row['COMPANY_ID']][$row['APPROVED_BY']]['GROUP_NO']*1;
            $row['SEQUENCE_NO'] = $electronic_app_data_arr[$row['COMPANY_ID']][$row['APPROVED_BY']]['SEQUENCE_NO']*1;

            if($row['SEQUENCE_NO'] != '' && $row['GROUP_NO'] != ''){
                $data_array_up[$row['MST_ID']] = explode(",",("".$row['SEQUENCE_NO'].",".$row['GROUP_NO']."")); 
                $target_app_id_arr[]=$row['MST_ID'];
                //......................
                if($data_array!=''){$data_array.=",";}
                $data_array.="(".$id.", $entry_form,".$row['MST_ID'].",".$row['SEQUENCE_NO'].",".$row['GROUP_NO'].",".$row['APPROVED_BY'].",'".$pc_date_time."','".$row['INSERTED_BY']."','".$pc_date_time."','".$user_ip."')"; 
                $id=$id+1;
            }
            

        }


        //print_r($data_array_up);die;

        if($flag == 1){
            $field_array_up="APPROVED_SEQU_BY*APPROVED_GROUP_BY"; 
            $multi_up_sql=bulk_update_sql_statement( "wo_non_order_info_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr,1 );
           //echo $multi_up_sql;die;
            $rID=execute_query($multi_up_sql); 
            if($rID){$flag=1;}else{$flag=0;}
        }

        if($flag == 1){
            $field_array="ID, ENTRY_FORM, MST_ID,SEQUENCE_NO,GROUP_NO,APPROVED_BY, APPROVED_DATE,INSERTED_BY,INSERT_DATE,USER_IP";
            $rID1=sql_insert("approval_mst",$field_array,$data_array,0);
            if($rID1){$flag=1;}else{$flag=0;}
        }
        
        
    }


	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo 0;
	}
	disconnect($con);
    die;

}
