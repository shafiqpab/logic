<?
/*-------------------------------------------- Comments
Purpose			: 	This Report will create color wise finish fabric stock report
				
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam
Creation date 	: 	06-03-2019
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
echo load_html_head_contents("Finish Fabric Stock Report","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	
	
	function openmypage_item_account()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		 var data=document.getElementById('cbo_company_name').value+'__'+document.getElementById('cbo_buyer_id').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/color_wise_finish_fabric_stock_report_controller.php?action=item_account_popup&data='+data,'Item Account Popup', 'width=710px,height=420px,center=1,resize=0','../../')
		
		emailwindow.onclose=function()
		{
			var item_account_id=this.contentDoc.getElementById("txt_selected_id").value;
			var item_account_des=this.contentDoc.getElementById("txt_selected").value;
			var item_account_no=this.contentDoc.getElementById("txt_selected_no").value;
			document.getElementById("txt_product_id").value=item_account_id;
			document.getElementById("txt_product_id_des").value=item_account_des;
			//document.getElementById("txt_item_account_no").value=item_account_no;
		}
	}
	
	
	
	function fn_receive_dtls(po_id)
	{

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/color_wise_finish_fabric_stock_report_controller.php?action=receive_popup&data='+po_id,'Receive', 'width=710px,height=420px,center=1,resize=0','../../')
		
		emailwindow.onclose=function()
		{
			var item_account_id=this.contentDoc.getElementById("txt_selected_id").value;
			var item_account_des=this.contentDoc.getElementById("txt_selected").value;
			var item_account_no=this.contentDoc.getElementById("txt_selected_no").value;
			document.getElementById("txt_product_id").value=item_account_id;
			document.getElementById("txt_product_id_des").value=item_account_des;
		}
			
		
		
	}
	
	
	
	
	
	
	
	
	var tableFilters = 
	{
		col_operation: {
		id: ["td_opening_stock","td_rcv_qty","td_transfer_in","td_issue_return","td_total_rcv","td_issue_qty","td_transfer_out","td_rcv_return","td_total_issue","td_closing_stock"],
		col: [15,16,17,18,19,20,21,22,23,24],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 
        
	
	function generate_report(operation)
	{
		if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date Form*Date To')==false )
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html(); 
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_buyer_id = $("#cbo_buyer_id").val();
		var cbo_season_id = $("#cbo_season_id").val();
		var txt_product_id = $("#txt_product_id").val();
		var txt_product_id_des = $("#txt_product_id_des").val();
		var txt_gsm = $("#txt_gsm").val();
		var txt_color = $("#txt_color").val();
		var cbo_sample_type = $("#cbo_sample_type").val();
		var txt_booking = $("#txt_booking").val();
		var txt_date_from = $("#txt_date_from").val();
		var txt_date_to = $("#txt_date_to").val();
		var cbo_store_name = $("#cbo_store_name").val();
		var cbo_fabric_source = $("#cbo_fabric_source").val();
		var cbo_year = $("#cbo_year_selection").val();
		var txt_job_prefix = $("#txt_job_prefix").val();
		
		var data = "action=generate_report&cbo_company_name="+cbo_company_name+"&txt_product_id="+txt_product_id+"&txt_gsm="+txt_gsm+"&txt_color="+txt_color+"&cbo_sample_type="+cbo_sample_type+"&txt_booking="+txt_booking+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&report_title="+report_title+"&report_type="+operation+"&cbo_buyer_id="+cbo_buyer_id+"&cbo_season_id="+cbo_season_id+"&txt_product_id_des="+txt_product_id_des+"&cbo_store_name="+cbo_store_name+"&cbo_fabric_source="+cbo_fabric_source+"&cbo_year="+cbo_year+"&txt_job_prefix="+txt_job_prefix;

		freeze_window(operation);
		http.open("POST","requires/color_wise_finish_fabric_stock_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			
			var reponse=trim(http.responseText).split("**");
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
		$("#table_body tr:first").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="250px";
                $('#scroll_body tr:first').show();
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
    <? echo load_freeze_divs ("../../../",$permission);  ?>    		 
        <form name="closingstock_1" id="closingstock_1" autocomplete="off" > 
         <h3 style="width:1550px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:1550px" >      
            <fieldset>  
                <table class="rpt_table" width="1550" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <th width="130" class="must_entry_caption">Company</th>
                        <th width="120">Buyer</th>
                        <th width="120">Store</th>
                        <th width="100">Season</th>
                        <th width="100">Fabric Source</th>
                        <th width="90">Composition</th>
                        <th width="90">GSM</th>
                        <th width="120">Color</th>
                        <th width="120"> Type</th>
                        <th width="100"> Job</th>
                        <th width="120">Booking</th>
                        <th class="must_entry_caption" width="170">Date</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_form('closingstock_1','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td>
                                <? 
                                    echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/color_wise_finish_fabric_stock_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/color_wise_finish_fabric_stock_report_controller',this.value, 'load_drop_down_store', 'store_td' );" );
                                ?>                            
                            </td>
                           	<td id="buyer_td">
								<?php 
									echo create_drop_down( "cbo_buyer_id", 120,"","", 1, "-- All --", 1, "","","","","","");
                                ?> 
                          	</td>
                           <td id="store_td">
                                <? 
                                    echo create_drop_down( "cbo_store_name", 120, $blank_array,"", 1, "--Select Store--", "", "" );
                                ?>
                           </td>
                            
							<td id="season_td">
                                <?   
                                    echo create_drop_down( "cbo_season_id", 100, "","",1,"--All--",0,"","","");
                                ?>
                            </td>
                            <td>
                            	<? echo create_drop_down( "cbo_fabric_source", 100, $fabric_source,"", 1, "-- Select --", "","", "", "1,2"); ?>
                            </td>
                            <td><input type="hidden" name="txt_product_id" id="txt_product_id" readonly />
                            	<input style="width:90px;" name="txt_item_acc" id="txt_product_id_des" class="text_boxes" onDblClick="openmypage_item_account__()" placeholder="Write"  />
                            </td>
                            <td>
                                <input type="text" name="txt_gsm" id="txt_gsm" value="" class="text_boxes" style="width:110px;" placeholder="Write" />
                            </td>
                           <td>
                           	<input type="text" name="txt_color" id="txt_color" value="" class="text_boxes" style="width:110px;" placeholder="Write" /> 
                           </td>
                            <td>
                                <? 
                                    $typeArr=array(1=>'With Order',2=>'Without Order');
									echo create_drop_down( "cbo_sample_type", 120, $typeArr,"", 1, "--All--", "", "", "","" );
                                ?>
                           </td>
                            <td>
                                <input type="text" name="txt_job_prefix" id="txt_job_prefix" value="" class="text_boxes" placeholder="Job Prefix" style="width:100px;" />                    							
                            </td>
                            <td>
                                <input type="text" name="txt_booking" id="txt_booking" value="" class="text_boxes" style="width:120px;" />                    							
                            </td>
                            <td>
                                <input type="text" name="txt_date_from" id="txt_date_from" value=" <? echo date("d-m-Y", time()); //- 86400?> " class="datepicker" style="width:60px;" readonly />                    							
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" value=" <? echo date("d-m-Y", time()); //- 86400?>" class="datepicker" style="width:60px;" readonly />                        
                            </td>
                            <td>
                                <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:60px" class="formbutton" />
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="12" align="center">
                                <? echo load_month_buttons(1);  ?>
                            </td>
                        </tr>
                    </tfoot>
                </table> 
            </fieldset> 
            </div>
            <br /> 
                <div id="report_container" align="center"></div>
                <div id="report_container2"></div> 
        </form>    
    </div>
</body>  
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
