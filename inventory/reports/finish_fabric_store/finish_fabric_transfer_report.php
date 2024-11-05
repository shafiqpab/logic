<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Order Wise Finich Fabric Stock Report
				
Functionality	:	
JS Functions	:
Created by		:	
Creation date 	: 	
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
echo load_html_head_contents("Finich Fabric Transfer Report","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	var tableFilters = 
	{
		col_operation: {
		id: ["value_trans_roll","value_trans_qnty"],
		col: [9,10],
		operation: ["sum","sum"],
		write_method: ["innerHTML","innerHTML"]
		}
	} 

	function generate_report()
	{
		if( form_validation('cbo_transfer_criteria*cbo_company_id*cbo_from_store_id*cbo_to_store_id*txt_date_from*txt_date_to','Transfer Criteria*Company Name*From Store*To Store*From Date*To Date')==false )
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_transfer_criteria*cbo_company_id*cbo_from_store_id*cbo_to_store_id*cbo_buyer_id*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;

		freeze_window(3);
		http.open("POST","requires/finish_fabric_transfer_report_controller.php",true);
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
			$("#report_container3").html(reponse[2]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>&nbsp;&nbsp;<input type="button" onclick="new_window_2()" value="Print Preview Summary" name="Print" class="formbutton" style="width:160px"/>';
			var cbo_presentation=$('#cbo_presentation').val(); 
			setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
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
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#scroll_body tr:first').show();
	}

	function new_window_2()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";
		//$('#table_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container3').innerHTML+'</body</html>');
		d.close(); 
	
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="380px";
		//$('#scroll_body tr:first').show();
	}

	function print_report_transfer_2(transfer_id)
	{
		var report_title="";
		
		var data = "action=finish_fabric_transfer_print_2&data="+ $('#cbo_company_id').val()+'*'+transfer_id+'*'+report_title+'*'+'transfer_report';

		http.open("POST","../../finish_fabric/requires/finish_fabric_transfer_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = transfer_report_reponse;
	}

	function transfer_report_reponse() {
		if (http.readyState == 4) {
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                '<html><head><title></title></head><body>' + http.responseText + '</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
			d.close();
		}

	}


	function fnc_load_with_company()
	{
		var companyID = $("#cbo_company_id").val();
		var transfer_criteria = $("#cbo_transfer_criteria").val();
		load_drop_down( 'requires/finish_fabric_transfer_report_controller',companyID, 'load_drop_down_buyer', 'buyer_td' );
		load_drop_down( 'requires/finish_fabric_transfer_report_controller',companyID, 'load_drop_down_from_store', 'from_store_td' );
		if(transfer_criteria !=1)
		{
			load_drop_down( 'requires/finish_fabric_transfer_report_controller',companyID, 'load_drop_down_to_store', 'to_store_td' );
		}else{
			load_drop_down( 'requires/finish_fabric_transfer_report_controller',0, 'load_drop_down_to_store', 'to_store_td' );
		}
	}
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?>   		 
    <form name="ordewisefinishfabricstock_1" id="ordewisefinishfabricstock_1" autocomplete="off" > 
    <h3 style="width:900px; margin-top:20px;" align="" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:900px;">
                <table class="rpt_table" width="900" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	
                        	<th width="120" class="must_entry_caption">Transfer Criteria</th> 	
                            <th width="130" class="must_entry_caption">Company</th>
                            <th width="110" class="must_entry_caption">From Store</th>
                            <th width="110" class="must_entry_caption">To Store</th>
                            <th width="110">Buyer</th> 
                            <th width="140" class="must_entry_caption">Transfer Date Range</th>
                            <th width="70"><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('ordewisefinishfabricstock_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr align="center" class="general">
                    	<td> 
                            <?
                                echo create_drop_down( "cbo_transfer_criteria", 130, $item_transfer_criteria, "", 1, "-- Select --", 2, "", "", "1,2,4");
                            ?>
                        </td>
                        <td>
                            <? 
                               echo create_drop_down( "cbo_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_load_with_company()" );

                            ?>
                        </td>
                         <td id="from_store_td">
                            <?
                            	echo create_drop_down( "cbo_from_store_id", 110, $blank_array,"", 1, "--Select Store--", 0, "",0 );
                            ?>
                        </td>
                        <td id="to_store_td">
                            <?
                            	echo create_drop_down( "cbo_to_store_id", 110, $blank_array,"", 1, "--Select Store--", 0, "",0 );
                            ?>
                        </td>
                        <td id="buyer_td"> 
                            <?
                                echo create_drop_down( "cbo_buyer_id", 110, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("01-m-Y", time());?>" class="datepicker" style="width:55px;" readonly/>	
                            To
                            <input type="text" name="txt_date_to" id="txt_date_to" value=" <? echo date("d-m-Y", time());?>" class="datepicker" style="width:55px;" readonly />  			
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:70px" class="formbutton" />
                        </td>
                    </tr>
                    <tfoot>
                        <tr>
                            <td colspan="8" align="center">
                                <? echo load_month_buttons(1);  ?>
                            </td>
                        </tr>
                    </tfoot>
                </table> 
            </fieldset> 
        </div>
             
    </form>    
</div>
	<div id="report_container" align="center" style="padding: 10px;"></div>
    <div id="report_container2"></div>      
    <div id="report_container3" style="display: none;"></div>      
</body>  
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
