<?php
/******************************************************************
|	Purpose			:	This form will create Composition Entry
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Md. Helal Uddin
|	Creation date 	:	23-02-2021
|	Updated by 		: 		
|	Update date		:    
|	QC Performed BY	:		
|	QC Date			:	
|	Comments		:
********************************************************************/


session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Composition Entry", "../../", 1, 1,$unicode,1,'');
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	function fnc_composition_entry( operation )
	{
		if (form_validation('txt_composition_name','Composition Name')==false)
		{
			return;
		}
		else
		{
			if(operation==1 || operation==2)
			{
				var update_id=$('#update_id').val();
				var status_id=$('#cbo_status').val();
				var response=trim(return_global_ajax_value( update_id, 'check_composition', '', 'requires/composition_entry_controller_v2'));
				var response=response.split("_");
				
				if(status_id!=2)
				{
					if(response[0]==1)
					{
							alert("This composition is already used another page");
							return;
					}
				}
				
			}
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_composition_name*cbo_status*update_id*cbo_yarn_fibre_type*cbo_yarn_fibre',"../../");
			freeze_window(operation);
			http.open("POST","requires/composition_entry_controller_v2.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_composition_entry_reponse;
		}
	}
	
	function fnc_composition_entry_reponse()
	{
		if(http.readyState == 4) 
		{  
		
		//alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			//alert(reponse[0])
			show_msg(reponse[0]);
			show_list_view('','composition_list_view','composition_list_view','requires/composition_entry_controller_v2','setFilterGrid("list_view",-1)');
			reset_form('compositionentry_1','','');
			set_button_status(0, permission, 'fnc_composition_entry',1);
			release_freezing();
		}
	}
</script>
</head>
<body  onload="set_hotkey()">
	<? echo load_freeze_divs ("../../",$permission);  ?>
    <div align="center" style="width:100%;">	
		<form name="compositionentry_1" id="compositionentry_1"  autocomplete="off">
            <fieldset style="width:650px;"><legend>Composition Entry</legend>
			<table cellpadding="0" cellspacing="2" width="620px">
			 	<tr>
					<td width="100" align="left" class="must_entry_caption">Composition Name</td>
					<td colspan="3">
						<input type="text" name="txt_composition_name" id="txt_composition_name" class="text_boxes" style="width:437px"/>
					</td>
					 
                </tr>
                <tr>
					
					<td  align="left" >Yarn Fibre Type </td>
					<td valign="top" >
						<?
							
							echo create_drop_down( "cbo_yarn_fibre_type", 160, $yarn_fibre_type_arr,"", 1, "--Select--", 0, "" );
						?>	

					</td>
                    <td  align="left">Yarn Fibre </td>
					<td valign="top" >
						
                       <?
							
							echo create_drop_down( "cbo_yarn_fibre", 160, $yarn_fibre_arr,"", 1, "--Select--", 0, "" );
						?>
					</td>
				</tr>
				
                <tr>
                    <td align="left">Status</td>
                    <td ><? echo create_drop_down( "cbo_status", 160, $row_status,'', $is_select, $select_text, 1, $onchange_func, '','','','',3 ); ?></td>
                    <td colspan="2"></td>
                   
                </tr>
                <tr>
                    <td colspan="4">&nbsp;<input type="hidden" id="update_id" name="update_id" class="text_boxes" readonly /></td>
                </tr>
                <tr>
                	
                	
                    <td colspan="4" align="center" style="padding-top:10px;" class="button_container">
                        <?php echo load_submit_buttons( $permission, "fnc_composition_entry", 0, 0,"reset_form('compositionentry_1','','','','','')"); ?>						
                    </td>
                </tr>
		   </table>
		</fieldset>	
        <fieldset style="width:750px;">
            <div id="composition_list_view">
				<?php
                $arr=array (1=>$yarn_fibre_arr,2=>$yarn_fibre_type_arr,3=>$row_status);
                echo  create_list_view ( "list_view", "Composition Name,Yarn Fibre,Fibre Type,Status", "350,100,100,100","750","220",0, "select id, composition_name, status_active , yarn_fibre,yarn_fibre_type from lib_composition_array where status_active in(1,2) and is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,yarn_fibre,yarn_fibre_type,status_active", $arr, "composition_name,yarn_fibre,yarn_fibre_type,status_active", "requires/composition_entry_controller_v2", 'setFilterGrid("list_view",-1);','0,0');
                ?>
            </div>
        </fieldset>
	</form>
	</div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
