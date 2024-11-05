<?
/*-------------------------------------------- Comments 
Version          : V1
Purpose			 : This form will create Dyeing W/O Bill
Functionality	 :	
JS Functions	 :
Created by		 : Kausar 
Creation date 	 : 08-07-2020
Requirment Client: 
Requirment By    : 
Requirment type  : 
Requirment       : 
Affected page    : 
Affected Code    :              
DB Script        : 
Updated by 		 : 
Update date		 : 
QC Performed BY	 :		
QC Date			 :	
Comments		 : From this version oracle conversion is start
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Dyeing W/O Bill", "../../", 1, 1,$unicode,'','');
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	function openmypage_bill_no(page_link,title)
	{
		if(form_validation('cbo_company_name','Company Name')==false){ return; }
		var cbo_company_name=$('#cbo_company_name').val();
		var cbo_supplier_id=$('#cbo_supplier_id').val();
		page_link+='&company_id='+cbo_company_name+'&supplier_id='+cbo_supplier_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{	
			var menu_info=this.contentDoc.getElementById("selected_work_order").value; //alert(menu_info);
			if(menu_info.length){
				var data=menu_info.split('**');
				//console.log(data);
	
				$("#update_id").val(data[0]);
				$("#txt_bill_no").val(data[1]);
				$("#txt_bill_date").val(data[2]);
				$("#hidd_pay_mode").val(data[3]);
				$("#cbo_supplier_id").val(data[4]);
				$("#txt_wo_no").val(data[5]);
				$("#hidd_wo_id").val(data[6]);
				$("#txt_manual_bill_no").val(data[7]);
				
				$("#txt_billQty").val(data[9]);
				$("#txt_woAmt").val(data[10]);
				$("#txt_upCharge").val(data[11]);
				$("#txt_discount").val(data[12]);
				$("#txt_remark").val(data[13]);

				var posted_in_account=data[14]; // is posted accounct
				if(posted_in_account==1) document.getElementById("accounting_posted_status").innerHTML="Already Posted In Accounting.";
					else  document.getElementById("accounting_posted_status").innerHTML="";
				
				var grandTotal=(data[10]*1)+(data[11]*1)-(data[12]*1);
				$("#txt_grandTotal").val(grandTotal);
				
				$("#cbo_company_name").attr("disabled",true);
	
				set_button_status(1, permission, 'fnc_dyeing_wo_bill',1);
				$('#scanning_tbl tbody tr').remove();
				$("#total_row").val(0);
			
				var details_ids = trim(return_global_ajax_value(data[6]+'__'+data[0], 'populate_details_data', '', 'requires/dyeing_wo_bill_controller'));
					
				if(details_ids!=""){
					rows=details_ids.split("**");
					for(var i=0;i<rows.length;i++){
						row=rows[i];
						//console.log(row);
						create_row(row);
					}
					const el = document.querySelector('#print_button');
					  if (el.classList.contains("formbutton_disabled")) {
						el.classList.remove("formbutton_disabled");
					}
				}
			}
		}
	}
	
	function fnc_check(inc_id)
	{
		
		if(document.getElementById('checkid_'+inc_id).checked==true)
		{
			document.getElementById('checkid_'+inc_id).value=1;
		}
		else if(document.getElementById('checkid_'+inc_id).checked==false)
		{
			document.getElementById('checkid_'+inc_id).value=2;
		}
	}

	function fnc_wo_no()
	{
		var update_id=$('#update_id').val();
		if(update_id!="")
		{
			alert('Not allow . Try to new bill');
			return;
		}
		if(form_validation('cbo_company_name','Company Name')==false){ return; }
		var cbo_company_name=$('#cbo_company_name').val();
		var page_link='requires/dyeing_wo_bill_controller.php?action=wo_popup&company_id='+cbo_company_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'W/O Popup', 'width=950px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];       
			var theemailid=this.contentDoc.getElementById("hidd_wo_id").value;	
			var theemailno=this.contentDoc.getElementById("hidd_wo_no").value;	
			var theemailpay_mode=this.contentDoc.getElementById("hidd_pay_mode").value;
			var theemailsupplier=this.contentDoc.getElementById("hidd_supplier").value;
			var theemailcurrency=this.contentDoc.getElementById("hidd_currency").value;	
				
			//console.log(theemail.value);
			if (theemailid!="")
			{
				$("#hidd_wo_id").val(theemailid);
				$("#txt_wo_no").val(theemailno);
				$("#cbo_supplier_id").val(theemailsupplier).attr("disabled",true);
				$("#hidd_pay_mode").val(theemailpay_mode);
				$("#hidd_currency").val(theemailcurrency);
				$("#cbo_company_name").attr("disabled",true);
				
				var details_ids = trim(return_global_ajax_value(theemailid+'__0', 'populate_details_data', '', 'requires/dyeing_wo_bill_controller'));
				//alert(details_ids);
				//console.log(details_ids);
				//return;
				
				if(details_ids!=""){
					rows=details_ids.split("**");
					for(var i=0;i<rows.length;i++){
						row=rows[i];
						//console.log(row);
						create_row(row);
					}
					const el = document.querySelector('#print_button');
					  if (el.classList.contains("formbutton_disabled")) {
						el.classList.remove("formbutton_disabled");
					}
				}
			}
		}
	}

	function decplace(value)
	{
		return Number(Math.round(parseFloat(value + 'e' + 2)) + 'e-' + 2).toFixed(2);
	}

	function create_row(datas)
	{
		//alert(datas);
		console.log(datas);
		//return;
		var total_row=Number($("#total_row").val());
		var j=Number(total_row);
		var row_datas=datas.split("&&&&");
		var row_data=row_datas[0];
		var msg=0; var qty=0;
		
		var data=row_data.split("__");
		
		var dtlsup_id=''; var rate=''; var amount=''; var remark=''; var bill_qty=0;
		
		if(row_datas.length>1){
			var row_data_update=row_datas[1].split("__");
			dtlsup_id=row_data_update[0];
			bill_qty=row_data_update[1];
			rate=row_data_update[2];
			amount=row_data_update[3];
			remark=row_data_update[4];
			amount=decplace(amount);
			rate=decplace(rate);
			bill_qty=decplace(bill_qty) ;
		}
		
		if(dtlsup_id=='')
		{
			dtlsup_id='';
			bill_qty=data[16];
			rate=data[17];
			amount=data[18];
			remark='';
			amount=decplace(amount);
			rate=decplace(rate);
			bill_qty=decplace(bill_qty) ;
		}

		
		$("#scanning_tbl").find('tbody tr').each(function() {
			var checking_data = $(this).find('input[name="checking_data[]"]').val();
			if(trim(row_data) == trim(checking_data)){
				msg++;
				return;
			}
		});
		if(msg>0){
			alert("Program Already exists");
		}
		else
		{ 	
			var trColor="";
			if (j%2==0) trColor="#E9F3FF"; else trColor="#FFFFFF";
			
			j++;
			
			var html="<tr id='tr_"+j+"' bgcolor="+trColor+" align='center' valign='middle' >";
			html+="<td bgcolor='#CCFFCC'><input type='checkbox' name='checkid[]' id='checkid_"+j+"' checked onClick='fnc_check("+j+");' value='1'></td>";
			html+="<td>"+j+"<input type='hidden' value='"+data[8]+"' name='detailsId[]' id='detailsId_"+j+"'><input type='hidden' value='"+dtlsup_id+"' name='dtlsupId[]' id='dtlsupId_"+j+"'><input type='hidden' value='"+trim(row_data)+"' id='checking_data_"+j+"' name='checking_data[]'></td>";
			html+="<td style='word-break:break-all'>"+data[2]+" <input type='hidden' name='buyerId[]' id='buyerId_"+j+"' value='"+data[1]+"' /></td>";
			html+="<td style='word-break:break-all'>"+data[3]+"</td>";
			html+="<td style='word-break:break-all'>"+data[4]+"</td>";
			html+="<td style='word-break:break-all'>"+data[5]+"</td>";
			html+="<td style='word-break:break-all'>"+data[6]+" <input type='hidden' name='mstId[]' id='mstId_"+j+"' value='"+data[0]+"' /></td>";
			html+="<td style='word-break:break-all'>"+data[9]+"</td>";
			html+="<td style='word-break:break-all'>"+data[10]+"</td>";
			html+="<td style='word-break:break-all'>"+data[11]+"</td>";
			html+="<td style='word-break:break-all'>"+data[12]+"</td>";
			html+="<td style='word-break:break-all'>"+data[13]+"</td>";
			html+="<td style='word-break:break-all'>"+data[14]+"</td>";
			html+="<td style='word-break:break-all'>"+data[15]+"</td>";
			html+="<td><input type='text' name='woqnty[]' style='width:67px' id='woqnty_"+j+"' disabled class='text_boxes_numeric' value='"+(Math.round(data[16]*100)/100)+"' readonly/></td>";
			html+="<td><input type='text' name='billqty[]' style='width:67px' id='billqty_"+j+"' disabled class='text_boxes_numeric' onkeyup='calculateAmt();' value='"+bill_qty+"' /></td>";
			html+="<td><input type='text' name='rate[]' style='width:57px' id='rate_"+j+"' value='"+rate+"' disabled class='text_boxes_numeric' onkeyup='calculateAmt();' /></td>";
			html+="<td><input type='text' name='amount[]' style='width:67px' id='amount_"+j+"' value='"+amount+"' disabled class='text_boxes_numeric'  /></td>";
			html+="<td><input type='text' name='remark[]' style='width:105px' id='remark_"+j+"' value='"+remark+"' class='text_boxes' /></td>";
			html+="</tr>";
			//console.log(html);
			$('#scanning_tbl tbody').append(html);
			calculateAmt();
			$("#total_row").val(j);
			//alert(html);
		}
	}

	function calculateAmt()
	{
		var totWoQty=0; var totBillQty=0; var totBillAmt=0;
		$("#scanning_tbl").find('tbody tr').each(function() {
			
			var woqty = Number($(this).find('input[name="woqnty[]"]').val());
			totWoQty+=woqty;
			
			var billqty = Number($(this).find('input[name="billqty[]"]').val());
			totBillQty+=billqty;
			
			var billrate = Number($(this).find('input[name="rate[]"]').val());
			
			var amount=billqty*billrate;
			Number($(this).find('input[name="amount[]"]').val(amount));
			
			var billAmt = Number($(this).find('input[name="amount[]"]').val());
			totBillAmt+=billAmt;
		});
		$("#txt_woQty").val(Number(totWoQty));
		$("#txt_billQty").val(Number(totBillQty));
		$("#txt_woAmt").val(Number(totBillAmt));
		
		var upCharge=$("#txt_upCharge").val()*1;
		var discount=$("#txt_discount").val()*1;
		var grnadAmt=(totBillAmt+upCharge)-discount;
		
		$("#txt_grandTotal").val(Number(grnadAmt));
	}

	function fnc_dyeing_wo_bill(operation)
	{
		if (form_validation('cbo_company_name*txt_wo_no*txt_bill_date*cbo_supplier_id','Company Name*Work Order*Bill Date*Supplier')==false)
		{
			return;
		}
		var j = 0;
		var dataString = '';
		var total_amount_update=0;
    	var total_bill_qnty_update=0;
   		var total_wo_qnty_update=0;
		$("#scanning_tbl").find('tbody tr').each(function () {
	
			var detailsId = $(this).find('input[name="detailsId[]"]').val();
			var dtlsupId = $(this).find('input[name="dtlsupId[]"]').val();
			var buyerId = $(this).find('input[name="buyerId[]"]').val();
			var womstId = $(this).find('input[name="mstId[]"]').val();
			
			var billqty = $(this).find('input[name="billqty[]"]').val();
			var woqnty = $(this).find('input[name="woqnty[]"]').val();
			var rate = $(this).find('input[name="rate[]"]').val();
			var amount = $(this).find('input[name="amount[]"]').val();
			var remark = $(this).find('input[name="remark[]"]').val();
			var checkid = $(this).find('input[name="checkid[]"]').val();
			try {
			   
				j++;

				if(checkid==1){
	            	total_amount_update+=Number(amount);
	            	total_bill_qnty_update+=Number(billqty);
	            	total_wo_qnty_update+=Number(woqnty);
	            }
	
				dataString +='&detailsId_' + j + '=' + detailsId + '&dtlsupId_' + j + '=' + dtlsupId + '&buyerId_' + j + '=' + buyerId + '&womstId_' + j + '=' + womstId+ '&billqty_' + j + '=' + billqty+ '&rate_' + j + '=' + rate + '&amount_' + j + '=' + amount + '&remark_' + j + '=' + remark+ '&checkid_' + j + '=' + checkid+ '&woqnty_' + j + '=' + woqnty;
			}
			catch (e) {
				//got error no operation
			}
		});
	
		if (j < 1) {
			alert('No data');
			return;
		}
		var data = "action=save_update_delete&operation=" + operation + '&tot_row=' + j + '&total_amount_update=' + total_amount_update + '&total_bill_qnty_update=' + total_bill_qnty_update + '&total_wo_qnty_update=' + total_wo_qnty_update + get_submitted_data_string('update_id*txt_bill_no*cbo_company_name*txt_wo_no*hidd_wo_id*hidd_pay_mode*hidd_currency*txt_bill_date*cbo_supplier_id*txt_manual_bill_no*txt_remark*txt_woQty*txt_billQty*txt_woAmt*txt_upCharge*txt_discount*txt_grandTotal', "../../") + dataString;
		freeze_window(operation);
		http.open("POST","requires/dyeing_wo_bill_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_dyeing_wo_bill_reponse;
	}

	function fnc_dyeing_wo_bill_reponse()
	{
		if(http.readyState == 4) 
		{
			 var response=trim(http.responseText).split('**');
			 console.log(http.responseText);
			 
			 if(response[0]==20)
			 {
				alert(response[1]);
				release_freezing();
				return;
			 }
			
			 show_msg(trim(response[0]));
			 if(response[0]==0 || response[0]==1)
			 {
				$("#txt_bill_no").val(response[2]);
				$("#update_id").val(response[1]);
				//reset_table();
				set_button_status(1, permission, 'fnc_dyeing_wo_bill',1);
				$('#scanning_tbl tbody tr').remove();
				$("#total_row").val(0);
				
				var details_ids = trim(return_global_ajax_value($("#hidd_wo_id").val()+'__'+response[1], 'populate_details_data', '', 'requires/dyeing_wo_bill_controller'));
					
				if(details_ids!=""){
					rows=details_ids.split("**");
					for(var i=0;i<rows.length;i++){
						row=rows[i];
						//console.log(row);
						create_row(row);
					}
				}
			 }
			 if(response[0]==2)
			 {
				reset_form('dyeingwobill_2','','','','','');
				$('#scanning_tbl tbody tr').remove();
				$("#total_row").val(0);
				set_button_status(0, permission, 'fnc_dyeing_wo_bill',1);
			 }
			 release_freezing();
		}
	}
	function reset_table()
	{
		$('#scanning_tbl tbody tr').remove();
	    $("#total_bill_qnty").text("");
	    $("#totalBillQnty").val(0);

	    $("#total_amount").text("");
	    $("#total_wo_qnty").text("");
	    $("#totalWoQnty").val(0);
	    $("#totalamount").val(0);

	  	$("#discount").val(0);
	    
	    $("#upcharge").val(0);
	    
	    $("#grand_total").val(0);

	   

	}

	function fnc_print_dyeing_bill()
	{
		if (form_validation('txt_bill_no*cbo_company_name','Bill No*Company Name')==false)
		{
			return;
		}
		/*var r=confirm("Press \"OK\" to open with Rate & Amount column\nPress \"Cancel\" to open without Rate & Amount column");
		if (r==true)
		{
			show_val_column="1";
		}
		else
		{
			show_val_column="0";
		}*/
		var report_title=$( "div.form_caption" ).html();
		
		print_report( $('#update_id').val()+"**"+$('#cbo_company_name').val()+"**"+report_title, "print_dyeing_bill", "requires/dyeing_wo_bill_controller");
		//show_msg(3);
		return;
	}

</script>
</head>

<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="dyeingwobill_1"  autocomplete="off" id="dyeingwobill_1">
        <fieldset style="width:950px;">
        <legend>Dyeing W/O Bill</legend>
            <table width="900" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td align="right" class="must_entry_caption" colspan="3">Bill No </td>
                    <td colspan="3">
                    	<input class="text_boxes" type="text" style="width:140px" onDblClick="openmypage_bill_no('requires/dyeing_wo_bill_controller.php?action=bill_popup','Bill Search')" readonly placeholder="Double Click for Bill" name="txt_bill_no" id="txt_bill_no"/>
                    	<input type="hidden" name="update_id" id="update_id">
                    </td>                       
                </tr>
                <tr>
                	<td width="130" class="must_entry_caption">Company Name</td>
                    <td width="170"><? echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name", "id,company_name",1, "--Select Company--", $selected, "load_drop_down('requires/dyeing_wo_bill_controller', this.value+'_0', 'load_drop_down_supplier', 'supplier_td' );","","" ); ?>	  
                    </td>
                    <td width="130" class="must_entry_caption">WO No</td>
                    <td width="170">
                    	<input type="text" class="text_boxes" name="txt_wo_no" placeholder="Browse WO No" onDblClick="fnc_wo_no();" id="txt_wo_no" style="width: 140px;" readonly="readonly">
                    	<input type="hidden" name="hidd_wo_id" id="hidd_wo_id" >
                        <input type="hidden" name="hidd_pay_mode" id="hidd_pay_mode" >
                        <input type="hidden" name="hidd_currency" id="hidd_currency" >
                    </td>
                    <td width="130" class="must_entry_caption">Bill Date</td>
                    <td><input class="datepicker" type="text" style="width:140px" name="txt_bill_date" id="txt_bill_date" value="<? echo date("d-m-Y")?>" /></td>
                </tr>
                <tr>
                    <td class="must_entry_caption">Supplier Name</td>
                    <td id="supplier_td"><? echo create_drop_down( "cbo_supplier_id", 150, $blank_array,"", 1, "-- Select Supplier --", $selected, "",1 ); ?></td> 
                    <td>Manual Bill No</td>
                    <td><input type="text" class="text_boxes" placeholder="Manual Bill No" name="txt_manual_bill_no" id="txt_manual_bill_no" style="width: 140px;"></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>Remarks</td>
                	<td colspan="3"><input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:443px;"></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </fieldset>
    </form>
    <br/>
	<div id="accounting_posted_status" style=" color:red; font-size:24px;" align="left"></div>
    <form name="dyeingwobill_2"  autocomplete="off" id="dyeingwobill_2">   
        <fieldset style="width:1710px;">
        <legend>Bill Details</legend>
            <table  width="1700" cellspacing="2" cellpadding="0" border="1" rules="all" class="rpt_table" id="scanning_tbl">
                <thead>
	                <tr>
                    	<th width="50">Check Box</th>
	                    <th width="30">SL No</th>
	                    <th width="70">Buyer</th>
	                    <th width="100">Style Ref.</th>
	                    <th width="110">Fab. Booking No</th>
	                    <th width="110">FSO No</th>
	                    <th width="110">WO No</th>
	                    <th width="80">Body Part</th>
	                    <th width="160">Fabric Description</th>
	                    <th width="100">Color Name</th>
	                    <th width="80">Color Range</th>
	                    <th width="70">Shade %</th>
	                    <th width="70">Process Loss %</th>
	                    <th width="130">Process Name</th>
	                    <th width="80">Wo Qty.</th>
	                    <th width="80">Bill Qty.</th>
	                    <th width="70">Rate</th>
	                    <th width="80">Amount</th>
	                    <th>Remarks</th>
	                </tr>
	            </thead>
	            <tbody>
	            	
	            </tbody>
	            <tfoot>
	            	<tr align='center' valign='middle' bgcolor="#CCCCCC" >
						<td colspan='13'><input type="hidden" name="total_row" id="total_row" value="0"></td>
                        <td>Total:</td>
						<td><input type="text" name="txt_woQty" id="txt_woQty" class="text_boxes_numeric" style="width:70px" value="" readonly disabled></td>
						<td><input type="text" name="txt_billQty" id="txt_billQty" class="text_boxes_numeric" style="width:70px" value="" readonly disabled></td>
						<td>&nbsp;</td>
						<td><input type="text" name="txt_woAmt" id="txt_woAmt" class="text_boxes_numeric" style="width:70px" value="" readonly disabled></td>
						<td>&nbsp;</td>
					</tr>
                    <tr align='center' valign='middle' bgcolor="#CCCCCC">
						<td colspan='17' align="right">Upcharge:</td>
						<td><input type="text" name="txt_upCharge" id="txt_upCharge" class="text_boxes_numeric" style="width:70px" value="" onBlur="calculateAmt();" ></td>
						<td>&nbsp;</td>
					</tr>
                    <tr align='center' valign='middle' bgcolor="#CCCCCC">
						<td colspan='17' align="right">Discount:</td>
						<td><input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" style="width:70px" value="" onBlur="calculateAmt();" ></td>
						<td>&nbsp;</td>
					</tr>
                    <tr align='center' valign='middle' bgcolor="#CCCCCC">
						<td colspan='17' align="right">Grand Total:</td>
						<td><input type="text" name="txt_grandTotal" id="txt_grandTotal" class="text_boxes_numeric" style="width:70px" value="" readonly></td>
						<td>&nbsp;</td>
					</tr>
	            	<tr>
	            		<td colspan="19" align="center" class="button_container">
	            			<? echo load_submit_buttons($permission, "fnc_dyeing_wo_bill", 0, "", "reset_form('*dyeingwobill_1*dyeingwobill_2','','','','','');", 1); ?>
	            			 <a id="print_button" style="cursor: pointer;border: outset 1px #66CC00;text-decoration: none;width:100px;height: 60px;" target="_blank" class="formbutton formbutton_disabled" onClick="fnc_print_dyeing_bill();"> &nbsp;&nbsp;&nbsp;Print&nbsp;&nbsp;&nbsp;
	            			 </a>
	            		</td>
	            	</tr>
	            </tfoot>
            </table>
            <div id="booking_list_view"></div>
        </fieldset>
    </form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>