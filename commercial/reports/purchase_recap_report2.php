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
	
	function generate_report()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}

		if ( $("#txt_pi_no").val() != '' && ( $("#txt_date_from").val() || $("#txt_date_to").val() || $("#txt_wo_po_no").val() || $("#txt_req_no").val() ) )
		{
			alert("Only PI Number Allowed");
			return;
		}	

		if($("#cbo_supplier").val() !=0 && $("#cbo_date_type").val() ==1 && $("#txt_date_from").val() && $("#txt_date_to").val() )
		{
			alert("Supplier and Requisition Date together Not Allowed");
			return;
		}

		if($("#cbo_supplier").val() !=0 && $("#txt_req_no").val())
		{
			alert("Supplier and Requisition No together Not Allowed");
			return;
		}

		if($("#txt_wo_po_no").val() != "" && $("#cbo_date_type").val() ==1 && $("#txt_date_from").val() && $("#txt_date_to").val())
		{
			alert("Work Order and Requisition Date together Not Allowed");
			return;
		}
		

		if($("#cbo_supplier").val() == 0 && $("#txt_req_no").val() == "" && $("#txt_wo_po_no").val() == "" && $("#txt_pi_no").val() == "")
		{
			if(form_validation('txt_date_from*txt_date_to','From Date*To Date')==false)
			{
				return;
			}
		}

        if($("#txt_req_no").val() != "" && $("#cbo_date_type").val() == 2 && $("#txt_wo_po_no").val() == "" && $("#txt_date_from").val() != "" && $("#txt_date_to").val() != "" )
        {
            alert("Without Work Order Number Purchase Order Date Not Allowed");
            return;
        }

        var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_item_category_id*txt_req_no*txt_date_from*txt_date_to*cbo_supplier*txt_wo_po_no*txt_pi_no*cbo_date_type*cbo_surch_type',"../../")+'&report_title='+report_title;
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/purchase_recap_report_controller2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("****");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			//setFilterGrid("table_body",-1,tableFilters);
			
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";
		
		//$("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');// media="print"
		d.close();
		
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="300px";
		//$("#table_body tr:first").show();

	}
	
	function openmypage_popup(wo_pi_req,prod_id,page_title,action)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/purchase_recap_report_controller2.php?wo_pi_req='+wo_pi_req+'&prod_id='+prod_id+'&action='+action, page_title, 'width=720px,height=400px,center=1,resize=0,scrolling=0','../');
	}

	function change_date_caption(id)
	{
		if (id==1) { $("#dynamic_caption").html("Requisition Date"); }
		else if (id==2) { $("#dynamic_caption").html("Purchase Order Date"); }
		else if (id==3) { $("#dynamic_caption").html("PI Date"); }
		else if (id==4) { $("#dynamic_caption").html("Import LC Date"); }
	}

</script>

</head>

<body onLoad="set_hotkey();">
<form id="PurchaseRecap_report">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",''); ?>
        <h3 align="left" id="accordion_h1" style="width:1160px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
            <fieldset style="width:1160px;">
                <table class="rpt_table" width="1150" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">
                	<thead>
                    	<tr>
                        	<th colspan="5" style="text-align:right;">Search Type:</th>
                            <th>
                            <?
								$search_type_arr=array(1=>"Exact", 2=>"Starts with", 3=>"Ends with", 4=>"Contents");
							   	echo create_drop_down( "cbo_surch_type", 100, $search_type_arr,"", 1,"-- Searching Type --",1,"" );
 							?>
                            </th>
                            <th colspan="3"></th>
                        </tr>
                   		<tr>                    
                            <th class="must_entry_caption" width="150">Company Name</th>
                            <th width="100">Supplier</th>
                            <th width="150">Item Category</th>
                            <th width="100">Requisition No</th>
                            <th width="100">PO/WO No</th>
                            <th width="100">PI No</th>
                            <th width="110">Date Type</th>
                            <th width="230" id="dynamic_caption">Requisition Date</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('PurchaseRecap_report','report_container*report_container2','','','')" /></th>
                        </tr>
                     </thead>
                    <tbody>
                        <tr class="general">
                            <td align="center"> 
								<?
                                echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/purchase_recap_report_controller2', this.value, 'load_drop_down_supplier', 'supplier_td' );" );
								//function create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name )
                                ?>
                            </td>
                            <td id="supplier_td"> 
						  	<?
							   	echo create_drop_down( "cbo_supplier", 100, $blank_array,"", 1,"-- Select --",0,"" );
 							?>
							</td>
                            <td align="center" id="category_td">
                            	<? 
                            		echo create_drop_down( "cbo_item_category_id", 150, $item_category,'', 1, '-- Select --',0,"",0,'','','','1,2,3,12,13,14,24,25,28,30'); //1,2,3,4,12,13,14,24,25,28,30 (was not showing) 
                            	?>
                            </td>
                            <td align="center">
                            	<input type="text" name="txt_req_no" id="txt_req_no" value="" class="text_boxes" style="width:100px"/>
                            </td>
                            <td align="center">
                            	<input type="text" name="txt_wo_po_no" id="txt_wo_po_no" value="" class="text_boxes" style="width:100px"/>
                            </td>
                            <td>
								<input type="text" id="txt_pi_no" name="txt_pi_no" class="text_boxes" style="width:100px;" placeholder="Write"/>
							</td>
                            <td>
                            	<?
                            		$date_type_arr=array(1=>"Requisition No",2=>"Purchase Order",3=>"PI",4=>"Import LC");
									echo create_drop_down( "cbo_date_type", 110, $date_type_arr,"", 0, "--Select Date--", 0,"change_date_caption(this.value)" );
                            	?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:80px" placeholder="From Date"/>
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:80px" placeholder="To Date"/>
                            </td>
                            <td>
                            	<input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:70px" class="formbutton" />
                            </td>
                        </tr>
                        <tr>
                        	<td colspan="9" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
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
