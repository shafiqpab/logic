<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$choosenCompany= $choosenCompany;
$permission=$_SESSION['page_permission'];
$user_id = $_SESSION['logic_erp']["user_id"];



if ($action=="load_drop_down_company_location")
{
    extract($_REQUEST);
    //ITEM_CATEGORY
	//echo create_drop_down( "cbo_location_name", 232, "select id,location_name from lib_location where company_id in ($choosenCompany) and status_active =1 and is_deleted=0 order by location_name","id,location_name", 0, "", '', '' ) .'**'. create_drop_down( "cbo_store_name", 232, "select id,store_name from lib_store_location where company_id in ($choosenCompany) and status_active=1 and is_deleted=0 order by store_name","id,store_name", 0, "", '', '');
	echo create_drop_down( "cbo_location_name", 232, "select id,location_name from lib_location where company_id in ($choosenCompany) and status_active =1 and is_deleted=0 order by location_name","id,location_name", 0, "", '', '' );
    //$inf=sql_select( "select * from user_passwd where id='".$update_id."' and valid=1" );
	//echo "**".($inf[0][csf("company_location_id")])."*".($inf[0][csf("store_location_id")]);
	//echo set_multiselect('cbo_location_name','0','0','','0','getStore()');
	exit();
}

if ($action=="load_drop_down_location")
{
    echo create_drop_down( "cbo_location_name", 232, "select id,location_name from lib_location where company_id in ($data) and status_active =1 and is_deleted=0 order by location_name","id,location_name", 0, "", '', "" );
	exit();
}

if ($action=="load_drop_down_location_store")
{
	extract($_REQUEST);
	echo create_drop_down( "cbo_store_name", 232, "select id,store_name from lib_store_location where location_id in ($location_id) and status_active=1 and is_deleted=0 and company_id in ($choosenCompany) order by store_name","id,store_name", 0, "", '', '');
	
	
	
	
	exit();
}
if ($action=="load_location_store")
{
	extract($_REQUEST);
	$data= explode('**', $data);
	if($data[0]>0) $data[0]=$data[0];else $data[0]=0;
	if($data[1]>0) $data[1]=$data[1];else $data[1]=0;
    echo create_drop_down( "cbo_store_name", 232, "select id,store_name from lib_store_location where location_id in ($data[0]) and status_active=1 and is_deleted=0 and company_id in ($data[1]) order by store_name","id,store_name", 0, "", '', '');
	exit();
}
if($action == "load_update_location_store"){
	extract($_REQUEST);
		if($location_id>0) $location_id=$location_id;else $location_id=0;
	echo create_drop_down( "cbo_store_name", 232, "select id,store_name from lib_store_location where location_id in ($location_id) and status_active=1 and is_deleted=0 and company_id in ($choosenCompany) order by store_name","id,store_name", 0, "", '', '');
	$inf=sql_select( "select store_location_id from user_passwd where id='".$update_id."' and valid=1");
	echo "**".($inf[0][csf("store_location_id")]);
	exit();
}
if($action=="show_list_view")
{
	 $custom_designation=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');
	 $Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');	;
	 $arr=array (2=>$custom_designation,3=>$Department);
	 echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, "select id,user_name,department_id,user_full_name,designation from  user_passwd where VALID=1", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_credentials_controller", 'setFilterGrid("list_view",-1);' ) ;
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==1)   // Update Here cbo_credential_user_planner CREDENTIAL_USE_FOR_PLANNER  credential_use_for_planner
	{
        $con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if(str_replace("'","",$update_id)>0)
		{
            $field_array="workstation_location*unit_id*company_location_id*store_location_id*buyer_id*supplier_id*item_cate_id*tna_task_id*credential_use_for_planner*is_planner*graph_id*created_on*created_by";
		    $data_array="".$cbo_workstation_location."*".$cbo_company_name."*".$cbo_location_name."*".$cbo_store_name."*".$cbo_user_buyer."*".$cbo_user_supplier."*".$cbo_item_category."*".$cbo_tna_task_id."*".$cbo_credential_user_planner."*".$cbo_planner_type."*".$cbo_graph_id."*'".$pc_date."'*'".$_SESSION['logic_erp']["user_id"]."'";
            $rID=sql_update("user_passwd",$field_array,$data_array,"id","".$update_id."",1,0);
		
			// echo $rID;die;
		
		   // cbo_item_category
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

if ($action=="save_update_delete_role")
{ 
	$process = array( &$_POST );
	//print_r($process );
	extract(check_magic_quote_gpc( $process ));
	$data_srting_arr=explode(",",$data_string);
	//print_r($data_srting_arr);
	if ($operation==0)  //Insert Here
	{
		$con = connect();
			
		
		$field_array_dtls="id,user_id,activities_id, inserted_by, insert_date, status_active, is_deleted";
		$data_array_dtls="";
		$dtlsid=return_next_id( "id", " user_activities_setup", 1) ;

		foreach($data_srting_arr as $role){
            if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$dtlsid.",".$role_user_id.",".$role[0].",".$user_id.",'".$pc_date_time."',1,0)";
			$dtlsid++;

		}
		
		
		
		$flag=1;
		
	
		$dtlsrID=sql_insert("user_activities_setup",$field_array_dtls,$data_array_dtls,1);
		if($dtlsrID==1 && $flag==1) $flag=1; else $flag=0;
		//============================================================================================
		//echo "10**insert into user_activities_setup($field_array_dtls)values".$data_array_dtls;die;
		//echo "10**".$rID."**".$dtlsrID."**".$flag; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "0**".$id."**".str_replace("'","",$role_user_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$id."**".str_replace("'","",$role_user_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".$id."**".str_replace("'","",$role_user_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".$id."**".str_replace("'","",$role_user_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{  
		$con = connect();

		$sql_detele=sql_select("delete from user_activities_setup  where user_id=$role_user_id");
		// $field_array_dtls="id,user_id,activities_id, inserted_by, insert_date, status_active, is_deleted";
		// $data_array_dtls="";
		
		$field_array_dtls="id,user_id,activities_id, inserted_by, insert_date, status_active, is_deleted";
		$data_array_dtls="";
		$dtlsid=return_next_id( "id", " user_activities_setup", 1) ;

		foreach($data_srting_arr as $role){
            if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$dtlsid.",".$role_user_id.",".$role[0].",".$user_id.",'".$pc_date_time."',1,0)";
			$dtlsid++;

		}
		
		
		
		$flag=1;
		
	
		$dtlsrID=sql_insert("user_activities_setup",$field_array_dtls,$data_array_dtls,1);
		if($dtlsrID==1 && $flag==1) $flag=1; else $flag=0;
		//============================================================================================
		//echo "10**insert into user_activities_setup($field_array_dtls)values".$data_array_dtls;die;
		//echo "10**".$rID."**".$dtlsrID."**".$flag; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "0**".$id."**".str_replace("'","",$role_user_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$id."**".str_replace("'","",$role_user_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".$id."**".str_replace("'","",$role_user_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".$id."**".str_replace("'","",$role_user_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$mst_id = str_replace("'", "", $txt_update_id);
		
		$flag=1;
		$rID = sql_delete("wo_buyer_claim_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id ',$txt_update_id,1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		$rID1 = sql_delete("wo_buyer_claim_dtls","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'mst_id ',$txt_update_id,1);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		
		//echo "10**".$rID."**".$rID1."**".$flag; die;
		
		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "2**".$mst_id."**".str_replace("'","",$order_id);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$mst_id."**".str_replace("'","",$order_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con);
				echo "2**".$mst_id."**".str_replace("'","",$order_id);
			}
			else{
				oci_rollback($con);
				echo "10**".$mst_id."**".str_replace("'","",$order_id);
			}
		}
		disconnect($con);
		//echo "2****".$rID;
	}
}


if ($action=="load_php_data_to_form")
{
    $buyer_array=return_library_array( "Select id,buyer_name from lib_buyer", "id", "buyer_name"  );
    $supplier_array=return_library_array( "Select id,supplier_name from lib_supplier", "id", "supplier_name");

    $nameArray=sql_select( "select * from user_passwd where id='$data' and valid=1" );





	foreach ($nameArray as $inf)
	{
		//..................................remove inactive unite location
		$location_array=return_library_array( "select a.id,a.id from lib_location a,LIB_COMPANY b where a.company_id=b.id and a.company_id in (".$inf[csf("unit_id")].") and a.id in(".$inf[csf("company_location_id")].") and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 order by a.location_name", "id", "id");
		$inf[csf("company_location_id")]=implode(',',$location_array);
		//..................................remove  inactive unite stor location
		$stor_id_array=return_library_array( "select a.id,a.id from lib_store_location a,LIB_COMPANY b where a.company_id=b.id and a.company_id in (".$inf[csf("unit_id")].") and a.id in(".$inf[csf("store_location_id")].") and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 order by a.id", "id", "id");
		$inf[csf("store_location_id")]=implode(',',$stor_id_array);


		if($inf[csf("tna_task_id")]!=''){
			$nameArray = sql_select("select TASK_TYPE,TASK_NAME,TASK_SHORT_NAME from LIB_TNA_TASK where is_deleted=0 and status_active=1 and task_name in(".$inf[csf("tna_task_id")].")");
			$tmpTaskArr=array();
			foreach ($nameArray as $rows) 
			{
				$tmpTaskArr[$rows['TASK_NAME']]=$rows['TASK_SHORT_NAME'];
			}
		}

		
		
		$bu=explode(",",$inf[csf("buyer_id")]);

		foreach($bu as $key)
		{
			if($buyer=="") $buyer=$buyer_array[$key]; else $buyer .=",".$buyer_array[$key];
		}

        $su=explode(",",$inf[csf("supplier_id")]);

		foreach($su as $key)
		{
			if($supplier=="") $supplier=$supplier_array[$key]; else $supplier .=",".$supplier_array[$key];
		}
		echo "$('#show_textcbo_company_name').attr('disabled',true);\n";
		echo "document.getElementById('cbo_user_name_show').value = '".trim(($inf[csf("user_name")]))."';\n";
        echo "document.getElementById('cbo_user_buyer_show').value  = '".$buyer."';\n";
        echo "document.getElementById('cbo_user_buyer').value  = '".($inf[csf("buyer_id")])."';\n";
        echo "document.getElementById('cbo_user_supplier_show').value  = '".$supplier."';\n";
        echo "document.getElementById('cbo_user_supplier').value  = '".($inf[csf("supplier_id")])."';\n";
        echo "document.getElementById('cbo_planner_type').value  = '".($inf[csf("is_planner")])."';\n";
        echo "load_drop_down( 'requires/user_credentials_controller', '".$inf[csf("unit_id")]."', 'load_drop_down_location', 'location_td' );\n";
        echo "load_drop_down( 'requires/user_credentials_controller', '".$inf[csf("company_location_id")]."**".$inf[csf("unit_id")]."', 'load_location_store', 'store_td' );\n";
        echo "set_multiselect('cbo_location_name','0','0','','0','updateStore()');\n";
        echo "set_multiselect('cbo_store_name','0','0','','0','0');\n";

		echo "set_multiselect('cbo_company_name*cbo_location_name*cbo_store_name*cbo_item_category','0*0*0*0','1','".($inf[csf("unit_id")])."*".($inf[csf("company_location_id")])."*".($inf[csf("store_location_id")])."*".($inf[csf("item_cate_id")])."','0*0*0*0');\n";
		echo "document.getElementById('cbo_graph_id').value = '".trim(($inf[csf("graph_id")]))."';\n";
		echo "document.getElementById('cbo_workstation_location').value = '".trim(($inf[csf("workstation_location")]))."';\n";
        echo "document.getElementById('cbo_tna_task_id').value  = '".($inf[csf("tna_task_id")])."';\n";
        echo "document.getElementById('cbo_tna_task_show').value  = '".implode(',',$tmpTaskArr)."';\n";
        echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_user_creation',1);\n";
    }
}


// Buyer
if ($action=="buyer_selection_popup")
{ 
	echo load_html_head_contents("Buyer Selection Form","../../", 1,1, $unicode);
	?>
    <script>
        var selected_id = new Array();
        var selected_name = new Array();

		function check_all_data(str) {
			tbl_row_count=str.split(',');
			for( var i = 0; i <= tbl_row_count.length; i++ ) {
				js_set_value( tbl_row_count[i] );
			}
		}

		function toggle(x,origColor ) {
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
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id ='';
			var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
		}
    </script>
    <input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
    <input type="hidden" name="txt_selected"  id="txt_selected" width="650px" value="" />
	<!-- for content type search  -->
	<input type="hidden"  id="cbo_string_search_type" value="4"/> 

    <div>
        <div style="width:625px;" align="left">
            <table cellspacing="0" cellpadding="0" width="100%" class="rpt_table" id="buyer_table">
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

        <div style="width:625px; overflow-y:scroll; min-height:50px; max-height:250px;" id="buyer_list_view" align="left">
            <table  cellspacing="0" cellpadding="0" width="100%" class="rpt_table" id="tbl_list_search" >
            <?php
			$i = 1;
			$nameArray = sql_select("select * from lib_buyer where is_deleted=0 and status_active=1  order by buyer_name");
			foreach ($nameArray as $selectResult) {
				
				$id_arr[] = $selectResult[csf('id')];
				if ($i % 2 == 0) {$bgcolor = "#E9F3FF";} else {$bgcolor = "#FFFFFF";}

					?>
                    <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$selectResult[csf('id')];?>" onclick="js_set_value(<?=$selectResult[csf('id')];?>)">
                        <td width="50" align="center"><?=$i; ?>
                            <input type="hidden" name="txt_individual" id="txt_individual<?=$selectResult[csf('id')];?>" value="<?=$selectResult[csf('buyer_name')]; ?>"/>
                            <input type="hidden" name="txt_individual_id" id="txt_individual_id<?=$selectResult[csf('id')];?>" value="<?=$selectResult[csf('id')]; ?>"/>
                        </td>
                        <td width="130" style="word-break:break-all"><?=$selectResult[csf('buyer_name')]; ?></td>
                        <td width="120" style="word-break:break-all">&nbsp;
                            <?php
							$frm = $replacement_lc[$selectResult[csf('subcontract_party')]];
							echo $frm;
							?>
                        </td>
                        <td width="100" style="word-break:break-all">&nbsp; <?=$selectResult[csf('contact_person')]; ?></td>
                        <td width="80" style="word-break:break-all">&nbsp; <?=$selectResult[csf('contact_no')]; ?></td>
                        <td style="word-break:break-all">&nbsp;<?=$selectResult[csf('buyer_email')]; ?></td>
                    </tr>
                    <?php
					$i++;
				}
			?>
            </table>
        </div>
        <div style="width:625px;" align="left">
            <table width="100%">
                <tr>
                    <td align="center" colspan="6" height="30" valign="bottom">
                        <div style="width:100%">
                                <div style="width:50%; float:left" align="left">
                                    <input type="checkbox" name="check_all" id="check_all" onclick="check_all_data('<?=implode(',',$id_arr);?>');" /> Check / Uncheck All
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
        var buyer_data='<?=$data;?>';
        buyer_arr=buyer_data.split(',');
        for(var i=0;i<=buyer_arr.length;i++)
        {
            js_set_value( buyer_arr[i] );
        }
    </script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>setFilterGrid('tbl_list_search',-1);</script>
    <?
	exit();
}

// Role
if ($action=="role_selection_popup")
{ 
	// echo "test".$data;
	echo load_html_head_contents("TNA Task Selection Form","../../", 1,1,$unicode,1,'');
	$width=340;
    $roll = array(1 => "Admin Planner ", 2 => "Can Lock");
	 
	?>
	<script>
	var permission='<? echo $permission; ?>';
    function fnc_user_activities_setup( operation )
	{     

		if(operation==2)
		{
			alert("Delete Restricted.")
			return;
		}
	    else
		{
			
			var input =  document.getElementsByName('roleID[]');
			var check_input =  document.getElementsByName('checkID[]');		 
			var role_user_id = document.getElementById('role_user_id').value;
			//var role_update_id = document.getElementById('update_id').value;


		
			
			var roleIdArr=Array();var updateidarr=Array();
			for(i=0; i < check_input.length; i++){
				
				if(check_input[i].checked == true ){
						roleIdArr.push(input[i].value);
				}
			}
  
			var dataString = roleIdArr.join(',');
			var updateString = updateidarr.join(',');
			//alert(updateString);
				
			
			var data="action=save_update_delete_role&operation="+operation+"&data_string="+dataString+"&role_user_id="+role_user_id+"&updateString="+updateString;
			
			
			
			//alert(data); release_freezing(); return;
			
			freeze_window(operation);
			http.open("POST","user_credentials_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_on_submit_reponse; 
		}
	}
	function fnc_on_submit_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=http.responseText.split('**');
			//console.log(reponse);
			if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
			{   
				show_msg(trim(reponse[0]));
				set_button_status(1, permission, 'fnc_user_activities_setup',1,0);	
				release_freezing();
			}
		   release_freezing();
		}
	}

	function fnc_checkbox(inc)
	{  
		
		if(document.getElementById('checkID_'+inc).checked==true)
		{
			document.getElementById('checkID_'+inc).value=1;
		
		}
		else if(document.getElementById('checkID_'+inc).checked==false)
		{
			document.getElementById('checkID_'+inc).value=2;
		
		}
	}
	
	</script>

   
        <div style="width:<? echo $width; ?>px; margin:0 auto;">
		<?echo load_freeze_divs ("../../",$permission);?>
            <table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="left " >
                <thead>
					<th width="25"></th>                   
                    <th width="20">SL</th>                   
                    <th width="80"> Role</th>
                    
                </thead>
            </table>            
            <div style="min-width:<? echo $width+25; ?>px; float:left; overflow-y:auto; max-height:320px;">
                <table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all"  class="rpt_table" id="tbl_claim" align="left">
                    <tbody>
					<input type="hidden" id="role_user_id" name="role_user_id"  value="<?=$data;?>" /> 
					<!-- <input type="hidden" id="txt_update_id" name="txt_update_id" />  -->
					<?	
					$sql_data=sql_select("select * from USER_ACTIVITIES_SETUP where user_id=$data");
					$btnStatus = 0;
					foreach($sql_data as $row){

						$activities_arr[$row[csf('activities_id')]]['activities_id']=$row[csf('activities_id')];
						$activities_arr[$row[csf('activities_id')]]['update_id']=$row[csf('id')];
						$btnStatus = 1;
					}
				
                        $i=1; 
						
                        foreach ($roll as $roleid=>$rolename)
                        { 
						 	$checked="" ;
							$checked = ($activities_arr[$roleid]['activities_id']) ? "checked" : "";
							$checked_val = ($activities_arr[$roleid]['activities_id']) ? "1" : "2";
							$update_id = ($activities_arr[$roleid]['activities_id']) ? $activities_arr[$roleid]['update_id'] : "";

							   //print_r($update_id);                                     
                           	$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					
                                ?>
							
								
                                    <td width="25" align="center" valign="middle">
										
                                        <input <?=$checked;?> type="checkbox" id="checkID_<?=$roleid; ?>;" name="checkID[]" onClick="fnc_checkbox(<?=$roleid;?>);" value="<?=$checked_val;?>" />
										<input type="hidden" id="roleID_<?=$roleid; ?>;" name="roleID[]"  value="<?=$roleid;?>" /> 

										
										
                                                                                       
                                   </td> 
                                    <td width="20" align="center"><?=$i?></td>
                                    <td width="80" align="center"><?= $rolename?></td>                                    
                                                                  
                                </tr>
								
								<?
                                $i++;

                     
                        }   
				              
                        ?>
                               
						<tr>
						
                        <td align="center" colspan="4" valign="middle" class="button_container">
							 
							
						<? echo load_submit_buttons( $permission, "fnc_user_activities_setup", $btnStatus,0 ,"reset_form('usercreationform_1','','')",1); ?>
                        </td>
                    </tr>        
                        
                    </tbody>
                </table>
            </div>
          
					</div>
					<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
             
	<?	
	exit();	
   
	
}

// TNA Task
if ($action=="tna_task_selection_popup")
{
	echo load_html_head_contents("TNA Task Selection Form","../../", 1,1,$unicode,1,'');
	?>
    <script>
        var selected_id = new Array();
        var selected_name = new Array();

		function check_all_data(str) {
			
			tbl_row_count=str.split(',');
			for( var i = 0; i <= tbl_row_count.length; i++ ) {
				if($('#search' + tbl_row_count[i]).is(':visible')){
					js_set_value( tbl_row_count[i] );
				}
			}
		}

		function toggle( x='', origColor ) {
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
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id ='';
			var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
		}
        setFilterGrid("tbl_sup_list_search",-1)
    </script>
    <input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
    <input type="hidden" name="txt_selected"  id="txt_selected" width="650px" value="" />
    <input type="hidden" name="cbo_string_search_type"  id="cbo_string_search_type" width="650px" value="4" />

    <div>
        <div style="width:625px;" align="left">
            <table cellspacing="0" cellpadding="0" width="100%" class="rpt_table" >
                    <thead>
                        <th width="50" align="left">SL</th>
						<th width="100" align="center">Task Type</th>
                        <th width="200" align="left">Task Full Name</th>
                        <th align="left">Task Short Name</th>
                    </thead>
            </table>
        </div>

        <div style="width:625px; overflow-y:scroll; min-height:50px; max-height:250px;" id="buyer_list_view" align="left">
            <table  cellspacing="0" cellpadding="0" width="100%" class="rpt_table" id="tbl_tna_list_search" >
            <?php
			$i = 1;
			$tmpTaskArr=array();
			$nameArray = sql_select("select TASK_TYPE,TASK_NAME,TASK_SHORT_NAME from LIB_TNA_TASK where is_deleted=0 and status_active=1  order by TASK_TYPE,TASK_SHORT_NAME");
			foreach ($nameArray as $rows) 
			{
				$tmpTaskArr['TASK_NAME'][$rows['TASK_NAME']][$rows['TASK_SHORT_NAME']]=$rows['TASK_SHORT_NAME'];
				$tmpTaskArr['TASK_TYPE'][$rows['TASK_NAME']][$rows['TASK_TYPE']]=$template_type_arr[$rows['TASK_TYPE']];

			}

			foreach ($tmpTaskArr['TASK_NAME'] as $task_id=>$TASK_SHORT_NAME) 
			{
				$id_arr[] = $task_id;
				$bgcolor =($i % 2 == 0)?"#E9F3FF":"#FFFFFF";
				?>
                <tr bgcolor="<?= $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?= $task_id;?>" onclick="js_set_value(<?= $task_id;?>)">
                    <td width="50" align="center"><?= $i; ?>
                        <input type="hidden" name="txt_individual" id="txt_individual<?= $task_id;?>" value="<?= implode(',',$TASK_SHORT_NAME); ?>"/>
                        <input type="hidden" name="txt_individual_id" id="txt_individual_id<?= $task_id;?>" value="<?= $task_id; ?>"/>
                    </td>
                    <td width="100"><?=implode(', ',$tmpTaskArr['TASK_TYPE'][$task_id]); ?></td>
                    <td width="200"><?=$tna_task_name[$task_id]; ?></td>
                    <td><p><?=implode(', ',$TASK_SHORT_NAME); ?></p></td>
                </tr>
                <?php
				$i++;
			}
			?>
        	</table>
        </div>

        <div style="width:625px;" align="left">
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
		setFilterGrid("tbl_tna_list_search",-1);
        var supplier_data='<? echo $data;?>';
        supplier_arr=supplier_data.split(',');
        for(var i=0;i<=supplier_arr.length;i++)
        {
            js_set_value( supplier_arr[i] );
        }
		
    </script>
    <?
}


// Supplier
if ($action=="supplier_selection_popup")
{
	echo load_html_head_contents("Supplier Selection Form","../../", 1,1,$unicode,1,'');
	?>
    <script>
        var selected_id = new Array();
        var selected_name = new Array();

		function check_all_data(str) {
			tbl_row_count=str.split(',');
			for( var i = 0; i <= tbl_row_count.length; i++ ) {
				js_set_value( tbl_row_count[i] );
			}
		}

		function toggle( x='', origColor ) {
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
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id ='';
			var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
		}
        setFilterGrid("tbl_sup_list_search",-1)
    </script>
    <input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
    <input type="hidden" name="txt_selected"  id="txt_selected" width="650px" value="" />

    <div>
        <div style="width:625px;" align="left">
            <table cellspacing="0" cellpadding="0" width="100%" class="rpt_table" >
                    <thead>
                        <th width="50" align="left">SL No</th>
                        <th width="130" align="left">Supplier Name</th>
                        <th width="100" align="left">Contact Person</th>
                        <th width="80" align="center">Contact NO.</th>
                        <th align="center">Email</th>
                    </thead>
            </table>
        </div>

        <div style="width:625px; overflow-y:scroll; min-height:50px; max-height:250px;" id="buyer_list_view" align="left">
            <table  cellspacing="0" cellpadding="0" width="100%" class="rpt_table" id="tbl_sup_list_search" >
            <?php
			$i = 1;
			$nameArray = sql_select("select id,supplier_name,contact_person,email,contact_no from lib_supplier where is_deleted=0 and status_active=1  order by supplier_name");

			foreach ($nameArray as $selectResult) 
			{
				$id_arr[] = $selectResult[csf('id')];
				if ($i % 2 == 0) {
					$bgcolor = "#E9F3FF";
				} else {
					$bgcolor = "#FFFFFF";
				}
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $selectResult[csf('id')];?>" onclick="js_set_value(<? echo $selectResult[csf('id')];?>)">
                    <td width="50" align="center"><?php echo "$i"; ?>
                        <input type="hidden" name="txt_individual" id="txt_individual<? echo $selectResult[csf('id')];?>" value="<?php echo $selectResult[csf('supplier_name')]; ?>"/>
                        <input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $selectResult[csf('id')];?>" value="<?php echo $selectResult[csf('id')]; ?>"/>
                    </td>
                    <td width="130">&nbsp;<?php echo $selectResult[csf('supplier_name')];//echo split_string($selectResult[csf('supplier_name')], 13); ?></td>
                    <td width="100">&nbsp; <?php echo split_string($selectResult[csf('contact_person')], 10,0); ?></td>
                    <td width="80">&nbsp; <?php echo split_string($selectResult[csf('contact_no')], 11,0); ?></td>
                    <td>&nbsp;<?php echo split_string($selectResult[csf('email')], 10,0); ?></td>

                </tr>
                <?php
				$i++;
			}
			?>
        	</table>
        </div>

        <div style="width:625px;" align="left">
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
        var supplier_data='<? echo $data;?>';
        supplier_arr=supplier_data.split(',');
        for(var i=0;i<=supplier_arr.length;i++)
        {
            js_set_value( supplier_arr[i] );
        }

    </script>
    <?
}

// user
if ($action=="user_selection_popup")
{
    echo load_html_head_contents("User Selection Form","../../",1,1,$unicode,1,'');
    ?>
    <input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
    <input type="hidden" name="txt_selected"  id="txt_selected" width="330px" value="" />
    <div>
        <div style="width:300px;" align="left">
            <table cellspacing="0" cellpadding="0" width="100%" class="rpt_table" >
                <thead>
                    <th width="48" align="left">SL No</th>
                    <th width="128" align="left">User Name</th>
                </thead>
            </table>
        </div>
        <div style="width:300px; overflow-y:scroll; min-height:50px; max-height:250px;" id="buyer_list_view" align="left">
            <table  cellspacing="0" cellpadding="0" width="100%" class="rpt_table" id="tbl_list_search" >
                <?php
$i = 1;
$nameArray = sql_select("select * from user_passwd where valid=1");
foreach ($nameArray as $selectResult) {
	if ($i % 2 == 0) {
		$bgcolor = "#E9F3FF";
	} else {
		$bgcolor = "#FFFFFF";
	}

	if (in_array($selectResult[csf('id')], $cu)) {
		$bgcolor = "#FFFF00";
	}

	?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?php echo $selectResult[csf('id')]; ?>" onclick="js_set_value(<?php echo $selectResult[csf('id')]; ?>)">
                        <td width="50" align="center"><?php echo "$i"; ?>
                            <input type="hidden" name="txt_individual" id="txt_individual<?php echo $selectResult[csf('id')]; ?>" value="<?php echo $selectResult[csf('user_name')]; ?>"/>
                            <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $selectResult[csf('id')]; ?>" value="<?php echo $selectResult[csf('id')]; ?>"/>
                        </td>
                        <td width="130">&nbsp;
                            <?php echo split_string($selectResult[csf('user_name')], 13,''); ?>
                        </td>
                    </tr>
                    <?php
$i++;
}
?>
            </table>
        </div>
    </div>


<script>

    function js_set_value( str ) {
        selected_id = $('#txt_individual_id' + str).val();
        selected_name = $('#txt_individual' + str).val();
        $('#txt_selected_id').val( selected_id);
		$('#txt_selected').val( selected_name );
        parent.emailwindow.hide();
    }
setFilterGrid("tbl_list_search",-1)
</script>

<?
}
?>