<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Dyeing QC Report
Functionality	:	
JS Functions	:
Created by		:	Tipu 
Creation date 	: 	05-09-2018
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
echo load_html_head_contents("Machine Wash Requisition Report", "../../", 1, 1,$unicode,1,1);
?>	
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';	

	var tableFilters = 
	{
		col_30: "none",
		col_operation: {
		id: ["tot_qnty"],
		col: [6],
		operation: ["sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	var tableFilters2 = 
	{
		col_30: "none",
		col_operation: {
		id: ["tot_qnty"],
		col: [6],
		operation: ["sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	
	function fn_report_generated()
	{
		
		
		var company_id=document.getElementById('cbo_company_id').value;
		var working_company_id=document.getElementById('cbo_working_company_id').value;
		//alert(working_company_id);
		
		if (working_company_id!=0) 
		{
			if(form_validation('cbo_working_company_id','Working Company')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_id','Company')==false)
			{
				return;
			}
		}
		

		var report_title=$( "div.form_caption" ).html();		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_working_company_id*cbo_location_id*cbo_buyer_name*txt_job_no*txt_booking_no*txt_order_no*txt_batch_no*cbo_order_type*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;		
		
		freeze_window(3);
		http.open("POST","requires/dyeing_qc_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;  
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split("****");
			//alert(http.responseText);//return;
			$('#report_container2').html(response[0]);
			release_freezing();
			//alert(response[1]);			
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			release_freezing();		
			setFilterGrid("table_body",-1,tableFilters);
			setFilterGrid("table_body2",-1,tableFilters2);
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		var orderType= $('#cbo_order_type').val();
		//alert(orderType);
		if (orderType==1) 
		{
			document.getElementById('scroll_body_short').style.overflow="auto";
			document.getElementById('scroll_body_short').style.maxHeight="none";
			document.getElementById('scroll_body_short2').style.overflow="auto";
			document.getElementById('scroll_body_short2').style.maxHeight="none";
		}
		else if(orderType==2 || orderType==3 || orderType==4)
		{
			document.getElementById('scroll_body_short').style.overflow="auto";
			document.getElementById('scroll_body_short').style.maxHeight="none";
		}
		else
		{
			document.getElementById('scroll_body_short2').style.overflow="auto";
			document.getElementById('scroll_body_short2').style.maxHeight="none";
		}
		$("#table_body tr:first").hide();
		$("#table_body2 tr:first").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		if (orderType==1) 
		{
			document.getElementById('scroll_body_short').style.overflow="auto";
			document.getElementById('scroll_body_short').style.maxHeight="none";
			document.getElementById('scroll_body_short2').style.overflow="auto";
			document.getElementById('scroll_body_short2').style.maxHeight="none";
		}
		else if(orderType==2 || orderType==3 || orderType==4)
		{
			document.getElementById('scroll_body_short').style.overflow="auto";
			document.getElementById('scroll_body_short').style.maxHeight="none";
		}
		else
		{
			document.getElementById('scroll_body_short2').style.overflow="auto";
			document.getElementById('scroll_body_short2').style.maxHeight="none";
		}
		$("#table_body tr:first").show();
		$("#table_body2 tr:first").show();
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
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>    		 
        <form name="dyeingqcreport_1" id="dyeingqcreport_1" autocomplete="off" > 
         <h3 style="width:1060px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:1210px" >      
            <fieldset>  
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" align="center" rules="all" border="1">
                <thead>                    
                    <th width="150" class="must_entry_caption">Company Name</th>
                     <th width="150" class="must_entry_caption">Working Company</th>
                    <th width="130">Location</th>
                    <th width="110">Buyer</th>
                    <th width="60">Job No</th>
                    <th width="60">Booking No</th>
                    <th width="60">Order No</th>
                    <th width="60">Batch</th>
                    <th width="100">Production Type</th>
                    <th width="">Production Date</th>                    
                    <th style="width:70px; text-align: center;">
                    	<input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('dyeingqcreport_1','report_container*report_container2','','','')" />
                    </th>
                </thead>
                <tbody>
                    <tr>
                        <td> 
							<?
								echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/dyeing_qc_report_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/dyeing_qc_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
						    ?>
						</td>
                        <td> 
							<?
								echo create_drop_down( "cbo_working_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
						    ?>
						</td>
						<td id="location_td">
						    <? 
						    	echo create_drop_down( "cbo_location_id", 140, $blank_array,"", 1, "-- Select Location --", 0, "" ); 
						    ?>
						</td>
                        <td id="buyer_td">
                            <? echo create_drop_down( "cbo_buyer_name", 110, $blank_array,"", 1, "-- Select Buyer --", 0, "" ); ?>
                        </td>
                        <td align="center">
                            <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:60px" placeholder="Write" />
                        </td>
                        <td align="center">
                            <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:60px" placeholder="Write" />
                        </td>
                        <td align="center">
                            <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:60px" placeholder="Write" />
                        </td>
                        <td align="center">
                            <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:60px" placeholder="Write" />
                        </td>
                        <td>
                            <? $order_type=array(1=>"ALL",2=>"Sample With Order",3=>"Sample Without Order",4=>"Direct Order",5=>"Subcontract Order");
                            echo create_drop_down( "cbo_order_type", 100, $order_type,"", 0, "-- Select Order --", 1, "" ); ?>
                        </td>
                        <td align="center">
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" value="" placeholder="From Date" > To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" value="" placeholder="To Date"  >
                        </td>
                    <td align="center">
                        <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated()" />
                    </td>                        
                    </tr>
                </tbody>
                <tr>
                    <td colspan="10" align="center">
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
<script> //$('#cbo_location_id').val(0); </script>
</html>
