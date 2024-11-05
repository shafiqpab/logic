<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 262, "select location_name,id from lib_location where company_id='$data' and is_deleted=0  and status_active=1  order by location_name",'id,location_name', 1, '--- Select Location ---', 0, "load_drop_down( 'requires/sewing_line_controller', this.value, 'load_drop_down_floor', 'floor' )"  );
	exit();
}

if ($action=="load_drop_down_floor")
{
	//echo $data; die;
	echo create_drop_down( "cbo_floor_name", 262, "select floor_name,id from  lib_prod_floor where location_id='$data' and is_deleted=0  and status_active=1  order by floor_name",'id,floor_name', 1, '--- Select Floor ---', 0, '' );
	exit();
}

if ($action=="sewing_line_list_view")
{
	$floor=return_library_array( "select floor_name,id from  lib_prod_floor where is_deleted=0", "id", "floor_name"  );
	$arr=array(2=>$floor);
	echo  create_list_view ( "list_view", "Company,Location,Floor,Sewing Line,Sewing Group,Line Serial,Man Power", "120,120,80,80,70,70","650","220",1, "SELECT c.company_name,l.location_name,a.floor_name, a.sewing_line_serial, a.sewing_group, a.line_name,a.id,a.man_power from lib_sewing_line a, lib_company c, lib_location l  where a.company_name=c.id and a.location_name=l.id and a.is_deleted=0  order by a.id desc", "get_php_form_data", "id","'load_php_data_to_form'", 1, "0,0,floor_name", $arr , "company_name,location_name,floor_name,line_name,sewing_group,sewing_line_serial,man_power", "../production/requires/sewing_line_controller", 'setFilterGrid("list_view",-1);' ) ;		
	exit();
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "SELECT company_name, location_name, floor_name, sewing_line_serial, line_name, sewing_group, prod_catagory_id, status_active, id, man_power, user_ids,item_ids from lib_sewing_line where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "load_drop_down('requires/sewing_line_controller', '".($inf[csf("company_name")])."', 'load_drop_down_location', 'location');\n";
		echo "load_drop_down('requires/sewing_line_controller', '".($inf[csf("location_name")])."', 'load_drop_down_floor', 'floor');\n";
		echo "document.getElementById('cbo_company_name').value = '".($inf[csf("company_name")])."';\n";    
		echo "document.getElementById('cbo_location_name').value  = '".($inf[csf("location_name")])."';\n"; 
		echo "document.getElementById('cbo_floor_name').value  = '".($inf[csf("floor_name")])."';\n";
		echo "document.getElementById('txt_sewing_line_serial').value  = '".($inf[csf("sewing_line_serial")])."';\n";
		echo "document.getElementById('txt_line_name').value  = '".($inf[csf("line_name")])."';\n";
		echo "document.getElementById('txt_sewing_group').value  = '".($inf[csf("sewing_group")])."';\n";
		echo "document.getElementById('cbo_product_category').value  = '".($inf[csf("prod_catagory_id")])."';\n";
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		echo "document.getElementById('txt_user_ids').value  = '".($inf[csf("user_ids")])."';\n"; 
		echo "document.getElementById('txt_item_ids').value  = '".($inf[csf("item_ids")])."';\n"; 
		echo "document.getElementById('txt_man_power').value  = '".($inf[csf("man_power")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_sewing_line_info',1);\n"; 
		
		
		$user_sql="select ID,USER_NAME,UNIT_ID,COMPANY_LOCATION_ID from  USER_PASSWD where is_deleted=0 and STATUS_ACTIVE=1 and id in(".$inf[csf("user_ids")].")";
		$user_sql_res=sql_select( $user_sql);
		$user_arr=array();
		foreach ($user_sql_res as $rows)
		{
			$user_arr[$rows['ID']]=$rows['USER_NAME'];
		}
		echo "document.getElementById('txt_user_name').value  = '".(implode(',',$user_arr))."';\n"; 

		$item_sql="SELECT ID,ITEM_NAME from  LIB_GARMENT_ITEM where status_active = 1 and id in(".$inf[csf("item_ids")].")";
		$item_sql_res=sql_select( $item_sql);
		$item_arr=array();
		foreach ($item_sql_res as $rows)
		{
			$item_arr[$rows['ID']]=$rows['ITEM_NAME'];
		}
		echo "document.getElementById('txt_gmts_item').value  = '".(implode(',',$item_arr))."';\n"; 
	}
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		if (is_duplicate_field( "line_name", "lib_sewing_line", " line_name=$txt_line_name and company_name=$cbo_company_name and location_name=$cbo_location_name and floor_name=$cbo_floor_name and is_deleted=0" ) == 1)
		{
			echo "11**0"; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			//cbo_company_name,cbo_location_name,cbo_floor_name,txt_sewing_line_serial,txt_line_name,cbo_status,update_id
			//company_name,location_name,floor_name,sewing_line_serial,line_name,status_active,id
			
			$id=return_next_id( "id", " lib_sewing_line", 1 ) ;
			$field_array="id, company_name, location_name, floor_name, sewing_line_serial, line_name, sewing_group, prod_catagory_id, man_power,item_ids, inserted_by, insert_date, status_active, is_deleted";
			$data_array="(".$id.",".$cbo_company_name.",".$cbo_location_name.",".$cbo_floor_name.",".$txt_sewing_line_serial.",".$txt_line_name.",".$txt_sewing_group.",".$cbo_product_category.",".$txt_man_power.",".$txt_item_ids.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";
			$rID=sql_insert("lib_sewing_line",$field_array,$data_array,1);
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
		if (is_duplicate_field( "line_name", "lib_sewing_line", " line_name=$txt_line_name and company_name=$cbo_company_name and location_name=$cbo_location_name and id!=$update_id and floor_name=$cbo_floor_name and is_deleted=0" ) == 1)
		{
			echo "11**0"; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			
			$field_array="company_name*location_name*floor_name*sewing_line_serial*line_name*sewing_group*prod_catagory_id*man_power*user_ids*item_ids*updated_by*update_date*status_active*is_deleted";
			$data_array="".$cbo_company_name."*".$cbo_location_name."*".$cbo_floor_name."*".$txt_sewing_line_serial."*".$txt_line_name."*".$txt_sewing_group."*".$cbo_product_category."*".$txt_man_power."*".$txt_user_ids."*".$txt_item_ids."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*0";
			 
			$rID=sql_update("lib_sewing_line",$field_array,$data_array,"id","".$update_id."",1);
			 
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
	else if ($operation==2)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$rID=sql_delete("lib_sewing_line",$field_array,$data_array,"id","".$update_id."",1);
		
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
				echo "2**".$rID;
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

if($action=="get_user_list")
{
    echo load_html_head_contents("User List","../../../", 1, 1, '','1','');
    extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_location_name=str_replace("'","",$cbo_location_name);
    ?>
    <script>

        $(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });

        var selected_id = new Array(); var selected_name = new Array();

        function check_all_data()
        {
			var isChecked = $('#check_all').is(":checked");

			$('#tbl_list_search tr').each(function(index, value) {
			 var idTag=	$('td:eq(1)', this).attr("id");
			 	if(idTag){
					var dataArr = idTag.split('_'); 
					var color = $( '#search'+dataArr[1] ).css( "background-color" );
					if(color!='rgb(255, 255, 0)' && isChecked==true){
						js_set_value( dataArr[1] );
					}
					else if(isChecked==false){
						js_set_value( dataArr[1] );
					}
				 }
			});

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

        function js_set_value( id )
        {
			var name =document.getElementById('username_'+id).innerText;
			
			toggle( document.getElementById( 'search' + id ), '#FFFFCC' );
            if( jQuery.inArray( id, selected_id ) == -1 ) {
                selected_id.push(id);
                selected_name.push( name);
            }
            else {
                for( var i = 0; i < selected_id.length; i++ ) {
                    if( selected_id[i] == id) break;
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

            $('#selected_user_ids').val(id);
            $('#selected_user_name').val(name);
            //parent.emailwindow.hide();
        }
    </script>

    </head>
    <body>
    <div align="center">
        <fieldset style="width:370px;margin-left:10px">
            
			<input type="hidden" name="selected_user_ids" id="selected_user_ids" value="">
            <input type="hidden" name="selected_user_name" id="selected_user_name" value="">
           
			
			<form name="searchbuyerfrm_1"  id="searchbuyerfrm_1" autocomplete="off">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
                    <thead>
                        <th width="50">SL</th>
                        <th>User Name</th>
                    </thead>
                </table>
                <div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
                    <?

				   $user_sql="select ID,USER_NAME,UNIT_ID,COMPANY_LOCATION_ID from  USER_PASSWD where valid = 1";
				   $user_sql_res=sql_select( $user_sql);
				   $user_arr=array();
				   foreach ($user_sql_res as $rows)
				   {
						
						if($rows['UNIT_ID'] && $rows['COMPANY_LOCATION_ID']){
							foreach(explode(',',$rows['UNIT_ID']) as $com_id){
								foreach(explode(',',$rows['COMPANY_LOCATION_ID']) as $loc_id){
									if($com_id==$cbo_company_name && $loc_id==$cbo_location_name){
										$user_arr[$rows['ID']]=$rows['USER_NAME'];	
									}
								}	
							}
						}
						else if($rows['UNIT_ID']){
							foreach(explode(',',$rows['UNIT_ID']) as $com_id){
								if($com_id==$cbo_company_name){
									$user_arr[$rows['ID']]=$rows['USER_NAME'];	
								}
							}
						}
						else{
							$user_arr[$rows['ID']]=$rows['USER_NAME'];	
						}
							
				   }

				 	  $i=1; $entry_page_row_id="";
                        $hidden_entry_page_id=explode(",",$txt_entry_page_id);
                        foreach($user_arr as $id=>$name)
                        {
							$bgcolor=($i%1==0)? "#E9F3FF":"#FFFFFF";
						?>
                                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$id;?>" onClick="js_set_value(<?= $id;?>)">
                                    <td width="50" align="center"><?php echo "$i"; ?></td>
                                    <td id="username_<?= $id;?>"><?=$name; ?></td>
                                </tr>
                                <?
                            $i++;
                        }
                    ?>
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
        var txt_user_ids='<?=$txt_user_ids;?>'; 
		var txt_user_id_arr=txt_user_ids.split(',');
		for(var i=0;i<txt_user_id_arr.length;i++){
			js_set_value(txt_user_id_arr[i]);
		}
    </script>

    </html>
    <?
    exit();
}

if($action=="get_item_list")
{
    echo load_html_head_contents("User List","../../../", 1, 1, '','1','');
    extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_location_name=str_replace("'","",$cbo_location_name);
    ?>
    <script>

        $(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });

        var selected_id = new Array(); var selected_name = new Array();

        function check_all_data()
        {
			var isChecked = $('#check_all').is(":checked");

			$('#tbl_list_search tr').each(function(index, value) {
			 var idTag=	$('td:eq(1)', this).attr("id");
			 	if(idTag){
					var dataArr = idTag.split('_'); 
					var color = $( '#search'+dataArr[1] ).css( "background-color" );
					if(color!='rgb(255, 255, 0)' && isChecked==true){
						js_set_value( dataArr[1] );
					}
					else if(isChecked==false){
						js_set_value( dataArr[1] );
					}
				 }
			});

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

        function js_set_value( id )
        {
			var name =document.getElementById('username_'+id).innerText;
			
			toggle( document.getElementById( 'search' + id ), '#FFFFCC' );
            if( jQuery.inArray( id, selected_id ) == -1 ) {
                selected_id.push(id);
                selected_name.push( name);
            }
            else {
                for( var i = 0; i < selected_id.length; i++ ) {
                    if( selected_id[i] == id) break;
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

            $('#selected_user_ids').val(id);
            $('#selected_user_name').val(name);
            //parent.emailwindow.hide();
        }
    </script>

    </head>
    <body>
    <div align="center">
        <fieldset style="width:370px;margin-left:10px">
            
			<input type="hidden" name="selected_user_ids" id="selected_user_ids" value="">
            <input type="hidden" name="selected_user_name" id="selected_user_name" value="">
           
			
			<form name="searchbuyerfrm_1"  id="searchbuyerfrm_1" autocomplete="off">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
                    <thead>
                        <th width="50">SL</th>
                        <th>User Name</th>
                    </thead>
                </table>
                <div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
                    <?

				   $user_sql="SELECT ID,ITEM_NAME from  LIB_GARMENT_ITEM where status_active = 1";
				   $user_sql_res=sql_select( $user_sql);
				   $user_arr=array();
				  

				 	  $i=1; $entry_page_row_id="";
                        $hidden_entry_page_id=explode(",",$txt_entry_page_id);
                        foreach($user_sql_res as $v)
                        {
							$bgcolor=($i%1==0)? "#E9F3FF":"#FFFFFF";
						?>
                                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$v['ID'];?>" onClick="js_set_value(<?=$v['ID'];?>)">
                                    <td width="50" align="center"><?php echo "$i"; ?></td>
                                    <td id="username_<?= $v['ID'];?>"><?=$v['ITEM_NAME']; ?></td>
                                </tr>
                                <?
                            $i++;
                        }
                    ?>
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
        var txt_user_ids='<?=$txt_item_ids;?>'; 
		var txt_user_id_arr=txt_user_ids.split(',');
		for(var i=0;i<txt_user_id_arr.length;i++){
			js_set_value(txt_user_id_arr[i]);
		}
    </script>

    </html>
    <?
    exit();
}
?>