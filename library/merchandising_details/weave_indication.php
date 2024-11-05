<?
/*-------------------------------------------- Comments

Purpose			       	: 	This form will create WEAVE INDICATION					
Functionality			 	:	
JS Functions				:
Created by					:		Md. Helal Uddin 
Creation date 			: 	27-10-2022
Updated by 					: 		
Update date					: 		   
QC Performed BY			:		
QC Date							:	
Comments						:

*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("WEAVE INDICATION", "../../", 1, 1,$unicode,1,'');
?>	
	<script>

		if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
		var permission='<? echo $permission; ?>';

		function fnc_weave_info( operation )
		{
			if (form_validation('txt_weave*txt_code','Weave * Code')==false)
			{
				return;
			}
			else
			{
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_weave*txt_code*cbo_status*update_id',"../../");
				//alert(data);
				freeze_window(operation);
				http.open("POST","requires/weave_indication_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_weave_info_reponse;
			}
		}

		function fnc_weave_info_reponse()
		{
			if(http.readyState == 4) 
			{  
			
				var reponse=trim(http.responseText).split('**');
				//alert(reponse[0])
				show_msg(reponse[0]);
				show_list_view('','weave_list_view','weave_list_view','../merchandising_details/requires/weave_indication_controller','setFilterGrid("list_view",-1)');
				reset_form('weaveinfo_1','','');
				set_button_status(0, permission, 'fnc_weave_info',1);
				release_freezing();
			}
		}
	</script>
</head>
<body  onload="set_hotkey()">
	<? echo load_freeze_divs ("../../",$permission);  ?>
	<div align="center" style="width:100%;">	
		<fieldset style="width:500px;">
			<legend>WEAVE INDICATION</legend>
			<form name="weaveinfo_1" id="weaveinfo_1"  autocomplete="off">	
				<table cellpadding="0" cellspacing="2" width="500px">
				 	<tr>
						<td width="200" class="must_entry_caption">Weave</td>
						<td >
							<input type="text" name="txt_weave" id="txt_weave" class="text_boxes" style="width:150px" />
						</td>
	          <td width="150" class="must_entry_caption">Code</td>
						<td >
							<input type="text" name="txt_code" id="txt_code" class="text_boxes" style="width:150px" />
						</td>
	        </tr>
	      
	        <tr>
	            <td width="50" >Status</td>
	            <td >
								 <?
								 echo create_drop_down( "cbo_status", 165, $row_status,'', $is_select, $select_text, 1, $onchange_func, '','','','',3 );
								 ?>
							</td>        
	        </tr>
	        <tr>
						<td colspan="4" align="center" class="button_container">
								<? 
						     	echo load_submit_buttons( $permission, "fnc_weave_info", 0,0 ,"reset_form('weaveinfo_1','','')",1);
					      ?> 
	            	<input type="hidden" name="update_id" id="update_id" >
						</td>				
					</tr>
	        <tr>
							<td colspan="4" align="center" id="weave_list_view">
								<?
								$arr=array (2=>$row_status);
								echo  create_list_view ( "list_view", "Weave,code,Status", "200,100,100","450","220",0, "select  weave,code,status_active,id from   lib_weave_indication where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,status_active", $arr , "weave,code,status_active", "../merchandising_details/requires/weave_indication_controller", 'setFilterGrid("list_view",-1);' ) ;
								 ?>
							</td>
					</tr>
			  </table>
			</form> 
		</fieldset>	
	</div>
</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
