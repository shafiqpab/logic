<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Raw Materials Closing Stock Report
				
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	04-05-2019
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
echo load_html_head_contents("Raw Materials Closing Stock Report","../../../", 1, 1, $unicode,'',''); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	var tableFilters = 
	{
		col_40: "none",
		col_operation: {
			
		/*id: ["value_tot_open_bl","value_tot_open_bl_amt","value_tot_purchase","value_tot_purchase_amount","value_tot_issue_return","value_tot_issue_return_amount","value_tot_total_receive","value_tot_total_receive_amount","value_tot_issue","value_tot_issue_value","value_tot_rec_return","value_tot_rec_return_amount","value_tot_total_issue","value_tot_total_issue_amount","value_tot_closing_stock","value_tot_closing_stock_ord","value_tot_stock_value","value_tot_stock_value_tk","value_totalpipeLine_qty"],*/
		
		// id: ["value_tot_purchase_amount","value_tot_issue_return_amount","value_tot_total_receive_amount","value_tot_issue_value","value_tot_rec_return_amount","value_tot_total_issue_amount","value_tot_total_issue","value_tot_stock_value","value_tot_stock_value_tk"],
		// col: [12,14,16,18,20,22,24,29,30],
		// operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		// write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	
	
	function openmypage_item_account()
	{
		 var data=document.getElementById('cbo_company_name').value+"_101"+"_"+document.getElementById('txt_item_group_id').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/raw_material_stock_report_controller.php?action=item_account_popup&data='+data,'Item Account Popup', 'width=800px,height=520px,center=1,resize=0','../../')
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
				get_php_form_data( response[0], "item_account_dtls_popup", "requires/raw_material_stock_report_controller" );
				release_freezing();
			}
		}
	}
	
	function openmypage_item_group()
	{
		var data=document.getElementById('cbo_company_name').value+"_101";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/raw_material_stock_report_controller.php?action=item_group_popup&data='+data,'Item Group Popup', 'width=520px,height=380px,center=1,resize=0,scrolling=0','../../')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("item_name_id");
			var response=theemail.value.split('_');
			//alert (response[1]);
			if (theemail.value!="")
			{
				//freeze_window(5);
				document.getElementById("txt_item_group_id").value=response[0];
				document.getElementById("txt_item_group").value=response[1];
				release_freezing();
			}
		}
	}
	
	function generate_report(report_type)
	{
		if((report_type!=3 && form_validation('cbo_company_name','Company Name')==false) || (report_type==3 && form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date*Date')==false))
		{
			return;
		}
		var action="";
		if(report_type == 1){action="generate_report"}
		else if(report_type == 2){action="generate_report2"}
		else if(report_type == 3){action="generate_report3"}
		else if(report_type == 4){action="generate_report4"}
		var report_title=$( "div.form_caption" ).html(); 
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_item_category_id = 101;
		var item_group_id = $("#txt_item_group_id").val();
		var item_account_id = $("#txt_item_account_id").val();
		var cbo_store_name = $("#cbo_store_name").val();
		var from_date = $("#txt_date_from").val();
		var to_date = $("#txt_date_to").val();
		var value_with 	= $("#cbo_value_with").val();
		var cbo_bond_status = $("#cbo_bond_status").val();
		var cbo_section = $("#cbo_section").val();
	
		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_store_name="+cbo_store_name+"&cbo_item_category_id="+cbo_item_category_id+"&from_date="+from_date+"&to_date="+to_date+"&item_account_id="+item_account_id+"&item_group_id="+item_group_id+"&value_with="+value_with+"&cbo_bond_status="+cbo_bond_status+"&cbo_section="+cbo_section+"&report_title="+report_title+"&report_type="+report_type;
		var data="action="+action+dataString;
		//alert (data);return;
		freeze_window(3);
		http.open("POST","requires/raw_material_stock_report_controller.php",true);
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
			if(reponse[2] == 5)
			{
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(2)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			}
			else
			{
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			}
			
			if(reponse[2]==2)
			{
				setFilterGrid("table_body_id",-1);
			}
			else if(reponse[2]==3)
			{
				setFilterGrid("table_body_id",-1);
			}
			else
			{
				setFilterGrid("table_body_id",-1,tableFilters);
			}
			
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window(type)
	{
		if(type == 1)
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$('#table_body_id tr:first').hide();
			$('#rpt_table_header tr th:last').attr('width', 120);
			$('#table_body_id tr td:last').attr('width', 100);
			$('#table_body_footer tr th:last').attr('width', 120);
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			d.close(); 
			$('#table_body_id tr:first').show();
			$('#rpt_table_header tr th:last').attr('width', '');
			$('#table_body_id tr td:last').attr('width', '');
			$('#table_body_footer tr th:last').attr('width', '');
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
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/raw_material_stock_report_controller.php?prod_id='+prod_id+'&action='+action,'Item Pipe Line Popup', 'width=700px,height=420px,center=1,resize=0','../../');
		emailwindow.onclose=function()
		{
			
		}
	}

	function fnc_receive_details(prod_id,rcv_id,action)
	{
		//var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_item_category_id').value+"_"+document.getElementById('txt_item_group_id').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/raw_material_stock_report_controller.php?prod_id='+prod_id+'&rcv_id='+rcv_id+'&action='+action,'Raw Material Receive Details', 'width=700px,height=420px,center=1,resize=0','../../');
		emailwindow.onclose=function()
		{
			
		}
	}

	function fnc_purchase_details(prod_id,action)
	{
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/raw_material_stock_report_controller.php?prod_id='+prod_id+'&action='+action,'purchase Popup', 'width=700px,height=320px,center=1,resize=0','../../');
		emailwindow.onclose=function()
		{
			
		}
	}

	function print_report_button_setting(report_ids)
	{
		$('#button_data_panel').html('');
		//alert(report_ids);
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==178)
			{
				$('#button_data_panel')
					.append( '<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />&nbsp;&nbsp;' );
			}
			if(report_id[k]==152)
			{
				$('#button_data_panel').append( '<input type="button" name="search" id="search" value="MRR WISE" onClick="generate_report(2)" style="width:70px" class="formbutton" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==475)
			{
				$('#button_data_panel').append( '<input type="button" name="search" id="search" value="Consumption" onClick="generate_report(3)" style="width:80px" class="formbutton" />&nbsp;&nbsp;' );
			}
			if(report_id[k]==826)
			{
				$('#button_data_panel').append( '<input type="button" name="search" id="search" value="MRR WISE V2" onClick="generate_report(4)" style="width:100px" class="formbutton" />&nbsp;&nbsp;&nbsp;' );
			}					
		}
	}

</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?><br />    		 
        <form name="closingstock_1" id="closingstock_1" autocomplete="off" > 
         <h3 style="width:1100px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:1100px">      
            <fieldset>  
                <table class="rpt_table" width="1100" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <th width="150" class="must_entry_caption">Company</th>
                        <th width="110">Section</th>
                        <th width="110">Item Group</th>
                        <th width="140">Item Account</th>
                        <th width="140">Store</th>
                        <th width="110">Value</th>
                        <th width="100">Bond Status</th>
                        <th width="160">Date</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('closingstock_1','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td>
                                <? 
                                    echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/raw_material_stock_report_controller', this.value, 'load_drop_down_store', 'store_td' );get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/raw_material_stock_report_controller' );" );//
                                ?>                            
                            </td>
                            <td>
                                <?
									echo create_drop_down( "cbo_section", 110, $trims_section , "", 1, "Select", 0, "" );
								?>                            
                            </td>
                            <td>
                            	<input style="width:100px;" name="txt_item_group" id="txt_item_group" class="text_boxes" onDblClick="openmypage_item_group()" placeholder="Browse" readonly />
                                <input type="hidden" name="txt_item_group_id" id="txt_item_group_id" style="width:90px;"/>  
                            </td>
                            <td>
                            	<input style="width:130px;" name="txt_item_acc" id="txt_item_acc" class="text_boxes" onDblClick="openmypage_item_account()" placeholder="Browse" readonly />
                                <input type="hidden" name="txt_item_account_id" id="txt_item_account_id" style="width:90px;"/>
                            </td>
                            <td width="120" id="store_td">
                                <? 
                                    echo create_drop_down( "cbo_store_name", 130, $blank_array,"", 1, "--Select Store--", "", "" );
                                ?>
                           </td>
                            <td> 
                            <?   
                                $valueWithArr=array(0=>'Value With 0',1=>'Value Without 0');
                                echo create_drop_down( "cbo_value_with", 100, $valueWithArr, "", 0, "--  --", 0, "", "", "");
                            ?>
                        	</td>
                            <td> 
                            <?   
                                $bond_status= array(1 => 'Non Bond', 2 => 'Bond');
                                echo create_drop_down( "cbo_bond_status", 100, $bond_status, "", 1, "ALL", 0, "", "", "");
                            ?>
                        	</td>
                            <td>
                                <input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:55px;"/>                    							
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:55px;"/>                        
                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" align="center"><? echo load_month_buttons(1);  ?>&nbsp;&nbsp;
                            </td>
                            <td colspan="3"><div id="button_data_panel" align="center"></div></td>
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
</script> 
</html>
