<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Party Wise Yarn Reconciliation V2
				
Functionality	:	
JS Functions	:
Created by		:	Abu Sayed
Creation date 	: 	20-10-2022
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
$user_id=$_SESSION['logic_erp']['user_id'];
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Party Wise Yarn Reconciliation V2","../../../", 1, 1, $unicode,1,0); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(type)
	{
        if( form_validation('cbo_company_name*cbo_knitting_source*txt_knitting_company','Company Name*Source*Knitting Company')==false)
        {
            return;
        }
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_knitting_source*txt_knitting_com_id*cbo_issue_purpose*txt_production_id',"../../../")+'&report_title='+report_title+'&type='+type;
		//alert(data);
		freeze_window(3);
		http.open("POST","requires/party_wise_yarn_reconciliation_v2_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}

	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>&nbsp;&nbsp;<input type="button" onclick="generate_pdf_report(1)" value="Pdf Export" name="Pdf" class="formbutton" style="width:100px"/>';
			
			show_msg('3');
			release_freezing();
		}
	} 

	function generate_pdf_report(type)
	{
		
		var path=1;
		$report_title=$( "div.form_caption" ).html();
		var data="action=pdf_generate"+get_submitted_data_string('cbo_company_name*cbo_knitting_source*txt_knitting_com_id*cbo_issue_purpose*txt_production_id',"../../../")+'&report_title='+$report_title+'&type='+type+'&path='+path;

		if(type==1)
		{
			freeze_window(5);
			var user_id = "<? echo $user_id; ?>";
			$.ajax({
				url: 'requires/party_wise_yarn_reconciliation_v2_controller.php',
				type: 'POST',
				data: data,
				success: function(data){
					window.open('requires/'+user_id+'.pdf');
					release_freezing();
				}
			});
			
		}
		
	}
	

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		//$("#table_body tr:first").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		//document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="250px";
	}
	
	function openmypage_party()
	{
		if( form_validation('cbo_company_name*cbo_knitting_source','Company Name* Knitting source')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var cbo_knitting_source = $("#cbo_knitting_source").val();
		var txt_knit_comp_id = $("#txt_knit_comp_id").val();
		var page_link='requires/party_wise_yarn_reconciliation_v2_controller.php?action=party_popup&companyID='+companyID+'&cbo_knitting_source='+cbo_knitting_source+'&txt_knit_comp_id='+txt_knit_comp_id;
		var title='Party Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=430px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var party_name=this.contentDoc.getElementById("hide_party_name").value;
			var party_id=this.contentDoc.getElementById("hide_party_id").value;
			
			$('#txt_knitting_company').val(party_name);
			$('#txt_knitting_com_id').val(party_id);	 
		}
	}

    function openmypage_production()
	{
		if( form_validation('cbo_company_name*cbo_knitting_source*txt_knitting_company','Company Name*Knitting source*Knitting Company')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var cbo_knitting_source = $("#cbo_knitting_source").val();
		var txt_knit_comp_id = $("#txt_knitting_com_id").val();
		var page_link='requires/party_wise_yarn_reconciliation_v2_controller.php?action=knitting_production_popup&companyID='+companyID+'&cbo_knitting_source='+cbo_knitting_source+'&txt_knit_comp_id='+txt_knit_comp_id;
		var title='Party Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=430px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hide_production=this.contentDoc.getElementById("hide_production").value;
			var production_id=this.contentDoc.getElementById("hide_production_id").value;
			
			$('#txt_production').val(hide_production);
			$('#txt_production_id').val(production_id);	 
		}
	}
	
	function kniting_company_val()
	{
		$('#txt_knitting_company').val('');
		$('#txt_knitting_com_id').val('');	 
        $('#txt_production').val('');
		$('#txt_production_id').val('');	 
	}
	
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../../",$permission); ?><br />    		 
    <form name="PartyWiseYarnReconciliation_2" id="PartyWiseYarnReconciliation_2" autocomplete="off" > 
        <div style="width:100%;" align="center">
            <fieldset style="width:600px;">
            <legend>Search Panel</legend> 
                <table class="rpt_table" width="590" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="140" class="must_entry_caption">Company</th>                                
                            <th width="130" class="must_entry_caption">Source</th>
                            <th width="100">Issue Purpose</th>
                            <th width="140" class="must_entry_caption">Party</th>
                            <th width="80" >Rcv.Challan</th>
                            <th>  <input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_form('PartyWiseYarnReconciliation_2', 'report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
                            <? 
                               echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "print_button_setting();" );
                            ?>                            
                        </td>
                        <td>
							<?
                                echo create_drop_down("cbo_knitting_source",130,$knitting_source,"", 1, "-- Select Source --", 0,"kniting_company_val();",0,'1,3');
                            ?>
                        </td>
                        <td width="">
                            <?
                            echo create_drop_down("cbo_issue_purpose", 100, $yarn_issue_purpose, "", 1, "-- Select Purpose --", $selected, "", 1, "1", "", "", "");
                            ?>
                        </td>             
                                        
                        <td id="knitting_com">
                            <input type="text" id="txt_knitting_company" name="txt_knitting_company" class="text_boxes" style="width:100px" onDblClick="openmypage_party();" placeholder="Browse Party" readonly/>
                            <input type="hidden" id="txt_knitting_com_id" name="txt_knitting_com_id" class="text_boxes" style="width:60px" />
                        </td>
                        <td>
                            <input type="text" id="txt_production" name="txt_production" class="text_boxes" style="width:80px" onDblClick="openmypage_production();" placeholder="Browse" readonly/>
                            <input type="hidden" id="txt_production_id" name="txt_production_id" class="text_boxes_numeric" style="width:60px" />
                        </td>
                        <td><input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:100px" class="formbutton" /></td>
                       
                    </tr>
                </table>
            </fieldset>  
            
            <div id="report_container" align="center"></div>
        	<div id="report_container2"></div>
			<div style="display:none" id="data_panel"></div>
        </div>
    </form>    
</div>    
</body>  
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
