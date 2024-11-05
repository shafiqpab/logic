<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	Cutting Wages Bill Report
Functionality	:	
JS Functions	:
Created by		:	REZA 
Creation date 	: 	25-06-2015
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

//----------------------------------------------------------------------------------------------
echo load_html_head_contents("Line Item Wise Production Report", "../../", 1, 1,$unicode,'','');

?>	
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';
//-------------------------------------------------------------------------------------- 
 
function fn_report_generated()
{
	if (form_validation('cbo_company_name*cbo_location*txt_date_from*txt_date_to','Comapny Name*Unite Name*From Bill Date*To Bill Date')==false)
	{
		return;
	}
	else
	{
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_location*cbo_division*cbo_department*cbo_shift*txt_date_from*txt_date_to',"../../");
		freeze_window(3);
		http.open("POST","requires/cutting_wages_bill_report_controller.php",true);
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
		document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>'+'&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
		 var tableFilters = 
				{
					col_17: "none",
					col_operation: {
									id: ["tot_po_qty","tot_wo_qty","tot_prev_bill_qty","tot_pro_qty","tot_bill_qty","tot_bill_qty_pcs","tot_amount"],
									col: [8,9,10,11,12,13,15],
									operation: ["sum","sum","sum","sum","sum","sum","sum"],
									write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
					 
				} 
		setFilterGrid("table_body",-1,tableFilters);
		release_freezing();
 	}
	
}
 


function new_window()
{
	document.getElementById('scroll_body').style.overflow='auto';
	document.getElementById('scroll_body').style.maxHeight='none'; 
	$("#table_body tr:first").hide();
	//$("#table_body1 tr:first").hide();
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>'); 
	d.close();
	
	document.getElementById('scroll_body').style.overflowY='scroll';
	document.getElementById('scroll_body').style.maxHeight='300px';
	$("#table_body tr:first").show();
}	 
 
 
 
function calculate_date()
{		
	var thisDate=($('#txt_date_from').val()).split('-');
	var in_date=thisDate[2]+'-'+thisDate[1]+'-'+thisDate[0];
	//var days=($('#days_required').val())-1;
	var days=5;
	var date = add_days(in_date,days);	
	var split_date=date.split('-');			
	var res_date=split_date[0]+'-'+split_date[1]+'-'+split_date[2];
	$('#txt_date_to').val(res_date);
}

 
	 
</script>

	<?
        $company_arr = return_library_array("select id, company_name from lib_company order by company_name","id","company_name");
        $location_details = return_library_array("select id,location_name from lib_location order by location_name","id","location_name");
        $division_details = return_library_array("select id,division_name from lib_division order by division_name","id","division_name");
        $department_details = return_library_array("select id,department_name from lib_department order by department_name","id","department_name");
    ?>	
    

        
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
                        <th id="search_text_td" class="must_entry_caption">Unit Name</th>
                        <th>Division Name</th>
                        <th>Department Name</th>
                        <th>Shift</th>                                                
                        <th colspan="2" class="must_entry_caption">Bill Date</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                    </tr>    
                 </thead>
                <tbody>
                <tr >
                    <td> 
                        <?
                            echo create_drop_down( "cbo_company_name", 140, $company_arr,"id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/cutting_wages_bill_report_controller', this.value, 'load_drop_down_location', 'location_td' );",0,"1,3" );
                        ?>
                    </td>  
                    <td id="location_td">
                    	<? 
                            echo create_drop_down( "cbo_location", 110, $blank_array,"", 1, "-- Select --", $selected, "", 1, "" );
                        ?>
                    </td>
                    
                    <td>
                    	<?
                        echo create_drop_down( "cbo_division", 160, $division_details,"", 1, "--Select Division--", 0, "" );
                    	?>
                    </td>                  
                    <td>
                    	<?
                        echo create_drop_down( "cbo_department", 160, $department_details,"", 1, "--Select Department--", 0, "" );
                    	?>
                    </td>  
                     <td>
                    	<?
                        echo create_drop_down( "cbo_shift", 60, $shift_name,"", 1, "--All--", 0, "" );
                   		?>
                    </td>
                   
                    <td>
                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:75px" onChange="calculate_date()" readonly >
                    </td>  
                    <td>
                    	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:75px" readonly >
                    </td>
                    <td>
                        <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1)" />
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
