<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Daily Cutting and Input Inhand Report.
Functionality	:	
JS Functions	:
Created by		:	Ashraful Islam
Creation date 	: 	26-04-2014
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
echo load_html_head_contents("Date Wise Production Report", "../../", 1, 1,$unicode,1,1);
?>	

<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	var tableFilters = 
	{
		
		col_operation: {
		id: ["grndTotID_gt_order_qnty","grndTotID_gt_lay_prev_qnty","grndTotID_gt_lay_qnty","grndTotID_gt_tot_lay_qnty","grndTotID_gt_cutting_prev_qnty","grndTotID_gt_cutting_qnty","grndTotID_gt_tot_cutting_qnty","grndTotID_gt_embroidery_rcv_qnty","grndTotID_gt_tot_embroidery_rcv_qnty","grndTotID_gt_sewing_in_prev_qnty","grndTotID_gt_sewing_in_qnty", "grndTotID_gt_tot_sewing_in_qnty","grndTotID_gt_sewing_out_prev_qnty","grndTotID_gt_sewing_out_qnty","grndTotID_gt_tot_sewing_out_qnty","grndTotID_gt_sewing_wip","grndTotID_gt_paking_finish_prev_qnty","grndTotID_gt_paking_finish_qnty","grndTotID_gt_tot_paking_finish_qnty","grndTotID_gt_carton_qnty","grndTotID_gt_finishing_wip", "grndTotID_gt_ex_fact_prev_qnty","grndTotID_gt_ex_fact_qnty","grndTotID_gt_tot_ex_fact_qnty","grndTotID_gt_ex_fact_fob","grndTotID_gt_ex_fact_wip","grndTotID_gt_ex_fact_wip_fob"],
		col: [10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 
	
 	function open_order_no()
	{
		var job_no=$("#txt_job_no").val();
		var job_id=$("#hidden_job_id").val();
		var buyer=$("#cbo_buyer_name").val();
		var cbo_year=$("#cbo_year").val();
	    var page_link='requires/daily_cutting_inhand_report_urmi_controller.php?action=order_wise_search&buyer='+buyer+'&job_id='+job_id+'&job_no='+job_no+'&cbo_year='+cbo_year;
		//alert(page_link);return; 
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=580px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			//alert(prodID); // product ID
			var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#txt_order_no").val(prodDescription);
			$("#hidden_order_id").val(prodID); 
		}
	}
	 
	function open_job_no()
	{
		var buyer=$("#cbo_buyer_name").val();
		var cbo_year=$("#cbo_year").val();
	    var page_link='requires/daily_cutting_inhand_report_urmi_controller.php?action=job_popup&buyer='+buyer+'&cbo_year='+cbo_year;
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var job_data=this.contentDoc.getElementById("selected_id").value;
			//alert(job_data); // Jov ID
			var job_data=job_data.split("_");
			var job_hidden_id=job_data[0];
			var job_no=job_data[1];
			//var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#txt_job_no").val(job_no);
			$("#hidden_job_id").val(job_hidden_id); 
			//alert($("#hidden_job_id").val())
		}
	 }
	 
	 

	function fn_generate_report(type)
	{
		if( form_validation('cbo_work_company_name*txt_production_date','Working Company Name*Production Date')==false )
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();

		var data="action=generate_report"+get_submitted_data_string('cbo_work_company_name*cbo_floor_name*cbo_location_name*cbo_buyer_name*cbo_year*txt_job_no*cbo_shipping_status*hidden_job_id*txt_order_no*txt_int_ref_no*txt_file_no*hidden_order_id*txt_production_date',"../../")+'&type='+type+'&report_title='+report_title;

		// var data="action=report_generate"+"&type="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_location*cbo_floor*cbo_job_year*txt_job_no*hidden_job_id*txt_production_date',"../../");

		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/daily_cutting_inhand_report_urmi_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			//alert (http.responseText);
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="All" class="formbutton" style="width:100px"/>';
			
			setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		$(".flt").css("display","none");
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
		$(".flt").css("display","block");
	}
	
	function openmypage_cutting_sewing_total(po,item,cutting,color,type,action)
	{
		var data=po+'**'+item+'**'+cutting+'**'+type+'**'+color;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_cutting_inhand_report_urmi_controller.php?data='+data+'&action='+action, 'Remarks View', 'width=650px,height=450px,center=1,resize=0,scrolling=0','../');
	}
	function openmypage_ex_fac_total(po,item,color,action)
	{
		var data=po+'**'+item+'**'+color;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_cutting_inhand_report_urmi_controller.php?data='+data+'&action='+action, 'Remarks View', 'width=450px,height=250px,center=1,resize=0,scrolling=0','../');
	}
	
	 function openmypage_fab_issue(po_id,color,type,action)
	{
		var data=po_id+'_'+color+'_'+type;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_cutting_inhand_report_urmi_controller.php?action='+action+'&data='+data, 'Issue Popup', 'width=780px,height=450px,center=1,resize=0,scrolling=0','../');
	}

	function reset_form()
	{
		$("#hidden_style_id").val("");
		$("#hidden_order_id").val("");
		$("#hidden_job_id").val("");
		
	}

	function openmypage_embl(company_id,order_id,order_number,insert_date,type,action,width,height,embl_type,color_id,today_total)
	{
		
		var popup_width=width;
		var popup_height=height;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_cutting_inhand_report_urmi_controller.php?company_id='+company_id+'&action='+action+'&insert_date='+insert_date+'&order_id='+order_id+'&order_number='+order_number+'&type='+type+'&embl_type='+embl_type+'&color_id='+color_id+'&today_total='+today_total, 'Detail Veiw', 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');
		
	} 	
	
  	function openmypage_swing(company_id,order_id,order_number,insert_date,type,action,width,height,resource,color_id,today_total)
	{
		var popup_width=width;
		var popup_height=height;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_cutting_inhand_report_urmi_controller.php?company_id='+company_id+'&action='+action+'&insert_date='+insert_date+'&order_id='+order_id+'&order_number='+order_number+'&type='+type+'&resource='+resource+'&color_id='+color_id+'&today_total='+today_total, 'Detail Veiw', 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');
	} 
	
	function today_fab_recv_po(company_id,order_id,color_id,prod_date,type)
	{
		var popup_width=560;
		var popup_height=380;
		//alert(type);
		if(type==1) 
		{ 
			var action="today_fabric_recv_qty";
			var title="Today Fabric  Recv Details";
		}
		else  
		{ 
			action="total_fabric_recv_qty";
			var title="Total Fabric  Recv Details";
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_cutting_inhand_report_urmi_controller.php?company_id='+company_id+'&action='+action+'&order_id='+order_id+'&color_id='+color_id+'&prod_date='+prod_date, title, 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');
		
	} 
	//function openmypage(po_break_down_id,item_id,action,location_id,floor_id,dateOrLocWise,country_id)
	function openmypage(po_break_down_id,item_id,action,country_id)
	{
		/*alert(po_break_down_id+'*'+item_id+'*'+action+'*'+country_id);
		return;*/
		//alert(po_break_down_id);
		if(action==2 || action==3)
			var popupWidth = "width=1050px,height=350px,";
		else if (action==10)
			var popupWidth = "width=550px,height=420px,";
		else
			var popupWidth = "width=800px,height=470px,";
		
		if (action==2)
		{
			var popup_caption="Embl. Issue Details";
		}
		else if (action==3)
		{
			var popup_caption="Embl. Rec. Details";
		}
		else
		{
			var popup_caption="Production Quantity";
		}
		emailwindow=dhtmlmodal.open('EmailBox','iframe','requires/daily_cutting_inhand_report_urmi_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&action='+action+'&country_id='+country_id, popup_caption, popupWidth+'center=1,resize=0,scrolling=0','../');
	}
	
	function openmypage_emb(wo_company,po_break_down_id,item_id,action,country_id,date)
	{
		/*alert(po_break_down_id+'*'+item_id+'*'+action+'*'+country_id);
		return;*/
		//alert(po_break_down_id);
		if(action==2 || action==3)
			var popupWidth = "width=1050px,height=350px,";
		else if (action==10)
			var popupWidth = "width=550px,height=420px,";
		else
			var popupWidth = "width=800px,height=470px,";
		
		if (action==2)
		{
			var popup_caption="Embl. Issue Details";
		}
		else if (action==3)
		{
			var popup_caption="Embl. Rec. Details";
		}
		else
		{
			var popup_caption="Production Quantity";
		}
		emailwindow=dhtmlmodal.open('EmailBox','iframe','requires/daily_cutting_inhand_report_urmi_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&action='+action+'&country_id='+country_id+'&wo_company='+wo_company+'&date='+date, popup_caption, popupWidth+'center=1,resize=0,scrolling=0','../');
	}

	function openmypage_production_popup(po,item,color,type,day,action,title,popup_width,popup_height) 	 		 	 
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_cutting_inhand_report_urmi_controller.php?po='+po+'&action='+action+'&item='+item+'&color='+color+'&type='+type+'&day='+day, title, 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');
	}

	function openmypage_production_sewing_popup(po,item,color,txt_production_date,type,day,action,title,popup_width,popup_height) 	
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_cutting_inhand_report_urmi_controller.php?po='+po+'&action='+action+'&item='+item+'&color='+color+'&type='+type+'&txt_production_date='+txt_production_date+'&day='+day, title, 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');
    }

	function getCompanyId() 
	{ 
	    var company_id = document.getElementById('cbo_work_company_name').value;
	    if(company_id !='')
		{
			var data="action=load_drop_down_location&choosenCompany="+company_id;
			http.open("POST","requires/daily_cutting_inhand_report_urmi_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data); 
			http.onreadystatechange = function()
			{
				if(http.readyState == 4) 
				{
					var response = trim(http.responseText);
					$('#location_td').html(response);
					set_multiselect('cbo_location_name','0','0','','0');
					setTimeout[($("#location_td a").attr("onclick","disappear_list(cbo_location_name,'0');getLocationId();") ,3000)];
					showReportButton(); 
				}			 
	      	};
	    }         
	}

	function showReportButton()
	{
		var company_id = document.getElementById('cbo_work_company_name').value;
		if(company_id !='')
		{
			var data="action=load_report_button&choosenCompany="+company_id;
			http.open("POST","requires/daily_cutting_inhand_report_urmi_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data); 
			http.onreadystatechange = function()
			{
				if(http.readyState == 4) 
				{
					var report_id = trim(http.responseText).split(',');
					// alert(report_id[1]);
					if(report_id!="")
					{           		
						$('.btn1').hide();
						$('.btn2').hide();
						$('.btn3').hide();
						$('.btn4').hide();
						$('.btn5').hide();
						$('.btn6').hide();
						$('.btn7').hide();
						$('.btn8').hide();
						$('.btn9').hide();

						$('.btn_'+report_id[0]).show();
						$('.btn_'+report_id[1]).show();
						$('.btn_'+report_id[2]).show();
						$('.btn_'+report_id[3]).show();
						$('.btn_'+report_id[4]).show();
						$('.btn_'+report_id[5]).show();
						$('.btn_'+report_id[6]).show();
						$('.btn_'+report_id[7]).show();
						$('.btn_'+report_id[8]).show();
						$('.btn_'+report_id[9]).show();
					}
				}			 
			};
		}
	}

	function getLocationId() 
	{	 
	    var location_id = document.getElementById('cbo_location_name').value;

	    if(location_id !='') {
	      	var data="action=load_drop_down_floor&choosenLocation="+location_id;
	      	http.open("POST","requires/daily_cutting_inhand_report_urmi_controller.php",true);
	      	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	      	http.send(data); 
	      	http.onreadystatechange = function()
			{
	        	if(http.readyState == 4) 
	          	{
	            	var response = trim(http.responseText);
	              	$('#floor_td').html(response);
	              	set_multiselect('cbo_floor_name','0','0','','0');
	          	}			 
	    	};
	    }         
	}
	
	function load_work_company()
	{
		if($('#table_bodycbo_work_company_name tr').length==1)
		{
			$('#cbo_work_company_name1').click();
			document.getElementById("cbo_work_company_name1").checked = true;
			getCompanyId();
		}
	}

	function getButtonSetting()
	{
		var company_id = document.getElementById('cbo_work_company_name').value;
		get_php_form_data(company_id,'print_button_variable_setting','requires/daily_cutting_inhand_report_urmi_controller' );
	}

	function print_report_button_setting(report_ids) 
    {
        //alert(report_ids);
        $('#show_button').hide();
        $('#show_button2').hide();
		$('#show_button3').hide();
        $('#cutting_status').hide();
        $('#cutting_status2').hide();
        $('#cutt_to_ship').hide();
        $('#production_status').hide();
        $('#show_button4').hide();
        $('#show_button5').hide();
		$('#show_button6').hide();
        var report_id=report_ids.split(",");
        report_id.forEach(function(items)
		{
            if(items==108){$('#show_button').show();}
            else if(items==195){$('#show_button2').show();}
			else if(items==242){$('#show_button3').show();}
            else if(items==4){$('#cutting_status').show();}
            else if(items==5){$('#cutting_status2').show();}
            else if(items==840){$('#cutt_to_ship').show();}
            else if(items==841){$('#production_status').show();}
            else if(items==359){$('#show_button4').show();}
            else if(items==712){$('#show_button5').show();}
			else if(items==389){$('#show_button6').show();}
        });
    }
</script>

</head>
 
<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center"> 
		<? echo load_freeze_divs ("../../",'');  ?>
		<h3 style="width:1796px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
		<div style="width:100%;" align="center" id="content_search_panel">
			<form id="dateWiseProductionReport_1">    
				<fieldset style="width:1796px;">
					<table class="rpt_table" width="1796" cellpadding="0" cellspacing="0" align="center" border="1" rules="all">
						<thead>                    
							<tr>
								<th class="must_entry_caption" width="210" >Working Company</th>
								<th class="" width="150" >Location</th>
								<th class="" width="150" >Floor</th>
								<th width="130" >Buyer Name</th>
								<th width="60">Job Year</th>
								<th width="100">Job No</th>
								<th width="100">Order No </th>
								<th width="100" title="Working in Show and Cutting Status button only">Int. Ref. No </th>
								<th width="100" title="Working in Show and Cutting Status button only">File No </th>
								<th width="150">Shipping Status</th>
								<th width="100" class="must_entry_caption">Production Date </th>
								<th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form()"/></th>
							</tr>
						</thead>
						<tbody>
							<tr class="general">
								<td align="center" id="td_company"> 
									<?
										echo create_drop_down( "cbo_work_company_name", 200, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0  $company_cond order by company_name","id,company_name", 0, "-- Select Company --", $selected, "" );
									?>
								</td>
								<td align="center" id="location_td"> 
									<?
										echo create_drop_down( "cbo_location_name", 200, $blank_array,"","", "-- Select location --", "", "" );
									?>
								</td>
								<td align="center" id="floor_td"> 
									<?
										echo create_drop_down( "cbo_floor_name", 200, $blank_array,"","", "-- Select floor --", "", "" );
									?>
								</td>
								<td align="center">
									<? 
									echo create_drop_down( "cbo_buyer_name", 130, "SELECT a.id,a.buyer_name from lib_buyer a where a.status_active=1 and a.is_deleted=0 and a.party_type not in('2') group by  a.id,a.buyer_name  order by a.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0,"" );
									?>
								</td>
								<td>
									<?
									echo create_drop_down( "cbo_year", 60, $year,"", 1, "Year--", 0, "",0 );
									?>
								</td>
								<td>
									<input type="text" id="txt_job_no"  name="txt_job_no"  style="width:100px" class="text_boxes" onDblClick="open_job_no()" placeholder="Browse" readonly />
									<input type="hidden" id="hidden_job_id"  name="hidden_job_id" />
								</td>
								<td>
									<input type="text" id="txt_order_no"  name="txt_order_no"  style="width:100px" class="text_boxes" onDblClick="open_order_no()" placeholder="Browse" readonly />
									<input type="hidden" id="hidden_order_id"  name="hidden_order_id" />
								</td>
								<td>
									<input type="text" id="txt_int_ref_no"  name="txt_int_ref_no"  style="width:100px" class="text_boxes" placeholder="Internal Ref. No" />
								</td>
								<td>
									<input type="text" id="txt_file_no"  name="txt_file_no"  style="width:100px" class="text_boxes" placeholder="File No" />
								</td>
								<td>
									<?
										echo create_drop_down( "cbo_shipping_status", 150, $shipment_status,"", 0, "-- Select Shipping Status", 0, "",0 );
									?>
								</td>
								<td>
									<input type="text" name="txt_production_date" id="txt_production_date" class="datepicker" style="width:70px"  placeholder="To Date" value="<? echo date("d-m-Y"); ?>" >
								</td>
								<td>
									<input type="button" id="show_button" class="formbutton btn1 btn_108" style="width:70px; display: none;" value="Show" onClick="fn_generate_report(1)" />
									<input type="button" id="show_button2" class="formbutton btn2 btn_195" style="width:70px;display: none;" value="Show 2" onClick="fn_generate_report(2)" />
									<input type="button" id="show_button3" class="formbutton btn3 btn_242" style="width:70px;display: none;" value="Show 3" onClick="fn_generate_report(3)" />
									<input type="button" id="cutting_status" class="formbutton btn4 btn_4" style="width:84px;display: none;" value="Cutting Status" onClick="fn_generate_report(4)" />
									<input type="button" id="cutting_status2" class="formbutton btn5 btn_5" style="width:90px;display: none;" value="Cutting Status2" onClick="fn_generate_report(44)" />
									<input type="button" id="cutt_to_ship" class="formbutton btn6 btn_840" style="width:95px;display: none;" value="Cutt to Ship" onClick="fn_generate_report(5)" />     
									<input type="button" id="production_status" class="formbutton btn7 btn_841" style="width:105px;display: none;" value="Production Status" onClick="fn_generate_report(6)" />
									<input type="button" id="show_button4" class="formbutton btn8 btn_359" style="width:105px;display: none;" value="Show 4" onClick="fn_generate_report(7)" />                        
									<input type="button" id="show_button5" class="formbutton btn9 btn_712" style="width:105px;display: none;" value="Show 5" onClick="fn_generate_report(8)" />   
									<input type="button" id="show_button6" class="formbutton btn10 btn_389" style="width:105px;display: none;" value="Show 6" onClick="fn_generate_report(9)" />                          
								</td>
							</tr>
						</tbody>
					</table>
				</fieldset>
			</form> 
		</div>
		<div id="report_container" style="padding: 10px;"></div>
		<div id="report_container2"></div>  
	</div>
</body>
<script> 
	set_multiselect('cbo_work_company_name','0','0','','0');
	set_multiselect('cbo_location_name','0','0','','0'); 
	set_multiselect('cbo_floor_name','0','0','','0'); 
	set_multiselect('cbo_shipping_status','0','0','','0'); 

	setTimeout[($("#td_company a").attr("onclick","disappear_list(cbo_work_company_name,'0');getCompanyId();getButtonSetting();") ,3000)];
	document.getElementById('cbo_year').value='<? echo date('Y');?>';
	load_work_company();
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
