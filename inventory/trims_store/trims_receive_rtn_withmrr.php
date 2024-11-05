<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Trims receive return
				
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	15-11-2014
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

$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];

if ($company_id !='') {
    $company_credential_cond = "and comp.id in($company_id)";
}

if ($store_location_id !='') {
    $store_location_credential_cond = " and a.id in($store_location_id)"; 
}

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Grey Fabric Receive Return Info","../../", 1, 1, $unicode,1,1); 
$con = connect();
?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

function open_returnpopup()
{
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	var company = $("#cbo_company_name").val();	
	var page_link='requires/trims_receive_rtn_controller.php?action=return_number_popup&company='+company; 
	var title="Search Return Number Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var returnNumber=this.contentDoc.getElementById("hidden_return_number").value.split("_"); // mrr number
  		// master part call here
		get_php_form_data(returnNumber[0], "populate_master_from_data", "requires/trims_receive_rtn_controller"); 
		get_php_form_data(returnNumber[2], "populate_data_from_data", "requires/trims_receive_rtn_controller");  
		//list view call here
		show_list_view(returnNumber[0],'show_dtls_list_view','list_container_yarn','requires/trims_receive_rtn_controller','');
		set_button_status(0, permission, 'fnc_yarn_receive_return_entry',1,1);
		//$("#tbl_master").find('input,select').attr("disabled", true);	
		$("#tbl_child").find('input,select').val('');
		disable_enable_fields( 'txt_return_no', 0, "", "" ); // disable false
 	}
}



function open_mrrpopup()
{
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	var company = $("#cbo_company_name").val();	
	var page_link='requires/trims_receive_rtn_controller.php?action=mrr_popup&company='+company; 
	var title="Search MRR Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var mrrNumber=this.contentDoc.getElementById("hidden_recv_number").value.split("_"); // mrr number
  		// master part call here
		$("#pi_id").val('');
		$("#txt_pi_no").val('');
		
		get_php_form_data(mrrNumber[0], "populate_data_from_data", "requires/trims_receive_rtn_controller");  
 		$("#tbl_child").find('input,select').val('');
 	}
}
 



//form reset/refresh function here
function fnResetForm()
{
	$("#tbl_master").find('input,select').attr("disabled", false);	
	set_button_status(0, permission, 'fnc_yarn_receive_entry',1);
	reset_form('yarn_receive_return_1','list_container_yarn*list_product_container','','','','');
}

// popup for PI----------------------	
function openmypage_pi()
{
	if( form_validation('cbo_company_name*txt_mrr_no','Company Name*MRR No')==false )
	{
		return;
	}
	
	var company = $("#cbo_company_name").val();
	 
	page_link='requires/trims_receive_rtn_controller.php?action=pi_popup&company='+company;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'PI Search', 'width=850px, height=370px, center=1, resize=0, scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var piID=this.contentDoc.getElementById("hidden_tbl_id").value; // pi table id
		var piNumber=this.contentDoc.getElementById("hidden_pi_number").value; // pi number
		
		$("#pi_id").val(piID);
		$("#txt_pi_no").val(piNumber);
	}		
}

function openmypage_rtn_qty()
{
	var cbo_company_name = $('#cbo_company_name').val();
	var txt_received_id = $('#txt_received_id').val();
	var txt_prod_id = $('#txt_prod_id').val();
	var hidden_receive_trans_id= $('#hidden_receive_trans_id').val();
	var update_id = $('#update_id').val();
	var txt_return_qnty = $('#txt_return_qnty').val();
	
	
	if (form_validation('cbo_company_name*txt_received_id*txt_prod_id','Company*Receive MRR*Item Description')==false)
	{
		return;
	}
	var title = 'Receive Info';	
	var page_link = 'requires/trims_receive_rtn_controller.php?cbo_company_name='+cbo_company_name+'&txt_received_id='+txt_received_id+'&txt_prod_id='+txt_prod_id+'&hidden_receive_trans_id='+hidden_receive_trans_id+'&txt_return_qnty='+txt_return_qnty+'&update_id='+update_id+'&action=return_po_popup';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=390px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var tot_qnty=this.contentDoc.getElementById("tot_qnty").value;	 //Access form field with id="emailfield"
		var break_qnty=this.contentDoc.getElementById("break_qnty").value; //Access form field with id="emailfield"
		var break_roll=this.contentDoc.getElementById("break_roll").value; //Access form field with id="emailfield"
		var break_order_id=this.contentDoc.getElementById("break_order_id").value; //Access form field with id="emailfield"
		//alert(tot_qnty);return;
		
		$('#txt_return_qnty').val(tot_qnty);
		$('#txt_break_qnty').val(break_qnty);
		$('#txt_break_roll').val(break_roll);
		$('#txt_order_id_all').val(break_order_id);
		calc_tot_amt(tot_qnty);
	}
}
	
	
	

function fnc_yarn_receive_return_entry(operation)
{
	if(operation==4)
	{
		 var report_title=$( "div.form_caption" ).html();
		 print_report( $('#cbo_company_name').val()+'*'+$('#issue_mst_id').val()+'*'+report_title, "trims_receive_return_print", "requires/trims_receive_rtn_controller" ) 
		 return;
	}
	else if(operation==2)
	{
		show_msg('13');
		return;
	}
	else
	{
		if( form_validation('cbo_company_name*txt_return_date*txt_mrr_no*cbo_return_to*txt_item_description*txt_return_qnty','Company Name*Return Date*MRR Number*Return To*Item Description*Return Quantity')==false )
		{
			return;
		}
		var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_return_date').val(), current_date)==false)
			{
				alert("Receive Return Date Can not Be Greater Than Current Date");
				return;
			}		
		var dataString = "txt_return_no*cbo_company_name*txt_return_date*txt_received_id*txt_mrr_no*cbo_return_to*txt_pi_no*pi_id*txt_item_description*txt_prod_id*txt_return_qnty*cbo_uom*txt_break_qnty*txt_break_roll*txt_order_id_all*txt_remarks*hidden_receive_trans_id*before_prod_id*update_id*issue_mst_id*prev_return_qnty*before_receive_trans_id*txt_global_stock*txt_rate*txt_amount*txt_cons_rate*txt_cons_uom*cbo_item_group*cbo_store_name";
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
		freeze_window(operation);
		http.open("POST","requires/trims_receive_rtn_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_yarn_receive_return_entry_reponse;
	}
}

function fnc_yarn_receive_return_entry_reponse()
{	
	if(http.readyState == 4) 
	{	  	
		var reponse=trim(http.responseText).split('**');
			/*alert(http.responseText);
			release_freezing();
			return;*/

		if(reponse[0]==20)
		{
			alert(reponse[1]);
			release_freezing();
			return;
		} 
		
		else if(reponse[0]==30)
		{
			alert(reponse[1]);
			release_freezing();
			return;
		} 
		 		
		else if(reponse[0]==0 || reponse[0]==1)
		{
			show_msg(reponse[0]);
			$("#txt_return_no").val(reponse[1]);
			$("#issue_mst_id").val(reponse[2]);
 			$("#tbl_master :input").attr("disabled", true);
			disable_enable_fields( 'txt_return_no', 0, "", "" ); // disable false
			show_list_view(reponse[2],'show_dtls_list_view','list_container_yarn','requires/trims_receive_rtn_controller','');		
			//child form reset here after save data-------------//
			set_button_status(0, permission, 'fnc_yarn_receive_return_entry',1,1);
			$("#tbl_child").find('input,select').val('');
			release_freezing();
		}
		else if(reponse[0]==10)
		{
			show_msg(reponse[0]);
			release_freezing();
			return;
		}
	}
}

function calc_tot_amt(amt)
{
	var rate=$("#txt_rate").val();
	$("#txt_amount").val((rate*1)*(amt*1));
}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
    <form name="grey_fab_receive_rtn_1" id="grey_fab_receive_rtn_1" autocomplete="off" > 
    <div style="width:80%;">       
    <table width="80%" cellpadding="0" cellspacing="2" align="left">
     	<tr>
        	<td width="80%" align="center" valign="top">   
            	<fieldset style="width:1000px; float:left;">
                <legend>Trims Receive Return</legend>
                <br />
                 	<fieldset style="width:900px;">                                       
                        <table  width="800" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                            <tr>
                           		<td colspan="3" align="right"><b>Return Number</b></td>
                           		<td colspan="3" align="left">
                                <input type="text" name="txt_return_no" id="txt_return_no" class="text_boxes" style="width:160px" placeholder="Double Click To Search" onDblClick="open_returnpopup()" readonly />
                                <input type="hidden" id="issue_mst_id" name="issue_mst_id" >
                                </td>
                   		   </tr>
                           <tr>
                           		<td colspan="6" align="center">&nbsp;</td>
                            </tr>
                           <tr>
                                <td  width="120" align="right" class="must_entry_caption">Company Name </td>
                                <td width="170">
									<? 
                                     	echo create_drop_down( "cbo_company_name", 170, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond $company_credential_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                                    ?>
                                </td>
                                <td width="120" align="right" class="must_entry_caption">Return Date</td>
                                <td width="170"><input type="text" name="txt_return_date" id="txt_return_date" class="datepicker" style="width:160px;" placeholder="Select Date" /></td>
                                <td width="120" align="right" class="must_entry_caption">Received ID</td>
                                <td width="160" >
                                <input class="text_boxes" type="text" name="txt_mrr_no" id="txt_mrr_no" style="width:160px;" placeholder="Double Click To Search" onDblClick="open_mrrpopup()" readonly  />
                                <input type="hidden" name="txt_received_id" id="txt_received_id" />
                                </td>
                          </tr>
                            <tr>
                                <td align="right" class="must_entry_caption">Returned To</td>
                                <td>
									<? 
										$blank_arr=array();
										echo create_drop_down( "cbo_return_to", 170, "select a.id,a.supplier_name from lib_supplier a","id,supplier_name", 1, "-- Select --", 0, "",1 ); 
									?>
                               	</td>
                                <td align="right">Store Name</td>
                                <td>
									<? 
										echo create_drop_down( "cbo_store_name", 170, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=4 and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "",1 );
									?>
                               	</td>
                                <td align="right">PI NO </td>
                                <td>
                                	<input class="text_boxes" type="text" name="txt_pi_no" id="txt_pi_no" onDblClick="openmypage_pi()" placeholder="Double Click To Search" style="width:160px;" readonly />
                                    <input class="text_boxes" type="hidden" name="pi_id" id="pi_id" disabled/>
                                </td>
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
                                               <td align="right" class="must_entry_caption">Item Description&nbsp;</td>
                                               <td colspan="3">
                                               		<input class="text_boxes" type="text" name="txt_item_description" id="txt_item_description" style="width:300px;" placeholder="Display" readonly disabled/>
                                                    <input type="hidden" id="txt_prod_id" name="txt_prod_id" />
                                               </td> 
                                          </tr>
                                           <tr>
                                               <td width="110"  align="right">Item Group &nbsp;</td>
                                               <td width="140">
                                               <?
                                                	echo create_drop_down( "cbo_item_group", 145, "select id,item_name from lib_item_group where item_category=4 and status_active=1 and is_deleted=0 order by item_name", "id,item_name", 1, "-- Select --", 0,  "",1 );
                                                ?>
                                               </td>
                                               <td width="70" align="right">Color</td>
                                               <td width="130">
                                               <?
                                                	echo create_drop_down( "cbo_item_color", 130, "select id,color_name from lib_color where status_active=1 and is_deleted=0 order by color_name", "id,color_name", 1, "-- Select --", 0,  "",1 );
                                                ?>
                                               </td>
                                          </tr>
                                          <tr>
                                               <td  align="right">Brand/Sup Ref</td>
                                               <td ><input class="text_boxes" type="text" name="txt_brad_supp" id="txt_brad_supp" style="width:135px;" placeholder="Display" readonly disabled  /></td>
                                               <td  align="right">Size</td>
                                               <td >
                                               <input type="text" id="txt_item_size" name="txt_item_size" class="text_boxes" style="width:120px;" readonly disabled >
                                               </td>
                                          </tr>
                                          <tr>
                                               <td  align="right" class="must_entry_caption">Returned Qnty&nbsp;</td>
                                               <td >
                                               <input class="text_boxes_numeric" type="text" name="txt_return_qnty" id="txt_return_qnty" style="width:135px;" placeholder="Double Click To Search" readonly onDblClick="openmypage_rtn_qty()"/>
                                               <input type="hidden" id="txt_break_qnty" name="txt_break_qnty" > 
                                               <input type="hidden" id="txt_break_roll" name="txt_break_roll" >
                                               <input type="hidden" id="txt_order_id_all" name="txt_order_id_all" >
                                               <input type="hidden" id="prev_return_qnty" name="prev_return_qnty" >
                                               </td>
                                               <td  align="right">Rate</td>
                                               <td >
                                               <input class="text_boxes_numeric" type="text" name="txt_rate" id="txt_rate" style="width:118px;" placeholder="Display" readonly disabled   />
                                               <input class="text_boxes_numeric" type="hidden" name="txt_cons_rate" id="txt_cons_rate" style="width:118px;" />
                                               </td>
                                               
                                          </tr>
                                          <tr>
                                               <td  align="right">amount</td>
                                               <td >
                                               <input class="text_boxes_numeric" type="text" name="txt_amount" id="txt_amount" style="width:135px;" placeholder="Display" readonly disabled  />
                                               
                                               </td>
                                               <td  align="right">UOM</td>
                                               <td >
											   <? echo create_drop_down( "cbo_uom", 130, $unit_of_measurement,"", 1, "Display", 0, "",1 ); ?>
                                               <input type="hidden" id="txt_cons_uom" name="txt_cons_uom" class="text_boxes_numeric" style="width:120px" >
                                               </td>
                                          </tr>
                                          
                                          <tr>
                                               <td  align="right">Remarks&nbsp;</td>
                                               <td colspan="3"><input class="text_boxes" type="text" name="txt_remarks" id="txt_remarks" style="width:300px;" placeholder="Write" /></td>
                                          </tr>
                                   </table>
                            </fieldset>
                       	 <fieldset style="width:460px; float:left; margin-left:5px">  
                           <legend>Display</legend>                                     
                                  <table  width="350" cellspacing="2" cellpadding="0" border="0" id="display" >                           
                            <tr>
                                  <td>Trims Received</td>
                                  <td width="100"><input  type="text" name="txt_fabric_received" id="txt_fabric_received" class="text_boxes" style="width:160px" readonly disabled  /></td>
                            </tr>                        
                            <tr>
                                <td>Cumulative Return</td>
                                <td>
                                <input  type="text" name="txt_cumulative_issued" id="txt_cumulative_issued" class="text_boxes" style="width:160px"  readonly disabled />
                                <input type="hidden" id="hidden_receive_trans_id" name="hidden_receive_trans_id" readonly disabled  />
                                <input type="hidden" id="before_receive_trans_id" name="before_receive_trans_id" readonly disabled  />
                                </td>
                            </tr>
                            <tr>
                                <td>Yet to Issue</td>
                                <td width="100">
                                    <input  type="text" name="txt_yet_to_issue" id="txt_yet_to_issue" class="text_boxes" style="width:160px"  readonly disabled />
                                </td>
                            </tr> 
                            <tr>
                                <td>Global Stock</td>
                                <td><input type="text" name="txt_global_stock" id="txt_global_stock" placeholder="Display" class="text_boxes" style="width:160px" disabled /></td>
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
                              <!-- -->
							 <? echo load_submit_buttons( $permission, "fnc_yarn_receive_return_entry", 0,1,"fnResetForm()",1);?>
                        </td>
                   </tr> 
                </table>                 
              	</fieldset>
              	<fieldset>
    			<div style="width:990px;" id="list_container_yarn"></div>
    		  	</fieldset>
           </td>
         </tr>
    </table>
    </div>
    <div id="list_product_container" style="max-height:500px; width:20%; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>  
	</form>
</div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
