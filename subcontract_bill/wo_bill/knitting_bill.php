<?
/*-------------------------------------------- Comments 
Version          : V1
Purpose			 : This form will create knitting work order
Functionality	 :	
JS Functions	 :
Created by		 : Md. Helal Uddin 
Creation date 	 : 6-07-2020
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
echo load_html_head_contents("Knitting Bill", "../../", 1, 1,$unicode,'','');
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";



function select_item_pop()
{
	var update_id=$('#update_id').val();
	if(update_id!="")
	{
		alert("Not Allow , Try To New Bill");
		return;
	}
	if (form_validation('cbo_company_name*cbo_supplier_name','Company Name*Supplier')==false)
	{
		return;
	}
	var cbo_company_name=$('#cbo_company_name').val();
	var cbo_supplier_name=$('#cbo_supplier_name').val();
	var page_link='requires/knitting_bill_controller.php?action=issue_no_pop&company_id='+cbo_company_name+'&supplier_id='+cbo_supplier_name;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Select Item Popup', 'width=980px,height=450px,center=1,resize=1,scrolling=0','../../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];       
		var theemail=this.contentDoc.getElementById("selected_work_order");	
		//console.log(theemail.value);
		if (theemail.value!="")
		{
			var rows_data=trim(theemail.value).split('***');
			//console.log(rows_data);
			var pay_mode;
			for( var j=0;j<rows_data.length;j++)
			{
				var wo_row=rows_data[j].split("__");
				var details_ids = trim(return_global_ajax_value(wo_row[0], 'populate_details_data', '', 'requires/knitting_bill_controller'));
				//console.log(details_ids);
				pay_mode=wo_row[1];
	            if(details_ids!=""){
	            	rows=details_ids.split("**");
	            	//console.log(rows);
	            	for(var i=0;i<rows.length;i++){
	            		row=rows[i];
	            		//console.log(row);
	            		create_row(row);
	            	}
	            	
	            	//document.getElementById("print_button").disabled = false; 
	            	
	            }



			}
			$("#pay_mode").val(pay_mode);
			const el = document.querySelector('#print_button');
			if (el.classList.contains("formbutton_disabled")) {
			   // el.classList.remove("formbutton_disabled");
			}
		}
	}
}
function openmypage_wo_no(page_link,title)
{
	var cbo_company_name=$('#cbo_company_name').val();
	var cbo_supplier_name=$('#cbo_supplier_name').val();
		// var update_id=$('#update_id').val();
	page_link+='&company_id='+cbo_company_name+'&cbo_supplier_name='+cbo_supplier_name;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1090px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{	
		
		var menu_info=this.contentDoc.getElementById("selected_work_order").value; 
		
		var posted_in_account=this.contentDoc.getElementById("hidden_posted_in_account").value; // is posted accounct
		if(posted_in_account==1) document.getElementById("accounting_posted_status").innerHTML="Already Posted In Accounting.";
			else  document.getElementById("accounting_posted_status").innerHTML="";

		//alert(menu_info);
        if(menu_info!=""){
            var data=menu_info.split('__');
           // console.log(data);
           
             reset_table();
             reset_master();
            $("#update_id").val(data[0]);
            $("#txt_bill_wo").val(data[1]);
            $("#cbo_company_name").val(data[2]).attr('disabled', 'disabled');
            $("#txt_booking_date").val(data[3]);
            $("#cbo_pay_mode").val(data[4]).attr('disabled', 'disabled');
            $("#cbo_supplier_name").val(data[5]).attr('disabled', 'disabled');
            $("#txt_manual_bill").val(data[8]);
            $("#txt_remark").val(data[9]);
            

            var tot_wo_qty=data[10];
            var tot_bill_qty=data[11];
            var tot_bill_amt=data[12];
            var upchage=data[13];
            var discount=data[14];
           $("#total_row").val(0);
            

           
            set_button_status(1, permission, 'fnc_knitting_bill',1);
           
            var details_ids = trim(return_global_ajax_value(data[0], 'populate_details_data_save', '', 'requires/knitting_bill_controller'));
            console.log(details_ids);
            if(details_ids!=""){
            	rows=details_ids.split("**");
            	//console.log(details_ids);
            	for(var i=0;i<rows.length;i++){
            		row=rows[i];
            		//console.log(row);
            		create_row(row);
            	}
            	const el = document.querySelector('#print_button');
				  if (el.classList.contains("formbutton_disabled")) {
				    el.classList.remove("formbutton_disabled");

				}

				
				$("#total_bill_qnty").text(tot_bill_qty);
			    $("#totalBillQnty").val(tot_bill_qty);
			    $("#total_amount").text(tot_bill_amt);
			    $("#totalamount").val(tot_bill_amt);
			    $("#upcharge").val(upchage);
			    $("#discount").val(discount);
			    

			   	calculate();
			   	//document.getElementById("print_button").disabled = false; 
            	
            }


            
            
            
        }
		
	}
}


function fnc_knitting_bill(operation)
{
	if (form_validation('cbo_company_name',' Company Name')==false)
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
        var wodtlsId = $(this).find('input[name="wodtlsId[]"]').val();
        var buyerid = $(this).find('input[name="buyerid[]"]').val();
        var fabricsaleorderno = $(this).find('input[name="fabricsaleorderno[]"]').val();
        var bookingno = $(this).find('input[name="bookingno[]"]').val();
        var woid = $(this).find('input[name="woid[]"]').val();
        var woqty = $(this).find('input[name="woqty[]"]').val();
        var billqty = $(this).find('input[name="billqty[]"]').val();
		//var Recvqty = $(this).find('input[name="Recvqty[]"]').val();
        var checkid = $(this).find('input[name="checkid[]"]').val();
        
        var rate = $(this).find('input[name="rate[]"]').val();
        var amount = $(this).find('input[name="amount[]"]').val();
        
        try {
           
            j++;
            if(checkid==1){
            	total_amount_update+=Number(amount);
            	total_bill_qnty_update+=Number(billqty);
            	total_wo_qnty_update+=Number(woqty);
            }

            dataString +='&detailsId_' + j + '=' + detailsId + '&wodtlsId_' + j + '=' + wodtlsId + '&buyerid_' + j + '=' + buyerid + '&fabricsaleorderno_' + j + '=' + fabricsaleorderno + '&bookingno_' + j + '=' + bookingno + '&woid_' + j + '=' + woid + '&woqty_' + j + '=' + woqty+'&billqty_'+j+'='+ billqty+ '&rate_' + j + '=' + rate + '&amount_' + j + '=' + amount + '&checkid_' + j + '=' + checkid ;
        }
        catch (e) {
            //got error no operation
        }
    });

    if (j < 1) {
        alert('No data');
        return;
    }
   // alert(total);
    //return;
    var data = "action=save_update_delete&operation=" + operation + '&tot_row=' + j  + '&total_amount_update=' + total_amount_update + '&total_bill_qnty_update=' + total_bill_qnty_update + '&total_wo_qnty_update=' + total_wo_qnty_update + get_submitted_data_string('txt_bill_wo*update_id*cbo_company_name*txt_booking_date*cbo_supplier_name*txt_manual_bill*txt_remark*po_breakdown_id*discount*grand_total*upcharge*totalamount*totalWoQnty*totalBillQnty', "../../") + dataString;
//	alert(data);
    freeze_window(operation);
	http.open("POST","requires/knitting_bill_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_knitting_bill_reponse;
	
}
function fnc_knitting_bill_reponse()
{
	if(http.readyState == 4) 
	{
		 var response=trim(http.responseText).split('**');
		 console.log(http.responseText);
		 //release_freezing();

		 //return;
		 
		 if(response[0]==20)
		 {
			 alert(response[1]);
			 release_freezing();
			 return;
		 }

		 show_msg(trim(response[0]));
		 if(response[0]==0 || response[0]==1)
		 {
		 	//release_freezing();
			$("#txt_bill_wo").val(response[2]);
			$("#update_id").val(response[1]);
			reset_table();

			
		 	
			reset_table();
			$("#total_row").val(0);
			//reset_form('knitting_bill_details','','','','','');
			//console.log(response[1]);
			var details_ids = trim(return_global_ajax_value(response[1], 'populate_details_data_save', '', 'requires/knitting_bill_controller'));
			console.log(details_ids);
				
			if(details_ids!=""){
				rows=details_ids.split("**");
				for(var i=0;i<rows.length;i++){
					row=rows[i];
					//console.log(row);
					create_row(row);
				}

			}
			const el = document.querySelector('#print_button');
			  if (el.classList.contains("formbutton_disabled")) {
			    el.classList.remove("formbutton_disabled");

			}
			$("#upcharge").val(response[3]);
			$("#discount").val(response[4]);
			set_button_status(1, permission, 'fnc_knitting_bill',1);
			calculate();
			
		 }

		 else if(response[0]==2)
		 {
			
			//release_freezing();
			reset_table();
			reset_master();
			//reset_form('knitting_bill_master','','','','','');
			
			
		 	set_button_status(0, permission, 'fnc_knitting_bill',1);

			/*reset_form('','','txt_booking_no*txt_order_no*cbo_company_name*txt_order_no_id*txt_job_no*cbo_buyer_name*cbo_currency*txt_exchange_rate*txt_booking_date*txt_delivery_date*cbo_pay_mode*txt_remark*cbo_supplier_name*txt_attention','txt_booking_date,<? //echo date("d-m-Y"); ?>'); 
			*/
		 }
		 release_freezing();
		 
		 
	}
}
function print_knitting_bill_wo_order()
{
	if (form_validation('txt_bill_wo*cbo_company_name','Bill No*Company Name')==false)
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();
		
		print_report( $('#update_id').val()+"**"+$('#cbo_company_name').val()+"**"+report_title, "print_knitting_bill", "requires/knitting_bill_controller");
		
		return;
}
function create_row(datas){
		console.log(datas);
		var msg=0;
		var total_row=Number($("#total_row").val());
		var qty=0;
		var j=Number(total_row);
		var row_datas=datas.split("&&&&");
		var row_data=row_datas[0];
		//console.log('row_data='+row_data);
		var details_id='';
		var rate='';
		var amount='';
		var wo_qty=0;
		var bill_qty=0;var recv_qty=0;
		var fab_pcs_qnty=0;
		
		var data=row_data.split("__");
		if(row_datas.length>1)
		{
			var row_data_update=row_datas[1].split("__");
			details_id=row_data_update[0];
			wo_qty=row_data_update[1]
			bill_qty=row_data_update[2]
			rate=row_data_update[3];
			amount=row_data_update[4];
			balance_qty=row_data_update[5];
			fab_pcs_qnty=row_data_update[6];
			recv_qty=row_data_update[7]*1;
			 //alert(recv_qty);
		}
		else
		{
			rate=(Math.round(data[20] * 100) / 100).toFixed(2);
			amount=(Math.round(data[21] * 100) / 100).toFixed(2);
			fab_pcs_qnty=(Math.round(data[22] * 100) / 100).toFixed(2);
			wo_qty=(Math.round(data[16] * 100) / 100).toFixed(2);
			bill_qty=(Math.round(data[15] * 100) / 100).toFixed(2);
			balance_qty=(Math.round(data[15] * 100) / 100).toFixed(2);
			recv_qty=(Math.round(data[23] * 100) / 100).toFixed(2);
			// alert('A='+recv_qty);
			if(recv_qty) bill_qty=recv_qty;else bill_qty=bill_qty;
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
        	
        	j++;

        	//console.log('else');
        	var html="<tr id='tr_"+j+"' align='center' valign='middle' >";
        	html+="<td bgcolor='#CCFFCC'><input type='checkbox' name='checkid[]' checked id='checkid_"+j+"' onClick='fnc_check("+j+");' value='1'></td>";
			html+="<td>"+j+" <input type='hidden' name='sl[]' id='sl_"+j+"' value='"+j+"' /><input type='hidden' value='"+trim(row_data)+"' id='checking_data_"+j+"' name='checking_data[]'><input type='hidden' value='"+details_id+"' name='detailsId[]' id='detailsId_"+j+"'><input type='hidden' value='"+data[18]+"' name='wodtlsId[]' id='wodtlsId_"+j+"'></td>";
			html+="<td>"+data[1]+" <input type='hidden' name='buyerid[]' id='buyerid_"+j+"' value='"+data[0]+"' />   </td>";
			html+="<td>"+data[2]+" <input type='hidden' name='styleref[]' id='styleref_"+j+"' value='"+data[2]+"' /></td>";
			html+="<td>"+data[3]+" <input type='hidden' name='bookingno[]' id='bookingno_"+j+"' value='"+data[3]+"' /></td>";
			html+="<td>"+data[4]+" <input type='hidden' name='fabricsaleorderno[]' id='fabricsaleorderno_"+j+"' value='"+data[4]+"' /></td>";
			html+="<td>"+data[5]+" <input type='hidden' name='wono[]' id='wono_"+j+"' value='"+data[5]+"' /> <input type='hidden' name='woid[]' id='woid_"+j+"' value='"+data[19]+"' /></td>";
			html+="<td>"+data[6]+" <input type='hidden' name='fabricdesc[]' id='fabricdesc_"+j+"' value='"+data[6]+"' /></td>";
			
			html+="<td>"+data[10] +" * "+data[11]+" <input type='hidden' name='dia[]' id='dia_"+j+"' value='"+data[10]+"' /> <input type='hidden' name='machingg[]' id='machingg_"+j+"' value='"+data[11]+"' /></td>";
			html+="<td>"+data[12]+" <input type='hidden' name='stitchlength[]' id='stitchlength_"+j+"' value='"+data[12]+"' /></td>";
			html+="<td>"+data[14]+" <input type='hidden' name='colorrange[]' id='colorrange_"+j+"' value='"+data[13]+"' /></td>";
			html+="<td>"+fab_pcs_qnty+" <input type='hidden' name='fabpcsqnty[]' disabled='disabled' id='fabpcsqnty_"+j+"' value='"+fab_pcs_qnty+"' /></td>";
			html+="<td>"+wo_qty+" <input type='hidden' name='woqty[]' disabled='disabled' id='woqty_"+j+"' value='"+wo_qty+"' /></td>";
			html+="<td><input type='text' name='Recvqty[]' id='Recvqty_"+j+"'class='text_boxes_numeric' value='"+recv_qty+"' disabled='disabled'/></td>";

			
			
			html+="<td><input type='text' name='billqty[]' id='billqty_"+j+"'class='text_boxes_numeric' onkeyup='calculate()' value='"+bill_qty+"'/><input type='hidden' name='balance[]' disabled='disabled' id='balance_"+j+"' value='"+balance_qty+"' /></td>";
			
			html+="<td><input type='text' name='rate[]' id='rate_"+j+"' value='"+rate+"' class='text_boxes_numeric' onkeyup='calculate()' disabled='disabled' /></td>";
			html+="<td><input type='text' name='amount[]' id='amount_"+j+"' value='"+amount+"' class='text_boxes_numeric' disabled='disabled'  /></td>";
			
			//html+="<td><input type='button'  onclick='fn_deleteRow("+j+")' class='formbuttonplasminus' value='-' style='width:30px' /></td>";
			html+="</tr>";
			//console.log(html);
			$('#scanning_tbl tbody').append(html);
			//calculate_total_qnty();
			$("#total_row").val(j);
			//alert(html);
			var wo_qty=0;
			
			
			$("input[name='woqty[]'").map(function (key){
				wo_qty+=Number($(this).val());
			});

			$("#totalWoQnty").val((Math.round(wo_qty*100)/100).toFixed(2));
			$("#total_wo_qnty").text((Math.round(wo_qty*100)/100).toFixed(2));

			var bill=0;
			$("input[name='billqty[]']").map( function(key){
		       bill+=Number($(this).val());
		    });
		    $("#total_bill_qnty").text((Math.round(bill*100)/100).toFixed(2));
    		$("#totalBillQnty").val((Math.round(bill*100)/100).toFixed(2));
    		calculate();
			
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

function calculate()
{
	var rate=[];
	var j=0;
	var total_wo_qnty=0;
	var total_bill_qnty=0;
	var total_amount=0;
	$("input[name='rate[]']").map( function(key){
		
       rate[j]=Math.round(Number($(this).val())*100) /100;
       j=j+1;
    });

    var billqty=[];
    var totalwoqty=0;
    var total_balance_qty=0;
    j=0;
    $("input[name='billqty[]']").map( function(key){
       billqty[j]=Math.round(Number($(this).val())*100) /100;
       total_bill_qnty+=billqty[j];
       j=j+1;
    });
    $("input[name='balance[]']").map( function(key){
       total_balance_qty+=Math.round(Number($(this).val())*100) /100;
    });
    j=0;
    $("input[name='amount[]']").map( function(key){
       var num=Math.round((rate[j]*billqty[j])*100)/100;
       $(this).val(num);
       total_amount+=num;
       j=j+1;
    });
    j=0;
    $("input[name='woqty[]']").map( function(key){
       totalwoqty+=Math.round(Number($(this).val())*100) /100;
       j=j+1;
    });
    if(total_bill_qnty>total_balance_qty)
    {
    	alert("Bill Qty Can Not Greater Then WO Qty");
	    $('input[name="billqty[]"]').val(0);
	    total_bill_qnty=0;
    }
    $("#total_bill_qnty").text(Math.round(total_bill_qnty*100)/100);
    $("#totalBillQnty").val(Math.round(total_bill_qnty*100)/100);

    $("#total_amount").text(Math.round(total_amount*100)/100);
    $("#totalamount").val(Math.round(total_amount*100)/100);

    var discount=Number($("#discount").val());
    
    var upcharge=Number($("#upcharge").val());
    var t=Number(Number(total_amount)-Number(discount));
    t=Number(t+Number(upcharge));
    $("#grand_total").val(Math.round(t*100)/100);
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
function reset_master()
{
	$("#update_id").val("");
    $("#txt_bill_wo").val("");
    $("#cbo_company_name").val("").prop("disabled", false);
    var today = new Date();
    var dd = today.getDate(); 
    var mm = today.getMonth() + 1; 
    var yyyy = today.getFullYear(); 
    if (dd < 10) { 
        dd = '0' + dd; 
    } 
    if (mm < 10) { 
        mm = '0' + mm; 
    } 
    var today = dd + '-' + mm + '-' + yyyy; 
    
   $("#txt_booking_date").val(today);
    $("#cbo_pay_mode").val("").prop("disabled", false);
    $("#cbo_supplier_name").val("").prop("disabled", false);
    $("#txt_manual_bill").val("");
    $("#txt_remark").val("");
}

</script>

</head>

<body >
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="knitting_bill_master"  autocomplete="off" id="knitting_bill_master">
        <fieldset style="width:950px;">
        <legend>Knitting Bill</legend>
            <table  width="900" cellspacing="2" cellpadding="0" border="0" style="">
                <tr>
                    <td width="130" align="right" class="must_entry_caption" colspan="3">WO No </td>              <!-- 11-00030  -->
                    <td width="170" colspan="3">
                    	<input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_wo_no('requires/knitting_bill_controller.php?action=work_order_popup','Knitting Bill WO')" readonly placeholder="Double Click for Dyeing WO" name="txt_bill_wo" id="txt_bill_wo"/>
                    	<input type="hidden" name="update_id" id="update_id">
                    	<input type="hidden" name="pay_mode" id="pay_mode">
                    </td>                       
                    <td></td>
                </tr>
                <tr>
                    <td class="must_entry_caption">Company Name</td>
                    <td>
						<? 
                            echo create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name", "id,company_name",1, "-- Select Company --", $selected, "","","" );
                        ?>	  
                    </td>
                    <td width="130">Bill Date</td>
                    <td width="170">
                    	<input class="datepicker  hasDatepicker" type="text" style="width:160px" name="txt_booking_date" id="txt_booking_date"  value="<? echo date("d-m-Y")?>"  />	
                    </td>
                    <td class="must_entry_caption">Selected FSO No</td>
                    <td >
                    	<input type="text" class="text_boxes" name="cbo_fso_no" placeholder="Browse FSO No" onDblClick="select_item_pop()" id="cbo_fso_no" style="width: 160px;">
                    	<input type="hidden" name="po_breakdown_id" id="po_breakdown_id" >
                    </td>
                </tr>
                <tr>
                	<td class="must_entry_caption">Supplier</td>
                    <td id="supplier_td">
						<?
                            echo create_drop_down( "cbo_supplier_name", 172, "select id,supplier_name from lib_supplier where  status_active =1 and is_deleted=0 and party_type =  '20' or party_type like '20,%' or party_type like '%,20' or party_type like'%,20,%' order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );
                        ?> 
                    </td> 
                    <td>Manual Bill No</td>
                    <td><input type="text" name="txt_manual_bill" id="txt_manual_bill" class="text_boxes" style="width: 160px;"></td>
                </tr>
                <tr>
                	<td> Remark</td>
                	<td colspan="3"><input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width: 97%;"></td>
                	
                </tr>
                
            </table>
           
        </fieldset>
    </form>
    <br/>
	<div id="accounting_posted_status" style=" color:red; font-size:24px;" align="left"></div>
    <form name="knitting_bill_details"  autocomplete="off" id="knitting_bill_details">   

        <fieldset style="width:1200px;">
        <legend>Details</legend>
            <table  width="1630" cellspacing="2" cellpadding="0" border="0" class="rpt_table" id="scanning_tbl">
                <thead>
	                <tr>
	                	<th width="30">Check Box</th>
	                    <th width="35">SL No</th>
	                    <th width="100">Buyer Name </th>
	                    <th width="100">Style Ref. No</th>
	                    <th width="110">Fab. Booking No</th>
	                    <th width="100">FSO No</th>
	                    <th width="110">WO No</th>
	                    <th width="180">Fabric Description</th>
	                    <th width="80">M/C Dia x Gauge</th>
	                    
	                    <th width="50">S.L</th>
	                    <th width="120">Color Range</th>
	                    <th width="100">Fabric Qty(Pcs.)</th>
	                    <th width="80">Wo Qty.</th>
                         <th width="80">Recv. Qty.</th>
	                    <th  width="80">Bill Qty.</th>
	                    <th width="80">Rate.</th>
	                    <th >Amount.</th>
	                </tr>
	            </thead>
	            <tbody>
	            	
	            </tbody>
	            <tfoot>
	            	<tr align='center' valign='middle' >

						<td colspan='11' align="right">
							Total:
							<input type="hidden" name="total_row" id="total_row" value="0">
						</td>
						<td  align='center' >
							<span id="total_fab_qnty" ></span>
							<input type='hidden' name='totalFabQnty' id='totalFabQnty' value='0'  />
						</td>
						<td  align='center' >
							<span id="total_wo_qnty" ></span>
							<input type='hidden' name='totalWoQnty' id='totalWoQnty' value='0'  />
						</td>
                        <td  align='right' >
                        <span id="total_revQty" ></span>
							<input type='hidden' name='totalRecvQnty' id='totalRecvQnty' value='0'  />
                        </td>
						<td  align='right' >
							<span id="total_bill_qnty"></span>

							<input type='hidden' name='totalBillQnty' id='totalBillQnty' value='0'  />
						</td>
						
						<td  align='right' id="total_rate">
							<input type='hidden' name='totalRate' id='totalRate' value='0'  />
						</td>
						<td align="right">
							<span id="total_amount"></span>
							<input type='hidden' name='totalamount' id='totalamount' value='0'  />
						</td>
						
					</tr>
					<tr>
						<td colspan="11" align="right">Upcharge:</td>
						<td colspan="5" align="right"><input type="text" style="width: 380px;" name="upcharge" class="text_boxes_numeric" id="upcharge" placeholder="Upcharge" onKeyUp="calculate()"></td>
					</tr>
					<tr>
						<td colspan="11" align="right">Discount:</td>
						<td colspan="5" align="right"><input type="text" name="discount" style="width: 380px;" class="text_boxes_numeric" id="discount" placeholder="Discount" onKeyUp="calculate()"></td>
					</tr>
					<tr>
						<td colspan="11" align="right">Grand Total:</td>
						<td colspan="5" align="right"><input type="text" name="grand_total" style="width: 380px;" class="text_boxes_numeric" id="grand_total" placeholder="Discount"></td>
					</tr>

	            	<tr>
	            		<td colspan="22" align="center">

	            			<? 

	            				//echo load_submit_buttons($permission, "fnc_knitting_bill", 0, "", "reset_form('knitting_bill_master','','','','','');reset_table()", 1);

	            				echo load_submit_buttons($permission, "fnc_knitting_bill", 0, "", "reset_table();reset_master()", 1); ?>
	            			 

	            			 <a id="print_button" style="cursor: pointer;border: outset 1px #66CC00;text-decoration: none;width:100px;height: 60px;" target="_blank" class="formbutton formbutton_disabled" onClick="print_knitting_bill_wo_order()">
	            			 	&nbsp;&nbsp;&nbsp;Print&nbsp;&nbsp;&nbsp;
	            			 </a>
	            		</td>
	            		
	            	</tr>
	            </tfoot>
                
            </table>
            
             
        </fieldset>
    </form>
</div>
</body>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>