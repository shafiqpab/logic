<?
/*--- ----------------------------------------- Comments
Purpose			: 						
Functionality	:	
JS Functions	:
Created by		:	K.M Nazim Uddin
Creation date 	: 	06-05-2019
Updated by 		: 		
Update date		:
Oracle Convert 	:		
Convert date	: 	   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
	session_start();
	if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
	require_once('../../includes/common.php');
	extract($_REQUEST);
	$_SESSION['page_permission']=$permission;
	//----------------------------------------------------------------------------------------------------------------
	echo load_html_head_contents("Booked UOM Setup", "../../", 1, 1,$unicode,'','');
?>
<script type="text/javascript">

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	<?
	//$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][184] );
	//echo "var field_level_data= ". $data_arr . ";\n";
	
	
	?>
	function fnc_booked_uom_setup( operation )
	{
		var j=0; var check_field=0; data_all=""; var i=0;
		var txt_deleted_id 			= $('#txt_deleted_id').val();
		var cbo_company_name 		= $('#cbo_company_name').val();
			
		$("#tbl_dtls_emb tbody tr").each(function()
		{
			var cboSection 			= $(this).find('select[name="cboSection[]"]').val();
			var cboSubSection 		= $(this).find('select[name="cboSubSection[]"]').val();
			var cboUom 				= $(this).find('select[name="cboUom[]"]').val();
			var hdnDtlsUpdateId 	= $(this).find('input[name="hdnDtlsUpdateId[]"]').val();
			j++

			if(cboSection==0 || cboSubSection==0 || cboUom==0)
			{	 				
				if(cboSection==0)
				{
					alert('Please Select Section');
					check_field=1 ; return;
				}
				else if(cboSubSection==0)
				{
					alert('Please Select Sub Section');
					check_field=1 ; return;
				}
				else
				{
					alert('Please Select Order UOM ');
					check_field=1 ; return;
				}
			}
			i++;
			data_all += "&cboSection_" + j + "='" + cboSection + "'&cboSubSection_" + j + "='" + cboSubSection + "'&cboUom_" + j + "='" + cboUom + "'&hdnDtlsUpdateId_" + j + "='" + hdnDtlsUpdateId + "'";
		});
		
		if(check_field==0)
		{
			var data="action=save_update_delete&operation="+operation+'&total_row='+i+'&cbo_company_name='+cbo_company_name+'&txt_deleted_id='+txt_deleted_id+data_all;
			//alert (data); //return;
			freeze_window(operation);
			http.open("POST","requires/booked_uom_setup_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_booked_uom_setup_reponse;
		}
		else
		{
			return;
		}
	}
	
	function fnc_booked_uom_setup_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
			if(response[0]==0 || response[0]==1|| response[0]==2)
			{
				load_booked_uom_list();
			}
			else if(response[0]=="26")
			{
				alert ("Duplicate Data Found .Operation Not Complete.");
				release_freezing();
				return;
			}
		}
		release_freezing();
	}
	

	function fnc_addRow( i, table_id, tr_id )
	{ 
		//alert();
		var prefix=tr_id.substr(0, tr_id.length-1);
		var row_num = $('#tbl_dtls_emb tbody tr').length; 
		//alert(i+"**"+table_id+"**"+tr_id+"**"+row_num);
		row_num++;
		var clone= $("#"+tr_id+i).clone();
		clone.attr({
			id: tr_id + row_num,
		});
		
		clone.find("input,select").each(function(){
			$(this).attr({ 
				'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
				'name': function(_, name) { return name },
				'value': function(_, value) { return value }
			});
		}).end();
		$("#"+tr_id+i).after(clone);
		$('table #row_'+row_num+' #subSectionTd_'+i).removeAttr("id").attr('id','subSectionTd_'+row_num);
		$('#cboSection_'+row_num).removeAttr("onChange").attr("onChange","load_sub_section("+row_num+")");

		$('#hdnDtlsUpdateId_'+row_num).removeAttr("value").attr("value","");
		$('#cboSection_'+row_num).removeAttr("value").attr("value","0");
		$('#cboSubSection_'+row_num).removeAttr("value").attr("value","0");
		$('#cboUom_'+row_num).removeAttr("value").attr("value","0");
		$('#increase_'+row_num).removeAttr("value").attr("value","+");
		$('#decrease_'+row_num).removeAttr("value").attr("value","-");
		$('#increase_'+row_num).removeAttr("onclick").attr("onclick","fnc_addRow("+row_num+",'"+table_id+"','"+tr_id+"');");
		$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fnc_deleteRow("+row_num+",'"+table_id+"','"+tr_id+"');");
		set_all_onclick();
	}

	function fn_deletebreak_down_tr(rowNo,table_id) 
	{   
		var numRow = $('table#tbl_dtls_emb tbody tr').length; 
		if(numRow==rowNo && rowNo!=1)
		{
			$('#tbl_dtls_emb tbody tr:last').remove();
		}
	}

	function show_detail_form(mst_id)
	{
		show_list_view(mst_id,'show_detail_form','form_div','requires/booked_uom_setup_entry_controller','');
	}


	function load_sub_section(rowNo)
	{
		//alert(rowNo);
		var section=$('#cboSection_'+rowNo).val();
		var row_num = $('#tbl_dtls_emb tbody tr').length;
		for(i=rowNo;i<=row_num;i++)
		{
			$('#cboSection_'+i).val(section);
			load_drop_down( 'requires/booked_uom_setup_entry_controller',section+'_'+i , 'load_drop_down_subsection', 'subSectionTd_'+i );
		}
	}

	
	function fnc_deleteRow(rowNo,table_id,tr_id) 
	{ 
		var numRow = $('#'+table_id+' tbody tr').length; 
		var prefix=tr_id.substr(0, tr_id.length-1);
		var total_row=$('#'+prefix+'_tot_row').val();
		
		var numRow = $('table#tbl_dtls_emb tbody tr').length; 
		if(numRow!=1)
		{
			var updateIdDtls=$('#hdnDtlsUpdateId_'+rowNo).val();
			var txt_deleted_id=$('#txt_deleted_id').val();
			var selected_id='';
			
			if(updateIdDtls!='')
			{
				if(txt_deleted_id=='') selected_id=updateIdDtls; else selected_id=txt_deleted_id+','+updateIdDtls;
				$('#txt_deleted_id').val( selected_id );
			}
			
			$("#"+tr_id+rowNo).remove();
			$('#'+prefix+'_tot_row').val(total_row-1);
			//calculate_total_amount(1);
		}
		else
		{
			return false;
		}
	}
	function load_booked_uom_list()
	{
		var company=$('#cbo_company_name').val();
		show_list_view(company,'dtls_list_view', 'booked_tbody', 'requires/booked_uom_setup_entry_controller', '' ) ;
		var cboSection=$('#cboSection_1').val();
		if(cboSection==0)
		{
			set_button_status(0, permission, 'fnc_booked_uom_setup',1);
		}
		else
		{
			set_button_status(1, permission, 'fnc_booked_uom_setup',1);
		}
	}
</script>

</head>	
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%; position:relative; margin-bottom:5px; margin-top:5px">
		<? echo load_freeze_divs ("../../",$permission);  ?>
        <form name="yarncountdetermination_1" id="yarncountdetermination_1" autocomplete="off">
            <fieldset style="width:480px;">
                <legend>Booked UOM Setup</legend>
                <div id="form_div">
                	<table>
                		<tr>
	                    	<td width="120" class="must_entry_caption"><strong>Company Name</strong></td>
	                        <td width="200"><? echo create_drop_down( "cbo_company_name", 200, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_booked_uom_list()"); ?>
	                        </td>
	                    </tr>
                	</table>
                    <table width="100%" border="0" id="tbl_dtls_emb" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" >
                        <thead>
                            <tr>
                            	<th width="150" class="must_entry_caption">Section</th>
                            	<th width="150" class="must_entry_caption">Sub Section</th>
                            	<th width="100" class="must_entry_caption">Booked UOM</th>
                            	<th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody id="booked_tbody">
                        </tbody>
                    </table>
                </div>
                <table width="100%" border="" cellpadding="0" cellspacing="0"  rules="all">
                    <tr>
                        <td colspan="4" align="center" class="button_container"><? echo load_submit_buttons( $permission, "fnc_booked_uom_setup", 0,0 ,"reset_form('yarncountdetermination_1','','')",1); ?> 
                        </td>
                        <input type="hidden" name="txt_deleted_id[]" id="txt_deleted_id" class="text_boxes_numeric" style="width:90px" readonly />				
                    </tr>
                </table>
            </fieldset>
        </form>	
		<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
		<script type="text/javascript">load_booked_uom_list()</script>
    </div>
</body>

</html>
