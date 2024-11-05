<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Work Order [Booking] Report.
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	29-12-2013
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
$user_id=$_SESSION['logic_erp']['user_id'];
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Work Order [Booking] Report", "../../", 1, 1,$unicode,1,1);
?>	
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';	

	var tableFilters = 
	{
		// col_23: "none",
		// col_24: "none",
		// col_6: "select",
		col_operation: {
		id: ["value_tot_fin_fab_qnty","value_tot_req_qnty","usd_tot_fin_fab_qnty"],
		col: [20,21,22],
		operation: ["sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML"]
		}
	} 
	
	var tableFilters1 = 
	{
		// col_22: "none",
		// col_7: "select",
		col_operation: {
		id: ["value_tot_fin_fab_qnty","value_tot_grey_fab_qnty"],
		col: [22,23],
		operation: ["sum","sum"],
		write_method: ["innerHTML","innerHTML"]
		}
	}
	var tableFilterscat3 = 
	{
		// col_22: "none",
		// col_7: "select",
		col_operation: {
		id: ["value_grnad_total_wo_qty","value_grnad_total_wo_val","total_fin_rcv_qty","total_fin_rcv_val","total_fin_issue_qty","total_fin_issue_val","total_fin_bal_qty","total_fin_bal_val"],
		col: [28,29,30,31,32,33,34,35],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	var tableFilters24 = 
	{
		// col_23: "none",
		// col_7: "select",
		col_operation: {
		id: ["tot_fin_fab_qnty","tot_wo_amount"],
		col: [22,23],
		operation: ["sum","sum"],
		write_method: ["innerHTML","innerHTML"]
		}
	}

	
	var tableFilters25 = 
	{
		// col_22: "none",
		// col_7: "select",
		col_operation: {
		id: ["value_grnad_total_wo_qty","value_grnad_total_wo_val"],
		col: [24,25],
		operation: ["sum","sum"],
		write_method: ["innerHTML","innerHTML"]
		}
	}
	var tableFilters2 = 
	{
		// col_21: "none",
		// col_6: "select",
		col_operation: {
		id: ["value_tot_fin_fab_qnty","value_tot_grey_fab_qnty"],
		col: [22,23],
		operation: ["sum","sum"],
		write_method: ["innerHTML","innerHTML"]
		}
	} 

	function openmypage_job()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		else
		{	
			var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_id").val()+"_"+$("#cbo_job_year_id").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/wo_or_fabric_booking_report_controller.php?data='+data+'&action=job_no_popup', 'Job No Search', 'width=700px,height=420px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemailid=this.contentDoc.getElementById("txt_job_id");
				var response=theemailid.value.split('_');
				if ( theemailid.value!="" )
				{
					//alert (response[0]);hidd_job_prefix_num
					freeze_window(5);
					$("#hidd_job_id").val(response[0]);
					$("#txt_job_no").val(response[1]);
					release_freezing();
				}
			}
		}
	}
	
	function openmypage_wo()
	{
		if(form_validation('cbo_company_id*cbo_category_id','Company Name*Item Category')==false)
		{
			return;
		}
		else
		{	
			var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_id").val()+"_"+$("#cbo_year_id").val()+"_"+$("#cbo_category_id").val()+"_"+$("#cbo_wo_type").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/wo_or_fabric_booking_report_controller.php?data='+data+'&action=wo_no_popup', 'Wo No Search', 'width=650px,height=420px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemailid=this.contentDoc.getElementById("txt_wo_id");
				var theemailval=this.contentDoc.getElementById("txt_wo_no");
				//var response=theemailid.value.split('_');
				if ( theemailval.value!="" )
				{
					//alert (response[0]);
					freeze_window(5);
					$("#hidd_wo_id").val(theemailid.value);
					$("#txt_wo_no").val(theemailval.value);
					release_freezing();
				}
			}
		}
	}
		
	function openmypage_po()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		else
		{	
			var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_id").val()+"_"+$("#txt_po_no").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/wo_or_fabric_booking_report_controller.php?data='+data+'&action=po_no_popup', 'PO No Search', 'width=700px,height=420px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemailid=this.contentDoc.getElementById("txt_po_id");
				var theemailval=this.contentDoc.getElementById("txt_po_val");
				if (theemailid.value!="" || theemailval.value!="")
				{
					//alert (theemailid.value);
					freeze_window(5);
					$("#hidd_po_id").val(theemailid.value);
					$("#txt_po_no").val(theemailval.value);
					release_freezing();
				}
			}
		}
	}
	
	function fn_report_generated(operation)
	{	
		var  txt_date_from= $("#txt_date_from").val();
		var  txt_date_to= $("#txt_date_to").val();
		var  txt_wo_no= $("#txt_wo_no").val();
		var  txt_job_no= $("#txt_job_no").val();
		var  txt_po_no= $("#txt_po_no").val();
		var  txt_internal_ref= $("#txt_internal_ref").val();
		var  txt_file_no= $("#txt_file_no").val();

		if(txt_date_to!='' &&  txt_date_from!="")
		{
			if(form_validation('cbo_category_id','Item Category')==false)
			{
				return;
			}
		}
		else
		{
			if(txt_wo_no!="" || txt_job_no!="" || txt_po_no!="" || txt_internal_ref!="" || txt_file_no!="")
			{
				if(form_validation('cbo_company_id','Company')==false)
				{
					return;
				}
			}
			else
			{
				if(form_validation('cbo_company_id*txt_date_from*txt_date_to','Company*From date Fill*To date Fill')==false)
				{
					return;
				}
			}
			/*if(form_validation('cbo_company_id*cbo_category_id','Company Name*Item Category')==false)
			{
				return;
			}*/
		}
	
		var report_title=$( "div.form_caption" ).html();
		if(operation==0)
		{
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_category_id*cbo_wo_type*cbo_year_id*txt_wo_no*hidd_wo_id*cbo_job_year_id*txt_job_no*hidd_job_id*txt_po_no*hidd_po_id*txt_date_from*txt_date_to*txt_internal_ref*txt_file_no*txt_date_category*cbo_fabric_source*cbo_order_status*cbo_season_id',"../../")+'&report_title='+report_title;	
		}
		if(operation==1)
		{
			var data="action=report_generate2"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_category_id*cbo_wo_type*cbo_year_id*txt_wo_no*hidd_wo_id*cbo_job_year_id*txt_job_no*hidd_job_id*txt_po_no*hidd_po_id*txt_date_from*txt_date_to*txt_internal_ref*txt_file_no*txt_date_category*cbo_fabric_source*cbo_order_status*cbo_season_id',"../../")+'&report_title='+report_title;	
		}
		if(operation==2)
		{
			var data="action=report_generate3"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_category_id*cbo_wo_type*cbo_year_id*txt_wo_no*hidd_wo_id*cbo_job_year_id*txt_job_no*hidd_job_id*txt_po_no*hidd_po_id*txt_date_from*txt_date_to*txt_internal_ref*txt_file_no*txt_date_category*cbo_fabric_source*cbo_order_status*cbo_season_id',"../../")+'&report_title='+report_title;	
		}
		if(operation==3)
		{
			var data="action=report_generate4"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_category_id*cbo_wo_type*cbo_year_id*txt_wo_no*hidd_wo_id*cbo_job_year_id*txt_job_no*hidd_job_id*txt_po_no*hidd_po_id*txt_date_from*txt_date_to*txt_internal_ref*txt_file_no*txt_date_category*cbo_fabric_source*cbo_order_status*cbo_season_id',"../../")+'&report_title='+report_title;	
		}	
		if(operation==4)
		{
			var data="action=report_generate5"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_category_id*cbo_wo_type*cbo_year_id*txt_wo_no*hidd_wo_id*cbo_job_year_id*txt_job_no*hidd_job_id*txt_po_no*hidd_po_id*txt_date_from*txt_date_to*txt_internal_ref*txt_file_no*txt_date_category*cbo_fabric_source*cbo_order_status*cbo_season_id',"../../")+'&report_title='+report_title;	
		}
		freeze_window(3);
		http.open("POST","requires/wo_or_fabric_booking_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("**");
			//var tot_rows=reponse[2];
			$('#report_container4').html(reponse[0]);
			//document.getElementById('report_container3').innerHTML=report_convert_button('../../');
			document.getElementById('report_container3').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
	 		show_msg('3');
			
			var cat_id=$("#cbo_category_id").val();
			if(cat_id==2   )
			{

				setFilterGrid("table_body",-1,tableFilters1);
			}
			else if(cat_id==3)
			{
				
				setFilterGrid("table_body",-1,tableFilterscat3);
			}
			else if(cat_id==24)
			{
				setFilterGrid("table_body",-1,tableFilters24);
			}
			else if(cat_id==25)
			{
				setFilterGrid("table_body",-1,tableFilters25);
			}
			else if(cat_id==12)
			{
				setFilterGrid("table_body",-1,tableFilters2);
			}
			else if(cat_id==4)
			{
				setFilterGrid("table_body",-1,tableFilters);
			}
			release_freezing();
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		$("#table_body tr:first").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container4').innerHTML+'</body</html>');
		d.close(); 
		$("#table_body tr:first").show();
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="350px";
	}
	
	function openmypage_job_color_size(page_link,title)
	{
		//alert("monzu");
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
		}
	}
	
	function generate_fabric_report(type,booking_no,company_id,order_id,fabric_nature,fabric_source,job_no,approved,category,is_short,emb_name,item_number_id,action_type,i,supplier_id,cbo_level,revised_no)
	{
		var style_id=0;
		if(type==140 || type==88) var style_id=supplier_id;
		//alert(category+"=="+type);//return;
		//console.log(category+"=="+type);
		var report_title ='';
		if(category==2 || category==3)//Knit Finish Fabrics/Woven
		{
			//alert(category+"=="+type);return;
			if(type==4)//sample booking without order
			{
				report_title ='Sample Booking Without Order';
				var data="action=show_fabric_booking_report"+
							'&txt_booking_no='+"'"+booking_no+"'"+
							'&cbo_company_name='+"'"+company_id+"'"+
							'&report_title='+""+report_title+""+
							'&id_approved_id='+"'"+approved+"'";	
							
				freeze_window(5);
				http.open("POST","../woven_order/requires/sample_booking_non_order_controller.php",true);			
			}
			else if(type==108) // Partial Fabric Booking
			{
				var show_yarn_rate = '';
				if(type=='print_booking_19')
				{
					var r=confirm("Press  \"OK\"  to Show Style Wise \nPress  \" Cancel\"  to Show PO Wise");
					if (r==true) show_yarn_rate="1"; else show_yarn_rate="0";
				}
		
			if(type!="print_booking_12" && type!=="print_booking_18" && type!=="print_booking_19")
			{
				
				if(type!='print_booking_5' && type!='print_booking_10' && type!='print_booking_11')
				{
					var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
					if (r==true) show_yarn_rate="1"; else show_yarn_rate="0";
				}
				if(type=='print_booking_10')
				{
					var r=confirm("Do You Want to Hide Buyer and Style Name?");
					if (r==true) show_yarn_rate="1"; else show_yarn_rate="0";
				}
			}
				// var r = confirm("Press  \"Cancel\"  to hide  Rate\nPress  \"OK\"  to Show Rate");
				// if (r == true) {
				// 	show_yarn_rate = 1;
				// }
				// else {
				// 	show_yarn_rate = 0;
				// }
				var report_title="Partial Fabric Booking";
				var data="action="+action_type+
				'&txt_booking_no='+"'"+booking_no+"'"+
				'&cbo_company_name='+"'"+company_id+"'"+
				'&txt_order_no_id='+"'"+order_id+"'"+
				'&cbo_fabric_natu='+"'"+fabric_nature+"'"+
				'&cbo_fabric_source='+"'"+fabric_source+"'"+
				'&id_approved_id='+"'"+approved+"'"+
				'&report_title='+""+report_title+""+
				'&show_yarn_rate='+"'"+show_yarn_rate+"'"+
				'&txt_job_no='+"'"+job_no+"'";				
				'&path=../../';
				//alert(type);
				freeze_window(5);
				http.open("POST","../woven_order/requires/partial_fabric_booking_controller.php",true);
			}
			else
			{ 
				if(type==140 || type==139)
				{
					var show_comment = '';
					var r = confirm("Press  \"Cancel\"  to hide  Rate\nPress  \"OK\"  to Show Rate");
					if (r == true) {
						show_comment = "1";
					}
					else {
						show_comment = "0";
					}
				}
				if(type==426)
				{
					var show_yarn_rate = '';
					var r=confirm("Press  \"OK\"  to Show Yarn Rate \nPress  \" Cancel\"  to Hide Yarn Rate");
					if (r==true) show_yarn_rate="1"; else show_yarn_rate="0";
		
				}
				if(type==139 || type==3 || type==4)
				{
					var report_title = "Sample Fabric Booking -With order ";
				}
				else if(type==1)
				{
					report_title = "Short Fabric Booking";
				}
				else if(type==2) //main fabric booking
				{
					report_title = "Main Fabric Booking";
				}
				else if((type==73) || (type==93 || type==269 || type==28 || type==45 || type==53 || type==93  || type==193 || type==719 || type==723 || type==383 || type==304 || type==426  || type==849  || type==13 || type==432)) //
				{
					report_title = "Main Fabric Booking V2";
				}
				else if(type==271) //woven Partial main fabric booking
				{
					
					report_title = "Woven Partial Main Fabric Booking";
				}
				else if(type==140) //Sample fabric booking Req without order
				{
					//alert(type);
					report_title = "Sample Fabric Booking Req Without Order";
				}
				else if(type==139) //Sample fabric booking Req with order
				{
					//alert(type);
					report_title = "Sample Fabric Booking Req With Order";
				}
				else if(type==90) //Sample fabric booking withOut order
				{
					//alert(type);
					report_title = "Sample Fabric Booking WithOut Order";
				}
				else if(type==89) //Sample fabric booking with order
				{
					//alert(type);
					report_title = "Sample Fabric Booking With Order";
				}
				else if(type==88) //Short fabric booking 
				{
					//alert(type);
					report_title = "Short Fabric Booking ";
				}
				else  
				{
					report_title ='Sample Booking';
				}
				
				var data="action="+action_type+
				'&txt_booking_no='+"'"+booking_no+"'"+
				'&cbo_company_name='+"'"+company_id+"'"+
				'&txt_order_no_id='+"'"+order_id+"'"+
				'&cbo_fabric_natu='+"'"+fabric_nature+"'"+
				'&cbo_fabric_source='+"'"+fabric_source+"'"+
				'&id_approved_id='+"'"+approved+"'"+
				'&report_title='+report_title+
				'&show_comment='+show_comment+
				'&txt_job_no='+"'"+job_no+"'"+
				'&show_yarn_rate='+"'"+show_yarn_rate+"'"+
				'&revised_no='+"'"+revised_no+"'";
				'&path=../../';
				// alert(revised_no);
				freeze_window(5);
				if(type==1)	//short fabric booking
				{			
					http.open("POST","../woven_order/requires/short_fabric_booking_controller.php",true);
				}
				else if(type==2) //main fabric booking
				{
					http.open("POST","../woven_order/requires/fabric_booking_controller.php",true);
				}
				else if((type==73) || (type==93 || type==269 || type==28 || type==45 || type==53 || type==93  || type==193 || type==719 || type==723 || type==383  || type==304 || type==426  || type==849  || type==13 || type==432)) //main fabric booking v2
				{
					http.open("POST","../woven_order/requires/fabric_booking_urmi_controller.php",true);
				}
				else if(type==271 && i=='wvn_p') //woven Partial main fabric booking 
				{
					http.open("POST","../woven_gmts/requires/partial_fabric_booking_controller.php",true);
				}
				else if(type==271 && i!='wvn_p') //woven Partial main fabric booking
				{
					http.open("POST","../woven_gmts/requires/woven_partial_fabric_booking_controller.php",true);
				}
				/*else if(type==108) //Partial main fabric booking
				{
					//alert(type);
					http.open("POST","../woven_order/requires/partial_fabric_booking_controller.php",true);
				}*/
				else if(type==140) //Sample fabric booking Req without order
				{
					//alert(type);
					http.open("POST","../woven_order/requires/sample_requisition_booking_non_order_controller.php",true);
				}
				else if(type==139) //Sample fabric booking Req with order
				{
					//alert(type);
					http.open("POST","../woven_order/requires/sample_requisition_booking_with_order_controller.php",true);
				}
				else if(type==90) //Sample fabric booking withOut order
				{
					//alert(type);
					http.open("POST","../woven_order/requires/sample_booking_non_order_controller.php",true);
				}
				/* else if(type==89 || type==3 || type==4) //Sample fabric booking with order
				{
					//alert(type);
					http.open("POST","../woven_order/requires/sample_booking_controller.php",true);
				} */
				else if(type==89) //Sample fabric booking with order
				{
					//alert(type);
					http.open("POST","../woven_order/requires/sample_booking_controller.php",true);
				}
				else if(type==88) //Short fabric booking 
				{
					//alert(type);
					http.open("POST","../woven_order/requires/short_fabric_booking_controller.php",true);
				}else if(type=="719_last_version_details") //last version check
				{
					//alert(type);
					http.open("POST","requires/wo_or_fabric_booking_report_controller.php",true);
				}
				else  
				{
					http.open("POST","../woven_order/requires/sample_booking_controller.php",true);
				}
			}
		}
		else if(category==4) //Accessories
		{
			if(type==87 || type==272 || type==43 || type==262 || type==719  || type==723  || type==178 || type==304 || type==260 || type==404 || type==419 || type==774 || type==183  || type==177) // trims_booking_multi_job_controllerurmi; entry_form=87
			{
				var show_comment='';
				var r=confirm("Press  \"Cancel\"  to hide  Remarks\nPress  \"OK\"  to Show Remarks");
				if (r==true){
					show_comment="1";
				}
				else{
					show_comment="0";
				}
				
				if(type==178){
					var report_title='Short Trims Booking [Multiple Order]';
				}
				else if(type==260){
					var report_title='Country and Order Wise Trims Booking V2';
				}
				else{
					var report_title='Multiple Job Wise Trims Booking V2';
				}
				
				var data="action="+action_type+
					'&txt_booking_no='+"'"+booking_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&id_approved_id='+"'"+approved+"'"+
					'&cbo_isshort='+"'"+is_short+"'"+
					'&cbo_level='+"'"+cbo_level+"'"+
					'&show_comment='+"'"+show_comment+"'"+
					'&report_title='+"'"+report_title+"'"+
					'&report_type=1&path=../../';
					
					freeze_window(5);
							
				if(type==87)
				{
					http.open("POST","../woven_order/requires/trims_booking_multi_job_controllerurmi.php",true);	
				}
				else if(type==43)
				{
					http.open("POST","../woven_order/requires/trims_booking_controller2.php",true);	
				}
				else if(type==262)
				{
					http.open("POST","../woven_order/requires/short_trims_booking_multi_job_controllerurmi.php",true);	
				}
				else if(type==178)
				{
					http.open("POST","../woven_order/requires/short_trims_booking_controller.php",true);	
				}
				else if(type==719 || type==723  || type==304)
				{
					http.open("POST","../woven_order/requires/fabric_booking_urmi_controller.php",true);
				}
				else if(type==260) //Country wise trim Booking V2
				{
					http.open("POST","../woven_order/requires/trims_booking_urmi_controller.php",true);	
				}
				else if(type==774) //Country wise trim Booking V2
				{
					http.open("POST","../woven_gmts/requires/trims_booking_multi_job_controllerurmi.php",true);	
				}
				else
				{
					http.open("POST","../woven_gmts/requires/trims_booking_multi_job_controllerurmi.php",true);
				}			
			}
			else if(type==4) // trims sample booking without order
			{
				var data="action=show_fabric_booking_report"+
							'&txt_booking_no='+"'"+booking_no+"'"+
							'&cbo_company_name='+"'"+company_id+"'"+
							'&id_approved_id='+"'"+approved+"'";
							
				freeze_window(5);	
							
				http.open("POST","../woven_order/requires/trims_sample_booking_without_order_controller.php",true);			
			}
			else
			{
				var data="action="+action_type+
					'&txt_booking_no='+"'"+booking_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&id_approved_id='+"'"+approved+"'"+
					'&cbo_isshort='+"'"+is_short+"'"+
					'&cbo_level='+"'"+cbo_level+"'"+
					'&show_comment='+"'"+show_comment+"'"+
					'&report_title='+"'"+report_title+"'"+
					'&report_type=1&path=../../';
				var show_comment='';
				var r=confirm("Press  \"Cancel\"  to hide  comments\nPress  \"OK\"  to Show comments");
				if (r==true)
				{
					show_comment="1";
				}
				else
				{
					show_comment="0";
				}
					var data="action="+action_type+
							'&txt_booking_no='+"'"+booking_no+"'"+
							'&cbo_company_name='+"'"+company_id+"'"+
							'&id_approved_id='+"'"+approved+"'"+
							'&cbo_isshort='+"'"+is_short+"'"+
							'&show_comment='+"'"+show_comment+"'"
							'&path=../../';
							//alert(data)
					freeze_window(5);
				if(type==1)	//short trim booking
				{
					//alert(type);			
					http.open("POST","../woven_order/requires/short_trims_booking_controller.php",true);
				}
				else if(type==2) // main trim booking
				{ 
					http.open("POST","../woven_order/requires/trims_booking_controller_v2.php",true);
				}
				else // sample trim booking
				{
					http.open("POST","../woven_gmts/requires/trims_booking_multi_job_controllerurmi.php",true);
				}
			}
		}
		else if(category==12) //Services - Fabric
		{
			if(action='show_trim_booking_report2')
			{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to Show  Rate and Amount\nPress  \"OK\"  to Hide Rate and Amount");
			if (r==true)
			{
				show_rate="1";
			}
			else
			{
				show_rate="0";
			}
			}
				
			var data="action="+action_type+
					'&txt_booking_no='+"'"+booking_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&txt_job_no='+"'"+job_no+"'"+
					'&id_approved_id='+"'"+approved+"'"+
					'&show_rate='+"'"+show_rate+"'"+
					'&report_title='+"'"+report_title+"'"+
					'&cbo_isshort='+"'"+is_short+"'";
					freeze_window(5);
				if(type==2) // service booking main
				{
					http.open("POST","../woven_order/requires/service_booking_controller.php",true);
				}
				else if(type==719 || type==304)
				{
					http.open("POST","../woven_order/requires/fabric_booking_urmi_controller.php",true);
				}
				else if(type==228) //Multi job Wise Knitting 
				{
					http.open("POST","../woven_order/requires/service_booking_multi_job_wise_knitting_controller.php",true);
				}
				else if(type==182) //service booking
				{
					http.open("POST","../woven_order/requires/service_booking_knitting_controller.php",true);
				}
			
				else if(type==534) //service booking v2
				{
					http.open("POST","../woven_order/requires/service_booking_knitting_controller_v2.php",true);
				}
				else if(type==8 || type==163 || type==164 || type==209 || type==177 || type==129  || type==161 || type==191 || type==220 || type==93) //service booking v2
				{
					http.open("POST","../woven_order/requires/service_booking_multi_job_wise_dyeing_controller.php",true);
				}
				else if(type==232)
				{
					
					var report_title='Service Booking For Dying ';
				
					var data="action="+action_type+
					'&txt_booking_no='+"'"+booking_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&txt_job_no='+"'"+job_no+"'"+
					'&id_approved_id='+"'"+approved+"'"+
					'&report_title='+"'"+report_title+"'";
					freeze_window(5);
					http.open("POST","../woven_order/requires/service_booking_dyeing_controller.php",true);
				}
				else if(type==163 || type==164 || type==16|| type==177|| type==288|| type==176) // service booking main
				{
					http.open("POST","../woven_order/requires/service_booking_aop_urmi_controller.php",true);
				}
		}
		else if(category==24) //Services - Yarn Dyeing
		{
			var data="action=show_trim_booking_report"+
						'&txt_booking_no='+"'"+booking_no+"'"+
						'&cbo_company_name='+"'"+company_id+"'"+
						'&id_approved_id='+"'"+approved+"'"+
						'&cbo_isshort='+"'"+is_short+"'";
						freeze_window(5);
			if(type==2) // yarn dyeing charge booking
			{
				http.open("POST","../woven_order/requires/yarn_dyeing_charge_booking_controller.php",true);
			}
			else if(type==719)
			{
				http.open("POST","../woven_order/requires/fabric_booking_urmi_controller.php",true);
			}else if(type==41)//Yarn Dying With Order
			{
				var data="action="+action_type+
						'&txt_booking_no='+"'"+booking_no+"'"+
						'&cbo_company_name='+"'"+company_id+"'"+
						'&show_comment=1'+						
						'&update_id='+"'"+order_id+"'";
				freeze_window(5);
				http.open("POST","../woven_order/requires/yarn_dyeing_charge_booking_controller2.php",true);
			}else if(type==42)//Yarn Dying Without Order
			{
				var data="action="+action_type+
						'&txt_booking_no='+"'"+booking_no+"'"+
						'&cbo_company_name='+"'"+company_id+"'"+
						'&show_comment=1'+						
						'&update_id='+"'"+order_id+"'";
				freeze_window(5);
				http.open("POST","../woven_order/requires/yarn_dyeing_wo_without_order_controller.php",true);
			}else if(type==125)//Yarn Dying Work Order Without Lot
			{
				var data="action="+action_type+
						'&txt_booking_no='+"'"+booking_no+"'"+
						'&cbo_company_name='+"'"+company_id+"'"+
						'&show_comment=1'+						
						'&update_id='+"'"+order_id+"'";
				freeze_window(5);
				http.open("POST","../woven_order/requires/yarn_dyeing_booking_without_lot_controller.php",true);
			}
			 else if(type==94)//Yarn Service Work Order
			 {
				if(action='sales_order_report')
				{
					var show_rate_column = "";
					var r=confirm("Press \"OK\" to open with Rate column\nPress \"Cancel\" to open without Rate column");
					if (r==true)
					{
						show_rate_column="1";
					}
					else
					{
						show_rate_column="0";
					}
				}
			 	var data="action="+action_type+
			 			'&txt_booking_no='+"'"+booking_no+"'"+
			 			'&cbo_company_name='+"'"+company_id+"'"+
						 '&show_val_column='+"'"+show_rate_column+"'"+	
			 			'&update_id='+"'"+order_id+"'";
			 	freeze_window(5);
				http.open("POST","../woven_order/requires/yarn_service_work_order_controller.php",true);
			 }
			// else if(type==135)//Yarn Dyeing Work Order Sales
			// {
			// 	var data="action="+action_type+
			// 			'&txt_booking_no='+"'"+booking_no+"'"+
			// 			'&cbo_company_name='+"'"+company_id+"'"+
			// 			'&show_comment=1'+						
			// 			'&update_id='+"'"+order_id+"'";
			// 	freeze_window(5);
			// 	http.open("POST","../woven_order/requires/yarn_dyeing_charge_booking_sales_controller.php",true);
			// }
		}
		else if(category==25) //Services - Embellishment
		{
			//var supplier_name=0;
			var show_comment='';
			var r=confirm("Press  \"Cancel\"  to hide  comments\nPress  \"OK\"  to Show comments");
			if (r==true)
			{
				show_comment="1";
			}
			else
			{
				show_comment="0";
			}
			//alert(action_type);
			
			if(type==201) // Multiple Job Wise Embellishment Work Order
			{	           var data="action="+action_type+
							'&txt_booking_no='+"'"+booking_no+"'"+
							'&txt_job_no='+"'"+job_no+"'"+
							'&cbo_company_name='+"'"+company_id+"'"+
							'&txt_order_no_id='+"'"+order_id+"'"+
							'&cbo_supplier_name='+"'"+supplier_id+"'"+
							'&cbo_booking_natu='+"'"+emb_name+"'"+
							'&cbo_gmt_item='+"'"+item_number_id+"'"+
							'&show_comment='+"'"+show_comment+"'"+
							'&cbo_template_id=1'+
							'&report_title= Embellishment Work Order '+
							'&id_approved_id='+"'"+approved+"'"
							'&path=../../';
				freeze_window(5);
				http.open("POST","../woven_order/requires/print_booking_multijob_controller.php",true);
			}	
			else if(type==574) // Multiple Job Wise Embellishment Work Order
			{				 var data="action="+action_type+
							'&txt_booking_no='+"'"+booking_no+"'"+
							'&txt_job_no='+"'"+job_no+"'"+
							'&cbo_company_name='+"'"+company_id+"'"+
							'&txt_order_no_id='+"'"+order_id+"'"+
							'&cbo_supplier_name='+"'"+supplier_id+"'"+
							'&cbo_booking_natu='+"'"+emb_name+"'"+
							'&cbo_gmt_item='+"'"+item_number_id+"'"+
							'&show_comment='+"'"+show_comment+"'"+
							'&cbo_template_id=1'+
							'&report_title= Embellishment Work Order '+
							'&id_approved_id='+"'"+approved+"'"
							'&path=../../';
				freeze_window(5);
				http.open("POST","../woven_gmts/requires/print_booking_multijob_controller.php",true);
			}				
			else // main print booking
			{
				var data="action=show_trim_booking_report"+
							'&txt_booking_no='+"'"+booking_no+"'"+
							'&txt_job_no='+"'"+job_no+"'"+
							'&cbo_company_name='+"'"+company_id+"'"+
							'&txt_order_no_id='+"'"+order_id+"'"+
							'&cbo_supplier_name='+"'"+supplier_id+"'"+
							'&cbo_booking_natu='+"'"+emb_name+"'"+
							'&cbo_gmt_item='+"'"+item_number_id+"'"+
							'&show_comment='+"'"+show_comment+"'"+
							'&cbo_template_id=1'+
							'&id_approved_id='+"'"+approved+"'";
				freeze_window(5);
				http.open("POST","../woven_order/requires/print_booking_controller2.php",true);
			}
		}
		
		else if(category==31) //Services - Embellishment
		{	
			var report_title='Lab Test Work Order';
			var data="action=show_trim_booking_report"+
							'&data='+"'"+company_id+'*'+booking_no+'*'+order_id+'*'+fabric_source+'*'+report_title+"'";
			freeze_window(5);
			http.open("POST","../woven_order/requires/labtest_work_order_controller.php",true);
		}
				
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function(){
		if(http.readyState == 4) 
		{
			var file_data=http.responseText.split('****');
			if(type==272 && category==4 && (file_data[0].length==0 || file_data[0].includes(".xls")))
			{
				var html=file_data[1];
			}
			else if(type==774 && category==4 && (file_data[0].length==0 || file_data[0].includes(".xls")))
			{
				var html=file_data[1];
			}
			else
			{
				var html=file_data[0];
			}

			//var html=file_data[0];
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+html+'</body</html>');
			d.close();
			release_freezing();
		}
		release_freezing();
		}
	}
	
	function generate_fabric_booking_report(txt_booking_no,action)// Report here
	{ 
		freeze_window(5);
		var data="action="+action+'&txt_booking_no='+txt_booking_no;
		http.open("POST","../woven_order/requires/print_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_trim_report_reponse;	
	}
	
	function generate_trim_report_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert( http.responseText);return;
			$('#data_panel').html( http.responseText );
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
			release_freezing();
		}
	}

	function booking_report_generate(company,booking_no,fabric_natu,type)
	{
		var report_title='Pro Forma Invoice V2';	

		print_report( company+'*'+booking_no+'*'+4, "print_f", "../../commercial/import_details/requires/pi_print_urmi")		
	}
	function booking_woven_report_generate(company,booking_no,fabric_natu,type)
	{
		var report_title='Pro Forma Invoice V2';	
		print_report(company+'*'+booking_no+'*'+3, "print_wf", "../../commercial/import_details/requires/pi_print_urmi")		
	}
	
	function generate_report(company,job_no,type,buyer_id,style_ref)
	{
		freeze_window(5);
		//alert(type);
		if(type=='summary')
		{
			var rpt_type=3;var comments_head=0;
		}
		else if(type=='budget3_details')
		{
			var rpt_type=4;var comments_head=1;
		}
		var excess_per_val="";
		if(type == 'budgetsheet')
		{
			var r=confirm("Press  \"OK\" to Show Budget, \nPress  \"Cancel\"  to Show Management Budget");
		}
		var report_title="Budget/Cost Sheet";//report_title
		var zero_val=1;	
		var path='../../';	
		var data="action="+type+"&txt_job_no='"+job_no+"'&cbo_company_name='"+company+"'&cbo_buyer_name='"+buyer_id+"'&txt_style_ref='"+style_ref+"'&reporttype='"+rpt_type+"'&comments_head='"+comments_head+"'&report_title="+report_title+"'&zero_value="+zero_val+"&excess_per_val="+excess_per_val+"'&img_path="+path;
		//alert(data);
		
		http.open("POST","../woven_order/requires/pre_cost_entry_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_report_reponse;
	}
	
	function fnc_generate_report_reponse()
	{
		if(http.readyState == 4) 
		{
			/*var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><body>'+http.responseText+'</body</html>');
			d.close();*/
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+http.responseText+'</body</html>');
			d.close();
			release_freezing();
		}
	}
		
	

	function fncfabricrcvissue(action,data,width)
	{ 
		 emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/wo_or_fabric_booking_report_controller.php?action='+action+'&data='+data, 'Details Info', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}
	function fnc_load_report_format()
    {
        var data=$('#cbo_company_id').val();
        var report_ids = return_global_ajax_value( data, 'report_formate_setting', '', 'requires/wo_or_fabric_booking_report_controller');
        print_report_button_setting(report_ids);
    }

	function print_report_button_setting(report_ids)
	{
		if(trim(report_ids)=="")
        {
		$("#show_button").hide();
		$("#show_button1").hide();
		$("#show_button3").hide();
		$("#show_button2").hide();
		$("#show_button4").hide();
		}else{
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==178) $("#show_button").show();
			if(report_id[k]==195) $("#show_button1").show();
			if(report_id[k]==242) $("#show_button3").show();
			if(report_id[k]==107) $("#show_button2").show();
			if(report_id[k]==359) $("#show_button4").show();
		}
		}
	}
	function fn_disable_com(str){
		if(str==1){$("#cbo_company_id").attr('disabled','disabled');}
		else{ $('#cbo_company_id').removeAttr("disabled");}
	}
	function getBuyerId() 
	{
	    var company_name = document.getElementById('cbo_company_id').value;
		
	    if(company_name !='') {
		  var data="action=load_drop_down_buyer&data="+company_name;
		 // alert(data);
		  http.open("POST","requires/wo_or_fabric_booking_report_controller.php",true);
		  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		  http.send(data); 
		  http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              var response = trim(http.responseText);
	              $('#buyer_td').html(response);
				  
				  //set_multiselect('cbo_buyer_id','0','0','','0');
	          }			 
	      };
	    }         
	}	

</script>
</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission); ?>    		 
    <form name="wofbreport_1" id="wofbreport_1" autocomplete="off" > 
    <h3 style="width:1250px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" >
            <fieldset style="width:1250px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>   
                    <th width="100" class="must_entry_caption">Item Category</th>
                    <th width="80">WO Type</th>
                    <th width="120" class="must_entry_caption">Company Name</th>
                    <th width="100">Buyer</th>
                     <th width="100">Season</th>
                    <th width="60">WO Year</th>
                    <th width="75">WO No.</th>
                    <th width="60">Job Year</th>
                    <th width="75">Job No.</th>
                    <th width="75">PO No.</th>
                    <th width="70">Internal Ref.</th>
                    <th width="70">File No</th>
                    <th width="120" colspan="2">Date</th>
                    <th width="80">Date Category</th>
                    <th width="80">Fabric Source</th>
                    <th>Order Status</th>
                </thead>
                 <tbody>
                    <tr class="general">
                        <td><? echo create_drop_down( "cbo_category_id", 100, $item_category,"", 1, "--Select Category--", $selected, "",0,"2,3,4,12,24,25,31","" ); ?></td>
                        <td>
							<? 
								$wo_type=array(1=>"Short",2=>"Main",3=>"Sample With Order",4=>"Sample Non Order");
								echo create_drop_down( "cbo_wo_type", 80, $wo_type,"", 1, "--All--", $selected, "",0,"","" );
                            ?>
                        </td>
                        <td><? echo create_drop_down( "cbo_company_id", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Select Company-", $selected, "" ); ?> </td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_id", 100, $blank_array,"", 1, "-Select Buyer-", $selected, "",0,"" ); ?></td>
                        <td id="season_td"><? echo create_drop_down( "cbo_season_id", 100, $blank_array,"", 1, "-Select Season-", $selected, "",0,"" ); ?></td>
                        <td><? echo create_drop_down( "cbo_year_id", 60, $year,"", 1, "-Year-", date("Y"), "",0,"","" ); ?></td>
                        <td>
                            <input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes" style="width:65px" placeholder="Write/Browse" onDblClick="openmypage_wo();"  />
                            <input type="hidden" id="hidd_wo_id" name="hidd_wo_id" style="width:50px" />
                        </td>
                        <td><? echo create_drop_down( "cbo_job_year_id", 60, $year,"", 1, "-Year-", date("Y"), "",0,"","" ); ?></td>
                        <td> 
                            <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:65px" placeholder="Write/Browse" onDblClick="openmypage_job();" />
                            <input type="hidden" id="hidd_job_id" name="hidd_job_id" style="width:50px" />
							<input type="hidden" id="hidd_job_prefix_num" name="hidd_job_prefix_num" style="width:50px" />
                        </td>
                        <td>
                            <input type="text" name="txt_po_no" id="txt_po_no" class="text_boxes" style="width:65px" placeholder="Browse" onDblClick="openmypage_po();" readonly />
                            <input type="hidden" id="hidd_po_id" name="hidd_po_id" style="width:50px" />
                        </td>
                        
                        <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:60px"></td>
                      	<td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:60px"></td>
                         
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" ></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="To Date" ></td>
                        <td>
							<? 
								$date_cat=array(1=>'Booking Date',2=>'Delivery Date',3=>'Country  Ship. date' ,4=>'Shipment date');
								$selected_year=1;
								echo create_drop_down( "txt_date_category", 80, $date_cat,"", 0, "--All--", 1, "",0,"","" );
                            ?>
                        </td>
                        <td><? echo create_drop_down( "cbo_fabric_source", 80, $fabric_source,"", 1, "-- Select --", "","", "", ""); ?></td>
                        <td><? echo create_drop_down( "cbo_order_status", 70, $row_status,"", 0, "", 1,"", "", "");	?></td>
                    </tr>
                    <tr>
                        <td colspan="16" align="center">
                            <? echo load_month_buttons(1); ?>
                            &nbsp;&nbsp; &nbsp;&nbsp;
                            <input type="button" id="show_button" class="formbutton"  value="Show" onClick="fn_report_generated(0)" style="display:none;width:90px"/>
                            <input type="button" id="show_button1" class="formbutton"  value="Show2" onClick="fn_report_generated(1)" style="display:none;width:90px"/>
							<input type="button" id="show_button3" class="formbutton"  value="Show 3" onClick="fn_report_generated(3)" style="display:none;width:90px"/>
							<input type="button" id="show_button2" class="formbutton"  value="Report" onClick="fn_report_generated(2)" style="display:none;width:90px" />
							<input type="button" id="show_button4" class="formbutton"  value="Show 4" onClick="fn_report_generated(4)" style="display:none;width:90px" />


				
					
                            <input type="reset" id="reset_btn" class="formbutton" style="width:90px" value="Reset" onClick="reset_form('wofbreport_1','report_container3*report_container4','','','')" />
                        </td>
                    </tr>
                </tbody>
            </table> 
        	</fieldset>
        </div>
        <div id="report_container3" align="center"></div>
        <div id="report_container4" align="left"></div>
    </form> 
</div>
   <div style="display:none" id="data_panel"></div>
</body>
<script>set_multiselect('cbo_company_id','0','0','','0',"fn_disable_com(1);fnc_load_report_format();getBuyerId();")</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
