<?
/*--- ----------------------------------------- Comments
Purpose			: 						
Functionality	:	
JS Functions	:
Created by		:	sohel
Creation date 	: 	19-03-2013
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start(); 
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sub-Contract Order Info", "../../", 1,1, $unicode,1,'');
?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>';

function new_window()
{
	if( form_validation('selected_order_id','Select Challan')==false )
	{
		return;
	}
	//document.getElementById('cbo_company_name').value
	
	var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_process_name').value+"_"+document.getElementById('txt_date_from').value+"_"+document.getElementById('txt_date_to').value+"_"+document.getElementById('selected_order_id').value;
	
	print_report(data,'chalan_print_window','requires/chalan_print_controller' ) 

}	
	 

	var selected_id = new Array(); var selected_challan = new Array();
	
function check_all_data()
{
var tbl_row_count = document.getElementById( 'chalan_table' ).rows.length;
	tbl_row_count = tbl_row_count - 1;
	for( var i = 1; i <= tbl_row_count; i++ ) {
	eval($('#tr_'+i).attr("onclick"));  
	}
}

function toggle( x, origColor ) {
	var newColor = 'yellow';
	if ( x.style ) {
	x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
	}
}

function js_set_value(id)
{
	//alert (id);
	var str=id.split("_");
	//alert (str[0]);
	var challan_arr=document.getElementById('selected_challan_no').value;
	var challan_str=challan_arr.split(",");
	var lastItem = challan_str.pop();
	if ( lastItem!="" && lastItem!=str[1] )
		{
				alert('Challan Mix Not Allowed')
				return;	
		}
	
	toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
	//str=str[0];
	if( jQuery.inArray(  str[0] , selected_id ) == -1 ) {
	selected_id.push( str[0] );
	selected_challan.push( str[1] );
	}
	else {
	for( var i = 0; i < selected_id.length; i++ ) {
	if( selected_id[i] == str[0]  ) break;
	}
	selected_id.splice( i, 1 );
	selected_challan.splice( i, 1 );
	}
	var id = ''; var challan = '';
	for( var i = 0; i < selected_id.length; i++ ) {
	id += selected_id[i] + ',';
	challan += selected_challan[i] + ',';
	}
	id = id.substr( 0, id.length - 1 );
	challan = challan.substr( 0, challan.length - 1 );
	
	$('#selected_order_id').val( id );
	$('#selected_challan_no').val( challan );
	
	
} 
	
</script>
</head>
<body onLoad="set_hotkey()">
<? echo load_freeze_divs ("../../",$permission);  ?>

<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="800" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
    	<tr>
        <input type="text" id="selected_order_id">
        <input type="text" id="selected_challan_no">
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                    <thead>                	 
                        <th width="150">Company Name</th>
                        <th width="150">Party Name</th>
                        <th width="150">Process</th>
                        <th width="200">Date Range</th>
                        <th>
                        <input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" />
                        </th>           
                    </thead>
        			<tr cellpading='0'>
                    	<td>
							<? 
							echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "load_drop_down( 'requires/chalan_print_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );"); 
							?>
                    	</td>
                   		<td id="buyer_td">
                        	<? echo create_drop_down( "cbo_party_name", 150, $blank_array,"", 1, "-- Select Party --", $selected, "" ); 	 
						    ?>
                         </td>
                         <td>
							<? 
							echo create_drop_down( "cbo_process_name", 150, $production_process,"", 1, "--Select Process--",0,"", "","" );
							?>
                    	 </td>
                    	 <td>
                        	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					  		<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 	 </td> 
                         <td align="center">
                     		<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('cbo_process_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'chalan_print_list_view', 'chalan_print_list_view', 'requires/chalan_print_controller', 'setFilterGrid(\'chalan_table\',-1)')" style="width:100px;" />
                            
                         </td>
        			</tr>
             	</table>
          	</td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle">
				<? echo load_month_buttons(1);  ?>
            </td>
        </tr>
    </table>
    <table>
    	<tr>
            <td align="center" valign="top" id="chalan_print_list_view"> 
            </td>
       </tr>
    </table> 
    
   <div id="report_container" align="center"></div>  
     <div id="report_container2"></div>
    </form>
   </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
