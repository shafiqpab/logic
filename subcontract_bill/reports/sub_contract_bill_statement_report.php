<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Sub Contract Bill Statement Report.
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam Reza 
Creation date 	: 	23-10-2017
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
echo load_html_head_contents("Sub Contract Bill Statement", "../../", 1, 1,$unicode,1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
			
	function fn_report_generate(type)
	{
		if(document.getElementById('txt_bill_no').value=='')
		{
			var fillData = "cbo_company_id*cbo_location_id*txt_date_from*txt_date_to";	
			var fillMsg  = "Comapny Name*Location*From Date*To Date";	
		}
		else
		{
			var fillData = "cbo_company_id*cbo_location_id";	
			var fillMsg  = "Comapny Name*Location";	
		}
		if (form_validation(fillData,fillMsg)==false)
		{
			return;
		}
		else
		{
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_search_type*cbo_party_source*cbo_party_name*cbo_bill_type*txt_bill_no*cbo_bill_for*txt_date_from*txt_date_to',"../../")+'&type='+type;
			//alert(data);return;
			freeze_window(3);
			http.open("POST","requires/sub_contract_bill_statement_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generate_reponse;
		}
	}

	function fn_report_generate_reponse()
	{
		if(http.readyState == 4) 
		{
			var search_by=$('#cbo_search_type').val();
			if(search_by==1)
			{
				var tableFilters = 
				{
					col_1: "select",
					col_2: "select",
					col_3: "select",
					col_4: "select",
					display_all_text: "-- All --",
					col_operation: {
						id: ["bill_qty","value_bill_amu"],
						col: [7,8],
						operation: ["sum","sum"],
						write_method: ["innerHTML","innerHTML"]
					}
				}
			}
			else if(search_by==2)
			{
				var tableFilters = 
				{
					col_1: "select",
					col_2: "select",
					col_3: "select",
					col_4: "select",
					display_all_text: "-- All --",
					col_operation: {
						id: ["bill_qty","value_bill_amu"],
						col: [8,9],
						operation: ["sum","sum"],
						write_method: ["innerHTML","innerHTML"]
					}
				}
			}
			
			var reponse=trim(http.responseText).split("**"); 
			$('#report_container2').html(reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			//append_report_checkbox('table_header_1',1);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tbody').find('tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		$('#table_body tbody').find('tr:first').show();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="none";
	}
	
	function fn_add_remove(str){
		if(str==''){$("#must_entry_caption_add_remove").css("color", "blue").text(" Bill Date");}
		else{$("#must_entry_caption_add_remove").css("color", "#444").text(" Bill Date");}	
	}
	
	function fnc_search_type(val)
	{
		if(val==1)
		{
			$('#cbo_party_source').removeAttr('disabled','disabled');
			load_drop_down( 'requires/sub_contract_bill_statement_report_controller', $('#cbo_company_id').val()+'_'+$('#cbo_party_source').val(), 'load_drop_down_party_name', 'party_td' );
		}
		else
		{
			$('#cbo_party_source').attr('disabled','disabled');
			$('#cbo_party_source').val(0);
			load_drop_down( 'requires/sub_contract_bill_statement_report_controller', $('#cbo_company_id').val()+'_3', 'load_drop_down_party_name', 'party_td' );
		}
	}
	
	function generate_bill_report(process_id,company_id,id,bill_no,party_source)
	{
		if(process_id==2)
		{
			var show_val_column='';
			if(party_source==1)
			{
				var r=confirm("Press \"OK\" to open with Order Comments\nPress \"Cancel\" to open without Order Comments");
				if (r==true) show_val_column="1"; else show_val_column="0";
			}
			else show_val_column="0";
			if(party_source==1)//Inbound
			{
			var report_title="Knitting Bill Issue";
			generate_report_file( company_id+'*'+id+'*'+bill_no+'*'+report_title+'*'+show_val_column,'knitting_bill_print', '../../subcontract_bill/requires/knitting_bill_issue_controller', process_id);
			}
			else //Outbound
			{
					
					var show_val_column='';
					var r=confirm("Press \"OK\" to open with Order Comments\nPress \"Cancel\" to open without Order Comments");
					if (r==true) show_val_column="1"; else show_val_column="0";
					var report_title="Outside Knitting Bill Issue";
					var cbo_company_id=company_id;
					var update_id=id;
					var txt_bill_no=bill_no;
					alert(party_source);
					//generate_report_file( cbo_company_id+'*'+update_id+'*'+txt_bill_no+'*'+report_title+'*'+show_val_column,'outbound_knitting_bill_print','../../subcontract_bill/requires/outside_knitting_bill_entry_controller',process_id);
					var data=cbo_company_id+'*'+update_id+'*'+txt_bill_no+'*'+report_title+'*'+show_val_column;
			window.open("../../subcontract_bill/requires/outside_knitting_bill_entry_controller.php?data=" + data+'&action=outbound_knitting_bill_print', true );
			return;
			}
		}
		else if(process_id==4)
		{
			//alert(party_source);
			if(party_source==2)//outbound
			{
				//alert(party_source);
				//var report_title="Dyeing And Finishing Bill";
			//generate_report_file( company_id+'*'+id+'*'+report_title,'fabric_finishing_print', '../../subcontract_bill/requires/outside_finishing_bill_entry_controller', process_id);
			var update_id=id;
			//alert(update_id);
			var report_title=$( "div.form_caption" ).html();
			var data=company_id+'*'+update_id+'*'+report_title;
			window.open("../../subcontract_bill/requires/outside_finishing_bill_entry_controller.php?data=" + data+'&action=fabric_finishing_print', true );
			return;
			}
			else
			{
			var report_title="Dyeing And Finishing Bill Issue";
			generate_report_file( company_id+'*'+id+'*'+bill_no+'*'+report_title+'*'+1,'fabric_finishing_print', '../../subcontract_bill/requires/sub_fabric_finishing_bill_issue_controller', process_id);
			}
		}
	}
	
	function generate_bill_report_reponse()
	{
		if(http.readyState == 4) 
		{
			$('#data_panel').html( http.responseText );
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
		}
	}
	
	function generate_report_file(data,action,page,process_id)
	{
		if(process_id==2)
		{
			window.open("../../subcontract_bill/requires/knitting_bill_issue_controller.php?data=" + data+'&action='+action, true );
		}
		else if(process_id==4)
		{
			window.open("../../subcontract_bill/requires/sub_fabric_finishing_bill_issue_controller.php?data=" + data+'&action='+action, true );
		}
	}

</script>
</head>
<body onLoad="set_hotkey();">
<form id="workProgressReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1100px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1100px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>                    
                    <th width="135" class="must_entry_caption">Company</th>
                    <th width="120" class="must_entry_caption">Location</th>
                    <th width="80">Search Type</th>
                    <th width="100">Source</th>
                    <th width="120">Party</th>
                    <th width="80">Bill Type</th>
                    <th width="60">Bill No</th>                     
                    <th width="80">Bill For</th>
                    <th width="130" id="must_entry_caption_add_remove" class="must_entry_caption" colspan="2">Bill Date</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td><? echo create_drop_down( "cbo_company_id", 135, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/sub_contract_bill_statement_report_controller', this.value, 'load_drop_down_location', 'location_td' );" ); ?></td>
                        <td id="location_td"><? echo create_drop_down( "cbo_location_id", 120, $blank_array,"", 1, "-- Select--", $selected, "",1,"" ); ?></td>
                        <td><? $billType_arr=array(1=>"In-Bound",2=>"Out-Bound"); 
							echo create_drop_down( "cbo_search_type", 80, $billType_arr,"", 0, "--Select--", 1, "fnc_search_type(this.value);",0,"","","",""); ?></td>
                        <td><? echo create_drop_down( "cbo_party_source", 100, $knitting_source,"", 1, "-- Select --", $selected, "load_drop_down( 'requires/sub_contract_bill_statement_report_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_party_name', 'party_td' ); fnc_bill_for(this.value);",0,"1,2","","","",5); ?></td>
                        <td id="party_td"><? echo create_drop_down( "cbo_party_name", 120, $blank_array,"", 1, "--Select--", $selected, "",0,"","","","",6); ?></td>
                        <td><? echo create_drop_down( "cbo_bill_type", 80, $production_process,"", 1, "-Select-", $selected, "","","" ); ?></td>
                        <td><input name="txt_bill_no" id="txt_bill_no" style="width:50px" placeholder="Bill No" class="text_boxes" onKeyUp="fn_add_remove(this.value);" ></td>
                        <td><? echo create_drop_down( "cbo_bill_for", 80, $bill_for,"", 0, "--Select--", 1, "",0,"","","","",8); ?></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" ></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date" ></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generate(1)" /></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="11" align="center">
							<? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </tfoot>
           </table> 
        </fieldset>
    </div>
    </div>
    <div style="display:none" id="data_panel"></div> 
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
