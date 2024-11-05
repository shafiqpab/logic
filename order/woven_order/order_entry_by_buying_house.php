<?
/*--------------------------------------------Comments----------------
Version (MySql)          :  V2
Version (Oracle)         :  V1
Functionality	         :
JS Functions	         :
Created by		         :	Md Mamun Ahmed Sagor
Creation date 	         : 	05-11-2023
Requirment Client        :
DB Script                :
Updated by 		         :
Update date		         :
QC Performed BY	         :
QC Date			         :
Comments		         :From this version oracle conversion is start
----------------------------------------------------------------------*/
session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');
//echo $_SESSION['logic_erp']['mandatory_field'][163][1].'=AAAAAAAAA';
$user_id=$_SESSION['logic_erp']['user_id'];

$teamIdSql=sql_select("select a.id from lib_marketing_team a,lib_mkt_team_member_info b where a.id=b.team_id and a.project_type=1 and a.status_active =1 and a.is_deleted=0 and b.user_tag_id =$user_id group by a.id order by a.id");
$teamId=$teamIdSql[0][csf('id')];
if($teamId==''){
	$team_group_data=sql_select("select id,team_leader_name from lib_marketing_team where project_type=1 and status_active =1 and is_deleted=0 order by team_leader_name");
	if(count($team_group_data)==1){
		$teamId=$team_group_data[0][csf('id')];
	}
}

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Order Info","../../", 1, 1, $unicode,1,'');

$qcCons_from=return_field_value("excut_source","variable_order_tracking","excut_source=2 and variable_list=68 and is_deleted=0 and status_active=1 order by id","excut_source");

?>
<script>
	var permission='<? echo $permission; ?>';
	var user='<? echo $_SESSION['logic_erp']['user_id']; ?>';
	var styleBrowse='<? echo $qcCons_from; ?>';
	var mst_mandatory_field="";
	var mst_mandatory_message="";
	var dtls_mandatory_field="";
	var dtls_mandatory_message="";
	var field_level_data="";

	//alert(styleBrowse);
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	<?
	if(isset($_SESSION['logic_erp']['data_arr'][163]))
	{
		$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][163] );
		echo "var field_level_data= ". $data_arr . ";\n";
	}
	


	$field_mst='';
	$message_mst='';
	$i=0;
	foreach($_SESSION['logic_erp']['mandatory_field'][163] as $key=>$value){
		
		if($key==3 || $key==4 || $key==5 || $key==6 || $key==9 || $key==10 || $key==14){
		
				if($i==0){
					$field_mst.=$value;
					$message_mst.=$value;
					
				}else{
					$field_mst.='*'.$value;
					$message_mst.='*'.$value;
				}
				$i++;
		}
	}
	echo "var mst_mandatory_field = '". ($field_mst) . "';\n";
	echo "var mst_mandatory_message = '". ($message_mst) . "';\n";

	
	


	 
	?>
	//alert(dtls_mandatory_field);
	// Master Form-----------------------------------------------------------------------------
    function internal(ref)
    {
        // alert(ref);
        var internal_ref = [];
        var int_ref=ref.split(",");
        for(var i=0; i<int_ref.length; i++)
        {
            //alert(int_ref[i].replace(/\"/g,''));
            internal_ref[i]= int_ref[i].replace(/\"/g,'');
        }
        $("#txt_grouping").autocomplete({
            source: internal_ref
        });
    }

    var str_port_of_loading = [<? echo substr(return_library_autocomplete("select distinct(port_of_loading) from com_export_lc", "port_of_loading"), 0, -1); ?>];
    var str_port_of_discharge = [<? echo substr(return_library_autocomplete("select distinct(port_of_discharge) from com_export_lc", "port_of_discharge"), 0, -1); ?>];
    var str_inco_term_place = [<? echo substr(return_library_autocomplete("select distinct(inco_term_place) from com_export_lc", "inco_term_place"), 0, -1); ?>];

    $(document).ready(function (e)
    {
        $("#txt_port_of_loading").autocomplete({
            source: str_port_of_loading
        });
        $("#txt_port_of_discharge").autocomplete({
            source: str_port_of_discharge
        });
        $("#txt_inco_term_place").autocomplete({
            source: str_inco_term_place
        });

    });
 
	function openmypage(page_link,title)
	{
		var garments_nature=document.getElementById('garments_nature').value;
		page_link=page_link+'&garments_nature='+garments_nature;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=450px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_job");
			if (theemail.value!="")
			{
				freeze_window(5);
				reset_form('','','txt_po_no*txt_po_received_date*txt_pub_shipment_date*txt_org_shipment_date*txt_po_quantity*txt_avg_price*txt_amount*txt_excess_cut*txt_plan_cut*cbo_status*txt_details_remark*cbo_delay_for*show_textcbo_delay_for*cbo_packing_po_level*update_id_details*color_size_break_down*hidden_po_qty','','');
				get_php_form_data(theemail.value, "populate_data_from_search_popup", "requires/order_entry_by_buying_house_controller" );
				show_list_view(theemail.value,'show_po_active_listview','po_list_view','requires/order_entry_by_buying_house_controller','');
				$('#cbo_company_name').attr('disabled',true);
				//$('#th_color input,#th_color select').css('background-color', '');
				 //document.getElementById('po_msg').innerHTML='';
				//show_list_view(theemail.value,'show_deleted_po_active_listview','deleted_po_list_view','../woven_order/requires/order_entry_by_buying_house_controller','');
				set_button_status(1, permission, 'fnc_order_entry',1);
				set_button_status(0, permission, 'fnc_order_entry_details',2);
				//load_drop_down( 'requires/order_entry_by_buying_house_controller', theemail.value, 'load_drop_down_projected_po', 'projected_po_td' )
				release_freezing();
			}
		}
	}
	
	
	function openmypage_file_no()
	{
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		//var txt_style_ref=document.getElementById('txt_style_ref').value;
		var title ='File No';
		//var action=repeat_job_popup;
		var page_link='requires/order_entry_by_buying_house_controller.php?action=file_no_popup&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name; 
		//page_link=action=repeat_job_popup+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name+'&txt_style_ref='+txt_style_ref;

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=450px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_file_no");

			freeze_window(5);
		    //var repeat_job=this.contentDoc.getElementById("selected_job");
			document.getElementById('txt_file_no').value=theemail.value;
			release_freezing();
		}
	}
	

	function repeat_openmypage(page_link,title)
	{
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var txt_style_ref=document.getElementById('txt_style_ref').value;
		page_link=page_link+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name+'&txt_style_ref='+txt_style_ref;

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=450px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_job");

			freeze_window(5);
		    //var repeat_job=this.contentDoc.getElementById("selected_job");
			document.getElementById('txt_repeat_job_no').value=theemail.value;
			release_freezing();
		}
	}

	function open_qoutation_popup(page_link,title)
	{
		if( form_validation('cbo_company_name*cbo_buyer_name','Company Name*Buyer Name')==false)
		{
			return;
		}
		else
		{
			var txt_quotation_id= document.getElementById('txt_quotation_id').value;
			if(txt_quotation_id!='')
			{
				var r=confirm('Quotation Id :'+txt_quotation_id+" Already Attached With This Job.\n If You want to Replace It Press OK \n After replace your SMV Will Remove.  ");
				if(r==false) return;
				else { }
			}

			var cbo_company_name=document.getElementById('cbo_company_name').value;
			var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
			var set_smv_id=document.getElementById('set_smv_id').value;
			
			page_link=page_link+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name+'&set_smv_id='+set_smv_id;

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("selected_id");
				if (theemail.value!="")
				{
					freeze_window(5);
					document.getElementById('tot_smv_qnty').value='';
					var cost_source=$('#hid_cost_source').val();
					if(set_smv_id==3 || set_smv_id==8 || set_smv_id==9)
					{
						$('#txt_style_ref').val(theemail.value);
					}
					else
					{
						if(cost_source==2)
						{
							get_php_form_data( theemail.value, "populate_data_from_search_popup_quotation", "requires/order_entry_by_buying_house_controller" );
							$('#cbo_order_uom').attr('disabled',true);
						}
						else if (cost_source==1)
						{
							get_php_form_data( theemail.value, "populate_data_from_search_popup_qc", "requires/order_entry_by_buying_house_controller" );
						}
						location_select();
					}
					release_freezing();
				}
			}
		}
	}

	function location_select()
	{
		if($('#cbo_location_name option').length==2)
		{
			if($('#cbo_location_name option:first').val()==0)
			{
				$('#cbo_location_name').val($('#cbo_location_name option:last').val());
				eval($('#cbo_location_name').attr('onchange'));
			}
		}
		else if($('#cbo_location_name option').length==1)
		{
			$('#cbo_location_name').val($('#cbo_location_name option:last').val());
			eval($('#cbo_location_name').attr('onchange'));
		}
	}

	function open_set_popup(unit_id,texboxid)
	{
		var	pcs_or_set="";
		var txt_job_no=document.getElementById('txt_job_no').value;
		var set_smv_id=document.getElementById('set_smv_id').value;
		var txt_style_ref=document.getElementById('txt_style_ref').value;
		var set_breck_down=document.getElementById('set_breck_down').value;
		var tot_set_qnty=document.getElementById('tot_set_qnty').value;
		var tot_smv_qnty=document.getElementById('tot_smv_qnty').value;
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		if(form_validation('cbo_buyer_name*txt_style_ref','Buyer*Style')==false)
		{
			return;
		}

		if(trim(txt_job_no)!='')
		{
			var precost = return_ajax_request_value(txt_job_no, 'check_precost', 'requires/order_entry_by_buying_house_controller');
		}
		else var precost=0+'_'+unit_id+'_'+0;

		var data=precost.split("_");
		if(data[2]>0){
			alert("Pre Cost Approved, Any Change not allowed.");
			document.getElementById('cbo_order_uom').value=data[1];
			//return;
		}
		else if(data[0]>0 && texboxid=='cbo_order_uom'){
			alert("Pre Cost Found, UOM Change not allowed");
			document.getElementById('cbo_order_uom').value=data[1];
			return;
		}
		else if (data[0]>0 && texboxid=='set_button'){
			alert("Pre Cost Found, only Sew. and Cut. SMV Change allowed");
			//document.getElementById('cbo_order_uom').value=data[1];
		}

		if(unit_id==58) pcs_or_set="Item Details For Set";
		if(unit_id==57) pcs_or_set="Item Details For Pack";
		else pcs_or_set="Item Details For Pcs";

		var page_link="requires/order_entry_by_buying_house_controller.php?txt_job_no="+trim(txt_job_no)+"&action=open_set_list_view&set_breck_down="+set_breck_down+"&tot_set_qnty="+tot_set_qnty+'&unit_id='+unit_id+'&tot_smv_qnty='+tot_smv_qnty+'&precostfound='+data[0]+'&precostapproved='+data[2]+'&set_smv_id='+set_smv_id+'&txt_style_ref='+txt_style_ref+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, pcs_or_set, 'width=1350px,height=300px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var set_breck_down=this.contentDoc.getElementById("set_breck_down");
			var item_id=this.contentDoc.getElementById("item_id");
			var tot_set_qnty=this.contentDoc.getElementById("tot_set_qnty");
			var tot_smv_qnty=this.contentDoc.getElementById("tot_smv_qnty");
			document.getElementById('set_breck_down').value=set_breck_down.value;
			document.getElementById('item_id').value=item_id.value;
			document.getElementById('tot_set_qnty').value=tot_set_qnty.value;
			document.getElementById('tot_smv_qnty').value=tot_smv_qnty.value;
		}
	}

	function fnc_order_entry( operation )
	{
		//alert(operation);return;
		if(operation==2)
		{
			alert("Delete Restricted")
			return;
		}
		var is_season_must=$('#is_season_must').val()*1;
		if(is_season_must==1)
		{
			var testoptionlength = $("#cbo_season_name option").length-1;
			//alert(testoptionlength);
			if(testoptionlength>0) {
				if(form_validation('cbo_season_name','Select Season')==false)
				{
					return;
				}
			}
		}
	//	alert(mst_mandatory_field);
		if(mst_mandatory_field)
		{
			if (form_validation(mst_mandatory_field,mst_mandatory_message)==false)
			{
				release_freezing();
				return;
			}
		}
		if(operation==1)
		{
			var po_id="";
			var txt_job_no=document.getElementById('txt_job_no').value;
			if($('#cbo_order_status').val()==1) //issue id 12070
			{
				var booking_no_with_approvet_status = return_ajax_request_value(txt_job_no+"_"+po_id, 'booking_no_with_approved_status', 'requires/order_entry_by_buying_house_controller')
				var booking_no_with_approvet_status_arr=booking_no_with_approvet_status.split("_");
				if(trim(booking_no_with_approvet_status_arr[0]) !="")
				{
					var al_magg="Main Fabric Approved Booking No "+booking_no_with_approvet_status_arr[0];
					if(booking_no_with_approvet_status_arr[1] !="")
					{
						al_magg+=" and Un-Approved Booking No "+booking_no_with_approvet_status_arr[1];
					}
					al_magg+=" found,\nPlease Un-approved the booking first";
					alert(al_magg)
					return;
				}
	
				if(trim(booking_no_with_approvet_status_arr[1]) !="")
				{
					var al_magg=" Main Fabric Un-Approved Booking No "+booking_no_with_approvet_status_arr[1]+" Found\n If you update this job\n You have to update color size break down, Pre-cost and booking against this Job ";
					var r=confirm(al_magg);
					if(r==false) return;
				}
			}
		}
		var sewing_company_validate_id=document.getElementById('sewing_company_validate_id').value*1;
		if(sewing_company_validate_id==1)
		{
			if (form_validation('cbo_working_company_id*cbo_working_location_id','Working Company*Working Location')==false){
				release_freezing();
				return;
			}
		}
		if (form_validation('cbo_company_name*cbo_location_name*cbo_buyer_name*txt_style_ref*cbo_product_department*txt_item_catgory*cbo_dealing_merchant*cbo_brand_id*item_id','Company Name*Location Name*Buyer Name*Style Ref*Product Department*Item Catagory*Dealing Merchandiser*Brand*Item Details')==false)
		{
			return;
		}
		else
		{
			var is_season_must=$('#is_season_must').val()*1;
			if(is_season_must==1)
			{
				if($('#cbo_season_name').val()==0)
				{
					alert('Season not blank.');
					$('#cbo_season_name').focus();
					return;
				}
			}		

			var data="action=save_update_delete_mst&operation="+operation+get_submitted_data_string('txt_job_no*hidd_job_id*garments_nature*cbo_company_name*cbo_location_name*cbo_buyer_name*txt_style_ref*txt_style_description*cbo_product_department*txt_product_code*cbo_sub_dept*cbo_currercy*cbo_agent*cbo_client*txt_repeat_no*cbo_region*txt_item_catgory*cbo_team_leader*cbo_dealing_merchant*cbo_packing*txt_remarks*cbo_ship_mode*cbo_order_uom*item_id*set_breck_down*tot_set_qnty*tot_smv_qnty*txt_quotation_id*update_id*cbo_season_name*cbo_factory_merchant*txt_bhmerchant*txt_repeat_job_no*set_smv_id*cbo_working_company_id*cbo_working_location_id*cbo_design_source_id*cbo_qltyLabel*cbo_brand_id*cbo_season_year*txt_requision_no*cbo_fab_material*sustainability_standard*cbo_quality_level*txt_composition*txt_port_of_discharge*txt_head_merchandiser*cbo_ready_to_approved*txt_port_of_loading*cbo_pay_term*cbo_ls_sc*txt_tenor*cbo_inco_term*txt_inco_term_place',"../../");
			//alert(data)
			freeze_window(operation);
			http.open("POST","requires/order_entry_by_buying_house_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_on_submit_reponse_mst;
		}
	}

	function fnc_on_submit_reponse_mst()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]=="SMV"){
				alert("Insert SMV in Item Pop-Up")
				release_freezing();
				return;
			}
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not  Approved. Please Approved the Quotation");
				release_freezing();
				return;
			}

			if(reponse[0]==16)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}

			var alt_booking_msg="Delete Restricted, Booking Found, Booking No: "+reponse[0];
			if(reponse[0]==13)
			{
				alert(alt_booking_msg);
				release_freezing();
				return;
			}

			if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1)
			{
				document.getElementById('txt_job_no').value=reponse[1];
				document.getElementById('update_id').value=reponse[1];
				document.getElementById('hidd_job_id').value=reponse[3];
				document.getElementById('set_pcs').value=document.getElementById('cbo_order_uom').value
				document.getElementById('set_unit').value=document.getElementById('cbo_currercy').value
				set_button_status(1, permission, 'fnc_order_entry',1);
			}
			show_msg(trim(reponse[0]));
			release_freezing();
		}
	}
	// Master Form End -----------------------------------------------------------------------------

	//Dtls Form-------------------------------------------------------------------------------------

	function openmypage_for_po_copy(page_link,title)
	{
		var garments_nature=document.getElementById('garments_nature').value;
		var txt_job_no=document.getElementById('txt_job_no').value;
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		//alert(cbo_company_name)
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var txt_style_ref=document.getElementById('txt_style_ref').value;
		page_link=page_link+'&garments_nature='+garments_nature+'&txt_job_no='+txt_job_no+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name+'&txt_style_ref='+txt_style_ref;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("po_id");
			if (theemail.value!="")
			{
				freeze_window(5);
				 get_details_form_data(theemail.value,'populate_order_details_form_data','requires/order_entry_by_buying_house_controller')
				 set_button_status(0, permission, 'fnc_order_entry_details',2);
				 document.getElementById('txt_po_no').value='';
				release_freezing();
			}
		}
	}

	function open_color_size_popup(page_link,title)
	{
		var with_qty=document.getElementById('with_qty').value;

		if((document.getElementById('txt_po_quantity').value=="" || document.getElementById('txt_po_quantity').value==0) && with_qty==1)
		{
			alert('Please enter valid order quantity');
			$('#txt_po_quantity').focus();
			return false;
		}
		if(document.getElementById('update_id_details').value=="" || document.getElementById('update_id_details').value==0 )
		{
		   alert('Please Save The Po first.');
			return false;
		}
		else
		{
			var update_id_details=document.getElementById('update_id_details').value;
			var txt_po_no=document.getElementById('txt_po_no').value;
			var txt_po_quantity=document.getElementById('txt_po_quantity').value;
			var set_breck_down=document.getElementById('set_breck_down').value;
			var item_id=document.getElementById('item_id').value;
			var tot_set_qnty=document.getElementById('tot_set_qnty').value;
			var cbo_order_uom=document.getElementById('cbo_order_uom').value;
			var color_size_break_down=document.getElementById('color_size_break_down').value;
			var txt_avg_price =document.getElementById('txt_avg_price').value;
			var txt_excess_cut=document.getElementById('txt_excess_cut').value;
			var cbo_company_name=document.getElementById('cbo_company_name').value;
			var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
			var txt_org_shipment_date=document.getElementById('txt_org_shipment_date').value;
			var txt_pub_shipment_date=document.getElementById('txt_pub_shipment_date').value;
			var cbo_packing_po_level=document.getElementById('cbo_packing_po_level').value;
			var cbo_order_status=document.getElementById('cbo_order_status').value;
			var hidd_job_id=document.getElementById('hidd_job_id').value;
			var working_company_id=document.getElementById('cbo_working_company_id').value;
			var working_location_id=document.getElementById('cbo_working_location_id').value;
			var cbo_status=document.getElementById('cbo_status').value;
			var tot_smv_qnty=document.getElementById('tot_smv_qnty').value;

			var page_link=page_link+'&data='+update_id_details+'&txt_po_quantity='+txt_po_quantity+'&set_breck_down='+set_breck_down+'&item_id='+item_id+'&tot_set_qnty='+tot_set_qnty+'&cbo_order_uom='+cbo_order_uom+'&color_size_break_down='+color_size_break_down+'&txt_po_no='+txt_po_no+'&txt_avg_price='+txt_avg_price+'&txt_excess_cut='+txt_excess_cut+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name+'&txt_org_shipment_date='+txt_org_shipment_date+'&txt_pub_shipment_date='+txt_pub_shipment_date+'&cbo_packing_po_level='+cbo_packing_po_level+'&with_qty='+with_qty+'&cbo_order_status='+cbo_order_status+'&hidd_job_id='+hidd_job_id+'&working_company_id='+working_company_id+'&working_location_id='+working_location_id+'&cbo_status='+cbo_status+'&tot_smv_qnty='+tot_smv_qnty;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1450px,height=550px,center=1,resize=1,scrolling=0','../../')

			emailwindow.onclose=function()
			{
				var tot_set_qnty=(document.getElementById('tot_set_qnty').value)*1;
				var cbo_order_uom=document.getElementById('cbo_order_uom').value;
				var txt_avg_rate=this.contentDoc.getElementById("txt_tot_avg_rate");
				var txt_total_amt=this.contentDoc.getElementById("txt_tot_amount");
				var txt_avg_excess_cut=this.contentDoc.getElementById("txt_tot_excess_cut");
				var txt_total_plan_cut=this.contentDoc.getElementById("txt_tot_plancut");
				var txt_tot_po_qnty=this.contentDoc.getElementById("txt_tot_po_qnty");
				var with_qty_pop=this.contentDoc.getElementById("with_qty");
				document.getElementById('po_msg').innerHTML='';
				var txt_avg_price="";
				var txt_plan_cut=""
				var txt_po_quantity="";
				if(cbo_order_uom==58)
				{
					txt_avg_price=txt_avg_rate.value*tot_set_qnty;
					//document.getElementById('txt_avg_price').value=txt_avg_rate.value*tot_set_qnty;
					txt_plan_cut=number_format_common((txt_total_plan_cut.value/tot_set_qnty),6,0,0);
					document.getElementById('txt_plan_cut').value=number_format_common((txt_total_plan_cut.value/tot_set_qnty),6,0,0);
					if(with_qty==0){
						txt_po_quantity=number_format_common((txt_tot_po_qnty.value/tot_set_qnty),6,0,0);
						document.getElementById('txt_po_quantity').value=number_format_common((txt_tot_po_qnty.value/tot_set_qnty),6,0,0);
						document.getElementById('txt_avg_price').value=txt_avg_rate.value*tot_set_qnty;
						var txt_amount=txt_total_amt.value;
						document.getElementById('txt_amount').value=txt_total_amt.value;
					}
				}
				else
				{
					txt_avg_price=txt_avg_rate.value;
					//document.getElementById('txt_avg_price').value=txt_avg_rate.value;
					txt_plan_cut=number_format_common(txt_total_plan_cut.value,6,0,0);;
					document.getElementById('txt_plan_cut').value=txt_total_plan_cut.value;
					if(with_qty==0){
						txt_po_quantity=number_format_common((txt_tot_po_qnty.value/tot_set_qnty),6,0,0);
						document.getElementById('txt_po_quantity').value=number_format_common((txt_tot_po_qnty.value/tot_set_qnty),6,0,0);
						document.getElementById('txt_avg_price').value=txt_avg_rate.value;
						var txt_amount=txt_total_amt.value;
						document.getElementById('txt_amount').value=txt_total_amt.value;
					}
				}

				var txt_amount=txt_total_amt.value;
				//document.getElementById('txt_amount').value=txt_total_amt.value;
				var txt_excess_cut=txt_avg_excess_cut.value;
				document.getElementById('txt_excess_cut').value=txt_avg_excess_cut.value;
				document.getElementById('with_qty_pop').value=with_qty_pop.value;
				fnc_order_entry_details( 5 );
				comm_caption_field(2);
			}
		}
	}

	function set_excess_cut( val, excs )
	{
		//alert(val)
		//if (excs=="")
		//{
		if ( val!="" || val!=0 )
		{
			var excs_cut=return_ajax_request_value(val+"_"+document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_buyer_name').value+"_"+document.getElementById('hidd_job_id').value, "get_excess_cut_percent", "requires/order_entry_by_buying_house_controller") ;
			var excs_cut_arr=excs_cut.split("_");
			//document.getElementById('txt_excess_cut_var').value=excs_cut_arr[0];
			
			if(excs_cut_arr[0]*1==1){
				var txt_plan_cut=(val*1)+((excs*val*1)/100);
				document.getElementById('txt_excess_cut').value=excs;
				$('#txt_excess_cut').attr('disabled',false);
				document.getElementById('txt_plan_cut').value=txt_plan_cut;
			}
			else if(excs_cut_arr[0]*1==2){
				document.getElementById('txt_excess_cut').value=excs_cut_arr[1];
				var txt_plan_cut=(val*1)+((excs_cut_arr[1]*val*1)/100);
				document.getElementById('txt_plan_cut').value=number_format_common(txt_plan_cut, 6, 0);
				if(excs_cut_arr[2]==1) //Slab// Yes
				{
					$('#txt_excess_cut').attr('disabled',false);
				}
				else
				{
					$('#txt_excess_cut').attr('disabled',true);
				}
			}
			else if( excs_cut_arr[0]*1==3){
				document.getElementById('txt_excess_cut').value='';
				$('#txt_excess_cut').attr('disabled',true);
				document.getElementById('txt_plan_cut').value=val;
			}
		}
		//}
		/*else
		{

			var txt_plan_cut=(val*1)+((excs*val)/100);
			document.getElementById('txt_plan_cut').value=number_format_common(txt_plan_cut, 6, 0);
		}*/
		
		var isDisabledQty = document.getElementById('txt_po_no').disabled;
		
		if(isDisabledQty==true) $('#txt_excess_cut').attr('disabled',true);
		
		var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
		math_operation( 'txt_amount', 'txt_avg_price*txt_po_quantity', '*','', ddd );
	}

	function calculate_plan_cut()
	{
		var val=document.getElementById('txt_po_quantity').value;
		var excs=document.getElementById('txt_excess_cut').value;
		var txt_plan_cut=(val*1)+((excs*val*1)/100);
		document.getElementById('txt_plan_cut').value=number_format_common(txt_plan_cut, 6, 0);
		var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
		math_operation( 'txt_amount', 'txt_avg_price*txt_po_quantity', '*','', ddd );
	}

	function set_pub_ship_date()
	{
		var company_id=$('#cbo_company_name').val()
		var publish_shipment_date=return_global_ajax_value(company_id, 'publish_shipment_date', '', 'requires/order_entry_by_buying_house_controller');
		if(publish_shipment_date==1){
			 $('#txt_pub_shipment_date').attr('disabled',false);
		}
		else{
			var txt_org_shipment_date=$('#txt_org_shipment_date').val()
			$('#txt_pub_shipment_date').val(txt_org_shipment_date);
		}
	}

	function format_date(date)
	{
		var data=date.split('-');
		var new_date=data[2]+'-'+data[1]+'-'+data[0];
		return new_date;
	}

	function fnc_order_entry_details( operation )
	{
		//HERE
		//alert(operation);return;
		var is_precost_arr=operation;
		if(operation==5) operation=1;
		freeze_window(operation);
		var po_id=document.getElementById('update_id_details').value;
		var txt_job_no=document.getElementById('txt_job_no').value;
		var grouping=document.getElementById('txt_grouping').value;
		var po_update_period=document.getElementById('po_update_period_maintain').value;
		//var sewing_company_validate_id=document.getElementById('sewing_company_validate_id').value*1;
		var po_datediff=document.getElementById('txt_po_datedif_hour').value*1;
		var po_update_period=document.getElementById('po_update_period_maintain').value*1;
		var user_id=document.getElementById('txt_user_id').value;
		var job_copy_form=document.getElementById('txt_copy_form').value;
		var working_company_id=document.getElementById('cbo_working_company_id').value;
		var working_location_id=document.getElementById('cbo_working_location_id').value;
		var cbo_status=document.getElementById('cbo_status').value*1;
		var cbo_order_status=document.getElementById('cbo_order_status').value*1;
		var permission_array=permission.split("_");
		var cbo_agent=document.getElementById('cbo_agent').value*1;
		var txt_foreign_commission=document.getElementById('txt_foreign_commission').value;

		//alert(permission_array[2])
		/*if(sewing_company_validate_id==1)
		{
			if (form_validation('cbo_working_company_id*cbo_working_location_id','Sewing Company*Sewing Location')==false){
				release_freezing();
				return;
			}

		}*/
		if(cbo_agent!=""){
			if(txt_foreign_commission==""){
				$('#txt_foreign_commission').focus();
				release_freezing();
				return;
			}
		}
		if(job_copy_form!="")
		{
			//alert(operation);return;
			if(working_company_id==0)
			{
				alert('Select Working Company');
				$('#cbo_working_company_id').focus();
				release_freezing();
				return;
			}
			else if(working_location_id==0)
			{
				alert('Select Working Location');
				$('#cbo_working_location_id').focus();
				release_freezing();
				return;
			}
		}
		if(cbo_status==2 && permission_array[2]==2 && operation==1)
		{
			alert("You have no Permission to inactive");
			release_freezing();
			return;
		}
		if(cbo_status==3 && permission_array[2]==2 && operation==1)
		{
			alert("You have no Permission to Cancel");
			release_freezing();
			return;
		}
		 
		
		if(operation==2)
		{
		   var r=confirm("Your going to Delete a PO.\n Please, Press OK to Delete\n Otherwise  Press Cancel");
			if(r==false){
				release_freezing();
				return;
			}
		}
		//alert(operation);return;
		var txt_quotation_id=(document.getElementById('txt_quotation_id').value)*1;
		var txt_quotation_price=(document.getElementById('txt_quotation_price').value)*1;
		var txt_avg_price=(document.getElementById('txt_avg_price').value)*1;
		if(txt_quotation_id>0 && txt_quotation_id !=""){
			if(txt_avg_price < txt_quotation_price){
				alert("Unit price can not be less than quoted price");
				release_freezing();
				return;
			}
		}
		if(operation==1) //Check Only Update event
		{
			var chk_extended_ship_date=document.getElementById('chk_extended_ship_date').value;
			var org_shipment_date=document.getElementById('txt_org_shipment_date').value;
			var factory_rec_date=document.getElementById('txt_factory_rec_date').value;
			var pub_shipment_date=document.getElementById('txt_pub_shipment_date').value;
			var po_received_date=document.getElementById('txt_po_received_date').value;
			 
			if(chk_extended_ship_date!="")
			{
				if(date_compare(org_shipment_date, chk_extended_ship_date)==false || date_compare(pub_shipment_date, chk_extended_ship_date)==false) 
				{
					alert('Ship date not allowed greater than extended ship date');
					release_freezing();
					return; 
					
				}
			}
		}
			
		

		//alert(po_update_period);return;
		if(operation==1)
		{
			//alert(operation);return;
		  	if(po_update_period!=0){
				var user_id_arr=user_id.split(',');
				if(jQuery.inArray( user , user_id_arr ) == -1)// It will use in Future
				{
					//var cbo_order_status = return_ajax_request_value(po_id, 'get_po_is_confirmed_status', 'requires/order_entry_by_buying_house_controller');
					var txt_order_status=document.getElementById('txt_order_status').value*1;
					if(po_update_period<=po_datediff)//&& txt_order_status==1
					{
						//alert(user_id);
						alert("Update Period Exsits,You are not Allowed to Update.Please take approval of authority and mail to concern person for permission");
						release_freezing();
						return;
					}
				}
			}
			var txt_order_status=document.getElementById('txt_order_status').value*1;
			if(txt_order_status!=2)
			{
				var booking_no_with_approvet_status = return_ajax_request_value(txt_job_no+"_"+po_id, 'booking_no_with_approved_status', 'requires/order_entry_by_buying_house_controller')
				var booking_no_with_approvet_status_arr=booking_no_with_approvet_status.split("_");
				if(trim(booking_no_with_approvet_status_arr[0]) !="")
				{
					var al_magg="Main Fabric Approved Booking No "+booking_no_with_approvet_status_arr[0];
					if(booking_no_with_approvet_status_arr[1] !="")
					{
						al_magg+=" and Un-Approved Booking No "+booking_no_with_approvet_status_arr[1];
					}
					al_magg+=" found,\nPlease Un-approved the booking first";
					alert(al_magg)
					release_freezing();
					return;
				}

				if(trim(booking_no_with_approvet_status_arr[1])!="")
				{
					var al_magg=" Main Fabric Un-Approved Booking No "+booking_no_with_approvet_status_arr[1]+" Found\n If you update this job\n You have to update color size break down, Pre-cost and booking against this Job ";
					var r=confirm(al_magg);
					if(r==false)
					{
						release_freezing();
						return;
					}
					else
					{
					}
				}
			}
		}

		 

		var with_qty=document.getElementById('with_qty').value;
		if(with_qty==1){
			if (form_validation('update_id*cbo_supplier_id*txt_po_no*txt_po_received_date*txt_po_quantity*txt_avg_price*txt_org_shipment_date*txt_po_unit_price','Master Info*Working Factory*PO Number*PO Received Date*PO Quantity*Avg. Price*Org. Shipment Date*Factory Price')==false){
			release_freezing();
			return;
			}
		}else{
			if (form_validation('update_id*cbo_supplier_id*txt_po_no*txt_po_received_date*txt_org_shipment_date*txt_po_unit_price','Master Info*Working Factory*PO Number*PO Received Date*Org. Shipment Date*Factory Price')==false){
				release_freezing();
				return;
			}
		}

		var response1=return_global_ajax_value( grouping+"**"+txt_job_no, 'check_internal_ref', '', 'requires/order_entry_by_buying_house_controller');
		var response1=response1.split("_");
		var inter_ref=$('#txt_grouping').val();
		if(inter_ref!=""){
			if(response1[0]==0){
				alert("Not Found This Internal Ref");
				$('#txt_grouping').val('');
				release_freezing();
				return;
			}
		}
		var defult_color=0;
		if(operation==0 && po_id=="" && cbo_order_status==2){
			var r=confirm("Please, Press OK for Default Color Size Breakdown\n Otherwise  Press Cencel");
			if(r==true) defult_color=1;
			else defult_color=0;
		}
		if(operation==0 && po_id!=""){
		   var r=confirm("Your going to copy a PO.\n Please, Press OK to copy\n Otherwise  Press Cencel");
			if(r==true){
				defult_color=1;
			}
			else{
				defult_color=0;
				release_freezing();
				return;
			}
		}
		var company_id=$('#cbo_company_name').val()
		var publish_shipment_date=return_global_ajax_value(company_id, 'publish_shipment_date', '', 'requires/order_entry_by_buying_house_controller');
		var txt_po_received_date=new Date(format_date(document.getElementById('txt_po_received_date').value));
		var txt_pub_shipment_date=new Date(format_date(document.getElementById('txt_pub_shipment_date').value));
		var txt_org_shipment_date=new Date(format_date(document.getElementById('txt_org_shipment_date').value));
		var txt_factory_rec_date=new Date(format_date(document.getElementById('txt_factory_rec_date').value));
		if (txt_po_received_date.getTime() > txt_pub_shipment_date.getTime() && publish_shipment_date==1) {
			alert("Pub. Shipment Date Less Than PO Received Date date!");
			release_freezing();
			return;
		}
		if (txt_po_received_date.getTime() > txt_org_shipment_date.getTime()) {
			alert("Org. Shipment Date Less Than PO Received Date!");
			release_freezing();
			return;
		}

		if (txt_po_received_date.getTime() > txt_factory_rec_date.getTime()) {
			alert("Fac. Received Date Less Than Po Receive Date!");
			release_freezing();
			return;
		}

		if (txt_factory_rec_date.getTime() > txt_pub_shipment_date.getTime() && publish_shipment_date==1) {
			alert("Pub. Shipment Date Less Than Fac. Receive Date!");
			release_freezing();
			return;
		}
		if (txt_factory_rec_date.getTime() > txt_org_shipment_date.getTime()) {
			alert("Org. Shipment Date  Less Than Fac. Receive Date!");
			release_freezing();
			return;
		}

		if (txt_pub_shipment_date.getTime() > txt_org_shipment_date.getTime()) {
			alert("Org. Shipment Date Less Than Pub. Shipment Date!");
			release_freezing();
			return;
		}

		var txt_avg_price=document.getElementById('txt_avg_price').value;
		if(txt_avg_price==0 && with_qty==1){
			alert("Avg. Price 0 not accepted");
			release_freezing();
			return;
		}
		var is_of_day=return_global_ajax_value(company_id+"_"+document.getElementById('txt_pub_shipment_date').value+"_"+document.getElementById('txt_org_shipment_date').value+"_"+document.getElementById('cbo_location_name').value, 'is_of_day', '', 'requires/order_entry_by_buying_house_controller');

		var is_of_day_arr=is_of_day.split("_");
		if(is_of_day_arr[0]==2){
			alert("Pub. Shipment Date is off day, Please Change it" );
			release_freezing();
			return;
		}
		if(is_of_day_arr[1]==2){
			alert("Shipment Date is off day, Please Change it" );
			release_freezing();
			return;
		}

		if(document.getElementById('update_id_details').value && document.getElementById('txt_job_no').value && operation>0)
	    {
	      var shipping_status = trim(return_global_ajax_value(document.getElementById('txt_job_no').value+'***'+document.getElementById('update_id_details').value, 'check_po_shipping_status_po_id', '', 'requires/order_entry_by_buying_house_controller'));
	        console.log(shipping_status);
	        var d=shipping_status.split("***");
	        if((d[0]*1)==3)
	        {
	          alert('Shipping Status is Full Delivery/Closed . Update and Delete not allowed');
	          release_freezing();
	          return;
	        }
	    }
		//return;
		var tna=check_tna_leadtime();
		var data=tna.split("_");
		var temp=data[0]; var tna=data[1]; var tna_process=data[3]; var bom_sync_msg=0;
		var isDisabled = document.getElementById('cbo_order_status').disabled;
		/*if($('#with_qty').val()!=1 && isDisabled==false)
		{*/
			if( $('#update_id_details').val()=="" ||  $('#update_id_details').val()==0) bom_sync_msg=1;
		//}
		
		if(tna==1 && temp==0 && tna_process==1){
			alert("Order Lead Time not allowed less than available TNA Template's Lead Time. So,Prepare the Template first")
			release_freezing();
			return;
		}
		else{
			var data="action=save_update_delete_dtls&operation="+operation+'&defult_color='+defult_color+'&is_precost_arr='+is_precost_arr+'&bom_sync_msg='+bom_sync_msg+get_submitted_data_string('cbo_order_status*txt_po_no*txt_po_received_date*txt_pub_shipment_date*txt_org_shipment_date*txt_factory_rec_date*txt_po_quantity*txt_avg_price*txt_amount*txt_excess_cut*txt_plan_cut*cbo_status*txt_details_remark*update_id_details*update_id*hidd_job_id*cbo_packing*cbo_delay_for*cbo_packing_po_level*color_size_break_down*txt_grouping*cbo_projected_po*cbo_tna_task*set_breck_down*txt_file_no*cbo_company_name*tot_set_qnty*cbo_buyer_name*cbo_team_leader*cbo_location_name*tot_smv_qnty*txt_sc_lc*with_qty*with_qty_pop*cbo_working_company_id*cbo_working_location_id*txt_etd_ldd*cbo_order_uom*cbo_supplier_id*commission_value_type*commission_per_pcs*txt_commission*txt_foreign_commission*txt_po_unit_price*txt_factory_price*cbo_within_group*cbo_lc_company_id',"../../");
			http.open("POST","requires/order_entry_by_buying_house_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_on_submit_reponse;
		}
	}

	function fnc_on_submit_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=http.responseText.split('**');
			console.log(http.responseText);
			if(reponse[0]=='10')
			{
				release_freezing();
				return;
			}
			if(reponse[0]=='bomApp')
			{
				alert("BOM is Approved.");
				release_freezing();
				return;
			}
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not  Approved. Please Approved the Quotation");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='LeadTime')
			{
				alert("Bellow "+ trim(reponse[2]) +" Days Lead Time not allow. If required, please take approval of Marketing Director.");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='CapaCityQty')
			{
				alert("Capacity Qty Over");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='CapaCityValue')
			{
				alert("Capacity Value Over");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='CapaCityMin')
			{
				alert("Order entry not allowed over Capacity SAH\nPlease check capacity balance SAH and input accordingly");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='WorkingCapacityMin') //For Working Company
			{
				// alert("Capacity Qty Over\n"+'Total Capacity Min='+reponse[2]);
				var bal_min = number_format(reponse[1]-reponse[2],2);
				alert('Total Capacity Min='+reponse[2]+'\n Capacity Min Over By= '+bal_min);
				release_freezing();
				return;
			}

			if(trim(reponse[0]) ==24)
			{
				alert("Please Tag Image");
				release_freezing();
				return;
			}
			if(trim(reponse[0]) ==12)
			{
				alert("Order Entry has remained Block before ship date 01/05/2016. If needed,Pls take approval of CEO Sir");
				release_freezing();
				return;
			}

			if(reponse[0]==16)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}

			if(trim(reponse[0])==13)
			{
				var al_msglc="";
				al_msglc="Booking No : '"+trim(reponse[1])+"' found and donot delete Order no,\n If Need Please delete the Booking first.";
				alert(al_msglc);
				release_freezing();
				return;
			}
			if(trim(reponse[0]) == 46)
			{
				alert(trim(reponse[1]))
				if( (trim(reponse[1])*1)==1)  var msg=" Published shipment date "; else var msg=" Orginal shipment date ";
				alert("Your "+ msg +" is on Off day, as per company policy, offday shipment is not allowed.");
				release_freezing();
				return;
			}

			if(trim(reponse[0]) == 50)
			{
				alert(trim(reponse[1]));
				release_freezing();
				return;
			}

			show_msg(reponse[0]);
			release_freezing();
			show_list_view(document.getElementById('txt_job_no').value,'show_po_active_listview','po_list_view','requires/order_entry_by_buying_house_controller','');
			 //show_list_view(document.getElementById('txt_job_no').value,'show_deleted_po_active_listview','deleted_po_list_view','requires/order_entry_by_buying_house_controller','');

			if(trim(reponse[0]) !=11)
			{
				reset_form('','','txt_po_no*txt_pub_shipment_date*txt_org_shipment_date*txt_factory_rec_date*txt_po_quantity*txt_avg_price*txt_amount*txt_excess_cut*txt_plan_cut*cbo_status*txt_details_remark*cbo_delay_for*show_textcbo_delay_for*cbo_packing_po_level*update_id_details*color_size_break_down*txt_grouping*cbo_projected_po*cbo_tna_task*txt_order_status*update_id_details*txt_foreign_commission*txt_po_unit_price*txt_factory_price','','');
				 $('#txt_avg_price').removeAttr('disabled');
				 $('#txt_avg_price').removeAttr('title');
				 document.getElementById('txt_total_job_quantity').value=trim(reponse[2])
				 document.getElementById('txt_avg_unit_price').value=trim(reponse[3])
				 document.getElementById('txt_job_total_price').value=trim(reponse[4])
				 document.getElementById('set_pcs').value=trim(reponse[5])
				 document.getElementById('set_unit').value=trim(reponse[6])
				 document.getElementById('txt_po_received_date').value= '<? echo date("d-m-Y"); ?>'
				 set_button_status(0, permission, 'fnc_order_entry_details',2);
			}
			if(trim(reponse[0])==0 || trim(reponse[0])==1){
				reset_form('','','cbo_supplier_id*txt_po_received_date*commission_per_pcs*txt_commission*txt_po_unit_price *txt_factory_price','','');
				release_freezing();
			}
			set_button_status(0, permission, 'fnc_order_entry_details',2);
			load_drop_down( 'requires/order_entry_by_buying_house_controller', document.getElementById('txt_job_no').value, 'load_drop_down_projected_po', 'projected_po_td' )
			release_freezing();
		}
	}

	function fnc_po_pop(type)
	{
		if (form_validation('txt_job_no','Job No')==false)
		{
			release_freezing();
			return;
		}
		else
		{	
			if(type==1){
				var hidd_job_id=$("#hidd_job_id").val();
				var cbo_template_id=$("#cbo_template_id").val();
				var page_link='requires/order_entry_by_buying_house_controller.php?action=order_no_popup&hidd_job_id='+hidd_job_id+'&cbo_template_id='+cbo_template_id;
				var title='Supplier Po Formate';

				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=400px,center=1,resize=1,scrolling=0','../')
				emailwindow.onclose=function()
				{
					var theform=this.contentDoc.forms[0];
					var po_id=this.contentDoc.getElementById("txt_selected_id").value;
					var po_no=this.contentDoc.getElementById("txt_selected").value;
					if (po_id!="")
					{
						//$("#txt_po_no").val(po_no);
						//$("#txt_po_breack_down_id").val(po_id);
						var report_title='Supplier Po Formate';//
						var data= $('#cbo_company_name').val()+'*'+po_id+'*'+$("#txt_job_no").val()+'*'+report_title;
						var action='supplier_po_print_formate&title='+title+'&cbo_template_id='+cbo_template_id;
						window.open("order_entry_by_buying_house_controller.php?data=" + data+'&action='+action, true );
					}
				}
			}
			if(type==2){
				var hidd_job_id=$("#hidd_job_id").val();
				var cbo_template_id=$("#cbo_template_id").val();
				var page_link='requires/order_entry_by_buying_house_controller.php?action=order_no_popup&hidd_job_id='+hidd_job_id+'&cbo_template_id='+cbo_template_id;
				var title='Supplier Po Formate';

				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=400px,center=1,resize=1,scrolling=0','../')
				emailwindow.onclose=function()
				{
					var theform=this.contentDoc.forms[0];
					var po_id=this.contentDoc.getElementById("txt_selected_id").value;
					var po_no=this.contentDoc.getElementById("txt_selected").value;
					if (po_id!="")
					{
						//$("#txt_po_no").val(po_no);
						//$("#txt_po_breack_down_id").val(po_id);
						var report_title='Supplier Po Formate';//
						var data= $('#cbo_company_name').val()+'*'+po_id+'*'+$("#txt_job_no").val()+'*'+report_title;
						var action='supplier_po_print_formate_print2&title='+title+'&cbo_template_id='+cbo_template_id;
						window.open("order_entry_by_buying_house_controller.php?data=" + data+'&action='+action, true );
					}
				}
			}
		}
	}

	function get_details_form_data(id,type,path)
	{
		reset_form('','','cbo_order_status*txt_po_no*txt_po_received_date*txt_pub_shipment_date*txt_org_shipment_date*txt_po_quantity*txt_avg_price*txt_amount*txt_excess_cut*txt_plan_cut*cbo_status*txt_details_remark*cbo_delay_for*show_textcbo_delay_for*cbo_packing_po_level*update_id_details*color_size_break_down*txt_grouping*cbo_projected_po*cbo_tna_task','','');
		get_php_form_data( id, type, path );
	}

	function set_tna_task()
	{
		check_tna_leadtime();
		var txt_po_received_date=document.getElementById('txt_po_received_date').value;
		var txt_pub_shipment_date=document.getElementById('txt_pub_shipment_date').value;
		var txt_org_shipment_date=document.getElementById('txt_org_shipment_date').value;
		var txt_factory_rec_date=document.getElementById('txt_factory_rec_date').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var datediff = date_compare(txt_po_received_date,txt_pub_shipment_date);//date_diff('d', txt_po_received_date, txt_pub_shipment_date);
		//alert(datediff);
		if(datediff==false)
		{
			alert("PO Received Date Is Greater Than Pub. Shipment Date.");
			$('#txt_pub_shipment_date').val("");
			return;
		}

		var recdatediff = date_compare(txt_po_received_date,txt_factory_rec_date);//date_diff('d', txt_po_received_date, txt_pub_shipment_date);
		//alert(datediff);
		if(recdatediff==false)
		{
			alert("PO Received Date Is Greater Than Fac. Receive Date.");
			$('#txt_factory_rec_date').val("");
			return;
		}

		var shipdatediff = date_compare(txt_pub_shipment_date,txt_org_shipment_date);
		if(shipdatediff==false)
		{
			alert("Pub. Shipment Date. Is Greater Than Org. Shipment Date.");
			$('#txt_org_shipment_date').val("");
			return;
		}

		load_drop_down( 'requires/order_entry_by_buying_house_controller', txt_po_received_date+"_"+txt_pub_shipment_date+"_"+cbo_buyer_name, 'load_drop_down_tna_task', 'tna_task_td' )
	}

	function check_tna_leadtime(){
		var buyer_id=document.getElementById('cbo_buyer_name').value;
		var txt_po_received_date=document.getElementById('txt_po_received_date').value
		var txt_pub_shipment_date=document.getElementById('txt_pub_shipment_date').value
			var cbo_company_name=$('#cbo_company_name').val();
			var tna=return_global_ajax_value(buyer_id+"_"+cbo_company_name+'_'+txt_po_received_date+'_'+txt_pub_shipment_date, 'check_tna_leadtime', '', 'requires/order_entry_by_buying_house_controller');
			return tna;
	}

	function sub_dept_load(cbo_buyer_name,cbo_product_department)
	{
		if(cbo_buyer_name ==0 || cbo_product_department==0 )
		{
			return
		}
		else
		{
			load_drop_down( 'requires/order_entry_by_buying_house_controller',cbo_buyer_name+'_'+cbo_product_department, 'load_drop_down_sub_dep', 'sub_td' )
		}
	}

	function pop_entry_actual_po()
	{
		var po_id = $('#update_id_details').val();
		var txt_job_no = $('#txt_job_no').val();
		var gmts_item = $('#item_id').val();
		var po_quantity = $('#txt_po_quantity').val();
		var job_id = $('#hidd_job_id').val();
		var po_rcv_date = $('#txt_po_received_date').val();
		var cbo_company_name = $('#cbo_company_name').val();
		var act_po_id = $('#act_po_id').val();
		if(po_id=="")
		{
			alert("Save The PO First");
			return;
		}
		var action="";
		if(act_po_id==1){
			action="actual_po_info_popup";
		}
		else{
			action="actual_po_info_popup_v1";
		}
		var page_link='requires/order_entry_by_buying_house_controller.php?action='+action+'&po_id='+po_id+'&txt_job_no='+txt_job_no+'&gmts_item='+gmts_item+'&po_quantity='+po_quantity+'&job_id='+job_id+'&rcv_date='+po_rcv_date+'&cbo_company_name='+cbo_company_name+'&act_po_id='+act_po_id;
		var title='Actual Po Entry Info';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=550px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
		}
	}
	
	function pop_entry_actual_po_back()//ISD-21-02141
	{
		var po_id = $('#update_id_details').val();
		var txt_job_no = $('#txt_job_no').val();
		var company_id = $('#cbo_company_name').val();
		if(po_id=="")
		{
			alert("Save The PO First");
			return;
		}
		var page_link='requires/order_entry_by_buying_house_controller.php?action=actual_po_info_popup&po_id='+po_id+'&txt_job_no='+txt_job_no+'&company_id='+company_id;
		var title='Actual Po Entry Info';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=300px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
		}
	}
	//Dtls Form End -------------------------------------------------------------------------------------

	function open_terms_condition_popup(page_link,title)
	{
		var txt_job_no=document.getElementById('txt_job_no').value;
		if (txt_job_no=="")
		{
			alert("Save The Job No First")
			return;
		}
		else
		{
			page_link=page_link+get_submitted_data_string('txt_job_no','../../');
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=470px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemail=this.contentDoc.getElementById("iref");
				//alert(theemail.value)
				internal( theemail.value );
			}
		}
	}
	function oepn_requision()
	{
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_entry_by_buying_house_controller.php?action=requisition_pop_up', "Requisition Pop Up", 'width=1050px,height=470px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var requisition_no=this.contentDoc.getElementById("requisition_no").value;
			console.log(requisition_no);
			$("#txt_requision_no").val(requisition_no);
			//$('#txt_requision_no').attr('disabled','disabled');
			//alert(theemail.value)
			//internal( theemail.value );
		}
	}

	function po_recevied_date()
	{
		var po_current_date_maintain=document.getElementById('po_current_date_maintain').value;
 		var order_status=document.getElementById('cbo_order_status').value;
		var current_date='<? echo date('d-m-Y'); ?>'
		//alert(order_status);
		if(po_current_date_maintain==1 && order_status==1)
		{
			//$('#txt_po_received_date').attr('disabled','disabled');
			$('#txt_po_received_date').val(current_date);
		}

		else if(po_current_date_maintain==1 && order_status==2)
		{
			//$('#txt_po_received_date').attr('disabled','disabled');
			$('#txt_po_received_date').val(current_date);
		}
		else
		{
			$('#txt_po_received_date').val('');
			//$('#txt_po_received_date').removeAttr('disabled','disabled');
		}
	}

	function validate_po_qty()
	{
		var saved_po_quantity=$('#txt_po_quantity').attr('saved_po_quantity');
	 
		var po_id = $('#update_id_details').val()*1;
		var txt_excess_cut=$('#txt_excess_cut').val()*1;
		var tot_set_qnty=$('#tot_set_qnty').val()*1;
		var txt_po_quantity=$('#txt_po_quantity').val()*1;
		var hidden_po_qty=$('#hidden_po_qty').val()*1;
		var po_quantity_pcs=txt_po_quantity*tot_set_qnty;
		var input=document.getElementById('txt_po_quantity');
		var hidden_color_qty=$('#txt_hidden_color_qty').val()*1;
		
		if(po_id!=0)
		{
			var cutting_qty=return_global_ajax_value(po_id, 'get_cutting_qty', '', 'requires/order_entry_by_buying_house_controller');
			cutting_qty=cutting_qty/tot_set_qnty;
	
			var excess_cut_per=(1+(txt_excess_cut/100));
			var allowed_qty=cutting_qty/excess_cut_per;
			allowed_qty=Math.ceil(allowed_qty);
			//alert(hidden_color_qty+'='+txt_po_quantity);
			if(hidden_color_qty>0)
			{
				if(po_quantity_pcs<hidden_color_qty)
				{
					alert("Can not reduce than Color Size Qty,You can update From "+hidden_color_qty+" Qty");
					$('#txt_po_quantity').val(hidden_po_qty);
					comm_caption_field(2);
					return;
				}
			}
			if(txt_po_quantity<allowed_qty)
			{
				alert("Cutting Qty Found,You can update upto"+allowed_qty+" Qty");
				$('#txt_po_quantity').val(saved_po_quantity);
				comm_caption_field(2);
				return;
			}
		}
	}

	function check_tna_templete(buyer_id){
		var cbo_company_name=$('#cbo_company_name').val();
		var tna=return_global_ajax_value(buyer_id+"_"+cbo_company_name, 'check_tna_templete', '', 'requires/order_entry_by_buying_house_controller');
		var data=tna.split("_");
		var temp=data[0];
		var tna=data[1];
		var tna_process=data[2];
		if(tna==1 && temp==0 && tna_process==1){
			alert("TNA Intregeted, But TAN templete not found for this Buyer; please set templete first.")
			$('#cbo_buyer_name').val(0);
		}
	}
	function fnc_file_no_check(file_no_check)
	{
		//var publish_shipment_date=return_global_ajax_value(company_id, 'publish_shipment_date', '', 'requires/order_entry_by_buying_house_controller');
		//alert(file_no_check);
		if(file_no_check==3)
		{
			//$('#txt_file_no').val('');
			$('#txt_file_no').removeAttr("onDblClick").attr("onDblClick","openmypage_file_no();");
			$('#txt_file_no').attr("placeholder","Click");
		}
		else
		{
			$('#txt_file_no').removeAttr("onDblClick").attr("placeholder","Write");
		}
	}
	

	function publish_shipment_date(publish_shipment_date)
	{
		//var publish_shipment_date=return_global_ajax_value(company_id, 'publish_shipment_date', '', 'requires/order_entry_by_buying_house_controller');
		if(publish_shipment_date==1)
		{
			$('#txt_pub_shipment_date').attr('disabled',false);
		}
		else
		{
			$('#txt_pub_shipment_date').attr('disabled',true);
		}
		$('#txt_style_ref').val('');
	}

	function get_company_config(company_id)
	{
		$('#cbo_working_company_id').val( company_id );
		load_drop_down( 'requires/order_entry_by_buying_house_controller', company_id, 'load_drop_down_sew_location', 'sew_location' );

		get_php_form_data(company_id,'get_company_config','requires/order_entry_by_buying_house_controller' ); 
		//location_select();
		po_update_period();
	}

	function get_sew_company_config(company_id)
	{
		load_drop_down( 'requires/order_entry_by_buying_house_controller', company_id, 'load_drop_down_sew_location', 'sew_location' );
	}


	function get_buyer_config(buyer_id)
	{
		sub_dept_load(buyer_id,document.getElementById('cbo_product_department').value);
		check_tna_templete(buyer_id)
		//get_php_form_data(buyer_id,'get_buyer_config','requires/order_entry_by_buying_house_controller' );
		load_drop_down( 'requires/order_entry_by_buying_house_controller', buyer_id, 'load_drop_down_season_buyer', 'season_td');
		load_drop_down( 'requires/order_entry_by_buying_house_controller', buyer_id+'*'+1, 'load_drop_down_brand', 'brand_td');
		load_tenor(buyer_id);
	}

	function po_update_period(company_id)
	{
		//var com=$('#cbo_company_name').val();
		//alert(com);
		//get_php_form_data($('#cbo_company_name').val(),'update_period_maintained_data','requires/order_entry_by_buying_house_controller' );
		//get_php_form_data($('#cbo_company_name').val(),'po_received_date_maintained_data','requires/order_entry_by_buying_house_controller' );

		var order_status=document.getElementById('cbo_order_status').value;
		var po_current_date_maintain=document.getElementById('po_current_date_maintain').value;
		var current_date='<? echo date('d-m-Y'); ?>'
		if(po_current_date_maintain==1 && order_status==1)
		{
			//$('#txt_po_received_date').attr('disabled','disabled');
			$('#txt_po_received_date').val(current_date);
		}
		else
		{
			$('#txt_po_received_date').val('');
			//$('#txt_po_received_date').removeAttr('disabled','');
		}
	}
	
	function budget_exceeds_quot(data)
	{
		var exdata=data.split("_");

		var copy_quotation=exdata[0];
		var cost_control_source=exdata[1];
		var set_smv_id=exdata[2];
		//alert(set_smv_id)
		//var copy_quotation=return_global_ajax_value(company_id, 'copy_quotation', '', 'requires/order_entry_by_buying_house_controller');
		//open_qoutation_popup('requires/order_entry_by_buying_house_controller.php?action=quotation_id_popup','Quotation ID Selection Form')
		if(copy_quotation==1)
		{
			if((set_smv_id==2 || set_smv_id==4 || set_smv_id==5 || set_smv_id==6) && cost_control_source==2)
			{
				$('#txt_style_ref').attr('readonly',true);
				$('#txt_style_ref').attr('placeholder','Browse');
				var page_link="'requires/order_entry_by_buying_house_controller.php?action=quotation_id_popup','Quotation ID Selection Form'";// for Price Quotation
				$('#txt_style_ref').removeAttr("onDblClick").attr("onDblClick","open_qoutation_popup("+page_link+");");
			}
			else if(set_smv_id==7 && styleBrowse==2 && cost_control_source==1)
			{
				$('#txt_style_ref').attr('readonly',true);
				$('#txt_style_ref').attr('placeholder','Browse');
				var page_link="'requires/order_entry_by_buying_house_controller.php?action=qc_id_popup','Quick Costing Style Selection'";// for Quick Costing
				$('#txt_style_ref').removeAttr("onDblClick").attr("onDblClick","open_qoutation_popup("+page_link+");");
			}
			else if(set_smv_id==7 && styleBrowse!=2 && cost_control_source==1)
			{
				$('#txt_style_ref').attr('readonly',false);
				$('#txt_style_ref').attr('placeholder','Browse/Write');
				var page_link="'requires/order_entry_by_buying_house_controller.php?action=qc_id_popup','Quick Costing Style Selection'";// for Quick Costing
				$('#txt_style_ref').removeAttr("onDblClick").attr("onDblClick","open_qoutation_popup("+page_link+");");
			}
			else if(set_smv_id==3 || set_smv_id==8 || set_smv_id==9)
			{
				$('#txt_style_ref').attr('readonly',false);
				$('#txt_style_ref').attr('placeholder','Browse/Write');
				var page_link="'requires/order_entry_by_buying_house_controller.php?action=ws_id_popup','Work Study Style Selection'";// for Work Study
				$('#txt_style_ref').removeAttr("onDblClick").attr("onDblClick","open_qoutation_popup("+page_link+");");
			}
			else
			{
				$('#txt_style_ref').attr('readonly',false);
				$('#txt_style_ref').attr('placeholder','Write');
				$('#txt_style_ref').removeAttr('onDblClick','onDblClick');
			}
		}
		else
		{
			$('#txt_style_ref').attr('readonly',false);
			$('#txt_style_ref').attr('placeholder','Write');
			$('#txt_style_ref').removeAttr('onDblClick','onDblClick');
		}
	}

	function set_checkvalue()
	{
		if(document.getElementById('with_qty').value==1){
			 document.getElementById('with_qty').value=0;
		}
		else {
			document.getElementById('with_qty').value=1;
		}
	}


	function fnResetForm()
	{
		//$dd="disable_enable_fields( 'cbo_company_name', '0')";
		reset_form('orderentry_1*orderdetailsentry_2','deleted_po_list_view*po_list_view','','','');
		$('#cbo_company_name').attr('disabled',false);
		$('#cbo_currercy').val(2);
	}

	$(document).ready(function(){
	  navigate_arrow_key()
	});

	 new function ($) {
        $.fn.getCursorPosition = function () {
            var pos = 0;
            var el = $(this).get(0);
            // IE Support
            if (document.selection) {
                el.focus();
                var Sel = document.selection.createRange();
                var SelLength = document.selection.createRange().text.length;
                Sel.moveStart('character', -el.value.length);
                pos = Sel.text.length - SelLength;
            }
            // Firefox support
            else if (el.selectionStart || el.selectionStart == '0')
                pos = el.selectionStart;
            return pos;
        }
    } (jQuery);

	function navigate_arrow_key()
	{
		$('input').keyup(function(e){

			if( e.which==39 )
			{
				 if( $(this).getCursorPosition() == $(this).val().length )
				 	$(this).closest('td').next().find('.text_boxes,.text_boxes_numeric').select();
			}
			else if( e.which==37 )
			{
				if( $(this).getCursorPosition() == 0 )
					$(this).closest('td').prev().find('.text_boxes,.text_boxes_numeric').select();
			}
			else if( e.which==40 )
			{
				$(this).closest('tr').next().find('td:eq('+$(this).closest('td').index() +')').find('.text_boxes,.text_boxes_numeric').select();
			}
			else if( e.which==38 )
			{
				$(this).closest('tr').prev().find('td:eq('+$(this).closest('td').index()+')').find('.text_boxes,.text_boxes_numeric').select();
			}
		});
	}

	function file_uploader_popup(type)
	{
		var txt_po_no=$("#txt_po_no").val();
		if (form_validation('txt_po_no','PO No')==false)
		{
			return;
		}
		else
		{
			//alert(20);
			if(type==2)
			{
				file_uploader ( '../../', document.getElementById('update_id_details').value,'', 'knit_po_entry', 2 ,1);
			}
		}
	}
	
	function fnc_avg_price_check(val)//14201 by Kausar
	{
		var copy_quotation=0;
		if ( $('#txt_style_ref').is('[readonly]') )
		{
			copy_quotation=1;
		}
		
		var cost_control_source=$("#hid_cost_source").val();
		var set_smv_id=$("#set_smv_id").val();
		//alert(copy_quotation+'_'+cost_control_source+'_'+set_smv_id)
		if(copy_quotation==1 && cost_control_source==1 && set_smv_id==7 && styleBrowse==2)
		{
			var qFob=$('#txt_avg_price').attr('quot_cost')*1;
			if(qFob>val)
			{
				alert("Avg. Rate Exceeded From Quick Costing FOB.");
				$('#txt_avg_price').val(qFob);
				return;
			}
		}
	}
	
	function location_select()
	{
		if($('#cbo_location_name option').length==2)
		{
			if($('#cbo_location_name option:first').val()==0)
			{
				$('#cbo_location_name').val($('#cbo_location_name option:last').val());
				//eval($('#cbo_location_name').attr('onchange')); 
			}
		}
		else if($('#cbo_location_name option').length==1)
		{
			$('#cbo_location_name').val($('#cbo_location_name option:last').val());
			//eval($('#cbo_location_name').attr('onchange'));
		}
		
		if($('#cbo_working_company_id option').length==2)
		{
			if($('#cbo_working_company_id option:first').val()==0)
			{
				$('#cbo_working_company_id').val($('#cbo_working_company_id option:last').val());
				//eval($('#cbo_location_name').attr('onchange')); 
			}
		}
		else if($('#cbo_working_company_id option').length==1)
		{
			$('#cbo_working_company_id').val($('#cbo_working_company_id option:last').val());
			//eval($('#cbo_location_name').attr('onchange'));
		}
		
		if($('#cbo_working_location_id option').length==2)
		{
			if($('#cbo_working_location_id option:first').val()==0)
			{
				$('#cbo_working_location_id').val($('#cbo_working_location_id option:last').val());
				//eval($('#cbo_location_name').attr('onchange')); 
			}
		}
		else if($('#cbo_working_location_id option').length==1)
		{
			$('#cbo_working_location_id').val($('#cbo_working_location_id option:last').val());
			//eval($('#cbo_location_name').attr('onchange'));
		}	
	}
	function check_po_shipping_status()
	{
		//console.log('shipping_status');
		if(document.getElementById('update_id_details').value && document.getElementById('txt_job_no').value)
		{
			var shipping_status = trim(return_global_ajax_value(document.getElementById('txt_job_no').value+'***'+document.getElementById('update_id_details').value, 'check_po_shipping_status_po_id', '', 'requires/order_entry_by_buying_house_controller'));
		    console.log(shipping_status);
		    var d=shipping_status.split("***");
		    if((d[0]*1)==3)
		    {
		      $('#cbo_status').val(d[1]);
		      alert('Shipping Status is Full Delivery/Closed . Any change in status not allowed');
		      return;
		    }
		}
		
	}
	function fnc_poQty_chk(type)
	{
		if(type==1)
		{
		//$('#th_color').css('background-color', 'yellow');
		// document.getElementById('po_msg').innerHTML='Color and Size breakdown is not completed';
		}
		else
		{
			//$('#th_color').css('background-color', '');
			//$('#th_color input,#th_color select').css('background-color', '');
			 document.getElementById('po_msg').innerHTML='';
		}
	}
	function comm_caption_field(type = 1)
	{
		var txt_po_quantity 		= $("#txt_po_quantity").val() * 1;
		var txt_avg_price 			= $("#txt_avg_price").val() * 1;
		var commission_value_type 	= $("#commission_value_type").val() * 1;
		var commission_per_pcs 		= $("#commission_per_pcs").val() * 1;
		var txt_commission 			= $("#txt_commission").val() * 1;
		var txt_foreign_commission 	= $("#txt_foreign_commission").val() * 1;
		var txt_po_unit_price 		= $("#txt_po_unit_price").val() * 1;
		var txt_factory_price 		= $("#txt_factory_price").val() * 1;
		var cbo_agent 		= $("#cbo_agent").val();
		if(commission_value_type == 1)
		{
			$('#commission_per_pcs').removeAttr('readonly');
		}
		else
		{
			$('#commission_per_pcs').attr('readonly',true);
		}
		if(type == 2)
		{
			txt_commission = txt_avg_price - txt_po_unit_price - txt_foreign_commission;
			
			commission_per_pcs = (txt_commission * 100) / txt_avg_price;
			$("#commission_per_pcs").val(number_format_common(commission_per_pcs, 2,0,0)) ;

			$("#txt_commission").val(number_format_common(txt_commission, 2,0,0)) ;
			txt_factory_price = txt_po_unit_price * txt_po_quantity * 1;
			$("#txt_factory_price").val(number_format_common(txt_factory_price, 2,0,0)) ;
		}else if(cbo_agent > 0){
			txt_commission = txt_avg_price - txt_po_unit_price - txt_foreign_commission;
			commission_per_pcs = (txt_commission * 100) / txt_avg_price;
			$("#txt_commission").val(number_format_common(txt_commission, 2,0,0)) ;
			$("#commission_per_pcs").val(number_format_common(commission_per_pcs, 2,0,0)) ;
		}
		else
		{
			txt_commission = (txt_avg_price * commission_per_pcs) / 100;
			$("#txt_commission").val(number_format_common(txt_commission, 2,0,0)) ;

			txt_po_unit_price = txt_avg_price - (txt_commission + txt_foreign_commission);
			txt_factory_price = txt_po_unit_price * txt_po_quantity * 1;

			$("#txt_po_unit_price").val(number_format_common(txt_po_unit_price, 2,0,0)) ;
			$("#txt_factory_price").val(number_format_common(txt_factory_price, 2,0,0)) ;
		}
		console.log('txt_po_unit_price=',txt_po_unit_price,' ; txt_factory_price=',txt_factory_price)
	}

	function oepn_composition_popup()
	{
		var txt_composition = $("#txt_composition").val();
		var txt_job_no 		= $("#txt_job_no").val();
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_entry_by_buying_house_controller.php?action=composition_pop_up&save_data='+txt_composition+'&txt_job_no='+txt_job_no, "Composition Pop Up", 'width=950px,height=470px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var save_data=this.contentDoc.getElementById("save_data").value;
			$("#txt_composition").val(save_data);
		}
	}
	//arrow function with parameter = incoterm
	const load_inco_term_place = incoterm =>{

		if(incoterm == 1)
		{
			$("#txt_inco_term_place").val('BD CHATTOGRAM');
		}
		else if(incoterm == 2)
		{
			$("#txt_inco_term_place").val('BD DHAKA');
		}else if(incoterm == 14)
		{
			$("#txt_inco_term_place").val('POST');
		}
		else
		{
			$("#txt_inco_term_place").val('');
		}
	}
	function load_tenor(buyer)
	{
		var tenor = trim(return_global_ajax_value(buyer, 'load_buyer_wise_payterm', '', 'requires/order_entry_by_buying_house_controller'));
		$("#txt_tenor").val(tenor);
	}

	function open_general_condition_popup(){
        var buyer_id = $('#cbo_buyer_name').val();
        var update_id = $('#update_id').val();
        if(update_id=="")
        {
            alert("Save Job No First");
            return;
        }
		
		var title = 'General Condition';   
        var page_link = 'requires/order_entry_by_buying_house_controller.php?buyer_id='+buyer_id+'&update_id='+update_id+'&action=general_condition_popup';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=700px,height=450px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            //var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
            //var information=this.contentDoc.getElementById("information").value;     //Access form field with id="emailfield"
           // $('#information').val(information);
        }
    }
	function reorder_size_color()
	{
		var txt_job_no=document.getElementById('txt_job_no').value;
		if(txt_job_no=="")
		{
			alert("Please Browse Job.");
			return;
		}
		else
		{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_entry_by_buying_house_controller.php?action=reorder_size_color&txt_job_no='+txt_job_no, 'Color Size Ordering', 'width=700px,height=400px,center=1,resize=1,scrolling=0','../')	
		}
	}


	function call_print_button_for_mail(mail,mail_body,type){		
		var hidd_job_id = $('#hidd_job_id').val();
		var data = return_global_ajax_value( hidd_job_id+'**'+mail+'**'+mail_body, 'send_mail', '', 'requires/order_entry_by_buying_house_controller');
		alert(data);
	}
	

</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
<!-- Important Field outside Form -->
    <?=load_freeze_divs ("../../",$permission,'',false);  ?>
    <fieldset style="width:1070px;">
    <legend>Garments Order Entry</legend>
        <form name="orderentry_1" id="orderentry_1" autocomplete="off">
            <table width="1060" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td width="90">Job No</td>              <!-- 11-00030  -->
                    <td width="140"><input style="width:120px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/order_entry_by_buying_house_controller.php?action=order_popup','Job/Order Selection Form');" class="text_boxes" placeholder="New Job No" name="txt_job_no" id="txt_job_no" readonly />
                        <input type="hidden" name="hidd_job_id" id="hidd_job_id" style="width:30px;" class="text_boxes" />
                    </td>
                    <td width="110" class="must_entry_caption">Company Name</td>
                    <td width="140">
						<?=create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "get_company_config(this.value); location_select(); get_php_form_data( this.value, 'company_wise_report_button_setting','requires/order_entry_by_buying_house_controller' );" ); ?>
                        <input type="hidden" name="po_update_period_maintain" id="po_update_period_maintain"/>
                        <input type="hidden" name="po_current_date_maintain" id="po_current_date_maintain"/>
                        <input type="hidden" name="set_smv_id" id="set_smv_id" />
                        <input type="hidden" name="sewing_company_validate_id" id="sewing_company_validate_id" />
                        <input type="hidden" name="act_po_id" id="act_po_id" value=""/>
                    </td>
                    <td width="110" class="must_entry_caption">Location Name</td>
                    <td width="140" id="location"><?=create_drop_down( "cbo_location_name", 130, $blank_array,"", 1, "-- Select --", $selected, "" ); ?></td>
                    <td width="90">Repeat No/Job</td>
                    <td><input style="width:25px;" class="text_boxes" name="txt_repeat_no" id="txt_repeat_no" />
                    <input style="width:65px;" class="text_boxes" name="txt_repeat_job_no" id="txt_repeat_job_no" onDblClick="repeat_openmypage( 'requires/order_entry_by_buying_house_controller.php?action=repeat_job_popup', 'Repeat Job Form');" placeholder="Browse Job" /> 
                    </td>
                </tr>
                <tr>
                    <td class="must_entry_caption">Buyer Name</td>
                    <td id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- Select Buyer --", $selected, "" ); ?></td>
                    <td class="must_entry_caption">Style Ref.</td>
                    <td><input class="text_boxes" type="text" style="width:120px" placeholder="Double Click for Quotation" name="txt_style_ref" id="txt_style_ref" onDblClick="open_qoutation_popup('requires/order_entry_by_buying_house_controller.php?action=quotation_id_popup','Quotation ID Selection Form');" readonly/></td>
                    <td>Style Description</td>
                    <td><input class="text_boxes" type="text" style="width:120px;" name="txt_style_description" id="txt_style_description"/></td>
                    <td class="must_entry_caption">Prod. Dept.</td>
                    <td><?=create_drop_down( "cbo_product_department", 72, $product_dept, "", 1, "-Select-", $selected, "sub_dept_load(document.getElementById('cbo_buyer_name').value,this.value)", "", "" ); ?>
                        <input class="text_boxes" type="text" style="width:40px;" name="txt_product_code" id="txt_product_code" maxlength="10" title="Maximum 10 Character" />                    </td>
                </tr>
                <tr>
                    <td class="must_entry_caption">Product Category</td>
                    <td><?=create_drop_down( "txt_item_catgory", 130, $product_category,"", 1, "-- Select Product Category --", 1, "","","" ); ?></td>
                    <td>Ship Mode</td>
                    <td><?=create_drop_down( "cbo_ship_mode", 130,$shipment_mode, 1, "", $selected, "" ); ?></td>
                    <td>Region</td>
                    <td><?=create_drop_down( "cbo_region", 130, $region, 1, "-- Select Region --", $selected, "" ); ?></td>
                    <td>Sub. Dept</td>
                    <td id="sub_td"><?=create_drop_down( "cbo_sub_dept", 130, $blank_array,"", 1, "-- Select Sub Dep --", $selected, "" ); ?></td>
                </tr>
                <tr>  	
                	<td>Season<input type="hidden" name="is_season_must" id="is_season_must" style="width:50px;" class="text_boxes" /><?=create_drop_down( "cbo_season_year", 50, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?></td>
                    <td id="season_td"><?=create_drop_down( "cbo_season_name", 130, $blank_array,"", 1, "-- Select Season --", $selected, "" ); ?></td>
                    <td class="must_entry_caption">Brand</td>
                    <td id="brand_td"><?=create_drop_down( "cbo_brand_id", 130, $blank_array,'', 1, "--Brand--",$selected, "" ); ?>
                    <td>Agent</td>
                    <td id="agent_td"><?=create_drop_down( "cbo_agent", 130, $blank_array,"", 1, "-- Select Agent --", $selected, "" ); ?></td>
                    <td>Client</td>
                    <td id="party_type_td"><?=create_drop_down( "cbo_client", 130, $blank_array,"", 1, "-- Select Client --", $selected, "" ); ?></td>
                </tr>
                <tr>
                    <td class="must_entry_caption">Team Leader</td>
                    <td id="div_teamleader"><?= create_drop_down( "cbo_team_leader", 130, "select id,team_leader_name from lib_marketing_team where project_type=1 and status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $teamId, "load_drop_down( 'requires/order_entry_by_buying_house_controller', this.value, 'cbo_dealing_merchant', 'div_marchant' );load_drop_down( 'requires/order_entry_by_buying_house_controller', this.value, 'cbo_factory_merchant', 'div_marchant_factory' ); " );
					?></td>
                    <td class="must_entry_caption">Respective Merchandiser</td>
                    <td id="div_marchant" ><?=create_drop_down( "cbo_dealing_merchant", 130, $blank_array,"", 1, "-- Select Team Member --", $selected, "" ); ?></td>
                    <td class="must_entry_caption">Factory Merchandiser</td>
                    <td id="div_marchant_factory"><?=create_drop_down( "cbo_factory_merchant", 130, "select a.id, a.team_member_name from lib_mkt_team_member_info a, lib_marketing_team b where a.team_id=b.id and b.team_type in (2) and a.status_active =1 and a.is_deleted=0 order by a.team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" ); ?></td>
					<td>Head Of Merchandiser</td>
					<td><input class="text_boxes" type="text" style="width:120px;" name="txt_head_merchandiser" id="txt_head_merchandiser"></td>
                </tr>
                <tr>
                    <td class="must_entry_caption">Order Uom</td>
                    <td><?=create_drop_down( "cbo_order_uom",50, $unit_of_measurement, "",0, "", 1, "open_set_popup(this.value,this.id)","","1,58" ); ?>
                    <input type="button" id="set_button" class="image_uploader" style="width:75px;" value="Item Details" onClick="open_set_popup(document.getElementById('cbo_order_uom').value,this.id)" />                    </td>
                    <td>SMV</td>
                    <td><input class="text_boxes_numeric" type="text" style="width:120px;" name="tot_smv_qnty" id="tot_smv_qnty" readonly/></td>
                    <td>Packing</td>
                    <td><?=create_drop_down( "cbo_packing", 130, $packing,"", 1, "--Select--", $selected, "","","" ); ?></td>
                    <td>Currency</td>
                    <td><?=create_drop_down( "cbo_currercy", 130, $currency,'', 0, "",2, "" ); ?></td>
                </tr>
                <tr>
				<td>Buyer Contact</td>
                    <td>
                    <?
                    $bhArr=array();
                    $sqlBh="select a.id, a.team_member_name from lib_mkt_team_member_info a, lib_marketing_team b where a.team_id=b.id and b.team_type in (3) and a.status_active =1 and a.is_deleted=0 order by a.team_member_name";
                    $sqlBhData=sql_select($sqlBh);
                    foreach($sqlBhData as $row)
                    {
						$bhArr[$row[csf('team_member_name')]]=$row[csf('team_member_name')];
                    }

                    if(count($bhArr)>0)
                    {
						echo create_drop_down( "txt_bhmerchant", 130, $bhArr,"", 1, "-- Select --", "", "" );
                    }
                    else
                    {
						?><input class="text_boxes" type="text" style="width:120px;"  name="txt_bhmerchant" id="txt_bhmerchant"/><?
                    }
                    ?></td>
                    <td width="110">Working Company </td>
                    <td><?=create_drop_down( "cbo_working_company_id", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "get_sew_company_config(this.value);" ); ?></td>
                    <td width="110">Working Location </td>
                    <td id="sew_location"><?=create_drop_down( "cbo_working_location_id", 130, $blank_array,"", 1, "-- Select Location --", $selected, ""); ?></td>
                    <td>Sample Req. No</td>
                    <td><input type="text" name="txt_requision_no" id="txt_requision_no"  onClick="oepn_requision();" class="text_boxes" style="width:120px;" readonly></td>
                </tr>
                <tr>
                    <td>Design Source</td>
                    <td><?=create_drop_down( "cbo_design_source_id", 130, $design_source_arr,"", 1, "-- Select --", "", "" ); ?></td>
                    <td>Quality Label</td>
                    <td><?=create_drop_down( "cbo_qltyLabel", 130, $quality_label,"", 1, "--Quality Label--", $selected, "" ); ?></td>
                    <td>Sustainability Standard</td>
                    <td><?=create_drop_down( "sustainability_standard", 130, $sustainability_standard,"", 1, "-- Select--", 0, "","","" ); ?></td>
                    <td>Fab. Material</td>
                    <td>
						<? 
							$fab_material=array(1=>"Organic",2=>"BCI", 3=>"Regular");
							echo create_drop_down( "cbo_fab_material", 130, $fab_material,"", 1, "-- Select--", 0, "","","" ); 
                        ?>
                    </td>
                    
                    
                </tr>
                <tr>
                    <td>Order Nature</td>
                    <td><?=create_drop_down( "cbo_quality_level", 130, $fbooking_order_nature,"", 1, "-- Select--", 0, "","","" ); ?></td>
                    <td>Care Instructions</td>
                    <td colspan="3"><input class="text_boxes" type="text" style="width:350px;"  name="txt_remarks" id="txt_remarks"/></td>
                    <td>Port of Loading</td>
                    <td><input type="text" name="txt_port_of_loading" style="width:120px" id="txt_port_of_loading" class="text_boxes" maxlength="50" title="Maximum Character 50" /></td>
                </tr>
                <tr>
                	<td>Pay Term</td>
                    <td>
                        <?
                        echo create_drop_down("cbo_pay_term", 70, $pay_term, "", 1, "--- Select ---", 0, "", '', '1,2,9');
                        
                        echo create_drop_down("cbo_ls_sc", 60, $ls_sc, "", 1, "--- Select ---", 0, "", '', '1,2');
                        ?>
                    </td>
                    <td>Tenor</td>
                    <td><input type="text" name="txt_tenor" id="txt_tenor" style="width:120px" class="text_boxes_numeric"  /></td>
                    <td>Incoterm</td>
                    <td>
                        <?
                        echo create_drop_down("cbo_inco_term", 130, $incoterm, "", 0, "", 0, "load_inco_term_place(this.value)");
                        ?>
                    </td>
                    <td>Incoterm Place</td>
                    <td><input type="text" name="txt_inco_term_place" style="width:120px" id="txt_inco_term_place" class="text_boxes" value="" maxlength="50" title="Maximum Character 50"/></td>
                </tr>
                <tr>
					<td>Copy From</td>
					<td><input class="text_boxes" type="text" style="width:120px;" name="txt_copy_form" id="txt_copy_form" disabled/></td>
                	<td>Port of Discharge</td>
                    <td><input type="text" name="txt_port_of_discharge" style="width:120px" id="txt_port_of_discharge" class="text_boxes" maxlength="50" title="Maximum Character 50" /></td>
                    <td >Composition</td>
                    <td>
                    	<input type="text" id="txt_composition" class="text_boxes" style="width:120px;" value="" placeholder="Browse" readonly onClick="oepn_composition_popup();">
                    </td>
                    <td>
                    	<input type="button" class="image_uploader" style="width:120px" value="ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'knit_order_entry', 0 ,1);">
                    </td>
					
                   
                    <td>
                    	<input type="button" id="set_button" class="image_uploader" style="width:120px;" value="Internal Ref" onClick="open_terms_condition_popup('requires/order_entry_by_buying_house_controller.php?action=terms_condition_popup','Terms Condition');" />
                    </td>
                </tr>
				<tr> 
					<td>Ready To App.</td>
					<td><? echo create_drop_down( "cbo_ready_to_approved", 130, $yes_no,"", 1, "-- Select--", 2, "","","" ); ?></td>
					<td>General Condition</td>
					<td>
                        <input type="text" name="general_condition" id="general_condition" style="width:120px" class="text_boxes" placeholder="Click Here" onClick="open_general_condition_popup();"/>
						
                    </td>
					
				</tr>
                <tr>
                    <td align="center" colspan="8" valign="middle" class="button_container" height="30">
                        <input type="hidden" id="update_id">
                        <input type="hidden" id="txt_quotation_id">
                        <input type="hidden" id="txt_quotation_price">
                        <input type="hidden" id="set_breck_down" />
                        <input type="hidden" id="item_id" />
                        <input type="hidden" id="tot_set_qnty" />
                        <input type="hidden" id="hid_cost_source" />
                        <input type="hidden" id="hid_colorQty" />
                        <? //reset_form('orderentry_1*orderdetailsentry_2','deleted_po_list_view*po_list_view','','',$dd)
                        $dd="disable_enable_fields( 'cbo_company_name', '0')";
                        echo load_submit_buttons( $permission, "fnc_order_entry", 0,0,"fnResetForm();",1);?>
						
                    </td>
                </tr>
				<tr>
					<td align="center" colspan="10">
                    	
						<input type="button" id="reorder" value="Color Size Sequence" width="120" class="image_uploader" onClick="reorder_size_color();"/>
						<input class="formbutton" type="button" onClick="fnSendMail('../../','',1,1,0,1)" value="Mail Send" style="width:80px;">
					</td>
				</tr>
            </table>
        </form>
    </fieldset>
    <br>
    <fieldset style="width:1560px;">
    <legend>PO Details Entry</legend>
        <form id="orderdetailsentry_2" autocomplete="off">
            <table style="border:none" width="1535px" cellpadding="0" cellspacing="2" border="0">
                <thead class="form_table_header">
                    <tr>
                        <th width="80" class="must_entry_caption">Order Status</th>
						<th width="80" class="must_entry_caption"> 	Within Group</th>
						<th width="100" class="">LC Company</th>
                        <th width="100" class="must_entry_caption">Working Factory</th>
                        <th width="100" class="must_entry_caption">PO No</th>
                        <th width="70" class="must_entry_caption">PO Received Date</th>
                        <th width="70" class="must_entry_caption">Pub. Shipment Date</th>
                        <th width="70" class="must_entry_caption">Org. Shipment Date</th>
                        <th width="70">Fac. Receive Date</th>
                        <th width="80" class="must_entry_caption">PO Quantity<input type="checkbox" name="with_qty" id="with_qty" value="1" onClick="set_checkvalue();"/> </th>
                        <th width="60" class="must_entry_caption">Unit Price</th>
                        <th width="85">Amount</th>
                        <th width="60">Excess Cut %</th>
                        <th width="70">Plan Cut</th>
                        <th width="70">Com. value type</th>
                        <th width="70">Comm. (%)</th>
                        <th width="70">Local Comm.</th>
                        <th width="70">Foreign Comm.</th>
                        <th width="70" class="must_entry_caption">Factory Price</th>
                        <th width="70">Factory Value</th>
                        <th class="must_entry_caption">Status</th>
                    </tr>
                </thead>
                <tr>
                    <td>
                    	<?=create_drop_down( "cbo_order_status", 80, $order_status,"", 0, "----", 1,"po_recevied_date( this.value )", "","","","" ); ?>
                    	<input name="txt_order_status" id="txt_order_status" type="hidden" value=""  style="width:65px " readonly/>
                    </td>
					<td>
							<? 
							echo create_drop_down("cbo_within_group", 100, $yes_no, "", 0, "--  --", 0, "load_drop_down( 'requires/order_entry_by_buying_house_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_working_company', 'supplier_td' );");
						?>
					</td>
					<td id="lc_company_td">
                    	<?=create_drop_down( "cbo_lc_company_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", "", "","" ,""); ?>
                    </td>
                    <td id="supplier_td">
                    	<?=create_drop_down( "cbo_supplier_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Supplier --", "", "","" ,""); ?>
                    </td>
                    <td>
                    	<input class="text_boxes" name="txt_po_no" id="txt_po_no" type="text" value=""  style="width:90px"/>
                    </td>
                    <td>
                    	<input name="txt_po_received_date" id="txt_po_received_date" class="datepicker" type="text" onChange="set_tna_task();" style="width:60px;" readonly/>
                    </td>
                    <td>
                    	<input name="txt_pub_shipment_date" id="txt_pub_shipment_date" class="datepicker" type="text" onChange="set_tna_task();" style="width:60px;" readonly/>
                    </td>
                    <td>
                    	<input name="txt_org_shipment_date" id="txt_org_shipment_date" class="datepicker" type="text" style="width:60px;" onChange="set_pub_ship_date();" readonly/>
                    </td>
                    <td>
                    	<input  name="txt_factory_rec_date" id="txt_factory_rec_date" class="datepicker" type="text" style="width:60px;" value="" readonly/>
                    </td>
                    <td>
                        <input name="txt_po_quantity" id="txt_po_quantity"  class="text_boxes_numeric" type="text" style="width:70px" onBlur="set_excess_cut(this.value,document.getElementById('txt_excess_cut').value);" onChange="validate_po_qty();" onkeyup="comm_caption_field(2);"  />
                        <input type="hidden" id="with_qty_pop" name="with_qty_pop" value="1"/>
                        <input type="hidden" id="hidden_po_qty" name="hidden_po_qty" value=""/>
                    </td>
                    <td>
                    	<input name="txt_avg_price" onkeyup="comm_caption_field(2);" id="txt_avg_price" onBlur="math_operation( 'txt_amount', 'txt_avg_price*txt_po_quantity', '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} ); fnc_avg_price_check(this.value);" quot_cost="" class="text_boxes_numeric" type="text" style="width:50px" />
                    </td>
                    <td>
                    	<input name="txt_amount" id="txt_amount" class="text_boxes_numeric" type="text" value=""  style="width:75px" readonly/>
                    </td>
                    <td>
                    	<input name="txt_excess_cut" id="txt_excess_cut" class="text_boxes_numeric" onChange="calculate_plan_cut();" style="width:50px" disabled/>
                    </td>

                    <td>
                    	<input name="txt_plan_cut" id="txt_plan_cut"  class="text_boxes_numeric" type="text" value=""  style="width:60px " readonly/>
                    </td>
                    <!-- // -->
                    <td>
                    	<?
                    		$commision_type = array(1=>"Percentage",2=>"Amount");
                    		echo create_drop_down( "commission_value_type", 80, $commision_type,"", 0, "--Select--", 2,"comm_caption_field( this.value )", "","","","" );
                    	?>
                    </td>
                    <td>
                    	<input name="commission_per_pcs" id="commission_per_pcs"  class="text_boxes_numeric" type="text" value="" onkeyup="comm_caption_field();"  style="width:60px " />
                    </td>
                    <td>
                    	<input name="txt_commission" id="txt_commission" onkeyup="comm_caption_field();" class="text_boxes_numeric" type="text" value=""  style="width:60px " />
                    </td>
                    <td>
                    	<input name="txt_foreign_commission" id="txt_foreign_commission" onkeyup="comm_caption_field();" class="text_boxes_numeric" type="text" value=""  style="width:60px " />
                    </td>
                    <td>
                    	<input name="txt_po_unit_price" id="txt_po_unit_price"  class="text_boxes_numeric" type="text" value=""  style="width:60px " onkeyup="comm_caption_field(2);" />
                    </td>
                    <td>
                    	<input name="txt_factory_price" id="txt_factory_price"  class="text_boxes_numeric" type="text" value=""  style="width:60px " readonly/>
                    </td>
                    <!-- // -->
                    <td>
                    	<?=create_drop_down("cbo_status", 80, $row_status, 0, "", 1, "","check_po_shipping_status()"); ?>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td align="right">
                    	<strong>Projected Po</strong>
                    </td>
                    <td id="projected_po_td">
                    	<?=create_drop_down("cbo_projected_po", 100, $blank_array, "", 1, "--Select--", ""); ?>
                    </td>
                    <td align="right" colspan="2">
                    	<strong>TNA From /Upto</strong>
                    </td>
                    <td id="tna_task_td" colspan="2">
                    	<?=create_drop_down("cbo_tna_task", 70, $blank_array, "", 1, "--Select--", ""); ?>
                    </td>
                    <td></td>
                   
                    
                    <td align="right" colspan="2">
                    	<strong>Internal Ref/Grouping</strong>
                    </td>
                    <td>
                    	<input type="text" id="txt_grouping" class="text_boxes" style="width:70px">
                    </td>
                    <td align="right">
                    	<strong>Delay For</strong>
                    </td>
                    <td colspan="2">
                    	<?=create_drop_down("cbo_delay_for", 150, $delay_for, 0, "", 1, ""); ?>
                    </td>
                </tr>
                <tr>
                    <td align="right"><strong>Packing</strong> </td>
                    <td><?=create_drop_down( "cbo_packing_po_level", 100, $packing,"", 1, "--Select--", "", "","","" ); ?></td>
                    <td align="right"><strong> &nbsp;</strong></td>
                    <td align="right"><strong> &nbsp;</strong></td>
                    <td ><input type="button" id="fileupload" value="File Add" class="image_uploader" style="width:70px"  onClick="file_uploader_popup(2);"/></td>
                    <td align="right"><input type="button" id="actualpo" value="Actual Po No" class="image_uploader" style="width:70px" onClick="pop_entry_actual_po();"/></td>
                    <td colspan="2"><strong>File No</strong><input type="text" id="txt_file_no" name="txt_file_no" class="text_boxes" style="width:50px"></td>
                   
                    <td >ETD/LDD</td>
                    <td><input name="txt_etd_ldd" id="txt_etd_ldd" class="datepicker" style="width:50px" placeholder="Date"></td>
                    <td align="right" width="30"><strong>SC/LC</strong></td>
                    <td><input type="text" id="txt_sc_lc" name="txt_sc_lc" class="text_boxes" style="width:70px"></td>
                </tr>
                <tr>
                    <td align="right"><strong>Remarks</strong> </td>
                    <td colspan="11"><input type="text" id="txt_details_remark" class="text_boxes" style="width:800px"></td>
                </tr>
                <tr>
                    <td colspan="12" valign="middle" align="center" class="button_container">
                        <input type="hidden" id="update_id_details" />
                        <input type="hidden" id="txt_hidden_color_qty" style="width:80px" />
                        <input type="hidden" id="color_size_break_down" value="" />
                        <input type="hidden" id="txt_po_datedif_hour" />
                        <input type="hidden" id="txt_user_id" />
                        <input type="hidden" id="chk_extended_ship_date"  />
                        <?
                        $dd="disable_enable_fields( 'txt_avg_price*cbo_company_name*cbo_order_status*txt_po_received_date*txt_po_no*txt_pub_shipment_date*txt_org_shipment_date*txt_factory_rec_date*chk_extended_ship_date*txt_etd_ldd*txt_excess_cut*txt_details_remark*cbo_status*cbo_packing_po_level*show_textcbo_delay_for*txt_grouping*cbo_projected_po*cbo_tna_task*txt_file_no*txt_sc_lc*with_qty*fileupload*actualpo*txt_amount*txt_plan_cut', 0,0)";
                        echo load_submit_buttons( $permission, "fnc_order_entry_details", 0,0 ,"reset_form('orderdetailsentry_2','','','',$dd)",2);
                        ?>
                        <br>
                        <p style="color:#FF0000" id="po_msg"></p>

                    </td>
                </tr>
                <tr align="center">
                	<td colspan="12" id="po_list_view"></td>
                </tr>
                <tr align="center">
                	<td colspan="12" id="deleted_po_list_view"></td>
                </tr>
                <tr align="center">
                    <td colspan="12">
                        <table>
                            <tr>
                                <td width="128" colspan="2">Initial Projected Job Qty.</td>
                                <td width="180"><input name="txt_projected_job_quantity" id="txt_projected_job_quantity" style="width:103px " class="text_boxes_numeric" readonly />
                                <?=create_drop_down( "pojected_set_pcs",60, $unit_of_measurement, "",1, "--", "", "",1,"1,58" ); ?>
                                </td>
                                <td>Initial Projected Avg Unit Price</td>
                                <td colspan="2">
                                <div><input name="txt_projected_price" type="text" class="text_boxes_numeric" id="txt_projected_price" style="width:85px; text-align:right " value="<? //echo $txt_unit_price; ?>" readonly />&nbsp;&nbsp;
                                	<?=create_drop_down( "projected_set_unit", 60, $currency,"", 1, "--", "", "" ,1,"");  ?>
                                </div>
                                </td>
                                <td>Initial Projected Total Price </td>
                                <td><input type="text" style="width:140px "  class='text_boxes_numeric' name="txt_project_total_price" id="txt_project_total_price" value="<? echo $txt_total_price; ?>" readonly /></td>
                            </tr>
                            <tr>
                                <td width="128" colspan="2">&nbsp;Projected Job Quantity</td>
                                <td width="180"><input name="txt_currprojected_job_qnty" id="txt_currprojected_job_qnty" style="width:103px " class="text_boxes_numeric" readonly />
                                <?=create_drop_down( "currpojected_set_pcs",60, $unit_of_measurement, "",1, "--", "", "",1,"1,58" ); ?>
                                </td>
                                <td>&nbsp;&nbsp;Projected Avg Unit Price</td>
                                <td colspan="2">
                                <div><input name="txt_currprojected_price" type="text" class="text_boxes_numeric" id="txt_currprojected_price" style="width:85px; text-align:right " value="<? //echo $txt_unit_price; ?>" readonly />&nbsp;&nbsp;
                                <?=create_drop_down( "currprojected_set_unit", 60, $currency,"", 1, "--", "", "" ,1,"");  ?>
                                </div>
                                </td>
                                <td>&nbsp;Projected Total Price </td>
                                <td><input type="text" style="width:140px "  class='text_boxes_numeric' name="txt_currproject_total_price" id="txt_currproject_total_price" value="<?=$txt_total_price; ?>" readonly /></td>
                            </tr>
                            <tr>
                                <td width="128" colspan="2">&nbsp;Job Quantity</td>
                                <td width="180"><input name="txt_total_job_quantity" id="txt_total_job_quantity" style="width:103px " class="text_boxes_numeric" readonly />
                                	<?=create_drop_down( "set_pcs",60, $unit_of_measurement, "",1, "--", "", "",1,"1,58" ); ?></td>
                                <td>&nbsp;&nbsp;Avg Unit Price</td>
                                <td colspan="2">
                                <div><input name="txt_avg_unit_price" type="text" class="text_boxes_numeric" id="txt_avg_unit_price" style="width:85px; text-align:right" value="<? echo $txt_unit_price; ?>" readonly />&nbsp;&nbsp;<?=create_drop_down( "set_unit", 60, $currency,"",1, "--", "", "" ,1,"");?></div>
                                </td>
                                <td>&nbsp;Total Price </td>
                                <td><input type="text" style="width:140px" class='text_boxes_numeric' name="txt_job_total_price" id="txt_job_total_price" value="<? echo $txt_total_price; ?>" readonly /></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                	<td colspan="12" align="center">
						<? echo create_drop_down( "cbo_template_id", 90, $report_template_list,'', 0, '', 0, ""); ?>
                		<input type="button" name="print_button" onclick="fnc_po_pop(1)" value="Print" id="print_button" class="formbutton" style="width:70px; display:none;">
						<input type="button" name="print_button2" onclick="fnc_po_pop(2)" value="Print2" id="print_button2" class="formbutton" style="width:70px; display:none;">
                	</td>
                </tr>
            </table>
        </form>
    </fieldset>
	</div>
</body>
<script>
	location_select(); set_multiselect('cbo_delay_for','0','0','','');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>