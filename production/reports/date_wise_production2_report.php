<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Date wise Production Report.
Functionality	:
JS Functions	:
Created by		:	Arnab
Creation date 	: 	10-10-2023
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
			id: ["total_order_qty","total_cut_inhouse","total_cut_outside","total_printissue_td","total_printrcv_td","total_emb_iss","total_emb_re","total_emb_sent_gmt","total_emb_rec_gmt","total_wash_iss","total_wash_re","total_sp_iss","total_sp_re","total_cut_delv_qty_td","total_sewin_inhouse_td","total_sewin_outbound_td","total_sewin_td","total_sewout_inhouse_td","total_sewout_outbound_td","total_sewout_td","total_sewout_sah_in_td","total_sewout_sah_out_td","total_sah_td","total_in_iron_td","total_out_iron_td","total_iron_td","total_iron_smv_td","total_re_iron_td","total_finish_td","total_carton_td","value_total_in_prod_dzn_td","value_total_out_prod_dzn_td","value_total_prod_dzn_td","value_total_in_cm_value_td","value_total_out_cm_value_td","value_total_cm_value_td","value_total_fob_in_value","value_total_fob_out_value","value_total_fob_value","value_total_in_cm_cost","value_total_out_cm_cost","value_total_cm_cost"],//42
			//col: [14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,47],
			col: [10,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
   	}

	var tableFilters2 =
	{
		col_0: "none",col_12: "select",col_51: "none",display_all_text: " -- All --",
		col_operation: {
			id: ["total_cut_inhouse","total_cut_outside","total_printissue_td","total_printrcv_td","total_emb_iss","total_emb_re","total_emb_gmt_iss","total_emb_gmt_re","total_wash_iss","total_wash_re","total_sp_iss","total_sp_re","total_cut_delv_qty_td","total_sewin_inhouse_td","total_sewin_outbound_td","total_sewin_td","total_sewout_inhouse_td","total_sewout_outbound_td","total_sewout_td","total_fob_td","total_iron_in_td","total_iron_out_td","total_iron_td","total_iron_smv_td","total_re_iron_td","total_finish_td","total_carton_td","value_total_in_prod_dzn_td","value_total_out_prod_dzn_td","value_total_prod_dzn_td","value_total_in_cm_value_td","value_total_out_cm_value_td","value_total_cm_value_td","value_total_in_cm_cost","value_total_out_cm_cost","value_total_cm_cost"],
			//col: [17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50],
			col: [18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
 	}

	function fn_report_generated(excel_type)
	{
		// if(document.getElementById('cbo_type').value==3)
		// {
		// 	var fvd=form_validation('cbo_company_id*cbo_working_company_id*cbo_location*cbo_type*txt_date_from*txt_date_to','LC Comapny *Location*Report Type*From Date*To Date');
		// }
		// else
		// {
		// 	var fvd=form_validation('cbo_type*txt_date_from*txt_date_to','Report Type*From Date*To Date');
		// }
		// if (fvd==false)
		// {
		// 	return;
		// }
		if($('#cbo_company_id').val()==0){
			var data='cbo_working_company_id*txt_date_from*txt_date_to';
			var filed='Working Company Name*From Date*Date To';
		}
		else
		{
			var data='cbo_company_id*txt_date_from*txt_date_to';
			var filed='Company Name*From Date*Date To';
		}
		if( form_validation(data,filed)==false )
		{
			return;
		}
		else
		{
			var fvd=form_validation('cbo_company_id*cbo_working_company_id*cbo_location*cbo_type*txt_date_from*txt_date_to','LC Comapny*Working Company*Location*Report Type*From Date*To Date');
			var data="action=report_generate"+get_submitted_data_string('cbo_garments_nature*cbo_company_id*cbo_working_company_id*cbo_buyer_name*cbo_location*cbo_floor*txt_file_no*txt_internal_ref*cbo_type*txt_date_from*txt_date_to',"../../")+'&excel_type='+excel_type;
		}


			freeze_window(3);
			http.open("POST","requires/date_wise_production2_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}


	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("####");
			$('#report_container2').css('display','block');
			$('#report_container2').html(reponse[0]);

			if(reponse[1]==0)
			{
				//document.getElementById('report_container').innerHTML=report_convert_button('../../');


				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[2]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';


				append_report_checkbox('table_header_1',1);
				if( $("#cbo_type").val()==1 ){ setFilterGrid("table_body",-1,tableFilters1);}
				if( $("#cbo_type").val()==2 ){ setFilterGrid("table_body",-1,tableFilters2);}
			}
			else
			{
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[2]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			}


			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		document.getElementById('scroll_body2').style.overflow="auto";
		document.getElementById('scroll_body2').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="325px";

		document.getElementById('scroll_body2').style.overflowY="scroll";
		document.getElementById('scroll_body2').style.maxHeight="200px";

	}

	function fn_report_excel_generated(excel_type) //Excel Convert Only
	{
		var cbo_type=$('#cbo_type').val()*1;
		if(excel_type==10 && cbo_type !=1)
		{
			alert("Convert to Excel Button Only Allow For Show Date Wise Type.");return;
		}

		if(document.getElementById('cbo_type').value==3){
			var fvd=form_validation('cbo_company_name*cbo_company_id*cbo_working_company_id*cbo_location*cbo_type*txt_date_from*txt_date_to','LC Comapny*Working Company*Location*Report Type*From Date*To Date');
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
			var data="action=report_generate"+get_submitted_data_string('cbo_garments_nature*cbo_company_id*cbo_working_company_id*cbo_buyer_name*cbo_location*cbo_floor*txt_file_no*txt_internal_ref*cbo_type*txt_date_from*txt_date_to',"../../")+'&excel_type='+excel_type;
			//alert(data);return;
			freeze_window(3);
			http.open("POST","requires/date_wise_production2_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse2;
		}
	}

	function fn_report_generated_reponse2()
	{
		if(http.readyState == 4)
		{
			//alert(2333);
			var response=trim(http.responseText).split("####");
			$('#report_container2').html(response[0]);
			$('#report_container2').css('display','none');
			if(response!='')
			{
				//document.getElementById('report_container').innerHTML='<a href="requires/'+response[2]+'" style="text-decoration:none">';

				 print_priview_html( 'report_container2', 'scroll_body','table_header_1','report_table_footer', 1, '0','../../' ) ;



				//$('#aa1').removeAttr('href').attr('href','requires/'+response[0]);
				// document.getElementById('aa1').click();
			}
			//$('#report_container2').css('display','block');
			show_msg('3');
			release_freezing();
		}
	}

	function openmypage_remark(po_break_down_id,action)
	{
		var garments_nature = $("#garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/date_wise_production2_report_controller.php?po_break_down_id='+po_break_down_id+'&action='+action, 'Remarks Veiw', 'width=550px,height=450px,center=1,resize=0,scrolling=0','../');
	}

	function openmypage_order(po_break_down_id,gmts_item_id,action)
	{
		var garments_nature = $("#garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/date_wise_production2_report_controller.php?po_break_down_id='+po_break_down_id+'&gmts_item_id='+gmts_item_id+'&action='+action+'&garments_nature='+garments_nature, 'Order Quantity', 'width=550px,height=250px,center=1,resize=0,scrolling=0','../');
	}

	function openmypage_sew_output(po_break_down_id,production_date,gmts_item_id,prod_source,page,action)
	{
		var width_pop=1050;
		var page_title="";
		if(page==4)  page_title='Sewing Input'; else  page_title='Sewing Output';
		if(action=="ironQnty_popup") page_title='Iron Entry';
		var garments_nature = $("#garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/date_wise_production2_report_controller.php?po_break_down_id='+po_break_down_id+'&production_date='+production_date+'&gmts_item_id='+gmts_item_id+'&prod_source='+prod_source+'&page='+page+'&action='+action+'&garments_nature='+garments_nature, page_title, 'width='+width_pop+'px,height=400px,center=1,resize=0,scrolling=0','../');
	}

	function openmypage_sew_output2(po_break_down_id,production_date,gmts_item_id,prod_source,page,action,location_id,floor_id,sewing_line)
	{
		var width_pop=920;
		var page_title="";
		if(page==4)  page_title='Sewing Input'; else  page_title='Sewing Output';
		if(action=="ironQnty_popup") page_title='Iron Entry';
		var garments_nature = $("#garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/date_wise_production2_report_controller.php?po_break_down_id='+po_break_down_id+'&production_date='+production_date+'&gmts_item_id='+gmts_item_id+'&prod_source='+prod_source+'&page='+page+'&action='+action+'&garments_nature='+garments_nature+'&location_id='+location_id+'&floor_id='+floor_id+'&sewing_line='+sewing_line, page_title, 'width='+width_pop+'px,height=400px,center=1,resize=0,scrolling=0','../');
	}

	function openmypage_location_floor(po_break_down_id,production_date,gmts_item_id,location,floor_id,sewing_line,action)
	{
		var width_pop=920;
		var page_title="";
		if(action=="sewingQnty_input_popup")  page_title='Sewing Input'; else  page_title='Sewing Output';
		var garments_nature = $("#garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/date_wise_production2_report_controller.php?po_break_down_id='+po_break_down_id+'&production_date='+production_date+'&gmts_item_id='+gmts_item_id+'&location='+location+'&floor_id='+floor_id+'&sewing_line='+sewing_line+'&action='+action+'&garments_nature='+garments_nature, page_title, 'width='+width_pop+'px,height=400px,center=1,resize=0,scrolling=0','../');
	}

	function openmypage_popup(po_break_down_id,production_date,gmts_item_id,production_source,page_title,action)
	{
		var width_pop=720;
		var garments_nature = $("#garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/date_wise_production2_report_controller.php?po_break_down_id='+po_break_down_id+'&production_date='+production_date+'&gmts_item_id='+gmts_item_id+'&production_source='+production_source+'&action='+action+'&garments_nature='+garments_nature, page_title, 'width='+width_pop+'px,height=400px,center=1,resize=0,scrolling=0','../');
	}

	function openmypage_popup2(po_break_down_id,production_date,gmts_item_id,page_title,action)
	{
		var width_pop=720;
		var garments_nature = $("#garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/date_wise_production2_report_controller.php?po_break_down_id='+po_break_down_id+'&production_date='+production_date+'&gmts_item_id='+gmts_item_id+'&production_source='+production_source+'&action='+action+'&garments_nature='+garments_nature, page_title, 'width='+width_pop+'px,height=400px,center=1,resize=0,scrolling=0','../');
	}

	function openmypage_popup_location(po_break_down_id,production_date,gmts_item_id,location,floor_id,sewing_line,production_source,page_title,action)
	{
		var width_pop=720;
		var garments_nature = $("#garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/date_wise_production2_report_controller.php?po_break_down_id='+po_break_down_id+'&production_date='+production_date+'&gmts_item_id='+gmts_item_id+'&location='+location+'&floor_id='+floor_id+'&sewing_line='+sewing_line+'&production_source='+production_source+'&action='+action+'&garments_nature='+garments_nature, page_title, 'width='+width_pop+'px,height=400px,center=1,resize=0,scrolling=0','../');
	}

	function print_report_button_setting(report_ids)
    {

        $('#show_button').hide();
        $('#show_button3').hide();
        var report_id=report_ids.split(",");
        report_id.forEach(function(items){
            if(items==108){$('#show_button').show();}
            else if(items==745){$('#show_button3').show();}
            });
    }
	function openmypage_party(type)
	{
		var page_link='requires/date_wise_production2_report_controller.php?action=party_popup&type='+type;
		var title='Company Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=430px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var party_name=this.contentDoc.getElementById("hide_party_name").value;
			var party_id=this.contentDoc.getElementById("hide_party_id").value;
			var poptype=this.contentDoc.getElementById("hidd_type").value;
			if(poptype==1)
			{
				$('#txt_company_name').val(party_name);
				$('#cbo_company_id').val(party_id);
				load_drop_down( 'requires/date_wise_production2_report_controller', party_id, 'load_drop_down_location', 'location_td' );
				load_drop_down( 'requires/date_wise_production2_report_controller', party_id, 'load_drop_down_buyer', 'buyer_td' );
				get_php_form_data(party_id,'print_button_variable_setting','requires/date_wise_production2_report_controller' );

			}
			else if (poptype==2)
			{
				$('#txt_working_company_name').val(party_name);
				$('#cbo_working_company_id').val(party_id);
				load_drop_down( 'requires/date_wise_production2_report_controller', party_id, 'load_drop_down_location', 'location_td' );
				load_drop_down( 'requires/date_wise_production2_report_controller', party_id, 'load_drop_down_buyer', 'buyer_td' );
				get_php_form_data(party_id,'print_button_variable_setting','requires/date_wise_production2_report_controller' );
			}
		}
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
         <fieldset style="width:1060px;">
        	<legend>Search Panel</legend>
            <table class="rpt_table" width="1060px" cellpadding="0" cellspacing="0" align="center" rules="all" border="1">
               <thead>
                    <tr>
                        <th class="must_entry_caption">LC Company</th>
                        <th class="must_entry_caption">Working Company</th>
                        <th>Buyer Name</th>
                        <th>Location</th>
                        <th>Floor</th>
                        <th>File No</th>
                        <th>Internal Ref</th>
                        <th class="must_entry_caption">Type</th>
                        <th>Gmts Nature</th>
                        <th id="search_text_td" class="must_entry_caption" colspan="2">Production Date</th>
                        <th width="170"><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                    </tr>
                 </thead>
                <tbody>
                <tr class="general">
                    <td id="cbo_company_id_td">
					<input type="text" id="txt_company_name" name="txt_company_name" class="text_boxes" style="width:100px" onDblClick="openmypage_party(1);" placeholder="Browse Party" readonly />
					<input type="hidden" id="cbo_company_id" name="cbo_company_id" class="text_boxes" style="width:30px" />
                        <?
                            // echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/date_wise_production2_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/date_wise_production2_report_controller', this.value, 'load_drop_down_location', 'location_td');get_php_form_data(this.value,'print_button_variable_setting','requires/date_wise_production2_report_controller' );" );

                        ?>
                    </td>
					<td id="cbo_working_company_id_td">
					<input type="text" id="txt_working_company_name" name="txt_working_company_name" class="text_boxes" style="width:100px" onDblClick="openmypage_party(2);" placeholder="Browse Party" readonly />
					<input type="hidden" id="cbo_working_company_id" name="cbo_working_company_id" class="text_boxes" style="width:30px" />
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
                            echo create_drop_down( "cbo_floor", 110, $blank_array,"", 0, "-- Select --", $selected, "",1,"" );
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
                    	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date" >
                    </td>
                    <td width="170">
                        <input type="button" id="show_button" class="formbutton" style="width:50px;display: none;" value="Show" onClick="fn_report_generated(0)" />

                    </td>
                </tr>
                <tr class="general">
                	<td colspan="8" align="center"><? echo load_month_buttons(1); ?></td>
                    <td colspan="4">
                    <input type="button" id="show_button3" class="formbutton" style="width:100px;display: none;;" value="Convert to Excel" onClick="fn_report_excel_generated(10)"/><a id="aa1" href="" style="text-decoration:none" download hidden></a>
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