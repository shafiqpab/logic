<?
/*-------------------------------------------- Comments
Purpose         :   Barcode Issue To Finishing      
Functionality   :   
JS Functions    :
Created by      :   Md. Helal Uddin
Creation date   :   10-09-2021
Updated by      :       
Update date     :          
QC Performed BY :       
QC Date         :   
Comments        :
*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

    require_once('../includes/common.php');
    extract($_REQUEST);

    $_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
    echo load_html_head_contents("GMT Finishing Receive", "../", 1, 1, $unicode, '', '');
    ?>
<script>

        if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../logout.php";
        var permission = '<? echo $permission; ?>';

        function generate_report(type){
           
            var working_company_id=document.getElementById('working_company_id').value;
            var wc_location_id=document.getElementById('wc_location_id').value;
            var lc_company_id=document.getElementById('lc_company_id').value;
            var lc_location_id=document.getElementById('lc_location_id').value;
            var wc_floor=document.getElementById('wc_floor').value;
           
            
            var txt_date_from=document.getElementById('txt_date_from').value;
            var txt_date_to=document.getElementById('txt_date_to').value;
            var txt_search_text=document.getElementById('txt_search_text').value;
            if(txt_search_text.length>0)
            {
                 if ( form_validation('cbo_bundle_level*working_company_id','Bundle Level*Working Company Name')==false )
                {
                    return;
                }
            }
            else
            {
                var cbo_source=document.getElementById('cbo_source').value*1;

                if(cbo_source==3)
                {
                    if ( form_validation('cbo_bundle_level*working_company_id*txt_date_from*txt_date_to','Bundle Level*Working Company Name*Date *Date')==false )
                    {
                        return;
                    }
                }
                else
                {
                    if ( form_validation('cbo_bundle_level*working_company_id*wc_location_id*txt_date_from*txt_date_to','Bundle Level*Working Company Name*Working company location*Date *Date')==false )
                    {
                        return;
                    }
                }

                
            }
           
           
            
            var data='action=generate_report&type='+type+get_submitted_data_string('working_company_id*wc_location_id*lc_company_id*lc_location_id*txt_date_from*txt_date_to*wc_floor*cbo_buyer_id*txt_search_text*cbo_search_by*cbo_source*cbo_bundle_level','../');
           // alert(data);
            //return;
            freeze_window(5);
            http.open("POST","requires/gmt_finishing_receive_controller.php",true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_show_details_reponse;  
        }
       

        function fnc_show_details_reponse(){
            if(http.readyState == 4) 
            {
                var response=trim(http.responseText);
                //alert(response);
                $("#update_id").val("");
                $("#txt_system_no").val("");
                $('#report_container').html(response);
                set_all_onclick();
                show_msg('18');
                release_freezing();

                
                 var div_overflow = document.getElementById("div_overflow");
                var height = div_overflow.clientHeight;
                console.log(height);
                if (height*1<200) {
                   document.getElementById("scanning_tbl").style.marginLeft = "-17px"; 
                }
                
               
            }
        }
       
        

      
        function opensystemno(page_link,title)
        {
                if ( form_validation('lc_company_id','Finishing Company')==false )
                {
                    return;
                }
                 page_link=page_link+"&company_id="+document.getElementById('lc_company_id').value;
          
                emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1080px,height=370px,center=1,resize=0,scrolling=0','')

                emailwindow.onclose=function()
                {
                    var theform=this.contentDoc.forms[0];

                    var data_str=this.contentDoc.getElementById("selected_work_order").value;//system_id and system_no
                    
                   
                    if(data_str!="" ){
                        var data=data_str.split("***");
                        if(data[0])
                        {
                            $("#update_id").val(data[0]);
                            $("#txt_system_no").val(data[1]);
                            get_php_form_data( data[0], "populate_data_from_search_popup", "requires/gmt_finishing_receive_controller" );
                            show_list_view(data[0],'populate_dtls_data','report_container','requires/gmt_finishing_receive_controller','setFilterGrid("scanning_tbl",-1)');
                            var div_overflow = document.getElementById("div_overflow");
                            var height = div_overflow.clientHeight;
                            console.log(height);
                            if (height*1<200) {
                               document.getElementById("scanning_tbl").style.marginLeft = "-17px"; 
                            }
                        }
                    }
                    
                  
                    release_freezing();
                   
                }
                
        
        }


    function fnc_valid_time(val,field_id)
    {
        var val_length=val.length;
        if(val_length==2)
        {
            document.getElementById(field_id).value=val+":";
        }

        var colon_contains=val.includes(":");
        if(colon_contains==false)
        {
            if(val>23)
            {
                document.getElementById(field_id).value='23:';
            }
        }
        else
        {
            var data=val.split(":");
            var minutes=data[1];
            var str_length=minutes.length;
            var hour=data[0]*1;

            if(hour>23)
            {
                hour=23;
            }

            if(str_length>=2)
            {
                minutes= minutes.substr(0, 2);
                if(minutes*1>59)
                {
                    minutes=59;
                }
            }

            var valid_time=hour+":"+minutes;
            document.getElementById(field_id).value=valid_time;
        }
    }

    function numOnly(myfield, e, field_id)
    {
        var key;
        var keychar;
        if (window.event)
            key = window.event.keyCode;
        else if (e)
            key = e.which;
        else
            return true;
        keychar = String.fromCharCode(key);

        // control keys
        if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
        return true;
        // numbers
        else if ((("0123456789:").indexOf(keychar) > -1))
        {
            var dotposl=document.getElementById(field_id).value.lastIndexOf(":");
            if(keychar==":" && dotposl!=-1)
            {
                return false;
            }
            return true;
        }
        else
            return false;
    }

    function save_update_delete(operation)
    {
        
       if (form_validation('cbo_bundle_level*cbo_source*lc_company_id*lc_location_id*txt_recv_date*txt_reporting_time*finishing_floor', 'Bundle Level*Production Source*Fini. Company*Fini. Location *Receive Date*Reporting Time*Finishing Floor') == false) {
                return;
        }

       
         var j = 0;
        var dataString = '';


        $("#scanning_tbl").find('tbody tr').each(function () {
           
            var productionDate = $(this).find('input[name="productionDate[]"]').val();
            var poBreakDownId = $(this).find('input[name="poBreakDownId[]"]').val();
            var challanNo = $(this).find('input[name="challanNo[]"]').val();
            var companyId = $(this).find('input[name="companyId[]"]').val();
            var locationId = $(this).find('input[name="locationId[]"]').val();
            var itemId = $(this).find('input[name="itemId[]"]').val();
            var flooId = $(this).find('input[name="flooId[]"]').val();
            var colorId = $(this).find('input[name="colorId[]"]').val();
            var sizeId = $(this).find('input[name="sizeId[]"]').val();
            var countryId = $(this).find('input[name="countryId[]"]').val();
            var lineId = $(this).find('input[name="lineId[]"]').val();
            var colorTypeId = $(this).find('input[name="colorTypeId[]"]').val();
            var productionHour = $(this).find('input[name="productionHour[]"]').val();
            var productionHour = $(this).find('input[name="productionHour[]"]').val();
            var txtRecvQnty = $(this).find('input[name="txtRecvQnty[]"]').val();
            var txtQcPassQnty = $(this).find('input[name="txtQcPassQnty[]"]').val();
            var dtlsId = $(this).find('input[name="dtlsId[]"]').val();
            var sourceId = $(this).find('input[name="sourceId[]"]').val();
            var lcCompanyId = $(this).find('input[name="lcCompanyId[]"]').val();
            var txtRemark = $(this).find('input[name="txtRemark[]"]').val();
            var bundleNo = $(this).find('input[name="bundleNo[]"]').val();
            var barcodeNo = $(this).find('input[name="barcodeNo[]"]').val();
            if(Number(txtRecvQnty)>0)
            {
                if(operation==1)
                {
                    if(Number(dtlsId)<1)
                    {
                        alert('Call System Popup and update');
                        return;
                    }
                }
                try {
                   
                    j++;

                    dataString +='&productionDate_' + j + '=' + productionDate + '&poBreakDownId_' + j + '=' + poBreakDownId + '&challanNo_' + j + '=' + challanNo + '&companyId_' + j + '=' + companyId + '&locationId_' + j + '=' + locationId + '&itemId_' + j + '=' + itemId + '&colorId_' + j + '=' + colorId + '&sizeId_' + j + '=' + sizeId + '&countryId_' + j + '=' + countryId + '&lineId_' + j + '=' + lineId+ '&colorTypeId_' + j + '=' + colorTypeId+ '&productionHour_' + j + '=' + productionHour+ '&flooId_' + j + '=' + flooId + '&txtRecvQnty_' + j + '=' + txtRecvQnty+ '&txtQcPassQnty_' + j + '=' + txtQcPassQnty+ '&dtlsId_' + j + '=' + dtlsId+ '&lcCompanyId_' + j + '=' + lcCompanyId+ '&txtRemark_' + j + '=' + txtRemark+ '&sourceId_' + j + '=' + sourceId+ '&bundleNo_' + j + '=' + bundleNo+ '&barcodeNo_' + j + '=' + barcodeNo;
                }
                catch (e) {
                    
                }
            }

           
        });

        if (j < 1 && operation<2) {
            alert('No data');
            return;
        }
        
        var data = "action=save_update_delete&operation=" + operation + '&tot_row=' + j + get_submitted_data_string('txt_recv_date*txt_reporting_time*update_id*working_company_id*finishing_floor*lc_company_id*lc_location_id*cbo_buyer_id*cbo_source*cbo_bundle_level', "../") + dataString;
        freeze_window(operation);

        

        http.open("POST", "requires/gmt_finishing_receive_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_save_update_delete_Reply_info;


    }

    function fnc_save_update_delete_Reply_info()
    {
        if (http.readyState == 4) {
            release_freezing();

            // console.log(http.responseText);
            // return;
            var response = trim(http.responseText).split('**');
            //console.log(response);
            if (response[0] == 11 || response[0]==121) {
                alert(response[1]);
               
                release_freezing();
                return;
            }
            show_msg(response[0]);
            if ((response[0] == 0 || response[0] == 1)) {
                if(response[2])
                {
                    $('#txt_system_no').val(response[2]);
                }
               
                $('#update_id').val(response[1]);
                set_button_status(1, permission, 'save_update_delete', 1);
            }
            else if(response[0]==2)
            {
                $("#report_container").val("");
                $("#update_id").val("");
                $("#txt_system_no").val("");
                $("#report_container").html("");
            }
            release_freezing();
        }
    }

    function compare_with_qc_pass(sl)
    {
        var recv_qnty=$("#txtRecvQnty_"+sl).val()*1;
        var qc_pass_qnty=$("#txtQcPassQnty_"+sl).val()*1;
        if(recv_qnty>qc_pass_qnty)
        {
            alert('Fin. Receive qnty can not be greater than QC pass qnty balance');
            $("#txtRecvQnty_"+sl).val(qc_pass_qnty);
            return;
        }

        var total_rows = $("#scanning_tbl tbody tr").length - 1;
        var total_receive_qnty=0*1;
        for(var i=1; i<=total_rows; i++)
        {
            total_receive_qnty += $("#txtRecvQnty_"+i).val()*1;
        }
        $("#total_fin_receive_qnty").val(total_receive_qnty);
    }
    function compare_with_total_recv(sl)
    {
        var recv_qnty=$("#txtRecvQnty_"+sl).val()*1;
        var txtQcPassQnty=$("#txtQcPassQnty_"+sl).val()*1;
       
        var poBreakDownId=$("#poBreakDownId_"+sl).val()*1;
        var challanNo=$("#challanNo_"+sl).val();
        var companyId=$("#companyId_"+sl).val();
        var locationId=$("#locationId_"+sl).val();
        var itemId=$("#itemId_"+sl).val();
        var colorId=$("#colorId_"+sl).val();
        var colorTypeId=$("#colorTypeId_"+sl).val();
        var sizeId=$("#sizeId_"+sl).val();
        var countryId=$("#countryId_"+sl).val();
        var floorId=$("#floorId_"+sl).val();
        var dtlsId=$("#dtlsId_"+sl).val();
        var dataStrin =poBreakDownId+'**'+ companyId + '**' + locationId + '**'+ challanNo + '**' + itemId + '**' + colorId + '**' + colorTypeId + '**' + sizeId + '**' + countryId + '**' + floorId + '**' + dtlsId ;
        var recv_qnty_total = trim(return_global_ajax_value(dataStrin, 'recv_qnty_total', '', 'requires/gmt_finishing_receive_controller'));
        console.log(recv_qnty_total);
        if((txtQcPassQnty-recv_qnty_total)*1<recv_qnty)
        {
            alert('Fin. Receive qnty can not be greater than balance');
            $("#txtRecvQnty_"+sl).val(txtQcPassQnty-recv_qnty_total);
            return;
        }

        var total_rows = $("#scanning_tbl tbody tr").length - 1;
        var total_receive_qnty=0*1;
        for(var i=1; i<=total_rows; i++)
        {
            total_receive_qnty += $("#txtRecvQnty_"+i).val()*1;
        }
        $("#total_fin_receive_qnty").val(total_receive_qnty);
    }
   
 

</script>
</head>


<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../",''); ?>
         <form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1"> 
           
         <h3 style="width:1450px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
             <fieldset style="width:1450px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th class="must_entry_caption">Bundle Level</td>
                            <th class="must_entry_caption">Prod. Source</td>
                            <th class="must_entry_caption">Sew. Company</th>
                            <th class="must_entry_caption">Sew. Location</th>
                            <th>Sew. Floor</th>
                            <th class="must_entry_caption">Fini. Company</th>
                            
                            <th class="must_entry_caption">Fini. Location</th>
                            <th class="must_entry_caption">Fini. Floor</th>
                            <th>Buyer</th>

                            <th>
                            <?
                                $search_arr=array(1=>'Order No',2=>'Style Ref.',3=>'Internal Ref.');
                               
                                echo create_drop_down("cbo_search_by", 80, $search_arr,'', 1, "Search Type",'','',0);

                             ?></th>
                           
                            <th class="must_entry_caption">Sew. Prod. Date</th>
                            <th class="must_entry_caption">Rcv. Date</th>
                            <th class="must_entry_caption">Rpt Time</th>
                           
                            <th class="must_entry_caption">System No</th>
                            <th>
                                <input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailyYarnStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:50px" />

                            </th>
                        </thead>
                        <tbody>
                            <tr class="general">

                             <td><? $level_array = array(1=>'No',2=>'Yes'); echo create_drop_down( "cbo_bundle_level", 60, $level_array,"", 1, "-- Select --", 2, "", 0, '' ); ?></td>
                             <td><? echo create_drop_down( "cbo_source", 100, $knitting_source,"", 1, "-- Select Source --", $selected, " load_drop_down( 'requires/gmt_finishing_receive_controller', this.value+'**', 'load_drop_down_sewing_output', 'sew_company_td' );", 0, '1,3' ); ?></td>
                                 <td id="sew_company_td">
                                  <?
                                    echo create_drop_down("working_company_id", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name", "id,company_name", 1, "--Select--", 0, "load_drop_down( 'requires/gmt_finishing_receive_controller', this.value, 'load_drop_down_working_location', 'wc_location_td' );load_drop_down( 'requires/gmt_finishing_receive_controller', document.getElementById('wc_location_id').value+'_'+document.getElementById('working_company_id').value, 'load_drop_down_wc_company_location_wise_floor', 'wc_floor_td' )", 0);//
                                    ?>
                                </td>
                               <td id="wc_location_td">
                                    <?
                                    echo create_drop_down("wc_location_id", 100, "select id, location_name from lib_location", "id,location_name", 1, "--Select--", 0, "", 1);
                                    ?>
                                </td>
                                 <td id="wc_floor_td">
                                     <?
                                        $arr=array();
                                        echo create_drop_down("wc_floor", 90, $arr, "", 1, "-- Select Floor --", 0, "", 1);
                                    ?>
                                </td>

                                <td> 
                                     <?
                                        echo create_drop_down("lc_company_id", 90, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by company_name", "id,company_name", 1, "--Select--", 0, "load_drop_down( 'requires/gmt_finishing_receive_controller', this.value, 'load_drop_down_lc_company_location', 'lc_location_td' );", 0);//
                                        ?>
                                </td>
                              
                                <td id="lc_location_td">
                                    <?
                                        $arr=array();
                                     echo create_drop_down("lc_location_id", 90, $arr, "", 1, "-- Select Location --", 0, "", 1);
                                    ?>
                                        
                                </td>
                                 <td id="fini_floor_td">
                                     <?
                                       $floor_arr=return_library_array( "select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and production_process =11",'id','floor_name');
                                        echo create_drop_down("finishing_floor", 90, $floor_arr, "", 1, "-- Select Floor --", 0, "", 0);
                                    ?>
                                </td>
                                 <td >
                                    <?
                                        
                                     echo create_drop_down("cbo_buyer_id", 90, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active=1 and buy.is_deleted=0", "id,buyer_name", 1, "-- Select Buyer --", 0, "");
                                    ?>
                                        
                                </td>
                               
                                <td align="center">

                                     <input type="text" name="txt_search_text" id="txt_search_text"  class="text_boxes" style="width:70px"  />
                                    
                                </td>
                                <td align="center">
                                     <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:50px" placeholder="From Date"/>
                                     &nbsp;To&nbsp;
                                     <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:50px" placeholder="To Date"/>
                                </td>
                                 <td align="center">
                                     <input type="text" name="txt_recv_date" id="txt_recv_date"  class="datepicker" style="width:50px" value="<?=date('d-m-Y')?>" />
                                    
                                </td>
                                <td align="center">
                                     <input type="text" name="txt_reporting_time" id="txt_reporting_time" class="text_boxes"   style="width:50px" placeholder="24 Hour Format" onBlur="fnc_valid_time(this.value,'txt_reporting_time');" onKeyUp="fnc_valid_time(this.value,'txt_reporting_time');" onKeyPress="return numOnly(this,event,this.id);" maxlength="8" value="<?= date('H:i',time());?>" />
                                    
                                </td>
                               
                                <td>
                                    <input type="text" name="txt_system_no" class="text_boxes" id="txt_system_no" placeholder="System No" onDblClick="opensystemno('requires/gmt_finishing_receive_controller.php?action=system_no_popup','System No');setFilterGrid('list_view_system',-1)" style="width: 120px;">
                                    <input type="hidden" name="update_id" id="update_id">
                                </td>
                                <td>
                                    <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="generate_report(1)" />
                                </td>
                            </tr>
                            
                        </tbody>
                    </table>
                    <table>
                <tr>
                    <td colspan="9">
                        <? echo load_month_buttons(1); ?>
                    </td>
                </tr>
            </table> 
            <br />
                </fieldset>
            </div>
            <div id="report_container"></div>
            <div id="report_container2"></div>
        </form>
    </div>
    
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>