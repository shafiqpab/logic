<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Out Side Knitting Bill Entry For Gross		
Functionality	:	
JS Functions	:
Created by		:	Aziz 
Creation date 	: 	20-09-2020
Updated by 		: 	
Update date		:   
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
echo load_html_head_contents("Kniting Bill Gross Entry", "../", 1,1, $unicode,1,'');
?>
<script>
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

	function check_all_data_view()
	{
		if(document.getElementById('checkall').checked==true)
		{
			document.getElementById('checkall').value=1;
		}
		else if(document.getElementById('checkall').checked==false)
		{
			document.getElementById('checkall').value=2;
		}
		
		var list_tot_row=$('#tbl_list_search tr').length-1;
		
		for( var i = 1; i <= list_tot_row; i++ )
		{
			
			var tr_id = $("#unique_id"+i).val();
			if($("#tr_"+tr_id).css("display") != "none")
			{
				if($('#checkall').val()==1)
				{
					document.getElementById('checkid'+i).checked=true;
					document.getElementById('checkid'+i).value=1;
				}
				else if($('#checkall').val()==2) 
				{
					document.getElementById('checkid'+i).checked=false;
					document.getElementById('checkid'+i).value=2;
				}
				js_set_value( tr_id,i );

			}
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
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/outside_knitting_bill_gross_entry_controller.php?data='+data+'&action=bill_no_popup','Bill Popup', 'width=880px,height=400px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("outkintt_receive_id"); //Access form field with id="emailfield" 
			if (theemail.value!="")
			{
				freeze_window(5);
				get_php_form_data( theemail.value, "load_php_data_to_form_issue", "requires/outside_knitting_bill_gross_entry_controller" );
				window_close( theemail.value );
				//alert(theemail.value);
				/*var tot_row=$('#bill_issue_table tr').length;
				//alert(tot_row)
				var all_value="";
				for (var n=1; n<=tot_row; n++)
				{
					if(all_value=="") all_value+=$('#txtChallenno_'+n).val()+'_'+$('#ordernoid_'+n).val()+'_'+$('#itemid_'+n).val()+'_'+$('#bodyPartId_'+n).val()+'_'+$('#febDescId_'+n).val(); else all_value+='!!!!'+$('#txtChallenno_'+n).val()+'_'+$('#ordernoid_'+n).val()+'_'+$('#itemid_'+n).val()+'_'+$('#bodyPartId_'+n).val()+'_'+$('#febDescId_'+n).val();
				}
				//alert(all_value); return;
				show_list_view(document.getElementById('cbo_company_id').value+'***'+document.getElementById('cbo_location_name').value+'***'+document.getElementById('cbo_bill_for').value+'***'+document.getElementById('cbo_supplier_company').value+'***'+document.getElementById('txt_bill_form_date').value+'***'+document.getElementById('txt_bill_to_date').value+'***'+theemail.value+'***'+all_value,'knitting_entry_list_view','knitting_info_list','requires/outside_knitting_bill_gross_entry_controller','','','');//set_all()*/
				
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
		//alert(uid);
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
						var wo_id='';var wo_no='';//split_str[12];
						var rec_qty=split_str[12];
						var body_part_name=split_str[13];
						var body_part_typeId=split_str[14];
						var uomId=split_str[15];
						var wo_no=split_str[16];
						var rec_qty_pcs=split_str[17];
						var wo_id=split_str[18];
					 	//alert(rec_qty_pcs);
						//var uom=split_str[14];
						//var rate=split_str[15];
						//var amount=split_str[16];
						//var remarks=split_str[17];
						//alert(body_part_typeId);
						var is_disable=""; var selected_uom="";//var is_disabled="";
						/*if (body_part_id==2 || body_part_id==3 || body_part_id==40 || body_part_id==172 || body_part_id==203 || body_part_id==214)
						{
							selected_uom=1; is_disable="";
						}
						else
						{
							selected_uom=12; is_disable="disabled";
						}*/
						selected_uom=uomId;
						if(body_part_typeId==40 || body_part_typeId==50)
						{
							is_disable="";
						}
						else  is_disable="disabled";
						// if(body_part_typeId==40 || body_part_typeId==50)
						// {
						// 	is_disabled="disabled";
						// }
						// else  is_disabled="";
						//alert(body_part_typeId);
						
						html+='<tr align="center" id="trent_'+trid+'"><td><input type="hidden" name="updateiddtls_'+i+'" id="updateiddtls_'+i+'" value=""><input type="hidden" name="reciveid_'+i+'" id="reciveid_'+i+'" value="'+rec_id+'"><input type="text" name="txtReceiveDate_'+i+'" id="txtReceiveDate_'+i+'" class="datepicker" style="width:60px" value="'+rec_date+'" disabled /></td><td><input type="text" name="txtChallenno_'+i+'" id="txtChallenno_'+i+'" class="text_boxes" style="width:60px" value="'+challan_no+'" readonly /></td><td><input type="hidden" name="ordernoid_'+i+'" id="ordernoid_'+i+'" value="'+po_id+'" style="width:50px" readonly /><input type="text" name="txtOrderno_'+i+'" id="txtOrderno_'+i+'"  class="text_boxes" style="width:50px" value="'+po_no+'" readonly /></td><td><input type="text" name="txtStylename_'+i+'" id="txtStylename_'+i+'" class="text_boxes" style="width:70px;" value="'+style_ref+'" readonly /></td><td><input type="text" name="txtPartyname_'+i+'" id="txtPartyname_'+i+'" class="text_boxes" style="width:50px" value="'+buyer+'" readonly /></td><td><input name="txtNumberroll_'+i+'" id="txtNumberroll_'+i+'" type="text" class="text_boxes_numeric" style="width:30px" value="'+roll_no+'" readonly /></td><td><input type="hidden" name="bodyPartId_'+i+'" id="bodyPartId_'+i+'" value="'+body_part_id+'"><input type="hidden" name="bodyPartType_'+i+'" id="bodyPartType_'+i+'" value="'+body_part_typeId+'"><input type="hidden" name="febDescId_'+i+'" id="febDescId_'+i+'" value="'+fab_des_id+'"><input type="hidden" name="itemid_'+i+'" id="itemid_'+i+'" value="'+prod_id+'"><input type="text" name="txtFebricdesc_'+i+'" id="txtFebricdesc_'+i+'" class="text_boxes" style="width:100px" title="'+prod_name+'" value="'+prod_name+'" readonly /></td><td><input type="text" name="textbodypart_'+i+'" id="textbodypart_'+i+'" class="text_boxes" style="width:60px" value="'+body_part_name+'" readonly /></td><td><input type="hidden" name="txtwonumid_'+i+'" id="txtwonumid_'+i+'" value="'+wo_id+'"><input type="text" name="textWoNum_'+i+'" id="textWoNum_'+i+'" class="text_boxes" style="width:100px" value="'+wo_no+'" placeholder="Browse" onDblClick="openmypage_wonum(\''+wo_no+'\','+i+');" readonly /></td><td><select name="cbouom_'+i+'" id="cbouom_'+i+'" class="text_boxes" style="width:60px"><option value="0">-UOM-</option><option value="1">Pcs</option><option value="2">Dzn</option><option value="12">Kg</option></select></td><td><input type="text" name="txtQnty_'+i+'" id="txtQnty_'+i+'" class="text_boxes_numeric" onBlur="qty_caculation('+i+');" style="width:50px" value="'+rec_qty+'" readonly /></td><td><input type="text" name="txtqntypcs_'+i+'" id="txtqntypcs_'+i+'" class="text_boxes_numeric" style="width:50px"  value="'+rec_qty_pcs+'"  onBlur="amount_caculation('+i+');" '+is_disable+' /></td><td><input type="text" name="txtRate_'+i+'" id="txtRate_'+i+'" class="text_boxes_numeric" style="width:30px" value="" onBlur="amount_caculation('+i+'); fnc_rate_copy('+i+');" /><input type="hidden" name="txthiddenrate_'+i+'" id="txthiddenrate_'+i+'" class="text_boxes_numeric" style="width:40px" value="" /></td><td><input type="text" name="txtAmount_'+i+'" id="txtAmount_'+i+'" style="width:50px" class="text_boxes_numeric" value="" readonly /></td><td><? echo create_drop_down( "curanci_'+i+'", 60, $currency,"", 1, "-Select Currency-",1,"",1,"" ); ?></td><td><input type="button" name="remarks_'+i+'" id="remarks_'+i+'" class="formbuttonplasminus" style="width:20px" value="R" onClick="openmypage_remarks('+i+');" /><input type="hidden" name="txtRemarks_'+i+'" id="txtRemarks_'+i+'" class="text_boxes" value="" /></td></tr>';
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
			var list_view_str = return_global_ajax_value( uid, 'load_dtls_data', '', 'requires/outside_knitting_bill_gross_entry_controller');
			var split_list_view=list_view_str.split('###');
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
				var qty_pcs=trim(split_list_str[19])*1;
				var body_part_Type=trim(split_list_str[20]);
				var curr=trim(split_list_str[20]);
				var body_part_Type=trim(split_list_str[20]);


				var currency_id=1;
				//console.log(split_list_str.length);
				if(split_list_str.length>21)
				{

					currency_id=trim(split_list_str[21]);

				}
				if(split_list_str.length>22)
				{

					wo_id=trim(split_list_str[22]);

				}
				if(split_list_str.length>23)
				{

					wo_no=trim(split_list_str[23]);

				}
				
				console.log(split_list_str[23]);
				console.log(wo_no);
				
				<? 
				
				//$dropdown= create_drop_down( "curanci_'+m+'", 60, $currency,"", 1, "-Select Currency-",1,"",1,"" );
				  $js_array = json_encode($currency);
				  echo "var javascript_currency_arr = ". $js_array . ";\n";
				 ?>
				 var dropdown='<input type="hidden" name="curanci_'+m+'" id="curanci_'+m+'" style="width:50px" class="text_boxes_numeric" value="'+currency_id+'" readonly /><input type="text" name="curan_'+m+'" id="curan_'+m+'" style="width:50px" class="text_boxes" value="'+javascript_currency_arr[currency_id]+'" readonly />';




			
				var is_disable="";
				//if (body_part_id==2 || body_part_id==3 || body_part_id==40 || body_part_id==172 || body_part_id==203 || body_part_id==214)
				if(body_part_Type==40 || body_part_Type==50)
				{
					is_disable="";
				}
				else
				{
					is_disable="disabled";
				}
				//listed_id[]=listed_id;
					//alert(qty_pcs+'='+body_part_Type);
				html+='<tr align="center" id="trent_'+trid+'"><td><input type="hidden" name="updateiddtls_'+m+'" id="updateiddtls_'+m+'" value="'+dtls_id+'"><input type="hidden" name="reciveid_'+m+'" id="reciveid_'+m+'" value="'+rec_id+'"><input type="text" name="txtReceiveDate_'+m+'" id="txtReceiveDate_'+m+'" class="datepicker" style="width:60px" value="'+rec_date+'" disabled /></td><td><input type="text" name="txtChallenno_'+m+'" id="txtChallenno_'+m+'" class="text_boxes" style="width:60px" value="'+challan_no+'" readonly /></td><td><input type="hidden" name="ordernoid_'+m+'" id="ordernoid_'+m+'" value="'+po_id+'" style="width:50px" readonly /><input type="text" name="txtOrderno_'+m+'" id="txtOrderno_'+m+'"  class="text_boxes" style="width:50px" value="'+po_no+'" readonly /></td><td><input type="text" name="txtStylename_'+m+'" id="txtStylename_'+m+'"  class="text_boxes" style="width:70px;" value="'+style_ref+'" readonly /></td><td><input type="text" name="txtPartyname_'+m+'" id="txtPartyname_'+m+'" class="text_boxes" style="width:50px" value="'+buyer+'" readonly /></td><td><input name="txtNumberroll_'+m+'" id="txtNumberroll_'+m+'" type="text" class="text_boxes_numeric" style="width:30px" value="'+roll_no+'" readonly /></td><td><input type="hidden" name="bodyPartId_'+m+'" id="bodyPartId_'+m+'" value="'+body_part_id+'"><input type="hidden" name="bodyPartType_'+m+'" id="bodyPartType_'+m+'" value="'+body_part_Type+'"><input type="hidden" name="febDescId_'+m+'" id="febDescId_'+m+'" value="'+fab_des_id+'"><input type="hidden" name="itemid_'+m+'" id="itemid_'+m+'" value="'+prod_id+'"><input type="text" name="txtFebricdesc_'+m+'" id="txtFebricdesc_'+m+'" class="text_boxes" style="width:100px" title="'+prod_name+'" value="'+prod_name+'" readonly /></td><td><input type="text" name="textbodypart_'+m+'" id="textbodypart_'+m+'" class="text_boxes" style="width:60px" value="'+body_part_name+'" readonly /></td><td><input type="hidden" name="txtwonumid_'+m+'" id="txtwonumid_'+m+'" value="'+wo_id+'"><input type="text" name="textWoNum_'+m+'" id="textWoNum_'+m+'" class="text_boxes" style="width:100px" value="'+wo_no+'" placeholder="Browse" onDblClick="openmypage_wonum('+wo_id+','+m+');" /></td><td><select name="cbouom_'+m+'" id="cbouom_'+m+'" class="text_boxes" style="width:60px"><option value="0">-UOM-</option><option value="1">Pcs</option><option value="2">Dzn</option><option value="12">Kg</option></select></td><td><input type="text" name="txtQnty_'+m+'" id="txtQnty_'+m+'" class="text_boxes_numeric" onBlur="qty_caculation('+m+');" style="width:50px" value="'+rec_qty+'" readonly /></td><td><input type="text" name="txtqntypcs_'+m+'" id="txtqntypcs_'+m+'" class="text_boxes_numeric" style="width:50px" onBlur="amount_caculation('+m+');" value="'+qty_pcs+'" '+is_disable+' /></td><td><input type="text" name="txtRate_'+m+'" id="txtRate_'+m+'" class="text_boxes_numeric" style="width:30px" value="'+rate+'" onBlur="amount_caculation('+m+'); fnc_rate_copy('+i+');" /><input type="hidden" name="txthiddenrate_'+i+'" id="txthiddenrate_'+i+'" class="text_boxes_numeric" style="width:40px" value="" /></td><td><input type="text" name="txtAmount_'+m+'" id="txtAmount_'+m+'" style="width:50px" class="text_boxes_numeric" value="'+amount+'" readonly /></td><td>'+dropdown+'</td><td><input type="button" name="remarks_'+m+'" id="remarks_'+m+'" class="formbuttonplasminus" style="width:20px" value="R" onClick="openmypage_remarks('+m+');" /><input type="hidden" name="txtRemarks_'+m+'" id="txtRemarks_'+m+'" class="text_boxes" value="" /></td></tr>';
				
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
			ddd={dec_type:2,comma:0};
		var tot_row=$('#bill_issue_table tr').length;
		math_operation( "txt_tot_qnty", "txtQnty_", "+", tot_row,ddd );
		math_operation( "txt_tot_amount", "txtAmount_", "+", tot_row,ddd );
		set_all_onclick();
	}
	function amount_caculation(id)
	{
		var tot_row=$('#bill_issue_table tr').length;
		var amount = ($("#txtQnty_"+id).val()*1)*($("#txtRate_"+id).val()*1);
			//alert(amount);
		var amount_chk = ($("#txtQnty_"+id).val()*1)*($("#txthiddenrate_"+id).val()*1);
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
		ddd={dec_type:2,comma:0};
		math_operation( "txt_tot_qnty", "txtQnty_", "+", tot_row,ddd );
		math_operation( "txt_tot_amount", "txtamount_", "+", tot_row,ddd );
	}

	function fnc_rate_copy( trid )
	{
		var is_ratecopy=document.getElementById('checkrate').value*1;
		var txtrate=$('#txtRate_'+trid).val()*1;
		//alert(is_ratecopy+'='+txtrate);
		if(is_ratecopy==1)
		{		
			var row_nums=$('#bill_issue_table tr').length;
			for(var j=trid; j<=row_nums; j++)
			{
				
				document.getElementById('txtRate_'+j).value=txtrate;
				amount_caculation(j);
			}
		}
	}

	function qty_caculation()
	{
		var tot_row=$('#bill_issue_table tr').length;
		math_operation( "txt_tot_qnty", "txtQnty_", "+", tot_row );
		math_operation( "txt_tot_amount", "txtAmount_", "+", tot_row );
	}
	
	function openmypage_wonum(woNo='', rowNum)
	{
		if (woNo=='') {
			//alert('WO Num cannot be browsed as this is not from service booking page');
			//return;
		}
		var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_supplier_company').value+"_"+woNo+"_"+document.getElementById('txtChallenno_'+rowNum).value+"_"+document.getElementById('reciveid_'+rowNum).value+"_"+document.getElementById('cbo_bill_for').value ;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/outside_knitting_bill_gross_entry_controller.php?action=wo_num_popup&data='+data,'Wo Number Popup', 'width=950px,height=400px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("hidd_item_id") 
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				//alert (response[0]);
				/*var tot_row=$('#bill_issue_table tr').length;
				for(var k=1; k<=tot_row; k++)
				{
					document.getElementById('txtwonumid_'+k).value=response[0];
					document.getElementById('textWoNum_'+k).value=response[1];
					document.getElementById('txtRate_'+k).value=response[2];
				}
				exchenge_rate_val(response[2])*/

				// updating only this rows' value
				document.getElementById('txtwonumid_'+rowNum).value=response[0];
				document.getElementById('textWoNum_'+rowNum).value=response[1];
				document.getElementById('txtRate_'+rowNum).value=response[2];
				document.getElementById('curanci_'+rowNum).value=response[3];
				amount_caculation(rowNum);

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
		
		if(operation==2)
		{
			alert("Delete not allowed");
			return;
		}
		if( form_validation('cbo_company_id*cbo_location_name*txt_bill_date*cbo_supplier_company*cbo_bill_for','Company Name*Location*Bill Date*supplier company*bill for')==false)
		{ 
			return;
		}
		else
		{
			var tot_row=$('#bill_issue_table tr').length;
									
			var data1="action=save_update_delete&operation="+operation+"&tot_row="+tot_row+get_submitted_data_string('txt_bill_no*cbo_company_id*cbo_location_name*txt_bill_date*cbo_supplier_company*cbo_bill_for*txt_party_bill_no*update_id',"../");
			
			var data2='';
			for(var i=1; i<=tot_row; i++)
			{
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
			http.open("POST","requires/outside_knitting_bill_gross_entry_controller.php",true);
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
			show_msg(response[0]);
			if(response[0]==0 || response[0]==1 )
			{
			document.getElementById('update_id').value = response[1];
			document.getElementById('txt_bill_no').value = response[2];
			window_close(response[1]);
			set_button_status(1, permission, 'fnc_knitting_bill_entry',1);
			}
			
			release_freezing();
		}
	}
	
	function amount_caculation(id)
	{
		var body_part=$('#bodyPartId_'+id).val();
		var bodyPartType=$('#bodyPartType_'+id).val()*1;
		//alert(bodyPartType)
		/*if(body_part==2 || body_part==3 || body_part==40 || body_part==172 || body_part==203 || body_part==214)
		{
			var delv_qty=$('#txtqntypcs_'+id).val();
		}
		else
		{
			var delv_qty=$('#txt_qnty_'+id).val();
		}*/
		if(bodyPartType==40 || bodyPartType==50)
		{
			var delv_qty=$('#txtqntypcs_'+id).val()*1;
		}
		else
		{
			var delv_qty=$('#txtQnty_'+id).val()*1;
		}
		
		var tot_amount='';
		tot_amount=(delv_qty*1)*(document.getElementById('txtRate_'+id).value*1);
	 	//alert(tot_amount+'='+delv_qty+'='+id);
		ddd={dec_type:2,comma:0};
		document.getElementById('txtAmount_'+id).value=number_format_common(tot_amount,2,0,'');
		// math_operation( "txt_tot_amount", "txtAmount_", "+", id,ddd );
		
		//var body_part=$('#bodyPartId_'+id).val();
		//alert(body_part)
		//if(body_part==2 || body_part==3 || body_part==40 || body_part==172 || body_part==203 || body_part==214)
		/*if(bodyPartType==40 || bodyPartType==50)
		{
			var delv_qty=$('#txtqntypcs_'+id).val()*1;
		}
		else
		{
			var delv_qty=$('#txtQnty_'+id).val()*1;
		}*/
		//$("#txtAmount_"+id).val((delv_qty*1)*($("#txtRate_"+id).val()*1));
		var tot_row=$('#bill_issue_table tr').length;
	//alert(delv_qty);
		//math_operation( "txt_tot_qnty", "txtRate_", "+", tot_row );
		math_operation( "total_qntyPcs", "txtqntypcs_", "+", tot_row,ddd );
		math_operation( "txt_tot_amount", "txtAmount_", "+", tot_row,ddd );
		
	 
	}
	
	// function fnc_check(inc_id)
	// {
	// 	if(document.getElementById('checkid'+inc_id).checked==true)
	// 	{
	// 		document.getElementById('checkid'+inc_id).value=1;
	// 	}
	// 	else if(document.getElementById('checkid'+inc_id).checked==false)
	// 	{
	// 		document.getElementById('checkid'+inc_id).value=2;
	// 	}
	// }

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
		if( form_validation('cbo_company_id*cbo_location_name*cbo_bill_for*cbo_supplier_company*txt_bill_form_date*txt_bill_to_date','Company Name*Location*Bill For*Supplier Name*Form Date*Form Date')==false)
		{
			return;
		}
		
		var tot_row=$('#bill_issue_table tr').length;
		
		$('#cbo_company_id').attr('disabled','disabled');
		$('#cbo_location_name').attr('disabled','disabled');
		$('#cbo_bill_for').attr('disabled','disabled');
		$('#cbo_supplier_company').attr('disabled','disabled');
		//alert(tot_row)
		
		show_list_view(document.getElementById('cbo_company_id').value+'***'+document.getElementById('cbo_location_name').value+'***'+document.getElementById('cbo_bill_for').value+'***'+document.getElementById('cbo_supplier_company').value+'***'+document.getElementById('txt_bill_form_date').value+'***'+document.getElementById('txt_bill_to_date').value+'***'+document.getElementById('update_id').value+'***'+document.getElementById('txt_job_id').value+'***'+document.getElementById('txt_booking_no').value,'knitting_entry_list_view','knitting_info_list','requires/outside_knitting_bill_gross_entry_controller','setFilterGrid("tbl_list_search",-1);','','');
	}
	
	function openmypage_remarks(id)
	{
		var data=document.getElementById('txt_remarks_'+id).value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/outside_knitting_bill_gross_entry_controller.php?data='+data+'&action=remarks_popup','Remarks Popup', 'width=420px,height=320px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("text_new_remarks");//Access form field with id="emailfield"
			if (theemail.value!="")
			{
				$('#txt_remarks_'+id).val(theemail.value);
			}
		}
	}

	function enable_disable(data)
	{
		if(data !=3)
		{
			$('#txt_job_no').removeAttr('disabled','disabled');
		}
		else
		{
			$('#txt_job_no').attr('disabled','disabled');
		}
	}

	function openmypage_job()
	{ 
		if(form_validation('cbo_company_id*cbo_location_name*cbo_bill_for*cbo_supplier_company','Company Name*Location*Bill For*Supplier Name')==false){ return; }

		var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_bill_for').value+"_"+document.getElementById('cbo_supplier_company').value;

		page_link='requires/outside_knitting_bill_gross_entry_controller.php?action=job_popup&data='+data;
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
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_bill_no').val()+'*'+report_title+'*'+show_val_column,'outbound_knitting_bill_print','requires/outside_knitting_bill_gross_entry_controller');
			//return;
			show_msg("3");
		}
		if(report_type==1) //Fso
		{
			var show_val_column='';
			var r=confirm("Press \"OK\" to open with Order Comments\nPress \"Cancel\" to open without Order Comments");
			if (r==true) show_val_column="1"; else show_val_column="0";
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_bill_no').val()+'*'+report_title+'*'+show_val_column,'outbound_knitting_bill_fso_print','requires/outside_knitting_bill_gross_entry_controller');
			//return;
			show_msg("3");
		}
	}
	
	function generate_report_file(data,action,page)
	{
		window.open("requires/outside_knitting_bill_gross_entry_controller.php?data=" + data+'&action='+action, true );
	}
	function openmypage_booking()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var location_name = $("#cbo_location_name").val();
		var cbo_booking_year = $("#cbo_job_year").val();
		var txt_booking_no = $("#txt_booking_no").val(); 
		var page_link='requires/outside_knitting_bill_gross_entry_controller.php?action=booking_no_popup&companyID='+companyID+'&location_name='+location_name+'&cbo_booking_year='+cbo_booking_year+'&txt_booking_no='+txt_booking_no;
		var title='Booking No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var booking_num=this.contentDoc.getElementById("txt_selected").value;
			var booking_ids=this.contentDoc.getElementById("txt_selected_id").value;
			var sys_ids=this.contentDoc.getElementById("txt_sys_id").value;
			Array.prototype.removeDuplicates = function () {
                return this.filter(function (item, index, self) {
                    return self.indexOf(item) == index;
                });
            };
			var booking_nums =this.contentDoc.getElementById("txt_selected").value.split(",").removeDuplicates();
			$('#txt_booking_no').val(booking_nums);
			$('#txt_booking_id').val(booking_ids);
			$('#txt_sys_id').val(sys_ids);	  
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
                    <input type="hidden" name="update_id" id="update_id" />
                    <input type="text" name="txt_bill_no" id="txt_bill_no" class="text_boxes" style="width:140px" placeholder="Double Click to Search" onDblClick="openmypage_bill();" readonly tabindex="1" >
                </td>
             </tr>
             <tr>
                <td width="110" class="must_entry_caption">Company</td>
                <td width="150"><?php echo create_drop_down( "cbo_company_id",145,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/outside_knitting_bill_gross_entry_controller', this.value, 'load_drop_down_location', 'location_td');load_drop_down( 'requires/outside_knitting_bill_gross_entry_controller', this.value, 'load_drop_down_supplier_name', 'supplier_td');","","","","","",2);	?></td>
                <td width="110" class="must_entry_caption">Location</td>                                              
                <td width="150" id="location_td"><? echo create_drop_down( "cbo_location_name", 145, $blank_array,"", 1, "--Select Location--", $selected,"","","","","","",3); ?></td>
                <td width="110" class="must_entry_caption">Bill Date</td>                                              
                <td><input class="datepicker" type="text" style="width:130px" name="txt_bill_date" id="txt_bill_date" tabindex="4" value="<? echo date('d-m-Y'); ?>" /></td>
            </tr> 
            <tr>
            	<td class="must_entry_caption">Bill For</td>
                <td><? echo create_drop_down( "cbo_bill_for", 145, $bill_for,"", 1, "-- Select --", $selected, "enable_disable(this.value);",0,"","","","",7); ?></td>
                <td class="must_entry_caption">Supplier Name</td>
                <td id="supplier_td"><? echo create_drop_down( "cbo_supplier_company", 145, $blank_array,"", 1, "-- Select supplier --", $selected, "",0,"","","","",5); ?></td>
                <td>Party Bill No</td>
                <td><input type="text" name="txt_party_bill_no" id="txt_party_bill_no" class="text_boxes" style="width:130px" /></td>
            </tr>
            <tr>
                <td class="must_entry_caption">Form Date</td>                                              
                <td><input class="datepicker" type="text" style="width:130px" name="txt_bill_form_date" id="txt_bill_form_date" /></td>
                <td class="must_entry_caption">To Date</td>                                              
                <td><input class="datepicker" type="text" style="width:130px" name="txt_bill_to_date" id="txt_bill_to_date" /></td>
                <td>Job No.</td>
                <td>
                	<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:140px" placeholder="Double Click to Search" onDblClick="openmypage_job();" readonly disabled>
                	<input type="hidden" name="txt_job_id" value="" class="txt_job_id" id="txt_job_id">
                </td>                                           
            </tr>
            <tr>
				<td>Booking No</td>
				<td>
                    <input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes_numeric" style="width:130px" onDblClick="openmypage_booking();" placeholder="Write/Browse" />
                    <input type="hidden" id="txt_booking_id" name="txt_booking_id" class="text_boxes"/>
					<input type="hidden" id="txt_sys_id" name="txt_sys_id" class="text_boxes"/>
                </td>
            	<td colspan="3">&nbsp;</td>
            	<td><input class="formbutton" type="button" onClick="fnc_populate_list_view();" style="width:130px" name="btn" value="Populate" id="btn" /></td>
            </tr>
        </table>
    </fieldset>
    <br>
    <fieldset style="width:1080px;">
    <legend>Knitting Bill Info </legend>
        <table style="border:none; width:1080px;" cellpadding="0" cellspacing="1" border="0" id="tb_bill_ent">
            <thead class="form_table_header">
                <th width="70" class="must_entry_caption">Receive Date </th>
                <th width="70" class="must_entry_caption">Sys. Challan</th>
                <th width="60">Order No.</th>
                <th width="80">Style</th>
                <th width="60">Buyer</th>
                <th width="40">N.O Roll</th>
                <th width="110">Fabric Descp</th>
                <th width="70">Body Part</th>
                <th width="160">WO Num</th>
                <th width="50">UOM</th>
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
                        <input type="hidden" name="bodyPartType_1" id="bodyPartType_1" value="">
                    </td>
                    <td><input type="text" name="text_wo_num_1" id="text_wo_num_1" class="text_boxes" style="width:100px" placeholder="Browse" onDblClick="openmypage_wonum();" readonly/></td>
					<td><? echo create_drop_down( "cbouom_1", 60, $unit_of_measurement,"", 1, "-UOM-",$selected,"",0,'1,2,12',"" ); ?></td>                    
                    <td><input type="text" name="txt_qnty_1" id="txt_qnty_1" class="text_boxes_numeric" style="width:50px" readonly /></td>
                    <td><input type="text" name="txtqntypcs_1" id="txtqntypcs_1" class="text_boxes_numeric" style="width:50px" readonly /></td>
                    <td><input type="text" name="txt_rate_1" id="txt_rate_1" class="text_boxes_numeric" style="width:30px" readonly onBlur="amount_caculation(1);" /></td>
                    <td><input type="text" name="txt_amount_1" id="txt_amount_1" class="text_boxes_numeric" style="width:50px"  readonly />
                    </td>
                    <td> <? echo create_drop_down( "curanci_1", 60, $currency,"", 1, "-Select Currency-",1,"",1,"" ); ?> </td>
                    <td>
                        <input type="button" name="remarks_1" id="remarks_1" class="formbuttonplasminus" style="width:20px" value="R" onClick="openmypage_remarks(1);" />
                        <input type="hidden" name="txt_remarks_1" id="txt_remarks_1" class="text_boxes" />
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
                    <td colspan="15" height="15" align="center"> </td>
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