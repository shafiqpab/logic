<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Daily Chemical Uses and Costing Report.
Functionality	:	
JS Functions	:
Created by		:	K.M Nazim Uddin
Creation date 	: 	30-11-2020
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
echo load_html_head_contents("Daily Chemical Uses and Costing Report", "../../", 1, 1,$unicode,1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';

	/*var tableFilters = 
	{
		//col_15: "none",
		col_operation: {
		id: ["value_total_rate","value_total_qty","value_total_amount"],
		col: [14,15,16],
		operation: ["sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML"]
		}
	} 
	
	var tableFilters2 = 
	{
		//col_15: "none",
		col_operation: {
		id: ["value_total_rate","value_total_qty","value_total_amount"],
		col: [14,15,16],
		operation: ["sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML"]
		}
	} */

	function fn_report_generated(type)
	{
		if (form_validation('cbo_company_id','Comapny Name')==false)//*txt_date_from*txt_date_to----*From Date*To Date
		{
			return;
		}
		else
		{
			if(type==3){
				if (form_validation('txt_style_ref','Buyer Style')==false)
				{
					return;
				}
			}
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_within_group*cbo_buyer_id*cbo_buyer_buyer*txt_job_no*txt_order_no*txt_int_ref*txt_order_id*txt_buyer_po*txt_style_ref*txt_recipe*txt_color*txt_date_from*txt_date_to*cbo_year_selection*cbo_year',"../../")+'&type='+type;
			freeze_window(3);
			http.open("POST","requires/printing_daily_chemical_uses_costing_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}

	function fn_report_generated_reponse()
	{
		/*if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("**"); 
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			append_report_checkbox('table_header_1',1);
			setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}*/

		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//append_report_checkbox('table_header_1',1);
			//setFilterGrid("table_body",-1,tableFilters);
			/*var cbo_presentation=$('#cbo_presentation').val(); 
			if(cbo_presentation==1)
			{
				setFilterGrid("table_body",-1,tableFilters);
			}
			else
			{
				setFilterGrid("table_body",-1,tableFilters2);	
			}*/
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		//$('#table_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#scroll_body tr:first').show();
	}
	
	function show_progress_report_details(action,order_id,width)
	{ 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/printing_daily_chemical_uses_costing_report_controller.php?action='+action+'&order_id='+order_id, 'Work Progress Report Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../');
		
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

	function openImageWindow(id)
	{
		var title = 'Image View';	
		var page_link = 'requires/printing_daily_chemical_uses_costing_report_controller.php?&action=image_view_popup&id='+id;
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=370px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
		}
	}
	
	function openmypage_job()
	{ 
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_id').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_id').value;
		var year=document.getElementById('cbo_year').value;
		var cbo_process_id=document.getElementById('cbo_process_id').value;
		var page_link="requires/printing_daily_chemical_uses_costing_report_controller.php?action=job_no_popup&company_id="+company_name+"&cbo_buyer_name="+cbo_buyer_name+"&year="+year;
		var title="Job Number";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=530px,height=420px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			//var job=theemail.split("_");
			document.getElementById('txt_job_no').value=theemail;
			release_freezing();
		}
	}	
	
	function openmypage_style()
	{ 
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_id').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_id').value;
		var year=document.getElementById('cbo_year').value;
		var cbo_process_id=document.getElementById('cbo_process_id').value;
		var page_link="requires/printing_daily_chemical_uses_costing_report_controller.php?action=style_no_popup&company_id="+company_name+"&cbo_buyer_name="+cbo_buyer_name+"&year="+year;
		var title="Style Ref.";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=530px,height=420px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			//var job=theemail.split("_");
			document.getElementById('txt_style_ref').value=theemail;
			release_freezing();
		}
	}

	function openmypage_order()
	{ 
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_id').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_id').value;
		var year=document.getElementById('cbo_year').value;
		var cbo_process_id=document.getElementById('cbo_process_id').value;
		var job_no=document.getElementById('txt_job_no').value;
		var page_link="requires/printing_daily_chemical_uses_costing_report_controller.php?action=order_no_popup&company_id="+company_name+"&cbo_buyer_name="+cbo_buyer_name+"&year="+year+"&job_no="+job_no;
		var title="Order Number";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=530px,height=420px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			var job=theemail.split("_");
			document.getElementById('txt_order_id').value=job[0];
			document.getElementById('txt_order_no').value=job[1];
			release_freezing();
		}
	}

	function job_search_popup()
	{
		if ( form_validation('cbo_company_id*cbo_buyer_id','Company Name*Party Name')==false )
		{
			return;
		}
		else
		{
			var title="Job No Pop-up";
			var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_buyer_id').value+"_"+document.getElementById('cbo_within_group').value;
			page_link='requires/printing_daily_chemical_uses_costing_report_controller.php?action=job_popup&data='+data;
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=400px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				freeze_window(5);
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("selected_order").value;
				var ex_data=theemail.split('_');
				$("#txt_job_no").val( ex_data[0] );
				$("#txt_job_id").val( ex_data[1] );
				$('#cbo_company_name').attr('disabled','disabled');
				$('#cbo_party_name').attr('disabled','disabled');
				release_freezing();
			}
		}
	}

	function order_search_popup()
	{
		if ( form_validation('cbo_company_id*cbo_buyer_id','Company Name*Party Name')==false )
		{
			return;
		}
		else
		{
			var title="Order No Pop-up";
			var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_buyer_id').value+"_"+document.getElementById('cbo_within_group').value;
			page_link='requires/printing_daily_chemical_uses_costing_report_controller.php?action=order_popup&data='+data;
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=400px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				freeze_window(5);
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("selected_order").value;
				var ex_data=theemail.split('_');
				$("#txt_order_no").val( ex_data[0] );
				$("#txt_job_id").val( ex_data[1] );
				$('#cbo_company_name').attr('disabled','disabled');
				$('#cbo_party_name').attr('disabled','disabled');
				release_freezing();
			}
		}
	}

	function openmypage_qty(issue_dtls_ids,action)
	{
		//alert(issue_dtls_ids);
		var type="1"
		var popup_width='';
		if(type=="1") popup_width='1150px'; else popup_width='890px';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/printing_daily_chemical_uses_costing_report_controller.php?issue_dtls_ids='+issue_dtls_ids+'&action='+action, 'Detail Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}

	$(function(){
		$("#cbo_within_group").change(function()
		{
			var company_name = $("#cbo_company_id").val();
			if(company_name==0)
			{
				alert("Please select company name.");return;
			}
			// alert('ok');
			if($(this).val()==2)
			{
				//$("#txt_style_ref").prop('disabled','disabled');
				$("#txt_int_ref").prop('disabled','disabled');
				//$("#cngTxt").text('Party');
			}
			else
			{
				//$("#txt_style_ref").prop('disabled','');
				$("#txt_int_ref").prop('disabled','');
				//$("#cngTxt").text('Company');
			}
		});
	})


	

</script>
</head>
<body onLoad="set_hotkey();">
<form id="workProgressReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1520px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" >
         <fieldset style="width:1700px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>
                    <th width="135" class="must_entry_caption">Company</th>
                    <th width="80">Within Group </th>
                    <th width="125">Party Name</th>
                    <th width="125">Party Buyer</th>
                    <th width="60">Job Year</th>
                    <th width="120">Job No</th>
                    <th width="80">Order No</th>
                    <th width="100">Internal Ref.</th>
                    <th width="80">Buyer PO</th>
                    <th width="80">Buyer Style</th>
                    <th width="80">Recipe No.</th>
                    <th width="80">Color</th>
                    <th width="160">Date Range</th>
                    <th width=""><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                </thead>
                <tbody>
                    <tr >
                        <td> 
                        <? echo create_drop_down( "cbo_company_id", 135, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/printing_daily_chemical_uses_costing_report_controller', this.value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_buyer', 'buyer_td' );"); ?>
                    	</td>
                    	<td>
							<?php echo create_drop_down( "cbo_within_group", 80, $yes_no,"", 1, "All", $selected, "load_drop_down( 'requires/printing_daily_chemical_uses_costing_report_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?>
						</td>
                        <td id="buyer_td">
                        	<? 
                        		echo create_drop_down( "cbo_buyer_id", 125, $blank_array,"", 1, "-- Select Party --", $selected, "" );
                        	?>
                    	</td>
                    	<td>
                        	<? 
                        		echo create_drop_down( "cbo_buyer_buyer", 125, $blank_array,"", 1, "-- Select Party --", $selected, "" );
                        	?>
                    	</td>
                        <td align="center">

                            <?

                                $selected_year=date("Y");

                                echo create_drop_down( "cbo_year", 60, $year,"", 1, "--Select Year--", $selected_year, "",0 );

                            ?>

                           </td>
                        <td>
                    		<input class="text_boxes"  type="text" name="txt_job_no" id="txt_job_no" onDblClick="job_search_popup();" placeholder="Double Click" style="width:107px;" readonly/>
                    	</td>
                    	<td>
                            <input name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:75px" onDblClick="order_search_popup();" placeholder="Double Click" >
                            <input type="hidden" name="txt_order_id" id="txt_order_id" class="text_boxes" style="width:70px">
                        </td>
						<td>
                            <input name="txt_int_ref" id="txt_int_ref" class="text_boxes" style="width:100px" placeholder="Write" onDblClick="openmypage_intRef();" >
                        </td>
                        <td>
                            <input name="txt_buyer_po" id="txt_buyer_po" class="text_boxes" style="width:75px" placeholder="Write" >
                        </td>
                        <td>
                            <input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:75px" placeholder="Write" >
                        </td>
                        <td>
                            <input name="txt_recipe" id="txt_recipe" class="text_boxes" style="width:75px" placeholder="Write" >
                        </td>
                        <td>
                            <input name="txt_color" id="txt_color" class="text_boxes" style="width:75px" placeholder="Write" >
                        </td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" >&nbsp; To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date"  >
                        </td>
                        <td align="center">
                            <input type="button" id="show_button" class="formbutton" style="width:60px" value="Show" onClick="fn_report_generated(1)" />
                            <input type="button" id="show_button" class="formbutton" style="width:60px" value="Show 2" onClick="fn_report_generated(2)" />
							 <input type="button" id="show_button" class="formbutton" style="width:60px" value="Show 3" onClick="fn_report_generated(3)" />
                              <input type="button" id="show_button" class="formbutton" style="width:60px" value="Show 4" onClick="fn_report_generated(4)" />
                           
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="12" align="center">
							<? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </tfoot>
           </table> 
           <br />
        </fieldset>
    </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
