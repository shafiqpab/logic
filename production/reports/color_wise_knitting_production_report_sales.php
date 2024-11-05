<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Report Will Create Color Wise knitting production.
Functionality	:	
JS Functions	:
Created by		:
Creation date 	: 	03-08-2022
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
echo load_html_head_contents("Color Wise Knitting Production Report", "../../", 1, 1,'','','');
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	var tableFilters = 
	 {
		col_47: "none",
		col_operation: {
			id: ["value_total_finish_qty","value_total_grey_qty","value_total_requ_qty_out","value_total_program_in","value_total_program_out","value_total_color_program_qnty","value_total_color_program_balance","value_total_knit_in","value_total_knit_out","value_total_color_production_qty","value_total_production_balance_in","value_total_production_balance_out","value_total_color_production_balance","value_total_delivery_qnty","value_total_delivery_balance","value_total_rcv_qnty","value_total_receive_balance"],
	   col: [15,16,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33],
	   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}	
	}
	function fn_report_generated(type)
	{
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_within_group*fso_number_show*fso_number*txt_date_from*txt_date_to*cbo_floor_id',"../../")+'&report_title='+report_title;
		freeze_window(type);
		http.open("POST","requires/color_wise_knitting_production_report_sales_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../'); 

			$("#report_container2").html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			setFilterGrid("table_body",-1,tableFilters);
			release_freezing();
		}
	}

	function new_window()
	{
		if(document.getElementById('table_body'))
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$('#scroll_body tr:first').hide();
		}

		var w = window.open("Surprise", "#");

		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		if(document.getElementById('table_body'))
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="397px";
	        $('#scroll_body tr:first').show();
	    }
	}
	
	
	function openmypage_knitting(data,type,action)
	{ 
		var companyID = $("#cbo_company_name").val();
		var txt_date_from = $("#txt_date_from").val();
		var txt_date_to = $("#txt_date_to").val();
		if(action == "requ_popup"){
			var popup_width='660px';
		}
		else if(action == "program_popup")
		{
			var popup_width='1020px';
		}
		else if(action == "knitting_popup" || action == "delivery_popup")
		{
			var popup_width='850px';
		}
		else if(action == "trans_in_popup" || action == "trans_out_popup")
		{
			var popup_width='1150px';
		}
		else if(action == "alloc_popup"){
			var popup_width='360px';
		}
		

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/color_wise_knitting_production_report_sales_controller.php?companyID='+companyID+'&data='+data+'&type='+type+'&txt_date_from='+txt_date_from+'&txt_date_to='+txt_date_to+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../');
	}

	function fsoNumber()
	{
 		var cbo_company_id = $('#cbo_company_name').val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_within_group = $("#cbo_within_group").val();
	 	if (form_validation('cbo_company_name', 'Company') == false) { return; }
	 	else
	 	{
 			var title = 'FSO Selection Form';
 			var page_link = 'requires/color_wise_knitting_production_report_sales_controller.php?cbo_company_id=' + cbo_company_id +'&buyer_name='+buyer_name +'&cbo_within_group='+ cbo_within_group+ '&action=FSO_No_popup';

 			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=1,scrolling=0', '../');

 			emailwindow.onclose = function ()
 			{
	 			var theform=this.contentDoc.forms[0];
				var fso_no=this.contentDoc.getElementById("hide_fso_no").value;
				var fso_id=this.contentDoc.getElementById("hide_fso_id").value;
				$('#fso_number_show').val(fso_no);
				$('#fso_number').val(fso_id);
 			}
 		}
 	}
	
</script>
</head>
<body onLoad="set_hotkey();">
<form id="knittingStatusReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1000px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1000px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Buyer Name</th>
                    <th>Within Group</th>
                    <th>Sales Order</th>
					<th>Knitting Production Floor</th>
                    <th>Production Date</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('knittingStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px" /></th>
                </thead>
                <tbody>
                    <tr align="center" class="general">
                        <td> 
                        <?
                        	echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "- Select Company -", $selected, "load_drop_down( 'requires/color_wise_knitting_production_report_sales_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/color_wise_knitting_production_report_sales_controller', this.value, 'load_drop_down_floor', 'floor_td' );" );
                        ?>
                        </td>
                        <td id="buyer_td">
                        <? 
                        	echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "- All Buyer -", $selected, "",0,"" );
                        ?>
                        </td>
						<td>
                        	<?
							echo create_drop_down( "cbo_within_group", 100, $yes_no,"", 1, "Select", 0, "" );
                        	?>
                        </td>
						<td>
                            <input type="text" id="fso_number_show" name="fso_number_show" class="text_boxes" style="width:150px;"  placeholder="Brows"  onDblClick="fsoNumber()" readonly/>
                            <input type="hidden" name="fso_number" id="fso_number" readonly>
                        </td>
                        <td id="floor_td">
							<?
								echo create_drop_down("cbo_floor_id", 120, $blank_array, "", 1, "-- Select Floor --", 0, "", 1);
							?>
                        </td>
                        <td align="center">
                        <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:70px" placeholder="From Date" readonly />
                        To
                        <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:70px" placeholder="To Date" readonly />
                        </td>
                        <td>
                        <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(3)" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    	</div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form>   
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
