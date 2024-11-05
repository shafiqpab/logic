<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Trims Issue Entry 
Functionality	:	
JS Functions	:
Created by		:	Fuad Shahriar 
Creation date 	: 	22/09/2013
Updated by 		: 	Kausar (Creating Print Report)		
Update date		: 	12-01-2014	   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];

if ($company_id !='') {
    $company_credential_cond = "and comp.id in($company_id)";
}

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Trims Issue Entry", "../../", 1, 1,'','1',''); 

?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	function openmypage_po()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var all_po_id = $('#all_po_id').val();
		var buyer_name=$('#cbo_buyer_name').val();
		var cbo_store_name=$('#cbo_store_name').val();

		if (form_validation('cbo_company_id*cbo_store_name','Company*Store Name')==false)
		{
			return;
		}
			
		var title = 'PO Info';	
		var page_link = 'requires/trims_issue_entry_controller.php?cbo_company_id='+cbo_company_id+'&all_po_id='+all_po_id+'&buyer_name='+buyer_name+'&action=po_search_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var hidden_order_id=this.contentDoc.getElementById("hidden_order_id").value; //Access form field with id="emailfield"
			var hidden_order_no=this.contentDoc.getElementById("hidden_order_no").value; //Access form field with id="emailfield"
			var hide_buyer=this.contentDoc.getElementById("hide_buyer").value; //Access form field with id="emailfield"
			
			if(hidden_order_id!=all_po_id)
			{
				reset_form('','','hidden_prod_id*txt_item_description*save_data*distribution_method_id*txt_issue_qnty*cbo_item_group*txt_item_color*txt_item_color_id*gmts_color_id*txt_item_size*gmts_size_id*txt_brand_supref*txt_rack*txt_shelf*cbo_uom*txt_yet_to_issue*txt_received_qnty*txt_cumulative_issued','','','');
			}
			
			$('#all_po_id').val(hidden_order_id);
			$('#txt_buyer_order').val(hidden_order_no);
			$('#cbo_buyer_name').val(hide_buyer);
			
			show_list_view(hidden_order_id+'****'+cbo_store_name,'create_itemDesc_search_list_view','list_fabric_desc_container','requires/trims_issue_entry_controller','setFilterGrid(\'tbl_list_search\',-1);');
		}
	}
	
	function openpage_booking()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
			
		var title = 'Sample Trims Booking Info';	
		var page_link = 'requires/trims_issue_entry_controller.php?cbo_company_id='+cbo_company_id+'&action=booking_search_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var hidden_booking_id=this.contentDoc.getElementById("hidden_booking_id").value; //Access form field with id="emailfield"
			var hidden_booking_no=this.contentDoc.getElementById("hidden_booking_no").value; //Access form field with id="emailfield"
			var hide_buyer=this.contentDoc.getElementById("hide_buyer").value; //Access form field with id="emailfield"
			
			reset_form('','list_fabric_desc_container','txt_booking_id*txt_booking_no*txt_buyer_order*all_po_id*cbo_buyer_name*txt_issue_qnty*txt_item_description*hidden_prod_id*cbo_item_group*txt_item_color*txt_brand_supref*txt_item_size*txt_shelf','','','');
			$("#tbl_display_info").find('input,select').val('');
			
			
			$('#txt_booking_id').val(hidden_booking_id);
			$('#txt_booking_no').val(hidden_booking_no);
			$('#cbo_buyer_name').val(hide_buyer);
			
			show_list_view(hidden_booking_id,'create_itemDesc_search_list_view_on_booking','list_fabric_desc_container','requires/trims_issue_entry_controller','setFilterGrid(\'tbl_list_search\',0);');
		}
	}
	
	function openmypage_issueQty()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var save_data = $('#save_data').val();
		var all_po_id = $('#all_po_id').val();
		var issueQnty = $('#txt_issue_qnty').val();
		var prod_id = $('#hidden_prod_id').val();
		var cbo_store_name = $('#cbo_store_name').val();
		var distribution_method = $('#distribution_method_id').val();
		var conversion_factor = $('#txt_conversion_faction').val()*1;
		
		
		if (form_validation('cbo_company_id*cbo_store_name*txt_buyer_order*txt_item_description','Company*Store Name*Buyer Order*Item Description')==false)
		{
			return;
		}
			
		var title = 'PO Info';	
		var page_link = 'requires/trims_issue_entry_controller.php?cbo_company_id='+cbo_company_id+'&save_data='+save_data+'&all_po_id='+all_po_id+'&issueQnty='+issueQnty+'&prev_method='+distribution_method+'&prod_id='+prod_id+'&conversion_factor='+conversion_factor+'&cbo_store_name='+cbo_store_name+'&action=po_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var save_string=this.contentDoc.getElementById("save_string").value;	 //Access form field with id="emailfield"
			var tot_trims_qnty=this.contentDoc.getElementById("tot_trims_qnty").value; //Access form field with id="emailfield"
			var all_po_id=this.contentDoc.getElementById("all_po_id").value; //Access form field with id="emailfield"
			var all_po_no=this.contentDoc.getElementById("all_po_no").value; //Access form field with id="emailfield"
			var distribution_method=this.contentDoc.getElementById("distribution_method").value; //Access form field with id="emailfield"
			//alert(all_po_id + "__"+ all_po_no);
			$('#save_data').val(save_string);
			$('#txt_issue_qnty').val(tot_trims_qnty);
			$('#all_po_id').val(all_po_id);
			$('#txt_buyer_order').val(all_po_no);
			$('#distribution_method_id').val(distribution_method);
			$('#cbo_store_name').attr('disabled',true);
			
			if(all_po_id!="")
			{
				//get_php_form_data(all_po_id+"**"+$('#hidden_prod_id').val(), 'get_trim_cum_info', 'requires/trims_issue_entry_controller' );
			}
		}
	}
	
	function generate_report_file(data,action,page)
	{
		window.open("requires/trims_issue_entry_controller.php?data=" + data+'&action='+action, true );
	}
		
	function fnc_trims_issue(operation)
	{
		if(operation==4)
		{
			 var report_title=$( "div.form_caption" ).html();
			 generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val()+'*'+report_title,'trims_issue_entry_print');

			// print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "trims_issue_entry_print", "requires/trims_issue_entry_controller" ) 
			 return;
		}
		else if(operation==5)
		{
			// alert(operation);
			  var report_title=$( "div.form_caption" ).html();
			 generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val()+'*'+report_title,'trims_issue_entry_print2');
			  return;

		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if(operation==2)
			{
				show_msg('13');
				return;
			}
			
			if( form_validation('cbo_company_id*txt_issue_date*cbo_issue_purpose*cbo_basis*cbo_store_name*cbo_sewing_source*cbo_sewing_company','Company*Issue Date*Issue Purpose*Issue Basis*Store Name*Sewing Source*Sewing Company')==false )
			{
				return;
			}
			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_issue_date').val(), current_date)==false)
			{
				alert("Issue Date Can not Be Greater Than Current Date");
				return;
			}	
			
			var issueBasis =$("#cbo_basis").val();
			if(issueBasis==1)
			{
				if( form_validation('txt_buyer_order','Buyer Order')==false )
				{
					return;
				}
			}
			else if(issueBasis==2)
			{
				if( form_validation('txt_booking_no',' Booking No')==false )
				{
					return;
				}
			}
			
			if( form_validation('txt_item_description*txt_issue_qnty','Item Description*Issue Qnty')==false )
			{
				return;
			}
			
			if(($("#txt_issue_qnty").val()*1 > $("#txt_yet_to_issue").val()*1+$("#hidden_issue_qnty").val()*1)) 
			{
				alert("Issue Quantity Excededs Receive Quantity.");
				return;
			}

			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_system_id*cbo_company_id*txt_issue_date*txt_issue_chal_no*cbo_basis*txt_booking_no*txt_booking_id*cbo_store_name*cbo_sewing_source*cbo_sewing_company*cbo_location_name*txt_remarks*cbo_item_group*cbo_uom*txt_item_description*hidden_prod_id*txt_item_color_id*txt_item_size*gmts_color_id*gmts_size_id*txt_brand_supref*txt_rack*txt_shelf*txt_buyer_order*txt_issue_qnty*hidden_issue_qnty*update_id*all_po_id*update_dtls_id*update_trans_id*save_data*previous_prod_id*cbo_issue_purpose*cbo_floor*cbo_sewing_line',"../../");
			
			freeze_window(operation);
			
			http.open("POST","requires/trims_issue_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange =fnc_trims_issue_Reply_info;
		}
	}
	
	function fnc_trims_issue_Reply_info()
	{
		if(http.readyState == 4) 
		{
			 //alert(http.responseText);
			//release_freezing();	return;
			var reponse=trim(http.responseText).split('**');	
				
			show_msg(reponse[0]);
			
			if((reponse[0]==0 || reponse[0]==1))
			{
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_system_id').value = reponse[2];
				$('#cbo_company_id').attr('disabled','disabled');
				
				reset_form('trimsissue_1','','','','','update_id*txt_system_id*cbo_company_id*txt_issue_date*txt_issue_chal_no*cbo_store_name*cbo_sewing_source*cbo_sewing_company*cbo_location_name*all_po_id*txt_buyer_order*cbo_buyer_name*cbo_basis*txt_booking_no*txt_booking_id*txt_remarks*cbo_issue_purpose');
				
				show_list_view(reponse[1],'show_trims_listview','div_details_list_view','requires/trims_issue_entry_controller','');
				show_list_view(reponse[4]+'****'+reponse[5],'create_itemDesc_search_list_view','list_fabric_desc_container','requires/trims_issue_entry_controller','setFilterGrid(\'tbl_list_search\',0);');
				set_button_status(0, permission, 'fnc_trims_issue',1,1);
			}
			else if(reponse[0]==17)
			{
				alert(reponse[1]);
			}
			else if(reponse[0]==11)
			{
				alert(reponse[1]);
			}
			
			release_freezing();	
		}
	}
	
	function openmypage_systemId()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 	
			var page_link='requires/trims_issue_entry_controller.php?cbo_company_id='+cbo_company_id+'&action=trims_issue_popup_search';
			var title='Trims Issue Form';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var trims_issue_id=this.contentDoc.getElementById("hidden_issue_id").value;

				if(trims_issue_id!="")
				{
					freeze_window(5);
					reset_form('trimsissue_1','div_details_list_view*list_fabric_desc_container','','','','cbo_company_id');
					get_php_form_data(trims_issue_id, "populate_data_from_trims_issue", "requires/trims_issue_entry_controller" );
					$('#cbo_store_name').attr('disabled',true);
					show_list_view(trims_issue_id,'show_trims_listview','div_details_list_view','requires/trims_issue_entry_controller','');
					set_button_status(0, permission, 'fnc_trims_issue',1,1);
					release_freezing();
				}
							 
			}
		}
	}
	
	function calculate()
	{
		//amount and book currency calculate--------------//
		var currency_id 	= $("#cbo_currency_id").val();
		var quantity 		= $("#txt_receive_qnty").val();
		var exchangeRate 	= $("#txt_exchange_rate").val();
		var rate			= $('#txt_rate').val();	 
		var ile_cost 		= $("#txt_ile").val();
		var amount 			= quantity*1*(rate*1+ile_cost*1); 
		var bookCurrency 	= (rate*1+ile_cost*1)*exchangeRate*1*quantity*1;
		$("#txt_amount").val(number_format_common(amount,"","",currency_id));
		$("#txt_book_currency").val(number_format_common(bookCurrency,"","",1));
	}
	
	function openmypage_goodsPlacement()
	{
		var update_dtls_id = $('#update_dtls_id').val();
		
		if (form_validation('update_dtls_id','Save First')==false)
		{
			alert('Please Save First.');
			return;
		}
		else
		{ 	
			var page_link='requires/trims_issue_entry_controller.php?update_dtls_id='+update_dtls_id+'&action=goods_placement_popup';
			var title='Goods Placement Entry Form';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','../');
			
		}
	}
	
	function set_form_data(data)
	{
		var data=data.split("**");
		$('#hidden_prod_id').val(data[0]);
		$('#cbo_item_group').val(data[1]);
		$('#txt_item_description').val(data[2]);
		$('#txt_item_color').val(data[3]);
		$('#gmts_color_id').val(data[4]);
		$('#txt_item_size').val(data[5]);
		$('#gmts_size_id').val(data[6]);
		$('#txt_brand_supref').val(data[7]);
		$('#cbo_uom').val(data[8]);
		$('#txt_conversion_faction').val(data[16]);
		$('#txt_rack').val(data[9]);
		$('#txt_shelf').val(data[10]);
		$('#txt_item_color_id').val(data[11]);
		$('#txt_global_stock').val(data[12]);
		$('#txt_cumulative_issued').val(data[13]);
		$('#txt_yet_to_issue').val(data[14]);
		$('#txt_received_qnty').val(data[15]);
		
		var issueBasis =$("#cbo_basis").val();
		var txt_booking_id = $('#txt_booking_id').val();
		if(issueBasis==2 && txt_booking_id!="")
		{
			get_php_form_data(txt_booking_id+"**"+$('#hidden_prod_id').val(), 'get_trim_cum_info_for_trims_booking', 'requires/trims_issue_entry_controller' );
		}
	}
	
	function enable_disable()
	{
		var issueBasis =$("#cbo_basis").val();
		reset_form('','list_fabric_desc_container','txt_booking_id*txt_booking_no*txt_buyer_order*all_po_id*cbo_buyer_name*txt_issue_qnty*txt_item_description*hidden_prod_id','','','');
		$("#tbl_display_info").find('input,select').val('');
		
		$('#txt_issue_qnty').attr('readonly','readonly');
		$('#txt_issue_qnty').attr('onClick','openmypage_issueQty();');	
		$('#txt_issue_qnty').attr('placeholder','Single Click');
		
		if(issueBasis==2)	
		{
			$("#txt_booking_no").removeAttr("disabled");
			$("#txt_buyer_order").attr("disabled",true);
			$('#txt_issue_qnty').removeAttr('readonly','readonly');
			$('#txt_issue_qnty').removeAttr('onClick','onClick');
			$('#txt_issue_qnty').attr('placeholder','Write');			
		}
		else
		{	
			$("#txt_buyer_order").removeAttr("disabled");
			$("#txt_booking_no").attr("disabled",true);	
		}
	}
	
	function load_location(sewing_company)
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_sewing_source = $('#cbo_sewing_source').val();
		var cbo_sewing_company = $('#cbo_sewing_company').val();
	//	alert(cbo_sewing_source);
		if(cbo_sewing_source==1)
		{
			load_drop_down( 'requires/trims_issue_entry_controller',sewing_company, 'load_drop_down_location', 'location_td' );
		}
		else
		{
			load_drop_down( 'requires/trims_issue_entry_controller',cbo_company_id, 'load_drop_down_location', 'location_td' );
		}
	}
</script>
<body onLoad="set_hotkey()">
<div style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission); ?><br />    		 
    <form name="trimsissue_1" id="trimsissue_1" autocomplete="off" >
    <div style="width:680px;float:left; margin-left:10px">   
        <fieldset style="width:680px;">
        <legend>Trims Issue Entry</legend>
        <br>
        	<fieldset style="width:680px;">
                <table width="678" cellspacing="2" cellpadding="0" border="0" id="tbl_master" align="center">
                    <tr>
                        <td colspan="3" align="right"><strong>Issue No</strong></td>
                        <td colspan="3" align="left">
                        	 <input type="hidden" name="update_id" id="update_id" />
                            <input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_systemId();" readonly />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="95" class="must_entry_caption"> Company </td>
                        <td width="150">
                            <? 
                                echo create_drop_down( "cbo_company_id", 132, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond $company_credential_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down( 'requires/trims_issue_entry_controller',this.value, 'load_drop_down_store', 'store_td' );load_drop_down( 'requires/trims_issue_entry_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                            ?>
                        </td>
                        <td width="98" class="must_entry_caption"> Issue Date </td>
                        <td width="143">
                            <input class="datepicker" type="date" style="width:120px" name="txt_issue_date" id="txt_issue_date"/>
                        </td>
                        <td width="90" class="must_entry_caption">Issue Purpose</td>
                        <td>
                            <? 
                                echo create_drop_down( "cbo_issue_purpose", 132, $yarn_issue_purpose,"", 1, "-- Select --", 36, "load_drop_down( 'requires/trims_issue_entry_controller', document.getElementById('cbo_location_name').value+'_'+document.getElementById('cbo_sewing_company').value+'_'+document.getElementById('cbo_sewing_source').value+'_'+this.value, 'load_drop_down_floor', 'floor_td');","","3,4,8,26,29,30,36,37,41,42" );
                            ?>
                       </td>
                    </tr> 
                    <tr>
                        <td width="100">Issue Challan No </td>
                        <td>
                            <input type="text" name="txt_issue_chal_no" id="txt_issue_chal_no" class="text_boxes" style="width:120px" >
                        </td>
                        <td class="must_entry_caption">Issue Basis</td>
                        <td>
                            <? 
                                $trims_issue_basis=array(1=>"With Order",2=>"Without Order");
                                echo create_drop_down( "cbo_basis", 132, $trims_issue_basis,"",0, "-- Select Basis --", $selected, "enable_disable();", "", "");
                            ?>
                        </td>
                        <td>Booking No</td>
                        <td>
                            <input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:120px"  placeholder="Double Click to Search" onDblClick="openpage_booking();" readonly disabled />
                            <input type="hidden" name="txt_booking_id" id="txt_booking_id" />
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Store Name </td>
                        <td id="store_td">
                            <?
                                echo create_drop_down( "cbo_store_name", 132, $blank_array,"",1, "--Select store--", 1, "" );
                            ?>
                        </td> 
                        
                        <td class="must_entry_caption">Sewing Source </td>
                        <td>
                            <?
                                echo create_drop_down("cbo_sewing_source", 132, $knitting_source,"", 1, "-- Select --", 0,"load_drop_down( 'requires/trims_issue_entry_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_sewing_com','sewing_com');",0,'1,3');
                            ?>
                        </td>
                        <td class="must_entry_caption">Sewing Comp.</td>
                        <td id="sewing_com">
                            <?
                                echo create_drop_down( "cbo_sewing_company", 132, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "--Select Sewing Company--", "", "load_drop_down( 'requires/trims_issue_entry_controller', this.value, 'load_drop_down_location', 'location_td' );","" );
                                //echo create_drop_down( "cbo_sewing_company", 132, $blank_array,"",1, "--Select Sewing Company--", 1, "" );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Location</td>                                              
                        <td id="location_td">
                            <? //load_drop_down( 'requires/trims_issue_entry_controller', this.value, 'load_drop_down_floor', 'floor_td' );
                                echo create_drop_down( "cbo_location_name", 132, $blank_array,"", 1, "-- Select Location --", 0, "" );
                            ?>
                        </td>
                        <td>Remarks</td>
                        <td colspan="5"><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:355px" ></td>
                    </tr>
                </table>
            </fieldset>
            <br>
            <table width="680" cellspacing="2" cellpadding="2" border="0" id="tbl_dtls" align="center">
                <tr>
                    <td width="65%" valign="top">
                        <fieldset>
                        <legend>New Entry</legend>
                            <table cellpadding="0" cellspacing="2" width="100%">
                                <tr>
                                    <td class="must_entry_caption" width="90">Buyer Order</td>
                                    <td>
                                        <input type="text" name="txt_buyer_order" id="txt_buyer_order" class="text_boxes" style="width:135px;" onDblClick="openmypage_po()" placeholder="Double click to search" readonly/>	
                                    </td>
                                    <td width="90">UOM</td>
                                    <td>
                                        <?
                                            echo create_drop_down( "cbo_uom", 100, $unit_of_measurement,"", 1, "-- UOM --", '0', "",1 );
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Item Group</td>
                                    <td>
                                    <?
                                        echo create_drop_down( "cbo_item_group", 146, "select id,item_name from lib_item_group where item_category=4 and status_active=1 and is_deleted=0 order by item_name", "id,item_name", 1, "-- Select --", 0,  "",1 );
                                    ?>	
                                    </td>
                                    <td>Brand/Sup Ref</td>
                                    <td>
                                        <input type="text" name="txt_brand_supref" id="txt_brand_supref" class="text_boxes" style="width:90px" disabled/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="must_entry_caption">Item Desc.</td>
                                    <td>
                                        <input type="text" name="txt_item_description" id="txt_item_description" class="text_boxes" style="width:135px" disabled readonly />
                                        <input type="hidden" name="hidden_prod_id" id="hidden_prod_id" disabled/>
                                    </td>
                                    <td><!--Gmts-->Item Size</td>   
                                    <td>
                                        <input type="text" name="txt_item_size" id="txt_item_size" class="text_boxes" style="width:90px;" disabled/>
                                        <input type="hidden" name="gmts_size_id" id="gmts_size_id" class="text_boxes" style="width:100px;" disabled/>
                                    </td>
                              </tr> 
                                <tr>
                                    <td><!--Gmts--> Item Color</td>
                                    <td>
                                        <input type="text" name="txt_item_color" id="txt_item_color" class="text_boxes" style="width:135px;" disabled/>
                                        <input type="hidden" name="gmts_color_id" id="gmts_color_id" class="text_boxes" style="width:100px;" disabled/>
                                        <input type="hidden" name="txt_item_color_id" id="txt_item_color_id" readonly>
                                    </td>
                                    <td class="must_entry_caption">Issue Qnty</td>
                                    <td>
                                        <input type="text" name="txt_issue_qnty" id="txt_issue_qnty" class="text_boxes_numeric" style="width:90px;" onClick="openmypage_issueQty()"placeholder="Single Click" readonly/>	
                                    </td>
                                </tr>
                                <tr>
                                    <td>Rack</td>
                                    <td>
                                        <input type="text" name="txt_rack" id="txt_rack" class="text_boxes" style="width:135px" placeholder="Display" disabled>
                                    </td>
                                    <td>Shelf</td>
                                    <td>
                                        <input type="text" name="txt_shelf" id="txt_shelf" class="text_boxes_numeric" style="width:90px" placeholder="Display" disabled>
                                    </td>
                                </tr>
                                
                                <tr>
                                
                                </tr>
                                     <td>Floor</td>
                                     <td id="floor_td">
                                     <?php
									 //get_php_form_data(document.getElementById('cbo_source').value,'line_disable_enable','requires/trims_issue_entry_controller'); load_drop_down( 'requires/trims_issue_entry_controller', this.value+'_'+document.getElementById('cbo_location').value'load_drop_down_sewing_line_floor', 'sewing_line_td' );
	//echo create_drop_down( "cbo_floor", 146, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0  order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );
	   echo create_drop_down( "cbo_floor", 146, $blank_array,"", 1, "-- Select Floor --", 0, "" ); 
?>
                                     </td>
                                     
                                    <td>Sewing Line No</td> 
                                    <td id="sewing_line_td" colspan="2">            
                                        <?
                                            echo create_drop_down( "cbo_sewing_line", 100, $blank_array,"", 1, "Select Line", $selected, "",1,0 );		
                                        ?>	
                                    </td> 
                                <tr>
                                	<td align="right" colspan="3" style="display:none"><input type="button" class="formbuttonplasminus" style="width:150px" value="Goods Placement" onClick="openmypage_goodsPlacement();"></td> 
                                </tr> 
                             </table>
                        </fieldset>
					</td>
                    <td width="2%" valign="top"></td>
					<td width="33%" valign="top">
						<fieldset>
                        <legend>Display</legend>					
                            <table id="tbl_display_info"  cellpadding="0" cellspacing="1" width="100%" >				
                                <tr>
                                    <td>Recv. Qty</td>						
                                	<td>
                                    	<input type="text" name="txt_received_qnty" id="txt_received_qnty" class="text_boxes_numeric" placeholder="Display" style="width:90px" disabled />
                                    </td>
								</tr>
                                <tr>
                                    <td>Cumul. Issued</td>
                                    <td><input type="text" name="txt_cumulative_issued" id="txt_cumulative_issued" class="text_boxes_numeric" placeholder="Display" style="width:90px" disabled /></td>
                                </tr>
                                <tr>
                                    <td>Yet to Issue</td>
                                    <td><input type="text" name="txt_yet_to_issue" id="txt_yet_to_issue" class="text_boxes_numeric" placeholder="Display" style="width:90px" disabled /></td>
                                </tr>					
                               	<tr>
                                    <td>Buyer</td>						
                                    <td>
                                    <?
										echo create_drop_down( "cbo_buyer_name", 101, "select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, " Display ", 0, "",1 );
									?>
                                    </td>
                                </tr>	
                                <tr>
                                    <td>Global Stock</td>
                                    <td><input type="text" name="txt_global_stock" id="txt_global_stock" placeholder="Display" class="text_boxes" style="width:90px" disabled /></td>
                                </tr>							
                            </table>                  
                       </fieldset>	
              		</td>
				</tr>
                <tr>
                    <td align="center" colspan="3" class="button_container" width="100%">
                        <?
                            echo load_submit_buttons($permission, "fnc_trims_issue", 0,1,"reset_form('trimsissue_1','div_details_list_view*list_fabric_desc_container','','','disable_enable_fields(\'cbo_company_id\');')",1);
                        ?>
                        <input id="Print1" type="button" class="formbutton" style="width:80px" onClick="fnc_trims_issue(5)" name="print" value="Print 2">
                        <input type="hidden" name="save_data" id="save_data" readonly>
                        <input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
                        <input type="hidden" name="update_trans_id" id="update_trans_id" readonly>
                        <input type="hidden" name="previous_prod_id" id="previous_prod_id" readonly>
                        <input type="hidden" name="all_po_id" id="all_po_id" readonly>
                        <input type="hidden" name="hidden_issue_qnty" id="hidden_issue_qnty" readonly>
                        <input type="hidden" name="distribution_method_id" id="distribution_method_id" readonly />
                        <input type="hidden" name="txt_conversion_faction" id="txt_conversion_faction" />
                    </td>
                </tr>
			</table>
            <div style="width:680px;" id="div_details_list_view"></div>
		</fieldset>
	</div>
    <div id="list_fabric_desc_container" style="width:595px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:15px"></div>
	</form>
</div>   
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	var company_id=$('#cbo_company_id').val();
	if(company_id!=0)
	{
		$('#cbo_sewing_source').val(1);
		$('#cbo_sewing_company').val(company_id);
		var length=$("#cbo_location_name option").length;
		if(length==2)
		{
			$('#cbo_location_name').val($('#cbo_location_name option:last').val());
		}
	}
</script>
</html>