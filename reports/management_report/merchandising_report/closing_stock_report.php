<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Closing Stock Report
				
Functionality	:	
JS Functions	:
Created by		:	Nazim
Creation date 	: 	28-05-2021
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:	Code is poetry, I try to do that :)
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Shipment pending Report","../../../", 1, 1, $unicode,1,'');
?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	function generate_report(type)
	{
		if( form_validation('cbo_company_id*txt_date_from*txt_date_to*txt_exchange_rate','Company Name*Date From*Date To*Exchange Rate')==false )
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*txt_date_from*txt_date_to*txt_exchange_rate',"../../../")+'&report_title='+report_title+'&type='+type;
		
		freeze_window(3);
		http.open("POST","requires/closing_stock_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
			/*$("#report_container6").html(''); 
			$("#report_container5").html(''); 
			$("#report_container4").html(''); 
			$("#report_container3").html(''); 
			$("#report_container2").html(''); */
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
		
	}	

	function new_window()
	{
		// document.getElementById('scroll_body').style.overflow="auto";
		// document.getElementById('scroll_body').style.maxHeight="none";
		// $('#scroll_body tr:last').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('all_report_container').innerHTML+'</body</html>');
		d.close(); 
	
		// document.getElementById('scroll_body').style.overflowY="scroll";
		// document.getElementById('scroll_body').style.maxHeight="380px";
		// $('#scroll_body tr:last').show();
	}
	
	function open_popup(company,from_date,to_date,exchange_rate,action,type)
	{
		var width=''; var height=''; 
		if(type==1){
			width='1050px'; height='420px';
		}else if(type==2){
			width='1335px'; height='400px';
		}else{
			width='1200px'; height='400px';
		}
		//alert(width);return;
		
		//open_acc_loc_popup('1_02-Apr-2021_15-Apr-2021_82_59_1','open_acc_location_details_popup')
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/closing_stock_report_controller.php?action='+action+'&company='+company+'&from_date='+from_date+'&to_date='+to_date+'&exchange_rate='+exchange_rate, 'Item Details', 'width='+width+',height='+height+',center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
		}
	}
</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../");  ?>
    
    <h3 style="width:520px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    
    <div id="content_search_panel" > 
	    <form name="shipmentpending_1" id="shipmentpending_1" autocomplete="off" > 
	        <fieldset style="width:520px" >
	            <table class="rpt_table" width="520" cellpadding="0" cellspacing="0" border="1" rules="all">
	                <thead>
	                	<tr>
		                	<th width="150" class="must_entry_caption">Company Name</th>
		                	<th width="220" class="must_entry_caption">Date Range</th>
		                	<th style="display: none" width="70" class="must_entry_caption">Exchange Rate</th>
		                    <th width="80"><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" /></th>
		                </tr>
	                </thead>
	                <tr class="general">
	                    <td>
							<?
	                        	echo create_drop_down( "cbo_company_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
	                        ?>                                     
	                    </td>
                        <td align="center">
                        <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:70px"/>                                             
                         To
                        <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:70px"/>
                        </td>
	                    <td style="display: none">
	                        <input type="text" name="txt_exchange_rate" id="txt_exchange_rate" value="82" class="text_boxes" style="width:70px"/>
	                    </td>

	                    <td>
	                    	<input type="button" name="show" id="formbutton1" onClick="generate_report(1);" class="formbutton" style="width:80px" value="Show" />
	                    </td>
	                </tr>               
                    <!-- <tr>
                        <td colspan="4" align="center"><? echo load_month_buttons(1);  ?></td>
                    </tr> -->
	            </table>
	        </fieldset>
	    </form>
	    </div>
	     
		<div id="report_container" align="center" style="padding: 10px"></div>
		<div id="all_report_container">		
		    <div id="report_container2"></div>      
		    <div id="report_container3"></div>      
		    <div id="report_container4"></div> 
		    <div id="report_container5"></div> 
		    <div id="report_container6"></div> 
		</div>     
    </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>