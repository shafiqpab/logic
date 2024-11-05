<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create for Demand for accessories
Functionality	:
JS Functions	:
Created by		:	Jahid
Creation date 	: 	22-05-2021
Updated by 		: 	REZA
Update date		: 	9-9-2021
QC Performed BY	:
QC Date			:
Comments		:

*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//-------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Demand For Accessories", "../../",  1, 1, $unicode,'','');

?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
    var permission='<? echo $permission; ?>';
	
	function openmypage(page_link,title)
	{
		if( form_validation('cbo_company_name*cbo_buyer_name*cbo_team_leader*cbo_dealing_merchant','Company*Buyer*Team Leader*Dealing Merchant')==false )
		{
			return;
		}
		var company_id=$('#cbo_company_name').val();
		var cbo_buyer_name=$('#cbo_buyer_name').val();
		var cbo_team_leader=$('#cbo_team_leader').val();
		var cbo_dealing_merchant=$('#cbo_dealing_merchant').val();
		var cbo_season_name=$('#cbo_season_name').val();
		var cbo_season_year=$('#cbo_season_year').val();
		var cbo_brand=$('#cbo_brand').val();
		
		var hidd_job_sl_no=$('#hidd_job_sl_no').val();
		var hidd_job_id=$('#hidd_job_id').val();
		var hidd_txt_style_no=$('#hidd_txt_style_no').val();
		var txt_style_no=$('#txt_style_no').val();		
		var update_id=$('#update_id').val();
		
		page_link+='&company_id='+company_id+'&cbo_buyer_name='+cbo_buyer_name+'&cbo_team_leader='+cbo_team_leader+'&cbo_dealing_merchant='+cbo_dealing_merchant+'&cbo_season_name='+cbo_season_name+'&cbo_season_year='+cbo_season_year+'&cbo_brand='+cbo_brand+'&hidd_job_sl_no='+hidd_job_sl_no+'&hidd_job_id='+hidd_job_id+'&hidd_txt_style_no='+hidd_txt_style_no+'&txt_style_no='+txt_style_no+'&update_id='+update_id;
		//alert(page_link);return;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=740px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theemailid=this.contentDoc.getElementById("txt_job_id").value;
			var theemailjobSlNo=this.contentDoc.getElementById("txt_job_sl_no").value;
			var theemailstyle=this.contentDoc.getElementById("txt_style_ref").value;
			var theemailstylerefno=this.contentDoc.getElementById("txt_style_ref_no").value;
			//var response=theemailid.value.split('_');
			if ( theemailid!="" )
			{
				$("#hidd_job_id").val(theemailid);
				$("#hidd_job_sl_no").val(theemailjobSlNo);
				$("#txt_style_no").val(theemailstyle);
				$("#hidd_txt_style_no").val(theemailstylerefno);
				disable_enable_fields('cbo_company_name*cbo_buyer_name*cbo_team_leader*cbo_dealing_merchant',1);
				$('#cs_generate_check').val(0);
			}
		}
	}
	
    function fnc_generate_demand() 
    {
        if( form_validation('cbo_company_name*cbo_buyer_name*cbo_team_leader*cbo_dealing_merchant*txt_style_no','Company*Buyer*Team Leader*Dealing Merchant*Style')==false )
		{
			return;
		}
		var update_id=$("#update_id").val();
		var hidd_job_id=$("#hidd_job_id").val();
		var company_id=$("#cbo_company_name").val();
        show_list_view(hidd_job_id+'**'+company_id+'**'+update_id, 'load_cs_table', 'cs_tbl', 'requires/demand_for_accessories_controller', 'setFilterGrid(\'cs_tbl\',-1)');
		$('#cs_generate_check').val(1);
		
    }
	
    function fnc_comparative_statement(operation) 
    {
        /*if(operation==4)
		{
            var form_caption=$( "div.form_caption" ).html();
	 	    print_report( $('#update_id').val()+'*'+form_caption, "comparative_statement_print", "requires/demand_for_accessories_controller" )
	 	    return;
        }*/
		
		var cs_generate_check=$('#cs_generate_check').val();
		if(cs_generate_check==0)
		{
			alert("Please Press Generate Button");return;
		}
		
       	if( form_validation('cbo_company_name*cbo_buyer_name*cbo_team_leader*cbo_dealing_merchant*txt_style_no*txt_demand_date','Company*Buyer*Team Leader*Dealing Merchant*Style*Date')==false )
		{
			return;
		}
		
        /*if(date_compare($('#txt_cs_date').val(), $('#txt_validity_date').val())==false)
		{
			alert("CS Validity Date Can not Be Less Than CS Date");
			return;
		}
		var cbo_approved=$("#cbo_approved").val();
		if(operation==2 && cbo_approved==1)
		{
			alert("CS Approved, Delete Not Allow");return;
		}
		
		var prev_req_dtls_id=('#prev_req_dtls_id').val();
		var txt_requisition_dtls=('#txt_requisition_dtls').val();
		var update_id=('#update_id').val();
		if(update_id!="" && prev_req_dtls_id !="" txt_requisition_dtls)*/

        var row_num=$('#tbl_details tbody tr').length;
		//alert(row_num);return;
        var data_dtls="";
        if(row_num==0)
		{
            alert("Please Click Genarate");
			return;
        }
		
        for (var i=1; i<=row_num; i++)
		{
            data_dtls += '&preCostDtlsId_' + i + '=' + $('#preCostDtlsId_'+i).val() + '&itemGroupId_' + i + '=' + $('#itemGroupId_'+i).val() + '&mainGroupId_' + i + '=' + $('#mainGroupId_'+i).val() + '&nominatedSup_' + i + '=' + $('#nominatedSup_'+i).val()+ '&uom_' + i + '=' + $('#uom_'+i).val()+ '&brandSup_' + i + '=' + $('#brandSup_'+i).text()+ '&description_' + i + '=' + $('#description_'+i).text()+ '&txtReqQty_' + i + '=' + $('#txtReqQty_'+i).val() + '&txtStockQty_' + i + '=' + $('#txtStockQty_'+i).val() + '&txtRate_' + i + '=' + $('#txtRate_'+i).val()+ '&txtAmount_' + i + '=' + $('#txtAmount_'+i).val()+ '&jobId_' + i + '=' + $('#jobId_'+i).val() + '&jobNo_' + i + '=' + $('#jobNo_'+i).val()+ '&styleNo_' + i + '=' + $('#styleNo_'+i).val() + '&txtDate_' + i + '=' + change_date_format(trim($('#txtDate_'+i).val())) + '&txtRemarks_' + i + '=' + $('#txtRemarks_'+i).val() + '&dtlsUpdateId_' + i + '=' + $('#dtlsUpdateId_'+i).val();
        }
        // console.log(data_supplier);
		// alert(data_dtls);return;
		// change_date_format(trim(document.getElementById(flds[i]).value),path)
        //var data_mst=get_submitted_data_string('cbo_basis_name*txt_requisition*txt_requisition_mst*txt_requisition_dtls*txt_rcvd_date*txt_cs_date*supplier_id*cbo_currency_name*txt_validity_date*cbo_source*cbo_approved*cbo_company_name*txt_comments*update_id*txt_system_id',"../../");
        var data="action=save_update_delete&operation="+operation+"&row_num="+row_num+"&data_dtls="+data_dtls+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_season_name*cbo_season_year*cbo_brand*cbo_team_leader*cbo_dealing_merchant*txt_style_no*hidd_job_id*txt_demand_date*txt_remarks*update_id*txt_system_id*txt_cs_req_date',"../../");
        // alert(data);return;
        freeze_window(operation);
        http.open("POST","requires/demand_for_accessories_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_comparative_statement_reponse;
    }
	
    function fnc_comparative_statement_reponse()
    {
        if(http.readyState == 4) 
		{
			//alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			
			if(reponse[3]){
				var row_num=$('#tbl_details tbody tr').length;
				var dtls_id_arr = reponse[3].split(',');
				for (var i=0; i<row_num; i++)
				{
					var dtls_file_id_name ='demandFile_'+(i+1);
					//alert(dtls_id_name);
					 fileUpload(dtls_file_id_name,dtls_id_arr[i],'demand_for_accessories','../../',2);	
				}
			}
			
			
			
			
			if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1)
			{
				document.getElementById('txt_system_id').value=reponse[1];
				document.getElementById('update_id').value=reponse[2];
				disable_enable_fields('cbo_company_name*cbo_buyer_name*cbo_season_name*cbo_season_year*cbo_brand*cbo_team_leader*cbo_dealing_merchant',1);
				set_button_status(1, permission, 'fnc_comparative_statement',1);
				
			}
			if(parseInt(trim(reponse[0]))==2)
			{
				$("#comparativestatement_1").find('input[type="text"],input[type="hidden"]').val('').attr("disabled",false);
				$("#comparativestatement_1").find('select').val(0).attr("disabled",false);
                set_button_status(0, permission, 'fnc_comparative_statement',1);
				$('#cs_tbl').html("");
			}
			if(parseInt(trim(reponse[0]))==11)
			{
				alert(trim(reponse[1]));release_freezing();return;
			}
			
			
			show_msg(trim(reponse[0]));
			release_freezing();
		}
    }
	
	function all_date()
	{
		var check_date_val=$('#check_date').val();
		var dtls_date=$('#txtDate_1').val();
		var row_num=$('#tbl_details tbody tr').length;
		if(check_date_val==0)
		{
			for (var i=1; i<=row_num; i++)
			{
				$('#txtDate_'+i).val(dtls_date);
			}
			$('#check_date').val(1);
		}
		if(check_date_val==1)
		{
			for (var i=1; i<=row_num; i++)
			{
				$('#txtDate_'+i).val(null);
			}
			$('#check_date').val(0);
		}

	}

    function openmypage_cs_no()
	{
		if( form_validation('cbo_company_name','Company')==false )
		{
			return;
		}
		var company_id=$('#cbo_company_name').val();
		
        var page_link='requires/demand_for_accessories_controller.php?action=system_popup&company_id='+company_id;
        var title='Search Demand PopUp';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=430px,center=1,resize=0,scrolling=0','../')
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var theemail=this.contentDoc.getElementById("selected_id").value;
			var company_id=this.contentDoc.getElementById("selected_company").value;
			var hidd_job_id=this.contentDoc.getElementById("selected_job").value;
			//alert(theemail+"="+company_id+"="+hidd_job_id);return;
            if (theemail!="")
            {
                freeze_window(5);
                get_php_form_data(theemail, "populate_data_from_search_popup", "requires/demand_for_accessories_controller" );
				show_list_view(hidd_job_id+'**'+company_id+'**'+theemail, 'load_cs_table', 'cs_tbl', 'requires/demand_for_accessories_controller', 'setFilterGrid(\'cs_tbl\',-1)');
				$('#cs_generate_check').val(1);
				disable_enable_fields('cbo_company_name*cbo_buyer_name*cbo_season_name*cbo_season_year*cbo_brand*cbo_team_leader*cbo_dealing_merchant',1);
                set_button_status(1, permission, 'fnc_comparative_statement',1);
                release_freezing();
            }
        }
	}
	
	function form_reset_cs(str)
	{
		if(str==1)
		{
			$("#comparativestatement_1").find('input[type="text"],input[type="hidden"]:not([name="cs_generate_check"])').val('');
			$("#comparativestatement_1").find('select:not([name="cbo_basis_name"])').val(0);
			$("#cs_tbl").html("");
			$('#cbo_basis_name').attr('disabled',false);
			set_button_status(1, permission, 'fnc_comparative_statement',0);
		}
		else
		{
			$("#comparativestatement_1").find('input[type="text"],input[type="hidden"]:not([name="cs_generate_check"])').val('');
			$("#comparativestatement_1").find('select').val(0);
			$("#cs_tbl").html("");
			$('#cbo_basis_name').attr('disabled',false);
			set_button_status(1, permission, 'fnc_comparative_statement',0);
		}
	}
	
	function print_button_setting()
	{
		get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/demand_for_accessories_controller' );
	}
	
	function fnc_print(type)
	{
		if (form_validation('update_id','Save Data First')==false)
		{
			alert("Save Data First");
			return;
		}
		else
		{
			print_report(type+'**'+$('#update_id').val()+'**'+$('#cbo_company_name').val()+'**'+$('#cbo_template_id').val(),'report_generate','requires/demand_for_accessories_controller');
		}
	}
	
	
	function sendMail()
	{
		if(confirm("Mail Send! Sure?")){
			var responseHtml = return_ajax_request_value($('#update_id').val()+'__'+$('#cbo_company_name').val()+'__'+$('#cbo_template_id').val(), 'mail_actin', '../../auto_mail/woven/demand_for_accessories_mail');
			//alert(responseHtml);
		
		}
	}
	
	function fn_leftover(row_ref)
	{
		var page_link='requires/demand_for_accessories_controller.php?action=leftover_popup&row_ref='+row_ref;
        var title='Leftover Quantity PopUp';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=0,scrolling=0','../')
        emailwindow.onclose=function()
        {
            
        }
	}
	

</script>
<body onLoad="set_hotkey()">
    <div align="left">
        <? echo load_freeze_divs ("../../",$permission); ?>
        <div style="width:100%;" align="center">
            <form name="comparativestatement_1" id="comparativestatement_1" autocomplete="off">
                <fieldset style="width:1020px;">
                    <legend>Demand For Accessories</legend>
                    <table border="0" cellpadding="0" cellspacing="2" id="tbl_btb">
                        <tr>
                            <td colspan="4" align="right" align="right">Demand Number</td>
                            <td colspan="4" >
                                <input style="width:140px;" type="text" title="Double Click to Search" onDblClick="openmypage_cs_no()" class="text_boxes" placeholder="Browse Demand Number" name="txt_system_id" id="txt_system_id" readonly />
                                <input type="hidden" name="update_id" id="update_id" />
                            </td>
                        </tr>
                        <tr>
                            <td width="80" class="must_entry_caption" align="right">Company</td>
                            <td >
                                <?  
								echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name", "id,company_name", 1, "Select Company", '', "print_button_setting();load_drop_down( 'requires/demand_for_accessories_controller', this.value, 'load_drop_down_buyer', 'buyer_td');"); 
								?>
                            </td>
                            <td width="80" class="must_entry_caption" align="right">Buyer</td>
                            <td id="buyer_td">
                            <?
                       		echo create_drop_down( "cbo_buyer_name", 150, "select id,buyer_name from lib_buyer  where status_active =1 and is_deleted=0  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ,0);
                           	?>
                            </td>
                            <td width="80" align="right">Season</td>
                            <td id="season_td"><? echo create_drop_down( "cbo_season_name", 150, $blank_array,"", 1, "-- Select Season --", $selected, "" ); ?></td>
                            <td width="80" align="right">Year</td>
                            <td><? echo create_drop_down( "cbo_season_year", 150, create_year_array(),"", 1,"-All-", 1, "",0,"" ); ?></td>
                        </tr>
                        <tr>
                            <td align="right">Brand</td>
                            <td id="brand_td"><? echo create_drop_down( "cbo_brand", 150, $blank_array,"",1, "-Brand-", $selected,""); ?></td>
                            <td class="must_entry_caption" align="right">Team Leader</td>
                            <td>
                            <? 
							echo create_drop_down( "cbo_team_leader", 150, "select id, team_leader_name from lib_marketing_team where project_type=2 and status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-Select Team-", $selected, "load_drop_down( 'requires/demand_for_accessories_controller', this.value, 'load_drop_down_dealing_merchant', 'div_marchant');" ); 
							?>
                            </td>
                            <td class="must_entry_caption" align="right">Deling Merchant</td>
                            <td id="div_marchant">
                            <? 
							echo create_drop_down( "cbo_dealing_merchant",150, "select b.id,b.team_member_name from lib_marketing_team a, lib_mkt_team_member_info b where a.id=b.team_id and a.PROJECT_TYPE=2 and b.status_active =1 and a.is_deleted=0 and a.status_active =1 and b.is_deleted=0 order by b.team_member_name","id,team_member_name", 1, "-- Select Merchant --", $selected, "" ); //a.lib_mkt_team_member_info_id=b.id and 
							?>
                            </td>
                            <td class="must_entry_caption" align="right">Style Ref</td>
                            <td>
                            <input name="txt_style_no" id="txt_style_no" style="width:140px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/demand_for_accessories_controller.php?action=order_popup','Job/Order Selection Form')" class="text_boxes" placeholder="Browse Style No." readonly />
                    		<input type="hidden" id="hidd_job_id" name="hidd_job_id" />
                      		<input type="hidden" id="hidd_job_sl_no" name="hidd_job_sl_no" />
                      		<input type="hidden" id="hidd_txt_style_no" name="hidd_txt_style_no" />
                            <input type="hidden" name="cs_generate_check" id="cs_generate_check" value="1" />
                            </td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption" align="right">Demand Date</td>
                            <td><input name="txt_demand_date" style="width:140px"  id="txt_demand_date" placeholder="Select Date" class="datepicker" type="text" value="" /></td>
                            <td align="right">CS Required Date</td>
                            <td><input name="txt_cs_req_date" style="width:140px"  id="txt_cs_req_date" placeholder="Select Date" class="datepicker" type="text" value="" /></td>
                            <td align="right">Remarks</td>
                            <td colspan="5">
                            <input type="text" class="text_boxes" id="txt_remarks" name="txt_remarks" style="width:300px" >
                            <input type="button" class="formbutton" id="generate_cs" value="Generate" onClick="fnc_generate_demand()" style="width:70px" ></td>
                        </tr>
                    </table>
                    <div id="cs_tbl"></div>
                    <table width="1000" border="0" cellpadding="0" cellspacing="2" id="tbl_btb">
                        <tr>
                            <td colspan="8" width="100%" align="center"> 
                            <?  echo load_submit_buttons( $permission, "fnc_comparative_statement", 0,0,"form_reset_cs(2)",1) ;?>
                            <input type="hidden" id="prev_req_dtls_id" name="prev_req_dtls_id" />
                            </td>  
                        </tr>
                    </table>
					<? echo create_drop_down( "cbo_template_id", 100, $report_template_list,'', 0, '', 0); ?>
					<input class="formbutton" type="button" onClick="sendMail()" value="Mail Send" style="width:80px;">
					<span id="button_data_panel"></span>
                </fieldset>
            </form>
        </div>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>