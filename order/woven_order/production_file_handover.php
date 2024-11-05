<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create for Production File Handover
Functionality	:	
JS Functions	:
Created by		:	Al-Hassan
Creation date 	: 	30-08-2023
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
echo load_html_head_contents("Lapdip Approval", "../../", 1, 1,'','','');
?>	
 
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission='<? echo $permission; ?>';

function openmypage(page_link,title)
{
	var garments_nature=$('#garments_nature').val();
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'?action=order_popup&garments_nature='+garments_nature, title, 'width=990px,height=400px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_job");
		$('#hide_color_id').val('');
		if(theemail.value!="")
		{
			freeze_window(5);
			get_php_form_data(theemail.value, "populate_data_from_search_popup", "requires/production_file_handover_controller" );
		    show_list_view(theemail.value+'**'+0,'lapdip_approval_list_view_edit','lapdip_approval_list_view','requires/production_file_handover_controller','',0);
			release_freezing();
		}
	}
}
 
function fnc_comments(id,value)
{
	var page_link='requires/production_file_handover_controller.php?action=comments_popup&comments_data='+value;
	var title='Comments Info';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=440px,height=200px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var comments_data=this.contentDoc.getElementById("txt_comments").value;
		$('#'+id).val(comments_data);
	}
}

function fnc_production_handover( operation )
{
	if(operation==2)
	{
		alert("Delete Restricted");
		release_freezing();
		return;
	}
	if (form_validation('txt_job_no','Job No')==false)
	{
		return;
	}
	else
	{
		var txt_job_no = $("#txt_job_no").val();
		var row_num = $('#tbl_order_details tbody tr').length;
		var data1='';
		for(var i=1; i<=row_num; i++)
		{
			// var po_id = $(this).find("input.file_handover_check").val();
	        // alert(po_id);return;
			var target_app_date = $("#target_app_date_"+i).val();
			var submitted_to_buyer = $("#submitted_to_buyer_"+i).val();
			var action_date = $("#action_date_"+i).val();
			var action = $("#action_"+i).val();
			if(action == 1){
				if(target_app_date == ''){
					$("#target_app_date_"+i).html("Please not empty");
					alert("Target date not empty");
					return;
				}
				if(submitted_to_buyer == ''){
					alert("Submitted to buyer date not empty");
					return;
				}
				if(action_date == ''){
					alert("Action date not empty");
					return;
				}
			}
			data1+=get_submitted_data_string('po_no_'+i+'*target_app_date_'+i+'*submitted_to_buyer_'+i+'*action_'+i+'*action_date_'+i+'*txt_comments_'+i+'*cbo_status_'+i+'*updateid_'+i,"../../",2);
		}
		var data="action=save_update_delete&operation="+operation+data1+"&tot_row="+row_num+"&job_no="+txt_job_no;
		freeze_window(operation);
		http.open("POST","requires/production_file_handover_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_lapdip_approval_reponse;
	}
}
	 
function fnc_lapdip_approval_reponse()
{
	if(http.readyState == 4) 
	{
		var response=trim(http.responseText).split('**');
		show_msg(trim(response[0]));
		// get_php_form_data(response[1], "populate_data_from_search_popup", "requires/production_file_handover_controller" );
		// show_list_view(response[1]+'**'+0,'lapdip_approval_list_view_edit','lapdip_approval_list_view','requires/production_file_handover_controller','',0);
		set_button_status(1, permission, 'fnc_production_handover',1);
		release_freezing();
	}
} 

function set_checkvalue()
{
	var selected = $(".selected").val();
	if (selected==1) {
		$(".selected").val(0);
		$('.file_handover_check').prop('checked', false);
	} else {
		$(".selected").val(1);
		$('.file_handover_check').prop('checked', true);
	}
}
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
     <?= load_freeze_divs ("../../",$permission); ?>
    <form id="lapdipapproval_1">
        <fieldset style="width:900px;">
		    <legend>Production File Handover</legend>
        	<table width="880" cellspacing="2" cellpadding="0" border="0">
				<tr>
					<td width="80" align="right" class="must_entry_caption"> Job No </td>  
                    <td width="170">
                        <input style="width:160px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/production_file_handover_controller.php','Job/Order Selection Form')" class="text_boxes" autocomplete="off" placeholder="Search Job" name="txt_job_no" id="txt_job_no" readonly/>
                    </td>
                    <td width="120" align="right">Company Name </td>
                    <td width="170">
						<?= create_drop_down("cbo_company_name", 172, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "Display", $selected, "",1);?> 
                    </td>
                    <td width="120" align="right">Location Name</td>
                    <td>
                        <?= create_drop_down( "cbo_location_name", 172, "select id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "Display", $selected, "",1 );?>	
                    </td>
                </tr>
                <tr>
                    <td align="right">Buyer Name</td>
                    <td>
                        <?= create_drop_down("cbo_buyer_name", 172, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id order by buyer_name","id,buyer_name", 1, "Display", $selected, "" ,1);?>	  
                    </td>
                    <td align="right">Style Ref.</td>
                    <td>
                        <input class="text_boxes" type="text" style="width:160px" disabled placeholder="Display" name="txt_style_ref" id="txt_style_ref"/>	
                    </td>
                    <td align="right">Style Description</td>
                    <td>	
                        <input class="text_boxes" type="text" style="width:160px;" name="txt_style_description" id="txt_style_description" placeholder="Display" disabled/>
                    </td>
                </tr>
                <tr>
                    <td align="right">Pord. Dept.</td>   
                    <td>
                        <?= create_drop_down("cbo_product_department", 172, $product_dept, "",1, "Display", $selected, "" ,1);?>
                    </td>
                    <td align="right">Currency</td>
                    <td>
                        <?= create_drop_down("cbo_currercy", 172, $currency, "", 1, "Display", "", "",1);?>	  
                    </td>
                    <td align="right">Agent</td>
                    <td>
						<?= create_drop_down( "cbo_agent", 172, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id  and a.id in (select  buyer_id from lib_buyer_party_type where party_type in (20,21)) order by buyer_name","id,buyer_name", 1, "Display", $selected, "",1 );?>
                    </td>
                </tr>
                <tr>
                    <td  align="right">Region</td>
                    <td>
                        <?= create_drop_down("cbo_region", 172, $region, "",1, "Display", $selected, "",1);?>	  
                    </td>
                    <td align="right">Team Leader</td>   
                    <td>
                        <?= create_drop_down("cbo_team_leader", 172, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "Display", $selected, "",1);?>		
                    </td>
                    <td align="right">Dealing Merchant</td>   
                    <td> 
                        <?= create_drop_down("cbo_dealing_merchant", 172, "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "Display", $selected, "",1);?>	
                   </td>
                </tr>
				<tr>
                    <td></td>
                    <td></td>
                    <td></td>   
                    <td> </td>
                    <td align="right"></td>   
                    <td> 
					<input type="button" class="image_uploader" style="width:172px" value="ADD FILE" onClick="file_uploader ( '../../', document.getElementById('txt_job_no').value,'', 'lapdip_app', 2 ,1)">	
                   </td>
                </tr>
                <tr>
                	<td colspan="8" id="lapdip_approval_list_view"></td>
                </tr>
                <tr>
                    <td align="center" colspan="10" valign="middle" class="button_container">
                        <?= load_submit_buttons($permission, "fnc_production_handover", 1,0 ,"reset_form('lapdipapproval_1','','','','lapdip_reset()')",1) ; 
					    ?>
                    </td>
                </tr>
            </table>
		</fieldset>
	</form>        
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>