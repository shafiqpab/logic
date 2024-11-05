<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Yarn Requirement Report as per Budget.
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	07-11-2019
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
echo load_html_head_contents("Yarn Requirement Report as per Budget","../../../", 1, 1, $unicode,1,1);
?>	
<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	function fn_report_generated(type)
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else
		{	
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year*txt_job_no*txt_int_ref*txt_order_no*hide_order_id*hide_yarncomp_id*hide_yarntype_id*hide_yarncount_id*cbo_date_type*txt_date_from*txt_date_to*cbo_bom_status',"../../../")+'&report_title='+report_title+'&type='+type;
			//alert(data);
			freeze_window(3);
			http.open("POST","requires/yarn_req_budget_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("****");
			$('#report_container2').html(response[0]);
			//alert(response[2]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none" ><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:135px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+response[2]+')" value="Html Preview" name="Print" class="formbutton" style="width:120px"/>';
			//setFilterGrid("table_body",-1); 
	 		show_msg('3');
			release_freezing();
		}
	}
	
	function openmypage_order()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var page_link='requires/yarn_req_budget_report_controller.php?action=pono_popup&companyID='+companyID;
		var title='Order No Search';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=420px,center=1,resize=0,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_no=this.contentDoc.getElementById("hide_order_no").value;
			var order_id=this.contentDoc.getElementById("hide_order_id").value;
			
			$('#txt_order_no').val(order_no);
			$('#hide_order_id').val(order_id);	 
		}
	}
	
	function openmypage_yarn(type)
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var page_link='requires/yarn_req_budget_report_controller.php?action=yarn_popup&companyID='+companyID+'&type='+type;
		var title='Yarn Details PopUp';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=420px,center=1,resize=0,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var strid=this.contentDoc.getElementById("hidden_id").value;
			var strName=this.contentDoc.getElementById("hidden_name").value;
			if(type==1)
			{
				$('#hide_yarncomp_id').val(strid);
				$('#txt_yarncomposition').val(strName);
			}
			if(type==2)
			{
				$('#hide_yarntype_id').val(strid);
				$('#txt_yarn_type').val(strName);
			}
			if(type==3)
			{
				$('#hide_yarncount_id').val(strid);
				$('#txt_yarn_count').val(strName);
			}
		}
	}
	
	function new_window(type)
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		if(type==1)
		{
			document.getElementById('scroll_body1').style.overflowY="scroll";
			document.getElementById('scroll_body1').style.maxHeight="none";
		}
		//$('#scroll_body tr:last').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="300px";
		if(type==1)
		{
			document.getElementById('scroll_body1').style.overflowY="scroll";
			document.getElementById('scroll_body1').style.maxHeight="200px";
		}
		//$('#scroll_body tr:last').show();
	}
	
	function fncCaption(val)
	{
		if(val==2) $('#thcap').html('PO Receive Date');
		else $('#thcap').html('Shipment Date');
		$("#thcap").css('color','blue');
	}
	
</script>
</head>
<body onLoad="set_hotkey();">	 
<form id="cost_breakdown_rpt">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:1200px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:1200px;">
                <table class="rpt_table" width="1200" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                	<thead>
                    	<tr>                   
                            <th width="150" class="must_entry_caption">Company Name</th>
                            <th width="150">Buyer Name</th>
                            <th width="60">Job Year</th>
                    		<th width="70">Job No</th>
                            <th width="70">Int. Ref.</th>
                            <th width="80">Order No</th>
                            <th width="80">Yarn Composition</th>
                            <th width="80">Yarn Type</th>
                            <th width="80">Yarn Count</th>
                            <th width="80" class="must_entry_caption">Date Type</th>
                            <th width="130" colspan="2" class="must_entry_caption" id="thcap">Shipment Date</th>
                            <th width="80">Budget App. Status</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                        <tr class="general">
                            <td><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/yarn_req_budget_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                            <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" ); ?></td>
                            <td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
                            <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:60px" placeholder="Write" /></td>
                            <td><input type="text" name="txt_int_ref" id="txt_int_ref" class="text_boxes" style="width:60px" placeholder="Write" /></td>
                            <td><input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:70px" placeholder="Br/Wr" onDblClick="openmypage_order();" onChange="$('#hide_order_id').val('');" autocomplete="off">
                                <input type="hidden" name="hide_order_id" id="hide_order_id">
                            </td>
                            <td><input type="text" name="txt_yarncomposition" id="txt_yarncomposition" class="text_boxes" style="width:70px" placeholder="Browse" onDblClick="openmypage_yarn(1);" onChange="$('#hide_yarncomp_id').val('');" autocomplete="off" readonly>
                                <input type="hidden" name="hide_yarncomp_id" id="hide_yarncomp_id">
                            </td>
                            <td><input type="text" name="txt_yarn_type" id="txt_yarn_type" class="text_boxes" style="width:70px" placeholder="Browse" onDblClick="openmypage_yarn(2);" onChange="$('#hide_yarntype_id').val('');" autocomplete="off" readonly>
                                <input type="hidden" name="hide_yarntype_id" id="hide_yarntype_id">
                            </td>
                            <td><input type="text" name="txt_yarn_count" id="txt_yarn_count" class="text_boxes" style="width:70px" placeholder="Browse" onDblClick="openmypage_yarn(3);" onChange="$('#hide_yarncount_id').val('');" autocomplete="off">
                                <input type="hidden" name="hide_yarncount_id" id="hide_yarncount_id">
                            </td>
                            <td><? 
                            $date_type_arr=array(1 => "Shipment Date", 2 => "PO Receive Date");
                            $bom_status_arr=array(1 => "Approved", 2 => "Unapproved");
                            echo create_drop_down( "cbo_date_type", 80, $date_type_arr,"", 0, "-- Select --", 1, "fncCaption(this.value);",0,'','','','','' ); ?></td>
                            <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" ></td>
                            <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date" ></td>
                            <td><? echo create_drop_down( "cbo_bom_status", 80, $bom_status_arr,"", 1, "All", $selected, "" ); ?></td>
                            <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1);" /></td>
                        </tr>
                        <tr>
                            <td colspan="13" align="center"><? echo load_month_buttons(1); ?></td>
                            <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Summary" onClick="fn_report_generated(2);" /></td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
