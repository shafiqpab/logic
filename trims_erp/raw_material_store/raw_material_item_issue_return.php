<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create

Functionality	:
JS Functions	:
Created by		:	Bilas
Creation date 	: 	03-09-2013
Updated by 		: 	Kausar,Jahid,Md mahbubur Rahman
Update date		: 	30-10-2013,15-01-2019
QC Performed BY	:	Creating Report & List view Repair
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
$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][266] );
echo "var field_level_data= ". $data_arr . ";\n";
?>
function open_itemdesc()
{
	if( form_validation('cbo_company_id','Company Name')==false )
	{
		return;
	}
	
	var company = $("#cbo_company_id").val();	
	//var page_link='requires/raw_material_item_issue_return_controller.php?action=return_number_popup&company='+company; 
 	var page_link='requires/raw_material_item_issue_return_controller.php?action=itemdesc_popup&company='+company; 
	var title="Search Item Description";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1150px,height=400px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var mrrNumber=this.contentDoc.getElementById("hidden_recv_number").value.split("_");//alert(mrrNumber); // mrr number
		var issue_rtn_qty = return_global_ajax_value(mrrNumber[1]+"**"+mrrNumber[7], 'issue_rtn_qty', '', 'requires/raw_material_item_issue_return_controller');
		// alert(issue_rtn_qty+"__"+mrrNumber[3]);
		
		$('#txt_item_description').val(mrrNumber[0]);
		$('#txt_prod_id').val(mrrNumber[1]);
		$('#cbo_uom').val(mrrNumber[2]);
		$('#total_issue').val(mrrNumber[3]);
		// $('#total_issue').val((mrrNumber[3]*1)-(issue_rtn_qty*1));
		$('#cbo_item_category').val(mrrNumber[4]);
		$('#cbo_item_group').val(mrrNumber[5]);
		$('#txt_avrage_rate').val(mrrNumber[6]);
		
		$('#txt_issue_id').val(mrrNumber[7]);
		$('#txt_issue_no').val(mrrNumber[8]);
		$('#txt_lot_no').val(mrrNumber[9]);
 	}
}
 
function open_returnpopup()
{
	if( form_validation('cbo_company_id','Company Name')==false )
	{
		return;
	}
	var company = $("#cbo_company_id").val();	
	var page_link='requires/raw_material_item_issue_return_controller.php?action=return_number_popup&company='+company; 
	var title="Search Return Number Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=400px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var returnNumber=this.contentDoc.getElementById("hidden_return_number").value.split("_"); // mrr number
  		// master part call here
		get_php_form_data(returnNumber[0], "populate_master_from_data", "requires/raw_material_item_issue_return_controller");  		
		show_list_view(returnNumber[1]+'**'+returnNumber[0],'show_dtls_list_view','list_container_yarn','requires/raw_material_item_issue_return_controller','');
		set_button_status(0, permission, 'fnc_gi_issue_return_entry',1,1);
 	}
}

function fnc_gi_issue_return_entry(operation)
{
	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_id').val()+'*'+$('#txt_sys_id').val()+'*'+report_title,"general_item_issue_return_print", "requires/raw_material_item_issue_return_controller")
		 return;
	}
	if( form_validation('cbo_company_id*cbo_location*txt_return_date*txt_item_description*cbo_store_name*txt_return_qnty','Company *Location*Return Date*Item Description*Store*Return Quantity')==false )
	{
		return;
	}
	
	var variable_lot=$("#variable_lot").val();
	var cbo_item_category=$("#cbo_item_category").val();
	if(variable_lot==1 && cbo_item_category==22)
	{
		if( form_validation('txt_lot_no','Lot')==false )
		{
			return;
		}
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
	
	
	//alert("gone");return;
	var dataString = "txt_return_no*cbo_company_id*cbo_location*txt_return_date*txt_return_challan_no*txt_item_description*txt_serial_no*cbo_item_group*txt_return_qnty*cbo_machine_no*txt_remarks*txt_reject_qnty*cbo_uom*cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*cbo_item_category*total_issue*hide_net_used*txt_avrage_rate*txt_prod_id*txt_supplier_id*txt_serial_id*before_prod_id*update_id*before_serial_id*txt_sys_id*txt_issue_id*txt_issue_no*txt_lot_no";
	var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");//
	
	//alert(data);return;
	freeze_window(operation);
	http.open("POST","requires/raw_material_item_issue_return_controller.php",true);
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
		}
		if(reponse[0]==50)
		{
			alert("Serial No. Not Over Return Qnty");
			release_freezing();
			return; 
		}
		if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
		{

			show_list_view(reponse[1]+'**'+reponse[3],'show_dtls_list_view','list_container_yarn','requires/raw_material_item_issue_return_controller','');		
			set_button_status(0, permission, 'fnc_gi_issue_return_entry',1,1);
			reset_form('','','txt_item_description*txt_serial_no*cbo_item_group*txt_return_qnty*cbo_machine_no*txt_remarks*txt_reject_qnty*cbo_uom*cbo_store_name*cbo_item_category*txt_lot_no','','','');
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
	var value=(parseInt(txt_return_qnty)+parseInt(txt_reject_qnty));
	

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
	var page_link="requires/raw_material_item_issue_return_controller.php?action=serial_popup&serialStringNo='"+serialStringNo+"'&serialStringID='"+serialStringID+"'+&current_prod_id="+current_prod_id; 
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

function chk_issue_requisition_variabe(company)
{
   var status = return_global_ajax_value(company, 'chk_issue_requisition_variabe', '', 'requires/raw_material_item_issue_return_controller').trim();
   status = status.split("__");
   $("#variable_lot").val(status[0]);
}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
    <form name="generalitemissue_1" id="generalitemissue_1" autocomplete="off" > 
    <div style="width:1100px;">       
    <table width="100%" cellpadding="0" cellspacing="2" align="center" id="tbl_master">
     	<tr>
        	<td width="80%" align="center" valign="top">   
            	<fieldset style="width:1000px;">
                <legend>Raw Material Issue Return</legend>
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
                                    echo create_drop_down( "cbo_company_id", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond $company_credential_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/raw_material_item_issue_return_controller', this.value, 'load_drop_down_location', 'location_td' );chk_issue_requisition_variabe(this.value)" );
                                    //load_drop_down( 'requires/raw_material_item_issue_return_controller', this.value, 'load_drop_down_store', 'store_td' );
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
                                        </td>
                                        <td width="100">Issue Challan</td>
                                        <td>
                                        <input name="txt_issue_no" id="txt_issue_no" class="text_boxes" type="text" style="width:140px;" placeholder=""  />
                                    	<input type="hidden" id="txt_issue_id" value="" />
                                        </td>  
                                        <td width="100" style="display:none">Serial No</td>
                                        <td style="display:none">
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
                                        <td  align="right" rowspan="3">Remarks</td>
                                        <td rowspan="3">
                                        <textarea id="txt_remarks" name="txt_remarks" class="text_area" style="height:60px; width: 150px;" rows="3"></textarea> 
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
                                    </tr>
                                    <tr>
                                        <td height="18"  align="left"><span class="must_entry_caption">Store</span></td>
                                        <td id="store_td">
											<? 
                                            echo create_drop_down( "cbo_store_name", 162, $blank_array,"", 1, "-- Select --", $select, "" ); 
                                            ?>
                                        </td>
                                        <td align="left">Item Category</td>
                                        <td>
										<?  
                                        echo create_drop_down( "cbo_item_category", 151, $item_category, "", 1, "Display", 0, "", 1,"" );
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
                                        <td height="18"  align="right">Total Issue</td>
                                        <td>
                                        <input class="text_boxes_numeric"  type="text" id="total_issue" name="total_issue" style="width:150px;"  readonly/>
										</td>
                                    </tr>
                                    <tr>
                                    	<td height="18"  align="left">Lot</td>
                                        <td><input type="text" class="text_boxes" name="txt_lot_no" id="txt_lot_no" style="width:150px;" readonly disabled  /></td>
										<td colspan="4">&nbsp;</td>
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
                             <!-- -->
							 <? echo load_submit_buttons( $permission, "fnc_gi_issue_return_entry", 0,1,"fnResetForm()",1);?>
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