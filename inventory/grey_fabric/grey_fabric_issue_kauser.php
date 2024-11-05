<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Knit Garments Order Entry
				
Functionality	:	
JS Functions	:
Created by		:	Bilas
Creation date 	: 	07-05-2013
Updated by 		: 	Kausar (Creating Report)
Update date		: 	12-12-2013	   
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
echo load_html_head_contents("Grey Issue Info","../../", 1, 1, $unicode,1,1); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
 	
// popup for booking no ----------------------	
function popuppage_fabbook()
{
	if( form_validation('cbo_company_name*cbo_basis*cbo_issue_purpose','Company Name*Issue Basis*Issue Purpose')==false )
	{
		return;
	}
	var company			 = $("#cbo_company_name").val();
	var issue_purpose	 = $("#cbo_issue_purpose").val();
	var page_link='requires/grey_fabric_issue_controller.php?action=fabbook_popup&company='+company+'&issue_purpose='+issue_purpose;
	var title="Booking Information";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px, height=400px, center=1, resize=0, scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var bookingNumber = this.contentDoc.getElementById("hidden_booking_number").value; //bookingID_bookingNo_buyerID_jobNo concate
		if (bookingNumber!="")
		{ 
			bookingNumber = bookingNumber.split("_"); 
			freeze_window(5);
			$("#txt_booking_id").val(bookingNumber[0]);
			$("#txt_booking_no").val(bookingNumber[1]);
			$("#cbo_buyer_name").val(bookingNumber[2]);				
			$("#txt_style_ref").val(bookingNumber[3]);
			$("#txt_order_no").val(bookingNumber[4]);
			$("#hidden_order_id").val(bookingNumber[5]);
			
			if(bookingNumber[5]!="")
			{
				get_php_form_data(bookingNumber[5], "populate_display_from_data", "requires/grey_fabric_issue_controller");	
			}
/*			if(issue_purpose==8)
			{
				load_drop_down( 'requires/grey_fabric_issue_controller', bookingNumber[0]+'_'+$('#cbo_issue_purpose').val()+'_'+bookingNumber[6], 'load_drop_down_color', 'color_td' );
			}
*/			release_freezing();	 
		}
	}		
}

function new_item_controll()
{
	var isRoll=$("#hidden_is_roll_maintain").val();
	var isBatch=$("#hidden_is_batch_maintain").val();
	var issuePurpose=$("#cbo_issue_purpose").val();
	if(isRoll==1)
	{
		$("#txtNoOfRoll").attr("placeholder","Double Click");
		$("#txtItemDescription").attr("placeholder","Display");
		$("#txtNoOfRoll").attr("readonly","readonly");
		$("#txtItemDescription").attr("readonly","readonly");
	}
	else
	{
		$("#txtNoOfRoll").attr("placeholder","Write No of Roll");
		$("#txtItemDescription").attr("placeholder","Double Click");
		$("#txtNoOfRoll").removeAttr("readonly","readonly");
		$("#txtItemDescription").attr("readonly","readonly");
	}
	if(issuePurpose==11 || issuePurpose==4)
	{
		$("#txtIssueQnty").attr("placeholder","Double Click"); 
		$("#txtIssueQnty").attr("readonly","readonly");
	}
	else
	{
		$("#txtIssueQnty").removeAttr("placeholder").attr("placeholder","Wirte"); 
		$("#txtIssueQnty").removeAttr("readonly");
	}
}

//function for field enable disable
function enable_disable()
{
	var issuePurpose	=$("#cbo_issue_purpose").val();
	var issueBasis		=$("#cbo_basis").val();
	var isRoll			=$("#hidden_is_roll_maintain").val();
	var isBatch			=$("#hidden_is_batch_maintain").val();
	
	//onchange initialize-------------
	/*$("#txt_booking_no").val(""); 
	$("#txt_batch_no").val("");
	$("#cbo_buyer_name").val(0);
	$("#txt_order_no").val(""); */
	
	//fabric booking
	if(issueBasis==2)	
	{
		$("#txt_booking_no").attr("disabled",true);		
		$("#txt_booking_no").val(""); 
	}
	else	
		$("#txt_booking_no").removeAttr("disabled");
	 	
	if(isBatch==1) 
	{
		//$("#txt_batch_no").removeAttr("disabled").attr("disabled",true);
		$("#txt_batch_no").removeAttr("placeholder").attr("placeholder","Display/Browse"); 	
		$("#txt_batch_no").attr("readonly","readonly");		 
		$("#txt_batch_no").val("");
	}
	else 
	{
		$("#txt_batch_no").removeAttr("placeholder").attr("placeholder","Write");  
		$("#txt_batch_no").removeAttr("readonly","readonly"); 
	}
	if(issuePurpose==3)
		$("#cbo_buyer_name").removeAttr("disabled");
	else
	{
		$("#cbo_buyer_name").removeAttr("disabled").attr("disabled",true);	
		$("#cbo_buyer_name").val(0);
	}
	//function call for item list enable disable
	new_item_controll();
}

function openpopup_batch()
{
	var isBatch=$("#hidden_is_batch_maintain").val();	
	if( isBatch!=1 ) // batch pop up not allow
	{ 		
		return;
	}
	var cbo_company_id = $('#cbo_company_name').val();
	var title = 'Batch Info';	
	var page_link = 'requires/grey_fabric_issue_controller.php?cbo_company_id='+cbo_company_id+'&action=batch_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var batch_id=this.contentDoc.getElementById("txt_batch_id").value;	 
		var batch_no=this.contentDoc.getElementById("txt_batch_no").value;
		 
		$("#txt_batch_id").val(batch_id);
		$("#txt_batch_no").val(batch_no);
  	}
}

function openroll_popup() 	 
{
	//txtRollNo  txtRollPOid txtRollPOQnty
	var cbo_company_id = $('#cbo_company_name').val();
 	var hidden_roll_id = $('#txtRollNo').val();
	var hidden_roll_qnty = $('#txtRollPOQnty').val();	
	var txt_batch_id = $('#txt_batch_id').val();
	//alert(hidden_roll_id+"="+hidden_roll_qnty);
	if(form_validation('cbo_company_name*cbo_basis*cbo_issue_purpose','Company*Basis*Issue Purpose')==false)
	{
		return;
	} 
	var isRoll=$("#hidden_is_roll_maintain").val();	
	if( isRoll!=1 ) // roll pop up not allow, roll if No
	{ 		
		return;
	}
	var title = 'Roll Info';	
	var page_link = 'requires/grey_fabric_issue_controller.php?cbo_company_id='+cbo_company_id+'&hidden_roll_id='+hidden_roll_id+'&hidden_roll_qnty='+hidden_roll_qnty+'&txt_batch_id='+txt_batch_id+'&action=roll_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var rollTableId=this.contentDoc.getElementById("txt_selected_id").value;	 
		var issueQnty=this.contentDoc.getElementById("txt_issue_qnty").value;
		 	
 		//show_list_view(rollTableId+"**"+issueQnty,'populate_child_from_data','td_item_list','requires/grey_fabric_issue_controller','');		
		get_php_form_data(rollTableId+"**"+issueQnty, "populate_child_from_data", "requires/grey_fabric_issue_controller");		
		//new_item_controll();
	}
}

function openDescription_popup() 	 
{
	
	var cbo_company_id = $('#cbo_company_name').val();	
	var cbo_basis = $('#cbo_basis').val();	
	var txt_booking_no = $('#txt_booking_no').val();
	var cbo_issue_purpose = $('#cbo_issue_purpose').val();	
	
	if(form_validation('cbo_company_name*cbo_basis*cbo_issue_purpose','Company*Basis*Issue Purpose')==false)
	{
		return;
	} 
	if(cbo_basis==1 && txt_booking_no=="")
	{
		alert("Select Booking No First.");
		return;
	}
	var isRoll=$("#hidden_is_roll_maintain").val();	
	if( isRoll==1 ) // roll pop up not allow, roll if Yes
	{ 		
		return;
	}
	var title = 'Item Description Info';	
	var page_link = 'requires/grey_fabric_issue_controller.php?cbo_company_id='+cbo_company_id+'&action=itemDescription_popup'+'&txt_booking_no='+txt_booking_no+'&cbo_basis='+cbo_basis+'&cbo_issue_purpose='+cbo_issue_purpose;
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=920px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var prodID=this.contentDoc.getElementById("txt_selected_id").value;
		get_php_form_data(prodID, "populate_child_from_data_item_desc", "requires/grey_fabric_issue_controller");
		
		if(cbo_basis==1)
		{
			get_php_form_data($("#hidden_order_id").val()+"**"+prodID+"**"+$("#cbo_issue_purpose").val(), "populate_data_about_order", "requires/grey_fabric_issue_controller" );
		}
  	}
}

function issueQntyPopup() //issue quantity
{
	var isRoll=$("#hidden_is_roll_maintain").val();
	var issuePurpose=$("#cbo_issue_purpose").val();
	//sales and no order has no pop up
	if(issuePurpose==3 || issuePurpose==8) return;
	
	var purpose = $("#cbo_issue_purpose").val();
	var receive_basis=$('#cbo_basis').val();
	var booking_no=$('#txt_booking_no').val();
	var cbo_company_id = $('#cbo_company_name').val();
 	var save_data = $("#save_data").val();
	var all_po_id = $("#all_po_id").val();
	var prod_id = $("#hiddenProdId").val();
	var issueQnty = $('#txtReqQnty').val();
	//if( (issueQnty=="" || issueQnty==0) && issueQnty*1==$('#txtReqQnty').val()*1 )  issueQnty = $('#txtReqQnty').val();	
	var distribution_method = $('#distribution_method_id').val();
	
	if(form_validation('cbo_company_name*cbo_basis*cbo_issue_purpose*txtItemDescription','Company*Basis*Issue Purpose*Item Description')==false)
	{
		return;
	}  
	else if(receive_basis==1 && (purpose==11 || purpose==4) )
	{ 
		if( form_validation('txt_booking_no','Booking')==false )
			return;
	}
	var title = 'PO Info';	
	var page_link = 'requires/grey_fabric_issue_controller.php?receive_basis='+receive_basis+'&cbo_company_id='+cbo_company_id+'&booking_no='+booking_no+'&all_po_id='+all_po_id+'&save_data='+save_data+'&issueQnty='+issueQnty+'&distribution_method='+distribution_method+'&isRoll='+isRoll+'&prod_id='+prod_id+'&action=po_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var save_string=this.contentDoc.getElementById("save_string").value;	 
		var tot_issue_qnty=this.contentDoc.getElementById("tot_grey_qnty").value;  //this is issue qnty 
 		var all_po_id=this.contentDoc.getElementById("all_po_id").value;  
		var distribution_method=this.contentDoc.getElementById("distribution_method").value;
		
		$('#save_data').val(save_string);
		$('#txtIssueQnty').val(tot_issue_qnty);
		$('#txtReqQnty').val(tot_issue_qnty);
 		$('#all_po_id').val(all_po_id);
		$('#distribution_method_id').val(distribution_method);
		var prod_id = $('#hiddenProdId').val();
		
		if(receive_basis==2)
		{
			get_php_form_data(all_po_id+"**"+prod_id, "populate_data_about_order", "requires/grey_fabric_issue_controller" );
		}
		
		load_drop_down( 'requires/grey_fabric_issue_controller', all_po_id+'_'+$('#cbo_issue_purpose').val(), 'load_drop_down_color', 'color_td' );
	}
}


function fnc_grey_fabric_issue_entry(operation)
{
	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_name').val()+'*'+$('#hidden_system_id').val()+'*'+report_title, "grey_fabric_issue_print", "requires/grey_fabric_issue_controller" ) 
		return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
		if( form_validation('cbo_company_name*txt_issue_date*cbo_basis*cbo_issue_purpose*cbo_store_name*txtItemDescription*txtIssueQnty','Company Name*Issue Date*Basis*Issue Purpose*Store Name*Item Description*Issue Quantity')==false )
		{
			return;
		}
		if( $("#txtIssueQnty").val()*1 > $("#txt_yet_to_issue").val()*1+$("#hiddenIssueQnty").val()*1) 
		{
			alert("Issue Quantity Exceded The Current Stock");
			return;
		}
		var dataString = "txt_system_no*hidden_system_id*cbo_company_name*hidden_is_roll_maintain*hidden_is_batch_maintain*txt_issue_date*cbo_basis*cbo_issue_purpose*cbo_dyeing_source*cbo_dyeing_company*txt_booking_no*txt_booking_id*txt_batch_no*txt_batch_id*cbo_buyer_name*txt_challan_no*txt_style_ref*hidden_order_id*cbo_store_name*txtNoOfRoll*txtRollNo*txtRollPOid*txtRollPOQnty*txtItemDescription*hiddenProdId*txtIssueQnty*save_data*all_po_id*distribution_method_id*txtYarnLot*cbo_color_id*cbo_yarn_count*dtls_tbl_id*trans_tbl_id*txt_remarks";
		//alert (dataString);
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
		freeze_window(operation);
		http.open("POST","requires/grey_fabric_issue_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_grey_fabric_issue_entry_reponse;
	}
}

function fnc_grey_fabric_issue_entry_reponse()
{	
	if(http.readyState == 4) 
	{	  		
		var reponse=trim(http.responseText).split('**');	
 		release_freezing();
		if(reponse[0]*1==20*1)
		{
			alert(reponse[1]);return;
		}
		else if(reponse[0]==10 || reponse[0]==15)
		{
			show_msg(reponse[0]);
			return;
		}
		else if(reponse[0]==0) //insert
		{
 			show_msg(reponse[0]);
			$("#txt_system_no").val(reponse[1]); 
			$('#hidden_system_id').val(reponse[2]);						
		}	
		else if(reponse[0]==1) //update
		{
			show_msg(reponse[0]);			
		}		 	
 		
		show_list_view(reponse[2],'show_dtls_list_view','list_view_container','requires/grey_fabric_issue_controller','');
		set_button_status(0, permission, 'fnc_grey_fabric_issue_entry',1,1);
		//after save reset child form
		$("#color_td").html('<? echo create_drop_down( "cbo_color_id", 170, $blank_array,"", 1, "-- Select Color --", $selected, "","","" ); ?>');
		$("#child_tbl").find('input,select').val('');
		$("#display").find('input,select').val('');
		release_freezing();
	}
}

function open_mrrpopup()
{
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	
	var company = $("#cbo_company_name").val();	
	var page_link='requires/grey_fabric_issue_controller.php?action=mrr_popup&company='+company; 
	var title="Search MRR Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=0,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var sysNumber=this.contentDoc.getElementById("hidden_sys_number").value; // system number
 		
		$("#txt_system_no").val(sysNumber);		
		// master part call here
		get_php_form_data(sysNumber, "populate_data_from_data", "requires/grey_fabric_issue_controller");	 
		//list view call here
		show_list_view($("#hidden_system_id").val(),'show_dtls_list_view','list_view_container','requires/grey_fabric_issue_controller','');
 		$("#child_tbl").find('input,select').val('');
		$("#display").find('input,select').val('');
		$("#color_td").html('<? echo create_drop_down( "cbo_color_id", 170, $blank_array,"", 1, "-- Select Color --", $selected, "","","" ); ?>');
		set_button_status(0, permission, 'fnc_grey_fabric_issue_entry',1,1);
		enable_disable();
  	}
}

//form reset/refresh function here
function fnResetForm()
{ 
	//disable_enable_fields( 'cbo_company_name*cbo_basis*cbo_receive_purpose*cbo_store_name', 0, "", "" );
 	set_button_status(0, permission, 'fnc_grey_fabric_issue_entry',1,0);
	reset_form('grey_issue_1','list_view_container','','','','');
	$("#color_td").html('<? echo create_drop_down( "cbo_color_id", 170, $blank_array,"", 1, "-- Select Color --", $selected, "","","" ); ?>');
	$("#cbo_issue_purpose").val(11);
	enable_disable();
}

$(document).ready(function(e) {
    $("#cbo_issue_purpose").val(11); //default set issue purpose fabric dyeing 
	enable_disable();
});

</script>
</head>

<body onLoad="set_hotkey()">
	<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
    <form name="grey_issue_1" id="grey_issue_1" autocomplete="off" > 
    	<div style="width:100%;" align="center">  
            <fieldset style="width:1000px;">
                <legend>Grey Fabric Issue</legend>
                    <br />
                       
                   <!-- ========================== Master table start ============================ -->     
                       <fieldset style="width:950px;">                                       
                            <table  width="950" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                                <tr>
                                    <td colspan="6" align="center"><b>Issue No&nbsp;</b>
                                        <input type="text" name="txt_system_no" id="txt_system_no" class="text_boxes" style="width:160px" placeholder="Double Click To Search" onDblClick="open_mrrpopup()" readonly />&nbsp;&nbsp;
                                    	<input type="hidden" id="hidden_system_id" /> 
                                    </td>
                               </tr>
                               <tr>
                                    <td colspan="6" align="center">&nbsp;</td>
                               </tr>
                               <tr>
                                    <td  width="120" align="right" class="must_entry_caption">Company Name </td>
                                    <td width="170">
                                        <?  		 
                                         echo create_drop_down( "cbo_company_name", 170, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "get_php_form_data(this.value, 'is_roll_maintain', 'requires/grey_fabric_issue_controller');reset_form('','','txt_booking_no*txt_batch_no*cbo_buyer_name*txt_order_no','','','');enable_disable();load_drop_down( 'requires/grey_fabric_issue_controller', this.value, 'load_drop_down_store', 'store_td' );$('#child_tbl').find('input,select').val('');" );
                                        ?>
                                        
                                        <!-- hiden field for check start-->
                                        <input type="hidden" id="hidden_is_roll_maintain" >
                                        <input type="hidden" id="hidden_is_batch_maintain" >
                                        <!-- hiden field for check end -->
                                        
                                    </td>
                                    <td width="120" align="right" class="must_entry_caption">Issue Date</td>
                                    <td width="160"><input type="text" name="txt_issue_date" id="txt_issue_date" class="datepicker" style="width:160px;" placeholder="Select Date" readonly /></td>
                                    <td width="120" align="right" class="must_entry_caption">Issue Basis</td>
                                    <td width="" id="issue_purpose_td"><? 
                                            echo create_drop_down( "cbo_basis", 170, $issue_basis,"", 1, "-- Select Basis --", $selected, "reset_form('','','txt_booking_id*txt_booking_no*txt_batch_no*txt_batch_id*cbo_buyer_name*txt_order_no*hidden_order_id*txtReqQnty*hiddenIssueQnty*save_data*txtIssueQnty*all_po_id*distribution_method_id*txt_fabric_received*txt_cumulative_issued*txt_yet_to_issue*hidden_yet_issue_qnty','','','');enable_disable();", "", "1,2");
                                        ?>
                                    </td>
                                </tr>
                                <tr>                           
                                    <td  width="120" align="right" class="must_entry_caption">Issue Purpose </td>
                                    <td width="170"><? 
                                         echo create_drop_down( "cbo_issue_purpose", 170, $yarn_issue_purpose,"", 1, "-- Select Purpose --", $selected, "reset_form('','','txt_booking_no*txt_batch_no*cbo_buyer_name*txt_order_no','','','');enable_disable()","","11,3,4,8" );
                                        ?></td>
                                    <td width="120" align="right" >Dyeing Source</td>
                                    <td width="160"><?
                                        echo create_drop_down( "cbo_dyeing_source", 170, $knitting_source, "", 1, "-- Select --", $selected, "load_drop_down( 'requires/grey_fabric_issue_controller', this.value+'**'+$('#cbo_company_name').val()+'**'+$('#cbo_issue_purpose').val(), 'load_drop_down_knit_com', 'dyeing_company_td' );","","1,3" );
                                    ?></td>
                                    <td width="120" align="right">Dyeing Company</td>
                                    <td width="" id="dyeing_company_td">
										<?
                                        	echo create_drop_down( "cbo_dyeing_company", 170, $blank_array,"", 1, "-- Select --", $selected, "","","" );
                                    	?>
                                	</td>
                                </tr>
                                <tr>                          
                                    <td width="120" align="right" id="knit_source">Fab Booking No</td>
                                    <td width="170">
                                        <input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:160px"  placeholder="Double Click to Search" onDblClick="popuppage_fabbook();" readonly />
                                        <input type="hidden" name="txt_booking_id" id="txt_booking_id" />
                                    </td>
                                    <td width="120" align="right"> Batch Number</td>
                                    <td width="160">
                                        <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:160px" placeholder="Display" onDblClick="openpopup_batch()" />
                                        <input type="hidden" id="txt_batch_id" />
                                    </td>
                                    <td width="120" align="right">Buyer Name</td>
                                    <td width="" id="supplier"><? 
                                            echo create_drop_down( "cbo_buyer_name", 170, "select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                   <td  width="120" align="right" >Challan No</td>
                                   <td width="170">
                                        <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:160px" placeholder="Entry" >
                                   </td>
                                   <td width="120" align="right" >Style Referece</td>
                                   <td width="160"><input  type="text" name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:160px"  readonly placeholder="Display" /></td>
                                   <td width="120" align="right">&nbsp;</td>
                                   <td width="">&nbsp;</td>                                   
                              	</tr>
                                <tr>
                                    <td align="right">Order Numbers</td>
                                    <td colspan="5">
                                        <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:450px" readonly placeholder="Display" />
                                        <input type="hidden" id="hidden_order_id" />
                                    </td>
                                 </tr>
                                 <tr>
                                    <td align="right">&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td align="right">&nbsp;</td>
                                    <td>&nbsp;</td>
                                 </tr> 
                            </table>
                        </fieldset> 
              <fieldset style="width:450px; margin-left:30px; position:relative; float:left">  
                <legend>Issued New  Item</legend>                                     
                      <table width="400" cellspacing="2" cellpadding="0" border="0" id="child_tbl" >                           
                            <tr>                                
                            	 <td width="130" class="must_entry_caption">Store Name</td>
                                 <td id="store_td"><? 
                                        echo create_drop_down( "cbo_store_name", 170, "select id,store_name from lib_store_location where status_active=1 and is_deleted=0 and FIND_IN_SET(2,item_category_id) order by store_name","id,store_name", 1, "-- Select Store --", $storeName, "" );
                                        ?>
                                 </td>
                            </tr>
                            <tr>                                
                            	 <td width="">No Of Roll</td>
                                 <td width="">
                                 	<input  type="text" name="txtNoOfRoll" id="txtNoOfRoll" class="text_boxes_numeric" style="width:160px" onDblClick="openroll_popup()" readonly />
                                 	<!-- hidden field for roll table entry very very important------>
                                    <input type="hidden" name="txtRollNo" id="txtRollNo" value="<? echo $row[csf('roll_no')]; ?>" readonly disabled />
                                    <input type="hidden" name="txtRollPOid" id="txtRollPOid" value="<? echo $row[csf('roll_po_id')]; ?>" readonly disabled />
                                    <input type="hidden" name="txtRollPOQnty" id="txtRollPOQnty" value="<? echo $row[csf('roll_wise_issue_qnty')]; ?>" readonly disabled />
                                    <!----------------------------- end --------------------------->
                                 </td>
                            </tr> 
                            <tr>
                                <td class="must_entry_caption">Item Description</td>
                                <td>
                                	<input  type="text" name="txtItemDescription" id="txtItemDescription" class="text_boxes" style="width:280px" onDblClick="openDescription_popup()" />
                                    <input type="hidden" name="hiddenProdId" id="hiddenProdId" />
                                 </td>
                            </tr>
                            <tr>
                                <td class="must_entry_caption">Issue Quantity</td>
                                <td>
                                	<input  type="hidden" name="txtReqQnty" id="txtReqQnty" class="text_boxes_numeric" />
                                    <input  type="hidden" name="hiddenIssueQnty" id="hiddenIssueQnty" class="text_boxes_numeric" />
                                    <input  type="text" name="txtIssueQnty" id="txtIssueQnty" class="text_boxes_numeric" style="width:160px"  onDblClick="issueQntyPopup()" />
                                </td>
                            </tr>
                            <tr>
                                <td>Fabric Color</td>
                                <td id="color_td">
									<? 
										echo create_drop_down( "cbo_color_id", 170, $blank_array,"", 1, "-- Select Color --", $selected, "","","" );
									?>
                                 </td>
                            </tr>
                            <tr>
                                <td>Yarn Lot</td>
                                <td><input  type="text" name="txtYarnLot" id="txtYarnLot" class="text_boxes" style="width:160px" placeholder="Display" readonly disabled /></td>
                            </tr>
                            <tr>
                                <td>Yarn Count</td>
                                <td>
                                	<?
										echo create_drop_down( "cbo_yarn_count", 170, "select id,yarn_count from lib_yarn_count where is_deleted = 0 AND status_active = 1 ORDER BY yarn_count ASC","id,yarn_count", 0, "--Select--", "", "",1 );
									?>
                                    	<!-- important hidden field --> 
                                             <input type="hidden" name="save_data" id="save_data" readonly  />	
                                             <input type="hidden" name="all_po_id" id="all_po_id" readonly />
                                             <input type="hidden" name="distribution_method_id" id="distribution_method_id" readonly />
                                        <!-- important hidden field -->
                                </td>
                            </tr> 
                            <tr>
                                <td>Remarks</td>
                                <td><input  type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:280px"  /></td>
                            </tr>
                      </table>
                      
                       
                        
                </fieldset>
                
             <fieldset style="width:450px; float:left; margin-left:30px">  
                <legend>Display</legend>                                     
                      <table  width="350" cellspacing="2" cellpadding="0" border="0" id="display" >                           
                            <tr>
                                  <td>Fabric Received</td>
                                  <td width="100"><input  type="text" name="txt_fabric_received" id="txt_fabric_received" class="text_boxes" style="width:160px" readonly disabled  /></td>
                            </tr>                        
                            <tr>
                                <td>Cumulative Issued</td>
                                <td><input  type="text" name="txt_cumulative_issued" id="txt_cumulative_issued" class="text_boxes" style="width:160px"  readonly disabled /></td>
                            </tr>
                            <tr>
                                  <td>Yet to Issue</td>
                                  <td width="100">
                                  	<input  type="text" name="txt_yet_to_issue" id="txt_yet_to_issue" class="text_boxes" style="width:160px"  readonly disabled />
                                    <input type="hidden" id="hidden_yet_issue_qnty" readonly disabled  />
                                  </td>
                            </tr> 
                      </table>
                      
                </fieldset>   
                <div style="clear:both"></div>
                   <!-- ========================== Master table end ============================ -->     
                    
                    
                   <!-- ========================== Child table start ============================ -->                                      
                    <table cellpadding="0" cellspacing="1" width="100%">
                        <tr> 
                           <td colspan="6" align="center"></td>				
                        </tr>
                        <tr>
                            <td align="center" colspan="6" valign="middle" class="button_container">
                                 <!-- details table id for update -->                             
                                 <input type="hidden" id="dtls_tbl_id" name="dtls_tbl_id" readonly />
                                 <input type="hidden" id="trans_tbl_id" name="trans_tbl_id" readonly />
                                 <input type="hidden" id="update_id" name="update_id" readonly />
                                 <!-- -->
                                 <? echo load_submit_buttons( $permission, "fnc_grey_fabric_issue_entry", 0,1,"fnResetForm()",1);?>
                            </td>
                       </tr> 
                    </table>                 
                    </fieldset>              	
                  <!-- ========================== Child table end ============================ -->   

    			<div style="width:990px; margin-top:5px" id="list_view_container"></div>

    		</div>
		</form>
	</div>    
</body>  
<script>
	set_multiselect('cbo_yarn_count','0','0','','0');
	disable_enable_fields('show_textcbo_yarn_count','1','','');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
