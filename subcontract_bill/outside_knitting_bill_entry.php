<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Out Side Knitting Bill Entry		
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	24-07-2013
Updated by 		: 	up didar	
Update date		:   24-12-2017
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
echo load_html_head_contents("Kniting Bill Entry", "../", 1,1, $unicode,1,'');
?>
<script type="text/javascript">
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';

	var selected_id = new Array(); var selected_currency_id = new Array();
	var selected_id_listed = new Array();
	var selected_id_removed = new Array(); 
	function check_all_data()
	{
		var tbl_row_count = document.getElementById( 'list_view_issue' ).rows.length;
		tbl_row_count = tbl_row_count - 1;
		
		for( var i = 1; i <= tbl_row_count; i++ ) 
		{
			eval($('#tr_'+i).attr("onclick"));  
		}
	}

	function toggle( x, origColor ) 
	{
		var newColor = 'yellow';
		if ( x.style ) {
		x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}

	function js_set_value(id)
	{
		//alert (id)
		var str=id.split("***");
		 
		toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
		if( jQuery.inArray(  str[0] , selected_id ) == -1) 
		{
			selected_id.push( str[0] );
			
		}
		else
		{
			selected_id_removed.push( str[0] );
			
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str[0]  ) break;
			}
			selected_id.splice( i, 1 );
			
		}
		var id = ''; var currency = '';
		for( var i = 0; i < selected_id.length; i++ ) 
		{
			id += selected_id[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		
		$('#selected_id').val( id );
	}

	function set_all()
	{
		selected_id = new Array();
		var old=document.getElementById('issue_id_all').value;
		//alert (old)
		if(old!="")
		{   
			old=old.split(",");
			for(var i=0; i<old.length; i++)
			{   
				js_set_value(old[i]) 
			}
		}
	}

	function openmypage_bill()
	{
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_supplier_company').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/outside_knitting_bill_entry_controller.php?data='+data+'&action=bill_no_popup','Bill Popup', 'width=880px,height=400px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("outkintt_receive_id"); //Access form field with id="emailfield" 
			if (theemail.value!="")
			{
				freeze_window(5);
				get_php_form_data( theemail.value, "load_php_data_to_form_issue", "requires/outside_knitting_bill_entry_controller" );
				
				window_close( theemail.value );
				accounting_integration_check($('#hidden_acc_integ').val());
				/*var tot_row=$('#bill_issue_table tr').length;
				//alert(tot_row)
				var all_value="";
				for (var n=1; n<=tot_row; n++)
				{
					if(all_value=="") all_value+=$('#txtChallenno_'+n).val()+'_'+$('#ordernoid_'+n).val()+'_'+$('#itemid_'+n).val()+'_'+$('#bodyPartId_'+n).val()+'_'+$('#febDescId_'+n).val(); else all_value+='!!!!'+$('#txtChallenno_'+n).val()+'_'+$('#ordernoid_'+n).val()+'_'+$('#itemid_'+n).val()+'_'+$('#bodyPartId_'+n).val()+'_'+$('#febDescId_'+n).val();
				}
				//alert(all_value); return;
				show_list_view(document.getElementById('cbo_company_id').value+'***'+document.getElementById('cbo_location_name').value+'***'+document.getElementById('cbo_bill_for').value+'***'+document.getElementById('cbo_supplier_company').value+'***'+document.getElementById('txt_bill_form_date').value+'***'+document.getElementById('txt_bill_to_date').value+'***'+theemail.value+'***'+all_value,'knitting_entry_list_view','knitting_info_list','requires/outside_knitting_bill_entry_controller','','','');//set_all()*/
				
				fnc_populate_list_view();
				
		
				set_button_status(1, permission, 'fnc_knitting_bill_entry',1);
				//setFilterGrid('tbl_list_search',-1);
				$('#tbl_list_search tbody tr').each(function(index, element) {
					
					if( $('#'+this.id).attr('bgcolor')=='yellow' )
					{
						var nid=this.id;//.replace( 'tr_', "");  
						var nid=nid.replace( 'tr_', "");  
						if( jQuery.inArray(  nid , selected_id_listed ) == -1) 
						{
							selected_id_listed.push( nid );
						}
						else
						{
							for( var i = 0; i < selected_id_listed.length; i++ ) {
								if( selected_id_listed[i] == nid  ) break;
							}
							selected_id_listed.splice( i, 1 );
						}
					}
				});
				
				release_freezing();
			}
		}
	}

	var listed_id=Array(); var seq_arr=Array(); var uom_arr = new Array();

	function window_close( uid )
	{
		var html="";
		if(uid==0)
		{
			var list_tot_row=$('#tbl_list_search tr').length-1;
			var existtot_row=$('#bill_issue_table tr').length;
			
			var i=1;
			if(seq_arr!=0) i=seq_arr; else i=i;
			
			for (var k=1; k<=list_tot_row; k++)
			{
				var check_id=''; var str_val=''; var trid="";
				
				var check_id=$('#checkid'+k).val();
								
				var str_val=$('#strid'+k).val();

				var split_str=str_val.split('_');
				var trid=split_str[2]+"_"+split_str[3]+"_"+split_str[10]+"_"+split_str[8]+"_"+split_str[9];
				//alert(str_val)
				if( check_id!=1) 
				{  
					$("#trent_"+trid).remove();
					for( var p = 0; p < selected_id_listed.length; p++ ) {
						
						if( selected_id_listed[p] == trid  ) break;
					}
					selected_id_listed.splice( p, 1 );
					//selected_id_listed.push( trid ); 
				}
				
				if(check_id==1)
				{
					//var trid=split_str[2]+"_"+split_str[3]+"_"+split_str[10]+"_"+split_str[8]+"_"+split_str[9];
					if(selected_id_listed.length==0)
					{
						$("#bill_issue_table tr").remove();
					}
					if( jQuery.inArray(  trid , selected_id_listed ) == -1) 
					{
						selected_id_listed.push( trid );
						//alert(selected_id_listed)
						var rec_id=split_str[0];
						var rec_date=split_str[1];
						var challan_no=split_str[2];
						var po_id=split_str[3];
						var po_no=split_str[4];
						var style_ref=split_str[5];
						var buyer=split_str[6];
						var roll_no=split_str[7];
						var body_part_id=split_str[8];
						var fab_des_id=split_str[9];
						var prod_id=split_str[10];
						var prod_name=split_str[11];
						var wo_id='';//split_str[12];
						var wo_no='';//split_str[13];
						var rec_qty=split_str[12];
						var body_part_name=split_str[13];
						var qty_pcs=split_str[14];
						var is_sales=split_str[15];
						var body_part_type=split_str[16];
						//alert(qty_pcs);
						//var uom=split_str[14];
						//var rate=split_str[15];
						//var amount=split_str[16];
						//var remarks=split_str[17];
						
						var is_disable=""; var selected_uom="";
						//if (body_part_id==2 || body_part_id==3 || body_part_id==40 || body_part_id==172 || body_part_id==203 || body_part_id==214)
						if(body_part_type==40 || body_part_type==50)
						{
							selected_uom=1; is_disable="";
						}
						else
						{
							selected_uom=12; is_disable="disabled";
						}
						
						html+='<tr align="center" id="trent_'+trid+'"><td><input type="hidden" name="updateiddtls_'+i+'" id="updateiddtls_'+i+'" value=""><input type="hidden" name="reciveid_'+i+'" id="reciveid_'+i+'" value="'+rec_id+'"><input type="text" name="txtReceiveDate_'+i+'" id="txtReceiveDate_'+i+'" class="datepicker" style="width:60px" value="'+rec_date+'" disabled /></td><td><input type="text" name="txtChallenno_'+i+'" id="txtChallenno_'+i+'" class="text_boxes" style="width:60px" value="'+challan_no+'" readonly /></td><td><input type="hidden" name="ordernoid_'+i+'" id="ordernoid_'+i+'" value="'+po_id+'" style="width:50px" readonly /><input type="text" name="txtOrderno_'+i+'" id="txtOrderno_'+i+'"  class="text_boxes" style="width:50px" value="'+po_no+'" readonly /></td><td><input type="text" name="txtStylename_'+i+'" id="txtStylename_'+i+'" class="text_boxes" style="width:70px;" value="'+style_ref+'" readonly /></td><td><input type="text" name="txtPartyname_'+i+'" id="txtPartyname_'+i+'" class="text_boxes" style="width:50px" value="'+buyer+'" readonly /></td><td><input name="txtNumberroll_'+i+'" id="txtNumberroll_'+i+'" type="text" class="text_boxes_numeric" style="width:30px" value="'+roll_no+'" readonly /></td><td><input type="hidden" name="bodyPartId_'+i+'" id="bodyPartId_'+i+'" value="'+body_part_id+'"><input type="hidden" name="febDescId_'+i+'" id="febDescId_'+i+'" value="'+fab_des_id+'"><input type="hidden" name="itemid_'+i+'" id="itemid_'+i+'" value="'+prod_id+'"><input type="text" name="txtFebricdesc_'+i+'" id="txtFebricdesc_'+i+'" class="text_boxes" style="width:100px" title="'+prod_name+'" value="'+prod_name+'" readonly /></td><td><input type="text" name="textbodypart_'+i+'" id="textbodypart_'+i+'" class="text_boxes" style="width:60px" value="'+body_part_name+'" bodyparttype="'+body_part_type+'" readonly /></td><td><input type="hidden" name="txtwonumid_'+i+'" id="txtwonumid_'+i+'" value="'+wo_id+'"><input type="text" name="textWoNum_'+i+'" id="textWoNum_'+i+'" class="text_boxes" style="width:50px" value="'+wo_no+'" placeholder="Browse" onDblClick="openmypage_wonum('+i+');" readonly /></td><td><select name="cboUomWidthType[]" id="cbouom_'+i+'" class="text_boxes" style="width:50px" onchange="copyUomWidthType(this.id,this.value)"><option value="0">-UOM-</option><option value="1">Pcs</option><option value="2">Dzn</option><option value="12">Kg</option></select></td><td><input type="text" name="txtQnty_'+i+'" id="txtQnty_'+i+'" class="text_boxes_numeric" onBlur="qty_caculation('+i+');" style="width:50px" value="'+rec_qty+'" readonly /></td><td><input type="text" name="txtqntypcs_'+i+'" id="txtqntypcs_'+i+'" class="text_boxes_numeric" style="width:50px" onBlur="amount_caculation('+i+');" '+is_disable+' value="'+qty_pcs+'" /></td><td><input type="text" name="txtRate_'+i+'" id="txtRate_'+i+'" class="text_boxes_numeric" style="width:30px" value="" onBlur="amount_caculation('+i+'); fnc_rate_copy('+i+');" /><input type="hidden" name="txthiddenrate_'+i+'" id="txthiddenrate_'+i+'" class="text_boxes_numeric" style="width:40px" value="" /></td><td><input type="text" name="txtAmount_'+i+'" id="txtAmount_'+i+'" style="width:50px" class="text_boxes_numeric" value="" readonly /></td><td><? echo create_drop_down( "curanci_'+i+'", 60, $currency,"", 1, "-Select Currency-",1,"",1,"" ); ?></td><td><input type="button" name="remarks_'+i+'" id="remarks_'+i+'" class="formbuttonplasminus" style="width:20px" value="R" onClick="openmypage_remarks('+i+');" /><input type="hidden" name="txtRemarks_'+i+'" id="txtRemarks_'+i+'" class="text_boxes" value="" /></td></tr>';
						uom_arr[i]=selected_uom;
						i++;
					}
				}
			}
			seq_arr=i;
		}
		else
		{
			$("#bill_issue_table tr").remove();
			var list_view_str = return_global_ajax_value( uid+'__'+$('#cbo_bill_for').val(), 'load_dtls_data', '', 'requires/outside_knitting_bill_entry_controller');
			var split_list_view=list_view_str.split('###');//
			var m=1; var mn=0;
			for (var n=1; n<=split_list_view.length; n++)
			{
				var split_list_str=split_list_view[mn].split('_');
				var trid=split_list_str[2]+"_"+split_list_str[3]+"_"+split_list_str[10]+"_"+split_list_str[8]+"_"+split_list_str[9];
				//alert(split_list_str)
				var rec_id=trim(split_list_str[0]);
				var rec_date=trim(split_list_str[1]);
				var challan_no=split_list_str[2];
				var po_id=trim(split_list_str[3]);
				var po_no=split_list_str[4];
				var style_ref=split_list_str[5];
				var buyer=split_list_str[6];
				var roll_no=split_list_str[7];
				var body_part_id=trim(split_list_str[8]);
				var fab_des_id=trim(split_list_str[9]);
				var prod_id=trim(split_list_str[10]);
				var prod_name=split_list_str[11];
				var wo_id='';//split_list_str[12];
				var wo_no='';//split_list_str[13];
				var rec_qty=split_list_str[12];
				var body_part_name=split_list_str[13];
				//var uom=split_str[13];
				var rate=split_list_str[14];
				var amount=split_list_str[15];
				var remarks=split_list_str[16];
				var uom=split_list_str[17];
				var dtls_id=trim(split_list_str[18]);
				var qty_pcs=trim(split_list_str[19]);
				var wo_no=trim(split_list_str[20]);
				var wo_id=trim(split_list_str[21]);
				var body_part_type=trim(split_list_str[23]);
				var currency_id=1;
				//console.log(split_list_str.length);
				if(split_list_str.length>22)
				{

					currency_id=trim(split_list_str[22]);

				}
				$("#hidden_currency_id").val(currency_id);
				//console.log("hidden_currency_id="+document.getElementById('hidden_currency_id').value);
				
				//echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
				
				//console.log(<?= $abc; ?>);
				<? 
				$abc = "<script>document.write(currency_id)</script>";
				//$dropdown= create_drop_down( "curanci_'+m+'", 60, $currency,"", 1, "-Select Currency-",1,"",1,"" );
				  $js_array = json_encode($currency);
				  echo "var javascript_currency_arr = ". $js_array . ";\n";
				 ?>
				 var dropdown='<input type="hidden" name="curanci_'+m+'" id="curanci_'+m+'" style="width:50px" class="text_boxes_numeric" value="'+currency_id+'" readonly /><input type="text" name="curan_'+m+'" id="curan_'+m+'" style="width:50px" class="text_boxes" value="'+javascript_currency_arr[currency_id]+'" readonly />';
				
				var is_disable="";
				//if (body_part_id==2 || body_part_id==3 || body_part_id==40 || body_part_id==172 || body_part_id==203 || body_part_id==214)
				if(body_part_type==40 || body_part_type==50)
				{
					is_disable="";
				}
				else
				{
					is_disable="disabled";
				}
				//listed_id[]=listed_id;
				html+='<tr align="center" id="trent_'+trid+'"><td><input type="hidden" name="updateiddtls_'+m+'" id="updateiddtls_'+m+'" value="'+dtls_id+'"><input type="hidden" name="reciveid_'+m+'" id="reciveid_'+m+'" value="'+rec_id+'"><input type="text" name="txtReceiveDate_'+m+'" id="txtReceiveDate_'+m+'" class="datepicker" style="width:60px" value="'+rec_date+'" disabled /></td><td><input type="text" name="txtChallenno_'+m+'" id="txtChallenno_'+m+'" class="text_boxes" style="width:60px" value="'+challan_no+'" readonly /></td><td><input type="hidden" name="ordernoid_'+m+'" id="ordernoid_'+m+'" value="'+po_id+'" style="width:50px" readonly /><input type="text" name="txtOrderno_'+m+'" id="txtOrderno_'+m+'"  class="text_boxes" style="width:50px" value="'+po_no+'" readonly /></td><td><input type="text" name="txtStylename_'+m+'" id="txtStylename_'+m+'"  class="text_boxes" style="width:70px;" value="'+style_ref+'" readonly /></td><td><input type="text" name="txtPartyname_'+m+'" id="txtPartyname_'+m+'" class="text_boxes" style="width:50px" value="'+buyer+'" readonly /></td><td><input name="txtNumberroll_'+m+'" id="txtNumberroll_'+m+'" type="text" class="text_boxes_numeric" style="width:30px" value="'+roll_no+'" readonly /></td><td><input type="hidden" name="bodyPartId_'+m+'" id="bodyPartId_'+m+'" value="'+body_part_id+'"><input type="hidden" name="febDescId_'+m+'" id="febDescId_'+m+'" value="'+fab_des_id+'"><input type="hidden" name="itemid_'+m+'" id="itemid_'+m+'" value="'+prod_id+'"><input type="text" name="txtFebricdesc_'+m+'" id="txtFebricdesc_'+m+'" class="text_boxes" style="width:100px" title="'+prod_name+'" value="'+prod_name+'" readonly /></td><td><input type="text" name="textbodypart_'+m+'" id="textbodypart_'+m+'" class="text_boxes" style="width:60px" value="'+body_part_name+'" bodyparttype="'+body_part_type+'" readonly /></td><td><input type="hidden" name="txtwonumid_'+m+'" id="txtwonumid_'+m+'" value="'+wo_id+'"><input type="text" name="textWoNum_'+m+'" id="textWoNum_'+m+'" class="text_boxes" style="width:50px" value="'+wo_no+'" placeholder="Browse" onDblClick="openmypage_wonum('+m+');" readonly /></td><td><select name="cboUomWidthType[]" id="cbouom_'+m+'" class="text_boxes" style="width:50px" onchange="copyUomWidthType(this.id,this.value)"><option value="0">-UOM-</option><option value="1">Pcs</option><option value="2">Dzn</option><option value="12">Kg</option></select></td><td><input type="text" name="txtQnty_'+m+'" id="txtQnty_'+m+'" class="text_boxes_numeric" onBlur="qty_caculation('+m+');" style="width:50px" value="'+rec_qty+'" readonly /></td><td><input type="text" name="txtqntypcs_'+m+'" id="txtqntypcs_'+m+'" class="text_boxes_numeric" style="width:50px" onBlur="amount_caculation('+m+');" value="'+qty_pcs+'" '+is_disable+' /></td><td><input type="text" name="txtRate_'+m+'" id="txtRate_'+m+'" class="text_boxes_numeric" style="width:30px" value="'+rate+'" onBlur="amount_caculation('+m+'); fnc_rate_copy('+m+');" /><input type="hidden" name="txthiddenrate_'+m+'" id="txthiddenrate_'+m+'" class="text_boxes_numeric" style="width:40px" value="'+rate+'" /></td><td><input type="text" name="txtAmount_'+m+'" id="txtAmount_'+m+'" style="width:50px" class="text_boxes_numeric" value="'+amount+'" readonly /></td><td>'+dropdown+'</td><td><input type="button" name="remarks_'+m+'" id="remarks_'+m+'" class="formbuttonplasminus" style="width:20px" value="R" onClick="openmypage_remarks('+m+');" /><input type="hidden" name="txtRemarks_'+m+'" id="txtRemarks_'+m+'" class="text_boxes" value="" /></td></tr>';
				
				uom_arr[m]=uom;
				mn++;
				m++;
			}
			seq_arr=m;
		}
		

		$("#bill_issue_table").append( html );
		
		var counter =$('#tb_bill_ent tbody tr').length; 
		for(var q=1; q<=counter; q++)
		{
			var index=q-1;
			$("#tb_bill_ent tbody tr:eq("+index+")").find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ q },
				}); 
				$('#txtRate_'+q).removeAttr("onBlur").attr("onBlur","amount_caculation("+q+"); fnc_rate_copy("+q+");");
			})
			$("#cbouom_"+q).val( uom_arr[q] );
		}
		var tot_row=$('#bill_issue_table tr').length;
		ddd={dec_type:2,comma:0};
		math_operation( "txt_tot_qnty", "txtQnty_", "+", tot_row,ddd );
		math_operation( "total_qntyPcs", "txtqntypcs_", "+", tot_row,ddd );
		math_operation( "txt_tot_amount", "txtAmount_", "+", tot_row,ddd );
		set_all_onclick();
	}

	function amount_caculation_old(id)
	{
		var uom_id=$("#cbouom_"+id).val();
		//alert(uom_id);
		if(uom_id==1) //pcs
		{
			var amount = ($("#txtqntypcs_"+id).val()*1)*($("#txtRate_"+id).val()*1);
		}
		else if(uom_id==12){
			var amount = ($("#txtQnty_"+id).val()*1)*($("#txtRate_"+id).val()*1);
		}
		var tot_row=$('#bill_issue_table tr').length;
		//var amount = ($("#txtqntypcs_"+id).val()*1)*($("#txtRate_"+id).val()*1);
		var amount_chk = ($("#txtqntypcs_"+id).val()*1)*($("#txthiddenrate_"+id).val()*1);
		//var txtrate=$("#txtrate_"+id).val()*1;
		//var wo_no=$("#textwonum_"+id).val();
		//var hidden_rate=$("#txthiddenrate_"+id).val()*1;
		// if(wo_no!="")
		// {
		// 	if(txtrate>hidden_rate)
		// 	{
		// 		$("#txtrate_"+id).val(hidden_rate);
		// 		var msg='Bill Rate is over than WO Rate';
		// 		//$("#txtamount_"+id).val(amount_chk);
		// 		alert(msg);
		// 		 return;
		// 	}
		// }
		
		$("#txtamount_"+id).val( amount.toFixed(4) );
		ddd={dec_type:5,comma:0};
		math_operation( "total_qntyPcs", "txtqntypcs_", "+", tot_row,ddd );
		math_operation( "txt_tot_amount", "txtAmount_", "+", tot_row,ddd );
	}

	function fnc_rate_copy( trid )
	{
		
		var is_ratecopy=document.getElementById('checkrate').value;
		var txtrate=$('#txtRate_'+trid).val();
		if(is_ratecopy==1)
		{	
			var row_nums=$('#bill_issue_table tr').length;
			for(var j=trid; j<=row_nums; j++)
			{
				//alert(trid);
				document.getElementById('txtRate_'+j).value=txtrate;
				amount_caculation(j);
			}
		}
	}
	
	function qty_caculation()
	{
		var tot_row=$('#bill_issue_table tr').length;
		ddd={dec_type:2,comma:0};
		math_operation( "txt_tot_qnty", "txtQnty_", "+", tot_row,ddd );
		math_operation( "txt_tot_amount", "txtAmount_", "+", tot_row,ddd );
	}
	
	function openmypage_wonum(i)
	{ 
		var reciveid=$("#reciveid_"+i).val();
		var fso_id=$("#ordernoid_"+i).val();
		var cbo_bill_for=$("#cbo_bill_for").val();
		//alert(reciveid);
		var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_supplier_company').value+"_"+reciveid+"_"+cbo_bill_for+"_"+fso_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/outside_knitting_bill_entry_controller.php?action=wo_num_popup&data='+data,'Wo Number Popup', 'width=950px,height=400px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("hidd_item_id") 
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				//alert (response[0]);
				txt_bill_no=document.getElementById('txt_bill_no').value;
				update_id=document.getElementById('update_id').value;
				console.log(txt_bill_no);
				console.log(update_id);
				//var tot_row=$('#bill_issue_table tr').length;
				document.getElementById('txtwonumid_'+i).value=response[0];
				document.getElementById('textWoNum_'+i).value=response[1];
				document.getElementById('txtRate_'+i).value=response[2];
				document.getElementById('txthiddenrate_'+i).value=response[2];
				if(response.length>3 )
				{
					if(response[2] && txt_bill_no.length==0 && update_id.length==0)
					{
						document.getElementById('curanci_'+i).value=response[3];
					}
				}
				// for(var k=1; k<=tot_row; k++)
				// {
					
						//document.getElementById('txtwonumid_'+k).value=response[0];
						//document.getElementById('textWoNum_'+k).value=response[1];
						//document.getElementById('txtRate_'+k).value=response[2];
				// 	if(response.length>3 && i==k)
				// 	{
				// 		if(response[2] && txt_bill_no.length==0 && update_id.length==0)
				// 		{
				// 			document.getElementById('curanci_'+k).value=response[3];
				// 		}
						
				// 	}
				// }
				exchenge_rate_val(response[2])
				release_freezing();
			}
		}
	}

	function exchenge_rate_val(rate)
	{
		var tot_row=$('#bill_issue_table tr').length;
		var amount_total=0;
		for(var k=1; k<=tot_row; k++)
		{
			amount_total=(document.getElementById('txtQnty_'+k).value*1)*(rate*1);
			document.getElementById('txtAmount_'+k).value=amount_total;
		}
	}
	
	function fnc_knitting_bill_entry( operation )
	{
		var party_source=$("#cbo_party_source").val();

		if($('#hidden_acc_integ').val()==1)
		{
			show_msg('13');
			return;
		}

		var isFileMandatory = "";
		<?php 
			
			if(!empty($_SESSION['logic_erp']['mandatory_field'][622][1])) echo " isFileMandatory = ". $_SESSION['logic_erp']['mandatory_field'][622][1] . ";\n";
		?>
		// alert(isFileMandatory); return;
		if($("#multiple_file_field")[0].files.length==0 && isFileMandatory!="" && $('#update_id').val()==''){

			document.getElementById("multiple_file_field").focus();
			var bgcolor='-moz-linear-gradient(bottom, rgb(254,151,174) 0%, rgb(255,255,255) 10%, rgb(254,151,174) 96%)';
			document.getElementById("multiple_file_field").style.backgroundImage=bgcolor;
			alert("Please Add File in Master Part");
			return;	
		}
		if(party_source==3) //Out Bound
		{
			if( form_validation('cbo_company_id*cbo_location_name*txt_bill_date*cbo_supplier_company','Company Name*Location*Bill Date*supplier company')==false)
			{ 
				return;
			}
		}
		if( form_validation('cbo_company_id*cbo_location_name*txt_bill_date*cbo_bill_for','Company Name*Location*Bill Date*bill for')==false)
		{ 
			return;
		}
		else
		{
			var tot_row=$('#bill_issue_table tr').length;
			var mandatory_check = $('#mandatory_check').val()*1;				
			var data1="action=save_update_delete&operation="+operation+"&tot_row="+tot_row+get_submitted_data_string('txt_bill_no*cbo_company_id*cbo_location_name*txt_bill_date*cbo_supplier_company*cbo_bill_for*txt_upcharge*txt_discount*txt_party_bill_no*cbo_party_source*update_id',"../");
			
			var selected_currecny=new Array();
			for(var i=1; i<=tot_row; i++)
			{
				var currecny=$('#curanci_'+i).val();
			//	currency_chk_arr+=currecny+',';	
				
				var str =$('#curanci_' + i).val();
				
				for( var s = 0; s < selected_currecny.length; s++ ) {
					if( selected_currecny[s]!= str ){
						//release_freezing();
						alert("Duplicate Currency not allowed");
						return;
					}
				}
				selected_currecny.push(str);
				 
			}
		
			var data2='';
			for(var i=1; i<=tot_row; i++)
			{
				
				if(mandatory_check==1)
				{
					if (form_validation('textWoNum_'+i,'WO NO')==false)
					{
						$('#textWoNum_'+i).focus();
						return;
					}
				}
			
				if (form_validation('cbouom_'+i+'*txtRate_'+i,'Uom*Rate')==false)
				{
					return;
				}
				else if($('#txtRate_'+i).val()==0)
				{
					alert ("Rate Not Blank or Zero.");
					$('#txtRate_'+i).focus();
					return;
				}
				data2+=get_submitted_data_string('txtReceiveDate_'+i+'*txtChallenno_'+i+'*ordernoid_'+i+'*itemid_'+i+'*bodyPartId_'+i+'*febDescId_'+i+'*txtwonumid_'+i+'*txtNumberroll_'+i+'*txtQnty_'+i+'*txtqntypcs_'+i+'*cbouom_'+i+'*txtRate_'+i+'*txtAmount_'+i+'*txtRemarks_'+i+'*txtStylename_'+i+'*reciveid_'+i+'*txtPartyname_'+i+'*updateiddtls_'+i+'*curanci_'+i,"../",i);
				
			}
			var data=data1+data2;
			
			freeze_window(operation);
			http.open("POST","requires/outside_knitting_bill_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_knitting_bill_entry_reponse;
		}
	}

	function fnc_knitting_bill_entry_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert (http.responseText);
			var response=trim(http.responseText).split('**');
			if(trim(response[0])==10) release_freezing();
			else
			{
				show_msg(response[0]);
				var check_system_id=$("#update_id").val();
				document.getElementById('update_id').value = response[1];
				if (check_system_id=="") uploadFile( $("#update_id").val());
				document.getElementById('txt_bill_no').value = response[2];
				window_close(response[1]);
				set_button_status(1, permission, 'fnc_knitting_bill_entry',1);
				release_freezing();
			}
		}
	}

	function uploadFile(mst_id)
	{
		$(document).ready(function() { 
			 
			var suc=0;
			var fail=0;
			for( var i = 0 ; i < $("#multiple_file_field")[0].files.length ; i++)
			{
				var fd = new FormData();
				console.log($("#multiple_file_field")[0].files[i]);
				var files = $("#multiple_file_field")[0].files[i]; 
				fd.append('file', files); 
				// alert(mst_id);
				$.ajax({
					url: 'requires/outside_knitting_bill_entry_controller.php?action=file_upload&mst_id='+ mst_id, 
					type: 'post', 
					data:fd, 
					contentType: false, 
					processData: false, 
					success: function(response){
						var res=response.split('**');
						if(res[0] == 0){ 
							
							suc++;
						}
						else if(fail==0)
						{
							alert('file not uploaded');
							fail++;
						}
					}, 
				}); 
			}

			if(suc > 0 )
			{
				 document.getElementById('multiple_file_field').value='';
			}
		}); 
	}
	
	function amount_caculation(id)
	{
		var body_part=$('#bodyPartId_'+id).val();
		var bodyPartType=$('#textbodypart_'+id).attr('bodyparttype');
		var hidden_rate=$('#txthiddenrate_'+id).val();
		var txtrate=$("#txtRate_"+id).val()*1;
		var wo_no=$("#textWoNum_"+id).val();
		
		//alert(body_part)
		//if(body_part==2 || body_part==3 || body_part==40 || body_part==172 || body_part==203 || body_part==214)
		if(bodyPartType==40 || bodyPartType==50)
		{
			var delv_qty=$('#txtqntypcs_'+id).val();
		}
		else
		{
			var delv_qty=$('#txt_qnty_'+id).val();
		}
		if(wo_no!="")
		{
			if(txtrate>hidden_rate)
			{
				$("#txtRate_"+id).val(hidden_rate);
				var msg='Bill Rate is over than WO Rate';
				alert(msg);
				 return;
			}
		}
		
		var tot_amount='';
		tot_amount=(delv_qty*1)*(document.getElementById('txtRate_'+id).value*1);
		document.getElementById('txtAmount_'+id).value=tot_amount;
		ddd={dec_type:2,comma:0};
		math_operation( "txt_tot_amount", "txtAmount_", "+", id,ddd );
		
		var body_part=$('#bodyPartId_'+id).val();//textbodypart_1
		var uom_id=$("#cbouom_"+id).val();
		//alert(body_part)
		//if(body_part==2 || body_part==3 || body_part==40 || body_part==172 || body_part==203 || body_part==214)
		if(uom_id==1) //pcs
		{
			var delv_qty=$('#txtqntypcs_'+id).val();
		}
		else
		{
			var delv_qty=$('#txtQnty_'+id).val();
		}
		//alert(body_part);
		$("#txtAmount_"+id).val((delv_qty*1)*($("#txtRate_"+id).val()*1));
		var tot_row=$('#bill_issue_table tr').length;
		//math_operation( "txt_tot_qnty", "txtRate_", "+", tot_row );
		ddd={dec_type:2,comma:0};
		math_operation( "total_qntyPcs", "txtqntypcs_", "+", tot_row,ddd );
		math_operation( "txt_tot_amount", "txtAmount_", "+", tot_row,ddd );
	}
	
	function fnc_check(inc_id)
	{
		//alert(inc_id)
		if(inc_id=="all")
		{
			if(document.getElementById('checkall').checked==true)
			{
				document.getElementById('checkall').value=1;
			}
			else if(document.getElementById('checkall').checked==false)
			{
				document.getElementById('checkall').value=2;
			}
		}
		else if(inc_id=="rate")
		{
			if(document.getElementById('checkrate').checked==true)
			{
				document.getElementById('checkrate').value=1;
			}
			else if(document.getElementById('checkrate').checked==false)
			{
				document.getElementById('checkrate').value=2;
			}
		}
		else
		{
			if(document.getElementById('checkid'+inc_id).checked==true)
			{
				document.getElementById('checkid'+inc_id).value=1;
			}
			else if(document.getElementById('checkid'+inc_id).checked==false)
			{
				document.getElementById('checkid'+inc_id).value=2;
			}
		}
	}
	
	function fnc_populate_list_view()
	{
		var party_source=document.getElementById('cbo_party_source').value;
		if(party_source==3)
		{
			if( form_validation('cbo_company_id*cbo_location_name*cbo_bill_for*cbo_supplier_company*txt_bill_form_date*txt_bill_to_date','Company Name*Location*Bill For*Supplier Name*Form Date*Form Date')==false)
			{
				return;
			}
		}
		else{
			if( form_validation('cbo_company_id*cbo_location_name*cbo_bill_for*txt_bill_form_date*txt_bill_to_date','Company Name*Location*Bill For*Form Date*Form Date')==false)
			{
				return;
			}
		}

		
		
		var tot_row=$('#bill_issue_table tr').length;
		
		$('#cbo_company_id').attr('disabled','disabled');
		$('#cbo_location_name').attr('disabled','disabled');
		$('#cbo_bill_for').attr('disabled','disabled');
		$('#cbo_supplier_company').attr('disabled','disabled');
		//alert(tot_row) 
		
		show_list_view(document.getElementById('cbo_company_id').value+'***'+document.getElementById('cbo_location_name').value+'***'+document.getElementById('cbo_bill_for').value+'***'+document.getElementById('cbo_supplier_company').value+'***'+document.getElementById('txt_bill_form_date').value+'***'+document.getElementById('txt_bill_to_date').value+'***'+document.getElementById('update_id').value+'***'+document.getElementById('txt_job_id').value+'***'+document.getElementById('cbo_party_source').value+'***'+document.getElementById('cbo_supplier_company').value,'knitting_entry_list_view','knitting_info_list','requires/outside_knitting_bill_entry_controller','setFilterGrid("tbl_list_search",-1);','','');
	}
	
	function openmypage_remarks(id)
	{
		var data=document.getElementById('txtRemarks_'+id).value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/outside_knitting_bill_entry_controller.php?data='+data+'&action=remarks_popup','Remarks Popup', 'width=420px,height=320px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("text_new_remarks");//Access form field with id="emailfield"
			if (theemail.value!="")
			{
				$('#txtRemarks_'+id).val(theemail.value);
			}
		}
	}

	function enable_disable(data)
	{
		//order_head
		var bill_for_id=$('#cbo_bill_for').val();
		if(data !=3)
		{
			$('#txt_job_no').removeAttr('disabled','disabled');
		}
		else
		{
			$('#txt_job_no').attr('disabled','disabled');
		}
		if(bill_for_id ==4)
		{
			document.getElementById('order_head').innerHTML='FSO No';
		}
		else{
			document.getElementById('order_head').innerHTML='Order No';

		}
		
	}

	function openmypage_job()
	{ 
		if(form_validation('cbo_company_id*cbo_location_name*cbo_bill_for*cbo_supplier_company','Company Name*Location*Bill For*Supplier Name')==false){ return; }

		var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_bill_for').value+"_"+document.getElementById('cbo_supplier_company').value;

		page_link='requires/outside_knitting_bill_entry_controller.php?action=job_popup&data='+data;
		title='Job No.';

		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=790px, height=420px, center=1, resize=0, scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemail=this.contentDoc.getElementById("selected_job");
			var theemaildata=theemail.value; 
			var new_data=theemaildata.split('_');
			$("#txt_job_no").val(new_data[0]);
			$("#txt_job_id").val(new_data[1]);
			fnc_populate_list_view();
		}
	}
	
	function fnc_print_report(report_type)
	{
		if(report_type==0)
		{
			var show_val_column='';
			var r=confirm("Press \"OK\" to open with Order Comments\nPress \"Cancel\" to open without Order Comments");
			if (r==true) show_val_column="1"; else show_val_column="0";
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_bill_no').val()+'*'+report_title+'*'+show_val_column,'outbound_knitting_bill_print','requires/outside_knitting_bill_entry_controller');
			//return;
			show_msg("3");
		}
		if(report_type==1) //Fso
		{
			var show_val_column='';
			var r=confirm("Press \"OK\" to open with Order Comments\nPress \"Cancel\" to open without Order Comments");
			if (r==true) show_val_column="1"; else show_val_column="0";
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_bill_no').val()+'*'+report_title+'*'+show_val_column,'outbound_knitting_bill_fso_print','requires/outside_knitting_bill_entry_controller');
			//return;
			show_msg("3");
		}
	}
	
	function generate_report_file(data,action,page)
	{
		window.open("requires/outside_knitting_bill_entry_controller.php?data=" + data+'&action='+action, true );
	}
	function accounting_integration_check(val)
	{
		var tot_row=$('#bill_issue_table tr').length;
		if(val==1)
		{
			$('#cbo_company_id').attr('disabled','disabled');
			$('#cbo_location_name').attr('disabled','disabled');
			$('#txt_bill_date').attr('disabled','disabled');
			$('#cbo_bill_for').attr('disabled','disabled');
			$('#cbo_supplier_company').attr('disabled','disabled');
			$('#txt_party_bill_no').attr('disabled','disabled');
			$('#txt_bill_form_date').attr('disabled','disabled');
			$('#txt_bill_to_date').attr('disabled','disabled');
		}
		
	}
	function print_button_setting()
	{
	//  console.log('hello');
		//$('#button_data_panel').html('');
		get_php_form_data($('#cbo_company_id').val(),'print_button_variable_setting','requires/outside_knitting_bill_entry_controller' );
	}
	function fnc_net_calculation()//here
	{
		//var tot_row=$('#bill_issue_table tr').length;
			var tot_amount=$('#txt_tot_amount').val()*1;
			var txt_upcharge=$('#txt_upcharge').val()*1;
			var txt_discount=$('#txt_discount').val()*1;
			var totamount=tot_amount+txt_upcharge;
			var tot_amount_cal=totamount-txt_discount;
			
			
			//$('#txt_tot_qnty').val(number_format(totqty,2,'.','' ));
			$('#txt_net_total').val(number_format(tot_amount_cal,2,'.','' ));
	}

	function copyUomWidthType(id,value)
            {
                var idArr = id.split("_");
                var sl = idArr[1]*1;
                // alert(value);
				// console.log("hello 1")

                if($('#checkuom').is(':checked'))
                {
                    $("#tb_bill_ent").find('tbody tr').each(function()
                    {
                        var UomWidthType = $(this).find('select[name="cboUomWidthType[]"]').attr("id");
                        var UomWidthTypeSlArr = UomWidthType.split("_");
                        // copy only that and below selected data
                        if( sl <= UomWidthTypeSlArr[1]*1 )
                        {
                            $("#cbouom_"+UomWidthTypeSlArr[1]*1).val(value);

                        }
                    });
                }
            }
	

</script>
</head>
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
    <? echo load_freeze_divs ("../",$permission);  ?>
    <form name="outknittingbill_1" id="outknittingbill_1"  autocomplete="off"  >
    <fieldset style="width:900px;">
    <legend>Knitting Bill Info </legend>
        <table cellpadding="0" cellspacing="2" width="100%">
            <tr>
                <td align="right" colspan="3"><strong>Bill No</strong></td>
                <td width="140" colspan="3">
                	<input type="hidden" name="hidden_acc_integ" id="hidden_acc_integ" />
                    <input type="hidden" name="update_id" id="update_id" />
                    <input type="hidden" name="mandatory_check" id="mandatory_check" />
                    <input type="text" name="txt_bill_no" id="txt_bill_no" class="text_boxes" style="width:140px" placeholder="Double Click to Search" onDblClick="openmypage_bill();" readonly tabindex="1" >
                </td>
             </tr>
             <tr>
                <td width="110" class="must_entry_caption">Company</td>
                <td width="150"><?php echo create_drop_down( "cbo_company_id",145,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "print_button_setting();load_drop_down( 'requires/outside_knitting_bill_entry_controller', this.value, 'load_drop_down_location', 'location_td');get_php_form_data(this.value,'load_variable_settings','requires/outside_knitting_bill_entry_controller')","","","","","",2);
				//load_drop_down( 'requires/outside_knitting_bill_entry_controller', this.value, 'load_drop_down_supplier_name', 'supplier_td');
				?></td>
                <td width="110" class="must_entry_caption">Location</td>                                              
                <td width="150" id="location_td"><? echo create_drop_down( "cbo_location_name", 145, $blank_array,"", 1, "--Select Location--", $selected,"","","","","","",3); ?></td>
                <td width="110" class="must_entry_caption">Bill Date</td>                                              
                <td><input class="datepicker" type="text" style="width:130px" name="txt_bill_date" id="txt_bill_date" tabindex="4" /></td>
            </tr> 
             <tr>
              <td width="110" class="must_entry_caption">Party Source</td>
                <td width="150"><? echo create_drop_down( "cbo_party_source", 150, $knitting_source,"", 1, "-- Select Party --", $selected, "load_drop_down( 'requires/outside_knitting_bill_entry_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_party_name', 'party_td' ); fnc_bill_for(this.value);",0,"1,3","","","",5); ?></td>
               
                 <td class="must_entry_caption">Knitting Party</td>
                <td id="party_td"><? echo create_drop_down( "cbo_supplier_company", 145, $blank_array,"", 1, "-- Select supplier --", $selected, "",0,"","","","",5); ?></td>
                
             </tr>
            <tr>
            	<td class="must_entry_caption">Bill For</td>
                <td><? echo create_drop_down( "cbo_bill_for", 145, $bill_for,"", 1, "-- Select --", $selected, "enable_disable(this.value);",0,"","","",7); ?></td>
               
                <td>Party Bill No</td>
                <td><input type="text" name="txt_party_bill_no" id="txt_party_bill_no" class="text_boxes" style="width:130px" /></td>
                 <td>Job No.</td>
                <td>
                	<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:140px" placeholder="Double Click to Search" onDblClick="openmypage_job();" readonly disabled>
                	<input type="hidden" name="txt_job_id" value="" class="txt_job_id" id="txt_job_id">
                </td>  
            </tr>
            <tr>
                <td class="must_entry_caption">Form Date</td>                                              
                <td><input class="datepicker" type="text" style="width:130px" name="txt_bill_form_date" id="txt_bill_form_date" /></td>
                <td class="must_entry_caption">To Date</td>                                              
                <td><input class="datepicker" type="text" style="width:130px" name="txt_bill_to_date" id="txt_bill_to_date" /></td>
                                                        
            </tr>
            <tr>
				<td><input type="file" class="image_uploader" id="multiple_file_field" name="multiple_file_field" multiple style="width:130px"></td>

				<td><input type="button" id="file_uploaded" class="image_uploader" style="width:130px" value="ADD FILE" onClick="file_uploader ( '../', document.getElementById('update_id').value,'', 'outside_knitting_bill_entry', 2 ,1)"></td>
            	<td><input class="formbutton" type="button" onClick="fnc_populate_list_view();" style="width:130px" name="btn" value="Populate" id="btn" /></td>
            	<input type="hidden" name="hidden_currency_id" id="hidden_currency_id">
            </tr>

        </table>
    </fieldset>
    <br>
    <fieldset style="width:980px;">
    <legend>Knitting Bill Info </legend>
        <table style="border:none; width:980px;" cellpadding="0" cellspacing="1" border="0" id="tb_bill_ent">
            <thead class="form_table_header">
                <th width="70" class="must_entry_caption">Receive Date </th>
                <th width="70" class="must_entry_caption">Sys. Challan</th>
                <th width="60" id="order_head">Order No.</th>
                <th width="80">Style</th>
                <th width="60">Buyer</th>
                <th width="40">N.O Roll</th>
                <th width="110">Fabric Descp</th>
                <th width="70">Body Part</th>
                <th width="60">WO Num</th>
                <th width="50" >UOM <input type="checkbox" name="cboUomWidthType" id="checkuom" value="2" ></th>
                <th width="60">Fabric Qty (Wgt.)</th>
                <th width="60">Fabric Qty (Pcs.)</th>
                <th width="40" class="must_entry_caption">Rate<input type="checkbox" name="checkrate" id="checkrate" onClick="fnc_check('rate'); " value="2" ></th>
                <th width="60">Amount</th>
                <th width="60">Currency</th>
                <th>RMK</th>
            </thead>
            <tbody id="bill_issue_table">
                <tr align="center">				
                    <td>
                    	<input type="hidden" name="updateiddtls_1" id="updateiddtls_1" value="">
                        <input type="hidden" name="reciveid_1" id="reciveid_1" style="width:70px">
                        <input type="text" name="txt_receive_date_1" id="txt_receive_date_1" class="datepicker" style="width:60px" disabled/>									
                    </td>
                    <td><input type="text" name="txtChallenno_1" id="txtChallenno_1" class="text_boxes" style="width:60px" readonly /> </td>
                    <td>
                        <input type="hidden" name="ordernoid_1" id="ordernoid_1"value="" style="width:40px">
                        <input type="text" name="txt_orderno_1" id="txt_orderno_1" class="text_boxes" style="width:50px" readonly />										
                    </td>
                    <td><input type="text" name="txt_stylename_1" id="txt_stylename_1" class="text_boxes" style="width:70px;" /></td>
                    <td><input type="text" name="txt_partyname_1" id="txt_partyname_1" class="text_boxes" style="width:50px" /></td>
                    <td><input type="text" name="txt_numberroll_1" id="txt_numberroll_1" class="text_boxes" style="width:30px" readonly /></td>  
                    <td>
                    	<input type="text" name="text_febricdesc_1" id="text_febricdesc_1" class="text_boxes" style="width:100px" readonly/>
                        <input type="hidden" name="febDescId_1" id="febDescId_1" value="">
                    	<input type="hidden" name="itemid_1" id="itemid_1" value="">
                    </td>
                    <td>
                    	<input type="text" name="textbodypart_1" id="textbodypart_1" class="text_boxes" style="width:60px" readonly/>
                    	<input type="hidden" name="bodyPartId_1" id="bodyPartId_1" value="">
                    </td>
                    <td><input type="text" name="text_wo_num_1" id="text_wo_num_1" class="text_boxes" style="width:50px" placeholder="Browse" onDblClick="openmypage_wonum(1);" readonly/></td>
					
					<td>
						
					<? 
						$cboUomWidthType=array(1,2,12);
						echo create_drop_down( "cbouom_1", 50, $unit_of_measurement,"", 1, "-UOM-",0,"copyUomWidthType(this.id,this.value)", "", implode(",",$cboUomWidthType),"" , "", "", "", "", 'cboUomWidthType[]' );
					?>
				
				
				</td>

                    <td><input type="text" name="txt_qnty_1" id="txt_qnty_1" class="text_boxes_numeric" style="width:50px" readonly /></td>
                    <td><input type="text" name="txtqntypcs_1" id="txtqntypcs_1" class="text_boxes_numeric" style="width:50px" readonly /></td>
					
                    <td><input type="text" name="txt_rate_1" id="txt_rate_1" class="text_boxes_numeric" style="width:30px" readonly onBlur="amount_caculation(1);" /></td>
                    <td><input type="text" name="txt_amount_1" id="txt_amount_1" class="text_boxes_numeric" style="width:50px"  readonly />
                    </td>
                    <td> <? echo create_drop_down( "curanci_1", 60, $currency,"", 1, "-Select Currency-",1,"",1,"" ); ?> </td>
                    <td>
                        <input type="button" name="remarks_1" id="remarks_1" class="formbuttonplasminus" style="width:20px" value="R" onClick="openmypage_remarks(1);" />
                        <input type="hidden" name="txtRemarks_1" id="txtRemarks_1" class="text_boxes" />
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                	<td width="70px">&nbsp;</td>
                    <td width="70px">&nbsp;</td>
                    <td width="60px">&nbsp;</td>
                    <td width="80px">&nbsp;</td>
                    <td width="60px">&nbsp;</td>
                    <td width="40px">&nbsp;</td>
                    <td width="110px">&nbsp;</td>
                    <td width="70px">&nbsp;</td>
                    <td width="60px" align="right">Total:</td>
                    <td width="110px" colspan="2" align="right"><input type="text" name="txt_tot_qnty" id="txt_tot_qnty"  class="text_boxes_numeric" style="width:60px" disabled /></td>
                    <td width="60px"><input type="text" name="total_qntyPcs" id="total_qntyPcs"  class="text_boxes_numeric" style="width:50px" disabled /></td>
                    <td width="40px">&nbsp;</td>
                    <td width="60px"><input type="text" name="txt_tot_amount" id="txt_tot_amount"  class="text_boxes_numeric" style="width:50px" disabled/></td>
                    <td width="60px">&nbsp;</td>
                    <td>&nbsp;</td>
                </tr> 
				<tr>
                    <td colspan="13" height="15" align="center"><div id="bill_on" style="float:left; font-size:18px; color:#FF0000;"></div><div id="accounting_integration_div" style="float:center; font-size:18px; color:#FF0000;"></div></td>
                    <td>
                     <input type="text" title="Upcharge" name="txt_upcharge" id="txt_upcharge" onBlur="fnc_net_calculation()"  class="text_boxes_numeric" style="width:50px" placeholder="Upcharge"/> 
                      <input type="text" title="Discount" name="txt_discount" id="txt_discount"  class="text_boxes_numeric"  onBlur="fnc_net_calculation()" style="width:50px" placeholder="Discount"/> 
                     <input type="text" title="Net Total" name="txt_net_total" id="txt_net_total"  class="text_boxes_numeric" style="width:50px" placeholder="Net Total"/> 
                     </td>
                </tr>               
                <tr>
                    <td colspan="15" height="15" align="center"> </td>
                </tr>
                <tr>
                    <td colspan="15" height="15" align="center"><div id="accounting_integration_div" style="float:center; font-size:18px; color:#FF0000;"></div></td>
                </tr>
                <tr>
                    <td colspan="15" align="center" class="button_container">
						<? echo load_submit_buttons($permission,"fnc_knitting_bill_entry",0,0,"reset_form('outknittingbill_1','knitting_info_list','', '', 'disable_enable_fields(\'cbo_company_id*cbo_location_name*cbo_supplier_company*cbo_bill_for\',0)'); $('#bill_issue_table tr:not(:first)').remove();",1); ?> 
                        <input type="button" name="printb1" id="printb1" value="Print B1" onClick="fnc_print_report(0);" style="width:80px" class="formbutton" />
						<input type="button" name="printb2" id="printb2" value="FSO print" onClick="fnc_print_report(1);" style="width:80px" class="formbutton" />
                    </td>
                </tr>  
                <tr>
                    <td colspan="15" id="list_view" align="center"></td>
                </tr>
            </tfoot>                                                             
        </table>
        </fieldset>
        </form>
        <div id="knitting_info_list"></div>
   </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>