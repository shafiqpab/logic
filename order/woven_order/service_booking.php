<?
/*-------------------------------------------- Comments
Version          : V1
Purpose			 : This form will create Service Booking
Functionality	 :
JS Functions	 :
Created by		 : MONZU
Creation date 	 : 27-12-2012
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
echo load_html_head_contents("Woven Service Booking", "../../", 1, 1,$unicode,'','');
?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	var permission='<? echo $permission; ?>';
	function openmypage_order22(page_link,title)
	{
		if (form_validation('cbo_booking_month*cbo_booking_year','Booking Month*Booking Year*Fabric Nature*Fabric Source')==false)
		{
			return;
		}
		else
		{
			page_link=page_link+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_booking_month*cbo_booking_year*txt_booking_date*vari_fab_source_id','../../');
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=470px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];;
				var id=this.contentDoc.getElementById("po_number_id");
				var po=this.contentDoc.getElementById("po_number");
				if (id.value!="")
				{
					reset_form('','booking_list_view','txt_order_no_id*txt_order_no*cbo_currency*txt_exchange_rate*cbo_pay_mode*txt_booking_date*cbo_supplier_name*hidden_supplier_id*txt_tenor*txt_attention*txt_delivery_date*cbo_source*txt_booking_no','txt_booking_date,<? echo date("d-m-Y"); ?>');
					freeze_window(5);
					document.getElementById('txt_order_no_id').value=id.value;
					document.getElementById('txt_order_no').value=po.value;
					get_php_form_data( id.value, "populate_order_data_from_search_popup", "requires/service_booking_controller" );

					check_exchange_rate();
					release_freezing();
				}
			}
		}
	}

	function set_process(fabric_desription_id,type)
	{
		var order_no_id=$('#txt_order_no_id').val();

		var txt_booking_no = $('#txt_booking_no').val();
		var txt_booking_date = $('#txt_booking_date').val();
		var hide_fabric_description = $('#cbo_fabric_description').val();
		var cbo_process = $('#cbo_process').val();
		var vari_fab_source_id = $('#vari_fab_source_id').val()*1;
		//alert(vari_fab_source_id);
		if(vari_fab_source_id==2)
		{
			 $('#cbo_colorsizesensitive').val(1);
			// return;
		}
		if(cbo_process!=0)
		{
			var response=return_global_ajax_value( cbo_process+"**"+hide_fabric_description+"**"+order_no_id+"**"+txt_booking_no, 'check_fabric_process_data', '', 'requires/service_booking_controller');
			var conv_id=rtrim(response);
			if(conv_id==1)
			{
				alert('Same Fabric and Process are not allowed.');
				return;
			}
		}

		if(type=="set_process")
		{


			//alert('ff');
			show_list_view(document.getElementById('txt_order_no_id').value+'**'+1+'**'+fabric_desription_id+'**'+document.getElementById('cbo_process').value+'**'+document.getElementById('cbo_colorsizesensitive').value+'**'+document.getElementById('txt_order_no_id').value+'**'+document.getElementById('txt_booking_no').value+'**'+document.getElementById('cbo_company_name').value+'**'+document.getElementById('txt_booking_date').value+'**'+document.getElementById('cbo_currency').value+'**'+document.getElementById('service_rate_from').value+'**'+document.getElementById('vari_fab_source_id').value, 'show_detail_booking_list_view','booking_list_view','requires/service_booking_controller','$(\'#hide_fabric_description\').val(\'\')');

		}
		$("#hide_fabric_description").val(fabric_desription_id);
		set_button_status(0, permission, 'fnc_service_booking_dtls',2);
	}

	function fnc_fabric_description_id(color_id, button_status, type)
	{
		var hide_color_id='';
		var cbo_process=$('#cbo_process').val();
		var color_ids=color_id+cbo_process;
		var hide_fabric_desc=document.getElementById('hide_fabric_description').value;
		if(type==1)
		{
			hide_color_id=document.getElementById('hide_fabric_description').value;
			hide_color_ids=hide_color_id+cbo_process;
		}
		else
		{
			if(hide_fabric_desc!='')
			{
				hide_color_id=parseInt(document.getElementById('hide_fabric_description').value);
				hide_color_ids=hide_color_id+cbo_process;
			}
			else
			{
				hide_color_ids=0;
			}
		}
		if(color_ids==hide_color_ids)
		{
			document.getElementById('hide_fabric_description').value='';
			set_button_status(0, permission, 'fnc_trims_booking',1);
		}
		else
		{
			document.getElementById('hide_fabric_description').value=color_id;
			set_button_status(button_status, permission, 'fnc_trims_booking',1);
		}
	}

	function setmaster_value(process, sensitivity,fabrication_id)
	{
		$('#cbo_fabric_description').val(fabrication_id);
		$('#cbo_process').val(process);
		document.getElementById('cbo_colorsizesensitive').value=sensitivity;
	}

	function calculate_amount( param1, param2 )
	{
		var cbo_currercy=$('#cbo_currency').val()*1;
		var cbo_process=$('#cbo_process').val();
		//alert(param1+'='+param2); return;
		//var rowCount=$('#table_'+param1+' tbody tr').length;
		var fab_req_source = $('#fab_req_source_'+param1).val();
		//alert(fab_req_source);
		var cbo_colorsizesensitive=$('#cbo_colorsizesensitive').val();
		var txt_exchange_rate=$('#txt_exchange_rate').val();
		var txt_woqnty=(document.getElementById('txt_woqnty_'+param1+'_'+param2).value)*1;
		var previ_woqnty=(document.getElementById('txt_prev_woqnty_'+param1+'_'+param2).value)*1;
		//alert(txt_woqnty);//txt_woqnty_6141_1 txt_prev_woqnty_6141_1
		var woamount=(document.getElementById('txt_amount_'+param1+'_'+param2).value)*1;
		//alert(txt_woqnty);
		var txt_rate=(document.getElementById('txt_rate_'+param1+'_'+param2).value)*1;
		if(txt_rate=="" || txt_rate==0)
		{
		var txt_rate=(document.getElementById('txt_hidden_rate_'+param1+'_'+param2).value)*1;
		}
		
		if(txt_woqnty<1)
		{
		var txt_amount=txt_woqnty*txt_rate;
		}
		else
		{
		var txt_amount=number_format(txt_woqnty*txt_rate,4,'.','');
		}

		var txt_reqwo_amt=(document.getElementById('txt_reqwoamt_'+param1+'_'+param2).value)*1;
		var txt_reqwoqnty=(document.getElementById('txt_reqwoqnty_'+param1+'_'+param2).value)*1;
		//txt_reqwoqnty_6141_1
		//alert(txt_reqwo_amt);
		var txt_prev_woamt=(document.getElementById('txt_prev_woamt_'+param1+'_'+param2).value)*1;
		if(txt_woqnty<1) //For Fraction value check
		{
			var tot_wo_amt=((txt_woqnty*txt_rate)+txt_prev_woamt);//-txt_prev_woamt;
		}
		else
		{
			var tot_wo_amt=number_format(((txt_woqnty*txt_rate)+txt_prev_woamt),4,'.','');//-txt_prev_woamt;
		}
		
		var woamount_hidden=(document.getElementById('txt_hidden_woamt_'+param1+'_'+param2).value);
		var	wo_hidden_amount=woamount_hidden.split("_");
		var wo_amount=wo_hidden_amount[0]*1;
		if(wo_amount==0 || wo_amount=='')
		{
		var wo_amount=wo_hidden_amount[4]*1;
		}
		
		var pre_cost_exchn_rate=wo_hidden_amount[1]*1;
		var wo_rate_hid=wo_hidden_amount[2]*1;
		var wo_qty_hid=wo_hidden_amount[3]*1;

		if(cbo_currercy==1)
		{
			var pre_req_amount_check=txt_reqwo_amt*pre_cost_exchn_rate;
			var wo_amount_check=wo_amount*pre_cost_exchn_rate;
		}
		else
		{
			var pre_req_amount_check=txt_reqwo_amt;
			var	wo_amount_check=wo_amount;
		}
		//alert(fab_req_source+'='+pre_req_amount_check);
		if(fab_req_source==1)
		{
			if(cbo_colorsizesensitive!=0)
			{
				//if(wo_amount_check<txt_amount)
				if(pre_req_amount_check<tot_wo_amt)
				{
					alert('WO amount is not allowed over budget amount\n Req Amount='+pre_req_amount_check+'\n'+'Total Wo Amount='+tot_wo_amt);
					$('#txt_woqnty_'+param1+'_'+param2).val(wo_qty_hid);
					$('#txt_amount_'+param1+'_'+param2).val(wo_qty_hid*wo_rate_hid);
					$('#txt_rate_'+param1+'_'+param2).val(wo_rate_hid);
					return;
				}
			}
			document.getElementById('txt_amount_'+param1+'_'+param2).value=txt_amount;
		}
		else if(fab_req_source==2) //Booking
		{
			if(pre_req_amount_check>0)
			{
				if(cbo_colorsizesensitive!=0)
				{
					//if(wo_amount_check<txt_amount)
					if(pre_req_amount_check<tot_wo_amt)
					{
						alert('WO amount is not allowed over booking amount\n Req Amount='+pre_req_amount_check+'\n'+'Total Wo Amount='+tot_wo_amt);
						$('#txt_woqnty_'+param1+'_'+param2).val(wo_qty_hid);
						$('#txt_amount_'+param1+'_'+param2).val(wo_qty_hid*wo_rate_hid);
						$('#txt_rate_'+param1+'_'+param2).val(wo_rate_hid);
						return;
					}
				}
				document.getElementById('txt_amount_'+param1+'_'+param2).value=txt_amount;
			}
			else
			{
					if(cbo_colorsizesensitive!=0)
					{
						//if(wo_amount_check<txt_amount)
						
						var tot_wo_qtyy=txt_woqnty+previ_woqnty;
						//var tot_reqwoqnty=number_format(txt_reqwoqnty,2,'.','');
						//var tot_woqty=number_format(tot_wo_qtyy,2,'.','');
						var tot_reqwoqnty=txt_reqwoqnty;
						var tot_woqty=tot_wo_qtyy;
						
						//alert(tot_woqty+'='+previ_woqnty+'='+tot_reqwoqnty);
						if(tot_woqty>tot_reqwoqnty)
						{
							alert('WO Qty is not allowed over Booking Qty\n Req Qty='+tot_reqwoqnty+'\n'+'Total Wo Qty='+tot_woqty);
							$('#txt_woqnty_'+param1+'_'+param2).val(wo_qty_hid);
							$('#txt_amount_'+param1+'_'+param2).val(wo_qty_hid*wo_rate_hid);
							$('#txt_rate_'+param1+'_'+param2).val(wo_rate_hid);
							return;
						}
					}
					document.getElementById('txt_amount_'+param1+'_'+param2).value=txt_amount;
			}
		}
	}

	function copy_value(param1,param2,type)
	{
		var cbo_process=$('#cbo_process').val();
		 var copy_val=document.getElementById('copy_val').checked;
		 var rowCount=$('#table_'+param1+' tbody tr').length;
		 if(copy_val==true)
		  {
			  for(var j=param2; j<=rowCount; j++)
			  {
				  if(type=='txt_rate')
				  {
					  var txt_woqnty=(document.getElementById('txt_woqnty_'+param1+'_'+j).value)*1;
					  var txt_rate=(document.getElementById('txt_rate_'+param1+'_'+param2).value)*1;
					  var txt_amount=txt_woqnty*txt_rate;
					  document.getElementById('txt_rate_'+param1+'_'+j).value=txt_rate;
					  document.getElementById('txt_amount_'+param1+'_'+j).value=txt_amount;
				  }
				  if(type=='txt_woqnty')
				  {
					  var txt_woqnty=(document.getElementById('txt_woqnty_'+param1+'_'+param2).value)*1;
					  var txt_rate=(document.getElementById('txt_rate_'+param1+'_'+j).value)*1;
					  var txt_amount=txt_woqnty*txt_rate;
					  document.getElementById('txt_amount_'+param1+'_'+j).value=txt_amount;
				  }
				  if(type=='uom')
				  {
					  var uom=(document.getElementById('uom_'+param1+'_'+param2).value);
					  document.getElementById('uom_'+param1+'_'+j).value=uom;
				  }
				  if(type=='mcdia')
				  {
					  var mcdia=(document.getElementById('mcdia_'+param1+'_'+param2).value);
					  document.getElementById('mcdia_'+param1+'_'+j).value=mcdia;
				  }
				   if(type=='findia')
				  {
					  var findia=(document.getElementById('findia_'+param1+'_'+param2).value);
					  document.getElementById('findia_'+param1+'_'+j).value=findia;
				  }
				  if(type=='fingsm')
				  {
					  var fingsm=(document.getElementById('fingsm_'+param1+'_'+param2).value);
					  document.getElementById('fingsm_'+param1+'_'+j).value=fingsm;
				  }
				  if(type=='slength')
				  {
					  var slength=(document.getElementById('slength_'+param1+'_'+param2).value);
					  document.getElementById('slength_'+param1+'_'+j).value=slength;
				  }
				  if(type=='yarncount')
				  {
					  var yarncount=(document.getElementById('yarncount_'+param1+'_'+param2).value);
					  document.getElementById('yarncount_'+param1+'_'+j).value=yarncount;
				  }
				  if(type=='lotno')
				  {
					  var lotno=(document.getElementById('lotno_'+param1+'_'+param2).value);
					  document.getElementById('lotno_'+param1+'_'+j).value=lotno;
				  }
				  if(type=='brand')
				  {
					  var brand=(document.getElementById('brand_'+param1+'_'+param2).value);
					  document.getElementById('brand_'+param1+'_'+j).value=brand;
				  }
				  if(type=='composition')
				  {
					  var composition=(document.getElementById('subcon_supplier_compo_'+param1+'_'+param2).value);
					  var supplier_rate_id=(document.getElementById('subcon_supplier_rateid_'+param1+'_'+param2).value);
					  document.getElementById('subcon_supplier_compo_'+param1+'_'+j).value=composition;
					  document.getElementById('subcon_supplier_rateid_'+param1+'_'+j).value=supplier_rate_id;
				  }
			  }
		  }
	}


	function fnc_generate_booking()
	{
		if (form_validation('txt_order_no_id','Order No*Fabric Nature*Fabric Source')==false)
		{
			return;
		}
		else
		{
			var data="action=generate_fabric_booking"+get_submitted_data_string('txt_order_no_id',"../../");
			http.open("POST","requires/service_booking_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_generate_booking_reponse;
		}
	}

	function fnc_generate_booking_reponse()
	{
		if(http.readyState == 4)
		{
			document.getElementById('booking_list_view').innerHTML=http.responseText;
		}
	}

	function open_consumption_popup(page_link,title,po_id,i)
	{
		var cbo_company_id=document.getElementById('cbo_company_name').value;
		var po_id =document.getElementById(po_id).value;

		var txtwoq=document.getElementById('txtwoq_'+i).value;
		var cons_breck_downn=document.getElementById('consbreckdown_'+i).value;
		var cbocolorsizesensitive=document.getElementById('cbocolorsizesensitive_'+i).value;
		if(po_id==0 )
		{
			alert("Select Po Id")
		}
		else
		{
			var page_link=page_link+'&po_id='+po_id+'&cbo_company_id='+cbo_company_id+'&txtwoq='+txtwoq+'&cons_breck_downn='+cons_breck_downn+'&cbocolorsizesensitive='+cbocolorsizesensitive;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1060px,height=450px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var cons_breck_down=this.contentDoc.getElementById("cons_breck_down");
				var woq=this.contentDoc.getElementById("cons_sum");
				document.getElementById('consbreckdown_'+i).value=cons_breck_down.value;
				document.getElementById('txtwoq_'+i).value=woq.value;
				document.getElementById('txtamount_'+i).value=(woq.value)*1*(document.getElementById('txtrate_'+i).value);
			}
		}
	}

	function openmypage_booking(page_link,title)
	{
		var cbo_pay_mode=$('#cbo_pay_mode').val();
		var cbo_company_name=$('#cbo_company_name').val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1070px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_booking");

			if (theemail.value!="")
			{
				reset_form('servicebooking_1','booking_list_view','','txt_booking_date,<? echo date("d-m-Y"); ?>');
				$('#hide_fabric_description').val('');
				get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/service_booking_controller" );
			    show_list_view(document.getElementById('txt_booking_no').value+'**'+document.getElementById('txt_booking_no').value, 'fabric_detls_list_view','data_panel','requires/service_booking_controller','');
				set_button_status(1, permission, 'fnc_trims_booking',1);
			}
		}
	}

	function open_terms_condition_popup(page_link,title)
	{
		var txt_booking_no=document.getElementById('txt_booking_no').value;
		if (txt_booking_no=="")
		{
			alert("Save The Booking First")
			return;
		}
		else
		{
			page_link=page_link+get_submitted_data_string('txt_booking_no','../../');
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=470px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
			}
		}
	}

	function fnc_trims_booking( operation )
	{
		
		if( document.getElementById('txt_app_status').value == 1 ||  document.getElementById('txt_app_status').value == 3){
			alert($('#display_app_status').text() + " Update/Delete not allow.");return;
		} 
		
		var data_all="";
		if (form_validation('cbo_booking_month*cbo_company_name*cbo_currency*cbo_pay_mode*cbo_supplier_name','Booking Month*Company Name*Currency*Pay Mode*Supplier')==false)
		{
			return;
		}

		data_all=data_all+get_submitted_data_string('txt_booking_no*cbo_booking_month*cbo_booking_year*cbo_company_name*cbo_supplier_name*hidden_supplier_id*cbo_currency*txt_exchange_rate*txt_booking_date*txt_delivery_date*cbo_pay_mode*cbo_source*txt_attention*cbo_buyer_name*vari_fab_source_id*txt_tenor*cbo_ready_to_approved',"../../");
		var data="action=save_update_delete&operation="+operation+data_all;


		freeze_window(operation);

		http.open("POST","requires/service_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_trims_booking_reponse;
	}

	function fnc_trims_booking_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if(trim(reponse[0])=='pi1'){
				alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}

			if(trim(reponse[0])=='iss1'){
				alert("Issue found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
				release_freezing();
				return;
			}
			
			if(trim(reponse[0])=='issFinPrcess'){
				alert("Fabric Issue to Fin. Process :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
				release_freezing();
				return;
			}

			if(reponse[0]==0 || reponse[0]==1)
			{
				document.getElementById('txt_booking_no').value=reponse[1];
				show_msg(trim(reponse[0]));
				
				document.getElementById('txt_booking_no').value=reponse[1];
				if(reponse[0]==0)
				{
					document.getElementById('booking_mst_id').value=reponse[2];
				}
				set_button_status(1, permission, 'fnc_trims_booking',1);
				
				release_freezing();
			}
			if(parseInt(trim(reponse[0]))==2)
			{
				alert(trim(reponse[0]));
				location.reload();
				release_freezing();
			}
			 release_freezing();
			if(parseInt(trim(reponse[0]))==17)
			{
				show_msg(trim(reponse[0]));
				alert(reponse[1]+'\nPrev Service Wo Qty='+reponse[2]+'\n Req. Qty='+reponse[3]);
				release_freezing();
				return;
			}
			if(parseInt(trim(reponse[0]))==10)
			{
				show_msg(trim(reponse[0]));
				release_freezing();
				return;
			}
		}
	}

	function fnc_service_booking_dtls( operation )
	{
		freeze_window(operation);
		var data_all="";
		if (form_validation('txt_booking_no','Booking No')==false)
		{
			alert('Please  Save Master Part First');
			release_freezing();
			return;
		}

		data_all=data_all+get_submitted_data_string('txt_booking_no*cbo_booking_month*cbo_booking_year*cbo_company_name*cbo_supplier_name*hidden_supplier_id*cbo_currency*txt_exchange_rate*txt_booking_date*txt_delivery_date*cbo_pay_mode*cbo_source*txt_attention*cbo_buyer_name*txt_order_no_id*cbo_process*cbo_colorsizesensitive',"../../");

		if (form_validation('txt_order_no*cbo_process*cbo_fabric_description','Order No*Process*Fabric Desc')==false)
		{
			release_freezing();
			return;
		}
		var hdfabid=$('#cbo_fabric_description').val();
		var cbo_process=$('#cbo_process').val()*1;

		var txt_order_no_id = $('#txt_order_no_id').val();
		var txt_booking_no = $('#txt_booking_no').val();
		var fab_req_source = $('#fab_req_source_'+hdfabid).val();
		//alert(fab_req_source);
		if(operation==0 && hdfabid!='')
		{
			var response=return_global_ajax_value( cbo_process+"**"+hdfabid+"**"+txt_order_no_id+"**"+txt_booking_no, 'check_fabric_process_data', '', 'requires/service_booking_controller');
			var conv_id=rtrim(response);
			if(conv_id==1)
			{
				alert('Same Fabric and Process are not allowed.');
				release_freezing();
				return;
			}
		}

		var row_num=$('#table_'+hdfabid+' tbody tr').length;
		//alert(row_num);
		for (var i=1; i<=row_num; i++)
		{
			if ((document.getElementById('txt_rate_'+hdfabid+'_'+i).value=="" || document.getElementById('txt_rate_'+hdfabid+'_'+i).value==0) && (document.getElementById('txt_woqnty_'+hdfabid+'_'+i).value !="" || document.getElementById('txt_woqnty_'+hdfabid+'_'+i).value!=0))
			{
				alert("WO. Rate is Empty");
				release_freezing();
				return;
			}
		//	alert('B');
			data_all+=get_submitted_data_string('po_id_'+hdfabid+'_'+i+'*fabric_description_id_'+hdfabid+'_'+i+'*artworkno_'+hdfabid+'_'+i+'*color_size_table_id_'+hdfabid+'_'+i+'*gmts_color_id_'+hdfabid+'_'+i+'*item_color_id_'+hdfabid+'_'+i+'*gmts_size_id_'+hdfabid+'_'+i+'*item_size_'+hdfabid+'_'+i+'*uom_'+hdfabid+'_'+i+'*txt_woqnty_'+hdfabid+'_'+i+'*txt_rate_'+hdfabid+'_'+i+'*txt_amount_'+hdfabid+'_'+i+'*txt_paln_cut_'+hdfabid+'_'+i+'*updateid_'+hdfabid+'_'+i+'*startdate_'+hdfabid+'_'+i+'*enddate_'+hdfabid+'_'+i+'*findia_'+hdfabid+'_'+i+'*item_color_'+hdfabid+'_'+i+'*labdipno_'+hdfabid+'_'+i+'*mcdia_'+hdfabid+'_'+i+'*fingsm_'+hdfabid+'_'+i+'*slength_'+hdfabid+'_'+i+'*yarncount_'+hdfabid+'_'+i+'*lotno_'+hdfabid+'_'+i+'*subcon_supplier_compo_'+hdfabid+'_'+i+'*subcon_supplier_rateid_'+hdfabid+'_'+i+'*brand_'+hdfabid+'_'+i+'*txt_job_no_'+hdfabid+'_'+i+'*txtremaks_'+hdfabid+'_'+i,"../../",i);
		}
		//alert(data_all);
		data_all=data_all+get_submitted_data_string('booking_mst_id*cbo_company_name*cbo_process*cbo_colorsizesensitive*txt_booking_no*txt_all_update_id*txt_order_no_id*vari_fab_source_id',"../../");
		var data="action=save_update_delete_dtls&operation="+operation+data_all+'&hide_fabric_description='+hdfabid+'&row_num='+row_num;
		//alert(data);
		http.open("POST","requires/service_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_service_booking_dtls_reponse;
	}

	function fnc_service_booking_dtls_reponse()
	{
		if(http.readyState == 4)
		{
			 var response=trim(http.responseText).split('**');
			 //alert(response[0])
			 if(trim(response[0])=='pi1'){
				alert("PI Number Found :"+trim(response[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}
			
			if(trim(response[0])=='17'){
				alert("Budget Qty Exceed.")
				release_freezing();
				return;
			}

			if(trim(response[0])=='iss1'){
				alert("Issue found :"+trim(response[2])+"\n So Update/Delete Not Possible");
				release_freezing();
				return;
			}
			
			if(trim(response[0])=='issFinPrcess'){
				alert("Fabric Issue to Fin. Process :"+trim(response[2])+"\n"+trim(response[3]));
				release_freezing();
				return;
			}
			 show_msg(trim(response[0]));
			 if(response[0]==0 || response[0]==1 || response[0]==2)
			 {
				$('#booking_list_view').text('');
				$("#cbo_colorsizesensitive").val(0);
				$("#cbo_fabric_description").val(0);
				$("#cbo_colorsizesensitive").removeAttr("disabled","disabled");
				$("#cbo_fabric_description").removeAttr("disabled","disabled");
				show_list_view(response[1]+'**'+response[1], 'fabric_detls_list_view','data_panel','requires/service_booking_controller',"$('#hide_fabric_description').val('')");
				set_button_status(0, permission, 'fnc_service_booking_dtls',2);
			 }
			 release_freezing();
		}
	}

	function fnc_show_booking()
	{
		freeze_window(0);
		if (form_validation('txt_booking_no','Booking No')==false)
		{
			release_freezing();
			return;
		}
		else
		{
			var data="action=show_trim_booking"+get_submitted_data_string('txt_booking_no',"../../");
			http.open("POST","requires/service_booking_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_show_booking_reponse;
		}
	}

	function fnc_show_booking_reponse()
	{
		if(http.readyState == 4)
		{
			document.getElementById('booking_list_view').innerHTML=http.responseText;
			set_button_status(1, permission, 'fnc_trims_booking',2);
			set_all_onclick();
			release_freezing();
		}
	}

	function update_booking_data(data)
	{
		var data=data.split("_");
		$("#booking_list_view").text('');
		$("#cbo_fabric_description").val(data[2]);
		$("#hide_fabric_description").val(data[2]);
		$("#cbo_process").val(data[3]);
		$("#cbo_colorsizesensitive").val(data[4]);
		$("#cbo_colorsizesensitive").attr("disabled",true);
		$("#cbo_fabric_description").attr("disabled",true);
		$("#txt_all_update_id").val(data[0]);
		$("#txt_order_no_id").val(data[5]);
		$("#txt_order_no").val(data[7]);
		var vari_fab_source_id =$("#vari_fab_source_id").val(); 
		//alert(vari_fab_source_id);

		var company= $("#cbo_company_name").val();

		if(vari_fab_source_id==1 || vari_fab_source_id==2)
		{
			load_drop_down( 'requires/service_booking_controller', data[1], 'load_drop_down_fabric_description', 'fabric_description_td' );
		}
		else
		{
			//load_drop_down( 'requires/service_booking_controller', data[5], 'load_drop_down_booking_fabric_description', 'fabric_description_td' );
		}
		$("#cbo_fabric_description").val(data[2]);
		show_list_view(data[1]+'**'+0+'**'+data[2]+'**'+data[3]+'**'+data[4]+'**'+data[5]+'**'+data[6]+'**'+data[0]+'**'+document.getElementById('service_rate_from').value+'**'+data[8]+'**'+company+'**'+vari_fab_source_id, 'update_detail_booking_list_view','booking_list_view','requires/service_booking_controller','');
		set_button_status(1, permission, 'fnc_service_booking_dtls',2);
	}

	function generate_trim_report(action,mail_send_data)
	{
		if (form_validation('txt_booking_no','Booking No')==false)
		{
			return;
		}
		var show_rate="0";
		var show_buyer="0";
		
		if(action=='show_trim_booking_bpkw_report' || action=='show_trim_booking_report' || action=='show_trim_booking_report2' || action=='show_trim_booking_report3')
		{
			var show_item='';
			var r=confirm("Press  \"OK\"  to Show  Rate and Amount\nPress  \"Cancel\"  to Hide Rate and Amount");
			if (r==true) show_rate="1"; else show_rate="0";
		}
		else if(action=='show_trim_booking_report3')
		{
			show_rate="0";
			var r=confirm("Press  \"Cancel\"  to Hide Buyer and Style \nPress  \"OK\"  to Show Buyer and Style ");
			if (r==true) show_buyer="1"; else show_buyer="0";
		}
		else if(action=='show_trim_booking_report4')
		{
			var show_comment='';
			var r=confirm("Press  \"Cancel\"  to hide Rate and Amount \nPress  \"OK\"  to Show Rate and Amount ");
			if (r==true) show_comment="1"; else show_comment="0";
		}

		var data="action="+action+get_submitted_data_string('txt_booking_no*cbo_company_name*hidden_supplier_id*booking_mst_id*cbo_template_id',"../../")+'&show_rate='+show_rate+'&show_buyer='+show_buyer+'&show_comment='+show_comment+'&mail_send_data='+mail_send_data;
		//alert(data);
		http.open("POST","requires/service_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_trim_report_reponse;
	}

	function generate_trim_report_reponse()
	{
		if(http.readyState == 4)
		{
			var file_data=http.responseText.split('****');
			$('#pdf_file_name').html(file_data[1]);
			$('#data_panel2').html(file_data[0] );
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel2').innerHTML+'</body</html>');
			d.close();
		}
	}

	function print_report_button_setting(report_ids)
	{
		$("#print_booking").hide();
		$("#print_booking1").hide();
		$("#print_booking_bpkw").hide();
		$("#print_booking_2").hide();
		$("#print_booking_3").hide();
		$("#print_booking_4").hide();
		//alert(report_ids);
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==11) $("#print_booking").show();
			if(report_id[k]==12) $("#print_booking1").show();
			if(report_id[k]==59) $("#print_booking_bpkw").show();
			if(report_id[k]==116) $("#print_booking_2").show();
			if(report_id[k]==136) $("#print_booking_3").show();
			if(report_id[k]==177) $("#print_booking_4").show();
		}
	}

	function check_exchange_rate()
	{
		var cbo_currercy=$('#cbo_currency').val();
		var booking_date = $('#txt_booking_date').val();
		var cbo_company_name = $('#cbo_company_name').val();
		var response=return_global_ajax_value( cbo_currercy+"**"+booking_date+"**"+cbo_company_name, 'check_conversion_rate', '', 'requires/service_booking_controller');
		var response=response.split("_");
		$('#txt_exchange_rate').val(response[1]);
	}

	function check_wo_qty_row(fab_id,tr_id)
	{
		var hide_fabric_description=$('#hide_fabric_description').val()*1;
		var cbo_process=$('#cbo_process').val();
		var row_num=$('#table_'+hide_fabric_description+' tbody tr').length;
		var txt_woqnty2=$('#txt_woqnty_'+hide_fabric_description+'_'+tr_id).val()*1;
		var prev_woqnty=$('#txt_prev_woqnty_'+hide_fabric_description+'_'+tr_id).val()*1;
		var stock_check=txt_woqnty2-prev_woqnty;
		if(stock_check<=0)
		{
			alert("No Stock Qty.");
			$('#txt_woqnty_'+hide_fabric_description+'_'+tr_id).val(0);
		}
		for (var i=1; i<=row_num; i++)
		{
			var txt_woqnty=$('#txt_woqnty_'+hide_fabric_description+'_'+i).val()*1;
			var txt_reqwoqnty=$('#txt_reqwoqnty_'+hide_fabric_description+'_'+i).val()*1;
			if(txt_woqnty>txt_reqwoqnty)
			{
				alert("Budget Qty. Over");
				$('#txt_woqnty_'+hide_fabric_description+'_'+i).val(txt_reqwoqnty);
			}
		}
	}

	function service_supplier_popup(id)
	{
		var cbo_company_name = $('#cbo_company_name').val();
		var cbo_supplier_name = $('#cbo_supplier_name').val();
		var hidden_supplier_id = $('#hidden_supplier_id').val();
		if (form_validation('cbo_company_name*cbo_supplier_name*txt_exchange_rate','Company Name*cbo_supplier_name*Exchange Rate')==false)
		{
			return;
		}
		hidden_supplier_rate_id=$('#subcon_supplier_rateid_'+id).val();
		var title="Supplier Work Order Rate Info";
		var page_link = 'requires/service_booking_controller.php?cbo_company_name='+cbo_company_name+'&cbo_supplier_name='+$("#cbo_supplier_name").val()+'&hidden_supplier_id='+$("#hidden_supplier_id").val()+'&cbo_process='+$("#cbo_process").val()+'&txt_exchange_rate='+$("#txt_exchange_rate").val()+'&hidden_supplier_rate_id='+hidden_supplier_rate_id+'&action=Supplier_workorder_popup';
		  //alert(page_link);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var hide_charge_id=this.contentDoc.getElementById("hide_charge_id").value;
			var hide_supplier_rate=this.contentDoc.getElementById("hide_supplier_rate").value;
			var construction_compo=this.contentDoc.getElementById("hide_construction_compo").value;
			//alert('#subcon_supplier_compo_'+id)
			$('#subcon_supplier_compo_'+id).val(construction_compo);
			$('#subcon_supplier_rateid_'+id).val(hide_charge_id);
			$('#txt_rate_'+id).val(hide_supplier_rate);
			var fabric_id=id.split("_");
			copy_value(fabric_id[0],fabric_id[1],'txt_rate');
			copy_value(fabric_id[0],fabric_id[1],'composition');
		}
	}

function process_rate_check(process_id)
	{
		var txt_order_no_id = $('#txt_order_no_id').val();
		var process_id = $('#cbo_process').val();
		var hidden_process_id = $('#cbo_process').val();
		var vari_fab_source_id = $('#vari_fab_source_id').val();
		
		if (form_validation('txt_order_no','Order No')==false)
		{
			return;
		}
		if(vari_fab_source_id==2)
		{
			var response=return_global_ajax_value( txt_order_no_id+"**"+process_id, 'check_process_rate', '', 'requires/service_booking_controller');
			//var response=response.split("_");
		//	alert(hidden_process_id+'='+response);
			if(response==0)
			{
				alert('Not Found This Process Rate in Budget');
				$('#cbo_process').val(0);
				$('#cbo_fabric_description').val(0);
				$('#booking_list_view').text('');
				//booking_list_view
				return;
			}
		}
		
	}


	function prev_booking(process_id)
	{
		var txt_order_no_id = $('#txt_order_no_id').val();
		var process_id = $('#cbo_process').val();

		show_list_view(txt_order_no_id+'_'+process_id, 'previous_booking_list_view','booking_previous_list_view','requires/service_booking_controller','');
	}

	function auto_completesupplier() // Auto Complite Party/Transport Com
	{
		if( form_validation('cbo_company_name*cbo_pay_mode','Company Name*PayMode')==false )
		{
			return;
		}
		//cbo_supplier_name hidden_supplier_id
		var company_id=document.getElementById('cbo_company_name').value;
		var pay_mode=document.getElementById('cbo_pay_mode').value;

		var supplier = return_global_ajax_value( company_id+'_'+pay_mode, 'supplier_company_action', '', 'requires/service_booking_controller');
		//alert(supplier);
		supplierInfo = eval(supplier);
		$("#cbo_supplier_name").autocomplete({
		 source: supplierInfo,
		 search: function( event, ui ) {
			$("#hidden_supplier_id").val("");
			$("#hidden_supplier_name").val("");
		},
		select: function (e, ui) {
				$(this).val(ui.item.label);
				$("#hidden_supplier_name").val(ui.item.label);
				$("#hidden_supplier_id").val(ui.item.id);
			}
		});

		$(".supplier_name").live("blur",function(){
			  if($(this).siblings(".hdn_supplier_name").val() == ""){
				  $(this).val("");
			 }
		});
	}

	function supplier_empty_check()
	{
		$("#cbo_supplier_name").val('');
		$("#hidden_supplier_name").val('');
		$("#hidden_supplier_id").val('');
	}

	function supplier_attention_check()
	{
		var hidden_supplier_id=document.getElementById('hidden_supplier_id').value;
		var cbo_pay_mode=document.getElementById('cbo_pay_mode').value;
		//alert(hidden_supplier_id);
		get_php_form_data( hidden_supplier_id+'_'+cbo_pay_mode, 'load_drop_down_attention', 'requires/service_booking_controller');
	}

	function fnc_poportionate_qty(qty,hide_fabric_description,cbo_process)
	{
		var row_num=$('#table_'+hide_fabric_description+' tbody tr').length;
		qty=qty*1;
		if(qty>0)
		{
			var tot_bal_woqnty=0;
			for (var k=1; k<=row_num; k++)
			{
				var bal_woqnty=($('#txt_hidden_woqnty_bal_'+hide_fabric_description+'_'+k).val())*1;
				 tot_bal_woqnty+=bal_woqnty;
			}

			for (var i=1; i<=row_num; i++)
			{
				var txt_woqnty=($('#txt_hidden_woqnty_bal_'+hide_fabric_description+'_'+i).val())*1;
				 var txtwoq_cal =number_format_common((txt_woqnty*qty)/(tot_bal_woqnty),5,0);
				$('#txt_woqnty_'+hide_fabric_description+'_'+i).val(txtwoq_cal);
			}
		}
	}

	function openmypage_order(page_link,title)
	{
		var vari_fab_source_id = $('#vari_fab_source_id').val()*1;
		
		
		if (form_validation('txt_booking_no*cbo_company_name','Booking No*Company')==false)
		{
			alert('Master Part Save First');return;
		}
		else
		{
			page_link=page_link+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_booking_month*cbo_booking_year*txt_booking_date','../../');
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1150px,height=470px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];;
				var id=this.contentDoc.getElementById("po_number_id");
				var po=this.contentDoc.getElementById("po_number");
			//	alert(id.value);
				if (id.value!="")
				{
					freeze_window(5);
					document.getElementById('txt_order_no_id').value=id.value;
					document.getElementById('txt_order_no').value=po.value;
					get_php_form_data( id.value+'_'+vari_fab_source_id, "populate_order_data_from_search_popup", "requires/service_booking_controller" );
					release_freezing();

				}
			}
		}
	}
	
	function call_print_button_for_mail(mail_address,mail_body,type){
		var response=return_global_ajax_value(document.getElementById('cbo_company_name').value, 'send_mail_report_setting_first_select', '', 'requires/service_booking_controller');
		var response=response.split(",");
		if(response[0]==11)generate_trim_report('show_trim_booking_report','1___'+mail_address);
		else if(response[0]==12) generate_trim_report('show_trim_booking_report1','1___'+mail_address);
		else if(response[0]==59) generate_trim_report('show_trim_booking_bpkw_report','1___'+mail_address);
		else if(response[0]==116) generate_trim_report('show_trim_booking_report2','1___'+mail_address);
		else if(response[0]==136) generate_trim_report('show_trim_booking_report3','1___'+mail_address);
		else if(response[0]==177) generate_trim_report('show_trim_booking_report4','1___'+mail_address);
	}
	
	
	
</script>
</head>
<body onLoad="set_hotkey();check_exchange_rate();">
<div style="width:100%;" align="center">
 <b style=" background:red"><!--Under Construction --></b>
     <? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="servicebooking_1"  autocomplete="off" id="servicebooking_1">
        <fieldset style="width:1000px;">
            <legend>Fabric Service Booking</legend>
            <table  width="1000" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td align="right" class="must_entry_caption" colspan="4">Booking No</td>
                    <td colspan="4"><input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_booking('requires/service_booking_controller.php?action=service_booking_popup','Service Booking Search');" readonly placeholder="Double Click for Booking" name="txt_booking_no" id="txt_booking_no"/>
                    <input type="hidden" id="booking_mst_id">
                    </td>
                </tr>
                <tr>
                    <td width="110" class="must_entry_caption">Booking Month</td>
                    <td width="140">
                        <?=create_drop_down( "cbo_booking_month", 80, $months,"", 1, "-- Select --", "", "",0 ); ?>
                        <?=create_drop_down( "cbo_booking_year", 50, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?>
                    </td>
                    <td width="110" class="must_entry_caption">Company Name</td>
                    <td width="140"><?=create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected, "load_drop_down( 'requires/service_booking_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data( this.value, 'populate_report_setting', 'requires/service_booking_controller' );check_exchange_rate();","","" ); ?>
                        <input type="hidden" id="report_ids">
                        <input type="hidden" id="vari_fab_source_id">
						<input type="hidden" name="approval_status_id" id="approval_status_id" class="text_boxes" value=""  style="width:150px;" />
                    </td>
                    <td width="110" >Buyer Name</td>
                    <td width="140" id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 130, "select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,"" ); ?></td>
                    <td width="110">Booking Date</td>
                    <td><input class="datepicker" type="text" style="width:120px" name="txt_booking_date" id="txt_booking_date" onChange="check_exchange_rate();" value="<?=date("d-m-Y"); ?>" disabled /></td>
                </tr>
                <tr>
                    <td>Delivery Date</td>
                    <td><input class="datepicker" type="text" style="width:120px" name="txt_delivery_date" id="txt_delivery_date"/></td>
                    <td class="must_entry_caption">Pay Mode</td>
                    <td><?=create_drop_down( "cbo_pay_mode", 130, $pay_mode,"", 1, "-- Select Pay Mode --", "", "supplier_empty_check();","" ); ?></td>
                    <td>Source</td>
                    <td><?=create_drop_down( "cbo_source", 130, $source,"", 1, "-- Select Source --", "", "","" ); ?></td>
                    <td class="must_entry_caption">Supplier Name</td>
                    <td>
                        <input type="text" name="cbo_supplier_name" id="cbo_supplier_name" class="text_boxes supplier_name" onFocus="auto_completesupplier();" onBlur="supplier_attention_check();" style="width:120px;" placeholder="Write"  />
                        <input type="hidden" class="hdn_supplier_name" id="hidden_supplier_name" name="hidden_supplier_name" />
                        <input type="hidden" id="hidden_supplier_id" name="hidden_supplier_id" style="width:60px;" class="text_boxes"  >
                    </td>
                </tr>
                <tr>
                    <td class="must_entry_caption">Currency</td>
                    <td><?=create_drop_down( "cbo_currency", 130, $currency,"", 1, "-- Select --", 2, "check_exchange_rate();",0 ); ?></td>
                    <td>Exchange Rate</td>
                    <td><input style="width:120px;" type="text" class="text_boxes"  name="txt_exchange_rate" id="txt_exchange_rate" readonly  /></td>
                    <td>Attention</td>
                    <td colspan="3">
                        <input class="text_boxes" type="text" style="width:370px;"  name="txt_attention" id="txt_attention"/>
                        <input type="hidden" class="image_uploader" style="width:60px" value="Lab DIP No" onClick="openmypage('requires/service_booking_controller.php?action=lapdip_no_popup','Lapdip No','lapdip')">
                    </td>
                </tr>
                <tr>
                    <td>Tenor</td>
                    <td><input style="width:120px;" type="text" class="text_boxes_numeric" name="txt_tenor" id="txt_tenor" /></td>
                    <td>Ready To Approved</td>
                    <td><? echo create_drop_down( "cbo_ready_to_approved", 130, $yes_no,"", 1, "-- Select--", 2, "","","" ); ?></td>
                    <td align="right"><b>Copy</b> :
                    <input type="hidden" name="hide_fabric_description" style="width:60px;" id="hide_fabric_description">
                    <input type="checkbox" id="copy_val" name="copy_val" checked/> </td>
                    <td valign="middle"><input type="button" class="image_uploader" style="width:120px" value="ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('txt_booking_no').value,'', 'service_booking', 0 ,1)"></td>
                    <td align="right">&nbsp;</td>
                    <td>
                        <?
                        include("../../terms_condition/terms_condition.php");
                        terms_condition(176,'txt_booking_no','../../');
                        ?>
                    </td>
                </tr>
                <tr>
					<td colspan="2" align="center">
						<span  id="display_app_status" style="color:red;font-size: larger;"></span>
						<input type="hidden" id="txt_app_status" value="0">
					</td>
                    <td align="center" colspan="4" valign="middle" class="button_container">
					<? 
					$date=date('d-m-Y'); 
					echo load_submit_buttons( $permission, "fnc_trims_booking", 0,0 ,"reset_form('servicebooking_1','','booking_list_view','txt_booking_date,".$date."')",1) ; 
					?>
                    <input type="button" value="Send" onClick="fnSendMail('../../','booking_mst_id',1,0,0,0,0);" style="width:80px;" class="formbutton" />
                    </td>
					<td colspan="2"></td>
                </tr>
                <tr>
                    <td align="center" colspan="8">
                        <div id="pdf_file_name"></div>
						<? echo create_drop_down( "cbo_template_id", 85, $report_template_list,'', 0, '', 0, ""); ?>

                        <input type="button" value="Print" onClick="generate_trim_report('show_trim_booking_report');" style="width:80px; display:none" name="print_booking" id="print_booking" class="formbutton" />
                        <input type="button" value="Print 1" onClick="generate_trim_report('show_trim_booking_report1');" style="width:80px;display:none" name="print_booking1" id="print_booking1" class="formbutton" />
                        <input type="button" value="Print 2" onClick="generate_trim_report('show_trim_booking_report2');" style="width:80px; display:none" name="print_booking_2" id="print_booking_2" class="formbutton" />
                        <input type="button" value="Print BPKW" onClick="generate_trim_report('show_trim_booking_bpkw_report');" style="width:80px;display:none" name="print_booking_bpkw" id="print_booking_bpkw" class="formbutton" />
                        <input type="button" value="Print 3" onClick="generate_trim_report('show_trim_booking_report3');" style="width:80px; display:none" name="print_booking_3" id="print_booking_3" class="formbutton" />
						<input type="button" value="Print 4" onClick="generate_trim_report('show_trim_booking_report4');" style="width:80px; display:none" name="print_booking_4" id="print_booking_4" class="formbutton" />
                    </td>
                </tr>
				<tr>
                        <td align="left" id="approval_status" colspan="8" style="color:#F00;"></td>
						
                    </tr>
            </table>
        </fieldset>
    </form>
	<br/>
    <form name="servicebookingknitting_1"  autocomplete="off" id="servicebookingknitting_1">
        <fieldset style="width:1200px;">
            <legend>Service Booking&nbsp;<b style=" margin-left:270px;">&nbsp;Select Po No <input class="text_boxes" type="text" style="width:200px;" placeholder="Double click for Order"  onDblClick="openmypage_order('requires/service_booking_controller.php?action=order_search_popup','Order Search')"   name="txt_order_no" id="txt_order_no"/>
            <input class="text_boxes" type="hidden" style="width:270px;"  name="txt_order_no_id" id="txt_order_no_id"/>
            <input class="text_boxes" type="hidden"   name="service_rate_from" id="service_rate_from" value=""/> </b></legend>
            <table width="900" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td>Process</td>
                    <td id="process_td"><?=create_drop_down( "cbo_process", 172, $conversion_cost_head_array,"", 1, "-- Select --", $selected, "prev_booking(this.value);process_rate_check(this.value);",0,"","","","1,30,31,35" ); ?></td>
                    <td>Sensitivity</td>
                    <td><?=create_drop_down( "cbo_colorsizesensitive", 172, $size_color_sensitive,"", 1, "--Select--", "1", "",$disabled,"" ); ?></td>
                    <td width="130" align="right"><input type="hidden" name="txt_all_update_id"   id="txt_all_update_id" value=""></td>              <!-- 11-00030  -->
                    <td width="170">&nbsp;</td>
                </tr>
                <tr>
                	<td align="center" colspan="6" valign="top" id="booking_list_view1">&nbsp;</td>
                </tr>
                <tr>
                    <td>Fabric Description</td>
                    <td id="fabric_description_td" colspan="5">
                    	<?=create_drop_down( "cbo_fabric_description",650, $blank_array,"", 1, "-- Select --", $selected, "",0 ); ?>
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="6" valign="middle" class="button_container">
                    	<? echo load_submit_buttons( $permission, "fnc_service_booking_dtls", 0,0 ,"",2) ; ?>
                    </td>
                </tr>
            </table>
            <div id="booking_previous_list_view" style="float:right; margin-top:-400px;"></div>
            <div id="booking_list_view"></div>
            <br/>  <br/>
            <div style="" id="data_panel"></div>
            <br/>  <br/>
            <div style="display:none" id="data_panel2"></div>
        </fieldset>
    </form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>