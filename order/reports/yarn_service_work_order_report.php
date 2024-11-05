<?
/*-------------------------------------------- Comments -----------------------
Purpose			:	This Form Will Create Service Booking Status For Dyeing.
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	04-03-2019
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
echo load_html_head_contents("Service Booking Status For Dyeing", "../../", 1, 1,$unicode,1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';	

	var tableFilters = 
	{
		col_15: "select",
		col_operation: {
			id: ["value_total_wo_qnty","value_total_issue","value_total_issue_returnable","value_total_issue_return","value_total_balance"],
		    col: [15,16,17,18,19],
		    operation: ["sum","sum","sum","sum","sum"],
		    write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 
	
		
	function openmypage_po()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		else
		{	
			var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_id").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/yarn_service_work_order_report_controller.php?data='+data+'&action=FSO_No_popup', 'PO No Search', 'width=740px,height=420px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var fso_no=this.contentDoc.getElementById("hide_fso_no").value;
				var fso_id=this.contentDoc.getElementById("hide_fso_id").value;
				//alert (job_no);
				$('#fso_number_show').val(fso_no);
				$('#fso_number').val(fso_id);
			}
		}
	}
	
	function fn_report_generated(operation)
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		else
		{	
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_service_type_id*txt_wo_no*fso_number_show*fso_number*txt_date_from*txt_date_to*txt_ir_no',"../../")+'&report_title='+report_title;
			freeze_window(3);
			http.open("POST","requires/yarn_service_work_order_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("**");
			//var tot_rows=reponse[2];
			$('#report_container4').html(reponse[0]);
			//document.getElementById('report_container3').innerHTML=report_convert_button('../../');
			document.getElementById('report_container3').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
	 		show_msg('3');
			setFilterGrid("table_body",-1,tableFilters);
			release_freezing();
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		$("#table_body tr:first").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+document.getElementById('report_container4').innerHTML+'</body</html>');
		d.close(); 
		$("#table_body tr:first").show();
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="350px";
	}
	

	function generate_print(bookingdata)
	{
		var exbookingdata=bookingdata.split("__");
		var booking_no=exbookingdata[0];
		var entry_form=exbookingdata[1];
		
		var cbo_company_name = $("#cbo_company_id").val();
		var show_comments='';
		var r=confirm("Press  \"Ok\"  to Hide  Comments\nPress  \"Cancel\"  to Show Comments");
				//alert(r)
		if (r==true) show_comments="1"; else show_comments="0";
		
		var data="action=show_trim_booking_report&txt_wo_no='"+booking_no+"'&cbo_company_name="+cbo_company_name+'&show_comments='+show_comments;
		
		
		if(entry_form==229)
		{
			http.open("POST","../woven_order/requires/service_booking_multi_job_wise_dyeing_controller.php",true);
		}
		else
		{
			http.open("POST","../woven_order/requires/service_booking_dyeing_controller.php",true);
		}
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_print_reponse;
	}

	function generate_print_reponse()
	{
		if(http.readyState == 4) 
		{
			var file_data=http.responseText.split('****');
			//$('#pdf_file_name').html(file_data[1]);
			$('#data_panel').html(file_data[0] );
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
		}
	}
	function openmypage_booking()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_id").val();
		page_link='requires/yarn_service_work_order_report_controller.php?action=yern_service_wo_popup&company='+company;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Yarn Dyeing Booking Search', 'width=885px, height=450px, center=1, resize=0, scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var sys_number=this.contentDoc.getElementById("hidden_sys_number").value.split("_");
			$('#txt_wo_no').val(sys_number[1]);
		}
	}
	function openmypage(booking_id,issue_purpose,fso_job_no,product_id,type,action)
	{ 
		var companyID = $("#cbo_company_id").val();
		var popup_width='470px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/yarn_service_work_order_report_controller.php?companyID='+companyID+'&booking_id='+booking_id+'&issue_purpose='+issue_purpose+'&fso_job_no='+fso_job_no+'&product_id='+product_id+'&type='+type+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=300px,center=1,resize=0,scrolling=0','../../');
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission); ?>    		 
    <form name="wofbreport_1" id="wofbreport_1" autocomplete="off" > 
    <h3 style="width:850px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" >
            <fieldset style="width:680px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>
                	<tr>
	                    <th width="120" class="must_entry_caption">Working Company</th>
	                    <th width="120">Buyer</th>
	                    <th width="80">Sales Order No</th>
						<th width="100">IR/IB</th>
	                    <th width="120">Service Type</th>
	                    <th width="80">Wo No.</th>
	                    <th width="140" colspan="2">Wo Date Range</th>
	                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:90px" value="Reset" onClick="reset_form( 'wofbreport_1', 'report_container3*report_container4', '','','')" /></th>
                    </tr>
                </thead>
                 <tbody>
                    <tr class="general">
                        <td><? echo create_drop_down( "cbo_company_id", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down('requires/yarn_service_work_order_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td');" );
                        	//load_drop_down('requires/yarn_service_work_order_report_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );
                         ?></td>  
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_id", 120, $blank_array,"", 1, "--Select Buyer--", $selected, "",1,"" ); ?></td>                      
                        <td>
                        	 <input type="text"  name="fso_number_show" id="fso_number_show" class="text_boxes" style="width:120px;" tabindex="1" placeholder="Browse" onDblClick="openmypage_po();" readonly>
                                     <input type="hidden" name="fso_number" id="fso_number">
                        </td>
						<td><input name="txt_ir_no" id="txt_ir_no" class="text_boxes" style="width:100px"></td>
                        <td><? echo create_drop_down( "cbo_service_type_id", 120, $yarn_issue_purpose,"", 1, "--Select--", $selected, "",0,"12,15,38,46,7,50,51" ); ?></td>
                        <td>
                            <input class="text_boxes" type="text" style="width:120px" onDblClick="openmypage_booking();" placeholder="Write/Browse" name="txt_wo_no" id="txt_wo_no" />
                        </td>
                        <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date" value="<? //echo date("d-m-Y");?>"></td>
                        <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date"  value=""></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:90px" value="Show" onClick="fn_report_generated(0)" /></td>
                    </tr>
                    <tr>
                        <td colspan="11" align="center"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </tbody>
            </table> 
        	</fieldset>
        </div>
        <div id="report_container3" align="center"></div>
        <div id="report_container4" align="left"></div>
    </form> 
</div>
   <div style="display:none" id="data_panel"></div>
</body>
<script>//set_multiselect('cbo_wo_type','0','0','','0');</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
