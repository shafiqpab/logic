<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Trims Prodution and delivery summery
				
Functionality	:	
JS Functions	:
Created by		:	Wayasel Ahmed
Creation date 	: 	17-05-2023
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
echo load_html_head_contents("Trims Prodution and delivery summery","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";


	var tableFilters = 
	{
		col_operation: {
		id: ["prod_total","value_prod_bal_qty","del_total","value_trims_del_qty"],
		col: [8,9,10,11],
		operation: ["sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 

	function generate_report(report_type)
	{
			if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*From date*To date')==false )
			{
				return;
			}
		
		var data="action=generate_report&report_type="+report_type+"&report_title="+$( "div.form_caption" ).html()+get_submitted_data_string('cbo_company_id*cbo_location_name*cbo_section_id*cbo_customer_source*cbo_customer_name*txt_date_from*txt_date_to*cbo_ItemGroup_id*txt_style',"../../../");
		
		//alert(data);
		freeze_window(3);
		http.open("POST","requires/trims_prodution_and_delivery_summery_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//setFilterGrid("table_body_id",-1,'');
			if(reponse[2]==1)
			{
				setFilterGrid("table_body_id",-1,tableFilters);
			}

			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window(type)
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body_id tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		$('#table_body_id tr:first').show();
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="250px";
	}
	
	
</script>
</head>
<body onLoad="set_hotkey();">
  <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?>
     <h3 style="width:900px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:900px">  
             <form name="date_wise_production_1" id="date_wise_production_1" autocomplete="off" >    
                    <fieldset> 
                        <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                            <thead>
                                <th width="120" class="must_entry_caption">Company</th>
                                <th width="120">Location</th>
                                <th width="100">Section</th>
                                <th width="100">Style</th>
                                <th width="100">Customer Source</th>
                                <th width="100">Party</th>
                                <th width="100">Item Group</th>
                                <th colspan="2">Prod. Date</th>
                                <th colspan="2"><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('date_wise_production_1','','','','')" /></th>
                            </thead>
                            <tbody>
                            	<tr>
                                    <td>
										 <? 
                                       		 echo create_drop_down( "cbo_company_id",120,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/trims_prodution_and_delivery_summery_controller', this.value, 'load_drop_down_location', 'location_td');","","","","","",2);  ?>
                                    </td>
                                    <td id="location_td">
		                                <?php 
		                                    echo create_drop_down( 'cbo_location_name', 100, $blank_array, '', 1, '-- Select Location --', $selected, '', '', '', '', '', '', 2);
		                                ?>
		                            </td>
                                    <td>
										<? 
											 echo create_drop_down( "cbo_section_id", 100, $trims_section,"", 1, "-- All --","","load_sub_section(1)",0,'','','','','','',"");
                                        ?>
                                    </td>
                                    <td >
                                        <input name="txt_style" id="txt_style" type="text"  class="text_boxes" style="width:100px" placeholder="Write"/></td>
                                    <td>
										<? 
                                       		 echo create_drop_down( "cbo_customer_source",100,$yes_no,"", 1, "--All--",0, "load_drop_down( 'requires/trims_prodution_and_delivery_summery_controller', (document.getElementById('cbo_company_id').value+'_'+this.value), 'load_drop_down_buyer', 'buyer_td' )","","","","","",2);
                                        ?>
                                    </td>
                                    <td id="buyer_td">
										<? 
                                        	echo create_drop_down( "cbo_customer_name",100,$blank_array,"", 1, "--All--", $selected, "","","","","","",2);
                                        ?>
                                    </td>
                                     <td ><? echo create_drop_down( "cbo_ItemGroup_id", 100, "select id, item_name from lib_item_group where item_category=4 and status_active=1 order by item_name","id,item_name", 1, "-- Select --",$selected, "",0,'','','','','','',""); ?>	
                                    </td>
                                     <td width="90">
                                    	<input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:80px;"/>                    				</td>
                                    <td width="90">
                                    	<input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:80px;"/>                        		    </td>
                                    <td>
                                    <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:80px" class="formbutton" />
                                	</td>
                                </tr>
                                <tr>
                                    <td colspan="9" align="center"><? echo load_month_buttons(1);  ?></td>
                                </tr>
                        	<tbody>
                        </table> 
                    </fieldset>
              </form> 
          </div>
          <div id="report_container" align="center"></div>
          <div id="report_container2"></div> 
   </div>
</body>  
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>