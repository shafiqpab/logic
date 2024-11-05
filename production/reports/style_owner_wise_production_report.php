<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Order wise Production Report.
Functionality	:
JS Functions	:
Created by		:	kamrul
Creation date 	: 	27-07-2023
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
echo load_html_head_contents("Style Owner Wise Production Report", "../../", 1, 1,$unicode,1,1);

?>
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission = '<? echo $permission; ?>';

	function openmypage_job()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		
		
				var company = $("#cbo_company_name").val();
				var style_onwer = $("#cbo_style_owner_name").val();
				var year=$("#cbo_year_selection").val();

				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_owner_wise_production_report_controller.php?company='+company+'&style_onwer='+style_onwer+'&year='+year+'&action=job_no_popup', 'Job No Search', 'width=700px,height=420px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemailid=this.contentDoc.getElementById("txt_job_id");
				var response=theemailid.value.split('_');
				if ( theemailid.value!="" )
				{
					//alert (response[0]);
					freeze_window(5);
					$("#hidd_job_id").val(response[0]);
					$("#txt_job_no").val(response[1]);
					release_freezing();
				}
			}
		
	}

	function openmypage_style()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
					var company = $("#cbo_company_name").val();
					var style_onwer = $("#cbo_style_owner_name").val();
					var year=$("#cbo_year_selection").val();

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_owner_wise_production_report_controller.php?company='+company+'&style_onwer='+style_onwer+'&year='+year+'&action=style_no_popup', 'Style No Search', 'width=700px,height=420px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemailid=this.contentDoc.getElementById("txt_style_ref_no");
				var response=theemailid.value.split('_');
				if ( theemailid.value!="" )
				{
					// alert (response[1]);
					freeze_window(5);
					$("#hidd_style_ref_no").val(response[0]);
					$("#txt_style_ref_no").val(response[1]);
					release_freezing();
				}
			}
		//}
	}

	function open_order_no()
	{
		if( form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
			
		
					var company = $("#cbo_company_name").val();
					var style_onwer = $("#cbo_style_owner_name").val();
					var year=$("#cbo_year_selection").val();

					emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_owner_wise_production_report_controller.php?company='+company+'&style_onwer='+style_onwer+'&year='+year+'&action=order_no_popup', 'Po No Search', 'width=500px,height=420px,center=1,resize=0,scrolling=0','../')
				emailwindow.onclose=function()
				{
						var theform=this.contentDoc.forms[0];
						var prodID=this.contentDoc.getElementById("txt_selected_id").value;
						//alert(prodID); // product ID
						var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
						$("#txt_order_no").val(prodDescription);
						$("#hidd_po_id").val(prodID);
				}
	}

	function generate_report(report_type)
	{
		if(form_validation('cbo_company_name*cbo_style_owner_name*txt_date_from*txt_date_to','Comapny Name*Report Type*From Date*To Date')==false)
		return;
	
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_style_owner_name*cbo_source*cbo_wo_company_name*cbo_location*cbo_buyer_name*cbo_brand*txt_style_ref_no*txt_job_no*hidd_job_id*txt_order_no*hidd_po_id*txt_ref_no*txt_date_from*txt_date_to',"../../")+'&report_type='+report_type;
		//alert (data);
		freeze_window(3);
		http.open("POST","requires/style_owner_wise_production_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("****");
			show_msg('3');
			release_freezing();
			
			//$("#report_container2").html(reponse[0]);  
			//document.getElementById('report_container').innerHTML = report_convert_button('../../'); 
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:135px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Html Preview" name="All" class="formbutton" style="width:120px"/>';
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
		'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="all" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			d.close(); 

		//$('#table_body tr:first').show();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="425px";
	}

	function openmypage_cutting_popup(w_com,job_id,po_id,item_id,color_id,txt_date_from,txt_date_to,production_date,page_title,action)
	{
		//alert (production_date);
		var width_pop=850;	
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_owner_wise_production_report_controller.php?w_com='+w_com+'&job_id='+job_id+'&po_id='+po_id+'&item_id='+item_id+'&color_id='+color_id+'&txt_date_from='+txt_date_from+'&txt_date_to='+txt_date_to+'&production_date='+production_date+'&action='+action, page_title, 'width='+width_pop+'px,height=400px,center=1,resize=0,scrolling=0','../');

	}

	function openmypage_sweing_popup(w_com,job_id,po_id,item_id,color_id,txt_date_from,txt_date_to,production_date,page_title,action)
	{
		//alert (production_date);
		var width_pop=820;	
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_owner_wise_production_report_controller.php?w_com='+w_com+'&job_id='+job_id+'&po_id='+po_id+'&item_id='+item_id+'&color_id='+color_id+'&txt_date_from='+txt_date_from+'&txt_date_to='+txt_date_to+'&production_date='+production_date+'&action='+action, page_title, 'width='+width_pop+'px,height=400px,center=1,resize=0,scrolling=0','../');

	}

</script>
</head>
<body onLoad="set_hotkey();">
<form id="dateWiseProductionReport_1">
    <div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",'');  ?>
        <h3 style="width:1420px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')"> -Search Panel</h3>
        <div id="content_search_panel" >
            <fieldset style="width:1410px;">
            	<table align="center" cellspacing="0" cellpadding="0" width="100%" border="1" rules="all" class="rpt_table" >
                    <thead>
                        <th class="must_entry_caption">Company Name</th>
                        <th  class="must_entry_caption">Style Owner</th>
						<th >Source</th>
						<th>Working Factory</th>
						<th>Location</th>
                        <th>Buyer Name</th>
						<th>Brand</th>
						<th>Style Ref</th>
						<th>Job No</th>
						<th>Order No</th>
						<th>Internal Ref</th>						
						<th class="must_entry_caption" colspan="2">Production Date</th>
						<th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                    </thead>
                    <tbody>
						<tr class="general">
							<td  width="100">
								<?
									echo create_drop_down( "cbo_company_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-Select Company-", $selected, "load_drop_down( 'requires/style_owner_wise_production_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/style_owner_wise_production_report_controller', this.value, 'load_drop_down_location', 'location_td');" );
								?>
							</td>
							<td  width="100">
								<?
									echo create_drop_down( "cbo_style_owner_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-Select Company-", $selected, "" );
								?>
							</td>
							<td  width="100">
								<?
									echo create_drop_down("cbo_source",100,$knitting_source,"", 1, "-- Select --", 0,"load_drop_down( 'requires/style_owner_wise_production_report_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_party','factory_td');",0,'1,3');
								?>	
							</td>							
							<td width="120" id="factory_td"> 
								<?
									echo create_drop_down( "cbo_wo_company_name", 100, $blank_array,"", 1, "-Select Company-", $selected, "",1,"" );
								?>
							</td>  							
							<td  width="100" id="location_td">
								<?
									echo create_drop_down( "cbo_location", 100, $blank_array,"", 1, "-Select Location-", $selected, "",1,"" );
								?>
							</td>
							<td width="100" id="buyer_td">
									<?
										echo create_drop_down( "cbo_buyer_name", 110, $blank_array,"", 1, "-Select Buyer-", $selected, "",1,"" );
									?>
							</td>
							<td width="100" id="brand_td">
									<? 
										echo create_drop_down( "cbo_brand", 100, $blank_array,"", 1, "-- All --", $selected, "",1,"" );
									?>
							</td>  
							<td width="100">
								<input type="text" name="txt_style_ref_no" id="txt_style_ref_no" class="text_boxes" style="width:100px" placeholder="Write/Browse" onChange="fnRemoveHidden('hidd_style_ref_no');" onDblClick="openmypage_style();
								"  />
								<input type="hidden" id="hidd_style_ref_no" name="hidd_style_ref_no" style="width:100px" />
							</td>
							<td>
								<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px" placeholder="Write/Browse" onChange="fnRemoveHidden('hidd_job_id');" onDblClick="openmypage_job();"  />
								<input type="hidden" id="hidd_job_id" name="hidd_job_id" style="width:100px" />
							</td>					
							<td width="100">
								<input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:80px"  onDblClick="open_order_no()" placeholder="Browse/Write">
								<input type="hidden" id="hidd_po_id" name="hidd_po_id" style="width:100px" />
							</td>
							<td width="100">
								<input name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:75px" placeholder="Write" >
							</td>
							<td width="70">
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"  >
							</td>  
							<td width="70">
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date"  >
							</td>
							<td>
								<input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="generate_report(1)" />
							</td>
										
					    </tr>
					</tbody>
				</table>
				<table cellpadding="1" cellspacing="2">
                      <tr>
							<td>
								<? echo load_month_buttons(1); ?>
							</td>
							<td colspan="18">
								<input type="button" id="show_button2" class="formbutton" style="width:80px" value="Cutting" onClick="generate_report(2)" />
							</td>
							<td>
								<input type="button" id="show_button3" class="formbutton" style="width:80px" value="Sewing" onClick="generate_report(3)" />
							</td>
							<td>
								<input type="button" id="show_button4" class="formbutton" style="width:80px" value="Finishing" onClick="generate_report(4)" />
							</td>
							<td>
								<input type="button" id="show_button5" class="formbutton" style="width:100px" value="Embellishment" onClick="generate_report(5)" />
							</td>		
					 </tr>							               
                </table>   
            </fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
</form>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$('#cbo_location').val(0);
	$('#cbo_year').val('<?=date('Y');?>');
</script>
<script class="include" type="text/javascript" src="../../js/chart/logic_chart.js"></script>
</html>
