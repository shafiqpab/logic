<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

$search_by_arr=array(1=>"Date Wise Report",2=>"Wait For Heat Setting",5=>"Wait For Singeing",3=>"Wait For Dyeing",4=>"Wait For Re-Dyeing");

if ($action == "load_drop_down_knitting_com") 
{
	$data = explode("_", $data);
	$company_id = $data[1];

	if ($data[0] == 1) 
    {
		echo create_drop_down("cbo_knitting_company", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Knitting Company --", "$company_id", "load_drop_down( 'requires/grey_fabric_transection_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );", "");
	} 
    else if ($data[0] == 3) 
    {
		echo create_drop_down("cbo_knitting_company", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select Knitting Company --", 0, "load_drop_down( 'requires/grey_fabric_transection_report_controller',this.value, 'load_drop_down_floor', 'floor_td' );");
	} 
    else 
    {
		echo create_drop_down("cbo_knitting_company", 130, $blank_array, "", 1, "-- Select Knitting Company --", 1, "");
	}
	exit();
}

if ($action=="load_drop_down_floor")
{
    echo create_drop_down( "cbo_floor_id", 65, "select a.id, a.floor_name from lib_prod_floor a where a.company_id in($data)  and a.production_process=2 and a.status_active=1 and a.is_deleted=0  group by a.id, a.floor_name order by a.floor_name","id,floor_name", 1, "-- Select Floor --", 0, "","" );
  exit();    
}

if($action=="load_drop_down_buyer")
{
    echo load_html_head_contents("Buyer Info", "../../../../", 1, 1,'','','');
    extract($_REQUEST);
    $data=explode('_',$data);
    $company=$data[0];
    if($company>0)
    {
        echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,2,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
    }
    else
    {
        echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- Select Party --", $selected, "",0,"","","","");
    }
    exit();
}

if ($action == "job_no_popup")
{
    echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
    extract($_REQUEST);
    ?>

    <script>
        var hide_fso_id='<? echo $hide_fso_id; ?>';
        var selected_id = new Array, selected_name = new Array();

        function check_all_data(is_checked)
        {
            var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
            tbl_row_count = tbl_row_count - 1;

            for( var i = 1; i <= tbl_row_count; i++ ) {
                js_set_value( i );
            }
        }

        function toggle( x, origColor )
        {
            var newColor = 'yellow';
            if ( x.style )
            {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
            }
        }

        function set_all()
        {
            var old=document.getElementById('txt_fso_row_id').value;
            if(old!="")
            {
                old=old.split(",");
                for(var i=0; i<old.length; i++)
                {
                    js_set_value( old[i] )
                }
            }
        }

        function js_set_value( str)
        {

            toggle( document.getElementById( 'search' + str ), '#FFFFCC' );


            if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 )
            {
                selected_id.push( $('#txt_individual_id' + str).val() );
                selected_name.push( $('#txt_individual' + str).val() );

            }
            else
            {
                for( var i = 0; i < selected_id.length; i++ )
                {
                    if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
                }
                selected_id.splice( i, 1 );
                selected_name.splice( i, 1 );
            }
            var id =''; var name = '';
            for( var i = 0; i < selected_id.length; i++ ) {
                id += selected_id[i] + ',';
                name += selected_name[i] + ',';
            }
            id = id.substr( 0, id.length - 1 );
            name = name.substr( 0, name.length - 1 );

            $('#hide_fso_id').val( id );
            $('#hide_fso_no').val( name );
        }

    </script>

    </head>
    <body>
        <div align="center">
            <form name="styleRef_form" id="styleRef_form">
                <fieldset style="width:710px;">
                    <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                        <thead>
                            <th>Company</th>
                            <th>Buyer Name</th>
                            <th>Job Year</th>
                            <th>Within Group</th>
                            <th>FSO NO.</th>
                            <th>Booking NO.</th>
                            <th>Knitting Delivery Challan No</th>
                            <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th>
                            <input type="hidden" name="hide_fso_no" id="hide_fso_no" value="" />
                            <input type="hidden" name="hide_fso_id" id="hide_fso_id" value="" />

                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <?
                                    echo create_drop_down( "cbo_company_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $cbo_company_id, "",0 );
                                    ?>
                                </td>
                                <td align="center">
                                    <?
                                    echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and b.tag_company=$cbo_company_id and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
                                    ?>
                                </td>
                                <td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
                                <td><? echo create_drop_down( "cbo_within_group", 80, $yes_no,"", 1, "-- Select --", $selected, "",0,"" );?></td>
                                <td>
                                    <input type="text" style="width:100px" class="text_boxes" name="txt_fso_no" id="txt_fso_no" />
                                </td>
                                <td>
                                    <input type="text" style="width:100px" class="text_boxes" name="txt_booking_no" id="txt_booking_no" />
                                </td>
                                <td>
                                    <input type="text" style="width:100px" class="text_boxes" name="txt_issue_no" id="txt_issue_no" />
                                </td>
                                <td align="center">
                                    <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_year').value+'**'+document.getElementById('cbo_within_group').value+'**'+document.getElementById('txt_fso_no').value+'**'+document.getElementById('txt_booking_no').value+'**'+'<? echo $hidden_fso_id;?>'+'**'+document.getElementById('txt_issue_no').value, 'create_fso_no_search_list_view', 'search_div', 'grey_fabric_transection_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action=="create_fso_no_search_list_view")
{
    $data=explode('**',$data);
    // print_r($data);
    $company_id=$data[0];
    $buyer_id=$data[1];
    $year=$data[2];
    $within_group=$data[3];
    $fso_no=trim($data[4]);
    $booking_no=trim($data[5]);
    $hidden_fso_id=trim($data[6]);
    $issue_no=trim($data[7]);

    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

    $search_cond = "";

    if($data[1]==0)
    {
        if ($_SESSION['logic_erp']["data_level_secured"]==1)
        {
            if($_SESSION['logic_erp']["buyer_id"]!="")
            {
                $buyer_cond_with_1=" and c.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
                $buyer_cond_with_2=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
            }
            else
            {
                $buyer_cond_with_1 =  "";
                $buyer_cond_with_2 =  "";
            }
        }
        else
        {
            $buyer_cond_with_1 =  "";
            $buyer_cond_with_2 =  "";
        }
    }
    else
    {
        $buyer_cond_with_1 =  " and c.buyer_id=$data[1]";
        $buyer_cond_with_2 =  " and a.buyer_id=$data[1]";
    }


    if($fso_no != "")
    {
        $search_cond .= " and a.job_no like '%$fso_no%'" ;
    }
    if($issue_no != "")
    {
        $search_cond .= " and e.sys_number like '%$issue_no%'" ;
    }
    if($booking_no != "")
    {
        $search_cond .= " and a.sales_booking_no like '%$booking_no%'" ;
    }
    if($db_type==0)
    {
        if($year!=0) $search_cond .=" and YEAR(a.insert_date)=$year"; else $search_cond .="";
    }
    else if($db_type==2)
    {
        $year_field_con=" and to_char(a.insert_date,'YYYY')";
        if($year!=0) $search_cond .=" $year_field_con=$year"; else $search_cond .="";
    }
    // echo $search_cond; die;
    $sql_2 ="SELECT a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,a.buyer_id, e.sys_number 
    from fabric_sales_order_mst a, fabric_sales_order_dtls b, pro_roll_details d, pro_grey_prod_delivery_mst e
    where a.id=b.mst_id and a.id=d.po_breakdown_id and d.mst_id=e.id and d.entry_form=56 and e.entry_form=56 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id $search_cond and a.within_group=2 $buyer_cond_with_2
    group by a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,a.buyer_id, e.sys_number 
    order by id desc";

    $sql_1 = "SELECT a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,c.buyer_id, e.sys_number 
    from fabric_sales_order_mst a, fabric_sales_order_dtls b, wo_booking_mst c, pro_roll_details d, pro_grey_prod_delivery_mst e
    where a.id=b.mst_id and a.sales_booking_no=c.booking_no and a.id=d.po_breakdown_id and d.mst_id=e.id and d.entry_form=56 and e.entry_form=56 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
    and b.is_deleted=0 and a.company_id=$company_id $search_cond and a.within_group=1 $buyer_cond_with_1
    group by a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,c.buyer_id, e.sys_number ";

    if($within_group == 1)
    {
        $sql = $sql_1 ;
    }
    else if($within_group == 2)
    {
        $sql = $sql_2;
    }
    else
    {
        $sql = $sql_1." union all ". $sql_2 ;
    }
    // echo $sql;
    ?>

    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table" align="left">
        <thead>
            <th width="40">SL</th>
            <th width="130">Company</th>
            <th width="120">Buyer</th>
            <th width="150">FSO No</th>
            <th width="100">Booking No</th>
            <th width="">Knitting Delivery Challan No</th>
        </thead>
    </table>
    <div style="width:700px; overflow-y:scroll; max-height:250px; float: left;" id="buyer_list_view" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="682" class="rpt_table" id="tbl_list_search" >
            <?php
            $i=1; $fso_row_id="";
            $nameArray=sql_select( $sql );
            foreach ($nameArray as $selectResult)
            {
                if ($i%2==0)
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";
                ?>

                <tr height="20" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
                    <td width="40" align="center"><?php echo "$i"; ?>
                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $selectResult[csf('id')]; ?>"/>
                    <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $selectResult[csf('job_no')]; ?>"/>
                </td>
                <td width="130"><p><?php echo $company_arr[$selectResult[csf('company_id')]]; ?></p></td>
                <td width="120" title="<? echo $selectResult[csf('buyer_id')];?>"><?php echo $buyer_arr[$selectResult[csf('buyer_id')]]; ?></td>
                <td width="150"><p><?php echo $selectResult[csf('job_no')];?></p></td>
                <td width="100"><?php echo $selectResult[csf('sales_booking_no')];?></td>
                <td width=""><?php echo $selectResult[csf('sys_number')];?></td>
            </tr>
            <?
            $i++;
        }
        ?>
    </table>
    </div>

    <table width="600" cellspacing="0" cellpadding="0" style="border:none" align="left">
        <tr>
            <td align="center" height="30" valign="bottom">
                <div style="width:100%">
                    <div style="width:50%; float:left" align="left">
                        <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                    </div>
                    <div style="width:50%; float:left" align="left">
                        <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <?
    exit();
}

if($action=="generated_report")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    $company = str_replace("'","",$cbo_company_name);
    $cbo_knitting_source = str_replace("'","",$cbo_knitting_source);
    $cbo_knitting_company = str_replace("'","",$cbo_knitting_company);
    $cbo_floor_id = str_replace("'","",$cbo_floor_id);

    $year = str_replace("'","",$cbo_year);
    $buyer = str_replace("'","",$cbo_buyer_name);
    $txt_job_no = str_replace("'","",$txt_job_no);
    $txt_int_ref = str_replace("'","",$txt_int_ref);
    $txt_style_no = str_replace("'","",$txt_style_no);
    $txt_booking_no = str_replace("'","",$txt_booking_no);

    $cbo_based_on = str_replace("'","",$cbo_based_on);
    $txt_date_from = str_replace("'","",$txt_date_from);
    $txt_date_to = str_replace("'","",$txt_date_to);

    if ($buyer==0) $buyer_cond=""; else $buyer_cond="  and b.buyer_name='$buyer'";
    if ($txt_job_no=="") $job_cond=""; else $job_cond="  and b.job_no_prefix_num='$txt_job_no'";
    if ($txt_int_ref=="") $int_ref_cond=""; else $int_ref_cond="  and a.grouping='$txt_int_ref'";
    if ($txt_style_no=="") $style_cond=""; else $style_cond="  and b.style_ref_no='$txt_style_no'";
    if ($txt_booking_no=="") $booking_no_cond=""; else $booking_no_cond="  and c.booking_no_prefix_num='$txt_booking_no'";

    if ($cbo_knitting_company==0) $knitting_company_cond=""; else $knitting_company_cond="  and a.knitting_company='$cbo_knitting_company'";
    if ($cbo_knitting_source==0) $knitting_source_cond=""; else $knitting_source_cond="  and a.knitting_source='$cbo_knitting_source'";
    if ($company==0) $company_cond=""; else $company_cond="  and a.company_id='$company'";
    if ($cbo_floor_id==0) $floor_cond=""; else $floor_cond="  and a.floor_ids='$cbo_floor_id'";
    if($db_type==0) $year_field_by="and YEAR(a.insert_date)"; else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
    if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";

    if($txt_date_from && $txt_date_to && $cbo_based_on>0) 
    {
        if($db_type==0)
        {
            $date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
            $date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
            if ($cbo_based_on==1)
            {
                $dates_com="and a.delevery_date between '$date_from' and '$date_to'";
            }
            
        }
        if($db_type==2)
        {
            $date_from=change_date_format($txt_date_from,'','',1);
            $date_to=change_date_format($txt_date_to,'','',1);
            if ($cbo_based_on==1)
            {
                $dates_com="and a.delevery_date between '$date_from' and '$date_to'";
            }
        }
    }
    // echo $dates_com.'='.$cbo_based_on;die;

    $user_name_arr = return_library_array( "select id, user_name from user_passwd",'id','user_name');
    $season_name_arr = return_library_array( "select id,season_name from lib_buyer_season",'id','season_name');
    $supplier_arr = return_library_array( "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name",'id','supplier_name');
    $store_arr = return_library_array( "SELECT a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id $company_cond and b.category_type=13 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name",'id','store_name');
    //floorSql
    $floorSql = "SELECT a.id, a.floor_name from lib_prod_floor a where a.production_process=2 $company_cond and a.status_active=1 and a.is_deleted=0";
    $floorDetails = return_library_array( $floorSql, 'id', 'floor_name');

    if ($buyer !=0 || $txt_job_no !="" || $txt_int_ref !="" || $txt_style_no !="" || $txt_booking_no !="") 
    {
        $po_sql =  sql_select("SELECT a.id, a.grouping, b.buyer_name, b.job_no, b.style_ref_no, b.season, c.booking_no, c.booking_type
        from wo_po_break_down a, wo_po_details_master b, wo_booking_mst c
        where a.job_no_mst=b.job_no and b.job_no=c.job_no $year_cond $buyer_cond $job_cond $int_ref_cond $style_cond $booking_no_cond and c.booking_type in(1,4)");
        $po_id_arr=array();
        foreach ($po_sql as $val)
        {
            $po_id_arr[$val[csf("id")]]=$val[csf("id")];
        }
        if ($txt_booking_no !="") 
        {
            if($db_type==0) $year_field_by="and YEAR(c.insert_date)"; else if($db_type==2) $year_field_by=" and to_char(c.insert_date,'YYYY')";
            if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";

           $non_order_booking=sql_select("SELECT c.id, null as grouping, 0 as buyer_name, null as job_no, null as style_ref_no, null as season, c.booking_no, 4 as booking_type from wo_non_ord_samp_booking_mst c where c.booking_type=4 $booking_no_cond $year_cond");
            foreach ($non_order_booking as $val)
            {
                $po_id_arr[$val[csf("id")]]=$val[csf("id")];
            }
        }
        
        if(!empty($po_id_arr))
        {
            $po_id_cond = where_con_using_array($po_id_arr, '0', 'c.po_breakdown_id');
        }
    }
    // echo $po_id_cond;die;

    // Grey Receive Date
    if ($cbo_based_on == 2) 
    {
        if($db_type==0)
        {
            $date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
            $date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
            if ($cbo_based_on==2) 
            {
                $rcv_dates_cond="and a.receive_date between '$date_from' and '$date_to'";
            }
        }
        if($db_type==2)
        {
            $date_from=change_date_format($txt_date_from,'','',1);
            $date_to=change_date_format($txt_date_to,'','',1);
            if ($cbo_based_on==2) 
            {
                $rcv_dates_cond="and a.receive_date between '$date_from' and '$date_to'";
            }
        }

        // Knit Grey Fabric Roll Receive
        $roll_recv_sql="SELECT a.booking_id as delevery_id FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
        WHERE a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.entry_form=58 and c.entry_form=58 and c.is_sales=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 $company_cond $knitting_source_cond $knitting_company_cond $rcv_dates_cond
        and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.booking_without_order=0";
        // echo $roll_recv_sql;
        $roll_recv_data=sql_select($roll_recv_sql);
        $delivery_id_arr = array();
        foreach($roll_recv_data as $val)
        {
            $delivery_id_arr[$val[csf("delevery_id")]] = $val[csf("delevery_id")];
        }
        if (empty($delivery_id_arr)) 
        {
            echo "Data Not Found";die;
        }
        $delivery_ids = implode(",", $delivery_id_arr);
        $delivery_ids_cond="";
        if($delivery_ids)
        {
            $delivery_ids = implode(",",array_filter(array_unique(explode(",", $delivery_ids))));
            $delivery_ids_arr = explode(",", $delivery_ids);
            if($db_type==0)
            {
                $delivery_ids_cond = " and a.booking_id in ($delivery_ids )";
            }
            else
            {
                if(count($delivery_ids_arr)>999)
                {
                    $delivery_ids_chunk_arr=array_chunk($delivery_ids_arr, 999);
                    $delivery_ids_cond=" and (";
                    foreach ($delivery_ids_chunk_arr as $value)
                    {
                        $delivery_ids_cond .=" a.id in (".implode(",", $value).") or ";
                    }
                    $delivery_ids_cond=chop($delivery_ids_cond,"or ");
                    $delivery_ids_cond.=")";
                }
                else
                {
                    $delivery_ids_cond = " and a.id in ($delivery_ids )";
                }
            }
        }
    }
    
    // Roll Wise Grey Fabric Delivery to Store // main query
    $delivery_barcode_data = "SELECT a.id as delevery_id, a.sys_number, a.delevery_date, a.company_id, a.knitting_source, a.knitting_company, a.remarks, a.inserted_by, a.insert_date , a.updated_by, a.update_date, a.floor_ids, b.id as dtls_id, c.barcode_no, c.roll_id, c.qnty as delivery_qty, c.po_breakdown_id, c.booking_without_order, a.remarks
    from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, pro_roll_details c 
    where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and a.entry_form=56 and c.entry_form=56 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_cond $knitting_source_cond $knitting_company_cond $dates_com $floor_cond $delivery_ids_cond $po_id_cond";
    // echo $delivery_barcode_data;die;
    $roll_delivery_data=sql_select($delivery_barcode_data);
    $delivery_roll_arr = array();
    foreach($roll_delivery_data as $val)
    {
        $delivery_roll_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
    }

    $con = connect();
    $r_id2=execute_query("delete from tmp_barcode_no where userid=$user_name");
    if($r_id2)
    {
        oci_commit($con);
    }
    if(!empty($delivery_roll_arr))
    {
        foreach ($delivery_roll_arr as $row)
        {
            $r_id=execute_query("insert into tmp_barcode_no (userid, barcode_no) values ($user_name,".$row.")");
            if($r_id) 
            {
                $r_id=1;
            } 
            else 
            {
                echo "insert into tmp_barcode_no (userid, barcode_no) values ($user_name,".$row.")";
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

    $delivery_roll_arr = array_filter($delivery_roll_arr);
    if(count($delivery_roll_arr ) >0 ) // production
    {
        $production_sql = sql_select("SELECT b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id, a.prod_id, b.booking_no,c.booking_id, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, c.knitting_source, c.challan_no as production_challan, c.knitting_company, c.challan_no as recv_challan_no, a.yarn_prod_id, a.body_part_id, b.coller_cuff_size
        from pro_grey_prod_entry_dtls a, pro_roll_details b, inv_receive_master c, tmp_barcode_no d 
        where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form in(2,58) and b.entry_form in(2,58)  and a.status_active=1 and b.status_active=1 and b.barcode_no = d.barcode_no and d.userid= $user_name order by c.entry_form desc");

        $prodBarcodeData = array();
        foreach ($production_sql as $row)
        {
            $prodBarcodeData[$row[csf("barcode_no")]]["body_part_id"] =$row[csf("body_part_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["detarmination_id"] =$row[csf("febric_description_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["dia_width"] =$row[csf("width")];
            $prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"] =$row[csf("stitch_length")];
            $prodBarcodeData[$row[csf("barcode_no")]]["gsm"] =$row[csf("gsm")];
            $prodBarcodeData[$row[csf("barcode_no")]]["recv_challan_no"] =$row[csf("recv_challan_no")];
            
            $allDeterArr[$row[csf("febric_description_id")]] =$row[csf("febric_description_id")];
            $allColorArr[$row[csf("color_id")]] =$row[csf("color_id")];
            $allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];
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
    }
    // echo "<pre>";print_r($roll_delivery_data);die;
    $roll_data_arr = array(); $delivery_id_arr = array();
    foreach($roll_delivery_data as $row)
    {
        $body_part_no=$prodBarcodeData[$row[csf("barcode_no")]]["body_part_id"];
        $detarmination_id=$prodBarcodeData[$row[csf("barcode_no")]]["detarmination_id"];
        $dia_width=$prodBarcodeData[$row[csf("barcode_no")]]["dia_width"];
        $gsm=$prodBarcodeData[$row[csf("barcode_no")]]["gsm"];
        $stitch_length=$prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"];

        $roll_data_arr[$row[csf("delevery_id")]][$body_part_no][$detarmination_id][$dia_width][$gsm]['delevery_date']=$row[csf("delevery_date")];
        $roll_data_arr[$row[csf("delevery_id")]][$body_part_no][$detarmination_id][$dia_width][$gsm]['sys_number']=$row[csf("sys_number")];
        $roll_data_arr[$row[csf("delevery_id")]][$body_part_no][$detarmination_id][$dia_width][$gsm]['knitting_source']=$row[csf("knitting_source")];
        $roll_data_arr[$row[csf("delevery_id")]][$body_part_no][$detarmination_id][$dia_width][$gsm]['knitting_company']=$row[csf("knitting_company")];
        $roll_data_arr[$row[csf("delevery_id")]][$body_part_no][$detarmination_id][$dia_width][$gsm]['recv_challan_no']=$prodBarcodeData[$row[csf("barcode_no")]]["recv_challan_no"];
        $roll_data_arr[$row[csf("delevery_id")]][$body_part_no][$detarmination_id][$dia_width][$gsm]['dtls_id'].=$row[csf("dtls_id")].'*';
        $roll_data_arr[$row[csf("delevery_id")]][$body_part_no][$detarmination_id][$dia_width][$gsm]['po_buyer']=$row[csf("po_buyer")];
        $roll_data_arr[$row[csf("delevery_id")]][$body_part_no][$detarmination_id][$dia_width][$gsm]['buyer_id']=$row[csf("buyer_id")];
        $roll_data_arr[$row[csf("delevery_id")]][$body_part_no][$detarmination_id][$dia_width][$gsm]['roll_count']++;
        $roll_data_arr[$row[csf("delevery_id")]][$body_part_no][$detarmination_id][$dia_width][$gsm]['delivery_qty']+=$row[csf("delivery_qty")];
        $roll_data_arr[$row[csf("delevery_id")]][$body_part_no][$detarmination_id][$dia_width][$gsm]['stitch_length']=$stitch_length;
        $roll_data_arr[$row[csf("delevery_id")]][$body_part_no][$detarmination_id][$dia_width][$gsm]['floor_ids']=$row[csf("floor_ids")];
        $roll_data_arr[$row[csf("delevery_id")]][$body_part_no][$detarmination_id][$dia_width][$gsm]['po_breakdown_id']=$row[csf("po_breakdown_id")];
        $roll_data_arr[$row[csf("delevery_id")]][$body_part_no][$detarmination_id][$dia_width][$gsm]['booking_without_order']=$row[csf("booking_without_order")];
        $roll_data_arr[$row[csf("delevery_id")]][$body_part_no][$detarmination_id][$dia_width][$gsm]['remarks']=$row[csf("remarks")];
        if ($row[csf("booking_without_order")]==0) 
        {
            $po_id_arr[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
        }
        else
        {
            $non_order_arr[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
        }
        
        $delivery_id_arr[$row[csf("delevery_id")]] = $row[csf("delevery_id")];
    }
    // echo "<pre>";print_r($delivery_id_arr);die;
    $po_id = implode(",", $po_id_arr);
    $non_order = implode(",", $non_order_arr);
    $delivery_ids = implode(",", $delivery_id_arr);

    $po_id_cond="";
    if($po_id)
    {
        $po_id = implode(",",array_filter(array_unique(explode(",", $po_id))));
        $po_id_arr = explode(",", $po_id);
        if($db_type==0)
        {
            $po_id_cond = " and a.id in ($po_id )";
        }
        else
        {
            if(count($po_id_arr)>999)
            {
                $po_id_chunk_arr=array_chunk($po_id_arr, 999);
                $po_id_cond=" and (";
                foreach ($po_id_chunk_arr as $value)
                {
                    $po_id_cond .="a.id in (".implode(",", $value).") or ";
                }
                $po_id_cond=chop($po_id_cond,"or ");
                $po_id_cond.=")";
            }
            else
            {
                $po_id_cond = " and a.id in ($po_id )";
            }
        }
    }

    $non_order_cond="";
    if($non_order)
    {
        $non_order = implode(",",array_filter(array_unique(explode(",", $non_order))));
        $non_order_arr = explode(",", $non_order);
        if($db_type==0)
        {
            $non_order_cond = " and a.id in ($non_order )";
        }
        else
        {
            if(count($non_order_arr)>999)
            {
                $po_id_chunk_arr=array_chunk($non_order_arr, 999);
                $non_order_cond=" and (";
                foreach ($po_id_chunk_arr as $value)
                {
                    $non_order_cond .="a.id in (".implode(",", $value).") or ";
                }
                $non_order_cond=chop($non_order_cond,"or ");
                $non_order_cond.=")";
            }
            else
            {
                $non_order_cond = " and a.id in ($non_order )";
            }
        }
    }

    /*$delivery_ids_cond="";
    if($delivery_ids)
    {
        $delivery_ids = implode(",",array_filter(array_unique(explode(",", $delivery_ids))));
        $delivery_ids_arr = explode(",", $delivery_ids);
        if($db_type==0)
        {
            $delivery_ids_cond = " and a.booking_id in ($delivery_ids )";
        }
        else
        {
            if(count($delivery_ids_arr)>999)
            {
                $delivery_ids_chunk_arr=array_chunk($delivery_ids_arr, 999);
                $delivery_ids_cond=" and (";
                foreach ($delivery_ids_chunk_arr as $value)
                {
                    $delivery_ids_cond .="a.booking_id in (".implode(",", $value).") or ";
                }
                $delivery_ids_cond=chop($delivery_ids_cond,"or ");
                $delivery_ids_cond.=")";
            }
            else
            {
                $delivery_ids_cond = " and a.booking_id in ($delivery_ids )";
            }
        }
    }*/

    // Knit Grey Fabric Roll Receive
    $roll_recv_sql="SELECT a.receive_date, a.recv_number, a.store_id, a.booking_id as delevery_id, a.booking_no, a.inserted_by, a.insert_date , a.updated_by, a.update_date, a.challan_no, b.febric_description_id as detar_id, b.gsm, b.width, b.body_part_id, c.po_breakdown_id as fso_id, c.qnty as recv_qty, c.barcode_no, c.id as roll_id, c.roll_no, c.qc_pass_qnty, c.is_sales, c.booking_without_order
    FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, tmp_barcode_no d
    WHERE a.id=b.mst_id and b.id=c.dtls_id and c.barcode_no=d.barcode_no and a.entry_form=58 and c.entry_form=58 and c.is_sales=0 and c.status_active=1 and c.is_deleted=0  and a.status_active=1  and d.userid= $user_name  $company_cond $knitting_source_cond $knitting_company_cond and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";// $delivery_ids_cond

    // echo  $roll_recv_sql;die;
    $roll_recv_data=sql_select($roll_recv_sql);
    $roll_recv_arr=array();
    $roll_recv_challan_no_arr=array();
    foreach ($roll_recv_data as $rows)
    {
        $roll_recv_challan_no_arr[$rows[csf("delevery_id")]]['delevery_number']=$rows[csf("booking_no")];

        $roll_recv_arr[$rows[csf("delevery_id")]][$rows[csf("body_part_id")]][$rows[csf("detar_id")]][$rows[csf("width")]][$rows[csf("gsm")]]['recv_number']=$rows[csf("recv_number")];
        $roll_recv_arr[$rows[csf("delevery_id")]][$rows[csf("body_part_id")]][$rows[csf("detar_id")]][$rows[csf("width")]][$rows[csf("gsm")]]['receive_date']=$rows[csf("receive_date")];
        $roll_recv_arr[$rows[csf("delevery_id")]][$rows[csf("body_part_id")]][$rows[csf("detar_id")]][$rows[csf("width")]][$rows[csf("gsm")]]['recv_qty']+=$rows[csf("recv_qty")];
        $roll_recv_arr[$rows[csf("delevery_id")]][$rows[csf("body_part_id")]][$rows[csf("detar_id")]][$rows[csf("width")]][$rows[csf("gsm")]]['store_id']=$store_arr[$rows[csf("store_id")]];
        $roll_recv_arr[$rows[csf("delevery_id")]][$rows[csf("body_part_id")]][$rows[csf("detar_id")]][$rows[csf("width")]][$rows[csf("gsm")]]['challan_no']=$rows[csf("challan_no")];

        $roll_recv_arr[$rows[csf("delevery_id")]][$rows[csf("body_part_id")]][$rows[csf("detar_id")]][$rows[csf("width")]][$rows[csf("gsm")]]['recv_roll_count']++;
    }
    // echo "<pre>";print_r($roll_recv_arr);die;
    
    // booking sql
    if (!empty($po_id_arr)) 
    {
        $booking_sql =  sql_select("SELECT a.id, a.grouping, b.buyer_name, b.job_no, b.style_ref_no, b.season_buyer_wise, c.booking_no, c.booking_type
        from wo_po_break_down a, wo_po_details_master b, wo_booking_mst c
        where a.job_no_mst=b.job_no and b.job_no=c.job_no $po_id_cond and c.booking_type in(1,4)");
        $with_order_booking_arr=array();
        foreach ($booking_sql as $val)
        {
            $with_order_booking_arr[$val[csf("id")]]["booking_no"]=$val[csf("booking_no")];
            $with_order_booking_arr[$val[csf("id")]]["buyer_name"]=$val[csf("buyer_name")];
            $with_order_booking_arr[$val[csf("id")]]["job_no"]=$val[csf("job_no")];
            $with_order_booking_arr[$val[csf("id")]]["grouping"]=$val[csf("grouping")];
            $with_order_booking_arr[$val[csf("id")]]["style_ref_no"]=$val[csf("style_ref_no")];
            $with_order_booking_arr[$val[csf("id")]]["season"]=$val[csf("season_buyer_wise")];
        }
    }

    if (!empty($non_order_arr)) 
    {
        $sampleBooking_sql =  sql_select("SELECT a.id, null as grouping, 0 as buyer_name, null as job_no, null as style_ref_no, null as season_buyer_wise, a.booking_no, 4 as booking_type from wo_non_ord_samp_booking_mst a where a.booking_type=4 $non_order_cond");
        $non_order_booking_arr=array();
        foreach ($sampleBooking_sql as $val)
        {
            $non_order_booking_arr[$val[csf("id")]]["booking_no"]=$val[csf("booking_no")];
        }
    }

    execute_query("delete from tmp_barcode_no where userid=$user_name");
    oci_commit($con);
    
    ob_start();
    ?>
    <style type="text/css">
        .word_wrap_break {
            word-break: break-all;
            word-wrap: break-word;
        }
    </style>
    <div align="left">
        <fieldset style="width:2125px;">
            <?
            if(count($roll_delivery_data)>0)
            {
                ?>
                <div  align="center"><strong>
                    <?
                    if ($company!=0) 
                    {
                        echo $company_library[$company]; 
                    }
                    else
                    {
                        if ($cbo_knitting_source==1) 
                        {
                            echo $company_library[$cbo_knitting_company]; 
                        }
                        else
                        {
                            echo $supplier_arr[$cbo_knitting_company];
                        } 
                    }
                     
                    ?></strong>
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
                echo "<b>Data Not Found</b>";die;
            }
            ?>
            <div align="left">
                <table class="rpt_table" width="2285" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                    <thead>
                        <tr>
                            <th colspan="21">Knitting Delivery Information</th>
                            <th colspan="8">Grey Receive Status</th>
                        </tr>
                    </thead>
                    <thead>
                        <tr>
                            <th width="30" class="word_wrap_break">SL</th>
                            <th width="75" class="word_wrap_break">Knitting Delivery Date</th>
                            <th width="120" class="word_wrap_break">Delivery Challan No</th>
                            <th width="80" class="word_wrap_break">Source</th>
                            <th width="80" class="word_wrap_break">Knitting Company</th>
                            <th width="80" class="word_wrap_break">Floor</th>
                            <th width="80" class="word_wrap_break">Buyer</th>
                            <th width="60" class="word_wrap_break">Job No</th>
                            <th width="70" class="word_wrap_break">Int.Ref.</th>
                            <th width="70" class="word_wrap_break">Style No</th>
                            <th width="70" class="word_wrap_break">Season</th>
                            <th width="120" class="word_wrap_break">Fabric Booking No.</th>
                            <th width="70" class="word_wrap_break">Body Part</th>
                            <th width="100" class="word_wrap_break">Construction</th>
                            <th width="150" class="word_wrap_break">Fab. Composition</th>
                            <th width="50" class="word_wrap_break">Dia/ Width</th>
                            <th width="50" class="word_wrap_break">S/L</th>
                            <th width="50" class="word_wrap_break">GSM</th>
                            <th width="70" class="word_wrap_break">Delivery Qty.</th>
                            <th width="50" class="word_wrap_break">No of Roll</th>
                            <th width="100" class="word_wrap_break">Remarks</th>

                            <th width="70" class="word_wrap_break">Grey Receive Date</th>
                            <th width="100" class="word_wrap_break">Receive Challan No</th>
                            <th width="100" class="word_wrap_break">Party Challan No</th>
                            <th width="100" class="word_wrap_break">Store Name</th>
                            <th width="70" class="word_wrap_break">Receive Qty.</th>
                            <th width="70" class="word_wrap_break">Receive Balance</th>
                            <th width="40" class="word_wrap_break">No of Roll</th>
                            <th width="70" class="word_wrap_break">Receive Status</th>
                        </tr>
                    </thead>
                </table>
                <div style=" max-height:350px; width:2305px; overflow-y:scroll;" id="scroll_body">
                    <table class="rpt_table" id="table_body" width="2285" cellpadding="0" cellspacing="0" border="1" rules="all">
                        <tbody>
                            <?
                            // echo "<pre>";print_r($roll_data_arr);die;
                            $i=1;$total_issue_qty=$total_roll_count=$total_roll_rcv_qty=$total_receive_balance=$total_roll_recv_count=0;
                            $roll_receive_date="";
                            foreach ($roll_data_arr as $delevery_id_key => $delevery_val)
                            {
                                foreach ($delevery_val as $body_part_key => $body_part_val)
                                {
                                    foreach ($body_part_val as $detar_id_key => $detar_id_val) 
                                    {
                                        foreach ($detar_id_val as $dia_width_key => $dia_width_val)
                                        {
                                            foreach ($dia_width_val as $gsm_key => $row)
                                            {
                                                $delivery_no_in_recv=$roll_recv_challan_no_arr[$delevery_id_key]['delevery_number'];
                                                $roll_receive_challan=$roll_recv_arr[$delevery_id_key][$body_part_key][$detar_id_key][$dia_width_key][$gsm_key]['challan_no'];
                                                $roll_receive_date=$roll_recv_arr[$delevery_id_key][$body_part_key][$detar_id_key][$dia_width_key][$gsm_key]['receive_date'];
                                                $roll_recv_number=$roll_recv_arr[$delevery_id_key][$body_part_key][$detar_id_key][$dia_width_key][$gsm_key]['recv_number'];
                                                $roll_recv_store=$roll_recv_arr[$delevery_id_key][$body_part_key][$detar_id_key][$dia_width_key][$gsm_key]['store_id'];
                                                $roll_rcv_qty=$roll_recv_arr[$delevery_id_key][$body_part_key][$detar_id_key][$dia_width_key][$gsm_key]['recv_qty'];
                                                $roll_recv_count=$roll_recv_arr[$delevery_id_key][$body_part_key][$detar_id_key][$dia_width_key][$gsm_key]['recv_roll_count'];
                                                $delivery_qty=number_format($row['delivery_qty'],2,'.','');
                                                $roll_rcv_qty=number_format($roll_rcv_qty,2,'.','');
                                                $receive_balance=$delivery_qty-$roll_rcv_qty;
                                                
                                                //$recv_status = ($receive_balance>0) ? "Partial Receive" : "Full" ;
                                                $recv_status="";
                                                if ($delivery_no_in_recv=="") 
                                                {
                                                    $recv_status ='Full Pending';
                                                }
                                                else if($receive_balance>0)
                                                {
                                                    $recv_status = "Partial Receive";
                                                }
                                                else
                                                {
                                                    $recv_status = "Full";
                                                }

                                                $delivery_date=change_date_format($row['delevery_date']);
                                                $roll_recv_date=change_date_format($roll_receive_date);
                                                $date1 = strtotime($delivery_date);
                                                $date2 = strtotime($roll_recv_date);
                                                $execution_days= ($date2 - $date1)/60/60/24; 
                                                //echo $execution_days+1;

                                                $report_title='Roll Wise Grey Fabric Delivery challan';
                                                $data=$row['company_id'].'*'.$row['sys_number'].'*'.$delevery_id_key.'*'.$report_title.'*'.$row['knitting_source'].'*0'.'*1';

                                                if ($row['booking_without_order']==0) 
                                                {
                                                    $booking_no=$with_order_booking_arr[$row['po_breakdown_id']]["booking_no"];
                                                    $buyer_name=$with_order_booking_arr[$row['po_breakdown_id']]["buyer_name"];
                                                    $job_no=$with_order_booking_arr[$row['po_breakdown_id']]["job_no"];
                                                    $int_ref=$with_order_booking_arr[$row['po_breakdown_id']]["grouping"];
                                                    $style_no=$with_order_booking_arr[$row['po_breakdown_id']]["style_ref_no"];
                                                    $season=$with_order_booking_arr[$row['po_breakdown_id']]["season"];
                                                }
                                                else
                                                {
                                                    $booking_no=$non_order_booking_arr[$row['po_breakdown_id']]["booking_no"];
                                                    $buyer_name="";
                                                    $job_no="";
                                                    $int_ref="";
                                                    $style_no="";
                                                    $season="";
                                                }
                                                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                                ?>
                                                <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
                                                    <td class="word_wrap_break" width="30"><? echo $i; ?></td>
                                                    <td class="word_wrap_break" align="center" width="75"><p><? echo change_date_format($row['delevery_date']); ?></p></td>
                                                    <td class='word_wrap_break' width='120'><p><? echo $row['sys_number']; ?></p></td>                                                      
                                                    <td width="80"><p class="word_wrap_break"><? echo $knitting_source[$row['knitting_source']]; ?></p></td>
                                                    <td width="80"><p class="word_wrap_break"><? 
                                                    if ($row['knitting_source']==1) 
                                                    {
                                                        echo $company_library[$row['knitting_company']];
                                                    }
                                                    else
                                                    {
                                                        echo $supplier_arr[$row['knitting_company']];
                                                    } 
                                                    ?></p></td>
                                                    <td width="80"><p class="word_wrap_break"><? echo $floorDetails[$row['floor_ids']]; ?></p></td>
                                                    <td width="80"><p class="word_wrap_break"><? echo $buyer_arr[$buyer_name]; ?></p></td>
                                                    <td class="word_wrap_break" width="60" align="center"><p><? echo $job_no; ?></p></td>
                                                    <td class="word_wrap_break" width="70" align="center"><p><? echo $int_ref; ?></p></td>
                                                    <td width="70"><p class="word_wrap_break"><? echo $style_no; ?></p></td>
                                                    <td class="word_wrap_break" width="70" align="center"><p><? echo $season_name_arr[$season]; ?></p></td>
                                                    <td class="word_wrap_break" width="120" title="<? echo $row['po_breakdown_id']; ?>"><p><? echo $booking_no; ?></p></td>
                                                    <td class="word_wrap_break" width="70"><p><? echo $body_part[$body_part_key]; ?></p></td>
                                                    <td width="100" align="center"><p class="word_wrap_break"><? echo $constuction_arr[$detar_id_key]; ?></p></td>
                                                    <td width="150" align="center"><p class="word_wrap_break"><? echo $composition_arr[$detar_id_key]; ?></p></td>
                                                    <td width="50" align="center"><p class="word_wrap_break"><? echo $dia_width_key; ?></p></td>
                                                    <td width="50" align="center"><p class="word_wrap_break"><? echo $row['stitch_length']; ?></p></td>
                                                    <td width="50" align="center"><p class="word_wrap_break"><? echo $gsm_key; ?></p></td>
                                                    <td class="word_wrap_break" width="70" align="right"><p><? echo $delivery_qty; ?></p></td>
                                                    <td width="50" align="center"><p class="word_wrap_break"><? echo $row['roll_count']; ?></p></td>
                                                    <td width="100" align="center"><p class="word_wrap_break"><? echo $row['remarks']; ?></p></td>
                                                    <td class="word_wrap_break" align="center" width="70"><? echo change_date_format($roll_receive_date);  ?></td>
                                                    <td class="word_wrap_break"  width="100"><p><? echo $roll_recv_number; ?></p></td>
                                                    <td class="word_wrap_break"  width="100"><p><? echo $roll_receive_challan; ?></p></td>
                                                    <td class="word_wrap_break"  width="100"><p><? echo $roll_recv_store; ?></p></td>
                                                    <td class="word_wrap_break" align="right" width="70"><? echo $roll_rcv_qty; ?></td>                          
                                                    <td class="word_wrap_break" align="right" width="70" title="Grey Issue Qty - Receive Qty, <? echo $delivery_qty.'-'.$roll_rcv_qty; ?>"><? echo number_format($receive_balance,2,'.',''); ?></td>
                                                    <td class="word_wrap_break" width="40" align="center"><? echo $roll_recv_count;?></td>
                                                    <td class="word_wrap_break" align="center" width="70"><? echo $recv_status;?></td>
                                                </tr>
                                                <?
                                                $i++;
                                                $total_delivery_qty+=$row['delivery_qty'];
                                                $total_roll_count+=$row['roll_count'];
                                                $total_roll_rcv_qty+=$roll_rcv_qty;
                                                $total_receive_balance+=$receive_balance;
                                                $total_roll_recv_count+=$roll_recv_count;
                                            }
                                        }
                                    }
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <table class="rpt_table" width="2285" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tfoot>
                        <tr>
                            <th width="30" class="word_wrap_break"></th>
                            <th width="75" class="word_wrap_break"></th>
                            <th width="120" class="word_wrap_break"></th>
                            <th width="80" class="word_wrap_break"></th>
                            <th width="80" class="word_wrap_break"></th>
                            <th width="80" class="word_wrap_break"></th>
                            <th width="80" class="word_wrap_break"></th>
                            <th width="60" class="word_wrap_break"></th>
                            <th width="70" class="word_wrap_break"></th>
                            <th width="70" class="word_wrap_break"></th>
                            <th width="70" class="word_wrap_break"></th>
                            <th width="120" class="word_wrap_break"></th>
                            <th width="70" class="word_wrap_break"></th>
                            <th width="100" class="word_wrap_break"></th>
                            <th width="150" class="word_wrap_break"></th>
                            <th width="50" class="word_wrap_break"></th>
                            <th width="50" class="word_wrap_break"></th>
                            <th width="50">Total:</th>
                            <th width="70" align="right"><? echo number_format($total_delivery_qty,2); ?></th>
                            <th width="50" align="center"><? echo $total_roll_count; ?></th>
                            <th width="100" class="word_wrap_break"></th>
                            <th width="70" class="word_wrap_break"></th>
                            <th width="100" class="word_wrap_break"></th>
                            <th width="100" class="word_wrap_break"></th>
                            <th width="100" class="word_wrap_break"></th>
                            <th width="70" class="word_wrap_break" align="right"><? echo number_format($total_roll_rcv_qty,2); ?></th>
                            <th width="70" class="word_wrap_break" align="right"><? echo number_format($total_receive_balance,2); ?></th>
                            <th width="40" class="word_wrap_break"><? echo $total_roll_recv_count; ?></th>
                            <th width="70" class="word_wrap_break"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
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
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$report_type";
    exit();
}

if ($action == "roll_delivery_popup") 
{
    echo load_html_head_contents("Report Info", "../../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    ?>
    <div align="center" id="data_panel" style="width:100%">
        <script>
            function new_window()
            {
                var w = window.open("Surprise", "#");
                var d = w.document.open();
                d.write(document.getElementById('report_container').innerHTML);
                d.close();
            }
        </script>
        <div id="exc"></div>
    </div>
    <fieldset style="width:380px; margin-left:7px">        
        <?    
        //echo $dtls_id.'<br>';        
        $dtls_id = implode(',', explode("*", $dtls_id)) ;
        // print_r($dtls_id);
        $delivery_popup_sql = "SELECT c.roll_no, c.barcode_no, c.qnty as roll_wgt 
        from pro_roll_details c 
        where c.entry_form=56 and c.status_active=1 and c.is_deleted=0 and c.mst_id=$delivery_id and c.po_breakdown_id=$fso_id and c.dtls_id in($dtls_id) and c.is_sales=1";
        // echo $delivery_popup_sql;die;
        $delivery_popup_data=sql_select($delivery_popup_sql);
        $delivery_roll_arr = array();
        foreach($delivery_popup_data as $val)
        {
            $delivery_roll_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
        }
        $delivery_roll = implode(",", $delivery_roll_arr);
        $delivery_roll_cond="";
        if($delivery_roll)
        {
            $delivery_roll = implode(",",array_filter(array_unique(explode(",", $delivery_roll))));
            $delivery_roll_arr = explode(",", $delivery_roll);
            if($db_type==0)
            {
                $delivery_roll_cond = " and c.barcode_no in ($delivery_roll )";
            }
            else
            {
                if(count($delivery_roll_arr)>999)
                {
                    $delivery_roll_chunk_arr=array_chunk($delivery_roll_arr, 999);
                    $delivery_roll_cond=" and (";
                    foreach ($delivery_roll_chunk_arr as $value)
                    {
                        $delivery_roll_cond .=" c.barcode_no in (".implode(",", $value).") or ";
                    }
                    $delivery_roll_cond=chop($delivery_roll_cond,"or ");
                    $delivery_roll_cond.=")";
                }
                else
                {
                    $delivery_roll_cond = " and c.barcode_no in ($delivery_roll )";
                }
            }
        }
        // echo  $delivery_roll_cond;die;
        
        $roll_recv_sql="SELECT c.barcode_no, c.roll_no
        FROM pro_roll_details c
        WHERE c.entry_form=58 and c.is_sales=1 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$fso_id $delivery_roll_cond";
        // echo $recv_batch_sql;die;
        $roll_recv_data=sql_select($roll_recv_sql);
        $roll_recv_arr=array();
        foreach ($roll_recv_data as $rows)
        {
            $roll_recv_arr[$rows[csf("barcode_no")]]['barcode_no']=$rows[csf("barcode_no")];
            $roll_recv_arr[$rows[csf("barcode_no")]]['roll_no']=$rows[csf("roll_no")];
        }

        $table_width=350;
        $div_width=370;
        $table_width2=350;
        ob_start();
        ?>
        <div id="report_container">
            <div style="width:<? echo $table_width+20; ?>px; float:left;">
                <table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="float:left; margin-top: 10px;">
                    <thead>
                        <tr>
                            <th width="40">SL</th>
                            <th width="100">Barcode No</th>
                            <th width="40">Roll No</th>
                            <th width="70">Roll Wgt.</th>
                            <th width="">Status</th>
                        </tr>
                    </thead>
                </table>
                
                <div style="width:<? echo $div_width; ?>px; float:left; max-height:300px; overflow-y:scroll" id="scroll_body">
                <table width="<? echo $table_width2; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body_show6" style="float:left;">
                    <tbody>
                    <?
                    $i=1;
                    $total_roll_wgt=0;
                    foreach ($delivery_popup_data as $key => $row)
                    {
                        $status="";
                        if($roll_recv_arr[$row[csf("barcode_no")]]['barcode_no']=="")
                        {
                            $status="Pending";
                        }
                        else{
                            $status="Received";
                        }
                        $roll_no=$roll_recv_arr[$row[csf("barcode_no")]]['roll_no'];
                        ?>                      
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_2nd<? echo $i; ?>">
                            <td width="40" align="center"><p><? echo $i; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('barcode_no')]; ?></p></td>
                            <td width="40" align="center"><p><? echo $roll_no; ?></p></td>
                            <td width="70" align="right"><p><? echo $row[csf('roll_wgt')]; ?></p></td>
                            <td width="" align="center"><p><? echo $status; ?></p></td>
                        </tr>
                        <?
                        $i++;
                        $total_roll_wgt+=$row[csf('roll_wgt')];
                    }
                    ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>

    </fieldset>  
    <?        
    foreach (glob("$user_name*.xls") as $filename) 
    {
        if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_name."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc,ob_get_contents());
    $filename=$user_name."_".$name.".xls";
    //echo "$total_data####$filename####$reportType";
    $filename=$filename;

    ?>
    <script>
        document.getElementById('exc').innerHTML='<a href="<? echo $filename;?>" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>';
    </script>
    <?
    exit();
}

?>