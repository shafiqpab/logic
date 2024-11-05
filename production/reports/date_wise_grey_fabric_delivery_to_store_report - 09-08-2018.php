<?
/*-------------------------------------------- Comments
Purpose			: 	This form will Create Date Wise Grey Fabric Delivery to Store Report
Functionality	:	
JS Functions	:
Created by		:	Kaiyum
Creation date 	: 	17-01-2018
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
echo load_html_head_contents("Date Wise Grey Fabric Delivery to Store Report", "../../", 1, 1,'','','');

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	function fn_report_generated(report_type)
	{
		
		
		if($('#cbo_company_name').val()==0){
			var data='cbo_working_company_id*txt_date_from';	
			var filed='Working Company Name*From Date';	
		}
		else
		{
			var data='cbo_company_name*txt_date_from';	
			var filed='Company Name*From Date';	
		}
		
		
		
		if( form_validation(data,filed)==false )
		{
			return;
		}
		else
		{	
		
		var report_title=$( "div.form_caption" ).html();
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_knitting_source*txt_date_from*txt_date_to*cbo_working_company_id',"../../")+'&report_title='+report_title+'&report_type='+report_type;
		//alert(data);return;
		freeze_window(5);
		http.open("POST","requires/date_wise_grey_fabric_delivery_to_store_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
		}
	}
	
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");

			$("#report_container2").html(response[0]); 
			//alert (response[0]);
			//document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
	 		show_msg('3');
			release_freezing();
		}
		// setFilterGrid("table_body", -1);
	}
		
	function new_window(type)
	{
	
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		if(type!=1)
		{
		document.getElementById('scroll_body1').style.overflow="auto";
		document.getElementById('scroll_body1').style.maxHeight="none";
		}
		//alert(type);
 		//$("tr th:first-child").hide();
		//$("tr td:first-child").hide();
		//$("#summary_tab tr th:first-child").show();
		//$("#summary_tab tr td:first-child").show();
		
		//$("#fill_td th:first-child").show();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="none";
		if(type!=1)
		{
		document.getElementById('scroll_body1').style.overflowY="scroll";
		document.getElementById('scroll_body1').style.maxHeight="none";
		
		}
		$("tr th:first-child").show();
		$("tr td:first-child").show();
	}



function fn_disable_com(str){
		if(str==2){$("#cbo_company_name").attr('disabled','disabled');}
		else{ $('#cbo_company_name').removeAttr("disabled");}
		if(str==1){$("#cbo_working_company_id").attr('disabled','disabled');}
		else{ $('#cbo_working_company_id').removeAttr("disabled");}
	}
	 		
</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",''); ?>
		 <form name="dateWiseFabDelvStoreReport_1" id="dateWiseFabDelvStoreReport_1"> 
         <h3 style="width:800px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')"> -Search Panel</h3> 
         <div id="content_search_panel" >      
             <fieldset style="width:800px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th class="must_entry_caption" width="130">Company Name</th>
                            <th width="100">Knitting Source</th>
                            <th class="must_entry_caption" width="150">Working Company</th>
                            <th class="must_entry_caption" width="170" colspan="2">Delivery Date Range</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dateWiseFabDelvStoreReport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px" /></th>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                                    ?>
                                </td>
                                <td>
                                	<?
										echo create_drop_down("cbo_knitting_source",150,$knitting_source,"", 1, "-- All --", 0,"load_drop_down( 'requires/date_wise_grey_fabric_delivery_to_store_report_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_knitting_com','knitting_com');",0,'1,3');
									?>
                                </td>
                                <td id="knitting_com" width="150" align="center"> 
			                        <?
			                           // echo create_drop_down( "cbo_working_company_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
									    echo create_drop_down( "cbo_working_company_id", 150, $blank_array,"", 1, "-- Select Company --", $selected, "" );
			                        ?>
                      			</td>
                                <td align="center">
                                     <input type="text" name="txt_date_from" id="txt_date_from" value=" <? echo date("d-m-Y", time() - 86400); ?>" class="datepicker" style="width:70px" placeholder="From Date"/>
                                </td>
                                <td>    
                                     <input type="text" name="txt_date_to" id="txt_date_to" value=" <? echo date("d-m-Y", time() - 86400); ?>" class="datepicker" style="width:70px" placeholder="To Date"/>
                                </td>
                                <td>
                                <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated(1)" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="11" align="center"><? echo load_month_buttons(1); ?></td>
                               
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
            <div style="width:100%;margin-top:10px;">
                <!--<input type="button" value="Delivery Challan" name="generate" id="generate" class="formbutton" style="width:150px" onClick="generate_delivery_challan_report()"/>-->
            </div>
            <br>
		</form>
	</div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$('#cbo_floor_id').val(0);
</script>
</html>