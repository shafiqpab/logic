<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Chemical Dyes Conssumption Status
				
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	10-02-2020
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
echo load_html_head_contents("Chemical Dyes Conssumption Status","../../../", 1, 1, $unicode,'',''); 

?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var tableFilters = 
	{
		col_40: "none",
		col_operation: {
		id: ["value_tot_open_bl","value_tot_pipe_qty","value_tot_qty","value_tot_issue_qty","value_tot_surplus_qty"],
		col: [5,6,7,8,9],
		operation: ["sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	
	function generate_report(rptType)
	{
		if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date Form*Date To')==false )
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html(); 
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_item_category_id = $("#cbo_item_category_id").val();
		var cbo_store_name = $("#cbo_store_name").val();
		var txt_date_from = $("#txt_date_from").val();
		var txt_date_to = $("#txt_date_to").val();		
		
		var dataString = "&rptType="+rptType+"&cbo_company_name="+cbo_company_name+"&cbo_store_name="+cbo_store_name+"&cbo_item_category_id="+cbo_item_category_id+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&report_title="+report_title;
		//alert(dataString);return;
		var data="action=generate_report"+dataString;
		//alert (data);
		freeze_window(3);
		http.open("POST","requires/chemical_dyes_consumption_status_controller.php",true);
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
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
				setFilterGrid("table_body_id",-1,tableFilters);
			
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window(type)
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?><br />    		 
    	<form name="dyes_cmcl_smry_rpt" id="dyes_cmcl_smry_rpt" autocomplete="off" > 
    		<h3 style="width:900px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
     		<div id="content_search_panel" style="width:900px">      
	        	<fieldset>  
	            	<table class="rpt_table" width="900" cellpadding="0" cellspacing="0" border="1" rules="all">
	                	<thead>
	                    	<th width="190" class="must_entry_caption">Company</th>
	                    	<th width="190">Item Category</th>
	                    	<th width="190">Store</th>
	                    	<th width="180" class="must_entry_caption">Issue Date Range</th>
	                    	<th>
	                    		<input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('dyes_cmcl_smry_rpt','report_container*report_container2','','','')" /></th>
	                	</thead>
	                	<tbody>
	                    	<tr class="general">
	                        	<td>
	                            <? 
	                                echo create_drop_down( "cbo_company_name", 180, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "Select", $selected, "load_drop_down( 'requires/chemical_dyes_consumption_status_controller', this.value, 'load_drop_down_store', 'store_td' );" );
	                            ?>                            
	                        	</td>
	                       		<td id="cat_td">
								<? 
									echo create_drop_down( "cbo_item_category_id", 180,$item_category,"", 1, "Select", $selected, "","","5,6,7,23","","","");
	                            ?> 
	                      		</td>
	                       		<td id="store_td">
	                            <? 
	                                echo create_drop_down( "cbo_store_name", 180, $blank_array,"", 1, "--Select Store--", "", "" );
	                            ?>
	                       		</td>
	                        	<td>
                                <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;"/>&nbsp;To
                                <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;"/>
                                </td>
	                    		<td>
	                            	<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />
	                            	<input type="button" name="search" id="search" value="Show2" onClick="generate_report(2)" style="width:70px" class="formbutton" title="This Button is only for these Issue Purpose : Gmt wash, Rnd, Lab Test, WTP, ETP and Machine Wash" />

	                        	</td>
	                    	</tr>
                            <tr>
		                        <td colspan="8" align="left">
		                        	<? echo load_month_buttons(1);  ?>&nbsp;&nbsp;	                        
		                        </td>
		                    </tr>
	                	</tbody>
		            </table> 
		        </fieldset> 
    		</div>
            <div id="report_container" align="center" style="width:1150px;"></div>
            <div id="report_container2"></div> 
    	</form>    
    </div>
</body>  
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
