<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Closing Stock Report
				
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	21-01-2014
Updated by 		: 		
Update date		:  Jahid 31-03-2015	   
QC Performed BY	:		
QC Date			:	
Comments		: stock mis mach
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Closing Stock Report","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	var tableFilters = 
	{
		//col_16: "none",
		col_operation: {
		id: ["tot_opening_bal","tot_purchuse","tot_issue_return","tot_trans_in","tot_receive","tot_issue","tot_receive_return","tot_trans_out","tot_total_issue","total_closing_stock"],
		col: [7,8,9,10,11,12,13,14,15,16],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 
        
    var tableFilters2 = 
	{
		//col_16: "none",
		col_operation: {
		id: ["tot_opening_bal","tot_opening_value","tot_purchuse","tot_issue_return","tot_receive","tot_issue","tot_receive_return","tot_total_issue","total_closing_stock","tot_closing_stock"],
		col: [8,9,10,11,12,14,15,16,18],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 

	var tableFilters8 = 
	{
		//col_16: "none",
		col_operation: {
		id: ["tot_opening_bal","tot_purchuse","tot_issue_return","tot_trans_in","tot_receive","tot_issue","tot_receive_return","tot_trans_out","tot_total_issue","tot_closing_stock"],
		col: [10,11,12,13,14,15,16,17,18,19],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 
	var tableFilters9 = 
	{
		//col_16: "none",
		col_operation: {
		id: ["tot_grand_opening","tot_grand_recv","tot_grand_trans_in","tot_grand_iss_return","tot_grand_recv_total","tot_grand_issue","tot_grand_trans_out","tot_grand_recv_return","tot_grand_other_issue","tot_grand_issue_total","tot_grand_closing_value","tot_grand_pre_issue"],
		col: [2,3,4,5,6,7,8,9,10,11,12,13],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 
	
	function openmypage_item_account()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		 var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_item_category_id').value+"_"+document.getElementById('txt_item_acc').value+"_"+document.getElementById('txt_product_id_des').value+"_"+document.getElementById('txt_item_account_no').value;
		 //alert(data);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/closing_stock_report_controller.php?action=item_account_popup&data='+data,'Item Account Popup', 'width=710px,height=420px,center=1,resize=0','../../')
		
		emailwindow.onclose=function()
		{
			var item_account_id=this.contentDoc.getElementById("txt_selected_id").value;
			var item_account_val=this.contentDoc.getElementById("txt_selected").value;
			var item_account_no=this.contentDoc.getElementById("txt_selected_no").value;
			document.getElementById("txt_product_id_des").value=item_account_id;
			document.getElementById("txt_item_acc").value=item_account_val;
			document.getElementById("txt_item_account_no").value=item_account_no;
		}
	}
	
	function generate_report(operation)
	{
		if(operation == 5)
		{
			if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date Form*Date To')==false )
			{
				return;
			}
		}
		else
		{
			if( form_validation('cbo_company_name*cbo_item_category_id*txt_date_from*txt_date_to','Company Name*Fabric Nature*Date Form*Date To')==false )
			{
				return;
			}
		}
		
		var report_title=$( "div.form_caption" ).html(); 
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_item_category_id = $("#cbo_item_category_id").val();
		var txt_product_id_des = $("#txt_product_id_des").val();
		var txt_product_id = $("#txt_product_id").val();
		var cbo_store_name = $("#cbo_store_name").val();
		var from_date = $("#txt_date_from").val();
		var to_date = $("#txt_date_to").val();
		var cbo_value_with = $("#cbo_value_with").val();
		var cbo_uom = $("#cbo_uom").val();
		var cbo_source_type = $("#cbo_source_type").val();
		
		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_store_name="+cbo_store_name+"&cbo_item_category_id="+cbo_item_category_id+"&txt_product_id_des="+txt_product_id_des+"&txt_product_id="+txt_product_id+"&from_date="+from_date+"&to_date="+to_date+"&report_title="+report_title+"&cbo_value_with="+cbo_value_with+"&report_type="+operation+"&cbo_uom="+cbo_uom+"&cbo_source_type="+cbo_source_type;
		//alert(dataString);
		var data="action=generate_report"+dataString;
		//alert (data);
		freeze_window(operation);
		http.open("POST","requires/closing_stock_report_controller.php",true);
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
			if(reponse[2] != 7)
			{
				if(reponse[2] == 3 || reponse[2] == 6)
				{
					setFilterGrid("table_body",-1,tableFilters);
				}
				else if (reponse[2] == 8) 
				{
					setFilterGrid("table_body",-1,tableFilters8);
				}
				else if (reponse[2] == 9) 
				{
					setFilterGrid("table_body",-1,tableFilters9);
				}
				else
				{
					setFilterGrid("table_body",-1,tableFilters2);
				}
			}
			
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

	function stock_qnty_popup(prod_id,item_category,batch_id)
	{
		var companyID = $("#cbo_company_name").val();
		var store_id = $("#cbo_store_name").val();
		var popup_width='600px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/closing_stock_report_controller.php?companyID='+companyID+'&prod_id='+prod_id+'&item_category='+item_category+'&store_id='+store_id+'&batch_id='+batch_id+'&action=stock_qnty_popup', 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}

	function generate_report_exel_only(excl_no)
	{
		if(excl_no==1)
		{
			if( form_validation('cbo_company_name*cbo_item_category_id*txt_date_from*txt_date_to','Company Name*Fabric Nature*Date Form*Date To')==false )
			{
				return;
			}


			var txt_product_id_des = $("#txt_product_id_des").val();
			var txt_product_id = $("#txt_product_id").val();
			var report_title=$( "div.form_caption" ).html(); 
			var cbo_company_name = $("#cbo_company_name").val();
			var cbo_item_category_id = $("#cbo_item_category_id").val();
			
			var cbo_store_name = $("#cbo_store_name").val();
			var from_date = $("#txt_date_from").val();
			var to_date = $("#txt_date_to").val();
			var cbo_value_with = $("#cbo_value_with").val();
			var cbo_uom = $("#cbo_uom").val();
			var cbo_source_type = $("#cbo_source_type").val();
			
			var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_store_name="+cbo_store_name+"&cbo_item_category_id="+cbo_item_category_id+"&txt_product_id_des="+txt_product_id_des+"&txt_product_id="+txt_product_id+"&from_date="+from_date+"&to_date="+to_date+"&report_title="+report_title+"&cbo_value_with="+cbo_value_with+"&report_type=&cbo_uom="+cbo_uom+"&cbo_source_type="+cbo_source_type;
			//alert(dataString);
			var data="action=report_generate_exel_only"+dataString;

			freeze_window(3);
			http.open("POST","requires/closing_stock_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_report_reponse_exel_only;


		}
	}

	function generate_report_reponse_exel_only()
	{
		if(http.readyState == 4) 
		{  
			var reponse=trim(http.responseText).split("####");

			if(reponse!='')
			{
				$('#aa1').removeAttr('href').attr('href','requires/'+reponse[0]);
				document.getElementById('aa1').click();
			}
			show_msg('3');
			release_freezing();
		}
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?>    		 
        <form name="closingstock_1" id="closingstock_1" autocomplete="off" > 
         <h3 style="width:1300px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:1300px" >      
            <fieldset>  
                <table class="rpt_table" width="1300" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <th width="130" class="must_entry_caption">Company</th>
                        <th width="120">Fabric Nature</th>
                        <th width="100">Fabric Source</th>
                        <th width="90">Item Description</th>
                        <th width="90">Product Id</th>
                        <th width="120">Store</th>
                        <th width="120">UOM</th>
                        <th width="100">Value</th>
                        <th class="must_entry_caption" width="170">Date</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_form('closingstock_1','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tbody>
                        <tr >
                            <td>
                                <? 
                                    echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "get_php_form_data(this.value,'print_button_variable_setting','requires/closing_stock_report_controller' );" );//load_drop_down( 'requires/closing_stock_report_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );
                                ?>                            
                            </td>
                           	<td>
								<?php 
									echo create_drop_down( "cbo_item_category_id", 120,$item_category,"", 1, "-- All --", 3, "load_drop_down( 'requires/closing_stock_report_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_store', 'store_td' );","","2,3","","","");
                                ?> 
                          	</td>
							<td>
                                <?   
                                    $source_type=array(1=>'Production',2=>'Purchase');
                                    echo create_drop_down( "cbo_source_type", 100, $source_type,"",1,"Select",0,"","","");
                                ?>
                            </td>
                            <td>
                            	<input style="width:90px;" name="txt_item_acc" id="txt_item_acc" class="text_boxes" onDblClick="openmypage_item_account()" placeholder="Browse" readonly />
                                <input type="hidden" name="txt_product_id_des" id="txt_product_id_des" style="width:90px;"/> <input type="hidden" name="txt_item_account_no" id="txt_item_account_no" style="width:90px;"/>
                            </td>
                            <td>
                                <input type="text" name="txt_product_id" id="txt_product_id" style="width:80px;" class="text_boxes" placeholder="Write"/>  
                            </td>
                           <td id="store_td">
                                <? 
                                    echo create_drop_down( "cbo_store_name", 120, $blank_array,"", 1, "--Select Store--", "", "" );
                                ?>
                           </td>
                            <td>
                                <? 
                                    echo create_drop_down( "cbo_uom", 120, $unit_of_measurement ,"", 0, "", "", "", "","1,12,23,27" );
                                ?>
                           </td>
                            <td>
                                <?   
                                    $valueWithArr=array(0=>'Value With 0',1=>'Value Without 0');
                                    echo create_drop_down( "cbo_value_with", 100, $valueWithArr,"","","",0,"","","");
                                ?>
                            </td>
                            <td>
                                <input type="text" name="txt_date_from" id="txt_date_from" value=" <? echo date("d-m-Y", time() - 86400);?> " class="datepicker" style="width:60px;" readonly />                    							
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" value=" <? echo date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:60px;" readonly />                        
                            </td>
                            <td rowspan="2">
								<span id="button_data_panel"></span>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="9" align="center">
                                <? echo load_month_buttons(1);  ?>
                               
                            </td>
                        </tr>
					</tbody>
                </table> 
            </fieldset> 
            </div>
            <br /> 
                <div id="report_container" align="center"></div>
                <div id="report_container2"></div> 
        </form>    
    </div>
</body>  
<script>
	set_multiselect('cbo_uom','0','0','','0');
</script> 
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
