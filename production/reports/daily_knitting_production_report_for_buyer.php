<?
/*-------------------------------------------- Comments
Purpose			: 	This form will Create Daily Knitting Production Report For Buyer
Functionality	:
JS Functions	:
Created by		:	
Creation date 	: 	14-11-2023
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		: This report copy to Daily Knitting Production Report 

*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
// echo load_html_head_contents("Daily Knitting Production Report", "../../", 1, 1,'','','');
echo load_html_head_contents("Daily Knitting Production Report", "../../", 1, 1, $unicode,1,1);

?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';

	var tableFilters =
	{
		col_0: "none",
		col_operation: {
		id: [""],
		col: [21],
		operation: ["sum"],
		write_method: ["innerHTML"],
		}
	}

	function fn_report_generated(report_type)
	{
		var job_no=$('#txt_job').val();
		var order_no=$('#txt_order').val();
		var fromDate=$('#txt_date_from').val();
		var toDate=$('#txt_date_to').val();
		var txt_style_ref=$('#txt_int_ref').val();
		
		// if(report_type==5 && (fromDate!=toDate))
		// {
		// 	alert("From Production Date and To Production Date must be same");
		// 	return;
		// }
		// if(report_type==2)
		// {
		// 	if($('#cbo_type').val()==2)
		// 	{
		// 		alert('Not applicable for this type');
		// 		return false;
		// 	}
		// }

		/*if(job_no!="" || order_no!="")
		{
			if(form_validation('cbo_company_name','Company')==false )
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_name*txt_date_from','Company*From Date')==false )
			{
				return;
			}
		}*/




		// if($('#cbo_company_name').val()==0){
		// 	var data='cbo_working_company_id*txt_date_from';
		// 	var filed='Working Company Name*From Date';
		// }
		// else
		// {
		// 	var data='cbo_company_name*txt_date_from';
		// 	var filed='Company Name*From Date';
		// }



		
		if($('#cbo_company_name').val()==0){
				var data='cbo_working_company_id*txt_date_from';
				var filed='Working Company Name*From Date';
			}
			else
			{

				if(job_no!="" || order_no!="" || txt_style_ref!="")
				{

					var data='cbo_company_name';
					var filed='Company Name';
				}
				else
				{

					var data='cbo_company_name*txt_date_from*txt_date_to';
					var filed='Company Name*From Date*To Date';
				}
			}



		// if(type==1)
		// {

		// 	if($('#cbo_company_name').val()==0){
		// 		var data='cbo_working_company_id*txt_date_from';
		// 		var filed='Working Company Name*From Date';
		// 	}
		// 	else
		// 	{

		// 		if(job_no!="" || order_no!="" || txt_style_ref!="")
		// 		{

		// 			var data='cbo_company_name';
		// 			var filed='Company Name';
		// 		}
		// 		else
		// 		{

		// 			var data='cbo_company_name*txt_date_from';
		// 			var filed='Company Name*From Date';
		// 		}
		// 	}

		// 	alert("1");
		// }
		// else
		// {
		// 	if($('#cbo_company_name').val()==0){
		// 		var data='cbo_working_company_id*txt_date_from';
		// 		var filed='Working Company Name*From Date';
		// 	}
		// 	else
		// 	{
		// 		var data='cbo_company_name*txt_date_from';
		// 		var filed='Company Name*From Date';
		// 	}
		// 	alert(2);
		// }



		if( form_validation(data,filed)==false )
		{
			return;
		}
		else
		{

			var report_title=$( "div.form_caption" ).html();
			var machine_wise_section=0;
			if(report_type==5)
			{
				report_type=1;
				machine_wise_section=1;
			}
			if(report_type==4)
			{
				var action = "report_generate_construction_wise";
			}
			else
			{
				var action = "report_generate";
			}
			


			var data="action="+action+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year*txt_job*txt_order*txt_int_ref*cbo_floor_id*txt_date_from*txt_date_to*cbo_working_company_id*cbo_location_id',"../../")+'&report_title='+report_title+'&report_type='+report_type+'&machine_wise_section='+machine_wise_section;
			//alert(data);return;
			freeze_window(5);

			


			http.open("POST","requires/daily_knitting_production_report_controller_for_buyer.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
	function fn_report_generated_today(type)
	{
		var job_no=$('#txt_job').val();
		var order_no=$('#txt_order').val();
		if($('#cbo_type').val()==2)
		{
			alert('Not applicable for this type');
			return false;
		}

		/* if(job_no!="" || order_no!="")
		{
			if(form_validation('cbo_company_name','Company')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_name*txt_date_from','Company*From Date')==false)
			{
				return;
			}
		} */

		if(type==1)
		{

			if($('#cbo_company_name').val()==0){
				var data='cbo_working_company_id*txt_date_from';
				var filed='Working Company Name*From Date';
			}
			else
			{

				if(job_no!="" || order_no!="" )
				{

					var data='cbo_company_name';
					var filed='Company Name';
				}
				else
				{

					var data='cbo_company_name*txt_date_from';
					var filed='Company Name*From Date';
				}
			}
		}
		else
		{
			if($('#cbo_company_name').val()==0){
				var data='cbo_working_company_id*txt_date_from';
				var filed='Working Company Name*From Date';
			}
			else
			{
				var data='cbo_company_name*txt_date_from';
				var filed='Company Name*From Date';
			}
		}


		/* if(txt_sales_no =="" && txt_booking_no ==""  )
        {
            if(txt_date_from =="" && txt_date_to =="")
            {
                alert("Please select either date range or sales order, booking no");
                return;
            }
        } */



		if( form_validation(data,filed)==false )
		{
			return;
		}
		else
		{
			var report_title=$( "div.form_caption" ).html();
			if (type==1) 
			{
				var data="action=report_generate_today"+get_submitted_data_string('cbo_company_name*cbo_type*cbo_buyer_name*cbo_year*txt_job*txt_order*cbo_knitting_source*cbo_floor_id*txt_date_from*txt_date_to*cbo_working_company_id*cbo_location_id*cbo_booking_type',"../../")+'&report_title='+report_title;
			}
			else
			{			
				var data="action=report_generate_today2"+get_submitted_data_string('cbo_company_name*cbo_type*cbo_buyer_name*cbo_year*txt_job*txt_order*cbo_knitting_source*cbo_floor_id*txt_date_from*txt_date_to*cbo_working_company_id*cbo_location_id*cbo_booking_type',"../../")+'&report_title='+report_title;
			}


			freeze_window(5);
			http.open("POST","requires/daily_knitting_production_report_controller_for_buyer.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split("####");

			$("#report_container2").html(response[0]);
			//alert (response[0]);
			//document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
	 		show_msg('3');
	
			 setFilterGrid("table_body",-1,tableFilters);

			release_freezing();
		}
	}

	function new_window(type)
	{

		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		if(type!=1)
		{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		}
		//alert(type);
 		//$("tr th:first-child").hide();
		//$("tr td:first-child").hide();
		//$("#summary_tab tr th:first-child").show();
		//$("#summary_tab tr td:first-child").show();

		//$("#fill_td th:first-child").show();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="none";
		if(type!=1)
		{
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="none";

		}
		$("tr th:first-child").show();
		$("tr td:first-child").show();
	}

	function selected_row(rowNo)
	{
		var isChecked=$('#tbl_'+rowNo).is(":checked");

		var in_out=$('#source_'+rowNo).val();

		if(isChecked==true)
		{
			var tot_row=$('#table_body tbody tr').length;
			for(var i=1; i<=tot_row; i++)
			{
				if(i!=rowNo)
				{
					try
					{
						if ($('#tbl_'+i).is(":checked"))
						{
							var inOut_noCurrent=$('#source_'+i).val();
							if((in_out!=inOut_noCurrent))
							{
								alert("Please Select Same Kniting Source.");
								$('#tbl_'+rowNo).attr('checked',false);
								return;
							}
						}
					}
					catch(e)
					{
						//got error no operation
					}
				}
			}
		}
	}

	function generate_delivery_challan_report()
	{
		var program_ids = ""; var source_ids=""; var total_tr=$('#table_body tbody tr').length;
		var company=$('#cbo_company_name').val();
		var from_date=$('#txt_date_from').val();
		var to_date=$('#txt_date_to').val();
		for(i=1; i<total_tr; i++)
		{
			try
			{
				if ($('#tbl_'+i).is(":checked"))
				{
					program_id = $('#production_id_'+i).val();
					source_id = $('#source_'+i).val();
					if(program_ids=="") program_ids= program_id; else program_ids +=','+program_id;
					if(source_ids=="") source_ids= source_id; else source_ids +=','+source_id;
				}
			}
			catch(e)
			{
				//got error no operation
			}
		}

		if(program_ids=="")
		{
			alert("Please Select At Least One Program");
			return;
		}
		//alert (program_ids)
		print_report(program_ids+'_'+source_ids+'_'+company+'_'+from_date+'_'+to_date, "delivery_challan_print", "requires/daily_knitting_production_report_controller_for_buyer" ) ;
	}

	function openmypage_job()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}

		var companyID = $("#cbo_company_name").val();
		var buyerID = $("#cbo_buyer_name").val();
		var yearID = $("#cbo_year").val();
		// var ordType = $("#cbo_type").val();
		var ordType = 2;
		var page_link='requires/daily_knitting_production_report_controller_for_buyer.php?action=job_no_search_popup&companyID='+companyID+'&buyerID='+buyerID+'&yearID='+yearID+'&ordType='+ordType;
		var title='Job No Search';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=590px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;

			$('#txt_job').val(job_no);
		}
	}

	function openmypage_order()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}

		var companyID = $("#cbo_company_name").val();
		var buyerID = $("#cbo_buyer_name").val();
		var yearID = $("#cbo_year").val();
		var jobID = $("#txt_job").val();
		// var ordType = $("#cbo_type").val();
		var ordType = 2;
		var page_link='requires/daily_knitting_production_report_controller_for_buyer.php?action=order_no_search_popup&companyID='+companyID+'&buyerID='+buyerID+'&yearID='+yearID+'&jobID='+jobID+'&ordType='+ordType;
		var title='Order No Search';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_no=this.contentDoc.getElementById("hide_order_no").value;

			$('#txt_order').val(order_no);
		}
	}

	function openmypage_int_ref()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}

		var companyID = $("#cbo_company_name").val();
		var buyerID = $("#cbo_buyer_name").val();
		var yearID = $("#cbo_year").val();
		var jobID = $("#txt_job").val();
		// var ordType = $("#cbo_type").val();
		
		var ordType = 2;
		var page_link='requires/daily_knitting_production_report_controller.php?action=int_ref_search_popup&companyID='+companyID+'&buyerID='+buyerID+'&yearID='+yearID+'&jobID='+jobID+'&ordType='+ordType;
		var title='Internal Ref. No Search';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var int_ref=this.contentDoc.getElementById("hide_order_no").value;

			$('#txt_int_ref').val(int_ref);
		}
	}

	function fnc_active_inactive(val)
	{
		if(val!=0)
		{
			// $('#cbo_buyer_name').removeAttr('disabled','disabled');
			$('#txt_job').removeAttr('disabled','disabled');
			$('#txt_order').removeAttr('disabled','disabled');
			$('#txt_int_ref').removeAttr('disabled','disabled');
		}
		else
		{
			// $('#cbo_buyer_name').attr('disabled','disabled');
			$('#txt_job').attr('disabled','disabled');
			$('#txt_order').attr('disabled','disabled');
			$('#txt_int_ref').attr('disabled','disabled');
		}
		if(val==1)
		{
			$('#cbo_knitting_source').removeAttr('disabled','disabled');
		}
		else
		{
			$('#cbo_knitting_source').attr('disabled','disabled');
		}
	}

function fn_disable_com(str){
	if(str==2){$("#show_textcbo_company_name").attr('disabled','disabled');}
	else{ $('#cbo_company_name').removeAttr("disabled");}
	if(str==1){$("#cbo_working_company_id").attr('disabled','disabled');}
	else{ $('#cbo_working_company_id').removeAttr("disabled");}

	if(str==1)
	{
		if($('#show_textcbo_company_name').val()==0){$("#cbo_working_company_id").removeAttr('disabled');}
	} else {
		if($('#cbo_working_company_id').val()==0){$("#show_textcbo_company_name").removeAttr('disabled');}
	}

}

	function print_report_button_setting(report_ids)
	{
		$('#show_button').hide();
		$('#show_button1').hide();
		
		var report_id=report_ids.split(",");
		report_id.forEach(function(items)
		{
			if(items==246){$('#show_button').show();}
			else if(items==245){$('#show_button1').show();}
			
		});
	}

	function getCompanyId()
	{
	    var company_id = document.getElementById('cbo_company_name').value;
		//var splidData=company_id.split(',');
		load_drop_down( 'requires/daily_knitting_production_report_controller_for_buyer', company_id, 'load_drop_down_floor', 'floor_td');
		fn_disable_com(1);
		get_php_form_data(company_id,'print_button_variable_setting','requires/daily_knitting_production_report_controller_for_buyer' );
	}
</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",''); ?>
		 <form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1">
         <h3 style="width:1550px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')"> -Search Panel</h3>
         <div id="content_search_panel" >
             <fieldset style="width:1550px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th class="must_entry_caption" width="130">Company Name</th>
 							<th class="must_entry_caption" width="150">Working Company</th>
 							<th class="must_entry_caption" width="150">Working Location</th>
                            <th width="130">Buyer</th>
                            <th width="70">Year</th>
                            <th width="80">Job</th>
                            <th width="80">Order</th>
                            <th width="80">Int Ref</th>
							<!-- <th width="80">Style Ref</th> -->
                           
                            <th width="130">Floor</th>
                            <th class="must_entry_caption" width="100" colspan="2">Production Date</th>
                            <th width="100"><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailyYarnStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px" /></th>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td id="td_company">
                                    <?
                                        echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                                    ?>
                                </td>

                                <td width="150" align="center">
			                        <?
			                            echo create_drop_down( "cbo_working_company_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/daily_knitting_production_report_for_buyer', $('#cbo_working_company_id').val(), 'load_drop_down_floor', 'floor_td');fn_disable_com(2);get_php_form_data(this.value,'print_button_variable_setting','requires/daily_knitting_production_report_controller_for_buyer' );load_drop_down('requires/daily_knitting_production_report_controller_for_buyer', this.value, 'load_drop_down_location', 'location_td' );" );
			                        ?>
                      			</td>

                      			<td id="location_td">
									<?
										echo create_drop_down( "cbo_location_id", 150, $blank_array,"", 1, "-- Select --", $selected, "",1,"" );
		                            ?>
		                        </td>

                                <td id="buyer_td">
                                	<?
                                        echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                                    ?>
                                </td>
                                <td>
								<?
                                    echo create_drop_down( "cbo_year", 70, create_year_array(),"", 1,"-- All --", "", "",0,"" );
                                ?>
                                </td>
                                <td><input type="text" name="txt_job" id="txt_job" value="" class="text_boxes" style="width:75px" placeholder="Wr/Br Job" onDblClick="openmypage_job();" /> </td>

                                <td><input type="text" id="txt_order" name="txt_order" class="text_boxes" style="width:75px;" placeholder="Wr/Br Order" onDblClick="openmypage_order();"  /></td>
								
                                <td><input type="text" id="txt_int_ref" name="txt_int_ref" class="text_boxes" style="width:75px;" placeholder="Wr/Br Order" onDblClick="openmypage_int_ref();" /></td>

								<!-- <td><input type="text" name="txt_style_ref" id="txt_style_ref" value="" class="text_boxes" style="width:75px" placeholder="Wr/Br Order"  disabled /> </td> -->

                               
                                <td id="floor_td">
                                    <? echo create_drop_down( "cbo_floor_id", 130, $blank_array,"", 1, "-- Select Floor --", 0, "",0 ); ?>
                                </td>
                                <td width="60" align="center">
                                     <input type="text" name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:60px" placeholder="From Date"/>
                                </td>
                                <td width="60">
                                     <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date"/>
                                </td>
                                <td width="100">
                                <input type="button" id="show_button1" class="formbutton" style="width:100px; display:none;" value="Prod. Wise" onClick="fn_report_generated(1)" />

								<!-- <td>
									<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px;" placeholder="From Date" readonly/>&nbsp; TO &nbsp;
									<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To Date" style="width:55px;" readonly/>
								</td> -->
                                 

                                </td>
                            </tr>
                            <tr>
                                <td colspan="11" align="center"><? echo load_month_buttons(1); ?></td>
                                
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
            <div style="width:100%;margin-top:10px;">
                <!--<input type="button" value="Delivery Challan" name="generate" id="generate" class="formbutton" style="width:150px" onClick="generate_delivery_challan_report()"/>-->
            </div>
            <br>
		</form>
	</div>
    <div id="report_container" align="center" style="padding-bottom: 10px;"></div>
    <div id="report_container2" align="left"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_multiselect('cbo_company_name','0','0','0','0');
	setTimeout[($("#td_company a").attr("onclick","disappear_list(cbo_company_name,'0');getCompanyId();") ,3000)];
	$('#cbo_floor_id').val(0);
</script>
</html>