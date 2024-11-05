<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Out Order Entry
				
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	01-10-2013
Updated by 		: 	Kausar 	(Creating print report)	/ Rakib
Update date		: 	11-01-2014		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Gate Out  Info","../", 1, 1, $unicode,1,1); 

?>
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
  	
// popup for SYSTEM ID----------------------
/*function openmypage_system()
{
	get_php_form_data(sysNumber, "populate_master_from_data", "requires/get_out_entry_controller" );
	show_list_view(sysNumber,'show_dtls_list_view','list_container','requires/get_out_entry_controller','');
}*/

function openmypage_system()
{
	page_link='requires/get_out_entry_controller.php?action=system_id_popup';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Gate Out Id Popup', 'width=780px, height=480px, center=1, resize=0, scrolling=0','');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var sysNumber=(this.contentDoc.getElementById("hidden_gate_pass_id").value).split('_');; // system number
		if(sysNumber!="")
		{
			freeze_window(5);		
			release_freezing();
		}
	}
}

function openmypage_getpass()
{
	page_link='requires/get_out_entry_controller.php?action=getpass_id_popup';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Gate Out Id Popup', 'width=940px, height=420px, center=1, resize=0, scrolling=0','');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var sysNumber=(this.contentDoc.getElementById("hidden_gate_pass_id").value).split('_');; // system number
		if(sysNumber!="")
		{
			freeze_window(5);
			$("#txt_gate_pass").val(sysNumber[1]);

			if (sysNumber[2] != "") $("#txt_vehicle_number").val(sysNumber[2]).attr('disabled','disabled');
			else $("#txt_vehicle_number").val("").removeAttr('disabled');

			if (sysNumber[3] != "") $("#txt_driver_name").val(sysNumber[3]).attr('disabled','disabled');
			else $("#txt_driver_name").val("").removeAttr('disabled');
			
			get_php_form_data( sysNumber[1], 'company_wise_report_button_setting','requires/get_out_entry_controller' );
			fnc_view_first_prt_btn();
			release_freezing();
		}
	}	
}

function fnc_getout_entry(operation)
{
	if( form_validation('txt_gate_pass','Gate Pass ID')==false )
	{
		return;
	}
	document.getElementById('list_container_gate_out_dtls').innerHTML='';
	var dataString = "txt_gate_pass*txt_gate_out_date*txt_gate_out_time*txt_vehicle_number*txt_driver_name";
	var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../");
	//alert(data);return;
	//freeze_window(operation);
	http.open("POST","requires/get_out_entry_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_getout_entry_reponse;
}

function fnc_getout_entry_reponse()
{
	if(http.readyState == 4) 
	{
		//alert(http.responseText);
		var response=trim(http.responseText).split('**');
		if(response[0]==0)
		{
			document.getElementById('txt_gate_pass').value='';
			document.getElementById('list_container_gate_out_dtls').innerHTML='<p class="bang" style="background:#CCC; padding:0 20px;"><span style="color:#FF0000;font-size:20px"></span>আপনার তথ্য জমা হয়েছে। </p><p class="eng" style=" background:#CCC; padding:0 5px; font-size:20px"><span style="color:#FF0000;"></span> Data Save Successfully, Gate Out ID NO : '+ response[2]+'</p>';
			$('#txt_gate_pass').focus(); 
			show_msg(trim(response[0]));
		}
	   	if (response[0]==40)
		{
			document.getElementById('txt_gate_pass').value='';
			document.getElementById('list_container_gate_out_dtls').innerHTML='<p class="bang" style="background:#CCC; padding:0 5px;font-size:20px"><span style="color:#FF0000;"> </span>আপনি যে Gate Pass নাম্বার টি বাছাই করেছেন তা আগে একবার পাঠানো হয়েছে।</p><p class="eng" style=" background:#CCC; padding:0 5px;font-size:20px"><span style="color:#FF0000;"></span>Second Time Gate Out Not Allowed.</p>';
			$('#txt_gate_pass').focus(); 
		}
		if (response[0]==5)
		{
			document.getElementById('txt_gate_pass').value='';
			document.getElementById('list_container_gate_out_dtls').innerHTML='<p class="bang" style="background:#CCC; padding:0 5px;font-size:20px"><span style="color:#FF0000;"> </span>গেইট পাস নম্বরটি  সঠিক  নয়।</p> <p class="eng" style=" background:#CCC; padding:0 5px;font-size:20px"><span style="color:#FF0000;"></span>This Gate Pass No. Does Not Match</p>';
			$('#txt_gate_pass').focus(); 
		}

		if (response[0]==30)
		{
			document.getElementById('txt_gate_pass').value='';
			document.getElementById('list_container_gate_out_dtls').innerHTML='<p class="bang" style="background:#CCC; padding:0 5px;font-size:20px"><span style="color:#FF0000;"></span> দুঃখিত, ২৪ ঘণ্টার পর Gate out করতে হলে আপনাকে অনুমতি নিতে হবে। </p><p class="eng" style=" background:#CCC; padding:0 5px;font-size:20px"><span style="color:#FF0000;"></span>After 24 hours Gate Out Not Allowed, Please contract autority.</p>';
			$('#txt_gate_pass').focus(); 
		}
		else if(response[0]==10 )
		{
			show_msg(trim(response[0]));
			document.getElementById('txt_gate_pass').value='';
			document.getElementById('list_container_gate_out_dtls').innerHTML='<p class="bang" style="background:#CCC; padding:0 20px;"><span style="color:#FF0000;"></span>দুঃখিত ! আপনার তথ্য জমা করা সম্ভব হয়নি । </p><p class="eng" style=" background:#CCC; padding:0 20px;"><span style="color:#FF0000;"></span>Sorry! Data don\'t Save.</p>';
			$('#txt_gate_pass').focus();
			release_freezing();
			return;
		}
		if (response[0]==11)
		{
			document.getElementById('txt_gate_pass').value='';
			document.getElementById('list_container_gate_out_dtls').innerHTML='<p class="bang" style="background:#CCC; padding:0 5px;font-size:20px"><span style="color:#FF0000;"></span> দুঃখিত, আগেই গেট আউট হয়ে গিয়েছে । </p><p class="eng" style=" background:#CCC; padding:0 5px;font-size:20px"><span style="color:#FF0000;"></span>Sorry , Duplicate Data Found.</p>';
			$('#txt_gate_pass').focus(); 
		}
		//show_list_view(response[2],'show_dtls_list_view','list_container','requires/get_out_entry_controller','');
		//reset_form('','','txt_item_description*cbo_uom*txt_quantity*txt_rate*txt_amount*txt_remarks','','','');
		//set_button_status(0, permission, 'fnc_getout_entry',1,1);
		release_freezing();
 	}
}

$('#txt_gate_pass').live('keydown', function(e) {
   
    if (e.keyCode === 13) {
        e.preventDefault();
		  gate_out_scan(this.value); 
    }
});

function gate_out_scan(str)
{
	get_php_form_data( str, 'scan_getpass','requires/get_out_entry_controller' );
	var gate_pass=$("#txt_gate_pass").val();
	 
    var uppercaseString = gate_pass.toUpperCase();
	//alert(uppercaseString);
	if(uppercaseString!=null)
	{
		get_php_form_data( uppercaseString, 'company_wise_report_button_setting','requires/get_out_entry_controller' );
		fnc_view_first_prt_btn();
	}
}

function startTime() 
{
	var today=new Date();
	var h=today.getHours();
	var m=today.getMinutes();
	var s=today.getSeconds();
	
	var ampm = h >= 12 ? 'pm' : 'am';
	h = h % 12;
	h = h ? h : 12; // the hour '0' should be '12'
	m = m < 10 ? '0'+m : m;
	var strTime = h + ':' + m + ':' + s + ' ' + ampm;
   
    m = checkTime(m);
    s = checkTime(s);
    document.getElementById('txt_gate_out_time').value = strTime;
    var t = setTimeout(function(){startTime()},500);	  
}
function checkTime(i) {
    if (i<10) {i = "0" + i};  // add zero in front of numbers < 10
    return i;
}
  
function setfocus() {
    $('#txt_gate_pass').focus();
} 
 
//----------------------------print---------------------------

function fnc_getpass_entry(operation)
{
	var get_pass_data=$( "#txt_get_pass_data" ).val();
	var getPassDataArr=get_pass_data.split('**');
	
	var system_id=getPassDataArr[0];
	var company_name=getPassDataArr[1];
	var basis=getPassDataArr[2];
	var location_id=getPassDataArr[3];
	var returnable=getPassDataArr[4];
	
	if(operation==4)
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
		var report_title="";

		generate_report_file( company_name+'*'+system_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+location_id+'&template_id='+$('#cbo_template_id').val(),'get_out_entry_print','requires/get_pass_entry_controller');
		//return;
	}
	else if(operation==8)
	{	
		var show_item="0";
		var report_title="";
		generate_report_file( company_name+'*'+system_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+location_id+'&template_id='+$('#cbo_template_id').val(),'get_out_entry_print8_fashion','requires/get_pass_entry_controller');
		return;
	}
	if(operation==6)
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
		generate_report_file( company_name+'*'+system_id+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&template_id='+$('#cbo_template_id').val(),'get_out_entry_print6','requires/get_pass_entry_controller');
		return;
	}		
}

function fnc_getpass_entry_reponse()  
{
	if(http.readyState == 4) 
	{
		var response=trim(http.responseText).split('**');
		show_msg(trim(response[0]));
		release_freezing();
	}
}
 
function print_to_html_report(operation)
{
	var get_pass_data=$( "#txt_get_pass_data" ).val();
	var getPassDataArr=get_pass_data.split('**');
	
	var system_id=getPassDataArr[0];
	var company_name=getPassDataArr[1];
	var basis=getPassDataArr[2];
	var location_id=getPassDataArr[3];
	var returnable=getPassDataArr[4];
	var challan_no=getPassDataArr[5];
	
	
	
	if (operation==2) 
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
		window.open("requires/get_pass_entry_controller.php?data=" + company_name+'*'+system_id+'*'+location_id+'*'+show_item+'&action=print_to_html_report&template_id='+$('#cbo_template_id').val(), true );
	}
	else if(operation==3)
	{
		//For Print Embellishment Issue
		if($("#cbo_basis").val()==13)
		{
			var emb_issue_ids= $("#txt_chalan_no").val();
			var report_title="Gate Pass";
			generate_report_file( company_name+'*'+system_id+'*'+location_id+'*'+report_title+'*'+emb_issue_ids+'&template_id='+$('#cbo_template_id').val(),'get_out_entry_emb_issue_print','requires/get_pass_entry_controller');
		}
		else if($("#cbo_basis").val()==49)
		{
			var emb_issue_ids= $("#txt_chalan_no").val();
			var issue_id= $("#txt_issue_no").val();
			var report_title="Gate Pass";
			//generate_report_file( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+report_title+'*'+show_item+'*'+$('#cbo_basis').val()+'&template_id='+$('#cbo_template_id').val(),'get_out_entry_print','requires/get_pass_entry_controller');
			
			generate_report_file( company_name+'*'+system_id+'*'+report_title+'*'+emb_issue_ids+'*'+issue_id+'&template_id='+$('#cbo_template_id').val(),'get_out_entry_printing_delivery_print','requires/get_pass_entry_controller');
		}
		else
		{
			alert("This is for Embellishment Issue Basis");
		}
		return;
	}
	else if (operation==4) 
	{
		if($("#cbo_basis").val()==12)
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
			window.open("requires/get_pass_entry_controller.php?data=" + company_name+'*'+system_id+'*'+show_item+'&action='+"print_to_html_report4&template_id="+$('#cbo_template_id').val(), true );
		}
		else
		{
			alert("This is for Garments Delivery Basis");
		}
	}
	else if (operation==9) //id_print_to_button10
	{
		if(basis==12)
		{
			var show_item='';
			var report_title="";
			generate_report_file( company_name+'*'+system_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+location_id+'&template_id='+$('#cbo_template_id').val(),'print_to_html_report9','requires/get_pass_entry_controller');
		}
		else
		{
			alert("This is for Garments Delivery Basis");
		}
	}
	else if (operation==10) //id_print_to_button10
	{
		if(basis_id==28)
		{
			var show_item='';
		
		var report_title="";
		generate_report_file( company_name+'*'+system_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+location_id+'&template_id='+$('#cbo_template_id').val(),'print_to_html_report10','requires/get_pass_entry_controller');
		}
		else
		{
			alert("This is for Sample Delivery Basis");
		}
	}
	else if (operation==11) 
	{
		if(basis==2)
		{
			
			window.open("requires/get_pass_entry_controller.php?data=" + company_name+'*'+system_id+'*'+location_id+'*'+'&action='+"print_to_html_report11&template_id="+$('#cbo_template_id').val(), true );
		}
		else
		{
			alert("This is for Yarn Basis Only");
		}
	}
	else if (operation==14) 
	{
		if(basis==11)
		{
			
			window.open("requires/get_pass_entry_controller.php?data=" + company_name+'*'+system_id+'*'+location_id+'*'+'&action='+"print_to_html_report14&template_id="+$('#cbo_template_id').val(), true );
		}
		else
		{
			alert("This is for Finish Fabric Delivery to Store Basis");
		}
	}
	else if (operation==5) 
	{
		
		var show_item=0;	
		window.open("requires/get_pass_entry_controller.php?data=" + company_name+'*'+system_id+'*'+location_id+'*'+show_item+'&template_id='+$('#cbo_template_id').val()+'&action='+"print_to_html_report5", true );
	}
	else if (operation==6) 
	{
		if(basis!=14)
		{
			alert('Report Generate only for Challan[Cutting Delivery] Basis');
		}
		else
		{
		  var show_item=0;	
			window.open("requires/get_pass_entry_controller.php?data=" + company_name+'*'+system_id+'*'+show_item+'*'+challan_no+'*'+location_id+'&template_id='+$('#cbo_template_id').val()+'&action='+"print_to_html_report6", true );
		}
	}
	else if (operation==12) 
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
		generate_report_file(company_name+'*'+system_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+location_id+'&template_id='+$('#cbo_template_id').val(),'get_out_entry_print12','requires/get_pass_entry_controller');
		return;
	}
	else if (operation==13) 
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
		window.open("requires/get_pass_entry_controller.php?data=" + company_name+'*'+system_id+'*'+location_id+'*'+show_item+'&action=print_to_html_report_13&template_id='+$('#cbo_template_id').val(), true );
	}
	else if (operation==7) 
	{
		if(basis!=4 && basis!=3)
		{
			alert('Report Generate only for Challan[Grey Fabric] and Challan[Finish Fabric] Basis');
		}
		else
		{
		  var show_item=0;	
			window.open("requires/get_pass_entry_controller.php?data=" + company_name+'*'+system_id+'*'+show_item+'*'+chalan_no+'&template_id='+$('#cbo_template_id').val()+'&action='+"print_to_html_report7", true );
		}
	}
}

function generate_report_file(data,action,page)
{
	window.open("requires/get_pass_entry_controller.php?data=" + data+'&action='+action, true );
}	

function fnc_view_first_prt_btn()
{
	var gate_pass_first_prt_btn= $("#txt_gate_pass_first_print_button").val();
	
	//alert(gate_pass_first_prt_btn); 
	var get_pass_data_arr= $("#txt_get_pass_data").val().split("**");
	if(gate_pass_first_prt_btn != "")
	{
		var report_title="Gate Pass Entry";
		var show_item=0;
		var gate_pass_no = get_pass_data_arr[0];
		var company_id = get_pass_data_arr[1];
		var basis_id = get_pass_data_arr[2];
		var location_id = get_pass_data_arr[3];
		var returnable_id = get_pass_data_arr[4];
		var challan_no = get_pass_data_arr[5];
		var issue_id = get_pass_data_arr[6];
		var template_id = $('#cbo_template_id').val();
		if(gate_pass_first_prt_btn==115)
		{
			/* var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){ show_item="1"; }else{ show_item="0"; } */

			show_list_view(company_id+'*'+gate_pass_no+'*'+report_title+'*'+show_item+'*'+basis_id+'*'+location_id+'&template_id='+template_id, 'get_out_entry_print', 'list_container_gate_out_dtls', 'requires/get_pass_entry_controller', '');
		}
		else if(gate_pass_first_prt_btn==116)
		{
			/* var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){ show_item="1"; }else{ show_item="0"; } */

			show_list_view(company_id+'*'+gate_pass_no+'*'+location_id+'*'+show_item+'&template_id='+template_id, 'print_to_html_report', 'list_container_gate_out_dtls', 'requires/get_pass_entry_controller', '');
		}
		else if(gate_pass_first_prt_btn==136)				
		{
			if(basis_id==13)
			{
				show_list_view(company_id+'*'+gate_pass_no+'*'+location_id+'*'+report_title+'*'+challan_no+'&template_id='+template_id, 'get_out_entry_emb_issue_print', 'list_container_gate_out_dtls', 'requires/get_pass_entry_controller', '');
			}
			else if(basis_id==62 || basis_id==55)
			{
				show_list_view(company_id+'*'+gate_pass_no+'*'+location_id+'*'+report_title+'*'+challan_no+'&template_id='+template_id, 'get_out_entry_emb_issue_print_emb_print', 'list_container_gate_out_dtls', 'requires/get_pass_entry_controller', '');
			}
			else if(basis_id==49)
			{		
				show_list_view(company_id+'*'+gate_pass_no+'*'+report_title+'*'+challan_no+'*'+issue_id+'&template_id='+template_id, 'get_out_entry_printing_delivery_print', 'list_container_gate_out_dtls', 'requires/get_pass_entry_controller', '');
			}

		}
		else if(gate_pass_first_prt_btn==137) 
		{
			show_item=0;
			show_list_view(company_id+'*'+gate_pass_no+'*'+location_id+'*'+show_item+'&template_id='+template_id, 'print_to_html_report5', 'list_container_gate_out_dtls', 'requires/get_pass_entry_controller', '');
		}
		else if(gate_pass_first_prt_btn==129)
		{
			/* var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){ show_item="1"; }else{ show_item="0"; } */

			show_list_view(company_id+'*'+gate_pass_no+'*'+report_title+'*'+show_item+'*'+basis_id+'*'+location_id+'&template_id='+template_id, 'get_out_entry_print12', 'list_container_gate_out_dtls', 'requires/get_pass_entry_controller', '');
		}
		else if(gate_pass_first_prt_btn==161)
		{
			/* var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){ show_item="1"; }else{ show_item="0"; } */

			show_list_view(company_id+'*'+gate_pass_no+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis_id+'*'+returnable_id+'&template_id='+template_id, 'get_out_entry_print6', 'list_container_gate_out_dtls', 'requires/get_pass_entry_controller', '');
		}
		else if(gate_pass_first_prt_btn==191)
		{
			/* var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){ show_item="1"; }else{ show_item="0"; } */
			
			show_list_view(company_id+'*'+gate_pass_no+'*'+location_id+'*'+show_item+'&template_id='+template_id, 'print_to_html_report_13', 'list_container_gate_out_dtls', 'requires/get_pass_entry_controller', '');
		}	
		else if(gate_pass_first_prt_btn==196) 
		{					
			if(basis_id==14)
			{
				show_item=0;
				show_list_view(company_id+'*'+gate_pass_no+'*'+show_item+'*'+basis_id+'*'+challan_no+'*'+location_id+'&template_id='+template_id, 'print_to_html_report6', 'list_container_gate_out_dtls', 'requires/get_pass_entry_controller', '');
			}
		}
		else if(gate_pass_first_prt_btn==199)
		{
			if(basis_id==4 || basis_id==3)
			{
				show_item=0;
				show_list_view(company_id+'*'+gate_pass_no+'*'+show_item+'*'+basis_id+'&template_id='+template_id, 'print_to_html_report7', 'list_container_gate_out_dtls', 'requires/get_pass_entry_controller', '');
			}
		}
		else if(gate_pass_first_prt_btn==206) 
		{
			show_item="0";
			show_list_view(company_id+'*'+gate_pass_no+'*'+report_title+'*'+show_item+'*'+basis_id+'*'+location_id+'&template_id='+template_id, 'get_out_entry_print8_fashion', 'list_container_gate_out_dtls', 'requires/get_pass_entry_controller', '');
		}
		else if(gate_pass_first_prt_btn==207) 
		{
			if(basis_id==12)
			{
				show_item=0;
				show_list_view(company_id+'*'+gate_pass_no+'*'+report_title+'*'+show_item+'*'+basis_id+'*'+location_id+'&template_id='+template_id, 'print_to_html_report9', 'list_container_gate_out_dtls', 'requires/get_pass_entry_controller', '');
			}
		}
		else if(gate_pass_first_prt_btn==208)
		{
			if(basis_id==28)
			{
				show_item=0;		
				show_list_view(company_id+'*'+gate_pass_no+'*'+report_title+'*'+show_item+'*'+basis_id+'*'+location_id+'&template_id='+template_id, 'print_to_html_report10', 'list_container_gate_out_dtls', 'requires/get_pass_entry_controller', '');
			}
		}
		else if(gate_pass_first_prt_btn==212) 
		{
			if(basis_id==2)
			{	
				show_list_view(company_id+'*'+gate_pass_no+'*'+location_id+'&template_id='+template_id, 'print_to_html_report11', 'list_container_gate_out_dtls', 'requires/get_pass_entry_controller', '');
			}
		}
		else if(gate_pass_first_prt_btn==271)
		{
			if(basis_id==11)
			{		
				show_list_view(company_id+'*'+gate_pass_no+'*'+location_id+'&template_id='+template_id, 'print_to_html_report14', 'list_container_gate_out_dtls', 'requires/get_pass_entry_controller', '');
			}
		}
		else if(gate_pass_first_prt_btn==42) 
		{
			/* var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){ show_item="1"; }else{ show_item="0"; } */

			show_list_view(company_id+'*'+gate_pass_no+'*'+location_id+'*'+show_item+'&template_id='+template_id, 'print_to_html_report_15', 'list_container_gate_out_dtls', 'requires/get_pass_entry_controller', '');
		}
		else if(gate_pass_first_prt_btn==362) 
		{
			/* var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){ show_item="1"; }else{ show_item="0"; } */

			show_list_view(company_id+'*'+gate_pass_no+'*'+location_id+'*'+show_item+'&template_id='+template_id, 'print_to_html_report_15_v2', 'list_container_gate_out_dtls', 'requires/get_pass_entry_controller', '');
		}
		else if(gate_pass_first_prt_btn==227) 
		{
			/* var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){ show_item="1"; }else{ show_item="0"; } */

			show_list_view(company_id+'*'+gate_pass_no+'*'+location_id+'*'+show_item+'&template_id='+template_id, 'print_to_html_report16', 'list_container_gate_out_dtls', 'requires/get_pass_entry_controller', '');
		}
		else if(gate_pass_first_prt_btn==707)
		{
			if (basis_id== 8){
				show_list_view(company_id+'*'+gate_pass_no+'*'+location_id+'*'+basis_id+'*'+challan_no+'&template_id='+template_id, 'print_to_html_report17', 'list_container_gate_out_dtls', 'requires/get_pass_entry_controller', '');
			}			
		}
		else if(gate_pass_first_prt_btn==235)
		{
			/* var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){ show_item="1"; }else{ show_item="0"; } */

			show_list_view(company_id+'*'+gate_pass_no+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis_id+'*'+returnable_id+'&template_id='+template_id, 'get_out_entry_print9', 'list_container_gate_out_dtls', 'requires/get_pass_entry_controller', '');
		}
		else if(gate_pass_first_prt_btn==274)
		{
			/* var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){ show_item="1"; }else{ show_item="0"; } */

			show_list_view(company_id+'*'+gate_pass_no+'*'+report_title+'*'+show_item+'*'+basis_id+'*'+location_id+'*1&template_id='+template_id, 'get_out_entry_print10', 'list_container_gate_out_dtls', 'requires/get_pass_entry_controller', '');
		}
		else if(gate_pass_first_prt_btn==738)
		{
			if(basis_id==13){
				show_item=0;
				show_list_view(company_id+'*'+gate_pass_no+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis_id+'*'+returnable_id+'&template_id='+template_id, 'get_out_entry_printamt', 'list_container_gate_out_dtls', 'requires/get_pass_entry_controller', '');
			}
		}
		else if(gate_pass_first_prt_btn==747) 
		{
			/* var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){ show_item="1"; }else{ show_item="0"; } */

			show_list_view(company_id+'*'+gate_pass_no+'*'+report_title+'*'+show_item+'*'+basis_id+'*'+location_id+'&template_id='+template_id, 'get_out_entry_print14', 'list_container_gate_out_dtls', 'requires/get_pass_entry_controller', '');
		}
		else if(gate_pass_first_prt_btn==241) 
		{
			/* var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){ show_item="1"; }else{ show_item="0"; } */

			show_list_view(company_id+'*'+gate_pass_no+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis_id+'*'+returnable_id+'&template_id='+template_id, 'get_pass_entry_print11', 'list_container_gate_out_dtls', 'requires/get_pass_entry_controller', '');
		}
		else if(gate_pass_first_prt_btn==427)
		{
			/* var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){ show_item="1"; }else{ show_item="0"; } */

			show_list_view(company_id+'*'+gate_pass_no+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis_id+'*'+returnable_id+'&template_id='+template_id, 'get_out_entry_print20', 'list_container_gate_out_dtls', 'requires/get_pass_entry_controller', '');
		}
		else if(gate_pass_first_prt_btn==28) 
		{
			/* var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){ show_item="1"; }else{ show_item="0"; } */

			show_list_view(company_id+'*'+gate_pass_no+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis_id+'*'+returnable_id+'&template_id='+template_id, 'get_out_entry_print21', 'list_container_gate_out_dtls', 'requires/get_pass_entry_controller', '');
		}
		else if(gate_pass_first_prt_btn==437) 
		{
			/* var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){ show_item="1"; }else{ show_item="0"; } */

			show_list_view(company_id+'*'+gate_pass_no+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis_id+'*'+returnable_id+'&template_id='+template_id, 'get_out_entry_print22', 'list_container_gate_out_dtls', 'requires/get_pass_entry_controller', '');
		}
		else if(gate_pass_first_prt_btn==280)
		{
			/* var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){ show_item="1"; }else{ show_item="0"; } */

			show_list_view(company_id+'*'+gate_pass_no+'*'+location_id+'*'+show_item+'&template_id='+template_id, 'print_to_html_report_scandex', 'list_container_gate_out_dtls', 'requires/get_pass_entry_controller', '');
		}
		else if(gate_pass_first_prt_btn==304)
		{
			/* var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){ show_item="1"; }else{ show_item="0"; } */

			show_list_view(company_id+'*'+gate_pass_no+'*'+report_title+'*'+show_item+'*'+basis_id+'*'+location_id+'&template_id='+template_id+'&print_mercer=Gate Pass', 'get_out_entry_print28', 'list_container_gate_out_dtls', 'requires/get_pass_entry_controller', '');
		}
		else if(gate_pass_first_prt_btn==719)
		{
			/* var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			if (r==true){ show_item="1"; }else{ show_item="0"; } */

			show_list_view(company_id+'*'+gate_pass_no+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis_id+'*'+returnable_id+'&template_id='+template_id+'&print_mercer=Gate Pass', 'get_out_entry_print16', 'list_container_gate_out_dtls', 'requires/get_pass_entry_controller', '');
  		 
		}
	}
}
  
</script>
<body onLoad="set_hotkey();startTime();setfocus();">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../",$permission);  ?>
        <form name="getout_1" id="getout_1"  autocomplete="off">
	    <div style="width:90%;">
	    <legend>Gate Out</legend>
	    <fieldset style="width:1000px;">
	        <table width="1000" cellpadding="0" cellspacing="2" id="tbl_master">           
	            <tr>
	                <td width="100" align="right" class="must_entry_caption">Gate Pass ID</td>
	                <td width="150">
						<input type="text" name="txt_gate_pass" id="txt_gate_pass" class="text_boxes" style="width:150px" placeholder="Scan/Write/Browse" onDblClick="openmypage_getpass();" />
	                </td>
	                <td width="100" align="right" class="must_entry_caption">Gate Out ID</td>
	                <td width="150">
						<input type="text" name="txt_gate_pass_insert" id="txt_gate_pass_insert" class="text_boxes" style="width:150px" placeholder="Browse" onDblClick="openmypage_system();"  readonly/>
	                </td>
	                <td width="100" align="right">Date & Time</td>
	                <td width="300">
	                 <input class="datepicker" type="text" style="width:100px;" name="txt_gate_out_date" id="txt_gate_out_date"  value="<? echo date('d-m-y'); ?>" /> 
	                 <input class="text_boxes" type="text" style="width:90px;" name="txt_gate_out_time" id="txt_gate_out_time"  value="<? echo date('h:m:s a',time()+6*60*60); ?>" /> 
	                </td>
	                <td width="100" align="right" ><input type="button" name="re_button" id="re_button" value="Save" style="width:100px" onClick="fnc_getout_entry(0)" class="formbutton"  /> </td>
	                <td width="140">
						<input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /> 
	                </td>
	            </tr>
	            <tr>
					<td align="right">Vehicle Number</td>
	                <td >
						<input type="text" name="txt_vehicle_number" id="txt_vehicle_number" class="text_boxes" style="width:150px"/>
	                </td>
					<td align="right">Driver Name</td>
	                <td >
						<input type="text" name="txt_driver_name" id="txt_driver_name" class="text_boxes" style="width:150px"/>
	                </td>
	            	<td colspan="4" align="center">
	            		<? echo create_drop_down( "cbo_template_id", 85, $report_template_list,'', 0, '', 0, ""); ?>
	                    <input id="Printt1" class="formbutton" type="button" style="width:80px; display:none;" onClick="fnc_getpass_entry(4)" name="print" value="Print">
	                    <input type="button" style="width:80px; display:none;" id="id_print_to_button"  onClick="print_to_html_report(2)"   class="formbutton" name="id_print_to_button" value="Print 2" />
	                    <input type="button" style="width:80px; display:none;" id="with_color_size_print"  onClick="print_to_html_report(3)"   class="formbutton" name="with_color_size_print" value="Print 3" />
	                    <input type="button" style="width:80px; display:none;" id="id_print_to_button5"  onClick="print_to_html_report(5)"   class="formbutton" name="id_print_to_button5" value="Print4" />
	                    <input type="button" style="width:80px; display:none;" id="id_print_to_button12"  onClick="print_to_html_report(12)"   class="formbutton" name="id_print_to_button12" value="Print5" />
	                    <input type="button" id="print6" class="formbutton"  style="width:80px;display:none;" onClick="fnc_getpass_entry(6)" name="print6" value="Print 6">
	                   <input type="button" id="print13" class="formbutton"  style="width:80px;display:none;" onClick="print_to_html_report(13)" name="print7" value="Print 7">
	                    <input type="button" style="width:80px; display:none;" id="id_print_to_button4"  onClick="print_to_html_report(4)"   class="formbutton" name="id_print_to_button4" value="Gmts. Delivery" />
	                    <input type="button" style="width:80px; display:none;" id="id_print_to_button6"  onClick="print_to_html_report(6)"   class="formbutton" name="id_print_to_button6" value="Cutting Delivery" />
	                    <input type="button" style="width:80px; display:none;" id="id_print_to_button7"  onClick="print_to_html_report(7)"   class="formbutton" name="id_print_to_button7" value="Fabric Delivery" />
	                    <input type="button" style="width:80px; display:none;" id="id_print_to_button8"  onClick="fnc_getpass_entry(8)"   class="formbutton" name="id_print_to_button8" value="General" />
	                    <input type="button" style="width:100px; display:none;" id="id_print_to_button9"  onClick="print_to_html_report(9)"   class="formbutton" name="id_print_to_button9" value="Gmts Delivery2" />
	                    <input type="button" style="width:100px; display:none;" id="id_print_to_button10"  onClick="print_to_html_report(10)"   class="formbutton" name="id_print_to_button10" value="Sample Delivery" />
	                    <input type="button" style="width:80px; display:none;" id="id_print_to_button11"  onClick="print_to_html_report(11)"   class="formbutton" name="id_print_to_button11" value="Yarn Delivery" />
	                    <input type="button" style="width:120px; display:none;" id="id_print_to_button14"  onClick="print_to_html_report(14)"   class="formbutton" name="id_print_to_button14" value="Finish Fabric Delivery" />
	                    <input type="hidden" id="txt_get_pass_data" value="">
	                    <input type="hidden" id="txt_gate_pass_first_print_button" value="">
	            	</td>
	            </tr>
	            
	           
	        </table>
	    </fieldset>
	    <br>       
	    <fieldset style="width:100%;">
	        <div id="list_container_gate_out_dtls"></div>	       
	    </fieldset>
		</div>
		</form>
	</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>