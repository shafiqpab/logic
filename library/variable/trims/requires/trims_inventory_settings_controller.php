<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../../includes/common.php');
$userid = $_SESSION['logic_erp']['user_id'];
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$rcv_type_arr= array(1=>"Item Level",2=>"WO PI Details Level"); 
$ack_yes_no=array(1=>"No",2=>"Yes");

if($action=="load_drop_down_menu")
{
	echo create_drop_down( "cbo_page_neme", 250, "select m_menu_id,menu_name from main_menu where m_module_id=$data and root_menu>0 and status=1 order by menu_name",'m_menu_id,menu_name', 1, '---- Select ----', "0", "" );
	exit;
}

if ($action=="on_change_data")
{
	//echo $data; die;
	$explode_data = explode("_",$data);
	$type = $explode_data[0];
	$company_id = $explode_data[1];
	if($type == "27")
	{
		?>
		<fieldset style="width:100%; margin-bottom:10px">
			<legend>Ack.  Required For Item Transfer</legend>
			<table cellspacing="0" cellpadding="0" width="600" class="rpt_table">
				<thead>
					<th class="must_entry_caption">Item Category</th>
					<th class="must_entry_caption">Is Required</th>
				</thead>
				<tr align="center">
					<td>
						<?
						echo create_drop_down( "cbo_item_category", 180, $item_category_type_arr,'', 0, '---- Select ----',"0", "" ,"",101);
						?>
					</td>
					<td>
						<? 
						
						echo create_drop_down( "cbo_independent_con", 150, $ack_yes_no,'', 1, '---- Select ----',$select_status, "" ); 
						?>
					</td>
				</tr>
				<tr>
					<td colspan="2" valign="bottom" align="center" class="button_container">
						<input  type="hidden"name="update_id" id="update_id" value="<? //echo $update_id;?>">
						<? echo load_submit_buttons( $permission, "fnc_variable_settings_auto_transfer_rcv", 0,0 ,"reset_form('inventoryvariablesettings_1','','')",1); ?>
					</td>
				</tr>
			</table>
            <div id="list_view_con" style="margin-top:15px;">
				<?
				$company_name_arr=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
				$arr=array (0=>$company_name_arr,1=>$item_category_type_arr,2=>$ack_yes_no);
				// echo "select id,company_name,item_category_id,rack_balance from variable_settings_inventory where company_name='$company_id' and variable_list='$type' and status_active=1 and is_deleted=0 ";
				echo  create_list_view ( "list_view", "Company Name,Item Category,Is Required", "250,250","650","220",0, "select id, company_name, item_category_id, auto_transfer_rcv from variable_settings_inventory where company_name='$company_id' and variable_list='$type' and item_category_id=101 and status_active=1 and is_deleted=0 ","get_php_form_data","id","'load_data_for_ack'",1,"company_name,item_category_id,auto_transfer_rcv", $arr , "company_name,item_category_id,auto_transfer_rcv", "../../variable/trims/requires/trims_inventory_settings_controller",'setFilterGrid("list_view",-1);' );
				?>
			</div>
		</fieldset>
		<?
		exit();
	}
	
}


if ($action=="menu_popup")
{
	echo load_html_head_contents("Menu Search","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(menu_str)
		{
			var menu_str_ref=menu_str.split("**");
			document.getElementById('txt_menu_id').value=menu_str_ref[0];
			document.getElementById('txt_menu_name').value=menu_str_ref[1];
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="460" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
				<thead>
					<tr>
						<th width="50">SL</th>
						<th width="320">Menu Name</th>
						<th> Menu Id
                        <input type="hidden" id="txt_menu_name" name="txt_menu_name">
                        <input type="hidden" id="txt_menu_id" name="txt_menu_id">
                        </th>
					</tr>
				</thead>
                <tbody id="list_view">
                <?
				$i=1;
				ksort($entry_form);
				foreach($entry_form as $m_id=>$menu_name)
				{
					if ($i % 2 == 0) $bgcolor = "#E9F3FF";
					else $bgcolor = "#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $m_id. "**" . $menu_name;?>');">
                        <td align="center"><? echo $i;?></td>
                        <td><? echo  $menu_name; ?></td>
                        <td align="center"><? echo $m_id; ?></td>
					</tr>
                    <?
					$i++;
				}
				?>
                </tbody>
            </table>
        </form>
    </div>
	</body>
	<script>
		setFilterGrid('list_view',1);
	</script>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}



//-------------------------------------------------------------------
//-------------------------------------------------------------------
if($action=="append_load_details_container")
{
	$itemGroupArr = return_library_array("select id,item_name from lib_item_group","id","item_name");
	$i=$data+1;
 	?>
            <tr> 
                <td width="170" align="center">
                    <?
                        echo create_drop_down( "cbo_category".$i, 170, $item_category,"", 1, "-- Select --", 0, "",0 );
                    ?>  
                </td>
                <td width="170" align="center">
                    <?
                        echo create_drop_down( "cbo_item_group".$i, 170, $itemGroupArr,"", 1, "-- Select --", 0, "",0 );
                    ?> 
                </td>
                <td width="170" align="center">
                    <?
                        echo create_drop_down( "cbo_source".$i, 170, $source,"", 1, "-- Select --", 0, "",0 );
                    ?>
                </td>
                <td width="170" align="center"><input type="text" name="txt_standard<? echo $i; ?>" id="txt_standard<? echo $i; ?>" value="<? echo $rows[csf("standard")]; ?>" class="text_boxes_numeric" onFocus="add_variable_row(<? echo $i; ?>)"   style="width:160px"/></td>
            </tr>
           
     <?   
	 exit();                    
}


if($action=="append_load_material_over_receive_control")
{
	$itemGroupArr = return_library_array("select id,item_name from lib_item_group","id","item_name");
	$i=$data+1;
 	?>
        <tr> 
            <td width="35"><? echo $i;?></td>
            <td width="170" align="center">
                <?
                    echo create_drop_down( "cbo_category".$i, 170, $item_category,"", 1, "-- Select --", "", "fu_check_duplicate_item($i)",0 );
                ?>  
            </td>
            <td width="80" align="center"><input type="text" name="txt_over_rcv_percent<? echo $i; ?>" id="txt_over_rcv_percent<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" onKeyUp="fn_over_rcv_percent_check(<? echo $i; ?>,this.value)"/></td>
            <td width="60" align="center">
                <?
                    echo create_drop_down( "txt_over_rcv_payment".$i, 60, $yes_no,"", 1, "-- Select --",'', "fn_over_rcv_percent_check(".$i.",this.value,2);",0 );
                ?>
            </td>
        </tr>            
     <?   
	 exit();                    
}


//-------------------------------------------------------------------
//-------------------------------------------------------------------


if ($action=="on_change_data_list")
{
	$ex_data = explode("_",$data);
	$type = $ex_data[0];
	$company_id = $ex_data[1];
	if($type==20)
	{
		$company_name_arr=return_library_array( "select company_name,id from lib_company", "id", "company_name"  );
		$arr=array (0=>$company_name_arr,1=>$entry_form,2=>$yes_no,3=>$yes_no,4=>$yes_no,5=>$yes_no);
		echo  create_list_view ("list_view", "Company Name,Page Name,Independent Controll,Rate Optional,Rate Hide,Rate Edit", "150,250,90,90,90","850","220",0, "select id,company_name,menu_page_id,independent_controll,rate_edit,rate_optional,is_editable from  variable_settings_inventory where company_name='$company_id' and variable_list='$type' and status_active=1 and is_deleted=0 ","get_php_form_data","id","'load_php_data_to_form_inventory'",1,"company_name,menu_page_id,independent_controll,rate_optional,is_editable,rate_edit", $arr , "company_name,menu_page_id,independent_controll,rate_optional,is_editable,rate_edit", "../../variable/trims/requires/trims_inventory_settings_controller",'setFilterGrid("list_view",-1);' );
	}
	/*else if($type==22)
	{
		
		$company_name_arr=return_library_array( "select company_name,id from lib_company", "id", "company_name"  );
		$menu_name_arr=return_library_array( "select m_menu_id,menu_name from main_menu where m_module_id=6 and root_menu>0", "m_menu_id", "menu_name"  );
		$arr=array (0=>$company_name_arr,1=>$menu_name_arr,2=>$yes_no,3=>$yes_no);
		echo  create_list_view ( "list_view", "Company Name,Page Name,Independent Controll", "180,200,100,150","570","220",0, "select id,company_name,menu_page_id,independent_controll from  variable_settings_inventory where company_name='$company_id' and variable_list='$type' and status_active=1 and is_deleted=0 ","get_php_form_data","id","'load_php_data_to_independent'",1,"company_name,menu_page_id,independent_controll", $arr , "company_name,menu_page_id,independent_controll", "../../variable/trims/requires/trims_inventory_settings_controller",'setFilterGrid("list_view",-1);' );
	}*/
    else if($type==10)
    {
      $company_name_arr=return_library_array( "select company_name,id from lib_company", "id", "company_name"  );
        $arr=array (0=>$company_name_arr,1=>$item_category,2=>$yes_no,3=>$yes_no);

        echo  create_list_view ( "list_view", "Company,Item Category,Rate Optional, Is Editable","180,180,100,100","630","220",0,"select id,company_name,item_category_id,rate_optional,is_editable from  variable_settings_inventory where company_name='$company_id' and variable_list='$type' and status_active=1 and is_deleted=0 ","get_php_form_data","id","'load_php_data_to_item_rate_mrr'",1,"company_name,item_category_id,rate_optional,is_editable",$arr,"company_name,item_category_id,rate_optional,is_editable","../../variable/trims/requires/trims_inventory_settings_controller",'setFilterGrid("list_view",1);' );
    }
	else
	{
		if($type==31) $item_type_arr=$rcv_type_arr; else $item_type_arr=$yes_no;
		$company_name_arr=return_library_array( "select company_name,id from lib_company", "id", "company_name"  );
		$arr=array (0=>$company_name_arr,1=>$item_category,2=>$item_type_arr);
		echo  create_list_view ( "list_view", "Company Name,Item Category,Status", "150,150,150","470","220",0, "select id,company_name,item_category_id,user_given_code_status from  variable_settings_inventory where company_name='$company_id' and variable_list='$type' and status_active=1 and is_deleted=0 ","get_php_form_data","id","'load_php_data_to_form'",1,"company_name,item_category_id,user_given_code_status", $arr , "company_name,item_category_id,user_given_code_status", "../../variable/trims/requires/trims_inventory_settings_controller",'setFilterGrid("list_view",-1);' );
	}
		
		  
}

if ($action=="load_php_data_to_form")
{
	  $nameArray=sql_select( "select id,company_name,variable_list,item_category_id,user_given_code_status from variable_settings_inventory where id='$data'" );
	  foreach ($nameArray as $inf)
	  {
		  echo "document.getElementById('cbo_company_name').value = '".($inf[csf("company_name")])."';\n"; 
		  echo "document.getElementById('cbo_variable_list').value = '".($inf[csf("variable_list")])."';\n";
		  echo "document.getElementById('cbo_item_category').value = '".($inf[csf("item_category_id")])."';\n"; 
		  echo "document.getElementById('cbo_item_status').value = '".($inf[csf("user_given_code_status")])."';\n";
		  echo "document.getElementById('update_id').value = '".($inf[csf("id")])."';\n";    
		  echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_variable_settings_inventory',1);\n";  
	  }
}

if ($action=="load_php_data_to_form_inventory")
{
	  $nameArray=sql_select( "select id,company_name,variable_list,module_id,menu_page_id,independent_controll,rate_optional, is_editable,rate_edit from variable_settings_inventory where id='$data'" );
	  foreach ($nameArray as $inf)
	  {
		  echo "document.getElementById('cbo_company_name').value = '".($inf[csf("company_name")])."';\n"; 
		  echo "document.getElementById('cbo_variable_list').value = '".($inf[csf("variable_list")])."';\n";
		  echo "document.getElementById('txt_menu_id').value = '".($inf[csf("menu_page_id")])."';\n";
		  echo "document.getElementById('txt_menu_name').value = '".($entry_form[$inf[csf("menu_page_id")]])."';\n"; 
		  echo "document.getElementById('cbo_independent_con').value = '".($inf[csf("independent_controll")])."';\n";
		  echo "document.getElementById('cbo_rate_opption').value = '".($inf[csf("rate_optional")])."';\n";
		  echo "document.getElementById('cbo_rate_hide').value = '".($inf[csf("is_editable")])."';\n";
		  echo "document.getElementById('cbo_rate_con').value = '".($inf[csf("rate_edit")])."';\n";
		  echo "document.getElementById('update_id').value = '".($inf[csf("id")])."';\n";    
		  echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_variable_settings_inventory',1);\n";  
	  }
}

//load_php_data_to_independent
if ($action=="load_php_data_to_independent")
{
	  $nameArray=sql_select( "select id,company_name,variable_list,module_id,menu_page_id,independent_controll,rate_edit from variable_settings_inventory where id='$data'" );
	  foreach ($nameArray as $inf)
	  {
		  echo "document.getElementById('cbo_company_name').value = '".($inf[csf("company_name")])."';\n"; 
		  echo "document.getElementById('cbo_variable_list').value = '".($inf[csf("variable_list")])."';\n";
		  echo "document.getElementById('cbo_module').value = '".($inf[csf("module_id")])."';\n";
		  echo "load_drop_down( 'requires/trims_inventory_settings_controller',".$inf[csf("module_id")].", 'load_drop_down_menu', 'menu_td' );\n";
		  echo "document.getElementById('cbo_page_neme').value = '".($inf[csf("menu_page_id")])."';\n"; 
		  echo "document.getElementById('cbo_independent_con').value = '".($inf[csf("independent_controll")])."';\n";
		
		  echo "document.getElementById('update_id').value = '".($inf[csf("id")])."';\n";    
		  echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_variable_settings_inventory',1);\n";  
	  }
}

if ($action=="load_php_data_to_item_rate_mrr")
{
    $nameArray=sql_select( "select id,company_name,variable_list,item_category_id,rate_optional,is_editable from variable_settings_inventory where id='$data'" );
    foreach ($nameArray as $inf)
    {
      echo "document.getElementById('cbo_company_name').value = '".($inf[csf("company_name")])."';\n"; 
      echo "document.getElementById('cbo_variable_list').value = '".($inf[csf("variable_list")])."';\n";
      echo "document.getElementById('cbo_category').value = '".($inf[csf("item_category_id")])."';\n";
      echo "document.getElementById('cbo_rate_optional').value = '".($inf[csf("rate_optional")])."';\n"; 
      echo "document.getElementById('cbo_editable').value = '".($inf[csf("is_editable")])."';\n";
      echo "document.getElementById('update_id').value = '".($inf[csf("id")])."';\n";    
      echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_variable_settings_inventory',1);\n";  
    }
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$variable_list=str_replace("'","",$cbo_variable_list);
	if($variable_list==20)
	{
		if ($operation==0)  // Insert Here==============================================================================
		{
			
			if(is_duplicate_field( "menu_page_id", " variable_settings_inventory", "menu_page_id=$cbo_page_neme and company_name=$cbo_company_name and variable_list=$cbo_variable_list and is_deleted=0" ) == 1)
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
				  $id=return_next_id( "id", "variable_settings_inventory", 1 );
				  
				  $field_array="id,company_name,variable_list,menu_page_id,independent_controll,rate_optional, is_editable,rate_edit,inserted_by,insert_date,status_active,is_deleted";
				  $data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$txt_menu_id.",".$cbo_independent_con.",".$cbo_rate_opption.",".$cbo_rate_hide.",".$cbo_rate_con.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,'0')";
				  //print_r($data_array);die;
				  $rID=sql_insert("variable_settings_inventory",$field_array,$data_array,1);
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
		  else if ($operation==1)   // Update Here=========================================================
		  {
			  
			 //  echo "select item_category_id from  variable_settings_inventory where item_category_id=$cbo_item_category and company_name=$cbo_company_name and variable_list=$cbo_variable_list and is_deleted=0";die;
			 if(is_duplicate_field( "menu_page_id", " variable_settings_inventory", "menu_page_id=$cbo_page_neme and company_name=$cbo_company_name and variable_list=$cbo_variable_list and id<>$update_id and is_deleted=0" ) == 1)
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
				  
				  $field_array="company_name*variable_list*menu_page_id*independent_controll*rate_optional*is_editable*rate_edit*updated_by*update_date*status_active*is_deleted";
				  $data_array="".$cbo_company_name."*".$cbo_variable_list."*".$txt_menu_id."*".$cbo_independent_con."*".$cbo_rate_opption."*".$cbo_rate_hide."*".$cbo_rate_con."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".'1'."*'0'";
				 // print_r($data_array);die;
				  $rID=sql_update("variable_settings_inventory",$field_array,$data_array,"id","".$update_id."",1);
				  //print_r($rID);die;
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
		  else if ($operation==2)   // Delete Here===================================================
		  {
			  $con = connect();
			  if($db_type==0)
			  {
				  mysql_query("BEGIN");
			  }
			  
			  $field_array="updated_by*update_date*status_active*is_deleted";
			  $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
			  
			  $rID=sql_delete("variable_settings_inventory",$field_array,$data_array,"id","".$update_id."",1);
			  
			  if($db_type==0)
			  {
				  if($rID ){
					  mysql_query("COMMIT");  
					  echo "2**".$rID;
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
	/*else if($variable_list==22)
	{
		if ($operation==0)  // Insert Here==============================================================================
		{
			
			if(is_duplicate_field( "menu_page_id", " variable_settings_inventory", "menu_page_id=$cbo_page_neme and company_name=$cbo_company_name and variable_list=$cbo_variable_list and is_deleted=0" ) == 1)
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
				  $id=return_next_id( "id", "variable_settings_inventory", 1 );
				  
				  $field_array="id,company_name,variable_list,module_id,menu_page_id,independent_controll,inserted_by,insert_date,status_active,is_deleted";
				  $data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_module.",".$cbo_page_neme.",".$cbo_independent_con.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,'0')";
				  //print_r($data_array);die;
				  $rID=sql_insert("variable_settings_inventory",$field_array,$data_array,1);
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
		  else if ($operation==1)   // Update Here=========================================================
		  {
			  
			 //  echo "select item_category_id from  variable_settings_inventory where item_category_id=$cbo_item_category and company_name=$cbo_company_name and variable_list=$cbo_variable_list and is_deleted=0";die;
			 if(is_duplicate_field( "menu_page_id", " variable_settings_inventory", "menu_page_id=$cbo_page_neme and company_name=$cbo_company_name and variable_list=$cbo_variable_list and id<>$update_id and is_deleted=0" ) == 1)
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
				  
				  $field_array="company_name*variable_list*module_id*menu_page_id*independent_controll*updated_by*update_date*status_active*is_deleted";
				  $data_array="".$cbo_company_name."*".$cbo_variable_list."*".$cbo_module."*".$cbo_page_neme."*".$cbo_independent_con."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".'1'."*'0'";
				 // print_r($data_array);die;
				  $rID=sql_update("variable_settings_inventory",$field_array,$data_array,"id","".$update_id."",1);
				  //print_r($rID);die;
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
		  else if ($operation==2)   // Delete Here===================================================
		  {
			  $con = connect();
			  if($db_type==0)
			  {
				  mysql_query("BEGIN");
			  }
			  
			  $field_array="updated_by*update_date*status_active*is_deleted";
			  $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
			  
			  $rID=sql_delete("variable_settings_inventory",$field_array,$data_array,"id","".$update_id."",1);
			  
			  if($db_type==0)
			  {
				  if($rID ){
					  mysql_query("COMMIT");  
					  echo "2**".$rID;
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
	}*/

	else if($variable_list==10)
	{
	    if ($operation==0)  // Insert Here==============================================================================
	    {
	      if(is_duplicate_field( "item_category_id", " variable_settings_inventory", "item_category_id=$cbo_category and company_name=$cbo_company_name and variable_list=$cbo_variable_list and is_deleted=0" ) == 1)
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
	          $id=return_next_id( "id", "variable_settings_inventory", 1 );
	          
	          $field_array="id,company_name,variable_list,item_category_id,rate_optional,is_editable,inserted_by,insert_date,status_active,is_deleted";
	          $data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_category.",".$cbo_rate_optional.",".$cbo_editable.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,'0')";
	          //print_r($data_array);die;
	          $rID=sql_insert("variable_settings_inventory",$field_array,$data_array,1);
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
	      else if ($operation==1)   // Update Here=========================================================
	      {
	        
	       //  echo "select item_category_id from  variable_settings_inventory where item_category_id=$cbo_item_category and company_name=$cbo_company_name and variable_list=$cbo_variable_list and is_deleted=0";die;
	       if(is_duplicate_field( "item_category_id", " variable_settings_inventory", "item_category_id=$cbo_category and company_name=$cbo_company_name and variable_list=$cbo_variable_list and id<>$update_id and is_deleted=0" ) == 1)
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
	          
	          $field_array="company_name*variable_list*item_category_id*rate_optional*is_editable*updated_by*update_date*status_active*is_deleted";
	          $data_array="".$cbo_company_name."*".$cbo_variable_list."*".$cbo_category."*".$cbo_rate_optional."*".$cbo_editable."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".'1'."*'0'";
	         // print_r($data_array);die;
	          $rID=sql_update("variable_settings_inventory",$field_array,$data_array,"id","".$update_id."",1);
	          //print_r($rID);die;
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
	      else if ($operation==2)   // Delete Here===================================================
	      {
	        $con = connect();
	        if($db_type==0)
	        {
	          mysql_query("BEGIN");
	        }
	        
	        $field_array="updated_by*update_date*status_active*is_deleted";
	        $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
	        
	        $rID=sql_delete("variable_settings_inventory",$field_array,$data_array,"id","".$update_id."",1);
	        
	        if($db_type==0)
	        {
	          if($rID ){
	            mysql_query("COMMIT");  
	            echo "2**".$rID;
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
       
	/*Yarn issue basis starts here*/

	else if($variable_list==28)
    {
            if($operation == 0)
            {
                 //item_category_id=$cbo_item_category and    ## Category not use for 24,26  in V. setting
          if(is_duplicate_field( "id", " variable_settings_inventory", "company_name=$cbo_company_name and variable_list=$cbo_variable_list and is_deleted=0" ) == 1)
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
                          $id=return_next_id( "id", "variable_settings_inventory", 1 );

                          $field_array="id,item_category_id,company_name,variable_list,yarn_issue_basis,inserted_by,insert_date,status_active,is_deleted";
                          $data_array="(".$id.",1,".$cbo_company_name.",".$cbo_variable_list.",".$cbo_independent_con.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,'0')";
                          //print_r($data_array);die;
                          $rID=sql_insert("variable_settings_inventory",$field_array,$data_array,1);
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
            else if($operation == 1)
            {
                 $con = connect();
                if($db_type==0)
                {
                        mysql_query("BEGIN");
                }

                $field_array="company_name*variable_list*yarn_issue_basis*updated_by*update_date*status_active*is_deleted";
                $data_array="".$cbo_company_name."*".$cbo_variable_list."*".$cbo_independent_con."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".'1'."*'0'";
               // print_r($data_array);die;
                $rID=sql_update("variable_settings_inventory",$field_array,$data_array,"id","".$update_id."",1);
                //print_r($rID);die;
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

	else if($variable_list==24 || $variable_list==26)
    {
            if($operation == 0)
            {
              	 //item_category_id=$cbo_item_category and    ## Category not use for 24,26  in V. setting
			    if(is_duplicate_field( "id", " variable_settings_inventory", "company_name=$cbo_company_name and variable_list=$cbo_variable_list and is_deleted=0" ) == 1)
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
                          $id=return_next_id( "id", "variable_settings_inventory", 1 );

                          $field_array="id,company_name,variable_list,allocation,inserted_by,insert_date,status_active,is_deleted";
                          $data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_independent_con.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,'0')";
                          //print_r($data_array);die;
                          $rID=sql_insert("variable_settings_inventory",$field_array,$data_array,1);
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
            else if($operation == 1)
            {
                 $con = connect();
                if($db_type==0)
                {
                        mysql_query("BEGIN");
                }

                $field_array="company_name*variable_list*allocation*updated_by*update_date*status_active*is_deleted";
                $data_array="".$cbo_company_name."*".$cbo_variable_list."*".$cbo_independent_con."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".'1'."*'0'";
               // print_r($data_array);die;
                $rID=sql_update("variable_settings_inventory",$field_array,$data_array,"id","".$update_id."",1);
                //print_r($rID);die;
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
		
	else if($variable_list==25)
    {
            if($operation == 0)
            {
                if(is_duplicate_field( "id", " variable_settings_inventory", "item_category_id=$cbo_item_category and company_name=$cbo_company_name and variable_list=$cbo_variable_list and is_deleted=0" ) == 1)
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
                          $id=return_next_id( "id", "variable_settings_inventory", 1 );

                          $field_array="id,company_name,variable_list,during_issue,inserted_by,insert_date,status_active,is_deleted";
                          $data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_during_issue.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,'0')";
                          //print_r($data_array);die;
                          $rID=sql_insert("variable_settings_inventory",$field_array,$data_array,1);
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
            else if($operation == 1)
            {
                 $con = connect();
                if($db_type==0)
                {
                        mysql_query("BEGIN");
                }

                $field_array="company_name*variable_list*during_issue*updated_by*update_date*status_active*is_deleted";
                $data_array="".$cbo_company_name."*".$cbo_variable_list."*".$cbo_during_issue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".'1'."*'0'";
               // print_r($data_array);die;
                $rID=sql_update("variable_settings_inventory",$field_array,$data_array,"id","".$update_id."",1);
                //print_r($rID);die;
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
	
	else
	{
		if ($operation==0)  // Insert Here==============================================================================
		{
			
			if(is_duplicate_field( "item_category_id", " variable_settings_inventory", "item_category_id=$cbo_item_category and company_name=$cbo_company_name and variable_list=$cbo_variable_list and is_deleted=0" ) == 1)
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
				  $id=return_next_id( "id", "variable_settings_inventory", 1 );
				  
				  $field_array="id,company_name,variable_list,item_category_id,user_given_code_status,inserted_by,insert_date,status_active,is_deleted";
				  $data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_item_category.",".$cbo_item_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,'0')";
				  //print_r($data_array);die;
				  $rID=sql_insert("variable_settings_inventory",$field_array,$data_array,1);
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
		else if ($operation==1)   // Update Here=========================================================
		{
			  
			//  echo "select item_category_id from  variable_settings_inventory where item_category_id=$cbo_item_category and company_name=$cbo_company_name and variable_list=$cbo_variable_list and is_deleted=0";die;
			if(is_duplicate_field( "item_category_id", " variable_settings_inventory", "item_category_id=$cbo_item_category and company_name=$cbo_company_name   and variable_list=$cbo_variable_list and is_deleted=0 and id!=$update_id" ) == 1)
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

				$field_array="company_name*variable_list*item_category_id*user_given_code_status*updated_by*update_date*status_active*is_deleted";
				$data_array="".$cbo_company_name."*".$cbo_variable_list."*".$cbo_item_category."*".$cbo_item_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".'1'."*'0'";
				// print_r($data_array);die;
				$rID=sql_update("variable_settings_inventory",$field_array,$data_array,"id","".$update_id."",1);
				//print_r($rID);die;
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
		else if ($operation==2)   // Delete Here===================================================
		{
			$con = connect();
			if($db_type==0)
			{
			  mysql_query("BEGIN");
			}
				  
			$field_array="updated_by*update_date*status_active*is_deleted";
			$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";

			$rID=sql_delete("variable_settings_inventory",$field_array,$data_array,"id","".$update_id."",1);

			if($db_type==0)
			{
			  if($rID ){
				  mysql_query("COMMIT");  
				  echo "2**".$rID;
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
				else
				{
					oci_rollback($con);
					echo "10**".$rID;
				}					
			}
			disconnect($con);
			die;
		}
	}
}



//---------------------------------------------ile standard save here-----------------------------------// 
if ($action=="save_update_delete_ile")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==0)  // Insert Here==============================================================================
	{
 	
		$con = connect();
		if($db_type==0)
		{
		  mysql_query("BEGIN");
		}
		
		$id=return_next_id( "id", "variable_inv_ile_standard", 1 );		
		$field_array="id,company_name, variable_list, category, item_group, source, standard,inserted_by,insert_date";
		$rows = str_replace("'","",$row);
		$data_array="";
		for($i=1;$i<=$rows;$i++)
		{ 
			
			$cbo_category 	= 'cbo_category'.$i;
			$cbo_item_group = 'cbo_item_group'.$i;
			$cbo_source 	= 'cbo_source'.$i;
			$txt_standard 	= 'txt_standard'.$i;
			if( $$cbo_category!=0 || $$cbo_item_group!=0 || $$cbo_source!=0 ||  $$txt_standard!="" )
			{ 
				if(trim($data_array)!="") $data_array .= ",";
				$data_array.="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$$cbo_category.",".$$cbo_item_group.",".$$cbo_source.",".$$txt_standard.",".$userid.",'".$pc_date_time."')";
				$id=$id+1;
			}
		}
		
		//echo $data_array."#####".$field_array;die;
		$rID=sql_insert("variable_inv_ile_standard",$field_array,$data_array,1);
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
 	else if ($operation==1)   // Update Here=========================================================
	{
		 
		$con = connect();
		if($db_type==0)
		{
		  mysql_query("BEGIN");
		}
		
		// delete here--------------- 
		$deleteSQL = execute_query("DELETE FROM variable_inv_ile_standard WHERE company_name=$cbo_company_name and variable_list=8",0);
		
		$id=return_next_id( "id", "variable_inv_ile_standard", 1 );		
		$field_array="id,company_name, variable_list, category, item_group, source, standard,inserted_by,insert_date";
		$rows = str_replace("'","",$row);
		$data_array="";
		for($i=1;$i<=$rows;$i++)
		{ 
			
			$cbo_category 	= 'cbo_category'.$i;
			$cbo_item_group = 'cbo_item_group'.$i;
			$cbo_source 	= 'cbo_source'.$i;
			$txt_standard 	= 'txt_standard'.$i;
			if( $$cbo_category!=0 || $$cbo_item_group!=0 || $$cbo_source!=0 ||  $$txt_standard!="" )
			{ 
				if(trim($data_array)!="") $data_array .= ",";
				$data_array.="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$$cbo_category.",".$$cbo_item_group.",".$$cbo_source.",".$$txt_standard.",".$userid.",'".$pc_date_time."')";
				$id=$id+1;
			}
		}
		
		//print_r($data_array);die;
		$rID=sql_insert("variable_inv_ile_standard",$field_array,$data_array,1);
		if($db_type==0)
		{
		  if( $rID ){
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
	 else if ($operation==2)   // Delete Here===================================================
	 {
		  //no operation
	 }
	  
}

if ($action=="save_update_delete_auto_transfer_rcv")
{
  $process = array( &$_POST );
  extract(check_magic_quote_gpc( $process )); 
  if ($operation==0)  // Insert Here==============================================================================
  {
  
    $con = connect();
    if($db_type==0)
    {
      mysql_query("BEGIN");
    }
	
    $check = is_duplicate_field( "id", "variable_settings_inventory", "status_active=1 and company_name=$cbo_company_name and variable_list=$cbo_variable_list and item_category_id=$cbo_item_category" );
	if($check==1)
	{
		echo"11** Duplicate Data Not Allow";
		disconnect($con);
		die;
	}
	
    $id=return_next_id( "id", "variable_settings_inventory", 1 );   
    $field_array="id,company_name,variable_list,item_category_id,auto_transfer_rcv,inserted_by,insert_date";
   
    $data_array.="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_item_category.",".$cbo_independent_con.",".$userid.",'".$pc_date_time."')";
    $id=$id+1;
     
    
    //echo $data_array."#####".$field_array;die;
    $rID=sql_insert("variable_settings_inventory",$field_array,$data_array,1);
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
  else if ($operation==1)   // Update Here=========================================================
  {
     
    $con = connect();
    if($db_type==0)
    {
      mysql_query("BEGIN");
    }
	
    $check = is_duplicate_field( "id", "variable_settings_inventory", "status_active=1 and company_name=$cbo_company_name and variable_list=$cbo_variable_list and item_category_id=$cbo_item_category and id <> $update_id" );
	if($check==1)
	{
		echo"11** Duplicate Data Not Allow";
		disconnect($con);
		die;
	}
    // delete here--------------- 
    $deleteSQL = execute_query("DELETE FROM variable_settings_inventory WHERE company_name=$cbo_company_name and item_category_id=$cbo_item_category and variable_list=$cbo_variable_list",0);
    
    $id=return_next_id( "id", "variable_settings_inventory", 1 );   
    $field_array="id,company_name,item_category_id,variable_list,auto_transfer_rcv,inserted_by,insert_date";
   
    $data_array.="(".$id.",".$cbo_company_name.",".$cbo_item_category.",".$cbo_variable_list.",".$cbo_independent_con.",".$userid.",'".$pc_date_time."')";
    $id=$id+1;
    //echo "10**".$data_array."#####".$field_array;die;
    $rID=sql_insert("variable_settings_inventory",$field_array,$data_array,1);
    if($db_type==0)
    {
      if( $rID ){
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
   else if ($operation==2)   // Delete Here===================================================
   {
      //no operation
   }
    
}
 
 
if ($action=="save_update_delete_material_over_receive_control")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==0)  // Insert Here==============================================================================
	{
 	
		$con = connect();
		if($db_type==0)
		{
		  mysql_query("BEGIN");
		}
		
		$id=return_next_id( "id", "variable_inv_ile_standard", 1 );		
		$field_array="id,company_name, variable_list, category,over_rcv_percent,over_rcv_payment,inserted_by,insert_date";
		$rows = str_replace("'","",$row);
		$data_array="";
		for($i=1;$i<=$rows;$i++)
		{ 
			$cbo_category = 'cbo_category'.$i;
			$txt_over_rcv_percent = 'txt_over_rcv_percent'.$i;
			$txt_over_rcv_payment = 'txt_over_rcv_payment'.$i;
			
			if( $$cbo_category!=0 || $$txt_over_rcv_percent!=0 ||  $$txt_over_rcv_payment!="" )
			{ 
				if(trim($data_array)!="") $data_array .= ",";
				$data_array.="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$$cbo_category.",".$$txt_over_rcv_percent.",".$$txt_over_rcv_payment.",".$userid.",'".$pc_date_time."')";
				$id=$id+1;
			}
		}
		
		//echo $data_array."#####".$field_array;die;
		$rID=sql_insert("variable_inv_ile_standard",$field_array,$data_array,1);
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
 	else if ($operation==1)   // Update Here=========================================================
	{
		 
		$con = connect();
		if($db_type==0)
		{
		  mysql_query("BEGIN");
		}
		
		// delete here--------------- 
		$deleteSQL = execute_query("DELETE FROM variable_inv_ile_standard WHERE company_name=$cbo_company_name and variable_list=23",0);
		
		$id=return_next_id( "id", "variable_inv_ile_standard", 1 );		
		$field_array="id,company_name, variable_list, category,over_rcv_percent,over_rcv_payment,inserted_by,insert_date";
		$rows = str_replace("'","",$row);
		$data_array="";
		for($i=1;$i<=$rows;$i++)
		{ 
			
			$cbo_category 	= 'cbo_category'.$i;
			$txt_over_rcv_percent = 'txt_over_rcv_percent'.$i;
			$txt_over_rcv_payment = 'txt_over_rcv_payment'.$i;
			if( $$cbo_category!=0 ||  $$txt_over_rcv_percent!=0 ||  $$txt_over_rcv_payment!="" )
			{ 
				if(trim($data_array)!="") $data_array .= ",";
				$data_array.="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$$cbo_category.",".$$txt_over_rcv_percent.",".$$txt_over_rcv_payment.",".$userid.",'".$pc_date_time."')";
				$id=$id+1;
			}
		}
		
		//print_r($data_array);die;
		$rID=sql_insert("variable_inv_ile_standard",$field_array,$data_array,1);
		if($db_type==0)
		{
		  if( $rID ){
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
	 else if ($operation==2)   // Delete Here===================================================
	 {
		  //no operation
	 }
	  
}
 
 
 
//save update delete for store method
if ($action=="save_update_delete_store_method")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==0)  // Insert Here==============================================================================
	{
 	
		$con = connect();
		if($db_type==0)
		{
		  mysql_query("BEGIN");
		}
		
		$check = is_duplicate_field( "id", "variable_settings_inventory", "company_name=$cbo_company_name and variable_list=$cbo_variable_list and item_category_id=$cbo_item_category" );
		if($check==1)
		{
			echo"11";
			disconnect($con);
			exit();
		}
		
		$id=return_next_id( "id", "variable_settings_inventory", 1 );		
		$field_array="id, company_name, variable_list, item_category_id, store_method, inserted_by, insert_date";		
		$data_array="";		 
		$data_array.="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_item_category.",".$cbo_store_method.",".$userid.",'".$pc_date_time."')";
		$id=$id+1;
						
		//echo $field_array."#####".$data_array;die;
		$rID=sql_insert("variable_settings_inventory",$field_array,$data_array,1);
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
 	else if ($operation==1)   // Update Here=========================================================
	{
		 
		$con = connect();
		if($db_type==0)
		{
		  mysql_query("BEGIN");
		}
		
		if(str_replace("'",$update_id)=="")
		{
			echo "10";disconnect($con);die;
		}
		
		$check = is_duplicate_field( "id", "variable_settings_inventory", "company_name=$cbo_company_name and variable_list=$cbo_variable_list and item_category_id=$cbo_item_category and id!=$update_id" );
		if($check==1)
		{
			echo"11";
			disconnect($con);
			exit();
		}
		 
		$field_array="company_name*variable_list*item_category_id*store_method*updated_by*update_date";		
		$data_array="";
		$data_array.="".$cbo_company_name."*".$cbo_variable_list."*".$cbo_item_category."*".$cbo_store_method."*".$userid."*'".$pc_date_time."'"; 		
		//print_r($data_array);die;
		$rID=sql_update("variable_settings_inventory",$field_array,$data_array,"id",$update_id,1);
		if($db_type==0)
		{
		  if( $rID ){
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
	 else if ($operation==2)   // Delete Here===================================================
	 {
		 
		$con = connect();
		if($db_type==0)
		{
		  mysql_query("BEGIN");
		}
		 $deleteSQL = execute_query("DELETE FROM variable_settings_inventory WHERE id=$update_id","","");
		 if($db_type==0)
		{
		  if( $deleteSQL ){
			  mysql_query("COMMIT");  
			  echo "2**".$rID;
		  }
		  else{
			  mysql_query("ROLLBACK"); 
			  echo "10**".$rID;
		  }
		}
		
		if($db_type==2 || $db_type==1 )
		{
			 if($deleteSQL )
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



//save update delete for allocated quantity
if ($action=="save_update_delete_allocated")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==0)  // Insert Here==============================================================================
	{
 	
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if(str_replace("'","",$cbo_variable_list)==21)
		{
			$check = is_duplicate_field( "id", "variable_settings_inventory", "company_name=$cbo_company_name and variable_list=$cbo_variable_list and item_category_id=$cbo_item_category and status_active=1" );
			if ($check==1)
			{
				echo "11";
				disconnect($con);
				exit();
			}
				
			$id=return_next_id( "id", "variable_settings_inventory", 1 );		
			$field_array="id, company_name, variable_list, item_category_id, rack_balance, store_method, inserted_by, insert_date";		
			$data_array.="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_item_category.",".$cbo_rack_balance.",".$cbo_up_to.",".$userid.",'".$pc_date_time."')";
		}
		else
		{
			$check = is_duplicate_field( "id", "variable_settings_inventory", "company_name=$cbo_company_name and variable_list=$cbo_variable_list and item_category_id=$cbo_item_category" );
			if($check==1)
			{
				echo"11";
				disconnect($con);
				exit();
			}
			
			$id=return_next_id( "id", "variable_settings_inventory", 1 );		
			$field_array="id, company_name, variable_list, item_category_id, allocation, smn_allocation, sales_allocation, inserted_by, insert_date";		
			$data_array.="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_item_category.",".$cbo_allocated.",".$cbo_smn_allocated.",".$cbo_sales_allocated.",".$userid.",'".$pc_date_time."')";
		}

		//echo $field_array."#####".$data_array;die;
		$rID=sql_insert("variable_settings_inventory",$field_array,$data_array,1);
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "0**".$rID;
			}
			else
			{
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
			else
			{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;		
	}
 	else if ($operation==1)   // Update Here=========================================================
	{
		$con = connect();
		if($db_type==0)
		{
		  mysql_query("BEGIN");
		}
	
		if(str_replace("'","",$update_id)=="")
		{
			echo "10";
			disconnect($con);
			die;
		}
		
		if(str_replace("'","",$cbo_variable_list)==21)
		{
			$field_array="company_name*variable_list*item_category_id*rack_balance*store_method*updated_by*update_date";		
			$data_array.="".$cbo_company_name."*".$cbo_variable_list."*".$cbo_item_category."*".$cbo_rack_balance."*".$cbo_up_to."*".$userid."*'".$pc_date_time."'"; 		
			$rID=sql_update("variable_settings_inventory",$field_array,$data_array,"id",$update_id,1);
		}
		else
		{
			$check = is_duplicate_field( "id", "variable_settings_inventory", "company_name=$cbo_company_name and variable_list=$cbo_variable_list and item_category_id=$cbo_item_category and id!=$update_id" );
			if($check==1)
			{
				echo"11";
				disconnect($con);
				exit();
			}
		 
			$field_array="company_name*variable_list*item_category_id*allocation*smn_allocation*sales_allocation*updated_by*update_date";		
			$data_array.="".$cbo_company_name."*".$cbo_variable_list."*".$cbo_item_category."*".$cbo_allocated."*".$cbo_smn_allocated."*".$cbo_sales_allocated."*".$userid."*'".$pc_date_time."'"; 		
			
			$rID=sql_update("variable_settings_inventory",$field_array,$data_array,"id",$update_id,1);
		}
		
		if($db_type==0)
		{
		  if( $rID ){
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
	else if ($operation==2)   // Delete Here===================================================
	{
		$con = connect();
		if($db_type==0)
		{
		  mysql_query("BEGIN");
		}
		 $deleteSQL=execute_query("DELETE FROM variable_settings_inventory WHERE id=$update_id",1);
		 if($db_type==0)
		{
		  if( $deleteSQL ){
			  mysql_query("COMMIT");  
			  echo "0**".$rID;
		  }
		  else{
			  mysql_query("ROLLBACK"); 
			  echo "10**".$rID;
		  }
		}
		
		if($db_type==2 || $db_type==1)
		{
			 if($deleteSQL )
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



//for update get data
if($action=="load_data_for_store_method_form")
{
	 $nameArray=sql_select( "select id,company_name,variable_list,item_category_id,user_given_code_status, store_method from variable_settings_inventory where id='$data'" );
	  foreach ($nameArray as $inf)
	  {
		  echo "document.getElementById('cbo_company_name').value = '".($inf[csf("company_name")])."';\n"; 
		  echo "document.getElementById('cbo_variable_list').value = '".($inf[csf("variable_list")])."';\n";
		  echo "document.getElementById('cbo_item_category').value = '".($inf[csf("item_category_id")])."';\n"; 
		  echo "document.getElementById('cbo_store_method').value = '".($inf[csf("store_method")])."';\n";
		  echo "document.getElementById('update_id').value = '".($inf[csf("id")])."';\n";    
		  echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_variable_settings_inventory_store_method',1);\n";  
	  }
	  exit();

}



//for update get data allocation------------------
if($action=="load_data_for_allocation_form")
{
	//echo "select id,company_name,variable_list,item_category_id,user_given_code_status, allocation from variable_settings_inventory where id='$data'";
	$nameArray=sql_select( "SELECT id AS ID, company_name AS COMPANY_NAME, variable_list AS VARIABLE_LIST, item_category_id AS ITEM_CATEGORY_ID, allocation AS ALLOCATION, smn_allocation AS SMN_ALLOCATION, sales_allocation AS SALES_ALLOCATION FROM variable_settings_inventory WHERE id='".$data."'",1 );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('cbo_company_name').value = '".($inf['COMPANY_NAME'])."';\n"; 
		echo "document.getElementById('cbo_variable_list').value = '".($inf['VARIABLE_LIST'])."';\n";
		echo "document.getElementById('cbo_item_category').value = '".($inf['ITEM_CATEGORY_ID'])."';\n"; 
		echo "document.getElementById('cbo_allocated').value = '".($inf['ALLOCATION'])."';\n";
		echo "document.getElementById('cbo_smn_allocated').value = '".($inf['SMN_ALLOCATION'])."';\n";
		echo "document.getElementById('cbo_sales_allocated').value = '".($inf['SALES_ALLOCATION'])."';\n";
		echo "document.getElementById('update_id').value = '".($inf['ID'])."';\n";    
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_variable_settings_inventory_allocation',1);\n";  
	}
	exit();
}

//for update get data allocation------------------
if($action=="load_data_for_rack_balance_form")
{
	//echo "select id,company_name,variable_list,item_category_id,user_given_code_status, allocation from variable_settings_inventory where id='$data'";
	 $nameArray=sql_select( "select id,company_name,variable_list,item_category_id,user_given_code_status, rack_balance, store_method from variable_settings_inventory where id='$data'",1 );
	  foreach ($nameArray as $inf)
	  {
		  echo "document.getElementById('cbo_company_name').value = '".($inf[csf("company_name")])."';\n"; 
		  echo "document.getElementById('cbo_variable_list').value = '".($inf[csf("variable_list")])."';\n";
		  echo "document.getElementById('cbo_item_category').value = '".($inf[csf("item_category_id")])."';\n"; 
		  echo "document.getElementById('cbo_rack_balance').value = '".($inf[csf("rack_balance")])."';\n";
		  echo "document.getElementById('cbo_up_to').value = '".($inf[csf("store_method")])."';\n";
		  echo "document.getElementById('update_id').value = '".($inf[csf("id")])."';\n";    
		  echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_variable_settings_inventory_allocation',1);\n";  
	  }
	  exit();

}


if($action=="load_data_for_ack")
{
	//echo "select id,company_name,variable_list,item_category_id,user_given_code_status, allocation from variable_settings_inventory where id='$data'";
	 $nameArray=sql_select( "select id,company_name,variable_list,item_category_id,user_given_code_status, auto_transfer_rcv from variable_settings_inventory where id='$data'",1 );
	  foreach ($nameArray as $inf)
	  {
		  echo "document.getElementById('cbo_company_name').value = '".($inf[csf("company_name")])."';\n"; 
		  echo "document.getElementById('cbo_variable_list').value = '".($inf[csf("variable_list")])."';\n";
		  echo "document.getElementById('cbo_item_category').value = '".($inf[csf("item_category_id")])."';\n"; 
		  echo "document.getElementById('cbo_independent_con').value = '".($inf[csf("auto_transfer_rcv")])."';\n";
		  echo "document.getElementById('update_id').value = '".($inf[csf("id")])."';\n";    
		  echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_variable_settings_auto_transfer_rcv',1);\n";  
	  }
	  exit();

}


 
?>