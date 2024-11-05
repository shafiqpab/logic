<?
/*-------------------------------------------- Comments
Version                  :   V1
Purpose			         : 	This form will create  Capacity Allocation Print Report
Functionality	         :	
JS Functions	         :
Created by		         :	Monzu 
Creation date 	         : 
Requirment Client        : 
Requirment By            : 
Requirment type          : 
Requirment               : 
Affected page            : 
Affected Code            :                   
DB Script                : 
Updated by 		         : 		
Update date		         : 		   
QC Performed BY	         :		
QC Date			         :	
Comments		         : From this version oracle conversion is start
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

 function generate_report_main()
 {
	 //var report=return_global_ajax_value($('#cbo_company_name').val()+'*'+$('#cbo_year_name').val()+'*'+$('#update_id').val()+'*'+$('#cbo_month').val()+'*'+$('#cbo_location_id').val(), 'capacity_allocation_print', '', 'requires/pre_cost_entry_controller');
	 print_report( $('#cbo_company_name').val()+'*'+$('#cbo_year_name').val()+'*'+$('#update_id').val()+'*'+$('#cbo_month').val()+'*'+$('#cbo_location_id').val(), "capacity_allocation_print", "requires/capacity_allocation_report_print_controller" ) 
	 return; 
 }
	
</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../");  ?>
   <h3 align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
       <div id="content_search_panel" > 
            <form>
                <div style="width:850px">
    <fieldset>  
    <legend>Monthly Capacity Allocation</legend>
        <table cellpadding="0" cellspacing="2" width="500px" class="tbl_capacity_allocation">
            <tr>
                <td>Company</td>
                <td>
                
					<? 
                    	echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected,"load_drop_down( 'requires/capacity_allocation_report_print_controller', this.value, 'load_drop_down_location', 'location_td');" );
                    ?>
                </td>
                <td>Location</td>
                <td id="location_td">
                
					<? 
                    	echo create_drop_down( "cbo_location_id",160,$blank_array,"", 1, "--Select Location--", $selected, "","","","","","",2);
                    ?>
                </td>
                <td>Year</td>
                <td>
					<? 
                    	echo create_drop_down( "cbo_year_name", 150,$year,"id,year", 1, "-- Select Year --", $selected,"" );
                    ?>
                </td>
                <td>Month</td>
                <td>
                <?
                echo create_drop_down( "cbo_month", 160,$months,"", 1, "-- Select --", "","" );
				?>
                </td>
                <td>
                <input type="button" name="search" id="search" value="Show" onClick="generate_report_main()" style="width:80px" class="formbutton" />
                </td>
            </tr>
        </table>
        
    </fieldset>
    <br>
     <fieldset>
    <div style="width:450px;" id="list_container"></div>
    </fieldset>
     
    </div>
                </fieldset>
            </form>
        </div>
       <div id="report_container" align="center"></div>
       <div id="report_container2"> 
        </div>
    </div>
    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>