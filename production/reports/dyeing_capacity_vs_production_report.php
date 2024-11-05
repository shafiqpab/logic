<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Dyeing Capacity Vs. Production Report
Functionality	:	
JS Functions	:
Created by		:	Abu Sayed 
Creation date 	: 	17-08-2022
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
echo load_html_head_contents("Dyeing Capacity Vs. Production Report", "../../", 1, 1,$unicode,1,1);
?>	
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';
	
	function fn_report_generated(operation)
	{
        var cbo_floor_from=$('#cbo_floor_from').val();
	    var cbo_floor_to=$('#cbo_floor_to').val();
		
        if( form_validation('cbo_company_id*cbo_floor_from*cbo_floor_to*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false )
        {
            return;
        }

        if(cbo_floor_from==cbo_floor_to)
        {
            alert("Same Floor Not Allowed...");
            return;
           
        }
		
		var report_title=$( "div.form_caption" ).html();
		if (operation==1) 
		{
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_within_group*cbo_inbound_subcon*cbo_floor_from*cbo_floor_to*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
		}
        //alert(data);return;
		
		freeze_window(3);
		http.open("POST","requires/dyeing_capacity_vs_production_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;  
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
        {	 
            var reponse=trim(http.responseText).split("####");
            $("#report_container2").html(reponse[0]);  
            document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            
            show_msg('3');
            release_freezing();
        }
	}
	
    function new_window()
    {
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
    '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
        d.close(); 
    }

    function calculate_date()
    {	
        var thisDate=($('#txt_date_from').val()).split('-');
        
        var last=new Date( (new Date(thisDate[2], thisDate[1],1))-1 );
        
        //alert(last);return;
        var last_date = last.getDate();
        var month = last.getMonth()+1;
        var year = last.getFullYear();
        
        if(month<10)
        {
            var months='0'+month;
        }
        else
        {
            var months=month;
        }
        
        var last_full_date=last_date+'-'+months+'-'+year;
        var first_full_date='01'+'-'+months+'-'+year;
        
        $('#txt_date_from').val(first_full_date);
        $('#txt_date_to').val(last_full_date);
    
    }
    
</script>

</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>    		 
        <form name="DyeingCapacity_1" id="DyeingCapacity_1" autocomplete="off" > 
         <h3 style="width:830px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:830px" >      
            <fieldset>  
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" align="center" rules="all" border="1">
                <thead>                    
                    <th width="150" class="must_entry_caption">Company</th>
                    <th width="100">Within Group</th>
                    <th width="100">Is Inbound Subcon</th>
                    <th width="100" class="must_entry_caption">From Floor</th>
                    <th width="100" class="must_entry_caption">To Floor</th>
                    <th width="150" class="must_entry_caption" colspan="2">Production Date</th>
                    <th>    
                        <input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('DyeingCapacity_1','report_container*report_container2','','','')" />
                    </th>
                </thead>
                <tbody>
                    <tr>
                        <td> 
							<?
								echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/dyeing_capacity_vs_production_report_controller', this.value, 'load_drop_down_floor_from', 'floor_td_from' );load_drop_down( 'requires/dyeing_capacity_vs_production_report_controller', this.value, 'load_drop_down_floor_to', 'floor_td_to' );" );
						    ?>
						</td>
                        <td>
                            <? 
                            	echo create_drop_down( "cbo_within_group", 100, $yes_no,"", 1, "-- Select --", 0, "");
                            ?>
                        </td>
                        <td > 
                        	<? 
						    	echo create_drop_down( "cbo_inbound_subcon", 100, $yes_no,"", 1, "-- Select --", 2, ""); 
						    ?>
                        </td>
                        <td id="floor_td_from">
                            <? echo create_drop_down( "cbo_floor_from", 100, $blank_array,"", 1, "-- Select Floor --", 0, "",1 ); ?>
                        </td>
                        <td id="floor_td_to">
                            <? echo create_drop_down( "cbo_floor_to", 100, $blank_array,"", 1, "-- Select Floor --", 0, "",1 ); ?>
                        </td>
                        <td align="center">
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:75px" placeholder="From Date" onchange="calculate_date()"> To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:75px" placeholder="To Date"  disabled>
                        </td>
                        <td align="center" colspan="2">
                            <input type="button" id="show_button" class="formbutton" style="width:60px" value="Show" onClick="fn_report_generated(1)" /> 
                        </td>
                        
                    </tr>
                </tbody>
                <tr>
                    <td colspan="9" align="center">
                        <? echo load_month_buttons(1); ?>
                    </td>
                </tr>
            </table> 
        </fieldset>
        </div>
        <div id="report_container" align="center" style="padding: 5px 0;"></div>
        <div id="report_container2" ></div>
    </form> 
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
