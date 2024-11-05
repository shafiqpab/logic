<?
/*-------------------------------------------- Comments -----------------------
Purpose			         : 	This Form Will Create Bom Of Fabric [Knit].
Functionality	         :	
JS Functions	         :
Created by		         :	Kausar 
Creation date 	         : 	28-12-2021
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
-------------------------------------------------------------------------------*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$user_level=$_SESSION['logic_erp']["user_level"];

//echo $user_level;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Bom Of Fabric [Knit]","../../", 1, 1, $unicode,1,'');
?>
	
<script type="text/javascript">
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	var str_construction=[ <? echo substr(return_library_autocomplete("select construction from wo_pri_quo_fabric_cost_dtls group by construction ", "construction"), 0, -1); ?>];
	var str_composition =[ <? echo substr(return_library_autocomplete("select composition from wo_pri_quo_fabric_cost_dtls group by composition", "composition"), 0, -1); ?>];
	
	function fn_report_generated(action)
	{
		if(form_validation('cbo_company_name*txt_job_no','Company Name*Job No')==false)
		{
			return;
		}
		else
		{	
			
			var report_title=$( "div.form_caption" ).html();
			var yarn_controller=1;
			show_list_view($("#txt_job_no").val()+'_'+$("#txt_quotation_id").val()+'_0_'+$("#cbo_company_name").val()+'_'+$("#cbo_buyer_name").val()+'_'+$("#txt_cost_control_source").val()+'_'+yarn_controller+'_bomofyarn',action,'report_container','requires/pre_cost_entry_controller_v2','');
			
			//show_list_view(update_id+'_'+document.getElementById('txt_quotation_id').value+'_'+document.getElementById('copy_quatation_id').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_cost_control_source').value+'_'+yarn_controller,action,'cost_container','requires/pre_cost_entry_controller_v2','');
		}
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			release_freezing();
		}
	}
	
	function set_auto_complete(type)
	{
		if(type=='tbl_fabric_cost')
		{
			var row_num=$('#tbl_fabric_cost tr').length-1;
			for (var i=1; i<=row_num; i++)
			{
				$("#txtconstruction_"+i).autocomplete({
					source: str_construction
				});
				$("#txtcomposition_"+i).autocomplete({
					source:  str_composition 
				}); 
			}
		}
	}
	
	function openmypage(title)
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var data = $("#cbo_company_name").val();
		var page_link='requires/bom_of_fabric_controller.php?action=order_popup';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/bom_of_fabric_controller.php?data='+data+'&action=job_popup', title, 'width=1160px,height=450px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var themail=this.contentDoc.getElementById("selected_job").value;
			if (themail!="")
			{
				freeze_window(5);
				var exdata=themail.split('_');
				
				if(exdata[6]==3) exdata[6]=1;
				$("#txt_job_no").val( exdata[0] );
				$("#update_id").val( exdata[0] );
				$("#cbo_buyer_name").val( exdata[1] );
				$("#txt_quotation_id").val( exdata[2] );
				
				$("#cbo_currercy").val( exdata[3] );
				$("#txt_exchange_rate").val( exdata[4] );
				$("#cbo_costing_per").val( exdata[5] );
				$("#cbo_approved_status").val( exdata[6] );
				$("#hidd_job_id").val( exdata[7] );
				$("#txt_cost_control_source").val( exdata[8] );
				$("#budget_exceeds_quot_id").val( exdata[9] );
				$("#copy_quatation_id").val( exdata[10] );
				
				$('#cbo_company_name').attr('disabled',true);
				$('#report_container').html('');
				
				release_freezing();
			}
		}
	}
	function call_print_button_for_mail(mail_id){

		
		get_php_form_data( $("#txt_job_no").val()+'**'+$("#txt_quotation_id").val()+'**'+$("#txt_quotation_id").val()+'**'+$("#cbo_company_name").val()+'**'+mail_id+'**'+$("#txt_comments").val(), "yarn_cost_auto_mail_send", "requires/bom_of_fabric_controller" );
	}
	function show_hide_content(row, id){
		$('#content_'+row).toggle('slow', function() {
		});
	}
	
	function sum_yarn_required_avg(value)
	{
		return;
	}
	
	function make_avg_for_same_row()
	{
		return;
	}
	
	function sum_yarn_required_avg_old(value)
	{
		return;
	}
	
	function open_body_part_popup(i)
	{
		var cbofabricnature=document.getElementById('cbofabricnature_'+i).value;
		var libyarncountdeterminationid =document.getElementById('libyarncountdeterminationid_'+i).value
		var page_link='requires/pre_cost_entry_controller_v2.php?action=body_part_popup&fabric_nature='+cbofabricnature+'&libyarncountdeterminationid='+libyarncountdeterminationid;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Fabric Description', 'width=450px,height=450px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var id=this.contentDoc.getElementById("gid");
			var name=this.contentDoc.getElementById("gname");
			var type=this.contentDoc.getElementById("gtype");
			document.getElementById('txtbodyparttext_'+i).value=name.value;
			document.getElementById('txtbodypart_'+i).value=id.value;
			document.getElementById('txtbodyparttype_'+i).value=type.value;
		}
	}
	function change_caption( value, td_id )
	{
		if(value==2) document.getElementById(td_id).innerHTML="GSM";
		else document.getElementById(td_id).innerHTML="Yarn Weight";
	}
	
	function open_fabric_decription_popup(i)
	{
		var cbofabricnature=document.getElementById('cbofabricnature_'+i).value;
		var libyarncountdeterminationid =document.getElementById('libyarncountdeterminationid_'+i).value
		var page_link='requires/pre_cost_entry_controller_v2.php?action=fabric_description_popup&fabric_nature='+cbofabricnature+'&libyarncountdeterminationid='+libyarncountdeterminationid;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Fabric Description', 'width=960px,height=450px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var fab_des_id=this.contentDoc.getElementById("fab_des_id");
			var fab_nature_id=this.contentDoc.getElementById("fab_nature_id");
			var fab_desctiption=this.contentDoc.getElementById("fab_desctiption");
			var fab_gsm=this.contentDoc.getElementById("fab_gsm");
			var yarn_desctiption=this.contentDoc.getElementById("yarn_desctiption");
			var construction=this.contentDoc.getElementById("construction");
			var composition=this.contentDoc.getElementById("composition");
			document.getElementById('libyarncountdeterminationid_'+i).value=fab_des_id.value;
			document.getElementById('fabricdescription_'+i).value=fab_desctiption.value;
			document.getElementById('fabricdescription_'+i).title=fab_desctiption.value;
			document.getElementById('cbofabricnature_'+i).value=fab_nature_id.value;
			document.getElementById('txtgsmweight_'+i).value=fab_gsm.value;
			document.getElementById('yarnbreackdown_'+i).value=yarn_desctiption.value;
			document.getElementById('construction_'+i).value=construction.value;
			document.getElementById('composition_'+i).value=composition.value;
		}
	}
	
	function enable_disable(value,fld_arry,i)
	{
		var fld_arry=fld_arry.split('*');
		if(value==2)
		{
			var rate_amount = return_ajax_request_value(document.getElementById('updateid_'+i).value, 'rate_amount', 'requires/pre_cost_entry_controller_v2')
			rate_amount_arr=rate_amount.split('_');
			for(var j=0;j<fld_arry.length;j++)
			{
				document.getElementById(fld_arry[j]+i).disabled=false;
				if(rate_amount_arr[j] != undefined){
					document.getElementById(fld_arry[j]+i).value=rate_amount_arr[j];
				}
			}
		}
		else
		{
			for(var j=0;j<fld_arry.length;j++)
			{
				document.getElementById(fld_arry[j]+i).disabled=true;
				document.getElementById(fld_arry[j]+i).value='';
			}
		}
	}
	
	function control_color_field(i)
	{
		var cbocolorsizesensitive = document.getElementById('cbocolorsizesensitive_'+i).value;
		if(cbocolorsizesensitive==0)
		{
			$('#txtcolor_'+i).removeAttr('disabled');
			$('#txtcolor_'+i).removeAttr('onClick');
		}
		if(cbocolorsizesensitive==1) $('#txtcolor_'+i).attr('disabled','true')
		else if(cbocolorsizesensitive==2) $('#txtcolor_'+i).attr('disabled','true')
		else if(cbocolorsizesensitive==3) $('#txtcolor_'+i).removeAttr('disabled').attr("onClick","open_color_popup("+i+");");
		else if(cbocolorsizesensitive==4) $('#txtcolor_'+i).attr('disabled','true')
	}
	
	function set_sum_value(des_fil_id,field_id,table_id)
	{
		if(table_id=='tbl_fabric_cost')
		{
			var rowCount = $('#tbl_fabric_cost tr').length-1;
			var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd);
			var txt_fabric_pre_cost=document.getElementById('txtamount_sum').value*1+(document.getElementById('txtamountyarn_sum').value)*1+(document.getElementById('txtconamount_sum').value)*1;
			document.getElementById('txt_fabric_pre_cost').value=number_format_common(txt_fabric_pre_cost, 1, 0,document.getElementById('cbo_currercy').value)
			//sum_yarn_required()
			//calculate_main_total();
		}
		if(table_id=='tbl_yarn_cost')
		{
			var rowCount = $('#tbl_yarn_cost tr').length-1;
			var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd );
			var txt_fabric_pre_cost=(document.getElementById('txtamount_sum').value)*1+(document.getElementById('txtamountyarn_sum').value)*1+(document.getElementById('txtconamount_sum').value)*1;
			document.getElementById('txt_fabric_pre_cost').value=number_format_common(txt_fabric_pre_cost, 1, 0,document.getElementById('cbo_currercy').value)

			//calculate_main_total();
		}
		if(table_id=='tbl_conversion_cost')
		{
			var rowCount = $('#tbl_conversion_cost tr').length-1;
			var ddd={ dec_type:1, comma:0, currency:document.getElementById('cbo_currercy').value}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd );
			//document.getElementById('txt_fabric_pre_cost').value=(document.getElementById('txtamount_sum').value)*1+(document.getElementById('txtamountyarn_sum').value)*1+(document.getElementById('txtconamount_sum').value)*1;
			var txt_fabric_pre_cost=(document.getElementById('txtamount_sum').value)*1+(document.getElementById('txtamountyarn_sum').value)*1+(document.getElementById('txtconamount_sum').value)*1;
			document.getElementById('txt_fabric_pre_cost').value=number_format_common(txt_fabric_pre_cost, 1, 0,document.getElementById('cbo_currercy').value)
			//calculate_main_total();
		}
	}
	
	function set_session_large_post_data(page_link,title,body_part_id,cbofabricnature_id,txtgsmweight_id,trorder,updateid_fc)
	{
		var operation=0;
		var updateid =document.getElementById(updateid_fc).value;
		
		if(updateid) operation=1; else operation=0;
		if(updateid) open_consumption_popup(page_link,title,body_part_id,cbofabricnature_id,txtgsmweight_id,trorder,updateid_fc)
		else fnc_fabric_cost_dtls_per_row(operation,trorder)
	}
	
	function open_consumption_popup(page_link,title,body_part_id,cbofabricnature_id,txtgsmweight_id,trorder,updateid_fc)
	{
		var cbo_company_id=document.getElementById('cbo_company_name').value;
		var cbo_costing_per=document.getElementById('cbo_costing_per').value;
		var cbo_approved_status=document.getElementById('cbo_approved_status').value;
		var hid_fab_cons_in_quotation_variable =document.getElementById('consumptionbasis_'+trorder).value;
		var body_part_id =document.getElementById(body_part_id).value;
		var txtgsmweight=document.getElementById(txtgsmweight_id).value;
		var cbofabricnature_id =document.getElementById(cbofabricnature_id).value;
		var cons_breck_downn=document.getElementById('consbreckdown_'+trorder).value;
		var msmnt_breack_downn=document.getElementById('msmntbreackdown_'+trorder).value;
		var marker_breack_down=document.getElementById('markerbreackdown_'+trorder).value;
		var calculated_conss=document.getElementById('txtconsumption_'+trorder).value;
		var txt_job_no = document.getElementById('txt_job_no').value;
		var cbogmtsitem = document.getElementById('cbogmtsitem_'+trorder).value;
		var garments_nature = document.getElementById('garments_nature').value;
		var pri_fab_cost_dtls_id=document.getElementById('prifabcostdtlsid_'+trorder).value;
		var pre_cost_fabric_cost_dtls_id=document.getElementById('updateid_'+trorder).value;
		var precostapproved=document.getElementById('precostapproved_'+trorder).value;
		var cbofabricsource=document.getElementById('cbofabricsource_'+trorder).value;
		var uom=document.getElementById('uom_'+trorder).value;
		var consumptionbasis=document.getElementById('consumptionbasis_'+trorder).value;
		
		var last_app_id= $('#fabricdescription_'+trorder).attr('last_app_id');
		//alert(last_app_id);
		document.getElementById('tr_ortder').value=trorder;
		if(cbogmtsitem==0 )
		{
			alert("Select Gmts Item");
			return;
		}
		if(body_part_id==0 )
		{
			alert("Select Body Part");
			return;
		}
		if(cbofabricnature_id==0 )
		{
			alert("Select Fabric Nature");
			return;
		}
		if(hid_fab_cons_in_quotation_variable=='' || hid_fab_cons_in_quotation_variable<=0 )
		{
			alert("You have to set Variable for this Company");
			return;
		}

		if(cbofabricnature_id==2 && (txtgsmweight==0 || txtgsmweight=='') && consumptionbasis==2)
		{
			alert("Fill up Gsm");
			document.getElementById(txtgsmweight_id).focus();
			return;
		}

		if(cbofabricnature_id==2 && (txtgsmweight==0 || txtgsmweight==''))
		{
			if(body_part_id !=2){
				if(body_part_id !=3){
					alert("Fill up Gsm");
					document.getElementById(txtgsmweight_id).focus();
					return;
				}
			}
		}
		var page_link=page_link+'&body_part_id='+body_part_id+'&cbo_costing_per='+cbo_costing_per+'&cbo_company_id='+cbo_company_id+'&cbofabricnature_id='+cbofabricnature_id+'&calculated_conss='+calculated_conss+'&hid_fab_cons_in_quotation_variable='+hid_fab_cons_in_quotation_variable+'&txtgsmweight='+txtgsmweight+'&txt_job_no='+txt_job_no+'&cbogmtsitem='+cbogmtsitem+'&garments_nature='+garments_nature+'&cbo_approved_status='+cbo_approved_status+'&pri_fab_cost_dtls_id='+pri_fab_cost_dtls_id+'&pre_cost_fabric_cost_dtls_id='+pre_cost_fabric_cost_dtls_id+'&precostapproved='+precostapproved+'&cbofabricsource='+cbofabricsource+'&uom='+uom;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1260px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var trorder= document.getElementById('tr_ortder').value;
			var cons_breck_down=this.contentDoc.getElementById("cons_breck_down");
			var msmnt_breack_down=this.contentDoc.getElementById("msmnt_breack_down");
			var marker_breack_down=this.contentDoc.getElementById("marker_breack_down");
			var calculated_cons=this.contentDoc.getElementById("calculated_cons");
			var finish_avg_cons=this.contentDoc.getElementById("avg_cons");
			var avg_process_loss=this.contentDoc.getElementById("calculated_procloss");
			var process_loss_method_id=this.contentDoc.getElementById("process_loss_method_id");
			//var tot_plancut_qty=this.contentDoc.getElementById("job_plancut_qty");
			var tot_plancut_qty=this.contentDoc.getElementById("tot_plancut_qty");
			var calculated_plancutqty=this.contentDoc.getElementById("calculated_plancutqty");

			var calculated_rate=this.contentDoc.getElementById("calculated_rate");
			var calculated_amount=this.contentDoc.getElementById("calculated_amount");

			$('#txtconsumption_'+trorder).val(calculated_cons.value);
			$('#txtfinishconsumption_'+trorder).val(finish_avg_cons.value);
			$('#txtavgprocessloss_'+trorder).val(avg_process_loss.value);
			$('#processlossmethod_'+trorder).val(process_loss_method_id.value);
			$('#consbreckdown_'+trorder).val(cons_breck_down.value);
			$('#msmntbreackdown_'+trorder).val(msmnt_breack_down.value);
			$('#markerbreackdown_'+trorder).val(marker_breack_down.value);
			$('#isclickedconsinput_'+trorder).val(1);
			$('#plancutqty_'+trorder).val(calculated_plancutqty.value);
			$('#jobplancutqty_'+trorder).val(tot_plancut_qty.value);
			$('#isconspopupupdate_'+trorder).val(1);
			$('#txtrate_'+trorder).val(calculated_rate.value);
			$('#txtamount_'+trorder).val(calculated_amount.value);

			math_operation( 'txtamount_'+trorder, 'txtconsumption_'+trorder+'*'+'txtrate_'+trorder, '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );
			if(last_app_id==2) //Color size change synchronize check
			{
				$('#lastappidchk_'+trorder).val(1);
			}

			set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost');
			sum_yarn_required()
			var row_num=$('#tbl_fabric_cost tr').length-1;
			if(trorder==row_num)
			{
				document.getElementById('check_input').value=0;
				document.getElementById('is_click_cons_box').value=0;
			}
		}
	}
	
	function loadTotal(i,type)
	{
		var txt_job_no=document.getElementById('txt_job_no').value;
		
		var res=return_global_ajax_value(txt_job_no+'__'+type, 'load_total_qtyAmount', '', 'requires/pre_cost_entry_controller_v2');
		
		if(type=='fabric')
		{
			var updateidfab=document.getElementById('updateid_'+i).value;
			var resObj=JSON.parse(res);
			var row_num=$('#tbl_fabric_cost tr').length-1;
			for (var j=1; j<=row_num; j++)
			{
				var updateidfab=document.getElementById('updateid_'+j).value;
				//alert(resObj.qty[updateidtrim]);
				if(updateidfab == ""){
					//alert("Save the row first");
					continue;
				}
				if(resObj.qty[updateidfab] !=undefined){
					document.getElementById('totalqty_'+j).value=resObj.qty[updateidfab];
					document.getElementById('totalamount_'+j).value=resObj.amt[updateidfab];
				}
			}
		}
	}
	
	function add_break_down_tr(i) 
	{
		var row_num=$('#tbl_fabric_cost tr').length-1;
		if (i==0)
		{
			i=1;
			$("#txtconstruction_"+i).autocomplete({
				source: str_construction
			});
			$("#txtcomposition_"+i).autocomplete({
				source:  str_composition 
			}); 
			return;
		}
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;
		 
			$("#tbl_fabric_cost tr:last").clone().find("input,select").each(function() {
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name + i },
				  'value': function(_, value) { return value }              
				});  
			}).end().appendTo("#tbl_fabric_cost");
			$("#tbl_fabric_cost tr:last").removeAttr('id').attr('id','fabriccosttbltr_'+i);
			$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
			$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_fabric_cost');");
			$('#txtbodyparttext_'+i).removeAttr("onDblClick").attr("onDblClick","open_body_part_popup("+i+")");
			$('#txtgsmweight_'+i).removeAttr("onBlur").attr("onBlur","sum_yarn_required()");
			$('#txtcolor_'+i).removeAttr("onClick").attr("onClick","open_color_popup("+i+")");
			$('#cbocolorsizesensitive_'+i).removeAttr("onChange").attr("onChange","control_color_field("+i+")");
			$('#fabricdescription_'+i).removeAttr("onDblClick").attr("onDblClick","open_fabric_decription_popup("+i+")");
			$('#txtconsumption_'+i).removeAttr("onBlur").attr("onBlur","math_operation( 'txtamount_"+i+"', 'txtconsumption_"+i+"*txtrate_"+i+"', '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );sum_yarn_required( );set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost')");
			$('#txtconsumption_'+i).removeAttr("onDblClick").attr("onDblClick","set_session_large_post_data( 'requires/pre_cost_entry_controller_v2.php?action=consumption_popup', 'Consumtion Entry Form', 'txtbodypart_"+i+"', 'cbofabricnature_"+i+"','txtgsmweight_"+i+"','"+i+"','updateid_"+i+"')");
			$('#cbofabricsource_'+i).removeAttr("onChange").attr("onChange","enable_disable( this.value,'txtrate_*txtamount_', "+i+")");
			$('#txtrate_'+i).removeAttr("onBlur").attr("onBlur","math_operation( 'txtamount_"+i+"', 'txtconsumption_"+i+"*txtrate_"+i+"', '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost')");
			$('#txtamount_'+i).removeAttr("onBlur").attr("onBlur","set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost')");
			$('#totalqty_'+i).removeAttr("onClick").attr("onClick","loadTotal( "+i+",'fabric' )");
			var j=i-1;
			$('#cbogmtsitem_'+i).val($('#cbogmtsitem_'+j).val());
			$('#txtbodyparttype_'+i).val($('#txtbodyparttype_'+j).val());
			$('#cbofabricnature_'+i).val($('#cbofabricnature_'+j).val());
			$('#cbocolortype_'+i).val($('#cbocolortype_'+j).val());
			$('#cbofabricsource_'+i).val($('#cbofabricsource_'+j).val());
			$('#cbostatus_'+i).val($('#cbostatus_'+j).val());
			$('#uom_'+i).val($('#uom_'+j).val());
			$('#txtconsumption_'+i).val("");
			$('#txtrate_'+i).val("");
			$('#txtamount_'+i).val("");
			$('#consbreckdown_'+i).val("");
			$('#msmntbreackdown_'+i).val("");
			$('#colorbreackdown_'+i).val("");
			$('#updateid_'+i).val("");
			$('#processlossmethod_'+i).val(""); 
			$('#txtfinishconsumption_'+i).val("");
			$('#txtavgprocessloss_'+i).val("");
			//$('#txtbodypart_'+i).val("");
			$('#txtgsmweight_'+i).val("");
			$('#markerbreackdown_'+i).val("");
			$('#cbowidthdiatype_'+i).val("");
			$('#prifabcostdtlsid_'+i).val("");
			$('#precostapproved_'+i).val(0);
			
			$('#cbogmtsitem_'+i).removeAttr('disabled'); 
			$('#txtbodypart_'+i).removeAttr('disabled');
			$('#txtbodyparttext_'+i).removeAttr('disabled');
			$('#cbofabricnature_'+i).removeAttr('disabled');
			$('#cbocolortype_'+i).removeAttr('disabled');
			$('#fabricdescription_'+i).removeAttr('disabled');
			$('#cbofabricsource_'+i).removeAttr('disabled');
			$('#cbowidthdiatype_'+i).removeAttr('disabled');
			$('#txtgsmweight_'+i).removeAttr('disabled');
			$('#cbocolorsizesensitive_'+i).removeAttr('disabled');
			$('#txtcolor_'+i).removeAttr('disabled');
			$('#uom_'+i).removeAttr('disabled');
			$('#cbonominasupplier_'+i).removeAttr('disabled');
			$('#consumptionbasis_'+i).removeAttr('disabled');
			$('#txtconsumption_'+i).removeAttr('disabled');
			$('#txtrate_'+i).removeAttr('disabled');
			$('#txtamount_'+i).removeAttr('disabled');
			$('#cbostatus_'+i).removeAttr('disabled');
			$('#decrease_'+i).removeAttr('disabled');
			  
			set_all_onclick();
			sum_yarn_required();
			set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost');
			$("#libyarncountdeterminationid_"+i).autocomplete({
				source: str_construction
			});
			$("#fabricdescription_"+i).autocomplete({
				source:  str_composition 
			});
		}
	}
	
	function fn_deletebreak_down_tr(rowNo,table_id)
	{ 
		var r=confirm("Do you want to delete this row?.\n If yes press OK \n or press Cancel." );
		if(r==false)
		{
			return;	
		}
		
		if(table_id=='tbl_fabric_cost'){
			if(rowNo!=1){
				var permission_array=permission.split("_");
				var updateid=$('#updateid_'+rowNo).val();
				var txt_job_no=$('#txt_job_no').val();
				
				if(updateid !="" && permission_array[2]==1){
					
					var is_booking=return_global_ajax_value(updateid+"__1__"+txt_job_no, 'validate_is_booking_create', '', 'requires/pre_cost_entry_controller_v2');
					var ex_booking_data=is_booking.split("***");
					if(trim(ex_booking_data[0])=='approved'){
						alert("This Costing is Approved");
						release_freezing();
						return;
					}
					else if(ex_booking_data[0]==1)
					{
						var booking_msg="Booking Found, Delete Restricted.\n Booking No : "+ex_booking_data[1];
						alert(booking_msg);
						return
					}
					else
					{
						var booking=return_global_ajax_value(updateid, 'delete_row_fabric_cost', '', 'requires/pre_cost_entry_controller_v2');
					}
				}
				var index=rowNo-1
				$("table#tbl_fabric_cost tbody tr:eq("+index+")").remove()
				var numRow = $('table#tbl_fabric_cost tbody tr').length; 
				for(i = rowNo;i <= numRow;i++){
					$("#tbl_fabric_cost tr:eq("+i+")").find("input,select").each(function() {
						$(this).attr({
							'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
							'value': function(_, value) { return value }             
						}); 
						$("#tbl_fabric_cost tr:eq("+i+")").removeAttr('id').attr('id','fabriccosttbltr_'+i);
						$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
						$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_fabric_cost');");
						$('#txtbodyparttext_'+i).removeAttr("onDblClick").attr("onDblClick","open_body_part_popup("+i+")");
						$('#txtgsmweight_'+i).removeAttr("onBlur").attr("onBlur","sum_yarn_required()");
						$('#txtcolor_'+i).removeAttr("onClick").attr("onClick","open_color_popup("+i+")");
						$('#cbocolorsizesensitive_'+i).removeAttr("onChange").attr("onChange","control_color_field("+i+")");
						$('#fabricdescription_'+i).removeAttr("onDblClick").attr("onDblClick","open_fabric_decription_popup("+i+")");
						$('#txtconsumption_'+i).removeAttr("onBlur").attr("onBlur","math_operation( 'txtamount_"+i+"', 'txtconsumption_"+i+"*txtrate_"+i+"', '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );sum_yarn_required( );set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost')");
						$('#txtconsumption_'+i).removeAttr("onDblClick").attr("onDblClick","set_session_large_post_data( 'requires/pre_cost_entry_controller_v2.php?action=consumption_popup', 'Consumtion Entry Form', 'txtbodypart_"+i+"', 'cbofabricnature_"+i+"','txtgsmweight_"+i+"','"+i+"','updateid_"+i+"')");
						$('#cbofabricsource_'+i).removeAttr("onChange").attr("onChange","enable_disable( this.value,'txtrate_*txtamount_', "+i+")");
						$('#txtrate_'+i).removeAttr("onBlur").attr("onBlur","math_operation( 'txtamount_"+i+"', 'txtconsumption_"+i+"*txtrate_"+i+"', '*','',{dec_type:1,comma:0,currency:document.getElementById('cbo_currercy').value} );set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost')");
						$('#txtamount_'+i).removeAttr("onBlur").attr("onBlur","set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost')");
					})
				}
			}
			set_sum_value( 'txtamount_sum', 'txtamount_','tbl_fabric_cost');
			sum_yarn_required();
		}
	}
	
	function sum_yarn_required()
	{
		return;
		var row_num=$('#tbl_fabric_cost tr').length-1;
		
		var yarn= new Array();
		var same= new Array();
		
		var knit_fab= new Array();
		var same_knit= new Array();
		
		var woven_fab= new Array();
		var same_woven= new Array();
		
		var knit_fin_fab= new Array();
		var same_knit_fin= new Array();
		
		var woven_fin_fab= new Array();
		var same_woven_fin= new Array();
		
		
		var knit_fab_prod= new Array();
		var same_knit_prod= new Array();
		
		var woven_fab_prod= new Array();
		var same_woven_prod= new Array();
		
		var knit_fin_fab_prod= new Array();
		var same_knit_fin_prod= new Array();
		
		var woven_fin_fab_prod= new Array();
		var same_woven_fin_prod= new Array();
		
		var knit_fab_purc= new Array();
		var same_knit_purc= new Array();
		
		var woven_fab_purc= new Array();
		var same_woven_purc= new Array();
		
		var knit_fin_fab_purc= new Array();
		var same_knit_fin_purc= new Array();
		
		var woven_fin_fab_purc= new Array();
		var same_woven_fin_purc= new Array();
		
		var knit_fab_purc_amt= new Array();
		var same_knit_purc_amt= new Array();
		
		var woven_fab_purc_amt= new Array();
		var same_woven_purc_amt= new Array();
		
		
		for (var i=1; i<=row_num; i++)
		{
			var cbofabricsource=document.getElementById('cbofabricsource_'+i).value;
			var cbogmtsitem=document.getElementById('cbogmtsitem_'+i).value;
			var txtbodypart=document.getElementById('txtbodypart_'+i).value;
			var cbofabricnature=document.getElementById('cbofabricnature_'+i).value;
			var arrindex=cbogmtsitem+'_'+txtbodypart+'_'+cbofabricnature;
			if(cbofabricnature==2 && cbofabricsource==1)
			{
				if(array_key_exists(arrindex, yarn))
				{
					yarn[arrindex]=yarn[arrindex]+(document.getElementById('txtconsumption_'+i).value)*1;
					same[arrindex]=same[arrindex]+1
				}
				else
				{
					yarn[arrindex]=(document.getElementById('txtconsumption_'+i).value)*1;
					same[arrindex]=1;
				}
			}
			if(cbofabricnature==3 && cbofabricsource==1)
			{
				if(array_key_exists(arrindex, yarn))
				{
					yarn[arrindex]=yarn[arrindex]+(document.getElementById('txtgsmweight_'+i).value)*1;
					same[arrindex]=same[arrindex]+1
				}
				else
				{
					yarn[arrindex]=(document.getElementById('txtgsmweight_'+i).value)*1;
					same[arrindex]=1;
				}
			}
			if(cbofabricnature==100 && cbofabricsource==1)
			{
				if(array_key_exists(arrindex, yarn))
				{
					yarn[arrindex]=yarn[arrindex]+(document.getElementById('txtgsmweight_'+i).value)*1;
					same[arrindex]=same[arrindex]+1
				}
				else
				{
					yarn[arrindex]=(document.getElementById('txtgsmweight_'+i).value)*1;
					same[arrindex]=1;
				}
			}
			
			if(cbofabricnature==2)
			{
				if(array_key_exists(arrindex, knit_fab))
				{
					knit_fab[arrindex]=knit_fab[arrindex]+(document.getElementById('txtconsumption_'+i).value)*1;
					same_knit[arrindex]=same_knit[arrindex]+1;
					knit_fin_fab[arrindex]=knit_fin_fab[arrindex]+(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_knit_fin[arrindex]=same_knit_fin[arrindex]+1
				}
				else
				{
					knit_fab[arrindex]=(document.getElementById('txtconsumption_'+i).value)*1;
					same_knit[arrindex]=1;
					
					knit_fin_fab[arrindex]=(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_knit_fin[arrindex]=1;
				}
			}
			
			if(cbofabricnature==3)
			{
				if(array_key_exists(arrindex, woven_fab))
				{
					woven_fab[arrindex]=woven_fab[arrindex]+(document.getElementById('txtconsumption_'+i).value)*1;
					same_woven[arrindex]=same_woven[arrindex]+1;
					
					woven_fin_fab[arrindex]=woven_fin_fab[arrindex]+(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_woven_fin[arrindex]=same_woven_fin[arrindex]+1
				}
				else
				{
					woven_fab[arrindex]=(document.getElementById('txtconsumption_'+i).value)*1;
					same_woven[arrindex]=1;
					
					woven_fin_fab[arrindex]=(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_woven_fin[arrindex]=1;
				}
			}
			if(cbofabricnature==2 && cbofabricsource==1)
			{
				if(array_key_exists(arrindex, knit_fab_prod))
				{
					knit_fab_prod[arrindex]=knit_fab_prod[arrindex]+(document.getElementById('txtconsumption_'+i).value)*1;
					same_knit_prod[arrindex]=same_knit_prod[arrindex]+1
					
					knit_fin_fab_prod[arrindex]=knit_fin_fab_prod[arrindex]+(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_knit_fin_prod[arrindex]=same_knit_fin_prod[arrindex]+1
				}
				else
				{
					knit_fab_prod[arrindex]=(document.getElementById('txtconsumption_'+i).value)*1;
					same_knit_prod[arrindex]=1;
					
					knit_fin_fab_prod[arrindex]=(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_knit_fin_prod[arrindex]=1;
				}
			}
			
			if(cbofabricnature==3 && cbofabricsource==1)
			{
				if(array_key_exists(arrindex, woven_fab_prod))
				{
					woven_fab_prod[arrindex]=woven_fab_prod[arrindex]+(document.getElementById('txtconsumption_'+i).value)*1;
					same_woven_prod[arrindex]=same_woven_prod[arrindex]+1
					
					woven_fin_fab_prod[arrindex]=woven_fin_fab_prod[arrindex]+(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_woven_fin_prod[arrindex]=same_woven_fin_prod[arrindex]+1
				}
				else
				{
					woven_fab_prod[arrindex]=(document.getElementById('txtconsumption_'+i).value)*1;
					same_woven_prod[arrindex]=1;
					
					woven_fin_fab_prod[arrindex]=(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_woven_fin_prod[arrindex]=1;
				}
			}
			
			if(cbofabricnature==2 && cbofabricsource==2)
			{
				if(array_key_exists(arrindex, knit_fab_purc))
				{
					knit_fab_purc[arrindex]=knit_fab_purc[arrindex]+(document.getElementById('txtconsumption_'+i).value)*1;
					same_knit_purc[arrindex]=same_knit_purc[arrindex]+1
					
					knit_fin_fab_purc[arrindex]=knit_fin_fab_purc[arrindex]+(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_knit_fin_purc[arrindex]=same_knit_fin_purc[arrindex]+1
					
					knit_fab_purc_amt[arrindex]=knit_fab_purc_amt[arrindex]+(document.getElementById('txtamount_'+i).value)*1;
					same_knit_purc_amt[arrindex]=same_knit_purc_amt[arrindex]+1
				}
				else
				{
					knit_fab_purc[arrindex]=(document.getElementById('txtconsumption_'+i).value)*1;
					same_knit_purc[arrindex]=1;
					
					knit_fin_fab_purc[arrindex]=(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_knit_fin_purc[arrindex]=1;
					
					knit_fab_purc_amt[arrindex]=(document.getElementById('txtamount_'+i).value)*1;
					same_knit_purc_amt[arrindex]=1;
				}
			}
			
			if(cbofabricnature==3 && cbofabricsource==2)
			{
				if(array_key_exists(arrindex, woven_fab_purc))
				{
					woven_fab_purc[arrindex]=woven_fab_purc[arrindex]+(document.getElementById('txtconsumption_'+i).value)*1;
					same_woven_purc[arrindex]=same_woven_purc[arrindex]+1
					
					woven_fin_fab_purc[arrindex]=woven_fin_fab_purc[arrindex]+(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_woven_fin_purc[arrindex]=same_woven_fin_purc[arrindex]+1
					
					woven_fab_purc_amt[arrindex]=woven_fab_purc_amt[arrindex]+(document.getElementById('txtamount_'+i).value)*1;
					same_woven_purc_amt[arrindex]=same_woven_purc_amt[arrindex]+1
				}
				else
				{
					woven_fab_purc[arrindex]=(document.getElementById('txtconsumption_'+i).value)*1;
					same_woven_purc[arrindex]=1;
					
					woven_fin_fab_purc[arrindex]=(document.getElementById('txtfinishconsumption_'+i).value)*1;
					same_woven_fin_purc[arrindex]=1;
					
					woven_fab_purc_amt[arrindex]=(document.getElementById('txtamount_'+i).value)*1;
					same_woven_purc_amt[arrindex]=1;
				}
			}
		}
		
		document.getElementById('tot_yarn_needed').value=number_format_common(array_sum (yarn), 5, 0);
		document.getElementById('tot_yarn_needed_span').innerHTML=number_format_common(array_sum (yarn), 5, 0);
		
		document.getElementById('txtwoven_sum').value=number_format_common(array_sum (woven_fab), 5, 0); 
		document.getElementById('txtknit_sum').value=number_format_common(array_sum (knit_fab), 5, 0);
		
		document.getElementById('txtwoven_fin_sum').value=number_format_common(array_sum (woven_fin_fab), 5, 0); 
		document.getElementById('txtknit_fin_sum').value=number_format_common(array_sum (knit_fin_fab), 5, 0); 
		
		document.getElementById('txtwoven_sum_production').value=number_format_common(array_sum (woven_fab_prod), 5, 0); 
		document.getElementById('txtknit_sum_production').value=number_format_common(array_sum (knit_fab_prod), 5, 0);
		
		document.getElementById('txtwoven_fin_sum_production').value=number_format_common(array_sum (woven_fin_fab_prod), 5, 0); 
		document.getElementById('txtknit_fin_sum_production').value=number_format_common(array_sum (knit_fin_fab_prod), 5, 0);
		
		
		document.getElementById('txtwoven_sum_purchase').value=number_format_common(array_sum (woven_fab_purc), 5, 0); 
		document.getElementById('txtknit_sum_purchase').value=number_format_common(array_sum (knit_fab_purc), 5, 0);
		
		document.getElementById('txtwoven_fin_sum_purchase').value=number_format_common(array_sum (woven_fin_fab_purc), 5, 0); 
		document.getElementById('txtknit_fin_sum_purchase').value=number_format_common(array_sum (knit_fin_fab_purc), 5, 0);
		
		document.getElementById('txtwoven_amount_sum_purchase').value=number_format_common(array_sum (woven_fab_purc_amt), 5, 0); 
		document.getElementById('txtkint_amount_sum_purchase').value=number_format_common(array_sum (knit_fab_purc_amt), 5, 0);
	}
	
	function fnc_fabric_cost_dtls( operation )
	{
		// alert(operation)
		freeze_window(operation);
		if(operation==2)
		{
			alert("Delete Restricted")
			release_freezing();
			return;
		}
		
		/*var buyer_profit_per= $('#txt_margin_dzn_po_price').attr('buyer_profit_per')*1;
		var margin_dzn_percent= $('#txt_margin_dzn_po_price').val()*1;
		if(buyer_profit_per!=0)
		{
			if(buyer_profit_per>margin_dzn_percent)
			{
				alert("Buyer Profit % is greater-than Margin Dzn %");
				if(user_level!=2)
				{
					release_freezing();
					return;
				}
			}
		}*/
		
		if(operation==1)
		{
			var txt_job_no=document.getElementById('txt_job_no').value;
			//get_php_form_data(txt_job_no, 'check_data_mismass', "requires/pre_cost_entry_controller_v2" );
			/*var check_input=document.getElementById('check_input').value*1;
			var is_click_cons_box=document.getElementById('is_click_cons_box').value*1;
			if(is_click_cons_box==1 && check_input==1)
			{
				alert("Change found in color size Brackdown,Please Click in Avg. Grey Cons Input Box and just close the popup and click update button")
				release_freezing();
				return;
			}*/
		}
		
	    var bgcolor='-moz-linear-gradient(bottom, rgb(254,151,174) 0%, rgb(255,255,255) 10%, rgb(254,151,174) 96%)';
	    var row_num=$('#tbl_fabric_cost tr').length-1;
		var data_all=""; 
		for (var i=1; i<=row_num; i++)
		{
			var txtconsumption=document.getElementById('txtconsumption_'+i).value;
			if(txtconsumption*1<=0)
			{
				document.getElementById('txtconsumption_'+i).focus();
				document.getElementById('txtconsumption_'+i).style.backgroundImage=bgcolor;
				/*$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
				{ 
					$(this).html('Please Fill up Consumption field Value').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
				});*/
				release_freezing();
				return;
			}
			//if (form_validation('cbogmtsitem_'+i+'*txtbodypart_'+i+'*cbofabricnature_'+i+'*cbocolortype_'+i+'*libyarncountdeterminationid_'+i+'*fabricdescription_'+i+'*txtconsumption_'+i+'*cbofabricsource_'+i+'*uom_'+i,'Gmts Item *Body Part*Fabric Nature*Color Type*Construction*Composition*Consunption*Fabric Source*UOM')==false)
			if (form_validation('cbogmtsitem_'+i+'*txtbodypart_'+i+'*txtbodyparttype_'+i+'*cbofabricnature_'+i+'*cbocolortype_'+i+'*libyarncountdeterminationid_'+i+'*fabricdescription_'+i+'*cbofabricsource_'+i+'*uom_'+i,'Gmts Item *Body Part*Body Part Type*Yarn Nature*Color Type*Construction*Composition*Yarn Source*UOM')==false)
			{
				release_freezing();
				return;
			}
			
			if ($('#cbofabricsource_'+i).val()=='2' &&  (form_validation('txtrate_'+i,'Rate')==false || $('#txtrate_'+i).val()=='0') )
			{
				document.getElementById('txtrate_'+i).focus();
				document.getElementById('txtrate_'+i).style.backgroundImage=bgcolor;
				/*$('#messagebox_main', window.parent.document).fadeTo(100,1,function(){ 
					$(this).html('Please Fill up Rate field Value').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
				});*/
				release_freezing();
				return;
			}
			
			if ($('#cbofabricsource_'+i).val()=='2' &&  (form_validation('txtamount_'+i,'Amount')==false || $('#txtamount_'+i).val()=='0') )
			{
				document.getElementById('txtamount_'+i).focus();
				document.getElementById('txtamount_'+i).style.backgroundImage=bgcolor;
				/*$('#messagebox_main', window.parent.document).fadeTo(100,1,function(){ 
					$(this).html('Please Fill up Amount field Value').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
				});*/
				release_freezing();
				return;
			}
			
			/*if ($('#cbocolorsizesensitive_'+i).val()=='0' && $('#txtcolor_'+i).val()=='')
			{
				document.getElementById('txtcolor_'+i).focus();
				document.getElementById('txtcolor_'+i).style.backgroundImage=bgcolor;
				$('#messagebox_main', window.parent.document).fadeTo(100,1,function(){ 
					$(this).html('Please Fill up Color field Value').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
				});
				release_freezing();
				return;
			}*/
			if($('#consbreckdown_'+i).val()=='')
			{
				$('#txtconsumption_'+i).click();
				release_freezing();
				return;
			}
			if($('#colorbreackdown_'+i).val()=='' && $('#cbocolorsizesensitive_'+i).val()==3)
			{
				alert("Please set Contrast Color");
				$('#txtcolor_'+i).click();
				release_freezing();
				return;
			}
			
			/*if($('#isclickedconsinput_'+i).val()==2)
			{
				document.getElementById('txtconsumption_'+i).focus();
				document.getElementById('txtconsumption_'+i).style.backgroundImage=bgcolor;
				alert(" Change found in color size Brackdown,Please Click in Avg. Grey Cons Input Box and just close the popup and click update button")
				release_freezing();
				return;
			}*/
			data_all=data_all+get_submitted_data_string('cbo_company_name*hidd_job_id*cbo_costing_per*consumptionbasis_'+i+'*update_id*cbogmtsitem_'+i+'*txtbodypart_'+i+'*cbofabricnature_'+i+'*cbocolortype_'+i+'*libyarncountdeterminationid_'+i+'*construction_'+i+'*composition_'+i+'*fabricdescription_'+i+'*txtgsmweight_'+i+'*cbocolorsizesensitive_'+i+'*txtcolor_'+i+'*txtconsumption_'+i+'*cbofabricsource_'+i+'*txtrate_'+i+'*txtamount_'+i+'*txtfinishconsumption_'+i+'*txtavgprocessloss_'+i+'*cbostatus_'+i+'*consbreckdown_'+i+'*msmntbreackdown_'+i+'*updateid_'+i+'*processlossmethod_'+i+'*colorbreackdown_'+i+'*yarnbreackdown_'+i+'*tot_yarn_needed*txtwoven_sum*txtknit_sum*txtwoven_fin_sum*txtknit_fin_sum*txtamount_sum*markerbreackdown_'+i+'*cbowidthdiatype_'+i+'*avg*avgtxtconsumption_'+i+'*avgtxtgsmweight_'+i+'*plancutqty_'+i+'*jobplancutqty_'+i+'*isclickedconsinput_'+i+'*oldlibyarncountdeterminationid_'+i+'*isconspopupupdate_'+i+'*uom_'+i+'*txtbodyparttype_'+i+'*txtwoven_sum_production*txtknit_sum_production*txtwoven_fin_sum_production*txtknit_fin_sum_production*txtwoven_sum_purchase*txtknit_sum_purchase*txtwoven_fin_sum_purchase*txtknit_fin_sum_purchase*txtwoven_amount_sum_purchase*txtkint_amount_sum_purchase*txt_quotation_id*bomfyarn_approval_id',"../../");
		}
		
		var data="action=save_update_delet_fabric_cost_dtls&operation="+operation+'&total_row='+row_num+data_all;
		http.open("POST","requires/pre_cost_entry_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_cost_dtls_reponse;	
	}
	
	function fnc_fabric_cost_dtls_reponse()
	{
		if(http.readyState == 4) 
		{  
			var reponse=trim(http.responseText).split('**');
			if(trim(reponse[0])=='approved'){
				alert("This Costing is Approved");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='materialRecive'){
				alert("Yarn Receive Found :"+trim(reponse[1])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			 }
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not  Fully Approved. Please Approved the Quotation");
				release_freezing();
				return;
			}
			if(reponse[0]==6)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			if(reponse[0]==15) 
			{ 
				 setTimeout('fnc_fabric_cost_dtls('+ reponse[1]+')',8000); 
			}
			else
			{
				var company_name=document.getElementById('cbo_company_name').value*1;
				if (reponse[0].length>2) reponse[0]=10;
				show_msg(reponse[0]);
				fn_report_generated('show_fabric_cost_listview');
				//show_sub_form(document.getElementById('txt_job_no').value, 'show_fabric_cost_listview');
				show_hide_content('fabric_cost', '')
				if(reponse[0]==1)
				{
					var txt_job_no=document.getElementById('txt_job_no').value;
					get_php_form_data(txt_job_no, 'check_data_mismass', "requires/pre_cost_entry_controller_v2" );
					//document.getElementById('is_click_cons_box').value=1;
				}
				release_freezing();
				if(reponse[0]==0 || reponse[0]==1)
				{
					fn_report_generated('show_fabric_cost_listview');
					/* var txt_confirm_price_pre_cost_dzn=(document.getElementById('txt_final_price_dzn_pre_cost').value)*1;
					 var txt_comml_po_price=((reponse[3]*1)/txt_confirm_price_pre_cost_dzn)*100;
					 document.getElementById('txt_comml_pre_cost').value=reponse[3];
					 document.getElementById('txt_comml_po_price').value=number_format_common(txt_comml_po_price, 7, 0);*/
					 //calculate_main_total();
					//fnc_quotation_entry_dtls1(1);
					//set_currier_cost_method_variable(company_name);
					release_freezing();
				}
			}
		}
	}
	

	function openmypage_po()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
      /*		else
		{	
   */		
		//}
	

		var title = 'Refusing Cause';
		var page_link = 'requires/bom_of_fabric_controller.php?action=comment_popup'+'&job_no='+$('#txt_job_no').val()+'&txt_comments='+$('#txt_comments').val();

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','../');

		emailwindow.onclose=function()
		{
			var refusing_cause=this.contentDoc.getElementById("hidden_mail_comment");

			$('#txt_comments').val(refusing_cause.value);
			

		}
	


	}







	function fnc_fabric_yarn_cost_dtls( operation )
	{
		freeze_window(operation);
		if(operation==2)
		{
			alert("Delete Restricted")
			release_freezing();
			return;
		}

		//var buyer_profit_per= $('#txt_margin_dzn_po_price').attr('buyer_profit_per')*1;
		//var margin_dzn_percent= $('#txt_margin_dzn_po_price').val()*1;

		//alert(buyer_profit_per+'-'+margin_dzn_percent);

		/*if(buyer_profit_per!=0)
		{
			if(buyer_profit_per>margin_dzn_percent)
			{
				alert("Buyer Profit % is greater-than Margin Dzn %");
				if(user_level!=2)
				{
					release_freezing();
					return;
				}
			}
		}

		var copy_quatation_id=document.getElementById('copy_quatation_id').value;
		var budget_exceeds_quot_id=document.getElementById('budget_exceeds_quot_id').value;
		var cost_control_source=document.getElementById('txt_cost_control_source').value;
		if(cost_control_source==1 || cost_control_source==5)
		{
			var qc_validate=fnc_budgete_cost_validate(2);
			var qc_validation=qc_validate.split("__");
			if(qc_validation[1]==1)
			{
				alert(qc_validation[0]);
				release_freezing();
				return;
			}
		}
		else if(cost_control_source==2)
		{
			if(copy_quatation_id==1 && budget_exceeds_quot_id==2)
			{
				var pri_fabric_pre_cost= $('#txt_fabric_pre_cost').attr('pri_fabric_pre_cost')*1;
				var txt_fabric_pre_cost= $('#txt_fabric_pre_cost').val()*1;
				//alert(pri_fabric_pre_cost+"==="+txt_fabric_pre_cost);
				if(txt_fabric_pre_cost>pri_fabric_pre_cost)
				{
					alert('Fabric cost is greater than Quotation');
					release_freezing();
					return;
				}
			}
		}*/

	    var row_num=$('#tbl_yarn_cost tr').length-1;
		var data_all=""; var z=1;
		for (var i=1; i<=row_num; i++)
		{
			if (form_validation('update_id*cbocompone_'+i+'*cbocount_'+i+'*cbotype_'+i,'Job No*Comp 1*Count*Type')==false)
			{
				release_freezing();
				return;
			}
			data_all+="&cbocount_" + z + "='" + $('#cbocount_'+i).val()+"'"+"&cbocompone_" + z + "='" + $('#cbocompone_'+i).val()+"'"+"&cbotype_" + z + "='" + $('#cbotype_'+i).val()+"'"+"&updateidyarncost_" + z + "='" + $('#updateidyarncost_'+i).val()+"'";
			z++;
		}
		
		var data="action=save_update_delet_fabric_yarn_cost_dtls&operation="+1+'&total_row='+row_num+get_submitted_data_string('cbo_company_name*hidd_job_id*update_id*txt_quotation_id*txt_cost_control_source*copy_quatation_id*txtconsumptionyarn_sum*txtamountyarn_sum',"../../")+data_all;
		//alert(data); release_freezing(); return;
		//freeze_window(operation);
		http.open("POST","requires/bom_of_fabric_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_yarn_cost_dtls_reponse;
	}

	function fnc_fabric_yarn_cost_dtls_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if(trim(reponse[0])=='approved')
			{
				alert("This Costing is Approved");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='papproved')
			{
				alert("This Costing is Partial Approved");
				release_freezing();
				return;
			}
			
			if(trim(reponse[0])=='readyapproved')
			{
				alert("This costing already submitted for approval. If any change is required, please make Ready To Approve No 1st then try to edit or change.");
				release_freezing();
				return;
			}
			
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not Approved. Please Approved the Quotation");
				release_freezing();
				return;
			}
			if(reponse[0]=='purReq')
			{
				alert("Purchase Requisition Found. Req. No:"+reponse[1]);
				release_freezing();
				return;
			}
			if(reponse[0]==15)
			{
				 setTimeout('fnc_fabric_yarn_cost_dtls('+ reponse[1]+')',8000);
			}
			else
			{
				var company_name=document.getElementById('cbo_company_name').value;
				if (reponse[0].length>2) reponse[0]=10;
				show_msg(reponse[0]);

				if(reponse[0]==0 || reponse[0]==1)
				{
					//var txt_fabric_pre_cost=(document.getElementById('txtamount_sum').value*1)+(document.getElementById('txtamountyarn_sum').value*1)+(document.getElementById('txtconamount_sum').value*1);
					//var pre_fabcost=number_format_common(txt_fabric_pre_cost, 1, 0,document.getElementById('cbo_currercy').value);
					//$("#txt_fabric_pre_cost").attr('pre_fab_cost',pre_fabcost);

					//calculate_main_total();
				}
				fn_report_generated('show_fabric_cost_listview');
				//show_sub_form(document.getElementById('update_id').value, 'show_fabric_cost_listview');
				show_hide_content('yarn_cost', '')
				release_freezing();
				if(reponse[0]==0 || reponse[0]==1)
				{
					//alert(reponse[3])
					/*var txt_confirm_price_pre_cost_dzn=(document.getElementById('txt_final_price_dzn_pre_cost').value)*1;
					var txt_comml_po_price=((reponse[3]*1)/txt_confirm_price_pre_cost_dzn)*100;
					document.getElementById('txt_comml_pre_cost').value=reponse[3];
					document.getElementById('txt_comml_po_price').value=number_format_common(txt_comml_po_price, 7, 0);
					calculate_main_total();
					set_currier_cost_method_variable(company_name);
					fnc_quotation_entry_dtls1(1);*/
					release_freezing();
				}
			}
		}
	}
	
	function open_color_popup(i)
	{
		var cbo_company_id=document.getElementById('cbo_company_name').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var txt_job_no = document.getElementById('txt_job_no').value;
		var cbogmtsitem = document.getElementById('cbogmtsitem_'+i).value;
		var color_breck_down=document.getElementById('colorbreackdown_'+i).value;
		var cbocolorsizesensitive=document.getElementById('cbocolorsizesensitive_'+i).value;
		var pre_cost_fabric_cost_dtls_id=document.getElementById('updateid_'+i).value;
		var precostapproved=document.getElementById('precostapproved_'+i).value;
		if(cbocolorsizesensitive==3)
		{
			var page_link="requires/pre_cost_entry_controller_v2.php?txt_job_no="+trim(txt_job_no)+"&action=open_color_list_view&color_breck_down="+color_breck_down+"&cbogmtsitem="+cbogmtsitem+"&cbo_company_id="+cbo_company_id+"&cbo_buyer_name="+cbo_buyer_name+'&pre_cost_fabric_cost_dtls_id='+pre_cost_fabric_cost_dtls_id+'&precostapproved='+precostapproved;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Set Details", 'width=400px,height=480px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var color_breck_down=this.contentDoc.getElementById("color_breck_down") //Access form field with id="emailfield"
				document.getElementById('colorbreackdown_'+i).value=color_breck_down.value;
			}
		}
	}
	
	function color_select_popup(buyer_name,texbox_id)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/pre_cost_entry_controller_v2.php?action=color_popup&buyer_name='+buyer_name, 'Color Select Pop Up', 'width=250px,height=450px,center=1,resize=1,scrolling=0','../../')
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
	
	function calculate_yarn_consumption_ratio(i,type)
	{
		var cbocount=document.getElementById('cbocount_'+i).value;
		var cbocompone=document.getElementById('cbocompone_'+i).value;
		var percentone=document.getElementById('percentone_'+i).value;
		//var cbocomptwo=document.getElementById('cbocomptwo_'+i).value;
		//var percenttwo=document.getElementById('percenttwo_'+i).value;
		var cbotype=document.getElementById('cbotype_'+i).value;
		var txtrateyarn=document.getElementById('txtrateyarn_'+i).value

		var rowCount = $('#tbl_yarn_cost tr').length-1;

		for (var k=i; k<=rowCount; k++)
		{
			var cbocountk=document.getElementById('cbocount_'+k).value;
			var cbocomponek=document.getElementById('cbocompone_'+k).value;
			var percentonek=document.getElementById('percentone_'+k).value;
			//var cbocomptwok=document.getElementById('cbocomptwo_'+k).value;
			//var percenttwok=document.getElementById('percenttwo_'+k).value;
			var cbotypek=document.getElementById('cbotype_'+k).value;
			if(cbocount==cbocountk && cbocompone==cbocomponek && percentone==percentonek && cbotype==cbotypek)
			{
				document.getElementById('txtrateyarn_'+k).value=txtrateyarn;
				document.getElementById('txtamountyarn_'+k).value=number_format_common((document.getElementById('consqnty_'+k).value*1)*(txtrateyarn*1),1,0,document.getElementById('cbo_currercy').value);
			}
			else
			{
				document.getElementById('txtamountyarn_'+i).value=number_format_common((document.getElementById('consqnty_'+i).value*1)*(document.getElementById('txtrateyarn_'+i).value*1),1,0,document.getElementById('cbo_currercy').value);
			}
		}
		//set_sum_value( 'txtconsratio_sum', 'consratio_', 'tbl_yarn_cost' );
		set_sum_value( 'txtconsumptionyarn_sum', 'consqnty_', 'tbl_yarn_cost' );
		set_sum_value( 'txtavgconsumptionyarn_sum', 'avgconsqnty_', 'tbl_yarn_cost' );
		set_sum_value( 'txtconsumptionyarn_sum', 'consqnty_', 'tbl_yarn_cost' );
		set_sum_value( 'txtamountyarn_sum', 'txtamountyarn_', 'tbl_yarn_cost' );
	}
	
	function fnc_fabric_yarn_cost_dtls2( operation )
	{
		freeze_window(operation);
		if(operation==2)
		{
			alert("Delete Restricted")
			release_freezing();
			return;
		}
		
		//var buyer_profit_per= $('#txt_margin_dzn_po_price').attr('buyer_profit_per')*1;
		//var margin_dzn_percent= $('#txt_margin_dzn_po_price').val()*1;
		
		//alert(buyer_profit_per+'-'+margin_dzn_percent);
		
		/*if(buyer_profit_per!=0)
		{
			if(buyer_profit_per>margin_dzn_percent)
			{
				alert("Buyer Profit % is greater-than Margin Dzn %");
				if(user_level!=2)
				{
					release_freezing();
					return;
				}
			}
		}*/
		
		/*var copy_quatation_id=document.getElementById('copy_quatation_id').value;
		var budget_exceeds_quot_id=document.getElementById('budget_exceeds_quot_id').value;
		var cost_control_source=document.getElementById('txt_cost_control_source').value;
		if(cost_control_source==1)
		{
			var qc_validate=fnc_budgete_cost_validate();
			var qc_validation=qc_validate.split("__");
			if(qc_validation[1]==1)
			{
				alert(qc_validation[0]);
				release_freezing();
				return;
			}
		}
		else if(cost_control_source==2)
		{
			if(copy_quatation_id==1 && budget_exceeds_quot_id==2)
			{
				var pri_fabric_pre_cost= $('#txt_fabric_pre_cost').attr('pri_fabric_pre_cost')*1;
				var txt_fabric_pre_cost= $('#txt_fabric_pre_cost').val()*1;
				//alert(pri_fabric_pre_cost+"==="+txt_fabric_pre_cost);
				if(txt_fabric_pre_cost>pri_fabric_pre_cost)
				{
					release_freezing();
					alert('Fabric cost is greater than Quotation');
					return;
				}
			}
		}*/
		
	    var row_num=$('#tbl_yarn_cost tr').length-1;
		var data_all="";
		for (var i=1; i<=row_num; i++)
		{
			if (form_validation('update_id*cbocompone_'+i+'*percentone_'+i+'*consqnty_'+i+'*cbocount_'+i+'*cbotype_'+i+'*txtrateyarn_'+i,'Company Name*Comp 1*Percent*Cons Qnty*Count*Type*Rate')==false)
			{
				release_freezing(); 
				return;
			}
			data_all=data_all+get_submitted_data_string('cbo_company_name*hidd_job_id*update_id*txt_quotation_id*cbocount_'+i+'*cbocompone_'+i+'*percentone_'+i+'*color_'+i+'*cbotype_'+i+'*consqnty_'+i+'*txtrateyarn_'+i+'*avgconsqnty_'+i+'*txtconsdznlbs_'+i+'*txtamountyarn_'+i+'*supplier_'+i+'*updateidyarncost_'+i+'*txtconsumptionyarn_sum*txtamountyarn_sum',"../../");
		}
		var data="action=save_update_delet_fabric_yarn_cost_dtls&operation="+operation+'&total_row='+row_num+data_all;
		//freeze_window(operation);
		http.open("POST","requires/pre_cost_entry_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_yarn_cost_dtls_reponse;
	}

	function fnc_fabric_yarn_cost_dtls_reponse2()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			if(trim(reponse[0])=='approved')
			{
				alert("This Costing is Approved");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='materialRecive'){
				alert("Yarn Receive Found :"+trim(reponse[1])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			 }
			if(reponse[0]=='quataNotApp')
			{
				alert("Quotation is not  Fully Approved. Please Approved the Quotation");
				release_freezing();
				return;
			}
			if(reponse[0]==15) 
			{ 
				 setTimeout('fnc_fabric_yarn_cost_dtls('+ reponse[1]+')',8000); 
			}
			else
			{
				fn_report_generated('show_fabric_cost_listview');
				release_freezing();
			}
		}
	}

	function generate_report(type)
	{
		if (form_validation('txt_job_no','Please Select The Job Number.')==false)
		{
			return;
		}
		else
		{
			
			var rate_amt=2; var zero_val='';
			if(type!='mo_sheet')
			{
				var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
			}
			
			if (r==true) zero_val="1"; else zero_val="0";
			//eval(get_submitted_variables('txt_job_no*cbo_company_name*cbo_buyer_name*txt_style_ref*txt_costing_date'));
			var data="action="+type+"&zero_value="+zero_val+"&rate_amt="+rate_amt+"&"+get_submitted_data_string('txt_job_no*cbo_company_name*cbo_buyer_name*cbo_costing_per',"../../");
			freeze_window(3);
			http.open("POST","requires/pre_cost_entry_controller_v2.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_generate_report_reponse;
		}
	}

	function fnc_generate_report_reponse()
	{
		if(http.readyState == 4) 
		{
			$('#data_panel').html( http.responseText );
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
			show_msg('3');
			release_freezing();
		}
	}

	function fn_bomfyarn(type){		
		if(type==1){
			$("#bomfyarn_id").hide();
			$("#bomfyarn_id1").show();
			$("#bomfyarn_approval_id").val(1);
		}else{
			$("#bomfyarn_id").show();
			$("#bomfyarn_id1").hide();
			$("#bomfyarn_approval_id").val(2);
		}
	}
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",''); ?>
    <h3 align="left" id="accordion_h1" style="width:360px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
            <fieldset style="width:360px;">
                <table class="rpt_table" width="360" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">
                    <thead>
                        <tr>                    
                            <th width="150" class="must_entry_caption">Company Name</th>
                            <th width="110" class="must_entry_caption">Job No</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:80px" value="Reset" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Select Company-", $selected, "" ); ?></td>
                            <td>
                                <input  style="width:100px;" type="text" title="Double Click to Search" onDblClick="openmypage('Job/Order Selection Form');" class="text_boxes" placeholder="Browse" name="txt_job_no" id="txt_job_no" value="" readonly />
                                
                                <input type="hidden" id="update_id" class="text_boxes" style="width:80px" value="" />
                                <input type="hidden" id="cbo_buyer_name" class="text_boxes" style="width:80px" value="" />
                                <input type="hidden" id="txt_quotation_id" class="text_boxes" style="width:80px" value="" />
                                
                                <input type="hidden" id="cbo_currercy" class="text_boxes" style="width:80px" value="" />
                                <input type="hidden" id="txt_exchange_rate" class="text_boxes" style="width:80px" value="" />
                                <input type="hidden" id="cbo_costing_per" class="text_boxes" style="width:80px" value="" />
                                <input type="hidden" id="cbo_approved_status" class="text_boxes" style="width:80px" value="" />
                                <input type="hidden" id="txt_cost_control_source" value="" />
                                <input type="hidden" id="copy_quatation_id" value="" />
                        		<input type="hidden" id="budget_exceeds_quot_id" value="" />
                                <input type="hidden" id="check_input" class="text_boxes" style="width:80px" value="" />
                                
                                <input type="hidden" id="txt_fabric_pre_cost" class="text_boxes" style="width:80px" value="" />
                                <input type="hidden" id="txt_fabric_po_price" class="text_boxes" style="width:80px" value="" />
                                <input type="hidden" name="hidd_job_id" id="hidd_job_id" style="width:30px;" class="text_boxes" />
								<input type="hidden" id="bomfyarn_approval_id" class="formbutton" style="width:80px"  value=""  />
								<input type="hidden" id="bomfyarn_approval_id" class="formbutton" style="width:80px"  value=""  />
								<input type="hidden" id="txt_comments" class="formbutton" style="width:80px"  value=""  />
								
                             </td>
                             <td>
                                <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_report_generated('show_fabric_cost_listview');" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
    <div id="check_sms" style="display:none;"></div>
    </form>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>    
    <div style="display:none;" id="data_panel"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>