<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create BTB Liability Coverage Report.
Functionality	:	
JS Functions	:
Created by		:	Fuad 
Creation date 	: 	18-06-2013
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
echo load_html_head_contents("BTB Liability Coverage Report", "../../", 1, 1,'','','');
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
 
	var permission = '<? echo $permission; ?>';
	
	function fn_report_generated(reportType)
	{
		if(form_validation('cbo_company_name*txt_internal_file_no','Company Name*Internal File No')==false)
		{
			return;
		}
		else
		{	
			var data="action=report_generate&reportType="+reportType+get_submitted_data_string('cbo_company_name*cbo_lien_bank*txt_year*txt_internal_file_no*hide_conversion_rate',"../../");
			freeze_window(3);
			http.open("POST","requires/btb_liability_coverage_report_controller.php",true);
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
			var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>';
			show_msg('3');
			release_freezing();
		}
	}

	function openmypage_conversion_rate()
	{
		var hide_conversion_rate=$('#hide_conversion_rate').val();		
		var page_link='requires/btb_liability_coverage_report_controller.php?action=conversion_rate_popup&hide_conversion_rate='+hide_conversion_rate;
		var title='BTB Liability Coverage Report';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=350px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var all_conversion_rate_id=this.contentDoc.getElementById("all_conversion_rate_id").value;
			
			$('#hide_conversion_rate').val(all_conversion_rate_id);		
		}
	}
	
	function openmypage_file()
	{
		if(form_validation('cbo_company_name*txt_year','Company Name*Year')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var lien_bank = $("#cbo_lien_bank").val();
		var year = $("#txt_year").val();
		var page_link='requires/btb_liability_coverage_report_controller.php?action=internal_file_no_search_popup&companyID='+companyID+'&lien_bank='+lien_bank+'&year='+year;
		var title='Internal File No Search';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var internal_file_no=this.contentDoc.getElementById("internal_file_no").value;
			var year=this.contentDoc.getElementById("txt_year").value;
			
			$('#txt_internal_file_no').val(internal_file_no);
			$('#txt_year').val(year);	 
		}
	}
	
	function openmypage(file_no,company_name,bank_id,text_year,action,title)
	{
		var popup_width="";
		if(action=="order_info") popup_width="980px"; else popup_width="850px";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/btb_liability_coverage_report_controller.php?file_no='+file_no+'&company_name='+company_name+'&bank_id='+bank_id+'&text_year='+text_year+'&action='+action, title, 'width='+popup_width+',height=420px,center=1,resize=0,scrolling=0','../');
	}	
	
	function generate_report_file(data,action,page)
	{
		var r=confirm("Press  \"Cancel\"  to Show Woven Report\nPress  \"OK\"  to Show Knit Report");
		var report_type = "";
		if (r==true)
		{
			report_type = "1";
			window.open(page+".php?data=" + data+'&action='+action+'&report_type='+report_type, true );
		}
		else
		{
			report_type = "0";
			window.open(page+".php?data=" + data+'&action='+action+'&report_type='+report_type, true );
		}
		
	}
</script>

</head>

<body onLoad="set_hotkey();">
<form id="btbLiabilityCoverage_report">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",''); ?>
        <h3 align="left" id="accordion_h1" style="width:900px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
            <fieldset style="width:900px;">
                <table class="rpt_table" width="880" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">
                	<thead>
                   		<tr>                    
                            <th class="must_entry_caption">Company Name</th>
                            <th>Lien Bank</th>
                            <th class="must_entry_caption">Year</th>
                            <th class="must_entry_caption">Internal File No</th>
                            <th><input type="button" name="search" id="search" value="Conversion Rate" style="width:110px" class="formbutton" onClick="openmypage_conversion_rate(); " /></th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:80px" value="Reset" onClick="reset_form('btbLiabilityCoverage_report','report_container*report_container2','','','')" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td> 
                            <?
								echo create_drop_down( "cbo_company_name", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <?
							   	echo create_drop_down( "cbo_lien_bank", 160, "select (bank_name || ' (' || branch_name || ')' ) as bank_name,id from lib_bank where is_deleted=0 and status_active=1 and lien_bank=1 order by bank_name","id,bank_name", 1, "-- All Lien Bank --", 0, "" );
							?>
                        </td>
                        <td><input name="txt_year" id="txt_year" class="text_boxes" style="width:100px" ></td>
                        <td><input type="text" name="txt_internal_file_no" id="txt_internal_file_no" class="text_boxes" style="width:150px" placeholder="Double Click to Search" onDblClick="openmypage_file('Internal File No Search');" readonly/>
                        </td>
                        <td align="center">
                            <input type="button" id="show_button" class="formbutton" style="width:110px" value="Show" onClick="fn_report_generated(1)" />
                       	</td>
                   		<td align="center">
                            <input type="button" id="show_button2" class="formbutton" style="width:80px" value="Short" onClick="fn_report_generated(2)" />
                            <input type="hidden" name="hide_conversion_rate" id="hide_conversion_rate" class="text_boxes" />
                    	</td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
    <div style="margin-top:10px" id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
