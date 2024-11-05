<?
/*-------------------------------------------- Comments

Purpose			: 	This form will Create Batch Reprot
Functionality	:	
JS Functions	:
Created by		:	Monir Hossain
Creation date 	: 	13-04-2016
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
echo load_html_head_contents("Batch Wise Dyeing Production Process Loss Reprot", "../../", 1, 1,'','','');
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

var tableFilters = 
	{
		col_30: "none",
		col_operation: {
		id: ["btg"],
		col: [12],
		operation: ["sum"],
		write_method: ["innerHTML"]
		}
	} 
function fn_report_generated(operation)
{
	var b_number=document.getElementById('batch_number').value;
	var batch_no=document.getElementById('batch_number_show').value;
		//alert(batch_number);job_number_show
	var order_no=document.getElementById('order_no').value;	
	var hidden_order=document.getElementById('hidden_order_no').value;	
	var hidden_ext=document.getElementById('hidden_ext_no').value;	
	var ext_no=document.getElementById('txt_ext_no').value;	
	var j_number=document.getElementById('job_number').value;	
	var job_number=document.getElementById('job_number_show').value;
	var repot_type=document.getElementById('cbo_type').value;		
	if(j_number!="" || job_number!="" || order_no!="" || hidden_order!="" || ext_no!="" || hidden_ext!="" || batch_no!="" || repot_type==2 || repot_type==3 || repot_type==4)
	{
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
	}
	else
	{
		if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*From date Fill*To date Fill')==false)
		{
			return;
		}
	}
			freeze_window(5);
		    var data="action=batch_report&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_buyer_name*job_number_show*job_number*batch_number*batch_number_show*txt_ext_no*hidden_ext_no*order_no*hidden_order_no*cbo_type*cbo_year*cbo_batch_type*txt_date_from*txt_date_to',"../../");
			//alert(data);
  			http.open("POST","requires/batch_wise_dyeing_production_process_loss_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_show_batch_report;
	}
	function fnc_show_batch_report()
	{
		if(http.readyState == 4) 
		{
			// alert(http.responseText);
			document.getElementById('report_container2').innerHTML=http.responseText;
			document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			setFilterGrid("table_body",-1);
			setFilterGrid("table_body2",-1);
			setFilterGrid("table_body3",-1);
			show_msg('3');
			release_freezing();
		}
 	}
<!--BatchNumber -->
function batchnumber()
{ 
	if(form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	var company_name=document.getElementById('cbo_company_name').value;
	var batch_number=document.getElementById('batch_number_show').value;
	var page_link="requires/batch_wise_dyeing_production_process_loss_report_controller.php?action=batchnumbershow&company_name="+company_name; 
	var title="Batch Number";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=530px,height=400px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("selected_id").value;
		var batch=theemail.split("_");
		document.getElementById('batch_number').value=batch[0];
		document.getElementById('batch_number_show').value=batch[1];
		release_freezing();
	}
}
<!--JobNumber -->
function jobnumber(id)
{ 
	if(form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	var company_name=document.getElementById('cbo_company_name').value;
	var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
	var year=document.getElementById('cbo_year').value;
	var batch_type=document.getElementById('cbo_batch_type').value;
	//alert(batch_type);
	var page_link="requires/batch_wise_dyeing_production_process_loss_report_controller.php?action=jobnumbershow&company_id="+company_name+"&cbo_buyer_name="+cbo_buyer_name+"&year="+year+"&batch_type="+batch_type;
	var title="Job Number";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=530px,height=420px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("selected_id").value;
		//var job=theemail.split("_");
		document.getElementById('job_number').value=theemail;
		document.getElementById('job_number_show').value=theemail;
		release_freezing();
	}
}
function openmypage_order(id)
{ 
	if(form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	var company_name=document.getElementById('cbo_company_name').value;
	var buyer_name=document.getElementById('cbo_buyer_name').value;
	var year=document.getElementById('cbo_year').value;
	var job_number=document.getElementById('job_number_show').value;
	var batch_number=document.getElementById('batch_number_show').value;
	var ext_number=document.getElementById('txt_ext_no').value;
	var year=document.getElementById('cbo_year').value;
	var batch_type=document.getElementById('cbo_batch_type').value;
	var page_link="requires/batch_wise_dyeing_production_process_loss_report_controller.php?action=order_number_popup&company_name="+company_name+"&buyer_name="+buyer_name+"&year="+year+"&batch_type="+batch_type;
	var title="Order Number";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=420px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("selected_id").value;
		//var job=theemail.split("_");
		document.getElementById('hidden_order_no').value=theemail;
		document.getElementById('order_no').value=theemail;
		release_freezing();
	}
}

function batch_extension()
{ 
	var company_name=document.getElementById('cbo_company_name').value;
	var buyer_name=document.getElementById('cbo_buyer_name').value;
	var year=document.getElementById('cbo_year').value;
	var job_number=document.getElementById('job_number_show').value;
	var batch_number=document.getElementById('batch_number_show').value;
	var batch_number_hidden=document.getElementById('batch_number').value;
	if(form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	var company_name=document.getElementById('cbo_company_name').value;
	var page_link="requires/batch_wise_dyeing_production_process_loss_report_controller.php?action=batchextensionpopup&company_name="+company_name+"&buyer_name="+buyer_name+"&year="+year+"&job_number_show="+job_number_show+"&batch_number_show="+batch_number_show+"&batch_number_hidden="+batch_number_hidden;
	//var page_link="requires/batch_report_controller.php?action=batchextensionpopup&company_name="+company_name; 
	var title="Extention Number";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=400px,center=1,resize=0,scrolling=0','../')

	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("selected_id").value;
		var batch=theemail.split("_");
		document.getElementById('txt_ext_no').value=batch[1];
		release_freezing();
	}
}

	function toggle( x, origColor ) {
		var newColor = 'green';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}
		
function js_set_value( str ) {
	toggle( document.getElementById( 'tr_' + str), '#FFF' );
}
</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",''); ?>
		 <form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1"> 
         <h3 style="width:650px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
             <fieldset style="width:617px;">
                 <table class="rpt_table" width="35%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th width="9%" class="must_entry_caption">Company Name</th>
                            <th width="6%">Buyer</th>
                            <th width="7%">File No </th>
                            <th width="11%">Internal Ref
</th>
                            <th width="9%">Batch No</th>
                            <th width="8%"><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailyYarnStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:100px" /></th>
                        <td width="50%"></thead>
                        <tbody>
                            <tr>
                                 
                                <td> 
                                    <?

                                echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "- Select Company -", $selected, "load_drop_down( 'requires/batch_wise_dyeing_production_process_loss_report_controller', this.value, 'load_drop_down_buyer', 'cbo_buyer_name_td' );" );
                            ?>
                                    
                                </td>
                               
                                <td id="cbo_buyer_name_td">
                                	<?
										  echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "- All Buyer -", $selected, "",0,"" );
									?>
                                </td>
                                 <td id="extention_td">
                                	<input type="text"  name="file_no" id="file_no" class="text_boxes" style="width:100px;" tabindex="1" placeholder="Write" onDblClick="">
                                </td>
                               
                                <td>
                                     <input type="text"  name="internal_ref" id="internal_ref" class="text_boxes" style="width:100px;" tabindex="1" placeholder="Write" onDblClick="">
                                     <input type="hidden" name="job_number" id="job_number">
                                 </td>
                                <td>
                                     <input type="text"  name="batch_number_show" id="batch_number_show" class="text_boxes" style="width:100px;" tabindex="1" placeholder="Write/Browse" onDblClick="batchnumber();">
                                     <input type="hidden" name="batch_number" id="batch_number">
                                </td>
                                <td><input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated()" /></td>
                            </tr>
                            
                        </tbody>
                    </table>
                    <table>
            	
            </table> 
            <br />
                </fieldset>
            </div>
            <div id="report_container" style="width:1100px; margin:0 auto;"></div>
    		<div id="report_container2" style="width:1100px; margin:0 auto; text-align:center;"></div>
		</form>
	</div>
    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>