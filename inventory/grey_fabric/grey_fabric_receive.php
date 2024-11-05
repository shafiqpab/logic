<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Grey Fabric Receive
Functionality	:
JS Functions	:
Created by		:	Fuad Shahriar
Creation date 	: 	12/05/2013
Updated by 		:   Kausar (Creating Report)
Update date		:   12-12-2013
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
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];
$location_id = $userCredential[0][csf('location_id')];

$company_credential_cond = "";

if ($company_id >0) {
    $company_credential_cond = "and comp.id in($company_id)";
}

if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)";
}

if($item_cate_id !='') {
    $item_cate_credential_cond = $item_cate_id ;
}
//========== user credential end ==========

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Grey Fabric Receive ", "../../", 1, 1,'','1','');

$independent_control_arr = return_library_array( "select company_name, independent_controll from variable_settings_inventory where variable_list=20 and menu_page_id=22 and status_active=1 and is_deleted=0",'company_name','independent_controll');
if (empty($independent_control_arr)) $independent_control_arr=0;

?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';

	var str_brand = [<? echo substr(return_library_autocomplete( "select distinct(brand_name) from lib_brand where status_active=1 and is_deleted=0", "brand_name"  ), 0, -1); ?>];
	//var str_color = [<?echo substr(return_library_autocomplete( "select color_name from lib_color where status_active=1 and is_deleted=0 group by color_name", "color_name" ), 0, -1); ?>];

	<?
	//$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][22]);
	//if($data_arr) echo "var field_level_data= ". $data_arr . ";\n";
	//echo "alert(JSON.stringify(field_level_data));";
	if($_SESSION['logic_erp']['mandatory_field'][22]!="")
	{
		$mandatory_field_arr= json_encode( $_SESSION['logic_erp']['mandatory_field'][22] );
		echo "var mandatory_field_arr= ". $mandatory_field_arr . ";\n";
	}
	?>

	$(document).ready(function(e)
	{
		$("#txt_brand").autocomplete({
			source: str_brand
		});

		   /*$("#txt_color").autocomplete({
			 source: str_color
			});*/
		});


	function set_receive_basis()
	{
		var recieve_basis = $('#cbo_receive_basis').val();

		$('#booking_without_order').val(0);
		$('#txt_job_no').val('');
		$('#txt_receive_qnty').val('');
		$('#all_po_id').val('');
		$('#save_data').val('');
		$('#save_data2').val('');
		$('#txt_color').val('');
		$('#color_id').val('');
		$('#txt_deleted_id').val('');
		$('#roll_details_list_view').html('');
		$('#txt_rate').val('');
		$('#txt_amount').val('');
		$('#grey_prod_dtls_id').val('');

		$('#txt_receive_qnty').attr('readonly','readonly');
		$('#txt_receive_qnty').attr('onClick','openmypage_po();');
		$('#txt_receive_qnty').attr('placeholder','Single Click');

		$('#cbo_knitting_source').removeAttr('disabled','disabled');
		$('#cbo_knitting_company').removeAttr('disabled','disabled');
		$('#cbo_knitting_source').val(0);
		$('#cbo_knitting_company').val(0);

		if(recieve_basis == 4 || recieve_basis == 6 )
		{
			$('#txt_booking_no').val('');
			$('#txt_booking_no_id').val('');
			$('#txt_booking_no').attr('disabled','disabled');
			$('#cbo_buyer_name').removeAttr('disabled','disabled');
			$('#cbo_body_part').removeAttr('disabled','disabled');
			$('#fabric_desc_id').val('');
			$('#txt_fabric_description').val('');
			$('#txt_fabric_description').removeAttr('disabled','disabled');
			//set_auto_complete();
		}
		else
		{
			$('#txt_booking_no').val('');
			$('#txt_booking_no_id').val('');
			$('#txt_booking_no').removeAttr('disabled','disabled');
			$('#cbo_buyer_name').val(0);
			$('#cbo_buyer_name').attr('disabled','disabled');

			if(recieve_basis==1)
			{
				$('#cbo_body_part').val(0);
				$('#cbo_body_part').removeAttr('disabled','disabled');
				$('#cbo_buyer_name').removeAttr('disabled','disabled');
				//set_auto_complete();
			}
			else
			{
				$('#cbo_body_part').val(0);
				$('#cbo_body_part').attr('disabled','disabled');
				$('#cbo_buyer_name').val(0);
				$('#cbo_buyer_name').attr('disabled','disabled');
			}

			$('#fabric_desc_id').val('');
			$('#txt_fabric_description').val('');
			$('#txt_fabric_description').attr('disabled','disabled');
		}

	}

	function openmypage_wo_pi_production_popup()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var recieve_basis = $('#cbo_receive_basis').val();
		var garments_nature = $('#garments_nature').val();
		var roll_maintained = $('#roll_maintained').val();
		//var process_costing_maintain = $('#process_costing_maintain').val();
		if($("#update_id").val() !=""){
			return;
		}

		if(recieve_basis==14)
		{
			if(roll_maintained !=1)
			{
				alert("Sales Order Based receive only allowed with roll maintained;");
				return;
			}
		}

		if (form_validation('cbo_company_id*cbo_receive_basis','Company*Receive Basis')==false)
		{
			return;
		}
		else
		{
			var title = 'WO/PI/Production Selection Form';
			var page_link = 'requires/grey_fabric_receive_controller.php?cbo_company_id='+cbo_company_id+'&recieve_basis='+recieve_basis+'&garments_nature='+garments_nature+'&action=wo_pi_production_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=400px,center=1,resize=1,scrolling=0','../');

			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var theemail=this.contentDoc.getElementById("hidden_wo_pi_production_id").value;	 //Knit Id for Kintting Plan
				var theename=this.contentDoc.getElementById("hidden_wo_pi_production_no").value; //all data for Kintting Plan
				var booking_without_order=this.contentDoc.getElementById("booking_without_order").value; //Access form field with id="emailfield"
				var hidden_buyer_id=this.contentDoc.getElementById("hidden_buyer_id").value; //Access form field with id="emailfield"
				var hidden_production_data=this.contentDoc.getElementById("hidden_production_data").value; //Access form field with id="emailfield"
				var knitting_company=this.contentDoc.getElementById("hidden_knitting_company").value; //Access form field with id="emailfield"

				if(theemail!="")
				{
					freeze_window(5);
					set_receive_basis();

					if(recieve_basis==2 || recieve_basis==11)
					{
						get_php_form_data(theemail+"**"+booking_without_order+"**"+recieve_basis+"**"+roll_maintained, "populate_data_from_booking", "requires/grey_fabric_receive_controller" );
						show_list_view(theename+"**"+booking_without_order+"**"+recieve_basis+"**"+cbo_company_id,'show_fabric_desc_listview','list_fabric_desc_container','requires/grey_fabric_receive_controller','');
						//if(recieve_basis==11)
						//{
							$('#cbo_knitting_source').val(3);
							load_drop_down('requires/grey_fabric_receive_controller','3_'+cbo_company_id, 'load_drop_down_knitting_com','knitting_com');
							$('#cbo_knitting_company').val(knitting_company);
							$('#cbo_knitting_source').attr('disabled','disabled');
							$('#cbo_knitting_company').attr('disabled','disabled');
						//}
					}
					else if(recieve_basis==14)
					{
						get_php_form_data(theemail+"**"+booking_without_order+"**"+recieve_basis+"**"+roll_maintained, "populate_data_from_sales_order", "requires/grey_fabric_receive_controller" );

						show_list_view(theename+"**"+booking_without_order+"**"+recieve_basis+"**"+cbo_company_id,'show_fabric_desc_listview_sales_order','list_fabric_desc_container','requires/grey_fabric_receive_controller','');

						$('#cbo_knitting_source').val(3);
						//load_drop_down('requires/grey_fabric_receive_controller','3_'+cbo_company_id, 'load_drop_down_knitting_com','knitting_com');
						//$('#cbo_knitting_company').val(knitting_company);
						$('#cbo_knitting_source').attr('disabled','disabled');
						$('#cbo_knitting_company').attr('disabled','disabled');

					}
					else
					{
						if(recieve_basis==9)
						{
							$('#cbo_buyer_name').val(hidden_buyer_id);
							if(booking_without_order==1)
							{
								$('#txt_receive_qnty').removeAttr('readonly','readonly');
								$('#txt_receive_qnty').removeAttr('onClick','onClick');
								$('#txt_receive_qnty').removeAttr('placeholder','placeholder');
								if($('#process_costing_maintain').val()==1)
								{
									$('#txt_receive_qnty').attr('onkeyup','calculate_amount();');
								}
							}
							else
							{
								$('#txt_receive_qnty').attr('readonly','readonly');
								$('#txt_receive_qnty').attr('onClick','openmypage_po();');
								$('#txt_receive_qnty').attr('placeholder','Single Click');
							}

							var data=hidden_production_data.split("**");
							$('#cbo_knitting_source').val(data[0]);
							$('#txt_receive_chal_no').val(data[2]);
							$('#txt_yarn_issue_challan_no').val(data[3]);
							$('#txt_job_no').val(data[4]);
							load_drop_down( 'requires/grey_fabric_receive_controller',data[0]+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_knitting_com','knitting_com');
							$('#cbo_knitting_company').val(data[1]);
							load_location();
							$('#cbo_knitting_source').attr('disabled','disabled');
							$('#cbo_knitting_company').attr('disabled','disabled');
						}
						else if(recieve_basis==1)
						{
							$('#cbo_knitting_source').val(3);
							load_drop_down( 'requires/grey_fabric_receive_controller',3+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_knitting_com','knitting_com');
							$('#cbo_knitting_company').val(knitting_company);
						}

						$('#txt_booking_no').val(theename);
						$('#txt_booking_no_id').val(theemail);
						$('#booking_without_order').val(booking_without_order);
						show_list_view(theemail+"**"+booking_without_order+"**"+recieve_basis+"**"+cbo_company_id,'show_fabric_desc_listview','list_fabric_desc_container','requires/grey_fabric_receive_controller','');
					}
					//set_auto_complete();
					release_freezing();
				}
			}
		}
	}

	function set_form_data(data)
	{
		var recieve_basis = $('#cbo_receive_basis').val();
		var roll_maintained = $('#roll_maintained').val();
		var knitting_source = $('#cbo_knitting_source').val();
		var process_costing_maintain = $('#process_costing_maintain').val();
		var receive_qty = $('#txt_receive_qnty').val();

		if(recieve_basis==9)
		{
			get_php_form_data(data+"**"+roll_maintained+"**"+knitting_source+"**"+process_costing_maintain, "populate_data_from_production", "requires/grey_fabric_receive_controller" );
		}
		else if(recieve_basis==14)
		{
			var data=data.split("**");

			$('#txt_old_gsm').val(data[2]);
			$('#txt_old_dia').val(data[3]);

			$('#txt_yarn_lot').removeAttr('readonly');
			$('#txt_yarn_lot').removeAttr('placeholder');
			$('#txt_yarn_lot').removeAttr('onclick');

			var totalAllowed = parseFloat(data[5]);
			var totalProduction = data[6];
			if(data[7] != ''){
				var over_receive_limit = parseFloat(data[7]); // Percentage
			}else{
				var over_receive_limit = 0;
			}

			var result = parseFloat((over_receive_limit / 100) * totalAllowed);
			if(over_receive_limit != ""){
				$('#allowedQty').text("(" + totalAllowed + " + " + over_receive_limit + "%) = " + (totalAllowed + result).toFixed(2)).attr("title","Over receive is allowed up to " + over_receive_limit + "%");
			}else{
				$('#allowedQty').text("= " + totalAllowed);
			}
			$('#allowedQtyTotal').val((totalAllowed + result).toFixed(2));
			$('#totalProduction').text((totalProduction != '')? totalProduction : '0.00');
			$("#total_pre_recv_qnt").val((totalProduction != '')? totalProduction : '0.00');
			$('#balance').text(((totalAllowed + result).toFixed(2) - totalProduction).toFixed(2));

			$('#cbo_body_part').val(data[0]);
			$('#txt_fabric_description').val(data[1]);
			$('#txt_gsm').val(data[2]);
			$('#txt_width').val(data[3]);
			$('#fabric_desc_id').val(data[4]);

			$('#cbo_body_part').attr('disabled','disabled');
			$('#txt_fabric_description').attr('disabled','disabled');
			$('#txt_gsm').attr('disabled','disabled');
			$('#txt_width').attr('disabled','disabled');
			$('#fabric_desc_id').attr('disabled','disabled');
		}
		else
		{
			var data=data.split("**");

			$('#txt_old_gsm').val(data[2]);
			$('#txt_old_dia').val(data[3]);

			if(recieve_basis==1)
			{
				var recv_date = $('#txt_receive_date').val();
				if(recv_date=="")
				{
					alert("Please Select Receive Date");
					$('#txt_receive_date').focus();
					return;
				}

				if(data[6]==1)
				{
					var rate=data[5];
				}
				else
				{
					var currency_id=2;
					var response=return_global_ajax_value( currency_id+"**"+recv_date, 'check_conversion_rate', '', 'requires/grey_fabric_receive_controller');
					var rate=data[5]*response;
				}
			}
			else
			{
				$('#cbo_body_part').val(data[0]);
				var rate=data[5];
			}

			if(recieve_basis==11)
			{
				$('#txt_yarn_lot').attr('readonly', 'readonly');
				$('#txt_yarn_lot').attr('placeholder', 'Browse');
				$('#txt_yarn_lot').attr('onclick', 'proces_costing_popup()');
				if(!data[18]){
					var totalAllowed = parseFloat(data[7]);
					var totalProduction = data[8];
					if(data[9] != ''){
						var over_receive_limit = parseFloat(data[9]); // Percentage
					}else{
						var over_receive_limit = 0;
					}
				}else{
					var totalAllowed = parseFloat(data[18]);
					var totalProduction = data[19];
					if(data[20] != ''){
						var over_receive_limit = parseFloat(data[20]); // Percentage
					}else{
						var over_receive_limit = 0;
					}
				}
				var result = parseFloat((over_receive_limit / 100) * totalAllowed);
				if(over_receive_limit != ""){
					$('#allowedQty').text("(" + totalAllowed + " + " + over_receive_limit + "%) = " + (totalAllowed + result).toFixed(2)).attr("title","Over receive is allowed up to " + over_receive_limit + "%");
				}else{
					$('#allowedQty').text("= " + totalAllowed);
				}
				$('#allowedQtyTotal').val((totalAllowed + result).toFixed(2));
				$('#totalProduction').text((totalProduction != '')? totalProduction : '0.00');
				$("#total_pre_recv_qnt").val((totalProduction != '')? totalProduction : '0.00');
				$('#balance').text(((totalAllowed + result).toFixed(2) - totalProduction).toFixed(2));
			}
			else
			{
				$('#txt_yarn_lot').removeAttr('readonly');
				$('#txt_yarn_lot').removeAttr('placeholder');
				$('#txt_yarn_lot').removeAttr('onclick');
				$('#txt_rate').val(rate);
			}

			$('#txt_fabric_description').val(data[1]);
			$('#txt_gsm').val(data[2]);
			$('#txt_width').val(data[3]);
			$('#fabric_desc_id').val(data[4]);
			if(data[17]){$('#hidden_program_no').val(data[17]);}
			else{$('#hidden_program_no').val(0);}
			//new development 10-11-2016
			if(data[7] !="")
			{
				//alert(data[7]);
				$('#hidden_prog_id').val(data[7]);
				//$('#txt_yarn_lot').val(data[8]);
				//$('#show_textcbo_yarn_count').val(data[9]);

				//$('#txt_stitch_length').val(data[10]);
				//$('#txt_brand').val(data[11]);
				//$('#txt_color').val(data[12]);
				$('#txt_stitch_length').val(data[13]);
				//alert($('#txt_color').val(data[14]));
				//$('#txt_color').val(data[14]);
				//$('#color_id').val(data[15]);
				$('#color_id').val(data[15]);
				$('#cbo_color_range').val(data[16]);

			}


		}
	}

	function openmypage_fabricDescription()
	{
		var garments_nature = $('#garments_nature').val();
		var title = 'Fabric Description Info';
		var page_link = 'requires/grey_fabric_receive_controller.php?action=fabricDescription_popup&garments_nature='+garments_nature;

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=370px,center=1,resize=1,scrolling=0','../');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theemail=this.contentDoc.getElementById("hidden_desc_id").value;	 //Access form field with id="emailfield"
			var theename=this.contentDoc.getElementById("hidden_desc_no").value; //Access form field with id="emailfield"
			var theegsm=this.contentDoc.getElementById("hidden_gsm").value; //Access form field with id="emailfield"

			$('#txt_fabric_description').val(theename);
			$('#fabric_desc_id').val(theemail);
			$('#txt_gsm').val(theegsm);
		}
	}

	function openmypage_po()
	{
		var receive_basis=$('#cbo_receive_basis').val();
		var booking_no=$('#txt_booking_no').val();
		var cbo_company_id = $('#cbo_company_id').val();
		var dtls_id = $('#update_dtls_id').val();
		var roll_maintained = $('#roll_maintained').val();
		var barcode_generation = $('#barcode_generation').val();
		var save_data = $('#save_data').val();
		var save_data2 = $('#save_data2').val();
		var all_po_id = $('#all_po_id').val();
		var txt_receive_qnty = $('#txt_receive_qnty').val();
		var txt_reject_fabric_recv_qnty = $('#txt_reject_fabric_recv_qnty').val();
		var distribution_method = $('#distribution_method_id').val();
		var fabric_description=$('#txt_fabric_description').val();

		var fabric_desc_id=$('#fabric_desc_id').val();
		var cbo_body_part=$('#cbo_body_part').val();
		var txt_gsm=$('#txt_gsm').val();
		var txt_width=$('#txt_width').val();
		var txt_deleted_id=$('#txt_deleted_id').val();
		var process_costing_maintain = $('#process_costing_maintain').val();
		var booking_without_order = $('#booking_without_order').val();


		if (form_validation('cbo_company_id*cbo_receive_basis','Company*Receive Basis')==false)
		{
			return;
		}

		var po_popup = "po_popup";
		if((receive_basis==2 || receive_basis==11) && booking_no=="")
		{
			alert("Please Select Booking No.");
			$('#txt_booking_no').focus();
			return false;
		}
		else if(receive_basis==9 && fabric_description=="")
		{
			alert("Please Select Fabric Description.");
			$('#txt_fabric_description').focus();
			return false;
		}
		else if(receive_basis==14)
		{
			if (form_validation('txt_fabric_description*txt_booking_no','Fabric Description*Sales Order')==false)
			{
				return;
			}
			var po_popup = "sales_order_popup";
		}

		if(roll_maintained==1)
		{
			popup_width='900px';
		}
		else
		{
			popup_width='855px';
		}

		var title = 'PO Info';
		var page_link = 'requires/grey_fabric_receive_controller.php?receive_basis='+receive_basis+'&cbo_company_id='+cbo_company_id+'&booking_no='+booking_no+'&booking_without_order='+booking_without_order+'&dtls_id='+dtls_id+'&all_po_id='+all_po_id+'&roll_maintained='+roll_maintained+'&barcode_generation='+barcode_generation+'&save_data='+save_data+'&save_data2='+save_data2+'&txt_receive_qnty='+txt_receive_qnty+'&txt_reject_fabric_recv_qnty='+txt_reject_fabric_recv_qnty+'&prev_distribution_method='+distribution_method+'&cbo_body_part='+cbo_body_part+'&txt_gsm='+txt_gsm+'&txt_width='+txt_width+'&fabric_desc_id='+fabric_desc_id+'&txt_deleted_id='+txt_deleted_id+'&action='+po_popup;

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=430px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var save_string=this.contentDoc.getElementById("save_string").value;	 //Access form field with id="emailfield"
			var save_string2=this.contentDoc.getElementById("save_string2").value;	 //Access form field with id="emailfield"
			var tot_grey_qnty=this.contentDoc.getElementById("tot_grey_qnty").value; //Access form field with id="emailfield"
			var tot_reject_qnty=this.contentDoc.getElementById("tot_reject_qnty").value; //Access form field with id="emailfield"
			var number_of_roll=this.contentDoc.getElementById("number_of_roll").value; //Access form field with id="emailfield"
			var all_po_id=this.contentDoc.getElementById("all_po_id").value; //Access form field with id="emailfield"
			var distribution_method=this.contentDoc.getElementById("distribution_method").value;
			var hide_deleted_id=this.contentDoc.getElementById("hide_deleted_id").value;
			$('#save_data').val(save_string);
			$('#save_data2').val(save_string2);
			$('#txt_receive_qnty').val(tot_grey_qnty);
			$('#txt_reject_fabric_recv_qnty').val(tot_reject_qnty);
			if(roll_maintained==1)
			{
				$('#txt_roll_no').val(number_of_roll);
				$('#txt_deleted_id').val(hide_deleted_id);
			}
			else
			{
				$('#txt_deleted_id').val('');
			}
			$('#all_po_id').val(all_po_id);
			$('#distribution_method_id').val(distribution_method);
			//if(process_costing_maintain==1) // crm 27237 confirm by tofael vai
			//{
				var amount=$('#txt_rate').val()*tot_grey_qnty;
				$('#txt_amount').val(number_format(amount,2,'.' , ""));
			//}
		}
	}

	function fnc_grey_fabric_receive(operation)
	{

		var receive_vasis=parseInt($('#cbo_receive_basis').val());
		if(operation!=4)
		{
			if($("#is_posted_accout").val()==1)
			{
				alert("Already Posted In Accounting. Save Update Delete Restricted.");
				return;
			}
		}

		if(operation==4)
		{
			if($('#update_id').val()=="")
			{
				alert("Please Save Data First.");
				return;
			}
			else
			{
				var report_title=$( "div.form_caption" ).html();
				print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_booking_no').val()+'*'+$('#cbo_receive_basis').val()+'*'+$('#cbo_location').val(), "grey_fabric_receive_print", "requires/grey_fabric_receive_controller" )
				return;
			}
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if(operation==2)
			{
				show_msg('13');
				return;
			}



			var knitting_source=parseInt($('#cbo_knitting_source').val());

			if($("#process_costing_maintain").val()==1 && receive_vasis==11)
			{
				if( form_validation('txt_yarn_lot','Yarn Lot')==false )
				{
					return;
				}

			}

			if(receive_vasis==1 || receive_vasis==2 || receive_vasis==4 || receive_vasis==11 || receive_vasis==14)
			{
				if(receive_vasis==14)
				{
					if( form_validation('cbo_company_id*cbo_receive_basis*txt_receive_date*txt_receive_chal_no*cbo_store_name*cbo_location','Company*Receive Basis*Production Date*Challan*Store Name*Location')==false )
					{
						return;
					}
				}
				else
				{
					if( form_validation('cbo_company_id*cbo_receive_basis*txt_receive_date*txt_receive_chal_no*cbo_store_name*cbo_knitting_source*cbo_knitting_company*cbo_location','Company*Receive Basis*Production Date*Challan*Store Name*Knitting Source*Knitting Com*Location')==false )
					{
						return;
					}
				}
			}
			else
			{
				if(receive_vasis==9 && knitting_source==3)
				{
					if( form_validation('cbo_company_id*cbo_receive_basis*txt_receive_date*txt_receive_chal_no*cbo_store_name*cbo_knitting_source*cbo_knitting_company*cbo_location','Company*Receive Basis*Production Date*Challan*Store Name*Knitting Source*Knitting Com*Location')==false )
					{
						return;
					}
				}
				else
				{
					if( form_validation('cbo_company_id*cbo_receive_basis*txt_receive_date*cbo_store_name*cbo_knitting_source*cbo_knitting_company*cbo_location','Company*Receive Basis*Production Date*Challan*Store Name*Knitting Source*Knitting Com*Location')==false )
					{
						return;
					}
				}
			}


			if(knitting_source==1)
			{
				if( form_validation('cbo_location','Location')==false )
				{
					return;
				}
			}

			if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][22]); ?>')
			{
				if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][22]); ?>','<? echo implode('*',$_SESSION['logic_erp']['mandatory_message'][22]); ?>')==false) {return;}
			}

			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_receive_date').val(), current_date)==false)
			{
				alert("Recevied Date Can not Be Greater Than Today");
				return;
			}

			if($('#cbo_receive_basis').val()==1 && $('#txt_booking_no').val()=="")
			{
				alert("Please Select Booking No");
				$('#txt_booking_no').focus();
				return;
			}

			if($('#txt_yarn_issue_challan_no').val()=="")
			{
				var r=confirm("Press \"OK\" to Insert Yarn Issue Challan No\nPress \"Cancel\" to Insert Yarn Issue Challan No Blank");
				if (r==true)
				{
					$('#txt_yarn_issue_challan_no').focus();
					return;
				}
			}

			if( form_validation('cbo_body_part*txt_fabric_description*txt_gsm*txt_width*txt_stitch_length*txt_receive_qnty*cbo_color_range','Body Part*Fabric Description*GSM*Dia / Width*Stitch Length*Grey Receive Qnty*Color Range')==false )
			{
				return;
			}

			var txt_receive_qnty = $('#txt_receive_qnty').val();
			var total_pre_recv_qnt = $('#total_pre_recv_qnt').val();
			var allowedQty = $('#allowedQtyTotal').val();
            disable_enable_fields( 'cbo_receive_basis', 1, "", "" );

			if(receive_vasis==11 || receive_vasis==14)
			{
				if(operation==0)
				{
					var total_recv=Number(parseFloat(txt_receive_qnty) + parseFloat(total_pre_recv_qnt));
				}
				else{
					var total_recv=Number(parseFloat(txt_receive_qnty));
				}
				allowedQty=Number(parseFloat(allowedQty));
				//if(allowedQty<=total_recv )
				if(allowedQty < total_recv )
				{
					alert("Over Grey Receive is not Allowed.");
					$('#txt_receive_qnty').focus().css("background-image","-moz-linear-gradient(center bottom , rgb(254, 151, 174) 0%, rgb(255, 255, 255) 10%, rgb(254, 151, 174) 96%)");
					return;
				}
				else
				{
					if(receive_vasis==11 )
					{
						var is_sample=document.getElementById('booking_without_order').value;
						var datas=document.getElementById('txt_booking_no').value+'**'+is_sample+'**11**'+document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_body_part').value+'**'+document.getElementById('fabric_desc_id').value+'**'+document.getElementById('txt_recieved_id').value+'**'+txt_receive_qnty;
						var check_result = trim(return_global_ajax_value(datas, 'quantity_check_for_service_booking', '', 'requires/grey_fabric_receive_controller'));
					}
					else
					{
						var datas=document.getElementById('txt_booking_no').value+'**14**'+document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_body_part').value+'**'+document.getElementById('fabric_desc_id').value+'**'+document.getElementById('txt_gsm').value+'**'+document.getElementById('txt_width').value+'**'+document.getElementById('update_dtls_id').value+'**'+txt_receive_qnty;
						var check_result = trim(return_global_ajax_value(datas, 'quantity_check_for_sales_order', '', 'requires/grey_fabric_receive_controller'));
					}


					console.log(check_result);
					res=check_result.split("**");
					if(res[0]==0 || res[0]=='0')
					{
						alert("Over Grey Receive is not Allowed.");
						$('#txt_receive_qnty').focus().css("background-image","-moz-linear-gradient(center bottom , rgb(254, 151, 174) 0%, rgb(255, 255, 255) 10%, rgb(254, 151, 174) 96%)");
						return;
					}
				}
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



			//return;

			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_recieved_id*cbo_company_id*cbo_receive_basis*txt_receive_date*txt_receive_chal_no*txt_booking_no_id*txt_booking_no*cbo_store_name*cbo_knitting_source*cbo_knitting_company*cbo_location*txt_yarn_issue_challan_no*cbo_buyer_name*cbo_body_part*txt_fabric_description*fabric_desc_id*txt_gsm*txt_width*txt_old_gsm*txt_old_dia*cbo_machine_name*txt_roll_no*txt_boe_mushak_challan_no*txt_boe_mushak_challan_date*txt_remarks*txt_receive_qnty*cbo_room*txt_reject_fabric_recv_qnty*txt_shift_name*cbo_floor*txt_rack*cbo_uom*txt_shelf*txt_yarn_lot*cbo_bin*cbo_yarn_count*txt_brand*cbo_color_range*txt_color*color_id*txt_stitch_length*txt_machine_dia*txt_machine_gg*update_id*all_po_id*update_dtls_id*update_trans_id*save_data*save_data2*previous_prod_id*hidden_receive_qnty*hidden_receive_amnt*roll_maintained*booking_without_order*garments_nature*product_id*txt_deleted_id*txt_amount*txt_rate*process_costing_maintain*process_string*knitting_charge_string*grey_prod_dtls_id*hidden_program_no',"../../");
			freeze_window(operation);

			http.open("POST","requires/grey_fabric_receive_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange =fnc_grey_fabric_receive_Reply_info;
		}
	}

	function fnc_grey_fabric_receive_Reply_info()
	{
		if(http.readyState == 4)
		{
			//alert(http.responseText);
			//release_freezing();	return;
			var reponse=trim(http.responseText).split('**');
			if((reponse[0]==0 || reponse[0]==1))
			{
				show_msg(reponse[0]);
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_recieved_id').value = reponse[2];
				$('#cbo_company_id').attr('disabled','disabled');
				$('#cbo_knitting_source').attr('disabled','disabled');
				$('#cbo_knitting_company').attr('disabled','disabled');
				$('#cbo_location').attr('disabled','disabled');
				$('#cbo_receive_basis').attr('disabled','disabled');
				$('#txt_booking_no').attr('disabled','disabled');
				show_list_view(reponse[1],'show_grey_prod_listview','list_container_knitting','requires/grey_fabric_receive_controller','');

				reset_form('greyreceive_1','roll_details_list_view','','','','update_id*txt_recieved_id*cbo_company_id*cbo_receive_basis*txt_receive_date*txt_receive_chal_no*txt_booking_no*txt_booking_no_id*cbo_buyer_name*cbo_store_name*cbo_knitting_source*cbo_knitting_company*cbo_location*txt_yarn_issue_challan_no*txt_job_no*txt_remarks*txt_boe_mushak_challan_no*txt_boe_mushak_challan_date*roll_maintained*barcode_generation*booking_without_order*process_costing_maintain*store_update_upto');

				$('#allowedQty').text("= 0.00");
				$('#allowedQtyTotal').val("");
				$('#totalProduction').text("0.00");
				$('#balance').text("0.00");

				set_button_status(0, permission, 'fnc_grey_fabric_receive',1,1);
				release_freezing();
			}
			else if(reponse[0]==30)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
            else if(reponse[0]==20)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			else
			{
				show_msg(reponse[0]);
				release_freezing();
			}
		}
	}

	function grey_receive_popup()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var garments_nature = $('#garments_nature').val();

		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{
			var page_link='requires/grey_fabric_receive_controller.php?cbo_company_id='+cbo_company_id+'&garments_nature='+garments_nature+'&action=grey_receive_popup_search';
			var title='Grey Receive Form';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=990px,height=390px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var grey_recv_id=this.contentDoc.getElementById("hidden_recv_id").value;
				var posted_in_account=this.contentDoc.getElementById("hidden_posted_account").value;
				if(trim(grey_recv_id)!="")
				{
					freeze_window(5);
					reset_form('greyreceive_1','list_container_knitting*list_fabric_desc_container','','','','roll_maintained*barcode_generation*process_costing_maintain*store_update_upto');
					var process_costing_maintain = $('#process_costing_maintain').val();
					get_php_form_data(grey_recv_id+"**"+process_costing_maintain, "populate_data_from_grey_recv", "requires/grey_fabric_receive_controller" );

					var booking_pi_production_no = $('#txt_booking_no').val();
					var booking_pi_production_id = $('#txt_booking_no_id').val();
					var booking_without_order = $('#booking_without_order').val();
					var cbo_receive_basis = $('#cbo_receive_basis').val();

					if(cbo_receive_basis==2 || cbo_receive_basis==11)
					{
						show_list_view(booking_pi_production_no+"**"+booking_without_order+"**"+cbo_receive_basis+"**"+cbo_company_id,'show_fabric_desc_listview','list_fabric_desc_container','requires/grey_fabric_receive_controller','');
					}
					else if(cbo_receive_basis==14)
					{
						show_list_view(booking_pi_production_no+"**"+booking_without_order+"**"+cbo_receive_basis+"**"+cbo_company_id,'show_fabric_desc_listview_sales_order','list_fabric_desc_container','requires/grey_fabric_receive_controller','');
					}
					else if(cbo_receive_basis==1 || cbo_receive_basis==9)
					{
						show_list_view(booking_pi_production_id+"**"+booking_without_order+"**"+cbo_receive_basis+"**"+cbo_company_id,'show_fabric_desc_listview','list_fabric_desc_container','requires/grey_fabric_receive_controller','');
					}

					show_list_view(grey_recv_id,'show_grey_prod_listview','list_container_knitting','requires/grey_fabric_receive_controller','');
					if(cbo_receive_basis==11 || cbo_receive_basis==14)
					{
						if(cbo_receive_basis==11)
						{
							$('#txt_yarn_lot').attr('readonly', 'readonly');
							$('#txt_yarn_lot').attr('placeholder', 'Browse');
							$('#txt_yarn_lot').attr('onclick', 'proces_costing_popup()');
						}
						else
						{
							$('#txt_yarn_lot').removeAttr('readonly');
							$('#txt_yarn_lot').removeAttr('placeholder');
							$('#txt_yarn_lot').removeAttr('onclick');
						}
						$('#qty_statistic').show();
						//$('#hidden_knitting_charge').val(rate);
						$('#allowedQty').text("= 0.00");
						$('#allowedQtyTotal').val("");
						$('#totalProduction').text("0.00");
						$('#balance').text("0.00");
					}
					else
					{
						$('#txt_yarn_lot').removeAttr('readonly');
						$('#txt_yarn_lot').removeAttr('placeholder');
						$('#txt_yarn_lot').removeAttr('onclick');
						$('#qty_statistic').hide();
						//$('#txt_rate').val(rate);
					}
					set_button_status(0, permission, 'fnc_grey_fabric_receive',1,1);
					release_freezing();
				}

				$("#is_posted_accout").val(posted_in_account);
				if(posted_in_account==1) document.getElementById("accounting_posted_status").innerHTML="Already Posted In Accounting.";
				else 					 document.getElementById("accounting_posted_status").innerHTML="";

			}
		}
	}

	function put_data_dtls_part(id,type,page_path)
	{
		//get_php_form_data(id+"**"+$('#roll_maintained').val()+"**"+$('#garments_nature').val(), type, page_path );
		var company_id = $('#cbo_company_id').val();
		var roll_maintained=$('#roll_maintained').val();
		var barcode_generation = $('#barcode_generation').val();
		var booking_without_order = $('#booking_without_order').val();

		get_php_form_data(id+"**"+roll_maintained+"**"+$('#garments_nature').val()+"**"+$('#cbo_receive_basis').val()+"**"+company_id, type, page_path );
		if(roll_maintained==1)
		{
			show_list_view("'"+id+"**"+barcode_generation+"**"+booking_without_order+"'",'show_roll_listview','roll_details_list_view','requires/grey_fabric_receive_controller','');
		}
		else
		{
			$('#roll_details_list_view').html('');
		}
	}

	function set_auto_complete()
	{
		var receive_basis=$('#cbo_receive_basis').val();
		if(receive_basis==2 || receive_basis==11)
		{
			var booking_id = $('#txt_booking_no_id').val();
			var booking_without_order = $('#booking_without_order').val();
			get_php_form_data(booking_id+"**"+booking_without_order+"**"+receive_basis, 'load_color', 'requires/grey_fabric_receive_controller');
		}
		else
		{
			$("#txt_color").autocomplete({
				source: str_color
			});
		}
	}

	function openmypage_color()
	{
		var recieve_basis 			= $('#cbo_receive_basis').val();
		var booking_id 				= $('#txt_booking_no_id').val();
		var booking_without_order 	= $('#booking_without_order').val();
		var cbo_body_part 			= $('#cbo_body_part').val();
		var fabric_desc_id 			= $('#fabric_desc_id').val();
		var txt_gsm 				= $('#txt_gsm').val();
		var txt_width 				= $('#txt_width').val();
		//var booking_no = $('#txt_booking_no').val();
		if(recieve_basis==9)
		{
			alert("Not Allowed");
			return;
		}

		if(recieve_basis==1 || recieve_basis==2 || recieve_basis==9 || recieve_basis==14)
		{
			if (form_validation('txt_booking_no','WO/PI/Prod./Sales Order')==false)
			{
				return;
			}
		}

		if(recieve_basis==14)
		{
			if (form_validation('cbo_body_part*fabric_desc_id*txt_gsm','Body Part*Fabric Description*Gsm')==false)
			{
				return;
			}
		}

		var color_id = $('#color_id').val();
		var program_no = $('#hidden_prog_id').val();
		var title = 'Color Info';
		var page_link='requires/grey_fabric_receive_controller.php?recieve_basis='+recieve_basis+'&color_id='+color_id+'&booking_id='+booking_id+'&booking_without_order='+booking_without_order+'&program_no='+program_no+'&cbo_body_part='+cbo_body_part+'&fabric_desc_id='+fabric_desc_id+'&txt_gsm='+txt_gsm+'&txt_width='+txt_width+'&action=color_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=350px,height=350px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theemail=this.contentDoc.getElementById("hidden_color_id").value;	 //Access form field with id="emailfield"
			var theename=this.contentDoc.getElementById("hidden_color_no").value; //Access form field with id="emailfield"

			$('#txt_color').val(theename);
			$('#color_id').val(theemail);
		}
	}

	function issue_challan_no()
	{
		var cbo_company_id = $('#cbo_company_id').val();

		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{
			var page_link='requires/grey_fabric_receive_controller.php?cbo_company_id='+cbo_company_id+'&action=issue_challan_no_popup';
			var title='Issue Challan Info';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=390px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var issue_challan=this.contentDoc.getElementById("issue_challan").value;
				if(trim(issue_challan)!="")
				{
					freeze_window(5);
					$('#txt_yarn_issue_challan_no').val(issue_challan);
					release_freezing();
				}
			}
		}
	}

	function fnc_check_issue(issue_num)
	{
		if(issue_num!="")
		{
			var issue_result = trim(return_global_ajax_value(issue_num, 'issue_num_check', '', 'requires/grey_fabric_receive_controller'));
			if(issue_result=="")
			{
				alert("Challan Number Not Found");
				$('#txt_yarn_issue_challan_no').val("");
			}
		}
	}

	function load_receive_basis()
	{
		var company_id=$("#cbo_company_id").val();
		var independent_control_arr = JSON.parse('<? echo json_encode($independent_control_arr); ?>');
        $("#cbo_receive_basis").val(0);
        $("#cbo_receive_basis option[value='4']").show();
        if(independent_control_arr[company_id]==1)
        {
            $("#cbo_receive_basis option[value='4']").hide();
        }

		//var theSelect = document.getElementById('cbo_receive_basis');
		//var lastValue = theSelect.options[theSelect.options.length - 2].value;

		var roll_maintained=$('#roll_maintained').val();
		if(roll_maintained==1)
		{
			$("#cbo_receive_basis option[value='9']").remove();
		}
		else
		{
			$("#cbo_receive_basis option[value='9']").remove();
			$("#cbo_receive_basis option[value='11']").remove();
			$("#cbo_receive_basis").append('<option value="9">Production</option>');
			$("#cbo_receive_basis").append('<option value="11">Service Booking Based</option>');

			/*if($('#cbo_receive_basis option:last').val()!=9)
			{
				$("#cbo_receive_basis").append('<option value="9">Production</option>');
			}*/
		}
	}

	function check_all_report()
	{
		$("input[name=chkBundle]").each(function(index, element)
		{
			if( $('#check_all').prop('checked')==true)
				$(this).attr('checked','true');
			else
				$(this).removeAttr('checked');
		});
	}

	function fnc_send_printer_text()
	{
		var dtls_id=$('#update_dtls_id').val();
		if(dtls_id=="")
		{
			alert("Save First");
			return;
		}
		var data="";
		var error=1;
		$("input[name=chkBundle]").each(function(index, element) {
			if( $(this).prop('checked')==true)
			{
				error=0;
				var idd=$(this).attr('id').split("_");
				var roll_id=$('#txtRollTableId_'+idd[1] ).val();
				if(roll_id!="")
				{
					if(data=="") data=$('#txtRollTableId_'+idd[1] ).val(); else data=data+","+$('#txtRollTableId_'+idd[1] ).val();
				}
				else
				{
					$(this).prop('checked',false);
				}
			}
		});

		if( error==1 )
		{
			alert('No data selected');
			return;
		}

		data=data+"***"+dtls_id;
		var url=return_ajax_request_value(data, "report_barcode_text_file", "requires/grey_fabric_receive_controller");
		window.open("requires/"+trim(url)+".zip","##");
	}

	function fnc_barcode_generation()
	{
		var dtls_id=$('#update_dtls_id').val();
		if(dtls_id=="")
		{
			alert("Save First");
			return;
		}
		var data="";
		var error=1;
		$("input[name=chkBundle]").each(function(index, element) {
			if( $(this).prop('checked')==true)
			{
				error=0;
				var idd=$(this).attr('id').split("_");
				var roll_id=$('#txtRollTableId_'+idd[1] ).val();
				if(roll_id!="")
				{
					if(data=="") data=$('#txtRollTableId_'+idd[1] ).val(); else data=data+","+$('#txtRollTableId_'+idd[1] ).val();
				}
				else
				{
					$(this).prop('checked',false);
				}
			}
		});

		if( error==1 )
		{
			alert('No data selected');
			return;
		}

		data=data+"***"+dtls_id;
		window.open("requires/grey_fabric_receive_controller.php?data=" + data+'&action=report_barcode_generation', true );
	}

	function load_location()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_knitting_source = $('#cbo_knitting_source').val();
		var cbo_knitting_company = $('#cbo_knitting_company').val();
		/*if(cbo_knitting_source==1)
		{
			load_drop_down( 'requires/grey_fabric_receive_controller',cbo_knitting_company+'_'+cbo_knitting_source, 'load_drop_down_location', 'location_td' );
		}
		else
		{
			load_drop_down( 'requires/grey_fabric_receive_controller',cbo_company_id+'_'+cbo_knitting_source, 'load_drop_down_location', 'location_td' );
		}*/
		load_drop_down( 'requires/grey_fabric_receive_controller',cbo_company_id+'_'+cbo_knitting_source, 'load_drop_down_location', 'location_td' ); // only company wise location, store, floor, room, rack, shelf load confirm by Rasel bhai
	}

	function load_floor()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_knitting_source = $('#cbo_knitting_source').val();
		var cbo_knitting_company = $('#cbo_knitting_company').val();
		var cbo_location = $('#cbo_location').val();
		if(cbo_knitting_source==1)
		{
			load_drop_down( 'requires/grey_fabric_receive_controller',cbo_knitting_company+'_'+cbo_location, 'load_drop_down_floor', 'floor_td' );
		}
		else
		{
			load_drop_down( 'requires/grey_fabric_receive_controller',cbo_company_id+'_'+cbo_location, 'load_drop_down_floor', 'floor_td' );
		}
	}

	function load_machine()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_knitting_source = $('#cbo_knitting_source').val();
		var cbo_knitting_company = $('#cbo_knitting_company').val();
		var floor_td = $('#floor_td').val();
		if(cbo_knitting_source==1)
		{
			load_drop_down( 'requires/grey_fabric_receive_controller',cbo_knitting_company+'_'+floor_td, 'load_drop_machine', 'machine_td' );
		}
		else
		{
			load_drop_down( 'requires/grey_fabric_receive_controller',cbo_company_id+'_'+floor_td, 'load_drop_machine', 'machine_td' );
		}
	}

	function calculate_amount()
	{
		var receive_qty = $('#txt_receive_qnty').val()*1;
		var rate = $('#txt_rate').val()*1;
		var amount=receive_qty*rate;
		$('#txt_amount').val(number_format(amount,2,'.' , ""));
	}


	function proces_costing_popup()
	{
		var txt_job_no= $('#txt_job_no').val();
		var recieve_basis = $('#cbo_receive_basis').val();
		var booking_id = $('#txt_booking_no_id').val();
		var fabric_description_id=$('#fabric_desc_id').val();
		var txt_receive_qnty=$('#txt_receive_qnty').val();
		var txt_receive_date=$('#txt_receive_date').val();
		var update_dtls_id=$('#update_dtls_id').val();
		var update_id=$('#update_id').val();
		var kitting_charge=$("#hidden_knitting_charge").val();
		var booking_without_order = $('#booking_without_order').val();
		var save_data =$('#save_data').val();
		if (form_validation('txt_booking_no*txt_receive_qnty*txt_receive_date*txt_fabric_description','WO/PI/Production*Grey Receive Qnty*Receive Date*Fabric Description')==false)
		{
			return;
		}

		var title = 'Yarn Cost Info';
		var page_link='requires/grey_fabric_receive_controller.php?recieve_basis='+recieve_basis+'&booking_id='+booking_id+'&booking_without_order='+booking_without_order+'&fabric_description_id='+fabric_description_id+'&txt_receive_qnty='+txt_receive_qnty+'&txt_job_no='+txt_job_no+'&txt_receive_date='+txt_receive_date+'&update_dtls_id='+update_dtls_id+'&update_id='+update_id+'&kitting_charge='+kitting_charge+'&save_data='+save_data+'&action=yarn_lot_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=880px,height=350px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theemail=this.contentDoc.getElementById("hidden_process_string").value;	 //Access form field with id="emailfield"
			var theename=this.contentDoc.getElementById("hidden_knitting_rate").value; //Access form field with id="emailfield"
			$('#knitting_charge_string').val(theename);
			$('#process_string').val(theemail);
			if(theename!="")
			{
				var popup_value=theename.split("*");
				$("#cbo_yarn_count").val(popup_value[3]);
				set_multiselect('cbo_yarn_count','0','1',popup_value[3],'0');
				$("#txt_yarn_lot").val(popup_value[2]);
				$("#txt_brand").val(popup_value[4]);
				rate	=	(popup_value[0]*1)+(popup_value[1]*1);
				rate 	= 	number_format(rate,2,'.' , "");
				amount=	($('#txt_receive_qnty').val()*1)*rate;
				$('#txt_rate').val(number_format(rate,2,'.' , ""));
				$('#txt_amount').val(number_format(amount,2,'.' , ""));
				$('#txt_rate').attr("title","Yarn Rate/Kg : "+popup_value[1]+"; knitting Charge/Kg :"+popup_value[0]);
			}
		}
	}

	function fnc_generate_bill()
	{
		freeze_window(0);
		if($('#txt_recieved_id').val()=='')
		{
			alert("Please Choose a Received ID for Bill Process.");
			$('#txt_recieved_id').focus();
			release_freezing();
			return;
		}
		else
		{
			var bill_response=return_global_ajax_value($('#update_id').val(), 'bill_generate', '', 'requires/grey_fabric_receive_controller');
			var response=bill_response.split("**");
			//alert(bill_response);

			if(response[0]==0 || response[0]==30)
			{
				alert(response[1]);
				release_freezing();
				return;
			}

			if(response[0]==55)
			{
				alert("Receive Data Not Found.");
				release_freezing();
				return;
			}
			if(response[0]==56)
			{
				alert("Rate Not Found in Lib.->Yarn Count Determination.");
				release_freezing();
				return;
			}

			release_freezing();
		}
	}

</script>
<body onLoad="set_hotkey()">
	<div style="width:100%;">
		<? echo load_freeze_divs ("../../",$permission); ?>
		<form name="greyreceive_1" id="greyreceive_1">
			<div style="width:950px; float:left;" align="center">
				<fieldset style="width:950px">
					<legend>Knitting Production Entry</legend>
					<fieldset style="width:930px">
						<table cellpadding="0" cellspacing="2" width="100%">
							<tr>
								<td align="right" colspan="3"><strong> Received ID </strong></td>
								<td>
									<input type="hidden" name="update_id" id="update_id" />
									<input type="text" name="txt_recieved_id" id="txt_recieved_id" class="text_boxes" style="width:145px" placeholder="Double Click" onDblClick="grey_receive_popup();" >
								</td>
							</tr>
							<tr>
								<td colspan="6" height="10"></td>
							</tr>
							<tr>
								<td width="110" class="must_entry_caption"> Company </td>
								<td width="150">
									<?
									echo create_drop_down( "cbo_company_id", 151, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3)  $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down( 'requires/grey_fabric_receive_controller', document.getElementById('cbo_receive_basis').value+'_'+this.value, 'load_drop_down_buyer', 'buyer_td_id' );load_drop_down( 'requires/grey_fabric_receive_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/grey_fabric_receive_controller', this.value, 'load_drop_machine', 'machine_td' );get_php_form_data(this.value,'roll_maintained','requires/grey_fabric_receive_controller' ); load_receive_basis();get_php_form_data( this.value, 'company_wise_report_button_setting','requires/grey_fabric_receive_controller' );" );
									//load_drop_down( 'requires/grey_fabric_receive_controller', this.value, 'load_drop_down_floor', 'floor_td' );
									//load_drop_down( 'requires/grey_fabric_receive_controller', this.value+'_'+document.getElementById('garments_nature').value, 'load_drop_down_store', 'store_td' );
									?>
								</td>
								<td width="110" class="must_entry_caption"> Receive Basis </td>
								<td width="150">
									<? // $_SESSION['fabric_nature']==2 is > Lib > Variable Settings > Production > Variable List: Fabric in Roll Level -> Item Category: Grey Fabric (Knit): Fabric in Roll Level: No
									if($_SESSION['fabric_nature']==2) $show_index='1,2,11,4,6,9,14'; else $show_index='1,2,11,4,6,14';
									echo create_drop_down("cbo_receive_basis",152,$receive_basis_arr,"",1,"-- Select --",0,"set_receive_basis();","",$show_index);
									?>
								</td>
								<td width="110" class="must_entry_caption"> Receive Date </td>
								<td width="150">
									<input class="datepicker" type="text" style="width:140px" name="txt_receive_date" id="txt_receive_date" value="<? echo date('d-m-Y'); ?>"/>
								</td>
							</tr>
							<tr>
								<td class="must_entry_caption"> Receive Challan No </td>
								<td>
									<input type="text" name="txt_receive_chal_no" id="txt_receive_chal_no" class="text_boxes" style="width:140px" >
								</td>
								<? if($_SESSION['fabric_nature']==2) $show_label='WO/PI/Prod./Sales'; else $show_label='WO/PI/Sales'; ?>
								<td><? echo $show_label; ?></td>
								<td>
									<input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:140px"  placeholder="Double Click to Search" onDblClick="openmypage_wo_pi_production_popup();" readonly>
									<input type="hidden" name="txt_booking_no_id" id="txt_booking_no_id" class="text_boxes">
									<input type="hidden" name="booking_without_order" id="booking_without_order"/>
								</td>
								<td class="must_entry_caption">Location</td>
								<td id="location_td">
									<?
									echo create_drop_down( "cbo_location", 152, $blank_array,"", 1, "-- Select Location --", 0, "" );
									?>
								</td>

							</tr>
							<tr>
								<td class="must_entry_caption"> Knitting Source </td>
								<td>
									<?
									echo create_drop_down("cbo_knitting_source",150,$knitting_source,"", 1, "-- Select --", 0,"load_drop_down( 'requires/grey_fabric_receive_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_knitting_com','knitting_com');",0,'1,3');
									?>
								</td>
								<td class="must_entry_caption">Knitting Comp.</td>
								<td id="knitting_com">
									<?
									echo create_drop_down( "cbo_knitting_company", 152, $blank_array,"",1, "--Select Knit Company--", 1, "" );
									?>
								</td>
								<td class="must_entry_caption"> Store Name </td>
								<td id="store_td">
									<?
									echo create_drop_down( "cbo_store_name", 152, $blank_array,"",1, "--Select store--", 1, "" );
									?>
								</td>
							</tr>
							<tr>
								<td>Yarn Issue Ch. No</td>
								<td>
									<input type="text" name="txt_yarn_issue_challan_no" id="txt_yarn_issue_challan_no" placeholder="Browse or Write" onDblClick="issue_challan_no();" class="text_boxes" style="width:140px" onBlur="fnc_check_issue(this.value);">
								</td>
								<td>Job No</td>
								<td>
									<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:140px" readonly>
								</td>
								<td>Buyer</td>
								<td id="buyer_td_id">
									<?
									echo create_drop_down( "cbo_buyer_name", 152, $blank_array,"", 1, "-- Select Buyer --", 0, "",1 );
									?>
								</td>
							</tr>
							<tr>
								<td>BOE/Mushak Challan No</td>
								<td>
									<input type="text" name="txt_boe_mushak_challan_no" id="txt_boe_mushak_challan_no" class="text_boxes" style="width:140px">
								</td>
								<td>BOE/Mushak Challan Date</td>
								<td>
									<input type="text" name="txt_boe_mushak_challan_date" id="txt_boe_mushak_challan_date" class="datepicker" style="width:140px">
								</td>
								<td>Remarks </td>
								<td>
									<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:140px">
								</td>
							</tr>
						</table>
					</fieldset>
					<table cellpadding="0" cellspacing="1" width="935" border="0">
						<tr>
							<td width="64%" valign="top">
								<fieldset>
									<legend>New Entry
										<div style="float: right; color: red;" id="qty_statistic">
											<span>Allowed Qty. <span id="allowedQty"> = 0.00</span></span> &nbsp;|&nbsp;
											<input type="hidden" id="allowedQtyTotal" />
											<span>Total Receive = <span id="totalProduction">0.00</span></span> &nbsp;|&nbsp;
											<span>Balance = <span id="balance">0.00</span></span>
										</div>
									</legend>
									<table cellpadding="0" cellspacing="2" width="100%">
										<tr>
											<td class="must_entry_caption">Body Part</td>
											<td>
												<?
												echo create_drop_down( "cbo_body_part", 130, $body_part,"", 1, "-- Select Body Part --", 0, "",1 );
												?>
											</td>
											<td>UOM</td>
											<td>
												<?
												echo create_drop_down( "cbo_uom", 132, $unit_of_measurement,"", 0, "", '12', "",1,12 );
												?>
											</td>
										</tr>
										<tr>
											<td class="must_entry_caption">Fabric Description </td>
											<td colspan="3">
												<input type="text" name="txt_fabric_description" id="txt_fabric_description" class="text_boxes" style="width:400px" onDblClick="openmypage_fabricDescription()" placeholder="Double Click To Search" disabled="disabled" readonly/>
												<input type="hidden" name="fabric_desc_id" id="fabric_desc_id" class="text_boxes">
											</td>
										</tr>
										<tr>
											<td class="must_entry_caption">GSM</td>
											<td>
												<input type="text" name="txt_gsm" id="txt_gsm" class="text_boxes_numeric" style="width:120px;"  />
											</td>
											<td>Old GSM</td>
											<td>
												<input type="text" name="txt_old_gsm" id="txt_old_gsm" class="text_boxes" style="width:120px;" disabled="disabled" readonly/>
											</td>
										</tr>
										<tr>
											<td class="must_entry_caption">Dia / Width</td>
											<td>
												<input type="text" name="txt_width" id="txt_width" class="text_boxes" style="width:120px;text-align:right;" />
											</td>
											<td>Old Dia</td>
											<td>
												<input type="text" name="txt_old_dia" id="txt_old_dia" class="text_boxes" style="width:120px;" disabled="disabled" readonly />
											</td>
										</tr>
										<tr>
											<td>Yarn Lot</td>
											<td>
												<input type="text" name="txt_yarn_lot" id="txt_yarn_lot" class="text_boxes" style="width:120px" />
											</td>
											<td>Yarn Count</td>
											<td>
												<?
												echo create_drop_down("cbo_yarn_count",132,"select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count","id,yarn_count",0, "-- Select --", $selected, "");
												?>
											</td>
										</tr>
										<tr>
											<td class="must_entry_caption">Stitch Length</td>
											<td>
												<input type="text" name="txt_stitch_length" id="txt_stitch_length" class="text_boxes" style="width:120px;"/>
											</td>
											<td>Brand</td>
											<td>
												<input type="text" name="txt_brand" id="txt_brand" class="text_boxes" style="width:120px" />
											</td>
										</tr>
										<tr>
											<td>M/C Dia</td>
											<td>
												<input type="text" name="txt_machine_dia" id="txt_machine_dia" class="text_boxes_numeric" style="width:120px;"/>
											</td>
											<td>M/C Gauge</td>
											<td>
												<input type="text" name="txt_machine_gg" id="txt_machine_gg" class="text_boxes" style="width:120px;"/>
											</td>
										</tr>
										<tr>
											<td>No of Roll</td>
											<td>
												<input type="text" name="txt_roll_no" id="txt_roll_no" class="text_boxes_numeric" style="width:120px" />
											</td>
											<td>Shift Name</td>
											<td>
												<!--<input type="text" name="txt_shift_name" id="txt_shift_name" class="text_boxes" style="width:120px;"  />-->
												<?
												echo create_drop_down( "txt_shift_name", 132, $shift_name,"", 1, "-- Select Shift --", 0, "",'' );
												?>
											</td>
										</tr>
										<tr>
											<td class="must_entry_caption">Grey Receive Qnty</td>
											<td>
												<input type="text" name="txt_receive_qnty" id="txt_receive_qnty" class="text_boxes_numeric" style="width:120px;" onClick="openmypage_po()" placeholder="Single Click" readonly/>
											</td>
											<td>Machine No.</td>
											<td id="machine_td">
												<? echo create_drop_down( "cbo_machine_name", 132, $blank_array,"", 1, "-- Select Machine --", 0, "",0 ); ?>
											</td>

										</tr>
										<tr>
											<td>Rate</td>
											<td>
												<input type="text" name="txt_rate" id="txt_rate" class="text_boxes_numeric" style="width:120px" readonly />
											</td>
											<td>Floor</td>
											<td id="floor_td">
												<? echo create_drop_down( "cbo_floor", 132,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
										</tr>
										<tr>
											<td>Amount</td>
											<td id="floor_td">
												<input type="text" name="txt_amount" id="txt_amount" class="text_boxes_numeric" style="width:120px" readonly/>
											</td>
											<td>Room</td>
											<td id="room_td">
												<? echo create_drop_down( "cbo_room", 132,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
										</tr>
										<tr>
											<td>Fabric Color</td>
											<td>
												<input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:120px;" placeholder="Browse" onDblClick="openmypage_color();" readonly/>
												<input type="hidden" name="color_id" id="color_id" />
												<input type="hidden" name="hidden_prog_id" id="hidden_prog_id" />
											</td>
											<td>Rack</td>
											<td id="rack_td">
												<? echo create_drop_down( "txt_rack", 132,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
										</tr>
										<tr>
											<td class="must_entry_caption">Color Range</td>
											<td>
												<?
												echo create_drop_down( "cbo_color_range", 132, $color_range,"",1, "-- Select --", 0, "" );
												?>
											</td>
											<td>Shelf</td>
											<td id="shelf_td">
												<? echo create_drop_down( "txt_shelf", 132,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
										</tr>
										<tr>
											<td>Reject Fabric Receive</td>
											<td>
												<input type="text" name="txt_reject_fabric_recv_qnty" id="txt_reject_fabric_recv_qnty" class="text_boxes_numeric" style="width:120px;" readonly />
											</td>
											<td>Bin/Box</td>
											<td id="bin_td">
												<? echo create_drop_down( "cbo_bin", 132,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
										</tr>


									</table>
								</fieldset>
							</td>
							<td width="1%" valign="top"></td>
							<td width="35%" valign="top">
								<div id="roll_details_list_view"></div>
                        <!--<fieldset style="display:none">
                        <legend>Display</legend>
                            <table  cellpadding="0" cellspacing="2" width="100%" >
                                <tr>
                                    <td>&nbsp;</td> <td>&nbsp;</td>
                                </tr>
                                <tr>
                                <tr>
                                    <td>Yarn to Knit Comp</td>
                                    <td>
                                        <input type="text"  class="text_boxes_numeric" name="txt_yern_to_knit_com" id="txt_yern_to_knit_com" style="width:135px" readonly />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Grey Received </td>
                                    <td>
                                        <input type="text"  class="text_boxes_numeric" name="txt_total_grey_recieved" id="txt_total_grey_recieved" style="width:135px" readonly />
                                     </td>
                                </tr>
                                <tr>
                                    <td> Reject Grey Fab. Rceived</td>
                                    <td>
                                        <input  type="text" class="text_boxes_numeric" name="txt_reject_fabric_receive" id="txt_reject_fabric_receive" style="width:135px" readonly/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Yet to Receive</td>
                                    <td>
                                        <input type="text" class="text_boxes_numeric" name="txt_yet_recieved" id="txt_yet_recieved" style="width:135px" readonly />
                                    </td>
                                </tr>
                            </table>
                        </fieldset>-->
                    </td>
                </tr>
                <tr>
                	<td colspan="6">&nbsp;</td>
                </tr>
                <tr>
                	<td colspan="6" align="center" class="button_container">
                		<?
                		echo load_submit_buttons($permission, "fnc_grey_fabric_receive", 0,1,"reset_form('greyreceive_1','list_container_knitting*list_fabric_desc_container*roll_details_list_view*accounting_posted_status','','cbo_receive_basis,0','disable_enable_fields(\'cbo_company_id*cbo_receive_basis*cbo_knitting_source*cbo_knitting_company*cbo_location\');set_receive_basis();')",1);
                		?>
                		<div id="accounting_posted_status" style=" color:red; font-size:24px;" align="left"></div>
                		<input type="hidden" name="is_posted_accout" id="is_posted_accout"/>
                		<input type="hidden" name="save_data" id="save_data" readonly>
                		<input type="hidden" name="save_data2" id="save_data2" readonly>
                		<input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
                		<input type="hidden" name="update_trans_id" id="update_trans_id" readonly>
                		<input type="hidden" name="previous_prod_id" id="previous_prod_id" readonly>
                		<input type="hidden" name="product_id" id="product_id" readonly><!--For Receive Basis Production-->
                		<input type="hidden" name="hidden_receive_qnty" id="hidden_receive_qnty" readonly>
                		<input type="hidden" name="hidden_receive_amnt" id="hidden_receive_amnt" readonly>
                		<input type="hidden" name="all_po_id" id="all_po_id" readonly>
                		<input type="hidden" name="store_update_upto" id="store_update_upto" readonly>
                		<input type="hidden" name="roll_maintained" id="roll_maintained" readonly>
                		<input type="hidden" name="barcode_generation" id="barcode_generation" readonly>
                		<input type="hidden" name="distribution_method_id" id="distribution_method_id" readonly />
                		<input type="hidden" name="txt_deleted_id" id="txt_deleted_id" readonly />
                		<input type="hidden" name="process_costing_maintain" id="process_costing_maintain" readonly>
                		<input type="hidden" name="hidden_knitting_charge" id="hidden_knitting_charge" readonly>
                		<input type="hidden" name="process_string" id="process_string"/>
                		<input type="hidden" name="knitting_charge_string" id="knitting_charge_string"/>
                		<input type="hidden" name="grey_prod_dtls_id" id="grey_prod_dtls_id"/>
                		<input type="hidden" name="hidden_program_no" id="hidden_program_no"/>
                		<input type="hidden" name="txt_allowed_qnty" id="txt_allowed_qnty"/>
                		<input type="hidden" name="total_pre_recv_qnt" id="total_pre_recv_qnt">

						<input type="button" id="Print" name="Print" style="width:100px;display:none;" class="formbutton" value="Print" onClick="fnc_grey_fabric_receive(4);" />
                        <input type="button" id="btnbill" name="btnbill" style="width:100px;display:none;" class="formbutton" value="Bill Generate" onClick="fnc_generate_bill();" />

                	</td>
                </tr>
            </table>
            <div style="width:930px;" id="list_container_knitting"></div>
        </fieldset>
    </div>
    <div style="width:10px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
    <div id="list_fabric_desc_container" style="max-height:500px; width:360px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
</form>
</div>
</body>
<script>
	$(document).ready(function() {
		for (var property in mandatory_field_arr) {
			$("#"+mandatory_field_arr[property]).parent().prev('td').css("color", "blue");
		}
	});
	set_multiselect('cbo_yarn_count','0','0','','0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$("#Print1").hide();
</script>
</html>