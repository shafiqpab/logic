<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
header("location:login.php");
include('../../../includes/common.php');
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$user_id = $_SESSION['logic_erp']['user_id'];
$module=return_library_array( "select m_mod_id,main_module from main_module where status=1", "m_mod_id", "main_module" );

if($action == "is_used_body_part"){
	$is_used_body_part=sql_select("SELECT id from wo_pre_cost_fabric_cost_dtls where body_part_id='$data' and status_active=1 and is_deleted=0 group by id");
	

	if(count($is_used_body_part)>0 ){
		echo 1;
		exit();
	}
	else{
		echo 0;
		exit();
	}
}

if ($action == "body_part_list_view") 
{

    $item_arr=return_library_array( "select id, item_name from  lib_garment_item", "id", "item_name"  );	
    $operation_name_arr=return_library_array( "select id, operation_name from  lib_sewing_operation_entry", "id", "operation_name");
    $arr = array(0 => $item_arr, 1 => $operation_name_arr,2=>$row_status);
     $sql="select id, item_id, operation_id, status from  garments_item_wise_operation where is_deleted=0 order by id desc";

    echo create_list_view("list_view", "Item Name,Operation Name,Status", "200,150,100", "650", "220", 1,$sql,"get_php_form_data", "id", "'load_php_data_to_form'", 1, "item_id,operation_id,status", $arr, "item_id,operation_id,status", "requires/garments_item_wise_operation_controller", 'setFilterGrid("list_view",-1);', '0,0,0');
}

if ($action == "load_php_data_to_form") 
{
    $nameArray = sql_select("select id, item_id,operation_id,status,operation_name from garments_item_wise_operation where is_deleted=0 and id='$data'");
    foreach ($nameArray as $inf) 
    {
        $operation_name_arr=return_library_array( "select id, operation_name from  lib_sewing_operation_entry", "id", "operation_name"  );
      
	    echo "document.getElementById('update_id').value = '" .$inf[csf("id")] . "';\n";
        echo "document.getElementById('cbo_item_name').value  = '" . $inf[csf("item_id")] . "';\n";
        echo "document.getElementById('txt_operation_id').value  = '" . $inf[csf("operation_id")]. "';\n";
        echo "document.getElementById('cbo_status').value  = '" . $inf[csf("status")] . "';\n";
        echo "document.getElementById('txt_operation_name').value  = '" . $inf[csf("operation_name")] . "';\n";
        
       
        echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_body_part_entry',1);\n";
    }
}

if ($action =="save_update_delete") 
{
    $process = array(&$_POST);
   
    extract(check_magic_quote_gpc($process));
    $cut_lay_entry=str_replace("'", "", $cut_lay_entry);
    if ($operation == 0) 
    {  
        $con = connect();
        if ($db_type == 0) 
        {
            mysql_query("BEGIN");
        }

        $id = return_next_id("id", "garments_item_wise_operation", 1); 
        
        $field_array = "id,item_id,operation_id,operation_name,status,inserted_by,insert_date,status_active,is_deleted";
        $data_array = "(".$id . "," . $cbo_item_name . "," . $txt_operation_id. "," . $txt_operation_name . ",". $cbo_status . "," . $user_id . ",'" . $pc_date_time . "'," . $cbo_status . ",0)";

        $rID = sql_insert("garments_item_wise_operation", $field_array, $data_array, 1);
        
    //    echo "10** insert into garments_item_wise_operation ($field_array) values  $data_array";die;
    //   echo "10**".$rID."**".$rID_1; die;
        if ($db_type == 0) 
		{
            if ($rID)
             {
                mysql_query("COMMIT");
                echo "0**" . $rID;
             } 
            else
             {
                mysql_query("ROLLBACK");
                echo "10**" . $rID;
             }
        }
        else if ($db_type == 2 || $db_type == 1) 
		{
            if ($rID) 
            {
                oci_commit($con);
                echo "0**" . $rID;
            } 
            else
             {
                oci_rollback($con);
                echo "10**" . $rID;
             }
        }
        disconnect($con);
        die;
       
    }
     else if ($operation == 1)
     {  
        $con = connect();
        if ($db_type == 0) {
            mysql_query("BEGIN");
        }

       $field_array = "id,item_id,operation_id,status*updated_by*update_date*status_active*is_deleted";
       $data_array = "" . $cbo_item_name . "*" . $txt_operation_id. "*" . $cbo_status. "*". $cbo_status. "*" .$user_id ."*'". $pc_date_time. "'*".$cbo_status."*0";
        
        $rID = sql_update("garments_item_wise_operation", $field_array, $data_array, "id", "" . $update_id . "", 1);
              
        if ($db_type == 0) 
        {
            if ($rID )
            {
                mysql_query("COMMIT");
                echo "1**" . str_replace("'", "", $update_id);
            }
            else
            {
                mysql_query("ROLLBACK");
                echo "10**" . str_replace("'", "", $update_id);
            }
        }
        if ($db_type == 2 || $db_type == 1) 
        {
            if ($rID) {
                oci_commit($con);
                echo "1**" . str_replace("'", "", $update_id);
            } else {
                oci_rollback($con);
                echo "10**" . str_replace("'", "", $update_id);
            }
        }
        disconnect($con);
        die;
    } 
    else if ($operation == 2)
     {  
        $con = connect();
        if ($db_type == 0) {
            mysql_query("BEGIN");
        }

        $field_array = "updated_by*update_date*status_active*is_deleted";
        $data_array = "" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*'0'*'1'";
        $rID = sql_delete("garments_item_wise_operation", $field_array, $data_array, "id", "" . $update_id . "", 1);

        if ($db_type == 0) 
        {
            if ($rID) 
            {
                mysql_query("COMMIT");
                echo "2**" . $rID;
            }
             else 
             {
                mysql_query("ROLLBACK");
                echo "10**" . $rID;
             }
        }

        if ($db_type == 2 || $db_type == 1) 
        {
            if ($rID)
             {
                oci_commit($con);
                echo "2**" . str_replace("'", "", $update_id);
             }
             else 
             {
                oci_rollback($con);
                echo "10**" . str_replace("'", "", $update_id);
             }
        }
        disconnect($con);
    }
}


if($action=="operation_name_popup")
{
    echo load_html_head_contents("Entry Page Info","../../../", 1, 1, '','1','');
    extract($_REQUEST);
?>
    <script>
    
        $(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });
        
        var selected_id = new Array(); var selected_name = new Array();
        
        function check_all_data() 
        {
            var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 

            tbl_row_count = tbl_row_count-1;
            for( var i = 1; i <= tbl_row_count; i++ ) {
                js_set_value( i );
            }
        }
        
        function toggle( x, origColor ) {
            var newColor = 'yellow';
            if ( x.style ) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
            }
        }
        
        function set_all()
        {
            var old=document.getElementById('txt_entry_page_row_id').value; 
            if(old!="")
            {   
                old=old.split(",");
                for(var k=0; k<old.length; k++)
                {   
                    js_set_value( old[k] ) 
                } 
            }
        }
        
        function js_set_value( str ) 
        {
            
            toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
            
            if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
                selected_id.push( $('#txt_individual_id' + str).val() );
                selected_name.push( $('#txt_individual' + str).val() );
                
            }
            else {
                for( var i = 0; i < selected_id.length; i++ ) {
                    if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
                }
                selected_id.splice( i, 1 );
                selected_name.splice( i, 1 );
            }
            
            var id = ''; var name = '';
            for( var i = 0; i < selected_id.length; i++ ) {
                id += selected_id[i] + ',';
                name += selected_name[i] + ',';
            }
            
            id = id.substr( 0, id.length - 1 );
            name = name.substr( 0, name.length - 1 );
            
            $('#operation_id').val(id);
            $('#operation_id_name').val(name);
        }
    </script>

</head>
<body>
<div align="center">
    <fieldset style="width:370px;margin-left:10px">
        <input type="hidden" name="operation_id" id="operation_id" class="text_boxes" value="">
        <input type="hidden" name="operation_id_name" id="operation_id_name" class="text_boxes" value="">
        <form name="searchbuyerfrm_1"  id="searchbuyerfrm_1" autocomplete="off">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
                <thead>
                    <th width="50">SL</th>
                    <th>Operation Name</th>
                </thead>
            </table>
            <div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
                <?
                $sql_entry_page=sql_select("SELECT id, operation_name from lib_sewing_operation_entry where STATUS_ACTIVE=1  order by id");
                    $i=1; 
                    // $sql_data=sql_select("SELECT ID, OPERATION_NAME from lib_sewing_operation_entry");
                    foreach($sql_entry_page as $row)
                    {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";         
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
                                <td width="50" align="center"><?php echo "$i"; ?>
                                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>  
                                    <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf("operation_name")] ?>"/>
                                </td>   
                                <td><p><? echo $row[csf("operation_name")] ?></p></td>
                            </tr>
                            <?
                            $i++;
                    }
                ?>
                    <input type="hidden" name="txt_entry_page_row_id" id="txt_entry_page_row_id" value="<?php echo $entry_page_row_id; ?>"/>
                </table>
            </div>
             <table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
                <tr>
                    <td align="center" height="30" valign="bottom">
                        <div style="width:100%"> 
                            <div style="width:50%; float:left" align="left">
                                <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                            </div>
                            <div style="width:50%; float:left" align="left">
                                <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>
</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
    set_all();
</script>
</html>
<?
exit();
}
?>