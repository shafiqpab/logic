<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];

if($action=="item_group_popup")
{
  	echo load_html_head_contents("Item Group Name Info","../../../", 1, 1, '','','');
	extract($_REQUEST);
    ?>
	<script>

		var selected_id = new Array(); 
		var selected_name = new Array();

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
			//alert(x);
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function set_all()
		{
			var old=document.getElementById('txt_item_group_row_id').value;
			//alert(old);
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
			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
				//alert($('#txt_individual' + str).val());
			}
			else 
			{
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
			//alert(id);
			$('#hidden_item_group_id').val(id);
			$('#hidden_item_group_name').val(name);
		}
    </script>

	</head>
	<body>
	<div align="center">
		<fieldset style="width:370px;margin-left:10px">
	    	<input type="hidden" name="hidden_item_group_id" id="hidden_item_group_id" class="text_boxes" value="">
	        <input type="hidden" name="hidden_item_group_name" id="hidden_item_group_name" class="text_boxes" value="">
	        <form name="searchbuyerfrm_1"  id="searchbuyerfrm_1" autocomplete="off">
	            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
	                <thead>
	                    <th width="50">SL</th>
	                    <th>Item Group Name</th>
	                </thead>
	            </table>
	            <div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
	                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
						<?
	                    $sql_item_name=sql_select("select id, item_name from lib_item_group where item_category=$cbo_item_category and status_active =1 and is_deleted=0 order by item_name");
	                    $i=1;
	                    $txt_item_group_row_id='';
	                    $hidden_item_group_id=explode(",",$txt_item_group_id);
	                    foreach($sql_item_name as $row)
	                    {
	                        if ($i%2==0) $bgcolor="#E9F3FF"; 
	                        else $bgcolor="#FFFFFF";

	                        $id=$row[('ID')];
	                        if(in_array($id,$hidden_item_group_id)) 
							{ 
								if($txt_item_group_row_id=="") $txt_item_group_row_id=$i; else $txt_item_group_row_id.=",".$i;
							}

	                        ?>
	                        <tr bgcolor="<?= $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<?= $i; ?>" onClick="js_set_value('<?= $i; ?>');">
	                            <td width="50" align="center"><?= $i; ?>
	                                <input type="hidden" name="txt_individual_id" id="txt_individual_id<?= $i; ?>" value="<?= $row[csf('id')]; ?>"/>
	                                <input type="hidden" name="txt_individual" id="txt_individual<?= $i; ?>" value="<?= $row[csf('item_name')]; ?>"/>
	                            </td>
	                            <td style="word-break:break-all"><?= $row[csf('item_name')]; ?></td>
	                        </tr>
	                        <?
	                        $i++;
	                    }
	                    ?>
	                    <input type="hidden" name="txt_item_group_row_id" id="txt_item_group_row_id" value="<?= $txt_item_group_row_id; ?>"/>
	                </table>
	            </div>
	             <table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
	                <tr>
	                    <td align="center" height="30" valign="bottom">
	                        <div style="width:100%">
	                            <div style="width:50%; float:left" align="left">
	                                <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();" /> Check / Uncheck All
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
		setFilterGrid('tbl_list_search',-1);
		set_all();
	</script>
	</html>
	<?
	exit();
}

if ($action=="main_group_list_view")
{
	$ex_data=explode('**', $data);
	$item_category_cond='';
	if ($ex_data[1] != 0) $item_category_cond=" and item_category_id='$ex_data[1]'";
	$itemGroupArr=return_library_array( "select id,item_name from lib_item_group",'id','item_name');
	$userArr=return_library_array( "select id,user_name from user_passwd",'id','user_name');

	$sql="select id,main_group_name,item_category_id,item_group_id,user_id,status_active from lib_main_group where is_deleted=0 $item_category_cond order by id desc";
	$sql_res=sql_select($sql);
	$item_group_arr=array();
	$user_arr=array();
	foreach($sql_res as $row)
	{
		$item_group_ids=explode(',',$row[csf('item_group_id')]);
		$item_groups='';
		foreach ($item_group_ids as  $id) {
			$item_groups.=$itemGroupArr[$id].', ';
		}
		$item_group_arr[$row[csf('id')]]= rtrim($item_groups,', ');

		$user_ids=explode(',',$row[csf('user_id')]);
		$users='';
		foreach ($user_ids as  $id) {
			$users .= $userArr[$id].', ';
		}
		$user_arr[$row[csf('id')]]= rtrim($users,', ');
	}
	$arr=array (1=>$item_category,2=>$item_group_arr,3=>$user_arr,4=>$row_status);
	echo create_list_view ( "list_view", "Main Group,Item Catagory,Item Group,User,Status", "150,150,200,200,100","890","320",0, $sql, "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,item_category_id,id,id,status_active", $arr , "main_group_name,item_category_id,id,id,status_active", "../item_details/requires/main_group_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0' );
	die;
}


if ($action=="save_update_delete")
{  

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	

	if ($operation==0)  // Insert Here
	{		
		if(is_duplicate_field( "main_group_name", "lib_main_group", "main_group_name=$txt_main_group and item_category_id=$cbo_item_category and is_deleted=0" ) == 1)
		{
			echo "11**0"; die;
		}

		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$item_group_ids=str_replace("'","",$txt_item_group_id);
		
		if ($item_group_ids == ""){
			$sql_item_name_res=sql_select("select id, item_name from lib_item_group where item_category='".str_replace("'","",$cbo_item_category)."' and status_active =1 and is_deleted=0 order by item_name");
			foreach ($sql_item_name_res as $val) {
				$all_item_id.=$val[csf('id')].',';
			}
			$item_group_ids=rtrim($all_item_id,',');
		}

		$id=return_next_id( "id", "lib_main_group", 1 ) ;
		$field_array="id, main_group_name, item_category_id, item_group_id, user_id, status_active, inserted_by, insert_date";
		$data_array="(".$id.",".$txt_main_group.",".$cbo_item_category.",'".$item_group_ids."',".$cbo_user.",".$cbo_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		$rID=sql_insert("lib_main_group",$field_array,$data_array,1);
		
		//echo "10**INSERT INTO lib_main_group (".$field_array.") VALUES ".$data_array;die;
		//$flag=1;

		
		//$txt_item_group_id=str_replace("'","",$txt_item_group_id);		
		if ($item_group_ids != '')
		{
			//echo "$item_group_ids";
			$field_array_item_update="main_group_id*updated_by*update_date";
			$data_array_item_update="".$id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID2=sql_multirow_update("lib_item_group",$field_array_item_update,$data_array_item_update,"id",$item_group_ids,0);
			//echo "10**$rID2";			
			if($rID2) $flag=1; else $flag=0;

		}	

		

		if($db_type==0)
		{
			if($rID && $flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'","",$id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $flag==1)
			{
				oci_commit($con);   
				echo "0**".str_replace("'","",$id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$id);
			}
		}
		disconnect($con);
		die;
	}
		
	else if ($operation==1)   // Update Here
	{
		$hidden_main_group_id=str_replace("'","",$hidden_main_group_id);
		if($hidden_main_group_id=="")
		{
			echo "11**0"; die;
		}
			
		if(is_duplicate_field( "main_group_name", "lib_main_group", "main_group_name=$txt_main_group and item_category_id=$cbo_item_category and is_deleted=0 and id<>$hidden_main_group_id" ) == 1)
		{
			echo "11**0"; die;
		}

		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$item_group_ids=str_replace("'","",$txt_item_group_id);

		
		if ($item_group_ids == "")
		{
			$sql_item_name_res=sql_select("select id, item_name from lib_item_group where item_category='".str_replace("'","",$cbo_item_category)."' and status_active =1 and is_deleted=0 order by item_name");
			foreach ($sql_item_name_res as $val) {
				$all_item_id.=$val[csf('id')].',';
			}
			$item_group_ids=rtrim($all_item_id,',');
		}
		//echo "10**$item_group_ids";die;
		$field_array="main_group_name*item_category_id*item_group_id*user_id*status_active*updated_by*update_date";
		$data_array="".$txt_main_group."*".$cbo_item_category."*'".$item_group_ids."'*".$cbo_user."*".$cbo_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID=sql_update("lib_main_group",$field_array,$data_array,"id","$hidden_main_group_id",1);
		//echo "10**".$rID;oci_rollback($con);disconnect($con);die;

		//$flag=1;
  
        //echo $hidden_main_group_id.'system';
		//$txt_item_group_id=str_replace("'","",$txt_item_group_id);		
		if ($item_group_ids != '')
		{			
			$field_array_item_update="main_group_id*updated_by*update_date";
			$data_array_item_update="".$hidden_main_group_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			//echo $data_array_item_update;
			$rID2=sql_multirow_update("lib_item_group",$field_array_item_update,$data_array_item_update,"id",$item_group_ids,0);			
			if($rID2) $flag=1; else $flag=0;
		}

		if($db_type==0)
		{
			if($rID && $flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$hidden_main_group_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$hidden_main_group_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
		    if($rID && $flag==1)
		    {
				oci_commit($con);   
				echo "1**".str_replace("'","",$hidden_main_group_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$hidden_main_group_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		/*$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$price_quat_check = return_field_value( "id", "wo_pri_quo_trim_cost_dtls", "trim_group=$update_id and status_active=1","id");
		$budge_check = return_field_value( "id", "wo_pre_cost_trim_cost_dtls", "trim_group=$update_id and status_active=1","id");
		$product_check = return_field_value( "id", "product_details_master", "item_group_id=$update_id and status_active=1","id");
		if($price_quat_check >0 || $budge_check >0 || $product_check >0)
		{
			echo "5555**".str_replace("'", "", $update_id);disconnect($con);die;
		}
		if(is_duplicate_field( "a.id", "inv_transaction a, product_details_master b", "b.item_group_id=$update_id and a.prod_id=b.id and b.item_category_id=$cbo_item_category and a.status_active=1 and a.is_deleted=0" ) == 1)
			{
				echo "5555**".str_replace("'", "", $update_id);disconnect($con);die;
			}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$rID=sql_delete("lib_item_group",$field_array,$data_array,"id","".$update_id."",1);
		
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
		die;*/
	}	
}

if ($action=="load_php_data_to_form")
{
	$itemGroupArr=return_library_array( "select id,item_name from lib_item_group",'id','item_name');
	$userArr=return_library_array( "select id,user_name from user_passwd",'id','user_name');
	$sql_main_group = "select id, main_group_name, item_category_id, item_group_id, user_id, status_active from lib_main_group where is_deleted=0 and id='$data'" ;//die;
	$nameArray=sql_select($sql_main_group);
	foreach ($nameArray as $inf)
	{
		$item_groups='';
		if ($inf[csf('item_group_id')] != '')
		{
			$item_group_ids=explode(',',$inf[csf('item_group_id')]);
			$item_groups='';
			foreach ($item_group_ids as  $id) {
				$item_groups.=$itemGroupArr[$id].', ';
			}
			$item_groups= rtrim($item_groups,', ');
		}	
		
		echo "document.getElementById('txt_main_group').value = '".($inf[csf("main_group_name")])."';\n";    
		echo "document.getElementById('cbo_item_category').value  = '".($inf[csf("item_category_id")])."';\n";
		echo "document.getElementById('txt_item_group').value = '".($item_groups)."';\n";    
		echo "document.getElementById('cbo_user').value = '".($inf[csf("user_id")])."';\n";    
		echo "document.getElementById('txt_item_group_id').value = '".($inf[csf("item_group_id")])."';\n";    
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('hidden_main_group_id').value  = '".($inf[csf("id")])."';\n";
		echo "set_multiselect('cbo_user','0','1','".($inf[csf("user_id")])."','0');\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_main_group',1);\n";
	}
	die;
}

?>