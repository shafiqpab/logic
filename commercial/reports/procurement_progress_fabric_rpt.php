<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Procurement Progress Report [Fabric].
Functionality	:	
JS Functions	:
Created by		:	Nayem
Creation date 	: 	30-4-2022
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
echo load_html_head_contents("Procurement Progress Report [Fabric]", "../../",  1, 1, $unicode,1,'');
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission = '<? echo $permission; ?>';
	
	function generate_report()
	{
		if($('#txt_booking_id').val()=="" && $('#txt_job_no').val()=="")
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date*Date')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_name','Company Name')==false)
			{
				return;
			}
		}

		var report_title=$( "div.form_caption" ).html();	
		var data="action=report_generate"+get_submitted_data_string("cbo_company_name*cbo_buyer_id*txt_job_id*txt_booking_id*cbo_shipment_status*cbo_date_type*txt_date_from*txt_date_to*cbo_rcv_status","../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/procurement_progress_fabric_rpt_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			//alert(response[0]);
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			release_freezing();
			
		}
	}
		
	function new_window()
	{	
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 		
	}
	
	function openmypage_popup(receive_basis,pi_id,booking_id,receive_basis,page_title,action)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/procurement_progress_fabric_rpt_controller.php?receive_basis='+receive_basis+'&pi_id='+pi_id+'&booking_id='+booking_id+'&receive_basis='+receive_basis+'&action='+action, page_title, 'width=650px,height=400px,center=1,resize=0,scrolling=0','../');
	}
		
	function openmypage_job()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_job_no').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/procurement_progress_fabric_rpt_controller.php?action=job_no_popup&data='+data,'Order No Popup', 'width=630px,height=380px,center=1,resize=0','../')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("hdn_job_info");
			if (theemail.value!="")
			{
				freeze_window(5);
                var response=theemail.value.split('_');
				document.getElementById("txt_job_id").value=response[0];
			    document.getElementById("txt_job_no").value=response[1];
			    document.getElementById("txt_style_no").value=response[2];
                disable_enable_fields('txt_job_no*txt_style_no',1);
				release_freezing();
			}
		}
	}

	function openmypage_booking()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_id').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/procurement_progress_fabric_rpt_controller.php?action=booking_no_popup&data='+data,'Booking No Popup', 'width=630px,height=380px,center=1,resize=0','../')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("hdn_booking_info");
			if (theemail.value!="")
			{
				freeze_window(5);
                var response=theemail.value.split('_');
				document.getElementById("txt_booking_id").value=response[0];
			    document.getElementById("txt_booking_no").value=response[1];
				release_freezing();
			}
		}
	}

	function openmypage_po(po_id,page_title,action)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/procurement_progress_fabric_rpt_controller.php?po_id='+po_id+'&action='+action, page_title, 'width=650px,height=400px,center=1,resize=0,scrolling=0','../');
		emailwindow.onclose=function()
		{
		}
	}

	function openmypage_rcv(rcv_id,prod_id,page_title,action)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/procurement_progress_fabric_rpt_controller.php?rcv_id='+rcv_id+'&prod_id='+prod_id+'&action='+action, page_title, 'width=650px,height=400px,center=1,resize=0,scrolling=0','../');
		emailwindow.onclose=function()
		{
		}
	}

</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",''); ?>
    <form id="procurementProgressFabric_rpt" name="procurementProgressFabric_rpt">
    <div style="width:1240px;">
    <h3 align="left" id="accordion_h1" style="width:1240px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
    <div id="content_search_panel"> 
    <fieldset style="width:1240px;">
		<table class="rpt_table" cellspacing="0" cellpadding="0" width="1220" rules="all">
			<thead>
				<tr>
					<th width="140" class="must_entry_caption">Company</th>
					<th width="140">Buyer</th>
					<th width="130">Style</th>                    
					<th width="80">Job</th>
					<th width="100">Booking No.</th>
					<th width="100">Shipment Status</th>
					<th width="110">Date Type</th>
					<th width="90" class="must_entry_caption">Date From</th>
					<th width="90" class="must_entry_caption">Date To</th>
					<th width="100">Material Rec Status</th>
					<th ><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('procurementProgressFabric_rpt','report_container*report_container2','','','')" /></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td align="center"> 
						<?
							echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/procurement_progress_fabric_rpt_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
						?>
					</td>
					<td align="center" id="buyer_td">
						<? echo create_drop_down( "cbo_buyer_id", 130, $blank_array,"", 1,"-- Select --",0,"" ); ?>
					</td>
					<td align="center">
						<input type="text" name="txt_style_no" id="txt_style_no" value="" class="text_boxes" style="width:120px" placeholder="Browse" onDblClick="openmypage_job();" readonly/>
					</td>
					<td align="center">
						<input type="text" name="txt_job_no" id="txt_job_no" value="" class="text_boxes" style="width:80px" placeholder="Browse" onDblClick="openmypage_job();" readonly/>
						<input type="hidden" name="txt_job_id" id="txt_job_id">
					</td>
					<td align="center">
						<input type="text" id="txt_booking_no" name="txt_booking_no" style="width:90px;" class="text_boxes"  placeholder="Browse" onDblClick="openmypage_booking();" readonly />
						<input type="hidden" name="txt_booking_id" id="txt_booking_id">
					</td>
					<td align="center">
						<? echo create_drop_down( "cbo_shipment_status", 100, $shipment_status, "", 1, "-- Select --", 0, "", "", ""); ?>
					</td>
					<td> 
						<? $date_type=array(1=>'Booking date',2=>'PO Ship Date',3=>'Buyer PO Rev date');
						echo create_drop_down( "cbo_date_type", 100, $date_type, "", 0, "-- Select --", 0, "", "", ""); ?>
					</td>
					<td align="center">
						<input name="txt_date_from" id="txt_date_from" class="datepicker"  style="width:80px">
					</td>
					<td align="center">
						<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px">
					</td>
					<td align="center">
						<? $rcv_status=array(1=>"Full",2=>"Pending");
							echo create_drop_down( "cbo_rcv_status", 120, $rcv_status,"", 0, "-- Select --", $selected,"",0,"" ); ?>
					</td>
					<td align="center">
						<input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:70px" class="formbutton" />
					</td>
				</tr>
				<tr class="general">
					<td colspan="11" align="center"><? echo load_month_buttons(1);  ?></td>
				</tr>
			</tbody>
        </table>
    </fieldset>
    </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
    </form>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>