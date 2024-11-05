<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];




if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		if (is_duplicate_field( "cause_name", "lib_category_wise_causes_entry", " cause_name=$cbo_npt_cause and status_active=1 and is_deleted=0" ) == 1)
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
			$id=return_next_id( "id", "lib_category_wise_causes_entry", 1 ) ;
			$field_array="id, CATEGORY_ID, CAUSE_NAME, inserted_by, insert_date, status_active, is_deleted";
			$data_array="(".$id.",".$cbo_npt_category.",".$cbo_npt_cause.",'".$user_id."','".$pc_date_time."',".$cbo_status.",0)"; 
			$rID=sql_insert("lib_category_wise_causes_entry",$field_array,$data_array,1);
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
	
	else if ($operation==1)   // Update here------------------------------------------Update here------------------------------------------
	{
		if (is_duplicate_field( "CAUSE_NAME", "LIB_CATEGORY_WISE_CAUSES_ENTRY", " CAUSE_NAME=$cbo_npt_cause and id<>$update_id and status_active=1 and is_deleted=0" ) == 1)
		{
			echo "11**0"; die;
		}
		else
		{
			
			$field_array="CATEGORY_ID*CAUSE_NAME*updated_by*update_date*status_active*is_deleted";
			$data_array="".$cbo_npt_category."*".$cbo_npt_cause."*'".$user_id."'*'".$pc_date_time."'*".$cbo_status."*0"; 
			$rID=sql_update("LIB_CATEGORY_WISE_CAUSES_ENTRY",$field_array,$data_array,"id","".$update_id."",1);
		
			
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
		
	}
	else if ($operation==2)
	{
		$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$field_array="updated_by*update_date*status_active*is_deleted";
			$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
			
			$rID=sql_delete("LIB_CATEGORY_WISE_CAUSES_ENTRY",$field_array,$data_array,"id","".$update_id."",1);
		
			
			
			
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

if ($action=="report_settings_cause_type")
{
	
	
	
	
	 $sql="select ID, CATEGORY_ID,CAUSE_NAME,STATUS_ACTIVE from LIB_CATEGORY_WISE_CAUSES_ENTRY where is_deleted=0 order by CAUSE_NAME";
	// echo $sql;die;

	?>
    	<div style="width:auto;" align="center" id="cause_type_list">
    	<fieldset>
    	<div style="width:620px;">
        <table align="left" width="600" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">
    		<thead>
            	<th width="35" align="center">SL</th>
            	<th width="200">Category</th>
            	<th width="200">Causes</th>
            	<th>Status</th>
        	</thead>
        </table> 
        </div>
        
   		<div style="overflow-y:scroll; max-height:260px; width:620px;"  align="center">
     	<table align="left" id="tbl_task_list" width="600" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">  
    	<?
		
		$i=0;
		$data_array=sql_select( $sql );
		foreach($data_array as $data)
		{
			$i++;
			$bgcolor=( $i%2==0 ? "#E9F3FF" : "#FFFFFF" );
		?>
        	<tbody>
         		<tr bgcolor="<? echo $bgcolor; ?>"style="cursor:pointer" onclick="get_php_form_data(<? echo $data['ID']; ?>,'set_update_form_data','requires/category_wise_cause_entry_controller')">
            		<td  align="center" width="35"><? echo $i; ?></td>
            		<td width="200"><? echo  $npt_category[$data['CATEGORY_ID']]; ?></td>
            		<td width="200"><? echo $npt_cause[$data['CAUSE_NAME']]; ?></td>
            		<td align="center"><? echo $row_status[$data['STATUS_ACTIVE']]; ?></td>
         		</tr>
         	</tbody>
    	<?
		}
		?>
    
   		</table>
   		</div>
    	</fieldset>
    	</div>
     
	<? 
}
 


if ($action=="cause_type_list")
{ 
	if($data==1) $cause_type_arry=[1,2,3,4,5,164,165,166,167,168,169,170,171,172,173,174,175,176,177,179,180,181,182,183,184,280,281,282,283,315];
	else if($data==2) $cause_type_arry=[1,6,7,8,9,10,11,12,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230];
	else if($data==3) $cause_type_arry=[1,13,14,15,16,52,53,210,211,212,213,214,215,297,298];
	else if($data==4) $cause_type_arry=[1,17,18,19];
	else if($data==5) $cause_type_arry=[1,20,21,110,111,112,231,232,233,234,235,289,316];
	else if($data==6) $cause_type_arry=[1,22,23,247,248,249,250,251,252,253,254,285];
	else if($data==7) $cause_type_arry=[1,24,25];
	else if($data==8) $cause_type_arry=[26,27,28,29];
	else if($data==9) $cause_type_arry=[1,30,31,32,33,34,35,3];
	else if($data==10) $cause_type_arry=[1,37,38,39,40,41];
	else if($data==11) $cause_type_arry=[42,43,44];
	else if($data==12) $cause_type_arry=[45,46,47];
	else if($data==13) $cause_type_arry=[48,49,50];
	else if($data==14) $cause_type_arry=[53,54,55,56,113,114,115,255,256,257,258,259,266,267,268,269,270,271,272,273,274,275,276,277,278,279];
	else if($data==15) $cause_type_arry=[57,58,59,60,61,62,63,185,186,187,188,189,190,191,192,193,194,195,196,197];
	else if($data==16) $cause_type_arry=[64,65,66,67];
	else if($data==17) $cause_type_arry=[68,69,70,71,72,206,207,208,209];
	else if($data==18) $cause_type_arry=[73,74,75,76,77,78,246,299,300];
	else if($data==19) $cause_type_arry=[79,58,80,81,82,83];
	else if($data==20) $cause_type_arry=[84,85,86,87,88,198,199,200,201,202,203,204,205];
	else if($data==21) $cause_type_arry=[89,90,91,92];
	else if($data==22) $cause_type_arry=[93,94,301,302];
	else if($data==23) $cause_type_arry=[95,96];
	else if($data==24) $cause_type_arry=[97,98,99];
	else if($data==25) $cause_type_arry=[100,101,102,103,104];
	else if($data==26) $cause_type_arry=[105,106,107,108];
	else if($data==28) $cause_type_arry=[286,287,288,108,316,317];
	else if($data==29) $cause_type_arry=[293,294,295,296];
	else if($data==30) $cause_type_arry=[243,244,245,291,292]; 
	else if($data==31) $cause_type_arry=[283,284]; 
	else if($data==32) $cause_type_arry=[236,237,238,239,240,241,242]; 
	else if($data==99) $cause_type_arry=[109,260,261,262,263,264,265,303];
	else if($data==27) $cause_type_arry=[116,117,118,119,120,121,122,123,124,125,126];
	else if($data==100) $cause_type_arry=[304,305,306,307,290];
	else if($data==101) $cause_type_arry=[308,309,310,311,312];
	else if($data==102) $cause_type_arry=[313,314];
	// echo "<pre>";print_r($cause_type_arry);die; 
	$save_cause_type_arr = return_library_array("select CAUSE_NAME,STATUS_ACTIVE from LIB_CATEGORY_WISE_CAUSES_ENTRY where is_deleted=0","CAUSE_NAME","STATUS_ACTIVE");
	
	?>
        
    <span style="background:#33CC00; padding:0 7px; border-radius:9px; cursor:pointer;"></span>&nbsp; Active&nbsp;                 
    <span style="background:#FFF000; padding:0 7px; border-radius:9px; cursor:pointer;"></span> &nbsp;Inactive &nbsp;                 
    <span style="background:#FF0000; padding:0 7px; border-radius:9px; cursor:pointer;"></span> &nbsp;Cancelled                 
        
        <fieldset>
			<div style="width:250px;">
				<table align="left" width="250" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">
					<thead>
						<th width="35" align="center">ID</th>
						<th>Cause List</th>
					</thead>
				</table> 
			</div>
			<div style="overflow-y:scroll; max-height:410px; width:270px;"  align="center">
				<table align="left" id="tbl_cause_type_list" width="250" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">  
					<?
					$i=0;
					foreach($cause_type_arry as $npt_type)
					{
					if ($npt_cause[$npt_type]) 
					{	
							$i++;
							$bgcolor=( $i%2==0 ? "#E9F3FF" : "#FFFFFF" );
							$onclickFunction=$onclickFunction='onclick="alert(\'Duplicate Select Not Allowed.\');"';
							
							$npt_cause2 = "'".$npt_cause."'";
							if($save_cause_type_arr[$npt_type]==1){$bgcolor="#33CC00";}
							else if($save_cause_type_arr[$npt_type]==2){$bgcolor="#FFF000";}
							else if($save_cause_type_arr[$npt_type]==3){$bgcolor="#FF0000";}
							$onclickFunction='onclick="fn_set_cause_type('.$npt_type.');"';
							
							?>
								<tbody>
									<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer" <? echo $onclickFunction;?>>
										<td  align="center" width="35"><? echo $npt_type; ?></td>
										<td><? echo $npt_cause[$npt_type]; ?></td>
									</tr>
								</tbody>
							<?
					}
					}
					?>
				</table>
			</div>
    	</fieldset>
		<?
	
}



if ($action=="set_update_form_data")
{
	
	$data_arr=sql_select("select ID, CATEGORY_ID,CAUSE_NAME,STATUS_ACTIVE from LIB_CATEGORY_WISE_CAUSES_ENTRY where id='$data'");
	// echo $data_arr;die;
	foreach ($data_arr as $row)
	{
		echo "document.getElementById('cbo_npt_category').value='".$row['CATEGORY_ID']."';\n";
		echo "document.getElementById('cbo_npt_cause').value='".$row['CAUSE_NAME']."';\n";
		echo "document.getElementById('cbo_status').value='".$row['STATUS_ACTIVE']."';\n";
		echo "document.getElementById('update_id').value='".$row['ID']."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."','fnc_category_wise_cause_entry',1);\n"; 
	}
}

?>
