<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Grey Fabric Issue Return Entry
				
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	22-11-2014
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
echo load_html_head_contents("Grey Fabric Issue Return Info","../../", 1, 1, $unicode,1,1); 
?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";




function new_item_controll()
{
	var issuePurpose=$("#cbo_issue_purpose").val();
	if(issuePurpose==11 || issuePurpose==4)
	{
		$("#txt_return_qnty").attr("placeholder","Double Click"); 
		$("#txt_return_qnty").attr("readonly","readonly");
	}
	else
	{
		$("#txt_return_qnty").removeAttr("placeholder").attr("placeholder","Wirte"); 
		$("#txt_return_qnty").removeAttr("readonly");
	}
}


function active_inactive(str)
{
	$("#txt_booking_no").val('');
	$("#txt_booking_id").val('');
	$("#txt_item_description").val('');
	$("#txt_prod_id").val('');
	$("#txt_supplier_id").val('');
	$("#txt_issue_id").val('');
	$("#txt_return_qnty").val('');
	$("#tbl_child").find('select,input').val('');	
	
 	if(str==1 || str==3)
	{
		disable_enable_fields( 'txt_booking_no', 0, "", "" ); // disable false
 	}
	else
	{		
		disable_enable_fields( 'txt_booking_no', 1, "", "" ); // disable true
	}
}

function return_qnty_basis(purpose)
{
	var basis = parseInt($("#cbo_basis").val());
	
	$("#txt_return_qnty").val('');
		
	if(purpose==3 || purpose==8)
	{
		$("#txt_return_qnty").attr('placeholder','Entry');
		$("#txt_return_qnty").removeAttr('ondblclick');
 		$("#txt_return_qnty").removeAttr('readOnly');
	}
	else 
	{
 		$("#txt_return_qnty").attr('placeholder','Double Click');
		$("#txt_return_qnty").attr('ondblclick','openmypage_rtn_qty()');
		$("#txt_return_qnty").attr('readOnly',true);
	}
}

function popuppage_fabbook()
{
	if( form_validation('cbo_company_id*cbo_basis*cbo_issue_purpose','Company Name*Issue Basis*Issue Purpose')==false )
	{
		return;
	}
	var company			= $("#cbo_company_id").val();
	var cbo_basis	 	= $("#cbo_basis").val();
	var issue_purpose	= $("#cbo_issue_purpose").val();
	var page_link='requires/grey_fabric_issue_rtn_controller.php?action=fabbook_popup&company='+company+'&cbo_basis='+cbo_basis+'&issue_purpose='+issue_purpose;
	var title="Booking Information";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px, height=400px, center=1, resize=0, scrolling=0','../')
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
			
			/*if(bookingNumber[5]!="")
			{
				txt_issue_id
				get_php_form_data(bookingNumber[5], "populate_display_from_data", "requires/grey_fabric_issue_rtn_controller");	
			}*/
			
			if(issue_purpose==8)
			{
				load_drop_down( 'requires/grey_fabric_issue_rtn_controller', bookingNumber[1]+'_'+$('#cbo_issue_purpose').val(), 'load_drop_down_color', 'color_td' );
			}
			
			release_freezing();	 
		}
	}		
}



function openmypage_rtn_qty() // issue quantity
{
	var cbo_company_id = $('#cbo_company_id').val();
	var txt_issue_id = $('#txt_issue_id').val();
	var txt_prod_id = $('#txt_prod_id').val();
	var update_id = $('#update_id').val();
	var txt_return_qnty = $('#txt_return_qnty').val();
	var txt_break_qnty = $('#txt_break_qnty').val();
	var txt_break_roll = $('#txt_break_roll').val();
	var distribution_method = $('#distribution_method_id').val();

	var store_id = $('#cbo_store_name').val();
	var floor_id = $('#cbo_floor').val();
	var room_id = $('#cbo_room').val();
	var rack_id = $('#txt_rack').val();
	var self_id = $('#txt_shelf').val();
	
	if (form_validation('cbo_company_id*txt_issue_id*txt_prod_id','Company*Issue*Item Description')==false)
	{
		return;
	}
	var title = 'Issue Return Info';	
	var page_link = 'requires/grey_fabric_issue_rtn_controller.php?cbo_company_id='+cbo_company_id+'&txt_issue_id='+txt_issue_id+'&txt_prod_id='+txt_prod_id+'&txt_return_qnty='+txt_return_qnty+'&prev_distribution_method='+distribution_method+'&txt_break_qnty='+txt_break_qnty+'&txt_break_roll='+txt_break_roll+'&update_id='+update_id+'&store_id='+store_id+'&floor_id='+floor_id+'&room_id='+room_id+'&rack_id='+rack_id+'&self_id='+self_id+'&action=return_po_popup';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=390px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var tot_qnty=this.contentDoc.getElementById("tot_qnty").value;	 //Access form field with id="emailfield"
		var break_qnty=this.contentDoc.getElementById("break_qnty").value; //Access form field with id="emailfield"
		var break_roll=this.contentDoc.getElementById("break_roll").value; //Access form field with id="emailfield"
		var break_order_id=this.contentDoc.getElementById("break_order_id").value; //Access form field with id="emailfield"
		var distribution_method=this.contentDoc.getElementById("distribution_method").value;
		//alert(tot_qnty);return;
		
		$('#txt_return_qnty').val(tot_qnty);
		$('#txt_break_qnty').val(break_qnty);
		$('#txt_break_roll').val(break_roll);
		$('#txt_order_id_all').val(break_order_id);
		$('#distribution_method_id').val(distribution_method);

		var amount=$('#txt_hdn_consRate').val()*tot_qnty;
		$('#txt_amount').val(number_format(amount,2,'.' , ""));
	}
}


function open_itemdesc()
{
	if( form_validation('cbo_company_id*cbo_basis*cbo_issue_purpose*txt_booking_no','Company Name*Basis*Issue Purpose*Booking No')==false )
	{
		return;
	}
	var company = $("#cbo_company_id").val();	
	var basis = $("#cbo_basis").val();
	var booking_no = $("#txt_booking_no").val(); 
	var booking_id = $("#txt_booking_id").val(); 
 	var page_link='requires/grey_fabric_issue_rtn_controller.php?action=itemdesc_popup&company='+company+'&booking_no='+booking_no+'&basis='+basis+'&booking_id='+booking_id; 
	var title="Search Item Description";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=400px,center=1,resize=0,scrolling=0',' ')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var ref_prod=this.contentDoc.getElementById("hidden_recv_number").value; // mrr number
  		// master part call here
		get_php_form_data(ref_prod, "populate_data_from_data", "requires/grey_fabric_issue_rtn_controller");  		
 	}
}
 

function fnc_yarn_issue_return_entry(operation)
{
	
	
	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_return_no').val()+'*'+report_title+'*'+$('#issue_mst_id').val()+'*'+$('#cbo_location').val(),'issue_return_print','requires/grey_fabric_issue_rtn_controller');
		return;
	}
	else if(operation==2)
	{
		show_msg('13');
		return;
	}
	else
	{
		if($("#is_posted_accout").val()==1)
		{
			alert("Already Posted In Accounting. Save Update Delete Restricted.");
			return;
		}
		if( form_validation('cbo_company_id*cbo_basis*cbo_issue_purpose*txt_return_date*txt_return_challan_no*txt_item_description*txt_return_qnty','Company Name*Basis*Issue Purpose*Return Date*Challan No*Item Description*Return Qnty')==false )
		{
			return;
		}	
		var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_return_date').val(), current_date)==false)
		{
			alert("Issue Return Date Can not Be Greater Than Today");
			return;
		}	
		if($("#txt_return_qnty").val()*1<=0)
		{
			alert("Return Quantity Should be Greater Than Zero(0).");
			return;
		}
		
		if($("#txt_return_qnty").val()*1>$("#txt_net_used").val()*1)
		{
			alert("Return Quantity Not Over Net Used.");
			return;
		}

		// Store upto validation start
		var store_update_upto=$('#store_update_upto').val()*1;
		var cbo_floor=$('#cbo_floor').val()*1;
		var cbo_room=$('#cbo_room').val()*1;
		var txt_rack=$('#txt_rack').val()*1;
		var txt_shelf=$('#txt_shelf').val()*1;
		//var cbo_bin=$('#cbo_bin').val()*1;
		
		if(store_update_upto > 1)
		{
			/*if(store_update_upto==6 && (cbo_floor==0 || cbo_room==0 || txt_rack==0 || txt_shelf==0 || cbo_bin==0))
			{
				alert("Up To Bin Value Full Fill Required For Inventory");return;
			}
			else */if(store_update_upto==5 && (cbo_floor==0 || cbo_room==0 || txt_rack==0 || txt_shelf==0))
			{
				alert("Up To Shelf Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==4 && (cbo_floor==0 || cbo_room==0 || txt_rack==0))
			{
				alert("Up To Rack Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==3 && (cbo_floor==0 || cbo_room==0))
			{
				alert("Up To Room Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==2 && cbo_floor==0)
			{
				alert("Up To Floor Value Full Fill Required For Inventory");return;
			}
		}
		// Store upto validation End
		
		var dataString = "txt_return_no*issue_mst_id*cbo_company_id*cbo_basis*cbo_issue_purpose*txt_booking_no*txt_booking_id*booking_without_order*cbo_location*txt_return_date*txt_return_challan_no*cbo_knitting_source*cbo_knitting_company*txt_item_description*txt_prod_id*txt_issue_id*cbo_uom*txt_return_qnty*txt_break_qnty*txt_break_roll*txt_order_id_all*prev_return_qnty*cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*txt_remarks*before_prod_id*update_id*txt_issue_challan_no*yarn_count*stitch_length*txt_yarn_lot*txt_hdn_consRate";
		
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
		freeze_window(operation);
		http.open("POST","requires/grey_fabric_issue_rtn_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_yarn_issue_return_entry_reponse;
	}
}

function fnc_yarn_issue_return_entry_reponse()
{	
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');		
		if(reponse[0]==20)
		{
			alert(reponse[1]);
			release_freezing();
			return;
		}
		show_msg(reponse[0]); 		
		if(reponse[0]==0 || reponse[0]==1)
		{
			$("#txt_return_no").val(reponse[1]);
			$("#issue_mst_id").val(reponse[2]);
 			disable_enable_fields( 'cbo_company_id', 1, "", "" ); // disable true
				
			show_list_view(reponse[2],'show_dtls_list_view','list_container_yarn','requires/grey_fabric_issue_rtn_controller','');		
			//child form reset here after save data-------------//
			$("#tbl_child").find('input,select').val('');
			set_button_status(0, permission, 'fnc_yarn_issue_return_entry',1,1);
			release_freezing();
		}
		release_freezing();
	}
}

function generate_report_file(data,action,page)
{
	window.open("requires/grey_fabric_issue_rtn_controller.php?data=" + data+'&action='+action, true );
}

function open_returnpopup()
{
	if( form_validation('cbo_company_id','Company Name')==false )
	{
		return;
	}
	var company = $("#cbo_company_id").val();	
	var page_link='requires/grey_fabric_issue_rtn_controller.php?action=return_number_popup&company='+company; 
	var title="Search Return Number Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=400px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var returnNumber=this.contentDoc.getElementById("hidden_return_number").value; // mrr number
		var posted_in_account=this.contentDoc.getElementById("hidden_posted_account").value; // mrr number
		
		$("#is_posted_accout").val(posted_in_account);
		if(posted_in_account==1) document.getElementById("accounting_posted_status").innerHTML="Already Posted In Accounting.";
		else 					 document.getElementById("accounting_posted_status").innerHTML="";
  		// master part call here
		get_php_form_data(returnNumber, "populate_master_from_data", "requires/grey_fabric_issue_rtn_controller");  		
		//list view call here
		show_list_view(returnNumber,'show_dtls_list_view','list_container_yarn','requires/grey_fabric_issue_rtn_controller','');
		disable_enable_fields( 'cbo_company_id', 1, "", "" ); // disable true
		set_button_status(0, permission, 'fnc_yarn_issue_return_entry',1,1);
 	}
}

//form reset/refresh function here
function fnResetForm()
{
	$("#tbl_master").find('input,select').attr("disabled", false);	
	set_button_status(0, permission, 'fnc_yarn_issue_return_entry',1,0);
	reset_form('grey_issue_rtn_1','list_container_yarn','','','','');
	document.getElementById("accounting_posted_status").innerHTML="";
}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
    <form name="grey_issue_rtn_1" id="grey_issue_rtn_1" autocomplete="off" > 
    <div style="width:100%;">       
    <table width="100%" cellpadding="0" cellspacing="2" align="center">
     	<tr>
        	<td width="80%" align="center" valign="top">   
            	<fieldset style="width:1000px;">
                <legend>Knit Grey Fabric Issue Return</legend>
                  <br />
                 	<fieldset style="width:900px;">                                       
                        <table  width="900" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                            <tr>
                           		<td colspan="3" align="right"><b>Return Number</b></td>
                           		<td colspan="3" align="left">
                                <input type="text" name="txt_return_no" id="txt_return_no" class="text_boxes" style="width:160px" placeholder="Double Click To Search" onDblClick="open_returnpopup()" readonly />
                                <input type="hidden" id="issue_mst_id" name="issue_mst_id" >
                                </td>
               		      </tr>
                          <tr>
                                <td  width="130" align="right" class="must_entry_caption">Company Name </td>
                                <td width="170">
									<? 
                                     	echo create_drop_down( "cbo_company_id", 170, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/grey_fabric_issue_rtn_controller', this.value, 'load_drop_down_location', 'location_td' );get_php_form_data(this.value,'varible_inventory','requires/grey_fabric_issue_rtn_controller' );" );
                                     	//load_room_rack_self_bin('requires/grey_fabric_issue_rtn_controller*13', 'store','store_td', this.value);
                                    ?>
                                </td>
                                <td width="130" align="right" class="must_entry_caption">Basis</td>
                                <td width="170">
                                	<? 
										$grey_issue_basis=array(1=>"Booking",2=>"Independent",3=>"Knitting Plan");
										echo create_drop_down( "cbo_basis", 170, $grey_issue_basis,"", 1, "-- Select Basis --", $selected, "active_inactive(this.value);", "", "1,3");
									?>
                                </td>
                                <td width="130" align="right" class="must_entry_caption">Issue Purpose </td>
                                <td >
                                <? 
                                	echo create_drop_down( "cbo_issue_purpose", 170, $yarn_issue_purpose,"", 1, "-- Select Purpose --", $selected, "reset_form('','','txt_booking_no*txt_item_description','','','');new_item_controll()","","11,3,4,8" );
                                ?>
                                </td>
                          </tr>
                          <tr>
                          		<td  align="right" >F.Booking/Prog.No</td>
                                <td >
                                	<input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:160px"  placeholder="Double Click to Search" onDblClick="popuppage_fabbook();" readonly disabled />
                                    <input type="hidden" name="txt_booking_id" id="txt_booking_id" />
                                    <input type="hidden" name="booking_without_order" id="booking_without_order"/>
                                </td>
                                <td  align="right">Location</td>
                                <td  id="location_td">
								<? 
                                   echo create_drop_down( "cbo_location", 170, $blank_array,"", 1, "-- Select Location --", "", "" );
                                ?>
                                </td>
                                
                                <td align="right" class="must_entry_caption">Return Date</td>
                                <td><input type="text" name="txt_return_date" id="txt_return_date" class="datepicker" style="width:160px;" placeholder="Select Date" /></td>
                                
                          </tr>
                          <tr>
                                
                                <td align="right" class="must_entry_caption">Return Challan</td>
                                <td><input type="text" name="txt_return_challan_no" id="txt_return_challan_no" class="text_boxes" style="width:160px" /></td>
                                <td align="right" >Return Source</td>
                                <td >
                                	<?
                                        echo create_drop_down( "cbo_knitting_source", 170, $knitting_source,"", 1, "-- Select --", $selected, "load_drop_down( 'requires/grey_fabric_issue_rtn_controller', this.value+'**'+$('#cbo_company_id').val(), 'load_drop_down_knit_com', 'knitting_company_td' );","","1,3" );
                                    ?>
                                </td>
                          		<td  align="right">Knitting Company</td>
                                <td  id="knitting_company_td">
                                    <?
                                        echo create_drop_down( "cbo_knitting_company", 170, $blank_array,"", 1, "-- Select --", $selected, "","","" );
                                    ?>	
                                </td>
                          </tr>
                          <tr>
                            <td align="right">&nbsp;</td>
                            <td>&nbsp;</td>
                            <td align="right" >&nbsp;</td>
                            <td>&nbsp;</td>
                            <td align="right">&nbsp;</td>
                            <td>&nbsp;</td>
                          </tr>
                        </table>
                    </fieldset>
                    <br />
                    <table cellpadding="0" cellspacing="1" width="96%" id="tbl_child">
                     <tr>
                   	   <td width="50%" valign="top" align="center">
                         <fieldset style="width:460px; float:left">  
                                <legend>Return Item Info</legend>                                     
                                  <table  width="450" cellspacing="2" cellpadding="0" border="0">
                                          <tr>
                                               <td width="110" align="right" class="must_entry_caption">Item Description&nbsp;</td>
                                               <td colspan="3">
                                               		<input class="text_boxes" type="text" name="txt_item_description" id="txt_item_description" style="width:300px;" placeholder="Double Click To Search" onDblClick="open_itemdesc()" readonly  />
                                                    <input type="hidden" id="txt_prod_id" name="txt_prod_id" />
                                    				<input type="hidden" id="txt_issue_id" name="txt_issue_id" />
                                               </td> 
                                          </tr>
                                          <tr>
                                               <td width="110" align="right">Yarn Lot&nbsp;</td>
                                               <td width="158">
                                               <input class="text_boxes" type="text" name="txt_yarn_lot" id="txt_yarn_lot" style="width:150px;" placeholder="Display" readonly  />
                                               <input type="hidden" id="yarn_count" name="yarn_count" >
                                               <input type="hidden" id="stitch_length" name="stitch_length" >
                                               </td>
                                                <td align="right" width="41">Store</td>
                                                <td id="store_td">
                                                	<? 
                                                	//echo create_drop_down( "cbo_store_name", 100, $blank_array,"", 1, "-- Select --", $storeName, "" );
                                                	?>
                                                	<input class="text_boxes" type="text" id="cbo_store_name_show" name="cbo_store_name_show" width="100">
                                                	<input type="hidden" id="cbo_store_name" name="cbo_store_name" width="100">
                                                </td>
                                          </tr>
                                           <tr>
                                          	 <td width="41" align="right">UOM</td>
                                               <td width="131"><? echo create_drop_down( "cbo_uom", 160, $unit_of_measurement,"", 1, "Display", 0, "",1 ); ?></td>
                                               <td align="right" width="41" >Floor</td>
											<td id="floor_td">
												<? //echo create_drop_down( "cbo_floor", 100,"","", 1, "--Select--", 0, "",0 ); ?>
												<input class="text_boxes" type="text" id="cbo_floor_show" name="cbo_floor_show" width="100">
                                                <input type="hidden" id="cbo_floor" name="cbo_floor" width="100">
											</td>
                                          </tr>
                                          <tr>
                                               <td width="110" align="right" class="must_entry_caption">Returned Qnty&nbsp;</td>
                                               <td>
                                               <input class="text_boxes_numeric" type="text" name="txt_return_qnty" id="txt_return_qnty" style="width:150px;" placeholder="Double Click To Search" readonly onDblClick="openmypage_rtn_qty()"   />
                                               <input type="hidden" id="txt_break_qnty" name="txt_break_qnty" > 
                                               <input type="hidden" id="txt_break_roll" name="txt_break_roll" >
                                               <input type="hidden" id="txt_order_id_all" name="txt_order_id_all" >
                                               <input type="hidden" id="prev_return_qnty" name="prev_return_qnty" >
                                               </td>
                                               <input type="hidden" name="distribution_method_id" id="distribution_method_id" />

                                              <td align="right" width="41">Room</td>
												<td id="room_td">
												<? //echo create_drop_down( "cbo_room", 100,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
												<input class="text_boxes" type="text" id="cbo_room_show" name="cbo_room_show" width="100">
                                                <input type="hidden" id="cbo_room" name="cbo_room" width="100">
											  </td>
                                          </tr>
                                          <tr>
											 
											<td></td>
                       						<td></td>
											<td align="right" width="41">Rack</td>
											<td id="rack_td">
												<? //echo create_drop_down( "txt_rack", 100,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
												<input class="text_boxes" type="text" id="txt_rack_show" name="txt_rack_show" width="100">
                                                <input type="hidden" id="txt_rack" name="txt_rack" width="100">
											</td>
                                          </tr>
                                          <tr>
                       						<td></td>
                       						<td></td>
                       						<td align="right" width="41">Shelf</td>
											<td id="shelf_td">
												<? //echo create_drop_down( "txt_shelf", 100,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
												<input class="text_boxes" type="text" id="txt_shelf_show" name="txt_shelf_show" width="100">
                                                <input type="hidden" id="txt_shelf" name="txt_shelf" width="100">
											</td>
                       					</tr>
                                         
                                          <tr>
                                               <td width="110" align="right">Remarks&nbsp;</td>
                                               <td colspan="3"><input class="text_boxes" type="text" name="txt_remarks" id="txt_remarks" style="width:300px;" placeholder="Entry"  /></td>
                                          </tr>
                                   </table>
                            </fieldset>
                       	 <fieldset style="width:460px; float:left; margin-left:5px">  
                           <legend>Display</legend>                                     
                                  <table  width="450" cellspacing="2" cellpadding="0" border="0" id="display_table" >
                                           <tr>
                                              <td width="110" align="right">Issue Qnty&nbsp;</td>
                                              <td width="100"><input class="text_boxes" type="text" name="txt_issue_qnty" id="txt_issue_qnty" style="width:100px;" placeholder="Display" readonly  /></td>
                                              <td width="120" align="right">Rate&nbsp;</td>
                                              <td width="100"><input class="text_boxes" type="text" name="txt_rate" id="txt_rate" style="width:100px;" placeholder="Display" readonly  /></td> 
                                              <input type="hidden" id="txt_hdn_consRate" name="txt_hdn_consRate">
                                          </tr>
                                          <tr>
                                              <td align="right">Total Return&nbsp;</td>
                                              <td>
                                              		<input class="text_boxes" type="hidden" name="txt_total_return" id="txt_total_return" style="width:100px;" placeholder="Display" readonly  />
                                                    <input class="text_boxes" type="text" name="txt_total_return_display" id="txt_total_return_display" style="width:100px;" placeholder="Display" readonly  />
                                              </td>
                                              <td align="right">Amount&nbsp;&nbsp;</td>
                                              <td><input class="text_boxes" type="text" name="txt_amount" id="txt_amount" style="width:100px;" placeholder="Display" readonly /></td>                               			  </tr>
                                          <tr>
                                              <td align="right">Net Used&nbsp;</td>
                                              <td>
                                              	<input class="text_boxes" type="text" name="txt_net_used" id="txt_net_used" style="width:100px;" placeholder="Display" readonly />
                                                <input class="text_boxes" type="hidden" name="hide_net_used" id="hide_net_used" readonly />
                                              </td>
                                              <td align="right">Issue Challan No&nbsp;</td>
                                              <td><input class="text_boxes" type="text" name="txt_issue_challan_no" id="txt_issue_challan_no" style="width:100px;" placeholder="Display" readonly  /></td>                                    
                                          </tr>
                                           <tr>
                                              <td align="right">Rack&nbsp;</td>
                                              <td>
                                              	<input class="text_boxes" type="text" name="txt_rack_issue" id="txt_rack_issue" style="width:100px;" placeholder="Display" readonly />
                                              </td>
                                              <td align="right">Self&nbsp;</td>
                                              <td><input class="text_boxes_numeric" type="text" name="txt_self_issue" id="txt_self_issue" style="width:100px;" placeholder="Display" readonly  /></td>                                    
                                          </tr>
                                          
                                          <tr>
                                            <td align="right">&nbsp;</td>
                                            <td colspan="2">&nbsp;</td>
                                            <td>&nbsp;</td>
                                          </tr>  
                                  </table>
                            </fieldset>
                   	   </td>
                    </tr>
                </table>                
               	<table cellpadding="0" cellspacing="1" width="100%">
                	<tr> 
                       <td colspan="6" align="center"></td>				
                	</tr>
                	<tr>
                        <td align="center" colspan="6" valign="middle" class="button_container">
                             <!-- details table id for update -->
                             <input type="hidden" id="before_prod_id" name="before_prod_id" value="" />
                             <input type="hidden" id="update_id" name="update_id" value="" />
 							 <input type="hidden" name="is_posted_accout" id="is_posted_accout"/>
 							 <input type="hidden" name="store_update_upto" id="store_update_upto"/>
                             <!-- -->
							 <? echo load_submit_buttons( $permission, "fnc_yarn_issue_return_entry", 0,1,"fnResetForm()",1);?>
                              <div id="accounting_posted_status" style=" color:red; font-size:24px;" align="left"></div>
                       
                        </td>
                   </tr> 
                </table>                 
              	</fieldset>
              	<fieldset style="width:1000px;">
    			<div style="width:990px;" id="list_container_yarn"></div>
    		  	</fieldset>
           </td>
         </tr>
    </table>
    </div>
    </form>
</div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
