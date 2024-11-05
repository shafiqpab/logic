<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Gate Pass and In Report
				
Functionality	:	
JS Functions	:
Created by		:	REZA 
Creation date 	: 	19/11/2015
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
echo load_html_head_contents("Gate Passs and In Report","../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function openmypage_chalan()
	{
		if( form_validation('cbo_item_cat*cbo_company_name','Item Category*Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var category= $("#cbo_item_cat").val();	
	
		var page_link='requires/gate_in_and_out_report_contorller.php?action=chalan_surch&company='+company+'&category='+category;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=500px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var system_id=this.contentDoc.getElementById("hidden_chalan_id").value; // product ID
			var system_no=this.contentDoc.getElementById("hidden_chalan_no").value; // product ID
			var search_by=this.contentDoc.getElementById("hidden_search_number").value; // product Description
		
			$("#txt_chalan_no").val(system_no);
			$("#txt_chalan_id").val(system_id);
			$("#txt_search_id").val(search_by); 
			
		}
	}
	
	
	function openmypage_order()
	{
		if( form_validation('cbo_item_cat*cbo_company_name','Item Category*Company Name*')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var category= $("#cbo_item_cat").val();	
	
		var page_link='requires/gate_in_and_out_report_contorller.php?action=pi_search&company='+company+'&category='+category;  
		var title="Search Chalan no/System Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=520px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var pi_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var pi_des=this.contentDoc.getElementById("txt_selected").value; // product Description
		
			//alert(style_des_no);
			$("#txt_pi_no").val(pi_des);
			$("#txt_pi_id").val(pi_id); 
			
		
		}
	}

	function openmypage_gatePassNo()
	{
		if( form_validation('cbo_company_name','Company Name*')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var category= $("#cbo_item_cat").val();	
	
		var page_link='requires/gate_in_and_out_report_contorller.php?action=gatepass_popup&company='+company;  
		var title="Search Gate Pass No";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=520px,height=370px,center=1,resize=0,scrolling=0','../')

		// emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/store_wise_item_stock_report_controller.php?action=description_popup&cbo_company_name='+cbo_company_name+'&cbo_item_category='+cbo_item_category+'&cbo_item_group='+cbo_item_group+'&txt_description_id='+txt_description_id, 'Description Details', 'width=510px,height=420px,center=1,resize=0,scrolling=0','../../');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var selected_name=this.contentDoc.getElementById("txt_selected").value;
			var selected_id=this.contentDoc.getElementById("txt_selected_id").value;

			$("#txt_gate_pass_no").val(selected_name);
			$("#txt_gate_pass_id").val(selected_id);

			var txt_gate_pass_no = $("#txt_gate_pass_no").val();	
			//alert(txt_gate_pass_no);
			if(txt_gate_pass_no !="")
			{
				$("#txt_date_from").val(""); 
				$("#txt_date_to").val(""); 
				//$("#search3").attr("disabled",true); 
				//$("#search2").attr("disabled",true);  
			}
		}

		
	}

		
	function  generate_report(type)
	{	
		var txt_gate_pass_no = $("#txt_gate_pass_no").val();

		if(txt_gate_pass_no =="")
		{
			if( form_validation('txt_date_from*txt_date_to','Date From*Date To')==false )
			{
				return;
			}
		}
		else
		{			
			$("#txt_date_from").val("");
			$("#txt_date_to").val(""); 
		}
		
		var cbo_item_cat 	= $("#cbo_item_cat").val();
		var cbo_company_name= $("#cbo_company_name").val();
		var cbo_location_id = $("#cbo_location_id").val();
		var cbo_withingroup = $("#cbo_withingroup").val();
		var txt_pi_no 		= $("#txt_pi_no").val();
		var cbo_search_by	= $("#cbo_search_by").val();	
		var cbo_party_type 	= $("#cbo_party_type").val();
		var txt_challan 	= $("#txt_chalan_no").val();
		var txt_search_item = $("#txt_search_id").val();
		var txt_date_from 	= $("#txt_date_from").val();
		var txt_date_to 	= $("#txt_date_to").val();
		var cbo_sample 		= $("#cbo_sample").val();		
		var sample_chk_id 	= $("#sample").val();	
		var txt_gate_pass_no 	= $("#txt_gate_pass_no").val();	
		var txt_gate_pass_id 	= $("#txt_gate_pass_id").val();	
		var cbo_returnable 	= $("#cbo_returnable").val();	
		
		var dataString = "&cbo_item_cat="+cbo_item_cat+"&cbo_company_name="+cbo_company_name+"&cbo_location_id="+cbo_location_id+"&cbo_withingroup="+cbo_withingroup+"&txt_pi_no="+txt_pi_no+"&txt_challan="+txt_challan+"&txt_search_item="+txt_search_item+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&cbo_search_by="+cbo_search_by+"&cbo_party_type="+cbo_party_type+"&cbo_sample="+cbo_sample+"&sample_chk_id="+sample_chk_id+"&txt_gate_pass_no="+txt_gate_pass_no+"&txt_gate_pass_id="+txt_gate_pass_id+"&type="+type+"&cbo_returnable="+cbo_returnable;
		var data="action=generate_report"+dataString;
		//alert(data);
		freeze_window(3);
		http.open("POST","requires/gate_in_and_out_report_contorller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse; 
		
	}

	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
		var reponse=trim(http.responseText).split("####");
		//alert(reponse[0]);
		$("#report_container2").html(reponse[0]);  
		document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		
			var tableFilters3 = 
			 {
				col_operation: {id: ["td_total_qty","td_total_amt"],col: [12,14], operation: ["sum","sum"],write_method: ["innerHTML","innerHTML"]}
			 }		
		
			if(reponse[2]==3){
				setFilterGrid("table_body_1",-1,tableFilters3);
			}
			else
			{
				setFilterGrid("table_body_1",-1);
			}
		show_msg('3');
		release_freezing();
		}
	} 

	function new_window()
	{
		
		//document.getElementById('caption').style.visibility='visible';
		$('#scroll_body tr:first').hide();
		$('#scroll_body').css('overflow','visible');
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	 d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
     '<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');

	//document.getElementById('caption').style.visibility='hidden';
	d.close(); 
	$('#scroll_body tr:first').show();

	}
	function search_populate(str)
	{
		if(str==1)
		{
			document.getElementById('search_by_th_up').innerHTML="Buyer";
			//$('#search_by_th_up').css('color','blue');
		}
		else if(str==2)
		{
			document.getElementById('search_by_th_up').innerHTML="Supplier";
			//$('#search_by_th_up').css('color','blue');
		}
		else if(str==3)
		{
			document.getElementById('search_by_th_up').innerHTML="Other Party";
			//$('#search_by_th_up').css('color','blue');
		}
	}
	function fnc_get_pass_print($print_format,cbo_company_name,txt_system_id,temp_id,com_location_id)
	{
		if($print_format==115){
			var report_title=$( "div.form_caption" ).html();
			print_report( cbo_company_name+'*'+txt_system_id+'*'+report_title+'*'+temp_id,'get_out_entry_print','../../inventory/requires/get_pass_entry_controller');
		}
		else($print_format==116)
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){show_item="1";}else{show_item="0";}
			window.open("../requires/get_pass_entry_controller.php?data=" + cbo_company_name+'*'+txt_system_id+'*'+com_location_id+'*'+show_item+'&action=print_to_html_report&template_id=1', true );

		}
	}

	function fnc_gate_pass_print(print_format,cbo_company_name,txt_system_id,basis,com_location_id,challan_no,issue_id,returnable)
	{
		var report_title="Gate Pass";
		if(print_format==115) 
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){show_item="1";}else{show_item="0";}
			print_report( cbo_company_name+'*'+txt_system_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+com_location_id+'*'+1,'get_out_entry_print','../requires/get_pass_entry_controller');
		}
		else if(print_format==116)
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){show_item="1";}else{show_item="0";}
			window.open("../requires/get_pass_entry_controller.php?data=" + cbo_company_name+'*'+txt_system_id+'*'+com_location_id+'*'+show_item+'&action=print_to_html_report&template_id=1', true );
		}
		else if(print_format==136)
		{
			var emb_issue_ids= $("#txt_chalan_no").val();
			if(basis==13)
			{
				var emb_issue_ids= $("#txt_chalan_no").val();
				print_report( cbo_company_name+'*'+txt_system_id+'*'+com_location_id+'*'+report_title+'*'+challan_no+'&template_id=1','get_out_entry_emb_issue_print','../requires/get_pass_entry_controller');
			}
			else if(basis==49)
			{
				print_report( cbo_company_name+'*'+txt_system_id+'*'+report_title+'*'+challan_no+'*'+issue_id+'&template_id=1','get_out_entry_printing_delivery_print','../requires/get_pass_entry_controller');
			}
			else
			{
				alert("This is for Embellishment Issue Basis");
			}
		}
		else if(print_format==137)
		{
			var show_item=0;	
			window.open("../requires/get_pass_entry_controller.php?data=" +cbo_company_name+'*'+txt_system_id+'*'+com_location_id+'*'+show_item+'&template_id=1&action='+"print_to_html_report5", true );
		}
		else if(print_format==129)
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){show_item="1";}else{show_item="0";}		
			print_report( cbo_company_name+'*'+txt_system_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+com_location_id+'&template_id=1','get_out_entry_print12','../requires/get_pass_entry_controller');
		}
		else if(print_format==161)
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){show_item="1";}else{show_item="0";}	
			print_report( cbo_company_name+'*'+txt_system_id+'*'+com_location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&template_id=1','get_out_entry_print6','../requires/get_pass_entry_controller');
		}
		else if(print_format==191)
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){show_item="1";}else{show_item="0";}
			window.open("../requires/get_pass_entry_controller.php?data=" + cbo_company_name+'*'+txt_system_id+'*'+com_location_id+'*'+show_item+'&action=print_to_html_report_13&template_id=1', true );
		}	
		else if(print_format==196)
		{
			if(basis!=14)
			{
				alert('Report Generate only for Challan[Cutting Delivery] Basis');
			}
			else
			{
				var show_item=0;	
				window.open("../requires/get_pass_entry_controller.php?data="+cbo_company_name+'*'+txt_system_id+'*'+show_item+'*'+challan_no+'*'+com_location_id+'&template_id=1&action='+"print_to_html_report6", true );
			}
		}
		else if(print_format==199)
		{
			if(basis!=4 && basis!=3)
			{
				alert('Report Generate only for Challan[Grey Fabric] and Challan[Finish Fabric] Basis');
			}
			else
			{
				var show_item=0;	
				window.open("../requires/get_pass_entry_controller.php?data=" +cbo_company_name+'*'+txt_system_id+'*'+show_item+'*'+challan_no+'&template_id=1&action='+"print_to_html_report7", true );
			}
		}
		else if(print_format==206)
		{
			var show_item="0";
			print_report( cbo_company_name+'*'+txt_system_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+com_location_id+'&template_id=1','get_out_entry_print8_fashion','../requires/get_pass_entry_controller');
		}
		else if(print_format==207)
		{
			if(basis==12)
			{
				var show_item='';			
				print_report( cbo_company_name+'*'+txt_system_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+com_location_id+'&template_id=1','print_to_html_report9','../requires/get_pass_entry_controller');
			}
			else
			{
				alert("This is for Garments Delivery Basis");
			}
		}
		else if(print_format==208)
		{
			if(basis==28)
			{
				var show_item='';
				var report_title=$( "div.form_caption" ).html();
				print_report( cbo_company_name+'*'+txt_system_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+com_location_id+'&template_id=1','print_to_html_report10','../requires/get_pass_entry_controller');
			}
			else
			{
				alert("This is for Sample Delivery Basis");
			}
		}
		else if(print_format==212)
		{
			if(basis==2)
			{				
				window.open("../requires/get_pass_entry_controller.php?data=" +cbo_company_name+'*'+txt_system_id+'*'+com_location_id+'*'+'&action='+"print_to_html_report11&template_id=1", true );
			}
			else
			{
				alert("This is for Yarn Basis Only");
			}
		}
		else if(print_format==271)
		{
			if(basis==11)
			{				
				window.open("../requires/get_pass_entry_controller.php?data=" +cbo_company_name+'*'+txt_system_id+'*'+com_location_id+'*'+'&action='+"print_to_html_report14&template_id=1", true );
			}
			else
			{
				alert("This is for Finish Fabric Delivery to Store Basis");
			}
		}
		else if(print_format==42)
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){show_item="1";}else{show_item="0";}
			window.open("../requires/get_pass_entry_controller.php?data=" +cbo_company_name+'*'+txt_system_id+'*'+com_location_id+'*'+show_item+'&action=print_to_html_report_15&template_id=1', true );
		}
		else if(print_format==362)
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){show_item="1";}else{show_item="0";}
			window.open("../requires/get_pass_entry_controller.php?data=" +cbo_company_name+'*'+txt_system_id+'*'+com_location_id+'*'+show_item+'&action=print_to_html_report_15_v2&template_id=1', true );
		}
		else if(print_format==227)
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){show_item="1";}else{show_item="0";}
			window.open("../requires/get_pass_entry_controller.php?data=" +cbo_company_name+'*'+txt_system_id+'*'+com_location_id+'*'+show_item+'&action=print_to_html_report16&template_id=1', true );
		}
		else if(print_format==707)
		{
			if (basis!= 8){
				alert("This Button Only For Subcon Knitting Delevery Basis");
				return;
			}			
			window.open("../requires/get_pass_entry_controller.php?data=" +cbo_company_name+'*'+txt_system_id+'*'+com_location_id+'*'+basis+'*'+challan_no+'&action=print_to_html_report17&template_id=1', true );
		}
		else if(print_format==235)
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){show_item="1";}else{show_item="0";}
			print_report(cbo_company_name+'*'+txt_system_id+'*'+com_location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&template_id=1','get_out_entry_print9','../requires/get_pass_entry_controller');
		}
		else if(print_format==274)
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){show_item="1";}else{show_item="0";}
			print_report( cbo_company_name+'*'+txt_system_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+com_location_id+'&template_id=1','get_out_entry_print10','../requires/get_pass_entry_controller');
		}
		else if(print_format==738)
		{
			if(basis==13)
			{
				print_report( cbo_company_name+'*'+txt_system_id+'*'+com_location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&template_id=1','get_out_entry_printamt','../requires/get_pass_entry_controller');
			}
			else{
					alert("This is for Embellishment Issue Entry");
			}
		}
		else if(print_format==747)
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){show_item="1";}else{show_item="0";}
			print_report( cbo_company_name+'*'+txt_system_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+com_location_id+'&template_id=1','get_out_entry_print14','../requires/get_pass_entry_controller');
		}
		else if(print_format==241)
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){show_item="1";}else{show_item="0";}
			print_report( cbo_company_name+'*'+txt_system_id+'*'+com_location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&template_id=1','get_pass_entry_print11','../requires/get_pass_entry_controller');
		}
		else if(print_format==427)
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){show_item="1";}else{show_item="0";}
			print_report( cbo_company_name+'*'+txt_system_id+'*'+com_location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&template_id=1','get_out_entry_print20','../requires/get_pass_entry_controller');
		}
		else if(print_format==28)
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){show_item="1";}else{show_item="0";}
			print_report( cbo_company_name+'*'+txt_system_id+'*'+com_location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&template_id=1','get_out_entry_print21','../requires/get_pass_entry_controller');
		}
		else if(print_format==437)
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){show_item="1";}else{show_item="0";}
			print_report(cbo_company_name+'*'+txt_system_id+'*'+com_location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&template_id=1','get_out_entry_print22','../requires/get_pass_entry_controller');
		}
		else if(print_format==719)
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){show_item="1";}else{show_item="0";}
			print_report(cbo_company_name+'*'+txt_system_id+'*'+com_location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&template_id=1','get_out_entry_print16','../requires/get_pass_entry_controller');
		}
		
	}


	function fnc_get_in_print(cbo_company_name,txt_system_id)
	{
		var report_title=$( "div.form_caption" ).html();
		print_report( cbo_company_name+'*'+txt_system_id+'*'+report_title,'get_in_entry_print','../../inventory/requires/get_in_entry_controller');
	}

	function fnc_grmts_del_print(print_format,company_id,txt_system_id,ex_factory_date,del_company_id)
	{
		var report_title="Garments Delivery Entry";	
		if(print_format==78) 
		{
			print_report( company_id+'*'+txt_system_id+'*'+ex_factory_date+'*'+report_title+'*1', "ex_factory_print", "../../production/requires/garments_delivery_entry_controller" ) ;
		}
		else if(print_format==121)
		{
			print_report( company_id+'*'+txt_system_id+'*'+ex_factory_date+'*'+report_title+'*2*'+del_company_id, "ex_factory_print_new", "../../production/requires/garments_delivery_entry_controller" ); 
		}
		else if(print_format==122)
		{
			var answer = confirm("Do you want to show delivery info? Please click OK button for show and CANCEL button for hide.");
			var show_delv_info = (answer==true) ? 1 : 0;
			print_report( company_id+'*'+txt_system_id+'*'+ex_factory_date+'*'+report_title+'*2*'+show_delv_info, "ex_factory_print_new2", "../../production/requires/garments_delivery_entry_controller" ) 
		}
		else if(print_format==123)
		{
			print_report( company_id+'*'+txt_system_id+'*'+ex_factory_date+'*'+report_title+'*5', "ExFactoryPrintSonia", "../../production/requires/garments_delivery_entry_controller" ) 
		}
		else if(print_format==169)
		{
			var answer = confirm("Do you want to show delivery info? Please click OK button for show and CANCEL button for hide.");
			var show_delv_info = (answer==true) ? 1 : 0;
			print_report( company_id+'*'+txt_system_id+'*'+ex_factory_date+'*'+report_title+'*7*'+show_delv_info, "ex_factory_print_new3", "../../production/requires/garments_delivery_entry_controller" ); 
		}
		else if(print_format==580)
		{
			print_report( company_id+'*'+txt_system_id+'*'+ex_factory_date+'*'+report_title+'*6', "ex_factory_print2", "../../production/requires/garments_delivery_entry_controller" ) ;
		}
		else if(print_format==758)
		{
			print_report( company_id+'*'+txt_system_id+'*'+ex_factory_date+'*'+report_title+'*8', "ex_factory_print_new7", "../../production/requires/garments_delivery_entry_controller" ); 
		}
		else if(print_format==227)
		{
			print_report( company_id+'*'+txt_system_id+'*'+ex_factory_date+'*'+report_title+'*9', "ExFactoryPrint8", "./../production/requires/garments_delivery_entry_controller" ) 
		}
		else if(print_format==235)
		{
			var answer = confirm("Do you want to show delivery info? Please click OK button for show and CANCEL button for hide.");
			var show_delv_info = (answer==true) ? 1 : 0;
			print_report( company_id+'*'+txt_system_id+'*'+ex_factory_date+'*'+report_title+'*10*'+show_delv_info, "ex_factory_print_new9", "./../production/requires/garments_delivery_entry_controller" );
		}
		else
		{
			print_report( company_id+'*'+txt_system_id+'*'+ex_factory_date+'*'+report_title+'*1', "ex_factory_print", "../../production/requires/garments_delivery_entry_controller" ) ;
		}
	}


	function gate_enable_disable(type)
	{

		var category=$("#cbo_item_cat").val();
		var sample_id=$("#cbo_sample").val();
		if(type==1)
		{
			if(category==4 || category==30)
			{
				$("#cbo_sample").attr("disabled",false); 
			}
			else
			{
				$("#cbo_sample").attr("disabled",true); 
			}
		}
		else
		{
			if(sample_id!=0)
			{
				$("#cbo_item_cat").attr("disabled",true); 
			}
			else
			{
				$("#cbo_item_cat").attr("disabled",false); 
			}
		}
	}

	function check_last_update(rowNo)
	{
		var isChecked=$('#sample').is(":checked");
		//$('#sample').attr('checked',false);
		
		//$('#sample').val();
		//alert(rowNo);
		if(isChecked==true)
		{
			$("#cbo_item_cat").attr("disabled",true);
			$("#cbo_sample").attr("disabled",true);
			$('#sample').val(1); 
			$('#cbo_sample').val(''); 
			$('#cbo_item_cat').val(''); 	
		}
		else
		{
			$("#cbo_item_cat").attr("disabled",false);
			$("#cbo_sample").attr("disabled",false);
			$('#sample').val(0); 	 	
		}
	}

	function fnc_get_qty_details( gate_pass_no, action, popupTitle) {
		var title="Search Item Popup";
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/gate_in_and_out_report_contorller.php?gate_pass_no='+gate_pass_no+'&action='+action+'&popupTitle='+popupTitle, title, 'width=880px,height=320px,center=1,resize=0,scrolling=0', '../../');
        emailwindow.onclose=function()
        {}
    }

	function print_button_setting(company)
	{
		$('#button_data_panel').html('');
		// alert(company);
		// console.log(company);
		get_php_form_data(company,'print_button_variable_setting','requires/gate_in_and_out_report_contorller' );
	}

	function print_report_button_setting(report_ids)
	{
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==779)
			{
				$('#button_data_panel').append( '<td colspan="2" align="center"><input type="button" name="search" id="search" value="Gate Out" onClick="generate_report(1)" style="width:80px" class="formbutton" /></td>&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==780)
			{
				$('#button_data_panel').append( '<td> <input type="button" name="search2" id="search2" value="Out Pending" onClick="generate_report(2)" style="width:80px" class="formbutton" /></td>&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==781)
			{
				$('#button_data_panel').append( '<td align="center"> <input type="button" name="search4" id="search4" value="Gate Out 2" onClick="generate_report(4)" style="width:80px" class="formbutton" /></td>&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==782)
			{
				$('#button_data_panel').append( '<td> <input type="button" name="search3" id="search3" value="Gate In" onClick="generate_report(3)" style="width:80px" class="formbutton"/></td>&nbsp;&nbsp;&nbsp;' );
			}	
		}
	}

</script>
</head>

<body onLoad="set_hotkey()">
 <form name="item_receive_issue_1" id="item_receive_issue_1" autocomplete="off" >
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
   <div style="width:1320px;" align="center">
    <h3 align="center" id="accordion_h1" style="width:1320px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    </div>
    <div style="width:1320px;" align="center" id="content_search_panel">
        <fieldset style="width:1320px;">
             <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                <thead>
                    <tr>
                    	<th width="130"><p>Item Category</p><p>&nbsp;</p></th>
                        <th width="100">Sample <input type="checkbox" name="sample" id="sample" onClick="check_last_update(1);"/>All</th>
                        <th width="100">Company</th>                                
                        <th width="150">Location</th>                                
                        <th width="100">Challan No./ System ID</th>
                        <th width="100">PI/WO/REQ</th>
                        <th width="100">Gate Pass No</th>
						<th width="100">Returnable</th>
                        <th width="100">Party Type</th> 
                        <th width="100" id="search_by_th_up"> Buyer</th>
						<th width="60">Within Group</th>
                        <th width="100" class="must_entry_caption">Date From</th>
                        <th width="100" class="must_entry_caption">Date To</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_field()" /></th>
                    </tr>
                </thead>
                <tr class="general">
                	<td>
						<?
					   	echo create_drop_down( "cbo_item_cat", 120, $item_category,"", 1, "--- Select ---", $selected, "gate_enable_disable(1);","","",0 );
                        ?>
                    </td>
                    <td>
					   <? 
					   	echo create_drop_down( "cbo_sample", 100, "select id,sample_name from lib_sample where status_active=1 order by sample_name","id,sample_name",1, "-- Select --", 0, "gate_enable_disable(2)" ); 
						?>
                    </td>
                    <td>
                        <?
                        echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "print_button_setting(this.value);load_drop_down( 'requires/gate_in_and_out_report_contorller',this.value, 'load_drop_down_location', 'com_location_td' );set_multiselect('cbo_location_id','0','0','','');" );
                        ?>                          
                    </td>
                    
                        <td id="com_location_td" >
							<? 
								echo create_drop_down( "cbo_location_id", 150, $blank_array,"", 1, "-- All  --", 0, "",0 );
                            ?>
                        </td>
                   
                    <td align="center">
                        <input style="width:95px;"  name="txt_chalan_no" id="txt_chalan_no"  ondblclick="openmypage_chalan()"  class="text_boxes" placeholder="Browse "   />   
                        <input type="hidden" name="txt_chalan_id" id="txt_chalan_id"/>    
                        <input type="hidden" name="txt_search_id" id="txt_search_id"/>            
                    </td> 
                    
                     <td align="center">
                        <input type="text" style="width:95px;"  name="txt_pi_no" id="txt_pi_no"  ondblclick="openmypage_order()"  class="text_boxes" placeholder="Browse or Write"   />         
                    </td>
					<td align="center">
                        <input type="text" style="width:95px;"  name="txt_gate_pass_no" id="txt_gate_pass_no"  ondblclick="openmypage_gatePassNo()"  class="text_boxes" placeholder="Browse"  />  
						<input type="hidden" name="txt_gate_pass_id" id="txt_gate_pass_id"/>        
                    </td>

					<td>
						<?
						
						echo create_drop_down( "cbo_returnable", 100, $yes_no,"", 1, "-- Select  --", 0, "",0 );
						?>
					</td>
                     <td width="100" id="">
						<? 
							$search_by = array(1=>'Buyer',2=>'Supplier',3=>'Other Party');
							$dd="search_populate(this.value)";
							$party_type_arr=array(1=>"Buyer",2=>"Supplier",3=>"Other Party");
							echo create_drop_down( "cbo_party_type", 100, $party_type_arr,"", 1, "-- Select Party Type --", $selected, "load_drop_down( 'requires/gate_in_and_out_report_contorller', this.value, 'load_drop_down_sent', 'sent_td');search_populate(this.value);",0 );
						?>
                    </td>
                    <td id="sent_td">
                    	<?
							 echo create_drop_down( "cbo_search_by", 100, $blank_array,"", 1, "-- Select --", $selected, "","","" );
                         ?>
                    </td>
					 <td>
						<? 
                            echo create_drop_down( "cbo_withingroup",60, $yes_no,"",1, "--All--", 0,"",0 );
                        ?>
                       </td>
                      <td>
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" value="<?php echo date("d-m-Y"); ?>" style="width:55px;" readonly/> 
                    </td>
                    <td>
                    	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" value="<?php echo date("d-m-Y"); ?>" style="width:55px;" readonly/>
                    </td>
					<td id="button_data_panel"> </td>
                </tr>
                <tr>
                	<td colspan="9" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                </tr>
            </table>  
        </fieldset> 
           
    </div>
    	<div id="report_container" align="center"></div>
        <div id="report_container2"></div> 
</div> 
 </form>    
</body> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
<script>
	$("#cbo_sample").val(0);
	gate_enable_disable(2);
</script>
</html>
