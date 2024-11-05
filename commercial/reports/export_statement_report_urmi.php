<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Export Statement Report.
Functionality	:
JS Functions	:
Created by		:	Md Shafiqul Islam Shafiq
Creation date 	: 	30-03-2020
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
echo load_html_head_contents("Ex-Factory Report","../../", 1, 1, $unicode,1,1);
?>
<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	var tableFilters =
	{
		col_operation: {
			id: ['value_gr_carton_qty','value_gr_ship_qty','value_gr_ship_val'],
			col: [14,15,17],
			operation: ['sum','sum','sum'],
			write_method: ['innerHTML','innerHTML','innerHTML']
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
		var company=$('#cbo_company_name').val();
		var del_company=$('#cbo_delivery_company_name').val();
		var internal_ref=$('#txt_internal_ref').val();
		var txt_date_from=$('#txt_date_from').val();
		var txt_date_to=$('#txt_date_to').val();
		reporttype=type;
		if(internal_ref !="")
		{
			var data="action=report_generate&reportType="+type+get_submitted_data_string('cbo_company_name*cbo_delivery_company_name*cbo_del_floor*cbo_source*cbo_shipping_status*cbo_location_name*txt_internal_ref*txt_date_from*txt_date_to*cbo_buyer_name',"../../");
			//alert(data);
			freeze_window(3);
			http.open("POST","requires/export_statement_report_urmi_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
		else if(txt_date_from=="" || txt_date_to=="")
		{
			if(form_validation('txt_date_from*txt_date_to','Date From*Date To')==false)
			{
				return;
			}
		}
		else
		{
			var data="action=report_generate&reportType="+type+get_submitted_data_string('cbo_company_name*cbo_delivery_company_name*cbo_del_floor*cbo_source*cbo_shipping_status*cbo_location_name*txt_internal_ref*txt_date_from*txt_date_to*cbo_buyer_name',"../../");
			//alert(data);
			freeze_window(3);
			http.open("POST","requires/export_statement_report_urmi_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			release_freezing();
			var response=trim(http.responseText).split("####");
			//alert(response);
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			reporttype=response[2];			
			setFilterGrid("table_body",-1,tableFilters);						
	 		show_msg('3');
		}
	}

	function new_window()
	{		
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		$('#table_body tr:first').hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title><link rel="stylesheet" href="../../css/style_print.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		$('#table_body tr:first').show();
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="225px";
	}

	function new_window_summary()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title><link rel="stylesheet" href="../../css/style_print.css" type="text/css"/></head><body>'+document.getElementById('summary').innerHTML+'</body</html>');
		d.close();
	}

	function openmypage_ex_date(company_id,order_id,item_number_id,ex_factory_date,action,challan_id,type)
	{
		var popup_width='';
		if(action=="ex_date_popup") popup_width='550px'; else popup_width='550px';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/export_statement_report_urmi_controller.php?order_id='+order_id+'&company_id='+company_id+'&ex_factory_date='+ex_factory_date+'&action='+action+'&challan_id='+challan_id+'&item_number_id='+item_number_id+'&type='+type, 'Detail View', 'width='+popup_width+', height=370px,center=1,resize=0,scrolling=0','../../');
	}

	function fnc_source(source,company)
	{
		var company_id = document.getElementById('cbo_delivery_company_name').value;
		load_drop_down( 'requires/export_statement_report_urmi_controller', source+'**'+company, 'load_drop_delivery_company', 'dev_company_td' );
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
			get_php_form_data(company,'print_button_variable_setting','requires/export_statement_report_urmi_controller' );
		}
		setTimeout[($("#dev_company_td a").attr("onclick","disappear_list(cbo_delivery_company_name,'0');load_buyer_location();") ,3000)];
		$('#cbo_location_name').val(0);
		$('#cbo_del_floor').val(0);
	}

	function getButtonSetting()
	{
		var company_id = document.getElementById('cbo_delivery_company_name').value;
		get_php_form_data(company_id,'print_button_variable_setting','requires/export_statement_report_urmi_controller' );
	}

	
	function reset_company_val()
 	{
 		 var delv_val=$("#cbo_delivery_company_name").val();
 		 if(delv_val) $("#cbo_company_name").val();
 	}

	
</script>
</head>
<body onLoad="set_hotkey();">
 <div style="width:100%;" align="center">
<form id="monthly_ex_factory" name="monthly_ex_factory">
    <div style="width:1195px;" align="center">
        <? echo load_freeze_divs ("../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:1190" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel">
            <fieldset style="width:1190px;">
                <table class="rpt_table" width="1190" border="1" align="center" rules="all">
                	<thead>
                    	<tr>
                            <th width="150">Company Name</th>
                            <th width="100">Buyer</th>
                            <th width="100">Source</th>
                            <th width="150">Delivery Company</th>
                            <th width="120">Delivery Location</th>
                            <th width="120">Floor</th>
                            <th width="80">Int. File No</th>
                            <th width="105">Shipment Status</th>
                            <th width="130" colspan="2" class="must_entry_caption">Ex-Factory Date</th>
                            <th><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:80px" onClick="reset_form('','report_container*report_container2','','','')" /></th>
                        </tr>
                     </thead>
                    <tbody>
                        <tr class="general" >
                            <td><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected,"load_drop_down( 'requires/export_statement_report_urmi_controller', this.value, 'load_drop_down_buyer', 'buyer_td' ); get_php_form_data(this.value,'print_button_variable_setting','requires/export_statement_report_urmi_controller' );" ); ?></td>
                            <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- Select --", $selected, "" ); ?></td>
                            <td><? echo create_drop_down( "cbo_source", 100, $knitting_source,"", 1, "-- Select Source --", $selected, "fnc_source(this.value,$('#cbo_company_name').val()); ", 0, '1,3' ); ?></td>
                            <td id="dev_company_td"><? echo create_drop_down( "cbo_delivery_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 0, "-- Select Delivery Company --", $selected,"load_drop_down( 'requires/export_statement_report_urmi_controller', this.value, 'load_drop_down_location', 'location' );" ); ?></td>
                            <td id="location"><? echo create_drop_down( "cbo_location_name", 120, $blank_array,"", 1, "-- Select --", $selected, "" ); ?></td>
                            <td id="del_floor_td"><? echo create_drop_down( "cbo_del_floor", 120, $blank_array,"", 1, "-- Select Delivery Floor --", $selected, "" );?></td>
                            <td> <input type="text" name="txt_internal_ref" id="txt_internal_ref" placeholder="Internal Ref." style="width:70px" class="text_boxes"></td>
                            <td id="shipment_status_td"><? echo create_drop_down( "cbo_shipping_status", 105, $shipment_status,"", 0, "-- Select --", 0, "",0,'0,2,3','','','','' ); ?></td>
                            <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" ></td>
                            <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date" ></td>
                            <td><input type="button" id="show_button" class="formbutton" style="width:80px;" value="Show" onClick="fn_report_generated(1);" /></td>
                        </tr>
                        <tr>
                            <td colspan="11" align="center"><? echo load_month_buttons(1); ?></td>
                        </tr>
                    </tbody>
                </table>

            </fieldset>
        </div>
    </div>
     </form>
    <div id="report_container" align="center" style="padding:10px;"></div>
    <div id="report_container2"></div>
    <div style="display:none" id="data_panel"></div>
 </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
	set_multiselect('cbo_delivery_company_name','0','0','0','0');
	//setTimeout[($("#dev_company_td a").attr("onclick","disappear_list(cbo_delivery_company_name,'0');getCompanyId();getButtonSetting();") ,3000)];

	$("#multi_select_cbo_delivery_company_name a").click(function(){
		load_buyer_location();
 	});
	function load_buyer_location()
	{
		var company=$("#cbo_delivery_company_name").val();
		load_drop_down( 'requires/export_statement_report_urmi_controller', company, 'load_drop_down_location', 'location' );
		reset_company_val();
		getButtonSetting();
	}
</script>
</html>
