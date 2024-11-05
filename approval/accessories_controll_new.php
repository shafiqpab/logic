<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Grey Fabric Issue Status Report
Functionality	:
JS Functions	:
Created by		:	Aziz
Creation date 	: 	18-02-2014
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
echo load_html_head_contents("Trims Received Issue Stock Report","../", 1, 1, $unicode,1,1);
?>
<script>
    var permission='<? echo $permission; ?>';
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

    var tableFilters =
        {
            col_30: "none",
            col_operation: {
                id: ["tot_qnty"],
                col: [6],
                operation: ["sum"],
                write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
            }
        }

    function openmypage_style()
    {
        if( form_validation('cbo_company_id','Company Name')==false )
        {
            return;
        }
        var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_id").val()+"_"+$("#cbo_year").val();
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/accessories_controll_new_controller.php?data='+data+'&action=style_popup', 'style Search', 'width=480px,height=420px,center=1,resize=0,scrolling=0','../../')
        emailwindow.onclose=function()
        {
            var theemailid=this.contentDoc.getElementById("txt_po_id");
            var theemailval=this.contentDoc.getElementById("txt_po_val");
            if (theemailid.value!="" || theemailval.value!="")
            {
                //alert (theemailid.value);
                freeze_window(5);
                $("#txt_style").val(theemailid.value);
                $("#txt_style_id").val(theemailval.value);
                release_freezing();
            }
        }
    }
	
    function openmypage_job()
    {
        if( form_validation('cbo_company_id','Company Name')==false )
        {
            return;
        }
        var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_id").val()+"_"+$("#cbo_year").val();
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/accessories_controll_new_controller.php?data='+data+'&action=job_popup', 'Job Number Search', 'width=480px,height=420px,center=1,resize=0,scrolling=0','../../')
        emailwindow.onclose=function()
        {
            var theemailid=this.contentDoc.getElementById("txt_job_id");
            var theemailval=this.contentDoc.getElementById("txt_job");
            if (theemailid.value!="" || theemailval.value!="")
            {
                //alert (theemailid.value);
                freeze_window(5);
                $("#txt_job_id").val(theemailid.value);
                $("#txt_job").val(theemailval.value);
                release_freezing();
            }
        }
    }
	
    function openmypage_order()
    {
        if( form_validation('cbo_company_id','Company Name')==false )
        {
            return;
        }

        var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_id").val()+"_"+$("#txt_style").val()+"_"+$("#cbo_year").val();
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/accessories_controll_new_controller.php?data='+data+'&action=order_no_popup', 'Order No Search', 'width=600px,height=400px,center=1,resize=0,scrolling=0','../')
        emailwindow.onclose=function()
        {
            var theemailid=this.contentDoc.getElementById("txt_po_id");
            var theemailval=this.contentDoc.getElementById("txt_po_val");
            if (theemailid.value!="" || theemailval.value!="")
            {
                //alert (theemailid.value);
                freeze_window(5);
                $("#txt_order_no_id").val(theemailid.value);
                $("#txt_order_no").val(theemailval.value);
                release_freezing();
            }
        }
    }
	

    function fn_report_generated()
    {
        var style=document.getElementById('txt_style').value;
        var order_no=document.getElementById('txt_order_no').value;
        //txt_int_ref_no,txt_file_no
        if(style!="" || order_no!="")
        {
            if(form_validation('cbo_company_id','Company')==false)
            {
                return;
            }
        }
        else
        {
            if(form_validation('cbo_company_id','Company')==false)
            {
                return;
            }
        }
        var report_title=$( "div.form_caption" ).html();
		
		 var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_year*txt_job*txt_job_id*txt_style_id*txt_style*txt_order_no*txt_order_no_id*cbo_approval_type',"../")+'&report_title='+report_title;
        //alert(data);return;
        freeze_window(3);
        http.open("POST","requires/accessories_controll_new_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fn_report_generated_response;

    }

    function fn_report_generated_response()
    {
        if(http.readyState == 4)
        {
            var reponse=trim(http.responseText).split("**");
            //alert (reponse[0]);
            $("#report_container2").html(reponse[0]);
            //$('#report_container2').html(reponse[0]);
            //document.getElementById('report_container').innerHTML=report_convert_button('../../../');
            setFilterGrid("tbl_issue_status",-1);
            show_msg('3');
            release_freezing();

        }
    }
   
    function new_window()
    {
        document.getElementById('scroll_body').style.overflow="auto";
        document.getElementById('scroll_body').style.maxHeight="none";
        $('#scroll_body tr:first').hide();
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
            '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
        d.close();
        document.getElementById('scroll_body').style.overflowY="scroll";
        document.getElementById('scroll_body').style.maxHeight="380px";
        $('#scroll_body tr:first').show();
        //document.getElementById('scroll_body').style.maxWidth="120px";
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
	
    function check_all(tot_check_box_id)
    {
        //alert(tot_check_box_id);
        if ($('#'+tot_check_box_id).is(":checked"))
        {
            $("#tbl_accessories_controll tbody").find('tr').each(function()
            {
                try
                {
                    //var dealing_merchant=$(this).find("td:eq(8)").text();
                    //var approval_status=$(this).find('select[id="cbo_approval_type"]').val();

                    var hide_approval_type=parseInt($('#hide_approval_type').val());
                    //alert(approval_status+"__"+hide_approval_type);return;

                    if(!(hide_approval_type==1))
                    {
                        $(this).find('input[name="tbl[]"]').attr('checked', true);
                    }
                    else
                    {
                        //alert("approved");
                        $(this).find('input[name="tbl[]"]').attr('checked', false);
                    }
                }
                catch(e)
                {
                    //got error no operation
                }

            });
        }
        else
        {
            $('#tbl_accessories_controll tbody tr').each(function() {
                $('#tbl_accessories_controll tbody tr input:checkbox').attr('checked', false);
            });
        }
    }

    function submit_approved(total_tr,type,permission)
    {
        //var operation=4;
		var po_breakdown_id = "";  var approval_status = ""; var sew_quantity = "";
		freeze_window(0);
        if (permission ==2) {
            alert('You Have No Authority');
			release_freezing();
            return false;
        }
        // Confirm Message  *********************************************************************************************************
        if(type==1)
        {
            if($("#all_check").is(":checked"))
            {
                first_confirmation=confirm("Are You Want to UnApproved All Booking No");
                if(first_confirmation==false)
                {
					release_freezing();
                    return;
                }
                else
                {
                    second_confirmation=confirm("Are You Sure Want to UnApproved All Booking No");
                    if(second_confirmation==false)
                    {
						release_freezing();
                        return;
                    }
                }
            }

        }
        // Confirm Message End ***************************************************************************************************
        var i;
		var po_breakdown_id=""; var sew_quantity ="";
		var data_string=""; var all_order_id="";
        for(i=1; i<total_tr; i++)
        {
            if ($('#tbl_'+i).is(":checked"))
            {
				if(all_order_id=="") all_order_id=$('#po_id_'+i).attr('title'); else all_order_id+=","+$('#po_id_'+i).attr('title');
                if(data_string=="") data_string=$('#po_id_'+i).attr('title')+"**"+$('#issue_amt_'+i).html()+"**"+$('#dtlsId_'+i).val(); 
                else data_string+="__"+$('#po_id_'+i).attr('title')+"**"+$('#issue_amt_'+i).html()+"**"+$('#dtlsId_'+i).val();
            }
        }
		
        var data="action=approve&approval_type="+type+"&data_string="+data_string+"&total_tr="+total_tr+"&all_order_id="+all_order_id;
		//alert(data);return;
        
        http.open("POST","requires/accessories_controll_new_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange=fnc_accessories_approval_Reply_info;
    }
	
    function fnc_accessories_approval_Reply_info()
    {
        if(http.readyState == 4)
        {
			var reponse = trim(http.responseText).split('**');
			//alert(http.responseText);
			$('#report_container2').html("");
			show_list_view(reponse[1]+"__"+reponse[2], 'show_dtls_list_view', 'report_container2', 'requires/accessories_controll_new_controller', ''); 
            show_msg(trim(reponse[0]));
        }
        release_freezing();
    }


</script>
</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../",$permission);  ?>
    <form name="greyissuestatus_1" id="greyissuestatus_1" autocomplete="off" >
        <h3 style="width:1050px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:1050px" >
            <fieldset>
                <table class="rpt_table" width="1050" cellpadding="0" cellspacing="0" rules="all">
                    <thead>
                        <th width="200" class="must_entry_caption">Company</th>
                        <th width="120" >Buyer</th>
                        <th width="120">Year</th>
                        <th width="120" >Job No</th>
                        <th width="120">Style Ref.</th>
                        <th width="120">Order No.</th>
                        <th width="120">Approval Type</th>
                        <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('palnningEntry_1','list_container_fabric_desc','','','')" class="formbutton" style="width:100px" /></th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td>
                                <?
                                echo create_drop_down( "cbo_company_id", 190, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and company_name!='0'  order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/accessories_controll_new_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                                ?>
                            </td>
                            <td id="buyer_td">
                                <?
                                echo create_drop_down( "cbo_buyer_id", 110,$blank_array,"", 1, "-- Select Buyer--", $selected, "","","","","","");
                                ?>
                            </td>
                            <td>
                                <?
                                echo create_drop_down( "cbo_year", 110, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
                                ?>
                            </td>
                            <td>
                                <input style="width:110px;" name="txt_job" id="txt_job" class="text_boxes" onDblClick="openmypage_job()" placeholder="Browse Job No" readonly />
                                <input type="hidden" name="txt_job_id" id="txt_job_id"/>
                            </td>
                            <td>
                                <input style="width:110px;" name="txt_style_id" id="txt_style_id" class="text_boxes" onDblClick="openmypage_style()" placeholder="Browse Style" readonly />
                                <input type="hidden" name="txt_style" id="txt_style" style="width:90px;"/>
                            </td>
                            <td >
                                <input style="width:110px;" name="txt_order_no" id="txt_order_no" class="text_boxes" onDblClick="openmypage_order()" placeholder="Browse/Write Order"  />
                                <input type="hidden" name="txt_order_no_id" id="txt_order_no_id" style="width:90px;"/>
                            </td>
                            <td>
                                <?
                                $pre_cost_approval_type=array(2=>"Un-Approved",1=>"Approved");
                                echo create_drop_down( "cbo_approval_type", 120, $pre_cost_approval_type,"", 0, "", $selected,"","", "" );
                                ?>
                            </td>
                            <td>
                                <input type="button" name="search" id="search" value="Show" onClick="fn_report_generated()" style="width:100px" class="formbutton" />
                            </td>
                        </tr>
                    </tbody>
                    
                </table>
            </fieldset>
        </div>

        <div id="report_container" align="center"></div>
        <div id="report_container2" align="left"></div>

    </form>
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
