<?
    /*-------------------------------------------- Comments -----------------------
    Purpose			:	This Form Will Create Sample Data Archive Report.
    Functionality	:
    JS Functions	:
    Created by		:	MD. SAKIBUL ISLAM
    Creation date 	: 	18-SEP-2023
    Updated by 		: 		
    Update date		: 		   
    QC Performed BY	:	Md. Taifur Rahman	
    QC Date			:	
    Comments		:
    */

    session_start();
    if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
    require_once('../../includes/common.php');
    extract($_REQUEST);
    $_SESSION['page_permission']=$permission;

    //--------------------------------------------------------------------------------------------------------------------
    echo load_html_head_contents("Data Archiving Report", "../../", 1, 1,$unicode,1,1);
?>	
    <script>

        if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
        var permission = '<? echo $permission; ?>';	

            var tableFilters = 
            {
                col_operation: {
                    id: ["value_total_wo_qnty"],
                    col: [7],
                    operation: ["sum"],
                    write_method: ["innerHTML"]
                }
            } 
            
            function fn_report_generated(operation)
            {
                var txt_booking_no=document.getElementById('txt_booking_no').value;
                var cbo_fab_color_code=document.getElementById('cbo_fab_color_code').value;
                var cbo_fabrication=document.getElementById('cbo_fabrication').value;
                var txt_color_type=document.getElementById('txt_color_type').value;
                
                //if(txt_booking_no=="" || cbo_fabrication=="" )
                //{
                    if(form_validation('txt_booking_no*cbo_fabrication','Booking*Fabrication')==false)
                    {
                        return;
                    }
                 //}  
                    var report_title=$( "div.form_caption" ).html();
                    var data="action=report_generate&operation="+operation+get_submitted_data_string('txt_booking_no*cbo_fab_color_code*cbo_fabrication*txt_color_type*update_id',"../../")+'&report_title='+report_title;
                    freeze_window(3);
                    //alert(data);
                    http.open("POST","requires/sample_data_archive_report_controller.php",true);
                    http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                    http.send(data);
                    http.onreadystatechange = fn_report_generated_reponse;
                 
            }
            
            function fn_report_generated_reponse()
            {
                if(http.readyState == 4) 
                {
                    var reponse=trim(http.responseText).split("**");
                    $('#report_container4').html(reponse[0]);
                    document.getElementById('report_container3').innerHTML=report_convert_button('../../');
                    document.getElementById('report_container3').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px;"/>';
                    
                    show_msg('3');
                    setFilterGrid("table_body",-1,tableFilters);
                    release_freezing();
                }
            }
            function new_window()
            {
                document.getElementById('scroll_body').style.overflow="auto";
                document.getElementById('scroll_body').style.maxHeight="none";
                $('#table_body tbody tr:first').hide();
                var w = window.open("Surprise", "#");
                var d = w.document.open();
                d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
                '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container4').innerHTML+'</body</html>');
                d.close();
                document.getElementById('scroll_body').style.overflowY="scroll";
                document.getElementById('scroll_body').style.maxHeight="400px";
                $('#table_body tbody tr:first').show();
            }

            function openmypage_booking()
            {
                var company_id=$("#company_id").val();
                var title = 'Booking Info';	//
                var page_link = 'requires/sample_data_archive_report_controller.php?&action=booking_popup'+'&company_id='+company_id;
                emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=400px,center=1,resize=1,scrolling=0','');
                emailwindow.onclose=function()
                {
                    var theform=this.contentDoc.forms[0];
                    var mst_tbl_id=this.contentDoc.getElementById("update_id").value;//mst id
                    var mst_tbl_idArr=mst_tbl_id.split("_");
                    var booking_id=mst_tbl_idArr[1];
                   // alert(mst_tbl_idArr);
                    
                    if (mst_tbl_id!="")
                    {
                        //freeze_window(5); 
                        $('#txt_booking_no').val(booking_id);
                        get_php_form_data(mst_tbl_id, "populate_data_from_booking_search_popup", "requires/sample_data_archive_report_controller" );
                        
                    }
                }
            }		
    </script>
    </head>
    <body onLoad="set_hotkey();">
        <div style="width:100%;" align="center">
            <? echo load_freeze_divs ("../../",$permission); ?>   
            <!-- Previose form name= "FHAReport_1" id="FHAReport_1" -->
            <form name="FHAReport_1" id="FHAReport_1" autocomplete="off" > 
            <h3 style="width:650px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Data Archiving Search Panel</h3>
                <div id="content_search_panel" >
                    <fieldset style="width:635px;">
                    <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <tr>
                                <th width="130" class="must_entry_caption">Booking No</th>
                                <th width="130">Fab Color Code</th>
                                <th width="130" class="must_entry_caption">Fabrication</th>
                                <th width="120">Color Type </th>
                                <th colspan="2"> </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td style="width:130px">
                                    <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width: 150px;" placeholder="Browse" readonly onDblClick="openmypage_booking()">
                                    <input type="hidden"  name="update_id" id="update_id" class="text_boxes_numeric" style="width:50px;">
                                </td>
                                <td style="width:130px" id="basic_color_td">
                                <? 
                                    echo create_drop_down( "cbo_fab_color_code",130,$sql,",", 1, "--Select--", $selected, "","","","","","",2);
                                ?>
                                </td>
                                <td style="width: 130px;" id="basic_fabric_td">
                                <? 
                                    echo create_drop_down( "cbo_fabrication",130,$sql,",", 1, "--Select--", $selected, "","","","","","",2);
                                ?> 
                                </td>
                                <td style="width: 120px;"  id="basic_color_type_td">
                                <!-- <input name="txt_color_type" id="txt_color_type" class="text_boxes" style="width:120px" placeholder="Display" readonly > -->
                                <? 
                                     echo create_drop_down( "txt_color_type",130,$sql,",", 1, "--Select--", $selected, "","","","","","",2);
                                ?> 
                                
                                 </td>

                                <td ><input type="button" id="show_button" class="formbutton" style="width:60px" value="Show" onClick="fn_report_generated(1)" /></td>
                                <td ><input type="button" id="show_button" class="formbutton" style="width:65px" value="QR Code" onClick="fn_report_generated(2)" /></td>
                            </tr>
                            <!-- <tr>
                                <td colspan="5" align="center"><? //echo load_month_buttons(1); ?></td>
                            </tr> -->
                        </tbody>
                    </table> 
                    </fieldset>
                </div>
                <div id="report_container3" style="margin-top: 2px;" align="center"></div>
                <div id="report_container4" align="center"></div>
            </form> 
        </div>
        <div style="display:none" id="data_panel"></div>
    </body>
        <script>//set_multiselect('cbo_wo_type','0','0','','0');</script>
        <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
