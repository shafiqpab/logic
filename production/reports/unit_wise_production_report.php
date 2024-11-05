<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Unit wise Production Report.
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	26-12-2013
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
echo load_html_head_contents("Unit Wise Production Report", "../../", 1, 1,$unicode,1,1);

?>	

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';	
			
	
	function fn_report_generated(operation)
	{
		/*if (form_validation('cbo_company_id*cbo_location_id*txt_date_from*txt_date_to','Comapny Name*Location*From Date*To Date')==false)
		{
			return;
		}*/
		
		if(operation==4){
			if($('#cbo_company_id').val()==0){
				var data='cbo_working_company_id*txt_date_from*txt_date_to';	
				var filed='Working Company Name*From Date*To Date';	
			}
			else
			{
				var data='cbo_company_id*txt_date_from*txt_date_to';	
				var filed='Company Name*From Date*To Date';	
			}
		}
		else if($('#cbo_company_id').val()==0){
			var data='cbo_working_company_id*cbo_location_id*txt_date_from*txt_date_to';	
			var filed='Working Company Name*Location*From Date*To Date';	
		}
		else
		{
			var data='cbo_company_id*cbo_location_id*txt_date_from*txt_date_to';	
			var filed='Company Name*Location*From Date*To Date';	
		}
		
		
		
		if( form_validation(data,filed)==false )
		{
			return;
		}
		else
		{ 
			var from_date = $('#txt_date_from').val();
			var to_date = $('#txt_date_to').val();
			var datediff = date_diff( 'd', from_date, to_date )+1;
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_id*txt_date_from*txt_date_to*cbo_working_company_id',"../../")+'&report_title='+report_title+'&type='+operation+'&datediff='+datediff;
			freeze_window(3);
			http.open("POST","requires/unit_wise_production_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
	
/*	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
	 		show_msg('3');
			release_freezing();
		}
	}*/	
	
	
	function fn_report_generated_reponse()
	{	
		if(http.readyState == 4) 
		{
			//alert (http.responseText);
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
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
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="300px";
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
function fn_disable_com(str){
		if(str==2){$("#cbo_company_id").attr('disabled','disabled');}
		else{ $('#cbo_company_id').removeAttr("disabled");}
		if(str==1){$("#cbo_working_company_id").attr('disabled','disabled');}
		else{ $('#cbo_working_company_id').removeAttr("disabled");}
	}	
</script>
</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
        <form name="unitwiseproduction_1" id="unitwiseproduction_1" autocomplete="off" > 
         <h3 style="width:920px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:920px" align="center" >      
            <fieldset>  
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" align="center">
                <thead>                    
                    <th width="150" class="must_entry_caption">Company Name</th>
                    <th class="must_entry_caption" width="150">Working Company</th>
                    <th width="150" class="must_entry_caption">Location</th>
                    <th width="" class="must_entry_caption">Date</th>
                    <th width="100"><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('unitwiseproduction_1','report_container*report_container2','','','')" /></th>
                </thead>
                <tbody>
                    <tr>
                        <td> 
							<?
								echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down('requires/unit_wise_production_report_controller', this.value, 'load_drop_down_location', 'location_td' );fn_disable_com(1)" );
                            ?>
                        </td>
                        <td width="150" align="center"> 
							<?
                                echo create_drop_down( "cbo_working_company_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/unit_wise_production_report_controller',  this.value, 'load_drop_down_location', 'location_td');fn_disable_com(2)" );
                            ?>
                      	</td>
                        <td id="location_td">
							<? 
								echo create_drop_down( "cbo_location_id", 150, $blank_array,"", 1, "-- Select --", $selected, "",1,"" );
                            ?>
                        </td>
                        <td align="center">
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:90px" placeholder="From Date" > To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:90px"  placeholder="To Date"  >
                        </td>
                        <td align="center">
                            <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(3)" />
                        </td>
                    </tr>
                </tbody>
                <tr>
                    <td colspan="5">
                        <? echo load_month_buttons(1); ?>
                        <input type="button" id="show_button" class="formbutton" style="width:70px" value="Summary" onClick="fn_report_generated(4)" />
                    </td>
                </tr>
            </table> 
        </fieldset>
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2" align="left"></div>
    </form> 
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
