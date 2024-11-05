<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create File Wise Yarn Status Report.
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
echo load_html_head_contents("File Wise Yarn Status Report", "../../", 1, 1,'','','');
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
 
	var permission = '<? echo $permission; ?>';
	
	function fn_report_generated()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else
		{	
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_lien_bank*txt_year*txt_internal_file_no*txt_pi_status',"../../");
			freeze_window(3);
			http.open("POST","requires/file_wise_yarn_status_controller.php",true);
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

	
	
	function openmypage_file()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var lien_bank = $("#cbo_lien_bank").val();
		var txt_year=$("#txt_year").val();
		var page_link='requires/file_wise_yarn_status_controller.php?action=internal_file_no_search_popup&companyID='+companyID+'&lien_bank='+lien_bank+'&txt_year='+txt_year;
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
	
	/*function openmypage(file_no,company_name,bank_id,text_year,action,title)
	{
		var popup_width="";
		if(action=="order_info") popup_width="980px"; else popup_width="850px";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/file_wise_yarn_status_controller.php?file_no='+file_no+'&company_name='+company_name+'&bank_id='+bank_id+'&text_year='+text_year+'&action='+action, title, 'width='+popup_width+',height=420px,center=1,resize=0,scrolling=0','../');
	}	
	
	function generate_report_file(data,action,page)
	{
		window.open("requires/internal_file_info_report.php?data=" + data+'&action='+action, true );
	}*/
	
	function file_wise_pi_popup(action,pi_idd,pi_basis_id,item_category_id,width)
	{
		//alert(action+pi_idd+width+pi_basis_id+item_category_id);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/file_wise_yarn_status_controller.php?action='+action+'&pi_idd='+pi_idd+'&pi_basis_id='+pi_basis_id+'&item_category_id='+item_category_id+'&type='+2, 'PI Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../');
		//alert(emailwindow); return;
	}
	function file_wise_btb_popup(action,pi_idd,item_category_id,width,pi_btb)
	{
		//alert(action,pi_idd,item_category_id,width); //return;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/file_wise_yarn_status_controller.php?action='+action+'&pi_idd='+pi_idd+'&item_category_id='+item_category_id+'&pi_btb='+pi_btb, 'BTB Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../');
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
                            <th class="must_entry_caption" width="170">Company Name</th>
                            <th width="170">Lien Bank</th>
                            <th width="110">Year</th>
                            <th width="170">Internal File No</th>
                            <th width="110">PI Type</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" onClick="reset_form('btbLiabilityCoverage_report','report_container*report_container2','','','')" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td> 
                            <?
								echo create_drop_down( "cbo_company_name", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3)  $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/file_wise_yarn_status_controller',this.value, 'load_drop_down_lc_year', 'lc_year_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <?
							   	echo create_drop_down( "cbo_lien_bank", 160, "select (bank_name || ' (' || branch_name || ')' ) as bank_name,id from lib_bank where is_deleted=0 and status_active=1 and lien_bank=1 order by bank_name","id,bank_name", 1, "-- All Lien Bank --", 0, "" );
							?>
                        </td>
                        <td id="lc_year_td">
						<?
                        echo create_drop_down( "txt_year", 100,$blank_array,"", 1, "-- Select --", 1,"");
                        ?>
                        </td>
                        <td><input type="text" name="txt_internal_file_no" id="txt_internal_file_no" class="text_boxes" style="width:150px" placeholder="Double Click to Search" onDblClick="openmypage_file('Internal File No Search');" readonly/>
                        </td>
                          <td id="pi_status_td">
						<?
						$piType=array(0=>"All",1=>"Approved");
                        echo create_drop_down( "txt_pi_status", 110,$piType,"", 1, "-- Select --", 0,"");
                        ?>
                        </td>
                        <td align="center">
                     <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated(1)" />
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
