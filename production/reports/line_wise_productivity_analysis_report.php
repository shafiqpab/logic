<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Line Wise Productivity Analysis Report

Functionality	:
JS Functions	:
Created by		:	Fuad
Creation date 	: 	16-01-2014
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
echo load_html_head_contents("Line Wise Productivity Analysis","../../", 1, 1, $unicode,1,1);
?>
<script>
	var permission='<? echo $permission; ?>';
	var tableFilters =
	{
		col_operation: {
			//id: ["grand_color_total","grand_fab_req","grand_fin_fab_req","grand_fab_issued_balance","grand_fab_possible_qty","grand_today_lay","grand_total_lay","grand_lay_balance","grand_today_cutting","grand_total_cutting","grand_cut_balance","grand_today_send","grand_total_send","grand_today_rcv","grand_total_rcv","grand_embl_balance","grand_today_input","grand_total_input","grand_input_balance","grand_inhand_qty"],
			//col: [17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50],
			//col: [9,11,12,13,14,15,16,17,18,19,20,21,22,23,24,,25,26,27,28,29],
			//operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			//write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
 	}

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(type)
	{


		var company = document.getElementById('cbo_company_id').value;
		var working_company = document.getElementById('cbo_wo_company_id').value;
		if ((company==0 || company=='') && (working_company==0 || working_company=='')) {
			alert('please select Company Or Working Company');
			return;
		}


		if( form_validation('txt_date','Production Date')==false )
		{
			return;
		}

		var report_title=$( "div.form_caption" ).html();

		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_wo_company_id*cbo_location_id*cbo_floor_id*cbo_line*cbo_shift_name*cbo_buyer_name*txt_date*cbo_source',"../../")+'&report_title='+report_title+'&type='+type;
		freeze_window(3);
		http.open("POST","requires/line_wise_productivity_analysis_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			//alert (http.responseText);
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflowY="auto";
		document.getElementById('scroll_body').style.maxHeight="400px";
	}

	function openmypage(company_id,order_id,item_id,location,floor_id,sewing_line,prod_date,action,prod_type,prod_reso_allo)
	{
		var popup_width='';
		if(action=="today_prod") popup_width='1000px';
		else if(action=="tot_smv_used") popup_width='700px';
		else popup_width='500px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/line_wise_productivity_analysis_report_controller.php?po_id='+order_id+'&company_id='+company_id+'&action='+action+'&item_id='+item_id+'&location='+location+'&floor_id='+floor_id+'&sewing_line='+sewing_line+'&prod_date='+prod_date+'&prod_type='+prod_type+'&prod_reso_allo='+prod_reso_allo, 'Detail Veiw', 'width='+popup_width+', height=370px,center=1,resize=0,scrolling=0','../');
	}
	function openmypage_rej(search_string,action)
	{
		var company_name=$('#cbo_company_name').val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/line_wise_productivity_analysis_report_controller.php?search_string='+search_string+'&action='+action, 'Reject Quantity', 'width=1060px,height=400px,center=1,resize=0,scrolling=0','../');
	}
	function openmypage_sew_today(company_name,color_id,prod_date,action,width,height)
	{
		var popup_width=width;
		var popup_height=height;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/line_wise_productivity_analysis_report_controller.php?company_name='+company_name+'&color_id='+color_id+'&prod_date='+prod_date+'&action='+action, 'Today Popup', 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');

	}

	function openmypage_sew_total(company_name,color_id,po_id,txt_date,action,width,height)
	{
		var popup_width=width;
		var popup_height=height;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/line_wise_productivity_analysis_report_controller.php?company_name='+company_name+'&color_id='+color_id+'&po_id='+po_id+'&txt_date='+txt_date+'&action='+action, 'Total Popup', 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');

	}
	function openmypage_sewout_today(company_name,color_id,prod_date,action,width,height)
	{
		var popup_width=width;
		var popup_height=height;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/line_wise_productivity_analysis_report_controller.php?company_name='+company_name+'&color_id='+color_id+'&prod_date='+prod_date+'&action='+action, 'Today Popup', 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');

	}
	function openmypage_sewout_total(company_name,color_id,po_id,txt_date,action,width,height)
	{
		var popup_width=width;
		var popup_height=height;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/line_wise_productivity_analysis_report_controller.php?company_name='+company_name+'&color_id='+color_id+'&po_id='+po_id+'&txt_date='+txt_date+'&action='+action, 'Total Popup', 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');

	}

	function openmypage_line_total(company_name,color_id,po_id,txt_date,action,width,height)
	{
		var popup_width=width;
		var popup_height=height;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/line_wise_productivity_analysis_report_controller.php?company_name='+company_name+'&color_id='+color_id+'&po_id='+po_id+'&txt_date='+txt_date+'&action='+action, 'Line Popup', 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');

	}

	function openmypage_po_total(company_name,color_id,po_id,txt_date,action,width,height)
	{
		var popup_width=width;
		var popup_height=height;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/line_wise_productivity_analysis_report_controller.php?company_name='+company_name+'&color_id='+color_id+'&po_id='+po_id+'&txt_date='+txt_date+'&action='+action, 'PO Balance Popup', 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');

	}
	
	


 	function show_line_remarks(company_id,order_id,floor_id,line_no,action,prod_date)
	{
		//alert(action)
		popup_width='550px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/line_wise_productivity_analysis_report_controller.php?po_id='+order_id+'&company_id='+company_id+'&floor_id='+floor_id+'&sewing_line='+line_no+'&prod_date='+prod_date+'&action='+action, 'Detail Veiw','width='+popup_width+', height=270px,center=1,resize=0,scrolling=0','../');
	}

	function getLocationId()
	{
	    var company_id = document.getElementById('cbo_wo_company_id').value;
	    // alert(floor_id);
	    var formData = company_id;

	    if(company_id !='') {
	      var data="action=load_drop_down_location&data="+formData;
	      http.open("POST","requires/line_wise_productivity_analysis_report_controller.php",true);
	      http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	      http.send(data);
	      http.onreadystatechange = function(){
	          if(http.readyState == 4)
	          {
	              var response = trim(http.responseText);
	              $('#location_td').html(response);
	          }
	      };
	    }
	}

	function getLineId()
	{
	    var company_id = document.getElementById('cbo_company_id').value;
	    var location_id = document.getElementById('cbo_location_id').value;
	    var floor_id = document.getElementById('cbo_floor_id').value;
	    var date = document.getElementById('txt_date').value;
	    // alert(floor_id);
	    var formData = floor_id+'_'+location_id+'_'+company_id+'_'+date;

	    if(company_id !='') {
	      var data="action=load_drop_down_line&formData="+formData;
	      http.open("POST","requires/line_wise_productivity_analysis_report_controller.php",true);
	      http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	      http.send(data);
	      http.onreadystatechange = function(){
	          if(http.readyState == 4)
	          {
	              var response = trim(http.responseText);
	              $('#line_td').html(response);
	          }
	      };
	    }
	}
</script>

</head>
<body onLoad="set_hotkey();">

	<form id="LineWiseProductivityAnalysis_1">
	    <div style="width:100%;" align="center">

	        <? echo load_freeze_divs ("../../",'');  ?>

	         <h3 style="width:1300px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
	         <div id="content_search_panel" >
	         <fieldset style="width:1300px;">
	             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
	             	<thead>
	                    <th class="must_entry_caption">Company</th>
	                    <th class="must_entry_caption">Working Company</th>
	                    <th class="must_entry_caption">Production Date</th>
	                    <th>Location</th>
	                    <th>Floor</th>
	                    <th>Line No</th>
	                    <th>Shift</th>
	                    <th>Buyer</th>
	                    <th>Production Source</th>
	                    <th><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('LineWiseProductivityAnalysis_1','report_container','','','')" /></th>
	                </thead>
	                <tbody>
	                    <tr class="general">
	                       <td>
								<?
	                                echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/line_wise_productivity_analysis_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id' );load_drop_down( 'requires/line_wise_productivity_analysis_report_controller', this.value, 'load_drop_down_location', 'location_td' );" );
	                            ?>
	                        </td>
	                        <td id="wo_company_td">
								<?
	                                echo create_drop_down( "cbo_wo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/line_wise_productivity_analysis_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id' );load_drop_down( 'requires/line_wise_productivity_analysis_report_controller', this.value, 'load_drop_down_location', 'location_td' );" );
	                            ?>
	                        </td>
	                         <td>
	                            <input type="text" name="txt_date" id="txt_date" class="datepicker" style="width:80px;" onChange="load_drop_down( 'requires/line_wise_productivity_analysis_report_controller',document.getElementById('cbo_floor_id').value+'_'+document.getElementById('cbo_location_id').value+'_'+document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_line', 'line_td' );" readonly/>
	                        </td>
	                        <td id="location_td">
	                            <?
	                                echo create_drop_down( "cbo_location_id", 140, $blank_array,"", 1, "-- Select Location --", "", "" );
	                            ?>
	                        </td>
	                        <td id="floor_td">
	                            <?
	                                echo create_drop_down( "cbo_floor_id", 130, $blank_array,"", 1, "-- Select Floor --", "", "" );
	                            ?>
	                        </td>
	                        <td id="line_td">
	                            <?
	                                echo create_drop_down( "cbo_line", 120, $blank_array,"", 1, "-- Select Line --", "", "" );
	                            ?>
	                        </td>
							<td>
								<?
								echo create_drop_down( "cbo_shift_name", 120, $shift_name,"", 1, "--ALL--", 0, "",0 );
								?>
                            </td>
	                        <td id="buyer_td_id">
	                            <?
	                               echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- Select Buyer --", 0, "",0 );
	                            ?>
	                        </td>
							<td>
	                            <?
	                               echo create_drop_down( "cbo_source", 100, $knitting_source,"", 1, "-- All --", 1, "",0,"1,3" );
	                            ?>
	                        </td>


	                        <td>
	                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />&nbsp;
	                            <input type="button" name="search" id="search" value="Show2" onClick="generate_report(2)" style="width:70px" class="formbutton" />&nbsp;
	                            <input type="button" name="search" id="search" value="Show3" onClick="generate_report(3)" style="width:70px" class="formbutton" />&nbsp;
								<input type="button" name="search" id="search" value="Show4" onClick="generate_report(4)" style="width:70px" class="formbutton" />&nbsp;
	                            <input type="button" name="search" id="search" value="Only Manpower" onClick="generate_report(4)" style="width:110px" class="formbutton" />
	                        </td>
	                    </tr>
	                </tbody>
	            </table>
	        </fieldset>
	    	</div>
	    </div>
	    <div id="report_container" align="center"></div>
	    <div id="report_container2" align="left" style="margin: 10px 0"></div>
 	</form>
</body>
<script>
	set_multiselect('cbo_wo_company_id','0','0','','0');
	setTimeout[($("#wo_company_td a").attr("onclick","disappear_list(cbo_wo_company_id,'0');getLocationId();") ,3000)];

	set_multiselect('cbo_floor_id','0','0','','0');
	setTimeout[($("#floor_td a").attr("onclick","disappear_list(cbo_floor_id,'0');getLineId();") ,3000)];
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$('#cbo_location_id').val(0);
</script>
</html>
