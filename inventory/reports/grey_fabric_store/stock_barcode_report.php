<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Closing Stock Report
				
Functionality	:
JS Functions	:
Created by		:
Creation date 	:
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
echo load_html_head_contents("Closing Stock Report","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var tableFilters = 
	{
		col_17: "none",
		col_operation: {
		id: ["value_tot_opening","value_tot_receive","value_tot_issue_return","value_tot_trans_in","value_total_receive","value_tot_issue","value_tot_rec_return","value_tot_transfer_out","value_total_issue","value_totalStock"],
		col: [6,7,8,9,10,11,12,13,14,15],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	function openmypage_fso()
	{
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/stock_barcode_report_controller.php?action=order_no_search_popup&companyID='+cbo_company_name,'Fso Popup', 'width=900px,height=370px,center=1,resize=0','../../')
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_data=this.contentDoc.getElementById("hidden_booking_data").value;
			if (order_data!="")
			{
				var exdata=order_data.split("**");
				$('#txt_fso_no').val(exdata[1]);
				$('#txt_fso_id').val(exdata[0]);	 
			}
		}
	}
	
	function generate_report(operation)
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		} 
		var report_title=$( "div.form_caption" ).html();
		var cbo_company_name = $("#cbo_company_name").val();
		var txt_fso_id = $("#txt_fso_id").val();
		var txt_fso_no = $("#txt_fso_no").val();
		
		var dataString = "&cbo_company_name="+cbo_company_name+"&txt_fso_id="+txt_fso_id+"&txt_fso_no="+txt_fso_no+"&report_title="+report_title;
		var data="action=generate_report"+dataString;
		//alert (data);
		freeze_window(operation);
		http.open("POST","requires/stock_barcode_report_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
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
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflow="scroll"; 
		document.getElementById('scroll_body').style.maxHeight="350px";
		
		$("#table_body tr:first").show();
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?>   		 
        <form name="closingstock_1" id="closingstock_1" autocomplete="off" > 
         <h3 style="width:720px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:720px" >      
            <fieldset>  
                <table class="rpt_table" width="700" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <th class="must_entry_caption">Company</th>
                        <th>FSO</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('closingstock_1','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td>
                                <?
                                    echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", "1", "" );
                                ?>                           
                            </td>
                            <td>
                            	<input style="width:140px;" name="txt_fso_no" id="txt_fso_no" class="text_boxes" onDblClick="openmypage_fso()" placeholder="Browse" readonly />
                                <input type="hidden" name="txt_fso_id" id="txt_fso_id" style="width:90px;"/>
                            </td>
                            <td>
                                <input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:100px" class="formbutton" />
                            </td>
                        </tr>
                    </tbody>
                </table> 
            </fieldset> 
            </div>
            <br /> 
                <div id="report_container" align="center"></div>
                <div id="report_container2"></div> 
        </form>    
    </div>
</body>  
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
