<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create EDF Liability Report.
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	22-11-2015
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
echo load_html_head_contents("EDF Liability", "../../", 1, 1,'','','');
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
 
	var permission = '<? echo $permission; ?>';

	/*function fn_html_change()
	{
		var file_year=$('#hide_year').val();
		if(file_year>0)
		{
			$('#btb_date_html').css('color','#000');
		}
		else
		{
			$('#btb_date_html').css('color','#00F');
		}
	}*/
	
	function generate_report()
	{
		var file_year=$('#hide_year').val();
		var cbo_lein_bank=$('#cbo_lein_bank').val();
		var txt_file_no=$('#txt_file_no').val();
		var import_source=$('#import_source').val();
		var txt_date_from=$('#txt_date_from').val();
		var txt_date_to=$('#txt_date_to').val();
		var txt_date_from_m=$('#txt_date_from_m').val();
		var txt_date_to_m=$('#txt_date_to_m').val();
		var txt_date_from_paid=$('#txt_date_from_paid').val();
		var txt_date_to_paid=$('#txt_date_to_paid').val();
		
		if(file_year>0)
		{
			if(form_validation('cbo_company_name*hide_year','Company Name*File Year')==false)
			{
				return;
			}
		}
		else if(cbo_lein_bank>0)
		{
			if(form_validation('cbo_company_name*cbo_lein_bank','Company Name*Lein Bank')==false)
			{
				return;
			}
		}
		else if(txt_file_no!="")
		{
			if(form_validation('cbo_company_name*txt_file_no','Company Name*File No')==false)
			{
				return;
			}
		}
		else if(import_source!="")
		{
			if(form_validation('cbo_company_name*import_source','Company Name*Source')==false)
			{
				return;
			}
		}
		else if(txt_date_from_m!="" && txt_date_to_m!="")
		{
			if(form_validation('cbo_company_name*txt_date_from_m*txt_date_to_m','Company Name*Maturity Date Form*Maturity Date to')==false)
			{
				return;
			}
		}
		else if(txt_date_from_paid!="" && txt_date_to_paid!="")
		{
			if(form_validation('cbo_company_name*txt_date_from_paid*txt_date_to_paid','Company Name*Maturity Date Form*Maturity Date to')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*BTB Date Form*BTB Date to')==false)
			{
				return;
			}
		}
		
		var report_title=$( "div.form_caption" ).html();	
		var data="action=report_generate"+get_submitted_data_string("cbo_company_name*cbo_lein_bank*txt_file_no*hide_year*txt_date_from*txt_date_to*txt_lc_category*txt_date_from_m*txt_date_to_m*txt_date_from_com*txt_date_to_com*txt_date_from_bank*txt_date_to_bank*txt_date_from_paid*txt_date_to_paid*cbo_pending","../../")+'&report_title='+report_title;
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/edf_liability_report_controller.php",true);
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
	function openmypage_file_info()
	{
		var company_id=document.getElementById('cbo_company_name').value;
		var lien_bank=document.getElementById('cbo_lein_bank').value;
		var cbo_year=document.getElementById('hide_year').value;
		//alert(buyer_id);
		page_link='requires/edf_liability_report_controller.php?action=file_popup&company_id='+company_id+'&lien_bank='+lien_bank+'&cbo_year='+cbo_year;
		if(form_validation('cbo_company_name*hide_year','Company Name*Year')==false)
		{
			return;
		}
		else
		{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Select File", 'width=600px,height=390px,center=1,resize=0,scrolling=0','../')
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var file_no=this.contentDoc.getElementById("hide_file_no").value.split(",");//alert(item_description_all); 
				//alert(file_no[0]);
				document.getElementById('txt_file_no').value=file_no[0];
			}
		}
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
		var page_link='requires/edf_liability_report_controller.php?action=source_surch&company_id='+company_id+'&import_source='+import_source+'&txt_lc_category='+txt_lc_category+'&txt_serial_no='+txt_serial_no;  
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
</script>
</head>
 
<body onLoad="set_hotkey();">
 <div style="width:100%" align="center">
    <form id="file_wise_explort_import_status" action="" autocomplete="off" method="post">
		<? echo load_freeze_divs ("../../"); ?>
        <h3 align="left" id="accordion_h1" style="width:1380px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:1380px;"> 

    	<fieldset style="width:100%" >
        <table class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" width="1380">
            <thead>
                <th class="must_entry_caption" width="150">Company Name</th> 
                <th width="150">Lien Bank</th>
                <th width="70">Year</th>
                <th width="80">File No</th>
                <th width="80">Import Source</th>
                <th width="140" id="btb_date_html">BTB Date Range</th>
                <th width="140">Maturity Date Range</th>
                <th width="140">Company Acc. Date Range</th>
                <th width="140">Bank Acc. Date Range</th>
                <th width="140">Paid Date Range</th>
                <th width="70">Pending Type</th>
                <th align="center"><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:60px" onClick="reset_form('file_wise_explort_import_status','report_container*report_container2','','','')" /></th>
           </thead>
            <tr class="general">                           
                <td align="center">
                   <?
                        echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3)  $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/edf_liability_report_controller',this.value, 'load_drop_down_year', 'lc_year' );" );
                    ?>
                </td>
                <td align="center">
                <? 
                    echo create_drop_down( "cbo_lein_bank", 150, "select (bank_name || ' (' || branch_name || ')' ) as bank_name,id from lib_bank where is_deleted=0  and status_active=1 and lien_bank=1 order by bank_name","id,bank_name", 1, "-- All Bank --", $selected, "",0,"" );
                ?>
                </td>
                <td id="lc_year">
                <?
                echo create_drop_down( "hide_year", 70,$blank_array,"", 1, "-- Select --", 1,"");

                ?>
                </td>
                <td align="center">
                	<input type="text" name="txt_file_no" id="txt_file_no" class="text_boxes" onDblClick="openmypage_file_info();" placeholder="Browse Or Write" style="width:70px;" />
                </td>
                <td>
	                <input type="text" name="import_source" id="import_source" class="text_boxes" onDblClick="openmypage_source();" placeholder="Browse" style="width:70px;" readonly />
	                <input type="hidden" name="txt_lc_category" id="txt_lc_category"/><input type="hidden" name="txt_serial_no" id="txt_serial_no"/>
                </td>
                <td align="center">
	                <input name="txt_date_from" id="txt_date_from" class="datepicker"  style="width:55px" placeholder="From Date" />
	                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"placeholder="To Date" />
                </td>
				<td align="center">
					<input name="txt_date_from_m" id="txt_date_from_m" class="datepicker"  style="width:55px" placeholder="From Date" />
					<input name="txt_date_to_m" id="txt_date_to_m" class="datepicker" style="width:55px"placeholder="To Date" /> 
                </td>
                <td align="center">
					<input name="txt_date_from_com" id="txt_date_from_com" class="datepicker"  style="width:55px" placeholder="From Date" />
					<input name="txt_date_to_com" id="txt_date_to_com" class="datepicker" style="width:55px"placeholder="To Date" /> 
                </td> 
                <td align="center">
					<input name="txt_date_from_bank" id="txt_date_from_bank" class="datepicker"  style="width:55px" placeholder="From Date" />
					<input name="txt_date_to_bank" id="txt_date_to_bank" class="datepicker" style="width:55px"placeholder="To Date" /> 
                </td>
                <td align="center">
					<input name="txt_date_from_paid" id="txt_date_from_paid" class="datepicker"  style="width:55px" placeholder="From Date" />
					<input name="txt_date_to_paid" id="txt_date_to_paid" class="datepicker" style="width:55px"placeholder="To Date" /> 
                </td>
                <td>
                	<? 
                	$pending_type=array(1=>'All',2=>'Loan Paid',3=>'Loan Pending');
                	echo create_drop_down("cbo_pending", 70, $pending_type, '', '', '');
                	?>
                </td>
				<td align="center"><input type="button" name="show" id="show" onClick="generate_report();" class="formbutton" style="width:60px" value="Show" /></td>
            </tr>
            <tr>
                <td colspan="12" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
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