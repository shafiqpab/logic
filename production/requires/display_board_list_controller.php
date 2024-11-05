<?
session_start();
include('../../includes/common.php');
 
$user_id = $_SESSION['logic_erp']["user_id"];
$user_level = $_SESSION['logic_erp']['user_level'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
 $action=$_REQUEST['action']; 

 if ($action=="search_list_view")
 {
    $sql = "SELECT a.id,a.report_name,a.report_link,a.status FROM  display_board_list a WHERE a.inserted_by = $user_id and a.status_active = 1 and is_deleted=0 order by id desc";
    $results = sql_select($sql); 
    ?>
        <div>
            <table class="rpt_table" id="rpt_tablelist_view" rules="all" width="580" cellspacing="0" cellpadding="0" border="0">
                <thead>
                    <tr>
                        <th width="30"> SL No </th>
                        <th width="150">  Report Name</th>
                        <th width="190"> Report Link </th>
                        <th width="70"> Status </th>
                        <th  width="30"> Action </th>
                    </tr>
                </thead>
            </table>
            <div style="max-height:220px; width:580px; overflow-y:scroll" id="tbl_scroll_body">
                <table class="rpt_table" id="list_view" rules="all" width="580" height="" cellspacing="0" cellpadding="0" border="0" align="left">
                    <tbody id="display_list_container"> 
                        <?php
                            $i=1; 
                            foreach ($results as $row) { 
                                $id = $row[csf('id')]  
                        ?>
                            <tr onclick="get_php_form_data(<?= $row[ csf('id')] ?>,'load_php_data_to_form','requires/display_board_list_controller')" style="cursor:pointer" id="tr_<?= $row[ csf('id')] ?>" height="20" bgcolor="#FFFFFF">
                                <td width="30"> <?= $i ?> </td>
                                <td width="150" align="left"> <p>  <?= $row[ csf('report_name')] ?> </p> </td>
                                <td width="190" align="left"> <p> <?= $row[ csf('report_link')] ?> </p></td>
                                <td  width="70" align="left"> <p> <?= $row_status[$row[ csf('status')]] ?> </td>
                                <td  width="30" align="left"> <a target="_blank" href="<?= $row[ csf('report_link')] ?>">View</td>
                            </tr>  
                        <?php  
                            $i++;     
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <script>
            setFilterGrid("list_view", -1);
        </script> 
    <?php 
 }
 
if ($action == 'load_php_data_to_form'){ 
      
    $con = connect();  
    $update_id =  $_REQUEST['data']; 
    $sql = "SELECT id,report_name,report_link,status  FROM  display_board_list  WHERE id=$update_id";
    $results = sql_select($sql);
    foreach ($results as $inf)
	{
		echo "document.getElementById('text_report_name').value = '".($inf[csf("report_name")])."';\n";    
		echo "document.getElementById('text_report_link').value  = '".($inf[csf("report_link")])."';\n";
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status")])."';\n";
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_iron_input',1);\n";  
	} 
    die;
} 
 
if ($action=="save_update_delete")
{ 
    
    $data = array( &$_POST ); 
    extract(check_magic_quote_gpc( $data )); 
    $con = connect(); 
     
    if ($operation == 0)
    {
        $con = connect();
        $field_array="id, report_name, report_link, status, status_active,is_deleted, inserted_by, insert_date"; 
        $id= return_next_id("id", "display_board_list", 1 );
        $table ='display_board_list';
        $data_array = "(".$id.",".$text_report_name.",".$text_report_link.",".$cbo_status.",1,0,".$user_id.",'".$pc_date_time."')";
        $flag = sql_insert("display_board_list",$field_array,$data_array,1); 
		if($db_type==1 || $db_type==2 )
		{
			if($flag==1)
			{
				oci_commit($con); 
				echo "0**";
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			} 
		} 
        
        disconnect($con); 
		die; 
    } 
    else if ($operation==1)   // Update Here
	{ 
		 
        $con = connect(); 
        $field_array="report_name*report_link*status*updated_by*update_date";
        $data_array="".$text_report_name."*".$text_report_link."*".$cbo_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
        $rID=sql_update("display_board_list",$field_array,$data_array,"id",$update_id,1);
        
            
        if($db_type==2 || $db_type==1 )
        {
            if($rID)
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
    else if ($operation==2)   // delete Here
	{  
        $con = connect(); 
		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$rID=sql_delete("display_board_list",$field_array,$data_array,"id","".$update_id."",1);
		
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