<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Knit Garments Order Entry
Functionality	:	
JS Functions	:
Created by		:	Monzu 
Creation date 	: 	13-10-2012
Updated by 		: 	Zakaria Joy	
Update date		: 	12-01-2021
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
echo load_html_head_contents("Color Size Entry", "../../", 1, 1,$unicode,'','');

?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	
	var permission='<? echo $permission; ?>';
	
	var str_size = [<? echo substr(return_library_autocomplete( "select size_name from  lib_size", "size_name"  ), 0, -1); ?>];
	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color", "color_name"  ), 0, -1); ?>];
	function loaddata_by_poid(po_id)
	{
		freeze_window(5);
		get_php_form_data( po_id, "populate_data_from_search_popup", "requires/size_color_breakdown_controller" );
		show_list_view(po_id,'populate_size_color_breakdown','size_color_breakdown','../woven_order/requires/size_color_breakdown_controller','');
		load_drop_down( 'requires/size_color_breakdown_controller',document.getElementById('item_id').value, 'load_drop_down_item', 'item_td' )
		$("#colse_1").css("visibility", "hidden")
		$("#colse_2").css("visibility", "hidden")
		release_freezing();
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
	function openmypage(page_link,title)
	{
		hide_left_menu("Button1");
		var garments_nature=document.getElementById('garments_nature').value;
	    page_link=page_link+'&garments_nature='+garments_nature;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theemail=this.contentDoc.getElementById("po_id") //Access form field with id="emailfield"

			if (theemail.value!="")
			{
				freeze_window(5);
				var job_no = return_global_ajax_value( theemail.value, 'get_jobno_by_poid', '', 'requires/size_color_breakdown_controller');
				get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/size_color_breakdown_controller" );
				show_list_view(theemail.value,'populate_size_color_breakdown','size_color_breakdown','../woven_order/requires/size_color_breakdown_controller','');
				show_list_view(job_no+'*'+theemail.value,'order_listview','job_po_list_view','requires/size_color_breakdown_controller','setFilterGrid(\'tbl_po_list\',-1)');
				load_drop_down( 'requires/size_color_breakdown_controller',document.getElementById('item_id').value, 'load_drop_down_item', 'item_td' )
				//document.getElementById('colse_1').value="Update Po";
				$("#colse_1").css("visibility", "hidden")
				$("#colse_2").css("visibility", "hidden")
				release_freezing();
			} 
		}
	}	
	function fnc_size_color_breakdown( operation )
	{
		var row_num=$('#size_color_break_down_list tr').length-1;
		var data_all="";
		var po_qnty="";
		var total=(document.getElementById('total').value)*1;
		var cbo_order_uom=document.getElementById('cbo_order_uom').value;
		var tot_set_qnty=(document.getElementById('tot_set_qnty').value)*1;
		var txt_total_order_qnty=(document.getElementById('txt_total_order_qnty').value)*1;
		var order_id=document.getElementById('order_id').value;
		var hidd_job_id=document.getElementById('hidd_job_id').value;
		var cbo_po_country=document.getElementById('cbo_po_country').value;
		var hid_old_country=document.getElementById('hid_old_country').value;
		var cbo_buyer_name= document.getElementById('cbo_buyer_name').value
		//alert(operation)
		if(operation==2)
		{
			var cutting_qty=return_global_ajax_value(order_id+"_"+cbo_po_country, 'get_cutting_qty_country', '', 'requires/size_color_breakdown_controller');
			if(cutting_qty>0){
			alert("Production found; So delete not allowed");
			return;
			}
		}
		if(operation==1 || operation==2)
		{
			var po_id=order_id;
			var txt_job_no=document.getElementById('txt_job_no').value;
			if($('#cbo_order_status').val()==1)//issue id 12070
			{
				var booking_no_with_approvet_status = return_ajax_request_value(txt_job_no+"_"+po_id, 'booking_no_with_approved_status', 'requires/woven_order_entry_controller')
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
					var al_magg=" Main Fabric Un-Approved Booking No "+booking_no_with_approvet_status_arr[1]+" Found\n If you update this job\n You have to update  Pre-cost and booking against this Job ";
					var r=confirm(al_magg);
					if(r==false) return;
				}
			}
		}		
		if(cbo_order_uom==58) po_qnty=total*tot_set_qnty; else po_qnty=total;		
		//alert(inserted_po_qnty_arr[0])
		if(operation==0)
		{
			var inserted_po_qnty=return_global_ajax_value(order_id+'_'+cbo_po_country, 'inserted_po_qnty', '', 'requires/size_color_breakdown_controller');
		    var inserted_po_qnty_arr=inserted_po_qnty.split("_");
			if((inserted_po_qnty_arr[0]*1+txt_total_order_qnty*1)>po_qnty*1)
			{
			alert("Break Down Qnty Does Not Match with Order Qnty In Pcs");
			return;
			}
			
		}
		
		if(operation==1)
		{
			var inserted_po_qnty=return_global_ajax_value(order_id+'_'+hid_old_country, 'inserted_po_qnty', '', 'requires/size_color_breakdown_controller');
		    var inserted_po_qnty_arr=inserted_po_qnty.split("_");
			if((((inserted_po_qnty_arr[0]*1+txt_total_order_qnty*1)-inserted_po_qnty_arr[1]*1))>po_qnty)
			{
			alert("Break Down Qnty Does Not Match with Order Qnty In Pcs");
			return;
			}
			
		}
		for (var i=1; i<=row_num; i++)
		{
			if (form_validation('cbo_po_country*txt_country_ship_date*cbogmtsitem_'+i+'*txtcolor_'+i+'*txtsize_'+i+'*txtorderquantity_'+i+'*txtorderrate_'+i,'Country*Company Name*Location Name*Buyer Name*Style Ref*Product Department*Item Catagory*Dealing Merchant*Packing')==false)
			{
				return;
			}
			if($('#txtorderrate_'+i).val()==0)
			{
				alert("Fill Up Rate");	
				$('#txtorderrate_'+i).focus();
				return;
			}
			eval(get_submitted_variables('txt_job_no*hidd_job_id*order_id*txt_tot_avg_rate*txt_tot_amount*cbo_order_uom*tot_set_qnty*txt_tot_excess_cut*txt_tot_plancut*cbo_po_country*txt_country_ship_date*hiddenid_'+i+'*cbogmtsitem_'+i+'*txtarticleno_'+i+'*txtcolor_'+i+'*txtsize_'+i+'*txtorderquantity_'+i+'*txtorderrate_'+i+'*txtorderamount_'+i+'*txtorderexcesscut_'+i+'*txtorderplancut_'+i+'*cbostatus_'+i));
			
			data_all=data_all+get_submitted_data_string('txt_country_ship_date*txt_cutup_date*cbo_cut_up*txt_country_remarks*cbo_po_country_type*cbo_packing_country_level*cbogmtsitem_'+i+'*hiddenid_'+i+'*txtarticleno_'+i+'*txtcolor_'+i+'*txtsize_'+i+'*txtorderquantity_'+i+'*txtorderrate_'+i+'*txtorderamount_'+i+'*txtorderexcesscut_'+i+'*txtorderplancut_'+i+'*cbostatus_'+i,"../../",i);
		}
		var is_po_levelqty_update=1;
		var data="action=save_update_delete&operation="+operation+'&total_row='+row_num+'&txt_job_no='+txt_job_no+'&hidd_job_id='+hidd_job_id+'&order_id='+order_id+'&txt_avg_rate='+txt_tot_avg_rate+'&txt_total_amt='+txt_tot_amount+'&cbo_order_uom='+cbo_order_uom +'&tot_set_qnty='+tot_set_qnty+'&txt_avg_excess_cut='+txt_tot_excess_cut+'&txt_total_plan_cut='+txt_tot_plancut+'&cbo_po_country='+cbo_po_country+'&hid_old_country='+hid_old_country+'&cbo_buyer_name='+cbo_buyer_name+'&is_po_levelqty_update='+is_po_levelqty_update+data_all;
		//'&txt_country_ship_date='+txt_country_ship_date+
		freeze_window(operation);
		http.open("POST","requires/size_color_breakdown_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_on_submit_reponse;
	}	
	function fnc_on_submit_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=http.responseText.split('**');
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not  Approved. Please Approved the Quotation");
				release_freezing();
				return;
			}
			if(trim(reponse[0]) ==12)
			{
				alert("Country Shipment Date Not Allowed");
				release_freezing();	
				return; 
			}
			 if(reponse[0]==16)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			show_list_view(document.getElementById('order_id').value,'populate_size_color_breakdown','size_color_breakdown','requires/size_color_breakdown_controller','');
		    show_list_view(document.getElementById('order_id').value+"_"+document.getElementById('cbo_po_country').value,'populate_size_color_breakdown_with_data','data_form','requires/size_color_breakdown_controller','');
			document.getElementById('cbo_po_country').value=0;
			document.getElementById('hid_old_country').value="";
			document.getElementById('txt_first_range').value="";
		    document.getElementById('txt_second_range').value="";
		    document.getElementById('txt_click_range').value="";
		    document.getElementById('txt_copy_color').value="";
			document.getElementById('txt_avg_price').value="";
			document.getElementById('txt_excess_cut').value="";
			var row_num=$('#size_color_break_down_list tr').length-1;
			for (var i=1; i<=row_num; i++)
		    {
				document.getElementById('hiddenid_'+i).value="";
			}
			set_button_status(0, permission, 'fnc_size_color_breakdown',1);
			if(reponse[0] !=2)
			{
			calculate_total_amnt( 1 )
			}
			if(reponse[0] ==2)
			{
			show_list_view(document.getElementById('order_id').value,'populate_size_color_breakdown','size_color_breakdown','requires/size_color_breakdown_controller','');
			}
			release_freezing();
		}
	}	
	function add_break_down_tr( i )
	{
		var row_num=$('#size_color_break_down_list tr').length-1;
		if (i==0)
		{
			i=1;
			$("#txtcolor_"+i).autocomplete({
			source: str_color
			});
			$("#txtsize_"+i).autocomplete({
			source:  str_size 
			}); 
			return;
		}
		if (row_num!=i)
		{
			return false;
		}
		if (form_validation('cbogmtsitem_'+i+'*txtcolor_'+i+'*txtsize_'+i+'*txtorderquantity_'+i+'*txtorderrate_'+i,'Company Name*Location Name*Buyer Name*Style Ref*Product Department*Item Catagory*Dealing Merchant*Packing')==false)
		{
			return;
		}
		if($('#txtorderrate_'+i).val()==0)
		{
			alert("Fill Up Rate");	
			$('#txtorderrate_'+i).focus();
			return;
		}
		else
		{
			i++;
			$("#size_color_break_down_list tr:last").clone().find("input,select").each(function() {
			$(this).attr({
			'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			'name': function(_, name) { return name + i },
			'value': function(_, value) { return value }              
			});
			}).end().appendTo("#size_color_break_down_list");
			
			$('#cbogmtsitem_'+i).removeAttr("onChange").attr("onChange","calculate_total_amnt("+i+");check_duplicate("+i+",this.id)");
			$('#txtcolor_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id)");
			$('#txtsize_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id)");
			
			$('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
			$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'size_color_break_down_list');");
			$('#txtorderquantity_'+i).removeAttr("onBlur").attr("onBlur","set_excess_cut(this.value,document.getElementById('txtorderexcesscut_"+i+"').value,"+i+")");
			$('#txtorderquantity_'+i).removeAttr("onChange").attr("onChange","validate_po_qty_co_si("+i+")");
			$('#txtorderrate_'+i).removeAttr("onBlur").attr("onBlur","calculate_total_amnt("+i+")");
			$('#txtorderexcesscut_'+i).removeAttr("onBlur").attr("onBlur","set_excess_cut(document.getElementById('txtorderquantity_"+i+"').value,this.value,"+i+")");
			$('#txtsize_'+i).val('');
			// onBlur="(document.getElementById('txtorderquantity_1').value, this.value, 1)"
			var j=i-1;
			//$('#txtcolor_'+i).removeAttr("onfocus").attr("onfocus","add_break_down_tr("+j+");");
			$('#cbogmtsitem_'+i).val($('#cbogmtsitem_'+j).val()); 
			$('#cbostatus_'+i).val($('#cbostatus_'+j).val());
			$('#hiddenid_'+i).val("");
			$('#txtorderquantity_'+i).val("");
			set_all_onclick();
			$("#txtcolor_"+i).autocomplete({
			source: str_color
			});
			$("#txtsize_"+i).autocomplete({
			source:  str_size 
			}); 
			calculate_total_amnt( i )
		}
	}	
	function copyset_tr_old()
	{
		var txt_first_range=document.getElementById('txt_first_range').value
		var txt_second_range=document.getElementById('txt_second_range').value
		for(var i=txt_first_range; i<=txt_second_range; i++)
		{
			var txt_copy_color=(document.getElementById('txt_copy_color').value).toUpperCase();
			var txtcolor=(document.getElementById('txtcolor_'+i).value).toUpperCase();
			var cbogmtsitem=(document.getElementById('cbogmtsitem_'+i).value);
			var cbogmtsitem_copy=(document.getElementById('cbogmtsitem').value);
			var row_num=$('#size_color_break_down_list tr').length-1;			
			if(txt_copy_color==txtcolor && cbogmtsitem_copy==cbogmtsitem)
			{
				alert("Duplicate Item, Color and Size found")
				continue;
			}		
			row_num+=1;
			$("#size_color_break_down_list tr:eq("+i+")").clone().find("input,select").each(function() {
			$(this).attr({
				'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
				'name': function(_, name) { return name + row_num },
				'value': function(_, value) { return value }              
			});
			}).end().appendTo("#size_color_break_down_list");
			$('#cbogmtsitem_'+row_num).removeAttr("onChange").attr("onChange","calculate_total_amnt("+row_num+");check_duplicate("+row_num+",this.id)");
			$('#txtcolor_'+row_num).removeAttr("onChange").attr("onChange","check_duplicate("+row_num+",this.id)");
			$('#txtsize_'+row_num).removeAttr("onChange").attr("onChange","check_duplicate("+row_num+",this.id)");
			
			$('#increaseset_'+row_num).removeAttr("onClick").attr("onClick","add_break_down_tr("+row_num+");");
			$('#decreaseset_'+row_num).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+row_num+",'size_color_break_down_list');");
			$('#txtorderquantity_'+row_num).removeAttr("onBlur").attr("onBlur","set_excess_cut(this.value,document.getElementById('txtorderexcesscut_"+row_num+"').value,"+row_num+")");
			$('#txtorderquantity_'+i).removeAttr("onChange").attr("onChange","validate_po_qty_co_si("+i+")");
			$('#txtorderrate_'+row_num).removeAttr("onBlur").attr("onBlur","calculate_total_amnt("+row_num+")");
			$('#txtorderexcesscut_'+row_num).removeAttr("onBlur").attr("onBlur","set_excess_cut(document.getElementById('txtorderquantity_"+row_num+"').value,this.value,"+row_num+")");
			if(txt_copy_color !="")
			{
				$('#txtcolor_'+row_num).val(txt_copy_color);
			}
			$('#cbogmtsitem_'+row_num).val(cbogmtsitem_copy);
			$('#hiddenid_'+row_num).val("");			
			calculate_total_amnt( i )
		}		
	}
	function copyset_tr()
	{
	   var rowNum=$('#size_color_break_down_list tr').length-1;
	   var checked=0;
		for (var k=1;k<=rowNum; k++)
		{
			var is_checked=$("#checktr_"+k).is(':checked');
			if(is_checked)
			{
			var txt_copy_color=(document.getElementById('txt_copy_color').value).toUpperCase();
			var txtcolor=(document.getElementById('txtcolor_'+k).value).toUpperCase();
			var cbogmtsitem=(document.getElementById('cbogmtsitem_'+k).value);
			var cbogmtsitem_copy=(document.getElementById('cbogmtsitem').value);
			if(txt_copy_color==txtcolor && cbogmtsitem_copy==cbogmtsitem)
			{
				//$("#size_color_break_down_list tr:eq("+i+")").css('background-color', 'Red');
				alert("Duplicate Item, Color and Size found")
				continue;
			}
			var row_num=$('#size_color_break_down_list tr').length-1;
			row_num+=1;
			$("#size_color_break_down_list tr:eq("+k+")").clone().find("input,select").each(function() {
			$(this).attr({
			'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
			'name': function(_, name) { return name + row_num },
			'value': function(_, value) { return value }              
			});
			}).end().appendTo("#size_color_break_down_list");
			$('#cbogmtsitem_'+row_num).removeAttr("onChange").attr("onChange","calculate_total_amnt("+row_num+");check_duplicate("+row_num+",this.id)");
			$('#txtcolor_'+row_num).removeAttr("onChange").attr("onChange","check_duplicate("+row_num+",this.id)");
			$('#txtsize_'+row_num).removeAttr("onChange").attr("onChange","check_duplicate("+row_num+",this.id)");
			
			$('#increaseset_'+row_num).removeAttr("onClick").attr("onClick","add_break_down_tr("+row_num+");");
			$('#decreaseset_'+row_num).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+row_num+",'size_color_break_down_list');");
			$('#txtorderquantity_'+row_num).removeAttr("onBlur").attr("onBlur","set_excess_cut(this.value,document.getElementById('txtorderexcesscut_"+row_num+"').value,"+row_num+")");
			$('#txtorderquantity_'+k).removeAttr("onChange").attr("onChange","validate_po_qty_co_si("+k+")");

			$('#txtorderrate_'+row_num).removeAttr("onBlur").attr("onBlur","calculate_total_amnt("+row_num+")");
			$('#txtorderexcesscut_'+row_num).removeAttr("onBlur").attr("onBlur","set_excess_cut(document.getElementById('txtorderquantity_"+row_num+"').value,this.value,"+row_num+")");
			$('#checktr_'+row_num).removeAttr("onClick").attr("onClick","tr_check("+row_num+",event);");
			if(txt_copy_color !="")
			{
			$('#txtcolor_'+row_num).val(txt_copy_color);
			}
			$('#cbogmtsitem_'+row_num).val(cbogmtsitem_copy);
			$('#hiddenid_'+row_num).val("");
			$('#checktr_'+row_num).prop('checked', false);
			calculate_total_amnt( k )
			checked+=1;
			}
		} 
		if(checked==0)
		{
		alert("Check row First")	
		}	
	}	
	function fn_deletebreak_down_tr(rowNo,table_id) 
	{   
		if(table_id=='size_color_break_down_list')
		{
			var numRow = $('table#size_color_break_down_list tbody tr').length; 
			//alert (numRow);
			/*if(numRow==rowNo && rowNo!=1)
			{
				
				if($('#hiddenid_'+rowNo).val()=="")
				{
					$('#size_color_break_down_list tbody tr:last').remove();
					calculate_total_amnt( rowNo-1 );
				}
				else
				{
					//permission_array=permission.split("_");
					//alert(permission_array[2]);
					//var cbogmtsitem=$('#cbogmtsitem_'+rowNo).val();
					//var cbogmtsitem=$('#cbogmtsitem_'+rowNo).val();
					//var cbogmtsitem=$('#cbogmtsitem_'+rowNo).val();
					//var cbogmtsitem=$('#cbogmtsitem_'+rowNo).val();
					
					$('#size_color_break_down_list tbody tr:last').remove();
					calculate_total_amnt( rowNo-1 );
					//alert("Remove Restricted!");	
				}
			}
			else
			{
				var index=rowNo-1
				//$("#size_color_break_down_list tbody tr:eq("+index+")").hide()
				$("#size_color_break_down_list tbody tr:eq("+index+")").remove()
				re_order()
				calculate_total_amnt( rowNo-1 );
			}*/ 
			
			if(rowNo!=1)
			{
				var permission_array=permission.split("_");
				var updateid=$('#hiddenid_'+rowNo).val();
				if(updateid !="" && permission_array[2]==1)
				{
				var booking=return_global_ajax_value(updateid, 'delete_row_color_size', '', 'requires/size_color_breakdown_controller');
				}
				var index=rowNo-1
				$("table#size_color_break_down_list tbody tr:eq("+index+")").remove()
				var numRow = $('table#size_color_break_down_list tbody tr').length; 
				for(i = rowNo;i <= numRow;i++)
				{
					$("#size_color_break_down_list tr:eq("+i+")").find("input,select").each(function() {
							$(this).attr({
								'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
								//'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
								'value': function(_, value) { return value }             
							}); 
						$('#cbogmtsitem_'+i).removeAttr("onChange").attr("onChange","calculate_total_amnt("+i+");check_duplicate("+i+",this.id)");
						$('#txtcolor_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id)");
						$('#txtsize_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id)");
						$('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
						$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'size_color_break_down_list');");
						$('#txtorderquantity_'+i).removeAttr("onBlur").attr("onBlur","set_excess_cut(this.value,document.getElementById('txtorderexcesscut_"+i+"').value,"+i+")");
						$('#txtorderquantity_'+i).removeAttr("onChange").attr("onChange","validate_po_qty_co_si("+i+")");
						$('#txtorderrate_'+i).removeAttr("onBlur").attr("onBlur","calculate_total_amnt("+i+")");
						$('#txtorderexcesscut_'+i).removeAttr("onBlur").attr("onBlur","set_excess_cut(document.getElementById('txtorderquantity_"+i+"').value,this.value,"+i+")");
					})
				}
			}
			calculate_total_amnt( rowNo-1 )
		}
	}
	function re_order()
	{
		var row_num=$('#size_color_break_down_list tr').length-1;
		for(i=0;i<=row_num;i++)
		{
		$("#size_color_break_down_list tr:eq("+i+")").find("input,select").each(function() {
			$(this).attr({
			'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			//'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
			'value': function(_, value) { return value }             
			}); 
			
			$('#cbogmtsitem_'+i).removeAttr("onChange").attr("onChange","calculate_total_amnt("+i+");check_duplicate("+i+",this.id)");
			$('#txtcolor_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id)");
			$('#txtsize_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id)");
			$('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
			$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'size_color_break_down_list');");
			$('#txtorderquantity_'+i).removeAttr("onBlur").attr("onBlur","set_excess_cut(this.value,document.getElementById('txtorderexcesscut_"+i+"').value,"+i+")");
			$('#txtorderquantity_'+i).removeAttr("onChange").attr("onChange","validate_po_qty_co_si("+i+")");
			$('#txtorderrate_'+i).removeAttr("onBlur").attr("onBlur","calculate_total_amnt("+i+")");
			$('#txtorderexcesscut_'+i).removeAttr("onBlur").attr("onBlur","set_excess_cut(document.getElementById('txtorderquantity_"+i+"').value,this.value,"+i+")");
			set_all_onclick();
			$("#txtcolor_"+i).autocomplete({
			source: str_color
			});
			$("#txtsize_"+i).autocomplete({
			source:  str_size 
			});
			})
		}
	}
	function populate_size_color_breakdown_with_data(data)
	{
		freeze_window(5);
		var data=data.split("_");
		
		document.getElementById('cbo_po_country').value=data[1];
		document.getElementById('hid_old_country').value=data[1];
		document.getElementById('txt_country_ship_date').value=data[2];
		document.getElementById('txt_cutup_date').value=data[3];
		document.getElementById('cbo_cut_up').value=data[4];
		document.getElementById('txt_country_remarks').value=data[5];
		document.getElementById('cbo_po_country_type').value=data[6];
		document.getElementById('cbo_packing_country_level').value=data[7];
		var cutting_found=data[8]*1;
		//alert(cutting_found);
		if(data[3] !="")
		{
			 $("#txt_country_ship_date").attr("disabled",true);
		}
		else
		{
			$("#txt_country_ship_date").attr("disabled",false);
		}
		if(cutting_found>0) //Cutting Found
		{
			 $("#cbo_po_country").attr("disabled",true);
		}
		else
		{
			$("#cbo_po_country").attr("disabled",false);
		}
		document.getElementById('txt_first_range').value="";
		document.getElementById('txt_second_range').value="";
		document.getElementById('txt_click_range').value="";
		document.getElementById('txt_copy_color').value="";
		show_list_view(data[0]+"_"+data[1],'populate_size_color_breakdown_with_data','data_form','requires/size_color_breakdown_controller','');
		set_button_status(1, permission, 'fnc_size_color_breakdown',1);
		calculate_total_amnt( 1 )
		
		
		release_freezing();
	}	
	function check_country(country_id)
	{
		var po_id=document.getElementById('order_id').value;
		var country=return_global_ajax_value(po_id+"_"+country_id, 'check_country', '', 'requires/size_color_breakdown_controller');
		if(country>0)
		{
			alert("This Country Data Already Inserted");
			document.getElementById('cbo_po_country').value=0;
		}	
	}
	

	<?
	$sql_temp=sql_select("SELECT percentage, upper_limit_qty, comapny_id, buyer_id, lower_limit_qty FROM lib_excess_cut_slab WHERE status_active=1 and is_deleted=0 order by comapny_id,buyer_id,lower_limit_qty asc");//comapny_id='$cbo_company_name' and buyer_id='$cbo_buyer_name' and 
	$i=0;
	foreach($sql_temp  as $row)
	{
		if( $exc[$row[csf("comapny_id")]][$row[csf("buyer_id")]]=='') $i=0;
		$exc_perc[$row[csf("comapny_id")]][$row[csf("buyer_id")]]['limit'][$i]=$row[csf("lower_limit_qty")]."__".$row[csf("upper_limit_qty")];
		$exc_perc[$row[csf("comapny_id")]][$row[csf("buyer_id")]]['val'][$i]=$row[csf("percentage")];
		$exc[$row[csf("comapny_id")]][$row[csf("buyer_id")]]=1;
		//echo $i."=";
		$i++;
	}
	unset($sql_temp);
	?>
	var exc_perc =<? echo json_encode($exc_perc); ?>;
	
	function excess_percentage( comp, buyer, qnty )
	{
		//var exc_perc=new Array();
		//alert (comp+'='+buyer+'='+qnty); return;
		
		//if( exc_perc[comp][buyer]!="undefined" )
		if(typeof(exc_perc[comp])!= 'undefined')
		{
			if(typeof(exc_perc[comp][buyer])!= 'undefined')
			{
				var newp=exc_perc[comp][buyer]["limit"]; 
				var newp= JSON.stringify(newp);
				var newstr=newp.split(",");
				for(var m=0; m< newstr.length; m++)
				{
					var limit=exc_perc[comp][buyer]["limit"][m].split("__");
					if((limit[1]*1)==0 && (qnty*1)>=(limit[0]*1))
					{
						return ( exc_perc[comp][buyer]["val"][m]*1);	
					}
					if( (qnty*1)>=(limit[0]*1) && (qnty*1)<=(limit[1]*1) )
					{
						return exc_perc[comp][buyer]["val"][m];
					}
					// alert( newstr[m]+"=="+m)
				}
			}
		}
		return 0;
	}
	
	function set_excess_cut( val, excs, inc )
	{
		var excess_per_level=0;
		var excess_variable=0;
		var editable_id=0;
		var cbo_company_name=$('#cbo_company_name').val();
		var cbo_buyer_name=$('#cbo_buyer_name').val();
		var excutvariable=$('#txt_style_description').attr('excutvariable');
		var str_excut=excutvariable.split("_");
		
		var excess_per_level=str_excut[1];
		var excess_variable=str_excut[0];
		var editable_id=str_excut[2];
		
		if ( val!="" || val!=0 )
		{
			var excut_fmLib =0;
			//var excs_cut=return_ajax_request_value(val+"_"+document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_buyer_name').value, "get_excess_cut_percent", "requires/woven_order_entry_controller") ;
			if((excess_variable*1)==2 && excess_per_level==1)
			{
				var excut_fmLib = excess_percentage(cbo_company_name,cbo_buyer_name,val);
				
				document.getElementById('txtorderexcesscut_'+inc).value=excut_fmLib;
				var txt_plan_cut=(val*1)+((excut_fmLib*val*1)/100);
				document.getElementById('txtorderplancut_'+inc).value=number_format_common(txt_plan_cut, 6, 0);
				if(editable_id==1) //Slap// Yes
				{
					$('#txtorderexcesscut_'+inc).attr('disabled',false);
				}
				else
				{
					$('#txtorderexcesscut_'+inc).attr('disabled',true);
				}
			}
			else if((excess_variable*1)==2 && excess_per_level==2)
			{
				document.getElementById('txtorderexcesscut_'+inc).value=excs;
				var txt_plan_cut=(val*1)+((excs*val*1)/100);
				document.getElementById('txtorderplancut_'+inc).value=number_format_common(txt_plan_cut, 6, 0);
				if(editable_id==1) //Slap// Yes
				{
					$('#txtorderexcesscut_'+inc).attr('disabled',false);
				}
				else
				{
					$('#txtorderexcesscut_'+inc).attr('disabled',true);
				}
			}
			else if(excess_variable*1==3){
				document.getElementById('txtorderexcesscut_'+inc).value='';
				$('#txtorderexcesscut_'+inc).attr('disabled',true);
				document.getElementById('txtorderplancut_'+inc).value=val;
			}
			else{
				document.getElementById('txtorderexcesscut_'+inc).value=excs;
				$('#txtorderexcesscut_'+inc).attr('disabled',false);
				//document.getElementById('txtorderplancut_'+inc).value=val;
				document.getElementById('txtorderplancut_'+inc).value=(val*1)+((excs*val)/100);
			}
		}
		//}
		/*else
		{
			
			var txt_plan_cut=(val*1)+((excs*val)/100);
			document.getElementById('txt_plan_cut').value=number_format_common(txt_plan_cut, 6, 0);
		}*/
		
		calculate_total_amnt(inc);
		//var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
		//math_operation( 'txt_amount', 'txt_avg_price*txt_po_quantity', '*','', ddd );
	}
	
	function calculate_total_amnt( id )
	{
		//alert(id);
		document.getElementById('txtorderamount_'+id).value=document.getElementById('txtorderrate_'+id).value*document.getElementById('txtorderquantity_'+id).value
		var po_qnty=(document.getElementById('total').value)*1;
		var item_id=(document.getElementById('cbogmtsitem_'+id).value);
		var cbo_order_uom=document.getElementById('cbo_order_uom').value;
		if(cbo_order_uom==58)
		{
			var set_breck_down=document.getElementById('set_breck_down').value;
			set_breck_down=set_breck_down.split("__");
			var item_id_value_array=new Array();
			for (var si=0;si<set_breck_down.length; si++)
			{
			var item_id_value=set_breck_down[si].split("_");
			item_id_value_array[item_id_value[0]]=item_id_value[1]
			}
			po_qnty=po_qnty*item_id_value_array[item_id];
			//document.getElementById('set_item_qnty_level').innerHTML="Set Item Qnty";
			//document.getElementById('set_item_qnty').innerHTML=item_id_value_array[item_id];
			//document.getElementById('qnty_eq_in_pcs_level').innerHTML="Qnty in Pcs";
			//document.getElementById('qnty_eq_in_pcs').innerHTML=po_qnty;
		}
		var row_num=$('#size_color_break_down_list tr').length-1;
		var tot=0;
		var item_tot=0;
		var avg_rate = 0; 
		var tot_amount = 0;
		var avg_excess_cut = 0;
		var tot_plan_cut = 0;
		for (var k=1;k<=row_num; k++)
		{
			if(item_id==document.getElementById('cbogmtsitem_'+k).value)
			{
				item_tot=(item_tot*1)+(document.getElementById('txtorderquantity_'+k).value*1);
			}
			tot=(tot*1)+(document.getElementById('txtorderquantity_'+k).value*1);
			avg_rate=((avg_rate*1)+(document.getElementById('txtorderrate_'+k).value*1));
			tot_amount=(tot_amount*1)+(document.getElementById('txtorderamount_'+k).value*1);
			avg_excess_cut=((avg_excess_cut*1)+(document.getElementById('txtorderexcesscut_'+k).value*1))
			tot_plan_cut=(tot_plan_cut*1)+(document.getElementById('txtorderplancut_'+k).value*1); 
		}
		avg_excess_cut=((tot_plan_cut-tot)/tot)*100;
		avg_rate=tot_amount/tot*1;
		$('#txt_total_order_qnty').val(tot);
		$('#txt_total_order_item_qnty').val(item_tot);
		$('#txt_total_order_item_yetto_qnty').val(po_qnty-item_tot);
		$('#txt_avg_rate').val(number_format_common(avg_rate, 3, 0,2));
		$('#txt_total_amt').val(tot_amount);
		$('#txt_avg_excess_cut').val(number_format_common(avg_excess_cut,6, 0,2));
		$('#txt_total_plan_cut').val(tot_plan_cut);
		
		if (item_tot>po_qnty)
		{
			alert('Breakdown Quantity Over The Po Qnty Not Allowed.');
			document.getElementById('txtorderquantity_'+id).value="";
			document.getElementById('txtorderplancut_'+id).value="";
			document.getElementById('txtorderamount_'+id).value="";
			$('#txtorderquantity_'+id).focus();
			return;
		}
	}
	
	function check_duplicate(id,td)
	{
		var item_id=(document.getElementById('cbogmtsitem_'+id).value);
		var txtcolor=(document.getElementById('txtcolor_'+id).value).toUpperCase();
		var txtsize=(document.getElementById('txtsize_'+id).value).toUpperCase();
		var row_num=$('#size_color_break_down_list tr').length-1;
		for (var k=1;k<=row_num; k++)
		{
			if(k==id)
			{
				continue;
			}
			else
			{
				//alert(item_id+"="+document.getElementById('cbogmtsitem_'+k).value);
				//alert(txtcolor+"="+document.getElementById('txtcolor_'+k).value);
				//alert(txtsize+"="+document.getElementById('txtsize_'+k).value);
				if(item_id==document.getElementById('cbogmtsitem_'+k).value && trim(txtcolor)==trim(document.getElementById('txtcolor_'+k).value.toUpperCase()) && trim(txtsize)==trim(document.getElementById('txtsize_'+k).value.toUpperCase()))
				{
				alert("Same Gmts Item, Same Color and Same Size Duplication Not Allowed.");
				document.getElementById(td).value="";
				document.getElementById(td).focus();
				}
			}
		}
	}
	
	function tr_index(tr)
	{
		var index_main=$(tr).index();
		var index=$(tr).index()+1;
		document.getElementById('txt_click_range').value=document.getElementById('txt_click_range').value*1+1;
		if(document.getElementById('txt_click_range').value==1)
		{
		document.getElementById('txt_first_range').value=index;
		$("#size_color_break_down_list tr:eq("+index+")").css('background-color', 'Yellow');
		//$(tr).css('background-color', 'Red');
		}
		else
		{
			var color_remove_in=document.getElementById('txt_second_range').value;
			//alert(color_remove_in)
			$("#size_color_break_down_list tr:eq("+color_remove_in+")").css('background-color', '');
			if(document.getElementById('txt_first_range').value<index)
			{
						document.getElementById('txt_second_range').value=index;
						$("#size_color_break_down_list tr:eq("+index+")").css('background-color', 'Yellow');
						//$(tr).css('background-color', 'Red');

			}
			else
			{
				document.getElementById('txt_second_range').value=document.getElementById('txt_first_range').value;
				document.getElementById('txt_first_range').value=index
				$("#size_color_break_down_list tr:eq("+index+")").css('background-color', 'Yellow');
			}
		}
		
	}
	
	function checkalltr_f(value)
 	{
		var row_num=$('#size_color_break_down_list tr').length-1;
		for (var k=1;k<=row_num; k++)
		{
			if(value==1)
			{
			$('#checktr_'+k).prop('checked', true);
			document.getElementById('checkalltr').value=2
			}
			if(value==2)
			{
			$('#checktr_'+k).prop('checked', false);
			document.getElementById('checkalltr').value=1	
			}
			//$('#checktr_'+k).click();
		}
		show_hide_button_holder()
 	}
 
	function tr_check(i,e)
	{
		
		if (e.ctrlKey) {
		   var row_num=$('#size_color_break_down_list tr').length-1;
		   var checked=[];
		   var i=0;
			for (var k=1;k<=row_num; k++)
			{
				var is_checked=$("#checktr_"+k).is(':checked');
				if(is_checked)
				{
					checked[i]=k;
					i++;
				}
			} 
			checked.sort(function(a, b){return b-a});
			var highest=checked[0];
			//alert(highest);
			checked.sort(function(a, b){return a-b});
			var lowest=checked[0];
			//alert(lowest);
			for (var j=lowest+1;j<=highest-1; j++)
			{
				$('#checktr_'+j).prop('checked', true);
			} 
	    }
		show_hide_button_holder()
	}
	function show_hide_button_holder()
	{
		
		 var row_num=$('#size_color_break_down_list tr').length-1;
		   var checked=0;
			for (var k=1;k<=row_num; k++)
			{
				if(checked==0)
				{
					var is_checked=$("#checktr_"+k).is(':checked');
					if(is_checked)
					{
						checked=1
					}
					else
					{
					 checked=0	
					}
				}
			}
			if(checked==1)
			{
			 $('#clear_button_holder').show();	
			}
			if(checked==0)
			{
			 $('#clear_button_holder').hide();	
			}     	
	}
	function clear_color(type)
	{
		var row_num=$('#size_color_break_down_list tr').length-1;
		var checked=0;
		for (var k=1;k<=row_num; k++)
		{
			var is_checked=$("#checktr_"+k).is(':checked');
			if(is_checked)
			{
			$("#"+type+k).val('');
			checked+=1;
			}
		} 
		if(checked==0)
		{
		alert("Check row First")	
		}	
	}	
	function check_copy(val)
	{
		copied_table="";
		if (val==0)
		{
			$('#chk_copy').val(1);	// attr('checked',true);
			copied_table=$("#size_color_break_down_list tbody").html();
		}
		else
		$('#chk_copy').val(0); 	//attr('checked',false);	
		alert(copied_table);
	}	
	function add_copied_po_breakdown()
	{
		$("#size_color_break_down_list tbody").html('');
		$("#size_color_break_down_list tbody").html(copied_table);
	}
	function color_select_popup(buyer_name,texbox_id)
	{
		//var page_link='requires/sample_booking_non_order_controller.php?action=color_popup'
		//alert(texbox_id)
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/size_color_breakdown_controller.php?action=color_popup&buyer_name='+buyer_name, 'Color Select Pop Up', 'width=250px,height=450px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var color_name=this.contentDoc.getElementById("color_name");
			if (color_name.value!="")
			{
				$('#'+texbox_id).val(color_name.value);
			}
		}
	}

	function copy_avg_price()
	{
		var avg_price=document.getElementById('txt_avg_price').value;	
		if(avg_price =="")
		{
			alert("Insert rate");
			return;
		}
		if(avg_price ==0)
		{
			alert("Insert rate");
			return;
		}	
		var order_id=document.getElementById('order_id').value;
			
		var tot_row=return_global_ajax_value(order_id+'_'+avg_price+'_'+txt_job_no, 'update_avg_rate', '', 'requires/size_color_breakdown_controller');
		show_list_view(document.getElementById('order_id').value,'populate_size_color_breakdown','size_color_breakdown','requires/size_color_breakdown_controller','');
	    show_list_view(document.getElementById('order_id').value+"_"+document.getElementById('cbo_po_country').value,'populate_size_color_breakdown_with_data','data_form','requires/size_color_breakdown_controller','');
		document.getElementById('cbo_po_country').value=0;
		document.getElementById('hid_old_country').value=0;
		alert("Total "+trim(tot_row)+" Rows Updated");
	}

	function copy_excess_cut()
	{
		var txt_excess_cut=document.getElementById('txt_excess_cut').value;	
		if(txt_excess_cut =="")
		{
			alert("Insert rate");
			return;
		}
		if(txt_excess_cut ==0)
		{
			alert("Insert rate");
			return;
		}
		
		var order_id=document.getElementById('order_id').value;
		var po_id=order_id;
		var txt_job_no=document.getElementById('txt_job_no').value;
		var booking_no_with_approvet_status = return_ajax_request_value(txt_job_no+"_"+po_id, 'booking_no_with_approved_status', 'requires/woven_order_entry_controller')
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
			var al_magg=" Main Fabric Un-Approved Booking No "+booking_no_with_approvet_status_arr[1]+" Found\n If you update this job\n You have to update  Pre-cost and booking against this Job ";
			var r=confirm(al_magg);
			if(r==false)
			{
				return;
			}
			else
			{
				//continue;
			}
			
		}
		
		var tot_row=return_global_ajax_value(order_id+'_'+txt_excess_cut+'_'+txt_job_no, 'update_txt_excess_cut', '', 'requires/size_color_breakdown_controller');
		show_list_view(document.getElementById('order_id').value,'populate_size_color_breakdown','size_color_breakdown','requires/size_color_breakdown_controller','');
	    show_list_view(document.getElementById('order_id').value+"_"+document.getElementById('cbo_po_country').value,'populate_size_color_breakdown_with_data','data_form','requires/size_color_breakdown_controller','');
		document.getElementById('cbo_po_country').value=0;
		document.getElementById('hid_old_country').value=0;
		alert("Total "+trim(tot_row)+" Rows Updated");
	}

	function vacant_form()
	{
		document.getElementById('cbogmtsitem').value="";
		document.getElementById('txt_first_range').value="";
		document.getElementById('txt_second_range').value="";
		document.getElementById('txt_copy_color').value="";
	}
	function set_ship_date() 
	{
		var txt_cutup_date=document.getElementById('txt_cutup_date').value;
		var cbo_cut_up=document.getElementById('cbo_cut_up').value;
		if(txt_cutup_date=="")
		{
			alert("Insert Cutup Date");
			 $("#txt_country_ship_date").attr("disabled",false);
			return;
		}
		if(cbo_cut_up==0)
		{
			alert("Select Cutup");
			$("#txt_country_ship_date").attr("disabled",false);
			return;
		}
		var set_ship_date=return_global_ajax_value(txt_cutup_date+'_'+cbo_cut_up, 'set_ship_date', '', 'requires/size_color_breakdown_controller');
		document.getElementById('txt_country_ship_date').value=set_ship_date;
		 $("#txt_country_ship_date").attr("disabled",true);
	}	
	function reorder_size_color()
	{
		var txt_job_no=document.getElementById('txt_job_no').value;
	  emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/size_color_breakdown_controller.php?action=reorder_size_color&txt_job_no='+txt_job_no, 'Color Size Ordering', 'width=600px,height=400px,center=1,resize=1,scrolling=0','../../')	
	}
	function validate_po_qty_co_si(i)
	{
		var saved_po_quantity=$('#txtorderquantity_'+i).attr('saved_po_quantity');
		//alert(saved_po_quantity)
	    var txt_po_quantity=$('#txtorderquantity_'+i).val()*1;
		var hiddenid=document.getElementById('hiddenid_'+i).value;
		var txt_excess_cut=$('#txtorderexcesscut_'+i).val()*1;
		var po_id=document.getElementById('order_id').value;
		
		if(hiddenid>0 && hiddenid!=""){
		var cutting_qty=return_global_ajax_value(hiddenid, 'get_cutting_qty', '', 'requires/size_color_breakdown_controller');
		}
		//alert(cutting_qty)
		var excess_cut_per=(1+(txt_excess_cut/100));
		var allowed_qty=cutting_qty/excess_cut_per;
		allowed_qty=Math.ceil(allowed_qty);
		
		if(txt_po_quantity<allowed_qty)
		{
			alert("Cutting Qty Found,You can update upto"+allowed_qty+" Qty");
			$('#txtorderquantity_'+i).val(saved_po_quantity);
			return;
		}
	}
</script>
</head>

<body onLoad="set_hotkey()">
    <div style="width:100%;">
        <!-- Important Field outside Form --> 
        <input type="hidden" id="garments_nature" value="2">
        <!-- End Important Field outside Form -->
        <? echo load_freeze_divs ("../../",$permission);  ?>
        <!-- <table width="100%" cellpadding="0" cellspacing="2" align="center" >
        <tr> 
        	<td valign="top" width="950"> -->
        	<div style="width: 1600px">       
		        <fieldset style="width:850px; float: left;">
		            <legend>Color & Size Breakdown Entry</legend>
		            <form name="sizecolormaster_1" id="sizecolormaster_1" autocomplete="off">
		                <table  width="850" cellspacing="2" cellpadding="0" border="0">
		                    <tr>
		                        <td  width="130" height="" align="right"></td>              
		                        <td  width="170" >
		                        </td>
		                        <td  width="130" align="right">Order No </td>
		                        <td width="170">
		                        <input style="width:160px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/size_color_breakdown_controller.php?action=order_popup','Job/Order Selection Form')" class="text_boxes" placeholder="Order No" name="txt_order_no" id="txt_order_no" readonly />
		                        <input type="hidden" id="order_id" name="order_id" readonly />
		                        </td>
		                        <td width="130" align="right"></td>
		                        <td>
		                        </td>
		                    </tr>
		                    <tr>
		                        <td  width="130" height="" align="right">Job No</td>              
		                        <td  width="170" >
		                        <input style="width:160px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/size_color_breakdown_controller.php?action=order_popup','Job/Order Selection Form')" class="text_boxes"  name="txt_job_no" id="txt_job_no" disabled />
								<input type="hidden" name="hidd_job_id" id="hidd_job_id" style="width:30px;" class="text_boxes" />

		                        </td>
		                        <td  width="130" align="right">Company Name </td>
		                        <td width="170">
		                        <?
		                        echo create_drop_down( "cbo_company_name", 172, "select comp.id,comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "",1);
		                        ?> 
		                        </td>
		                        <td width="130" align="right">Location Name</td>
		                        <td id="location">
		                        <? 
		                        echo create_drop_down( "cbo_location_name", 172, "select id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select --", $selected, "",1 );		
		                        ?>	
		                        </td>
		                    </tr>
		                    <tr>
		                        <td align="right">Buyer Name</td>
		                        <td id="buyer_td">
		                        <? 
		                        echo create_drop_down( "cbo_buyer_name", 172, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ,1);   
		                        ?>	  
		                        </td>
		                        <td align="right">Style Ref.</td>
		                        <td>
		                        <input class="text_boxes" type="text" style="width:160px" disabled placeholder="Double Click for Quotation" name="txt_style_ref" id="txt_style_ref"/>	
		                        </td>
		                        <td align="right">
		                        Style Description
		                        </td>
		                        <td>	
		                        <input class="text_boxes" type="text" style="width:160px;" excutvariable="" disabled name="txt_style_description" id="txt_style_description"/>
		                        </td>
		                    </tr>
		                    <tr>
		                        <td height="" align="right">Pord. Dept.</td>   
		                        <td >
		                        <? 
		                        echo create_drop_down( "cbo_product_department", 172, $product_dept, "",1, "-- Select prod. Dept--", $selected, "" ,1);
		                        ?>
		                        </td>
		                        <td align="right">Currency</td>
		                        <td>
		                        <? 
		                        echo create_drop_down( "cbo_currercy", 172, $currency, "", 1, "-- Select Currency--", 2, "",1 );
		                        ?>	  
		                        </td>
		                        <td align="right">Agent </td>
		                        <td id="agent_td">
		                        <?	 	echo create_drop_down( "cbo_agent", 172, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id  and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3))  order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "",1 );  
		                        
		                        ?>
		                        </td>
		                    </tr>
		                    <tr>
		                        <td  align="right">Region</td>
		                        <td>
		                        <? 
		                        echo create_drop_down( "cbo_region", 172, $region, "",1, "-- Select Region --", $selected, "",1 );
		                        ?>	  
		                        </td>
		                        <td align="right">Team Leader</td>   
		                        <td>
		                        <?  
		                        echo create_drop_down( "cbo_team_leader", 172, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, "",1 );
		                        ?>		
		                        </td>
		                        <td align="right">Dealing Merchant</td>   
		                        <td> 
		                        <? 
		                        echo create_drop_down( "cbo_dealing_merchant", 172, "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "",1 );
		                        ?>	
		                        </td>
		                    </tr>
		                    <tr>
		                        <td  align="right">Shipment Date</td>
		                        <td>
		                        <input class="datepicker" type="text" style="width:160px;"  name="txt_ship_date" id="txt_ship_date" disabled/>
		                        </td>
		                        <td  align="right">Po Qnty</td>
		                        <td>
		                        <input class="text_boxes" type="text" style="width:100px;"  name="txt_po_qnty" id="txt_po_qnty" disabled/>
		                        <? 
		                        echo create_drop_down( "cbo_order_uom",55, $unit_of_measurement, "",0, "", 1, "","1","1,58" );
		                        ?>
		                        </td>
		                        <td  align="right">Plan Cut Qnty</td>
		                        <td>
		                        <input class="text_boxes" type="text" style="width:100px;"  name="txt_plan_cut_qnty" id="txt_plan_cut_qnty" disabled/>
		                        <? 
		                        echo create_drop_down( "cbo_order_uom_2",55, $unit_of_measurement, "",0, "", 1, "","1","1,58" );
		                        ?>
		                        </td>
		                    </tr>
		                    
		                    
		                    
		                    <tr>
		                        <td align="center" height="20" colspan="6" class="image_uploaders">
		                        <input type="hidden" id="update_id">
		                        <input type="hidden" id="set_breck_down" />     
		                        <input type="hidden" id="item_id" />
		                        <input type="hidden" id="tot_set_qnty" />
		                        <input type="hidden" id="cbo_order_status" />  
		                        </td>
		                    </tr>
		                    <tr>
		                        <td align="center" colspan="6" valign="middle" style="max-height:180px; min-height:15px;" id="po_list_view">
		                        </td>
		                        </tr>
		                        <tr>
		                        <td align="center" colspan="6" valign="middle" style="max-height:380px; min-height:15px;" id="size_color_breakdown11">
		                        <? 
		                        //echo load_submit_buttons( $permission, "fnc_size_color_breakdown", 0,0 ,"reset_form('sizecolormaster_1','po_list_view*size_color_breakdown','')",1) ;                        ?>
		                        </td>
		                    </tr>
		                </table>
		            </form>
		        </fieldset>
		        <div id="job_po_list_view" style="margin-left: 8px; float: left;"></div>
	        </div>
	        <fieldset style="width:950px">
	            <legend>Copy Panel</legend>
	            <table>
	            <tr>
	            <td>
	            New Item: 
	            <input class="text_boxes" type="hidden" style="width:100px;"  name="txt_first_range" id="txt_first_range" value=""/>
	            <input class="text_boxes" type="hidden" style="width:100px;"  name="txt_second_range" id="txt_second_range" value=""/>
	            <input class="text_boxes" type="hidden" style="width:100px;"  name="txt_click_range" id="txt_click_range" value="0"/> 
	            </td>
	            </td>
	            <td id="item_td">
				<?
	            echo create_drop_down( "cbogmtsitem", 170, $garments_item,"", 0, "","", "","",$item_id);
	            ?> 
	            <td>
	             New Color: 
	            </td>
	            <td> 
	             <input class="text_boxes" type="text" style="width:160px;"  name="txt_copy_color" id="txt_copy_color" <? echo $onClick." ".$readonly." ".$plachoder; ?>/>
	            </td>
	            <td>
	            Copy Rate
	            </td>
	            <td>
	            <input name="txt_avg_price" id="txt_avg_price" class="text_boxes_numeric" type="text" value=""  style="width:150px "  />
	            </td>
	            <td align="right">
	            Copy Excess Cut %
	            </td>
	            <td>
	            <input name="txt_excess_cut" id="txt_excess_cut"  class="text_boxes_numeric" type="text" style="width:160px "/>
	            </td>
	        </tr>
	        
	        <tr>
	            <td colspan="4" align="center">
	             <input type="reset" value="Reset Range" class="formbutton"  onClick="vacant_form()"/>
	            <input type="button" id="copyset1" style="width:50px" class="formbutton" value="Copy" onClick="copyset_tr()" /> 
	            </td>
	            <td colspan="2" align="center">
	            <input type="button" id="copyset2" style="width:100px" class="formbutton" value="Copy Rate" onClick="copy_avg_price()" /> 
	            </td>
	            
	            <td colspan="2" align="center">
	            <input type="button" id="copyset3" style="width:100px" class="formbutton" value="Copy Excess Cut" onClick="copy_excess_cut()" /> 
	            </td>
	        </tr>
	        </table>
	        </fieldset>
	        <br/>
	        
	        <fieldset style="width:950px">
	            <legend>Input Panel</legend>
	            <table>
	             <tr>
	                        <td  align="right">Country</td>
	                        <td>
	                       <?php
							echo create_drop_down( "cbo_po_country", 170,"select id,country_name from lib_country where status_active=1 and is_deleted=0 order by country_name", "id,country_name", 1, "Select", "","check_country(this.value)" ); 
	                        ?>
	                         <input type="hidden" id="hid_old_country" />
	                        </td>
	                        <td  align="right">Country Type</td>
	                        <td>
	                         <?php
								echo create_drop_down( "cbo_po_country_type", 170,$country_type, "", 0, "", "","" ); 
	                         ?>
	                        </td>
	                        <td  align="right">Cut-off Date</td>
	                        <td>
	                        <input class="datepicker" type="text" style="width:160px;"  name="txt_cutup_date" id="txt_cutup_date" onChange="set_ship_date()" value="<? echo $txt_org_shipment_date; ?>"/>
	                        </td>
	                    </tr>
	                    <tr>
	                        <td  align="right">Cutoff</td>
	                        <td>
	                        <? 
							echo create_drop_down( "cbo_cut_up",170, $cut_up_array, "",1, "Select", "", "set_ship_date()","","" );
	                        ?>
	                        </td>
	                        <td  align="right">Country Shipment Date</td>
	                        <td>
	                         <input class="datepicker" type="text" style="width:160px;"  name="txt_country_ship_date" id="txt_country_ship_date"  value="<? echo $txt_pub_shipment_date; ?>"/>

	                        </td>
	                        <td  align="right">Remarks</td>
	                        <td>
	                        <input class="text_boxes" type="text" style="width:160px;"  name="txt_country_remarks" id="txt_country_remarks" />
	                        </td>
	                    </tr>
	                    <tr>
	                        <td align="right">Packing </td>
	                        <td>
	                        <?	
	                            echo create_drop_down( "cbo_packing_country_level", 170, $packing,"", 1, "--Select--", $cbo_packing_po_level, "","","" ); ?>
	                        </td>
	                    </tr>
	        </table>
	        </fieldset>
	        <br/>        
	        <div id="size_color_breakdown">
	        </div>
	        <!-- </td>
		    <td valign="top" align="left" >
				<div id="job_po_list_view" width="400" style="padding-left: 2px; overflow: hidden;"></div>
				<div id="country_po_list_view" width="400" style="padding-left: 2px; margin-top: 6px "></div>
			</td>
	    </tr> -->
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>