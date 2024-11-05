<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create for Comparative Statement [General]
Functionality	:
JS Functions	:
Created by		:	Nayem
Creation date 	: 	16-3-2021
Updated by 		: 	Rakib
Update date		: 	
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
echo load_html_head_contents("Comparative Statement", "../../",  1, 1, $unicode,1,'');

?>
<script src="../../js/gridviewscroll.js" type="text/javascript"></script> 
<style type="text/css">        
    .GridViewScrollHeader TH, .GridViewScrollHeader TD {padding: 2px 4px;font-weight: normal; white-space: nowrap; border-right: 1px solid #e6e6e6; border-bottom: 1px solid #e6e6e6; color: #000000; text-align: left; vertical-align: middle;}
    .GridViewScrollHeader TH{ background-image: -webkit-linear-gradient( rgb(194,220,255) 10%, rgb(136,170,214) 96%); border: 1px solid #8DAFDA; color:#444; font-size: 13px; font-weight: bold; text-align: center; line-height: 12px; height: 25px;}
    .GridViewScrollItem TD { padding: 2px 4px; white-space: nowrap; border-right: 1px solid #e6e6e6; border-bottom: 1px solid #e6e6e6; color: #000000; vertical-align: middle;}
    .GridViewScrollItemFreeze TD {padding: 2px 4px; white-space: nowrap; border-right: 1px solid #e6e6e6; border-bottom: 1px solid #e6e6e6; color: #000000; vertical-align: middle;}
    .GridViewScrollFooterFreeze TD {padding: 2px 4px; white-space: nowrap; border-right: 1px solid #e6e6e6; border-top: 1px solid #e6e6e6; border-bottom: 1px solid #e6e6e6; color: #000000; vertical-align: middle; font-weight: 700; background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #F0F0F0), color-stop(100%, #DBDBDB));}
    tr.GridViewScrollItemFreeze:last-child TD{ background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #F0F0F0), color-stop(100%, #DBDBDB)); }
    tr.footerTr:last-child TD{ background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #F0F0F0), color-stop(100%, #DBDBDB)); }
    html { --scrollbarBG: #CFD8DC; --thumbBG: #8ec5fc;}
    body::-webkit-scrollbar { width: 11px; }
    body { scrollbar-width: thin; scrollbar-color: var(--thumbBG) var(--scrollbarBG);}
    body::-webkit-scrollbar-track { background: var(--scrollbarBG);}
    body::-webkit-scrollbar-thumb { background-color: var(--thumbBG);border-radius: 6px;border: 3px solid var(--scrollbarBG);}
</style>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
    var permission='<? echo $permission; ?>';
    function openmypage_supplier()
	{
		var supplier_id = $('#supplier_id').val();
		var title = 'Supplier Name';	
		var page_link = 'requires/comparative_statement_controller.php?supplier_id='+supplier_id+'&action=supplier_name_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var supplier_id=this.contentDoc.getElementById("hidden_supplier_id").value;	
			var supplier_name=this.contentDoc.getElementById("hidden_supplier_name").value;
			$('#supplier_id').val(supplier_id);
			$('#txt_supplier_name').val(supplier_name);
		}
	}

    function openmypage_requisition_no() 
    {
        if( form_validation('cbo_basis_name','Basis')==false )
		{
			return;
		}
        var basis_id = $('#cbo_basis_name').val();
        if(basis_id==1)
		{
            var title = 'Requisition No';
			var txt_requisition_mst=$('#txt_requisition_mst').val();
			var txt_requisition_dtls=$('#prev_req_dtls_id').val();
			var update_id=$('#update_id').val();
            var page_link = 'requires/comparative_statement_controller.php?action=requisition_popup&txt_requisition_mst='+txt_requisition_mst+'&txt_requisition_dtls='+txt_requisition_dtls+'&update_id='+update_id;
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1130px,height=450px,center=1,resize=1,scrolling=0','../');
            emailwindow.onclose=function()
            {
                var theform=this.contentDoc.forms[0];
                var requisition_no=this.contentDoc.getElementById("hidden_req_no").value;	
                var requisition_id=this.contentDoc.getElementById("hidden_req_id").value;	
                var requisition_dtls=this.contentDoc.getElementById("hidden_req_dtls_id").value;
                var req_no_arr = requisition_no.split(',');
                var req = $.unique(req_no_arr);

                $('#txt_requisition').val(req);
                $('#txt_requisition_mst').val(requisition_id);
                $('#txt_requisition_dtls').val(requisition_dtls);
				$('#cs_generate_check').val(0);
				
            }
        }
        if(basis_id==2){
            var title = 'Item No';
			var txt_requisition_mst=$('#txt_requisition_mst').val();
			var txt_requisition_dtls=$('#prev_req_dtls_id').val();
			var update_id=$('#update_id').val();
            var page_link = 'requires/comparative_statement_controller.php?action=item_popup&txt_requisition_mst='+txt_requisition_mst+'&txt_requisition_dtls='+txt_requisition_dtls+'&update_id='+update_id;
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=450px,center=1,resize=1,scrolling=0','../');
            emailwindow.onclose=function()
            {
                var theform=this.contentDoc.forms[0];
                var item_no=this.contentDoc.getElementById("hidden_item_no").value;	
                var item_id=this.contentDoc.getElementById("hidden_item_id").value;	
                var item_dtls=this.contentDoc.getElementById("hidden_item_dtls_id").value;	

                var item_no_arr = item_no.split(',');
                var item = $.unique(item_no_arr);

                $('#txt_requisition').val(item);
                $('#txt_requisition_mst').val(item_id);
                $('#txt_requisition_dtls').val(item_dtls);
            }
        }
    }

    function fnc_generate_cs() 
    {
        if( form_validation('cbo_basis_name*txt_requisition*txt_supplier_name','Basis*Requisition No/Item*Supplier')==false )
		{
			return;
		}
		var update_id=$("#update_id").val();
		var txt_requisition_dtls=$("#prev_req_dtls_id").val();
        show_list_view(document.getElementById('cbo_basis_name').value+'**'+document.getElementById('txt_requisition_mst').value+'**'+document.getElementById('txt_requisition_dtls').value+'**'+document.getElementById('supplier_id').value+'**'+update_id+'**'+txt_requisition_dtls, 'load_cs_table', 'cs_tbl', 'requires/comparative_statement_controller', 'setFilterGrid(\'cs_tbl\',-1)');

        show_list_view(document.getElementById('cbo_basis_name').value+'**'+document.getElementById('txt_requisition_mst').value+'**'+document.getElementById('txt_requisition_dtls').value+'**'+document.getElementById('supplier_id').value, 'load_statment_table', 'statment_tbl', 'requires/comparative_statement_controller', 'setFilterGrid(\'statment_tbl\',-1)');
		$('#cs_generate_check').val(1);
        
        var external_js_file = document.createElement('script');
        external_js_file.type = 'text/javascript';
        external_js_file.async = true;
        external_js_file.src = '../../js/gridviewscroll.js';
        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(external_js_file);

        var gridViewScroll = null;
        fnGridView = function () {
            gridViewScroll = new GridViewScroll({
                elementID: "gvMain",
                width: screen.width-30,
                // width: 1335,
                height: 380,
                freezeColumn: true,
                //freezeFooter: true,
                freezeColumnCssClass: "GridViewScrollItemFreeze",
                //freezeFooterCssClass: "GridViewScrollFooterFreeze",
                freezeHeaderRowCount: 2,
                freezeColumnCount: 10,
                onscroll: function (scrollTop, scrollLeft) {
                    console.log(scrollTop + " - " + scrollLeft);
                }
            });
            gridViewScroll.enhance();
        }
        function getScrollPosition() {
            var position = gridViewScroll.scrollPosition;
            alert("scrollTop: " + position.scrollTop + ", scrollLeft: " + position.scrollLeft);
        }
        function setScrollPosition() {
            var scrollPosition = { scrollTop: 50, scrollLeft: 50};
            gridViewScroll.scrollPosition = scrollPosition;
        }
        fnGridView();
    }

    function Fn_Report_Btn(){
        var update_id=$('#update_id').val();
            if(update_id==0){
                alert("SAVE FIRST");return;
            }
            var form_caption=$( "div.form_caption" ).html();
	 	    print_report( $('#update_id').val()+'*'+form_caption+'*'+$('#txt_system_id').val() +'*'+$('#cbo_template_id').val(), "comparative_statement_print2", "requires/comparative_statement_controller" )
	 	    return;
    }

    function fnc_comparative_statement(operation)
    {
        if(operation==4){
            var form_caption=$( "div.form_caption" ).html();
	 	    print_report( $('#update_id').val()+'*'+form_caption+'*'+$('#txt_system_id').val() +'*'+$('#cbo_template_id').val(), "comparative_statement_print", "requires/comparative_statement_controller" )
	 	    return;
        }
		var cs_generate_check=$('#cs_generate_check').val()*1;
		
		if(cs_generate_check==0)
		{
			alert("Please Press Generate CS Button");return;
		}

        if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][481]);?>'){
            if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][481]);?>','<? echo implode('*',$_SESSION['logic_erp']['mandatory_message'][481]);?>')==false)
            {
                return;
            }
        }
		
        if( form_validation('cbo_basis_name*txt_requisition*txt_cs_date*txt_supplier_name*cbo_currency_name*txt_validity_date*cbo_company_name','Basis*Requisition No/Item*CS Date*Supplier*Currency*CS Validity*Applicable Company')==false )
		{
			return;
		}
        if(date_compare($('#txt_cs_date').val(), $('#txt_validity_date').val())==false)
		{
			alert("CS Validity Date Can not Be Less Than CS Date");
			return;
		}
		/*var cbo_ready_to_approved=$("#cbo_ready_to_approved").val();
		if(operation==2 && cbo_ready_to_approved==1)
		{
			alert("CS Approved, Delete Not Allow");return;
		}*/
		
		/*var prev_req_dtls_id=('#prev_req_dtls_id').val();
		var txt_requisition_dtls=('#txt_requisition_dtls').val();
		var update_id=('#update_id').val();
		if(update_id!="" && prev_req_dtls_id !="" txt_requisition_dtls)*/

        var row_num=$('#gvMain tbody tr').length;
        var data_dtls="";
        var supplier_id = $('#supplier_id').val();
        var col_num_arr = supplier_id.split(',');
        var col_num = col_num_arr.length; 
        var data_supplier="";
        if(row_num==0){
            alert("Please Click Genarate CS");
			return;
        }
        for (var i=1; i<=row_num; i++)
		{
            data_dtls += '&txtprod_' + i + '=' + $('#txtprod_'+i).val() + '&txtcategory_' + i + '=' + $('#txtcategory_'+i).val() + '&txtgroup_' + i + '=' + $('#txtgroup_'+i).attr('title') + '&txtqty_' + i + '=' + $('#txtqty_'+i).val()+ '&txtcsbrand_' + i + '=' + $('#txtcsbrand_'+i).val()+ '&txtcsmodel_' + i + '=' + $('#txtcsmodel_'+i).val()+ '&txtcsorigin_' + i + '=' + $('#txtcsorigin_'+i).val();
            //var quoted_validation=0;
            //var neg_validation=0;
            //var con_validation=0;
            for (var m=0; m<col_num; m++)
            {
                var mm=col_num_arr[m];
                data_supplier += '&txtsuppier_' + i + '_' + mm + '=' + mm + '&txtquoted_' + i + '_' + mm + '=' + $('#txtquoted_'+i+'_'+mm).val() + '&txtneg_' + i + '_' + mm + '=' + $('#txtneg_'+i+'_'+mm).val()+ '&txtcon_' + i + '_' + mm + '=' + $('#txtcon_'+i+'_'+mm).val() + '&txtbrand_' + i + '_' + mm + '=' + $('#txtbrand_'+i+'_'+mm).val() + '&txtmodel_' + i + '_' + mm + '=' + $('#txtmodel_'+i+'_'+mm).val() + '&txtPayTerm_' + i + '_' + mm + '=' + $('#txtPayTerm_'+i+'_'+mm).val() + '&txtTenor_' + i + '_' + mm + '=' + $('#txtTenor_'+i+'_'+mm).val() + '&txtorigin_' + i + '_' + mm + '=' + $('#txtorigin_'+i+'_'+mm).val()+ '&txtWorranty_' + i + '_' + mm + '=' + $('#txtWorranty_'+i+'_'+mm).val()+ '&txtIncoTram_' + i + '_' + mm + '=' + $('#txtIncoTram_'+i+'_'+mm).val()+ '&txtspecification_' + i + '_' + mm + '=' + $('#txtspecification_'+i+'_'+mm).val();
				
                //quoted_validation += +$('#txtquoted_'+i+'_'+mm).val();
                //neg_validation += +$('#txtneg_'+i+'_'+mm).val();
                //con_validation += +$('#txtcon_'+i+'_'+mm).val();
            }
			
            /* if(quoted_validation==0)
			{
                alert("Please Fill the Price");
                return;
                || neg_validation==0 || con_validation==0
            } */
        }
        // console.log(data_supplier);
		// alert(data_dtls);return;
		//alert(data_supplier);return;
        //var data_mst=get_submitted_data_string('cbo_basis_name*txt_requisition*txt_requisition_mst*txt_requisition_dtls*txt_rcvd_date*txt_cs_date*supplier_id*cbo_currency_name*txt_validity_date*cbo_source*cbo_ready_to_approved*cbo_company_name*txt_comments*update_id*txt_system_id',"../../");
        var data="action=save_update_delete&operation="+operation+"&row_num="+row_num+"&data_dtls="+data_dtls+"&col_num="+col_num+"&data_supplier="+data_supplier+get_submitted_data_string('cbo_basis_name*txt_requisition*txt_requisition_mst*txt_requisition_dtls*txt_rcvd_date*txt_cs_date*supplier_id*cbo_currency_name*txt_validity_date*cbo_source*cbo_ready_to_approved*cbo_company_name*txt_comments*update_id*txt_system_id',"../../");
        //alert(data);return;
        freeze_window(operation);
        http.open("POST","requires/comparative_statement_controller.php",true);
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
			if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1)
			{
				document.getElementById('txt_system_id').value=reponse[1];
				document.getElementById('update_id').value=reponse[2];
				document.getElementById('prev_req_dtls_id').value=reponse[3];
				$('#cbo_basis_name').attr('disabled',true);
                //$('#generate_cs').attr('disabled',true);
				set_button_status(1, permission, 'fnc_comparative_statement',1);
				
			}
			if(parseInt(trim(reponse[0]))==2)
			{
				$("#comparativestatement_1").find('input[type="text"],input[type="hidden"]').val('');
				$("#comparativestatement_1").find('select:not([name="cbo_basis_name"])').val(0);
				$('#cbo_basis_name').attr('disabled',false);
                set_button_status(0, permission, 'fnc_comparative_statement',1);
			}
			if(parseInt(trim(reponse[0]))==11)
			{
				alert(trim(reponse[1]));release_freezing();return;
			}
			show_msg(trim(reponse[0]));
			release_freezing();
		}
    }
	
    function openmypage_cs_no()
	{
        var page_link='requires/comparative_statement_controller.php?action=system_popup';
        var title='Search CS PopUp';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=430px,center=1,resize=0,scrolling=0','../')
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var theemail=this.contentDoc.getElementById("selected_id");
            if (theemail.value!="")
            {
                freeze_window(5);
                get_php_form_data(theemail.value, "populate_data_from_search_popup", "requires/comparative_statement_controller" );
                //$('#generate_cs').attr('disabled',true);
				$('#cbo_basis_name').attr('disabled',true);
                set_button_status(1, permission, 'fnc_comparative_statement',1);

                var external_js_file = document.createElement('script');
                external_js_file.type = 'text/javascript';
                external_js_file.async = true;
                external_js_file.src = '../../js/gridviewscroll.js';
                (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(external_js_file);

                var gridViewScroll = null;
                fnGridView = function () {
                    gridViewScroll = new GridViewScroll({
                        elementID: "gvMain",
                        width: screen.width-30,
                        // width: 1335,
                        height: 380,
                        freezeColumn: true,
                        //freezeFooter: true,
                        freezeColumnCssClass: "GridViewScrollItemFreeze",
                        //freezeFooterCssClass: "GridViewScrollFooterFreeze",
                        freezeHeaderRowCount: 2,
                        freezeColumnCount: 10,
                        onscroll: function (scrollTop, scrollLeft) {
                            console.log(scrollTop + " - " + scrollLeft);
                        }
                    });
                    gridViewScroll.enhance();
                }
                function getScrollPosition() {
                    var position = gridViewScroll.scrollPosition;
                    alert("scrollTop: " + position.scrollTop + ", scrollLeft: " + position.scrollLeft);
                }
                function setScrollPosition() {
                    var scrollPosition = { scrollTop: 50, scrollLeft: 50};
                    gridViewScroll.scrollPosition = scrollPosition;
                }
                fnGridView();
                
                release_freezing();
            }
        }
	}

    function openmypage_estimated_price()
    {
        var mst_id=$('#update_id').val();
        if ( mst_id=='')
		{
			alert ('CS Number Not Select.');
			return;
		}
        var page_link='requires/comparative_statement_controller.php?action=estimated_price_popup&mst_id='+mst_id;
        var title='Estimated Price PopUp';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=430px,center=1,resize=0,scrolling=0','../')
        emailwindow.onclose=function()
        {
            var theemailDtlsID=this.contentDoc.getElementById("hidden_dtlsid").value;
			var theemailsuppID=this.contentDoc.getElementById("hidden_suppid").value;
            var theemailsuppdata=this.contentDoc.getElementById("hidden_supp_data").value;          
            var form_caption=$( "div.form_caption" ).html();
            print_report( $('#update_id').val()+'*'+form_caption+'*'+$('#txt_system_id').val()+'*'+$('#cbo_template_id').val()+'*'+theemailDtlsID+'*'+theemailsuppID+'*'+theemailsuppdata, "print_report_estimated_price", "requires/comparative_statement_controller" );
        }
    }

	function form_reset_cs(str)
	{
		if(str==1)
		{
			$("#comparativestatement_1").find('input[type="text"],input[type="hidden"]:not([name="cs_generate_check"])').val('');
			$("#comparativestatement_1").find('select:not([name="cbo_basis_name"])').val(0);
			$("#cs_tbl").html("");
			$("#statment_tbl").html("");
			$('#cbo_basis_name').attr('disabled',false);
			set_button_status(1, permission, 'fnc_comparative_statement',0);
		}
		else
		{
			$("#comparativestatement_1").find('input[type="text"],input[type="hidden"]:not([name="cs_generate_check"])').val('');
			$("#comparativestatement_1").find('select').val(0);
			$("#cs_tbl").html("");
			$("#statment_tbl").html("");
			$('#cbo_basis_name').attr('disabled',false);
			set_button_status(1, permission, 'fnc_comparative_statement',0);
		}
	}

    function fn_report_generate(type)
    {
        if ( $('#update_id').val()=='')
		{
			alert ('CS Number Not Select.');
			return;
		}
        if (type==1)
		{	
            if (confirm('Press  OK to open  with Last Purchase History\n Press  Cancel to open  without Last Purchase History')) {
                zero_value=0;
            } else {
                zero_value=1
            }
		    var form_caption=$( "div.form_caption" ).html();			
            print_report( $('#update_id').val()+'*'+form_caption+'*'+$('#txt_system_id').val()+'*'+zero_value+'*'+$('#cbo_template_id').val(), "print_report_generate", "requires/comparative_statement_controller" );
            show_msg( "3" );
		}
        else if (type==2)
        {
            var form_caption=$( "div.form_caption" ).html();			
            print_report( $('#update_id').val()+'*'+form_caption+'*'+$('#txt_system_id').val()+'*'+$('#cbo_template_id').val(), "print_report_estimated_price", "requires/comparative_statement_controller" );
            show_msg( "3" );
        }
    }

    function uploadFile(mst_id){
        $(document).ready(function() {
            var fd = new FormData();
            var files = $('#statement_mst_file')[0].files; 
            for (let i = 0; i < files.length; i++) {
                    fd.append('file[]',files[i],files[i].name);
                }
            //fd.append('statement_mst_file',this.file_group_id);
            //fd.append('file', files); 
            $.ajax({ 
                url: 'requires/comparative_statement_controller.php?action=file_upload&mst_id='+ mst_id, 
                type: 'post', 
                data: fd,  
                contentType: false, 
                processData: false, 
                
                success: function(response){
                    if(response != 0){
                        document.getElementById('statement_mst_file').value=null;
                    } 
                    else{ 
                        alert('file not uploaded'); 
                    } 
                }, 
            }); 
        }); 
    }


    var fn_open_deny_comments = () =>{
        var update_id=$('#update_id').val();
        if ( update_id=='')
		{
			alert ('Please Select CS Number');
			return;
		}
        var page_link='requires/comparative_statement_controller.php?action=deny_cause_list&update_id='+update_id;
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Deny Case List', 'width=750px,height=430px,center=1,resize=0,scrolling=0','../')
        emailwindow.onclose=function()
        {

        }   
    }

</script>

<body onLoad="set_hotkey()">
    <div align="left">
        <? echo load_freeze_divs ("../../",$permission); ?>
        <div style="width:100%" >
            <form name="comparativestatement_1" id="comparativestatement_1" autocomplete="off">
                <fieldset style="width:1345px">
                    <legend>Comparative Statement</legend>
                    <table width="width:900" border="0" cellpadding="0" cellspacing="2" id="tbl_btb">
                        <tr>
                            <td colspan="4" align="right">CS Number</td>
                            <td colspan="4" >
                                <input style="width:140px;" type="text" title="Double Click to Search" onDblClick="openmypage_cs_no()" class="text_boxes" placeholder="Browse CS Number" name="txt_system_id" id="txt_system_id" readonly />
                                <input type="hidden" name="update_id" id="update_id" />
                            </td>
                        </tr>
                        <tr>
                            <td width="80" class="must_entry_caption">Basis</td>
                            <td >
                                <? echo create_drop_down( "cbo_basis_name",150,array(1=>"Requisition",2=>"Item"),'',1,'--Select--',0,"form_reset_cs(1)",0); ?>
                            </td>
                            <td width="120" class="must_entry_caption">Requisition No/Item</td>
                            <td >
                                <input style="width:140px;" type="text" title="Double Click to Search" onDblClick="openmypage_requisition_no()" class="text_boxes" placeholder="Browse Requisition" name="txt_requisition" id="txt_requisition" readonly />
                                <input type="hidden" name="txt_requisition_mst" id="txt_requisition_mst" />
                                <input type="hidden" name="txt_requisition_dtls" id="txt_requisition_dtls" />
                                <input type="hidden" name="cs_generate_check" id="cs_generate_check" value="1" />
                            </td>
                            <td width="120">Req. Rcvd Date</td>
                            <td >
                                <input style="width:140px " name="txt_rcvd_date" id="txt_rcvd_date" class="datepicker"  value="<?echo date('d-m-Y')?>" readonly />
                            </td>
                            <td width="80" class="must_entry_caption">CS Date</td>
                            <td ><input style="width:140px " name="txt_cs_date" id="txt_cs_date" class="datepicker" readonly /></td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption" >Supplier</td>
                            <td > 
                                <input type="text" name="txt_supplier_name" id="txt_supplier_name" class="text_boxes" style="width:140px;" placeholder="Double Click To Search" onDblClick="openmypage_supplier()" readonly />
                                <input type="hidden" name="supplier_id" id="supplier_id" /></td>
                            <td class="must_entry_caption" >Currency</td>
                            <td >
                                <? echo create_drop_down( "cbo_currency_name", 150, $currency,'',1,'-- Select Currency --',1,""); ?>
                            </td>
                            <td class="must_entry_caption" >CS Validity</td>
                            <td >
                                <input style="width:140px " name="txt_validity_date" id="txt_validity_date" class="datepicker" readonly />
                            </td>
                            <!-- <td >CS Type</td>
                            <td ><? echo create_drop_down( "cbo_cs_type", 150, $wo_type_array,'',1,'-- Select --',0,""); ?></td> -->
                        </tr>
                        <tr>
                            <td  >Source</td>
                            <td > <? echo create_drop_down( "cbo_source", 150, $source,"", 1, "-- Select --", 0, "",0 );?>
                            </td>
                            <td >Ready To Approved</td>
                            <td >
                                <? echo create_drop_down( "cbo_ready_to_approved", 150, $yes_no,'',1,'-- Select --',0,""); ?>
                            </td>
                            <td   class="must_entry_caption">Applicable Company</td>
                            <td >
                                <?  echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name", "id,company_name", 0, "", '', "get_php_form_data( this.value, 'company_wise_report_button_setting','requires/comparative_statement_controller');"); ?>
                            </td>
                            <td ></td>
                            <td ></td>
                        </tr>
                        <tr>
                            <td >Comments</td>
                            <td colspan="5" >
                                <input type="text" class="text_boxes" id="txt_comments" style="width:690px" >
                            </td>
                            <td colspan="2">
                                <input type="button" class="formbutton" id="generate_cs" value="Generate CS" onClick="fnc_generate_cs()" style="width:80px" >
                            </td>
                        </tr>
                        <tr>
                        <td  align="left">File</td>
                        <td align="left">
                            <input type="file" multiple id="statement_mst_file" class="image_uploader" style="width:150px" onChange="document.getElementById('txt_file').value=1">
                            <input type="hidden" multiple id="txt_file">
                        </td>
                        <td colspan="2">
                            <input type="button" id="image_button" class="image_uploader" style="width:75px;" value="IMAGE" onClick="file_uploader( '../../', document.getElementById('update_id').value,'', 'comparative_statement',1,1)" />

                            <input type="button" id="image_button" class="image_uploader" style="width:75px;" value="FILE" onClick="file_uploader( '../../', document.getElementById('update_id').value,'', 'comparative_statement',2,1)" />

                            <input onclick="fn_open_deny_comments()" type="button" value="View Deny Comments" class="image_uploader" style="width:150px;">
                        </td>
						<td align="center" height="10" colspan="4">
							<?
                            include("../../terms_condition/terms_condition.php");
                            terms_condition(481,'txt_system_id','../../');
                            ?>
                        </td>
					</tr>
                    </table>
                    <div id="cs_tbl" style="width:1340px;"></div>
                    <table width="1000" border="0" cellpadding="0" cellspacing="2" id="tbl_btb">
                        <tr>
                            <td colspan="8" height="20" width="100%" align="center"> 
                            <div id="approved" style="float:left; font-size:24px; color:#FF0000;"></div>
                            <? echo create_drop_down( "cbo_template_id", 100, $report_template_list,'', 0, '', 0, "");?>&nbsp;
                            <?  echo load_submit_buttons( $permission, "fnc_comparative_statement", 0,1,"form_reset_cs(2)",1) ;?>
                            <input type="hidden" id="prev_req_dtls_id" name="prev_req_dtls_id" />
                            <input type="button" style="width:80px;" id="show_button"  onClick="fn_report_generate(1)"   class="formbutton printReport" name="show_button" value="CHEM CS" />
                            <!-- <input type="button" style="width:100px;" id="estimated_price"  onClick="fn_report_generate(2)"   class="formbutton printReport" name="estimated_price" value="Estimated Price" /> -->
                            <input type="button" style="width:100px;" id="estimated_price"  onClick="openmypage_estimated_price()"   class="formbutton printReport" name="estimated_price" value="Estimated Price" />
                            <input type="button" style="width:80px;" id="show_button"  onClick="Fn_Report_Btn(1)"   class="formbutton printReport" name="show_button" value="Print 2" />
                            </td>  
                        </tr>
                    </table>                    
                </fieldset>
            </form>
            <div id="statment_tbl"></div>
        </div>
    </div>
</body>
<script>set_multiselect('cbo_company_name','0','0','','0');</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>