<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Sample Checklist
Functionality	:	
JS Functions	:
Created by		:	Rehan Uddin
Creation date 	: 	22-02-2017
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
echo load_html_head_contents("Sample Checklist", "../", 1, 1,'','','');

?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';
 	function fnc_sample_checklist_mst_info( operation )
	{
	   if (form_validation('requisition_hidden_id*cbo_garments_item*txt_checklist_date','Browse Requisition*Garments Item*Checklist Date')==false)
		{
			return;
		}	
		else  
		{ 
			var data="action=save_update_delete_mst&operation="+operation+get_submitted_data_string('requisition_hidden_id*cbo_garments_item*txt_checklist_date*update_id*txt_remarks_mst*cbo_completion_status',"../");
			//alert(data);return;
			freeze_window(operation);
			http.open("POST","requires/sample_checklist_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_sample_checklist_mst_info_response;
		}
	}
	
	function fnc_sample_checklist_mst_info_response()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
 			if(response[0]==0)
			 {
				show_msg(response[0]);
				$("#txt_checklist_id").val(response[3]);
 				$("#requisition_hidden_id").val(response[2]);
				$("#update_id").val(response[1]);
 				set_button_status(1, permission, 'fnc_sample_checklist_mst_info',1);
			 }
			if(response[0]==10 )
			{
				show_msg(response[0]);
			}
			if(response[0]==1 )
			{
				show_msg(response[0]);
			}
 			release_freezing();
		}
	}

	function fnc_sample_checklist_dtls( operation )
	{
		var update_id=$("#update_id").val(); 
		var requisition_hidden_id=$("#requisition_hidden_id").val();
		if(update_id=="")
		{
			alert("Save Master Part !!");
			return;
		}
		var checkArrayNo="";var dataAll2="";var submitDateAll="";
		var updateDtls="";
		var forNewSave="";
		var total_tr=$('#tblChecklistDtls tr').length;
		var z=1;
		for(i=1; i<=total_tr; i++)
		{
			if ($('#txtCheckBoxId_'+i).is(":checked"))
			{
				//txtremarks_* txtsubmitdate_
				var checklist_id = $('#txtDocumentSetArrayid_'+i).val();
				var updateDtlsId = $('#updateDtlsId_'+i).val();

				var submitdate = $('#txtsubmitdate_'+i).val();
				var remarks = $('#txtremarks_'+i).val();
				//if(RemarksAll=="") RemarksAll= txtremarks; else RemarksAll +=','+txtremarks;
				//if(submitDateAll=="") submitDateAll= txtsubmitdate; else submitDateAll +=','+txtsubmitdate;

				if(checkArrayNo=="") checkArrayNo= checklist_id; else checkArrayNo +=','+checklist_id;
				if(updateDtlsId!="")
				{
					if(updateDtls=="") updateDtls= updateDtlsId; else updateDtls +=','+updateDtlsId;
				}

				dataAll2+="&txtsubmitdate_" + z + "='" + $('#txtsubmitdate_'+i).val()+"'"+"&txtremarks_" + z + "='" + $('#txtremarks_'+i).val()+"'";

				z++;
			}

			if ($('#txtCheckBoxId_'+i).is(":checked") && $('#updateDtlsId_'+i).val()=='')
			{
				 var checklist_idn = $('#txtDocumentSetArrayid_'+i).val();
				 if(forNewSave=="") forNewSave= checklist_idn; else forNewSave +=','+checklist_idn;
			}
			if(checklist_id==440)
			{
				var image_pattern=return_global_ajax_value(document.getElementById('update_id').value, 'Image_check', '', 'requires/sample_checklist_controller');
				if(image_pattern==0){
					alert("ADD IMAGE !!");
					return;
				}
				
			} 
		
				
		}
	
		//alert(checklist_id+'='+update_id+'='+checkArrayNo+'='+checklist_idn);
		
		var data="action=save_update_delete_dtls&operation="+operation+'&checkArrayNo='+checkArrayNo+'&update_id='+update_id+'&requisition_hidden_id='+requisition_hidden_id+'&updateDtls='+updateDtls+'&forNewSave='+forNewSave+'&dataAll2='+dataAll2;
		// alert(data); 
		freeze_window(operation);
		http.open("POST","requires/sample_checklist_controller.php", true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_sample_checklist_dtls_response;
	}
	
	function fnc_sample_checklist_dtls_response()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
 			if(response[0]==0)
			 {
				show_msg(response[0]);
				fnc_load_tr(response[1]);
 			 }
 			 if(response[0]==1)
			 {
				show_msg(response[0]);
				fnc_load_tr(response[1]);
 			 }
			if(response[0]==10 )
			{
				show_msg(response[0]);
			}
 			release_freezing();
		}
	}

	function fnc_load_tr(data) 
	{
  		var list_view_tr = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/sample_checklist_controller');
 		if(trim(list_view_tr)!="")
		{	
 			$("#sample_details_container tr").remove();
			$("#sample_details_container").append(list_view_tr);
			set_all_onclick();
 			set_button_status(1, permission, 'fnc_sample_checklist_dtls',2);
			return;
		}
	} 
 
	function button_status(type)
	{
		if(type==1)
		{
			reset_form('samplechecklist_1','','');
			set_button_status(0, permission, 'fnc_sample_checklist_mst_info',1);
		}
		if(type==2)
		{
			reset_form('checklistDtls_1','','');
			set_button_status(0, permission, 'fnc_sample_checklist_dtls',2);
		}
	}

	function openmypage_checklist()
	{
		var title = 'Checklist ID Search';	
		var page_link = 'requires/sample_checklist_controller.php?&action=checklist_id_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=400px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var mst_tbl_id=this.contentDoc.getElementById("selected_job").value;//mst id
			
			if (mst_tbl_id!="")
			{
				freeze_window(5); 
				get_php_form_data(mst_tbl_id, "populate_data_from_checklist_search_popup", "requires/sample_checklist_controller" );
				var bookingdata=return_global_ajax_value(mst_tbl_id, 'booking_data', '', 'requires/sample_checklist_controller');
				var booking_data=bookingdata.split("__");
				$('#booking_td').text(booking_data[0]);
				$('#booking_td').css({
					'color':'blue',
					'font-weight':'bold',
					'cursor':'pointer'
				});
				$('#booking_td').removeAttr("onclick").attr("onclick","generate_fabric_report('"+booking_data[0]+'___'+booking_data[1]+'___'+booking_data[2]+'___'+booking_data[3]+'___'+booking_data[4]+"');");
				release_freezing();
				set_button_status(1, permission, 'fnc_sample_checklist_mst_info',1);
			}
		}
	}

	function openmypage_requisition()
	{
		var title = 'Requisition ID Search';	
		var page_link = 'requires/sample_checklist_controller.php?&action=requisition_id_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=920px,height=400px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var mst_tbl_id=this.contentDoc.getElementById("selected_job").value;//mst id
			
			if (mst_tbl_id!="")
			{
				freeze_window(5);
				get_php_form_data(mst_tbl_id, "populate_data_from_requisition_search_popup", "requires/sample_checklist_controller" );
				
				var bookingdata=return_global_ajax_value(mst_tbl_id, 'booking_data', '', 'requires/sample_checklist_controller');
				var booking_data=bookingdata.split("__");
				$('#booking_td').text(booking_data[0]);
				$('#booking_td').css({
					'color':'blue',
					'font-weight':'bold',
					'cursor':'pointer'
				});
				$('#booking_td').removeAttr("onclick").attr("onclick","generate_fabric_report('"+booking_data[0]+'___'+booking_data[1]+'___'+booking_data[2]+'___'+booking_data[3]+'___'+booking_data[4]+"');");
				release_freezing();
			}
		}
	}
	
	function generate_fabric_report(data)
	{
		if ($('#booking_td').text()=="")
		{
			alert("Booking No Not Found.");
			return;
		}
		else
		{
			var booking_data=data.split("___");
			var booking_no=booking_data[0];
			var type=booking_data[1];
			if(type==1) var action='show_fabric_booking_report';
			if(type==2) var action='show_fabric_booking_report_barnali';
			var company_id=booking_data[2];
			var approved=booking_data[3];
			var fabric_nature=booking_data[4];
			
			var data="action="+action+
					'&txt_booking_no='+"'"+booking_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&id_approved_id='+"'"+approved+"'"+
					'&cbo_fabric_natu='+"'"+fabric_nature;
			
			//freeze_window(5);
			http.open("POST","../order/woven_order/requires/sample_requisition_booking_non_order_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_fabric_report_reponse;
		}	
	}
	
	
	function generate_fabric_report_reponse()
	{
		if(http.readyState == 4) 
		{	
			var file_data=http.responseText.split('****');
			//$('#pdf_file_name').html(file_data[1]);
			$('#data_panel').html(file_data[0] );
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
		}
	}
	
	function fnc_receive_dtls()
	{
		if (form_validation('txt_checklist_id','Checklist Id')==false)
		{
			return;
		}
		else
		{
			var booking_no=$('#booking_td').text();
			var title = 'Finish Fabric Receive Details';	
			var page_link = 'requires/sample_checklist_controller.php?&action=receive_popup&booking_no='+booking_no;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=400px,center=1,resize=1,scrolling=0','');
		}
	}
 		
	function check_all_data()
	{
			if(document.getElementById('checkall').checked==true)
			{
				document.getElementById('checkall').value=1;
			}
			else if(document.getElementById('checkall').checked==false)
			{
				document.getElementById('checkall').value=2;
			}
		
			var tot_row=$('#sample_details_container tr').length;
		//	alert(tot_row);
			for( var i = 1; i <= tot_row; i++ )
			{
				if($('#checkall').val()==1)
				{
					document.getElementById('txtCheckBoxId_'+i).checked=true;
					//document.getElementById('checkid'+i).value=1;
				}
				else if($('#checkall').val()==2) 
				{
					document.getElementById('txtCheckBoxId_'+i).checked=false;
					//document.getElementById('checkid'+i).value=2;
				}
			}
	}
</script>
</head>
<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",''); ?>
    <form name="samplechecklist_1" id="samplechecklist_1"> 
        <fieldset style="width:800px;margin-bottom: 10px;" id="checklistMst">
        <legend>Sample Checklist</legend>
            <table cellpadding="2" cellspacing="2" width="800" align="center"> 
                <tr>
                    <td colspan="3" align="right"><b>Checklist Id</b></td>
                    <td colspan="3"><input type="text" name="txt_checklist_id" id="txt_checklist_id" class="text_boxes" style="width: 120px;" placeholder="Browse" readonly onDblClick="openmypage_checklist()" ></td>
                </tr>
                <tr>
                    <td width="120" class="must_entry_caption">Requisition</td>
                        <input type="hidden" name="requisition_hidden_id" id="requisition_hidden_id">
                        <input type="hidden" name="update_id" id="update_id">
                    <td width="140"><input type="text" name="txt_requisition_id" id="txt_requisition_id" class="text_boxes" style="width:120px;" placeholder="Browse" readonly onDblClick="openmypage_requisition();" ></td>
                    <td width="120" class="must_entry_caption">Garments Item</td>
                    <td width="140" id="gmts_td"><? echo create_drop_down( "cbo_garments_item", 130, $blank_array, "", 1, "-- Select Item --", $selected, ""); ?></td>
                    <td width="120" class="must_entry_caption">Checklist Date</td>
                    <td><input name="txt_checklist_date" id="txt_checklist_date" class="datepicker" type="text" value="" style="width:120px;"  /></td>
                </tr>
                <tr>
                    <td><b>Completion Status</b></td>
                    <td><? echo create_drop_down( "cbo_completion_status", 130, $yes_no, "", 1, "-- Select --", 2, ""); ?></td>
					<td>&nbsp;</td>
                    <td><input type="button" class="image_uploader" style="width:110px" value="ADD FILE" onClick="file_uploader ( '../', document.getElementById('update_id').value,'', 'sample_checklist', 2 ,1)"></td>
                    <td>&nbsp;</td>
                    <td><input type="button" id="fabrecdtls" value="Finish Fab. Receive Dtls" width="140" class="image_uploader" onClick="fnc_receive_dtls();"/></td>
                </tr>
				<tr>
                    <td><b>Remarks</b></td>
                    
					<td><input type="text" name="txt_remarks_mst" id="txt_remarks_mst" class="text_boxes" style="width:120px;" ></td>
                     </td>
                </tr>
                <tr>
                    <td colspan="6" align="center" height="15">
                        <span id="cutting_approved_msg" style="color:crimson;font-weight: bold;font-size: 19px;"></span>
                        <input type="hidden" name="update_id" id="update_id" value="">
                    </td>		 
                </tr>
                <tr>
                    <td colspan="6" valign="bottom" align="center" class="button_container">
                    <? echo load_submit_buttons( $permission, "fnc_sample_checklist_mst_info", 0,'',"button_status(1)"); ?>
                    <div style="display:none" id="data_panel"></div>
                    <!--<div id="pdf_file_name"></div>-->
                    </td>		 
                </tr>
            </table>
        </fieldset>
    </form>
    <form name="checklistDtls_1" id="checklistDtls_1">
        <fieldset style="width: 800px;" id="checklistDtls">
        <legend style="width:800px;text-align: center;">Size Specification & Trim Specification</legend>
            <table style="margin-left: 150px;" cellpadding="0" cellspacing="0" width="600px" class="" border="1" rules="all" id="tblChecklistDtls">
                <tbody id="sample_details_container">
                <?
                $i=1;
                foreach($sample_checklist_set as $id=>$name)
                {
					
					?> 
					<tr id="tr_<? echo $i; ?>" style="height:10px;" > 
                        <th align="left">   
						<b><input type="text" style="width: 80px;" class="datepicker" name="txtsubmitdate_<?php echo $i ?>" id="txtsubmitdate_<?php echo $i ?>" placeholder="Date" />  &nbsp; 
						<input type="text" style="width: 100px;"  class="text_boxes" name="txtremarks_<?php echo $i ?>"  id="txtremarks_<?php echo $i ?>" placeholder="Remarks" />  <b>	
						<input type="checkbox" class=""  name="txtCheckBoxId_<? echo $i ?>" id="txtCheckBoxId_<? echo $i ?>" />&nbsp; <? echo $name; ?>
						&nbsp; 
                            <input type="hidden" name="txtDocumentSetArrayid_<?php echo $i ?>" id="txtDocumentSetArrayid_<?php echo $i ?>" value="<? echo $id; ?>"/>
                            <input type="hidden" name="updateDtlsId_<?php echo $i ?>" id="updateDtlsId_<?php echo $i ?>" />
                        </th>
					</tr>
					<?
					$i++;
                }
                ?>
				  
                </tbody>        
            </table>
            <div style=" float:left"><p style=" float:left"> <input type="checkbox" name="checkall" id="checkall" class="formbutton" value="2" onClick="check_all_data();"/><b>Check all</b></p> 
			<td><input type="button" class="image_uploader" style="width:150px" value="ADD/VIEW IMAGE" onClick="file_uploader ( '../', document.getElementById('update_id').value,'', 'sample_checklist_pattern_img', 0 ,1)"></td>
            </div>
            <table>
              
                
                <tr>
                    
                    
                    <td colspan="7" height="40" valign="bottom" align="center" class="">
                    	<? echo load_submit_buttons( $permission, "fnc_sample_checklist_dtls", 0,0 ,"button_status(2)",2); ?>
                    </td>	
                </tr>
            </table>
        </fieldset>
    </form>
	</div>
    <script src="../includes/functions_bottom.js" type="text/javascript"></script>
</body>
</html>