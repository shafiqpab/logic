<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create CI Statement Report.
Functionality	:
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	23-12-2013
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
echo load_html_head_contents("Monthly Bank Submission/Export Status", "../../", 1, 1,'','','');
?>

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	var tableFilters =
	 {
		col_5: "none",
		col_operation: {
		id: ["total_ex_fact_qnty","total_invoice_qty","total_invoice_qty_pcs","value_total_cm_asper_invoice_qty","total_carton_qty","value_total_avg_price","value_total_grs_value","value_total_discount_value","value_atsite_discount_amt","value_total_bonous_value","value_total_claim_value","value_total_commission_value","value_total_other_discount_value","value_total_upcharge_value","value_total_net_invo_value","value_total_carton_gross_weight","value_total_carton_net_weight","value_total_rlz_amt"],
		//    col: [14,15,16,17,18,19,20,21,22,23,  26,58],
	   col: [17,18,19,22,23,24,25,26,27,29,30,31,32,33,34,64,65,71],
	   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
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
	 
	 var tableFilters_3 =
	 {
		col_61: "none",
		col_operation: {
		id: ["value_total_total_carton_qnty","value_total_invoice_quantity","value_total_invoice_value","value_total_rlz_value","value_total_inv_discount","value_total_inv_discount"],
	   col: [6,7,8,9,17,18],
	   operation: ["sum","sum","sum","sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 }


	var permission = '<? echo $permission; ?>';

	function generate_report(RptType)
	{
		var cbo_based_on=$('#cbo_based_on').val();
		var cbo_buyer_name=$('#cbo_buyer_name').val();
		var cbo_lien_bank=$('#cbo_lien_bank').val();
		var cbo_location=$('#cbo_location').val();
		var forwarder_name=$('#forwarder_name').val();
		var txt_invoice_no=$('#txt_invoice_no').val();
		var txt_lc_sc_no=$('#txt_lc_sc_no').val();
		var cbo_ascending_by=$('#cbo_ascending_by').val();
		var txt_int_ref_no=$('#txt_int_ref_no').val();
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
			if(cbo_buyer_name>0 || cbo_lien_bank>0 || forwarder_name>0 || txt_invoice_no!="" || txt_lc_sc_no!="" || txt_int_ref_no!="")
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

		if(RptType==6 && cbo_based_on != 2)
		{
			alert("This Report Only Based On Exfactory Date");return;
		}

		if(RptType==3 && cbo_based_on == 9)
		{
			alert("Based On Bank Submission Date is applicable for only Details 4 Button");return;
		}
		if(RptType==4  && cbo_based_on == 9)
		{
			alert("Based On Bank Submission Date is applicable for only Details 4 Button");return;
		}
		// if(RptType==8 && cbo_based_on == 9)
		// {
		// 	alert("Based On Bank Submission Date is applicable for only Details 4 Button");return;
		// }
		
		/*if(RptType==1)
		{
			if(cbo_based_on==0)
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
		else
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Form Date*To Date')==false)
			{
				return;
			}
		}*/


		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string("cbo_company_name*cbo_buyer_name*cbo_lien_bank*cbo_location*forwarder_name*shipping_mode*cbo_based_on*txt_date_from*txt_date_to*txt_invoice_no*txt_lc_sc_no*cbo_ascending_by*txt_int_ref_no","../../")+'&report_title='+report_title+'&RptType='+RptType;
		// alert(data);return;
		freeze_window(3);
		http.open("POST","requires/export_ci_statement_controller.php",true);
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
			if(response[1] == 1 || response[1] == 2 || response[1] == 3 || response[1] == 4 || response[1] == 5 || response[1] == 6 || response[1] == 7)
			{
				document.getElementById('report_container').innerHTML='<a href="requires/'+response[2]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				release_freezing();
				// return;
			}
			else
			{
				document.getElementById('report_container').innerHTML=report_convert_button('../../');
				append_report_checkbox('table_header_1',1);
			}
			if(response[1]==1)
			{
				setFilterGrid("table_body",-1,tableFilters);
			}
			if(response[1]==2 || response[1]==3)
			{
				// setFilterGrid("table_body",-1,tableFilters);
				setFilterGrid("table_body",-1);
			}
			if(response[1]==6)
			{
				setFilterGrid("table_body",-1,tableFilters_3);
			}
			
			show_msg('3');
			release_freezing();
		}
	}

	function generate_report_excel(RptType)
	{
		var cbo_based_on=$('#cbo_based_on').val();
		var cbo_buyer_name=$('#cbo_buyer_name').val();
		var cbo_lien_bank=$('#cbo_lien_bank').val();
		var cbo_location=$('#cbo_location').val();
		var forwarder_name=$('#forwarder_name').val();
		var txt_invoice_no=$('#txt_invoice_no').val();
		var txt_lc_sc_no=$('#txt_lc_sc_no').val();
		var cbo_ascending_by=$('#cbo_ascending_by').val();
		var txt_int_ref_no=$('#txt_int_ref_no').val();
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
			if(cbo_buyer_name>0 || cbo_lien_bank>0 || forwarder_name>0 || txt_invoice_no!="" || txt_lc_sc_no!="" || txt_int_ref_no!="")
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

		if(RptType==6 && cbo_based_on != 2)
		{
			alert("This Report Only Based On Exfactory Date");return;
		}

		if(RptType==3 && cbo_based_on == 9)
		{
			alert("Based On Bank Submission Date is applicable for only Details 4 Button");return;
		}
		if(RptType==4  && cbo_based_on == 9)
		{
			alert("Based On Bank Submission Date is applicable for only Details 4 Button");return;
		}
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate_excel"+get_submitted_data_string("cbo_company_name*cbo_buyer_name*cbo_lien_bank*cbo_location*forwarder_name*shipping_mode*cbo_based_on*txt_date_from*txt_date_to*txt_invoice_no*txt_lc_sc_no*cbo_ascending_by*txt_int_ref_no","../../")+'&report_title='+report_title+'&RptType='+RptType;
		// alert(data);return;
		freeze_window(3);
		http.open("POST","requires/export_ci_statement_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse_excel;
	}

	function fn_report_generated_reponse_excel()
	{	
		if(http.readyState == 4)
		{
			// alert(http.responseText);
			var reponse=trim(http.responseText).split("####");
			show_msg('3');
			if(reponse[0]!='')
			{
				// alert(reponse[1]);
				$('#aa1').removeAttr('href').attr('href','requires/'+reponse[1]);
				document.getElementById('aa1').click();
			}
			show_msg('3');
			release_freezing();return;
		}
		
	}



	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$(".flt").css("display","none");

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflowY="auto";
		document.getElementById('scroll_body').style.maxHeight="400px";
        $(".flt").css("display","block");
	}
	

	function openmypage(invoice_id,k)
	{
		page_link='requires/export_ci_statement_controller.php?action=po_id_details'+'&invoice_id='+invoice_id+'&k='+k;;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'PO Info', 'width=1080px,height=350px,center=1,resize=0,scrolling=0','../');
		emailwindow.onclose=function()
		{
			//alert("Jahid");
		}
	}
	function openmypage_cm(type,data)
	{
		page_link='requires/export_ci_statement_controller.php?action=po_id_details_cm'+'&type='+type+'&data='+data;;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'PO Info', 'width=350px,height=390px,center=1,resize=0,scrolling=0','../');
		emailwindow.onclose=function()
		{
			//alert("Nayem");
		}
	}

	/*function total_value()
	{
		var tamount=0;
		var total_row=$( "#table_body tbody tr" ).length-1;
		//alert(total_row);
		for(var i=1; i<=total_row;i++)
		 {
		tamount +=$("#net_invo_value_"+i).text()*1;
		};
		$("#total_net_invo").html(tamount);
	}*/
	
	function generate_ex_factory_popup(action,country_id,id,width)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/export_ci_statement_controller.php?action='+action+'&country_id='+country_id+'&id='+id, 'Ex-Factory Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../');
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
			var all_code=trim(return_global_ajax_value( company_id, 'lc_sc_no_auto_com', '', 'requires/export_ci_statement_controller'));
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
			var page_link='requires/export_ci_statement_controller.php?action=show_file&invoice_id='+invoice_id; 
		}
		else
		{
			var page_link='requires/export_ci_statement_controller.php?action=qc_show_file&issue_id='+issue_id; 	
		}
		var title="File View";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=250px,center=1,resize=0,scrolling=0','../')
	}

	function openmypage_invoce_no(){


		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}

		var cbo_company_name=$('#cbo_company_name').val();

		page_link='requires/export_ci_statement_controller.php?action=invoice_popup_search'+'&cbo_company_name='+cbo_company_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Invoice No PopUp', 'width=900px,height=350px,center=1,resize=0,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var txt_invoice_no=this.contentDoc.getElementById("txt_invoice_no").value;
			var cbo_buyer_name=this.contentDoc.getElementById("cbo_buyer_name").value;

			document.getElementById('txt_invoice_no').value = txt_invoice_no;
			document.getElementById('cbo_buyer_name').value = cbo_buyer_name;

			$('#cbo_company_name').attr('disabled','disabled');
			$('#cbo_buyer_name').attr('disabled','disabled');

		}
	}

	function fn_print_link(action,format_id,update_id,additional_info)
	{		
		if (format_id==788){
			var cbo_buyer_name=additional_info;
			window.open('../../commercial/export_details/requires/export_information_entry_controller.php?action='+action+'&update_id='+update_id+'&cbo_buyer_name='+cbo_buyer_name);
			return;
		} else if (format_id==85 || format_id==84 || format_id==150 || format_id==791 || format_id==797){
			print_report(update_id, action, "../../commercial/export_details/requires/export_information_entry_controller");
		} else if (format_id==789){
			var data = update_id+"|"+additional_info;
			print_report(data, action, "../../commercial/export_details/requires/export_information_entry_controller");
		} else if (format_id==798){
			var data = update_id+"|"+additional_info;
			print_report(update_id, action, "../../commercial/export_details/requires/export_information_entry_controller");
		} else if (format_id==790 || format_id==792 || format_id==793 || format_id==794 || format_id==795 || format_id==796){
			var add_info=additional_info.split('_');
			if (add_info[0]==1 && add_info[1]==37)
			{
				var type=add_info[2];
				if(type==1 || type==2 || type==3 || type==4 || type==5)
				{
					var data = update_id+"**"+type;
					print_report(data, action, "../../commercial/export_details/requires/export_information_entry_controller");
				}
				if(type==6)
				{
					var r=confirm("Press  \"Cancel\"  to print Single page\nPress  \"OK\"  to print Double page");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}
					var data = update_id+"**"+type+"**"+show_item;
					print_report( data, action, "../../commercial/export_details/requires/export_information_entry_controller") ;
					
				}
			}
			else
			{
				alert("Print for only Wash Garment");
				return;
			}		
		}
	}


</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",''); ?>
    <form id="frm_lc_salse_contact" name="frm_lc_salse_contact">
    <div style="width:1500px;">
    <h3 align="left" id="accordion_h1" style="width:1500px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div id="content_search_panel">
    <fieldset style="width:1500px;">
        <table class="rpt_table" cellspacing="0" cellpadding="0" width="1500" border="1" rules="all">
            <thead>
                <th width="130" class="must_entry_caption">Company</th>
                <th width="130">Buyer</th>
                <th width="130">Lien Bank</th>
                <th width="130">Location</th>
                <th width="100"> C.& F. </th>
                <th width="100"> Ship Mode </th>
                <th width="120">Based On</th>
                <th width="140" class="must_entry_caption">Date Range</th>
                <th width="60">Ascending By</th>
                <th width="70">Invoice No</th>
                <th width="70">Int. Ref. No</th>
                <th width="80">LC/SC No</th>
                <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('frm_lc_salse_contact','report_container*report_container2','','','')" /></th>
            </thead>
            <tbody>
                <tr>
                    <td>
                    <?
                        echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/export_ci_statement_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data( this.value, 'company_wise_report_button_setting','requires/export_ci_statement_controller' );" );
                    ?>
                    </td>
                    <td id="buyer_td"><?
                        echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
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
                    	<input name="txt_date_from" id="txt_date_from" class="datepicker"  style="width:55px" placeholder="From Date" readonly>
                    	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"placeholder="To Date" readonly>
                    </td>
					<td><? 
						$ascending_by_array = array(0=> "--Select--", 1=>"Invoice No", 2=>"Invoice Date", 3=>"Exp Form No", 4=>"Exp Form Date");
						echo create_drop_down("cbo_ascending_by", 60, $ascending_by_array, "", 0, "----- Select -----",0); ?></td>
                    <td>
                    	<input ondblclick="openmypage_invoce_no();" type="text" id="txt_invoice_no" name="txt_invoice_no" class="text_boxes" style="width:70px;" placeholder="Browse/Write" >
                    </td>
                    <td>
                    	<input type="text" id="txt_int_ref_no" name="txt_int_ref_no" class="text_boxes" style="width:70px;" placeholder="Write" >
                    </td>
                    <td>
                    <!--<input type="text" id="txt_lc_sc_no" name="txt_lc_sc_no" class="text_boxes" style="width:70px;" placeholder="Write" >-->
                    <input type="text" id="txt_lc_sc_no" name="txt_lc_sc_no" class="text_boxes" style="width:70px;" onFocus="lc_sc_no_auto_com()" placeholder="Write" >
                    </td>
                    <td align="center">
                    <input type="button" name="search_1" id="search_1" value="Details" onClick="generate_report(1)" style="width:60px; display:none" class="formbutton" />
                    <input type="button" name="search_2" id="search_2" value="Short" onClick="generate_report(2)" style="width:60px; display:none" class="formbutton" />
                    <input type="button" name="search_7" id="search_7" value="Short 2" onClick="generate_report(7)" style="width:60px; display:none" class="formbutton" />
                    <input type="button" name="search_3" id="search_3" value="Details-2" onClick="generate_report(3)" style="width:60px; display:none" class="formbutton" />                    
                    </td>
                </tr>
                <tr>
					<td></td>
                    <td colspan="11" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                    <td align="center">
                    	<input type="button" name="search_4" id="search_4" value="search-3" onClick="generate_report(4)" style="width:60px; display:none" class="formbutton" />
                    	<input type="button" name="search_8" id="search_8" value="search-4" onClick="generate_report(8)" style="width:60px; display:none" class="formbutton" />
                    	<input type="button" name="search_5" id="search_5" value="Details-4" onClick="generate_report(5)" style="width:60px; display:none" class="formbutton" />
                        <input type="button" name="search_6" id="search_6" value="Details-5" onClick="generate_report(6)" style="width:60px;" class="formbutton" />
						<input type="button" name="search_1" id="search_1" value="Details Excel" onClick="generate_report_excel(1)" style="width:70px" class="formbutton" />
						<a id="aa1" style="text-decoration:none;"></a>
                    </td>
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
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
