<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create General Work Order Report.
Functionality	:	
JS Functions	:
Created by		:	Abdul Barik Tipu 
Creation date 	: 	07-05-2020
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

//----------------------------------------------------------------------------------------------------------
echo load_html_head_contents("General Work Order Report", "../../", 1, 1, $unicode,1,1);
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
 
	var permission = '<? echo $permission; ?>';
	
	var tableFilters = { 
		col_80: "none", 
		col_operation: {
			id: ["value_total_req_qnty","value_total_wo_qnty","value_total_wo_amt","value_total_wo_balance"],
			col: [7,11,13,15],
			operation: ["sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	
	function generate_report(rep_type)
	{
		var supplier_id=$("#cbo_supplier").val();
		var cbo_insert_by=$("#cbo_insert_by").val();
		var txt_req_no=$("#txt_req_no").val();
		var txt_wo_no=$("#txt_wo_no").val();
		var item_category_id=$("#cbo_item_category_id").val();
		
		if(txt_req_no =="" && txt_wo_no =="" )
		{
			if(form_validation('txt_date_from*txt_date_to','From Date*To Date')==false)
			{
				return;
			}
		}
		/*else
		{
			if(form_validation('cbo_company_name','Company Name')==false)
			{
				return;
			}
		}*/

		var action='';
		if(rep_type==1){
			action="report_generate";
		}
		/* else if(rep_type==2){
			action="report_generate_trims";
		}else{
			action="report_generate_woven";
		} */
		
		var report_title=$( "div.form_caption" ).html();	
		var data="action="+action+get_submitted_data_string('cbo_company_name*cbo_item_category_id*txt_req_no*txt_date_from*txt_date_to*cbo_supplier*txt_wo_no*cbo_insert_by',"../../")+'&report_title='+report_title;
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/general_work_order_report_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window();" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			setFilterGrid("table_body",-1,tableFilters);
			//append_report_checkbox('table_header_1',1);

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
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="450px";
		
		$("#table_body tr:first").show();
	}

	function fn_load_supplier()
	{
		load_drop_down( 'requires/general_work_order_report_controller',  $('#cbo_company_name').val(), 'load_drop_down_supplier', 'supplier_td' );
		set_multiselect('cbo_supplier','0','0','','0','fn_load_category()');
	}

	function fn_load_category()
	{
		load_drop_down( 'requires/general_work_order_report_controller',$('#cbo_supplier').val(), 'load_drop_down_category', 'category_td' );
		set_multiselect('cbo_item_category_id','0','0','','0');
	}

</script>
</head>

<body onLoad="set_hotkey();">
<? echo load_freeze_divs ("../../",''); ?>
<form id="generalWorkOrderReport">
    <div style="width:100%;" align="center">
        <h3 align="left" id="accordion_h1" style="width:1050px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
            <fieldset style="width:1050px;">
                <table class="rpt_table" width="1050" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">
                	<thead>
                   		<tr>                    
                            <th width="160">Company Name</th>
                            <th width="160">Supplier</th>
                            <th width="160">Item Category</th>
                            <th width="90">Requisition No</th>
                            <th width="90">WO No</th>
                            <th width="100">Insert By</th>
                            <th width="170" class="must_entry_caption" id="dynamic_caption">WO Date Range</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('generalWorkOrderReport','report_container*report_container2','','','')" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td align="center"> 
								<?
                                echo create_drop_down( "cbo_company_name", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "fn_load_supplier();fn_load_category();" );
                                ?>
                            </td>
                            <td id="supplier_td"> 
						  	<?
							   	echo create_drop_down( "cbo_supplier", 160, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1,"-- Select --",$selected,"" );
 							?>
							</td>
                            <td align="center" id="category_td">
                            	<?
                            		echo create_drop_down( "cbo_item_category_id", 160, $item_category,'', 1, '-- Select --',0,"",0,'','','','1,2,6,12,13,14,23,24,25,28,30');
                            	?>
                            </td>
                            <td align="center">
                            	<input type="text" name="txt_req_no" id="txt_req_no" value="" class="text_boxes" style="width:80px" placeholder="Write" />
                            </td>
                            <td align="center">
                            	<input type="text" name="txt_wo_no" id="txt_wo_no" value="" class="text_boxes" style="width:80px" placeholder="Write" />
                            </td>
                            <td>
                            	<?
									$sql = "select id, user_name, user_full_name from user_passwd where 1=1 and designation=1181 order by user_name";
									echo create_drop_down( "cbo_insert_by", 100, $sql,"id,user_name", 1, "--Select User--", 0,"" );
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
                        	<td colspan="9" align="center" width="95%">
                                <? echo load_month_buttons(1); ?>&nbsp;&nbsp;
                            </td>
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
<script>
	set_multiselect('cbo_supplier','0','0','','0','fn_load_category()');
	set_multiselect('cbo_item_category_id','0','0','','0');
</script>
</html>
