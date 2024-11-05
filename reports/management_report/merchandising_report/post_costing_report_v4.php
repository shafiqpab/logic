<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Post Costing Report.
Functionality	:	
JS Functions	:
Created by		:	Fuad 
Creation date 	: 	21-03-2015
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();



if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Post Costing Report","../../../", 1, 1, $unicode,1,1);
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	function fn_report_generated(report)
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else
		{	
			var report_title=$( "div.form_caption" ).html();
			
			if(report==1)
			{
				var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*cbo_year*txt_order_no*txt_file_no*txt_ref_no*hide_order_id*txt_date_from*txt_date_to*txt_ex_date_from*txt_ex_date_to*shipping_status*cbo_based_on',"../../../")+'&report='+report;
			}
			else if(report==3)
			{
				var data="action=report_generate3"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*cbo_year*txt_order_no*txt_file_no*txt_ref_no*hide_order_id*txt_date_from*txt_date_to*txt_ex_date_from*txt_ex_date_to*shipping_status*cbo_based_on',"../../../")+'&report='+report;
			}
			else
			{
				var data="action=report_generate2"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*cbo_year*txt_order_no*txt_file_no*txt_ref_no*hide_order_id*txt_date_from*txt_date_to*txt_ex_date_from*txt_ex_date_to*shipping_status*cbo_based_on',"../../../")+'&report='+report;
			}
			
		
			
			freeze_window(3);
			http.open("POST","requires/post_costing_report_v4_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("****");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:135px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window3()" value="Html Preview" name="Print" class="formbutton" style="width:120px"/>'; 
	 		show_msg('3');
			release_freezing();
			if(response[2]==2)
			{
			setFilterGrid("table_body",-1);
			}
			//table_body
		}
	}
	function new_window3()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		$("#table_body tr:first").hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflow="scroll";
		document.getElementById('scroll_body').style.maxHeight="350px";

		$("#table_body tr:first").show();
	}
	
	function openmypage_order()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var page_link='requires/post_costing_report_v4_controller.php?action=order_no_search_popup&companyID='+companyID;
		var title='Order No Search';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_no=this.contentDoc.getElementById("hide_order_no").value;
			var order_id=this.contentDoc.getElementById("hide_order_id").value;
			
			$('#txt_order_no').val(order_no);
			$('#hide_order_id').val(order_id);	 
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#div_buyer').hide();
		$('#div_summary').hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#div_buyer').show();
		$('#div_summary').show();
	}
	
	function new_window2(comp_div, container_div)
	{
		document.getElementById(comp_div).style.visibility="visible";
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById(container_div).innerHTML+'</body</html>');
		document.getElementById(comp_div).style.visibility="hidden";
		d.close();
	}
	
	function openmypage(po_id,type,tittle)
	{
		var popup_width='';
		if(type=="dye_fin_cost") 
		{
			popup_width='1140px';
		}
		else if(type=="fabric_purchase_cost") 
		{
			popup_width='740px';
		}
		else if(type=="fabric_purchase_cost3") 
		{
			popup_width='780px';
		}
		else popup_width='1060px';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/post_costing_report_v4_controller.php?po_id='+po_id+'&action='+type, tittle, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '../../');
	}
	
	function openmypage_mkt(mkt_data,type,tittle)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/post_costing_report_v4_controller.php?mkt_data='+mkt_data+'&action='+type, tittle, 'width=660px, height=200px, center=1, resize=0, scrolling=0', '../../');
	}
	
	function openmypage_actual(po_id,type,tittle,popup_width)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/post_costing_report_v4_controller.php?po_id='+po_id+'&action='+type, tittle, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '../../');
	}
	function openmypage_actual2(po_id,type,tittle,popup_type,popup_width)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/post_costing_report_v4_controller.php?po_id='+po_id+'&action='+type+'&popup_type='+popup_type, tittle, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '../../');
	}
	function openmypage_gmt_actual(po_id,embl_name,type,tittle,popup_width)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/post_costing_report_v4_controller.php?po_id='+po_id+'&action='+type+'&embl_name='+embl_name, tittle, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '../../');
	}
	
	function generate_ex_factory_popup(action,job,id,width)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/post_costing_report_v4_controller.php?action='+action+'&job='+job+'&id='+id, 'Ex-Factory Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}	
	
	function generate_po_report(company_name,po_id,job_no,action,type)
	{
		//var report_title='PO Detail';
		popup_width='940px';
		//alert(po_id);
		//emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/post_costing_report_v4_controller.php?po_id='+po_id+'&company_name='+company_name+'&action='+type, report_title, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '../../');
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/post_costing_report_v4_controller.php?action='+action+'&po_id='+po_id+'&job_no='+job_no+'&company_name='+company_name, 'PO Detail', 'width='+popup_width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}



// 	function generate_report(type)
// 	{
// 		if (form_validation('txt_job_no*txt_order_no','Please Select The Job Number*Order No')==false)
// 		{
// 			return;
// 		}
// 		else
// 		{

// 			var rate_amt=2; var zero_val='';
// 				if(type!='mo_sheet' && type != 'budgetsheet')
// 				{
// 					var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
// 				}
// 				var excess_per_val="";
// 				if(type=='mo_sheet')
// 				{
// 					excess_per_val = prompt("Please enter your Excess %", "0");
// 					if(excess_per_val==null) excess_per_val=0;else excess_per_val=excess_per_val;
// 				}
// 				if(type == 'budgetsheet')
// 				{
// 					var r=confirm("Press  \"OK\" to Show Budget, \nPress  \"Cancel\"  to Show Management Budget");
// 				}

// 				if (r==true) zero_val="1"; else zero_val="0";
		
// 				var data="action="+type+"&zero_value="+zero_val+"&rate_amt="+rate_amt+"&excess_per_val="+excess_per_val+"&"+get_submitted_data_string('txt_job_no*cbo_company_name*cbo_buyer_name*txt_order_no',"../../../");
// 				http.open("POST","requires/post_costing_report_v4_controller.php",true);
// 				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
// 				http.send(data);
// 				http.onreadystatechange = fnc_generate_report_reponse;
// 		}
// 	}

// 	function fnc_generate_report_reponse()
// 	{
// 		if(http.readyState == 4)
// 		{
// 			$('#data_panel').html( http.responseText );
// 			var w = window.open("Surprise", "_blank");
// 			var d = w.document.open();
// 				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
// '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
// 			d.close();
// 			show_msg('3');
// 			release_freezing();
// 		}
// 	}


</script>



</head>
 
<body onLoad="set_hotkey();">
		 
<form id="cost_breakdown_rpt">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:1400px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:1400px;">
                <table class="rpt_table" width="1400" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                	<thead>
                    	<tr>                   
                            <th class="must_entry_caption">Company Name</th>
                            <th>Buyer Name</th>
                            <th>Job Year</th>
                    		<th>Job No</th>
                            <th>Order No</th>
                            <th>File No</th>
                     		<th>Ref. No</th>
                            <th>Shipment Status</th>
                            <th>Based On</th>
                            <th>Shipment Date</th>
							<th>Ex-factory Date</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:50px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/post_costing_report_v4_controller',this.value, 'load_drop_down_buyer', 'buyer_td' ); get_php_form_data(this.value,'print_button_variable_setting','requires/post_costing_report_v4_controller' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td>
                        	<?
								echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );//date("Y",time()) 
							?>
                        </td>
                        <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px" placeholder="Write" /></td>
                        <td>
                            <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:120px" placeholder="Browse Or Write" onDblClick="openmypage_order();" onChange="$('#hide_order_id').val('');" autocomplete="off">
                            <input type="hidden" name="hide_order_id" id="hide_order_id" readonly>
                        </td>
                        <td>
                           <input type="text" name="txt_file_no" id="txt_file_no" class="text_boxes_numeric" style="width:60px"  placeholder="Write" >
                          </td>
                          <td>
                           <input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:70px"  placeholder="Write" >
                        </td>
                        <td>
                        	<?
								echo create_drop_down( "shipping_status", 120, $shipment_status,"", 1, "-- All --", 3, "",0,'','','','','' );
							?>
                        </td>
                        <td>
                        	<?
								$based_on_arr=array(1=>"Machine Minute");
								echo create_drop_down( "cbo_based_on", 97, $based_on_arr,"",0, "", "",'',0 );
							?>
                        </td> 
                        <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" >&nbsp; To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date" ></td>
						<td><input type="text" name="txt_ex_date_from" id="txt_ex_date_from" class="datepicker" style="width:55px" placeholder="From Date" >&nbsp; To
                        <input type="text" name="txt_ex_date_to" id="txt_ex_date_to" class="datepicker" style="width:55px"  placeholder="To Date" ></td>
                        <td>
							<span id="button_data_panel"></span>
                             
							
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
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
