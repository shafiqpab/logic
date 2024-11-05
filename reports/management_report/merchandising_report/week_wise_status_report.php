<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create week wise Status Report
				
Functionality	:	
JS Functions	:
Created by		:	Monzu 
Creation date 	: 	02/01/2015
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
echo load_html_head_contents("Order Info","../../../", 1, 1, $unicode);
?>	

<script>
var permission='<? echo $permission; ?>';

var tableFilters = 
	{
		col_operation: 
		{
			id: ["total_order_qnty","total_order_qnty_pcs","value_total_order_value","value_total_commission","value_total_net_order_value","value_total_ex_factory_qnty","total_short_access_qnty","total_over_access_qnty","value_total_short_access_value","value_total_over_access_value","value_total_over_access_value"],
			col: [12,14,16,17,18,19,20,21,22,23,24],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		} , 
		col_28: "select",
		col_31: "select",
	}	
function generate_report_main(e)
	{
			if (e!=13) {var unicode=e.keyCode? e.keyCode : e.charCode } else {unicode=13;}
			var inn=document.getElementById('fillter_check').value;
			if(inn=='')
			{
				generate_report('report_container2',1)
			}
			if(inn==1)
			{
				show_inner_filter(unicode);
			}
	}
		
function generate_report(type,week_pad){
	document.getElementById('report_container2').innerHTML="";
	var txt_date_from=document.getElementById('txt_date_from').value;
	var txt_date_to=document.getElementById('txt_date_to').value;
	var cbo_year_selection=document.getElementById('cbo_year_selection').value;
	var job_no=document.getElementById('txt_job_no').value;
	var cbo_company_name=document.getElementById('cbo_company_name').value;
	var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
	var cbo_order_status=2;
	var cbo_team_name=document.getElementById('cbo_team_name').value;
	var cbo_team_member=document.getElementById('cbo_team_member').value;
	var cbo_category_by=document.getElementById('cbo_category_by').value;
	var job_no=document.getElementById('txt_job_no').value;
	var cbo_year_selection=document.getElementById('cbo_year_selection').value;
	var data=cbo_company_name+"_"+cbo_buyer_name+"_"+txt_date_from+"_"+txt_date_to+"_"+cbo_order_status+"_"+cbo_team_name+"_"+cbo_team_member+"_"+cbo_category_by+"_"+cbo_year_selection+"_"+job_no;
	
	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari
	xmlhttp=new XMLHttpRequest();
	}
	else
	{// code for IE6, IE5
	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	

	freeze_window(3);
	xmlhttp.onreadystatechange=function()
	{
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
			var response=(xmlhttp.responseText).split('####');	
			document.getElementById('report_container2').innerHTML=response[0];
			document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
			append_report_checkbox('table_header_1',1);
			setFilterGrid("table-body",-1,tableFilters);
			release_freezing();
		}
	}
	xmlhttp.open("GET","requires/week_wise_status_report_controller.php?data="+data+"&type="+type+"&week_pad="+week_pad,true);
	xmlhttp.send();
}
	
	
	
function generate_report1(){
	var stype=1;
	var myColValues=TF_GetColValues("table-body",28);
	myColValues="'"+myColValues.join()+"'";
	var txt_date_from=document.getElementById('txt_date_from').value;
	var txt_date_to=document.getElementById('txt_date_to').value;
	if (stype==1) // main call
	{
		document.getElementById('report_container2').innerHTML="";
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var cbo_order_status=2;
		var cbo_team_name=document.getElementById('cbo_team_name').value;
		var cbo_team_member=document.getElementById('cbo_team_member').value;
		var cbo_category_by=document.getElementById('cbo_category_by').value;
		var cbo_year_selection=document.getElementById('cbo_year_selection').value;
		
		var data=cbo_company_name+"_"+cbo_buyer_name+"_"+txt_date_from+"_"+txt_date_to+"_"+cbo_order_status+"_"+cbo_team_name+"_"+cbo_team_member+"_"+cbo_category_by+'_'+myColValues+'_'+cbo_year_selection;
		//alert(data)
	}
	
	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari
	xmlhttp=new XMLHttpRequest();
	}
	else
	{// code for IE6, IE5
	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function()
	{
	if (xmlhttp.readyState==4 && xmlhttp.status==200)
	{
	var response=(xmlhttp.responseText).split('####');	
	document.getElementById('report_container2').innerHTML=response[0];
	document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
	append_report_checkbox('table_header_1',1);
		setFilterGrid("table-body",-1,tableFilters);
		document.getElementById('content_summary3_panel').innerHTML=document.getElementById('shipment_performance').innerHTML
	}
	}
	xmlhttp.open("GET","requires/week_wise_status_report_controller.php?data="+data+"&type=report_generate",true);
	xmlhttp.send();
}
	
function percent_set()
{
	var tot_row=document.getElementById('tot_row').value;
	var tot_value_js=document.getElementById('total_value').value;
	for(var i=1;i<tot_row;i++)
	{
		var value_js=document.getElementById('value_'+i).value;
		var percent_value_js=((value_js*1)/(tot_value_js*1))*100
		document.getElementById('value_percent_'+i).innerHTML=percent_value_js.toFixed(2);
	}
}
function openmypage_image(page_link,title)
{
	//alert("monzu");
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../../')
	emailwindow.onclose=function()
	{
	}
}

function print_report_part_by_part(id,button_id)
{
	//javascript:window.print()
		//$('#data_panel').html( http.responseText );
		 $(button_id).removeAttr("onClick").attr("onClick","javascript:window.print()");
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title></head><body>'+document.getElementById(id).innerHTML+'</body</html>');
		
		d.close();
		 $(button_id).removeAttr("onClick").attr("onClick","print_report_part_by_part("+id,button_id+")");
	
}
function openmypage_job()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		//var cbo_year_id = $("#cbo_year").val();
		//var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/week_wise_status_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_job_no').val(job_no);
			$('#txt_job_id').val(job_id);	 
		}
	}
	
	function generate_ex_factory_popup(action,country_id,id,width)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/week_wise_status_report_controller.php?action='+action+'&country_id='+country_id+'&id='+id, 'Ex-Factory Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}		
</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../");  ?>
   <h3 align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')"> -Search Panel</h3>
       <div id="content_search_panel"> 
       
            <form>
                <fieldset style="width:98%;">
                    <div  style="width:100%" align="center">
                            <table class="rpt_table" width="1300" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <thead>
                                    <tr>
                                        <th colspan="9"><font size="3"></font></th>
                                    </tr>
                                    <tr>
                                        <th>Company</th>
                                        <th>Buyer</th>
                                        <th>Job No.</th>
                                        <th>Team</th>
                                        <th>Team Member</th>
                                        <th colspan="2">Date</th>
                                        <th>Date Category</th>
                                        <th><input type="reset" name="reset" id="reset" value="Reset" style="width:80px" class="formbutton" /></th>
                                    </tr>
                                </thead>
                                <tr>
                                    <td>
                                           <?
                                           echo create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, " load_drop_down( '../merchandising_report/requires/week_wise_status_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' )" );
                                            ?> 
                                    </td>
                                    <td id="buyer_td">
                                     <? 
                                        echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --", $selected, "" );
                                     ?>	
                                    </td>
                                     <td>
                                        <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes_numeric" style="width:100px" onDblClick="openmypage_job();" placeholder="Write/Browse Job" />
                                        <input type="hidden" id="txt_job_id" name="txt_job_id" class="text_boxes"/>
                    				</td>
                                    <td >                
                                    
                                    <?
                                           echo create_drop_down( "cbo_team_name", 130, "select id,team_name from lib_marketing_team  where status_active =1 and is_deleted=0  order by team_name","id,team_name", 1, "-- Select Team --", $selected, " load_drop_down( '../merchandising_report/requires/week_wise_status_report_controller', this.value, 'load_drop_down_team_member', 'team_td' )" );
                                            ?>
                                    </td>
                                    <td id="team_td">
                                    <div id="div_team">
                                    <? 
                                        echo create_drop_down( "cbo_team_member", 172, $blank_array,"", 1, "- Select Team Member- ", $selected, "" );
                                     ?>	
                                    </div>
                                    </td>
                                    <td><input name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:75px">
                                    </td>
                                    <td><input name="txt_date_to" id="txt_date_to"  class="datepicker" style="width:75px">
                                    </td>
                                    <td>
                                    <select name="cbo_category_by" id="cbo_category_by"  style="width:130px" class="combo_boxes">
                                    <option value="1">Ship Date Wise </option>
                                    <option value="2">PO Rec. Date Wise </option>
                                    </select>
                                    </td>
                                    <td>
                                    <input type="button" name="search" id="search" value="Show" onClick="generate_report('report_generate',0)" style="width:80px" class="formbutton" />
                                    <input type="button" name="search" id="search" value="Show-2" onClick="generate_report('report_generate',1)" style="width:80px" class="formbutton" />
                                    <input name="fillter_check" id="fillter_check" type="hidden" >
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="8" align="center">
                                        <? echo load_month_buttons(1); ?>
                                    </td>
                                </tr>
                            </table>
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