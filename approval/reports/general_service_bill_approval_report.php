<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create General Service Bill Approval Report.
Functionality	:
JS Functions	:
Created by		:	Safa
Creation date 	: 	15-05-2023
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
echo load_html_head_contents("General Service Bill Approval Report", "../../", 1, 1, '', 1, 1);
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';

	var tableFilters = {
							col_operation: {
								id: ["value_total_amount"],
								col: [11],
								operation: ["sum"],
								write_method: ["innerHTML"]
							}
						}

	function fn_report_generated()
	{
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}

		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_location*cbo_year*cbo_supplier_id*txt_party_bill_no*txt_bill_no*cbo_date_type*txt_date_from*txt_date_to*cbo_type',"../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/general_service_bill_approval_report_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none" ><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;&nbsp;<input type="button" onClick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("tbl_list_search",-1, tableFilters);
			show_msg('3');
			release_freezing();
	 	}
	}


	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		$("#tbl_list_search tr:first").hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'+
	   '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="330px";

		$("#tbl_list_search tr:first").show();
	}

    function openmypage_outside_bill()
	{ 
		if( form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
	
		var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('cbo_year').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/general_service_bill_approval_report_controller.php?data='+data+'&action=outside_bill_popup','Outside Bill Popup', 'width=700px,height=400px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById('txt_bill_no');
			if (theemail.value!="")
			{
				// freeze_window(5);
				get_php_form_data( theemail.value, 'load_php_data_to_form_outside_bill', 'requires/general_service_bill_approval_report_controller');
				
				set_button_status(1, permission, 'fnc_saveUpdateDelete',1);				
				// set_all_onclick();
				release_freezing();
			}
		}
	}

	function general_service_print_report(cbo_company_name,update_id,txt_bill_no,report_title)
	{
		var report_title='General Service Bill Entry';
			print_report( cbo_company_name+'*'+update_id+'*'+txt_bill_no+'*'+report_title, "general_service_bill_entry_print", "../../subcontract_bill/outbound_billing/requires/general_service_bill_entry_controller" ) ;
		//return;
		show_msg("3");


	}
</script>
</head>
<body onLoad="set_hotkey();">
<form id="priceQuotationApprovalReport_1">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",''); ?>
         <h3 style="width:1250px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
         <div id="content_search_panel">
         <fieldset style="width:1240px;">
             <table width="100%" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="150" class="must_entry_caption">Company Name</th>
                    <th width="130">Location</th>
                    <th width="80">Year</th>
                    <th width="150">Supplier</th>
					<th width="100">Party Bill No</th>
					<th width="120">Bill No</th>
					<th width="100">Date Type</th>
                    <th width="160">Date Range</th>
                    <th width="100">Type</th>
                    <th width="80"><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('priceQuotationApprovalReport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px"/></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td>
                            <?
                                echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/general_service_bill_approval_report_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/general_service_bill_approval_report_controller',this.value, 'load_drop_supplier', 'supplier_td' );" );
                            ?>
                        </td>
                        <td align="center" id="location_td">
                            <? 
                                echo create_drop_down( "cbo_location", 130, $blank_array,"", 1,"-- Select --",0,"" );
                            ?>
                        </td>
                        <td align="center">
                            <?
                                echo create_drop_down( "cbo_year", 80, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); 
                            ?> 
                        </td>
                        <td id="supplier_td">
                            <?
                                echo create_drop_down("cbo_supplier_id", 150, $blank_array,"", 1, "-- All --", $selected, "", 0,"");
                            ?>
                        </td>
                         <td>
                            <input type="text" name="txt_party_bill_no" id="txt_party_bill_no" class="text_boxes" style="width:100px">
                        </td>
                        <td width="120">
                            <input type="hidden" name="hidden_acc_integ" id="hidden_acc_integ" />
                            <input type="hidden" name="selected_id" id="selected_id" />
                            <input type="hidden" name="update_id" id="update_id" />
                            <input type="hidden" name="variable_check" id="variable_check" />
                            <input type="hidden" name="txt_is_posted_account" id="txt_is_posted_account" />
                            <input type="text" name="txt_bill_no" id="txt_bill_no" class="text_boxes" style="width:120px" placeholder="Browse/Write" onDblClick="openmypage_outside_bill();" tabindex="1" >
                        </td>
                        <td>
                        	<?
								$search_by_arr=array(1=>"Bill Date",2=>"Approved Date");
								echo create_drop_down("cbo_date_type", 100, $search_by_arr, "", 0, "", "", "", 0);
							?>
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px;" placeholder="From Date" readonly/>
                            To
                            <input type="text" name="txt_date_to" id="txt_date_to" placeholder="To Date" class="datepicker" style="width:60px;" readonly />
                        </td>
                        <td>
                        	<?
								$search_by_arr=array(0=>"All",1=>"Pending",2=>"Partial Approved",3=>"Full Approved");
								echo create_drop_down("cbo_type", 100, $search_by_arr, "", 0, "", "3", "", 0);
							?>
                        </td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated()"/>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="8" align="center"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
    	</div>
        <div id="report_container" align="center"></div>
        <div id="report_container2" align="center"></div>
    </div>
 </form>
</body>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
