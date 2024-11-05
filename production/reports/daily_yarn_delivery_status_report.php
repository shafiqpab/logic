<?
/*-------------------------------------------- Comments

Purpose			: 	This form will Create Daily Yarn Delivery Status
					
Functionality	:	
				

JS Functions	:

Created by		:	Fuad Shahriar 
Creation date 	: 	06-10-2013
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
echo load_html_head_contents("Daily Yarn Delivery Status", "../../", 1, 1,'','','');

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	var tableFilters = 
	 {
		col_17: "none", 
		col_operation: {
		id: ["value_tot_reqsn_qnty","value_tot_demand_qnty","value_tot_delivery_qnty","value_tot_balance"],
	    col: [9,10,11,12],
	    operation: ["sum","sum","sum","sum"],
	    write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 }
	
	function fn_report_generated()
	{
		if(form_validation('cbo_company_name*txt_order_no','Company*Order No')==false)
		{
			return;
		}
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*hide_order_id',"../../");
		
		freeze_window(5);
		http.open("POST","requires/daily_yarn_delivery_status_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=http.responseText;

			$('#report_container2').html(reponse);
			document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			append_report_checkbox('table_header_1',1);
			setFilterGrid("table_body",-1,tableFilters);
	 		show_msg('3');
			release_freezing();
		}
	}
		
	function openmypage_orderNo()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var page_link='requires/daily_yarn_delivery_status_report_controller.php?action=orderNo_search_popup&companyID='+companyID;
		var title='Order No Info';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=830px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var po_id=this.contentDoc.getElementById("hide_po_id").value;
			var po_no=this.contentDoc.getElementById("hide_po_no").value;
			
			$('#txt_order_no').val(po_no);
			$('#hide_order_id').val(po_id);	 
		}
	}
	
	
</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",''); ?>
		 <form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1"> 
         <h3 style="width:600px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
             <fieldset style="width:600px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th class="must_entry_caption">Company Name</th>
                            <th class="must_entry_caption">Order No</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailyYarnStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:100px" /></th>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                                    ?>
                                </td>
                                <td>
                                    <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:250px" placeholder="Double Click To Search" onDblClick="openmypage_orderNo();" readonly>
                                    <input type="hidden" name="hide_order_id" id="hide_order_id" readonly>
                                </td>
                                <td><input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated()" /></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>