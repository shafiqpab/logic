<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create CI Statement Details Report.
Functionality	:
JS Functions	:
Created by		:	Rakib 
Creation date 	: 	20-02-2020
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
echo load_html_head_contents("Export CI Statement Details", "../../", 1, 1,$unicode,1,1);
?>

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	var tableFilters =
	 {
		// col_61: "none",
		col_operation: {
		id: ["total_ex_fact_qnty","total_invoice_qty","total_invoice_qty_pcs","total_carton_qty","value_total_avg_price","value_total_grs_value","value_total_discount_value","value_total_bonous_value","value_total_claim_value","value_total_commission_value","value_total_net_invo_value","value_total_rlz_amt"],
	   	col: [15,16,17,18,19,20,21,22,23,24,27,64],
		// col: [14,15,16,17,18,19,20,21,22,23,26,58],
	   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 }
	 var tableFilters_2 =
	 {
		col_61: "none",
		col_operation: {
		id: ["value_total_ex_fact_qnty","value_total_invoice_qty","value_total_invoice_qty_pcs","value_total_net_invo_value","value_total_rlz_amt","value_total_rlz_dist","value_total_rlz_deduct"],
	   col: [6,7,8,10,17,18,19],
	   operation: ["sum","sum","sum","sum","sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 }

	var permission = '<? echo $permission; ?>';

	function generate_report(RptType)
	{
		var cbo_company_name=$("#cbo_company_name").val();
		//alert(cbo_company_name);return;
		var cbo_based_on=$('#cbo_based_on').val();
		var cbo_buyer_name=$('#cbo_buyer_name').val();
		var cbo_lien_bank=$('#cbo_lien_bank').val();
		var cbo_location=$('#cbo_location').val();
		var forwarder_name=$('#forwarder_name').val();
		var txt_invoice_no=$('#txt_invoice_no').val();
		var txt_lc_sc_no=$('#txt_lc_sc_no').val();
		var cbo_ascending_by=$('#cbo_ascending_by').val();
		if (cbo_based_on==8 || cbo_based_on==10) 
		{
			if(cbo_buyer_name>0 || cbo_lien_bank>0 || forwarder_name>0 || txt_invoice_no!="" || txt_lc_sc_no!="")
			{
				if(form_validation('cbo_company_name','Company Name')==false)
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
		}
		else
		{
			if(cbo_buyer_name>0 || cbo_lien_bank>0 || forwarder_name>0 || txt_invoice_no!="" || txt_lc_sc_no!="")
			{
				if(form_validation('cbo_company_name','Company Name')==false)
				{
					return;
				}
			}
			else
			{
				if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Form Date*To Date')==false)
				{
					return;
				}
			}
		}	

		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string("cbo_buyer_name*cbo_lien_bank*cbo_location*forwarder_name*shipping_mode*cbo_based_on*txt_date_from*txt_date_to*txt_invoice_no*txt_lc_sc_no*cbo_ascending_by","../../")+'&report_title='+report_title+'&RptType='+RptType+'&cbo_company_name='+cbo_company_name;
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/export_ci_statement_details_controller.php",true);
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
			if(response[1] == 1 || response[1] == 2 || response[1] == 3 || response[1] == 4 || response[1] == 5)
			{
				document.getElementById('report_container').innerHTML='<a href="requires/'+response[2]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				setFilterGrid("table_body",-1,tableFilters);
				release_freezing();
				return;
			}
			else
			{
				document.getElementById('report_container').innerHTML=report_convert_button('../../');
				append_report_checkbox('table_header_1',1);
			}

			if(response[1]==1 || response[1]==2 || response[1]==3)
			{
				//setFilterGrid("table_body",-1,tableFilters);
			}
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		// $(".flt").css("display","none");
		$('#table_body tr:first').hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="290px";
        // $(".flt").css("display","block");
		$('#table_body tr:first').show();
	}
	

	function openmypage(invoice_id,k)
	{
		page_link='requires/export_ci_statement_details_controller.php?action=po_id_details'+'&invoice_id='+invoice_id+'&k='+k;;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'PO Info', 'width=980px,height=350px,center=1,resize=0,scrolling=0','../');
		emailwindow.onclose=function()
		{
			//alert("Jahid");
		}
	}

	function generate_ex_factory_popup(action,country_id,id,width)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/export_ci_statement_details_controller.php?action='+action+'&country_id='+country_id+'&id='+id, 'Ex-Factory Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../');
	}

	function lc_sc_no_auto_com()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else
		{
			var company_id=$('#cbo_company_name').val();
			var all_code=trim(return_global_ajax_value( company_id, 'lc_sc_no_auto_com', '', 'requires/export_ci_statement_details_controller'));
			str_code =eval(all_code);
			$("#txt_lc_sc_no").autocomplete({
				source: str_code
			});
		}
	}


	function openmypage_file(i,type)
	{
		var invoice_id=$('#sysid_'+i).val();
		var issue_id=$('#txtissueid_'+i).val();
		if(type==1)
		{
			var page_link='requires/export_ci_statement_details_controller.php?action=show_file&invoice_id='+invoice_id; 
		}
		else
		{
			var page_link='requires/export_ci_statement_details_controller.php?action=qc_show_file&issue_id='+issue_id; 	
		}
		var title="File View";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=250px,center=1,resize=0,scrolling=0','../')
	}


</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",''); ?>
    <form id="frm_lc_salse_contact" name="frm_lc_salse_contact">
    <div style="width:1360px;">
    <h3 align="left" id="accordion_h1" style="width:1360px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div id="content_search_panel">
    <fieldset style="width:1360px;">
        <table class="rpt_table" cellspacing="0" cellpadding="0" width="1360" border="1" rules="all">
            <thead>
                <th width="140" class="must_entry_caption">Company</th>
                <th width="140">Buyer</th>
                <th width="130">Lien Bank</th>
                <th width="130">Location</th>
                <th width="100"> C.& F. </th>
                <th width="100"> Ship Mode </th>
                <th width="120">Based On</th>
                <th width="150" class="must_entry_caption">Date Range</th>
                <th width="60">Ascending By</th>
                <th width="70">Invoice No</th>
                <th width="80">LC/SC No</th>
                <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('frm_lc_salse_contact','report_container*report_container2','','','')" /></th>
            </thead>
            <tbody>
                <tr>
                    <td>
                    <?
                        echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", "", "", $selected, "load_drop_down( 'requires/export_ci_statement_details_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                    ?>
                    </td>
                    <td id="buyer_td"><?
                        //echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                        echo create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by  buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );
                    ?></td>
                    <td>
                    <?
                        echo create_drop_down( "cbo_lien_bank", 130, "select (bank_name||' ('||branch_name||')') as bank_name,id from lib_bank where is_deleted=0 and status_active=1 and lien_bank=1 order by bank_name","id,bank_name", 1, "-- All Lien Bank --", 0, "" );
                    ?>
                    </td>
                    <td>
                    <?
                        echo create_drop_down( "cbo_location", 130, "select id,location_name from lib_location","id,location_name", 1, "-- Select Location --", $selected,"",0,"" );
                    ?>
                    </td>
                    <td>
                    <?
                        echo create_drop_down( "forwarder_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (30,32)) group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "--Select Frowarder--", $selected, "" );
                    ?>
                    </td>
                    <td>
						<?
                            echo create_drop_down( "shipping_mode", 100, $shipment_mode,"", 1, "-- Select --", 0, "" );
                        ?>
                    </td>
                    <td>
                    <?
                    $based_on_arr=array(1=>"Invoice Date",2=>"Exfactory Date",3=>"Actual Date",4=>"BL/Cargo Date",5=>"Shipping Bill Date",6=>"Realization Date",7=>"Insert Date",8=>"Un-Realization",9=>"Bank Submission Date",10=>"Un-Submitted Invoice");
                        echo create_drop_down( "cbo_based_on", 120, $based_on_arr,"", 1, "------ Select ------", 0);
                    ?>
                    </td>
                    <td>
                    	<input name="txt_date_from" id="txt_date_from" class="datepicker"  style="width:60px" placeholder="From Date" readonly>
                    	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"placeholder="To Date" readonly>
                    </td>
					<td><? 
						$ascending_by_array = array(0=> "--Select--", 1=>"Invoice No", 2=>"Invoice Date", 3=>"Exp Form No", 4=>"Exp Form Date");
						echo create_drop_down("cbo_ascending_by", 60, $ascending_by_array, "", 0, "----- Select -----",0); ?></td>
                    <td>
                    	<input type="text" id="txt_invoice_no" name="txt_invoice_no" class="text_boxes" style="width:70px;" placeholder="Write" >
                    </td>
                    <td>
                    	<input type="text" id="txt_lc_sc_no" name="txt_lc_sc_no" class="text_boxes" style="width:70px;" onFocus="lc_sc_no_auto_com()" placeholder="Write" >
                    </td>
                    <td align="center">
                    <input type="button" name="search" id="search_1" value="Show" onClick="generate_report(1)" style="width:60px" class="formbutton" />                             
                    </td>
                </tr>
                <tr>
                    <td colspan="10" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>    
                </tr>
            </tbody>
        </table>
    </fieldset>
    </div>
    </div>
    <div id="report_container" align="center" style="padding: 10px;"></div>
    <div id="report_container2"></div>
    </form>
    </div>
</body>
<script>
	set_multiselect('cbo_company_name','0','0','0','0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>