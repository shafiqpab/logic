<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Knit Garments Labdip Approval
Functionality	:	
JS Functions	:
Created by		:	Fuad 
Creation date 	: 	02-03-2012
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
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var theemail=this.contentDoc.getElementById("selected_job") //Access form field with id="emailfield"
		$('#hide_color_id').val('');
		if(theemail.value!="")
		{
			freeze_window(5);
			get_php_form_data(theemail.value, "populate_data_from_search_popup", "requires/lapdip_approval_controller" );
			show_list_view(theemail.value+'**'+0,'lapdip_approval_list_view_edit','lapdip_approval_list_view','requires/lapdip_approval_controller','',0);
			release_freezing();
		}
	}
}

	function color_select_popup(buyer_name,texbox_id)
	{
		//alert(buyer_name);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/lapdip_approval_controller.php?action=color_popup&buyer_name='+buyer_name, 'Color Select Pop Up', 'width=380px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var color_name=this.contentDoc.getElementById("color_name");
			if (color_name.value!="")
			{
				$('#'+texbox_id).val(color_name.value);
			}
		}
	}
	
function fnc_comments(id,value)
{
	var page_link='requires/lapdip_approval_controller.php?action=comments_popup&comments_data='+value;
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
	if(operation==2)
	{
		alert("Delete Restricted");
		release_freezing();
		return;
	}
	if (form_validation('txt_job_no*hide_color_id','Job No*Color Tab')==false)
	{
		return;
	}	
	else
	{
		var garments_nature=$('#garments_nature').val();
		var txt_job_no=$('#txt_job_no').val();
		var hide_color_id=$('#hide_color_id').val();
		var current_status=$('#current_status_'+hide_color_id).val();
		var tot_row=$('#table_'+hide_color_id+' tbody tr').length;
		var color_data='';
		
		var current_date='<? echo date("d-m-Y"); ?>';
		
		for(i=1; i<=tot_row; i++)
		{
			var action=$('#'+'action_'+hide_color_id+'_'+i).val();
			var action_date=$('#action_date_'+hide_color_id+'_'+i).val();
			
			//alert(action_date+'___'+current_date);
			
			/*if(date_compare2( $('#action_date_'+hide_color_id+'_'+i).val(), current_date)==false)
			{
				release_freezing();
				alert("Back Date (Action Date) Entry Not Allow.");
				return;
			}*/
			
			if(action==2 || action==3)
			{
				if(form_validation('action_date_'+hide_color_id+'_'+i+'','Action Date')==false)
				{
					return;
				}
			}
			
			if(action==1 || action==3)
			{
				if(form_validation('txt_lapdip_no_'+hide_color_id+'_'+i+'*action_date_'+hide_color_id+'_'+i,'Lapdip No*Action Date')==false)
				{
					return;
				}
			}
			if(hide_color_id=="ec")
			{
				/*if(form_validation('color_id_'+hide_color_id+'_'+i+'','Color Name')==false)
				{
					return;
				}*/
			}
			color_data+=get_submitted_data_string('po_id_'+hide_color_id+'_'+i+'*color_id_'+hide_color_id+'_'+i+'*target_app_date_'+hide_color_id+'_'+i+'*send_to_factory_date_'+hide_color_id+'_'+i+'*recv_from_factory_date_'+hide_color_id+'_'+i+'*submitted_to_buyer_'+hide_color_id+'_'+i+'*action_'+hide_color_id+'_'+i+'*action_date_'+hide_color_id+'_'+i+'*txt_lapdip_no_'+hide_color_id+'_'+i+'*txt_shade_per_no_'+hide_color_id+'_'+i+'*txt_pantone_no_'+hide_color_id+'_'+i+'*txt_comments_'+hide_color_id+'_'+i+'*cbo_status_'+hide_color_id+'_'+i+'*updateid_'+hide_color_id+'_'+i,"../../",i);	
		}
		//alert (color_data); return;

		var data="action=save_update_delete&operation="+operation+color_data+"&color_id="+hide_color_id+"&garments_nature="+garments_nature+"&txt_job_no="+txt_job_no+"&current_status="+current_status+"&tot_row="+tot_row;
		freeze_window(operation);
		http.open("POST","requires/lapdip_approval_controller.php",true);
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
		$('#hide_color_id').val('');
		get_php_form_data(response[1], "populate_data_from_search_popup", "requires/lapdip_approval_controller" );
		show_list_view(response[1]+'**'+0,'lapdip_approval_list_view_edit','lapdip_approval_list_view','requires/lapdip_approval_controller','',0);
		set_button_status(0, permission, 'fnc_lapdip_approval',1);
		release_freezing();
	}
}

function resubmit(color_id,i)
{
	var row_num=$('#table_'+color_id+' tbody tr').length;
	var po_id=document.getElementById('po_id_'+color_id+'_'+i).value;
	var color_name_id=document.getElementById('color_id_'+color_id+'_'+i).value;	
	var action=document.getElementById('action_'+color_id+'_'+i).value;
	
	for (var j=1; j<=row_num; j++)
	{
		if(j==i)
		{
			continue;
		}
		else
		{
			var po_id_check=document.getElementById('po_id_'+color_id+'_'+j).value;
			var color_name_id_check=document.getElementById('color_id_'+color_id+'_'+j).value;	
			var action_check=document.getElementById('action_'+color_id+'_'+j).value;

		    if(po_id==po_id_check && color_name_id==color_name_id_check && action_check!=2)
			{
				alert("It is already Re-Submitted!");
				return;
			}
		}
	}
	
	$('#target_app_date_'+color_id+'_'+i).val('');
	$('#target_app_date_'+color_id+'_'+i).removeAttr("disabled");
	
	$('#send_to_factory_date_'+color_id+'_'+i).val('');
	$('#send_to_factory_date_'+color_id+'_'+i).removeAttr("disabled");
	
	$('#recv_from_factory_date_'+color_id+'_'+i).val('');
	$('#recv_from_factory_date_'+color_id+'_'+i).removeAttr("disabled");
	
	$('#submitted_to_buyer_'+color_id+'_'+i).val('');
	$('#submitted_to_buyer_'+color_id+'_'+i).removeAttr("disabled");
	
	$('#action_'+color_id+'_'+i).val(0);
	$('#action_'+color_id+'_'+i).removeAttr("disabled");
	
	$('#action_date_'+color_id+'_'+i).val('');
	$('#action_date_'+color_id+'_'+i).removeAttr("disabled");
	
	$('#txt_lapdip_no_'+color_id+'_'+i).val('');
	$('#txt_lapdip_no_'+color_id+'_'+i).removeAttr("disabled");

	$('#txt_shade_per_no_'+color_id+'_'+i).val('');
	$('#txt_shade_per_no_'+color_id+'_'+i).removeAttr("disabled");

	$('#txt_pantone_no_'+color_id+'_'+i).val('');
	$('#txt_pantone_no_'+color_id+'_'+i).removeAttr("disabled");
	
	$('#txt_comments_'+color_id+'_'+i).val('');
	$('#txt_comments_'+color_id+'_'+i).removeAttr("disabled");
	
	$('#cbo_status_'+color_id+'_'+i).val(1);
	$('#cbo_status_'+color_id+'_'+i).removeAttr("disabled");
	
	var updateid=$('#updateid_'+color_id+'_'+i).val();
	var current_status=$('#current_status_'+color_id).val();
	var selected_id='';
	
	if(updateid!='')
	{
		if(current_status=='') selected_id=updateid; else selected_id=current_status+','+updateid;
		$('#current_status_'+color_id).val( selected_id );
	}
	
	$('#updateid_'+color_id+'_'+i).val('');
	
}

function lapdip_reset()
{
	document.getElementById('load_color').innerHTML='<? echo create_drop_down("cbo_color_name", 172, $blank_array,"", 1, "-- Select Color --","" ); ?>';
	document.getElementById('lapdip_approval_list_view').innerHTML='';
}

function check_color_name(value,field_id, i)
{
	if(value!="")
	{
		var color_id=$('#hide_color_id').val();
		var txt_job_no=$('#txt_job_no').val();
		var color_response=return_global_ajax_value( value+"**"+txt_job_no, 'check_color_name', '', 'requires/lapdip_approval_controller');
		if(color_response==1)
		{
			alert("Given Extra Color already available in this Style");
			$('#color_id_'+color_id+'_'+i).val('');
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
	var color_id=$('#hide_color_id').val();
	var rowCount=$('#table_'+color_id+' tbody tr').length;

	var today = new Date();
	var dd = String(today.getDate()).padStart(2, '0');
	var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
	var yyyy = today.getFullYear();
	var current_date = mm + '-' + dd + '-' + yyyy;
	if(copy_val==true)
	{
		for(var j=i; j<=rowCount; j++)
		{
			var action=document.getElementById('action_'+color_id+'_'+j).value;
			
			var update_id=document.getElementById('updateid_'+color_id+'_'+j).value;
			var action_dis=document.getElementById('action_'+color_id+'_'+j).disabled;
			
			
			if(!(action==2 || action==3) || update_id=="" || action_dis==false)
			{
				document.getElementById(field_id+color_id+'_'+j).value=value;
			}
			if(field_id=='action_date_' || field_id=='target_app_date_'){
				var action_date=document.getElementById('action_date_'+color_id+'_'+j).value;
				var datediff = date_compare(action_date,current_date);
				if(datediff==false)
				{
					document.getElementById('action_date_'+color_id+'_'+j).value='';
					alert("Not Allowed Greater Than Current Date");
					return;
				}							
			}
		}
	}
	else{
		if(field_id=='action_date_' || field_id=='target_app_date_'){
			var action_date=document.getElementById('action_date_'+color_id+'_'+i).value;
			var datediff = date_compare(action_date,current_date);
			if(datediff==false)
			{
				document.getElementById('action_date_'+color_id+'_'+i).value='';
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
        <fieldset style="width:1080px;">
		<legend>Lapdip Approval</legend>
        	<table width="1075" cellspacing="2" cellpadding="0" border="0">
				<tr>
					<td width="130" align="right" class="must_entry_caption"> Job No </td>  
                    <td width="170">
                    <input style="width:160px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/lapdip_approval_controller.php','Job/Order Selection Form')" class="text_boxes" autocomplete="off" placeholder="Search Job" name="txt_job_no" id="txt_job_no" readonly />
                     
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
                    <td colspan="6">
                        <h3 align="left" style="width:1075px" class="accordion_h_top">Color Name
                        <span id="load_color">
                            <?
                                echo create_drop_down("cbo_color_name", 172, $blank_array,"", 1, "-- Select Color --","" );
                            ?>  
                        </span> 
                        <input type="" name="hide_color_id"   id="hide_color_id">
                        <b>Copy</b> : <input type="checkbox" id="copy_val" name="copy_val"/></h3>
                    </td>
                </tr>
                <tr>
                	<td colspan="6" id="lapdip_approval_list_view"></td>
                </tr>
                <tr>
                    <td align="center" colspan="10" valign="middle" class="button_container">
                      <? echo load_submit_buttons($permission, "fnc_lapdip_approval", 0,0 ,"reset_form('lapdipapproval_1','','','','lapdip_reset()')",1) ; ?>
                    </td>
                </tr>
            </table>
		</fieldset>
	</form>        
</div>
</body>
           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>