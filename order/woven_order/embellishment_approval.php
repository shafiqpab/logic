<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Woven Garments Embellishment Approval
Functionality	:	
JS Functions	:
Created by		:	Fuad 
Creation date 	: 	07-03-2012
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
echo load_html_head_contents("Embellishment Approval", "../../", 1, 1,'','','');
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
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var theemail=this.contentDoc.getElementById("selected_job") //Access form field with id="emailfield"
		
		$('#hide_embell_id').val('');
		
		if(theemail.value!="")
		{
			freeze_window(5);
			
			get_php_form_data(theemail.value, "populate_data_from_search_popup", "requires/embellishment_approval_controller" );
			show_list_view(theemail.value+'**'+0,'embell_approval_list_view_edit','embell_approval_list_view','requires/embellishment_approval_controller','',0);
			set_button_status(0, permission, 'fnc_embellishment_approval',1);
			release_freezing();
		}
	}
}

function fnc_embell_id(embell_id, button_status)
{
	var hide_embell_id=document.getElementById('hide_embell_id').value;
	if(embell_id==hide_embell_id)
	{
		document.getElementById('hide_embell_id').value='';
		set_button_status(0, permission, 'fnc_embellishment_approval',1);
	}
	else
	{
		document.getElementById('hide_embell_id').value=embell_id;
		set_button_status(button_status, permission, 'fnc_embellishment_approval',1);	
	}
}

function load_embell_type()
{
	if(form_validation('txt_job_no','Job No')==false)
	{
		$('#cbo_embell_name').val(0);
		return;
	}
	else
	{
		var txt_job_no=$('#txt_job_no').val();
		var cbo_embell_name=$('#cbo_embell_name').val();
		$('#hide_embell_id').val('');
		
		load_drop_down('requires/embellishment_approval_controller',txt_job_no+'_'+cbo_embell_name, 'load_drop_down_embellishment_type', 'load_embell_type' );
		
		if(cbo_embell_name==5)
		{
			show_list_view(txt_job_no+'**'+1+'**'+cbo_embell_name+'**'+0,'embell_approval_list_view_edit','embell_approval_list_view','requires/embellishment_approval_controller','',0);
		}
	}
}

function fnc_embellishment_approval( operation )
{
	if(operation==2)
	{
		alert("Delete Restricted");
		release_freezing();
		return;
	}
	if (form_validation('txt_job_no*hide_embell_id','Job No*Embellishment Name Tab')==false)
	{
		return;
	}	
	else
	{
		var garments_nature=$('#garments_nature').val();
		var txt_job_no=$('#txt_job_no').val();
		var embell_id=$('#hide_embell_id').val();
		var current_status=$('#current_status_'+embell_id).val();

		var tot_row=$('#table_'+embell_id+' tbody tr').length;
		
		var embell_data='';
		
		for(i=1; i<=tot_row; i++)
		{
			var action=$('#'+'action_'+embell_id+'_'+i).val();
			if(action==2 || action==3)
			{
				if(form_validation('action_date_'+embell_id+'_'+i+'','Action Date')==false)
				{
					return;
				}
			}
			
			embell_data+=get_submitted_data_string('po_id_'+embell_id+'_'+i+'*embell_name_'+embell_id+'_'+i+'*color_id_'+embell_id+'_'+i+'*target_app_date_'+embell_id+'_'+i+'*sent_to_suppl_'+embell_id+'_'+i+'*sent_to_buyer_'+embell_id+'_'+i+'*action_'+embell_id+'_'+i+'*action_date_'+embell_id+'_'+i+'*cbo_supplier_'+embell_id+'_'+i+'*txt_comments_'+embell_id+'_'+i+'*cbo_status_'+embell_id+'_'+i+'*updateid_'+embell_id+'_'+i,"../../",i);	
			
		}

		var data="action=save_update_delete&operation="+operation+embell_data+"&embell_id="+embell_id+"&garments_nature="+garments_nature+"&txt_job_no="+txt_job_no+"&current_status="+current_status+"&tot_row="+tot_row;
		freeze_window(operation);
	  
		http.open("POST","requires/embellishment_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_embell_approval_reponse;
	}
	 
}
	 
function fnc_embell_approval_reponse()
{
	if(http.readyState == 4) 
	{
	  
		var response=trim(http.responseText).split('**');
		show_msg(trim(response[0]));
		$('#hide_embell_id').val('');
		get_php_form_data(response[1], "populate_data_from_search_popup", "requires/embellishment_approval_controller" );
		show_list_view(response[1]+'**'+0,'embell_approval_list_view_edit','embell_approval_list_view','requires/embellishment_approval_controller','',0);
		set_button_status(0, permission, 'fnc_embellishment_approval',1);
		release_freezing();
	}
}

function resubmit(embell_id,i)
{
	var row_num=$('#table_'+embell_id+' tbody tr').length;
	var po_id=document.getElementById('po_id_'+embell_id+'_'+i).value;
	var embell_name=document.getElementById('embell_name_'+embell_id+'_'+i).value;	
	var color_id=document.getElementById('color_id_'+embell_id+'_'+i).value;	
	var action=document.getElementById('action_'+embell_id+'_'+i).value;

	for (var j=1; j<=row_num; j++)
	{
		if(j==i)
		{
			continue;
		}
		else
		{
			var po_id_check=document.getElementById('po_id_'+embell_id+'_'+j).value;
			var embell_name_id_check=document.getElementById('embell_name_'+embell_id+'_'+j).value;	
			var color_id_check=document.getElementById('color_id_'+embell_id+'_'+j).value;	
			var action_check=document.getElementById('action_'+embell_id+'_'+j).value;

		    if(po_id==po_id_check && embell_name==embell_name_id_check && color_id==color_id_check && action_check!=2)
			{
				alert("It is already Re-Submitted!");
				return;
			}
		}
	}
	
	$('#target_app_date_'+embell_id+'_'+i).val('');
	$('#target_app_date_'+embell_id+'_'+i).removeAttr("disabled");
	
	$('#sent_to_suppl_'+embell_id+'_'+i).val('');
	$('#sent_to_suppl_'+embell_id+'_'+i).removeAttr("disabled");
	
	$('#sent_to_buyer_'+embell_id+'_'+i).val('');
	$('#sent_to_buyer_'+embell_id+'_'+i).removeAttr("disabled");
	
	$('#action_'+embell_id+'_'+i).val(0);
	$('#action_'+embell_id+'_'+i).removeAttr("disabled");
	
	$('#action_date_'+embell_id+'_'+i).val('');
	$('#action_date_'+embell_id+'_'+i).removeAttr("disabled");
	
	$('#cbo_supplier_'+embell_id+'_'+i).val(0);
	$('#cbo_supplier_'+embell_id+'_'+i).removeAttr("disabled");
	
	$('#txt_comments_'+embell_id+'_'+i).val('');
	$('#txt_comments_'+embell_id+'_'+i).removeAttr("disabled");
	
	$('#cbo_status_'+embell_id+'_'+i).val(1);
	$('#cbo_status_'+embell_id+'_'+i).removeAttr("disabled");
	
	var updateid=$('#updateid_'+embell_id+'_'+i).val();
	var current_status=$('#current_status_'+embell_id).val();
	var selected_id='';
	
	if(updateid!='')
	{
		if(current_status=='') selected_id=updateid; else selected_id=current_status+','+updateid;
		$('#current_status_'+embell_id).val( selected_id );
	}
	
	$('#updateid_'+embell_id+'_'+i).val('');
	
}

function embell_reset()
{
	document.getElementById('load_embell_type').innerHTML='<? echo create_drop_down("cbo_embell_type", 172, $blank_array,"", 1, "-- Select Type --","" ); ?>';
	document.getElementById('embell_approval_list_view').innerHTML='';
}

function copy_value(value,field_id, i)
{
	var copy_val=document.getElementById('copy_val').checked;
	var embell_id=$('#hide_embell_id').val();
	var rowCount=$('#table_'+embell_id+' tbody tr').length;

	if(copy_val==true)
	{
		for(var j=i; j<=rowCount; j++)
		{
			var action=document.getElementById('action_'+embell_id+'_'+j).value;
			var update_id=document.getElementById('updateid_'+embell_id+'_'+j).value;
			var action_dis=document.getElementById('action_'+embell_id+'_'+j).disabled;
			
			if(!(action==2 || action==3) || update_id=="" || action_dis==false)
			{
				document.getElementById(field_id+embell_id+'_'+j).value=value;
			}
			//document.getElementById(field_id+embell_id+'_'+j).value=value;
		}
	}
}
</script>
 
</head>
 
<body onLoad="set_hotkey()">

<div style="width:100%;" align="center">
     <? echo load_freeze_divs ("../../",$permission);  ?>
    <form id="embellishmentapproval_1">
        <fieldset style="width:1080px;">
		<legend>Embellishment Approval</legend>
        	<table width="1200" cellspacing="2" cellpadding="0" border="0">
				<tr>
					<td width="130" align="right" class="must_entry_caption"> Job No </td>  
                    <td width="170">
                    <input style="width:160px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/embellishment_approval_controller.php','Job/Order Selection Form')" class="text_boxes" autocomplete="off" placeholder="Search Job" name="txt_job_no" id="txt_job_no" readonly />
                     
                    </td>
                    <td width="130" align="right">Company Name </td>
                    <td width="170">
                   <?
                        echo create_drop_down( "cbo_company_name", 172, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "Display", $selected, "",1 );
                   ?> 
                    </td>
                    <td width="130" align="right">Location Name</td>
                    <td>
                        <? 
                            echo create_drop_down( "cbo_location_name", 172, "select id, location_name from lib_location where status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "Display", $selected, "",1 );		
                        ?>	
                    </td>
                </tr>
                <tr>
                    <td align="right">Buyer Name</td>
                    <td>
                        <? 
                            echo create_drop_down( "cbo_buyer_name", 172, "select a.id, a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id order by buyer_name","id,buyer_name", 1, "Display", $selected, "" ,1);   
                        ?>	  
                    </td>
                    <td align="right">Style Ref.</td>
                    <td>
                        <input class="text_boxes" type="text" style="width:160px" disabled placeholder="Display" name="txt_style_ref" id="txt_style_ref"/>	
                    </td>
                    <td align="right">
                       Style Description
                    </td>
                    <td>	
                        <input class="text_boxes" type="text" style="width:160px;" name="txt_style_description" id="txt_style_description" placeholder="Display" disabled/>
                    </td>
                </tr>
                <tr>
                    <td align="right">Pord. Dept.</td>   
                    <td>
                        <? 
                            echo create_drop_down( "cbo_product_department", 172, $product_dept, "",1, "Display", $selected, "" ,1);
                        ?>
                    </td>
                    <td align="right">Currency</td>
                    <td>
                      <? 
                            echo create_drop_down( "cbo_currercy", 172, $currency, "", 1, "Display", "", "",1 );
                      ?>	  
                    </td>
                    <td align="right">Agent</td>
                    <td>
                    <?	
                        echo create_drop_down( "cbo_agent", 172, "select a.id, a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id  and a.id in (select  buyer_id from lib_buyer_party_type where party_type in (20,21)) order by buyer_name","id,buyer_name", 1, "Display", $selected, "",1 );  
                    ?>
                    </td>
                </tr>
                <tr>
                    <td  align="right">Region</td>
                    <td>
                        <? 
                            echo create_drop_down( "cbo_region", 172, $region, "",1, "Display", $selected, "",1 );
                        ?>	  
                    </td>
                    <td align="right">Team Leader</td>   
                    <td>
                        <?  
                            echo create_drop_down( "cbo_team_leader", 172, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "Display", $selected, "",1 );
                        ?>		
                    </td>
                    <td align="right">Dealing Merchant</td>   
                    <td> 
                        <? 
                            echo create_drop_down( "cbo_dealing_merchant", 172, "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "Display", $selected, "",1 );
                        ?>	
                   </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <h3 align="left" style="width:1200px" class="accordion_h_top">Embellishment Name
                        <span id="load_embell_name">
                        <?
							echo create_drop_down("cbo_embell_name", 172, $emblishment_name_array,"", 1, "-- Select Embellishment --",0,"load_embell_type()" );
						?>
                        </span>
                        Type
                        <span id="load_embell_type">
                            <?
                                echo create_drop_down("cbo_embell_type", 172, $blank_array,"", 1, "-- Select Type --","" );
                            ?>  
                        </span> 
                        <input type="hidden" name="hide_embell_id" id="hide_embell_id">
                        <b>Copy</b> : <input type="checkbox" id="copy_val" name="copy_val"/></h3>
                    </td>
                </tr>
                <tr>
                	<td colspan="6" id="embell_approval_list_view"></td>
                </tr>
                <tr>
                    <td align="center" colspan="10" valign="middle" class="button_container">
                      <? echo load_submit_buttons($permission, "fnc_embellishment_approval", 0,0 ,"reset_form('embellishmentapproval_1','','','','embell_reset()')",1) ; ?>
                    </td>
                </tr>
            </table>
		</fieldset>
	</form>        
</div>
</body>
           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>