<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="order_no_search_popup")
{
    echo load_html_head_contents("Sales Order Info","../../../../", 1, 1, '','1','');
    extract($_REQUEST);
    ?>
    <script>

        function js_set_value(booking_data)
        {
            document.getElementById('hidden_booking_data').value=booking_data;
            parent.emailwindow.hide();
        }

    </script>
</head>
<body>
    <div align="center">
        <fieldset style="width:830px;margin-left:4px;">
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
                <table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
                    <thead>
                        <th>Within Group</th>
                        <th>Search By</th>
                        <th>Search</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                            <input type="hidden" name="hidden_booking_data" id="hidden_booking_data" value="">
                        </th>
                    </thead>
                    <tr class="general">
                        <td align="center"><? echo create_drop_down( "cbo_within_group", 150, $yes_no,"",1, "--Select--", "",$dd,0 ); ?></td>
                        <td align="center">
                            <?
                            $search_by_arr=array(1=>"Sales Order No",2=>"Sales / Booking No",3=>"Style Ref.");
                            echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                            ?>
                        </td>
                        <td align="center">
                            <input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('cbo_within_group').value, 'create_order_no_search_list_view', 'search_div', 'stock_barcode_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                        </td>
                    </tr>
                </table>
                <div id="search_div" style="margin-top:10px"></div>
            </form>
        </fieldset>
    </div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_order_no_search_list_view")
{
    $data=explode('_',$data);

    $company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
    $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
    $location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');

    $search_string=trim($data[0]);
    $search_by =$data[1];
    $company_id =$data[2];
    $within_group=$data[3];

    $search_field_cond='';
    if($search_string!="")
    {
        if($search_by==1) $search_field_cond=" and job_no like '%".$search_string."'";
        else if($search_by==2) $search_field_cond=" and sales_booking_no like '%".$search_string."'";
        else $search_field_cond=" and style_ref_no like '".$search_string."%'";
    }

    if($within_group==0) $within_group_cond=""; else $within_group_cond=" and within_group=$within_group";


    if($cbo_year>0){
        if($db_type==0) $year_cond="and YEAR(insert_date) =$cbo_year";
        else if($db_type==2) $year_cond="and to_char(insert_date,'YYYY') =$cbo_year";
        else $year_cond="";//defined Later
    }

    if($db_type==0) $year_field="YEAR(insert_date) as year";
    else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
    else $year_field="";//defined Later
    $booking_arr = array();
    $booking_info = sql_select("select a.id,a.booking_no, a.booking_type, a.company_id, a.entry_form, a.fabric_source, a.item_category, a.job_no, a.po_break_down_id, a.is_approved, is_short from wo_booking_mst a where a.is_deleted = 0 and a.status_active=1");
    foreach ($booking_info as $row) {
        $booking_arr[$row[csf('booking_no')]]['id'] = $row[csf('id')];
        $booking_arr[$row[csf('booking_no')]]['booking_no'] = $row[csf('booking_no')];
        $booking_arr[$row[csf('booking_no')]]['booking_type'] = $row[csf('booking_type')];
        $booking_arr[$row[csf('booking_no')]]['company_id'] = $row[csf('company_id')];
        $booking_arr[$row[csf('booking_no')]]['entry_form'] = $row[csf('entry_form')];
        $booking_arr[$row[csf('booking_no')]]['fabric_source'] = $row[csf('fabric_source')];
        $booking_arr[$row[csf('booking_no')]]['item_category'] = $row[csf('item_category')];
        $booking_arr[$row[csf('booking_no')]]['job_no'] = $row[csf('job_no')];
        $booking_arr[$row[csf('booking_no')]]['po_break_down_id'] = $row[csf('po_break_down_id')];
        $booking_arr[$row[csf('booking_no')]]['is_approved'] = $row[csf('is_approved')];
        $booking_arr[$row[csf('booking_no')]]['is_short'] = $row[csf('is_short')];
    }
    $sql = "select id, $year_field, job_no_prefix_num, job_no, within_group, sales_booking_no, booking_date, buyer_id, style_ref_no, location_id from fabric_sales_order_mst where status_active=1 and is_deleted=0 and company_id=$company_id $within_group_cond $search_field_cond  $year_cond order by id DESC";
    //echo $sql;//die;
    $result = sql_select($sql);
    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="90">Sales Order No</th>
            <th width="60">Year</th>
            <th width="80">Within Group</th>
            <th width="70">Buyer</th>
            <th width="120">Sales/ Booking No</th>
            <th width="80">Booking date</th>
            <th width="110">Style Ref.</th>
            <th>Location</th>
        </thead>
    </table>
    <div style="width:800px; max-height:300px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="tbl_list_search">
            <?
            $i=1;
            foreach ($result as $row)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

                if($row[csf('within_group')]==1)
                    $buyer=$company_arr[$row[csf('buyer_id')]];
                else
                    $buyer=$buyer_arr[$row[csf('buyer_id')]];

                $booking_data =$row[csf('id')]."**".$row[csf('job_no')];
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $booking_data; ?>');">
                    <td width="40"><? echo $i; ?></td>
                    <td width="90"><p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p></td>
                    <td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="80"><p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p></td>
                    <td width="70"><p><? echo $buyer; ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
                    <td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                    <td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                    <td><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
                </tr>
                <?
                $i++;
            }
            ?>
        </table>
    </div>
    <?
    exit();
}

if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($cbo_company_name==0) $company_id =""; else $company_id =" and a.company_id='$cbo_company_name'";
	if ($txt_fso_id =="") $txt_fso_cond=""; else $txt_fso_cond=" and a.po_breakdown_id=$txt_fso_id";

    $colspan = 6;
    $tableWidth = 700;

	ob_start();	
	?>
    <div>
        <table width="<? echo $tableWidth;?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        	<thead>
                <tr>
                    <th width="40">SL</th>
                    <th width="160">Buyer</th>
                    <th width="150">FSO No.</th>
                    <th width="150">Booking No</th>
                    <th width="100">Barcode No</th>
                    <th width="100">Roll Weight</th>
                </tr>
            </thead>
        </table>
        <div style="width:<? echo $tableWidth + 18?>px; max-height:350px; overflow-y:scroll;" id="scroll_body"> 
            <table width="<? echo $tableWidth;?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
            <?
			$buyer_Arr = return_library_array("select id,buyer_name from lib_buyer where status_active in (1,2) and is_deleted=0","id","buyer_name");
                
               $sql="SELECT a.barcode_no, b.within_group, b.po_buyer, b.buyer_id, b.job_no, b.sales_booking_no, a.qnty, c.barcode_no as issued_barcode
                    from pro_roll_details a left join pro_roll_details c on a.barcode_no = c.barcode_no and c.entry_form=61 and c.is_returned=0 and c.status_active=1 and c.is_deleted=0, fabric_sales_order_mst b
                    where b.company_id='$cbo_company_name' and a.po_breakdown_id=b.id and a.status_active=1 and a.is_deleted=0 and a.entry_form in (58,133) and a.re_transfer=0  and a.is_sales=1
                    $txt_fso_cond
                    group by a.barcode_no, b.within_group, b.po_buyer, b.buyer_id, b.job_no, b.sales_booking_no, a.qnty, c.barcode_no
                    having c.barcode_no is null
                    order by a.barcode_no asc";	
                $result = sql_select($sql);
                $i=1;
                foreach($result as $row)
                {
                    if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
        			if($row[csf("within_group")] ==1)
        			{
        				$buyer_name = $buyer_Arr[$row[csf("po_buyer")]];
        			}else{
        				$buyer_name = $buyer_Arr[$row[csf("buyer_id")]];
        			}
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="40"><? echo $i; ?></td>	
                            <td width="160" align="center"><p><? echo $buyer_name; ?></p></td>
                            <td width="150"><p><? echo $row[csf("job_no")]; ?></p></td> 
                            <td width="150"><p><? echo $row[csf("sales_booking_no")]; ?></p></td>
                            <td width="100" align="right"><p><? echo $row[csf("barcode_no")];?></p></td>
                            <td width="100" align="right"><p><? echo number_format($row[csf("qnty")],2); ?></p></td>
                        </tr>
                    <? 												
                     $i++; 				
					}
				?>
            </table>
		</div>
	</div>
    <?
    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$report_type"; 
    exit();
}
?>