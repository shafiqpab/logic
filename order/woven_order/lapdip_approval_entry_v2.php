<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Knit Garments Labdip Approval v2
Functionality	:	
JS Functions	:
Created by		:	Md Mamun Ahmed Sagor
Creation date 	: 	24-10-2022
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
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'?action=order_popup&garments_nature='+garments_nature, title, 'width=1090px,height=400px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var booking_id=this.contentDoc.getElementById("selected_booking_id");
		var job_no=this.contentDoc.getElementById("selected_job_no");
		var req_id=this.contentDoc.getElementById("selected_req_id");
		var app_type=this.contentDoc.getElementById("cbo_type").value;
		//alert(req_id.value)
		//$('#hide_color_id').val('');
		if(booking_id.value!="" || req_id.value!="")
		{
			freeze_window(5);
			$('#txt_app_type').val(app_type);
			get_php_form_data(booking_id.value+'**'+req_id.value+'**'+app_type, "populate_data_from_search_popup", "requires/lapdip_approval_entry_v2_controller" );
			show_list_view(job_no.value+'**'+0+'**'+0+'**'+booking_id.value+'**'+req_id.value+'**'+app_type,'lapdip_approval_list_view_edit','lapdip_approval_list_view','requires/lapdip_approval_entry_v2_controller','',0);
			set_button_status(1, permission, 'fnc_lapdip_approval',1);
			
			release_freezing();
		}
	}
}

function fnc_comments(id,value)
{
	var page_link='requires/lapdip_approval_entry_v2_controller.php?action=comments_popup&comments_data='+value;
	var title='Comments Info';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=440px,height=200px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var comments_data=this.contentDoc.getElementById("txt_comments").value;

		$('#'+id).val(comments_data);
	}
}

function fnc_color_id(color_id, button_status, type)
{
	var hide_color_id='';
	if(type==1)
	{
		hide_color_id=document.getElementById('hide_color_id').value;
		//document.getElementById('copy_val').checked=true;
	}
	else
	{
		hide_color_id=parseInt(document.getElementById('hide_color_id').value);
		//document.getElementById('copy_val').checked=false;
	}

	if(color_id==hide_color_id)
	{
		document.getElementById('hide_color_id').value='';
		set_button_status(0, permission, 'fnc_lapdip_approval',1);
	}
	else
	{
		document.getElementById('hide_color_id').value=color_id;
		set_button_status(button_status, permission, 'fnc_lapdip_approval',1);	
	}
}

function date_compare2( fdate, tdate)
{
	var fdate=fdate.split('-');
	var new_date_from=fdate[2]+'-'+fdate[1]+'-'+fdate[0];

	var tdate=tdate.split('-');
	var new_date_to=tdate[2]+'-'+tdate[1]+'-'+tdate[0];

	var fromDate=new Date(new_date_from);
	var toDate=new Date(new_date_to);

	if(toDate.getTime() > fromDate.getTime())
	{
		 return false;
	}
	else
	{
		return true;
	}
}

function fnc_lapdip_approval( operation )
{
	freeze_window(operation);
	if(operation==2)
	{
		alert("Delete Restricted");
		release_freezing();
		return;
	}
	if (form_validation('txt_booking_no','Booking No')==false)
	{
		release_freezing();
		return;
	}	
	else
	{
		//var txt_job_no=$('#txt_job_no').val();
		var tot_row=$('#color_table tbody tr').length;
		
		var color_data='';
		
		var current_date='<? echo date("d-m-Y"); ?>';
		var z=1;
		for(i=1; i<=tot_row; i++)
		{
			var action=$('#cboaction_'+i).val();
			var action_test=$('#cboaction_'+i).text();
			var action_date=$('#txtactiondate_'+i).val();
			var lapdip_no=$('#txtlapdipno_'+i).val();
			//alert($('#txtrecvfromfactorydate_'+i).val()+'___'+current_date);
			if(operation!=5)  
			{
				if(date_compare(current_date,$('#txtrecvfromfactorydate_'+i).val())==false)
				{
					alert("Recv. From Lab Section Can not Be Less Than Current Date.");
					release_freezing();
					return;
				}
				if(action==2 || action==3)
				{
					if(form_validation('txtactiondate_'+i+'','Action Date')==false)
					{
						release_freezing();
						return;
					}
				}
				
				if(action==3)
				{
					if(form_validation('txtlapdipno_'+i+'*txtactiondate_'+i,'Lapdip No*Action Date')==false)
					{
						release_freezing();
						return;
					}
				}
			}
			else
			{ //For Deny Button
				if(action!=0)
				{
					alert('Action is Found ,Deny not allowed.');	
					release_freezing();
						return;
				}
				else{
					if(form_validation('txtdenycomments_'+i,'Deny Comments')==false)
					{
						release_freezing();
						return;
					}
				}

			}
	 
			
			//txtdenycomments_1
			color_data+="&cbofabriccolor_" + z + "='" + $('#cbofabriccolor_'+i).val()+"'"+"&txtgcolorid_" + z + "='" + $('#txtgcolorid_'+i).val()+"'"+"&txtjobno_" + z + "='" + $('#txtjobno_'+i).val()+"'"+"&txttargetappdate_" + z + "='" + $('#txttargetappdate_'+i).val()+"'"+"&txtsendtofactorydate_" + z + "='" + $('#txtsendtofactorydate_'+i).val()+"'"+"&txtplandeliverydate_" + z + "='" + $('#txtplandeliverydate_'+i).val()+"'"+"&txtrecvfromfactorydate_" + z + "='" + $('#txtrecvfromfactorydate_'+i).val()+"'"+"&txtsubmittedtobuyer_" + z + "='" + $('#txtsubmittedtobuyer_'+i).val()+"'"+"&cboaction_" + z + "='" + $('#cboaction_'+i).val()+"'"+"&txtactiondate_" + z + "='" + $('#txtactiondate_'+i).val()+"'"+"&txtlapdipno_" + z + "='" + $('#txtlapdipno_'+i).val()+"'"+"&txtapplabdipno_" + z + "='" + $('#txtapplabdipno_'+i).val()+"'"+"&txtshadeper_" + z + "='" + $('#txtshadeper_'+i).val()+"'"+"&txtcomments_" + z + "='" + $('#txtcomments_'+i).val()+"'"+"&txtdenycomments_" + z + "='" + $('#txtdenycomments_'+i).val()+"'"+"&cbostatus_" + z + "='" + $('#cbostatus_'+i).val()+"'"+"&updateid_" + z + "='" + $('#updateid_'+i).val()+"'";
			
			z++;
		}
		var data="action=save_update_delete&operation="+operation+"&tot_row="+tot_row+get_submitted_data_string('garments_nature*txt_booking_no*txt_booking_id*txt_req_id*txt_app_type',"../../")+color_data;
		// alert (data); //release_freezing(); return;
		
		http.open("POST","requires/lapdip_approval_entry_v2_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_lapdip_approval_reponse;
	}
}
	 
function fnc_lapdip_approval_reponse()
{
	if(http.readyState == 4) 
	{
	   // alert(http.responseText);
		var response=trim(http.responseText).split('**');
		show_msg(trim(response[0]));
		var app_type=$('#txt_app_type').val();
		if(response[0]==0 || response[0]==1)
		{
			get_php_form_data(response[2]+'**'+response[3]+'**'+response[4], "populate_data_from_search_popup", "requires/lapdip_approval_entry_v2_controller" );
			show_list_view(response[1]+'**'+0+'**'+0+'**'+response[2]+'**'+response[3]+'**'+response[4],'lapdip_approval_list_view_edit','lapdip_approval_list_view','requires/lapdip_approval_entry_v2_controller','',0);
			//get_php_form_data(booking_id.value+'**'+req_id.value+'**'+app_type, "populate_data_from_search_popup", "requires/lapdip_approval_entry_v2_controller" );
			//show_list_view(job_no.value+'**'+0+'**'+0+'**'+booking_id.value+'**'+req_id.value+'**'+app_type,'lapdip_approval_list_view_edit','lapdip_approval_list_view','requires/lapdip_approval_entry_v2_controller','',0);
			
			set_button_status(1, permission, 'fnc_lapdip_approval',1);
		}
		release_freezing();
	}
}

function resubmit(i)
{
	var row_num=$('#color_table tbody tr').length;
	var color_name_id=document.getElementById('cbofabriccolor_'+i).value;	
	var action=document.getElementById('cboaction_'+i).value;
	
	for (var j=1; j<=row_num; j++)
	{
		if(j==i)
		{
			continue;
		}
		else
		{
			var color_name_id_check=document.getElementById('cbofabriccolor_'+j).value;	
			var action_check=document.getElementById('cboaction_'+j).value;

		    if(color_name_id==color_name_id_check && action_check!=2)
			{
				alert("It is already Re-Submitted!");
				return;
			}
		}
	}
	
	$('#txttargetappdate_'+i).val('');
	$('#txttargetappdate_'+i).removeAttr("disabled");
	
	$('#txtsendtofactorydate_'+i).val('');
	$('#txtsendtofactorydate_'+i).removeAttr("disabled");
	
	$('#txtrecvfromfactorydate_'+i).val('');
	$('#txtrecvfromfactorydate_'+i).removeAttr("disabled");
	
	$('#txtsubmittedtobuyer_'+i).val('');
	$('#txtsubmittedtobuyer_'+i).removeAttr("disabled");
	
	$('#cboaction_'+i).val(0);
	$('#cboaction_'+i).removeAttr("disabled");
	
	$('#txtactiondate_'+i).val('');
	$('#txtactiondate_'+i).removeAttr("disabled");
	
	$('#txtlapdipno_'+i).val('');
	$('#txtlapdipno_'+i).removeAttr("disabled");

	$('#txtshadeper_'+i).val('');
	$('#txtshadeper_'+i).removeAttr("disabled");
	
	$('#txtcomments_'+i).val('');
	$('#txtcomments_'+i).removeAttr("disabled");
	
	$('#cbostatus_'+i).val(1);
	$('#cbostatus_'+i).removeAttr("disabled");
	
	$('#updateid_'+i).val('');
	
}

function lapdip_reset()
{
	//document.getElementById('load_color').innerHTML='<? //echo create_drop_down("cbo_color_name", 172, $blank_array,"", 1, "-- Select Color --","" ); ?>';
	document.getElementById('lapdip_approval_list_view').innerHTML='';
}

function check_color_name(value,field_id, i)
{
	if(value!="")
	{
		var color_id=$('#hide_color_id').val();
		var txt_job_no=$('#txt_job_no').val();
		var txt_booking_id=$('#txt_booking_id').val();
		if(txt_booking_id>0){
			var color_response=return_global_ajax_value( value+"**"+txt_job_no+"**2**"+txt_booking_id, 'check_color_name', '', 'requires/lapdip_approval_entry_v2_controller');
		}else{
			
			var color_response=return_global_ajax_value( value+"**"+txt_job_no+"**1**"+txt_booking_id, 'check_color_name', '', 'requires/lapdip_approval_entry_v2_controller');
		}
		
		if(color_response==1)
		{
			alert("Given Extra Color already available in this Style");
			$('#color_id_'+i).val('');
			copy_value('',field_id, i);
		}
		else
		{
			copy_value(value,field_id, i);
		}
	}
}

function copy_value(value,field_id, i) 
{
	var copy_val=document.getElementById('copy_val').checked;
	var rowCount=$('#color_table tbody tr').length;

	/* var currentTime = new Date();
	var day = currentTime.getDate();
	var month = currentTime.getMonth() + 1;
	var year = currentTime.getFullYear();
	var current_date =  day + "-" + month  + "-" + year; */
	var today = new Date();
	var dd = String(today.getDate()).padStart(2, '0');
	var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
	var yyyy = today.getFullYear();
	var current_date = mm + '-' + dd + '-' + yyyy;
	if(copy_val==true)
	{
		for(var j=i; j<=rowCount; j++)
		{
			var action=document.getElementById('cboaction_'+j).value;
			
			var update_id=document.getElementById('updateid_'+color_id+'_'+j).value;
			var action_dis=document.getElementById('cboaction_'+j).disabled;
			
			
			if(!(action==2 || action==3) || update_id=="" || action_dis==false)
			{
				document.getElementById(field_id+j).value=value;
			}
			if(field_id=='txtactiondate_' || field_id=='txttargetappdate_'){
				var action_date=document.getElementById('txtactiondate_'+j).value;
				var datediff = date_compare(action_date,current_date);
				if(datediff==false)
				{
					document.getElementById('txtactiondate_'+j).value='';
					alert("Not Allowed Greater Than Current Date");
					return;
				}							
			}
		}
	}
	else{
		if(field_id=='txtactiondate_' || field_id=='txttargetappdate_'){
			var action_date=document.getElementById('txtactiondate_'+i).value;
			var datediff = date_compare(action_date,current_date);
			if(datediff==false)
			{
				document.getElementById('txtactiondate_'+i).value='';
				alert("Not Allowed Greater Than Current Date");
				return;
			}
		}		
	}
}
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission); ?>
    <form id="lapdipapproval_1">
        <fieldset style="width:1245px;">
		<legend>Lapdip Approval</legend>
        	<table width="1240" cellspacing="2" cellpadding="0" border="0">
				<tr>
					<td width="110" align="right" class="must_entry_caption">F.Booking/Req. No </td>  
                    <td width="170">
                        <input style="width:160px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/lapdip_approval_entry_v2_controller.php','Job/Order Selection Form');" class="text_boxes" autocomplete="off" placeholder="Booking/Req. BR" name="txt_booking_no" id="txt_booking_no" value="" readonly />
                        <input style="width:160px;" type="hidden"  name="txt_booking_id" id="txt_booking_id" value="" readonly />
                        <input style="width:160px;" type="hidden"  name="txt_req_id" id="txt_req_id" value="" readonly />
                        <input style="width:160px;" type="hidden"  name="txt_job_no" id="txt_job_no" readonly />
                        <input style="width:160px;" type="hidden"  name="txt_app_type" id="txt_app_type" readonly />
                    </td>
                    <td width="110" align="right">Company Name </td>
                    <td width="170"><?=create_drop_down( "cbo_company_name", 172, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "Display", $selected, "",1 ); ?></td>
                    <td width="110" align="right">Location Name</td>
                    <td width="170"><?=create_drop_down( "cbo_location_name", 172, "select id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "Display", $selected, "",1 ); ?></td>
                    <td width="110" align="right">Buyer Name</td>
                    <td><?=create_drop_down( "cbo_buyer_name", 172, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id order by buyer_name","id,buyer_name", 1, "Display", $selected, "" ,1); ?></td>
                </tr>
                <tr>
                    <td align="right">Style Ref.</td>
                    <td><input class="text_boxes" type="text" style="width:160px" disabled placeholder="Display" name="txt_style_ref" id="txt_style_ref"/></td>
                    <td align="right">Style Description</td>
                    <td><input class="text_boxes" type="text" style="width:160px;" name="txt_style_description" id="txt_style_description" placeholder="Display" disabled/></td>
                    <td align="right">Pord. Dept.</td>   
                    <td><?=create_drop_down( "cbo_product_department", 172, $product_dept, "",1, "Display", $selected, "" ,1); ?></td>
                    <td align="right">Currency</td>
                    <td><?=create_drop_down( "cbo_currercy", 172, $currency, "", 1, "Display", "", "",1 ); ?></td>
                </tr>
                <tr>
                	<td align="right">Agent</td>
                    <td><?=create_drop_down( "cbo_agent", 172, "select a.id, a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and a.id in (select buyer_id from lib_buyer_party_type where party_type in (20,21)) order by buyer_name","id,buyer_name", 1, "Display", $selected, "",1 ); ?></td>
                    <td align="right">Region</td>
                    <td><?=create_drop_down( "cbo_region", 172, $region, "",1, "Display", $selected, "",1 ); ?></td>
                    <td align="right">Team Leader</td>   
                    <td><?=create_drop_down( "cbo_team_leader", 172, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "Display", $selected, "",1 ); ?></td>
                    <td align="right">Dealing Merchant</td>   
                    <td><?=create_drop_down( "cbo_dealing_merchant", 172, "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "Display", $selected, "",1 ); ?></td>
                </tr>
                <tr>
                	<td colspan="8" align="center"><b>Copy</b> : <input type="checkbox" id="copy_val" name="copy_val"/></h3></td>
                </tr>
                <tr>
                	<td colspan="8" id="lapdip_approval_list_view"></td>
                </tr>
                <tr>
                    <td align="center" colspan="10" valign="middle" class="button_container">
                      <? echo load_submit_buttons($permission, "fnc_lapdip_approval", 0,0 ,"reset_form('lapdipapproval_1','','','','lapdip_reset()')",1) ; ?>
					  <input class="formbutton" type="button" onclick="fnc_lapdip_approval(5)" value="Deny" style="width:80px;">
                    </td>
                </tr>
            </table>
		</fieldset>
	</form>        
</div>
</body>
           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>