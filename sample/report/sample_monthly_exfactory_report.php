<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Monthly Ex-Factory Report [Sample].
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	03-09-2022
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
echo load_html_head_contents("Monthly Ex-Factory Report [Sample]", "../../", 1, 1,$unicode,'','');
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	var permission = '<? echo $permission; ?>';
	

	var tableFilters = 
	{
		col_operation: {
			id: ["val_sqty","val_bhqty","val_exqty","val_exval","val_texqty","val_carqty"],
			col: [19,20,21,22,23,24],
			operation: ["sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		},
 	}
	 

	function sample_stage_change(data)	
	{
		return;
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
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_location_name*cbo_buyer_name*cbo_delivery_basis*cbo_shipping_status*cbo_sample_type*cbo_sample_stage*txt_req_no*txt_date_from*txt_date_to*cbo_year_selection',"../../")+'&excel_type='+excel_type+'&report_title='+report_title;
			//alert(data);return;
			freeze_window(3);
			http.open("POST","requires/sample_monthly_exfactory_report_controller.php",true);
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
        <?=load_freeze_divs ("../../",'');  ?>
         <fieldset style="width:1000px;">
        	<legend>Search Panel</legend>
            <table class="rpt_table" width="985px" cellpadding="0" cellspacing="0" align="center" border="1" rules="all">
               <thead>                    
                    <tr>
						<th width="130" class="must_entry_caption">Company</th>
						<th width="130">Location</th>
						<th width="130">Buyer</th>
                        <th width="80">Delivery Basis</th>
                        <th width="80">Delivery Status</th>         
						<th width="70">Sample Type</th>
                        <th width="80">Sample Stage</th>                     
                        <th width="70">Requisition No</th>                   
                        <th width="130" colspan="2" id="search_text_td">Ex-Factory Date Range</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                    </tr>    
                 </thead>
                <tbody>
                    <tr class="general">
                        <td><?=create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sample_monthly_exfactory_report_controller', this.value, 'load_drop_down_location', 'location_td' ); load_drop_down( 'requires/sample_monthly_exfactory_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                        <td id="location_td"><?=create_drop_down( "cbo_location_name", 130, $blank_array,"", 1, "-Select Location-", $selected, "" ); ?></td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- Select Buyer --", $selected, "",0,"" ); ?></td>
                        <td><?=create_drop_down( "cbo_delivery_basis", 80, $sample_delivery_basis,"", 1, "-Select Delivery Basis-", 1, "",0 ); ?></td>
                        <td><?=create_drop_down( "cbo_shipping_status", 80, $shipment_status,"", 1, "-- Select --", 0, "",0,'','','','','' ); ?></td>
                        <td><?=create_drop_down( "cbo_sample_type", 70, $sample_type,"", 1, "-- Select --", 14, "" ); ?></td>
                        <td><?=create_drop_down( "cbo_sample_stage", 80, $sample_stage,"", 1, "--Select Stage--", $selected, "sample_stage_change(this.value);",0,"" ); ?></td>
                        <td><input type="text"  name="txt_req_no" id="txt_req_no" class="text_boxes_numeric" style="width:60px;" placeholder="Write"></td>                     
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date" ></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To Date" ></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(0);" /></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="11"><?=load_month_buttons(1); ?></td>
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
