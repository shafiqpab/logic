<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	Report.
Functionality	:	
JS Functions	:
Created by		:	Aziz 
Creation date 	: 	03-04-2016
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
echo load_html_head_contents("Date Wise Production Report", "../../", 1, 1,$unicode,'','');

?>	

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';
 
var tableFilters = 
		{
			col_0: "none", 
		} 
		   		
var tableFilters1 = 
		{
			col_0: "none", 
		} 
					
function fn_report_generated()
{
	if (form_validation('cbo_company_name*txt_date','Comapny Name*From Date')==false)
	{
		return;
	}
	else
	{
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_location*cbo_floor*cbo_line*txt_style_no*txt_date*cbo_subcon',"../../");
		freeze_window(3);
		http.open("POST","requires/buyer_line_wise_hourly_production_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
}
	

function fn_report_generated_reponse()
{
 	if(http.readyState == 4) 
	{
	 	show_msg('3'); 
  		var reponse=trim(http.responseText).split("####"); 
		$('#report_container2').html(reponse[0]);
		//alert(reponse[1]);
		document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>'+'&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
		//document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
		//append_report_checkbox('table_header_1',1);		
		//setFilterGrid("table_body",-1,tableFilters);
		
		//setFilterGrid("table_body1",-1,tableFilters);		
		release_freezing();
 	}
	
}

function new_window()
{
	document.getElementById('scroll_body').style.overflow='auto';
	document.getElementById('scroll_body').style.maxHeight='none'; 
	$("#table_body tr:first").hide();
	$("#table_body1 tr:first").hide();
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><style type="text/css">.block_div { width:auto;height:auto;text-wrap:normal;vertical-align:bottom;display: block;position: !important;-webkit-transform: rotate(-90deg);} </style><body>'+document.getElementById('report_container2').innerHTML+'</body</html>'); 
	d.close();
	
	document.getElementById('scroll_body').style.overflowY='scroll';
	document.getElementById('scroll_body').style.maxHeight='425px';
	$("#table_body tr:first").show();
}	 
 
 function show_line_remarks(company_id,order_id,floor_id,line_no,prod_date,action)
	{
		
		popup_width='550px'; 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/buyer_line_wise_hourly_production_report_controller.php?order_id='+order_id+'&company_id='+company_id+'&floor_id='+floor_id+'&sewing_line='+line_no+'&prod_date='+prod_date+'&action='+action, 'Detail Veiw','width='+popup_width+', height=270px,center=1,resize=0,scrolling=0','../');
	}
	  		
	
	 
</script>
        
</head>
 
<body onLoad="set_hotkey();">

<form id="lineWiseProductionReport_1">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../",'');  ?>
               
         <fieldset style="width:850px;">
        	<legend>Search Panel</legend>
            <table class="rpt_table" width="850px" cellpadding="0" cellspacing="0" border="1" align="center">
               <thead>                    
                    <tr>
                        <th class="must_entry_caption">Company Name</th>
                        <th id="search_text_td" class="must_entry_caption">Prod. Date</th>
                        <th>Location</th>
                        <th>Floor</th>
                        <th>Line</th>                                                
                        <th>Style No</th>
                        <th>Subcon.</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                    </tr>    
                 </thead>
                <tbody>
                <tr >
                    <td width="140"> 
                        <?
                            echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/buyer_line_wise_hourly_production_report_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                        ?>
                    </td>  
                    <td width="">
                    	<input name="txt_date" id="txt_date" class="datepicker" style="width:75px" onChange="load_drop_down( 'requires/buyer_line_wise_hourly_production_report_controller',document.getElementById('cbo_floor').value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_line', 'line_td' );" readonly >
                    </td>                  
                    <td width="110" id="location_td">
                    	<? 
                            echo create_drop_down( "cbo_location", 110, $blank_array,"", 1, "-- Select --", $selected, "", 1, "" );
                        ?>
                    </td>
                    <td width="110" id="floor_td">
                    	<? 
                            echo create_drop_down( "cbo_floor", 110, $blank_array,"", 1, "-- Select --", $selected, "", 1, "" );
                        ?>
                    </td>  
                     <td width="110" id="line_td">
                    	<? 
 							echo create_drop_down( "cbo_line", 110, $blank_array,"", 1, "-- Select --", $selected, "", 1, "" );
                        ?>
                    </td>
                   
                    <td width="130">
                    	 <input type="text" id="txt_style_no" name="txt_style_no" class="text_boxes" style="width:115px" />
                    </td>  
                    <td width="70">
                        <? 
                            $cubcon_arr = array(1=>"No",2=>"Yes");
                            echo create_drop_down( "cbo_subcon", 65, $cubcon_arr,"", 0, "", 1, "",0,"" );
                        ?>
                    </td>
                    <td width="70">
                        <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(0)" />
                    </td>
                </tr>
                </tbody>
            </table>
        </fieldset>
    </div>
    
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
