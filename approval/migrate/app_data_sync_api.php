<?
//Note: Please follow "approval/migrate/app_data_sync_api.php?action=quick_costing_approval_v3_controller" to create new action
header('Content-type:text/html; charset=utf-8');
session_start();
 

require_once('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];


if ($action=="quick_costing_approval_v3_controller")
{
    //api link: approval/migrate/app_data_sync_api.php?action=quick_costing_approval_v3_controller
  

    //SYNC .................................
    define('ENTRY_FORM',70);

    $sync_data_arr[1] = [
        'ENTRY_FORM' => ENTRY_FORM,
        'BUYER_ID' => 99,
        'FROM_APPROVED_BY' => 26,
        'FROM_SEQUENCE_NO' => 10,
        'TO_APPROVED_BY' => 516,
        'TO_SEQUENCE_NO' => 4,
    ];

    $sync_data_arr[2] = [
        'ENTRY_FORM' => ENTRY_FORM,
        'BUYER_ID' => '222,202',
        'FROM_APPROVED_BY' => 26,
        'FROM_SEQUENCE_NO' => 10,
        'TO_APPROVED_BY' => 45,
        'TO_SEQUENCE_NO' => 7,
    ];

    $sync_data_arr[3] = [
        'ENTRY_FORM' => ENTRY_FORM,
        'BUYER_ID' => 43,
        'FROM_APPROVED_BY' => 34,
        'FROM_SEQUENCE_NO' => 5,
        'TO_APPROVED_BY' => 40,
        'TO_SEQUENCE_NO' => 6,
    ];


    $sync_data_arr[4] = [
        'ENTRY_FORM' => ENTRY_FORM,
        'BUYER_ID' => 81,
        'FROM_APPROVED_BY' => 387,
        'FROM_SEQUENCE_NO' => 3,
        'TO_APPROVED_BY' => 40,
        'TO_SEQUENCE_NO' => 6,
    ];


    $con = connect();     
    $flag = 1;
   foreach($sync_data_arr as $APP_ROWS){
        
        //history......................................................................
        if($flag == 1){
            if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and a.buyer_id in(".$APP_ROWS['BUYER_ID'].")";}
            $APPROVAL_HISTORY_SQL = "update APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']."  where id in(
            select id from APPROVAL_HISTORY where mst_id in(select a.ID from qc_mst a,qc_tot_cost_summary b,qc_confirm_mst c where a.qc_no=b.mst_id and b.mst_id=c.cost_sheet_id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and c.READY_TO_APPROVE=1 $buyer_con) and APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']."   and ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM'].")";
            $eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
        }


        //app mst......................................................................
        if($flag == 1){
            if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and buyer_id in(".$APP_ROWS['BUYER_ID'].")";}
            $APPROVAL_MST_SQL = "update APPROVAL_MST set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO']." where ENTRY_FORM =".$APP_ROWS['ENTRY_FORM']." AND APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." AND SEQUENCE_NO = ".$APP_ROWS['FROM_SEQUENCE_NO']." and mst_id in(select qc_no from QC_MST where APPROVED_SEQU_BY = ".$APP_ROWS['FROM_APPROVED_BY']." $buyer_con)";
            $eq_rr['appmst'][] = $flag = execute_query($APPROVAL_MST_SQL,0);
        }

        //echo  $APPROVAL_MST_SQL;oci_rollback($con);die;
        
        //mst.........................................................................
        if($flag == 1){
            if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and buyer_id in(".$APP_ROWS['BUYER_ID'].")";}
            $MST_SQL = "update QC_MST set APPROVED_SEQU_BY=".$APP_ROWS['TO_SEQUENCE_NO']."  where APPROVED_SEQU_BY = ".$APP_ROWS['FROM_APPROVED_BY']." and APPROVED > 0 $buyer_con";
            $eq_rr['mst'][] = $flag = execute_query($MST_SQL,0);
        }



   }


   //print_r($eq_rr['history']);oci_rollback($con);die;
    
 
	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo "0**".implode(',',$eq_rr['history'])."**".implode(',',$eq_rr['appmst'])."**".implode(',',$eq_rr['mst']);
	}
	disconnect($con);
    die;

}

else if ($action=="short_feb_booking_approval_controller")
{
    //api link: approval/migrate/app_data_sync_api.php?action=short_feb_booking_approval_controller
  

    //SYNC .................................
    define('ENTRY_FORM',12);
    define('COMPANY_ID',1);

    $sync_data_arr[1] = [
        'COMPANY_ID' => COMPANY_ID,
        'ENTRY_FORM' => ENTRY_FORM,
        'BUYER_ID' => 99,
        'FROM_APPROVED_BY' => 26,
        'FROM_SEQUENCE_NO' => 11,
        'TO_APPROVED_BY' => 516,
        'TO_SEQUENCE_NO' => 4,
    ];

    $sync_data_arr[2] = [
        'COMPANY_ID' => COMPANY_ID,
        'ENTRY_FORM' => ENTRY_FORM,
        'BUYER_ID' => '222,202',
        'FROM_APPROVED_BY' => 26,
        'FROM_SEQUENCE_NO' => 11,
        'TO_APPROVED_BY' => 45,
        'TO_SEQUENCE_NO' => 7,
    ];

    $sync_data_arr[3] = [
        'COMPANY_ID' => COMPANY_ID,
        'ENTRY_FORM' => ENTRY_FORM,
        'BUYER_ID' => 43,
        'FROM_APPROVED_BY' => 34,
        'FROM_SEQUENCE_NO' => 5,
        'TO_APPROVED_BY' => 40,
        'TO_SEQUENCE_NO' => 6,
    ];


    $sync_data_arr[4] = [
        'COMPANY_ID' => COMPANY_ID,
        'ENTRY_FORM' => ENTRY_FORM,
        'BUYER_ID' => 81,
        'FROM_APPROVED_BY' => 387,
        'FROM_SEQUENCE_NO' => 2,
        'TO_APPROVED_BY' => 40,
        'TO_SEQUENCE_NO' => 6,
    ];


    $con = connect();     
    $flag = 1;
   foreach($sync_data_arr as $APP_ROWS){
        
        //history......................................................................
        if($flag == 1){
            if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and a.BUYER_ID in(".$APP_ROWS['BUYER_ID'].")";}
            $APPROVAL_HISTORY_SQL = "update  APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']." where ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM']."  AND APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." AND SEQUENCE_NO = ".$APP_ROWS['FROM_SEQUENCE_NO']." and MST_ID  in(select a.id from wo_booking_mst a where a.COMPANY_ID = ".$APP_ROWS['COMPANY_ID']."  and READY_TO_APPROVED = 1 and item_category in(2,3,13) $buyer_con)";
            $eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
        }


   }


   //print_r($eq_rr['history']);oci_rollback($con);die;
    
 
	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo "0**".implode(',',$eq_rr['history'])."**".implode(',',$eq_rr['appmst'])."**".implode(',',$eq_rr['mst']);
	}
	disconnect($con);
    die;

}

else if ($action=="sample_feb_booking_wo_approval_controller")
{
    //api link: approval/migrate/app_data_sync_api.php?action=sample_feb_booking_wo_approval_controller
  

    //SYNC .................................
    define('ENTRY_FORM',13);
    define('COMPANY_ID',1);

    $sync_data_arr[1] = [
        'COMPANY_ID' => COMPANY_ID,
        'ENTRY_FORM' => ENTRY_FORM,
        'BUYER_ID' => '67,158',
        'FROM_APPROVED_BY' => 45,
        'FROM_SEQUENCE_NO' => 7,
        'TO_APPROVED_BY' => 627,
        'TO_SEQUENCE_NO' => 12,
    ];

   


    $con = connect();     
    $flag = 1;
   foreach($sync_data_arr as $APP_ROWS){
        
        //history......................................................................
        if($flag == 1){
            if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and a.BUYER_ID in(".$APP_ROWS['BUYER_ID'].")";}
            $APPROVAL_HISTORY_SQL = "update  APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']." where ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM']."  AND APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." AND SEQUENCE_NO = ".$APP_ROWS['FROM_SEQUENCE_NO']." and MST_ID  in(select a.id from wo_booking_mst a where a.COMPANY_ID = ".$APP_ROWS['COMPANY_ID']." and a.is_short=2  and A.booking_type=4  and A.READY_TO_APPROVED = 1 and A.item_category in(2,3,13) and a.IS_APPROVED in(1,3) $buyer_con)";
            
           // echo $APPROVAL_HISTORY_SQL;die;
            
            $eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
        }


   }


   //print_r($eq_rr['history']);oci_rollback($con);die;
    
 
	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo "0**".implode(',',$eq_rr['history'])."**".implode(',',$eq_rr['appmst'])."**".implode(',',$eq_rr['mst']);
	}
	disconnect($con);
    die;

}

else if ($action=="non_order_sample_booking_approval_controller")
{
    //api link: approval/migrate/app_data_sync_api.php?action=non_order_sample_booking_approval_controller
  
    //SYNC .................................
    define('ENTRY_FORM',9);
    define('COMPANY_ID',1);

    $sync_data_arr[1] = [
        'COMPANY_ID' => COMPANY_ID,
        'ENTRY_FORM' => ENTRY_FORM,
        'BUYER_ID' => 99,
        'FROM_APPROVED_BY' => 26,
        'FROM_SEQUENCE_NO' => 11,
        'TO_APPROVED_BY' => 516,
        'TO_SEQUENCE_NO' => 4,
    ];

    $sync_data_arr[2] = [
        'COMPANY_ID' => COMPANY_ID,
        'ENTRY_FORM' => ENTRY_FORM,
        'BUYER_ID' => '222,202',
        'FROM_APPROVED_BY' => 26,
        'FROM_SEQUENCE_NO' => 11,
        'TO_APPROVED_BY' => 45,
        'TO_SEQUENCE_NO' => 7,
    ];

    $sync_data_arr[3] = [
        'COMPANY_ID' => COMPANY_ID,
        'ENTRY_FORM' => ENTRY_FORM,
        'BUYER_ID' => 43,
        'FROM_APPROVED_BY' => 34,
        'FROM_SEQUENCE_NO' => 5,
        'TO_APPROVED_BY' => 40,
        'TO_SEQUENCE_NO' => 6,
    ];


    $sync_data_arr[4] = [
        'COMPANY_ID' => COMPANY_ID,
        'ENTRY_FORM' => ENTRY_FORM,
        'BUYER_ID' => 81,
        'FROM_APPROVED_BY' => 387,
        'FROM_SEQUENCE_NO' => 2,
        'TO_APPROVED_BY' => 40,
        'TO_SEQUENCE_NO' => 6,
    ];


    $con = connect();     
    $flag = 1;
   foreach($sync_data_arr as $APP_ROWS){
        
        //history......................................................................
        if($flag == 1){
            if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and a.BUYER_ID in(".$APP_ROWS['BUYER_ID'].")";}
            $APPROVAL_HISTORY_SQL = "update APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']." where id in(
                select id from APPROVAL_HISTORY where mst_id in(select a.id  from wo_non_ord_samp_booking_mst a where a.status_active=1 and a.is_deleted=0 and a.entry_form_id in(90,610,140,439) and a.item_category in(2,3,13)  and a.ready_to_approved=1 and a.company_id=".$APP_ROWS['COMPANY_ID']." $buyer_con) and APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." and ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM']."
                )";
            $eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
        }




   }


   //print_r($eq_rr['history']);oci_rollback($con);die;
    
 
	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo "0**".implode(',',$eq_rr['history'])."**".implode(',',$eq_rr['appmst'])."**".implode(',',$eq_rr['mst']);
	}
	disconnect($con);
    die;

}


else if ($action=="trims_booking_approval_controller")
{
    //api link: approval/migrate/app_data_sync_api.php?action=trims_booking_approval_controller
  
    //SYNC .................................
    define('ENTRY_FORM',8);
    define('COMPANY_ID',1);

    // $sync_data_arr[1] = [
    //     'COMPANY_ID' => COMPANY_ID,
    //     'ENTRY_FORM' => ENTRY_FORM,
    //     'BUYER_ID' => '69',
    //     'FROM_APPROVED_BY' => 576,
    //     'FROM_SEQUENCE_NO' => 2,
    //     'TO_APPROVED_BY' => 681,
    //     'TO_SEQUENCE_NO' => 3,
    // ];

    // $sync_data_arr[1] = [
    //     'COMPANY_ID' => COMPANY_ID,
    //     'ENTRY_FORM' => ENTRY_FORM,
    //     'BUYER_ID' => '',
    //     'FROM_APPROVED_BY' => 145,
    //     'FROM_SEQUENCE_NO' => 1,
    //     'TO_APPROVED_BY' => 68,
    //     'TO_SEQUENCE_NO' => 1,
    // ];

    $sync_data_arr[2] = [
        'COMPANY_ID' => COMPANY_ID,
        'ENTRY_FORM' => ENTRY_FORM,
        'BUYER_ID' => '',
        'FROM_APPROVED_BY' => 145,
        'FROM_SEQUENCE_NO' => 2,
        'TO_APPROVED_BY' => 388,
        'TO_SEQUENCE_NO' => 2,
    ];



    $con = connect();     
    $flag = 1;
   foreach($sync_data_arr as $APP_ROWS){
        
        //history......................................................................
        if($flag == 1){
            if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and a.BUYER_ID in(".$APP_ROWS['BUYER_ID'].")";}
            $APPROVAL_HISTORY_SQL = "update APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']." where id in(
                select id from APPROVAL_HISTORY where mst_id in(select a.id  from wo_booking_mst a where a.status_active=1 and a.is_deleted=0  and a.item_category in(4)  and a.ready_to_approved=1 and a.company_id=".$APP_ROWS['COMPANY_ID']." $buyer_con) and APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." and SEQUENCE_NO=".$APP_ROWS['FROM_SEQUENCE_NO']." and ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM']."
                )";
            $eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
        }
   }


   //print_r($eq_rr['history']);oci_rollback($con);die;
    
 
	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo "0**".implode(',',$eq_rr['history'])."**".implode(',',$eq_rr['appmst'])."**".implode(',',$eq_rr['mst']);
	}
	disconnect($con);
    die;

}


else if ($action=="fabric_booking_approval_controller")
{
    //api link: approval/migrate/app_data_sync_api.php?action=fabric_booking_approval_controller
  
    //SYNC .................................
    define('ENTRY_FORM',7);
    define('COMPANY_ID',1);

    $sync_data_arr[1] = [
        'COMPANY_ID' => COMPANY_ID,
        'ENTRY_FORM' => ENTRY_FORM,
        'BUYER_ID' => '',
        'FROM_APPROVED_BY' => 72,
        'FROM_SEQUENCE_NO' => 3,
        'TO_APPROVED_BY' => 636,
        'TO_SEQUENCE_NO' => 3,
    ];


    $con = connect();     
    $flag = 1;
   foreach($sync_data_arr as $APP_ROWS){
        
        //history......................................................................
        if($flag == 1){
            if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and a.BUYER_ID in(".$APP_ROWS['BUYER_ID'].")";}
            $APPROVAL_HISTORY_SQL = "update APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']." where id in(
                select id from APPROVAL_HISTORY where mst_id in(select a.id  from wo_booking_mst a where a.status_active=1 and a.is_deleted=0  and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_approved in(1,3)  and a.ready_to_approved=1 and a.company_id=".$APP_ROWS['COMPANY_ID']." $buyer_con) and APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." and ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM']." and SEQUENCE_NO = ".$APP_ROWS['FROM_SEQUENCE_NO']."
                )";
            $eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
        }
   }


   //print_r($eq_rr['history']);oci_rollback($con);die;
    
 
	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo "0**".implode(',',$eq_rr['history'])."**".implode(',',$eq_rr['appmst'])."**".implode(',',$eq_rr['mst']);
	}
	disconnect($con);
    die;

}

else if ($action=="pi_approval_new")
{
    //api link: approval/migrate/app_data_sync_api.php?action=pi_approval_new
  
    //SYNC .................................
    define('ENTRY_FORM',27);
    define('COMPANY_ID',1);

    $sync_data_arr[1] = [
        'COMPANY_ID' => COMPANY_ID,
        'ENTRY_FORM' => ENTRY_FORM,
        'BUYER_ID' => '',
        'FROM_APPROVED_BY' => 26,
        'FROM_SEQUENCE_NO' => 11,
        'TO_APPROVED_BY' => 516,
        'TO_SEQUENCE_NO' => 4,
    ];


    $con = connect();     
    $flag = 1;
   foreach($sync_data_arr as $APP_ROWS){
        
        //history......................................................................
        if($flag == 1){
            if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and a.BUYER_ID in(".$APP_ROWS['BUYER_ID'].")";}
            $APPROVAL_HISTORY_SQL = "update APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']." where id in(
                select id from APPROVAL_HISTORY where mst_id in(select a.id  from com_pi_master_details a where a.status_active=1 and a.is_deleted=0 and a.ready_to_approved=1 and a.importer_id=".$APP_ROWS['COMPANY_ID']." $buyer_con) and APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." and ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM']."
                )";
            $eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
        }
   }


   //print_r($eq_rr['history']);oci_rollback($con);die;
    
 
	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo "0**".implode(',',$eq_rr['history'])."**".implode(',',$eq_rr['appmst'])."**".implode(',',$eq_rr['mst']);
	}
	disconnect($con);
    die;

}
else if ($action=="purchase_requisition_approval_controller_v2")
{
    //api link: approval/migrate/app_data_sync_api.php?action=purchase_requisition_approval_controller_v2
  
    //SYNC .................................
    define('ENTRY_FORM',1);
    define('COMPANY_ID',3);

   


    $sync_data_arr[1] = [
        'COMPANY_ID' => COMPANY_ID,
        'ENTRY_FORM' => ENTRY_FORM,
        'BUYER_ID' => '',
        'LOCATION_ID' => 4,
        'FROM_APPROVED_BY' => 620,
        'FROM_SEQUENCE_NO' => 1,
        'TO_APPROVED_BY' => 784,
        'TO_SEQUENCE_NO' => 1,
    ];


    $con = connect();     
    $flag = 1;
   foreach($sync_data_arr as $APP_ROWS){
        
        //history......................................................................
        if($flag == 1){
            //if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and a.buyer_id in(".$APP_ROWS['BUYER_ID'].")";}
            //if($APP_ROWS['LOCATION_ID'] != ''){$buyer_con .= " and a.location_id in(".$APP_ROWS['LOCATION_ID'].")";}
 
            $APPROVAL_HISTORY_SQL = "update APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']."  where id in(
            select id from APPROVAL_HISTORY where mst_id in(select id from inv_purchase_requisition_mst where ENTRY_FORM=69 and IS_APPROVED in(1,3) and STATUS_ACTIVE=1 and READY_TO_APPROVE=1 and COMPANY_ID={$APP_ROWS['COMPANY_ID']} $buyer_con) and APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']."   and ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM'].")";
            $eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
        }


        //app mst......................................................................
        if($flag == 1){
           // if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and buyer_id in(".$APP_ROWS['BUYER_ID'].")";}
            $APPROVAL_MST_SQL = "update APPROVAL_MST set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO']." where ENTRY_FORM =".$APP_ROWS['ENTRY_FORM']." AND APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." AND SEQUENCE_NO = ".$APP_ROWS['FROM_SEQUENCE_NO']." and mst_id in(select id from inv_purchase_requisition_mst where ENTRY_FORM=69 and IS_APPROVED in(1,3) and STATUS_ACTIVE=1 and READY_TO_APPROVE=1  and COMPANY_ID={$APP_ROWS['COMPANY_ID']}  and APPROVED_SEQU_BY = ".$APP_ROWS['FROM_APPROVED_BY']." $buyer_con)";
            $eq_rr['appmst'][] = $flag = execute_query($APPROVAL_MST_SQL,0);
        }

        //echo  $APPROVAL_MST_SQL;oci_rollback($con);die;
        
        //mst.........................................................................
        if($flag == 1){
           // if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and buyer_id in(".$APP_ROWS['BUYER_ID'].")";}
            $MST_SQL = "update inv_purchase_requisition_mst set APPROVED_SEQU_BY=".$APP_ROWS['TO_SEQUENCE_NO']."  where APPROVED_SEQU_BY = ".$APP_ROWS['FROM_APPROVED_BY']." and IS_APPROVED in(1,3) $buyer_con";
            $eq_rr['mst'][] = $flag = execute_query($MST_SQL,0);
        }



   }


   //print_r($eq_rr['history']);oci_rollback($con);die;
    
 
	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo "0**".implode(',',$eq_rr['history'])."**".implode(',',$eq_rr['appmst'])."**".implode(',',$eq_rr['mst']);
	}
	disconnect($con);
    die;

}



else if ($action=="pre_costing_approval_wvn_v2_controller")
{
    //api link: approval/migrate/app_data_sync_api.php?action=pre_costing_approval_wvn_v2_controller
  
    //SYNC .................................
    define('ENTRY_FORM',46);
    define('COMPANY_ID',1);

    $sync_data_arr[2] = [
        'COMPANY_ID' => COMPANY_ID,
        'ENTRY_FORM' => ENTRY_FORM,
        'BUYER_ID' => '16',
        'LOCATION_ID' => '',
        'FROM_APPROVED_BY' => 108,
        'FROM_SEQUENCE_NO' => 6,
        'TO_APPROVED_BY' => 39,
        'TO_SEQUENCE_NO' => 7,
    ];

 


 


    $con = connect();     
    $flag = 1;
   foreach($sync_data_arr as $APP_ROWS){
        
        //history......................................................................
        if($flag == 1){
            if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and b.buyer_name in(".$APP_ROWS['BUYER_ID'].")";}
            //if($APP_ROWS['LOCATION_ID'] != ''){$buyer_con .= " and a.location_id in(".$APP_ROWS['LOCATION_ID'].")";}

           //echo "select id from wo_pre_cost_mst a,wo_po_details_master b where  a.job_id=b.id and b.company_name={$APP_ROWS['COMPANY_ID']} and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0";die;


            $APPROVAL_HISTORY_SQL = "update APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']."  where id in(
            select id from APPROVAL_HISTORY where mst_id in(select a.id from wo_pre_cost_mst a,wo_po_details_master b where  a.job_id=b.id and b.company_name={$APP_ROWS['COMPANY_ID']} and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 $buyer_con) and APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']."   and ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM'].")";
            $eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
        }
       // echo  $APPROVAL_HISTORY_SQL;oci_rollback($con);die;

        //app mst......................................................................
        if($flag == 1){
            if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and buyer_name in(".$APP_ROWS['BUYER_ID'].")";}
            $APPROVAL_MST_SQL = "update APPROVAL_MST set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']." where ENTRY_FORM =".$APP_ROWS['ENTRY_FORM']." AND APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." AND SEQUENCE_NO = ".$APP_ROWS['FROM_SEQUENCE_NO']." and mst_id in(select a.id from wo_pre_cost_mst a,wo_po_details_master b where  a.job_id=b.id and b.company_name={$APP_ROWS['COMPANY_ID']} and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and  a.READY_TO_APPROVEd=1 and a.APPROVED_SEQU_BY = ".$APP_ROWS['FROM_APPROVED_BY']." $buyer_con)";
            $eq_rr['appmst'][] = $flag = execute_query($APPROVAL_MST_SQL,0);
        }

      // echo  $APPROVAL_MST_SQL;oci_rollback($con);die;
        
        //mst.........................................................................
        if($flag == 1){
            if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and buyer_name in(".$APP_ROWS['BUYER_ID'].")";}
            $MST_SQL = "update wo_pre_cost_mst set APPROVED_SEQU_BY=".$APP_ROWS['TO_SEQUENCE_NO']."  where APPROVED_SEQU_BY = ".$APP_ROWS['FROM_SEQUENCE_NO']." and APPROVED in(1,3) and READY_TO_APPROVEd=1 and job_id in(select b.id from wo_po_details_master b where  b.company_name={$APP_ROWS['COMPANY_ID']} and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 $buyer_con)";
            $eq_rr['mst'][] = $flag = execute_query($MST_SQL,0);
        }

       // echo  $MST_SQL;oci_rollback($con);die;

   }


  // print_r($eq_rr['history']);oci_rollback($con);die;
    
 
	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo "0**".implode(',',$eq_rr['history'])."**".implode(',',$eq_rr['appmst'])."**".implode(',',$eq_rr['mst']);
	}
	disconnect($con);
    die;

}



else if ($action=="pre_costing_approval_controller")
{
    //api link: approval/migrate/app_data_sync_api.php?action=pre_costing_approval_controller
  

    //SYNC .................................
    define('ENTRY_FORM',15);
    define('COMPANY_ID',1);

    $sync_data_arr[1] = [
        'COMPANY_ID' => COMPANY_ID,
        'ENTRY_FORM' => ENTRY_FORM,
        'BUYER_ID' => '',
        'FROM_APPROVED_BY' => 94,
        'FROM_SEQUENCE_NO' => 5,
        'TO_APPROVED_BY' => 94,
        'TO_SEQUENCE_NO' => 6,
    ];


    $con = connect();     
    $flag = 1;
   foreach($sync_data_arr as $APP_ROWS){
        
        //history......................................................................
        if($flag == 1){
            $APPROVAL_HISTORY_SQL = "update  APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']." where ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM']."  AND APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." AND SEQUENCE_NO = ".$APP_ROWS['FROM_SEQUENCE_NO']." and MST_ID  in(select a.id from wo_pre_cost_mst a,wo_po_details_master b where a.job_id=b.id and b.company_name = ".$APP_ROWS['COMPANY_ID']." and A.ready_to_approved = 1 and a.approved in (1,3)  $buyer_con)";
            $eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
        }


   }


   //print_r($eq_rr['history']);oci_rollback($con);die;
    
 
	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo "0**".implode(',',$eq_rr['history'])."**".implode(',',$eq_rr['appmst'])."**".implode(',',$eq_rr['mst']);
	}
	disconnect($con);
    die;

}
else if ($action=="lab_test_approval_controller_v2")
{
    //api link: approval/migrate/app_data_sync_api.php?action=lab_test_approval_controller_v2
  
    //SYNC .................................
    define('ENTRY_FORM',78);
    define('COMPANY_ID',3);


    // $sync_data_arr[1] = [
    //         'COMPANY_ID' => COMPANY_ID,
    //         'ENTRY_FORM' => ENTRY_FORM,
    //         'BUYER_ID' => '69',
    //         'FROM_APPROVED_BY' => 576,
    //         'FROM_SEQUENCE_NO' => 2,
    //         'TO_APPROVED_BY' => 681,
    //         'TO_SEQUENCE_NO' => 3,
    //     ];

    // $sync_data_arr[3] = [
    //     'COMPANY_ID' => COMPANY_ID,
    //     'ENTRY_FORM' => ENTRY_FORM,
    //     'BUYER_ID' => '69,37',
    //     'FROM_APPROVED_BY' => 636,
    //     'FROM_SEQUENCE_NO' => 2,
    //     'TO_APPROVED_BY' => 681,
    //     'TO_SEQUENCE_NO' => 3,
    // ];

    $sync_data_arr[1] = [
        'COMPANY_ID' => COMPANY_ID,
        'ENTRY_FORM' => ENTRY_FORM,
        'BUYER_ID' => '16',
        'FROM_APPROVED_BY' => 579,
        'FROM_SEQUENCE_NO' => 1,
        'TO_APPROVED_BY' => 682,
        'TO_SEQUENCE_NO' => 4,
    ];



    $con = connect();     
    $flag = 1;
   foreach($sync_data_arr as $APP_ROWS){
        
        //history......................................................................
        if($flag == 1){
            if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and c.BUYER_NAME in(".$APP_ROWS['BUYER_ID'].")";}
  
            $APPROVAL_HISTORY_SQL = "update APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']."  where id in(
            select id from APPROVAL_HISTORY where mst_id in(select a.id from wo_labtest_mst a,wo_labtest_dtls b,wo_po_details_master c where a.id=b.mst_id and b.job_no=c.job_no and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and a.IS_APPROVED in(1,3) and a.READY_TO_APPROVED=1 and a.COMPANY_ID={$APP_ROWS['COMPANY_ID']}  $buyer_con) and APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']."   and ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM'].")";
            $eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
        }


        //app mst......................................................................
        if($flag == 1){
            if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and c.BUYER_NAME in(".$APP_ROWS['BUYER_ID'].")";}
            $APPROVAL_MST_SQL = "update APPROVAL_MST set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']." where ENTRY_FORM =".$APP_ROWS['ENTRY_FORM']." AND APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." AND SEQUENCE_NO = ".$APP_ROWS['FROM_SEQUENCE_NO']." and mst_id in( select a.id from wo_labtest_mst a,wo_labtest_dtls b,wo_po_details_master c where a.id=b.mst_id and b.job_no=c.job_no and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and a.IS_APPROVED in(1,3) and a.READY_TO_APPROVED=1 and a.COMPANY_ID={$APP_ROWS['COMPANY_ID']} and a.APPROVED_SEQU_BY ={$APP_ROWS['FROM_APPROVED_BY']}  $buyer_con)";
            $eq_rr['appmst'][] = $flag = execute_query($APPROVAL_MST_SQL,0);
        }

       

        //echo  $APPROVAL_MST_SQL;oci_rollback($con);die;
        
        //mst.........................................................................
        if($flag == 1){
            $MST_SQL = "update wo_labtest_mst set APPROVED_SEQU_BY=".$APP_ROWS['TO_SEQUENCE_NO']."  where APPROVED_SEQU_BY = ".$APP_ROWS['FROM_APPROVED_BY']." and IS_APPROVED in(1,3) and READY_TO_APPROVEd=1 and COMPANY_ID={$APP_ROWS['COMPANY_ID']} and id in(select a.id from wo_labtest_mst a,wo_labtest_dtls b,wo_po_details_master c where a.id=b.mst_id and b.job_no=c.job_no and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and a.IS_APPROVED in(1,3) and a.READY_TO_APPROVED=1 and a.COMPANY_ID={$APP_ROWS['COMPANY_ID']} and a.APPROVED_SEQU_BY ={$APP_ROWS['FROM_APPROVED_BY']}  $buyer_con)";
            $eq_rr['mst'][] = $flag = execute_query($MST_SQL,0);
        }



   }


   //print_r($eq_rr['history']);oci_rollback($con);die;
    
 
	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo "0**".implode(',',$eq_rr['history'])."**".implode(',',$eq_rr['appmst'])."**".implode(',',$eq_rr['mst']);
	}
	disconnect($con);
    die;

}


else if ($action=="embellishment_work_order_approval_controller")
{
    //api link: approval/migrate/app_data_sync_api.php?action=embellishment_work_order_approval_controller
  

    //SYNC .................................
    define('ENTRY_FORM',32);
    define('COMPANY_ID',3);


    $sync_data_arr[1] = [
        'COMPANY_ID' => COMPANY_ID,
        'ENTRY_FORM' => ENTRY_FORM,
        'BUYER_ID' => '62',
        'FROM_APPROVED_BY' => 579,
        'FROM_SEQUENCE_NO' => 1,
        'TO_APPROVED_BY' => 681,
        'TO_SEQUENCE_NO' => 3,
    ];


    $sync_data_arr[2] = [
        'COMPANY_ID' => COMPANY_ID,
        'ENTRY_FORM' => ENTRY_FORM,
        'BUYER_ID' => '37,99,22,14,17',
        'FROM_APPROVED_BY' => 681,
        'FROM_SEQUENCE_NO' => 3,
        'TO_APPROVED_BY' => 579,
        'TO_SEQUENCE_NO' => 1,
    ];

      $sync_data_arr[3] = [
        'COMPANY_ID' => COMPANY_ID,
        'ENTRY_FORM' => ENTRY_FORM,
        'BUYER_ID' => '69',
        'FROM_APPROVED_BY' => 681,
        'FROM_SEQUENCE_NO' => 3,
        'TO_APPROVED_BY' => 682,
        'TO_SEQUENCE_NO' => 4,
    ];  
  
    


    $con = connect();     
    $flag = 1;
   foreach($sync_data_arr as $APP_ROWS){
        
        //history......................................................................
        if($flag == 1){
            if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and a.BUYER_ID in(".$APP_ROWS['BUYER_ID'].")";}

            $APPROVAL_HISTORY_SQL = "update  APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']." where ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM']."  AND APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." AND SEQUENCE_NO = ".$APP_ROWS['FROM_SEQUENCE_NO']." and MST_ID  in(select a.id from wo_booking_mst a where  a.company_id={$APP_ROWS['COMPANY_ID']} and a.is_short in(2,3) and a.booking_type=6 and a.item_category=25 and a.status_active=1 and a.is_deleted=0 and a.ready_to_approved=1 and a.is_approved  in (1,3)  $buyer_con)";
            $eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
        }


   }


   //print_r($eq_rr['history']);oci_rollback($con);die;
    
 
	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo "0**".implode(',',$eq_rr['history'])."**".implode(',',$eq_rr['appmst'])."**".implode(',',$eq_rr['mst']);
	}
	disconnect($con);
    die;

}

else if ($action=="yarn_work_order_approval_controller")
{
    //api link: approval/migrate/app_data_sync_api.php?action=yarn_work_order_approval_controller
  
    //SYNC .................................
    define('ENTRY_FORM',2);
    define('COMPANY_ID',1);

    $sync_data_arr[1] = [
        'COMPANY_ID' => COMPANY_ID,
        'ENTRY_FORM' => ENTRY_FORM,
        'BUYER_ID' => '16',
        'FROM_APPROVED_BY' => 72,
        'FROM_SEQUENCE_NO' => 1,
        'TO_APPROVED_BY' => 636,
        'TO_SEQUENCE_NO' => 1,
    ];

    $sync_data_arr[2] = [
        'COMPANY_ID' => COMPANY_ID,
        'ENTRY_FORM' => ENTRY_FORM,
        'BUYER_ID' => '16',
        'FROM_APPROVED_BY' => 68,
        'FROM_SEQUENCE_NO' => 2,
        'TO_APPROVED_BY' => 388,
        'TO_SEQUENCE_NO' => 2,
    ];


    $con = connect();     
    $flag = 1;
   foreach($sync_data_arr as $APP_ROWS){

        //history......................................................................
        if($flag == 1){
            $APPROVAL_HISTORY_SQL = "update APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']." where id in(
                select id from APPROVAL_HISTORY where mst_id in(select a.id  from wo_non_order_info_mst a,wo_non_order_info_dtls b where a.id = b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.entry_form=144  and b.item_category_id=1 and a.ready_to_approved=1 and a.company_name=".$APP_ROWS['COMPANY_ID']."  and a.is_approved in(1,3)) and SEQUENCE_NO=".$APP_ROWS['FROM_SEQUENCE_NO']." and APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." and ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM']."
                )";
            $eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
        }
   }


   //print_r($eq_rr['history']);oci_rollback($con);die;
    
 
	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo "0**".implode(',',$eq_rr['history'])."**".implode(',',$eq_rr['appmst'])."**".implode(',',$eq_rr['mst']);
	}
	disconnect($con);
    die;

}

else if ($action=="service_work_order_approval_controller")
{
    //api link: approval/migrate/app_data_sync_api.php?action=service_work_order_approval_controller
  
    //SYNC .................................
    define('ENTRY_FORM',60);
    define('COMPANY_ID',1);

    $sync_data_arr[1] = [
        'COMPANY_ID' => COMPANY_ID,
        'ENTRY_FORM' => ENTRY_FORM,
        'BUYER_ID' => '',
        'FROM_APPROVED_BY' => 68,
        'FROM_SEQUENCE_NO' => 1,
        'TO_APPROVED_BY' => 388,
        'TO_SEQUENCE_NO' => 1,
    ];




    $con = connect();     
    $flag = 1;
   foreach($sync_data_arr as $APP_ROWS){

        //history......................................................................
        if($flag == 1){
            $APPROVAL_HISTORY_SQL = "update APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']." where id in(
                select id from APPROVAL_HISTORY where mst_id in(select a.id  from wo_non_order_info_mst a where a.status_active=1 and a.is_deleted=0 and a.entry_form=484 and a.ready_to_approved=1 and a.company_name=".$APP_ROWS['COMPANY_ID']."  and a.is_approved in(1,3)) and SEQUENCE_NO=".$APP_ROWS['FROM_SEQUENCE_NO']." and APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." and ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM']."
                )";
            $eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
        }
   }


   //print_r($eq_rr['history']);oci_rollback($con);die;
    
 
	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo "0**".implode(',',$eq_rr['history'])."**".implode(',',$eq_rr['appmst'])."**".implode(',',$eq_rr['mst']);
	}
	disconnect($con);
    die;

}

else if ($action=="stationary_work_order_approval_controller")
{
    //api link: approval/migrate/app_data_sync_api.php?action=stationary_work_order_approval_controller
  
    //SYNC .................................
    define('ENTRY_FORM',5);
    define('COMPANY_ID',1);

    $sync_data_arr[1] = [
        'COMPANY_ID' => COMPANY_ID,
        'ENTRY_FORM' => ENTRY_FORM,
        'BUYER_ID' => '',
        'FROM_APPROVED_BY' => 68,
        'FROM_SEQUENCE_NO' => 1,
        'TO_APPROVED_BY' => 388,
        'TO_SEQUENCE_NO' => 1,
    ];




    $con = connect();     
    $flag = 1;
   foreach($sync_data_arr as $APP_ROWS){

        //history......................................................................
        if($flag == 1){
            $APPROVAL_HISTORY_SQL = "update APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']." where id in(
                select id from APPROVAL_HISTORY where mst_id in(select a.id  from wo_non_order_info_mst a,wo_non_order_info_dtls b where a.id = b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.entry_form=146  and b.item_category_id not in(1,2,3,12,13,14) and a.ready_to_approved=1 and a.company_name=".$APP_ROWS['COMPANY_ID']."  and a.is_approved in(1,3)) and SEQUENCE_NO=".$APP_ROWS['FROM_SEQUENCE_NO']." and APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." and ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM']."
                )";
            $eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
        }
   }


   //print_r($eq_rr['history']);oci_rollback($con);die;
    
 
	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo "0**".implode(',',$eq_rr['history'])."**".implode(',',$eq_rr['appmst'])."**".implode(',',$eq_rr['mst']);
	}
	disconnect($con);
    die;

}



else if ($action=="dyes_chemical_wo_approval_controller")
{
    //api link: approval/migrate/app_data_sync_api.php?action=dyes_chemical_wo_approval_controller
  
    //SYNC .................................
    define('ENTRY_FORM',3);
    define('COMPANY_ID',1);

    $sync_data_arr[1] = [
        'COMPANY_ID' => COMPANY_ID,
        'ENTRY_FORM' => ENTRY_FORM,
        'BUYER_ID' => '',
        'FROM_APPROVED_BY' => 68,
        'FROM_SEQUENCE_NO' => 1,
        'TO_APPROVED_BY' => 388,
        'TO_SEQUENCE_NO' => 1,
    ];




    $con = connect();     
    $flag = 1;
   foreach($sync_data_arr as $APP_ROWS){

        //history......................................................................
        if($flag == 1){
            $APPROVAL_HISTORY_SQL = "update APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']." where id in(
                select id from APPROVAL_HISTORY where mst_id in(select a.id  from wo_non_order_info_mst a,wo_non_order_info_dtls b where a.id = b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.entry_form=145  and b.item_category_id in (5,6,7,23) and a.ready_to_approved=1 and a.company_name=".$APP_ROWS['COMPANY_ID']."  and a.is_approved in(1,3)) and SEQUENCE_NO=".$APP_ROWS['FROM_SEQUENCE_NO']." and APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." and ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM']."
                )";
            $eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
        }
   }


   //print_r($eq_rr['history']);oci_rollback($con);die;
    
 
	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo "0**".implode(',',$eq_rr['history'])."**".implode(',',$eq_rr['appmst'])."**".implode(',',$eq_rr['mst']);
	}
	disconnect($con);
    die;

}
 


else if ($action=="other_purchase_work_order_approval_controller")
{
    //api link: approval/migrate/app_data_sync_api.php?action=other_purchase_work_order_approval_controller
  
    //SYNC .................................
    define('ENTRY_FORM',17);
    define('COMPANY_ID',1);

    $sync_data_arr[1] = [
        'COMPANY_ID' => COMPANY_ID,
        'ENTRY_FORM' => ENTRY_FORM,
        'BUYER_ID' => '',
        'FROM_APPROVED_BY' => 68,
        'FROM_SEQUENCE_NO' => 1,
        'TO_APPROVED_BY' => 388,
        'TO_SEQUENCE_NO' => 1,
    ];




    $con = connect();     
    $flag = 1;
   foreach($sync_data_arr as $APP_ROWS){

        //history......................................................................
        if($flag == 1){
            $APPROVAL_HISTORY_SQL = "update APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']." where id in(
                select id from APPROVAL_HISTORY where mst_id in(select a.id  from wo_non_order_info_mst a,wo_non_order_info_dtls b where a.id = b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.entry_form=147  and b.item_category_id not in(1,4,5,6,7,11,23) and a.ready_to_approved=1 and a.company_name=".$APP_ROWS['COMPANY_ID']."  and a.is_approved in(1,3)) and SEQUENCE_NO=".$APP_ROWS['FROM_SEQUENCE_NO']." and APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." and ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM']."
                )";
            $eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
        }
   }


   //print_r($eq_rr['history']);oci_rollback($con);die;
    
 
	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo "0**".implode(',',$eq_rr['history'])."**".implode(',',$eq_rr['appmst'])."**".implode(',',$eq_rr['mst']);
	}
	disconnect($con);
    die;

}

else if ($action=="sample_additional_yarn_workorder_approval_group_by_controller")
{
    //api link: approval/migrate/app_data_sync_api.php?action=sample_additional_yarn_workorder_approval_group_by_controller
  
    //SYNC .................................
    define('ENTRY_FORM',53);
    define('COMPANY_ID',1);

    $sync_data_arr[1] = [
        'COMPANY_ID' => COMPANY_ID,
        'ENTRY_FORM' => ENTRY_FORM,
        'BUYER_ID' => '',
        'FROM_APPROVED_BY' => 68,
        'FROM_SEQUENCE_NO' => 1,
        'TO_APPROVED_BY' => 388,
        'TO_SEQUENCE_NO' => 1,
    ];




    $con = connect();     
    $flag = 1;
   foreach($sync_data_arr as $APP_ROWS){

        //history......................................................................
        if($flag == 1){
       
        }
   }


   //print_r($eq_rr['history']);oci_rollback($con);die;
    
 
	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo "0**".implode(',',$eq_rr['history'])."**".implode(',',$eq_rr['appmst'])."**".implode(',',$eq_rr['mst']);
	}
	disconnect($con);
    die;

}


else if ($action=="price_quatation_approval_controller")
{
    //api link: approval/migrate/app_data_sync_api.php?action=price_quatation_approval_controller
  
    //SYNC .................................
    define('ENTRY_FORM',10);
    define('COMPANY_ID',1);

    $sync_data_arr[1] = [
        'COMPANY_ID' => COMPANY_ID,
        'ENTRY_FORM' => ENTRY_FORM,
        'BUYER_ID' => '185,100',
        'FROM_APPROVED_BY' => 49,
        'FROM_SEQUENCE_NO' => 9,
        'TO_APPROVED_BY' => 189,
        'TO_SEQUENCE_NO' => 10,
    ];




    $con = connect();     
    $flag = 1;
   foreach($sync_data_arr as $APP_ROWS){

    //history......................................................................
    if($flag == 1){
        if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and a.buyer_id in(".$APP_ROWS['BUYER_ID'].")";}
        $APPROVAL_HISTORY_SQL = "update APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']." where id in(
            
            select id from APPROVAL_HISTORY where mst_id in(
                select a.id from wo_price_quotation a where a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and a.approved in(1,3)  and a.company_id=".$APP_ROWS['COMPANY_ID']." $buyer_con ) and SEQUENCE_NO=".$APP_ROWS['FROM_SEQUENCE_NO']." and APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." and ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM']."
            )";
            //echo $APPROVAL_HISTORY_SQL;die;
        $eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
    }
   }


   //print_r($eq_rr['history']);oci_rollback($con);die;
    
 
	if($flag==1)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo "0**".implode(',',$eq_rr['history'])."**".implode(',',$eq_rr['appmst'])."**".implode(',',$eq_rr['mst']);
	}
	disconnect($con);
    die;

}

