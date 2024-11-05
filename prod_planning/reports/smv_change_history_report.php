<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Bundle Status Report.
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam Reza 
Creation date 	: 	24-08-2020
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:logout.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("SMV Change History Report","../../", 1, 1, $unicode,1,1);
?>	
<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	
	
	
	
	function fnc_report_generated()
	{
		
		if($('#txt_style_no').val()!=''){
			var validation='cbo_buyer_name';	
			var msg='Buyer Name';	
		}
		else
		{
			var validation='cbo_buyer_name*txt_date_from*txt_date_to';	
			var msg='Buyer Name*From Date*End Date';	
		}
		
		if(form_validation(validation,msg)==false)
		{
			return;
		}
		else
		{	
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_buyer_name*txt_date_from*txt_date_to*txt_job_no*hidden_job_id*txt_style_no',"../../")+'&report_title='+report_title;
			freeze_window(3);
			http.open("POST","requires/smv_change_history_report_controller.php",true);
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
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("tbl_list",-1,'');
	 		show_msg('3');
			release_freezing();
		}
	}

	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#scroll_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_print.css" type="text/css"  /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="350px";
		$('#scroll_body tr:first').show();
		
		
		//document.getElementById('scroll_body').style.maxWidth="120px";
	}

	function open_job_no()
	{	
		var buyer_name=$("#cbo_buyer_name").val();
		var cbo_year=$("#cbo_year").val();
		var page_link='requires/smv_change_history_report_controller.php?action=job_popup&buyer_name='+buyer_name+'&cbo_year='+cbo_year;
		var title="Search Job No Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			var job_no=this.contentDoc.getElementById("hide_job_no").value;

			$("#txt_job_no").val(job_no);
			$("#hidden_job_id").val(job_id); 
		}
	}
function open_style_ref()
{
	var buyer=$("#cbo_buyer_name").val();
	var page_link='requires/smv_change_history_report_controller.php?action=style_wise_search&buyer='+buyer; 
	var title="Search Style Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=520px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var poID=this.contentDoc.getElementById("txt_selected_id").value;
		var styleDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
		$("#txt_style_no").val(styleDescription);
		$("#hidden_order_id").val(poID); 
	}
}	

</script>
</head>
 
<body onLoad="set_hotkey();">
		 
<form id="date_wise_yarn_allocation">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:750px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:750px;">
                <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                	<thead>
                    	<tr>                   
                            <th>Buyer Name</th>
							<th>Job No</th>
                            <th>Style</th>
                            <th colspan="3" class="must_entry_caption">Entry Date</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:80px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                          <td>
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 180, "select id,buyer_name from lib_buyer  where  status_active =1 and is_deleted=0 $buyer_cond order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" ); 
                            ?>
                        </td>
						<td>
							<input type="text" id="txt_job_no"  name="txt_job_no"  style="width:100px" class="text_boxes" onDblClick="open_job_no()" placeholder="Browse" readonly />
							<input type="hidden" id="hidden_job_id"  name="hidden_job_id" />
                        </td> 
                        <td>
                            <input type="text" id="txt_style_no" name="txt_style_no" class="text_boxes" style="width:200px" onDblClick="open_style_ref()" placeholder="Browse/Write"  />
							<input type="hidden" id="hidden_style_id"  name="hidden_style_id" />
                        </td>
                        <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" ></td>
                        <td>To</td>
                        <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px"  placeholder="To Date" ></td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fnc_report_generated()" />
                        </td>
                    </tr>
                    </tbody>
                </table>
                <table>
                    <tr>
                        <td>
                            <? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </table> 
            </fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div align="center" id="report_container2"></div>
   
 </form>    
</body>
<script>
	set_multiselect('cbo_buyer_name','0','0','','0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>
