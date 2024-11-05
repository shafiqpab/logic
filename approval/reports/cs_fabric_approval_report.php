<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create CS Fabric Approval Report.
Functionality	:
JS Functions	:
Created by		:	Rakib
Creation date 	: 	30-05-2023
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
echo load_html_head_contents("CS Fabric Approval Report", "../../", 1, 1, '', 1, 1);
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
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_item_category_id*cbo_year*txt_cs_no*cbo_date_type*txt_date_from*txt_date_to*cbo_type',"../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/cs_fabric_approval_report_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none" ><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;&nbsp;<input type="button" onClick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
			//setFilterGrid("tbl_list_search",-1);
			show_msg('3');
			release_freezing();
	 	}
	}


	function new_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'+
	   '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
	}

    function openmypage_cs()
	{ 
		if( form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
	
		var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_year').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/cs_fabric_approval_report_controller.php?data='+data+'&action=cs_no_popup','CS No Popup', 'width=400px,height=350px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
	        var txt_selected_id=this.contentDoc.getElementById("hidden_cs_id").value;
	        var txt_selected_name=this.contentDoc.getElementById("hidden_cs_no").value;
	        $("#hid_cs_id").val(txt_selected_id);
	        $("#txt_cs_no").val(txt_selected_name);			
		}
	}

	function generate_report(update_id,company_id)
	{
		var form_caption=$( "div.form_caption" ).html();
		print_report( update_id+'*'+form_caption+'*'+company_id, "comparative_statement_print", "../../commercial/work_order/requires/comparative_statement_fabrics_controller");
		return;
	}
</script>
</head>
<body onLoad="set_hotkey();">
<form id="priceQuotationApprovalReport_1">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",''); ?>
         <h3 style="width:1100px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
         <div id="content_search_panel">
         <fieldset style="width:1100px;">
             <table width="100%" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="150" class="must_entry_caption">Company Name</th>
                    <th width="130">Item Category</th>
                    <th width="80">CS Year</th>
					<th width="120">CS No</th>
					<th width="100">Date Type</th>
                    <th width="160">Date Range</th>
                    <th width="120">Approval Type</th>
                    <th width="80"><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('priceQuotationApprovalReport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px"/></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td>
                            <?
                                echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                            ?>
                        </td>
                        <td align="center">
                            <? 
                                echo create_drop_down( "cbo_item_category_id", 160, $item_category,"", 1, "-- All Category --", $selected,"",1,3,"","","");
                            ?>
                        </td>
                        <td align="center">
                            <?
                                echo create_drop_down( "cbo_year", 80, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); 
                            ?> 
                        </td>
                        <td width="120">
                            <input type="hidden" name="hid_cs_id" id="hid_cs_id" />
                            <input type="text" name="txt_cs_no" id="txt_cs_no" class="text_boxes" style="width:120px" placeholder="Browse/Write" onDblClick="openmypage_cs();" tabindex="1" >
                        </td>
                        <td>
                        	<?
								$search_by_arr=array(1=>"CS Date",2=>"CS Approved Date");
								echo create_drop_down("cbo_date_type", 120, $search_by_arr, "", 0, "", 2, "", 0);
							?>
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px;" placeholder="From Date" readonly/>
                            To
                            <input type="text" name="txt_date_to" id="txt_date_to" placeholder="To Date" class="datepicker" style="width:60px;" readonly />
                        </td>
                        <td>
                        	<?
								$search_by_arr=array(1=>"Pending",2=>"Partial Approved",3=>"Full Approved");
								echo create_drop_down("cbo_type", 120, $search_by_arr, "", 0, "", "3", "", 0);
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
</html>
