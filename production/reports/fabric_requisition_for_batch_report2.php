<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Fabric Requisition For Batch 2 Report.
Functionality	:	
JS Functions	:
Created by		:	 
Creation date 	: 	
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
echo load_html_head_contents("Fabric Requisition For Batch 2 Report", "../../", 1, 1,$unicode,1,1);

?>	

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';	
			
	var tableFilters = {
							col_operation: {
								id: ["value_req_qnty"],
							   col: [14],
							   operation: ["sum"],
							   write_method: ["innerHTML"]
							}
						}
	function fn_report_generated(operation)
	{
		
		var search_string = $("#txt_search_string").val();
		var requ_no = $("#txt_requisition_no").val();
		if(search_string!="" || requ_no !=""){
			var validation_id = "";
			var validation_msg = "";
		}else{
			var validation_id = "*txt_date_from*txt_date_to";
			var validation_msg = "*From Date*To Date";
		}


		if( form_validation("cbo_company_id"+validation_id,"Company Name"+validation_msg)==false )
		{
			return;
		}
		else
		{ 
			var from_date = $('#txt_date_from').val();
			var to_date = $('#txt_date_to').val();
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_buyer_name*cbo_search_by*txt_search_string*txt_requisition_no*txt_date_from*txt_date_to*cbo_year_selection' , "../../")+'&report_title='+report_title;
			freeze_window(3);
			http.open("POST","requires/fabric_requisition_for_batch_report2_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
		
	
	
	function fn_report_generated_reponse()
	{	
		if(http.readyState == 4) 
		{
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
		$(".flt").css("display","none");
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		$(".flt").css("display","block");
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="300px";
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
	function fn_chang_caption()
	{
		$("#txt_search_string").val("");
		var search_id = $("#cbo_search_by").val();
		if(search_id==1) $("#search_by_caption").html("Booking No");
		else if(search_id==2) $("#search_by_caption").html("Sales Order No");		
	}
		
</script>
</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
        <form name="unitwiseproduction_1" id="unitwiseproduction_1" autocomplete="off" > 
         <h3 style="width:1090px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:1090px" align="center" >      
            <fieldset>  
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" align="center">
                <thead>                    
                    <th width="150" class="must_entry_caption">Company Name</th>
                    <th width="130">Location</th>
                    <th width="130">Buyer</th>
                    <th width="120">Search Type</th>
                    <th width="120" id="search_by_caption">Booking No</th>
                    <th width="100" >Requisition No</th>
                    <th width="190" class="must_entry_caption">Requisition Date</th>
                    <th width="100"><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('unitwiseproduction_1','report_container*report_container2','','','')" /></th>
                </thead>
                <tbody>
                    <tr>
                        <td> 
							<?
								echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down('requires/fabric_requisition_for_batch_report2_controller', this.value, 'load_drop_down_location', 'location_td');load_drop_down('requires/fabric_requisition_for_batch_report2_controller', this.value, 'load_drop_down_buyer', 'buyer_td');" );
                            ?>
                        </td>

                        <td id="location_td">
							<? 
								echo create_drop_down( "cbo_location_id", 130, $blank_array,"", 1, "-- Select --", $selected, "","","" );
                            ?>
                        </td>
                        <td id="buyer_td">
							<? 
								echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- Select Buyer--", $selected, "","","" );
                            ?>
                        </td>
                        <td>
                        <?
							$search_by_arr=array(1=>"Booking No",2=>"Sales Order No");
                        	echo create_drop_down( "cbo_search_by", 120, $search_by_arr,"", 0,"", 1, "fn_chang_caption()",0,"" );
                        ?>
                        </td>
                        <td>
                        	<input type="text" id="txt_search_string" name="txt_search_string" class="text_boxes" style="width:100px" placeholder="Write" />
                        </td>
                         <td>
                        	<input type="text" id="txt_requisition_no" name="txt_requisition_no" class="text_boxes" style="width:100px" placeholder="Write" />
                        </td>
                        <td align="center">
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" > To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date"  >
                        </td>
                        <td align="center">
                            <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(3)" />
                        </td>
                    </tr>
                </tbody>
                <tr>
                    <td colspan="8" align="center">
                        <? echo load_month_buttons(1); ?>
                    </td>
                </tr>
            </table> 
        </fieldset>
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2" align="left"></div>
    </form> 
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
