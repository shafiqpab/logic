<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Purchase Recap Report.
Functionality	:	
JS Functions	:
Created by		:	Wayasel Ahmed
Creation date 	: 	13-03-23
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
echo load_html_head_contents("General Service Receive Report", "../../", 1, 1,'','','');
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
 
	var permission = '<? echo $permission; ?>';
	
	var tableFilters = { 
		col_80: "none", 
		col_operation: {
			id: ["value_total_req_qnty","value_total_wo_qnty","value_total_wo_amt","value_total_wo_balance","value_total_pi_qnty","value_total_pi_amt","value_total_lc_amt","value_total_pkg_qnty","value_total_pay_amt","value_total_mrr_qnty","value_total_mrr_amt","value_total_short_amt","value_total_pipe_line"],
			col: [12,19,21,23,30,32,39,51,58,59,60,61,62],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	
	function generate_report()
	{
		var txt_req_no=$("#txt_search_string").val();
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}

		if(txt_req_no =="")
		{
			if(form_validation('txt_date_from*txt_date_to','From Date*To Date')==false)
			{
				return;
			}
		}

        var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_supplier*cbo_location*cbo_department_name*cbo_type*txt_search_string*cbo_section*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/general_service_receive_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("****");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			//setFilterGrid("table_body",-1,tableFilters);
			
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";
		
		//$("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');// media="print"
		d.close();
		
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="300px";
		//$("#table_body tr:first").show();

	}
	
	function openmypage_popup(wo_pi_req,prod_id,page_title,action)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/general_service_receive_report_controller.php?wo_pi_req='+wo_pi_req+'&prod_id='+prod_id+'&action='+action, page_title, 'width=720px,height=400px,center=1,resize=0,scrolling=0','../');
	}

    function search_by(val,type)
	{
		if(type==2)
		{
			$('#txt_search_string').val('');
			if(val==1) $('#search_by_td_up').html('Req. No');
			else if(val==2) $('#search_by_td_up').html('Wo No.');
			else if(val==3) $('#search_by_td_up').html('Service Ack. No');

			$('#txt_search_string').val('');
			if(val==1) $('#cange_date_title').html('Req. Date');
			else if(val==2) $('#cange_date_title').html('Wo Date.');
			else if(val==3) $('#cange_date_title').html('Service Ack. Date');
		}
	}
</script>

</head>

<body onLoad="set_hotkey();">
<form id="PurchaseRecap_report">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",''); ?>
        <h3 align="left" id="accordion_h1" style="width:1360px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
            <fieldset style="width:1360px;">
                <table class="rpt_table" width="1350" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">
                	<thead>
                   		<tr>                    
                            <th class="must_entry_caption" width="150">Company Name</th>
                            <th width="100">Supplier</th>
                            <th width="150">Location</th>
                            <th width="100">Department</th>
                            <th width="100">Section</th>
                            <th width="100">Service For</th>
                            <th width="80" id="search_by_td_up">Req. No</th>
                            <th width="200" id="cange_date_title">Date range</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('PurchaseRecap_report','report_container*report_container2','','','')" /></th>
                        </tr>
                     </thead>
                    <tbody>
                        <tr class="general">
                            <td align="center"> 
								<?
                                echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/general_service_receive_report_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );load_drop_down( 'requires/general_service_receive_report_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/general_service_receive_report_controller', this.value, 'load_drop_down_department', 'department_td' );" );
                                ?>
                            </td>
                            <td id="supplier_td"> 
						  	<?
							   	echo create_drop_down( "cbo_supplier", 100, $blank_array,"", 1,"-- Select --",0,"" );
 							?>
							</td>
                            <td id="location_td" align="center">
                            <? 
                                echo create_drop_down( "cbo_location", 140, "select id,location_name from lib_location","id,location_name", 1, "-- Select Location --", $selected,"",0,"" );
                            ?>
                            </td>
                            <td id="department_td"> 
								<? echo create_drop_down( "cbo_department_name", 100, $blank_array,"", 1,"-- All --",0,"" ); ?>
							</td>
        
                            <td id="section_td"> 
						  	<?
							   	echo create_drop_down( "cbo_section", 100, $blank_array,"", 1,"-- Select --",0,"" );
 							?>
							</td>
                            <td>
                        	<?
								$search_by_arr=array(1=>"Req. No",2=>"Wo No",3=>"Service Ack. No");
								echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "", "",'search_by(this.value,2)',0 );
							?>
                            </td>
                            <td id="search_by_td"><input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:70px" /></td>
                            <td align="center">
                                <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:80px" placeholder="From Date"/>
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:80px" placeholder="To Date"/>
                            </td>
                            <td>
                            	<input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:70px" class="formbutton" />
                            </td>
                        </tr>
                        <tr>
                        	<td colspan="9" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
    
    <div style="margin-top:10px" id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
