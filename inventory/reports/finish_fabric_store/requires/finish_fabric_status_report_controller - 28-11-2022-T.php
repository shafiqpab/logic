<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="job_no_popup")
{
    echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
    extract($_REQUEST);
    ?>

    <script>
        var selected_id = new Array, selected_name = new Array();

        function check_all_data()
        {
            var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
            tbl_row_count = tbl_row_count - 1;

            for( var i = 1; i <= tbl_row_count; i++ )
            {
                $('#tr_'+i).trigger('click'); 
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
            var str=id.split("_");
            toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
            var strdt=str[2];
            str=str[1];

            if( jQuery.inArray(  str , selected_id ) == -1 ) {
                selected_id.push( str );
                selected_name.push( strdt );
            }
            else {
                for( var i = 0; i < selected_id.length; i++ ) {
                    if( selected_id[i] == str  ) break;
                }
                selected_id.splice( i, 1 );
                selected_name.splice( i,1 );
            }
            var id = '';
            var ddd='';
            for( var i = 0; i < selected_id.length; i++ ) {
                id += selected_id[i] + ',';
                ddd += selected_name[i] + ',';
            }
            id = id.substr( 0, id.length - 1 );
            ddd = ddd.substr( 0, ddd.length - 1 );
            $('#hide_job_id').val( id );
            $('#hide_job_no').val( ddd );
        }
    </script>

    </head>
        <body>
            <div align="center">
                <form name="styleRef_form" id="styleRef_form">
                    <fieldset style="width:580px;">
                        <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                            <thead>
                                <th>Buyer</th>
                                <th>Job Year</th>
                                <th>Search By</th>
                                <th id="search_by_td_up" width="170">Please Enter Job No</th>
                                <th>
                                    <input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">
                                    <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                                </th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td align="center">
                                        <?
                                        echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
                                        ?>
                                    </td>
                                    <td>
                                        <? echo create_drop_down( "cbo_job_year", 70, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?>
                                    </td>
                                    <td align="center">
                                        <?
                                        $search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Order",4=>"Booking");
                                        $dd="change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../') ";
                                        echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                                        ?>
                                    </td>
                                    <td align="center" id="search_by_td">
                                        <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                                    </td>
                                    <td align="center">
                                        <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_job_year').value+'**'+'<? echo $cbo_type; ?>', 'create_job_no_search_list_view', 'search_div', 'finish_fabric_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div style="margin-top:15px" id="search_div"></div>
                    </fieldset>
                </form>
            </div>
        </body>
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_job_no_search_list_view")
{
    $data=explode('**',$data);
    $company_id=$data[0];
    $year_id=$data[4];
    $booking_type=$data[5];

    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );

    if($data[1]==0)
    {
        if ($_SESSION['logic_erp']["data_level_secured"]==1)
        {
            if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
        }
        else
        {
            $buyer_id_cond="";
        }
    }
    else
    {
        $buyer_id_cond=" and buyer_name=$data[1]";
    }

    $search_by=$data[2];
    $search_string="%".trim($data[3])."%";

    if($search_by==1) 
    {
        $search_field="a.job_no";
    }
    else if($search_by==2)
    {
       $search_field="a.style_ref_no"; 
    }
    else if($search_by==3)
    {
        $search_field="b.po_number"; 
    }
    else 
    {
        $search_field="c.booking_no";
    }

    if ($booking_type==1) // Bulk // Main Fabric
    {
       $booking_type_cond="and c.booking_type=1 and c.is_short=2";
    }
    else if($booking_type==2) // Sample With Order
    {
        $booking_type_cond="and c.booking_type=4 and c.is_short=2";
    }
    else if($booking_type==3) // Sample Non Order
    {
        die('Only for Order');
    }
    else if($booking_type==4) // Short
    {
        $booking_type_cond="and c.booking_type=1 and c.is_short=1";
    }

    if($db_type==0)
    {
        if($year_id!=0) $year_search_cond=" and year(a.insert_date)=$year_id"; else $year_search_cond="";
        $year_cond= "year(a.insert_date)as year";
    }
    else if($db_type==2)
    {
        if($year_id!=0) $year_search_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id"; else $year_search_cond="";
        $year_cond= "TO_CHAR(a.insert_date,'YYYY') as year";
    }

    /*$sql= "SELECT id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_cond 
    from wo_po_details_master
    where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_search_cond $month_cond order by job_no DESC";*/
    
    $sql= "SELECT a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $year_cond, c.booking_no, c.booking_type, b.po_number
    from wo_po_details_master a, wo_po_break_down  b, wo_booking_mst c
    where  a.job_no=b.job_no_mst and a.job_no=c.job_no and b.job_no_mst=c.job_no and c.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_search_cond $month_cond $booking_type_cond
    group by a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, a.insert_date, c.booking_no, c.booking_type, b.po_number order by a.job_no desc";
    // echo $sql;
    $arr=array (0=>$buyer_arr);
    echo create_list_view("tbl_list_search", "Buyer Name,Year,Job No,Style,Booking No,Po Number", "100,60,60,90,90,80","620","270",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "buyer_name,0,0,0,0,0", $arr , "buyer_name,year,job_no_prefix_num,style_ref_no,booking_no,po_number", "",'','0,0,0,0,0,0','',1) ;
    exit();
}

if($action=="style_no_popup")
{
    echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
    extract($_REQUEST);
    ?>

    <script>
        var selected_id = new Array, selected_name = new Array();

        function check_all_data()
        {
            var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
            tbl_row_count = tbl_row_count - 1;

            for( var i = 1; i <= tbl_row_count; i++ )
            {
                $('#tr_'+i).trigger('click'); 
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
            var str=id.split("_");
            toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
            var strdt=str[2];
            str=str[1];

            if( jQuery.inArray(  str , selected_id ) == -1 ) {
                selected_id.push( str );
                selected_name.push( strdt );
            }
            else {
                for( var i = 0; i < selected_id.length; i++ ) {
                    if( selected_id[i] == str  ) break;
                }
                selected_id.splice( i, 1 );
                selected_name.splice( i,1 );
            }
            var id = '';
            var ddd='';
            for( var i = 0; i < selected_id.length; i++ ) {
                id += selected_id[i] + ',';
                ddd += selected_name[i] + ',';
            }
            id = id.substr( 0, id.length - 1 );
            ddd = ddd.substr( 0, ddd.length - 1 );
            $('#hide_job_id').val( id );
            $('#hide_job_no').val( ddd );
        }
    </script>

    </head>
        <body>
            <div align="center">
                <form name="styleRef_form" id="styleRef_form">
                    <fieldset style="width:580px;">
                        <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                            <thead>
                                <th>Buyer</th>
                                <th>Job Year</th>
                                <th>Search By</th>
                                <th id="search_by_td_up" width="170">Please Enter Job No</th>
                                <th>
                                    <input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">
                                    <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                                </th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td align="center">
                                        <?
                                        echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
                                        ?>
                                    </td>
                                    <td>
                                        <? echo create_drop_down( "cbo_job_year", 70, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?>
                                    </td>
                                    <td align="center">
                                        <?
                                        $search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Order",4=>"Booking");
                                        $dd="change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../') ";
                                        echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                                        ?>
                                    </td>
                                    <td align="center" id="search_by_td">
                                        <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                                    </td>
                                    <td align="center">
                                        <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_job_year').value+'**'+'<? echo $cbo_type; ?>', 'create_style_no_search_list_view', 'search_div', 'finish_fabric_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div style="margin-top:15px" id="search_div"></div>
                    </fieldset>
                </form>
            </div>
        </body>
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_style_no_search_list_view")
{
    $data=explode('**',$data);
    $company_id=$data[0];
    $year_id=$data[4];
    $booking_type=$data[5];

    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );

    if($data[1]==0)
    {
        if ($_SESSION['logic_erp']["data_level_secured"]==1)
        {
            if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
        }
        else
        {
            $buyer_id_cond="";
        }
    }
    else
    {
        $buyer_id_cond=" and buyer_name=$data[1]";
    }

    $search_by=$data[2];
    $search_string=trim($data[3]);

    if($search_by==1) 
    {
        $search_field="a.job_no";
        $smn_search_field="";
    }
    else if($search_by==2)
    {
       $search_field="a.style_ref_no"; 
       $smn_search_field="c.style_ref_no "; 
    }
    else if($search_by==3)
    {
        $search_field="b.po_number"; 
        $smn_search_field="";
    }
    else 
    {
        $search_field="c.booking_no";
        $smn_search_field="a.booking_no"; 
    }

    if ($search_string!="") 
    {
        if ($booking_type==3) 
        {
            $search_string_cond= "and $smn_search_field like '%$search_string%'";
        }
        else
        {
            $search_string_cond= "and $search_field like '%$search_string%'";
        }
    }
    // echo $search_string_cond;die;
    if ($booking_type==1) // Bulk // Main Fabric
    {
       $booking_type_cond="and c.booking_type=1 and c.is_short=2";
    }
    else if($booking_type==2) // Sample With Order
    {
        $booking_type_cond="and c.booking_type=4 and c.is_short=2";
    }
    else if($booking_type==4) // Short
    {
        $booking_type_cond="and c.booking_type=1 and c.is_short=1";
    }

    if($db_type==0)
    {
        if($year_id!=0) $year_search_cond=" and year(a.insert_date)=$year_id"; else $year_search_cond="";
        $year_cond= "year(a.insert_date)as year";
    }
    else if($db_type==2)
    {
        if($year_id!=0) $year_search_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id"; else $year_search_cond="";
        $year_cond= "TO_CHAR(a.insert_date,'YYYY') as year";
    }

    $style_ref_no_lib=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );

    if ($booking_type==3) // Sample Non Order
    {
        $sql= "SELECT a.id as booking_id, a.booking_no_prefix_num, a.booking_no,a.booking_date, a.company_id,a.buyer_id as buyer_name,a.item_category,a.grouping, a.fabric_source, a.supplier_id, b.style_id, b.style_des, c.style_ref_no, $year_cond 
        from wo_non_ord_samp_booking_mst  a, wo_non_ord_samp_booking_dtls b, sample_development_mst c 
        where  a.booking_no=b.booking_no and b.style_id=c.id  and a.booking_type=4 and a.item_category=2 and a.entry_form_id=140 and  b.entry_form_id=140 and b.status_active=1 and b.is_deleted=0 
        and a.company_id=$company_id $search_string_cond $buyer_id_cond $year_search_cond $booking_type_cond and a.booking_type=4 and  a.status_active=1 and a.is_deleted=0 
        group by a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.item_category, a.grouping, a.fabric_source, a.supplier_id, b.style_id, b.style_des, c.style_ref_no, a.insert_date order by a.booking_no desc";
    }
    else
    {
        $sql= "SELECT a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $year_cond, c.id as booking_id, c.booking_no, c.booking_type, b.po_number
        from wo_po_details_master a, wo_po_break_down  b, wo_booking_mst c
        where  a.job_no=b.job_no_mst and a.job_no=c.job_no and b.job_no_mst=c.job_no and c.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id $search_string_cond $buyer_id_cond $year_search_cond $booking_type_cond
        group by a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, a.insert_date, c.booking_no, c.id, c.booking_type, b.po_number order by a.job_no desc";
    }
    
    $arr=array (0=>$buyer_arr);
    echo create_list_view("tbl_list_search", "Buyer Name,Year,Job No,Style,Booking No,Po Number", "100,60,60,90,90,80","620","270",0, $sql , "js_set_value", "style_ref_no,style_ref_no", "", 1, "buyer_name,0,0,0,0,0", $arr , "buyer_name,year,job_no_prefix_num,style_ref_no,booking_no,po_number", "",'','0,0,0,0,0,0','',1) ;
    exit();
}

if($action=="booking_no_popup")
{
    echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
    extract($_REQUEST);
    ?>

    <script>
        var selected_id = new Array, selected_name = new Array();

        function check_all_data()
        {
            var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
            tbl_row_count = tbl_row_count - 1;

            for( var i = 1; i <= tbl_row_count; i++ )
            {
                $('#tr_'+i).trigger('click'); 
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
            var str=id.split("_");
            toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
            var strdt=str[2];
            str=str[1];

            if( jQuery.inArray(  str , selected_id ) == -1 ) {
                selected_id.push( str );
                selected_name.push( strdt );
            }
            else {
                for( var i = 0; i < selected_id.length; i++ ) {
                    if( selected_id[i] == str  ) break;
                }
                selected_id.splice( i, 1 );
                selected_name.splice( i,1 );
            }
            var id = '';
            var ddd='';
            for( var i = 0; i < selected_id.length; i++ ) {
                id += selected_id[i] + ',';
                ddd += selected_name[i] + ',';
            }
            id = id.substr( 0, id.length - 1 );
            ddd = ddd.substr( 0, ddd.length - 1 );
            $('#hide_job_id').val( id );
            $('#hide_job_no').val( ddd );
        }
    </script>

    </head>
        <body>
            <div align="center">
                <form name="styleRef_form" id="styleRef_form">
                    <fieldset style="width:580px;">
                        <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                            <thead>
                                <th>Buyer</th>
                                <th>Job Year</th>
                                <th>Search By</th>
                                <th id="search_by_td_up" width="170">Please Enter Job No</th>
                                <th>
                                    <input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">
                                    <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                                </th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td align="center">
                                        <?
                                        echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
                                        ?>
                                    </td>
                                    <td>
                                        <? echo create_drop_down( "cbo_job_year", 70, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?>
                                    </td>
                                    <td align="center">
                                        <?
                                        $search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Order",4=>"Booking");
                                        $dd="change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../') ";
                                        echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                                        ?>
                                    </td>
                                    <td align="center" id="search_by_td">
                                        <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                                    </td>
                                    <td align="center">
                                        <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_job_year').value+'**'+'<? echo $cbo_type; ?>', 'create_booking_no_search_list_view', 'search_div', 'finish_fabric_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div style="margin-top:15px" id="search_div"></div>
                    </fieldset>
                </form>
            </div>
        </body>
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_booking_no_search_list_view")
{
    $data=explode('**',$data);
    $company_id=$data[0];
    $year_id=$data[4];
    $booking_type=$data[5];

    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );

    if($data[1]==0)
    {
        if ($_SESSION['logic_erp']["data_level_secured"]==1)
        {
            if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
        }
        else
        {
            $buyer_id_cond="";
        }
    }
    else
    {
        $buyer_id_cond=" and buyer_name=$data[1]";
    }

    $search_by=$data[2];
    $search_string=trim($data[3]);

    if($search_by==1) 
    {
        $search_field="a.job_no";
        $smn_search_field="";
    }
    else if($search_by==2)
    {
       $search_field="a.style_ref_no"; 
       $smn_search_field="c.style_ref_no "; 
    }
    else if($search_by==3)
    {
        $search_field="b.po_number"; 
        $smn_search_field="";
    }
    else 
    {
        $search_field="c.booking_no";
        $smn_search_field="a.booking_no"; 
    }

    if ($search_string!="") 
    {
        if ($booking_type==3) 
        {
            $search_string_cond= "and $smn_search_field like '%$search_string%'";
        }
        else
        {
            $search_string_cond= "and $search_field like '%$search_string%'";
        }
    }
    // echo $search_string_cond;die;
    if ($booking_type==1) // Bulk // Main Fabric
    {
       $booking_type_cond="and c.booking_type=1 and c.is_short=2";
    }
    else if($booking_type==2) // Sample With Order
    {
        $booking_type_cond="and c.booking_type=4 and c.is_short=2";
    }
    else if($booking_type==4) // Short
    {
        $booking_type_cond="and c.booking_type=1 and c.is_short=1";
    }

    if($db_type==0)
    {
        if($year_id!=0) $year_search_cond=" and year(a.insert_date)=$year_id"; else $year_search_cond="";
        $year_cond= "year(a.insert_date)as year";
    }
    else if($db_type==2)
    {
        if($year_id!=0) $year_search_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id"; else $year_search_cond="";
        $year_cond= "TO_CHAR(a.insert_date,'YYYY') as year";
    }

    $style_ref_no_lib=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );

    if ($booking_type==3) // Sample Non Order
    {
        $sql= "SELECT a.id as booking_id, a.booking_no_prefix_num, a.booking_no,a.booking_date, a.company_id,a.buyer_id as buyer_name, a.item_category,a.grouping, a.fabric_source, a.supplier_id, b.style_id, b.style_des, c.style_ref_no, $year_cond 
        from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, sample_development_mst c 
        where  a.booking_no=b.booking_no and b.style_id=c.id  and a.booking_type=4 and a.item_category=2 and a.entry_form_id=140 and  b.entry_form_id=140 and b.status_active=1 and b.is_deleted=0 
        and a.company_id=$company_id $search_string_cond $buyer_id_cond $year_search_cond $booking_type_cond and a.booking_type=4 and  a.status_active=1 and a.is_deleted=0 
        group by a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.item_category, a.grouping, a.fabric_source, a.supplier_id, b.style_id, b.style_des, c.style_ref_no, a.insert_date order by a.booking_no desc";
    }
    else
    {
        $sql= "SELECT a.id, a.job_no, a.job_no_prefix_num, c.booking_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $year_cond, c.id as booking_id, c.booking_no, c.booking_type, b.po_number
        from wo_po_details_master a, wo_po_break_down  b, wo_booking_mst c
        where  a.job_no=b.job_no_mst and a.job_no=c.job_no and b.job_no_mst=c.job_no and c.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id $search_string_cond $buyer_id_cond $year_search_cond $booking_type_cond
        group by a.id, a.job_no, a.job_no_prefix_num, c.booking_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, a.insert_date, c.booking_no, c.id, c.booking_type, b.po_number order by a.job_no desc";
    }
    
    /*$sql= "SELECT a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $year_cond, c.id as booking_id, c.booking_no, c.booking_type, b.po_number
    from wo_po_details_master a, wo_po_break_down  b, wo_booking_mst c
    where  a.job_no=b.job_no_mst and a.job_no=c.job_no and b.job_no_mst=c.job_no and c.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_search_cond $booking_type_cond
    group by a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, a.insert_date, c.booking_no, c.id, c.booking_type, b.po_number order by a.job_no desc";*/
    // echo $sql;
    $arr=array (0=>$buyer_arr);
    echo create_list_view("tbl_list_search", "Buyer Name,Year,Job No,Style,Booking No,Po Number", "100,60,60,90,90,80","620","270",0, $sql , "js_set_value", "booking_id,booking_no_prefix_num", "", 1, "buyer_name,0,0,0,0,0", $arr , "buyer_name,year,job_no_prefix_num,style_ref_no,booking_no,po_number", "",'','0,0,0,0,0,0','',1) ;
    exit();
}

if($action=="order_no_popup")
{
    echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
    extract($_REQUEST);
    ?>

    <script>
        var selected_id = new Array, selected_name = new Array();

        function check_all_data()
        {
            var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
            tbl_row_count = tbl_row_count - 1;

            for( var i = 1; i <= tbl_row_count; i++ )
            {
                $('#tr_'+i).trigger('click'); 
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
            var str=id.split("_");
            toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
            var strdt=str[2];
            str=str[1];

            if( jQuery.inArray(  str , selected_id ) == -1 ) {
                selected_id.push( str );
                selected_name.push( strdt );
            }
            else {
                for( var i = 0; i < selected_id.length; i++ ) {
                    if( selected_id[i] == str  ) break;
                }
                selected_id.splice( i, 1 );
                selected_name.splice( i,1 );
            }
            var id = '';
            var ddd='';
            for( var i = 0; i < selected_id.length; i++ ) {
                id += selected_id[i] + ',';
                ddd += selected_name[i] + ',';
            }
            id = id.substr( 0, id.length - 1 );
            ddd = ddd.substr( 0, ddd.length - 1 );
            $('#hide_job_id').val( id );
            $('#hide_job_no').val( ddd );
        }
    </script>

    </head>
        <body>
            <div align="center">
                <form name="styleRef_form" id="styleRef_form">
                    <fieldset style="width:580px;">
                        <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                            <thead>
                                <th>Buyer</th>
                                <th>Job Year</th>
                                <th>Search By</th>
                                <th id="search_by_td_up" width="170">Please Enter Job No</th>
                                <th>
                                    <input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">
                                    <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                                </th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td align="center">
                                        <?
                                        echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
                                        ?>
                                    </td>
                                    <td>
                                        <? echo create_drop_down( "cbo_job_year", 70, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?>
                                    </td>
                                    <td align="center">
                                        <?
                                        $search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Order",4=>"Booking");
                                        $dd="change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../') ";
                                        echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                                        ?>
                                    </td>
                                    <td align="center" id="search_by_td">
                                        <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                                    </td>
                                    <td align="center">
                                        <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_job_year').value+'**'+'<? echo $cbo_type; ?>', 'create_order_no_search_list_view', 'search_div', 'finish_fabric_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div style="margin-top:15px" id="search_div"></div>
                    </fieldset>
                </form>
            </div>
        </body>
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_order_no_search_list_view")
{
    $data=explode('**',$data);
    $company_id=$data[0];
    $year_id=$data[4];
    $booking_type=$data[5];

    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );

    if($data[1]==0)
    {
        if ($_SESSION['logic_erp']["data_level_secured"]==1)
        {
            if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
        }
        else
        {
            $buyer_id_cond="";
        }
    }
    else
    {
        $buyer_id_cond=" and buyer_name=$data[1]";
    }

    $search_by=$data[2];
    $search_string="%".trim($data[3])."%";

    if($search_by==1) 
    {
        $search_field="a.job_no";
    }
    else if($search_by==2)
    {
       $search_field="a.style_ref_no"; 
    }
    else if($search_by==3)
    {
        $search_field="b.po_number"; 
    }
    else 
    {
        $search_field="c.booking_no";
    }

    if ($booking_type==1) // Bulk // Main Fabric
    {
       $booking_type_cond="and c.booking_type=1 and c.is_short=2";
    }
    else if($booking_type==2) // Sample With Order
    {
        $booking_type_cond="and c.booking_type=4 and c.is_short=2";
    }
    else if($booking_type==3) // Sample Non Order
    {
        die('Only for Order');
    }
    else if($booking_type==4) // Short
    {
        $booking_type_cond="and c.booking_type=1 and c.is_short=1";
    }

    if($db_type==0)
    {
        if($year_id!=0) $year_search_cond=" and year(a.insert_date)=$year_id"; else $year_search_cond="";
        $year_cond= "year(a.insert_date)as year";
    }
    else if($db_type==2)
    {
        if($year_id!=0) $year_search_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id"; else $year_search_cond="";
        $year_cond= "TO_CHAR(a.insert_date,'YYYY') as year";
    }
    
    $sql= "SELECT a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $year_cond, c.booking_no, c.booking_type, b.id as order_id, b.po_number
    from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c
    where  a.job_no=b.job_no_mst and a.job_no=c.job_no and b.job_no_mst=c.job_no and c.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_search_cond $month_cond $booking_type_cond
    group by a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, a.insert_date, c.booking_no, c.booking_type, b.po_number, b.id order by a.job_no desc";
    // echo $sql;
    $arr=array (0=>$buyer_arr);
    echo create_list_view("tbl_list_search", "Buyer Name,Year,Job No,Style,Booking No,Po Number", "100,60,60,90,90,80","620","270",0, $sql , "js_set_value", "order_id,po_number", "", 1, "buyer_name,0,0,0,0,0", $arr , "buyer_name,year,job_no_prefix_num,style_ref_no,booking_no,po_number", "",'','0,0,0,0,0,0','',1) ;
    exit();
}

if($action=="generated_report")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    $company = str_replace("'","",$cbo_company_name);
    $year = str_replace("'","",$cbo_year);
    $cbo_type = str_replace("'","",$cbo_type);
    $txt_job_no = str_replace("'","",$txt_job_no);
    $txt_job_id = str_replace("'","",$txt_job_id);
    $txt_style_no = str_replace("'","",$txt_style_no);
    $hidden_style = str_replace("'","",$hidden_style);
    $txt_booking_no = str_replace("'","",$txt_booking_no);
    $hidden_booking_id = str_replace("'","",$hidden_booking_id);
    $txt_order_no = str_replace("'","",$txt_order_no);
    $hidden_order_id = str_replace("'","",$hidden_order_id);
    $cbo_stock_for = str_replace("'","",$cbo_stock_for);
    $cbo_get_upto = str_replace("'","",$cbo_get_upto);
    $txt_days = str_replace("'","",$txt_days);
    $cbo_get_upto_qnty = str_replace("'","",$cbo_get_upto_qnty);
    $txt_qnty = str_replace("'","",$txt_qnty);
    $report_criteria = str_replace("'","",$cbo_report_criteria);
    $txt_date_from = str_replace("'","",$txt_date_from);
    $txt_date_to = str_replace("'","",$txt_date_to);

    if ($cbo_type==1) // Bulk // Main Fabric
    {
       $booking_type_cond="and c.booking_type=1 and c.is_short=2";
    }
    else if($cbo_type==2) // Sample With Order
    {
        $booking_type_cond="and c.booking_type=4 and c.is_short=2";
    }
    else if($cbo_type==4) // Short
    {
        $booking_type_cond="and c.booking_type=1 and c.is_short=1";
    }

    if($txt_job_id == ""){
        $job_no_cond = ($txt_job_no != "") ? " and b.job_no_prefix_num in($txt_job_no)" : "";
    }else{
        $job_no_cond = " and b.id in($txt_job_id)";
    }

    $style_no_cond = ($txt_style_no != "") ? ' and b.style_ref_no in('."'".implode("','", explode(",", $txt_style_no))."'".')' : "";
    // echo $style_no_cond;die;

    if($hidden_booking_id == ""){
        $booking_no_cond = ($txt_booking_no != "") ? " and c.booking_no in('$txt_booking_no')" : "";
    }else{
        $booking_no_cond = " and c.id in($hidden_booking_id)";
    }

    if($hidden_booking_id == ""){
        $smnBooking_no_cond = ($txt_booking_no != "") ? " and e.booking_no in('$txt_booking_no')" : "";
        $smnBooking_no_cond2 = ($txt_booking_no != "") ? " and c.booking_no in('$txt_booking_no')" : "";
    }else{
        $smnBooking_no_cond = " and e.id in($hidden_booking_id)";
        $smnBooking_no_cond2 = " and c.id in($hidden_booking_id)";
    }

    if($hidden_order_id == ""){
        $order_no_cond = ($txt_order_no != "") ? " and a.po_number in('$txt_order_no')" : "";
    }else{
        $order_no_cond = " and a.id in($hidden_order_id)";
    }

    if($db_type==0) 
    {
        $year_field_by="and YEAR(b.insert_date)"; 
        if($year_id!=0) $booking_year = " and year(e.booking_date) = $year_id" ;
        if($year_id!=0) $booking_year2 = " and year(c.booking_date) = $year_id" ;
    }
    else if($db_type==2) 
    {
        $year_field_by=" and to_char(b.insert_date,'YYYY')";
        if($year_id!=0) $booking_year=" and TO_CHAR(e.booking_date,'YYYY')=$year_id"; 
        if($year_id!=0) $booking_year2=" and TO_CHAR(c.booking_date,'YYYY')=$year_id"; 
    }
    if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";



    $order_cond="";
    if($cbo_stock_for==1)
    {
        $order_cond=" and a.shiping_status<>3 and a.status_active=1";
    }
    else if($cbo_stock_for==2)
    {
        $order_cond=" and a.status_active=3";
    }
    else if($cbo_stock_for==3)
    {
        $order_cond=" and a.shiping_status=3 and a.status_active=1";
    }
    else
    {
        $order_cond="";
    }

    if($txt_date_from && $txt_date_to)
    {
        if($db_type==0)
        {
            $date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
            $date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
            $dates_cond="and b.transaction_date between '$date_from' and '$date_to'";
            $transfer_date="and a.transfer_date between '$date_from' and '$date_to'";
            
        }
        if($db_type==2)
        {
            $date_from=change_date_format($txt_date_from,'','',1);
            $date_to=change_date_format($txt_date_to,'','',1);
            $dates_cond="and b.transaction_date between '$date_from' and '$date_to'";
            $transfer_date="and a.transfer_date between '$date_from' and '$date_to'";
        }
    }

    $company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
    $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
    $supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
    $color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
    $yarncount_arr=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count"  );
    $store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');

    if ($report_criteria==1) // Receive Issue and Stock
    {
        if ($cbo_type==3) // Sample Non Order
        {
            if ($cbo_type==3 && $txt_style_no !="")
            {
                $search_string="%".trim($txt_style_no)."%";
                $style_sql= "SELECT a.id as booking_id, a.booking_no_prefix_num, a.booking_no, b.style_id, c.style_ref_no 
                from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, sample_development_mst c 
                where  a.booking_no=b.booking_no and b.style_id=c.id  and a.booking_type=4 and a.item_category=2 and a.entry_form_id=140 and  b.entry_form_id=140 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company and c.style_ref_no like '$search_string' and a.booking_type=4 and  a.status_active=1 and a.is_deleted=0
                group by a.id, a.booking_no_prefix_num, a.booking_no, b.style_id, c.style_ref_no order by a.booking_no desc";
                // echo $non_order_sql;die;
                $style_data_arr=sql_select($style_sql);
                foreach($style_data_arr as $row)
                {
                    $booking_id_arr[$row[csf("booking_id")]]=$row[csf("booking_id")];
                    $style_arr[$row[csf("booking_no")]]=$row[csf("style_ref_no")];
                }
                unset($style_data_arr);
                if(count($booking_id_arr) >0 )
                {
                    $booking_id_ids = implode(",", $booking_id_arr);
                    $booking_idCond = $booking_id_cond = "";$booking_idCond2 = $booking_id_cond2 = "";
                    if($db_type==2 && count($booking_id_arr)>999)
                    {
                        $booking_id_arr_chunk=array_chunk($booking_id_arr,999) ;
                        foreach($booking_id_arr_chunk as $chunk_arr)
                        {
                            $booking_idCond.=" e.id in(".implode(",",$chunk_arr).") or ";
                            $booking_idCond2.=" e.id in(".implode(",",$chunk_arr).") or ";
                        }
                        $booking_id_cond.=" and (".chop($booking_idCond,'or ').")";
                        $booking_id_cond2.=" and (".chop($booking_idCond2,'or ').")";
                    }
                    else
                    {
                        $booking_id_cond=" and e.id in($booking_id_ids)";
                        $booking_id_cond2=" and e.id in($booking_id_ids)";
                    }
                }
                // echo $booking_id_cond;die;
            }

            $non_order_recv_query="SELECT * from (SELECT a.entry_form, d.barcode_no, d.po_breakdown_id, d.qnty, b.store_id, c.febric_description_id as detar_id, c.body_part_id, c.color_id, c.gsm, c.width, c.stitch_length, c.machine_dia, c.machine_gg, c.yarn_count, b.cons_uom as uom, 
            max(b.transaction_date) as max_date, b.prod_id, e.booking_no,
            sum(case when b.cons_uom=12 then d.qnty else 0 end) as qntykg, 
            sum(case when b.cons_uom=1 then d.qnty else 0 end) as qntypcs, 
            sum(case when b.cons_uom=27 then d.qnty else 0 end) as qntyyds, 0 as qntykgIn, 0 as qntypcsIn, 0 as qntyydsIn  
            from inv_receive_master a, inv_transaction b, pro_grey_prod_entry_dtls c, wo_non_ord_samp_booking_mst e, pro_roll_details d 
            where a.id = c.mst_id and a.id=b.mst_id and b.id=c.trans_id and a.entry_form in (58) and d.po_breakdown_id = e.id and a.id = d.mst_id and c.id = d.dtls_id and d.entry_form in (58) and d.booking_without_order=1 and c.status_active =1 
            and c.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 and e.company_id=$company $booking_id_cond $smnBooking_no_cond $dates_cond $booking_year
            group by a.entry_form, d.barcode_no, d.po_breakdown_id, d.qnty, b.store_id, c.febric_description_id, c.body_part_id, c.color_id, c.gsm, c.width, c.stitch_length, c.machine_dia, c.machine_gg, c.yarn_count, b.cons_uom, b.prod_id, e.booking_no
            UNION ALL 
            SELECT a.entry_form, d.barcode_no, d.po_breakdown_id, d.qnty, b.to_store as store_id, b.feb_description_id as detar_id, b.body_part_id, to_char(b.color_id) as color_id, b.gsm, b.dia_width as width, 
            b.stitch_length, null as machine_dia, null as machine_gg , b.y_count as  yarn_count, e.unit_of_measure as uom, max(a.transfer_date) as max_date, b.to_prod_id as prod_id, c.booking_no,
            0 as qntykg, 0 as qntypcs, 0 as qntyyds,
            sum(case when e.unit_of_measure=12 then d.qnty else 0 end) as qntykgIn, 
            sum(case when e.unit_of_measure=1 then d.qnty else 0 end) as qntypcsIn, 
            sum(case when e.unit_of_measure=27 then d.qnty else 0 end) as qntyydsIn 
            from inv_item_transfer_mst a, inv_item_transfer_dtls b, wo_non_ord_samp_booking_mst c , pro_roll_details d, product_details_master e
            where a.to_company=$company and a.id=b.mst_id and c.id=a.to_order_id and a.id = d.mst_id and b.id = d.dtls_id and b.to_prod_id=e.id and d.entry_form in (110,180) 
            and c.company_id=$company $booking_id_cond2 $smnBooking_no_cond2 $transfer_date $booking_year2
            and a.transfer_criteria in(6,8) and a.entry_form in (110,180) and a.item_category=13 
            and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
            group by a.entry_form, d.barcode_no, d.po_breakdown_id, d.qnty, b.to_store, b.feb_description_id, b.body_part_id, b.color_id, b.gsm, b.dia_width, b.stitch_length , b.y_count, e.unit_of_measure, b.to_prod_id, c.booking_no) order by detar_id, color_id asc";
            // echo $non_order_recv_query;die;
            // and A.TRANSFER_SYSTEM_ID='RpC-GFOTSTE-21-00001'
            $result=sql_select($non_order_recv_query);

            $con = connect();
            $r_id2=execute_query("delete from tmp_barcode_no where userid=$user_id");
            if($r_id2)
            {
                oci_commit($con);
            }
            if(!empty($result))
            {
                foreach ($result as $row)
                {
                    $barcodeArr[$row[csf("barcode_no")]]        = $row[csf("barcode_no")];
                    $all_booking_no[$row[csf("booking_no")]]    = $row[csf("booking_no")];
                    $booking_id_arr[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
                    $prod_id_arr[$row[csf("prod_id")]]          =$row[csf("prod_id")];

                    $r_id=execute_query("insert into tmp_barcode_no (userid, barcode_no) values ($user_id,".$row[csf('barcode_no')].")");
                    if($r_id) 
                    {
                        $r_id=1;
                    } 
                    else 
                    {
                        echo "insert into tmp_barcode_no (userid, barcode_no) values ($user_id,".$row[csf('barcode_no')].")";
                        oci_rollback($con);
                        die;
                    }
                }
            }
            else
            {
                echo "Data Not Found";
                die;
            }

            if($r_id)
            {
                oci_commit($con);
            }
            else
            {
                oci_rollback($con);
                disconnect($con);
            }

            $barcodeArr = array_filter($barcodeArr);
            if(count($barcodeArr ) >0 ) // production
            {
                $production_sql = sql_select("SELECT b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id, a.prod_id, b.booking_no,c.booking_id, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, c.knitting_source, c.challan_no as production_challan, c.knitting_company, a.yarn_prod_id, a.body_part_id, b.coller_cuff_size
                from pro_grey_prod_entry_dtls a,pro_roll_details b, inv_receive_master c, tmp_barcode_no d 
                where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form in(2,58) and b.entry_form in(2,58)  and a.status_active=1 and b.status_active=1 and b.barcode_no = d.barcode_no and d.userid= $user_id order by c.entry_form desc");

                foreach ($production_sql as $row)
                {
                    $prodBarcodeData[$row[csf("barcode_no")]]["receive_basis"] =$row[csf("receive_basis")];
                    $prodBarcodeData[$row[csf("barcode_no")]]["prog_book"] =$row[csf("booking_no")];
                    $prodBarcodeData[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
                    $prodBarcodeData[$row[csf("barcode_no")]]["color_range_id"] =$row[csf("color_range_id")];
                    $prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"] =$row[csf("yarn_lot")];
                    $prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"] =$row[csf("yarn_count")];
                    $prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"] =$row[csf("yarn_prod_id")];
                    $prodBarcodeData[$row[csf("barcode_no")]]["prod_id"] =$row[csf("prod_id")];
                    $prodBarcodeData[$row[csf("barcode_no")]]["color_id"] =$row[csf("color_id")];
                    $prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"] =$row[csf("febric_description_id")];
                    $prodBarcodeData[$row[csf("barcode_no")]]["gsm"] =$row[csf("gsm")];
                    $prodBarcodeData[$row[csf("barcode_no")]]["width"] =$row[csf("width")];
                    $prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"] =$row[csf("stitch_length")];
                    $prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"] =$row[csf("machine_dia")];
                    $prodBarcodeData[$row[csf("barcode_no")]]["machine_gg"] =$row[csf("machine_gg")];
                    $prodBarcodeData[$row[csf("barcode_no")]]["item_size"] =$row[csf("coller_cuff_size")];
                    $prodBarcodeData[$row[csf("barcode_no")]]["machine_no_id"] =$row[csf("machine_no_id")];
                    $prodBarcodeData[$row[csf("barcode_no")]]["prod_challan"] =$row[csf("production_challan")];
                    $prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"] =$row[csf("knitting_source")];
                    $prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"] =$row[csf("knitting_company")];
                    $prodBarcodeData[$row[csf("barcode_no")]]["body_part_id"] =$row[csf("body_part_id")];
                    $allDeterArr[$row[csf("febric_description_id")]] =$row[csf("febric_description_id")];
                    $allColorArr[$row[csf("color_id")]] =$row[csf("color_id")];
                    $allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];

                    if($row[csf('receive_basis')] == 2 )
                    {
                        $program_id_arr[$row[csf('booking_id')]]=$row[csf('booking_id')];
                    }
                }
                $febric_description_arr = array_filter($allDeterArr);
                if(!empty($febric_description_arr))
                {
                    $ref_febric_description_ids = implode(",", $febric_description_arr);
                    $fabCond = $ref_febric_description_cond = "";
                    if($db_type==2 && count($febric_description_arr)>999)
                    {
                        $ref_febric_description_arr_chunk=array_chunk($febric_description_arr,999) ;
                        foreach($ref_febric_description_arr_chunk as $chunk_arr)
                        {
                            $fabCond.=" a.id in(".implode(",",$chunk_arr).") or ";
                        }
                        $ref_febric_description_cond.=" and (".chop($fabCond,'or ').")";
                    }
                    else
                    {
                        $ref_febric_description_cond=" and a.id in($ref_febric_description_ids)";
                    }

                    $sql_deter="select a.id, a.construction, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id $ref_febric_description_cond and a.status_active!=0 and a.is_deleted!=1  and b.status_active!=0 and b.is_deleted!=1";
                    $deter_array=sql_select($sql_deter);
                    if(count($deter_array)>0)
                    {
                        foreach($deter_array as $row )
                        {
                            if(array_key_exists($row[csf('id')],$composition_arr))
                            {
                                $composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
                            }
                            else
                            {
                                $composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
                            }

                            $constuction_arr[$row[csf('id')]]=$row[csf('construction')];

                            if($row[csf('type_id')]>0)
                            {
                                $type_array[$row[csf('id')]].=$yarn_type[$row[csf('type_id')]].",";
                            }
                        }
                    }
                    unset($deter_array);
                }

                $all_color_arr = array_filter($allColorArr);
                if(!empty($all_color_arr))
                {
                    $all_color_ids = implode(",", $all_color_arr);
                    $colorCond = $all_color_cond = "";
                    if($db_type==2 && count($all_color_arr)>999)
                    {
                        $all_color_chunk=array_chunk($all_color_arr,999) ;
                        foreach($all_color_chunk as $chunk_arr)
                        {
                            $colorCond.=" id in(".implode(",",$chunk_arr).") or ";
                        }
                        $all_color_cond.=" and (".chop($colorCond,'or ').")";
                    }
                    else
                    {
                        $all_color_cond=" and id in($all_color_ids)";
                    }

                    $colorArr=return_library_array( "select id, color_name from lib_color where status_active=1 $all_color_cond", "id", "color_name" );
                }

                if(count($program_id_arr) >0 )
                {

                    $program_ids = implode(",", $program_id_arr);
                    $programCond = $program_id_cond = "";
                    if($db_type==2 && count($program_id_arr)>999)
                    {
                        $program_id_arr_chunk=array_chunk($program_id_arr,999) ;
                        foreach($program_id_arr_chunk as $chunk_arr)
                        {
                            $programCond.=" id in(".implode(",",$chunk_arr).") or ";
                        }
                        $program_id_cond.=" and (".chop($programCond,'or ').")";
                    }
                    else
                    {
                        $program_id_cond=" and id in($program_ids)";
                    }

                    $plan_arr=array();
                    $plan_data=sql_select("select id, machine_dia, machine_gg from ppl_planning_info_entry_dtls where status_active=1 $program_id_cond");
                    foreach($plan_data as $row)
                    {
                        $plan_arr[$row[csf('id')]]=$row[csf('machine_dia')]."X".$row[csf('machine_gg')];
                    }
                    unset($plan_data);
                }

                $yarn_prod_id_arr = array_filter($allYarnProdArr);
                if(count($yarn_prod_id_arr)>0)
                {
                    $yarn_prod_ids = implode(",", $yarn_prod_id_arr);
                    $yarnCond = $yarn_prod_id_cond = "";
                    if($db_type==2 && count($yarn_prod_id_arr)>999)
                    {
                        $yarn_prod_id_arr_chunk=array_chunk($yarn_prod_id_arr,999) ;
                        foreach($yarn_prod_id_arr_chunk as $chunk_arr)
                        {
                            $yarnCond.=" id in(".implode(",",$chunk_arr).") or ";
                        }
                        $yarn_prod_id_cond.=" and (".chop($yarnCond,'or ').")";
                    }
                    else
                    {
                        $yarn_prod_id_cond=" and id in($yarn_prod_ids)";
                    }

                    $yarn_sql=  sql_select("select id, yarn_type,yarn_comp_type1st, brand from product_details_master where status_active = 1 $yarn_prod_id_cond and item_category_id =1");
                    foreach ($yarn_sql as $row)
                    {
                        $yarn_ref[$row[csf("id")]]["type"] = $yarn_type[$row[csf("yarn_type")]];
                        $yarn_ref[$row[csf("id")]]["comp"] = $composition[$row[csf("yarn_comp_type1st")]];
                        $yarn_ref[$row[csf("id")]]["brand"] = $brand_arr[$row[csf("brand")]];
                    }
                }
            }

            $all_booking_no = "'".implode("','", array_filter(array_unique($all_booking_no)))."'";
            $bookCond = $all_booking_no_cond = ""; 
            //$bookCond2 = $all_booking_no_cond2 = "";
            $all_booking_no_arr=explode(",", $all_booking_no);

            if(count(array_filter(explode(",", str_replace("'", "", $all_booking_no))))>0)
            {
                //echo "**".$all_booking_no;
                if($db_type==2 && count($all_booking_no_arr)>999)
                {
                    $all_booking_no_arr_chunk=array_chunk($all_booking_no_arr,999) ;
                    foreach($all_booking_no_arr_chunk as $chunk_arr)
                    {
                        $bookCond.=" a.booking_no in(".implode(",",$chunk_arr).") or "; 
                        //$bookCond2.=" c.booking_no in(".implode(",",$chunk_arr).") or ";  
                    }
                            
                    $all_booking_no_cond.=" and (".chop($bookCond,'or ').")";           
                    //$all_booking_no_cond2.=" and (".chop($bookCond2,'or ').")";           
                    
                }
                else
                {   
                    $all_booking_no_cond=" and a.booking_no in($all_booking_no)";  
                    //$all_booking_no_cond2=" and c.booking_no in($all_booking_no)";  
                }
            }

            $all_booking_ids = implode(",",array_filter(array_unique($booking_id_arr)));
            if($all_booking_ids=="") $all_booking_ids=0;
            $bookingidCond = $all_booking_id_cond = "";$bookingidCond2 = $all_booking_id_cond2 = "";
            $all_po_id_arr=explode(",",$all_booking_ids);
            if($db_type==2 && count($all_po_id_arr)>999)
            {
                $all_po_id_chunk_arr=array_chunk($all_po_id_arr,999) ;
                foreach($all_po_id_chunk_arr as $chunk_arr)
                {
                    $chunk_arr_value=implode(",",$chunk_arr);
                    $bookingidCond.=" a.id in($chunk_arr_value) or ";
                    $bookingidCond2.=" d.from_order_id in($chunk_arr_value) or ";
                }
                $all_booking_id_cond.=" and (".chop($bookingidCond,'or ').")";
                $all_booking_id_cond2.=" and (".chop($bookingidCond2,'or ').")";
            }
            else
            {
                $all_booking_id_cond=" and a.id in($all_booking_ids)";
                $all_booking_id_cond2=" and d.from_order_id in($all_booking_ids)";
            }

            if(count($prod_id_arr) >0 ) // Days In Hand
            {
                $prod_id_ids = implode(",", $prod_id_arr);
                $prod_idCond = $prod_id_id_cond = "";
                if($db_type==2 && count($prod_id_arr)>999)
                {
                    $prod_id_id_arr_chunk=array_chunk($prod_id_arr,999) ;
                    foreach($prod_id_id_arr_chunk as $chunk_arr)
                    {
                        $prod_idCond.=" prod_id in(".implode(",",$chunk_arr).") or ";
                    }
                    $prod_id_id_cond.=" and (".chop($prod_idCond,'or ').")";
                }
                else
                {
                    $prod_id_id_cond=" and prod_id in($prod_id_ids)";
                }

                $transaction_date_array=array();
                $sql_date="SELECT prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where status_active=1 and is_deleted=0 and item_category=13 $prod_id_id_cond group by prod_id";
                $sql_date_result=sql_select($sql_date);
                foreach( $sql_date_result as $row )
                {
                    $transaction_date_array[$row[csf('prod_id')]]['min_date']=$row[csf('min_date')];
                    $transaction_date_array[$row[csf('prod_id')]]['max_date']=$row[csf('max_date')];
                }
                unset($sql_date_result);
            }

            $req_qnty_sql = sql_select("SELECT c.id, a.booking_no,a.lib_yarn_count_deter_id, sum(a.grey_fabric) as req_qnty, b.style_ref_no, b.buyer_name,
            sum(case when a.uom=12 then a.grey_fabric else 0 end) as requQntyKg,
            sum(case when a.uom=1 then a.grey_fabric else 0 end) as requqntyPcs,
            sum(case when a.uom=27 then a.grey_fabric else 0 end) as requQntyYds
            from wo_non_ord_samp_booking_mst c, wo_non_ord_samp_booking_dtls a, sample_development_mst b
            where c.booking_no=a.booking_no and a.style_id=b.id and a.is_deleted=0 $all_booking_no_cond
            group by c.id, a.booking_no,a.lib_yarn_count_deter_id, b.style_ref_no, b.buyer_name order by booking_no desc");
            if(!empty($req_qnty_sql))
            {
                foreach($req_qnty_sql as $row)
                {
                    $non_order_arr[$row[csf("id")]]["buyer_name"]      =$row[csf("buyer_name")];
                    $non_order_arr[$row[csf("id")]]["style_ref_no"]    =$row[csf("style_ref_no")];
                    $non_order_arr[$row[csf("id")]]["booking_no"]      =$row[csf("booking_no")];

                    $requQnty_arr[$row[csf("booking_no")]]['requQntyKg']+=$row[csf('requQntyKg')];
                    $requQnty_arr[$row[csf("booking_no")]]['requqntyPcs']+=$row[csf('requqntyPcs')];
                    $requQnty_arr[$row[csf("booking_no")]]['requQntyYds']+=$row[csf('requQntyYds')];
                }
            }

            // echo "<pre>";print_r($non_order_arr);die;

            $non_order_data_arr=array(); $supplier_wise_recv_summary_arr=array();
            foreach ($result as $row)
            {
                $buyer_id=$non_order_arr[$row[csf("po_breakdown_id")]]["buyer_name"];
                $style_ref_no=$non_order_arr[$row[csf("po_breakdown_id")]]["style_ref_no"];
                $booking_no=$non_order_arr[$row[csf("po_breakdown_id")]]["booking_no"];

                $requQntyKg=$requQnty_arr[$booking_no]["requQntyKg"];
                $requqntyPcs=$requQnty_arr[$booking_no]["requqntyPcs"];
                $requQntyYds=$requQnty_arr[$booking_no]["requQntyYds"];
                
                $machine_dia_gg='';
                if($prodBarcodeData[$row[csf("barcode_no")]]["receive_basis"]==2)
                {
                    $machine_dia_gg=$plan_arr[$prodBarcodeData[$row[csf("barcode_no")]]["booking_id"]];
                }

                $str_ref=$buyer_id."*".$style_ref_no."*".$booking_no."*".$prodBarcodeData[$row[csf("barcode_no")]]["body_part_id"]."*".$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"]."*".$prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"]."*".$prodBarcodeData[$row[csf("barcode_no")]]["width"]."*".$prodBarcodeData[$row[csf("barcode_no")]]["gsm"]."*".$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"]."*".$prodBarcodeData[$row[csf("barcode_no")]]["item_size"];

                $non_order_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['qntykg']+=$row[csf("qntykg")];
                $non_order_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['qntypcs']+=$row[csf("qntypcs")];
                $non_order_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['qntyyds']+=$row[csf("qntyyds")];
                
                $non_order_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref][$row[csf("uom")]]++;
                
                
                $non_order_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['roll_count']++;

                $non_order_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['qntykgIn']+=$row[csf("qntykgIn")];
                $non_order_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['qntypcsIn']+=$row[csf("qntypcsIn")];
                $non_order_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['qntyydsIn']+=$row[csf("qntyydsIn")];
                if ($row[csf("entry_form")]==110 || $row[csf("entry_form")]==180)
                {
                    $non_order_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['roll_countIn']++;
                }

                $non_order_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['requQntyKg']=$requQntyKg;
                $non_order_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['requqntyPcs']=$requqntyPcs;
                $non_order_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['requQntyYds']=$requQntyYds;
                $non_order_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['ex_fac_date']=$ex_fac_date;
                $non_order_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['store_id']=$row[csf("store_id")];
                $non_order_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['prod_id']=$row[csf("prod_id")];
                $non_order_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['entry_form']=$row[csf("entry_form")];

                $supplier_wise_recv_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['RcvKg']+=$row[csf("qntykg")];
                $supplier_wise_recv_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['RcvPcs']+=$row[csf("qntypcs")];
                $supplier_wise_recv_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['RcvYds']+=$row[csf("qntyyds")];

                $supplier_wise_recv_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['RcvKg']+=$row[csf("qntykgIn")];
                $supplier_wise_recv_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['RcvPcs']+=$row[csf("qntypcsIn")];
                $supplier_wise_recv_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['RcvYds']+=$row[csf("qntyydsIn")];

                if ($row[csf("entry_form")]==110 || $row[csf("entry_form")]==180)
                {
                    $supplier_wise_recv_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['RollCount']++;
                }
                else
                {
                    $supplier_wise_recv_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['RollCount']++;
                }
            }
            // echo "<pre>";print_r($non_order_data_arr);die;

            $iss_qty_sql=sql_select("SELECT c.po_breakdown_id, c.barcode_no, e.store_id as store_name, 
                max(e.transaction_date) as max_date, a.unit_of_measure as uom, 
            sum(case when a.unit_of_measure=12 then c.qnty else 0 end) as qntykgIssue,
            sum(case when a.unit_of_measure=1 then c.qnty else 0 end) as qntypcsIssue, 
            sum(case when a.unit_of_measure=27 then c.qnty else 0 end) as qntyydsIssue
            from pro_roll_details c, inv_grey_fabric_issue_dtls d, inv_transaction e, tmp_barcode_no b, product_details_master a
            where c.dtls_id = d.id and c.mst_id = d.mst_id and d.trans_id = e.id and c.barcode_no = b.barcode_no and b.userid= $user_id and e.transaction_type=2 and e.prod_id=a.id
            and c.entry_form=61 and c.status_active=1 and c.is_deleted=0 and c.booking_without_order=1 
            group by c.po_breakdown_id, c.barcode_no, c.qnty, e.store_id, a.unit_of_measure");

            $issue_data_arr=array(); //$color_issue_summary_arr=array();
            foreach ($iss_qty_sql as $row)
            {
                $buyer_id=$non_order_arr[$row[csf("po_breakdown_id")]]["buyer_name"];
                $style_ref_no=$non_order_arr[$row[csf("po_breakdown_id")]]["style_ref_no"];
                $booking_no=$non_order_arr[$row[csf("po_breakdown_id")]]["booking_no"];
                
                $machine_dia_gg='';
                if($prodBarcodeData[$row[csf("barcode_no")]]["receive_basis"]==2)
                {
                    $machine_dia_gg=$plan_arr[$prodBarcodeData[$row[csf("barcode_no")]]["booking_id"]];
                }

                $str_ref2=$buyer_id."*".$style_ref_no."*".$booking_no."*".$prodBarcodeData[$row[csf("barcode_no")]]["body_part_id"]."*".$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"]."*".$prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"]."*".$prodBarcodeData[$row[csf("barcode_no")]]["width"]."*".$prodBarcodeData[$row[csf("barcode_no")]]["gsm"]."*".$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"]."*".$prodBarcodeData[$row[csf("barcode_no")]]["item_size"];
                // echo $str_ref2.'<br>';
                // 65*Norban QC*Norban 3*RpC-Fb-21-00055*1*12*18899*44*160*1.3
                // echo $machine_dia_gg.'<br>';
                $issue_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2]['max_date']=$row[csf("max_date")];
                $issue_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2]['qntykgIssue']+=$row[csf("qntykgIssue")];
                $issue_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2]['qntypcsIssue']+=$row[csf("qntypcsIssue")];
                $issue_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2]['qntyydsIssue']+=$row[csf("qntyydsIssue")];
                $issue_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2]['roll_countIssue']++;

                $issue_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2][$row[csf("uom")]]++;

                /*if ($prodBarcodeData[$row[csf("barcode_no")]]["color_id"]!="") 
                {
                    $color_issue_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]]['colorIssKg']+=$row[csf("qntykgIssue")];
                    $color_issue_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]]['colorIssPcs']+=$row[csf("qntypcsIssue")];
                    $color_issue_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]]['colorIssYds']+=$row[csf("qntyydsIssue")];                
                    $color_issue_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]]['ColorRollCount']++;
                }*/
                if ($prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"]==1 || $prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"]==3) 
                {
                    $supplier_wise_issue_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['IssKg']+=$row[csf("qntykgIssue")];
                    $supplier_wise_issue_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['IssPcs']+=$row[csf("qntypcsIssue")];
                    $supplier_wise_issue_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['IssYds']+=$row[csf("qntyydsIssue")];                
                    $supplier_wise_issue_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['RollCount']++;
                }
            }
            unset($iss_qty_sql);
            // echo "<per>";print_r($issue_data_arr); echo "</per>";

            $iss_rtn_qty_sql=sql_select("SELECT c.po_breakdown_id, c.barcode_no, a.unit_of_measure as uom, max(e.transaction_date) as max_date, 
            sum(case when a.unit_of_measure=12 then c.qnty else 0 end) as qntykgIssueRtn, 
            sum(case when a.unit_of_measure=1 then c.qnty else 0 end) as qntypcsIssueRtn, 
            sum(case when a.unit_of_measure=27 then c.qnty else 0 end) as qntyydsIssueRtn
            from pro_roll_details c, inv_receive_master d, inv_transaction e,pro_grey_prod_entry_dtls f, product_details_master a, tmp_barcode_no b
            where c.entry_form=84 and c.mst_id = d.id and d.id = e.mst_id and c.dtls_id=f.id and e.id=f.trans_id and F.PROD_ID=a.id and C.BARCODE_NO=B.BARCODE_NO
            and d.entry_form=84 and c.status_active=1 and c.is_deleted=0 and e.transaction_type=4 
            and e.item_category=13 and e.status_active =1 and e.is_deleted=0 and f.status_active =1 and b.userid=$user_id and f.is_deleted=0  $poIds_cond_roll
            group by c.po_breakdown_id, c.barcode_no, a.unit_of_measure");
            foreach ($iss_rtn_qty_sql as $row)
            {
                $buyer_id=$non_order_arr[$row[csf("po_breakdown_id")]]["buyer_name"];
                $style_ref_no=$non_order_arr[$row[csf("po_breakdown_id")]]["style_ref_no"];
                $booking_no=$non_order_arr[$row[csf("po_breakdown_id")]]["booking_no"];
                
                $machine_dia_gg='';
                if($prodBarcodeData[$row[csf("barcode_no")]]["receive_basis"]==2)
                {
                    $machine_dia_gg=$plan_arr[$prodBarcodeData[$row[csf("barcode_no")]]["booking_id"]];
                }

                $str_ref2=$buyer_id."*".$style_ref_no."*".$booking_no."*".$prodBarcodeData[$row[csf("barcode_no")]]["body_part_id"]."*".$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"]."*".$prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"]."*".$prodBarcodeData[$row[csf("barcode_no")]]["width"]."*".$prodBarcodeData[$row[csf("barcode_no")]]["gsm"]."*".$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"]."*".$prodBarcodeData[$row[csf("barcode_no")]]["item_size"];

                $issue_rtn_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2]['max_date']=$row[csf("max_date")];
                $issue_rtn_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2]['qntykgIssueRtn']+=$row[csf("qntykgIssueRtn")];
                $issue_rtn_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2]['qntypcsIssueRtn']+=$row[csf("qntypcsIssueRtn")];
                $issue_rtn_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2]['qntyydsIssueRtn']+=$row[csf("qntyydsIssueRtn")];
                $issue_rtn_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2]['roll_countIssueRtn']++;

                $issue_rtn_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2][$row[csf("uom")]]++;

                /*if ($prodBarcodeData[$row[csf("barcode_no")]]["color_id"]!="") 
                {
                    $color_rev_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]]['colorRcvKg']+=$row[csf("qntykgIssueRtn")];
                    $color_rev_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]]['colorRcvPcs']+=$row[csf("qntypcsIssueRtn")];
                    $color_rev_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]]['colorRcvYds']+=$row[csf("qntyydsIssueRtn")];                
                    $color_rev_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]]['ColorRollCount']++;
                }*/
                $supplier_wise_recv_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['RcvKg']+=$row[csf("qntykgIssueRtn")];
                $supplier_wise_recv_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['RcvPcs']+=$row[csf("qntypcsIssueRtn")];
                $supplier_wise_recv_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['RcvYds']+=$row[csf("qntyydsIssueRtn")];                
                $supplier_wise_recv_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['RollCount']++;
            }
            unset($iss_rtn_qty_sql);
            // echo "<per>";print_r($issue_rtn_data_arr); echo "</per>";

            $transfer_out_sql=sql_select("SELECT d.from_order_id as po_breakdown_id, c.barcode_no, e.unit_of_measure as uom, max(d.transfer_date) as max_date,
            sum(case when e.unit_of_measure=12 then c.qnty else 0 end) as qntykgTransOut, 
            sum(case when e.unit_of_measure=1 then c.qnty else 0 end) as qntypcsTransOut, 
            sum(case when e.unit_of_measure=27 then c.qnty else 0 end) as qntyydsTransOut
            from inv_item_transfer_dtls b,  pro_roll_details c, inv_item_transfer_mst d, product_details_master e
            where b.id=c.dtls_id and b.from_prod_id=e.id and d.transfer_criteria in(7,8) and c.entry_form in (183,180) and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $all_booking_id_cond2 and c.booking_without_order=1 and b.mst_id=d.id and d.entry_form in (183,180)
            GROUP BY d.from_order_id, c.barcode_no, e.unit_of_measure");

            foreach ($transfer_out_sql as $row)
            {
                $buyer_id=$non_order_arr[$row[csf("po_breakdown_id")]]["buyer_name"];
                $style_ref_no=$non_order_arr[$row[csf("po_breakdown_id")]]["style_ref_no"];
                $booking_no=$non_order_arr[$row[csf("po_breakdown_id")]]["booking_no"];
                
                $machine_dia_gg='';
                if($prodBarcodeData[$row[csf("barcode_no")]]["receive_basis"]==2)
                {
                    $machine_dia_gg=$plan_arr[$prodBarcodeData[$row[csf("barcode_no")]]["booking_id"]];
                }

                $str_ref2=$buyer_id."*".$style_ref_no."*".$booking_no."*".$prodBarcodeData[$row[csf("barcode_no")]]["body_part_id"]."*".$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"]."*".$prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"]."*".$prodBarcodeData[$row[csf("barcode_no")]]["width"]."*".$prodBarcodeData[$row[csf("barcode_no")]]["gsm"]."*".$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"]."*".$prodBarcodeData[$row[csf("barcode_no")]]["item_size"];

                $trans_out_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2]['max_date']=$row[csf("max_date")];
                $trans_out_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2]['qntykgTransOut']+=$row[csf("qntykgTransOut")];
                $trans_out_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2]['qntypcsTransOut']+=$row[csf("qntypcsTransOut")];
                $trans_out_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2]['qntyydsTransOut']+=$row[csf("qntyydsTransOut")];
                $trans_out_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2]['roll_countTransOut']++;

                $trans_out_data_arr[$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2][$row[csf("uom")]]++;

                /*if ($prodBarcodeData[$row[csf("barcode_no")]]["color_id"]!="") 
                {
                    $color_issue_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]]['colorIssKg']+=$row[csf("qntykgTransOut")];
                    $color_issue_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]]['colorIssPcs']+=$row[csf("qntypcsTransOut")];
                    $color_issue_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]]['colorIssYds']+=$row[csf("qntyydsTransOut")];                
                    $color_issue_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]]['ColorRollCount']++;
                }*/
                if ($prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"]==1 || $prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"]==3) 
                {
                    $supplier_wise_issue_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['IssKg']+=$row[csf("qntykgTransOut")];
                    $supplier_wise_issue_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['IssPcs']+=$row[csf("qntypcsTransOut")];
                    $supplier_wise_issue_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['IssYds']+=$row[csf("qntyydsTransOut")];                
                    $supplier_wise_issue_summary_arr[$prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['RollCount']++;
                }
            }
            unset($transfer_out_sql);
            // echo "<per>";print_r($trans_out_data_arr); echo "</per>";
        } // non order end
        else
        {
            if ($txt_job_no !="" || $txt_style_no !="" || $txt_booking_no !="" || $txt_order_no !="" || $cbo_stock_for !="") 
            {
                $poArr=array(); $poIds=''; $tot_rows=0; $fileRefArr=array();
                $po_sql="SELECT b.job_no, b.buyer_name, b.style_ref_no, a.id, a.po_number, c.booking_no 
                from wo_po_details_master b, wo_po_break_down a, wo_booking_dtls c
                where b.id=a.job_id and a.id=c.po_break_down_id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.company_name=$company $booking_type_cond $year_cond $job_no_cond $style_no_cond $order_no_cond $booking_no_cond $order_cond group by b.job_no, b.buyer_name, b.style_ref_no, a.id, a.po_number, c.booking_no order by a.id";
                
                // echo $po_sql;die;

                $po_result=sql_select($po_sql);
                if(!empty($po_result))
                {
                    foreach($po_result as $row)
                    {
                        $tot_rows++;
                        $poIds.=$row[csf('id')].",";
                    }
                }
                unset($po_result);
                $poIds=chop($poIds,','); $poIds_cond_roll="";$trans_inPoIds_cond="";
                if($db_type==2 && $tot_rows>1000)
                {
                    $poIds_cond_pre=" and (";
                    $poIds_cond_suff.=")";
                    $poIds_cond_pre2=" and (";
                    $poIds_cond_suff2.=")";
                    $poIdsArr=array_chunk(array_unique(explode(",",$poIds)),999);
                    foreach($poIdsArr as $ids)
                    {
                        $ids=implode(",",$ids);
                        $poIds_cond_roll.=" d.po_breakdown_id in($ids) or ";
                        $trans_inPoIds_cond.=" a.to_order_id in($ids) or ";
                    }

                    $poIds_cond_roll=$poIds_cond_pre.chop($poIds_cond_roll,'or ').$poIds_cond_suff;
                    $trans_inPoIds_cond=$poIds_cond_pre2.chop($trans_inPoIds_cond,'or ').$poIds_cond_suff2;
                }
                else
                {
                    $poIds_cond_roll=" and d.po_breakdown_id in($poIds)";
                    $trans_inPoIds_cond=" and a.to_order_id in($poIds)";
                }
                // echo $poIds_cond_roll;die;
            }

            $with_order_recv_query="SELECT * from (
            SELECT a.entry_form, d.barcode_no, d.po_breakdown_id, b.store_id, c.fabric_description_id as detar_id, c.color_id, f.unit_of_measure as uom, b.prod_id, e.batch_no, e.color_range_id, e.booking_no, 
            sum(case when f.unit_of_measure=12 then d.qnty else 0 end) as qntykg, 
            sum(case when f.unit_of_measure=1 then d.qnty else 0 end) as qntypcs, 
            sum(case when f.unit_of_measure=27 then d.qnty else 0 end) as qntyyds, 0 as qntykgin, 0 as qntypcsin, 0 as qntyydsin
            from inv_receive_master a, inv_transaction b, pro_finish_fabric_rcv_dtls c, pro_roll_details d, pro_batch_create_mst e, product_details_master f 
            where a.entry_form in(68) and a.id=b.mst_id and b.id=c.trans_id and c.id=d.dtls_id and d.entry_form in(68) and d.status_active=1 and d.is_deleted=0 and a.company_id=$company $dates_cond $poIds_cond_roll and c.batch_id=e.id and c.prod_id=f.id and a.status_active=1 and c.status_active=1 
            and d.status_active=1 and b.status_active=1 and d.is_sales <>1 
            group by a.entry_form, d.barcode_no, d.po_breakdown_id, b.store_id, c.fabric_description_id, c.color_id, f.unit_of_measure, b.prod_id, e.batch_no, e.color_range_id, e.booking_no
            UNION ALL 
            SELECT a.entry_form, d.barcode_no, d.po_breakdown_id, b.to_store as store_id, e.detarmination_id as detar_id, b.color_id as color_id, e.unit_of_measure as uom, b.from_prod_id as prod_id, c.batch_no, c.color_range_id, c.booking_no
            , 0 as qntykg, 0 as qntypcs, 0 as qntyyds, 
            sum(case when e.unit_of_measure=12 then d.qnty else 0 end) as qntykgIn, 
            sum(case when e.unit_of_measure=1 then d.qnty else 0 end) as qntypcsIn, 
            sum(case when e.unit_of_measure=27 then d.qnty else 0 end) as qntyydsIn
            from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details d, product_details_master e, pro_batch_create_mst c
            where a.id=b.mst_id and b.id=d.dtls_id and b.from_prod_id=e.id and B.BATCH_ID=c.id and a.entry_form in(134) and d.entry_form in(134) and a.transfer_criteria in (1,2,4) and d.status_active=1 and d.is_deleted=0
            and nvl(d.booking_without_order,0)=0 and a.company_id=$company $trans_inPoIds_cond $transfer_date 
            group by a.entry_form, d.barcode_no, d.po_breakdown_id, b.to_store, e.detarmination_id, b.color_id, e.unit_of_measure, b.from_prod_id, c.batch_no, c.color_range_id, c.booking_no
            ) order by detar_id, color_id asc";
             //echo $with_order_recv_query;die; // $transfer_date // and a.transfer_system_id='FAL-GFTE-21-00004'
            $result = sql_select($with_order_recv_query);
            if(!empty($result))
            {
                foreach ($result as $row)
                {
                    $barcodeArr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
                    $po_id_arr[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
                    $prod_id_arr[$row[csf("prod_id")]]=$row[csf("prod_id")];
                }
            }

            $con = connect();
            $r_id2=execute_query("delete from tmp_barcode_no where userid=$user_id");
            if($r_id2)
            {
                oci_commit($con);
            }
            if(!empty($barcodeArr))
            {
                foreach ($barcodeArr as $row)
                {
                    $r_id=execute_query("insert into tmp_barcode_no (userid, barcode_no) values ($user_id,".$row.")");
                    if($r_id) 
                    {
                        $r_id=1;
                    } 
                    else 
                    {
                        echo "insert into tmp_barcode_no (userid, barcode_no) values ($user_id,".$row.")";
                        oci_rollback($con);
                        die;
                    }
                }
            }
            else
            {
                echo "Data Not Found";
                die;
            }

            if($r_id)
            {
                oci_commit($con);
            }
            else
            {
                oci_rollback($con);
                disconnect($con);
            }

            $barcodeArr = array_filter($barcodeArr);
            if(count($barcodeArr ) >0 ) // production
            {
                /*"SELECT a.id,a.entry_form, a.barcode_no, a.mst_id,a.dtls_id,a .po_breakdown_id, a.roll_no,a.roll_id, a.qnty, a.qc_pass_qnty, b.grey_receive_qnty, a.booking_without_order, a.qc_pass_qnty_pcs 
                from pro_roll_details a, pro_grey_prod_entry_dtls b, TMP_BARCODE_NO c
                where a.dtls_id=b.id and a.entry_form in(68) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  b.trans_id<>0 
                and a.barcode_no = c.barcode_no and c.userid= $user_id";

                "SELECT a.id,c.entry_form,a.booking_without_order,a.booking_id,a.booking_no,b.id as grey_id, a.company_id,a.knitting_company,a.knitting_source, b.prod_id, b.body_part_id, b.febric_description_id, b.yarn_lot, 
                b.yarn_count, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.is_sales,c.booking_no as roll_booking_no, c.qc_pass_qnty_pcs 
                FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
                WHERE a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.id=118440
                order by c.entry_form asc"*/

                /*select b.barcode_no, c.qnty, c.roll_split_from from pro_roll_split b, pro_roll_details c, tmp_barcode_no d 
                where b.entry_form =75 and  b.split_from_id = c.roll_split_from and b.status_active =1 and c.status_active =1 and C.BARCODE_NO=D.BARCODE_NO  and d.userid= 1;*/
                
                /*$split_chk_sql = sql_select("SELECT c.barcode_no , c.qnty from pro_roll_split b, pro_roll_details c,tmp_barcode_no d where b.entry_form =75 and b.split_from_id = c.roll_split_from and b.status_active=1 and c.status_active=1 and b.barcode_no = d.barcode_no and d.userid= $user_id");*/
                /*$split_chk_sql = sql_select("SELECT c.barcode_no, b.BARCODE_NO as split_barcode_no, c.qnty from pro_roll_split b, pro_roll_details c
                where b.entry_form =75 and b.split_from_id = c.roll_split_from and b.status_active=1 and c.status_active=1 and b.roll_id in(118440,143602,143603,135817,120410,135816)");

                if(!empty($split_chk_sql))
                {
                    foreach ($split_chk_sql as $val)
                    {
                        $split_barcode_arr[$val[csf("barcode_no")]]= $val[csf("barcode_no")];
                    }

                    $split_barcodes = implode(",", $split_barcode_arr);
                    if($db_type==2 && count($split_barcode_arr)>999)
                    {
                        $split_barcode_arr_chunk=array_chunk($split_barcode_arr,999) ;
                        $split_barcode_cond = " and (";

                        foreach($split_barcode_arr_chunk as $chunk_arr)
                        {
                            $split_barcode_cond.=" a.barcode_no in(".implode(",",$chunk_arr).") or ";
                        }

                        $split_barcode_cond = chop($split_barcode_cond,"or ");
                        $split_barcode_cond .=")";
                    }
                    else
                    {
                        $split_barcode_cond=" and a.barcode_no in($split_barcodes)";
                    }

                    $split_ref_sql = sql_select("SELECT a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b where a.entry_form = 61 and a.roll_id = b.id and a.status_active =1 and b.status_active=1 $split_barcode_cond");

                    if(!empty($split_ref_sql))
                    {
                        foreach ($split_ref_sql as $value)
                        {
                            $mother_barcode_arr[$value[csf("barcode_no")]] = $value[csf("mother_barcode")];
                        }
                    }
                    // echo "<pre>";print_r($mother_barcode_arr);
                    $split_knitting_production=sql_select("SELECT a.id,c.entry_form,a.booking_without_order,a.booking_id,a.booking_no,b.id as grey_id, a.company_id,a.knitting_company,a.knitting_source, b.prod_id, b.body_part_id, b.febric_description_id, b.yarn_lot, 
                    b.yarn_count, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.is_sales,c.booking_no as roll_booking_no, c.qc_pass_qnty_pcs 
                    FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
                    WHERE a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.id=118440
                    order by c.entry_form asc");
                    $prodBarcodeData=array();
                    foreach ($split_knitting_production as $row)
                    {
                        $mother_barcode_no = $mother_barcode_arr[$row[csf("barcode_no")]];
                        // echo $mother_barcode_no."==".$row[csf("barcode_no")].'<br>';
                        $prodBarcodeData[$mother_barcode_no]["receive_basis"] =$row[csf("receive_basis")];
                        $prodBarcodeData[$mother_barcode_no]["item_size"] =$row[csf("coller_cuff_size")];
                        $prodBarcodeData[$mother_barcode_no]["yarn_lot"] =$row[csf("yarn_lot")];
                        $prodBarcodeData[$mother_barcode_no]["yarn_count"] =$row[csf("yarn_count")];
                        $prodBarcodeData[$mother_barcode_no]["yarn_prod_id"] =$row[csf("yarn_prod_id")];
                        $prodBarcodeData[$mother_barcode_no]["febric_description_id"] =$row[csf("febric_description_id")];
                        $prodBarcodeData[$mother_barcode_no]["color_id"] =$row[csf("color_id")];
                        $prodBarcodeData[$mother_barcode_no]["body_part_id"] =$row[csf("body_part_id")];
                        $prodBarcodeData[$mother_barcode_no]["width"] =$row[csf("width")];
                        $prodBarcodeData[$mother_barcode_no]["gsm"] =$row[csf("gsm")];
                        $prodBarcodeData[$mother_barcode_no]["stitch_length"] =$row[csf("stitch_length")];
                        
                        $allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];
                    }
                }*/
                // echo "<pre>";print_r($prodBarcodeData);

                $knitting_production_sql = sql_select("SELECT b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id, a.prod_id, b.booking_no,c.booking_id, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, c.knitting_source, c.challan_no as production_challan, c.knitting_company, a.yarn_prod_id, a.body_part_id, b.coller_cuff_size
                from pro_grey_prod_entry_dtls a, pro_roll_details b, inv_receive_master c, tmp_barcode_no d 
                where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form in(2,58) and b.entry_form in(2,58)  and a.status_active=1 and b.status_active=1  
                and b.barcode_no = d.barcode_no and d.userid=$user_id 
                order by c.entry_form desc");
                // and b.barcode_no in(21020005625 ,21020005624)
                foreach ($knitting_production_sql as $row)
                {
                    $prodBarcodeData[$row[csf("barcode_no")]]["receive_basis"] =$row[csf("receive_basis")];
                    $prodBarcodeData[$row[csf("barcode_no")]]["item_size"] =$row[csf("coller_cuff_size")];
                    $prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"] =$row[csf("yarn_lot")];
                    $prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"] =$row[csf("yarn_count")];
                    $prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"] =$row[csf("yarn_prod_id")];
                    $prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"] =$row[csf("febric_description_id")];
                    $prodBarcodeData[$row[csf("barcode_no")]]["color_id"] =$row[csf("color_id")];
                    $prodBarcodeData[$row[csf("barcode_no")]]["body_part_id"] =$row[csf("body_part_id")];
                    $prodBarcodeData[$row[csf("barcode_no")]]["width"] =$row[csf("width")];
                    $prodBarcodeData[$row[csf("barcode_no")]]["gsm"] =$row[csf("gsm")];
                    $prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"] =$row[csf("stitch_length")];




                    
                    $allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];

                    /*if($row[csf('receive_basis')] == 2 )
                    {
                        $program_id_arr[$row[csf('booking_id')]]=$row[csf('booking_id')];
                    }*/
                }

                $yarn_prod_id_arr = array_filter($allYarnProdArr);
                if(count($yarn_prod_id_arr)>0)
                {
                    $yarn_prod_ids = implode(",", $yarn_prod_id_arr);
                    $yarnCond = $yarn_prod_id_cond = "";
                    if($db_type==2 && count($yarn_prod_id_arr)>999)
                    {
                        $yarn_prod_id_arr_chunk=array_chunk($yarn_prod_id_arr,999) ;
                        foreach($yarn_prod_id_arr_chunk as $chunk_arr)
                        {
                            $yarnCond.=" id in(".implode(",",$chunk_arr).") or ";
                        }
                        $yarn_prod_id_cond.=" and (".chop($yarnCond,'or ').")";
                    }
                    else
                    {
                        $yarn_prod_id_cond=" and id in($yarn_prod_ids)";
                    }

                    $yarn_sql=  sql_select("select id, yarn_type,yarn_comp_type1st, brand from product_details_master where status_active = 1 $yarn_prod_id_cond and item_category_id =1");
                    foreach ($yarn_sql as $row)
                    {
                        $yarn_ref[$row[csf("id")]]["type"] = $yarn_type[$row[csf("yarn_type")]];
                        $yarn_ref[$row[csf("id")]]["comp"] = $composition[$row[csf("yarn_comp_type1st")]];
                        $yarn_ref[$row[csf("id")]]["brand"] = $brand_arr[$row[csf("brand")]];
                    }
                }
    
                $finish_production_sql = sql_select("SELECT a.prod_id, a.batch_id, a.body_part_id, a.fabric_description_id, a.gsm, a.width, a.dia_width_type, a.color_id, a.order_id, a.roll_id, a.roll_no, a.original_gsm, a.original_width, b.barcode_no, b.po_breakdown_id, b.booking_no, b.coller_cuff_size, c.booking_id, b.receive_basis, c.knitting_source, c.challan_no as production_challan, c.knitting_company 
                from pro_finish_fabric_rcv_dtls a, pro_roll_details b, inv_receive_master c, tmp_barcode_no d 
                where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form in(66) and b.entry_form in(66) and a.status_active=1 and b.status_active=1 and b.barcode_no = d.barcode_no and d.userid=$user_id order by c.entry_form desc");

                foreach ($finish_production_sql as $row)
                {
                    // $mother_barcode_no = $mother_barcode_arr[$row[csf("barcode_no")]];
                    // echo $mother_barcode_no.'===='.$row[csf("barcode_no")].'<br>';
                    $finBarcodeData[$row[csf("barcode_no")]]["receive_basis"] =$row[csf("receive_basis")];
                    $finBarcodeData[$row[csf("barcode_no")]]["prog_book"] =$row[csf("booking_no")];
                    $finBarcodeData[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
                    $finBarcodeData[$row[csf("barcode_no")]]["prod_id"] =$row[csf("prod_id")];
                    $finBarcodeData[$row[csf("barcode_no")]]["color_id"] =$row[csf("color_id")];
                    $finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"] =$row[csf("fabric_description_id")];
                    $finBarcodeData[$row[csf("barcode_no")]]["gsm"] =$row[csf("gsm")];
                    $finBarcodeData[$row[csf("barcode_no")]]["width"] =$row[csf("width")];
                    $finBarcodeData[$row[csf("barcode_no")]]["yarn_count"]=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"];
                    $finBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"]=$prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"];
                    $finBarcodeData[$row[csf("barcode_no")]]["item_size"]=$prodBarcodeData[$row[csf("barcode_no")]]["item_size"];
                    $finBarcodeData[$row[csf("barcode_no")]]["stitch_length"]=$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"];
                    $finBarcodeData[$row[csf("barcode_no")]]["prod_challan"] =$row[csf("production_challan")];
                    $finBarcodeData[$row[csf("barcode_no")]]["knitting_source"] =$row[csf("knitting_source")];
                    $finBarcodeData[$row[csf("barcode_no")]]["knitting_company"] =$row[csf("knitting_company")];
                    $finBarcodeData[$row[csf("barcode_no")]]["body_part_id"] =$row[csf("body_part_id")];
                    $allDeterArr[$row[csf("fabric_description_id")]] =$row[csf("fabric_description_id")];
                    $allColorArr[$row[csf("color_id")]] =$row[csf("color_id")];
                }
                // echo "<pre>";print_r($allDeterArr);die;

                $febric_description_arr = array_filter($allDeterArr);
                if(!empty($febric_description_arr))
                {
                    $ref_febric_description_ids = implode(",", $febric_description_arr);
                    $fabCond = $ref_febric_description_cond = "";
                    if($db_type==2 && count($febric_description_arr)>999)
                    {
                        $ref_febric_description_arr_chunk=array_chunk($febric_description_arr,999) ;
                        foreach($ref_febric_description_arr_chunk as $chunk_arr)
                        {
                            $fabCond.=" a.id in(".implode(",",$chunk_arr).") or ";
                        }
                        $ref_febric_description_cond.=" and (".chop($fabCond,'or ').")";
                    }
                    else
                    {
                        $ref_febric_description_cond=" and a.id in($ref_febric_description_ids)";
                    }

                    $sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id $ref_febric_description_cond and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0";
                    $deter_array=sql_select($sql_deter);
                    if(count($deter_array)>0)
                    {
                        foreach($deter_array as $row )
                        {
                            if(array_key_exists($row[csf('id')],$composition_arr))
                            {
                                $composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
                            }
                            else
                            {
                                $composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
                            }

                            $constuction_arr[$row[csf('id')]]=$row[csf('construction')];

                            if($row[csf('type_id')]>0)
                            {
                                $type_array[$row[csf('id')]].=$yarn_type[$row[csf('type_id')]].",";
                            }
                        }
                    }
                    unset($deter_array);
                }

                $all_color_arr = array_filter($allColorArr);
                if(!empty($all_color_arr))
                {
                    $all_color_ids = implode(",", $all_color_arr);
                    $colorCond = $all_color_cond = "";
                    if($db_type==2 && count($all_color_arr)>999)
                    {
                        $all_color_chunk=array_chunk($all_color_arr,999) ;
                        foreach($all_color_chunk as $chunk_arr)
                        {
                            $colorCond.=" id in(".implode(",",$chunk_arr).") or ";
                        }
                        $all_color_cond.=" and (".chop($colorCond,'or ').")";
                    }
                    else
                    {
                        $all_color_cond=" and id in($all_color_ids)";
                    }

                    $colorArr=return_library_array( "select id, color_name from lib_color where status_active=1 $all_color_cond", "id", "color_name" );
                }
            }
            // echo "<pre>";print_r($finBarcodeData);echo "</pre>";die;
            

            $all_po_ids = implode(",",array_filter(array_unique($po_id_arr)));
            if($all_po_ids=="") $all_po_ids=0;
            $poidCond = $all_po_id_cond = "";$poidCond2 = $all_po_id_cond2 = "";
            $poidCond3 = $poIds_cond_roll = "";$poidCond4 = $poIds_cond_trans_roll = "";
            $poidCond5 = $ctct_po_cond = "";
            $all_po_id_arr=explode(",",$all_po_ids);
            if($db_type==2 && count($all_po_id_arr)>999)
            {
                $all_po_id_chunk_arr=array_chunk($all_po_id_arr,999) ;
                foreach($all_po_id_chunk_arr as $chunk_arr)
                {
                    $chunk_arr_value=implode(",",$chunk_arr);
                    $poidCond.=" a.id in($chunk_arr_value) or ";
                    $poidCond2.=" b.po_breakdown_id in($chunk_arr_value) or ";
                    $poidCond3.=" c.po_breakdown_id in($chunk_arr_value) or ";
                    $poidCond4.=" a.po_breakdown_id in($chunk_arr_value) or ";
                    $poidCond5.=" b.from_order_id in($chunk_arr_value) or ";
                }
                $all_po_id_cond.=" and (".chop($poidCond,'or ').")";
                $all_po_id_cond2.=" and (".chop($poidCond2,'or ').")";
                $poIds_cond_roll.=" and (".chop($poidCond3,'or ').")";
                $poIds_cond_trans_roll.=" and (".chop($poidCond4,'or ').")";
                $ctct_po_cond.=" and (".chop($poidCond5,'or ').")";
            }
            else
            {
                $all_po_id_cond=" and a.id in($all_po_ids)";
                $all_po_id_cond2=" and b.po_breakdown_id in($all_po_ids)";
                $poIds_cond_roll=" and c.po_breakdown_id in($all_po_ids)";
                $poIds_cond_trans_roll=" and a.po_breakdown_id in($all_po_ids)";
                $ctct_po_cond=" and b.from_order_id in($all_po_ids)";
            }

            if(count($prod_id_arr) >0 ) // Days In Hand
            {
                $prod_id_ids = implode(",", $prod_id_arr);
                $prod_idCond = $prod_id_id_cond = "";
                if($db_type==2 && count($prod_id_arr)>999)
                {
                    $prod_id_id_arr_chunk=array_chunk($prod_id_arr,999) ;
                    foreach($prod_id_id_arr_chunk as $chunk_arr)
                    {
                        $prod_idCond.=" prod_id in(".implode(",",$chunk_arr).") or ";
                    }
                    $prod_id_id_cond.=" and (".chop($prod_idCond,'or ').")";
                }
                else
                {
                    $prod_id_id_cond=" and prod_id in($prod_id_ids)";
                }

                $transaction_date_array=array();
                $sql_date="SELECT prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where status_active=1 and is_deleted=0 and item_category=2 $prod_id_id_cond group by prod_id";
                $sql_date_result=sql_select($sql_date);
                foreach( $sql_date_result as $row )
                {
                    $transaction_date_array[$row[csf('prod_id')]]['min_date']=$row[csf('min_date')];
                    $transaction_date_array[$row[csf('prod_id')]]['max_date']=$row[csf('max_date')];
                }
                unset($sql_date_result);
            }

            $poArr=array(); $poIds=''; $tot_rows=0; $fileRefArr=array();
            $po_sql="SELECT b.job_no, b.buyer_name, b.style_ref_no, a.id, a.po_number, c.booking_no, a.pub_shipment_date, c.dia_width, c.gsm_weight, c.fabric_color_id, d.body_part_id, d.lib_yarn_count_deter_id as deter_id,
            sum(case when d.uom=12 then c.grey_fab_qnty else 0 end) as requQntyKg,
            sum(case when d.uom=1 then c.grey_fab_qnty else 0 end) as requqntyPcs,
            sum(case when d.uom=27 then c.grey_fab_qnty else 0 end) as requQntyYds
            from wo_po_details_master b, wo_po_break_down a, wo_booking_dtls c, wo_pre_cost_fabric_cost_dtls d 
            where b.id=a.job_id and a.id=c.po_break_down_id and b.job_no=c.job_no and c.job_no=d.job_no and c.pre_cost_fabric_cost_dtls_id=d.id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.company_name=$company $booking_type_cond $year_cond $job_no_cond $style_no_cond $order_no_cond $booking_no_cond $all_po_id_cond $order_cond group by b.job_no, b.buyer_name, b.style_ref_no, a.id, a.po_number, c.booking_no, d.uom, a.pub_shipment_date, c.dia_width, c.gsm_weight, c.fabric_color_id, d.body_part_id, d.lib_yarn_count_deter_id 
            order by b.job_no";
            $po_result=sql_select($po_sql);
            $booking_dia_gsm=array(); $requQnty_arr=array();
            if(!empty($po_result))
            {
                foreach($po_result as $row)
                {
                    $po_arr[$row[csf("id")]]["buyer_name"]      =$row[csf("buyer_name")];
                    $po_arr[$row[csf("id")]]["job_no"]          =$row[csf("job_no")];
                    $po_arr[$row[csf("id")]]["style_ref_no"]    =$row[csf("style_ref_no")];
                    $po_arr[$row[csf("id")]]["po_number"]       =$row[csf("po_number")];
                    $po_arr[$row[csf("id")]]["booking_no"]      =$row[csf("booking_no")];
                    $po_arr[$row[csf("id")]]["ex_fac_date"]     =$row[csf("pub_shipment_date")];

                    $requQnty_arr[$row[csf("booking_no")]][$row[csf("id")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("fabric_color_id")]]['requQntyKg']+=$row[csf('requQntyKg')];
                    $requQnty_arr[$row[csf("booking_no")]][$row[csf("id")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("fabric_color_id")]]['requqntyPcs']+=$row[csf('requqntyPcs')];
                    $requQnty_arr[$row[csf("booking_no")]][$row[csf("id")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("fabric_color_id")]]['requQntyYds']+=$row[csf('requQntyYds')];

                    $booking_dia_gsm[$row[csf("booking_no")]][$row[csf("id")]][$row[csf("body_part_id")]]['booking_dia'].=$row[csf('dia_width')].',';
                    $booking_dia_gsm[$row[csf("booking_no")]][$row[csf("id")]][$row[csf("body_part_id")]]['booking_gsm'].=$row[csf('gsm_weight')].',';
                }
            }
            // echo "<pre>";print_r($po_arr);die;

            $with_order_data_arr=array(); $supplier_wise_recv_summary_arr=array();
            foreach ($result as $row) // main loop array
            {
                $buyer_id=$po_arr[$row[csf("po_breakdown_id")]]["buyer_name"];
                $job_no=$po_arr[$row[csf("po_breakdown_id")]]["job_no"];
                $po_number=$po_arr[$row[csf("po_breakdown_id")]]["po_number"];
                $style_ref_no=$po_arr[$row[csf("po_breakdown_id")]]["style_ref_no"];
                $booking_no=$po_arr[$row[csf("po_breakdown_id")]]["booking_no"];
                $ex_fac_date=$po_arr[$row[csf("po_breakdown_id")]]["ex_fac_date"];

                // ."*".$finBarcodeData[$row[csf("barcode_no")]]["yarn_count"]."*".$finBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"]."*".$finBarcodeData[$row[csf("barcode_no")]]["width"]."*".$finBarcodeData[$row[csf("barcode_no")]]["gsm"]."*".$finBarcodeData[$row[csf("barcode_no")]]["stitch_length"]."*".$finBarcodeData[$row[csf("barcode_no")]]["item_size"]
                $str_ref=$buyer_id."*".$style_ref_no."*".$po_number."*".$booking_no."*".$finBarcodeData[$row[csf("barcode_no")]]["body_part_id"];
                //echo $str_ref.'<br>';

                $with_order_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['qntykg']+=$row[csf("qntykg")];
                $with_order_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['qntypcs']+=$row[csf("qntypcs")];
                $with_order_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['qntyyds']+=$row[csf("qntyyds")];
                
                $with_order_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref][$row[csf("uom")]]++;
                
                
                $with_order_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['roll_count']++;

                $with_order_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['qntykgIn']+=$row[csf("qntykgIn")];
                $with_order_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['qntypcsIn']+=$row[csf("qntypcsIn")];
                $with_order_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['qntyydsIn']+=$row[csf("qntyydsIn")];
                
                if ($row[csf("entry_form")]==134) 
                {
                    $with_order_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['roll_countIn']++;
                }

                $with_order_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['ex_fac_date']=$ex_fac_date;
                $with_order_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['store_id']=$row[csf("store_id")];
                $with_order_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['prod_id']=$row[csf("prod_id")];
                $with_order_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['entry_form']=$row[csf("entry_form")];
                $with_order_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['barcode_no']=$row[csf("barcode_no")];
                $with_order_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['dia'].=$finBarcodeData[$row[csf("barcode_no")]]["width"].',';
                $with_order_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['gsm'].=$finBarcodeData[$row[csf("barcode_no")]]["gsm"].',';
                $with_order_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['yarn_count'].=$finBarcodeData[$row[csf("barcode_no")]]["yarn_count"].',';
                $with_order_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['yarn_prod_id'].=$finBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"].',';
                $with_order_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['item_size'].=$finBarcodeData[$row[csf("barcode_no")]]["item_size"].',';
                $with_order_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['stitch_length'].=$finBarcodeData[$row[csf("barcode_no")]]["stitch_length"].',';
                $with_order_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref]['po_id']=$row[csf("po_breakdown_id")];
                

                $supplier_wise_recv_summary_arr[$finBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$finBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['RcvKg']+=$row[csf("qntykg")];
                $supplier_wise_recv_summary_arr[$finBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$finBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['RcvPcs']+=$row[csf("qntypcs")];
                $supplier_wise_recv_summary_arr[$finBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$finBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['RcvYds']+=$row[csf("qntyyds")];

                $supplier_wise_recv_summary_arr[$finBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$finBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['RcvKg']+=$row[csf("qntykgIn")];
                $supplier_wise_recv_summary_arr[$finBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$finBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['RcvPcs']+=$row[csf("qntypcsIn")];
                $supplier_wise_recv_summary_arr[$finBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$finBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['RcvYds']+=$row[csf("qntyydsIn")];

                if ($row[csf("entry_form")]==134)
                {
                    $supplier_wise_recv_summary_arr[$finBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$finBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['RollCount']++;
                }
                else
                {
                    $supplier_wise_recv_summary_arr[$finBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$finBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['RollCount']++;
                }
            }

            // echo "<pre>";print_r($with_order_data_arr);die;

            $iss_qty_sql=sql_select("SELECT c.po_breakdown_id, c.barcode_no, e.store_id as store_name, 
                max(e.transaction_date) as max_date, a.unit_of_measure as uom, 
            sum(case when a.unit_of_measure=12 then c.qnty else 0 end) as qntykgIssue,
            sum(case when a.unit_of_measure=1 then c.qnty else 0 end) as qntypcsIssue, 
            sum(case when a.unit_of_measure=27 then c.qnty else 0 end) as qntyydsIssue
            from pro_roll_details c, inv_finish_fabric_issue_dtls d, inv_transaction e, tmp_barcode_no b, 
            product_details_master a
            where c.dtls_id = d.id and c.mst_id = d.mst_id and d.trans_id = e.id and c.barcode_no = b.barcode_no and b.userid= $user_id 
            and e.transaction_type=2 and e.prod_id=a.id 
            and c.entry_form=71 and c.status_active=1 and c.is_deleted=0 and c.booking_without_order =0  
            group by c.po_breakdown_id, c.barcode_no, c.qnty, e.store_id, a.unit_of_measure");
            // and c.barcode_no in(21020005625,21020005624)
            $issue_data_arr=array(); $supplier_wise_issue_summary_arr=array();
            foreach ($iss_qty_sql as $row)
            {
                $buyer_id=$po_arr[$row[csf("po_breakdown_id")]]["buyer_name"];
                $job_no=$po_arr[$row[csf("po_breakdown_id")]]["job_no"];
                $po_number=$po_arr[$row[csf("po_breakdown_id")]]["po_number"];
                $style_ref_no=$po_arr[$row[csf("po_breakdown_id")]]["style_ref_no"];
                $booking_no=$po_arr[$row[csf("po_breakdown_id")]]["booking_no"];

                $str_ref2=$buyer_id."*".$style_ref_no."*".$po_number."*".$booking_no."*".$finBarcodeData[$row[csf("barcode_no")]]["body_part_id"];
                // echo $str_ref2.'<br>';

                //echo $job_no."=".$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]."=".$prodBarcodeData[$row[csf("barcode_no")]]["color_id"]."=".$str_ref2."<br/>";
                $issue_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2]['max_date']=$row[csf("max_date")];
                $issue_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2]['qntykgIssue']+=$row[csf("qntykgIssue")];
                $issue_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2]['qntypcsIssue']+=$row[csf("qntypcsIssue")];
                $issue_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2]['qntyydsIssue']+=$row[csf("qntyydsIssue")];
                $issue_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2]['roll_countIssue']++;

                $issue_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2][$row[csf("uom")]]++;

                if ($finBarcodeData[$row[csf("barcode_no")]]["knitting_source"]==1 || $finBarcodeData[$row[csf("barcode_no")]]["knitting_source"]==3) 
                {
                    $supplier_wise_issue_summary_arr[$finBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$finBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['IssKg']+=$row[csf("qntykgIssue")];
                    $supplier_wise_issue_summary_arr[$finBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$finBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['IssPcs']+=$row[csf("qntypcsIssue")];
                    $supplier_wise_issue_summary_arr[$finBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$finBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['IssYds']+=$row[csf("qntyydsIssue")];                
                    $supplier_wise_issue_summary_arr[$finBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$finBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['RollCount']++;
                }
            }
            // echo "<per>"; print_r($issue_data_arr); echo "</per>";
            unset($iss_qty_sql);
            // echo "<per>";print_r($color_issue_summary_arr); echo "</per>";
          
            $iss_rtn_qty_sql=sql_select("SELECT c.po_breakdown_id, c.barcode_no, a.unit_of_measure as uom, max(e.transaction_date) as max_date, 
            sum(case when a.unit_of_measure=12 then c.qnty else 0 end) as qntykgIssueRtn, 
            sum(case when a.unit_of_measure=1 then c.qnty else 0 end) as qntypcsIssueRtn, 
            sum(case when a.unit_of_measure=27 then c.qnty else 0 end) as qntyydsIssueRtn
            from pro_roll_details c, inv_receive_master d, inv_transaction e,PRO_FINISH_FABRIC_RCV_DTLS f, product_details_master a, tmp_barcode_no b
            where c.entry_form=126 and c.mst_id = d.id and d.id = e.mst_id and c.dtls_id=f.id and e.id=f.trans_id and F.PROD_ID=a.id and C.BARCODE_NO=B.BARCODE_NO
            and d.entry_form=126 and c.status_active=1 and c.is_deleted=0 and e.transaction_type=4 
            and e.item_category=2 and e.status_active =1 and e.is_deleted=0 and f.status_active =1 and f.is_deleted=0 and b.userid=$user_id  $poIds_cond_roll
            group by c.po_breakdown_id, c.barcode_no, a.unit_of_measure");
            foreach ($iss_rtn_qty_sql as $row)
            {
                $buyer_id=$po_arr[$row[csf("po_breakdown_id")]]["buyer_name"];
                $job_no=$po_arr[$row[csf("po_breakdown_id")]]["job_no"];
                $po_number=$po_arr[$row[csf("po_breakdown_id")]]["po_number"];
                $style_ref_no=$po_arr[$row[csf("po_breakdown_id")]]["style_ref_no"];
                $booking_no=$po_arr[$row[csf("po_breakdown_id")]]["booking_no"];


                $str_ref2=$buyer_id."*".$style_ref_no."*".$po_number."*".$booking_no."*".$finBarcodeData[$row[csf("barcode_no")]]["body_part_id"];

                $issue_rtn_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2]['max_date']=$row[csf("max_date")];
                $issue_rtn_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2]['qntykgIssueRtn']+=$row[csf("qntykgIssueRtn")];
                $issue_rtn_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2]['qntypcsIssueRtn']+=$row[csf("qntypcsIssueRtn")];
                $issue_rtn_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2]['qntyydsIssueRtn']+=$row[csf("qntyydsIssueRtn")];
                $issue_rtn_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2]['roll_countIssueRtn']++;

                $issue_rtn_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2][$row[csf("uom")]]++;

                $supplier_wise_recv_summary_arr[$finBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$finBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['RcvKg']+=$row[csf("qntykgIssueRtn")];
                $supplier_wise_recv_summary_arr[$finBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$finBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['RcvPcs']+=$row[csf("qntypcsIssueRtn")];
                $supplier_wise_recv_summary_arr[$finBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$finBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['RcvYds']+=$row[csf("qntyydsIssueRtn")];                
                $supplier_wise_recv_summary_arr[$finBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$finBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['RollCount']++;
            }
            unset($iss_rtn_qty_sql);
            //echo "<per>";print_r($issue_rtn_data_arr); echo "</per>";

            $transfer_out_sql=sql_select("SELECT a.po_breakdown_id, c.barcode_no, e.unit_of_measure as uom, max(d.transfer_date) as max_date,
            sum(case when e.unit_of_measure=12 then c.qnty else 0 end) as qntykgTransOut, 
            sum(case when e.unit_of_measure=1 then c.qnty else 0 end) as qntypcsTransOut, 
            sum(case when e.unit_of_measure=27 then c.qnty else 0 end) as qntyydsTransOut
            from order_wise_pro_details a, inv_item_transfer_dtls b,  pro_roll_details c, inv_item_transfer_mst d, product_details_master e
            where a.trans_id=b.trans_id and b.id=c.dtls_id and a.prod_id=e.id and b.from_prod_id=e.id and c.entry_form=134 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.trans_type=6 $poIds_cond_trans_roll and c.booking_without_order =0 and b.mst_id=d.id and d.entry_form=134
            GROUP BY a.po_breakdown_id, c.barcode_no, e.unit_of_measure");
            foreach ($transfer_out_sql as $row)
            {
                $buyer_id=$po_arr[$row[csf("po_breakdown_id")]]["buyer_name"];
                $job_no=$po_arr[$row[csf("po_breakdown_id")]]["job_no"];
                $po_number=$po_arr[$row[csf("po_breakdown_id")]]["po_number"];
                $style_ref_no=$po_arr[$row[csf("po_breakdown_id")]]["style_ref_no"];
                $booking_no=$po_arr[$row[csf("po_breakdown_id")]]["booking_no"];

                $str_ref2=$buyer_id."*".$style_ref_no."*".$po_number."*".$booking_no."*".$finBarcodeData[$row[csf("barcode_no")]]["body_part_id"];

                // echo $str_ref2.'<br>';

                $trans_out_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2]['max_date']=$row[csf("max_date")];
                $trans_out_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2]['qntykgTransOut']+=$row[csf("qntykgTransOut")];
                $trans_out_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2]['qntypcsTransOut']+=$row[csf("qntypcsTransOut")];
                $trans_out_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2]['qntyydsTransOut']+=$row[csf("qntyydsTransOut")];
                $trans_out_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2]['roll_countTransOut']++;

                $trans_out_data_arr[$job_no][$finBarcodeData[$row[csf("barcode_no")]]["febric_description_id"]][$finBarcodeData[$row[csf("barcode_no")]]["color_id"]][$str_ref2][$row[csf("uom")]]++;

                if ($finBarcodeData[$row[csf("barcode_no")]]["knitting_source"]==1 || $finBarcodeData[$row[csf("barcode_no")]]["knitting_source"]==3) 
                {
                    $supplier_wise_issue_summary_arr[$finBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$finBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['IssKg']+=$row[csf("qntykgTransOut")];
                    $supplier_wise_issue_summary_arr[$finBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$finBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['IssPcs']+=$row[csf("qntypcsTransOut")];
                    $supplier_wise_issue_summary_arr[$finBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$finBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['IssYds']+=$row[csf("qntyydsTransOut")];                
                    $supplier_wise_issue_summary_arr[$finBarcodeData[$row[csf("barcode_no")]]["knitting_source"]][$finBarcodeData[$row[csf("barcode_no")]]["knitting_company"]]['RollCount']++;
                }
            }
            unset($transfer_out_sql);
            // echo "<per>";print_r($trans_out_data_arr); echo "</per>";
        }// for order end

        ob_start();
        ?>
        <style type="text/css">
            .word_wrap_break {
                word-break: break-all;
                word-wrap: break-word;
            }
        </style>
        <div align="left">
            <fieldset style="width:4235px;">
            <?
            if(count($with_order_data_arr)>0 || count($non_order_data_arr)>0)
            {
                ?>
                <div  align="center"> <strong> <? echo $company_arr[$company]; ?> </strong>
                <br><b>
                    <?
                    $date_head="";
                    if( $date_from)
                    {
                        $date_head .= change_date_format($date_from).' To ';
                    }
                    if( $date_to)
                    {
                        $date_head .= change_date_format($date_to);
                    }
                    echo $date_head;
                    ?> </b>
                </div>
                <?
            }
            else
            {
                echo "<b>Data Not Found</b>";
            }
            ?>
            <div align="left">
                <table class="rpt_table" width="4225" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                    <thead>
                        <tr>
                            <th width="30" rowspan="2" class="word_wrap_break">SL</th>
                            <th width="75" rowspan="2" class="word_wrap_break">Buyer Name</th>
                            <th width="100" rowspan="2" class="word_wrap_break">Job Number</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Style</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Booking</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Order/PO Number</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Ex-Factory Date</th>
                            <th width="80" rowspan="2" class="word_wrap_break">Body Part </th>
                            <th width="60" rowspan="2" class="word_wrap_break">Item Size</th>
                            <th width="70" rowspan="2" class="word_wrap_break">Fabric Color</th>
                            <th width="110" rowspan="2" class="word_wrap_break">Fabric Construction</th>
                            <th width="120" rowspan="2" class="word_wrap_break">Fabric Composition</th>
                            <th width="120" rowspan="2" class="word_wrap_break">Yarn Composition</th>
                            <th width="60" rowspan="2" class="word_wrap_break">Dia</th>
                            <th width="60" rowspan="2" class="word_wrap_break">GSM</th>
                            <th width="60" rowspan="2" class="word_wrap_break">Stitch<br>Length</th>

                            <th colspan="3">Required</th>
                            <th colspan="4">Receive</th>
                            <!-- <th colspan="3">Receive Balance</th> -->
                            <th colspan="4">Transfer In</th>
                            <th colspan="4">Issue Return</th>
                            
                            <th width="70" class="word_wrap_break" rowspan="2">Total Receive<br>KG</th>
                            <th width="70" class="word_wrap_break" rowspan="2">Total Receive<br>PCS</th>
                            <th width="70" class="word_wrap_break" rowspan="2">Total Receive<br>YDS</th>
                            <th width="40" class="word_wrap_break" rowspan="2">Total<br>Roll</th>

                            <th colspan="4">Issue</th>
                            <th colspan="4">Transfer Out</th>
                            <th colspan="4">Receive Return</th>

                            <th width="60" class="word_wrap_break" rowspan="2">Total Issue<br>KG</th>
                            <th width="60" class="word_wrap_break" rowspan="2">Total Issue<br>PCS</th>
                            <th width="60" class="word_wrap_break" rowspan="2">Total Issue<br>YDS</th>
                            <th width="40" class="word_wrap_break" rowspan="2">Total<br>Roll</th>

                            <th colspan="4">Stock In Hand</th>

                            <th width="40" class="word_wrap_break" rowspan="2">Days In Hand</th>
                            <th width="100" class="word_wrap_break" rowspan="2">Store Name</th>
                        </tr>
                        <tr>
                            <th width="60" class="word_wrap_break">KG</th>
                            <th width="60" class="word_wrap_break">PCS</th>
                            <th width="60" class="word_wrap_break">YDS</th>

                            <th width="60" class="word_wrap_break">KG</th>
                            <th width="60" class="word_wrap_break">PCS</th>
                            <th width="60" class="word_wrap_break">YDS</th>
                            <th width="40" class="word_wrap_break">ROLL</th>

                            <!-- <th width="60" class="word_wrap_break">KG</th>
                            <th width="60" class="word_wrap_break">PCS</th>
                            <th width="60" class="word_wrap_break">YDS</th> -->

                            <th width="60" class="word_wrap_break">KG</th>
                            <th width="60" class="word_wrap_break">PCS</th>
                            <th width="60" class="word_wrap_break">YDS</th>
                            <th width="40" class="word_wrap_break">ROLL</th>

                            <th width="60" class="word_wrap_break">KG</th>
                            <th width="60" class="word_wrap_break">PCS</th>
                            <th width="60" class="word_wrap_break">YDS</th>
                            <th width="40" class="word_wrap_break">ROLL</th>

                            <th width="60" class="word_wrap_break">KG</th>
                            <th width="60" class="word_wrap_break">PCS</th>
                            <th width="60" class="word_wrap_break">YDS</th>
                            <th width="40" class="word_wrap_break">ROLL</th>

                            <th width="60" class="word_wrap_break">KG</th>
                            <th width="60" class="word_wrap_break">PCS</th>
                            <th width="60" class="word_wrap_break">YDS</th>
                            <th width="40" class="word_wrap_break">ROLL</th>

                            <th width="60" class="word_wrap_break">KG</th>
                            <th width="60" class="word_wrap_break">PCS</th>
                            <th width="60" class="word_wrap_break">YDS</th>
                            <th width="40" class="word_wrap_break">ROLL</th>

                            <th width="60" class="word_wrap_break">KG</th>
                            <th width="60" class="word_wrap_break">PCS</th>
                            <th width="60" class="word_wrap_break">YDS</th>
                            <th width="40" class="word_wrap_break">ROLL</th>
                        </tr>
                    </thead>
                </table>
                <div style=" max-height:350px; width:4245px; overflow-y:scroll;" id="scroll_body">
                    <table class="rpt_table" id="table_body" width="4225" cellpadding="0" cellspacing="0" border="1" rules="all">
                        <tbody>
                            <?
                            $i=1;
                            $grand_tot_requQntyKg=$grand_tot_requQntyPcs=$grand_tot_requQntyYds=$grand_tot_rcv_qntykg=$grand_tot_rcv_qntypcs=$grand_tot_rcv_qntyyds=$grand_tot_rcv_roll=$grand_tot_recv_kg_balance=$grand_tot_recv_pcs_balance=$grand_tot_recv_yds_balance=$grand_tot_qntykgIn=$grand_tot_qntypcsIn=$grand_tot_qntyydsIn=$grand_tot_roll_countIn=$grand_tot_qntykgIssueRtn=$grand_tot_qntypcsIssueRtn=$grand_tot_qntyydsIssueRtn=$grand_tot_roll_countIssueRtn=$grand_tot_total_recv_kg=$grand_tot_total_recv_pcs=$grand_tot_total_recv_yds=$grand_tot_total_recv_rollkg=$grand_tot_total_recv_rollpcs=$grand_tot_total_recv_rollyds=$grand_tot_qntykgIssue=$grand_tot_qntypcsIssue=$grand_tot_qntyydsIssue=$grand_tot_roll_countIssue=$grand_tot_issue_balanceKg=$grand_tot_issue_balancePcs=$grand_tot_issue_balanceYds=$grand_tot_qntykgTransOut=$grand_tot_qntypcsTransOut=$grand_tot_qntyydsTransOut=$grand_tot_roll_countTransOut=$grand_tot_total_issue_kg=$grand_tot_total_issue_pcs=$grand_tot_total_issue_yds=$grand_tot_total_issue_rollkg=$grand_tot_total_issue_rollpcs=$grand_tot_total_issue_rollyds=$grand_tot_stock_in_hand_kg=$grand_tot_stock_in_hand_pcs=$grand_tot_stock_in_hand_yds=$grand_tot_stock_in_hand_roll=0;  
                            $color_rev_summary_arr=array();  $color_issue_summary_arr=array();

                            // with order data show
                            foreach ($with_order_data_arr as $job_no_key => $job_no_val)
                            {

                                $job_tot_requQntyKg=$job_tot_requQntyPcs=$job_tot_requQntyYds=$job_tot_rcv_qntykg=$job_tot_rcv_qntypcs=$job_tot_rcv_qntyyds=$job_tot_rcv_roll=$job_tot_recv_kg_balance=$job_tot_recv_pcs_balance=$job_tot_recv_yds_balance=$job_tot_qntykgIn=$job_tot_qntypcsIn=$job_tot_qntyydsIn=$job_tot_roll_countIn=$job_tot_qntykgIssueRtn=$job_tot_qntypcsIssueRtn=$job_tot_qntyydsIssueRtn=$job_tot_roll_countIssueRtn=$job_tot_total_recv_kg=$job_tot_total_recv_pcs=$job_tot_total_recv_yds=$job_tot_total_recv_rollkg=$job_tot_total_recv_rollpcs=$job_tot_total_recv_rollyds=$job_tot_qntykgIssue=$job_tot_qntypcsIssue=$job_tot_qntyydsIssue=$job_tot_roll_countIssue=$job_tot_issue_balanceKg=$job_tot_issue_balancePcs=$job_tot_issue_balanceYds=$job_tot_qntykgTransOut=$job_tot_qntypcsTransOut=$job_tot_qntyydsTransOut=$job_tot_roll_countTransOut=$job_tot_total_issue_kg=$job_tot_total_issue_pcs=$job_tot_total_issue_yds=$job_tot_total_issue_rollkg=$job_tot_total_issue_rollpcs=$job_tot_total_issue_rollyds=$job_tot_stock_in_hand_kg=$job_tot_stock_in_hand_pcs=$job_tot_stock_in_hand_yds=$job_tot_stock_in_hand_roll=0;
                                foreach ($job_no_val as $detar_id_key => $detar_id_val)
                                {
                                    $fabric_tot_requQntyKg=$fabric_tot_requQntyPcs=$fabric_tot_requQntyYds=$fabric_tot_rcv_qntykg=$fabric_tot_rcv_qntypcs=$fabric_tot_rcv_qntyyds=$fabric_tot_rcv_roll=$fabric_tot_recv_kg_balance=$fabric_tot_recv_pcs_balance=$fabric_tot_recv_yds_balance=$fabric_tot_qntykgIn=$fabric_tot_qntypcsIn=$fabric_tot_qntyydsIn=$fabric_tot_roll_countIn=$fabric_tot_qntykgIssueRtn=$fabric_tot_qntypcsIssueRtn=$fabric_tot_qntyydsIssueRtn=$fabric_tot_roll_countIssueRtn=$fabric_tot_total_recv_kg=$fabric_tot_total_recv_pcs=$fabric_tot_total_recv_yds=$fabric_tot_total_recv_rollkg=$fabric_tot_total_recv_rollpcs=$fabric_tot_total_recv_rollyds=$fabric_tot_qntykgIssue=$fabric_tot_qntypcsIssue=$fabric_tot_qntyydsIssue=$fabric_tot_roll_countIssue=$fabric_tot_issue_balanceKg=$fabric_tot_issue_balancePcs=$fabric_tot_issue_balanceYds=$fabric_tot_qntykgTransOut=$fabric_tot_qntypcsTransOut=$fabric_tot_qntyydsTransOut=$fabric_tot_roll_countTransOut=$fabric_tot_total_issue_kg=$fabric_tot_total_issue_pcs=$fabric_tot_total_issue_yds=$fabric_tot_total_issue_rollkg=$fabric_tot_total_issue_rollpcs=$fabric_tot_total_issue_rollyds=$fabric_tot_stock_in_hand_kg=$fabric_tot_stock_in_hand_pcs=$fabric_tot_stock_in_hand_yds=$fabric_tot_stock_in_hand_roll=0;
                                    foreach ($detar_id_val as $color_id_key => $color_id_val)
                                    {
                                        $color_tot_requQntyKg=$color_tot_requQntyPcs=$color_tot_requQntyYds=$color_tot_rcv_qntykg=$color_tot_rcv_qntypcs=$color_tot_rcv_qntyyds=$color_tot_rcv_roll=$color_tot_recv_kg_balance=$color_tot_recv_pcs_balance=$color_tot_recv_yds_balance=$color_tot_qntykgIn=$color_tot_qntypcsIn=$color_tot_qntyydsIn=$color_tot_roll_countIn=$color_tot_qntykgIssueRtn=$color_tot_qntypcsIssueRtn=$color_tot_qntyydsIssueRtn=$color_tot_roll_countIssueRtn=$color_tot_total_recv_kg=$color_tot_total_recv_pcs=$color_tot_total_recv_yds=$color_tot_total_recv_rollkg=$color_tot_total_recv_rollpcs=$color_tot_total_recv_rollyds=$color_tot_qntykgIssue=$color_tot_qntypcsIssue=$color_tot_qntyydsIssue=$color_tot_roll_countIssue=$color_tot_issue_balanceKg=$color_tot_issue_balancePcs=$color_tot_issue_balanceYds=$color_tot_qntykgTransOut=$color_tot_qntypcsTransOut=$color_tot_qntyydsTransOut=$color_tot_roll_countTransOut=$color_tot_total_issue_kg=$color_tot_total_issue_pcs=$color_tot_total_issue_yds=$color_tot_total_issue_rollkg=$color_tot_total_issue_rollpcs=$color_tot_total_issue_rollyds=$color_tot_stock_in_hand_kg=$color_tot_stock_in_hand_pcs=$color_tot_stock_in_hand_yds=$color_tot_stock_in_hand_roll=0;
                                        foreach ($color_id_val as $str_ref => $row)
                                        {
                                            $str_ref_arr = explode("*", $str_ref);
                                            $buyer=$str_ref_arr[0];
                                            $style_ref_no=$str_ref_arr[1];
                                            $po_number=$str_ref_arr[2];
                                            $booking_no=$str_ref_arr[3];
                                            $body_part_id=$str_ref_arr[4];
                                            //$yarn_count=$str_ref_arr[5];
                                            //$yarn_prod_id=$str_ref_arr[6];
                                            //$width=$str_ref_arr[7];
                                            //$gsm=$str_ref_arr[8];
                                            //$stitch_length=$str_ref_arr[9];
                                            //$item_size=$str_ref_arr[10];

                                            $booking_dia=$booking_dia_gsm[$booking_no][$row['po_id']][$body_part_id]["booking_dia"];
                                            $booking_gsm=$booking_dia_gsm[$booking_no][$row['po_id']][$body_part_id]["booking_gsm"];
                                            $booking_dia =implode(",",array_filter(array_unique(explode(",", $booking_dia))));
                                            $booking_gsm =implode(",",array_filter(array_unique(explode(",", $booking_gsm))));

                                            $item_size =implode(",",array_filter(array_unique(explode(",", $row['item_size']))));
                                            $stitch_length =implode(",",array_filter(array_unique(explode(",", $row['stitch_length']))));

                                            // $yarn_counts_arr = explode(",", $yarn_count);
                                            $yarn_counts_arr = array_unique(array_filter(explode(",", $row['yarn_count'])));
                                            $yarn_counts="";
                                            foreach ($yarn_counts_arr as $count) {
                                                $yarn_counts .= $yarncount_arr[$count] . ",";
                                            }
                                            // $yarn_counts = rtrim($yarn_counts, ", ");
                                            $yarn_counts =implode(",",array_filter(array_unique(explode(",", $yarn_counts))));

                                            $color_arr = explode(",", $color_id_key);
                                            $colors="";$booking_requQntyKg=0;$booking_requqntyPcs=0;$booking_requQntyYds=0;
                                            foreach ($color_arr as $color) 
                                            {
                                                $colors .= $colorArr[$color] . ",";

                                                //echo $booking_no.'='.$row['po_id'].'='.$body_part_id.'='.$detar_id_key.'='.$color.'<br>';

                                                $booking_requQntyKg+=$requQnty_arr[$booking_no][$row['po_id']][$body_part_id][$detar_id_key][$color]['requQntyKg'];
                                                $booking_requqntyPcs+=$requQnty_arr[$booking_no][$row['po_id']][$body_part_id][$detar_id_key][$color]['requqntyPcs'];
                                                $booking_requQntyYds+=$requQnty_arr[$booking_no][$row['po_id']][$body_part_id][$detar_id_key][$color]['requQntyYds'];
                                                // echo  $booking_requQntyKg.'<br>';
                                            }
                                            // echo  $booking_requQntyKg.'<br>';
                                            $colors = rtrim($colors, ", ");

                                            $yarn_id_arr = array_unique(array_filter(explode(",", $row['yarn_prod_id'])));
                                            $yarn_brand = $yarn_comp = $yarn_type_name = "";
                                            foreach ($yarn_id_arr as $yid)
                                            {
                                                // $yarn_brand .= ($yarn_brand =="") ? $yarn_ref[$yid]["brand"] :  ",". $yarn_ref[$yid]["brand"];
                                                $yarn_comp .= ($yarn_comp =="") ? $yarn_ref[$yid]["comp"] :  ",". $yarn_ref[$yid]["comp"];
                                                $yarn_type_name .= ($yarn_type_name =="") ? $yarn_ref[$yid]["type"] :  ",". $yarn_ref[$yid]["type"];
                                            }

                                            // $yarn_brand =implode(",",array_filter(array_unique(explode(",", $yarn_brand))));
                                            $yarn_comp =implode(",",array_filter(array_unique(explode(",", $yarn_comp))));
                                            $yarn_type_name =implode(",",array_filter(array_unique(explode(",", $yarn_type_name))));
                                            $dia =implode(",",array_filter(array_unique(explode(",", $row['dia']))));
                                            $gsm =implode(",",array_filter(array_unique(explode(",", $row['gsm']))));

                                            //echo $job_no_key."=".$detar_id_key."=".$color_id_key."=".$str_ref."<br/>";

                                            $qntykgIssue=$issue_data_arr[$job_no_key][$detar_id_key][$color_id_key][$str_ref]['qntykgIssue'];
                                            $qntypcsIssue=$issue_data_arr[$job_no_key][$detar_id_key][$color_id_key][$str_ref]['qntypcsIssue'];
                                            $qntyydsIssue=$issue_data_arr[$job_no_key][$detar_id_key][$color_id_key][$str_ref]['qntyydsIssue'];
                                            $roll_countIssue=$issue_data_arr[$job_no_key][$detar_id_key][$color_id_key][$str_ref]['roll_countIssue'];

                                            $issue_balanceKg=$row['qntykg']-$qntykgIssue;
                                            $issue_balancePcs=$row['qntypcs']-$qntypcsIssue;
                                            $issue_balanceYds=$row['qntyyds']-$qntyydsIssue;
                                            
                                            // echo $job_no_key.'='.$detar_id_key.'='.$color_id_key.'='.$str_ref;

                                            $qntykgIssueRtn=$issue_rtn_data_arr[$job_no_key][$detar_id_key][$color_id_key][$str_ref]['qntykgIssueRtn'];
                                            $qntypcsIssueRtn=$issue_rtn_data_arr[$job_no_key][$detar_id_key][$color_id_key][$str_ref]['qntypcsIssueRtn'];
                                            $qntyydsIssueRtn=$issue_rtn_data_arr[$job_no_key][$detar_id_key][$color_id_key][$str_ref]['qntyydsIssueRtn'];
                                            $roll_countIssueRtn=$issue_rtn_data_arr[$job_no_key][$detar_id_key][$color_id_key][$str_ref]['roll_countIssueRtn'];

                                            $qntykgTransOut=$trans_out_data_arr[$job_no_key][$detar_id_key][$color_id_key][$str_ref]['qntykgTransOut'];
                                            $qntypcsTransOut=$trans_out_data_arr[$job_no_key][$detar_id_key][$color_id_key][$str_ref]['qntypcsTransOut'];
                                            $qntyydsTransOut=$trans_out_data_arr[$job_no_key][$detar_id_key][$color_id_key][$str_ref]['qntyydsTransOut'];
                                            $roll_countTransOut=$trans_out_data_arr[$job_no_key][$detar_id_key][$color_id_key][$str_ref]['roll_countTransOut'];
                                            $total_issue_kg=$qntykgIssue+$qntykgTransOut;
                                            $total_issue_pcs=$qntypcsIssue+$qntypcsTransOut;
                                            $total_issue_yds=$qntyydsIssue+$qntyydsTransOut;

                                            $total_recv_kg=$row['qntykg']+$row['qntykgIn']+$qntykgIssueRtn;
                                            $total_recv_pcs=$row['qntypcs']+$row['qntypcsIn']+$qntypcsIssueRtn;
                                            $total_recv_yds=$row['qntyyds']+$row['qntyydsIn']+$qntyydsIssueRtn;
                                            
                                            $recv_rollkg=$with_order_data_arr[$job_no_key][$detar_id_key][$color_id_key][$str_ref][12];
                                            $recv_rollpcs=$with_order_data_arr[$job_no_key][$detar_id_key][$color_id_key][$str_ref][1];
                                            $recv_rollyds=$with_order_data_arr[$job_no_key][$detar_id_key][$color_id_key][$str_ref][27];

                                            $roll_countIssueRtnKg=$issue_rtn_data_arr[$job_no_key][$detar_id_key][$color_id_key][$str_ref][12];
                                            $roll_countIssueRtnpcs=$issue_rtn_data_arr[$job_no_key][$detar_id_key][$color_id_key][$str_ref][1];
                                            $roll_countIssueRtnyds=$issue_rtn_data_arr[$job_no_key][$detar_id_key][$color_id_key][$str_ref][27];

                                            $roll_countIssueKg=$issue_data_arr[$job_no_key][$detar_id_key][$color_id_key][$str_ref][12];
                                            $roll_countIssuepcs=$issue_data_arr[$job_no_key][$detar_id_key][$color_id_key][$str_ref][1];
                                            $roll_countIssueyds=$issue_data_arr[$job_no_key][$detar_id_key][$color_id_key][$str_ref][27];

                                            $roll_countTrans_outKg=$trans_out_data_arr[$job_no_key][$detar_id_key][$color_id_key][$str_ref][12];
                                            $roll_countTrans_outpcs=$trans_out_data_arr[$job_no_key][$detar_id_key][$color_id_key][$str_ref][1];
                                            $roll_countTrans_outyds=$trans_out_data_arr[$job_no_key][$detar_id_key][$color_id_key][$str_ref][27];

                                            $total_recv_rollkg=$recv_rollkg+$roll_countIssueRtnKg;
                                            $total_recv_rollpcs=$recv_rollpcs+$roll_countIssueRtnpcs;
                                            $total_recv_rollyds=$recv_rollyds+$roll_countIssueRtnyds;

                                            $total_issue_rollkg=$roll_countIssueKg+$roll_countTrans_outKg;
                                            $total_issue_rollpcs=$roll_countIssuepcs+$roll_countTrans_outpcs;
                                            $total_issue_rollyds=$roll_countIssueyds+$roll_countTrans_outyds;


                                            $stock_in_hand_kg=$total_recv_kg-$total_issue_kg;
                                            $stock_in_hand_pcs=$total_recv_pcs-$total_issue_pcs;
                                            $stock_in_hand_yds=$total_recv_yds-$total_issue_yds;
                                            $stock_in_hand_roll=($total_recv_rollkg+$total_recv_rollpcs+$total_recv_rollyds)-($total_issue_rollkg+$total_issue_rollpcs+$total_issue_rollyds);

                                            $daysOnHand = datediff("d",change_date_format($transaction_date_array[$row['prod_id']]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
                                            


                                            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                            if ((($cbo_get_upto_qnty == 1 && $stock_in_hand_kg > $txt_qnty) || ($cbo_get_upto_qnty == 1 && $stock_in_hand_pcs > $txt_qnty) || ($cbo_get_upto_qnty == 1 && $stock_in_hand_yds > $txt_qnty) || ($cbo_get_upto_qnty == 2 && $stock_in_hand_kg < $txt_qnty) || ($cbo_get_upto_qnty == 2 && $stock_in_hand_pcs < $txt_qnty) || ($cbo_get_upto_qnty == 2 && $stock_in_hand_yds < $txt_qnty) || ($cbo_get_upto_qnty == 3 && $stock_in_hand_kg >= $txt_qnty) || ($cbo_get_upto_qnty == 3 && $stock_in_hand_pcs >= $txt_qnty) || ($cbo_get_upto_qnty == 3 && $stock_in_hand_yds >= $txt_qnty) || ($cbo_get_upto_qnty == 4 && $stock_in_hand_kg <= $txt_qnty) || ($cbo_get_upto_qnty == 4 && $stock_in_hand_pcs <= $txt_qnty) || ($cbo_get_upto_qnty == 4 && $stock_in_hand_yds <= $txt_qnty) || ($cbo_get_upto_qnty == 5 && $stock_in_hand_kg == $txt_qnty) || ($cbo_get_upto_qnty == 5 && $stock_in_hand_pcs == $txt_qnty) || ($cbo_get_upto_qnty == 5 && $stock_in_hand_yds == $txt_qnty) || $cbo_get_upto_qnty == 0) && (($cbo_get_upto == 1 && $daysOnHand > $txt_days) || ($cbo_get_upto == 2 && $daysOnHand < $txt_days) || ($cbo_get_upto == 3 && $daysOnHand >= $txt_days) || ($cbo_get_upto == 4 && $daysOnHand <= $txt_days) || ($cbo_get_upto == 5 && $daysOnHand == $txt_days) || $cbo_get_upto == 0))
                                            {
                                                // echo $cbo_get_upto.'&&'.$daysOnHand.'=='.$txt_days.'<br>';
                                            ?>
                                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
                                                <td width="30"><? echo $i; ?></td>
                                                <td width="75"><p><? echo $buyer_arr[$buyer]; ?></p></td>
                                                <td width="100"><p><? echo $job_no_key; ?></p></td>
                                                <td width="80"><p><? echo $style_ref_no;?></p></td>
                                                <td width="80"><p><? echo $booking_no; ?></p></td>
                                                <td width="80" title="<? echo $row['po_id']; ?>"><p><? echo $po_number; ?></p></td>
                                                <td width="80"><p><? echo change_date_format($row['ex_fac_date'])?></p></td>
                                                <td width="80" title="<? echo $body_part_id;?>"><p><? echo $body_part[$body_part_id]; ?></p></td>
                                                <td width="60"><p><? echo $item_size; ?></p></td>
                                                <td width="70" title="<? echo $color_id_key;?>"><p><? echo $colors; ?></p></td>
                                                <td width="110" class="word_wrap_break" title="<?=$detar_id_key;?>"><p><? echo $constuction_arr[$detar_id_key]; ?></p></td>
                                                <td width="120" class="word_wrap_break"><p><? echo $composition_arr[$detar_id_key]; ?></p></td>
                                                <td width="120" class="word_wrap_break" title="<?echo $detar_id_key;?>"><p></p><? echo $yarn_counts.','.$yarn_type_name.','.$composition_arr[$detar_id_key]; ?></td>
                                                <td width="60"><p><? echo $dia.'<br>---<br>'.$booking_dia; ?></p></td>
                                                <td width="60"><p><? echo $gsm.'<br>---<br>'.$booking_gsm; ?></p></td>
                                                <td width="60"><p><? echo $stitch_length; ?></p></td>


                                                <td width="60" align="right"><p><?
                                                echo ($row['entry_form']!=134) ? $requQntyKg=number_format($booking_requQntyKg,2,'.','') : $requQntyKg='0.00' ;
                                                ?></p></td>
                                                <td width="60" align="right"><p><? echo ($row['entry_form']!=134) ? $requQntyPcs=number_format($booking_requqntyPcs,2,'.','') : $requQntyPcs='0.00';?></p></td>
                                                <td width="60" align="right"><p><? echo ($row['entry_form']!=134) ? $requQntyYds=number_format($booking_requQntyYds,2,'.','') : '0.00';?></p></td>

                                                <td width="60" align="right"><p><? echo number_format($row['qntykg'],2,'.','');?></p></td>
                                                <td width="60" align="right"><p><? echo number_format($row['qntypcs'],2,'.','');?></p></td>
                                                <td width="60" align="right"><p><? echo number_format($row['qntyyds'],2,'.','');?></p></td>
                                                <td width="40" align="right"><p><? echo ($row['entry_form']!=134) ? $roll_count=number_format($row['roll_count'],2,'.','') : $roll_count='0.00';?></p></td>
                                                
                                                <!-- <td width="80" align="right"><p><? //echo $recv_kg_balance=number_format($requQntyKg-$row['qntykg'],2,'.',''); ?></p></td>
                                                <td width="80" align="right"><p><? //echo $recv_pcs_balance=number_format($requQntyPcs-$row['qntypcs'],2,'.',''); ?></p></td> 
                                                <td width="80" align="right"><p><? //echo $recv_yds_balance=number_format($requQntyYds-$row['qntyyds'],2,'.',''); ?></p></td> -->


                                                <td width="60" align="right"><p><? echo number_format($row['qntykgIn'],2,'.','');?></p></td>
                                                <td width="60" align="right"><p><? echo number_format($row['qntypcsIn'],2,'.','');?></p></td>
                                                <td width="60" align="right"><p><? echo number_format($row['qntyydsIn'],2,'.','');?></p></td>
                                                <td width="40" align="right"><p><? echo number_format($row['roll_countIn'],2,'.','');?></p></td>

                                                <td width="60" align="right"><p><? echo number_format($qntykgIssueRtn,2,'.','');?></p></td>
                                                <td width="60" align="right"><p><? echo number_format($qntypcsIssueRtn,2,'.','');?></p></td>
                                                <td width="60" align="right"><p><? echo number_format($qntyydsIssueRtn,2,'.','');?></p></td>
                                                <td width="40" align="right"><p><? echo number_format($roll_countIssueRtn,2,'.','');?></p></td>


                                                <td width="70" align="right"><p></p><? echo number_format($total_recv_kg,2,'.','');?></td>
                                                <td width="70" align="right"><p><? echo number_format($total_recv_pcs,2,'.','');?></p></td>
                                                <td width="70" align="right"><p><? echo number_format($total_recv_yds,2,'.','');?></p></td>
                                                <td width="40" align="right" title="tot roll kg"><p><? echo number_format($total_recv_rollkg,2,'.',''); ?></p></td>


                                                <td width="60" align="right"><p><? echo number_format($qntykgIssue,2,'.','');?></p></td>
                                                <td width="60" align="right"><p><? echo number_format($qntypcsIssue,2,'.','');?></p></td>
                                                <td width="60" align="right"><p><? echo number_format($qntyydsIssue,2,'.','');?></p></td>
                                                <td width="40" align="right"><p><? echo number_format($roll_countIssue,2,'.','');?></p></td>

                                                <td width="60" align="right"><p><? echo number_format($qntykgTransOut,2,'.','');?></p></p></td>
                                                <td width="60" align="right"><p><? echo number_format($qntypcsTransOut,2,'.','');?></p></td> 
                                                <td width="60" align="right"><p><? echo number_format($qntyydsTransOut,2,'.','');?></p></td>
                                                <td width="40" align="right"><p><? echo number_format($roll_countTransOut,2,'.','');?></p></td>
                                                
                                                <td width="60" align="right"><p></p></td>
                                                <td width="60" align="right"><p></p></td>
                                                <td width="60" align="right"><p></p></td>
                                                <td width="40" align="right"><p></p></td>


                                                <td width="60" align="right"><p><? echo number_format($total_issue_kg,2,'.','');?></p></td>
                                                <td width="60" align="right"><p><? echo number_format($total_issue_pcs,2,'.','');?></p></td>
                                                <td width="60" align="right"><p><? echo number_format($total_issue_yds,2,'.','');?></p></td>
                                                <td width="40" align="right"><p><? echo number_format($total_issue_rollkg,2,'.','');?></p></td>


                                                <td width="60" align="right"><p><? echo number_format($stock_in_hand_kg,2,'.','');?></p></td>
                                                <td width="60" align="right"><p><? echo number_format($stock_in_hand_pcs,2,'.','');?></p></td>
                                                <td width="60" align="right"><p><? echo number_format($stock_in_hand_yds,2,'.','');?></p></td>
                                                <td width="40" align="right"><p><? echo number_format($stock_in_hand_roll,2,'.','');?></p></td>


                                                <td width="40" align="center"><p><? echo $daysOnHand;?></p></td>
                                                <td width="100"><p><? echo $store_arr[$row['store_id']]; ?></p></td>
                                            </tr>
                                            <?
                                            $color_rev_summary_arr[$color_id_key]['colorRcvKg']+=$row['qntykg']+$row['qntykgIn']+$qntykgIssueRtn;
                                            $color_rev_summary_arr[$color_id_key]['colorRcvPcs']+=$row['qntypcs']+$row['qntypcsIn']+$qntypcsIssueRtn;
                                            $color_rev_summary_arr[$color_id_key]['colorRcvYds']+=$row['qntyyds']+$row['qntyydsIn']+$qntyydsIssueRtn;
                                            $color_rev_summary_arr[$color_id_key]['ColorRollCount']+=$roll_count+$row['roll_countIn']+$roll_countIssueRtn;

                                            $color_issue_summary_arr[$color_id_key]['colorIssKg']+=$qntykgIssue+$qntykgTransOut;
                                            $color_issue_summary_arr[$color_id_key]['colorIssPcs']+=$qntypcsIssue+$qntypcsTransOut;
                                            $color_issue_summary_arr[$color_id_key]['colorIssYds']+=$qntyydsIssue+$qntyydsTransOut;
                                            $color_issue_summary_arr[$color_id_key]['ColorRollCount']+=$roll_countIssue+$roll_countTransOut;

                                            $show_color_total=1;
                                            $show_fabric_total=1;
                                            $show_job_total=1;
                                            $i++;
                                            $color_tot_requQntyKg+=$requQntyKg;
                                            $color_tot_requQntyPcs+=$requQntyPcs;
                                            $color_tot_requQntyYds+=$requQntyYds;

                                            $color_tot_rcv_qntykg+=$row['qntykg'];
                                            $color_tot_rcv_qntypcs+=$row['qntypcs'];
                                            $color_tot_rcv_qntyyds+=$row['qntyyds'];
                                            $color_tot_rcv_roll+=$roll_count;

                                            $color_tot_recv_kg_balance+=$recv_kg_balance;
                                            $color_tot_recv_pcs_balance+=$recv_pcs_balance;
                                            $color_tot_recv_yds_balance+=$recv_yds_balance;

                                            $color_tot_qntykgIn+=$row['qntykgIn'];
                                            $color_tot_qntypcsIn+=$row['qntypcsIn'];
                                            $color_tot_qntyydsIn+=$row['qntyydsIn'];
                                            $color_tot_roll_countIn+=$row['roll_countIn'];

                                            $color_tot_qntykgIssueRtn+=$qntykgIssueRtn;
                                            $color_tot_qntypcsIssueRtn+=$qntypcsIssueRtn;
                                            $color_tot_qntyydsIssueRtn+=$qntyydsIssueRtn;
                                            $color_tot_roll_countIssueRtn+=$roll_countIssueRtn;

                                            $color_tot_total_recv_kg+=$total_recv_kg;
                                            $color_tot_total_recv_pcs+=$total_recv_pcs;
                                            $color_tot_total_recv_yds+=$total_recv_yds;
                                            $color_tot_total_recv_rollkg+=$total_recv_rollkg;
                                            $color_tot_total_recv_rollpcs+=$total_recv_rollpcs;
                                            $color_tot_total_recv_rollyds+=$total_recv_rollyds;

                                            $color_tot_qntykgIssue+=$qntykgIssue;
                                            $color_tot_qntypcsIssue+=$qntypcsIssue;
                                            $color_tot_qntyydsIssue+=$qntyydsIssue;
                                            $color_tot_roll_countIssue+=$roll_countIssue;

                                            $color_tot_issue_balanceKg+=$issue_balanceKg;
                                            $color_tot_issue_balancePcs+=$issue_balancePcs;
                                            $color_tot_issue_balanceYds+=$issue_balanceYds;

                                            $color_tot_qntykgTransOut+=$qntykgTransOut;
                                            $color_tot_qntypcsTransOut+=$qntypcsTransOut;
                                            $color_tot_qntyydsTransOut+=$qntyydsTransOut;
                                            $color_tot_roll_countTransOut+=$roll_countTransOut;

                                            $color_tot_total_issue_kg+=$total_issue_kg;
                                            $color_tot_total_issue_pcs+=$total_issue_pcs;
                                            $color_tot_total_issue_yds+=$total_issue_yds;
                                            $color_tot_total_issue_rollkg+=$total_issue_rollkg;
                                            $color_tot_total_issue_rollpcs+=$total_issue_rollpcs;
                                            $color_tot_total_issue_rollyds+=$total_issue_rollyds;

                                            $color_tot_stock_in_hand_kg+=$stock_in_hand_kg;
                                            $color_tot_stock_in_hand_pcs+=$stock_in_hand_pcs;
                                            $color_tot_stock_in_hand_yds+=$stock_in_hand_yds;
                                            $color_tot_stock_in_hand_roll+=$stock_in_hand_roll;

                                            // ============================================

                                            $fabric_tot_requQntyKg+=$requQntyKg;
                                            $fabric_tot_requQntyPcs+=$requQntyPcs;
                                            $fabric_tot_requQntyYds+=$requQntyYds;

                                            $fabric_tot_rcv_qntykg+=$row['qntykg'];
                                            $fabric_tot_rcv_qntypcs+=$row['qntypcs'];
                                            $fabric_tot_rcv_qntyyds+=$row['qntyyds'];
                                            $fabric_tot_rcv_roll+=$roll_count;

                                            $fabric_tot_recv_kg_balance+=$recv_kg_balance;
                                            $fabric_tot_recv_pcs_balance+=$recv_pcs_balance;
                                            $fabric_tot_recv_yds_balance+=$recv_yds_balance;

                                            $fabric_tot_qntykgIn+=$row['qntykgIn'];
                                            $fabric_tot_qntypcsIn+=$row['qntypcsIn'];
                                            $fabric_tot_qntyydsIn+=$row['qntyydsIn'];
                                            $fabric_tot_roll_countIn+=$row['roll_countIn'];

                                            $fabric_tot_qntykgIssueRtn+=$qntykgIssueRtn;
                                            $fabric_tot_qntypcsIssueRtn+=$qntypcsIssueRtn;
                                            $fabric_tot_qntyydsIssueRtn+=$qntyydsIssueRtn;
                                            $fabric_tot_roll_countIssueRtn+=$roll_countIssueRtn;

                                            $fabric_tot_total_recv_kg+=$total_recv_kg;
                                            $fabric_tot_total_recv_pcs+=$total_recv_pcs;
                                            $fabric_tot_total_recv_yds+=$total_recv_yds;
                                            $fabric_tot_total_recv_rollkg+=$total_recv_rollkg;
                                            $fabric_tot_total_recv_rollpcs+=$total_recv_rollpcs;
                                            $fabric_tot_total_recv_rollyds+=$total_recv_rollyds;

                                            $fabric_tot_qntykgIssue+=$qntykgIssue;
                                            $fabric_tot_qntypcsIssue+=$qntypcsIssue;
                                            $fabric_tot_qntyydsIssue+=$qntyydsIssue;
                                            $fabric_tot_roll_countIssue+=$roll_countIssue;

                                            $fabric_tot_issue_balanceKg+=$issue_balanceKg;
                                            $fabric_tot_issue_balancePcs+=$issue_balancePcs;
                                            $fabric_tot_issue_balanceYds+=$issue_balanceYds;

                                            $fabric_tot_qntykgTransOut+=$qntykgTransOut;
                                            $fabric_tot_qntypcsTransOut+=$qntypcsTransOut;
                                            $fabric_tot_qntyydsTransOut+=$qntyydsTransOut;
                                            $fabric_tot_roll_countTransOut+=$roll_countTransOut;

                                            $fabric_tot_total_issue_kg+=$total_issue_kg;
                                            $fabric_tot_total_issue_pcs+=$total_issue_pcs;
                                            $fabric_tot_total_issue_yds+=$total_issue_yds;
                                            $fabric_tot_total_issue_rollkg+=$total_issue_rollkg;
                                            $fabric_tot_total_issue_rollpcs+=$total_issue_rollpcs;
                                            $fabric_tot_total_issue_rollyds+=$total_issue_rollyds;

                                            $fabric_tot_stock_in_hand_kg+=$stock_in_hand_kg;
                                            $fabric_tot_stock_in_hand_pcs+=$stock_in_hand_pcs;
                                            $fabric_tot_stock_in_hand_yds+=$stock_in_hand_yds;
                                            $fabric_tot_stock_in_hand_roll+=$stock_in_hand_roll;

                                            // ============================================

                                            $job_tot_requQntyKg+=$requQntyKg;
                                            $job_tot_requQntyPcs+=$requQntyPcs;
                                            $job_tot_requQntyYds+=$requQntyYds;

                                            $job_tot_rcv_qntykg+=$row['qntykg'];
                                            $job_tot_rcv_qntypcs+=$row['qntypcs'];
                                            $job_tot_rcv_qntyyds+=$row['qntyyds'];
                                            $job_tot_rcv_roll+=$roll_count;

                                            $job_tot_recv_kg_balance+=$recv_kg_balance;
                                            $job_tot_recv_pcs_balance+=$recv_pcs_balance;
                                            $job_tot_recv_yds_balance+=$recv_yds_balance;

                                            $job_tot_qntykgIn+=$row['qntykgIn'];
                                            $job_tot_qntypcsIn+=$row['qntypcsIn'];
                                            $job_tot_qntyydsIn+=$row['qntyydsIn'];
                                            $job_tot_roll_countIn+=$row['roll_countIn'];

                                            $job_tot_qntykgIssueRtn+=$qntykgIssueRtn;
                                            $job_tot_qntypcsIssueRtn+=$qntypcsIssueRtn;
                                            $job_tot_qntyydsIssueRtn+=$qntyydsIssueRtn;
                                            $job_tot_roll_countIssueRtn+=$roll_countIssueRtn;

                                            $job_tot_total_recv_kg+=$total_recv_kg;
                                            $job_tot_total_recv_pcs+=$total_recv_pcs;
                                            $job_tot_total_recv_yds+=$total_recv_yds;
                                            $job_tot_total_recv_rollkg+=$total_recv_rollkg;
                                            $job_tot_total_recv_rollpcs+=$total_recv_rollpcs;
                                            $job_tot_total_recv_rollyds+=$total_recv_rollyds;

                                            $job_tot_qntykgIssue+=$qntykgIssue;
                                            $job_tot_qntypcsIssue+=$qntypcsIssue;
                                            $job_tot_qntyydsIssue+=$qntyydsIssue;
                                            $job_tot_roll_countIssue+=$roll_countIssue;

                                            $job_tot_issue_balanceKg+=$issue_balanceKg;
                                            $job_tot_issue_balancePcs+=$issue_balancePcs;
                                            $job_tot_issue_balanceYds+=$issue_balanceYds;

                                            $job_tot_qntykgTransOut+=$qntykgTransOut;
                                            $job_tot_qntypcsTransOut+=$qntypcsTransOut;
                                            $job_tot_qntyydsTransOut+=$qntyydsTransOut;
                                            $job_tot_roll_countTransOut+=$roll_countTransOut;

                                            $job_tot_total_issue_kg+=$total_issue_kg;
                                            $job_tot_total_issue_pcs+=$total_issue_pcs;
                                            $job_tot_total_issue_yds+=$total_issue_yds;
                                            $job_tot_total_issue_rollkg+=$total_issue_rollkg;
                                            $job_tot_total_issue_rollpcs+=$total_issue_rollpcs;
                                            $job_tot_total_issue_rollyds+=$total_issue_rollyds;

                                            $job_tot_stock_in_hand_kg+=$stock_in_hand_kg;
                                            $job_tot_stock_in_hand_pcs+=$stock_in_hand_pcs;
                                            $job_tot_stock_in_hand_yds+=$stock_in_hand_yds;
                                            $job_tot_stock_in_hand_roll+=$stock_in_hand_roll;

                                            // ============================================

                                            $grand_tot_requQntyKg+=$requQntyKg;
                                            $grand_tot_requQntyPcs+=$requQntyPcs;
                                            $grand_tot_requQntyYds+=$requQntyYds;

                                            $grand_tot_rcv_qntykg+=$row['qntykg'];
                                            $grand_tot_rcv_qntypcs+=$row['qntypcs'];
                                            $grand_tot_rcv_qntyyds+=$row['qntyyds'];
                                            $grand_tot_rcv_roll+=$roll_count;

                                            $grand_tot_recv_kg_balance+=$recv_kg_balance;
                                            $grand_tot_recv_pcs_balance+=$recv_pcs_balance;
                                            $grand_tot_recv_yds_balance+=$recv_yds_balance;

                                            $grand_tot_qntykgIn+=$row['qntykgIn'];
                                            $grand_tot_qntypcsIn+=$row['qntypcsIn'];
                                            $grand_tot_qntyydsIn+=$row['qntyydsIn'];
                                            $grand_tot_roll_countIn+=$row['roll_countIn'];

                                            $grand_tot_qntykgIssueRtn+=$qntykgIssueRtn;
                                            $grand_tot_qntypcsIssueRtn+=$qntypcsIssueRtn;
                                            $grand_tot_qntyydsIssueRtn+=$qntyydsIssueRtn;
                                            $grand_tot_roll_countIssueRtn+=$roll_countIssueRtn;

                                            $grand_tot_total_recv_kg+=$total_recv_kg;
                                            $grand_tot_total_recv_pcs+=$total_recv_pcs;
                                            $grand_tot_total_recv_yds+=$total_recv_yds;
                                            $grand_tot_total_recv_rollkg+=$total_recv_rollkg;
                                            $grand_tot_total_recv_rollpcs+=$total_recv_rollpcs;
                                            $grand_tot_total_recv_rollyds+=$total_recv_rollyds;

                                            $grand_tot_qntykgIssue+=$qntykgIssue;
                                            $grand_tot_qntypcsIssue+=$qntypcsIssue;
                                            $grand_tot_qntyydsIssue+=$qntyydsIssue;
                                            $grand_tot_roll_countIssue+=$roll_countIssue;

                                            $grand_tot_issue_balanceKg+=$issue_balanceKg;
                                            $grand_tot_issue_balancePcs+=$issue_balancePcs;
                                            $grand_tot_issue_balanceYds+=$issue_balanceYds;

                                            $grand_tot_qntykgTransOut+=$qntykgTransOut;
                                            $grand_tot_qntypcsTransOut+=$qntypcsTransOut;
                                            $grand_tot_qntyydsTransOut+=$qntyydsTransOut;
                                            $grand_tot_roll_countTransOut+=$roll_countTransOut;

                                            $grand_tot_total_issue_kg+=$total_issue_kg;
                                            $grand_tot_total_issue_pcs+=$total_issue_pcs;
                                            $grand_tot_total_issue_yds+=$total_issue_yds;
                                            $grand_tot_total_issue_rollkg+=$total_issue_rollkg;
                                            $grand_tot_total_issue_rollpcs+=$total_issue_rollpcs;
                                            $grand_tot_total_issue_rollyds+=$total_issue_rollyds;

                                            $grand_tot_stock_in_hand_kg+=$stock_in_hand_kg;
                                            $grand_tot_stock_in_hand_pcs+=$stock_in_hand_pcs;
                                            $grand_tot_stock_in_hand_yds+=$stock_in_hand_yds;
                                            $grand_tot_stock_in_hand_roll+=$stock_in_hand_roll;

                                            }
                                        }
                                        if($show_color_total)
                                        {
                                            $show_color_total=0;
                                            ?>
                                            <!-- Color Total -->
                                            <tr class="tbl_bottom">
                                                <td width="100" align="right" colspan="16">Color Total</td>
                                                <td width="60"><? echo number_format($color_tot_requQntyKg,2,'.',''); ?></th>
                                                <td width="60" align="right"><? echo number_format($color_tot_requQntyPcs,2,'.',''); ?></td>
                                                <td width="60" align="center"><? echo number_format($color_tot_requQntyYds,2,'.',''); ?></td>

                                                <td width="60" align="right"><p><? echo number_format($color_tot_rcv_qntykg,2,'.',''); ?></p></td>
                                                <td width="60" align="right"><p><? echo number_format($color_tot_rcv_qntypsc,2,'.',''); ?></p></td>
                                                <td width="60" align="right"><p><? echo number_format($color_tot_rcv_qntyyds,2,'.',''); ?></p></td>
                                                <td width="40" align="right"><p><? echo number_format($color_tot_rcv_roll,2,'.',''); ?></p></td>

                                                <!-- <td width="80"><? //echo number_format($color_tot_recv_kg_balance,2,'.',''); ?></td>
                                                <td width="80"><? //echo number_format($color_tot_recv_pcs_balance,2,'.',''); ?></td>
                                                <td width="80"><? //echo number_format($color_tot_recv_yds_balance,2,'.',''); ?></td> -->

                                                <td width="60"><? echo number_format($color_tot_qntykgIn,2,'.',''); ?></td>
                                                <td width="60"><? echo number_format($color_tot_qntypcsIn,2,'.',''); ?></td>
                                                <td width="60"><? echo number_format($color_tot_qntyydsIn,2,'.',''); ?></td>
                                                <td width="40"><? echo number_format($color_tot_roll_countIn,2,'.',''); ?></td>

                                                <td width="60"><? echo number_format($color_tot_qntykgIssueRtn,2,'.',''); ?></td>
                                                <td width="60"><? echo number_format($color_tot_qntypcsIssueRtn,2,'.',''); ?></td>
                                                <td width="60"><? echo number_format($color_tot_qntyydsIssueRtn,2,'.',''); ?></td>
                                                <td width="40"><? echo number_format($color_tot_roll_countIssueRtn,2,'.',''); ?></td>


                                                <td width="70"><? echo number_format($color_tot_total_recv_kg,2,'.',''); ?></td>
                                                <td width="70"><? echo number_format($color_tot_total_recv_pcs,2,'.',''); ?></td>
                                                <td width="70"><? echo number_format($color_tot_total_recv_yds,2,'.',''); ?></td>
                                                <td width="40"><? echo number_format($color_tot_total_recv_rollkg,2,'.',''); ?></td>


                                                <td width="60"><? echo number_format($color_tot_qntykgIssue,2,'.',''); ?></td>
                                                <td width="60"><? echo number_format($color_tot_qntypcsIssue,2,'.',''); ?></td>
                                                <td width="60"><? echo number_format($color_tot_qntyydsIssue,2,'.',''); ?></td>
                                                <td width="40"><? echo number_format($color_tot_roll_countIssue,2,'.',''); ?></td>

                                                <td width="60"><? echo number_format($color_tot_qntykgTransOut,2,'.',''); ?></td>
                                                <td width="60"><? echo number_format($color_tot_qntypcsTransOut,2,'.',''); ?></td>
                                                <td width="60"><? echo number_format($color_tot_qntyydsTransOut,2,'.',''); ?></td>
                                                <td width="40"><? echo number_format($color_tot_roll_countTransOut,2,'.',''); ?></td>

                                                <td width="60"></td>
                                                <td width="60"></td>
                                                <td width="60"></td>
                                                <td width="40"></td>


                                                <td width="60"><? echo number_format($color_tot_total_issue_kg,2,'.',''); ?></td>
                                                <td width="60"><? echo number_format($color_tot_total_issue_pcs,2,'.',''); ?></td>
                                                <td width="60"><? echo number_format($color_tot_total_issue_yds,2,'.',''); ?></td>
                                                <td width="40"><? echo number_format($color_tot_total_issue_rollkg,2,'.',''); ?></td>


                                                <td width="60"><? echo number_format($color_tot_stock_in_hand_kg,2,'.',''); ?></td>
                                                <td width="60"><? echo number_format($color_tot_stock_in_hand_pcs,2,'.',''); ?></td>
                                                <td width="60"><? echo number_format($color_tot_stock_in_hand_yds,2,'.',''); ?></td>
                                                <td width="40"><? echo number_format($color_tot_stock_in_hand_roll,2,'.',''); ?></td>

                                                <td width="40"></td>
                                                <td width="100"></td>
                                            </tr>
                                            <? 
                                        }
                                    }
                                    if($show_fabric_total)
                                    {
                                        $show_fabric_total=0;
                                        ?>
                                        <!-- Fabric Total -->
                                        <tr class="tbl_bottom">
                                            <td width="100" align="right" colspan="16">Fabric Total</td>
                                            <td width="60"><? echo number_format($fabric_tot_requQntyKg,2,'.',''); ?></th>
                                            <td width="60" align="right"><? echo number_format($fabric_tot_requQntyPcs,2,'.',''); ?></td>
                                            <td width="60" align="center"><? echo number_format($fabric_tot_requQntyYds,2,'.',''); ?></td>

                                            <td width="60" align="right"><p><? echo number_format($fabric_tot_rcv_qntykg,2,'.',''); ?></p></td>
                                            <td width="60" align="right"><p><? echo number_format($fabric_tot_rcv_qntypsc,2,'.',''); ?></p></td>
                                            <td width="60" align="right"><p><? echo number_format($fabric_tot_rcv_qntyyds,2,'.',''); ?></p></td>
                                            <td width="40" align="right"><p><? echo number_format($fabric_tot_rcv_roll,2,'.',''); ?></p></td>

                                            <!-- <td width="80"><? //echo number_format($fabric_tot_recv_kg_balance,2,'.',''); ?></td>
                                            <td width="80"><? //echo number_format($fabric_tot_recv_pcs_balance,2,'.',''); ?></td>
                                            <td width="80"><? //echo number_format($fabric_tot_recv_yds_balance,2,'.',''); ?></td> -->

                                            <td width="60"><? echo number_format($fabric_tot_qntykgIn,2,'.',''); ?></td>
                                            <td width="60"><? echo number_format($fabric_tot_qntypcsIn,2,'.',''); ?></td>
                                            <td width="60"><? echo number_format($fabric_tot_qntyydsIn,2,'.',''); ?></td>
                                            <td width="40"><? echo number_format($fabric_tot_roll_countIn,2,'.',''); ?></td>

                                            <td width="60"><? echo number_format($fabric_tot_qntykgIssueRtn,2,'.',''); ?></td>
                                            <td width="60"><? echo number_format($fabric_tot_qntypcsIssueRtn,2,'.',''); ?></td>
                                            <td width="60"><? echo number_format($fabric_tot_qntyydsIssueRtn,2,'.',''); ?></td>
                                            <td width="40"><? echo number_format($fabric_tot_roll_countIssueRtn,2,'.',''); ?></td>


                                            <td width="70"><? echo number_format($fabric_tot_total_recv_kg,2,'.',''); ?></td>
                                            <td width="70"><? echo number_format($fabric_tot_total_recv_pcs,2,'.',''); ?></td>
                                            <td width="70"><? echo number_format($fabric_tot_total_recv_yds,2,'.',''); ?></td>
                                            <td width="40"><? echo number_format($fabric_tot_total_recv_rollkg,2,'.',''); ?></td>


                                            <td width="60"><? echo number_format($fabric_tot_qntykgIssue,2,'.',''); ?></td>
                                            <td width="60"><? echo number_format($fabric_tot_qntypcsIssue,2,'.',''); ?></td>
                                            <td width="60"><? echo number_format($fabric_tot_qntyydsIssue,2,'.',''); ?></td>
                                            <td width="40"><? echo number_format($fabric_tot_roll_countIssue,2,'.',''); ?></td>

                                            <td width="60"><? echo number_format($fabric_tot_qntykgTransOut,2,'.',''); ?></td>
                                            <td width="60"><? echo number_format($fabric_tot_qntypcsTransOut,2,'.',''); ?></td>
                                            <td width="60"><? echo number_format($fabric_tot_qntyydsTransOut,2,'.',''); ?></td>
                                            <td width="40"><? echo number_format($fabric_tot_roll_countTransOut,2,'.',''); ?></td>

                                            <td width="60"></td>
                                            <td width="60"></td>
                                            <td width="60"></td>
                                            <td width="40"></td>


                                            <td width="60"><? echo number_format($fabric_tot_total_issue_kg,2,'.',''); ?></td>
                                            <td width="60"><? echo number_format($fabric_tot_total_issue_pcs,2,'.',''); ?></td>
                                            <td width="60"><? echo number_format($fabric_tot_total_issue_yds,2,'.',''); ?></td>
                                            <td width="40"><? echo number_format($fabric_tot_total_issue_rollkg,2,'.',''); ?></td>


                                            <td width="60"><? echo number_format($fabric_tot_stock_in_hand_kg,2,'.',''); ?></td>
                                            <td width="60"><? echo number_format($fabric_tot_stock_in_hand_pcs,2,'.',''); ?></td>
                                            <td width="60"><? echo number_format($fabric_tot_stock_in_hand_yds,2,'.',''); ?></td>
                                            <td width="40"><? echo number_format($fabric_tot_stock_in_hand_roll,2,'.',''); ?></td>

                                            <td width="40"></td>
                                            <td width="100"></td>
                                        </tr>
                                        <? 
                                    }
                                }
                                if($show_job_total)
                                {
                                    $show_job_total=0;
                                    ?>
                                    <!-- Job Total -->
                                    <tr class="tbl_bottom">
                                        <td width="100" align="right" colspan="16">Job Total</td>
                                        <td width="60"><? echo number_format($job_tot_requQntyKg,2,'.',''); ?></th>
                                        <td width="60" align="right"><? echo number_format($job_tot_requQntyPcs,2,'.',''); ?></td>
                                        <td width="60" align="center"><? echo number_format($job_tot_requQntyYds,2,'.',''); ?></td>

                                        <td width="60" align="right"><p><? echo number_format($job_tot_rcv_qntykg,2,'.',''); ?></p></td>
                                        <td width="60" align="right"><p><? echo number_format($job_tot_rcv_qntypsc,2,'.',''); ?></p></td>
                                        <td width="60" align="right"><p><? echo number_format($job_tot_rcv_qntyyds,2,'.',''); ?></p></td>
                                        <td width="40" align="right"><p><? echo number_format($job_tot_rcv_roll,2,'.',''); ?></p></td>

                                        <!-- <td width="80"><? //echo number_format($job_tot_recv_kg_balance,2,'.',''); ?></td>
                                        <td width="80"><? //echo number_format($job_tot_recv_pcs_balance,2,'.',''); ?></td>
                                        <td width="80"><? //echo number_format($job_tot_recv_yds_balance,2,'.',''); ?></td> -->

                                        <td width="60"><? echo number_format($job_tot_qntykgIn,2,'.',''); ?></td>
                                        <td width="60"><? echo number_format($job_tot_qntypcsIn,2,'.',''); ?></td>
                                        <td width="60"><? echo number_format($job_tot_qntyydsIn,2,'.',''); ?></td>
                                        <td width="40"><? echo number_format($job_tot_roll_countIn,2,'.',''); ?></td>

                                        <td width="60"><? echo number_format($job_tot_qntykgIssueRtn,2,'.',''); ?></td>
                                        <td width="60"><? echo number_format($job_tot_qntypcsIssueRtn,2,'.',''); ?></td>
                                        <td width="60"><? echo number_format($job_tot_qntyydsIssueRtn,2,'.',''); ?></td>
                                        <td width="40"><? echo number_format($job_tot_roll_countIssueRtn,2,'.',''); ?></td>


                                        <td width="70"><? echo number_format($job_tot_total_recv_kg,2,'.',''); ?></td>
                                        <td width="70"><? echo number_format($job_tot_total_recv_pcs,2,'.',''); ?></td>
                                        <td width="70"><? echo number_format($job_tot_total_recv_yds,2,'.',''); ?></td>
                                        <td width="40"><? echo number_format($job_tot_total_recv_rollkg,2,'.',''); ?></td>


                                        <td width="60"><? echo number_format($job_tot_qntykgIssue,2,'.',''); ?></td>
                                        <td width="60"><? echo number_format($job_tot_qntypcsIssue,2,'.',''); ?></td>
                                        <td width="60"><? echo number_format($job_tot_qntyydsIssue,2,'.',''); ?></td>
                                        <td width="40"><? echo number_format($job_tot_roll_countIssue,2,'.',''); ?></td>

                                        <td width="60"><? echo number_format($job_tot_qntykgTransOut,2,'.',''); ?></td>
                                        <td width="60"><? echo number_format($job_tot_qntypcsTransOut,2,'.',''); ?></td>
                                        <td width="60"><? echo number_format($job_tot_qntyydsTransOut,2,'.',''); ?></td>
                                        <td width="40"><? echo number_format($job_tot_roll_countTransOut,2,'.',''); ?></td>

                                        <td width="60"></td>
                                        <td width="60"></td>
                                        <td width="60"></td>
                                        <td width="40"></td>


                                        <td width="60"><? echo number_format($job_tot_total_issue_kg,2,'.',''); ?></td>
                                        <td width="60"><? echo number_format($job_tot_total_issue_pcs,2,'.',''); ?></td>
                                        <td width="60"><? echo number_format($job_tot_total_issue_yds,2,'.',''); ?></td>
                                        <td width="40"><? echo number_format($job_tot_total_issue_rollkg,2,'.',''); ?></td>


                                        <td width="60"><? echo number_format($job_tot_stock_in_hand_kg,2,'.',''); ?></td>
                                        <td width="60"><? echo number_format($job_tot_stock_in_hand_pcs,2,'.',''); ?></td>
                                        <td width="60"><? echo number_format($job_tot_stock_in_hand_yds,2,'.',''); ?></td>
                                        <td width="40"><? echo number_format($job_tot_stock_in_hand_roll,2,'.',''); ?></td>

                                        <td width="40"></td>
                                        <td width="100"></td>
                                    </tr>
                                    <?
                                }
                            }
                            
                            // non order data show
                            foreach ($non_order_data_arr as $detar_id_key => $detar_id_val)
                            {
                                
                                $fabric_tot_requQntyKg=$fabric_tot_requQntyPcs=$fabric_tot_requQntyYds=$fabric_tot_rcv_qntykg=$fabric_tot_rcv_qntypcs=$fabric_tot_rcv_qntyyds=$fabric_tot_rcv_roll=$fabric_tot_recv_kg_balance=$fabric_tot_recv_pcs_balance=$fabric_tot_recv_yds_balance=$fabric_tot_qntykgIn=$fabric_tot_qntypcsIn=$fabric_tot_qntyydsIn=$fabric_tot_roll_countIn=$fabric_tot_qntykgIssueRtn=$fabric_tot_qntypcsIssueRtn=$fabric_tot_qntyydsIssueRtn=$fabric_tot_roll_countIssueRtn=$fabric_tot_total_recv_kg=$fabric_tot_total_recv_pcs=$fabric_tot_total_recv_yds=$fabric_tot_total_recv_rollkg=$fabric_tot_total_recv_rollpcs=$fabric_tot_total_recv_rollyds=$fabric_tot_qntykgIssue=$fabric_tot_qntypcsIssue=$fabric_tot_qntyydsIssue=$fabric_tot_roll_countIssue=$fabric_tot_issue_balanceKg=$fabric_tot_issue_balancePcs=$fabric_tot_issue_balanceYds=$fabric_tot_qntykgTransOut=$fabric_tot_qntypcsTransOut=$fabric_tot_qntyydsTransOut=$fabric_tot_roll_countTransOut=$fabric_tot_total_issue_kg=$fabric_tot_total_issue_pcs=$fabric_tot_total_issue_yds=$fabric_tot_total_issue_rollkg=$fabric_tot_total_issue_rollpcs=$fabric_tot_total_issue_rollyds=$fabric_tot_stock_in_hand_kg=$fabric_tot_stock_in_hand_pcs=$fabric_tot_stock_in_hand_yds=$fabric_tot_stock_in_hand_roll=0;
                                foreach ($detar_id_val as $color_id_key => $color_id_val)
                                {
                                    $color_tot_requQntyKg=$color_tot_requQntyPcs=$color_tot_requQntyYds=$color_tot_rcv_qntykg=$color_tot_rcv_qntypcs=$color_tot_rcv_qntyyds=$color_tot_rcv_roll=$color_tot_recv_kg_balance=$color_tot_recv_pcs_balance=$color_tot_recv_yds_balance=$color_tot_qntykgIn=$color_tot_qntypcsIn=$color_tot_qntyydsIn=$color_tot_roll_countIn=$color_tot_qntykgIssueRtn=$color_tot_qntypcsIssueRtn=$color_tot_qntyydsIssueRtn=$color_tot_roll_countIssueRtn=$color_tot_total_recv_kg=$color_tot_total_recv_pcs=$color_tot_total_recv_yds=$color_tot_total_recv_rollkg=$color_tot_total_recv_rollpcs=$color_tot_total_recv_rollyds=$color_tot_qntykgIssue=$color_tot_qntypcsIssue=$color_tot_qntyydsIssue=$color_tot_roll_countIssue=$color_tot_issue_balanceKg=$color_tot_issue_balancePcs=$color_tot_issue_balanceYds=$color_tot_qntykgTransOut=$color_tot_qntypcsTransOut=$color_tot_qntyydsTransOut=$color_tot_roll_countTransOut=$color_tot_total_issue_kg=$color_tot_total_issue_pcs=$color_tot_total_issue_yds=$color_tot_total_issue_rollkg=$color_tot_total_issue_rollpcs=$color_tot_total_issue_rollyds=$color_tot_stock_in_hand_kg=$color_tot_stock_in_hand_pcs=$color_tot_stock_in_hand_yds=$color_tot_stock_in_hand_roll=0;
                                    foreach ($color_id_val as $str_ref => $row)
                                    {
                                        $str_ref_arr = explode("*", $str_ref);
                                        $buyer=$str_ref_arr[0];
                                        $style_ref_no=$str_ref_arr[1];
                                        $booking_no=$str_ref_arr[2];
                                        $body_part_id=$str_ref_arr[3];
                                        $yarn_count=$str_ref_arr[4];
                                        $yarn_prod_id=$str_ref_arr[5];
                                        $width=$str_ref_arr[6];
                                        $gsm=$str_ref_arr[7];
                                        $stitch_length=$str_ref_arr[8];
                                        $item_size=$str_ref_arr[9];
                                        $machine_dia_gg=$str_ref_arr[10];

                                        $yarn_counts_arr = explode(",", $yarn_count);
                                        $yarn_counts="";
                                        foreach ($yarn_counts_arr as $count) {
                                            $yarn_counts .= $count_arr[$count] . ",";
                                        }
                                        $yarn_counts = rtrim($yarn_counts, ", ");

                                        $color_arr = explode(",", $color_id_key);
                                        $colors="";
                                        foreach ($color_arr as $color) {
                                            $colors .= $colorArr[$color] . ",";
                                        }
                                        $colors = rtrim($colors, ", ");

                                        $yarn_id_arr = array_unique(array_filter(explode(",", $yarn_prod_id)));
                                        $yarn_brand = $yarn_comp = $yarn_type_name = "";
                                        foreach ($yarn_id_arr as $yid)
                                        {
                                            // $yarn_brand .= ($yarn_brand =="") ? $yarn_ref[$yid]["brand"] :  ",". $yarn_ref[$yid]["brand"];
                                            $yarn_comp .= ($yarn_comp =="") ? $yarn_ref[$yid]["comp"] :  ",". $yarn_ref[$yid]["comp"];
                                            $yarn_type_name .= ($yarn_type_name =="") ? $yarn_ref[$yid]["type"] :  ",". $yarn_ref[$yid]["type"];
                                        }

                                        // $yarn_brand =implode(",",array_filter(array_unique(explode(",", $yarn_brand))));
                                        $yarn_comp =implode(",",array_filter(array_unique(explode(",", $yarn_comp))));
                                        $yarn_type_name =implode(",",array_filter(array_unique(explode(",", $yarn_type_name))));

                                        $qntykgIssue=$issue_data_arr[$detar_id_key][$color_id_key][$str_ref]['qntykgIssue'];
                                        $qntypcsIssue=$issue_data_arr[$detar_id_key][$color_id_key][$str_ref]['qntypcsIssue'];
                                        $qntyydsIssue=$issue_data_arr[$detar_id_key][$color_id_key][$str_ref]['qntyydsIssue'];
                                        $roll_countIssue=$issue_data_arr[$detar_id_key][$color_id_key][$str_ref]['roll_countIssue'];

                                        $issue_balanceKg=$row['qntykg']-$qntykgIssue;
                                        $issue_balancePcs=$row['qntypcs']-$qntypcsIssue;
                                        $issue_balanceYds=$row['qntyyds']-$qntyydsIssue;
                                        
                                        // echo $detar_id_key.'='.$color_id_key.'='.$str_ref;

                                        $qntykgIssueRtn=$issue_rtn_data_arr[$detar_id_key][$color_id_key][$str_ref]['qntykgIssueRtn'];
                                        $qntypcsIssueRtn=$issue_rtn_data_arr[$detar_id_key][$color_id_key][$str_ref]['qntypcsIssueRtn'];
                                        $qntyydsIssueRtn=$issue_rtn_data_arr[$detar_id_key][$color_id_key][$str_ref]['qntyydsIssueRtn'];
                                        $roll_countIssueRtn=$issue_rtn_data_arr[$detar_id_key][$color_id_key][$str_ref]['roll_countIssueRtn'];

                                        $qntykgTransOut=$trans_out_data_arr[$detar_id_key][$color_id_key][$str_ref]['qntykgTransOut'];
                                        $qntypcsTransOut=$trans_out_data_arr[$detar_id_key][$color_id_key][$str_ref]['qntypcsTransOut'];
                                        $qntyydsTransOut=$trans_out_data_arr[$detar_id_key][$color_id_key][$str_ref]['qntyydsTransOut'];
                                        $roll_countTransOut=$trans_out_data_arr[$detar_id_key][$color_id_key][$str_ref]['roll_countTransOut'];
                                        $total_issue_kg=$qntykgIssue+$qntykgTransOut;
                                        $total_issue_pcs=$qntypcsIssue+$qntypcsTransOut;
                                        $total_issue_yds=$qntyydsIssue+$qntyydsTransOut;

                                        $total_recv_kg=$row['qntykg']+$row['qntykgIn']+$qntykgIssueRtn;
                                        $total_recv_pcs=$row['qntypcs']+$row['qntypcsIn']+$qntypcsIssueRtn;
                                        $total_recv_yds=$row['qntyyds']+$row['qntyydsIn']+$qntyydsIssueRtn;
                                        
                                        $recv_rollkg=$non_order_data_arr[$detar_id_key][$color_id_key][$str_ref][12];
                                        $recv_rollpcs=$non_order_data_arr[$detar_id_key][$color_id_key][$str_ref][1];
                                        $recv_rollyds=$non_order_data_arr[$detar_id_key][$color_id_key][$str_ref][27];

                                        $roll_countIssueRtnKg=$issue_rtn_data_arr[$detar_id_key][$color_id_key][$str_ref][12];
                                        $roll_countIssueRtnpcs=$issue_rtn_data_arr[$detar_id_key][$color_id_key][$str_ref][1];
                                        $roll_countIssueRtnyds=$issue_rtn_data_arr[$detar_id_key][$color_id_key][$str_ref][27];

                                        $roll_countIssueKg=$issue_data_arr[$detar_id_key][$color_id_key][$str_ref][12];
                                        $roll_countIssuepcs=$issue_data_arr[$detar_id_key][$color_id_key][$str_ref][1];
                                        $roll_countIssueyds=$issue_data_arr[$detar_id_key][$color_id_key][$str_ref][27];

                                        $roll_countTrans_outKg=$trans_out_data_arr[$detar_id_key][$color_id_key][$str_ref][12];
                                        $roll_countTrans_outpcs=$trans_out_data_arr[$detar_id_key][$color_id_key][$str_ref][1];
                                        $roll_countTrans_outyds=$trans_out_data_arr[$detar_id_key][$color_id_key][$str_ref][27];

                                        $total_recv_rollkg=$recv_rollkg+$roll_countIssueRtnKg;
                                        $total_recv_rollpcs=$recv_rollpcs+$roll_countIssueRtnpcs;
                                        $total_recv_rollyds=$recv_rollyds+$roll_countIssueRtnyds;

                                        $total_issue_rollkg=$roll_countIssueKg+$roll_countTrans_outKg;
                                        $total_issue_rollpcs=$roll_countIssuepcs+$roll_countTrans_outpcs;
                                        $total_issue_rollyds=$roll_countIssueyds+$roll_countTrans_outyds;


                                        $stock_in_hand_kg=$total_recv_kg-$total_issue_kg;
                                        $stock_in_hand_pcs=$total_recv_pcs-$total_issue_pcs;
                                        $stock_in_hand_yds=$total_recv_yds-$total_issue_yds;
                                        $stock_in_hand_roll=($total_recv_rollkg+$total_recv_rollpcs+$total_recv_rollyds)-($total_issue_rollkg+$total_issue_rollpcs+$total_issue_rollyds);

                                        $daysOnHand = datediff("d",change_date_format($transaction_date_array[$row['prod_id']]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
                                        


                                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                        if ((($cbo_get_upto_qnty == 1 && $stock_in_hand_kg > $txt_qnty) || ($cbo_get_upto_qnty == 1 && $stock_in_hand_pcs > $txt_qnty) || ($cbo_get_upto_qnty == 1 && $stock_in_hand_yds > $txt_qnty) || ($cbo_get_upto_qnty == 2 && $stock_in_hand_kg < $txt_qnty) || ($cbo_get_upto_qnty == 2 && $stock_in_hand_pcs < $txt_qnty) || ($cbo_get_upto_qnty == 2 && $stock_in_hand_yds < $txt_qnty) || ($cbo_get_upto_qnty == 3 && $stock_in_hand_kg >= $txt_qnty) || ($cbo_get_upto_qnty == 3 && $stock_in_hand_pcs >= $txt_qnty) || ($cbo_get_upto_qnty == 3 && $stock_in_hand_yds >= $txt_qnty) || ($cbo_get_upto_qnty == 4 && $stock_in_hand_kg <= $txt_qnty) || ($cbo_get_upto_qnty == 4 && $stock_in_hand_pcs <= $txt_qnty) || ($cbo_get_upto_qnty == 4 && $stock_in_hand_yds <= $txt_qnty) || ($cbo_get_upto_qnty == 5 && $stock_in_hand_kg == $txt_qnty) || ($cbo_get_upto_qnty == 5 && $stock_in_hand_pcs == $txt_qnty) || ($cbo_get_upto_qnty == 5 && $stock_in_hand_yds == $txt_qnty) || $cbo_get_upto_qnty == 0) && (($cbo_get_upto == 1 && $daysOnHand > $txt_days) || ($cbo_get_upto == 2 && $daysOnHand < $txt_days) || ($cbo_get_upto == 3 && $daysOnHand >= $txt_days) || ($cbo_get_upto == 4 && $daysOnHand <= $txt_days) || ($cbo_get_upto == 5 && $daysOnHand == $txt_days) || $cbo_get_upto == 0))
                                        {
                                        ?>
                                        <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
                                            <td width="30"><? echo $i; ?></td>
                                            <td width="75"><p><? echo $buyer_arr[$buyer]; ?></p></td>
                                            <td width="100"><p></p></td>
                                            <td width="80"><p><? echo $style_ref_no;?></p></td>
                                            <td width="80"><p><? echo $booking_no; ?></p></td>
                                            <td width="80"><p><? echo $po_number; ?></p></td>
                                            <td width="80"><p><? echo change_date_format($row['ex_fac_date'])?></p></td>
                                            <td width="80"><p><? echo $body_part[$body_part_id]; ?></p></td>
                                            <td width="60"><p><? echo $item_size; ?></p></td>
                                            <td width="70"><p><? echo $colors; ?></p></td>
                                            <td width="110"><p><? echo $constuction_arr[$detar_id_key]; ?></p></td>
                                            <td width="120"><p><? echo $composition_arr[$detar_id_key]; ?></p></td>
                                            <td width="120"><p></p><? echo $yarn_counts.','.$yarn_type_name.','.$yarn_comp; ?></td>
                                            <td width="60"><p><? echo $width; ?></p></td>
                                            <td width="60"><p><? echo $gsm; ?></p></td>
                                            <td width="60"><p><? echo $stitch_length; ?></p></td>

                                            <td width="60" align="right"><p><?
                                            echo ($row['entry_form']!=110 && $row['entry_form']!=180) ? $requQntyKg=number_format($row['requQntyKg'],2,'.','') : $requQntyKg='0.00' ;
                                            ?></p></td>
                                            <td width="60" align="right"><p><? echo ($row['entry_form']!=110 && $row['entry_form']!=180) ? $requQntyPcs=number_format($row['requQntyPcs'],2,'.','') : $requQntyPcs='0.00';?></p></td>
                                            <td width="60" align="right"><p><? echo ($row['entry_form']!=110 && $row['entry_form']!=180) ? $requQntyYds=number_format($row['requQntyYds'],2,'.','') : '0.00';?></p></td>

                                            <td width="60" align="right"><p><? echo number_format($row['qntykg'],2,'.','');?></p></td>
                                            <td width="60" align="right"><p><? echo number_format($row['qntypcs'],2,'.','');?></p></td>
                                            <td width="60" align="right"><p><? echo number_format($row['qntyyds'],2,'.','');?></p></td>
                                            <td width="40" align="right"><p><? echo ($row['entry_form']!=110 && $row['entry_form']!=180) ? $roll_count=number_format($row['roll_count'],2,'.','') : $roll_count='0.00';?></p></td>
                                            
                                            <!-- <td width="60" align="right"><p><? //echo $recv_kg_balance=number_format($requQntyKg-$row['qntykg'],2,'.',''); ?></p></td>
                                            <td width="60" align="right"><p><? //echo $recv_pcs_balance=number_format($requQntyPcs-$row['qntypcs'],2,'.',''); ?></p></td> 
                                            <td width="60" align="right"><p><? //echo $recv_yds_balance=number_format($requQntyYds-$row['qntyyds'],2,'.',''); ?></p></td> -->


                                            <td width="60" align="right"><p><? echo number_format($row['qntykgIn'],2,'.','');?></p></td>
                                            <td width="60" align="right"><p><? echo number_format($row['qntypcsIn'],2,'.','');?></p></td>
                                            <td width="60" align="right"><p><? echo number_format($row['qntyydsIn'],2,'.','');?></p></td>
                                            <td width="40" align="right"><p><? echo number_format($row['roll_countIn'],2,'.','');?></p></td>

                                            <td width="60" align="right"><p><? echo number_format($qntykgIssueRtn,2,'.','');?></p></td>
                                            <td width="60" align="right"><p><? echo number_format($qntypcsIssueRtn,2,'.','');?></p></td>
                                            <td width="60" align="right"><p><? echo number_format($qntyydsIssueRtn,2,'.','');?></p></td>
                                            <td width="40" align="right"><p><? echo number_format($roll_countIssueRtn,2,'.','');?></p></td>


                                            <td width="70" align="right"><p></p><? echo number_format($total_recv_kg,2,'.','');?></td>
                                            <td width="70" align="right"><p><? echo number_format($total_recv_pcs,2,'.','');?></p></td>
                                            <td width="70" align="right"><p><? echo number_format($total_recv_yds,2,'.','');?></p></td>
                                            <td width="40" align="right"><p><? echo number_format($total_recv_rollkg,2,'.',''); ?></p></td>


                                            <td width="60" align="right"><p><? echo number_format($qntykgIssue,2,'.','');?></p></td>
                                            <td width="60" align="right"><p><? echo number_format($qntypcsIssue,2,'.','');?></p></td>
                                            <td width="60" align="right"><p><? echo number_format($qntyydsIssue,2,'.','');?></p></td>
                                            <td width="40" align="right"><p><? echo number_format($roll_countIssue,2,'.','');?></p></td>

                                            <td width="60" align="right"><p><? echo number_format($qntykgTransOut,2,'.','');?></p></p></td>
                                            <td width="60" align="right"><p><? echo number_format($qntypcsTransOut,2,'.','');?></p></td> 
                                            <td width="60" align="right"><p><? echo number_format($qntyydsTransOut,2,'.','');?></p></td>
                                            <td width="40" align="right"><p><? echo number_format($roll_countTransOut,2,'.','');?></p></td>
                                            
                                            <td width="60" align="right"><p></p></td>
                                            <td width="60" align="right"><p></p></td>
                                            <td width="60" align="right"><p></p></td>
                                            <td width="40" align="right"><p></p></td>


                                            <td width="60" align="right"><p><? echo number_format($total_issue_kg,2,'.','');?></p></td>
                                            <td width="60" align="right"><p><? echo number_format($total_issue_pcs,2,'.','');?></p></td>
                                            <td width="60" align="right"><p><? echo number_format($total_issue_yds,2,'.','');?></p></td>
                                            <td width="40" align="right"><p><? echo number_format($total_issue_rollkg,2,'.','');?></p></td>


                                            <td width="60" align="right"><p><? echo number_format($stock_in_hand_kg,2,'.','');?></p></td>
                                            <td width="60" align="right"><p><? echo number_format($stock_in_hand_pcs,2,'.','');?></p></td>
                                            <td width="60" align="right"><p><? echo number_format($stock_in_hand_yds,2,'.','');?></p></td>
                                            <td width="40" align="right"><p><? echo number_format($stock_in_hand_roll,2,'.','');?></p></td>


                                            <td width="40" align="center"><p><? echo $daysOnHand;?></p></td>
                                            <td width="100"><p><? echo $store_arr[$row['store_id']]; ?></p></td>
                                        </tr>
                                        <?
                                        $color_rev_summary_arr[$color_id_key]['colorRcvKg']+=$row['qntykg']+$row['qntykgIn']+$qntykgIssueRtn;
                                        $color_rev_summary_arr[$color_id_key]['colorRcvPcs']+=$row['qntypcs']+$row['qntypcsIn']+$qntypcsIssueRtn;
                                        $color_rev_summary_arr[$color_id_key]['colorRcvYds']+=$row['qntyyds']+$row['qntyydsIn']+$qntyydsIssueRtn;
                                        $color_rev_summary_arr[$color_id_key]['ColorRollCount']+=$roll_count+$row['roll_countIn']+$roll_countIssueRtn;
                                        
                                        $color_issue_summary_arr[$color_id_key]['colorIssKg']+=$qntykgIssue+$qntykgTransOut;
                                        $color_issue_summary_arr[$color_id_key]['colorIssPcs']+=$qntypcsIssue+$qntypcsTransOut;
                                        $color_issue_summary_arr[$color_id_key]['colorIssYds']+=$qntyydsIssue+$qntyydsTransOut;
                                        $color_issue_summary_arr[$color_id_key]['ColorRollCount']+=$roll_countIssue+$roll_countTransOut;

                                        $show_color_total=1;
                                        $show_fabric_total=1;
                                        $show_job_total=1;
                                        $i++;
                                        $color_tot_requQntyKg+=$requQntyKg;
                                        $color_tot_requQntyPcs+=$requQntyPcs;
                                        $color_tot_requQntyYds+=$requQntyYds;

                                        $color_tot_rcv_qntykg+=$row['qntykg'];
                                        $color_tot_rcv_qntypcs+=$row['qntypcs'];
                                        $color_tot_rcv_qntyyds+=$row['qntyyds'];
                                        $color_tot_rcv_roll+=$roll_count;

                                        $color_tot_recv_kg_balance+=$recv_kg_balance;
                                        $color_tot_recv_pcs_balance+=$recv_pcs_balance;
                                        $color_tot_recv_yds_balance+=$recv_yds_balance;

                                        $color_tot_qntykgIn+=$row['qntykgIn'];
                                        $color_tot_qntypcsIn+=$row['qntypcsIn'];
                                        $color_tot_qntyydsIn+=$row['qntyydsIn'];
                                        $color_tot_roll_countIn+=$row['roll_countIn'];

                                        $color_tot_qntykgIssueRtn+=$qntykgIssueRtn;
                                        $color_tot_qntypcsIssueRtn+=$qntypcsIssueRtn;
                                        $color_tot_qntyydsIssueRtn+=$qntyydsIssueRtn;
                                        $color_tot_roll_countIssueRtn+=$roll_countIssueRtn;

                                        $color_tot_total_recv_kg+=$total_recv_kg;
                                        $color_tot_total_recv_pcs+=$total_recv_pcs;
                                        $color_tot_total_recv_yds+=$total_recv_yds;
                                        $color_tot_total_recv_rollkg+=$total_recv_rollkg;
                                        $color_tot_total_recv_rollpcs+=$total_recv_rollpcs;
                                        $color_tot_total_recv_rollyds+=$total_recv_rollyds;

                                        $color_tot_qntykgIssue+=$qntykgIssue;
                                        $color_tot_qntypcsIssue+=$qntypcsIssue;
                                        $color_tot_qntyydsIssue+=$qntyydsIssue;
                                        $color_tot_roll_countIssue+=$roll_countIssue;

                                        $color_tot_issue_balanceKg+=$issue_balanceKg;
                                        $color_tot_issue_balancePcs+=$issue_balancePcs;
                                        $color_tot_issue_balanceYds+=$issue_balanceYds;

                                        $color_tot_qntykgTransOut+=$qntykgTransOut;
                                        $color_tot_qntypcsTransOut+=$qntypcsTransOut;
                                        $color_tot_qntyydsTransOut+=$qntyydsTransOut;
                                        $color_tot_roll_countTransOut+=$roll_countTransOut;

                                        $color_tot_total_issue_kg+=$total_issue_kg;
                                        $color_tot_total_issue_pcs+=$total_issue_pcs;
                                        $color_tot_total_issue_yds+=$total_issue_yds;
                                        $color_tot_total_issue_rollkg+=$total_issue_rollkg;
                                        $color_tot_total_issue_rollpcs+=$total_issue_rollpcs;
                                        $color_tot_total_issue_rollyds+=$total_issue_rollyds;

                                        $color_tot_stock_in_hand_kg+=$stock_in_hand_kg;
                                        $color_tot_stock_in_hand_pcs+=$stock_in_hand_pcs;
                                        $color_tot_stock_in_hand_yds+=$stock_in_hand_yds;
                                        $color_tot_stock_in_hand_roll+=$stock_in_hand_roll;

                                        // ============================================

                                        $fabric_tot_requQntyKg+=$requQntyKg;
                                        $fabric_tot_requQntyPcs+=$requQntyPcs;
                                        $fabric_tot_requQntyYds+=$requQntyYds;

                                        $fabric_tot_rcv_qntykg+=$row['qntykg'];
                                        $fabric_tot_rcv_qntypcs+=$row['qntypcs'];
                                        $fabric_tot_rcv_qntyyds+=$row['qntyyds'];
                                        $fabric_tot_rcv_roll+=$roll_count;

                                        $fabric_tot_recv_kg_balance+=$recv_kg_balance;
                                        $fabric_tot_recv_pcs_balance+=$recv_pcs_balance;
                                        $fabric_tot_recv_yds_balance+=$recv_yds_balance;

                                        $fabric_tot_qntykgIn+=$row['qntykgIn'];
                                        $fabric_tot_qntypcsIn+=$row['qntypcsIn'];
                                        $fabric_tot_qntyydsIn+=$row['qntyydsIn'];
                                        $fabric_tot_roll_countIn+=$row['roll_countIn'];

                                        $fabric_tot_qntykgIssueRtn+=$qntykgIssueRtn;
                                        $fabric_tot_qntypcsIssueRtn+=$qntypcsIssueRtn;
                                        $fabric_tot_qntyydsIssueRtn+=$qntyydsIssueRtn;
                                        $fabric_tot_roll_countIssueRtn+=$roll_countIssueRtn;

                                        $fabric_tot_total_recv_kg+=$total_recv_kg;
                                        $fabric_tot_total_recv_pcs+=$total_recv_pcs;
                                        $fabric_tot_total_recv_yds+=$total_recv_yds;
                                        $fabric_tot_total_recv_rollkg+=$total_recv_rollkg;
                                        $fabric_tot_total_recv_rollpcs+=$total_recv_rollpcs;
                                        $fabric_tot_total_recv_rollyds+=$total_recv_rollyds;

                                        $fabric_tot_qntykgIssue+=$qntykgIssue;
                                        $fabric_tot_qntypcsIssue+=$qntypcsIssue;
                                        $fabric_tot_qntyydsIssue+=$qntyydsIssue;
                                        $fabric_tot_roll_countIssue+=$roll_countIssue;

                                        $fabric_tot_issue_balanceKg+=$issue_balanceKg;
                                        $fabric_tot_issue_balancePcs+=$issue_balancePcs;
                                        $fabric_tot_issue_balanceYds+=$issue_balanceYds;

                                        $fabric_tot_qntykgTransOut+=$qntykgTransOut;
                                        $fabric_tot_qntypcsTransOut+=$qntypcsTransOut;
                                        $fabric_tot_qntyydsTransOut+=$qntyydsTransOut;
                                        $fabric_tot_roll_countTransOut+=$roll_countTransOut;

                                        $fabric_tot_total_issue_kg+=$total_issue_kg;
                                        $fabric_tot_total_issue_pcs+=$total_issue_pcs;
                                        $fabric_tot_total_issue_yds+=$total_issue_yds;
                                        $fabric_tot_total_issue_rollkg+=$total_issue_rollkg;
                                        $fabric_tot_total_issue_rollpcs+=$total_issue_rollpcs;
                                        $fabric_tot_total_issue_rollyds+=$total_issue_rollyds;

                                        $fabric_tot_stock_in_hand_kg+=$stock_in_hand_kg;
                                        $fabric_tot_stock_in_hand_pcs+=$stock_in_hand_pcs;
                                        $fabric_tot_stock_in_hand_yds+=$stock_in_hand_yds;
                                        $fabric_tot_stock_in_hand_roll+=$stock_in_hand_roll;

                                        // ============================================

                                        $job_tot_requQntyKg+=$requQntyKg;
                                        $job_tot_requQntyPcs+=$requQntyPcs;
                                        $job_tot_requQntyYds+=$requQntyYds;

                                        $job_tot_rcv_qntykg+=$row['qntykg'];
                                        $job_tot_rcv_qntypcs+=$row['qntypcs'];
                                        $job_tot_rcv_qntyyds+=$row['qntyyds'];
                                        $job_tot_rcv_roll+=$roll_count;

                                        $job_tot_recv_kg_balance+=$recv_kg_balance;
                                        $job_tot_recv_pcs_balance+=$recv_pcs_balance;
                                        $job_tot_recv_yds_balance+=$recv_yds_balance;

                                        $job_tot_qntykgIn+=$row['qntykgIn'];
                                        $job_tot_qntypcsIn+=$row['qntypcsIn'];
                                        $job_tot_qntyydsIn+=$row['qntyydsIn'];
                                        $job_tot_roll_countIn+=$row['roll_countIn'];

                                        $job_tot_qntykgIssueRtn+=$qntykgIssueRtn;
                                        $job_tot_qntypcsIssueRtn+=$qntypcsIssueRtn;
                                        $job_tot_qntyydsIssueRtn+=$qntyydsIssueRtn;
                                        $job_tot_roll_countIssueRtn+=$roll_countIssueRtn;

                                        $job_tot_total_recv_kg+=$total_recv_kg;
                                        $job_tot_total_recv_pcs+=$total_recv_pcs;
                                        $job_tot_total_recv_yds+=$total_recv_yds;
                                        $job_tot_total_recv_rollkg+=$total_recv_rollkg;
                                        $job_tot_total_recv_rollpcs+=$total_recv_rollpcs;
                                        $job_tot_total_recv_rollyds+=$total_recv_rollyds;

                                        $job_tot_qntykgIssue+=$qntykgIssue;
                                        $job_tot_qntypcsIssue+=$qntypcsIssue;
                                        $job_tot_qntyydsIssue+=$qntyydsIssue;
                                        $job_tot_roll_countIssue+=$roll_countIssue;

                                        $job_tot_issue_balanceKg+=$issue_balanceKg;
                                        $job_tot_issue_balancePcs+=$issue_balancePcs;
                                        $job_tot_issue_balanceYds+=$issue_balanceYds;

                                        $job_tot_qntykgTransOut+=$qntykgTransOut;
                                        $job_tot_qntypcsTransOut+=$qntypcsTransOut;
                                        $job_tot_qntyydsTransOut+=$qntyydsTransOut;
                                        $job_tot_roll_countTransOut+=$roll_countTransOut;

                                        $job_tot_total_issue_kg+=$total_issue_kg;
                                        $job_tot_total_issue_pcs+=$total_issue_pcs;
                                        $job_tot_total_issue_yds+=$total_issue_yds;
                                        $job_tot_total_issue_rollkg+=$total_issue_rollkg;
                                        $job_tot_total_issue_rollpcs+=$total_issue_rollpcs;
                                        $job_tot_total_issue_rollyds+=$total_issue_rollyds;

                                        $job_tot_stock_in_hand_kg+=$stock_in_hand_kg;
                                        $job_tot_stock_in_hand_pcs+=$stock_in_hand_pcs;
                                        $job_tot_stock_in_hand_yds+=$stock_in_hand_yds;
                                        $job_tot_stock_in_hand_roll+=$stock_in_hand_roll;

                                        // ============================================

                                        $grand_tot_requQntyKg+=$requQntyKg;
                                        $grand_tot_requQntyPcs+=$requQntyPcs;
                                        $grand_tot_requQntyYds+=$requQntyYds;

                                        $grand_tot_rcv_qntykg+=$row['qntykg'];
                                        $grand_tot_rcv_qntypcs+=$row['qntypcs'];
                                        $grand_tot_rcv_qntyyds+=$row['qntyyds'];
                                        $grand_tot_rcv_roll+=$roll_count;

                                        $grand_tot_recv_kg_balance+=$recv_kg_balance;
                                        $grand_tot_recv_pcs_balance+=$recv_pcs_balance;
                                        $grand_tot_recv_yds_balance+=$recv_yds_balance;

                                        $grand_tot_qntykgIn+=$row['qntykgIn'];
                                        $grand_tot_qntypcsIn+=$row['qntypcsIn'];
                                        $grand_tot_qntyydsIn+=$row['qntyydsIn'];
                                        $grand_tot_roll_countIn+=$row['roll_countIn'];

                                        $grand_tot_qntykgIssueRtn+=$qntykgIssueRtn;
                                        $grand_tot_qntypcsIssueRtn+=$qntypcsIssueRtn;
                                        $grand_tot_qntyydsIssueRtn+=$qntyydsIssueRtn;
                                        $grand_tot_roll_countIssueRtn+=$roll_countIssueRtn;

                                        $grand_tot_total_recv_kg+=$total_recv_kg;
                                        $grand_tot_total_recv_pcs+=$total_recv_pcs;
                                        $grand_tot_total_recv_yds+=$total_recv_yds;
                                        $grand_tot_total_recv_rollkg+=$total_recv_rollkg;
                                        $grand_tot_total_recv_rollpcs+=$total_recv_rollpcs;
                                        $grand_tot_total_recv_rollyds+=$total_recv_rollyds;

                                        $grand_tot_qntykgIssue+=$qntykgIssue;
                                        $grand_tot_qntypcsIssue+=$qntypcsIssue;
                                        $grand_tot_qntyydsIssue+=$qntyydsIssue;
                                        $grand_tot_roll_countIssue+=$roll_countIssue;

                                        $grand_tot_issue_balanceKg+=$issue_balanceKg;
                                        $grand_tot_issue_balancePcs+=$issue_balancePcs;
                                        $grand_tot_issue_balanceYds+=$issue_balanceYds;

                                        $grand_tot_qntykgTransOut+=$qntykgTransOut;
                                        $grand_tot_qntypcsTransOut+=$qntypcsTransOut;
                                        $grand_tot_qntyydsTransOut+=$qntyydsTransOut;
                                        $grand_tot_roll_countTransOut+=$roll_countTransOut;

                                        $grand_tot_total_issue_kg+=$total_issue_kg;
                                        $grand_tot_total_issue_pcs+=$total_issue_pcs;
                                        $grand_tot_total_issue_yds+=$total_issue_yds;
                                        $grand_tot_total_issue_rollkg+=$total_issue_rollkg;
                                        $grand_tot_total_issue_rollpcs+=$total_issue_rollpcs;
                                        $grand_tot_total_issue_rollyds+=$total_issue_rollyds;

                                        $grand_tot_stock_in_hand_kg+=$stock_in_hand_kg;
                                        $grand_tot_stock_in_hand_pcs+=$stock_in_hand_pcs;
                                        $grand_tot_stock_in_hand_yds+=$stock_in_hand_yds;
                                        $grand_tot_stock_in_hand_roll+=$stock_in_hand_roll;

                                        }
                                    }
                                    if($show_color_total)
                                    {
                                        $show_color_total=0;
                                        ?>
                                        <!-- Color Total -->
                                        <tr class="tbl_bottom">
                                            <td width="100" align="right" colspan="16">Color Total</td>
                                            <td width="60"><? echo number_format($color_tot_requQntyKg,2,'.',''); ?></th>
                                            <td width="60" align="right"><? echo number_format($color_tot_requQntyPcs,2,'.',''); ?></td>
                                            <td width="60" align="center"><? echo number_format($color_tot_requQntyYds,2,'.',''); ?></td>

                                            <td width="60" align="right"><p><? echo number_format($color_tot_rcv_qntykg,2,'.',''); ?></p></td>
                                            <td width="60" align="right"><p><? echo number_format($color_tot_rcv_qntypsc,2,'.',''); ?></p></td>
                                            <td width="60" align="right"><p><? echo number_format($color_tot_rcv_qntyyds,2,'.',''); ?></p></td>
                                            <td width="40" align="right"><p><? echo number_format($color_tot_rcv_roll,2,'.',''); ?></p></td>

                                            <!-- <td width="60"><? //echo number_format($color_tot_recv_kg_balance,2,'.',''); ?></td>
                                            <td width="60"><? //echo number_format($color_tot_recv_pcs_balance,2,'.',''); ?></td>
                                            <td width="60"><? //echo number_format($color_tot_recv_yds_balance,2,'.',''); ?></td> -->

                                            <td width="60"><? echo number_format($color_tot_qntykgIn,2,'.',''); ?></td>
                                            <td width="60"><? echo number_format($color_tot_qntypcsIn,2,'.',''); ?></td>
                                            <td width="60"><? echo number_format($color_tot_qntyydsIn,2,'.',''); ?></td>
                                            <td width="40"><? echo number_format($color_tot_roll_countIn,2,'.',''); ?></td>

                                            <td width="60"><? echo number_format($color_tot_qntykgIssueRtn,2,'.',''); ?></td>
                                            <td width="60"><? echo number_format($color_tot_qntypcsIssueRtn,2,'.',''); ?></td>
                                            <td width="60"><? echo number_format($color_tot_qntyydsIssueRtn,2,'.',''); ?></td>
                                            <td width="40"><? echo number_format($color_tot_roll_countIssueRtn,2,'.',''); ?></td>


                                            <td width="70"><? echo number_format($color_tot_total_recv_kg,2,'.',''); ?></td>
                                            <td width="70"><? echo number_format($color_tot_total_recv_pcs,2,'.',''); ?></td>
                                            <td width="70"><? echo number_format($color_tot_total_recv_yds,2,'.',''); ?></td>
                                            <td width="40"><? echo number_format($color_tot_total_recv_rollkg,2,'.',''); ?></td>


                                            <td width="60"><? echo number_format($color_tot_qntykgIssue,2,'.',''); ?></td>
                                            <td width="60"><? echo number_format($color_tot_qntypcsIssue,2,'.',''); ?></td>
                                            <td width="60"><? echo number_format($color_tot_qntyydsIssue,2,'.',''); ?></td>
                                            <td width="40"><? echo number_format($color_tot_roll_countIssue,2,'.',''); ?></td>

                                            <td width="60"><? echo number_format($color_tot_qntykgTransOut,2,'.',''); ?></td>
                                            <td width="60"><? echo number_format($color_tot_qntypcsTransOut,2,'.',''); ?></td>
                                            <td width="60"><? echo number_format($color_tot_qntyydsTransOut,2,'.',''); ?></td>
                                            <td width="40"><? echo number_format($color_tot_roll_countTransOut,2,'.',''); ?></td>

                                            <td width="60"></td>
                                            <td width="60"></td>
                                            <td width="60"></td>
                                            <td width="40"></td>


                                            <td width="60"><? echo number_format($color_tot_total_issue_kg,2,'.',''); ?></td>
                                            <td width="60"><? echo number_format($color_tot_total_issue_pcs,2,'.',''); ?></td>
                                            <td width="60"><? echo number_format($color_tot_total_issue_yds,2,'.',''); ?></td>
                                            <td width="40"><? echo number_format($color_tot_total_issue_rollkg,2,'.',''); ?></td>


                                            <td width="60"><? echo number_format($color_tot_stock_in_hand_kg,2,'.',''); ?></td>
                                            <td width="60"><? echo number_format($color_tot_stock_in_hand_pcs,2,'.',''); ?></td>
                                            <td width="60"><? echo number_format($color_tot_stock_in_hand_yds,2,'.',''); ?></td>
                                            <td width="40"><? echo number_format($color_tot_stock_in_hand_roll,2,'.',''); ?></td>

                                            <td width="40"></td>
                                            <td width="100"></td>
                                        </tr>
                                        <? 
                                    }
                                }
                                if($show_fabric_total)
                                {
                                    $show_fabric_total=0;
                                    ?>
                                    <!-- Fabric Total -->
                                    <tr class="tbl_bottom">
                                        <td width="100" align="right" colspan="16">Fabric Total</td>
                                        <td width="60"><? echo number_format($fabric_tot_requQntyKg,2,'.',''); ?></th>
                                        <td width="60" align="right"><? echo number_format($fabric_tot_requQntyPcs,2,'.',''); ?></td>
                                        <td width="60" align="center"><? echo number_format($fabric_tot_requQntyYds,2,'.',''); ?></td>

                                        <td width="60" align="right"><p><? echo number_format($fabric_tot_rcv_qntykg,2,'.',''); ?></p></td>
                                        <td width="60" align="right"><p><? echo number_format($fabric_tot_rcv_qntypsc,2,'.',''); ?></p></td>
                                        <td width="60" align="right"><p><? echo number_format($fabric_tot_rcv_qntyyds,2,'.',''); ?></p></td>
                                        <td width="40" align="right"><p><? echo number_format($fabric_tot_rcv_roll,2,'.',''); ?></p></td>

                                        <!-- <td width="60"><? //echo number_format($fabric_tot_recv_kg_balance,2,'.',''); ?></td>
                                        <td width="60"><? //echo number_format($fabric_tot_recv_pcs_balance,2,'.',''); ?></td>
                                        <td width="60"><? //echo number_format($fabric_tot_recv_yds_balance,2,'.',''); ?></td> -->

                                        <td width="60"><? echo number_format($fabric_tot_qntykgIn,2,'.',''); ?></td>
                                        <td width="60"><? echo number_format($fabric_tot_qntypcsIn,2,'.',''); ?></td>
                                        <td width="60"><? echo number_format($fabric_tot_qntyydsIn,2,'.',''); ?></td>
                                        <td width="40"><? echo number_format($fabric_tot_roll_countIn,2,'.',''); ?></td>

                                        <td width="60"><? echo number_format($fabric_tot_qntykgIssueRtn,2,'.',''); ?></td>
                                        <td width="60"><? echo number_format($fabric_tot_qntypcsIssueRtn,2,'.',''); ?></td>
                                        <td width="60"><? echo number_format($fabric_tot_qntyydsIssueRtn,2,'.',''); ?></td>
                                        <td width="40"><? echo number_format($fabric_tot_roll_countIssueRtn,2,'.',''); ?></td>


                                        <td width="70"><? echo number_format($fabric_tot_total_recv_kg,2,'.',''); ?></td>
                                        <td width="70"><? echo number_format($fabric_tot_total_recv_pcs,2,'.',''); ?></td>
                                        <td width="70"><? echo number_format($fabric_tot_total_recv_yds,2,'.',''); ?></td>
                                        <td width="40"><? echo number_format($fabric_tot_total_recv_rollkg,2,'.',''); ?></td>


                                        <td width="60"><? echo number_format($fabric_tot_qntykgIssue,2,'.',''); ?></td>
                                        <td width="60"><? echo number_format($fabric_tot_qntypcsIssue,2,'.',''); ?></td>
                                        <td width="60"><? echo number_format($fabric_tot_qntyydsIssue,2,'.',''); ?></td>
                                        <td width="60"><? echo number_format($fabric_tot_roll_countIssue,2,'.',''); ?></td>

                                        <td width="60"><? echo number_format($fabric_tot_qntykgTransOut,2,'.',''); ?></td>
                                        <td width="60"><? echo number_format($fabric_tot_qntypcsTransOut,2,'.',''); ?></td>
                                        <td width="60"><? echo number_format($fabric_tot_qntyydsTransOut,2,'.',''); ?></td>
                                        <td width="40"><? echo number_format($fabric_tot_roll_countTransOut,2,'.',''); ?></td>

                                        <td width="60"></td>
                                        <td width="60"></td>
                                        <td width="60"></td>
                                        <td width="40"></td>


                                        <td width="60"><? echo number_format($fabric_tot_total_issue_kg,2,'.',''); ?></td>
                                        <td width="60"><? echo number_format($fabric_tot_total_issue_pcs,2,'.',''); ?></td>
                                        <td width="60"><? echo number_format($fabric_tot_total_issue_yds,2,'.',''); ?></td>
                                        <td width="40"><? echo number_format($fabric_tot_total_issue_rollkg,2,'.',''); ?></td>


                                        <td width="60"><? echo number_format($fabric_tot_stock_in_hand_kg,2,'.',''); ?></td>
                                        <td width="60"><? echo number_format($fabric_tot_stock_in_hand_pcs,2,'.',''); ?></td>
                                        <td width="60"><? echo number_format($fabric_tot_stock_in_hand_yds,2,'.',''); ?></td>
                                        <td width="40"><? echo number_format($fabric_tot_stock_in_hand_roll,2,'.',''); ?></td>

                                        <td width="40"></td>
                                        <td width="100"></td>
                                    </tr>
                                    <? 
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <table class="rpt_table" width="4225" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tfoot>
                        <tr>
                            <th width="30" class="word_wrap_break"></th>
                            <th width="75" class="word_wrap_break"></th>
                            <th width="100" class="word_wrap_break"></th>
                            <th width="80" class="word_wrap_break"></th>
                            <th width="80" class="word_wrap_break"></th>
                            <th width="80" class="word_wrap_break"></th>
                            <th width="80" class="word_wrap_break"></th>
                            <th width="80" class="word_wrap_break"></th>
                            <th width="60" class="word_wrap_break"></th>
                            <th width="70" class="word_wrap_break"></th>
                            <th width="110" class="word_wrap_break"></th>
                            <th width="120" class="word_wrap_break"></th>
                            <th width="120" class="word_wrap_break"></th>
                            <th width="60" class="word_wrap_break"></th>
                            <th width="60" class="word_wrap_break"></th>
                            <th width="60" class="word_wrap_break" align="right">Grand Total:</th>


                            <th width="60"><? echo number_format($grand_tot_requQntyKg,2,'.',''); ?></th>
                            <th width="60" align="right"><? echo number_format($grand_tot_requQntyPcs,2,'.',''); ?></th>
                            <th width="60" align="center"><? echo number_format($grand_tot_requQntyYds,2,'.',''); ?></th>

                            <th width="60"><p><? echo number_format($grand_tot_rcv_qntykg,2,'.',''); ?></p></th>
                            <th width="60"><p><? echo number_format($grand_tot_rcv_qntypcs,2,'.',''); ?></p></th>
                            <th width="60"><p><? echo number_format($grand_tot_rcv_qntyyds,2,'.',''); ?></p></th>
                            <th width="40"><p><? echo number_format($grand_tot_rcv_roll,2,'.',''); ?></p></th>

                            <!-- <th width="60"><p><? //echo number_format($grand_tot_recv_kg_balance,2,'.',''); ?></p></th>
                            <th width="60"><p><? //echo number_format($grand_tot_recv_pcs_balance,2,'.',''); ?></p></th>
                            <th width="60"><p><? //echo number_format($grand_tot_recv_yds_balance,2,'.',''); ?></p></th> -->

                            <th width="60"><p><? echo number_format($grand_tot_qntykgIn,2,'.',''); ?></p></th>
                            <th width="60"><p><? echo number_format($grand_tot_qntypcsIn,2,'.',''); ?></p></th>
                            <th width="60"><p><? echo number_format($grand_tot_qntyydsIn,2,'.',''); ?></p></th>
                            <th width="40"><p><? echo number_format($grand_tot_roll_countIn,2,'.',''); ?></p></th>

                            <th width="60"><p><? echo number_format($grand_tot_qntykgIssueRtn,2,'.',''); ?></p></th>
                            <th width="60"><p><? echo number_format($grand_tot_qntypcsIssueRtn,2,'.',''); ?></p></th>
                            <th width="60"><p><? echo number_format($grand_tot_qntyydsIssueRtn,2,'.',''); ?></p></th>
                            <th width="40"><p><? echo number_format($grand_tot_roll_countIssueRtn,2,'.',''); ?></p></th>


                            <th width="70"><p><? echo number_format($grand_tot_total_recv_kg,2,'.',''); ?></p></th>
                            <th width="70"><p><? echo number_format($grand_tot_total_recv_pcs,2,'.',''); ?></p></th>
                            <th width="70"><p><? echo number_format($grand_tot_total_recv_yds,2,'.',''); ?></p></th>
                            <th width="40"><p><? echo number_format($grand_tot_total_recv_rollkg,2,'.',''); ?></p></th>


                            <th width="60"><p><? echo number_format($grand_tot_qntykgIssue,2,'.',''); ?></p></th>
                            <th width="60"><p><? echo number_format($grand_tot_qntypcsIssue,2,'.',''); ?></p></th>
                            <th width="60"><p><? echo number_format($grand_tot_qntyydsIssue,2,'.',''); ?></p></th>
                            <th width="40"><p><? echo number_format($grand_tot_roll_countIssue,2,'.',''); ?></p></th>

                            <th width="60"><p><? echo number_format($grand_tot_qntykgTransOut,2,'.',''); ?></p></th>
                            <th width="60"><p><? echo number_format($grand_tot_qntypcsTransOut,2,'.',''); ?></p></th>
                            <th width="60"><p><? echo number_format($grand_tot_qntyydsTransOut,2,'.',''); ?></p></th>
                            <th width="40"><p><? echo number_format($grand_tot_roll_countTransOut,2,'.',''); ?></p></th>

                            <th width="60"></th>
                            <th width="60"></th>
                            <th width="60"></th>
                            <th width="40"></th>


                            <th width="60"><p><? echo number_format($grand_tot_total_issue_kg,2,'.',''); ?></p></th>
                            <th width="60"><p><? echo number_format($grand_tot_total_issue_pcs,2,'.',''); ?></p></th>
                            <th width="60"><p><? echo number_format($grand_tot_total_issue_yds,2,'.',''); ?></p></th>
                            <th width="40"><p><? echo number_format($grand_tot_total_issue_rollkg,2,'.',''); ?></p></th>


                            <th width="60"><p><? echo number_format($grand_tot_stock_in_hand_kg,2,'.',''); ?></p></th>
                            <th width="60"><p><? echo number_format($grand_tot_stock_in_hand_pcs,2,'.',''); ?></p></th>
                            <th width="60"><p><? echo number_format($grand_tot_stock_in_hand_yds,2,'.',''); ?></p></th>
                            <th width="40"><p><? echo number_format($grand_tot_stock_in_hand_roll,2,'.',''); ?></p></th>

                            <th width="40"></th>
                            <th width="100"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <br>
            <!-- Summary Start -->
            <!-- Recv issue summary start -->
            <table width="10%">
                <tr>
                    <td width="50%" style="float: left;">
                        <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                            <tr>
                                <td colspan="2" align="center" style="font-size: 13px;">RECEIVE SUMMERY</td>
                                <td style="font-size: 13px;">Total Receive</td>
                            </tr>
                            <tr>
                                <td rowspan="4" align="center" style="font-size: 13px; vertical-align: middle;">RECEIVE</td>
                                <td style="font-size: 13px;">Receive Qty KG</td>
                                <td style="font-size: 13px;" align="right"><p><? echo number_format($grand_tot_rcv_qntykg,2,'.',''); ?></p></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Receive Qty PCS</td>
                                <td style="font-size: 13px;" align="right"><p><? echo number_format($grand_tot_rcv_qntypcs,2,'.',''); ?></p></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Receive Qty YDS</td>
                                <td style="font-size: 13px;" align="right"><p><? echo number_format($grand_tot_rcv_qntyyds,2,'.',''); ?></p></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Roll Qty</td>
                                <td style="font-size: 13px;" align="right"><p><? echo number_format($grand_tot_rcv_roll,2,'.',''); ?></p></td>
                            </tr>
                            <!--  -->
                            <tr>
                                <td rowspan="4" align="center" style="font-size: 13px; vertical-align: middle;">ISSUE RETURN</td>
                                <td style="font-size: 13px;">Receive Qty KG</td>
                                <td style="font-size: 13px;" align="right"><p><? echo number_format($grand_tot_qntykgIssueRtn,2,'.',''); ?></p></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Receive Qty PCS</td>
                                <td style="font-size: 13px;" align="right"><p><? echo number_format($grand_tot_qntypcsIssueRtn,2,'.',''); ?></p></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Receive Qty YDS</td>
                                <td style="font-size: 13px;" align="right"><? echo number_format($grand_tot_qntyydsIssueRtn,2,'.',''); ?></p></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Roll Qty</td>
                                <td style="font-size: 13px;" align="right"><p><? echo number_format($grand_tot_roll_countIssueRtn,2,'.',''); ?></p></td>
                            </tr>
                            <!--  -->
                            <tr>
                                <td rowspan="4" align="center" style="font-size: 13px; vertical-align: middle;">TRANSFER IN</td>
                                <td style="font-size: 13px;">Receive Qty KG</td>
                                <td style="font-size: 13px;" align="right"><p><? echo number_format($grand_tot_qntykgIn,2,'.',''); ?></p></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Receive Qty PCS</td>
                                <td style="font-size: 13px;" align="right"><p><? echo number_format($grand_tot_qntypcsIn,2,'.',''); ?></p></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Receive Qty YDS</td>
                                <td style="font-size: 13px;" align="right"><p><? echo number_format($grand_tot_qntyydsIn,2,'.',''); ?></p></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Roll Qty</td>
                                <td style="font-size: 13px;" align="right"><p><? echo number_format($grand_tot_roll_countIn,2,'.',''); ?></p></td>
                            </tr>
                            <!--  -->
                            <tr>
                                <td rowspan="4" align="center" style="font-size: 13px; vertical-align: middle;">TOTAL</td>
                                <td style="font-size: 13px;">Receive Qty KG</td>
                                <td style="font-size: 13px;" align="right"><p><? echo number_format($grand_tot_total_recv_kg,2,'.',''); ?></p></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Receive Qty PCS</td>
                                <td style="font-size: 13px;" align="right"><p><? echo number_format($grand_tot_total_recv_pcs,2,'.',''); ?></p></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Receive Qty YDS</td>
                                <td style="font-size: 13px;" align="right"><p><? echo number_format($grand_tot_total_recv_yds,2,'.',''); ?></p></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Roll Qty</td>
                                <td style="font-size: 13px;" align="right"><p><? echo number_format($grand_tot_total_recv_rollkg+$grand_tot_total_recv_rollpcs+$grand_tot_total_recv_rollyds,2,'.',''); ?></p></td>
                            </tr>
                        </table>
                    </td>
                    <!-- ISSUE SUMMERY -->
                    <td width="48%" style="float: right;">
                        <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                            <tr>
                                <td colspan="2" align="center" style="font-size: 13px;">ISSUE SUMMERY</td>
                                <td style="font-size: 13px;">Total Issue</td>
                            </tr>
                            <tr>
                                <td rowspan="4" align="center" style="font-size: 13px; vertical-align: middle;">ISSUE</td>
                                <td style="font-size: 13px;">Issue Qty KG</td>
                                <td style="font-size: 13px;" align="right"><p><? echo number_format($grand_tot_qntykgIssue,2,'.',''); ?></p></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Issue Qty PCS</td>
                                <td style="font-size: 13px;" align="right"><p><? echo number_format($grand_tot_qntypcsIssue,2,'.',''); ?></p></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Issue Qty YDS</td>
                                <td style="font-size: 13px;" align="right"><p><? echo number_format($grand_tot_qntyydsIssue,2,'.',''); ?></p></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Roll Qty</td>
                                <td style="font-size: 13px;" align="right"><p><? echo number_format($grand_tot_roll_countIssue,2,'.',''); ?></p></td>
                            </tr>
                            <!--  -->
                            <tr>
                                <td rowspan="4" align="center" style="font-size: 13px; vertical-align: middle;">RECEIVE RETURN</td>
                                <td style="font-size: 13px;">Issue Qty KG</td>
                                <td style="font-size: 13px;" align="right"></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Issue Qty PCS</td>
                                <td style="font-size: 13px;" align="right"></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Issue Qty YDS</td>
                                <td style="font-size: 13px;" align="right"></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Roll Qty</td>
                                <td style="font-size: 13px;" align="right"></td>
                            </tr>
                            <!--  -->
                            <tr>
                                <td rowspan="4" align="center" style="font-size: 13px; vertical-align: middle;">TRANSFER OUT</td>
                                <td style="font-size: 13px;">Issue Qty KG</td>
                                <td style="font-size: 13px;" align="right"><p><? echo number_format($grand_tot_qntykgTransOut,2,'.',''); ?></p></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Issue Qty PCS</td>
                                <td style="font-size: 13px;" align="right"><p><? echo number_format($grand_tot_qntypcsTransOut,2,'.',''); ?></p></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Issue Qty YDS</td>
                                <td style="font-size: 13px;" align="right"><p><? echo number_format($grand_tot_qntyydsTransOut,2,'.',''); ?></p></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Roll Qty</td>
                                <td style="font-size: 13px;" align="right"><p><? echo number_format($grand_tot_roll_countTransOut,2,'.',''); ?></p></td>
                            </tr>
                            <!--  -->
                            <tr>
                                <td rowspan="4" align="center" style="font-size: 13px; vertical-align: middle;">TOTAL</td>
                                <td style="font-size: 13px;">Issue Qty KG</td>
                                <td style="font-size: 13px;" align="right"><p><? echo number_format($grand_tot_total_issue_kg,2,'.',''); ?></p></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Issue Qty PCS</td>
                                <td style="font-size: 13px;" align="right"><p><? echo number_format($grand_tot_total_issue_pcs,2,'.',''); ?></p></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Issue Qty YDS</td>
                                <td style="font-size: 13px;" align="right"><p><? echo number_format($grand_tot_total_issue_yds,2,'.',''); ?></p></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Roll Qty</td>
                                <td style="font-size: 13px;" align="right"><p><? echo number_format($grand_tot_total_issue_rollkg+$grand_tot_total_issue_rollpcs+$grand_tot_total_issue_rollyds,2,'.',''); ?></p></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <!-- Recv issue summary end -->
            <br>
            <!-- color summary start -->
            <table width="10%">
                <tr>
                    <td width="50%" style="float: left;">
                        <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                            <tr>
                                <td colspan="2" align="center" style="font-size: 13px;">COLOR WISE RECEIVE SUMMERY</td>
                                <td style="font-size: 13px;">Total Receive</td>
                            </tr>
                            
                            <? 
                            // echo "<pre>";print_r($color_rev_summary_arr);echo "</pre><br>";
                            foreach ($color_rev_summary_arr as $color_key => $row) 
                            {
                                $color_arr = explode(",", $color_key);
                                $colorName="";
                                foreach ($color_arr as $color) {
                                    $colorName .= $colorArr[$color] . ",";
                                }
                                $colorName = rtrim($colorName, ", ");
                                ?>
                                <tr>
                                    <td rowspan="4" align="center" style="font-size: 13px; vertical-align: middle; width: 150px;"><p><? echo $colorName; ?></p></td>
                                    <td style="font-size: 13px; vertical-align: middle;">Receive Qty KG</td>
                                    <td align="right" style="font-size: 13px;"><? echo number_format($row['colorRcvKg'],2,'.',''); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-size: 13px; vertical-align: middle;">Receive Qty PCS</td>
                                    <td align="right" style="font-size: 13px;"><? echo number_format($row['colorRcvPcs'],2,'.',''); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-size: 13px; vertical-align: middle;">Receive Qty YDS</td>
                                    <td align="right" style="font-size: 13px;"><? echo number_format($row['colorRcvYds'],2,'.',''); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-size: 13px; vertical-align: middle;">Roll Qty</td>
                                    <td align="right" style="font-size: 13px;"><? echo number_format($row['ColorRollCount'],2,'.',''); ?></td>
                                </tr>
                                <?
                                $totalColorRcvKg+=$row['colorRcvKg'];
                                $totalColorRcvPcs+=$row['colorRcvPcs'];
                                $totalColorRcvYds+=$row['colorRcvYds'];
                                $totalRollCount+=$row['ColorRollCount'];
                            } 
                            ?>                            
                            <!--  -->
                            <tr>
                                <td rowspan="4" align="center" style="font-size: 13px; vertical-align: middle;">TOTAL</td>
                                <td style="font-size: 13px;">Issue Qty KG</td>
                                <td align="right" style="font-size: 13px;"><? echo number_format($totalColorRcvKg,2,'.',''); ?></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Issue Qty PCS</td>
                                <td align="right" style="font-size: 13px;"><? echo number_format($totalColorRcvPcs,2,'.',''); ?></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Issue Qty YDS</td>
                                <td align="right" style="font-size: 13px;"><? echo number_format($totalColorRcvYds,2,'.',''); ?></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Roll Qty</td>
                                <td align="right" style="font-size: 13px;"><? echo number_format($totalRollCount,2,'.',''); ?></td>
                            </tr>
                        </table>
                    </td>
                    <td width="48%" style="float: right;">
                        <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                            <tr>
                                <td colspan="2" align="center" style="font-size: 13px;">COLOR WISE ISSUE SUMMERY</td>
                                <td style="font-size: 13px;">Total Issue</td>
                            </tr>
                            
                            <? 
                            // echo "<pre>";print_r($color_issue_summary_arr);echo "</pre>";
                            foreach ($color_issue_summary_arr as $color_key => $row) 
                            {
                                $color_arr = explode(",", $color_key);
                                $colorName="";
                                foreach ($color_arr as $color) {
                                    $colorName .= $colorArr[$color] . ",";
                                }
                                $colorName = rtrim($colorName, ", ");
                                ?>
                                <tr>
                                    <td rowspan="4" align="center" style="font-size: 13px; vertical-align: middle; width: 150px;"><p><? echo $colorName; ?></p></td>
                                    <td style="font-size: 13px; vertical-align: middle;">Issue Qty KG</td>
                                    <td align="right" style="font-size: 13px;"><? echo number_format($row['colorIssKg'],2,'.',''); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-size: 13px; vertical-align: middle;">Issue Qty PCS</td>
                                    <td align="right" style="font-size: 13px;"><? echo number_format($row['colorIssPcs'],2,'.',''); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-size: 13px; vertical-align: middle;">Issue Qty YDS</td>
                                    <td align="right" style="font-size: 13px;"><? echo number_format($row['colorIssYds'],2,'.',''); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-size: 13px; vertical-align: middle;">Roll Qty</td>
                                    <td align="right" style="font-size: 13px;"><? echo number_format($row['ColorRollCount'],2,'.',''); ?></td>
                                </tr>
                                <?
                                $isstotalColorRcvKg+=$row['colorIssKg'];
                                $isstotalColorRcvPcs+=$row['colorIssPcs'];
                                $isstotalColorRcvYds+=$row['colorIssYds'];
                                $isstotalRollCount+=$row['ColorRollCount'];
                            } 
                            ?>                            
                            <!--  -->
                            <tr>
                                <td rowspan="4" align="center" style="font-size: 13px; vertical-align: middle;">TOTAL</td>
                                <td style="font-size: 13px;">Issue Qty KG</td>
                                <td align="right" style="font-size: 13px;"><? echo number_format($isstotalColorRcvKg,2,'.',''); ?></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Issue Qty PCS</td>
                                <td align="right" style="font-size: 13px;"><? echo number_format($isstotalColorRcvPcs,2,'.',''); ?></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Issue Qty YDS</td>
                                <td align="right" style="font-size: 13px;"><? echo number_format($isstotalColorRcvYds,2,'.',''); ?></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Roll Qty</td>
                                <td align="right" style="font-size: 13px;"><? echo number_format($isstotalRollCount,2,'.',''); ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <!-- color summary end -->
            <br>
            <!-- Supplier wise summary start -->
            <table width="10%">
                <tr>
                    <td width="50%" style="float: left;">
                        <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                            <tr>
                                <td colspan="2" align="center" style="font-size: 13px;">SUPPLIER WISE RECEIVE SUMMERY</td>
                                <td style="font-size: 13px;">Total Receive</td>
                            </tr>
                            
                            <? 
                            // echo "<pre>";print_r($supplier_wise_recv_summary_arr);echo "</pre>";
                            foreach ($supplier_wise_recv_summary_arr as $knit_source => $knit_sourceVal) 
                            {
                                foreach ($knit_sourceVal as $supplier_key => $row) 
                                {
                                    ?>
                                    <tr>
                                        <td rowspan="4" align="center" style="font-size: 13px; vertical-align: middle; width: 150px;"><p><?
                                        if ($knit_source==1) 
                                        {
                                            echo $company_arr[$supplier_key];
                                        }
                                        else
                                        {
                                            echo $supplier_arr[$supplier_key];
                                        }
                                         ?></p></td>
                                        <td style="font-size: 13px; vertical-align: middle;">Receive Qty KG</td>
                                        <td align="right" style="font-size: 13px;"><? echo number_format($row['RcvKg'],2,'.',''); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 13px; vertical-align: middle;">Receive Qty PCS</td>
                                        <td align="right" style="font-size: 13px;"><? echo number_format($row['RcvPcs'],2,'.',''); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 13px; vertical-align: middle;">Receive Qty YDS</td>
                                        <td align="right" style="font-size: 13px;"><? echo number_format($row['RcvYds'],2,'.',''); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 13px; vertical-align: middle;">Roll Qty</td>
                                        <td align="right" style="font-size: 13px;"><? echo number_format($row['RollCount'],2,'.',''); ?></td>
                                    </tr>
                                    <?
                                    $totalSupplierRcvKg+=$row['RcvKg'];
                                    $totalSupplierRcvPcs+=$row['RcvPcs'];
                                    $totalSupplierRcvYds+=$row['RcvYds'];
                                    $totalSupplierRollCount+=$row['RollCount'];
                                }
                            } 
                            ?>                            
                            <!--  -->
                            <tr>
                                <td rowspan="4" align="center" style="font-size: 13px; vertical-align: middle;">TOTAL</td>
                                <td style="font-size: 13px;">Issue Qty KG</td>
                                <td align="right" style="font-size: 13px;"><? echo number_format($totalSupplierRcvKg,2,'.',''); ?></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Issue Qty PCS</td>
                                <td align="right" style="font-size: 13px;"><? echo number_format($totalSupplierRcvPcs,2,'.',''); ?></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Issue Qty YDS</td>
                                <td align="right" style="font-size: 13px;"><? echo number_format($totalSupplierRcvYds,2,'.',''); ?></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Roll Qty</td>
                                <td align="right" style="font-size: 13px;"><? echo number_format($totalSupplierRollCount,2,'.',''); ?></td>
                            </tr>
                        </table>
                    </td>
                    <td width="48%" style="float: right;">
                        <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                            <tr>
                                <td colspan="2" align="center" style="font-size: 13px;">SUPPLIER WISE ISSUE SUMMERY</td>
                                <td style="font-size: 13px;">Total Issue</td>
                            </tr>
                            <? 
                            sort($supplier_wise_issue_summary_arr);
                            // echo "<pre>";print_r($supplier_wise_issue_summary_arr);echo "</pre>";
                            foreach ($supplier_wise_issue_summary_arr as $knit_source => $knit_sourceVal) 
                            {
                                foreach ($knit_sourceVal as $supplier_key => $row) 
                                {
                                    ?>
                                    <tr>
                                        <td rowspan="4" align="center" style="font-size: 13px; vertical-align: middle; width: 150px;"><p><?
                                        if ($knit_source==0) 
                                        {
                                            echo $company_arr[$supplier_key];
                                        }
                                        else
                                        {
                                            echo $supplier_arr[$supplier_key];
                                        } ?></p></td>
                                        <td style="font-size: 13px; vertical-align: middle;">Issue Qty KG</td>
                                        <td align="right" style="font-size: 13px;"><? echo number_format($row['IssKg'],2,'.',''); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 13px; vertical-align: middle;">Issue Qty PCS</td>
                                        <td align="right" style="font-size: 13px;"><? echo number_format($row['IssPcs'],2,'.',''); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 13px; vertical-align: middle;">Issue Qty YDS</td>
                                        <td align="right" style="font-size: 13px;"><? echo number_format($row['IssYds'],2,'.',''); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 13px; vertical-align: middle;">Roll Qty</td>
                                        <td align="right" style="font-size: 13px;"><? echo number_format($row['RollCount'],2,'.',''); ?></td>
                                    </tr>
                                    <?
                                    $totalSupplierIssKg+=$row['IssKg'];
                                    $totalSupplierIssPcs+=$row['IssPcs'];
                                    $totalSupplierIssYds+=$row['IssYds'];
                                    $totalSupplierIssRollCount+=$row['RollCount'];
                                }
                            } 
                            ?>                            
                            <!--  -->
                            <tr>
                                <td rowspan="4" align="center" style="font-size: 13px; vertical-align: middle;">TOTAL</td>
                                <td style="font-size: 13px;">Issue Qty KG</td>
                                <td align="right" style="font-size: 13px;"><? echo number_format($totalSupplierIssKg,2,'.',''); ?></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Issue Qty PCS</td>
                                <td align="right" style="font-size: 13px;"><? echo number_format($totalSupplierIssPcs,2,'.',''); ?></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Issue Qty YDS</td>
                                <td align="right" style="font-size: 13px;"><? echo number_format($totalSupplierIssYds,2,'.',''); ?></td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px;">Roll Qty</td>
                                <td align="right" style="font-size: 13px;"><? echo number_format($totalSupplierIssRollCount,2,'.',''); ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <!-- Supplier wise summary end -->
            <!-- Summary End -->
            </fieldset>
        </div>
        <?
        $html = ob_get_contents();
        ob_clean();
        foreach (glob("$user_name*.xls") as $filename) 
        {
            if( @filemtime($filename) < (time()-$seconds_old) )
            @unlink($filename);
        }
        $name=time();
        $filename=$user_name."_".$name.".xls";
        $create_new_doc = fopen($filename, 'w') or die('Cant open');
        $is_created = fwrite($create_new_doc, $html) or die('Cant open');
        echo "$html##$filename##$report_type";
        exit();
    }
}

?>