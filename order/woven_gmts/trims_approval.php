<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create Woven Garments Trims Approval
					
Functionality	:	
				

JS Functions	:

Created by		:	Fuad 
Creation date 	: 	17-2-2012
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
echo load_html_head_contents("Trims Approval", "../../", 1, 1,'','','');

?>	
 
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

var permission='<? echo $permission; ?>';
 	 
function openmypage(page_link,title)
{
	var garments_nature=$('#garments_nature').val(); 
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'?action=order_popup&garments_nature='+garments_nature, title, 'width=1100px,height=400px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var theemail=this.contentDoc.getElementById("selected_job") //Access form field with id="emailfield"
		
		$('#hide_item_group_id').val('');
		
		if(theemail.value!="")
		{
			freeze_window(5);
			
			get_php_form_data(theemail.value, "populate_data_from_search_popup", "requires/trims_approval_controller" );
			show_list_view(theemail.value+'**'+0,'trims_approval_list_view_edit','trims_approval_list_view','requires/trims_approval_controller','',0);
		
			release_freezing();
		}
	}
}

function fnc_item_group_id(item_group_id, button_status)
{
	var hide_item_group=parseInt(document.getElementById('hide_item_group_id').value);
	if(item_group_id==hide_item_group)
	{
		document.getElementById('hide_item_group_id').value='';
		set_button_status(0, permission, 'fnc_trims_approval',1);
	}
	else
	{
		document.getElementById('hide_item_group_id').value=item_group_id;
		set_button_status(button_status, permission, 'fnc_trims_approval',1);	
	}
}

function fnc_trims_approval( operation )
{
	if (form_validation('txt_job_no*hide_item_group_id','Job No*Item Group Tab')==false)
	{
		return;
	}	
	else
	{
		var garments_nature=$('#garments_nature').val();
		var txt_job_no=$('#txt_job_no').val();
		var item_group_id=$('#hide_item_group_id').val();
		var current_status=$('#current_status_'+item_group_id).val();
		var tot_row=$('#table_'+item_group_id+' tbody tr').length;
		
		var item_data='';
		
		for(i=1; i<=tot_row; i++)
		{
			var action=$('#'+'action_'+item_group_id+'_'+i).val();
			if(action==2 || action==3)
			{
				if(form_validation('action_date_'+item_group_id+'_'+i+'','Action Date')==false)
				{
					return;
				}
			}
			
			item_data+=get_submitted_data_string('po_id_'+item_group_id+'_'+i+'*item_group_id_'+item_group_id+'_'+i+'*target_app_date_'+item_group_id+'_'+i+'*sent_to_suppl_'+item_group_id+'_'+i+'*sent_to_buyer_'+item_group_id+'_'+i+'*action_'+item_group_id+'_'+i+'*action_date_'+item_group_id+'_'+i+'*cbo_supplier_'+item_group_id+'_'+i+'*txt_comments_'+item_group_id+'_'+i+'*cbo_status_'+item_group_id+'_'+i+'*updateid_'+item_group_id+'_'+i,"../../",i);	
			
		}

		var data="action=save_update_delete&operation="+operation+item_data+"&item_group_id="+item_group_id+"&garments_nature="+garments_nature+"&txt_job_no="+txt_job_no+"&current_status="+current_status+"&tot_row="+tot_row;
		freeze_window(operation);
	  
		http.open("POST","requires/trims_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_trims_approval_reponse;
	}
	 
}
	 
function fnc_trims_approval_reponse()
{
	if(http.readyState == 4) 
	{
	    //alert(http.responseText);
		var response=trim(http.responseText).split('**');
		show_msg(trim(response[0]));
		$('#hide_item_group_id').val('');
		get_php_form_data(response[1], "populate_data_from_search_popup", "requires/trims_approval_controller" );
		show_list_view(response[1]+'**'+0,'trims_approval_list_view_edit','trims_approval_list_view','requires/trims_approval_controller','',0);
		set_button_status(0, permission, 'fnc_trims_approval',1);
		release_freezing();
	}
}

function resubmit(item_group_id,i)
{
	var row_num=$('#table_'+item_group_id+' tbody tr').length;
	var po_id=document.getElementById('po_id_'+item_group_id+'_'+i).value;
	var item_group_id=document.getElementById('item_group_id_'+item_group_id+'_'+i).value;	
	var action=document.getElementById('action_'+item_group_id+'_'+i).value;

	for (var j=1; j<=row_num; j++)
	{
		if(j==i)
		{
			continue;
		}
		else
		{
			
			var po_id_check=document.getElementById('po_id_'+item_group_id+'_'+j).value;
			var item_group_id_check=document.getElementById('item_group_id_'+item_group_id+'_'+j).value;	
			var action_check=document.getElementById('action_'+item_group_id+'_'+j).value;

		    if(po_id==po_id_check && item_group_id==item_group_id_check && action_check!=2)
			{
				alert("It is already Re-Submitted!");
				return;
			}
		}
	}
	
	$('#target_app_date_'+item_group_id+'_'+i).val('');
	$('#target_app_date_'+item_group_id+'_'+i).removeAttr("disabled");
	
	$('#sent_to_suppl_'+item_group_id+'_'+i).val('');
	$('#sent_to_suppl_'+item_group_id+'_'+i).removeAttr("disabled");
	
	$('#sent_to_buyer_'+item_group_id+'_'+i).val('');
	$('#sent_to_buyer_'+item_group_id+'_'+i).removeAttr("disabled");
	
	$('#action_'+item_group_id+'_'+i).val(0);
	$('#action_'+item_group_id+'_'+i).removeAttr("disabled");
	
	$('#action_date_'+item_group_id+'_'+i).val('');
	$('#action_date_'+item_group_id+'_'+i).removeAttr("disabled");
	
	$('#cbo_supplier_'+item_group_id+'_'+i).val(0);
	$('#cbo_supplier_'+item_group_id+'_'+i).removeAttr("disabled");
	
	$('#txt_comments_'+item_group_id+'_'+i).val('');
	$('#txt_comments_'+item_group_id+'_'+i).removeAttr("disabled");
	
	$('#cbo_status_'+item_group_id+'_'+i).val(1);
	$('#cbo_status_'+item_group_id+'_'+i).removeAttr("disabled");
	
	var updateid=$('#updateid_'+item_group_id+'_'+i).val();
	var current_status=$('#current_status_'+item_group_id).val();
	var selected_id='';
	
	if(updateid!='')
	{
		if(current_status=='') selected_id=updateid; else selected_id=current_status+','+updateid;
		$('#current_status_'+item_group_id).val( selected_id );
	}
	
	$('#updateid_'+item_group_id+'_'+i).val('');
	
}

function trims_item_reset()
{
	document.getElementById('load_item').innerHTML='<? echo create_drop_down("cbo_trims_name", 172, $blank_array,"", 1, "-- Select Trims --","" ); ?>';
	document.getElementById('trims_approval_list_view').innerHTML='';
}

function copy_value(value,field_id, i)
{
	
	var copy_val=document.getElementById('copy_val').checked;
	var item_group_id=$('#hide_item_group_id').val();
	var rowCount=$('#table_'+item_group_id+' tbody tr').length;

	if(copy_val==true)
	{
		for(var j=i; j<=rowCount; j++)
		{
			var action=document.getElementById('action_'+item_group_id+'_'+j).value;
			var update_id=document.getElementById('updateid_'+item_group_id+'_'+j).value;
			var action_dis=document.getElementById('action_'+item_group_id+'_'+j).disabled;
			
			if(!(action==2 || action==3) || update_id=="" || action_dis==false)
			{
				document.getElementById(field_id+item_group_id+'_'+j).value=value;
			}
			//document.getElementById(field_id+item_group_id+'_'+j).value=value;
		}
	}
}
</script>
 
</head>
 
<body onLoad="set_hotkey()">

<div style="width:100%;" align="center">
     <? echo load_freeze_divs ("../../",$permission);  ?>
    <form id="trimsapproval_1">
        <fieldset style="width:1080px;">
		<legend>Trims Approval</legend>
        	<table width="1075" cellspacing="2" cellpadding="0" border="0">
				<tr>
					<td width="130" align="right" class="must_entry_caption"> Job No </td>  
                    <td width="170">
                    <input style="width:160px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/trims_approval_controller.php','Job/Order Selection Form')" class="text_boxes" autocomplete="off" placeholder="Search Job" name="txt_job_no" id="txt_job_no" readonly />
                     
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
                            echo create_drop_down( "cbo_location_name", 172, "select id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "Display", $selected, "",1 );		
                        ?>	
                    </td>
                </tr>
                <tr>
                    <td align="right">Buyer Name</td>
                    <td>
                        <? 
                            echo create_drop_down( "cbo_buyer_name", 172, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id order by buyer_name","id,buyer_name", 1, "Display", $selected, "" ,1);   
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
                        echo create_drop_down( "cbo_agent", 172, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id  and a.id in (select  buyer_id from lib_buyer_party_type where party_type in (20,21)) order by buyer_name","id,buyer_name", 1, "Display", $selected, "",1 );  
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
                        <h3 align="left" style="width:1075px" class="accordion_h_top">Trims Name
                        <span id="load_item">
                            <?
                                echo create_drop_down("cbo_trims_name", 172, $blank_array,"", 1, "-- Select Trims --","" );
                            ?>  
                        </span> 
                        <input type="hidden" name="hide_item_group_id"   id="hide_item_group_id">
                        <b>Copy</b> : <input type="checkbox" id="copy_val" name="copy_val"/></h3>
                    </td>
                </tr>
                <tr>
                	<td colspan="6" id="trims_approval_list_view"></td>
                </tr>
                <tr>
                    <td align="center" colspan="10" valign="middle" class="button_container">
                      <? echo load_submit_buttons($permission, "fnc_trims_approval", 0,0 ,"reset_form('trimsapproval_1','','','','trims_item_reset()')",1) ; ?>
                    </td>
                </tr>
            </table>
		</fieldset>
	</form>        
</div>
</body>
           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>