<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

extract($_REQUEST);

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ){
    header("location:login.php");
    die;
}
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($db_type==0)
{
	$select_year="year";
	$year_con="";
}
else
{
	$select_year="to_char";
	$year_con=",'YYYY'";
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
	die;
}

if($action=="style_reference_search")
{
	echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	    <script>
            var selected_id = new Array;
            var selected_name = new Array;
            var selected_no = new Array;
            function check_all_data() {
                var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
                tbl_row_count = tbl_row_count - 0;
                for( var i = 1; i <= tbl_row_count; i++ ) {
                    var onclickString = $('#tr_' + i).attr('onclick');
                    var paramArr = onclickString.split("'");
                    var functionParam = paramArr[1];
                    js_set_value( functionParam );

                }
            }

            function toggle( x, origColor ) {
                var newColor = 'yellow';
                if ( x.style ) {
                    x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
                }
            }

            function js_set_value( strCon )
            {
                    var splitSTR = strCon.split("_");
                    var str = splitSTR[0];
                    var selectID = splitSTR[1];
                    var selectDESC = splitSTR[2];

                    toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );

                    if( jQuery.inArray( selectID, selected_id ) == -1 ) {
                        selected_id.push( selectID );
                        selected_name.push( selectDESC );
                        selected_no.push( str );
                    }
                    else {
                        for( var i = 0; i < selected_id.length; i++ ) {
                            if( selected_id[i] == selectID ) break;
                        }
                        selected_id.splice( i, 1 );
                        selected_name.splice( i, 1 );
                        selected_no.splice( i, 1 );
                    }
                    var id = ''; var name = ''; var job = ''; var num='';
                    for( var i = 0; i < selected_id.length; i++ ) {
                        id += selected_id[i] + ',';
                        name += selected_name[i] + ',';
                        num += selected_no[i] + ',';
                    }
                    id 		= id.substr( 0, id.length - 1 );
                    name 	= name.substr( 0, name.length - 1 );
                    num 	= num.substr( 0, num.length - 1 );
                    //alert(num);
                    $('#txt_selected_id').val( id );
                    $('#txt_selected').val( name );
                    $('#txt_selected_no').val( num );
            }

            function fn_selected()
            {
                var style_no='<? echo $txt_style_ref_no;?>';
                var style_id='<? echo $txt_style_ref_id;?>';
                var style_des='<? echo $txt_style_ref;?>';

                if(style_no!="")
                {
                    style_no_arr=style_no.split(",");
                    style_id_arr=style_id.split(",");
                    style_des_arr=style_des.split(",");
                    var str_ref="";
                    for(var k=0;k<style_no_arr.length; k++)
                    {
                        str_ref=style_no_arr[k]+'_'+style_id_arr[k]+'_'+style_des_arr[k];
                        js_set_value(str_ref);
                    }
                }
            }
        </script>
    </head>
    <body>
        <div align="center">
            <form name="styleRef_form" id="styleRef_form">
                <fieldset>
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                        <thead>
                            <tr>
                                <th>Style Ref No</th>
                                <th>Job No</th>
                                <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td align="center">
                                    <input type="text" style="width:130px" class="text_boxes" name="txt_style_ref_no" id="txt_style_ref_no" />
                                </td>
                                <td align="center">
                                     <input type="text" style="width:130px" class="text_boxes" name="txt_job_no" id="txt_job_no" />
                                </td>
                                <td align="center">
                                    <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<?=$company;?>'+'**'+'<?=$buyer;?>'+'**'+document.getElementById('txt_style_ref_no').value+'**'+document.getElementById('txt_job_no').value+'**'+'<?=$cbo_year;?>', 'style_reference_search_list_view', 'search_div', 'date_wise_finish_garments_recv_issue_report_controller', 'setFilterGrid(\'list_view\',-1)');fn_selected();" style="width:100px;" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
                <div style="margin-top:15px" id="search_div"></div>
            </form>
        </div>
    </body>
        <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="style_reference_search_list_view")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	list($company,$buyer,$style_ref_no,$job_no,$cbo_year)=explode('**',$data);

	if($style_ref_no!=""){$search_con=" and style_ref_no like('%$style_ref_no%')";}
	if($job_no!=""){$search_con .=" and job_no like('%$job_no')";}

	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$cbo_year=str_replace("'","",$cbo_year);
	if($buyer > 0)
        $buyer_cond=" and buyer_name=$buyer";
    else
        $buyer_cond="";

	if($cbo_year > 0){
        if($db_type==0)
            $year_cond=" and year(insert_date)='$cbo_year'";
        else
            $year_cond=" and to_char(insert_date,'YYYY')='$cbo_year'";
    }else{
        $year_cond="";
    }

	$sql = "select id,style_ref_no,job_no,job_no_prefix_num,$select_year(insert_date $year_con) as year from wo_po_details_master where company_name=$company $buyer_cond $year_cond  $search_con and is_deleted=0 order by job_no_prefix_num";

	echo create_list_view("list_view", "Style Ref No,Job No,Year","160,90,100","400","235",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","",1) ;
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";

	exit();
}

if($action=="job_search")
{
    echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    ?>
        <script>
            var selected_id = new Array;
            var selected_name = new Array;
            var selected_no = new Array;
            function check_all_data() {
                var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
                tbl_row_count = tbl_row_count - 0;
                for( var i = 1; i <= tbl_row_count; i++ ) {
                    var onclickString = $('#tr_' + i).attr('onclick');
                    var paramArr = onclickString.split("'");
                    var functionParam = paramArr[1];
                    js_set_value( functionParam );

                }
            }

            function toggle( x, origColor ) {
                var newColor = 'yellow';
                if ( x.style ) {
                    x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
                }
            }

            function js_set_value( strCon )
            {
                var splitSTR = strCon.split("_");
                var str = splitSTR[0];
                var selectID = splitSTR[1];
                var selectDESC = splitSTR[2];

                toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );

                if( jQuery.inArray( selectID, selected_id ) == -1 ) {
                    selected_id.push( selectID );
                    selected_name.push( selectDESC );
                    selected_no.push( str );
                }
                else {
                    for( var i = 0; i < selected_id.length; i++ ) {
                        if( selected_id[i] == selectID ) break;
                    }
                    selected_id.splice( i, 1 );
                    selected_name.splice( i, 1 );
                    selected_no.splice( i, 1 );
                }
                var id = ''; var name = ''; var job = ''; var num='';
                for( var i = 0; i < selected_id.length; i++ ) {
                    id += selected_id[i] + ',';
                    name += selected_name[i] + ',';
                    num += selected_no[i] + ',';
                }
                id 		= id.substr( 0, id.length - 1 );
                name 	= name.substr( 0, name.length - 1 );
                num 	= num.substr( 0, num.length - 1 );
                //alert(num);
                $('#txt_selected_id').val( id );
                $('#txt_selected').val( name );
                $('#txt_selected_no').val( num );
            }

            function fn_selected()
            {
                var job_no='<? echo $txt_job_no;?>';
                var job_id='<? echo $txt_job_id;?>';
                var job_des='<? echo $txt_job;?>';

                if(job_no!="")
                {
                    job_no_arr=job_no.split(",");
                    job_id_arr=job_id.split(",");
                    job_des_arr=job_des.split(",");
                    var str_ref="";
                    for(var k=0;k<job_no_arr.length; k++)
                    {
                        str_ref=job_no_arr[k]+'_'+job_id_arr[k]+'_'+job_des_arr[k];
                        js_set_value(str_ref);
                    }
                }
            }
        </script>
    </head>
    <body>
        <div align="center">
            <form name="styleRef_form" id="styleRef_form">
                <fieldset>
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                        <thead>
                        <tr>
                            <th>Job No</th>
                            <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td align="center">
                                <input type="text" style="width:130px" class="text_boxes" name="txt_job_no" id="txt_job_no" />
                            </td>
                            <td align="center">
                                <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<?=$company;?>'+'**'+'<?=$buyer;?>'+'**'+document.getElementById('txt_job_no').value+'**'+'<?=$cbo_year;?>'+'**'+'<?=$txt_style_ref_id;?>', 'job_search_list_view', 'search_div', 'date_wise_finish_garments_recv_issue_report_controller', 'setFilterGrid(\'list_view\',-1)');fn_selected();" style="width:100px;" />
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </fieldset>
                <div style="margin-top:15px" id="search_div"></div>
            </form>
        </div>
    </body>
        <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="job_search_list_view")
{
    extract($_REQUEST);
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
    list($company,$buyer,$job_no,$cbo_year,$selected_style)=explode('**',$data);
    $search_con = '';
    if($job_no!="")
        $search_con .=" and job_no_prefix_num =$job_no";

    $buyer=str_replace("'","",$buyer);
    $company=str_replace("'","",$company);
    $cbo_year=str_replace("'","",$cbo_year);
    $selected_style=str_replace("'","",$selected_style);
    if($selected_style != "")
        $search_con .=" and id in ($selected_style)";

    if($buyer > 0)
        $buyer_cond=" and buyer_name=$buyer";
    else
        $buyer_cond="";

    if($cbo_year > 0){
        if($db_type==0)
            $year_cond=" and year(insert_date)='$cbo_year'";
        else
            $year_cond=" and to_char(insert_date,'YYYY')='$cbo_year'";
    }else{
        $year_cond="";
    }

    $sql = "select id,style_ref_no,job_no,job_no_prefix_num,$select_year(insert_date $year_con) as year from wo_po_details_master where company_name=$company $buyer_cond $year_cond  $search_con and is_deleted=0 order by job_no_prefix_num desc";

    echo create_list_view("list_view", "Job No,Style Ref No,Year","110,160,90","400","235",0, $sql , "js_set_value", "id,job_no", "", 1, "0", $arr, "job_no,style_ref_no,year", "","setFilterGrid('list_view',-1)","0","",1) ;
    echo "<input type='hidden' id='txt_selected_id' />";
    echo "<input type='hidden' id='txt_selected' />";
    echo "<input type='hidden' id='txt_selected_no' />";

    exit();
}

if($action=="order_search")
{
	echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
        <script>
            var selected_id = new Array;
            var selected_name = new Array;
            var selected_no = new Array;
            function check_all_data() {
                var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
                tbl_row_count = tbl_row_count - 0;
                for( var i = 1; i <= tbl_row_count; i++ ) {
                    var onclickString = $('#tr_' + i).attr('onclick');
                    var paramArr = onclickString.split("'");
                    var functionParam = paramArr[1];
                    js_set_value( functionParam );

                }
            }
            function toggle( x, origColor ) {
                var newColor = 'yellow';
                if ( x.style ) {
                    x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
                }
            }
            function js_set_value( strCon )
            {
                //alert(strCon);
                    var splitSTR = strCon.split("_");
                    var str_or = splitSTR[0];
                    var selectID = splitSTR[1];
                    var selectDESC = splitSTR[2];
                    //$('#txt_individual_id' + str).val(splitSTR[1]);
                    //$('#txt_individual' + str).val('"'+splitSTR[2]+'"');

                    toggle( document.getElementById( 'tr_' + str_or ), '#FFFFCC' );

                    if( jQuery.inArray( selectID, selected_id ) == -1 ) {
                        selected_id.push( selectID );
                        selected_name.push( selectDESC );
                        selected_no.push( str_or );
                    }
                    else {
                        for( var i = 0; i < selected_id.length; i++ ) {
                            if( selected_id[i] == selectID ) break;
                        }
                        selected_id.splice( i, 1 );
                        selected_name.splice( i, 1 );
                        selected_no.splice( i, 1 );
                    }
                    var id = ''; var name = ''; var job = ''; var num='';
                    for( var i = 0; i < selected_id.length; i++ ) {
                        id += selected_id[i] + ',';
                        name += selected_name[i] + ',';
                        num += selected_no[i] + ',';
                    }
                    id 		= id.substr( 0, id.length - 1 );
                    name 	= name.substr( 0, name.length - 1 );
                    num 	= num.substr( 0, num.length - 1 );
                    //alert(num);
                    $('#txt_selected_id').val( id );
                    $('#txt_selected').val( name );
                    $('#txt_selected_no').val( num );
            }

            function fn_selected()
            {
                var order_no='<? echo $txt_order_id_no; ?>';
                var order_id='<? echo $txt_order_id;?>';
                var order_des='<? echo $txt_order;?>';
                //alert(style_id);
                if(order_no!="")
                {
                    order_no_arr=order_no.split(",");
                    order_id_arr=order_id.split(",");
                    order_des_arr=order_des.split(",");
                    var order_ref="";
                    for(var k=0;k<order_no_arr.length; k++)
                    {
                        order_ref=order_no_arr[k]+'_'+order_id_arr[k]+'_'+order_des_arr[k];
                        js_set_value(order_ref);
                    }
                }
            }


        </script>
    </head>
    <body>
        <div align="center">
            <form name="styleRef_form" id="styleRef_form">
                <fieldset>
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                        <thead>
                            <th>Order No</th>
                            <th>Job No</th>
                            <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                        </thead>
                        <tbody>
                            <tr>
                                <td align="center">
                                    <input type="text" style="width:130px" class="text_boxes" name="txt_order_no" id="txt_order_no" />
                                </td>
                                <td align="center">
                                     <input type="text" style="width:130px" class="text_boxes" name="txt_job_no" id="txt_job_no" />
                                </td>
                                <td align="center">
                                    <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+'<? echo $buyer; ?>'+'**'+document.getElementById('txt_order_no').value+'**'+document.getElementById('txt_job_no').value+'**'+'<?=$txt_job_id?>'+'**'+'<?=$txt_style_ref_id?>'+'**'+'<?=$cbo_year?>', 'order_search_list_view', 'search_div', 'date_wise_finish_garments_recv_issue_report_controller', 'setFilterGrid(\'list_view\',-1)');fn_selected();" style="width:100px;" />
                            </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
                <div style="margin-top:15px" id="search_div"></div>
            </form>
        </div>
    </body>
        <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="order_search_list_view")
{
	extract($_REQUEST);

	list($company,$buyer,$order_no,$job_no,$selected_job_id,$selected_style_id,$cbo_year)=explode('**',$data);
    $search_con = "";
	if($order_no!="")
        $search_con .=" and a.po_number like('%$order_no%')";
	if($job_no!=""){
        $search_con .=" and b.job_no_prefix_num = $job_no";
    }else{
        if($selected_job_id != ''){
            $search_con .= " and b.id in ($selected_job_id)";
        }else{
            if($selected_style_id != "")
                $search_con .= " and b.id in ($selected_style_id)";
        }
    }

	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
    $selected_style_id=str_replace("'","",$selected_style_id);
    $selected_job_id=str_replace("'","",$selected_job_id);


    $cbo_year=str_replace("'","",$cbo_year);

    $year_cond="";
    if($cbo_year > 0){
        if($db_type==0)
            $year_cond=" and year(b.insert_date)='$cbo_year'";
        else
            $year_cond=" and to_char(b.insert_date,'YYYY')='$cbo_year'";
    }

	if($buyer!=0) $buyer_cond="and b.buyer_name=$buyer"; else $buyer_cond="";

	$sql = "select a.id,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,$select_year(b.insert_date $year_con) as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and b.company_name=$company $buyer_cond  $search_con $year_cond and a.status_active=1 order by b.id desc";

	echo create_list_view("list_view", "Order NO,Job No,Year,Style Ref No","150,80,70,150","500","230",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_no_prefix_num,year,style_ref_no", "","setFilterGrid('list_view',-1)","0","",1);

    echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";

 	exit();
}

//report generated here--------------------//
if($action=="generate_report")
{
	$started = microtime(true);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_style_ref_id=str_replace("'","",$txt_style_ref_id);
    $txt_job=str_replace("'","",$txt_job);
	$txt_job_id=str_replace("'","",$txt_job_id);
	$txt_order=str_replace("'","",$txt_order);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$rptType=str_replace("'","",$rptType);
	$fso_id=str_replace("'","",$fso_id);
	$cbo_based_on=str_replace("'","",$cbo_based_on);
	$cbo_search_id=str_replace("'","",$cbo_search_id);
	$txt_search_val=str_replace("'","",$txt_search_val);
	$cbo_year=str_replace("'","",$cbo_year);


    $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $party_arr=return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=22 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id","supplier_name");
    $buyer_short_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
    $user_name_arr=return_library_array( "select id, user_name from  user_passwd",'id','user_name');

    $sql_cond="";
    if($cbo_buyer_name > 0)
        $sql_cond .= " and e.buyer_name = $cbo_buyer_name ";

    if($txt_style_ref_id != ""){
        $sql_cond .= " and e.id in ($txt_style_ref_id) ";
    }else{
        if($txt_job_id != "")
            $sql_cond .= " and e.id in ($txt_job_id) ";
    }

    if($txt_order_id != "")
        $sql_cond .= " and d.id in ($txt_order_id) ";


    /* if($cbo_year != 0)
        $sql_cond .=" and to_char(e.insert_date,'YYYY')='$cbo_year'"; */

    if($cbo_search_id == 1 && $txt_search_val != "")
        $sql_cond .= " and d.file_no = '$txt_search_val' ";

    if($cbo_search_id == 2 && $txt_search_val != "")
        $sql_cond .= " and d.grouping = '$txt_search_val' ";



    if($cbo_based_on==1)
    {
        if( $txt_date_from!="" && $txt_date_to!="" )
            $sql_cond .=" and b.delivery_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' ";
    }
    else
    {
        if($txt_date_from!="" && $txt_date_to!="")
            $sql_cond .=" and b.insert_date between '".date("j-M-Y",strtotime($txt_date_from))."  01:00:01 AM' and '".date("j-M-Y",strtotime($txt_date_to))."  11:59:59 PM' ";

    }
    $select_insert_date=" to_char(b.insert_date,'DD-MM-YYYY HH24:MI:SS')  as insert_date";//HH24:MI:SS,32,34,35,36,37,38,39
    $select_insert_time=" to_char(b.insert_date,'HH24:MI:SS') as insert_time";

    if($rptType == 1){
        $sql_cond .= " and a.production_type in (81,82,83,84) ";
    }elseif($rptType == 2){
        $sql_cond .= " and a.production_type in (81,84) ";
    }elseif($rptType == 3){
        $sql_cond .= " and a.production_type in (82,83) ";
    }

    $slq_main_data = sql_select("SELECT e.buyer_name, e.style_ref_no, to_char(e.insert_date, 'yyyy') as job_year, e.job_no, $select_insert_date, $select_insert_time,
       d.po_number, b.sys_number, to_char(b.delivery_date, 'dd-mm-yyy') as trans_date, to_char(d.shipment_date, 'dd-mm-yyyy') as ship_date,
       sum(a.production_qnty) as qty, c.color_number_id, b.inserted_by, b.challan_no, f.serving_company, a.production_type, b.production_source
       from pro_garments_production_dtls a, pro_gmts_delivery_mst b, wo_po_color_size_breakdown c, wo_po_break_down d, wo_po_details_master e, pro_garments_production_mst f
       where a.delivery_mst_id = b.id and a.mst_id = f.id and a.color_size_break_down_id = c.id and c.po_break_down_id = d.id and d.job_no_mst = e.job_no
         and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and b.company_id = $cbo_company_name  $sql_cond
       group by e.buyer_name, e.style_ref_no, e.insert_date, e.job_no, d.po_number, b.sys_number, b.delivery_date, d.shipment_date, c.color_number_id, b.inserted_by, b.challan_no, f.serving_company, b.insert_date, a.production_type, b.production_source order by b.inserted_by desc");

    if(count($slq_main_data) == 0){
        echo "<h3>Transaction Not Found!</h3>";
        die;
    }
    $mainArr = []; $returnParty=[];
    foreach ($slq_main_data as $data){
        $key = $data[csf('style_ref_no')]."**".$data[csf('buyer_name')]."**".$data[csf('job_no')]."**".$data[csf('po_number')]."**".$data[csf('sys_number')]."**".$data[csf('color_number_id')];
        $mainArr[$key]['buyer'] = $buyer_short_arr[$data[csf('buyer_name')]];
        $mainArr[$key]['job'] = $data[csf('job_no')];
        $mainArr[$key]['job_year'] = $data[csf('job_year')];
        $mainArr[$key]['order_number'] = $data[csf('po_number')];
        $mainArr[$key]['style'] = $data[csf('style_ref_no')];
        $mainArr[$key]['trans_ref'] = $data[csf('sys_number')];
        $mainArr[$key]['trans_date'] = $data[csf('trans_date')];
        $mainArr[$key]['ship_date'] = $data[csf('ship_date')];
        $mainArr[$key]['production_type'] = $data[csf('production_type')];
        $mainArr[$key]['color'] = $color_arr[$data[csf('color_number_id')]];
        $mainArr[$key]['user'] = $user_name_arr[$data[csf('inserted_by')]];
        $mainArr[$key]['date_time'] = $data[csf('insert_date')];
        if($data[csf('challan_no')] != "" && ($mainArr[$key]['production_type'] == 83 || $mainArr[$key]['production_type'] == 84)){
            $returnParty[$data[csf('challan_no')]] = $data[csf('challan_no')];
        }
        if($mainArr[$key]['production_type'] == 81){
            $mainArr[$key]['recv_qty'] += $data[csf('qty')];
            $mainArr[$key]['recv_rtn'] += 0;
            $mainArr[$key]['issue_qty'] += 0;
            $mainArr[$key]['issue_rtn'] += 0;
            $mainArr[$key]['delivery_challan'] = $data[csf('challan_no')];
            if($data[csf('production_source')] == 1) {
                $mainArr[$key]['party'] = $company_arr[$data[csf('serving_company')]];
            }else {
                $mainArr[$key]['party'] = $party_arr[$data[csf('serving_company')]];
            }
        }elseif($mainArr[$key]['production_type'] == 82){
            $mainArr[$key]['recv_qty'] += 0;
            $mainArr[$key]['recv_rtn'] += 0;
            $mainArr[$key]['issue_qty'] += $data[csf('qty')];
            $mainArr[$key]['issue_rtn'] += 0;
            $mainArr[$key]['party'] = $company_arr[$cbo_company_name];
            $returnParty[$mainArr[$key]['trans_ref']] = $company_arr[$cbo_company_name];
        }elseif($mainArr[$key]['production_type'] == 83){
            $mainArr[$key]['recv_qty'] += 0;
            $mainArr[$key]['recv_rtn'] += 0;
            $mainArr[$key]['issue_qty'] += 0;
            $mainArr[$key]['issue_rtn'] += $data[csf('qty')];
            $mainArr[$key]['party'] = $data[csf('challan_no')];
        }elseif($mainArr[$key]['production_type'] == 84){
            $mainArr[$key]['recv_qty'] += 0;
            $mainArr[$key]['recv_rtn'] += $data[csf('qty')];
            $mainArr[$key]['issue_qty'] += 0;
            $mainArr[$key]['issue_rtn'] += 0;
            $mainArr[$key]['party'] = $data[csf('challan_no')];
        }
    }
    $party_arr_return = [];
    if(count($returnParty) > 0){
        $returnParty = array_chunk($returnParty, 900);
        $return_sql_cond = "";
        foreach ($returnParty as $key => $val){
            if($key == 0){
                $return_sql_cond .= " a.sys_number in ('".implode("','", $val)."')";
            }else{
                $return_sql_cond .= " or a.sys_number in ('".implode("','", $val)."')";
            }
        }
        $sql_get_return_party = sql_select("select a.sys_number, b.serving_company, b.company_id, b.production_source, b.production_type from pro_gmts_delivery_mst a, pro_garments_production_mst b where a.id = b.delivery_mst_id and ($return_sql_cond)");
        foreach ($sql_get_return_party as $v){
            if($v[csf('production_type')] == 81) {
                if ($v[csf('production_source')] == 1) {
                    $party_arr_return[$v[csf('sys_number')]] = $company_arr[$v[csf('serving_company')]];
                } else {
                    $party_arr_return[$v[csf('sys_number')]] = $party_arr[$v[csf('serving_company')]];
                }
            }elseif($v[csf('production_type')] == 82){
                $party_arr_return[$v[csf('sys_number')]] = $company_arr[$v[csf('company_id')]];
            }
        }
    }
    ob_start();
    if($rptType == 1){
    ?>
        <div style="width: 1750px; margin-bottom: 20px;">
            <table style="width:1720px; margin-left: 5px; text-align: center;">
                <tr>
                    <td class="form_caption"><h3 style="font-size: 16px;">Date Wise Finish Garments Receive and Issue Report</h3></td>
                </tr>
                <tr>
                    <td style="font-size: 15px;"><strong>Company Name: <?=$company_arr[$cbo_company_name]?></strong></td>
                </tr>
            </table>
            <table width="1720" style="margin-left: 5px;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" align="left">
                <thead>
                    <tr>
                        <th width="30">SL No.</th>
                        <th width="120">Buyer</th>
                        <th width="70">Job Year</th>
                        <th width="80">Job No.</th>
                        <th width="110">Order No.</th>
                        <th width="130">Style No.</th>
                        <th width="110">Trans Ref.</th>
                        <th width="80">Trans Date</th>
                        <th width="120">Delivery Challan No</th>
                        <th width="120">Party Name</th>
                        <th width="80">Ship Date</th>
                        <th width="110">Color</th>
                        <th width="90">Receive Qty (Pcs)</th>
                        <th width="90">Receive Return Qty (Pcs)</th>
                        <th width="90">Issue Qty (Pcs)</th>
                        <th width="90">Issue Return Qty (Pcs)</th>
                        <th width="90">User</th>
                        <th>Insert Time</th>
                    </tr>
                </thead>
            </table>
            <div style="width:1750px; max-height:300px; overflow-y:scroll; margin-left: 5px;" id="scroll_body">
                <table width="1720" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" align="left" id="table_body">
                    <tbody>
                    <?
                    $i = 1; $recv_total = 0; $recv_rtn_total = 0; $issue_total = 0; $issue_rtn_total = 0;
                    foreach ($mainArr as $val){
                        if($val['recv_qty'] > 0 || $val['recv_rtn'] > 0 || $val['issue_qty'] > 0 || $val['issue_rtn'] > 0){
                            if($i%2==0)
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";

                    ?>
                            <tr bgcolor="<?=$bgcolor?>">
                                <td style="font-size: 9.5pt;" width="30" align="center" valign="middle"><?=$i?></td>
                                <td style="font-size: 9.5pt;" valign="middle" width="120"  align="center"><?=$val['buyer']?></td>
                                <td style="font-size: 9.5pt;" valign="middle" width="70" align="center"><?=$val['job_year']?></td>
                                <td style="font-size: 9.5pt;" valign="middle" width="80" align="center"><?=$val['job']?></td>
                                <td style="font-size: 9.5pt;"  align="center" valign="middle" width="110"><?=$val['order_number']?></td>
                                <td style="font-size: 9.5pt;"  align="center" valign="middle" width="130"><?=$val['style']?></td>
                                <td style="font-size: 9.5pt;"  align="center" valign="middle" width="110"><?=$val['trans_ref']?></td>
                                <td style="font-size: 9.5pt;"  align="center" valign="middle" width="80"><?=$val['trans_date']?></td>
                                <td style="font-size: 9.5pt;"  align="center" valign="middle" width="120"> 
                                    <? if($val['production_type'] == 81){echo $val['delivery_challan'];} ?>  
                                </td>
                                <td style="font-size: 9.5pt;"  align="center" valign="middle" width="120">
                                    <?
                                    if($val['production_type'] == 81 || $val['production_type'] == 82)
                                        echo $val['party'];
                                    else
                                        echo $party_arr_return[$val['party']];
                                    ?>
                                </td>
                                <td style="font-size: 9.5pt;"  align="center" valign="middle" width="80"><?=$val['ship_date']?></td>
                                <td style="font-size: 9.5pt;"  align="center" valign="middle" width="110"><?=$val['color']?></td>
                                <td style="font-size: 9.5pt;"  align="right" valign="middle" width="90"><?=$val['recv_qty']?></td>
                                <td style="font-size: 9.5pt;"  align="right" valign="middle" width="90"><?=$val['recv_rtn']?></td>
                                <td style="font-size: 9.5pt;"  align="right" valign="middle" width="90"><?=$val['issue_qty']?></td>
                                <td style="font-size: 9.5pt;"  align="right" valign="middle" width="90"><?=$val['issue_rtn']?></td>
                                <td style="font-size: 9.5pt;"  align="center" valign="middle" width="90"><?=$val['user']?></td>
                                <td style="font-size: 9.5pt;"  valign="middle"><?=$val['date_time']?></td>
                            </tr>
                    <?
                            $recv_total += $val['recv_qty']; $recv_rtn_total += $val['recv_rtn']; $issue_total += $val['issue_qty']; $issue_rtn_total += $val['issue_rtn'];
                            $i++;
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <table width="1720" style="margin-left: 5px;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" align="left">
                <tfoot>
                <tr>
                    <th width="30"></th>
                    <th width="120"></th>
                    <th width="70"></th>
                    <th width="80"></th>
                    <th width="110"></th>
                    <th width="130"></th>
                    <th width="110"></th>
                    <th width="80"></th>
                    <th width="120"></th>
                    <th width="120"></th>
                    <th width="80"></th>
                    <th width="110"></th>
                    <th width="90" id="value_total_receive"><?=$recv_total?></th>
                    <th width="90" id="value_total_receive_rtn"><?=$recv_rtn_total?></th>
                    <th width="90" id="value_total_issue"><?=$issue_total?></th>
                    <th width="90" id="value_total_issue_rtn"><?=$issue_rtn_total?></th>
                    <th width="90"></th>
                    <th></th>
                </tr>
                </tfoot>
            </table>
        </div>
<?
    }
    elseif($rptType == 2){
    ?>
    <div style="width: 1570px; margin-bottom: 20px;">
        <table style="width:1540px; margin-left: 5px; text-align: center;">
            <tr>
                <td class="form_caption"><h3 style="font-size: 16px;">Date Wise Finish Garments Receive and Issue Report</h3></td>
            </tr>
            <tr>
                <td style="font-size: 15px;"><strong>Company Name: <?=$company_arr[$cbo_company_name]?></strong></td>
            </tr>
        </table>
        <table width="1540" style="margin-left: 5px;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" align="left">
            <thead>
            <tr>
                <th width="30">SL No.</th>
                <th width="120">Buyer</th>
                <th width="70">Job Year</th>
                <th width="80">Job No.</th>
                <th width="110">Order No.</th>
                <th width="130">Style No.</th>
                <th width="110">Trans Ref.</th>
                <th width="80">Trans Date</th>
                <th width="120">Delivery Challan No</th>
                <th width="120">Party Name</th>
                <th width="80">Ship Date</th>
                <th width="110">Color</th>
                <th width="90">Receive Qty (Pcs)</th>
                <th width="90">Receive Return Qty (Pcs)</th>
                <th width="90">User</th>
                <th>Insert Time</th>
            </tr>
            </thead>
        </table>
        <div style="width:1570px; max-height:300px; overflow-y:scroll; margin-left: 5px;" id="scroll_body">
            <table width="1540" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" align="left" id="table_body">
                <tbody>
                <?
                $i = 1; $recv_total = 0; $recv_rtn_total = 0;
                foreach ($mainArr as $val){
                    if($val['recv_qty'] > 0 || $val['recv_rtn'] > 0){
                        if($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";

                        ?>
                        <tr bgcolor="<?=$bgcolor?>">
                            <td style="font-size: 9.5pt;" width="30" align="center" valign="middle"><?=$i?></td>
                            <td style="font-size: 9.5pt;" valign="middle" width="120"  align="center"><?=$val['buyer']?></td>
                            <td style="font-size: 9.5pt;" valign="middle" width="70" align="center"><?=$val['job_year']?></td>
                            <td style="font-size: 9.5pt;" valign="middle" width="80" align="center"><?=$val['job']?></td>
                            <td style="font-size: 9.5pt;"  align="center" valign="middle" width="110"><?=$val['order_number']?></td>
                            <td style="font-size: 9.5pt;"  align="center" valign="middle" width="130"><?=$val['style']?></td>
                            <td style="font-size: 9.5pt;"  align="center" valign="middle" width="110"><?=$val['trans_ref']?></td>
                            <td style="font-size: 9.5pt;"  align="center" valign="middle" width="80"><?=$val['trans_date']?></td>
                            <td style="font-size: 9.5pt;"  align="center" valign="middle" width="120"> 
                                <? if($val['production_type'] == 81){echo $val['delivery_challan'];} ?>  
                            </td>
                            <td style="font-size: 9.5pt;"  align="center" valign="middle" width="120">
                                <?
                                if($val['production_type'] == 81 || $val['production_type'] == 82)
                                    echo $val['party'];
                                else
                                    echo $party_arr_return[$val['party']];
                                ?>
                            </td>
                            <td style="font-size: 9.5pt;"  align="center" valign="middle" width="80"><?=$val['ship_date']?></td>
                            <td style="font-size: 9.5pt;"  align="center" valign="middle" width="110"><?=$val['color']?></td>
                            <td style="font-size: 9.5pt;"  align="right" valign="middle" width="90"><?=$val['recv_qty']?></td>
                            <td style="font-size: 9.5pt;"  align="right" valign="middle" width="90"><?=$val['recv_rtn']?></td>
                            <td style="font-size: 9.5pt;"  align="center" valign="middle" width="90"><?=$val['user']?></td>
                            <td style="font-size: 9.5pt;"  valign="middle"><?=$val['date_time']?></td>
                        </tr>
                        <?
                        $recv_total += $val['recv_qty']; $recv_rtn_total += $val['recv_rtn'];
                        $i++;
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
        <table width="1540" style="margin-left: 5px;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" align="left">
            <tfoot>
            <tr>
                <th width="30"></th>
                <th width="120"></th>
                <th width="70"></th>
                <th width="80"></th>
                <th width="110"></th>
                <th width="130"></th>
                <th width="110"></th>
                <th width="80"></th>
                <th width="120"></th>
                <th width="120"></th>
                <th width="80"></th>
                <th width="110"></th>
                <th width="90" id="value_total_receive"><?=$recv_total?></th>
                <th width="90" id="value_total_receive_rtn"><?=$recv_rtn_total?></th>
                <th width="90"></th>
                <th></th>
            </tr>
            </tfoot>
        </table>
    </div>
    <?
    }
    elseif($rptType == 3){
        ?>
        <div style="width: 1450px; margin-bottom: 20px;">
            <table style="width:1420px; margin-left: 5px; text-align: center;">
                <tr>
                    <td class="form_caption"><h3 style="font-size: 16px;">Date Wise Finish Garments Receive and Issue Report</h3></td>
                </tr>
                <tr>
                    <td style="font-size: 15px;"><strong>Company Name: <?=$company_arr[$cbo_company_name]?></strong></td>
                </tr>
            </table>
            <table width="1420" style="margin-left: 5px;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" align="left">
                <thead>
                <tr>
                    <th width="30">SL No.</th>
                    <th width="120">Buyer</th>
                    <th width="70">Job Year</th>
                    <th width="80">Job No.</th>
                    <th width="110">Order No.</th>
                    <th width="130">Style No.</th>
                    <th width="110">Trans Ref.</th>
                    <th width="80">Trans Date</th>
                    <th width="120">Party Name</th>
                    <th width="80">Ship Date</th>
                    <th width="110">Color</th>
                    <th width="90">Issue Qty (Pcs)</th>
                    <th width="90">Issue Return Qty (Pcs)</th>
                    <th width="90">User</th>
                    <th>Insert Time</th>
                </tr>
                </thead>
            </table>
            <div style="width:1450px; max-height:300px; overflow-y:scroll; margin-left: 5px;" id="scroll_body">
                <table width="1420" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" align="left" id="table_body">
                    <tbody>
                    <?
                    $i = 1; $issue_total = 0; $issue_rtn_total = 0;
                    foreach ($mainArr as $val){
                        if($val['issue_qty'] > 0 || $val['issue_rtn'] > 0){
                            if($i%2==0)
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";

                            ?>
                            <tr bgcolor="<?=$bgcolor?>">
                                <td style="font-size: 9.5pt;" width="30" align="center" valign="middle"><?=$i?></td>
                                <td style="font-size: 9.5pt;" valign="middle" width="120"  align="center"><?=$val['buyer']?></td>
                                <td style="font-size: 9.5pt;" valign="middle" width="70" align="center"><?=$val['job_year']?></td>
                                <td style="font-size: 9.5pt;" valign="middle" width="80" align="center"><?=$val['job']?></td>
                                <td style="font-size: 9.5pt;"  align="center" valign="middle" width="110"><?=$val['order_number']?></td>
                                <td style="font-size: 9.5pt;"  align="center" valign="middle" width="130"><?=$val['style']?></td>
                                <td style="font-size: 9.5pt;"  align="center" valign="middle" width="110"><?=$val['trans_ref']?></td>
                                <td style="font-size: 9.5pt;"  align="center" valign="middle" width="80"><?=$val['trans_date']?></td>
                                <td style="font-size: 9.5pt;"  align="center" valign="middle" width="120">
                                    <?
                                    if($val['production_type'] == 81 || $val['production_type'] == 82)
                                        echo $val['party'];
                                    else
                                        echo $party_arr_return[$val['party']];
                                    ?>
                                </td>
                                <td style="font-size: 9.5pt;"  align="center" valign="middle" width="80"><?=$val['ship_date']?></td>
                                <td style="font-size: 9.5pt;"  align="center" valign="middle" width="110"><?=$val['color']?></td>
                                <td style="font-size: 9.5pt;"  align="right" valign="middle" width="90"><?=$val['issue_qty']?></td>
                                <td style="font-size: 9.5pt;"  align="right" valign="middle" width="90"><?=$val['issue_rtn']?></td>
                                <td style="font-size: 9.5pt;"  align="center" valign="middle" width="90"><?=$val['user']?></td>
                                <td style="font-size: 9.5pt;"  valign="middle"><?=$val['date_time']?></td>
                            </tr>
                            <?
                            $issue_total += $val['issue_qty']; $issue_rtn_total += $val['issue_rtn'];
                            $i++;
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <table width="1420" style="margin-left: 5px;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" align="left">
                <tfoot>
                <tr>
                    <th width="30"></th>
                    <th width="120"></th>
                    <th width="70"></th>
                    <th width="80"></th>
                    <th width="110"></th>
                    <th width="130"></th>
                    <th width="110"></th>
                    <th width="80"></th>
                    <th width="120"></th>
                    <th width="80"></th>
                    <th width="110"></th>
                    <th width="90" id="value_total_issue"><?=$issue_total?></th>
                    <th width="90" id="value_total_issue_rtn"><?=$issue_rtn_total?></th>
                    <th width="90"></th>
                    <th></th>
                </tr>
                </tfoot>
            </table>
        </div>
        <?
    }
    $html = ob_get_contents();
    ob_clean();
	foreach (glob($user_id."*.xls") as $filename) {
	if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	
	echo "$html**$filename**$rptType";
	disconnect($con);

	exit();
}

?>
