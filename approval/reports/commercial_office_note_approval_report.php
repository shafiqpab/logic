<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Commercial Office Note Approval Report
Functionality	:	
JS Functions	:
Created by		:	Rakib 
Creation date 	: 	28-02-2022
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
echo load_html_head_contents("Commercial Office Note Approval Report", "../../", 1, 1, '', 1, 1);
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';
	 
	function fn_report_generated()
	{
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_lc_type_id*cbo_date_by*txt_date_from*txt_date_to*txt_office_note_no*txt_pi_no*cbo_type',"../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/commercial_office_note_approval_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
		

	function fn_report_generated_reponse()
	{
	 	if(http.readyState == 4) 
		{
	  		var response=trim(http.responseText).split("####");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;&nbsp;<input type="button" onClick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
			var tableFilters = { col_0: "none" }
			setFilterGrid("tbl_list_search",-1);
			show_msg('3');
			release_freezing();
	 	}		
	}

    function search_type_function(id) {
        if (id==3) $('#cbo_type').val(2).attr('disabled',true);
        else $('#cbo_type').val(1).attr('disabled',false);
    }

</script>
</head>
<body onLoad="set_hotkey();">
<form id="commercialOfficeNoteApprovalReport_1">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",''); ?>
         <h3 style="width:1010px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
         <div id="content_search_panel">
         <fieldset style="width:1010px;">
             <table class="rpt_table" width="1010" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th>LC Type</th>
                    <th>Date Type</th>
                    <th>Date Range</th>
                    <th>Office Note No</th>
                    <th>PI No</th>
                    <th>Type</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('commercialOfficeNoteApprovalReport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px"/></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td>
                            <?
                                echo create_drop_down( "cbo_company_name", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 0, "-- Select Company --", $selected, "" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down("cbo_lc_type_id", 100, $lc_type, "", 1,"All", "0", "", "", "");
                            ?>
                        </td>
                  
                        <td>
                        	<?
								$search_by_date=array(1=>"Office Note Date",2=>"Office Note Insert Date",3=>"Aprroval date");
								echo create_drop_down("cbo_date_by", 140, $search_by_date, "", 0, "", "", "search_type_function(this.value);", 0);
							?>
                        </td>
                         <td>
                         	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;" placeholder="From Date" />                    							
                            To
                            <input type="text" name="txt_date_to" id="txt_date_to" placeholder="To Date" class="datepicker" style="width:70px;" />                            
                        </td>
                        <td>
                            <input type="text" name="txt_office_note_no" id="txt_office_note_no" class="text_boxes" style="width:100px">
                        </td>                        
                         <td>
                            <input type="text" name="txt_pi_no" id="txt_pi_no" class="text_boxes" style="width:100px">
                        </td> 
                        <td>
                        	<?
								$search_by_arr=array(1=>"Pending",2=>"Full Approved");
								echo create_drop_down("cbo_type", 100, $search_by_arr, "", 0, "", "", "", 0);
							?>
                        </td> 
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated()"/>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="8" align="center"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
    	</div>
        <div id="report_container" align="center"></div>
        <div id="report_container2" align="center"></div>
    </div>
 </form>   
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
set_multiselect('cbo_company_name','0','0','','0');
</script>
</html>
