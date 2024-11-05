<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create General Store Issue Return Entry
				
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	03-12-2013
Updated by 		: 	Kausar	(Creating Report)	
Update date		: 	11-12-2013	   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//========== user credential start ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id,store_location_id,item_cate_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];

if ($company_id !='') {
    $company_credential_cond = "and comp.id in($company_id)";
}
if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)"; 
}
//========== user credential end ==========

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Yarn Receive Info","../../", 1, 1, $unicode,1,1); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
<?
$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][27] );
echo "var field_level_data= ". $data_arr . ";\n";
?>
function open_itemdesc()
{
	if( form_validation('cbo_company_id*cbo_location','Company Name*Location')==false )
	{
		return;
	}
	
	var company = $("#cbo_company_id").val();	
	var location = $("#cbo_location").val();	
	//var page_link='requires/general_item_issue_return_controller.php?action=return_number_popup&company='+company; 
 	var page_link='requires/general_item_issue_return_controller.php?action=itemdesc_popup&company='+company+"&location="+location; 
	var title="Search Item Description";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1110px,height=400px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var mrrNumber=this.contentDoc.getElementById("hidden_recv_number").value.split("_");//alert(mrrNumber); // mrr number
		var issue_rtn_qty = return_global_ajax_value(mrrNumber[1]+"**"+mrrNumber[13], 'issue_rtn_qty', '', 'requires/general_item_issue_return_controller');
		$('#txt_item_description').val(mrrNumber[0]);
		$('#txt_prod_id').val(mrrNumber[1]);
		$('#txt_issue_id').val(mrrNumber[13]);
		$('#cbo_uom').val(mrrNumber[2]);
		//alert(mrrNumber[3]+"="+issue_rtn_qty);
		$('#total_issue').val((mrrNumber[3]*1)-(issue_rtn_qty*1));
		$('#cbo_item_category').val(mrrNumber[4]);
		$('#cbo_item_group').val(mrrNumber[5]);
		$('#txt_avrage_rate').val(mrrNumber[6]);
		$('#txt_lot').val(mrrNumber[14]);
        $('#txt_issue_qnty').val(mrrNumber[15] - (issue_rtn_qty*1));
		
		var store = mrrNumber[7]; var floor = mrrNumber[8]; var room = mrrNumber[9]; var rack = mrrNumber[10]; 
		var self = mrrNumber[11]; var bin = mrrNumber[12]; 
		get_php_form_data(company+'*'+location+'*'+store+'*'+floor+'*'+room+'*'+rack+'*'+self+'*'+bin, "room_rack_self_bin_from_data", "requires/general_item_issue_return_controller");  
 	}
}
 
// ==============End Floor Room Rack Shelf Bin disable============
function storeUpdateUptoDisable() 
{	
	$('#cbo_store_name').prop("disabled", true);
	$('#cbo_floor').prop("disabled", true);
	$('#cbo_room').prop("disabled", true);
	$('#txt_rack').prop("disabled", true);
	$('#txt_shelf').prop("disabled", true);	
	$('#cbo_bin').prop("disabled", true);
}
// ==============End Floor Room Rack Shelf Bin disable============

function open_returnpopup()
{
	if( form_validation('cbo_company_id','Company Name')==false )
	{
		return;
	}
	var company = $("#cbo_company_id").val();	
	var page_link='requires/general_item_issue_return_controller.php?action=return_number_popup&company='+company; 
	var title="Search Return Number Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=400px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var returnNumber=this.contentDoc.getElementById("hidden_return_number").value.split("_"); // mrr number
  		// master part call here
		var posted_in_account=returnNumber[2]; // is posted accounct
		$("#is_posted_account").val(posted_in_account);
		if(posted_in_account==1) document.getElementById("accounting_posted_status").innerHTML="Already Posted In Accounting.";
		else 					 document.getElementById("accounting_posted_status").innerHTML="";
		
		get_php_form_data(returnNumber[0], "populate_master_from_data", "requires/general_item_issue_return_controller");  		
		show_list_view(returnNumber[1]+'**'+returnNumber[0],'show_dtls_list_view','list_container_yarn','requires/general_item_issue_return_controller','');
		set_button_status(0, permission, 'fnc_gi_issue_return_entry',1,1);
 	}
}

function fnc_gi_issue_return_entry(operation)
{
	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_id').val()+'*'+$('#txt_sys_id').val()+'*'+report_title,"general_item_issue_return_print", "requires/general_item_issue_return_controller")
		 return;
	}
	
	if ($("#is_posted_account").val()*1 == 1) {
		alert("Already Posted In Accounting. Save Update Delete Restricted.");
		return;
	}
	
	if( form_validation('cbo_company_id*cbo_location*txt_return_date*txt_item_description*cbo_store_name*txt_return_qnty','Company *Location*Return Date*Item Description*Store*Return Quantity')==false )
	{
		return;
	}
	
	var variable_lot=$('#variable_lot').val()*1;
	var cbo_item_category=$('#cbo_item_category').val()*1;
	var txt_lot=$('#txt_lot').val();
	if(variable_lot==1 && cbo_item_category==22 && txt_lot=="")
	{
		alert("Lot Maintain Mandatory.");
		$('#txt_lot').focus();
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
	// Store upto validation start
	var store_update_upto=$('#store_update_upto').val()*1;
	var cbo_floor=$('#cbo_floor').val()*1;
	var cbo_room=$('#cbo_room').val()*1;
	var txt_rack=$('#txt_rack').val()*1;
	var txt_shelf=$('#txt_shelf').val()*1;
	var cbo_bin=$('#cbo_bin').val()*1;
	
	if(store_update_upto > 1)
	{
		if(store_update_upto==6 && (cbo_floor==0 || cbo_room==0 || txt_rack==0 || txt_shelf==0 || cbo_bin==0))
		{
			alert("Up To Bin Value Full Fill Required For Inventory");return;
		}
		else if(store_update_upto==5 && (cbo_floor==0 || cbo_room==0 || txt_rack==0 || txt_shelf==0))
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
	//alert("gone");return;
	var dataString = "txt_return_no*cbo_company_id*cbo_location*txt_return_date*txt_return_challan_no*txt_item_description*txt_serial_no*cbo_item_group*txt_return_qnty*cbo_machine_no*txt_remarks*txt_reject_qnty*cbo_uom*cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*cbo_item_category*total_issue*hide_net_used*txt_avrage_rate*txt_prod_id*txt_supplier_id*txt_serial_id*before_prod_id*update_id*before_serial_id*txt_sys_id*txt_issue_id*variable_lot*txt_lot*store_update_upto";
	var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");//alert(data);return;
	freeze_window(operation);
	http.open("POST","requires/general_item_issue_return_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_gi_issue_return_entry_reponse;
}

function fnc_gi_issue_return_entry_reponse()
{	
	if(http.readyState == 4) 
	{	  		
		var reponse=trim(http.responseText).split('**');
		//alert(http.responseText);		
		if(reponse[0]==20)
		{
			alert(reponse[1]);
			release_freezing();
			return;
		}
		else if(reponse[0]*1==16*1)
		{
			alert(reponse[1]);
			release_freezing(); return;
		}
		else if(reponse[0]*1==17*1)
		{
			alert(reponse[1]);
			release_freezing(); return;
		}
		
		show_msg(reponse[0]); 		
		if(reponse[0]==0 || reponse[0]==1)
		{
			$("#txt_return_no").val(reponse[1]);
			$("#txt_sys_id").val(reponse[3]);
			disable_enable_fields( 'cbo_company_id*cbo_location', 1, "", "" );
 			//disable_enable_fields( 'cbo_company_id', 1, "", "" ); // disable true
			show_list_view(reponse[1]+'**'+reponse[3],'show_dtls_list_view','list_container_yarn','requires/general_item_issue_return_controller','');		
			set_button_status(0, permission, 'fnc_gi_issue_return_entry',1,1);
			reset_form('','','txt_item_description*txt_serial_no*cbo_item_group*txt_return_qnty*cbo_machine_no*txt_remarks*txt_reject_qnty*cbo_uom*cbo_store_name*cbo_item_category*txt_lot','','','');
		}

		if(reponse[0]==2)
		{
			if(reponse[4]==1)
			{
				release_freezing();
				location.reload();
			}
			if(reponse[4]==2)
			{
				show_list_view(reponse[1]+'**'+reponse[3],'show_dtls_list_view','list_container_yarn','requires/general_item_issue_return_controller','');		
				set_button_status(0, permission, 'fnc_gi_issue_return_entry',1,1);
				reset_form('','','txt_item_description*txt_serial_no*cbo_item_group*txt_return_qnty*cbo_machine_no*txt_remarks*txt_reject_qnty*cbo_uom*cbo_store_name*cbo_item_category*txt_lot','','','');
			}
		}

		if(reponse[0]==50)
		{
			alert("Serial No. Not Over Return Qnty");
			release_freezing();
			return; 
		}
		release_freezing();
	}
}

function fnResetForm()
{
	$("#cbo_company_id").attr("disabled", false);
	reset_form('generalitemissue_1','list_container_yarn','','','','');
}


function check_data(id)
{
	var txt_return_qnty=$('#txt_return_qnty').val();
	var txt_reject_qnty=$('#txt_reject_qnty').val();
	var value=(parseFloat(txt_return_qnty)+parseFloat(txt_reject_qnty));
	

	var current_total_issue=$('#total_issue').val();
	if((parseInt(current_total_issue)*1)<parseInt(txt_return_qnty))
	{
		$('#txt_return_qnty').val(0);
		alert("Return quantity over the issue quantity not allowed");
		//$('#txt_reject_qnty').val(0);
	}
	else if((parseInt(current_total_issue)*1)<parseInt(txt_reject_qnty))
	{
		$('#txt_reject_qnty').val(0);
		alert("Return quantity over the issue quantity not allowed");
	}
	else if((parseInt(current_total_issue)*1)<value)
	{
		$(id).val(0);
		alert("Return quantity over the issue quantity not allowed");
	}
}

function popup_serial()
{
	if( form_validation('cbo_company_id*txt_item_description','Company Name*Item Description')==false )
	{
		return;
	}
	var serialStringNo = $("#txt_serial_no").val();
	var serialStringID = $("#txt_serial_id").val();
	var current_prod_id = $("#txt_prod_id").val(); 
	var page_link="requires/general_item_issue_return_controller.php?action=serial_popup&serialStringNo='"+serialStringNo+"'&serialStringID='"+serialStringID+"'+&current_prod_id="+current_prod_id; 
	var title="Serial Popup";	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=310px,height=300px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var txt_stringId=this.contentDoc.getElementById("txt_string_id").value;  
		var txt_stringNo=this.contentDoc.getElementById("txt_string_no").value;
 		$("#txt_serial_no").val(txt_stringNo);
		$("#txt_serial_id").val(txt_stringId);
  	}
}

function company_onchange(company)
{
   	/*var data='cbo_company_id='+company+'&action=varible_inventory';
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() 
    {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("store_update_upto").value = this.responseText;
        }
    }
    xmlhttp.open("POST", "requires/general_item_issue_return_controller.php", true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send(data);*/
	
	var varible_string=return_global_ajax_value( company, 'varible_inventory', '', 'requires/general_item_issue_return_controller');
	var varible_string_ref=varible_string.split("**");
	$('#store_update_upto').val(varible_string_ref[0]);
	$('#variable_lot').val(varible_string_ref[1]);
}
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
    <form name="generalitemissue_1" id="generalitemissue_1" autocomplete="off"> 
    <div style="width:1100px;">       
    <table width="100%" cellpadding="0" cellspacing="2" align="center" id="tbl_master">
     	<tr>
        	<td width="80%" align="center" valign="top">   
            	<fieldset style="width:1000px;">
                <legend>General Item Issue Return</legend>
                <br />
                <fieldset style="width:90%;">                                       
                    <table  width="100%" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                        <tr>
                            <td colspan="3" align="right"><b>Return Number</b></td>
                            <td colspan="4" align="left">
                            <input type="text" name="txt_return_no" id="txt_return_no" class="text_boxes" style="width:160px" placeholder="Double Click To Search" onDblClick="open_returnpopup()" readonly />
                            <input type="hidden" id="txt_sys_id" name="txt_sys_id" >
                            </td>
                      </tr>
                      <tr>
                            <td align="right" class="must_entry_caption">Company Name </td>
                            <td width="145">
                                <? 
                                    echo create_drop_down( "cbo_company_id", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond $company_credential_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/general_item_issue_return_controller', this.value, 'load_drop_down_location', 'location_td' );company_onchange(this.value)" );
                                    //load_drop_down( 'requires/general_item_issue_return_controller', this.value, 'load_drop_down_store', 'store_td' );
                                ?>
                                <input type="hidden" id="variable_lot" name="variable_lot" />
                            </td>
                            <td width="70" align="right" class="must_entry_caption">Location</td>
                            <td width="145" id="location_td">
                                <? 
                                    echo create_drop_down( "cbo_location", 140, $blank_array,"", 1, "-- Select Location --", "", "" );
                                ?>
                            </td>
                            <td width="90" align="right"><span class="must_entry_caption">Return Date</span></td>
                            <td width="90" id="location_td"><input type="text" name="txt_return_date" id="txt_return_date" class="datepicker" style="width:90px;"  value="<? echo date("d-m-Y"); ?>" /></td>
                            <td width="100" align="right" >Return Challan</td>
                            <td width="100"><input type="text" name="txt_return_challan_no" id="txt_return_challan_no" class="text_boxes" style="width:100px" />
                                
                            </td>
                      </tr>
                      <tr>
                      </tr>
                      <tr>
                        <td align="right">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right" >&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right" >&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                    </table>
                </fieldset>
                    <br />
                    <table cellpadding="0" cellspacing="1" width="96%" id="tbl_child">
                     <tr>
                   	   <td width="" valign="top" align="center">
                         <fieldset style="width:900px;">  
                                <legend>Return Item Info</legend>                                     
                                  <table  width="100%" cellspacing="2" cellpadding="0" border="0">
                                    <tr>
                                      <td width="120" align="left" class="must_entry_caption">Item Description</td>
                                        <td width="220">
                                            <input class="text_boxes" type="text" name="txt_item_description" id="txt_item_description" style="width:210px;" placeholder="Double Click To Search" onDblClick="open_itemdesc()" readonly  />
                                            <input type="hidden" id="txt_prod_id" name="txt_prod_id" />
                                            <input type="hidden" id="txt_supplier_id" name="txt_supplier_id" />
                                            <input type="hidden" id="txt_issue_id" name="txt_issue_id" />
                                        </td>
                                        <td width="100">Serial No</td>
                                        <td>
                                        <input name="txt_serial_no" id="txt_serial_no" class="text_boxes" type="text" style="width:140px;" placeholder="Double Click" onDblClick="popup_serial()" />
                                    	<input type="hidden" id="txt_serial_id" value="" />
                                        </td>
                                        <td  align="right" width="100">Item Group</td>
                                        <td width="140">
                                            <?
                                                echo create_drop_down( "cbo_item_group", 180, "select id,item_name from lib_item_group","id,item_name", 1, "Display", 0, "",1);
                                            ?>
                                        </td>
                                        
                                    </tr>
                                    <tr>
                                        <td  align="left"><span class="must_entry_caption">Returned Qnty</span></td>
                                        <td>
                                        	<input class="text_boxes_numeric"  onKeyUp="check_data('#txt_return_qnty')" type="text" name="txt_return_qnty" id="txt_return_qnty" style="width:150px;" />
                                        </td>
                                        <td align="left">Machine No</td>
                                        <td>
                                        	<?
											 echo create_drop_down( "cbo_machine_no", 151, "select id,machine_no from  lib_machine_name", "id,machine_no", 1, "--Select--", 0, "", 0,"" );
											?>
                                        </td>
                                        <td align="right" width="100">Item Category</td>
                                        <td width="140">
                                            <?
                                            echo create_drop_down( "cbo_item_category", 180, $item_category, "", 1, "Display", 0, "", 1,"" );
                                            ?>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td  align="left">Rejected Qnty</td>
                                        <td>
                                       	  <input class="text_boxes_numeric" type="text" name="txt_reject_qnty" onKeyUp="check_data('#txt_reject_qnty')" id="txt_reject_qnty" style="width:150px;"   />
                                        </td>
                                        <td align="left">UOM</td>
                                        <td>
                                        <?
                                        echo create_drop_down( "cbo_uom", 151, $unit_of_measurement,"", 1, "Display", 0, "",1 );
                                        ?>
                                        </td>
                                        <td  align="right" rowspan="2">Remarks</td>
                                        <td rowspan="2">
                                            <textarea id="txt_remarks" name="txt_remarks" class="text_area" style="height:40px; width: 150px;" rows="2"></textarea>
                                        </td>

                                    </tr>
                                      <tr>
                                          <td  align="left">Issue Qnty</td>
                                          <td>
                                              <input class="text_boxes_numeric" readonly type="text" name="txt_issue_qnty" id="txt_issue_qnty" style="width:150px;"   />
                                          </td>
                                          <td height="18"  align="left"><span class="must_entry_caption">Store</span></td>
                                          <td id="store_td">
                                              <?
                                              echo create_drop_down( "cbo_store_name", 162, $blank_array,"", 1, "-- Select --", $select, "" );
                                              ?>
                                          </td>
                                      </tr>
                                     <tr>
                                        <td height="18"  align="left">Floor</td>
                                        <td id="floor_td">
											<? echo create_drop_down( "cbo_floor", 162,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
                                        <td align="left">Room</td>
                                        <td id="room_td">
											<? echo create_drop_down( "cbo_room", 162,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
										 <td align="right">Rack</td>
                                        <td id="rack_td">
											<? echo create_drop_down( "txt_rack", 162,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
                                    </tr>
                                    <tr>
                                    	<td height="18"  align="left">Shelf</td>
                                        <td id="shelf_td">
											<? echo create_drop_down( "txt_shelf", 162,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
										<td height="18"  align="left">Bin Box</td>
                                        <td id="bin_td">
											<? echo create_drop_down( "cbo_bin", 162,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
                                        <td align="right">Lot</td>
                                        <td><input name="txt_lot" id="txt_lot" class="text_boxes" type="text" style="width:140px;" readonly disabled /></td> 
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
                        <td height="18" colspan="6" align="center" valign="middle" class="button_container">
                             <!-- details table id for update -->
                             <input type="hidden" id="before_serial_id" name="before_serial_id" value=""/>
                             <input type="hidden" id="total_issue" name="total_issue" />
                             <input type="hidden" name="hide_net_used" id="hide_net_used" readonly />
                             <input type="hidden" id="txt_avrage_rate" name="txt_avrage_rate" >
                             <input type="hidden" id="before_prod_id" name="before_prod_id" value="" />
                             <input type="hidden" id="update_id" name="update_id" value="" />
                             <input type="hidden" id="is_posted_account" name="is_posted_account" value=""/>
                             <input type="hidden" name="store_update_upto" id="store_update_upto">
                             <!-- -->
							 <? echo load_submit_buttons( $permission, "fnc_gi_issue_return_entry", 0,1,"fnResetForm()",1);?>
                        </td>
                   </tr>
                    <tr>
                        <td colspan="6" align="center">
                            <div id="accounting_posted_status" style=" color:red; font-size:24px;";  ></div>
                        </td>
                    </tr>
                </table>                 
              	</fieldset>
                <br>
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
