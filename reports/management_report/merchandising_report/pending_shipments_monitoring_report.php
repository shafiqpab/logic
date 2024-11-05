<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Pending Shipment Monitoring Report
				
Functionality	:	
JS Functions	:
Created by		:	Monzu 
Creation date 	: 	11/01/2013
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
echo load_html_head_contents("Order Info","../../../", 1, 1, $unicode);
?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	function generate_report(type)
	{
		if( form_validation('txt_demand_date','To Date')==false )
		{
			//return;
		}
		var report_title=$( "div.form_caption" ).html();
	 
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*txt_demand_date',"../../../")+'&report_title='+report_title;
		
		//alert (data);// return;
		freeze_window(3);
		http.open("POST","requires/pending_shipments_monitoring_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);  
			
            
                        document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none<input type="but"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" id="Print" class="formbutton" style="width:100px"/>';
			//setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";
		//$('#scroll_body tr:last').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close; 
	
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="380px";
		//$('#scroll_body tr:last').show();
	}
	
	function order_dtls_popup(job_no,po_id,template_id,tna_process_type)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/capacity_and_order_booking_status_controller.php?action=work_progress_report_details&job_no='+job_no+'&po_id='+po_id+'&template_id='+template_id+'&tna_process_type='+tna_process_type, 'Work Progress Report Details', 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
		}
	}
	
	function openmypage_image(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
		}
	}
        
	
//	function show_inner_filter(e)
//	{
//		//alert (e.value)
//		if (e!=13) {var unicode=e.keyCode? e.keyCode : e.charCode } else {unicode=13;}
//		if (unicode==13 )
//		{
//			generate_report(2);
//		}
//	}
</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../");  ?>
    <form name="shipmentpending_1" id="shipmentpending_1" autocomplete="off" > 
        <h3 style="width:400px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
        <fieldset style="width:300px" >
            <table class="rpt_table" width="300" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th class="must_entry_caption">Date</th>
                    <th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" /></th>
                </thead>
                <tr class="general">
                    <td>
                        <?

						echo create_drop_down( "cbo_company_id", 172, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, " " );
                        ?>                                     
                    </td>
                    <td>
                        <input type="text" name="txt_demand_date" id="txt_demand_date" value="<? echo date("d-m-Y",time());?>" class="datepicker" style="width:140px;" tabindex="6" />
                    </td>
                    <td>
                    	<input type="button" name="show" id="show" onClick="generate_report(1);" class="formbutton" style="width:80px" value="Show" />
                    </td>
                </tr>
                
            </table>
        </fieldset>
        </div>
    </form>
        <div id="report_container"></div>
        <div id="report_container2"></div>      
    </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>