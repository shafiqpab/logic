<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Bank Liability Report.
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	10-07-2018
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
echo load_html_head_contents("Bank Liability", "../../", 1, 1,'','','');
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
 
	var permission = '<? echo $permission; ?>';
	
	function generate_report(str)
	{
		/*if(form_validation('cbo_company_name*cbo_lein_bank*hide_year','Company Name*Lein Bank*Year')==false)
		{
			return;
		}*/
		
		var report_title=$( "div.form_caption" ).html();
		if(str==1){ var action="report_generate"; }
		else if(str==2){ var action="report_generate_retention";}
		else if(str==3){ var action="report_generate_bank_status";}
		var data="action="+action+get_submitted_data_string("cbo_company_name*cbo_lein_bank*txt_exchange_rate","../../")+'&report_title='+report_title;
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/bank_liability_today_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			//alert(http.responseText);return;
			$('#report_container2').html(response[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		/*document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";*/
		var report_title=$( "div.form_caption" ).html();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body><p style="font-size:16px; font-weight:bold" align="center">'+report_title+'</p>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		/*document.getElementById('scroll_body').style.overflowY="auto";
		document.getElementById('scroll_body').style.maxHeight="auto";*/
	}
	
	function openmypage_popup(com_bank_data,title,action)
	{
		
		if(action=="bank_order_in_hand_popup")
		{
			var widths="750px";
			var title="Bank In Hand";
		}
		else if(action=="docs_in_hand_popup") 
		{
			var widths="1200px";
			var title="Docs In Hand";
		}
		else if(action=="docs_forcast_popup") 
		{
			var widths="1200px";
			var title="Docs Forcast";
		}
		else if(action=="bill_receiveable_popup") 
		{
			var widths="1200px";
			var title="Bill Receiveable";
		}
		else if(action=="order_in_hold_popup") 
		{
			var widths="1350px";
			var title="Order In Hold";
		}
		else if(action=="order_in_cancel_popup") 
		{
			var widths="1350px";
			var title="Order In Hold";
		}
		else 
		{
			var widths="1350px";
			var title="Order In Hand";
		}
		//alert(com_bank_data);return;
		page_link='requires/bank_liability_today_report_controller.php?action='+action+'&company_id='+com_bank_data+'&type='+title;
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,title, 'width='+widths+',height=400px,center=1,resize=0,scrolling=0','../');
	}
	
	function openmypage(company_id,bank_id,action,title,type)
	{
		if(action=='btb_popup')
			width="850px";
		else if(action=='margine_popup')
			width="1050px";
		else if(action=='fdbp_popup')
			width="1050px";
		else if(action=='btb_opened_popup')
			width="350px";
		else
			width="950px";
		page_link='requires/bank_liability_today_report_controller.php?action='+action+'&company_id='+company_id+'&bank_id='+bank_id+'&type='+type;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,title, 'width='+width+',height=350px,center=1,resize=0,scrolling=0','../');
		emailwindow.onclose=function()
		{
			//alert("Jahid");
		}
	}

	// function print_button_setting(company)
	// {
	// 	get_php_form_data(company,'print_button_variable_setting','requires/bank_liability_today_report_controller' );
	// }

	// function print_report_button_setting(report_ids)
	// {
	// 	$('#show').hide();
	// 	$('#show_2').hide();
	// 	$('#show_3').hide();
	// 	//alert(report_ids);
	// 	var report_id=report_ids.split(",");
	// 	for (var k=0; k<report_id.length; k++)
	// 	{
	// 		if(report_id[k]==108)
	// 		{
	// 			$('#show').show();
	// 		}
	// 		if(report_id[k]==289)
	// 		{
	// 			$('#show_2').show();
	// 		}
	// 		if(report_id[k]==472)
	// 		{
	// 			$('#show_3').show();
	// 		}
	// 	}
	// }
	
	
</script>
</head>
 
<body onLoad="set_hotkey();">
 <div style="width:100%" align="center">
    <form id="file_wise_explort_import_status" action="" autocomplete="off" method="post">
		<? echo load_freeze_divs ("../../"); ?>
        <h3 align="left" id="accordion_h1" style="width:900px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:900px;"> 

    	<fieldset style="width:100%" >
        <table class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" width="900">
            <thead>
                <th width="220">Company Name</th> 
                <th width="220">Lien Bank</th>
                <th width="120">Exchange Rate</th>
                <th align="center"><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:90px" onClick="reset_form('file_wise_explort_import_status','report_container*report_container2','','','')" /></th>
           </thead>
            <tr class="general">                           
                <td align="center">
                   <?
                        echo create_drop_down( "cbo_company_name", 200, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "print_button_setting(this.value);" );
                    ?>
					<input type="hidden" id="report_ids" >
                </td>
                <td align="center">
                <? 
                    echo create_drop_down( "cbo_lein_bank", 200, "select (bank_name || ' (' || branch_name || ')' ) as bank_name,id from lib_bank where is_deleted=0  and status_active=1 and lien_bank=1 order by bank_name","id,bank_name", 1, "-- All Bank --", $selected, "",0,"" );
                ?>
                </td>
                <td>
                <?
				
                //echo create_drop_down( "hide_year", 100,$blank_array,"", 1, "-- Select --", 1,"");
                ?>
                <input type="text" id="txt_exchange_rate" name="txt_exchange_rate" class="text_boxes_numeric" style="width:100px;" value="83" />
                </td>
				<td align="center">
				<!-- style="display:none" -->
                <input  type="button" name="show" id="show" onClick="generate_report(1);" class="formbutton" style="width:60px" value="Show" />
                <input  type="button" name="show_2" id="show_2" onClick="generate_report(2);" class="formbutton" style="width:120px" value="Retention Statement" />
                <input  type="button" name="show_3" id="show_3" onClick="generate_report(3);" class="formbutton" style="width:100px" value="Bank Status" />
                </td>
            </tr>
            <tr style="display:none" >
                <td colspan="4" style="display:none" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
            </tr>
         </table>
    </fieldset>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div> 
        </form>
    </div>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>