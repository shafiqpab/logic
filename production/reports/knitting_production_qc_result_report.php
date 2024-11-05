<?
/*-------------------------------------------- Comments

Purpose			: 	This form will Create Knitting Production QC Result Roll Wise Report
					
Functionality	:	
				

JS Functions	:

Created by		:	Kaiyum
Creation date 	: 	9-11-2016
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
echo load_html_head_contents("Knitting Production QC Result Roll Wise Report", "../../", 1, 1,'','',1);

?>
 <script src="../../Chart.js-master/Chart.js"></script>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	var tableFilters = 
	 {
		col_60: "none",
		col_operation: {
		id: ["value_total_grey_qnty"],
	   	col: [16],
	   	operation: ["sum"],
	   	write_method: ["innerHTML"]
		}
	 }
	 
	function fn_report_generated()
	{
		var txt_file_no=$('#txt_file_no').val();
		var txt_job_no=$('#txt_job_no').val();
		var txt_order_no=$('#txt_order_no').val();
		var txt_inter_ref=$('#txt_inter_ref').val();
		var txt_barcode_no=$('#txt_barcode_no').val();
		var txt_program_no=$('#txt_program_no').val();
		if(txt_file_no=="" && txt_job_no=="" && txt_order_no=="" && txt_inter_ref=="" && txt_barcode_no=="" && txt_program_no=="")
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*Date From*Date To')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_name','Company')==false)
			{
				return;
			}
		}
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_file_no*txt_job_no*txt_order_no*txt_inter_ref*txt_barcode_no*txt_program_no*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
		
		freeze_window(5);
		http.open("POST","requires/knitting_production_qc_result_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			//alert(response[0]); return;
			$("#report_container2").html(response[0]);  
			document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
	 		show_msg('3');			
			release_freezing();

			/*release_freezing();
			var reponse=trim(http.responseText).split("####");
			//alert(reponse);
			$('#report_container2').html(reponse[0]);
			//setFilterGrid("table_body",-1,tableFilters); // for HTML Search

			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
	 		show_msg('3');*/
		}
	}

	/*function new_window()
	{
		$('#table_body tr:first').hide();		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		$('#table_body tr:first').show();
	}*/

	function openmypage(po_id,color_id)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/knitting_production_qc_result_report_controller.php?po_id='+po_id+'&color_id='+color_id+'&action=color_popup', 'Detail Veiw', 'width=860px, height=370px,center=1,resize=0,scrolling=0','../');
	}
	
	
	function openmypage_popup(roll_id,action)
	{
		emailwindow=dhtmlmodal.open('EmailBox','iframe','requires/knitting_production_qc_result_report_controller.php?roll_id='+roll_id+'&action='+action, 'Roll Popup', 'width=1100px,height=250px,center=1,resize=0,scrolling=0','../');
		emailwindow.onclose=function()
		{
			
		}
		
	}
	function openmypage_popup_qc(barcode_no,action)
	{
		emailwindow=dhtmlmodal.open('EmailBox','iframe','requires/knitting_production_qc_result_report_controller.php?barcode_no='+barcode_no+'&action='+action, 'QC Result Popup', 'width=620px,height=250px,center=1,resize=0,scrolling=0','../');
		emailwindow.onclose=function()
		{
			
		}
		
	}

</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",''); ?>
		 <form name="knitDyeingLoadReport_1" id="knitDyeingLoadReport_1"> 
         <h3 style="width:1250px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
             <fieldset style="width:1250px;">
                 <table class="rpt_table" width="1250" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th class="must_entry_caption" width="150">Company Name</th>
                            <th  width="150">Buyer Name</th>
                            <th  width="110">File No</th>
                            <th  width="110">Job No</th>
                            <th  width="110">Order No</th>
                            <th  width="110">Internal Reff No</th>
                            <th  width="110">Barcode No</th>
                            <th width="80">Program No</th>
                            <th class="must_entry_caption" width="160" id="pdate">Production Date</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('knitDyeingLoadReport_1','report_container*report_container2','','','')" class="formbutton" style="width:100px" /></th>
                        </thead>
                        <tbody>
                            <tr class="general" align="center">
                               <td> 
									<?
                                        echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/knitting_production_qc_result_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                                    ?>
                                </td>
                                <td id="buyer_td" align="center">
                                    <? 
                                        echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --", $selected, "",0,"" );
                                    ?>
                                </td>
                                <td align="center">
                                     <input type="text" name="txt_file_no" id="txt_file_no" class="text_boxes" placeholder="Write" style="width:90px" />
                                </td>
                                <td align="center">
                                     <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" placeholder="Write" style="width:90px" />
                                </td>
                                <td align="center">
                                     <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" placeholder="Write" style="width:90px" />
                                </td>
                                <td align="center">
                                     <input type="text" name="txt_inter_ref" id="txt_inter_ref" class="text_boxes" placeholder="Write" style="width:90px" />
                                </td>
                                <td align="center">
                                     <input type="text" name="txt_barcode_no" id="txt_barcode_no" class="text_boxes" placeholder="Write" style="width:90px" />
                                </td>
                                <td>
                                	<input type="text" name="txt_program_no" id="txt_program_no" class="text_boxes" style="width:70px" placeholder="Write" o  autocomplete="off">
                        		</td>
                                <td align="center">
                               	<input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:55px" placeholder="From Date"/>
                               	To
                               	<input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:55px" placeholder="To Date"/>
                              	</td>
                                <td><input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated()" /></td>
                            </tr>
                            <tr>
	                            <td colspan="9" align="center" width="95%">
								 <? 
								 echo load_month_buttons(1); ?></td>
                        	</tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="center"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>