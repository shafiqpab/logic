<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Finished Goods Stock Valuation Analysis Report
				
Functionality	:	
JS Functions	:
Created by		:	Shafiq
Creation date 	: 	09-12-2020
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
echo load_html_head_contents("Monthly Production Summary Report AKH","../../", 1, 1, $unicode,1,1); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(type)
	{
		if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*Production Date From*Production Date To')==false )
		{
			return;
		}

		var report_title=$( "div.form_caption" ).html();
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title+'&type='+type;
		freeze_window(3);
		http.open("POST","requires/finished_goods_stock_valuation_analysis_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			//alert (http.responseText);
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);  
			//alert(reponse[1]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window()
	{
		// document.getElementById('scroll_body').style.overflow="auto";
		// document.getElementById('scroll_body').style.maxHeight="none"; 
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_print.css" type="text/css"  /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		// document.getElementById('scroll_body').style.overflowY="scroll"; 
		// document.getElementById('scroll_body').style.maxHeight="400px";
	}	
	
	function fn_load_buyer()
	{
		load_drop_down( 'requires/finished_goods_stock_valuation_analysis_controller', $('#cbo_company_id').val(), 'load_drop_down_buyer', 'buyer_td' );
	}
	 
</script>
</head>
<body onLoad="set_hotkey();">
<form id="hourlyPolyMonitoring_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:780px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:780px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="200" class="must_entry_caption">Working Company</th>
                    <th width="200">Buyer Name</th>
                    <th width="200" class="must_entry_caption" colspan="2">Date Range</th>
                    <th width=""><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('hourlyPolyMonitoring_1','report_container','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                       <td>
							<? 
                                echo create_drop_down( "cbo_company_id", 200, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Working Company --", $selected,"" );
                            ?>                            
                        </td>
                        <td id="buyer_td">
                            <?  echo create_drop_down( "cbo_buyer_id", 200, $blank_array,"", 1, "-- Select Buyer --", "", "" ); ?>                            
                        </td>
                        <td>
                        	<input name="txt_date_from" value="<?=date("d-m-Y", strtotime(date('Y-m-1')))?>" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" readonly></td>
                        <td>
                        	<input name="txt_date_to" value="<?=date( 't-m-Y' );?>" id="txt_date_to" class="datepicker" style="width:100px"  placeholder="To Date"  readonly>
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show Report" onClick="generate_report(1)" style="width:100px" class="formbutton" />
                        </td>
                    </tr>

                        <tr>
                            <td colspan="5" align="center"><?=load_month_buttons(1); ?></td>
                        </tr>
                </tbody>
            </table>
        </fieldset>
    	</div>
    </div>
    <div id="report_container" align="center" style="padding:5px 0;"></div>
    <div id="report_container2" align="left"></div>
 </form>   
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
<script type="text/javascript">
	set_multiselect('cbo_company_id','0','0','','0','fn_load_buyer();');
</script>
</html>
