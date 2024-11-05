<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Grey Fabric Receive
Functionality	:
JS Functions	:
Created by		:
Creation date 	:
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
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Grey Fabric Receive ", "../../", 1, 1,'','1','');

?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';

	<?
	if($_SESSION['logic_erp']['mandatory_field'][58]!="")
	{
		$mandatory_field_arr= json_encode( $_SESSION['logic_erp']['mandatory_field'][58] );
		echo "var mandatory_field_arr= ". $mandatory_field_arr . ";\n";
	}
	?>

	var tableFilters =
	{
		col_30: "none",
		col_operation: {
		id: ["value_tot_qnty"],
		col: [6],
		operation: ["sum"],
		write_method: ["innerHTML"]
		}
	}

	function generate_report_file(data,action)
	{
		window.open("requires/grey_fabric_receive_roll_controller.php?data=" + data+'&action='+action, true );
	}

	function fnc_grey_fabric_receive(operation)
	{
		if($('#txt_no_bill').is(':checked'))
		{
			var txt_no_bill=1;
		}
		else
		{
			var txt_no_bill=0;
		}

		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_recieved_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_location_name').val()+"*"+$("#cbo_store_name").val(),'grey_fabric_receive_print');
			return;
		}
		else if(operation==5)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_recieved_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_location_name').val()+'*'+'print2'+'*'+$('#txtBasis_1').val(),'grey_fabric_receive_print');
			return;
		}
		else if(operation==6)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_recieved_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+'print3'+'*'+$('#txtBasis_').val()+'*'+$('#cbo_knitting_location_id').val()+'*'+$('#cbo_knitting_source').val(),'grey_fabric_receive_print3');
			return;
		}
		else if(operation==7)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_recieved_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_location_name').val()+"*"+$("#cbo_store_name").val(),'grey_fabric_receive_print4');
			return;
		}
		else if(operation==8)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_recieved_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+'print3'+'*'+$('#txtBasis_').val()+'*'+$('#cbo_knitting_location_id').val()+'*'+$('#cbo_knitting_source').val(),'grey_fabric_receive_print5');
			return;
		}
		else if(operation==9)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_recieved_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_location_name').val()+"*"+$("#cbo_store_name").val(),'grey_fabric_receive_printmg');
			return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if($("#is_posted_accout").val()==1)
			{
				alert("Already Posted In Accounting. Save Update Delete Restricted.");
				return;
			}
			if(operation==2)
			{
				show_msg('13');
				return;
			}

			if( form_validation('cbo_company_id*txt_receive_date*cbo_store_name*cbo_knitting_source*cbo_knitting_company*cbo_location_name','Company*Receive Basis*Production Date*Store Name*Knitting Source*Knitting Com*Location')==false )
			{
				return;
			}

			var knitting_source=parseInt($('#cbo_knitting_source').val());
			if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][58]); ?>')
			{
				if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][58]); ?>','<? echo implode('*',$_SESSION['logic_erp']['mandatory_message'][58]); ?>')==false) {return;}
			}

            var current_date='<? echo date("d-m-Y"); ?>';
            if(date_compare($('#txt_receive_date').val(), current_date)==false)
            {
				alert("Receive Date Can not Be Greater Than Today");
				return;
            }
			var tr_rows=$("#total_row").val();
			var floor_id ="";
			var store_update_upto=$('#store_update_upto').val()*1;
			var cbo_floor = cbo_room = txt_rack = txt_shelf= 0;
			if(operation==0)
			{
				var j=1; var i=1;
				for(var i=1; i<=tr_rows; i++)
				{
					if($('#checkedId_'+i).is(':checked'))
					{
						if(store_update_upto > 1)
						{
							cbo_floor=document.getElementById('cbo_floor_to_' + i).value;
							cbo_room=document.getElementById('cbo_room_to_' + i).value;
							txt_rack=document.getElementById('txt_rack_to_' + i).value;
							txt_shelf=document.getElementById('txt_shelf_to_' + i).value;
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

						if($('#checkedId_'+i).is(':checked')) { var	check_value=1; } else {var check_value=0;}
						if(j==1) var check_data=check_value; else check_data=check_data+"*"+check_value;

						if(j==1) var tran_id = escape(document.getElementById('hiden_transid_' + i).value); else tran_id=tran_id+"*"+escape(document.getElementById('hiden_transid_' + i).value);
						if(j==1) var gray_dtlsid = escape(document.getElementById('hidden_greyid_' + i).value); else gray_dtlsid=gray_dtlsid+"*"+escape(document.getElementById('hidden_greyid_' + i).value);
						if(j==1) var roll_id = escape(document.getElementById('hidden_rollid_' + i).value); else roll_id=roll_id+"*"+escape(document.getElementById('hidden_rollid_' + i).value);

						if(j==1) var sys = escape(document.getElementById('hidesysid_' + i).value); else sys=sys+"*"+escape(document.getElementById('hidesysid_' + i).value);
						if(j==1) var receive_number = escape(document.getElementById('hidesysnum_' + i).value); else receive_number=receive_number+"*"+escape(document.getElementById('hidesysnum_' + i).value);
						if(j==1) var program_id = escape(document.getElementById('hideprogrum_' + i).value); else program_id=program_id+"*"+escape(document.getElementById('hideprogrum_' + i).value);
						if(j==1) var receive_basis = escape(document.getElementById('txtBasis_' + i).value); else receive_basis=receive_basis+"*"+escape(document.getElementById('txtBasis_' + i).value);
						if(j==1) var barcode_id= escape(document.getElementById('hidenBarcode_' + i).value); else barcode_id=barcode_id+"*"+escape(document.getElementById('hidenBarcode_' + i).value);

						if(j==1) var prod_id= escape(document.getElementById('hideprodid_' + i).value); else prod_id=prod_id+"*"+escape(document.getElementById('hideprodid_' + i).value);
						//if(j==1) var floor_id = escape(document.getElementById('cbo_floor_to_' + i).value); else floor_id=floor_id+"*"+escape(document.getElementById('cbo_floor_to_' + i).value);
						//if(j==1) var bin = escape(document.getElementById('txtBin_' + i).value); else bin=bin+"*"+escape(document.getElementById('txtBin_' + i).value);
						if(j==1) var floor_id = escape(document.getElementById('cbo_floor_to_' + i).value); else floor_id=floor_id+"*"+escape(document.getElementById('cbo_floor_to_' + i).value);
						if(j==1) var room_no = escape(document.getElementById('cbo_room_to_' + i).value); else room_no=room_no+"*"+escape(document.getElementById('cbo_room_to_' + i).value);
						if(j==1) var rack = escape(document.getElementById('txt_rack_to_' + i).value); else rack=rack+"*"+escape(document.getElementById('txt_rack_to_' + i).value);
						if(j==1) var self = escape(document.getElementById('txt_shelf_to_' + i).value); else self=self+"*"+escape(document.getElementById('txt_shelf_to_' + i).value);
						if(j==1) var bin = escape(document.getElementById('txt_bin_to_' + i).value);
							else bin=bin+"*"+escape(document.getElementById('txt_bin_to_' + i).value);

						if(j==1) var issue_qty= escape(document.getElementById('txtcurrentdelivery_' + i).value); else issue_qty=issue_qty+"*"+escape(document.getElementById('txtcurrentdelivery_' + i).value);

						if(j==1) var roll_no = escape(document.getElementById('txtroll_' + i).value); else roll_no=roll_no+"*"+escape(document.getElementById('txtroll_' + i).value);
						if(j==1) var knitting_source= escape(document.getElementById('knittingsource_' + i).value); else knitting_source=knitting_source+"*"+escape(document.getElementById('knittingsource_' + i).value);
						if(j==1) var buyer_id = escape(document.getElementById('hiddenBuyer_' + i).value); else buyer_id=buyer_id+"*"+escape(document.getElementById('hiddenBuyer_' + i).innerHTML);
						if(j==1) var po_id= escape(document.getElementById('hiddenPoId_' + i).value); else po_id=po_id+"*"+escape(document.getElementById('hiddenPoId_' + i).value);
						if(j==1) var dia= escape(document.getElementById('hidedia_' + i).value); else dia=dia+"*"+escape(document.getElementById('hidedia_' + i).value);
						if(j==1) var determination_id = escape(document.getElementById('hideconstruction_' + i).value); else determination_id=determination_id+"*"+escape(document.getElementById('hideconstruction_' + i).value);

						if(j==1) var body_part= escape(document.getElementById('hidden_bodypart_' + i).value); else body_part=body_part+"*"+escape(document.getElementById('hidden_bodypart_' + i).value);
						// alert(ac_code); return;
						if(j==1) var color_id = escape(document.getElementById('hiddenColor_' + i).value); else color_id=color_id+"*"+escape(document.getElementById('hiddenColor_' + i).value);
						if(j==1) var color_range= escape(document.getElementById('hiddenColorRange_' + i).value); else color_range=color_range+"*"+escape(document.getElementById('hiddenColorRange_' + i).value);
						if(j==1) var yean_lot = encodeURIComponent(document.getElementById('hidden_yeanlot_' + i).value); else yean_lot=yean_lot+"*"+encodeURIComponent(document.getElementById('hidden_yeanlot_' + i).value);
						if(j==1) var gsm= escape(document.getElementById('hidegsm_' + i).value); else gsm=gsm+"*"+escape(document.getElementById('hidegsm_' + i).value);

						if(j==1) var uom = escape(document.getElementById('hiddenUom_' + i).value); else uom=uom+"*"+escape(document.getElementById('hiddenUom_' + i).value);
						if(j==1) var yean_cont= escape(document.getElementById('hiddenYeanCount_' + i).value); else yean_cont=yean_cont+"*"+escape(document.getElementById('hiddenYeanCount_' + i).value);
						if(j==1) var band_id = escape(document.getElementById('hiddenBand_' + i).value); else band_id=band_id+"*"+escape(document.getElementById('hiddenBand_' + i).value);

						if(j==1) var shift_id= escape(document.getElementById('hiddenShift_' + i).value); else shift_id=shift_id+"*"+escape(document.getElementById('hiddenShift_' + i).value);
						if(j==1) var machine_name = escape(document.getElementById('hiddenMachine_' + i).value); else machine_name=machine_name+"*"+escape(document.getElementById('hiddenMachine_' + i).value);
						if(j==1) var hidden_qty = escape(document.getElementById('hidden_delivery_qty_' + i).value); else hidden_qty=hidden_qty+"*"+escape(document.getElementById('hidden_delivery_qty_' + i).value);
						if(j==1) var stitch_length = encodeURIComponent(document.getElementById('hidden_stl_' + i).value); else stitch_length=stitch_length+"*"+encodeURIComponent(document.getElementById('hidden_stl_' + i).value);
						if(j==1) var roll_rate = escape(document.getElementById('rollRate_' + i).value); else roll_rate=roll_rate+"*"+escape(document.getElementById('rollRate_' + i).value);

						if(j==1) var knitting_charge = escape(document.getElementById('knittingCharge_' + i).value); else knitting_charge=knitting_charge+"*"+escape(document.getElementById('knittingCharge_' + i).value);
						if(j==1) var yarn_rate = escape(document.getElementById('yarnRate_' + i).value); else yarn_rate=yarn_rate+"*"+escape(document.getElementById('yarnRate_' + i).value);
						if(j==1) var hidden_withoutOrder = escape(document.getElementById('hidden_withoutOrder_' + i).value); else hidden_withoutOrder=hidden_withoutOrder+"*"+escape(document.getElementById('hidden_withoutOrder_' + i).value);
						if(j==1) var hidden_booking = escape(document.getElementById('hideBooking_' + i).value); else hidden_booking=hidden_booking+"*"+escape(document.getElementById('hideBooking_' + i).value);
						if(j==1) var isSales = escape(document.getElementById('isSales_' + j).value); else isSales=isSales+"*"+escape(document.getElementById('isSales_' + j).value);
						if(j==1) var reject_qnty = escape(document.getElementById('hidden_reject_fabric_recv_qnty_' + j).value); else reject_qnty=reject_qnty+"*"+escape(document.getElementById('hidden_reject_fabric_recv_qnty_' + j).value);
						if(j==1) var hidden_qnty_in_pcs = escape(document.getElementById('hidden_qnty_in_pcs_' + j).value); else hidden_qnty_in_pcs=hidden_qnty_in_pcs+"*"+escape(document.getElementById('hidden_qnty_in_pcs_' + j).value);
						if(j==1) var fsoDeliveryType = escape(document.getElementById('fsoDeliveryType_' + j).value); else fsoDeliveryType=fsoDeliveryType+"*"+escape(document.getElementById('fsoDeliveryType_' + j).value);
						j++;
					}
				}
			}
			else
			{
				for(var i=1; i<=tr_rows; i++)
				{
					if(store_update_upto > 1)
					{
						cbo_floor=document.getElementById('cbo_floor_to_' + i).value;
						cbo_room=document.getElementById('cbo_room_to_' + i).value;
						txt_rack=document.getElementById('txt_rack_to_' + i).value;
						txt_shelf=document.getElementById('txt_shelf_to_' + i).value;
						if(store_update_upto==5 && (cbo_floor==0 || cbo_room==0 || txt_rack==0 || txt_shelf==0))
						{
							alert("Up To Shelf Value Full Fill Required For Inventory");
							return;
						}
						else if(store_update_upto==4 && (cbo_floor==0 || cbo_room==0 || txt_rack==0))
						{
							alert("Up To Rack Value Full Fill Required For Inventory");
							return;
						}
						else if(store_update_upto==3 && (cbo_floor==0 || cbo_room==0))
						{
							alert("Up To Room Value Full Fill Required For Inventory");
							return;
						}
						else if(store_update_upto==2 && cbo_floor==0)
						{
							alert("Up To Floor Value Full Fill Required For Inventory");
							return;
						}
					}

					if($('#checkedId_'+i).is(':checked')) { var	check_value=1; } else {var check_value=0;}
					if(i==1) var check_data=check_value; else check_data=check_data+"*"+check_value;
					if(i==1) var tran_id = escape(document.getElementById('hiden_transid_' + i).value); else tran_id=tran_id+"*"+escape(document.getElementById('hiden_transid_' + i).value);
					if(i==1) var gray_dtlsid = escape(document.getElementById('hidden_greyid_' + i).value); else gray_dtlsid=gray_dtlsid+"*"+escape(document.getElementById('hidden_greyid_' + i).value);
					if(i==1) var roll_id = escape(document.getElementById('hidden_rollid_' + i).value); else roll_id=roll_id+"*"+escape(document.getElementById('hidden_rollid_' + i).value);
					if(i==1) var receive_number = escape(document.getElementById('hidesysnum_' + i).value); else receive_number=receive_number+"*"+escape(document.getElementById('hidesysnum_' + i).value);
					if(i==1) var program_id = escape(document.getElementById('hideprogrum_' + i).value); else program_id=program_id+"*"+escape(document.getElementById('hideprogrum_' + i).value);
					if(i==1) var receive_basis = escape(document.getElementById('txtBasis_' + i).value); else receive_basis=receive_basis+"*"+escape(document.getElementById('txtBasis_' + i).value);
					if(i==1) var barcode_id= escape(document.getElementById('hidenBarcode_' + i).value); else barcode_id=barcode_id+"*"+escape(document.getElementById('hidenBarcode_' + i).value);

					if(i==1) var prod_id= escape(document.getElementById('hideprodid_' + i).value); else prod_id=prod_id+"*"+escape(document.getElementById('hideprodid_' + i).value);
					//if(i==1) var floor_id = escape(document.getElementById('cbo_floor_to_' + i).value); else floor_id=floor_id+"*"+escape(document.getElementById('cbo_floor_to_' + i).value);
					//if(j==1) var bin = escape(document.getElementById('txtBin_' + i).value); else bin=bin+"*"+escape(document.getElementById('txtBin_' + i).value);
					if(i==1) var floor_id = escape(document.getElementById('cbo_floor_to_' + i).value); else floor_id=floor_id+"*"+escape(document.getElementById('cbo_floor_to_' + i).value);
					if(i==1) var room_no = escape(document.getElementById('cbo_room_to_' + i).value); else room_no=room_no+"*"+escape(document.getElementById('cbo_room_to_' + i).value);
					if(i==1) var rack = escape(document.getElementById('txt_rack_to_' + i).value); else rack=rack+"*"+escape(document.getElementById('txt_rack_to_' + i).value);
					if(i==1) var self = escape(document.getElementById('txt_shelf_to_' + i).value); else self=self+"*"+escape(document.getElementById('txt_shelf_to_' + i).value);
					if(i==1) var bin = escape(document.getElementById('txt_bin_to_' + i).value);    else bin=bin+"*"+escape(document.getElementById('txt_bin_to_' + i).value);

					if(i==1) var issue_qty=document.getElementById('txtcurrentdelivery_' + i).value; else issue_qty=issue_qty+"*"+document.getElementById('txtcurrentdelivery_' + i).value;

					if(i==1) var roll_no = escape(document.getElementById('txtroll_' + i).value); else roll_no=roll_no+"*"+escape(document.getElementById('txtroll_' + i).value);
					if(i==1) var knitting_source= escape(document.getElementById('knittingsource_' + i).value); else knitting_source=knitting_source+"*"+escape(document.getElementById('knittingsource_' + i).value);

					if(i==1) var buyer_id = escape(document.getElementById('hiddenBuyer_' + i).value); else buyer_id=buyer_id+"*"+escape(document.getElementById('hiddenBuyer_' + i).innerHTML);
					if(i==1) var po_id= escape(document.getElementById('hiddenPoId_' + i).value); else po_id=po_id+"*"+escape(document.getElementById('hiddenPoId_' + i).value);
					if(i==1) var dia= escape(document.getElementById('hidedia_' + i).value); else dia=dia+"*"+escape(document.getElementById('hidedia_' + i).value);
					if(i==1) var determination_id = escape(document.getElementById('hideconstruction_' + i).value); else determination_id=determination_id+"*"+escape(document.getElementById('hideconstruction_' + i).value);

					if(i==1) var body_part= escape(document.getElementById('hidden_bodypart_' + i).value); else body_part=body_part+"*"+escape(document.getElementById('hidden_bodypart_' + i).value);
					if(i==1) var color_id = escape(document.getElementById('hiddenColor_' + i).value); else color_id=color_id+"*"+escape(document.getElementById('hiddenColor_' + i).value);
					if(i==1) var color_range= escape(document.getElementById('hiddenColorRange_' + i).value); else color_range=color_range+"*"+escape(document.getElementById('hiddenColorRange_' + i).value);
					if(i==1) var yean_lot = encodeURIComponent(document.getElementById('hidden_yeanlot_' + i).value); else yean_lot=yean_lot+"*"+encodeURIComponent(document.getElementById('hidden_yeanlot_' + i).value);
					if(i==1) var gsm= escape(document.getElementById('hidegsm_' + i).value); else gsm=gsm+"*"+escape(document.getElementById('hidegsm_' + i).value);

					if(i==1) var uom = escape(document.getElementById('hiddenUom_' + i).value); else uom=uom+"*"+escape(document.getElementById('hiddenUom_' + i).value);
					if(i==1) var yean_cont= escape(document.getElementById('hiddenYeanCount_' + i).value); else yean_cont=yean_cont+"*"+escape(document.getElementById('hiddenYeanCount_' + i).value);
					if(i==1) var band_id = escape(document.getElementById('hiddenBand_' + i).value); else band_id=band_id+"*"+escape(document.getElementById('hiddenBand_' + i).value);

					if(i==1) var shift_id= escape(document.getElementById('hiddenShift_' + i).value); else shift_id=shift_id+"*"+escape(document.getElementById('hiddenShift_' + i).value);
					//if(i==1) var floor_id = escape(document.getElementById('hiddenFloorId_' + i).value); else floor_id=floor_id+"*"+escape(document.getElementById('hiddenFloorId_' + i).value);
					if(i==1) var machine_name = escape(document.getElementById('hiddenMachine_' + i).value); else machine_name=machine_name+"*"+escape(document.getElementById('hiddenMachine_' + i).value);
					if(i==1) var hidden_qty = escape(document.getElementById('hidden_delivery_qty_' + i).value); else hidden_qty=hidden_qty+"*"+escape(document.getElementById('hidden_delivery_qty_' + i).value);

					if(i==1) var stitch_length = encodeURIComponent(document.getElementById('hidden_stl_' + i).value); else stitch_length=stitch_length+"*"+encodeURIComponent(document.getElementById('hidden_stl_' + i).value);
					if(i==1) var roll_rate = escape(document.getElementById('rollRate_' + i).value); else roll_rate=roll_rate+"*"+escape(document.getElementById('rollRate_' + i).value);

					if(i==1) var knitting_charge = escape(document.getElementById('knittingCharge_' + i).value); else knitting_charge=knitting_charge+"*"+escape(document.getElementById('knittingCharge_' + i).value);
					if(i==1) var yarn_rate = escape(document.getElementById('yarnRate_' + i).value); else yarn_rate=yarn_rate+"*"+escape(document.getElementById('yarnRate_' + i).value);
					if(i==1) var hidden_withoutOrder = escape(document.getElementById('hidden_withoutOrder_' + i).value); else hidden_withoutOrder=hidden_withoutOrder+"*"+escape(document.getElementById('hidden_withoutOrder_' + i).value);
					if(i==1) var hidden_booking = escape(document.getElementById('hideBooking_' + i).value); else hidden_booking=hidden_booking+"*"+escape(document.getElementById('hideBooking_' + i).value);
					if(i==1) var isSales = escape(document.getElementById('isSales_' + i).value); else isSales=isSales+"*"+escape(document.getElementById('isSales_' + i).value);
					//if(i==1) var reject_qnty = escape(document.getElementById('hidden_reject_fabric_recv_qnty_' + i).value); else reject_qnty=reject_qnty+"*"+escape(document.getElementById('hidden_reject_fabric_recv_qnty_' + i).value);
					if(i==1) var hidden_qnty_in_pcs = escape(document.getElementById('hidden_qnty_in_pcs_' + i).value*1); else hidden_qnty_in_pcs=hidden_qnty_in_pcs+"*"+escape(document.getElementById('hidden_qnty_in_pcs_' + i).value*1);

					if(i==1) var fsoDeliveryType = escape(document.getElementById('fsoDeliveryType_' + i).value); else fsoDeliveryType=fsoDeliveryType+"*"+escape(document.getElementById('fsoDeliveryType_' + i).value);
				}
			}

			//alert(barcode_id); return;
			var company_id=$("#cbo_company_id").val();
			var cbo_store_name=$("#cbo_store_name").val();
			var txt_receive_date=$("#txt_receive_date").val();
			var txt_receive_chal_no=$("#txt_receive_chal_no").val();
			var cbo_location_name=$("#cbo_location_name").val();
			var cbo_knitting_location_id=$("#cbo_knitting_location_id").val();
			var txt_recieved_id=$("#txt_recieved_id").val();
			var cbo_knitting_source=$("#cbo_knitting_source").val();
			var cbo_knitting_company=$("#cbo_knitting_company").val();
			var yarn_issue_challan_no=$("#txt_yarn_issue_challan_no").val();
			var txt_boe_mushak_challan_no=$("#txt_boe_mushak_challan_no").val();
			var txt_boe_mushak_challan_date=$("#txt_boe_mushak_challan_date").val();
			var txt_remarks=$("#txt_remarks").val();
			var update_id=$("#update_id").val();
			var hidden_delivery_id=$("#hidden_delivery_id").val();
			var hidden_delevery_scan = $("#hidden_delevery_scan").val();
			var hidden_prev_barcode = $("#hidden_prev_barcode").val();
			var hidden_prev_dtls_id = $("#hidden_prev_dtls_id").val();
			var hidden_prev_trans_id = $("#hidden_prev_trans_id").val();
			var hidden_prev_prod_id = $("#hidden_prev_prod_id").val();
			var challan_discurd = $("#challan_discurd").val();
			var txt_challan_no=$("#txt_challan_no").val();

			var data='action=save_update_delete&operation='+operation+
			'&txt_recieved_id='+txt_recieved_id+
			'&cbo_company_id='+company_id+
			'&cbo_store_name='+cbo_store_name+
			'&cbo_location_name='+cbo_location_name+
			'&txt_receive_date='+txt_receive_date+
			'&txt_receive_chal_no='+txt_receive_chal_no+
			'&cbo_knitting_source='+cbo_knitting_source+
			'&cbo_knitting_company='+cbo_knitting_company+
			'&cbo_knitting_location_id='+cbo_knitting_location_id+
			'&yarn_issue_challan_no='+yarn_issue_challan_no+
			'&txt_boe_mushak_challan_no='+txt_boe_mushak_challan_no+
			'&txt_boe_mushak_challan_date='+txt_boe_mushak_challan_date+
			'&txt_remarks='+txt_remarks+
			'&update_id='+update_id+
			'&hidden_delivery_id='+hidden_delivery_id+
			'&txt_challan_no='+txt_challan_no+
			'&hidden_delevery_scan='+hidden_delevery_scan+
			'&challan_discurd='+challan_discurd+
			'&hidden_prev_barcode='+hidden_prev_barcode+
			'&hidden_prev_dtls_id='+hidden_prev_dtls_id+
			'&hidden_prev_trans_id='+hidden_prev_trans_id+
			'&hidden_prev_prod_id='+hidden_prev_prod_id+
			'&check_data='+check_data+
			'&receive_number='+receive_number+
			'&receive_basis='+receive_basis+
			'&barcode_id='+barcode_id+
			'&gsm='+gsm+

			'&program_id='+program_id+
			'&prod_id='+prod_id+
			'&floor_id='+floor_id+
			'&room_no='+room_no+
			'&rack='+rack+
			'&self='+self+
			'&bin='+bin+
			'&issue_qty='+issue_qty+
			'&roll_no='+roll_no+
			'&knitting_source='+knitting_source+
			//'&receive_date='+receive_date+
			'&buyer_id='+buyer_id+
			'&hidden_qty='+hidden_qty+

			'&tran_id='+tran_id+
			'&gray_dtlsid='+gray_dtlsid  +
			'&roll_id='+roll_id+

			'&po_id='+po_id+
			'&dia='+dia+
			'&determination_id='+determination_id+
			'&body_part='+body_part+
			'&color_id='+color_id+
			'&color_range='+color_range+
			'&yean_lot='+yean_lot+
			'&uom='+uom+
			'&yean_cont='+yean_cont+
			'&band_id='+band_id+
			'&shift_id='+shift_id+
			'&stitch_length='+stitch_length+
			'&roll_rate='+roll_rate+
			'&knitting_charge='+knitting_charge+
			'&yarn_rate='+yarn_rate+

			'&machine_name='+machine_name+
			'&hidden_withoutOrder='+hidden_withoutOrder+
			'&hidden_booking='+hidden_booking+
			'&isSales='+isSales
			+'&reject_qnty='+reject_qnty
			+'&hidden_qnty_in_pcs='+hidden_qnty_in_pcs +
			'&fsoDeliveryType='+fsoDeliveryType +
			'&txt_no_bill='+txt_no_bill;

			// alert(data);return;
			freeze_window(operation);
			http.open("POST","requires/grey_fabric_receive_roll_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange =fnc_grey_fabric_receive_Reply_info;
		}
	}

	function fnc_grey_fabric_receive_Reply_info()
	{
		if(http.readyState == 4)
		{
			//release_freezing();	return;
			var reponse=trim(http.responseText).split('**');
                        if(reponse[0]==20)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			show_msg(reponse[0]);
			if(reponse[0]==11)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			if((reponse[0]==0 || reponse[0]==1))
			{
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_recieved_id').value =reponse[2];
				document.getElementById('hidden_delevery_scan').value =reponse[4];
				$('#cbo_company_id').attr('disabled','disabled');
				//$('#cbo_location_name').attr('disabled','disabled');
				set_button_status(1, permission, 'fnc_grey_fabric_receive',1,1);
				$("#print1").removeClass('formbutton_disabled');
				$("#print1").addClass('formbutton');
				$("#print2").removeClass('formbutton_disabled');
				$("#print2").addClass('formbutton');
				$("#print3").removeClass('formbutton_disabled');
				$("#print3").addClass('formbutton');
				$("#print4").removeClass('formbutton_disabled');
				$("#print4").addClass('formbutton');
				$("#print5").removeClass('formbutton_disabled');
				$("#print5").addClass('formbutton');
				$("#print_barcode").removeClass('formbutton_disabled');
				$("#print_barcode").addClass('formbutton');
				$("#btn_fabric_details").removeClass('formbutton_disabled');
				$("#btn_fabric_details").addClass('formbutton');
				$("#print_mg").removeClass('formbutton_disabled');
				$("#print_mg").addClass('formbutton');
				$("#txt_challan_scan").attr('disabled','disabled');
				$("#cbo_store_name").attr('disabled','disabled');
				$("#cbo_location_name").attr('disabled','disabled');

				show_list_view(reponse[1]+"_"+reponse[4]+"_"+$("#cbo_knitting_source").val()+"_"+$("#cbo_company_id").val()+"_"+$("#cbo_location_name").val()+"_"+$("#cbo_store_name").val(), 'grey_item_details_update', 'recipe_items_list_view','requires/grey_fabric_receive_roll_controller', 'setFilterGrid(\'table_body\',-1,tableFilters);' );
			}
			release_freezing();
		}
	}

	function grey_receive_popup()
	{
		var page_link='requires/grey_fabric_receive_roll_controller.php?action=grey_receive_popup_search';
		var title='Grey Receive Form';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var grey_recv_id=this.contentDoc.getElementById("hidden_recv_id").value;
			var grey_recv_no=(this.contentDoc.getElementById("hidden_data").value).split("_");
			if(trim(grey_recv_id)!="")
			{
				//alert(grey_recv_no[8])
				//$("#txt_challan_no").val(grey_recv_no[0]);
				$("#cbo_company_id").val(grey_recv_no[1]);
				$("#txt_company_name").val(grey_recv_no[2]);
				$("#cbo_knitting_source").val(grey_recv_no[3]);
				$("#cbo_knitting_company").val(grey_recv_no[4]);
				$("#txt_knitting_company").val(grey_recv_no[5]);
				$("#hidden_delivery_id").val(grey_recv_no[6]);
				$("#txt_challan_no").val(grey_recv_no[7]);
				$("#is_posted_accout").val(grey_recv_no[8]);
				if(grey_recv_no[8]==1) document.getElementById("accounting_posted_status").innerHTML="Already Posted In Accounting.";
				else document.getElementById("accounting_posted_status").innerHTML="";
				$("#update_id").val(grey_recv_id);
				get_php_form_data(grey_recv_id, "load_php_update_form", "requires/grey_fabric_receive_roll_controller" );
				show_list_view(grey_recv_id+"_"+grey_recv_no[7]+"_"+grey_recv_no[3]+"_"+$("#cbo_company_id").val()+"_"+$("#cbo_location_name").val()+"_"+$("#cbo_store_name").val(), 'grey_item_details_update', 'recipe_items_list_view','requires/grey_fabric_receive_roll_controller', 'setFilterGrid(\'table_body\',-1,tableFilters);' );
				set_button_status(1, permission, 'fnc_grey_fabric_receive',1,1);
				$("#print1").removeClass('formbutton_disabled');
				$("#print1").addClass('formbutton');
				$("#print2").removeClass('formbutton_disabled');
				$("#print2").addClass('formbutton');
				$("#print3").removeClass('formbutton_disabled');
				$("#print3").addClass('formbutton');
				$("#print4").removeClass('formbutton_disabled');
				$("#print4").addClass('formbutton');
				$("#print5").removeClass('formbutton_disabled');
				$("#print5").addClass('formbutton');
				$("#print_barcode").removeClass('formbutton_disabled');
				$("#print_barcode").addClass('formbutton');
				$("#btn_fabric_details").removeClass('formbutton_disabled');
				$("#btn_fabric_details").addClass('formbutton');
				$("#print_mg").removeClass('formbutton_disabled');
				$("#print_mg").addClass('formbutton');
				$("#txt_challan_scan").attr('disabled','disabled');
				//$('#cbo_location_name').attr('disabled','disabled');
				//release_freezing();
			}
		}
	}

	function issue_challan_no()
	{
		var cbo_company_id = $('#cbo_company_id').val();

		if (form_validation('cbo_company_id','Company')==false)
		{
			alert('Browse or Scan/Write Challan No');
			return;
		}
		else
		{
			var page_link='requires/grey_fabric_receive_roll_controller.php?cbo_company_id='+cbo_company_id+'&action=issue_challan_no_popup';
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

	function focace_change()
	{
		$('#txt_challan_scan').focus();
	}

	function challan_no_popup()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var garments_nature =2;
		var page_link='requires/grey_fabric_receive_roll_controller.php?cbo_company_id='+cbo_company_id+'&garments_nature='+garments_nature+'&action=challan_popup';
		var title='Grey Challan Form';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','../');

		var update_id = $('#update_id').val();

		emailwindow.onclose=function()
		{
			//var theform=this.contentDoc.forms[0];
			var process_costing_maintain=this.contentDoc.getElementById("hidden_process_costing").value;
			var grey_recv_no=(this.contentDoc.getElementById("hidden_data").value).split("_");
			/*if(grey_recv_no[3]==3 && process_costing_maintain==1)
			{
				alert("Out-bound Subcontract not allowed in this page because accounting module integrated");
				reset_form('greyreceive_1','recipe_items_list_view','','');
				return;
			}*/
			var grey_recv_id=this.contentDoc.getElementById("hidden_receive_id").value;

			if(update_id !="")
			{
				if(cbo_company_id != grey_recv_no[1]){
					alert("To update system id please select same company's challan");
					return;
				}
			}

			$("#txt_challan_no").val(grey_recv_no[0]);
			$("#cbo_company_id").val(grey_recv_no[1]);
			$("#txt_company_name").val(grey_recv_no[2]);
			$("#cbo_knitting_source").val(grey_recv_no[3]);
			$("#cbo_knitting_company").val(grey_recv_no[4]);
			$("#txt_knitting_company").val(grey_recv_no[6]);
			$("#hidden_delivery_id").val(grey_recv_id);
			$("#store_update_upto").val(grey_recv_no[9]);
			if(grey_recv_no[3]==3)
			{
				$('#txt_receive_chal_no').val(grey_recv_no[7]);
				$('#txt_receive_chal_no').attr('disabled',true);
			}
			else
			{
				$('#txt_receive_chal_no').val("");
				$('#txt_receive_chal_no').attr('disabled',false);
			}

			get_php_form_data( grey_recv_no[1], 'company_wise_report_button_setting','requires/grey_fabric_receive_roll_controller' );
			$("#print1").removeClass('formbutton_disabled');
			$("#print1").addClass('formbutton');
			$("#print2").removeClass('formbutton_disabled');
			$("#print2").addClass('formbutton');
			$("#print3").removeClass('formbutton_disabled');
			$("#print3").addClass('formbutton');
			$("#print4").removeClass('formbutton_disabled');
			$("#print4").addClass('formbutton');
			$("#print5").removeClass('formbutton_disabled');
			$("#print5").addClass('formbutton');
			$("#print_barcode").removeClass('formbutton_disabled');
			$("#print_barcode").addClass('formbutton');
			$("#btn_fabric_details").removeClass('formbutton_disabled');
			$("#btn_fabric_details").addClass('formbutton');
			$("#print_mg").removeClass('formbutton_disabled');
			$("#print_mg").addClass('formbutton');

			if(trim(grey_recv_id)!="")
			{
				//set_button_status(0, permission, 'fnc_grey_fabric_receive',1,1);
				//show_list_view(str+"**"+$('#cbo_knitting_source').val(), 'grey_item_details', 'recipe_items_list_view','requires/grey_fabric_receive_roll_controller', '' );
				show_list_view(grey_recv_no[0]+"**"+grey_recv_no[3], 'grey_item_details', 'recipe_items_list_view','requires/grey_fabric_receive_roll_controller', 'setFilterGrid(\'table_body\',-1,tableFilters);' );
				//load_drop_down( 'requires/grey_fabric_receive_roll_controller', grey_recv_no[1], 'load_drop_down_store', 'store_td');

				if(grey_recv_no[3]==1)
				{
					//load_drop_down( 'requires/grey_fabric_receive_roll_controller', grey_recv_no[4], 'load_drop_down_location', 'location_td');
				}else {
					//load_drop_down( 'requires/grey_fabric_receive_roll_controller', grey_recv_no[1], 'load_drop_down_location', 'location_td');
				}

				load_drop_down( 'requires/grey_fabric_receive_roll_controller', grey_recv_no[1], 'load_drop_down_location', 'location_td');

				$("#cbo_knitting_location_id").val(grey_recv_no[5]);
				$("#txt_knitting_location_id").val(grey_recv_no[8]);
				$('#txt_knitting_location_id').attr('disabled',true);
			}
		}
	}

	$('#txt_challan_scan').live('keydown', function(e) {

		if (e.keyCode === 13) {
			e.preventDefault();
			scan_challan_no(this.value);
		}
	});

	function scan_challan_no(str)
	{
		if(str.length<15)
		{
			alert("Invalid Challan No");
			$('#txt_challan_scan').val('');
			return;
		}

		var update_id = $('#update_id').val();
		var cbo_company_id = $('#cbo_company_id').val();

		if(update_id !="")
		{
			var company_chk = trim(return_global_ajax_value(str, 'previous_challan_comp_chk', '', 'requires/grey_fabric_receive_roll_controller'));
			if(company_chk != cbo_company_id)
			{
				alert("To update system id please select same company's challan");
				return;
			}
		}


		var left_barcode = trim(return_global_ajax_value(str, 'check_left_barcode_exist', '', 'requires/grey_fabric_receive_roll_controller'));

		if(left_barcode == "0")
		{
			alert("Barcode Not Available of this challan");
			$('#txt_challan_scan').val('');
			return;
		}

		/*if(proces_costing_maintain==1)
		{
			alert("Out-bound Subcontract not allowed in this page because accounting module integrated");
			reset_form('greyreceive_1','recipe_items_list_view','','');
			return;
		}
		else
		{*/
			var previous_challan=$('#txt_challan_no').val();
			var updatable_challan=$('#hidden_delevery_scan').val();

			if(previous_challan!="")
			{
				if(updatable_challan!="")
				{
					if(str==updatable_challan)
					{
						alert("Roll of this Challan already Received.");
						$('#txt_challan_scan').val('');
						return;
					}
				}

				if(str==previous_challan)
				{
					$('#txt_challan_scan').val('')	;
					alert("Roll of this challan already shown");
					return;
				}
				else
				{
					r=confirm("Press OK to Save Previous Challan Or Press Cancel to discard previous challan");
					if(r==false)
					{
						$('#txt_challan_no').val(str);
						$('#txt_challan_scan').val('');
						var previous_hdn_barcode = $('#previous_hdn_barcode').val();
						var previous_hdn_dtls_id = $('#previous_hdn_dtls_id').val();
						var previous_hdn_trans_id =$('#previous_hdn_trans_id').val();
						var previous_hdn_prod_id = $('#previous_hdn_prod_id').val();

						var previousChalaDiscurd = 1;

						get_php_form_data( str+"**"+previousChalaDiscurd+"**"+previous_hdn_barcode+"**"+previous_hdn_dtls_id+"**"+previous_hdn_trans_id+"**"+previous_hdn_prod_id, "load_php_mst_form", "requires/grey_fabric_receive_roll_controller" );

						show_list_view(str+"**"+$('#cbo_knitting_source').val(), 'grey_item_details', 'recipe_items_list_view','requires/grey_fabric_receive_roll_controller', 'setFilterGrid(\'table_body\',-1,tableFilters);' );

						//$('#hidden_delevery_scan').val('');
					}
					else
					{
						$('#txt_challan_scan').val('');
						return;
					}
				}
			}
			else
			{
				$('#txt_challan_no').val(str);
				$('#txt_challan_scan').val('');
				var previousChalaDiscurd = 2;
				get_php_form_data( str+"**"+previousChalaDiscurd, "load_php_mst_form", "requires/grey_fabric_receive_roll_controller" );
				show_list_view(str+"**"+$('#cbo_knitting_source').val(), 'grey_item_details', 'recipe_items_list_view','requires/grey_fabric_receive_roll_controller', 'setFilterGrid(\'table_body\',-1,tableFilters);' );
				$('#hidden_delevery_scan').val('');
			}
		//}
	}



	function barcode_print()
	{
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_recieved_id').val()+'*'+$('#update_id').val(),'receive_challan_print');
	}

	function fabric_details()
	{
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_recieved_id').val()+'*'+$('#update_id').val(),'fabric_details_print');
	}



	// ================================== floor_room_rack_shelf section Start ===================================
	function fn_load_floor(store_id)
	{
		var com_id=$('#cbo_company_id').val();
		var location_id=$('#cbo_location_name').val();
		var all_data=com_id + "__" + store_id + "__" + location_id;
		//alert(all_data);return;
		var floor_result = return_global_ajax_value(all_data, 'floor_list', '', 'requires/grey_fabric_receive_roll_controller');
		var tbl_length=$('#table_body tbody tr').length;
		//alert(floor_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(floor_result);
		for(var i=1; i<=tbl_length; i++)
		{

			for (var key of Object.keys(JSONObject).sort())
			{
				//alert(Object.keys(JSONObject));
				// $('#cbo_floor_to_'+i).html('<option value="'+0+'">Select</option>');
				$('#cbo_floor_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
			};
		}
	}

	function fn_load_room(floor_id, sequenceNo)
	{
		// alert(floor_id);return;
		var com_id=$('#cbo_company_id').val();
		var location_id=$('#cbo_location_name').val();
		var store_id=$('#cbo_store_name').val();

		var copy_program_wise=$("#copy_program_wise").is(":checked");
		var copy_color_wise=$("#copy_color_wise").is(":checked");
		var copy_lot_wise=$("#copy_lot_wise").is(":checked");
		var copy_stitch_length=$("#copy_stitch_length").is(":checked");

		var txtProgram = document.getElementById('hideBooking_'+sequenceNo).value;
		var txtColor = document.getElementById('hiddenColor_'+sequenceNo).value;
		var txtLot = document.getElementById('hidden_yeanlot_'+sequenceNo).value;
		var txtStitchLength = document.getElementById('hidden_stitchLength_'+sequenceNo).value;

		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
		//alert(txtProgram);return;
		var room_result = return_global_ajax_value(all_data, 'room_list', '', 'requires/grey_fabric_receive_roll_controller');
		var tbl_length=$('#table_body tbody tr').length-1;
		// alert(room_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(room_result);

		if(copy_program_wise && copy_color_wise && copy_lot_wise && copy_stitch_length && $('#floorIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_floor_to_'+i).prop('disabled')== false)
				{
					var txtProgram_check = document.getElementById('hideBooking_'+i).value;
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
					var txtLot_check = document.getElementById('hidden_yeanlot_'+i).value;
					var txtStitchLength_check = document.getElementById('hidden_stitchLength_'+i).value;
					// alert(i+'='+txtProgram +"=="+ txtProgram_check);
	                if (txtProgram == txtProgram_check && txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#cbo_room_to_'+i).html('<option value="'+0+'">Select</option>');// problem found is
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#cbo_room_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');

						};
	                }
                }
			}
		}
		else if(copy_program_wise && copy_color_wise && copy_lot_wise && $('#floorIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_floor_to_'+i).prop('disabled')== false)
				{
					var txtProgram_check = document.getElementById('hideBooking_'+i).value;
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
					var txtLot_check = document.getElementById('hidden_yeanlot_'+i).value;
					// alert(i+'='+txtColor +"=="+ txtColor_check);
	                if (txtProgram == txtProgram_check && txtColor == txtColor_check && txtLot == txtLot_check)
	                {
						$('#cbo_room_to_'+i).html('<option value="'+0+'">Select</option>');// problem found is
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#cbo_room_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');

						};
	                }
                }
			}
		}
		else if(copy_program_wise && copy_color_wise && copy_stitch_length && $('#floorIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_floor_to_'+i).prop('disabled')== false)
				{
					var txtProgram_check = document.getElementById('hideBooking_'+i).value;
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
					var txtStitchLength_check = document.getElementById('hidden_stitchLength_'+i).value;
					// alert(i+'='+txtColor +"=="+ txtColor_check);
	                if (txtProgram == txtProgram_check && txtColor == txtColor_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#cbo_room_to_'+i).html('<option value="'+0+'">Select</option>');// problem found is
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#cbo_room_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');

						};
	                }
                }
			}
		}
		else if(copy_program_wise && copy_lot_wise && copy_stitch_length && $('#floorIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_floor_to_'+i).prop('disabled')== false)
				{
					var txtProgram_check = document.getElementById('hideBooking_'+i).value;
					var txtLot_check = document.getElementById('hidden_yeanlot_'+i).value;
					var txtStitchLength_check = document.getElementById('hidden_stitchLength_'+i).value;
					// alert(i+'='+txtColor +"=="+ txtColor_check);
	                if (txtProgram == txtProgram_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#cbo_room_to_'+i).html('<option value="'+0+'">Select</option>');// problem found is
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#cbo_room_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');

						};
	                }
                }
			}
		}
		else if(copy_program_wise && copy_color_wise && $('#floorIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_floor_to_'+i).prop('disabled')== false)
				{
					var txtProgram_check = document.getElementById('hideBooking_'+i).value;
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
					// alert(i+'='+txtColor +"=="+ txtColor_check);
	                if (txtProgram == txtProgram_check && txtColor == txtColor_check)
	                {
						$('#cbo_room_to_'+i).html('<option value="'+0+'">Select</option>');// problem found is
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#cbo_room_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');

						};
	                }
                }
			}
		}
		else if(copy_program_wise && copy_lot_wise && $('#floorIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_floor_to_'+i).prop('disabled')== false)
				{
					var txtProgram_check = document.getElementById('hideBooking_'+i).value;
					var txtLot_check = document.getElementById('hidden_yeanlot_'+i).value;
					// alert(i+'='+txtColor +"=="+ txtColor_check);
	                if (txtProgram == txtProgram_check && txtLot == txtLot_check)
	                {
						$('#cbo_room_to_'+i).html('<option value="'+0+'">Select</option>');// problem found is
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#cbo_room_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');

						};
	                }
                }
			}
		}
		else if(copy_program_wise && copy_stitch_length && $('#floorIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_floor_to_'+i).prop('disabled')== false)
				{
					var txtProgram_check = document.getElementById('hideBooking_'+i).value;
					var txtStitchLength_check = document.getElementById('hidden_stitchLength_'+i).value;
					// alert(i+'='+txtColor +"=="+ txtColor_check);
	                if (txtProgram == txtProgram_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#cbo_room_to_'+i).html('<option value="'+0+'">Select</option>');// problem found is
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#cbo_room_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');

						};
	                }
                }
			}
		}
		else if(copy_program_wise && $('#floorIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_floor_to_'+i).prop('disabled')== false)
				{
					var txtProgram_check = document.getElementById('hideBooking_'+i).value;
					//alert(i+'='+txtProgram +"=="+ txtProgram_check);
	                if (txtProgram*1 == txtProgram_check*1)
	                {
						$('#cbo_room_to_'+i).html('<option value="'+0+'">Select</option>');// problem found is
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#cbo_room_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');

						};
	                }
                }
			}
		}
		else if(copy_color_wise && copy_lot_wise && copy_stitch_length && $('#floorIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_floor_to_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
					var txtLot_check = document.getElementById('hidden_yeanlot_'+i).value;
					var txtStitchLength_check = document.getElementById('hidden_stitchLength_'+i).value;
					// alert(i+'='+txtColor +"=="+ txtColor_check);
	                if (txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#cbo_room_to_'+i).html('<option value="'+0+'">Select</option>');// problem found is
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#cbo_room_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');

						};
	                }
                }
			}
		}
		else if(copy_color_wise && copy_lot_wise && $('#floorIds').is(':checked'))
		{
			// alert('color=lot');
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_floor_to_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
					var txtLot_check = document.getElementById('hidden_yeanlot_'+i).value;
					// alert(i+'='+txtColor +"=="+ txtColor_check);
	                if (txtColor == txtColor_check && txtLot == txtLot_check)
	                {
						$('#cbo_room_to_'+i).html('<option value="'+0+'">Select</option>');// problem found is
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#cbo_room_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');

						};
	                }
                }
			}
		}
		else if(copy_color_wise && copy_stitch_length && $('#floorIds').is(':checked'))
		{
			// alert('color=stitch');
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_floor_to_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
					var txtStitchLength_check = document.getElementById('hidden_stitchLength_'+i).value;
					// alert(i+'='+txtColor +"=="+ txtColor_check);
	                if (txtColor == txtColor_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#cbo_room_to_'+i).html('<option value="'+0+'">Select</option>');// problem found is
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#cbo_room_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');

						};
	                }
                }
			}
		}
		else if(copy_lot_wise && copy_stitch_length && $('#floorIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_floor_to_'+i).prop('disabled')== false)
				{
					var txtLot_check = document.getElementById('hidden_yeanlot_'+i).value;
					var txtStitchLength_check = document.getElementById('hidden_stitchLength_'+i).value;
	                if (txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#cbo_room_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#cbo_room_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
	                }
                }
			}
		}
		else if(copy_color_wise && $('#floorIds').is(':checked'))
		{
			// alert('color');
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_floor_to_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
					// alert(i+'='+txtColor +"=="+ txtColor_check);
	                if (txtColor == txtColor_check)
	                {
						$('#cbo_room_to_'+i).html('<option value="'+0+'">Select</option>');// problem found is
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#cbo_room_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');

						};
	                }
                }
			}
		}
		else if(copy_lot_wise && $('#floorIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_floor_to_'+i).prop('disabled')== false)
				{
					var txtLot_check = document.getElementById('hidden_yeanlot_'+i).value;
					// alert(i+'='+txtLot +"=="+ txtLot_check);
	                if (txtLot == txtLot_check)
	                {
						$('#cbo_room_to_'+i).html('<option value="'+0+'">Select</option>');// problem found is
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#cbo_room_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');

						};
	                }
                }
			}
		}
		else if(copy_stitch_length && $('#floorIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_floor_to_'+i).prop('disabled')== false)
				{
					var txtStitchLength_check = document.getElementById('hidden_stitchLength_'+i).value;
	                if (txtStitchLength == txtStitchLength_check)
	                {
						$('#cbo_room_to_'+i).html('<option value="'+0+'">Select</option>');// problem found is
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#cbo_room_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');

						};
	                }
                }
			}
		}
		else if($('#floorIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_floor_to_'+i).prop('disabled')== false)
				{
					$('#cbo_room_to_'+i).html('<option value="'+0+'">Select</option>');// problem found is
					for (var key of Object.keys(JSONObject).sort())
					{
						$('#cbo_room_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
					};
				}
			}
		}
		else
		{
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#cbo_room_to_'+sequenceNo).html('<option value="'+0+'">Select</option>');
				$('#cbo_room_to_'+sequenceNo).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
			};
		}
	}

	function fn_load_rack(room_id, sequenceNo)
	{
		// alert(floor_id);return;
		var com_id=$('#cbo_company_id').val();
		var location_id=$('#cbo_location_name').val();
		var store_id=$('#cbo_store_name').val();

		var copy_program_wise=$("#copy_program_wise").is(":checked");
		var copy_color_wise=$("#copy_color_wise").is(":checked");
		var copy_lot_wise=$("#copy_lot_wise").is(":checked");
		var copy_stitch_length=$("#copy_stitch_length").is(":checked");

		var txtProgram = document.getElementById('hideBooking_'+sequenceNo).value;
		var txtColor = document.getElementById('hiddenColor_'+sequenceNo).value;
		var txtLot = document.getElementById('hidden_yeanlot_'+sequenceNo).value;
		var txtStitchLength = document.getElementById('hidden_stitchLength_'+sequenceNo).value;

		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + room_id;
		//alert(all_data);return;
		var rack_result = return_global_ajax_value(all_data, 'rack_list', '', 'requires/grey_fabric_receive_roll_controller');
		var tbl_length=$('#table_body tbody tr').length-1;
		//alert(rack_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(rack_result);

		if(copy_program_wise && copy_color_wise && copy_lot_wise && copy_stitch_length && $('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_room_to_'+i).prop('disabled')== false)
				{
					var txtProgram_check = document.getElementById('hideBooking_'+i).value;
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
					var txtLot_check = document.getElementById('hidden_yeanlot_'+i).value;
					var txtStitchLength_check = document.getElementById('hidden_stitchLength_'+i).value;
	                if (txtProgram == txtProgram_check && txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#txt_rack_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_rack_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_program_wise && copy_color_wise && copy_lot_wise && $('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_room_to_'+i).prop('disabled')== false)
				{
					var txtProgram_check = document.getElementById('hideBooking_'+i).value;
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
					var txtLot_check = document.getElementById('hidden_yeanlot_'+i).value;
	                if (txtProgram == txtProgram_check && txtColor == txtColor_check && txtLot == txtLot_check)
	                {
						$('#txt_rack_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_rack_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_program_wise && copy_color_wise && copy_stitch_length && $('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_room_to_'+i).prop('disabled')== false)
				{
					var txtProgram_check = document.getElementById('hideBooking_'+i).value;
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
					var txtStitchLength_check = document.getElementById('hidden_stitchLength_'+i).value;
	                if (txtProgram == txtProgram_check && txtColor == txtColor_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#txt_rack_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_rack_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_program_wise && copy_lot_wise && copy_stitch_length && $('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_room_to_'+i).prop('disabled')== false)
				{
					var txtProgram_check = document.getElementById('hideBooking_'+i).value;
					var txtLot_check = document.getElementById('hidden_yeanlot_'+i).value;
					var txtStitchLength_check = document.getElementById('hidden_stitchLength_'+i).value;
	                if (txtProgram == txtProgram_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#txt_rack_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_rack_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_program_wise && copy_color_wise && $('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_room_to_'+i).prop('disabled')== false)
				{
					var txtProgram_check = document.getElementById('hideBooking_'+i).value;
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
	                if (txtProgram == txtProgram_check && txtColor == txtColor_check)
	                {
						$('#txt_rack_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_rack_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		if(copy_program_wise && copy_lot_wise && $('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_room_to_'+i).prop('disabled')== false)
				{
					var txtProgram_check = document.getElementById('hideBooking_'+i).value;
					var txtLot_check = document.getElementById('hidden_yeanlot_'+i).value;
	                if (txtProgram == txtProgram_check && txtLot == txtLot_check)
	                {
						$('#txt_rack_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_rack_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_program_wise && copy_stitch_length && $('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_room_to_'+i).prop('disabled')== false)
				{
					var txtProgram_check = document.getElementById('hideBooking_'+i).value;
					var txtStitchLength_check = document.getElementById('hidden_stitchLength_'+i).value;
	                if (txtProgram == txtProgram_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#txt_rack_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_rack_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_program_wise && $('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_room_to_'+i).prop('disabled')== false)
				{
					var txtProgram_check = document.getElementById('hideBooking_'+i).value;
	                if (txtProgram == txtProgram_check)
	                {
						$('#txt_rack_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_rack_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && copy_lot_wise && copy_stitch_length && $('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_room_to_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
					var txtLot_check = document.getElementById('hidden_yeanlot_'+i).value;
					var txtStitchLength_check = document.getElementById('hidden_stitchLength_'+i).value;
	                if (txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#txt_rack_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_rack_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && copy_lot_wise && $('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_room_to_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
					var txtLot_check = document.getElementById('hidden_yeanlot_'+i).value;
	                if (txtColor == txtColor_check && txtLot == txtLot_check)
	                {
						$('#txt_rack_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_rack_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && copy_stitch_length && $('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_room_to_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
					var txtStitchLength_check = document.getElementById('hidden_stitchLength_'+i).value;
	                if (txtColor == txtColor_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#txt_rack_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_rack_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_lot_wise && copy_stitch_length && $('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_room_to_'+i).prop('disabled')== false)
				{
					var txtLot_check = document.getElementById('hidden_yeanlot_'+i).value;
					var txtStitchLength_check = document.getElementById('hidden_stitchLength_'+i).value;
	                if (txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#txt_rack_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_rack_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && $('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_room_to_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
	                if (txtColor == txtColor_check)
	                {
						$('#txt_rack_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							// $('#txt_rack_to_'+i).html('<option value="'+0+'">Select</option>');
							$('#txt_rack_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_lot_wise && $('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_room_to_'+i).prop('disabled')== false)
				{
					var txtLot_check = document.getElementById('hidden_yeanlot_'+i).value;
	                if (txtLot == txtLot_check)
	                {
						$('#txt_rack_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							// $('#txt_rack_to_'+i).html('<option value="'+0+'">Select</option>');
							$('#txt_rack_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_stitch_length && $('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_room_to_'+i).prop('disabled')== false)
				{
					var txtStitchLength_check = document.getElementById('hidden_stitchLength_'+i).value;
	                if (txtStitchLength == txtStitchLength_check)
	                {
						$('#txt_rack_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_rack_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if($('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#cbo_room_to_'+i).prop('disabled')== false)
				{
					$('#txt_rack_to_'+i).html('<option value="'+0+'">Select</option>');
					for (var key of Object.keys(JSONObject).sort())
					{
						// $('#txt_rack_to_'+i).html('<option value="'+0+'">Select</option>');
						$('#txt_rack_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
					};
				}
			}
		}
		else
		{
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#txt_rack_to_'+sequenceNo).html('<option value="'+0+'">Select</option>');
				$('#txt_rack_to_'+sequenceNo).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
			};
		}
	}

	function fn_load_shelf(rack_id, sequenceNo)
	{
		// alert(floor_id);return;
		var com_id=$('#cbo_company_id').val();
		var location_id=$('#cbo_location_name').val();
		var store_id=$('#cbo_store_name').val();

		var copy_program_wise=$("#copy_program_wise").is(":checked");
		var copy_color_wise=$("#copy_color_wise").is(":checked");
		var copy_lot_wise=$("#copy_lot_wise").is(":checked");
		var copy_stitch_length=$("#copy_stitch_length").is(":checked");

		var txtProgram = document.getElementById('hideBooking_'+sequenceNo).value;
		var txtColor = document.getElementById('hiddenColor_'+sequenceNo).value;
		var txtLot = document.getElementById('hidden_yeanlot_'+sequenceNo).value;
		var txtStitchLength = document.getElementById('hidden_stitchLength_'+sequenceNo).value;

		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + rack_id;
		//alert(all_data);return;
		var shelf_result = return_global_ajax_value(all_data, 'shelf_list', '', 'requires/grey_fabric_receive_roll_controller');
		var tbl_length=$('#table_body tbody tr').length-1;
		// alert(shelf_result+"="+tbl_length+"="+sequenceNo);//return;
		var JSONObject = JSON.parse(shelf_result);

		if(copy_program_wise && copy_color_wise && copy_lot_wise && copy_stitch_length && $('#rackIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#txt_rack_to_'+i).prop('disabled')== false)
				{
					var txtProgram_check = document.getElementById('hideBooking_'+i).value;
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
					var txtLot_check = document.getElementById('hidden_yeanlot_'+i).value;
					var txtStitchLength_check = document.getElementById('hidden_stitchLength_'+i).value;
	                if (txtProgram == txtProgram_check && txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#txt_shelf_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_shelf_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_program_wise && copy_color_wise && copy_lot_wise && $('#rackIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#txt_rack_to_'+i).prop('disabled')== false)
				{
					var txtProgram_check = document.getElementById('hideBooking_'+i).value;
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
					var txtLot_check = document.getElementById('hidden_yeanlot_'+i).value;
	                if (txtProgram == txtProgram_check && txtColor == txtColor_check && txtLot == txtLot_check)
	                {
						$('#txt_shelf_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_shelf_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_program_wise && copy_color_wise && copy_stitch_length && $('#rackIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#txt_rack_to_'+i).prop('disabled')== false)
				{
					var txtProgram_check = document.getElementById('hideBooking_'+i).value;
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
					var txtStitchLength_check = document.getElementById('hidden_stitchLength_'+i).value;
	                if (txtProgram == txtProgram_check && txtColor == txtColor_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#txt_shelf_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_shelf_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_program_wise && copy_color_wise && $('#rackIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#txt_rack_to_'+i).prop('disabled')== false)
				{
					var txtProgram_check = document.getElementById('hideBooking_'+i).value;
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
	                if (txtProgram == txtProgram_check && txtColor == txtColor_check)
	                {
						$('#txt_shelf_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_shelf_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_program_wise && $('#rackIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#txt_rack_to_'+i).prop('disabled')== false)
				{
					var txtProgram_check = document.getElementById('hideBooking_'+i).value;
	                if (txtProgram == txtProgram_check)
	                {
						$('#txt_shelf_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_shelf_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && copy_lot_wise && copy_stitch_length && $('#rackIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#txt_rack_to_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
					var txtLot_check = document.getElementById('hidden_yeanlot_'+i).value;
					var txtStitchLength_check = document.getElementById('hidden_stitchLength_'+i).value;
	                if (txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#txt_shelf_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_shelf_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && copy_lot_wise && $('#rackIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#txt_rack_to_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
					var txtLot_check = document.getElementById('hidden_yeanlot_'+i).value;
	                if (txtColor == txtColor_check && txtLot == txtLot_check)
	                {
						$('#txt_shelf_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_shelf_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && copy_stitch_length && $('#rackIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#txt_rack_to_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
					var txtStitchLength_check = document.getElementById('hidden_stitchLength_'+i).value;
	                if (txtColor == txtColor_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#txt_shelf_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_shelf_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_lot_wise && copy_stitch_length && $('#rackIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#txt_rack_to_'+i).prop('disabled')== false)
				{
					var txtLot_check = document.getElementById('hidden_yeanlot_'+i).value;
					var txtStitchLength_check = document.getElementById('hidden_stitchLength_'+i).value;
	                if (txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#txt_shelf_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_shelf_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && $('#rackIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#txt_rack_to_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
	                if (txtColor == txtColor_check)
	                {
						$('#txt_shelf_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_shelf_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_lot_wise && $('#rackIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#txt_rack_to_'+i).prop('disabled')== false)
				{
					var txtLot_check = document.getElementById('hidden_yeanlot_'+i).value;
	                if (txtLot == txtLot_check)
	                {
						$('#txt_shelf_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_shelf_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_stitch_length && $('#rackIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#txt_rack_to_'+i).prop('disabled')== false)
				{
					var txtStitchLength_check = document.getElementById('hidden_stitchLength_'+i).value;
	                if (txtStitchLength == txtStitchLength_check)
	                {
						$('#txt_shelf_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_shelf_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if($('#rackIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#txt_rack_to_'+i).prop('disabled')== false)
				{
					$('#txt_shelf_to_'+i).html('<option value="'+0+'">Select</option>');
					for (var key of Object.keys(JSONObject).sort())
					{
						$('#txt_shelf_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
					};
				}
			}
		}
		else
		{
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#txt_shelf_to_'+sequenceNo).html('<option value="'+0+'">Select</option>');
				$('#txt_shelf_to_'+sequenceNo).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
			};
		}
	}

	function fn_load_bin(shelf_id, sequenceNo)
	{
		// alert(floor_id);return;
		var com_id=$('#cbo_company_id').val();
		var location_id=$('#cbo_location_name').val();
		var store_id=$('#cbo_store_name').val();

		var copy_program_wise=$("#copy_program_wise").is(":checked");
		var copy_color_wise=$("#copy_color_wise").is(":checked");
		var copy_lot_wise=$("#copy_lot_wise").is(":checked");
		var copy_stitch_length=$("#copy_stitch_length").is(":checked");

		var txtProgram = document.getElementById('hideBooking_'+sequenceNo).value;
		var txtColor = document.getElementById('hiddenColor_'+sequenceNo).value;
		var txtLot = document.getElementById('hidden_yeanlot_'+sequenceNo).value;
		var txtStitchLength = document.getElementById('hidden_stitchLength_'+sequenceNo).value;

		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + shelf_id;
		//alert(all_data);return;
		var shelf_result = return_global_ajax_value(all_data, 'bin_list', '', 'requires/grey_fabric_receive_roll_controller');
		var tbl_length=$('#table_body tbody tr').length-1;
		//alert(shelf_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(shelf_result);

		if(copy_program_wise && copy_color_wise && copy_lot_wise && copy_stitch_length && $('#shelfIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#txt_shelf_to_'+i).prop('disabled')== false)
				{
					var txtProgram_check = document.getElementById('hideBooking_'+i).value;
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
					var txtLot_check = document.getElementById('hidden_yeanlot_'+i).value;
					var txtStitchLength_check = document.getElementById('hidden_stitchLength_'+i).value;
	                if (txtProgram == txtProgram_check && txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
					{
						$('#txt_bin_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_bin_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_program_wise && copy_color_wise && copy_lot_wise && $('#shelfIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#txt_shelf_to_'+i).prop('disabled')== false)
				{
					var txtProgram_check = document.getElementById('hideBooking_'+i).value;
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
					var txtLot_check = document.getElementById('hidden_yeanlot_'+i).value;
	                if (txtProgram == txtProgram_check && txtColor == txtColor_check && txtLot == txtLot_check)
					{
						$('#txt_bin_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_bin_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_program_wise && copy_color_wise && copy_stitch_length && $('#shelfIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#txt_shelf_to_'+i).prop('disabled')== false)
				{
					var txtProgram_check = document.getElementById('hideBooking_'+i).value;
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
					var txtStitchLength_check = document.getElementById('hidden_stitchLength_'+i).value;
	                if (txtProgram == txtProgram_check && txtColor == txtColor_check && txtStitchLength == txtStitchLength_check)
					{
						$('#txt_bin_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_bin_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_program_wise && copy_color_wise && $('#shelfIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#txt_shelf_to_'+i).prop('disabled')== false)
				{
					var txtProgram_check = document.getElementById('hideBooking_'+i).value;
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
	                if (txtProgram == txtProgram_check && txtColor == txtColor_check)
					{
						$('#txt_bin_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_bin_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_program_wise && $('#shelfIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#txt_shelf_to_'+i).prop('disabled')== false)
				{
					var txtProgram_check = document.getElementById('hideBooking_'+i).value;
	                if (txtProgram == txtProgram_check)
					{
						$('#txt_bin_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_bin_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && copy_lot_wise && copy_stitch_length && $('#shelfIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#txt_shelf_to_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
					var txtLot_check = document.getElementById('hidden_yeanlot_'+i).value;
					var txtStitchLength_check = document.getElementById('hidden_stitchLength_'+i).value;
	                if (txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
					{
						$('#txt_bin_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_bin_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && copy_lot_wise && $('#shelfIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#txt_shelf_to_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
					var txtLot_check = document.getElementById('hidden_yeanlot_'+i).value;
	                if (txtColor == txtColor_check && txtLot == txtLot_check)
					{
						$('#txt_bin_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_bin_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && copy_stitch_length && $('#shelfIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#txt_shelf_to_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
					var txtStitchLength_check = document.getElementById('hidden_stitchLength_'+i).value;
	                if (txtColor == txtColor_check && txtStitchLength == txtStitchLength_check)
					{
						$('#txt_bin_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_bin_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_lot_wise && copy_stitch_length && $('#shelfIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#txt_shelf_to_'+i).prop('disabled')== false)
				{
					var txtLot_check = document.getElementById('hidden_yeanlot_'+i).value;
					var txtStitchLength_check = document.getElementById('hidden_stitchLength_'+i).value;
	                if (txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
					{
						$('#txt_bin_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_bin_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && $('#shelfIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#txt_shelf_to_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('hiddenColor_'+i).value;
	                if (txtColor == txtColor_check)
					{
						$('#txt_bin_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_bin_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_lot_wise && $('#shelfIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#txt_shelf_to_'+i).prop('disabled')== false)
				{
					var txtLot_check = document.getElementById('hidden_yeanlot_'+i).value;
	                if (txtLot == txtLot_check)
					{
						$('#txt_bin_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_bin_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_stitch_length && $('#shelfIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#txt_shelf_to_'+i).prop('disabled')== false)
				{
					var txtStitchLength_check = document.getElementById('hidden_stitchLength_'+i).value;
	                if (txtStitchLength == txtStitchLength_check)
					{
						$('#txt_bin_to_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txt_bin_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if($('#shelfIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				if ( $('#txt_shelf_to_'+i).prop('disabled')== false)
				{
					$('#txt_bin_to_'+i).html('<option value="'+0+'">Select</option>');
					for (var key of Object.keys(JSONObject).sort())
					{
						$('#txt_bin_to_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
					};
				}
			}
		}
		else
		{
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#txt_bin_to_'+sequenceNo).html('<option value="'+0+'">Select</option>');
				$('#txt_bin_to_'+sequenceNo).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
			};
		}
	}

	function copy_all(str)
	{
		var data=str.split("_");
		var trall=$("#txt_tr_length").val();
		var copy_tr=parseInt(trall);

		var copy_program_wise=$("#copy_program_wise").is(":checked");
        var copy_color_wise=$("#copy_color_wise").is(":checked");
        var copy_lot_wise=$("#copy_lot_wise").is(":checked");
        var copy_stitch_length=$("#copy_stitch_length").is(":checked");

        var txtProgram = document.getElementById('hideBooking_'+data[0]).value;
		var txtColor = document.getElementById('hiddenColor_'+data[0]).value;
		var txtLot = document.getElementById('hidden_yeanlot_'+data[0]).value;
		var txtStitchLength = document.getElementById('hidden_stitchLength_'+data[0]).value;

		if($('#floorIds').is(':checked'))
		{
			if(data[1]==0) data_value=$("#cbo_floor_to_"+data[0]).val();
		}
		if($('#roomIds').is(':checked'))
		{
			if(data[1]==1) data_value=$("#cbo_room_to_"+data[0]).val();
		}
		if($('#rackIds').is(':checked'))
		{
			if(data[1]==2) data_value=$("#txt_rack_to_"+data[0]).val();
		}
		if($('#shelfIds').is(':checked'))
		{
			if(data[1]==3) data_value=$("#txt_shelf_to_"+data[0]).val();
		}
		if($('#binIds').is(':checked'))
		{
			if(data[1]==4) data_value=$("#txt_bin_to_"+data[0]).val();
		}

		// ======================================================
        // alert($('#table_body tbody tr:not([style*="display: none"])').length-1);
        // alert($('#table_body tbody tr:visible').length-1);
		// ======================================================
		var first_tr=parseInt(data[0])+1;
		for(var k=first_tr; k<=copy_tr; k++)
		{
			var txtProgram_check = document.getElementById('hideBooking_'+k).value;
			var txtColor_check = document.getElementById('hiddenColor_'+k).value;
			var txtLot_check = document.getElementById('hidden_yeanlot_'+k).value;
			var txtStitchLength_check = document.getElementById('hidden_stitchLength_'+k).value;
			// Floor
			if(copy_program_wise && copy_color_wise && copy_lot_wise && copy_stitch_length && $('#floorIds').is(':checked'))
	        {
                if (txtProgram == txtProgram_check && txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#cbo_floor_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==0) 	$("#cbo_floor_to_"+k).val(data_value);
                	}
                }
	        }
	        else if(copy_program_wise && copy_color_wise && copy_lot_wise && $('#floorIds').is(':checked'))
	        {
                if (txtProgram == txtProgram_check && txtColor == txtColor_check && txtLot == txtLot_check)
                {
                	if ( $('#cbo_floor_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==0) 	$("#cbo_floor_to_"+k).val(data_value);
                	}
                }
	        }
	        else if(copy_program_wise && copy_color_wise && copy_stitch_length && $('#floorIds').is(':checked'))
	        {
                if (txtProgram == txtProgram_check && txtColor == txtColor_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#cbo_floor_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==0) 	$("#cbo_floor_to_"+k).val(data_value);
                	}
                }
	        }
	        else if(copy_program_wise && copy_color_wise && $('#floorIds').is(':checked'))
	        {
                if (txtProgram == txtProgram_check && txtColor == txtColor_check)
                {
                	if ( $('#cbo_floor_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==0) 	$("#cbo_floor_to_"+k).val(data_value);
                	}
                }
	        }
	        else if(copy_program_wise && $('#floorIds').is(':checked'))
	        {
                if (txtProgram == txtProgram_check)
                {
                	// alert(x+'='+floor_id);
                	// alert(txtColor+'='+txtColor_check);
                	if ( $('#cbo_floor_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==0) 	$("#cbo_floor_to_"+k).val(data_value);
                    }
                }
	        }
			else if(copy_color_wise && copy_lot_wise && copy_stitch_length && $('#floorIds').is(':checked'))
	        {
                if (txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#cbo_floor_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==0) 	$("#cbo_floor_to_"+k).val(data_value);
                	}
                }
	        }
			else if(copy_color_wise && copy_lot_wise && $('#floorIds').is(':checked'))
	        {
                if (txtColor == txtColor_check && txtLot == txtLot_check)
                {
                	if ( $('#cbo_floor_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==0) 	$("#cbo_floor_to_"+k).val(data_value);
                    }
                }
	        }
			else if(copy_color_wise && copy_stitch_length && $('#floorIds').is(':checked'))
			{
				if (txtColor == txtColor_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#cbo_floor_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==0) 	$("#cbo_floor_to_"+k).val(data_value);
                    }
                }
			}
			else if(copy_lot_wise && copy_stitch_length && $('#floorIds').is(':checked'))
			{
				if (txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#cbo_floor_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==0) 	$("#cbo_floor_to_"+k).val(data_value);
                    }
                }
			}
			else if(copy_color_wise && $('#floorIds').is(':checked'))
	        {
                if (txtColor == txtColor_check)
                {
                	// alert(x+'='+floor_id);
                	// alert(txtColor+'='+txtColor_check);
                	if ( $('#cbo_floor_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==0) 	$("#cbo_floor_to_"+k).val(data_value);
                    }
                }
	        }
	        else if(copy_lot_wise && $('#floorIds').is(':checked'))
	        {
                if (txtLot == txtLot_check)
                {
                	// alert(x+'='+floor_id);
                	// alert(txtColor+'='+txtColor_check);
                	if ( $('#cbo_floor_to_'+k).prop('disabled')== false)
					{
                   		if(data[1]==0) 	$("#cbo_floor_to_"+k).val(data_value);
                   	}
                }
	        }
	        else if(copy_stitch_length && $('#floorIds').is(':checked'))
	        {
                if (txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#cbo_floor_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==0) 	$("#cbo_floor_to_"+k).val(data_value);
                    }
                }
	        }
	        else if($('#floorIds').is(':checked'))
	        {
	        	if ( $('#cbo_floor_to_'+k).prop('disabled')== false)
				{
					// alert('floor='+k);
	        		if(data[1]==0) 	$("#cbo_floor_to_"+k).val(data_value);
	        	}
	        }
	        // Room
	        if(copy_program_wise && copy_color_wise && copy_lot_wise && copy_stitch_length && $('#roomIds').is(':checked'))
	        {
                if (txtProgram == txtProgram_check && txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#cbo_room_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==1) 	$("#cbo_room_to_"+k).val(data_value);
                    }
                }
	        }
	        else if(copy_program_wise && copy_color_wise && copy_lot_wise && $('#roomIds').is(':checked'))
	        {
                if (txtProgram == txtProgram_check && txtColor == txtColor_check && txtLot == txtLot_check)
                {
                	if ( $('#cbo_room_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==1) 	$("#cbo_room_to_"+k).val(data_value);
                    }
                }
	        }
	        else if(copy_program_wise && copy_color_wise && copy_stitch_length && $('#roomIds').is(':checked'))
	        {
                if (txtProgram == txtProgram_check && txtColor == txtColor_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#cbo_room_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==1) 	$("#cbo_room_to_"+k).val(data_value);
                    }
                }
	        }
	        else if(copy_program_wise && copy_color_wise && $('#roomIds').is(':checked'))
	        {
                if (txtProgram == txtProgram_check && txtColor == txtColor_check)
                {
                	if ( $('#cbo_room_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==1) 	$("#cbo_room_to_"+k).val(data_value);
                    }
                }
	        }
	        else if(copy_program_wise && $('#roomIds').is(':checked'))
	        {
                if (txtProgram == txtProgram_check)
                {
                	if ( $('#cbo_room_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==1) 	$("#cbo_room_to_"+k).val(data_value);
                    }
                }
	        }
	        else if(copy_color_wise && copy_lot_wise && copy_stitch_length && $('#roomIds').is(':checked'))
	        {
                if (txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#cbo_room_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==1) 	$("#cbo_room_to_"+k).val(data_value);
                    }
                }
	        }
			else if(copy_color_wise && copy_lot_wise && $('#roomIds').is(':checked'))
	        {
                if (txtColor == txtColor_check && txtLot == txtLot_check)
                {
                	if ( $('#cbo_room_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==1) 	$("#cbo_room_to_"+k).val(data_value);
                    }
                }
	        }
			else if(copy_color_wise && copy_stitch_length && $('#roomIds').is(':checked'))
			{
				if (txtColor == txtColor_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#cbo_room_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==1) 	$("#cbo_room_to_"+k).val(data_value);
                    }
                }
			}
			else if(copy_lot_wise && copy_stitch_length && $('#roomIds').is(':checked'))
			{
				if (txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#cbo_room_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==1) 	$("#cbo_room_to_"+k).val(data_value);
                    }
                }
			}
			else if(copy_color_wise && $('#roomIds').is(':checked'))
			{
				if (txtColor == txtColor_check)
				{
					if ( $('#cbo_room_to_'+k).prop('disabled')== false)
					{
						if(data[1]==1) 	$("#cbo_room_to_"+k).val(data_value);
					}
				}
			}
			else if(copy_lot_wise && $('#roomIds').is(':checked'))
			{
				if (txtLot == txtLot_check)
				{
					if ( $('#cbo_room_to_'+k).prop('disabled')== false)
					{
						if(data[1]==1) 	$("#cbo_room_to_"+k).val(data_value);
					}
				}
			}
			else if(copy_stitch_length && $('#roomIds').is(':checked'))
			{
				if (txtStitchLength == txtStitchLength_check)
				{
					if ( $('#cbo_room_to_'+k).prop('disabled')== false)
					{
						if(data[1]==1) 	$("#cbo_room_to_"+k).val(data_value);
					}
				}
			}
			else if($('#roomIds').is(':checked'))
	        {
	        	if ( $('#cbo_room_to_'+k).prop('disabled')== false)
				{
	        		if(data[1]==1) 	$("#cbo_room_to_"+k).val(data_value);
	        	}
	        }
	        // Rack
	        if(copy_program_wise && copy_color_wise && copy_lot_wise && copy_stitch_length && $('#rackIds').is(':checked'))
	        {
                if (txtProgram == txtProgram_check && txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#txt_rack_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==2) 	$("#txt_rack_to_"+k).val(data_value);
                    }
                }
	        }
	        else if(copy_program_wise && copy_color_wise && copy_lot_wise && $('#rackIds').is(':checked'))
	        {
                if (txtProgram == txtProgram_check && txtColor == txtColor_check && txtLot == txtLot_check)
                {
                	if ( $('#txt_rack_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==2) 	$("#txt_rack_to_"+k).val(data_value);
                    }
                }
	        }
	        else if(copy_program_wise && copy_color_wise && copy_stitch_length && $('#rackIds').is(':checked'))
	        {
                if (txtProgram == txtProgram_check && txtColor == txtColor_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#txt_rack_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==2) 	$("#txt_rack_to_"+k).val(data_value);
                    }
                }
	        }
	        else if(copy_program_wise && copy_color_wise && $('#rackIds').is(':checked'))
	        {
                if (txtProgram == txtProgram_check && txtColor == txtColor_check)
                {
                	if ( $('#txt_rack_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==2) 	$("#txt_rack_to_"+k).val(data_value);
                    }
                }
	        }
	        else if(copy_program_wise && $('#rackIds').is(':checked'))
	        {
                if (txtProgram == txtProgram_check)
                {
                	if ( $('#txt_rack_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==2) 	$("#txt_rack_to_"+k).val(data_value);
                    }
                }
	        }
	        else if(copy_color_wise && copy_lot_wise && copy_stitch_length && $('#rackIds').is(':checked'))
	        {
                if (txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#txt_rack_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==2) 	$("#txt_rack_to_"+k).val(data_value);
                    }
                }
	        }
			else if(copy_color_wise && copy_lot_wise && $('#rackIds').is(':checked'))
	        {
                if (txtColor == txtColor_check && txtLot == txtLot_check)
                {
                	if ( $('#txt_rack_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==2) 	$("#txt_rack_to_"+k).val(data_value);
                    }
                }
	        }
			else if(copy_color_wise && copy_stitch_length && $('#rackIds').is(':checked'))
			{
				if (txtColor == txtColor_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#txt_rack_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==2) 	$("#txt_rack_to_"+k).val(data_value);
                    }
                }
			}
			else if(copy_lot_wise && copy_stitch_length && $('#rackIds').is(':checked'))
			{
				if (txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#txt_rack_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==2) 	$("#txt_rack_to_"+k).val(data_value);
                    }
                }
			}
	        else if(copy_color_wise && $('#rackIds').is(':checked'))
	        {
	        	if (txtColor == txtColor_check)
				{
					if ( $('#txt_rack_to_'+k).prop('disabled')== false)
					{
	        			if(data[1]==2) 	$("#txt_rack_to_"+k).val(data_value);
	        		}
	        	}
	        }
	        else if(copy_lot_wise && $('#rackIds').is(':checked'))
	        {
	        	if (txtLot == txtLot_check)
				{
					if ( $('#txt_rack_to_'+k).prop('disabled')== false)
					{
	        			if(data[1]==2) 	$("#txt_rack_to_"+k).val(data_value);
	        		}
	        	}
	        }
	        else if(copy_stitch_length && $('#rackIds').is(':checked'))
	        {
	        	if (txtStitchLength == txtStitchLength_check)
				{
					if ( $('#txt_rack_to_'+k).prop('disabled')== false)
					{
	        			if(data[1]==2) 	$("#txt_rack_to_"+k).val(data_value);
	        		}
	        	}
	        }
			else if($('#rackIds').is(':checked'))
			{
				if ( $('#txt_rack_to_'+k).prop('disabled')== false)
				{
					if(data[1]==2) 	$("#txt_rack_to_"+k).val(data_value);
				}
			}
			// Shelf
			if(copy_program_wise && copy_color_wise && copy_lot_wise && copy_stitch_length && $('#shelfIds').is(':checked'))
	        {
                if (txtProgram == txtProgram_check && txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#txt_shelf_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==3) 	$("#txt_shelf_to_"+k).val(data_value);
                    }
                }
	        }
	        else if(copy_program_wise && copy_color_wise && copy_lot_wise && $('#shelfIds').is(':checked'))
	        {
                if (txtProgram == txtProgram_check && txtColor == txtColor_check && txtLot == txtLot_check)
                {
                	if ( $('#txt_shelf_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==3) 	$("#txt_shelf_to_"+k).val(data_value);
                    }
                }
	        }
	        else if(copy_program_wise && copy_color_wise && copy_stitch_length && $('#shelfIds').is(':checked'))
	        {
                if (txtProgram == txtProgram_check && txtColor == txtColor_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#txt_shelf_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==3) 	$("#txt_shelf_to_"+k).val(data_value);
                    }
                }
	        }
	        else if(copy_program_wise && copy_color_wise && $('#shelfIds').is(':checked'))
	        {
                if (txtProgram == txtProgram_check && txtColor == txtColor_check)
                {
                	if ( $('#txt_shelf_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==3) 	$("#txt_shelf_to_"+k).val(data_value);
                    }
                }
	        }
	        else if(copy_program_wise && $('#shelfIds').is(':checked'))
	        {
                if (txtProgram == txtProgram_check)
                {
                	if ( $('#txt_shelf_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==3) 	$("#txt_shelf_to_"+k).val(data_value);
                    }
                }
	        }
			else if(copy_color_wise && copy_lot_wise && copy_stitch_length && $('#shelfIds').is(':checked'))
	        {
                if (txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#txt_shelf_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==3) 	$("#txt_shelf_to_"+k).val(data_value);
                    }
                }
	        }
			else if(copy_color_wise && copy_lot_wise && $('#shelfIds').is(':checked'))
	        {
                if (txtColor == txtColor_check && txtLot == txtLot_check)
                {
                	if ( $('#txt_shelf_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==3) 	$("#txt_shelf_to_"+k).val(data_value);
                    }
                }
	        }
			else if(copy_color_wise && copy_stitch_length && $('#shelfIds').is(':checked'))
			{
				if (txtColor == txtColor_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#txt_shelf_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==3) 	$("#txt_shelf_to_"+k).val(data_value);
                    }
                }
			}
			else if(copy_lot_wise && copy_stitch_length && $('#shelfIds').is(':checked'))
			{
				if (txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#txt_shelf_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==3) 	$("#txt_shelf_to_"+k).val(data_value);
                    }
                }
			}
			else if(copy_color_wise && $('#shelfIds').is(':checked'))
			{
				if (txtColor == txtColor_check)
				{
					if ( $('#txt_shelf_to_'+k).prop('disabled')== false)
					{
						if(data[1]==3) 	$("#txt_shelf_to_"+k).val(data_value);
					}
				}
			}
			else if(copy_lot_wise && $('#shelfIds').is(':checked'))
			{
				if (txtLot == txtLot_check)
				{
					if ( $('#txt_shelf_to_'+k).prop('disabled')== false)
					{
						if(data[1]==3) 	$("#txt_shelf_to_"+k).val(data_value);
					}
				}
			}
			else if(copy_stitch_length && $('#shelfIds').is(':checked'))
			{
				if (txtStitchLength == txtStitchLength_check)
				{
					if ( $('#txt_shelf_to_'+k).prop('disabled')== false)
					{
						if(data[1]==3) 	$("#txt_shelf_to_"+k).val(data_value);
					}
				}
			}
			else if($('#rackIds').is(':checked'))
			{
				if ( $('#txt_shelf_to_'+k).prop('disabled')== false)
				{
					if(data[1]==3) 	$("#txt_shelf_to_"+k).val(data_value);
				}
			}
			// Bin
			if(copy_program_wise && copy_color_wise && copy_lot_wise && copy_stitch_length && $('#binIds').is(':checked'))
	        {
                if (txtProgram == txtProgram_check && txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#txt_bin_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==4) 	$("#txt_bin_to_"+k).val(data_value);
                    }
                }
	        }
	        else if(copy_program_wise && copy_color_wise && copy_lot_wise && $('#binIds').is(':checked'))
	        {
                if (txtProgram == txtProgram_check && txtColor == txtColor_check && txtLot == txtLot_check)
                {
                	if ( $('#txt_bin_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==4) 	$("#txt_bin_to_"+k).val(data_value);
                    }
                }
	        }
	        else if(copy_program_wise && copy_color_wise && copy_stitch_length && $('#binIds').is(':checked'))
	        {
                if (txtProgram == txtProgram_check && txtColor == txtColor_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#txt_bin_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==4) 	$("#txt_bin_to_"+k).val(data_value);
                    }
                }
	        }
	        else if(copy_program_wise && copy_color_wise && $('#binIds').is(':checked'))
	        {
                if (txtProgram == txtProgram_check && txtColor == txtColor_check)
                {
                	if ( $('#txt_bin_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==4) 	$("#txt_bin_to_"+k).val(data_value);
                    }
                }
	        }
	        else if(copy_program_wise && $('#binIds').is(':checked'))
	        {
                if (txtProgram == txtProgram_check)
                {
                	if ( $('#txt_bin_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==4) 	$("#txt_bin_to_"+k).val(data_value);
                    }
                }
	        }
			else if(copy_color_wise && copy_lot_wise && copy_stitch_length && $('#binIds').is(':checked'))
	        {
                if (txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#txt_bin_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==4) 	$("#txt_bin_to_"+k).val(data_value);
                    }
                }
	        }
			else if(copy_color_wise && copy_lot_wise && $('#binIds').is(':checked'))
	        {
                if (txtColor == txtColor_check && txtLot == txtLot_check)
                {
                	if ( $('#txt_bin_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==4) 	$("#txt_bin_to_"+k).val(data_value);
                    }
                }
	        }
			else if(copy_color_wise && copy_stitch_length && $('#binIds').is(':checked'))
			{
				if (txtColor == txtColor_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#txt_bin_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==4) 	$("#txt_bin_to_"+k).val(data_value);
                    }
                }
			}
			else if(copy_lot_wise && copy_stitch_length && $('#binIds').is(':checked'))
			{
				if (txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#txt_bin_to_'+k).prop('disabled')== false)
					{
                    	if(data[1]==4) 	$("#txt_bin_to_"+k).val(data_value);
                    }
                }
			}
			if(copy_color_wise && $('#binIds').is(':checked'))
			{
				if (txtColor == txtColor_check)
				{
					if ( $('#txt_bin_to_'+k).prop('disabled')== false)
					{
						if(data[1]==4) 	$("#txt_bin_to_"+k).val(data_value);
					}
				}
			}
			else if(copy_lot_wise && $('#binIds').is(':checked'))
			{
				if (txtLot == txtLot_check)
				{
					if ( $('#txt_bin_to_'+k).prop('disabled')== false)
					{
						if(data[1]==4) 	$("#txt_bin_to_"+k).val(data_value);
					}
				}
			}
			else if(copy_stitch_length && $('#binIds').is(':checked'))
			{
				if (txtStitchLength == txtStitchLength_check)
				{
					if ( $('#txt_bin_to_'+k).prop('disabled')== false)
					{
						if(data[1]==4) 	$("#txt_bin_to_"+k).val(data_value);
					}
				}
			}
			else if($('#binIds').is(':checked'))
			{
				if ( $('#txt_bin_to_'+k).prop('disabled')== false)
				{
					if(data[1]==4) 	$("#txt_bin_to_"+k).val(data_value);
				}
			}
		}
	}

	function reset_room_rack_shelf(id,fieldName)
	{
		// return;
		var numRow=$('#table_body tbody tr').length-1;

		var copy_program_wise=$("#copy_program_wise").is(":checked");
		var copy_color_wise=$("#copy_color_wise").is(":checked");
		var copy_lot_wise=$("#copy_lot_wise").is(":checked");
		var copy_stitch_length=$("#copy_stitch_length").is(":checked");

		var txtProgram = document.getElementById('hideBooking_'+id).value;
		var txtColor = document.getElementById('hiddenColor_'+id).value;
		var txtLot = document.getElementById('hidden_yeanlot_'+id).value;
		var txtStitchLength = document.getElementById('hidden_stitchLength_'+id).value;

		if (fieldName=="cbo_store_name")
		{
			for (var i = 1;numRow>=i; i++)
			{
				$("#cbo_floor_to_"+i).val(0);
				$("#cbo_room_to_"+i).val(0);
				$("#txt_rack_to_"+i).val(0);
				$("#txt_shelf_to_"+i).val(0);
				$("#txt_bin_to_"+i).val(0);
			}
		}
		else if (fieldName=="cbo_floor_to")
		{
			if(copy_program_wise && copy_color_wise && copy_lot_wise && copy_stitch_length && $('#floorIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtProgram_check = $("#hideBooking_"+i).val();
					var txtColor_check = $("#hiddenColor_"+i).val();
					var txtLot_check = $("#hidden_yeanlot_"+i).val();
					var txtStitchLength_check = $("#hidden_stitchLength_"+i).val();

	                if (txtProgram == txtProgram_check && txtColor==txtColor_check && txtLot==txtLot_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#cbo_floor_to_'+i).prop('disabled')== false)
						{
		                	$("#cbo_room_to_"+i).val(0);
							$("#txt_rack_to_"+i).val(0);
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_program_wise && copy_color_wise && copy_lot_wise && $('#floorIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtProgram_check = $("#hideBooking_"+i).val();
					var txtColor_check = $("#hiddenColor_"+i).val();
					var txtLot_check = $("#hidden_yeanlot_"+i).val();

	                if (txtProgram == txtProgram_check && txtColor==txtColor_check && txtLot==txtLot_check)
	                {
	                	if ( $('#cbo_floor_to_'+i).prop('disabled')== false)
						{
		                	$("#cbo_room_to_"+i).val(0);
							$("#txt_rack_to_"+i).val(0);
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_program_wise && copy_color_wise && copy_stitch_length && $('#floorIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtProgram_check = $("#hideBooking_"+i).val();
					var txtColor_check = $("#hiddenColor_"+i).val();
					var txtStitchLength_check = $("#hidden_stitchLength_"+i).val();

	                if (txtProgram == txtProgram_check && txtColor==txtColor_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#cbo_floor_to_'+i).prop('disabled')== false)
						{
		                	$("#cbo_room_to_"+i).val(0);
							$("#txt_rack_to_"+i).val(0);
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_program_wise && copy_color_wise && $('#floorIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtProgram_check = $("#hideBooking_"+i).val();
					var txtColor_check = $("#hiddenColor_"+i).val();

	                if (txtProgram == txtProgram_check && txtColor==txtColor_check)
	                {
	                	if ( $('#cbo_floor_to_'+i).prop('disabled')== false)
						{
		                	$("#cbo_room_to_"+i).val(0);
							$("#txt_rack_to_"+i).val(0);
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_program_wise && $('#floorIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtProgram_check = $("#hideBooking_"+i).val();
	                if (txtProgram == txtProgram_check)
	                {
	                	if ( $('#cbo_floor_to_'+i).prop('disabled')== false)
						{
		                	$("#cbo_room_to_"+i).val(0);
							$("#txt_rack_to_"+i).val(0);
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_color_wise && copy_lot_wise && copy_stitch_length && $('#floorIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtColor_check = $("#hiddenColor_"+i).val();
					var txtLot_check = $("#hidden_yeanlot_"+i).val();
					var txtStitchLength_check = $("#hidden_stitchLength_"+i).val();

	                if (txtColor==txtColor_check && txtLot==txtLot_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#cbo_floor_to_'+i).prop('disabled')== false)
						{
		                	$("#cbo_room_to_"+i).val(0);
							$("#txt_rack_to_"+i).val(0);
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_color_wise && copy_lot_wise && $('#floorIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtColor_check = $("#hiddenColor_"+i).val();
					var txtLot_check = $("#hidden_yeanlot_"+i).val();
	                if (txtColor == txtColor_check && txtLot == txtLot_check)
	                {
	                	if ( $('#cbo_floor_to_'+i).prop('disabled')== false)
						{
		                	$("#cbo_room_to_"+i).val(0);
							$("#txt_rack_to_"+i).val(0);
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_color_wise && copy_stitch_length && $('#floorIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtColor_check = $("#hiddenColor_"+i).val();
					var txtStitchLength_check = $("#hidden_stitchLength_"+i).val();
	                if (txtColor==txtColor_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#cbo_floor_to_'+i).prop('disabled')== false)
						{
		                	$("#cbo_room_to_"+i).val(0);
							$("#txt_rack_to_"+i).val(0);
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_lot_wise && copy_stitch_length && $('#floorIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtLot_check = $("#hidden_yeanlot_"+i).val();
					var txtStitchLength_check = $("#hidden_stitchLength_"+i).val();
	                if (txtLot==txtLot_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#cbo_floor_to_'+i).prop('disabled')== false)
						{
		                	$("#cbo_room_to_"+i).val(0);
							$("#txt_rack_to_"+i).val(0);
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_color_wise && $('#floorIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					// var txtColor_check = document.getElementById('hiddenColor_' +i).value;
					var txtColor_check = $("#hiddenColor_"+i).val();
					// alert(i+'='+txtColor +"=="+ txtColor_check);
	                if (txtColor == txtColor_check)
	                {
	                	if ( $('#cbo_floor_to_'+i).prop('disabled')== false)
						{
		                	$("#cbo_room_to_"+i).val(0);
							$("#txt_rack_to_"+i).val(0);
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_lot_wise && $('#floorIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtLot_check = $("#hidden_yeanlot_"+i).val();
					// alert(i+'='+txtColor +"=="+ txtColor_check);
	                if (txtLot == txtLot_check)
	                {
	                	if ( $('#cbo_floor_to_'+i).prop('disabled')== false)
						{
		                	$("#cbo_room_to_"+i).val(0);
							$("#txt_rack_to_"+i).val(0);
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_stitch_length && $('#floorIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtStitchLength_check = $("#hidden_stitchLength_"+i).val();
	                if (txtStitchLength == txtStitchLength_check)
	                {
	                	if ( $('#cbo_floor_to_'+i).prop('disabled')== false)
						{
		                	$("#cbo_room_to_"+i).val(0);
							$("#txt_rack_to_"+i).val(0);
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
	                }
				}
			}
			else if($('#floorIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					if ( $('#cbo_floor_to_'+i).prop('disabled')== false)
					{
						$("#cbo_room_to_"+i).val(0);
						$("#txt_rack_to_"+i).val(0);
						$("#txt_shelf_to_"+i).val(0);
						$("#txt_bin_to_"+i).val(0);
					}
				}
			}
			else
			{
				$("#cbo_room_to_"+id).val(0);
				$("#txt_rack_to_"+id).val(0);
				$("#txt_shelf_to_"+id).val(0);
				$("#txt_bin_to_"+id).val(0);
			}
		}
		else if (fieldName=="cbo_room_to")
		{
			if(copy_program_wise && copy_color_wise && copy_lot_wise && copy_stitch_length && $('#roomIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtProgram_check = $("#hideBooking_"+i).val();
					var txtColor_check = $("#hiddenColor_"+i).val();
					var txtLot_check = $("#hidden_yeanlot_"+i).val();
					var txtStitchLength_check = $("#hidden_stitchLength_"+i).val();

	                if (txtProgram == txtProgram_check && txtColor==txtColor_check && txtLot==txtLot_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#cbo_room_to_'+i).prop('disabled')== false)
						{
		                	$("#txt_rack_to_"+i).val(0);
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_program_wise && copy_color_wise && copy_lot_wise && $('#roomIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtProgram_check = $("#hideBooking_"+i).val();
					var txtColor_check = $("#hiddenColor_"+i).val();
					var txtLot_check = $("#hidden_yeanlot_"+i).val();

	                if (txtProgram == txtProgram_check && txtColor==txtColor_check && txtLot==txtLot_check)
	                {
	                	if ( $('#cbo_room_to_'+i).prop('disabled')== false)
						{
		                	$("#txt_rack_to_"+i).val(0);
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_program_wise && copy_color_wise && copy_stitch_length && $('#roomIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtProgram_check = $("#hideBooking_"+i).val();
					var txtColor_check = $("#hiddenColor_"+i).val();
					var txtStitchLength_check = $("#hidden_stitchLength_"+i).val();

	                if (txtProgram == txtProgram_check && txtColor==txtColor_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#cbo_room_to_'+i).prop('disabled')== false)
						{
		                	$("#txt_rack_to_"+i).val(0);
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_program_wise && copy_color_wise && $('#roomIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtProgram_check = $("#hideBooking_"+i).val();
					var txtColor_check = $("#hiddenColor_"+i).val();

	                if (txtProgram == txtProgram_check && txtColor==txtColor_check)
	                {
	                	if ( $('#cbo_room_to_'+i).prop('disabled')== false)
						{
		                	$("#txt_rack_to_"+i).val(0);
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_program_wise && $('#roomIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtProgram_check = $("#hideBooking_"+i).val();

	                if (txtProgram == txtProgram_check)
	                {
	                	if ( $('#cbo_room_to_'+i).prop('disabled')== false)
						{
		                	$("#txt_rack_to_"+i).val(0);
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_color_wise && copy_lot_wise && copy_stitch_length && $('#roomIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtColor_check = $("#hiddenColor_"+i).val();
					var txtLot_check = $("#hidden_yeanlot_"+i).val();
					var txtStitchLength_check = $("#hidden_stitchLength_"+i).val();

	                if (txtColor==txtColor_check && txtLot==txtLot_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#cbo_room_to_'+i).prop('disabled')== false)
						{
		                	$("#txt_rack_to_"+i).val(0);
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_color_wise && copy_lot_wise && $('#roomIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtColor_check = $("#hiddenColor_"+i).val();
					var txtLot_check = $("#hidden_yeanlot_"+i).val();
	                if (txtColor == txtColor_check && txtLot == txtLot_check)
	                {
	                	if ( $('#cbo_room_to_'+i).prop('disabled')== false)
						{
		                	$("#txt_rack_to_"+i).val(0);
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_color_wise && copy_stitch_length && $('#roomIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtColor_check = $("#hiddenColor_"+i).val();
					var txtStitchLength_check = $("#hidden_stitchLength_"+i).val();
	                if (txtColor==txtColor_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#cbo_room_to_'+i).prop('disabled')== false)
						{
		                	$("#txt_rack_to_"+i).val(0);
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_lot_wise && copy_stitch_length && $('#roomIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtLot_check = $("#hidden_yeanlot_"+i).val();
					var txtStitchLength_check = $("#hidden_stitchLength_"+i).val();
	                if (txtLot==txtLot_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#cbo_room_to_'+i).prop('disabled')== false)
						{
		                	$("#txt_rack_to_"+i).val(0);
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_color_wise && $('#roomIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtColor_check = $("#hiddenColor_"+i).val();
	                if (txtColor == txtColor_check)
					{
						if ( $('#cbo_room_to_'+i).prop('disabled')== false)
						{
							$("#txt_rack_to_"+i).val(0);
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
					}
				}
			}
			else if(copy_lot_wise && $('#roomIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtLot_check = $("#hidden_yeanlot_"+i).val();
	                if (txtLot == txtLot_check)
					{
						if ( $('#cbo_room_to_'+i).prop('disabled')== false)
						{
							$("#txt_rack_to_"+i).val(0);
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
					}
				}
			}
			else if(copy_stitch_length && $('#roomIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtStitchLength_check = $("#hidden_stitchLength_"+i).val();
	                if (txtStitchLength == txtStitchLength_check)
					{
						if ( $('#cbo_room_to_'+i).prop('disabled')== false)
						{
							$("#txt_rack_to_"+i).val(0);
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
					}
				}
			}
			else if($('#roomIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					if ( $('#cbo_room_to_'+i).prop('disabled')== false)
					{
						$("#txt_rack_to_"+i).val(0);
						$("#txt_shelf_to_"+i).val(0);
						$("#txt_bin_to_"+i).val(0);
					}
				}
			}
			else
			{
				$("#txt_rack_to_"+id).val(0);
				$("#txt_shelf_to_"+id).val(0);
				$("#txt_bin_to_"+id).val(0);
			}
		}
		else if (fieldName=="txt_rack_to")
		{
			if(copy_program_wise && copy_color_wise && copy_lot_wise && copy_stitch_length && $('#rackIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtProgram_check = $("#hideBooking_"+i).val();
					var txtColor_check = $("#hiddenColor_"+i).val();
					var txtLot_check = $("#hidden_yeanlot_"+i).val();
					var txtStitchLength_check = $("#hidden_stitchLength_"+i).val();
	                if (txtProgram == txtProgram_check && txtColor==txtColor_check && txtLot==txtLot_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#txt_rack_to_'+i).prop('disabled')== false)
						{
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
					}
				}
			}
			else if(copy_program_wise && copy_color_wise && copy_lot_wise && $('#rackIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtProgram_check = $("#hideBooking_"+i).val();
					var txtColor_check = $("#hiddenColor_"+i).val();
					var txtLot_check = $("#hidden_yeanlot_"+i).val();
	                if (txtProgram == txtProgram_check && txtColor==txtColor_check && txtLot==txtLot_check)
	                {
	                	if ( $('#txt_rack_to_'+i).prop('disabled')== false)
						{
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
					}
				}
			}
			else if(copy_program_wise && copy_color_wise && copy_stitch_length && $('#rackIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtProgram_check = $("#hideBooking_"+i).val();
					var txtColor_check = $("#hiddenColor_"+i).val();
					var txtStitchLength_check = $("#hidden_stitchLength_"+i).val();
	                if (txtProgram == txtProgram_check && txtColor==txtColor_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#txt_rack_to_'+i).prop('disabled')== false)
						{
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
					}
				}
			}
			else if(copy_program_wise && copy_color_wise && $('#rackIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtProgram_check = $("#hideBooking_"+i).val();
					var txtColor_check = $("#hiddenColor_"+i).val();
	                if (txtProgram == txtProgram_check && txtColor==txtColor_check)
	                {
	                	if ( $('#txt_rack_to_'+i).prop('disabled')== false)
						{
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
					}
				}
			}
			else if(copy_program_wise && $('#rackIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtProgram_check = $("#hideBooking_"+i).val();
	                if (txtProgram == txtProgram_check)
	                {
	                	if ( $('#txt_rack_to_'+i).prop('disabled')== false)
						{
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
					}
				}
			}
			else if(copy_color_wise && copy_lot_wise && copy_stitch_length && $('#rackIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtColor_check = $("#hiddenColor_"+i).val();
					var txtLot_check = $("#hidden_yeanlot_"+i).val();
					var txtStitchLength_check = $("#hidden_stitchLength_"+i).val();
	                if (txtColor==txtColor_check && txtLot==txtLot_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#txt_rack_to_'+i).prop('disabled')== false)
						{
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
					}
				}
			}
			else if(copy_color_wise && copy_lot_wise && $('#rackIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtColor_check = $("#hiddenColor_"+i).val();
					var txtLot_check = $("#hidden_yeanlot_"+i).val();
	                if (txtColor==txtColor_check && txtLot==txtLot_check)
	                {
	                	if ( $('#txt_rack_to_'+i).prop('disabled')== false)
						{
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
					}
				}
			}
			else if(copy_color_wise && copy_stitch_length && $('#rackIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtColor_check = $("#hiddenColor_"+i).val();
					var txtStitchLength_check = $("#hidden_stitchLength_"+i).val();
	                if (txtColor==txtColor_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#txt_rack_to_'+i).prop('disabled')== false)
						{
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
					}
				}
			}
			else if(copy_lot_wise && copy_stitch_length && $('#rackIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtLot_check = $("#hidden_yeanlot_"+i).val();
					var txtStitchLength_check = $("#hidden_stitchLength_"+i).val();
	                if (txtLot==txtLot_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#txt_rack_to_'+i).prop('disabled')== false)
						{
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
					}
				}
			}
			else if(copy_color_wise && $('#rackIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtColor_check = $("#hiddenColor_"+i).val();
	                if (txtColor == txtColor_check)
	                {
	                	if ( $('#txt_rack_to_'+i).prop('disabled')== false)
						{
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
					}
				}
			}
			else if(copy_lot_wise && $('#rackIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtLot_check = $("#hidden_yeanlot_"+i).val();
	                if (txtLot == txtLot_check)
	                {
	                	if ( $('#txt_rack_to_'+i).prop('disabled')== false)
						{
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
					}
				}
			}
			else if(copy_stitch_length && $('#rackIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtStitchLength_check = $("#hidden_stitchLength_"+i).val();
	                if (txtStitchLength == txtStitchLength_check)
	                {
	                	if ( $('#txt_rack_to_'+i).prop('disabled')== false)
						{
							$("#txt_shelf_to_"+i).val(0);
							$("#txt_bin_to_"+i).val(0);
						}
					}
				}
			}
			else if($('#rackIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					if ( $('#txt_rack_to_'+i).prop('disabled')== false)
					{
						$("#txt_shelf_to_"+id).val(0);
						$("#txt_bin_to_"+id).val(0);
					}
				}
			}
			else
			{
				$("#txt_shelf_to_"+id).val(0);
				$("#txt_bin_to_"+id).val(0);
			}
		}
		else if (fieldName=="txt_shelf_to")
		{
			if(copy_program_wise && copy_color_wise && copy_lot_wise && copy_stitch_length && $('#shelfIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtProgram_check = $("#hideBooking_"+i).val();
					var txtColor_check = $("#hiddenColor_"+i).val();
					var txtLot_check = $("#hidden_yeanlot_"+i).val();
					var txtStitchLength_check = $("#hidden_stitchLength_"+i).val();
	                if (txtProgram == txtProgram_check && txtColor==txtColor_check && txtLot==txtLot_check && txtStitchLength==txtStitchLength_check)
					{
						if ( $('#txt_shelf_to_'+i).prop('disabled')== false)
						{
							$("#txt_bin_to_"+i).val(0);
						}
					}
				}
			}
			else if(copy_program_wise && copy_color_wise && copy_lot_wise && $('#shelfIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtProgram_check = $("#hideBooking_"+i).val();
					var txtColor_check = $("#hiddenColor_"+i).val();
					var txtLot_check = $("#hidden_yeanlot_"+i).val();
	                if (txtProgram == txtProgram_check && txtColor==txtColor_check && txtLot==txtLot_check)
					{
						if ( $('#txt_shelf_to_'+i).prop('disabled')== false)
						{
							$("#txt_bin_to_"+i).val(0);
						}
					}
				}
			}
			else if(copy_program_wise && copy_color_wise && copy_stitch_length && $('#shelfIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtProgram_check = $("#hideBooking_"+i).val();
					var txtColor_check = $("#hiddenColor_"+i).val();
					var txtStitchLength_check = $("#hidden_stitchLength_"+i).val();
	                if (txtProgram == txtProgram_check && txtColor==txtColor_check && txtStitchLength==txtStitchLength_check)
					{
						if ( $('#txt_shelf_to_'+i).prop('disabled')== false)
						{
							$("#txt_bin_to_"+i).val(0);
						}
					}
				}
			}
			else if(copy_program_wise && copy_color_wise && $('#shelfIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtProgram_check = $("#hideBooking_"+i).val();
					var txtColor_check = $("#hiddenColor_"+i).val();
	                if (txtProgram == txtProgram_check && txtColor==txtColor_check)
					{
						if ( $('#txt_shelf_to_'+i).prop('disabled')== false)
						{
							$("#txt_bin_to_"+i).val(0);
						}
					}
				}
			}
			else if(copy_program_wise && $('#shelfIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtProgram_check = $("#hideBooking_"+i).val();
	                if (txtProgram == txtProgram_check)
					{
						if ( $('#txt_shelf_to_'+i).prop('disabled')== false)
						{
							$("#txt_bin_to_"+i).val(0);
						}
					}
				}
			}
			else if(copy_color_wise && copy_lot_wise && copy_stitch_length && $('#shelfIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtColor_check = $("#hiddenColor_"+i).val();
					var txtLot_check = $("#hidden_yeanlot_"+i).val();
					var txtStitchLength_check = $("#hidden_stitchLength_"+i).val();
	                if (txtColor==txtColor_check && txtLot==txtLot_check && txtStitchLength==txtStitchLength_check)
					{
						if ( $('#txt_shelf_to_'+i).prop('disabled')== false)
						{
							$("#txt_bin_to_"+i).val(0);
						}
					}
				}
			}
			else if(copy_color_wise && copy_lot_wise && $('#shelfIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtColor_check = $("#hiddenColor_"+i).val();
					var txtLot_check = $("#hidden_yeanlot_"+i).val();
	                if (txtColor==txtColor_check && txtLot==txtLot_check)
					{
						if ( $('#txt_shelf_to_'+i).prop('disabled')== false)
						{
							$("#txt_bin_to_"+i).val(0);
						}
					}
				}
			}
			else if(copy_color_wise && copy_stitch_length && $('#shelfIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtColor_check = $("#hiddenColor_"+i).val();
					var txtStitchLength_check = $("#hidden_stitchLength_"+i).val();
	                if (txtColor==txtColor_check && txtStitchLength==txtStitchLength_check)
					{
						if ( $('#txt_shelf_to_'+i).prop('disabled')== false)
						{
							$("#txt_bin_to_"+i).val(0);
						}
					}
				}
			}
			else if(copy_lot_wise && copy_stitch_length && $('#shelfIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtLot_check = $("#hidden_yeanlot_"+i).val();
					var txtStitchLength_check = $("#hidden_stitchLength_"+i).val();
	                if (txtLot==txtLot_check && txtStitchLength==txtStitchLength_check)
					{
						if ( $('#txt_shelf_to_'+i).prop('disabled')== false)
						{
							$("#txt_bin_to_"+i).val(0);
						}
					}
				}
			}
			else if(copy_color_wise && $('#shelfIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtColor_check = $("#hiddenColor_"+i).val();
	                if (txtColor == txtColor_check)
					{
						if ( $('#txt_shelf_to_'+i).prop('disabled')== false)
						{
							$("#txt_bin_to_"+i).val(0);
						}
					}
				}
			}
			else if(copy_lot_wise && $('#shelfIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtLot_check = $("#hidden_yeanlot_"+i).val();
	                if (txtLot == txtLot_check)
					{
						if ( $('#txt_shelf_to_'+i).prop('disabled')== false)
						{
							$("#txt_bin_to_"+i).val(0);
						}
					}
				}
			}
			else if(copy_stitch_length && $('#shelfIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					var txtStitchLength_check = $("#hidden_stitchLength_"+i).val();
	                if (txtStitchLength == txtStitchLength_check)
					{
						if ( $('#txt_shelf_to_'+i).prop('disabled')== false)
						{
							$("#txt_bin_to_"+i).val(0);
						}
					}
				}
			}
			else if($('#shelfIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++)
				{
					if ( $('#txt_shelf_to_'+i).prop('disabled')== false)
					{
						$("#txt_bin_to_"+id).val(0);
					}
				}
			}
			else
			{
				$("#txt_bin_to_"+id).val(0);
			}
		}
	}
	// ================================== floor_room_rack_shelf section End ===================================


	function check_all()
	{
		if($('#txt_check_all').attr('checked'))
		{
			$('#table_body tbody').find('tr').each(function(index, element) {
				$('input:checkbox:not(:disabled)').attr('checked','checked');
				$("#copy_color_wise").prop("checked", false);
				$("#copy_lot_wise").prop("checked", false);
	            $("#copy_stitch_length").prop("checked", false);
            });
		}
		else
		{
			$('#table_body tbody').find('tr').each(function(index, element) {
				$('input:checkbox:not(:disabled)').removeAttr('checked');
            });
		}
	}

	function fnc_copy_process(id) // not in use crm 13597
	{
	    /*if(id=="copy_color_wise")
	    {
	        var copy_color_wise=$("#copy_color_wise").is(":checked");
	        if(copy_color_wise)
	        {
	            $("#copy_lot_wise").prop("checked", false);
	            $("#copy_stitch_length").prop("checked", false);
	        }
	    }

	    if(id=="copy_lot_wise")
	    {
	        var copy_lot_wise=$("#copy_lot_wise").is(":checked");
	        if(copy_lot_wise)
	        {
	            $("#copy_color_wise").prop("checked", false);
				$("#copy_stitch_length").prop("checked", false);
	        }
	    }

		if(id=="copy_stitch_length")
	    {
	        var copy_stitch_length=$("#copy_stitch_length").is(":checked");
	        if(copy_stitch_length)
	        {
	            $("#copy_lot_wise").prop("checked", false);
				$("#copy_color_wise").prop("checked", false);
	        }
	    }*/
	}

</script>
<body onLoad="set_hotkey();focace_change()">
	<div align="center" style="width:100%;">
		<? echo load_freeze_divs ("../../",$permission); ?>
		<form name="greyreceive_1" id="greyreceive_1" class="appForm">
			<fieldset style="width:850px">
				<legend>Knitting Production Entry</legend>
				<table cellpadding="0" cellspacing="2" width="100%">
					<tr>
						<td colspan="2"></td>
                        <td align="right"><strong> Received ID </strong></td>
						<td>
							<input type="hidden" name="update_id" id="update_id" />
							<input type="hidden" name="hidden_delevery_scan" id="hidden_delevery_scan" />
							<input type="hidden" name="hidden_prev_barcode" id="hidden_prev_barcode"/>
							<input type="hidden" name="hidden_prev_dtls_id" id="hidden_prev_dtls_id"/>
							<input type="hidden" name="hidden_prev_trans_id" id="hidden_prev_trans_id"/>
							<input type="hidden" name="hidden_prev_prod_id" id="hidden_prev_prod_id"/>
							<input type="hidden" name="challan_discurd" id="challan_discurd"/>
							<input type="text" name="txt_recieved_id" id="txt_recieved_id" class="text_boxes" style="width:140px" placeholder="Double Click" onDblClick="grey_receive_popup();" readonly>
						</td>
                        <td colspan="2"></td>
					</tr>
					<!--<tr><td  align="left" colspan="6"></td> </tr>-->
					<tr>
						<td width="110" align="right">Scan Challan No</td>              <!-- 11-00030  -->
						<td width="150"><input type="text" name="txt_challan_scan" id="txt_challan_scan" class="text_boxes" style="width:140px" placeholder="Browse or Scan/Write" onDblClick="challan_no_popup()"   /></td>
						<td width="110" align="right">Receive Challan </td>              <!-- 11-00030  -->
						<td width="150"><input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:140px" placeholder="" onDblClick="" disabled/></td>
						<td width="110" align="right" class="must_entry_caption"> Company </td>
						<td width="150">
							<input type="hidden" name="cbo_company_id" id="cbo_company_id" />
							<input type="text" name="txt_company_name" id="txt_company_name" class="text_boxes" style="width:140px" placeholder="Display" disabled/>
                            <input type="hidden" name="store_update_upto" id="store_update_upto" readonly>
						</td>
					</tr>
					<tr>
						<td align="right" class="must_entry_caption"> Receive Date </td>
						<td>
							<input class="datepicker" type="text" style="width:140px" name="txt_receive_date" id="txt_receive_date" value="<? echo date("d-m-Y"); ?>" />
							<input type="hidden" id="hidden_delivery_id" name="hidden_delivery_id">
						</td>
                        <td align="right" class="must_entry_caption">Location </td>
						<td id="location_td" >
							<?
							echo create_drop_down( "cbo_location_name", 152, $blank_array,"", 1, "-- Select Location --", 1, "" );
							?>
						</td>
                        <td align="right" class="must_entry_caption"> Store Name </td>
						<td id="store_td">
							<?
							echo create_drop_down( "cbo_store_name", 152, $blank_array,"",1, "--Select store--", 1, "" );
							?>
						</td>
					</tr>

					<tr>
						<td align="right" class="must_entry_caption"> Knitting Source </td>
						<td>
							<?
							echo create_drop_down("cbo_knitting_source",150,$knitting_source,"", 1, "-- Select --", 0,"",1,'1,3');
							//load_drop_down( 'requires/grey_fabric_receive_roll_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_knitting_com','knitting_com');
							?>
						</td>
						<td align="right" class="must_entry_caption">Knitting Com</td>
						<td id="knitting_com">
							<input type="hidden" name="cbo_knitting_company" id="cbo_knitting_company" />
							<input type="text" name="txt_knitting_company" id="txt_knitting_company" class="text_boxes" style="width:140px" placeholder="Display"    disabled/>

						</td>
                        <td align="right">Knitting Location </td>
						<td>
							<input type="hidden" name="cbo_knitting_location_id" id="cbo_knitting_location_id" />
							<input type="text" name="txt_knitting_location_id" id="txt_knitting_location_id" class="text_boxes" style="width:140px" placeholder="Display"    disabled/>
						</td>
					</tr>
					<tr>
                        <td align="right">Challan No </td>
						<td>
							<input type="text" name="txt_receive_chal_no" id="txt_receive_chal_no" class="text_boxes" style="width:140px" >
						</td>
                        <td align="right">Yarn Issue Ch. No</td>
						<td>
							<input type="text" name="txt_yarn_issue_challan_no" id="txt_yarn_issue_challan_no" placeholder="Browse or Write" onDblClick="issue_challan_no();" class="text_boxes" style="width:140px" onBlur="fnc_check_issue(this.value);">
						</td>
						<td align="right"><b>No Bill</b></td>
						<td> <input type="checkbox" id="txt_no_bill" name="txt_no_bill" ></td>
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
						<td align="right">Remarks </td>
						<td>
							<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:140px;">
						</td>
                    </tr>

					<tr>
						<td colspan="6">
							<input type="hidden" name="is_posted_accout" id="is_posted_accout"/>
							<div id="accounting_posted_status" style=" color:red; font-size:24px;" align="left"></div>
						</td>
					</tr>

				</table>
				<br>
			</fieldset>
			<br>
			<fieldset style="width:1520px;text-align:left">
				<legend>
                    Roll Details
                    <span style="margin-left: 650px">
                    	Program wise <input type="checkbox" id="copy_program_wise" name="copy_program_wise" onClick="fnc_copy_process(this.id)"/>
                        Color wise <input type="checkbox" id="copy_color_wise" name="copy_color_wise" onClick="fnc_copy_process(this.id)"/>
						&nbsp;
                        Lot wise <input type="checkbox" id="copy_lot_wise" name="copy_lot_wise" onClick="fnc_copy_process(this.id)" />
                        &nbsp;
                        Stitch length wise <input type="checkbox" id="copy_stitch_length" name="copy_stitch_length" onClick="fnc_copy_process(this.id)"/>
					</span>
                </legend>
				<div id="recipe_items_list_view" style="margin-top:10px"> </div>
				<table width="1500" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
					<tr>
						<td align="center" class="button_container">
							<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">
							<?
							//echo load_submit_buttons($permission,"fnc_grey_fabric_receive",0,1,"reset_form('greyreceive_1','recipe_items_list_view*accounting_posted_status','','')",1);

							echo load_submit_buttons($permission,"fnc_grey_fabric_receive",0,0,"reset_form('greyreceive_1','recipe_items_list_view*accounting_posted_status','','','disable_enable_fields(\'txt_challan_scan*cbo_location_name*cbo_store_name\')')",1);

							//echo load_submit_buttons($permission, "fnc_grey_fabric_receive", 0,1,"reset_form('greyreceive_1','list_container_knitting*list_fabric_desc_container*roll_details_list_view*accounting_posted_status','','cbo_receive_basis,0','disable_enable_fields(\'cbo_company_id*cbo_receive_basis*cbo_knitting_source*cbo_knitting_company*cbo_location\');set_receive_basis();')",1);
							?>
							<input type="button" name="print1" id="print1" value="Print" class="formbutton_disabled" style=" width:100px;" onClick="fnc_grey_fabric_receive(4)" >
							<input type="button" name="print2" id="print2" value="Print 2" class="formbutton_disabled" style=" width:100px" onClick="fnc_grey_fabric_receive(5)" >
							<input type="button" name="print3" id="print3" value="Print 3" class="formbutton_disabled" style=" width:100px" onClick="fnc_grey_fabric_receive(6)" >
							<input type="button" name="print4" id="print4" value="Print 4" class="formbutton_disabled" style=" width:100px" onClick="fnc_grey_fabric_receive(7)" >
							<input type="button" name="print5" id="print5" value="Print 5" class="formbutton_disabled" style=" width:100px" onClick="fnc_grey_fabric_receive(8)" >
							<input type="button" name="print_mg" id="print_mg" value="Print MG" class="formbutton_disabled" style=" width:100px;" onClick="fnc_grey_fabric_receive(9)" >
							<input type="button" name="print_barcode" id="print_barcode" class="formbutton_disabled" value="Print Barcode" style=" width:100px" onClick="barcode_print();" >
							<input type="button" name="btn_fabric_details" id="btn_fabric_details" class="formbutton_disabled" value="Fabric Details" style=" width:100px" onClick="fabric_details();" >
						</td>
					</tr>
				</table>
			</fieldset>
		</form>
	</div>

</body>
<script>
	$(document).ready(function() {
		for (var property in mandatory_field_arr) {
	    	$("#"+mandatory_field_arr[property]).parent().prev('td').css("color", "blue");
		}
	});
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>