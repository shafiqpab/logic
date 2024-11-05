<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Machine Wash Requisition Report
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
	
	function openmypage_machine()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		 var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_location_id').value+"_"+document.getElementById('cbo_floor_id').value;
		 // alert(data);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/machine_wash_requisition_report_controller.php?action=machine_no_popup&data='+data,'Machine Name Popup', 'width=470px,height=420px,center=1,resize=0','../')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("hid_machine_id");
			var theemailv=this.contentDoc.getElementById("hid_machine_name");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById("txt_machine_id").value=theemail.value;
			    document.getElementById("txt_machine_name").value=theemailv.value;
				release_freezing();
			}
		}
	}
	
	function fn_report_generated(operation)
	{
		var req_no = document.getElementById('txt_req_no').value;
		if (req_no != '')
		{
			if (form_validation('cbo_company_id','Company Name') == false)
			{
				return;
			}
		}
		else
		{
			if (form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false)
			{
				return;
			}
		}
		var report_title=$( "div.form_caption" ).html();
		if (operation==4) 
		{
			var data = "action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_floor_id*txt_machine_name*txt_machine_id*cbo_type*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title+'&req_no='+req_no;
		}
		freeze_window(3);
		http.open("POST","requires/machine_wash_requisition_report_controller.php",true);
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
			//release_freezing();		
			setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body_short').style.overflow="auto";
		document.getElementById('scroll_body_short').style.maxHeight="none";
		$("#table_body tr:first").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		document.getElementById('scroll_body_short').style.overflowY="auto";
		document.getElementById('scroll_body_short').style.maxHeight="400px";
		$("#table_body tr:first").show();
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

	function issue_popup(company_id, mst_id, action, title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/machine_wash_requisition_report_controller.php?company_id='+company_id+'&mst_id='+mst_id+'&action='+action, title, 'width=900px,height=390px,center=1,resize=0,scrolling=0','../');
	}

	function requ_popup(company_id, mst_id, action, title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/machine_wash_requisition_report_controller.php?company_id='+company_id+'&mst_id='+mst_id+'&action='+action, title, 'width=900px,height=390px,center=1,resize=0,scrolling=0','../');
	}

</script>
</head>

<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>    		 
        <form name="machinewiseproduction_1" id="machinewiseproduction_1" autocomplete="off" > 
         <h3 style="width:1060px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:1060px">      
            <fieldset>  
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" align="center" rules="all" border="1">
                <thead>                    
                    <th width="150" class="must_entry_caption">Company Name</th>
                    <th width="130">Location</th>
                    <th width="120">Floor</th>
                    <th width="140">Requisition No</th>
                    <th width="100">Machine Name</th>
                    <th width="100">Based No</th>
                    <th width="180" class="must_entry_caption">Date Range</th>                    
                    <th style="width:70px; text-align: center;">
                    	<input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('machinewiseproduction_1','report_container*report_container2','','','')" />
                    </th>
                </thead>
                <tbody>
                    <tr>
                        <td> 
							<?
								echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/machine_wash_requisition_report_controller', this.value, 'load_drop_down_location', 'location_td' );" );
						    ?>
						</td>
						<td id="location_td">
						    <? 
						    	echo create_drop_down( "cbo_location_id", 130, $blank_array,"", 1, "-- Select Location --", 0, "",1 ); 
						    ?>
						</td>
                        <td id="floor_td">
                            <? echo create_drop_down( "cbo_floor_id", 120, $blank_array,"", 1, "-- Select Floor --", 0, "",1 ); ?>
                        </td>
                        <td align="center">
                            <input type="text" name="txt_req_no" id="txt_req_no" class="text_boxes" style="width:100px" placeholder="Write"/>
                        </td>
                        <td align="center">
                            <input type="text" name="txt_machine_name" id="txt_machine_name" class="text_boxes" style="width:100px" placeholder="Browse Machine" onDblClick="openmypage_machine()" readonly />
                            <input type="hidden" name="txt_machine_id" id="txt_machine_id" class="text_boxes" style="width:80px"  />
                        </td>
                        <td>
						    <?
                                $search_by_arr = array(1 => "Requisition Date", 2 => "Issue Date");
                                echo create_drop_down("cbo_type", 140, $search_by_arr, "", 0, "-- All --", "", 'search_by(this.value)', 0);
                            ?>
						</td>
                        <td align="center">
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" value="" placeholder="From Date" > To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" value="" placeholder="To Date"  >
                        </td>
                    <td align="center">
                        <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(4)" />
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
        <div id="report_container2" align="left" style="margin-left: 10px;"></div>
    </form> 
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
