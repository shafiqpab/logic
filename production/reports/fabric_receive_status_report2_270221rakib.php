<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Fabric Receive Status Report 2.
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	15-01-2015
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
echo load_html_head_contents("Fabric Receive Status Report 2", "../../", 1, 1,'',1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	 
	function fn_report_generated(type)
	{
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		else
		{
			if ($('#chk_no_boking').attr('checked')) var chk_no_boking = 1; else var chk_no_boking = 0;
			
			var search_string = $('#txt_search_string').val();
			var job = $('#txt_job_no').val();
			var booking = $('#txt_booking_no').val();
			var date_from = $('#txt_date_from').val();
			//alert('system');
			if (search_string=="" && date_from=="" && job=="" && booking=="")
			{
				if (form_validation('txt_date_from*txt_date_to', 'Date Form*Date To')==false)
				{
					return;
				}
			}
			
			if(type==1)
			{
				var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_date_type*txt_date_from*txt_date_to*cbo_year*txt_job_no*txt_booking_no*cbo_type*txt_search_string*cbo_order_status*cbo_discrepancy*cbo_active_status',"../../")+'&type='+type + '&chk_no_boking=' + chk_no_boking;
			}
			else
			{
				var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_date_type*txt_date_from*txt_date_to*cbo_year*txt_job_no*txt_booking_no*cbo_type*txt_search_string*cbo_order_status*cbo_discrepancy*cbo_active_status',"../../")+'&type='+type + '&chk_no_boking=' + chk_no_boking;
			}
			
			freeze_window(3);
			http.open("POST","requires/fabric_receive_status_report2_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{ 
			show_msg('3');			
			var response=trim(http.responseText).split("####");
			//alert(response[3]);
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:155px"/></a>'; 
			$('#report_container').append('&nbsp;&nbsp;&nbsp;<a href="'+response[2]+'" style="text-decoration:none"><input type="button" value="Convert To Excel Short" name="excel" id="excel" class="formbutton" style="width:155px"/></a>');
			$('#report_container').append('&nbsp;&nbsp;&nbsp;<a href="'+response[4]+'" style="text-decoration:none"><input type="button" value="Color Wise Summary" name="excel" id="excel" class="formbutton" style="width:155px"/></a>');
			var tot_rows=$('#table_body tr').length;
			var rpt_type=response[5];
			if(tot_rows>1)
			{
				var type=$('#cbo_type').val();
				if(rpt_type==1 || rpt_type==3)
				
				var tableFilters = 
				{
					col_operation: 
					{
						id: ["total_tot_order_qnty","total_tot_country_ship_qty","value_tot_mkt_required","value_tot_yarnAllocationQty","value_tot_yetTo_allocate","value_tot_yarn_issue","value_tot_net_trans_yarn","value_tot_yarn_balance","value_tot_fabric_req","value_tot_grey_recv_qnty","value_tot_grey_prod_balance","value_tot_net_del_store","value_tot_greyKnitFloor","value_tot_grey_production_qnty","value_tot_grey_purchase_qnty","value_tot_net_gray_return","value_tot_net_trans_knit_qnty","value_tot_grey_available","value_tot_grey_balance","value_tot_grey_issue","value_tot_grey_inhand","value_tot_receive_by_batch","value_tot_grey_req_color","value_tot_batch","value_tot_dye_qnty","value_tot_grey_balance_color","value_tot_dye_qnty_balance","value_tot_fini_req","value_tot_fini_receive","value_tot_fabric_recv_balance","value_tot_fin_delivery_qty","value_tot_finProdFloor","value_tot_fabric_production","value_tot_fabric_purchase","value_tot_fab_net_return","value_tot_trans_finish_qnty","value_tot_fabric_available","value_tot_fabric_rec_bal","value_tot_issue_to_cut_qnty","value_tot_yet_to_cut","value_tot_fabric_left_over"],
						col:[13,19,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64],
						//col:[12,18,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63],
				   		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
				   		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				}
			}
			else if(rpt_type==2)
			{
				var tableFilters = 
				{
					col_operation: 
					{
						id: ["total_tot_order_qnty","total_tot_country_ship_qty","value_tot_mkt_required","value_tot_yarnAllocationQty","value_tot_yetTo_allocate","value_tot_yarn_issue","value_tot_net_trans_yarn","value_tot_yarn_balance","value_tot_fabric_req","value_tot_grey_recv_qnty","value_tot_grey_prod_balance","value_tot_net_del_store","value_tot_greyKnitFloor","value_tot_grey_production_qnty","value_tot_grey_purchase_qnty","value_tot_net_gray_return","value_tot_net_trans_knit_qnty","value_tot_grey_available","value_tot_grey_balance","value_tot_grey_issue","value_tot_grey_inhand","value_tot_receive_by_batch","value_tot_grey_req_color","value_tot_batch","value_tot_dye_qnty","value_tot_grey_balance_color","value_tot_dye_qnty_balance","value_tot_fini_req","value_tot_fini_receive","value_tot_fabric_recv_balance","value_tot_fin_delivery_qty","value_tot_finProdFloor","value_tot_fabric_production","value_tot_fabric_purchase","value_tot_fab_net_return","value_tot_trans_finish_qnty","value_tot_fabric_available","value_tot_fabric_rec_bal","value_tot_issue_to_cut_qnty","value_tot_yet_to_cut","value_tot_fabric_left_over"],
						//col: [11,14,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,  40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55],
						col: [13,16,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,  43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61],
						//col: [12,15,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,  42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58, 59,60],
					    operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
					    write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				}
			}
			release_freezing();
			setFilterGrid("table_body",-1,tableFilters);
		}		
	}

	function new_window()
	{
		document.getElementById('company_id_td').style.visibility='visible';
		document.getElementById('date_td').style.visibility='visible';
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write(document.getElementById('buyer_summary').innerHTML);
		document.getElementById('company_id_td').style.visibility='hidden';
		document.getElementById('date_td').style.visibility='hidden';
		d.close();
	}

	function show_inner_filter(e)
	{
		if (e!=13) {var unicode=e.keyCode? e.keyCode : e.charCode } else {unicode=13;}
		if (unicode==13 )
		{
			fn_report_generated(2);
		}
	}
 	
	function search_by(val,type)
	{
		if(type==2)
		{
			$('#txt_search_string').val('');
			if(val==1) $('#search_by_td_up').html('Order No');
			else if(val==2) $('#search_by_td_up').html('Style Ref.');
			else if(val==3) $('#search_by_td_up').html('File No');
			else if(val==4) $('#search_by_td_up').html('Internal Ref');
		}
		if(type==1)
		{
			$('#txt_date_from').val('');
			$('#txt_date_to').val('');
			if(val==1) $('#date_td').html('Country Shipment Date');
			else if(val==2) $('#date_td').html('Pub. Ship Date');
			else if(val==3) $('#date_td').html('Org. Ship Date');
			else if(val==4) $('#date_td').html('PO Insert Date');
			else $('#date_td').html('Shipment Date');
		}
	}

	function open_febric_receive_status_order_wise_popup(order_id,type,color)
	{
		var popup_width='';
		if(type=="fabric_receive" || type=="fabric_purchase" || type=="grey_issue" || type=="dye_qnty" || type=="grey_return" || type=="finish_return" || type=="receive_by_batch") 
		{
			popup_width='990px';
		}
		else if(type=="grey_receive" || type=="grey_purchase")
		{
			popup_width='1050px';	
		}
		else popup_width='760px';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/fabric_receive_status_report2_controller.php?order_id='+order_id+'&action='+type+'&color='+color, 'Detail Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}

	function openmypage(order_id,type,yarn_count,yarn_comp_type1st,yarn_comp_percent1st,yarn_comp_type2nd,yarn_comp_percent2nd,yarn_type)
	{
		var popup_width='';
		if(type=="yarn_issue_not") popup_width='1000px'; else popup_width='890px';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/fabric_receive_status_report2_controller.php?order_id='+order_id+'&action='+type+'&yarn_count='+yarn_count+'&yarn_comp_type1st='+yarn_comp_type1st+'&yarn_comp_percent1st='+yarn_comp_percent1st+'&yarn_comp_type2nd='+yarn_comp_type2nd+'&yarn_comp_percent2nd='+yarn_comp_percent2nd+'&yarn_type_id='+yarn_type, 'Detail Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}

	function generate_worder_report(type,booking_no,company_id,order_id,fabric_nature,fabric_source,job_no,approved,action,page)
	{
		var show_yarn_rate='';
		var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
		
		if (r==true) show_yarn_rate="1"; else show_yarn_rate="0";
		
		var data="action="+action+
					'&txt_booking_no='+"'"+booking_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&txt_order_no_id='+"'"+order_id+"'"+
					'&cbo_fabric_natu='+"'"+fabric_nature+"'"+
					'&cbo_fabric_source='+"'"+fabric_source+"'"+
					'&id_approved_id='+"'"+approved+"'"+
					'&txt_job_no='+"'"+job_no+"'"+
					'&show_yarn_rate='+show_yarn_rate;
					
					//var data="action="+type+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*id_approved_id*txt_job_no',"../../")+'&report_title='+$report_title+'&show_yarn_rate='+show_yarn_rate+'&path=../../';
		if(type==1)	
		{			
			http.open("POST","../../order/woven_order/requires/short_fabric_booking_controller.php",true);
		}
		else if(type==2)
		{
			if(page==154) http.open("POST","../../order/woven_order/requires/fabric_booking_urmi_controller.php",true);
			else http.open("POST","../../order/woven_order/requires/fabric_booking_controller.php",true);
		}
		else
		{
			http.open("POST","../../order/woven_order/requires/sample_booking_controller.php",true);
		}
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_fabric_report_reponse;
	}
	
	function generate_fabric_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
			d.close();
		}
	}

	function generate_pre_cost_report(type,job_no,company_id,buyer_id,style_ref,costing_date)
	{
		var data="action="+type+
				'&txt_job_no='+"'"+job_no+"'"+
				'&cbo_company_name='+"'"+company_id+"'"+
				'&cbo_buyer_name='+"'"+buyer_id+"'"+
				'&txt_style_ref='+"'"+style_ref+"'"+
				'&txt_costing_date='+"'"+costing_date+"'"+
				"&zero_value=1"+
				'&path=../../';
					
		http.open("POST","../../order/woven_order/requires/pre_cost_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_report_reponse;
	}

	function fnc_generate_report_reponse()
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

	/*function progress_comment_popup(job_no,po_id,template_id,tna_process_type)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', '../../reports/management_report/merchandising_report/requires/shipment_date_wise_wp_report_controller.php?job_no='+job_no+'&po_id='+po_id+'&template_id='+template_id+'&tna_process_type='+tna_process_type+'&action=update_tna_progress_comment'+'&permission='+permission, "TNA Progress Comment", 'width=1030px,height=390px,center=1,resize=1,scrolling=0','../');
	}*/


	function progress_comment_popup(job_no,po_id,template_id,tna_process_type)
	{
		var data="action=update_tna_progress_comment"+
				'&job_no='+"'"+job_no+"'"+
				'&po_id='+"'"+po_id+"'"+
				'&template_id='+"'"+template_id+"'"+
				'&tna_process_type='+"'"+tna_process_type+"'"+
				'&permission='+"'"+permission+"'";	
								
		http.open("POST","../../reports/management_report/merchandising_report/requires/shipment_date_wise_wp_report_controller.php",true);
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_progress_comment_reponse;	
	}

	function generate_progress_comment_reponse()
	{
		if(http.readyState == 4) 
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
			d.close();
		}
	}

	function country_order_dtls(po_id,start_date,end_date,buyer_id,job_no,action)
	{  
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/fabric_receive_status_report2_controller.php?po_id='+po_id+'&start_date='+start_date+'&end_date='+end_date+'&buyer_id='+buyer_id+'&job_no='+job_no+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}

	function openmypage_booking()
    {
        if( form_validation('cbo_company_name','Company Name')==false )
        {
            return;
        }
        
        var title='Booking No Search';
        var action='booking_no_popup';
        var widthVal='1055px';
        
        var companyID = $("#cbo_company_name").val();
        var buyer_name = $("#cbo_buyer_name").val();
        var cbo_year  = $("#cbo_year").val();
        var page_link='requires/fabric_receive_status_report2_controller.php?action='+action+'&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year='+cbo_year;
        
        
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+widthVal+',height=370px,center=1,resize=1,scrolling=0','../../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            // alert(no+','+id);
            
            var no=this.contentDoc.getElementById("txt_booking_no").value;
            var id=this.contentDoc.getElementById("txt_booking_id").value;
            var po=this.contentDoc.getElementById("txt_order_id").value;
            $('#txt_booking_no').val(no);
            $('#txt_booking_id').val(id);             
        }
    }
	
	function openmypage_image(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{

		}
	}
	function print_report_button_setting(report_ids) 
    {
     
        $('#show_button').hide();
        $('#show_button1').hide();
        $('#show_button2').hide();
        var report_id=report_ids.split(",");
        report_id.forEach(function(items)
        {
            if(items==23){$('#summary_button').show();}
            else if(items==124){$('#order_wise_button').show();}
            else if(items==223){$('#style_wise_button').show();}
        });
    }
</script>

<style>
	hr
	{
		color: #676767;
		background-color: #676767;
		height: 1px;
	}
</style> 
</head>
 
<body onLoad="set_hotkey();">

<form id="fabricReceiveStatusReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1240px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1240px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                	<th class="must_entry_caption">Company Name</th>
                    <th>Buyer Name</th>
                    <th>Date Type</th>
                    <th colspan="2" title="Data Will be Populated Acording to Pub. Ship Date Wise." id="date_td">Shipment Date</th>
                    <th>Job Year</th>
                    <th>Job No</th>
                    <th>Search Type</th>
                    <th id="search_by_td_up">Order No</th>
                    <th>Booking No</th>
                    <th>Active Status</th>
                    <th>Order Status</th>
                    <th>Discrepancy</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('fabricReceiveStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/fabric_receive_status_report2_controller',this.value, 'load_drop_down_buyer', 'buyer_td' ); get_php_form_data(this.value,'load_variable_settings','requires/fabric_receive_status_report2_controller');get_php_form_data(this.value,'print_button_variable_setting','requires/fabric_receive_status_report2_controller' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                       <td>
                         	<? 
								$date_type_arr=array(1=>"Country Ship Date",2=>"Pub. Ship Date",3=>"Org. Ship Date",4=>"PO Insert Date");
                                echo create_drop_down( "cbo_date_type", 120, $date_type_arr,"", 0, "-Select-", 0, "search_by(this.value,1);",0,"" );
                            ?>
                       </td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" readonly>
                        </td>
                        <td>
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date" readonly>
                        </td>
                        <td>
                        	<? //date("Y",time())
								echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", "", "",0,"" );
							?>
                        </td>
                        <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px" /></td>
                        <td>
                        	<?
								$search_by_arr=array(1=>"Order No",2=>"Style Ref.",3=>"File No",4=>"Internal Ref");
								echo create_drop_down( "cbo_type",80, $search_by_arr,"",0, "", "",'search_by(this.value,2)',0 );
							?>
                        </td> 
                        <td id="search_by_td"><input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:70px" /></td>
                        <td>
                        	<input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" placeholder="Write/Browse" style="width:70px" onDblClick="openmypage_booking();" /></td>
                            <input type="hidden" id="txt_booking_id" name="txt_booking_id">
                        </td>
                        <td>
							<?
                            $order_status = array(0 => "ALL", 1 => "Confirmed", 2 => "Projected");
                            echo create_drop_down("cbo_order_status", 80, $order_status, "", 0, "", 0, "", "");
                            ?>
                        </td>
                        <td>
							<?
							$active_status=array(1=>"Active",2=>"In-Active",3=>"Cancel",4=>"All"); 
							 echo create_drop_down( "cbo_active_status", 80, $active_status, "", 1, "----Select----",1, "",0,"" ); ?>
                        </td> 
                        <td>
                            <? 
								$discrepancy_arr=array(1=>"Grey Fab.");//,2=>"Finish Fab.",3=>"Issue To Cut"
                                echo create_drop_down( "cbo_discrepancy", 90, $discrepancy_arr,"", 1, "--   --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td>
                            <input type="button" id="order_wise_button" class="formbutton" style="width:70px;display: none;" value="Order Wise" onClick="fn_report_generated(1)" />
                        </td>
                    </tr>
                    <tr class="general">
                    	<td colspan="11" align="center"><? echo load_month_buttons(1); ?></td>
                        <td>
                            <input type="checkbox" name="chk_no_boking" id="chk_no_boking">&nbsp;No Booking.
                        </td>
                        <td>
                            <input type="button" id="summary_button" class="formbutton" style="width:70px;display: none;" value="Summary" onClick="fn_report_generated(3)" />
                        </td>
                    	<td>
                            <input type="button" id="style_wise_button" class="formbutton" style="width:70px;display: none;" value="Style Wise" onClick="fn_report_generated(2)" />
                        </td>
                    </tr>
                </tbody>
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
<script>
	$('#cbo_discrepancy').val(0);
</script>
</html>
