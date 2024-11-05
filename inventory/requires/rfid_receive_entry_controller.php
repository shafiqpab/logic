<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="save_update_delete")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
    $con = connect();
    if($db_type==0)
    {
        mysql_query("BEGIN");
    }
    if ($operation==0)  // Insert Here
    {
        // $receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_receive_date)));
        // duplicate check
        if (is_duplicate_field( "id", "lib_rfid_mst", "rfid_number=$rfid_no and rfid_type=$cbo_rfid_type and receive_date=$receive_date and STATUS_ACTIVE=$cbo_status and is_deleted=0" ) == 1)
        {
            
            echo "11**0"; disconnect($con); die;
        }
        else
        {
            $id=return_next_id( "id", "lib_rfid_mst", 1 ) ;
            $field_array="id,rfid_number,rfid_type,receive_date,status_active,inserted_by,insert_date, is_deleted";
            $data_array="(".$id.",".$rfid_no.",".$cbo_rfid_type.",".$txt_receive_date.",".$cbo_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0)";
            //  echo "INSERT INTO LIB_RFID_MST ($field_array) VALUES $data_array";die;
            $rID=sql_insert("LIB_RFID_MST",$field_array,$data_array,1);
            if($db_type==0)
            {
                if($rID ){
                    mysql_query("COMMIT");  
                    echo "0**".$rID;
                }
                else{
                    mysql_query("ROLLBACK"); 
                    echo "10**".$rID;
                }
            }
            if($db_type==2 || $db_type==1 )
            {
                if($rID )
                {
                    oci_commit($con);   
                    echo "0**".$rID;
                }
                else{
                    oci_rollback($con);
                    echo "10**".$rID;
                }
            }
            disconnect($con);
            die;
        }
    }
    else if ($operation==1)   // Update Here
    {
        // duplicate check based on section, reason type, reason
        if (is_duplicate_field( "id", "LIB_RFID_MST", "rfid_number=$rfid_no and rfid_type=$cbo_rfid_type  and status_active=$cbo_status and is_deleted=0 and id <> $update_id " ) == 1)
        {
            echo "11**0"; disconnect($con); die;
        }
        
        else
        {
            $field_array="rfid_number*rfid_type*receive_date*status_active*updated_by*update_date";
            $data_array="".$rfid_no."*".$cbo_rfid_type."*".$txt_receive_date."*".$cbo_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
            
            $rID=sql_update("lib_rfid_mst",$field_array,$data_array,"id","".$update_id."",1);
            if($db_type==0)
            {
                if($rID ){
                    mysql_query("COMMIT");  
                    echo "1**".$rID;
                }
                else{
                    mysql_query("ROLLBACK"); 
                    echo "10**".$rID;
                }
            }
            if($db_type==2 || $db_type==1 )
            {
                if($rID )
                {
                    oci_commit($con);   
                    echo "1**".$rID;
                }
                else{
                    oci_rollback($con);
                    echo "10**".$rID;
                }
            }
        disconnect($con);
        die;
        }
    }
    // else if ($operation==2)   // delete Here
    // {

    //     $field_array="updated_by*update_date*status_active*is_deleted";
    //     $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
        
    //     $rID=sql_delete("lib_re_process_reason_entry",$field_array,$data_array,"id","".$update_id."",1);
        
    //     if($db_type==0)
    //     {
    //         if($rID ){
    //             mysql_query("COMMIT");  
    //             echo "1**".$rID;
    //         }
    //         else{
    //             mysql_query("ROLLBACK"); 
    //             echo "10**".$rID;
    //         }
    //     }
    //     if($db_type==2 || $db_type==1 )
    //         {
    //         if($rID )
    //             {
    //                 oci_commit($con);   
    //                 echo "2**".$rID;
    //             }
    //             else{
    //                 oci_rollback($con);
    //                 echo "10**".$rID;
    //             }
    //         }
    //     disconnect($con);
    //     die;
    // }
}
if ($action=="load_php_data_to_form")//load list view data to the form
{
    $nameArray=sql_select( "SELECT  id, RFID_NUMBER,RFID_TYPE, STATUS_ACTIVE,RECEIVE_DATE  from LIB_RFID_MST where id='$data'" );
    foreach ($nameArray as $inf)
    {
        
        echo "document.getElementById('rfid_no').value  = '".($inf[csf("RFID_NUMBER")])."';\n"; 
        echo "document.getElementById('cbo_rfid_type').value  = '".($inf[csf("RFID_TYPE")])."';\n";
    
        echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
        echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_rfid_receive_entry',1);\n";
        echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
        $date = change_date_format($inf[csf("RECEIVE_DATE")]);
        echo "document.getElementById('txt_receive_date').value  = '".($date)."';\n";
    }
}

if ($action=="rfid_list_view")
{ 
    $rfid_type_arr = array(1=>"Regular",2=>"Loose Bag");
    $status_arr = array(1=>"Active",2=>"Inactive");
    $arr = array(1 => $rfid_type_arr,2 => $status_arr);
    echo  create_list_view ( "list_view", "RFID No,RFID Type, Status", "120,150,180","450","150",1, "SELECT id, rfid_number,rfid_type, status_active  from lib_rfid_mst where is_deleted=0 order by id desc", "get_php_form_data", "id","'load_php_data_to_form'", 1, "0,rfid_type,status_active", $arr , "rfid_number,rfid_type,status_active", "../inventory/requires/rfid_receive_entry_controller", 'setFilterGrid("list_view",-1);' ) ;
}

?>
