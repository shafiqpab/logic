<?php
/*-------------------------------------------- Comments ----------------------------------------
Version (MySql)          : 
Version (Oracle)         :  V1
Purpose			         :  This form will create Batcher Entry
Created by		         : Shajib Jaman
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
		
		if (form_validation('cbo_style_type*cbo_cutting_no','Style Type*Cutting No')==false)
		{
			return;
		}
		else
		{


			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_style_type*cbo_cutting_no*txt_cutting_target*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/fabric_cutting_planning_controller.php",true);
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
			show_list_view('','batcher_entry_list_view','batcher_entry_list_view','../planning/requires/fabric_cutting_planning_controller','setFilterGrid("list_view",-1)');
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
    
	<fieldset style="width:900px;">
		<legend>Fabric Cutting Planning as Sewing Plan</legend>
		<form name="batchertitle" id="batchertitle"  autocomplete="off">	
			<table cellpadding="0" cellspacing="2" width="100%">
            <tr>
					<td colspan="2" valign="top">
						<table  cellpadding="0" cellspacing="2" width="100%">
			 				<tr>
								<td width="140" class="must_entry_caption">Style Types</td>
								<td width="170"> <?  
								   $style_type=array(1=>"Solid",2=>"Stripe",3=>"Printed",4=>"Embroidery",5=>"Print & Embroidery",6=>"Lay Wash",7=>"Stripe & Printed"); 
                                   echo create_drop_down( "cbo_style_type", 155, $style_type,"", 1, "-- Select --", 0, "" );
                                ?></td>
								<td width="130" class="must_entry_caption">Cutting Will Start Before</td>
								
                                <td width="170"> <?  
								   $cutting_no=array(1=>"1",2=>"2",3=>"3",4=>"4",5=>"5",6=>"6",7=>"7",8=>"8",9=>"9",10=>"10",11=>"11",12=>"12",13=>"13",14=>"14",15=>"15",16=>"16",17=>"17",18=>"18",19=>"19",20=>"20"); 
                                   echo create_drop_down( "cbo_cutting_no", 155, $cutting_no,"", 1, "-- Select --", 0, "" );
                                ?></td>
								<td width="130">Cutting Target Over Plan(%)</td>
								 <td> 
                            <input type="text" name="txt_cutting_target" id="txt_cutting_target"  class="text_boxes_numeric" style="width:155px" /> 
                            </td> 								
							</tr>	 
							
			    		</table>
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
							 $style_type=array(1=>"Solid",2=>"Stripe",3=>"Printed",4=>"Embroidery",5=>"Print & Embroidery",6=>"Lay Wash",7=>"Stripe & Printed"); 

							 $cutting_no=array(1=>"1",2=>"2",3=>"3",4=>"4",5=>"5",6=>"6",7=>"7",8=>"8",9=>"9",10=>"10",11=>"11",12=>"12",13=>"13",14=>"14",15=>"15",16=>"16",17=>"17",18=>"18",19=>"19",20=>"20"); 

							$arr = array(0 => $style_type,1 => $cutting_no);
							echo  create_list_view ( "list_view", "Style Types,Cutting Will Start Before,Cutting Target Over Plan(%)", "160,140,200","500","220", 0, "select style_id,cutting_id,cutting_target,id from lib_fabric_cutting_planning where status_active=1 and is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "style_id,cutting_id, 0", $arr, "style_id,cutting_id,cutting_target", "../planning/requires/fabric_cutting_planning_controller", 'setFilterGrid("list_view",-1);' );
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
