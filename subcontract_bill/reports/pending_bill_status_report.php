<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Pending Bill Status Report
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	16-09-2019
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
echo load_html_head_contents("Pending Bill Status Report", "../../", 1, 1,$unicode,'','');
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
			
	function fn_report_generated(type)
	{
		if (form_validation('cbo_company_id*cbo_location_id*cbo_source*cbo_bill_type*txt_date_from*txt_date_to','Comapny Name*Location*Source*Bill Type*Date From*Date To')==false)
		{
			return;
		}
		else
		{
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_source*cbo_bill_type*cbo_party_name*txt_chln_from*txt_chln_to*cbo_year*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title+'&type='+type;
			//alert (data);
			freeze_window(3);
			http.open("POST","requires/pending_bill_status_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("**"); 
			$('#report_container2').html(reponse[0]);
			//alert (reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("table_body",-1);
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
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="300px";
		$("#table_body tr:first").show();
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
<form id="partyStatementReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:920px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel','')">-Search Panel</h3>
         <div id="content_search_panel" >      
         <fieldset style="width:920px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>                    
                    <th width="140" class="must_entry_caption">Company</th>
                    <th width="120" class="must_entry_caption">Location</th>
                    <th width="80" class="must_entry_caption">Source</th>
                    <th width="80" class="must_entry_caption">Bill Type</th>
                    <th width="120">Party Name</th>
                    <th width="100" colspan="2">Sys. Challan Range</th>
                    <th width="60">Year</th>
                    <th width="120" class="must_entry_caption" colspan="2">Trans Date Range</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                </thead>
                <tbody>
                    <tr class="general" >
                        <td><? echo create_drop_down( "cbo_company_id", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/pending_bill_status_report_controller', this.value, 'load_drop_down_location', 'location_td' );" ); ?></td>
                        <td id="location_td"><? echo create_drop_down( "cbo_location_id", 120, $blank_array,"", 1, "-Select Location-", $selected, "",1,"" ); ?></td>
                        <td><? echo create_drop_down("cbo_source",80,$knitting_source,"", 1, "--Select--", 0,"load_drop_down( 'requires/pending_bill_status_report_controller', this.value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_bill_type').value, 'load_drop_down_party_name','party_name');",0,'');?></td>
                        <td><?
							$bill_type_arr=array(1=>"Knitting",2=>"Dyeing",3=>"Trims"); 
							echo create_drop_down("cbo_bill_type",80,$bill_type_arr,"", 1, "--Select--", 0,"",0,'');?>
                        </td>
                        <td id="party_name"><? echo create_drop_down( "cbo_party_name", 120, $blank_array,"",1, "-Select Party-", 1, "" ); ?></td>
                        <td><input name="txt_chln_from" id="txt_chln_from" class="text_boxes_numeric" style="width:45px" placeholder="From No" ></td>
                        <td><input name="txt_chln_to" id="txt_chln_to" class="text_boxes_numeric" style="width:45px" placeholder="To No" ></td>
                        <td><? 
								$selected_year=date("Y");
								echo create_drop_down( "cbo_year", 60, $year,"", 1, "-All-", $selected_year, "",0 );
							?>
                        </td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date" ></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To Date" ></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1);" /></td>
                    </tr>
                    <tr>
                        <td colspan="11" align="center"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </tbody>
           </table> 
        </fieldset>
    </div>
    </div>
        
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
