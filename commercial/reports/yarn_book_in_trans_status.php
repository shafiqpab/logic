<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yarn Work Order Statement
				
Functionality	:	
JS Functions	:
Created by		:	Rezoanul
Creation date 	: 	28-11-2023
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
echo load_html_head_contents("Yarn Booked and In Transit Status","../../", 1, 1, $unicode,1,1); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report()
	{
		// alert("ss");
		var cbo_supplier = $("#cbo_supplier").val();
		var txt_date_from = $("#txt_date_from").val();
		var txt_date_to = $("#txt_date_to").val();
		if((form_validation('txt_date_from', 'Date Range')==false) ||  (form_validation('txt_date_to', 'Date Range')==false))
		{
			// alert("out");
			return;			
		}
		else
		{	
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_supplier*cbo_yarn_count*cbo_yarn_type*cbo_composition*cbo_date_type*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
			freeze_window(3);
			http.open("POST","requires/yarn_book_in_trans_status_controller.php",true);
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
			//document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
	 		show_msg('3');
			setFilterGrid("table_body",-1);
			release_freezing();
		}
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

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tr:first').hide(); 
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="250px";
		$('#table_body tr:first').show();
	}

	function invoice_popup(pi_id,dtls_id,wo_number)
	{
		var page_link='requires/yarn_book_in_trans_status_controller.php?action=invoice_popup&all_pi_ids='+pi_id+'&dtls_id='+dtls_id+'&wo_number='+wo_number; 
		var title="Invoice Package Qty Pop-Up";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=300px,center=1,resize=0,scrolling=0','../../');
	}

	function mrr_popup(count,comp,percent,type,color,wo_number)
	{
		var page_link='requires/yarn_book_in_trans_status_controller.php?action=mrr_popup&count='+count+'&comp='+comp+'&percent='+percent+'&type='+type+'&color='+color+'&wo_number='+wo_number; 
		var title="Total Receive Qty Pop-Up";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=300px,center=1,resize=0,scrolling=0','../../');
	}
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../","");  ?> 		 
        <form name="yarnworkorderstatement_1" id="yarnworkorderstatement_1" autocomplete="off" > 
    <h3 style="width:1080px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:1080px;">
                <table class="rpt_table" width="1070" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="140" class="must_entry_caption">Company</th>
                            <th width="100">Supplier</th>
							<th width="160">Count</th>
							<th width="160">Yarn Type</th>
							<th width="200">Composition</th>
                            <th width="110">Date Range Basis</th>
                            <th >Date Range</th>
                            <th width="70"><input type="text" name="res" id="res" value="Reset" style="width:70px;text-align: center;" class="formbutton" onClick="reset_form('yarnworkorderstatement_1','report_container*report_container2','','','','res*cbo_wo_basis*cbo_year_selection');" /></th>
                        </tr>
                    </thead>
                    <tr align="center">
                        <td>
                            <? 
							echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- All Company --", $selected, "load_drop_down( 'requires/yarn_book_in_trans_status_controller', this.value, 'load_drop_down_supplier', 'supplier' );get_php_form_data( this.value, 'eval_multi_select', 'requires/yarn_book_in_trans_status_controller' );" );
                            ?>                            
                        </td>
                        <td id="supplier"> 
						  	<?
							   	echo create_drop_down( "cbo_supplier", 100, $blank_array,"", 1,"-- Select --",0,"" );
 							?>
						</td>
						<td>
							<?
							echo create_drop_down("cbo_yarn_count",150,"select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count","id,yarn_count",0, "Select Count", $selected, "");
							?>
						</td>
						<td>
							<?
							echo create_drop_down("cbo_yarn_type",150,$yarn_type,"",1, "Select Type", $selected, "");
							?>
						</td>
						<td>
							<?
							echo create_drop_down("cbo_composition",180,$composition,"",1, "Select Composition", $selected, "");
							?>
						</td>
						
                        <td> 
							<? $date_type=array(1=>'WO date',2=>'Invoice Date');
							echo create_drop_down( "cbo_date_type", 100, $date_type, "", 0, "-- Select --", 0, "", "", ""); ?>
						</td>
                        <td>
                                <input type="text" name="txt_date_from" id="txt_date_from" value="<? //echo date("d-m-Y", time());?>" class="datepicker" style="width:60px;" placeholder="From Date" readonly />
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" value="<? //echo date("d-m-Y", time());?>" class="datepicker" style="width:60px;" placeholder="To Date" readonly />
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:70px" class="formbutton" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="11" align="center">
                            <? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </table> 
            </fieldset> 
        </div>
            <div id="report_container" align="center"></div>
            <div id="report_container2"></div>   
    </form>    
    </div>
</body>
<script>
	set_multiselect('cbo_supplier*cbo_yarn_count*cbo_composition*cbo_yarn_type','0*0*0','0*0*0','','0*0*0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>
