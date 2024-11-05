<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Budget Variance Report [History]
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	01-02-2022
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
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Budget Variance Report [History]", "../../", 1, 1,$unicode,1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';	

	function fn_report_generated(rpttype)
	{
		freeze_window(3);
		if(form_validation('cbo_company_id*txt_job_no*txt_styleref','Company Name*Job No*Style Ref.')==false)
		{
			release_freezing();
			return;
		}
		else
		{	
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_year*txt_job_no*hidd_job_id*txt_styleref',"../../")+'&report_title='+report_title+'&rpttype='+rpttype;
			
			http.open("POST","requires/budget_variance_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####");
			var totRow=reponse[2];
			$('#report_container2').html(reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			//$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;';/*<input type="button" onclick="new_window('+totRow+');" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>*/
			//setc()
	 		show_msg('3');
			if(totRow>1)
			{
				var tableFilters = {
					col_operation: {
					  id: ["value_tdpo","value_tdbom","value_tdmargin"],
					  col: [6,7,9],
					  
					   operation: ["sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML"]
					}	
				}
				setFilterGrid("table_body",-1,tableFilters);
			}
			
			release_freezing();
		}
	}
	
	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
		}
	}
	
	function new_window(totRow)
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		if(totRow*1>1) $("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="400px";
		if(totRow*1>1) $("#table_body tr:first").show();
	}
	
	function fnRemoveHidden(str){
		document.getElementById(str).value='';
	}
	
	function openmypage_job()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		else 
		{	
			var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_id").val()+"_"+$("#cbo_year").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/budget_variance_report_controller.php?data='+data+'&action=job_no_popup', 'Job No Search', 'width=500px,height=420px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemailid=this.contentDoc.getElementById("txt_job_id").value;
				var theemailjob=this.contentDoc.getElementById("txt_job_no").value;
				var theemailstyle=this.contentDoc.getElementById("txt_styleref").value;
				//var response=theemailid.value.split('_');
				if ( theemailid!="" )
				{
					//alert (response[0]);
					freeze_window(5);
					$("#hidd_job_id").val(theemailid);
					$("#txt_job_no").val(theemailjob);
					$("#txt_styleref").val(theemailstyle);
					release_freezing();
				}
			}
		}
	}
	
	function openmypage_image(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{

		}
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
<form id="budgetvariancereport_1">
<div style="width:100%; margin:1px auto;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <h3 style="width:600px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
        <div id="content_search_panel" style="width:600px" > 		 
            <fieldset style="width:600px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>                    
                    <th width="130" class="must_entry_caption">Company Name</th>
                    <th width="130">Buyer</th>
                    <th width="60">Job Year</th>
                    <th width="80" class="must_entry_caption">Job No.</th>
                    <th width="110" class="must_entry_caption">Style Ref.</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('budgetvariancereport_1','report_container*report_container2','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td><?=create_drop_down( "cbo_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down('requires/budget_variance_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                        <td id="buyer_td"><?=create_drop_down( "cbo_buyer_id", 130, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id    and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, ""); ?></td>
                        <td><?=create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
                        <td>
                        	<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:68px" placeholder="Browse" onChange="fnRemoveHidden('hidd_job_id')" onDblClick="openmypage_job();" readonly />
                        	<input type="hidden" id="hidd_job_id" name="hidd_job_id" style="width:60px" />
                        </td>
                        <td>
                        	<input type="text" name="txt_styleref" id="txt_styleref" class="text_boxes" style="width:98px" placeholder="Browse" onChange="fnRemoveHidden('hidd_job_id')" onDblClick="openmypage_job();" readonly />
                        </td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(0);" /></td>
                    </tr>
                </tbody>
            </table> 
        	</fieldset>
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2" align="left"></div>
    </div>
</form> 
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
