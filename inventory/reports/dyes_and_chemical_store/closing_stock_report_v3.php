<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Closing Stock Report
				
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	21-01-2014
Updated by 		: Aziz/Jahid		
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
echo load_html_head_contents("Closing Stock Report","../../../", 1, 1, $unicode,1,1); 


?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var tableFilters = 
	{
		col_40: "none",
		col_operation: {
		id: ["value_tot_open_bl","value_tot_open_bl_amt","value_tot_purchase","value_tot_transfer_in_qty","value_tot_issue_return","value_tot_total_receive","value_tot_issue","value_tot_transfer_out_qty","value_tot_rec_return","value_tot_total_issue","value_tot_closing_stock","value_tot_stock_value","value_totalpipeLine_qty"],
		col: [11,12,13,14,15,16,17,18,19,20,21,23,27],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	
	
	var tableFilters3 = 
	{
		col_40: "none",
		col_operation: {
		id: ["value_tot_open_bl","value_tot_open_bl_amt","value_tot_purchase","value_tot_loan_rcv","value_tot_transfer_in_qty","value_tot_issue_return","value_tot_total_receive","value_tot_issue","value_tot_loan_issue","value_tot_transfer_out_qty","value_tot_rec_return","value_tot_total_issue","value_tot_closing_stock","value_tot_stock_value","value_totalpipeLine_qty"],
		col: [11,12,13,14,15,16,17,19,20,21,22,23,25,27,29],
              //col: [10,11,12,13,14,15,16,17,18,19,20,21,22,24,26],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	var tableFilter6 = 
	{
		
		col_40: "none",
		col_operation: {
		id: ["value_tot_open_bl","value_tot_open_bl_amt","value_tot_purchase","value_tot_transfer_in_qty","value_tot_issue_return","value_tot_total_receive","value_tot_issue","value_tot_transfer_out_qty","value_tot_rec_return","value_tot_total_issue","value_tot_closing_stock","value_tot_stock_value","value_totalpipeLine_qty"],
		col: [12,13,14,15,16,17,19,20,21,22,24,26,29],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	var tableFilters7 = 
	{
		col_40: "none",
		col_operation: {
		id: ["value_tot_open_bl","value_tot_open_bl_amt","value_tot_purchase","value_tot_transfer_in_qty","value_tot_issue_return","value_tot_total_receive","value_tot_issue","value_tot_transfer_out_qty","value_tot_rec_return","value_tot_total_issue","value_tot_closing_stock","value_tot_stock_value","value_totalpipeLine_qty"],
		col: [11,12,13,14,15,16,17,18,19,20,21,23,28],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	
	
	function openmypage_item_account()
	{
		 var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_item_category_id').value+"_"+document.getElementById('txt_item_group_id').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/closing_stock_report_v3_controller.php?action=item_account_popup&data='+data,'Item Account Popup', 'width=800px,height=450px,center=1,resize=0','../../')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("item_account_id");
			var theemailv=this.contentDoc.getElementById("item_account_val");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById("txt_item_account_id").value=response[0];
			    document.getElementById("txt_item_acc").value=theemailv.value;
				//reset_form();
				get_php_form_data( response[0], "item_account_dtls_popup", "requires/closing_stock_report_v3_controller" );
				release_freezing();
			}
		}
	}
	
	function openmypage_item_group()
	{
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_item_category_id').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/closing_stock_report_v3_controller.php?action=item_group_popup&data='+data,'Item Group Popup', 'width=520px,height=380px,center=1,resize=0,scrolling=0','../../')
		
		emailwindow.onclose=function()
		{
			//var theemail=this.contentDoc.getElementById("item_name_id");
			//var response=theemail.value.split('_');
			var theemail=this.contentDoc.getElementById("item_name_id");
			var theemailv=this.contentDoc.getElementById("item_name_val");
			var response=theemail.value.split('_');
			//alert (response[1]);
			if (theemail.value!="")
			{
				//freeze_window(5);
				document.getElementById("txt_item_group_id").value=response[0];
				document.getElementById("txt_item_group").value=theemailv.value;
				//release_freezing();
			}
		}
	}
	
	function generate_report(report_type)
	{
		
		var tot_dying_qnty=0;
		if(report_type == 5)
		{
			if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false )
			{
				return;
			}
			//tot_dying_qnty=prompt("Please Input Total Dyeing Production Qty: ", "");
			var from_date_arr = trim($("#txt_date_from").val()).split("-");
			var to_date_arr = trim($("#txt_date_to").val()).split("-");
			//alert(from_date_arr[1]*1+"="+to_date_arr[1]*1);
			if(from_date_arr[1]*1 != to_date_arr[1]*1)
			{
				alert("Multiple Month Not Allow");return;
			}
		}
		else
		{
			if( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
		}
		
		var report_title=$( "div.form_caption" ).html(); 
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_item_category_id = $("#cbo_item_category_id").val();
	//	var txt_product_id = $("#txt_product_id").val();
		var item_group_id = $("#txt_item_group_id").val();
		var item_account_id = $("#txt_item_account_id").val();
		//var txt_item_code = $("#txt_item_code").val();
		var cbo_store_wise = $("#cbo_store_wise").val();
		var cbo_store_name = $("#cbo_store_name").val();
		var from_date = $("#txt_date_from").val();
		var to_date = $("#txt_date_to").val();
		var value_with 	= $("#cbo_value_with").val();
		var cbo_get_upto = $("#cbo_get_upto").val();
		var txt_days = $("#txt_days").val();
		var cbo_get_upto_qnty = $("#cbo_get_upto_qnty").val();
		var txt_qnty = $("#txt_qnty").val();
		var txt_excenge_rate = $("#txt_excenge_rate").val();
		var cbo_compliance = $("#cbo_compliance").val();
		
		if(cbo_compliance>0 && report_type!=1 && report_type!=9)
		{
			alert("Zero Discharge Applicable Only For Show And Item Wise Button");return;
		}
		
		if(cbo_get_upto!=0 && txt_days*1<=0)
		{
			alert("Please Insert Days.");	
			$("#txt_days").focus();
			return;
		}
		if(cbo_get_upto_qnty!=0 && txt_qnty*1<=0)
		{
			alert("Please Insert Qty.");	
			$("#txt_qnty").focus();
			return;
		}
		
		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_store_wise="+cbo_store_wise+"&cbo_store_name="+cbo_store_name+"&cbo_item_category_id="+cbo_item_category_id+"&from_date="+from_date+"&to_date="+to_date+"&item_account_id="+item_account_id+"&item_group_id="+item_group_id+"&value_with="+value_with+"&get_upto="+cbo_get_upto+"&txt_days="+txt_days+"&get_upto_qnty="+cbo_get_upto_qnty+"&txt_qnty="+txt_qnty+"&txt_excenge_rate="+txt_excenge_rate+"&cbo_compliance="+cbo_compliance+"&report_title="+report_title+"&report_type="+report_type+"&tot_dying_qnty="+tot_dying_qnty;
		var data="action=generate_report"+dataString;
		//alert (data);
		freeze_window(3);
		http.open("POST","requires/closing_stock_report_v3_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+reponse[2]+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			if(reponse[2]==3)
			{
				setFilterGrid("table_body_id",-1,tableFilters3);
			}
			else if(reponse[2]==5)
			{
				//setFilterGrid("table_body_id",-1,tableFilters3);
			}
			else if(reponse[2]==6)
			{
				setFilterGrid("table_body_id",-1,tableFilter6);
			}
			else
			{
				var cbo_store_wise = $("#cbo_store_wise").val();
				if(cbo_store_wise==1)
				{
					setFilterGrid("table_body_id",-1,tableFilters7);
				}
				else
				{
					setFilterGrid("table_body_id",-1,tableFilters);
				}

			}
			
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window(type)
	{
		if(type == 1 ||type == 2 || type == 3 || type == 6 || type == 7 )
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$('#table_body_id tr:first').hide();
			//$('#rpt_table_header tr th:last').attr('width', 120);
			//$('#table_body_id tr td:last').attr('width', 100);
			//$('#table_body_footer tr th:last').attr('width', 120);
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			d.close(); 
			$('#table_body_id tr:first').show();
			//$('#rpt_table_header tr th:last').attr('width', '');
			//$('#table_body_id tr td:last').attr('width', '');
			//$('#table_body_footer tr th:last').attr('width', '');
			document.getElementById('scroll_body').style.overflow="scroll"; 
			document.getElementById('scroll_body').style.maxHeight="250px";
		}
		else
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			//$('#table_body_id tr:first').hide();
			//$('#rpt_table_header tr th:last').attr('width', 120);
			//$('#table_body_id tr td:last').attr('width', 100);
			//$('#table_body_footer tr th:last').attr('width', 120);
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			d.close(); 
			//$('#table_body_id tr:first').show();
			//$('#rpt_table_header tr th:last').attr('width', '');
			//$('#table_body_id tr td:last').attr('width', '');
			//$('#table_body_footer tr th:last').attr('width', '');
			document.getElementById('scroll_body').style.overflow="auto"; 
			document.getElementById('scroll_body').style.maxHeight="250px";
		}
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
	
	function fnc_pipeLine_details(prod_id,action)
	{
		//var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_item_category_id').value+"_"+document.getElementById('txt_item_group_id').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/closing_stock_report_v3_controller.php?prod_id='+prod_id+'&action='+action,'Item Pipe Line Popup', 'width=700px,height=420px,center=1,resize=0','../../');
		emailwindow.onclose=function()
		{
			
		}
	}
	
	function check_itemGroupAndAccount(cat_id)
	{
		document.getElementById('txt_item_group_id').value='';
		document.getElementById('txt_item_group').value='';
		document.getElementById('txt_item_group_id').value='';
		document.getElementById('txt_item_acc').value='';
	}
	

	function fn_store_visibility(yes_no_id)
	{
		$('#cbo_store_name').val(0);
		if(yes_no_id==2) {
			$('#cbo_store_name').attr('disabled',true);
		} else {
			$('#cbo_store_name').attr('disabled',false);
		}
	}


</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?><br />    		 
        <form name="closingstock_1" id="closingstock_1" autocomplete="off" > 
         <h3 style="width:1350px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:1350px">      
            <fieldset>  
                <table class="rpt_table" width="1350" cellpadding="0" cellspacing="0">
                    <thead>
                        <th width="130" class="must_entry_caption">Company</th>
                        <th width="120">Item Category</th>
                        <th width="100">Item Group</th>
                        <th width="100">Item Account</th>
						<th width="80">Zero Discharge</th>
                        <th width="60">Store Wise</th>
                        <th width="120">Store</th>
                        <th width="100">Value</th>
                        <th width="160">Date</th>
                        <th width="70">Get Upto</th>
                        <th width="40">Days</th>
                        <th width="70">Get Upto</th>
                        <th width="40">Qty.</th>
                        <th width="40">Ex.Rate.</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('closingstock_1','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td>
                                <? 
                                    echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 0, "", $selected, "","" );
                                ?>                            
                            </td>
                           <td>
								<?php
									echo create_drop_down( "cbo_item_category_id", 120,$item_category,"", 0, "", $selected, "check_itemGroupAndAccount(this.value); ","","5,6,7,23","","","");
									//set_multiselect('cbo_store_name','0','0','','0');
                                ?> 
                          </td>
                            <td>
                            	<input style="width:90px;" name="txt_item_group" id="txt_item_group" class="text_boxes" onDblClick="openmypage_item_group()" placeholder="Browse" readonly />
                                <input type="hidden" name="txt_item_group_id" id="txt_item_group_id" style="width:90px;"/>  
                            </td>
                            <td>
                            	<input style="width:90px;" name="txt_item_acc" id="txt_item_acc" class="text_boxes" onDblClick="openmypage_item_account()" placeholder="Browse" readonly />
                                <input type="hidden" name="txt_item_account_id" id="txt_item_account_id" style="width:90px;"/>
                            </td>
							<td align="center">
							<? 
                                 echo create_drop_down( "cbo_compliance", 80, $compliance_arr,"", 1, "Select", "", "",0 );
                            ?>
                           </td>
                           <td align="center">
							<? 
                                echo create_drop_down( "cbo_store_wise", 50, $yes_no,"", 1, "--Select--", 2, "fn_store_visibility(this.value);load_drop_down( 'requires/closing_stock_report_v3_controller', document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_item_category_id').value+'_'+document.getElementById('cbo_store_wise').value, 'load_drop_down_store', 'store_td' );set_multiselect('cbo_store_name','0','0','','0');" );
                            ?>
                           </td>
                           <td width="120" id="store_td">
                                <? 
                                    echo create_drop_down( "cbo_store_name", 120, $blank_array,"", 1, "--Select--", "", "" );
                                ?>
                           </td>
                             <td> 
                           <?   
                                $valueWithArr=array(0=>'Value With 0',1=>'Value Without 0');
                                echo create_drop_down( "cbo_value_with", 100, $valueWithArr, "", 0, "--  --", 0, "", "", "");
                            ?>
                        </td>
                            <td>
                                <input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:55px;"/>                    							
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:55px;"/>                        
                            </td>
                             <td>
                            <?   
                                
								$get_upto=array(1=>"Greater Than",2=>"Less Than",3=>"Greater/Equal",4=>"Less/Equal",5=>"Equal");
                                echo create_drop_down( "cbo_get_upto", 70, $get_upto,"", 1, "- All -", 0, "",0 );
                            ?>
                        </td>
                        <td align="center">
                       <input type="text" id="txt_days" name="txt_days" class="text_boxes_numeric" style="width:30px" value="" />
                            
                        </td>
                         <td> 
                            <?
                                echo create_drop_down( "cbo_get_upto_qnty", 70, $get_upto,"", 1, "- All -", 0, "",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_qnty" name="txt_qnty" class="text_boxes_numeric" style="width:30px" value="" />
                        </td>
						<td>
                            <input type="text" id="txt_excenge_rate" name="txt_excenge_rate" class="text_boxes_numeric" style="width:30px" value="" />
                        </td>
                            <td>
                                <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                                
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="15" align="center"><? echo load_month_buttons(1);  ?>&nbsp;&nbsp;
                            <input type="button" name="search" id="search" value="Report2" onClick="generate_report(3)" style="width:80px" class="formbutton" />
                            <input type="button" name="search" id="search" value="Lot Wise" onClick="generate_report(6)" style="width:70px" class="formbutton" />
                            <input type="button" name="search" id="search" value="Stock Value" onClick="generate_report(5)" style="width:80px" class="formbutton" />
							<span id="button_data_panel"></span>
                            </td>
                        </tr>
                    </tfoot>
                </table> 
            </fieldset> 
            </div>
                <div id="report_container" align="center"></div>
                <div id="report_container2"></div> 
        </form>    
    </div>
</body>  
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
<script>
	$("#cbo_value_with").val(0);
	$("#cbo_store_name").val(0);
	set_multiselect('cbo_company_name*cbo_item_category_id*cbo_store_name','0*0*0','0*0*0','','0*0*0');
</script> 
</html>