<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Ex-Factory Report.
Functionality	:
JS Functions	:
Created by		:	Jahid
Creation date 	: 	02-01-2014
Updated by 		: 	Shafiq
Update date		: 	15-02-2019
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
echo load_html_head_contents("Ex-Factory Report","../../../", 1, 1, $unicode,1,1);
?>
<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";

	var tableFilters =
	 {
		col_30: "none",
		col_operation: {
		id: ["total_buyer_org_po_quantity","total_buyer_po_quantity","value_total_buyer_po_value","parcentages","value_upcharge","total_current_ex_Fact_Qty","value_total_current_ex_fact_value","value_total_current_ex_fact_value_with_up_charge","mt_total_ex_fact_qty","value_mt_total_ex_fact_value","value_mt_total_ex_fact_value_with_up_charge","sales_minutes","sales_cm","total_buyer_basic_qnty"],
	   //col: [2,3,4,5,6,7,8,9,11],
	   col: [3,4,5,6,7,8,9,10,11,12,13,14,15,16],
	   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 }
	 var tableFilters2 =
	 {
		col_34: "none",
		col_operation: {
		//id: ["total_po_qty","value_total_po_valu","total_ex_qty","value_total_ex_valu","total_crtn_qty","g_total_ex_qty","value_g_total_ex_val","g_total_ex_crtn","value_sales_minutes","total_basic_qty","total_eecess_storage_qty","value_total_eecess_storage_val"],
		id: ["total_ex_qty","value_total_ex_valu","total_crtn_qty","g_total_ex_qty","value_g_total_ex_val","g_total_ex_crtn","value_sales_minutes","total_basic_qty","total_eecess_storage_qty","total_ex_per","value_cm_per_pcs_tot"],
	   //col: [19,21,22,23,24,25,26,27,28,29,30,31],
	   //col: [20,22,23,24,25,26,27,28,29,30,31,32],value_total_eecess_storage_val
	  // col: [26,27,28,29,30,31,32,33,35,36,37],
	   //col: [27,28,29,30,31,32,33,34,36,37,38],
	   col: [28,29,30,31,32,33,34,35,37,38,39],
	   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 }
	var tableFiltersDtls =
	{ 
		col_operation: { 
			id: ["total_po_qtybk","value_total_po_valubk","value_tdupcharge","total_ex_qty","total_ex_qty2","total_ex_qty3","value_total_ex_valu","value_current_ex_up_charge","value_current_ex_up_charge_value","total_crtn_qty","g_total_ex_qtybk","value_g_total_ex_valbk","value_total_ex_up_charge","value_total_ex_up_charge_value","g_total_ex_crtnbk","value_sales_minutesbk","total_basic_qtybk","total_eecess_storage_qtybk","value_total_eecess_storage_valbk","total_ex_perbk","value_cm_per_pcs_totbk"], 
			col: [25,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	 var tableFilters5 =
	 {
 	 	col_33: "none",
	 	col_operation: {
	 		id: ["gr_order_fob_id","gr_ex_qnty_id","gr_ex_fob_id"],
	 		col: [12,13,14],
	 		operation: ["sum","sum","sum","sum"],
	 		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML",]
	 	}
	 } //10, "gr_order_qnty_id",
	  var tableFilters10 =
	 {
 	 	col_33: "none",
	 	col_operation: {
	 		id: ["gr_order_qnty_id","gr_ex_qnty_id_curr","gr_ex_qnty_id","gr_ex_fob_id_short","gr_ex_fob_id_ctn_curr","gr_ex_fob_id_ctn"],
	 		col: [13,15,16,17,18,19],
	 		operation: ["sum","sum","sum","sum","sum","sum","sum"],
	 		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
	 	}
	 } //10, "gr_order_qnty_id",

	var tableFilters11 =
	 {
		col_operation: {
		id: ["total_po_qty","value_total_po_valu","total_ex_qty","value_total_ex_valu","total_crtn_qty","g_total_ex_qty","value_g_total_ex_val","g_total_ex_crtn","value_sales_minutes","total_basic_qty","total_eecess_qty","total_eecess_val","total_storage_qty","total_storage_val","total_balance_qty","total_balance_val","total_cbm"],
	   col: [17,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,35],
	   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 }

	 var tableFilters6 =
	 {
		col_operation: {
		id: ["total_ex_qty_td","value_total_cm_pcs_td","value_total_cm_pcs_lc_td","value_total_fob_td"],
		col: [12,13,14,15],
		operation: ["sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 }
	 var tableFilters8 =
	 {
		col_operation: {
		id: ["total_ex_qty_td","value_total_cm_pcs_td","value_total_cm_pcs_lc_td","value_total_fob_td"],
		col: [12,13,14,15],
		operation: ["sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 }
	 var tableFilters7 =
	 {
		col_operation: {
		id: ["total_po_qtybk","value_total_po_valubk","total_ex_qty","value_total_ex_valu","total_crtn_qty","g_total_ex_qtybk","value_g_total_ex_valbk","g_total_ex_crtnbk","value_sales_minutesbk","total_basic_qtybk","total_eecess_storage_qtybk","value_total_eecess_storage_valbk","total_ex_perbk","value_cm_per_pcs_totbk"],
		col: [23,25,26,27,28,29,30,35,36,37,38,39,40,41],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 }



	 var tableFilters14 =
	 {
		col_operation: {
		id: ["total_po_qtybk","value_total_po_valubk","value_total_po_valu_net","total_ex_qty","value_total_ex_valu","total_crtn_qty","g_total_ex_qtybk","value_g_total_ex_valbk","g_total_ex_crtnbk","value_sales_minutesbk","total_basic_qtybk","total_short_qty","value_total_short","total_excess_qty","value_total_excess","total_ex_perbk"],
		col: [23,25,26,27,28,29,30,31,36,37,38,40,41,42,43,44],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 }



	function chng_val(vall)
	{
		if(vall=1001)
		{
			if(form_validation('txt_date_from','Date From')==false)
			{
				if(form_validation('txt_date_to','Date From')==false)
				{
					return;
				}
			}
		}
		if(vall=1002)
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false)
			{
				return;
			}
		}
	}
	var reporttype=0;
	function fn_report_generated(type)
	{
		freeze_window(3);
		var company=$('#cbo_company_name').val();
		var del_company=$('#cbo_delivery_company_name').val();
		var internal_ref=$('#txt_internal_ref').val();
		var txt_date_from=$('#txt_date_from').val();
		var txt_date_to=$('#txt_date_to').val();
		reporttype=type;
		if(internal_ref !="")
		{
			var data="action=report_generate&reportType="+type+get_submitted_data_string('cbo_company_name*cbo_delivery_company_name*cbo_del_floor*cbo_source*cbo_shipping_status*cbo_location_name*txt_internal_ref*txt_date_from*txt_date_to*cbo_buyer_name*cbo_style_owner_company_name',"../../../");
			//alert(data);
			// freeze_window(3);
			http.open("POST","requires/monthly_ex_factory_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
		else if(txt_date_from=="" || txt_date_to=="")
		{
			if(form_validation('txt_date_from*txt_date_to','Date From*Date To')==false)
			{
				release_freezing();
				return;
			}
		}
		else
		{
			var data="action=report_generate&reportType="+type+get_submitted_data_string('cbo_company_name*cbo_delivery_company_name*cbo_del_floor*cbo_source*cbo_shipping_status*cbo_location_name*txt_internal_ref*txt_date_from*txt_date_to*cbo_buyer_name*cbo_brand_id*cbo_season_name*cbo_season_year*cbo_style_owner_company_name',"../../../");
			//alert(data);
			// freeze_window(3);
			http.open("POST","requires/monthly_ex_factory_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
	function fn_add_date_field()
	{
		$("#txt_date_to").val(add_days($('#txt_date_from').val(),'0'));
	}
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			release_freezing();
			var reponse=trim(http.responseText).split("####");
			//alert(reponse);
			$('#report_container2').html(reponse[0]);
			reporttype=reponse[2];
			if(reponse[2]==1)
			{
				setFilterGrid("table_body",-1,tableFilters);
				// setFilterGrid("table_body2",-1,tableFilters2);
				setFilterGrid("table_body2",-1,tableFiltersDtls);
			}
			else if(reponse[2]==6)
			{
				//setFilterGrid("table_body",-1,tableFilters);
				setFilterGrid("table_body2",-1,tableFilters6);
			}
			else if(reponse[2]==8)
			{
				setFilterGrid("table_body2",-1,tableFilters8);
			}
			else if(reponse[2]==7)
			{
				setFilterGrid("table_body2",-1,tableFilters7);
			}
			else if(reponse[2]==14)
			{
				setFilterGrid("table_body2",-1,tableFilters14);
			}
			else if(reponse[2]==18)
			{
				setFilterGrid("table_body_id",-1);
			}


			else if(reponse[2]==4)
			{
				setFilterGrid("table_body",-1,tableFilters5);
 				$("#gr_order_qnty_id").css("text-align","center");
				$("#gr_order_fob_id").css("text-align","center");
				$("#gr_ex_qnty_id").css("text-align","center");
				$("#gr_ex_fob_id").css("text-align","center");
			}
			else if(reponse[2]==10)
			{
				setFilterGrid("table_body",-1,tableFilters10);
 				$("#gr_order_qnty_id").css("text-align","center");
				$("#gr_ex_qnty_id_curr").css("text-align","center");
				$("#gr_ex_qnty_id").css("text-align","center");
				$("#gr_ex_fob_id_short").css("text-align","center");
			}
			else if(reponse[2]==11)
			{
				setFilterGrid("table_body2",-1,tableFilters11);
 				//$("#gr_order_qnty_id").css("text-align","center");
				//$("#gr_ex_qnty_id_curr").css("text-align","center");
				//$("#gr_ex_qnty_id").css("text-align","center");
				//$("#gr_ex_fob_id_short").css("text-align","center");
			}
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>&nbsp;&nbsp;<input type="button" onclick="new_window_summary()" value="Print Summary" name="Print" class="formbutton" style="width:100px"/>';
	 		show_msg('3');
		}
	}

	function fn_report_generated_excel(type)
	{
		freeze_window(3);
		var company=$('#cbo_company_name').val();
		var del_company=$('#cbo_delivery_company_name').val();
		var internal_ref=$('#txt_internal_ref').val();
		var txt_date_from=$('#txt_date_from').val();
		var txt_date_to=$('#txt_date_to').val();
		reporttype=type;
		if(internal_ref !="")
		{
			var data="action=report_generate_excel&reportType="+type+get_submitted_data_string('cbo_company_name*cbo_delivery_company_name*cbo_del_floor*cbo_source*cbo_shipping_status*cbo_location_name*txt_internal_ref*txt_date_from*txt_date_to*cbo_buyer_name*cbo_style_owner_company_name',"../../../");
			//alert(data);
			// freeze_window(3);
			http.open("POST","requires/monthly_ex_factory_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_excel_reponse;
		}
		else if(txt_date_from=="" || txt_date_to=="")
		{
			if(form_validation('txt_date_from*txt_date_to','Date From*Date To')==false)
			{
				release_freezing();
				return;
			}
		}
		else
		{
			var data="action=report_generate_excel&reportType="+type+get_submitted_data_string('cbo_company_name*cbo_delivery_company_name*cbo_del_floor*cbo_source*cbo_shipping_status*cbo_location_name*txt_internal_ref*txt_date_from*txt_date_to*cbo_buyer_name*cbo_brand_id*cbo_season_name*cbo_season_year*cbo_style_owner_company_name',"../../../");
			//alert(data);
			// freeze_window(3);
			http.open("POST","requires/monthly_ex_factory_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_excel_reponse;
		}
	}

	function fn_report_generated_excel_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("####");
			show_msg('3');
			if(reponse[0]!='')
			{
				//alert("reponse");
				$('#aa1').removeAttr('href').attr('href','requires/'+reponse[1]);
				document.getElementById('aa1').click();
			}
			show_msg('3');
			release_freezing();return;
		}
	}

	function new_window()
	{
		if(reporttype==1)
		{
			document.getElementById('scroll_body1').style.overflow="auto";
			document.getElementById('scroll_body1').style.maxHeight="none";
		}
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		$('#table_body tr:first').hide();
		$('#table_body2 tr:first').hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_print.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		$('#table_body tr:first').show();
		$('#table_body2 tr:first').show();
		if(reporttype==1)
		{
			document.getElementById('scroll_body1').style.overflow="scroll";
			document.getElementById('scroll_body1').style.maxHeight="225px";
		}

		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="225px";
	}

	function new_window_summary()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_print.css" type="text/css"/></head><body>'+document.getElementById('summary').innerHTML+'</body</html>');
		d.close();
	}

	function openmypage_ex_date(company_id,order_id,item_number_id,ex_factory_date,action,challan_id,type)
	{
		var popup_width='';
		if(action=="ex_date_popup") popup_width='550px'; else popup_width='550px';
		//3,'41170','01-Sep-2020*30-Sep-2020_1','ex_date_popup'

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/monthly_ex_factory_report_controller.php?order_id='+order_id+'&company_id='+company_id+'&ex_factory_date='+ex_factory_date+'&action='+action+'&challan_id='+challan_id+'&item_number_id='+item_number_id+'&type='+type, 'Detail View', 'width='+popup_width+', height=370px,center=1,resize=0,scrolling=0','../../');
	}

	function fnc_source(source,company)
	{
		var company_id = document.getElementById('cbo_delivery_company_name').value;
		load_drop_down( 'requires/monthly_ex_factory_report_controller', source+'**'+company, 'load_drop_delivery_company', 'dev_company_td' );
		set_multiselect('cbo_delivery_company_name','0','0','0','0');
		if(source==0)
		{
			$('#show_button1').hide();
			$('#show_button2').hide();
			$('#show_button3').hide();
			$('#show_button4').hide();
			$('#show_button5').hide();
			$('#show_button6').hide();
		}
		else
		{
			get_php_form_data(company,'print_button_variable_setting','requires/monthly_ex_factory_report_controller' );
		}
		setTimeout[($("#dev_company_td a").attr("onclick","disappear_list(cbo_delivery_company_name,'0');load_buyer_location();") ,3000)];
		$('#cbo_location_name').val(0);
		$('#cbo_del_floor').val(0);
	}

	function getButtonSetting()
	{
		var company_id = document.getElementById('cbo_delivery_company_name').value;
		get_php_form_data(company_id,'print_button_variable_setting','requires/monthly_ex_factory_report_controller' );
	}

	function print_report_button_setting(report_ids)
    {
        $('#show_button1').hide();
        $('#show_button2').hide();
        $('#show_button3').hide();
        $('#show_button4').hide();
        $('#show_button5').hide();
        $('#show_button6').hide();
        $('#show_button9').hide();
		$('#show_button10').hide();
		$('#show_button11').hide();
		$('#show_button7').hide();
		$('#show_button8').hide();
		$('#show_button12').hide();
		$('#show_button13').hide();
		$('#show_button14').hide();

        var report_id=report_ids.split(",");
        report_id.forEach(function(items){
            if(items==250){$('#show_button1').show();}
            else if(items==251){$('#show_button2').show();}
            else if(items==42){$('#show_button3').show();}
            else if(items==126){$('#show_button4').show();}
            else if(items==252){$('#show_button5').show();}
            else if(items==253){$('#show_button6').show();}
            else if(items==291){$('#show_button9').show();}
			else if(items==254){$('#show_button10').show();}
			else if(items==284){$('#show_button11').show();}
			else if(items==282){$('#show_button7').show();}
			else if(items==283){$('#show_button8').show();}
			else if(items==312){$('#show_button12').show();}
			else if(items==53){$('#show_button13').show();}
			else if(items==72){$('#show_button14').show();}
        });
    }

	function reset_company_val()
 	{
 		 var delv_val=$("#cbo_delivery_company_name").val();
 		 if(delv_val) $("#cbo_company_name").val();
 	}

	function fn_generate_print(button,sys_id,company,del_company,factory_date)
	{
		if(button==121){
			print_report( company +'*'+sys_id+'*'+factory_date+'*Garments Delivery Entry*2*../../*'+del_company, "ex_factory_print_new", "../../../production/requires/garments_delivery_entry_controller" )
			return;
		}
		else if(button==122){
		 	print_report( company+'*'+sys_id+'*'+factory_date+'*Garments Delivery Entry*2*../../', "ex_factory_print_new2", "../../../production/requires/garments_delivery_entry_controller" )
			return;
		}
		else if(button==123){
		 	print_report(company+'*'+sys_id+'*'+factory_date+'*Garments Delivery Entry*5*../../', "ExFactoryPrintSonia", "../../../production/requires/garments_delivery_entry_controller" )
			return;
		}
		else if(button==580)
		{
		 	print_report( company+'*'+sys_id+'*'+factory_date+'*Garments Delivery Entry*6*../../', "ex_factory_print2", "../../../production/requires/garments_delivery_entry_controller" );
		 	return;
		 }
		else if(button==169)
		{
		 	var answer = confirm("Do you want to show delivery info? Please click OK button for show and CANCEL button for hide.");
		 	// alert(answer);
		 	var show_delv_info = (answer==true) ? 1 : 0;
		 	print_report( company+'*'+sys_id+'*'+factory_date+'*Garments Delivery Entry*7*'+show_delv_info, "ex_factory_print_new3", "../../../production/requires/garments_delivery_entry_controller" );
		 	return;
		 }
		 else if(button==758)
		{
		 	print_report( company+'*'+sys_id+'*'+factory_date+'*Garments Delivery Entry*8*../../', "ex_factory_print_new7", "../../../production/requires/garments_delivery_entry_controller" );
		 	return;
		 }
		else if(button==78)
		{
			 print_report( company+'*'+sys_id+'*'+factory_date+'*Garments Delivery Entry*1*../../', "ex_factory_print", "../../../production/requires/garments_delivery_entry_controller" ) ;
			 return;
		}
		else
		{
			 print_report( company+'*'+sys_id+'*'+factory_date+'*Garments Delivery Entry*1*../../', "ex_factory_print", "../../../production/requires/garments_delivery_entry_controller" ) ;
			 return;
		}
	}


	function generate_print_button(str){
				 print_report( str, "print_invoice", "../../../commercial/export_details/requires/export_information_entry_controller" ) ;
				 return;

	}

    function style_owner_opt(val) {
        if ($('#cbo_company_name').val() == 0) {
            load_drop_down('requires/monthly_ex_factory_report_controller', val, 'load_drop_down_buyer', 'buyer_td');
            get_php_form_data(val, 'print_button_variable_setting', 'requires/monthly_ex_factory_report_controller');
        }
    }


</script>
</head>
<body onLoad="set_hotkey();">
 <div style="width:100%;" align="center">
<form id="monthly_ex_factory" name="monthly_ex_factory">
    <div style="width:1600px;" align="center">
        <? echo load_freeze_divs ("../../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:1595px;" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel">
            <fieldset style="width:1595px;">
                <table class="rpt_table" width="1595" border="1" align="center" rules="all">
                	<thead>
                    	<tr>
                            <th width="150">Company Name</th>
                            <th width="140">Style Owner</th>
                            <th width="100">Buyer</th>
                            <th width="100">Brand</th>
                            <th width="100">Season</th>
                            <th width="80">Season Year</th>
                            <th width="100">Source</th>
                            <th width="150">Delivery Company</th>
                            <th width="120">Delivery Location</th>
                            <th width="120">Floor</th>
                            <th width="80">Internal Ref.</th>
                            <th width="100">Shipment Status</th>
                            <th width="120" colspan="2" class="must_entry_caption">Ex-Factory Date</th>
                            <th><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:80px" onClick="reset_form('','report_container*report_container2','','','')" /></th>
                        </tr>
                     </thead>
                    <tbody>
                        <tr class="general" >
                            <td><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected,"load_drop_down( 'requires/monthly_ex_factory_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' ); get_php_form_data(this.value,'print_button_variable_setting','requires/monthly_ex_factory_report_controller' );" ); ?></td>
                            <td><? echo create_drop_down( "cbo_style_owner_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected,"style_owner_opt(this.value);" ); ?></td>
                            <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- Select --", $selected, "" ); ?></td>
                            <td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 100, $blank_array,"", 1, "-- Select --", $selected, "" ); ?></td>
                            <td id="season_td"><? echo create_drop_down( "cbo_season_name", 100, $blank_array,"", 1, "-- Select --", $selected, "" ); ?></td>
                            <td ><? echo create_drop_down( "cbo_season_year", 80, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?></td>
                            <td><? echo create_drop_down( "cbo_source", 100, $knitting_source,"", 1, "-- Select Source --", $selected, "fnc_source(this.value,$('#cbo_company_name').val()); ", 0, '1,3' ); ?></td>
                            <td id="dev_company_td"><? echo create_drop_down( "cbo_delivery_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 0, "-- Select Delivery Company --", $selected,"load_drop_down( 'requires/monthly_ex_factory_report_controller', this.value, 'load_drop_down_location', 'location' );" ); ?></td>
                            <td id="location"><? echo create_drop_down( "cbo_location_name", 120, $blank_array,"", 1, "-- Select --", $selected, "" ); ?></td>
                            <td id="del_floor_td"><? echo create_drop_down( "cbo_del_floor", 120, $blank_array,"", 1, "-- Select Delivery Floor --", $selected, "" );?></td>
                            <td> <input type="text" name="txt_internal_ref" id="txt_internal_ref" placeholder="Internal Ref." style="width:70px" class="text_boxes"></td>
                            <td id="shipment_status_td">
							<?
							$ship_status_arr = array(1=>"Full Pending",2=>"Partial Delivery",3=>"Full Delivery/Closed");
							echo create_drop_down( "cbo_shipping_status", 100, $ship_status_arr,"", 1,"-- All --","", "",0,"" );
							//echo create_drop_down( "cbo_shipping_status", 105, $shipment_status,"", 1, "-- Select --", 0, "",0,'0,2,3','','','','' );
							?>
							</td>
                            <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" onChange="fn_add_date_field()" ></td>
                            <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date" ></td>
                            <td><input type="button" id="show_button1" class="formbutton" style="width:60px;display: none;" value="Details" onClick="fn_report_generated(1);" /></td>
                        </tr>
                        <tr>
                            <td colspan="15" align="center">
								<? echo load_month_buttons(1); ?>
                                <input type="button" id="show_button2" class="formbutton" style="width:60px;" value="Monthly" onClick="fn_report_generated(2)" />
                                <input type="button" id="show_button9" class="formbutton" style="width:60px;" value="Monthly2" onClick="fn_report_generated(9)" />
                                <input type="button" id="show_button12" class="formbutton" style="width:60px;" value="Monthly 3" onClick="fn_report_generated(12)" />
                                <input type="button" id="show_button3" class="formbutton" style="width:65px;" value="Challan" onClick="fn_report_generated(3)" />
                                <input type="button" id="show_button4" class="formbutton" style="width:65px;" value="Country" onClick="fn_report_generated(4)" />
                                <input type="button" id="show_button5" class="formbutton" style="width:65px;" value="Country 2" onClick="fn_report_generated(5)" />
                                <input type="button" id="show_button6" class="formbutton" style="width:60px;" value="Daily" onClick="fn_report_generated(6)" />
                                <input type="button" id="show_button6" class="formbutton" style="width:60px;" value="Daily 2" onClick="fn_report_generated(16)" />
                                <input type="button" id="show_button7" class="formbutton" style="width:60px;" value="Details 2" onClick="fn_report_generated(7);" />
                                <input type="button" id="show_button8" class="formbutton" style="width:60px;" value="Details 3" onClick="fn_report_generated(8);" />
                                <input type="button" id="show_button10" class="formbutton" style="width:78px;" value="Show-Country" onClick="fn_report_generated(10)" />


                                <input type="button" id="show_button11" class="formbutton" style="width:80px;" value="Details 4" onClick="fn_report_generated(11)" />
                                <input type="button" id="show_button13" class="formbutton" style="width:80px;" value="Details 5" onClick="fn_report_generated(13)" />


                                <input type="button" id="show_button14" class="formbutton" style="width:60px;" value="Details 6" onClick="fn_report_generated(14);" />
                                <input type="button" id="show_button15" class="formbutton" style="width:60px;" value="Details 7" onClick="fn_report_generated(15);" />
								<input type="button" id="show_button16" class="formbutton" style="width:60px;" value="EX VS INV" onClick="fn_report_generated(18);" />

								<input type="button" id="show_button1" class="formbutton" style="width:60px;" value="Details excl" onClick="fn_report_generated_excel(1);" />
								<input type="button" id="show_button5" class="formbutton" style="width:80px;" value="Country 2 excl" onClick="fn_report_generated_excel(2);" />
								<a id="aa1" style="text-decoration:none;"></a>
                            </td>
                        </tr>
                    </tbody>
                </table>

            </fieldset>
        </div>
    </div>
     </form>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
    <div style="display:none" id="data_panel"></div>
 </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
	set_multiselect('cbo_delivery_company_name','0','0','0','0');
	//setTimeout[($("#dev_company_td a").attr("onclick","disappear_list(cbo_delivery_company_name,'0');getCompanyId();getButtonSetting();") ,3000)];

	$("#multi_select_cbo_delivery_company_name a").click(function(){
		load_buyer_location();
 	});
	function load_buyer_location()
	{
		var company=$("#cbo_delivery_company_name").val();
		load_drop_down( 'requires/monthly_ex_factory_report_controller', company, 'load_drop_down_location', 'location' );
		reset_company_val();
		getButtonSetting();
	}
</script>
<script>
	$('#cbo_location_name').val(0);
</script>
</html>
