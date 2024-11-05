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


if ($action == "body_part_list_view") 
{
  /* $sql_entry_page=sql_select("select m_menu_id,menu_name from main_menu where status=1  order by menu_name");
    foreach($sql_entry_page as $row)
    {
            $entry_page_arr[$row[csf('m_menu_id')]]=$row[csf('menu_name')];
    }*/
    unset($sql_entry_page);
    $sqls=sql_select("select id,entry_page_id from lib_body_part where is_deleted=0");
    foreach($sqls as $val)
    {
        if($val[csf("entry_page_id")]!="" || $val[csf("entry_page_id")]!=0)
        {
            if(strpos($val[csf("entry_page_id")], ",")==false)
            {
               $entry_page[$val[csf("id")]]=$entry_form[$val[csf('entry_page_id')]];
            }
            else
            {
                $menu_name="";
                $vals=explode(",", $val[csf("entry_page_id")]);
                foreach($vals as $menu_id)
                {
                    if($menu_name=="") {$menu_name .=$entry_form[$menu_id];}
                    else{$menu_name .=','.$entry_form[$menu_id];}
                }
                $entry_page[$val[csf("id")]]=$menu_name;
            }
        }
        
    }
    unset($sqls);
   
    $arr = array(2 => $body_part_type, 3 => $row_status,4=>$entry_page);
    $sql="select body_part_full_name,body_part_short_name,body_part_type,status,id,entry_page_id from  lib_body_part where is_deleted=0 order by id desc";
    
    echo create_list_view("list_view", "Body Part Full Name,Body Part Short Name,Body Part Type,Status,Entry Page", "200,150,100,70,200", "850", "220", 1,$sql,"get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,body_part_type,status,id", $arr, "body_part_full_name,body_part_short_name,body_part_type,status,id", "requires/body_part_entry_controller", 'setFilterGrid("list_view",-1);', '0,0,0,0');
       
}

if ($action == "load_php_data_to_form") 
{
    $nameArray = sql_select("select id, body_part_full_name, body_part_short_name,entry_page_id, body_part_type,status,is_emplishment from lib_body_part where is_deleted=0 and id='$data'");
    foreach ($nameArray as $inf) 
    {
       //entry_form
	   $entry_page=explode(",",$inf[csf("entry_page_id")]);
	   $page_name="";
	   foreach($entry_page as $page_id)
	   {
		  if($page_name=="") $page_name=$entry_form[$page_id];else $page_name.=",".$entry_form[$page_id]; 
	   }
      
	    echo "document.getElementById('update_id').value = '" .$inf[csf("id")] . "';\n";
        echo "document.getElementById('body_part_full_name').value  = '" . $inf[csf("body_part_full_name")] . "';\n";
        echo "document.getElementById('body_part_short_name').value  = '" . $inf[csf("body_part_short_name")] . "';\n";
        echo "document.getElementById('body_part_type').value  = '" . $inf[csf("body_part_type")] . "';\n";
         echo "document.getElementById('txt_entry_page_id').value  = '" . $inf[csf("entry_page_id")] . "';\n";
		 echo "document.getElementById('txt_entry_page').value  = '" . $page_name . "';\n";
        echo "document.getElementById('cbo_status').value  = '" . $inf[csf("status")] . "';\n";
        
       if($inf[csf("is_emplishment")]==1)
       {
            echo "document.getElementById('cut_lay_entry').checked=true;\n";

       }
       else
       {

           echo "document.getElementById('cut_lay_entry').checked=false;\n";
       }
       echo "document.getElementById('cut_lay_entry').value  = '" . $inf[csf("is_emplishment")] . "';\n";
       
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

        $id = return_next_id("id", "lib_body_part", 1); 
        
        $field_array = "id,body_part_full_name,body_part_short_name,entry_page_id,body_part_type,is_emplishment,status,inserted_by,insert_date,status_active,is_deleted";
        $data_array = "(" . $id . "," . $body_part_full_name . "," . $body_part_short_name . "," . $txt_entry_page_id . "," . $body_part_type. "," . $cut_lay_entry . "," . $cbo_status . "," . $user_id . ",'" . $pc_date_time . "'," . $cbo_status . ",0)";
        $field_array_entry_page="id,mst_id,entry_page_id,inserted_by,insert_date,status_active,is_deleted";
        $dtls_id=return_next_id( "id", "lib_body_part_tag_entry_page", 1 );
        $data_array_entry_page="";
        $entry_page=explode(',',str_replace("'","",$txt_entry_page_id));
        for($i=0; $i<count($entry_page); $i++)
        {
            if($data_array_entry_page=="") $data_array_entry_page=""; else $data_array_entry_page.=",";
            $data_array_entry_page.="(".$dtls_id.",".$id.",'".$entry_page[$i]."'," . $user_id . ",'" . $pc_date_time . "',1,0)";
            $dtls_id++;
        }
        $rID = sql_insert("lib_body_part", $field_array, $data_array, 1);
        $rID_1=sql_insert("lib_body_part_tag_entry_page",$field_array_entry_page,$data_array_entry_page,1);
        
    //  echo "10** insert into lib_body_part ($field_array) values  $data_array";die;
    //   echo "10**".$rID."**".$rID_1; die;
        if ($db_type == 0) 
		{
            if ($rID and $rID_1 and $rID_2)
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
            if ($rID and $rID_1) 
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

       $field_array = "body_part_full_name*body_part_short_name*entry_page_id*body_part_type*is_emplishment*status*updated_by*update_date*status_active*is_deleted";
       $data_array = "" . $body_part_full_name . "*" . $body_part_short_name . "*" . $txt_entry_page_id . "*" . $body_part_type. "*" . $cut_lay_entry . "*" . $cbo_status . "*" . $user_id . "*'" . $pc_date_time . "'*" . $cbo_status . "*0";
       $field_array_entry_page="id,mst_id,entry_page_id,inserted_by,insert_date,status_active,is_deleted";
        $dtls_id=return_next_id( "id", "lib_body_part_tag_entry_page", 1 );
        $data_array_entry_page="";
        $entry_page=explode(',',str_replace("'","",$txt_entry_page_id));
        if(count($entry_page)>0)
        {
            for($i=0; $i<count($entry_page); $i++)
            {
                if($data_array_entry_page=="") $data_array_entry_page=""; else $data_array_entry_page.=",";
                $data_array_entry_page.="(".$dtls_id.",".$update_id.",".$entry_page[$i]."," . $user_id . ",'" . $pc_date_time . "',1,0)";
                $dtls_id++;
            }
             $rID1=execute_query( "delete from lib_body_part_tag_entry_page where  mst_id = $update_id",0);
             $rID_1=sql_insert("lib_body_part_tag_entry_page",$field_array_entry_page,$data_array_entry_page,1);

        }
        
        $rID = sql_update("lib_body_part", $field_array, $data_array, "id", "" . $update_id . "", 1);
              
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
        $rID1=execute_query( "delete from lib_body_part_tag_entry_page where  mst_id = $update_id",0);
        $rID = sql_delete("lib_body_part", $field_array, $data_array, "id", "" . $update_id . "", 1);

        if ($db_type == 0) 
        {
            if ($rID and $rID1) 
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
            if ($rID and $rID1)
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


if($action=="entry_page_popup")
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
            
            $('#hidden_entry_page_id').val(id);
            $('#hidden_entry_page_name').val(name);
        }
    </script>

</head>
<body>
<div align="center">
    <fieldset style="width:370px;margin-left:10px">
        <input type="hidden" name="hidden_entry_page_id" id="hidden_entry_page_id" class="text_boxes" value="">
        <input type="hidden" name="hidden_entry_page_name" id="hidden_entry_page_name" class="text_boxes" value="">
        <form name="searchbuyerfrm_1"  id="searchbuyerfrm_1" autocomplete="off">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
                <thead>
                    <th width="50">SL</th>
                    <th>Page Name</th>
                </thead>
            </table>
            <div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
                <?
                /*$sql_entry_page=sql_select("select m_menu_id,menu_name,m_module_id,fabric_nature from main_menu where status=1  order by menu_name");
                 foreach($sql_entry_page as $row) 
                    {
                         $entry_page_arr[$row[csf('m_menu_id')]]=$row[csf('menu_name')].' ('.$module[$row[csf('m_module_id')]].' ) '.$item_category[$row[csf('fabric_nature')]];
                    }*/
                    $i=1; $entry_page_row_id=""; 
                    $hidden_entry_page_id=explode(",",$txt_entry_page_id);
                    foreach($entry_form as $id=>$name)
                    {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                             
                            if(in_array($id,$hidden_entry_page_id)) 
                            { 
                                if($entry_page_row_id=="") $entry_page_row_id=$i; else $entry_page_row_id.=",".$i;
                            }
                            
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
                                <td width="50" align="center"><?php echo "$i"; ?>
                                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>  
                                    <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $name; ?>"/>
                                    <input type="hidden" name="txt_mandatory" id="txt_mandatory<?php echo $i ?>" value="<? echo $mandatory; ?>"/>
                                </td>   
                                <td><p><? echo $name; ?></p></td>
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