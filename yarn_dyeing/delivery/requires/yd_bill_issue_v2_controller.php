<?php
	
header('Content-type:text/html; charset=utf-8'); 
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header('location:login.php');
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_level=$_SESSION['logic_erp']["user_level"];

if ($action=="load_drop_down_party")
{
    $data=explode("_",$data);

    if($data[1]==1 && $data[0]!=0)
    {

        echo create_drop_down( "cbo_party_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "-- Select Party --", $data[2], "load_drop_down('requires/yd_bill_issue_v2_controller', this.value,'load_drop_down_party_location', 'party_location_td' );");
    }
    elseif($data[1]==2 && $data[0]!=0)
    {
        echo create_drop_down( "cbo_party_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",0, "" );
    }
    else
    {
    	echo create_drop_down('cbo_party_name', 120, $blank_array, '', 1, '-- Select Party --', $selected, "",1);
    }   
    exit();  
}

if ($action=="load_drop_down_location")
{
    
    echo create_drop_down("cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );   
    exit();
}

if ($action=="load_drop_down_party_location")
{
    echo create_drop_down("cbo_party_location", 150, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );   
    exit();
}

if ($action == "check_conversion_rate") {
    $data = explode("**", $data);
    if ($db_type == 0) {
        $conversion_date = change_date_format($data[1], "Y-m-d", "-", 1);
    } else {
        $conversion_date = change_date_format($data[1], "d-M-y", "-", 1);
    }
    $exchange_rate = set_conversion_rate($data[0], $conversion_date, $data[2]);
    //$exchange_rate = set_conversion_rate($data[0], $conversion_date);
    echo $exchange_rate;
    exit();
}

if($action == "job_search_popup_job")
{
    echo load_html_head_contents('Search Yarn Dyeing Job', '../../../', 1, 0, $unicode);
    extract($_REQUEST);

    ?>
    <script>
        function search_by(val)
        {
            $('#txt_search_string').val('');
            if(val==1 || val==0) $('#search_by_td').html('YD Job No');
            else if(val==2) $('#search_by_td').html('W/O No');
            else if(val==3) $('#search_by_td').html('Buyer Style');
            else if(val==4) $('#search_by_td').html('Buyer Job');
        }

        var selected_id = new Array(); var jobNoArr = new Array;
        function toggle( x, origColor ) 
        {
            var newColor = 'yellow';
            if ( x.style ) {
            x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
            }
        }
        
        function check_all_data() 
        {
            $("#tbl_data_list tbody tr").each(function() 
            {
                var valTP=$(this).attr("id");
                //if(valTP==1) return;
                //alert(valTP);
                //$("#"+valTP).click();

                if (typeof valTP != "undefined")
                {
                    var val = valTP.split("_");
                    var id = val[1];
                    var job_no =  $('#txt_buyer_id'+id).val()*1;

                    if(jobNoArr.length==0)
                    {
                        jobNoArr.push( job_no );
                    }
                    else if( jQuery.inArray( job_no, jobNoArr )==-1 &&  jobNoArr.length>0)
                    {
                        alert("Buyer Mixed Not Allowed");
                        return true;
                    }
                    toggle( document.getElementById( 'search_' +id ), '#FFFFCC' );

                    if( jQuery.inArray( $('#txt_individual_id_' +id).val(), selected_id ) == -1 ) 
                    {
                        selected_id.push( $('#txt_individual_id_' +id).val() );
                    }
                    else 
                    {
                        for( var i = 0; i < selected_id.length; i++ ) 
                        {
                            if( selected_id[i] == $('#txt_individual_id_' +id).val() ) break;
                        }
                        selected_id.splice( i, 1 );
                        jobNoArr.splice( i, 1 );
                    }

                    var id = '';
                    for( var i = 0; i < selected_id.length; i++ ) 
                    {
                        id += selected_id[i] + ',';
                    }
                    id = id.substr( 0, id.length - 1 );
                    $('#txt_selected_id').val( id );
                    document.getElementById('hidden_party_id').value=job_no;
                }
            });
        }

        function js_set_value(str)
        {
            splitArr = str.split('***'); 
            var delevery_id=splitArr[0]*1;
            var receive_dtls_id=splitArr[1]*1;
            var job_no=splitArr[2]*1;

            $("#tbl_data_list tbody tr").each(function() 
            {
                var valTP=$(this).attr("id");

                if (typeof valTP != "undefined")
                {
                    var val = valTP.split("_");
                    var id = val[1];
                    var delevery_id1 =  $('#txt_id'+id).val()*1;
                    if(delevery_id==delevery_id1)
                    {
        
                    if(jobNoArr.length==0)
                    {
                        jobNoArr.push( job_no );
                    }
                    else if( jQuery.inArray( job_no, jobNoArr )==-1 &&  jobNoArr.length>0)
                    {
                        alert("Buyer Mixed Not Allowed");
                        return true;
                    }
                    toggle( document.getElementById( 'search_' +id ), '#FFFFCC' );

                    if( jQuery.inArray( $('#txt_individual_id_' +id).val(), selected_id ) == -1 ) 
                    {
                        selected_id.push( $('#txt_individual_id_' +id).val() );
                    }
                    else 
                    {
                        for( var i = 0; i < selected_id.length; i++ ) 
                        {
                            if( selected_id[i] == $('#txt_individual_id_' +id).val() ) break;
                        }
                        selected_id.splice( i, 1 );
                        jobNoArr.splice( i, 1 );
                    }

                    var id = '';
                    for( var i = 0; i < selected_id.length; i++ ) 
                    {
                        id += selected_id[i] + ',';
                    }
                    id = id.substr( 0, id.length - 1 );
                    $('#txt_selected_id').val( id );
                    document.getElementById('hidden_party_id').value=job_no;
                }

                }
            });
        }
            
    </script>
    </head>
    <body>
        <div align="center" style="width:100%;" >
            <form name="searchorderfrm_<?php echo $tblRow;?>" id="searchorderfrm_<?php echo $tblRow;?>" autocomplete="off">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" style="width: 100%;">
                    <thead>
                        <tr>
                            <th colspan="11"><?php echo create_drop_down('cbo_string_search_type', 163, $string_search_type, '', 1, '-- Searching Type --'); ?></th>
                        </tr>
                        <tr>
                            <th width="120" class="must_entry_caption" >Company Name</th>
                            <th width="80" class="must_entry_caption" >Within Group</th>
                            <th width="120">Party Name</th>
                            <th width="80">Delivery Id</th>
                            <th width="80">Search By</th>
                            <th width="80" id="search_by_td">YD Job No</th>
                            <th width="70">Prod. Type</th>
                            <th width="70">Order Type</th>
                            <th width="70">Y/D Type</th>
                            <th width="160">Delivery Date Range</th>
                            <th>
                                <input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width: 100%" />
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td>
                                <?php echo create_drop_down('cbo_company_name', 120, "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", 'id,company_name', 1, '-- Select Company --', $cbo_company_name, "load_drop_down( 'yd_delivery_entry_v2_controller', this.value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_party', 'party_td' );",1); ?>
                            </td>
                            <td> 
                                <?php echo create_drop_down('cbo_within_group', 80, $yes_no, '', 1, '-- Select Within Group --', $cbo_within_group, "load_drop_down( 'yd_delivery_entry_v2_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_party', 'party_td' );",1); ?>
                            </td>
                            <td id="party_td"> 
                                <?php 

                                    if($cbo_within_group==1 && $cbo_company_name!=0)
                                    {

                                        echo create_drop_down( "cbo_party_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "-- Select Party --", $cbo_party_name, "",1);
                                    }
                                    elseif($cbo_within_group==2 && $cbo_company_name!=0)
                                    {
                                        echo create_drop_down( "cbo_party_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_name' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",$cbo_party_name, "",1 );
                                    }
                                    else
                                    {
                                        echo create_drop_down('cbo_party_name', 120, $blank_array, '', 1, '-- Select Party --', $selected, "",1);
                                    }

                                ?>
                            </td>
                            <td> 
                                <input type="text" name="txt_receive_no" id="txt_receive_no" class="text_boxes" placeholder="Write Receive Id" />
                            </td>
                            <td>
                                <?
                                    $search_by_arr=array(1=>"YD Job No",2=>"W/O No",3=>"Buyer Style",4=>"Buyer Job");
                                    echo create_drop_down( "cbo_type",80, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                                ?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:80px" placeholder="Write" />
                            </td>
                            <td>
                                <? echo create_drop_down( "cbo_pro_type",70, $w_pro_type_arr,"",1, "--Select--",$selected,'',0 );?>
                            </td>
                            <td>
                                <? echo create_drop_down( "cbo_order_type",70, $w_order_type_arr,"",1, "--Select--",$selected,'',0 ); ?>
                            </td>
                            <td>
                                <? echo create_drop_down( "cbo_yd_type",70, $yd_type_arr,"",1, "--Select--",$selected,'',0 ); ?>
                            </td>
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To">
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_pro_type').value+'_'+document.getElementById('cbo_order_type').value+'_'+document.getElementById('cbo_yd_type').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_receive_no').value, 'create_job_search_list_view', 'search_div', 'yd_bill_issue_v2_controller', 'setFilterGrid(\'tbl_data_list\',-1)')" style="width:100%;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="10" align="center" valign="middle">
                                <? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_party_id" id="hidden_party_id" class="text_boxes" style="width:70px">
                                <input type="hidden" id="txt_selected_id" name="txt_selected_id" value="" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <div id="search_div" align="center">
            
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?php
}

if($action=="create_job_search_list_view")
{   
    $data=explode('_',$data);

    $party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $comp_arr=return_library_array("select id, company_name from lib_company",'id','company_name');

    $search_type            =trim(str_replace("'","",$data[0]));
    $cbo_company_name       =trim(str_replace("'","",$data[1]));
    $cbo_within_group       =trim(str_replace("'","",$data[2]));
    $cbo_party_name         =trim(str_replace("'","",$data[3]));
    $search_by              =trim(str_replace("'","",$data[4]));
    $search_str             =trim(str_replace("'","",$data[5]));
    $cbo_pro_type           =trim(str_replace("'","",$data[6]));
    $cbo_order_type         =trim(str_replace("'","",$data[7]));
    $cbo_yd_type            =trim(str_replace("'","",$data[8]));
    $txt_date_from          =trim(str_replace("'","",$data[9]));
    $txt_date_to            =trim(str_replace("'","",$data[10]));
    $cbo_year_selection     =trim(str_replace("'","",$data[11]));
    $txt_receive_no         =trim(str_replace("'","",$data[12]));

    if($cbo_company_name==0)
    {
        echo "<p style='margin-top: 10px;'>Please Select Company Name first!!!</p>";
        die;
    }

    if($cbo_within_group==0)
    {
        echo "<p style='margin-top: 10px;'>Please Select Within Group first!!!</p>";
        die;
    }

    $condition = "";

    if($cbo_company_name!=0)
    {
        $condition .= " and a.company_id=$cbo_company_name";
    }

    if($txt_receive_no!=0)
    {
        $condition .= " and a.receive_no_prefix_num=$txt_receive_no";
    }

    if($cbo_within_group!=0)
    {
        $condition .= " and a.within_group=$cbo_within_group";
    }

    if($cbo_party_name!=0)
    {
        $condition .= " and a.party_id=$cbo_party_name";
    }

    if($cbo_pro_type!=0)
    {
        $condition .= " and a.pro_type=$cbo_pro_type";
    }

    if($cbo_order_type!=0)
    {
        $condition .= " and a.order_type=$cbo_order_type";
    }

    if($cbo_yd_type!=0)
    {
        $condition .= " and a.yd_type=$cbo_yd_type";
    }


    $date_con = '';
    if($db_type==0)
    { 
        if ($txt_date_from!="" &&  $txt_date_to!="") $date_con = "and a.receive_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'"; else $date_con ="";
    }
    else
    {
        if ($txt_date_from!="" &&  $txt_date_to!="") $date_con = "and a.receive_date between '".change_date_format($txt_date_from, "", "",1)."' and '".change_date_format($txt_date_to, "", "",1)."'"; else $date_con ="";
    }


    if($search_type==1)
    {
        if($search_str!="")
        {
            if($search_by==1) $condition="and a.job_no='$search_str'";
            else if($search_by==2) $condition="and a.order_no='$search_str'";
            else if ($search_by==3) $condition=" and b.style_ref = '$search_str' ";
            else if ($search_by==4) $condition=" and b.sales_order_no = '$search_str' ";
        }
        
    }
    else if($search_type==2)
    {
        if($search_str!="")
        {
            if($search_by==1) $condition="and a.job_no like '$search_str%'";
            else if($search_by==2) $condition="and a.order_no like '$search_str%'";
            else if ($search_by==3) $condition=" and b.style_ref like  '$search_str%' ";
            else if ($search_by==4) $condition=" and b.sales_order_no like  '$search_str%' ";
        }
        
    }
    else if($search_type==3)
    {
        if($search_str!="")
        {
            if($search_by==1) $condition="and a.job_no like '%$search_str'";
            else if($search_by==2) $condition="and a.order_no like '%$search_str'";
            else if ($search_by==3) $condition=" and b.style_ref like  '%$search_str' ";
            else if ($search_by==4) $condition=" and b.sales_order_no like  '%$search_str' ";
        }
        
    }
    else if($search_type==4 || $search_type==0)
    {
        if($search_str!="")
        {
            if($search_by==1) $condition="and a.job_no like '%$search_str%'";
            else if($search_by==2) $condition="and a.order_no like '%$search_str%'";
            else if ($search_by==3) $condition=" and b.style_ref like  '%$search_str%' ";
            else if ($search_by==4) $condition=" and b.sales_order_no like  '%$search_str%' ";
        }
        
    }

    $sql = "select a.yd_receive, a.id, b.id as receive_dtls_id, b.style_ref, a.party_id, a.pro_type, a.within_group, a.job_no, a.order_no, a.order_id, a.order_type, a.receive_date, b.count_type, b.sales_order_no from yd_store_receive_mst a, yd_store_receive_dtls b, yd_ord_mst c where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.yd_job=a.job_no and a.entry_form=295 $condition $date_con group by a.yd_receive, a.id, b.id, b.style_ref, a.party_id, a.pro_type, a.within_group, a.job_no, a.order_no, a.order_id, a.order_type, a.receive_date, b.count_type, b.sales_order_no order by a.yd_receive, a.id";

    $result = sql_select($sql);
    ?>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="995" >
        <thead>
            <th width="30">SL</th>
            <th width="100">Party Name</th>
            <th width="60">Prod. Type</th>
            <th width="80">Within Group</th>
            <th width="100">Delivery No</th>
            <th width="100">Job No</th>
            <th width="100">WO No</th>
            <th width="80">Buyer Style</th>
            <th width="80">Buyer Job</th>
            <th width="80">Order Type</th>
            <th width="80">Count Type</th>
            <th >Delivery Date</th>
        </thead>
    </table>
    <div style="width:996px; max-height:370px;overflow-y:scroll;" >
        <table class="rpt_table" border="1" id="tbl_data_list" cellpadding="0" cellspacing="0" rules="all" width="995" >
            <tbody>
                <?php
                    $i=1;
                    $count_type_arr = array(1 => "Single",2 => "Double");
                    foreach($result as $data)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

                        if($data[csf('within_group')]==1)
                        {
                            $party_name = $comp_arr[$data[csf('party_id')]];

                        }
                        else
                        {
                            $party_name = $party_arr[$data[csf('party_id')]];
                        }
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $data[csf('id')].'***'.$data[csf('receive_dtls_id')].'***'.$data[csf('party_id')]; ?>")' style="cursor:pointer" id="search_<? echo $data[csf('receive_dtls_id')];?>">
                    <td align="center" width="30"><? echo $i; ?></td>
                    <td align="center" width="100"><? echo $party_name; ?></td>
                    <td align="center" width="60"><? echo $w_pro_type_arr[$data[csf('pro_type')]]; ?></td>
                    <td align="center" width="80"><? echo $yes_no[$data[csf('within_group')]]; ?></td>
                    <td align="center" width="100"><? echo $data[csf('yd_receive')]; ?></td>
                    <td align="center" width="100"><? echo $data[csf('job_no')]; ?></td>
                    <td align="center" width="100"><? echo $data[csf('order_no')]; ?></td>
                    <td align="center" width="80"><? echo $data[csf('style_ref')]; ?></td>
                    <td align="center" width="80"><? echo $data[csf('sales_order_no')]; ?></td>
                    <td align="center" width="80"><? echo $w_order_type_arr[$data[csf('order_type')]]; ?></td>
                    <td align="center" width="80"><? echo $count_type_arr[$data[csf('count_type')]]; ?></td>
                    <td align="center" >
                        <? echo $data[csf('receive_date')]; ?>
                        <input type="hidden" name="txt_individual_id" id="txt_individual_id_<?php echo $data[csf('receive_dtls_id')]; ?>" value="<? echo $data[csf('receive_dtls_id')]; ?>"/>
                        <input type="hidden" name="txt_id" id="txt_id<? echo $data[csf('receive_dtls_id')];?>" value="<? echo $data[csf('id')]; ?>"/>
                        <input type="hidden" name="txt_buyer_id" id="txt_buyer_id<? echo $data[csf('receive_dtls_id')];?>" value="<? echo $data[csf('party_id')]; ?>"/>
                    </td>
                </tr>
                <?php
                    $i++;
                    }
                ?>
            </tbody>
        </table>
        <br>
        <table width="100%" cellspacing="0" cellpadding="0" style="border:none" align="center">
            <tr>
                <td align="center" height="30" valign="middle">
                    <div style="width:100%">
                    <div style="width:50%; float:left;" align="left">
                    <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                    </div>
                    <div style="width:100%; float:left" align="center">
                    <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                    </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <?php

    exit();
}

if($action=="dtls_list_view")
{
    $data=explode('_', $data);
    
    $party_id = $data[0];
    $receive_ids = $data[1];

    $party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $comp_arr=return_library_array("select id, company_name from lib_company",'id','company_name');

    $sql = "select a.yd_receive, a.party_id, a.job_no, a.order_type, a.pro_type, a.order_no from yd_store_receive_mst a, yd_store_receive_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_id='$party_id' and b.id in($receive_ids) and a.entry_form=295 group by a.yd_receive, a.party_id, a.job_no, a.order_type, a.pro_type, a.order_no";

    $result = sql_select($sql);
    ?>

    <table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
        <thead>
            <th width="35">Sl</th>
            <th width="150">Party Name</th>
            <th width="150">Delivery No</th>
            <th width="150">Job No</th>
            <th width="100">WO No</th>
            <th width="100">Prod. Type</th>
            <th width="100">Order Type</th>
        </thead>
        <tbody id="tbl_list_view">
    <?php

    $tblRow = 1;
    foreach($result as $data)
    {
        if ($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

        if($data[csf('within_group')]==1)
                        {
            $party_name = $comp_arr[$data[csf('party_id')]];

        }
        else
        {
            $party_name = $party_arr[$data[csf('party_id')]];
        }
    ?>
        <tr style="cursor:pointer" id="dtls_row_<?php echo $tblRow;?>" bgcolor="<? echo $bgcolor; ?>" onClick="load_delivery_data('<?php echo $data[csf('job_no')];?>','<?php echo $data[csf('yd_receive')];?>',<?php echo $tblRow;?>)">
            <td width="35" align="center"><?php echo $tblRow;?></td>
            <td align="center" width="150">
                <?php echo $party_name;?>
            </td>
            <td width="150" align="center">
                <?php echo $data[csf('yd_receive')];?>
            </td>
            <td width="150" align="center">
                <?php echo $data[csf('job_no')];?>
            </td>
            <td width="100" align="center">
                <?php echo $data[csf('order_no')];?>
            </td>
            <td width="100" align="center">
                <?php echo $w_pro_type_arr[$data[csf('pro_type')]];?>
            </td>
            <td width="100" align="center">
                <?php echo $w_order_type_arr[$data[csf('order_type')]];?>
            </td>
        </tr>
    <?php
        $tblRow++;
    }
    ?>
    </tbody>
    </table>
    <?php
    exit();

}

if($action=="load_php_yd_job_data_to_form")
{
    $sql = "select a.id, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.order_no, a.pro_type, a.order_type from yd_ord_mst a where a.yd_job='$data' and a.status_active=1 and a.is_deleted=0 and a.check_box_confirm=1";

    $data_array = sql_select($sql);
    unset($sql);

    foreach($data_array as $data)
    {
        //echo "document.getElementById('cbo_company_name').value = '".$data[csf('company_id')]."';\n";

        //echo "load_drop_down( 'requires/yd_delivery_entry_v2_controller',".$data[csf('company_id')]."+'_'+1, 'load_drop_down_location', 'location_td' );\n";
        //echo "document.getElementById('cbo_location_name').value = '".$data[csf('location_id')]."';\n";
        //echo "$('#cbo_location_name').attr('disabled','disabled');\n";

        //echo "document.getElementById('cbo_within_group').value = '".$data[csf('within_group')]."';\n";

        //echo "load_drop_down( 'requires/yd_delivery_entry_v2_controller',".$data[csf('company_id')]."+'_'+".$data[csf('within_group')].", 'load_drop_down_party', 'party_td' );\n";
        ////echo "document.getElementById('cbo_party_name').value = '".$data[csf('party_id')]."';\n";
        //echo "$('#cbo_party_name').attr('disabled','disabled');\n";

        //echo "load_drop_down( 'requires/yd_delivery_entry_v2_controller',".$data[csf('party_id')]."+'_'+2, 'load_drop_down_location', 'party_location_td' );\n";
        //echo "document.getElementById('cbo_party_location').value = '".$data[csf('party_location')]."';\n";
        //echo "$('#cbo_party_location').attr('disabled','disabled');\n";

        echo "document.getElementById('txt_wo_no').value = '".$data[csf('order_no')]."';\n";
        echo "document.getElementById('cbo_pro_type').value = '".$data[csf('pro_type')]."';\n";
        echo "document.getElementById('cbo_order_type').value = '".$data[csf('order_type')]."';\n";

        // $update_id = "'".$data[csf('id')]."'";

        //echo "show_list_view(".$update_id.",'job_details_list_view','receive_details','requires/yd_delivery_entry_v2_controller','');\n";
    }
}

if($action=="delivery_dtls_list_view")
{
    $data=explode('_', $data);
    
    $job_no = $data[0];
    $receive_no = $data[1];

    $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');

    $sql = "select a.id, a.order_type, b.id as receive_dtls_id, b.style_ref, b.sales_order_no, b.sales_order_id, b.buyer_buyer, b.lot, b.count_type, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.yd_color_id, b.no_bag, b.cone_per_bag, b.uom, b.order_quantity, b.process_loss, b.adj_type, b.total_order_quantity, b.receive_qty, d.dtls_id, b.job_no from yd_store_receive_mst a, yd_store_receive_dtls b, yd_store_receive_mst c, yd_store_receive_dtls d where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yd_receive='$receive_no' and a.entry_form=295 and b.job_no='$job_no' and c.id=d.mst_id and b.dtls_id=d.id and c.status_active=1 and c.is_deleted=0 and c.entry_form=571 and d.is_deleted=0 and d.status_active=1";

    $sql1 = "select b.dtls_id, sum(b.receive_qty) as receive_qty from yd_store_receive_mst a, yd_store_receive_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no='$job_no' and a.entry_form=295 group by b.dtls_id";

    $sql2 = "select a.yd_job, b.rate, b.id, a.currency_id from yd_ord_mst a, yd_ord_dtls b where a.yd_job='$job_no' and a.status_active=1 and a.is_deleted=0 and a.id=b.mst_id";

    $result = sql_select($sql);

    $receive_result = sql_select($sql1);

    $order_result = sql_select($sql2);

    $receive_array = array();
    $order_arr = array();

    foreach($receive_result as $data)
    {
        $receive_array[$data[csf('dtls_id')]]+= $data[csf('receive_qty')];
    }

    foreach($order_result as $data)
    {
        $order_arr[$data[csf('yd_job')]][$data[csf('id')]]['rate'] = $data[csf('rate')];
        $order_arr[$data[csf('yd_job')]][$data[csf('id')]]['currency'] = $data[csf('currency_id')];
    }

    $tblRow = 1;
    foreach($result as $data)
    {

        if($data[csf('order_type')]==1)
        {
            $current_delivery = "placeholder='".$data[csf('order_quantity')]."'";
            $order_qty = $data[csf('order_quantity')];
        }
        elseif($data[csf('order_type')]==2)
        {
            $current_delivery = "placeholder='".$data[csf('total_order_quantity')]."'";
            $order_qty = $data[csf('total_order_quantity')];
        }

        $receive_qty = $receive_array[$data[csf('receive_dtls_id')]];

        $balance = $data[csf('receive_qty')];
    ?>
        <tr id="row_<?php echo $tblRow;?>">
            <td align="center" width="80">
                <input style="width:80px" readonly class="text_boxes" type="text" name="txtstyleRef[]" id="txtstyleRef_<?php echo $tblRow;?>" value="<?php echo $data[csf('style_ref')];?>">
            </td>
            <td width="60">
                <input style="width:60px" readonly class="text_boxes" type="text" name="txtsaleOrder[]" id="txtsaleOrder_<?php echo $tblRow;?>" value="<?php echo $data[csf('sales_order_no')];?>">
                <input  class="text_boxes_numeric" readonly type="hidden" name="txtsaleOrderID[]" id="txtsaleOrderID_<?php echo $tblRow;?>" value="<?php echo $data[csf('sales_order_id')];?>">
            </td>
            <td width="60">
                <input style="width:60px" readonly class="text_boxes" type="text" name="buyerBuyer[]" id="buyerBuyer_<?php echo $tblRow;?>" value="<?php echo $data[csf('buyer_buyer')];?>">
            </td>
            <td width="80">
                <input style="width:80px" readonly class="text_boxes" type="text" name="txtlot[]" id="txtlot_<?php echo $tblRow;?>" value="<?php echo $data[csf('lot')];?>">
            </td>
            <td width="80">
                <input style="width:80px" readonly class="text_boxes" type="text" name="txtGrayLot[]" id="txtGrayLot_<?php echo $tblRow;?>" value="<?php echo $data[csf('lot')];?>">
                <input readonly class="text_boxes" type="hidden" name="txtHiddenGrayLot[]" id="txtHiddenGrayLot_<?php echo $tblRow;?>" value="<?php echo $data[csf('lot')];?>">
            </td>
            <td width="60">
                <input class="text_boxes" readonly type="hidden" name="txtcountTypeId[]" id="txtcountTypeId_<?php echo $tblRow;?>" value="<?php echo $data[csf('count_type')];?>">
                <?
                $count_type_arr = array(1 => "Single",2 => "Double");
                echo create_drop_down( "txtcountType_".$tblRow, 60, $count_type_arr,'', 1, '--- Select---', $data[csf('count_type')], "",1,'','','','','','',"txtcountType[]");
                ?>
            </td>
            <td width="60">
                <input class="text_boxes" readonly type="hidden" name="txtcountId[]" id="txtcountId_<?php echo $tblRow;?>" value="<?php echo $data[csf('count_id')];?>">
                <?
                   if ($within_group==2) 
                   {
                        
                        $sql="select distinct(b.id) as id,b.yarn_count from lib_yarn_count b where b.status_active=1 and b.is_deleted=0";
                   }
                   else
                   {
                        
                        $sql="select distinct(b.id) as id,b.yarn_count from lib_yarn_count b where b.status_active=1 and b.is_deleted=0";
                   }

                    echo create_drop_down( "cboCount_".$tblRow, 60, $sql,"id,yarn_count", 1, "-- Select --",$data[csf('count_id')],"",1,'','','','','','',"cboCount[]"); 
                ?>
            </td>
            <td width="80">
                <input class="text_boxes" readonly type="hidden" name="cboYarnTypeId[]" id="cboYarnTypeId_<?php echo $tblRow;?>" value="<?php echo $data[csf('yarn_type_id')];?>">

                <? echo create_drop_down( "cboYarnType_".$tblRow, 80, $yarn_type,"", 1, "-- Select --",$data[csf('yarn_type_id')],"",1,'','','','','','',"cboYarnType[]"); ?>
            </td>
            <td width="100">
                <input class="text_boxes" readonly type="hidden" name="txtydCompositionId[]" id="txtydCompositionId_<?php echo $tblRow;?>" value="<?php echo $data[csf('yarn_composition_id')];?>">
                <? echo create_drop_down( "cboComposition_".$tblRow, 100, $composition,"", 1, "-- Select --",$data[csf('yarn_composition_id')],"",1,'','','','','','',"cboComposition[]"); ?>
            </td>
            <td width="80">
                <input class="text_boxes" type="hidden" name="txtYarnColorId[]" id="txtYarnColorId_<?php echo $tblRow;?>" value="<?php echo $data[csf('yd_color_id')]; ?>">
                <? echo create_drop_down( "txtYarnColor_".$tblRow, 80, $color_arr,"", 1, "-- Select --",$data[csf('yd_color_id')],"",1,'','','','','','',"txtYarnColor[]"); ?>
            </td>
            <td width="40">
                <input style="width:40px" class="text_boxes_numeric" type="text" name="txtnoBag[]" id="txtnoBag_<?php echo $tblRow;?>" value="<?php echo $data[csf('no_bag')];?>">
            </td>
            <td width="50">
                <input style="width:50px" class="text_boxes_numeric" type="text" name="txtConeBag[]" id="txtConeBag_<?php echo $tblRow;?>" value="<?php echo $data[csf('cone_per_bag')];?>">
            </td>
            <td width="50">
                <input class="text_boxes" type="hidden" name="cboUomId[]" id="cboUomId_<?php echo $tblRow;?>" value="<?php echo $data[csf('uom')];?>">

                <? echo create_drop_down( "cboUom_".$tblRow, 50, $unit_of_measurement,"", 1, "-- Select --",$data[csf('uom')],"", 1,'','','','','','',"cboUom[]"); ?>
            </td>
            <td width="50">
                <input style="width:50px" readonly class="text_boxes_numeric" type="text" name="txtOrderqty[]" id="txtOrderqty_<?php echo $tblRow;?>" value="<?php echo $data[csf('order_quantity')];?>">
                <input readonly class="text_boxes_numeric" type="hidden" name="txtHiddenOrderqty[]" id="txtHiddenOrderqty_<?php echo $tblRow;?>" value="<?php echo $data[csf('order_quantity')];?>">
            </td>
            <td width="50">
                <input style="width:50px" readonly class="text_boxes_numeric" type="text" name="txtProcessLoss[]" id="txtProcessLoss_<?php echo $tblRow;?>" value="<?php echo $data[csf('process_loss')];?>">
                <input readonly class="text_boxes_numeric" type="hidden" name="txtHiddenProcessLoss[]" id="txtHiddenProcessLoss_<?php echo $tblRow;?>" value="<?php echo $data[csf('process_loss')];?>">
            </td>
            <td width="80">
                <input  readonly class="text_boxes" type="hidden" name="txtadjTypeId[]" id="txtadjTypeId_<?php echo $tblRow;?>" value="<?php echo $data[csf('adj_type')];?>">
                <?
                    echo create_drop_down( "txtadjType_".$tblRow, 80, $adj_type_arr,'', 1, '--- Select---',$data[csf('adj_type')], "",1,'','','','','','',"txtadjType[]");
                ?>
            </td>
            <td width="50">
                <input style="width:50px" readonly class="text_boxes_numeric" type="text" name="txtTotalqty[]" id="txtTotalqty_<?php echo $tblRow;?>" value="<?php echo $data[csf('total_order_quantity')];?>">
                <input readonly class="text_boxes_numeric" type="hidden" name="txtHiddenTotalqty[]" id="txtHiddenTotalqty_<?php echo $tblRow;?>" value="<?php echo $data[csf('total_order_quantity')];?>">
            </td>
            <td width="50">
                <input style="width:50px" readonly class="text_boxes_numeric" type="text" name="txtTotalDeliveryqty[]" id="txtTotalDeliveryqty_<?php echo $tblRow;?>" value="<?php echo $data[csf('receive_qty')];?>">
                <input readonly class="text_boxes_numeric" type="hidden" name="txtHiddenTotalDeliveryqty[]" id="txtHiddenTotalDeliveryqty_<?php echo $tblRow;?>" value="<?php echo $data[csf('receive_qty')];?>">
            </td>
            <td width="50">
                <input style="width:50px" readonly class="text_boxes_numeric" type="text" name="txtPreviousBillqty[]" id="txtPreviousBillqty_<?php echo $tblRow;?>" value="<?php echo $receive_qty;?>">
                <input readonly class="text_boxes_numeric" type="hidden" name="txtHiddenPreviousBillqty[]" id="txtHiddenPreviousBillqty_<?php echo $tblRow;?>" value="<?php echo $receive_qty;?>">
            </td>
            <td width="50">
                <input style="width:50px" class="text_boxes_numeric" type="text" name="txtbalanceqty[]" id="txtbalanceqty_<?php echo $tblRow;?>" value="<?php echo $balance;?>" readonly>
                <input readonly class="text_boxes_numeric" type="hidden" name="txtHiddenbalanceqty[]" id="txtHiddenbalanceqty_<?php echo $tblRow;?>" value="<?php echo $balance;?>">
            </td>
            <td width="50">
                <input style="width:50px" class="text_boxes_numeric" type="text" onkeyup="calculate_bill_ammount(<?php echo $tblRow;?>,this.value);" name="txtReceiveQty[]" id="txtReceiveQty_<?php echo $tblRow;?>" value="" >
                <input class="text_boxes_numeric" type="hidden" name="hiddenOrderQty[]" id="hiddenOrderQty_<?php echo $tblRow;?>" value="<?php echo $order_qty;?>">
                <input class="text_boxes_numeric" type="hidden" name="txtHiddenDeliveryId[]" id="txtHiddenDeliveryId_<?php echo $tblRow;?>" value="">
                <input class="text_boxes_numeric" type="hidden" name="txtHiddenDtlsId[]" id="txtHiddenDtlsId_<?php echo $tblRow;?>" value="<?php echo $data[csf('receive_dtls_id')];?>">
            </td>
            <td width="50">
                <input style="width:50px" readonly class="text_boxes_numeric" type="text" name="rate[]" id="rate_<?php echo $tblRow;?>" value="<?php echo $order_arr[$data[csf('job_no')]][$data[csf('dtls_id')]]['rate'];?>">
                <input class="text_boxes" type="hidden" name="txtHiddenCurrencyId[]" id="txtHiddenCurrencyId_<?php echo $tblRow;?>" value="<?php echo $order_arr[$data[csf('job_no')]][$data[csf('dtls_id')]]['currency'];?>">
            </td>
            <td width="50">
                <input style="width:50px" readonly class="text_boxes_numeric" type="text" name="billAmount[]" id="billAmount_<?php echo $tblRow;?>" readonly value="">
            </td>
        </tr>
    <?php
        $tblRow++;
    }

    exit();

}
