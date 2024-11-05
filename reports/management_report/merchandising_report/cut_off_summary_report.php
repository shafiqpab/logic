<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Cut off summary Report
				
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam 
Creation date 	: 	05/10/2019
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
echo load_html_head_contents("Order Info","../../../", 1, 1, $unicode);
?>	

<script>
var permission='<? echo $permission; ?>';



	function set_week_date(){
		var week=document.getElementById('cbo_week').value*1;
		var year=document.getElementById('cbo_year_selection').value;
	
		if(week){
			
			$('.month_button').attr('disabled','true');
			$('.month_button_selected').attr('disabled','true');
			$('#txt_date_from').attr('disabled','true');
			$('#txt_date_to').attr('disabled','true');
			var week_date=return_global_ajax_value(week+"_"+year, 'week_date', '', 'requires/cut_off_summary_report_controller');
			var week_date_arr=week_date.split('_');
			document.getElementById('txt_date_from').value=week_date_arr[0];
			document.getElementById('txt_date_to').value=week_date_arr[1];
		}else{
			$('.month_button').removeAttr('disabled');
			$('.month_button_selected').removeAttr('disabled');
			$('#txt_date_from').removeAttr('disabled');
			$('#txt_date_to').removeAttr('disabled');
		}
	}


	function generate_report()
	{
		if (form_validation('cbo_company_name*txt_date_from*txt_date_to','Comapny Name*From Date*To Date')==false)
		{
			return;
		}
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_season_id*cbo_week*cbo_date_category*txt_date_from*txt_date_to*cbo_order_status',"../../../");
		freeze_window(3);
		http.open("POST","requires/cut_off_summary_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			document.getElementById('report_container2').innerHTML=response[0];
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>'; 
			show_msg('3');
			release_freezing();
		}
	}



	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_print.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="330px";
	}
</script>



</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../");  ?>
   <h3 style="width:1000px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')"> -Search Panel</h3>
       <div id="content_search_panel"> 
        <form>
            <fieldset style="width:1000px;">
            <div  style="width:100%" align="center">
               <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th class="must_entry_caption">Company</th>
                            <th>Buyer</th>
                            <th>Season</th>
                             <th>Week</th>
                            <th>Date Category</th>
                            <th colspan="2" class="must_entry_caption">Date Range</th>
                            <th>Order Status</th>
                            <th><input type="reset" name="reset" id="reset" value="Reset" style="width:80px" class="formbutton" /></th>
                        </tr>
                    </thead>
                    <tr>
                        <td>
						   <?
                           echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, " load_drop_down( '../merchandising_report/requires/cut_off_summary_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?> 
                        </td>
                        <td id="buyer_td" align="center">
                         <? 
                            echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --", $selected, "" );
                         ?>	
                        </td>
                        <td id="season_td" align="center">
                         <? 
                            echo create_drop_down( "cbo_season_id", 120, $blank_array,"", 1, "-- All --", $selected, "" );
                         ?>	
                        </td>
                        <td> 
                        <?
                        $weekArr=array();
                        $sql=sql_select("select id,week from week_of_year  where year=".date("Y"));
                        foreach($sql as $row){
                            $weekArr[$row[csf('week')]]="Week-".$row[csf('week')];
                        }
                         echo create_drop_down( "cbo_week", 80, $weekArr,"", 1, "-- Select --", $selected, "set_week_date()"  );
                        ?>
                        </td>
                        <td>
                        <? 
                            $category_arr=array(1=>"Ship Date Wise",2=>"PO Rec. Date Wise",3=>"Country Ship Date Wise");
                            echo create_drop_down( "cbo_date_category", 100,$category_arr,"", 0, "--All--", 3, "" );
                        ?>	
                        
                        </td>
                        <td align="center"><input name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:75px">
                        </td>
                        <td align="center"><input name="txt_date_to" id="txt_date_to"  class="datepicker" style="width:75px">
                        </td>
                        <td>
                        <? 
                            echo create_drop_down( "cbo_order_status", 100, $order_status,"", 1, "ALL", 1, "" );
                        ?>	
                        </td>
                        <td>
                        <input type="button" name="search" id="search1" value="Show" onClick="generate_report('report_generate',0)" style="width:80px;" class="formbutton" />
                        
                        </td>
                    </tr>
                    <tr>
                        <td colspan="9" align="center">
                            <? echo load_month_buttons(1); ?>
                        </td>
                        
                    </tr>
                </table>
            </div>
            </fieldset>
        </form>
        </div>
       <div id="report_container" align="center"></div>
       <div id="report_container2"></div>
    </div>
    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>