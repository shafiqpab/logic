<?php
/*-------------------------------------------- Comments ----------------------------------------
Version (MySql)          : 
Version (Oracle)         :  V1
Purpose			         :  This form will create Batcher Entry
Created by		         :  Md Mamun Ahmed Sagor
Creation date 	         :  23-03-2023
Requirment               :
Updated by 		         :
Update date		         :
QC Performed BY	         :
QC Date			         :
Comments		         :
*/


	session_start();
	if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
	require_once('../../includes/common.php');
	extract($_REQUEST);
	$_SESSION['page_permission']=$permission;
	//--------------------------------------------------------------------------------------------------------------------
	echo load_html_head_contents("Batcher Entry Information", "../../", 1, 1,$unicode,1,'');
 ?>
 <script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';


	function fnc_batcher_entry( operation )
	{
		
		if (form_validation('cbo_company_name*txt_batcher_name','Comany Name*Batcher Name')==false)
		{
			return;
		}
		else
		{


			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_location_id*txt_batcher_name*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/batcher_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_batcher_entry_reponse;
		}
	}

	function fnc_batcher_entry_reponse()
	{
		if(http.readyState == 4)
		{
		    //alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			//alert(reponse[0])

			if (reponse[0]==11) {
				alert("Duplicate Entry Found");
				release_freezing(); return;
			}
			show_msg(reponse[0]);
			show_list_view('','batcher_entry_list_view','batcher_entry_list_view','../merchandising_details/requires/batcher_entry_controller','setFilterGrid("list_view",-1)');
			reset_form('batchertitle','','');
			if(reponse[0]==1 || reponse[0]==0)
			{
				$('#cbo_company_name').removeAttr('disabled','disabled');
				$('#txt_batcher_name').removeAttr('disabled','disabled');
			}
			set_button_status(0, permission, 'fnc_batcher_entry',1);
			release_freezing();
		}
	}



 

</script>
</head>
<body  onload="set_hotkey()">
<? echo load_freeze_divs ("../../",$permission);  ?>
<div align="center" style="width:100%;">	
    
	<fieldset style="width:500px;">
		<legend>Batcher Entry Form</legend>
		<form name="batchertitle" id="batchertitle"  autocomplete="off">	
			<table cellpadding="0" cellspacing="2" width="75%">
			 	<tr>
					<td width="109" class="must_entry_caption">Company Name</td>
					<td colspan="3">
						<?
                            echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/batcher_entry_controller', this.value, 'load_drop_down_location', 'location_td');" );
						?>
					</td>
				</tr>
				<tr>
				<td >Location </td>
                <td align="left" id="location_td" colspan="3">
                    <?
                        echo create_drop_down( "cbo_location_id",150,$blank_array,"", 1, "--Select Location--", $selected, "","","","","","",2);
                    ?>
                </td>
				</tr>
                <tr>
					<td width="109" class="must_entry_caption">Batcher Name</td>
					<td colspan="3">
						<input type="text" name="txt_batcher_name" id="txt_batcher_name" class="text_boxes" style="width:140px" />                         
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
					     echo load_submit_buttons( $permission, "fnc_batcher_entry", 0,0 ,"reset_form('batchertitle','','')",1);
				        ?> 
					</td>				
				</tr>	
			</table>
		</form>	
	</fieldset>	
	<div style="width:650px;" align="center">
		<fieldset style="width:500px;">
			<legend>Batcher Title List</legend>
			 
            	<table width="470" cellspacing="2" cellpadding="0" border="0">                     
					<tr>
						<td colspan="3" id="batcher_entry_list_view">
							<?
							$com_arr=return_library_array( "select id, company_name from lib_company", 'id', 'company_name');
							$loc_arr=return_library_array( "select id, location_name from lib_location", 'id', 'location_name');
							$arr = array(0 => $com_arr,1 => $loc_arr);
							echo  create_list_view ( "list_view", "Company Name,Location,Batcher Name", "180,120,200","500","220", 0, "select company_id,batcher_name,location_id,id from lib_batcher where status_active=1 and is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "company_id,location_id, 0", $arr, "company_id,location_id,batcher_name", "../merchandising_details/requires/batcher_entry_controller", 'setFilterGrid("list_view",-1);' );
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
