<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Purchase Recap Report.
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	18-08-2015
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
echo load_html_head_contents("Purchase Recap Report", "../../", 1, 1,'','','');
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
 
	var permission = '<? echo $permission; ?>';
	
	var tableFilters = { 
		col_80: "none", 
		col_operation: {
			id: ["value_total_req_qnty","value_total_wo_qnty","value_total_wo_amt","value_total_wo_balance","value_total_pi_qnty","value_total_pi_amt","value_total_lc_amt","value_total_pkg_qnty","value_total_pay_amt","value_total_mrr_qnty","value_total_mrr_amt","value_total_short_amt","value_total_pipe_line"],
			col: [12,19,21,23,30,32,39,51,58,59,60,61,62],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	
	function generate_report(rep_type)
	{
		var supplier_id=$("#cbo_supplier").val();
		var date_type=$("#cbo_date_type").val();
		var txt_req_no=$("#txt_req_no").val();
		var txt_wo_po_no=$("#txt_wo_po_no").val();
		var txt_pi_no=$("#txt_pi_no").val();
		var txt_lc_no=$("#txt_lc_no").val();
		var txt_pi_id=$("#txt_pi_id").val();
		var txt_lc_id=$("#txt_lc_id").val();
		var item_category_id=$("#cbo_item_category_id").val();
		var cbo_location=$("#cbo_location").val();
		var cbo_store_name=$("#cbo_store_name").val();
		
		if(supplier_id>0 && date_type==1)
		{
			alert("Requisition Date Not Allow With Supplier");return;
		}
		
		if(txt_req_no =="" && txt_wo_po_no =="" && txt_pi_no =="" && txt_lc_no =="")
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_name','Company Name')==false)
			{
				return;
			}
		}

		if(rep_type==2 && date_type==1)
		{
			alert("Requisition Date Not Allow For Trims Report.");return;
		}

		var action='';
		if(rep_type==1){
			action="report_generate";
		}else if(rep_type==2){
			action="report_generate_trims";
		}else{
			action="report_generate_woven";
		}
		
		var report_title=$( "div.form_caption" ).html();	
		var data="action="+action+get_submitted_data_string('cbo_company_name*cbo_item_category_id*txt_req_no*txt_date_from*txt_date_to*cbo_supplier*txt_wo_po_no*cbo_date_type*txt_pi_no*txt_pi_id*txt_lc_no*txt_lc_id*cbo_location*cbo_store_name',"../../")+'&report_title='+report_title;
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/purchase_recap_report3_controller.php",true);
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
			
			
			if (response[2]==3) 
			{
				document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window2()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				setFilterGrid("table_body",-1,tableFilters);
			}
			else 
			{
				document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			} 			
			show_msg('3');
			if (response[2]==2) setFilterGrid('table_body',-1);
			release_freezing();
		}
	}
	
	function new_window2()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tbody').find('tr:first').hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');// media="print"
		d.close();

		$('#table_body tbody').find('tr:first').show();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="300px";
	}

	function new_window()
	{
		/*document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tbody').find('tr:first').hide();*/
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');// media="print"
		d.close();

		/*$('#table_body tbody').find('tr:first').show();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="300px";*/
	}
	
	function openmypage_popup(wo_pi_req,prod_id,page_title,action)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/purchase_recap_report3_controller.php?wo_pi_req='+wo_pi_req+'&prod_id='+prod_id+'&action='+action, page_title, 'width=820px,height=400px,center=1,resize=0,scrolling=0','../');
	}

	function change_date_caption(id)
	{
		if (id==1) { $("#dynamic_caption").html("Requisition Date"); }
		else if (id==2) { $("#dynamic_caption").html("Purchase Order Date"); }
		else if (id==3) { $("#dynamic_caption").html("PI Date"); }
		else if (id==4) { $("#dynamic_caption").html("Import LC Date"); }
	}
	
	function fn_req_wo_check(str)
	{
		if(str>0)
		{
			$('#txt_req_no').val("").attr("disabled",true);	
			$('#txt_wo_po_no').val("").attr("disabled",true);		
		}
		else
		{
			$('#txt_req_no').val("").attr("disabled",false);	
			$('#txt_wo_po_no').val("").attr("disabled",false);
		}
	}
	
	function fn_req_wo(str)
	{
		var txt_req_no=$('#txt_req_no').val();
		var txt_wo_po_no=$('#txt_wo_po_no').val();
		var txt_pi_no=$('#txt_pi_no').val();
		if(str==1 && txt_req_no)
		{
			$('#txt_req_no').attr("disabled",false);
			$('#txt_wo_po_no').val("").attr("disabled",true);
			$('#txt_pi_no').val("").attr("disabled",true);		
		}
		else if(str==2 && txt_wo_po_no)
		{
			$('#txt_wo_po_no').attr("disabled",false);
			$('#txt_req_no').val("").attr("disabled",true);
			$('#txt_pi_no').val("").attr("disabled",true);	
		}
		else  if(str==3 && txt_pi_no)
		{
			$('#txt_req_no').val("").attr("disabled",true);	
			$('#txt_wo_po_no').val("").attr("disabled",true);
			$('#txt_pi_no').attr("disabled",false);
		}
		else
		{
			$('#txt_req_no').val("").attr("disabled",false);	
			$('#txt_wo_po_no').val("").attr("disabled",false);
			$('#txt_pi_no').val("").attr("disabled",false);
			$('#txt_date_from').val("").attr("disabled",false);
			$('#txt_date_to').val("").attr("disabled",false);
			$("#txt_date_from").addClass("datepicker");
		}
		
		/*if(str!=''){
			$('#txt_date_from').val("").attr("disabled",true);
			$('#txt_date_to').val("").attr("disabled",true);
		}else{
			$('#txt_date_from').val("").attr("disabled",false);
			$('#txt_date_to').val("").attr("disabled",false);
		}*/
	}

	function openmypage_wo()
	{
		if( form_validation('cbo_company_name*cbo_item_category_id','Company Name*Item Category')==false ){
			return;
		}
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_supplier = $("#cbo_supplier").val();
		var item_category_id = $("#cbo_item_category_id").val();
		if(item_category_id!=3){
			alert("Only for Woven Fabrics Category");
			return;
		}

		var page_link='requires/purchase_recap_report3_controller.php?action=wo_no_popup&cbo_company_name='+cbo_company_name+'&cbo_supplier='+cbo_supplier+'&item_category_id='+item_category_id;
		var title='WO Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=460px,height=350px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var wo_no=this.contentDoc.getElementById("hide_wo_no").value;
			var wo_id=this.contentDoc.getElementById("hide_wo_id").value;
			$('#txt_wo').val(wo_no);
			$('#txt_wo_po_no').val(wo_no);
			$('#txt_wo_id').val(wo_id);
			if(wo_id!='')
			{
				$('#txt_date_from').val("").attr("disabled",true);
				$('#txt_date_to').val("").attr("disabled",true);
			}
		}
	}

	function openmypage_pi()
	{
		if( form_validation('cbo_company_name*cbo_item_category_id','Company Name*Item Category')==false ){
			return;
		}
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_supplier = $("#cbo_supplier").val();
		var item_category_id = $("#cbo_item_category_id").val();
		if(item_category_id!=4 & item_category_id!=3){
			alert("Only for Accessories Category");
			return;
		}

		var page_link='requires/purchase_recap_report3_controller.php?action=pi_no_popup&cbo_company_name='+cbo_company_name+'&cbo_supplier='+cbo_supplier+'&item_category_id='+item_category_id;
		var title='PI Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=460px,height=350px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var pi_no=this.contentDoc.getElementById("hide_pi_no").value;
			var pi_id=this.contentDoc.getElementById("hide_pi_id").value;
			$('#txt_pi').val(pi_no);
			$('#txt_pi_no').val(pi_no);
			$('#txt_pi_id').val(pi_id);
			if(pi_id!='')
			{
				$('#txt_date_from').val("").attr("disabled",true);
				$('#txt_date_to').val("").attr("disabled",true);
			}
		}
	}
    function openmypage_lc()
    {
        if( form_validation('cbo_company_name*cbo_item_category_id','Company Name*Item Category')==false ){
            return;
        }
        var cbo_company_name = $("#cbo_company_name").val();
        var cbo_supplier = $("#cbo_supplier").val();
        var item_category_id = $("#cbo_item_category_id").val();
        if(item_category_id!=4 & item_category_id!=3){
            alert("Only for Accessories Category");
            return;
        }

        var page_link='requires/purchase_recap_report3_controller.php?action=lc_no_popup&cbo_company_name='+cbo_company_name+'&cbo_supplier='+cbo_supplier+'&item_category_id='+item_category_id;
        var title='LC Search';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=560px,height=350px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var lc_no=this.contentDoc.getElementById("hide_lc_no").value;
            var lc_id=this.contentDoc.getElementById("hide_lc_id").value;
            $('#txt_lc').val(lc_no);
            $('#txt_lc_no').val(lc_no);
            $('#txt_lc_id').val(lc_id);
            if(lc_id != '')
            {
                $('#txt_date_from').val("").attr("disabled",true);
                $('#txt_date_to').val("").attr("disabled",true);
            }
        }
    }

	function fnc_chk_category(id)
	{
		if(id==3)
		{
			$('#txt_req_no').val("").attr("disabled",true);
			// $('#txt_pi_no').val("").attr("disabled",true);
            // $('#txt_lc_no').val("").attr("disabled",true);
			$('#cbo_date_type').val(3).attr("disabled",true);
			change_date_caption(3);
		}
		else
		{
			$('#txt_req_no').val("").attr("disabled",false);
			$('#txt_pi_no').val("").attr("disabled",false);
			$('#txt_lc_no').val("").attr("disabled",false);
			$('#cbo_date_type').val(1).attr("disabled",false);
			change_date_caption(1);
		}	
	}

	function fnc_yarn_req_entry(operation, reqId)
	{
		var report_title = 'Purchase Requisition';
		if(operation==4)
		 {
			print_report( $('#cbo_company_name').val()+'*'+reqId+'*'+report_title+'*'+$('#is_approved').val(),"yarn_requisition_print", "../work_order/requires/yarn_requisition_entry_controller")
			return;
		 }

		 else if(operation==6)
		 {
			print_report( $('#cbo_company_name').val()+'*'+reqId+'*'+report_title+'*'+$('#cbo_basis').val(),"yarn_requisition_print_2", "../work_order/requires/yarn_requisition_entry_controller")
			return;
		 }

		 else if(operation==7)
		 {

			print_report( $('#cbo_company_name').val()+'*'+reqId+'*'+report_title+'*'+operation+'*'+$("#cbo_basis").val(),"yarn_requisition_print_3", "../work_order/requires/yarn_requisition_entry_controller")
			return;
		 }

		else if(operation==8)
		{
			print_report( $('#cbo_company_name').val()+'*'+reqId+'*'+report_title,"yarn_requisition_print_4", "../work_order/requires/yarn_requisition_entry_controller")
			return;
		}
		else if(operation==9)
		{
			print_report( $('#cbo_company_name').val()+'*'+reqId+'*'+report_title,"yarn_requisition_print_5", "../work_order/requires/yarn_requisition_entry_controller")
			return;
		}
	}

	function generate_report_purchase(type, reqId, locationId, isApproved)
	{
		var report_title = 'Purchase Requisition';
		var show_item= '';
		var templateId = 1;
		var page = 'purchase_recap_report3';

		if(type==3)
		{
			print_report( $('#cbo_company_name').val()+'*'+reqId+'*'+report_title+'*'+'122'+'*'+type+'*'+show_item+'*'+templateId+'*'+locationId+'*'+page, "purchase_requisition_print_2", "../../inventory/requires/purchase_requisition_controller" ) ;
			//return;
			show_msg("3");
			//var show_item="";
			//var report_title=$( "div.form_caption" ).html();
			/*print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remarks').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_2", "requires/purchase_requisition_controller" ) ;
			//return;
			show_msg("3");*/

		}
		else if(type==5)
		{			
			print_report( $('#cbo_company_name').val()+'*'+reqId+'*'+report_title+'*'+''+'*'+type+'*'+show_item+'*'+templateId+'*'+locationId+'*'+page+'*'+$('#is_approved').val(), "purchase_requisition_print_3", "../../inventory/requires/purchase_requisition_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(type==8)
		{			
			print_report( $('#cbo_company_name').val()+'*'+reqId+'*'+report_title+'*'+169+'*'+type+'*'+show_item+'*'+templateId+'*'+locationId+'*'+page, "purchase_requisition_print_8", "../../inventory/requires/purchase_requisition_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(type==6)
		{
			print_report( $('#cbo_company_name').val()+'*'+reqId+'*'+report_title+'*'+''+'*'+type+'*'+show_item+'*'+templateId+'*'+locationId+'*'+page, "purchase_requisition_print_4", "../../inventory/requires/purchase_requisition_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(type==9)
		{
			print_report( $('#cbo_company_name').val()+'*'+reqId+'*'+report_title+'*'+''+'*'+type+'*'+show_item+'*'+templateId+'*'+locationId+'*'+page, "purchase_requisition_print_9", "../../inventory/requires/purchase_requisition_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(type==7)
		{
			print_report( $('#cbo_company_name').val()+'*'+reqId+'*'+report_title+'*'+''+'*'+type+'*'+show_item+'*'+templateId+'*'+locationId+'*'+page, "purchase_requisition_print_5", "../../inventory/requires/purchase_requisition_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(type==10)
		{	
			var show_item="";
			var r=confirm("Press  \"Cancel\"  to hide  Last Rate & Req Value \nPress  \"OK\"  to Show Last Rate & Req Value");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}
			
			print_report( $('#cbo_company_name').val()+'*'+reqId+'*'+report_title+'*'+''+'*'+type+'*'+show_item+'*'+templateId+'*'+locationId+'*'+page, "purchase_requisition_print_10", "../../inventory/requires/purchase_requisition_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(type==11)
		{
			print_report( $('#cbo_company_name').val()+'*'+reqId+'*'+report_title+'*'+''+'*'+type+'*'+''+'*'+templateId+'*'+locationId+'*'+page, "purchase_requisition_print_11", "../../inventory/requires/purchase_requisition_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(type==12)
		{		
			print_report( $('#cbo_company_name').val()+'*'+reqId+'*'+report_title+'*'+''+'*'+type+'*'+show_item+'*'+templateId+'*'+locationId+'*'+page, "purchase_requisition_print_4_akh", "../../inventory/requires/purchase_requisition_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(type==13)
		{	
			print_report( $('#cbo_company_name').val()+'*'+reqId+'*'+report_title+'*'+''+'*'+type+'*'+show_item+'*'+templateId+'*'+locationId+'*'+page, "purchase_requisition_print_13", "../../inventory/requires/purchase_requisition_controller" ) ;
			show_msg("3");
		}
		else if(type==14)
		{			
			print_report( $('#cbo_company_name').val()+'*'+reqId+'*'+report_title+'*'+''+'*'+type+'*'+show_item+'*'+templateId+'*'+locationId+'*'+page, "purchase_requisition_print_14", "../../inventory/requires/purchase_requisition_controller" ) ;
			show_msg("3");
		}
		else if(type==15)
		{
			print_report( $('#cbo_company_name').val()+'*'+reqId+'*'+report_title+'*'+''+'*'+type+'*'+show_item+'*'+templateId+'*'+locationId+'*'+page, "purchase_requisition_print_15", "../../inventory/requires/purchase_requisition_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(type==16)
		{			
			print_report( $('#cbo_company_name').val()+'*'+reqId+'*'+report_title+'*'+''+'*'+type+'*'+show_item+'*'+templateId+'*'+locationId+'*'+page, "purchase_requisition_print_16", "../../inventory/requires/purchase_requisition_controller" ) ;
			//return;
			show_msg("3");
		}
		else
		{	
			print_report( $('#cbo_company_name').val()+'*'+reqId+'*'+report_title+'*'+isApproved+'*'+''+'*'+templateId+'*'+locationId, "purchase_requisition_print", "../../inventory/requires/purchase_requisition_controller" ) ;
			//return;
			show_msg("3");
		}
	}

	function generate_report_comparative_statement(update_id, txt_system_id)
	{	
		var report_title = 'Comparative Statement';

			print_report( update_id+'*'+report_title+'*'+txt_system_id+'*'+1, "print_report_estimated_price", "../../commercial/work_order/requires/comparative_statement_controller" ) ;
			//return;
			show_msg("3");
	}
	
</script>
</head>

<body onLoad="set_hotkey();">
<form id="PurchaseRecap_report">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",''); ?>
        <h3 align="left" id="accordion_h1" style="width:1510px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
            <fieldset style="width:1510px;">
                <table class="rpt_table" width="1500" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">
                	<thead>
                   		<tr>                    
                            <th width="160" class="must_entry_caption">Company Name</th>
                            <th width="130">Location</th>
                            <th width="160">Item Category</th>
                            <th width="130">Store</th>
                            <th width="160">Supplier</th>
                            <th width="90">Requisition No</th>
                            <th width="90">WO No</th>
                            <th width="90">PI No</th>
                            <th width="90">LC No</th>
                            <th width="100">Data Type</th>
                            <th width="170" class="must_entry_caption" id="dynamic_caption">Requisition Date</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('PurchaseRecap_report','report_container*report_container2','','','')" /></th>
                        </tr>
                     </thead>
                    <tbody>
                        <tr class="general">
                            <td align="center"> 
								<?
                                echo create_drop_down( "cbo_company_name", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/purchase_recap_report3_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );load_drop_down( 'requires/purchase_recap_report3_controller', this.value, 'load_drop_down_location', 'location_td' );" );
								//function create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name )
                                ?>
                            </td>
                            <td align="center" id="location_td">
                            	<? 
                            		echo create_drop_down( "cbo_location", 130, $blank_array,"", 1,"-- Select --",0,"" );
                            	?>
                            </td>
                            <td align="center" id="category_td">
                            	<? 
                            		echo create_drop_down( "cbo_item_category_id", 160, $item_category,'', 1, '-- Select --',0,"fnc_chk_category(this.value);load_drop_down( 'requires/purchase_recap_report3_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_supplier', 'supplier_td' );",0,'','','','2,12,13,14,24,25,28,30'); //1,2,3,4,12,13,14,24,25,28,30 (was not showing) 
                            	?>
                            </td>
                            <td id="store_td"> 
						  	<?
							   	echo create_drop_down( "cbo_store_name", 130, $blank_array,"", 1,"-- Select --",0,"" );
 							?>
							</td>
                            <td id="supplier_td"> 
						  	<?
							   	echo create_drop_down( "cbo_supplier", 160, $blank_array,"", 1,"-- Select --",0,"" );
 							?>
							</td>
                            <td align="center">
                            	<input type="text" name="txt_req_no" id="txt_req_no" value="" class="text_boxes" style="width:80px" onBlur="fn_req_wo(1);" />
                            </td>
                            <td align="center">
                            	<input type="text" name="txt_wo_po_no" id="txt_wo_po_no" value="" class="text_boxes" style="width:80px" placeholder="Browse/Write" onDblClick="openmypage_wo();"  onBlur="fn_req_wo(2);" />
                            	<input type="hidden" name="txt_wo" id="txt_wo">
                            	<input type="hidden" name="txt_wo_id" id="txt_wo_id">
                            </td>
                            <td align="center">
                            	<input type="text" name="txt_pi_no" id="txt_pi_no" value="" class="text_boxes" style="width:80px" placeholder="Browse/Write" onDblClick="openmypage_pi();"  onBlur="fn_req_wo(3);" />
                            	<input type="hidden" name="txt_pi" id="txt_pi">
                            	<input type="hidden" name="txt_pi_id" id="txt_pi_id">
                            </td>
                            <td align="center">
                                <input type="text" name="txt_lc_no" id="txt_lc_no" value="" class="text_boxes" style="width:80px" placeholder="Browse/Write" onDblClick="openmypage_lc();"  onBlur="fn_req_wo(3);" />
                                <input type="hidden" name="txt_lc" id="txt_lc">
                                <input type="hidden" name="txt_lc_id" id="txt_lc_id">
                            </td>
                            <td>
                            	<?
                            		//$date_type_arr=array(1=>"Requisition No",2=>"Purchase Order",3=>"PI",4=>"Import LC");
									$date_type_arr=array(1=>"Requisition No",2=>"Purchase Order",3=>"PI",4=>"Import LC");
									echo create_drop_down( "cbo_date_type", 100, $date_type_arr,"", 0, "--Select Date--", 0,"change_date_caption(this.value)" );
                            	?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:60px" placeholder="From Date" readonly />
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:60px" placeholder="To Date" readonly />
                            </td>
                            <td>
                            	<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                            </td>
                        </tr>
                        <tr>
                        	<td colspan="12" align="center" width="95%"><? echo load_month_buttons(1); ?>&nbsp;&nbsp;<input type="button" name="search2" id="search2" value="Trims Report" onClick="generate_report(2)" style="width:100px" class="formbutton" /><input type="button" name="search3" id="search3" value="Woven Report" onClick="generate_report(3)" style="width:100px" class="formbutton" /></td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
    
    <div style="margin-top:10px" id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
