<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Export Lc Or Sc Statement For Custom
				
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
echo load_html_head_contents("Lc Or Sc Statement For Custom","../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	var tableFilters = 
	{
		col_30: "none",
		col_operation: {
		id: ["value_total_invoice_qnty","value_total_net_weight","value_total_gross_weight","value_total_invoice_value","value_total_cons_per_pcs","value_total_yarn_used"],
		col: [6,7,8,9,12,13],
		operation: ["sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
	}
	}
	function openmypage_lc_sc()
	{
		if( form_validation('cbo_company_name*cbo_buyer_name*cbo_search_by','Company Name*Buyer Name*Search By')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var cbo_search_by = $("#cbo_search_by").val();
		var cbo_buyer_name = $("#cbo_buyer_name").val();
		var page_link='requires/lc_sc_statement_custom_controller.php?action=lc_sc_search&company='+company+'&cbo_search_by='+cbo_search_by+'&cbo_buyer_name='+cbo_buyer_name; 
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var lc_id=this.contentDoc.getElementById("lc_id").value; // lc sc ID
			var lc_no=this.contentDoc.getElementById("lc_no").value; // lc sc no
			$("#txt_lc_sc").val(lc_no);
			$("#txt_lc_sc_id").val(lc_id);
		}
	}


	function generate_report(operation)
	{
		if( form_validation('cbo_company_name*txt_lc_sc','Company Name*Lc Sc')==false )
		{
			return;
		} 
		else
		{
			//alert("xx");
			var cbo_company_name = $("#cbo_company_name").val();
			var cbo_buyer_name = $("#cbo_buyer_name").val();
			var cbo_search_by = $("#cbo_search_by").val();
			var txt_lc_sc = $("#txt_lc_sc").val();
			var txt_lc_sc_id = $("#txt_lc_sc_id").val();	
			var report_title=$( "div.form_caption" ).html();
			
			var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_buyer_name="+cbo_buyer_name+"&cbo_search_by="+cbo_search_by+"&txt_lc_sc="+txt_lc_sc+"&txt_lc_sc_id="+txt_lc_sc_id+"&report_title="+report_title;
			var data="action=generate_report"+dataString;
			freeze_window(operation);
			http.open("POST","requires/lc_sc_statement_custom_controller.php",true);
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
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);
			//document.getElementById('report_container').innerHTML='<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("table_body",-1,tableFilters);
			release_freezing();
			show_msg('3');
			//document.getElementById('report_container').innerHTML=report_convert_button('../../');
		}
	} 

	function new_window()
	{
		 
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$('#table_body tr:first').hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			d.close();
			$('#table_body tr:first').show(); 
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
                        <th width="150">Buyer</th>                              
                        <th width="150">Search By</th>
                        <th width="150" class="must_entry_caption">LC/SC No</th>
                        <th width="100"><input type="reset" name="res" id="res" value="Reset" style="width:90px" class="formbutton" onClick="reset_field()" /></th>
                    </tr>
                </thead>
                <tr class="general">
                    <td align="center">
                            <?
                        	echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/lc_sc_statement_custom_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                        ?>                          
                    </td>
                    <td id="buyer_td" align="center">
						<? 
                            echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --", $selected, "",0,"" );
                        ?>
                    </td>
                    <td align="center">
                    <?
						$search_by_type=array(1=>"Export LC",2=>"Sales Contract");
						echo create_drop_down( "cbo_search_by", 162, $search_by_type,"", 0, "", 1, "" );
					?>
                    </td>
                    <td align="center">
                        <input  type="text" style="width:130px;"  name="txt_lc_sc" id="txt_lc_sc"  ondblclick="openmypage_lc_sc()"  class="text_boxes" placeholder="Dubble Click For LC/SC"  readonly />   
                        <input type="hidden" name="txt_lc_sc_id" id="txt_lc_sc_id"/>             
                    </td>
                    
                     
                    
                    <td align="center">
                        <input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:90px" class="formbutton" />
                    </td>
                </tr>
                
            </table> 
        </fieldset> 
           
    </div>
    <br /> 
    
        <!-- Result Contain Start-------------------------------------------------------------------->
        	<div id="report_container" align="center"></div>
            <div id="report_container2"></div> 
        <!-- Result Contain END-------------------------------------------------------------------->
    
    
    </form>    
</div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
