<?
/*-------------------------------------------- Comments
Purpose			: 	This Form Will Create Process Wise Yarn History Report.
				
Functionality	:	
JS Functions	:
Created by		:	Wayasel Ahmmed
Creation date 	: 	02-08-2023
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:	Passion to write neat and clean code!
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Rack Wise Statement Report V2","../../", 1, 1, $unicode,1,1); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";


	function generate_report(rpt_type)
	{
	
        if(form_validation('cbo_year_name*cbo_month*cbo_end_year_name*cbo_month_end','Start Year*Start Month*End Year*End Month')==false)
		{
			return;
		}
  
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_item_cat*txt_item_group*cbo_item_group*txt_item_account_id*cbo_store_name*cbo_issue_purpose*cbo_uom*cbo_year_name*cbo_month*cbo_end_year_name*cbo_month_end',"../../")+'&report_title='+report_title+'&c='+rpt_type;
		//alert (data);
		freeze_window(3);
		http.open("POST","requires/month_wise_issue_summery_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="##" onclick="exportToExcel();" id="dlink" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("table_body",-1);
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window()
	{
		// document.getElementById('scroll_body').style.overflow="auto";
		// document.getElementById('scroll_body').style.maxHeight="none";
		// $("#table_body tr:first").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	    '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		// document.getElementById('scroll_body').style.overflowY="scroll";
		// document.getElementById('scroll_body').style.maxHeight="380px";
		// $("#table_body tr:first").show();
	}


	function exportToExcel()
	{
		$(".fltrow").hide();
		var tableData = document.getElementById("report_container2").innerHTML;
		// alert(tableData);
	    var data_type = 'data:application/vnd.ms-excel;base64,',
		template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table border="2px">{table}</table></body></html>',
		base64 = function (s) {
			return window.btoa(unescape(encodeURIComponent(s)))
		},
		format = function (s, c) {
			return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; })
		}
		
		var ctx = {
			worksheet: 'Worksheet',
			table: tableData
		}
		
	    document.getElementById("dlink").href = data_type + base64(format(template, ctx));
	    document.getElementById("dlink").traget = "_blank";
		document.getElementById("dlink").download = '<?=$_SESSION['logic_erp']['user_id']."_".time();?>' + '.xls';
	    document.getElementById("dlink").click();
		$(".fltrow").show();
		// alert('ok');
	}

    function openmypage_group()
	{
		var cbo_year_selection = $("#cbo_year_selection").val()
		// alert(cbo_year_selection);return;
		var cbo_item_group = $("#cbo_item_group").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/month_wise_issue_summery_report_controller.php?action=item_group_popup&cbo_year_selection='+cbo_year_selection+'&cbo_item_group='+cbo_item_group, 'Item Group Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0','../../');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var selected_name=this.contentDoc.getElementById("selected_name").value;
			var selected_id=this.contentDoc.getElementById("selected_id").value;
			$("#txt_item_group").val(selected_name);
			$("#cbo_item_group").val(selected_id);
		} 
	}


    function openmypage_item_account()
	{
		var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_item_cat').value+"_"+document.getElementById('cbo_item_group').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/month_wise_issue_summery_report_controller.php?action=item_account_popup&data='+data,'Item Account Popup', 'width=800px,height=520px,center=1,resize=0','../../')		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("item_account_id");
			var theemailv=this.contentDoc.getElementById("item_account_val");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{

				//freeze_window(5);
				document.getElementById("txt_item_account_id").value=response[0];
			    document.getElementById("txt_item_acc").value=theemailv.value;
				//reset_form();
				//get_php_form_data( response[0], "item_account_dtls_popup", "requires/dyes_and_cmcl_store_wise_stock_report_controller" );
				release_freezing();
			}
		}
	}
	
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission); ?>  		 
    <form name="orderwisegreyfabricstock_1" id="orderwisegreyfabricstock_1" autocomplete="off" > 
    <h3 style="width:1600px; margin-top:10px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:1600px;">
                <table class="rpt_table" width="1600" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th class="must_entry_caption">Company</th>                                
                          	<th> Item Category</th>
                          	<th> Item group </th> 
                            <th> Item Account</th>
                            <th> Store </th>
                            <th> Issue Purpose </th>
                            <th> UOM </th>
                            <th> Start Year</th>
                            <th> Start Month</th>
                            <th> End Year</th>
                            <th> End Month</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('orderwisegreyfabricstock_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
                            <? 
                               echo create_drop_down( "cbo_company_id", 110, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                            ?>                            
                        </td>                 
                        <td id="cat_td">
                            <?                           
                                echo create_drop_down( "cbo_item_cat", 120, $item_category,"", 1, "-- Select Item --", $selected, "",0,"5,6,7,23" ); 
                            ?>
                        </td>
                       
                        <td>
                             <input type="text" id="txt_item_group" name="txt_item_group" class="text_boxes" style="width:100px" value="" onDblClick="openmypage_group();" placeholder="Browse" readonly />
                            <input type="hidden" id="cbo_item_group" name="cbo_item_group" />
                        </td>	

                        <td>
                            <input style="width:110px;" name="txt_item_acc" id="txt_item_acc" class="text_boxes" onDblClick="openmypage_item_account()" placeholder="Browse" readonly />
                            <input type="hidden" name="txt_item_account_id" id="txt_item_account_id" style="width:90px;"/>  
	                    </td>
                        
                        <td width="120" id="store_td">
                            <? 
                                echo create_drop_down( "cbo_store_name", 120, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and  b.category_type in(5,6,7,23) group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select Store--", "", "" );
                            ?>
                        </td>

                        <td>
                            <? 
                            echo create_drop_down( "cbo_issue_purpose", 120, $general_issue_purpose,"", 1, "-- Select Purpose --", "83", "fn_loan_paty(this.value);", "","1,5,15,56,61,63,64,66,69,80,83", "", "", "", "", "", "", "", "");
                            ?>
                        </td>

						<td> 
                            <?
								echo create_drop_down( "cbo_uom", 100,$unit_of_measurement,"", 1,"-- All --", $selected, "",0,"" );
                            ?>
                        </td>

                        <td align="center">
                                <? 
                                echo create_drop_down( "cbo_year_name", 100,$year,"id,year", 1, "-- Select Year --", date('Y'),"" );
                                ?>
                         </td>
                         <td align="center">
                                <?
                                echo create_drop_down( "cbo_month", 100,$months,"", 1, "-- Select --", "","" );
                                ?>
                          </td>
                         <td align="center">
                                <? 
                                echo create_drop_down( "cbo_end_year_name", 100,$year,"id,year", 1, "-- Select Year --", date('Y'),"" );
                                ?>
                         </td>
                         <td align="center">
                                <?
                                echo create_drop_down( "cbo_month_end", 100,$months,"", 1, "-- Select --", "","" );
                                ?>
                        </td>
           
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:80px" class="formbutton" />
                        </td>
                    </tr>
                </table> 
            </fieldset>  
        </div>
        <div id="report_container" align="center" style="margin:5px 0;"></div>
        <div id="report_container2"></div>   
    </form>    
</div>    
</body>  
<script>
	set_multiselect('cbo_company_id*cbo_item_cat*cbo_store_name*cbo_issue_purpose*cbo_uom','0*0*0*0*0','0*0*0*0*0','','0*0*0*0*0');
	// setTimeout[($("#cat_td a").attr("onclick","disappear_list(cbo_item_cat,'0');getStoreId();") ,3000)];
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
