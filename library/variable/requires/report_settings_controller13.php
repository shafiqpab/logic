<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
 
include('../../../includes/common.php');

$permission = $_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

if ($_SESSION['logic_erp']["data_level_secured"]==1)
{
	if($_SESSION['logic_erp']["buyer_id"]!=0) $buyer_cond=" and id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
	if($_SESSION['logic_erp']["company_id"]!=0) $company_cond=" and id in (".$_SESSION['logic_erp']["company_id"].")"; else $company_cond="";
}
else
{
	$buyer_cond="";	$company_cond="";
}


if ($action=="load_drop_down_report_name")
{
	//echo "select m_menu_id,menu_name from main_menu where m_module_id='$data' and report_menu=1 and status=1 order by menu_name";  die;
	echo create_drop_down( "cbo_report_name", 182, "select m_menu_id,menu_name from main_menu where m_module_id='$data' and report_menu=1 and status=1 order by menu_name","m_menu_id,menu_name", 1, "--- Select Report ---", $selected, "" );
}

if ($action=="get_page_url")
{
	
	echo return_field_value( "f_location", "main_menu", "m_menu_id='$data'" );
	//echo "sds";
}

if ($action=="save_update_delete")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if($operation==0)  // Insert Here
	{ 
	
/*	 echo   "select template_id from lib_report_template where template_name=".trim($txt_template_name)." and status_active=1";die;
		$templateArray=sql_select( "select template_id from lib_report_template where template_name=".trim($txt_template_name)." and status_active=1" );
		
		if(count($templateArray)>0)
		{
			$template_id=$templateArray[0];
		}
		else
		{
			$template_id=return_next_id("template_id", "lib_report_template",1);
		}*/
		//echo $template_id;die;
		$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
		$cbo_bank_name=str_replace("'","",$cbo_bank_name);

		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$buyer_name=explode(",",$cbo_buyer_name);
		$bank_name=explode(",",$cbo_bank_name);
		
		if($cbo_buyer_name!="") $buyer_specific=1; else $buyer_specific=0;
		if($cbo_bank_name!="") $bank_specific=1; else $bank_specific=0;
		$data_array="";
		$field_array="id, template_id, template_name, module_id, buyer_id, bank_id, report_id, format_id, buyer_specific, bank_specific, inserted_by, insert_date, status_active, is_deleted";
		if($cbo_buyer_name!="")
		{
			for($i=0;$i<count($buyer_name); $i++)
			{
				if($id=="") $id=return_next_id( "id", "lib_report_template", 1 ); else $id=$id+1;
				if($i==0) $add_comma=""; else $add_comma=",";

				$data_array.="$add_comma(".$id.",".$txt_template_id.",".trim($txt_template_name).",".$cbo_module_name.",'".$buyer_name[$i]."','".$cbo_bank_name."',".$cbo_report_name.",".$cbo_template_name.",".$buyer_specific.",".$bank_specific.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";       
			}
			
		}
		else if($cbo_bank_name!="")
		{
			for($i=0;$i<count($bank_name); $i++)
			{
				if($id=="") $id=return_next_id( "id", "lib_report_template", 1 ); else $id=$id+1;
				if($i==0) $add_comma=""; else $add_comma=",";

				$data_array.="$add_comma(".$id.",".$template_id.",".trim($txt_template_name).",".$cbo_module_name.",'".$cbo_buyer_name."','".$bank_name[$i]."',".$cbo_report_name.",".$cbo_template_name.",".$buyer_specific.",".$bank_specific.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";     
			}
		}
		else
		{
			$id=return_next_id( "id", "lib_report_template", 1 );

			$data_array="(".$id.",".$template_id.",".trim($txt_template_name).",".$cbo_module_name.",'".$cbo_buyer_name."','".$cbo_bank_name."',".$cbo_report_name.",".$cbo_template_name.",".$buyer_specific.",".$bank_specific.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";     
		}
		//echo "insert into lib_report_template($field_array) values".$data_array;die;
		$rID=sql_insert("lib_report_template",$field_array,$data_array,1);

		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");
				echo "0**".$template_id."**".$id."";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**".$template_id."**".$id."";
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			    {
					oci_commit($con);   
					echo "0**".$template_id."**".$id."";
				}
				else{
					oci_rollback($con);
				    echo "5**".$template_id."**".$id."";
				}
		}
		disconnect($con);
		die;
	}
	else if($operation==1)   // Update Here
	{
       $con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="module_id*buyer_id*bank_id*report_id*format_id*buyer_specific*bank_specific*updated_by*update_date*status_active*is_deleted";
		
		if($cbo_buyer_name!="") $buyer_specific=1; else $buyer_specific=0;
		if($cbo_bank_name!="") $bank_specific=1; else $bank_specific=0;

		$data_array="".$cbo_module_name."*".$cbo_buyer_name."*".$cbo_bank_name."*".$cbo_report_name."*".$cbo_template_name."*".$buyer_specific."*".$bank_specific."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*0"; 
		
		$rID=sql_update("lib_report_template",$field_array,$data_array,"id",$update_id,1);
		//echo $rID;die;
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$txt_template_id)."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".str_replace("'","",$txt_template_id)."**1";
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			    {
					oci_commit($con);   
					echo "1**".str_replace("'","",$txt_template_id)."**0";
				}
				else{
					oci_rollback($con);
					echo "6**".str_replace("'","",$txt_template_id)."**1";
				}
		}
		disconnect($con);
		die;
	}
	else if($operation==2)//Delete here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		$rID=sql_delete("lib_report_template",$field_array,$data_array,"id","".$update_id."",1);
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_template_id)."**0";  
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "7**".str_replace("'","",$txt_template_id)."**1";
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con); 
				echo "2**".str_replace("'","",$txt_template_id)."**0";
			}
			else
			{ 
			    oci_rollback($con);
				echo "7**".str_replace("'","",$txt_template_id)."**1";
			}
		}
		disconnect($con);
		die;
	}
		
}

if($action=="report_settings")
{
	$sql="select id, template_id, template_name, module_id, buyer_id, bank_id, report_id, format_id, status_active from lib_report_template where template_id='$data' and is_deleted=0";
		
	$module_lib=return_library_array("select m_mod_id, main_module from main_module",'m_mod_id','main_module');
	$menu_lib=return_library_array( "select m_menu_id, menu_name from main_menu",'m_menu_id','menu_name');
	$tmpl=array();
	for($i=1; $i<11; $i++)
	{
		$tmpl[$i]="Template- ".$i;
	}
	$buyer_lib=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name'); 
	$bank_lib=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');  
	 
	$arr=array(1=>$module_lib,2=>$menu_lib,3=>$tmpl,4=>$buyer_lib,5=>$bank_lib,6=>$row_status);
				
	echo create_list_view("list_view", "Template Name,Module Name,Report Name,Report Format,Buyer Name,Bank Name,Status", "100,100,100,100,100,100,60","750","240",0, $sql , "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,module_id,report_id,format_id,buyer_id,bank_id,status_active", $arr , "template_name,module_id,report_id,format_id,buyer_id,bank_id,status_active", "requires/report_settings_controller",'','0,0,0,0,0,0,0') ;
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id, template_id, template_name, module_id, buyer_id, bank_id, report_id, format_id, status_active from lib_report_template where id='$data'");
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('txt_template_name').value = '".($inf[csf("template_name")])."';\n"; 
		echo "document.getElementById('cbo_module_name').value = '".($inf[csf("module_id")])."';\n"; 
		echo "load_drop_down( 'requires/report_settings_controller', document.getElementById('cbo_module_name').value, 'load_drop_down_report_name', 'report_td');\n";   
		echo "document.getElementById('cbo_report_name').value  = '".($inf[csf("report_id")])."';\n"; 
		echo "document.getElementById('cbo_template_name').value  = '".($inf[csf("format_id")])."';\n"; 
		echo "document.getElementById('cbo_buyer_name').value  = '".($inf[csf("buyer_id")])."';\n";  
		echo "document.getElementById('cbo_bank_name').value = '".($inf[csf("bank_id")])."';\n";    
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n"; 
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		echo "document.getElementById('txt_template_id').value  = '".($inf[csf("template_id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_report_settings',1);\n"; 
		
		if($inf[csf("buyer_id")]==0) $buyer_id=""; else $buyer_id=$inf[csf("buyer_id")];
		if($inf[csf("bank_id")]==0) $bank_id=""; else $bank_id=$inf[csf("bank_id")];
		echo "set_multiselect('cbo_buyer_name*cbo_bank_name','0*0','1','".$buyer_id."*".$bank_id."','0*0');\n";
		  
 
	}
}
if($action=="report_settings_popup")
{
	 echo load_html_head_contents("Report Settings Info","../../../", 1, 1, $unicode);
	?>
     
	<script>
		function js_set_value(val)
		{
			var val=val.split("_");
			$('#id_field').val(val[0]);
			$('#name_field').val(val[1]);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:530px;">
	<form name="searchscfrm"  id="searchscfrm">
		<fieldset style="width:100%;">
            <legend>Enter search words</legend>           
            	<table cellpadding="0" cellspacing="0" width="450" class="rpt_table">
                	<thead>
                    	<th>Template Name</th>
                        <th>
                        	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        	<input type="hidden" name="id_field" id="id_field" value="" />
                            <input type="hidden" name="name_field" id="name_field" value="" />
                        </th>
                    </thead>
                    <tr class="general">
                        <td> 
                             <input type="text" name="txt_template_name" id="txt_template_name" class="text_boxes" style="width:150px">
                        </td>
                         <td>
                 		  	<input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_template_name').value, 'create_popup_list_view', 'search_div', 'report_settings_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                         </td>
					</tr>
               </table>
               <table width="450" style="margin-top:5px" align="center">
					<tr>
                    	<td colspan="2" id="search_div" align="center"></td>
                    </tr>
                </table> 
            </fieldset>
		</form>
	</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit(); 
 
}
if($action=="create_popup_list_view")
{
	
	if($data=="0") $data="%%"; else $data="%".trim($data)."%";

	$sql="select template_id, template_name from lib_report_template where template_name like '$data' and is_deleted=0 ";
	//echo $sql;	
	echo create_list_view("list_view", "Template Id,Template Name", "130,250","450","240",0, $sql , "js_set_value", "template_id,template_name", "", 1, "0,0", $arr , "template_id,template_name", "",'','0,0') ;
	
}