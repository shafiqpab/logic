<?
/*-------------------------------------------- Comments ----------------------------------------
Purpose			: 	This form will create Short Fabric Requisition[Knit]
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	16-01-2024	
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
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');
//--------------------------------------------------------------------------------------------------------------------

echo load_html_head_contents("Short Fabric Requisition[Knit]", "../../", 1, 1,$unicode,1,'');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';
	$(document).ready(function(){
		$("#cbo_brand_id").prop("disabled", true);
		$("#cbo_season_id").prop("disabled", true);
		$("#cbo_season_year").prop("disabled", true);
		$("#txt_rd_no").prop("disabled", true);
		$("#txt_fabric_ref").prop("disabled", true);
	});

	function openmypage_order(page_link,title)
	{
		if(document.getElementById('id_approved_id').value==1 || document.getElementById('id_approved_id').value==3)
		{
			alert("This Requisition is Approved.")
			return;
		}
		if (form_validation('cbo_company_name*cbo_buyer_name*cbo_fabric_natu*cbo_fabric_source','Company Name*Buyer Name*Fabric Nature*Fabric Source')==false)
		{
			return;
		}
		
		var txt_reqsn_no=document.getElementById('txt_reqsn_no').value;
		var check_is_reqsn_used_id=return_global_ajax_value(txt_reqsn_no, 'check_is_requisition_used', '', 'requires/short_fabric_requisition_controller');
		if(trim(check_is_reqsn_used_id) !="")
		{
			alert("This Requisition used in Short Fabric Booking. So Adding or removing order is not allowed")
			return;
		}
		else
		{
			if(txt_reqsn_no=="")
			{
				page_link=page_link+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_reqsn_date*cbo_brand_id*cbo_season_id*cbo_season_year','../../');
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=450px,center=1,resize=1,scrolling=0','../')
			}
			else
			{
				var r=confirm("Existing Item against these Order Will be Deleted")
				if(r==true)
				{
					var delete_requisition_item=return_global_ajax_value(txt_reqsn_no, 'delete_requisition_item', '', 'requires/short_fabric_requisition_controller');
					show_list_view(txt_reqsn_no,'show_fabric_requisition','requisition_list_view','requires/short_fabric_requisition_controller','setFilterGrid(\'list_view\',-1)');
					page_link=page_link+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_reqsn_date*cbo_brand_id*cbo_season_id*cbo_season_year','../../');
					emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=450px,center=1,resize=1,scrolling=0','../')
				}
				else
				{
					return;
				}
			}
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];;
				var id=this.contentDoc.getElementById("po_number_id");
				var po=this.contentDoc.getElementById("po_number");
				if (id.value!="")
				{
					freeze_window(5);
					reset_form('orderdetailsentry_2','requisition_list_view','','','')
					document.getElementById('txt_order_no_id').value=id.value;
					document.getElementById('txt_order_no').value=po.value;
					var cbo_fabric_natu =document.getElementById('cbo_fabric_natu').value
					var cbo_fabric_source=document.getElementById('cbo_fabric_source').value
				
					var cbouom=document.getElementById('cbouom').value
					get_php_form_data( id.value, "populate_order_data_from_search_popup", "requires/short_fabric_requisition_controller" );
				
					//var reportId=document.getElementById('report_ids').value;
					//print_report_button_setting(reportId);
					
					fnc_get_po_config(id.value+'_'+cbo_fabric_natu+'_'+cbo_fabric_source+'_'+cbouom);
					
					release_freezing();
					fnc_generate_booking()
				}
			}
		}
	}
	
	function set_process_loss(str)
	{
		var prosess_loss=return_global_ajax_value(str, 'prosess_loss_set', '', 'requires/short_fabric_requisition_controller');
		get_php_form_data(str, 'prosess_loss_set_2', 'requires/short_fabric_requisition_controller');
		document.getElementById('txt_process_loss').value=trim(prosess_loss);
		calculate_requirement();
	}

	function openmypage_requisition(page_link,title)
	{
		var company=$("#cbo_company_name").val()*1;
		var buyer=$("#cbo_buyer_name").val()*1;
		var brand_id=$("#cbo_brand_id").val()*1;
		var season_id=$("#cbo_season_id").val()*1;
		//alert(company);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&company='+company+'&buyer_id='+buyer+'&cbo_brand_id='+brand_id+'&cbo_season_id='+season_id, title, 'width=1070px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_booking");
			if (theemail.value!="")
			{
				freeze_window(5);
				reset_form('fabricreqsn_1','requisition_list_view','', 'txt_reqsn_date,<? echo date("d-m-Y"); ?>');
				get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/short_fabric_requisition_controller" );
				//var reportId=document.getElementById('report_ids').value;
	
				//print_report_button_setting(reportId);
				reset_form('orderdetailsentry_2','requisition_list_view','','','')
				var txt_order_no_id=document.getElementById('txt_order_no_id').value
				var cbo_fabric_natu =document.getElementById('cbo_fabric_natu').value
				var cbo_fabric_source=document.getElementById('cbo_fabric_source').value
				var cbouom=document.getElementById('cbouom').value
				
				//fnc_get_po_config(txt_order_no_id+'_'+cbo_fabric_natu+'_'+cbo_fabric_source+'_'+cbouom);
				
				show_list_view(theemail.value,'show_fabric_requisition','requisition_list_view','requires/short_fabric_requisition_controller','setFilterGrid(\'list_view\',-1)');
				set_button_status(1, permission, 'fnc_short_fabric_requisition',1);
				release_freezing();
			}
		}
	}
	
	function fnc_get_po_config(data)
	{
		var exdata=data.split('_');
		var po_id=exdata[0];
		var fabricnature=exdata[1];
		var fabricsource=exdata[2];
		var fabricuom=exdata[3];
		get_php_form_data(po_id+'_'+fabricnature+'_'+fabricsource+'_'+fabricuom,'get_po_config','requires/short_fabric_requisition_controller' );
	}

	function calculate_requirement()
	{
		var cbo_company_name= document.getElementById('cbo_company_name').value;
		var cbo_fabric_natu= document.getElementById('cbo_fabric_natu').value
		var process_loss_method_id=return_global_ajax_value(cbo_company_name+'_'+cbo_fabric_natu, 'process_loss_method_id', '', 'requires/short_fabric_requisition_controller');
		var txt_finish_qnty=(document.getElementById('txt_finish_qnty').value)*1;
		var processloss=(document.getElementById('txt_process_loss').value)*1;
		var WastageQty='';
		if(process_loss_method_id==1)
		{
			WastageQty=txt_finish_qnty+txt_finish_qnty*(processloss/100);
		}
		else if(process_loss_method_id==2)
		{
			var devided_val = 1-(processloss/100);
			var WastageQty=parseFloat(txt_finish_qnty/devided_val);
		}
		else
		{
			WastageQty=0;
		}
		WastageQty= number_format_common( WastageQty, 5, 0) ;
		document.getElementById('txt_grey_qnty').value= WastageQty;
	}

	function fnc_short_fabric_requisition( operation )
	{
		freeze_window(operation);
		if(operation==2)
		{
			alert("Delete Restricted");
			release_freezing();
			return;
		}
		if(document.getElementById('id_approved_id').value==1)
		{
			alert("This Booking is Approved")
			release_freezing();
			return;
		}
	
		var delivery_date=$('#txt_delivery_date').val();
		if(date_compare($('#txt_reqsn_date').val(), delivery_date)==false)
		{
			alert("Required Date Not Allowed Less than Requisition Date");
			release_freezing();
			return;
		}
		
		if (form_validation('cbo_company_name*cbo_buyer_name*txt_order_no_id*txt_reqsn_date*cbo_fabric_natu*cbo_fabric_source*txt_delivery_date','Company Name*Buyer Name*Order No*Requisition Date*Fabric Nature*Fabric Source*Required Date')==false)
		{
			release_freezing();
			return;
		}
		else
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_order_no_id*update_id*cbo_company_name*cbo_buyer_name*txt_job_no*txt_reqsn_no*cbo_fabric_natu*cbo_fabric_source*txt_reqsn_date*txt_delivery_date*cbo_ready_to_approved*cbo_short_booking_type*cbouom*txt_remark*cbo_season_year*cbo_season_id*cbo_brand_id',"../../");
			
			http.open("POST","requires/short_fabric_requisition_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_short_fabric_requisition_reponse;
		}
	}

	function fnc_short_fabric_requisition_reponse(){
	
		if(http.readyState == 4){
			 var reponse=trim(http.responseText).split('**');
			 
			 if(trim(reponse[0])=='approved'){
				alert("This requisition is approved");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='shortBookingno'){
				alert("Short Booking Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}
			 
			show_msg(trim(reponse[0]));
			if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1 || parseInt(trim(reponse[0]))==2){
				document.getElementById('txt_reqsn_no').value=reponse[1];
				document.getElementById('update_id').value=reponse[2];
				set_button_status(1, permission, 'fnc_short_fabric_requisition',1);
				release_freezing();
			}
			release_freezing();
		}
	}

	function fnc_short_fabric_requisition_dtls( operation )
	{
		freeze_window(operation);
		/*if(operation==2)
		{
			alert("Delete Restricted")
			release_freezing();
			return;
		}*/
		if(document.getElementById('id_approved_id').value==1)
		{
			alert("This Requisition is approved");
			release_freezing();
			return;
		}
		if(document.getElementById('cbo_order_id').value==0)
		{
			alert("Select Po No");
			release_freezing();
			return;
		}
		
		if (form_validation('txt_order_no_id*txt_reqsn_date*txt_reqsn_no*cbo_order_id*cbo_fabricdescription_id*cbo_garmentscolor_id*cbo_fabriccolor_id*cbo_garmentssize_id*txt_dia_width*txt_finish_qnty','Order No*Requisition Date*Requisition No*Po No*Fabric Description*Garments Color*Fabric color*Garments Size*Dia Width*Finish Qty')==false)
		{
			release_freezing();
			return;
		}
		
		var data="action=save_update_delete_dtls&operation="+operation+get_submitted_data_string('txt_reqsn_no*update_id*txt_job_no*cbo_order_id*cbo_fabricdescription_id*cbo_fabriccolor_id*cbo_garmentscolor_id*cbo_itemsize_id*cbo_garmentssize_id*txt_dia_width*txt_finish_qnty*txt_process_loss*txt_grey_qnty*txt_rmg_qty*cbo_responsible_dept*cbo_responsible_person*txt_reason*update_id_details*txt_fabric_weight',"../../");
		
		//alert(data); release_freezing(); return;
		
		http.open("POST","requires/short_fabric_requisition_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_short_fabric_requisition_dtls_reponse;
	}

	function fnc_short_fabric_requisition_dtls_reponse(){
		if(http.readyState == 4){
			 var reponse=http.responseText.split('**');
			 
			 if(trim(reponse[0])=='approved'){
				alert("This Requisition is approved");
				release_freezing();
				return;
			}
			/*if(trim(reponse[0])=='sal1'){
				alert("Sales Order  found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
				release_freezing();
				return;
			}*/
			if(trim(reponse[0])=='shortBookingno'){
				alert("Short Booking Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='recv1'){
				alert("Receive Number Found :"+trim(reponse[2])+"\n So Delete Not Possible")
			    release_freezing();
			    return;
		    }
			show_msg(trim(reponse[0]));
			if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1 || parseInt(trim(reponse[0]))==2){
				reset_form('orderdetailsentry_2','requisition_list_view','','','','cbo_order_id*cbo_fabricdescription_id*txt_rd_no*txt_fabric_ref*txt_fabric_weight*cbo_fabriccolor_id*cbo_garmentscolor_id')
				set_button_status(0, permission, 'fnc_short_fabric_requisition_dtls',2);
				show_list_view(reponse[1],'show_fabric_requisition','requisition_list_view','requires/short_fabric_requisition_controller','setFilterGrid(\'list_view\',-1)');
			}
			 
			release_freezing();
		}
	}

	function generate_fabric_report(type,report_type)
	{
		if (form_validation('txt_reqsn_no','Requisition No')==false)
		{
			return;
		}
		else
		{
			var show_yarn_rate='';
			var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
			if (r==true)
			{
				show_yarn_rate="1";
			}
			else
			{
				show_yarn_rate="0";
			}
			var report_title=$( "div.form_caption" ).html();
	
			var data="action="+type+get_submitted_data_string('txt_reqsn_no*cbo_company_name*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*id_approved_id*txt_job_no',"../../")+'&report_title='+report_title+'&show_yarn_rate='+show_yarn_rate+'&report_type='+report_type+'&path=../../';
			freeze_window(5);
			http.open("POST","requires/short_fabric_requisition_controller.php",true);
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
			$('#pdf_file_name').html(file_data[1]);
			$('#data_panel').html(file_data[0] );
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
			release_freezing();
		}
	}

	function print_report_button_setting(report_ids)
	{
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
			//alert(report_id[k]);
			if(report_id[k]==8)
			{
				$("#print").show();
			}
		}
	}
	
	function buyer_select()
	{
		if($('#cbo_buyer_name option').length==2)
		{
			if($('#cbo_buyer_name option:first').val()==0)
			{
				$('#cbo_buyer_name').val($('#cbo_buyer_name option:last').val());
				//eval($('#cbo_buyer_name').attr('onchange')); 
			}
		}
		else if($('#cbo_buyer_name option').length==1)
		{
			$('#cbo_buyer_name').val($('#cbo_buyer_name option:last').val());
			//eval($('#cbo_buyer_name').attr('onchange'));
		}	
	}
	
	function fnc_brandload()
	{
		var buyer=$('#cbo_buyer_name').val();
		if(buyer!=0)
		{
			load_drop_down( 'requires/short_fabric_requisition_controller', buyer, 'load_drop_down_brand', 'brand_td');
		}
	}
</script>

</head>

<body onLoad="set_hotkey(); buyer_select(); fnc_brandload();">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?>
        <form name="fabricreqsn_1"  autocomplete="off" id="fabricreqsn_1">
            <fieldset style="width:1010px;">
            <legend>Short Fabric Requisition[Knit]</legend>
            <table width="1010" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td colspan="4" align="right" class="must_entry_caption"><b>Requisition No</b></td>
                    <td colspan="4">
                    	<input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_requisition('requires/short_fabric_requisition_controller.php?action=fabric_requisition_popup','Fabric Requisition Search');" readonly placeholder="Double Click for Requisition" name="txt_reqsn_no" id="txt_reqsn_no"/>
                        <input type="hidden" id="id_approved_id">
                        <input type="hidden" id="update_id">
                    </td>
                </tr>
                <tr>
                    <td width="100" class="must_entry_caption">Company Name</td>
                    <td width="150"><?=create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected, "load_drop_down( 'requires/short_fabric_requisition_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",0,"" ); ?>
                        <input type="hidden" id="report_ids">
                    </td>
                    <td width="100" class="must_entry_caption">Buyer Name</td>
                    <td width="150" id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 130, "select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,"" ); ?></td>
                    <td width="100">Brand</td>
                    <td width="150" id="brand_td"><?= create_drop_down( "cbo_brand_id", 130, $blank_array,'', 1, "--Brand--",$selected );?></td>
                    <td width="100">Season <? echo create_drop_down( "cbo_season_year", 50, create_year_array(),"", 1,"-Year-", 0, "",0,"" ); ?></td>
                    <td id="season_td"><? echo create_drop_down( "cbo_season_id", 130, $blank_array,'', 1, "--Season--",$selected, "" ); ?></td>
                </tr>
                <tr>
                	<td class="must_entry_caption">Requisition Date:</td>
                    <td><input class="datepicker" type="text" style="width:120px" name="txt_reqsn_date" id="txt_reqsn_date" value="<?=date("d-m-Y"); ?>" disabled /></td>
                    <td class="must_entry_caption">Fabric Nature</td>
                    <td>
						<?
                        echo create_drop_down( "cbo_fabric_natu", 78, $item_category,"", 1, "-- Select --", 2,$onchange_func, $is_disabled, "2,3");
                        echo create_drop_down( "cbouom", 50, $unit_of_measurement,'', 1, '-Uom-', $row[csf('uom')], "",$disabled,"1,12,23,27" );
                        ?>
                    </td>
                    <td class="must_entry_caption">Fabric Source</td>
                    <td><?=create_drop_down( "cbo_fabric_source", 130, $fabric_source,"", 1, "-- Select --", 1,"enable_disable(this.value);", "", ""); ?></td>
                    <td class="must_entry_caption">Required Date</td>
                    <td><input class="datepicker" type="text" style="width:120px" name="txt_delivery_date" id="txt_delivery_date"/></td>
                </tr>
                <tr>
                    <td>Job No.</td>
                    <td><input style="width:120px;" type="text" class="text_boxes"  name="txt_job_no" id="txt_job_no" disabled  /></td>
                    <td class="must_entry_caption">Order No</td>
                    <td colspan="3">
                        <input class="text_boxes" type="text" style="width:370px;" placeholder="Double click for Order"  onDblClick="openmypage_order('requires/short_fabric_requisition_controller.php?action=order_search_popup','Order Search');" name="txt_order_no" id="txt_order_no"/>
                        <input class="text_boxes" type="hidden" style="width:100px;"  name="txt_order_no_id" id="txt_order_no_id"/>
                    </td>
                    <td>Internal Ref No:</td>
                    <td><Input name="txt_intarnal_ref" class="text_boxes" readonly placeholder="Display" ID="txt_intarnal_ref" style="width:120px" ></td>
                </tr>
                <tr>
                	<td>File no</td>
                    <td ><Input name="txt_file_no" class="text_boxes" readonly placeholder="Display" ID="txt_file_no" style="width:120px" ></td>
                    <td class="<?=$short_booking_mendatory; ?>">Short Booking Type</td>
                    <td><?=create_drop_down( "cbo_short_booking_type", 130, $short_booking_type,"", 1, "-- Select--", "", "","","" ); ?></td>
                    <td>Ready To Approved</td>
                    <td><?=create_drop_down( "cbo_ready_to_approved", 130, $yes_no,"", 1, "-- Select--", 2, "","","" ); ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>Remarks</td>
                    <td colspan="3"><textarea class="text_area" type="text" maxlength="300" style="width:370px; height:30px;" name="txt_remark" id="txt_remark" placeholder="Remarks"/></textarea></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td align="center" colspan="8" valign="top" id="app_sms2" style="font-size:18px; color:#F00"></td>
                </tr>
                <tr>
                    <td align="center" colspan="8" valign="middle" class="button_container">
                    <? $date=date('d-m-Y'); echo load_submit_buttons( $permission, "fnc_short_fabric_requisition", 0,0 ,"reset_form('fabricreqsn_1','','requisition_list_view','cbo_ready_to_approved,2*txt_reqsn_date,".$date."')",1) ; ?>
                    </td>
                </tr>
           </table>
            </fieldset>
        </form>
        <br/>
        <form name="orderdetailsentry_2"  autocomplete="off" id="orderdetailsentry_2">
            <fieldset style="width:1010px;">
            <legend>Short Fabric Requisition Details</legend>
            <table width="1010" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td class="must_entry_caption">PO No</td>
                    <td id="order_drop_down_td"><?=create_drop_down( "cbo_order_id", 130, $blank_array,"", 1, "--Select--", $selected, "" ); ?></td>
                    <td class="must_entry_caption" >Fabric Description</td>
                    <td colspan="3" id="fabricdescription_id_td"><?=create_drop_down( "cbo_fabricdescription_id", 382, $blank_array,"", 1, "--Select--", $selected, "" ); ?></td>
                    <td>RD No</td>
                    <td><input name="txt_rd_no" id="txt_rd_no" class="text_boxes_numeric" type="text" value=""  style="width:120px " readonly/></td>
                </tr>
                <tr>
                    <td width="100">Construction</td>
                    <td width="150"><input name="txt_fabric_ref" id="txt_fabric_ref" class="text_boxes" type="text" style="width:120px" /></td>
                    <td width="100">GSM</td>
                    <td width="150"><input name="txt_fabric_weight" id="txt_fabric_weight" class="text_boxes_numeric" type="text" style="width:120px" disabled="" /></td>
                    <td width="100" class="must_entry_caption">Dia/ Width</td>
                    <td width="150"><input name="txt_dia_width" id="txt_dia_width" class="text_boxes" type="text" placeholder="Write" style="width:120px "/></td>
                    <td width="100">RMG Qty</td>
                    <td><input name="txt_rmg_qty" id="txt_rmg_qty" class="text_boxes_numeric" type="text" style="width:120px " /></td>
                </tr>
                <tr>
                    <td class="must_entry_caption">Garments Color </td>
                    <td id="garmentscolor_id_id_td" ><?=create_drop_down( "cbo_garmentscolor_id", 130, $blank_array,"", 1, "--Select--", $selected, "" ); ?></td>
                    <td class="must_entry_caption">Fabric Color</td>
                    <td id="fabriccolor_id_id_td"><?=create_drop_down( "cbo_fabriccolor_id", 130, $blank_array,"", 1, "--Select--", $selected, "" ); ?></td>
                    <td class="must_entry_caption">Garments size</td>
                    <td id="garmentssize_id_td"><?=create_drop_down( "cbo_garmentssize_id", 130, $blank_array,"", 1, "--Select--", $selected, "" ); ?></td>
                    <td>Item size</td>
                    <td id="itemsize_id_td"><?=create_drop_down( "cbo_itemsize_id", 130, $blank_array,"", 1, "--Select--", $selected, "" ); ?></td>
                </tr>
                <tr>
                    <td class="must_entry_caption">Finish Fabric</td>
                    <td><input name="txt_finish_qnty" id="txt_finish_qnty" class="text_boxes_numeric" type="text" onChange="calculate_requirement();" style="width:120px"/></td>
                    <td>Process loss </td>
                    <td><input name="txt_process_loss" id="txt_process_loss" class="text_boxes_numeric" type="text" onChange="calculate_requirement();" style="width:120px" /></td>
                    <td>Gray Fabric</td>
                    <td><input name="txt_grey_qnty" id="txt_grey_qnty" class="text_boxes_numeric" type="text" style="width:120px" readonly/></td>
                    <td>Responsible Dept.</td>
                    <td><?=create_drop_down( "cbo_responsible_dept", 130,"select id,department_name from  lib_department where status_active=1 and is_deleted=0 order by  department_name", "id,department_name", 0, "", '', '', $onchange_func_param_db,$onchange_func_param_sttc ); ?></td>
                </tr>
                <tr>
                    <td>Responsible Person</td>
                    <td><input name="cbo_responsible_person" id="cbo_responsible_person" class="text_boxes" type="text" style="width:120px "/></td>
                    <td>Reason</td>
                    <td colspan="3"><input name="txt_reason" id="txt_reason" class="text_boxes" type="text" style="width:375px "/></td>
                    <td><input type="hidden" id="update_id_details"></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td align="center" colspan="8" valign="middle" class="button_container">
						<?=load_submit_buttons( $permission, "fnc_short_fabric_requisition_dtls", 0,0 ,"reset_form('orderdetailsentry_2','','','','')",2) ; ?>
                        <!--<input type="button" value="Print Booking" onClick="generate_fabric_report('show_fabric_requisition_report',1)"  style="width:100px; display:none;" name="print" id="print" class="formbutton" />
                        <input type="button"  value="Print Booking2" onClick="generate_fabric_report('show_fabric_requisition_report3',1)"  style="width:100px; display:none;" name="print_booking3" id="print_booking3" class="formbutton" />
                        <input type="button"  value="Fabric Booking" onClick="generate_fabric_report('show_fabric_requisition_report4',1)"  style="width:100px; display:none;" name="print_booking4" id="print_booking4" class="formbutton" />
                        <input type="button"  value="Print Booking Urmi" onClick="generate_fabric_report('show_fabric_requisition_report_urmi',1)"  style="width:110px; display:none;" name="print_booking_urmi" id="print_booking_urmi" class="formbutton" />
                        <input type="button" value="Print 3 " onClick="generate_fabric_report('print_booking_3',1)"  style="width:130px;display:none;" name="print_booking_3" id="print_booking_3" class="formbutton" />
                        <input type="button"  value="Fabric Booking Report" onClick="generate_fabric_report('fabric_booking_report',1)"  style="width:140px; display:none;" name="fabric_booking_report" id="fabric_booking_report" class="formbutton" />
						<input type="button"  value="Fabric Booking Report 2" onClick="generate_fabric_report('fabric_booking_report_2',1)"  style="width:140px; display:none;" name="fabric_booking_report_2" id="fabric_booking_report_2" class="formbutton" />-->
                        <div id="pdf_file_name" style="display: none;"></div>
                    </td>
                </tr>
            </table>
            </fieldset>
        </form>
        <br/>
        <fieldset style="width:1110px;">
            <legend>List View</legend>
            <table style="border:none" cellpadding="0" cellspacing="2" border="0">
                <tr align="center">
                	<td id="requisition_list_view"></td>
                </tr>
            </table>
        </fieldset>
	</div>
   <div style="display:none" id="data_panel"></div>
</body>

<script>
	set_multiselect('cbo_responsible_dept','0','0','','0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>