<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Export Import Coverage Report.
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	22-09-2016
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
echo load_html_head_contents("Export Import Coverage Report", "../../", 1, 1,'','','');
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
 
	var permission = '<? echo $permission; ?>';

	function generate_report()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();	
		var data="action=report_generate"+get_submitted_data_string("cbo_company_name","../../")+'&report_title='+report_title;
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/export_import_coverage_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			//alert(http.responseText);return;
			$('#report_container2').html(response[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		/*document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";*/
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		/*document.getElementById('scroll_body').style.overflowY="auto";
		document.getElementById('scroll_body').style.maxHeight="auto";*/
	}
	
	function openmypage_source()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company_id = $("#cbo_company_name").val();	
		var import_source = $("#import_source").val();
		var txt_lc_category = $("#txt_lc_category").val();
		var txt_serial_no = $("#txt_serial_no").val();
		var page_link='requires/export_import_coverage_report_controller.php?action=source_surch&company_id='+company_id+'&import_source='+import_source+'&txt_lc_category='+txt_lc_category+'&txt_serial_no='+txt_serial_no;  
		var title="Search Source";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=505px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var category_des=this.contentDoc.getElementById("txt_selected").value; 
			var category_id=this.contentDoc.getElementById("txt_selected_id").value; 
			var serial_no=this.contentDoc.getElementById("txt_selected_no").value; 
			//alert(style_des_no);
			$("#import_source").val(category_des);
			$("#txt_lc_category").val(category_id); 
			$("#txt_serial_no").val(serial_no);
		}
	}
	
	
	function openmypage(btb_id,year_val,month_val,type,title)
	{
		
		if(type==4 || type==5)
		{
			var action="btb_open_details";
			var width="500px"; 
		}
		else 
		{
			var action="btb_details";
			var width="700px";
		}
		page_link='requires/export_import_coverage_report_controller.php?action='+action+'&btb_id='+btb_id+'&year_val='+year_val+'&month_val='+month_val+'&type='+type;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,title, 'width='+width+',height=350px,center=1,resize=0,scrolling=0','../');
		emailwindow.onclose=function()
		{
			//alert("Jahid");
		}
	}
	
	function openmypage_btb_paid(lc_inv_id,year_val,month_val,type,title)
	{
		page_link='requires/export_import_coverage_report_controller.php?action=btb_paid_details'+'&inv_id='+lc_inv_id+'&year_val='+year_val+'&month_val='+month_val+'&type='+type;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,title, 'width=800px,height=350px,center=1,resize=0,scrolling=0','../');
		emailwindow.onclose=function()
		{
			//alert("Jahid");
		}
	}
	
	function openmypage_btb(btb_id,year_val,month_val,type,title)
	{
		page_link='requires/export_import_coverage_report_controller.php?action=btb_open_details'+'&btb_id='+btb_id+'&year_val='+year_val+'&month_val='+month_val+'&type='+type;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,title, 'width=550px,height=350px,center=1,resize=0,scrolling=0','../');
		emailwindow.onclose=function()
		{
			//alert("Jahid");
		}
	}
	
</script>
</head>
 
<body onLoad="set_hotkey();">
 <div style="width:100%" align="center">
    <form id="file_wise_explort_import_status" action="" autocomplete="off" method="post">
		<? echo load_freeze_divs ("../../"); ?>
        <h3 align="left" id="accordion_h1" style="width:500px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:500px;"> 

    	<fieldset style="width:100%" >
        <table class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" width="500">
            <thead>
                <th class="must_entry_caption" width="350">Company Name</th> 
                <th align="center"><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:90px" onClick="reset_form('file_wise_explort_import_status','report_container*report_container2','','','')" /></th>
           </thead>
            <tr class="general">                           
                <td align="center">
                   <?
                        echo create_drop_down( "cbo_company_name", 250, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/export_import_coverage_report_controller',this.value, 'load_drop_down_year', 'lc_year' );" );
                    ?>
                </td>
				<td align="center"><input type="button" name="show" id="show" onClick="generate_report();" class="formbutton" style="width:90px" value="Show" /></td>
            </tr>
         </table>
    </fieldset>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div> 
        </form>
    </div>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>