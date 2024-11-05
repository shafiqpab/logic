<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Buyer wise Sample production Report.
Created by		:	Zakaria joy
Creation date 	: 	21-09-2023
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		: Gbl temp id: 134
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$basis_arr=array(1=>'Requisition', 2=>'Production', 3=>'Delivery');

//---------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Data Archiving Report", "../../", 1, 1,$unicode,1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	var tableFilters1 = 
	{
		// col_0: "none",col_53: "none",
		col_operation: {
			id: ["total_cut_td","total_printissue_td","total_printrcv_td","total_emb_iss","total_emb_re","total_wash_iss","total_wash_re","total_sp_iss","total_sp_re","total_sewin_inhouse_td","total_sewin_outbound_td","total_sewin_td","total_sewout_inhouse_td","total_sewout_outbound_td","total_sewout_td","total_in_iron_td","total_out_iron_td","total_iron_td","total_iron_smv_td","total_re_iron_td","total_finish_td","total_carton_td","value_total_in_prod_dzn_td","value_total_out_prod_dzn_td","value_total_prod_dzn_td","value_total_in_cm_value_td","value_total_out_cm_value_td","value_total_cm_value_td","value_total_in_cm_cost","value_total_out_cm_cost","value_total_cm_cost"],
			col: [14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,43,44,45],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
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
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_sample_id*txt_item_id*cbo_basis*txt_date_from*txt_date_to',"../")+'&excel_type='+excel_type;
			freeze_window(3);
			http.open("POST","requires/buyer_wise_sample_production_report_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			// document.getElementById('report_container').innerHTML=report_convert_button('../'); 
			// append_report_checkbox('table_header_1',1);			
			setFilterGrid("table_body",-1,tableFilters1);			
			show_msg('3');
			release_freezing();
		}
	}
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		$("#table_body tr:first").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="350px";
		$("#table_body tr:first").show();
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
			var data="action=report_generate"+get_submitted_data_string('cbo_garments_nature*cbo_company_name*cbo_buyer_name*cbo_location*cbo_floor*txt_file_no*txt_internal_ref*cbo_type*txt_date_from*txt_date_to',"../")+'&excel_type='+excel_type;
			//alert(data);return;
			freeze_window(3);
			http.open("POST","requires/buyer_wise_sample_production_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse2;
		}
	}

	
	
	function open_popup(arg,page_title,action)
	{
		var width_pop=720;
		var garments_nature = $("#garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/buyer_wise_sample_production_report_controller.php?action='+action+'&data='+arg+'&garments_nature='+garments_nature, page_title, 'width='+width_pop+'px,height=400px,center=1,resize=0,scrolling=0','../');
	}	
	
	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00") document.getElementById(v_id).bgColor=e_color;
		else document.getElementById(v_id).bgColor="#33CC00";
	}
	function sample_item_popup(type){
		var title="";
		if(type==1){
			title="Sample Name Search";
		}
		else{
			title="Item Name Search";
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/buyer_wise_sample_production_report_controller.php?data='+type+'&action=sample_item_popup', title, 'width=450px,height=400px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var sampleitem=this.contentDoc.getElementById("txt_sample_item").value;
			var response=sampleitem.split('_');
			console.log(sampleitem);
			if (sampleitem!="" )
			{
				freeze_window(5);
				if(response[2]==1){
					$("#txt_sample_name").val(response[0]);
					$("#txt_sample_id").val(response[1]);
				}
				if(response[2]==2){
					$("#txt_item_name").val(response[0]);
					$("#txt_item_id").val(response[1]);
				}				
				release_freezing();
			}
		}
	}
</script>
</head>
<body onLoad="set_hotkey();">

<form id="SampleProgressReport_1">
    <div style="width:100%;" align="center">    
		<? echo load_freeze_divs ("../../",$permission); ?>  
         <fieldset style="width:600px;">
        	<legend>Search Panel</legend>
            <table class="rpt_table" width="600px" cellpadding="0" cellspacing="0" align="center" border="1" rules="all">
               <thead>                    
                    <tr>
						<th width="130" class="must_entry_caption">Company Name</th>
						<th width="130">Buyer Name</th>
                        <th width="130">Sample Name</th>
                        <th width="130">Item Name</th>
                        <th width="100">Basis</th>
                        <th width="140" colspan="2">Date Range</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onclick="reset_form('','report_container*report_container2','txt_sample_id*txt_item_id','','')" /></th>
                    </tr>    
                 </thead>
                <tbody>
                    <tr class="general">
                        <td><? echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/buyer_wise_sample_production_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- Select Buyer --", $selected, "",0,"" ); ?></td>
                        <td>
                            <input type="text"  name="txt_sample_name" id="txt_sample_name" class="text_boxes" style="width:130px;" placeholder="Browse" ondblclick="sample_item_popup(1)" readonly>
                            <input type="hidden"  name="txt_sample_id" id="txt_sample_id" value="">
                        </td>
                        <td>
                            <input type="text" name="txt_item_name" id="txt_item_name" class="text_boxes" style="width:130px;" placeholder="Browse" ondblclick="sample_item_popup(2)" readonly>
                            <input type="hidden"  name="txt_item_id" id="txt_item_id" value="">
                        </td>
                        <td><? echo create_drop_down( "cbo_basis", 100, $basis_arr,"", 0, "-- Select Stage --", 1, "",0,"" ); ?></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" ></td>
                        <td>
                        	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" >
                        	<input type="hidden" id="report_ids" name="report_ids"/>
                        </td>
                        <td id="data_panel">
							<input type="button"  id="show_button" class="formbutton" style="width:90px;" value="Show"  name="show"  onClick="fn_report_generated(0)" />
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="12"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    </div>
    <div id="report_container" align="center" style="padding: 10px;"></div>
    <div id="report_container2" align="center"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
