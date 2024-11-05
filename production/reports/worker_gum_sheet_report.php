<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create .
Functionality	:	
JS Functions	:
Created by		:	CTO 
Creation date 	: 	18-11-2013
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
echo load_html_head_contents("Worker Gum Sheet Report", "../../", 1, 1,$unicode,'',''); 

?>	

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';
	
function fnc_report_generated()
{
	$('#txt_selected_id').val('');
	var detls=$('#cbo_company_name').val()+"__"+$('#cbo_location').val()+"__"+$('#cbo_division').val()+"__"+$('#cbo_department').val()+"__"+$('#cbo_floor').val()+"__"+$('#cbo_line').val()+"__"+$('#txt_emp_code').val()+"__"+$('#txt_id_card').val();
	
	 show_list_view(detls,'show_employee_listview','report_container','requires/worker_gum_sheet_report_controller','setFilterGrid("list_view",-1); $("#close").hide();');
	 
	 
} 

var selected_id = new Array, selected_name = new Array();
		
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				eval( $('#tr_'+i).attr('onclick'));
				
			}
		}
		
		function toggle( x, origColor ) {
			
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			var str1=str.split("_");
			toggle( document.getElementById( 'tr_' + str1[0] ), '#FFFFCC' );
			str=str1[1];
		 
			if( jQuery.inArray( str, selected_id ) == -1 ) {
				selected_id.push( str );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			$('#txt_selected_id').val( id );
		}
function print_report_gumsheet()
{
	var url=return_ajax_request_value( $('#txt_selected_id').val(), "print_report_employee_barcode", "requires/worker_gum_sheet_report_controller");
//	alert(url);
	window.open("requires/"+url,"##");
	//return_ajax_request_value(1, "", "requires/employee_info_controller");
}

</script>

</head>
 
<body onLoad="set_hotkey();">

<form id="dateWiseProductionReport_1" autocomplete="off">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../",'');  ?>
         
         <h3 style="width:1000px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1000px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" align="center">
               <thead>                    
                        <th width="150">Company Name</th>
                        <th width="110">Location</th>
                        <th width="110">Division</th>
                        <th width="110">Department</th>
                        <th width="110">Floor</th>
                        <th width="80">Line</th>
                       
                        <th width="">Emp Code</th>
                         <th width="">ID Card</th>
                        <th width="110"><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                 </thead>
                <tbody>
                <tr class="general">
                    <td> 
                        <?
                            echo create_drop_down( "cbo_company_name", 150, "select distinct  company_name from lib_employee comp where status_active =1 and is_deleted=0 and company_name!='' order by company_name","company_name,company_name", 1, "-- Select Company --", 0, "" );
                        ?>
                    </td>
                    <td id="location_td">
                    	<? 
                            echo create_drop_down( "cbo_location", 110, $blank_array,"", 1, "-- Select --", $selected, "",0,"" );
                        ?>
                    </td>
                    <td>
                        <? 
                            echo create_drop_down( "cbo_division", 110, "Select distinct division_name from lib_employee order by division_name","division_name,division_name", 1, "-- Select Division --", $selected, "",0,"" );
                        ?>
                    </td>
                     <td>
                    	<? 
							echo create_drop_down( "cbo_department", 110,"Select distinct  department_name from lib_employee where department_name!='' order by department_name","department_name,department_name", 1, "-- Select --", 0, "",0,"" );
                        ?>
                    </td> 
                    <td id="floor_td">
                    	<? 
                            echo create_drop_down( "cbo_floor", 110, $blank_array,"", 1, "-- Select --", $selected, "",0,"" );
                        ?>
                    </td>
                    <td>
                    	<? 
                           echo create_drop_down( "cbo_line", 110, "Select distinct line_name from  lib_employee order by line_name","line_name,line_name", 1, "-- Select --", 0, "",'',"" );
                        ?>
                    </td> 
                      
                    <td><input name="txt_emp_code" id="txt_emp_code" class="text_boxes" style="width:150px" placeholder="Write here" >
                    </td>
                    <td>
                    	<input name="txt_id_card" id="txt_id_card" class="text_boxes" style="width:150px" placeholder="Write here" >
                    </td>
                    <td>
                        <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fnc_report_generated()" />
                    </td>
                </tr>
                <tr class="general">
                    <td colspan="8" height="15"><input type="hidden" id="txt_selected_id" name="txt_selected_id" /></td>
                </tr>
                <tr>
                    <td colspan="8" id="report_container"></td>
                </tr>
                <tr>
                    <td colspan="8" align="center" height="35" valign="middle">
                    	<input type="button" onClick="print_report_gumsheet()" value="Print Gum Sheet" style="width:150px" class="formbutton" />
                    </td>
                </tr>
                </tbody>
            </table>
            
            <br />
        </fieldset>
    </div>
    </div>
        
    
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

<script>
$( ".combo_boxes" ).each(function( index ) {

	if(  this.id !='cbo_company_name')
			$('#'+this.id).val($('#'+this.id+' option:first').val());
		


});
</script>
</html>
