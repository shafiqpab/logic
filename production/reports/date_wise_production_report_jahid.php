<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Date wise Production Report.
Functionality	:	
JS Functions	:
Created by		:	Bilas 
Creation date 	: 	30-03-2013
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
echo load_html_head_contents("Date Wise Production Report", "../../", 1, 1,$unicode,1,1);
?>	
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';
	
	var tableFilters1 = 
	{
		col_0: "none",col_55: "none",
		col_operation: {
			id: ["total_cut_inhouse","total_cut_outside","total_printissue_td","total_printrcv_td","total_emb_iss","total_emb_re","total_emb_sent_gmt","total_emb_rec_gmt","total_wash_iss","total_wash_re","total_sp_iss","total_sp_re","total_sewin_inhouse_td","total_sewin_outbound_td","total_sewin_td","total_sewout_inhouse_td","total_sewout_outbound_td","total_sewout_td","total_sah_td","total_in_iron_td","total_out_iron_td","total_iron_td","total_iron_smv_td","total_re_iron_td","total_finish_td","total_carton_td","value_total_in_prod_dzn_td","value_total_out_prod_dzn_td","value_total_prod_dzn_td","value_total_in_cm_value_td","value_total_out_cm_value_td","value_total_cm_value_td","value_total_fob_value","value_total_in_cm_cost","value_total_out_cm_cost","value_total_cm_cost"],
			col: [14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,47],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
   	}	
	
	var tableFilters2 = 
	{
		col_0: "none",col_12: "select",col_50: "none",display_all_text: " -- All --",
		col_operation: {
			id: ["total_cut_inhouse","total_cut_outside","total_printissue_td","total_printrcv_td","total_emb_iss","total_emb_re","total_emb_gmt_iss","total_emb_gmt_re","total_wash_iss","total_wash_re","total_sp_iss","total_sp_re","total_sewin_inhouse_td","total_sewin_outbound_td","total_sewin_td","total_sewout_inhouse_td","total_sewout_outbound_td","total_sewout_td","total_iron_in_td","total_iron_out_td","total_iron_td","total_iron_smv_td","total_re_iron_td","total_finish_td","total_carton_td","value_total_in_prod_dzn_td","value_total_out_prod_dzn_td","value_total_prod_dzn_td","value_total_in_cm_value_td","value_total_out_cm_value_td","value_total_cm_value_td","value_total_in_cm_cost","value_total_out_cm_cost","value_total_cm_cost"],
			col: [17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
 	}
			
	function fn_report_generated(excel_type)
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
			freeze_window(3);
			http.open("POST","requires/date_wise_production_report_controller.php",true);
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
			append_report_checkbox('table_header_1',1);
			
			if( $("#cbo_type").val()==1 ){ setFilterGrid("table_body",-1,tableFilters1);}
			if( $("#cbo_type").val()==2 ){ setFilterGrid("table_body",-1,tableFilters2);}
			
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
			http.open("POST","requires/date_wise_production_report_controller.php",true);
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
	
	function openmypage_remark(po_break_down_id,action)
	{
		var garments_nature = $("#garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/date_wise_production_report_controller.php?po_break_down_id='+po_break_down_id+'&action='+action, 'Remarks Veiw', 'width=550px,height=450px,center=1,resize=0,scrolling=0','../');
	}

	function openmypage_order(po_break_down_id,gmts_item_id,action)
	{
		var garments_nature = $("#garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/date_wise_production_report_controller.php?po_break_down_id='+po_break_down_id+'&gmts_item_id='+gmts_item_id+'&action='+action+'&garments_nature='+garments_nature, 'Order Quantity', 'width=550px,height=250px,center=1,resize=0,scrolling=0','../');
	}
	
	function openmypage_sew_output(po_break_down_id,production_date,gmts_item_id,prod_source,page,action)
	{
		var width_pop=1050;
		var page_title="";
		if(page==4)  page_title='Sewing Input'; else  page_title='Sewing Output';
		if(action=="ironQnty_popup") page_title='Iron Entry'; 
		var garments_nature = $("#garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/date_wise_production_report_controller.php?po_break_down_id='+po_break_down_id+'&production_date='+production_date+'&gmts_item_id='+gmts_item_id+'&prod_source='+prod_source+'&page='+page+'&action='+action+'&garments_nature='+garments_nature, page_title, 'width='+width_pop+'px,height=400px,center=1,resize=0,scrolling=0','../');
	}
	
	function openmypage_sew_output2(po_break_down_id,production_date,gmts_item_id,prod_source,page,action,location_id,floor_id,sewing_line)
	{
		var width_pop=920;
		var page_title="";
		if(page==4)  page_title='Sewing Input'; else  page_title='Sewing Output';
		if(action=="ironQnty_popup") page_title='Iron Entry'; 
		var garments_nature = $("#garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/date_wise_production_report_controller.php?po_break_down_id='+po_break_down_id+'&production_date='+production_date+'&gmts_item_id='+gmts_item_id+'&prod_source='+prod_source+'&page='+page+'&action='+action+'&garments_nature='+garments_nature+'&location_id='+location_id+'&floor_id='+floor_id+'&sewing_line='+sewing_line, page_title, 'width='+width_pop+'px,height=400px,center=1,resize=0,scrolling=0','../');
	}
	
	function openmypage_location_floor(po_break_down_id,production_date,gmts_item_id,location,floor_id,sewing_line,action)
	{
		var width_pop=920;
		var page_title="";
		if(action=="sewingQnty_input_popup")  page_title='Sewing Input'; else  page_title='Sewing Output';
		var garments_nature = $("#garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/date_wise_production_report_controller.php?po_break_down_id='+po_break_down_id+'&production_date='+production_date+'&gmts_item_id='+gmts_item_id+'&location='+location+'&floor_id='+floor_id+'&sewing_line='+sewing_line+'&action='+action+'&garments_nature='+garments_nature, page_title, 'width='+width_pop+'px,height=400px,center=1,resize=0,scrolling=0','../');
	}
	
	function openmypage_popup(po_break_down_id,production_date,gmts_item_id,production_source,page_title,action)
	{
		var width_pop=720;
		var garments_nature = $("#garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/date_wise_production_report_controller.php?po_break_down_id='+po_break_down_id+'&production_date='+production_date+'&gmts_item_id='+gmts_item_id+'&production_source='+production_source+'&action='+action+'&garments_nature='+garments_nature, page_title, 'width='+width_pop+'px,height=400px,center=1,resize=0,scrolling=0','../');
	}
	
	function openmypage_popup2(po_break_down_id,production_date,gmts_item_id,page_title,action)
	{
		var width_pop=720;
		var garments_nature = $("#garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/date_wise_production_report_controller.php?po_break_down_id='+po_break_down_id+'&production_date='+production_date+'&gmts_item_id='+gmts_item_id+'&production_source='+production_source+'&action='+action+'&garments_nature='+garments_nature, page_title, 'width='+width_pop+'px,height=400px,center=1,resize=0,scrolling=0','../');
	}
	
	function openmypage_popup_location(po_break_down_id,production_date,gmts_item_id,location,floor_id,sewing_line,production_source,page_title,action)
	{
		var width_pop=720;
		var garments_nature = $("#garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/date_wise_production_report_controller.php?po_break_down_id='+po_break_down_id+'&production_date='+production_date+'&gmts_item_id='+gmts_item_id+'&location='+location+'&floor_id='+floor_id+'&sewing_line='+sewing_line+'&production_source='+production_source+'&action='+action+'&garments_nature='+garments_nature, page_title, 'width='+width_pop+'px,height=400px,center=1,resize=0,scrolling=0','../');
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
</script>
</head>
<body onLoad="set_hotkey();">
<form id="dateWiseProductionReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <fieldset style="width:910px;">
        	<legend>Search Panel</legend>
            <table class="rpt_table" width="910px" cellpadding="0" cellspacing="0" align="center" rules="all" border="1">
               <thead>                    
                    <tr>
                        <th class="must_entry_caption">Company Name</th>
                        <th>Buyer Name</th>
                        <th>Location</th>
                        <th>Floor</th>
                        <th>File No</th>
                        <th>Internal Ref</th>
                        <th class="must_entry_caption">Type</th>
                        <th>Gmts Nature</th>
                        <th id="search_text_td" class="must_entry_caption" colspan="2">Production Date</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                    </tr>    
                 </thead>
                <tbody>
                <tr class="general">
                    <td width="130"> 
                        <?
                            echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/date_wise_production_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/date_wise_production_report_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                        ?>
                    </td>
                    <td width="110" id="buyer_td">
                        <? 
                            echo create_drop_down( "cbo_buyer_name", 110, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td width="110" id="location_td">
                    	<? 
                            echo create_drop_down( "cbo_location", 110, $blank_array,"", 1, "-- Select --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td width="110" id="floor_td">
                    	<? 
                            echo create_drop_down( "cbo_floor", 110, $blank_array,"", 1, "-- Select --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td width="80">
                  	 <input type="text"  name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px;"   placeholder="Write">
                    </td>
                    <td width="80">
                  	 <input type="text"  name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px;"   placeholder="Write">
                    </td>
                    <td width="110">
                    	<? 
                            $arr = array(1=>"Show Date Wise",2=>"Show Date Location & Floor Wise",3=>"Line Wise");
							echo create_drop_down( "cbo_type", 110, $arr,"", 1, "-- Select --", 1, "",0,"" );
                        ?>
                    </td>
                    <td width="70">
                    	<? 
                            $arr = array(1=>"ALL",2=>"Woven",3=>"Knit");
							echo create_drop_down( "cbo_garments_nature", 70, $arr,"", 0, "-- Select --", $selected, "",0,"" );
                        ?>
                    </td>  
                    <td width="65">
                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" >
                    </td>  
                    <td>
                    	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date"  >
                    </td>
                    <td width="70">
                        <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(0)" />
                    </td>
                </tr>
                <tr class="general">
                	<td colspan="9" align="center"><? echo load_month_buttons(1); ?></td>
                    <td colspan="2"><input type="button" id="show_button" class="formbutton" style="width:100px;" value="Convert to Excel" onClick="fn_report_excel_generated(10)" /><a   id="aa1" href="" style="text-decoration:none" download hidden></a>
                    </td>
                </tr>
                </tbody>
            </table>
        </fieldset>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="center"></div>
 </form>    
</body>
<script>
	set_multiselect('cbo_floor','0','0','','0');
</script> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$('#cbo_location').val(0);
</script>
</html>
