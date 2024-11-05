<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="cbo_root_menu_under")
{
	echo create_drop_down( "cbo_sub_main_menu_name", 155, "select m_menu_id,menu_name from main_menu where position='2' and root_menu ='$data' and status = 1 and status_active=1 and is_deleted=0 order by m_menu_id","m_menu_id,menu_name", 1, "-- Select Menu Name --", $selected, "load_drop_down( '../tools/requires/user_priviledge_controller', this.value, 'cbo_sub_root_menu_under', 'sub_subrootdiv' )" );
	exit();
}

if ($action=="cbo_sub_root_menu_under")
{
	$where_con = ($data>0)?" and sub_root_menu='$data'":"";
	echo create_drop_down( "cbo_sub_menu_name", 155, "select m_menu_id,menu_name from main_menu where position='3' $where_con and status=1 and status_active=1 and is_deleted=0 order by m_menu_id","m_menu_id,menu_name", 1, "-- Select Sub Menu --", $selected, "" );
	exit();
}

if ($action=="load_priv_list_view")
{
	$data=explode('_',$data);
  
	$sql= "SELECT a.menu_name,a.m_menu_id, b.show_priv,b.save_priv,b.edit_priv,b.delete_priv,b.approve_priv,b.id FROM main_menu a, user_priv_mst b WHERE b.user_id='$data[0]' AND a.m_module_id = '$data[1]' AND a.m_menu_id = b.main_menu_id and a.status=1 and a.status_active=1 and a.is_deleted=0 ORDER BY main_menu_id ASC";
	 
	$arr=array (1=>$form_permission_type,2=>$form_permission_type,3=>$form_permission_type,4=>$form_permission_type,5=>$form_permission_type);
	echo  create_list_view ( "list_view", "Menu Name,Visibility,Insert,Update ,Delete,Approve", "520,80,80,80,80,80","1050","320",1, $sql, "get_php_form_data", "id","'load_php_data_to_form'", 1, "0,show_priv,save_priv,edit_priv,delete_priv,approve_priv", $arr , "menu_name,show_priv,save_priv,edit_priv,delete_priv,approve_priv", "../tools/requires/user_priviledge_controller", '' ) ;	
	exit(); 
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//echo "10**";
	//01 Set Selected Module or menu not visible as a whole
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	$cbo_set_module_privt=str_replace("'","",$cbo_set_module_privt);
	$cbo_user_name=str_replace("'","",$cbo_user_name);

	//echo "10**$cbo_user_name";die;
	
	if( $cbo_set_module_privt == 2  ) 
	{
		$nameArray=sql_select( "SELECT module_id,user_id FROM user_priv_module WHERE user_id in(".$cbo_user_name.") AND module_id = $cbo_main_module" );
		foreach ($nameArray as $inf)
		{
			$rID=execute_query( "delete from user_priv_module where user_id =".$inf[csf("user_id")]." AND module_id = ".$inf[csf("module_id")]."", 1 );
			$rID1=execute_query( "delete from user_priv_mst where main_menu_id in ( select m_menu_id from main_menu where  m_module_id=".$inf[csf("module_id")]." ) and user_id=".$inf[csf("user_id")]."",1 );
		}
		//die;
		if($db_type==0)
		{
			if($rID && $rID1){
				mysql_query("COMMIT");  
				echo "0**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1)
			{
				oci_commit($con);   
				echo "0**".$rID;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		exit();
	}  
	//01 Set Selected Module or menu not visible as a whole
	
	//02 Set Selected Module or menu  visible as a whole
	else if( $cbo_set_module_privt == 1  ) 
	{

		$field_array="id,user_id,module_id,valid";
		$field_array1 = "id,user_id, main_menu_id, show_priv, delete_priv, save_priv, edit_priv, approve_priv, valid";
		$nameArray1=sql_select( "SELECT m_menu_id FROM main_menu WHERE m_module_id = $cbo_main_module and status=1" );
		$count=count($nameArray1);
			
		$cbo_user_arr=explode(",",$cbo_user_name);
		$data_array1="";
		foreach($cbo_user_arr as $single_user)
		{
			$id=return_next_id( "id", " user_priv_module", 1 ) ;
			
			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",".$single_user.",".$cbo_main_module.",1)";
			
			$i=0;
			if($data_array1!="") $data_array1.=",";
			$id3 = return_next_id( "id", "user_priv_mst",1 );
			foreach ($nameArray1 as $inf)
			{
				$id4=$id3+$i;
				$i++;
				if ($i!=$count) $data_array1 .="( ".$id4.",".$single_user.",".$inf[csf("m_menu_id")].", 1, 1, 1, 1, 1, 1 ),";
				else $data_array1 .="( ".$id4.",".$single_user.",".$inf[csf("m_menu_id")].", 1, 1, 1, 1, 1, 1 )";
				
			}
		}
		
		$rID=execute_query( "delete from user_priv_module where user_id in (".$cbo_user_name.") AND module_id = $cbo_main_module", 1 );
		$rID1=execute_query( "delete from user_priv_mst where main_menu_id in ( select m_menu_id from main_menu where  m_module_id=$cbo_main_module ) and user_id in (".$cbo_user_name.")",1 );
		
		$rID2=sql_insert("user_priv_module",$field_array,$data_array,1);
		$rID3=sql_insert("user_priv_mst",$field_array1,$data_array1,1);
		
		//echo  "10**insert into user_priv_mst($field_array1)values$data_array1".$rID.','.$rID1.','.$rID2.','.$rID3; die;
		//echo $data_array1; die;
		
		if($db_type==0)
		{
			if($rID && $rID1 && $rID2 && $rID3)
			{
				mysql_query("COMMIT");  
				echo "0**".$rID3;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID3;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1 && $rID2 && $rID3)
			{
				oci_commit($con);  
				echo "0**".$rID3;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$rID3;
			}
		}

		exit();
	}
	//02 Set Selected Module or menu  visible as a whole
	
	//03 Set Selected Module or menu  visible as a partial
	else if( $cbo_set_module_privt == 0  ) 
	{
		$cbo_main_menu_name=str_replace("'","",$cbo_main_menu_name);
		$cbo_sub_main_menu_name=str_replace("'","",$cbo_sub_main_menu_name);
		$cbo_sub_menu_name=str_replace("'","",$cbo_sub_menu_name);
		
		//echo $cbo_main_menu_name.'_'.$cbo_sub_main_menu_name.'_'.$cbo_sub_menu_name;die;
		$rID=true;
		$cbo_user_arr=explode(",",$cbo_user_name);
		$field_array="id,user_id,module_id,valid";
		$data_array1="";
		$field_array1 = "id,user_id, main_menu_id, show_priv, delete_priv, save_priv, edit_priv, approve_priv, valid";
		$id4 = return_next_id( "id", "user_priv_mst",1 );
		$id=return_next_id( "id", "user_priv_module", 1 ) ;
		
		if ($cbo_main_menu_name!=0 && $cbo_sub_main_menu_name!=0 && $cbo_sub_menu_name!=0)
		{
			
			foreach($cbo_user_arr as $single_user)
			{
				$nameArray=sql_select( "SELECT * FROM user_priv_module WHERE user_id=".$single_user." AND module_id = $cbo_main_module" );
				
				if (count($nameArray)<1)
				{
					if($data_array!="") $data_array.=",";
					$data_array.="(".$id.",".$single_user.",".$cbo_main_module.",1)";
					$id++;
				}
				if($data_array1!="") $data_array1.=",";
				$data_array1 .="( ".$id4.",".$single_user.",".$cbo_main_menu_name.",1,1,1,1,1,1),";
				$id4++;
		
				$data_array1 .="( ".$id4.",".$single_user.",".$cbo_sub_main_menu_name.",1,1,1,1,1,1),";
				$id4++;
				$data_array1 .="( ".$id4.",".$single_user.",".$cbo_sub_menu_name.", ".$cbo_visibility.",".$cbo_delete.", ".$cbo_insert.", ".$cbo_edit.",".$cbo_approve.", 1 )";
				$id4++;
			}
			
			$rID1=execute_query( "delete from user_priv_mst where main_menu_id in ( select m_menu_id from main_menu where m_module_id=$cbo_main_module ) and user_id in (".$cbo_user_name.") and main_menu_id in ($cbo_main_menu_name,$cbo_sub_main_menu_name,$cbo_sub_menu_name)",1 );
			if($data_array!="")
			{
				$rID=sql_insert("user_priv_module",$field_array,$data_array,1);
			}
			$rID2=sql_insert("user_priv_mst",$field_array1,$data_array1,1); 
			if($db_type==0)
			{
				if($rID2 )
				{
					mysql_query("COMMIT");  
					echo "0**".$rID2;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID2;
				}
			}
			
			else if($db_type==2 || $db_type==1 )
			{
				if($rID && $rID1 && $rID2 )
				{
					oci_commit($con);
					echo "0**".$rID2;
				}
				else
				{
					oci_rollback($con);
					echo "10**".$rID2;
				}
			}
			
			exit();
		}
		else if ($cbo_main_menu_name!=0 && $cbo_sub_main_menu_name!=0 && $cbo_sub_menu_name==0)
		{
			$nameArray2=sql_select( "SELECT * FROM main_menu WHERE sub_root_menu =$cbo_sub_main_menu_name AND m_module_id = $cbo_main_module and status=1");
			foreach($cbo_user_arr as $single_user)
			{
				$nameArray=sql_select( "SELECT * FROM user_priv_module WHERE user_id=".$single_user." AND module_id = $cbo_main_module" );
				if (count($nameArray)<1)
				{
					if($data_array!="") $data_array.=",";
					$data_array.="(".$id.",".$single_user.",".$cbo_main_module.",1)";
					$id++;
				}
				
				if($data_array1!="") $data_array1.=",";
				$data_array1 .="( ".$id4.",".$single_user.",".$cbo_main_menu_name.", 1,".$cbo_delete.", ".$cbo_insert.", ".$cbo_edit.",".$cbo_approve.", 1 )";
				$id4++;
				
				$count=count($nameArray2);
				$i=0;
				if ($count>0)
				{
					if($data_array1!="") $data_array1.=",";
					$data_array1 .="( ".$id4.",".$single_user.",".$cbo_sub_main_menu_name.", ".$cbo_visibility.",".$cbo_delete.", ".$cbo_insert.", ".$cbo_edit.",".$cbo_approve.", 1 )";
					$id4++;
					foreach ($nameArray2 as $inf)
					{
						
						if($data_array1!="") $data_array1.=",";
						$data_array1 .="( ".$id4.",".$single_user.",".$inf[csf("m_menu_id")].", ".$cbo_visibility.",".$cbo_delete.", ".$cbo_insert.", ".$cbo_edit.",".$cbo_approve.", 1 )";
						$id4++;
					} 
				}
				else
				{
					
					if($data_array1!="") $data_array1.=",";
					$data_array1 .="( ".$id4.",".$single_user.",".$cbo_sub_main_menu_name.", ".$cbo_visibility.",".$cbo_delete.", ".$cbo_insert.", ".$cbo_edit.",".$cbo_approve.", 1 )";
					$id4++;
				}
			}
		
			$sql1=execute_query( "delete from user_priv_mst where main_menu_id in ( select m_menu_id from main_menu where m_module_id=$cbo_main_module and sub_root_menu in ($cbo_sub_main_menu_name)) and user_id  in (".$cbo_user_name.")",1);
			
			$sql2=execute_query( "delete from user_priv_mst where main_menu_id in ( select m_menu_id from main_menu where  m_module_id=$cbo_main_module ) and user_id in (".$cbo_user_name.") and main_menu_id=$cbo_main_menu_name",1 );
			
			$sql3=execute_query( "delete from user_priv_mst where main_menu_id in( select m_menu_id from main_menu where m_module_id=$cbo_main_module) and user_id in (".$cbo_user_name.") and main_menu_id=$cbo_sub_main_menu_name", 1 );
			
			if($data_array!="")
			{
				$rID=sql_insert("user_priv_module",$field_array,$data_array,1);
			}
			$rID1=sql_insert("user_priv_mst",$field_array1,$data_array1,1); 
			//echo "10**insert into user_priv_module($field_array)values$data_array $sql1 && $sql2 && $sql3 && $rID && $rID1";die;
			//echo "10**$sql1 && $sql2 && $sql3 && $rID && $rID1";die;
			if($db_type==0)
			{
				if($sql1 && $sql2 && $sql3 && $rID && $rID1)
				{
					mysql_query("COMMIT");  
					echo "0**".$rID1;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID1;
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($sql1 && $sql2 && $sql3 && $rID && $rID1)
				{
					oci_commit($con); 
					echo "0**".$rID1;
				}
				else
				{
					oci_rollback($con);
					echo "10**".$rID1;
				}
			}
			
			exit();
		}
		else if ($cbo_main_menu_name!=0 && $cbo_sub_main_menu_name==0 && $cbo_sub_menu_name==0)
		{
			$nameArray1=sql_select( "SELECT * FROM main_menu WHERE m_module_id = $cbo_main_module and root_menu=$cbo_main_menu_name and status=1");
			foreach($cbo_user_arr as $single_user)
			{
				$nameArray=sql_select( "SELECT * FROM user_priv_module WHERE user_id in (".$single_user.") AND module_id = $cbo_main_module" );
				if (count($nameArray)<1)
				{
					if($data_array!="") $data_array.=",";
					$data_array.="(".$id.",".$single_user.",".$cbo_main_module.",1)";
					$id++;
				}
				
				$count=count($nameArray1);
				if ($count>0)
				{
					if($data_array1!="") $data_array1.=",";
					$data_array1 .="( ".$id4.",".$single_user.",".$cbo_main_menu_name.", ".$cbo_visibility.",".$cbo_delete.", ".$cbo_insert.", ".$cbo_edit.",".$cbo_approve.", 1 )";
					$id4++;
				
					foreach ($nameArray1 as $inf)
					{
						if($data_array1!="") $data_array1.=",";
						$data_array1 .="( ".$id4.",".$single_user.",".$inf[csf("m_menu_id")].", ".$cbo_visibility.",".$cbo_delete.", ".$cbo_insert.", ".$cbo_edit.",".$cbo_approve.", 1 )";
						$id4++;
					} 
				}
				else
				{
					if($data_array1!="") $data_array1.=",";
					$data_array1 .="( ".$id4.",".$single_user.",".$cbo_main_menu_name.", ".$cbo_visibility.",".$cbo_delete.", ".$cbo_insert.", ".$cbo_edit.",".$cbo_approve.", 1 )";
					$id4++;
				}
			}
			
			$sql1=execute_query( "delete from user_priv_mst where main_menu_id in( select m_menu_id from main_menu where m_module_id=$cbo_main_module and root_menu=$cbo_main_menu_name) and user_id in (".$cbo_user_name.")", 1 );
			
			$sql2=execute_query( "delete from user_priv_mst where main_menu_id in ( select m_menu_id from main_menu where m_module_id=$cbo_main_module) and user_id in (".$cbo_user_name.") and main_menu_id=$cbo_main_menu_name", 1 );
			
			if($data_array!="")
			{
				$rID=sql_insert("user_priv_module",$field_array,$data_array,1);
			}
			$rID1=sql_insert("user_priv_mst",$field_array1,$data_array1,1); 
			//echo "10**insert into user_priv_mst($field_array1)values$data_array1 $sql1 && $sql2 && $rID && $rID1";die;
			if($db_type==0)
			{
				if($rID && $sql1 && $sql2 && $rID1 )
				{
					mysql_query("COMMIT");  
					echo "0**".$rID1;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID1;
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID && $sql1 && $sql2 && $rID1 )
				{
					oci_commit($con);   
					echo "0**".$rID1;
				}
				else
				{
					oci_rollback($con);
					echo "10**".$rID1;
				}
			}
			exit();
		}
	}
	 //01 Set Selected Module or menu not visible as a whole	 
}
 
if ($action=="load_php_data_to_form")
{
	$sql_name_arr = "SELECT a.menu_name,a.root_menu,a.sub_root_menu,a.m_menu_id, b.show_priv,b.save_priv,b.edit_priv,b.delete_priv,b.approve_priv FROM main_menu a, user_priv_mst b WHERE b.id = '$data' AND a.m_menu_id = b.main_menu_id and a.status=1 and a.status_active=1 and a.is_deleted=0 ORDER BY main_menu_id ASC";
	//echo $sql_name_arr;die;
	$nameArray=sql_select($sql_name_arr);
	foreach ($nameArray as $inf)
	{
		echo "load_drop_down( 'requires/user_priviledge_controller', '".($inf[csf("root_menu")])."', 'cbo_root_menu_under', 'subrootdiv' );\n";
		echo "load_drop_down( 'requires/user_priviledge_controller', '".($inf[csf("sub_root_menu")])."', 'cbo_sub_root_menu_under', 'sub_subrootdiv' );\n";

		echo "document.getElementById('cbo_main_menu_name').value = '".trim(($inf[csf("root_menu")]))."';\n";    
		echo "document.getElementById('cbo_sub_main_menu_name').value  = '".($inf[csf("sub_root_menu")])."';\n"; 
		echo "document.getElementById('cbo_sub_menu_name').value  = '".($inf[csf("m_menu_id")])."';\n";  

		echo "document.getElementById('cbo_visibility').value  = '".($inf[csf("show_priv")])."';\n";
		echo "document.getElementById('cbo_insert').value  = '".($inf[csf("save_priv")])."';\n";  
		echo "document.getElementById('cbo_edit').value  = '".($inf[csf("edit_priv")])."';\n";  
		echo "document.getElementById('cbo_delete').value  = '".($inf[csf("delete_priv")])."';\n";  
		echo "document.getElementById('cbo_approve').value  = '".($inf[csf("approve_priv")])."';\n";  
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n";     
		//echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_user_creation',1);\n";  
	}
	exit();
}

if ($action=="load_priviledge_list")
{
    $data=explode("_", $data);
	?>
    <table width="100%" height="123" border="0" cellpadding="0" cellspacing="2">
		<tr>
			<td height="29" colspan="2">
				<b style="visibility:hidden">Set Admin Priviledge for this Module</b>&nbsp;
				<input type="checkbox" style="visibility:hidden" name="admin_privilege_all" id="admin_privilege_all" value="1">
			</td>
			<td colspan="4" align="left">
				<input type="button" name="load_data" id="load_data" class="formbutton" value="Load Data" tabindex="10" onclick="show_list_view(document.getElementById('cbo_user_name').value+'_'+document.getElementById('cbo_main_module').value,'load_priv_list_view','load_list_priv','../tools/requires/user_priviledge_controller','')" />
		    </td>
			<td colspan="3">Permission Level&nbsp;<?=create_drop_down( "cbo_set_module_privt", 162, $mod_permission_type,'', '', '',0 ); ?></td>
	    </tr>
		<tr><td colspan="9" height="10"></td></tr>
		<tr>
			<th rowspan="2" style="border:thin solid #000000;">Menu Name</th>
			<th rowspan="2" style="border:thin solid #000000;">Sub Main Menu</th>
			<th rowspan="2" style="border:thin solid #000000;">Sub Menu Name</th>
			<th colspan="5" style="border:thin solid #000000;">Permission</th>
			<th rowspan="2" style="border:thin solid #000000;">Action<input type="hidden" name="update_id" id="update_id" /></th>
		</tr>
		<tr>
			<th style="border:thin solid #000000;">Visibility</th>
			<th style="border:thin solid #000000;">Insert</th>
			<th style="border:thin solid #000000;">Edit</th>
			<th style="border:thin solid #000000;">Delete</th>
			<th style="border:thin solid #000000;">Approve</th>
		</tr>
		<tr>
			<td><?=create_drop_down( "cbo_main_menu_name", 260, "select m_menu_id,menu_name from main_menu where position='1' and m_module_id='".$data[1]."' and status = 1 and status_active=1 and is_deleted=0 order by m_menu_id","m_menu_id,menu_name", 1, "-- Select Menu --", $selected, "load_drop_down( '../tools/requires/user_priviledge_controller', this.value, 'cbo_root_menu_under', 'subrootdiv' )" ); ?></td>
			<td id="subrootdiv"><?=create_drop_down( "cbo_sub_main_menu_name", 155, $blank_array,'', 1, '--- Select ---',1 ); ?></td>
			<td id="sub_subrootdiv"><?=create_drop_down( "cbo_sub_menu_name", 155, $blank_array,'', 1, '--- Select ---',1 ); ?></td>
			<td><?=create_drop_down( "cbo_visibility", 85, $form_permission_type,'', '', '',1 ); ?></td>
			<td><?=create_drop_down( "cbo_insert", 85, $form_permission_type,'', '', '',1 ); ?></td>
			<td><?=create_drop_down( "cbo_edit", 85, $form_permission_type,'', '', '',1 ); ?></td>
			<td><?=create_drop_down( "cbo_delete", 85, $form_permission_type,'', '', '',1 ); ?></td>
			<td><?=create_drop_down( "cbo_approve", 85, $form_permission_type,'', '', '',1 ); ?></td>
			<td><input type="hidden" id="update_id" /><input type="button" name="save" id="save" tabindex="11" class="formbutton" onclick="fnc_set_priviledge(0);" value="Set Priviledge" /></td>
		</tr>
		<tr><td colspan="9" style="padding-top:10px;" id="load_list_priv"></td></tr>
	</table>
     <?
	 exit();
}

if($action=="save_copy_previledge")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//echo "10**";
	//01 Set Selected Module or menu not visible as a whole
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	$user_name=str_replace("'","",$cbo_user_name);
	$copyuser_name_arr=explode(',',str_replace("'","",$cbo_copyuser_name));
	$flag=1;
	foreach($copyuser_name_arr as $copyuser_name){
	
	//echo $copyuser_name;die;
	
		$idmodule=return_next_id( "id", " user_priv_module", 1);
		$fieldModule="id, user_id, module_id, user_only, valid, entry_date";
		$sqlPreModule = sql_select("select id, user_id, module_id, user_only, valid, entry_date from user_priv_module where user_id='$user_name' and valid=1");
		//echo "10**select id, user_id, module_id, user_only, valid, entry_date from user_priv_module where user_id='$user_name' and module_id=$cbo_main_module and valid=1";
		$dataArrModule=""; $module=0; $rowidstr="";
		foreach($sqlPreModule as $rowmd)
		{
			if($rowidstr=="") $rowidstr=$rowmd[csf('id')]; else $rowidstr.=','.$rowmd[csf('id')];
			
			if ($module!=0) $dataArrModule.=",";
			$dataArrModule.="(".$idmodule.",".$copyuser_name.",'".$rowmd[csf('module_id')]."','".$rowmd[csf('user_only')]."','".$rowmd[csf('valid')]."','".$rowmd[csf('entry_date')]."')";
			$idmodule=$idmodule+1;
			$module++;
		}
	 
		
		
		$idmst=return_next_id( "id", " user_priv_mst", 1);
		$fieldMst="id, user_id, main_menu_id, show_priv, delete_priv, save_priv, edit_priv, approve_priv, entry_date, user_only, last_updated_by, inserted_by, last_update_date, valid";
		
		$sqlPreMst=sql_select("select id, user_id, main_menu_id, show_priv, delete_priv, save_priv, edit_priv, approve_priv, entry_date, user_only, last_updated_by, inserted_by, last_update_date, valid from user_priv_mst where user_id='$user_name' and valid=1");
		//echo "10**select id, user_id, main_menu_id, show_priv, delete_priv, save_priv, edit_priv, approve_priv, entry_date, user_only, last_updated_by, inserted_by, last_update_date, valid from user_priv_mst where user_id='$user_name' and main_menu_id=".$cbo_main_module." and valid=1";
		$dataArrMst=""; $mst=0; 
		foreach($sqlPreMst as $rowmst)
		{
			if ($mst!=0) $dataArrMst.=",";
			$dataArrMst.="(".$idmst.",".$copyuser_name.",'".$rowmst[csf('main_menu_id')]."','".$rowmst[csf('show_priv')]."','".$rowmst[csf('delete_priv')]."','".$rowmst[csf('save_priv')]."','".$rowmst[csf('edit_priv')]."','".$rowmst[csf('approve_priv')]."','".$rowmst[csf('entry_date')]."','".$rowmst[csf('user_only')]."',0,".$_SESSION['logic_erp']['user_id'].",'','".$rowmst[csf('valid')]."')";
			$idmst=$idmst+1;
			$mst++;
		}
		
		
		if($flag==1){
			$rID=execute_query( "delete from user_priv_module where user_id ='".$copyuser_name."' ", 1 );
			if($rID==1) $flag=1; else $flag=0;
		}

		if($flag==1){
			$rID1=execute_query( "delete from user_priv_mst where user_id='".$copyuser_name."'",1 );
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		if($flag==1){
			$rID2=sql_insert("user_priv_mst",$fieldMst,$dataArrMst,1);
			if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		if($flag==1){
			$rID3=sql_insert("user_priv_module",$fieldModule,$dataArrModule,1);
			if($rID3==1 && $flag==1) $flag=1; else $flag=0;
		}
		//echo "10**".$rID.'='.$rID1.'='.$rID2.'='.$rID3.'='.$flag; die;
	}
	
	if($db_type==0)
	{
		if($flag==1)
		{
			mysql_query("COMMIT");  
			echo "0**".$rID3;
		}
		else
		{
			mysql_query("ROLLBACK"); 
			echo "10**".$rID3;
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($flag==1)
		{
			oci_commit($con);  
			echo "0**".$rID3;
		}
		else
		{
			oci_rollback($con);
			echo "10**".$rID3;
		}
	}
	disconnect($con);
	die;
}
?>