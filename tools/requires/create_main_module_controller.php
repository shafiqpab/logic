<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");



include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

extract($_REQUEST);

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$id=return_next_id( "m_mod_id", "main_module", 1 ) ;
		$field_array="m_mod_id,main_module,file_name,status,mod_slno";
		$data_array="(".$id.",".$txt_module_name.",".$txt_module_link.",".$cbo_module_sts.",".$txt_module_seq.")";
		$rID=sql_insert("main_module",$field_array,$data_array,1);
		
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
			else
			{
				oci_rollback($con);
				echo "10**".$rID;
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
		
		$field_array="main_module*file_name*status*mod_slno";
		$data_array=" ".$txt_module_name."*".$txt_module_link."*".$cbo_module_sts."*".$txt_module_seq."";
		$rID=sql_update("main_module",$field_array,$data_array,"m_mod_id","".$update_id."",1);
		
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
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="status";
		$data_array="'0'";
		$rID=sql_update("main_module",$field_array,$data_array,"m_mod_id","".$update_id."",1);
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



else if ($action=="show_module_list_view")
{
	//$arr=array (1=>$yes_no);
	if($data==0) $cond=""; 
	$arr=array (3=>$yes_no); 
	echo  create_list_view ( "list_view", " Module Name,File Location,Sequence,Visiblity", "150,100,150","600","220",0, "select  main_module,file_name,mod_slno,status,m_mod_id from main_module order by mod_slno", "get_php_form_data", "m_mod_id", "'load_php_data_to_form'", 1, "0,0,0,status", $arr , "main_module,file_name,mod_slno,status", "../tools/requires/create_main_module_controller", 'setFilterGrid("list_view",-1);' ) ; 
}

else if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select m_mod_id,main_module,file_name,status,mod_slno from main_module where m_mod_id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('txt_module_name').value = '".($inf[csf("main_module")])."';\n";    
		echo "document.getElementById('txt_module_link').value  = '".($inf[csf("file_name")])."';\n"; 
		echo "document.getElementById('txt_module_seq').value  = '".($inf[csf("mod_slno")])."';\n"; 
		echo "document.getElementById('cbo_module_sts').value  = '".($inf[csf("status")])."';\n";  
		echo "document.getElementById('update_id').value  = '".($inf[csf("m_mod_id")])."';\n";   
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_main_module',1);\n";  
	}
}

?>