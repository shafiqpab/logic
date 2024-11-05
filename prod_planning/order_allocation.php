<?  
/*--------------------------------------------Comments----------------
Version (MySql)          :  
Version (Oracle)         :  
Converted by             :  
Converted Date           :  
Purpose			         : 	This form will create Order Allocation Entry
Functionality	         :	
JS Functions	         :
Created by		         :	Kaiyum 
Creation date 	         : 	5-10-2016
Requirment Client        : 
Requirment By            : 
Requirment type          : 
Requirment               : 
Affected page            : 
Affected Code            :                   
DB Script                : 
Updated by 		         : 	 
Update date		         : 		   
QC Performed BY	         :		
QC Date			         :	
Comments		         :
----------------------------------------------------------------------*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Order Allocation","../", 1, 1, $unicode,1,'');
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";  
	var permission='<? echo $permission; ?>';

	function set_value_po_qty(po_id)
	{
		var data=po_id+'__'+$("#cbo_item").val();
		var list_view_wo =return_global_ajax_value( data, 'populate_data_wo_form', '', 'requires/order_allocation_controller') ;
		var res=trim(list_view_wo).split("_");
		$("#txt_po_qty").val(res[0]);
		$("#txt_allocated_qty").val(res[0]);
		$("#txt_shipment_date").val(res[1]);
		var txt_total_smv = $("#tot_smv_qnty").val() * res[0];
		$("#txt_total_smv").val(txt_total_smv);
	}
	function set_cut_po_qty(cut_date)
	{
		var po_id=$("#cbo_po_no").val();
		var item_id=$("#cbo_item").val();
		var data=cut_date+'_'+po_id+'_'+item_id;
		get_php_form_data(data, "populate_data_po_cut_off_form", "requires/order_allocation_controller" );
		//var list_view_wo =return_global_ajax_value( data, 'populate_data_po_cut_off_form', '', 'requires/order_allocation_controller') ;
		//var res=trim(list_view_wo).split("_");
		//$("#txt_po_qty").val(res[0]);
		//$("#txt_shipment_date").val(res[1]);
	}

	function set_value_smv(dt,job)
	{
		var data=dt+"__"+job;
		$("#tot_smv_qnty").val('');	
		var list_view_smv =return_global_ajax_value( data, 'populate_data_smv_form', '', 'requires/order_allocation_controller') ;
		//alert(list_view_smv);
		var res=trim(list_view_smv).split("_");
		$("#tot_smv_qnty").val(res[0]);	
		if($("#cbo_po_no").val()!=0)
		{
			set_value_po_qty( $("#cbo_po_no").val() )
		}
	}

	function openmypage(page_link,title)
	{
		var garments_nature=document.getElementById('garments_nature').value;
		page_link=page_link+'&garments_nature='+garments_nature;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=400px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_job");
			//alert(theemail.value);
			if (theemail.value!="")
			{
				get_php_form_data(theemail.value, "populate_data_from_search_popup", "requires/order_allocation_controller" );
				show_list_view ( theemail.value, 'order_allocation_list_view', 'section_list_view', 'requires/order_allocation_controller', 'setFilterGrid(\'list_views\',-1)')
				release_freezing();
			}
		}
	}

	function clear_input_boxes()
	{
		$("#cbo_po_no").val('');
		$("#txt_po_qty").val('');
		$("#txt_allocated_qty").val('');
		$("#txt_total_smv").val('');
		$("#txt_shipment_date").val('');
		$("#txt_date_from").val('');	
		$("#txt_date_to").val('');
		$("#txt_popup_no").val('');
		$("#cbo_item").val('');
	}
 
	function get_buyer_config(buyer_id)
	{
		load_drop_down('requires/order_allocation_controller', buyer_id+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_season_buyer', 'season_td');
	}

	function open_date_wise_distr_popup(page_link,title)
	{
		var date_form=document.getElementById('txt_date_from').value;	
		var date_to=document.getElementById('txt_date_to').value;
		var txt_allocated_qty=document.getElementById('txt_allocated_qty').value;
		var tot_smv_qnty=document.getElementById('tot_smv_qnty').value;
		var dist_data=document.getElementById('hidden_row_pop').value;
		//var garments_nature=document.getElementById('garments_nature').value;
		page_link=page_link + "&date_form="+date_form+"&date_to="+date_to+"&txt_allocated_qty="+txt_allocated_qty+"&tot_smv_qnty="+tot_smv_qnty+'&dist_data='+dist_data;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=300px,height=450px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var data_dtls_row=this.contentDoc.getElementById("hidden_distr_month_row").value;
			data_dtls_row = data_dtls_row.slice(0, -1);
			document.getElementById('hidden_row_pop').value = data_dtls_row;
			document.getElementById('txt_popup_no').value = data_dtls_row;
		}
	}

	function show_unallocated_jobs()
	{
		if($('#cbo_company_mst').val()==0) { alert('Please select Company'); return; } 
		var data=$('#cbo_company_mst').val()+"__"+$('#cbo_location_name').val();
		show_list_view ( data, 'list_view_popup', 'un_allocated_job', 'requires/order_allocation_controller', 'setFilterGrid(\'list_view\',-1)')
	}

	function total_smv()
	{
		var smv_qty 		=$("#tot_smv_qnty").val()*1;  
		var allocated_qty =$("#txt_allocated_qty").val()*1;
		var cal_total_smv = (smv_qty*allocated_qty);
		$("#txt_total_smv").val(cal_total_smv);
		job_qty_bln();
		$("#hidden_row_pop").val('');
	}

	function job_qty_bln()
	{
		var list_view_qty =return_global_ajax_value( $("#txt_job_no").val()+'__'+$("#update_id").val(), 'load_allocated_qty', '', 'requires/order_allocation_controller') ;
		//alert(list_view_qty)
		var allocated_qtyy =$("#txt_allocated_qty").val()*1;
		var total_job_qtyy =$("#txt_total_job_quantity").val()*1;
		var balance_job_qty=(total_job_qtyy-(allocated_qtyy+(list_view_qty*1)));
		$("#txt_qty_balance").val(balance_job_qty);
	}
	
	//save operation
	function fnc_order_allocation_entry(operation)
	{ 
		if (form_validation('cbo_po_no*txt_style*cbo_item*txt_job_no*cbo_company_mst*cbo_location_name*cbo_po_cut_date*txt_popup_no',' PO No*Style*Item*Job Number*Company Name*Location* Cut Off Date*Date Wise Distribute')== false)
		{
			return; 
		}
		
		if( ($("#txt_po_qty").val()*1)<($("#txt_allocated_qty").val()*1))
		{
			alert("Allocated Quantity greater than PO Quantity is not Allowed");
			return;
		}
		
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_job_no*txt_qty_balance*cbo_company_mst*cbo_location_name*cbo_item*cbo_complexity*tot_smv_qnty*tot_c_smv_qnty*txt_po_qty*txt_shipment_date*txt_allocated_qty*txt_total_smv*txt_date_from*txt_date_to*cbo_po_no*hidden_row_pop*update_id*txt_total_job_quantity*cbo_po_cut_date',"../");
		//alert(data);
		freeze_window(operation);
		http.open("POST","requires/order_allocation_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_section_response;
	}

	function fnc_section_response()
	{
		if(http.readyState == 4)
		{
			var reponse=http.responseText.split('**');
			 if(reponse[0]==31)
			 {
				alert("Allocated Quantity greater than PO Quantity is not Allowed");
				release_freezing();
				return; 
			 }
			show_msg(trim(reponse[0]));
			 if(reponse[0]==1 || reponse[0]==0)
			 {
			 show_list_view ($("#txt_job_no").val(), 'order_allocation_list_view', 'section_list_view', 'requires/order_allocation_controller', 'setFilterGrid(\'list_views\',-1)')
			//show_list_view( $("#txt_job_no").val(),'section_list_view_action','section_list_view','requires/order_allocation_controller','setFilterGrid("list_views",-1)');
			$("#update_id").val('');
			 clear_input_boxes();
			 set_button_status(0, permission, 'fnc_order_allocation_entry',1);
			 }
			
			release_freezing();
		}
	}
	
	function fnc_change_dist()
	{
		$("#hidden_row_pop").val('');
		$("#txt_popup_no").val(''); 
	}

	var row_color=new Array();
	var lastid='';
	function change_color_tr(v_id,e_color)
	{
		if(lastid!='') $('#tr_'+lastid).attr('bgcolor',row_color[lastid])
		 
		if( row_color[v_id]==undefined ) row_color[v_id]=$('#tr_'+v_id).attr('bgcolor');
		
		if( $('#tr_'+v_id).attr('bgcolor')=='#FF9900')
				$('#tr_'+v_id).attr('bgcolor',row_color[v_id])
			else
				$('#tr_'+v_id).attr('bgcolor','#FF9900')
		
		lastid=v_id;
	}
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
<!-- Important Field outside Form -->  
	<? echo load_freeze_divs ("../",$permission);  ?>
    <form name="orderallocation_1" id="orderallocation_1" autocomplete="off">
        <fieldset style="width:1000px;">
        <legend> Order Allocation Info  <span style="margin-left:510px;"> Un Allocated Job &nbsp; &nbsp;<input type="button" name="list_view_button" id="list_view_button" onClick="show_unallocated_jobs()" value="Show Styles" class="formbutton" style="width:145px;"></span> </legend>
        <div style="width: 450px; float: left;">
	        <table width="450" cellspacing="2" cellpadding="0" border="0">
	        	<tr>
	        		<td width="80" class="must_entry_caption" colspan="2" style="text-align:right;">Job No</td>
	                <td width="120" colspan="2">
	                	<input style="width:110px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/order_allocation_controller.php?action=order_popup','Job/Order Selection Form')" class="text_boxes" placeholder="Browse Job No" name="txt_job_no" id="txt_job_no" readonly  />                               
	                </td>

	        	</tr>
	            <tr>                
	                <td width="80">Company Name</td>
	                <td width="120"> <? echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, " ",1 );?> 
	                </td>
	                <td width="80">Buyer Name</td>
	                <td id="buyer_td" width="120"><? echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1 ); ?>	  
	                </td>
	                
	            </tr>
	            <tr>                
	                <td>Team Leader</td>
	                <td>
	                	<? echo create_drop_down( "cbo_team_leader", 120, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, "",1);
	                ?>	
	                </td>
	                <td>Dealing Merchant</td>
	                <td id="marchant_td"><? echo create_drop_down( "cbo_dealing_merchant", 120, "select id,team_member_name from lib_mkt_team_member_info where   status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "",1 ); ?>	  
	                </td>
	            </tr>
	            <tr>                
	                <td>Prod. Dept </td>
	                <td><? echo create_drop_down( "cbo_product_department", 70, $product_dept, "", 1, "-Select-", $selected, "", 1, "" ); ?>
	                	<input class="text_boxes" type="text" style="width:30px;" name="txt_product_code" id="txt_product_code" maxlength="10" title="Maximum 10 Character" disabled />	  
	                </td>
	                <td>Job Qty</td>
	                <td><input  value="" name="txt_total_job_quantity" id="txt_total_job_quantity" style="width:35px " class="text_boxes_numeric" readonly disabled/>&nbsp;Bal&nbsp;<input class="text_boxes_numeric" type="text" style="width:35px;" name="txt_qty_balance" id="txt_qty_balance" maxlength="10" title="Maximum 10 Character"  disabled/>	  
	                </td>
	            </tr>
	            <tr>                
	                <td>Season</td>
	                <td id="season_td">
	                	<? echo create_drop_down( "cbo_season_name", 120, "select a.id,a.season_name from lib_buyer_season a,variable_order_tracking b where a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0   and b.season_mandatory=1 and b.variable_list=44","id,season_name", 1, "-- Select Season --", $selected, "",1 ); ?>           
	                </td>
	                <td>Entry Date</td>
	                <td><input  value="" name="txt_entry_date" id="txt_entry_date" style="width:110px " class="text_boxes" readonly disabled/></td>
	            </tr>
	        </table>
    	</div>
        <div id="un_allocated_job" style="width: 500px; float: left;"></div>
        </fieldset>
        
        <fieldset style="width:1100px;">
        <legend> Order Allocation Entry </legend>
            <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" width="100%" rules="all">
                <thead>  
                    <th class="must_entry_caption">PO No</th> 
                    <th class="must_entry_caption">Style</th> 
                    <th class="must_entry_caption">Item</th>           	 
                    <th class="must_entry_caption">Company Name</th>
                    <th class="must_entry_caption">Location</th>
                    <th>Complexity</th>
                    <th>SMV</th>
                     <th title="Customize SMV">C.SMV</th>
                    <th class="must_entry_caption">Cut Off Date</th>
                    <th>PO Qty</th>
                    <th>Allocated Qty</th>
                    <th>Total SMV</th>
                    <th>Shipment Date</th>
                    <th colspan="2">Sewing Date Range</th>
                    <th class="must_entry_caption">Date Wise Distribution</th>        
                </thead>
                <tbody>
                    <tr>
                        <td id="po_td"><? echo create_drop_down( "cbo_po_no", 120, $blank_array,"",1, "-- Select Po --", $selected, "" ); ?></td>
                        <td><input name="txt_style"  id="txt_style" class="text_boxes" style="width:70px" readonly></td> 
                        
                        <td id="gmts_item_td"><? echo create_drop_down( "cbo_item", 140, $garments_item, "",1," -- Select Item --", 0); ?></td>
                        <td><? echo create_drop_down( "cbo_company_mst", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $selected, "load_drop_down( 'requires/order_allocation_controller', this.value, 'load_location', 'location_td' ) " ); ?></td>
                        <td id="location_td"><? echo create_drop_down( "cbo_location_name", 130, "select location_name,id from lib_location where is_deleted=0  and status_active=1 order by location_name",'id,location_name', 1, '--- Select Location ---', 0, "" ); ?></td>
                        <td id="complexity_td"><? echo create_drop_down( "cbo_complexity", 80, $complexity_level,"", 1, "-- Select Complexity --", 0, "","","" ); ?></td>
                        <td><input class="text_boxes_numeric" type="text" style="width:30px;" name="tot_smv_qnty" id="tot_smv_qnty" readonly onKeyUp="total_smv();"/></td>
                        <td><input class="text_boxes_numeric" type="text" style="width:30px;" name="tot_c_smv_qnty" id="tot_c_smv_qnty"  /></td>
                         
                        <td id="cut_off_td"><? echo create_drop_down( "cbo_cut_off_date", 80, $blank_array,"", 1, "-- Select Cut Off --", 0, "","","" ); ?></td>
                        <td title="Cut off date wise PO qty"><input name="txt_po_qty"  id="txt_po_qty" class="text_boxes_numeric" style="width:50px" readonly></td>
                        <td><input name="txt_allocated_qty"  id="txt_allocated_qty" class="text_boxes_numeric" style="width:50px" onBlur="total_smv();" onClick="fnc_change_dist();"></td> 
                        <td><input name="txt_total_smv"  id="txt_total_smv" class="text_boxes_numeric" style="width:50px" readonly></td> 
                        <td><input name="txt_shipment_date" id="txt_shipment_date" readonly class="datepicker" style="width:45px;"></td> 
                        <td><input name="txt_date_from" id="txt_date_from" readonly class="datepicker" style="width:50px" onClick="fnc_change_dist();">
                        </td>
                        <td>
                        	<input name="txt_date_to" id="txt_date_to" readonly class="datepicker" style="width:50px" onClick="fnc_change_dist();">
                        </td>                     
                        <td><input style="width:50px;"  title="Click to Date Wise Distribution" onClick="open_date_wise_distr_popup('requires/order_allocation_controller.php?action=date_wise_distr_popup','Date wise distribution')" class="text_boxes" placeholder="Popup" name="txt_popup_no" id="txt_popup_no" readonly /> 
                        </td>
                    </tr>
                    <tr>
                        <input type="hidden" id="update_id"> 
                        <input type="hidden" id="hidden_row_pop">
                    </tr>
                    <tr>
                        <td align="center" height="40" valign="middle" colspan="15">
							<? echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "--Select--", date('Y'), "",0 );		
                            echo load_month_buttons(); ?>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="16" valign="middle" class="button_container">
                        	<? echo load_submit_buttons( $permission, "fnc_order_allocation_entry", 0,0 ,"reset_form('orderallocation_1','','')"); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
			<div style="width:1200px;" id="section_list_view" align="left"></div>
    </form>
	</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>