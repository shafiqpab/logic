<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Sample Progress Report.
Functionality	:	
JS Functions	:
Created by		:	Rehan Uddin
Creation date 	: 	18-04-2017
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

//---------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sample Progress Report", "../../", 1, 1,$unicode,'','');
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	var permission = '<? echo $permission; ?>';
	

	 var tableFilters = 
	{
		
		col_operation: {
			// id: ["sample_req_qty","cuttting_qty","cuttting_bal","print_issue","print_rcv","embro_issue","embro_rcv","spworks_issue","spworks_rcv","sewing_input_qty","sewing_output_qty","wash_issue","wash_rcv","others_reject","sewing_reject","delivery_qty","ex_factory_qty"],
			col: [15,18,19,20,21,22,23,24,25,26],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		},
 	}

	
		
	function sample_stage_change(data)	
	{
 		if(data.trim()==2 ||  data.trim()==3 || data==0)  
 		{
	 		$("#txt_file_no").val('');
	 		$("#txt_internal_ref").val('');
	 		$("#txt_job_no").val('');
	 		$("#txt_order_no").val('');
	 		$("#txt_file_no").attr("disabled",'');
	 		$("#txt_internal_ref").attr("disabled",'');
	 		$("#txt_job_no").attr("disabled",'');
	 		$("#txt_order_no").attr("disabled",'');
 	    }
 	    else
 	    {
 	    	$("#txt_file_no").removeAttr("disabled",'');
	 		$("#txt_internal_ref").removeAttr("disabled",'');
	 		$("#txt_job_no").removeAttr("disabled",'');
	 		$("#txt_order_no").removeAttr("disabled",'');
 	    }
	}

	function fn_report_generated(excel_type)
	{
		if(form_validation('cbo_company_name','Company Name')==false)	
		{
			return;
		}
		else
		{
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_sample_stage*cbo_buyer_name*cbo_dealing_merchant*cbo_year*txt_req_no*txt_booking_no*txt_file_no*txt_internal_ref*txt_date_from*txt_date_to',"../../")+'&excel_type='+excel_type;
			//alert(data);return;
			freeze_window(3);
			http.open("POST","requires/sample_details_report_controller.php",true);
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
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
		
			setFilterGrid("table_body",-1,tableFilters);
			
		
			
			show_msg('3');
			release_freezing();
		}
	}
	
	function fn_report_excel_generated(excel_type) //Excel Convert Only
	{
		if(document.getElementById('cbo_type').value==3){
			var fvd=form_validation('cbo_company_name*cbo_location*cbo_type*txt_date_from*txt_date_to','Comapny Name*Location*Report Type*From Date*To Date');	
		}
		else
		{
			var fvd=form_validation('cbo_type*txt_date_from*txt_date_to','Report Type*From Date*To Date');	
		}
		
		if (fvd==false)
		{
			return;
		}
		else
		{
			var data="action=report_generate"+get_submitted_data_string('cbo_garments_nature*cbo_company_name*cbo_buyer_name*cbo_location*cbo_floor*txt_file_no*txt_internal_ref*cbo_type*txt_date_from*txt_date_to',"../../")+'&excel_type='+excel_type;
			//alert(data);return;
			freeze_window(3);
			http.open("POST","requires/sample_details_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse2;
		}
	}

	function fn_report_generated_reponse2()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####"); 
			if(response!='')
			{
				$('#aa1').removeAttr('href').attr('href','requires/'+response[0]);
				document.getElementById('aa1').click();
			}
			show_msg('3');
			release_freezing();
		}
	}
	
	function openmypage_ex_factory(sample_req_mst_id,sample_name,action)
	{
  		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sample_details_report_controller.php?sample_req_mst_id='+sample_req_mst_id+'&sample_name='+sample_name+'&action='+action, 'MnM Ex-Factory Date  View', 'width=500px,height=450px,center=1,resize=0,scrolling=0','');
	}
	
	function openmypage_remark(sample_dtls_id,action,smp_dev_mst_id)
	{
  		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sample_details_report_controller.php?sample_dtls_id='+sample_dtls_id+'&smp_dev_mst_id='+smp_dev_mst_id+'&action='+action, 'Remarks View', 'width=500px,height=450px,center=1,resize=0,scrolling=0','');
	}

	





	function openmypage_reject_qty(sample_dtls_id,action)
	{
  		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sample_details_report_controller.php?sample_dtls_id='+sample_dtls_id+'&action='+action, 'Remarks View', 'width=500px,height=450px,center=1,resize=0,scrolling=0','');
	}



	function openmypage_order(po_break_down_id,gmts_item_id,action)
	{
		var garments_nature = $("#garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sample_details_report_controller.php?po_break_down_id='+po_break_down_id+'&gmts_item_id='+gmts_item_id+'&action='+action+'&garments_nature='+garments_nature, 'Order Quantity', 'width=550px,height=250px,center=1,resize=0,scrolling=0','');
	}
	


	
	function openmypage_location_floor(po_break_down_id,production_date,gmts_item_id,location,floor_id,sewing_line,action)
	{
		var width_pop=920;
		var page_title="";
		if(action=="sewingQnty_input_popup")  page_title='Sewing Input'; else  page_title='Sewing Output';
		var garments_nature = $("#garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sample_details_report_controller.php?po_break_down_id='+po_break_down_id+'&production_date='+production_date+'&gmts_item_id='+gmts_item_id+'&location='+location+'&floor_id='+floor_id+'&sewing_line='+sewing_line+'&action='+action+'&garments_nature='+garments_nature, page_title, 'width='+width_pop+'px,height=400px,center=1,resize=0,scrolling=0','../../');
	}
	

	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00") document.getElementById(v_id).bgColor=e_color;
		else document.getElementById(v_id).bgColor="#33CC00";
	}
</script>
</head>
<body onLoad="set_hotkey();">

<form id="SampleProgressReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <fieldset style="width:1000px;">
        	<legend>Search Panel</legend>
            <table class="rpt_table" width="1000px" cellpadding="0" cellspacing="0" align="center" border="1" rules="all">
               <thead>                    
                    <tr>
						<th width="130" class="must_entry_caption">Company Name</th>
						<th width="110">Sample Stage</th>
						<th width="110">Buyer Name</th>
                        <th width="110">Dealing Merchant</th>
                        <th width="60">Year</th>         
						<th width="70">Booking No</th>                     
                        <th width="70">Requisition No</th>                   
                        <th width="70">File No</th>
                        <th width="70">Intl.Ref.No</th>
                        <th width="130" colspan="2" id="search_text_td">Delivery Date Range</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                    </tr>    
                 </thead>
                <tbody>
                    <tr class="general">
                        <td><? echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sample_details_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                        <td><? echo create_drop_down( "cbo_sample_stage", 110, $sample_stage,"", 1, "-- Select Stage --", $selected, "sample_stage_change(this.value);",0,"" ); ?></td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 110, $blank_array,"", 1, "-- Select Buyer --", $selected, "",0,"" ); ?></td>
                        <td id="dealing_merchant_td"><? echo create_drop_down( "cbo_dealing_merchant", 110, "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" ); ?></td>
                        <td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>         
						<td><input type="text"  name="txt_booking_no" id="txt_booking_no" class="text_boxes_numeric" style="width:60px;" placeholder="Write"></td>                           
                        <td><input type="text"  name="txt_req_no" id="txt_req_no" class="text_boxes_numeric" style="width:60px;" placeholder="Write"></td>                     
                        <td><input type="text"  name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:60px;" placeholder="Write"></td>
                        <td><input type="text"  name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:60px;" placeholder="Write"></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" ></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" ></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(0)" /></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="14"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="center"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
