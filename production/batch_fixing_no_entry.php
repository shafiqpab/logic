<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create No Of Batch Fixing Entry
				
Functionality	:	
JS Functions	:
Created by		: Aziz
Creation date 	: 	 21-3-16
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("No Of Batch Fixing Info","../", 1, 1, $unicode,'','');

?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
 
	function openmypage_batch_plan_no(page_link,title)
	{
		page_link=page_link;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=470px,center=1,resize=1,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];;
			var save_data=this.contentDoc.getElementById("txt_save_data");
			var fab_desc=this.contentDoc.getElementById("txt_fab_desc");
			if (save_data.value!="")
			{
				freeze_window(5);
				document.getElementById('txt_save_data').value=save_data.value;
				document.getElementById('txt_descrp_no').value=fab_desc.value;
				release_freezing();
			}
		}
	}
 
	function fnc_batch_fixing_entry( operation )
	{
		if (form_validation('txt_descrp_no*txt_no_of_batch','Description*No Of batch')==false)
		{
			return;  
		}	
		else
		{
			//var from_date = $('#txt_from_date').val();
			//var to_date = $('#txt_to_date').val();
			//var datediff = date_diff( 'd', from_date, to_date )+1;
			//alert (datediff);return;
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_descrp_no*txt_no_of_batch*txt_update_id*txt_save_data',"../");//+'&datediff='+datediff
			//alert(data);
			freeze_window(operation);
			http.open("POST","requires/batch_fixing_no_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_fnc_batch_fixing_entry_reponse;
		}
	}
	 
function fnc_fnc_batch_fixing_entry_reponse()
{
	if(http.readyState == 4) 
	{
		//alert(http.responseText);
		var response=http.responseText.split('**');
		if(response[0]==0 || response[0]==1)
		{
		document.getElementById('txt_update_id').value = response[1];
		show_msg(trim(response[0]));
		set_button_status(1, permission, 'fnc_batch_fixing_entry',1); 
		release_freezing();
		}
		if(response[0]==2)
		{
		show_msg(trim(response[0]));
		set_button_status(0, permission, 'fnc_batch_fixing_entry',1); 
		reset_form('','','txt_update_id*txt_no_of_batch*txt_save_data*txt_descrp_no','','');
		release_freezing();
		}
		if(response[0]==11)
		{
		show_msg(trim(response[0]));
		set_button_status(0, permission, 'fnc_batch_fixing_entry',1); 
		reset_form('','','txt_update_id*txt_no_of_batch*txt_save_data*txt_descrp_no','','');
		release_freezing();
		}
		else
		{
			show_msg(trim(response[0]));
			release_freezing();	
		}

	}
}

</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
        <div style="width:350px;" align="center">
             <? echo load_freeze_divs ("../",$permission);  ?>
        </div>
        <fieldset style="width:350px"> 
        <legend>Production Module</legend>
        <form name="batchfixing_1" id="batchfixing_1" action=""  autocomplete="off">
        	<fieldset>
            	<table width="100%">
                    <tr>
                        <td width="130" class="must_entry_caption">Fabric Description</td>
                        <td width="170" >
                            <input type="text" name="txt_descrp_no" id="txt_descrp_no" class="text_boxes" style="width:170px" placeholder="Double Click to Search" onDblClick="openmypage_batch_plan_no('requires/batch_fixing_no_entry_controller.php?action=batch_plan_no_search_popup','Description No Search')" readonly/>
                            <input type="hidden" name="txt_update_id" id="txt_update_id" class="text_boxes"  style="width:50px" readonly >
                             <input type="hidden" name="txt_save_data" id="txt_save_data" class="text_boxes"  style="width:50px" readonly >
                         </td>
                    </tr>
                    <tr>
                         <td width="130" class="must_entry_caption">No Of Batch </td>
                         <td width="170">
                          <input name="txt_no_of_batch" id="txt_no_of_batch" class="text_boxes_numeric"  style="width:170px"  />                      </td>
                    </tr>
                    <tr>
                            <td align="center" colspan="4" valign="middle" class="button_container">
                                <?
                                echo load_submit_buttons( $permission, "fnc_batch_fixing_entry", 0,0 ,"reset_form('batchfixing_1','cause_of_machine_idle_list_view','','','')",1); 
                                ?>
                                <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly >	
                            </td>
                                                
                        </tr>
                    </table>
                    </fieldset>
                                       
                	<div style="width:450px; margin-top:5px;"  id="cause_of_machine_idle_list_view" align="center"></div>
          </form>
        </fieldset>
    </div>
</body> 
<script src="../includes/functions_bottom.js" type="text/javascript"></script>  
</html>