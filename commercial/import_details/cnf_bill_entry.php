<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create for C and F Bill Entry

Functionality	:

JS Functions	:

Created by		:	Nayem
Creation date 	: 	26-1-2021
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
//-------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("C and F Bill Entry", "../../",  1, 1, $unicode,1,'');

$cnf_import_bill_head=implode(',',array_keys($cnf_import_bill_head_arr));
$cnf_export_bill_head=implode(',',array_keys($cnf_export_bill_head_arr));

?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
    var permission='<? echo $permission; ?>';

	function openmypage_Invoice()
	{
		var cbo_country_id = document.getElementById('cbo_company_name').value;
		var cbo_type_name = document.getElementById('cbo_type_name').value;
		
		if(form_validation('cbo_type_name','C&F Type')==false ){
			return;
		}
	
		var page_link='requires/cnf_bill_entry_controller.php?action=invoice_popup_search&cbo_type_name='+cbo_type_name+'&cbo_country_id='+cbo_country_id;
		var title='C and F Bill Entry';
	
	
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=990px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hidden_info=this.contentDoc.getElementById("hidden_info").value.split('_');
			
			$('#invoice_id').val( hidden_info[0] );
			$('#cbo_company_name').val( hidden_info[5] );
			$('#cbo_buyer_name').val( hidden_info[1] );
			$('#txt_invoice_date').val( hidden_info[3] );
			$('#txt_invoice_no').val( hidden_info[2] );
			$('#txt_invoice_value').val( hidden_info[4]);
			$('#txt_exp_no').val( hidden_info[6] );
			$('#txt_exp_date').val( hidden_info[7]);
			$('#txt_value_qty').val( hidden_info[8]);
			$('#txt_pack_qty').val( hidden_info[9]);
			$('#txt_gross').val( hidden_info[10]);
			$('#txt_sb_no').val( hidden_info[11]);
			$('#cbo_shipment_id').val( hidden_info[12]);
			$('#txt_lc_no').val( hidden_info[13] );
			$('#cbo_supplier_name').val( hidden_info[14] );
			// var company_id=this.contentDoc.getElementById("company_id").value;
			// var invoice_date=this.contentDoc.getElementById("invoice_date").value;
			// var invoice_no=this.contentDoc.getElementById("invoice_no").value;
			// var invoice_value=this.contentDoc.getElementById("invoice_value").value;
			// var buyer_name=this.contentDoc.getElementById("buyer_name").value;
			// var exp_form_no=this.contentDoc.getElementById("exp_form_no").value;
			// var exp_form_date=this.contentDoc.getElementById("exp_form_date").value;
			// var btb_lc_no=this.contentDoc.getElementById("btb_lc_no").value;
			// var supplier_id=this.contentDoc.getElementById("supplier_id").value;
			// $('#cbo_company_name').val( company_id );
			// $('#cbo_buyer_name').val( buyer_name );
			// $('#txt_invoice_date').val( invoice_date );
			// $('#txt_invoice_no').val( invoice_no );
			// $('#txt_invoice_value').val( invoice_value );
			// //alert(exp_form_no+"="+exp_form_date);
			// $('#txt_exp_no').val( exp_form_no );
			// $('#txt_exp_date').val( exp_form_date );
			// $('#txt_lc_no').val( btb_lc_no );
			// $('#cbo_supplier_name').val( supplier_id );
			
			$('#txt_value_bdt').val( '');
			// $('#txt_ex_rate').val( '');
			$('#txt_invoice_no').attr('readonly',true);
			$('#txt_value_qty').attr('readonly',true);
			fn_ex_rate($('#txt_ex_rate').val());
			fn_Pack_laber(hidden_info[9]);
		}
	}

	function fn_ex_rate(rate)
	{
		var invoice_value = parseFloat(document.getElementById('txt_invoice_value').value);
		var cbo_type_name=$('#cbo_type_name').val();
		if(invoice_value){
		$('#txt_value_bdt').val( (invoice_value * rate).toFixed(2));
		var cost_bdt=parseFloat(trim($('#txt_value_bdt').val())) ;
			if(cbo_type_name==1 && cost_bdt){
				if(cost_bdt>2000000){
					var tax= ((+(1000000*0.005) + +(1000000*0.003) + +((cost_bdt-2000000)*0.002)) * 0.1);
				}else if(cost_bdt>1000000){
					var tax= ((+(1000000*0.005) + +((cost_bdt-1000000)*0.003)) * 0.1);
				}else{
					var tax= ((+(cost_bdt*0.005)) * 0.1);
				}
				// var tax= ((+(cost_bdt*0.005) + +(cost_bdt*0.003) + +(cost_bdt*0.002)) * 0.1);
				$('#txtamount_1').val( tax.toFixed(2).replace(/\.?0+$/, ''));
			}
		}
		
		if(cbo_type_name==1)
		{
			fn_formula(13);
		}
	}

	function fn_charge(bill,type)
	{
		if(type==1)
		{
			$('#txtcostper_'+bill).val('').attr('disabled',true);
			$('#txtcost_'+bill).val('').attr('disabled',true);
		}
		else if(type==2)
		{
			if(bill==13)
			{ 
				$('#txtcost_'+bill).val(0.06);
				fn_formula(13);
			}
			if (bill==6)
			{
				var cbo_company_name=$('#cbo_company_name').val();
				var cbo_type_name=$('#cbo_type_name').val();
				var txt_gross=$('#txt_gross').val();
				get_php_form_data(cbo_company_name+'**'+cbo_type_name+'**'+txt_gross, "populate_data_from_landing_charge", "requires/cnf_bill_entry_controller" );
			}
			$('#txtcostper_'+bill).attr('disabled',false);
			$('#txtcost_'+bill).attr('disabled',false);
		}
	}

	function fn_container(type)
	{
		show_list_view(type, 'load_bill_table', 'bill_td', 'requires/cnf_bill_entry_controller', 'setFilterGrid(\'bill_td\',-1)');
		if(type==1)
		{
			$('#cbo_container_name').attr('disabled',false);
			// $('#txtamount_3').val( 30);
			// $('#txtamount_9').val( 100);
			// $('#txtamount_12').val( 1000);
		}
		else if(type==2 || type==0)
		{
			$('#cbo_container_name').attr('disabled',true);
			$('#cbo_container_name').val(0);
			$('#txt_container_rate').val('');
		}
		fn_total();
	}

	function fn_container_rate(type)
	{
		var ex_rate=parseInt(trim($('#txt_ex_rate').val()));
		if(type==1)
		{
			$('#txt_container_rate').val(1.5);
			if(ex_rate){
				$('#txtamount_2').val( ex_rate*1.5);
				var value_1= $('#txtamount_1').val();
				var value_2= $('#txtamount_2').val();
				var value_3= $('#txtamount_3').val();
				if(value_1 && value_2 && value_3){
					var tot_amount=(parseFloat(value_1)+parseFloat(value_2)+parseFloat(value_3));
					$('#txtamount_4').val(tot_amount);
				}
			}
		}else if(type==2){
			$('#txt_container_rate').val(3);
			if(ex_rate){
				$('#txtamount_2').val( ex_rate*3);
				var value_1= $('#txtamount_1').val();
				var value_2= $('#txtamount_2').val();
				var value_3= $('#txtamount_3').val();
				if(value_1 && value_2 && value_3){
					var tot_amount=(parseFloat(value_1)+parseFloat(value_2)+parseFloat(value_3));
					$('#txtamount_4').val(tot_amount);
				}
			}
		}else if(type==0){
			$('#txt_container_rate').val('');
		}
		fn_total();
	}
	
	function fn_c_and_f_bill_entry( operation )
	{
		if($("#hidden_posted_in_account").val()*1==1)
		{
			alert("Already Posted In Accounts.Save,Update & Delete Not Allowed.");
            return;
		}
        
		if (form_validation('cbo_company_name*cbo_type_name*cbo_candf_name*txt_ex_rate*txt_pack_qty*txt_bill_no*txt_bill_date*cbo_shipment_id','Company Name*C&F Type*C&F Name*Ex. Rate*Pack. Qty.*Bill NO*Bill Date*Ship Mode')==false)
		{
			release_freezing();
			return;
		}	
		var cbo_type = document.getElementById('cbo_type_name').value;
		if(cbo_type==1){
			var row_num_export ='<?= $cnf_export_bill_head;?>'; 
		var row_num_arr = row_num_export.split(',');
		}
		
		if(cbo_type==2){
			var row_num_import ='<?= $cnf_import_bill_head;?>'; 
		var row_num_arr = row_num_import.split(',');
		}
		var row_num =row_num_arr.length; 
		var data_mst=get_submitted_data_string('txt_system_id*cbo_company_name*cbo_type_name*cbo_candf_name*txt_ex_rate*txt_invoice_no*txt_invoice_date*txt_invoice_value*txt_value_bdt*txt_value_qty*txt_pack_qty*txt_gross*txt_bill_no*txt_bill_date*cbo_buyer_name*txt_sb_no*txt_job_no*cbo_shipment_id*cbo_container_name*txt_container_rate*txt_remarks*invoice_id*txtamount_tot*cbo_approve_status*update_id',"../../");
		// alert(data_mst);
		var data_panel="";
		for(var ii=0; ii<row_num; ii++)
		{
			var i=row_num_arr[ii];
			data_panel += '&txtbillid_' + i + '=' + $('#txtbillid_'+i).val() + '&cbo_formula_' + i + '=' + $('#cbo_formula_'+i).val()+ '&txtcost_' + i + '=' + $('#txtcost_'+i).val() + '&txtcostper_' + i + '=' + $('#txtcostper_'+i).val() + '&txtamount_' + i + '=' + $('#txtamount_'+i).val()+ '&txtdaducation_' + i + '=' + $('#txtdaducation_'+i).val()+ '&txtpaybale_' + i + '=' + $('#txtpaybale_'+i).val(); 		
		}
		var data="action=save_update_delete&operation="+operation+data_mst+"&row_num_arr="+row_num_arr+"&data_panel="+data_panel;
		//alert(data); return;
		freeze_window(operation);
		http.open("POST","requires/cnf_bill_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_c_and_f_bill_reponse;
	}
	
    function fn_c_and_f_bill_reponse()
	{
        if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1)
			{
				document.getElementById('txt_system_id').value=reponse[1];
				document.getElementById('update_id').value=reponse[2];
				disable_enable_fields('cbo_company_name*cbo_type_name*cbo_candf_name*txt_invoice_no',1);
				set_button_status(1, permission, 'fn_c_and_f_bill_entry',1);
			}
			if(parseInt(trim(reponse[0]))==2)
			{
				location.reload();
			}
			show_msg(trim(reponse[0]));
			release_freezing();
		}
    }
	
    function openmypage_sys_no()
	{
		if(form_validation('cbo_type_name','C&F Type')==false )
		{
			return;
		}            
		var cbo_company_name=$('#cbo_company_name').val();
		var cbo_type_name=$('#cbo_type_name').val();
		var page_link='requires/cnf_bill_entry_controller.php?action=system_popup&cbo_company_name='+cbo_company_name+'&cbo_type_name='+cbo_type_name;
		var title='C and F Bill Form';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=430px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_id");
			if (theemail.value!="")
			{
				freeze_window(5);
				get_php_form_data(theemail.value, "populate_data_from_search_popup", "requires/cnf_bill_entry_controller" );
				set_button_status(1, permission, 'fn_c_and_f_bill_entry',1);
				release_freezing();
			}
		}
	}
	
    function fn_formula(id)
	{
        var cbo_type_name=$('#cbo_type_name').val();
		if($('#txtcost_'+id).val()*1==0)
		{
			var cost_perc=0.06;
		}
		else
		{
			var cost_perc=parseFloat(trim($('#txtcost_'+id).val())) ;
		}
        
        var cost_tk=parseFloat(trim($('#txtcostper_'+id).val())) ;
        var cost_bdt=parseFloat(trim($('#txt_value_bdt').val())) ;
		if(cbo_type_name==1)
		{
			// if(cost_perc && cost_bdt){
			if(cost_bdt)
			{
				if(id==13)
				{
					var cost_amount = (cost_perc/100) * cost_bdt;
					//var cost_amount = 0.0006 * cost_bdt;
					if(cost_amount<=1000)
					{
						$('#txtamount_'+id).val( 1000);
					}
					else if(cost_amount>=5000)
					{
						$('#txtamount_'+id).val( 5000);
					}
					else if(cost_amount > 1000 && cost_amount < 5000)
					{
						$('#txtamount_'+id).val( cost_amount.toFixed(2).replace(/\.?0+$/, ''));
					}
					//$('#txtcost_'+id).val( 0.06);
				}
			}
			
		}

		if(cbo_type_name==2)
		{
			if(!cost_perc || !cost_bdt){
			return;
			}else{
				if(id==20)
				{
					var cost_amount = cost_perc * cost_bdt;
					if(cost_amount<=1000){
						$('#txtamount_'+id).val( 1000);
					}else if(cost_amount>=5000){
						$('#txtamount_'+id).val( 5000);
					}else if(cost_amount > 1000 && cost_amount < 5000){
						$('#txtamount_'+id).val( cost_amount);
					}
				}
			}
		}
    }
	
	function fn_Pack_laber(value)
	{
		var cbo_type=$('#cbo_type_name').val();
		if(cbo_type==1 && trim(value))
		{
			$('#txtamount_5').val( trim(value)*3);
		}
		fn_total();
	}
	
	function fn_total(str)
	{
        var cbo_type = document.getElementById('cbo_type_name').value;
		if(cbo_type==1){
			var row_num_export ='<?= $cnf_export_bill_head;?>'; 
			var row_num_arr = row_num_export.split(',');
			var row_num =row_num_arr.length; 
			var data_panel=0; var txt_ammount=0;
		    var txt_daducation=0; var total_payable=0;
			var txt_payable_tot=0; var txt_daducation_tot=0; 
			txt_ammount=$('#txtamount_'+str).val()*1; 
			txt_daducation=$('#txtdaducation_'+str).val()*1; 
				if(txt_ammount<=0){
					$('#txtdaducation_'+str).val(0);
					txt_daducation=0;
				}
			total_payable=txt_ammount-txt_daducation;
			$('#txtpaybale_'+str).val(total_payable.toFixed(2)); 
			for(var ii=0; ii<row_num; ii++)
			{
				var i=row_num_arr[ii];
				if(($('#txtamount_'+i).val()*1)!=0) 
				{
					// if(i!=4) data_panel+=$('#txtamount_'+i).val()*1; 
				    data_panel+=$('#txtamount_'+i).val()*1; 
					txt_ammount=$('#txtamount_'+i).val()*1; 
					txt_daducation=$('#txtdaducation_'+i).val()*1; 
					total_payable=txt_ammount-txt_daducation;
		            $('#txtpaybale_'+i).val(total_payable.toFixed(2));
		            txt_payable_tot+=total_payable;
	             	txt_daducation_tot+=txt_daducation;
					
				}			
			}			       
		}
		if(cbo_type==2){
				var row_num_import ='<?= $cnf_import_bill_head;?>'; 
				var row_num_arr = row_num_import.split(',');
				var row_num =row_num_arr.length; 
				// var data_panel=0;
				// for(var ii=0; ii<row_num; ii++)
				// {
				// 	var i=row_num_arr[ii];
				// 	if(($('#txtamount_'+i).val()*1)!=0) 
				// 	{
				// 		data_panel+=$('#txtamount_'+i).val()*1; 
				// 	}			
				// }
				var data_panel=0; var txt_ammount=0;
				var txt_daducation=0; var total_payable=0;
				var txt_payable_tot=0; var txt_daducation_tot=0; 
				txt_ammount=$('#txtamount_'+str).val()*1; 
				txt_daducation=$('#txtdaducation_'+str).val()*1; 
					if(txt_ammount<=0){
						$('#txtdaducation_'+str).val(0);
						txt_daducation=0;
					}
				total_payable=txt_ammount-txt_daducation;
				$('#txtpaybale_'+str).val(total_payable.toFixed(2)); 
				for(var ii=0; ii<row_num; ii++)
				{
					var i=row_num_arr[ii];
					if(($('#txtamount_'+i).val()*1)!=0) 
					{
						// if(i!=4) data_panel+=$('#txtamount_'+i).val()*1; 
						data_panel+=$('#txtamount_'+i).val()*1; 
						txt_ammount=$('#txtamount_'+i).val()*1; 
						txt_daducation=$('#txtdaducation_'+i).val()*1; 
						total_payable=txt_ammount-txt_daducation;
						$('#txtpaybale_'+i).val(total_payable.toFixed(2));
						txt_payable_tot+=total_payable;
						txt_daducation_tot+=txt_daducation;
						
					}			
				}
	    }
		
		$('#txtamount_tot').val( data_panel.toFixed(2) );
		$('#txtdaducation_tot').val( txt_daducation_tot.toFixed(2) );
		$('#txtpaybale_tot').val( txt_payable_tot.toFixed(2) );
	}

	function fn_ex_rate_lib(company_id)
	{
		get_php_form_data(company_id, "ex_rate_lib", "requires/cnf_bill_entry_controller" );
	}
</script>
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission); ?>
        <div>
            <form name="candfbillentry_1" id="candfbillentry_1" autocomplete="off" data-entry_form="105">
                <fieldset style="width:1150px;">
                    <legend>C and F Bill Entry</legend>
                    <table width="100%" border="0" cellpadding="0" cellspacing="2" id="tbl_btb">
                        <tr>
                            <td colspan="5" align="right"><strong>System ID</strong></td> 
                            <td colspan="5">
                                <input style="width:140px;" type="text" title="Double Click to Search" onDblClick="openmypage_sys_no()" class="text_boxes" placeholder="Browse" name="txt_system_id" id="txt_system_id" readonly />
                                <input type="hidden" id="update_id">
                                <input type="hidden" id="hidden_posted_in_account" name="hidden_posted_in_account" value="" />
                            </td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Company</td>
                            <td>
                            <?
                            echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "--- Select Company ---",  $cbo_country_id, "fn_ex_rate_lib(this.value);");
                            ?>
                            </td>
                            <td class="must_entry_caption">C&F Type</td>
                            <td><?
                                echo create_drop_down( "cbo_type_name",120,array(1=>"Export",2=>"Import"),'',1,'--Select--',0,"fn_container(this.value)",0);
                            ?></td>
                            <td class="must_entry_caption">C&F Name</td>
                            <td>
                            <?
							echo create_drop_down( "cbo_candf_name",120,"select a.id, a.supplier_name FROM lib_supplier a , lib_supplier_party_type b WHERE a.id= b.supplier_id and b.party_type=30 and a.STATUS_ACTIVE=1 AND a.IS_DELETED=0","ID,supplier_name", 1, "-- Select --", 0, "" );
							?>
							</td>
							<td>Invoice No</td>
                            <td><input type="text" name="txt_invoice_no" id="txt_invoice_no" style="width:110px" class="text_boxes" placeholder="Double Click to Search" onDblClick="openmypage_Invoice()" />
							<input type="hidden" name="invoice_id" id="invoice_id">
							</td>
                            <td  class="must_entry_caption">Ex. Rate</td>
                            <td><input type="text" name="txt_ex_rate" id="txt_ex_rate" style="width:110px" class="text_boxes_numeric" onBlur="fn_ex_rate(this.value)"></td>
                        </tr>
                        <tr>
                            <td width="100">Invoice Date</td>
                            <td width="130"><input style="width:110px " name="txt_invoice_date" id="txt_invoice_date" class="datepicker"  placeholder="Display" disabled/></td>
                            <td width="100">Invoice Value</td>
                            <td width="130"><input type="text" name="txt_invoice_value" id="txt_invoice_value" style="width:110px" class="text_boxes" placeholder="Display" disabled></td>
                            <td width="100">Inv. Value BDT</td>
                            <td width="130"><input type="text" name="txt_value_bdt" id="txt_value_bdt" style="width:110px" class="text_boxes" placeholder="Display" disabled></td>
                            <td width="100">Inv. Qty.</td>
                            <td width="130"><input type="text" name="txt_value_qty" id="txt_value_qty" style="width:110px" class="text_boxes" ></td>
                            <td class="must_entry_caption">Pack. Qty.</td>
                            <td><input type="text" name="txt_pack_qty" id="txt_pack_qty" style="width:110px" class="text_boxes_numeric"  onkeyup="fn_Pack_laber(this.value)"></td>
                        </tr>
                        <tr>
                            <td>Gross Weight</td>
                            <td><input type="text" name="txt_gross" id="txt_gross" style="width:110px" class="text_boxes" ></td>
                            <td class="must_entry_caption">Bill NO</td>
                            <td><input type="text" name="txt_bill_no" id="txt_bill_no" style="width:110px" class="text_boxes" ></td>
                            <td class="must_entry_caption">Bill Date</td>
                            <td><input style="width:110px " name="txt_bill_date" id="txt_bill_date" class="datepicker" placeholder="Select Date" /></td>
                            <td>Buyer</td>
                            <td>
                            <?
                           echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy where buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, " Display ", 0, "",1 ); 
                            ?>
                            </td>
                            <td>S/B No</td>
                            <td><input type="text" name="txt_sb_no" id="txt_sb_no" style="width:110px" class="text_boxes" ></td>
                        </tr>
                        <tr>
                            <td>Job No.</td>
                            <td><input type="text" name="txt_job_no" id="txt_job_no" style="width:110px" class="text_boxes" ></td>
                            <td class="must_entry_caption">Ship Mode</td>
                            <td>
                            <? 
							echo create_drop_down( "cbo_shipment_id", 120, $shipment_mode,"", 1, "-- Select --", 0, "" );
                            ?>
                            </td>
                            <td>Container</td>
                            <td ><?
							echo create_drop_down( "cbo_container_name",80,array(1=>"20",2=>"40"),'',1,'--Select--',0,"fn_container_rate(this.value)",1);
							?>
							<input type="text" name="txt_container_rate" id="txt_container_rate" style="width:30px" class="text_boxes" disabled>
							</td>
                            <td>Remarks</td>
                            <td colspan="3"><input type="text" name="txt_remarks" id="txt_remarks" style="width:305px" class="text_boxes" ></td>
                        </tr>
                        <tr>
                            <td>EXP No</td>
                            <td><input type="text" name="txt_exp_no" id="txt_exp_no" style="width:110px" class="text_boxes" readonly /></td>
                            <td>EXP Date</td>
                            <td><input type="text" name="txt_exp_date" id="txt_exp_date" style="width:110px" class="datepicker" readonly disabled /></td>
                            <td>LC Number</td>
                            <td><input type="text" name="txt_lc_no" id="txt_lc_no" style="width:110px" class="text_boxes" readonly /></td>
                            <td>Supplier Name</td>
                            <td>
                            <?
                           echo create_drop_down( "cbo_supplier_name", 120, "select id, supplier_name from lib_supplier where is_deleted=0 order by supplier_name","id,supplier_name", 1, " Display ", 0, "",1 ); 
                            ?>
                            </td>

							<td>Ready To Approve</td>
                            <td><? echo create_drop_down( "cbo_approve_status", 110, $yes_no,"", 1, "-- Select --", "", "","" ); ?> </td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
    				</table>
                    <br>
                    <div id="bill_td"></div>
                    <div id="is_posted_accounts" style="float:left; font-size:24px; color:#FF0000;"></div>
                    <? echo load_submit_buttons( $permission, "fn_c_and_f_bill_entry", 0,0,"reset_form('candfbillentry_1','','','','','txt_system_id')",1); ?>
                </fieldset>
            </form>
        </div>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>