<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Consolidated Sewing Production Report.
Functionality	:	
JS Functions	:
Created by		:	REZA 
Creation date 	: 	9-12-2015
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
echo load_html_head_contents("Consolidated Sewing Production Report", "../../", 1, '',$unicode,'','');

?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';

	function fn_report_generated(type)
	{
		if( form_validation('txt_date_from*txt_date_to','From Date*To Date')==false )
		{
			return;
		}
		else
		{
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*txt_date_from*txt_date_to',"../../")+"&report_title="+report_title;
			freeze_window(3);
			http.open("POST","requires/consolidated_sewing_production_report_controller.php",true);
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
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>'; 
			//setFilterGrid("table_body",-1);
			show_msg('3');
			release_freezing();
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
	'<html><head><title></title><link rel="stylesheet" href="../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		//$("#table_body tr:first").show();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="350px";
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
	 
</script>
</head>
<body onLoad="set_hotkey();">
<form>
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:800px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
        <div id="content_search_panel">
         <fieldset style="width:800px;">
            <table class="rpt_table" cellpadding="0" cellspacing="0" align="center" rules="all" border="1">
               <thead>                    
                    <tr>
                        <th>Company</th>
                        <th class="must_entry_caption">Sewing Date</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:90px" value="Reset" /></th>
                    </tr>    
                 </thead>
                <tbody>
                <tr class="general">
                    <td width="190" align="center"> 
                        <?
                            echo create_drop_down( "cbo_company_name", 180, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- All Company --", $selected, "" );
                        ?>
                    </td>
                    <td width="290">
                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:110px" placeholder="From Date" >&nbsp; To &nbsp;
                    	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:110px"  placeholder="To Date"  >
                    </td>
                    <td width="100">
                        <input type="button" id="show_button" class="formbutton" style="width:90px" value="Show" onClick="fn_report_generated(1)" />
                    </td>
                </tr>
                </tbody>
            </table>
            <table>
            	<tr>
                	<td colspan="3" width="800" align="center">
 						<? echo load_month_buttons(1); ?>
                   	</td>
                </tr>
            </table> 
            <br />
        </fieldset>
        </div>
    </div>
  </form>
  <div id="report_container" align="center"></div>
  <div id="report_container2" align="center"></div>
    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$('#cbo_location').val(0);
</script>
</html>
