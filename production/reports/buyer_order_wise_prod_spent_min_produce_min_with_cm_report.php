<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create  Order Wisr Production Spent Min and Produce Min With CM Report.
Functionality	:	
JS Functions	:
Created by		:	Aziz 
Creation date 	: 	27-03-2018
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

echo load_html_head_contents("Production Status Summary Report","../../", 1, 1, $unicode,1,1);
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
			
	function fn_report_generated(type)
	{
		//var job_no=document.getElementById('txt_job_no').value;	
		//var order_no=document.getElementById('txt_order_no').value;
	
		var style_no=document.getElementById('txt_style_no').value;
	//alert(style_no);
		
		if (type==1)
		{
			if(style_no!="")
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
		}
		else
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*From date Fill*To date Fill')==false)
			{
				return;
			}
		}
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate&reporttype="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to*cbo_year*txt_style_no*txt_style_id*cbo_product_cat*cbo_location_id',"../../")+'&report_title='+report_title;
		//alert(data);return;
		freeze_window(3);
		if(type==1 || type==2)
		{
			http.open("POST","requires/buyer_order_wise_prod_spent_min_produce_min_with_cm_report_controller.php",true);
		}
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			//alert(reponse[0]);
			//var tot_rows=reponse[0];
			$('#report_container2').html(reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../');
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
				var tableFilters = {
			  //col_10:'none',
			 // display_all_text: " ---Show All---",
					col_operation: {
					id: ["value_total_sew_out","value_total_spent_min","value_total_produced_min","value_total_cm_cost_earning","value_total_fob_earning","value_total_cm_cost","value_total_profit_loss","value_total_po_qty_pcs","value_total_po_value","value_total_shipment_value"],
					col: [7,8,9,12,13,14,15,16,17,18],
					operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
					write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				}
				setFilterGrid("table_body",-1,tableFilters);
			
			
			//append_report_checkbox('table_header_1',1);
			//setFilterGrid("table_body",-1);
			//alert(document.getElementById('graph_data').value);
			//show_graph( '', document.getElementById('graph_data').value, "pie", "chartdiv", "", "../../../", '',580,700 );
			release_freezing();
			show_msg('3');
		}
	}
	
	
	
	function fn_report_generated2(type)
	{
		
		if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*From date Fill*To date Fill')==false)
		{
			return;
		}
		
		var fDate = $('#txt_date_from').val().split('-');
		var makeFDate = fDate[2]+"/"+fDate[1]+"/"+fDate[0];
		var date_from = new Date(makeFDate);
		
		var lDate = $('#txt_date_to').val().split('-');
		var makeLDate = lDate[2]+"/"+lDate[1]+"/"+lDate[0];
		var date_to = new Date(makeLDate);
		
		var fromDate = date_from.getMonth();
		var toDate = date_to.getMonth();
		if(fromDate != toDate){
			alert("Cross month is not allowed"); return;
		}
		
		
		
		
		
		var style_no=document.getElementById('txt_style_no').value;
		
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate2&reporttype="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to*cbo_year*txt_style_no*txt_style_id*cbo_product_cat*cbo_location_id',"../../")+'&report_title='+report_title;
		
		freeze_window(3);
		if(type==3 )
		{
			http.open("POST","requires/buyer_order_wise_prod_spent_min_produce_min_with_cm_report_controller.php",true);
		}
		//alert(data);return;
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse2;
	}
	
	function fn_report_generated_reponse2()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			$('#report_container2').html(reponse[0]);
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
				var tableFilters = {
			 // display_all_text: " ---Show All---",
					col_operation: {
					id: ["value_total_prod_sew_out_qty","value_total_avaiable_spent_min","value_total_produced_min","value_total_cm_cost_earning","value_total_fob_earning"],
					col: [4,5,6,7,9],
					operation: ["sum","sum","sum","sum","sum"],
					write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				}
				setFilterGrid("table_body",-1,tableFilters);
				release_freezing();
				show_msg('3');
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$("#img_id").css("display","block");
		 
		$("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflow="scroll"; 
		document.getElementById('scroll_body').style.maxHeight="350px";
		$("#img_id").css("display","none");
		
		$("#table_body tr:first").show();
	}
	
	function openmypage_job(search_type)
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_year_id = $("#cbo_year").val();
	//	var po_no = $("#txt_order_no").val();
		//var job_no = $("#txt_job_no").val();
		//var cbo_month_id = $("#cbo_month").val(); txt_style_no//txt_order_no
		var page_link='requires/buyer_order_wise_prod_spent_min_produce_min_with_cm_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id+'&search_type='+search_type;
		var title='Style No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=730px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			//alert(search_type);
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			if(search_type==1)
			{
				$('#txt_job_no').val(job_no);
				$('#txt_job_id').val(job_id);	
			}
			else if(search_type==2)
			{
				$('#txt_order_no').val(job_no);
				$('#txt_order_id').val(job_id);	
			}
			else
			{
				$('#txt_style_no').val(job_no);
				$('#txt_style_id').val(job_id);	
			}
		}
	}
	
	function openmypage_order()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value;
		//alert (data);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/buyer_order_wise_prod_spent_min_produce_min_with_cm_report_controller.php?action=order_no_popup&data='+data,'Order No Popup', 'width=630px,height=420px,center=1,resize=0','../')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("order_no_id");
			var theemailv=this.contentDoc.getElementById("order_no_val");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById("txt_order_id").value=theemail.value;
			    document.getElementById("txt_order_no").value=theemailv.value;
				release_freezing();
			}
		}
	}
	
	function generate_order_report(po_id,company_id,job_no,buyer_id,style,ship_date,action,type_id)
	{  
		
		if(type_id==1) var popup_width=730;
		else if(type_id==2) var popup_width=730+80;
		else if(type_id==3) var popup_width=540;
		else if(type_id==4) var popup_width=530;
		else if(type_id==5) var popup_width=920;
		else if(type_id==6) var popup_width=650;
		
		//var popup_width='730px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/buyer_order_wise_prod_spent_min_produce_min_with_cm_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style='+style+'&ship_date='+ship_date+'&type_id='+type_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../');
	}
	
	
	
	
	function country_order_dtls(po_id,country_date,buyer_id,job_no,action)
	{  
		if (action=="country_trims_dtls_popup")
		{
			var popup_width='850px';
		}
		else
		{
			var popup_width='750px';
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/buyer_order_wise_prod_spent_min_produce_min_with_cm_report_controller.php?po_id='+po_id+'&country_date='+country_date+'&buyer_id='+buyer_id+'&job_no='+job_no+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}
	function country_order_dtls_trim(po_id,country_id,buyer_id,job_no,action)
	{  
		 
		var popup_width='850px';
		//country_trims_dtls_popup
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/buyer_order_wise_prod_spent_min_produce_min_with_cm_report_controller.php?po_id='+po_id+'&country_id='+country_id+'&buyer_id='+buyer_id+'&job_no='+job_no+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}
	
	function new_window1(type)
	{
		var report_div='';
		var scroll_div='';
		if(type==1)
		{
			report_div="yarn_summary";
			//scroll_div='scroll_body';
		}
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById(report_div).innerHTML+'</body</html>');
		d.close();
	}
	
	
	
	function search_populate(str)
	{
		if(str==1)
		{
			document.getElementById('search_by_th_up').innerHTML="Shipment Date";
			$('#search_by_th_up').css('color','blue');
		}
		else if(str==2)
		{
			document.getElementById('search_by_th_up').innerHTML="PO Received Date";
			$('#search_by_th_up').css('color','blue');
		}
		else if(str==3)
		{
			document.getElementById('search_by_th_up').innerHTML="PO Insert Date";
			$('#search_by_th_up').css('color','blue');
		}
	}
	

</script>
</head>
<body onLoad="set_hotkey();">
<form id="costSheetReport_1">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",$permission);  ?>
          <h3 align="left" id="accordion_h1" style="width:920px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
         <fieldset style="width:1000px;" id="content_search_panel">
            <table class="rpt_table" width="1000" cellpadding="1" cellspacing="2" align="center" border="1" rules="all">
                <thead>                    
                    <th class="must_entry_caption">Company</th>
					<th>Location</th>
                    <th>Buyer</th>
                    <th>Year</th>
                    <th>Style No.</th>
                    <th>Product Category</th>
                    <th colspan="2" id="search_by_th_up" class="must_entry_caption">Production Date</th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td> 
							<?
                           		echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/buyer_order_wise_prod_spent_min_produce_min_with_cm_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/buyer_order_wise_prod_spent_min_produce_min_with_cm_report_controller',this.value, 'load_drop_down_location', 'location_td' );get_php_form_data( this.value, 'buyer_location_multi_select', 'requires/buyer_order_wise_prod_spent_min_produce_min_with_cm_report_controller' );" );
                            ?>
                        </td>
						 <td id="location_td">
							<? 
                            	echo create_drop_down( "cbo_location_id", 100, $blank_array,"", 1, "--All Location--", $selected, "",0,"" );
                            ?>
                        </td>
                        <td id="buyer_td">
							<? 
                            	echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "--All Buyer--", $selected, "",0,"" );
                            ?>
                        </td>
						
                        <td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", "", "",0,"" ); ?></td>
                       
                         <td>
                        
                            <input type="text" id="txt_style_no" name="txt_style_no" class="text_boxes" style="width:70px" onDblClick="openmypage_job(3);" placeholder="Wr./Br. Style" />
                            <input type="hidden" id="txt_style_id" name="txt_style_id"/>
                        
                        </td>
                        <td width="" align="center">
							<?  
								echo create_drop_down( "cbo_product_cat", 100, $product_category,"",1, "--Select--", $selected,"",0,"" );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" >
                        </td>
                        <td>
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date" >
                        </td>
                    </tr>
                    <tr align="center"  class="general">
                        <td colspan="12">
                        	<? echo load_month_buttons(1); ?>
                         
                            <input type="reset" id="reset_btn" class="formbutton" style="width:60px;float:right" value="Reset" onClick="reset_form('costSheetReport_1','report_container*report_container2','','','')" />   
                            
                            &nbsp;
							<input type="button" id="show_button2" class="formbutton" style="width:60px; float:right" value="Buyer Wise" onClick="fn_report_generated2(3)" />
                            &nbsp;
                            <input type="button" id="show_button" class="formbutton" style="width:60px; float:right" value="PO Wise" onClick="fn_report_generated(1)" /> &nbsp;
							<input type="button" id="show_summary" class="formbutton" style="width:60px; float:right" value="Summary" onClick="fn_report_generated(2)" />
                            
                        </td>
                    </tr>
                    <tr >
                    	<td colspan="12" align="center" id="data_panel">
                          
                       	</td>
                    </tr>
                   
                </table> 
          </fieldset>
        </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
set_multiselect('cbo_buyer_name','0','0','','');
set_multiselect('cbo_location_id','0','0','','');
</script>
</html>