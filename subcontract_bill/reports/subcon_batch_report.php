<?
/*-------------------------------------------- Comments
Purpose			: 	This form will Create Subcon Batch Reprot
Functionality	:	
JS Functions	:

Created by		:	Kausar
Creation date 	: 	24-08-2014
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
echo load_html_head_contents("Subcon Batch Reprot", "../../", 1, 1,'','','');

?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	var tableFilters = 
	{
		col_30: "none",
		col_operation: {
		id: ["batch_td_qty"],
		col: [13],
		operation: ["sum"],
		write_method: ["innerHTML"]
		}
	} 
	
	function fn_report_generated(operation)
	{		
		var b_number=document.getElementById('hid_batch_id').value;
		var batch_no=document.getElementById('txt_batch_no').value;
			
		var order_no=document.getElementById('txt_order_no').value;	
		var hidden_order=document.getElementById('hid_order_id').value;	
		
		var hidden_ext=document.getElementById('hidden_ext_no').value;	
		var ext_no=document.getElementById('txt_ext_no').value;	
		
		var j_number=document.getElementById('hid_job_id').value;	
		var job_number=document.getElementById('txt_job_no').value;
		var repot_type=document.getElementById('cbo_type').value;	

		if(j_number!="" || job_number!="" || order_no!="" || hidden_order!="" || ext_no!="" || hidden_ext!="" || repot_type==2 || repot_type==3 || repot_type==4)
		{
			if(form_validation('cbo_company_id','Company')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_id*txt_date_from*txt_date_to','Company*From date Fill*To date Fill')==false)
			{
				return;
			}
		}
			
		freeze_window(5);
	    var data="action=batch_report&operation="+operation+get_submitted_data_string('cbo_company_id*cbo_buyer_id*txt_job_no*hid_job_id*hid_batch_id*txt_batch_no*txt_ext_no*hidden_ext_no*txt_order_no*hid_order_id*cbo_type*cbo_year*txt_date_from*txt_date_to',"../../");
			http.open("POST","requires/subcon_batch_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_show_batch_report;
	}
	  
	function fnc_show_batch_report()
	{
		if(http.readyState == 4) 
		{
			// alert(http.responseText);
			document.getElementById('report_container2').innerHTML=http.responseText;
			document.getElementById('report_container').innerHTML=report_convert_button('../../'); 

			setFilterGrid("table_body",-1,tableFilters);
			release_freezing();
		}
	}

	function openmypage_job_num()
	{ 
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_id').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_id').value;
		var year=document.getElementById('cbo_year').value;
		var page_link="requires/subcon_batch_report_controller.php?action=job_no_popup&company_id="+company_name+"&cbo_buyer_name="+cbo_buyer_name+"&year="+year;
		var title="Job Number";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=530px,height=420px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			//var job=theemail.split("_");
			document.getElementById('txt_job_no').value=theemail;
			document.getElementById('hid_job_id').value=theemail;
			release_freezing();
		}
	}
	
	function openmypage_order()
	{ 
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_id').value;
		var buyer_name=document.getElementById('cbo_buyer_id').value;
		var year=document.getElementById('cbo_year').value;
		var job_number=document.getElementById('txt_job_no').value;
		
		var page_link="requires/subcon_batch_report_controller.php?action=order_number_popup&company_name="+company_name+"&buyer_name="+buyer_name+"&year="+year+"&job_number="+job_number;
		var title="Order Number";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=420px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			var order=theemail.split("_");
			document.getElementById('txt_order_no').value=order[1];
			document.getElementById('hid_order_id').value=order[0];
			release_freezing();
		}
	}

	function openmypage_batch_num()
	{ 
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_id').value;
		var hid_order_id=document.getElementById('hid_order_id').value;
		var page_link="requires/subcon_batch_report_controller.php?action=batch_no_popup&company_name="+company_name+"&hid_order_id="+hid_order_id;
		var title="Batch Number";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=530px,height=400px,center=1,resize=0,scrolling=0','../')

		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			var batch=theemail.split("_");
			document.getElementById('hid_batch_id').value=batch[0];
			document.getElementById('txt_batch_no').value=batch[1];
			document.getElementById('txt_ext_no').value=batch[2];
			release_freezing();
		}
	}

	function batch_extension()
	{ 
		var company_name=document.getElementById('cbo_company_id').value;
		var buyer_name=document.getElementById('cbo_buyer_id').value;
		var year=document.getElementById('cbo_year').value;
		var job_number=document.getElementById('txt_job_no').value;
		var batch_number=document.getElementById('txt_batch_no').value;
		var batch_number_hidden=document.getElementById('hid_batch_id').value;
		
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_id').value;
		var page_link="requires/subcon_batch_report_controller.php?action=batchextensionpopup&company_name="+company_name+"&buyer_name="+buyer_name+"&year="+year+"&job_number_show="+job_number+"&batch_number_show="+batch_number+"&batch_number_hidden="+batch_number_hidden;
		var title="Batch Number";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=400px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			var batch=theemail.split("_");
			document.getElementById('txt_ext_no').value=batch[1];
			release_freezing();
		}
	}

	function toggle( x, origColor ) {
		var newColor = 'green';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}
		
	function js_set_value( str ) {
		toggle( document.getElementById( 'tr_' + str), '#FFF' );
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
<? echo load_freeze_divs ("../../",''); ?>
     <form name="subconbatchreport_1" id="subconbatchreport_1"> 
     <h3 style="width:1050px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
     <div id="content_search_panel" >      
             <fieldset style="width:1050px;">
                 <table class="rpt_table" width="1050" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                    <thead>
                        <th class="must_entry_caption">Company</th>
                        <th>Buyer</th>
                        <th>Year</th>
                        <th>Job No</th>
                        <th>Order No</th>
                        <th>Batch No</th>
                        <th>Ext. No</th>
                        <th>Report Type</th>
                        <th class="must_entry_caption">Batch Date</th>
                        <th><input type="reset" name="reset" id="reset" value="Reset" onClick="reset_form('subconbatchreport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px" /></th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> 
								<?
									echo create_drop_down( "cbo_company_id", 140, "select id,company_name from lib_company where status_active=1 and is_deleted=0 and core_business not in(3)  order by company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down('requires/subcon_batch_report_controller', this.value, 'load_drop_down_location', 'buyer_td' );" );
                                ?>
                            </td>
                            <td id="buyer_td">
								<?
									echo create_drop_down( "cbo_buyer_id", 140, "select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 order by short_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
                                ?>
                                </td>
                            <td id="extention_td">
								<?
									echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
                                ?>
                            </td>
                            
                            <td>
                                <input type="text"  name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:80px;" placeholder="Write/Browse" onDblClick="openmypage_job_num();">
                                <input type="hidden" name="hid_job_id" id="hid_job_id" style="width:50px;">
                            </td>
                            <td>
                                <input type="text"  name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:80px;" placeholder="Write/Browse" onDblClick="openmypage_order()">
                                <input type="hidden" name="hid_order_id" id="hid_order_id" style="width:50px;">
                            </td>
                            <td>
                                <input type="text"  name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:80px;" placeholder="Write/Browse" onDblClick="openmypage_batch_num();">
                                <input type="hidden" name="hid_batch_id" id="hid_batch_id" style="width:50px;">
                            </td>
                            <td>
                                <input type="text"  name="txt_ext_no" id="txt_ext_no" class="text_boxes" style="width:60px;" placeholder="Write/Browse" onDblClick="batch_extension();">
                                <input type="hidden" name="hidden_ext_no" id="hidden_ext_no" style="width:50px;">
                            </td>
                            <td>
								<?
									$search_by_arr=array(1=>"Date Wise Report",2=>"Wait For Heat Setting",3=>"Wait For Dyeing",4=>"Wait For Re-Dyeing");
									echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "", "",'',0 );
                                ?>
                            </td>
                            <td align="center" width="200">
                                <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:60px" placeholder="From Date"/>
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:60px" placeholder="To Date"/>
                            </td>
                            <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated()" /></td>
                        </tr>
                    </tbody>
            	<tr>
                	<td colspan="10" align="center">
 						<? echo load_month_buttons(1); ?>
                   	</td>
                </tr>
            </table> 
            <br />
                </fieldset>
            </div>
		</form>
	</div>
    <div id="report_container" style="width:1050px; margin:0 auto;"></div>
    <div id="report_container2" style="width:1050px; margin:0 auto; text-align:center;"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>