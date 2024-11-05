<?
    /*-------------------------------------------- Comments
    Purpose			: 	This form will create Basic Dayes and Chemical Summery Report

    Functionality	:
    JS Functions	:
    Created by		:	Mohammad Shafiqur Rahman
    Creation date 	: 	22-05-2018
    Updated by 		:
    Update date		:
    QC Performed BY	:
    QC Date			:
    Comments		:
    */

    session_start();
    if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

    require_once('../../../includes/common.php');
    extract($_REQUEST);
    $_SESSION['page_permission']=$permission;
    //--------------------------------------------------------------------------------------------------------------------
    echo load_html_head_contents("Item Wise Summery Report","../../../", 1, 1, $unicode,1,1);


?>
<script>
    var permission='<? echo $permission; ?>';
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

    var tableFilters =
            {
                //col_8: "none",
                col_operation: {
                    id: ["value_tot_rcv_qty","value_tot_amount_qty"],
                    col: [6,8],
                    operation: ["sum","sum"],
                    write_method: ["innerHTML","innerHTML"]
                }
            }
    var tableFilters_issue =
            {
                //col_8: "none",
                col_operation: {
                    id: ["value_tot_issue_qty","value_tot_amnt_qty"],
                    col: [7,9],
                    operation: ["sum","sum"],
                    write_method: ["innerHTML","innerHTML"]
                }
            }
    var tableFilters_all =
            {
                //col_8: "none",
                col_operation: {
                    id: ["value_tot_rcv_qty","value_tot_rcv_amnt_qty","value_tot_issue_qty","value_tot_issue_amnt_qty"],
                    col: [5,7,8,10],
                    operation: ["sum","sum","sum","sum"],
                    write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
                }
            }

    function generate_report(report_type)
    {
        if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date Range')==false )
        {
            return;
        }
        var report_title=$( "div.form_caption" ).html();
        var cbo_company_name = $("#cbo_company_name").val();
        var cbo_item_category_id = $("#cbo_item_category_id").val();
        var item_group_id = $("#txt_item_group_id").val();
        var txt_date_from = $("#txt_date_from").val();
        var txt_date_to = $("#txt_date_to").val();
        var txt_item_account_id = $("#txt_item_account_id").val();
        var txt_item_acc = $("#txt_item_acc").val();

        var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_item_category_id="+cbo_item_category_id+"&txt_date_from="+txt_date_from+"&item_group_id="+item_group_id+"&txt_date_to="+txt_date_to+"&report_title="+report_title+"&txt_item_account_id="+txt_item_account_id+"&txt_item_acc="+txt_item_acc;

        if(report_type==="receive"){

            var data="action=generate_report_receive"+dataString;
            //alert (data); return;
            freeze_window(3);
            http.open("POST","requires/dyes_cmcl_item_wise_summery_report_controller.php",true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = generate_report_receive_response;

        }else if(report_type==="issue"){

            var data="action=generate_report_issue"+dataString;
            //alert (data); return;
            freeze_window(3);
            http.open("POST","requires/dyes_cmcl_item_wise_summery_report_controller.php",true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = generate_report_issue_response;
        }else{
            var data="action=generate_report_all"+dataString;
            //alert (data); return;
            freeze_window(3);
            http.open("POST","requires/dyes_cmcl_item_wise_summery_report_controller.php",true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = generate_report_all_response;
        }

    }

    function generate_report_receive_response()
    {
        if(http.readyState == 4)
        {
            var reponse=trim(http.responseText).split("**");
            $("#report_container2").html(reponse[0]);
            document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

            setFilterGrid("table_body_id",-1,tableFilters);

            show_msg('3');
            release_freezing();
        }
    }


    function generate_report_issue_response()
    {
        if(http.readyState == 4)
        {
            var reponse=trim(http.responseText).split("**");
            $("#report_container2").html(reponse[0]);
            document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

            setFilterGrid("table_body_id",-1,tableFilters_issue);

            show_msg('3');
            release_freezing();
        }
    }

    function generate_report_all_response()
    {
        if(http.readyState == 4)
        {
            var reponse=trim(http.responseText).split("**");
            $("#report_container2").html(reponse[0]);
            document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

            setFilterGrid("table_body_id",-1,tableFilters_all);

            show_msg('3');
            release_freezing();
        }
    }

    function new_window(type)
    {
        if(type == 1)
        {
            document.getElementById('scroll_body').style.overflow="auto";
            document.getElementById('scroll_body').style.maxHeight="none";
            $('#table_body_id tr:first').hide();
            //$('#rpt_table_header tr th:last').attr('width', 120);
            //$('#table_body_id tr td:last').attr('width', 100);
            //$('#table_body_footer tr th:last').attr('width', 120);
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
                     '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
            d.close();
            $('#table_body_id tr:first').show();
            $('#rpt_table_header tr th:last').attr('width', '');
            $('#table_body_id tr td:last').attr('width', '');
            $('#table_body_footer tr th:last').attr('width', '');
            document.getElementById('scroll_body').style.overflowY="scroll";
            document.getElementById('scroll_body').style.maxHeight="250px";
        }
        else
        {
            document.getElementById('scroll_body').style.overflow="auto";
            document.getElementById('scroll_body').style.maxHeight="none";
            //$('#table_body_id tr:first').hide();
            //$('#rpt_table_header tr th:last').attr('width', 120);
            //$('#table_body_id tr td:last').attr('width', 100);
            //$('#table_body_footer tr th:last').attr('width', 120);
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
                     '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
            d.close();
            //$('#table_body_id tr:first').show();
            //$('#rpt_table_header tr th:last').attr('width', '');
            //$('#table_body_id tr td:last').attr('width', '');
            //$('#table_body_footer tr th:last').attr('width', '');
            document.getElementById('scroll_body').style.overflow="auto";
            document.getElementById('scroll_body').style.maxHeight="250px";
        }
    }

    function change_color(v_id,e_color)
    {
        if (document.getElementById(v_id).bgColor=="#33CC00")
        {
            document.getElementById(v_id).bgColor=e_color;
        }
        else
        {
            document.getElementById(v_id).bgColor="#33CC00";
        }
    }

    function openmypage_item_group()
    {
        var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_item_category_id').value;
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/dyes_cmcl_item_wise_summery_report_controller' +
                                                         '.php?action=item_group_popup&data='+data,
                                    'Item Group Popup', 'width=520px,height=380px,center=1,resize=0,scrolling=0','../../')

        emailwindow.onclose=function()
        {
            var theemail=this.contentDoc.getElementById("item_name_id");
            var response=theemail.value.split('_');
            //alert (response[1]);
            if (theemail.value!="")
            {
                //freeze_window(5);
                document.getElementById("txt_item_group_id").value=response[0];
                document.getElementById("txt_item_group").value=response[1];
                release_freezing();
            }
        }
    }

    function receive_qnty_dtls(prod_id, transaction_date, action)
    {
        var width=460;
        //alert(prod_id+"__"+transaction_date+"__"+action); return;
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/dyes_cmcl_item_wise_summery_report_controller' +
        '.php?action='+action+'&prod_id='+prod_id+'&transaction_date='+transaction_date, 'Receive Quantity Details', 'width='+width+',' +
                                                                                  'height=400px, center=1,resize=0,scrolling=0','../../');
    }
    function issue_qnty_dtls(prod_id, transaction_date, action)
    {
        var width=460;
        //alert(prod_id+"__"+transaction_date+"__"+action); return;
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/dyes_cmcl_item_wise_summery_report_controller' +
                                                          '.php?action='+action+'&prod_id='+prod_id+'&transaction_date='+transaction_date, 'Issue ' +
                                                                                                                                           'Quantity Details', 'width='+width+',' +
                                                                                                                                                                       'height=400px, center=1,resize=0,scrolling=0','../../');
    }

    function openmypage_item_account()
	{
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_item_category_id').value+"_"+document.getElementById('txt_item_group').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/dyes_cmcl_item_wise_summery_report_controller.php?action=item_account_popup&data='+data,'Item Account Popup', 'width=800px,height=520px,center=1,resize=0','../../')		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("item_account_id");
			var theemailv=this.contentDoc.getElementById("item_account_val");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{

				//freeze_window(5);
				document.getElementById("txt_item_account_id").value=response[0];
			    document.getElementById("txt_item_acc").value=theemailv.value;
				//reset_form();
				//get_php_form_data( response[0], "item_account_dtls_popup", "requires/dyes_and_cmcl_store_wise_stock_report_controller" );
				release_freezing();
			}
		}
	}

</script>
</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?><br />
    <form name="dyes_cmcl_item_wise_smry_rpt" id="dyes_cmcl_item_wise_smry_rpt" autocomplete="off" >
        <h3 style="width:1055px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')">
            -Search Panel</h3>
        <div id="content_search_panel" style="width:1050px">
            <fieldset>
                <table class="rpt_table" width="1050" cellpadding="0" cellspacing="0" border="1" rull="all">
                    <thead>
                    <th width="160" class="must_entry_caption">Company</th>
                    <th width="160">Item Category</th>
                    <th width="130">Item Group</th>
                    <th width="120">Item Account</th>
                    <th colspan="2" width="90" class="must_entry_caption">Date Range</th>
                    <th>
                        <input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('dyes_cmcl_item_wise_smry_rpt','report_container*report_container2','','','')" />


                    </th>
                    </thead>
                    <tbody>
                    <tr class="general">
                        <td>
                            <?
                                echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 0, "", $selected, "" );
                            ?>
                        </td>
                        <td id="cat_td">
                            <?php
                                echo create_drop_down( "cbo_item_category_id", 150,$item_category,"", 0, "", $selected, "","","5,6,7,19,20,22,23,39","","","");
                            ?>
                        </td>
                        <td>
                            <input style="width:110px;" name="txt_item_group" id="txt_item_group" class="text_boxes" onDblClick="openmypage_item_group()" placeholder="Browse" readonly />
                            <input type="hidden" name="txt_item_group_id" id="txt_item_group_id" style="width:90px;"/>
                        </td>
                        <td>
                            <input style="width:110px;" name="txt_item_acc" id="txt_item_acc" class="text_boxes" onDblClick="openmypage_item_account()" placeholder="Browse" readonly />
                            <input type="hidden" name="txt_item_account_id" id="txt_item_account_id" style="width:90px;"/>  
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" value="<? //echo date("d-m-Y");?>" class="datepicker"
                                   style="width:100px;"/>
                        </td>
                        <td>
                            <input type="text" name="txt_date_to" id="txt_date_to" value="<? //echo date("d-m-Y");?>" class="datepicker"
                                   style="width:100px;"/>
                        </td>
                        <td>
                            <input type="button" name="receive" id="receive" value="Receive" onClick="generate_report(this.id)"
                                   style="width:70px"
                                   class="formbutton" />
                            <input type="button" name="issue" id="issue" value="Issue" onClick="generate_report(this.id)" style="width:70px"
                                   class="formbutton" />
                            <input type="button" name="all" id="all" value="All" style="width:70px" class="formbutton"
                                   onClick="generate_report(this.id)" />
                        </td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="9" align="left">
                            <? echo load_month_buttons(1);  ?>&nbsp;&nbsp;
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </fieldset>
        </div>
        <div id="report_container" align="center" style="width:1150px;"></div>
        <div id="report_container2"></div>
    </form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
    set_multiselect('cbo_company_name*cbo_item_category_id','0*0','0*0','','0*0');
</script>
</html>
