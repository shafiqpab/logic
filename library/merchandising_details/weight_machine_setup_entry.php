<?php
/*-------------------------------------------- Comments
Purpose			: 	This form will create Weight Machine Setup Entry
Functionality	:	
JS Functions	:
Created by		:	Md Mamun Ahmed Sagor
Creation date 	: 	04-10-2021
Updated by 		: 	
Update date		: 	   
QC Performed BY	:		
QC Date			:	
Comments		:
*/





	session_start();
	if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
	require_once('../../includes/common.php');
	extract($_REQUEST);
	$_SESSION['page_permission']=$permission;
	//--------------------------------------------------------------------------------------------------------------------
	echo load_html_head_contents("Weight Machine Setup Entry", "../../", 1, 1,$unicode,1,'');
?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';


	function fnc_machine_setup( operation )
	{
		
		if (form_validation('cbo_company_name*txt_api_link','Company Name*Body Part Title Name')==false)
		{
			return;
		}
		else
		{
			
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_user_name*txt_api_link*cbo_status*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/weight_machine_setup_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_machine_setup_reponse;
		}
	}

	function fnc_machine_setup_reponse()
	{
		if(http.readyState == 4)
		{
		    //alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			//alert(reponse[0])
			show_msg(reponse[0]);
			show_list_view('','body_part_title_list_view','body_part_title_list_view','../merchandising_details/requires/weight_machine_setup_entry_controller','setFilterGrid("list_view",-1)');
			reset_form('bodyparttitle','','');
			if(reponse[0]==1 || reponse[0]==0)
			{
				$('#cbo_company_name').removeAttr('disabled','disabled');
				$('#txt_api_link').removeAttr('disabled','disabled');
			}
			set_button_status(0, permission, 'fnc_machine_setup',1);
			release_freezing();
		}
	}

</script>
</head>
<body  onload="set_hotkey()">
<? echo load_freeze_divs ("../../",$permission);  ?>
<div align="center" style="width:100%;">	
    
	<fieldset style="width:500px;">
		<legend>Body Part Title</legend>
		<form name="bodyparttitle" id="bodyparttitle"  autocomplete="off">	
			<table cellpadding="0" cellspacing="2" width="75%">
			 	<tr>
					<td width="109" class="must_entry_caption">Company Name</td>
					<td colspan="3">
						<?
                            echo create_drop_down( "cbo_company_name", 240, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "-- Select Company --", 0, "" );
						?>
					</td>
				</tr>
				<tr>
				<td width="109" class="must_entry_caption" >User Name</td>
				<td colspan="3">
                      
							<? 
                                echo create_drop_down( "cbo_user_name", 240, "select user_name,id from user_passwd where valid=1 order by user_name ASC",'id,user_name', 1, '--- Select User ---', 0, "" );
                            ?>
							 
				</td>
				</tr>
                <tr>
					<td width="109" title="http://192.168.11.199">Api Link</td>
					<td colspan="3" >
						<input type="text" name="txt_api_link" id="txt_api_link" class="text_boxes" style="width:230px" />                         
					</td>
				</tr>	
				<tr>
					<td width="109" class="must_entry_caption">Active Status</td>
					<td>
						
						<?
						$row_status=array(1=>"Active",2=>"InActive");
                        echo create_drop_down( "cbo_status", 110, $row_status,"", "", "", 1, "" );
						?>                        
					</td>
					
				</tr>		
			  	<tr>
					 <td colspan="3" align="center">&nbsp;						
						<input type="hidden" name="update_id" id="update_id">
					</td>					
				</tr>
				<tr>
					<td colspan="3" align="center" class="button_container">
						<? 
					     echo load_submit_buttons( $permission, "fnc_machine_setup", 0,0 ,"reset_form('bodyparttitle','','')",1);
				        ?> 
					</td>				
				</tr>	
			</table>
		</form>	
	</fieldset>	
	<div style="width:650px;" align="center">
		<fieldset style="width:500px;">
			<legend>Body Part Title List</legend>
			 
            	<table width="470" cellspacing="2" cellpadding="0" border="0">                     
					<tr>
						<td colspan="3" id="body_part_title_list_view">
							<?
							$com_arr=return_library_array( "select id, company_name from lib_company", 'id', 'company_name');
							$user_arr=return_library_array( "select id, user_name from user_passwd where valid=1 order by user_name ASC", 'id', 'user_name');
							$arr = array(0 => $com_arr,1 => $user_arr);							
							echo  create_list_view ( "list_view", "Company Name,User Name,Api Link", "150,75,225","500","220", 0, "select company_id,user_id,api_link,id from lib_weight_machine_setup where status_active=1 and is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "company_id,user_id,0,0", $arr, "company_id,user_id,api_link", "../merchandising_details/requires/weight_machine_setup_entry_controller", 'setFilterGrid("list_view",-1);' );
							?>
						</td>
					</tr>
				</table>
			 
		</fieldset>	
	</div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
