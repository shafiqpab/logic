<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create for PI entry
Functionality	:
JS Functions	:
Created by		:	Fuad Shahriar
Creation date 	: 	22-06-2016
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
$menu_id=$_SESSION['menu_id'];
$user_id=$_SESSION['logic_erp']['user_id'];
$userCredentialCategory = sql_select("SELECT item_cate_id as ITEM_CATE_ID FROM user_passwd where id=$user_id");
$item_cate_id = $userCredentialCategory[0]['ITEM_CATE_ID'];
if($item_cate_id !='') {
	$item_category_credential_cond = $item_cate_id ;
}
else
{
	$item_category_credential_cond = implode(",",array_keys($item_category));
}
$independent_control_arr = return_library_array( "select company_name, independent_controll from variable_settings_inventory where variable_list=20 and menu_page_id=$menu_id and status_active=1 and is_deleted=0",'company_name','independent_controll');
if($independent_control_arr=="")
{
	$independent_control_arr=array();
}

$good_receive_data_source_arr = return_library_array( "select company_name, export_invoice_qty_source from variable_settings_commercial where variable_list=23 and status_active=1 and is_deleted=0",'company_name','export_invoice_qty_source');
if($good_receive_data_source_arr=="")
{
	$good_receive_data_source_arr=array();
}

$color_from_library_arr = return_library_array( "select company_name, color_from_library from variable_order_tracking where status_active=1 and is_deleted=0 and variable_list=23",'company_name','color_from_library');
if($color_from_library_arr=="")
{
	$color_from_library_arr=array();
}
$color_sql=sql_select("select id, color_name from lib_color where status_active=1 and is_deleted=0 order by color_name");
$i=1;
foreach($color_sql as $row)
{
	if($row[csf("color_name")]!="")
	{
		$color_array[$row[csf("color_name")]]=$row[csf("id")];
	}
}
//$general_item_category
$select_item_arr=array(5=>"Chemicals",6=>"Dyes",7=>"Auxilary Chemicals",23=>"Dyes Chemicals & Auxilary Chemicals",101=>"Raw Material",106=>"Embroidery Material");
$select_item_arrs=$select_item_arr+$general_item_category;

$item_category_without_general=array_diff($item_category,$general_item_category);
$genarel_item_arr=array(4=>"Accessories",8=>"General Item");
$item_category_with_gen=$item_category_without_general+$genarel_item_arr;
ksort($item_category_with_gen);

//echo implode('*',$_SESSION['logic_erp']['mandatory_field'][405]).'say1';
//echo implode('*',$_SESSION['logic_erp']['mandatory_message'][405]).'sys2';
//print_r($select_item_arrs);//die;
//------------------------------------------------------------------------------------------------
echo load_html_head_contents("Pro Forma Invoice", "../../", 1, 1,'','','');

// echo $data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][405]);die;
?>

<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';
	var mandatory_field_arr="";
	<?
	// $data_arr=json_encode(array());
	if($_SESSION['logic_erp']['data_arr'][405]!="")
	{
		$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][405] );
		echo "var field_level_data= ". $data_arr . ";\n";
		 
	}
	
	if($_SESSION['logic_erp']['mandatory_field'][405]!="")
	{
		$mandatory_field_arr= json_encode( $_SESSION['logic_erp']['mandatory_field'][405] );
		echo "var mandatory_field_arr= ". $mandatory_field_arr . ";\n";

		// condition for txt internal File No
		$temp_mandatory_field_arr = $_SESSION['logic_erp']['mandatory_field'][405];
		unset($temp_mandatory_field_arr[3]);
		$temp_mandatory_message_arr = $_SESSION['logic_erp']['mandatory_message'][405];
		unset($temp_mandatory_message_arr[3]);
	}	
	
	$js_color_array= json_encode($color_array);
	echo "var js_colors_array = ". $js_color_array . ";\n";	
	?>
	
	var str_color = [<? echo substr(return_library_autocomplete( "select distinct(color_name) from lib_color", "color_name"  ), 0, -1); ?>];
	var str_size = [<? echo substr(return_library_autocomplete( "select distinct(size_name) from lib_size", "size_name"  ), 0, -1); ?>];
	var str_composition = [<? echo substr(return_library_autocomplete( "select distinct(fabric_composition) from com_pi_item_details", "fabric_composition"  ), 0, -1); ?>];
	var str_construction = [<? echo substr(return_library_autocomplete( "select distinct(fabric_construction) from com_pi_item_details", "fabric_construction"  ), 0, -1); ?>];
	var str_dia_width = [<? echo substr(return_library_autocomplete( "select distinct(dia_width) from com_pi_item_details", "dia_width"  ), 0, -1); ?>];
	
	$('#pi_number').change(function(e) {
        var c = String.fromCharCode(e.which);
		alert(c);
    });
	
	
	
	/*$('#pi_number').blur(function(e) {
		var c = String.fromCharCode(e.which);
		alert(c);
		var allowed = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890.-,%@!/\<>?[]{};: '; // ~ replace of Hash(#)()
		if (e.which != 8 && e.which !=0 && allowed.indexOf(c) < 0)
		{
			alert(1);
			return false;
		}
		else
		{
			alert(2);
		}
	});*/
	
	function fnc_pi_mst( operation )
	{ 
		var update_id=$('#update_id').val();
		var cbo_importer_id=$('#cbo_importer_id').val();
		var cbo_item_category_id=$('#cbo_item_category_id').val();
		var cbo_goods_rcv_status=$('#cbo_goods_rcv_status').val();
		var cross_check_activity_status=$('#cross_check_activity_status').val();
		var export_pi_id=$("#export_pi_id").val();
		var is_approved=$('#is_approved').val();//Chech The Approval requisition item.. Change not allowed
		var cbo_pi_basis_id=$('#cbo_pi_basis_id').val(); //Chech basis and rcv status
		var entry_form='';
		//alert(cbo_item_category_id +"="+ cbo_goods_rcv_status);return;
		if((cbo_item_category_id==74 || cbo_item_category_id==102 || cbo_item_category_id==103 || cbo_item_category_id==104)  && export_pi_id=="")
		{
			alert("This Category Not Allow Without Export PI");return;
		}
		
		// cbo_item_category_id==12 || cbo_item_category_id==25 || 
		if((cbo_item_category_id==102 || cbo_item_category_id==103 || cbo_item_category_id==104 || cbo_item_category_id==31 || cbo_item_category_id==115) && cbo_goods_rcv_status==1)
		{
			alert("After Goods Receive Status Not Allow For This Category");return;
		}

		var good_rece_data_source_arr = JSON.parse('<? echo json_encode($good_receive_data_source_arr); ?>');
		if(cbo_item_category_id==25 && cbo_goods_rcv_status==1 && good_rece_data_source_arr[cbo_importer_id]!=1)
		{
			alert("After Goods Receive Status Only Allow For Varriable Setting After Good Receive Data Source always Work Order This Category.");return;
		}
		  
		if(operation!=4)
		{
			if(is_approved==1 || is_approved==3)
			{
				alert("PI is Approved. So Change Not Allowed");
				return;
			}
		}
		
		if(cbo_pi_basis_id==2 && cbo_goods_rcv_status==1)
		{
			alert("Goods Rcv Status (After Goods Rcv) Not Allow For PI Basis (Independent)");
			return;
		}

		if(operation==4)
		{
			if($('#txt_system_id').val()=="")
			{
				alert("Please Save First.");
				return;
			}
			if( cbo_item_category_id == "1")
			{
				 entry_form = "165";
			}
			else if( cbo_item_category_id == "2" ||  cbo_item_category_id == "3" ||  cbo_item_category_id == "13" ||  cbo_item_category_id == "14")
			{
				 entry_form = "166";
			}
			else if( cbo_item_category_id == "4")
			{
				 entry_form = "167";
			}
			else if( cbo_item_category_id == "12")
			{
				 entry_form = "168";
			}
			else if( cbo_item_category_id == "24")
			{
				 entry_form = "169";
			}
			else if( cbo_item_category_id == "25" || cbo_item_category_id == "102" || cbo_item_category_id == "103")
			{
				 entry_form = "170";
			}
			else if( cbo_item_category_id == "30")
			{
			    entry_form = "197";
			}
			else if( cbo_item_category_id == "31")
			{
				entry_form = "171";
			}
			else if( cbo_item_category_id == "5" ||  cbo_item_category_id == "6" ||  cbo_item_category_id == "7" ||  cbo_item_category_id == "23")
			{
				entry_form = "227";
			}
			else
			{
				entry_form = "172";
			}
			// alert(entry_form); // 167
			// alert(cbo_item_category_id); // 4 pi_mst_file
			// print_report( $('#cbo_importer_id').val()+'*'+$('#update_id').val()+'*'+entry_form, "print", "requires/pi_print_urmi" );
			print_report( $('#cbo_importer_id').val()+'*'+$('#update_id').val()+'*'+entry_form+'*'+cbo_item_category_id, "print", "requires/pi_print_urmi" );
			return;
		}
		
		var cbo_pi_for=$("#cbo_pi_for").val();
		if(cbo_pi_for!=2){
			if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][405]); ?>' && cbo_pi_for != 2) // 2=Margin LC
			{
				if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][405]); ?>','<? echo implode('*',$_SESSION['logic_erp']['mandatory_message'][405]); ?>')==false) {return;}
			}
			else if('<? echo implode('*',$temp_mandatory_field_arr); ?>') 
			{			
				if (form_validation('<? echo implode('*',$temp_mandatory_field_arr); ?>','<? echo implode('*',$temp_mandatory_message_arr); ?>')==false) {return;}
			}
		}
		
		 
		var allowed_char = document.getElementById('pi_number').value;
		var outputVal = allowed_char.replace(/[^0-9a-zA-Z.,-;: %!<>?\[\]\\]/g,"");       
		if (allowed_char != outputVal) {
			alert('Please enter a valid text in PI number Field.');
			$('#pi_number').focus();
			return false;
		}
		
		if (form_validation('cbo_item_category_id*cbo_importer_id*cbo_supplier_id*pi_number*pi_date*cbo_currency_id*cbo_source_id*cbo_pi_basis_id*cbo_goods_rcv_status','Item Category*Importer*Supplier*Pi Number*Pi Date*Currency*Source*Pi Basis*Goods Rcv Status')==false)
		{
			return;
		}
		else
		{

			if(export_pi_id>0)
			{
				var row_num=$('#tbl_pi_item tbody tr').length;
				var data_all="";
				if(cbo_item_category_id==2)
				{
					for (var j=1; j<=row_num; j++)
					{
						data_all+="&workOrderNo_" + j + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + j + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + j + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&construction_" + j + "='" + $('#construction_'+j).val()+"'"+"&composition_" + j + "='" + $('#composition_'+j).val()+"'"+"&colorId_" + j + "='" + $('#colorId_'+j).val()+"'"+"&gsm_" + j + "='" + $('#gsm_'+j).val()+"'"+"&diawidth_" + j + "='" + $('#diawidth_'+j).val()+"'"+"&uom_" + j + "='" + $('#uom_'+j).val()+"'"+"&quantity_" + j + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + j + "='" + $('#rate_'+j).val()+"'"+"&amount_" + j + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + j + "='" + $('#updateIdDtls_'+j).val()+"'"+"&hideDeterminationId_" + j + "='" + $('#hideDeterminationId_'+j).val()+"'";
					}
				}
				if(cbo_item_category_id==3)
				{
					for (var j=1; j<=row_num; j++)
					{
						data_all+="&workOrderNo_" + j + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + j + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + j + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&construction_" + j + "='" + $('#construction_'+j).val()+"'"+"&composition_" + j + "='" + $('#composition_'+j).val()+"'"+"&colorId_" + j + "='" + $('#colorId_'+j).val()+"'"+"&gsm_" + j + "='" + $('#fabWeight_'+j).val()+"'"+"&diawidth_" + j + "='" + $('#diawidth_'+j).val()+"'"+"&uom_" + j + "='" + $('#uom_'+j).val()+"'"+"&quantity_" + j + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + j + "='" + $('#rate_'+j).val()+"'"+"&amount_" + j + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + j + "='" + $('#updateIdDtls_'+j).val()+"'"+"&hideDeterminationId_" + j + "='" + $('#hideDeterminationId_'+j).val()+"'"+"&itemdescription_" + j + "='" + $('#fabDescription_'+j).val()+"'"+"&bodyPart_" + j + "='" + $('#bodyPart_'+j).val()+"'";
					}
				}
				else if(cbo_item_category_id==4)
				{
					for (var j=1; j<=row_num; j++)
					{
						data_all+="&workOrderNo_" + j + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + j + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + j + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&bookingNo_" + j + "='" + $('#bookingNo_'+j).val()+"'"+"&itemgroupid_" + j + "='" + $('#itemgroupid_'+j).val()+"'"+"&itemgroupidPlace_" + j + "='" + $('#itemgroupid_'+j).attr('placeholder')+"'"+"&itemdescription_" + j + "='" + $('#itemdescription_'+j).val()+"'"+"&itemColor_" + j + "='" + $('#itemColor_'+j).val()+"'"+"&itemColorPlace_" + j + "='" + $('#itemColor_'+j).attr('placeholder')+"'"+"&itemSize_" + j + "='" + $('#itemSize_'+j).val()+"'"+"&itemSizePlace_" + j + "='" + $('#itemSize_'+j).attr('placeholder')+"'"+"&uom_" + j + "='" + $('#uom_'+j).val()+"'"+"&uomPlace_" + j + "='" + $('#uom_'+j).attr('placeholder')+"'"+"&quantity_" + j + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + j + "='" + $('#rate_'+j).val()+"'"+"&ratePlace_" + j + "='" + $('#rate_'+j).attr('placeholder')+"'"+"&amount_" + j + "='" + $('#amount_'+j).val()+"'"+"&amountPlace_" + j + "='" + $('#amount_'+j).attr('placeholder')+"'"+"&updateIdDtls_" + j + "='" + $('#updateIdDtls_'+j).val()+"'"+"&bookingWithoutOrder_" + j + "='" + $('#bookingWithoutOrder_'+j).val()+"'";
					}
				}
				else if(cbo_item_category_id==74)
				{
					for (var j=1; j<=row_num; j++)
					{
						data_all+="&workOrderNo_" + j + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + j + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + j + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&bookingNo_" + j + "='" + $('#bookingNo_'+j).val()+"'"+"&aopColor_" + j + "='" + $('#aopColor_'+j).val()+"'"+"&aopColorPlace_" + j + "='" + $('#aopColor_'+j).attr('placeholder')+"'"+"&itemColor_" + j + "='" + $('#itemColor_'+j).val()+"'"+"&itemColorPlace_" + j + "='" + $('#itemColor_'+j).attr('placeholder')+"'"+"&gsm_" + j + "='" + $('#gsm_'+j).val()+"'"+"&bodyPart_" + j + "='" + $('#bodyPart_'+j).val()+"'"+"&bodyPartPlace_" + j + "='" + $('#bodyPart_'+j).attr('placeholder')+"'"+"&uom_" + j + "='" + $('#uom_'+j).val()+"'"+"&uomPlace_" + j + "='" + $('#uom_'+j).attr('placeholder')+"'"+"&quantity_" + j + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + j + "='" + $('#rate_'+j).val()+"'"+"&ratePlace_" + j + "='" + $('#rate_'+j).attr('placeholder')+"'"+"&amount_" + j + "='" + $('#amount_'+j).val()+"'"+"&amountPlace_" + j + "='" + $('#amount_'+j).attr('placeholder')+"'"+"&updateIdDtls_" + j + "='" + $('#updateIdDtls_'+j).val()+"'"+"&bookingWithoutOrder_" + j + "='" + $('#bookingWithoutOrder_'+j).val()+"'";
					}
				}
				else if(cbo_item_category_id==25 || cbo_item_category_id==102 || cbo_item_category_id==104)
				{
					for (var j=1; j<=row_num; j++)
					{
						data_all+="&workOrderNo_" + j + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + j + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + j + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&bookingNo_" + j + "='" + $('#bookingNo_'+j).val()+"'"+"&gmtsItem_" + j + "='" + $('#gmtsItem_'+j).val()+"'"+"&gmtsItemPlace_" + j + "='" + $('#gmtsItem_'+j).attr('placeholder')+"'"+"&bodyPart_" + j + "='" + $('#bodyPart_'+j).val()+"'"+"&bodyPartPlace_" + j + "='" + $('#bodyPart_'+j).attr('placeholder')+"'"+"&embName_" + j + "='" + $('#embName_'+j).val()+"'"+"&embNamePlace_" + j + "='" + $('#embName_'+j).attr('placeholder')+"'"+"&embType_" + j + "='" + $('#embType_'+j).val()+"'"+"&embTypePlace_" + j + "='" + $('#embType_'+j).attr('placeholder')+"'"+"&itemdescription_" + j + "='" + $('#itemdescription_'+j).val()+"'"+"&itemColor_" + j + "='" + $('#itemColor_'+j).val()+"'"+"&itemColorPlace_" + j + "='" + $('#itemColor_'+j).attr('placeholder')+"'"+"&itemSize_" + j + "='" + $('#itemSize_'+j).val()+"'"+"&itemSizePlace_" + j + "='" + $('#itemSize_'+j).attr('placeholder')+"'"+"&uom_" + j + "='" + $('#uom_'+j).val()+"'"+"&uomPlace_" + j + "='" + $('#uom_'+j).attr('placeholder')+"'"+"&quantity_" + j + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + j + "='" + $('#rate_'+j).val()+"'"+"&ratePlace_" + j + "='" + $('#rate_'+j).attr('placeholder')+"'"+"&amount_" + j + "='" + $('#amount_'+j).val()+"'"+"&amountPlace_" + j + "='" + $('#amount_'+j).attr('placeholder')+"'"+"&updateIdDtls_" + j + "='" + $('#updateIdDtls_'+j).val()+"'"+"&bookingWithoutOrder_" + j + "='" + $('#bookingWithoutOrder_'+j).val()+"'";
					}
				}
				else if(cbo_item_category_id==103)
				{
					for (var j=1; j<=row_num; j++)
					{
						data_all+="&workOrderNo_" + j + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + j + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + j + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&bookingNo_" + j + "='" + $('#bookingNo_'+j).val()+"'"+"&styleRef_" + j + "='" + $('#styleRef_'+j).val()+"'"+"&gmtsItem_" + j + "='" + $('#gmtsItem_'+j).val()+"'"+"&gmtsItemPlace_" + j + "='" + $('#gmtsItem_'+j).attr('placeholder')+"'"+"&embName_" + j + "='" + $('#embName_'+j).val()+"'"+"&embNamePlace_" + j + "='" + $('#embName_'+j).attr('placeholder')+"'"+"&embType_" + j + "='" + $('#embType_'+j).val()+"'"+"&embTypePlace_" + j + "='" + $('#embType_'+j).attr('placeholder')+"'"+"&itemdescription_" + j + "='" + $('#itemdescription_'+j).val()+"'"+"&itemColor_" + j + "='" + $('#itemColor_'+j).val()+"'"+"&itemColorPlace_" + j + "='" + $('#itemColor_'+j).attr('placeholder')+"'"+"&uom_" + j + "='" + $('#uom_'+j).val()+"'"+"&uomPlace_" + j + "='" + $('#uom_'+j).attr('placeholder')+"'"+"&quantity_" + j + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + j + "='" + $('#rate_'+j).val()+"'"+"&ratePlace_" + j + "='" + $('#rate_'+j).attr('placeholder')+"'"+"&amount_" + j + "='" + $('#amount_'+j).val()+"'"+"&amountPlace_" + j + "='" + $('#amount_'+j).attr('placeholder')+"'"+"&updateIdDtls_" + j + "='" + $('#updateIdDtls_'+j).val()+"'"+"&bookingWithoutOrder_" + j + "='" + $('#bookingWithoutOrder_'+j).val()+"'";
					}
				}
				
				//alert (data_all);return;

				if(data_all=="")
				{
					alert("No Item");
					return;
				}

				var data="action=save_update_delete&operation="+operation+'&total_row='+row_num+get_submitted_data_string('cbo_item_category_id*cbo_importer_id*cbo_supplier_id*pi_number*pi_date*last_shipment_date*pi_validity_date*cbo_currency_id*cbo_source_id*hs_code*txt_internal_file_no*is_lc_sc*lc_sc_id*lc_sc_no*lc_sc_file_year*intendor_name*cbo_pi_basis_id*txt_remarks*hide_approved_status*update_id*cbo_goods_rcv_status*export_pi_id*within_group*txt_total_amount*txt_upcharge*txt_discount*txt_total_amount_net*cbo_ready_to_approved*txt_lc_group_no*hiddn_user_id*txt_beneficiary_name*cross_check_activity_status*update_activity_id*cbo_priority*cbo_payterm_id*txt_tenor*cbo_pi_for*cbo_nagotiate_by*txt_notes*pi_inhand_date*cbo_location_name*cbo_buyer_name*cbo_brand_id*cbo_season_year*cbo_season_id*lc_req_date*txt_order_file_no',"../../")+data_all;
			}
			else
			{
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_item_category_id*cbo_importer_id*cbo_supplier_id*pi_number*pi_date*last_shipment_date*pi_validity_date*cbo_currency_id*cbo_source_id*hs_code*txt_internal_file_no*is_lc_sc*lc_sc_id*lc_sc_no*lc_sc_file_year*intendor_name*cbo_pi_basis_id*txt_remarks*hide_approved_status*update_id*cbo_goods_rcv_status*export_pi_id*within_group*txt_total_amount*txt_upcharge*txt_discount*txt_total_amount_net*cbo_ready_to_approved*txt_lc_group_no*hiddn_user_id*txt_beneficiary_name*cross_check_activity_status*update_activity_id*cbo_priority*cbo_payterm_id*txt_tenor*cbo_pi_for*cbo_nagotiate_by*txt_notes*pi_inhand_date*cbo_location_name*cbo_buyer_name*cbo_brand_id*cbo_season_year*cbo_season_id*lc_req_date*txt_order_file_no',"../../");
			}
			// alert(data);return;
			freeze_window(operation);
			http.open("POST","requires/pi_controller_urmi.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_pi_mst_reponse;
		}
	}
	
	function fnc_pi_mst_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');			

			if((reponse[0]==0 || reponse[0]==1))
			{
				document.getElementById('txt_system_id').value = reponse[1];
				document.getElementById('update_id').value = reponse[1];

				var export_pi_id=$('#export_pi_id').val();
				if(export_pi_id!="")
				{
					show_list_view(reponse[1],'export_pi_details_update','pi_details_container','requires/pi_controller_urmi','');
				}
				set_button_status(1, permission, 'fnc_pi_mst',1);

				$('#cbo_item_category_id').attr('disabled','true');
				$('#cbo_importer_id').attr('disabled','true');
				$('#cbo_supplier_id').attr('disabled','true');
				$('#cbo_pi_basis_id').attr('disabled','true');
				$('#cbo_currency_id').attr('disabled','true');
				$('#cbo_goods_rcv_status').attr('disabled','false');
			}
			else if(reponse[0]==2)
			{
				//reset_form('proforma_invoice','pi_details_container','','cbo_currency_id,2*cbo_pi_basis_id,2',"disable_enable_fields('cbo_importer_id*cbo_item_category_id*cbo_pi_basis_id',0)");
				reset_fnc();
				set_button_status(0, permission, 'fnc_pi_mst',1);
			}
			else if(reponse[0]==14)
			{
				alert(reponse[1]);
			}
			else if(reponse[0]==15)
			{
				alert(reponse[1]);release_freezing();return;
			}
			else if(reponse[0]==16)
			{
				alert("This PI is already Approved. So You can not change/delete it.");
			}
			else if(reponse[0]==20)
			{
				alert(reponse[1]);
			}

			show_msg(trim(reponse[0]));
			release_freezing();
			uploadFile( $("#update_id").val());
		}
	}

	function math_operation_byName( target_fld, value_fld, operator, fld_range, dec_point)
	{
		//number_format_common( number, dec_type, comma, path, currency )
		//var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
		//	math_operation( des_fil_id, field_id, '+', rowCount,ddd);
		if (!dec_point) var dec_point=0;
		var tot=0;
		$("#"+fld_range).find('tbody tr').each(function()
		{
			tot=(tot*1) + ($(this).find('input[name="'+value_fld+'[]"]').val()*1);
		});
		document.getElementById(target_fld).value=number_format_common(tot,dec_point.dec_type, dec_point.comma,dec_point.currency);
	}

	function add_auto_complete(i)
	{
		$("#colorName_"+i).autocomplete({
			source: str_color
		});
		$("#itemColor_"+i).autocomplete({
			source: str_color
		});
		$("#sizeName_"+i).autocomplete({
			source: str_size
		});
		$("#composition_"+i).autocomplete({
			source: str_composition
		});
		$("#construction_"+i).autocomplete({
			source: str_construction
		});
		$("#diawidth_"+i).autocomplete({
			source: str_dia_width
		});
	}
	
	function fn_add_color_id(i)
	{
		var color_name=$("#colorName_"+i).val();
		//alert(color_name+"="+i+"="+js_colors_array[color_name]);
		var txt_color_form_lib=$("#txt_color_form_lib").val();
		if(js_colors_array[color_name]==undefined || js_colors_array[color_name]=="")
		{
			if(txt_color_form_lib==1) 
			{
				$("#colorName_"+i).val("");
			}
			$("#colorId_"+i).val("");
		}
		else
		{
			$("#colorId_"+i).val(js_colors_array[color_name]);
		}
	}


	function add_break_down_tr( i )
	{
		var row_num=$('#tbl_pi_item tbody tr').length;
		var category=$('#cbo_item_category_id').val();
		var select_item_arrs=JSON.parse('<? echo json_encode($select_item_arrs); ?>');
		if(select_item_arrs[category] !="" && select_item_arrs[category] != undefined){
			if(trim($('#itemdescription_'+i).val())==""){
				alert("Please Fullfill Previous Row Data");return;
			}
		}else if(category==24){
			if(trim($('#lot_'+i).val())==""){
				alert("Please Fullfill Previous Row Data");return;
			}
		}else if(category==31){
			if(trim($('#txtTestItem_'+i).val())=="" || trim($('#amount_'+i).val())==""){
				alert("Please Fullfill Previous Row Data");return;
			}
		}else if(category==1){
			//alert( category+'c');
			if((trim($('#colorName_'+i).val())=="" || trim($('#countName_'+i).val())==0) && trim($('#rate_'+i).val())==""){
				alert("Please Fullfill Previous Row Data");return;
			}
		}
		
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;
			if(category==4)
			{
				$("#tbl_pi_item tbody tr:last").clone().find("input,select").each(function(){
	                $(this).attr({
	                    'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
	                    'name': function (_, name) {return name},
	                    'value': function (_, value) {return value}
	                });    
                }).end().appendTo("#tbl_pi_item");
			}
			else
			{
				$("#tbl_pi_item tbody tr:last").clone().find("input,select").each(function(){
                $(this).attr({
                    'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
                    'name': function (_, name) {return name},
	                'value': function (_, value) {return value}
                });
    
                }).end().appendTo("#tbl_pi_item");
			}
			
			if(category==1)
			{
				var color_name=$('#colorName_'+row_num).val();
				var color_id=$('#colorId_'+row_num).val();
				$('#colorName_'+i).val(color_name);
				$('#colorId_'+i).val(color_id);
			}
			
			$('#rate_'+i).val("");
			$('#quantity_'+i).val("");
			$('#amount_'+i).val("");
			
			/*$("#tbl_pi_item tbody tr:last").clone().find("input,select").each(function(){
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
			  'value': function(_, value) { return "" }
			});

			}).end().appendTo("#tbl_pi_item");*/

			$("#tbl_pi_item tbody tr:last").removeAttr('id').attr('id','row_'+i);


			$('#rate_'+i).removeAttr("onKeyUp").attr("onKeyUp","calculate_amount("+i+")");
			$('#quantity_'+i).removeAttr("onKeyUp").attr("onKeyUp","calculate_amount("+i+")");
			
			if(category==1)
			{
				$('#txtyarnCompositionItem1_'+i).removeAttr("onchange").attr("onClick","openmypage_comp("+i+")").removeAttr("disabled");
				$('#yarnCompositionPercentage1_'+i).removeAttr("onchange").attr("onchange","control_composition("+i+",'percent_one')");
				$('#yarnCompositionItem2_'+i).removeAttr("onchange").attr("onchange","control_composition("+i+",'comp_two')");
				$('#yarnCompositionPercentage2_'+i).removeAttr("onchange").attr("onchange","control_composition("+i+",'percent_two')");
				$('#yarnCompositionPercentage1_'+i).removeAttr("value").attr("value","100");
				$('#colorName_'+i).removeAttr("disabled");
				$('#countName_'+i).removeAttr("disabled");
				$('#yarnType_'+i).removeAttr("disabled");
                $('#uom_'+i).val(12);
				$('#colorName_'+i).removeAttr("onBlur").attr("onBlur","fn_add_color_id("+i+")");
			}else if(category==4){
				$('#itemgroupid_'+i).removeAttr("onchange").attr("onchange","get_php_form_data(this.value+'**'+'uom_"+i+"','get_uom', 'requires/pi_controller_urmi')");
			}else if(category==3){
				$('#fabDescription_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_fabricDescription("+i+")");
				$('#fabWeight_'+i).removeAttr("disabled");
			}
			//else if(category==5 || category==6 || category==7 || category==8 || category==9 || category==10 || category==11 || category==15 || category==16 || category==17 || category==18 || category==19 || category==20 || category==21 || category==22 || category==32 || category==33 || category==34 || category==35)
			else if(select_item_arrs[category] !=""){
				$('#itemdescription_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_item_desc("+i+")");
				$('#itemgroupid_'+i).val(0);
			}else if(category==24){
				$('#lot_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_item_desc("+i+")");
			}else if(category==25){
				$('#embellname_'+i).removeAttr("onchange").attr("onchange","load_drop_down('requires/pi_controller_urmi',this.value+'**'+0+'**'+'embelltype_"+i+"', 'load_drop_down_embelltype','embelltypeTd_"+i+"')");
				$('#row_'+i).find("td:eq(2)").removeAttr('id').attr('id','embelltypeTd_'+i);
			}else if(category==31){
				$('#txtTestItem_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_test_item("+i+")");
				$('#amount_'+i).removeAttr("onKeyUp").attr("onKeyUp","check_amount("+i+")");
			}else{
				if(category==2) {$('#uom_'+i).val(0);}
				if(category==13) {$('#uom_'+i).val(12);}
			}

			$('#updateIdDtls_'+i).val("");
			
			$('#construction_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_fabricDescription("+i+")");
			$('#increase_'+i).removeAttr("value").attr("value","+");
			$('#decrease_'+i).removeAttr("value").attr("value","-");
			$('#hsCode_'+i).removeAttr("disabled");
			$('#rate_'+i).removeAttr("disabled");
			$('#decrease_'+i).removeAttr("disabled");
			$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
			$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");

			add_auto_complete(i);
			set_all_onclick();
		}
	}

	function fn_deleteRow(rowNo)
	{
		var item_category = $('#cbo_item_category_id').val();
		var numRow = $('table#tbl_pi_item tbody tr').length;
		//if(rowNo!=1)
		if(numRow!=1)
		{
			var updateIdDtls=$('#updateIdDtls_'+rowNo).val();
			var txt_deleted_id=$('#txt_deleted_id').val();
			var selected_id='';

			if(updateIdDtls!='')
			{
				if(txt_deleted_id=='') selected_id=updateIdDtls; else selected_id=txt_deleted_id+','+updateIdDtls;
				$('#txt_deleted_id').val( selected_id );
			}

			//$('#tbl_pi_item tbody tr:last').remove();
			$('#row_' + rowNo).remove();
			var txt_tot_row = $('#txt_tot_row').val();
			var tot_row=txt_tot_row-1;
			$('#txt_tot_row').val( tot_row );
			calculate_total_amount(1);
		}
		else
		{
			return false;
		}
	}

	function check_amount(i)
	{
		var pi_basis_id = $('#cbo_pi_basis_id').val();
		if(pi_basis_id==1)
		{
			var bl_amnt=$('#amount_'+i).attr('placeholder')*1;
			var pi_amnt=$('#amount_'+i).val()*1;
			if(pi_amnt>bl_amnt)
			{
				alert("Amount Exceeds WO Balance Amount.");
				$('#amount_'+i).val('');
			}
		}
		calculate_total_amount(1);
	}

	function calculate_amount(i)
	{
		var pi_basis_id = $('#cbo_pi_basis_id').val();
		if(pi_basis_id==1)
		{
			var bl_qty=$('#quantity_'+i).attr('placeholder')*1;
			var pi_qty=$('#quantity_'+i).val()*1;
			if(pi_qty>bl_qty)
			{
				alert("Quantity Exceeds WO Balance Qty.");
				$('#quantity_'+i).val('');
			}
		}

		var ddd={ dec_type:5, comma:0, currency:''}
		math_operation( 'amount_'+i, 'quantity_'+i+'*rate_'+i, '*','',ddd);
		calculate_total_amount(1);
	}

	function calculate_total_amount(type)
	{
		//alert(type);
		if(type==1)
		{
			var ddd={ dec_type:5, comma:0, currency:''}
			var numRow = $('table#tbl_pi_item tbody tr').length;
			//alert(numRow);
			math_operation_byName( "txt_total_amount", "amount", "+", "tbl_pi_item",ddd );
		}

		var txt_total_amount=$('#txt_total_amount').val();
		var txt_upcharge=$('#txt_upcharge').val();
		var txt_discount=$('#txt_discount').val();

		var net_tot_amnt=txt_total_amount*1+txt_upcharge*1-txt_discount*1;
		$('#txt_total_amount_net').val(net_tot_amnt.toFixed(4));
	}

	function fnCheckUnCheckAll(checkVal)
	{
		/*var i=1;
		for (Looper=0; Looper < document.pimasterform_2.length ; Looper++ )
		{
			var strType = document.pimasterform_2.elements[Looper].type;
			alert($("#row_"+i).is(':visible'));
			if (strType=="checkbox" && $("#row_"+i).is(':visible'))
			{
				document.pimasterform_2.elements[Looper].checked=checkVal;
			}
			i++;
		}*/
		
		var tal_length=$("#tbl_pi_item tbody tr").length*1;
		for(var i=1; i<=tal_length;i++)
		{
			if($("#row_"+i).is(':visible'))
			{
				if(checkVal)
				{
					$("#workOrderChkbox_"+i).prop('checked', true);
				}
				else
				{
					$("#workOrderChkbox_"+i).prop('checked', false);
				}
			}
		}
	}

	function fnc_pi_item_details( operation )
	{

		var cbo_item_category_id = $('#cbo_item_category_id').val();
		var cbo_importer_id = $('#cbo_importer_id').val();
		var cbo_pi_basis_id = $('#cbo_pi_basis_id').val();
		var is_lc_sc = $('#is_lc_sc').val();
		var lc_sc_id = $('#lc_sc_id').val();
		var cbo_ready_to_approved = $('#cbo_ready_to_approved').val();
		var update_id = $('#update_id').val();
		var txt_upcharge = $('#txt_upcharge').val();
		var up_charge_break_down = $('#up_charge_break_down').val();
		var txt_discount = $('#txt_discount').val();
		var discount_break_down = $('#discount_break_down').val();

		var txt_deleted_id = $('#txt_deleted_id').val();
		var cbo_currency_id = $('#cbo_currency_id').val();
		var cbo_goods_rcv_status = $('#cbo_goods_rcv_status').val();
		var txt_order_type = $('#txt_order_type').val();
		var export_pi_id = $('#export_pi_id').val();
		


		var goods_rcv_variable="";
		goods_rcv_variable=return_global_ajax_value( cbo_goods_rcv_status+"***"+cbo_importer_id, 'goods_rcv_variable_form_lib', '', 'requires/pi_controller_urmi');
		if(goods_rcv_variable=="")
		{
			alert("Please Check Variable Settings Commercial.");return;
		}

		//var txt_total_amount = $('#txt_total_amount').val();
		//var txt_total_amount_net = $('#txt_total_amount_net').val();

		var txt_total_amount=0; var txt_total_amount_net=0;

		if(update_id=='')
		{
			alert('Please Save PI First');
			return false;
		}

		/*/if(operation==2 && cbo_pi_basis_id==2)
		{
			show_msg('13');
			return false;
		}*/

		var row_num=$('#tbl_pi_item tbody tr').length;
		var data_all=""; var i=0; var selected_row=0;
		var data_all_checked=""; var tot_del_amount=0;  var tot_del_quantity=0;
		if(operation==2)
		{
			txt_deleted_id='';
			if(cbo_item_category_id==1)
			{
				if(cbo_pi_basis_id==1)
				{
					for (var j=1; j<=row_num; j++)
					{
						var updateIdDtls=$('#updateIdDtls_'+j).val();
						if($('#workOrderChkbox_'+j).is(':checked'))
						{
							if(txt_deleted_id=="") txt_deleted_id=updateIdDtls; else txt_deleted_id+=","+updateIdDtls;
							i++;
							data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&hideOrdeSource_" + i + "='" + $('#hideOrdeSource_'+j).val()+"'"+"&hsCode_" + i + "='" + $('#hsCode_'+j).val()+"'"+"&colorName_" + i + "='" + $('#colorName_'+j).val()+"'"+"&countName_" + i + "='" + $('#countName_'+j).val()+"'"+"&yarnCompositionItem1_" + i + "='" + $('#yarnCompositionItem1_'+j).val()+"'"+"&yarnCompositionPercentage1_" + i + "='" + $('#yarnCompositionPercentage1_'+j).val()+"'"+"&yarnCompositionItem2_" + i + "='" + $('#yarnCompositionItem2_'+j).val()+"'"+"&yarnCompositionPercentage2_" + i + "='" + $('#yarnCompositionPercentage2_'+j).val()+"'"+"&yarnType_" + i + "='" + $('#yarnType_'+j).val()+"'"+"&uom_" + i + "='" + $('#uom_'+j).val()+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'"+"&hideTransData_" + i + "='" + $('#hideTransData_'+j).val()+"'";
	
							txt_total_amount+=$('#amount_'+j).val()*1;
							tot_del_amount+=$('#amount_'+j).val()*1; tot_del_quantity+=$('#quantity_'+j).val()*1;
							if($('#workOrderChkbox_'+j).is(':checked')) selected_row++;
						}
					}
				}
				else
				{
					var y=0;
					var color_form_lib=$('#txt_color_form_lib').val()*1;
					$("#tbl_pi_item").find('tbody tr').each(function() {
						var hsCode = $(this).find('input[name="hsCode[]"]').val();
						var colorName = $(this).find('input[name="colorName[]"]').val();
						var colorId = $(this).find('input[name="colorId[]"]').val();
        				var countName = $(this).find('select[name="countName[]"]').val();
        				var yarnCompositionItem1 = $(this).find('input[name="yarnCompositionItem1[]"]').val();
        				var yarnCompositionPercentage1 = $(this).find('input[name="yarnCompositionPercentage1[]"]').val();
        				var yarnCompositionItem2 = $(this).find('input[name="yarnCompositionItem2[]"]').val();
        				var yarnCompositionPercentage2 = $(this).find('input[name="yarnCompositionPercentage2[]"]').val();
        				var yarnType = $(this).find('select[name="yarnType[]"]').val();
        				var uom = $(this).find('select[name="uom[]"]').val();
        				var quantity = $(this).find('input[name="quantity[]"]').val();
        				var rate = $(this).find('input[name="rate[]"]').val();
        				var amount = $(this).find('input[name="amount[]"]').val();
        				var updateIdDtls = $(this).find('input[name="updateIdDtls[]"]').val();

        				if(txt_deleted_id=="") txt_deleted_id=updateIdDtls; else txt_deleted_id+=","+updateIdDtls;

        				txt_total_amount+=amount*1;
        				tot_del_amount+=amount*1; 
        				tot_del_quantity+=quantity*1;
        				selected_row++;
        				i++;
        				
						/*if(color_form_lib==1 && (colorId=="" || colorId==0))
						{
							$('#colorName_'+j).val("");
							$('#colorName_'+j).focus();
							alert("Color Not Found In Library");
							return;
						}*/

						if(color_form_lib==1 && (colorId=="" || colorId==0))
						{
							y=1;
						}        				

        				data_all +='&hsCode_' + i + '=' + hsCode + '&colorName_' + i + '=' + colorName + '&countName_' + i + '=' + countName + '&yarnCompositionItem1_' + i + '=' + yarnCompositionItem1 + '&yarnCompositionPercentage1_' + i + '=' + yarnCompositionPercentage1 + '&yarnCompositionItem2_' + i + '=' + yarnCompositionItem2 + '&yarnCompositionPercentage2_' + i + '=' + yarnCompositionPercentage2 + '&yarnType_' + i + '=' + yarnType + '&uom_' + i + '=' + uom + '&quantity_' + i + '=' + quantity + '&rate_' + i + '=' + rate + '&amount_' + i + '=' + amount + '&updateIdDtls_' + i + '=' + updateIdDtls;
					});	

					if (y==1) {
		    			alert("Color Not Found In Library");
		    			return;
		    		}					
				}
				//alert(txt_deleted_id);return;
			}
			else
			{
				if(cbo_pi_basis_id==1)
				{
					for (var j=1; j<=row_num; j++)
					{
						var updateIdDtls=$('#updateIdDtls_'+j).val();
						txt_total_amount+=$('#amount_'+j).val()*1;

						if($('#workOrderChkbox_'+j).is(':checked') && updateIdDtls!="")
						{
							selected_row++;
							if(txt_deleted_id=="") txt_deleted_id=updateIdDtls; else txt_deleted_id+=","+updateIdDtls;
							i++;
							
								if(cbo_item_category_id!=31)
								{
									data_all+="&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+i).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'";
								}
								else
								{
									data_all+="&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'";
								}
							
							
							
							tot_del_amount+=$('#amount_'+j).val()*1; tot_del_quantity+=$('#quantity_'+j).val()*1;
							//txt_total_amount_net=txt_total_amount*1+txt_upcharge*1-txt_discount*1;
						}

						// is checked
						if(updateIdDtls!="" && $('#workOrderChkbox_'+j).is(':checked'))
						{
							i++;
							data_all_checked+="&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'";
							//txt_total_amount+=$('#amount_'+j).val()*1;
						}
					}	
				}
				else
				{				
					if(cbo_item_category_id!=31)
					{
						$("#tbl_pi_item").find('tbody tr').each(function() {
	        				var quantity = $(this).find('input[name="quantity[]"]').val();
	        				var rate = $(this).find('input[name="rate[]"]').val();
	        				var amount = $(this).find('input[name="amount[]"]').val();
	        				var updateIdDtls = $(this).find('input[name="updateIdDtls[]"]').val();

							txt_total_amount+=amount*1;
							tot_del_amount+=amount*1;
							tot_del_quantity+=quantity*1;
							selected_row++;
							if(txt_deleted_id=="") txt_deleted_id=updateIdDtls; else txt_deleted_id+=","+updateIdDtls;
							i++;

	        				data_all +='&quantity_' + i + '=' + quantity + '&rate_' + i + '=' + rate + '&amount_' + i + '=' + amount + '&updateIdDtls_' + i + '=' + updateIdDtls;
						});
					}
					else
					{
						$("#tbl_pi_item").find('tbody tr').each(function() {
	        				var amount = $(this).find('input[name="amount[]"]').val();
	        				var updateIdDtls = $(this).find('input[name="updateIdDtls[]"]').val();

	        				txt_total_amount+=amount*1;
	        				tot_del_amount+=amount*1;
							selected_row++;
							if(txt_deleted_id=="") txt_deleted_id=updateIdDtls; else txt_deleted_id+=","+updateIdDtls;
							i++;

	        				data_all +='&amount_' + i + '=' + amount + '&updateIdDtls_' + i + '=' + updateIdDtls;
						});							
					}					 

					// is checked
					/*if(updateIdDtls!="" && $('#workOrderChkbox_'+j).is(':checked'))
					{
						i++;
						data_all_checked+="&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'";
						//txt_total_amount+=$('#amount_'+j).val()*1;
					}*/									
				}					
			}
			//alert(data_all);return;
			//alert(txt_total_amount+"--"+tot_del_amount);
			txt_total_amount=txt_total_amount-tot_del_amount;
		}
		else
		{
			if(cbo_item_category_id==1)  // Yarn
			{
				if(cbo_pi_basis_id==1)
				{
					for (var j=1; j<=row_num; j++)
					{
						var updateIdDtls=$('#updateIdDtls_'+j).val();

						if($('#workOrderChkbox_'+j).is(':checked') || updateIdDtls!="")
						{
							if($('#hideOrdeSource_'+j).val()!=5)
							{
								if (form_validation('workOrderNo_'+j+'*quantity_'+j+'*rate_'+j,'WO*Qunatity*Rate')==false)
								{
									return;
								}
							}

							i++;
							//data_all+=get_submitted_data_string('workOrderNo_'+i+'*hideWoId_'+i+'*hideWoDtlsId_'+i+'*colorName_'+i+'*countName_'+i+'*yarnCompositionItem1_'+i+'*yarnCompositionPercentage1_'+i+'*yarnCompositionItem2_'+i+'*yarnCompositionPercentage2_'+i+'*yarnType_'+i+'*uom_'+i+'*quantity_'+i+'*rate_'+i+'*amount_'+i+'*updateIdDtls_'+i,"../../");

							data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&hideOrdeSource_" + i + "='" + $('#hideOrdeSource_'+j).val()+"'"+"&hsCode_" + i + "='" + $('#hsCode_'+j).val()+"'"+"&colorName_" + i + "='" + $('#colorName_'+j).val()+"'"+"&countName_" + i + "='" + $('#countName_'+j).val()+"'"+"&yarnCompositionItem1_" + i + "='" + $('#yarnCompositionItem1_'+j).val()+"'"+"&yarnCompositionPercentage1_" + i + "='" + $('#yarnCompositionPercentage1_'+j).val()+"'"+"&yarnCompositionItem2_" + i + "='" + $('#yarnCompositionItem2_'+j).val()+"'"+"&yarnCompositionPercentage2_" + i + "='" + $('#yarnCompositionPercentage2_'+j).val()+"'"+"&yarnType_" + i + "='" + $('#yarnType_'+j).val()+"'"+"&uom_" + i + "='" + $('#uom_'+j).val()+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'"+"&hideTransData_" + i + "='" + $('#hideTransData_'+j).val()+"'";
							//alert(data_all);return;

							txt_total_amount+=$('#amount_'+j).val()*1;
							if($('#workOrderChkbox_'+j).is(':checked')) selected_row++;
						}
					}
					//alert(data_all);//return;
				}
				else
				{
					var y=0;
					var z=0;
					var color_form_lib=$('#txt_color_form_lib').val()*1;

					$("#tbl_pi_item").find('tbody tr').each(function() {

						var hsCode = $(this).find('input[name="hsCode[]"]').val();
						var colorName = $(this).find('input[name="colorName[]"]').val();
						var colorId = $(this).find('input[name="colorId[]"]').val();
        				var countName = $(this).find('select[name="countName[]"]').val();
        				var yarnCompositionItem1 = $(this).find('input[name="yarnCompositionItem1[]"]').val();
        				var yarnCompositionPercentage1 = $(this).find('input[name="yarnCompositionPercentage1[]"]').val();
        				var yarnCompositionItem2 = $(this).find('input[name="yarnCompositionItem2[]"]').val();
        				var yarnCompositionPercentage2 = $(this).find('input[name="yarnCompositionPercentage2[]"]').val();
        				var yarnType = $(this).find('select[name="yarnType[]"]').val();
        				var uom = $(this).find('select[name="uom[]"]').val();
        				var quantity = $(this).find('input[name="quantity[]"]').val();
        				var rate = $(this).find('input[name="rate[]"]').val();
        				var amount = $(this).find('input[name="amount[]"]').val();
        				var updateIdDtls = $(this).find('input[name="updateIdDtls[]"]').val();

        				txt_total_amount+=amount*1;
        				selected_row++;
        				i++;

        				/*if(color_form_lib==1 && (color_id=="" || color_id==0))
						{
							$('#colorName_'+j).val("");
							$('#colorName_'+j).focus();
							alert("Color Not Found In Library");
							return;
						}*/

						if(color_form_lib==1 && (colorId=="" || colorId==0))
						{
							y=1;
						}

        				if (colorName=='' || countName==0 || yarnCompositionItem1=='' || yarnCompositionPercentage1=='' || quantity=="" || rate=="") {
        					z=1; //alert(colorName+"="+countName+"="+yarnCompositionItem1+"="+yarnCompositionPercentage1+"="+quantity+"="+rate);
        				}

        				data_all +='&hsCode_' + i + '=' + hsCode + '&colorName_' + i + '=' + colorName + '&countName_' + i + '=' + countName + '&yarnCompositionItem1_' + i + '=' + yarnCompositionItem1 + '&yarnCompositionPercentage1_' + i + '=' + yarnCompositionPercentage1 + '&yarnCompositionItem2_' + i + '=' + yarnCompositionItem2 + '&yarnCompositionPercentage2_' + i + '=' + yarnCompositionPercentage2 + '&yarnType_' + i + '=' + yarnType + '&uom_' + i + '=' + uom + '&quantity_' + i + '=' + quantity + '&rate_' + i + '=' + rate + '&amount_' + i + '=' + amount + '&updateIdDtls_' + i + '=' + updateIdDtls;

        			});

        			if (y==1) {
		    			alert("Color Not Found In Library");
		    			return;
		    		}

        			if (z==1) {
		    			alert('You must fill the Color, Count, Composition, Quantity and Rate');
		    			return;
		    		}					
				}
			}

			else if(cbo_item_category_id==2 || cbo_item_category_id==13) // 2=Knit Finish Fabrics, 13=Grey Fabric(Knit)
			{
				if(cbo_pi_basis_id==1)
				{
					for (var j=1; j<=row_num; j++)
					{
						var updateIdDtls=$('#updateIdDtls_'+j).val();
						if($('#workOrderChkbox_'+j).is(':checked') || updateIdDtls!="")
						{
							if (form_validation('workOrderNo_'+j+'*quantity_'+j+'*rate_'+j+'*uom_'+j,'WO*Qunatity*Rate*UOM')==false)
							{
								return;
							}

							i++;
							if(cbo_goods_rcv_status==1 && goods_rcv_variable !=1)
							{
								data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&hsCode_" + i + "='" + $('#hsCode_'+j).val()+"'"+"&construction_" + i + "='" + encodeURIComponent($('#construction_'+j).val())+"'"+"&composition_" + i + "='" + encodeURIComponent($('#composition_'+j).val())+"'"+"&colorName_" + i + "='" + encodeURIComponent($('#colorName_'+j).val())+"'"+"&gsm_" + i + "='" + $('#gsm_'+j).val()+"'"+"&diawidth_" + i + "='" + $('#diawidth_'+j).val()+"'"+"&uom_" + i + "='" + $('#uom_'+j).val()+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'"+"&hideDeterminationId_" + i + "='" + $('#hideDeterminationId_'+j).val()+"'"+"&bookingWithoutOrder_" + i + "='" + $('#bookingWithoutOrder_'+j).val()+"'"+"&hideTransData_" + i + "='" + $('#hideTransData_'+j).val()+"'";
							}
							else
							{
								data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&hsCode_" + i + "='" + $('#hsCode_'+j).val()+"'"+"&construction_" + i + "='" + encodeURIComponent($('#construction_'+j).val())+"'"+"&composition_" + i + "='" + encodeURIComponent($('#composition_'+j).val())+"'"+"&colorName_" + i + "='" + encodeURIComponent($('#colorName_'+j).val())+"'"+"&gsm_" + i + "='" + $('#gsm_'+j).val()+"'"+"&diawidth_" + i + "='" + $('#diawidth_'+j).val()+"'"+"&uom_" + i + "='" + $('#uom_'+j).attr('placeholder')+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'"+"&hideDeterminationId_" + i + "='" + $('#hideDeterminationId_'+j).val()+"'"+"&bookingWithoutOrder_" + i + "='" + $('#bookingWithoutOrder_'+j).val()+"'"+"&hideTransData_" + i + "='" + $('#hideTransData_'+j).val()+"'";
							}


							txt_total_amount+=$('#amount_'+j).val()*1;
							if($('#workOrderChkbox_'+j).is(':checked')) selected_row++;

						}
					}
				}
				else
				{
					var z=0;

					$("#tbl_pi_item").find('tbody tr').each(function() {
						var hsCode = $(this).find('input[name="hsCode[]"]').val();
						var construction = $(this).find('input[name="construction[]"]').val();
						var composition = $(this).find('input[name="composition[]"]').val();
        				var colorName = $(this).find('input[name="colorName[]"]').val();
        				var gsm = $(this).find('input[name="gsm[]"]').val();
        				var diawidth = $(this).find('input[name="diawidth[]"]').val();
        				var uom = $(this).find('select[name="uom[]"]').val();
        				var quantity = $(this).find('input[name="quantity[]"]').val();
        				var rate = $(this).find('input[name="rate[]"]').val();
        				var amount = $(this).find('input[name="amount[]"]').val();
        				var updateIdDtls = $(this).find('input[name="updateIdDtls[]"]').val();
        				var hideDeterminationId = $(this).find('input[name="hideDeterminationId[]"]').val();

        				if (construction=='' || composition=='' || diawidth=='' || uom==0) {
        					z=1;
        				}

        				if(quantity>0 || updateIdDtls!="")
        				{
        					i++;

        					data_all +='&hsCode_' + i + '=' + hsCode + '&construction_' + i + '=' + encodeURIComponent(construction) + '&composition_' + i + '=' + encodeURIComponent(composition) + '&colorName_' + i + '=' + encodeURIComponent(colorName) + '&gsm_' + i + '=' + gsm + '&diawidth_' + i + '=' + diawidth + '&uom_' + i + '=' + uom + '&quantity_' + i + '=' + quantity + '&rate_' + i + '=' + rate + '&amount_' + i + '=' + amount + '&updateIdDtls_' + i + '=' + updateIdDtls + '&hideDeterminationId_' + i + '=' + hideDeterminationId;

        					txt_total_amount+=amount*1;
							selected_row++;
        				}
        			});

        			if (z==1) {
		    			alert('You must fill the construction, composition, Composition, Diawidth and UOM');
		    			return;
		    		}

				}
			}

			else if(cbo_item_category_id==3)  // Woven Fabrics
			{
				if(cbo_pi_basis_id==1)
				{
					for (var j=1; j<=row_num; j++)
					{
						var updateIdDtls=$('#updateIdDtls_'+j).val();
						if($('#workOrderChkbox_'+j).is(':checked') || updateIdDtls!="")
						{
							if (form_validation('workOrderNo_'+j+'*quantity_'+j+'*rate_'+j,'WO*Qunatity*Rate')==false)
							{
								return;
							}

							i++;
							//data_all+=get_submitted_data_string('workOrderNo_'+i+'*hideWoId_'+i+'*hideWoDtlsId_'+i+'*construction_'+i+'*composition_'+i+'*colorName_'+i+'*weight_'+i+'*diawidth_'+i+'*uom_'+i+'*quantity_'+i+'*rate_'+i+'*amount_'+i+'*updateIdDtls_'+i,"../../");
							if(cbo_goods_rcv_status==1 && goods_rcv_variable !=1)
							{
								/*data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&hsCode_" + i +"='" + $('#hsCode_'+j).val()+"'"+"&construction_" + i + "='" + encodeURIComponent($('#construction_'+j).val())+"'"+"&composition_" + i + "='" + encodeURIComponent($('#composition_'+j).val())+"'"+"&colorName_" + i + "='" + encodeURIComponent($('#colorName_'+j).val())+"'"+"&weight_" + i + "='" + $('#gsm_'+j).val()+"'"+"&gsm_" + i + "='" + $('#gsm_'+j).val()+"'"+"&diawidth_" + i +"='" + $('#diawidth_'+j).val()+"'"+"&uom_" + i + "='" + $('#uom_'+j).val()+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'"+"&hideDeterminationId_" + i + "='" + $('#hideDeterminationId_'+j).val()+"'"+"&hideTransData_" + i + "='" + $('#hideTransData_'+j).val()+"'";*/
								data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&fabricRef_" + i +"='" + $('#fabricRef_'+j).val()+"'"+"&rdNo_" + i +"='" + $('#rdNo_'+j).val()+"'"+"&construction_" + i + "='" + encodeURIComponent($('#construction_'+j).val())+"'"+"&composition_" + i + "='" + encodeURIComponent($('#composition_'+j).val())+"'"+"&colorName_" + i + "='" + encodeURIComponent($('#colorName_'+j).val())+"'"+"&diawidth_" + i + "='" + $('#diawidth_'+j).val()+"'"+"&fabWeight_" + i + "='" + $('#fabWeight_'+j).val()+"'"+"&fabWeightType_" + i + "='" + $('#fabWeightType_'+j).attr('placeholder')+"'"+"&diawidthType_" + i + "='" + $('#diawidthType_'+j).attr('placeholder')+"'"+"&cutableWidth_" + i + "='" + $('#cutableWidth_'+j).val()+"'"+"&uom_" + i + "='" + $('#uom_'+j).val()+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'"+"&hideDeterminationId_" + i + "='" + $('#hideDeterminationId_'+j).val()+"'"+"&bookingWithoutOrder_" + i + "='" + $('#bookingWithoutOrder_'+j).val()+"'"+"&bodyPart_" + i + "='" + $('#bodyPart_'+j).val()+"'"+"&fabType_" + i + "='" + $('#fabType_'+j).val()+"'"+"&fabDesign_" + i + "='" + $('#fabDesign_'+j).val()+"'"+"&hideTransData_" + i + "='" + $('#hideTransData_'+j).val()+"'";

							}
							else
							{
								data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&fabricRef_" + i +"='" + $('#fabricRef_'+j).val()+"'"+"&rdNo_" + i +"='" + $('#rdNo_'+j).val()+"'"+"&construction_" + i + "='" + encodeURIComponent($('#construction_'+j).val())+"'"+"&composition_" + i + "='" + encodeURIComponent($('#composition_'+j).val())+"'"+"&colorName_" + i + "='" + encodeURIComponent($('#colorName_'+j).val())+"'"+"&diawidth_" + i + "='" + $('#diawidth_'+j).val()+"'"+"&fabWeight_" + i + "='" + $('#fabWeight_'+j).val()+"'"+"&fabWeightType_" + i + "='" + $('#fabWeightType_'+j).attr('placeholder')+"'"+"&diawidthType_" + i + "='" + $('#diawidthType_'+j).attr('placeholder')+"'"+"&cutableWidth_" + i + "='" + $('#cutableWidth_'+j).val()+"'"+"&uom_" + i + "='" + $('#uom_'+j).attr('placeholder')+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'"+"&hideDeterminationId_" + i + "='" + $('#hideDeterminationId_'+j).val()+"'"+"&bookingWithoutOrder_" + i + "='" + $('#bookingWithoutOrder_'+j).val()+"'"+"&bodyPart_" + i + "='" + $('#bodyPart_'+j).val()+"'"+"&fabType_" + i + "='" + $('#fabType_'+j).val()+"'"+"&fabDesign_" + i + "='" + $('#fabDesign_'+j).val()+"'"+"&hideTransData_" + i + "='" + $('#hideTransData_'+j).val()+"'";

							}
							//alert(data_all);return;


							txt_total_amount+=$('#amount_'+j).val()*1;
							if($('#workOrderChkbox_'+j).is(':checked')) selected_row++;
						}
					}
				}
				else
				{

					var z=0;

					$("#tbl_pi_item").find('tbody tr').each(function() {
						var fabricRef = $(this).find('input[name="fabricRef[]"]').val();
						var rdNo = $(this).find('input[name="rdNo[]"]').val();
						var construction = $(this).find('input[name="construction[]"]').val();
						var composition = $(this).find('input[name="composition[]"]').val();
        				var colorName = $(this).find('input[name="colorName[]"]').val();
        				var diawidth = $(this).find('input[name="diawidth[]"]').val();
        				var fabWeight = $(this).find('input[name="fabWeight[]"]').val();
        				var fabWeightType = $(this).find('select[name="fabWeightType[]"]').val();
        				var diawidthType = $(this).find('select[name="diawidthType[]"]').val();
        				var cutableWidth = $(this).find('input[name="cutableWidth[]"]').val();
        				var uom = $(this).find('select[name="uom[]"]').val();
        				var quantity = $(this).find('input[name="quantity[]"]').val();
        				var rate = $(this).find('input[name="rate[]"]').val();
        				var amount = $(this).find('input[name="amount[]"]').val();
        				var updateIdDtls = $(this).find('input[name="updateIdDtls[]"]').val();
        				var hideDeterminationId = $(this).find('input[name="hideDeterminationId[]"]').val();
        				var bookingWithoutOrder = $(this).find('input[name="bookingWithoutOrder[]"]').val();
        				var bodyPart = $(this).find('input[name="bodyPart[]"]').val();
        				var fabType = $(this).find('input[name="fabType[]"]').val();
        				var fabDesign = $(this).find('input[name="fabDesign[]"]').val();

        				if (construction=='' || colorName=='' || diawidth=='') {
        					z=1;
        				}

        				if(quantity>0 || updateIdDtls!="")
        				{
        					i++;

        					data_all +='&fabricRef_' + i + '=' + fabricRef + '&rdNo_' + i + '=' + rdNo + '&construction_' + i + '=' + encodeURIComponent(construction) + '&composition_' + i + '=' + encodeURIComponent(composition) + '&colorName_' + i + '=' + encodeURIComponent(colorName) + '&diawidth_' + i + '=' + diawidth + '&fabWeight_' + i + '=' + fabWeight + '&fabWeightType_' + i + '=' + fabWeightType + '&diawidthType_' + i + '=' + diawidthType + '&cutableWidth_' + i + '=' + cutableWidth + '&uom_' + i + '=' + uom + '&quantity_' + i + '=' + quantity + '&rate_' + i + '=' + rate + '&amount_' + i + '=' + amount + '&updateIdDtls_' + i + '=' + updateIdDtls + '&hideDeterminationId_' + i + '=' + hideDeterminationId + '&bookingWithoutOrder_' + i + '=' + bookingWithoutOrder + '&bodyPart_' + i + '=' + bodyPart + '&fabType_' + i + '=' + fabType + '&fabDesign_' + i + '=' + fabDesign;

        					txt_total_amount+=amount*1;
							selected_row++;
        				}
        			});

        			if (z==1) {
		    			alert('You must fill the Construction, Color Name, Diawidth');
		    			return;
		    		}
				}
			}

			else if(cbo_item_category_id==14)  //Grey Fabric(woven)
			{
				if(cbo_pi_basis_id==1)
				{
					for (var j=1; j<=row_num; j++)
					{
						var updateIdDtls=$('#updateIdDtls_'+j).val();
						if($('#workOrderChkbox_'+j).is(':checked') || updateIdDtls!="")
						{
							if (form_validation('workOrderNo_'+j+'*quantity_'+j+'*rate_'+j,'WO*Qunatity*Rate')==false)
							{
								return;
							}

							i++;
							//data_all+=get_submitted_data_string('workOrderNo_'+i+'*hideWoId_'+i+'*hideWoDtlsId_'+i+'*construction_'+i+'*composition_'+i+'*colorName_'+i+'*weight_'+i+'*diawidth_'+i+'*uom_'+i+'*quantity_'+i+'*rate_'+i+'*amount_'+i+'*updateIdDtls_'+i,"../../");
							if(cbo_goods_rcv_status==1 && goods_rcv_variable !=1)
							{
								data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&hsCode_" + i +"='" + $('#hsCode_'+j).val()+"'"+"&construction_" + i + "='" + encodeURIComponent($('#construction_'+j).val())+"'"+"&composition_" + i + "='" + encodeURIComponent($('#composition_'+j).val())+"'"+"&colorName_" + i + "='" + encodeURIComponent($('#colorName_'+j).val())+"'"+"&weight_" + i + "='" + $('#gsm_'+j).val()+"'"+"&gsm_" + i + "='" + $('#gsm_'+j).val()+"'"+"&diawidth_" + i +"='" + $('#diawidth_'+j).val()+"'"+"&uom_" + i + "='" + $('#uom_'+j).val()+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'"+"&hideDeterminationId_" + i + "='" + $('#hideDeterminationId_'+j).val()+"'"+"&hideTransData_" + i + "='" + $('#hideTransData_'+j).val()+"'"+"&bodyPart_" + i + "='" + $('#bodyPart_'+j).val()+"'";

							}
							else
							{
								data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&hsCode_" + i +"='" + $('#hsCode_'+j).val()+"'"+"&construction_" + i + "='" + encodeURIComponent($('#construction_'+j).val())+"'"+"&composition_" + i + "='" + encodeURIComponent($('#composition_'+j).val())+"'"+"&colorName_" + i + "='" + encodeURIComponent($('#colorName_'+j).val())+"'"+"&weight_" + i + "='" + $('#gsm_'+j).val()+"'"+"&gsm_" + i + "='" + $('#gsm_'+j).val()+"'"+"&diawidth_" + i + "='" + $('#diawidth_'+j).val()+"'"+"&uom_" + i + "='" + $('#uom_'+j).attr('placeholder')+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'"+"&hideDeterminationId_" + i + "='" + $('#hideDeterminationId_'+j).val()+"'"+"&hideTransData_" + i + "='" + $('#hideTransData_'+j).val()+"'"+"&bodyPart_" + i + "='" + $('#bodyPart_'+j).val()+"'";

							}


							txt_total_amount+=$('#amount_'+j).val()*1;
							if($('#workOrderChkbox_'+j).is(':checked')) selected_row++;
						}
					}
				}
				else
				{

					var z=0;

					$("#tbl_pi_item").find('tbody tr').each(function() {
						var hsCode = $(this).find('input[name="hsCode[]"]').val();
						var construction = $(this).find('input[name="construction[]"]').val();
						var composition = $(this).find('input[name="composition[]"]').val();
        				var colorName = $(this).find('input[name="colorName[]"]').val();
        				var weight = $(this).find('input[name="weight[]"]').val();
        				var gsm = $(this).find('input[name="gsm[]"]').val();
        				var diawidth = $(this).find('input[name="diawidth[]"]').val();
        				var uom = $(this).find('select[name="uom[]"]').val();        				
        				var quantity = $(this).find('input[name="quantity[]"]').val();
        				var rate = $(this).find('input[name="rate[]"]').val();
        				var amount = $(this).find('input[name="amount[]"]').val();
        				var updateIdDtls = $(this).find('input[name="updateIdDtls[]"]').val();
        				var hideDeterminationId = $(this).find('input[name="hideDeterminationId[]"]').val();  
        				var bodyPart = $(this).find('input[name="bodyPart[]"]').val();        				

        				if (construction=='' || colorName=='' || diawidth=='') {
        					z=1;
        				}

        				if(quantity>0 || updateIdDtls!="")
        				{
        					i++;

        					data_all +='&hsCode_' + i + '=' + hsCode + '&construction_' + i + '=' + encodeURIComponent(construction) + '&composition_' + i + '=' + encodeURIComponent(composition) + '&colorName_' + i + '=' + encodeURIComponent(colorName) + '&weight_' + i + '=' + weight + '&gsm_' + i + '=' + gsm + '&diawidth_' + i + '=' + diawidth + '&uom_' + i + '=' + uom + '&quantity_' + i + '=' + quantity + '&rate_' + i + '=' + rate + '&amount_' + i + '=' + amount + '&updateIdDtls_' + i + '=' + updateIdDtls + '&hideDeterminationId_' + i + '=' + hideDeterminationId + '&bodyPart_' + i + '=' + bodyPart;

        					txt_total_amount+=amount*1;
							selected_row++;
        				}
        			});

        			if (z==1) {
		    			alert('You must fill the Construction, Color Name, Diawidth');
		    			return;
		    		}					
				}
				//console.log(data_all);
				//return;
			}

			else if(cbo_item_category_id==4)  // Accessories
			{

				if( cbo_pi_basis_id==1)
				{
					for (var j=1; j<=row_num; j++)
					{
						var updateIdDtls=$('#updateIdDtls_'+j).val();
						if($('#workOrderChkbox_'+j).is(':checked') || updateIdDtls!="")
						{
							if (form_validation('workOrderNo_'+j+'*quantity_'+j+'*rate_'+j,'WO*Qunatity*Rate')==false)
							{
								return;
							}

							i++;
							//data_all+=get_submitted_data_string('workOrderNo_'+i+'*hideWoId_'+i+'*hideWoDtlsId_'+i+'*itemgroupid_'+i+'*itemdescription_'+i+'*colorName_'+i+'*sizeName_'+i+'*itemColor_'+i+'*itemSize_'+i+'*uom_'+i+'*quantity_'+i+'*rate_'+i+'*amount_'+i+'*updateIdDtls_'+i,"../../"); +'*hideTransData_'+i+'*itemProdId_'+i
							if(cbo_goods_rcv_status==1 && goods_rcv_variable !=1)
							{
								data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&hidePoId_" + i + "='" + $('#hidePoId_'+j).val()+"'"+"&poNo_" + i + "='" + trim($('#poNo_'+j).text())+"'"+"&poRef_" + i + "='" + trim($('#poNo_'+j).attr("title"))+"'"+"&styleRef_" + i + "='" + trim($('#styleRef_'+j).text())+"'"+"&hsCode_" + i + "='" + $('#hsCode_'+j).val()+"'"+"&itemgroupid_" + i + "='" + $('#itemgroupid_'+j).attr("title")+"'"+"&itemdescription_" + i + "='" + encodeURIComponent(trim($('#itemdescription_'+j).text()))+"'"+"&colorName_" + i + "='" + encodeURIComponent(trim($('#colorName_'+j).text()))+"'"+"&sizeName_" + i + "='" + encodeURIComponent(trim($('#sizeName_'+j).text()))+"'"+"&itemColor_" + i + "='" + encodeURIComponent(trim($('#itemColor_'+j).text()))+"'"+"&itemSize_" + i + "='" + encodeURIComponent(trim($('#itemSize_'+j).text()))+"'"+"&uom_" + i + "='" + $('#uom_'+j).attr("title")+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'"+"&bookingWithoutOrder_" + i + "='" + $('#bookingWithoutOrder_'+j).val()+"'"+"&brandSupRef_" + i + "='" + encodeURIComponent(trim($('#brandSupRef_'+j).text()))+"'"+"&hideTransData_" + i + "='" + $('#hideTransData_'+j).val()+"'"+"&itemProdId_" + i + "='" + $('#itemProdId_'+j).val()+"'";
							}
							else
							{
								data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&hidePoId_" + i + "='" + $('#hidePoId_'+j).val()+"'"+"&poNo_" + i + "='" + trim($('#poNo_'+j).text())+"'"+"&poRef_" + i + "='" + trim($('#poNo_'+j).attr("title"))+"'"+"&styleRef_" + i + "='" + trim($('#styleRef_'+j).text())+"'"+"&hsCode_" + i + "='" + $('#hsCode_'+j).val()+"'"+"&itemgroupid_" + i + "='" + $('#itemgroupid_'+j).attr("title")+"'"+"&itemdescription_" + i + "='" + encodeURIComponent(trim($('#itemdescription_'+j).text()))+"'"+"&colorName_" + i + "='" + encodeURIComponent(trim($('#colorName_'+j).text()))+"'"+"&sizeName_" + i + "='" + encodeURIComponent(trim($('#sizeName_'+j).text()))+"'"+"&itemColor_" + i + "='" + encodeURIComponent(trim($('#itemColor_'+j).text()))+"'"+"&itemSize_" + i + "='" + encodeURIComponent(trim($('#itemSize_'+j).text()))+"'"+"&uom_" + i + "='" + $('#uom_'+j).attr('title')+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'"+"&bookingWithoutOrder_" + i + "='" + $('#bookingWithoutOrder_'+j).val()+"'"+"&brandSupRef_" + i + "='" + encodeURIComponent(trim($('#brandSupRef_'+j).text()))+"'"+"&hideTransData_" + i + "='" + $('#hideTransData_'+j).val()+"'"+"&itemProdId_" + i + "='" + $('#itemProdId_'+j).val()+"'";
							}

							txt_total_amount+=$('#amount_'+j).val()*1;
							if($('#workOrderChkbox_'+j).is(':checked')) selected_row++;
						}
					}
				}
				else
				{
					if(cbo_goods_rcv_status==1 && goods_rcv_variable !=1)
					{
						var z=0;
						$("#tbl_pi_item").find('tbody tr').each(function() {

							var hsCode = $(this).find('input[name="hsCode[]"]').val();
            				var itemgroupid = $(this).find('select[name="itemgroupid[]"]').val();
            				var itemdescription = $(this).find('input[name="itemdescription[]"]').val();
            				var colorName = $(this).find('input[name="colorName[]"]').val();
            				var sizeName = $(this).find('input[name="sizeName[]"]').val();
            				var itemColor = $(this).find('input[name="itemColor[]"]').val();
            				var itemSize = $(this).find('input[name="itemSize[]"]').val();
            				var uom = $(this).find('select[name="uom[]"]').val();
            				var quantity = $(this).find('input[name="quantity[]"]').val();
            				var rate = $(this).find('input[name="rate[]"]').val();
            				var amount = $(this).find('input[name="amount[]"]').val();
            				var updateIdDtls = $(this).find('input[name="updateIdDtls[]"]').val();
            				var brandSupRef = $(this).find('input[name="brandSupRef[]"]').val();
            				var hideTransData = $(this).find('input[name="hideTransData[]"]').val();
            				var itemProdId = $(this).find('input[name="itemProdId[]"]').val();

            				txt_total_amount+=amount*1;
            				selected_row++;
            				i++;

            				if (itemgroupid==0 || quantity=="" || rate=="") {
            					z=1;
            				}

            				data_all +='&hsCode_' + i + '=' + hsCode + '&itemgroupid_' + i + '=' + itemgroupid + '&itemdescription_' + i + '=' + itemdescription + '&colorName_' + i + '=' + colorName + '&sizeName_' + i + '=' + sizeName + '&itemColor_' + i + '=' + itemColor + '&itemSize_' + i + '=' + itemSize + '&uom_' + i + '=' + uom + '&quantity_' + i + '=' + quantity + '&rate_' + i + '=' + rate + '&amount_' + i + '=' + amount + '&updateIdDtls_' + i + '=' + updateIdDtls + '&brandSupRef_' + i + '=' + brandSupRef+ '&hideTransData_' + i + '=' + hideTransData+ '&itemProdId_' + i + '=' + itemProdId;

            			});

            			if (z==1) {
			    			alert('You must fill the Item Group, Quantity and Rate');
			    			return;
			    		}
					}
					else
					{
						var z=0;
						$("#tbl_pi_item").find('tbody tr').each(function() {

							var hsCode = $(this).find('input[name="hsCode[]"]').val();
            				var itemgroupid = $(this).find('select[name="itemgroupid[]"]').val();
            				var itemdescription = $(this).find('input[name="itemdescription[]"]').val();
            				var colorName = $(this).find('input[name="colorName[]"]').val();
            				var sizeName = $(this).find('input[name="sizeName[]"]').val();
            				var itemColor = $(this).find('input[name="itemColor[]"]').val();
            				var itemSize = $(this).find('input[name="itemSize[]"]').val();
            				var uom = $(this).find('select[name="uom[]"]').val();
            				var quantity = $(this).find('input[name="quantity[]"]').val();
            				var rate = $(this).find('input[name="rate[]"]').val();
            				var amount = $(this).find('input[name="amount[]"]').val();
            				var updateIdDtls = $(this).find('input[name="updateIdDtls[]"]').val();
            				var brandSupRef = $(this).find('input[name="brandSupRef[]"]').val();    

            				txt_total_amount+=amount*1;
            				selected_row++;
            				i++;

            				if (itemgroupid==0 || itemdescription=="" || quantity=="" || rate=="") {
            					z=1;
            				}

            				data_all +='&hsCode_' + i + '=' + hsCode + '&itemgroupid_' + i + '=' + itemgroupid + '&itemdescription_' + i + '=' + itemdescription + '&colorName_' + i + '=' + colorName + '&sizeName_' + i + '=' + sizeName + '&itemColor_' + i + '=' + itemColor + '&itemSize_' + i + '=' + itemSize + '&uom_' + i + '=' + uom + '&quantity_' + i + '=' + quantity + '&rate_' + i + '=' + rate + '&amount_' + i + '=' + amount + '&updateIdDtls_' + i + '=' + updateIdDtls + '&brandSupRef_' + i + '=' + brandSupRef;
            			});

            			if (z==1) {
			    			alert('You must fill the Item Group, Item Description, Quantity and Rate');
			    			return;
			    		}

					}

				}
			}

			else if(cbo_item_category_id==12)  // Services - Fabric
			{
				if(cbo_pi_basis_id==1)
				{
					for (var j=1; j<=row_num; j++)
					{
						var updateIdDtls=$('#updateIdDtls_'+j).val();
						if($('#workOrderChkbox_'+j).is(':checked') || updateIdDtls!="")
						{
							if (form_validation('workOrderNo_'+j+'*quantity_'+j+'*rate_'+j,'WO*Qunatity*Rate')==false)
							{
								return;
							}

							i++;
							//data_all+=get_submitted_data_string('workOrderNo_'+i+'*hideWoId_'+i+'*hideWoDtlsId_'+i+'*servicetype_'+i+'*itemdescription_'+i+'*colorName_'+i+'*sizeName_'+i+'*itemColor_'+i+'*itemSize_'+i+'*uom_'+i+'*quantity_'+i+'*rate_'+i+'*amount_'+i+'*updateIdDtls_'+i,"../../");

							data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&servicetype_" + i + "='" + $('#servicetype_'+j).val()+"'"+"&itemdescription_" + i + "='" + $('#itemdescription_'+j).val()+"'"+"&colorName_" + i + "='" + $('#colorName_'+j).val()+"'"+"&sizeName_" + i + "='" + $('#sizeName_'+j).val()+"'"+"&itemColor_" + i + "='" + $('#itemColor_'+j).val()+"'"+"&itemSize_" + i + "='" + $('#itemSize_'+j).val()+"'"+"&uom_" + i + "='" + $('#uom_'+j).val()+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'" +"&bookingWithoutOrder_" + i + "='" + $('#bookingWithoutOrder_'+j).val()+"'";

							txt_total_amount+=$('#amount_'+j).val()*1;
							if($('#workOrderChkbox_'+j).is(':checked')) selected_row++;
						}
					}
				}
				else
				{
					var z=0;
					$("#tbl_pi_item").find('tbody tr').each(function() {
						var servicetype = $(this).find('select[name="servicetype[]"]').val();
						var itemdescription = $(this).find('input[name="itemdescription[]"]').val();
						var colorName = $(this).find('input[name="colorName[]"]').val();
        				var sizeName = $(this).find('input[name="sizeName[]"]').val();
        				var itemColor = $(this).find('input[name="itemColor[]"]').val();
        				var itemSize = $(this).find('input[name="itemSize[]"]').val();
        				var uom = $(this).find('select[name="uom[]"]').val();        				
        				var quantity = $(this).find('input[name="quantity[]"]').val();
        				var rate = $(this).find('input[name="rate[]"]').val();
        				var amount = $(this).find('input[name="amount[]"]').val();
        				var updateIdDtls = $(this).find('input[name="updateIdDtls[]"]').val();

        				if (servicetype=='' || itemdescription=='' || quantity=='' || rate=='') {
        					z=1;
        				}

       					i++;

        				data_all +='&servicetype_' + i + '=' + servicetype + '&itemdescription_' + i + '=' + itemdescription + '&colorName_' + i + '=' + colorName + '&sizeName_' + i + '=' + sizeName + '&itemColor_' + i + '=' + itemColor + '&itemSize_' + i + '=' + itemSize + '&uom_' + i + '=' + uom + '&quantity_' + i + '=' + quantity + '&rate_' + i + '=' + rate + '&amount_' + i + '=' + amount + '&updateIdDtls_' + i + '=' + updateIdDtls;

        				txt_total_amount+=amount*1;
						selected_row++;
        			});

        			if (z==1) {
		    			alert('You must fill the Item Group, Item Description, Quantity, Rate');
		    			return;
		    		}					
				}
			}

			else if(cbo_item_category_id==24)  // Services - Yarn Dyeing
			{
				if(cbo_pi_basis_id==1)
				{
					for (var j=1; j<=row_num; j++)
					{
						var updateIdDtls=$('#updateIdDtls_'+j).val();
						// var colorId=$('#colorId_'+j).val();
						if($('#workOrderChkbox_'+j).is(':checked') || updateIdDtls!="")
						{
							if (form_validation('workOrderNo_'+j+'*quantity_'+j+'*rate_'+j,'WO*Qunatity*Rate')==false)
							{
								return;
							}

							i++;

							data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&lot_" + i + "='" + $('#lot_'+j).val()+"'"+"&itemProdId_" + i + "='" + $('#itemProdId_'+j).val()+"'"+"&countName_" + i + "='" + $('#countName_'+j).val()+"'"+"&itemdescription_" + i + "='" + $('#itemdescription_'+j).val()+"'"+"&yarnColor_" + i + "='" + $('#yarnColor_'+j).val()+"'"+"&colorRange_" + i + "='" + $('#colorRange_'+j).val()+"'"+"&uom_" + i + "='" + $('#uom_'+j).val()+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'"+"&hideTransData_" + i + "='" + $('#hideTransData_'+j).val()+"'" +"&bookingWithoutOrder_" + i + "='" + $('#bookingWithoutOrder_'+j).val()+"'";

							txt_total_amount+=$('#amount_'+j).val()*1;
							if($('#workOrderChkbox_'+j).is(':checked')) selected_row++;
						}
					}
				}
				else
				{

					var z=0;

					$("#tbl_pi_item").find('tbody tr').each(function() {
						var lot = $(this).find('input[name="lot[]"]').val();
						var itemProdId = $(this).find('input[name="itemProdId[]"]').val();
						var colorName = $(this).find('input[name="colorName[]"]').val();
						var colorId = $(this).find('input[name="colorId[]"]').val();
        				var countName = $(this).find('select[name="countName[]"]').val();
        				var itemdescription = $(this).find('input[name="itemdescription[]"]').val();        				
        				var colorRange = $(this).find('select[name="colorRange[]"]').val();        				
        				var uom = $(this).find('select[name="uom[]"]').val();        				
        				var quantity = $(this).find('input[name="quantity[]"]').val();
        				var rate = $(this).find('input[name="rate[]"]').val();
        				var amount = $(this).find('input[name="amount[]"]').val();
        				var updateIdDtls = $(this).find('input[name="updateIdDtls[]"]').val();

        				if (lot=='' || quantity=='' || rate=='') {
        					z=1;
        				}

       					i++;

        				data_all +='&lot_' + i + '=' + lot + '&itemProdId_' + i + '=' + itemProdId + '&colorName_' + i + '=' + colorName + '&yarnColor_' + i + '=' + colorId + '&countName_' + i + '=' + countName + '&itemdescription_' + i + '=' + itemdescription + '&colorRange_' + i + '=' + colorRange + '&uom_' + i + '=' + uom + '&quantity_' + i + '=' + quantity + '&rate_' + i + '=' + rate + '&amount_' + i + '=' + amount + '&updateIdDtls_' + i + '=' + updateIdDtls;

        				txt_total_amount+=amount*1;
						selected_row++;
        			});

        			if (z==1) {
		    			alert('You must fill the Lot No, Qunatity and Rate');
		    			return;
		    		}
					
				}
			}

			else if(cbo_item_category_id==25)  // Services - Embellishment
			{
				if(cbo_pi_basis_id==1)
				{
					for (var j=1; j<=row_num; j++)
					{
						var updateIdDtls=$('#updateIdDtls_'+j).val();
						if($('#workOrderChkbox_'+j).is(':checked') || updateIdDtls!="")
						{
							if (form_validation('workOrderNo_'+j+'*quantity_'+j+'*rate_'+j,'WO*Qunatity*Rate')==false)
							{
								return;
							}

							i++;

							data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&gmtsitem_" + i + "='" + $('#gmtsitem_'+j).val()+"'"+"&embellname_" + i + "='" + $('#embellname_'+j).val()+"'"+"&embelltype_" + i + "='" + $('#embelltype_'+j).val()+"'"+"&colorName_" + i + "='" + $('#colorName_'+j).val()+"'"+"&uom_" + i + "='" + $('#uom_'+j).val()+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'";

							txt_total_amount+=$('#amount_'+j).val()*1;
							if($('#workOrderChkbox_'+j).is(':checked')) selected_row++;
						}
					}
				}
				else
				{

					var z=0;

					$("#tbl_pi_item").find('tbody tr').each(function() {
        				var gmtsitem = $(this).find('select[name="gmtsitem[]"]').val();
        				var embellname = $(this).find('select[name="embellname[]"]').val();
        				var embelltype = $(this).find('select[name="embelltype[]"]').val();
        				var colorName = $(this).find('input[name="colorName[]"]').val();
        				var uom = $(this).find('select[name="uom[]"]').val();        				
        				var quantity = $(this).find('input[name="quantity[]"]').val();
        				var rate = $(this).find('input[name="rate[]"]').val();
        				var amount = $(this).find('input[name="amount[]"]').val();
        				var updateIdDtls = $(this).find('input[name="updateIdDtls[]"]').val();

        				if (gmtsitem==0 || embellname==0 || embelltype==0 || quantity=='' || rate=='') {
        					z=1;
        				}

       					i++;

        				data_all +='&gmtsitem_' + i + '=' + gmtsitem + '&embellname_' + i + '=' + embellname + '&embelltype_' + i + '=' + embelltype + '&colorName_' + i + '=' + colorName + '&uom_' + i + '=' + uom + '&quantity_' + i + '=' + quantity + '&rate_' + i + '=' + rate + '&amount_' + i + '=' + amount + '&updateIdDtls_' + i + '=' + updateIdDtls;

        				txt_total_amount+=amount*1;
						selected_row++;
        			});

        			if (z==1) {
		    			alert('You must fill the Gmts. Item, Embell Name, Embell Type, Qunatity and Rate');
		    			return;
		    		}
					
				}
			}

			else if(cbo_item_category_id==31 || cbo_item_category_id==115)  // Services Lab Test Services Inspection
			{
				//alert(31);
				if(cbo_pi_basis_id==1)
				{
					for (var j=1; j<=row_num; j++)
					{
						var updateIdDtls=$('#updateIdDtls_'+j).val();
						if($('#workOrderChkbox_'+j).is(':checked') || updateIdDtls!="")
						{
							if (form_validation('workOrderNo_'+j+'*amount_'+j,'WO*Amount')==false)
							{
								return;
							}

							i++;

							data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&cboTestFor_" + i + "='" + $('#cboTestFor_'+j).val()+"'"+"&remarks_" + i + "='" + $('#remarks_'+j).val()+"'"+"&colorName_" + i + "='" + $('#colorName_'+j).val()+"'"+"&testItemId_" + i + "='" + $('#testItemId_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'";
							//alert(data_all);
							txt_total_amount+=$('#amount_'+j).val()*1;
							if($('#workOrderChkbox_'+j).is(':checked')) selected_row++;
						}
					}
				}
				else
				{
					var z=0;

					$("#tbl_pi_item").find('tbody tr').each(function() {
        				var cboTestFor = $(this).find('select[name="cboTestFor[]"]').val();
        				var remarks = $(this).find('input[name="remarks[]"]').val();
        				var colorName = $(this).find('input[name="colorName[]"]').val();
        				var testItemId = $(this).find('input[name="testItemId[]"]').val();
        				var amount = $(this).find('input[name="amount[]"]').val();
        				var updateIdDtls = $(this).find('input[name="updateIdDtls[]"]').val();

        				if (cboTestFor==0 || amount=='') {
        					z=1;
        				}

       					i++;

        				data_all +='&cboTestFor_' + i + '=' + cboTestFor + '&remarks_' + i + '=' + remarks + '&colorName_' + i + '=' + colorName + '&testItemId_' + i + '=' + testItemId + '&amount_' + i + '=' + amount + '&updateIdDtls_' + i + '=' + updateIdDtls;

        				txt_total_amount+=amount*1;
						selected_row++;
        			});

        			if (z==1) {
		    			alert('You must fill the Test For and Amount');
		    			return;
		    		}

				}
			}
			
			else if(cbo_item_category_id==116 || cbo_item_category_id==100)  // Services - Garments
			{
				for (var j=1; j<=row_num; j++)
				{
					var updateIdDtls=$('#updateIdDtls_'+j).val();
					if($('#workOrderChkbox_'+j).is(':checked') || updateIdDtls!="")
					{
						if (form_validation('workOrderNo_'+j+'*quantity_'+j+'*rate_'+j,'WO*Qunatity*Rate')==false)
						{
							return;
						}

						i++;
						data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&gmtsitem_" + i + "='" + $('#itemdescription_'+j).attr("title")+"'"+"&itemdescription_" + i + "='" + $('#itemdescription_'+j).val()+"'"+"&testItemId_" + i + "='" + $('#processName_'+j).attr("title")+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'";

						txt_total_amount+=$('#amount_'+j).val()*1;
						if($('#workOrderChkbox_'+j).is(':checked')) selected_row++;
					}
				}
			}

			else
			{
				if(cbo_pi_basis_id==1)
				{
					for (var j=1; j<=row_num; j++)
					{
						var updateIdDtls=$('#updateIdDtls_'+j).val();

						if($('#workOrderChkbox_'+j).is(':checked') || updateIdDtls!="")
						{
							if (form_validation('workOrderNo_'+j+'*quantity_'+j+'*rate_'+j,'WO*Qunatity*Rate')==false)
							{
								return;
							}

							i++;
							//data_all+=get_submitted_data_string('workOrderNo_'+j+'*hideWoId_'+j+'*hideWoDtlsId_'+j+'*itemgroupid_'+j+'*itemdescription_'+j+'*itemSize_'+j+'*uom_'+j+'*quantity_'+j+'*rate_'+j+'*amount_'+j+'*itemProdId_'+j+'*updateIdDtls_'+j,"../../");

							data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&hsCode_" + i + "='" + $('#hsCode_'+j).val()+"'"+"&itemgroupid_" + i + "='" + $('#itemgroupid_'+j).val()+"'"+"&itemdescription_" + i + "='" + $('#itemdescription_'+j).val()+"'"+"&itemSize_" + i + "='" + $('#itemSize_'+j).val()+"'"+"&uom_" + i + "='" + $('#uom_'+j).val()+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&itemProdId_" + i + "='" + $('#itemProdId_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'"+"&hideTransData_" + i + "='" + $('#hideTransData_'+j).val()+"'"+"&itemcategory_" + i + "='" + $('#itemcategory_'+j).val()+"'";

							txt_total_amount+=$('#amount_'+j).val()*1;
							if($('#workOrderChkbox_'+j).is(':checked')) selected_row++;
						}
					}
				}
				else
				{
					var z=0;

					$("#tbl_pi_item").find('tbody tr').each(function() {
						var hsCode = $(this).find('input[name="hsCode[]"]').val();
        				var itemgroupid = $(this).find('select[name="itemgroupid[]"]').val();        				
        				var itemdescription = $(this).find('input[name="itemdescription[]"]').val();
        				var itemSize = $(this).find('input[name="itemSize[]"]').val();
        				var uom = $(this).find('select[name="uom[]"]').val();
        				var quantity = $(this).find('input[name="quantity[]"]').val();
        				var rate = $(this).find('input[name="rate[]"]').val();
        				var amount = $(this).find('input[name="amount[]"]').val();
        				var itemProdId = $(this).find('input[name="itemProdId[]"]').val();
        				var updateIdDtls = $(this).find('input[name="updateIdDtls[]"]').val();
						var itemcategory = $(this).find('select[name="itemcategory[]"]').val();

        				if (itemgroupid==0 || itemdescription=='' || quantity=='' || rate=='') {
        					z=1;
        				}

       					i++;

        				data_all +='&hsCode_' + i + '=' + hsCode + '&itemgroupid_' + i + '=' + itemgroupid + '&itemdescription_' + i + '=' + itemdescription + '&itemSize_' + i + '=' + itemSize + '&uom_' + i + '=' + uom + '&quantity_' + i + '=' + quantity + '&rate_' + i + '=' + rate + '&amount_' + i + '=' + amount + '&itemProdId_' + i + '=' + itemProdId + '&updateIdDtls_' + i + '=' + updateIdDtls+ '&itemcategory_' + i + '=' + itemcategory;

        				txt_total_amount+=amount*1;
						selected_row++;
        			});

        			if (z==1) {
		    			alert('You must fill the Item Group, Item Description, Qunatity and Rate');
		    			return;
		    		}
					
				}
			}
		}
		
		//alert(data_all);return;
		if(selected_row<1 && cbo_pi_basis_id!=2)
		{
			alert("Please Select WO");
			return;
		}
		//alert(data_all);return;
		txt_total_amount_net=txt_total_amount*1+txt_upcharge*1-txt_discount*1;

		var data="action=save_update_delete_dtls&operation="+operation+'&cbo_item_category_id='+cbo_item_category_id+'&cbo_pi_basis_id='+cbo_pi_basis_id+'&is_lc_sc='+is_lc_sc+'&lc_sc_id='+lc_sc_id+'&cbo_ready_to_approved='+cbo_ready_to_approved+'&total_row='+i+'&update_id='+update_id+'&txt_total_amount='+txt_total_amount+'&txt_upcharge='+txt_upcharge+'&up_charge_break_down='+up_charge_break_down+'&txt_discount='+txt_discount+'&discount_break_down='+discount_break_down+'&txt_total_amount_net='+txt_total_amount_net+'&txt_deleted_id='+txt_deleted_id+'&cbo_currency_id='+cbo_currency_id+'&cbo_goods_rcv_status='+cbo_goods_rcv_status+'&cbo_importer_id='+cbo_importer_id+'&txt_order_type='+txt_order_type+'&export_pi_id='+export_pi_id+data_all+data_all_checked;

		//alert(data); return; //txt_order_type
		freeze_window(operation);
		http.open("POST","requires/pi_controller_urmi.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_pi_item_details_reponse;
	}

	function fnc_pi_item_details_reponse()
	{
		if(http.readyState == 4)
		{
			//alert(http.responseText);release_freezing();
			var reponse=http.responseText.split('**');
			//alert(reponse[0]); return;			

			if((reponse[0]==0 || reponse[0]==1 || reponse[0]==2))
			{
				var cbo_pi_basis_id = document.getElementById('cbo_pi_basis_id').value;
				var item_category = document.getElementById('cbo_item_category_id').value;
				var goods_rcv_status = document.getElementById('cbo_goods_rcv_status').value;
				var company_id = document.getElementById('cbo_importer_id').value;
				
				show_list_view(cbo_pi_basis_id+'_'+item_category+'_'+'2'+'_'+reponse[1]+'_'+goods_rcv_status+'_'+company_id, 'pi_details', 'pi_details_container', 'requires/pi_controller_urmi', '' ) ;
				
				var numRow = $('table#tbl_pi_item tbody tr').length;
				//alert(numRow);release_freezing();return;
				if(reponse[0]==2)
				{
					if(item_category==31)
					{
						if(numRow==1 && (trim($('#amount_'+numRow).val())==""))
						{
							numRow=0;
						}
					}
					else
					{
						if(numRow==1 && (trim($('#quantity_'+numRow).val())==""))
						{
							numRow=0;
						}
					}
					
					$('#txt_tot_row').val(numRow);
				}
				else
				{
					$('#txt_tot_row').val(numRow);
				}

				//if(cbo_pi_basis_id==2 && (item_category==2 || item_category==3 || item_category==13 || item_category==14))
				//{
					//add_break_down_tr(numRow);
				//}
				
				// setFieldLevelAccess(company_id);
			}
			else if(reponse[0]==11)
			{
				alert(reponse[1]);release_freezing();return;
			}
			else if(reponse[0]==14)
			{
				alert(reponse[1]);release_freezing();return;
			}
			else if(reponse[0]==15)
			{
				alert(reponse[1]);release_freezing();return;
			}
			else if(reponse[0]==16)
			{
				alert("This PI is already Approved. So You can't change it.");release_freezing();return;
			}
			show_msg(trim(reponse[0]));
			set_button_status(reponse[2], permission, 'fnc_pi_item_details',2);
			release_freezing();
		}
	}

	function openmypage()
	{
		var item_category 	= $('#cbo_item_category_id').val();
		var importer_id 	= $('#cbo_importer_id').val();
		var supplier_id		= $('#cbo_supplier_id').val();

		var title = 'PI Selection Form';
		var page_link = 'requires/pi_controller_urmi.php?item_category_id='+item_category+'&importer_id='+importer_id+'&supplier_id='+supplier_id+'&action=pi_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=980px,height=450px,center=1,resize=1,scrolling=0','../');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theemail=this.contentDoc.getElementById("txt_selected_pi_id").value.split("_"); //Access form field with id="emailfield"
			var pi_id=theemail[0];
			var export_pi_id=theemail[1];
			var item_category_id=theemail[6];

			if(pi_id!="" && item_category_id>0)
			{					
				freeze_window(5);
				$('#txt_not_approved_cause').attr('disabled',false);
				//show_list_view(document.getElementById('cbo_pi_basis_id').value+'_'+item_category_id+'_'+'1', 'pi_details', 'pi_details_container', 'requires/pi_controller_urmi', '' );
				$('#cbo_item_category_id').val(item_category_id);
				if(export_pi_id>0)
				{
					show_list_view(document.getElementById('cbo_pi_basis_id').value+'_'+item_category_id+'_'+'1', 'pi_details', 'pi_details_container', 'requires/pi_controller_urmi', '' );
					get_php_form_data( pi_id, "populate_data_from_search_popup", "requires/pi_controller_urmi" );
					get_php_form_data($('#cbo_importer_id').val(),'print_button_variable_setting','requires/pi_controller_urmi' );
					disable_enable_fields('cbo_item_category_id*cbo_importer_id*cbo_supplier_id*pi_number*pi_date*last_shipment_date*pi_validity_date*cbo_currency_id*hs_code*txt_internal_file_no*cbo_pi_basis_id',1,'','');
					show_list_view(pi_id+"**"+theemail[2]+"**"+theemail[3]+"**"+theemail[4]+"**"+theemail[5],'export_pi_details_update','pi_details_container','requires/pi_controller_urmi','');
					set_button_status(1, permission, 'fnc_pi_mst',1);
					release_freezing();
				}
				else
				{
					get_php_form_data( pi_id, "populate_data_from_search_popup", "requires/pi_controller_urmi" );
					get_php_form_data($('#cbo_importer_id').val(),'print_button_variable_setting','requires/pi_controller_urmi' );
					//alert(cbo_pi_basis_id);
					var com_id=$('#cbo_importer_id').val();
					var color_from_library_arr = JSON.parse('<? echo json_encode($color_from_library_arr); ?>');
					$("#txt_color_form_lib").val(color_from_library_arr[com_id]);
					show_list_view($('#cbo_pi_basis_id').val()+'_'+item_category_id+'_'+'2'+'_'+pi_id+'_'+$('#cbo_goods_rcv_status').val()+'_'+$('#cbo_importer_id').val(),'pi_details','pi_details_container', 'requires/pi_controller_urmi','');
					var txt_tot_row = $('#txt_tot_row').val();
					if(txt_tot_row==0)
						set_button_status(0, permission, 'fnc_pi_item_details',2);
					else
						set_button_status(1, permission, 'fnc_pi_item_details',2);

					var cbo_pi_basis_id=$('#cbo_pi_basis_id').val();
					if(cbo_pi_basis_id==2 && (item_category==2 || item_category==13 || item_category==14))
					{
						add_break_down_tr(txt_tot_row);
					}
					calculate_total_amount(1);
					// setFieldLevelAccess(importer_id);
					release_freezing();
				}
				
			}
		}
	}

	function openmypage_fabricDescription(row_num)
	{
		var tot_row=$("#tbl_pi_item tbody tr").length;
		var item_category = $('#cbo_item_category_id').val();

		var prev_attached_id=fabricNature='';

		if(item_category==2 || item_category==13)
		{
			fabricNature=2;

		}
		else
		{
			fabricNature=3;
		}

		for(var j=1; j<=tot_row; j++)
		{
			var deter_id=$('#hideDeterminationId_'+j).val();

			if(deter_id!="")
			{
				if(prev_attached_id=="") prev_attached_id=deter_id; else prev_attached_id+=","+deter_id;
			}
		}

		var title = 'Fabric Description Info';
		var page_link = 'requires/pi_controller_urmi.php?action=fabricDescription_popup&fabricNature='+fabricNature+"&prev_attached_id="+prev_attached_id;

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=370px,center=1,resize=1,scrolling=0','../');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var txt_selected_id=this.contentDoc.getElementById("txt_selected_id").value;	 //Access form field with id="emailfield"
			var txt_selected=this.contentDoc.getElementById("txt_selected").value; //Access form field with id="emailfield"

			var determinationId_arr = txt_selected_id.split(',');
			var arr = txt_selected.split(',');
			var row_id=$("#tbl_pi_item tbody tr:last").attr('id').split('_');

			//var i=parseInt(row_id[1]);
			var i=$("#tbl_pi_item tbody tr").length;

			$(arr).each(function(index, element)
			{
				var all_data = this.split('**');
				var construction  = all_data[0];
				var composition  = all_data[1];
				var gsn_weight  = all_data[2];
				var fabric_ref  = all_data[3];
				var rd_no  		= all_data[4];
				var determinationId  = determinationId_arr[index];

				if(index!=0 )//|| constr!=""
				{
					var constr= $('#construction_'+i).val();
					if(constr!="")
					{
						//var last_row=$("#tbl_pi_item tbody tr:last").attr('id').split('_');
						//z=parseInt(row_id[1]);
						z=$("#tbl_pi_item tbody tr").length;

						add_break_down_tr(z);
						i++;
					}

					$('#construction_'+i).val(construction);
					$('#composition_'+i).val(composition);
					$('#hideDeterminationId_'+i).val(determinationId);
					if(item_category==3)
					{
						$('#fabDescription_'+i).val(construction+' '+composition);
						$('#fabWeight_'+i).val(gsn_weight);
						$('#fabricRef_'+i).val(fabric_ref);
						$('#rdNo_'+i).val(rd_no);
					}else{
						$('#gsm_'+i).val(gsn_weight);
					}

					/*if(item_category==2 || item_category==13)
					{
						$('#gsm_'+i).val(gsn_weight);
					}
					else
					{
						$('#weight_'+i).val(gsn_weight);
					}*/
				}
				else
				{
					$('#construction_'+row_num).val(construction);
					$('#composition_'+row_num).val(composition);
					$('#hideDeterminationId_'+row_num).val(determinationId);
					if(item_category==3)
					{
						$('#fabDescription_'+i).val(construction+' '+composition);
						$('#fabWeight_'+i).val(gsn_weight);
						$('#fabricRef_'+i).val(fabric_ref);
						$('#rdNo_'+i).val(rd_no);
					}else{
						$('#gsm_'+i).val(gsn_weight);
					}


					/*if(item_category==2 || item_category==13)
					{
						$('#gsm_'+row_num).val(gsn_weight);
					}
					else
					{
						$('#weight_'+row_num).val(gsn_weight);
					}*/
				}

				index++;
			});

			var last_row_id=$("#tbl_pi_item tbody tr:last").attr('id').split('_');
			var last_ro_no=last_row_id[1];
			var constr 	= $('#construction_'+last_ro_no).val();
			//if(constr!="") add_break_down_tr(last_ro_no);
		}
	}

	function openmypage_test_item(row_num)
	{
		if (form_validation('cboTestFor_'+row_num,'Test For')==false)
		{
			return;
		}

		var cboTestFor = $('#cboTestFor_'+row_num).val();
		var prev_attached_id=$('#testItemId_'+row_num).val();

		var title = 'Test Item Info';
		var page_link = 'requires/pi_controller_urmi.php?action=testItem_popup&cboTestFor='+cboTestFor+"&prev_attached_id="+prev_attached_id;

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=370px,center=1,resize=1,scrolling=0','../');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var txt_selected_id=this.contentDoc.getElementById("txt_selected_id").value;	 //Access form field with id="emailfield"
			var txt_selected=this.contentDoc.getElementById("txt_selected").value; //Access form field with id="emailfield"
			$('#testItemId_'+row_num).val(txt_selected_id);
			$('#txtTestItem_'+row_num).val(txt_selected);
		}
	}
	
	function return_global_ajax_value_sync( data, action, path, page_name) {

		if (!page_name) var page_name="";
		if (page_name=="")  page_name='includes/common_functions_for_js';
		return $.ajax({
		  url: path+page_name+".php?data="+data+"&action="+action,
		  contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
		  async: false
		}).responseText
	}

	function openmypage_wo(row_num)
	{
		//alert(row_num);
		var pi_mst_id		= $('#txt_system_id').val();
		var item_category	= $('#cbo_item_category_id').val();
		var importer_id 	= $('#cbo_importer_id').val();
		var supplier_id		= $('#cbo_supplier_id').val();
		var goods_rcv_status= $('#cbo_goods_rcv_status').val();
		var cbo_pi_basis_id= $('#cbo_pi_basis_id').val();

		if($('#txt_system_id').val()=="")
		{
			alert("Save Data First.");return;
		}

		if (form_validation('cbo_item_category_id','Item Category')==false)
		{
			return;
		}
		else
		{
			//$prev_pi_qty_arr[$row[csf('booking_no')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]][$row[csf('copmposition')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('uom')]]
			var prev_wo_ids=''; var prev_wo_feb_datas='';
			var row_num=$('#tbl_pi_item tbody tr').length;
			for (var j=1; j<=row_num; j++)
			{
				var hideWoDtlsId=$('#hideWoDtlsId_'+j).val();
				var updateIdDtls=trim($('#updateIdDtls_'+j).val());
				if(hideWoDtlsId!="")
				{
					if(updateIdDtls=="")
					{
						if(prev_wo_ids=="") prev_wo_ids=hideWoDtlsId; else prev_wo_ids+=","+hideWoDtlsId;
					}
				}
				
				if(item_category==2 || item_category==3)
				{

					var hideWoId=$('#hideWoId_'+j).val();
					var workOrderNo=$('#workOrderNo_'+j).val();
					var hideDeterminationId=$('#hideDeterminationId_'+j).val();
					var colorName=$('#colorName_'+j).val();
					var construction=$('#construction_'+j).val();
					var composition=$('#composition_'+j).val();
					var gsm=$('#gsm_'+j).val();
					var diawidth=$('#diawidth_'+j).val();
					var uom=$('#uom_'+j).val();
					if(hideWoId!="")
					{
						if(prev_wo_feb_datas=="") prev_wo_feb_datas=hideWoId+"**"+workOrderNo+"**"+hideDeterminationId+"**"+colorName+"**"+construction+"**"+composition+"**"+gsm+"**"+diawidth+"**"+uom;
						else prev_wo_feb_datas+="__"+hideWoId+"**"+workOrderNo+"**"+hideDeterminationId+"**"+colorName+"**"+construction+"**"+composition+"**"+gsm+"**"+diawidth+"**"+uom;
					}
				}
			}
			//alert(prev_wo_feb_datas);return;
			var title = 'WO Selection Form';
			var page_link = 'requires/pi_controller_urmi.php?item_category_id='+item_category+'&importer_id='+importer_id+'&supplier_id='+supplier_id+'&goods_rcv_status='+goods_rcv_status+'&prev_wo_ids='+prev_wo_ids+'&prev_wo_feb_datas='+prev_wo_feb_datas+'&pi_mst_id='+pi_mst_id+'&action=wo_popup';
			//alert(page_link);

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=450px,center=1,resize=1,scrolling=0','../');

			emailwindow.onclose=function()
			{

				var theform=this.contentDoc.forms[0]; //("search_order_frm"); //Access the form inside the modal window
				var theemail=this.contentDoc.getElementById("txt_selected_wo_id"); //Access form field with id="emailfield"
				var order_type=this.contentDoc.getElementById("order_non_order_type").value; //Access form field with id="emailfield"
				var prev_order_type=$('#txt_order_type').val();
				//alert(prev_order_type + "="+ order_type);
				if(prev_order_type !="")
				{
					if(prev_order_type*1 != order_type*1 && (item_category ==4 || item_category ==2 || item_category ==3 ||  item_category ==13 ||  item_category ==14 ||  item_category ==12 ||  item_category ==24 ||  item_category ==25 ||  item_category ==31 ||  item_category ==115))
					{
						alert("Order Non Order Mix Not Allow");return;
					}
				}
				else
				{
					$('#txt_order_type').val(order_type);
				}
				
				if (theemail.value!="")
				{
					var tot_row=$('#txt_tot_row').val();
					var data=theemail.value+"**"+item_category+"**"+tot_row+"**"+order_type+"**"+goods_rcv_status+"**"+importer_id+"**"+cbo_pi_basis_id;

					//var list_view_wo =trim(return_global_ajax_value( data, 'populate_data_wo_form', '', 'requires/pi_controller_urmi'));

					/*if(list_view_wo=="")
					{
						alert("This Work order has already been taged to another PI.");
						return;
					}*/


					freeze_window(5);
					var list_view_wo =trim(return_global_ajax_value_post( data, 'populate_data_wo_form', '', 'requires/pi_controller_urmi'));
					//alert(list_view_wo);return;
					release_freezing();
					if(list_view_wo!="")
					{
						var wo_no=$('#workOrderNo_'+row_num).val();

						if(wo_no=="")
						{
							$("#row_"+row_num).remove();
						}

						$('#cbo_importer_id').attr('disabled',true);
						$('#cbo_supplier_id').attr('disabled',true);

						$("#tbl_pi_item tbody:last").append(list_view_wo);

						var numRow = $('table#tbl_pi_item tbody tr').length;
						$('#txt_tot_row').val(numRow);
						calculate_total_amount(1);
						//setFieldLevelAccess(importer_id);
					}
					if(item_category==4)
					{
						setFilterGrid("tbl_pi_item",-1);
					}
					
					release_freezing();
				}
			}
		}
	}

	function openmypage_item_desc(row_num)
	{
		//alert(row_num);return;
		var item_category = $('#cbo_item_category_id').val();
		var cbo_importer_id = $('#cbo_importer_id').val();
		var cbo_supplier_id = $('#cbo_supplier_id').val();
		if(item_category==24) item_category=1;
		if (form_validation('cbo_item_category_id*txt_system_id','Item Category*PI Id')==false)
		{
			alert("Please Save Master Part");return;
		}
		else
		{
			var title = 'Item Description Form';
			var page_link = 'requires/pi_controller_urmi.php?item_category='+item_category+'&action=itemDesc_popup'+'&cbo_importer_id='+cbo_importer_id+'&cbo_supplier_id='+cbo_supplier_id;

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=820px,height=450px,center=1,resize=1,scrolling=0','../');

			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var theemail=this.contentDoc.getElementById("txt_selected_item_id") //Access form field with id="emailfield"

				if (theemail.value!="")
				{
					//freeze_window(5);

					var tot_row=$('#txt_tot_row').val();
					var data=theemail.value+"**"+item_category+"**"+tot_row;

					var list_view_wo =return_global_ajax_value( data, 'populate_data_item_form', '', 'requires/pi_controller_urmi');
					//alert(list_view_wo);
					if(item_category==1) var item_desc=$('#lot_'+row_num).val();
					else var item_desc=$('#itemdescription_'+row_num).val();

					if(item_desc=="")
					{
						$("#row_"+row_num).remove();
					}

					$("#tbl_pi_item tbody:last").append(list_view_wo);

					var numRow = $('table#tbl_pi_item tbody tr').length;
					$('#txt_tot_row').val(numRow);
					calculate_total_amount(1);
					//release_freezing();
				}
			}
		}
	}

	function control_composition(id,type)
	{
		//alert();
		var cbocompone=(document.getElementById('yarnCompositionItem1_'+id).value);
		var cbocomptwo=(document.getElementById('yarnCompositionItem2_'+id).value);
		var percentone=(document.getElementById('yarnCompositionPercentage1_'+id).value)*1;
		var percenttwo=(document.getElementById('yarnCompositionPercentage2_'+id).value)*1;
		var row_num=$('#tbl_pi_item tbody tr').length;

		if(type=='percent_one' && percentone>100)
		{
			alert("Greater Than 100 Not Allwed");
			document.getElementById('yarnCompositionPercentage1_'+id).value="";
		}

		if(type=='percent_one' && percentone<=0)
		{
			alert("0 Or Less Than 0 Not Allwed")
			document.getElementById('yarnCompositionPercentage1_'+id).value="";
			document.getElementById('yarnCompositionPercentage1_'+id).disabled=true;
			document.getElementById('yarnCompositionItem1_'+id).value=0;
			document.getElementById('yarnCompositionItem1_'+id).disabled=true;
			document.getElementById('yarnCompositionPercentage2_'+id).value=100;
			document.getElementById('yarnCompositionPercentage2_'+id).disabled=false;
			document.getElementById('yarnCompositionItem2_'+id).disabled=false;
		}
		if(type=='percent_one' && percentone==100)
		{
			document.getElementById('yarnCompositionPercentage2_'+id).value="";
			document.getElementById('yarnCompositionItem2_'+id).value=0;
			document.getElementById('yarnCompositionPercentage2_'+id).disabled=true;
			document.getElementById('yarnCompositionItem2_'+id).disabled=true;
		}

		if(type=='percent_one' && percentone < 100 && percentone > 0 )
		{
			document.getElementById('yarnCompositionPercentage2_'+id).value=100-percentone;
			document.getElementById('yarnCompositionPercentage2_'+id).disabled=false;
			document.getElementById('yarnCompositionItem2_'+id).disabled=false;
		}

		if(type=='comp_one' && cbocomptwo!=0 && cbocompone==cbocomptwo )
		{
			alert("Same Composition Not Allowed");
			document.getElementById('yarnCompositionItem1_'+id).value=0;
		}

		if(type=='percent_two' && percenttwo>100)
		{
			alert("Greater Than 100 Not Allwed")
			document.getElementById('yarnCompositionPercentage2_'+id).value="";
		}
		if(type=='percent_two' && percenttwo<=0)
		{
			alert("0 Or Less Than 0 Not Allwed")
			document.getElementById('yarnCompositionPercentage2_'+id).value="";
			document.getElementById('yarnCompositionPercentage2_'+id).disabled=true;
			document.getElementById('yarnCompositionItem2_'+id).value=0;
			document.getElementById('yarnCompositionItem2_'+id).disabled=true;
			document.getElementById('yarnCompositionPercentage1_'+id).value=100;
			document.getElementById('yarnCompositionPercentage1_'+id).disabled=false;
			document.getElementById('yarnCompositionItem1_'+id).disabled=false;
		}
		if(type=='percent_two' && percenttwo==100)
		{
			document.getElementById('yarnCompositionPercentage1_'+id).value="";
			document.getElementById('yarnCompositionItem1_'+id).value=0;
			document.getElementById('yarnCompositionPercentage1_'+id).disabled=true;
			document.getElementById('yarnCompositionItem1_'+id).disabled=true;
		}

		if(type=='percent_two' && percenttwo<100 && percenttwo>0)
		{
			document.getElementById('yarnCompositionPercentage1_'+id).value=100-percenttwo;
			document.getElementById('yarnCompositionPercentage1_'+id).disabled=false;
			document.getElementById('yarnCompositionItem1_'+id).disabled=false;
		}

		if(type=='comp_two'&& cbocompone!=0 && cbocomptwo==cbocompone)
		{
			alert("Same Composition Not Allowed");
			document.getElementById('yarnCompositionItem2_'+id).value=0;
		}
	}


	function openmypage_exportPi()
	{
		if (form_validation('cbo_item_category_id','Item Category')==false)
		{
			return;
		}
		var item_cat=$('#cbo_item_category_id').val();
		var title = 'Export PI Selection Form';
		//var page_link = 'requires/pi_controller_urmi.php?action=export_pi_popup';
		var page_link = 'requires/pi_controller_urmi.php?item_category_id='+item_cat+'&action=export_pi_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=920px,height=450px,center=1,resize=1,scrolling=0','../');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theemail=this.contentDoc.getElementById("txt_selected_data") //Access form field with id="emailfield"

			if(theemail.value!="")
			{
				freeze_window(5);

				var datas=theemail.value.split("_");

				/*if(datas[22]==10)
				{
					$("#cbo_item_category_id").val(2);
				}
				else
				{
					$("#cbo_item_category_id option[value!='0']").remove();
					$("#cbo_item_category_id").append("<option selected value='"+datas[22]+"'>"+datas[23]+"</option>");
				}*/
				
				$("#export_pi_id").val(datas[0]);
				$("#within_group").val(datas[1]);
				$("#cbo_item_category_id").val(datas[22]);
				$("#cbo_importer_id option[value!='0']").remove();
				$("#cbo_importer_id").append("<option selected value='"+datas[2]+"'>"+datas[12]+"</option>");
				if(datas[1]==1){
					load_drop_down('requires/pi_controller_urmi',datas[2]+'_'+datas[26], 'load_drop_down_location', 'location_td',datas[26] );
				}
				// alert(datas[26]);
				$("#cbo_supplier_id option[value!='0']").remove();
				$("#cbo_supplier_id").append("<option selected value='"+datas[3]+"'>"+datas[13]+"</option>");

				$("#pi_number").val(datas[4]);
				$("#pi_date").val(datas[5]);
				$("#last_shipment_date").val(datas[6]);
				$("#pi_validity_date").val(datas[7]);
				$("#cbo_currency_id").val(datas[8]);
				$("#hs_code").val(datas[9]);
				$("#txt_internal_file_no").val(datas[10]);
				$("#txt_remarks").val(datas[11]);
				$("#update_id").val(datas[18]);
				$("#txt_system_id").val(datas[18]);
				$("#cbo_source_id").val(datas[19]);
				$("#intendor_name").val(datas[20]);
				$("#txt_lc_group_no").val(datas[24]);
				$("#cbo_goods_rcv_status").val(datas[21]);
				$("#cbo_ready_to_approved").val(datas[25]);
				$("#cbo_pi_basis_id").val(2);
				$("#cbo_goods_rcv_status").val(2);

				//$('#pi_number').attr('readOnly','readOnly');
				//$('#pi_number').removeAttr('readOnly','readOnly');

				disable_enable_fields('cbo_item_category_id*cbo_importer_id*cbo_supplier_id*pi_number*pi_date*last_shipment_date*pi_validity_date*cbo_currency_id*hs_code*txt_internal_file_no*cbo_pi_basis_id',1,'','');

				if(datas[18]*1>0)
				{
					show_list_view(datas[18],'export_pi_details_update','pi_details_container','requires/pi_controller_urmi','');
					set_button_status(1, permission, 'fnc_pi_mst',1);
				}
				else
				{
					show_list_view(datas[0]+"**"+datas[14]+"**"+datas[15]+"**"+datas[16]+"**"+datas[17]+"**"+datas[22], 'export_pi_details', 'pi_details_container', 'requires/pi_controller_urmi','');
					set_button_status(0, permission, 'fnc_pi_mst',1);
				}
				get_php_form_data($('#cbo_importer_id').val(),'print_button_variable_setting','requires/pi_controller_urmi' );
				release_freezing();
			}
		}
	}

	function reset_fnc()
	{
		//location.reload();
		reset_form('proforma_invoice','pi_details_container*approved','','cbo_currency_id,2*cbo_pi_basis_id,2','disable_enable_fields(\'cbo_item_category_id*cbo_pi_basis_id*cbo_importer_id\',0)');
		window.location.reload();
	}

	function openmypage_user()
	{
		var title = 'Approval User';
		var menu_id=document.getElementById('active_menu_id').value
		var page_link = 'requires/pi_controller_urmi.php?action=approvalUser_popup&menu_id='+menu_id;

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=410px,height=370px,center=1,resize=1,scrolling=0','../');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theemail_id=this.contentDoc.getElementById("hdn_user_id").value; //Access form field with id="emailfield"
			var theemail_name=this.contentDoc.getElementById("hdn_user_name").value;
			if(theemail_id!="")
			{
				freeze_window(5);

				$("#hiddn_user_id").val( theemail_id );
				$("#txt_user_name").val( theemail_name );
			}
			release_freezing();
		}
	}

	function fnc_print_pi()
	{

		if($('#txt_system_id').val()=="")
		{
			alert("Please Save First.");
			return;
		}
		else
		{
			if($('#cbo_item_category_id').val()==4)
			{
				print_report( $('#cbo_importer_id').val()+'*'+$('#update_id').val()+'*'+$('#cbo_item_category_id').val(), "print_pi", "requires/pi_print_urmi");
			}
			else
			{
				alert("Only Accessories Item Print Allowed.");
				return;
			}
		}
	}

	function fnc_print_pi_two()
	{

		if($('#txt_system_id').val()=="")
		{
			alert("Please Save First.");
			return;
		}
		else
		{
			if($('#cbo_item_category_id').val()==4)
			{
				print_report( $('#cbo_importer_id').val()+'*'+$('#update_id').val()+'*'+$('#cbo_item_category_id').val(), "print_pi_two", "requires/pi_print_urmi");
			}
			else
			{
				alert("Only Accessories Item Print Allowed.");
				return;
			}
		}
	}

	function fnc_print_wf()
	{

		if($('#txt_system_id').val()=="")
		{
			alert("Please Save First.");
			return;
		}
		else
		{
			if($('#cbo_item_category_id').val()==3)
			{
				print_report( $('#cbo_importer_id').val()+'*'+$('#update_id').val()+'*'+$('#cbo_item_category_id').val(), "print_wf", "requires/pi_print_urmi");
			}
			else
			{
				alert("Only Woven Fabrics Item Print Allowed.");
				return;
			}
		}
	}

	function fnc_print_sf()
	{

		if($('#txt_system_id').val()=="")
		{
			alert("Please Save First.");
			return;
		}
		else
		{
			if($('#cbo_item_category_id').val()==12)
			{
				print_report( $('#cbo_importer_id').val()+'*'+$('#update_id').val()+'*'+$('#cbo_item_category_id').val(), "print_sf", "requires/pi_print_urmi");
			}
			else
			{
				alert("Only Services Fabrics Item Print Allowed.");
				return;
			}
		}
	}

	function fnc_print_f()
	{

		if($('#txt_system_id').val()=="")
		{
			alert("Please Save First.");
			return;
		}
		else
		{
			if($('#cbo_item_category_id').val()==4)
			{
				print_report( $('#cbo_importer_id').val()+'*'+$('#update_id').val()+'*'+$('#cbo_item_category_id').val(), "print_f", "requires/pi_print_urmi");
			}
			else
			{
				alert("Only Accessories Item Print Allowed.");
				return;
			}

		}
	}

    function independence_basis_controll_function(data)
    {
        var independent_control_arr = JSON.parse('<? echo json_encode($independent_control_arr); ?>');
        $("#cbo_pi_basis_id option[value='2']").show();
        // $("#cbo_pi_basis_id").val(0);
        if(independent_control_arr && independent_control_arr[data]==1)
        {
            $("#cbo_pi_basis_id option[value='2']").hide();
        }
		
		var item_category=$("#cbo_item_category_id").val();
		var pi_basis_id=$("#cbo_pi_basis_id").val();
		var good_receive_data_source_arr = JSON.parse('<? echo json_encode($good_receive_data_source_arr); ?>');
		//alert(good_receive_data_source_arr[data]+"="+item_category);
		if(good_receive_data_source_arr && good_receive_data_source_arr[data]==1 && item_category==4 && pi_basis_id!=2)
        {
			
            $("#cbo_goods_rcv_status").val(1).attr("disabled",false);
        }
		else
		{
			$("#cbo_goods_rcv_status").attr("disabled",false);
		}
		
		var color_from_library_arr = JSON.parse('<? echo json_encode($color_from_library_arr); ?>');
		$("#txt_color_form_lib").val(color_from_library_arr[data]);

    }

    function good_receive_status_controll_function(item_category) 
    {
    	var importer=$("#cbo_importer_id").val();
    	//var item_category=$("#cbo_item_category_id").val();
		var pi_basis_id=$("#cbo_pi_basis_id").val();
		
		var good_receive_data_source_arr = JSON.parse('<? echo json_encode($good_receive_data_source_arr); ?>');
		//alert(good_receive_data_source_arr[importer]+"="+id);
		if(good_receive_data_source_arr && good_receive_data_source_arr[importer]==1 && item_category==4 && pi_basis_id!=2)
        {
            $("#cbo_goods_rcv_status").val(1).attr("disabled",false);
        }
		else
		{
			$("#cbo_goods_rcv_status").attr("disabled",false);
		}
    }
	
	
	function fn_upcharge(str_type)
	{
		if( form_validation('txt_total_amount','Total Amount')==false )
		{
			return;
		}
		if(str_type==1)
		{
			var txt_upcharge = $("#txt_upcharge").val();	
			var up_charge_break_down = $("#up_charge_break_down").val();
		}
		else
		{
			var txt_upcharge = $("#txt_discount").val();	
			var up_charge_break_down = $("#discount_break_down").val();
		}
		
		var page_link='requires/pi_controller_urmi.php?action=upcharge_details&txt_upcharge='+txt_upcharge+'&up_charge_break_down='+up_charge_break_down+'&str_type='+str_type;  
		var title="Up-Charge Breakdown";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=505px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			if(str_type==1)
			{
				var theform=this.contentDoc.forms[0]; 
				var txt_freight_cost=this.contentDoc.getElementById("txt_freight_cost").value;
				var txt_couried_cost=this.contentDoc.getElementById("txt_couried_cost").value;
				var txt_upcharge=this.contentDoc.getElementById("txt_upcharge").value;
				var txt_transport_cost=this.contentDoc.getElementById("txt_transport_cost").value;
				var txt_bank_charge=this.contentDoc.getElementById("txt_bank_charge").value;
				var txt_vat=this.contentDoc.getElementById("txt_vat").value;
				var txt_service_charge=this.contentDoc.getElementById("txt_service_charge").value;
				var txt_adjustment=this.contentDoc.getElementById("txt_adjustment").value;
				
				var total_cost=(txt_freight_cost*1)+(txt_couried_cost*1)+(txt_upcharge*1)+(txt_transport_cost*1)+(txt_bank_charge*1)+(txt_vat*1)+(txt_service_charge*1)+(txt_adjustment*1);
				var cost_breakdown=txt_freight_cost+"_"+txt_couried_cost+"_"+txt_upcharge+"_"+txt_transport_cost+"_"+txt_bank_charge+"_"+txt_vat+"_"+txt_service_charge+"_"+txt_adjustment;
				$("#txt_upcharge").val(total_cost.toFixed(4));
				$("#up_charge_break_down").val(cost_breakdown);
			}
			else
			{
				var theform=this.contentDoc.forms[0]; 
				var txt_discount=this.contentDoc.getElementById("txt_discount").value;
				var txt_adjustment=this.contentDoc.getElementById("txt_adjustment").value;
				
				var total_cost=(txt_discount*1)+(txt_adjustment*1);
				var cost_breakdown=txt_discount+"_"+txt_adjustment;
				$("#txt_discount").val(total_cost.toFixed(4));
				$("#discount_break_down").val(cost_breakdown);
			}
			
			calculate_total_amount(2);
		}
	}

	function fnc_pi_cross_check() {
		var item_category 	= $('#cbo_item_category_id').val();
		var importer_id 	= $('#cbo_importer_id').val();
		var supplier_id		= $('#cbo_supplier_id').val();
		var pi_number		= $('#pi_number').val();
		var pi_id			= $('#txt_system_id').val();

		if (form_validation('txt_system_id','Back To Back System Id')==false )
		{
			alert("Please save data first");
			$('#txt_system_id').focus();
			return;
		}
		else
		{
			var title = 'Cross Check Details';
			var page_link = 'requires/pi_controller_urmi.php?item_category='+item_category+'&importer_id='+importer_id+'&supplier_id='+supplier_id+'&pi_number='+pi_number+'&pi_id='+pi_id+'&action=cross_check_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=520px,height=250px,center=1,resize=1,scrolling=0','../');

			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
				var theemail=this.contentDoc.getElementById("cross_checked_item"); //Access form field with id="emailfield"
				var update_a_id=this.contentDoc.getElementById("update_a_id"); //Access form field with id="emailfield"

				//alert(update_a_id);

				if(theemail.value!="" || update_a_id.value != "")
				{
					freeze_window(5);
					//get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/pi_controller_urmi" );
					var checked_items = theemail.value.split("_");
					var update_check_id = update_a_id.value;
					//alert(checked_items[0]); COM_CROSS_CHECK_ACTIVITY
					$("#cross_check_activity_status").val(checked_items);
					$("#update_activity_id").val(update_check_id);
					release_freezing();
				}
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
		page_link='requires/pi_controller_urmi.php?action=file_popup&company_id='+company_id+'&is_lc_sc='+is_lc_sc+'&lc_sc_id='+lc_sc_id;
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

	function openmypage_order_file_info()
	{
		var company_id=document.getElementById('cbo_importer_id').value;
		var is_lc_sc1=document.getElementById('is_lc_sc1').value;
		var lc_sc_id1=document.getElementById('lc_sc_id1').value;
		//var cbo_year=document.getElementById('hide_year').value;
		// alert(company_id);
		//page_link='requires/file_wise_export_status_controller.php?action=file_popup&company_id='+company_id+'&buyer_id='+buyer_id+'&lien_bank='+lien_bank+'&cbo_year='+cbo_year;
		page_link='requires/pi_controller_urmi.php?action=order_file_popup&company_id='+company_id+'&is_lc_sc1='+is_lc_sc1+'&lc_sc_id1='+lc_sc_id1;
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
				var data=this.contentDoc.getElementById("hide_order_file_no").value.split('_');//alert(item_description_all);
				// alert(data[4]);
				document.getElementById('txt_order_file_no').value=data[0];
				document.getElementById('is_lc_sc1').value=data[1];
				document.getElementById('lc_sc_id1').value=data[2];
				document.getElementById('lc_sc_no1').value=data[3];
				document.getElementById('lc_sc_file_year1').value=data[4];
			}
		}
	}

	function openmypage_comp(inc)
	{
		var page_link="requires/pi_controller_urmi.php?action=composition_popup&inc="+inc;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Composition Popup", 'width=480px,height=350px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			
			var hidcompid=this.contentDoc.getElementById("hidcompid").value;
			var hidcompname=this.contentDoc.getElementById("hidcompname").value;
			$('#yarnCompositionItem1_'+inc).val(hidcompid);
			$('#txtyarnCompositionItem1_'+inc).val(hidcompname);
			control_composition(inc,'comp_one');
			//check_duplicate(inc,1);
		}
	}

	function fnc_not_approved_cause()
	{
		var pi_id = $('#update_id').val();
		//var company_id=document.getElementById('cbo_importer_id').value;
		//alert(update_id);return;
		var title = 'Not Approve Cause Details';
		var page_link = 'requires/pi_controller_urmi.php?pi_id='+pi_id+'&action=not_approved_cause_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450,height=250px,center=1,resize=1,scrolling=0','../');

		emailwindow.onclose=function()
		{}	
	}

</script>

<script type="text/javascript"> 
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
			url: 'requires/pi_controller_urmi.php?action=file_upload&mst_id='+ mst_id, 
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

function open_notes_popup()
{
	var title='Notes';
	var hdn_notes=$('#hdn_notes').val();
	var page_link='requires/pi_controller_urmi.php?action=notes_popup&hdn_notes='+hdn_notes;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=370px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var notes_dtls_data=this.contentDoc.getElementById("notes_dtls_data").value;
		$('#txt_notes').val(notes_dtls_data);
		$('#hdn_notes').val(notes_dtls_data);
	};
}

//function sendMail()

function call_print_button_for_mail(mail,mail_body,type){
		var cbo_item_category_id=$('#cbo_item_category_id').val();
		
		if( cbo_item_category_id == "1")
		{
			 entry_form = "165";
		}
		else if( cbo_item_category_id == "2" ||  cbo_item_category_id == "3" ||  cbo_item_category_id == "13" ||  cbo_item_category_id == "14")
		{
			 entry_form = "166";
		}
		else if( cbo_item_category_id == "4")
		{
			 entry_form = "167";
		}
		else if( cbo_item_category_id == "12")
		{
			 entry_form = "168";
		}
		else if( cbo_item_category_id == "24")
		{
			 entry_form = "169";
		}
		else if( cbo_item_category_id == "25" || cbo_item_category_id == "102" || cbo_item_category_id == "103")
		{
			 entry_form = "170";
		}
		else if( cbo_item_category_id == "30")
		{
			 entry_form = "197";
		}
		else if( cbo_item_category_id == "31")
		{
			 entry_form = "171";
		}
		else if( cbo_item_category_id == "5" ||  cbo_item_category_id == "6" ||  cbo_item_category_id == "7" ||  cbo_item_category_id == "23")
		{
			 entry_form = "227";
		}
		else
		{
			 entry_form = "172";
		}

			// print_report( $('#cbo_importer_id').val()+'*'+$('#update_id').val()+'*'+entry_form+'*'+cbo_item_category_id+'*1', "print", "requires/pi_print_urmi" ); return;	
				
		
		
		if (form_validation('cbo_importer_id*update_id','Import ID*Sys Id')==false)
		{
			return;
		}
		
		
		var data="action=print&data="+$('#cbo_importer_id').val()+'*'+$('#update_id').val()+'*'+entry_form+'*'+cbo_item_category_id+'*1*'+mail;
 		freeze_window(operation);
		//http.open("POST","../../auto_mail/btb_margin_lc_auto_mail.php",true);
		http.open("POST","requires/pi_print_urmi.php",true);
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


function get_company_config(company_id)
	{
		$('#cbo_working_company_id').val( company_id );
		load_drop_down( 'requires/pi_controller_urmi', company_id, 'load_drop_down_sew_location', 'sew_location' );

		get_php_form_data(company_id,'get_company_config','requires/pi_controller_urmi' );
		//location_select();
		po_update_period();
	}
	
	function get_sew_company_config(company_id)
	{
		load_drop_down( 'requires/pi_controller_urmi', company_id, 'load_drop_down_sew_location', 'sew_location' );
	}
	
	function fn_buyer_permission(cat_id)
	{
		if(cat_id==2 || cat_id==3 || cat_id==13 || cat_id==14 || cat_id==1 || cat_id==4 || cat_id==25 || cat_id==102 || cat_id==103 || cat_id==12 || cat_id==104)
		{
			disable_enable_fields('cbo_buyer_name*cbo_brand_id*cbo_season_year*cbo_season_id',0,'','');
		}
		else
		{
			disable_enable_fields('cbo_buyer_name*cbo_brand_id*cbo_season_year*cbo_season_id',1,'','');
		}
	}
	
	function fn_break_data_change(str_type)
	{
		if(str_type==1) $("#up_charge_break_down").val("");
		else  $("#discount_break_down").val("");
	}
	
	function fn_reset_thead()
	{
		var pi_category=$("#cbo_item_category_id").val();
		if(pi_category==4)
		{
			$('.fltrow').remove();
		}
	} 

	function PiMarginLC(id){
		// alert(id);
		if(id==2){
			$("#buyer").css({"color":"black"});
			$("#internal_file_no").css({"color":"black"});
			$("#fileid").css({"color":"black"});
			
		} 
		else{
			$("#buyer").css({"color":"blue"});
			$("#internal_file_no").css({"color":"blue"});
			$("#fileid").css({"color":"blue"});
		}
	}
	
</script>

<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission); ?>		
        <div>
        <form name="proforma_invoice" id="proforma_invoice" enctype="multipart/form-data" method="post">
            <fieldset style="width:1160px;">
            <legend>PI Details</legend>
                <table width="100%" border="0" cellpadding="0" cellspacing="2">
                    <tr height="25">
                        <td colspan="8" valign="middle" align="center" style="border-bottom:0px solid #666">
                            <strong>System ID</strong>&nbsp;&nbsp;<input type="text" name="txt_system_id" id="txt_system_id" style="width:140px" class="text_boxes" readonly/>
                            &nbsp;&nbsp;&nbsp;<strong>Get Export PI</strong>&nbsp;&nbsp;
                            <input type="text" name="txt_export_pi" id="txt_export_pi" style="width:140px" class="text_boxes" placeholder="Double click for Export PI" onDblClick="openmypage_exportPi()" readonly />
                            <input type="hidden" name="export_pi_id" id="export_pi_id"/>
                            <input type="hidden" name="within_group" id="within_group"/>
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption" id="category_td" width="110">Item Category</td><input type="hidden" name="is_approved" id="is_approved" value="44">
                        <td><? 
						echo create_drop_down( "cbo_item_category_id", 151, $item_category_with_gen,'', 1, '--Select--',0,"load_drop_down( 'requires/pi_controller_urmi',document.getElementById('cbo_importer_id').value+'_'+this.value, 'load_supplier_dropdown', 'supplier_td' );show_list_view(document.getElementById('cbo_pi_basis_id').value+'_'+this.value+'_'+'1', 'pi_details', 'pi_details_container', 'requires/pi_controller_urmi', '' );good_receive_status_controll_function(this.value);setFieldLevelAccess(document.getElementById('cbo_importer_id').value); fn_buyer_permission(this.value);",0,$item_category_credential_cond,'','','72,79,73,71,77,78,75,76'); ?>
                        </td>
                        <td class="must_entry_caption"  width="110">Importer</td>
                        <td id="importer_td">
						<?
							echo create_drop_down( "cbo_importer_id", 151,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name",'id,company_name', 1, 'Select',0,"load_drop_down( 'requires/pi_controller_urmi',this.value+'_'+document.getElementById('cbo_item_category_id').value, 'load_supplier_dropdown', 'supplier_td' );independence_basis_controll_function(this.value);setFieldLevelAccess(this.value);load_drop_down( 'requires/pi_controller_urmi', this.value, 'load_drop_down_location','location_td');load_drop_down( 'requires/pi_controller_urmi', this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data(this.value,'print_button_variable_setting','requires/pi_controller_urmi' );",0);
						?>
                        <input type="hidden" id="txt_color_form_lib" name="txt_color_form_lib" />
                        </td>
                        <td width="110">Location</td>
                        <td id="location_td"><? echo create_drop_down( "cbo_location_name", 151, $blank_array,"", 1, "-- Select Location --", 0, "" ); ?></td>
                        <td class="must_entry_caption">Supplier</td>
                        <td id="supplier_td"><? echo create_drop_down( "cbo_supplier_id", 151, $blank_array,"", 1, "-- Select Supplier --", 0, "" ); ?></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">PI No</td>
                        <td><input type="text" name="pi_number" id="pi_number" class="text_boxes" style="width:140px" placeholder="Double click for PI" onDblClick="openmypage()" maxlength="100" /></td>
                        <td class="must_entry_caption">PI Date</td>
                        <td><input type="text" name="pi_date" id="pi_date" class="datepicker" value="<? echo date('d-m-Y'); ?>"  style="width:140px" /></td>
                        <td>PI Validity Date</td>
                        <td><input type="text" name="pi_validity_date" id="pi_validity_date"  style="width:140px"  class="datepicker" value="" /></td>
                        <td>PI In Hand Date</td>
                        <td><input type="text" name="pi_inhand_date"  style="width:140px"  id="pi_inhand_date" class="datepicker" value="" /></td>
                        
                    </tr>
                    <tr>
                        <td>HS Code</td>
                        <td><input type="text" name="hs_code" id="hs_code" class="text_boxes"  style="width:140px"  value=""  maxlength="60" /></td>
                        <td class="must_entry_caption">Currency</td>
                        <td><? echo create_drop_down( "cbo_currency_id", 151,$currency,'',0,'',2,0,0); ?></td>
                        <td id="internal_file_no">Internal File No</td>
                        <td>
							<input type="text" name="txt_internal_file_no" id="txt_internal_file_no"  style="width:140px"  class="text_boxes" readonly maxlength="50" onClick="openmypage_file_info()" placeholder="Browse"  />
							<input type="hidden" name="is_lc_sc" id="is_lc_sc"/>
							<input type="hidden" name="lc_sc_id" id="lc_sc_id"/>
							<input type="hidden" name="lc_sc_no" id="lc_sc_no"/>
							<input type="hidden" name="lc_sc_file_year" id="lc_sc_file_year"/>
						</td>
                        <td class="must_entry_caption">Source</td>
                        <td><? echo create_drop_down( "cbo_source_id", 151, $source,'', 0, '',0,0); ?></td>
                        
                    </tr>

                    <tr>
                        <td class="must_entry_caption">PI Basis</td>
                        <td><? echo create_drop_down( "cbo_pi_basis_id", 151, $pi_basis,'', 0, '',1,"independence_basis_controll_function(document.getElementById('cbo_importer_id').value);show_list_view(this.value+'_'+document.getElementById('cbo_item_category_id').value+'_'+'1', 'pi_details', 'pi_details_container', 'requires/pi_controller_urmi', '' ) ;",0); ?>
                        </td>
                        <td class="must_entry_caption">Goods Rcv Status</td>
                        <td><? echo create_drop_down( "cbo_goods_rcv_status", 151, $acceptance_time,'', 0, '',2,"",0); ?></td>
                        <td>Indentor Name</td>
                        <td><? echo create_drop_down( "intendor_name", 151,"select a.id,a.supplier_name from lib_supplier a,lib_supplier_party_type b where a.id = b.supplier_id and b.party_type = 40 and a.status_active=1 and a.is_deleted=0 group by  a.id,a.supplier_name order by a.supplier_name",'id,supplier_name', 1, 'Select',0,0,0); ?>
                        </td>
                        <td>LC Group No.</td>
                        <td><input type="text" id="txt_lc_group_no" name="txt_lc_group_no" style="width:140px" class="text_boxes_numeric" /></td>
                    </tr>
                    <tr>
                    	<td>Beneficiary</td>
                        <td><textarea id="txt_beneficiary_name"  name="txt_beneficiary_name" pre_beneficiary_name="" class="text_area" style="width:140px;" placeholder="Beneficiary Name"></textarea></td>
                        <td>Pay Term</td>
                        <td><? echo create_drop_down("cbo_payterm_id", 151, $pay_term, '', 1, '-Select-', 0, "", 0, ''); ?></td>
                    	<td>Priority</td>
                        <td>
							<?
							$priority_array=array(1=>"Normal",2=>"Urgent",3=>"Critical");
							echo create_drop_down( "cbo_priority", 151, $priority_array,"", 1, "-- Select--", 0, "","","" ); 
							?>
						</td>
                    	<td>Tenor</td>
                    	<td><input type="text"  name="txt_tenor" style="width:140px" id="txt_tenor" class="text_boxes_numeric" /></td>
                    </tr>
                    <tr>
                    	<td>PI For</td>
                        <td>
                        	<?
							$piFor_array=array(1=>"BTB", 2=>"Margin LC", 3=>"Fund Buildup", 4=>"TT/Pay Order", 5=>"FTT", 6=>"FDD/RTGS");
							echo create_drop_down( "cbo_pi_for", 151, $piFor_array,"", 1, "-- Select--", 0, "PiMarginLC(this.value)","","" ); 
							?>
                        </td>
                        <td>Notes</td>
                        <td>
							<input type="text" id="txt_notes" name="txt_notes" class="text_boxes" style="width:140px;" onClick="open_notes_popup()" readonly placeholder="Browse" />
                            <input type="hidden" id="hdn_notes" class="text_boxes" style="width:100px;" />
						</td>
						<td>Rate Negotiate by</td>
                        <td>
                        	<?
							$nagotiate_array=array(1=>"Buyer",2=>"Procurement");
							echo create_drop_down( "cbo_nagotiate_by", 151, $nagotiate_array,"", 1, "-- Select--", 0, "","","" ); 
							?>
                        </td>
                    	
                        <td>Ready To Approved</td>
                    	<td><? echo create_drop_down( "cbo_ready_to_approved", 151, $yes_no,"", 1, "-- Select--", 2, "","","" ); ?></td>                    	
                    </tr>
                    <tr>
                    	<td>Approval User</td>
                        <td>
						<input type="text" name="txt_user_name" id="txt_user_name"  style="width:140px;" class="text_boxes" placeholder="Browse Approval User" onDblClick="openmypage_user();" readonly /><input type="hidden" name="hiddn_user_id" id="hiddn_user_id">	
						</td>
						<td>Last Shipment Date</td>
                        <td><input type="text" name="last_shipment_date"  style="width:140px"  id="last_shipment_date" class="datepicker" value="" /></td>
                    	<td style="display:none">Inco Term</td>
                    	<td style="display:none"><? echo create_drop_down( "cbo_inco_term_id",151,$incoterm,'',1,'-Select-',0,0,0); ?></td>
                    	<td>
							<input type="button" id="cross_check_popup" class="cross_check_popup formbutton" style="width:110px;" value="Cross Check Popup" onClick="fnc_pi_cross_check()" />
							<input type="hidden" id="cross_check_activity_status" name="cross_check_activity_status" value="">
							<input type="hidden" id="update_activity_id" name="update_activity_id" value="">
						</td>
                        <td>
							<input type="button" id="image_button" class="image_uploader" style="width:75px;" value="IMAGE" onClick="file_uploader( '../../', document.getElementById('update_id').value,'', 'proforma_invoice',1,1)" />

							<input type="button" id="image_button" class="image_uploader" style="width:75px;" value="FILE" onClick="file_uploader( '../../', document.getElementById('update_id').value,'', 'proforma_invoice',2,1)" />

						</td>

						<td id="fileid" align="left">File</td>
						<td align="left">
                        	<input type="file" multiple id="pi_mst_file" class="image_uploader" style="width:150px" onChange="document.getElementById('txt_file').value=1">
                        	<input type="hidden" multiple id="txt_file">
                        </td>
                    </tr>                    
                    <tr>
                    	<td id="buyer">Buyer</td>
                        <td id="buyer_td">
                        <?
						echo create_drop_down( "cbo_buyer_name", 150, "select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", "", "","","" );
						?>
                        </td>
                    	<td>Brand</td>
                    	<td id="brand_td">
                        <?
						echo create_drop_down( "cbo_brand_id", 150, $blank_array,"", 1,"-Brand-", 1, "",0,"" );
						?>
                        </td>
                        <td>Season</td>
                        <td id="season_td">
                        <?
						echo create_drop_down( "cbo_season_id", 150, $blank_array,"", 1,"-Season-", 1, "",0,"" );
						?>
                        </td>
                    	<td>Season Year</td>
                    	<td>
                        <?
						echo create_drop_down( "cbo_season_year", 150, create_year_array(),"", 1,"-Year-", 1, "",0,"" );
						
						?>
                        </td>
                    </tr>
                    <tr>
                    	<td>Remarks</td>
                    	<td><input type="text" name="txt_remarks" id="txt_remarks"  style="width:140px;"  class="text_boxes" /></td>
                    	<td>Not Approve Cause</td>
                    	<td><input type="text" name="txt_not_approved_cause" id="txt_not_approved_cause" style="width:140px;"  class="text_boxes" onClick="fnc_not_approved_cause()" disabled="disabled" readonly placeholder="Browse" /></td>
                        <td>LC required date</td>
                        <td><input type="text" name="lc_req_date" id="lc_req_date"  style="width:140px"  class="datepicker" value="" /></td>
						<td>Terms and Condition</td>
                        <td>
	                        <? 
	                        include("../../terms_condition/terms_condition.php");
	                        terms_condition(405,'txt_system_id','../../');
	                        ?>
                        </td>
                    </tr>
					<tr>
                    	<td id="order_file_no">From Order File Ref</td>
                    	<td>
							<input type="text" name="txt_order_file_no" id="txt_order_file_no"  style="width:140px"  class="text_boxes" readonly maxlength="50" onClick="openmypage_order_file_info()" placeholder="Browse"  />
							<input type="hidden" name="is_lc_sc1" id="is_lc_sc1"/>
							<input type="hidden" name="lc_sc_id1" id="lc_sc_id1"/>
							<input type="hidden" name="lc_sc_no1" id="lc_sc_no1"/>
							<input type="hidden" name="lc_sc_file_year1" id="lc_sc_file_year1"/>
						</td>
                    
                    </tr>
                    <tr>
                    	<td colspan="8" height="15"></td>
                    </tr>
                    <tr>
                        <td colspan="8" height="50" valign="middle" align="center" class="button_container">
                        <div id="approved" style="float:left; font-size:24px; color:#FF0000;"></div>
                        <input type="hidden" name="update_id" id="update_id" value="" readonly/>                        
                        <input type="hidden" name="hide_approved_status" id="hide_approved_status" value="" readonly />
                        <input type="hidden" name="hide_attached_status" id="hide_attached_status" value="" readonly />
                        <input type="hidden" name="txt_order_type" id="txt_order_type"/>

                        <? echo load_submit_buttons( $permission, "fnc_pi_mst", 0,0 ,"reset_fnc();",1);
                        //reset_form('proforma_invoice','pi_details_container*approved','','cbo_currency_id,2*cbo_pi_basis_id,2','disable_enable_fields(\'cbo_item_category_id*cbo_pi_basis_id\',0)')
                        ?>
                        <!-- <input type="button" name="printBtn4" id="printBtn4" value="Print 2" onClick="fnc_print_wf()" style="width:100px" class="formbutton" />
                        <input type="button" name="printBtn" id="printBtn" value="PI-Print" onClick="fnc_print_pi()" style="width:100px" class="formbutton" /> -->
                        <input type="button" name="printBtn3" id="printBtn3" value="Send Mail" onClick="fnSendMail('../../','',1,0,0,1,0)" style="width:100px" class="formbutton" />
						<span id="button_data_panel"></span>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>
            <form name="pimasterform_2" id="pimasterform_2" autocomplete="off">
                <fieldset style="width:1160px; margin-top:10px;">
                    <legend>PI Item Details</legend>
                    <div id="pi_details_container"></div>
                </fieldset>
            </form>
        </div>
    </div>
</body>
<script>
		
		$(document).ready(function() 
		{ 
			for (var property in mandatory_field_arr) {
			  $("#"+mandatory_field_arr[property]).parent().prev('td').css("color", "blue");
			}
		});
		
		jQuery("#txt_beneficiary_name").keyup(function(e) 
		{
			var c = String.fromCharCode(e.which);
			var evt = (e) ? e : window.event;
			var key = (evt.keyCode) ? evt.keyCode : evt.which;
			// var key = e.keyCode;
			 //alert (key )
			if (key == 13) 
			{
				var text = $("#txt_beneficiary_name").val();   
				//alert(text);
				//var lines = text.split(/\r|\r\n|\n/);
				//var count = (lines);
				document.getElementById("txt_beneficiary_name").value =document.getElementById("txt_beneficiary_name").value + "\n";
				return false;
			}
			else {
				return true;
			}
		});
	</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">$("#cbo_buyer_name").val(0);//$("#cbo_importer_id").val(0);</script>
</html>
