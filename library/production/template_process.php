<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Color List
Functionality	:
JS Functions	:
Created by		:	Ashraful
Creation date 	: 	17-02-2014
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
echo load_html_head_contents("Template Process Info", "../../", 1, 1,$unicode,1,'');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<?=$permission; ?>';
	
	function template_process_info( operation )
	{
		if (form_validation('txt_template_name','Template Name')==false)
		{
			// release_freezing();
			return;
		}
		else
		{
			
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_template_name*txt_process_name*txt_process_id*txt_process_seq*cbo_status*update_id',"../../");
			http.open("POST","requires/template_process_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = template_process_info_reponse;
		}
	}
	
	function template_process_info_reponse()
	{
		if(http.readyState == 4)
		{
			//alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			//alert(reponse[0])
			show_msg(reponse[0]);
			
			if(reponse[0]==1 || reponse[0]==0)
			{
				$('#txt_template_name').removeAttr('disabled','disabled');
				$('#cbo_status').removeAttr('disabled','disabled');
				show_list_view('','template_process_list_view','template_process_list_view','requires/template_process_controller','setFilterGrid("list_view",-1)');
				
				set_button_status(0, permission, 'template_process_info',1);
				
				reset_form('templateprocessinfo_1','','');
				document.getElementById('usedmsg').innerHTML ='';
			}
			
			release_freezing();
		}
	}
	function openmypage_process()
	{
		
		var txt_process_id = $('#txt_process_id').val();
		var txt_process_seq = $('#txt_process_seq').val();


		var title = 'Process Name Selection Form';
		var page_link = 'requires/template_process_controller.php?txt_process_id='+txt_process_id+'&process_seq='+txt_process_seq+'&action=process_name_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var process_id=this.contentDoc.getElementById("hidden_process_id").value;	 //Access form field with id="emailfield"
			var process_name=this.contentDoc.getElementById("hidden_process_name").value;
			var process_seq=this.contentDoc.getElementById("hidden_process_seq").value;
			//alert(process_seq);

			$('#txt_process_id').val(process_id);
			$('#txt_process_name').val(process_name);
			$('#txt_process_seq').val(process_seq);
		}
	}
</script>
</head>
<body  onload="set_hotkey()">
<? echo load_freeze_divs ("../../",$permission);  ?>
    <div align="center" style="width:100%;">
    <fieldset style="width:500px;">
        <legend>Template Process Info</legend>
        <form name="templateprocessinfo_1" id="templateprocessinfo_1"  autocomplete="off">
            <table cellpadding="0" cellspacing="2" width="500px">
                <tr>
                    <td width="80" class="must_entry_caption">Template Name</td>
                    <td width="150"><input type="text" name="txt_template_name" id="txt_template_name" class="text_boxes" style="width:250px" /></td> 
                </tr>
				<tr>
					<td>Status</td>
					<td  colspan="2">
						<? 
							echo create_drop_down( "cbo_status", 262, $row_status,'', '', '', 1 );
						?>
					</td>
				</tr>
                <tr>
                    <td class="must_entry_caption">Process Name</td>
                    <td>
							<input type="text" name="txt_process_name" id="txt_process_name" class="text_boxes" style="width:250px;" placeholder="Double Click To Search" onDblClick="openmypage_process();" tabindex="13" readonly />
							<input type="hidden" name="txt_process_id" id="txt_process_id" value="" />
							<input type="hidden" name="txt_process_seq" id="txt_process_seq" value="" />
                    </td>
                </tr>
                <tr>
                    <td colspan="4" align="center" class="button_container">
						<?=load_submit_buttons( $permission, "template_process_info", 0,0 ,"reset_form('templateprocessinfo_1','','')",1); ?>
                        <input type="hidden" name="update_id" id="update_id" >
                    </td>
                </tr>
                <tr>
                    <td colspan="4" id="template_process_list_view">
						<?
                        $arr=array (2=>$row_status); 
                        echo create_list_view ( "list_view","Template Name,Process Name,Status", "160,200,140","500","220",0, "select template_name, process_name,id, status_active from lib_template_process_mst where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,status_active", $arr , "template_name,process_name,status_active", "requires/template_process_controller", 'setFilterGrid("list_view",-1);' ) ;//\\192.168.11.252\logic_erp_3rd_version\prod_planning\cutting_plan\requires
                        ?>
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>
	</div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>
