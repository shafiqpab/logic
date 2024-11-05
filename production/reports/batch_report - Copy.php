<?
/*-------------------------------------------- Comments

Purpose			: 	This form will Create Batch Reprot
					
Functionality	:	
				

JS Functions	:

Created by		:	Saidul Reza
Creation date 	: 	21-01-2014
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
echo load_html_head_contents("Daily Knitting Production Report", "../../", 1, 1,'','','');

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
	var j_number=document.getElementById('job_number').value;	

	if(b_number!="" || j_number!="")
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
		    var data="action=batch_report&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_buyer_name*job_number*batch_number*txt_date_from*txt_date_to',"../../");
  			http.open("POST","requires/batch_report_controller.php",true);
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

			setFilterGrid("table_body",-1,tableFilters);
			release_freezing();
			}
 	}



	
	
<!--BatchNumber -->
function BatchNumber()
{ 
	
	if(form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	var company_name=document.getElementById('cbo_company_name').value;
	var page_link="requires/batch_report_controller.php?action=BatchNumberShow&company_id="+company_name; 
	var title="Batch Number";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=400px,center=1,resize=0,scrolling=0','../')

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
function JobNumber(id)
{ 
	
	if(form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	var company_name=document.getElementById('cbo_company_name').value;
	var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
	var page_link="requires/batch_report_controller.php?action=JobNumberShow&company_id="+company_name+"&cbo_buyer_name="+cbo_buyer_name;
	var title="Job Number";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=715px,height=420px,center=1,resize=0,scrolling=0','../')

	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("selected_id").value;
		//var job=theemail.split("_");
		document.getElementById('job_number').value=theemail;
		document.getElementById('job_number_show').value=theemail;
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
         <h3 style="width:1000px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
             <fieldset style="width:1000px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th class="must_entry_caption">Company Name</th>
                            <th>Buyer</th>
                            <th>Job No</th>
                            <th>Batch No</th>
                            <th class="must_entry_caption">Batch Date</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailyYarnStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:100px" /></th>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_company_name", 170, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down('requires/batch_report_controller', this.value, 'load_drop_down_location', 'cbo_buyer_name_td' );" );
                                    ?>
                                </td>
                                <td id="cbo_buyer_name_td">
                                	<?
                                        echo create_drop_down( "cbo_buyer_name", 150, "select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 order by short_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
									?>
                                </td>
                                <td>
                                     <input type="text" readonly name="job_number_show" id="job_number_show" class="text_boxes" style="width:120px;" tabindex="1" placeholder="Double Click to Search" onDblClick="JobNumber();">
                                     <input type="hidden" name="job_number" id="job_number">
                                 </td>
                                <td>
                                     <input type="text" readonly name="batch_number_show" id="batch_number_show" class="text_boxes" style="width:120px;" tabindex="1" placeholder="Double Click to Search" onDblClick="BatchNumber();">
                                     <input type="hidden" name="batch_number" id="batch_number">
                                </td>
                                <td align="center">
                                     <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:90px" placeholder="From Date"/>
                                     &nbsp;To&nbsp;
                                     <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:90px" placeholder="To Date"/>
                                </td>
                                <td><input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated()" /></td>
                            </tr>
                            <tr>
                                <td colspan="6" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
    <div id="report_container" style="width:1100px; margin:0 auto;"></div>
    <div id="report_container2" style="width:1100px; margin:0 auto; text-align:center;"></div>
</body>


<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>