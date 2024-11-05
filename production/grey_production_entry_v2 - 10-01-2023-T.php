<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Grey Production Entry
Functionality	:	
JS Functions	:
Created by		:	Fuad Shahriar 
Creation date 	: 	12/05/2013
Updated by 		: 	Md Didarul Alam	[ QC Result ]
Update date		: 	09-09-2018	   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
	require_once('../includes/common.php');
	extract($_REQUEST);
	$_SESSION['page_permission']=$permission;
	$user_id=$_SESSION['logic_erp']['user_id'];
	date_default_timezone_set("Asia/Dhaka");

	//========== user credential start ========
	$user_id = $_SESSION['logic_erp']['user_id'];
	$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
	$company_id = $userCredential[0][csf('company_id')];
	$store_location_id = $userCredential[0][csf('store_location_id')];
	$item_cate_id = $userCredential[0][csf('item_cate_id')];
	$location_id = $userCredential[0][csf('location_id')];

	$company_credential_cond = "";

	if ($company_id >0) {
	    $company_credential_cond = " and comp.id in($company_id)";
	}

	if ($store_location_id !='') {
	    $store_location_credential_cond = " and a.id in($store_location_id)"; 
	}

	if($item_cate_id !='') {
	    $item_cate_credential_cond = $item_cate_id ;  
	}
//========== user credential end ==========
//--------------------------------------------------------------------------------------------------------------------
	echo load_html_head_contents("Grey Production Entry", "../", 1, 1,'','1',''); 
	/*echo "<pre>";
	print_r($_SESSION['logic_erp']['data_arr'][98]);
	echo "</pre>";*/

	?>
	<script>
		if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
		var permission='<? echo $permission; ?>';

		var field_level_data="";
		<?
		if(isset($_SESSION['logic_erp']['data_arr'][98]))
		{
			$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][98] );
			echo "field_level_data= ". $data_arr . ";\n";
		}
		?>

		//var str_brand = [<? echo substr(return_library_autocomplete( "select distinct(brand_name) from lib_brand", "brand_name"  ), 0, -1); ?>];
 	//var str_color = [<?echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name", "color_name" ), 0, -1); ?>];

 	$(document).ready(function(e)
 	{
 		/*$("#txt_brand").autocomplete({
 			source: str_brand
 		});

		  $("#txt_color").autocomplete({
			 source: str_color
			});*/
		set_receive_basis();
	});



 	function set_receive_basis(loadBuyer=1)
 	{
 		var cbo_company_id = $('#cbo_company_id').val();
 		var recieve_basis = $('#cbo_receive_basis').val();
 		$("#greyproductionentry_1 :input").prop("disabled", false);
 		$('#cbo_receive_basis').attr('disabled','disabled');
		setFieldLevelAccess(cbo_company_id);

 		$('#txt_yarn_issued').val('');
 		$('#booking_without_order').val('');
 		$('#txt_job_no').val('');
 		$('#txt_receive_qnty').val('');
 		$('#txt_receive_qnty_pcs').val('');
 		$('#txt_service_booking').val('');
 		$('#txt_reject_fabric_recv_qnty').val('');
 		$('#all_po_id').val('');
 		$('#save_data').val('');
 		$('#txt_color').val('');
 		$('#color_id').val('');
 		$('#txt_deleted_id').val('');
 		$('#roll_details_list_view').html('');
 		$('#txt_machine_gg').val('');
 		$('#txt_machine_dia').val('');
 		$('#within_group').val('');

 		$('#txt_receive_qnty').attr('readonly','readonly');
 		$('#txt_receive_qnty').attr('onClick','openmypage_po();');	
 		$('#txt_receive_qnty').attr('placeholder','Single Click');
		$('#txt_coller_cuff_size').attr('disabled','disabled');
		
 		$('#txt_reject_fabric_recv_qnty').attr('readonly','readonly');
 		$('#txt_reject_fabric_recv_qnty').attr('placeholder','Display');

 		if(loadBuyer==1)
 		{
	 		if(cbo_company_id != 0)
	 		{
	 			load_drop_down( 'requires/grey_production_entry_controller_v2',recieve_basis+'_'+cbo_company_id, 'load_drop_down_buyer', 'buyer_td_id' );
	 		}
	 		else
	 		{
	 			$("#buyer_td_id").html('<? echo create_drop_down( "cbo_buyer_name", 152, $blank_array,"", 1, "-- Select Buyer --", 0, "",1 ); ?>');
	 		}
	 	}

 		if(recieve_basis == 0 )
 		{
 			$('#txt_booking_no').val('');	
 			$('#txt_booking_no_id').val('');	
 			$('#txt_booking_no').attr('disabled','disabled');
 			$('#cbo_buyer_name').removeAttr('disabled','disabled');	
 			$('#cbo_body_part').removeAttr('disabled','disabled');	
 			$('#fabric_desc_id').val('');
 			$('#txt_fabric_description').val('');
 			$('#txt_fabric_description').removeAttr('disabled','disabled');	
 			$('#txt_machine_gg').removeAttr('disabled','disabled');	
 			$('#txt_machine_dia').removeAttr('disabled','disabled');	
 		}
 		else
 		{
 			$('#txt_booking_no').val('');	
 			$('#txt_booking_no_id').val('');
 			$('#txt_booking_no').removeAttr('disabled','disabled');
 			$('#cbo_buyer_name').val(0);
 			$('#cbo_buyer_name').attr('disabled','disabled');
 			$('#cbo_body_part').val(0);
 			$('#cbo_body_part').attr('disabled','disabled');
 			$('#fabric_desc_id').val('');
 			$('#txt_fabric_description').val('');
 			$('#txt_fabric_description').attr('disabled','disabled');


 			if(recieve_basis == 2 )
 			{
 				$('#txt_machine_gg').attr('disabled','disabled');	
 				$('#txt_machine_dia').attr('disabled','disabled');
 				$('#txt_job_no').attr('disabled','disabled');
 				$('#cbo_buyer_name').attr('disabled','disabled');
 			}
 			else
 			{
 				$('#txt_machine_gg').removeAttr('disabled','disabled');	
 				$('#txt_machine_dia').removeAttr('disabled','disabled');
 				$('#cbo_buyer_name').removeAttr('disabled','disabled');
 				$('#txt_job_no').removeAttr('disabled','disabled');
 			}
 		}

 		$('#list_fabric_desc_container').html('');
 		$('#list_program_wise_fabric_desc_container').html('');

 	}


	function openmypage_serviceBooking(type) //Service Booking Knitting
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var recieve_basis = $('#cbo_receive_basis').val();
		var cbo_knitting_source = $('#cbo_knitting_source').val();
		var cbo_knitting_company = $('#cbo_knitting_company').val();
		var txt_booking_no_id = $('#txt_booking_no_id').val();
		var is_service_booking_mandatory = $('#is_service_booking_mandatory').val();
		
		if (form_validation('cbo_company_id*cbo_knitting_source*txt_booking_no','Company*Knitting Source*Booking/Knit Plan')==false)
		{
			return;
		}
		
			var title = 'Booking Selection Form';//
			var page_link = 'requires/grey_production_entry_controller_v2.php?cbo_company_id='+cbo_company_id+'&cbo_knitting_source='+cbo_knitting_source+'&cbo_knitting_company='+cbo_knitting_company+'&recieve_basis='+recieve_basis+'&txt_booking_no_id='+txt_booking_no_id+'&is_service_booking_mandatory='+is_service_booking_mandatory+'&action=serviceBooking_popup';
			var popup_width="1070px";
		    //if(recieve_basis==1)  popup_width="1060px"; else  popup_width="1070px"; 
		    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=400px,center=1,resize=1,scrolling=0','');
		    emailwindow.onclose=function()
		    {
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var theemail=this.contentDoc.getElementById("service_hidden_booking_id").value;	 //Knit Id for Kintting Plan
				var theename=this.contentDoc.getElementById("service_hidden_booking_no").value; //all data for Kintting Plan
				var service_booking_without_order=this.contentDoc.getElementById("service_booking_without_order").value;
				var hidden_knitting_company=this.contentDoc.getElementById("hidden_knitting_company").value; 
				
				if(theemail!="")
				{
					freeze_window(5);
					
					if(recieve_basis==2)
					{
						get_php_form_data(theename+"**"+service_booking_without_order+"**"+hidden_knitting_company+"**"+cbo_company_id+"**"+cbo_knitting_source, "populate_data_from_service_booking", "requires/grey_production_entry_controller_v2" );
						$('#txt_service_booking').val(theename);
						//$('#txt_booking_no_id').val(theemail);
						$('#cbo_knitting_company').attr('disabled',true);
						$('#service_booking_without_order').val(service_booking_without_order);
						$('#booking_without_order').val(service_booking_without_order);
					}
					show_list_view(theemail,'show_service_booking_programlist','list_program_wise_fabric_desc_container','requires/grey_production_entry_controller_v2','');
					release_freezing();
				}
			}
		}
		
	function openmypage_fabricBooking()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var recieve_basis = $('#cbo_receive_basis').val();
		var cbo_knitting_source = $('#cbo_knitting_source').val();
		var cbo_knitting_company = $('#cbo_knitting_company').val();
		var roll_maintained = $('#roll_maintained').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'Booking Selection Form';
			var page_link = 'requires/grey_production_entry_controller_v2.php?cbo_company_id='+cbo_company_id+'&recieve_basis='+recieve_basis+'&action=fabricBooking_popup';
			var popup_width="1070px";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=400px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var theemail=this.contentDoc.getElementById("hidden_booking_id").value;	 //Knit Id for Kintting Plan
				var theename=this.contentDoc.getElementById("hidden_booking_no").value; //all data for Kintting Plan
				var booking_without_order=this.contentDoc.getElementById("booking_without_order").value; 
				var salesData=this.contentDoc.getElementById("salesData").value; 
				var entry_form=this.contentDoc.getElementById("entry_form").value; 
				if(theemail!="")
				{
					freeze_window(5);
					set_receive_basis();
					
					//Fabric Booking
					if(recieve_basis==1)
					{
						get_php_form_data(theemail+"**"+booking_without_order+"**"+roll_maintained, "populate_data_from_booking", "requires/grey_production_entry_controller_v2" );
						show_list_view(theename+"**"+booking_without_order+"**"+entry_form,'show_fabric_desc_listview','list_fabric_desc_container','requires/grey_production_entry_controller_v2','');
						
					}

					//Sales Order
					else if(recieve_basis==4)
					{
						$('#txt_booking_no').val(theename);
						$('#txt_booking_no_id').val(theemail);
						$('#booking_without_order').val(booking_without_order);
						
						var data=salesData.split("**");						
						$('#within_group').val(data[0]);
						
						if(data[0]==1)
						{
							$("#cbo_buyer_name option[value!='0']").remove();
							$("#cbo_buyer_name").append("<option selected value='"+data[1]+"'>"+data[2]+"</option>");
						}
						else
						{
							$("#cbo_buyer_name").val(data[1]);
						}
						
						if(roll_maintained==0)
						{
							$('#txt_receive_qnty').removeAttr('readonly','readonly');
							$('#txt_receive_qnty').removeAttr('onClick','onClick');
							$('#txt_receive_qnty').removeAttr('placeholder','Single Click');
							$('#txt_coller_cuff_size').attr('disabled',false);	
							
							$('#txt_reject_fabric_recv_qnty').removeAttr('readonly','readonly');
							$('#txt_reject_fabric_recv_qnty').removeAttr('placeholder','Write');	
						}
						else
						{
							$('#txt_receive_qnty').attr('readonly','readonly');
							$('#txt_receive_qnty').attr('onClick','openmypage_po();');
							$('#txt_receive_qnty').attr('placeholder','Single Click');
							$('#txt_coller_cuff_size').attr('disabled','disabled');
							
							$('#txt_reject_fabric_recv_qnty').attr('readonly','readonly');
							$('#txt_reject_fabric_recv_qnty').attr('placeholder','Display');	
						}
						
						show_list_view(theemail,'show_fabric_desc_listview_salesOrder','list_fabric_desc_container','requires/grey_production_entry_controller_v2','');
					}
					else
					{
						
						var data=theename.split("**");
						$('#txt_booking_no').val(theemail);
						$('#txt_booking_no_id').val(theemail);
						$('#booking_without_order').val(booking_without_order);
						$('#cbo_body_part').val(data[0]);
						$('#fabric_desc_id').val(data[1]);
						$('#txt_fabric_description').val(data[2]);
						$('#txt_gsm').val(data[3]);
						$('#txt_width').val(data[4]);
						$('#txt_job_no').val(data[5]);
						
						$('#all_po_id').val(data[7]);
						$('#cbo_knitting_source').val(data[8]);
						$('#txt_stitch_length').val(data[10]);
						$('#cbo_color_range').val(data[11]);
						$('#txt_color').val(data[15]);
						$('#color_id').val(data[16]);
						$('#txt_machine_dia').val(data[17]);
						$('#txt_machine_gg').val(data[18]);
						$('#within_group').val(data[19]);
						$('#req_no_id').val(data[21]);
						$('#body_part_type_id').val(data[24]);
						
						//knitting_source
						if(data[8] == 3)
						{
							$('#txt_service_booking').attr('disabled',false);
						}
						
						//sales order
						if(data[23]==1)
						{
							//within_group
							if(data[19]==1)
							{
								$("#cbo_buyer_name option[value!='0']").remove();
								$("#cbo_buyer_name").append("<option selected value='"+data[6]+"'>"+data[20]+"</option>");
							}
							else
							{
								load_drop_down( 'requires/grey_production_entry_controller_v2',recieve_basis+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_buyer', 'buyer_td_id' );	
								$('#cbo_buyer_name').val(data[6]);
							}
						}
						else
						{
							load_drop_down( 'requires/grey_production_entry_controller_v2',recieve_basis+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_buyer', 'buyer_td_id' );	
							$('#cbo_buyer_name').val(data[6]);
						}
						
	
						if($('#process_costing_maintain').val()*1!=1)
						{
							$('#txt_brand').val(data[14]);
							set_multiselect('cbo_yarn_count','0','1',data[12],'0');
							$('#txt_yarn_lot').val(data[13]);
							$('#yarn_prod_id').val(data[22]);
						}
						
						load_drop_down( 'requires/grey_production_entry_controller_v2',data[8]+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_knitting_com','knitting_com');
						$('#cbo_knitting_company').val(data[9]);
	
						var cbo_knitting_company2 = $('#cbo_knitting_company').val();
						load_drop_down( 'requires/grey_production_entry_controller_v2',cbo_knitting_company2, 'load_drop_down_location', 'location_td' );
						if(data[8]==1)
						{
							//load_drop_down( 'requires/grey_production_entry_controller_v2', data[9], 'load_drop_down_store', 'store_td' );
							//store_load(1);
						}
						
						//Knitting Plan
						if(recieve_basis==2)
						{
							get_php_form_data(theemail, "populate_data_from_plan", "requires/grey_production_entry_controller_v2" );
							$('#cbo_buyer_name').attr('disabled','disabled');
							get_php_form_data(theemail+'_'+cbo_company_id, "populate_data_from_booking2", "requires/grey_production_entry_controller_v2" );
							$("#cbo_knitting_source").attr("disabled",'true');
							$("#cbo_knitting_company").attr("disabled",'true');
							//$("#cbo_location_name").attr("disabled",'true');
							//load_floor();

							if($("#cbo_knitting_source").val() ==3 && $("#is_service_booking_mandatory").val() ==1 && booking_without_order != 1 )
							{
								$("#service_booking_td_text").css("color","blue");//addClass("must_entry_caption");
							}


							load_operator();
							out_bound_machine_load($("#cbo_knitting_source").val());
						}
						//show_list_view(theemail,'show_fabric_desc_listview_plan','list_fabric_desc_container','requires/grey_production_entry_controller_v2','');
						
					}
					load_floor();
					openmypage_po();
					//set_auto_complete(2);
					release_freezing();
				} 
			}
		}
	}
	
	function set_form_data(data)
	{
		var data=data.split("**");
		$('#cbo_body_part').val(data[0]);
		$('#txt_fabric_description').val(data[1]);
		$('#txt_gsm').val(data[2]);
		$('#txt_width').val(data[3]);
		$('#fabric_desc_id').val(data[4]);
		$('#body_part_type_id').val(data[5]);
	}
	
	function set_sales_form_data(data)
	{
		var data=data.split("**");
		$('#cbo_body_part').val(data[0]);
		$('#txt_fabric_description').val(data[1]);
		$('#txt_gsm').val(data[2]);
		$('#txt_width').val(data[3]);
		$('#fabric_desc_id').val(data[4]);
		$('#color_id').val(data[5]);
		$('#txt_color').val(data[6]);
		$('#cbo_color_range').val(data[7]);
		$('#body_part_type_id').val(data[8]);
	}
	
	function set_form_data_plan(data)
	{
		var data=data.split("**");
		$('#cbo_body_part').val(data[0]);
		$('#txt_fabric_description').val(data[1]);
		$('#txt_gsm').val(data[2]);
		$('#txt_width').val(data[3]);
		$('#fabric_desc_id').val(data[4]);
		$('#cbo_color_range').val(data[5]);
		$('#txt_stitch_length').val(data[6]);
		$('#txt_color').val(data[10]);
		$('#color_id').val(data[11]);
		$('#txt_machine_dia').val(data[12]);
		$('#txt_machine_gg').val(data[13]);
		$('#body_part_type_id').val(data[14]);
		
		if($('#process_costing_maintain').val()*1!=1)
		{
			set_multiselect('cbo_yarn_count','0','1',data[7],'0');
			$('#txt_yarn_lot').val(data[8]);
		}
	}


	function set_form_data_sb(data)
	{
		var data=data.split("**");
		$('#cbo_body_part').val(data[0]);
		$('#txt_fabric_description').val(data[1]);
		$('#txt_gsm').val(data[2]);
		$('#txt_width').val(data[3]);
		$('#fabric_desc_id').val(data[4]);
		$('#cbo_color_range').val(data[5]);
		$('#txt_stitch_length').val(data[6]);
		$('#txt_color').val(data[7]);
		$('#color_id').val(data[8]);
		$('#txt_machine_dia').val(data[9]);
		$('#txt_machine_gg').val(data[10]);
		$('#txt_booking_no').val(data[11]);
		$('#txt_booking_no_id').val(data[11]);

		$('#cbo_knitting_source').val(data[12]);
		$('#txt_brand').val(data[16]);
		$('#body_part_type_id').val(data[18]);

		if($('#process_costing_maintain').val()*1!=1)
		{
			set_multiselect('cbo_yarn_count','0','1',data[14],'0');
			$('#txt_yarn_lot').val(data[15]);
		}
		$('#cbo_knitting_company').val(data[13]);

		var recieve_basis = $('#cbo_receive_basis').val();
		var cbo_company_id = $('#cbo_company_id').val();
		var txt_service_booking = $('#txt_service_booking').val();

		load_drop_down( 'requires/grey_production_entry_controller_v2',recieve_basis+'_'+cbo_company_id, 'load_drop_down_buyer', 'buyer_td_id' );
		$('#cbo_buyer_name').val(data[17]);
		load_drop_down( 'requires/grey_production_entry_controller_v2',cbo_company_id, 'load_drop_down_location', 'location_td' );

		if(recieve_basis==2 && data[12] == 3 && txt_service_booking!="")
		{
			get_php_form_data(data[11]+'_'+cbo_company_id, "populate_data_from_booking2", "requires/grey_production_entry_controller_v2" );
		}
	}
	
	function openmypage_fabricDescription()
	{
		var title = 'Fabric Description Info';	
		var page_link = 'requires/grey_production_entry_controller_v2.php?action=fabricDescription_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=370px,center=1,resize=1,scrolling=0','');
		
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
		var booking_id=$('#txt_booking_no_id').val();
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_com_location_name = $('#cbo_com_location_name').val();
		var dtls_id = $('#update_dtls_id').val();
		var roll_maintained = $('#roll_maintained').val();
		var fabric_store_auto_update = $('#fabric_store_auto_update').val();
		var store_update_upto = $('#store_update_upto').val();
		var cbo_store_name = $('#cbo_store_name').val();
		if(fabric_store_auto_update==1 && store_update_upto>0 )
		{
			if(cbo_company_id == 0)
			{
				alert("Company Select First");
				$('#cbo_company_id').focus();
				return;
			}
			if(cbo_com_location_name == 0)
			{
				alert("Company Location Select First");
				$('#cbo_com_location_name').focus();
				return;
			}
			if(cbo_store_name == 0)
			{
				alert("Store Select First");
				$('#cbo_store_name').focus();
				return;
			}
		}
		
		var barcode_generation = $('#barcode_generation').val();
		var production_control = $('#production_control').val();
		var save_data_stylewise = $('#save_data_stylewise').val();
		var all_po_id = $('#all_po_id').val();
		var txt_receive_qnty = $('#txt_receive_qnty').val(); 
		var txt_reject_fabric_recv_qnty = $('#txt_reject_fabric_recv_qnty').val(); 
		var distribution_method = $('#distribution_method_id').val();
		var booking_without_order = $('#booking_without_order').val();
		
		var cbo_body_part=$('#cbo_body_part').val();
		var txt_fabric_description=$('#txt_fabric_description').val();
		var txt_gsm=$('#txt_gsm').val();
		var txt_width=$('#txt_width').val();
		var fabric_desc_id=$('#fabric_desc_id').val();
		var txt_deleted_id=$('#txt_deleted_id').val();

		if((receive_basis==1 || receive_basis==2) && booking_no=="")
		{
			alert("Please Select Booking No. / Knit Plan");
			$('#txt_booking_no').focus();
			return false;
		}
		else if((receive_basis==1 || receive_basis==2 || receive_basis==4) && txt_fabric_description=="")
		{
			alert("Please Select Fabric Description.");
			$('#txt_fabric_description').focus();
			return false;
		}
		else if(receive_basis==0 && cbo_company_id==0)
		{
			alert("Please Select Company.");
			$('#cbo_company_id').focus();
			return false;
		}
		
		if(roll_maintained==1) 
		{
			popup_width='1300px';
		}
		else
		{
			popup_width='1200px';
		}
		var title = 'PO Info';	
		var page_link = 'requires/grey_production_entry_controller_v2.php?receive_basis='+receive_basis+'&cbo_company_id='+cbo_company_id+'&booking_no='+booking_no+'&dtls_id='+dtls_id+'&all_po_id='+all_po_id+'&roll_maintained='+roll_maintained+'&production_control='+production_control+'&barcode_generation='+barcode_generation+'&save_data_stylewise='+save_data_stylewise+'&txt_receive_qnty='+txt_receive_qnty+'&txt_receive_qnty_pcs='+txt_receive_qnty_pcs+'&prev_distribution_method='+distribution_method+'&cbo_body_part='+cbo_body_part+'&txt_gsm='+txt_gsm+'&txt_width='+txt_width+'&fabric_desc_id='+fabric_desc_id+'&txt_deleted_id='+txt_deleted_id+'&txt_reject_fabric_recv_qnty='+txt_reject_fabric_recv_qnty+'&booking_without_order='+booking_without_order+'&booking_id='+booking_id+'&cbo_com_location_name='+cbo_com_location_name+'&cbo_store_name='+cbo_store_name+'&fabric_store_auto_update='+fabric_store_auto_update+'&store_update_upto='+store_update_upto+'&action=po_popup_stylewise';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=430px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var save_string=this.contentDoc.getElementById("save_string").value;	 //Access form field with id="emailfield"
			var tot_grey_qnty=this.contentDoc.getElementById("tot_grey_qnty").value; //Access form field with id="emailfield"
			var tot_grey_qnty_pcs=this.contentDoc.getElementById("tot_grey_qnty_pcs").value; //Access form field with id="emailfield"
			var tot_reject_qnty=this.contentDoc.getElementById("tot_reject_qnty").value;
			var number_of_roll=this.contentDoc.getElementById("number_of_roll").value; //Access form field with id="emailfield"
			
			var string_size_qty=this.contentDoc.getElementById("string_size_qty").value;
			var all_po_id=this.contentDoc.getElementById("all_po_id").value; //Access form field with id="emailfield"
			var distribution_method=this.contentDoc.getElementById("distribution_method").value;
			var hide_deleted_id=this.contentDoc.getElementById("hide_deleted_id").value;
			
			$('#save_data_stylewise').val(save_string);
			$('#txt_coller_cuff_size').val(string_size_qty);
			$('#txt_receive_qnty').val(tot_grey_qnty);
			$('#txt_receive_qnty_pcs').val(tot_grey_qnty_pcs);
			$('#txt_reject_fabric_recv_qnty').val(tot_reject_qnty);
			$('#txt_receive_qnty').val(tot_grey_qnty);
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
		}
	}
	
	function fnc_grey_production_entry(operation)
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		if($("#cbo_company_id").val() ==0 )
		{
			alert("Please Fill up Company Value");
			fnc_reset();
			return;
		}
		if($("#txt_receive_date").val() =="" )
		{
			alert("Please Fill up Production Date Value");
			fnc_reset();
			return;
		}
		if($("#cbo_com_location_name").val() ==0 )
		{
			alert("Please Fill up Company Location Value");
			fnc_reset();
			return;
		}

		var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_receive_date').val(), current_date)==false)
		{
			alert("Production Date Can not Be Greater Than Today");
			fnc_reset();
			return;
		}
		
		if($('#fabric_store_auto_update').val()==1)
		{
			if($("#cbo_store_name").val() ==0 )
			{
				alert("Please Fill up Store Name Value");
				fnc_reset();
				return;
			}
		}
		var roll_maintained=$('#roll_maintained').val()*1;
		var fabric_store_auto_update=$('#fabric_store_auto_update').val()*1;
		var store_update_upto=$('#store_update_upto').val()*1;
		var cbo_floor=$('#cbo_floor').val()*1;
		var cbo_room=$('#cbo_room').val()*1;
		var txt_rack=$('#txt_rack').val()*1;
		var txt_shelf=$('#txt_shelf').val()*1;
		
		if(fabric_store_auto_update==1 && store_update_upto > 1)
		{
			if(store_update_upto==5 && (cbo_floor==0 || cbo_room==0 || txt_rack==0 || txt_shelf==0))
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

		var body_part_type_id=$("#body_part_type_id").val();
		
		if($("#process_costing_maintain").val()==1)
		{	
			if($("#txt_yarn_lot").val() =="" )
			{
				alert("Please Fill up Yarn Lot Value");
				fnc_reset();
				return;
			}
		}
		if($('#cbo_knitting_source').val()==3)
		{
			/*if($("#txt_receive_chal_no").val() =="" )
			{
				alert("Please Fill up Receive Challan No Value");
				fnc_reset();
				return;
			}*/
			if($("#cbo_knitting_source").val()==0 )
			{
				alert("Please Fill up Knitting Source Value");
				fnc_reset();
				return;
			}
			if($("#cbo_knitting_company").val()==0)
			{
				alert("Please Fill up Knitting Company Value");
				fnc_reset();
				return;
			}
			if($("#cbo_body_part").val()==0)
			{
				alert("Please Fill up Body Part Value");
				fnc_reset();
				return;
			}
			if($("#txt_fabric_description").val()=="")
			{
				alert("Please Fill up Fabric Description Value");
				fnc_reset();
				return;
			}
			if($("#txt_gsm").val()=="")
			{
				alert("Please Fill up GSM Value");
				fnc_reset();
				return;
			}
			if($("#txt_brand").val()=="")
			{
				alert("Please Fill up Brand Value");
				fnc_reset();
				return;
			}
			if($("#cbo_color_range").val()==0)
			{
				alert("Please Fill up Color Range Value");
				fnc_reset();
				return;
			}
		}
		else
		{
			if($("#cbo_knitting_source").val()==0 )
			{
				alert("Please Fill up Knitting Source Value");
				fnc_reset();
				return;
			}
			if($("#cbo_knitting_company").val()==0)
			{
				alert("Please Fill up Knitting Company Value");
				fnc_reset();
				return;
			}
			if($("#cbo_location_name").val() =="" )
			{
				alert("Please Fill up Location Name Value");
				fnc_reset();
				return;
			}
			if($("#cbo_body_part").val()==0)
			{
				alert("Please Fill up Body Part Value");
				fnc_reset();
				return;
			}
			if($("#txt_fabric_description").val()=="")
			{
				alert("Please Fill up Fabric Description Value");
				fnc_reset();
				return;
			}
			if($("#txt_gsm").val()=="")
			{
				alert("Please Fill up GSM Value");
				fnc_reset();
				return;
			}

			if($("#cbo_color_range").val()==0)
			{
				alert("Please Fill up Color Range Value");
				fnc_reset();
				return;
			}

			if($("#cbo_floor_id").val()==0)
			{
				alert("Please Fill up Prod. Floor Value");
				fnc_reset();
				return;
			}
		}

		if($('#txt_receive_qnty').val()==0 && $('#txt_reject_fabric_recv_qnty').val()==0)
		{
			if($("#txt_receive_qnty").val()==0 || $("#txt_receive_qnty").val()=="")
			{
				alert("Please Fill up Grey Receive Qnty Value ==");
				alert($("#txt_receive_qnty").val());
				fnc_reset();
				return;
			}
			if($("#txt_reject_fabric_recv_qnty").val()==0 || $("#txt_reject_fabric_recv_qnty").val()=="")
			{
				alert("Please Fill up Reject Fabric Receive Value");
				fnc_reset();
				return;
			}
		}
		
		if(!(body_part_type_id==50 || body_part_type_id==40))
		{
			if($("#txt_width").val()=="")
			{
				alert("Please Fill up Dia/Width Value");
				fnc_reset();
				return;
			}
			if($("#txt_stitch_length").val()=="")
			{
				alert("Please Fill up Stitch Length Value");
				fnc_reset();
				return;
			}	
		}

		if($('#is_service_booking_mandatory').val()==1 && $('#cbo_knitting_source').val()==3 && $('#cbo_receive_basis').val()==2 && $('#booking_without_order').val() !=1 )
		{	
			if($("#txt_service_booking").val()=="")
			{
				alert("Please Fill up Service Booking Value");
				fnc_reset();
				return;
			}	
		}

		if($('#cbo_receive_basis').val()==2 && ($('#txt_booking_no_id').val() != $('#txt_booking_no').val()))
		{
			alert("Please Browse Knitting Plan again");
			$('#txt_booking_no').focus();
			fnc_reset();
			return;
		}
		
		if($('#cbo_receive_basis').val()==1 && $('#txt_booking_no').val()=="")
		{
			alert("Please Select Booking No");
			$('#txt_booking_no').focus();
			fnc_reset();
			return;
		}
		// if($('#cbo_knitting_source').val()==1 && $('#cbo_machine_name').val()==0)
		if( ($('#cbo_knitting_source').val()==1 && $('#cbo_machine_name').val()==0) || ($('#cbo_knitting_source').val()==1 && $('#cbo_machine_name').val()==null) )
		{
			alert("Please Select Machine Name");
			$('#cbo_machine_name').focus();
			fnc_reset();
			return;
		}
		if($('#cbo_knitting_source').val()==1 && $('#txt_shift_name').val()==0)
		{
			alert("Please Select Shift Name");
			$('#txt_shift_name').focus();
			fnc_reset();
			return;
		}
		
		var txt_receive_qnty = $('#txt_receive_qnty').val();
		var txt_service_booking = $('#txt_service_booking').val();
		var allowedQty = $('#balance').text()*1;

		var booking_no_id = $('#txt_booking_no_id').val();
		var booking_without_order = $('#booking_without_order').val();
		var cbo_receive_basis = $('#cbo_receive_basis').val();
		var update_dtls_id = $('#update_dtls_id').val();
		var check_production_qnty = return_global_ajax_value(booking_no_id+'_'+booking_without_order+'_'+cbo_receive_basis+'_'+txt_receive_qnty+'_'+operation+'_'+update_dtls_id, 'check_production_qnty', '', 'requires/grey_production_entry_controller_v2');
		var check_production_qnty_reponse=trim(check_production_qnty).split('**');
		if(check_production_qnty_reponse[0]==1){
			if(!confirm("Production Quantity Exceeded Required Quantity. Do you want to continue?")){
				return;
			}
		}
		
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_recieved_id*cbo_company_id*cbo_receive_basis*txt_receive_date*txt_receive_chal_no*txt_booking_no_id*txt_booking_no*cbo_store_name*cbo_knitting_source*cbo_knitting_company*cbo_location_name*txt_yarn_issue_challan_no*cbo_buyer_name*txt_yarn_issued*cbo_body_part*txt_fabric_description*fabric_desc_id*txt_gsm*txt_width*cbo_floor_id*cbo_machine_name*txt_roll_no*txt_remarks*txt_receive_qnty*txt_reject_fabric_recv_qnty*txt_shift_name*cbo_uom*txt_yarn_lot*txt_binbox*cbo_yarn_count*txt_brand*txt_color*color_id*cbo_color_range*txt_stitch_length*txt_machine_dia*txt_machine_gg*update_id*all_po_id*update_dtls_id*update_trans_id*save_data*previous_prod_id*hidden_receive_qnty*hidden_receive_rate*roll_maintained*fabric_store_auto_update*booking_without_order*txt_deleted_id*process_string*knitting_charge_string*process_costing_maintain*within_group*txt_operator_id*txt_receive_qnty_pcs*txt_service_booking*service_booking_without_order*previous_receive_qnty*cbo_sub_contract*yarn_prod_id*txt_coller_cuff_size*store_update_upto*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_com_location_name*save_data_stylewise',"../");
		//alert(data);return;
		freeze_window(operation);
		
		http.open("POST","requires/grey_production_entry_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_grey_production_entry_Reply_info;
	}
	
	function fnc_grey_production_entry_Reply_info()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText);return;
			var reponse=trim(http.responseText).split('**');
			/*if(reponse[5]==31)
			{
			alert('Production Quantity Exceeded Required Quantity');
			//release_freezing();	
			//return;
			}*/
			if(reponse[0]==15) 
			{ 
				setTimeout('fnc_grey_production_entry('+ reponse[1] +')',8000); 
				if(reponse[2]!="")
				{
					alert(reponse[2]);
				}
				fnc_reset();
				return;
			}	
			else if(reponse[0]==30)
			{
				alert(reponse[1]);
				fnc_reset();
				release_freezing();	
				return;
			}	
			else if(reponse[0]==23)
			{
				alert(reponse[1]);
				fnc_reset();
				release_freezing();	
				return;
			}
			else if(reponse[0]==13)
			{
				alert('Bellow Grey fabric delivery to store Id Found. Update Not Allowed.\n Delivery Challan No: '+reponse[1]+"\n");
				release_freezing();	
				fnc_reset();
				return;
			}	

			show_msg(reponse[0]);

			var auto_barcode_generate=$('#auto_barcode_generate').val();

			//if((reponse[0]==0 || reponse[0]==1))
			if( (reponse[0]==1 && auto_barcode_generate !=2) || ((reponse[0]==0 || reponse[0]==1) && auto_barcode_generate==2) )
			{
			 	document.getElementById('update_id').value = reponse[1];
			 	document.getElementById('txt_recieved_id').value = reponse[2];

			 	$('#cbo_company_id').attr('disabled','disabled');
			 	$('#cbo_receive_basis').attr('disabled','disabled');
			 	$('#txt_booking_no').attr('disabled','disabled');
			 	$('#cbo_com_location_name').attr('disabled','disabled');

			 	show_list_view(reponse[1],'show_grey_prod_listview','list_container_knitting','requires/grey_production_entry_controller_v2','');

			 	var receive_basis=$('#cbo_receive_basis').val();
			 	if(receive_basis==2 || receive_basis==1 || receive_basis==4)
			 	{
			 		reset_form('greyproductionentry_1','roll_details_list_view','','','','update_id*txt_recieved_id*cbo_company_id*cbo_receive_basis*txt_receive_date*cbo_com_location_name*txt_receive_chal_no*txt_booking_no*txt_booking_no_id*cbo_buyer_name*cbo_store_name*cbo_knitting_source*cbo_knitting_company*cbo_location_name*cbo_sub_contract*txt_yarn_issue_challan_no*txt_job_no*txt_remarks*roll_maintained*barcode_generation*fabric_store_auto_update*booking_without_order*txt_yarn_issued*cbo_body_part*fabric_desc_id*txt_fabric_description*txt_gsm*txt_width*txt_stitch_length*txt_machine_dia*txt_machine_gg*txt_color*color_id*cbo_color_range*cbo_yarn_count*txt_yarn_lot*yarn_prod_id*process_costing_maintain*production_control*auto_barcode_generate*txt_service_booking*service_booking_without_order*allowedQtyTotal');
			 	}
			 	else
			 	{
			 		reset_form('greyproductionentry_1','roll_details_list_view','','','','update_id*txt_recieved_id*cbo_company_id*cbo_receive_basis*txt_receive_date*cbo_com_location_name*txt_receive_chal_no*txt_booking_no*txt_booking_no_id*cbo_buyer_name*cbo_store_name*cbo_knitting_source*cbo_knitting_company*cbo_location_name*cbo_sub_contract*txt_yarn_issue_challan_no*txt_job_no*txt_remarks*roll_maintained*barcode_generation*fabric_store_auto_update*booking_without_order*txt_yarn_issued*process_costing_maintain*production_control*auto_barcode_generate');
			 	}

			 	set_button_status(0, permission, 'fnc_grey_production_entry',1);
			 	var cbo_receive_basis=$('#cbo_receive_basis').val();
			 	var roll_maintained=$('#roll_maintained').val();
			 	var knitting_source=$('#cbo_knitting_source').val();
			 	var txt_service_booking=$('#txt_service_booking').val();
			 	var service_booking_without_order=$('#service_booking_without_order').val();
			 	var cbo_knitting_company=$('#cbo_knitting_company').val();
			 	var cbo_company_id=$('#cbo_company_id').val();
			 	

			 	if(cbo_receive_basis==2 && knitting_source==3)
			 	{
			 		$('#qty_statistic').show();
			 		$('#allowedQty').text("= 0.00");
			 		$('#allowedQtyTotal').val("");
			 		$('#totalProduction').text("0.00");
			 		$('#balance').text("0.00");

					get_php_form_data(txt_service_booking+"**"+service_booking_without_order+"**"+cbo_knitting_company+"**"+cbo_company_id, "populate_data_from_service_booking", "requires/grey_production_entry_controller_v2" );					
				}

				if(cbo_receive_basis==2)
				{
					var theemail = $('#txt_booking_no_id').val();
					get_php_form_data(theemail+'_'+cbo_company_id, "populate_data_from_booking2", "requires/grey_production_entry_controller_v2" );
				}
			}
			else if(reponse[0]==20)
			{
				alert(reponse[1]);
				fnc_reset();
			}

			if(reponse[0]==0 && auto_barcode_generate !=2)
			{
				var data="***"+reponse[4]+"*********"+reponse[1];
				var api_data=return_ajax_request_value(data, "report_barcode_text_file_for_api", "requires/grey_production_entry_controller_v2");
				fnc_reset();
				$('#txt_booking_no').focus();
			}

			release_freezing();	
		}
	}
	
	function grey_receive_popup()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_location_name = $('#cbo_location_name').val();
		
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 	
			var page_link='requires/grey_production_entry_controller_v2.php?cbo_company_id='+cbo_company_id+'&cbo_location_name='+cbo_location_name+'&action=grey_receive_popup_search';
			var title='Grey Production Form';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=390px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var grey_recv_id=this.contentDoc.getElementById("hidden_recv_id").value;
				if(trim(grey_recv_id)!="")
				{
					freeze_window(5);
					reset_form('greyproductionentry_1','list_container_knitting*list_fabric_desc_container*list_program_wise_fabric_desc_container','','','','roll_maintained*barcode_generation*fabric_store_auto_update*process_costing_maintain*production_control');
					enable_from_field();

					get_php_form_data(grey_recv_id, "populate_data_from_grey_recv", "requires/grey_production_entry_controller_v2" );
					var txt_booking_no = $('#txt_booking_no').val();
					var txt_service_booking = $('#txt_service_booking').val();
					var service_booking_without_order = $('#service_booking_without_order').val();
					var cbo_knitting_company = $('#cbo_knitting_company').val();
					var cbo_company_id = $('#cbo_company_id').val();
					var knitting_source = $('#cbo_knitting_source').val();
					
					if(knitting_source==1)
					{
						//load_drop_down( 'requires/grey_production_entry_controller_v2', cbo_knitting_company, 'load_drop_operator', 'operator_td'); //me
					}
					
					
					var booking_without_order = $('#booking_without_order').val();
					var cbo_receive_basis = $('#cbo_receive_basis').val();
					//release_freezing();
					if(txt_booking_no!="" && cbo_receive_basis==1)
					{
						show_list_view(txt_booking_no+"**"+booking_without_order,'show_fabric_desc_listview','list_fabric_desc_container','requires/grey_production_entry_controller_v2','');
					}
					else if(txt_booking_no!="" && cbo_receive_basis==2 && knitting_source!=3)
					{
						show_list_view(txt_booking_no,'show_fabric_desc_listview_plan','list_fabric_desc_container','requires/grey_production_entry_controller_v2','');	
					}
					
					if(cbo_receive_basis==2 && knitting_source==3)
					{
						
						$('#qty_statistic').show();
						$('#allowedQty').text("= 0.00");
						$('#allowedQtyTotal').val("");
						$('#totalProduction').text("0.00");
						$('#balance').text("0.00");
						//get_php_form_data(theename+"**"+service_booking_without_order+"**"+hidden_knitting_company+"**"+cbo_company_id, "populate_data_from_service_booking", "requires/grey_production_entry_controller_v2" );
						get_php_form_data(txt_service_booking+"**"+service_booking_without_order+"**"+cbo_knitting_company+"**"+cbo_company_id+"**"+knitting_source, "populate_data_from_service_booking", "requires/grey_production_entry_controller_v2" );
						var service_booking_id = $('#service_booking_id').val();
						show_list_view(service_booking_id,'show_service_booking_programlist','list_program_wise_fabric_desc_container','requires/grey_production_entry_controller_v2','');
						$("#cbo_knitting_source").attr("disabled",'true');
						$("#cbo_knitting_company").attr("disabled",'true');
					}
					else if(cbo_receive_basis==2)
					{
						var theemail = $('#txt_booking_no_id').val();
						get_php_form_data(theemail+"_"+cbo_company_id, "populate_data_from_booking2", "requires/grey_production_entry_controller_v2" );
						$("#cbo_knitting_source").attr("disabled",'true');
						$("#cbo_knitting_company").attr("disabled",'true');
						//$("#cbo_location_name").attr("disabled",'true');
					}
					else
					{
						$('#qty_statistic').hide();	
					}
					
					show_list_view(grey_recv_id,'show_grey_prod_listview','list_container_knitting','requires/grey_production_entry_controller_v2','');
					if($("#roll_maintained").val()==1)
					{
						var next_process = trim(return_global_ajax_value(grey_recv_id, 'next_process_check', '', 'requires/grey_production_entry_controller_v2'));
						if(next_process==1)
						{
							$("#master_table :input:not([id=txt_recieved_id])").prop("disabled", true);
							$("#next_process_check").val(next_process);
						}
					}
					release_freezing();
				}
			}
		}
	}
	
	function enable_from_field()
	{
		if($('#roll_maintained').val()==1)
		{
			$("#greyproductionentry_1 :input").prop("disabled", false);
			var cbo_company_id = $('#cbo_company_id').val();
			setFieldLevelAccess(cbo_company_id);
		}
	}
	
	function put_data_dtls_part(id,type,page_path)
	{
		//get_php_form_data(id+"**"+$('#roll_maintained').val(), type, page_path );
		var roll_maintained=$('#roll_maintained').val();
		var barcode_generation = $('#barcode_generation').val();
		get_php_form_data(id+"**"+roll_maintained, type, page_path );
		var cbo_company_id = $('#cbo_company_id').val();
		//alert('ff');
	
		
		if(roll_maintained==1)
		{
		
			show_list_view("'"+id+"**"+barcode_generation+"**"+cbo_company_id+"'",'show_roll_listview','roll_details_list_view','requires/grey_production_entry_controller_v2','');

			if($("#next_process_check").val()==1)
			{
				$("#details_table :input:not([id=txt_receive_qnty],[id=txt_yarn_lot],:input[type=button],:input[type=checkbox])").prop("disabled", true);
				//$('#txt_yarn_lot').removeAttr('disabled','disabled');
			}
		}
		else
		{
			$('#roll_details_list_view').html('');
		}
	}
	
	function set_auto_complete(type)
	{
		if(type==1)
		{
			$("#txt_color").autocomplete({
				source: str_color
			});
		}
		else
		{
			var receive_basis=$('#cbo_receive_basis').val();
			var booking_id = $('#txt_booking_no_id').val();
			var booking_without_order = $('#booking_without_order').val();
			get_php_form_data(booking_id+"**"+booking_without_order+"**"+receive_basis, 'load_color', 'requires/grey_production_entry_controller_v2');
		}
	}
	
	function openmypage_color()
	{
		var recieve_basis = $('#cbo_receive_basis').val();
		var booking_id = $('#txt_booking_no_id').val();
		var booking_without_order = $('#booking_without_order').val();
		if(recieve_basis==1 || recieve_basis==2 || recieve_basis==4)
		{
			if (form_validation('txt_booking_no','F. Booking/Knit Plan')==false)
			{
				return;
			}
		}
		var color_id = $('#color_id').val();
		var title = 'Color Info';	
		var page_link='requires/grey_production_entry_controller_v2.php?recieve_basis='+recieve_basis+'&color_id='+color_id+'&booking_id='+booking_id+'&booking_without_order='+booking_without_order+'&action=color_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=350px,height=350px,center=1,resize=1,scrolling=0','');
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
		var booking_id = $('#txt_booking_no_id').val();
		var req_no = $('#req_no_id').val();
		var receive_basis = $('#cbo_receive_basis').val();
		if (form_validation('cbo_company_id*txt_booking_no','Company*F. Booking/Knit Plan')==false)
		{
			return;
		}
		else
		{ 	
			var page_link='requires/grey_production_entry_controller_v2.php?cbo_company_id='+cbo_company_id+'&booking_id='+booking_id+'&req_no='+req_no+'&receive_basis='+receive_basis+'&action=issue_challan_no_popup';
			var title='Issue Challan Info';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=390px,center=1,resize=1,scrolling=0','');
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
	function fnc_operator_name()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_knitting_company = $('#cbo_knitting_company').val();
		var booking_id = $('#txt_booking_no_id').val();
		var receive_basis = $('#cbo_receive_basis').val();
		var cbo_location_name = $('#cbo_location_name').val();
		if (form_validation('cbo_company_id*cbo_knitting_company*cbo_location_name','Company*Knitting Company*Location')==false)
		{
			return;
		}
		else
		{ 	
			var page_link='requires/grey_production_entry_controller_v2.php?cbo_company_id='+cbo_company_id+'&booking_id='+booking_id+'&receive_basis='+receive_basis+'&cbo_location_name='+cbo_location_name+'&cbo_knitting_company='+cbo_knitting_company+'&action=operator_name_popup';
			var title='Operator Info';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=280px,height=290px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var operator_hdn=this.contentDoc.getElementById("operator_hdn").value;
				if(trim(operator_hdn)!="")
				{
					operator_data=operator_hdn.split("_");
					operator_id=operator_data[0];
					operator_name=operator_data[1];
					freeze_window(5);
					$('#txt_operator_name').val(operator_name);
					$('#txt_operator_id').val(operator_id);
					release_freezing();
				}
			}
		}
	}
	
	function fnc_check_issue(issue_num)
	{
		if(issue_num!="")
		{
			var issue_result = trim(return_global_ajax_value(issue_num, 'issue_num_check', '', 'requires/grey_production_entry_controller_v2'));
			if(issue_result=="")
			{
				alert("Challan Number Not Found");
				$('#txt_yarn_issue_challan_no').val("");
			}
		}
	}
	function fnc_operator_popup(issue_num)
	{
		if(issue_num!="")
		{
			var issue_result = trim(return_global_ajax_value(issue_num, 'operator_popup_action', '', 'requires/grey_production_entry_controller_v2'));
			if(issue_result=="")
			{
				alert("Challan Number Not Found");
				$('#txt_yarn_issue_challan_no').val("");
			}
		}
	}
	function check_all_report()
	{
		$("input[name=chkBundle]").each(function(index, element) { 

			if( $('#check_all').prop('checked')==true) 
				$(this).attr('checked','true');
			else
				$(this).removeAttr('checked');
		});
	}
	
	function fnc_send_printer_text()
	{
		var dtls_id=$('#update_dtls_id').val();
		var mst_id=$('#update_id').val();
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
		
		data=data+"***"+dtls_id+"*********"+mst_id;
		var url=return_ajax_request_value(data, "report_barcode_text_file", "requires/grey_production_entry_controller_v2");
		// alert(url);
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
		window.open("requires/grey_production_entry_controller_v2.php?data=" + data+'&action=report_barcode_generation', true );
	}
	
	function fnc_barcode_code128(type)
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

		freeze_window(3);
		
		data=data+"***"+dtls_id;
		//window.open("requires/grey_production_entry_controller_v2.php?data=" + data+'&action=report_barcode_code128', true );
		if(type==1){
			var url=return_ajax_request_value(data, "print_barcode_one_128", "requires/grey_production_entry_controller_v2");
		} else if(type==2){
			var url=return_ajax_request_value(data, "print_barcode_one_128_v2", "requires/grey_production_entry_controller_v2");
		}else if(type==6){
			var url=return_ajax_request_value(data, "print_barcode_one_128_v3", "requires/grey_production_entry_controller_v2");
		} else if(type==4){
			var url=return_ajax_request_value(data, "print_barcode_metro", "requires/grey_production_entry_controller_v2");
		} else if(type==5){
			var url=return_ajax_request_value(data, "direct_print_barcode", "requires/grey_production_entry_controller_v2");
		} else if(type==7){
			var url=return_ajax_request_value(data, "print_barcode_one_88Y", "requires/grey_production_entry_controller_v2");
		} else if(type==8){
			var url=return_ajax_request_value(data, "print_barcode_a", "requires/grey_production_entry_controller_v2");
		} else if(type==9){
			var url=return_ajax_request_value(data, "print_barcode_b", "requires/grey_production_entry_controller_v2");
		} else if(type==10){
			var url=return_ajax_request_value(data, "print_barcode_one_128_v4", "requires/grey_production_entry_controller_v2");
		} else if(type==11){
			var url=return_ajax_request_value(data, "direct_print_barcode_2", "requires/grey_production_entry_controller_v2");
		} else if(type==12){
			var url=return_ajax_request_value(data, "direct_print_barcode_3", "requires/grey_production_entry_controller_v2");
		}else if(type==13){
			var url=return_ajax_request_value(data, "direct_print_barcode_4", "requires/grey_production_entry_controller_v2");
		} else{
			var url=return_ajax_request_value(data, "print_barcode_one_88", "requires/grey_production_entry_controller_v2");
		}

		window.open(url,"##");
		release_freezing();
	}
	
	function fnc_barcode_For_database() {
		var dtls_id=$('#update_dtls_id').val();
		var mst_id=$('#update_id').val();
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
		
		data=data+"***"+dtls_id+"*********"+mst_id;
		var response = return_ajax_request_value(data, "save_barcode_for_norsel", "requires/grey_production_entry_controller_v2");
		if(response==0)
		{
			show_msg('0');
		}
		else
		{
			show_msg('10');
		}
	   // window.open(url + ".zip", "##");
	}
	
	function fnc_barcode_For_extranal_database() {
		var dtls_id=$('#update_dtls_id').val();
		var mst_id=$('#update_id').val();
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
		
		data=data+"***"+dtls_id+"*********"+mst_id;
		var response = return_ajax_request_value(data, "save_barcode_for_extranal_database", "requires/grey_production_entry_controller_v2");
		if(response==0)
		{
			show_msg('0');
		}
		else
		{
			show_msg('10');
		}
	   // window.open(url + ".zip", "##");
	}
	
	function load_location()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_knitting_source = $('#cbo_knitting_source').val();
		var cbo_knitting_company = $('#cbo_knitting_company').val();
		if(cbo_knitting_source==1)
		{
			load_drop_down( 'requires/grey_production_entry_controller_v2',cbo_knitting_company, 'load_drop_down_location', 'location_td' );
		}
		else
		{
			//load_drop_down( 'requires/grey_production_entry_controller_v2',cbo_company_id, 'load_drop_down_location', 'location_td' );
		}
	}
	
	function load_floor()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_knitting_source = $('#cbo_knitting_source').val();
		var cbo_knitting_company = $('#cbo_knitting_company').val();
		var cbo_location_name = $('#cbo_location_name').val();
		if(cbo_knitting_source==1)
		{
			load_drop_down( 'requires/grey_production_entry_controller_v2',cbo_knitting_company+'_'+cbo_location_name, 'load_drop_down_floor', 'prod_floor_td' );
		}
		else
		{
			load_drop_down( 'requires/grey_production_entry_controller_v2',cbo_company_id+'_'+cbo_location_name, 'load_drop_down_floor', 'prod_floor_td' );
		}
	}
	
	function load_machine(machine_id=0)
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_knitting_source = $('#cbo_knitting_source').val();
		var cbo_knitting_company = $('#cbo_knitting_company').val();
		var cbo_floor_id = $('#cbo_floor_id').val();
		var cbo_receive_basis = $('#cbo_receive_basis').val();
		var program_no =0;
		if(cbo_receive_basis ==2)
		{
			program_no = $('#txt_booking_no').val();
		}
		if(cbo_knitting_source==1)
		{
			load_drop_down( 'requires/grey_production_entry_controller_v2',cbo_knitting_company+'_'+cbo_floor_id+'_'+machine_id+'_'+program_no, 'load_drop_machine', 'machine_td' );
		}
		else
		{
			load_drop_down( 'requires/grey_production_entry_controller_v2',cbo_company_id+'_'+cbo_floor_id+'_'+machine_id+'_'+program_no, 'load_drop_machine', 'machine_td' );
		}

	}

	function populate_operator()
	{
		var knitting_source = $('#cbo_knitting_source').val();
		if(knitting_source==1)
		{
			var cbo_company_id = $('#cbo_knitting_company').val();
			//load_drop_down( 'requires/grey_production_entry_controller_v2',cbo_company_id, 'load_drop_operator', 'operator_td' ); //me
		}
		/*else
		{
			var cbo_company_id = $('#cbo_company_id').val();
		}*/
		//var cbo_company_id = $('#cbo_company_id').val();
		var cbo_floor_id = $('#cbo_floor_id').val();
		
	}

	function load_operator()
	{
		var cbo_company_id = $('#cbo_knitting_company').val();
		var cbo_floor_name = $('#cbo_floor_id').val();
		//load_drop_down( 'requires/grey_production_entry_controller_v2',cbo_company_id+'_'+cbo_floor_name, 'load_drop_operator', 'operator_td' ); //me
	}

	function proces_costing_popup()
	{
		var cbo_company_id= $('#cbo_company_id').val();
		var knitting_charge_string= $('#knitting_charge_string').val();
		var txt_job_no= $('#txt_job_no').val();
		var recieve_basis = $('#cbo_receive_basis').val();
		var booking_id = $('#txt_booking_no_id').val();
		var txt_booking_no = $('#txt_booking_no').val();
		var fabric_description_id=$('#fabric_desc_id').val();
		var txt_receive_qnty=$('#txt_receive_qnty').val();
		var txt_receive_date=$('#txt_receive_date').val();
		var update_dtls_id=$('#update_dtls_id').val();
		var txt_service_booking=$('#txt_service_booking').val();
		var next_process_check=$('#next_process_check').val();
		var update_id=$('#update_id').val();
		var booking_without_order = $('#booking_without_order').val();
		if(recieve_basis==1 || recieve_basis==2)
		{
			if (form_validation('txt_booking_no*txt_receive_qnty*txt_receive_date','F. Booking/Knit Plan*Grey Prod. Qnty*Production Date')==false)
			{
				return;
			}
		}
		var title = 'Yarn Consumption Info';	
		var page_link='requires/grey_production_entry_controller_v2.php?recieve_basis='+recieve_basis+'&booking_id='+booking_id+'&txt_booking_no='+txt_booking_no+'&booking_without_order='+booking_without_order+'&fabric_description_id='+fabric_description_id+'&txt_receive_qnty='+txt_receive_qnty+'&txt_job_no='+txt_job_no+'&txt_service_booking='+txt_service_booking+'&txt_receive_date='+txt_receive_date+'&update_dtls_id='+update_dtls_id+'&update_id='+update_id+'&next_process_check='+next_process_check+'&cbo_company_id='+cbo_company_id+'&knitting_charge_string='+ knitting_charge_string+'&action=yarn_lot_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=870px,height=250px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theemail=this.contentDoc.getElementById("hidden_process_string").value;	 //Access form field with id="emailfield"
			var theename=this.contentDoc.getElementById("hidden_knitting_rate").value; //Access form field with id="emailfield"
			//alert(theemail)
			$('#knitting_charge_string').val(theename);
			$('#process_string').val(theemail);
			//alert(1);
			if(theename!="") 
			{
				var popup_value=theename.split("*");
				$("#cbo_yarn_count").val(popup_value[5]);
				set_multiselect('cbo_yarn_count','0','1',popup_value[5],'0');
				$("#txt_yarn_lot").val(popup_value[4]);
				$("#txt_brand").val(popup_value[6]);		
				$("#yarn_prod_id").val(popup_value[8]);		
			}
		}
	}
	
	function fn_knit_defect()
	{
		var mst_id = $("#update_id").val();
		var dtls_id=$('#update_dtls_id').val();
		var roll_maintained=$('#roll_maintained').val();
		var company_id=$('#cbo_company_id').val();
		if(dtls_id=="")
		{
			alert("Select Data First.");return;
		}
		else
		{
			var title = 'Knitting Defect Info';	
			var page_link='requires/grey_production_entry_controller_v2.php?update_dtls_id='+dtls_id+'&roll_maintained='+roll_maintained+'&company_id='+company_id+'&mst_id='+mst_id+'&action=knit_defect_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=500px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				
			}
		}
	}
	
	

	function store_load(location_id='')
	{
		//alert(location_id);
		var fstore_auto_update= $("#fabric_store_auto_update").val()*1;
		var knit_source= $("#cbo_knitting_source").val()*1;
		var cbo_knitting_company= $("#cbo_knitting_company").val();
		var cbo_company_id= $("#cbo_company_id").val();
		var roll_maintained= $("#roll_maintained").val();
		var company_localtion= $("#cbo_com_location_name").val();
		var knitt_location= $("#cbo_location_name").val();
		//alert(location_id+'='+fstore_auto_update+'='+knit_source);
		// var fstore_auto_update==2;
		if(fstore_auto_update==1) // Yes
		{
			// alert(knit_source);
			if(location_id != company_localtion)
			{
				//alert("Select Company first");
				return;
			}
			
			if(location_id>0 && cbo_company_id >0) // && knit_source==0
			{
				// alert(knit_source);return;
				//load_drop_down( 'requires/grey_production_entry_controller_v2', cbo_company_id+'_'+location_id, 'load_drop_down_store', 'store_td' );
				load_room_rack_self_bin('requires/grey_production_entry_controller_v2*13', 'store','store_td', cbo_company_id,location_id,'','','','','','','','132','V');
			}
		}
		else // No
		{
			// alert(fstore_auto_update+'='+location_id+'='+knitt_location);
			$('#cbo_store_name').val(0);
			$('#cbo_store_name').attr('disabled','disabled');return; // issue 14670
			//alert(knit_source);return;
			if(location_id != knitt_location)
			{
				//alert("Select Company first");
				return;
			}
			if(cbo_knitting_company > 0 && location_id>0)
			{
				load_room_rack_self_bin('requires/grey_production_entry_controller_v2*13', 'store','store_td', cbo_knitting_company,knitt_location,'','','','','','','','132','V');
				//disable_enable_fields('cbo_floor*cbo_room*txt_rack*txt_shelf',0);
			}

		}
		if (location_id==0) 
		{
			//$("#cbo_store_name").val(0);
			load_room_rack_self_bin('requires/grey_production_entry_controller_v2*13', 'store','store_td', 0,0,'','','','','','','','132','V');
		}

		$("#txt_service_booking").val('');
		
	}

	function generate_report_file(data,action)
	{
		window.open("requires/grey_production_entry_controller_v2.php?data=" + data+'&action='+action, true );
	}
	function fnc_rejection_challan()
	{
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_recieved_id').val()+'*'+$('#update_id').val()+'*'+$('#all_po_id').val(),'rejection_challan_print');
	}
	function out_bound_machine_load(source)
	{
		if(source ==3)
		{
			var cbo_receive_basis = $('#cbo_receive_basis').val();
			var program_no =0;
			if(cbo_receive_basis ==2)
			{
				program_no = $('#txt_booking_no').val();
			}

			load_drop_down( 'requires/grey_production_entry_controller_v2', $('#cbo_company_id').val()+'_'+program_no, 'load_drop_machine_out_bound', 'machine_td');
			$("#cbo_location_name").val(0);
			$("#cbo_location_name").attr('disabled','disabled');
		}
	}

	$('#txt_booking_no').live('keydown', function (e) 
	{
    	if (e.keyCode === 13)
		{
            if($("#txt_recieved_id").val() !="")
            {
            	return;
            }

            var cbo_receive_basis = $('#cbo_receive_basis').val();
			
    		e.preventDefault();
    		if(cbo_receive_basis==2)
    		{
    			var txt_booking_no = $('#txt_booking_no').val();
    			//console.log(txt_booking_no);

    			//var datam="***"+reponse[4]+"*********"+reponse[1];   for api print data checking
    			//var api_data=return_ajax_request_value("***2189519*********822387", "report_barcode_text_file_for_api", "requires/grey_production_entry_controller_v2");
    			//return;

    			var response = trim(return_global_ajax_value(cbo_receive_basis+'**'+txt_booking_no, 'populate_barcode_data', '', 'requires/grey_production_entry_controller_v2'));

    			 //console.log(response);
    			 var res=response.split("##");
    			 var theename=res[1]; //all data for Kintting Plan
    			 var theemail=res[0];	 //Knit Id for Kintting Plan
				
				 if(theemail=="Cancelled"){
					alert(theename);
					return;
				 }
				//var booking_without_order=0; 
				var recieve_basis=cbo_receive_basis;
    			var data=theename.split("**");

    			var cbo_company_id = data[25];
				company_wise_load(cbo_company_id);

				var booking_without_order = data[30];
				$('#txt_booking_no').val(theemail);
				$('#txt_booking_no_id').val(theemail);
				$('#booking_without_order').val(booking_without_order);
				$('#cbo_body_part').val(data[0]);
				$('#fabric_desc_id').val(data[1]);
				$('#txt_fabric_description').val(data[2]);
				$('#txt_gsm').val(data[3]);
				$('#txt_width').val(data[4]);
				$('#txt_job_no').val(data[5]);
				
				$('#all_po_id').val(data[7]);
				$('#cbo_knitting_source').val(data[8]);
				$('#txt_stitch_length').val(data[10]);
				$('#cbo_color_range').val(data[11]);
				$('#txt_brand').val(data[14]);
				$('#txt_color').val(data[15]);
				$('#color_id').val(data[16]);
				$('#txt_machine_dia').val(data[17]);
				$('#txt_machine_gg').val(data[18]);
				$('#within_group').val(data[19]);
				$('#req_no_id').val(data[21]);
				$('#body_part_type_id').val(data[24]);
				$('#cbo_company_id').val(data[25]);

				//load_machine(data[26]);
				//$("#cbo_machine_name").val(data[26]);
				$("#cbo_com_location_name").val(data[27]);

				$("#txt_receive_date").val(data[37]);
				
				//knitting_source
				if(data[8] == 3)
				{
					$('#txt_service_booking').attr('disabled',false);
				}

				//sales order
				if(data[23]==1)
				{
					//within_group
					if(data[31]==1)
					{
						$("#cbo_buyer_name option[value!='0']").remove();
						$("#cbo_buyer_name").append("<option selected value='"+data[34]+"'>"+data[35]+"</option>");
					}
					else
					{
						$("#cbo_buyer_name option[value!='0']").remove();
						$("#cbo_buyer_name").append("<option selected value='"+data[32]+"'>"+data[33]+"</option>");
					}
				}
				else
				{
					load_drop_down( 'requires/grey_production_entry_controller_v2',recieve_basis+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_buyer', 'buyer_td_id' );	
					$('#cbo_buyer_name').val(data[6]);
				}

				if($('#process_costing_maintain').val()*1!=1)
				{
					$('#txt_brand').val(data[14]);
					set_multiselect('cbo_yarn_count','0','1',data[12],'0');
					$('#txt_yarn_lot').val(data[13]);
					$('#yarn_prod_id').val(data[22]);
				}
				
				//load_drop_down( 'requires/grey_production_entry_controller_v2',data[8]+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_knitting_com','knitting_com');
				//$('#cbo_knitting_company').val(data[9]);

				// var cbo_knitting_company2 = $('#cbo_knitting_company').val();
				// load_drop_down( 'requires/grey_production_entry_controller_v2',cbo_knitting_company2, 'load_drop_down_location', 'location_td' );
				// if(data[8]==1)
				// {
				// 	$("#cbo_location_name").val(data[36]);
				// }
				
				//inisialize service mandatory or not
				$("#service_booking_td_text").css('color', 'black');
				$("#is_service_booking_mandatory").val("0");

				var auto_barcode_generate = $("#auto_barcode_generate").val();
				
				//Knitting Plan
				if(recieve_basis==2)
				{
					$('#cbo_buyer_name').attr('disabled','disabled');
					get_php_form_data(theemail+'_'+cbo_company_id, "populate_data_from_booking2", "requires/grey_production_entry_controller_v2" );
					$("#cbo_knitting_source").attr("disabled",'true');
					$("#cbo_knitting_company").attr("disabled",'true');
					//$("#cbo_location_name").attr("disabled",'true');

					if($("#cbo_knitting_source").val() ==3 && $("#is_service_booking_mandatory").val() ==1 && booking_without_order != 1)
					{
						$("#service_booking_td_text").css('color', 'blue');//addClass("must_entry_caption");
					}

					//load_floor();
					//load_operator();
					//out_bound_machine_load($("#cbo_knitting_source").val());
					store_load(data[27]);

				}
				//$("#cbo_floor_id").val(data[28]);
				$("#txt_shift_name").val(data[29]);

				if($('#process_costing_maintain').val()*1==1)
				{
					disable_enable_fields('cbo_com_location_name*cbo_company_id*cbo_com_location_name*txt_receive_date*cbo_location_name*txt_service_booking*txt_gsm*txt_width*txt_shift_name*txt_stitch_length*cbo_floor_id*txt_color*cbo_color_range*txt_brand*cbo_uom*cbo_floor*cbo_room*txt_rack*txt_shelf*txt_roll_no',1); //cbo_store_name
					//length*cbo_machine_name
				}
				else
				{
					disable_enable_fields('cbo_com_location_name*cbo_company_id*cbo_com_location_name*txt_receive_date*cbo_location_name*txt_service_booking*txt_gsm*txt_width*txt_shift_name*txt_stitch_length*cbo_floor_id*txt_color*txt_yarn_lot*cbo_color_range*show_textcbo_yarn_count*txt_brand*cbo_uom*cbo_floor*cbo_room*txt_rack*txt_shelf*txt_roll_no',1); //cbo_store_name
					//length*cbo_machine_name
				}

				if(auto_barcode_generate == 2)
				{
					$('#cbo_floor_id').removeAttr("disabled");
					openmypage_po();
				}
				else
				{

					$.post( "requires/grey_production_entry_controller_v2.php", { machine_id: $("#cbo_machine_name").val(), action: "load_weight_machine_data"})
					.done(function( data ) {
						var obj = JSON.parse(data);
						if (obj != null && obj !== undefined) 
						{
							var api_weight=obj.weight; 
							var id_card_no=obj.operator;
							var operatorInfo = trim(return_global_ajax_value(id_card_no+'_'+$("#cbo_machine_name").val(), 'load_operator_name', '', 'requires/grey_production_entry_controller_v2'));

							if(operatorInfo == 0){
								alert("Operator not found");
								fnc_reset();
								return;
							}
							var operatorInfoArr=operatorInfo.split("**");

							var knit_operator=operatorInfoArr[0];
							var knit_operator_name=operatorInfoArr[1];
							var pipe_weight=operatorInfoArr[2]*1;
							//reducing machine pipe weight 
							api_weight = api_weight-pipe_weight;

							$("#txt_operator_id").val(knit_operator);
							$("#txt_operator_name").val(knit_operator_name);
							$("#txt_operator_name").attr("disabled",'true');
							get_po_popup_data(api_weight);
						}else{
							alert("weight not found");
							fnc_reset();
							return;
						}
					});
				}
    		}
    		
    	}
    });

	function get_po_popup_data(api_weight)
	{
		var receive_basis=$('#cbo_receive_basis').val();
		var booking_no=$('#txt_booking_no').val();
		var booking_id=$('#txt_booking_no_id').val();
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_com_location_name = $('#cbo_com_location_name').val();
		var dtls_id = $('#update_dtls_id').val();
		var roll_maintained = $('#roll_maintained').val();
		var fabric_store_auto_update = $('#fabric_store_auto_update').val();
		var store_update_upto = $('#store_update_upto').val();
		var cbo_store_name = $('#cbo_store_name').val();
		if(fabric_store_auto_update==1 && store_update_upto>0 )
		{
			if(cbo_company_id == 0)
			{
				alert("Company Select First");
				$('#cbo_company_id').focus();
				return;
			}
			if(cbo_com_location_name == 0)
			{
				alert("Company Location Select First");
				$('#cbo_com_location_name').focus();
				return;
			}
			if(cbo_store_name == 0)
			{
				alert("Store Select First");
				$('#cbo_store_name').focus();
				return;
			}
		}
		
		var barcode_generation = $('#barcode_generation').val();
		var production_control = $('#production_control').val();
		var save_data_stylewise = $('#save_data_stylewise').val();
		var all_po_id = $('#all_po_id').val();
		var txt_receive_qnty = $('#txt_receive_qnty').val(); 
		var txt_reject_fabric_recv_qnty = $('#txt_reject_fabric_recv_qnty').val(); 
		var distribution_method = $('#distribution_method_id').val();
		var booking_without_order = $('#booking_without_order').val();
		
		var cbo_body_part=$('#cbo_body_part').val();
		var txt_fabric_description=$('#txt_fabric_description').val();
		var txt_gsm=$('#txt_gsm').val();
		var txt_width=$('#txt_width').val();
		var fabric_desc_id=$('#fabric_desc_id').val();
		var txt_deleted_id=$('#txt_deleted_id').val();

		if((receive_basis==1 || receive_basis==2) && booking_no=="")
		{
			alert("Please Select Booking No. / Knit Plan");
			$('#txt_booking_no').focus();
			return false;
		}
		else if((receive_basis==1 || receive_basis==2 || receive_basis==4) && txt_fabric_description=="")
		{
			alert("Please Select Fabric Description.");
			$('#txt_fabric_description').focus();
			return false;
		}
		else if(receive_basis==0 && cbo_company_id==0)
		{
			alert("Please Select Company.");
			$('#cbo_company_id').focus();
			return false;
		}
		
		var prev_distribution_method =distribution_method;

		get_php_form_data(receive_basis+'**'+cbo_company_id+'**'+booking_no+'**'+dtls_id+'**'+all_po_id+'**'+roll_maintained+'**'+production_control+'**'+barcode_generation+'**'+save_data_stylewise+'**'+txt_receive_qnty+'**'+txt_receive_qnty_pcs+'**'+prev_distribution_method+'**'+cbo_body_part+'**'+txt_gsm+'**'+txt_width+'**'+fabric_desc_id+'**'+txt_deleted_id+'**'+txt_reject_fabric_recv_qnty+'**'+booking_without_order+'**'+booking_id+'**'+cbo_com_location_name+'**'+cbo_store_name+'**'+fabric_store_auto_update+'**'+store_update_upto+'**'+api_weight, "populate_data_from_po_popup_stylewise", "requires/grey_production_entry_controller_v2" );
		
		
		//Saving data
		fnc_grey_production_entry(0);
	}


	function company_wise_load(company_id) 
	{
		get_php_form_data( company_id,'company_wise_load' ,'requires/grey_production_entry_controller_v2');
		//get_php_form_data(company_id,'weight_machine_set_api', 'requires/grey_production_entry_controller_v2');
	}

	$('#txt_operator_name').live('keydown', function (e) 
	{
    	if (e.keyCode === 13)
		{
            var operator_code = $('#txt_operator_name').val();
            var cbo_knitting_company = $('#cbo_knitting_company').val();
			var cbo_location_name = $('#cbo_location_name').val();

            var response = trim(return_global_ajax_value(operator_code+'_'+cbo_knitting_company+'_'+cbo_location_name, 'populate_operator_name', '', 'requires/grey_production_entry_controller_v2'));
			if(response=="")
			{
				alert("Operator Not Found");
				$('#txt_operator_name').val("");
			}
			else
			{
				var data=response.split("##");
				var operator_id=data[0];
				var	operator_name=data[1];
				$('#txt_operator_name').val(operator_name);
				$('#txt_operator_id').val(operator_id);
			}
        }
    });

	function change_machine(machine_no)
	{
		get_php_form_data(machine_no, "load_production_floor", "requires/grey_production_entry_controller_v2" );
	}

	function fnc_reset()
	{
		var auto_barcode_generate = $('#auto_barcode_generate').val();
		if(auto_barcode_generate !=2)
		{
			reset_form('greyproductionentry_1','list_container_knitting*list_fabric_desc_container*roll_details_list_view*list_program_wise_fabric_desc_container','','cbo_receive_basis,2','disable_enable_fields(\'cbo_company_id\');set_receive_basis();','txt_receive_date');
			$("txt_booking_no").focus();
			//window.location.reload();
			$("txt_receive_date").val("<? echo date('d-m-Y');?>");
		}
	}

</script>
<body onLoad="set_hotkey()">
	<div style="width:100%;">
		<? echo load_freeze_divs ("../",$permission); ?>
		<form name="greyproductionentry_1" id="greyproductionentry_1">
			<div style="width:950px; float:left;" align="center">        
				<fieldset style="width:950px">
					<legend>Knitting Production Entry</legend>
					<fieldset style="width:930px">
						<table cellpadding="0" cellspacing="2" width="100%" id="master_table">
							<tr>
								<td align="right" colspan="3"><strong>Production ID</strong></td>
								<td>
									<input type="hidden" name="update_id" id="update_id" />
									<input type="text" name="txt_recieved_id" id="txt_recieved_id" class="text_boxes" style="width:145px" placeholder="Double Click" onDblClick="grey_receive_popup();" >
								</td>
							</tr>
							<tr>
								<td width="110" class="must_entry_caption">Knit Plan</td>
								<td width="150">
									<input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes_numeric" style="width:140px"  placeholder="Double Click to Search" onDblClick="openmypage_fabricBooking();" >
									<input type="hidden" name="txt_booking_no_id" id="txt_booking_no_id" class="text_boxes">
									<input type="hidden" name="booking_without_order" id="booking_without_order"/>
									<input type="hidden" name="req_no_id" id="req_no_id"/>
									<input type="hidden" name="next_process_check" id="next_process_check"/>
								</td>
								<td width="110" class="must_entry_caption"> Company </td>
								<td width="150">
									<? 

									echo create_drop_down( "cbo_company_id", 151, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond $company_credential_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "company_wise_load(this.value)" );
									?>
								</td>
								
								<td width="110" class="must_entry_caption"> Production Date </td>
								<td width="150">
									<input class="datepicker" type="text" style="width:140px" name="txt_receive_date" id="txt_receive_date" value="<? echo date("d-m-Y");?>" readonly/>
								</td>
							</tr> 
							<tr>
                            	<td class="must_entry_caption">Company Location</td>
								<td id="com_location_td">
									<?
									echo create_drop_down("cbo_com_location_name",150,$blank_array,"", 1, "-- Select --", 0,"",0,'');
									?>
								</td>

								<td width="110"> Production Basis </td>
								<td width="150" id="receive_baisis_td">
									<?									
									$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan",4=>"Sales Order");
									echo create_drop_down("cbo_receive_basis",152,$receive_basis,"",0,"",2,"set_receive_basis();",1);
									?>
								</td>
								
                                <td> Receive Challan No </td>
								<td>
									<input type="text" name="txt_receive_chal_no" id="txt_receive_chal_no" class="text_boxes" style="width:140px" >
								</td>
							</tr>
							<tr>
								<td class="must_entry_caption"> Knitting Source </td>
								<td>
									<?
									//store_load(1);
									echo create_drop_down("cbo_knitting_source",150,$knitting_source,"", 1, "-- Select --", 0,"load_drop_down( 'requires/grey_production_entry_controller_v2', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_knitting_com','knitting_com');out_bound_machine_load(this.value);",0,'1,3');
									?>
								</td>
								<td class="must_entry_caption">Knitting Com</td>
								<td id="knitting_com">
									<?
									//load_drop_down( 'requires/grey_production_entry_controller_v2', this.value, 'load_drop_down_floor', 'prod_floor_td' );
									echo create_drop_down( "cbo_knitting_company", 152, $blank_array,"",1, "--Select Knit Company--", 1, "" );
									?>
								</td>
								<td class="must_entry_caption">Location </td>                                              
								<td id="location_td">
									<? 
									//load_drop_down( 'requires/grey_production_entry_controller_v2', this.value+'_'+document.getElementById('cbo_knitting_company').value, 'load_drop_down_floor', 'prod_floor_td' );
									//load_drop_down( 'requires/grey_production_entry_controller_v2', this.value, 'load_drop_machine', 'machine_td');
									echo create_drop_down( "cbo_location_name", 152, $blank_array,"", 1, "-- Select Location --", 0, "" );
									?>
								</td>
							</tr>
							<tr>
								<td>Yarn Issue Ch. No</td>                                              
								<td> 
									<input type="text" name="txt_yarn_issue_challan_no" id="txt_yarn_issue_challan_no" placeholder="Browse" onDblClick="issue_challan_no();" class="text_boxes" style="width:140px" onBlur="fnc_check_issue(this.value);">
								</td> 
								<td>Yarn Issued </td>
								<td>
									<input type="text" name="txt_yarn_issued" id="txt_yarn_issued" class="text_boxes_numeric" style="width:140px" readonly>
								</td> 
								<td>Job No</td>
								<td>
									<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:140px" readonly>
								</td>
							</tr>
							<tr>
								<td>Buyer</td>
								<td id="buyer_td_id"> 
									<?
									echo create_drop_down( "cbo_buyer_name", 152, $blank_array,"", 1, "-- Select Buyer --", 0, "",1 );
									?>
								</td>
								<td id="service_booking_td_text">Service Booking Based </td>                                              
								<td> 
									<input type="text" name="txt_service_booking" id="txt_service_booking" class="text_boxes" style="width:140px" placeholder="Double Click to Search" onDblClick="openmypage_serviceBooking(2);" readonly disabled>
									<input type="hidden" name="service_booking_without_order" id="service_booking_without_order" class="text_boxes" style="width:40px">
									<input type="hidden" name="service_booking_id" id="service_booking_id">

								</td> 

								<td id="store_th"> Store Name </td>
								<td id="store_td">
									<?
									echo create_drop_down( "cbo_store_name", 152, $blank_array,"",1, "--Select Store--", 1, "",'' );
									?>
								</td>                                                               
								
							</tr>   
							<tr>
								<td>Remarks </td>                                              
								<td> 
									<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:140px">
								</td> 
                                <td>Sub-Subcontract</td>
								<td id="subcontract_td_id"> 
									<?
									echo create_drop_down( "cbo_sub_contract", 152, $blank_array,"", 1, "-- Select subcontract --", 0, "" );
									?>
								</td>
							</tr>  
						</table>
					</fieldset>
					<table cellpadding="0" cellspacing="1" width="935" border="0" id="details_table">
						<tr>
							<td width="64%" valign="top">
								<fieldset>
									<legend>New Entry
										<div style="float: right; color: red;" id="qty_statistic">
											<span id="td_title">Allowed Qty. <span id="allowedQty"> = 0.00</span></span> &nbsp;|&nbsp;
											<input type="hidden" id="allowedQtyTotal" class="text_boxes" style="width:40px" />
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
                                                <input type="hidden" name="body_part_type_id" id="body_part_type_id" class="text_boxes">
											</td>
										</tr>
										<tr>
											<td class="must_entry_caption">GSM</td>
											<td>
												<input type="text" name="txt_gsm" id="txt_gsm" class="text_boxes_numeric" style="width:120px;"  />	
											</td>
											<td>Brand</td>
											<td>
												<input type="text" name="txt_brand" id="txt_brand" class="text_boxes" style="width:120px" /> 
											</td> 
										</tr> 
										<tr>
											<td class="must_entry_caption">Dia / Width</td>
											<td>
												<input type="text" name="txt_width" id="txt_width" class="text_boxes" style="width:120px;text-align:right;" />	
											</td>
											<td>Shift Name</td>
											<td>
												<? 
												echo create_drop_down( "txt_shift_name", 132, $shift_name,"", 1, "-- Select Shift --", 0, "",'' );
												?>	
											</td> 
										</tr>
										<tr>
											<td class="must_entry_caption">Stitch Length</td>
											<td>
												<input type="text" name="txt_stitch_length" id="txt_stitch_length" class="text_boxes" style="width:120px;"/>
											</td>
											
											<td>M/C Gauge</td>
											<td>
												<input type="text" name="txt_machine_gg" id="txt_machine_gg" class="text_boxes" style="width:120px;"/>
											</td> 
										</tr>
										<tr>
											<td>M/C Dia</td>
											<td>
												<input type="text" name="txt_machine_dia" id="txt_machine_dia" class="text_boxes_numeric" style="width:120px;"/>
											</td>
											<td class="must_entry_caption">Prod. Floor</td>
											<td id="prod_floor_td">
												<? echo create_drop_down( "cbo_floor_id", 132, $blank_array,"", 1, "-- Select Floor --", 0, "",0 ); ?>
											</td>
											
										</tr>
										<tr>
											<td>No of Roll</td>
											<td>
												<input type="text" name="txt_roll_no" id="txt_roll_no" class="text_boxes_numeric" style="width:120px" />
											</td>
											
											<td>Machine No.</td>
											<td id="machine_td">
												<? echo create_drop_down( "cbo_machine_name", 132, $blank_array,"", 1, "-- Select Machine --", 0, "",0 ); ?>
											</td>
											
										</tr>
										<tr>
											<td class="must_entry_caption">Grey Prod. Qnty</td>
											<td>
												<input type="text" name="txt_receive_qnty" id="txt_receive_qnty" class="text_boxes_numeric" style="width:60px;" onClick="openmypage_po()" placeholder="Single Click" readonly/>	
												<input type="text" name="txt_receive_qnty_pcs" id="txt_receive_qnty_pcs" class="text_boxes_numeric" style="width:50px;"  placeholder="Pcs" readonly/>	
											</td>
											<td>Operator</td>
											<td>
												<input type="text" name="txt_operator_name" id="txt_operator_name" placeholder="Browse" onDblClick="fnc_operator_name();" class="text_boxes" style="width:120px" >
												<input type="hidden" name="txt_operator_id" id="txt_operator_id" class="text_boxes" style="width:120px" disabled="disabled">
											</td>
										</tr>
										<tr>
											<td>Fabric Color</td>
											<td>
												<input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:120px;" placeholder="Browse" onDblClick="openmypage_color();" readonly/>
												<input type="hidden" name="color_id" id="color_id" />
											</td>
                                            <td>Store Floor</td>
                                            <td id="floor_td">
												<? echo create_drop_down( "cbo_floor", 132,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
										</tr> 
										<tr>
											<td class="must_entry_caption">Color Range</td>
											<td>
												<?
												echo create_drop_down( "cbo_color_range", 132, $color_range,"",1, "-- Select --", 0, "" );
												?>
											</td>
                                            <td>Room</td>
											<!--<td>
												<input type="text" name="txt_room" id="txt_room" class="text_boxes_numeric" style="width:120px">
											</td>-->
                                            <td id="room_td">
												<? echo create_drop_down( "cbo_room", 132,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
										</tr>
										<tr>
											<td>Reject Fabric Receive</td>
											<td>
												<input type="text" name="txt_reject_fabric_recv_qnty" id="txt_reject_fabric_recv_qnty" class="text_boxes_numeric" style="width:120px;" placeholder="Display" readonly />	
											</td>
                                            <td>Rack</td>
											<!--<td>
												<input type="text" name="txt_rack" id="txt_rack" class="text_boxes" style="width:120px">
											</td>-->
                                            <td id="rack_td">
												<? echo create_drop_down( "txt_rack", 132,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
										</tr>
										<tr>
											<td>Yarn Lot</td>
											<td>
												<input type="text" name="txt_yarn_lot" id="txt_yarn_lot" class="text_boxes" style="width:120px"  />
												<input type="hidden" name="yarn_prod_id" id="yarn_prod_id" readonly />
											</td>
                                            <td>Shelf</td>
											<td id="shelf_td">
												<? echo create_drop_down( "txt_shelf", 132,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
											<!--<td>
												<input type="text" name="txt_self" id="txt_self" class="text_boxes_numeric" style="width:120px">
											</td>-->
											<td style="display:none">Bin/Box</td>
											<td style="display:none">
												<input type="text" name="txt_binbox" id="txt_binbox" class="text_boxes_numeric" style="width:120px">
											</td>
											
										</tr>
										<tr>
											<td>Yarn Count</td>
											<td>
												<?
												echo create_drop_down("cbo_yarn_count",132,"select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count","id,yarn_count",0, "-- Select --", $selected, "");
												?>
											</td>
											<td>Size</td>
											<td>
												<input type="text" name="txt_coller_cuff_size" id="txt_coller_cuff_size" class="text_boxes" style="width:120px" disabled="disabled">
											</td>
											
										</tr>
										<tr>
											<td colspan="4" align="right">&nbsp;</td>
										</tr>
										<tr>
											<td colspan="4" align="right"><input style="display: none;" type="button" id="knit_defect" name="knit_defect" class="formbuttonplasminus" style="width:200px;" value="QC Result" onClick="fn_knit_defect()" ></td>
										</tr>
									</table>
								</fieldset>
							</td>                    
							<td width="1%" valign="top"></td>
							<td width="35%" valign="top">
							<div id="roll_details_list_view"></div>
                    </td>                       
                </tr>
                <tr>
                	<td colspan="6">&nbsp;</td>
                </tr>
                <tr>
                	<td colspan="6" align="center" class="button_container">
                		<? 
                            echo load_submit_buttons($permission, "fnc_grey_production_entry", 0,0,"reset_form('greyproductionentry_1','list_container_knitting*list_fabric_desc_container*roll_details_list_view*list_program_wise_fabric_desc_container','','cbo_receive_basis,2','disable_enable_fields(\'cbo_company_id\');set_receive_basis();')",1);//set_auto_complete(1);
                            ?>
                            <input type="button" name="print" id="print" value="Rej.Challan" class="formbutton" style=" width:100px; margin-right: 100px;" onClick="fnc_rejection_challan()" >
                            <input type="hidden" name="save_data" id="save_data" readonly>
                            <input type="hidden" name="save_data_stylewise" id="save_data_stylewise" readonly>
                            <input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
                            <input type="hidden" name="update_trans_id" id="update_trans_id" readonly>
                            <input type="hidden" name="previous_prod_id" id="previous_prod_id" readonly>
                            <input type="hidden" name="hidden_receive_qnty" id="hidden_receive_qnty" readonly>
                            <input type="hidden" name="hidden_receive_rate" id="hidden_receive_rate" readonly>
                            <input type="hidden" name="all_po_id" id="all_po_id" readonly>
                            <input type="hidden" name="roll_maintained" id="roll_maintained" readonly>
                            <input type="hidden" name="barcode_generation" id="barcode_generation" readonly>
                            <input type="hidden" name="fabric_store_auto_update" id="fabric_store_auto_update" readonly>
                            <input type="hidden" name="store_update_upto" id="store_update_upto" readonly>
                            <input type="hidden" name="distribution_method_id" id="distribution_method_id" readonly />
                            <input type="hidden" name="txt_deleted_id" id="txt_deleted_id" readonly />
                            <input type="hidden" name="process_costing_maintain" id="process_costing_maintain" readonly>
                            <input type="hidden" name="process_string" id="process_string"/>
                            <input type="hidden" name="knitting_charge_string" id="knitting_charge_string"/>
                            <input type="hidden" name="production_control" id="production_control"/>
                            <input type="hidden" name="within_group" id="within_group"/>
                            <input type="hidden" name="previous_receive_qnty" id="previous_receive_qnty"/>
                            <input type="hidden" name="required_qnty" id="required_qnty"/>
                            <input type="hidden" name="previous_book_plan_production" id="previous_book_plan_production"/>
                            <input type="hidden" name="is_service_booking_mandatory" id="is_service_booking_mandatory"/>
                            <input type="hidden" name="auto_barcode_generate" id="auto_barcode_generate"/>
                        </td>	  
                    </tr>
                </table>
                <div style="width:930px;" id="list_container_knitting"></div>
            </fieldset>
        </div>  
        <div style="width:10px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
        <div id="list_fabric_desc_container" style="max-height:500px; width:340px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
        <div id="list_program_wise_fabric_desc_container" style="max-height:500px; width:340px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
    </form>
</div>
</body>
<script>
	set_multiselect('cbo_yarn_count','0','0','','0');
</script>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>