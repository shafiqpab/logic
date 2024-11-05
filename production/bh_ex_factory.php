<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create for Ex-Factory Entry For Buying House

Functionality	:
JS Functions	:
Created by		:	MD. Rakib Hasan Mondal
Creation date 	: 	19-12-2023
Updated by 		: 	
Update date		: 	
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
echo load_html_head_contents("Wash Issue Entry","../", 1, 1, $unicode,'','');

if ($db_type == 0) {
    $sending_location="select concat(b.id,'*',a.id) id,concat(b.location_name,':',a.company_name) location_name from lib_company a, lib_location b where a.id=b.company_id and b.status_active =1 and b.is_deleted=0 and a.status_active =1 and a.is_deleted=0 order by a.company_name";
} else if ($db_type == 2 || $db_type == 1) {
    $sending_location="select b.id||'*'||a.id as id, b.location_name||' : '||a.company_name as location_name  from lib_company a, lib_location b where a.id=b.company_id and b.status_active =1 and b.is_deleted=0 and a.status_active =1 and a.is_deleted=0 order by a.company_name";
}
?>

<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";  
	
	function openmypage(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=370px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{  
			$("#cbo_company_name").attr("disabled","disabled");
			let po_id = $("#hidden_po_break_down_id").val(); 
            show_list_view(po_id,'show_dtls_listview','production_list_view','requires/bh_ex_factory_controller','');
		}
	}//end function

	function fnc_issue_print_embroidery_entry(operation)
	{ 
		if(operation==0 || operation==1 || operation==2)
		{ 
			if ( form_validation('txt_order_no*cbo_company_name*cbo_source*cbo_ex_fact_comp*txt_issue_date*txt_ex_fact_qty*txt_carton_qty','Order No*company Name*Source*Ex Factory Comp*Ex Factory Date*Ex Factory Qty*Reject Qty*Carton Qty')==false )
			{
				return;
			}
			else
			{  
				 
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_order_no*hidden_po_break_down_id*hidden_job_id*cbo_company_name*cbo_buyer_name*txt_style_no*cbo_item_name*txt_order_qty*txt_plan_cut_qty*cbo_source*cbo_ex_fact_comp*cbo_source*txt_issue_date*txt_reject_qty*txt_ex_fact_qty*txt_carton_qty*txt_challan*txt_qty_ctn*delivery_status*txt_remarks*txt_mst_id',"../");
				//alert (data);return;
				freeze_window(operation);
				http.open("POST","requires/bh_ex_factory_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_issue_print_embroidery_Reply_info;
			}
		}
	}
	
	function fnc_issue_print_embroidery_Reply_info()
	{
		if(http.readyState == 4)
		{
            let reponse = http.responseText.split('**');
            let po_id  = reponse[1];
			childFormReset();
            show_list_view(po_id,'show_dtls_listview','production_list_view','requires/bh_ex_factory_controller','');
            set_button_status(0, permission, 'fnc_issue_print_embroidery_entry',1,1);
            if(reponse[0]==98)
            {
                release_freezing();
                alert('Update restricted.');
                return;
            }if(reponse[0]==99)
            {
                release_freezing();
                alert('Same date duplicate entry not allowed.');
                return;
            }
			release_freezing();
		}	  
	}

	function childFormReset()
	{
		reset_form('','','txt_order_no*hidden_po_break_down_id*hidden_job_id*cbo_company_name*cbo_buyer_name*txt_style_no*cbo_item_name*txt_order_qty*txt_plan_cut_qty*cbo_source*cbo_ex_fact_comp*cbo_source*txt_reject_qty*txt_ex_fact_qty*txt_carton_qty*txt_challan*txt_qty_ctn*delivery_status*txt_remarks*txt_po_qty*txt_cumul_ex_fact_qty*txt_yet_to_ex_fact*txt_mst_id','','');
		$('#txt_po_qty').attr('placeholder','');//placeholder value initilize
		$('#txt_cumul_ex_fact_qty').attr('placeholder','');//placeholder value initilize
		$('#txt_yet_to_ex_fact').attr('placeholder','');//placeholder value initilize 
	
	} 

	function validate_ex_fact_qty() 
	{
		let yet_to_ex_fact  = $('#txt_yet_to_ex_fact').val();
		let ex_fact_qty 	= $('#txt_ex_fact_qty').val();

		let balance = yet_to_ex_fact-ex_fact_qty;
		console.log(`Befor cond ${balance}`);
		if(balance<0)
		{
            console.log(`After cond ${balance}`);
			if( confirm("Qnty Excceded by"+(yet_to_ex_fact-ex_fact_qty)) )
			{
				// void(0); 
                $('#txt_ex_fact_qty').val('0');
			}
			else
			{
				$('#txt_ex_fact_qty').val('0');
			}
		}
	}
	function fnc_load_from_dtls(data)
	{
        freeze_window(5);
		//alert(data); return;
		get_php_form_data(data,'populate_issue_form_data','requires/bh_ex_factory_controller'); 
        release_freezing();
	}
 
</script>
</head>
<body onLoad="set_hotkey()">
 <div style="width:100%;">
  	<? echo load_freeze_divs ("../",$permission);  ?>
    <div style="width:930px; float:left" align="center">
 		<fieldset style="width:930px;">
        <legend>Ex-Factory Entry For Buying House</legend>
        <form name="printembro_1" id="printembro_1" method="" autocomplete="off" >
            <fieldset>
                <table width="100%">
                    <tr>
						<td width="110" class="must_entry_caption">Order No</td>
						<td width="200">
							<input name="txt_order_no" placeholder="Double Click to Search" onDblClick="openmypage('requires/bh_ex_factory_controller.php?action=order_popup&company='+document.getElementById('cbo_company_name').value,'Order Search')" id="txt_order_no" class="text_boxes" style="width:160px " readonly />
							<input type="hidden" id="hidden_po_break_down_id" value="" /> 
							<input type="hidden" id="hidden_job_id" value="" /> 
						</td>
                        <td width="110" class="must_entry_caption">Company</td>
                        <td width="200">
							<? echo create_drop_down( "cbo_company_name", 170, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "",1 );
                            ?>
                        </td> 
						<td>Buyer</td>
                        <td><? echo create_drop_down( "cbo_buyer_name", 170, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,0 ); ?></td>
                    </tr>
                    <tr> 
                        <td>Style</td>
                        <td><input name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:160px" disabled  readonly></td>
                        <td>Item</td>
                        <td><? echo create_drop_down( "cbo_item_name", 170, $garments_item,"", 1, "-- Select Item --", $selected, "",1,0 ); ?></td>
						<td>Order Qty</td>
                        <td><input name="txt_order_qty" id="txt_order_qty" class="text_boxes_numeric"  style="width:160px" disabled readonly></td>
                    </tr>
                    <tr> 
						<td>Plan Cut Qty</td>
                        <td><input name="txt_plan_cut_qty" id="txt_plan_cut_qty" class="text_boxes_numeric"  style="width:160px" disabled readonly></td>
                        <td class="must_entry_caption">Source</td>
                        <td><? echo create_drop_down( "cbo_source", 170, $knitting_source,"", 0, "-- Select Source --",3, "", 1, 3 ); ?></td>
						<td width="110" class="must_entry_caption">Ex-Factory Company</td>
                        <td width="200">
							<? echo create_drop_down("cbo_ex_fact_comp", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(98)  and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select Supplier --", $selected, "", 0);
                            ?>
                        </td> 
                    </tr>  
                </table>
                </fieldset> <br />
                <table cellpadding="0" cellspacing="1" width="100%">
                    <tr>
                          <td width="35%" valign="top">
                               <fieldset>
                                  <legend>New Entry</legend>
                                   <table cellpadding="0" cellspacing="2" width="100%">
                                      <tr>
                                           <td width="80" class="must_entry_caption">Ex- Factory Date </td>
                                           <td colspan="3" width="110"><input type="text" name="txt_issue_date" id="txt_issue_date" value="<? echo date("d-m-Y")?>" class="datepicker" style="width:100px;" disabled/></td>
                                      </tr> 
                                      <tr>
                                           <td class="must_entry_caption">Ex- Factory Qty</td>
                                           <td colspan="3">
                                               <input type="text" name="txt_ex_fact_qty" id="txt_ex_fact_qty"  class="text_boxes_numeric"  style="width:100px" onchange="validate_ex_fact_qty()"> 
                                           </td>
                                      </tr> 
                                      <tr>
                                           <td class="must_entry_caption" style="display: none;">Reject Qty</td>
                                           <td colspan="3">
                                               <input style="display: none;" type="text" name="txt_reject_qty" id="txt_reject_qty"  class="text_boxes_numeric"  style="width:100px"  > 
                                           </td>
                                      </tr>
                                      <tr>
                                           <td class="must_entry_caption">Total Carton Qty</td>
                                           <td colspan="3">
                                               <input type="text" name="txt_carton_qty" id="txt_carton_qty"  class="text_boxes_numeric"  style="width:100px"  > 
                                           </td>
                                      </tr>
                                      <tr>
                                           <td>Challan No</td>
                                           <td><input type="text" name="txt_challan" id="txt_challan" class="text_boxes" style="width:100px" /></td> 
                                     </tr>
                                     <tr>
                                        <td>Qty/Ctn(Pcs/Set)</td>
                                        <td><input type="text" name="txt_qty_ctn" id="txt_qty_ctn" class="text_boxes" style="width:100px" /></td>
                                     </tr>
                                     <tr>
                                        <td>Shipping Status</td>
                                        <td>
											<?
												$search_by_arr=array(2=>"Partial Delivery",3=>"Full Delivery/Closed ");					
												echo create_drop_down( "delivery_status", 110, $search_by_arr,"",0, "--Select--", 2,$selected,0 ); 
											?>
										</td>
                                     </tr>
                                     <tr>
                                           <td>Remarks</td>
                                           <td colspan="3"><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:212px" title="450 Characters Only." /></td>
                                     </tr>
                                </table>
                             </fieldset>
                          </td>
                          <td width="1%" valign="top"></td>
                          <td width="22%" valign="top">
                            <fieldset>
                                <legend>Display</legend>
                                <table  cellpadding="0" cellspacing="2" width="100%" >
                                    <tr>
                                        <td width="100">Order Qty</td>
                                        <td width="90"><input type="text" name="txt_po_qty" id="txt_po_qty" class="text_boxes_numeric" style="width:80px" disabled readonly/></td>
                                    </tr>
                                    <tr>
                                        <td>Cuml. Ex-Factory Qty</td>
                                        <td><input type="text" name="txt_cumul_ex_fact_qty" id="txt_cumul_ex_fact_qty" class="text_boxes_numeric" style="width:80px" disabled readonly/></td>
                                    </tr>
                                    <tr>
                                        <td>Yet to Ex-Factory Qty</td>
                                        <td><input type="text" name="txt_yet_to_ex_fact" id="txt_yet_to_ex_fact" class="text_boxes_numeric" style="width:80px" disabled readonly/></td>
                                    </tr>
                                </table>
                            </fieldset>
                        </td>
                        <td width="40%" valign="top">
                            <div style="max-height:350px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="9" valign="middle" class="button_container">
                            <?
								$date=date('d-m-Y');
                                echo load_submit_buttons( $permission, "fnc_issue_print_embroidery_entry", 0,0 ,"reset_form('printembro_1','','txt_issue_date,".$date."','childFormReset()')",1);
                            ?>
                            <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly >
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                     <tr>
                    	<td colspan="10" align="center" id="data_panel"></td>
                    </tr> 
               </table>
               <div style="width:900px; margin-top:5px;" id="production_list_view" align="center"></div>
        </form>
        </fieldset>
    </div> 
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
