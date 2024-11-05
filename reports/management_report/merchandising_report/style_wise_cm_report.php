<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create  Style Wise CM Report.
Functionality	:	
JS Functions	:
Created by		:	Aziz 
Creation date 	: 	09-01-2020
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------

echo load_html_head_contents("Style Wise CM Report","../../../", 1, 1, $unicode,1,1);
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
			
	function fn_report_generated(type)
	{
		
		var company_name=document.getElementById('cbo_company_name').value;
		var style_owner=document.getElementById('cbo_style_owner').value;
		var buyer_name=document.getElementById('cbo_buyer_name').value;
		var txt_style_ref=document.getElementById('txt_style_ref').value;	
		
		
			
		if(company_name==0)
		{
			if(style_owner==0)
			{
				if(form_validation('cbo_company_name*cbo_buyer_name','Company*Buyer')==false)
					{
						return;
					}
			}
		}
		if(style_owner==0)
		{
			if(company_name==0)
			{
				if(form_validation('cbo_style_owner*cbo_buyer_name','Style Owner*Buyer')==false)
				{
					return;
				}
			}
		}
		/*if(buyer_name==0)
			{
				if(form_validation('cbo_buyer_name','Buyer')==false)
					{
						return;
					}
			}*/
		
		if (type==1 || type==2 || type==4 || type==6 || type==7)
		{
			if(buyer_name!=0)
			{
				if(form_validation('cbo_company_name','Company')==false)
				{
					return;
				}
			}
			else
			{
				
				if(company_name==0)
				{
					if(style_owner==0)
					{
						if(form_validation('cbo_company_name*cbo_buyer_name','Company*Buyer')==false)
							{
								return;
							}
					}
				}
				if(style_owner==0)
				{
					if(company_name==0)
					{
						if(form_validation('cbo_style_owner*cbo_buyer_name','Style Owner*Buyer')==false)
						{
							return;
						}
					}
				}
				if(buyer_name==0)
				{
					if(form_validation('cbo_buyer_name','Buyer')==false)
						{
							return;
						}
				}
				
			}
		}
		
		if(type==1)
		{
		var data="action=report_generate&reporttype="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_style_owner*txt_style_ref*txt_style_ref_id*txt_date_from*txt_date_to',"../../../");
		}else if(type==3)
		{
		var data="action=report_generate_pre_cost&reporttype="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_style_owner*txt_style_ref*txt_style_ref_id*txt_date_from*txt_date_to',"../../../");
		}
		else if(type==4)
		{
		var data="action=report_generate_obs_report&reporttype="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_style_owner*txt_style_ref*txt_style_ref_id*txt_date_from*txt_date_to',"../../../");
		}
		else if(type==5)
		{
		var data="action=report_generate_approve&reporttype="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_style_owner*txt_style_ref*txt_style_ref_id*txt_date_from*txt_date_to',"../../../");
		}
		else if(type==6)
		{
		var data="action=report_generate_pq_vs_budged&reporttype="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_style_owner*txt_style_ref*txt_style_ref_id*txt_date_from*txt_date_to',"../../../");
		}
		else if(type==7) //Show 2
		{
		var data="action=report_generate2&reporttype="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_style_owner*txt_style_ref*txt_style_ref_id*txt_date_from*txt_date_to',"../../../");
		}
		else
		{
			var data="action=report_generate_cm&reporttype="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_style_owner*txt_style_ref*txt_style_ref_id*txt_date_from*txt_date_to',"../../../");
		}
			//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/style_wise_cm_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			//alert(reponse[0]);
			//alert(reponse[2]);
			//var tot_rows=reponse[0];
			$('#report_container2').html(reponse[0]);
			//alert(type);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../');
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Convert to Excel" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+reponse[2]+');" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
				/*var tableFilters = {
			  //col_10:'none',
			 // display_all_text: " ---Show All---",
					col_operation: {
					id: ["value_total_fab_qnty","value_total_trims_amount"],
					col: [10,12],
					operation: ["sum","sum"],
					write_method: ["innerHTML","innerHTML"]
					}
				}*/
				//setFilterGrid("table_body_accss",-1,tableFilters);
				/*
				setFilterGrid("table_body1",-1);
				setFilterGrid("table_body2",-1);
				setFilterGrid("table_body3",-1);
				setFilterGrid("table_body4",-1);
				setFilterGrid("table_body5",-1);
				setFilterGrid("table_body6",-1);
				setFilterGrid("table_body7",-1);
			*/
			
			//append_report_checkbox('table_header_1',1);
			//show_graph( '', document.getElementById('graph_data').value, "pie", "chartdiv", "", "../../../", '',580,700 );
			release_freezing();
			show_msg('3');
		}
	}
	
	function new_window(type)
	{
		//alert(1);
		//document.getElementById('scroll_body1').style.overflow="auto";
		//document.getElementById('scroll_body1').style.maxHeight="none";
		//document.getElementById('scroll_body2').style.overflow="auto";
		//document.getElementById('scroll_body2').style.maxHeight="none";
		// alert(type);
		if(type==2)
		{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		  
		 $('.scroll_div_inner').css('overflow','auto');
		 $('.scroll_div_inner').css('maxHeight','none');
		}
		 
		//$("#table_body_accss tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><style> a[href]{ font-size:18px;}.rpt_table tr th,rpt_table tr td{font-size:14px;}@media all {#report_container2 { margin-top:200px;} } </style> <title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		if(type==2)
		{
		$('.scroll_div_inner').css('overflow','scroll');
		$('.scroll_div_inner').css('maxHeight','480px');
		document.getElementById('scroll_body').style.overflowY="scroll"; 
		document.getElementById('scroll_body').style.maxHeight="480px";
		}
		 /*
		document.getElementById('scroll_body1').style.overflowY="scroll"; 
		document.getElementById('scroll_body1').style.maxHeight="480px";
		document.getElementById('scroll_body2').style.overflowY="scroll"; 
		document.getElementById('scroll_body2').style.maxHeight="480px";
		document.getElementById('scroll_body1').style.overflowY="scroll"; 
		document.getElementById('scroll_body1').style.maxHeight="480px";
	*/	
		
		//$("#table_body_accss tr:first").show();
	}
	
	
	
	function openmypage_style22()
	{
			//alert(5);
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();
	
		var cbo_year = $("#cbo_year").val();
		var txt_style_ref_no = $("#txt_style_ref_no").val();
		var txt_style_ref_id = $("#txt_style_ref_id").val();
		var txt_style_ref = $("#txt_style_ref").val();
		var page_link='requires/style_wise_cm_report_controller.php?action=style_ref_search&companyID='+company+'&buyer_name='+buyer+'&txt_style_ref_no='+txt_style_ref_no+'&txt_style_ref_id='+txt_style_ref_id+'&txt_style_ref='+txt_style_ref+'&cbo_year_id='+cbo_year;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_no);
			$("#txt_style_ref").val(style_des);
			$("#txt_style_ref_id").val(style_id); 
			$("#txt_style_ref_no").val(style_no); 
		}
	}
	
	function openmypage_order()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();	
		var txt_style_ref = $("#txt_style_ref").val();
		var style_ref_id = $("#txt_style_ref_id").val();
		var cbo_year = $("#cbo_year").val();
		var txt_order_id_no = $("#txt_order_id_no").val();
		var txt_order_id = $("#txt_order_id").val();
		var txt_order = $("#txt_order").val();
		var page_link='requires/style_wise_cm_report_controller.php?action=order_search&company='+company+'&buyer_name='+buyer+'&txt_order_id_no='+txt_order_id_no+'&txt_order_id='+txt_order_id+'&txt_order='+txt_order+'&txt_style_ref='+txt_style_ref+'&cbo_year='+cbo_year+'&style_ref_id='+style_ref_id; 
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=770px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			$("#txt_order").val(style_des);
			$("#txt_order_id").val(style_id); 
			$("#txt_order_id_no").val(style_des_no);
		}
	}

	
	function generate_trims_detail(po_id,job_no,type,action)
	{  
		var company_id=$("#cbo_company_name").val();
		var style_owner=$("#cbo_style_owner").val();
		var buyer_id=$("#cbo_buyer_name").val();
		if(type==1){
			var popup_width='1120px';
		}
		else if(type!=3)
		{
			var popup_width='1250px';
		}
		else
		{
		var popup_width='550px';
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_cm_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}	//Pre cost end
	
	function generate_image_view(link,job_no,action)
	{  
			var popup_width='1120px';		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_cm_report_controller.php?link='+link+'&job_no='+job_no+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}	//Pre cost end
	
	 
	function generate_worder_report(txt_booking_no,cbo_company_name,txt_order_no_id,cbo_fabric_natu,cbo_fabric_source,txt_job_no,id_approved_id,print_id,type,i)
	{
		//var report_title='Budget Wise Fabric Booking';
		if(print_id==45)
		{
			var report_title='Budget Wise Fabric Booking';
		}
		else if(print_id==46)
		{
			var report_title='Short Fabric Booking';
		}
		else if(print_id==67)
		{
			var report_title='Multiple Job Wise Trims Booking Urmi';
		}
		else if(print_id==61)
		{
			var report_title='Service Booking For AOP';
		}
		else if(print_id==100)
		{
			var report_title='Embellishment Work Order Urmi'; 
		}
		else if(print_id==108)
		{
			var report_title='Partial Fabric Booking ';
		}
		var data="action="+type+
		'&txt_booking_no='+"'"+txt_booking_no+"'"+
		'&cbo_company_name='+"'"+cbo_company_name+"'"+
		'&txt_order_no_id='+"'"+txt_order_no_id+"'"+
		'&cbo_fabric_natu='+"'"+cbo_fabric_natu+"'"+
		'&cbo_fabric_source='+"'"+cbo_fabric_source+"'"+
		'&id_approved_id='+"'"+id_approved_id+"'"+
		'&report_title='+""+report_title+""+
		'&txt_job_no='+"'"+txt_job_no+"'"+
		
		'&path=../';
		


		freeze_window(5);
		//http.open("POST","requires/fabric_booking_controller.php",true);
		if(print_id==45 || print_id==53)
		{
			//alert(print_id);
			http.open("POST","../../../order/woven_order/requires/fabric_booking_urmi_controller.php",true);
		}
		if(print_id==46)
		{
			//alert(print_id);
			http.open("POST","../../../order/woven_order/requires/short_fabric_booking_controller.php",true);
		}
		if(print_id==67)
		{
			//alert(print_id);
			http.open("POST","../../../order/woven_order/requires/trims_booking_multi_job_controllerurmi.php",true);
		}
		if(print_id==61) //AOP Urmi
		{
			//alert(print_id);
			http.open("POST","../../../order/woven_order/requires/service_booking_aop_urmi_controller.php",true);
		}
		if(print_id==100) //Embl Urmi
		{
			//alert(print_id);
			http.open("POST","../../../order/woven_order/requires/print_booking_urmi_controller.php",true);
		}
		
		if(print_id==101) //LabTest Urmi
		{
			//alert(print_id);
			http.open("POST","../../../order/woven_order/requires/labtest_work_order_controller.php",true);
		}
		if(print_id==108) //Partial Urmi
		{
			//alert(print_id);
			http.open("POST","../../../order/woven_order/requires/partial_fabric_booking_controller.php",true);
		}
						
					
		/*if(type==1)	
		{			
			http.open("POST","../order/woven_order/requires/short_fabric_booking_controller.php",true);
		}
		else if(action=='show_fabric_booking_report_gr')
		{
			http.open("POST","../order/woven_order/requires/fabric_booking_controller.php",true);
		}
		else
		{
			http.open("POST","../order/woven_order/requires/sample_booking_controller.php",true);
		}*/
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			if(http.readyState == 4) 
		    {
				var w = window.open("Surprise", "_blank");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
				d.close();
				release_freezing();
		   }
			
		}
	}
	function generate_worder_report3(cbo_company_name,update_id,print_id,type,i)
	{
		var report_title='Lab Test Work Order';
		//var data="action="+type+
		//'&cbo_company_name='+"'"+cbo_company_name+"'"+
		//'&update_id='+"'"+update_id+"'"+
		
		//'&path=../';
		//var data=cbo_company_name+'*'+update_id+'*'+print_id+'*'+0+'*'+0+'*'+type

		print_report(cbo_company_name+'*'+update_id+'*'+0+'*'+0+'*'+report_title, "show_trim_booking_report","../../../order/woven_order/requires/labtest_work_order_controller" ) ;
	}

	function fnc_print_report(data,action){
	
		print_report(data, action,"../../../order/spot_costing/requires/short_quotation_v4_controller" ) ;
	}




	function generate_report(job_no,comapny,buyer,style_ref,costing_date,po_id,cost_per,type)
	{
	
		

		

			if(type==0){

				var date_type="1";
				var data="action=report_generate"+
				'&reportType='+"'"+type+"'"+
				'&cbo_company_name='+"'"+comapny+"'"+
				'&cbo_buyer='+"'"+buyer+"'"+
				'&txt_style='+"'"+style_ref+"'"+	
				'&cbo_costing_per='+"'"+cost_per+"'"+
				'&cbo_date_type='+"'"+date_type+"'"+
				'&txt_job='+"'"+job_no+"'"+					
				'&path=../';

				freeze_window(5);
				http.open("POST","../../../reports/management_report/sweater_report/requires/order_status_report_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = function(){

					if(http.readyState == 4) 
						{
							var w = window.open("Surprise", "_blank");
							var d = w.document.open();
								d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
							'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
							d.close();
							release_freezing();
						}



				}

			}else{

				var zero_val="1"; var rate_amt=2;			
				var report_title='Pre- Costing';
				var data="action="+type+
				'&txt_job_no='+"'"+job_no+"'"+
				'&cbo_company_name='+"'"+comapny+"'"+
				'&cbo_buyer_name='+"'"+buyer+"'"+
				'&txt_style_ref='+"'"+style_ref+"'"+
				'&txt_costing_date='+"'"+costing_date+"'"+
				'&txt_po_breack_down_id='+"'"+po_id+"'"+
				'&cbo_costing_per='+"'"+cost_per+"'"+
				'&zero_value='+"'"+zero_val+"'"+
				'&rate_amt='+"'"+rate_amt+"'"+
				'&report_title='+""+report_title+""+		
				'&path=../';

					freeze_window(5);
					http.open("POST","../../../order/sweater/requires/pre_cost_entry_controller_v2.php",true);
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = function()
					{
						if(http.readyState == 4) 
						{
							var w = window.open("Surprise", "_blank");
							var d = w.document.open();
								d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
							'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
							d.close();
							release_freezing();
						}
				
					}
			}

	
		
	}




   function fn_date_chack(str)
	{
		if(str==1)
		{
			var ship_date=$('#txt_date_from').val();
			if(ship_date!="")
			{
				$('#txt_ex_date_form').val("");
				$('#txt_ex_date_to').val("");
			}
		}
		else
		{
			var ex_fact_date=$('#txt_ex_date_form').val();
			if(ex_fact_date!="")
			{
				$('#txt_date_from').val("");
				$('#txt_date_to').val("");
			}
		}
	}
	function openmypage_style()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();
		var cbo_year = $("#cbo_year_selection").val();
		var txt_style_ref_no = $("#txt_style_ref_no").val();
		var txt_style_ref_id = $("#txt_style_ref_id").val();
		var txt_style_ref = $("#txt_style_ref").val();
		var page_link='requires/style_wise_cm_report_controller.php?action=style_ref_search&companyID='+company+'&buyer_name='+buyer+'&txt_style_ref_no='+txt_style_ref_no+'&txt_style_ref_id='+txt_style_ref_id+'&txt_style_ref='+txt_style_ref+'&cbo_year_id='+cbo_year;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=580px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_no);
			$("#txt_style_ref").val(style_des);
			$("#txt_style_ref_id").val(style_id); 
			$("#txt_style_ref_no").val(style_no); 
		}
	}
    function print_button_setting()
    {
        get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/style_wise_cm_report_controller' );
    }
</script>
	
    
<style>
 /* The Modal (background) */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

 /* Modal Header */
.modal-header {
    padding: 2px 16px;
    background-color: #999;
    color: white;
}

/* Modal Body */
.modal-body {padding: 2px 16px;}

/* Modal Footer */
.modal-footer {
    padding: 2px 16px;
    background-color: #999;
    color: white;
}

/* Modal Content */
.modal-content {
    position: relative;
    background-color: #fefefe;
    margin: auto;
    padding: 0;
    border: 1px solid #888;
    width: 80%;
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
    -webkit-animation-name: animatetop;
    -webkit-animation-duration: 0.4s;
    animation-name: animatetop;
    animation-duration: 0.4s
}

/* Add Animation */
@-webkit-keyframes animatetop {
    from {top: 300px; opacity: 0}
    to {top: 0; opacity: 1}
}

@keyframes animatetop {
    from {top: 300px; opacity: 0}
    to {top: 0; opacity: 1}
}

/* The Close Button */
.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}
</style> 
</head>
<body onLoad="set_hotkey();">
<form id="costSheetReport_1">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../../",$permission);  ?>
          <h3 align="left" id="accordion_h1" style="width:910px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
         <fieldset style="width:910px;" id="content_search_panel">
            <table class="rpt_table" width="910" cellpadding="1" cellspacing="2" align="center" border="1" rules="all">
                <thead>                    
                    <th class="must_entry_caption" width="145">Company</th>
                    <th width="125">Working Comppany</th>
                    <th class="must_entry_caption" width="125">Buyer</th>
                     <th class="" width="110">Style Ref</th>
                    <th width="190">Pub. Ship Date</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                    </thead>
                <tbody>
                    <tr class="general">
                        <td> 
							<?
							
                           		echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/style_wise_cm_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );set_multiselect('cbo_buyer_name','0','0','','0','0');print_button_setting();" );
                            ?>
                        </td>
                         <td>
							<? 
                            echo create_drop_down( "cbo_style_owner", 120, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Style Owner--", $selected, ""); ?>
                           
                        </td>
                        
                        <td id="buyer_td">
							<? 
                            	echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "--All Buyer--", $selected, "",0,"" );
                            ?>
                        </td>
                        <td>
                             <input style="width:100px;" name="txt_style_ref" id="txt_style_ref" onDblClick="openmypage_style()" class="text_boxes" placeholder="Browse or Write"/>
                            <input type="hidden" name="txt_style_ref_id" id="txt_style_ref_id"/>   
                             <input type="hidden" name="txt_style_ref_no" id="txt_style_ref_no"/>  
                        </td>
                        
                        <td>
						<input name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:80px" placeholder="From Date" value="01-01-<?= date('Y');?>">
						<input name="txt_date_to" id="txt_date_to"  class="datepicker" style="width:80px" placeholder="To Date"  value="31-12-<?= date('Y');?>">
                        </td>
                         <td rowspan="2" id="load_print_button">

                        </td>
                        
                    </tr>
                   <tr align="center"  class="general">
                        <td colspan="5">
                        	<? echo load_month_buttons(1); ?>
							<input type="button" id="show_button" class="formbutton" style="width:125px; margin-top: 2px; margin-right: 2px;" value="S.Quot VS P.Costing" onClick="fn_report_generated(6)" />
                        </td>
						
                    </tr>
                </table> 
            </fieldset>
        </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="center"></div>
  
 </form>    
   <input type="button" id="myBtn" value="OPen" style="display:none"/>
    <div id="myModal" class="modal">
  <div class="modal-content">
  <div class="modal-header">
    <span class="close">×</span>
    <h2>Po Number</h2>
  </div>
  <div class="modal-body">
    <p id="ccc">Some text in the Modal Body</p>
   
  </div>
  <div class="modal-footer">
    <h3></h3>
  </div>
</div>

</div>
<script>set_multiselect('cbo_buyer_name','0','0','','0','0');</script>
<script>
//============modal=========
var modal = document.getElementById('myModal');

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on the button, open the modal
btn.onclick = function() {
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

function setdata_po(data){
	
	document.getElementById('ccc').innerHTML=data;
	document.getElementById('myBtn').click();
}
</script>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>