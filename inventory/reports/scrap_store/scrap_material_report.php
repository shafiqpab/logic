<?php
/*-------------------------------------------- Comments
Purpose			: 	This form will create Scrap Material Report
				
Functionality	:	
JS Functions	:
Created by		:	Sapayth
Creation date 	: 	14-11-2020
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
echo load_html_head_contents('Scrap Material Report', '../../../', 1, 1, $unicode, 1, 1); 
?>	
<script>
	function openmypage_item_account()
	{
		if( form_validation('cbo_company_id*cbo_item_category','Company Name*Item Category')==false )
		{
			return;
		}
		 var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_item_category').value+"_"+document.getElementById('txt_item_acc').value+"_"+document.getElementById('txt_product_id_des').value;
		 //alert(data);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/scrap_material_report_controller.php?action=item_account_popup&data='+data, 'Item Account Popup', 'width=510px,height=400px,center=1,resize=0', '../../')
		
		emailwindow.onclose=function()
		{

			var item_account_val=this.contentDoc.getElementById("item_account_val").value;
			document.getElementById("txt_item_acc").value=item_account_val;
			var item_account_id=this.contentDoc.getElementById("item_account_id").value;
			document.getElementById("txt_product_id_des").value=item_account_id;
		}
	}

	function loadStore() {
		var companyIds = document.getElementById('cbo_company_id').value;
		var categoryTypes = document.getElementById('cbo_item_category').value;
		var data = companyIds + '_' + categoryTypes;

		load_drop_down('requires/scrap_material_report_controller', data, 'load_drop_down_store', 'td_store');
		set_multiselect('cbo_store_name', '0', '0', '', '0');
	}

	function generate_report(rpt_type)
	{

		var report_title=$( "div.form_caption" ).html(); 
		var cbo_company_id = $("#cbo_company_id").val();
		var cbo_mat_placement = $("#cbo_mat_placement").val();
		var txt_product_id_des = $("#txt_product_id_des").val();
		// var txt_product_id = $("#txt_product_id").val();
		var cbo_item_category = $("#cbo_item_category").val();
		var cbo_year = $("#cbo_year").val();
		var txt_trans_ref_no = $("#txt_trans_ref_no").val();
		var from_date = $("#txt_date_from").val();
		var to_date = $("#txt_date_to").val();
		var cbo_year = $("#cbo_year_selection").val();
		var cbo_receive_basis = $("#cbo_receive_basis").val();
		var cbo_store_name = $("#cbo_store_name").val();
		// var cbo_search_by_zero = $("#cbo_search_by_zero").val();
		if(txt_product_id_des !='' ||  cbo_receive_basis !='' ||  txt_trans_ref_no !='')
		{
			if( !form_validation('cbo_company_id*cbo_item_category','Company Name*Item Category') )
			{
				return;
			}
		}else{
			if( !form_validation('cbo_company_id*cbo_item_category*txt_date_from*txt_date_to','Company Name*Item Category*Start Date*End Date') )
			{
				return;
			}
		}
		
		var dataString = "&cbo_company_id="+cbo_company_id+"&cbo_item_category="+cbo_item_category+"&cbo_receive_basis="+cbo_receive_basis+"&cbo_year="+cbo_year+"&txt_trans_ref_no="+txt_trans_ref_no+"&cbo_mat_placement="+cbo_mat_placement+"&from_date="+from_date+"&to_date="+to_date+"&txt_product_id_des="+txt_product_id_des+"&cbo_store_name="+cbo_store_name+"&report_title="+report_title+"&rpt_type="+rpt_type;
		//alert(dataString);
		var data="action=generate_report_"+rpt_type+dataString;
		//alert (data);
		// console.log(data);
		freeze_window(5);
		http.open("POST","requires/scrap_material_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_response;  
	}

	function generate_report_response() {
		if(http.readyState == 4) 
		{	 
	
			var response=trim(http.responseText).split("**");
			// alert(response);
			$("#report_container2").html(response[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

			if(response[2]==2){
				var tableFilters = {
					col_operation: {
					id: ['preRcvGross', 'todayRcvGross', 'totalRcvGross', 'preIssueGross', 'totalSalesGross', 'totalDisposalGross', 'totalIssueGross', 'totalBalanceGross', 'totalSalesValueGross'],
					col:  [13,14,15,16,17,18,19,20,22],
					operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum"],
					write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				}
			}
			if(response[2]==3){
				var tableFilters = {
					col_operation: {
					id: ['totalPreRcv', 'totalCurrentRcv', 'GrandTotalRcv', 'totalPreIssue', 'totalCurrentIssue', 'GrandTotalIssue', 'totalBalance', 'totalAvgRate', 'totalSalesAmount'],
					col:  [9, 10, 11, 12, 13, 14, 15, 16, 17],
					operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum"],
					write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				}
			}
			
			setFilterGrid('report_body', -1, tableFilters);

			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		// $("#table_body tr:first").hide();
		$('.fltrow').hide(); 
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		// $("#table_body tr:first").show();
		$('.fltrow').show(); 

		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="250px";
	}

	function report_popup(garmentsItemId, type, mst_id, product_id, title, popup_width) {
		//alert(prod_id);
		var action = '';
		if (type == 1) {
			switch(garmentsItemId) {
				case 1: 	// yarn
					action = 'total_yarn_receive_popup';
					break;
				case 2: 	// Knit Finish Fabrics
					action = 'total_finish_fabric_receive_popup';
					break;
				case 13: 	// Grey Fabric(Knit)
					action = 'total_grey_fabric_receive_popup';
					break;
			}
		} else {
			switch(garmentsItemId) {
				case 1: 	// yarn
					action = 'total_yarn_issue_popup';
					break;
				case 2: 	// Knit Finish Fabrics
					action = 'total_finish_fabric_issue_popup';
					break;
				case 13: 	// Grey Fabric(Knit)
					action = 'total_grey_fabric_issue_popup';
					break;
			}
		}
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/scrap_material_report_controller.php?action='+action+'&mst_id='+mst_id+'&product_id='+product_id, title, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '../../');
	}

	function total_report_popup(garmentsItemId, type, product_id, title, popup_width) 
	{
		//alert(prod_id);
		var action = '';
		if (type == 1) {
			action = 'total_receive_popup';
		} else {
			action = 'total_issue_popup';
		}
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/scrap_material_report_controller.php?action='+action+'&garmentsItemId='+garmentsItemId+'&product_id='+product_id, title, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '../../');
	}
</script>
</head>
<body>
    <div style="width:100%;" align="center">
    	<?php echo load_freeze_divs('../../../', $permission); ?> 
    	<!-- <form name="stockMaterialReport_1" id="stockMaterialReport_1" autocomplete="off" > 
        <h3 style="width:1140px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
	        <div id="content_search_panel" style="width:1140px" >      
	            <fieldset>  
	                <table class="rpt_table" width="1130" cellpadding="0" cellspacing="0" border="1" rules="all">
	                    <thead>
	                        <th class="must_entry_caption">Company</th>
	                        <th>Item Category</th>
	                        <th>Item Description</th>
	                        <th>Method</th>
	                        <th>Store Name</th>
	                        <th class="must_entry_caption" colspan="2">Date</th>
	                        <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('stockMaterialReport_1','report_container*report_container','','','')" /></th>
	                    </thead>
	                    <tbody>
	                        <tr class="general">
	                            <td>
	                                <?php 
	                                    // echo create_drop_down( "cbo_company_id", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "" );
	                                ?>                            
	                            </td>
	                            <td>
	                                <?php
	                                   // echo create_drop_down( "cbo_item_category", 120, $item_category,"", 1, "--Select Category--", "", "" );
	                                ?>
	                           </td>
	                            <td>
	                            	<input style="width:120px;" name="txt_item_acc" id="txt_item_acc" class="text_boxes" onDblClick="openmypage_item_account()" placeholder="Browse" readonly />
	                            </td>
	                            <td>
	                            	<input type="text">
	                            </td>
	                            <td>
	                            	<input type="text">
	                            </td>
	                            <td>
	                                <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px;" readonly />                    							
	                           </td>
	                           <td>
	                                <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px;" readonly />                        
	                            </td>
	                            <td>
	                                <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />
	                            </td>
	                        </tr>
	                    </tbody>
	                    <tfoot>
	                        <tr>
	                            <td colspan="9" align="center"><?php // echo load_month_buttons(1);  ?></td>
	                        </tr>
	                    </tfoot>
	                </table> 
	            </fieldset> 
	        </div>
        </form> -->
        <form name="stockMaterialReport_2" id="stockMaterialReport_2" autocomplete="off" > 
        <h3 style="width:1320px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')">-Search Panel</h3> 
	        <div id="content_search_panel" style="width:1320px" >      
	            <fieldset>  
	                <table class="rpt_table" width="1300" cellpadding="0" cellspacing="0" border="1" rules="all">
	                    <thead>
	                        <th class="must_entry_caption">Company</th>
	                        <th class="must_entry_caption">Item Category</th>
	                        <th>Item Description</th>
	                        <th>Rcv Basis</th>
	                        <th>Transaction Ref No</th>
	                        <th>Store Name</th>
	                        <th>Material Placement</th>
	                        <th class="must_entry_caption" colspan="2">Date</th>
	                        <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('stockMaterialReport_2','report_container*report_container2','','','')" /></th>
	                    </thead>
	                    <tbody>
	                        <tr class="general">
	                            <td id="td_company">
	                                <?php 
	                                    echo create_drop_down( 'cbo_company_id', 170, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name", 'id,company_name', 0, '', $selected, '' );
	                                ?>                            
	                            </td>
	                            <td id="td_item_category">
	                                <?php
	                                   echo create_drop_down( 'cbo_item_category', 170, $item_category, '', 0, '', '', '' );
	                                ?>
	                           </td>
	                            <td>
	                            	<input style="width:120px;" name="txt_item_acc" id="txt_item_acc" class="text_boxes" onDblClick="openmypage_item_account()" placeholder="Browse" readonly />
	                            	<input type="hidden" name="txt_product_id_des" id="txt_product_id_des" style="width:90px;"/>
	                            </td>
	                            <td>
	                            	<?php
                                        $scrap_receive_basis_arr = array(1=>'Receive-Reject', 2=>'Issue-Damage', 3=>'Issue-Scrape Store',4=>'Independent');
                                        echo create_drop_down( 'cbo_receive_basis', 120, $scrap_receive_basis_arr, '', 1, '-- Select --', $selected, 0, '' );
                                    ?>
	                            </td>
	                            <td>
	                            	<input style="width:100px;" name="txt_trans_ref_no" id="txt_trans_ref_no" class="text_boxes" />
	                            </td>
	                            <td id="td_store">
	                            	<?php
	                            		echo create_drop_down( 'cbo_store_name', 120, $blank_array, '', 0, '', '', '' );
	                            	?>
	                            </td>
	                            <td id="td_mat_placement">
	                                <?php
	                                	$material_placement = array(1=>'Top Floor', 2=>'Bulding Side', 3=>'Old Store', 4=>'Tin Shade');
	                                	echo create_drop_down( 'cbo_mat_placement', 120, $material_placement, '', 0, '', '', '' );
	                                ?>
	                        	</td>
	                            <td>
	                                <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px;" readonly />                    							
	                           </td>
	                           <td>
	                                <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px;" readonly />                        
	                            </td>
	                            <td>
	                                <input type="button" name="search" id="search" value="Show 2" onClick="generate_report(2)" style="width:70px" class="formbutton" />
	                                <input type="button" name="search" id="search" value="Show 3" onClick="generate_report(3)" style="width:70px" class="formbutton" />
	                            </td>
	                        </tr>
	                    </tbody>
	                    <tfoot>
	                        <tr>
	                            <td colspan="9" align="center"><?php echo load_month_buttons(1); ?></td>
	                        </tr>
	                    </tfoot>
	                </table> 
	            </fieldset> 
	        </div>
        </form>
        <div class="report-area">
        	<div class="category-report-area">
        		<div id="category_report_1" align="center">
        			<table>
        				<tr>
        					<th></th>
        				</tr>
        			</table>
        		</div>
        		
        	</div>
			
        	<div id="report_container" style="margin-top: 20px;"></div>
			<div id="report_container2" align="center"></div>
        </div>
    </div>
</body>
<script>
	set_multiselect('cbo_company_id', '0', '0', '', '0');
	set_multiselect('cbo_item_category', '0', '0', '', '0');
	set_multiselect('cbo_store_name','0','0','','0');
	set_multiselect('cbo_mat_placement','0','0','','0');
	// set_multiselect('cbo_working_floor_id','0','0','','0');

	setTimeout[($("#td_company a").attr("onclick","disappear_list(cbo_company_id,'0');loadStore();"), 3000)];
	setTimeout[($("#td_item_category a").attr("onclick","disappear_list(cbo_item_category,'0');loadStore();"), 3000)];
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>