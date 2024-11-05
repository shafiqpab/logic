<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Job Wise Audit Report.
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	13-03-2023
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Job Wise Audit Report","../../../", 1, 1, $unicode,1,1);
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	
	var tableFilters = 
	 {
		col_33: "none",
		col_operation: {
		id: ["value_currexqty","value_exqty","value_totalShipamt","value_totalcmcost","value_totalcm","value_salescm","value_invoiceqty","value_invoiceamt"],
	    col: [84,85,91,94,97,98,102,103],
	    operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
	    write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 }
	
	function fn_report_generated()
	{
		if(form_validation('cbo_company_name','Company Name')==false)//*txt_date_from*txt_date_to*From Date*To Date
		{
			return;
		}
		else
		{
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_type*cbo_buyer_name*cbo_year*hide_job_id*txt_job_no*txt_style_ref*cbo_team_name*cbo_team_member*cbo_order_status*cbo_shipment_status*cbo_date_type*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;
			freeze_window(3);
			http.open("POST","requires/job_wise_audit_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			/*var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../../'); */

			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

			//append_report_checkbox('table_header_1',1);

			setFilterGrid("table_body",-1,tableFilters);
			//show_graph( '', document.getElementById('graph_data').value, "pie", "chartdiv", "", "../../../", '',400,700 );
	 		show_msg('3');
			release_freezing();
		}
	}



	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#scroll_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		$('#scroll_body tr:first').show();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
	}
	
	function openmypage(po_id,po_qnty,po_no,job_no,type,tittle)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/job_wise_audit_report_controller.php?po_id='+po_id+'&po_qnty='+po_qnty+'&po_no='+po_no+'&job_no='+job_no+'&action='+type, tittle, 'width=650px, height=350px, center=1, resize=0, scrolling=0', '../../');
	}
		
	function generate_pre_cost_report(po_id,job_no,company_id,buyer_id,style_ref,costing_date)
	{
		var zero_val='';
		var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
		if (r==true) zero_val="1"; else zero_val="0";
		
		var data="&action=preCostRpt"+
		'&txt_po_breack_down_id='+"'"+po_id+"'"+
		'&txt_job_no='+"'"+job_no+"'"+
		'&cbo_company_name='+"'"+company_id+"'"+
		'&txt_style_ref='+"'"+style_ref+"'"+
		'&txt_costing_date='+"'"+costing_date+"'"+
		'&zero_value='+zero_val+
		'&cbo_buyer_name='+"'"+buyer_id+"'";
		http.open("POST","../../../order/woven_order/requires/pre_cost_entry_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_report_reponse;
	}
	
	function fnc_generate_report_reponse()
	{
		if(http.readyState == 4) 
		{
			$('#data_panel').html( http.responseText );
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
		}
	}	
	function fnc_open_view(action,title,company_id,job_id,job_no,po_id)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/job_wise_audit_report_controller.php?job_id='+job_id+'&job_no='+job_no+'&po_id='+po_id+'&company_id='+company_id+'&action='+action, title, 'width=1000px,height=350px,center=1,resize=0,scrolling=0','../../');
	}	

	function fnc_gray_view(action,title,trans_in,trans_out,trans_amnt)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/job_wise_audit_report_controller.php?trans_in='+trans_in+'&trans_out='+trans_out+'&trans_amnt='+trans_amnt+'&action='+action, title, 'width=420,height=200px,center=1,resize=0,scrolling=0','../../');
	}	

	function openmypage_job()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else 
		{	
			var data = $("#cbo_company_name").val()+"_"+$("#cbo_buyer_name").val()+"_"+$("#cbo_year").val()+"_"+$("#hide_job_id").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/job_wise_audit_report_controller.php?data='+data+'&action=job_no_popup', 'Job No Search', 'width=700px,height=420px,center=1,resize=0,scrolling=0','../../')
			emailwindow.onclose=function()
			{
				var theemailjobid=this.contentDoc.getElementById("txt_job_id").value;
				var theemailjob=this.contentDoc.getElementById("txt_job_no").value;
				var response=theemailjob.split('_');
				//alert(theemailjob)
				if ( theemailjob!="" )
				{
					freeze_window(5);
					$("#hide_job_id").val(theemailjobid);
					$("#txt_job_no").val(theemailjob);
					//$("#txt_style_ref").val(response[2]);
					release_freezing();
				}
			}
		}
	}
	
	function search_by(val,type)
	{
		if(type==2)
		{
			$('#txt_search_string').val('');
			if(val==1) $('#search_by_td_up').html('Order No');
			else if(val==2) $('#search_by_td_up').html('Style Ref.');
			else if(val==3) $('#search_by_td_up').html('File No');
			else if(val==4) $('#search_by_td_up').html('Internal Ref');
		}
		else if(type==1)
		{
			$('#txt_date_from').val('');
			$('#txt_date_to').val('');
			if(val==1) $('#date_td').html('Country Shipment Date');
			else if(val==2) $('#date_td').html('Pub. Ship Date');
			else if(val==3) $('#date_td').html('Org. Ship Date');
			else if(val==4) $('#date_td').html('PO Insert Date');
			else $('#date_td').html('Shipment Date');
		}
	}
</script>

</head>
<body onLoad="set_hotkey();">
<form id="jobwiseauditrpt_1">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:1260px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:1260px;">
                <table class="rpt_table" width="1160" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                	<thead>
                    	<tr>                   
                            <th width="140" class="must_entry_caption">Company Name</th>
                            <th width="100">Type</th>
                            <th width="120">Buyer Name</th>
                            <th width="60">Job Year</th>
                            <th width="80">Job No.</th>
                            <th width="90">Style Ref.</th>
                            <th width="100">Team</th>
                        	<th width="110">Team Member</th>
                            <th width="70">Order Status</th>
                            <th width="80">Ship Status</th>
                            <th width="120">Date Category </th>
                            <th width="120" colspan="2" title="Data Will be Populated Acording to Pub. Ship Date Wise." id="date_td">Shipment Date</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:60px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                        <tr class="general">
                            <td><?=create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/job_wise_audit_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td'); get_php_form_data(this.value, 'load_variable_settings', 'requires/job_wise_audit_report_controller');" ); ?></td>
							<?php
							$typeArr = array(2=>'Knit', 3=>'Woven');
							?>
                            <td id="type_td"><?=create_drop_down( "cbo_type", 100, $typeArr,"", 0, "-- Type --", $selected, "",0,"" ); ?></td>
							<td id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" ); ?></td>
                            <td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", $selected, "",0,"" ); ?></td>
                            <td>
                            	<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px" placeholder="Write/Browse" onDblClick="openmypage_job();" onChange="$('#hide_job_id').val('');" />
                                <input type="hidden" name="hide_job_id" id="hide_job_id" >
                            </td>
                            <td><input type="text" id="txt_style_ref" name="txt_style_ref" class="text_boxes" style="width:50px"  placeholder="Write"  /></td>
                            <td><? echo create_drop_down( "cbo_team_name", 100, "select id,team_name from lib_marketing_team where status_active=1 and is_deleted=0 order by team_name","id,team_name", 1, "-- Select Team --", $selected, " load_drop_down( 'requires/job_wise_audit_report_controller', this.value, 'load_drop_down_team_member', 'team_td' )" ); ?></td>
                            <td id="team_td"><? echo create_drop_down( "cbo_team_member", 110, $blank_array,"", 1, "- Team Member- ", $selected, "" ); ?></td>
                            <td><?=create_drop_down( "cbo_order_status", 70, $order_status,"", 1,"-- All --", $selected, "",0,"" ); ?>
							<?
							$shpmnt_status = array(0 => "--Select--",1 => "Full Pending", 2 => "Partial Delivery", 3 => "Full Delivery/Closed");
							?>
                            <td><?=create_drop_down( "cbo_shipment_status", 80, $shpmnt_status,"", 0,"-- Select --", $selected, "",0,"" ); ?></td>
                            <td>
								<? 
                                $date_type_arr=array(1=>"Country Ship Date",2=>"Pub. Ship Date",3=>"Org. Ship Date",4=>"Job Insert Date",5=>"Ex-Factory Date");
                                echo create_drop_down( "cbo_date_type", 120, $date_type_arr,"", 0, "-Select-", 0, "search_by(this.value,1);",0,"" );
                                ?>
                           </td>
                            <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:53px" placeholder="From Date" ></td>
                            <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:53px"  placeholder="To Date" ></td>
                            <td><input type="button" id="show_button" class="formbutton" style="width:60px" value="Show" onClick="fn_report_generated();" /></td>
                        </tr>
                    </tbody>
                    <tfoot>
                    	<tr>
                            <td colspan="13" align="center">
                                <? echo load_month_buttons(1); ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
    <div style="display:none" id="data_panel"></div>  
 </form>    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
