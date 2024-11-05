<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Wash Dyes and Chemical Issue Return
				
Functionality	:	
JS Functions	:
Created by		:	Md Mahbubur Rahman 
Creation date 	: 	25-02-2020
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
echo load_html_head_contents("Wash Dyes and Chemical Issue Return","../../", 1, 1, $unicode,1,1); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

function open_item()
{
	if( form_validation('cbo_company_id','Company Name')==false )
	{
		return;
	}
	
	var company = $("#cbo_company_id").val();	
	//var page_link='requires/general_item_issue_return_controller.php?action=return_number_popup&company='+company; 
 	var page_link='requires/wash_chemical_dyes_issue_return_controller.php?action=itemdesc_popup&company='+company; 
	var title="Search Item Description";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=930px,height=400px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var mrrNumber=this.contentDoc.getElementById("hidden_recv_number").value;//alert(mrrNumber); // mrr number  hidden_issue_id
		var issue_id=mrrNumber.split('_');
		$("#hidden_issue_id").val(issue_id[1]);
		get_php_form_data(mrrNumber, "populate_data_from_data", "requires/wash_chemical_dyes_issue_return_controller");
		show_list_view(mrrNumber,'populate_independent_data','item_description_list','requires/wash_chemical_dyes_issue_return_controller','');  		
 	}
}
 

function open_item_basis()
{
	if( form_validation('cbo_company_id','Company Name')==false )
	{
		return;
	}
	
	var company = $("#cbo_company_id").val();	
 	var page_link='requires/wash_chemical_dyes_issue_return_controller.php?action=itemdesc_popup&company='+company; 
	var title="Search Item Description";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=930px,height=400px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var mrrNumber=this.contentDoc.getElementById("hidden_recv_number").value;//alert(mrrNumber); // mrr number
		get_php_form_data(mrrNumber, "populate_data_from_data", "requires/wash_chemical_dyes_issue_return_controller");  		
 	}
}
 
function open_returnpopup()
{
	if( form_validation('cbo_company_id','Company Name')==false )
	{
		return;
	}
	var company = $("#cbo_company_id").val();
	
	var page_link='requires/wash_chemical_dyes_issue_return_controller.php?action=return_number_popup&company='+company; 
	var title="Search Return Number Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=400px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{ 
	
		var theform=this.contentDoc.forms[0]; 
		var data=(this.contentDoc.getElementById("hidden_return_number").value).split('**');
		var returnNumber=data[0]; // mrr number
  		document.getElementById('txt_return_no').value=returnNumber;
		rcv_variable_check(company);
        get_php_form_data(returnNumber, "populate_master_from_data", "requires/wash_chemical_dyes_issue_return_controller"); 
		if(parseInt(data[2])==4)
		{
			disable_enable_fields( 'cbo_company_id*cbo_issue_basis*txt_batch_name', 1, '', '' );
			$("#item_description_list").hide('fast');
		}
	    else
		{
			$("#item_description_list").show('fast');
			disable_enable_fields( 'cbo_company_id*cbo_issue_basis*txt_item_description*txt_batch_name', 1, '', '' );
		}
		show_list_view(company+"**"+data[3],'populate_batch_no_data_update','item_description_list','requires/wash_chemical_dyes_issue_return_controller','');
		var hidden_posted_account = $("#hidden_posted_account").val();
			
		show_list_view(data[3]+"**"+hidden_posted_account,'show_dtls_list_view','list_container_dyes','requires/wash_chemical_dyes_issue_return_controller','');
		
		set_button_status(0, permission, 'fnc_chemical_issue_return_entry',1,1);
 	}
}


function fnc_chemical_issue_return_entry(operation)
{
	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_return_no').val()+'*'+report_title+'*'+$('#update_id').val(),'issue_return_print','requires/wash_chemical_dyes_issue_return_controller');
		return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
		var variable_lot=$('#variable_lot').val();
		if(variable_lot==1)
		{
			if( form_validation('cbo_company_id*cbo_location*cbo_issue_basis*txt_return_date*cbo_store_name*txt_item_description*txt_lot*txt_return_qnty','Company *Location*Issue Basis*Return Date*Store*Item Description*Lot*Return Quantity')==false )
			{
				return;
			}
		}
		else
		{
			if( form_validation('cbo_company_id*cbo_location*cbo_issue_basis*txt_return_date*cbo_store_name*txt_item_description*txt_return_qnty','Company *Location*Issue Basis*Return Date*Store*Item Description*Return Quantity')==false )
			{
				return;
			}
		}
			
		var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_return_date').val(), current_date)==false)
		{
			alert("Issue Return Date Can not Be Greater Than Current Date");
			return;
		}
		
		if($("#txt_return_qnty").val()*1<=0)
		{
			alert("Return Quantity Should be Greater Than Zero(0).");
			return;
		}
		var dataString = "txt_return_no*cbo_company_id*cbo_location*txt_return_date*txt_return_challan_no*txt_item_description*txt_return_date*cbo_issue_basis*txt_return_qnty*total_issue*txt_batch_name*txt_remarks*txt_reject_qnty*cbo_uom*cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_item_category*update_id*txt_prod_id*before_prod_id*txt_batch_id*hidden_issue_id*txt_rate*txt_amount_qnty*hidden_trans_id*txt_lot*variable_lot";
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/wash_chemical_dyes_issue_return_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_chemical_issue_return_entry_reponse;
	}
}

function fnc_chemical_issue_return_entry_reponse()
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
		
		show_msg(reponse[0]); 		
		if(reponse[0]==0 || reponse[0]==1)
		{
			$("#txt_return_no").val(reponse[1]);
			$("#update_id").val(reponse[2]);
			var issue_basis=$("#cbo_issue_basis").val();
			if(issue_basis==4)
			{
				disable_enable_fields( 'cbo_company_id*cbo_issue_basis*txt_batch_name', 1, "", "" ); // disable true
			}
			else
			{
				disable_enable_fields( 'cbo_company_id*cbo_issue_basis*txt_item_description', 1, "", "" ); // disable true  
			}
		}
		if(reponse[0]==50)
		{
			alert("Serial No. Not Over Return Qnty");
			release_freezing();
			return; 
		}		
		show_list_view(reponse[2],'show_dtls_list_view','list_container_dyes','requires/wash_chemical_dyes_issue_return_controller','');		
		set_button_status(0, permission, 'fnc_chemical_issue_return_entry',1,1);
		reset_form('','','txt_item_description*cbo_uom*txt_return_qnty*txt_reject_qnty*cbo_item_category*txt_issue_id*txt_issue_qty*txt_issue_challan*txt_rate*txt_amount_qnty*txt_net_used*txt_return_total*txt_lot*cbo_store_name*txt_remarks','','','');
		release_freezing();
	}
}

function generate_report_file(data,action,page)
{
	window.open("requires/wash_chemical_dyes_issue_return_controller.php?data=" + data+'&action='+action, true );
}

function fnResetForm()
{
	$("#tbl_master").find('input').attr("disabled", false);	
	reset_form('chemicalissuereturn_1','item_description_list','','','','');
}


function check_data(id)
{
	var txt_return_qnty=$('#txt_return_qnty').val();
	var txt_reject_qnty=$('#txt_reject_qnty').val();
	var value=(parseInt(txt_return_qnty)+parseInt(txt_reject_qnty));
	var current_total_issue=$('#txt_net_used').val();
	if((parseInt(current_total_issue)*1)<parseInt(txt_return_qnty))
	{
		$('#txt_return_qnty').val(0);
		alert("Return quantity over the net used quantity not allowed");
		//$('#txt_reject_qnty').val(0);
	}
	else if((parseInt(current_total_issue)*1)<parseInt(txt_reject_qnty))
	{
		$('#txt_reject_qnty').val(0);
		alert("Return quantity over the net used quantity not allowed");
	}
	else if((parseInt(current_total_issue)*1)<value)
	{
		$(id).val(0);
		alert("Return quantity over the issue quantity not allowed");
	}
}

function fn_independent(basis)
{
	if(basis==4)
	   
		 {
		    $('#txt_batch_name').val("");
			$("#txt_batch_name").attr("disabled",true);
			$('#txt_batch_name').attr('placeholder','No need');
			$('#txt_item_description').attr("disabled",false);
			$('#txt_item_description').attr('placeholder','Browse');	
	   }
	else
	    {
			$("#txt_batch_name").attr("disabled",false);
			$('#txt_batch_name').attr('placeholder','Browse');	
			$('#txt_item_description').attr("disabled",true);
			$('#txt_item_description').attr('placeholder','No need');
			$('#txt_item_description').val("");
			$('#cbo_item_group').val("");	
			$('#cbo_uom').val("");	
			$('#cbo_item_category').val("");			
	    }
}
//for batch 


function openmypage_batchNo()
	{

		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_issue_basis = $('#cbo_issue_basis').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'Batch No Selection Form';	
			var page_link = 'requires/wash_chemical_dyes_issue_return_controller.php?cbo_company_id='+cbo_company_id+'&cbo_issue_basis='+cbo_issue_basis+'&action=batch_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=400px,center=1,resize=0,scrolling=0','../');
			emailwindow.onclose=function()
			 {
				var cbo_company_id=document.getElementById("cbo_company_id").value;
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var batch_id=this.contentDoc.getElementById("hidden_batch_id").value;
				var batch_no=this.contentDoc.getElementById("hidden_batch_no").value;
				var mst_id=this.contentDoc.getElementById("issue_master_id").value;
		        show_list_view(cbo_issue_basis+"**"+cbo_company_id+"**"+batch_id+"**"+mst_id,'populate_batch_no_data','item_description_list','requires/wash_chemical_dyes_issue_return_controller','');
				freeze_window(5);
			    document.getElementById('txt_batch_name').value=batch_no;
				document.getElementById('txt_batch_id').value=batch_id;
				release_freezing();
			
			}
		}
	}
	
	function rcv_variable_check(company_id)
	{
		reset_form('chemicalissuereturn_1','item_description_list','','','','cbo_company_id');
		var lots_variable=return_global_ajax_value( company_id, 'populate_data_lib_data', '', 'requires/wash_chemical_dyes_issue_return_controller');
		$('#variable_lot').val(lots_variable);
		if(lots_variable==1)
		{
			$('#lot_caption').css('color', 'blue');
		}
		else
		{
			$('#lot_caption').css('color', 'black');
		}
	}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
    <form name="chemicalissuereturn_1" id="chemicalissuereturn_1" autocomplete="off" > 
    
    <div style="width:1300px;">       
    <table width="1300" cellpadding="0" cellspacing="2" align="left">
     	<tr>
        	<td width="850" align="center" valign="top">   
            	<fieldset style="width:850px;">
                <legend>Dyes/Chemical Issue Return</legend>
                <br />
                <fieldset style="width:800px;">                                       
                    <table  width="100%" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                        <tr>
                            <td colspan="3" align="right"><b>Return Number</b></td>
                            <td colspan="4" align="left"><input type="text" name="txt_return_no" id="txt_return_no" class="text_boxes" style="width:160px" placeholder="Double Click To Search" onDblClick="open_returnpopup()" readonly /></td>
                      </tr>
                      <tr>
                            <td align="right" class="must_entry_caption">Company Name </td>
                            <td width="145">
                                <? 
                                    echo create_drop_down( "cbo_company_id", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "rcv_variable_check(this.value);load_drop_down( 'requires/wash_chemical_dyes_issue_return_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                                    //load_drop_down( 'requires/wash_chemical_dyes_issue_return_controller', this.value, 'load_drop_down_store', 'store_td' );
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
                            <td width="145" id="location_td"><input type="text" name="txt_return_date" id="txt_return_date" class="datepicker" style="width:130px;" placeholder="Select Date" />             </td>
                           
                      </tr>
                      <tr>
                      </tr>
                      <tr>
                        <td align="right" class="must_entry_caption">  Basis</td>
                        <td width="160">
							<? 
                               echo create_drop_down( "cbo_issue_basis", 140, $receive_basis_arr,"", 1, "- Select  Basis -", $selected, "fn_independent(this.value)","","4,5,7" );
                            ?>
                        </td>
                        <td align="right" >Requisition</td>
                        <td><input type="text" name="txt_batch_name" id="txt_batch_name" class="text_boxes" style="width:130px" onDblClick="openmypage_batchNo()" placeholder="No need" disabled/>
                            <input type="hidden" name="txt_batch_id" id="txt_batch_id"/>
                            <input type="hidden" id="total_issue" name="total_issue" />
                            <input type="hidden" name="hidden_issue_id" id="hidden_issue_id"/>
                            <input type="hidden" name="hidden_posted_account" id="hidden_posted_account"/>
                        </td>
                        <td align="right">Return Challan</td>
                        <td>
                           <input type="text" name="txt_return_challan_no" id="txt_return_challan_no" class="text_boxes" style="width:130px" />
                         </td>
                      </tr>
                    </table>
                </fieldset>
                    <br />
                
                    <table cellpadding="0" cellspacing="1" width="820" id="tbl_child">
                     <tr>
                   	   <td width="" valign="top" align="center">
                         <fieldset style="width:400px;float:left;">  
                                <legend>Return Item Info</legend>                                     
                                  <table  width="100%" cellspacing="2" cellpadding="0" border="0">
                                    <tr>
                                       <td width="100" align="right" class="must_entry_caption">Item Description</td>
                                       <td width="300" colspan="3">
                                            <input class="text_boxes" type="text" name="txt_item_description" id="txt_item_description" style="width:260px;" placeholder="Double Click To Search" onDblClick="open_item()" readonly  />
                                            <input type="hidden" id="txt_prod_id" name="txt_prod_id" />
                                            <input type="hidden" id="before_prod_id" name="before_prod_id" />
                                            <input type="hidden" id="cbo_item_category" name="cbo_item_category" />
                                            <input type="hidden" id="hidden_trans_id" name="hidden_trans_id" />
                                         
                                        </td>
                                    </tr>
                                     <tr>
                                        <td  align="right" width="100" id="lot_caption" style="display:none">Lot</td>
                                        <td style="display:none">
                                             <input class="text_boxes" type="text" name="txt_lot"  id="txt_lot" style="width:70px;" readonly /> 
                                        </td>
                                        <td align="right">UOM</td>
                                        <td >
                                        <?
                                        echo create_drop_down( "cbo_uom", 140, $unit_of_measurement,"", 1, "Display", 0, "",1 );
                                        ?>
                                        </td>
                                        <td  align="right" width="100"><span class="must_entry_caption">Returned Qnty</span></td>
                                        <td>
                                        	<input class="text_boxes_numeric"  onKeyUp="check_data('#txt_return_qnty')" type="text" name="txt_return_qnty" id="txt_return_qnty" style="width:70px;" />
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        
                                         <td align="right" class="must_entry_caption"> Store</td>

                                         <td width="160" id="store_td">
                                                 <? echo create_drop_down( "cbo_store_name", 140, $blank_array,"", 1, "-- Select --", $storeName, "" ); ?> 
                                         </td>
                                         <td  align="right">Rejected Qnty</td>
                                        <td>
                                       	  <input class="text_boxes_numeric" type="text" name="txt_reject_qnty" onKeyUp="check_data('#txt_reject_qnty')" id="txt_reject_qnty" style="width:70px;"   />
                                        </td>
                                      </tr>
                                      <tr>
                                        
                                        <td align="right" width="41" style="display:none">Floor</td>
										<td id="floor_td" style="display:none">
											<? echo create_drop_down( "cbo_floor", 140,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
                                    </tr>
                                    <tr>
                                    	<td></td>
                                    	<td></td>
                                    	<td align="right" width="41" style="display:none">Room</td>
										<td id="room_td" style="display:none">
											<? echo create_drop_down( "cbo_room", 140,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
                                    </tr>
                                    <tr>
                                    	<td></td>
                                    	<td></td>
                                    	<td align="right" width="41" style="display:none">Rack</td>
										<td id="rack_td" style="display:none">
											<? echo create_drop_down( "txt_rack", 140,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td> 
                                    </tr>
                                    <tr>
                                    	<td></td>
                                    	<td></td>
                                    	<td align="right" width="41" style="display:none">Shelf</td>
										<td id="shelf_td" style="display:none">
											<? echo create_drop_down( "txt_shelf", 140,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
                                    </tr>
                                    <tr>
                                         <td  align="right" >Remarks</td>
                                         <td colspan="3">
                                        <input type="text" id="txt_remarks" name="txt_remarks" class="text_boxes"  style="width:260px" /> 
                                        </td> 
                                 	</tr>
                                  
                           </table>
                          </fieldset>
                   	   </td>
                        <td width="" valign="top" align="center">
                         <fieldset style="width:400px;float:left; margin-left:5px">  
                                <legend>Display</legend>                                     
                                  <table  width="100%" cellspacing="2" cellpadding="0" border="0">
                                    <tr>
                                       <td width="100" align="right" >Issue Id</td>
                                       <td width="120" >
                                            <input class="text_boxes" type="text" name="txt_issue_id" id="txt_issue_id" style="width:120px;" placeholder="Display"  readonly  />
                                        </td>
                                        <td width="70" align="right" >Issue Qnty</td>
                                       <td width="100" >
                                            <input class="text_boxes_numeric" type="text" name="txt_issue_qty" id="txt_issue_qty" style="width:100px;" placeholder="Display"  readonly  />
                                        </td>
                                    </tr>
                                    
                                     <tr>
                                        <td  align="right" >Issue Challan No</td>
                                        <td >
                                             <input class="text_boxes" type="text" name="txt_issue_challan"  id="txt_issue_challan" style="width:120px;"  placeholder="Display"  readonly /> 
                                        </td>
                                        <td  align="right" >Rate</td>
                                       <td  >
                                            <input class="text_boxes_numeric" type="text" name="txt_rate" id="txt_rate" style="width:100px;" placeholder="Display"  readonly  />
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td  align="right" >Total Return</td>
                                        <td>
                                        	<input class="text_boxes_numeric"   type="text" name="txt_return_total" id="txt_return_total" style="width:120px;" placeholder="Display"  readonly/>
                                        </td>
                                         <td align="right" > Amount</td>
                                        <td width="" id="store_td">
                                            <input class="text_boxes_numeric"   type="text" name="txt_amount_qnty" id="txt_amount_qnty" style="width:100px;" placeholder="Display"  readonly/>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td  align="right">Net Used</td>
                                        <td>
                                       	  <input class="text_boxes_numeric" type="text" name="txt_net_used"  id="txt_net_used" style="width:120px;"   />
                                        </td>
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
                             <input type="hidden" name="save_data" id="save_data" readonly  />	
                             <input type="hidden" name="all_po_id" id="all_po_id" readonly />
                             <input type="hidden" id="distribution_method" readonly />
                             
                       
                             <input type="hidden" id="update_id" name="update_id" value="" />
                             <!-- -->
							 <? echo load_submit_buttons( $permission, "fnc_chemical_issue_return_entry", 0,1,"fnResetForm()",1);?>
                        </td>
                   </tr> 
                </table>                 
              	</fieldset>
                <br>
              	<fieldset style="width:850px;">
    			<div style="width:850px;" id="list_container_dyes"></div>
    		  	</fieldset>
           </td>
           <td valign="top">
           <div id="item_description_list" style="margin-left:5px"></div>
           </td>
         </tr>
    </table>
    </div>
    
    </form>
</div>
   
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
