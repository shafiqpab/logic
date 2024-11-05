<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Daily Gate and Our Report
				
Functionality	:	
JS Functions	:
Created by		:	Ashraful 
Creation date 	: 	24/03/2014
Updated by 		: 	Rakib	
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
echo load_html_head_contents("Yarn Item Ledger","../../", 1, 1, $unicode,1,1); 
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
	
		var page_link='requires/daily_gate_entry_report_contorller.php?action=chalan_surch&company='+company+'&category='+category;
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
	
		var page_link='requires/daily_gate_entry_report_contorller.php?action=pi_search&company='+company+'&category='+category;  
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

	/*	
	function reset_field()
	{
		reset_form('item_receive_issue_1','report_container2','','','','');
	}
	function job_order_per()
	{
		var item_val=$('#cbo_item_cat').val();
		if((item_val==2)||(item_val==3)||(item_val==4)||(item_val==13)||(item_val==14))
		{
			$('#txt_style_ref').attr("disabled",false);
			$('#txt_order').attr("disabled",false);
			$('#cbo_buyer_name').attr("disabled",false);
		}
		
		else
		{
			$('#txt_style_ref').attr("disabled",true);
			$('#txt_order').attr("disabled",true);
			$('#cbo_buyer_name').attr("disabled",true);
		}
		
	}
	*/
    function  generate_report(operation)
	{
		
		//alert("xx");
		var cbo_item_cat = $("#cbo_item_cat").val();
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_location = $("#cbo_location").val();
		var cbo_gate_type = $("#cbo_gate_type").val();
	
		var txt_pi_no = $("#txt_pi_no").val();
		var cbo_search_by= $("#cbo_search_by").val();
        var cbo_group = $('#cbo_group').val();
		//alert(cbo_search_by);	
		var cbo_party_type = $("#cbo_party_type").val();
		var txt_challan = $("#txt_chalan_no").val();
		var txt_search_item = $("#txt_search_id").val();
		var txt_date_from = $("#txt_date_from").val();
		var txt_date_to = $("#txt_date_to").val();
		var cbo_sample = $("#cbo_sample").val();		
		var sample_chk_id = $("#sample").val();	
		//alert(cbo_search_by);
		
		if(operation==6 && txt_challan!=''){
			if(form_validation('txt_chalan_no','txt_challan')==false )
			{
				return;
			} 
		}
		else
		{
			if( form_validation('txt_date_from*txt_date_to','Date From*Date To')==false )
			{
				return;
			} 
		}

		var dataString = "&cbo_item_cat="+cbo_item_cat+"&cbo_company_name="+cbo_company_name+"&cbo_location="+cbo_location+"&cbo_gate_type="+cbo_gate_type+"&txt_pi_no="+txt_pi_no+"&txt_challan="+txt_challan+"&txt_search_item="+txt_search_item+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&cbo_search_by="+cbo_search_by+"&cbo_party_type="+cbo_party_type+"&cbo_sample="+cbo_sample+"&sample_chk_id="+sample_chk_id+"&cbo_group="+cbo_group;
		if (operation==3) var data="action=generate_report"+dataString;
		else if (operation==4) var data="action=generate_report2"+dataString;
        else if (operation==5) var data="action=generate_report3"+dataString;
        else if (operation==6) var data="action=generate_report4"+dataString;
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/daily_gate_entry_report_contorller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse; 		
	}

	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			//alert(http.responseText);				
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("table_body",-1);
			setFilterGrid("table_body_1",-1);
			setFilterGrid("table_body_2",-1);
			setFilterGrid("table_body_3",-1);
			
			show_msg('3');
			release_freezing();			
			
			/*	var reponse=trim(http.responseText).split("**");
				//alert(reponse[2]);
				$("#report_container2").html(reponse[0]);
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				if(reponse[2]==2)
				{
					setFilterGrid("table_body",-1,tableFilters);
					setFilterGrid("table_body_1",-1,tableFilters);
				}
				else if(reponse[2]==13)
				{
					setFilterGrid("table_body",-1,tableFilters2);
					setFilterGrid("table_body_1",-1,tableFilters2);
				}
				else if(reponse[2]==4)
				{
					setFilterGrid("table_body",-1,tableFilters3);
					setFilterGrid("table_body_1",-1,tableFilters2);
				}
				else
				{
					setFilterGrid("table_body",-1);
					setFilterGrid("table_body_1",-1);
				}
				release_freezing();
				show_msg('3');*/
				//document.getElementById('report_container').innerHTML=report_convert_button('../../');
		}
	} 


	function new_window()
	{		
		//document.getElementById('scroll_body').style.visibility='visible';		
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('.fltrow').hide();
		//$('#scroll_body table tbody tr:first').hide();
		//$('#scroll_body').css('overflow','visible');
	    var w = window.open("Surprise", "#");
	    var d = w.document.open();
	    d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');

	    //document.getElementById('caption').style.visibility='hidden';
	    d.close(); 
		//$('#scroll_body table tbody tr:first').show();
		$('.fltrow').show();
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
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

	

	function fnc_get_pass_print(cbo_company_name,txt_system_id,action,basis,location,returnable,challan_no,issue_id)
	{
		

		//print_report( cbo_company_name+'*'+txt_system_id+'*'+report_title,action,'../../inventory/requires/get_pass_entry_controller');

		if(action=='get_out_entry_print' || action=='get_out_entry_print8_fashion'  || action=='get_out_entry_print14')
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}

			var report_title=$( "div.form_caption" ).html();

			print_report( cbo_company_name+'*'+txt_system_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+location,action,'../../inventory/requires/get_pass_entry_controller');

			//generate_report_file( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+report_title+'*'+show_item+'*'+$('#cbo_basis').val()+'*'+$('#cbo_com_location_id').val()+'&template_id='+$('#cbo_template_id').val(),'get_out_entry_print14','requires/get_pass_entry_controller');

		}

		else if(action=='get_out_entry_print10')
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}

			var report_title=$( "div.form_caption" ).html();
             var no_copy = 1 ;
			print_report( cbo_company_name+'*'+txt_system_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+location+'*'+no_copy  ,action,'../../inventory/requires/get_pass_entry_controller');


		}
		else if(action=='get_out_entry_print6' || action=='get_out_entry_print9')
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}

			var report_title=$( "div.form_caption" ).html();

			print_report( cbo_company_name+'*'+txt_system_id+'*'+location+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable,action,'../../inventory/requires/get_pass_entry_controller');

			//generate_report_file( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+report_title+'*'+show_item+'*'+$('#cbo_basis').val()+'*'+$('#cbo_returnable').val()+'&template_id='+$('#cbo_template_id').val(),'get_out_entry_printamt','requires/get_pass_entry_controller');
		}
		else if(action=='get_out_entry_printamt')
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}

			var report_title=$( "div.form_caption" ).html();
			if (basis==13) {
			print_report( cbo_company_name+'*'+txt_system_id+'*'+location+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable,action,'../../inventory/requires/get_pass_entry_controller');
			}
			else{
			     alert("This is for Embellishment Issue Entry");
			}
			//generate_report_file( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+report_title+'*'+show_item+'*'+$('#cbo_basis').val()+'*'+$('#cbo_returnable').val()+'&template_id='+$('#cbo_template_id').val(),'get_out_entry_printamt','requires/get_pass_entry_controller');
		}
		else if(action=='print_to_html_report')
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}

			var report_title=$( "div.form_caption" ).html();
			
			print_report( cbo_company_name+'*'+txt_system_id+'*'+location+'*'+show_item,action,'../../inventory/requires/get_pass_entry_controller');
			
			//window.open("requires/get_pass_entry_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+show_item+'&action=print_to_html_report&template_id='+$('#cbo_template_id').val(), true );
		}
		else if(action=='get_out_entry_emb_issue_print')
		{
			var report_title="Gate Pass";
			if (basis==13) {
			print_report( cbo_company_name+'*'+txt_system_id+'*'+location+'*'+report_title+'*'+challan_no,action,'../../inventory/requires/get_pass_entry_controller');
			}
			else{
			     alert("This is for Embellishment Issue Basis");
			}
			
			//generate_report_file( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+report_title+'*'+emb_issue_ids+'&template_id='+$('#cbo_template_id').val(),'get_out_entry_emb_issue_print','requires/get_pass_entry_controller');
		}
		else if(action=='print_to_html_report4')
		{
			
			if(basis==12)
			{
				var show_item='';
				var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
				if (r==true)
				{
					show_item="1";
				}
				else
				{
					show_item="0";
				}

				print_report( cbo_company_name+'*'+txt_system_id+'*'+show_item,action,'../../inventory/requires/get_pass_entry_controller');
				//window.open("requires/get_pass_entry_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+show_item+'&action='+"print_to_html_report4&template_id="+$('#cbo_template_id').val(), true );
			}
			else
			{
				alert("This is for Garments Delivery Basis");
			}
			
		}

		else if(action=='print_to_html_report9')
		{
			
			if(basis==12)
			{
				var show_item='';
				var report_title=$( "div.form_caption" ).html();
				print_report( cbo_company_name+'*'+txt_system_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+location,action,'../../inventory/requires/get_pass_entry_controller');
				//generate_report_file( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+report_title+'*'+show_item+'*'+$('#cbo_basis').val()+'*'+$('#cbo_com_location_id').val()+'&template_id='+$('#cbo_template_id').val(),'print_to_html_report9','requires/get_pass_entry_controller');
			}
			else
			{
				alert("This is for Garments Delivery Basis");
			}
			
		}
		else if(action=='print_to_html_report10')
		{
			
			if(basis==28)
			{
				var show_item='';
				var report_title=$( "div.form_caption" ).html();
				print_report( cbo_company_name+'*'+txt_system_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+location,action,'../../inventory/requires/get_pass_entry_controller');
				//generate_report_file( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+report_title+'*'+show_item+'*'+$('#cbo_basis').val()+'*'+$('#cbo_com_location_id').val()+'&template_id='+$('#cbo_template_id').val(),'print_to_html_report10','requires/get_pass_entry_controller');
			}
			else
			{
				alert("This is for Garments/Sample Delivery Basis");
			}
			
		}

		else if(action=='print_to_html_report11')
		{
			
			if(basis==2)
			{
				print_report( cbo_company_name+'*'+txt_system_id+'*'+location,action,'../../inventory/requires/get_pass_entry_controller');
				//window.open("requires/get_pass_entry_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+'&action='+"print_to_html_report11&template_id="+$('#cbo_template_id').val(), true );
			}
			else
			{
				alert("This is for Yarn Basis Only");
			}
			
		}

		else if(action=='print_to_html_report14')
		{
			
			if(basis==11)
			{
				print_report( cbo_company_name+'*'+txt_system_id+'*'+location,action,'../../inventory/requires/get_pass_entry_controller');
				//window.open("requires/get_pass_entry_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+'&action='+"print_to_html_report14&template_id="+$('#cbo_template_id').val(), true );
			}
			else
			{
				alert("This is for Finish Fabric Delivery to Store Basis");
			}
			
		}
		else if(action=='print_to_html_report5')
		{
			var show_item=0;	
			print_report( cbo_company_name+'*'+txt_system_id+'*'+location+'*'+show_item,action,'../../inventory/requires/get_pass_entry_controller');
			
			//window.open("requires/get_pass_entry_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+show_item+'&template_id='+$('#cbo_template_id').val()+'&action='+"print_to_html_report5", true );
			
			
		}

		else if(action=='print_to_html_report6')
		{
			
			
			if(basis!=14)
			{
				alert('Report Generate only for Challan[Cutting Delivery] Basis');
			}
			else
			{
			 	var show_item=0;	
				print_report( cbo_company_name+'*'+txt_system_id+'*'+show_item+'*'+challan_no+'*'+location,action,'../../inventory/requires/get_pass_entry_controller');
				//window.open("requires/get_pass_entry_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+show_item+'*'+$('#txt_chalan_no').val()+'*'+$('#cbo_com_location_id').val()+'&template_id='+$('#cbo_template_id').val()+'&action='+"print_to_html_report6", true );
			}
			
			
		}
		else if(action=='get_out_entry_print12')
		{
			var show_item='';
			var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}
	
			
			var report_title=$( "div.form_caption" ).html();
				
			print_report( cbo_company_name+'*'+txt_system_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+location,action,'../../inventory/requires/get_pass_entry_controller');
			
			//generate_report_file( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+report_title+'*'+show_item+'*'+$('#cbo_basis').val()+'*'+$('#cbo_com_location_id').val()+'&template_id='+$('#cbo_template_id').val(),'get_out_entry_print12','requires/get_pass_entry_controller');
			//return;
			
			
		}
		else if(action=='print_to_html_report_13' || action=='print_to_html_report_15' || action=='print_to_html_report_15_v2' || action=='print_to_html_report16')
		{
			var show_item='';
				var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
				if (r==true)
				{
					show_item="1";
				}
				else
				{
					show_item="0";
				}
				
			print_report( cbo_company_name+'*'+txt_system_id+'*'+location+'*'+show_item,action,'../../inventory/requires/get_pass_entry_controller');
			
			//window.open("requires/get_pass_entry_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+show_item+'&action=print_to_html_report_13&template_id='+$('#cbo_template_id').val(), true );
			
			
		}
		else if(action=='print_to_html_report17')
		{
			if (basis != 8){
				alert("This Button Only For Subcon Knitting Delevery Basis");
				return;
			}			
				
			print_report( cbo_company_name+'*'+txt_system_id+'*'+location+'*'+basis+'*'+challan_no,action,'../../inventory/requires/get_pass_entry_controller');
			
			//window.open("requires/get_pass_entry_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_com_location_id').val()+'*'+$('#cbo_basis').val()+'*'+$('#txt_chalan_no').val()+'&action=print_to_html_report17&template_id='+$('#cbo_template_id').val(), true );
			
			
		}
		else if(action=='print_to_html_report7')
		{
			if(basis!=4 && basis!=3)
			{
				alert('Report Generate only for Challan[Grey Fabric] and Challan[Finish Fabric] Basis');
			}
			else
			{
			  var show_item=0;	
				
			}			
				
			print_report( cbo_company_name+'*'+txt_system_id+'*'+show_item+'*'+challan_no,action,'../../inventory/requires/get_pass_entry_controller');
			
			//window.open("requires/get_pass_entry_controller.php?data=" + $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+show_item+'*'+$('#txt_chalan_no').val()+'&template_id='+$('#cbo_template_id').val()+'&action='+"print_to_html_report7", true );
			
			
		}
		
	}

	function gate_in_report(company_id, sys_number , out_company, com_location_id) {
		var report_title="Gate In Entry";
		// alert(out_company);
		print_report(company_id+'*'+sys_number+'*'+out_company+'*'+report_title+'*'+com_location_id, "get_in_entry_print", "../requires/get_in_entry_controller" ) 
		return;
	}


    function gate_enable_disable(type)
    {	
	    var category=$("#cbo_item_cat").val();
	    var sample_id=$("#cbo_sample").val();
		if(type==1)
		{
			if (category ==4 || category ==30) $("#cbo_sample").attr("disabled",false); 
			else $("#cbo_sample").attr("disabled",true); 
		}
		else
		{
			if (sample_id !=0) $("#cbo_item_cat").attr("disabled",true); 
			else $("#cbo_item_cat").attr("disabled",false); 
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

	function print_report_button_setting(report_ids)
	{
	    $('#search').hide();
	    $('#search2').hide();
	    var report_id=report_ids.split(",");
	    report_id.forEach(function(items)
	    {
	        if(items==222){$('#search').show();}
	        else if(items==259){$('#search2').show();}
            else if(items==242){$('#search3').show();}
            else if(items==359){$('#search4').show();}
        });
	}


	function fnc_rec_qty_in_out_details(cbo_company_name, gat_pass_system_no, buyer_order, action, popupTitle) {
    var emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_gate_entry_report_contorller.php?cbo_company_name=' + cbo_company_name + '&gat_pass_system_no=' + gat_pass_system_no + '&buyer_order=' + buyer_order +'&action=' + action, popupTitle, 'width=800px,height=320px,center=1,resize=0', '../../');
    emailwindow.onclose = function() {
        // Optional: Add any custom close event handling code here
    }; 
    }


</script>
</head>

<body onLoad="set_hotkey()">
 <form name="item_receive_issue_1" id="item_receive_issue_1" autocomplete="off" >
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
   
    <h3 align="left" id="accordion_h1" style="width:1470px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div style="width:1470px;" align="center" id="content_search_panel">
        <fieldset style="width:1470px;">
             <table class="rpt_table" width="1470" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                    	<th width="130"><p>Item Category</p></th>
                        <th width="100">Sample <input type="checkbox" name="sample" id="sample" onClick="check_last_update(1);"/>All</th>
                        <th width="130">Company</th>
						<th width="120">Location</th>                               
                        <th width="100">Challan No./ System ID</th>
                        <th width="100">PI/WO/REQ</th>
                        <th width="100">Party Type</th>
                        <th width="100" id="search_by_th_up"> Buyer</th>
						<th width="80"> Type</th>
                        <th width="80"> Within Group</th>
                        <th width="100" class="must_entry_caption">Date From</th>
                        <th width="100" class="must_entry_caption">Date To</th>
                        <th width="130"><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_field()" /></th>
                    </tr>
                </thead>
                <tr class="general">
                	<td>
						<?
					   echo create_drop_down( "cbo_item_cat", 120, $item_category,"", 1, "--- Select ---", $selected, "gate_enable_disable(1)","","",0 );
                        ?>
                    </td>
                    <td>
						
					   <? echo create_drop_down( "cbo_sample", 100, "select id,sample_name from lib_sample where status_active=1 order by sample_name","id,sample_name",1, "-- Select --", 0, "gate_enable_disable(2)" ); ?>
                       
                    </td>
                    <td>
                            <?
                        echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "get_php_form_data(this.value,'print_button_variable_setting','requires/daily_gate_entry_report_contorller' );" );
							//load_drop_down( 'requires/daily_gate_entry_report_contorller', this.value, 'load_drop_down_supplier', 'suplier_td');
                        ?>                          
                    </td>
					<td>
						
					   <? echo create_drop_down( "cbo_location", 120, "select id, location_name from lib_location where status_active=1 group by id,location_name order by location_name","id,location_name",1, "-- Select --", 0, "" ); ?>
                       
                    </td>
                   
                    <td align="center">
                        <input style="width:95px;"  name="txt_chalan_no" id="txt_chalan_no"  ondblclick="openmypage_chalan()"  class="text_boxes" placeholder="Browse "   />   
                        <input type="hidden" name="txt_chalan_id" id="txt_chalan_id"/>    
                        <input type="hidden" name="txt_search_id" id="txt_search_id"/>            
                    </td> 
                    
                     <td align="center">
                        <input type="text" style="width:95px;"  name="txt_pi_no" id="txt_pi_no"  ondblclick="openmypage_order()"  class="text_boxes" placeholder="Browse or Write"   />         
                    </td>
                     <td width="100" id="">
						<? 
						$search_by = array(1=>'Buyer',2=>'Supplier',3=>'Other Party');
						$dd="search_populate(this.value)";
						$party_type_arr=array(1=>"Buyer",2=>"Supplier",3=>"Other Party");
						echo create_drop_down( "cbo_party_type", 100, $party_type_arr,"", 1, "-- Select Party Type --", $selected, "load_drop_down( 'requires/daily_gate_entry_report_contorller', this.value, 'load_drop_down_sent', 'sent_td');search_populate(this.value);",0 );
					?></td>
                    <td id="sent_td">
                    	<?
								 echo create_drop_down( "cbo_search_by", 100, $blank_array,"", 1, "-- Select --", $selected, "","","" );
                         ?>
                    </td>
					 <td>
						<? //load_drop_down('requires/dyeing_production_report_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_buyer', 'cbo_buyer_name_td' );
						$gate_type_arr=array(1=>"Gate In",2=>"Gate Out",3=>"Gate Out Pending",4=>"Return Pending");
						echo create_drop_down( "cbo_gate_type",70, $gate_type_arr,"",1, "--All--", 0,"",0 );
                        ?>
                     </td>
                    <td>
                        <?
                        echo create_drop_down( "cbo_group", 80, $yes_no,"", 1, "-- Select Group --", 0, "",0 );
                        ?>
                    </td>
                      <td>
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:95px;" readonly/> 
                    </td>
                    <td>
                    	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:95px;" readonly/>
                    </td>
                    <td>
                        <input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:50px; display: none; margin-top: 2px;" class="formbutton" />
                        <input type="button" name="search" id="search2" value="Show 2" onClick="generate_report(4)" style="width:50px; display: none; margin-top: 2px;" class="formbutton" />
                        <input type="button" name="search" id="search3" value="Show 3" onClick="generate_report(5)" style="width:50px; display: none; margin-top: 2px;" class="formbutton" />
                        <input type="button" name="search4" id="search4" value="Show 4" onClick="generate_report(6)" style="width:50px; display: none; margin-top: 2px;" class="formbutton" />
                    </td>
                </tr>
                <tr>
                	<td colspan="12" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                </tr>
            </table>  
        </fieldset> 
           
    </div>
        <!-- Result Contain Start-->
        
        	<fieldset><div id="report_container" align="center"></div></fieldset>
           <fieldset><div id="report_container2"></div> </fieldset>


        <!-- Result Contain END-->
</div> 
 </form>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
<script>
set_multiselect('cbo_location','0','0','','0');
</script> 
<script>
	$("#cbo_sample").val(0);
	gate_enable_disable(2);
</script>
</html>
