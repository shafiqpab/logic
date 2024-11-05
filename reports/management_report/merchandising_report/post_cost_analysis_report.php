<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Post Cost Analysis Report.
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	29-08-2018
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
echo load_html_head_contents("Post Cost Analysis Report","../../../", 1, 1, $unicode,1,1);
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	function fn_report_generated(report)
	{
		
		var pre_cost_version=document.getElementById('cbo_pre_cost_class').value;
		
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else
		{	
			if(report==1){
				var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*cbo_year*txt_order_no*txt_file_no*txt_ref_no*hide_order_id*txt_date_from*txt_date_to*shipping_status*cbo_order_status*cbo_pre_cost_class',"../../../");
			}
			else
			{
				var data="action=report_generate_actual_cost"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*cbo_year*txt_order_no*txt_file_no*txt_ref_no*hide_order_id*txt_date_from*txt_date_to*shipping_status*cbo_order_status*cbo_pre_cost_class',"../../../");
			}
			
			
			freeze_window(3);
			if(pre_cost_version==1)
			{
				http.open("POST","requires/post_costing_report_controller.php",true);
			}
			else
			{
				http.open("POST","requires/post_costing_report_controller2.php",true);
			}
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
			document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:135px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:120px"/>'; 
	 		show_msg('3');
			release_freezing();
		}
	}
	
	function openmypage_order()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var cbo_pre_cost_class = $("#cbo_pre_cost_class").val();
		var page_link='requires/post_costing_report_controller.php?action=order_no_search_popup&companyID='+companyID+'&cbo_pre_cost_class='+cbo_pre_cost_class;
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
		else popup_width='1060px';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/post_costing_report_controller.php?po_id='+po_id+'&action='+type, tittle, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '../../');
	}
	
	function openmypage_mkt(mkt_data,type,tittle)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/post_costing_report_controller.php?mkt_data='+mkt_data+'&action='+type, tittle, 'width=660px, height=200px, center=1, resize=0, scrolling=0', '../../');
	}
	
	function openmypage_actual(po_id,type,tittle,popup_width)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/post_costing_report_controller.php?po_id='+po_id+'&action='+type, tittle, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '../../');
	}
	
	function generate_ex_factory_popup(action,job,id,width)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/post_costing_report_controller.php?action='+action+'&job='+job+'&id='+id, 'Ex-Factory Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}	
	
	function generate_po_report(company_name,po_id,job_no,action,type)
	{
		//var report_title='PO Detail';
		popup_width='940px';
		//alert(po_id);
		//emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/post_costing_report_controller.php?po_id='+po_id+'&company_name='+company_name+'&action='+type, report_title, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '../../');
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/post_costing_report_controller.php?action='+action+'&po_id='+po_id+'&job_no='+job_no+'&company_name='+company_name, 'PO Detail', 'width='+popup_width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}
</script>

</head>
 
<body onLoad="set_hotkey();">
		 
<form id="cost_breakdown_rpt">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:1350px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:1370px;">
                <table class="rpt_table" width="1370" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                	<thead>
                    	<tr>                   
                            <th class="must_entry_caption">Company Name</th>
                            <th>Buyer Name</th>
                            <th>Job Year</th>
                    		<th>Job No</th>
                            <th>Order No</th>
                            <th>File No</th>
                     		<th>Ref. No</th>
							<th>Order Status</th>
                            <th>Shipment Status</th>
							<th>Budget Version</th>
                            <th>Shipment Date</th>
							
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/post_costing_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td>
                        	<?
								echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", 1, "",0,"" );//date("Y",time())
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
                            <?php
                            	echo create_drop_down( "cbo_order_status", 85, $row_status,"",0,"",1,"" ); 
                            ?>
                        </td>
                        <td>
                        	<?
								echo create_drop_down( "shipping_status", 120, $shipment_status,"", 0, "-- Select --", 3, "",0,'','','','','' );
							?>
                        </td>
						 <td width="" align="center">
							<?  
								$pre_cost_class_arr = array(1=>'Pre Cost 1',2=>'Pre Cost 2');
								//$dd="search_populate(this.value)";
								echo create_drop_down( "cbo_pre_cost_class", 80, $pre_cost_class_arr,"",0, "--Select--", 2,"",0 );
                            ?>
                        </td>
                        <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:53px" placeholder="From Date" >&nbsp; To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:53px"  placeholder="To Date" ></td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1)" />
                             <input type="button" id="show_button" class="formbutton" style="width:90px" value="Pre VS Actual" onClick="fn_report_generated(2)" />
                           
                            
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
