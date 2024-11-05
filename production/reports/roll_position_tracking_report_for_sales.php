<?
/*-------------------------------------------- Comments

Purpose			: 	This form will Create Roll Position Tracking Report
Functionality	:
JS Functions	:

Created by		:	Jahid
Creation date 	: 	20-10-2015
Updated by 		: 	tofazzal Al Hoque	
Update date		: 	29/04/2017	   

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
echo load_html_head_contents("Roll Position Tracking Report", "../../", 1, 1,'','',1);

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
	   	col: [15],
	   	operation: ["sum"],
	   	write_method: ["innerHTML"]
		}
	 }
	 
	function fn_report_generated(type)
	{
		var txt_sales_order_no=$('#txt_sales_order_no').val();
		var txt_style_ref_no=$('#txt_style_ref_no').val();
		var txt_booking_no=$('#txt_booking_no').val();
		var txt_barcode_no=$('#txt_barcode_no').val();
		var txt_internal_ref=$('#txt_internal_ref').val();
		var cbo_buyer_name=$('#cbo_buyer_name').val();
		if(txt_sales_order_no=="" && txt_style_ref_no=="" && txt_booking_no=="" && txt_barcode_no=="" && txt_internal_ref=="")
		{
			if(form_validation('cbo_company_name*txt_sales_order_no*txt_style_ref_no*txt_booking_no*txt_barcode_no*txt_internal_ref','Company*sales_order*style_ref*booking_no*barcode_no*batch_no*Internal Ref.')==false)
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
		if(type==1)
		{
			if(txt_sales_order_no=="" && txt_style_ref_no=="" && txt_booking_no=="" && txt_barcode_no=="" && txt_internal_ref=="" )
			{
				alert("Please Select Report2");
				return;
			}
	  		var report_title=$( "div.form_caption" ).html();
	  		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_sales_order_no*txt_style_ref_no*txt_booking_no*txt_barcode_no*cbo_year*txt_internal_ref',"../../")+'&report_title='+report_title;
		}
		//alert(data);
		freeze_window(5);
		http.open("POST","requires/roll_position_tracking_report_for_sales_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			$("#report_container2").html(response[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			release_freezing();
		//	setFilterGrid("table_body",-1,tableFilters);
			var level= new Array();
			var leveld= new Array();
			var obj=JSON.parse(response[2]);
			var objd=JSON.parse(response[3]);
			for(i in obj){
				level.push(obj[i])
				leveld.push(objd[i])
			}
			
	 		show_msg('3');
			
			var barChartData3 = {
				labels : level,
				datasets : [
					{
						fillColor : "red",
						strokeColor : "rgba(255,220,220,0.8)",
						highlightFill: "rgba(255,0,0,0.5)",
						highlightStroke: "rgba(255,220,220,1)",
						data :  leveld
					}
				]
			}
		
			var ctx3 = document.getElementById("canvas3").getContext("2d");
			myBar = new Chart(ctx3).Bar(barChartData3, {
				responsive : true
			});

			setFilterGrid('table_body',-1);
			
			
		}
	}
		
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tr:first').hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		$('#table_body tr:first').show();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="330px";
	}
	
	function openmypage(po_id,color_id)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/roll_position_tracking_report_for_sales_controller.php?po_id='+po_id+'&color_id='+color_id+'&action=color_popup', 'Detail Veiw', 'width=860px, height=370px,center=1,resize=0,scrolling=0','../');
	}
	
	
	function openmypage_popup(roll_id,action)
	{
		emailwindow=dhtmlmodal.open('EmailBox','iframe','requires/roll_position_tracking_report_for_sales_controller.php?roll_id='+roll_id+'&action='+action, 'Roll Popup', 'width=1100px,height=250px,center=1,resize=0,scrolling=0','../');
		emailwindow.onclose=function()
		{
			
		}
		
	}
	
	function openmypage_sys_no(entry_form,barcode)
	{
		if(entry_form==133 || entry_form=='T133')
		{
			var width="760px";
			var height="250px";
		}
		else
		{
			var width="260px";
			var height="170px";
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/roll_position_tracking_report_for_sales_controller.php?entry_form='+entry_form+'&barcode_no='+barcode+'&action=system_no_popup', 'System Info', 'width='+width+', height='+height+',center=1,resize=0,scrolling=0','../');
	}
</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",''); ?>
		 <form name="knitDyeingLoadReport_1" id="knitDyeingLoadReport_1"> 
         <h3 style="width:1070px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
             <fieldset style="width:1060px;">
                 <table class="rpt_table" width="1060" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th class="must_entry_caption" width="150">Sales Order Company</th>
                            <th  class="must_entry_caption" width="120">Buyer Name</th>
                            <th width="60">Year</th>
                            <th  width="110">Sales Order No</th>
                            <th  width="100">Internal Ref.</th>
                            <th  width="110">Style Ref.</th>
                            <th  width="110">Booking No</th>
                            <th  width="110">Barcode No</th>
                            <th colspan="2"><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('knitDyeingLoadReport_1','report_container*report_container2','','','')" class="formbutton" style="width:60px" /></th>
                            <th></th>
                        </thead>
                        <tbody>
                            <tr class="general" align="center">
                               <td> 
									<?
                                        echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/roll_position_tracking_report_for_sales_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                                    ?>
                                </td>
                                <td id="buyer_td" align="center">
                                    <? 
                                        echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "",0,"" );
                                    ?>
                                </td>
                                <td>
				<? 
                                    $selected_year=date("Y");
                                    echo create_drop_down( "cbo_year", 60, $year,"", 1, "-All-", $selected_year, "",0 );
                                ?>
                                </td>
                                <td align="center">
                                     <input type="text" name="txt_sales_order_no" id="txt_sales_order_no" class="text_boxes" placeholder="Write" style="width:110px" />
                                </td>
								<td align="center">
                                     <input type="text" name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" placeholder="Write" style="width:100px" />
                                </td>
                                 
                                 <td align="center">
                                     <input type="text" name="txt_style_ref_no" id="txt_style_ref_no" class="text_boxes" placeholder="Write" style="width:110px" />
                                </td>
                                <td align="center">
                                     <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" placeholder="Write" style="width:110px" />
                                </td>
                                <td align="center">
                                     <input type="text" name="txt_barcode_no" id="txt_barcode_no" class="text_boxes" placeholder="Write" style="width:110px" />
                                </td>
                                <td><input type="button" id="show_button" class="formbutton" style="width:60px" value="Show" onClick="fn_report_generated(1)" /></td>
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