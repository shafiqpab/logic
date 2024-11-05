<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Grey Fabric Delivery Roll Wise
Functionality	:	
JS Functions	:
Created by		:	Fuad
Creation date 	: 	27-01-2015
Updated by 		: 	Zaman	
Update date		: 	22.10.2015	   
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
echo load_html_head_contents("Grey Fabric Delivery Roll Wise","../", 1, 1, $unicode,'',''); 
$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';
	var scanned_barcode=new Array();
<? 
	$company_name_array=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	
	$scanned_barcode_array=array(); $barcode_dtlsId_array=array(); $barcode_scanned_qnty_array=array();  
	$scanned_barcode_data=sql_select("select id, barcode_num, current_delivery from pro_grey_prod_delivery_dtls where entry_form=56 and status_active=1 and is_deleted=0");
	foreach($scanned_barcode_data as $row)
	{
		$scanned_barcode_array[]=$row[csf('barcode_num')];
		$barcode_dtlsId_array[$row[csf('barcode_num')]]=$row[csf('id')];
		$barcode_scanned_qnty_array[$row[csf('barcode_num')]]=number_format($row[csf('current_delivery')],2,'.','');
	}
	
	$jsscanned_barcode_array= json_encode($scanned_barcode_array);
	echo "scanned_barcode = ". $jsscanned_barcode_array . ";\n";
	
	$jsbarcode_dtlsId_array= json_encode($barcode_dtlsId_array);
	echo "var barcode_dtlsId_array = ". $jsbarcode_dtlsId_array . ";\n";
	
	$jsbarcode_scanned_qnty_array= json_encode($barcode_scanned_qnty_array);
	echo "var barcode_scanned_qnty_array = ". $jsbarcode_scanned_qnty_array . ";\n";
	
	$data_array=sql_select("SELECT a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst");
	$po_details_array=array();
	foreach($data_array as $row)
	{
		$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no_prefix_num")];
		$po_details_array[$row[csf("po_id")]]['buyer_name']=$buyer_name_array[$row[csf("buyer_name")]];
		$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
		$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
		$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
	}
	$jspo_details_array= json_encode($po_details_array);
	echo "var po_details_array = ". $jspo_details_array . ";\n";
	
	$composition_arr=array(); $constructtion_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
	$jsconstructtion_arr=json_encode($constructtion_arr);
	echo "var constructtion_arr = ". $jsconstructtion_arr . ";\n";
	
	$jscomposition_arr=json_encode($composition_arr);
	echo "var composition_arr = ". $jscomposition_arr . ";\n";
	
	$data_array=sql_select("select a.id, a.company_id, a.recv_number, a.receive_basis, a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, a.location_id, a.buyer_id, b.id as dtls_id, b.prod_id, b.febric_description_id, b.gsm, b.width, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.reject_qnty, c.booking_no as bwo, c.booking_without_order from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0");
	$roll_details_array=array(); $barcode_array=array(); 
	foreach($data_array as $row)
	{
		$roll_details_array[$row[csf("barcode_no")]]['mst_id']=$row[csf("id")];
		$roll_details_array[$row[csf("barcode_no")]]['company_id']=$row[csf("company_id")];
		$roll_details_array[$row[csf("barcode_no")]]['recv_number']=$row[csf("recv_number")];
		$roll_details_array[$row[csf("barcode_no")]]['receive_basis']=$receive_basis[$row[csf("receive_basis")]];
		$roll_details_array[$row[csf("barcode_no")]]['receive_date']=change_date_format($row[csf("receive_date")]);
		$roll_details_array[$row[csf("barcode_no")]]['booking_no']=$row[csf("booking_no")];
		$roll_details_array[$row[csf("barcode_no")]]['knitting_source_id']=$row[csf("knitting_source")];
		$roll_details_array[$row[csf("barcode_no")]]['knitting_source']=$knitting_source[$row[csf("knitting_source")]];
		$roll_details_array[$row[csf("barcode_no")]]['knitting_company_id']=$row[csf("knitting_company")];
		$roll_details_array[$row[csf("barcode_no")]]['location_id']=$row[csf("location_id")];
		$roll_details_array[$row[csf("barcode_no")]]['buyer_name']=$buyer_name_array[$row[csf("buyer_id")]];
		
		if($row[csf("knitting_source")]==1)
		{
			$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$company_name_array[$row[csf("knitting_company")]];
		}
		else if($row[csf("knitting_source")]==3)
		{
			$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$supplier_arr[$row[csf("knitting_company")]];
		}
		
		$roll_details_array[$row[csf("barcode_no")]]['dtls_id']=$row[csf("dtls_id")];
		$roll_details_array[$row[csf("barcode_no")]]['prod_id']=$row[csf("prod_id")];
		$roll_details_array[$row[csf("barcode_no")]]['deter_d']=$row[csf("febric_description_id")];
		$roll_details_array[$row[csf("barcode_no")]]['gsm']=$row[csf("gsm")];
		$roll_details_array[$row[csf("barcode_no")]]['width']=$row[csf("width")];
		$roll_details_array[$row[csf("barcode_no")]]['roll_id']=$row[csf("roll_id")];
		$roll_details_array[$row[csf("barcode_no")]]['roll_no']=$row[csf("roll_no")];
		$roll_details_array[$row[csf("barcode_no")]]['po_breakdown_id']=$row[csf("po_breakdown_id")];
		$roll_details_array[$row[csf("barcode_no")]]['qnty']=$row[csf("qnty")];
		$roll_details_array[$row[csf("barcode_no")]]['prodQnty']=number_format(($row[csf("qnty")]+$row[csf("reject_qnty")]),2,'.','');
		$roll_details_array[$row[csf("barcode_no")]]['bwo']=$row[csf("bwo")];
		$roll_details_array[$row[csf("barcode_no")]]['booking_without_order']=$row[csf("booking_without_order")];
		
		$barcode_array[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
	}
	$jsroll_details_array= json_encode($roll_details_array);
	echo "var roll_details_array = ". $jsroll_details_array . ";\n";
	
	$jsbarcode_array= json_encode($barcode_array);
	echo "var barcode_array = ". $jsbarcode_array . ";\n";
	
    $receive_barcode_array=array();
	$receive_barcode_data=sql_select("select barcode_no from pro_roll_details where entry_form=58 and status_active=1 and is_deleted=0");
	foreach($receive_barcode_data as $row)
	{
		$receive_barcode_array[]=$row[csf('barcode_no')];
	}
	$jsreceive_barcode_array= json_encode($receive_barcode_array);
	echo "var receive_barcode_array = ". $jsreceive_barcode_array . ";\n";

?>

	function openmypage_challan()
	{ 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/grey_feb_delivery_roll_wise_entry_controller.php?action=challan_popup','Challan Popup', 'width=780px,height=350px,center=1,resize=1,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var hidden_data=this.contentDoc.getElementById("hidden_data").value;	 //challan Id and Number
			var barcode_nos=this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode Nos
			
			if(barcode_nos!="")
			{
				fnc_reset_form();
				var challan_data=hidden_data.split("**");
				$('#update_id').val(challan_data[0]);
				$('#txt_challan_no').val(challan_data[1]);
				$('#txt_delivery_date').val(challan_data[2]);
				
				var barcode_upd=barcode_nos.split(",");
				for(var k=0; k<barcode_upd.length; k++)
				{
					create_row(1,barcode_upd[k]);
				}
				set_all_onclick();
				set_button_status(1, permission, 'fnc_grey_delivery_roll_wise',1);
			}
		}
	}
	
	function openmypage_barcode()
	{ 
		var company_id=$('#cbo_company_id').val();
		var location_id=$('#cbo_location_id').val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/grey_feb_delivery_roll_wise_entry_controller.php?company_id='+company_id+'&location_id='+location_id+'&action=barcode_popup','Barcode Popup', 'width=1085px,height=350px,center=1,resize=1,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var barcode_nos=this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode Nos
			
			if(barcode_nos!="")
			{
				var barcode_upd=barcode_nos.split(",");
				for(var k=0; k<barcode_upd.length; k++)
				{
					create_row(0,barcode_upd[k]);
				}
				set_all_onclick();
			}
		}
	}
	
	function generate_report_file(data,action)
	{
		window.open("requires/grey_feb_delivery_roll_wise_entry_controller.php?data=" + data+'&action='+action, true );
	}

	function fnc_grey_delivery_roll_wise( operation )
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_challan_no').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_knitting_source').val(),'grey_delivery_print');
			return;
		}
		
	 	if(form_validation('txt_delivery_date*cbo_company_id*cbo_location_id*cbo_knitting_source*txt_knit_company','Delivery Date*Company*Knitting Source*Knitting Company')==false)
		{
			return; 
		}
		
		remove_duplicate_row();
		var j=0; var dataString='';
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var currentDelivery=$(this).find('input[name="currentDelivery[]"]').val()*1;
			var productionId=$(this).find('input[name="productionId[]"]').val();
			var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
			var productionDtlsId=$(this).find('input[name="productionDtlsId[]"]').val();
			var deterId=$(this).find('input[name="deterId[]"]').val();
			var productId=$(this).find('input[name="productId[]"]').val();
			var orderId=$(this).find('input[name="orderId[]"]').val();
			var rollId=$(this).find('input[name="rollId[]"]').val();
			var dtlsId=$(this).find('input[name="dtlsId[]"]').val();
			var rollNo=$(this).find("td:eq(16)").text();
			var bookingWithoutOrder=$(this).find('input[name="bookingWithoutOrder[]"]').val();
			var smnBookingNo=$(this).find("td:eq(3)").text();
			
			try 
			{
				if(currentDelivery<0.1)
				{
					alert("Please Insert Roll Qty.");
					return;
				}
				
				j++;
				
				dataString+='&currentDelivery_' + j + '=' + currentDelivery + '&productionId_' + j + '=' + productionId + '&barcodeNo_' + j + '=' + barcodeNo + '&productionDtlsId_' + j + '=' + productionDtlsId + '&deterId_' + j + '=' + deterId + '&productId_' + j + '=' + productId + '&orderId_' + j + '=' + orderId + '&rollId_' + j + '=' + rollId + '&rollNo_' + j + '=' + rollNo + '&dtlsId_' + j + '=' + dtlsId + '&bookingWithoutOrder_' + j + '=' + bookingWithoutOrder + '&smnBookingNo_' + j + '=' + smnBookingNo;
			}
			catch(e) 
			{
				//got error no operation
			}
		});
		
		if(j<1)
		{
			alert('No data');
			return;
		}
		
		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_delivery_date*txt_challan_no*cbo_company_id*cbo_location_id*cbo_knitting_source*txt_knit_company*knit_company_id*update_id*txt_deleted_id*txt_deleted_roll_id',"../")+dataString;
		//alert(data);return;
		freeze_window(operation);
		
		http.open("POST","requires/grey_feb_delivery_roll_wise_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_grey_delivery_roll_wise_Reply_info;
	}

	function fnc_grey_delivery_roll_wise_Reply_info()
	{
		if(http.readyState == 4) 
		{
			//release_freezing();return;
			var response=trim(http.responseText).split('**');
			show_msg(response[0]);
			
			if((response[0]==0 || response[0]==1))
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_challan_no').value = response[2];
				$('#txt_deleted_id').val( '' );
				$('#txt_deleted_roll_id').val( '' );
				add_dtls_data( response[3]);
				set_button_status(1, permission, 'fnc_grey_delivery_roll_wise',1);
			}
			release_freezing();
		}
	}
	
	//var scanned_barcode=new Array();
	function create_row(is_update,barcode_no)
	{
		var row_num=$('#txt_tot_row').val();
		//var bar_code=$('#txt_bar_code_num').val();
		var bar_code=trim(barcode_no);
		var num_row =$('#scanning_tbl tbody tr').length; 
		
		if(is_update==0)
		{
			if(!barcode_array[bar_code])
			{ 	
				alert('Barcode is Not Valid');
				$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
				 {
					$('#messagebox_main', window.parent.document).html('Barcode is Not Valid.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
				 });
				 $('#txt_bar_code_num').val('');
				return; 
			}
			
			if( jQuery.inArray( bar_code, scanned_barcode )>-1) 
			{ 
				alert('Sorry! Barcode Already Scanned.'); 
				$('#txt_bar_code_num').val('');
				return; 
			}
		}
			
		var bar_code_no=$('#barcodeNo_'+row_num).val();
		if(bar_code_no=="")
		{
			$('#cbo_company_id').val(roll_details_array[bar_code]['company_id']);
			$('#cbo_knitting_source').val(roll_details_array[bar_code]['knitting_source_id']);
			$('#txt_knit_company').val(roll_details_array[bar_code]['knitting_company']);
			$('#knit_company_id').val(roll_details_array[bar_code]['knitting_company_id']);
			$('#cbo_location_id').val(roll_details_array[bar_code]['location_id']);
		}
		else
		{
			var company_id_prev=$('#cbo_company_id').val();
			var knitting_source_prev=$('#cbo_knitting_source').val();
			var knitting_company_prev=$('#knit_company_id').val();
			var location_id_prev=$('#cbo_location_id').val();
			
			var company_id=roll_details_array[bar_code]['company_id'];
			var knitting_source=roll_details_array[bar_code]['knitting_source_id'];
			var knitting_company=roll_details_array[bar_code]['knitting_company_id'];
			var location_id=roll_details_array[bar_code]['location_id'];
			
			if(company_id_prev!=company_id)
			{
				alert("Multiple Company Not Allowed");
				return;	
			}
			
			if(location_id_prev!=location_id)
			{
				alert("Multiple Location Not Allowed");
				return;	
			}
			
			if(knitting_source_prev!=knitting_source)
			{
				alert("Multiple Knitting Source Not Allowed");
				return;	
			}
			
			if(knitting_company_prev!=knitting_company)
			{
				alert("Multiple Knitting Company Not Allowed");
				return;	
			}
			
			row_num++;
			$("#scanning_tbl tbody tr:first").clone().find("td,input,select").each(function()
			{
				$(this).attr({ 
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
				  'value': function(_, value) { return value }              
				});
			}).end().prependTo("#scanning_tbl");
			
			$("#scanning_tbl tbody tr:first").removeAttr('id').attr('id','tr_'+row_num);
		}
		
		if(is_update==0)
		{
			scanned_barcode.push(bar_code);
		}

		var qty='';
		if(barcode_dtlsId_array[bar_code]*1>0)
		{
			qty=barcode_scanned_qnty_array[bar_code];
		}
		else
		{
			qty=roll_details_array[bar_code]['qnty'];
		}
		
		$("#sl_"+row_num).text(row_num);
		$("#barcode_"+row_num).text(barcode_array[bar_code]);
		$("#systemId_"+row_num).text(roll_details_array[bar_code]['recv_number']);
		$("#progBookId_"+row_num).text(roll_details_array[bar_code]['booking_no']);
		$("#basis_"+row_num).text(roll_details_array[bar_code]['receive_basis']);
		$("#knitSource_"+row_num).text(roll_details_array[bar_code]['knitting_source']);
		$("#prodDate_"+row_num).text(roll_details_array[bar_code]['receive_date']);
		$("#prodId_"+row_num).text(roll_details_array[bar_code]['prod_id']);
		
		if(roll_details_array[bar_code]['booking_without_order']==1)
		{
			$("#year_"+row_num).text('');
			$("#job_"+row_num).text('');
			$("#buyer_"+row_num).text(roll_details_array[bar_code]['buyer_name']);
			$("#order_"+row_num).text(roll_details_array[bar_code]['bwo']);
		}
		else
		{
			$("#year_"+row_num).text(po_details_array[roll_details_array[bar_code]['po_breakdown_id']]['year']);
			$("#job_"+row_num).text(po_details_array[roll_details_array[bar_code]['po_breakdown_id']]['job_no']);
			$("#buyer_"+row_num).text(po_details_array[roll_details_array[bar_code]['po_breakdown_id']]['buyer_name']);
			$("#order_"+row_num).text(po_details_array[roll_details_array[bar_code]['po_breakdown_id']]['po_number']);	
		}
		
		$("#cons_"+row_num).text(constructtion_arr[roll_details_array[bar_code]['deter_d']]);
		$("#comps_"+row_num).text(composition_arr[roll_details_array[bar_code]['deter_d']]);
		$("#gsm_"+row_num).text(roll_details_array[bar_code]['gsm']);
		$("#dia_"+row_num).text(roll_details_array[bar_code]['width']);
		$("#roll_"+row_num).text(roll_details_array[bar_code]['roll_no']);
		$("#prodQty_"+row_num).text(roll_details_array[bar_code]['prodQnty']);
		$("#currentDelivery_"+row_num).val(qty);
		
		if( jQuery.inArray( bar_code, receive_barcode_array )>-1) 
		{ 
			 $("#currentDelivery_"+row_num).attr('disabled',true);
			 $("#decrease_"+row_num).attr('disabled',true);
		}
		else
		{
			 $("#currentDelivery_"+row_num).attr('disabled',false);
			 $("#decrease_"+row_num).attr('disabled',false);
		}
		
		$("#barcodeNo_"+row_num).val(barcode_array[bar_code]);
		$("#productionId_"+row_num).val(roll_details_array[bar_code]['mst_id']);
		$("#productionDtlsId_"+row_num).val(roll_details_array[bar_code]['dtls_id']);
		$("#deterId_"+row_num).val(roll_details_array[bar_code]['deter_d']);
		$("#productId_"+row_num).val(roll_details_array[bar_code]['prod_id']);
		$("#orderId_"+row_num).val(roll_details_array[bar_code]['po_breakdown_id']);
		$("#rollId_"+row_num).val(roll_details_array[bar_code]['roll_id']);
		$("#dtlsId_"+row_num).val(barcode_dtlsId_array[bar_code]);
		$("#bookingWithoutOrder_"+row_num).val(roll_details_array[bar_code]['booking_without_order']);
		
		$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
		$('#currentDelivery_'+row_num).removeAttr("onKeyUp").attr("onKeyUp","check_qty("+row_num+");");
		
		$('#txt_tot_row').val(row_num);
		$('#txt_bar_code_num').val('');
		$('#txt_bar_code_num').focus();
	}
	
	$('#txt_bar_code_num').live('keydown', function(e) {
		if (e.keyCode === 13) 
		{
			e.preventDefault();
			var bar_code=$('#txt_bar_code_num').val();
			create_row(0,bar_code);
		}
	});
	
	function add_dtls_data( data )
	{
		var barcode_datas=data.split(",");
		for(var k=0; k<barcode_datas.length; k++)
		{
			var datas=barcode_datas[k].split("__");
			var barcode_no=datas[0];
			var dtls_id=datas[1];
			var qty=datas[2];
			
			barcode_dtlsId_array[barcode_no] = dtls_id;
			barcode_scanned_qnty_array[barcode_no] = qty;
			//barcode_dtlsId_array.push(bar_code);
		}
		
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
			var dtlsId=$(this).find('input[name="dtlsId[]"]').val();
			
			if(dtlsId=="") 
			{
				$(this).find('input[name="dtlsId[]"]').val(barcode_dtlsId_array[barcodeNo]);	
			}
		});
		
		/*var objJSON={
			  "student":[{
				"id": 456,
				"full_name": "GOOBER, ANGELA",
				"user_id": "2733245678",
				"stin": "2733212346"
				},{
				"id": 123,
				"full_name": "BOB, STEVE",
				"user_id": "abc213",
				"stin": "9040923411"
			  }]
			}
			for(var i=0; i<objJSON.student.length; i++) {
				alert(objJSON.student[i].id);
		  	}*/
	}
	
	function remove_duplicate_row()
	{
		var check_barcode_arr=new Array();
		var txt_deleted_id=$('#txt_deleted_id').val();
		var txt_deleted_roll_id=$('#txt_deleted_roll_id').val();
		var selected_id=''; var selected_id_roll='';
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
			var dtlsId=$(this).find('input[name="dtlsId[]"]').val();
			var rollId=$(this).find('input[name="rollId[]"]').val();
			
			if( jQuery.inArray( barcodeNo, check_barcode_arr )>-1) 
			{ 
				if(dtlsId!='')
				{
					if(selected_id=='') selected_id=dtlsId; else selected_id=selected_id+','+dtlsId;
					if(selected_id_roll=='') selected_id_roll=rollId; else selected_id_roll=selected_id_roll+','+rollId;
				}
				
				$(this).remove();
			}
			else
			{
				check_barcode_arr.push(barcodeNo);
			}
		});
		
		if(selected_id!='')
		{
			if(txt_deleted_id=='') txt_deleted_id=selected_id; else txt_deleted_id=txt_deleted_id+','+selected_id;
			$('#txt_deleted_id').val( txt_deleted_id );
			
			if(txt_deleted_roll_id=='') txt_deleted_roll_id=selected_id_roll; else txt_deleted_roll_id=txt_deleted_roll_id+','+selected_id_roll;
			$('#txt_deleted_roll_id').val( txt_deleted_roll_id );
		}
	}
	
	function fn_deleteRow( rid )
	{
		var num_row =$('#scanning_tbl tbody tr').length;
		var dtlsId =$("#dtlsId_"+rid).val();
		var rollId =$("#rollId_"+rid).val();
		var txt_deleted_id=$('#txt_deleted_id').val();
		var txt_deleted_roll_id=$('#txt_deleted_roll_id').val();
		var bar_code =$("#barcodeNo_"+rid).val();
		
		if(num_row==1)
		{
			$('#tr_'+rid+' td:not(:nth-last-child(2)):not(:last-child)').each(function(index, element) {
				$(this).html('');
			});
			
			$('#tr_'+rid).find(":input:not(:button)").val('');
			$('#cbo_company_id').val(0);
			$('#cbo_location_id').val(0);
			$('#cbo_knitting_source').val(0);
			$('#txt_knit_company').val('');
			$('#knit_company_id').val('');
		}
		else
		{
			$("#tr_"+rid).remove();
		}
		
		var selected_id=''; var selected_id_roll='';
		if(dtlsId!='')
		{
			if(txt_deleted_id=='') selected_id=dtlsId; else selected_id=txt_deleted_id+','+dtlsId;
			$('#txt_deleted_id').val( selected_id );
			
			if(txt_deleted_roll_id=='') selected_id_roll=rollId; else selected_id_roll=txt_deleted_roll_id+','+rollId;
			$('#txt_deleted_roll_id').val( selected_id_roll );
		}
		
		var index = scanned_barcode.indexOf(bar_code);
		scanned_barcode.splice(index,1);
	}
	
	function check_qty( rid )
	{
		var production_qty=$("#prodQty_"+rid).text()*1;
		var roll_delv_qty=$("#currentDelivery_"+rid).val()*1;
		if(roll_delv_qty>production_qty)
		{
			alert("Delivery Quantity Exceeds Production Quantity.");
			$("#currentDelivery_"+rid).val(production_qty.toFixed(2));
			return;
		}
	}
	
	function fnc_reset_form()
	{
		/*$('#scanning_tbl tbody tr:not(:last)').remove();
		var tr_id=$('#scanning_tbl tbody tr:last').attr('id');
		var tr_id=tr_id.split("_");
		var row_num=tr_id[1];
		
		$('#tr_'+row_num+' td:not(:nth-last-child(2)):not(:last-child)').each(function(index, element) {
			$(this).html('');
		});

		$('#tr_'+row_num).find(":input:not(:button)").val('');*/
		
		$('#scanning_tbl tbody tr').remove();
		
		var html='<tr id="tr_1" align="center" valign="middle"><td width="30" id="sl_1"></td><td width="80" id="barcode_1"></td><td width="100" id="systemId_1"></td><td width="85" id="progBookId_1"></td><td width="75" id="basis_1"></td><td width="75" id="knitSource_1"></td><td width="70" id="prodDate_1"></td><td width="50" id="prodId_1"></td><td width="40" id="year_1" align="center"></td><td width="50" id="job_1"></td><td width="55" id="buyer_1"></td><td width="80" id="order_1" style="word-break:break-all;" align="left"></td><td width="80" id="cons_1" style="word-break:break-all;" align="left"></td><td width="100" id="comps_1" style="word-break:break-all;" align="left"></td><td width="40" id="gsm_1"></td><td width="40" id="dia_1"></td><td width="40" id="roll_1"></td><td width="70" id="prodQty_1" align="right"></td><td id="delevQt_1" width="80" align="center"><input type="tex" name="currentDelivery[]" id="currentDelivery_1" style="width:65px" class="text_boxes_numeric" onKeyUp="check_qty(1)" readonly/></td><td id="button_1" align="center"><input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" /><input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/><input type="hidden" name="productionId[]" id="productionId_1"/><input type="hidden" name="productionDtlsId[]" id="productionDtlsId_1"/><input type="hidden" name="deterId[]" id="deterId_1"/><input type="hidden" name="productId[]" id="productId_1"/><input type="hidden" name="orderId[]" id="orderId_1"/><input type="hidden" name="rollId[]" id="rollId_1"/><input type="hidden" name="dtlsId[]" id="dtlsId_1"/><input type="hidden" name="bookingWithoutOrder[]" id="bookingWithoutOrder_1"/></td></tr>';
		
		$('#cbo_company_id').val(0);
		$('#cbo_knitting_source').val(0);
		$('#txt_knit_company').val('');
		$('#knit_company_id').val('');
		$('#cbo_location_id').val(0);
		$('#txt_tot_row').val(1);
		$('#update_id').val('');
		$('#txt_challan_no').val('');
		$('#txt_delivery_date').val('');
		$('#txt_deleted_id').val('');
		$('#txt_deleted_roll_id').val('');
		$("#scanning_tbl tbody").html(html);	
		
		set_button_status(0, permission, 'fnc_grey_delivery_roll_wise',1);
		
		/*$("#scanning_tbl tbody tr:last").removeAttr('id').attr('id','tr_'+1);*/
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
    <div align="center" style="width:100%;">
		<? echo load_freeze_divs ("../",$permission); ?>
        <form name="rollscanning_1" id="rollscanning_1"  autocomplete="off"  >
            <fieldset style="width:810px;">
				<legend>Roll Scanning</legend>
                <table cellpadding="0" cellspacing="2" width="800">
                    <tr>
                        <td align="right" class="must_entry_caption" width="100">Delivery Date</td>
                        <td width="160"><input type="text" name="txt_delivery_date" id="txt_delivery_date" class="datepicker" style="width:140px;" readonly /></td>
                        <td align="right" width="100">Challan No</td>
                        <td>
                        	<input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:140px;" onDblClick="openmypage_challan()" placeholder="Browse For Challan No" readonly/>
                            <input type="hidden" name="update_id" id="update_id"/>
                        </td>
                        <td align="right">Location</td>
                        <td>
                            <? 
                                echo create_drop_down( "cbo_location_id", 152, "select id, location_name from lib_location","id,location_name", 1, "--Display--", 0, "",1 );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Company</td>
                        <td>
                            <? 
                                echo create_drop_down( "cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0","id,company_name", 1, "--Display--", 0, "",1 );//$company_cond 
                            ?>
                        </td>
                        <td align="right">Knitting Source </td>
                        <td>
							<? 
								echo create_drop_down("cbo_knitting_source",152,$knitting_source,"", 1, "-- Display --", 0,"",1); 
							?>
                        </td>
                        <td align="right">Knitting Company</td>
                        <td id="knitting_com">
                            <input type="text" name="txt_knit_company" id="txt_knit_company" class="text_boxes" style="width:140px;" placeholder="Display" disabled/>
                            <input type="hidden" name="knit_company_id" id="knit_company_id"/>
                        </td>
                    </tr>
                    <tr>
                    	<td height="5" colspan="6"></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="6"><strong>Barcode Number</strong>&nbsp;&nbsp;
                            <input type="text" name="txt_bar_code_num" id="txt_bar_code_num" class="text_boxes_numeric" style="width:170px;" onDblClick="openmypage_barcode()" placeholder="Browse/Write/Scan"/>
                        </td>
                    </tr>
                </table>
			</fieldset>
			<br>
			<fieldset style="width:1320px;text-align:left">
				<style>
                    #scanning_tbl tr td
                    {
                        background-color:#FFF;
                        color:#000;
                        border: 1px solid #666666;
                        line-height:12px;
                        height:20px;
                        overflow:auto;
                    }
                </style>
				<table cellpadding="0" width="1300" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <thead>
                    	<th width="30">SL</th>
                        <th width="80">Barcode No</th>
                        <th width="100">System Id</th>
                        <th width="85">Booking/ Programm No</th>
                        <th width="75">Production Basis</th>
                        <th width="75">Knitting Source</th>
                        <th width="70">Production date</th>
                        <th width="50">Product Id</th>
                        <th width="40">Year</th>
                        <th width="50">Job No</th>
                        <th width="55">Buyer</th>
                        <th width="80">Order No</th>
                        <th width="80">Construction</th>
                        <th width="100">Composition</th>
                        <th width="40">GSM</th>
                        <th width="40">Dia</th>
                        <th width="40">Roll No</th>
                        <th width="70">Production Qty.</th>
                        <th width="80">QC Pass Qty.</th>
                        <th></th>
                    </thead>
                 </table>
                 <div style="width:1320px; max-height:250px; overflow-y:scroll" align="left">
                 	<table cellpadding="0" cellspacing="0" width="1300" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                    	<tbody>
                        	<tr id="tr_1" align="center" valign="middle">
                                <td width="30" id="sl_1"></td>
                                <td width="80" id="barcode_1"></td>
                                <td width="100" id="systemId_1"></td>
                                <td width="85" id="progBookId_1"></td>
                                <td width="75" id="basis_1"></td>
                                <td width="75" id="knitSource_1"></td>
                                <td width="70" id="prodDate_1"></td>
                                <td width="50" id="prodId_1"></td>
                                <td width="40" id="year_1" align="center"></td>
                                <td width="50" id="job_1"></td>
                                <td width="55" id="buyer_1"></td>
                                <td width="80" id="order_1" style="word-break:break-all;" align="left"></td>
                                <td width="80" id="cons_1" style="word-break:break-all;" align="left"></td>
                                <td width="100" id="comps_1" style="word-break:break-all;" align="left"></td>
                                <td width="40" id="gsm_1"></td>
                                <td width="40" id="dia_1"></td>
                                <td width="40" id="roll_1"></td>
                                <td width="70" id="prodQty_1" align="right"></td>
                                <td id="delevQt_1" width="80" align="center"><input type="text" name="currentDelivery[]" id="currentDelivery_1" style="width:65px" class="text_boxes_numeric" onKeyUp="check_qty(1)" readonly/></td>
                                <td id="button_1" align="center">
                                    <input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
                                    <input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/>
                                    <input type="hidden" name="productionId[]" id="productionId_1"/>
                                    <input type="hidden" name="productionDtlsId[]" id="productionDtlsId_1"/>
                                    <input type="hidden" name="deterId[]" id="deterId_1"/>
                                    <input type="hidden" name="productId[]" id="productId_1"/>
                                    <input type="hidden" name="orderId[]" id="orderId_1"/>
                                    <input type="hidden" name="rollId[]" id="rollId_1"/>
                                    <input type="hidden" name="dtlsId[]" id="dtlsId_1"/>
                                    <input type="hidden" name="bookingWithoutOrder[]" id="bookingWithoutOrder_1"/>
                                </td>
                            </tr>
                        </tbody>
                	</table>
                </div>
                <br>
                <table width="1320" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
                    <tr>
                        <td align="center" class="button_container">
                        	<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">
                            <input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes" value="">
                            <input type="hidden" name="txt_deleted_roll_id" id="txt_deleted_roll_id" class="text_boxes" value="">
                            <? 
                            	echo load_submit_buttons($permission,"fnc_grey_delivery_roll_wise",0,1,"fnc_reset_form()",1);
                            ?>
                        </td>
                    </tr>  
                </table>
			</fieldset>
        </form>	 
    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>