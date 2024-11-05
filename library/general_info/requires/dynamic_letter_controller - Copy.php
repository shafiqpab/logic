<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
include('../../../includes/array_function.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="store_location_list_view")
{
		$companyarr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$arr=array (1=>$companyarr);
		echo  create_list_view ( "list_view", "Store Name,Company Name,Location Name", "120,120,220,","530","220",0, "select id,store_name,company_id,store_location from  lib_store_location where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,company_id,0", $arr , "store_name,company_id,store_location", "../general_info/requires/store_location_controller", 'setFilterGrid("list_view",-1);' ) ; 
}

else if ($action=="load_php_data_to_form")
{
		$nameArray=sql_select( "select id, letter_type, letter_body from dynamic_letter where id='$data'" );
		foreach ($nameArray as $inf)
		{
			echo "document.getElementById('cbo_letter_type').value	= '".$inf[csf("letter_type")]."';\n"; 
			//echo "document.getElementById('txt_letter_body').value	= '".base64_decode($inf[csf("letter_body")])."';\n";
			//editor.setData(res[0])
			echo "editor.setData('".$inf[csf("letter_body")]."');\n";   
			echo "document.getElementById('update_id').value		= '".$inf[csf("id")]."';\n"; 
			echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_submit_dynamic_letter',1);\n"; 
		}
}

else if ($action=="save_update_delete")
{
	    $process = array( &$_POST );
		extract(check_magic_quote_gpc( $process )); 
		base64_encode( $data );
		//echo $cbo_letter_type."==".$txt_letter_body ; die;
		
		$txt_letter_body=str_replace("****", "?", $txt_letter_body);
		$txt_letter_body=str_replace("**", "&", $txt_letter_body);
		
		if ($operation==0)  // Insert Here
		{
			if (is_duplicate_field( "letter_type", "dynamic_letter", "letter_type=$cbo_letter_type and is_deleted=0" ) == 1)
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
				//id, letter_type, letter_body, status_active, is_deleted, inserted_by, insert_date, updated_by, update_date
				//cbo_letter_type,txt_letter_body
				$id=return_next_id( "id", "dynamic_letter", 1 );
				$field_array="id,letter_type,letter_body,inserted_by,insert_date";
				//$data_array="(".$id.",".$cbo_letter_type.",'".base64_encode( $txt_letter_body )."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$data_array="(".$id.",".$cbo_letter_type.",'".$txt_letter_body."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				//echo "insert into  dynamic_letter ".$field_array." values  ".$data_array.""; die;
				
				$flag=1;
				$rID=sql_insert("dynamic_letter",$field_array,$data_array,0);
				if($rID) $flag=1; else $flag=0;
				//=================================================================================
				if($db_type==0)
				{
					if($flag==1)
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
				
				if($db_type==2 || $db_type==1 )
				{
					if($flag==1)
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
		
		if ($operation==1)  // Update Here
		{
			if (is_duplicate_field( "letter_type", "dynamic_letter", "letter_type=$cbo_letter_type and id!=$update_id and  is_deleted=0" ) == 1)
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
				$field_array="letter_type*letter_body*status_active*is_deleted*updated_by*update_date";
			    $data_array="".$cbo_letter_type."*'".base64_encode( $txt_letter_body )."'*0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				$flag==1;
				$rID=sql_update("dynamic_letter",$field_array,$data_array,"id","".$update_id."",1);
				if($rID) $flag=1; else $flag=0;
				//=======================================================================================================
			
				if($db_type==0)
				{
					if($flag==1 )
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
				if($db_type==2 || $db_type==1 )
				{
				  if($flag==1 )
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
		}
		
		if ($operation==2)  // Delete Here
		{
				$con = connect();
				if($db_type==0)
				{
					mysql_query("BEGIN");
				}
				$field_array="updated_by*update_date*status_active*is_deleted";
				$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
				$rID=sql_update("lib_store_location",$field_array,$data_array,"id","".$update_id."",1);
				if($db_type==0)
				{
					if($rID )
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
			
				if($db_type==2 || $db_type==1 )
				{	if($rID )
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

if ($action=="dynamic_letter_list_view")
{
	//echo "su..re"; die;
	
	?>
    <table width="270" cellpadding="0" cellspacing="0" border="0" class="rpt_table" rules="all">        
        <thead> 
            <tr>
                <th width="50">SL No</th>
                <th>Letter Type</th>
            </tr>
        </thead>
    </table>
    <div style="max-height:200px; width:270px; overflow-y:scroll" id="">
        <table id="list_view" width="250" height="" cellpadding="0" cellspacing="0" border="0" class="rpt_table" rules="all">
            <tbody>
            <?php 
			$array=sql_select("select id, letter_type, letter_body from dynamic_letter where status_active=1 and is_deleted=0");
			$letter_type_arr=array(1=>"Import Document Acceptance", 2=>"Import Lien Letter", 3=>"Export Lien Letter");
			$sl=0;
			foreach($array as $row)
			{
				$sl++;
				?>
                <tr id="tr_<?php echo $sl; ?>" height="20" bgcolor="<?php echo ($sl%2==0)?"#E9F3FF":"#FFFFFF"; ?>" onclick="fnc_set_value('<?php echo $sl; ?>')" style="cursor:pointer;"> 
                	<td width="50"><?php echo $sl; ?>
                    <input type="hidden" id="txt_hidden<? echo $sl; ?>" value="<? echo $row[csf('letter_body')]; ?>" />
                    </td>
                    <td><?php echo $letter_type_arr[$row[csf('letter_type')]]; ?></td>
                </tr>
                <?php 
			}
			?>            
            </tbody>
    </table>
    <?php
	exit();
}
?>