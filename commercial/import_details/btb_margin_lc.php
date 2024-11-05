<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create for BTB entry

Functionality	:


JS Functions	:

Created by		:	Rashed
Creation date 	: 	18-11-2012
Updated by 		: 	Fuad Shahriar
Update date		: 	04-04-2013

QC Performed BY	:

QC Date			:

Comments		:

*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

$independent_control_arr = return_library_array( "select company_name, independent_controll from variable_settings_inventory where variable_list=20 and menu_page_id=105 and status_active=1 and is_deleted=0",'company_name','independent_controll');

$item_category_without_general=array_diff($item_category,$general_item_category);
$genarel_item_arr=array(4=>"Accessories",8=>"General Item");
$item_category_with_gen=$item_category_without_general+$genarel_item_arr;
ksort($item_category_with_gen);


//-------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("BTB / Margin LC","../../", 1, 1, $unicode,'','');
// echo $data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][105]);die;

?>

<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';
	var field_level_data="";
	<?
		if(isset($_SESSION['logic_erp']['data_arr'][105]))
		{
		  $data_arr=json_encode($_SESSION['logic_erp']['data_arr'][105] );
		  echo "var field_level_data= ". $data_arr . ";\n";
		}
		else
		{
			 echo "var field_level_data= [];\n";
		}

		if($_SESSION['logic_erp']['mandatory_field'][105]!="")
		{
			$mandatory_field_arr= json_encode( $_SESSION['logic_erp']['mandatory_field'][105] );
			echo "var mandatory_field_arr= ". $mandatory_field_arr . ";\n";
		}
		else
		{
			echo "var mandatory_field_arr= [];\n";
		}
	?>
	var str_port_of_loading=[];var str_port_of_discharge=[];var str_inco_term_place=[];var str_insurance_company=[];var str_advising_bank=[];
	//var str_port_of_loading 	= [<?// echo substr(return_library_autocomplete( "select distinct(port_of_loading) from com_btb_lc_master_details", "port_of_loading"  ), 0, -1); ?>];
	//var str_port_of_discharge 	= [<?// echo substr(return_library_autocomplete("select distinct(port_of_discharge) from com_btb_lc_master_details", "port_of_discharge"), 0, -1); ?>];
	//var str_inco_term_place 	= [<?// echo substr(return_library_autocomplete( "select distinct(inco_term_place) from com_btb_lc_master_details", "inco_term_place"  ), 0, -1); ?>];

	//var str_insurance_company 	= [<?// echo substr(return_library_autocomplete( "select distinct(insurance_company_name) from com_btb_lc_master_details", "insurance_company_name"  ), 0, -1); ?>];
	//var str_advising_bank 	= [<?// echo substr(return_library_autocomplete( "select distinct(advising_bank) from com_btb_lc_master_details", "advising_bank"  ), 0, -1); ?>];

	$(document).ready(function(e)
	{
		$("#txt_port_loading").autocomplete({
			source: str_port_of_loading
		});
		$("#txt_port_discharge").autocomplete({
			source: str_port_of_discharge
		});
		$("#txt_inco_term_place").autocomplete({
			source: str_inco_term_place
		});
		$("#txt_insurance_company").autocomplete({
			source: str_insurance_company
		});
		$("#cbo_adv_bank").autocomplete({
			source: str_advising_bank
		});

	});

	function openmypage(type,row_num)
	{
		//hide_left_menu("Button1");
		if(type==2)
		{
			var item_category = $('#cbo_item_category_id').val();
			var txt_hidden_pi_id = $('#txt_hidden_pi_id').val();
			var btb_id = $('#update_id').val();
			var cbo_importer_id = document.getElementById('cbo_importer_id').value;

			//if (form_validation('cbo_importer_id*cbo_item_category_id','Importer*Item Category')==false)
			if (form_validation('cbo_importer_id','Importer')==false)
			{
				return;
			}
			else
			{
				var title = 'PI Selection Form';
				var page_link = 'requires/btb_margin_lc_controller.php?item_category_id='+item_category+'&txt_hidden_pi_id='+txt_hidden_pi_id+'&btb_id='+btb_id+'&cbo_importer_id='+cbo_importer_id+'&action=pi_popup';

				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1300px,height=470px,center=1,resize=1,scrolling=0','../')

				emailwindow.onclose=function()
				{
					var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
					var pi_id=this.contentDoc.getElementById("txt_selected_id").value;
					var pi_no=this.contentDoc.getElementById("txt_selected").value;
					var txt_item_category=this.contentDoc.getElementById("txt_item_category").value;
					var txt_pi_entry_form=this.contentDoc.getElementById("txt_pi_entry_form").value;
					var payTerm=this.contentDoc.getElementById("payTerm").value;
					var tenor=this.contentDoc.getElementById("tenor").value;

                    $('#cbo_importer_id').attr("disabled",true);
                    $('#cbo_item_category_id').attr("disabled",true);
					if (pi_id!="")
					{
						$('#txt_hidden_pi_id').val(pi_id);
						$('#txt_pi').val(pi_no);
						//$('#txt_hidden_pi_item').val(txt_item_category);
						$('#txt_hidden_pi_item').val(txt_pi_entry_form);
						if($('#cbo_payterm_id').val()=="")
						{
							$('#cbo_payterm_id').val(payTerm);
						}
						
						$('#txt_tenor').val(tenor);
						$('#cbo_supplier_id').attr("disabled",true);

						freeze_window(5);
						get_php_form_data(pi_id, "set_value_pi_select", "requires/btb_margin_lc_controller" );

						//show_list_view(pi_id+'_'+item_category,'show_pi_details_list','pi_details_list','requires/btb_margin_lc_controller','');
						show_list_view(pi_id+'_'+txt_pi_entry_form+'_'+txt_item_category,'show_pi_details_list','pi_details_list','requires/btb_margin_lc_controller','setFilterGrid(\'pi_details_list\',-1)');
						release_freezing();
					}
					else
					{
						$('#txt_pi').val('');
						$('#txt_hidden_pi_id').val('');
						$('#txt_hidden_pi_item').val('');
						$('#cbo_payterm_id').val(0);
						$('#txt_tenor').val('');
						reset_form('','','txt_pi_value*cbo_supplier_id*txt_last_shipment_date*cbo_pi_currency_id*cbo_lc_currency_id');
						reset_form('','pi_details_list');
					}
				}
			}
		}
		else if(type==1)
		{
			//alert(1);
			var cbo_importer_id = $('#cbo_importer_id').val();

			if (form_validation('cbo_importer_id','Importer')==false)
			{
				return;
			}

			var page_link = 'requires/btb_margin_lc_controller.php?cbo_importer_id='+cbo_importer_id+'&action=btb_lc_search';
			var title='BTB L/C Search Form';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=450px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var btb_ref=this.contentDoc.getElementById("hidden_btb_id").value.split("__");
				var btb_id=btb_ref[0];
				var item_category_id=btb_ref[1];
				var ref_closing_status=btb_ref[2];
				if(trim(btb_id)!="")
				{
					freeze_window(5);
					get_php_form_data( btb_id, "populate_data_from_btb_lc", "requires/btb_margin_lc_controller" );

					show_list_view(trim(btb_id),'show_lc_listview','sc_lc_list_view','requires/btb_margin_lc_controller','');

					var item_category = $('#cbo_item_category_id').val();
					var txt_hidden_pi_id = $('#txt_hidden_pi_id').val();
					var pi_entry_form = $("#txt_hidden_pi_item").val();
					$("#hidden_ref_closing_status").val(ref_closing_status);
					//alert(pi_entry_form);

					if(pi_entry_form!="")
					{
						show_list_view(txt_hidden_pi_id+'_'+pi_entry_form+'_'+item_category_id,'show_pi_details_list','pi_details_list','requires/btb_margin_lc_controller','setFilterGrid(\'pi_details_list\',-1)');
					}

					var numRow = $('table#tbl_lc_list tbody tr').length;
					var button_status='';

					if(numRow==1)
					{
						var lc_sc_no=$('#txtlcsc_1').val();
						if(lc_sc_no=="")
						{
							$('#txt_tot_row').val(0);
							button_status=0;
						}
						else
						{
							$('#txt_tot_row').val(numRow);
							button_status=1;
						}
					}
					else
					{
						$('#txt_tot_row').val(numRow);
						button_status=1;
					}

					var ddd={ dec_type:4, comma:0, currency:$('#cbo_pi_currency_id').val()}

					math_operation( "totalLcScValue", "txtlcscvalue_", "+", numRow,ddd );
					math_operation( "totalCurrentDistri", "txtcurdistribution_", "+", numRow,ddd );
					math_operation( "totalCumuDisri", "txtcumudistribution_", "+", numRow,ddd );
					calculate_occupied(numRow);
					set_button_status(button_status, permission, 'fnc_lc_details',2);
					release_freezing();

				}

			}
		}
		else if(type==3)
		{
			//var update_id = document.getElementById('update_id').value;
			var cbo_importer_id = document.getElementById('cbo_importer_id').value;
			var txt_hidden_pi_id = document.getElementById('txt_hidden_pi_id').value;
			var txt_hidden_pi_item = document.getElementById('txt_hidden_pi_item').value;
			var cboBuyerID = document.getElementById('cboBuyerID').value;
			
			var lc_sc="";
			var numRow = $('table#tbl_lc_list tbody tr').length;
			for(var c=1; c<=numRow; c++)
			{
				var lc_sc_id=$('#txtLcScid_'+c).val();
				if(lc_sc_id!="")
				{
					var type=$('#txtlcscflagId_'+c).val();
					if(lc_sc=="") lc_sc=lc_sc_id+"__"+type; else lc_sc=lc_sc+","+lc_sc_id+"__"+type;
				}
			}

			page_link = 'requires/btb_margin_lc_controller.php?action=lc_popup&company_id='+cbo_importer_id+'&txt_hidden_pi_id='+txt_hidden_pi_id+'&txt_hidden_pi_item='+txt_hidden_pi_item+'&lc_sc='+lc_sc+'&cboBuyerID='+cboBuyerID,'LC/SC Selection Form';

			if(form_validation('txt_system_id','System ID')==false )
			{
				return;
			}
			else
			{
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'LC/SC Information', 'width=900px,height=360px,center=1,resize=1,scrolling=0','../');
				emailwindow.onclose=function()
				{
					var theform=this.contentDoc.forms[0];
					var lc_sc_id=this.contentDoc.getElementById("txt_selected_id").value;
					var tot_row=$('#txt_tot_row').val();
					var cbo_pi_currency_id=$('#cbo_pi_currency_id').val();
					var cbo_importer_id=$('#cbo_importer_id').val();
					var txt_lc_date=$('#txt_lc_date').val();
					var data=tot_row+"**"+lc_sc_id+"**"+cbo_pi_currency_id+"**"+txt_lc_date+"**"+cbo_importer_id;
					
					var list_view_orders = return_global_ajax_value( data, 'lc_list_for_attach', '', 'requires/btb_margin_lc_controller');
					var lc_sc_no=$('#txtlcsc_'+row_num).val();

					if(lc_sc_no=="")
					{
						$("#tr_"+row_num).remove();
					}
					if(tot_row==0)
					{
						$('#sc_lc_list_view').html(list_view_orders);
					}
					else
					{
						$("#tbl_lc_list tbody:last").append(list_view_orders);
					}

					var numRow = $('table#tbl_lc_list tbody tr').length;
					//alert($('#txtcurdistribution_1').val())
					$('#txt_tot_row').val(numRow);
					var ddd={ dec_type:4, comma:0, currency:$('#cbo_pi_currency_id').val()}
					math_operation( "totalLcScValue", "txtlcscvalue_", "+", numRow,ddd );
					calculate_all(numRow);
				}
			}
		}

	}

 	function calculate_all(tot_row)
	{
		current_distribution(tot_row);
		cummulative_distribution(tot_row);
		calculate_occupied(tot_row);
	}

	function current_distribution(tot_row)
	{
		var btb_val=$('#txt_lc_value').val();
		var tot_lc_sc_val=$('#totalLcScValue').val();
		var cbo_pi_currency_id=$('#cbo_pi_currency_id').val();
		var totalDistribution=0;

		for(i=1;i<=tot_row;i++)
		{
			var lc_sc_val=$('#txtlcscvalue_'+i).val();
			var current_distri = (btb_val/tot_lc_sc_val)*lc_sc_val;
			current_distri=number_format_common(current_distri,'','',cbo_pi_currency_id);

			totalDistribution = totalDistribution*1+current_distri*1;
			totalGrey = number_format_common(totalDistribution,'','',cbo_pi_currency_id);

			if(i==tot_row)
			{
				var balance = btb_val*1-totalDistribution*1;
				balance=number_format_common(balance,'','',cbo_pi_currency_id);
				if(balance!=0) current_distri=current_distri*1+(balance*1);
			}

			$('#txtcurdistribution_'+i).val(current_distri);
		}
		var ddd={ dec_type:4, comma:0, currency:cbo_pi_currency_id}
		math_operation( "totalCurrentDistri", "txtcurdistribution_", "+", tot_row, ddd );
	}


	function cummulative_distribution(tot_row)
	{
		var cbo_pi_currency_id=$('#cbo_pi_currency_id').val();

		for(i=1;i<=tot_row;i++)
		{
			var hidden_exchange_rate=$('#hiddenExchangeRate_'+i).val();
			var current_distri = $('#txtcurdistribution_'+i).val()/hidden_exchange_rate;
			var cumudistri=$('#hiddencumudistribution_'+i).val();
			//alert(current_distri +"="+ cumudistri);
			var curr_cumudistri=cumudistri*1+current_distri*1;
			curr_cumudistri=number_format_common(curr_cumudistri,'','',cbo_pi_currency_id);
			$('#txtcumudistribution_'+i).val(curr_cumudistri);
		}
		var ddd={ dec_type:4, comma:0, currency:cbo_pi_currency_id}
		math_operation( "totalCumuDisri", "txtcumudistribution_", "+", tot_row, ddd );
	}

	function calculate_occupied(tot_row)
	{
		for(i=1;i<=tot_row;i++)
		{
			var lc_sc_val=$('#txtlcscvalue_'+i).val()*1;
			if(lc_sc_val>0)
			{
				var cumudistri=$('#txtcumudistribution_'+i).val()*1;
				var occupied=(cumudistri/lc_sc_val)*100;
			}
			else
			{
				var occupied=0;
			}

			$('#txtoccupied_'+i).val(occupied.toFixed(4));
		}

	}

	function distribution_value(mehtod,row_id)
	{
		var row_num = $('table#tbl_lc_list tbody tr').length;
		if(row_id==0)
		{
			if(mehtod==1)
			{
				$('#tbl_lc_list input[name="txtcurdistribution[]"]').removeAttr('disabled', 'disabled');
				for (var i=1; i<=row_num; i++)
				{
					var type=$('#txtcaltype_'+i).val();
					if(type==1)
					{
						var cumu_val = $('#hiddencumudistribution_'+i).val()*1;
						$('#txtcumudistribution_'+i).val(cumu_val.toFixed(2));
						$('#txtcurdistribution_'+i).val('');
					}
					else if(type==2)
					{
						var current_distri = $('#txtcurdistribution_'+i).val();
						var cumudistri=$('#txtcumudistribution_'+i).val();
						var curr_cumudistri=cumudistri*1-current_distri*1;
						$('#txtcumudistribution_'+i).val(curr_cumudistri.toFixed(2));
						$('#txtcurdistribution_'+i).val('');
					}
				}
				$('#totalCurrentDistri').val('');
				var ddd={ dec_type:4, comma:0, currency:$('#cbo_pi_currency_id').val()}
				math_operation( "totalCumuDisri", "txtcumudistribution_", "+", row_num,ddd );
				calculate_occupied(row_num);

			}
			else
			{
				$('#tbl_lc_list input[name="txtcurdistribution[]"]').attr('disabled', 'disabled');
				current_distribution(row_num);
				for (var i=1; i<=row_num; i++)
				{
					var type=$('#txtcaltype_'+i).val();
					if(type==1)
					{
						var txtcurdistribution = $('#txtcurdistribution_'+i).val();
						var cumudistri = $('#hiddencumudistribution_'+i).val();
						var curr_cumudistri=cumudistri*1+txtcurdistribution*1;
						$('#txtcumudistribution_'+i).val(curr_cumudistri.toFixed(2));
					}
					else if(type==2)
					{
						var txtcurdistribution = $('#txtcurdistribution_'+i).val();
						var cumudistri = $('#hiddencumudistribution_'+i).val();
						var hidecurdistribution = $('#hidecurdistribution_'+i).val();
						var curr_cumudistri=(cumudistri*1+txtcurdistribution*1)-hidecurdistribution*1;
						$('#txtcumudistribution_'+i).val(curr_cumudistri.toFixed(2));
					}
				}
				var ddd={ dec_type:4, comma:0, currency:$('#cbo_pi_currency_id').val()}
				math_operation( "totalCumuDisri", "txtcumudistribution_", "+", row_num,ddd );
				calculate_occupied(row_num);
			}
		}
		else
		{
			var type=$('#txtcaltype_'+row_id).val();
			var txtcurdistribution=$('#txtcurdistribution_'+row_id).val();
			var txtcumudistribution=$('#hiddencumudistribution_'+row_id).val();
			var curr_cumudistri=txtcumudistribution*1+txtcurdistribution*1;

			$('#txtcumudistribution_'+row_id).val(curr_cumudistri.toFixed(2));

			var lc_sc_val=$('#txtlcscvalue_'+row_id).val();
			var occupied=(curr_cumudistri/lc_sc_val)*100;
			$('#txtoccupied_'+row_id).val(occupied.toFixed(2));

			var ddd={ dec_type:4, comma:0, currency:$('#cbo_pi_currency_id').val()}
			math_operation( "totalCumuDisri", "txtcumudistribution_", "+", row_num,ddd );
			math_operation( "totalCurrentDistri", "txtcurdistribution_", "+", row_num,ddd );
		}
	}

	function fn_add_row_lc(i)
	{
		var row_num = $('table#tbl_lc_list tbody tr').length;
		if(i==0)
		{
			return false;
		}
		else if(row_num!=i)
		{
			return false;
		}
		else if(row_num==i && $('#txtlcsc_'+i).val()=="")
		{
			return false;
		}
		else
		{
			i++;

			 $("#tbl_lc_list tbody tr:last").clone().find("input,select").each(function() {
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0]+"_"+i; },
				  'name': function(_, name) { var name=name.split("_"); return name[0]+"_"+i;  },
				  'value': function(_, value) { return ""; }
				});

			  }).end().appendTo("#tbl_lc_list");

			  $("#tbl_lc_list tbody tr:last").removeAttr('id').attr('id','tr_'+i);

			  $('#txtlcsc_'+i).removeAttr("onDblClick").attr("onDblClick","onDblClick= openmypage(3,"+i+")");
			  $('#increase_'+i).removeAttr("value").attr("value","+");
			  $('#decrease_'+i).removeAttr("value").attr("value","-");
			  $('#increase_'+i).removeAttr("onclick").attr("onclick","javascript:fn_add_row_lc("+i+");");
			  $('#decrease_'+i).removeAttr("onclick").attr("onclick","javascript:fn_deleteRow("+i+");");
		}
	}


	function fn_deleteRow(rowNo)
	{
		var numRow = $('table#tbl_lc_list tbody tr').length;

		if(numRow==rowNo && rowNo!=1)
		{
		    $('#tbl_lc_list tbody tr:last').remove();
		  	var numRow = $('table#tbl_lc_list tbody tr').length;
			$('#txt_tot_row').val(numRow);
			var ddd={ dec_type:4, comma:0, currency:$('#cbo_pi_currency_id').val()}
			math_operation( "totalLcScValue", "txtlcscvalue_", "+", numRow,ddd );
			calculate_all(numRow);
		}
		else
		{
			return false;
		}

	}

	function fnc_btb_mst( operation )
	{
		/*if(operation==2)
		{
			show_msg('13');
			return;
		}*/

		var lc_basis = $('#cbo_lc_basis_id').val();
		var txt_last_shipment_date = $('#txt_last_shipment_date').val();
		var cbo_payterm_id = $('#cbo_payterm_id').val();
		var cbo_lc_type_id = $('#cbo_lc_type_id').val();
		var cbo_item_category_id =$('#cbo_item_category_id').val()*1;
		var ref_closing_status = $('#hidden_ref_closing_status').val();
		
		if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][105]);?>') {
			if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][105]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][105]);?>')==false) {
				return;
			}
		}
		if(cbo_payterm_id==3 )
		{
			if (form_validation('cbo_importer_id*application_date*cbo_issuing_bank*cbo_lc_basis_id*cbo_supplier_id*cbo_lc_type_id*txt_lc_value*cbo_inco_term_id*cbo_payterm_id*cbo_delevery_mode*txt_last_shipment_date','Importer*Application Date*Issuing Bank*Item Category*L/C Basis*Supplier*L/C Type*L/C Value*Incoterm*Pay Term*Delevery Mode*Last Shipment Date')==false)
			{
				return;
			}
		}
		else
		{
			if (ref_closing_status!=1) {
				if (form_validation('cbo_importer_id*application_date*cbo_issuing_bank*cbo_lc_basis_id*cbo_supplier_id*cbo_lc_type_id*txt_lc_value*cbo_inco_term_id*cbo_payterm_id*cbo_delevery_mode*cbo_maturit_from_id*txt_last_shipment_date','Importer*Application Date*Issuing Bank*Item Category*L/C Basis*Supplier*L/C Type*L/C Value*Incoterm*Pay Term*Delevery Mode*Maturity From*Last Shipment Date')==false)
				{
					return;
				}
			}else{
				alert("This reference is closed. No operation is allowed.");
				$("#update1").attr("disabled",true);
				return;
			}
		}


		if(lc_basis == 1)
		{
			if(form_validation('txt_pi_value','PI Value')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_item_category_id','Item Category')==false)
			{
				return;
			}
		}


		if($('#txt_bank_code').val()!='' || $('#txt_lc_year').val()!='' || $('#txt_category').val()!='' || $('#txt_lc_serial').val()!='')
		{
			if(form_validation('txt_lc_date*txt_lc_expiry_date','L/C Date*L/C Expiry Date')==false)
			{
				return;
			}
		}
		if(txt_last_shipment_date!='')
		{

			if(form_validation('txt_lc_expiry_date','L/C Expiry Date')==false)
			{
				return;
			}

			if( date_compare($('#txt_last_shipment_date').val(), $('#txt_lc_expiry_date').val() )==false )
			{
				alert("Expiry date must be equal or higher than last shipment date");
				$('#txt_lc_expiry_date').focus();
				return;
			}
		}
		if(cbo_payterm_id==2)// || cbo_payterm_id==4 || cbo_payterm_id==6 || cbo_payterm_id==7 || cbo_payterm_id==9
		{
			if(form_validation('txt_tenor','Tenor')==false)
			{
				return;
			}
		}
		if(cbo_lc_type_id==2)
		{
			/*if(form_validation('txt_margin_deposit','Margin Deposit %')==false)
			{
				return;
			}*/
			if(trim($('#txt_margin_deposit').val())=="")
			{
				alert("Please Input Margin Deposit");
				$('#txt_margin_deposit').focus();
				return;
			}
		}

		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_system_id*cbo_importer_id*application_date*cbo_issuing_bank*cbo_lc_basis_id*txt_pi*txt_hidden_pi_id*txt_pi_value*cbo_pi_currency_id*cbo_supplier_id*cbo_lc_type_id*txt_bank_code*txt_lc_year*txt_category*txt_lc_serial*txt_lc_date*txt_last_shipment_date*txt_lc_expiry_date*txt_lc_value*cbo_lc_currency_id*cbo_inco_term_id*txt_inco_term_place*cbo_payterm_id*txt_tenor*txt_tolerance*cbo_delevery_mode*txt_doc_perc_days*txt_port_loading*txt_port_discharge*txt_etd_date*txt_lca_no*txt_lcaf_no*txt_imp_form_no*txt_insurance_company*txt_cover_note_no*txt_cover_note_date*txt_psi_company*cbo_maturit_from_id*txt_margin_deposit*cbo_origin_id*txt_shiping_mark*txt_gmt_qnty*cbo_gmt_uom_id*txt_ud_no*txt_ud_date*cbo_credit_advice_id*cbo_partial_ship_id*cbo_transhipment_id*cbo_add_confirm_id*txt_conf_bank*cbo_bond_warehouse_id*txt_remarks*cbo_status*update_id*txt_hidden_pi_item*txt_upas_rate*cbo_item_category_id*cbo_adv_bank*txt_adv_bank_address*txt_lc_reference_no*txt_expiry_days',"../../");
		
		freeze_window(operation);
		http.open("POST","requires/btb_margin_lc_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_btb_mst_reponse;
	}

	function fnc_btb_mst_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');

			if(reponse[0]==35)
			{
				alert(reponse[1]);release_freezing();return;
			}

			if((reponse[0]==0 || reponse[0]==1))
			{
				show_msg(trim(reponse[0]));
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_system_id').value = reponse[2];
				$('#cbo_importer_id').attr('disabled','disabled');
                $('#cbo_item_category_id').attr('disabled','disabled');
				$('#cbo_lc_type_id').attr('disabled','disabled');
				set_button_status(1, permission, 'fnc_btb_mst',1);
				release_freezing();
			}
			else if(reponse[0]==2)
			{
				location.reload();
				release_freezing();return;
			}
			else if(reponse[0]==40)
			{
				alert(reponse[1]);
				release_freezing();return;
			}
			else if(reponse[0]==11)
			{
				show_msg(trim(reponse[0]));
				alert(reponse[1]);
				release_freezing();
			}
			release_freezing();
			uploadFile( $("#update_id").val());

		}
	}

	function fnc_lc_details( operation )
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}

		var update_id = $('#update_id').val();
	 	var row_num=$('#tbl_lc_list tbody tr').length;
		var totalCurrentDistri = $('#totalCurrentDistri').val();
		var txt_lc_value = $('#txt_lc_value').val();
		var max_btb_limit = $('#max_btb_limit').val()*1;
		var btb_limit_controll = $('#btb_limit_controll').val()*1;
		

		if(form_validation('txt_system_id','System ID')==false)
		{
			return;
		}
		else if(totalCurrentDistri*1>txt_lc_value*1)
		{
			alert("Total Current Distribution Value Can Not Exceed LC Value");
			$('#totalCurrentDistri').focus();
			return;
		}
		else
		{
			var data_all=""; var j=0;
			for (var i=1; i<=row_num; i++)
			{
				if($('#txtLcScid_'+i).val()!="")
				{
					j++;
				}
				
				//if(($('#txtoccupied_'+i).val()*1>max_btb_limit) && max_btb_limit>0)
				//{
					//alert("Occupied Percent Not Allow Max BTB Limit");return;
				//}
				
				if(btb_limit_controll==1)
				{
					if($('#txtoccupied_'+i).val()*1>$('#maxBtbLimit_'+i).val())
					{
						alert("Occupied Percent Not Allow Max BTB Limit");return;
					}
				}
				
				data_all=data_all+get_submitted_data_string('txtLcScid_'+i+'*txtlcscflagId_'+i+'*txtcurdistribution_'+i+'*cbostatus_'+i,"../../",i);
			}
			
			if(j==0)
			{
				alert("Please Select SC/LC No");return;
			}

			var data="action=save_update_delete_dtls&operation="+operation+'&total_row='+row_num+'&update_id='+update_id + data_all;

			freeze_window(operation);
			http.open("POST","requires/btb_margin_lc_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_lc_details_reponse;
		}
	}


	function fnc_lc_details_reponse()
	{
		if(http.readyState == 4)
		{
			var response=http.responseText.split('**');
			if(response[0]==40)
			{
				alert(response[1]);release_freezing();return;
			}
			else if(response[0]==0 || response[0]==1)
			{
				show_list_view($('#update_id').val(),'show_lc_listview','sc_lc_list_view','requires/btb_margin_lc_controller','');
				var numRow = $('table#tbl_lc_list tbody tr').length;

				var ddd={ dec_type:4, comma:0, currency:$('#cbo_pi_currency_id').val()}

				math_operation( "totalLcScValue", "txtlcscvalue_", "+", numRow,ddd );
				math_operation( "totalCurrentDistri", "txtcurdistribution_", "+", numRow,ddd );
				math_operation( "totalCumuDisri", "txtcumudistribution_", "+", numRow,ddd );
				calculate_occupied(numRow);
				show_msg(trim(response[0]));
				set_button_status(1, permission, 'fnc_lc_details',2);
				release_freezing();
			}
			else
			{
				show_msg(trim(response[0]));
				release_freezing();
			}
			

		}
	}
	
	function openmypage_file_info()
	{
		var company_id=document.getElementById('cbo_importer_id').value;
		var is_lc_sc=document.getElementById('is_lc_sc').value;
		var lc_sc_id=document.getElementById('lc_sc_id').value;
		//var cbo_year=document.getElementById('hide_year').value;
		// alert(company_id);
		//page_link='requires/file_wise_export_status_controller.php?action=file_popup&company_id='+company_id+'&buyer_id='+buyer_id+'&lien_bank='+lien_bank+'&cbo_year='+cbo_year;
		page_link='requires/btb_margin_lc_controller.php?action=file_popup&company_id='+company_id+'&is_lc_sc='+is_lc_sc+'&lc_sc_id='+lc_sc_id;
		if(form_validation('cbo_importer_id','Company Name')==false)
		{
			return;
		}
		else
		{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Select File", 'width=600px,height=390px,center=1,resize=0,scrolling=0','../')

			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var data=this.contentDoc.getElementById("hide_file_no").value.split('_');//alert(item_description_all);
				// alert(data[4]);
				document.getElementById('txt_internal_file_no').value=data[0];
				document.getElementById('is_lc_sc').value=data[1];
				document.getElementById('lc_sc_id').value=data[2];
				document.getElementById('lc_sc_no').value=data[3];
				document.getElementById('lc_sc_file_year').value=data[4];
			}
		}
	}
	
	function print_button_setting()
	{
		$('#button_data_panel').html('');
		set_field_level_access($('#cbo_importer_id').val());
		get_php_form_data($('#cbo_importer_id').val(),'print_button_variable_setting','requires/btb_margin_lc_controller' );
	}

	function set_port_loading_value(id)
	{
		if(id == 3 ||id == 4 ||id == 99)
		{
			$('#txt_port_loading').val('From Suppliers Factory');
			$('#txt_port_discharge').val('To Importers Factory');
		}
		else
		{
			$('#txt_port_loading').val('');
			$('#txt_port_discharge').val('');
		}
	}

	function check_value_same(lc_val)
	{
		var pi_val = $('#txt_pi_value').val();

		if(lc_val!=pi_val)
		{
			alert('PI value and L/C value should be same');
			$('#txt_lc_value').val('');
			$('#txt_lc_value').focus();
		}
	}

	function active_inactive(str)
	{
		document.getElementById('txt_lc_value').value="";
		document.getElementById('txt_pi').value="";
		document.getElementById('txt_hidden_pi_id').value="";
		document.getElementById('txt_pi_value').value="";

		if(str==1)
		{
			document.getElementById('txt_lc_value').disabled=true;
			document.getElementById('txt_pi').disabled=false;
		}
		else
		{
			document.getElementById('txt_lc_value').disabled=false;
			document.getElementById('txt_pi').disabled=true;
		}
	}

	function fnc_move_cursor(val, field_id, lnth)
	{
		var str_length=val.length;
		if(str_length==lnth)
		{
			$('#'+field_id).select();
			$('#'+field_id).focus();
		}
	}

	function fnc_len_check(val)
	{
		if(val.length<2)
		{
			alert("Single Digit Not Allow");
			$('#txt_category').val("");return;
		}
	}

	function fn_print_letter(str)
	{
		if (form_validation('update_id','Save Data First')==false)
		{
			alert("Save Data First");
			return;
		}
		else
		{
			var letter_type=$('#cbo_letter_type').val();
			if(str==1)
			{
				print_report( $('#update_id').val(), "btb_import_lc_letter", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==2)
			{
				print_report( $('#update_id').val(), "btb_import_lc_letter2", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==3)
			{
				print_report( $('#update_id').val(), "btb_import_lc_letter3", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==5)
			{
				print_report( $('#update_id').val(), "lc_opening_later", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==6)
			{
				print_report( $('#update_id').val(), "btb_import_lc_letter5", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==7)
			{
				print_report( $('#update_id').val(), "btb_import_lc_tt", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==8)
			{
				print_report( $('#update_id').val(), "btb_import_lc_tt_fdd", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==9)
			{
				print_report( $('#update_id').val(), "btb_import_lc_open", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==10)
			{
				print_report( $('#update_id').val(), "btb_import_lc_ftt", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==11)
			{
				print_report( $('#update_id').val(), "btb_req", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==12)
			{
				print_report( $('#update_id').val(), "btb_import_lc_letter6", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==13)
			{
				print_report( $('#update_id').val(), "btn_print_letter_chem", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==14)
			{
				print_report( $('#update_id').val(), "btb_print_letter_forwarding", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==15)
			{
				print_report( $('#update_id').val(), "btb_import_lc_letter7", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==16)
			{
				print_report( $('#update_id').val(), "btb_import_lc_letter8", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==17)
			{
				print_report( $('#update_id').val(), "btb_import_lc_letter9", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==18)
			{
				var cbo_lc_type= $('#cbo_lc_type_id').val();
				if(cbo_lc_type!=5 && cbo_lc_type!=6 && cbo_lc_type!=4)
				{
					alert('Print for L/C Type TT, FTT and FDD');
					return;
				}
				print_report( $('#update_id').val(), "btb_import_lc_tt_fdd2", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==19)
			{
				var show_item="";
				var r=confirm("Press  \"Ok\" to open with Foreign Import Format \nPress \"Cancel\" to open with Local Import Format");
				if(r==true){ type="1"; }else{ type="0"; }
				print_report( $('#update_id').val()+'*'+type, "btb_print_letter_forwarding2", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==20)
			{
				print_report( $('#update_id').val()+'**'+$('#cbo_importer_id').val(), "btb_import_lc_letter10", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==21)
			{
				print_report( $('#update_id').val(), "btb_import_lc_letter11", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==22)
			{
				print_report( $('#update_id').val(), "lc_opening_letter3", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==23)
			{
				print_report( $('#update_id').val(), "btb_import_lc_pwa", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==24)
			{
				print_report( $('#update_id').val(), "btb_import_lc_letter12", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==25)
			{
				print_report( $('#update_id').val(), "btb_print_letter_forwarding3", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==26)
			{
				print_report( $('#update_id').val(), "btb_print_letter_forwarding4", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==27)
			{
				print_report( $('#update_id').val(), "btb_print_letter_forwarding5", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==28)
			{
				print_report( $('#update_id').val(), "btb_import_lc_letter13", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==29)
			{
				print_report( $('#update_id').val(), "btb_import_lc_letter14", "requires/btb_margin_lc_controller" ) ;
			}
			
			else if(str==30)
			{
				print_report( $('#update_id').val(), "btb_import_lc_letter15", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==31)
			{
				print_report( $('#update_id').val(), "btb_import_lc_letter31", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==32)
			{
				print_report( $('#update_id').val(), "btb_import_lc_letter32", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==33)
			{
				print_report( $('#update_id').val(), "btb_import_lc_letter33", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==34)
			{
				print_report( $('#update_id').val(), "btb_import_lc_letter34", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==35)
			{
				print_report( $('#update_id').val(), "btb_import_lc_letter35", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==36)
			{
				print_report( $('#update_id').val(), "btb_import_lc_letter36", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==37)
			{
				print_report( $('#update_id').val(), "btb_import_lc_letter37", "requires/btb_margin_lc_controller" ) ;
			}
			else if(str==38)
			{
				print_report( $('#update_id').val(), "btb_import_lc_letter38", "requires/btb_margin_lc_controller" ) ;
			}
			else 
			{
				print_report( $('#update_id').val(), "btb_import_lc_letter4", "requires/btb_margin_lc_controller" ) ;
			}

		}
	}



	function fn_application_form()
	{
		if (form_validation('update_id','Save Data First')==false)
		{
			alert("Save Data First");
			return;
		}
		else
		{
			 print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/exim_bank_form" ) ;
		}
	}
	function fn_application_form_new(bank_name)
	{
		if (form_validation('update_id','Save Data First')==false)
		{
			alert("Save Data First");
			return;
		}
		else
		{
			if(bank_name==11){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/brac_bank_form" ) ;
			}else if(bank_name==12){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/ific_bank_form" ) ;
			}else if(bank_name==13){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/one_bank_form" ) ;
			}else if(bank_name==14){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/dbbl_bank_form" ) ;
			}else if(bank_name==15){
				var r=confirm("Press  \"Cancel\" to hide in words \nPress  \"OK\" to show in words");
				if (r==true){ show_item="1"; }else{ show_item="0"; }
				print_report( $('#update_id').val()+"**"+show_item, "btb_application_form", "../application_form/requires/brack_bank_lca_form" ) ;
			}else if(bank_name==16){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/ific_bank_lca_form" ) ;
			}else if(bank_name==17){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/eastern_bank_form" ) ;
			}else if(bank_name==18){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/jamuna_bank_form" ) ;
			}else if(bank_name==19){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/ucb_bank_form" ) ;
			}else if(bank_name==20){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/prime_bank_form" ) ;
			}else if(bank_name==21){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/city_bank_form" ) ;
			}else if(bank_name==22){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/bank_asia_form" ) ;
			}else if(bank_name==23){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/shahjalal_islami_bank_lca_form" ) ;
			}else if(bank_name==24){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/mtbl_bank_form" ) ;
			}else if(bank_name==25){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/dhaka_bank_lca_form" ) ;
			}else if(bank_name==26){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/pubali_bank_form" ) ;
			}else if(bank_name==27){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/hsbc_bank_form" ) ;
			}else if(bank_name==28){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/sibl_lc_bank_form" ) ;
			}else if(bank_name==29){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/bank_asia_lc_bank_form" ) ;
			}else if(bank_name==30){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/dhaka_bank_cf7_bank_form" ) ;
			}else if(bank_name==31){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/ebl_lca_bank_form" ) ;
			}else if(bank_name==32){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/prime_bank_cf7_form" ) ;
			}else if(bank_name==33){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/ibbl_bank_form_cf7" ) ;
			}else if(bank_name==34){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/pubali_bank_form_lca" ) ;
			}else if(bank_name==35){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/scb_bank_form_lca" ) ;
			}else if(bank_name==36){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/jamuna_bank_form_lca" ) ;
			}else if(bank_name==37){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/ncc_bank_form_cf7" ) ;
			}else if(bank_name==38){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/ncc_bank_form_lca" ) ;
			}else if(bank_name==39){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/ucb_bank_form_cf7" ) ;
			}else if(bank_name==40){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/exim_bank_form_lca" ) ;
			}else if(bank_name==41){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/premier_bank_form_lca" ) ;
			}else if(bank_name==42){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/premier_bank_form_cf7" ) ;
			}else if(bank_name==43){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/sb_bank_form_cf7" ) ;
			}else if(bank_name==44){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/al_arafa_islami_bank_cf7" ) ;
			}else if(bank_name==45){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/agrani_bank_cf7" ) ;
			}else if(bank_name==46){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/agrani_bank_lca" ) ;
			}else if(bank_name==47){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/seb_bank_lca" ) ;
			}else if(bank_name==48){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/fsibl_bank_cf7" ) ;
			}else if(bank_name==49){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/fsibl_bank_lca" ) ;
			}else if(bank_name==50){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/dbbl_bank_lca" ) ;
			}else if(bank_name==51){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/city_bank_lca" ) ;
			}else if(bank_name==52){
				var r=confirm("Press  \"Cancel\" to hide in words \nPress  \"OK\" to show in words");
				if (r==true){ show_item="1"; }else{ show_item="0"; }
				print_report( $('#update_id').val()+"**"+show_item, "btb_application_form", "../application_form/requires/ucb_bank_lca" ) ;
			}else if(bank_name==53){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/al_arafa_bank_lca" ) ;
			}else if(bank_name==54){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/sibl_bank_cf7_v2" ) ;
			}else if(bank_name==55){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/mtbl_form_lca" ) ;
			}else if(bank_name==56){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/sibl_bank_form" ) ;
			}		
			else if(bank_name==57){
				print_report( $('#update_id').val(), "btb_application_form", "../application_form/requires/midland_form_lca" ) ;
			}		
		}
	}

    function independence_basis_controll_function(data)
    {
		// alert(data);
        var independent_control_arr = JSON.parse('<? echo json_encode($independent_control_arr); ?>');
        $("#cbo_lc_basis_id option[value='2']").show();
        $("#cbo_lc_basis_id").val(0);
        if(independent_control_arr && independent_control_arr[data]==1)
        {
            $("#cbo_lc_basis_id option[value='2']").hide();
        }
		var max_btb_limit = return_global_ajax_value( data, 'lib_max_btb_limit_data', '', 'requires/btb_margin_lc_controller').split("_");
		//alert(max_btb_limit);
		$("#max_btb_limit").val(max_btb_limit[0]);
		$("#btb_limit_controll").val(max_btb_limit[1]);
    }


	function fnc_letter_print()
	{
		var letter_type=$('#cbo_letter_type').val();
		if (form_validation('update_id','Save Data First')==false)
		{
			alert("Save Data First");
			return;
		}
		else
		{
			//print_report(letter_type+'**'+$('#update_id').val(),'print_letter','requires/btb_margin_lc_controller');
			print_report(letter_type+'**'+$('#update_id').val(),'btb_import_lc_dynamic_letter','requires/btb_margin_lc_controller');
		}
		/*else if(type==4)
		{
			print_report(letter_type+'**'+$('#update_id').val(),'btb_import_lc_dynamic_letter','requires/btb_margin_lc_controller');
		}*/

	}

	function fn_undertaking_letter()
	{
		var entry_form=$('#btbmargin_1').data("entry_form");
		var letter_type=$('#cbo_letter_type').val();

		if (form_validation('update_id','Save Data First')==false)
		{
			alert("Save Data First");
			return;
		}
		else
		{
			print_report(entry_form+'**'+letter_type+'**'+$('#update_id').val(),'undertaking_letter_print','requires/btb_margin_lc_controller');
		}


	}

	function fn_fdd_letter_form()
	{
		var entry_form=$('#btbmargin_1').data("entry_form");
		var letter_type=$('#cbo_letter_type').val();

		if (form_validation('update_id','Save Data First')==false)
		{
			alert("Save Data First");
			return;
		}
		else
		{
			print_report(entry_form+'**'+letter_type+'**'+$('#update_id').val(),'ffd_form_letter_print','requires/btb_margin_lc_controller');
		}
	}

	function fn_set_pay(str)
	{
		if(str==4 || str==5 || str==6)
		{
			$('#cbo_payterm_id').val(3);
		}
		else
		{
			$('#cbo_payterm_id').val(0);
		}
	}

	function sendMail()
	{
		
		if (form_validation('txt_system_id*txt_bank_code*txt_lc_serial','System Id*L/C Number*L/C Number')==false)
		{
			return;
		}
		  
		fnSendMail('../../', '', 1, 0, 0, 1, '', $('#cbo_importer_id').val());

		// var sys_id=$('#txt_system_id').val();
		
		
		// var data="action=btb_margin_lc_auto_mail&sys_id="+sys_id;
 		// freeze_window(operation);
		// http.open("POST","../../auto_mail/btb_margin_lc_auto_mail.php",true);
		// http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		// http.send(data);
		// http.onreadystatechange = function fnc_btb_mst_reponse()
		// {
		// 	if(http.readyState == 4)
		// 	{
		// 		var reponse=trim(http.responseText);
		// 		alert(reponse);
		// 		release_freezing();
				
	
		// 	}
		// }

	}

	function call_print_button_for_mail(mail,mail_body,type)
	{ 
		var sys_id=$('#txt_system_id').val();
		var data="action=btb_margin_lc_auto_mail&sys_id="+sys_id;
 		freeze_window(operation);
		http.open("POST","../../auto_mail/btb_margin_lc_auto_mail.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function fnc_btb_mst_reponse()
		{
			if(http.readyState == 4)
			{
				var reponse=trim(http.responseText);
				alert(reponse);
				release_freezing(); 
	
			}
		}
	}

	function fn_margin(str)
	{
		var lc_val=trim($("#txt_lc_value").val())*1;
		var margin_perset=trim($("#txt_margin_deposit").val())*1;
		var margin_amount=trim($("#txt_margin_deposit_amt").val())*1;
		var ref_val=0;
		if(str==1)
		{
			ref_val=(lc_val*margin_perset/100);
			$("#txt_margin_deposit_amt").val(ref_val);
		}
		else
		{
			
			ref_val=((margin_amount/lc_val)*100);
			$("#txt_margin_deposit").val(ref_val);
		}
	}

	function show_without_lc_number() 
	{
		if (form_validation('cbo_importer_id','Importer')==false)
		{
			return;
		}
     	show_list_view($('#cbo_importer_id').val(), 'show_without_lc_number', 'without_lc_number', 'requires/btb_margin_lc_controller', 'setFilterGrid(\'tbl_list_search_without_lc\',-1);');
    }

	function fnc_set_form_data(data)
	{
		freeze_window(5);
		var data = data.split("**");
		var btb_id=data[0];
		var ref_closing_status=data[1];
		get_php_form_data( btb_id, "populate_data_from_btb_lc", "requires/btb_margin_lc_controller" );

		show_list_view(trim(btb_id),'show_lc_listview','sc_lc_list_view','requires/btb_margin_lc_controller','');

		var item_category = $('#cbo_item_category_id').val();
		var txt_hidden_pi_id = $('#txt_hidden_pi_id').val();
		var pi_entry_form = $("#txt_hidden_pi_item").val();
		$("#hidden_ref_closing_status").val(ref_closing_status);
		//alert(pi_entry_form);

		if(pi_entry_form!="")
		{
			show_list_view(txt_hidden_pi_id+'_'+pi_entry_form+'_'+item_category,'show_pi_details_list','pi_details_list','requires/btb_margin_lc_controller','setFilterGrid(\'pi_details_list\',-1)');
		}

		var numRow = $('table#tbl_lc_list tbody tr').length;
		var button_status='';

		if(numRow==1)
		{
			var lc_sc_no=$('#txtlcsc_1').val();
			if(lc_sc_no=="")
			{
				$('#txt_tot_row').val(0);
				button_status=0;
			}
			else
			{
				$('#txt_tot_row').val(numRow);
				button_status=1;
			}
		}
		else
		{
			$('#txt_tot_row').val(numRow);
			button_status=1;
		}

		var ddd={ dec_type:4, comma:0, currency:$('#cbo_pi_currency_id').val()}

		math_operation( "totalLcScValue", "txtlcscvalue_", "+", numRow,ddd );
		math_operation( "totalCurrentDistri", "txtcurdistribution_", "+", numRow,ddd );
		math_operation( "totalCumuDisri", "txtcumudistribution_", "+", numRow,ddd );
		calculate_occupied(numRow);
		set_button_status(button_status, permission, 'fnc_lc_details',2);
		release_freezing();
	}

	function uploadFile(mst_id){
	$(document).ready(function() {
		var fd = new FormData();
		var files = $('#pi_mst_file')[0].files; 
		 for (let i = 0; i < files.length; i++) {
				 fd.append('file[]',files[i],files[i].name);
			}
		//fd.append('pi_mst_file',this.file_group_id);
		//fd.append('file', files); 
		$.ajax({ 
			url: 'requires/btb_margin_lc_controller.php?action=file_upload&mst_id='+ mst_id, 
			type: 'post', 
			data: fd,  
			contentType: false, 
			processData: false, 
			
			success: function(response){
				if(response != 0){
					document.getElementById('pi_mst_file').value=null;
				} 
				else{ 
					alert('file not uploaded'); 
				} 
			}, 
		}); 
	}); 
}


</script>
<style>
	#tbl_btb input
	{
		width:155px;
	}
</style>

<body onLoad="set_hotkey()">
    <div align="center" style="width:900px;">
        <? echo load_freeze_divs ("../../", $permission); ?>
        <div>
            <form name="btbmargin_1" id="btbmargin_1" autocomplete="off" data-entry_form="105">
                <fieldset style="width:1224px;">
                    <legend>BTB / Margin LC</legend>
					<div style="width:860px; float:left;" align="center">
						<fieldset style="width:860px;">
							<table width="100%" border="0" cellpadding="0" cellspacing="2" id="tbl_btb">
								<tr>
									<td></td>
									<td></td>
									<td align="right"><strong>System ID</strong></td>
									<td>
										<input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" placeholder="Double Click to Search L/C" onDblClick="openmypage(1,'')" readonly />
										<input type="hidden" name="update_id" id="update_id" value=""/>
										<input type="hidden" name="hidden_ref_closing_status" id="hidden_ref_closing_status" />
									</td>
									<td></td>
									<td></td>
								</tr>
								<tr height="10"></tr>
								<tr>
									<td width="150" class="must_entry_caption">Importer</td>
									<td width="120">
										<?php //
										echo create_drop_down( "cbo_importer_id", 165,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name",'id,company_name', 1, '----Select----',0,"independence_basis_controll_function(this.value);load_drop_down( 'requires/btb_margin_lc_controller',this.value+'_'+document.getElementById('cbo_item_category_id').value, 'load_supplier_dropdown', 'supplier_td' );print_button_setting();",0); ?>
                                        <input type="hidden" name="max_btb_limit" id="max_btb_limit" value=""/>
                                        <input type="hidden" name="btb_limit_controll" id="btb_limit_controll" value=""/>

									</td>
									<td width="120" class="must_entry_caption">Application Date</td>
									<td width="180"><input type="text" name="application_date" id="application_date" value="<?echo date('d-m-Y')?>" class="datepicker" /></td>
									<td width="150" class="must_entry_caption">Issuing Bank</td>
									<td>
										<?php 
											if ($db_type==0)
											{
												echo create_drop_down( "cbo_issuing_bank", 165,"select id,concat(a.bank_name,' (', a.branch_name,')') as bank_name from lib_bank where is_deleted=0 and status_active=1 and issusing_bank = 1 order by bank_name",'id,bank_name', 1, '----Select----',0,0,0);
											}
											else
											{
												//get_php_form_data(this.value, 'load_band_code', 'requires/btb_margin_lc_controller' );
												echo create_drop_down( "cbo_issuing_bank", 165,"select id,(bank_name || ' (' || branch_name || ')' ) as bank_name from lib_bank where is_deleted=0 and status_active=1 and issusing_bank = 1 order by bank_name",'id,bank_name', 1, '----Select----',0,"	",0);
											}
										?>
									</td>
								</tr>
								<tr>
									<td>Item Category</td>
									<td>
										<?= create_drop_down( "cbo_item_category_id", 165, $item_category_with_gen,'', 1, '----Select----',0,"load_drop_down( 'requires/btb_margin_lc_controller',document.getElementById('cbo_importer_id').value+'_'+this.value, 'load_supplier_dropdown', 'supplier_td' );",0,'','','','74,72,79,73,71,77,78,75,76');?>
									<input type="hidden" name="txt_prev_pi_id" id="txt_prev_pi_id" class="text_boxes"  />
									</td>
									<td class="must_entry_caption">LC Basis</td>
									<td>
										<?= create_drop_down( "cbo_lc_basis_id", 165, $lc_basis,'', 0,'',1,"active_inactive(this.value)",0,'','','','2'); ?>
									</td>
									<td class="must_entry_caption">Pro Forma Invoice</td>
									<td>
									<input type="text" name="txt_pi" id="txt_pi" class="text_boxes" placeholder="Double Click for PI" onDblClick="openmypage(2,'')" readonly />
									<input type="hidden" name="txt_hidden_pi_id" id="txt_hidden_pi_id" class="text_boxes"  />
									<input type="hidden" name="txt_hidden_pi_item" id="txt_hidden_pi_item" class="text_boxes"  />
									</td>
								</tr>
								<tr>
									<td>PI Value</td>
									<td>
										<input type="text" name="txt_pi_value" id="txt_pi_value" class="text_boxes_numeric"  style="width:80px" disabled />
										<?= create_drop_down( "cbo_pi_currency_id",70,$currency,'',0,'',2,"",0); ?>
									</td>
									<td class="must_entry_caption">Supplier</td>
									<td id="supplier_td">
										<?= create_drop_down( "cbo_supplier_id", 165,$blank_array,'', 1, '----Select----',0,0,0); ?>
									</td>
									<td class="must_entry_caption">L/C Type</td>
									<td>
										<?= create_drop_down( "cbo_lc_type_id",165,$lc_type,'',1,'-Select',1,"fn_set_pay(this.value)",0); ?>
									</td>
								</tr>
								<tr>
									<td>L/C Number</td>
									<td>
										<input type="text" name="txt_bank_code" id="txt_bank_code" class="text_boxes_numeric" maxlength="4" style="width:30px" onKeyUp="fnc_move_cursor(this.value,'txt_lc_year',4)" placeholder="Bank Code" />
										<input type="text" name="txt_lc_year" id="txt_lc_year" class="text_boxes_numeric" maxlength="2"  style="width:16px" onKeyUp="fnc_move_cursor(this.value,'txt_category',2)" placeholder="Year"/>
										<input type="text" name="txt_category" id="txt_category" class="text_boxes_numeric" maxlength="2" style="width:16px" onKeyUp="fnc_move_cursor(this.value,'txt_lc_serial',2)" onBlur="fnc_len_check(this.value)" placeholder="Category"/>
										<input type="text" name="txt_lc_serial" id="txt_lc_serial" class="text_boxes" maxlength="25" style="width:45px" placeholder="Serial"/>
									</td>
									<td>L/C Date</td>
									<td><input type="text" name="txt_lc_date" id="txt_lc_date" class="datepicker" style="width:45px" value=""/>&nbsp;L/C Ref. No<input type="text" name="txt_lc_reference_no" id="txt_lc_reference_no" class="text_boxes" style="width:35px" value=""/></td>
									<td class="must_entry_caption">Last Shipment Date</td>
									<td><input type="text" name="txt_last_shipment_date" id="txt_last_shipment_date" class="datepicker" value="" onChange="add_days(this.value,document.getElementById('txt_expiry_days').value,1,'txt_lc_expiry_date')" style="width:55px;" />&nbsp;Expiry Days <input type="text" name="txt_expiry_days" id="txt_expiry_days" class="text_boxes_numeric" maxlength="2"  style="width:22px" value="15" onBlur="add_days(document.getElementById('txt_last_shipment_date').value,this.value,1,'txt_lc_expiry_date')"/></td>
								</tr>
								<tr>
									<td height="24" class="must_entry_caption">L/C Expiry Date</td>
								<td><input type="text" name="txt_lc_expiry_date"  id="txt_lc_expiry_date" class="datepicker" value="" /></td>
									<td class="must_entry_caption">L/C Value</td>
									<td>
										<input type="text" name="txt_lc_value" id="txt_lc_value" class="text_boxes_numeric"  style="width:80px" disabled />
										<?php echo create_drop_down( "cbo_lc_currency_id",70,$currency,'',0,'',2,0,0); ?>
									</td>
									<td class="must_entry_caption">Incoterm</td>
									<td><?php echo create_drop_down( "cbo_inco_term_id",165,$incoterm,'',1,'-Select-',0,0,0); ?> </td>
								</tr>
								<tr>
									<td>Incoterm Place</td>
									<td><input type="text"  name="txt_inco_term_place" id="txt_inco_term_place" class="text_boxes" /></td>
									<td class="must_entry_caption">Pay Term</td>
									<td><?php echo create_drop_down( "cbo_payterm_id",165,$pay_term,'',1,'-Select-',0,"",0,'1,2,3,4');//set_port_loading_value(this.value)1,2 ?></td>
									<td class="must_entry_caption">Tenor</td>
									<td><input type="text"  name="txt_tenor" id="txt_tenor" class="text_boxes_numeric" /></td> 
								</tr>
								<tr>
									<td>Tolerance %</td>
									<td><input type="text"  name="txt_tolerance" id="txt_tolerance" class="text_boxes_numeric" value="5" /></td>
									<td class="must_entry_caption">Delivery Mode</td>
									<td><?php echo create_drop_down( "cbo_delevery_mode",165,$shipment_mode,'',1,'-Select-',1,0,0); ?></td>
									<td>Doc Present Days</td>
									<td><input type="text"  name="txt_doc_perc_days" id="txt_doc_perc_days" class="text_boxes" /></td>
								</tr>
								<tr>
									<td>Port of Loading</td>
									<td><input type="text"  name="txt_port_loading" id="txt_port_loading" class="text_boxes" maxlength="50" /></td>
									<td>Port of Discharge</td>
									<td><input type="text"  name="txt_port_discharge" id="txt_port_discharge" class="text_boxes"  maxlength="50" /></td>
									<td>ETD Date</td>
									<td><input type="text" name="txt_etd_date" id="txt_etd_date" class="datepicker" value="" /></td>
								</tr>
								<tr>
									<td>LCA No</td>
									<td><input type="text" name="txt_lca_no" id="txt_lca_no" class="text_boxes" value=""  maxlength="30" /></td>
									<td>LCAF No</td>
									<td><input type="text" name="txt_lcaf_no" id="txt_lcaf_no" class="text_boxes" value=""  maxlength="30" /></td>
									<td>IMP Form No</td>
									<td><input type="text" name="txt_imp_form_no" id="txt_imp_form_no" class="text_boxes" value=""  maxlength="50" /></td>
								</tr>
								<tr>
									<td>Insurance Company</td>
									<td><input type="text" name="txt_insurance_company" id="txt_insurance_company" class="text_boxes" value=""  maxlength="50" /></td>
									<td>Cover Note No</td>
									<td><input type="text" name="txt_cover_note_no" id="txt_cover_note_no" class="text_boxes" value=""  maxlength="50" /></td>
									<td>Cover Note Date</td>
									<td><input type="text" name="txt_cover_note_date" id="txt_cover_note_date" class="datepicker" value="" /></td>
								</tr>
								<tr>
									<td>PSI Company</td>
									<td><input type="text" name="txt_psi_company" id="txt_psi_company" class="text_boxes" value=""  maxlength="50" /></td>
									<td  class="must_entry_caption">Maturity From</td>
									<td><?php echo create_drop_down( "cbo_maturit_from_id",165,$maturity_from,'',1,'-Select-',0,0,0); ?></td>
									<td class="must_entry_caption">Margin Deposit %</td>
									<td>
									<input type="text" name="txt_margin_deposit" id="txt_margin_deposit" class="text_boxes_numeric" value="" style="width:40px;" placeholder="%" onBlur="fn_margin(1)" />
									<input type="text" name="txt_margin_deposit_amt" id="txt_margin_deposit_amt" class="text_boxes_numeric" value="" style="width:100px;" placeholder="Amount"  onBlur="fn_margin(2)" />
									</td>
								</tr>
								<tr>
									<td>Origin</td>
									<td><?php echo create_drop_down( "cbo_origin_id",165,"SELECT id,country_name from lib_country order by country_name",'id,country_name',1,'-Select-',0,0,0); ?></td>
									<td>Shipping Mark</td>
									<td><input type="text" name="txt_shiping_mark" id="txt_shiping_mark" class="text_boxes" value=""  maxlength="200" /></td>
									<td>Garments Qnty & UOM</td>
									<td>
										<input type="text" name="txt_gmt_qnty" id="txt_gmt_qnty" class="text_boxes_numeric" value="" style="width:90px;" />
										<?php echo create_drop_down( "cbo_gmt_uom_id",60,$unit_of_measurement,'',0,'',1,'',1,0); ?>
									</td>
								</tr>
								<tr>
									<td>UD No</td>
									<td><input type="text" name="txt_ud_no" id="txt_ud_no" class="text_boxes" value=""  maxlength="50" /></td>
									<td>UD Date</td>
									<td><input type="text" name="txt_ud_date" id="txt_ud_date" class="datepicker" value="" /></td>
									<td>Credit To Be Advised</td>
									<td><?php echo create_drop_down( "cbo_credit_advice_id",165,$credit_to_be_advised,'',0,'',1,0,0); ?></td>
								</tr>
								<tr>
								<td>Partial Shipment</td>
								<td><?php echo create_drop_down( "cbo_partial_ship_id",165,$yes_no,'',0,'',2,0,0); ?></td>
								<td>Transhipment</td>
								<td><?php echo create_drop_down( "cbo_transhipment_id",165,$yes_no,'',0,'',1,0,0); ?></td>
								<td>Add Confirmation Req.</td>
								<td><?php echo create_drop_down( "cbo_add_confirm_id",165,$yes_no,'',0,'',2,0,0); ?></td>
								</tr>
								<tr>
								<td>Add Confirming Bank</td>
								<td><input type="text" name="txt_conf_bank" id="txt_conf_bank" class="text_boxes" value=""  maxlength="50" /></td>
								<td>Bonded Warehouse</td>
								<td><?php echo create_drop_down( "cbo_bond_warehouse_id",165,$yes_no,'',0,'',2,0,0); ?></td>
								<td>Status</td>
								<td>
									<?php echo create_drop_down( "cbo_status", 165, $row_status,'', 0, '',1,0,''); ?>
								</td>
								</tr>
								<tr>
									<td>UPAS Rate %</td>
									<td><input type="text" name="txt_upas_rate" id="txt_upas_rate" class="text_boxes_numeric" value="" /></td>
									<td>Remarks</td>
									<td ><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" value="" style="width:165px"  maxlength="250"/></td>
									<td>File No</td>
									<td>
										<input type="text" name="txt_internal_file_no" id="txt_internal_file_no"  style="width:155px"  class="text_boxes" readonly maxlength="50" onClick="openmypage_file_info()" placeholder="Browse"  />
										<input type="hidden" name="is_lc_sc" id="is_lc_sc"/>
										<input type="hidden" name="lc_sc_id" id="lc_sc_id"/>
										<input type="hidden" name="lc_sc_no" id="lc_sc_no"/>
										<input type="hidden" name="lc_sc_file_year" id="lc_sc_file_year"/>
									</td>
								</tr>
								<tr>
									<td>Advising / Party Bank </td>
									<td>
									<input type="text" name="cbo_adv_bank" id="cbo_adv_bank" class="text_boxes" value="" />
									<!-- <?
									echo create_drop_down("cbo_adv_bank", 165, "select bank_name,id from lib_bank where is_deleted=0 and status_active=1 and lien_bank=1 order by bank_name", "id,bank_name", 1, "-- Select Advising Bank --", 0, "");
									?> -->
									</td>
									<td>Advising bank Address</td>
									<td colspan="3"><input type="text" name="txt_adv_bank_address" id="txt_adv_bank_address" class="text_boxes" value="" style="width:475px"  maxlength="150"/></td>
								</tr>
								<tr>
									<td  align="left">File</td>
									<td align="left">
										<input type="file" multiple id="pi_mst_file" class="image_uploader" style="width:150px" onChange="document.getElementById('txt_file').value=1">
										<input type="hidden" multiple id="txt_file">
									</td>
									<td colspan="2">
										<input type="button" id="image_button" class="image_uploader" style="width:75px;" value="IMAGE" onClick="file_uploader( '../../', document.getElementById('update_id').value,'', 'BTBMargin LC',1,1)" />

										<input type="button" id="image_button" class="image_uploader" style="width:75px;" value="FILE" onClick="file_uploader( '../../', document.getElementById('update_id').value,'', 'BTBMargin LC',2,1)" />
									</td>
									<td align="right" height="10">
										<?
											include("../../terms_condition/terms_condition.php");
											terms_condition(105,'txt_system_id','../../');
										?>
									</td>
									<!-- <td align="right" colspan="6">
										<input type="button" id="image_button" class="image_uploader" style="width:105px; float:right; margin-right:105px;" value="CLICK TO ADD FILE" onClick="file_uploader('../../', document.getElementById('txt_system_id').value, '', 'BTBMargin LC', 2, 1)" />
									</td> -->
								
								</tr>
				
								<tr>
									<td colspan="6" height="15"></td>
								</tr>
								<tr>
									<td colspan="6" height="50" valign="middle" align="center" class="button_container">
										<?= load_submit_buttons( $permission, "fnc_btb_mst", 0,0 ,"reset_form('btbmargin_1*lcform_2','pi_details_list','','','disable_enable_fields(\'cbo_importer_id*txt_last_shipment_date*txt_lc_expiry_date*cbo_delevery_mode*cbo_inco_term_id*txt_inco_term_place*cbo_delevery_mode*txt_port_of_loading*txt_port_of_discharge*cbo_payterm_id*txt_tenor*txt_pi*txt_remarks*cbo_lc_type_id\',0)');active_inactive(1);$('#tbl_order_list tbody tr:not(:first)').remove();",1) ;?>
										<?= create_drop_down( "cbo_letter_type", 150, $letter_type_arr,"", 1, "-- select --", 0, "","","9" );?>
										<input class="formbutton" type="button" onClick="sendMail()" value="Mail Send" style="width:80px;">
										<span id="button_data_panel"></span>
										<input class="formbutton" type="button" onClick="fn_print_letter(30)" value="LC Forwarding 6" style="width:100px;">
 
										<!-- <input type="button" class="formbutton" id="btn_fdd_form" value="FDD Form" style="width:120px;" onClick="fn_fdd_letter_form()" > -->
									</td>
								</tr>
							</table>
						</fieldset>
					</div>
					<input type="button" style="width:80px; overflow:auto; float:left; margin-top:40px; margin-left:30px; position:relative;" id="show"  onClick="show_without_lc_number()" class="formbutton" name="show" value="Show" title="BTB Without LC Number" />
					<div id="without_lc_number"
					style="max-height:300px; width:350px; overflow:auto; margin-top:80px; position:relative;"></div>
                </fieldset>
            </form>
            <form name="lcform_2" id="lcform_2" autocomplete="off">
                <fieldset style="width:1054px; margin-top:10px;">
                    <legend>LC/SC attached</legend>
                    	<div id="lc_details_container" style="max-height:350px; overflow:auto;">
                      		<table>
                            	<tr>
                                    <td align="center" colspan="8" style="padding-bottom:10px;">
                                        <strong>Distribution Method:</strong>
                                      <input type="radio" name="distribution_type" id="distribution_type_0" value="0" onClick="distribution_value(this.value,0)" checked /><label for="distribution_type_0">Proportionately</label>
                                        <input type="radio" name="distribution_type" id="distribution_type_1" value="1" onClick="distribution_value(this.value,0)"  /><label for="distribution_type_1">Manually</label>
                                    </td>
                                </tr>
                            </table>
                          <div style="max-height:130px;overflow:auto;">
                            <table width="100%" cellspacing="0" cellpadding="0" class="rpt_table" id="tbl_lc_list">
                                <thead>
                                    <tr>
                                        <th width="120">SC/LC No</th>
                                        <th width="120">Buyer</th>
                                        <th width="70">LC/SC</th>
                                        <th width="120">LC/SC Value</th>
                                        <th width="120">Current Distribution</th>
                                        <th width="120">Cumulative Distribution</th>
                                        <th width="100">Occupied %</th>
                                        <th width="100">Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="sc_lc_list_view">
                                    <tr class="general">
                                        <td>
                                        <input type="text" name="txtlcsc_1" id="txtlcsc_1" class="text_boxes" style="width:100px"  onDblClick= "openmypage(3,1)" readonly= "readonly" placeholder="Double For Search" />
                                        <input type="hidden" name="txtLcScid_1" id="txtLcScid_1" class="text_boxes" style="width:100px" value="" readonly= "readonly" />
                                        <input type="hidden" name="txtLcScidType_1" id="txtLcScidType_1" class="text_boxes" style="width:100px" value="" readonly= "readonly" />
                                        <input type="hidden" name="maxBtbLimit_1" id="maxBtbLimit_1" class="text_boxes" style="width:100px" value="" readonly= "readonly" />
                                        </td>
                                        <td><input type="text" name="txtbuyer_1" id="txtbuyer_1" class="text_boxes" style="width:120px;" readonly= "readonly" />
										<input type="hidden" name="cboBuyerID" id="cboBuyerID" value="0" >
										</td>
                                        <td>
                                        <input type="text" name="txtlcscflag_1" id="txtlcscflag_1" class="text_boxes" style="width:70px;" readonly= "readonly" />
                                        <input type="hidden" name="txtlcscflagId_1" id="txtlcscflagId_1" class="text_boxes" style="width:100px" value="" readonly= "readonly" />
                                        </td>
                                        <td><input type="text" name="txtlcscvalue_1" id="txtlcscvalue_1" class="text_boxes_numeric" style="width:120px;" readonly= "readonly"/></td>
                                        <td><input type="text" name="txtcurdistribution[]" id="txtcurdistribution_1" class="text_boxes_numeric" style="width:120px" disabled="disabled"/></td>
                                        <td>
                                        <input type="text" name="txtcumudistribution_1" id="txtcumudistribution_1" class="text_boxes_numeric" style="width:120px" readonly= "readonly"/>
                                        <input type="hidden" name="hiddencumudistribution_1" id="hiddencumudistribution_1" class="text_boxes_numeric" style="width:120px"/>
                                        <input type="hidden" name="hiddenExchangeRate_1" id="hiddenExchangeRate_1" class="text_boxes_numeric" style="width:120px" value="<? ?>" readonly />
                                        </td>
                                        <td><input type="text" name="txtoccupied_1" id="txtoccupied_1"  style="width:100px" class="text_boxes_numeric" readonly= "readonly"/></td>

                                        <td>
                                            <?
                                                 echo create_drop_down( "cbostatus_1", 100, $row_status,"", 0, "", 1, "" );
                                            ?>
                                        </td>
                                        <td width="65">
                                            <input type="button" id="increase_1" name="increase_1" style="width:25px" class="formbuttonplasminus" value="+" onClick="fn_add_row_lc(0)" />
                                            <input type="button" id="decrease_1" name="decrease_1" style="width:25px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(0)" />
                                            <input type="hidden" name="txtcaltype_1" id="txtcaltype_1" class="text_boxes" style="width:100px" value="0" readonly= "readonly" />
                                       </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="tbl_bottom">
                                      <td colspan="3">Total</td>
                                      <td align="center"><input type="text" name="totalLcScValue" id="totalLcScValue" class="text_boxes_numeric" style="width:120px;" readonly= "readonly" /></td>
                                      <td align="center"><input type="text" name="totalCurrentDistri" id="totalCurrentDistri" class="text_boxes_numeric" style="width:120px;" readonly= "readonly" /></td>
                                      <td align="center"><input type="text" name="totalCumuDisri" id="totalCumuDisri" class="text_boxes_numeric" style="width:120px;" readonly= "readonly" /></td>
                                      <td colspan="3"><input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes_numeric"  readonly= "readonly" value="0" /></td>
                                    </tr>
                                </tfoot>
                            </table>
                         </div>
           				<div style="max-height:100px;overflow:hidden;">
                           <table>
                           		 <tr>
                                    <td colspan="11" height="50" valign="middle" align="center" class="button_container">
                                    <? echo load_submit_buttons( $permission, "fnc_lc_details", 0,0 ,"reset_form('lcform_2','','','','$(\'#tbl_lc_list tbody tr:not(:first)\').remove();')",2) ; ?>
                                    </td>
                                </tr>
                           </table>
                   		</div>
                     </div>
                </fieldset>
            </form>
             <form name="pimasterform_3" id="pimasterform_3" autocomplete="off">
                <fieldset style="width:1050px; margin-top:10px;">
                    <legend>PI Item List</legend>
                    <div id="pi_details_list" style="max-height:200px; overflow:auto;"></div>
                </fieldset>
            </form>
        </div>
    </div>
</body>
<script>
	$(function(){
		// alert("body loaded");
		for (var property in mandatory_field_arr) {
			// alert(property)
			$("#"+mandatory_field_arr[property]).parent().prev('td').css("color", "blue");
		}
	});
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>