<?

header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");

include('../../../includes/common.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$user_id = $_SESSION['logic_erp']['user_id'];

if ($action=="user_tag_popup_leader")
{
	echo load_html_head_contents("User Information", "../../../", 1, 1,$unicode,'','');
	?> 
	<script>
		function js_set_value( str) 
		{
			// alert(str);
			$('#hidden_selected_usertag_popup_id_leader').val( str );
			parent.emailwindow.hide();
		}
	</script>
	<input type="hidden" name="hidden_selected_usertag_popup_id_leader" id="hidden_selected_usertag_popup_id_leader" class="text_boxes" />
	<?
		$sql="select id, employee_id, user_full_name, designation, department_id, user_email from user_passwd where valid=1";
		
		$lib_designation_arr=return_library_array( "select id,custom_designation from lib_designation", "id","custom_designation");
		$lib_department_arr=return_library_array( "select id,department_name from lib_department", "id","department_name");
		
		$arr=array (1=>$lib_designation_arr,2=>$lib_department_arr);
		
		echo create_list_view("list_view", "User Full Name, Designation, Department", "200,200","585","220",0, $sql, "js_set_value", "id,employee_id,user_full_name,designation,department_id,user_email", "", 1, "0,designation,department_id", $arr, "user_full_name,designation,department_id",'','setFilterGrid("list_view",-1);','0,0,0');
	exit();
}

if ($action=="user_tag_popup_member")
{
	echo load_html_head_contents("User Information", "../../../", 1, 1,$unicode,'','');
	?> 
	<script>
		function js_set_value( str) 
		{
			$('#hidden_selected_usertag_popup_id_member').val( str );
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<input type="hidden" name="hidden_selected_usertag_popup_id_member" id="hidden_selected_usertag_popup_id_member" class="text_boxes" />
	<?
	$sql="select id,employee_id,user_full_name,designation,department_id,user_email from user_passwd where valid=1";
	
	$lib_designation_arr=return_library_array( "select id,custom_designation from lib_designation", "id","custom_designation");
	$lib_department_arr=return_library_array( "select id,department_name from lib_department", "id","department_name");
	
	$arr=array (1=>$lib_designation_arr,2=>$lib_department_arr);
	
	echo create_list_view("list_view", "User Full Name, Designation, Department", "200,200","585","220",0, $sql, "js_set_value", "id,employee_id,user_full_name,designation,department_id,user_email", "", 1, "0,designation,department_id", $arr, "user_full_name,designation,department_id",'','setFilterGrid("list_view",-1);','','','','');
	exit();
}

if ($action == "sewing_operation_list_view") {
    $location_arr = return_library_array("select id, location_name from lib_location where status_active=1", "id", "location_name");
    
	$arr = array(1=>$project_type_arr,2 => $location_arr, 5 => $row_status);
	$sql ="select team_name,product_category,location_name,no_of_members,team_efficiency,status,id from  lib_sample_production_team where is_deleted=0";
    echo create_list_view("list_view", "Team Name,Product Category,Location,No of Member,Team Efficiency,Status", "200,150,100,60,60", "750", "220", 1, $sql, "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,product_category,location_name,0,0,status", $arr, "team_name,product_category,location_name,no_of_members,team_efficiency,status", "requires/sample_prod_team_sweater_controller", 'setFilterGrid("list_view",-1);', '0,0,0,0,0,0');        
}

if ($action=="marchant_team_info_det_list_view")
{
	if ($data!="")
	{
		$sql= "select id,team_member_name,designation,team_member_email,team_id,member_contact_no,status_active,user_tag_id from team_member_info where team_id ='$data'  and is_deleted=0 order by id";
	}
	else
	{
		$sql= "select id,team_member_name,designation,team_member_email,team_id,member_contact_no,status_active,user_tag_id from team_member_info where is_deleted=0 order by id";
	}

	$team_name_arr=return_library_array( "select id, team_name from lib_team_mst",'id','team_name');
	$arr=array (3=>$team_name_arr,4=>$row_status);
	echo  create_list_view ( "list_view1", "Member Name,Designation,Email,Team Name,Status", "150,150,200,150,50","800","120",0, $sql, "get_php_form_data", "id","'load_php_data_to_form_marchant_team_info_det'", 1, "0,0,0,team_id,status_active", $arr , "team_member_name,designation,team_member_email,team_id,status_active", "../merchandising_details/requires/marchant_team_info_v2_controller", 'setFilterGrid("list_view1",-1);' );
	exit(); 
}

if ($action == "load_php_data_to_form") {
    // this query will fetch all data from the table into the view page
    $nameArray = sql_select("select id, team_name, location_name, no_of_members, team_efficiency, status,product_category,style_capacity,email from lib_sample_production_team where is_deleted=0 and id='$data'");
    foreach ($nameArray as $inf) {
        echo "document.getElementById('update_id').value = '" .$inf[csf("id")]. "';\n";
        echo "document.getElementById('txt_teamname').value  = '" . $inf[csf("team_name")] . "';\n";
        echo "document.getElementById('cbo_location').value  = '" . $inf[csf("location_name")] . "';\n";
        echo "document.getElementById('txt_no_of_member').value  = '" . $inf[csf("no_of_members")] . "';\n";
        echo "document.getElementById('txt_team_efficiency').value  = '" . $inf[csf("team_efficiency")] . "';\n";
        echo "document.getElementById('cbo_status').value  = '" . $inf[csf("status")]. "';\n";
		
        echo "document.getElementById('cbo_product_category').value  = '" . $inf[csf("product_category")]. "';\n";
        echo "document.getElementById('txt_style_capacity').value  = '" . $inf[csf("style_capacity")]. "';\n";
        echo "document.getElementById('txt_team_email').value  = '" . $inf[csf("email")]. "';\n";
		
		
        echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_sample_prduction',1);\n";
        
        echo "show_list_view('".$inf[csf("id")]."', 'marchant_team_info_det_list_view', 'member_list_view', '../production/requires/sample_prod_team_sweater_controller', 'setFilterGrid(\'list_view1\',-1)');\n";  
    }
}

if ($action == "save_update_delete") {
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));

    if ($operation == 0) {
        $con = connect();
        if ($db_type == 0) {
            mysql_query("BEGIN");
        }

        $id = return_next_id("id", "lib_sample_production_team", 1);
        $field_array = "id,team_name,location_name,no_of_members,team_leader_id,team_efficiency,status,product_category,style_capacity,inserted_by,insert_date,email,status_active,is_deleted";
        $data_array = "(" . $id . "," . $txt_teamname . "," . $cbo_location . "," . $txt_no_of_member ."," . $txt_team_leader_name . "," . $txt_team_efficiency . "," . $cbo_status . "," .$cbo_product_category . "," . $txt_style_capacity . "," .$user_id . ",'" . $pc_date_time . "'," .  $txt_team_email . "," .$cbo_status . ",0)";
        //echo "insert into lib_sample_production_team($field_array)values".$data_array;die;
		//echo "10** insert into lib_sample_production_team $field_array value" . $data_array;die;
		
		
		$rID = sql_insert("lib_sample_production_team", $field_array, $data_array, 1);

        if ($db_type == 0) {
            if ($rID) {
                mysql_query("COMMIT");
                echo "0**" . $rID;
            } else {
                mysql_query("ROLLBACK");
                echo "10**" . $rID;
            }
        }

        if ($db_type == 2 || $db_type == 1) {
            if ($rID) {
                oci_commit($con);
                echo "0**" . $rID;
            } else {
                oci_rollback($con);
                echo "10**" . $rID;
            }
        }
        disconnect($con);
        die;
        //}
    } else if ($operation == 1) { 
        $con = connect();
        if ($db_type == 0) {
            mysql_query("BEGIN");
        }

        $field_array = "team_name*location_name*no_of_members*team_leader_id*team_efficiency*status*product_category*style_capacity*updated_by*update_date*email*status_active*is_deleted";
        $data_array = "" . $txt_teamname . "*" . $cbo_location . "*" . $txt_no_of_member . "*". $txt_team_leader_name . "*" . $txt_team_efficiency . "*" . $cbo_status . "*" . $cbo_product_category . "*" .$txt_style_capacity . "*" .$user_id . "*'" . $pc_date_time. "'*" .  $txt_team_email . "*" . $cbo_status . "*0";

         //echo "10**" ;print_r($data_array);die;
		
		
		$rID = sql_update("lib_sample_production_team", $field_array, $data_array, "id", "" . $update_id . "", 1);

        if ($db_type == 0) {
            if ($rID) {
                mysql_query("COMMIT");
                echo "1**" . str_replace("'", "", $update_id);
            } else {
                mysql_query("ROLLBACK");
                echo "10**" . str_replace("'", "", $update_id);
            }
        }
        if ($db_type == 2 || $db_type == 1) {
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
        //}
    } else if ($operation == 2) {   // delete Here
        $con = connect();
        if ($db_type == 0) {
            mysql_query("BEGIN");
        }

        $field_array = "updated_by*update_date*status_active*is_deleted";
        $data_array = "" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*'0'*'1'";

        $rID = sql_delete("lib_sample_production_team", $field_array, $data_array, "id", "" . $update_id . "", 1);

        if ($db_type == 0) {
            if ($rID) {
                mysql_query("COMMIT");
                echo "2**" . $rID;
            } else {
                mysql_query("ROLLBACK");
                echo "10**" . $rID;
            }
        }

        if ($db_type == 2 || $db_type == 1) {
            if ($rID) {
                oci_commit($con);
                echo "2**" . str_replace("'", "", $update_id);
            } else {
                oci_rollback($con);
                echo "10**" . str_replace("'", "", $update_id);
            }
        }

        disconnect($con);
    }
}

 if ($action=="save_update_delete_dtl")
{       
   $process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$txt_capacity_smv_member="''";
	$txt_capacity_basic_member="''";
	if ($operation==0)  // Insert Here
	{
		if (is_duplicate_field( "id", "team_member_info", "team_member_name=$txt_member_name and designation=$txt_member_designation and team_member_email=$txt_member_email and team_id=$update_id  and is_deleted=0" ) == 1)
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
			$id=return_next_id( "id", "team_member_info", 1 ) ;
			$field_array="id,designation,team_member_name,team_member_email,team_id,capacity_smv_member,capacity_basic_member,member_contact_no,user_tag_id,inserted_by,insert_date,status_active,is_deleted,designation_id";
			$data_array="(".$id.",".$txt_member_designation.",".$txt_member_name.", ".$txt_member_email.",".$update_id.",".$txt_capacity_smv_member.",".$txt_capacity_basic_member.",".$txt_member_contact_no.",".$hidden_user_id_member.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_team_member_status.",0,".$txt_member_designation_id.")";
			//$rID=sql_insert("team_member_info",$field_array,$data_array,0);
			$total_member=return_field_value("count(team_id)","team_member_info","team_id=$update_id and is_deleted=0");
			$field_array1="total_member";
			$data_array1="".$total_member."";
			$rID=sql_insert("team_member_info",$field_array,$data_array,0);
			$rID1=sql_update("lib_team_mst",$field_array1,$data_array1,"id","".$update_id."",1);
			  //echo "10**".$total_member; die;
			
			
			
			if($db_type==0)
			{
			  if($rID && $rID1)
				{
					mysql_query("COMMIT");  
					echo "0**".$rID."**".$id;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				 if($rID && $rID1)
				{
					oci_commit($con); 
					echo "0**".$rID."**".$id;
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
	else if ($operation==1)  //Update Here
	{
		if (is_duplicate_field( "id", "team_member_info", "team_member_name=$txt_member_name and designation=$txt_member_designation and team_member_email=$txt_member_email and  team_id=$update_id and id!=$update_id_dtl  and is_deleted=0" ) == 1)
		{
			echo "11**0"; die;
		}
		if (is_duplicate_field( "id", "lib_team_mst", "lib_mkt_team_member_info_id=$update_id_dtl and is_deleted=0" ) == 1)
		{
			echo "14**0"; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$field_array="designation*team_member_name*team_member_email*team_id*capacity_smv_member*capacity_basic_member*member_contact_no*user_tag_id*updated_by*update_date*status_active*is_deleted*designation_id";
			$data_array="".$txt_member_designation."*".$txt_member_name."* ".$txt_member_email."*".$update_id."*".$txt_capacity_smv_member."*".$txt_capacity_basic_member."*".$txt_member_contact_no."*".$hidden_user_id_member."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_team_member_status."*0*".$txt_member_designation_id."";
			$rID=sql_update("team_member_info",$field_array,$data_array,"id","".$update_id_dtl."",1);
			
			/*$field_array="team_leader_name*team_leader_desig*team_leader_email*updated_by*update_date*status_active*is_deleted";
			$data_array="".$txt_member_name."*".$txt_member_designation."*".$txt_member_email."*".$_SESSION['logic_erp']['user_id']."*'".$date."'*".$cbo_team_member_status."*0";
			$rID=sql_update("lib_team_mst",$field_array,$data_array,"lib_mkt_team_member_info_id","".$update_id_dtl."",1);*/
			//echo $rID; die;
			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");  
					echo "1**".$rID;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			else if($db_type==2 || $db_type==1 )
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
		}
	}
	else if ($operation==2)  // Delete Here
	{
		if (is_duplicate_field( "dealing_marchant", "wo_po_details_master", "dealing_marchant=$update_id_dtl and is_deleted=0" ) == 1)
		{
			echo "13**0"; die;
		}
		if (is_duplicate_field( "id", "lib_team_mst", "lib_mkt_team_member_info_id=$update_id_dtl and is_deleted=0" ) == 1)
		{
			echo "13**0"; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$field_array1="updated_by*update_date*status_active*is_deleted";
			$data_array1="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
			//$rID=sql_delete("lib_mkt_team_member_info",$field_array,$data_array,"id","".$update_id_dtl."",0);
			$total_member=return_field_value("count(team_id)","team_member_info","team_id=$update_id and is_deleted=0");
			$field_array="total_member";
			$data_array="".$total_member."";
			$rID=sql_delete("team_member_info",$field_array1,$data_array1,"id","".$update_id_dtl."",0);
			$rID1=sql_update("lib_team_mst",$field_array,$data_array,"id","".$update_id."",1);
			if($db_type==0)
			{
				if($rID && $rID1 )
				{
					mysql_query("COMMIT");  
					echo "2**".$rID;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID && $rID1 )
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
		}
	}
}
?>