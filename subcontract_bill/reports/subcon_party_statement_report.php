<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Party Statement Report.
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	21-10-2014
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
echo load_html_head_contents("Party Statement Report", "../../", 1, 1,$unicode,1,1);

?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';

	var tableFilters = 
	{
		
		col_operation: {
			id: ["tot_bill_qnty","tot_bill_amt"],
			col: [9,10],
			operation: ["sum","sum"],
			write_method: ["innerHTML","innerHTML"]
		},
 	}
			
	function fn_report_generated(type)
	{
		//alert(type);
		if(type==1 || type==4 || type==5)
		{
			if (form_validation('cbo_company_id*txt_date_from*txt_date_to','Comapny Name*Date From*Date To')==false)
			{
				return;
			}
			/*else if (type==1)
			{
				if ($('#cbo_party_id').val()==0)
				{
					alert ("Please Select Party.");
					return;
				}
			}*/
		}
		else if(type==2 || type==3)
		{
			if (form_validation('cbo_company_id*txt_date_to','Comapny Name*Date To')==false)
			{
				return;
			}
		}
		
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_name*cbo_party_source*cbo_party_id*cbo_bill_type*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title+'&type='+type;
			//alert (data);
			freeze_window(3);
			http.open("POST","requires/subcon_party_statement_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("**"); 
			$('#report_container2').html(reponse[0]);
			//alert (reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//append_report_checkbox('table_header_1',1);
			setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}
	
	/*function new_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	}*/
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
	}
	
	function show_progress_report_details(action,order_id,width)
	{ 
		 emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/subcon_party_statement_report_controller.php?action='+action+'&order_id='+order_id, 'Work Progress Report Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../');
		
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

	function openImageWindow(id)
	{
		var title = 'Image View';	
		var page_link = 'requires/subcon_party_statement_report_controller.php?&action=image_view_popup&id='+id;
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
		}
	}

</script>
</head>
<body onLoad="set_hotkey();">
<form id="partyStatementReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1070px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1070px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>                    
                    <th width="130" class="must_entry_caption">Company</th>
                    <th width="130">Location</th>
                    <th width="100">Party Source</th>
                    <th width="130">Party </th>
                    <th width="70">Bill Type</th>
                    <th width="130" colspan="2" class="must_entry_caption">Transaction Date</th>
                    <th colspan="4"><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                </thead>
                <tbody>
                    <tr class="general" >
                        <td><? echo create_drop_down( "cbo_company_id", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/subcon_party_statement_report_controller', this.value, 'load_drop_down_location', 'location_td');" ); ?></td>
                        <td id="location_td"><? echo create_drop_down( "cbo_location_name", 130, $blank_array,"", 1, "-Select Location-", $selected,"","","","","","",3); ?></td>
                        <td><? echo create_drop_down( "cbo_party_source", 100, $knitting_source,"", 1, "-Party Source-", $selected, "load_drop_down( 'requires/subcon_party_statement_report_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_buyer', 'buyer_td' ); ",0,"1,2","","","",5); ?></td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_party_id", 130, $blank_array,"", 1, "--Select Party--", $selected, "",1,"" ); ?></td>
                        <td><? echo create_drop_down( "cbo_bill_type", 70, $production_process,"", 1, "-Select Type-", $selected,"","","","","",""); ?></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" ></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date" ></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:55px" value="Ledger" onClick="fn_report_generated(1);" /></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Receivable" onClick="fn_report_generated(2);" /></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:110px" value="Invoice(AR)" onClick="fn_report_generated(3);" /></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:90px" value="Periodic Bill" onClick="fn_report_generated(4);" /></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="9" align="center"><? echo load_month_buttons(1); ?></td>
                        <td align="center"><input type="button" id="show_button" class="formbutton" style="width:110px" value="Periodic Bill (Rate)" onClick="fn_report_generated(5);" /></td>
						<td><input type="button" id="show_button" class="formbutton" style="width:90px" value="Bill Issue" onClick="fn_report_generated(6);" /></td>
                    </tr>
                </tfoot>
           </table> 
        </fieldset>
    </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
