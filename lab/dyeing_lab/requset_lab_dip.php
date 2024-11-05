<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Request For Lab Dip
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	29-12-2019
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
echo load_html_head_contents("Request For Lab Dip","../../", 1, 1, $unicode,1,'');
?>	
<script>

	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	// Master Form-----------------------------------------------------------------------------
	function fn_report_generated(operation)
	{
		var cbo_company=document.getElementById('cbo_company_name').value;
		var cbo_buyer_id=document.getElementById('cbo_buyer_name').value;
		var txt_job_no=document.getElementById('txt_job_no').value;
		var txt_date_from=document.getElementById('txt_date_from').value;
		var txt_date_to=document.getElementById('txt_date_to').value;
		
		/*else if(txt_date_from=="" && txt_date_to=="" && cbo_company!=0 && cbo_buyer_id==0 && txt_job_no==""){
			var divData="cbo_lab_company_name*cbo_within_group*cbo_req_type*txt_date_from*txt_date_to";	
			var msgData="Lab Company*Within Group*Req. Type*From Date*To Date";	
		}*/
		
		if(txt_date_from=="" && txt_date_to=="" && cbo_company==0 && cbo_buyer_id==0 && txt_job_no==""){
			var divData="cbo_lab_company_name*cbo_within_group*cbo_company_name*cbo_req_type*txt_date_from*txt_date_to";	
			var msgData="Lab Company*Within Group*Company Name*Req. Type*From Date*To Date";	
		}
		else
		{
			var divData="cbo_lab_company_name*cbo_within_group*cbo_company_name*cbo_req_type";	
			var msgData="Lab Company*Within Group*Company Name*Req. Type";	
		}
		
		if(form_validation(divData,msgData)==false)
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_lab_company_name*cbo_within_group*cbo_company_name*cbo_buyer_name*cbo_req_type*hid_job_id*txt_job_no*cbo_status*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/requset_lab_dip_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####");
			var tot_rows=reponse[2];
			
			$('#cbo_company_name').attr('disabled','disabled');
			$('#cbo_req_type').attr('disabled','disabled');
			
			$('#report_container2').html(reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			//$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window();" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//setc()
	 		show_msg('3');
			release_freezing();
		}
	}
	
	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="330px";
	}
	
	function fnc_labdip_request_entry( operation, increment_id)
	{
		//alert(lab_com);return;
		if(increment_id!='')//cbo_status_$i
		{
			if (form_validation('txtColorRef_'+increment_id+'*txtSwatchNo_'+increment_id+'*txtFabricType_'+increment_id+'*txtFabricWeight_'+increment_id+'*txtFabricCompos_'+increment_id,'Color Reference*Swatch No*Fabric Type*Fabric weight*Fabric Composition')==false)
			{
				return;
			}
			
			var data="action=save_update_delete&operation="+operation+"&increment_id="+increment_id+get_submitted_data_string('cbo_req_type*txtSysNo_'+increment_id+'*txtColorRef_'+increment_id+'*txtColorRefId_'+increment_id+'*labCompanyId_'+increment_id+'*txtCompanyId_'+increment_id+'*txtBuyerId_'+increment_id+'*txtPoId_'+increment_id+'*txtColorId_'+increment_id+'*txtLabDipId_'+increment_id+'*txtSwatchNo_'+increment_id+'*txtSwatchNoFrom_'+increment_id+'*txtSwatchNoTo_'+increment_id+'*txtFabricType_'+increment_id+'*txtFabricWeight_'+increment_id+'*txtFabricCompos_'+increment_id+'*txtSwatchDelDate_'+increment_id+'*txtSwatchRecDate_'+increment_id+'*txtFabRecDate_'+increment_id+'*txtLabDipProcessDate_'+increment_id+'*txtLabDipSendDate_'+increment_id+'*txtRemarks_'+increment_id+'*txtUpdateId_'+increment_id,'../../');
			//alert(data);return;
			freeze_window(operation);
			http.open("POST","requires/requset_lab_dip_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_labdip_request_entry_reponse;
		}
	}
	
	function fnc_labdip_request_entry_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');

			show_msg(trim(response[0]));
			$('#txtUpdateId_'+response[3]).val(response[1]);
			$('#txtSysNo_'+response[3]).val(response[2]);
			
			$('#txtSysNo_'+response[3]).css({
				'color':'blue',
				'font-weight':'bold'
			});
			
			$('#txtSysNo_'+response[3]).removeAttr("onDblClick").attr("onDblClick","generate_lab_report("+response[3]+");");
			
			release_freezing();
		}
	}
	
	function generate_lab_report(inc)
	{
		var company = $('#txtCompanyId_'+inc).val();
		var sysNo = $('#txtSysNo_'+inc).val();
		var updateid = $('#txtUpdateId_'+inc).val();
		if (sysNo==''){
			alert("Sys. No. not Found. Please Save First.");
			return;
		}

		freeze_window(operation);
		var report_title=$( "div.form_caption" ).html();
		var action="labdip_order_submission_print";

		var data="action="+action+'&report_title='+report_title+'&sysNo='+sysNo+'&company='+company+'&updateid='+updateid+'&report_type='+1;
		
		http.open("POST","requires/requset_lab_dip_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_lab_report_reponse;
	}

	function generate_lab_report_reponse()
	{
		if(http.readyState == 4){
			var file_data=http.responseText.split("****");
			$('#data_panel').html(file_data[0]);
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../css/prt.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
			release_freezing();
		}
	}
	
	function generate_report_file(data,action,page)
	{
		window.open("requires/requset_lab_dip_controller.php?data=" + data+'&action='+action, true );
	}
	
	function fnc_openmyPage_colorRef(inc)
	{
		var page_link='requires/requset_lab_dip_controller.php?action=colorref_popup';
		var title="Color Ref. Search Popup";
		var data=$('#txtCompanyId_'+inc).val();
		var k=1;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&data='+data, title, 'width=730px,height=450px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var str_data=this.contentDoc.getElementById("selected_str_data").value; // product ID
			if(str_data!="")
			{
				get_php_form_data(str_data+'***'+inc, "populate_data_from_search_popup", "requires/requset_lab_dip_controller" );
			}
		}
	}
	
	function fnc_openmyPage_job_no()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var req_type = $("#cbo_req_type").val();
		//alert(cbo_year_id);
		var page_link='requires/requset_lab_dip_controller.php?action=jobno_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&req_type='+req_type;
		if(req_type==1) var title='Job No Search'; else if(req_type==2) var title='Req. No Search';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_job_no').val(job_no);
			$('#hid_job_id').val(job_id);	 
		}
	}
	
	function fnc_search_type(val)
	{
		if(val==1)
		{
			//$('#txt_job_no').attr('disabled','disabled');
			$('#td_job_req').html('Job No');
			$('#td_date').html('Pub. Ship Date');
			$("#td_date").css('color','blue');
		}
		else if(val==2)
		{
			//$('#txt_job_no').removeAttr('disabled','');
			$('#td_job_req').html('Req. No');
			$('#td_date').html('Req. Date');
			$("#td_date").css('color','blue');
		}
	}
	
	
	function generate_booking_report(txt_booking_no,cbo_company_name,txt_order_no_id,cbo_fabric_natu,cbo_fabric_source,id_approved_id,txt_job_no)
	{
		var show_yarn_rate='';
		var report_title='Sample Fabric Booking';
		
		var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
		if (r==true)
		{
			show_yarn_rate="1";
		}
		else
		{
			show_yarn_rate="0";
		}
		var data="action=show_fabric_booking_report&txt_booking_no='"+txt_booking_no+"'&cbo_company_name='"+cbo_company_name+"'&txt_order_no_id='"+txt_order_no_id+"'&cbo_fabric_natu='"+cbo_fabric_natu+"'&cbo_fabric_source='"+cbo_fabric_source+"'&id_approved_id='"+id_approved_id+"'&txt_job_no='"+txt_job_no+"'&report_title="+report_title+'&show_yarn_rate='+show_yarn_rate;
		//alert(data);return;
		http.open("POST","../../order/woven_order/requires/sample_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_fabric_report_reponse;
	}	


	function generate_fabric_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var file_data=http.responseText.split('****');
			//$('#pdf_file_name').html(file_data[1]);
			//$('#data_panel').html(file_data[0] );
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+file_data[0]+'</body</html>');
			d.close();
		}
	}
	
	
	function fn_swatch_rcv_date(row_id,dtls_id,req_type)
	{
		var title = 'Swatch Receive Date';	
		var page_link='requires/requset_lab_dip_controller.php?action=swath_rcv_date_popup&row_id='+row_id+'&dtls_id='+dtls_id+'&req_type='+req_type;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=300px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
			var swatch_date=this.contentDoc.getElementById("hidden_swatch_date").value; //Access form field with id="emailfield"
			//alert(swatch_date);			
			$("#txtSwatchRecDate_"+row_id).val(swatch_date);
		}
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
<form id="searchReport_1">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",$permission);  ?>
          <h3 align="left" id="accordion_h1" style="width:1000px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
         <fieldset style="width:1000px;" id="content_search_panel">
            <table class="rpt_table" width="980" cellpadding="1" cellspacing="2" align="center" border="1" rules="all">
                <thead>
                    <th width="140" class="must_entry_caption">Lab Company</th>
                    <th width="100" class="must_entry_caption">Within Group</th>
                    <th width="140" class="must_entry_caption">Req. Company</th>
                    <th width="120">Buyer</th>
                    <th width="80" class="must_entry_caption">Request Type</th>
                    <th width="100" id="td_job_req">Req. No</th>
                    <th width="130" class="must_entry_caption" colspan="2" id="td_date">Req. Date</th>
                    <th width="100">Approval Status</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('searchReport_1','report_container*report_container2','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td><? echo create_drop_down( "cbo_lab_company_name", 140, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-Company-", $selected, ""); ?></td>
                        <td><? echo create_drop_down("cbo_within_group", 100, $yes_no, "", 1, "--  --", 0, "",1,"1"); ?></td>
                        <td><? echo create_drop_down( "cbo_company_name", 140, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-Company-", $selected, "load_drop_down( 'requires/requset_lab_dip_controller', this.value, 'load_drop_down_buyer', 'buyer_td');"); ?></td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-Buyer-", $selected, "" ); ?></td>
                        <td><? 
						$reqTypeArr=array(1=>"Order",2=>"Requisition");
						echo create_drop_down( "cbo_req_type", 80, $reqTypeArr,"", 0, "-Type-", 2, "fnc_search_type(this.value);" ); ?></td>
                        <td>
                            <input style="width:90px;" type="text" class="text_boxes" name="txt_job_no" id="txt_job_no" placeholder="Browse" onDblClick="fnc_openmyPage_job_no();" readonly onChange="$('#hid_job_id').val('');" />
                            <input type="hidden" id="hid_job_id">
                        </td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date"/></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date" /></td>
                        
                        <td><? 
						$statusArray=array(1=>"Pending",2=>"Complete",3=>"Approved");
						echo create_drop_down( "cbo_status",  100, $statusArray, "",  1, "--Select--", $selected, "",0 ); ?></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:70px;" value="Show" onClick="fn_report_generated(1);" /></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="10"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table>
            </fieldset>
        </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
    <div style="display:none" id="data_panel"></div>
	</form>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>