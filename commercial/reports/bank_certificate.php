<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create PO Wise Invoice Report
				
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	24/03/2014
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
echo load_html_head_contents("Yarn Item Ledger","../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function openmypage_lc_sc()
	{
		if( form_validation('cbo_company_name*cbo_lien_bank','Company Name*Lien Bank')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var cbo_lien_bank = $("#cbo_lien_bank").val();
		var page_link='requires/bank_certificate_controller.php?action=lc_sc_search&company='+company+'&cbo_lien_bank='+cbo_lien_bank; 
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var lc_sc_id=this.contentDoc.getElementById("txt_selected_id").value; // lc sc ID
			var lc_sc_no=this.contentDoc.getElementById("txt_selected").value; // lc sc no
			var serial_no=this.contentDoc.getElementById("txt_selected_no").value; // Serial No
			var is_lc_sc=this.contentDoc.getElementById("is_lc_or_sc").value;// is lc sc
			$("#txt_lc_sc").val(lc_sc_no);
			$("#txt_lc_sc_id").val(lc_sc_id);
			$("#txt_lc_sc_no").val(serial_no); 
			$("#is_lc_or_sc").val(is_lc_sc); 
		}
	}


	function generate_report(operation)
	{
		if( form_validation('cbo_company_name*cbo_lien_bank*txt_lc_sc','Company Name*Lien Bamk*Lc Sc')==false )
		{
			return;
		} 
		else
		{
			//alert("xx");
			var cbo_company_name = $("#cbo_company_name").val();
			var cbo_lien_bank = $("#cbo_lien_bank").val();
			var txt_lc_sc = $("#txt_lc_sc").val();
			var txt_lc_sc_id = $("#txt_lc_sc_id").val();	
			var txt_reference = $("#txt_reference").val();
			var is_lc_or_sc = $("#is_lc_or_sc").val();
			
			var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_lien_bank="+cbo_lien_bank+"&txt_lc_sc="+txt_lc_sc+"&txt_lc_sc_id="+txt_lc_sc_id+"&is_lc_or_sc="+is_lc_or_sc+"&txt_reference="+txt_reference;
			var data="action=generate_report"+dataString;
			freeze_window(operation);
			http.open("POST","requires/bank_certificate_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_report_reponse; 
		}
	}

	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			//alert(http.responseText);	 
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			release_freezing();
			show_msg('3');
			//document.getElementById('report_container').innerHTML=report_convert_button('../../');
		}
	} 

	function new_window()
	{
		 
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			d.close(); 
			document.getElementById('scroll_body').style.overflow="auto"; 
			document.getElementById('scroll_body').style.maxHeight="250px";
	}
	
	
function reset_field()
{
	reset_form('stock_ledger_1','report_container2','','','','');
}
	
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
    <form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off" >
    <h3 align="left" id="accordion_h1" style="width:800px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div style="width:800px;" align="center" id="content_search_panel">
        <fieldset style="width:800px;">
        <legend>Search Panel</legend> 
                <table class="rpt_table" width="700" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th width="150" class="must_entry_caption">Company</th>                                
                        <th width="150" class="must_entry_caption">Lien Bank</th>
                        <th width="150" class="must_entry_caption">LC/SC No</th>
                        <th width="150" >Reference</th>
                        <th width="100"><input type="reset" name="res" id="res" value="Reset" style="width:90px" class="formbutton" onClick="reset_field()" /></th>
                    </tr>
                </thead>
                <tr class="general">
                    <td align="center">
                            <?
                        	echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                        ?>                          
                    </td>
                    <td align="center">
					<?
						echo create_drop_down( "cbo_lien_bank", 162, "select (bank_name || ' (' || branch_name || ')' ) as bank_name,id from lib_bank where is_deleted=0 and status_active=1 and lien_bank=1 order by bank_name","id,bank_name", 1, "-- Select Lien Bank --", 0, "" );
					?>
					</td>
                    <td align="center">
                        <input  type="text" style="width:130px;"  name="txt_lc_sc" id="txt_lc_sc"  ondblclick="openmypage_lc_sc()"  class="text_boxes" placeholder="Dubble Click For Item"  readonly />   
                        <input type="hidden" name="txt_lc_sc_id" id="txt_lc_sc_id"/>   <input type="hidden" name="txt_lc_sc_no" id="txt_lc_sc_no"/> <input type="hidden" name="is_lc_or_sc" id="is_lc_or_sc"/>           
                    </td>
                    
                     <td align="center">
                        <input type="text" style="width:130px;" name="txt_reference" id="txt_reference"   class="text_boxes"  /></td>
                    
                    <td align="center">
                        <input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:90px" class="formbutton" />
                    </td>
                </tr>
                
            </table> 
        </fieldset> 
           
    </div>
    <br /> 
    
        <!-- Result Contain Start-------------------------------------------------------------------->
        <fieldset style="width:700px;">
        	<div id="report_container" align="center"></div>
            <div id="report_container2"></div> 
        </fieldset>
        <!-- Result Contain END-------------------------------------------------------------------->
    
    
    </form>    
</div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
