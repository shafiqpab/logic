<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Partial Fabric Analysis Report.
Functionality	:	
JS Functions	:
Created by		:	Aziz
Creation date 	: 	27-12-2017
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
echo load_html_head_contents("Partial Fabric Booking Analysis Report", "../../", 1, 1,$unicode,'1','');
?>	

<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	function fn_report_generated()
	{
		var cbo_search_type=document.getElementById('cbo_search_type').value;	
		var search_common=document.getElementById('txt_search_common').value;	
		if(search_common!="")
		{
			if(form_validation('cbo_company_name','Company')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*From date Fill*To date Fill')==false)
			{
				return;
			}
		}
			
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_search_type*txt_search_common*txt_job_po_id*txt_date_from*txt_date_to*cbo_year_selection*cbo_booking_type',"../../")+'&report_title='+report_title;
			//alert(data);
			freeze_window(3);
			http.open("POST","requires/partial_fabric_booking_analysis_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		
	}
		
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			
			var reponse=trim(http.responseText).split("****");
			var tot_rows=reponse[2];
			var search_by=reponse[3];
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+tot_rows+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			
			 var tableFilters = 
				 {
					  
					col_operation: {
					   id: ["value_total_fab_req_qty","value_total_adjust_qty","value_total_adjust_amount","value_total_grey_fin_qnty","value_total_booking_amount"],
					   col: [14,16,17,18,19],
					   operation: ["sum","sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					},
						
				 }
			setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}

	function fn_report_generated2()
	{
		var cbo_search_type=document.getElementById('cbo_search_type').value;	
		var search_common=document.getElementById('txt_search_common').value;	
		if(search_common!="")
		{
			if(form_validation('cbo_company_name','Company')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*From date Fill*To date Fill')==false)
			{
				return;
			}
		}
			
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate2"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_search_type*txt_search_common*txt_job_po_id*txt_date_from*txt_date_to*cbo_year_selection*cbo_booking_type*cbo_fabric_source',"../../")+'&report_title='+report_title;
			//alert(data);
			freeze_window(3);
			http.open("POST","requires/partial_fabric_booking_analysis_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated2_reponse;
		
	}
		
	
	function fn_report_generated2_reponse()
	{
		if(http.readyState == 4) 
		{
			
			var reponse=trim(http.responseText).split("****");
			var tot_rows=reponse[2];
			var search_by=reponse[3];
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+tot_rows+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			
			 var tableFilters_2 = 
				 {					  
					col_operation: {
					id: ["total_fin_fab_qty","total_grey_fab_qty"],
					col: [21,23],
					operation: ["sum","sum"],
					write_method: ["innerHTML","innerHTML"]
					},		
				 }
			setFilterGrid("table_body_2",-1,tableFilters_2);
			show_msg('3');
			release_freezing();
		}
	}
	function openmypage_image(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{

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
	

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$("#table_body tr:first").hide();
		$("#table_body_2 tr:first").hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		
		document.getElementById('scroll_body').style.overflow="scroll"; 
		document.getElementById('scroll_body').style.maxHeight="350px";
		
		$("#table_body tr:first").show();
		$("#table_body_2 tr:first").show();
	}	



function change_search_type(str)
	{
		$('#txt_search_common').val('');
		$('#txt_job_po_id').val('');
		if(str==1)
		{
			document.getElementById('search_by_td_up').innerHTML="Style Ref.";
			//$('#search_by_th_up').css('color','blue');
			$('#txt_search_common').attr('placeholder','Wirte/Browse');
			$('#txt_search_common').attr('onDblClick','openmypage_job()');
		}
		else if(str==2)
		{
			document.getElementById('search_by_td_up').innerHTML="Job No";
			$('#txt_search_common').attr('placeholder','Wirte/Browse');
			$('#txt_search_common').attr('onDblClick','openmypage_job()');
		}
		else if(str==3)
		{
			document.getElementById('search_by_td_up').innerHTML="PO NO";
			$('#txt_search_common').attr('placeholder','Wirte/Browse');
			$('#txt_search_common').attr('onDblClick','openmypage_job()');
		}
		else if(str==4)
		{
			document.getElementById('search_by_td_up').innerHTML="File No";
		}
		else if(str==5)
		{
			document.getElementById('search_by_td_up').innerHTML="Internal Ref.";
		}
		else if(str==6)
		{
			document.getElementById('search_by_td_up').innerHTML="Booking No";
		//	$('#txt_search_common').removeAttr('onDblClick','onDblClick');
			$('#txt_search_common').attr('onDblClick','openmypage_job()');
			$('#txt_search_common').attr('placeholder','Wirte/Browse');
		}
	}
	
	function generate_worder_report3(txt_booking_no,cbo_company_name,txt_order_no_id,cbo_fabric_natu,cbo_fabric_source,txt_job_no,id_approved_id,print_id,type,i)
	{
		//var report_title='Budget Wise Fabric Booking';
		var show_yarn_rate='';
			if(type!='print_booking_5')
			{
				
				var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
				if (r==true){
					show_yarn_rate="1";
				}
				else{
					show_yarn_rate="0";
				} 
			}
			
		var data="action="+type+
		'&txt_booking_no='+"'"+txt_booking_no+"'"+
		'&cbo_company_name='+"'"+cbo_company_name+"'"+
		'&txt_order_no_id='+"'"+txt_order_no_id+"'"+
		'&cbo_fabric_natu='+"'"+cbo_fabric_natu+"'"+
		'&cbo_fabric_source='+"'"+cbo_fabric_source+"'"+
		'&id_approved_id='+"'"+id_approved_id+"'"+
		'&report_title='+"Partial Fabric Booking"+
		'&txt_job_no='+"'"+txt_job_no+"'"+
		'&show_yarn_rate='+"'"+show_yarn_rate+"'"+
		'&path=../';
		
		freeze_window(5);
		if(print_id==143 || print_id==84 || print_id==85 || print_id==151 || print_id==160 || print_id==175 || print_id==218 || print_id==220 || print_id==235 || print_id==274 || print_id==241 || print_id==269 || print_id==28 || print_id==280 || print_id==304 || print_id==719 || print_id==155 || print_id==723 )
		{
			if(print_id==155)
			{
			  http.open("POST","../../order/woven_gmts/requires/partial_fabric_booking_controller.php",true);
			}
			else
			{
			  http.open("POST","../../order/woven_order/requires/partial_fabric_booking_controller.php",true);

			}
		}
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			if(http.readyState == 4) 
		    {
				var w = window.open("Surprise", "_blank");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+http.responseText+'</body</html>');
				d.close();
				release_freezing();
		   }
			
		}
	}
	  function fabric_sales_order_print3(company,booking_id,booking_no,job_no,title,within_group,row_id,action,i) {
    	var data = company + '*' + booking_id + '*' + booking_no + '*' + job_no + '*' + title;
		if (within_group == 1) {
    			window.open("../../production/requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print', true);
    		} else {
    			window.open("../../production/requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print2', true);
    		}
    	/*window.open("../../production/requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print3', true);*/
    	return;
    }

	function yarn_requ_print_button(company,update_id,title,action,i) {
    	var data = company + '*' + update_id +'*' + title;
		window.open("../../commercial/work_order/requires/yarn_requisition_entry_controller.php?data=" + data + '&action=yarn_requisition_print_5', true);
    	return;
    }

	function show_popup(action, job_number, width,type)
    {
        var title = '';
        if(type==1)
        {
            title = "Sample Approval Details";
        }
        else if(type==2)
        {
            title = "Order Status Details";
        }
        else if(type==3 || type==4)
        {
            title = "Main Fabric Booking Approval Status";
        }
        else if(type==5)
        {
            title = "Work Order Details";
        }
        else if(type==6)
        {
            title = "Trims/Acc Status Details";
        }
        else
        {
            title = "Closing Status Details";
        }
        
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/partial_fabric_booking_analysis_report_controller.php?action=' + action + '&job_number=' + job_number, title, 'width=' + width + ',height=400px,center=1,resize=0,scrolling=0', '../');
    }

	function generate_related_booking(booking_no,company_name,job_no,item_category,related_booking,action)
	{  
		
		var popup_width='580px';
		var title_var='Related Booking No';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/partial_fabric_booking_analysis_report_controller.php?company_name='+company_name+'&job_no='+job_no+'&related_booking='+related_booking+'&booking_no='+booking_no+'&action='+action, title_var, 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../');
	}
	function openmypage_job()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
	    // var cbo_year_id = $("#cbo_year").val();
		var cbo_search_type=document.getElementById('cbo_search_type').value;	
		var search_common=document.getElementById('txt_search_common').value;
		
		// var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/partial_fabric_booking_analysis_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_search_type='+cbo_search_type+'&search_common='+search_common;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=830px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			//var job_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_search_common').val(job_no);
			//$('#txt_job_po_id').val(job_id);	 
		}
	}
	function fn_on_change()
	{
		var cbo_company_name = $("#cbo_company_name").val();
		load_drop_down( 'requires/partial_fabric_booking_analysis_report_controller', cbo_company_name, 'load_drop_down_buyer', 'buyer_td' );
		//set_multiselect('cbo_buyer_name','0','0','','0','');
	}
</script>

</head>

<body onLoad="set_hotkey();">

<form id="accessoriesFollowup_report">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",''); ?>
        <h3 align="left" id="accordion_h1" style="width:1100px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
            <fieldset style="width:1100px;">
                <table class="rpt_table" width="1100" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">
                	<thead>
                   		<tr>                    
                            <th class="must_entry_caption">Company Name</th>
                            <th>Buyer Name</th>
                            <th>Search Type</th>
							<th>Booking Type</th> 
							<th>Source</th> 
                            <th align="center" id="search_by_td_up">Job No.</th>
                            <th align="center" class="must_entry_caption">Booking Date</th>
                            <th align="center"><input type="reset" id="reset_btn" class="formbutton" style="width:60px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td>
                            <?
							echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/partial_fabric_booking_analysis_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <? 
                            echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td>
                            <?
							$search_by = array(1 => 'Style', 2 => 'Job No', 3 => "PO No", 4 => "File No", 5 => 'Internal Ref', 6 => 'Booking No');
							$dd = "change_search_type(this.value)";
							echo create_drop_down("cbo_search_type", 130, $search_by, "", 0, "--Select--", "", $dd, 0);
                            ?>
                        </td>
						<td>
                            <?
							$bookingTypeArr = array(2 => 'Main', 1 => 'Short');
							//$dd = "change_search_type(this.value)";
							echo create_drop_down("cbo_booking_type", 100, $bookingTypeArr, "", 1, "--Select--", "","", 0);
                            ?>
                        </td>
						<td><? echo create_drop_down( "cbo_fabric_source", 100, $fabric_source,"",1,"--Select Source--", $selected, ""); ?></td>
 
                        <td id="search_by_td">
                           <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" onDblClick="openmypage_job()" placeholder="Write/Browse"/>
                            <input type="hidden" id="txt_job_po_id" name="txt_job_po_id"/>
                        </td>
                    			
                  <td>
                  <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" value="<? //echo date('d-m-Y')?>" >&nbsp; To
                   <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" value="<? //echo date('d-m-Y')?>"></td>
                    </td> 
                       
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:60px" value="Show" onClick="fn_report_generated()" />
							<input type="button" id="show_button" class="formbutton" style="width:60px" value="Show 2" onClick="fn_report_generated2()" />
							<td>
                           
                        </td>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <table>
                    <tr>
                        <td>
                            <? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </table> 
            </fieldset>
        </div>
    </div>
    
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>    
</body>
<script>
	//set_multiselect('cbo_item_group','0','0','0','0');
	set_multiselect('cbo_company_name','0','0','','0','fn_on_change();');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript"> 
	set_multiselect('cbo_fabric_source', '0', '0', '', '0');
</script>
</html>


