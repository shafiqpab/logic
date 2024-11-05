<?
/*-------------------------------------------- Comments
Purpose			: 	This form for Next to Ex-Factory Entry
				
Functionality	:	
JS Functions	:
Created by		:	Saidul Reza 
Creation date 	: 	28-12-2013
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_id = $_SESSION['logic_erp']["user_id"];
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//========== user credential start ========
$userCredential = sql_select("SELECT WORKING_UNIT_ID, unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
$working_unit_id = $userCredential[0][csf('WORKING_UNIT_ID')];

$working_credential_cond = "";

if ($working_unit_id > 0) 
{
    $working_credential_cond = " and comp.id in($working_unit_id)";
}
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sewing Out Info","../", 1, 1, $unicode,1,'');
?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
 



 // System ID 
function open_popup()
{ 
	if( form_validation('cbo_company_name*txt_target_hour','Company Name*Target/Hour/line')==false )
	{
		return;
	}
	
	if( $("#h_dtl_mst_id").val()=="" )
	{
		alert("Please save First.");
		return;
	}
	var company_name=$('#cbo_company_name').val();
	var sid=$('#system_id').val();
	var mst_dtls_id=$('#h_dtl_mst_id').val();
	
	var from_date=$('#txt_date_from').val();
	var to_date=$('#txt_date_to').val();
	var target_hour=$('#txt_target_hour').val();
	
	var page_link="requires/actual_production_resource_entry_controller.php?action=job_no_popup&company_name="+company_name+'&sid='+sid+'&from_date='+from_date+'&to_date='+to_date+'&target_hour='+target_hour+'&mst_dtls_id='+mst_dtls_id;
	var title="Job PopUp";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1240px,height=450px,center=1,resize=0,scrolling=0','')

	emailwindow.onclose=function()
	{
		show_list_view($('#system_id').val(),'create_acl_pdc_rec_entry_list_view','list_container','requires/actual_production_resource_entry_controller','setFilterGrid("list_view",-1)');
		release_freezing();
		
	}
}

// <!--System ID-->
function open_line_popup(id)
{ 
	var page_link="requires/actual_production_resource_entry_controller.php?action=Line_popup"+"**"+document.getElementById('cbo_line_no').value+"**"+document.getElementById('cbo_line_merge').value+"**"+document.getElementById('cbo_company_name').value+"**"+document.getElementById('cbo_location').value+"**"+document.getElementById('cbo_floor').value; 
	var title="Style Ref";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=350px,height=420px,center=1,resize=0,scrolling=0','')

	emailwindow.onclose=function()
	{

		var lineid=this.contentDoc.getElementById("selecteds").value; 
		var linetext=this.contentDoc.getElementById("linename").value; 
		if (lineid!="")
		{
			var cbo_company_name = $('#cbo_company_name').val();
			var cbo_location = $('#cbo_location').val();
			var cbo_floor = $('#cbo_floor').val();
			var cbo_line_merge = $('#cbo_line_merge').val();
			var data = cbo_company_name+'*'+cbo_location+'*'+cbo_floor+'*'+cbo_line_merge+'*'+lineid;

			document.getElementById('cbo_line_no').value=lineid;
			document.getElementById('cbo_line_no_sow').value=linetext;

			get_php_form_data(data,'sweing_production_start','requires/actual_production_resource_entry_controller' );

			release_freezing();
		}
		
	}
}

function openmypage_time()
{
	var cbo_company_name = $('#cbo_company_name').val();
	var cbo_location = $('#cbo_location').val();
	var cbo_floor = $('#cbo_floor').val();
	var cbo_line_no = $('#cbo_line_no').val();
	var cbo_line_merge = $('#cbo_line_merge').val();
	var save_string = $('#save_string').val();
	if (form_validation('cbo_company_name*cbo_line_no','Company*Line No')==false)
	{
		return;
	}
	
	var title="Production Info";
	var page_link = 'requires/actual_production_resource_entry_controller.php?cbo_company_name='+cbo_company_name+'&cbo_location='+cbo_location+'&cbo_floor='+cbo_floor+'&cbo_line_no='+cbo_line_no+'&cbo_line_merge='+cbo_line_merge+'&save_string='+save_string+'&action=time_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=250px,center=1,resize=1,scrolling=0','');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var save_string=this.contentDoc.getElementById("hide_save_string").value;	
	
		$('#save_string').val(save_string);
	}
}

function openmypage_employee(order_id,action,width,height)
{
	var popup_width=width;
	var popup_height=height;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/actual_production_resource_entry_controller.php?order_id='+order_id+'&action='+action, 'Employee Popup', 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');

}


function openmypage_adjustment_min()
{
	var cbo_company_name = $('#cbo_company_name').val();
	var system_id = $('#system_id').val();
	var h_dtl_mst_id = $('#h_dtl_mst_id').val();
	var txt_date_to = $('#txt_date_to').val();
	var txt_date_from = $('#txt_date_from').val();
	
	
	if (form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	
	if(($("#h_dtl_mst_id").val()*1)==0)
	{
		alert("Please Save First.");
		return;
	}
	
	var title="Production Info";
	var page_link = 'requires/actual_production_resource_entry_controller.php?cbo_company_name='+cbo_company_name+'&cbo_company_name='+cbo_company_name+'&system_id='+system_id+'&h_dtl_mst_id='+h_dtl_mst_id+'&txt_date_to='+txt_date_to+'&txt_date_from='+txt_date_from+'&action=adjustment_minint_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=350px,center=1,resize=1,scrolling=0','');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var save_string=this.contentDoc.getElementById("hide_save_string").value;	
	
		$('#save_string').val(save_string);
	}
}
// <!--System ID-->
function system_id_popup()
{
	if (form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	var company_name=$('#cbo_company_name').val(); 
	var page_link="requires/actual_production_resource_entry_controller.php?action=SystemIdPopup&company_name="+company_name; 
	var title="Style Ref";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=625px,height=420px,center=1,resize=0,scrolling=0','')
	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("selected_id").value; 
		if (theemail!="")
		{
			theemail=theemail.split("_");
			document.getElementById('system_id').value=theemail[0];
			document.getElementById('system_id_show').value=theemail[1]; 
			document.getElementById('cbo_company_name').value=theemail[2];
			document.getElementById('txt_style_ref_show').value='';

			
			load_drop_down( 'requires/actual_production_resource_entry_controller', theemail[2], 'load_drop_down_location', 'location_td' );
			document.getElementById('cbo_location').value=theemail[3];
			load_drop_down( 'requires/actual_production_resource_entry_controller',theemail[3]+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_floor', 'floor_td' );			
			
			document.getElementById('cbo_floor').value=theemail[4];
			document.getElementById('cbo_line_merge').value=theemail[5];
			document.getElementById('cbo_line_no').value=theemail[6];
			document.getElementById('cbo_line_no_sow').value=theemail[7];
			jQuery('#cbo_company_name').attr('disabled',true);
			//jQuery('#cbo_floor').attr('disabled',true);
			jQuery('#cbo_line_no').attr('disabled',true);
			jQuery('#cbo_location').attr('disabled',true);
			jQuery('#cbo_line_merge').attr('disabled',true);
			jQuery('#cbo_line_no_sow').attr('disabled',true);
			jQuery('#cbo_floor').attr('disabled',true);
			$('#h_dtl_mst_id').val('');
			set_button_status(0, permission, 'fnc_Ac_Production_Resource_Entry',1);
			show_list_view(theemail[0],'create_acl_pdc_rec_entry_list_view','list_container','requires/actual_production_resource_entry_controller','setFilterGrid("list_view",-1)');

			var data = theemail[2]+'*'+theemail[3]+'*'+theemail[4]+'*'+theemail[5]+'*'+theemail[6];

			get_php_form_data(data,'sweing_production_start','requires/actual_production_resource_entry_controller' );
			
			release_freezing();
			
		}
		
	}
}

function fnc_Ac_Production_Resource_Entry(operation)
{
	if(operation!=2)
	{ 
		if( form_validation('cbo_company_name*cbo_location*cbo_floor*cbo_line_no*cbo_line_merge*txt_date_from*txt_date_to*txt_target_hour*txt_working_hour*txt_man_power*txt_capacity*txt_efficiency','Company Name*Location*Floor*Line No*Line Merge*Date From*Date To*Target/Hour*Working Hour*Man Power/line*Capacity*Efficiency')==false ) 
		{
			return;
		}
	}
	var save_string = $("#save_string").val().split(',');
	var save_string2 = save_string[0].split('_');
	if(save_string2[1] == "")
	{
		alert('Please set sewing production start time');
		return false;
	}
	var dataString = "cbo_company_name*cbo_location*cbo_floor*cbo_line_merge*cbo_line_no*txt_date_from*txt_target_hour*txt_date_to*txt_working_hour*txt_man_power*txt_active_machine*system_id*txt_operator*txt_helper*txt_Line_chief*h_dtl_mst_id*txt_capacity*save_string*txt_smv_adjustment*txt_efficiency*txt_iron_man*txt_qi";
 	var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../");
	//alert(data)
	freeze_window(operation);
	http.open("POST","requires/actual_production_resource_entry_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_emp_info_reponse;
}

function fnc_emp_info_reponse()
{
	if(http.readyState == 4) 
	{ 
 		//alert(http.responseText)
		var response=trim(http.responseText).split('**');

		$('#txt_date_from').attr('disabled',false);	
		$('#txt_date_to').attr('disabled',false);	
		
		$('#cbo_company_name').attr('disabled',true);	
		$('#cbo_location').attr('disabled',true);	
		$('#cbo_floor').attr('disabled',true);	
		$('#cbo_line_merge').attr('disabled',true);	
		$('#cbo_line_no_sow').attr('disabled',true);
		if(response[0]==505)
		{
			alert("Line No. Found in Sewing Operation. Update Restricted!!!")

		}
		if(response[0]==201 || response[0]==202)
		{
			alert(response[1]);
			//set_button_status(0, permission, 'fnc_emp_info',1,1);
			release_freezing();return;
		}
		
		
		if(response[0]==0 || response[0]==1|| response[0]==2)
		{	
			show_msg(trim(response[0]));
			if(response[2]){document.getElementById("system_id_show").value=response[2];}
			document.getElementById("system_id").value=response[1];
			reset_form('','','txt_date_from*txt_target_hour*txt_date_to*txt_working_hour*txt_man_power*txt_active_machine*txt_operator*txt_helper*txt_Line_chief*txt_style_ref_show*txt_capacity*txt_smv_adjustment*txt_efficiency*txt_iron_man*txt_qi','','','');
			show_list_view(response[1],'create_acl_pdc_rec_entry_list_view','list_container','requires/actual_production_resource_entry_controller','setFilterGrid("list_view",-1)');
			set_button_status(0, permission, 'fnc_Ac_Production_Resource_Entry',1);
			document.getElementById("h_dtl_mst_id").value='';
			var company_name=$('#cbo_company_name').val();
			var cbo_location = $('#cbo_location').val();
			var cbo_floor = $('#cbo_floor').val();
			var cbo_line_merge = $('#cbo_line_merge').val();
			var lineid = $('#cbo_line_no').val();
			var data = company_name+'*'+cbo_location+'*'+cbo_floor+'*'+cbo_line_merge+'*'+lineid;
			get_php_form_data(data,'sweing_production_start','requires/actual_production_resource_entry_controller' );
			 
			$("#txt_date_from").val('<? echo date('d-m-Y');?>');
			$("#txt_date_to").val('<? echo date('d-m-Y');?>');
			
		}
		if(response[0]==10)
		{
			show_msg(trim(response[0]));

		}
		if(response[0]==11)
		{
			alert("Id Card Number Should not be Duplicate");
			//set_button_status(0, permission, 'fnc_emp_info',1,1);
			release_freezing();
		}
		if(response[0]==12)
		{
			alert(response[1]);
			//set_button_status(0, permission, 'fnc_emp_info',1,1);
			release_freezing();
		}
		if(response[0]==420)
		{
			alert("Please select multiple line for line merge yes.");
			release_freezing();return;
		}
		
		
		if(response[0]==45)
		{
			alert("Make Variable setting yes (Production Resource Allocation) for data entry in this page");
			//set_button_status(0, permission, 'fnc_emp_info',1,1);
			release_freezing();
		}
		/*if(response[0]==5)
		{
			alert('Duplicate system id not allowed for same line number. Check existing ID : '+response[1]+' for this line.');
			release_freezing();
		}*/
		if(response[0]==6)
			{
				alert('Duplicate Date Range not allowed for same line number. Check existing ID : '+response[1]+' for this line.');
				release_freezing();
			}
			release_freezing();
	}
}

function fnResetForm()
{
	jQuery('#cbo_company_name').attr('disabled',false);
	jQuery('#cbo_floor').attr('disabled',false);
	jQuery('#cbo_line_no').attr('disabled',false);
	jQuery('#cbo_location').attr('disabled',false);
	jQuery('#cbo_line_merge').attr('disabled',false);
	jQuery('#cbo_line_no_sow').attr('disabled',false);
	jQuery('#txt_date_from').attr('disabled',false);
	jQuery('#txt_date_to').attr('disabled',false);

	// reset_form('actual_production_resource_entry_1','list_container','','','','');
	reset_form('','','cbo_line_no*system_id_show*system_id*h_dtl_mst_id*cbo_line_merge*cbo_line_no_sow*txt_target_hour*txt_working_hour*txt_man_power*txt_active_machine*txt_operator*txt_helper*txt_Line_chief*txt_style_ref_show*txt_capacity*txt_smv_adjustment*txt_efficiency','','','');
	set_button_status(0, permission, 'fnc_Ac_Production_Resource_Entry',1);
	
}

function reset_line()
{
	$('#cbo_line_merge').val(2);
	$('#cbo_line_no_sow').val('');
	$('#cbo_line_no').val('');
}

function fn_set_manpower()
{
	let operator = $("#txt_operator").val()*1;
	let helper = $("#txt_helper").val()*1;
	let iron_man = $("#txt_iron_man").val()*1;
	let qi = $("#txt_qi").val()*1;
	$("#txt_man_power").val(operator+helper+iron_man+qi);
}
function calculate(value)
{
	
	document.getElementById('txt_capacity').value=document.getElementById('txt_target_hour').value*document.getElementById('txt_working_hour').value;
}
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">

<? echo load_freeze_divs ("../",$permission);  ?>

<fieldset style="width:900px;">
<legend>Production Module</legend>  
    <form name="actual_production_resource_entry_1" id="actual_production_resource_entry_1" autocomplete="off" >
            <table width="100%" border="0">
               <tr>
                    <td colspan="3" align="right">System Id</td>
                    <td colspan="3">
                         <input type="text" name="system_id_show" id="system_id_show" class="text_boxes" style="width:150px;" tabindex="1" placeholder="Double Click to Search" onDblClick="system_id_popup();">
                         <input type="hidden" name="system_id" id="system_id">
                         
                    </td>
               </tr>
                <tr>
                    <td width="100" class="must_entry_caption" align="right">Working Company</td>
                    <td width="170">                                
                        <? 
                         echo create_drop_down( "cbo_company_name",170, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $working_credential_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/actual_production_resource_entry_controller', this.value, 'load_drop_down_location', 'location_td' );get_php_form_data(this.value,'sweing_production_start','requires/actual_production_resource_entry_controller' );" ,"","","","","",2 );
                        ?>
                    </td>
                    <td width="100" class="must_entry_caption" align="right">Location</td>
                    <td width="170" id="location_td">
                        <?
                           
						   echo create_drop_down( "cbo_location", 170, $blank_array,"", 1, "-- Select Floor --", $selected, "",0,"","","","",3 );
                        ?> 
                    </td>
                    <td width="100" class="must_entry_caption" align="right">Floor</td>
                    <td id="floor_td">
                        <?
                            echo create_drop_down( "cbo_floor",170, $blank_array,"", 1, "-- Select Floor --", $selected, "",0,"","","","",4 );
                         ?> 
                    </td>
                </tr>
                <tr>    
                    <td class="must_entry_caption" align="right">Line Merge</td>
                    <td>
                        <?
                            echo create_drop_down( "cbo_line_merge", 170, $yes_no,"", 1, "-- Select --", 2, "",0,"","","","",5);
                        ?>
                    </td>
                    <td class="must_entry_caption" align="right">Line No</td>
                    <td id="line_no_td" colspan="3">
                         <input type="text" name="cbo_line_no_sow" id="cbo_line_no_sow" class="text_boxes" tabindex="6" placeholder="Brows" onClick="open_line_popup();"  style="width:160px " readonly>
                         <input type="hidden" name="cbo_line_no" id="cbo_line_no">
                    </td>
                </tr>
            </table>
           
            <table width="80%" border="0" id="acprsreenTable">
                <tr>
                    <td width="110" class="must_entry_caption" align="right">From date</td>
                    <td>
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" tabindex="7"  style="width:208px " value="<? echo date('d-m-Y'); ?>" readonly>
                        <input type="hidden" name="h_dtl_mst_id" id="h_dtl_mst_id">
                    </td>
                   
                    <td width="110" align="right">Line Chief</td>
                    <td>
                        <input type="text" name="txt_Line_chief" id="txt_Line_chief" class="text_boxes" tabindex="13"  style="width:208px ">
                    </td>
                </tr>
                <tr>
                     <td class="must_entry_caption" align="right"> To Date </td>
                     <td>
                         <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker"  tabindex="8" style="width:208px " value="<? echo date('d-m-Y'); ?>" readonly>
                     </td>  
                     <td align="right">Active Machine/line</td>
                     <td>
                         <input type="text" name="txt_active_machine" id="txt_active_machine" class="text_boxes_numeric" tabindex="14" style="width:208px ">
                     </td>  
                    
                </tr>
                <tr>
                     <td class="must_entry_caption" align="right">Man Power/line</td>
                     <td>
                         <input type="text" name="txt_man_power" id="txt_man_power" class="text_boxes_numeric" tabindex="9" style="width:208px " disabled>
                     </td>  
                    <td class="must_entry_caption" align="right">Target/Hour/line</td>
                    <td>
                        <input type="text" name="txt_target_hour" id="txt_target_hour" class="text_boxes_numeric" tabindex="15" onkeyup="calculate(this.value);" style="width:208px ">
                    </td>
                </tr>
                <tr>
                     <td align="right">Operator/line</td>
                     <td>
                         <input type="text" name="txt_operator" id="txt_operator" class="text_boxes_numeric" tabindex="10" style="width:208px" onblur="fn_set_manpower();">
                     </td> 
                    <td class="must_entry_caption" align="right">Working Hour</td>
                    <td>
                        <input type="text" name="txt_working_hour" id="txt_working_hour" class="text_boxes_numeric"  tabindex="16" onkeyup="calculate(this.value);" style="width:208px ">
                    </td>
                </tr>
                <tr>
                     <td align="right">Helper / Iron Man / QI</td>
                     <td>
                         <input type="text" name="txt_helper" id="txt_helper" class="text_boxes_numeric" placeholder="Helper" style="width:57px " onblur="fn_set_manpower();">&nbsp;&nbsp;<input type="text" name="txt_iron_man" id="txt_iron_man" class="text_boxes_numeric" placeholder="Iron Man" style="width:57px" onblur="fn_set_manpower();">&nbsp;&nbsp;<input type="text" name="txt_qi" id="txt_qi" class="text_boxes_numeric" placeholder="QI" style="width:57px" onblur="fn_set_manpower();">
                     </td> 
                        <td align="right" class="must_entry_caption">Capacity & Target Eff.</td>
                     <td>
                         <input type="text" name="txt_capacity" id="txt_capacity" class="text_boxes_numeric" tabindex="17" style="width:95px ">
                         <input type="text" name="txt_efficiency" id="txt_efficiency" class="text_boxes_numeric" tabindex="17" style="width:97px ">
                     </td>
                </tr>
                <tr>
                     <td align="right">Style Ref.</td>
                     <td>
                         <input type="text" name="txt_style_ref_show" id="txt_style_ref_show" class="text_boxes" style="width:208px;" tabindex="12" placeholder="Browse" onDblClick="open_popup();">
                     
                     </td>
                      <td align="right">Extra Hour SMV Ad. </td>
                     <td>
                         <input type="text" name="txt_smv_adjustment" id="txt_smv_adjustment" class="text_boxes" placeholder="Browse" tabindex="17" style="width:208px " onDblClick="openmypage_adjustment_min();" readonly>
                           	<? //echo create_drop_down( "cbo_smv_adjust_by", 105, $increase_decrease,"", 1, "--- Select ---", 0, "" ,1); ?>
                     </td>
                </tr>
                <tr>
                   <td> </td>
                    <td>
                         <input type="button" name="btn" id="btn" value="Sewing Production Start" class="formbuttonplasminus" onClick="openmypage_time();" style="width:220px">
                         <input type="hidden" name="save_string" id="save_string">
                     </td>
                
                </tr>
            </table>
            <table cellpadding="0" cellspacing="1" width="100%">
                <tr>
                    <td align="center" colspan="6" valign="middle" class="button_container">
                        <? 
                            echo load_submit_buttons( $permission, "fnc_Ac_Production_Resource_Entry", 0,0,"fnResetForm()",1);
                        ?>
                    </td>
                </tr> 
            </table>
 	</form>
</fieldset>
<br>
<fieldset style="width:1050px;">
	<div id="list_container"></div> 
</fieldset>
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$('#cbo_location').val(0);
</script>
</html>