<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Supplier List Report
				
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	15/12/2013
Updated by 		: 	Reza	
Update date		: 	11/10/2017	   
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
echo load_html_head_contents("Supplier List", "../../", 1, 1, $unicode,1,'');
?>	
<script>
var permission='<? echo $permission; ?>';
	

var tableFilters = 
		{
		
		
			col_operation: {
			id:["value_sizelbs_td","value_kni_td","value_first_in_rec_td","value_first_ins_td","value_issue_td","value_bal_td"],
			col: [9,10,11,12,13,14],
			operation: ["sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
			}
		
		}
	function openmypage_supplier()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		else
		{	
			var data = $("#cbo_company_id").val();
			var party = $("#cbo_party_type").val();
		
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/supplier_list_controller.php?data='+data+'_'+party+'&action=supplier_popup', 'Supplier Search', 'width=600px,height=420px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemailid=this.contentDoc.getElementById("supplier_id");
				var theemailval=this.contentDoc.getElementById("supplier_val");
				if (theemailid.value!="" || theemailval.value!="")
				{
					freeze_window(5);
					$("#txt_supplier").val(theemailval.value);
					$("#txt_supplier_id").val(theemailid.value);
					release_freezing();
				}
			}
		}
	}
	
	// function fn_report_generated(action_type)
	// {
	// 	if(form_validation('cbo_company_id','Company Name')==false)
	// 	{
	// 		return;
	// 	}
	// 	else
	// 	{	
	// 		var report_title=$( "div.form_caption" ).html();
	// 		var data="action="+action_type+get_submitted_data_string('cbo_company_id*cbo_party_type*txt_supplier_id',"../../")+'&report_title='+report_title;
	// 		freeze_window(3);
	// 		http.open("POST","requires/supplier_list_controller.php",true);
	// 		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	// 		http.send(data);
	// 		http.onreadystatechange = function(){
	// 			if(http.readyState == 4) 
	// 			{
	// 				var reponse=trim(http.responseText).split("****");
	// 				var tot_rows=reponse[2];
	// 				$('#report_container2').html(reponse[0]);
	// 				document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
	// 				show_msg('3');
	// 				release_freezing();
	// 				if(action_type=='report_generate')setFilterGrid('table_body',-1);	
	// 			}
			 	 
	// 		};
	// 	}
	// }
function fn_report_generated()
{
	if(form_validation('cbo_company_id','Company Name')==false)
	{
		return;
	}
	else
	{	
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_party_type*txt_supplier_id',"../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/supplier_list_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	
	}
}

function fn_report_generated_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split("****");
		// var tot_rows=reponse[2];
		$('#report_container2').html(reponse[0]);
		
		// document.getElementById('report_container').innerHTML=report_convert_button('../../'); $('#report_container4').html(reponse[0]);
		//document.getElementById('report_container3').innerHTML=report_convert_button('../../');
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
	
	$('#table_body tr:first').hide();
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_print.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close(); 
	$('#table_body tr:first').show();
	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="550px";

}		


function openmypage_owner_info(owner_name,owner_nid,owner_contact,owner_email)
    {
	
        owner_name*owner_nid*owner_contact*owner_email
      
    
       
     
        var title = 'Owner Information Form';    
        var page_link = 'requires/supplier_list_controller.php?owner_name='+owner_name+'&owner_nid='+owner_nid+'&owner_contact='+owner_contact+'&owner_email='+owner_email+'&action=owner_info';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=700px,height=370px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
            var owner_name=this.contentDoc.getElementById("owner_name").value;    //Access form field with id="emailfield"
            var owner_nid=this.contentDoc.getElementById("owner_nid").value;
            var owner_contact=this.contentDoc.getElementById("owner_contact").value;
            var owner_email=this.contentDoc.getElementById("owner_email").value;
            $('#owner_info').val(owner_name);
            $('#owner_name').val(owner_name);
            $('#owner_nid').val(owner_nid);
            $('#owner_contact').val(owner_contact);
            $('#owner_email').val(owner_email);
        }
    }
	
</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../");  ?>
        <form id="supplierList_1">
         <h3 style="width:950px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         
         <div id="content_search_panel" style="width:950px" >      
            <fieldset>  
                <table cellpadding="0" cellspacing="2" width="900px">
                    <tr>
                        <td width="90" align="right" class="must_entry_caption"><strong>Company </strong></td>
                        <td width="150">
							<? 
								echo create_drop_down( "cbo_company_id", 150, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select Company --", $selected,"" );
                            ?>
                        </td>
                        <td width="90" align="right"><strong>Party Type </strong></td>
                        <td width="145">
							<? 
								echo create_drop_down( "cbo_party_type", 145, $party_type_supplier, "", 0, "", '', 'set_value_supplier_nature(this.value)', $onchange_func_param_db,$onchange_func_param_sttc  ); 
                            ?>				
                        </td>
                        <td width="90" align="right"><strong>Supplier </strong></td>
                        <td width="140">
                        	<input type="text" name="txt_supplier" id="txt_supplier" class="text_boxes" style="width:140px" placeholder="Double Click" onDblClick="openmypage_supplier();" /><input type="hidden" name="txt_supplier_id" id="txt_supplier_id" class="text_boxes" style="width:140px" />
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="fn_report_generated('report_generate')" style="width:80px" class="formbutton" />
                            <input type="button" name="search" id="search" value="Party Wise" onClick="fn_report_generated('report_generate_party_wise')" style="width:80px" class="formbutton" />
                        </td>
                    </tr>
                </table>
                
            </fieldset>
            
            </div>
        </form>
        <div id="report_container" align="center"></div>
        <div id="report_container2"> </div>
    </div>
</body>
<script>
	set_multiselect('cbo_party_type','0*0','0','','0');
</script>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>