<?
/************************************************Comments************************************
Purpose			: 	This form will create Embellishment Dyes And Chemical Issue Entry
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	14-01-2019
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
**********************************************************************************************/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

$user_id = $_SESSION['logic_erp']['user_id'];

// user credential data prepare start

$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, company_location_id, supplier_id, buyer_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$company_location_id = $userCredential[0][csf('company_location_id')];

$company_credential_cond=$store_location_credential_cond=$company_location_credential_cond="";
if ($company_id !='') {
    $company_credential_cond = " and comp.id in($company_id) ";
}
if ($store_location_id !='') {
    $store_location_credential_cond = " and a.id in($store_location_id)";
}
if ($company_location_id !='') {
    $company_location_credential_cond = " and id in($company_location_id)";
}

echo load_html_head_contents("Dyes And Chemical Issue","../../", 1, 1, $unicode,'',''); 
//--------------------------------------------------------------------------------------------------------------------
// last yarn receive exchange rate, currency,store name
/*$sql = sql_select("select store_id,max(id) from inv_issue_master where item_category in(5,6,7)");
$storeName=$exchangeRate=$currencyID=0;
foreach($sql as $row)
{
	$storeName=$row[csf("store_id")];
}*/

?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	// enable disable field for independent
	function fn_independent(val)
	{
		if(val==0)
		{
			$("#cbo_store_name").attr("disabled",true);
			$('#txt_req_id').val("");
			$('#txt_req_no').val("");
			
			$("#txt_req_no").attr("disabled",false);
			$('#txt_req_no').removeAttr('placeholder','No Need');
			$('#txt_req_no').attr('placeholder','Double Click');
			
			$('#txt_ext_no').val("");
			$('#txt_batch_weight').val("");
			$('#txt_tot_liquor').val("");
			
		}
		if(val==4)
		{
			$("#cbo_store_name").attr("disabled",false);
			$('#txt_req_id').val("");
			$('#txt_req_no').val("");
			
			$("#txt_req_no").attr("disabled",true);
			$('#txt_req_no').attr('placeholder','No Need');
			
			$('#txt_ext_no').val("");
			$('#txt_batch_weight').val("");
			$('#txt_tot_liquor').val("");
			load_drop_down( 'requires/chemical_dyes_issue_controller', $('#cbo_company_name').val(), 'load_drop_down_color', 'sub_process_td');
			
		}
		if(val==7)
		{
			$("#cbo_store_name").attr("disabled",true);
			$("#txt_req_no").attr("disabled",false);
			$('#txt_req_no').removeAttr('placeholder','No Need');
			$('#txt_req_no').attr('placeholder','Double Click');
			
			$('#txt_ext_no').val("");
			$('#txt_batch_weight').val("");
			$('#txt_tot_liquor').val("");
		}
	}
	
		
	function fnc_chemical_dyes_issue_entry(operation)
	{
		if(operation==4)
		{
			if($('#update_id').val()<1)
			{
				alert("Save Data First.");return;
			}
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_mrr_no').val()+'*'+$('#cbo_location_name').val(),'chemical_dyes_issue_print','requires/chemical_dyes_issue_controller');

		}
		else if(operation==0 || operation==1 || operation==2)
		{
			/*if(operation==2)
			{
				show_msg('13');
				return;
			}*/
			
			
			var row_num=$('#tbl_list_search tbody tr').length;
			
			if( form_validation('cbo_company_name*cbo_issue_basis*txt_issue_date*cbo_sub_process*cbo_store_name','Company Name*Issue Basis*Issue Date*Sub-Process*Store Name')==false )
			{
				return;
			}
			var current_date = '<? echo date("d-m-Y"); ?>';
			if (date_compare($('#txt_issue_date').val(), current_date) == false) 
			{
				alert("Issue Date Can not Be Greater Than Current Date");
				return;
			}
			if( $("#cbo_issue_basis").val()==7)
			{
				var msg=msg2="";
				for(var i=1; i<row_num; i++)
				{
					if( ($('#txt_reqn_qnty_edit_'+i).val()*1)>0)
					{
						if($('#transId_'+i).val()*1!="")
						{
							if(($('#stock_qty_'+i).val()*1+$('#hidtxt_reqn_qnty_edit_'+i).val()*1)<($('#txt_reqn_qnty_edit_'+i).val()*1))
							{
								msg ="Issue Qnty Over Stock Qnty Not Allowed \n";
							}
						}
						else
						{
							if(($('#stock_qty_'+i).val()*1)<($('#txt_reqn_qnty_edit_'+i).val()*1))
							{
								msg ="Issue Qnty Over Stock Qnty Not Allowed \n";
							}
						}
						
						if( ($('#hidtxt_reqn_qnty_edit_'+i).val()*1)==0)
						{
							msg ="Issue Qnty zero Not Allowed \n";
						}
						if(($('#txt_reqn_qnty_edit_'+i).val()*1)>($('#txt_reqn_qnty_'+i).val()*1))
						{
							msg ="Issue Qnty Over The Requisition Qnty Not Allowed \n";
						}
					}
				}
				
				if(msg!="")
				{
					alert(msg);
					return;
				}
			}
			
			var dataString = "txt_mrr_no*update_id*cbo_company_name*cbo_location_name*cbo_issue_basis*cbo_issue_purpose*cbo_loan_party*txt_req_no*txt_req_id*txt_issue_date*txt_recipe_no*txt_recipe_id*txt_job_no*txt_order_no*hidden_order_id*cbo_store_name*txt_buyer_po*hidden_buyer_po_id*cbo_sub_process*txt_buyer_style*cbo_flore_name*cbo_machine_name*txt_remarks";
			var mst_data=get_submitted_data_string(dataString,"../../");
			var dtls_data="";
			for (var i=1; i<row_num; i++)
			{
				if(($('#txt_reqn_qnty_edit_'+i).val()*1)>0)
				{
					dtls_data=dtls_data+get_submitted_data_string('txt_prod_id_'+i+'*txt_lot_'+i+'*txt_item_cat_'+i+'*cbo_dose_base_'+i+'*txt_ratio_'+i+'*txt_recipe_qnty_'+i+'*txt_adj_per_'+i+'*cbo_adj_type_'+i+'*txt_reqn_qnty_'+i+'*txt_reqn_qnty_edit_'+i+'*updateIdDtls_'+i+'*transId_'+i+'*hidtxt_reqn_qnty_edit_'+i,"../../",2);
				}
			}
			
			var all_data=mst_data+dtls_data;
			var data="action=save_update_delete&operation="+operation+'&total_row='+row_num+all_data;
			//alert (data); return;
			freeze_window(operation);
			http.open("POST","requires/chemical_dyes_issue_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_chemical_dyes_issue_entry_reponse;
		}
	}
	
	function fnc_chemical_dyes_issue_entry_reponse()
	{	
		if(http.readyState == 4) 
		{
			// alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			release_freezing();
			
			if (reponse[0]*1 == 20)
			{
				alert(reponse[1]);
				return;
			}
			show_msg(reponse[0]);
			if((reponse[0]==0 || reponse[0]==1 || reponse[0]==2))
			{
				disable_enable_fields( 'cbo_company_name*cbo_issue_basis', 1, '', '' ); //*cbo_sub_process
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_mrr_no').value = reponse[2];
				show_list_view(reponse[1], 'recipe_item_details', 'recipe_items_list_view','requires/chemical_dyes_issue_controller', '' );
				document.getElementById('cbo_sub_process').value=0;
				reset_form( '', 'list_container_recipe_items', '', '', '', '' ); 
				set_button_status(reponse[3], permission, 'fnc_chemical_dyes_issue_entry',1,1);		
			}
			release_freezing();
			//$("#btn_cost_print").removeClass("formbutton_disabled");
			//$("#btn_cost_print").addClass("formbutton");
			//	
		}
		
	}
	
	function fnResetForm()
	{
		fn_independent(0)
		set_button_status(0, permission, 'fnResetForm',1);
		reset_form('chemicaldyesissue_1','list_container_yarn','','','','');
	}
	
	function open_reqpopup()
	{
		if( form_validation('cbo_company_name*cbo_issue_basis','Company*Issue Basis')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();
		var issue_purpose = $("#cbo_issue_purpose").val();	
		var issue_basis = $("#cbo_issue_basis").val();	
		var store_id = 0;
		var page_link='requires/chemical_dyes_issue_controller.php?action=req_popup&company='+company+'&issue_purpose='+issue_purpose+'&issue_basis='+issue_basis+'&store_id='+store_id;
		//alert(page_link);return; 
		var title="Search Requisition Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=400px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var requ_data_ref=this.contentDoc.getElementById("hidden_requ_data").value.split("_");
			
			$("#txt_req_no").val(requ_data_ref[0]);
			$("#txt_req_id").val(requ_data_ref[1]);
			$("#txt_recipe_no").val(requ_data_ref[3]);
			$("#txt_recipe_id").val(requ_data_ref[2]);
			$("#txt_job_no").val(requ_data_ref[4]);
			$("#txt_order_no").val(requ_data_ref[5]);
			$("#hidden_order_id").val(requ_data_ref[6]);
			$("#txt_buyer_style").val(requ_data_ref[7]);
			$("#txt_buyer_po").val(requ_data_ref[8]);
			$("#hidden_buyer_po_id").val(requ_data_ref[9]);
			$("#cbo_store_name").val(requ_data_ref[10]);
			$("#cbo_recipe_for").val(requ_data_ref[11]);
			
			$('#cbo_company_name').attr('disabled',true);
			$('#cbo_store_name').attr('disabled',true);
			document.getElementById("list_container_recipe_items").innerHTML = "";
			load_drop_down( 'requires/chemical_dyes_issue_controller', company+"**"+requ_data_ref[1], 'load_drop_down_sub_process', 'sub_process_td');
			fn_sub_process_enable(requ_data_ref[10]);
		}
	}

	
	function fnc_item_details(sub_process_id,is_update,store_id)
	{
		if(store_id!="") $('#cbo_store_name').val(store_id);
		var subId=''; var breakOut = true;
		$(".accordion_h").each(function() 
		{
			 var tid=$(this).attr('id'); 
			 var sid=tid+"span";
			 var id=tid+"id";
			 $('#'+sid).html("+");
			 
			 subId=$('#'+id).html();
			 if(is_update=="" && sub_process_id==subId)
			 {
				 breakOut=false;
				 return false;
			 }
		});

		if(breakOut==false)
		{
			alert("This Color/Process Already Saved.");
			$('#cbo_sub_process').val(0);
			return;	
		}
		
		if (form_validation('cbo_company_name*cbo_issue_basis*cbo_store_name','Company*Issue Basis*Store Name')==false)
		{
			return;
		}
		$('#accordion_h'+sub_process_id+'span').html("-");
		var txt_recipe_id= $('#txt_recipe_id').val();
		var cbo_company_id= $('#cbo_company_name').val();
		var cbo_store_name= $('#cbo_store_name').val();
		var cbo_issue_basis= $('#cbo_issue_basis').val();
		var txt_req_id= $('#txt_req_id').val();
		if(is_update!="")
		{
			load_drop_down( 'requires/chemical_dyes_issue_controller', sub_process_id+"**"+is_update, 'load_drop_down_sub_process_up', 'sub_process_td');
			
		}
		$('#cbo_sub_process').val(sub_process_id);
		var hidden_posted_account=$("#hidden_posted_account").val();
		
		show_list_view(cbo_company_id+'**'+sub_process_id+"**"+txt_recipe_id+"**"+cbo_issue_basis+"**"+is_update+"**"+txt_req_id+"**"+cbo_store_name+"**"+hidden_posted_account, 'item_details', 'list_container_recipe_items', 'requires/chemical_dyes_issue_controller', '');
		if(is_update !="")
		{
			set_button_status(1, permission, 'fnc_chemical_dyes_issue_entry',1,1);
		}
		else
		{
			set_button_status(0, permission, 'fnc_chemical_dyes_issue_entry',1,0);
		}
		$('#cbo_store_name').attr('disabled','disabled');
		var tableFilters = 
		 {
			col_0: "none",
			col_7: "none",
			col_8: "none",
			col_9: "none",
			col_10: "none"
		 }
		
	   setFilterGrid("tbl_list_search",-1,tableFilters);
	
	}
	
	function calculate_receipe_qty(i)
	{
		var cbo_dose_base = $("#cbo_dose_base_"+i).val();
		var txt_ratio = $("#txt_ratio_"+i).val();	
		var txt_tot_liquor = $("#txt_tot_liquor").val();
		var txt_batch_weight = $("#txt_batch_weight").val();
		var txt_adj_per = $("#txt_adj_per_"+i).val();
		var cbo_adj_type = $("#cbo_adj_type_"+i).val();	
	    var recipe_qnty=0;
	    if(cbo_dose_base==1)
	    {
		  recipe_qnty=(txt_tot_liquor*txt_ratio) /1000;
	    }
	    if(cbo_dose_base==2)
	    {
		  recipe_qnty=(txt_batch_weight*txt_ratio) /100;
	    }
		var required_qty=recipe_qnty;
		$("#txt_recipe_qnty_"+i).val(number_format_common(recipe_qnty,5,0));
		if(txt_adj_per !="")
		{
			var adjust_percent=recipe_qnty*txt_adj_per/100
			
			if(cbo_adj_type==1)
			{
				required_qty=required_qty+adjust_percent;
			}
			if(cbo_adj_type==2)
			{
				required_qty=required_qty-adjust_percent;
			}
		
		}
		$("#txt_reqn_qnty_"+i).val(number_format_common(required_qty,5,0));
		$("#txt_reqn_qnty_edit_"+i).val(number_format_common(required_qty,5,0));
	}
	
	function open_mrrpopup()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var page_link='requires/chemical_dyes_issue_controller.php?action=mrr_popup&company='+company; 
		var title="Search Issue Number Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=460px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var mrrNumber=this.contentDoc.getElementById("hidden_issue_number").value; // mrr number
			mrrNumber = mrrNumber.split("_"); 
			$("#txt_mrr_no").val(mrrNumber[0]);
			$("#update_id").val(mrrNumber[1]);
			$("#hidden_posted_account").val(mrrNumber[4]);
			$('#cbo_sub_process').attr('disabled',false);

			
			document.getElementById("list_container_recipe_items").innerHTML = "";
			show_list_view(mrrNumber[1], 'recipe_item_details', 'recipe_items_list_view', 'requires/chemical_dyes_issue_controller', '' ) ;
			get_php_form_data(mrrNumber[1], "populate_data_from_data", "requires/chemical_dyes_issue_controller");
			var batch_id= document.getElementById('txt_batch_id').value;
			set_button_status(0, permission, 'fnc_chemical_dyes_issue_entry',1,1);	
			$("#btn_cost_print").removeClass("formbutton_disabled");
			$("#btn_cost_print").addClass("formbutton");

			
			disable_enable_fields( 'cbo_company_name*cbo_issue_basis*cbo_dying_source', 1, '', '' );
		}
	}
	
	function check_data(id,stock_value)
	{
		if(parseInt(stock_value)===NaN || stock_value===undefined)
		{
			$(id).val(0);
			alert("Issue quantity over the Stock quantity not allowed");
			return;
		}
		var issure_value=$(id).val();
		if((parseInt(stock_value)*1)<parseInt(issure_value))
		{
			$(id).val(0);
			alert("Issue quantity over the Stock quantity not allowed");
			return;
		}
		var req_qty = $(id).attr('placeholder');
		if((parseInt(req_qty)*1)<parseInt(issure_value))
		{
			$(id).val(0);
			alert("Issue quantity over the Requisition quantity not allowed");
			return;
		}
	}
	
	function fn_sub_process_enable(sid)
	{
		if( form_validation('cbo_company_name*cbo_issue_basis','Company Name*Basis')==false )
		{
			return;
		}
		if(sid>0)
		{
			$('#cbo_sub_process').attr('disabled',false);
		}
		else
		{
			$('#cbo_sub_process').val(0);
			reset_form('','list_container_recipe_items','','','','');
			$('#cbo_sub_process').attr('disabled',true);
		}
		
	}
	
</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="left">
    <? echo load_freeze_divs ("../../",$permission);  ?><br />
    <form name="chemicaldyesissue_1" id="chemicaldyesissue_1" autocomplete="off" > 
        <div style="width:100%;">
        	<fieldset style="width:1000px;">
            <legend>Dyes And Chemical Issue</legend>
            	<br />
                <fieldset style="width:950px;">                                       
                    <table  width="950" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                        <tr>
                            <td colspan="6" align="center">&nbsp;<b>Issue Number</b>
                            <input type="text" name="txt_mrr_no" id="txt_mrr_no" class="text_boxes" style="width:155px" placeholder="Double Click To Search" onDblClick="open_mrrpopup()" readonly /> 
                            <input type="hidden" name="update_id" id="update_id" />
                            <input type="hidden" name="hidden_posted_account" id="hidden_posted_account" />
                            </td>
                        </tr>
                        <tr>
                            <td  width="130" align="right" class="must_entry_caption">Issue Date </td>
                            <td width="170">
                            <input type="text" name="txt_issue_date" id="txt_issue_date" class="datepicker" style="width:160px;" placeholder="Select Date" value="<? echo date('d-m-Y'); ?>" />
                            </td>
                            <td  width="130" align="right" class="must_entry_caption">Company</td>
                            <td width="170">
                            <? 
                            echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond $company_credential_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "reset_form('chemicaldyesissue_1','list_container_yarn*list_container_recipe_items','','cbo_issue_basis,7','fn_independent(7)','txt_issue_date*cbo_company_name*cbo_issue_purpose');load_drop_down( 'requires/chemical_dyes_issue_controller', this.value, 'load_drop_down_location', 'location_td');load_drop_down( 'requires/chemical_dyes_issue_controller', this.value+'**'+7, 'load_drop_down_store', 'store_td' );load_drop_down( 'requires/chemical_dyes_issue_controller', this.value, 'load_drop_down_loan_party', 'loan_party_td');" );
                            ?>
                            </td>
                            <td width="130" align="right">Location</td>
                            <td width="170" id="location_td">
                            <? 
                            echo create_drop_down( "cbo_location_name", 170, $blank_array,"", 1, "-- Select Location --", 0, "" );
                            ?>
                            </td>
                        </tr>
                        
                         <tr>
                            <td align="right" class="must_entry_caption"> Issue Basis  </td>
                            <td>
                            <? 
                            echo create_drop_down( "cbo_issue_basis", 170, $receive_basis_arr,"", 1, "- Select Receive Basis -", 7, "fn_independent(this.value)","","4,7" );//fn_independent(this.value)
                            ?>
                            </td>
                            <td align="right">Issue Purpose </td>
                            <td>
                            <? 
                            echo create_drop_down( "cbo_issue_purpose", 170, $yarn_issue_purpose,"", 1, "-- Select Purpose --", 53, "", "","3,5,26,30,32,33,40,53,59,60,65,66");
                            ?>
                            </td>
                            <td align="right">Loan/Sales Party</td>
                            <td id="loan_party_td">
                            <? 
                            echo create_drop_down( "cbo_loan_party", 170, $blank_array,"", 1, "- Select Loan Party -", $selected, "","","" );
                            ?>
                            </td>
                         </tr>
                        <tr>
                            <td align="right">Req. No </td>
                            <td>
                            <input class="text_boxes"  type="text" name="txt_req_no" id="txt_req_no" onDblClick="open_reqpopup()" placeholder="Double Click" style="width:160px;"  readonly  /> 
                            <input type="hidden" id="txt_req_id" name="txt_req_id" value="" />
                            </td>
                            <td align="right">Recipe No.</td>
                            <td>
                            <input class="text_boxes"  type="text" name="txt_recipe_no" id="txt_recipe_no"  style="width:160px;" disabled /><!-- onDblClick="openmypage_labdipNo();"-->
                            <input class="text_boxes"  type="hidden" name="txt_recipe_id" id="txt_recipe_id"style="width:160px;"  readonly/> 
                            </td>
                            <td align="right">Job No</td>
                            <td>
                            <input class="text_boxes"  type="text" name="txt_job_no" id="txt_job_no" style="width:160px;" placeholder="Display" readonly   /> 
                            </td>
                        </tr>
                        <tr>
                        	<td align="right">Order No </td>
                            <td>
                            <input class="text_boxes"  type="text" name="txt_order_no" id="txt_order_no" style="width:160px;" placeholder="Display" readonly   /> 
                            <input type="hidden" name="hidden_order_id" id="hidden_order_id" /> 
                            </td>
                            <td align="right" class="must_entry_caption">Store Name</td>
                            <td id="store_td" >
							<? 
                                echo create_drop_down( "cbo_store_name", 170, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and b.category_type in(5,6,7) $store_location_credential_cond group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", "", "fn_sub_process_enable(this.value);", 1,"");
                            ?>
                            </td>
                        	<td align="right">Buyer PO</td>
                            <td>
                            <input class="text_boxes"  type="text" name="txt_buyer_po" id="txt_buyer_po" placeholder="Display" style="width:160px;" readonly />
                            <input type="hidden" name="hidden_buyer_po_id" id="hidden_buyer_po_id" /> 
                            </td>
                        </tr>
                        <tr>
                        	<td align="right">Buyer Style</td>
                            <td>
                            <input class="text_boxes"  type="text" name="txt_buyer_style" id="txt_buyer_style" placeholder="Display" style="width:160px;" readonly /> 
                            </td>
                            <td align="right">Floor</td>
                            <td id="floor_td">
                            <? 
						    echo create_drop_down( "cbo_flore_name", 170, $blank_array,"", 1, "-- Select--", "", "",1);
                            ?>
                            </td>
                            <td align="right">Machine</td>
                            <td id="machine_td">
                            <? 
						    echo create_drop_down( "cbo_machine_name", 170, $blank_array,"", 1, "-- Select--", "", "",1);
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" class="must_entry_caption" id="sub_process_caption">Color/Process</td>
                            <td id="sub_process_td" >
                            <? 
						    echo create_drop_down( "cbo_sub_process", 170, $blank_array,"", 1, "-- Select--", "", "",1);
                            ?>
                            </td>
                            <td align="right">Recipe For</td>
                        	<td><? echo create_drop_down("cbo_recipe_for", 160, $recipe_for,"", 1,"-Select-", 0,"fnc_disable_prod(this.value);",1); ?></td>
                            <td align="right">Remarks</td>
                            <td>
                            <input class="text_boxes"  type="text" name="txt_remarks" id="txt_remarks" placeholder="Write" style="width:160px;" /> 
                            </td>
                        </tr>
                    </table>
                </fieldset>
                <table cellpadding="0" cellspacing="1" width="100%">
                    <tr> 
                    	<td colspan="6" align="center"></td>				
                    </tr>
                    <tr> 
                    	<td colspan="6" align="center">  <div id="list_container_recipe_items" style="margin-top:10px"></div></td>				
                    </tr>
                    <tr>
                        <td align="center" colspan="6" valign="middle" class="button_container">
                        <!-- details table id for update -->
                        <input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
                        <input type="hidden" name="update_for_cost" id="update_for_cost" readonly>
                        <? echo load_submit_buttons( $permission, "fnc_chemical_dyes_issue_entry", 0,1,"fnResetForm()",1);?>
                        </td>
                    </tr>
                    <tr> 
                    	<td colspan="6" align="center"> <div id="recipe_items_list_view" style="margin-top:10px"> </div></td>				
                    </tr> 
                </table>  
                <div style="width:990px;" id="list_container_yarn"></div>               
            </fieldset>  
        </div>
    </form>
    </div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
