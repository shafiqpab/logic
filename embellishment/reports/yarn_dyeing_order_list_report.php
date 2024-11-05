<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Date Wise Embroidery Bill Status Report.
Functionality	:	
JS Functions	:
Created by		:	Wayasel Ahmmed
Creation date 	: 	5-02-2023
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
echo load_html_head_contents("Yarn Dyeing Order List", "../../", 1, 1,$unicode,1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
			
	var tableFilters = 
	{
		col_operation: {
		   id: ["tot_job_qty","tot_mat_rcv","tot_prod_qty","tot_del_qty","tot_wip_qty","tot_ex_cutting","tot_delivery_qty_dzn","tot_value"],
		   col: [6,7,8,9,10,11,12,14],
		   operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
		   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}	
	}
	
	function fn_report_generated(type)
	{
		if (form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*From date*To date')==false)
		{
			return;
		}

		var report_title=$( "div.form_caption" ).html();
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*txt_job_no*cbo_party_id*cbo_within_group*cbo_pro_type*cbo_order_type*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;			
		
		freeze_window(3);
		http.open("POST","requires/yarn_dyeing_order_list_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function fn_report_generated_reponse()
		{			
			if(http.readyState == 4) 
			{   
				show_msg('3');
				var reponse=trim(http.responseText).split("**"); 
				$('#report_container2').html(reponse[0]);
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				setFilterGrid("table_body",-1,tableFilters);
				release_freezing();
			}
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		$("#table_body tr:first").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="350px";
		$("#table_body tr:first").show();
	}

    function yd_print_report(update_id,cbo_company_name,cbo_within_group)
    {   
            var action = 'yarn_dyeing_order_entry_print';
            var data  = cbo_company_name+'*'+update_id+'*'+cbo_within_group;
            window.open("../../yarn_dyeing/order_material/requires/yd_order_entry_controller.php?data=" + data+'&action='+action, true );
     }
</script>
</head>
<body onLoad="set_hotkey();">
<form>
    <div style="width:1180px;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:900px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:900px;">
            <table class="rpt_table" width="900px" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>                    
                    <th width="140" class="must_entry_caption">Company</th>
                    <th width="100">Within Group</th>
                    <th width="100">Job Number</th>
                    <th width="130">Buyer Name </th>                 
                    <th width="100">Prod. Type</th>
                    <th width="100">Order Type</th>
                    <th colspan="2" width="150" class="must_entry_caption">Date</th>
                    <th width=""><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                </thead>
                <tbody>
                    <tr >
                        <td> 
                        <? echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, ""); ?>
                    	</td>
						<td>
                        <?php echo create_drop_down( "cbo_within_group", 80, $yes_no,"", 1, "-- Select --", '', "load_drop_down( 'requires/yarn_dyeing_order_list_report_controller', $('#cbo_within_group').val()+'_'+$('#cbo_company_id').val(), 'load_drop_down_buyer', 'buyer_td' );" ); ?>
                    </td>
                        <td >
                            <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px;" />
                        </td>
						<td id="buyer_td">
                        <?
						 echo create_drop_down( "cbo_party_id", 100, $blank_array,"", 1, "-- Select Party --",'', "" );      
                        ?>
                    </td>
                        <td >
                            <?     
                            echo create_drop_down("cbo_pro_type", 150, $w_pro_type_arr,"", 1, "-- Select Type --",$selected,"", "","","","","",7 ); 
                            ?>
                        </td>                                      
                        <td>
                            <?php
                             echo create_drop_down("cbo_order_type", 150, $w_order_type_arr,"", 1, "-- Select Type --",$selected,"fnc_load_order_type(this.value);", "","","","","",7 ); 
                            ?>
                        </td>                       
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" readonly>
                        </td>
                        <td>
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" readonly>
                        </td>
                        <td align="center">
                            <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_report_generated(1)" />
                           
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="9" align="center">
							<? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </tfoot>
           </table> 
        </fieldset>
    </div>
    </div>
    <div id="report_container" align="center" style="padding: 5px;"></div>
    <div id="report_container2" align="left"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
