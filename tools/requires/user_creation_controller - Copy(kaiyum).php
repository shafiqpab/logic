<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$buyer_array=return_library_array( "Select id,buyer_name from lib_buyer", "id", "buyer_name"  );
$company_array=return_library_array( "Select id,company_name from lib_company", "id", "company_name"  );

if($action=="show_list_view")
{
	//ALTER TABLE `user_passwd` ADD `department_id` number( 11 ) NOT NULL DEFAULT '0' AFTER `valid`
	 $custom_designation=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');	
	 $Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');	;
	 $arr=array (2=>$custom_designation,3=>$Department);
	 echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, "select id,user_name,department_id,user_full_name,designation from  user_passwd where VALID=1", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
								
}
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//echo "saju".$update_id;die; 
	 
	if ($operation==0)  // Insert Here
	{
		if (is_duplicate_field( "user_name", "user_passwd", "user_name=$txt_user_id" )==1)
		{
			echo "11**0"; die;
		}
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN"); 
		}
		 $txt_passwd=trim(encrypt(str_replace("'","",$txt_passwd)));
		 
		$id=return_next_id( "id", "user_passwd", 1 ) ;
		$field_array="id,user_name,password,user_full_name,designation,created_on,created_by,access_ip,expire_on,user_level,buyer_id,unit_id,is_data_level_secured,valid,department_id";
		$data_array="(".$id.",".$txt_user_id.",'".$txt_passwd."',".$txt_full_user_name.",".$cbo_designation.",'".$pc_date."','".$_SESSION['logic_erp']["user_id"]."',".$txt_ip_addres.",".$txt_exp_date.",".$cbo_user_level.",".$cbo_user_buyer.",".$cbo_unit_name.",".$cbo_data_level_sec.",1,".$cbo_department.")";
		
		
		//echo "INSERT INTO user_passwd(".$field_array.") VALUES ".$data_array;die;

		$rID=sql_insert("user_passwd",$field_array,$data_array,1);
		 
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0****".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10****".$rID;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con); 
				echo "0****".$rID;
			}
			else{
				oci_rollback($con);
				echo "10****".$rID;
			}
		}
		disconnect($con);
		die;
	}
	
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//echo $update_id."shajjad";die;
/*function sql_update2($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit)
{
	$strQuery = "UPDATE ".$strTable." SET ";
	$arrUpdateFields=explode("*",$arrUpdateFields);
	$arrUpdateValues=explode("*",$arrUpdateValues);	
	
	if(is_array($arrUpdateFields))
	{
		$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
		$Arraysize = count($arrayUpdate);
		$i = 1;
		foreach($arrayUpdate as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value." WHERE ";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrUpdateFields."=".$arrUpdateValues." WHERE ";
	}
	$arrRefFields=explode("*",$arrRefFields);
	$arrRefValues=explode("*",$arrRefValues);	
	if(is_array($arrRefFields))
	{
		$arrayRef = array_combine($arrRefFields,$arrRefValues);
		$Arraysize = count($arrayRef);
		$i = 1;
		foreach($arrayRef as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value." AND ":$key."=".$value."";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrRefFields."=".$arrRefValues."";
	}
	
	global $con;
	echo $strQuery; die;
	 //return $strQuery; die;
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	if ($exestd) 
		return "1";
	else 
		return "0";
	
	die;
	if ( $commit==1 )
	{
		if (!oci_error($stid))
		{
			oci_commit($con); 
			return "1";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else
		return 1;
	die;
}*/

		if(str_replace("'","",$update_id)>0)
		{
		
			$txt_passwd=trim(encrypt(str_replace("'","",$txt_passwd)));
			$field_array="password*user_full_name*designation*created_on*created_by*access_ip*expire_on*user_level*buyer_id*unit_id*is_data_level_secured*valid*department_id";
			//$field_array="id,user_name,password,created_on,created_by,access_ip,expire_on,user_level,buyer_id,unit_id,is_data_level_secured,valid";
			 
			$data_array="'".$txt_passwd."'*".$txt_full_user_name."*".$cbo_designation."*'".$pc_date."'*'".$_SESSION['logic_erp']["user_id"]."'*".$txt_ip_addres."*".$txt_exp_date."*".$cbo_user_level."*".$cbo_user_buyer."*".$cbo_unit_name."*".$cbo_data_level_sec."*'1'*".$cbo_department."";
			
			 //echo '10**'.$field_array._.$data_array;die;
			
			$rID=sql_update("user_passwd",$field_array,$data_array,"id","".$update_id."",1);
		}
		else
		{
			echo "5**"; die;
		}
		
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
			if($rID)
			{
				oci_commit($con); 
				echo "1**".$rID;
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
	else if ($operation==2)   // Delete Here
	{
		
		$con = connect();
		
/**/

		$field_array="valid";
		$data_array="'0'";
		$rID=sql_update("user_passwd",$field_array,$data_array,"id","".$update_id."",1);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "2****".$rID;
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
				echo "2****".$rID;
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
 
if ($action=="populate_user_info")
{
	//$buyer_array=return_library_array( "Select id,buyer_name from lib_buyer", "id", "buyer_name"  );
///$company_array=return_library_array( "Select id,company_name from lib_company", "id", "company_name"  );
	$nameArray=sql_select( "select * from user_passwd where user_name='$data' and valid=1" );
	
	foreach ($nameArray as $inf)
	{
		$bu=explode(",",$inf[csf("buyer_id")]);
		
		foreach($bu as $key)
		{
			if($buyer=="") $buyer=$buyer_array[$key]; else $buyer .=",".$buyer_array[$key];
		}
		$cu=explode(",",$inf[csf("unit_id")]);
		foreach($cu as $key)
		{
			if($unit=="") $unit=$company_array[$key]; else $unit .=",".$company_array[$key];
		}
		//echo "document.getElementById('txt_passwd').value = '".trim(decrypt( ($inf[csf("password")])))."';\n";    
		echo "document.getElementById('txt_passwd').value = '';\n";    

		echo "document.getElementById('txt_full_user_name').value = '".($inf[csf("user_full_name")])."';\n";    
		echo "document.getElementById('cbo_designation').value = '".($inf[csf("designation")])."';\n";
		echo "document.getElementById('cbo_department').value = '".($inf[csf("department_id")])."';\n";    
		echo "document.getElementById('cbo_user_level').value  = '".($inf[csf("user_level")])."';\n"; 
		echo "document.getElementById('cbo_user_buyer_show').value  = '".$buyer."';\n"; 
		echo "document.getElementById('cbo_user_buyer').value  = '".($inf[csf("buyer_id")])."';\n";
		echo "document.getElementById('cbo_unit_name_show').value  = '".$unit."';\n"; 
		echo "document.getElementById('cbo_unit_name').value  = '".($inf[csf("unit_id")])."';\n"; 
		echo "document.getElementById('cbo_data_level_sec').value  = '".($inf[csf("is_data_level_secured")])."';\n";
		if($inf[csf("access_proxy_ip")]!="")	
			echo "document.getElementById('txt_ip_addres').value  = '".$inf[csf("access_ip")].",".$inf[csf("access_proxy_ip")]."';\n";
		else
			echo "document.getElementById('txt_ip_addres').value  = '".$inf[csf("access_ip")]."';\n";
			
		//echo "document.getElementById('txt_exp_date').value  = '".($inf[csf("expire_on")])."';\n"; 
		
		echo "document.getElementById('txt_exp_date').value  = '".change_date_format(($inf[csf("expire_on")]),'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n";   
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_user_creation',1);\n";  
	}
}



if ($action=="load_php_data_to_form")
{
	//$buyer_array=return_library_array( "Select id,buyer_name from lib_buyer", "id", "buyer_name"  );
///$company_array=return_library_array( "Select id,company_name from lib_company", "id", "company_name"  );
	
	
	$nameArray=sql_select( "select * from user_passwd where id='$data' and valid=1" );
	
	foreach ($nameArray as $inf)
	{
		$bu=explode(",",$inf[csf("buyer_id")]);
		
		foreach($bu as $key)
		{
			if($buyer=="") $buyer=$buyer_array[$key]; else $buyer .=",".$buyer_array[$key];
		}
		$cu=explode(",",$inf[csf("unit_id")]);
		foreach($cu as $key)
		{
			if($unit=="") $unit=$company_array[$key]; else $unit .=",".$company_array[$key];
		}
		echo "document.getElementById('txt_user_id').value = '".trim(($inf[csf("user_name")]))."';\n";    
		echo "document.getElementById('txt_passwd').value = '';\n";
		echo "document.getElementById('txt_full_user_name').value = '".($inf[csf("user_full_name")])."';\n";    
		echo "document.getElementById('cbo_designation').value = '".($inf[csf("designation")])."';\n";
		echo "document.getElementById('cbo_department').value = '".($inf[csf("department_id")])."';\n";    
		echo "document.getElementById('cbo_user_level').value  = '".($inf[csf("user_level")])."';\n"; 
		echo "document.getElementById('cbo_user_buyer_show').value  = '".$buyer."';\n"; 
		echo "document.getElementById('cbo_user_buyer').value  = '".($inf[csf("buyer_id")])."';\n";
		echo "document.getElementById('cbo_unit_name_show').value  = '".$unit."';\n"; 
		echo "document.getElementById('cbo_unit_name').value  = '".($inf[csf("unit_id")])."';\n"; 
		echo "document.getElementById('cbo_data_level_sec').value  = '".($inf[csf("is_data_level_secured")])."';\n";
		if($inf[csf("access_proxy_ip")]!="")	
			echo "document.getElementById('txt_ip_addres').value  = '".$inf[csf("access_ip")].",".$inf[csf("access_proxy_ip")]."';\n";
		else
			echo "document.getElementById('txt_ip_addres').value  = '".$inf[csf("access_ip")]."';\n";
			
		//echo "document.getElementById('txt_exp_date').value  = '".($inf[csf("expire_on")])."';\n"; 
		
		echo "document.getElementById('txt_exp_date').value  = '".change_date_format(($inf[csf("expire_on")]),'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n";   
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_user_creation',1);\n";  
	}
}



if ($action=="buyer_selection_popup")
{
	echo load_html_head_contents("Buyer Selection Form","../../", $filter, '', $unicode);
 	
	?>
    <script>

	var selected_id = new Array();
	var selected_name = new Array();
		
		function check_all_data(str) {
			//var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			
			//tbl_row_count = tbl_row_count - 1;
			tbl_row_count=str.split(',');
			for( var i = 0; i <= tbl_row_count.length; i++ ) {
				js_set_value( tbl_row_count[i] );
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
				//selected_job.push( $('#txt_individual_job' + str).val() );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				//selected_job.splice( i,1);
			}
			var id ='';
			var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
				//job += selected_job[i] + '*';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			//job = job.substr( 0, job.length - 1 );
			
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
			//$('#txt_selected_job').val( job );
		}
		
		
		
     </script>
     <input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
        <input type="hidden" name="txt_selected"  id="txt_selected" width="650px" value="" />
<div>
	<div style="width:600px;" align="left">
		<table cellspacing="0" cellpadding="0" width="100%" class="rpt_table" >
				<thead>
					<th width="50" align="left">SL No</th>
					<th width="130" align="left">Buyer Name</th>
                    <th width="120" align="left">Sub Con. Party</th>
					<th width="100" align="left">Contact Person</th>
					<th width="80" align="center">Contact NO.</th>
					<th align="center">Email</th>
				</thead>
		</table>
	</div>	
	<div style="width:600px; overflow-y:scroll; min-height:50px; max-height:250px;" id="buyer_list_view" align="left">
		<table  cellspacing="0" cellpadding="0" width="100%" class="rpt_table" id="tbl_list_search" >
		<?php
		 
			$i=1;
			 $nameArray=sql_select( "select * from lib_buyer where is_deleted=0 and status_active=1  order by buyer_name" );
			 foreach ($nameArray as $selectResult)
			 {
			 $id_arr[]=$selectResult[csf('id')];
				if ($i%2==0)  
                	$bgcolor="#E9F3FF";
                else
                	$bgcolor="#FFFFFF";	
	
		?>
		
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $selectResult[csf('id')];?>" onclick="js_set_value(<? echo $selectResult[csf('id')];?>)"> 
				<td width="50" align="center"><?php echo "$i"; ?>
                 <input type="hidden" name="txt_individual" id="txt_individual<? echo $selectResult[csf('id')];?>" value="<?php echo $selectResult[csf('buyer_name')]; ?>"/>
                 <input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $selectResult[csf('id')];?>" value="<?php echo $selectResult[csf('id')]; ?>"/>	
					                         
                </td>	
				<td width="130">&nbsp;
					<?php 
						 
						
						echo split_string($selectResult[csf('buyer_name')],13);
												
					?>
				</td>
                <td width="120">&nbsp; <?php
										$frm=$replacement_lc[$selectResult[csf(subcontract_party)]];  
										echo $frm;
								 ?>
                </td>
				<td width="100">&nbsp; <?php echo split_string($selectResult[csf(contact_person)],10);  ?></td>
				<td width="80">&nbsp; <?php echo  split_string($selectResult[csf(contact_no)],11);  ?></td>
				<td>&nbsp;<?php echo  split_string($selectResult[csf(buyer_email)],10);   ?></td>				
			</tr>
			<?php
			$i++;
			}
			?>
            
		</table>
	</div>
    <div style="width:600px;" align="left">
		<table width="100%">
    		<tr>
				<td align="center" colspan="6" height="30" valign="bottom">
					<div style="width:100%"> 
							<div style="width:50%; float:left" align="left">
								<input type="checkbox" name="check_all" id="check_all" onclick="check_all_data('<? echo implode(',',$id_arr);?>')" /> Check / Uncheck All
							</div>
							<div style="width:50%; float:left" align="left">
							<input type="button" name="close" onclick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
							</div>
					</div>
				</td>
			</tr>
            </table>
	</div>
</div>
<script>
		var buyer_data='<? echo $data;?>';
		buyer_arr=buyer_data.split(',');
		for(var i=0;i<=buyer_arr.length;i++)
		{
			js_set_value( buyer_arr[i] );	
		}

</script>
     <?
	  
	 
	 
}



if ($action=="company_selection_popup")
{
	echo load_html_head_contents("Company Selection Form","../../", $filter, '', $unicode);
 /*	$cu=explode(",",$data);
	foreach($cu as $key)
	{
		if($unit=="") $unit=$company_array[$key]; else $unit .=",".$company_array[$key];
	}*/
	 ?>
     <script>
		//var company_id='<? //echo $data; ?>';
		var selected_id = new Array();
		var selected_name = new Array();
		//selected_id=company_id.split(",");
		//$('#txt_selected_id').val( company_id );
		//$('#txt_selected').val( '<? //echo $unit;?>' );
		//var comp='<? //echo $unit; ?>';
		// selected_id=comp.split(",");
		 
		function check_all_data(str) {
			//var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			//tbl_row_count = tbl_row_count ;
			tbl_row_count=str.split(',');
			for( var i = 0; i <= tbl_row_count.length; i++ ) {
				js_set_value( tbl_row_count[i] );
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
				//selected_job.push( $('#txt_individual_job' + str).val() );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				//selected_job.splice( i,1);
			}
			var id ='' 
			var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
				//job += selected_job[i] + '*';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			//job = job.substr( 0, job.length - 1 );
			
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
			//$('#txt_selected_job').val( job );
		}
     </script>
     <input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
        <input type="hidden" name="txt_selected"  id="txt_selected" width="650px" value="" />
<div>
	<div style="width:600px;" align="left">
		<table cellspacing="0" cellpadding="0" width="100%" class="rpt_table" >
				<thead>
					<th width="50" align="left">SL No</th>
					<th width="130" align="left">Company Name</th>
                    <th width="120" align="left">Short Name</th>
					<th width="100" align="left">Email</th>
					<th width="80" align="center">Web</th>
					<th align="center">Contact Person</th>
				</thead>
		</table>
	</div>	
	<div style="width:600px; overflow-y:scroll; min-height:50px; max-height:250px;" id="buyer_list_view" align="left">
		<table  cellspacing="0" cellpadding="0" width="100%" class="rpt_table" id="tbl_list_search" >
		<?php
		 
			$i=1;
			 $nameArray=sql_select( "select * from lib_company where is_deleted=0 and status_active=1  order by company_name" );$id_arr=array();
			 foreach ($nameArray as $selectResult)
			 {
			 	$id_arr[]=$selectResult[csf('id')];
				if ($i%2==0)  
                	$bgcolor="#E9F3FF";
                else
                	$bgcolor="#FFFFFF";	
			if(in_array($selectResult[csf('id')],$cu)) $bgcolor="#FFFF00";
		?>
		
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?php echo $selectResult[csf('id')]; ?>" onclick="js_set_value(<?php echo $selectResult[csf('id')]; ?>)"> 
				<td width="50" align="center"><?php echo "$i"; ?>
                 <input type="hidden" name="txt_individual" id="txt_individual<?php echo $selectResult[csf('id')]; ?>" value="<?php echo $selectResult[csf('company_name')]; ?>"/>
                 <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $selectResult[csf('id')]; ?>" value="<?php echo $selectResult[csf('id')]; ?>"/>	
					                         
                </td>	
				<td width="130">&nbsp;
					<?php 
						 
						
						echo split_string($selectResult[csf('company_name')],13);
												
					?>
				</td>
                <td width="120">&nbsp;<?php
										$frm=$replacement_lc[$selectResult[csf(company_short_name)]];  
										echo $frm;
									  ?>
                </td>
				<td width="100">&nbsp; <?php echo split_string($selectResult[csf(email)],10);  ?></td>
				<td width="80">&nbsp; <?php echo  split_string($selectResult[csf(website)],11);  ?></td>
				<td>&nbsp;<?php echo  split_string($selectResult[csf(contract_person)],10);   ?></td>				
			</tr>
			<?php
			$i++;
			}
			?>
            
		</table>
	</div>
    <div style="width:600px;" align="left">
		<table width="100%">
    		<tr>
				<td align="center" colspan="6" height="30" valign="bottom">
					<div style="width:100%"> 
							<div style="width:50%; float:left" align="left">
								<input type="checkbox" name="check_all" id="check_all" onclick="check_all_data('<? echo implode(',',$id_arr);?>')" /> Check / Uncheck All
							</div>
							<div style="width:50%; float:left" align="left">
							<input type="button" name="close" onclick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
							</div>
					</div>
				</td>
			</tr>
            </table>
	</div>
</div>
<script>
		var comSelectedId='<? echo $data;?>';
		comSelectedIdArr=comSelectedId.split(',');
		for(var i=0;i<=comSelectedIdArr.length;i++)
		{
			js_set_value( comSelectedIdArr[i] );	
		}

</script>


     <?
}


?>