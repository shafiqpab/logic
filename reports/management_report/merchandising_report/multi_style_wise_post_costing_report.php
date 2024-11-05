<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Multi Style wise Post Costing Report.
Functionality	:	
JS Functions	:
Created by		:	Zakaria joy 
Creation date 	: 	26-10-2023
Updated by 		: 		
Update date		: 		   
QC Performed BY	:	Abidul Islam	
QC Date			:	13-11-2023
Comments		:   Temp tbl id:144
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Post Costing Report V3","../../../", 1, 1, $unicode,1,1);
?>	

<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	function fn_report_generated(action)
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else
		{	
			var report=1;
			if(action=='report_generate2' || action=='report_generate5'){
				report=2;
			}
			var report_title=$( "div.form_caption" ).html();			
			var data="action="+action+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*cbo_year*txt_order_no*txt_file_no*txt_ref_no*hide_order_id*txt_date_from*txt_date_to*txt_ex_date_from*txt_ex_date_to*shipping_status*cbo_based_on',"../../../")+'&report='+report;
			freeze_window(3);
			http.open("POST","requires/multi_style_wise_post_costing_report_controller.php",true);
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
		var page_link='requires/multi_style_wise_post_costing_report_controller.php?action=order_no_search_popup&companyID='+companyID;
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
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/multi_style_wise_post_costing_report_controller.php?po_id='+po_id+'&action='+type, tittle, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '../../');
	}
	
	function openmypage_mkt(mkt_data,type,tittle)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/multi_style_wise_post_costing_report_controller.php?mkt_data='+mkt_data+'&action='+type, tittle, 'width=660px, height=200px, center=1, resize=0, scrolling=0', '../../');
	}
	
	function openmypage_actual(job_id,type,tittle,popup_width)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/multi_style_wise_post_costing_report_controller.php?job_id='+job_id+'&action='+type, tittle, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '../../');
	}
	function openmypage_actual2(job_id,type,tittle,popup_type,popup_width)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/multi_style_wise_post_costing_report_controller.php?job_id='+job_id+'&action='+type+'&popup_type='+popup_type, tittle, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '../../');
	}
	function openmypage_actual3(job_id,type,tittle,popup_width)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/multi_style_wise_post_costing_report_controller.php?job_id='+job_id+'&action='+type, tittle, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '../../');
	}
	function openmypage_gmt_actual(job_id,embl_name,type,tittle,popup_width)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/multi_style_wise_post_costing_report_controller.php?job_id='+job_id+'&action='+type+'&embl_name='+embl_name, tittle, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '../../');
	}
	
	function generate_ex_factory_popup(action,job,id,width)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/multi_style_wise_post_costing_report_controller.php?action='+action+'&job='+job+'&id='+id, 'Ex-Factory Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}	
	
	function generate_po_report(company_name,po_id,job_no,action,type)
	{
		popup_width='940px';		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/multi_style_wise_post_costing_report_controller.php?action='+action+'&po_id='+po_id+'&job_no='+job_no+'&company_name='+company_name, 'PO Detail', 'width='+popup_width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}

	function generate_report(type)
	{
		var txt_job_no=$('#txt_job_no').val();
		var cbo_buyer_name=$('#cbo_buyer_name').val();
		var cbo_company_name=$('#cbo_company_name').val();
		var txt_order_no=$('#txt_order_no').val();
		var cbo_year=$('#cbo_year').val();
		var hide_order_id=$('#hide_order_id').val();

			var path = '../../';
            print_report(cbo_company_name+'**'+cbo_buyer_name+'**'+txt_job_no+'**'+txt_order_no+'**'+cbo_year+'**'+hide_order_id+'**'+ path , "bomRpt2", "requires/multi_style_wise_post_costing_report_controller");
	}
</script>



</head>
 
<body onLoad="set_hotkey();">
		 
<form id="cost_breakdown_rpt">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:1420px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:1450px;">
                <table class="rpt_table" width="1430" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
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
							<th>Ex-Factory Date</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:50px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/multi_style_wise_post_costing_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
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
								echo create_drop_down( "shipping_status", 120, $shipment_status,"", 1, "-- All --", "3", "",1,'','','','','' );
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
                            <input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated('report_generate')" />
                        </td>
					
                    </tr>
					<tr class="">
							<td class="" colspan="11" align="center">
								
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
